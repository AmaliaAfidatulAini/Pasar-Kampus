<?php

declare(strict_types=1);

require_model('admin/common');

function admin_report_query(string $redirect): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'QUERY') {
        return;
    }

    role_include('admin');
    header('Content-Type: application/json; charset=UTF-8');

    try {
        $input = $_GET;
        if (str_starts_with(strtolower((string) ($_SERVER['CONTENT_TYPE'] ?? '')), 'application/json')) {
            $body = json_decode(file_get_contents('php://input'), true);
            if (is_array($body)) {
                $input = array_merge($input, $body);
            }
        }

        $query = $input['query'] ?? 'table';
        if (!is_string($query)) {
            throw new InvalidArgumentException('Query laporan tidak valid.');
        }

        if ($query === 'edit-report') {
            $id = admin_post_id($input);
            $report = fetch(executeQuery(
                "SELECT r.report_id AS id,
                    COALESCE(reporter.name, '') || CASE WHEN reporter.name IS NOT NULL AND reporter_user.email IS NOT NULL THEN ' (' || reporter_user.email || ')' ELSE COALESCE(reporter_user.email, '') END AS pelapor,
                    COALESCE(owner.name, '') || CASE WHEN owner.name IS NOT NULL AND owner_user.email IS NOT NULL THEN ' (' || owner_user.email || ')' ELSE COALESCE(owner_user.email, '') END AS terlapor,
                    r.report_type AS alasan,
                    COALESCE(r.description, '') AS deskripsi,
                    r.review_status AS status,
                    COALESCE(r.decision, '') AS keputusan,
                    r.created_at,
                    (SELECT COALESCE(json_agg(a.file_path), '[]'::json) FROM attachments a WHERE a.report_id = r.report_id) AS bukti
                 FROM reports r
                 LEFT JOIN users reporter_user ON reporter_user.user_id = r.user_id
                 LEFT JOIN user_profiles reporter ON reporter.user_id = reporter_user.user_id
                 LEFT JOIN products product ON product.product_id = r.product_id
                 LEFT JOIN users owner_user ON owner_user.user_id = product.user_id
                 LEFT JOIN user_profiles owner ON owner.user_id = owner_user.user_id
                 WHERE r.report_id = ?",
                [$id]
            ));
            if (!$report) {
                http_response_code(404);
                echo json_encode(['error' => 'Laporan tidak ditemukan.'], JSON_UNESCAPED_UNICODE);
                exit;
            }
            $report['bukti'] = json_decode((string) $report['bukti'], true) ?: [];
            echo json_encode($report, JSON_UNESCAPED_UNICODE);
            exit;
        }

        if ($query !== 'table') {
            throw new InvalidArgumentException('Query laporan tidak valid.');
        }

        $draw = max(0, (int) ($input['draw'] ?? 0));
        $start = max(0, (int) ($input['start'] ?? 0));
        $length = (int) ($input['length'] ?? 10);
        $length = $length < 1 ? 10 : min($length, 100);
        $search = $input['search']['value'] ?? '';
        $search = is_string($search) ? trim($search) : '';
        $where = '';
        $bindings = [];
        if ($search !== '') {
            $where = " WHERE COALESCE(reporter.name, '') ILIKE :reporter
                OR COALESCE(reporter_user.email, '') ILIKE :reporter_email
                OR COALESCE(owner.name, '') ILIKE :owner
                OR COALESCE(owner_user.email, '') ILIKE :owner_email
                OR r.report_type ILIKE :type
                OR r.review_status ILIKE :status";
            $term = '%' . $search . '%';
            $bindings = [
                ':reporter' => $term,
                ':reporter_email' => $term,
                ':owner' => $term,
                ':owner_email' => $term,
                ':type' => $term,
                ':status' => $term,
            ];
        }

        $from = " FROM reports r
            LEFT JOIN users reporter_user ON reporter_user.user_id = r.user_id
            LEFT JOIN user_profiles reporter ON reporter.user_id = reporter_user.user_id
            LEFT JOIN products product ON product.product_id = r.product_id
            LEFT JOIN users owner_user ON owner_user.user_id = product.user_id
            LEFT JOIN user_profiles owner ON owner.user_id = owner_user.user_id";
        $total = (int) fetch(executeQuery('SELECT COUNT(*) AS total FROM reports'))['total'];
        $filtered = (int) fetch(executeQuery('SELECT COUNT(DISTINCT r.report_id) AS total' . $from . $where, $bindings))['total'];
        $orderColumns = [
            0 => 'r.report_id',
            1 => 'reporter_user.email',
            2 => "COALESCE(owner.name, COALESCE(owner_user.email, ''))",
            3 => 'r.report_type',
            4 => 'r.report_id',
            5 => 'r.review_status',
            6 => 'r.created_at',
        ];
        $orderIndex = (int) ($input['order'][0]['column'] ?? 0);
        $order = $orderColumns[$orderIndex] ?? 'r.report_id';
        $direction = strtolower((string) ($input['order'][0]['dir'] ?? 'desc')) === 'asc' ? 'ASC' : 'DESC';
        $rows = executeQuery(
            "SELECT r.report_id AS id,
                COALESCE(reporter_user.email, '-') AS pelapor,
                COALESCE(owner.name, '') || CASE WHEN owner.name IS NOT NULL AND owner_user.email IS NOT NULL THEN ' (' || owner_user.email || ')' ELSE COALESCE(owner_user.email, '') END AS terlapor,
                r.report_type AS alasan,
                (SELECT COUNT(*) FROM attachments a WHERE a.report_id = r.report_id) AS \"jmlBukti\",
                r.review_status AS status,
                r.created_at
             {$from}{$where}
             ORDER BY {$order} {$direction} LIMIT :limit OFFSET :offset",
            [...$bindings, ':limit' => $length, ':offset' => $start],
            'fetchAll'
        );
        echo json_encode([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $rows,
        ], JSON_UNESCAPED_UNICODE);
    } catch (InvalidArgumentException $error) {
        http_response_code(400);
        echo json_encode(['error' => $error->getMessage()], JSON_UNESCAPED_UNICODE);
    } catch (Throwable $error) {
        error_log($error->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Terjadi kesalahan server.'], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

function admin_report_post(string $redirect): void
{
    admin_handle_post($redirect, static function (array $input): string {
        $action = $input['action'] ?? '';
        $reportId = admin_post_id($input);
        if ($action === 'delete-report') {
            $stmt = executeQuery('DELETE FROM reports WHERE report_id = ?', [$reportId]);
            if ($stmt->rowCount() === 0) {
                throw new InvalidArgumentException('Laporan tidak ditemukan.');
            }
            return 'Laporan berhasil dihapus.';
        }
        if ($action !== 'resolve-report') {
            throw new InvalidArgumentException('Tindakan laporan tidak valid.');
        }
        $decision = admin_post_string($input, 'decision', 'Keputusan');
        if (!in_array($decision, ['Abaikan', 'Suspend', 'Banned'], true)) {
            throw new InvalidArgumentException('Keputusan laporan tidak valid.');
        }
        $report = fetch(executeQuery(
            'SELECT product.user_id AS target_user_id FROM reports r LEFT JOIN products product ON product.product_id = r.product_id WHERE r.report_id = ?',
            [$reportId]
        ));
        if (!$report) {
            throw new InvalidArgumentException('Laporan tidak ditemukan.');
        }
        executeQuery(
            'UPDATE reports SET review_status = ?, decision = ?, updated_at = CURRENT_TIMESTAMP WHERE report_id = ?',
            [$decision === 'Abaikan' ? 'Diabaikan' : 'Selesai', $decision, $reportId]
        );
        if ($decision !== 'Abaikan' && $report['target_user_id'] !== null) {
            executeQuery(
                'UPDATE users SET account_status = ?, notes = ?, updated_at = CURRENT_TIMESTAMP WHERE user_id = ? AND deleted_at IS FALSE',
                [$decision, 'Sanksi dari Laporan Aduan #' . $reportId, (int) $report['target_user_id']]
            );
        }
        return 'Keputusan laporan berhasil diproses.';
    });
}

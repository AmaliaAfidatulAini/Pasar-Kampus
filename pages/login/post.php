<?php

declare(strict_types=1);

use Azolee\Validator\Validator;

$session = get_session();
if (isset($session['user_id'])) {
    redirect(($session['role'] ?? '') === 'admin' ? '/admin' : '/', 302);
}

/** Valid `redirect` target posted by the login form, or '' when absent/unsafe. */
function login_target(): string
{
    return redirect_target($_POST['redirect'] ?? '');
}

/** Where a successful login lands. Admins always go to /admin. */
function login_success_url(array $user): string
{
    if (($user['role'] ?? '') === 'admin') {
        return '/admin';
    }

    $target = login_target();

    return $target !== '' ? $target : '/';
}

/** /login, carrying the pending redirect through a failed attempt. */
function login_failure_url(): string
{
    $target = login_target();

    return '/login' . ($target !== '' ? '?redirect=' . rawurlencode($target) : '');
}

function process(): void
{
    $validation = Validator::make([
        'email' => 'bail|required|string|email|max:50',
        'password' => 'bail|required|string',
    ], $_POST);

    if ($validation->isFailed()) {
        flash('error', implode(' ', parse_errors($validation)));
        return;
    }

    $result = (object) $validation->validated();

    $user = executeQuery(
        "SELECT u.*, p.name, p.phone, p.campus, p.subdistrict, p.district
        FROM users u
        LEFT JOIN user_profiles p ON u.user_id = p.user_id
        WHERE u.email = :email AND (u.deleted_at IS FALSE OR u.deleted_at IS NULL)",
        ["email" => $result->email],
        "fetch"
    );

    if (!$user || !password_verify($result->password, $user['password'])) {
        flash('error', 'Email atau Password Salah!');
        return;
    }

    unset($user['password']);
    create_session($user);
    flash('success', 'Berhasil masuk!');
    redirect(login_success_url($user), 303);
}

function handle(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        redirect('/login', 303);
    }

    process();
    redirect(login_failure_url(), 303);
}

handle();

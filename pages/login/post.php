<?php

declare(strict_types=1);

use Azolee\Validator\Validator;

$session = get_session();
if (isset($session['user_id'])) {
    redirect(($session['role'] ?? '') === 'admin' ? '/admin' : '/', 302);
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
    redirect(($user['role'] ?? '') === 'admin' ? '/admin' : '/', 303);
}

function handle(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        redirect('/login', 303);
    }

    process();
    redirect('/login', 303);
}

handle();

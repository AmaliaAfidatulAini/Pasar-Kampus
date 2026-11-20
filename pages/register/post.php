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
        'password' => 'bail|required|string|min:8',
    ], $_POST);

    if ($validation->isFailed()) {
        flash('error', implode(' ', parse_errors($validation)));
        return;
    }
    
    $result = (object) $validation->validated();

    $existingUser = executeQuery(
        'SELECT user_id FROM users WHERE email = :email',
        ["email" => $result->email],
        'fetch'
    );
    if ($existingUser) {
        flash('error', 'Email sudah terdaftar. Silakan masuk.');
        return;
    }

    executeQuery(
        "INSERT INTO users (email, password, role) VALUES (:email, :password, 'user') RETURNING user_id",
        ["email" => $result->email, "password" => password_hash($result->password, PASSWORD_DEFAULT)],
        "fetch"
    );

    flash('success', 'Akun berhasil dibuat! Silakan masuk.');
    redirect('/login', 303);
}

function handle(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        redirect('/register', 303);
    }

    process();
    redirect('/register', 303);
}

handle();

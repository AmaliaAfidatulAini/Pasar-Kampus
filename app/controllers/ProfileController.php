<?php

namespace App\Controllers;

use Azolee\Validator\Validator;
use Core\Request;
use Core\Storage;
use App\Models\UserModel;

class ProfileController
{
    private UserModel $model;

    public function __construct() { $this->model = new UserModel(); }

    public function show(): void { view('profile/index', ['user' => $this->user(), 'errors' => []]); }

    public function update(Request $req): void
    {
        $user = $this->user();
        $name = trim((string) $req->body('name', ''));
        $email = strtolower(trim((string) $req->body('email', '')));
        $result = Validator::make(['name' => ['required', 'string'], 'email' => ['required', 'email']], compact('name', 'email'));
        $errors = array_values($result->getErrorsForFields());
        if (!$errors && $email !== $user['email'] && $this->model->findByEmail($email)) $errors[] = 'That email is already registered.';
        if ($errors) { view('profile/index', compact('user', 'errors')); return; }

        $photo = $req->file('photo_profile');
        $path = null;
        if ($photo && $photo['error'] !== UPLOAD_ERR_NO_FILE) {
            $path = Storage::put($photo, 'profiles');
            if ($user['photo_profile']) Storage::delete($user['photo_profile']);
        }
        $this->model->update($user['id'], $name, $email, $path);
        session((object) ['id' => (int) $user['id'], 'name' => $name, 'email' => $email]);
        redirect('/profile');
    }

    public function password(Request $req): void
    {
        $user = $this->user();
        $current = (string) $req->body('current_password', '');
        $password = (string) $req->body('password', '');
        $confirmation = (string) $req->body('password_confirmation', '');
        $result = Validator::make(['password' => ['required', 'min:8'], 'password_confirmation' => ['same:password']], compact('password', 'password_confirmation'));
        $errors = array_values($result->getErrorsForFields());
        if (!password_verify($current, $user['password'])) $errors[] = 'Current password is incorrect.';
        if ($errors) { view('profile/index', compact('user', 'errors')); return; }
        $this->model->updatePassword($user['id'], password_hash($password, PASSWORD_DEFAULT));
        redirect('/profile');
    }

    private function user(): array
    {
        $user = $this->model->find((int) (session()->id ?? 0));
        if (!$user) redirect('/login');
        return $user;
    }
}

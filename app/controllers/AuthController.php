<?php

namespace App\Controllers;

use Azolee\Validator\Validator;
use Core\Request;

class AuthController
{
    private object $model;

    public function __construct() {
        $this->model = new \App\Models\UserModel();
    }

    public function login()
    {
        return view('auth/login');
    }

    public function authenticate(Request $request)
    {
        $email = strtolower(trim($request->body('email', '')));
        $password = $request->body('password', '');
        
        $validation = Validator::make([
            'email' => ['required', 'email'],
            'password' => 'required',
        ], compact('email', 'password'));

        if ($validation->isFailed()) {
            return view('auth/login', [
                'errors' => array_values($validation->getErrorsForFields()), 
                'email' => $email
            ]);
        }

        $user = $this->model->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            return view('auth/login', [
                'errors' => ['Email or password is incorrect.'], 
                'email' => $email
            ]);
        }

        session((object) [
            'id' => (int) $user['id'], 
            'name' => $user['name'], 
            'email' => $user['email']
        ]);

        return redirect('/posts', 303);
    }

    public function register()
    {
        return view('auth/register');
    }

    public function store(Request $request)
    {
        $name = trim($request->body('name', ''));
        $email = strtolower(trim($request->body('email', '')));
        $password = $request->body('password', '');
        
        $validation = Validator::make([
            'name' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], compact('name', 'email', 'password'));

        $errors = array_values($validation->getErrorsForFields());

        if (!$errors && $this->model->findByEmail($email))
            array_push($errors, 'That email is already registered.');

        if ($validation->isFailed() || $errors)
            return view('auth/register', compact('errors', 'name', 'email'));

        $id = $this->model->create($name, $email, password_hash($password, PASSWORD_DEFAULT));
        session((object) compact('id', 'name', 'email'));
        
        return redirect('/posts', 303);
    }

    public function logout()
    {
        remove_session();
        
        return redirect('/login', 303);
    }
}

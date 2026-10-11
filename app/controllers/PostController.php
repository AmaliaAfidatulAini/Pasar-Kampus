<?php

namespace App\Controllers;

use Azolee\Validator\Validator;
use Core\Request;
use Core\Storage;

class PostController
{
    private object $model;

    public function __construct() {
        $this->model = new \App\Models\PostModel();
    }

    public function index()
    {
        return view('posts/index', [
            'posts' => $this->model->all()
        ]);
    }

    public function create()
    {
        return view('posts/form', ['post' => null, 'errors' => []]);
    }

    public function store(Request $req)
    {
        $data = $this->validated($req);
        if ($data['errors']) {
            return view('posts/form', [
                'post' => $data['input'], 
                'errors' => $data['errors']
            ]);
        }

        $this->model->create($this->userId(), $data['title'], $data['content'], $this->uploadPhotos($req) ?? null);
        return redirect('/posts');
    }

    public function edit(Request $req)
    {
        $post = $this->ownedPost((int) $req->param('id'));
        return view('posts/form', ['post' => $post]);
    }

    public function update(Request $req)
    {
        $post = $this->ownedPost((int) $req->param('id'));
        $data = $this->validated($req);
        
        if ($data['errors']) {
            $post = array_merge($post, $data['input']);
            return view('posts/form', [
                'post' => $post, 
                'errors' => $data['errors']
            ]);
        }

        $photos = $this->uploadPhotos($req);
        if ($photos === null) {
            $photos = $post['photos'];
        } else {
            foreach ($this->model->photos((int) $post['id']) as $photo) Storage::delete($photo);
        }
        $this->model->update((int) $post['id'], $this->userId(), $data['title'], $data['content'], $photos);
        return redirect('/posts');
    }

    public function delete(Request $req)
    {
        $post = $this->ownedPost((int) $req->param('id'));

        foreach (json_decode($post['photos'] ?? '[]', true) ?: [] as $file) {
            if (Storage::check($file)) Storage::delete($file);
        }

        $this->model->delete((int) $req->param('id'), $this->userId());
        return redirect('/posts');
    }

    private function validated(Request $req)
    {
        $title = trim((string) $req->body('title'));
        $content = trim((string) $req->body('content'));

        $rules = [
            'title' => ['required', 'string'],
            'content' => ['required', 'string'],
        ];

        $validation = Validator::make($rules, compact('title', 'content'));
        $errors = array_values($validation->getErrorsForFields());

        return [
            'title' => $title,
            'content' => $content,
            'input' => compact('title', 'content'),
            'errors' => $errors,
        ];
    }

    private function uploadPhotos(Request $req): ?string
    {
        $files = $req->file('photos');
        if (!$files || (isset($files[0]['error']) && $files[0]['error'] === UPLOAD_ERR_NO_FILE)) return null;
        $files = isset($files['name']) ? [$files] : $files;
        $paths = [];
        foreach ($files as $file) {
            $paths[] = Storage::put($file, 'posts');
        }
        return json_encode($paths, JSON_THROW_ON_ERROR);
    }

    private function ownedPost(int $id)
    {
        $post = $this->model->find($id);
        if (!$post || (int) $post['user_id'] !== $this->userId()) 
            redirect('/404', 404);
        return $post;
    }

    private function userId(): int
    {
        $id = session()->id ?? null;
        if (!is_numeric($id) || (int) $id < 1) {
            throw new \RuntimeException('A logged-in user is required for this action.');
        }
        return (int) $id;
    }
}

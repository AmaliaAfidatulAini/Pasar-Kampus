<?php

namespace App\Controllers;

class HomeController
{
    private object $model;

    public function __construct()
    {
        $this->model = new \App\Models\PostModel();    
    }
    public function index()
    {
        $data = $this->model->all();
        foreach ($data as $key => $item) {
            
            $data[$key]["photos"] = json_decode($data[$key]["photos"]);
        }

        return view('home/index', [
            "posts" => $data
        ]);
    }
}

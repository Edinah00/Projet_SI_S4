<?php

namespace App\Controllers;

class AuthController extends BaseController
{
    public function index()
    {
        return view('login', [
            'title' => 'Connexion',
        ]);
    }

    public function login()
    {
        $this->session->set([
            'isLoggedIn' => true,
            'userName' => $this->request->getPost('email') ?: 'admin@releve.mg',
        ]);

        return redirect()->to('/etudiants');
    }

    public function logout()
    {
        $this->session->destroy();

        return redirect()->to('/');
    }
}

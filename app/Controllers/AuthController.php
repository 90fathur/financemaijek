<?php

namespace App\Controllers;

use App\Models\UserModel;

class AuthController extends BaseController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        helper(['form', 'url']);
    }

    public function login()
    {
        if (session()->get('logged_in')) {
            return redirect()->to(base_url('dashboard'));
        }

        return view('auth/login');
    }

    public function processLogin()
    {
        $rules = [
            'username' => 'required',
            'password' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Silakan masukkan username dan password.');
        }

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $user = $this->userModel->where('username', $username)
            ->orWhere('email', $username)
            ->first();

        if (!$user) {
            return redirect()->back()->withInput()->with('error', 'Username atau password tidak ditemukan.');
        }

        if (!password_verify($password, $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Password yang dimasukkan salah.');
        }

        // Set session
        $sessionData = [
            'user_id'     => $user['id'],
            'nama'        => $user['nama'],
            'username'    => $user['username'],
            'email'       => $user['email'],
            'role'        => $user['role'],
            'investor_id' => $user['investor_id'],
            'logged_in'   => true,
        ];
        session()->set($sessionData);

        return redirect()->to(base_url('dashboard'))->with('success', 'Selamat datang kembali, ' . $user['nama'] . '!');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('auth/login'))->with('success', 'Anda telah berhasil keluar.');
    }
}

<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Flash;
use App\Core\Request;
use App\Models\User;

final class AuthController extends Controller
{
    public function showLogin(): string
    {
        return $this->view('auth/login', ['title' => 'Giriş Yap']);
    }

    public function login(Request $request): void
    {
        $this->verifyCsrf($request);
        $username = $request->string('username');
        $password = $request->string('password');

        if ($username === '' || $password === '') {
            Flash::error('Kullanıcı adı ve şifre zorunludur.');
            Flash::withInput(['username' => $username]);
            redirect('giris');
        }

        $user = Auth::attempt($username, $password, $request->ip());
        if (!$user instanceof User) {
            Flash::error('Kullanıcı adı veya şifre hatalı.');
            Flash::withInput(['username' => $username]);
            redirect('giris');
        }

        Auth::login($user);
        Flash::success('Tekrar hoş geldin, ' . $user->username . '!');
        redirect('profil');
    }

    public function showRegister(): string
    {
        return $this->view('auth/register', ['title' => 'Kayıt Ol']);
    }

    public function register(Request $request): void
    {
        $this->verifyCsrf($request);
        $username = trim($request->string('username'));
        $email = trim($request->string('email'));
        $password = $request->string('password');
        $passwordConfirm = $request->string('password_confirm');

        $errors = [];
        if (!preg_match('/^[a-zA-Z0-9_]{3,16}$/', $username)) {
            $errors[] = 'Kullanıcı adı 3-16 karakter olmalı ve sadece harf, rakam, alt çizgi içerebilir.';
        }
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Geçerli bir e-posta adresi giriniz.';
        }
        if (strlen($password) < 6) {
            $errors[] = 'Şifre en az 6 karakter olmalıdır.';
        }
        if ($password !== $passwordConfirm) {
            $errors[] = 'Şifreler eşleşmiyor.';
        }

        if ($errors !== []) {
            foreach ($errors as $error) {
                Flash::error($error);
            }
            Flash::withInput(['username' => $username, 'email' => $email]);
            redirect('kayit');
        }

        [$user, $error] = Auth::register($username, $email, $password, $request->ip());
        if (!$user instanceof User) {
            Flash::error($error ?? 'Kayıt başarısız oldu.');
            Flash::withInput(['username' => $username, 'email' => $email]);
            redirect('kayit');
        }

        Auth::login($user);
        Flash::success('Hesabın oluşturuldu, aramıza hoş geldin!');
        redirect('profil');
    }

    public function logout(Request $request): void
    {
        $this->verifyCsrf($request);
        Auth::logout();
        Flash::success('Çıkış yapıldı.');
        redirect('/');
    }
}

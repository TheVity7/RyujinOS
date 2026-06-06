<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Flash;
use App\Core\Request;
use App\Models\CreditTransaction;
use App\Models\Order;
use App\Models\User;
use App\Services\AuthMeService;

final class ProfileController extends Controller
{
    public function index(): string
    {
        $user = Auth::user();
        return $this->view('profile/index', [
            'title'        => 'Profilim',
            'user'         => $user,
            'orders'       => Order::forUser($user->id, 5),
            'transactions' => CreditTransaction::forUser($user->id, 5),
            'orderCount'   => count(Order::forUser($user->id, 1000)),
        ]);
    }

    public function edit(): string
    {
        return $this->view('profile/edit', ['title' => 'Profili Düzenle', 'user' => Auth::user()]);
    }

    public function update(Request $request): void
    {
        $this->verifyCsrf($request);
        $user = Auth::user();
        $email = trim($request->string('email'));

        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Flash::error('Geçerli bir e-posta adresi giriniz.');
            redirect('profil/duzenle');
        }
        $existing = $email !== '' ? User::findByEmail($email) : null;
        if ($existing instanceof User && $existing->id !== $user->id) {
            Flash::error('Bu e-posta başka bir hesap tarafından kullanılıyor.');
            redirect('profil/duzenle');
        }

        $user->email = $email !== '' ? $email : null;
        $avatar = trim($request->string('avatar'));
        $user->avatar = $avatar !== '' ? $avatar : null;
        $user->save();

        Flash::success('Profil bilgilerin güncellendi.');
        redirect('profil');
    }

    public function security(): string
    {
        return $this->view('profile/security', ['title' => 'Güvenlik', 'user' => Auth::user()]);
    }

    public function updatePassword(Request $request): void
    {
        $this->verifyCsrf($request);
        $user = Auth::user();
        $current = $request->string('current_password');
        $new = $request->string('new_password');
        $confirm = $request->string('new_password_confirm');

        if (strlen($new) < 6) {
            Flash::error('Yeni şifre en az 6 karakter olmalıdır.');
            redirect('profil/guvenlik');
        }
        if ($new !== $confirm) {
            Flash::error('Yeni şifreler eşleşmiyor.');
            redirect('profil/guvenlik');
        }

        if (!$user->verifyPassword($current)) {
            Flash::error('Mevcut şifren hatalı.');
            redirect('profil/guvenlik');
        }

        $user->changePassword($new);
        Flash::success('Şifren başarıyla güncellendi.');
        redirect('profil/guvenlik');
    }

    public function orders(): string
    {
        $user = Auth::user();
        return $this->view('profile/orders', [
            'title'  => 'Siparişlerim',
            'user'   => $user,
            'orders' => Order::forUser($user->id, 100),
        ]);
    }

    public function credits(): string
    {
        $user = Auth::user();
        return $this->view('profile/credits', [
            'title'        => 'Kredi Geçmişi',
            'user'         => $user,
            'transactions' => CreditTransaction::forUser($user->id, 100),
        ]);
    }
}

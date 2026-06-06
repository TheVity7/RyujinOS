<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Flash;
use App\Core\Request;
use App\Models\CreditTransaction;
use App\Models\Order;
use App\Models\User;

final class UserController extends Controller
{
    public function index(Request $request): string
    {
        $search = $request->string('q');
        return $this->view('admin/users/index', [
            'title'  => 'Kullanıcılar',
            'users'  => User::all(200, $search),
            'search' => $search,
        ]);
    }

    public function edit(Request $request, string $id): string
    {
        $user = User::find((int) $id);
        if ($user === null) {
            return $this->notFound();
        }
        return $this->view('admin/users/edit', [
            'title'        => $user->username,
            'user'         => $user,
            'orders'       => Order::forUser($user->id, 20),
            'transactions' => CreditTransaction::forUser($user->id, 20),
        ]);
    }

    public function update(Request $request, string $id): void
    {
        $this->verifyCsrf($request);
        $user = User::find((int) $id);
        if ($user === null) {
            Flash::error('Kullanıcı bulunamadı.');
            redirect('yonetim/kullanicilar');
        }
        $email = $request->string('email');
        $user->email = $email !== '' ? $email : null;
        $user->role = $request->string('role') === 'admin' ? 'admin' : 'member';
        $user->save();
        Flash::success('Kullanıcı güncellendi.');
        redirect('yonetim/kullanicilar/' . $user->id);
    }

    public function adjustCredit(Request $request, string $id): void
    {
        $this->verifyCsrf($request);
        $user = User::find((int) $id);
        if ($user === null) {
            Flash::error('Kullanıcı bulunamadı.');
            redirect('yonetim/kullanicilar');
        }
        $amount = $request->float('amount');
        if ($amount === 0.0) {
            Flash::error('Tutar 0 olamaz.');
            redirect('yonetim/kullanicilar/' . $user->id);
        }
        $note = $request->string('note') ?: 'Yönetici kredi düzenlemesi';
        $user->addBalance($amount, 'admin', $note);
        Flash::success('Bakiye güncellendi. Yeni bakiye: ' . credits($user->balance));
        redirect('yonetim/kullanicilar/' . $user->id);
    }
}

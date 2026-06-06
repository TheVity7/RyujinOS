<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Flash;
use App\Core\Request;
use App\Models\SupportTicket;

final class SupportController extends Controller
{
    public function index(): string
    {
        $user = Auth::user();
        return $this->view('support/index', [
            'title'   => 'Destek',
            'user'    => $user,
            'tickets' => SupportTicket::forUser($user->id),
        ]);
    }

    public function store(Request $request): void
    {
        $this->verifyCsrf($request);
        $user = Auth::user();
        $subject = $request->string('subject');
        $message = $request->string('message');

        if (mb_strlen($subject) < 3 || mb_strlen($message) < 10) {
            Flash::error('Konu en az 3, mesaj en az 10 karakter olmalıdır.');
            $this->back();
        }

        $id = SupportTicket::create([
            'user_id' => $user->id,
            'subject' => $subject,
            'message' => $message,
        ]);
        Flash::success('Destek talebiniz oluşturuldu.');
        redirect('destek/' . $id);
    }

    public function show(Request $request, string $id): string
    {
        $user = Auth::user();
        $ticket = SupportTicket::find((int) $id);
        if ($ticket === null || ($ticket->user_id !== $user->id && !$user->isAdmin())) {
            return $this->notFound();
        }
        return $this->view('support/show', [
            'title'   => $ticket->subject,
            'user'    => $user,
            'ticket'  => $ticket,
            'replies' => SupportTicket::replies($ticket->id),
        ]);
    }

    public function reply(Request $request, string $id): void
    {
        $this->verifyCsrf($request);
        $user = Auth::user();
        $ticket = SupportTicket::find((int) $id);
        if ($ticket === null || ($ticket->user_id !== $user->id && !$user->isAdmin())) {
            Flash::error('Talep bulunamadı.');
            redirect('destek');
        }
        if ($ticket->status === 'closed') {
            Flash::error('Bu talep kapatılmış.');
            redirect('destek/' . $ticket->id);
        }
        $message = $request->string('message');
        if (mb_strlen($message) < 2) {
            Flash::error('Mesaj çok kısa.');
            $this->back();
        }
        SupportTicket::addReply($ticket->id, $user->id, $message, $user->isAdmin());
        Flash::success('Yanıtınız gönderildi.');
        redirect('destek/' . $ticket->id);
    }
}

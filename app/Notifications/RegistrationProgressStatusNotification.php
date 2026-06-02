<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RegistrationProgressStatusNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $event,
        private readonly string $step,
        private readonly ?string $adminNote = null,
        private readonly ?string $nextStep = null,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $stepLabel = $this->stepLabel($this->step);
        $subject = 'Update pendaftaran Anda';
        $message = 'Status pendaftaran Anda telah diperbarui.';

        if ($this->event === 'submitted') {
            $subject = "Tahap {$stepLabel} berhasil dikirim";
            $message = "Dokumen/data tahap {$stepLabel} sudah kami terima dan sedang menunggu tinjauan admin.";
        } elseif ($this->event === 'approved') {
            $subject = "Tahap {$stepLabel} disetujui";
            $message = "Tahap {$stepLabel} telah disetujui.";
        } elseif ($this->event === 'revision_requested') {
            $subject = "Perlu perbaikan tahap {$stepLabel}";
            $message = "Tahap {$stepLabel} perlu diperbaiki sesuai catatan admin.";
        } elseif ($this->event === 'completed') {
            $subject = 'Pendaftaran selesai';
            $message = 'Semua tahap pendaftaran Anda telah disetujui.';
        } elseif ($this->event === 'registered') {
            $subject = 'Pendaftaran akun berhasil';
            $message = 'Akun Anda sudah aktif. Silakan verifikasi email untuk melanjutkan proses pendaftaran.';
        }

        $mail = (new MailMessage())
            ->subject($subject)
            ->greeting('Halo, '.($notifiable->name ?? 'Peserta').'!')
            ->line($message)
            ->line('Pantau status terbaru di dashboard pendaftaran Anda.');

        if ($this->nextStep !== null) {
            $mail->line('Tahap berikutnya: '.$this->stepLabel($this->nextStep).'.');
        }

        if ($this->adminNote !== null && trim($this->adminNote) !== '') {
            $mail->line('Catatan admin: '.$this->adminNote);
        }

        return $mail
            ->action('Buka Dashboard', url('/dashboard'))
            ->line('Terima kasih telah mendaftar.');
    }

    private function stepLabel(string $step): string
    {
        return match ($step) {
            'administration' => 'Administrasi',
            'psychology' => 'Psikologi',
            'health' => 'Kesehatan',
            'physical' => 'Fisik',
            'completed' => 'Selesai',
            default => ucfirst($step),
        };
    }
}

<?php

namespace App\Jobs;

use App\Mail\ForgotPasswordMail;
use App\Models\ForgotPassword;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendForgotPasswordMail implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private User $user) {
        $this->queue = 'remember-password-mail';
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Mail::to($this->user)->send(new ForgotPasswordMail());
        } catch(Exception $e) {
            Log::info('Falha ao enviar email de recuperação de senha: ' . $e->getMessage());
        }
    }
}

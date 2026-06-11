<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage as BaseMailMessage;

class AppMailMessage extends BaseMailMessage
{
    public function __construct()
    {
        parent::__construct();

        $this->salutation((string) config('app.mail_salutation'));
    }
}

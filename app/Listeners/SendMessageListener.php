<?php

namespace App\Listeners;

use App\Events\SendMessage;
use App\Mail\MessageMailable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendMessageListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(SendMessage $event): void
    {
        $correo = new MessageMailable($event->message);
        Mail::to('ejemplo@ejemplo.com')->send($correo);
    }
}

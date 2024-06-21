<?php

namespace App\Jobs;

use App\Events\ShowToastEvent;
use App\Mail\MessageMailable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $correo = new MessageMailable("Esto es un mensaje");
        Mail::to('ejemplo@ejemplo.com')->send($correo);
        event(new ShowToastEvent('Email enviado correctamente'));
    }
}

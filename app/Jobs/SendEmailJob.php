<?php

namespace App\Jobs;

use App\Events\ShowToastEvent;
use App\Mail\MessageMailable;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $title, $type;
    public $tries = 3;
    /**
     * Create a new job instance.
     */
    public function __construct(public User $user, public string $email) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        try {
            $correo = new MessageMailable("Esto es un mensaje");
            Mail::to($this->email)->send($correo);

            $title = Lang::get('Success when sending !');
            event(new ShowToastEvent($this->user, $title, Lang::get('Success when sending your work to the email address '.$this->email), 'success', 5000));
        } catch (\Throwable $th) {
            $title = Lang::get('An unexpected error has occurred !');
            event(new ShowToastEvent($this->user, $title, Lang::get('Your work could not be emailed to '.$this->email), 'danger', 20000));
        }

    }

}

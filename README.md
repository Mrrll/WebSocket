# WebSocket

Proyecto de uso de WebSocket con laravel Reverb.

> [!TIP]
> Estamos usando el repositorio [Laravel Initial Bootstrap](https://github.com/Mrrll/Laravel-Initial-Bootstrap) que puedes utilizar, tiene una explicación detallada para el uso del repositorio y todo lo que lleva implementado, para que puedas por ti mismo aplicarlo paso a paso o usarlo directamente. Este repositorio es una ayuda rápida para tener un entorno agradable y funcional de un proyecto de laravel 10 con bootstrap.

> [!NOTE]
> La finalidad de este proyecto es poder informar al usuario cuando ha finalizado un tarea que se ha ejecutado en segundo plano. Nos hemos ido guiando por la documentación de laravel, hemos utilizado: [Broadcasting](https://laravel.com/docs/10.x/broadcasting#server-side-installation), [Laravel Reverb](https://laravel.com/docs/10.x/reverb#introduction), [Queues](https://laravel.com/docs/10.x/queues#error-handling), [Events](https://laravel.com/docs/10.x/events#main-content) y [Mail](https://laravel.com/docs/10.x/mail#main-content).

<a name="top"></a>

## Indice de Contenidos.

-   [Instalación laravel-reverb](#item1)
-   [Instalación de Laravel Echo](#item2)
-   [Creamos evento y oyente](#item3)
-   [Creamos oyente del cliente](#item4)
-   [Uso y pruebas](#item5)
-   [Mostramos toast](#item6)
-   [Creamos Email](#item7)
-   [Creamos un trabajo para la cola (Job & Queue)](#item8)
-   [Canal Privado](#item9)



<a name="item1"></a>

## Instalación

### Instalación laravel-reverb

> Typee: en la Consola:

```console
composer require laravel/reverb:@beta
```

```console
php artisan reverb:install
```

> [!NOTE]
> Si el cursor se queda bloqueado al poner la instrucción presionamos enter.

> Abrimos el archivo `.env` añadimos lo siguiente.

```.env
REVERB_SERVER_HOST="127.0.0.1"
REVERB_SERVER_PORT="${REVERB_PORT}"
```

[Subir](#top)

<a name="item2"></a>

## Instalación de Laravel Echo

> Typee: en la Consola:

```console
npm install --save-dev laravel-echo pusher-js
```

> Abrimos el archivo `bootstrap.js` ubicado en `resources\js\` remplazamos y copiamos lo siguiente.

```js
import Echo from "laravel-echo";

import Pusher from "pusher-js";
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: "reverb",
    key: import.meta.env.VITE_REVERB_APP_KEY,
    cluster: import.meta.env.VITE_REVERB_APP_CLUSTER ?? "mt1",
    wsHost: import.meta.env.VITE_REVERB_HOST
        ? import.meta.env.VITE_REVERB_HOST
        : `ws-${import.meta.env.VITE_REVERB_APP_CLUSTER}.pusher.com`,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? "https") === "https",
    enabledTransports: ["ws", "wss"],
});
```

[Subir](#top)

<a name="item3"></a>

## Creamos evento y oyente

> Typee: en la Consola:

```console
php artisan make:event MessageProcessed
```

```console
php artisan make:listener SendMessage --event=MessageProcessed
```

> Abrimos el archivo `MessageProcessed` ubicado en `app\Events\` añadimos lo siguiente

```php
<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageProcessed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    /**
     * Create a new event instance.
     */
    public function __construct(string $message)
    {
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): Channel
    {
        return new Channel('message');
    }
}
```

> Abrimos el archivo `EventServiceProvider.php` ubicado en `app\Providers\` añadimos lo siguiente

```php
use App\Events\MessageProcessed;
use App\Listeners\SendMessage;

protected $listen = [
    Registered::class => [
        SendEmailVerificationNotification::class,
    ],
    MessageProcessed::class => [
        SendMessage::class
    ]
];
```

<a name="item4"></a>

### Creamos oyente del cliente

> Abrimos el archivo `bootstrap.js` ubicado en `resources\js\` añadimos lo siguiente.

```js
window.Echo.channel("messages").listen("MessageProcessed", (e) => {
    console.log(e.message);
});
```

[Subir](#top)

<a name="item5"></a>

### Uso y pruebas

> [!NOTE]
> Aparte de inicializar el `npm run dev` y `php artisan serve` inicializaremos el servicio de websocket Reverb.

> Typee: en la Consola:

```console
php artisan reverb:start
```

> [!IMPORTANT]
> Para probar el servicio de websocket Reverb solo tenemos que ejecutar el evento `MessageProcessed` para mayor facilidad y si tememos instalado tinker lo podemos probar de esta manera.

>Abrimos una consola y nos ubicamos en la dentro de nuestro proyecto:

> Typee: en la Consola:

```console
php artisan tinker
```

```console
use App\Events\MessageProcessed;
```

```console
MessageProcessed::dispatch("Hola WebSocket");
```

> Pues eso es todo espero que sirva. 👍

[Subir](#top)

<a name="item6"></a>

## Mostramos toast

> Abrimos el archivo `bootstrap.js` ubicado en `resources\js\` añadimos lo siguiente.

```js
window.Echo.channel("events").listen("SendMessage", (e) => {
    CreateToast("Hay un mensaje", e.message);
});
```

[Subir](#top)

<a name="item7"></a>

## Creamos Email

> Typee: en la Consola:

```console
php artisan make:mail MessageMailable
```

```console
php artisan make:view mails.message
```

> Abrimos el archivo `MessageMailable.php` ubicado en `app\Mail\` añadimos lo siguiente.

```php
<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MessageMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $body;

    /**
     * Create a new message instance.
     */
    public function __construct(string $body)
    {
        $this->body = $body;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Envío de mensaje',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mails.message',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

```

> Abrimos el archivo `message.blade.php` ubicado en `resources\mails\` añadimos lo siguiente.

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <h1>Correo electrónico</h1>
    <p>{{ $body }}</p>
</body>
</html>
```

[Subir](#top)

<a name="item8"></a>

## Creamos un trabajo para la cola (Job & Queue)

### Configuramos para utilizar la cola Queue

> Abrimos el archivo `.env` ubicado en `\` y en la constante añadimos lo siguiente.

```env
QUEUE_CONNECTION=database
```

> Typee: en la Consola:

```console
php artisan queue:table
```

> [!IMPORTANT]
> La instrucción es para la version 10 de laravel

```console
php artisan migrate
```

### Creamos el trabajo (Job) que enviara el email a la cola (Queue)

> Typee: en la Consola:

```console
php artisan make:job SendEmailJob
```

### Creamos el evento que usara el websocket

> Typee: en la Consola:

```console
php artisan make:event ShowToastEvent
```

> Abrimos el archivo `ShowToastEvent` ubicado en `app\Events\` y  añadimos lo siguiente.

```php
<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ShowToastEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public string $title,
        public string $message,
        public string $type = "success",
        public int $delay = 10000,
        ) {}

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): Channel
    {
        return new Channel('toast-channel');
    }
}
```

> Abrimos el archivo `SendEmailJob` ubicado en `app\Jobs\` y  añadimos lo siguiente.

```php
<?php

namespace App\Jobs;

use App\Events\ShowToastEvent;
use App\Mail\MessageMailable;
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

    /**
     * Create a new job instance.
     */
    public function __construct(public string $email) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $correo = new MessageMailable("Esto es un mensaje");
            Mail::to($this->email)->send($correo);

            $title = Lang::get('Success when sending !');
            event(new ShowToastEvent($title, Lang::get('Success when sending your work to the email address '.$this->email), 'success', 5000));
        } catch (\Throwable $th) {
            $title = Lang::get('An unexpected error has occurred !');
            event(new ShowToastEvent($title, Lang::get('Your work could not be emailed to '.$this->email), 'danger', 20000));
        }

    }

}
```
> [!WARNING]
> No nos olvidemos de importar las clases.

> Abrimos el archivo `bootstrap.js` ubicado en `resources\js\` añadimos lo siguiente.

```js
window.Echo.channel("toast-channel").listen("ShowToastEvent", (e) => {
    CreateToast(e.title, e.message, e.type, e.delay);
});
```

## Podemos probarlo de esta manera.

> [!IMPORTANT]
> Hay que inicializar los respectivos servidores para el procesamiento.

> Typee: en la Consola:

```console
php artisan reverb:start

php artisan queue:work
```

> Abrimos el archivo `web.php` ubicado en `routes\` añadimos lo siguiente.

```php
Route::get('event', function () {
    SendEmailJob::dispatch('ejemplo@ejemplo.com');
});
```

[Subir](#top)

<a name="item9"></a>

## Canal Privado

> Abrimos el archivo `SendEmailJob` ubicado en `app\Jobs\` y  añadimos lo siguiente.

```php
public function __construct(public User $user, public string $email) {}
```

> Abrimos el archivo `web.php` ubicado en `routes\` añadimos lo siguiente.

```php
Route::get('event', function () {
    SendEmailJob::dispatch(auth()->user(), 'ejemplo@ejemplo.com');
});
```

>['!IMPORTANT']
> Pasamos el usuario conectado, dentro de job o del evento la instrucción `auth()->user()` no es reconocida.

> Abrimos el archivo `SendEmailJob` ubicado en `app\Jobs\` y  añadimos lo siguiente.

```php
public function __construct(public User $user, public string $email) {}

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
```

> Abrimos el archivo `ShowToastEvent` ubicado en `app\Events\` y  añadimos lo siguiente.

```php
public function __construct(
    public User $user,
    public string $title,
    public string $message,
    public string $type = "success",
    public int $delay = 10000,
) {}
```

```php
public function broadcastOn(): Channel
{
    return new PrivateChannel('toast-channel.'.$this->user->id);
}
```

> [!WARNING]
> No estamos definiendo una variable con el canal si no definiendo el nombre del canal este canal se llamará `private-toast-channel.(Id del usuario)`, el `private` lo coloca la clase `PrivateChannel`.

### Pasarela de canales para permitir el acceso.

> Abrimos el archivo `channels.php` ubicado en `routes\` añadimos lo siguiente.

```php
Broadcast::channel('toast-channel.{userId}', function (User $user, $userId) {
    return (int) $user->id === (int) $userId;
});
```

> [!WARNING]
> El parámetro `userId` no llega del evento si no del front end, en el evento ya hemos definido el nombre del canal configurado para el usuario que esta autentificado en el sistema por la parte del back end, En esta pasarela escuchamos el nombre del evento para eso debe de coincidir el nombre del canal de parte del front end con el nombre del canal por parte del back end.

### Laravel Echo el oyente del cliente.

> Abrimos el archivo `bootstrap.js` ubicado en `resources\js\` añadimos lo siguiente.

```js
window.Echo.private("toast-channel." + userId).listen("ShowToastEvent", (e) => {
    CreateToast(e.title, e.message, e.type, e.delay);
});
```
> [!WARNING]
> La variable `userId` sera undefined. Hay que pasarla.

> [!TIP]
> Ejemplo 1: en el mismo archivo `bootstrap.js` podemos recoger el valor de un input que este oculto en nuestro html.

```js
let userId = document.getElementById("userId").value
```

> [!TIP]
> Ejemplo 2: Si tenemos un estructura html echa con layouts podemos ir al layout principal en este proyecto seria en `plantilla.blade.php` ubicado en `resources\views\layouts` y antes de declarar `@vite` escribimos lo siguiente.

```html
<script>
    window.userId = "{{ auth()->user()->id ?? '' }}"
</script>
```

### Y lo podemos probar de la misma manera accediendo la ruta `envent`.

> Pues eso es todo espero que sirva. 👍

[Subir](#top)

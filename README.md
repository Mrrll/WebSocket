# WebSocket

Proyecto de uso de WebSocket con laravel Reverb.

<a name="top"></a>

## Indice de Contenidos.

-   [Instalación laravel-reverb](#item1)
-   [Instalación de Laravel Echo](#item2)
-   [Creamos evento y oyente](#item3)
-   [Creamos oyente del cliente](#item4)
-   [Uso y pruebas](#item5)
-   [Mostramos toast](#item6)
-   [Creamos Email](#item7)



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

<a name="item6"></a>

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
> Abrimos el archivo `SendMessage.php` ubicado en `app\Listeners\` añadimos lo siguiente.

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

> Pues eso es todo espero que sirva. 👍

[Subir](#top)

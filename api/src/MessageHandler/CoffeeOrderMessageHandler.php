<?php

namespace App\MessageHandler;

use App\Message\CoffeeOrderMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CoffeeOrderMessageHandler
{
    public function __invoke(CoffeeOrderMessage $message)
    {
        // Simule la préparation du café
        sleep(2); // simulate delay
        file_put_contents('/var/www/html/api/coffee_log.txt', sprintf(
            "[%s] Café préparé : %s (%s, %s)\n",
            date('H:i:s'),
            $message->type,
            $message->intensity,
            $message->size
        ), FILE_APPEND);
    }
}

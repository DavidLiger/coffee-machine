<?php

namespace App\MessageHandler;

use App\Entity\CoffeeOrder;
use App\Message\CoffeeOrderMessage;
use App\Enum\CoffeeStatus;
use App\Repository\CoffeeOrderRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CoffeeOrderMessageHandler
{
    private CoffeeOrderRepository $orderRepository;

    public function __construct(
        CoffeeOrderRepository $orderRepository
    ) {
        $this->orderRepository = $orderRepository;
    }
    public function __invoke(CoffeeOrderMessage $message)
    {
        // Convertir la chaîne de caractères du statut en objet CoffeeStatus
        $status = CoffeeStatus::from($message->status); // Utilise la méthode `from()` pour la conversion

        // Créer une nouvelle commande ou la mettre à jour selon le message
        $order = new CoffeeOrder();
        $order->setType($message->type)
              ->setIntensity($message->intensity)
              ->setSize($message->size)
              ->setStatus($status)  // On utilise l'énumération CoffeeStatus ici
              ->setCreatedAt($message->createdAt ?? new \DateTime())
              ->setStartedAt($message->startedAt)
              ->setEndedAt($message->endedAt)
              ->setStepsLog($message->stepsLog);

        // Sauvegarder la commande dans la base de données
        $this->orderRepository->save($order, true);

        // Log du processus dans un fichier (facultatif)
        file_put_contents('/var/www/html/api/coffee_log.txt', sprintf(
            "[%s] Commande traitée : %s (%s, %s) - Status: %s\n",
            date('H:i:s'),
            $message->type,
            $message->intensity,
            $message->size,
            $message->status
        ), FILE_APPEND);
    }
}


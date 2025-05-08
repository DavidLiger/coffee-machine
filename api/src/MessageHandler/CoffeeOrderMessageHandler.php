<?php

namespace App\MessageHandler;

use App\Entity\CoffeeOrder;
use App\Message\CoffeeOrderMessage;
use App\Enum\CoffeeStatus;
use App\Repository\CoffeeOrderRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Doctrine\ORM\EntityManagerInterface;

#[AsMessageHandler]
class CoffeeOrderMessageHandler
{

    public function __construct(
        private CoffeeOrderRepository $orderRepository,
        private EntityManagerInterface $em
    ) {
        $this->orderRepository = $orderRepository;
    }
    
    public function __invoke(CoffeeOrderMessage $message)
    {
        // Recherche d'une commande existante avec les mêmes données et en statut pending
        $existingOrder = $this->orderRepository->findOneBy([
            'type' => $message->type,
            'intensity' => $message->intensity,
            'size' => $message->size,
            'status' => CoffeeStatus::PENDING
        ]);

        $order = $existingOrder ?? new CoffeeOrder();

        $order->setType($message->type)
            ->setIntensity($message->intensity)
            ->setSize($message->size)
            ->setStatus(CoffeeStatus::IN_PROGRESS)
            ->setCreatedAt($message->createdAt ?? new \DateTime())
            ->setStartedAt(new \DateTime())
            ->setStepsLog([
                "[" . date('H:i:s') . "] Grinding beans",
                "[" . date('H:i:s', time() + 3) . "] Heating water",
                "[" . date('H:i:s', time() + 6) . "] Pouring coffee"
            ]);

        sleep(3); // Simuler les étapes
        $order->setEndedAt(new \DateTime());
        $order->setStatus(CoffeeStatus::DONE);

        $this->orderRepository->save($order, true);
    }

}


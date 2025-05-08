<?php

// src/MessageHandler/CoffeeOrderMessageHandler.php
namespace App\MessageHandler;

use App\Message\CoffeeOrderMessage;
use App\Repository\CoffeeOrderRepository;
use App\Service\CoffeeProcessStateService;
use App\Enum\CoffeeStatus;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler()]
class CoffeeOrderMessageHandler 
{
    public function __construct(
        private CoffeeOrderRepository $orderRepository,
        private CoffeeProcessStateService $processStateService
    ) {}

    public function __invoke(CoffeeOrderMessage $message)
    {
        if (!$this->processStateService->isEnabled()) {
            // Forcer un retry en rejetant temporairement le message
            throw new \Symfony\Component\Messenger\Exception\RecoverableMessageHandlingException('Process is stopped, retry later.');
        }

        // Récupère la commande la plus ancienne en attente
        $order = $this->orderRepository->findOldestPendingOrder();

        if (!$order) {
            // L'ordre n'existe pas (erreur logique), on ignore
            return;
        }

        $order->setStatus(CoffeeStatus::IN_PROGRESS);
        $order->setStartedAt(new \DateTime());
        $order->setStepsLog([]);
        $this->orderRepository->save($order, true);

        $steps = [];

        // Étape 1 : Grinding
        $steps[] = "[" . date('H:i:s') . "] Grinding beans";
        $order->setStepsLog($steps);
        $this->orderRepository->save($order, true);
        sleep($this->getGrindingTime($order->getIntensity()));

        // Étape 2 : Heating water
        $steps[] = "[" . date('H:i:s') . "] Heating water";
        $order->setStepsLog($steps);
        $this->orderRepository->save($order, true);
        sleep($this->getWaterTime($order->getSize()));

        // Étape 3 : Pouring
        $steps[] = "[" . date('H:i:s') . "] Pouring coffee";
        $order->setStepsLog($steps);
        $order->setEndedAt(new \DateTime());
        $order->setStatus(CoffeeStatus::DONE);
        $this->orderRepository->save($order, true);
    }

    private function getGrindingTime(string $intensity): int
    {
        return match (strtolower($intensity)) {
            'high' => 5,
            'medium' => 3,
            'low' => 2,
            default => 3,
        };
    }

    private function getWaterTime(string $size): int
    {
        return match (strtolower($size)) {
            'long' => 5,
            'medium' => 3,
            'short' => 2,
            default => 3,
        };
    }
}



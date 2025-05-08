<?php

namespace App\Controller;

use App\Entity\CoffeeOrder;
use App\Enum\CoffeeStatus;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Message\CoffeeOrderMessage;
use App\Repository\CoffeeOrderRepository;
use Symfony\Component\Messenger\MessageBusInterface;

class CoffeeOrderController extends AbstractController
{
    private $coffeeProcessService;
    private CoffeeOrderRepository $orderRepository;

    public function __construct(
        private MessageBusInterface $bus,
        CoffeeOrderRepository $orderRepository
    ) {
        $this->orderRepository = $orderRepository;
    }

    #[Route('/order', name: 'order_coffee', methods: ['POST'])]
    public function orderCoffee(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $order = new CoffeeOrder();
        $order->setType($data['type'])
              ->setIntensity($data['intensity'])
              ->setSize($data['size'])
              ->setStatus(CoffeeStatus::PENDING)
              ->setCreatedAt(new \DateTime());

        // On crée un message avec les données de la commande
        $message = new CoffeeOrderMessage(
            $data['type'],
            $data['intensity'],
            $data['size'],
            CoffeeStatus::PENDING->value,
            new \DateTime() // On ajoute la date de création
        );

        // Envoie du message dans la file RabbitMQ (via le bus Symfony Messenger)
        $this->bus->dispatch($message);

        // Sauvegarder la commande dans la base de données (pour l'historique)
        $this->orderRepository->save($order, true);

        return new JsonResponse([
            'status' => 'Order queued',
            'order' => $data
        ]);
    }


    #[Route('/start', name: 'start_process', methods: ['POST'])]
    public function startProcess()
    {
        $this->coffeeProcessService->startProcess();

        return new JsonResponse([
            'status' => 'Process started'
        ]);
    }

    #[Route('/stop', name: 'stop_process', methods: ['POST'])]
    public function stopProcess()
    {
        $this->coffeeProcessService->stopProcess();

        return new JsonResponse([
            'status' => 'Process stopped'
        ]);
    }

    #[Route('/orders/queue', name: 'orders_queue', methods: ['GET'])]
    public function getQueueOrders(): JsonResponse
    {
        $orders = $this->orderRepository->findPendingOrders();
        return new JsonResponse($orders);
    }
    
    #[Route('/orders/current', name: 'orders_current', methods: ['GET'])]
    public function getCurrentOrder(): JsonResponse
    {
        $order = $this->orderRepository->findCurrentOrder();
        return new JsonResponse($order);
    }
    
    #[Route('/orders/history', name: 'orders_history', methods: ['GET'])]
    public function getCompletedOrders(): JsonResponse
    {
        $orders = $this->orderRepository->findCompletedOrders();
        return new JsonResponse($orders);
    }    

    #[Route('/order/{id}', name: 'order_detail', methods: ['GET'])]
    public function orderDetail(int $id): JsonResponse
    {
        $order = $this->orderRepository->find($id);
        if (!$order) {
            return $this->json(['error' => 'Order not found'], 404);
        }
        return $this->json($order);
    }
}

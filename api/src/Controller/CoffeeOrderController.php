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
use App\Service\CoffeeProcessStateService;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;

class CoffeeOrderController extends AbstractController
{
    private CoffeeOrderRepository $orderRepository;

    public function __construct(
        private MessageBusInterface $bus,
        CoffeeOrderRepository $orderRepository,
        private CoffeeProcessStateService $coffeeProcessService 
    ) {
        $this->orderRepository = $orderRepository;
    }

    #[Route('/order', name: 'order_coffee', methods: ['POST'])]
    public function orderCoffee(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $uuid = Uuid::v4()->toRfc4122();

        $order = new CoffeeOrder();
        $order->setExternalId($uuid);
        $order->setType($data['type']);
        $order->setIntensity($data['intensity']);
        $order->setSize($data['size']);
        $order->setStatus(CoffeeStatus::PENDING);
        $order->setCreatedAt(new \DateTime());
        $this->orderRepository->save($order, true);

        // On crée un message avec les données de la commande
        $message = new CoffeeOrderMessage(
            $uuid,
            $data['type'],
            $data['intensity'],
            $data['size'],
            CoffeeStatus::PENDING->value,
            new \DateTime()
        );

        // Envoie du message dans la file RabbitMQ (via le bus Symfony Messenger)
        $this->bus->dispatch($message);

        return new JsonResponse([
            'status' => 'Order queued',
            'orderId' => $uuid
        ]);
    }


    #[Route('/start', name: 'start_process', methods: ['POST'])]
    public function startProcess()
    {
        $this->coffeeProcessService->start();

        return new JsonResponse([
            'status' => 'Process started'
        ]);
    }

    #[Route('/stop', name: 'stop_process', methods: ['POST'])]
    public function stopProcess()
    {
        $this->coffeeProcessService->stop();

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
        $order = $this->orderRepository->findOneBy(['status' => CoffeeStatus::IN_PROGRESS]);
    
        if (!$order) {
            return new JsonResponse(['message' => 'No order in progress'], 404);
        }
    
        return $this->json($order, 200, [], ['groups' => ['coffee:read']]);
    }
    
    #[Route('/orders/history', name: 'orders_history', methods: ['GET'])]
    public function getOrdersHistory(): JsonResponse
    {
        $orders = $this->orderRepository->findBy(
            ['status' => CoffeeStatus::DONE],
            ['endedAt' => 'DESC']
        );
    
        $data = array_map(function (CoffeeOrder $order) {
            return [
                'id' => $order->getId(),
                'type' => $order->getType(),
                'intensity' => $order->getIntensity(),
                'size' => $order->getSize(),
                'status' => $order->getStatus()->value,
                'createdAt' => $order->getCreatedAt()->format('Y-m-d H:i:s'),
                'startedAt' => $order->getStartedAt()?->format('Y-m-d H:i:s'),
                'endedAt' => $order->getEndedAt()?->format('Y-m-d H:i:s'),
                'stepsLog' => $order->getStepsLog(),
            ];
        }, $orders);
        
        return new JsonResponse($data);
        
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

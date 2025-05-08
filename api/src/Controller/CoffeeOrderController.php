<?php

namespace App\Controller;

use App\Entity\CoffeeOrder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Message\CoffeeOrderMessage;
use Symfony\Component\Messenger\MessageBusInterface;

class CoffeeOrderController extends AbstractController
{
    private $coffeeProcessService;

    public function __construct(private MessageBusInterface $bus){}

    #[Route('/order', name: 'order_coffee', methods: ['POST'])]
    public function orderCoffee(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $message = new CoffeeOrderMessage(
            $data['type'],
            $data['intensity'],
            $data['size']
        );

        $this->bus->dispatch($message);

        return new JsonResponse([
            'status' => 'Order queued',
            'order' => $data
        ]);
    }

    // #[Route('/order', name: 'order_coffee', methods: ['POST'])]
    // public function orderCoffee(Request $request)
    // {
    //     $data = json_decode($request->getContent(), true);

    //     $order = new CoffeeOrder();
    //     $order->setType($data['type'])
    //           ->setIntensity($data['intensity'])
    //           ->setSize($data['size']);

    //     $this->coffeeProcessService->addOrder($order);

    //     return new JsonResponse([
    //         'status' => 'Order received',
    //         'order' => $data
    //     ]);
    // }

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
}

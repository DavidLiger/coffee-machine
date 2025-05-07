<?php

namespace App\Controller;

use App\Entity\CoffeeOrder;
use App\Service\CoffeeProcessService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CoffeeOrderController extends AbstractController
{
    private $coffeeProcessService;

    public function __construct(CoffeeProcessService $coffeeProcessService)
    {
        $this->coffeeProcessService = $coffeeProcessService;
    }

    #[Route('/order', name: 'order_coffee', methods: ['POST'])]
    public function orderCoffee(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $order = new CoffeeOrder();
        $order->setType($data['type'])
              ->setIntensity($data['intensity'])
              ->setSize($data['size']);

        $this->coffeeProcessService->addOrder($order);

        return new JsonResponse([
            'status' => 'Order received',
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
}

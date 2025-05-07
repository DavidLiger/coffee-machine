<?php
namespace App\Service;

use App\Entity\CoffeeOrder;

class CoffeeProcessService
{
    private $orders = [];
    private $isRunning = false;

    public function addOrder(CoffeeOrder $order)
    {
        $this->orders[] = $order;
    }

    public function startProcess()
    {
        $this->isRunning = true;
        while ($this->isRunning && !empty($this->orders)) {
            $order = array_shift($this->orders);
            $this->prepareCoffee($order);
        }
    }

    public function stopProcess()
    {
        $this->isRunning = false;
    }

    public function prepareCoffee(CoffeeOrder $order)
    {
        // Simuler la préparation du café avec un délai pour chaque étape
        echo "Preparing coffee: " . $order->getType() . "...\n";
        sleep(2);  // Simuler un délai pour chaque étape du processus
        echo "Coffee " . $order->getType() . " is ready!\n";
    }
}

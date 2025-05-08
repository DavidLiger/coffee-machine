<?php

namespace App\Service;

use App\Entity\CoffeeProcessState;
use Doctrine\ORM\EntityManagerInterface;

class CoffeeProcessStateService
{
    public function __construct(private EntityManagerInterface $em) {}

    public function start(): void
    {
        $state = $this->getState();
        $state->setIsEnabled(true);
        $this->em->flush();
    }

    public function stop(): void
    {
        $state = $this->getState();
        $state->setIsEnabled(false);
        $this->em->flush();
    }

    public function isEnabled(): bool
    {
        return $this->getState()->isEnabled();
    }

    private function getState(): CoffeeProcessState
    {
        $repo = $this->em->getRepository(CoffeeProcessState::class);
        $state = $repo->find(1);

        if (!$state) {
            $state = new CoffeeProcessState();
            $this->em->persist($state);
            $this->em->flush();
        }

        return $state;
    }
}


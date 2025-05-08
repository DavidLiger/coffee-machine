<?php

namespace App\EventListener;

use App\Entity\CoffeeProcessState;
use Doctrine\ORM\EntityManagerInterface;

class AppStartupListener
{
    public function __construct(private EntityManagerInterface $em)
    {
        $repo = $this->em->getRepository(CoffeeProcessState::class);

        if (!$repo->findOneBy([])) {
            $state = new CoffeeProcessState();
            $state->setIsEnabled(true);

            $this->em->persist($state);
            $this->em->flush();
        }
    }
}

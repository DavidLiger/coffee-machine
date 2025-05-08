<?php

namespace App\EventListener;


use App\Entity\CoffeeProcessState;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AppStartupListener implements EventSubscriberInterface
{
    public function __construct(private EntityManagerInterface $em) {}

    public function onKernelBoot(KernelEvent $event): void
    {
        $repo = $this->em->getRepository(CoffeeProcessState::class);

        if (!$repo->findOneBy([])) {
            $state = new CoffeeProcessState();
            $state->setIsEnabled(true);

            $this->em->persist($state);
            $this->em->flush();
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelBoot',
        ];
    }
}

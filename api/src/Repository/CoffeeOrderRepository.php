<?php

namespace App\Repository;

use App\Entity\CoffeeOrder;
use App\Enum\CoffeeStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CoffeeOrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CoffeeOrder::class);
    }

    // Ajout d'une nouvelle commande
    public function save(CoffeeOrder $order, bool $flush = false): void
    {
        $entityManager = $this->getEntityManager(); // Utilise la méthode getEntityManager()
        $entityManager->persist($order);

        if ($flush) {
            $entityManager->flush();
        }
    }

    // Trouver toutes les commandes (ex : pour l'historique ou la file d'attente)
    public function findAllOrders(): array
    {
        return $this->findBy([], ['createdAt' => 'DESC']);
    }

    // Trouver les commandes en attente
    public function findPendingOrders(): array
    {
        return $this->findBy(['status' => 'PENDING'], ['createdAt' => 'ASC']);
    }

    // Trouver la commande en cours
    public function findCurrentOrder(): ?CoffeeOrder
    {
        return $this->findOneBy(['status' => CoffeeStatus::IN_PROGRESS], ['createdAt' => 'ASC']);
    }

    // Trouver les commandes terminées
    public function findCompletedOrders(): array
    {
        return $this->findBy(['status' => CoffeeStatus::DONE], ['createdAt' => 'DESC']);
    }

    public function findOldestPendingOrder(): ?CoffeeOrder
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.status = :status')
            ->setParameter('status', CoffeeStatus::PENDING)
            ->orderBy('o.createdAt', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

}

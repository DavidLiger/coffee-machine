<?php

namespace App\Message;

class CoffeeOrderMessage
{
    public function __construct(
        public readonly string $type,
        public readonly string $intensity,
        public readonly string $size,
        public readonly string $status = 'PENDING',  // Par défaut, la commande est en attente
        public readonly ?\DateTime $createdAt = null, // Date de création
        public readonly ?\DateTime $startedAt = null, // Date de début
        public readonly ?\DateTime $endedAt = null,   // Date de fin
        public readonly ?array $stepsLog = null       // Journal des étapes
    ) {}
}


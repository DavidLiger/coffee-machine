<?php

namespace App\Message;

class CoffeeOrderMessage
{
    public function __construct(
        public readonly string $type,
        public readonly string $intensity,
        public readonly string $size
    ) {}
}

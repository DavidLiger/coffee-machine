<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Enum\CoffeeStatus;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new \ApiPlatform\Metadata\GetCollection(),
        new \ApiPlatform\Metadata\Post(),
        new \ApiPlatform\Metadata\Get()
    ],
    normalizationContext: ['groups' => ['coffee:read']],
    denormalizationContext: ['groups' => ['coffee:write']],
)]
class CoffeeOrder
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 36, unique: true)]
    #[Groups(['coffee:read', 'coffee:write'])]
    private string $externalId;

    #[Groups(['coffee:read', 'coffee:write'])]
    #[ORM\Column(type: 'string')]
    private string $type;

    #[Groups(['coffee:read', 'coffee:write'])]
    #[ORM\Column(type: 'string')]
    private string $intensity;

    #[Groups(['coffee:read', 'coffee:write'])]
    #[ORM\Column(type: 'string')]
    private string $size;

    #[Groups(['coffee:read'])]
    #[ORM\Column(type: 'string', enumType: CoffeeStatus::class)]
    private CoffeeStatus $status = CoffeeStatus::PENDING;

    #[Groups(['coffee:read'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private \DateTime $createdAt;

    #[Groups(['coffee:read'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTime $startedAt = null;

    #[Groups(['coffee:read'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTime $endedAt = null;

    #[Groups(['coffee:read'])]
    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $stepsLog = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    public function setExternalId(string $externalId): self
    {
        $this->externalId = $externalId;
        return $this;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getIntensity(): ?string
    {
        return $this->intensity;
    }

    public function setIntensity(string $intensity): self
    {
        $this->intensity = $intensity;
        return $this;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(string $size): self
    {
        $this->size = $size;
        return $this;
    }

    public function getStatus(): CoffeeStatus
    {
        return $this->status;
    }

    public function setStatus(CoffeeStatus $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getStartedAt(): ?\DateTime
    {
        return $this->startedAt;
    }

    public function setStartedAt(?\DateTime $startedAt): self
    {
        $this->startedAt = $startedAt;
        return $this;
    }

    public function getEndedAt(): ?\DateTime
    {
        return $this->endedAt;
    }

    public function setEndedAt(?\DateTime $endedAt): self
    {
        $this->endedAt = $endedAt;
        return $this;
    }

    public function getStepsLog(): ?array
    {
        return $this->stepsLog;
    }

    public function setStepsLog(?array $stepsLog): self
    {
        $this->stepsLog = $stepsLog;
        return $this;
    }

}




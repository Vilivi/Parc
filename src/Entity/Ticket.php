<?php

namespace App\Entity;

use App\Repository\TicketRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TicketRepository::class)
 */
class Ticket
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $ticketPrice;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ticketName;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTicketType(): ?int
    {
        return $this->ticketType;
    }

    public function setTicketType(int $ticketType): self
    {
        $this->ticketType = $ticketType;

        return $this;
    }

    public function getTicketPrice(): ?float
    {
        return $this->ticketPrice;
    }

    public function setTicketPrice(float $ticketPrice): self
    {
        $this->ticketPrice = $ticketPrice;

        return $this;
    }

    public function getTicketName(): ?string
    {
        return $this->ticketName;
    }

    public function setTicketName(string $ticketName): self
    {
        $this->ticketName = $ticketName;

        return $this;
    }
}

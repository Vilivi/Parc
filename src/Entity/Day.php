<?php

namespace App\Entity;

use App\Repository\DayRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DayRepository::class)
 */
class Day
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $remainingTickets = 1000;

    /**
     * @ORM\ManyToMany(targetEntity=Receipt::class, inversedBy="days")
     */
    private $receipts;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    public function __construct()
    {
        $this->receipts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRemainingTickets(): ?int
    {
        return $this->remainingTickets;
    }

    public function setRemainingTickets(int $remainingTickets): self
    {
        $this->remainingTickets = $remainingTickets;

        return $this;
    }

    /**
     * @return Collection|Receipt[]
     */
    public function getReceipts(): Collection
    {
        return $this->receipts;
    }

    public function addReceipt(Receipt $receipt): self
    {
        if (!$this->receipts->contains($receipt)) {
            $this->receipts[] = $receipt;
        }

        return $this;
    }

    public function removeReceipt(Receipt $receipt): self
    {
        $this->receipts->removeElement($receipt);

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }
}

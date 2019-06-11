<?php

namespace Entities;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

/**
 * Class Transaction
 * @package Entities
 * @Entity
 * @Table(name="transactions")
 */
class Transaction
{
    /**
     * @var string
     * @Id
     * @Column(type="string", length=16)
     */
    protected $id;

    /**
     * @var string
     * @Column(type="string", length=16, nullable=true)
     */
    protected $id_other;

    /**
     * @var DateTime
     * @Column(type="date")
     */
    protected $date_transaction;

    /**
     * @var DateTime
     * @Column(type="date")
     */
    protected $date_value;

    /**
     * @var string
     * @Column(type="string")
     */
    protected $type;

    /**
     * @var string
     * @Column(type="string")
     */
    protected $sender;

    /**
     * @var string
     * @Column(type="string", length=8, nullable=true)
     */
    protected $bic;

    /**
     * @var string
     * @Column(type="string", length=34)
     */
    protected $iban;

    /**
     * @var float
     * @Column(type="float")
     */
    protected $amount;

    /**
     * @var string
     * @Column(type="string")
     */
    protected $reason;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getIdOther(): string
    {
        return $this->id_other;
    }

    /**
     * @param string $id_other
     */
    public function setIdOther(string $id_other): void
    {
        $this->id_other = $id_other;
    }

    /**
     * @return DateTime
     */
    public function getDateTransaction(): DateTime
    {
        return $this->date_transaction;
    }

    /**
     * @param DateTime $date_transaction
     */
    public function setDateTransaction(DateTime $date_transaction): void
    {
        $this->date_transaction = $date_transaction;
    }

    /**
     * @return DateTime
     */
    public function getDateValue(): DateTime
    {
        return $this->date_value;
    }

    /**
     * @param DateTime $date_value
     */
    public function setDateValue(DateTime $date_value): void
    {
        $this->date_value = $date_value;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getSender(): string
    {
        return $this->sender;
    }

    /**
     * @param string $sender
     */
    public function setSender(string $sender): void
    {
        $this->sender = $sender;
    }

    /**
     * @return string
     */
    public function getBic(): string
    {
        return $this->bic;
    }

    /**
     * @param string $bic
     */
    public function setBic(string $bic): void
    {
        $this->bic = $bic;
    }

    /**
     * @return string
     */
    public function getIban(): string
    {
        return $this->iban;
    }

    /**
     * @param string $iban
     */
    public function setIban(string $iban): void
    {
        $this->iban = $iban;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @return string
     */
    public function getReason(): string
    {
        return $this->reason;
    }

    /**
     * @param string $reason
     */
    public function setReason(string $reason): void
    {
        $this->reason = $reason;
    }

}

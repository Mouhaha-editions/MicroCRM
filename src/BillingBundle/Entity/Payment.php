<?php

namespace BillingBundle\Entity;

use CustomerBundle\Entity\Customer;
use CustomerBundle\Entity\CustomerAddress;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 *
 * @ORM\Table(name="payment")
 * @ORM\Entity(repositoryClass="BillingBundle\Repository\PaymentRepository")
 */
class Payment
{


    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="decimal",scale=2,precision=10, nullable=false)
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="label", type="string",length=200, nullable=false)
     */
    private $label;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=true)
     */
    private $date;



    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set amount.
     *
     * @param string $amount
     *
     * @return Payment
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount.
     *
     * @return string
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set date.
     *
     * @param \DateTime|null $date
     *
     * @return Payment
     */
    public function setDate($date = null)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date.
     *
     * @return \DateTime|null
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set label.
     *
     * @param string $label
     *
     * @return Payment
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label.
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }
}

<?php

namespace BillingBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 *
 * @ORM\Table(name="sales_document_details")
 * @ORM\Entity(repositoryClass="BillingBundle\Repository\SalesDocumentDetailRepository")
 */
class SalesDocumentDetail
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
     * @ORM\Column(name="reference", type="string", length=255, nullable=true)
     */
    private $reference;

    /**
     * @var string
     *
     * @ORM\Column(name="label", type="string", length=255, nullable=true)
     */
    private $label;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=true)
     */
    private $date;
    /**
     * @var int
     *
     * @ORM\Column(name="duration", type="integer",nullable=true)
     */
    private $duration = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="unit_amount_ht", type="decimal",scale=2, precision=10, nullable=true)
     */
    private $unit_amount_ht = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="taxes", type="decimal",scale=2, precision=10, nullable=true)
     */
    private $taxes = 20;

    /**
     * @var float
     *
     * @ORM\Column(name="quantity", type="decimal",scale=2, precision=10, nullable=true)
     */
    private $quantity = 1;

    /**
     * @var float
     *
     * @ORM\Column(name="total_amount_ttc", type="decimal",scale=2, precision=10, nullable=true)
     */
    private $total_amount_ttc = 0;

    /**
     * @var SalesDocument
     *
     * @ORM\ManyToOne(targetEntity="BillingBundle\Entity\SalesDocument",inversedBy="details")
     * @ORM\JoinColumn(name="id_sales_document", referencedColumnName="id")
     *
     */
    private $salesDocument;

    /**
     * @var float
     *
     * @ORM\Column(name="taxes_to_apply", type="decimal",scale=2, precision=10, nullable=true)
     */

    private $taxes_to_apply;
    /**
     * @var float
     *
     * @ORM\Column(name="buy_price", type="decimal",scale=2, precision=10, nullable=true)
     */

    private $buyPrice;
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
     * Set label.
     *
     * @param string $label
     *
     * @return SalesDocumentDetail
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

    /**
     * Set description.
     *
     * @param string|null $description
     *
     * @return SalesDocumentDetail
     */
    public function setDescription($description = null)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set unitAmountHt.
     *
     * @param string $unitAmountHt
     *
     * @return SalesDocumentDetail
     */
    public function setUnitAmountHt($unitAmountHt)
    {
        $this->unit_amount_ht = $unitAmountHt;

        return $this;
    }

    /**
     * Get unitAmountHt.
     *
     * @return string
     */
    public function getUnitAmountHt()
    {
        return $this->unit_amount_ht;
    }

    /**
     * Set taxes.
     *
     * @param string $taxes
     *
     * @return SalesDocumentDetail
     */
    public function setTaxes($taxes)
    {
        $this->taxes = $taxes;

        return $this;
    }

    /**
     * Get taxes.
     *
     * @return string
     */
    public function getTaxes()
    {
        return $this->taxes;
    }

    /**
     * Set quantity.
     *
     * @param string $quantity
     *
     * @return SalesDocumentDetail
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity.
     *
     * @return string
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set totalAmountTtc.
     *
     * @param string $totalAmountTtc
     *
     * @return SalesDocumentDetail
     */
    public function setTotalAmountTtc($totalAmountTtc)
    {
        $this->total_amount_ttc = $totalAmountTtc;

        return $this;
    }

    /**
     * Get totalAmountTtc.
     *
     * @return string
     */
    public function getTotalAmountTtc()
    {
        return $this->total_amount_ttc;
    }

    /**
     * Get totalAmountTtc.
     *
     * @return string
     */
    public function getTotalAmountHt()
    {
        return $this->getUnitAmountHt() * $this->getQuantity();
    }

    /**
     * Set isDeleted.
     *
     * @param bool $isDeleted
     *
     * @return SalesDocumentDetail
     */
    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    /**
     * Get isDeleted.
     *
     * @return bool
     */
    public function getIsDeleted()
    {
        return $this->isDeleted;
    }

    /**
     * Set dateCreated.
     *
     * @param \DateTime|null $dateCreated
     *
     * @return SalesDocumentDetail
     */
    public function setDateCreated($dateCreated = null)
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * Get dateCreated.
     *
     * @return \DateTime|null
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Set dateUpdated.
     *
     * @param \DateTime|null $dateUpdated
     *
     * @return SalesDocumentDetail
     */
    public function setDateUpdated($dateUpdated = null)
    {
        $this->dateUpdated = $dateUpdated;

        return $this;
    }

    /**
     * Get dateUpdated.
     *
     * @return \DateTime|null
     */
    public function getDateUpdated()
    {
        return $this->dateUpdated;
    }

    /**
     * Set salesDocument.
     *
     * @param \BillingBundle\Entity\SalesDocument|null $salesDocument
     *
     * @return SalesDocumentDetail
     */
    public function setSalesDocument(\BillingBundle\Entity\SalesDocument $salesDocument = null)
    {
        $this->salesDocument = $salesDocument;

        return $this;
    }

    /**
     * Get salesDocument.
     *
     * @return \BillingBundle\Entity\SalesDocument|null
     */
    public function getSalesDocument()
    {
        return $this->salesDocument;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return SalesDocumentDetail
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set reference
     *
     * @param string $reference
     *
     * @return SalesDocumentDetail
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Get reference
     *
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Set duration
     *
     * @param integer $duration
     *
     * @return SalesDocumentDetail
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration
     *
     * @return integer
     */
    public function getDuration()
    {
        return $this->duration != null ? $this->duration : 0;
    }

    /**
     * @return DateTime
     */
    public function getEndDate()
    {
        $date = clone $this->getDate();
        return $date->modify('+' . $this->getDuration() . " minutes");
    }

    /**
     * @return float
     */
    public function getTaxesToApply()
    {
        return $this->taxes_to_apply;
    }

    /**
     * @param float $taxes_to_apply
     */
    public function setTaxesToApply($taxes_to_apply)
    {
        $this->taxes_to_apply = $taxes_to_apply;
    }

    /**
     * @return float
     */
    public function getBuyPrice()
    {
        return $this->buyPrice;
    }

    /**
     * @param float $buyPrice
     */
    public function setBuyPrice($buyPrice)
    {
        $this->buyPrice = $buyPrice;
    }


}

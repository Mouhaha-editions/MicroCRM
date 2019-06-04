<?php

namespace BillingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 *
 * @ORM\Table(name="sales_document_product")
 * @ORM\Entity(repositoryClass="BillingBundle\Repository\SalesDocumentProductRepository")
 */
class SalesDocumentProduct
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
     * @ORM\Column(name="taxes_to_apply", type="decimal",scale=2, precision=10, nullable=true)
     */

    private $taxes_to_apply;

    /**
     * @var integer
     *
     * @ORM\Column(name="duration", type="integer", nullable=true)
     */
    private $duration;

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
     * @param string|null $label
     *
     * @return SalesDocumentProduct
     */
    public function setLabel($label = null)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label.
     *
     * @return string|null
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
     * @return SalesDocumentProduct
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
     * @param string|null $unitAmountHt
     *
     * @return SalesDocumentProduct
     */
    public function setUnitAmountHt($unitAmountHt = null)
    {
        $this->unit_amount_ht = $unitAmountHt;

        return $this;
    }

    /**
     * Get unitAmountHt.
     *
     * @return string|null
     */
    public function getUnitAmountHt()
    {
        return $this->unit_amount_ht;
    }

    /**
     * Set taxes.
     *
     * @param string|null $taxes
     *
     * @return SalesDocumentProduct
     */
    public function setTaxes($taxes = null)
    {
        $this->taxes = $taxes;

        return $this;
    }

    /**
     * Get taxes.
     *
     * @return string|null
     */
    public function getTaxes()
    {
        return $this->taxes;
    }

    /**
     * @return float
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param float $duration
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
    }


    /**
     * Set reference
     *
     * @param string $reference
     *
     * @return SalesDocumentProduct
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




}

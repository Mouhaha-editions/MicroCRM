<?php

namespace BillingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 *
 * @ORM\Table(name="sales_document_payments")
 * @ORM\Entity(repositoryClass="BillingBundle\Repository\SalesDocumentPayment")
 */
class SalesDocumentPayment
{
    const TYPE_CLOTURE_FACTURE = 500;
    const TYPE_PERTE = 600;
    const TYPE_CHEQUE = 200;
    const TYPE_PRELEVEMENT = 300;
    const TYPE_VIREMENT = 400;

    const STATE_WAITING = 100;
    const STATE_PAID = 200;
    const STATE_UNPAID = 300;
    const STATE_CANCELED = 400;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    private static $ReverseState = [
        self::STATE_CANCELED => "Annulé",
        self::STATE_PAID => "Payé",
        self::STATE_UNPAID => "Non payé",
        self::STATE_WAITING => "En attente",
    ];
    private static $ReverseType = [
        0 => "Aucun",
        self::TYPE_PERTE => "Perte",
        self::TYPE_CLOTURE_FACTURE => "Cloture Facture",
        self::TYPE_CHEQUE => "Chèque",
        self::TYPE_VIREMENT => "Virement",
        self::TYPE_PRELEVEMENT => "Prélèvement",

    ];

    /**
     * @var string
     *
     * @ORM\Column(name="label", type="string", length=255, nullable=true)
     */
    private $label;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text", nullable=true)
     */
    private $comment;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=false)
     */
    private $date;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="decimal",scale=2, precision=10, nullable=true)
     */
    private $amount = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="integer", nullable=false)
     */
    private $type = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="state", type="integer", nullable=false)
     */
    private $state = 0;

    /**
     * @var SalesDocument
     *
     * @ORM\ManyToOne(targetEntity="BillingBundle\Entity\SalesDocument",inversedBy="payments",cascade={"persist","remove"})
     * @ORM\JoinColumn(name="id_sales_document", referencedColumnName="id")
     *
     */
    private $salesDocument;
    /**
     * @var float
     *
     * @ORM\Column(name="transaction_taxe", type="decimal",scale=2, precision=10, nullable=true)
     */
    private $transaction_taxe = 0;
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
     * Get label.
     *
     * @return string|null
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set label.
     *
     * @param string|null $label
     *
     * @return SalesDocumentPayment
     */
    public function setLabel($label = null)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get comment.
     *
     * @return string|null
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set comment.
     *
     * @param string|null $comment
     *
     * @return SalesDocumentPayment
     */
    public function setComment($comment = null)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get date.
     *
     * @return string
     */
    public function getDateStr()
    {
        return $this->getDate() != null ? $this->getDate()->format('d/m/Y') : '';
    }

    /**
     * Get date.
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set date.
     *
     * @param \DateTime $date
     *
     * @return SalesDocumentPayment
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get amount.
     *
     * @return string|null
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set amount.
     *
     * @param string|null $amount
     *
     * @return SalesDocumentPayment
     */
    public function setAmount($amount = null)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get type.
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type.
     *
     * @param int $type
     *
     * @return SalesDocumentPayment
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type.
     *
     * @return string
     */
    public function getTypeLabel()
    {
//        return $this->type;
        return self::$ReverseType[$this->type];
    }

    /**
     * Get state.
     *
     * @return int
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set state.
     *
     * @param int $state
     *
     * @return SalesDocumentPayment
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state.
     *
     * @return int
     */
    public function getStateBGColor()
    {
        switch ($this->state) {
            case self::STATE_PAID:
                return 'bg-success';
            case self::STATE_UNPAID:
                return 'bg-danger';
            case self::STATE_CANCELED:
                return 'bg-warning';
            case self::STATE_WAITING:
            default:
                return '';
        }
    }

    /**
     * Get etat.
     *
     * @return string
     */
    public function getStateLabel()
    {
        return self::$ReverseState[$this->state];
    }

    /**
     * Set isDeleted.
     *
     * @param bool $isDeleted
     *
     * @return SalesDocumentPayment
     */
    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;

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
     * Set salesDocument.
     *
     * @param \BillingBundle\Entity\SalesDocument|null $salesDocument
     *
     * @return SalesDocumentPayment
     */
    public function setSalesDocument(\BillingBundle\Entity\SalesDocument $salesDocument = null)
    {
        $this->salesDocument = $salesDocument;

        return $this;
    }

}

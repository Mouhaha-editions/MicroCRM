<?php

namespace BillingBundle\Entity;

use CustomerBundle\Entity\Customer;
use CustomerBundle\Entity\CustomerAddress;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 *
 * @ORM\Table(name="sales_document")
 * @ORM\Entity(repositoryClass="BillingBundle\Repository\SalesDocumentRepository")
 */
class SalesDocument
{
    const DEVIS = 50;
    const BON_COMMANDE = 100;
    const FACTURE = 200;
    const AVOIR = 300;
    const ERREUR = 400;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    private static $ReverseState = [
        self::DEVIS => "Devis",
        self::BON_COMMANDE => "Bon de commande",
        self::FACTURE => "Facture",
        self::AVOIR => "Avoir",
        self::ERREUR => "Erreur",
    ];

    /**
     * @var string
     *
     * @ORM\Column(name="chrono", type="text", nullable=true)
     */
    private $chrono;

    /**
     * @var integer
     *
     * @ORM\Column(name="state", type="integer", nullable=true)
     */
    private $state = self::BON_COMMANDE;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=true)
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="serialized_address", type="text", nullable=true)
     */
    private $serializedAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text", nullable=true)
     */
    private $comment;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_paid", type="boolean", nullable=false)
     */
    private $isPaid = false;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="payment_date", type="datetime", nullable=true)
     */
    private $paymentDate;

    /**
     * @var SalesDocumentDetail
     *
     * @ORM\OneToMany(targetEntity="SalesDocumentDetail",mappedBy="salesDocument",cascade={"persist","remove"},fetch="EAGER")
     * @ORM\OrderBy({"id" = "ASC"})
     */
    private $details;

    /**
     * @var Customer
     *
     * @ORM\ManyToOne(targetEntity="CustomerBundle\Entity\Customer",inversedBy="salesDocuments",cascade={"persist","remove"})
     * @ORM\JoinColumn(name="id_customer",referencedColumnName="id")
     */
    private $customer;

    /**
     * @var SalesDocumentPayment[]
     *
     * @ORM\OneToMany(targetEntity="BillingBundle\Entity\SalesDocumentPayment",mappedBy="salesDocument",cascade={"persist","remove"})
     *
     */
    private $payments;


    private $unserializedAddress;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->details = new ArrayCollection();
        $this->payments = new ArrayCollection();
    }

    public function getFullNameSelect()
    {
        return $this->getStateLabel() . " du  " . $this->getDate()->format('d/m/y') . (!empty($this->chrono) ? " - n°" . $this->chrono : '');
    }

    /**
     * Get state.
     *
     * @return int|null
     */
    public function getStateLabel()
    {
        return self::$ReverseState[$this->state];
    }

    /**
     * @return array
     */
    public static function getReverseState()
    {
        return self::$ReverseState;
    }

    /**
     * @param array $ReverseState
     */
    public static function setReverseState($ReverseState)
    {
        self::$ReverseState = $ReverseState;
    }

    /**
     * @return DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getDateStr()
    {
        return $this->getDate() != null ? $this->getDate()->format('d/m/Y') : '';
    }

    /**
     * @param DateTime $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param Customer $customer
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;
    }


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
     * Add detail.
     *
     * @param \BillingBundle\Entity\SalesDocumentDetail $detail
     *
     * @return SalesDocument
     */
    public function addDetail(SalesDocumentDetail $detail)
    {
        $this->details[] = $detail;
        $detail->setSalesDocument($this);
        return $this;
    }

    /**
     * Remove detail.
     *
     * @param \BillingBundle\Entity\SalesDocumentDetail $detail
     *
     * @return SalesDocument
     */
    public function removeDetail(SalesDocumentDetail $detail)
    {
        $this->details->removeElement($detail);
        $detail->setSalesDocument(null);
        return $this;
    }

    public function removeDetails()
    {
        foreach ($this->details AS $d) {
            $this->removeDetail($d);
        }
    }

    public function removePayments()
    {
        foreach ($this->payments AS $p) {
            $this->removePayment($p);
        }
    }


    /**
     * Get chrono.
     *
     * @return string|null
     */
    public function getChrono()
    {
        return $this->chrono;
    }

    /**
     * Set chrono.
     *
     * @param string|null $chrono
     *
     * @return SalesDocument
     */
    public function setChrono($chrono = null)
    {
        $this->chrono = $chrono;

        return $this;
    }

    /**
     * Get serializedAddress.
     *
     * @return string|null
     */
    public function getSerializedAddress()
    {
        return $this->serializedAddress;
    }

    /**
     * Set serializedAddress.
     *
     * @param string|null $serializedAddress
     *
     * @return SalesDocument
     */
    public function setSerializedAddress($serializedAddress = null)
    {
        $this->serializedAddress = $serializedAddress;

        return $this;
    }

    /**
     * Set serializedAddress.
     *
     *
     * @return CustomerAddress
     */
    public function getUnserializedAddress()
    {
        if ($this->serializedAddress == null) {
//            return $this->getCustomer()->getDefaultAdresse();
        }
        if ($this->unserializedAddress === null) {
            $this->unserializedAddress = unserialize($this->serializedAddress);
        }

        return $this->unserializedAddress;
    }

    /**
     * Set Adresse.
     *
     * @param CustomerAddress $adresse
     * @return $this
     */
    public function setAddress(CustomerAddress $adresse)
    {
        $this->serializedAddress = serialize($adresse);
        return $this;
    }

    /**
     * Le total de la facture HT
     * @return float|int
     */
    public function getTotalHT()
    {
        $total = 0;
        foreach ($this->getDetails() AS $detail) {
            $total += ($detail->getUnitAmountHt() * $detail->getQuantity());
        }
        return $total;
    }

    /**
     * Get details.
     *
     * @return \Doctrine\Common\Collections\Collection|SalesDocumentDetail[]
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * Le total de la facture TTC
     * @return float|int
     */
    public function getTotalTTC()
    {
        $total = 0;
        foreach ($this->getDetails() AS $detail) {
            $total += ($detail->getTotalAmountTtc());
        }
        return $total;
    }

    /**
     * return First detail label
     * @return string
     */
    public function getFirstDetailLabel()
    {
        if ($this->getDetails()->Count() == 0) {
            return "";
        }
        return $this->getDetails()[0]->getLabel();
    }

    /**
     * Formatted date
     * @return string
     */
    public function getDateDebutStr()
    {
        return $this->getDateDebut()->format('d/m/Y');
    }


    public function isAvoir()
    {
        return $this->getState() == self::AVOIR;
    }

    /**
     * Get state.
     *
     * @return int|null
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set state.
     *
     * @param int|null $state
     *
     * @return SalesDocument
     */
    public function setState($state = null)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * est-ce une facture ?
     * @return bool
     */
    public function isFacture()
    {
        return $this->getState() == self::FACTURE;
    }

    /**
     * est-ce une facture ?
     * @return bool
     */
    public function isDevis()
    {
        return $this->getState() == self::DEVIS;
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
     * @return SalesDocument
     */
    public function setComment($comment = null)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Add payment.
     *
     * @param \BillingBundle\Entity\SalesDocumentPayment $payment
     *
     * @return SalesDocument
     */
    public function addPayment(SalesDocumentPayment $payment)
    {
        $this->payments[] = $payment;

        return $this;
    }

    /**
     * Remove payment.
     *
     * @param \BillingBundle\Entity\SalesDocumentPayment $payment
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removePayment(SalesDocumentPayment $payment)
    {
        return $this->payments->removeElement($payment);
    }

    /**
     * Get payments.
     *
     * @return \Doctrine\Common\Collections\Collection|SalesDocumentPayment[]
     */
    public function getPayments()
    {
        return $this->payments;
    }



    /**
     * Set isPaid.
     *
     * @param bool $isPaid
     *
     * @return SalesDocument
     */
    public function setIsPaid($isPaid)
    {
        $this->isPaid = $isPaid;

        return $this;
    }

    /**
     * Get isPaid.
     *
     * @return bool
     */
    public function getIsPaid()
    {
        return $this->isPaid;
    }

    /**
     * @return DateTime
     */
    public function getPaymentDate()
    {
        return $this->paymentDate;
    }

    /**
     * @param DateTime $paymentDate
     */
    public function setPaymentDate($paymentDate)
    {
        $this->paymentDate = $paymentDate;
    }


}

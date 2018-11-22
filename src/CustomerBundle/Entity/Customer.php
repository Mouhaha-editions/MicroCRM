<?php

namespace CustomerBundle\Entity;

use CustomerBundle\Enums\ECustomerCommunicationType;
use CustomerBundle\Enums\ECustomerType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Customer
 *
 * @ORM\Table(name="customer")
 * @ORM\Entity(repositoryClass="CustomerBundle\Repository\CustomerRepository")
 */
class Customer
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="customer_type", type="integer", nullable=true)
     */
    private $customerType;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=50, nullable=true)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=50, nullable=true)
     */
    private $lastName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birthday", type="date", nullable=true)
     */
    private $birthday;

    /**
     * @var string
     *
     * @ORM\Column(name="company_name", type="string", length=50, nullable=true)
     */
    private $companyName;

    /**
     * @var string
     *
     * @ORM\Column(name="siret", type="string", length=50, nullable=true)
     */
    private $siret;
    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text",  nullable=true)
     */
    private $comment;
    /**
     * @ORM\OneToMany(targetEntity="CustomerBundle\Entity\CustomerAddress", mappedBy="customer",cascade={"persist","remove"})
     */
    private $customer_addresses;

    /**
     * @ORM\OneToMany(targetEntity="CustomerBundle\Entity\CustomerCommunication", mappedBy="customer",cascade={"persist","remove"})
     */
    private $customer_communications;
    /**
     * @ORM\OneToMany(targetEntity="BillingBundle\Entity\SalesDocument", mappedBy="customer",cascade={"persist","remove"})
     */
    private $salesDocuments;

    public function __construct()
    {
        $this->customer_addresses = new ArrayCollection();
        $this->customer_communications = new ArrayCollection();
        $this->salesDocuments = new ArrayCollection();
    }

    public function getFullName()
    {
        if ($this->getCompanyName() == null) {
            return $this->getLastName() . ' ' . $this->getFirstName();
        } elseif ($this->getLastName() == null AND $this->getFirstName() == null) {
            return $this->getCompanyName();
        }
        return $this->getCompanyName() . "(" . $this->getLastName() . ' ' . $this->getFirstName() . ")";

    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set customerType
     *
     * @param integer $customerType
     *
     * @return Customer
     */
    public function setCustomerType($customerType)
    {
        $this->customerType = $customerType;

        return $this;
    }

    /**
     * Get customerType
     *
     *
     * @return string
     */
    public function getCustomerTypeStr()
    {
        return ECustomerType::ToString($this->customerType);
    }

    /**
     * Get customerType
     *
     * @return integer
     */
    public function getCustomerType()
    {
        return $this->customerType;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return Customer
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return Customer
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set companyName
     *
     * @param string $companyName
     *
     * @return Customer
     */
    public function setCompanyName($companyName)
    {
        $this->companyName = $companyName;

        return $this;
    }

    /**
     * Get companyName
     *
     * @return string
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     * Set siret
     *
     * @param string $siret
     *
     * @return Customer
     */
    public function setSiret($siret)
    {
        $this->siret = $siret;

        return $this;
    }

    /**
     * Get siret
     *
     * @return string
     */
    public function getSiret()
    {
        return $this->siret;
    }

    /**
     * Add customerAddress
     *
     * @param \CustomerBundle\Entity\CustomerAddress $customerAddress
     *
     * @return Customer
     */
    public function addCustomerAddress(\CustomerBundle\Entity\CustomerAddress $customerAddress)
    {
        $this->customer_addresses[] = $customerAddress;
        $customerAddress->setCustomer($this);
        return $this;
    }

    /**
     * Remove customerAddress
     *
     * @param \CustomerBundle\Entity\CustomerAddress $customerAddress
     */
    public function removeCustomerAddress(\CustomerBundle\Entity\CustomerAddress $customerAddress)
    {
        $this->customer_addresses->removeElement($customerAddress);
    }

    /**
     * Get customerAddresses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCustomerAddresses()
    {
        return $this->customer_addresses;
    }

    /**
     * Add customerCommunication
     *
     * @param \CustomerBundle\Entity\CustomerCommunication $customerCommunication
     *
     * @return Customer
     */
    public function addCustomerCommunication(\CustomerBundle\Entity\CustomerCommunication $customerCommunication)
    {
        $this->customer_communications[] = $customerCommunication;
        $customerCommunication->setCustomer($this);

        return $this;
    }

    /**
     * Remove customerCommunication
     *
     * @param \CustomerBundle\Entity\CustomerCommunication $customerCommunication
     */
    public function removeCustomerCommunication(\CustomerBundle\Entity\CustomerCommunication $customerCommunication)
    {
        $this->customer_communications->removeElement($customerCommunication);
    }

    /**
     * Get customerCommunications
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCustomerCommunications()
    {
        return $this->customer_communications;
    }

    /**
     * @return \DateTime
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * @param \DateTime $birthday
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }


    /**
     * Add salesDocument
     *
     * @param \BillingBundle\Entity\SalesDocument $salesDocument
     *
     * @return Customer
     */
    public function addSalesDocument(\BillingBundle\Entity\SalesDocument $salesDocument)
    {
        $this->salesDocuments[] = $salesDocument;

        return $this;
    }

    /**
     * Remove salesDocument
     *
     * @param \BillingBundle\Entity\SalesDocument $salesDocument
     */
    public function removeSalesDocument(\BillingBundle\Entity\SalesDocument $salesDocument)
    {
        $this->salesDocuments->removeElement($salesDocument);
    }

    /**
     * Get salesDocuments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSalesDocuments()
    {
        return $this->salesDocuments;
    }
}

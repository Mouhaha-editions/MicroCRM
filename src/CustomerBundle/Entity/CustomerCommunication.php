<?php

namespace CustomerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CustomerCommunication
 *
 * @ORM\Table(name="customer_communication")
 * @ORM\Entity(repositoryClass="CustomerBundle\Repository\CustomerCommunicationRepository")
 */
class CustomerCommunication
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
     * @ORM\Column(name="customer_communication_type", type="integer", nullable=false)
     */
    private $customerCommunicationType;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=255, nullable=true)
     */
    private $value;

    /**
     * @var boolean
     *
     * @ORM\Column(name="confirmed", type="boolean", nullable=false)
     */
    private $confirmed = true;

    /**
     * @var Customer
     *
     * @ORM\ManyToOne(targetEntity="Customer",inversedBy="customer_communications")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_customer", referencedColumnName="id")
     * })
     */
    private $customer;



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
     * Set customerCommunicationType
     *
     * @param integer $customerCommunicationType
     *
     * @return CustomerCommunication
     */
    public function setCustomerCommunicationType($customerCommunicationType)
    {
        $this->customerCommunicationType = $customerCommunicationType;

        return $this;
    }

    /**
     * Get customerCommunicationType
     *
     * @return integer
     */
    public function getCustomerCommunicationType()
    {
        return $this->customerCommunicationType;
    }

    /**
     * Set value
     *
     * @param string $value
     *
     * @return CustomerCommunication
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set confirmed
     *
     * @param boolean $confirmed
     *
     * @return CustomerCommunication
     */
    public function setConfirmed($confirmed)
    {
        $this->confirmed = $confirmed;

        return $this;
    }

    /**
     * Get confirmed
     *
     * @return boolean
     */
    public function getConfirmed()
    {
        return $this->confirmed;
    }

    /**
     * Set customer
     *
     * @param \CustomerBundle\Entity\Customer $customer
     *
     * @return CustomerCommunication
     */
    public function setCustomer(\CustomerBundle\Entity\Customer $customer = null)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Get customer
     *
     * @return \CustomerBundle\Entity\Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }
}

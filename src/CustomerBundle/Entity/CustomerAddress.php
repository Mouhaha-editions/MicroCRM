<?php

namespace CustomerBundle\Entity;

use CustomerBundle\Enums\ECustomerAddressType;
use Doctrine\ORM\Mapping as ORM;

/**
 * CustomerAddress
 *
 * @ORM\Table(name="customer_address")
 * @ORM\Entity(repositoryClass="CustomerBundle\Repository\CustomerAddressRepository")
 */
class CustomerAddress
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
     * @ORM\Column(name="customer_adresse_type", type="integer", nullable=false)
     */
    private $customerAdresseType = ECustomerAddressType::BASE;

    /**
     * @var string
     *
     * @ORM\Column(name="ligne_1", type="string", length=50, nullable=true)
     */
    private $ligne1;

    /**
     * @var string
     *
     * @ORM\Column(name="ligne_2", type="string", length=50, nullable=true)
     */
    private $ligne2;

    /**
     * @var string
     *
     * @ORM\Column(name="ligne_3", type="string", length=50, nullable=true)
     */
    private $ligne3;

    /**
     * @var string
     *
     * @ORM\Column(name="code_postal", type="string", length=50, nullable=false)
     */
    private $codePostal;

    /**
     * @var string
     *
     * @ORM\Column(name="ville", type="string", length=100, nullable=false)
     */
    private $ville;

    /**
     * @var string
     *
     * @ORM\Column(name="pays", type="string", length=50, nullable=true)
     */
    private $pays;

    /**
     * @var Customer
     *
     * @ORM\ManyToOne(targetEntity="Customer",inversedBy="customer_addresses")
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
     * Set customerAdresseType
     *
     * @param integer $customerAdresseType
     *
     * @return CustomerAddress
     */
    public function setCustomerAdresseType($customerAdresseType)
    {
        $this->customerAdresseType = $customerAdresseType;

        return $this;
    }

    /**
     * Get customerAdresseType
     *
     * @return integer
     */
    public function getCustomerAdresseType()
    {
        return $this->customerAdresseType;
    }

    /**
     * Set ligne1
     *
     * @param string $ligne1
     *
     * @return CustomerAddress
     */
    public function setLigne1($ligne1)
    {
        $this->ligne1 = $ligne1;

        return $this;
    }

    /**
     * Get ligne1
     *
     * @return string
     */
    public function getLigne1()
    {
        return $this->ligne1;
    }

    /**
     * Set ligne2
     *
     * @param string $ligne2
     *
     * @return CustomerAddress
     */
    public function setLigne2($ligne2)
    {
        $this->ligne2 = $ligne2;

        return $this;
    }

    /**
     * Get ligne2
     *
     * @return string
     */
    public function getLigne2()
    {
        return $this->ligne2;
    }

    /**
     * Set ligne3
     *
     * @param string $ligne3
     *
     * @return CustomerAddress
     */
    public function setLigne3($ligne3)
    {
        $this->ligne3 = $ligne3;

        return $this;
    }

    /**
     * Get ligne3
     *
     * @return string
     */
    public function getLigne3()
    {
        return $this->ligne3;
    }

    /**
     * Set codePostal
     *
     * @param string $codePostal
     *
     * @return CustomerAddress
     */
    public function setCodePostal($codePostal)
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    /**
     * Get codePostal
     *
     * @return string
     */
    public function getCodePostal()
    {
        return $this->codePostal;
    }

    /**
     * Set ville
     *
     * @param string $ville
     *
     * @return CustomerAddress
     */
    public function setVille($ville)
    {
        $this->ville = $ville;

        return $this;
    }

    /**
     * Get ville
     *
     * @return string
     */
    public function getVille()
    {
        return $this->ville;
    }

    /**
     * Set pays
     *
     * @param string $pays
     *
     * @return CustomerAddress
     */
    public function setPays($pays)
    {
        $this->pays = $pays;

        return $this;
    }

    /**
     * Get pays
     *
     * @return string
     */
    public function getPays()
    {
        return $this->pays;
    }

    /**
     * Set customer
     *
     * @param \CustomerBundle\Entity\Customer $customer
     *
     * @return CustomerAddress
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

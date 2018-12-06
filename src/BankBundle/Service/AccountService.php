<?php
/**
 * Created by PhpStorm.
 * User: pierr
 * Date: 05/12/2018
 * Time: 22:12
 */

namespace BankBundle\Service;


use BankBundle\Entity\Account;
use Doctrine\ORM\EntityManager;

class AccountService
{
    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @param Account $account
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getAmountPointed(Account $account)
    {
        $count = $this->em->getRepository('BankBundle:Operation')
            ->createQueryBuilder('o')
            ->select('SUM(o.amount)')
            ->where('o.pointed = :true')
            ->andWhere('o.account = :account')
            ->setParameter('account', $account)
            ->setParameter('true', true)
            ->setMaxResults(1)
            ->setFirstResult(0)
            ->getQuery()->getOneOrNullResult();
        return array_pop($count);
    }
}
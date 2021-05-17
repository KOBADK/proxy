<?php

namespace Itk\ExchangeBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Itk\ExchangeBundle\Entity\Resource;

/**
 * @method Resource|null find($id, $lockMode = null, $lockVersion = null)
 * @method Resource|null findOneBy(array $criteria, array $orderBy = null)
 * @method Resource[]    findAll()
 * @method Resource[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResourceRepository extends EntityRepository
{
    /**
     * Get one resource by mail.
     *
     * @param string $mail
     *   Resource mail-address.
     *
     * @return int|mixed|string|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByMail(string $mail)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.mail = :val')
            ->setParameter('val', $mail)
            ->getQuery()
            ->getOneOrNullResult();
    }
}

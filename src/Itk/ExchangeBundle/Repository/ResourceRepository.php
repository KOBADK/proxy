<?php

namespace Itk\ExchangeBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Itk\ExchangeBundle\Entity\Resource;

/**
 * @method Resource|null find($id, $lockMode = null, $lockVersion = null)
 * @method Resource|null findOneBy(array $criteria, array $orderBy = null)
 * @method Resource[]    findAll()
 * @method Resource[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResourceRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Resource::class);
    }

    /**
     * Get one resource by mail.
     *
     * @param string $mail
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByMail($mail)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.mail = :val')
            ->setParameter('val', $mail)
            ->getQuery()
            ->getOneOrNullResult();
    }
}

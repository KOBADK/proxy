<?php

namespace Koba\MainBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Koba\MainBundle\Entity\ApiKey;

/**
 * @method ApiKey|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApiKey|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApiKey[]    findAll()
 * @method ApiKey[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApiKeyRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ApiKey::class);
    }

    /**
     * Find one by apiKey
     *
     * @param $apiKey
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByApiKey($apiKey) {
        return $this->createQueryBuilder('a')
            ->andWhere('a.apiKey = :val')
            ->setParameter('val', $apiKey)
            ->getQuery()
            ->getOneOrNullResult();
    }
}

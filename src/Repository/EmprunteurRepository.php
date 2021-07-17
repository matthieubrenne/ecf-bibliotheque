<?php

namespace App\Repository;

use App\Entity\Emprunteur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Emprunteur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Emprunteur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Emprunteur[]    findAll()
 * @method Emprunteur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmprunteurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Emprunteur::class);
    }

    public function findByUserId($userId)
    {
        return $this->createQueryBuilder('e')
        ->innerJoin('e.user', 'u')
        ->andWhere('u.id LIKE :userId')
        ->setParameter('userId', "{$userId}")
        ->orderBy('e.id','ASC')
        ->getQuery()
        ->getResult();
    }

    public function findByNomOuPrenom($value)
    {
        $qb = $this->createQueryBuilder('s');

        return $qb->where($qb->expr()->orX(
                $qb->expr()->like('s.prenom', ':value'),
                $qb->expr()->like('s.nom', ':value')
            ))

            ->setParameter('value', "%{$value}%")
            ->orderBy('s.prenom', 'ASC')
            ->orderBy('s.nom', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByTel($tel)
    {
        return $this->createQueryBuilder('e')
        ->where('e.tel LIKE :tel')
        ->setParameter('tel', "%{$tel}%")
        ->orderBy('e.id','ASC')
        ->getQuery()
        ->getResult();
    }

    public function findByDateCreation($date)
    {
        return $this->createQueryBuilder('e')
        ->where('e.date_creation < :date')
        ->setParameter('date', "{$date}")
        ->orderBy('e.id','ASC')
        ->getQuery()
        ->getResult();
    }

    public function findByStatus($isActive)
    {
        return $this->createQueryBuilder('e')
        ->where('e.actif = :isActive')
        ->setParameter('isActive', $isActive)
        ->orderBy('e.id','ASC')
        ->getQuery()
        ->getResult();
    }
}
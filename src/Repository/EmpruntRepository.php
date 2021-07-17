<?php

namespace App\Repository;

use App\Entity\Emprunt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Emprunt|null find($id, $lockMode = null, $lockVersion = null)
 * @method Emprunt|null findOneBy(array $criteria, array $orderBy = null)
 * @method Emprunt[]    findAll()
 * @method Emprunt[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmpruntRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Emprunt::class);
    }

    public function findLastTen()
    {
        return $this->createQueryBuilder('e')
        ->where('e.date_emprunt IS NOT NULL')
        ->orderBy('e.date_emprunt','DESC')
        ->setMaxResults(10)
        ->getQuery()
        ->getResult();
    }

    public function findByLivreId($livreId)
    {
        return $this->createQueryBuilder('e')
        ->innerJoin('e.livre', 'l')
        ->andWhere('l.id LIKE :livreId')
        ->setParameter('livreId', "{$livreId}")
        ->orderBy('e.id','ASC')
        ->getQuery()
        ->getResult();
    }

    public function findByEmprunteurId($emprunteurId)
    {
        return $this->createQueryBuilder('e')
        ->innerJoin('e.emprunteur', 'l')
        ->andWhere('l.id LIKE :emprunteurId')
        ->setParameter('emprunteurId', "{$emprunteurId}")
        ->orderBy('e.id','ASC')
        ->getQuery()
        ->getResult();
    }

    public function findByDateRetour($dateRetour)
    {
        return $this->createQueryBuilder('e')
        ->where('e.date_retour < :dateRetour')
        ->setParameter('dateRetour', "{$dateRetour}")
        ->orderBy('e.id','ASC')
        ->getQuery()
        ->getResult();
    }

    public function findEmpruntsNonRendus()
    {
        return $this->createQueryBuilder('e')
        ->where('e.date_retour IS NULL')
        ->orderBy('e.id','ASC')
        ->getQuery()
        ->getResult();
    }

    public function findOneByLivreIdEtDateRetour($livreId)
    {
        return $this->createQueryBuilder('e')
        ->where('e.livre = :livreId')
        ->setParameter('livreId', "{$livreId}")
        ->andWhere('e.date_retour IS NULL')
        ->orderBy('e.id','ASC')
        ->getQuery()
        ->getResult();
    }

}
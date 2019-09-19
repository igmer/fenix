<?php

namespace App\Repository;

use App\Entity\Cobrador;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Symfony\Bridge\Doctrine\RegistryInterface;

class CobradorRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Cobrador::class);
    }

    public function ContarCobradoresPorUsuario($idUsuario){
        return $this->getEntityManager()
            ->createQuery("
             SELECT cobradores.id
             FROM App\Entity\Cobrador cobradores
             WHERE cobradores.user =:idUsuario
           ")
            ->setParameter('idUsuario', $idUsuario)
            ->getResult();
    }

    public function ObtenerCobradorPorEmail($Email){
        return $this->getEntityManager()
            ->createQuery("
            SELECT cobrador.id
            FROM App\Entity\Cobrador cobrador
            WHERE cobrador.email =:Email
            ")
            ->setParameter('Email',$Email)
            ->getResult();

    }





    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('c')
            ->where('c.something = :value')->setParameter('value', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}

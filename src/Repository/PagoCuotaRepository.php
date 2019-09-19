<?php

namespace App\Repository;

use App\Entity\PagoCuota;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class PagoCuotaRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PagoCuota::class);
    }

    public function BuscarCuotas($arrayPrestamos, $mesActual, $mesFin){
        return $this->getEntityManager()
            ->createQuery('
                SELECT cuotas
                FROM App\Entity\PagoCuota cuotas
                where cuotas.prestamo IN (:arrayPrestamos) AND cuotas.estado =:estado  AND cuotas.fecha_pago BETWEEN (:fechainicial) and (:fechaFinal) OR cuotas.estado =:mora
                ORDER BY cuotas.fecha_pago ASC 
            ')
            ->setParameter('arrayPrestamos', $arrayPrestamos)
            ->setParameter('estado', 'Activo')
            ->setParameter('mora', 'En mora')
            ->setParameter('fechainicial', $mesActual.'-01')
            ->setParameter('fechaFinal', $mesFin.'-01')
            ->getResult();
    }
}

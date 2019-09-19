<?php

namespace App\Repository;

use App\Entity\Prestamo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class PrestamoRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Prestamo::class);
    }

    public function BuscarPrestamosPorUsuario($arrayCliente){
        return $this->getEntityManager()
            ->createQuery('
                SELECT prestamos 
                FROM App\Entity\Prestamo prestamos
                where prestamos.cliente IN (:arrayClientes)
            ')
            ->setParameter('arrayClientes', $arrayCliente)
            ->getResult();
    }




    public function BuscarPrestamosPendientesPorUsuario($arrayCliente){
        return $this->getEntityManager()
            ->createQuery('
                SELECT prestamos 
                FROM App\Entity\Prestamo prestamos
                where prestamos.cliente IN (:arrayClientes) AND prestamos.estado=:estado
            ')
            ->setParameter('arrayClientes', $arrayCliente)
            ->setParameter('estado', 'Pendiente')
            ->getResult();
    }


    public function BuscarPrestamosCobradorLogueado($cobrador){
        return $this->getEntityManager()
            ->createQuery('
                SELECT prestamos
                FROM App\Entity\Prestamo prestamos
                WHERE prestamos.cobrador IN(:cobrador) 
            ')
            ->setParameter('cobrador', $cobrador)
            ->getResult();
    }

    public function BuscarPrestamosCobradores($ArrayCobradores){
        return $this->getEntityManager()
            ->createQuery('
            SELECT prestamos
            FROM App\Entity\Prestamo prestamos
            WHERE prestamos.cobrador IN(:cobradores)
            ')
            ->setParameter('cobradores',$ArrayCobradores)
            ->getResult();
    }



}

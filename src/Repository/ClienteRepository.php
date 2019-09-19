<?php

namespace App\Repository;

use App\Entity\Cliente;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ClienteRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Cliente::class);
    }

    public function BuscarPorNombre($entrada){
        return $this->getEntityManager()
            ->createQuery(
                '
                    SELECT clientes
                    FROM App\Entity\Cliente clientes
                    WHERE clientes.nombre LIKE :entrada 
                ')
            ->setParameter('entrada','%'.$entrada.'%')
            ->getResult();
    }
    //funcion de arriba original
//    public function BuscarPorNombre($entrada, $idUsuario){
//        return $this->getEntityManager()
//            ->createQuery(
//                '
//                    SELECT clientes
//                    FROM App\Entity\Cliente clientes
//                    WHERE clientes.nombre LIKE :entrada AND clientes.user =:idUsuario
//                ')
//            ->setParameter('entrada','%'.$entrada.'%')
//            ->setParameter('idUsuario',$idUsuario)
//            ->getResult();
//    }


    public function BuscarPorId($entrada){
        return $this->getEntityManager()
            ->createQuery(
                '
                    SELECT clientes
                    FROM App\Entity\Cliente clientes
                    WHERE clientes.cedula LIKE :entrada
                ')
            ->setParameter('entrada','%'.$entrada.'%')
            ->getResult();
    }

//    public function BuscarPorId($entrada, $idUsuario){
//        return $this->getEntityManager()
//            ->createQuery(
//                '
//                    SELECT clientes
//                    FROM App\Entity\Cliente clientes
//                    WHERE clientes.cedula LIKE :entrada AND clientes.user =:idUsuario
//                ')
//            ->setParameter('entrada','%'.$entrada.'%')
//            ->setParameter('idUsuario',$idUsuario)
//            ->getResult();
//    }




   public function BuscarClientesPorUsuario($idUsuario){
        return $this->getEntityManager()
            ->createQuery('
            SELECT clientes
            FROM App\Entity\Cliente clientes
            WHERE clientes.user =:idUsuario
            ')
            ->setParameter('idUsuario', $idUsuario);
   }

   public function ContarClientesPorUsuario($idUsuario){
       return $this->getEntityManager()
           ->createQuery("
             SELECT clientes.id
             FROM App\Entity\Cliente clientes
             WHERE clientes.user =:idUsuario
           ")
           ->setParameter('idUsuario', $idUsuario)
           ->getResult();
   }
}

<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PagoCuotaRepository")
 */
class PagoCuota
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\Column(type="integer")
     */
    private $saldo;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fecha_pago;

    /**
     * @ORM\Column(type="string")
     */
    private $estado;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Prestamo", inversedBy="pago_cuota", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $prestamo;

    /**
     * @ORM\Column(type="datetime")
     */
    private $FechaCancelacion;


    /**
     * PagoCuota constructor.
     * @param $saldo
     * @param $fecha_pago
     * @param $estado
     * @param $prestamo
     */
    public function __construct($saldo, $fecha_pago, $estado, $prestamo)
    {
        $this->saldo = $saldo;
        $this->fecha_pago = $fecha_pago;
        $this->estado = $estado;
        $this->prestamo = $prestamo;
        $this->FechaCancelacion = new \DateTime();
    }


    /**
     * @param mixed $saldo
     */
    public function setSaldo($saldo)
    {
        $this->saldo = $saldo;
    }

    /**
     * @return mixed
     */
    public function getSaldo()
    {
        return $this->saldo;
    }

    /**
     * @return mixed
     */
    public function getFechaPago()
    {
        return $this->fecha_pago;
    }


    /**
     * @param mixed $fecha_pago
     */
    public function setFechaPago($fecha_pago)
    {
        $this->fecha_pago = $fecha_pago;
    }

    /**
     * @return mixed
     */
    public function getPrestamo()
    {
        return $this->prestamo;
    }

    /**
     * @param mixed $prestamo
     */
    public function setPrestamo($prestamo)
    {
        $this->prestamo = $prestamo;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * @param mixed $estado
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    public function AplicarMora(){
        $interes = $this->saldo*0.05;
        $this->saldo = $this->saldo + ($interes);
        $totalPrestamo = (int)($this->prestamo->getTotal());
        $totalPrestamo += $interes;
        $this->prestamo->setTotal($totalPrestamo);
    }

    /**
     * @return mixed
     */
    public function getFechaCancelacion()
    {
        return $this->FechaCancelacion;
    }

    /**
     * @param mixed $FechaCancelacion
     */
    public function setFechaCancelacion($FechaCancelacion)
    {
        $this->FechaCancelacion = $FechaCancelacion;
    }









}

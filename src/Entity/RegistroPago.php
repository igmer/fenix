<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RegistroPagoRepository")
 */
class RegistroPago
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fechaPago;

    /**
     * @ORM\Column(type="integer")
     */
    private $cobradorId;

    /**
     * @ORM\Column(type="integer")
     */
    private $cuotaId;

    /**
     * @ORM\Column(type="integer")
     */
    private $valorCuota;

    /**
     * @ORM\Column(type="string")
     */
    private $accion;

    /**
     * RegistroPago constructor.
     * @param $fechaPago
     * @param $cobradorId
     * @param $cuotaId
     * @param $valorCuota
     */
    public function __construct($fechaPago, $cobradorId, $cuotaId, $valorCuota)
    {
        $this->fechaPago = $fechaPago;
        $this->cobradorId = $cobradorId;
        $this->cuotaId = $cuotaId;
        $this->valorCuota = $valorCuota;
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getFechaPago()
    {
        return $this->fechaPago;
    }

    /**
     * @param mixed $fechaPago
     */
    public function setFechaPago($fechaPago)
    {
        $this->fechaPago = $fechaPago;
    }

    /**
     * @return mixed
     */
    public function getCobradorId()
    {
        return $this->cobradorId;
    }

    /**
     * @param mixed $cobradorId
     */
    public function setCobradorId($cobradorId)
    {
        $this->cobradorId = $cobradorId;
    }

    /**
     * @return mixed
     */
    public function getCuotaId()
    {
        return $this->cuotaId;
    }

    /**
     * @param mixed $cuotaId
     */
    public function setCuotaId($cuotaId)
    {
        $this->cuotaId = $cuotaId;
    }

    /**
     * @return mixed
     */
    public function getValorCuota()
    {
        return $this->valorCuota;
    }

    /**
     * @param mixed $valorCuota
     */
    public function setValorCuota($valorCuota)
    {
        $this->valorCuota = $valorCuota;
    }

    /**
     * @return mixed
     */
    public function getAccion()
    {
        return $this->accion;
    }

    /**
     * @param mixed $accion
     */
    public function setAccion($accion)
    {
        $this->accion = $accion;
    }







}

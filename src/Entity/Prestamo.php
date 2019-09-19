<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PrestamoRepository")
 */
class Prestamo
{
    const PENDIENTE = "Pendiente";
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $estado;

    /**
     * @ORM\column(type="integer")
     */
    private $valor_prestamo;

    /**
     * @ORM\Column(type="string")
     */
    private $modo_pago;


    /**
     * @ORM\Column(type="integer")
     */
    private $tasa_interes;


    /**
     * @ORM\Column(type="integer")
     */
    private $dias_pago;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Cliente", inversedBy="prestamos")
     * @ORM\JoinColumn(nullable=true)
     */
    private $cliente;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ruta", inversedBy="prestamo")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $ruta;


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PagoCuota", mappedBy="prestamo")
     */
    private $pago_cuota;


    /**
     * @ORM\Column(type="string")
     */
    private $url;


    /**
     * @ORM\Column(type="integer")
     */
    private $pagado;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fecha_prestamo;

    /**
     * @ORM\Column(type="integer")
     */
    private $total_cuotas;

    /**
     * @ORM\Column(type="string")
     */
    private $precio_cuota;

    /**
     * @ORM\Column(type="string")
     */
    private $total;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Cobrador", inversedBy="prestamo")
     * @ORM\JoinColumn(nullable=true)
     */
    private $cobrador;

    /**
     * @ORM\Column(type="integer")
     * @ORM\JoinColumn(nullable=true)
     */
    private $alive;


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

    /**
     * @return mixed
     */
    public function getValorPrestamo()
    {
        return $this->valor_prestamo;
    }

    /**
     * @param mixed $valor_prestamo
     */
    public function setValorPrestamo($valor_prestamo)
    {
        $this->valor_prestamo = $valor_prestamo;
    }

    /**
     * @return mixed
     */
    public function getModoPago()
    {
        return $this->modo_pago;
    }

    /**
     * @param mixed $modo_pago
     */
    public function setModoPago($modo_pago)
    {
        $this->modo_pago = $modo_pago;
    }

    /**
     * @return mixed
     */
    public function getTasaInteres()
    {
        return $this->tasa_interes;
    }

    /**
     * @param mixed $tasa_interes
     */
    public function setTasaInteres($tasa_interes)
    {
        $this->tasa_interes = $tasa_interes;
    }

    /**
     * @return mixed
     */
    public function getDiasPago()
    {
        return $this->dias_pago;
    }

    /**
     * @param mixed $dias_pago
     */
    public function setDiasPago($dias_pago)
    {
        $this->dias_pago = $dias_pago;
    }

    /**
     * @return mixed
     */
    public function getCliente()
    {
        return $this->cliente;
    }

    /**
     * @param mixed $cliente
     */
    public function setCliente($cliente)
    {
        $this->cliente = $cliente;
    }


    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $clave_md5= md5($url);
        $this->url = $clave_md5;
    }



    /**
     * @return mixed
     */
    public function getPagado()
    {
        return $this->pagado;
    }

    /**
     * @param mixed $pagado
     */
    public function setPagado($pagado)
    {
        $this->pagado = $pagado;
    }

    /**
     * @return mixed
     */
    public function getFechaPrestamo()
    {
        return $this->fecha_prestamo;
    }

    /**
     * @param mixed $fecha_prestamo
     */
    public function setFechaPrestamo($fecha_prestamo)
    {
        $this->fecha_prestamo = $fecha_prestamo;
    }

    /**
     * @return mixed
     */
    public function getTotalCuotas()
    {
        return $this->total_cuotas;
    }

    /**
     * @param mixed $total_cuotas
     */
    public function setTotalCuotas($total_cuotas)
    {
        $this->total_cuotas = $total_cuotas;
    }

    /**
     * @return mixed
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param mixed $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }

    /**
     * @return mixed
     */
    public function getPrecioCuota()
    {
        return $this->precio_cuota;
    }

    /**
     * @param mixed $precio_cuota
     */
    public function setPrecioCuota($precio_cuota)
    {
        $this->precio_cuota = $precio_cuota;
    }

    /**
     * @return mixed
     */
    public function getRuta()
    {
        return $this->ruta;
    }

    /**
     * @param mixed $ruta
     */
    public function setRuta($ruta)
    {
        $this->ruta = $ruta;
    }

    /**
     * @return mixed
     */
    public function getPagoCuota()
    {
        return $this->pago_cuota;
    }

    /**
     * @param mixed $pago_cuota
     */
    public function setPagoCuota($pago_cuota)
    {
        $this->pago_cuota = $pago_cuota;
    }

    /**
     * @return mixed
     */
    public function getCobrador()
    {
        return $this->cobrador;
    }

    /**
     * @param mixed $cobrador
     */
    public function setCobrador($cobrador)
    {
        $this->cobrador = $cobrador;
    }

    /**
     * @return mixed
     */
    public function getAlive()
    {
        return $this->alive;
    }

    /**
     * @param mixed $alive
     */
    public function setAlive($alive)
    {
        $this->alive = $alive;
    }









}

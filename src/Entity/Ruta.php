<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RutaRepository")
 */
class Ruta
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $zona_cobro;

    /**
     * @ORM\Column(type="string")
     */
    private $nombre_socio;

    /**
     * @ORM\Column(type="integer")
     */
    private $cuota;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="ruta")
     * @ORM\JoinColumn(nullable=true)
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Prestamo", mappedBy="ruta")
     * @ORM\JoinColumn(nullable=true)
     *
     */
    private $prestamo;

    /**
     * Ruta constructor.
     */
    public function __construct()
    {
        $this->user = new ArrayCollection();
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
    public function getZonaCobro()
    {
        return $this->zona_cobro;
    }

    /**
     * @param mixed $zona_cobro
     */
    public function setZonaCobro($zona_cobro)
    {
        $this->zona_cobro = $zona_cobro;
    }

    /**
     * @return mixed
     */
    public function getNombreSocio()
    {
        return $this->nombre_socio;
    }

    /**
     * @param mixed $nombre_socio
     */
    public function setNombreSocio($nombre_socio)
    {
        $this->nombre_socio = $nombre_socio;
    }

    /**
     * @return mixed
     */
    public function getCuota()
    {
        return $this->cuota;
    }

    /**
     * @param mixed $cuota
     */
    public function setCuota($cuota)
    {
        $this->cuota = $cuota;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }






}

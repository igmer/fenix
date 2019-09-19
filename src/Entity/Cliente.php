<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ClienteRepository")
 */
class Cliente
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;


    /**
     * @ORM\Column(type="bigint")
     */
    private $cedula;


    /**
     * @ORM\Column(type="string")
     */
    private $nombre;


    /**
     * @ORM\Column(type="text")
     */
    private $nota;

    /**
     * @param mixed $id
     */

    /**
     * @ORM\Column(type="string")
     */

    private $direccion;


    /**
     * @ORM\Column(type="string")
     */
    private $barrio;


    /**
     * @ORM\Column(type="string")
     */
    private $fijo;


    /**
     * @ORM\Column(type="string")
     */
    private $celular;

    /**
     * @return mixed
     */


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Prestamo", mappedBy="cliente")
     */
    private $prestamos;

    /**
     * @ORM\Column(type="string")
     */
    private $ruta;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="cliente")
     * @ORM\JoinColumn(nullable=true)
     */
    private $user;


    public function __construct()
    {
        $this->prestamos = new ArrayCollection();
    }


    public function getId()
    {
        return $this->id;
    }


    /**
     * @return mixed
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * @param mixed $nombre
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    /**
     * @return mixed
     */
    public function getNota()
    {
        return $this->nota;
    }

    /**
     * @param mixed $nota
     */
    public function setNota($nota)
    {
        $this->nota = $nota;
    }

    /**
     * @return mixed
     */
    public function getDireccion()
    {
        return $this->direccion;
    }

    /**
     * @param mixed $direccion
     */
    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;
    }

    /**
     * @return mixed
     */
    public function getBarrio()
    {
        return $this->barrio;
    }

    /**
     * @param mixed $barrio
     */
    public function setBarrio($barrio)
    {
        $this->barrio = $barrio;
    }

    /**
     * @return mixed
     */
    public function getFijo()
    {
        return $this->fijo;
    }

    /**
     * @param mixed $fijo
     */
    public function setFijo($fijo)
    {
        $this->fijo = $fijo;
    }

    /**
     * @return mixed
     */
    public function getCelular()
    {
        return $this->celular;
    }

    /**
     * @param mixed $celular
     */
    public function setCelular($celular)
    {
        $this->celular = $celular;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param Prestamo $prestamo
     * @return $this
     */
    public function addPrestamos(Prestamo $prestamo)
    {
        $this->prestamos[] = $prestamo;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPrestamos()
    {
        return $this->prestamos;
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
        $clave_md5= md5($ruta);
        $this->ruta = $clave_md5;
    }

    /**
     * @return mixed
     */
    public function getCedula()
    {
        return $this->cedula;
    }

    /**
     * @param mixed $cedula
     */
    public function setCedula($cedula)
    {
        $this->cedula = $cedula;
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

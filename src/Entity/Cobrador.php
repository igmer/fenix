<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CobradorRepository")
 */
class Cobrador
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
     * @ORM\Column(type="string")
     */
    private $email;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="cobrador")
     * @ORM\JoinColumn(nullable=true)
     */
    private $user;


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Prestamo", mappedBy="cobrador")
     * @ORM\JoinColumn(nullable=true)
     *
     */
    private $prestamo;

    /**
     * @ORM\Column(type="string")
     */
    private $ruta;

    /**
     * Cobrador constructor.
     */
    public function __construct()
    {
        $this->prestamo= new ArrayCollection();
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
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }








}

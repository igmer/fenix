<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User extends BaseUser
{
    const USUARIOADMIN = 0;
    const USUARIO = 1;
    const USUARIOCOBRADOR= 2;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Ruta", mappedBy="user")
     */
    private $ruta;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Cobrador", mappedBy="user")
     */
    private $cobrador;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Cobrador", mappedBy="user")
     */
    private $cliente;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $userEstadp=null;


    public function __construct()
    {
        parent::__construct();
        $this->ruta= new ArrayCollection();
        $this->cobrador = new ArrayCollection();
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
    public function getUserEstadp()
    {
        return $this->userEstadp;
    }

    /**
     * @param mixed $userEstadp
     */
    public function setUserEstadp($userEstadp)
    {
        $this->userEstadp = $userEstadp;
    }











}
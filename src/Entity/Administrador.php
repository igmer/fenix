<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AdministradorRepository")
 */
class Administrador
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
    private $NombreUsuario;

    /**
     * @ORM\Column(type="string")
     */
    private $email;

    /**
     * @ORM\Column(type="string")
     */
    private $Contrasena;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="administrador")
     * @ORM\JoinColumn(nullable=true)
     */
    private $usuario;

    /**
     * @return mixed
     */
    public function getNombreUsuario()
    {
        return $this->NombreUsuario;
    }

    /**
     * @param mixed $NombreUsuario
     */
    public function setNombreUsuario($NombreUsuario)
    {
        $this->NombreUsuario = $NombreUsuario;
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
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * @param mixed $usuario
     */
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;
    }

    /**
     * @return mixed
     */
    public function getContrasena()
    {
        return $this->Contrasena;
    }

    /**
     * @param mixed $Contrasena
     */
    public function setContrasena($Contrasena)
    {
        $this->Contrasena = $Contrasena;
    }






}

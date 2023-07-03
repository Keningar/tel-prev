<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoTelconetUsersAAAA
 *
 * @ORM\Table(name="users")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoTelconetUsersAAAARepository")
 */
class InfoTelconetUsersAAAA
{

    /**
     * @var string login
     *
     * @ORM\Column(name = "login", type="string", nullable = false)
     * @ORM\Id
     */
    private $login;

    /**
     * @var string $nombre
     *
     * @ORM\Column(name="nombre", type="string", nullable=false)
     */
    private $nombre;

    /**
     * @var string $ciudad
     *
     * @ORM\Column(name="ciudad", type="string", nullable=false)
     */
    private $ciudad;

    /**
     * @var string $departamento
     *
     * @ORM\Column(name="departamento", type="string", nullable=false)
     */
    private $departamento;

    /**
     * @var integer $perfil
     *
     * @ORM\Column(name="Perfil", type="integer")
     */
    private $perfil;

    /**
     * @var integer $perfilRouters
     *
     * @ORM\Column(name="Perfil_Routers", type="integer")
     */
    private $perfilRouters;

    /**
     * @var string $shell
     *
     * @ORM\Column(name="shell", type="string", nullable=false)
     */
    private $shell;

    /**
     * @var string $password
     *
     * @ORM\Column(name="Password", type="string")
     */
    private $password;

    /**
     * @var string $tipo
     *
     * @ORM\Column(name="Tipo", type="string")
     */
    private $tipo;

    /**
     * @var string $estado
     *
     * @ORM\Column(name="estado", type="string")
     */
    private $estado;

    /**
     * @var integer $idDepartamento
     *
     * @ORM\Column(name="id_departamento", type="integer")
     */
    private $idDepartamento;

    /**
     * Get login
     *
     * @return string
     */
    function getLogin()
    {
        return $this->login;
    }

    /**
     * Set login
     *
     * @param string $login
     */
    function setLogin($login)
    {
        $this->login = $login;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     */
    function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    /**
     * Get ciudad
     *
     * @return string
     */
    function getCiudad()
    {
        return $this->ciudad;
    }

    /**
     * Set ciudad
     *
     * @param string $ciudad
     */
    function setCiudad($ciudad)
    {
        $this->ciudad = $ciudad;
    }

    /**
     * Get departamento
     *
     * @return string
     */
    function getDepartamento()
    {
        return $this->departamento;
    }

    /**
     * Set departamento
     *
     * @param string $departamento
     */
    function setDepartamento($departamento)
    {
        $this->departamento = $departamento;
    }

    /**
     * Get perfil
     *
     * @return integer
     */
    function getPerfil()
    {
        return $this->perfil;
    }

    /**
     * Set perfil
     *
     * @param integer $perfil
     */
    function setPerfil($perfil)
    {
        $this->perfil = $perfil;
    }

    /**
     * Get perfilRouters
     *
     * @return integer
     */
    function getPerfilRouters()
    {
        return $this->perfilRouters;
    }

    /**
     * Set perfilRouters
     *
     * @param integer $perfilRouters
     */
    function setPerfilRouters($perfilRouters)
    {
        $this->perfilRouters = $perfilRouters;
    }

    /**
     * Get shell
     *
     * @return string
     */
    function getShell()
    {
        return $this->shell;
    }

    /**
     * Set shell
     *
     * @param string $shell
     */
    function setShell($shell)
    {
        $this->shell = $shell;
    }

    /**
     * Get password
     *
     * @return resource
     */
    function getPassword()
    {
        return $this->password;
    }

    /**
     * Set password
     *
     * @param string $password
     */
    function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Get tipo
     *
     * @return string
     */
    function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set tipo
     *
     * @param string $tipo
     */
    function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }

    /**
     * Get estado
     *
     * @return string
     */
    function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set estado
     *
     * @param string $estado
     */
    function setEstado($estado)
    {
        $this->estado = $estado;
    }

    /**
     * Get idDepartamento
     *
     * @return integer
     */
    function getIdDepartamento()
    {
        return $this->idDepartamento;
    }

    /**
     * Set idDepartamento
     *
     * @param integer $idDepartamento
     */
    function setIdDepartamento($idDepartamento)
    {
        $this->idDepartamento = $idDepartamento;
    }

}

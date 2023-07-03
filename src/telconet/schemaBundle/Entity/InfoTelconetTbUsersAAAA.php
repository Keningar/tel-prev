<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoTelconetTbUsersAAAA
 *
 * @ORM\Table(name="tb_users")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoTelconetTbUsersAAAARepository")
 */
class InfoTelconetTbUsersAAAA
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
     * @ORM\Column(name="nombre", type="string")
     */
    private $nombre;

    /**
     * @var integer $departamento
     *
     * @ORM\Column(name="departamento", type="integer")
     */
    private $departamento;

    /**
     * @var integer $perfilSw
     *
     * @ORM\Column(name="perfil_sw", type="integer")
     */
    private $perfilSw;

    /**
     * @var integer $perfilRo
     *
     * @ORM\Column(name="perfil_ro", type="integer")
     */
    private $perfilRo;

    /**
     * @var integer $perfilAp
     *
     * @ORM\Column(name="perfil_ap", type="integer")
     */
    private $perfilAp;

    /**
     * @var integer $perfilAc
     *
     * @ORM\Column(name="perfil_ac", type="integer")
     */
    private $perfilAc;

    /**
     * @var integer $perfilSit
     *
     * @ORM\Column(name="perfil_sit", type="integer",nullable = false)
     */
    private $perfilSit;

    /**
     * @var integer $perfilNot
     *
     * @ORM\Column(name="perfil_not", type="integer", nullable = false)
     */
    private $perfilNot;

    /**
     * @var integer $perfilChsw
     *
     * @ORM\Column(name="perfil_chsw", type="integer", nullable = false)
     */
    private $perfilChsw;

    /**
     * @var integer $perfilCmsw
     *
     * @ORM\Column(name="perfil_cmsw", type="integer", nullable = false)
     */
    private $perfilCmsw;

    /**
     * @var integer $perfilIncseg
     *
     * @ORM\Column(name="perfil_incseg", type="integer", nullable = false)
     */
    private $perfilIncseg;

    /**
     * @var integer $perfilApval
     *
     * @ORM\Column(name="perfil_apval", type="integer", nullable = false)
     */
    private $perfilApval;

    /**
     * @var integer $perfil_resvsala
     *
     * @ORM\Column(name="perfil_resvsala", type="integer")
     */
    private $perfilResvsala;

    /**
     * @var integer $perfilMetrovia
     *
     * @ORM\Column(name="perfil_metrovia", type="integer")
     */
    private $perfilMetrovia;

    /**
     * @var integer $perfilTacacsClientes
     *
     * @ORM\Column(name="perfil_tacacs_clientes", type="integer")
     */
    private $perfilTacacsClientes;

    /**
     * @var string $shell
     *
     * @ORM\Column(name="shell", type="string", length = 15)
     */
    private $shell;

    /**
     * @var string $password
     *
     * @ORM\Column(name="`password`", type="string")
     */
    private $password;

    /**
     * @var string $tipo
     *
     * @ORM\Column(name="tipo", type="string", length = 15)
     */
    private $tipo;

    /**
     * @var string $email
     *
     * @ORM\Column(name="email", type="string", length = 200, nullable = false)
     */
    private $email;

    /**
     * @var string $estado
     *
     * @ORM\Column(name="estado", type="string")
     */
    private $estado;

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
     * Get departamento
     *
     * @return integer
     */
    function getDepartamento()
    {
        return $this->departamento;
    }

    /**
     * Set departamento
     *
     * @param integer $departamento
     */
    function setDepartamento($departamento)
    {
        $this->departamento = $departamento;
    }

    /**
     * Get perfilSw
     *
     * @return integer
     */
    function getPerfilSw()
    {
        return $this->perfilSw;
    }

    /**
     * Set perfilSw
     *
     * @param integer $perfilSw
     */
    function setPerfilSw($perfilSw)
    {
        $this->perfilSw = $perfilSw;
    }

    /**
     * Get perfilRo
     *
     * @return integer
     */
    function getPerfilRo()
    {
        return $this->perfilRo;
    }

    /**
     * Set perfilRo
     *
     * @param integer $perfilRo
     */
    function setPerfilRo($perfilRo)
    {
        $this->perfilRo = $perfilRo;
    }

    /**
     * Get perfilAp
     *
     * @return integer
     */
    function getPerfilAp()
    {
        return $this->perfilAp;
    }

    /**
     * Set perfilAp
     *
     * @param integer $perfilAp
     */
    function setPerfilAp($perfilAp)
    {
        $this->perfilAp = $perfilAp;
    }

    /**
     * Get perfilAc
     *
     * @return integer
     */
    function getPerfilAc()
    {
        return $this->perfilAc;
    }

    /**
     * Set perfilAc
     *
     * @param integer $perfilAc
     */
    function setPerfilAc($perfilAc)
    {
        $this->perfilAc = $perfilAc;
    }

    /**
     * Get perfilSit
     *
     * @return integer
     */
    function getPerfilSit()
    {
        return $this->perfilSit;
    }

    /**
     * Set perfilSit
     *
     * @param integer $perfilSit
     */
    function setPerfilSit($perfilSit)
    {
        $this->perfilSit = $perfilSit;
    }

    /**
     * Get perfilNot
     *
     * @return integer
     */
    function getPerfilNot()
    {
        return $this->perfilNot;
    }

    /**
     * Set perfilNot
     *
     * @param integer $perfilNot
     */
    function setPerfilNot($perfilNot)
    {
        $this->perfilNot = $perfilNot;
    }

    /**
     * Get perfilChsw
     *
     * @return integer
     */
    function getPerfilChsw()
    {
        return $this->perfilChsw;
    }

    /**
     * Set perfilChsw
     *
     * @param integer $perfilChsw
     */
    function setPerfilChsw($perfilChsw)
    {
        $this->perfilChsw = $perfilChsw;
    }

    /**
     * Get perfilCmsw
     *
     * @return integer
     */
    function getPerfilCmsw()
    {
        return $this->perfilCmsw;
    }

    /**
     * Set perfilCmsw
     *
     * @param integer $perfilCmsw
     */
    function setPerfilCmsw($perfilCmsw)
    {
        $this->perfilCmsw = $perfilCmsw;
    }

    /**
     * Get perfilIncseg
     *
     * @return integer
     */
    function getPerfilIncseg()
    {
        return $this->perfilIncseg;
    }

    /**
     * Set perfilIncseg
     *
     * @param integer $perfilIncseg
     */
    function setPerfilIncseg($perfilIncseg)
    {
        $this->perfilIncseg = $perfilIncseg;
    }

    /**
     * Get perfilApval
     *
     * @return integer
     */
    function getPerfilApval()
    {
        return $this->perfilApval;
    }

    /**
     * Set perfilApval
     *
     * @param integer $perfilApval
     */
    function setPerfilApval($perfilApval)
    {
        $this->perfilApval = $perfilApval;
    }

    /**
     * Get perfilResvsala
     *
     * @return integer
     */
    function getPerfilResvsala()
    {
        return $this->perfilResvsala;
    }

    /**
     * Set perfilResvsala
     *
     * @param integer $perfilResvsala
     */
    function setPerfilResvsala($perfilResvsala)
    {
        $this->perfilResvsala = $perfilResvsala;
    }

    /**
     * Get perfilMetrovia
     *
     * @return integer
     */
    function getPerfilMetrovia()
    {
        return $this->perfilMetrovia;
    }

    /**
     * Set perfilMetrovia
     *
     * @param integer $perfilMetrovia
     */
    function setPerfilMetrovia($perfilMetrovia)
    {
        $this->perfilMetrovia = $perfilMetrovia;
    }

    /**
     * Get perfilTacacsClientes
     *
     * @return integer
     */
    function getPerfilTacacsClientes()
    {
        return $this->perfilTacacsClientes;
    }

    /**
     * Set perfilTacacsClientes
     *
     * @param integer $perfilTacacsClientes
     */
    function setPerfilTacacsClientes($perfilTacacsClientes)
    {
        $this->perfilTacacsClientes = $perfilTacacsClientes;
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
     * Get email
     *
     * @return string
     */
    function getEmail()
    {
        return $this->email;
    }

    /**
     * Set email
     *
     * @param string $email
     */
    function setEmail($email)
    {
        $this->email = $email;
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

}

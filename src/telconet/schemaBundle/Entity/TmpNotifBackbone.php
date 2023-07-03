<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\TmpNotifBackbone
 *
 * @ORM\Table(name="TEMP_NOTIF_BACKBONE")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\TmpNotifBackboneRepository")
 */
class TmpNotifBackbone
{

    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID_TEMP", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_TEMP_NOTIF_BACKBONE", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string $casoId
     *
     * @ORM\Column(name="CASO_ID", type="integer", nullable=false)
     */
    private $casoId;

    /**
     * @var string $numeroCaso
     *
     * @ORM\Column(name="NUMERO_CASO", type="string", nullable=false)
     */
    private $numeroCaso;

    /**
     * @var string $cadenaLogin
     *
     * @ORM\Column(name="CADENA_LOGIN", type="string", nullable=false)
     */
    private $cadenaLogin;

    /**
     * @var string $cadenaCorreo
     *
     * @ORM\Column(name="CADENA_CORREO", type="string", nullable=false)
     */
    private $cadenaCorreo;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     *
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get casoId
     *
     * @return integer
     */
    public function getCasoId()
    {
        return $this->casoId;
    }

    /**
     * Set casoId
     *
     * @param integer $casoId
     */
    public function setCasoId($casoId)
    {
        $this->casoId = $casoId;
    }

    /**
     * Get numeroCaso
     *
     * @return string
     */
    public function getNumeroCaso()
    {
        return $this->numeroCaso;
    }

    /**
     * Set numeroCaso
     *
     * @param string $numeroCaso
     */
    public function setNumeroCaso($numeroCaso)
    {
        $this->numeroCaso = $numeroCaso;
    }

    /**
     * Get cadenaLogin
     *
     * @return string
     */
    public function getCadenaLogin()
    {
        return $this->cadenaLogin;
    }

    /**
     * Set cadenaLogin
     *
     * @param string $cadenaLogin
     */
    public function setCadenaLogin($cadenaLogin)
    {
        $this->cadenaLogin = $cadenaLogin;
    }

    /**
     * Get cadenaCorreo
     *
     * @return string
     */
    public function getCadenaCorreo()
    {
        return $this->cadenaCorreo;
    }

    /**
     * Set cadenaCorreo
     *
     * @param string $cadenaCorreo
     */
    public function setCadenaCorreo($cadenaCorreo)
    {
        $this->cadenaCorreo = $cadenaCorreo;
    }

}

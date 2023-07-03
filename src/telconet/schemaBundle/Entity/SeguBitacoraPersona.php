<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\SeguBitacoraPersona
 *
 * @ORM\Table(name="SEGU_BITACORA_PERSONA")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\SeguBitacoraPersonaRepository")
 */
class SeguBitacoraPersona
{

    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID_BITACORA_PERSONA", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_SEGU_BITACORA_PERSONA", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var integer $personaId
     *
     * @ORM\Column(name="PERSONA_ID", type="integer", nullable=false)
     */
    private $personaId;

    /**
     * @var integer $relacionSistemaId
     *
     * @ORM\Column(name="RELACION_SISTEMA_ID", type="integer", nullable=false)
     */
    private $relacionSistemaId;

    /**
     * @var string $bitacoraDetalle
     *
     * @ORM\Column(name="BITACORA_DETALLE", type="string", nullable=false)
     */
    private $bitacoraDetalle;

    /**
     * @var string $usrCreacion
     *
     * @ORM\Column(name="USR_CREACION", type="string", nullable=false)
     */
    private $usrCreacion;

    /**
     * @var datetime $feCreacion
     *
     * @ORM\Column(name="FE_CREACION", type="datetime", nullable=false)
     */
    private $feCreacion;

    /**
     * @var string $ipCreacion
     *
     * @ORM\Column(name="IP_CREACION", type="string", nullable=false)
     */
    private $ipCreacion;

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
     * Get relacionSistemaId
     *
     * @return integer
     */
    public function getRelacionSistemaId()
    {
        return $this->relacionSistemaId;
    }

    /**
     * Set relacionSistemaId
     *
     * @param integer $relacionSistemaId
     */
    public function setRelacionSistemaId($relacionSistemaId)
    {
        $this->relacionSistemaId = $relacionSistemaId;
    }

    /**
     * Get usrCreacion
     *
     * @return string
     */
    public function getUsrCreacion()
    {
        return $this->usrCreacion;
    }

    /**
     * Set usrCreacion
     *
     * @param string $usrCreacion
     */
    public function setUsrCreacion($usrCreacion)
    {
        $this->usrCreacion = $usrCreacion;
    }

    /**
     * Get feCreacion
     *
     * @return datetime
     */
    public function getFeCreacion()
    {
        return $this->feCreacion;
    }

    /**
     * Set feCreacion
     *
     * @param datetime $feCreacion
     */
    public function setFeCreacion($feCreacion)
    {
        $this->feCreacion = $feCreacion;
    }

    /**
     * Get ipCreacion
     *
     * @return string
     */
    public function getIpCreacion()
    {
        return $this->ipCreacion;
    }

    /**
     * Set ipCreacion
     *
     * @param string $ipCreacion
     */
    public function setIpCreacion($ipCreacion)
    {
        $this->ipCreacion = $ipCreacion;
    }

    /**
     * Get personaId
     *
     * @return integer
     */
    public function getPersonaId()
    {
        return $this->personaId;
    }

    /**
     * Set personaId
     *
     * @param integer $personaId
     */
    public function setPersonaId($personaId)
    {
        $this->personaId = $personaId;
    }

    /**
     * Get bitacoraDetalle
     *
     * @return string
     */
    public function getBitacoraDetalle()
    {
        return $this->bitacoraDetalle;
    }

    /**
     * Set bitacoraDetalle
     *
     * @param string $bitacoraDetalle
     */
    public function setBitacoraDetalle($bitacoraDetalle)
    {
        $this->bitacoraDetalle = $bitacoraDetalle;
    }
    
}

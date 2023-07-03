<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoElementoContrasena
 *
 * @ORM\Table(name="INFO_ELEMENTO_CONTRASENA")
 * @ORM\Entity 
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoElementoContrasenaRepository")
 */
class InfoElementoContrasena
{

    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID_ELEMENTO_CONTRASENA", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_ELEMENTO_CONTRASENA", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var $elementoId
     *
     * @ORM\ManyToOne(targetEntity="InfoElemento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ELEMENTO_ID", referencedColumnName="ID_ELEMENTO")
     * })
     */
    private $elementoId;
    
    /**
     * @var $usuarioId
     *
     * @ORM\ManyToOne(targetEntity="AdmiUsuarioAcceso")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="USUARIO_ID", referencedColumnName="ID_USUARIO_ACCESO")
     * })
     */
    private $usuarioId;

    /**
     * @var string $contrasena
     *
     * @ORM\Column(name="CONTRASENA", type="string", nullable=true)
     * })
     */
    private $contrasena;

    /**
     * @var string $estado
     *
     * @ORM\Column(name="ESTADO", type="string", nullable=false)
     */
    private $estado;

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
     * @var string $usrCreacion
     *
     * @ORM\Column(name="USR_CREACION", type="string", nullable=false)
     */
    private $usrCreacion;

    /**
     * @var datetime $feVigencia
     *
     * @ORM\Column(name="FE_VIGENCIA", type="datetime", nullable=true)
     */
    private $feVigencia;

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
     * Set estado
     *
     * @param string $estado
     * @return InfoComunicacion
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado
     *
     * @return string 
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set feCreacion
     *
     * @param \DateTime $feCreacion
     * @return InfoComunicacion
     */
    public function setFeCreacion($feCreacion)
    {
        $this->feCreacion = $feCreacion;

        return $this;
    }

    /**
     * Get feCreacion
     *
     * @return \DateTime 
     */
    public function getFeCreacion()
    {
        return $this->feCreacion;
    }

    /**
     * Set ipCreacion
     *
     * @param string $ipCreacion
     * @return InfoComunicacion
     */
    public function setIpCreacion($ipCreacion)
    {
        $this->ipCreacion = $ipCreacion;

        return $this;
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
     * Set usrCreacion
     *
     * @param string $usrCreacion
     */
    public function setUsrCreacion($usrCreacion)
    {
        $this->usrCreacion = $usrCreacion;

        return $this;
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
     * Set elementoId
     *
     * @param \telconet\schemaBundle\Entity\InfoElemento $elementoId
     * @return InfoElemento
     */
    public function setElementoId(\telconet\schemaBundle\Entity\InfoElemento $elementoId = null)
    {
        $this->elementoId = $elementoId;

        return $this;
    }

    /**
     * Get elementoId
     *
     * @return \telconet\schemaBundle\Entity\InfoElemento
     */
    public function getElementoId()
    {
        return $this->elementoId;
    }
    
    /**
     * Set usuarioId
     *
     * @param \telconet\schemaBundle\Entity\AdmiUsuarioAcceso $usuarioId
     * @return AdmiUsuarioAcceso
     */
    public function setUsuarioId(\telconet\schemaBundle\Entity\AdmiUsuarioAcceso $usuarioId = null)
    {
        $this->usuarioId = $usuarioId;

        return $this;
    }

    /**
     * Get usuarioId
     *
     * @return \telconet\schemaBundle\Entity\AdmiUsuarioAcceso
     */
    public function getUsuarioId()
    {
        return $this->usuarioId;
    }

    /**
     * Get contrasena
     *
     * @return string 
     */
    public function getContrasena()
    {
        return $this->contrasena;
    }

    /**
     * Set contrasena
     *
     * @param string $contrasena     
     */
    public function setContrasena($contrasena)
    {
        $this->contrasena = $contrasena;

        return $this;
    }

    /**
     * Set feVigencia
     *
     * @param \DateTime $feVigencia
     */
    public function setFeVigencia($feVigencia)
    {
        $this->feVigencia = $feVigencia;

        return $this;
    }

    /**
     * Get feVigencia
     *
     * @return \DateTime 
     */
    public function getFeVigencia()
    {
        return $this->feVigencia;
    }

}

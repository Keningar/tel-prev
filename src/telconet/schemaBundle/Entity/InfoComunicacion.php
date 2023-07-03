<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoComunicacion
 *
 * @ORM\Table(name="INFO_COMUNICACION")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoComunicacionRepository")
 */
class InfoComunicacion
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_COMUNICACION", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_COMUNICACION", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var integer $formaContactoId
*
* @ORM\Column(name="FORMA_CONTACTO_ID", type="integer", nullable=true)
* })
*/
		
private $formaContactoId;

/**
* @var $tramiteId
*
* @ORM\ManyToOne(targetEntity="InfoTramite")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="TRAMITE_ID", referencedColumnName="ID_TRAMITE")
* })
*/
		
private $tramiteId;

/**
* @var string $casoId
*
* @ORM\Column(name="CASO_ID", type="integer", nullable=true)
*/		
     		
public $casoId;

/**
* @var string $detalleId
*
* @ORM\Column(name="DETALLE_ID", type="integer", nullable=true)
*/		
     		
private $detalleId;

/**
* @var string $remitenteId
*
* @ORM\Column(name="REMITENTE_ID", type="integer", nullable=true)
*/		
     		
private $remitenteId;

/**
* @var string remitenteNombre
*
* @ORM\Column(name="REMITENTE_NOMBRE", type="string", nullable=true)
*/		
     		
private $remitenteNombre;

/**
* @var string $claseComunicacion
*
* @ORM\Column(name="CLASE_COMUNICACION", type="string", nullable=true)
*/		
     		
private $claseComunicacion;

/**
* @var string $fechaComunicacion
*
* @ORM\Column(name="FECHA_COMUNICACION", type="datetime", nullable=true)
*/		
     		
private $fechaComunicacion;

/**
* @var string $descripcionComunicacion
*
* @ORM\Column(name="DESCRIPCION_COMUNICACION", type="string", nullable=true)
*/		
     		
private $descripcionComunicacion;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=false)
*/		
     		
private $estado;

/**
* @var string $usrCreacion
*
* @ORM\Column(name="USR_CREACION", type="string", nullable=false)
*/		
     		
private $usrCreacion;

/**
* @var datetime $feCreacion
*
* @ORM\Column(name="FE_CREACION", type="datetime", nullable=true)
*/		
     		
private $feCreacion;

/**
* @var string $ipCreacion
*
* @ORM\Column(name="IP_CREACION", type="string", nullable=false)
*/		
     		
private $ipCreacion;

/**
* @var string $puntoId
*
* @ORM\Column(name="PUNTO_ID", type="integer", nullable=true)
*/		
     		
private $puntoId;

/**
* @var string $empresaCod
*
* @ORM\Column(name="EMPRESA_COD", type="string", nullable=false)
*/		
     		
private $empresaCod;


/**
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}


    /**
     * Set casoId
     *
     * @param integer $casoId
     * @return InfoComunicacion
     */
    public function setCasoId($casoId)
    {
        $this->casoId = $casoId;
    
        return $this;
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
     * Set detalleId
     *
     * @param integer $detalleId
     * @return InfoComunicacion
     */
    public function setDetalleId($detalleId)
    {
        $this->detalleId = $detalleId;
    
        return $this;
    }

    /**
     * Get detalleId
     *
     * @return integer 
     */
    public function getDetalleId()
    {
        return $this->detalleId;
    }

    /**
     * Set remitenteId
     *
     * @param integer $remitenteId
     * @return InfoComunicacion
     */
    public function setRemitenteId($remitenteId)
    {
        $this->remitenteId = $remitenteId;
    
        return $this;
    }

    /**
     * Get remitenteId
     *
     * @return integer 
     */
    public function getRemitenteId()
    {
        return $this->remitenteId;
    }

    /**
     * Set remitenteNombre
     *
     * @param string $remitenteNombre
     * @return InfoComunicacion
     */
    public function setRemitenteNombre($remitenteNombre)
    {
        $this->remitenteNombre = $remitenteNombre;
    
        return $this;
    }

    /**
     * Get remitenteNombre
     *
     * @return string 
     */
    public function getRemitenteNombre()
    {
        return $this->remitenteNombre;
    }

    /**
     * Set claseComunicacion
     *
     * @param string $claseComunicacion
     * @return InfoComunicacion
     */
    public function setClaseComunicacion($claseComunicacion)
    {
        $this->claseComunicacion = $claseComunicacion;
    
        return $this;
    }

    /**
     * Get claseComunicacion
     *
     * @return string 
     */
    public function getClaseComunicacion()
    {
        return $this->claseComunicacion;
    }

    /**
     * Set fechaComunicacion
     *
     * @param \DateTime $fechaComunicacion
     * @return InfoComunicacion
     */
    public function setFechaComunicacion(\DateTime $fechaComunicacion)
    {
        $this->fechaComunicacion = $fechaComunicacion;
    
        return $this;
    }

    /**
     * Get fechaComunicacion
     *
     * @return \DateTime 
     */
    public function getFechaComunicacion()
    {
        return $this->fechaComunicacion;
    }

    /**
     * Set descripcionComunicacion
     *
     * @param string $descripcionComunicacion
     * @return InfoComunicacion
     */
    public function setDescripcionComunicacion($descripcionComunicacion)
    {
        $this->descripcionComunicacion = $descripcionComunicacion;
    
        return $this;
    }

    /**
     * Get descripcionComunicacion
     *
     * @return string 
     */
    public function getDescripcionComunicacion()
    {
        return $this->descripcionComunicacion;
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
     * Set usrCreacion
     *
     * @param string $usrCreacion
     * @return InfoComunicacion
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
     * Set formaContactoId
     *
     * @param integer $formaContactoId
     * @return InfoComunicacion
     */
    public function setFormaContactoId($formaContactoId = null)
    {
        $this->formaContactoId = $formaContactoId;
    
        return $this;
    }

    /**
     * Get formaContactoId
     *
     * @return integer
     */
    public function getFormaContactoId()
    {
        return $this->formaContactoId;
    }

    /**
     * Set tramiteId
     *
     * @param \telconet\schemaBundle\Entity\InfoTramite $tramiteId
     * @return InfoComunicacion
     */
    public function setTramiteId(\telconet\schemaBundle\Entity\InfoTramite $tramiteId = null)
    {
        $this->tramiteId = $tramiteId;
    
        return $this;
    }

    /**
     * Get tramiteId
     *
     * @return \telconet\schemaBundle\Entity\InfoTramite 
     */
    public function getTramiteId()
    {
        return $this->tramiteId;
    }
    
    /**
     * Set puntoId
     *
     * @param integer $puntoId     
     */
    public function setPuntoId($puntoId)
    {
        $this->puntoId = $puntoId;
    
        return $this;
    }

    /**
     * Get puntoId
     *
     * @return integer 
     */
    public function getPuntoId()
    {
        return $this->puntoId;
    }
      
     /**
     * Set empresaCod
     *
     * @param string $empresaCod     
     */
    public function setEmpresaCod($empresaCod)
    {
        $this->empresaCod = $empresaCod;
    
        return $this;
    }

    /**
     * Get empresaCod
     *
     * @return string 
     */
    public function getEmpresaCod()
    {
        return $this->empresaCod;
    }
}
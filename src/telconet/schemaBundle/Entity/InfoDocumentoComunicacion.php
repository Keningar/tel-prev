<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoDocumentoComunicacion
 *
 * @ORM\Table(name="INFO_DOCUMENTO_COMUNICACION")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoDocumentoComunicacionRepository")
 */
class InfoDocumentoComunicacion
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_DOCUMENTO_COMUNICACION", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_DOCUMENTO_COMUNICACION", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var $documentoId
*
* @ORM\ManyToOne(targetEntity="InfoDocumento")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="DOCUMENTO_ID", referencedColumnName="ID_DOCUMENTO")
* })
*/
		
private $documentoId;

/**
* @var $comunicacionId
*
* @ORM\ManyToOne(targetEntity="InfoComunicacion")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="COMUNICACION_ID", referencedColumnName="ID_COMUNICACION")
* })
*/
		
private $comunicacionId;
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
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}




    /**
     * Set estado
     *
     * @param string $estado
     * @return InfoDocumentoComunicacion
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
     * @return InfoDocumentoComunicacion
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
     * @return InfoDocumentoComunicacion
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
     * @return InfoDocumentoComunicacion
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
     * Set documentoId
     *
     * @param \telconet\schemaBundle\Entity\InfoDocumento $documentoId
     * @return InfoDocumentoComunicacion
     */
    public function setDocumentoId(\telconet\schemaBundle\Entity\InfoDocumento $documentoId = null)
    {
        $this->documentoId = $documentoId;
    
        return $this;
    }

    /**
     * Get documentoId
     *
     * @return \telconet\schemaBundle\Entity\InfoDocumento 
     */
    public function getDocumentoId()
    {
        return $this->documentoId;
    }

    /**
     * Set comunicacionId
     *
     * @param \telconet\schemaBundle\Entity\InfoComunicacion $comunicacionId
     * @return InfoDocumentoComunicacion
     */
    public function setComunicacionId(\telconet\schemaBundle\Entity\InfoComunicacion $comunicacionId = null)
    {
        $this->comunicacionId = $comunicacionId;
    
        return $this;
    }

    /**
     * Get comunicacionId
     *
     * @return \telconet\schemaBundle\Entity\InfoComunicacion 
     */
    public function getComunicacionId()
    {
        return $this->comunicacionId;
    }
}
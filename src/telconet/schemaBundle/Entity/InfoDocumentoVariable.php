<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoDocumentoVariable
 *
 * @ORM\Table(name="INFO_DOCUMENTO_VARIABLE")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoDocumentoVariableRepository")
 */
class InfoDocumentoVariable
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_DOCUMENTO_VARIABLE", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_DOCUMENTO_VARIABLE", allocationSize=1, initialValue=1)
*/		
		
private $id;	

/**
* @var InfoDocumento
*
* @ORM\ManyToOne(targetEntity="InfoDocumento")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="DOCUMENTO_ID", referencedColumnName="ID_DOCUMENTO")
* })
*/			
     		
private $documentoId;

/**
* @var string $nombreDocumentoVariable
*
* @ORM\Column(name="NOMBRE_DOCUMENTO_VARIABLE", type="string", nullable=false)
*/		
     		
private $nombreDocumentoVariable;

/**
* @var integer $posicionDocumentoVariable
*
* @ORM\Column(name="POSICION_DOCUMENTO_VARIABLE", type="integer", nullable=false)
*/		
     		
private $posicionDocumentoVariable;

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
     		
public function getId(){
	return $this->id; 
}

/**
* Get documentoId
*
* @return telconet\schemaBundle\Entity\InfoDocumento
*/		
     		
public function getDocumentoId(){
	return $this->documentoId; 
}

/**
* Set documentoId
*
* @param telconet\schemaBundle\Entity\InfoDocumento $documentoId
*/
public function setDocumentoId(\telconet\schemaBundle\Entity\InfoDocumento $documentoId)
{
        $this->documentoId = $documentoId;
}

/**
* Get nombreDocumentoVariable
*
* @return string
*/		
     		
public function getNombreDocumentoVariable(){
	return $this->nombreDocumentoVariable; 
}

/**
* Set nombreDocumentoVariable
*
* @param string $nombreDocumentoVariable
*/
public function setNombreDocumentoVariable($nombreDocumentoVariable)
{
        $this->nombreDocumentoVariable = $nombreDocumentoVariable;
}

/**
* Get posicionDocumentoVariable
*
* @return integer
*/		
     		
public function getPosicionDocumentoVariable(){
	return $this->posicionDocumentoVariable; 
}

/**
* Set posicionDocumentoVariable
*
* @param string $posicionDocumentoVariable
*/
public function setPosicionDocumentoVariable($posicionDocumentoVariable)
{
        $this->posicionDocumentoVariable = $posicionDocumentoVariable;
}

/**
* Get estado
*
* @return string
*/		
     		
public function getEstado(){
	return $this->estado; 
}

/**
* Set estado
*
* @param string $estado
*/
public function setEstado($estado)
{
        $this->estado = $estado;
}


/**
* Get usrCreacion
*
* @return string
*/		
     		
public function getUsrCreacion(){
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
     		
public function getFeCreacion(){
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
     		
public function getIpCreacion(){
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


public function __toString()
{
    return $this->nombreDocumentoVariable;
}

}
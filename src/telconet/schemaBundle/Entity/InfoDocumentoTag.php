<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
* telconet\schemaBundle\Entity\InfoDocumentoTag
*
* @ORM\Table(name="INFO_DOCUMENTO_TAG")
* @ORM\Entity
*/
class InfoDocumentoTag
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_DOCUMENTO_TAG", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_DOCUMENTO_TAG", allocationSize=1, initialValue=1)
*/	
private $id;	
/**
* @var integer $documentoId
*
* @ORM\Column(name="DOCUMENTO_ID", type="integer", nullable=true)
*/	
private $documentoId;

/**
* @var integer $tagDocumentoId
*
* @ORM\Column(name="TAG_DOCUMENTO_ID", type="integer", nullable=true)
*/	
private $tagDocumentoId;

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
* @return integer
*/	
public function getDocumentoId(){
    return $this->valor;
}

/**
* Set documentoId
*
* @param integer documentoId
*/
public function setDocumentoId($documentoId)
{
    $this->documentoId = $documentoId;
}


//------------------------------------------------------------

/**
* Get tagDocumentoId
*
* @return tagDocumentoId
*/	
public function getTagDocumentoId(){
    return $this->tagDocumentoId;
}

/**
* Set tagDocumentoId
*
* @param integer $tagDocumentoId
*/
public function setTagDocumentoId($tagDocumentoId)
{
    $this->tagDocumentoId = $tagDocumentoId;
}

//------------------------------------------------------------
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



}
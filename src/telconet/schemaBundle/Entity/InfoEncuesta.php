<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoEncuesta
 *
 * @ORM\Table(name="INFO_ENCUESTA")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoEncuestaRepository")
 */
class InfoEncuesta
{

/**
* @var integer $id
*
* @ORM\Column(name="ID_ENCUESTA", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_ENCUESTA", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	

/**
* @var string $nombreEncuesta
*
* @ORM\Column(name="NOMBRE_ENCUESTA", type="string", nullable=false)
*/		
     		
private $nombreEncuesta;


/**
* @var string $descripcionEncuesta
*
* @ORM\Column(name="DESCRIPCION_ENCUESTA", type="string", nullable=true)
*/		
     		
private $descripcionEncuesta;

/**
* @var string $codigo
*
* @ORM\Column(name="CODIGO", type="string", nullable=false)
*/		
     		
private $codigo;

/**
* @var string $firma
*
* @ORM\Column(name="FIRMA", type="string", nullable=false)
*/		
     		
private $firma;

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

//--------------------------------------------------------------

/**
* Get nombreEncuesta
*
* @return string
*/		
     		
public function getNombreEncuesta(){
	return $this->nombreEncuesta; 
}

/**
* Set nombreEncuesta
*
* @param string $nombreEncuesta
*/
public function setNombreEncuesta($nombreEncuesta)
{
        $this->nombreEncuesta = $nombreEncuesta;
}

//--------------------------------------------------------------

/**
* Get descripcionEncuesta
*
* @return string
*/		
     		
public function getDescripcionEncuesta(){
	return $this->descripcionEncuesta; 
}

/**
* Set descripcionEncuesta
*
* @param string $descripcionEncuesta
*/
public function setDescripcionEncuesta($descripcionEncuesta)
{
        $this->descripcionEncuesta = $descripcionEncuesta;
}

//--------------------------------------------------------------
/**
* Get codigo
*
* @return string
*/		
     		
public function getCodigo(){
	return $this->codigo; 
}

/**
* Set codigo
*
* @param integer $codigo
*/
public function setCodigo($codigo)
{
        $this->codigo = $codigo;
}

//--------------------------------------------------------------
/*
* Get firma
*
* @return string
*/		
     		
public function getFirma(){
	return $this->firma; 
}

/**
* Set firma
*
* @param string $firma
*/
public function setFirma($firma)
{
        $this->firma = $firma;
}

//--------------------------------------------------------------
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
* @param string ipCreacion
*/
public function setIpCreacion($ipCreacion)
{
        $this->ipCreacion = $ipCreacion;
}



public function __toString()
{
        return $this->codigo;
}

}
<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiPregunta
 *
 * @ORM\Table(name="ADMI_PREGUNTA")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiPreguntaRepository")
 */
class AdmiPregunta
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_PREGUNTA", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_PREGUNTA", allocationSize=1, initialValue=1)
*/		
		
private $id;	

/**
* @var string $pregunta
*
* @ORM\Column(name="PREGUNTA", type="string", nullable=false)
*/		
     		
private $pregunta;
	
/**
* @var string $descripcion
*
* @ORM\Column(name="DESCRIPCION", type="string", nullable=true)
*/		
     		
private $descripcion;


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
* @var string $usrUltMod
*
* @ORM\Column(name="USR_ULT_MOD", type="string", nullable=false)
*/		
     		
private $usrUltMod;

/**
* @var datetime $feUltMod
*
* @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=false)
*/		
     		
private $feUltMod;

/**
* @var string $tipoRespuesta
*
* @ORM\Column(name="TIPO_RESPUESTA", type="string", nullable=false)
*/		
     		
private $tipoRespuesta;

/**
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Get pregunta
*
* @return string
*/		
     		
public function getPregunta(){
	return $this->pregunta; 
}

/**
* Set pregunta
*
* @param string $pregunta
*/
public function setPregunta($pregunta)
{
        $this->pregunta = $pregunta;
}

/**
* Get descripcion
*
* @return string
*/		
     		
public function getDescripcion(){
	return $this->descripcion; 
}

/**
* Set descripcion
*
* @param string $descripcion
*/
public function setDescripcion($descripcion)
{
        $this->descripcion = $descripcion;
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
* Get usrUltMod
*
* @return string
*/		
     		
public function getUsrUltMod(){
	return $this->usrUltMod; 
}

/**
* Set usrUltMod
*
* @param string $usrUltMod
*/
public function setUsrUltMod($usrUltMod)
{
        $this->usrUltMod = $usrUltMod;
}


/**
* Get feUltMod
*
* @return datetime
*/		
     		
public function getFeUltMod(){
	return $this->feUltMod; 
}

/**
* Set feUltMod
*
* @param datetime $feUltMod
*/
public function setFeUltMod($feUltMod)
{
        $this->feUltMod = $feUltMod;
}


/**
* Get tipoRespuesta
*
* @return string
*/		
     		
public function getTipoRespuesta(){
	return $this->tipoRespuesta; 
}

/**
* Set tipoRespuesta
*
* @param string $tipoRespuesta
*/
public function setTipoRespuesta($tipoRespuesta)
{
        $this->tipoRespuesta = $tipoRespuesta;
}



public function __toString()
{
        return $this->pregunta;
}

}
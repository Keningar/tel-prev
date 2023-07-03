<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiPreguntaRespuesta
 *
 * @ORM\Table(name="ADMI_PREGUNTA_RESPUESTA")
 * @ORM\Entity 
 */
class AdmiPreguntaRespuesta
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_PREGUNTA_RESPUESTA", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_PREGUNTA_RESPUESTA", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var integer $respuestaId
*
* @ORM\Column(name="RESPUESTA_ID", type="integer", nullable=false)
*/		
     		
private $respuestaId;

/**
* @var integer $preguntaId
*
* @ORM\Column(name="PREGUNTA_ID", type="integer", nullable=false)
*/		
     		
private $preguntaId;
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
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}


/**
* Get respuestaId
*
* @return integer
*/		
     		
public function getRespuestaId(){
	return $this->respuestaId; 
}

/**
* Set respuestaId
*
* @param integer $respuestaId
*/
public function setRespuestaId($respuestaId)
{
        $this->respuestaId = $respuestaId;
}

/**
* Get preguntaId
*
* @return integer
*/		
     		
public function getPreguntaId(){
	return $this->preguntaId; 
}

/**
* Set preguntaId
*
* @param integer $preguntaId
*/
public function setPreguntaId($preguntaId)
{
        $this->preguntaId = $preguntaId;
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


}
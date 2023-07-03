<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoRespuesta
 *
 * @ORM\Table(name="INFO_RESPUESTA")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoRespuestaRepository")
 */
class InfoRespuesta
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_RESPUESTA", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_RESPUESTA", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var integer $encuestaId
*
* @ORM\Column(name="ENCUESTA_ID", type="integer", nullable=false)
*/		
     		
private $encuestaId;

/**
* @var integer $preguntaId
*
* @ORM\Column(name="PREGUNTA_ID", type="integer", nullable=false)
*/		
     		
private $preguntaId;


/**
* @var integer $valor
*
* @ORM\Column(name="VALOR", type="integer", nullable=false)
*/		
     		
private $valor;

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
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Get encuestaId
*
* @return integer
*/		
     		
public function getEncuestaId(){
	return $this->encuestaId; 
}

/**
* Set encuestaId
*
* @param integer $encuestaId
*/
public function setEncuestaId($encuestaId)
{
        $this->encuestaId = $encuestaId;
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
* Get valor
*
* @return integer
*/		
     		
public function getValorId(){
	return $this->valor; 
}

/**
* Set valor
*
* @param integer $valor
*/
public function setValorId($valor)
{
        $this->valor = $valor;
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

public function __toString()
{
        return $this->valor;
}

}
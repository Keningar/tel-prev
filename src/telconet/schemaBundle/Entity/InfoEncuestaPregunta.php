<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoEncuestaPregunta
 *
 * @ORM\Table(name="INFO_ENCUESTA_PREGUNTA")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoEncuestaPreguntaRepository")
 */
class InfoEncuestaPregunta
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_ENCUESTA_PREGUNTA", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_ENCUESTA_PREGUNTA", allocationSize=1, initialValue=1)
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
* @ORM\Column(name="VALOR", type="string", nullable=false)
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
* @return string
*/		
     		
public function getValor(){
	return $this->valor; 
}

/**
* Set valor
*
* @param string $valor
*/
public function setValor($valor)
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
        return $this->valor;
}

}
<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiValidacionFormato
 *
 * @ORM\Table(name="ADMI_VALIDACION_FORMATO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiValidacionFormatoRepository")
 */
class AdmiValidacionFormato
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_VALIDACION_FORMATO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_VAL_FORMATO_DEBITO", allocationSize=1, initialValue=1)
*/		
		
private $id;	


/**
* @var AdmiFormatoDebito
*
* @ORM\ManyToOne(targetEntity="AdmiFormatoDebito")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="FORMATO_DEBITO_ID", referencedColumnName="ID_FORMATO_DEBITO")
* })
*/
		
private $formatoDebitoId;

/**
* @var string $campoTablaId
*
* @ORM\Column(name="CAMPO_TABLA_ID", type="string", nullable=true)
*/		
     		
private $campoTablaId;


/**
* @var string $equivalencia
*
* @ORM\Column(name="EQUIVALENCIA", type="string", nullable=true)
*/		
     		
private $equivalencia;


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
* Get formatoDebitoId
*
* @return telconet\schemaBundle\Entity\AdmiFormatoDebito
*/		
     		
public function getFormatoDebitoId(){
	return $this->formatoDebitoId; 
}

/**
* Set formatoDebitoId
*
* @param telconet\schemaBundle\Entity\AdmiFormatoDebito $formatoDebitoId
*/
public function setFormatoDebitoId(\telconet\schemaBundle\Entity\AdmiFormatoDebito $formatoDebitoId)
{
        $this->formatoDebitoId = $formatoDebitoId;
}


/**
* Get campoTablaId
*
* @return string
*/		
     		
public function getCampoTablaId(){
	return $this->campoTablaId; 
}

/**
* Set campoTablaId
*
* @param string $campoTablaId
*/
public function setCampoTablaId($campoTablaId)
{
        $this->campoTablaId = $campoTablaId;
}


/**
* Get equivalencia
*
* @return string
*/		
     		
public function getEquivalencia(){
	return $this->equivalencia; 
}

/**
* Set equivalencia
*
* @param string $equivalencia
*/
public function setEquivalencia($equivalencia)
{
        $this->equivalencia = $equivalencia;
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

}

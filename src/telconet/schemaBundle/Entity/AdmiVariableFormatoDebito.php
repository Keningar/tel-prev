<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiVariableFormatoDebito
 *
 * @ORM\Table(name="ADMI_VARIABLE_FORMATO_DEBITO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiVariableFormatoDebitoRepository")
 */
class AdmiVariableFormatoDebito
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_VARIABLE_FORMATO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_VAR_FORMATO_DEBITO", allocationSize=1, initialValue=1)
*/		
		
private $id;	


/**
* @var string $campo
*
* @ORM\Column(name="CAMPO", type="string", nullable=true)
*/		
     		
private $campo;


/**
* @var string $campoCriterio
*
* @ORM\Column(name="CAMPO_CRITERIO", type="string", nullable=false)
*/		
     		
private $campoCriterio;

/**
* @var string $descripcion
*
* @ORM\Column(name="DESCRIPCION", type="string", nullable=true)
*/		
     		
private $descripcion;


/**
* @var string $tabla
*
* @ORM\Column(name="Tabla", type="string", nullable=true)
*/		
     		
private $tabla;


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
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=false)
*/		
     		
private $estado;

/**
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
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
* Get campo
*
* @return string
*/		
     		
public function getCampo(){
	return $this->campo; 
}

/**
* Set campo
*
* @param string $campo
*/
public function setCampo($campo)
{
        $this->campo = $campo;
}

/**
* Get campoCriterio
*
* @return string
*/		
     		
public function getCampoCriterio(){
	return $this->campoCriterio; 
}

/**
* Set campoCriterio
*
* @param string $campoCriterio
*/
public function setCampoCriterio($campoCriterio)
{
        $this->campoCriterio = $campoCriterio;
}

/**
* Get tabla
*
* @return string
*/		
     		
public function getTabla(){
	return $this->tabla; 
}

/**
* Set tabla
*
* @param string $tabla
*/
public function setTabla($tabla)
{
        $this->tabla = $tabla;
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


}

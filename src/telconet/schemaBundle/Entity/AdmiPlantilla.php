<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiPlantilla
 *
 * @ORM\Table(name="ADMI_PLANTILLA")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiPlantillaRepository")
 */
class AdmiPlantilla
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_PLANTILLA", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_PLANTILLA", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var string $empresaCod
*
* @ORM\Column(name="EMPRESA_COD", type="string", nullable=false)
*/		
     		
private $empresaCod;


/**
* @var string $nombrePlantilla
*
* @ORM\Column(name="NOMBRE_PLANTILLA", type="string", nullable=false)
*/		
     		
private $nombrePlantilla;

/**
* @var string $codigo
*
* @ORM\Column(name="CODIGO", type="string", nullable=false)
*/		
     		
private $codigo;

/**
* @var string $plantilla
*
* @ORM\Column(name="PLANTILLA", type="string", nullable=false)
*/		
     		
private $plantilla;


/**
* @var string $modulo
*
* @ORM\Column(name="MODULO", type="string", nullable=false)
*/		
     		
private $modulo;

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
* Get empresaCod
*
* @return string
*/		
     		
public function getEmpresaCod(){
	return $this->empresaCod; 
}

/**
* Set empresaCod
*
* @param string $empresaCod
*/
public function setEmpresaCod($empresaCod)
{
        $this->empresaCod = $empresaCod;
}



/**
* Get nombrePlantilla
*
* @return string
*/		
     		
public function getNombrePlantilla(){
	return $this->nombrePlantilla; 
}

/**
* Set nombrePlantilla
*
* @param string $nombrePlantilla
*/
public function setNombrePlantilla($nombrePlantilla)
{
        $this->nombrePlantilla = $nombrePlantilla;
}


/**
* Get modulo
*
* @return string
*/		
     		
public function getModulo(){
	return $this->modulo; 
}

/**
* Set modulo
*
* @param string $modulo
*/
public function setModulo($modulo)
{
        $this->modulo = $modulo;
}




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
* @param string $codigo
*/
public function setCodigo($codigo)
{
        $this->codigo = $codigo;
}



/**
* Get plantilla
*
* @return string
*/		
     		
public function getPlantilla(){
	return $this->plantilla; 
}

/**
* Set plantilla
*
* @param string $plantilla
*/
public function setPlantilla($plantilla)
{
        $this->plantilla = $plantilla;
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
        return $this->nombrePlantilla;
}

}
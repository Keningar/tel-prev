<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiTecnologia
 *
 * @ORM\Table(name="ADMI_TECNOLOGIA")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiTecnologiaRepository")
 */
class AdmiTecnologia
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_TECNOLOGIA", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_TECNOLOGIA", allocationSize=1, initialValue=1)
*/		
		
private $id;	

/**
* @var string $nombreTecnologia
*
* @ORM\Column(name="NOMBRE_TECNOLOGIA", type="string", nullable=true)
*/		
     		
private $nombreTecnologia;

/**
* @var string $descripcionTecnologia
*
* @ORM\Column(name="DESCRIPCION_TECNOLOGIA", type="string", nullable=true)
*/		
     		
private $descripcionTecnologia;

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
* Get nombreTecnologia
*
* @return string
*/		
     		
public function getNombreTecnologia(){
	return $this->nombreTecnologia; 
}

/**
* Set nombreTecnologia
*
* @param string $nombreTecnologia
*/
public function setNombreTecnologia($nombreTecnologia)
{
        $this->nombreTecnologia = $nombreTecnologia;
}


/**
* Get descripcionTecnologia
*
* @return string
*/		
     		
public function getDescripcionTecnologia(){
	return $this->descripcionTecnologia; 
}

/**
* Set descripcionTecnologia
*
* @param string $descripcionTecnologia
*/
public function setDescripcionTecnologia($descripcionTecnologia)
{
        $this->descripcionTecnologia = $descripcionTecnologia;
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
    return $this->nombreTecnologia;
}

}
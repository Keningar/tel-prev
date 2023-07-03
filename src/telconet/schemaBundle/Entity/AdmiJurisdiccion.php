<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiJurisdiccion
 *
 * @ORM\Table(name="ADMI_JURISDICCION")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiJurisdiccionRepository")
 */
class AdmiJurisdiccion
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_JURISDICCION", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_JURISDICCION", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var integer $oficinaId
*
* @ORM\Column(name="OFICINA_ID", type="integer", nullable=false)
*/		
     		
private $oficinaId;

/**
* @var string $nombreJurisdiccion
*
* @ORM\Column(name="NOMBRE_JURISDICCION", type="string", nullable=false)
*/		
     		
private $nombreJurisdiccion;

/**
* @var string $descripcionJurisdiccion
*
* @ORM\Column(name="DESCRIPCION_JURISDICCION", type="string", nullable=true)
*/		
     		
private $descripcionJurisdiccion;

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
* @var integer $cupo
*
* @ORM\Column(name="CUPO", type="integer", nullable=false)
*/		
     		
private $cupo;

/**
* @var integer $cupoMobile
*
* @ORM\Column(name="CUPO_MOBILE", type="integer", nullable=false)
*/

private $cupoMobile;


/**
* Get id
*
* @return integer
*/

public function getId(){
	return $this->id; 
}

/**
* Get oficinaId
*
* @return integer
*/		
     		
public function getOficinaId(){
	return $this->oficinaId; 
}

/**
* Set oficinaId
*
* @param integer $oficinaId
*/
public function setOficinaId($oficinaId)
{
        $this->oficinaId = $oficinaId;
}



/**
* Get nombreJurisdiccion
*
* @return string
*/		
     		
public function getNombreJurisdiccion(){
	return $this->nombreJurisdiccion; 
}

/**
* Set nombreJurisdiccion
*
* @param string $nombreJurisdiccion
*/
public function setNombreJurisdiccion($nombreJurisdiccion)
{
        $this->nombreJurisdiccion = $nombreJurisdiccion;
}


/**
* Get descripcionJurisdiccion
*
* @return string
*/		
     		
public function getDescripcionJurisdiccion(){
	return $this->descripcionJurisdiccion; 
}

/**
* Set descripcionJurisdiccion
*
* @param string $descripcionJurisdiccion
*/
public function setDescripcionJurisdiccion($descripcionJurisdiccion)
{
        $this->descripcionJurisdiccion = $descripcionJurisdiccion;
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
* Get cupo
*
* @return integer
*/		
     		
public function getCupo(){
	return $this->cupo; 
}

/**
* Set cupo
*
* @param integer $cupo
*/
public function setCupo($cupo)
{
        $this->cupo = $cupo;
}

/**
* Get cupoMobile
*
* @return integer
*/		
     		
public function getCupoMobile(){
	return $this->cupoMobile; 
}

/**
* Set cupoMobile
*
* @param integer $cupoMobile
*/
public function setCupoMobile($cupoMobile)
{
        $this->cupoMobile = $cupoMobile;
}

public function __toString()
{
    return $this->nombreJurisdiccion;
}

}

<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiTipoElemento
 *
 * @ORM\Table(name="ADMI_TIPO_ELEMENTO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiTipoElementoRepository")
 */
class AdmiTipoElemento
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_TIPO_ELEMENTO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_TIPO_ELEMENTO", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var string $nombreTipoElemento
*
* @ORM\Column(name="NOMBRE_TIPO_ELEMENTO", type="string", nullable=false)
*/		
     		
private $nombreTipoElemento;

/**
* @var string $descripcionTipoElemento
*
* @ORM\Column(name="DESCRIPCION_TIPO_ELEMENTO", type="string", nullable=true)
*/		
     		
private $descripcionTipoElemento;

/**
* @var string $claseTipoElemento
*
* @ORM\Column(name="CLASE_TIPO_ELEMENTO", type="string", nullable=false)
*/		
     		
private $claseTipoElemento;


/**
* @var string $esDe
*
* @ORM\Column(name="ES_DE", type="string", nullable=false)
*/		
     		
private $esDe;

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
* Get nombreTipoElemento
*
* @return string
*/		
     		
public function getNombreTipoElemento(){
	return $this->nombreTipoElemento; 
}

/**
* Set nombreTipoElemento
*
* @param string $nombreTipoElemento
*/
public function setNombreTipoElemento($nombreTipoElemento)
{
        $this->nombreTipoElemento = $nombreTipoElemento;
}


/**
* Get descripcionTipoElemento
*
* @return string
*/		
     		
public function getDescripcionTipoElemento(){
	return $this->descripcionTipoElemento; 
}

/**
* Set descripcionTipoElemento
*
* @param string $descripcionTipoElemento
*/
public function setDescripcionTipoElemento($descripcionTipoElemento)
{
        $this->descripcionTipoElemento = $descripcionTipoElemento;
}


/**
* Get claseTipoElemento
*
* @return string
*/		
     		
public function getClaseTipoElemento(){
	return $this->claseTipoElemento; 
}

/**
* Set claseTipoElemento
*
* @param string $claseTipoElemento
*/
public function setClaseTipoElemento($claseTipoElemento)
{
        $this->claseTipoElemento = $claseTipoElemento;
}

/**
* Get esDe
*
* @return string
*/		
     		
public function getEsDe(){
	return $this->esDe; 
}

/**
* Set esDe
*
* @param string $esDe
*/
public function setEsDe($esDe)
{
        $this->esDe = $esDe;
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
    return $this->nombreTipoElemento;
}

}
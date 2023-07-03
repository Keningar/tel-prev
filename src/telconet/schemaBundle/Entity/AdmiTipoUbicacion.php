<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiTipoUbicacion
 *
 * @ORM\Table(name="ADMI_TIPO_UBICACION")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiTipoUbicacionRepository")
 */
class AdmiTipoUbicacion
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_TIPO_UBICACION", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_TIPO_UBICACION", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var string $codigoTipoUbicacion
*
* @ORM\Column(name="CODIGO_TIPO_UBICACION", type="string", nullable=false)
*/		
     		
private $codigoTipoUbicacion;

/**
* @var string $descripcionTipoUbicacion
*
* @ORM\Column(name="DESCRIPCION_TIPO_UBICACION", type="string", nullable=false)
*/		
     		
private $descripcionTipoUbicacion;

/**
* @var datetime $feCreacion
*
* @ORM\Column(name="FE_CREACION", type="datetime", nullable=false)
*/		
     		
private $feCreacion;

/**
* @var string $usrCreacion
*
* @ORM\Column(name="USR_CREACION", type="string", nullable=false)
*/		
     		
private $usrCreacion;

/**
* @var datetime $feUltMod
*
* @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=true)
*/		
     		
private $feUltMod;

/**
* @var string $usrUltMod
*
* @ORM\Column(name="USR_ULT_MOD", type="string", nullable=true)
*/		
     		
private $usrUltMod;

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
* Get codigoTipoUbicacion
*
* @return string
*/		
     		
public function getCodigoTipoUbicacion(){
	return $this->codigoTipoUbicacion; 
}

/**
* Set codigoTipoUbicacion
*
* @param string $codigoTipoUbicacion
*/
public function setCodigoTipoUbicacion($codigoTipoUbicacion)
{
        $this->codigoTipoUbicacion = $codigoTipoUbicacion;
}


/**
* Get descripcionTipoUbicacion
*
* @return string
*/		
     		
public function getDescripcionTipoUbicacion(){
	return $this->descripcionTipoUbicacion; 
}

/**
* Set descripcionTipoUbicacion
*
* @param string $descripcionTipoUbicacion
*/
public function setDescripcionTipoUbicacion($descripcionTipoUbicacion)
{
        $this->descripcionTipoUbicacion = $descripcionTipoUbicacion;
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

public function __toString() {
    return $this->descripcionTipoUbicacion;

}

}
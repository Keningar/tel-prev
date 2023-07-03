<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiTipoProgreso
 *
 * @ORM\Table(name="ADMI_TIPO_PROGRESO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiTipoProgresoRepository")
 */
class AdmiTipoProgreso
{
/**
* @var integer $id
*
* @ORM\Column(name="ID_TIPO_PROGRESO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_TIPO_PROGRESO", allocationSize=1, initialValue=1)
*/		
		
private $id;	
/**
* @var string $nombreTipoProgreso
*
* @ORM\Column(name="NOMBRE_TIPO_PROGRESO", type="string", nullable=false)
*/		
     		
private $nombreTipoProgreso;

/**
* @var string $descripcion
*
* @ORM\Column(name="DESCRIPCION", type="string", nullable=true)
*/		
     		
private $descripcion;

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
* @var string $usrUltMod
*
* @ORM\Column(name="USR_ULT_MOD", type="string", nullable=true)
*/		
     		
private $usrUltMod;

/**
* @var datetime $feUltMod
*
* @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=true)
*/		
     		
private $feUltMod;

/**
* @var string $codigo
*
* @ORM\Column(name="CODIGO", type="string", nullable=true)
*/		
     		
private $codigo;
/**
* Get id
*
* @return integer
*/		

public function getId(){
	return $this->id; 
}

/**
* Get nombreTipoProgreso
*
* @return string
*/		
     		
public function getNombreTipoProgreso(){
	return $this->nombreTipoProgreso; 
}

/**
* Set nombreTipoProgreso
*
* @param string $nombreTipoProgreso
*/
public function setNombreTipoProgreso($nombreTipoProgreso)
{
        $this->nombreTipoProgreso = $nombreTipoProgreso;
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
* @return 
*/		
     		
public function getFeCreacion(){
	return $this->feCreacion; 
}

/**
* Set feCreacion
*
* @param  $feCreacion
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

}
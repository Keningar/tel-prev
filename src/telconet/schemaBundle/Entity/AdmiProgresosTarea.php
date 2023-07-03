<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiTipoProgreso
 *
 * @ORM\Table(name="ADMI_PROGRESOS_TAREA")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiProgresosTareaRepository")
 */
class AdmiProgresosTarea
{
/**
* @var integer $id
*
* @ORM\Column(name="ID_PROGRESOS_TAREA", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_PROGRESOS_TAREA", allocationSize=1, initialValue=1)
*/		
		
private $id;	

/**
* @var string $codigoTarea
*
* @ORM\Column(name="CODIGO_TAREA", type="string", nullable=false)
*/		
     		
private $codigoTarea;

/**
* @var string $nombreTarea
*
* @ORM\Column(name="NOMBRE_TAREA", type="string", nullable=true)
*/		
     		
private $nombreTarea;

/**
* @var string $descripcionTarea
*
* @ORM\Column(name="DESCRIPCION_TAREA", type="string", nullable=true)
*/		
     		
private $descripcionTarea;


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
* Get id
*
* @return integer
*/		

public function getId(){
	return $this->id; 
}

/**
* Get codigoTarea
*
* @return string
*/		
     		
public function getCodigoTarea(){
	return $this->codigoTarea; 
}

/**
* Set codigoTarea
*
* @param string $codigoTarea
*/
public function setCodigoTarea($codigoTarea)
{
        $this->codigoTarea = $codigoTarea;
}


/**
* Get nombreTarea
*
* @return string
*/		
     		
public function getNombreTarea(){
	return $this->nombreTarea; 
}

/**
* Set nombreTarea
*
* @param string $nombreTarea
*/
public function setNombreTarea($nombreTarea)
{
        $this->nombreTarea = $nombreTarea;
}


/**
* Get descripcionTarea
*
* @return string
*/		
     		
public function getDescripcionTarea(){
	return $this->descripcionTarea; 
}

/**
* Set descripcionTarea
*
* @param string $descripcionTarea
*/
public function setDescripcion($descripcionTarea)
{
        $this->descripcionTarea = $descripcionTarea;
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

}
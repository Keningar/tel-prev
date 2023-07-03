<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiTipoHorarios
 *
 * @ORM\Table(name="ADMI_TIPO_HORARIOS")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiTipoHorariosRepository")
 */
class AdmiTipoHorarios
{

/**
* @var integer $id
*
* @ORM\Column(name="ID_TIPO_HORARIO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_TIPO_HORARIO", allocationSize=1, initialValue=1)
*/		
		
private $id;	

/**
* @var string $nombreTipoHorario
*
* @ORM\Column(name="NOMBRE_TIPO_HORARIO", type="string", nullable=false)
*/		
     		
private $nombreTipoHorario;

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
* @var string $usrModificacion
*
* @ORM\Column(name="USR_MODIFICACION", type="string", nullable=false)
*/		
     		
private $usrModificacion;

/**
* @var datetime $feModificacion
*
* @ORM\Column(name="FE_MODIFICACION", type="datetime", nullable=false)
*/		
     		
private $feModificacion;

/**
* @return int
*/
public function getId()
{
   return $this->id;
}

/**
* @param int $id
*/
public function setId($id)
{
    $this->id = $id;
}

/**
* Get nombreTipoHorario
*
* @return string
*/		
     		
public function getNombreTipoHorario(){
	return $this->nombreTipoHorario; 
}

/**
* Set nombreTipoHorario
*
* @param string $nombreTipoHorario
*/
public function setNombreTipoHorario($nombreTipoHorario)
{
        $this->nombreTipoHorario = $nombreTipoHorario;
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
* Get usrModificacion
*
* @return string
*/		
     		
public function getUsrModificacion(){
	return $this->usrModificacion; 
}

/**
* Set usrModificacion
*
* @param string $usrModificacion
*/
public function setUsrModificacion($usrModificacion)
{
        $this->usrModificacion = $usrModificacion;
}
/*
public function __toString() {
    return $this->nombreTipoHorario;
}*/

}

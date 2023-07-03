<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiCaracteristica
 *
 * @ORM\Table(name="ADMI_CARACTERISTICA")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiCaracteristicaRepository")
 */
class AdmiCaracteristica
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_CARACTERISTICA", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_CARACTERISTICA", allocationSize=1, initialValue=1)
*/		
		
private $id;	

/**
* @var string $descripcionCaracteristica
*
* @ORM\Column(name="DESCRIPCION_CARACTERISTICA", type="string", nullable=false)
*/		
     		
private $descripcionCaracteristica;

/**
* @var datetime $feCreacion
*
* @ORM\Column(name="FE_CREACION", type="datetime", nullable=true)
*/		
     		
private $feCreacion;

/**
* @var string $usrCreacion
*
* @ORM\Column(name="USR_CREACION", type="string", nullable=false)
*/		
     		
private $usrCreacion;

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
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=true)
*/		
     		
private $estado;

/**
* @var string $tipoIngreso
*
* @ORM\Column(name="TIPO_INGRESO", type="string", nullable=true)
*/		
     		
private $tipoIngreso;

/**
* @var string $tipo
*
* @ORM\Column(name="TIPO", type="string", nullable=true)
*/		
     		
private $tipo;

/**
* @var string $detalleCaracteristica
*
* @ORM\Column(name="DETALLE_CARACTERISTICA", type="string", nullable=true)
*/		
     		
private $detalleCaracteristica;

/**
* Get id
*
* @return integer
*/		

public function getId(){
	return $this->id; 
}

/**
* Get descripcionCaracteristica
*
* @return string
*/		
     		
public function getDescripcionCaracteristica(){
	return $this->descripcionCaracteristica; 
}

/**
* Set descripcionCaracteristica
*
* @param string $descripcionCaracteristica
*/
public function setDescripcionCaracteristica($descripcionCaracteristica)
{
        $this->descripcionCaracteristica = $descripcionCaracteristica;
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
* Get tipoIngreso
*
* @return string
*/		
     		
public function getTipoIngreso(){
	return $this->tipoIngreso; 
}

/**
* Set tipoIngreso
*
* @param string $tipoIngreso
*/
public function setTipoIngreso($tipoIngreso)
{
        $this->tipoIngreso = $tipoIngreso;
}

/**
* Get tipo
*
* @return string
*/		
     		
public function getTipo(){
	return $this->tipo; 
}

/**
* Set tipo
*
* @param string $tipo
*/
public function setTipo($tipo)
{
        $this->tipo = $tipo;
}

/**
* Get detalleCaracteristica
*
* @return string
*/

public function getDetalleCaracteristica(){
        return $this->detalleCaracteristica; 
}

/**
* Set detalleCaracteristica
*
* @param string $detalleCaracteristica
*/
public function setDetalleCaracteristica($detalleCaracteristica)
{
        $this->detalleCaracteristica = $detalleCaracteristica;
}

public function __toString()
{
        return $this->descripcionCaracteristica;
}

}
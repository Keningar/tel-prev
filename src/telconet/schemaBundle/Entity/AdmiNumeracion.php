<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiNumeracion
 *
 * @ORM\Table(name="ADMI_NUMERACION")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiNumeracionRepository")
 */
class AdmiNumeracion
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_NUMERACION", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_NUMERACION", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var string $empresaId
*
* @ORM\Column(name="EMPRESA_ID", type="string", nullable=false)
*/		
     		
private $empresaId;

/**
* @var integer $oficinaId
*
* @ORM\Column(name="OFICINA_ID", type="integer", nullable=false)
*/		
     		
private $oficinaId;

/**
* @var string $descripcion
*
* @ORM\Column(name="DESCRIPCION", type="string", nullable=false)
*/		
     		
private $descripcion;

/**
* @var string $codigo
*
* @ORM\Column(name="CODIGO", type="string", nullable=false)
*/		
     		
private $codigo;

/**
* @var string $numeracionUno
*
* @ORM\Column(name="NUMERACION_UNO", type="string", nullable=false)
*/		
     		
private $numeracionUno;

/**
* @var string $numeracionDos
*
* @ORM\Column(name="NUMERACION_DOS", type="string", nullable=false)
*/		
     		
private $numeracionDos;

/**
* @var integer $secuencia
*
* @ORM\Column(name="SECUENCIA", type="integer", nullable=false)
*/		
     		
private $secuencia;

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
* @var string $tabla
*
* @ORM\Column(name="TABLA", type="string", nullable=false)
*/		
     		
private $tabla;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=false)
*/		
     		
private $estado;

/**
* @var string $numeroAutorizacion
*
* @ORM\Column(name="NUMERO_AUTORIZACION", type="string", nullable=true)
*/			
private $numeroAutorizacion;

/**
* @var string $procesosAutomaticos
*
* @ORM\Column(name="PROCESOS_AUTOMATICOS", type="string", nullable=false)
*/		
     		
private $procesosAutomaticos;

/**
* @var string $tipoId
*
* @ORM\Column(name="TIPO_ID", type="integer", nullable=true)
*/			
private $tipoId;

/**
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Get empresaId
*
* @return string
*/		
     		
public function getEmpresaId(){
	return $this->empresaId; 
}

/**
* Set empresaId
*
* @param string $empresaId
*/
public function setEmpresaId($empresaId)
{
        $this->empresaId = $empresaId;
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
* Get numeracionUno
*
* @return string
*/		
     		
public function getNumeracionUno(){
	return $this->numeracionUno; 
}

/**
* Set numeracionUno
*
* @param string $numeracionUno
*/
public function setNumeracionUno($numeracionUno)
{
        $this->numeracionUno = $numeracionUno;
}


/**
* Get numeracionDos
*
* @return string
*/		
     		
public function getNumeracionDos(){
	return $this->numeracionDos; 
}

/**
* Set numeracionDos
*
* @param string $numeracionDos
*/
public function setNumeracionDos($numeracionDos)
{
        $this->numeracionDos = $numeracionDos;
}


/**
* Get secuencia
*
* @return integer
*/		
     		
public function getSecuencia(){
	return $this->secuencia; 
}

/**
* Set secuencia
*
* @param integer $secuencia
*/
public function setSecuencia($secuencia)
{
        $this->secuencia = $secuencia;
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
* Get numeroAutorizacion
*
* @return numeroAutorizacion
*/		
     		
public function getNumeroAutorizacion(){
	return $this->numeroAutorizacion; 
}

/**
* Set numeroAutorizacion
*
* @param string $numeroAutorizacion
*/
public function setNumeroAutorizacion($numeroAutorizacion)
{
        $this->numeroAutorizacion = $numeroAutorizacion;
}

/**
* Get procesosAutomaticos
*
* @return string
*/		
     		
public function getProcesosAutomaticos()
{
	return $this->procesosAutomaticos; 
}

/**
* Set procesosAutomaticos
*
* @param string $procesosAutomaticos
*/
public function setProcesosAutomaticos($procesosAutomaticos)
{
    $this->procesosAutomaticos = $procesosAutomaticos;
}

/**
* Get tipoId
*
* @return tipoId
*/		
     		
public function getTipoId()
{
	return $this->tipoId; 
}

/**
* Set tipoId
*
* @param integer $tipoId
*/
public function setTipoId($tipoId)
{
    $this->tipoId = $tipoId;
}

public function __toString(){
      return $this->numeracionUno.'-'.$this->numeracionDos;
}

}

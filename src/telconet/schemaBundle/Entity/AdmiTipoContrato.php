<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiTipoContrato
 *
 * @ORM\Table(name="ADMI_TIPO_CONTRATO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiTipoContratoRepository")
 */
class AdmiTipoContrato
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_TIPO_CONTRATO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_TIPO_CONTRATO", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var InfoEmpresaGrupo
*
* @ORM\ManyToOne(targetEntity="InfoEmpresaGrupo")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="EMPRESA_COD", referencedColumnName="COD_EMPRESA")
* })
*/
		
private $empresaCod;

/**
* @var string $descripcionTipoContrato
*
* @ORM\Column(name="DESCRIPCION_TIPO_CONTRATO", type="string", nullable=false)
*/		
     		
private $descripcionTipoContrato;

/**
* @var integer $tiempoFinalizacion
*
* @ORM\Column(name="TIEMPO_FINALIZACION", type="integer", nullable=false)
*/		
     		
private $tiempoFinalizacion;

/**
* @var integer $tiempoAlertaFinalizacion
*
* @ORM\Column(name="TIEMPO_ALERTA_FINALIZACION", type="integer", nullable=false)
*/		
     		
private $tiempoAlertaFinalizacion;

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
* Get empresaCod
*
* @return telconet\schemaBundle\Entity\InfoEmpresaGrupo
*/		
     		
public function getEmpresaCod(){
	return $this->empresaCod; 
}

/**
* Set empresaCod
*
* @param telconet\schemaBundle\Entity\InfoEmpresaGrupo $empresaCod
*/
public function setEmpresaCod(\telconet\schemaBundle\Entity\InfoEmpresaGrupo $empresaCod)
{
        $this->empresaCod = $empresaCod;
}


/**
* Get descripcionTipoContrato
*
* @return string
*/		
     		
public function getDescripcionTipoContrato(){
	return $this->descripcionTipoContrato; 
}

/**
* Set descripcionTipoContrato
*
* @param string $descripcionTipoContrato
*/
public function setDescripcionTipoContrato($descripcionTipoContrato)
{
        $this->descripcionTipoContrato = $descripcionTipoContrato;
}

/**
* Get tiempoFinalizacion
*
* @return integer
*/		
     		
public function getTiempoFinalizacion(){
	return $this->tiempoFinalizacion; 
}

/**
* Set tiempoFinalizacion
*
* @param integer $tiempoFinalizacion
*/
public function setTiempoFinalizacion($tiempoFinalizacion)
{
        $this->tiempoFinalizacion = $tiempoFinalizacion;
}

/**
* Get tiempoAlertaFinalizacion
*
* @return integer
*/		
     		
public function getTiempoAlertaFinalizacion(){
	return $this->tiempoAlertaFinalizacion; 
}

/**
* Set tiempoAlertaFinalizacion
*
* @param integer $tiempoAlertaFinalizacion
*/
public function setTiempoAlertaFinalizacion($tiempoAlertaFinalizacion)
{
        $this->tiempoAlertaFinalizacion = $tiempoAlertaFinalizacion;
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

public function __toString()
{
        return $this->descripcionTipoContrato;
}

}
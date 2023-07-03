<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoPlanCab
 *
 * @ORM\Table(name="INFO_PLAN_CAB")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoPlanCabRepository")
 */
class InfoPlanCab
{


/**
* @var string $ipCreacion
*
* @ORM\Column(name="IP_CREACION", type="string", nullable=true)
*/		
     		
private $ipCreacion;

/**
* @var integer $id
*
* @ORM\Column(name="ID_PLAN", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_PLAN_CAB", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var string $codigoPlan
*
* @ORM\Column(name="CODIGO_PLAN", type="string", nullable=true)
*/		
     		
private $codigoPlan;

/**
* @var string $nombrePlan
*
* @ORM\Column(name="NOMBRE_PLAN", type="string", nullable=true)
*/		
     		
private $nombrePlan;

/**
* @var string $descripcionPlan
*
* @ORM\Column(name="DESCRIPCION_PLAN", type="string", nullable=true)
*/		
     		
private $descripcionPlan;

/**
* @var datetime $feCreacion
*
* @ORM\Column(name="FE_CREACION", type="datetime", nullable=true)
*/		
     		
private $feCreacion;

/**
* @var string $usrCreacion
*
* @ORM\Column(name="USR_CREACION", type="string", nullable=true)
*/		
     		
private $usrCreacion;

/**
* @var integer $descuentoPlan
*
* @ORM\Column(name="DESCUENTO_PLAN", type="integer", nullable=true)
*/		
     		
private $descuentoPlan;

/**
* @var string $empresaCod
*
* @ORM\Column(name="EMPRESA_COD", type="string", nullable=true)
*/		
     		
private $empresaCod; //se modifico el tipo de dato (integer -> string)

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=true)
*/		
     		
private $estado;

/**
* @var string $iva
*
* @ORM\Column(name="IVA", type="string", nullable=true)
*/		
     		
private $iva;	

/**
* @var string $tipo
*
* @ORM\Column(name="TIPO", type="string", nullable=true)
*/		
     		
private $tipo;		


/**
* @var integer $idSit
*
* @ORM\Column(name="ID_SIT", type="integer", nullable=true)
*/		
     		
private $idSit;

/**
* @var integer $planId
*
* @ORM\Column(name="PLAN_ID", type="integer", nullable=true)
*/		
     
private $planId;

/**
* @var string $codigoInterno
*
* @ORM\Column(name="CODIGO_INTERNO", type="string", nullable=true)
*/		
     		
private $codigoInterno;

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
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Get codigoPlan
*
* @return string
*/		
     		
public function getCodigoPlan(){
	return $this->codigoPlan; 
}

/**
* Set codigoPlan
*
* @param string $codigoPlan
*/
public function setCodigoPlan($codigoPlan)
{
        $this->codigoPlan = $codigoPlan;
}


/**
* Get nombrePlan
*
* @return string
*/		
     		
public function getNombrePlan(){
	return $this->nombrePlan; 
}

/**
* Set nombrePlan
*
* @param string $nombrePlan
*/
public function setNombrePlan($nombrePlan)
{
        $this->nombrePlan = $nombrePlan;
}

/**
* Get descripcionPlan
*
* @return string
*/		
     		
public function getDescripcionPlan(){
	return $this->descripcionPlan; 
}

/**
* Set descripcionPlan
*
* @param string $descripcionPlan
*/
public function setDescripcionPlan($descripcionPlan)
{
        $this->descripcionPlan = $descripcionPlan;
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
* Get descuentoPlan
*
* @return integer
*/		
     		
public function getDescuentoPlan(){
	return $this->descuentoPlan; 
}

/**
* Set descuentoPlan
*
* @param integer $descuentoPlan
*/
public function setDescuentoPlan($descuentoPlan)
{
        $this->descuentoPlan = $descuentoPlan;
}

/**
* Get empresaCod
*
* @return string
*/		
     		
public function getEmpresaCod(){
	return $this->empresaCod; 
}

/**
* Set empresaId
*
* @param string $empresaCod
*/
public function setEmpresaCod($empresaCod)
{
        $this->empresaCod = $empresaCod;
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
* Get iva
*
* @return string
*/		
     		
public function getIva(){
	return $this->iva; 
}

/**
* Set estado
*
* @param string $estado
*/
public function setIva($iva)
{
        $this->iva = $iva;
}


public function __toString(){
    return $this->nombrePlan;
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
* Get idSit
*
* @return integer
*/		
     		
public function getIdSit(){
	return $this->idSit; 
}

/**
* Set idSit
*
* @param integer $idSit
*/
public function setIdSit($idSit)
{
        $this->idSit = $idSit;
}

/**
* Get planId
*
* @return integer
*/		
     		
public function getPlanId(){
	return $this->planId; 
}

/**
* Set planId
*
* @param integer $planId
*/
public function setPlanId($planId)
{
        $this->planId = $planId;
}

/**
* Get codigoInterno
*
* @return string
*/		
     		
public function getCodigoInterno(){
	return $this->codigoInterno; 
}

/**
* Set codigoInterno
*
* @param string $codigoInterno
*/
public function setCodigoInterno($codigoInterno)
{
        $this->codigoInterno = $codigoInterno;
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

}

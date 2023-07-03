<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoProgresoTarea
 *
 * @ORM\Table(name="INFO_PROGRESO_TAREA")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoProgresoTareaRepository")
 */
class InfoProgresoTarea
{

    
/**
* @var integer $id
*
* @ORM\Column(name="ID_PROGRESO_TAREA", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_PROGRESO_TAREA", allocationSize=1, initialValue=1)
*/		
		
private $id;	

/**
* @var string $horaTransaccionTarea
*
* @ORM\Column(name="HORA_TRANSACCION_PROGR_TAREA", type="datetime", nullable=true)
*/		
     		
private $horaTransaccionTarea;

/**
* @var InfoProgresoPorcentaje
*
* @ORM\ManyToOne(targetEntity="InfoProgresoPorcentaje")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="PROGRESO_PORCENTAJE_ID", referencedColumnName="ID_PROGRESO_PORCENTAJE")
* })
*/
    
private $progresoPorcentajeId;

/**
* @var InfoDetalle
*
* @ORM\ManyToOne(targetEntity="InfoDetalle")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="DETALLE_ID", referencedColumnName="ID_DETALLE")
* })
*/
    
private $detalleId;

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
* @var datetime $ipCreacion
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


/**
* @var string $comunicacionId
*
* @ORM\Column(name="COMUNICACION_ID", type="string", nullable=false)
*/
private $comunicacionId;

/**
* @var string $origen
*
* @ORM\Column(name="ORIGEN", type="string", nullable=false)
*/		
     		
private $origen;

/**
* @var integer $valorProgreso
*
* @ORM\Column(name="VALOR_PROGRESO", type="integer", nullable=false)
*/		
		
private $valorProgreso;

public function getId(){
	return $this->id; 
}

/**
* Get $horaTransaccion
*
* @return 
*/		
     		
public function getHoraTransaccion(){
	return $this->horaTransaccionTarea; 
}

/**
* Set $horaTransaccion
*
* @param string $horaTransaccion
*/
public function setHoraTransaccion($horaTransaccion)
{
        $this->horaTransaccionTarea = $horaTransaccion;
}

/*
* Get progresoPorcentajeId
*
* @return telconet\schemaBundle\Entity\InfoProgresoPorcentaje
*/    
         
public function getProgresoPorcentaje(){
  return $this->progresoPorcentajeId; 
}

/*
* Set progresoPorcentajeId
*
* @param telconet\schemaBundle\Entity\InfoProgresoPorcentaje $progresoProcentajeId
*/
public function setProgresoPorcentaje(\telconet\schemaBundle\Entity\InfoProgresoPorcentaje $progresoProcentajeId)
{
        $this->progresoPorcentajeId = $progresoProcentajeId;
}

/*
* Get $detalleId
*
* @return telconet\schemaBundle\Entity\InfoDetalle
*/    
         
public function getDetalleId(){
  return $this->detalleId; 
}

/*
* Set progresoPorcentajeId
*
* @param telconet\schemaBundle\Entity\InfoDetalle $detalleId
*/
public function setDetalleId(\telconet\schemaBundle\Entity\InfoDetalle $detalleId)
{
        $this->detalleId = $detalleId;
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
* @return 
*/		
     		
public function getIpCreacion(){
	return $this->ipCreacion; 
}

/**
* Set ipCreacion
*
* @param  $ipCreacion
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
* @return 
*/		
     		
public function getFeUltMod(){
	return $this->feUltMod; 
}

/**
* Set feUltMod
*
* @param  $feUltMod
*/
public function setFeUltMod($feUltMod)
{
        $this->feUltMod = $feUltMod;
}

/**
* Get idComunicacion
*
* @return 
*/
function getComunicacionId()
{
    return $this->comunicacionId;
}

/**
* Set idComunicacion
*
* @param  $idComunicacion
*/
function setComunicacionId($comunicacionId)
{
    $this->comunicacionId = $comunicacionId;
}

/**
 * Get the value of origen
 */ 
public function getOrigen()
{
return $this->origen;
}

/**
 * Set the value of origen
 *
 * @return  self
 */ 
public function setOrigen($origen)
{
$this->origen = $origen;

return $this;
}

/**
 * Get the value of valorProgreso
 */ 
public function getValorProgreso()
{
return $this->valorProgreso;
}

/**
 * Set the value of valorProgreso
 *
 * @return  self
 */ 
public function setValorProgreso($valorProgreso)
{
$this->valorProgreso = $valorProgreso;

return $this;
}

}
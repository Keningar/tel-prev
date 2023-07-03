<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoAgendaCupoCab
 *
 * @ORM\Table(name="INFO_AGENDA_CUPO_CAB")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoAgendaCupoCabRepository")
 */
class InfoAgendaCupoCab
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_AGENDA_CUPOS", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_AGENDA_CUPO_CAB", allocationSize=1, initialValue=1)
*/			
private $id;	


/**
* @var datetime $fechaPeriodo
*
* @ORM\Column(name="FECHA_PERIODO", type="datetime", nullable=false)
*/
private $fechaPeriodo;


/**
* @var integer $totalCupos
*
* @ORM\Column(name="TOTAL_CUPOS", type="integer", nullable=false)
*/		   		
private $totalCupos;


/**
* @var string $observacion
*
* @ORM\Column(name="OBSERVACION", type="string", nullable=true)
*/		
     		
private $observacion;


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
* @var string $ipCreacion
*
* @ORM\Column(name="IP_CREACION", type="string", nullable=false)
*/		
private $ipCreacion;


/**
* @var $jurisdiccionId
*
* @ORM\ManyToOne(targetEntity="AdmiJurisdiccion")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="JURISDICCION_ID", referencedColumnName="ID_JURISDICCION")
* })
*/	
private $jurisdiccionId;


/**
* @var plantillaHorarioId
*
* @ORM\ManyToOne(targetEntity="AdmiPlantillaHorarioCab")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="PLANTILLA_HORARIO_ID", referencedColumnName="ID_PLANTILLA_HORARIO")
* })
*/		
private $plantillaHorarioId;

/**
* @var datetime $feModifica
*
* @ORM\Column(name="FE_MODIFICA", type="datetime", nullable=true)
*/		
private $feModifica;

/**
* @var string $usrModifica
*
* @ORM\Column(name="USR_MODIFICA", type="string", nullable=true)
*/		
private $usrModifica;

/**
* @var string $estadoRegistro
*
* @ORM\Column(name="ESTADO_REGISTRO", type="string", nullable=false)
*/		
private $estadoRegistro;

/**
* @var string $empresaCod
*
* @ORM\Column(name="EMPRESA_COD", type="string", nullable=false)
*/		
private $empresaCod;


/**
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}


/**
* Get fechaPeriodo
*
* @return datetime $fechaPeriodo
*/		
     		
public function getFechaPeriodo(){
	return $this->fechaPeriodo; 
}


/**
* Set fechaPeriodo
*
* @param datetime $fechaPeriodo
*/
public function setFechaPeriodo($fechaPeriodo)
{
        $this->fechaPeriodo = $fechaPeriodo;
}


/**
* Get totalCupos
*
* @return integer $totalCupos
*/		  		
public function getTotalCupos() {
    return $this->totalCupos;
}


/**
* Set totalCupos
*
* @param integer $totalCupos
*/
public function setTotalCupos($totalCupos) {
    $this->totalCupos = $totalCupos;
}


/**
* Get observacion
*
* @return string $observacion
*/		
     		
public function getObservacion(){
	return $this->observacion; 
}

/**
* Set observacion
*
* @param string $observacion
*/
public function setObservacion($observacion)
{
        $this->observacion = $observacion;
}

/**
* Get feCreacion
*
* @return datetime $feCreacion
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
* @return string $usrCreacion
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
* Get ipCreacion
*
* @return string $ipCreacion
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
* Get jurisdiccionId
*
* @return \telconet\schemaBundle\Entity\AdmiJurisdiccion $jurisdiccionId
*/		
     		
public function getJurisdiccionId(){
	return $this->jurisdiccionId; 
}

/**
* Set jurisdiccionId
*
* @param \telconet\schemaBundle\Entity\AdmiJurisdiccion $jurisdiccionId
*/
public function setJurisdiccionId(\telconet\schemaBundle\Entity\AdmiJurisdiccion $jurisdiccionId)
{
        $this->jurisdiccionId = $jurisdiccionId;
}


/**
* Get plantillaHorarioId
*
* @return \telconet\schemaBundle\Entity\AdmiPlantillaHorarioCab $plantillaHorarioId
*/		
     		
public function getPlantillaHorarioId(){
	return $this->plantillaHorarioId; 
}

/**
* Set plantillaHorarioId
*
* @param telconet\schemaBundle\Entity\AdmiPlantillaHorarioCab $plantillaHorarioId
*/
public function setPlantillaHorarioId(\telconet\schemaBundle\Entity\AdmiPlantillaHorarioCab $plantillaHorarioId)
{
        $this->plantillaHorarioId = $plantillaHorarioId;
}

/**
* Get feModifica
*
* @return datetime $feModifica
*/		
     		
public function getFeModifica(){
	return $this->feModifica; 
}

/**
* Set feModifica
*
* @param datetime $feModifica
*/
public function setFeModifica($feModifica)
{
        $this->feModifica = $feModifica;
}

/**
* Get usrModifica
*
* @return string $usrModifica
*/		
     		
public function getUsrModifica(){
	return $this->usrModifica; 
}

/**
* Set usrModifica
*
* @param string $usrModifica
*/
public function setUsrModifica($usrModifica)
{
        $this->usrModifica = $usrModifica;
}


/**
* Get estadoRegistro
*
* @return string $estadoRegistro
*/		
     		
public function getEstadoRegistro(){
	return $this->estadoRegistro; 
}

/**
* Set estadoRegistro
*
* @param string $estadoRegistro
*/
public function setEstadoRegistro($estadoRegistro)
{
        $this->estadoRegistro = $estadoRegistro;
}

/**
* Get empresaCod
*
* @return string $empresaCod
*/		
     		
public function getEmpresaCod(){
	return $this->empresaCod; 
}

/**
* Set empresaCod
*
* @param string $empresaCod
*/
public function setEmpresaCod($empresaCod)
{
        $this->empresaCod = $empresaCod;
}

}

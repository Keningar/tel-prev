<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoAgendaCupoDet
 *
 * @ORM\Table(name="INFO_AGENDA_CUPO_DET")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoAgendaCupoDetRepository")
 */
class InfoAgendaCupoDet
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_AGENDA_CUPO_DET", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_AGENDA_CUPO_DET", allocationSize=1, initialValue=1)
*/			
private $id;	


/**
* @var agendaCupoId
*
* @ORM\ManyToOne(targetEntity="InfoAgendaCupoCab")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="AGENDA_CUPO_ID", referencedColumnName="ID_AGENDA_CUPOS")
* })
*/	
private $agendaCupoId;


/**
* @var integer $cuposWeb
*
* @ORM\Column(name="CUPOS_WEB", type="integer", nullable=false)
*/		   		
private $cuposWeb;


/**
* @var integer $cuposMovil
*
* @ORM\Column(name="CUPOS_MOVIL", type="integer", nullable=false)
*/		   		
private $cuposMovil;

/**
* @var integer $totalCupos
*
* @ORM\Column(name="TOTAL_CUPOS", type="integer", nullable=false)
*/		   		
private $totalCupos;


/**
* @var datetime $horaDesde
*
* @ORM\Column(name="HORA_DESDE", type="datetime", nullable=false)
*/		
private $horaDesde;


/**
* @var datetime $horaHasta
*
* @ORM\Column(name="HORA_HASTA", type="datetime", nullable=false)
*/		
private $horaHasta;


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
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}


/**
* Get agendaCupoId
*
* @return \telconet\schemaBundle\Entity\InfoAgendaCupoCab $agendaCupoId
*/		
     		
public function getAgendaCupoId(){
	return $this->agendaCupoId; 
}


/**
* Set agendaCupoId
*
* @param integer $agendaCupoId
*/
public function setAgendaCupoId(\telconet\schemaBundle\Entity\InfoAgendaCupoCab $agendaCupoId)
{
        $this->agendaCupoId = $agendaCupoId;
}

/**
* Get cuposWeb
*
* @return integer $cuposWeb
*/		  		
public function getCuposWeb() {
    return $this->cuposWeb;
}


/**
* Set cuposWeb
*
* @param integer $cuposWeb
*/
public function setCuposWeb($cuposWeb) {
    $this->cuposWeb = $cuposWeb;
}

/**
* Get cuposMovil
*
* @return integer $cuposMovil
*/		  		
public function getCuposMovil() {
    return $this->cuposMovil;
}


/**
* Set cuposMovil
*
* @param integer $cuposMovil
*/
public function setCuposMovil($cuposMovil) {
    $this->cuposMovil = $cuposMovil;
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
* Get horaDesde
*
* @return datetime $horaDesde
*/		
     		
public function getHoraDesde(){
	return $this->horaDesde; 
}

/**
* Set horaDesde
*
* @param datetime $horaDesde
*/
public function setHoraDesde($horaDesde)
{
        $this->horaDesde = $horaDesde;
}

/**
* Get horaHasta
*
* @return datetime $horaHasta
*/		
     		
public function getHoraHasta(){
	return $this->horaHasta; 
}

/**
* Set horaHasta
*
* @param datetime $horaHasta
*/
public function setHoraHasta($horaHasta)
{
        $this->horaHasta = $horaHasta;
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

}

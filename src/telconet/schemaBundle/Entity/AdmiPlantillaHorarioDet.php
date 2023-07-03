<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiPlantillaHorarioDet
 *
 * @ORM\Table(name="ADMI_PLANTILLA_HORARIO_DET")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiPlantillaHorarioDetRepository")
 */
class AdmiPlantillaHorarioDet
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_PLANTILLA_HORARIO_DET", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_PLANTILLA_HORARIO_DET", allocationSize=1, initialValue=1)
*/		
		
private $id;	


/**
* @var AdmiPlantillaHorarioCab
*
* @ORM\ManyToOne(targetEntity="AdmiPlantillaHorarioCab")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="PLANTILLA_HORARIO_ID", referencedColumnName="ID_PLANTILLA_HORARIO")
* })
*/
private $plantillaHorarioId;


/**
* @var datetime $horaInicio
*
* @ORM\Column(name="HORA_INICIO", type="datetime", nullable=false)
*/      
            
private $horaInicio;

/**
* @var datetime $horaFin
*
* @ORM\Column(name="HORA_FIN", type="datetime", nullable=false)
*/      
            
private $horaFin;


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
* @var string $almuerzo
*
* @ORM\Column(name="ALMUERZO", type="string", nullable=true)
*/		
     		

private $almuerzo;

/**
* @var integer $cupoWeb
*
* @ORM\Column(name="CUPO_WEB", type="integer", nullable=true)
*/	

private $cupoWeb;

/**
* @var integer $cupoMobile
*
* @ORM\Column(name="CUPO_MOBILE", type="integer", nullable=true)
*/	

private $cupoMobile;



/**
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Get plantillaHorarioId
*
* @return telconet\schemaBundle\Entity\PlantillaHorarioCab
*/
public function getPlantillaHorarioId()
{
    return $this->plantillaHorarioId; 
}

/**
* Set plantillaHorarioId
*
* @param telconet\schemaBundle\Entity\PlantillaHorarioCab $plantillaHorarioId
*/
public function setPlantillaHorarioId(\telconet\schemaBundle\Entity\AdmiPlantillaHorarioCab $plantillaHorarioId)
{
    $this->plantillaHorarioId = $plantillaHorarioId;
}


/**
* Get horaInicio
*
* @return datetime
*/      
            
public function getHoraInicio(){
    return $this->horaInicio; 
}

/**
* Set horaInicio
*
* @param datetime $horaInicio
*/
public function setHoraInicio($horaInicio)
{
        $this->horaInicio = $horaInicio;
}



/**
* Get horaFin
*
* @return datetime
*/      
            
public function getHoraFin(){
    return $this->horaFin; 
}

/**
* Set horaFin
*
* @param datetime $horaFin
*/
public function setHoraFin($horaFin)
{
        $this->horaFin = $horaFin;
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
* Get almuerzo
*
* @return string
*/		
     		
public function getAlmuerzo(){
	return $this->almuerzo; 
}

/**
* Set almuerzo
*
* @param string $almuerzo
*/
public function setAlmuerzo($almuerzo)
{
        $this->almuerzo = $almuerzo;
}

/**
* Get cupoWeb
*
* @return integer
*/      
            
public function getCupoWeb(){
    return $this->cupoWeb; 
}

/**
* Set cupoWeb
*
* @param integer $cupoWeb
*/
public function setCupoWeb($cupoWeb)
{
        $this->cupoWeb = $cupoWeb;
}

/**
* Get cupoMobile
*
* @return integer
*/      
            
public function getCupoMobile(){
    return $this->cupoMobile; 
}


/**
* Set cupoMobile
*
* @param integer $cupoMobile
*/
public function setCupoMobile($cupoMobile)
{
        $this->cupoMobile = $cupoMobile;
}

}

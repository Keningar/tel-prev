<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoParteAfectadaElemento
 *
 * @ORM\Table(name="INFO_PARTE_AFECTADA_ELEMENTO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoParteAfectadaElementoRepository")
 */
class InfoParteAfectadaElemento
{


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
* @var integer $id
*
* @ORM\Column(name="ID_PARTE_AFECTADA_ELEMENTO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_PARTE_AFECTADA_ELEMENTO", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var InfoCriterioAfectado
*
* @ORM\ManyToOne(targetEntity="InfoCriterioAfectado")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="CRITERIO_AFECTADO_ID", referencedColumnName="ID_CRITERIO_AFECTADO")
* })
*/
		
private $criterioAfectadoId;

/**
* @var integer $afectadoId
*
* @ORM\Column(name="AFECTADO_ID", type="integer", nullable=false)
*/		
     		
private $afectadoId;

/**
* @var string $afectadoNombre
*
* @ORM\Column(name="AFECTADO_NOMBRE", type="string", nullable=false)
*/		
     		
private $afectadoNombre;

/**
* @var string $afectadoDescripcion
*
* @ORM\Column(name="AFECTADO_DESCRIPCION", type="string", nullable=false)
*/		
     		
private $afectadoDescripcion;

/**
* @var datetime $feIniIncidencia
*
* @ORM\Column(name="FE_INI_INCIDENCIA", type="datetime", nullable=false)
*/		
     		
private $feIniIncidencia;

/**
* @var datetime $feFinIncidencia
*
* @ORM\Column(name="FE_FIN_INCIDENCIA", type="datetime", nullable=false)
*/		
     		
private $feFinIncidencia;

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
* Get criterioAfectadoId
*
* @return telconet\schemaBundle\Entity\InfoCriterioAfectado
*/		
     		
public function getCriterioAfectadoId(){
	return $this->criterioAfectadoId; 
}

/**
* Set criterioAfectadoId
*
* @param telconet\schemaBundle\Entity\InfoCriterioAfectado $criterioAfectadoId
*/
public function setCriterioAfectadoId(\telconet\schemaBundle\Entity\InfoCriterioAfectado $criterioAfectadoId)
{
        $this->criterioAfectadoId = $criterioAfectadoId;
}


/**
* Get afectadoId
*
* @return integer
*/		
     		
public function getAfectadoId(){
	return $this->afectadoId; 
}

/**
* Set afectadoId
*
* @param integer $afectadoId
*/
public function setAfectadoId($afectadoId)
{
        $this->afectadoId = $afectadoId;
}


/**
* Get afectadoNombre
*
* @return string
*/		
     		
public function getAfectadoNombre(){
	return $this->afectadoNombre; 
}

/**
* Set afectadoNombre
*
* @param string $afectadoNombre
*/
public function setAfectadoNombre($afectadoNombre)
{
        $this->afectadoNombre = $afectadoNombre;
}


/**
* Get afectadoDescripcion
*
* @return string
*/		
     		
public function getAfectadoDescripcion(){
	return $this->afectadoDescripcion; 
}

/**
* Set afectadoDescripcion
*
* @param string $afectadoDescripcion
*/
public function setAfectadoDescripcion($afectadoDescripcion)
{
        $this->afectadoDescripcion = $afectadoDescripcion;
}


/**
* Get feIniIncidencia
*
* @return datetime
*/		
     		
public function getFeIniIncidencia(){
	return $this->feIniIncidencia; 
}

/**
* Set feIniIncidencia
*
* @param datetime $feIniIncidencia
*/
public function setFeIniIncidencia($feIniIncidencia)
{
        $this->feIniIncidencia = $feIniIncidencia;
}


/**
* Get feFinIncidencia
*
* @return datetime
*/		
     		
public function getFeFinIncidencia(){
	return $this->feFinIncidencia; 
}

/**
* Set feFinIncidencia
*
* @param datetime $feFinIncidencia
*/
public function setFeFinIncidencia($feFinIncidencia)
{
        $this->feFinIncidencia = $feFinIncidencia;
}

}
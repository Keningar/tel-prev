<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiPlantillaHorarioCab
 *
 * @ORM\Table(name="ADMI_PLANTILLA_HORARIO_CAB")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiPlantillaHorarioCabRepository")
 */
class AdmiPlantillaHorarioCab
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_PLANTILLA_HORARIO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_PLANTILLA_HORARIO_CAB", allocationSize=1, initialValue=1)
*/		
		
private $id;	


/**
* @var string $empresaCod
*
* @ORM\Column(name="EMPRESA_COD", type="string", nullable=false)
*/
		

private $empresaCod;
/**
* @var string $descripcion
*
* @ORM\Column(name="DESCRIPCION", type="string", nullable=true)
*/		
     		
private $descripcion;


/**
* @var string $esDefault
*
* @ORM\Column(name="ES_DEFAULT", type="string", nullable=true)
*/		
     		
private $esDefault;


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
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=true)
*/		
     		

private $estado;

/**
* @var integer $jurisdiccionId
*
* @ORM\Column(name="JURISDICCION_ID", type="integer", nullable=true)
*/		
     		

private $jurisdiccionId;

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
* @var integer $cupoTotal
*
* @ORM\Column(name="CUPO_TOTAL", type="integer", nullable=true)
*/		
     		

private $cupoTotal;

/**
* Get id
*
* @return integer
*/

/**
* @var datetime $feUltGeneracion
*
* @ORM\Column(name="FE_ULT_GENERACION", type="datetime", nullable=true)
*/		
     		
private $feUltGeneracion;
     		
public function getId(){
	return $this->id; 
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


/**
* Get descripcion
*
* @return string
*/		  		
public function getDescripcion() {
    return $this->descripcion;
}


/**
* Set descripcion
*
* @param string $descripcion
*/
public function setDescripcion($descripcion) {
    $this->descripcion = $descripcion;
}


/**
* Get esDefault
*
* @return string
*/		
     		
public function getEsDefault(){
	return $this->esDefault; 
}

/**
* Set esDefault
*
* @param string $esDefault
*/
public function setEsDefault($esDefault)
{
        $this->esDefault = $esDefault;
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
* Get jurisdiccionId
*
* @return integer
*/		
     		
public function getJurisdiccionId(){
	return $this->jurisdiccionId; 
}

/**
* Set jurisdiccionId
*
* @param integer $jurisdiccionId
*/
public function setJurisdiccionId($jurisdiccionId)
{
        $this->jurisdiccionId = $jurisdiccionId;
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

/**
* Get cupoTotal
*
* @return integer
*/		
     		
public function getCupoTotal(){
	return $this->cupoTotal; 
}

/**
* Set cupoTotal
*
* @param integer $cupoTotal
*/
public function setCupoTotal($cupoTotal)
{
        $this->cupoTotal = $cupoTotal;
}

/**
* Get feUltGeneracion
*
* @return datetime
*/		
     		
public function getFeUltGeneracion(){
	return $this->feUltGeneracion; 
}

/**
* Set feUltGeneracion
*
* @param datetime $feUltGeneracion
*/
public function setFeUltGeneracion($feUltGeneracion)
{
        $this->feUltGeneracion = $feUltGeneracion;
}

}

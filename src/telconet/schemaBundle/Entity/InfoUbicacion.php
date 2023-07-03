<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoUbicacion
 *
 * @ORM\Table(name="INFO_UBICACION")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoUbicacionRepository")
 */
class InfoUbicacion
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_UBICACION", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_UBICACION", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var AdmiParroquia
*
* @ORM\ManyToOne(targetEntity="AdmiParroquia")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="PARROQUIA_ID", referencedColumnName="ID_PARROQUIA")
* })
*/
		
private $parroquiaId;

/**
* @var string $direccionUbicacion
*
* @ORM\Column(name="DIRECCION_UBICACION", type="string", nullable=false)
*/		
     		
private $direccionUbicacion;

/**
* @var float $longitudUbicacion
*
* @ORM\Column(name="LONGITUD_UBICACION", type="float", nullable=false)
*/		
     		
private $longitudUbicacion;

/**
* @var float $latitudUbicacion
*
* @ORM\Column(name="LATITUD_UBICACION", type="float", nullable=false)
*/		
     		
private $latitudUbicacion;

/**
* @var float $alturaSnm
*
* @ORM\Column(name="ALTURA_SNM", type="float", nullable=false)
*/		
     		
private $alturaSnm;

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
* @var integer $oficinaId
*
* @ORM\Column(name="OFICINA_ID", type="integer", nullable=true)
*/
		
private $oficinaId;

/**
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Get parroquiaId
*
* @return telconet\schemaBundle\Entity\AdmiParroquia
*/		
     		
public function getParroquiaId(){
	return $this->parroquiaId; 
}

/**
* Set parroquiaId
*
* @param telconet\schemaBundle\Entity\AdmiParroquia $parroquiaId
*/
public function setParroquiaId(\telconet\schemaBundle\Entity\AdmiParroquia $parroquiaId)
{
        $this->parroquiaId = $parroquiaId;
}


/**
* Get direccionUbicacion
*
* @return string
*/		
     		
public function getDireccionUbicacion(){
	return $this->direccionUbicacion; 
}

/**
* Set direccionUbicacion
*
* @param string $direccionUbicacion
*/
public function setDireccionUbicacion($direccionUbicacion)
{
        $this->direccionUbicacion = $direccionUbicacion;
}


/**
* Get longitudUbicacion
*
* @return 
*/		
     		
public function getLongitudUbicacion(){
	return $this->longitudUbicacion; 
}

/**
* Set longitudUbicacion
*
* @param  $longitudUbicacion
*/
public function setLongitudUbicacion($longitudUbicacion)
{
        $this->longitudUbicacion = $longitudUbicacion;
}


/**
* Get latitudUbicacion
*
* @return 
*/		
     		
public function getLatitudUbicacion(){
	return $this->latitudUbicacion; 
}

/**
* Set latitudUbicacion
*
* @param  $latitudUbicacion
*/
public function setLatitudUbicacion($latitudUbicacion)
{
        $this->latitudUbicacion = $latitudUbicacion;
}


/**
* Get alturaSnm
*
* @return 
*/		
     		
public function getAlturaSnm(){
	return $this->alturaSnm; 
}

/**
* Set alturaSnm
*
* @param  $alturaSnm
*/
public function setAlturaSnm($alturaSnm)
{
        $this->alturaSnm = $alturaSnm;
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
* Get oficinaId
*
* @return oficinaId
*/		
     		
public function getOficinaId()
{
	return $this->oficinaId; 
}

/**
* Set oficinaId
*
* @param  $oficinaId
*/
public function setOficinaId($oficinaId)
{
    $this->oficinaId = $oficinaId;
}

}
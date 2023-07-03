<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoConexion
 *
 * @ORM\Table(name="INFO_CONEXION")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoConexionRepository")
 */
class InfoConexion
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_CONEXION", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_CONEXION", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var integer $tramoAId
*
* @ORM\Column(name="TRAMO_A_ID", type="integer", nullable=false)
*/		
     		
private $tramoAId;

/**
* @var integer $tramoBId
*
* @ORM\Column(name="TRAMO_B_ID", type="integer", nullable=false)
*/		
     		
private $tramoBId;

/**
* @var AdmiTipoConexion
*
* @ORM\ManyToOne(targetEntity="AdmiTipoConexion")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="TIPO_CONEXION_ID", referencedColumnName="ID_TIPO_CONEXION")
* })
*/
		
private $tipoConexionId;

/**
* @var integer $tramoAHiloId
*
* @ORM\Column(name="TRAMO_A_HILO_ID", type="integer", nullable=true)
*/		
     		
private $tramoAHiloId;

/**
* @var integer $tramoBHiloId
*
* @ORM\Column(name="TRAMO_B_HILO_ID", type="integer", nullable=true)
*/		
     		
private $tramoBHiloId;

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
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Get tramoAId
*
* @return integer
*/		
     		
public function getTramoAId(){
	return $this->tramoAId; 
}

/**
* Set tramoAId
*
* @param integer $tramoAId
*/
public function setTramoAId($tramoAId)
{
        $this->tramoAId = $tramoAId;
}


/**
* Get tramoBId
*
* @return integer
*/		
     		
public function getTramoBId(){
	return $this->tramoBId; 
}

/**
* Set tramoBId
*
* @param integer $tramoBId
*/
public function setTramoBId($tramoBId)
{
        $this->tramoBId = $tramoBId;
}


/**
* Get tipoConexionId
*
* @return telconet\schemaBundle\Entity\AdmiTipoConexion
*/		
     		
public function getTipoConexionId(){
	return $this->tipoConexionId; 
}

/**
* Set tipoConexionId
*
* @param telconet\schemaBundle\Entity\AdmiTipoConexion $tipoConexionId
*/
public function setTipoConexionId(\telconet\schemaBundle\Entity\AdmiTipoConexion $tipoConexionId)
{
        $this->tipoConexionId = $tipoConexionId;
}


/**
* Get tramoAHiloId
*
* @return integer
*/		
     		
public function getTramoAHiloId(){
	return $this->tramoAHiloId; 
}

/**
* Set tramoAHiloId
*
* @param integer $tramoAHiloId
*/
public function setTramoAHiloId($tramoAHiloId)
{
        $this->tramoAHiloId = $tramoAHiloId;
}


/**
* Get tramoBHiloId
*
* @return integer
*/		
     		
public function getTramoBHiloId(){
	return $this->tramoBHiloId; 
}

/**
* Set tramoBHiloId
*
* @param integer $tramoBHiloId
*/
public function setTramoBHiloId($tramoBHiloId)
{
        $this->tramoBHiloId = $tramoBHiloId;
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

}
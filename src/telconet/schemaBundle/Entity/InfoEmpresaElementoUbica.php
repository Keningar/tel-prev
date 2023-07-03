<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoEmpresaElementoUbica
 *
 * @ORM\Table(name="INFO_EMPRESA_ELEMENTO_UBICA")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoEmpresaElementoUbicaRepository")
 */
class InfoEmpresaElementoUbica
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_EMPRESA_ELEMENTO_UBICACION", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_EMPRESA_ELEMENTO_UBI", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var string $empresaCod
*
* @ORM\Column(name="EMPRESA_COD", type="string", nullable=false)
*/
		
private $empresaCod;

/**
* @var InfoElemento
*
* @ORM\ManyToOne(targetEntity="InfoElemento")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="ELEMENTO_ID", referencedColumnName="ID_ELEMENTO")
* })
*/
		
private $elementoId;

/**
* @var InfoUbicacion
*
* @ORM\ManyToOne(targetEntity="InfoUbicacion")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="UBICACION_ID", referencedColumnName="ID_UBICACION")
* })
*/
		
private $ubicacionId;

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
* Get empresaCod
*
* @return string
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
* Get elementoId
*
* @return telconet\schemaBundle\Entity\InfoElemento
*/		
     		
public function getElementoId(){
	return $this->elementoId; 
}

/**
* Set elementoId
*
* @param telconet\schemaBundle\Entity\InfoElemento $elementoId
*/
public function setElementoId(\telconet\schemaBundle\Entity\InfoElemento $elementoId)
{
        $this->elementoId = $elementoId;
}


/**
* Get ubicacionId
*
* @return telconet\schemaBundle\Entity\InfoUbicacion
*/		
     		
public function getUbicacionId(){
	return $this->ubicacionId; 
}

/**
* Set ubicacionId
*
* @param telconet\schemaBundle\Entity\InfoUbicacion $ubicacionId
*/
public function setUbicacionId(\telconet\schemaBundle\Entity\InfoUbicacion $ubicacionId)
{
        $this->ubicacionId = $ubicacionId;
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
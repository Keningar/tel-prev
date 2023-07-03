<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoCasosMovil
 *
 * @ORM\Table(name="INFO_CASOS_MOVIL")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoCasosMovilRepository")
 */
class InfoCasosMovil
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_CASOS_MOVIL", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_CASOS_MOVIL", allocationSize=1, initialValue=1)
*/		
		
private $id;	


/**
* @var integer $casoId
*
* @ORM\Column(name="CASO_ID", type="integer", nullable=false)
*/		
     		
private $casoId;

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
* Get id
*
* @return integer
*/		

public function getId()
{
	return $this->id; 
}

/**
* Get casoId
*
* @return string
*/		
     		
public function getCasoId()
{
	return $this->casoId; 
}

/**
* Set codigoDispositivo
*
* @param integer $strCasoId
*/
public function setCasoId($strCasoId)
{
    $this->casoId = $strCasoId;
}
   		
public function getUsrCreacion()
{
	return $this->usrCreacion; 
}

/**
* Set usrCreacion
*
* @param string $strUsrCreacion
*/
public function setUsrCreacion($strUsrCreacion)
{
    $this->usrCreacion = $strUsrCreacion;
}

/**
* Get feCreacion
*
* @return 
*/		
     		
public function getFeCreacion()
{
	return $this->feCreacion; 
}

/**
* Set feCreacion
*
* @param  $strFeCreacion
*/
public function setFeCreacion($strFeCreacion)
{
    $this->feCreacion = $strFeCreacion;
}
  
}

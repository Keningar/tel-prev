<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiProcesoEmpresa
 *
 * @ORM\Table(name="ADMI_PROCESO_EMPRESA")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiProcesoEmpresaRepository")
 */
class AdmiProcesoEmpresa
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_PROCESO_EMPRESA", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_PROCESO_EMPRESA", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var AdmiProceso
*
* @ORM\ManyToOne(targetEntity="AdmiProceso")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="PROCESO_ID", referencedColumnName="ID_PROCESO")
* })
*/ 	
private $procesoId;


/**
* @var string $empresaCod
*
* @ORM\Column(name="EMPRESA_COD", type="string", nullable=false)
*/
		
private $empresaCod;

		

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
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
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



////////////////////////////////////////////////////////////////////////////

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



/////////////////////////////////////////////////////////////////////////////////

/**
* Get procesoId
*
* @return telconet\schemaBundle\Entity\AdmiProceso
*/		
     		
public function getProcesoId(){
	return $this->procesoId; 
}

/**
* Set procesoId
*
* @param telconet\schemaBundle\Entity\AdmiProceso $procesoId
*/
public function setProcesoId(\telconet\schemaBundle\Entity\AdmiProceso $procesoId)
{
        $this->procesoId = $procesoId;
}





}
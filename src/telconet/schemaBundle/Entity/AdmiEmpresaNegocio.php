<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiEmpresaNegocio
 *
 * @ORM\Table(name="ADMI_EMPRESA_NEGOCIO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiEmpresaNegocioRepository")
 */
class AdmiEmpresaNegocio
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_EMP_NEGOCIO_REF", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_EMPRESA_NEGOCIO", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var AdmiTipoNegocio
*
* @ORM\ManyToOne(targetEntity="AdmiTipoNegocio")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="TIPO_NEGOCIO_ID", referencedColumnName="ID_TIPO_NEGOCIO")
* })
*/
		
private $tipoNegocioId;

/**
* @var string $empresaId
*
* @ORM\Column(name="EMPRESA_ID", type="string", nullable=true)
*/		
     		
private $empresaId;

/**
* @var DATE $feCreacion
*
* @ORM\Column(name="FE_CREACION", type="DATE", nullable=true)
*/		
     		
private $feCreacion;

/**
* @var DATE $feUltMod
*
* @ORM\Column(name="FE_ULT_MOD", type="DATE", nullable=true)
*/		
     		
private $feUltMod;

/**
* @var string $usrCreacion
*
* @ORM\Column(name="USR_CREACION", type="string", nullable=true)
*/		
     		
private $usrCreacion;

/**
* @var string $usrUltMod
*
* @ORM\Column(name="USR_ULT_MOD", type="string", nullable=true)
*/		
     		
private $usrUltMod;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=true)
*/		
     		
private $estado;

/**
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Get tipoNegocioId
*
* @return telconet\schemaBundle\Entity\AdmiTipoNegocio
*/		
     		
public function getTipoNegocioId(){
	return $this->tipoNegocioId; 
}

/**
* Set tipoNegocioId
*
* @param telconet\schemaBundle\Entity\AdmiTipoNegocio $tipoNegocioId
*/
public function setTipoNegocioId(\telconet\schemaBundle\Entity\AdmiTipoNegocio $tipoNegocioId)
{
        $this->tipoNegocioId = $tipoNegocioId;
}


/**
* Get empresaId
*
* @return string
*/		
     		
public function getEmpresaId(){
	return $this->empresaId; 
}

/**
* Set empresaId
*
* @param string $empresaId
*/
public function setEmpresaId($empresaId)
{
        $this->empresaId = $empresaId;
}


/**
* Get feCreacion
*
* @return 
*/		
     		
public function getFeCreacion(){
	return $this->feCreacion; 
}

/**
* Set feCreacion
*
* @param  $feCreacion
*/
public function setFeCreacion($feCreacion)
{
        $this->feCreacion = $feCreacion;
}


/**
* Get feUltMod
*
* @return 
*/		
     		
public function getFeUltMod(){
	return $this->feUltMod; 
}

/**
* Set feUltMod
*
* @param  $feUltMod
*/
public function setFeUltMod($feUltMod)
{
        $this->feUltMod = $feUltMod;
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
* Get usrUltMod
*
* @return string
*/		
     		
public function getUsrUltMod(){
	return $this->usrUltMod; 
}

/**
* Set usrUltMod
*
* @param string $usrUltMod
*/
public function setUsrUltMod($usrUltMod)
{
        $this->usrUltMod = $usrUltMod;
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

}
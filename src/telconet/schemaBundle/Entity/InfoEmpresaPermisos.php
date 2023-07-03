<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoEmpresaPermisos
 *
 * @ORM\Table(name="INFO_EMPRESA_PERMISOS")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoEmpresaPermisosRepository")
 */
class InfoEmpresaPermisos
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_EMPRESA_PERMISOS", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_EMPRESA_PERMISOS", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var InfoEmpresaGrupo
*
* @ORM\ManyToOne(targetEntity="InfoEmpresaGrupo")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="EMPRESA_COD", referencedColumnName="COD_EMPRESA")
* })
*/
		
private $empresaCod;

/**
* @var string $tipoPermiso
*
* @ORM\Column(name="TIPO_PERMISO", type="string", nullable=true)
*/		
     		
private $tipoPermiso;

/**
* @var string $tienePermiso
*
* @ORM\Column(name="TIENE_PERMISO", type="string", nullable=false)
*/		
     		
private $tienePermiso;

/**
* @var string $observacion
*
* @ORM\Column(name="OBSERVACION", type="string", nullable=true)
*/		
     		
private $observacion;

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
* @ORM\Column(name="IP_CREACION", type="string", nullable=true)
*/		
     		
private $ipCreacion;

/**
* @var datetime $fechaVigencia
*
* @ORM\Column(name="FECHA_VIGENCIA", type="datetime", nullable=true)
*/		
     		
private $fechaVigencia;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=false)
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
* Get empresaCod
*
* @return telconet\schemaBundle\Entity\InfoEmpresaGrupo
*/		
     		
public function getEmpresaCod(){
	return $this->empresaCod; 
}

/**
* Set empresaCod
*
* @param telconet\schemaBundle\Entity\InfoEmpresaGrupo $empresaCod
*/
public function setEmpresaCod(\telconet\schemaBundle\Entity\InfoEmpresaGrupo $empresaCod)
{
        $this->empresaCod = $empresaCod;
}


/**
* Get tipoPermiso
*
* @return string
*/		
     		
public function getTipoPermiso(){
	return $this->tipoPermiso; 
}

/**
* Set tipoPermiso
*
* @param string $tipoPermiso
*/
public function setTipoPermiso($tipoPermiso)
{
        $this->tipoPermiso = $tipoPermiso;
}


/**
* Get tienePermiso
*
* @return string
*/		
     		
public function getTienePermiso(){
	return $this->tienePermiso; 
}

/**
* Set tienePermiso
*
* @param string $tienePermiso
*/
public function setTienePermiso($tienePermiso)
{
        $this->tienePermiso = $tienePermiso;
}


/**
* Get observacion
*
* @return string
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
* Get fechaVigencia
*
* @return datetime
*/		
     		
public function getFechaVigencia(){
	return $this->fechaVigencia; 
}

/**
* Set fechaVigencia
*
* @param datetime $fechaVigencia
*/
public function setFechaVigencia($fechaVigencia)
{
        $this->fechaVigencia = $fechaVigencia;
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
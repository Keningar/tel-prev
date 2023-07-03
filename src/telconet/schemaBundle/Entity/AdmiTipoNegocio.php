<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiTipoNegocio
 *
 * @ORM\Table(name="ADMI_TIPO_NEGOCIO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiTipoNegocioRepository")
 */
class AdmiTipoNegocio
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_TIPO_NEGOCIO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_TIPO_NEGOCIO", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var string $codigoTipoNegocio
*
* @ORM\Column(name="CODIGO_TIPO_NEGOCIO", type="string", nullable=false)
*/		
     		
private $codigoTipoNegocio;

/**
* @var string $nombreTipoNegocio
*
* @ORM\Column(name="NOMBRE_TIPO_NEGOCIO", type="string", nullable=false)
*/		
     		
private $nombreTipoNegocio;

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
* @var datetime $feUltMod
*
* @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=true)
*/		
     		
private $feUltMod;

/**
* @var string $usrUltMod
*
* @ORM\Column(name="USR_ULT_MOD", type="string", nullable=true)
*/		
     		
private $usrUltMod;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=false)
*/		
     		
private $estado;


/**
* @var string $empresaCod
*
* @ORM\Column(name="EMPRESA_COD", type="string", nullable=false)
*/		
     		
private $empresaCod;

/**
* @var string $grupoNegocio
*
* @ORM\Column(name="GRUPO_NEGOCIO", type="string", nullable=false)
*/		
     		
private $grupoNegocio;

/**
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Get codigoTipoNegocio
*
* @return string
*/		
     		
public function getCodigoTipoNegocio(){
	return $this->codigoTipoNegocio; 
}

/**
* Set codigoTipoNegocio
*
* @param string $codigoTipoNegocio
*/
public function setCodigoTipoNegocio($codigoTipoNegocio)
{
        $this->codigoTipoNegocio = $codigoTipoNegocio;
}


/**
* Get nombreTipoNegocio
*
* @return string
*/		
     		
public function getNombreTipoNegocio(){
	return $this->nombreTipoNegocio; 
}

/**
* Set nombreTipoNegocio
*
* @param string $nombreTipoNegocio
*/
public function setNombreTipoNegocio($nombreTipoNegocio)
{
        $this->nombreTipoNegocio = $nombreTipoNegocio;
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
* Get feUltMod
*
* @return datetime
*/		
     		
public function getFeUltMod(){
	return $this->feUltMod; 
}

/**
* Set feUltMod
*
* @param datetime $feUltMod
*/
public function setFeUltMod($feUltMod)
{
        $this->feUltMod = $feUltMod;
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

public function __toString() {
    return $this->nombreTipoNegocio;

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
* Set grupoNegocio
*
* @param string $grupoNegocio
*/
public function setGrupoNegocio($grupoNegocio)
{
        $this->grupoNegocio = $grupoNegocio;
}

/**
* Get grupoNegocio
*
* @return string
*/		
     		
public function getGrupoNegocio(){
	return $this->grupoNegocio; 
}

}

<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM; 

/**
 * telconet\schemaBundle\Entity\AdmiTipoDocumentoGeneral
 *
 * @ORM\Table(name="ADMI_TIPO_DOCUMENTO_GENERAL")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiTipoDocumentoGeneralRepository")
 */
class AdmiTipoDocumentoGeneral
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_TIPO_DOCUMENTO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_TIPO_DOCUMENT_GENERAL", allocationSize=1, initialValue=1)
*/		
		
private $id;	

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=true)
*/		
     		
private $estado;

/**
* @var string $codigoTipoDocumento
*
* @ORM\Column(name="CODIGO_TIPO_DOCUMENTO", type="string", nullable=true)
*/		
     		
private $codigoTipoDocumento;

/**
* @var string $descripcionTipoDocumento
*
* @ORM\Column(name="DESCRIPCION_TIPO_DOCUMENTO", type="string", nullable=true)
*/		
     		
private $descripcionTipoDocumento;

/**
* @var DATE $feCreacion
*
* @ORM\Column(name="FE_CREACION", type="datetime", nullable=true)
*/		
     		
private $feCreacion;

/**
* @var DATE $feUltMod
*
* @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=true)
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
* @var string $ipCreacion
*
* @ORM\Column(name="IP_CREACION", type="string", nullable=true)
*/		
     		
private $ipCreacion;

/**
* @var string $visible
*
* @ORM\Column(name="VISIBLE", type="string", nullable=false)
*/		
     		
private $visible;


/**
* @var string $visibleEnPersona
*
* @ORM\Column(name="PERSONA", type="string", nullable=false)
*/		
     		
private $visibleEnPersona;

/**
* @var string $visibleEnElemento
*
* @ORM\Column(name="ELEMENTO", type="string", nullable=false)
*/		
     		
private $visibleEnElemento;


/**
* @var string $mostrarApp
*
* @ORM\Column(name="MOSTRAR_APP", type="string", nullable=true)
*/		
     		
private $mostrarApp;


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
* Get codigoTipoDocumento
*
* @return string
*/		
     		
public function getCodigoTipoDocumento(){
	return $this->codigoTipoDocumento; 
}

/**
* Set codigoTipoDocumento
*
* @param string $codigoTipoDocumento
*/
public function setCodigoTipoDocumento($codigoTipoDocumento)
{
        $this->codigoTipoDocumento = $codigoTipoDocumento;
}

/**
* Get descripcionTipoDocumento
*
* @return string
*/		
     		
public function getDescripcionTipoDocumento(){
	return $this->descripcionTipoDocumento; 
}

/**
* Set descripcionTipoDocumento
*
* @param string $descripcionTipoDocumento
*/
public function setDescripcionTipoDocumento($descripcionTipoDocumento)
{
        $this->descripcionTipoDocumento = $descripcionTipoDocumento;
}

/**
* Get visible
*
* @return string
*/		
     		
public function getVisible(){
	return $this->visible; 
}

/**
* Set visible
*
* @param string $visible
*/
public function setVisible($visible)
{
        $this->visible = $visible;
}




/**
* Get visibleEnPersona
*
* @return string
*/		
     		
public function getVisibleEnPersona(){
	return $this->visibleEnPersona; 
}

/**
* Set visibleEnPersona
*
* @param string $visibleEnPersona
*/
public function setVisibleEnPersona($visibleEnPersona)
{
        $this->visibleEnPersona = $visibleEnPersona;
}


/**
* Get visibleEnElemento
*
* @return string
*/		
     		
public function getVisibleEnElemento(){
	return $this->visibleEnElemento; 
}

/**
* Set visibleEnElemento
*
* @param string $visibleEnElemento
*/
public function setVisibleEnElemento($visibleEnElemento)
{
        $this->visibleEnElemento = $visibleEnElemento;
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
* Get mostrarApp
*
* @return string
*/		
     		
public function getMostrarApp(){
	return $this->mostrarApp; 
}

/**
* Set mostrarApp
*
* @param string $strMostrarApp
*/
public function setMostrarApp($strMostrarApp)
{
        $this->mostrarApp = $strMostrarApp;
}

public function __toString()
{
        return $this->descripcionTipoDocumento;
}

}
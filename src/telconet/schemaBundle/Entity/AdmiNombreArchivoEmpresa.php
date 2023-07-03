<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiNombreArchivoEmpresa
 *
 * @ORM\Table(name="ADMI_NOMBRE_ARCHIVO_EMPRESA")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiNombreArchivoEmpresaRepository")
 */
class AdmiNombreArchivoEmpresa
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_NOMBRE_ARCHIVO_EMPRESA", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ANAE", allocationSize=1, initialValue=1)
*/		
		
private $id;	

/**
* @var string $nombreArchivoFormato
*
* @ORM\Column(name="NOMBRE_ARCHIVO_FORMATO", type="string", nullable=true)
*/		
     		
private $nombreArchivoFormato;


/**
* @var string $separadorColumna
*
* @ORM\Column(name="SEPARADOR_COLUMNA", type="string", nullable=true)
*/		
     		
private $separadorColumna;

/**
* @var string $tipoArchivoFormato
*
* @ORM\Column(name="TIPO_ARCHIVO_FORMATO", type="string", nullable=true)
*/		
     		
private $tipoArchivoFormato;

/**
* @var string $consultarPor
*
* @ORM\Column(name="CONSULTAR_POR", type="string", nullable=true)
*/		
     		
private $consultarPor;

/**
* @var integer $empresaCod
*
* @ORM\Column(name="EMPRESA_COD", type="integer", nullable=true)
*/		
     		
private $empresaCod;

/**
* @var integer $bancoTipoCuentaId
*
* @ORM\Column(name="BANCO_TIPO_CUENTA_ID", type="integer", nullable=true)
*/		
     		
private $bancoTipoCuentaId;


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
* @var string $usrUltMod
*
* @ORM\Column(name="USR_ULT_MOD", type="string", nullable=true)
*/		
     		
private $usrUltMod;

/**
* @var datetime $feUltMod
*
* @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=true)
*/		
     		
private $feUltMod;

/**
* Get id
*
* @return integer
*/
public function getId() {
    return $this->id;
}
/**
* Get nombreArchivoFormato
*
* @return string
*/
public function getNombreArchivoFormato() {
    return $this->nombreArchivoFormato;
}
/**
* Set nombreArchivoFormato
*
* @param string $nombreArchivoFormato
*/
public function setNombreArchivoFormato($nombreArchivoFormato) {
    $this->nombreArchivoFormato = $nombreArchivoFormato;
}
/**
* Get separadorColumna
*
* @return string
*/
public function getSeparadorColumna() {
    return $this->separadorColumna;
}
/**
* Set separadorColumna
*
* @param string $separadorColumna
*/
public function setSeparadorColumna($separadorColumna) {
    $this->separadorColumna = $separadorColumna;
}
/**
* Get tipoArchivoFormato
*
* @return string
*/
public function getTipoArchivoFormato() {
    return $this->tipoArchivoFormato;
}
/**
* Set tipoArchivoFormato
*
* @param string $tipoArchivoFormato
*/
public function setTipoArchivoFormato($tipoArchivoFormato) {
    $this->tipoArchivoFormato = $tipoArchivoFormato;
}
/**
* Get consultarPor
*
* @return string
*/
public function getConsultarPor() {
    return $this->consultarPor;
}
/**
* Set consultarPor
*
* @param string $consultarPor
*/
public function setConsultarPor($consultarPor) {
    $this->consultarPor = $consultarPor;
}

/**
* Get empresaCod
*
* @return integer
*/
public function getEmpresaCod() {
    return $this->empresaCod;
}
/**
* Set empresaCod
*
* @param integer $empresaCod
*/
public function setEmpresaCod($empresaCod) {
    $this->empresaCod = $empresaCod;
}
/**
* Get bancoTipoCuentaId
*
* @return integer
*/
public function getBancoTipoCuentaId() {
    return $this->bancoTipoCuentaId;
}
/**
* Set bancoTipoCuentaId
*
* @param integer $bancoTipoCuentaId
*/
public function setBancoTipoCuentaId($bancoTipoCuentaId) {
    $this->bancoTipoCuentaId = $bancoTipoCuentaId;
}
/**
* Get usrCreacion
*
* @return string
*/
public function getUsrCreacion() {
    return $this->usrCreacion;
}
/**
* Set usrCreacion
*
* @param string $usrCreacion
*/
public function setUsrCreacion($usrCreacion) {
    $this->usrCreacion = $usrCreacion;
}
/**
* Get feCreacion
*
* @return string
*/
public function getFeCreacion() {
    return $this->feCreacion;
}
/**
* Set feCreacion
*
* @param string $feCreacion
*/
public function setFeCreacion($feCreacion) {
    $this->feCreacion = $feCreacion;
}
/**
* Get usrUltMod
*
* @return string
*/
public function getUsrUltMod() {
    return $this->usrUltMod;
}
/**
* Set usrUltMod
*
* @param string $usrUltMod
*/
public function setUsrUltMod($usrUltMod) {
    $this->usrUltMod = $usrUltMod;
}
/**
* Get feUltMod
*
* @return string
*/
public function getFeUltMod() {
    return $this->feUltMod;
}
/**
* Set feUltMod
*
* @param string $feUltMod
*/
public function setFeUltMod($feUltMod) {
    $this->feUltMod = $feUltMod;
}


public function __toString()
{
    return $this->bancoId->getDescripcionBanco();
}

}

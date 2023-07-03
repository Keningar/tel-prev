<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiFormatoDebito
 *
 * @ORM\Table(name="ADMI_FORMATO_DEBITO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiFormatoDebitoRepository")
 */
class AdmiFormatoDebito
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_FORMATO_DEBITO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_FORMATO_DEBITO", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var integer $bancoTipoCuentaId
*
* @ORM\Column(name="BANCO_TIPO_CUENTA_ID", type="integer", nullable=true)
*/
		
private $bancoTipoCuentaId;


/**
* @var string $descripcion
*
* @ORM\Column(name="DESCRIPCION", type="string", nullable=true)
*/		
     		
private $descripcion;


/**
* @var string $tipoCampo
*
* @ORM\Column(name="TIPO_CAMPO", type="string", nullable=true)
*/		
     		
private $tipoCampo;


/**
* @var string $contenido
*
* @ORM\Column(name="CONTENIDO", type="string", nullable=true)
*/		
     		
private $contenido;


/**
* @var integer $longitud
*
* @ORM\Column(name="LONGITUD", type="integer", nullable=true)
*/		
     		
private $longitud;

/**
* @var string $caracterRelleno
*
* @ORM\Column(name="CARACTER_RELLENO", type="string", nullable=true)
*/		
     		
private $caracterRelleno;


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
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=false)
*/		
     		
private $estado;

/**
* @var string $orientacionCaracterRelleno
*
* @ORM\Column(name="ORIENTACION_CARACTER_RELLENO", type="string", nullable=true)
*/		
     		
private $orientacionCaracterRelleno;

/**
* @var string $tipoDato
*
* @ORM\Column(name="TIPO_DATO", type="string", nullable=false)
*/		
     		
private $tipoDato;

/**
* @var AdmiVariableFormatoDebito
*
* @ORM\ManyToOne(targetEntity="AdmiVariableFormatoDebito")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="VARIABLE_FORMATO_ID", referencedColumnName="ID_VARIABLE_FORMATO")
* })
*/
		
private $variableFormatoId;

/**
* @var string $requiereValidacion
*
* @ORM\Column(name="REQUIERE_VALIDACION", type="string", nullable=false)
*/		
     		
private $requiereValidacion;

/**
* @var string $posicion
*
* @ORM\Column(name="POSICION", type="integer", nullable=false)
*/		
     		
private $posicion;

/**
* @var integer $empresaCod
*
* @ORM\Column(name="EMPRESA_COD", type="integer", nullable=true)
*/		
     		
private $empresaCod;

public function getEmpresaCod() {
    return $this->empresaCod;
}

public function setEmpresaCod($empresaCod) {
    $this->empresaCod = $empresaCod;
}


/**
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Get bancoTipoCuentaId
*
* @return integer
*/		
     		
public function getBancoTipoCuentaId(){
	return $this->bancoTipoCuentaId; 
}

/**
* Set bancoTipoCuentaId
*
* @param integer $bancoTipoCuentaId
*/
public function setBancoTipoCuentaId($bancoTipoCuentaId)
{
        $this->bancoTipoCuentaId = $bancoTipoCuentaId;
}


/**
* Get descripcion
*
* @return string
*/		
     		
public function getDescripcion(){
	return $this->descripcion; 
}

/**
* Set descripcion
*
* @param string $descripcion
*/
public function setDescripcion($descripcion)
{
        $this->descripcion = $descripcion;
}


/**
* Get tipoCampo
*
* @return string
*/		
     		
public function getTipoCampo(){
	return $this->tipoCampo; 
}

/**
* Set tipoCampo
*
* @param string $tipoCampo
*/
public function setTipoCampo($tipoCampo)
{
        $this->tipoCampo = $tipoCampo;
}


/**
* Get contenido
*
* @return string
*/		
     		
public function getContenido(){
	return $this->contenido; 
}

/**
* Set contenido
*
* @param string $contenido
*/
public function setContenido($contenido)
{
        $this->contenido = $contenido;
}

/**
* Get longitud
*
* @return integer
*/		
     		
public function getLongitud(){
	return $this->longitud; 
}

/**
* Set longitud
*
* @param integer $longitud
*/
public function setLongitud($longitud)
{
        $this->longitud = $longitud;
}

/**
* Get caracterRelleno
*
* @return string
*/		
     		
public function getCaracterRelleno(){
	return $this->caracterRelleno; 
}

/**
* Set caracterRelleno
*
* @param string $caracterRelleno
*/
public function setCaracterRelleno($caracterRelleno)
{
        $this->caracterRelleno = $caracterRelleno;
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
* Get orientacionCaracterRelleno
*
* @return string
*/		
     		
public function getOrientacionCaracterRelleno(){
	return $this->orientacionCaracterRelleno; 
}

/**
* Set orientacionCaracterRelleno
*
* @param string $orientacionCaracterRelleno
*/
public function setOrientacionCaracterRelleno($orientacionCaracterRelleno)
{
        $this->orientacionCaracterRelleno = $orientacionCaracterRelleno;
}


/**
* Get tipoDato
*
* @return string
*/		
     		
public function getTipoDato(){
	return $this->tipoDato; 
}

/**
* Set tipoDato
*
* @param string $tipoDato
*/
public function setTipoDato($tipoDato)
{
        $this->tipoDato = $tipoDato;
}


/**
* Get variableFormatoId
*
* @return telconet\schemaBundle\Entity\AdmiVariableFormatoDebito
*/		
     		
public function getVariableFormatoId(){
	return $this->variableFormatoId; 
}

/**
* Set variableFormatoId
*
* @param telconet\schemaBundle\Entity\AdmiVariableFormatoDebito $variableFormatoId
*/
public function setVariableFormatoId(\telconet\schemaBundle\Entity\AdmiVariableFormatoDebito $variableFormatoId)
{
        $this->variableFormatoId = $variableFormatoId;
}

/**
* Get posicion
*
* @return integer
*/		
     		
public function getPosicion(){
	return $this->posicion; 
}

/**
* Set posicion
*
* @param integer $posicion
*/
public function setPosicion($posicion)
{
        $this->posicion = $posicion;
}

/**
* Get requiereValidacion
*
* @return string
*/		
     		
public function getRequiereValidacion(){
	return $this->requiereValidacion; 
}

/**
* Set requiereValidacion
*
* @param string $requiereValidacion
*/
public function setRequiereValidacion($requiereValidacion)
{
        $this->requiereValidacion = $requiereValidacion;
}


}

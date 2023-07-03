<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoReasignacionCuadrilla
 *
 * @ORM\Table(name="INFO_REASIGNACION_CUADRILLA")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoEventoRepository")
 */
class InfoReasignacionCuadrilla
{


/**
* @var integer $idReasignacionCuadrilla
*
* @ORM\Column(name="ID_REASIGNACION_CUADRILLA", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_REASIG_CUADRILLA", allocationSize=1, initialValue=1)
*/		
		
private $idReasignacionCuadrilla;	
	
/**
* @var string $idDetalle
*
* @ORM\Column(name=" ID_DETALLE", type="integer", nullable=false)
*/		
     		
private $idDetalle;

/**
* @var string $idEmpresaRol
*
* @ORM\Column(name=" ID_EMPRESA_ROL", type="integer", nullable=false)
*/
    
private $idEmpresaRol;


/**
* @var string $userLogin
*
* @ORM\Column(name="USER_LOGIN", type="integer", nullable=false)
*/		
     		
private $userLogin;

/**
* @var string $idDepartamentoDestino
*
* @ORM\Column(name="ID_DEPARTAMENTO_DESTINO", type="integer", nullable=false)
*/		
     		
private $idDepartamentoDestino;

/**
* @var string $idDepartamentoDestino
*
* @ORM\Column(name="ID_PERSONA", type="integer", nullable=false)
*/		
     		
private $idDPersona;

/**
* @var string $idDepartamentoDestino
*
* @ORM\Column(name="NOMBRE_COMPLETO", type="integer", nullable=false)
*/		
     		
private $nombreCompleto;

/**
* @var string $usrCreacion
*
* @ORM\Column(name="USR_CREACION", type="string", nullable=false)
*/		
     		
private $usrCreacion;

/**
* @var string $usrUltMod
*
* @ORM\Column(name="USR_ULT_MOD", type="string", nullable=true)
*/		
     		
private $usrUltMod;

/**
* @var datetime $feCreacion
*
* @ORM\Column(name="FE_CREACION", type="datetime", nullable=true)
*/		
     		
private $feCreacion;

/**
* @var datetime $feUltMod
*
* @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=true)
*/		
     		
private $feUltMod;

/**
* @var datetime $feUltMod
*
* @ORM\Column(name="IP_CREACION", type="string", nullable=true)
*/		
     		
private $ipCreacion;

/**
* @var datetime $ipUltMod
*
* @ORM\Column(name="IP_ULT_MOD", type="string", nullable=true)
*/		
     		
private $ipUltMod;

/**
* Get idReasignacionCuadrilla
*
* @return integer
*/
function getIdReasignacionCuadrilla()
{
    return $this->idReasignacionCuadrilla;
}

/**
* Get idDetalle
*
* @return integer $detalleId
*/

function getIdDetalle()
{
    return $this->idDetalle;
}

/**
* Get idEmpresaRol
*
* @return integer
*/

function getIdEmpresaRol()
{
    return $this->idEmpresaRol;
}

/**
* Get userLogin
*
* @return string
*/

function getUserLogin()
{
    return $this->userLogin;
}

/**
* Get idDepartamentoDestino
*
* @return integer
*/

function getIdDepartamentoDestino()
{
    return $this->idDepartamentoDestino;
}

/**
* Set idReasignacionCuadrilla
*
* @param int $idReasignacionCuadrilla
*/

function setIdReasignacionCuadrilla($idReasignacionCuadrilla)
{
    $this->idReasignacionCuadrilla = $idReasignacionCuadrilla;
}

/**
* Set idDetalle
*
* @param int $idDetalle
*/

function setIdDetalle($idDetalle)
{
    $this->idDetalle = $idDetalle;
}

/**
* Set idEmpresaRol
*
* @param int $idEmpresaRol
*/


function setIdEmpresaRol($idEmpresaRol)
{
    $this->idEmpresaRol = $idEmpresaRol;
}

/**
* Set userLogin
*
* @param string $userLogin
*/

function setUserLogin($userLogin)
{
    $this->userLogin = $userLogin;
}

/**
* Set idDepartamentoDestino
*
* @param integer $idDepartamentoDestino
*/

function setIdDepartamentoDestino($idDepartamentoDestino)
{
    $this->idDepartamentoDestino = $idDepartamentoDestino;
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
* Get ipUltMod
*
* @return string
*/		
     		
public function getIpUltMod(){
	return $this->ipUltMod; 
}

/**
* Set ipUltMod
*
* @param string $ipUltMod
*/
public function setIpUltMod($ipUltMod)
{
    $this->ipUltMod = $ipUltMod;
}

/**
* Get idDPersona
*
* @return integer
*/	

function getIdDPersona()
{
    return $this->idDPersona;
}

/**
* Get nombreCompleto
*
* @return string
*/	

function getNombreCompleto()
{
    return $this->nombreCompleto;
}

/**
* Set idDPersona
*
* @param string $idDPersona
*/

function setIdDPersona($idDPersona)
{
    $this->idDPersona = $idDPersona;
}

/**
* Set nombreCompleto
*
* @param string $nombreCompleto
*/
function setNombreCompleto($nombreCompleto)
{
    $this->nombreCompleto = $nombreCompleto;
}



}
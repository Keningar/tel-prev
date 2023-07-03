<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiProyectos
 *
 * @ORM\Table(name="ADMI_PROYECTO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiProyectosRepository")
 */
class AdmiProyectos
{


/**
* @ORM\Column(name="ID_PROYECTO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_PROYECTO", allocationSize=1, initialValue=1)
*/	
		
private $idProyecto;	

	
/**
* @var string $nombre
*
* @ORM\Column(name="NOMBRE", type="string", nullable=false)
*/		
     		
private $nombre;


/**
* @var integer $responsableId
*
* @ORM\Column(name="RESPONSABLE_ID", type="integer", nullable=false)
*/	
		
private $responsableId;

/**
* @var string $tipoContabilidad
*
* @ORM\Column(name="TIPO_CONTABILIDAD", type="string", nullable=false)
*/		
     		
private $tipoContabilidad;

/**
* @var string $noCia
*
* @ORM\Column(name="NO_CIA", type="string", nullable=false)
*/		
     		
private $noCia;

/**
* @var string $cuentaId
*
* @ORM\Column(name="CUENTA_ID", type="string", nullable=true)
*/		
     		
private $cuentaId;


/**
* @var datetime $feInicio
*
* @ORM\Column(name="FE_INICIO", type="datetime", nullable=false)
*/		
     		
private $feInicio;

/**
* @var datetime $feFin
*
* @ORM\Column(name="FE_FIN", type="datetime", nullable=true)
*/		
     		
private $feFin;


/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=false)
*/	
		
private $estado;


/**
* @var datetime $feCreacion
*
* @ORM\Column(name="FE_CREACION", type="datetime", nullable=true)
*/		
     		
private $feCreacion;



/**
* @var string $usr_creacion
*
* @ORM\Column(name="USR_CREACION", type="string", nullable=false)
*/	
		
private $usr_creacion;



/**
* @var string $usrUltMod
*
* @ORM\Column(name="USR_ULT_MOD", type="string", nullable=false)
*/		
     		
private $usrUltMod;

/**
* @var datetime $feUltMod
*
* @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=false)
*/		
     		
private $feUltMod;


function getIdProyecto() {
    return $this->idProyecto;
}

function getNombre() {
    return $this->nombre;
}

function getResponsableId() {
    return $this->responsableId;
}

function getTipoContabilidad() {
    return $this->tipoContabilidad;
}

function getNoCia() {
    return $this->noCia;
}

function getCuentaId() {
    return $this->cuentaId;
}

function getFeInicio() {
    return $this->feInicio;
}

function getFeFin() {
    return $this->feFin;
}

function getEstado() {
    return $this->estado;
}

function getFeCreacion() {
    return $this->feCreacion;
}

function getUsr_creacion() {
    return $this->usr_creacion;
}

function getUsrUltMod() {
    return $this->usrUltMod;
}

function getFeUltMod() {
    return $this->feUltMod;
}

function setIdProyecto($idProyecto) {
    $this->idProyecto = $idProyecto;
}

function setNombre($nombre) {
    $this->nombre = $nombre;
}

function setResponsableId($responsableId) {
    $this->responsableId = $responsableId;
}

function setTipoContabilidad($tipoContabilidad) {
    $this->tipoContabilidad = $tipoContabilidad;
}

function setNoCia($noCia) {
    $this->noCia = $noCia;
}

function setCuentaId($cuentaId) {
    $this->cuentaId = $cuentaId;
}

function setFeInicio($feInicio) {
    $this->feInicio = $feInicio;
}

function setFeFin($feFin) {
    $this->feFin = $feFin;
}

function setEstado($estado) {
    $this->estado = $estado;
}

function setFeCreacion($feCreacion) {
    $this->feCreacion = $feCreacion;
}

function setUsr_creacion($usr_creacion) {
    $this->usr_creacion = $usr_creacion;
}

function setUsrUltMod($usrUltMod) {
    $this->usrUltMod = $usrUltMod;
}

function setFeUltMod($feUltMod) {
    $this->feUltMod = $feUltMod;
}



}

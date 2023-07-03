<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * telconet\schemaBundle\Entity\InfoRegistroExcel
 *
 * @ORM\Table(name="INFO_REGISTRO_EXCEL")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoRegistroExcelRepository")
 */
class InfoRegistroExcel
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_REGISTRO_EXCEL", type="integer", nullable=false)
* @ORM\Id
*/		
		
private $id;

/**
* @var integer $codigoArchivo
*
* @ORM\Column(name="NUMERO_ARCHIVO_ID", type="integer", nullable=false)
*/		
		
private $codigoArchivo;


/**
* @var string $numeroOrdenTrabajo
*
* @ORM\Column(name="NUMEROORDENTRABAJO", type="string", nullable=false)
*/		
     		
private $numeroOrdenTrabajo;

/**
* @var string $cliente
*
* @ORM\Column(name="CLIENTE", type="string", nullable=false)
*/		
     		
private $cliente;

/**
* @var string $nombreVendedor
*
* @ORM\Column(name="NOMBREVENDEDOR", type="string", nullable=false)
*/		
     		
private $nombreVendedor;

/**
* @var string $login
*
* @ORM\Column(name="LOGIN", type="string", nullable=false)
*/		
     		
private $login;

/**
* @var string $nombreProductoPlan
*
* @ORM\Column(name="NOMBREPRODUCTOPLAN", type="string", nullable=false)
*/		
     		
private $nombreProductoPlan;

/**
* @var string $ciudad
*
* @ORM\Column(name="CIUDAD", type="string", nullable=false)
*/		
     		
private $ciudad;

/**
* @var string $coordenadas
*
* @ORM\Column(name="COORDENADAS", type="string", nullable=false)
*/		
     		
private $coordenadas;

/**
* @var string $direccion
*
* @ORM\Column(name="DIRECCION", type="string", nullable=false)
*/		
     		
private $direccion;

/**
* @var string $nombreSector
*
* @ORM\Column(name="NOMBRESECTOR", type="string", nullable=false)
*/		
     		
private $nombreSector;

/**
* @var string $contactos
*
* @ORM\Column(name="CONTACTOS", type="string", nullable=false)
*/		
     		
private $contactos;

/**
* @var string $tipoOrden
*
* @ORM\Column(name="TIPOORDEN", type="string", nullable=false)
*/		
     		
private $tipoOrden;

/**
* @var string $fecSolicitaPlanificacion
*
* @ORM\Column(name="FESOLICITAPLANIFICACION", type="string", nullable=false)
*/		
     		
private $fecSolicitaPlanificacion;
	

/**
* @var string $fechaPlanificacionReal
*
* @ORM\Column(name="FECHAPLANIFICACIONREAL", type="string", nullable=false)
*/		
     		
private $fechaPlanificacionReal;

/**
* @var string $nombrePlanifica
*
* @ORM\Column(name="NOMBREPLANIFICA", type="string", nullable=false)
*/		
     		
private $nombrePlanifica;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=false)
*/		
     		
private $estado;



/**
* @var string $nombreMotivo
*
* @ORM\Column(name="NOMBREMOTIVO", type="string", nullable=false)
*/		
     		
private $nombreMotivo;

/**
* @var string $descripcion
*
* @ORM\Column(name="DESCRIPCION", type="string", nullable=false)
*/		
     		
private $descripcion;

/**
* @var string $observacion
*
* @ORM\Column(name="OBSERVACION", type="string", nullable=false)
*/		
     		
private $observacion;

/**
* @var string $nombreTarea
*
* @ORM\Column(name="NOMBRE_TAREA", type="string", nullable=false)
*/		
     		
private $nombreTarea;

/**
* @var string $nombreAsigna
*
* @ORM\Column(name="NOMBRE_ASIGNA", type="string", nullable=false)
*/		
     		
private $nombreAsigna;

/**
* @var string $observacionSolicitud
*
* @ORM\Column(name="OBSERVACION_SOLICITUD", type="string", nullable=false)
*/		
     		
private $observacionSolicitud;

/**
* @var string $asignado
*
* @ORM\Column(name="ASIGNADO", type="string", nullable=false)
*/		
     		
private $asignado;

/**
* @var string $nombreElemento
*
* @ORM\Column(name="NOMBRE_ELEMENTO", type="string", nullable=false)
*/		
     		
private $nombreElemento;

/**
* @var string $nombreInterface
*
* @ORM\Column(name="NOMBRE_INTERFACE", type="string", nullable=false)
*/		
     		
private $nombreInterface;

/**
* @var string $ipsCliente
*
* @ORM\Column(name="IPS_CLIENTE", type="string", nullable=false)
*/		
     		
private $ipsCliente;

/**
* @var string $caja
*
* @ORM\Column(name="CAJA", type="string", nullable=false)
*/		
     		
private $caja;

/**
* @var string $splitter
*
* @ORM\Column(name="SPLITTER", type="string", nullable=false)
*/		
     		
private $splitter;

/**
* @var string $idSplitter
*
* @ORM\Column(name="ID_SPLITTER", type="string", nullable=false)
*/		
     		
private $idSplitter;



public function getId() {
    return $this->id;
}

public function getCodigoArchivo() {
    return $this->codigoArchivo;
}

public function getNumeroOrdenTrabajo() {
    return $this->numeroOrdenTrabajo;
}

public function getCliente() {
    return $this->cliente;
}

public function getNombreVendedor() {
    return $this->nombreVendedor;
}

public function getLogin() {
    return $this->login;
}

public function getNombreProductoPlan() {
    return $this->nombreProductoPlan;
}

public function getCiudad() {
    return $this->ciudad;
}

public function getCoordenadas() {
    return $this->coordenadas;
}

public function getDireccion() {
    return $this->direccion;
}

public function getNombreSector() {
    return $this->nombreSector;
}

public function getContactos() {
    return $this->contactos;
}

public function getTipoOrden() {
    return $this->tipoOrden;
}

public function getFecSolicitaPlanificacion() {
    return $this->fecSolicitaPlanificacion;
}

public function getFechaPlanificacionReal() {
    return $this->fechaPlanificacionReal;
}

public function getNombrePlanifica() {
    return $this->nombrePlanifica;
}

public function getEstado() {
    return $this->estado;
}

public function getNombreMotivo() {
    return $this->nombreMotivo;
}

public function getDescripcion() {
    return $this->descripcion;
}

public function getObservacion() {
    return $this->observacion;
}

public function getNombreTarea() {
    return $this->nombreTarea;
}

public function getNombreAsigna() {
    return $this->nombreAsigna;
}

public function getObservacionSolicitud() {
    return $this->observacionSolicitud;
}

public function getAsignado() {
    return $this->asignado;
}

public function getNombreElemento() {
    return $this->nombreElemento;
}

public function getNombreInterface() {
    return $this->nombreInterface;
}

public function getIpsCliente() {
    return $this->ipsCliente;
}

public function getCaja() {
    return $this->caja;
}

public function getSplitter() {
    return $this->splitter;
}

public function getIdSplitter() {
    return $this->idSplitter;
}



public function setId($id) {
    $this->id = $id;
}

public function setCodigoArchivo($codigoArchivo) {
    $this->codigoArchivo = $codigoArchivo;
}

public function setNumeroOrdenTrabajo($numeroOrdenTrabajo) {
    $this->numeroOrdenTrabajo = $numeroOrdenTrabajo;
}

public function setCliente($cliente) {
    $this->cliente = $cliente;
}

public function setNombreVendedor($nombreVendedor) {
    $this->nombreVendedor = $nombreVendedor;
}

public function setLogin($login) {
    $this->login = $login;
}

public function setNombreProductoPlan($nombreProductoPlan) {
    $this->nombreProductoPlan = $nombreProductoPlan;
}

public function setCiudad($ciudad) {
    $this->ciudad = $ciudad;
}

public function setCoordenadas($coordenadas) {
    $this->coordenadas = $coordenadas;
}

public function setDireccion($direccion) {
    $this->direccion = $direccion;
}

public function setNombreSector($nombreSector) {
    $this->nombreSector = $nombreSector;
}

public function setContactos($contactos) {
    $this->contactos = $contactos;
}

public function setTipoOrden($tipoOrden) {
    $this->tipoOrden = $tipoOrden;
}

public function setFecSolicitaPlanificacion($fecSolicitaPlanificacion) {
    $this->fecSolicitaPlanificacion = $fecSolicitaPlanificacion;
}

public function setFechaPlanificacionReal($fechaPlanificacionReal) {
    $this->fechaPlanificacionReal = $fechaPlanificacionReal;
}

public function setNombrePlanifica($nombrePlanifica) {
    $this->nombrePlanifica = $nombrePlanifica;
}

public function setEstado($estado) {
    $this->estado = $estado;
}

public function setNombreMotivo($nombreMotivo) {
    $this->nombreMotivo = $nombreMotivo;
}

public function setDescripcion($descripcion) {
    $this->descripcion = $descripcion;
}

public function setObservacion() {
    $this->observacion = $observacion;
}

public function setNombreTarea() {
    $this->nombreTarea = $nombreTarea;
}

public function setNombreAsigna() {
    $this->nombreAsigna = $nombreAsigna;
}

public function setObservacionSolicitud() {
    $this->observacionSolicitud = $observacionSolicitud;
}

public function setAsignado() {
    $this->asignado =  $asignado;
}

public function setNombreElemento() {
    $this->nombreElemento =  $nombreElemento;
}

public function setNombreInterface() {
    $this->nombreInterface = $nombreInterface;
}

public function setIpsCliente() {
    $this->ipsCliente = $ipsCliente;
}

public function setCaja() {
    $this->caja = $caja;
}

public function setSplitter() {
    $this->splitter = $splitter;
}

public function setIdSplitter() {
     $this->idSplitter = $idSplitter;
}

}
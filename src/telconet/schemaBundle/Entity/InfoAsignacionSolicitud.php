<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoAsignacionSolicitud
 *
 * @ORM\Table(name="INFO_ASIGNACION_SOLICITUD")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoAsignacionSolicitudRepository")
 */
class InfoAsignacionSolicitud
{


/**
* @var InfoEmpresaGrupo
*
* @ORM\Column(name="EMPRESA_COD", type="string", nullable=false)
*/
		
private $empresaCod;

/**
* @var integer $id
*
* @ORM\Column(name="ID_ASIGNACION_SOLICITUD", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_ASIGNACION_SOLICITUD", allocationSize=1, initialValue=1)
*/		

private $id;	

/**
* @var string $departamentoId
*
* @ORM\Column(name="DEPARTAMENTO_ID", type="string", nullable=false)
*/		
private $departamentoId;

/**
* @var string $oficinaId
*
* @ORM\Column(name="OFICINA_ID", type="string", nullable=false)
*/
private $oficinaId;


/**
* @var string $referenciaCliente
*
* @ORM\Column(name="REFERENCIA_CLIENTE", type="string", nullable=false)
*/		
     		
private $referenciaCliente;

/**
* @var string $origen
*
* @ORM\Column(name="ORIGEN", type="string", nullable=false)
*/		
     		
private $origen;

/**
* @var string $tipoAtencion
*
* @ORM\Column(name="TIPO_ATENCION", type="string", nullable=false)
*/		
     		
private $tipoAtencion;

/**
* @var string $tipoProblema
*
* @ORM\Column(name="TIPO_PROBLEMA", type="string", nullable=false)
*/		
     		
private $tipoProblema;


/**
* @var string $criticidad
*
* @ORM\Column(name="CRITICIDAD", type="string", nullable=false)
*/		
     		
private $criticidad;

/**
* @var string $nombreReporta
*
* @ORM\Column(name="NOMBRE_REPORTA", type="string", nullable=false)
*/		
     		
private $nombreReporta;


/**
* @var string $nombreSitio
*
* @ORM\Column(name="NOMBRE_SITIO", type="string", nullable=false)
*/		
     		
private $nombreSitio;



/**
* @var integer $referenciaId
*
* @ORM\Column(name="REFERENCIA_ID", type="integer", nullable=true)
*/		
     		
private $referenciaId;


/**
* @var string $usrAsignado
*
* @ORM\Column(name="USR_ASIGNADO", type="string", nullable=false)
*/		
     		
private $usrAsignado;


/**
* @var string $detalle
*
* @ORM\Column(name="DETALLE", type="string", nullable=false)
*/		
     		
private $detalle;

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
* @ORM\Column(name="USR_ULT_MOD", type="string", nullable=false)
*/		
     		
private $usrUltMod;

/**
* @var datetime $feUltMod
*
* @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=false)
*/		
     		
private $feUltMod;

/**
* @var string $ipCreacion
*
* @ORM\Column(name="IP_CREACION", type="string", nullable=false)
*/		
     		
private $ipCreacion;


/**
* @var string
*
* @ORM\Column(name="CAMBIO_TURNO", type="string", nullable=true)
*/
		
private $cambioTurno;


/**
* @var string
*
* @ORM\Column(name="ESTADO", type="string", nullable=true)
*/
		
private $estado;


/**
* @var string
*
* @ORM\Column(name="DATO_ADICIONAL", type="string", nullable=true)
*/
		
private $datoAdicional;

/**
* @var string
*
* @ORM\Column(name="ASIGNACION_PADRE_ID", type="string", nullable=true)
*/
		
private $asignacionPadreId;

/**
* @var string
*
* @ORM\Column(name="TAB_VISIBLE", type="string", nullable=true)
*/
	
private $tabVisible;

/**
* @var string
*
* @ORM\Column(name="TRAMO", type="string", nullable=true)
*/
	
private $tramo;

/**
* @var string
*
* @ORM\Column(name="HILO_TELEFONICA", type="string", nullable=true)
*/
	
private $hiloTelefonica;

/**
* @var string
*
* @ORM\Column(name="CIRCUITO", type="string", nullable=true)
*/
	
private $circuito;

/**
* @var string
*
* @ORM\Column(name="NOTIFICACION", type="string", nullable=true)
*/
	
private $notificacion;

/**
* Get $asignacionPadreId
*
* @return integer
*/		
     		
public function getAsignacionPadreId(){
	return $this->asignacionPadreId; 
}

/**
* Set asignacionPadreId
*
* @param integer $asignacionPadreId
*/
public function setAsignacionPadreId($asignacionPadreId)
{
        $this->asignacionPadreId = $asignacionPadreId;
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
* Get departamentoId
*
* @return integer
*/		
     		
public function getDepartamentoId(){
	return $this->departamentoId; 
}


/**
* Set departamentoId
*
* @param integer $departamentoId
*/
public function setDepartamentoId($departamentoId)
{
        $this->departamentoId = $departamentoId;
}


/**
* Get oficinaId
*
* @return integer
*/

public function getOficinaId()
{
    return $this->oficinaId;
}


/**
* Set oficinaId
*
* @param integer $oficinaId
*/
public function setOficinaId($intOficinaId)
{
        $this->oficinaId = $intOficinaId;
}

/**
* Get referenciaCliente
*
* @return string
*/		
     		
public function getReferenciaCliente(){
	return $this->referenciaCliente; 
}

/**
* Set referenciaCliente
*
* @param string $referenciaCliente
*/
public function setReferenciaCliente($referenciaCliente)
{
        $this->referenciaCliente = $referenciaCliente;
}


/**
* Get origen
*
* @return string
*/		
     		
public function getOrigen(){
	return $this->origen; 
}

/**
* Set origen
*
* @param string $origen
*/
public function setOrigen($origen)
{
        $this->origen = $origen;
}


/**
* Get tipoAtencion
*
* @return string
*/		
     		
public function getTipoAtencion(){
	return $this->tipoAtencion; 
}

/**
* Set tipoAtencion
*
* @param string $tipoAtencion
*/
public function setTipoAtencion($tipoAtencion)
{
        $this->tipoAtencion = $tipoAtencion;
}

/**
* Get tipoProblema
*
* @return string
*/		
     		
public function getTipoProblema(){
	return $this->tipoProblema; 
}

/**
* Set tipoProblema
*
* @param string $tipoProblema
*/
public function setTipoProblema($tipoProblema)
{
        $this->tipoProblema = $tipoProblema;
}

/**
* Get criticidad
*
* @return string
*/		
     		
public function getCriticidad(){
	return $this->criticidad; 
}

/**
* Set criticidad
*
* @param string $criticidad
*/
public function setCriticidad($criticidad)
{
        $this->criticidad = $criticidad;
}

/**
* Get nombreReporta
*
* @return string
*/		
     		
public function getNombreReporta(){
	return $this->nombreReporta; 
}

/**
* Set nombreReporta
*
* @param string $nombreReporta
*/
public function setNombreReporta($nombreReporta)
{
        $this->nombreReporta = $nombreReporta;
}


/**
* Get nombreSitio
*
* @return string
*/		
     		
public function getNombreSitio(){
	return $this->nombreSitio; 
}

/**
* Set nombreSitio
*
* @param string $nombreSitio
*/
public function setNombreSitio($nombreSitio)
{
        $this->nombreSitio = $nombreSitio;
}


/**
* Get referenciaId
*
* @return integer
*/		
     		
public function getReferenciaId(){
	return $this->referenciaId; 
}


/**
* Set referenciaId
*
* @param integer $referenciaId
*/
public function setReferenciaId($referenciaId)
{
        $this->referenciaId = $referenciaId;
}

/**
* Get usrAsignado
*
* @return string
*/		
     		
public function getUsrAsignado(){
	return $this->usrAsignado; 
}

/**
* Set usrAsignado
*
* @param string $usrAsignado
*/
public function setUsrAsignado($usrAsignado)
{
        $this->usrAsignado = $usrAsignado;
}


/**
* Get detalle
*
* @return string
*/		
     		
public function getDetalle(){
	return $this->detalle; 
}

/**
* Set detalle
*
* @param string $detalle
*/
public function setDetalle($detalle)
{
        $this->detalle = $detalle;
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
* Get cambioTurno
*
* @return string
*/		
     		
public function getCambioTurno()
{
	return $this->cambioTurno;
}

/**
* Set cambioTurno
*
* @param string $cambioTurno
*/
public function setCambioTurno($cambioTurno)
{
    $this->cambioTurno = $cambioTurno;
}

/**
* Get estado
*
* @return string
*/		
     		
public function getEstado()
{
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
 * Get datoAdicional
 *
 * @return string
 */
public function getDatoAdicional()
{
	return $this->datoAdicional;
}

/**
 * Set datoAdicional
 *
 * @param string $strDatoAdicional
 */
public function setDatoAdicional($strDatoAdicional)
{
    $this->datoAdicional = $strDatoAdicional;
}

/**
 * Get tabVisible
 *
 * @return string
 */
public function getTabVisible()
{
	return $this->tabVisible;
}

/**
 * Set tabVisible
 *
 * @param string $strTabVisible
 */
public function setTabVisible($strTabVisible)
{
    $this->tabVisible = $strTabVisible;
}

/**
 * Get tramo
 *
 * @return string
 */
public function getTramo()
{
	return $this->tramo;
}

/**
 * Set tramo
 *
 * @param string $strTramo
 */
public function setTramo($strTramo)
{
    $this->tramo = $strTramo;
}

/**
 * Get hiloTelefonica
 *
 * @return string
 */
public function getHiloTelefonica()
{
	return $this->hiloTelefonica;
}

/**
 * Set hiloTelefonica
 *
 * @param string $strHiloTelefonica
 */
public function setHiloTelefonica($strHiloTelefonica)
{
    $this->hiloTelefonica = $strHiloTelefonica;
}


/**
 * Get circuito
 *
 * @return string
 */
public function getCircuito()
{
	return $this->circuito;
}

/**
 * Set circuito
 *
 * @param string $strCircuito
 */
public function setCircuito($strCircuito)
{
    $this->circuito = $strCircuito;
}

/**
 * Get notificacion
 *
 * @return string
 */
public function getNotificacion()
{
	return $this->notificacion;
}

/**
 * Set notificacion
 *
 * @param string $strNotificacion
 */
public function setNotificacion($strNotificacion)
{
    $this->notificacion = $strNotificacion;
}


}
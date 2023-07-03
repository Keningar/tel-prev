<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoOrdenTrabajo
 *
 * @ORM\Table(name="INFO_ORDEN_TRABAJO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoOrdenTrabajoRepository")
 */
class InfoOrdenTrabajo
{
/**
* @var string $tipoOrden
*
* @ORM\Column(name="TIPO_ORDEN", type="string", nullable=true)
*/		
     		
private $tipoOrden;

/**
* @var integer $oficinaId
*
* @ORM\Column(name="OFICINA_ID", type="integer", nullable=true)
*/		
     		
private $oficinaId;

/**
* @var integer $id
*
* @ORM\Column(name="ID_ORDEN_TRABAJO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_ORDEN_TRABAJO", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var string $numeroOrdenTrabajo
*
* @ORM\Column(name="NUMERO_ORDEN_TRABAJO", type="string", nullable=false)
*/		
     		
private $numeroOrdenTrabajo;

/**
* @var InfoPunto
*
* @ORM\ManyToOne(targetEntity="InfoPunto")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="PUNTO_ID", referencedColumnName="ID_PUNTO",nullable=true)
* })
*/
		
private $puntoId;

/**
* @var integer $elementoId
*
* @ORM\Column(name="ELEMENTO_ID", type="integer", nullable=true)
*/	

private $elementoId;


/**
* @var datetime $feInicio
*
* @ORM\Column(name="FE_INICIO", type="datetime", nullable=false)
*/
private $feInicio;

/**
* @var datetime $feFin
*
* @ORM\Column(name="FE_FIN", type="datetime", nullable=false)
*/
private $feFin;


/**
* @var integer $perAutorizacionId
*
* @ORM\Column(name="PER_AUTORIZACION_ID", type="integer", nullable=true)
*/	

private $perAutorizacionId;



/**
* @var datetime $feCreacion
*
* @ORM\Column(name="FE_CREACION", type="datetime", nullable=false)
*/		
     		
private $feCreacion;

/**
* @var datetime $feUltMod
*
* @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=true)
*/		
     		
private $feUltMod;

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
* @var string $ipCreacion
*
* @ORM\Column(name="IP_CREACION", type="string", nullable=true)
*/		
     		
private $ipCreacion;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=true)
*/		
     		
private $estado;

/**
* @var integer $ultimaMillaId
*
* @ORM\Column(name="ULTIMA_MILLA_ID", type="integer", nullable=true)
*/
		
private $ultimaMillaId;

/**
* @var string $observacion
*
* @ORM\Column(name="OBSERVACION", type="string", nullable=true)
*/		
     		
private $observacion;

/**
* Get tipoOrden
*
* @return string
*/		
     		
public function getTipoOrden(){
	return $this->tipoOrden; 
}

/**
* Set tipoOrden
*
* @param string $tipoOrden
*/
public function setTipoOrden($tipoOrden)
{
        $this->tipoOrden = $tipoOrden;
}


/**
* Get oficinaId
*
* @return integer
*/		
     		
public function getOficinaId(){
	return $this->oficinaId; 
}

/**
* Set oficinaId
*
* @param integer $oficinaId
*/
public function setOficinaId($oficinaId)
{
        $this->oficinaId = $oficinaId;
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
* Get numeroOrdenTrabajo
*
* @return string
*/		
     		
public function getNumeroOrdenTrabajo(){
	return $this->numeroOrdenTrabajo; 
}

/**
* Set numeroOrdenTrabajo
*
* @param string $numeroOrdenTrabajo
*/
public function setNumeroOrdenTrabajo($numeroOrdenTrabajo)
{
        $this->numeroOrdenTrabajo = $numeroOrdenTrabajo;
}


/**
* Get puntoId
*
* @return telconet\schemaBundle\Entity\InfoPunto
*/		
     		
public function getPuntoId(){
	return $this->puntoId; 
}

/**
* Set puntoId
*
* @param telconet\schemaBundle\Entity\InfoPunto $puntoId
*/
public function setPuntoId(\telconet\schemaBundle\Entity\InfoPunto $puntoId)
{
        $this->puntoId = $puntoId;
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
* Get ultimaMillaId
*
* @return int
*/		
     		
public function getUltimaMillaId(){
	return $this->ultimaMillaId; 
}

/**
* Set ultimaMillaId
*
* @param int $ultimaMillaId
*/
public function setUltimaMillaId($ultimaMillaId)
{
        $this->ultimaMillaId = $ultimaMillaId;
}


/**
 * Get elementoId
 *
 * @return integer
 */
public function getElementoId()
{
    return $this->elementoId;
}

/**
 * Set elementoId
 *
 * @param integer $elementoId
 */
public function setElementoId($elementoId)
{
    $this->elementoId = $elementoId;
}

/**
 * Get feInicio
 *
 * @return datetime
 */
public function getFeInicio()
{
    return $this->feInicio;
}

/**
 * Set feInicio
 *
 * @param datetime $feInicio
 */
public function setFeInicio($feInicio)
{
    $this->feInicio = $feInicio;
}

/**
 * Get feFin
 *
 * @return datetime
 */
public function getFeFin()
{
    return $this->feFin;
}

/**
 * Set feFin
 *
 * @param datetime $feFin
 */
public function setFeFin($feFin)
{
    $this->feFin = $feFin;
}


/**
 * Get perAutorizacionId
 *
 * @return integer
 */
public function getPerAutorizacionId()
{
    return $this->perAutorizacionId;
}

/**
 * Set perAutorizacionId
 *
 * @param integer $perAutorizacionId
 */
public function setPerAutorizacionId($perAutorizacionId)
{
    $this->perAutorizacionId = $perAutorizacionId;
}

/**
 * Get observacion
 *
 * @return string
 */
public function getObservacion()
{
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

}

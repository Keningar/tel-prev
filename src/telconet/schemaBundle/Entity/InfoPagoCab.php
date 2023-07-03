<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoPagoCab
 *
 * @ORM\Table(name="INFO_PAGO_CAB")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoPagoCabRepository")
 */
class InfoPagoCab
{


/**
* @var string $empresaId
*
* @ORM\Column(name="EMPRESA_ID", type="string", nullable=true)
*/		
     		
private $empresaId;

/**
* @var integer $id
*
* @ORM\Column(name="ID_PAGO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_PAGO_CAB", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var integer $puntoId
*
* @ORM\Column(name="PUNTO_ID", type="integer", nullable=true)
*/		
     		
private $puntoId;

/**
* @var integer $oficinaId
*
* @ORM\Column(name="OFICINA_ID", type="integer", nullable=true)
*/		
     		
private $oficinaId;

/**
* @var string $numeroPago
*
* @ORM\Column(name="NUMERO_PAGO", type="string", nullable=true)
*/		
     		
private $numeroPago;

/**
* @var string $numPagoMigracion
*
* @ORM\Column(name="NUM_PAGO_MIGRACION", type="string", nullable=true)
*/		
     		
private $numPagoMigracion;

/**
* @var float $valorTotal
*
* @ORM\Column(name="VALOR_TOTAL", type="float", nullable=true)
*/		
     		
private $valorTotal;

/**
* @var datetime $feEliminacion
*
* @ORM\Column(name="FE_ELIMINACION", type="datetime", nullable=true)
*/		
     		
private $feEliminacion;

/**
* @var string $estadoPago
*
* @ORM\Column(name="ESTADO_PAGO", type="string", nullable=true)
*/		
     		
private $estadoPago;

/**
* @var string $comentarioPago
*
* @ORM\Column(name="COMENTARIO_PAGO", type="string", nullable=true)
*/		
     		
private $comentarioPago;

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
* @var AdmiTipoDocumentoFinanciero
*
* @ORM\ManyToOne(targetEntity="AdmiTipoDocumentoFinanciero")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="TIPO_DOCUMENTO_ID", referencedColumnName="ID_TIPO_DOCUMENTO")
* })
*/
		
private $tipoDocumentoId;

/**
* @var string $debitoDetId
*
* @ORM\Column(name="DEBITO_DET_ID", type="integer", nullable=true)
*/		
     		
private $debitoDetId;



/**
* @var InfoDebitoGeneralHistorial
*
* @ORM\ManyToOne(targetEntity="InfoDebitoGeneralHistorial")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="DEBITO_GENERAL_HISTORIAL_ID", referencedColumnName="ID_DEBITO_GENERAL_HISTORIAL")
* })
*/
		
private $debitoGeneralHistorialId;


/**
* @var InfoRecaudacion
*
* @ORM\ManyToOne(targetEntity="InfoRecaudacion")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="RECAUDACION_ID", referencedColumnName="ID_RECAUDACION")
* })
*/
		
private $recaudacionId;


/**
* @var InfoRecaudacionDet
*
* @ORM\ManyToOne(targetEntity="InfoRecaudacionDet",cascade={"persist"})
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="RECAUDACION_DET_ID", referencedColumnName="ID_RECAUDACION_DET")
* })
*/
		
private $recaudacionDetId;

/**
 * @var \telconet\schemaBundle\Entity\InfoPagoLinea
 *
 * @ORM\ManyToOne(targetEntity="InfoPagoLinea")
 * @ORM\JoinColumns({
 *   @ORM\JoinColumn(name="PAGO_LINEA_ID", referencedColumnName="ID_PAGO_LINEA")
 * })
 */
private $pagoLinea;


/**
* @var datetime $feCruce
*
* @ORM\Column(name="FE_CRUCE", type="datetime", nullable=true)
*/		
     		
private $feCruce;


/**
* @var string $usrCruce
*
* @ORM\Column(name="USR_CRUCE", type="string", nullable=true)
*/		
     		
private $usrCruce;


/**
* @var string $anticipoId
*
* @ORM\Column(name="ANTICIPO_ID", type="integer", nullable=true)
*/		
     		
private $anticipoId;

/**
* @var integer $pagoId
*
* @ORM\Column(name="PAGO_ID", type="integer", nullable=true)
*/		
     		
private $pagoId;

/**
* @var integer $motivoId
* 
* @ORM\Column(name="MOTIVO_ID", type="integer", nullable=true)
*/
private $motivoId;


/**
* @var integer $detallePagoAutomaticoId
* 
* @ORM\Column(name="DETALLE_PAGO_AUTOMATICO_ID", type="integer", nullable=true)
*/
private $detallePagoAutomaticoId;

/**
* Get pagoId
*
* @return integer
*/
public function getPagoId() {
    return $this->pagoId;
}

/**
* Set pagoId
*
* @param integer $pagoId
*/
public function setPagoId($pagoId) {
    $this->pagoId = $pagoId;
}



/**
* Get empresaId
*
* @return string
*/		 		
public function getEmpresaId(){
	return $this->empresaId; 
}

/**
* Set empresaId
*
* @param string $empresaId
*/
public function setEmpresaId($empresaId)
{
        $this->empresaId = $empresaId;
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
* Get puntoId
*
* @return integer
*/		
     		
public function getPuntoId(){
	return $this->puntoId; 
}

/**
* Set puntoId
*
* @param integer $puntoId
*/
public function setPuntoId($puntoId)
{
        $this->puntoId = $puntoId;
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
* Get numeroPago
*
* @return string
*/		
     		
public function getNumeroPago(){
	return $this->numeroPago; 
}

/**
* Set numeroPago
*
* @param string $numeroPago
*/
public function setNumeroPago($numeroPago)
{
        $this->numeroPago = $numeroPago;
}

/**
* Get numPagoMigracion
*
* @return string
*/		
     		
public function getNumPagoMigracion(){
	return $this->numPagoMigracion; 
}

/**
* Set numPagoMigracion
*
* @param string $numPagoMigracion
*/
public function setNumPagoMigracion($numPagoMigracion)
{
        $this->numPagoMigracion = $numPagoMigracion;
}


/**
* Get valorTotal
*
* @return float
*/		
     		
public function getValorTotal(){
	return $this->valorTotal; 
}

/**
* Set valorTotal
*
* @param float $valorTotal
*/
public function setValorTotal($valorTotal)
{
        $this->valorTotal = $valorTotal;
}


/**
* Get feEliminacion
*
* @return datetime
*/		
     		
public function getFeEliminacion(){
	return $this->feEliminacion; 
}

/**
* Set feEliminacion
*
* @param datetime $feEliminacion
*/
public function setFeEliminacion($feEliminacion)
{
        $this->feEliminacion = $feEliminacion;
}


/**
* Get estadoPago
*
* @return string
*/		
     		
public function getEstadoPago(){
	return $this->estadoPago; 
}

/**
* Set estadoPago
*
* @param string $estadoPago
*/
public function setEstadoPago($estadoPago)
{
        $this->estadoPago = $estadoPago;
}


/**
* Get comentarioPago
*
* @return 
*/		
     		
public function getComentarioPago(){
	return $this->comentarioPago; 
}

/**
* Set comentarioPago
*
* @param  $comentarioPago
*/
public function setComentarioPago($comentarioPago)
{
        $this->comentarioPago = $comentarioPago;
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
* Get tipoDocumentoId
*
* @return telconet\schemaBundle\Entity\AdmiTipoDocumentoFinanciero
*/		
     		
public function getTipoDocumentoId(){
	return $this->tipoDocumentoId; 
}

/**
* Set tipoDocumentoId
*
* @param telconet\schemaBundle\Entity\AdmiTipoDocumentoFinanciero $tipoDocumentoId
*/
public function setTipoDocumentoId(\telconet\schemaBundle\Entity\AdmiTipoDocumentoFinanciero $tipoDocumentoId)
{
        $this->tipoDocumentoId = $tipoDocumentoId;
}


/**
* Get debitoDetId
*
* @return integer
*/		
     		
public function getDebitoDetId(){
	return $this->debitoDetId; 
}

/**
* Set debitoDetId
*
* @param integer $debitoDetId
*/
public function setDebitoDetId($debitoDetId)
{
        $this->debitoDetId = $debitoDetId;
}




/**
* Get debitoGeneralHistorialId
*
* @return telconet\schemaBundle\Entity\InfoDebitoGeneralHistorial
*/		
     		
public function getDebitoGeneralHistorialId(){
	return $this->debitoGeneralHistorialId; 
}

/**
* Set debitoGeneralHistorialId
*
* @param telconet\schemaBundle\Entity\InfoDebitoGeneralHistorial $debitoGeneralHistorialId
*/
public function setDebitoGeneralHistorialId(\telconet\schemaBundle\Entity\InfoDebitoGeneralHistorial $debitoGeneralHistorialId = NULL)
{
        $this->debitoGeneralHistorialId = $debitoGeneralHistorialId;
}


/**
* Get recaudacionId
*
* @return telconet\schemaBundle\Entity\InfoRecaudacion
*/		
     		
public function getRecaudacionId(){
	return $this->recaudacionId; 
}

/**
* Set recaudacionId
*
* @param telconet\schemaBundle\Entity\InfoRecaudacion $recaudacionId
*/
public function setRecaudacionId(\telconet\schemaBundle\Entity\InfoRecaudacion $recaudacionId = NULL)
{
        $this->recaudacionId = $recaudacionId;
}


/**
* Get recaudacionDetId
*
* @return telconet\schemaBundle\Entity\InfoRecaudacionDet
*/		
     		
public function getRecaudacionDetId(){
	return $this->recaudacionDetId; 
}

/**
* Set recaudacionDetId
*
* @param telconet\schemaBundle\Entity\InfoRecaudacionDet $recaudacionDetId
*/
public function setRecaudacionDetId(\telconet\schemaBundle\Entity\InfoRecaudacionDet $recaudacionDetId = NULL)
{
        $this->recaudacionDetId = $recaudacionDetId;
}


/**
 *
 * @return \telconet\schemaBundle\Entity\InfoPagoLinea
 */
public function getPagoLinea() {
    return $this->pagoLinea;
}

/**
 *
 * @param \telconet\schemaBundle\Entity\InfoPagoLinea $pagoLinea
 */
public function setPagoLinea(\telconet\schemaBundle\Entity\InfoPagoLinea $pagoLinea = NULL) {
    $this->pagoLinea = $pagoLinea;
}

/**
* Get feCruce
*
* @return datetime
*/		
     		
public function getFeCruce(){
	return $this->feCruce; 
}

/**
* Set feCruce
*
* @param datetime $feCruce
*/
public function setFeCruce($feCruce)
{
        $this->feCruce = $feCruce;
}


/**
* Get usrCruce
*
* @return string
*/		
     		
public function getUsrCruce(){
	return $this->usrCruce; 
}

/**
* Set usrCruce
*
* @param string $usrCruce
*/
public function setUsrCruce($usrCruce)
{
        $this->usrCruce = $usrCruce;
}

/**
* Get anticipoId
*
* @return integer
*/		
     		
public function getAnticipoId(){
	return $this->anticipoId; 
}

/**
* Set anticipoId
*
* @param integer $anticipoId
*/
public function setAnticipoId($anticipoId)
{
        $this->anticipoId = $anticipoId;
}

/**
* Get motivoId
*
* @return integer
*/  

public function getMotivoId(){
    return $this->motivoId; 
}

/**
* Set motivoId
*
* @param integer $motivoId
*/
public function setMotivoId($motivoId)
{
        $this->motivoId = $motivoId;
}

/**
* Get detallePagoAutomaticoId
*
* @return integer
*/		
     		
public function getDetallePagoAutomaticoId()
{
    return $this->detallePagoAutomaticoId; 
}

/**
* Set detallePagoAutomaticoId
*
* @param integer $detallePagoAutomaticoId
*/
public function setDetallePagoAutomaticoId($detallePagoAutomaticoId)
{
    $this->detallePagoAutomaticoId = $detallePagoAutomaticoId;
}
}
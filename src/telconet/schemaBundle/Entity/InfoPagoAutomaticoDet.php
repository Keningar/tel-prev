<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiFormatoDebito
 *
 * @ORM\Table(name="INFO_PAGO_AUTOMATICO_DET")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoPagoAutomaticoDetRepository")
 */
class InfoPagoAutomaticoDet
{


    /**
    * @var integer $id
    *
    * @ORM\Column(name="ID_DETALLE_PAGO_AUTOMATICO", type="integer", nullable=false)
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="SEQUENCE")
    * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_PAGO_AUTOMATICO_DET", allocationSize=1, initialValue=1)
    */		

    private $id;	

    /**
    * @var integer $pagoAutomaticoId
    *
    * @ORM\Column(name="PAGO_AUTOMATICO_ID", type="integer", nullable=true)
    */

    private $pagoAutomaticoId;

    /**
    * @var integer $personaEmpresaRolId
    *
    * @ORM\Column(name="PERSONA_EMPRESA_ROL_ID", type="integer", nullable=true)
    */

    private $personaEmpresaRolId;


    /**
    * @var integer $formaPagoId
    *
    * @ORM\Column(name="FORMA_PAGO_ID", type="integer", nullable=true)
    */

    private $formaPagoId;

    /**
    * @var integer $formaPagoRetId
    *
    * @ORM\Column(name="FORMA_PAGO_RET_ID", type="integer", nullable=true)
    */

    private $formaPagoRetId;


    /**
    * @var string $observacion
    *
    * @ORM\Column(name="OBSERVACION", type="string", nullable=true)
    */		

    private $observacion;


    /**
    * @var string $numeroReferencia
    *
    * @ORM\Column(name="NUMERO_REFERENCIA", type="string", nullable=true)
    */		

    private $numeroReferencia;


    /**
    * @var integer $monto
    *
    * @ORM\Column(name="MONTO", type="float", nullable=true)
    */		

    private $monto;

    /**
    * @var string $ipCreacion
    *
    * @ORM\Column(name="IP_CREACION", type="string", nullable=false)
    */		

    private $ipCreacion;

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
    * @var string $fecha
    *
    * @ORM\Column(name="FECHA", type="string", nullable=true)
    */		

    private $fecha;
    
    /**
    * @var integer $codigoImpuesto
    *
    * @ORM\Column(name="CODIGO_IMPUESTO", type="integer", nullable=true)
    */		

    private $codigoImpuesto;    

    /**
    * @var integer $porcentajeRetencion
    *
    * @ORM\Column(name="PORCENTAJE_RETENCION", type="float", nullable=true)
    */		

    private $porcentajeRetencion;

    /**
    * @var integer $baseImponible
    *
    * @ORM\Column(name="BASE_IMPONIBLE", type="float", nullable=true)
    */		

    private $baseImponible;

    /**
    * @var integer $baseImponibleCal
    *
    * @ORM\Column(name="BASE_IMPONIBLE_CAL", type="float", nullable=true)
    */		

    private $baseImponibleCal;


    /**
     * @var string $numeroFactura
     *
     * @ORM\Column(name="NUMERO_FACTURA", type="string", nullable=true)
     */
    private $numeroFactura;

    /**
     * @var string $empresaCod
     *
     * @ORM\Column(name="EMPRESA_COD", type="string", nullable=true)
     */    
    private $empresaCod;
    
    /**
     * @var string $esNotificado
     *
     * @ORM\Column(name="ES_NOTIFICADO", type="string", nullable=true)
     */    
    private $esNotificado;

    /**
    * Get id
    *
    * @return integer
    */		

    public function getId()
    {
        return $this->id; 
    }

    /**
    * Get pagoAutomaticoId
    *
    * @return integer
    */		

    public function getPagoAutomaticoId()
    {
        return $this->pagoAutomaticoId; 
    }

    /**
    * Set pagoAutomaticoId
    *
    * @param integer $pagoAutomaticoId
    */
    public function setPagoAutomaticoId($pagoAutomaticoId)
    {
        $this->pagoAutomaticoId = $pagoAutomaticoId;
    }

    /**
    * Get personaEmpresaRolId
    *
    * @return integer
    */		

    public function getPersonaEmpresaRolId()
    {
        return $this->personaEmpresaRolId; 
    }

    /**
    * Set personaEmpresaRolId
    *
    * @param integer $personaEmpresaRolId
    */
    public function setPersonaEmpresaRolId($personaEmpresaRolId)
    {
        $this->personaEmpresaRolId = $personaEmpresaRolId;
    }


    /**
    * Get documentoId
    *
    * @return integer
    */		

    public function getDocumentoId()
    {
        return $this->documentoId; 
    }

    /**
    * Set documentoId
    *
    * @param integer $documentoId
    */
    public function setDocumentoId($documentoId)
    {
        $this->documentoId = $documentoId;
    }

    /**
    * Get formaPagoId
    *
    * @return integer
    */		

    public function getFormaPagoId()
    {
        return $this->formaPagoId; 
    }

    /**
    * Set formaPagoId
    *
    * @param integer $formaPagoId
    */
    public function setFormaPagoId($formaPagoId)
    {
        $this->formaPagoId = $formaPagoId;
    }

    /**
    * Get formaPagoRetId
    *
    * @return integer
    */		

    public function getFormaPagoRetId()
    {
        return $this->formaPagoRetId; 
    }

    /**
    * Set formaPagoRetId
    *
    * @param integer $formaPagoRetId
    */
    public function setFormaPagoRetId($formaPagoRetId)
    {
        $this->formaPagoRetId = $formaPagoRetId;
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


    /**
    * Get numeroReferencia
    *
    * @return string
    */		

    public function getNumeroReferencia()
    {
        return $this->numeroReferencia; 
    }

    /**
    * Set numeroReferencia
    *
    * @param string $numeroReferencia
    */
    public function setNumeroReferencia($numeroReferencia)
    {
        $this->numeroReferencia = $numeroReferencia;
    }

    /**
    * Get monto
    *
    * @return integer
    */		

    public function getMonto()
    {
        return $this->monto; 
    }

    /**
    * Set monto
    *
    * @param integer $monto
    */
    public function setMonto($monto)
    {
        $this->monto = $monto;
    }

    /**
    * Get ipCreacion
    *
    * @return string
    */		

    public function getIpCreacion()
    {
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
    * Get usrCreacion
    *
    * @return string
    */		

    public function getUsrCreacion()
    {
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

    public function getFeCreacion()
    {
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

    public function getUsrUltMod()
    {
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

    public function getFeUltMod()
    {
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
    * Get fecha
    *
    * @return string
    */		

    public function getFecha()
    {
        return $this->fecha; 
    }

    /**
    * Set fecha
    *
    * @param string $strFecha
    */
    public function setFecha($strFecha)
    {
        $this->fecha = $strFecha;
    }
    
    /**
    * Get codigoImpuesto
    *
    * @return integer
    */		

    public function getCodigoImpuesto(){
        return $this->codigoImpuesto; 
    }

    /**
    * Set codigoImpuesto
    *
    * @param integer $codigoImpuesto
    */
    public function setCodigoImpuesto($codigoImpuesto)
    {
            $this->codigoImpuesto = $codigoImpuesto;
    }    

    /**
    * Get porcentajeRetencion
    *
    * @return integer
    */		

    public function getPorcentajeRetencion(){
        return $this->porcentajeRetencion; 
    }

    /**
    * Set porcentajeRetencion
    *
    * @param integer $porcentajeRetencion
    */
    public function setPorcentajeRetencion($porcentajeRetencion)
    {
            $this->porcentajeRetencion = $porcentajeRetencion;
    }

    /**
    * Get baseImponible
    *
    * @return integer
    */		

    public function getBaseImponible(){
        return $this->baseImponible; 
    }

    /**
    * Set baseImponible
    *
    * @param integer $baseImponible
    */
    public function setBaseImponible($baseImponible)
    {
            $this->baseImponible = $baseImponible;
    }

    /**
    * Get baseImponibleCal
    *
    * @return integer
    */		

    public function getBaseImponibleCal(){
        return $this->baseImponibleCal; 
    }

    /**
    * Set baseImponibleCal
    *
    * @param integer $baseImponibleCal
    */
    public function setBaseImponibleCal($baseImponibleCal)
    {
            $this->baseImponibleCal = $baseImponibleCal;
    }
    /**
     * Get numeroFactura
     *
     * @return string
     */
    public function getNumeroFactura()
    {
        return $this->numeroFactura;
    }

    /**
     * Set numeroFactura
     *
     * @param string $numeroFactura
     */
    public function setNumeroFactura($numeroFactura)
    {
        $this->numeroFactura = $numeroFactura;
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
    * Get esNotificado
    *
    * @return string
    */		

    public function getEsNotificado()
    {
        return $this->esNotificado; 
    }

    /**
    * Set esNotificado
    *
    * @param string $esNotificado
    */
    public function setEsNotificado($esNotificado)
    {
        $this->esNotificado = $esNotificado;
    }
}


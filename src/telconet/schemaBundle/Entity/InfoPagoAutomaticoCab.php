<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiFormatoDebito
 *
 * @ORM\Table(name="INFO_PAGO_AUTOMATICO_CAB")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoPagoAutomaticoCabRepository")
 */
class InfoPagoAutomaticoCab
{


    /**
    * @var integer $id
    *
    * @ORM\Column(name="ID_PAGO_AUTOMATICO", type="integer", nullable=false)
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="SEQUENCE")
    * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_PAGO_AUTOMATICO_CAB", allocationSize=1, initialValue=1)
    */		

    private $id;


    /**
    * @var integer $CuentaContableId
    *
    * @ORM\Column(name="CUENTA_CONTABLE_ID", type="integer", nullable=true)
    */

    private $cuentaContableId;

    /**
    * @var integer $bancoTipoCuentaId
    *
    * @ORM\Column(name="BANCO_TIPO_CUENTA_ID", type="integer", nullable=true)
    */

    private $bancoTipoCuentaId;


    /**
    * @var integer $oficinaId
    *
    * @ORM\Column(name="OFICINA_ID", type="integer", nullable=true)
    */

    private $oficinaId;


    /**
    * @var string $rutaArchivo
    *
    * @ORM\Column(name="RUTA_ARCHIVO", type="string", nullable=true)
    */		

    private $rutaArchivo;


    /**
    * @var string $nombreArchivo
    *
    * @ORM\Column(name="NOMBRE_ARCHIVO", type="string", nullable=true)
    */		

    private $nombreArchivo;

    /**
    * @var string $observacion
    *
    * @ORM\Column(name="OBSERVACION", type="string", nullable=true)
    */		

    private $observacion;

    /**
    * @var string $ipCreacion
    *
    * @ORM\Column(name="IP_CREACION", type="string", nullable=true)
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
     * @var string $razonSocial
     *
     * @ORM\Column(name="RAZON_SOCIAL", type="string", nullable=true)
     */
    private $razonSocial;


    /**
     * @var string $identificacionCliente
     *
     * @ORM\Column(name="IDENTIFICACION_CLIENTE", type="string", nullable=true)
     */
    private $identificacionCliente;
    
    /**
    * @var string $tipoFormaPago
    *
    * @ORM\Column(name="TIPO_FORMA_PAGO", type="string", nullable=true)
    */
    private $tipoFormaPago;

    /**
    * Get id
    *
    * @return integer
    */		

    public function getId(){
        return $this->id; 
    }


    /**
    * Get cuentaContableId
    *
    * @return integer
    */		

    public function getCuentaContableId()
    {
        return $this->cuentaContableId; 
    }

    /**
    * Set cuentaContableId
    *
    * @param integer $cuentaContableId
    */
    public function setCuentaContableId($cuentaContableId)
    {
        $this->cuentaContableId = $cuentaContableId;
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
    * Get rutaArchivo
    *
    * @return string
    */		

    public function getRutaArchivo(){
        return $this->rutaArchivo; 
    }

    /**
    * Set rutaArchivo
    *
    * @param string $rutaArchivo
    */
    public function setRutaArchivo($rutaArchivo)
    {
            $this->rutaArchivo = $rutaArchivo;
    }


    /**
    * Get nombreArchivo
    *
    * @return string
    */		

    public function getNombreArchivo(){
        return $this->nombreArchivo; 
    }

    /**
    * Set ipCreacion
    *
    * @param string $nombreArchivo
    */
    public function setNombreArchivo($nombreArchivo)
    {
            $this->nombreArchivo = $nombreArchivo;
    }

    /**
    * Get observacion
    *
    * @return string
    */		

    public function getObservacion(){
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
     * Get razonSocial
     *
     * @return string
     */
    public function getRazonSocial()
    {
        return $this->razonSocial;
    }

    /**
     * Set razonSocial
     *
     * @param string $razonSocial
     */
    public function setRazonSocial($razonSocial)
    {
        $this->razonSocial = $razonSocial;
    }

    /**
     * Get identificacionCliente
     *
     * @return string
     */
    public function getIdentificacionCliente()
    {
        return $this->identificacionCliente;
    }

    /**
     * Set identificacionCliente
     *
     * @param string $identificacionCliente
     */
    public function setIdentificacionCliente($identificacionCliente)
    {
        $this->identificacionCliente = $identificacionCliente;
    }
    
    /**
    * Get tipoFormaPago
    *
    * @return string
    */	
    public function getTipoFormaPago()
    {
        return $this->tipoFormaPago; 
    }

    /**
    * Set tipoFormaPago
    *
    * @param string $tipoFormaPago
    */
    public function setTipoFormaPago($tipoFormaPago)
    {
        $this->tipoFormaPago = $tipoFormaPago;
    }
}

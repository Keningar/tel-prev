<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoDeposito
 *
 * @ORM\Table(name="INFO_DEPOSITO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoDepositoRepository")
 */
class InfoDeposito
{


    /**
    * @var integer $id
    *
    * @ORM\Column(name="ID_DEPOSITO", type="integer", nullable=false)
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="SEQUENCE")
    * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_DEPOSITO", allocationSize=1, initialValue=1)
    */		

    private $id;	

    /**
    * @var integer $bancoNafId
    *
    * @ORM\Column(name="BANCO_NAF_ID", type="integer", nullable=true)
    */		

    private $bancoNafId;

    /**
    * @var string $noCuentaBancoNaf
    *
    * @ORM\Column(name="NO_CUENTA_BANCO_NAF", type="string", nullable=true)
    */		

    private $noCuentaBancoNaf;

    /**
    * @var string $noCuentaContableNaf
    *
    * @ORM\Column(name="NO_CUENTA_CONTABLE_NAF", type="string", nullable=true)
    */		

    private $noCuentaContableNaf;

    /**
    * @var string $noComprobanteDeposito
    *
    * @ORM\Column(name="NO_COMPROBANTE_DEPOSITO", type="string", nullable=true)
    */		

    private $noComprobanteDeposito;

    /**
    * @var float $valor
    *
    * @ORM\Column(name="VALOR", type="float", nullable=true)
    */		

    private $valor;

    /**
    * @var datetime $feDeposito
    *
    * @ORM\Column(name="FE_DEPOSITO", type="datetime", nullable=true)
    */		

    private $feDeposito;

    /**
    * @var date $feAnulado
    *
    * @ORM\Column(name="FE_ANULADO", type="datetime", nullable=true)
    */		

    private $feAnulado;

    /**
    * @var date $feProcesado
    *
    * @ORM\Column(name="FE_PROCESADO", type="datetime", nullable=true)
    */		

    private $feProcesado;

    /**
    * @var date $feCreacion
    *
    * @ORM\Column(name="FE_CREACION", type="datetime", nullable=true)
    */		

    private $feCreacion;

    /**
    * @var date $feUltMod
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
    * @var string $usrProcesa
    *
    * @ORM\Column(name="USR_PROCESA", type="string", nullable=true)
    */		

    private $usrProcesa;

    /**
    * @var string $usrAnula
    *
    * @ORM\Column(name="USR_ANULA", type="string", nullable=true)
    */		

    private $usrAnula;

    /**
    * @var string $usrUltMod
    *
    * @ORM\Column(name="USR_ULT_MOD", type="string", nullable=true)
    */		

    private $usrUltMod;

    /**
    * @var string $estado
    *
    * @ORM\Column(name="ESTADO", type="string", nullable=true)
    */		

    private $estado;

    /**
    * @var string $ipCreacion
    *
    * @ORM\Column(name="IP_CREACION", type="string", nullable=true)
    */		

    private $ipCreacion;

    /**
    * @var integer $cuentaContableId
    *
    * @ORM\Column(name="CUENTA_CONTABLE_ID", type="integer", nullable=true)
    */		

    private $cuentaContableId;


    /**
    * @var string $empresaId
    *
    * @ORM\Column(name="EMPRESA_ID", type="string", nullable=true)
    */		

    private $empresaId;

    /**
    * @var string $contabilizado
    *
    * @ORM\Column(name="CONTABILIZADO", type="string", nullable=true)
    */		

    private $contabilizado='N';

    /**
    * @var integer $oficinaId
    *
    * @ORM\Column(name="OFICINA_ID", type="integer", nullable=true)
    */
    private $oficinaId;  

    /**
    * Get id
    *
    * @return integer
    */		

    public function getId(){
        return $this->id; 
    }

    /**
    * Get bancoNafId
    *
    * @return int
    */		

    public function getBancoNafId(){
        return $this->bancoNafId; 
    }

    /**
    * Set bancoNafId
    *
    * @param int $bancoNafId
    */
    public function setBancoNafId($bancoNafId)
    {
        $this->bancoNafId = $bancoNafId;
    }


    /**
    * Get noCuentaBancoNaf
    *
    * @return string
    */		

    public function getNoCuentaBancoNaf(){
        return $this->noCuentaBancoNaf; 
    }

    /**
    * Set noCuentaBancoNaf
    *
    * @param string $noCuentaBancoNaf
    */
    public function setNoCuentaBancoNaf($noCuentaBancoNaf)
    {
        $this->noCuentaBancoNaf = $noCuentaBancoNaf;
    }


    /**
    * Get noCuentaContableNaf
    *
    * @return string
    */		

    public function getNoCuentaContableNaf(){
        return $this->noCuentaContableNaf; 
    }

    /**
    * Set noCuentaContableNaf
    *
    * @param string $noCuentaContableNaf
    */
    public function setNoCuentaContableNaf($noCuentaContableNaf)
    {
        $this->noCuentaContableNaf = $noCuentaContableNaf;
    }


    /**
    * Get noComprobanteDeposito
    *
    * @return string
    */		

    public function getNoComprobanteDeposito(){
        return $this->noComprobanteDeposito; 
    }

    /**
    * Set noComprobanteDeposito
    *
    * @param string $noComprobanteDeposito
    */
    public function setNoComprobanteDeposito($noComprobanteDeposito)
    {
        $this->noComprobanteDeposito = $noComprobanteDeposito;
    }


    /**
    * Get valor
    *
    * @return float
    */		

    public function getValor(){
        return $this->valor; 
    }

    /**
    * Set valor
    *
    * @param float $valor
    */
    public function setValor($valor)
    {
        $this->valor = $valor;
    }


    /**
    * Get feDeposito
    *
    * @return 
    */		

    public function getFeDeposito(){
        return $this->feDeposito; 
    }

    /**
    * Set feDeposito
    *
    * @param  $feDeposito
    */
    public function setFeDeposito($feDeposito)
    {
        $this->feDeposito = $feDeposito;
    }


    /**
    * Get feAnulado
    *
    * @return 
    */		

    public function getFeAnulado(){
        return $this->feAnulado; 
    }

    /**
    * Set feAnulado
    *
    * @param  $feAnulado
    */
    public function setFeAnulado($feAnulado)
    {
        $this->feAnulado = $feAnulado;
    }


    /**
    * Get feProcesado
    *
    * @return 
    */		

    public function getFeProcesado(){
        return $this->feProcesado; 
    }

    /**
    * Set feProcesado
    *
    * @param  $feProcesado
    */
    public function setFeProcesado($feProcesado)
    {
        $this->feProcesado = $feProcesado;
    }


    /**
    * Get feCreacion
    *
    * @return 
    */		

    public function getFeCreacion(){
        return $this->feCreacion; 
    }

    /**
    * Set feCreacion
    *
    * @param  $feCreacion
    */
    public function setFeCreacion($feCreacion)
    {
        $this->feCreacion = $feCreacion;
    }


    /**
    * Get feUltMod
    *
    * @return 
    */		

    public function getFeUltMod(){
        return $this->feUltMod; 
    }

    /**
    * Set feUltMod
    *
    * @param  $feUltMod
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
    * Get usrProcesa
    *
    * @return string
    */		

    public function getUsrProcesa(){
        return $this->usrProcesa; 
    }

    /**
    * Set usrProcesa
    *
    * @param string $usrProcesa
    */
    public function setUsrProcesa($usrProcesa)
    {
        $this->usrProcesa = $usrProcesa;
    }


    /**
    * Get usrAnula
    *
    * @return string
    */		

    public function getUsrAnula(){
        return $this->usrAnula; 
    }

    /**
    * Set usrAnula
    *
    * @param string $usrAnula
    */
    public function setUsrAnula($usrAnula)
    {
        $this->usrAnula = $usrAnula;
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
    * Get cuentaContableId
    *
    * @return int
    */		

    public function getCuentaContableId(){
        return $this->cuentaContableId; 
    }

    /**
    * Set cuentaContableId
    *
    * @param int $cuentaContableId
    */
    public function setCuentaContableId($cuentaContableId)
    {
        $this->cuentaContableId = $cuentaContableId;
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
    * Get contabilizado
    *
    * @return string
    */
    public function getContabilizado()
    {
        return $this->contabilizado;
    }

    /**
    * Set contabilizado
    *
    * @param string $contabilizado
    */
    public function setContabilizado($contabilizado)
    {
        $this->contabilizado = $contabilizado;
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
    public function setOficinaId($oficinaId)
    {
            $this->oficinaId = $oficinaId;
    }    
    
}
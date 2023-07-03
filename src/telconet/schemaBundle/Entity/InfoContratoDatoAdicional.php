<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoContratoDatoAdicional
 *
 * @ORM\Table(name="INFO_CONTRATO_DATO_ADICIONAL")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoContratoDatoAdicionalRepository")
 */
class InfoContratoDatoAdicional
{


/**
* @var InfoContrato
*
* @ORM\ManyToOne(targetEntity="InfoContrato")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="CONTRATO_ID", referencedColumnName="ID_CONTRATO")
* })
*/
		
private $contratoId;

/**
* @var string $esVip
*
* @ORM\Column(name="ES_VIP", type="string", nullable=false)
*/		
     		
private $esVip;

/**
* @var string $esTramiteLegal
*
* @ORM\Column(name="ES_TRAMITE_LEGAL", type="string", nullable=false)
*/		
     		
private $esTramiteLegal;

/**
* @var string $permiteCorteAutomatico
*
* @ORM\Column(name="PERMITE_CORTE_AUTOMATICO", type="string", nullable=false)
*/		
     		
private $permiteCorteAutomatico;

/**
* @var string $fideicomiso
*
* @ORM\Column(name="FIDEICOMISO", type="string", nullable=false)
*/		
     		
private $fideicomiso;

/**
* @var string $convenioPago
*
* @ORM\Column(name="CONVENIO_PAGO", type="string", nullable=false)
*/		
     		
private $convenioPago;

/**
* @var string $notificaPago
*
* @ORM\Column(name="NOTIFICA_PAGO", type="string", nullable=false)
*/		
     		
private $notificaPago;

/**
* @var integer $tiempoEsperaMesesCorte
*
* @ORM\Column(name="TIEMPO_ESPERA_MESES_CORTE", type="integer", nullable=false)
*/		
     		
private $tiempoEsperaMesesCorte;

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
* @var integer $id
*
* @ORM\Column(name="ID_DATO_ADICIONAL", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_CONTRA_DATO_ADI", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* Get contratoId
*
* @return telconet\schemaBundle\Entity\InfoContrato
*/		
     		
public function getContratoId(){
	return $this->contratoId; 
}

/**
* Set contratoId
*
* @param telconet\schemaBundle\Entity\InfoContrato $contratoId
*/
public function setContratoId(\telconet\schemaBundle\Entity\InfoContrato $contratoId)
{
        $this->contratoId = $contratoId;
}


/**
* Get esVip
*
* @return string
*/		
     		
public function getEsVip(){
        if ($this->esVip=="S")
            $variable=true;
        else
            $variable=false;
	//return $this->esVip; 
        return $variable;
}

/**
* Set esVip
*
* @param string $esVip
*/
public function setEsVip($esVip)
{
        $this->esVip = $esVip;
}


/**
* Get esTramiteLegal
*
* @return string
*/		
     		
public function getEsTramiteLegal(){
        if ($this->esTramiteLegal=="S")
            $variable=true;
        else
            $variable=false;
	//return $this->esTramiteLegal; 
        return $variable;
}

/**
* Set esTramiteLegal
*
* @param string $esTramiteLegal
*/
public function setEsTramiteLegal($esTramiteLegal)
{
        $this->esTramiteLegal = $esTramiteLegal;
}


/**
* Get permiteCorteAutomatico
*
* @return string
*/		
     		
public function getPermiteCorteAutomatico(){
        if ($this->permiteCorteAutomatico=="S")
            $variable=true;
        else
            $variable=false;
	//return $this->permiteCorteAutomatico; 
        return $variable;
}

/**
* Set permiteCorteAutomatico
*
* @param string $permiteCorteAutomatico
*/
public function setPermiteCorteAutomatico($permiteCorteAutomatico)
{
        $this->permiteCorteAutomatico = $permiteCorteAutomatico;
}


/**
* Get fideicomiso
*
* @return string
*/		
     		
public function getFideicomiso(){
        if ($this->fideicomiso=="S")
            $variable=true;
        else
            $variable=false;
	//return $this->fideicomiso; 
        return $variable;
}

/**
* Set fideicomiso
*
* @param string $fideicomiso
*/
public function setFideicomiso($fideicomiso)
{
        $this->fideicomiso = $fideicomiso;
}


/**
* Get convenioPago
*
* @return string
*/		
     		
public function getConvenioPago(){
        if ($this->convenioPago=="S")
            $variable=true;
        else
            $variable=false;
	//return $this->convenioPago; 
        return $variable;
}

/**
* Set convenioPago
*
* @param string $convenioPago
*/
public function setConvenioPago($convenioPago)
{
        $this->convenioPago = $convenioPago;
}

/**
* Get notificaPago
*
* @return string
*/		
     		
public function getNotificaPago(){
        if ($this->notificaPago=="S")
            $boolVariable=true;
        else
            $boolVariable=false;
	//return $this->convenioPago; 
        return $boolVariable;
}

/**
* Set notificaPago
*
* @param string $notificaPago
*/
public function setNotificaPago($notificaPago)
{
        $this->notificaPago = $notificaPago;
}


/**
* Get tiempoEsperaMesesCorte
*
* @return integer
*/		
     		
public function getTiempoEsperaMesesCorte(){
	return $this->tiempoEsperaMesesCorte; 
}

/**
* Set tiempoEsperaMesesCorte
*
* @param integer $tiempoEsperaMesesCorte
*/
public function setTiempoEsperaMesesCorte($tiempoEsperaMesesCorte)
{
        $this->tiempoEsperaMesesCorte = $tiempoEsperaMesesCorte;
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
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}
}
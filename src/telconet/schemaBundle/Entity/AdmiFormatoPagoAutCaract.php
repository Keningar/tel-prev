<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiFormatoPagoAutCaract
 *
 * @ORM\Table(name="ADMI_FORMATO_PAGO_AUT_CARACT")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiFormatoPagoAutCaractRepository")
 */

class AdmiFormatoPagoAutCaract
{

    /**
    * @var integer $id
    *
    * @ORM\Column(name="ID_FORMATO_PAGO_AUT_CARACT", type="integer", nullable=false)
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="SEQUENCE")
    * @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_FORM_PAG_AUT_CARACT", allocationSize=1, initialValue=1)
    */		

    private $id;


    /**
    * @var integer $formatoPagoAutomaticoId
    *
    * @ORM\Column(name="FORMATO_PAGO_AUTOMATICO_ID", type="integer", nullable=false)
    */		

    private $formatoPagoAutomaticoId;


    /**
    * @var integer $empresaCod
    *
    * @ORM\Column(name="EMPRESA_COD", type="string", nullable=false)
    */		

    private $empresaCod;


    /**
    * @var integer $caracteristicaId
    *
    * @ORM\Column(name="CARACTERISTICA_ID", type="integer", nullable=false)
    */		

    private $caracteristicaId;


    /**
    * @var string $valor
    *
    * @ORM\Column(name="VALOR", type="string", nullable=false)
    */		

    private $valor;


    /**
    * @var string $observacion
    *
    * @ORM\Column(name="OBSERVACION", type="string", nullable=true)
    */		

    private $observacion;


    /**
    * @var string $estado
    *
    * @ORM\Column(name="ESTADO", type="string", nullable=false)
    */		

    private $estado;
    
    /**
    * @var datetime $feCreacion
    *
    * @ORM\Column(name="FE_CREACION", type="datetime", nullable=false)
    */		

    private $feCreacion;
    
    /**
    * @var string $usrCreacion
    *
    * @ORM\Column(name="USR_CREACION", type="string", nullable=false)
    */		

    private $usrCreacion;
    
    /**
    * @var string $ipCreacion
    *
    * @ORM\Column(name="IP_CREACION", type="string", nullable=true)
    */		

    private $ipCreacion;    

    /**
    * @var datetime $feUltMod
    *
    * @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=true)
    */		

    private $feUltMod;
    
    /**
    * @var string $usrUltMod
    *
    * @ORM\Column(name="USR_ULT_MOD", type="string", nullable=true)
    */		

    private $usrUltMod;
    
    /**
    * @var string $ipUltMod
    *
    * @ORM\Column(name="IP_ULT_MOD", type="string", nullable=true)
    */		

    private $ipUltMod;       

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
    * Get formatoPagoAutomaticoId
    *
    * @return integer
    */		

    public function getFormatoPagoAutomaticoId()
    {
        return $this->formatoPagoAutomaticoId; 
    }


    /**
    * Get empresaCod
    *
    * @return integer
    */

    public function getEmpresaCod()
    {
        return $this->empresaCod;
    }


    /**
    * Get caracteristicaId
    *
    * @return integer
    */

    public function getCaracteristicaId()
    {
        return $this->caracteristicaId;
    }


    /**
    * Get valor
    *
    * @return string
    */		

    public function getValor()
    {
        return $this->valor;
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
    * Get estado
    *
    * @return string
    */

    public function getEstado()
    {
        return $this->estado;
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
    * Get usrCreacion
    *
    * @return string
    */		

    public function getUsrCreacion(){
        return $this->usrCreacion; 
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
    * Get feUltMod
    *
    * @return datetime
    */		

    public function getFeUltMod(){
        return $this->feUltMod; 
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
    * Get ipUltMod
    *
    * @return string
    */		

    public function getIpUltMod(){
        return $this->ipUltMod; 
    }    
    
    /**
    * Set formatoPagoAutomaticoId
    *
    * @param integer $formatoPagoAutomaticoId
    */

    public function setFormatoPagoAutomaticoId($formatoPagoAutomaticoId)
    {
        $this->formatoPagoAutomaticoId = $formatoPagoAutomaticoId;
    }


    /**
    * Set empresaCod
    *
    * @param integer $empresaCod
    */

    public function setEmpresaCod($empresaCod)
    {
        $this->empresaCod = $empresaCod;
    }


    /**
    * Set caracteristicaId
    *
    * @param integer $caracteristicaId
    */

    public function setCaracteristicaId($caracteristicaId)
    {
        $this->caracteristicaId = $caracteristicaId;
    }


    /**
    * Set valor
    *
    * @param string $valor
    */

    public function setValor($valor)
    {
        $this->valor = $valor;
    }


    /**
    * Set proceso
    *
    * @param string $proceso
    */

    public function setObservacion($observacion)
    {
        $this->observacion = $observacion;
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
    * Set feCreacion
    *
    * @param datetime $feCreacion
    */
    public function setFeCreacion($feCreacion)
    {
        $this->feCreacion = $feCreacion;
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
    * Set ipCreacion
    *
    * @param string $ipCreacion
    */

    public function setIpCreacion($ipCreacion)
    {
        $this->ipCreacion = $ipCreacion;
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
    * Set usrUltMod
    *
    * @param string $usrUltMod
    */

    public function setUsrUltMod($usrUltMod)
    {
        $this->usrUltMod = $usrUltMod;
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
    

}

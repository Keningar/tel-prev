<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoDebitoGeneralCaract
 *
 * @ORM\Table(name="INFO_DEBITO_GENERAL_CARACT")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoDebitoGeneralCaractRepository")
 */
class InfoDebitoGeneralCaract
{

    /**
    * @var integer $id
    * 
    * @ORM\Column(name="ID_CLIENTE_CARACT", type="integer", nullable=false)
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="SEQUENCE")
    * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_DEBITO_GENERAL_CARACT", allocationSize=1, initialValue=1)
    */		

    private $id;
    
    /**
    * @var integer $debitoGeneralId
    *
    * @ORM\Column(name="DEBITO_GENERAL_ID", type="integer", nullable=false)
    */

    private $debitoGeneralId;

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
    * @var string $empresaCod
    *
    * @ORM\Column(name="EMPRESA_COD", type="string", nullable=false)
    */ 

    private $empresaCod;

    /**
    * @var string $estado
    *
    * @ORM\Column(name="ESTADO", type="string", nullable=true)
    */		

    private $estado;

    /**
    * @var datetime $feCreacion
    *
    * @ORM\Column(name="FE_CREACION", type="datetime", nullable=true)
    */		

    private $feCreacion;

    /**
    * @var string $usrCreacion
    *
    * @ORM\Column(name="USR_CREACION", type="string", nullable=true)
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
    * @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=false)
    */		

    private $feUltMod;

    /**
    * @var string $usrUltMod
    *
    * @ORM\Column(name="USR_ULT_MOD", type="string", nullable=false)
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
    public function getId(){
        return $this->id; 
    }

    /**
    * Get debitoGeneralId
    *
    * @return integer
    */		
                
    public function getDebitoGeneralId(){
        return $this->debitoGeneralId; 
    }
    
    /**
    * Set debitoGeneralId
    *
    * @param integer $debitoGeneralId
    */
    public function setDebitoGeneralId($debitoGeneralId)
    {
        $this->debitoGeneralId = $debitoGeneralId;
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
    * Set caracteristicaId
    *
    * @param integer $caracteristicaId
    */
    public function setCaracteristicaId($caracteristicaId)
    {
        $this->caracteristicaId = $caracteristicaId;
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
    * Set valor
    *
    * @param string $valor
    */
    public function setValor($valor)
    {
        $this->valor = $valor;
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
    * Get ipUltMod
    *
    * @return string
    */		
    public function getIpUltMod(){
        return $this->ipUltMod; 
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

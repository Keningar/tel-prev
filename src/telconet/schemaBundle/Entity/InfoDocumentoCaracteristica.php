<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoDocumentoCaracteristica
 *
 * @ORM\Table(name="INFO_DOCUMENTO_CARACTERISTICA")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoDocumentoCaracteristicaRepository")
 */
class InfoDocumentoCaracteristica
{
    /**
    * @var integer $id
    *
    * @ORM\Column(name="ID_DOCUMENTO_CARACTERISTICA", type="integer", nullable=false)
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="SEQUENCE")
    * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_DOCUMENTO_CARACT", allocationSize=1, initialValue=1)
    */		
    private $id;	
    
	
    /**
    * @var InfoDocumentoFinancieroCab
    *
    * @ORM\ManyToOne(targetEntity="InfoDocumentoFinancieroCab")
    * @ORM\JoinColumns({
    *   @ORM\JoinColumn(name="DOCUMENTO_ID", referencedColumnName="ID_DOCUMENTO")
    * })
    */
    private $documentoId;

    
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
    * @var string $estado
    *
    * @ORM\Column(name="ESTADO", type="string", nullable=false)
    */		
    private $estado;

    
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
    * @var string $ipCreacion
    *
    * @ORM\Column(name="IP_CREACION", type="string", nullable=false)
    */		
    private $ipCreacion;

    
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
    * @var string $ipUltMod
    *
    * @ORM\Column(name="IP_ULT_MOD", type="string", nullable=false)
    */	 		
    private $ipUltMod;

    /**
     * @var integer $documentoCaracteristicaId
     *
     * @ORM\Column(name="DOCUMENTO_CARACTERISTICA_ID", type="integer", nullable=true)
     */
    private $documentoCaracteristicaId;

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
    * Get documentoId
    *
    * @return telconet\schemaBundle\Entity\InfoDocumentoFinancieroCab
    */		   		
    public function getDocumentoId()
    {
        return $this->documentoId; 
    }

    /**
    * Set documentoId
    *
    * @param telconet\schemaBundle\Entity\InfoDocumentoFinancieroCab $documentoId
    */
    public function setDocumentoId(\telconet\schemaBundle\Entity\InfoDocumentoFinancieroCab $documentoId)
    {
        $this->documentoId = $documentoId;
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
    * Get ipUltMod
    *
    * @return string
    */				
    public function getIpUltMod()
    {
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
    
    /**
     * Get documentoCaracteristicaId
     *
     * @return integer
     */
    public function getDocumentoCaracteristicaId()
    {
        return $this->documentoCaracteristicaId;
    }

    /**
     * Set documentoCaracteristicaId
     *
     * @param integer $documentoCaracteristicaId
     */
    public function setDocumentoCaracteristicaId($documentoCaracteristicaId)
    {
        $this->documentoCaracteristicaId = $documentoCaracteristicaId;
    }


}
<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiOid
 *
 * @ORM\Table(name="ADMI_OID")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiOidRepository")
 */
class AdmiOid
{
    /**
    * @var integer $id
    *
    * @ORM\Column(name="ID_OID", type="integer", nullable=false)
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="SEQUENCE")
    * @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_OID", allocationSize=1, initialValue=1)
    */		
    private $id;	

    
    /**
    * @var AdmiMarcaElemento
    *
    * @ORM\ManyToOne(targetEntity="AdmiMarcaElemento")
    * @ORM\JoinColumns({
    *   @ORM\JoinColumn(name="MARCA_ELEMENTO_ID", referencedColumnName="ID_MARCA_ELEMENTO")
    * })
    */
    private $marcaElementoId;

    
    /**
    * @var string $nombreOid
    *
    * @ORM\Column(name="NOMBRE_OID", type="string", nullable=false)
    */		
    private $nombreOid;

    
    /**
    * @var string $descripcionOid
    *
    * @ORM\Column(name="DESCRIPCION_OID", type="string", nullable=true)
    */	
    private $descripcionOid;

    
    /**
    * @var string $oid
    *
    * @ORM\Column(name="OID", type="string", nullable=false)
    */	
    private $oid;

    
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
    * Get id
    *
    * @return integer
    */	
    public function getId()
    {
        return $this->id; 
    }


    /**
    * Get nombreOid
    *
    * @return string
    */		
    public function getNombreOid()
    {
        return $this->nombreOid; 
    }

    /**
    * Set nombreOid
    *
    * @param string $nombreOid
    */
    public function setNombreOid($nombreOid)
    {
        $this->nombreOid = $nombreOid;
    }


    /**
    * Get descripcionOid
    *
    * @return string
    */	
    public function getDescripcionOid()
    {
        return $this->descripcionOid; 
    }

    /**
    * Set descripcionOid
    *
    * @param string $descripcionOid
    */
    public function setDescripcionOid($descripcionOid)
    {
        $this->descripcionOid = $descripcionOid;
    }


    /**
    * Get oid
    *
    * @return string
    */	
    public function getOid()
    {
        return $this->oid; 
    }

    /**
    * Set oid
    *
    * @param string $oid
    */
    public function setOid($oid)
    {
        $this->oid = $oid;
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
    * Get marcaElementoId
    *
    * @return telconet\schemaBundle\Entity\AdmiMarcaElemento
    */		
    public function getMarcaElementoId()
    {
        return $this->marcaElementoId; 
    }

    /**
    * Set marcaElementoId
    *
    * @param telconet\schemaBundle\Entity\AdmiMarcaElemento $marcaElementoId
    */
    public function setMarcaElementoId(\telconet\schemaBundle\Entity\AdmiMarcaElemento $marcaElementoId)
    {
        $this->marcaElementoId = $marcaElementoId;
    }

    
    public function __toString()
    {
        return $this->nombreOid;
    }

}
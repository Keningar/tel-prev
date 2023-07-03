<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace telconet\schemaBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Clase que almacena el historial de cambio de estado
 *
 * @author nnaulal
 */

/**
 * telconet\schemaBundle\Entity\InfoIncidenciaDetHist
 *
 * @ORM\Table(name="INFO_INCIDENCIA_DET_HIST")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoIncidenciaDetHistRepository")
 */
class InfoIncidenciaDetHist
{
    /**
    * @var integer $id
    *
    * @ORM\Column(name="ID_INCIDENCIA_DET_HIST", type="integer", nullable=false)
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="SEQUENCE")
    * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_INCIDENCIA_DET_HIST", allocationSize=1, initialValue=1)
    */		

    private $id;	
    
    /**
    * @var int $detalleIncidenciaId
    *
    * @ORM\Column(name="DETALLE_INCIDENCIA_ID", type="integer", nullable=false)
    */		

    private $detalleIncidenciaId;
    
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
    * @var string $usrUltMod
    *
    * @ORM\Column(name="USR_ULT_MOD", type="string", nullable=true)
    */		

    private $usrUltMod;

    /**
    * @var \DateTime $feCreacion
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
    * @var datetime $feUltMod
    *
    * @ORM\Column(name="IP_CREACION", type="string", nullable=true)
    */		

    private $ipCreacion;

    /**
    * @var datetime $ipUltMod
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
    * Get detalleIncidenciaId
    *
    * @return detalleIncidenciaId
    */
    
    public function getDetalleIncidenciaId()
    {
        return $this->detalleIncidenciaId;
    }

    /**
    * Get estado
    *
    * @return estado
    */
    
    public function getEstado()
    {
        return $this->estado;
    }

    /**
    * Get usrCreacion
    *
    * @return usrCreacion
    */
    
    public function getUsrCreacion()
    {
        return $this->usrCreacion;
    }

    /**
    * Get usrUltMod
    *
    * @return usrUltMod
    */
    
    public function getUsrUltMod()
    {
        return $this->usrUltMod;
    }

    /**
    * Get feCreacion
    *
    * @return feCreacion
    */
    
    public function getFeCreacion()
    {
        return $this->feCreacion;
    }

    /**
    * Get feUltMod
    *
    * @return feUltMod
    */
    
    public function getFeUltMod()
    {
        return $this->feUltMod;
    }

    /**
    * Get ipCreacion
    *
    * @return ipCreacion
    */
    
    public function getIpCreacion()
    {
        return $this->ipCreacion;
    }

    /**
    * Get ipUltMod
    *
    * @return ipUltMod
    */
    
    public function getIpUltMod()
    {
        return $this->ipUltMod;
    }
    
    /**
    * Set id
    *
    * @param string $id
    */

    public function setId($intId)
    {
        $this->id = $intId;
    }

    /**
    * Set detalleIncidenciaId
    *
    * @param string $intDetalleIncidenciaId
    */
    
    public function setDetalleIncidenciaId($intDetalleIncidenciaId)
    {
        $this->detalleIncidenciaId = $intDetalleIncidenciaId;
    }

    /**
    * Set estado
    *
    * @param string $estado
    */
    
    public function setEstado($strEstado)
    {
        $this->estado = $strEstado;
    }

    /**
    * Set usrCreacion
    *
    * @param string $usrCreacion
    */
    
    public function setUsrCreacion($strUsrCreacion)
    {
        $this->usrCreacion = $strUsrCreacion;
    }
    
    /**
    * Set usrUltMod
    *
    * @param string $usrUltMod
    */

    public function setUsrUltMod($strUsrUltMod)
    {
        $this->usrUltMod = $strUsrUltMod;
    }

    /**
    * Set feCreacion
    *
    * @param \DateTime $feCreacion
    */
    
    public function setFeCreacion(\DateTime $strFeCreacion)
    {
        $this->feCreacion = $strFeCreacion;
    }

    /**
    * Set feUltMod
    *
    * @param string $feUltMod
    */
    
    public function setFeUltMod($strFeUltMod)
    {
        $this->feUltMod = $strFeUltMod;
    }

    /**
    * Set ipCreacion
    *
    * @param string $ipCreacion
    */
    
    public function setIpCreacion($strIpCreacion)
    {
        $this->ipCreacion = $strIpCreacion;
    }

     /**
    * Set ipTultMod
    *
    * @param string $ipUltMod
    */
    
    public function setIpUltMod($strIpUltMod)
    {
        $this->ipUltMod = $strIpUltMod;
    }
    
}

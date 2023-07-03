<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace telconet\schemaBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Clase que almacena las incidencias enviadas por ECUCERT
 *
 * @author nnaulal
 */

/**
 * telconet\schemaBundle\Entity\InfoIncidenciaCab
 *
 * @ORM\Table(name="INFO_INCIDENCIA_CAB")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoIncidenciaCabRepository")
 */
class InfoIncidenciaCab
{
    /**
    * @var integer $id
    *
    * @ORM\Column(name="ID_INCIDENCIA", type="integer", nullable=false)
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="SEQUENCE")
    * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_INCIDENCIA_CAB", allocationSize=1, initialValue=1)
    */		

    private $id;	
    
    /**
    * @var int $noTicket
    *
    * @ORM\Column(name="NO_TICKET", type="integer", nullable=false)
    */		

    private $noTicket;
    
    /**
    * @var datetime $fechaTicket
    *
    * @ORM\Column(name="FE_TICKET", type="datetime", nullable=false)
    */		

    private $fechaTicket;
    
    /**
    * @var string $categoria
    *
    * @ORM\Column(name="CATEGORIA", type="string", nullable=false)
    */		

    private $categoria;
    
    /**
    * @var string $subCategoria
    *
    * @ORM\Column(name="SUBCATEGORIA", type="string", nullable=true)
    */		

    private $subCategoria;
    
    /**
    * @var string $estado
    *
    * @ORM\Column(name="ESTADO", type="string", nullable=false)
    */		

    private $estado;
    
    /**
    * @var string $prioridad
    *
    * @ORM\Column(name="PRIORIDAD", type="string", nullable=false)
    */		

    private $prioridad;
    
    /**
    * @var string $subject
    *
    * @ORM\Column(name="SUBJECT", type="string", nullable=false)
    */		

    private $subject;
    
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
    * Get noTicket
    *
    * @return noTicket
    */
    
    public function getNoTicket()
    {
        return $this->noTicket;
    }

    /**
    * Get fechaTicket
    *
    * @return fechaTicket
    */
    
    public function getFechaTicket()
    {
        return $this->fechaTicket;
    }

    /**
    * Get categoria
    *
    * @return categoria
    */
    
    public function getCategoria()
    {
        return $this->categoria;
    }

    /**
    * Get subCategoria
    *
    * @return subCategoria
    */
    
    public function getSubCategoria()
    {
        return $this->subCategoria;
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
    * Get prioridad
    *
    * @return prioridad
    */
    
    public function getPrioridad()
    {
        return $this->prioridad;
    }

    /**
    * Get subject
    *
    * @return subject
    */
    
    public function getSubject()
    {
        return $this->subject;
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
    * Set noTicket
    *
    * @param string $noTicket
    */
    
    public function setNoTicket($strNoTicket)
    {
        $this->noTicket = $strNoTicket;
    }

    /**
    * Set fechaIncidencia
    *
    * @param string $fechaTicket
    */
    
    public function setFechaTicket($strFechaTicket)
    {
        $this->fechaTicket = $strFechaTicket;
    }

    /**
    * Set categoria
    *
    * @param string $categoria
    */
    
    public function setCategoria($strCategoria)
    {
        $this->categoria = $strCategoria;
    }

    /**
    * Set subCategoria
    *
    * @param string $subCategoria
    */
    
    public function setSubCategoria($strSubCategoria)
    {
        $this->subCategoria = $strSubCategoria;
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
    * Set prioridad
    *
    * @param string $prioridad
    */
    
    public function setPrioridad($strPrioridad)
    {
        $this->prioridad = $strPrioridad;
    }

    /**
    * Set subject
    *
    * @param string $subject
    */
    
    public function setSubject($strSubject)
    {
        $this->subject = $strSubject;
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
    * @param string $feCreacion
    */
    
    public function setFeCreacion($strFeCreacion)
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

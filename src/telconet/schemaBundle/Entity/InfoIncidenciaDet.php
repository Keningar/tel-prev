<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace telconet\schemaBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Clase que almacena el detalle de las incidencias enviadas por ECUCERT
 *
 * @author nnaulal
 */

/**
 * telconet\schemaBundle\Entity\InfoIncidenciaDet
 *
 * @ORM\Table(name="INFO_INCIDENCIA_DET")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoIncidenciaDetRepository")
 */
class InfoIncidenciaDet
{
    /**
    * @var integer $id
    *
    * @ORM\Column(name="ID_DETALLE_INCIDENCIA", type="integer", nullable=false)
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="SEQUENCE")
    * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_INCIDENCIA_DET", allocationSize=1, initialValue=1)
    */		

    private $id;	
    
    /**
    * @var int $incidenciaId
    *
    * @ORM\Column(name="INCIDENCIA_ID", type="integer", nullable=false)
    */		

    private $incidenciaId;
    
    /**
    * @var datetime $ip
    *
    * @ORM\Column(name="IP", type="string", nullable=false)
    */		

    private $ip;

    /**
    * @var int $comunicacionId
    *
    * @ORM\Column(name="COMUNICACION_ID", type="integer", nullable=true)
    */		

    private $comunicacionId;
    
    /**
    * @var string $puerto
    *
    * @ORM\Column(name="PUERTO", type="integer", nullable=true)
    */		

    private $puerto;
    
    /**
    * @var string $ipDestino
    *
    * @ORM\Column(name="IP_DEST", type="string", nullable=true)
    */		

    private $ipDestino;
    
    /**
    * @var string $puertoDestino
    *
    * @ORM\Column(name="PUERTO_DEST", type="string", nullable=true)
    */		

    private $puertoDestino;
    
    /**
    * @var string $ipControla
    *
    * @ORM\Column(name="IP_CC", type="string", nullable=true)
    */		

    private $ipControla;
    
    /**
    * @var string $status
    *
    * @ORM\Column(name="STATUS", type="string", nullable=false)
    */		

    private $status;
    
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
    * @var datetime $feIncidencia
    *
    * @ORM\Column(name="FE_INCIDENCIA", type="string", nullable=true)
    */		

    private $feIncidencia;

    /**
    * @var string $estadoGestion
    *
    * @ORM\Column(name="ESTADO_GESTION", type="string", nullable=false)
    */		

    private $estadoGestion;
    
    /**
    * @var string $loginAdicional
    *
    * @ORM\Column(name="LOGIN_ADICIONAL", type="string", nullable=true)
    */		

    private $loginAdicional;
    
    /**
    * @var int $casoId
    *
    * @ORM\Column(name="CASO_ID", type="integer", nullable=true)
    */		

    private $casoId;

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
    * Get incidenciaId
    *
    * @return incidenciaId
    */
    
    public function getIncidenciaId()
    {
        return $this->incidenciaId;
    }

    /**
    * Get ip
    *
    * @return ip
    */
    
    public function getIp()
    {
        return $this->ip;
    }

    /**
    * Get comunicacionId
    *
    * @return comunicacionId
    */
    
    public function getComunicacionId()
    {
        return $this->comunicacionId;
    }

    /**
    * Get puerto
    *
    * @return puerto
    */
    
    public function getPuerto()
    {
        return $this->puerto;
    }

    /**
    * Get ipDestino
    *
    * @return ipDestino
    */
    
    public function getIpDestino()
    {
        return $this->ipDestino;
    }

    /**
    * Get puertoDestino
    *
    * @return puertoDestino
    */
    
    public function getPuertoDestino()
    {
        return $this->puertoDestino;
    }

    /**
    * Get ipControla
    *
    * @return ipControla
    */
    
    public function getIpControla()
    {
        return $this->ipControla;
    }

    /**
    * Get status
    *
    * @return status
    */
    
    public function getStatus()
    {
        return $this->status;
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
    * Get feIncidencia
    *
    * @return feIncidencia
    */
    
    public function getFeIncidencia()
    {
        return $this->feIncidencia;
    }

    /**
    * Get estadoGestion
    *
    * @return estadoGestion
    */
    
    public function getEstadoGestion()
    {
        return $this->estadoGestion;
    }

    /**
    * Get loginAdicional
    *
    * @return loginAdicional
    */
    
    public function getLoginAdicional()
    {
        return $this->loginAdicional;
    }

    /**
    * Get casoId
    *
    * @return casoId
    */
    
    public function getCasoId()
    {
        return $this->casoId;
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
    * Set incidenciaId
    *
    * @param string $intIncidenciaId
    */
    
    public function setIncidenciaId($intIncidenciaId)
    {
        $this->incidenciaId = $intIncidenciaId;
    }

    /**
    * Set ip
    *
    * @param string $strIp
    */
    
    public function setIp($strIp)
    {
        $this->ip = $strIp;
    }

    /**
    * Set  comunicacionId
    *
    * @param string $intComunicacionId
    */
    
    public function setComunicacionId($intComunicacionId)
    {
        $this->comunicacionId = $intComunicacionId;
    }

    /**
    * Set puerto
    *
    * @param string $strPuerto
    */
    
    public function setPuerto($strPuerto)
    {
        $this->puerto = $strPuerto;
    }

    /**
    * Set ipDestino
    *
    * @param string $strIpDestino
    */
    
    public function setIpDestino($strIpDestino)
    {
        $this->ipDestino = $strIpDestino;
    }

    /**
    * Set puertoDestino
    *
    * @param string $strPuertoDestino
    */
    
    public function setPuertoDestino($strPuertoDestino)
    {
        $this->puertoDestino = $strPuertoDestino;
    }

    /**
    * Set ipControla
    *
    * @param string $strIpControla
    */
    
    public function setIpControla($strIpControla)
    {
        $this->ipControla = $strIpControla;
    }

    /**
    * Set status
    *
    * @param string $strStatus
    */
    
    public function setStatus($strStatus)
    {
        $this->status = $strStatus;
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

   /**
    * Set feIncidencia
    *
    * @param string $strFeIncidencia
    */
    
    public function setFeIncidencia($strFeIncidencia)
    {
        $this->feIncidencia   = $strFeIncidencia;
    }

    /**
    * Set estadoGestion
    *
    * @param  $strEstadoGestion
    */
    
    public function setEstadoGestion($strEstadoGestion)
    {
        $this->estadoGestion   = $strEstadoGestion;
    }

    /**
    * Set loginAdicional
    *
    * @param  $strLoginAdicional
    */
    
    public function setLoginAdicional($strLoginAdicional)
    {
        $this->loginAdicional   = $strLoginAdicional;
    }

    /**
    * Set  casoId
    *
    * @param int $intCasoId
    */
    
    public function setCasoId($intCasoId)
    {
        $this->casoId = $intCasoId;
    }

}

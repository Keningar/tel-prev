<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoTransacciones
 *
 * @ORM\Table(name="INFO_TRANSACCIONES")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoTransaccionesRepository")
 */
class InfoTransacciones
{
    /**
    * @var integer $id
    *
    * @ORM\Column(name="ID_TRANSACCION", type="integer", nullable=false)
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="SEQUENCE")
    * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_TRANSACCIONES", allocationSize=1, initialValue=1)
    */		
    private $id;

    /**
    * @var SeguRelacionSistema
    *
    * @ORM\ManyToOne(targetEntity="SeguRelacionSistema")
    * @ORM\JoinColumns({
    *   @ORM\JoinColumn(name="RELACION_SISTEMA_ID", referencedColumnName="ID_RELACION_SISTEMA")
    * })
    */
    private $relacionSistemaId;	

    /**
    * @var string $nombreTransaccion
    *
    * @ORM\Column(name="NOMBRE_TRANSACCION", type="string", nullable=false)
    */		
    private $nombreTransaccion;

    /**
    * @var string $tipoTransaccion
    *
    * @ORM\Column(name="TIPO_TRANSACCION", type="string", nullable=false)
    */		
    private $tipoTransaccion;

    /**
    * @var string $estado
    *
    * @ORM\Column(name="ESTADO", type="string", nullable=false)
    */		
    private $estado;

    /**
    * @var string $empresaId
    *
    * @ORM\Column(name="EMPRESA_ID", type="string", nullable=false)
    */		
    private $empresaId;

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
    * Get nombreTransaccion
    *
    * @return string
    */		
    public function getNombreTransaccion()
    {
        return $this->nombreTransaccion; 
    }

    /**
    * Set nombreTransaccion
    *
    * @param string $nombreTransaccion
    */
    public function setNombreTransaccion($nombreTransaccion)
    {
        $this->nombreTransaccion = $nombreTransaccion;
    }


    /**
    * Get tipoTransaccion
    *
    * @return string
    */		
    public function getTipoTransaccion()
    {
        return $this->tipoTransaccion; 
    }

    /**
    * Set tipoTransaccion
    *
    * @param string $tipoTransaccion
    */
    public function setTipoTransaccion($tipoTransaccion)
    {
        $this->tipoTransaccion = $tipoTransaccion;
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
    * Get empresaId
    *
    * @return string
    */		
    public function getEmpresaId()
    {
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
    * Get relacionSistemaId
    *
    * @return telconet\schemaBundle\Entity\SeguRelacionSistema
    */		
    public function getRelacionSistemaId()
    {
        return $this->relacionSistemaId; 
    }

    /**
    * Set relacionSistemaId
    *
    * @param telconet\schemaBundle\Entity\SeguRelacionSistema $relacionSistemaId
    */
    public function setRelacionSistemaId(\telconet\schemaBundle\Entity\SeguRelacionSistema $relacionSistemaId)
    {
        $this->relacionSistemaId = $relacionSistemaId;
    }
    
}

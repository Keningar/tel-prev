<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoServicioComisionHisto
 *
 * @ORM\Table(name="INFO_SERVICIO_COMISION_HISTO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoServicioComisionHistoRepository")
 */
class InfoServicioComisionHisto
{
    /**
    * @var integer $id
    *
    * @ORM\Column(name="ID_SERVICIO_COMISION_HISTO", type="integer", nullable=false)
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="SEQUENCE")
    * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_SERVICIO_COMI_HISTO", allocationSize=1, initialValue=1)
    */
    private $id;

    /**
    * @var InfoServicioComision
    *
    * @ORM\ManyToOne(targetEntity="InfoServicioComision")
    * @ORM\JoinColumns({
    *   @ORM\JoinColumn(name="SERVICIO_COMISION_ID", referencedColumnName="ID_SERVICIO_COMISION")
    * })
    */
    private $servicioComisionId;

    /**
    * @var InfoServicio
    *
    * @ORM\ManyToOne(targetEntity="InfoServicio")
    * @ORM\JoinColumns({
    *   @ORM\JoinColumn(name="SERVICIO_ID", referencedColumnName="ID_SERVICIO")
    * })
    */
    private $servicioId;

    /**
    * @var AdmiComisionDet
    *
    * @ORM\ManyToOne(targetEntity="AdmiComisionDet")
    * @ORM\JoinColumns({
    *   @ORM\JoinColumn(name="COMISION_DET_ID", referencedColumnName="ID_COMISION_DET")
    * })
    */
    private $comisionDetId;

    /**
    * @var InfoPersonaEmpresaRol
    *
    * @ORM\ManyToOne(targetEntity="InfoPersonaEmpresaRol")
    * @ORM\JoinColumns({
    *   @ORM\JoinColumn(name="PERSONA_EMPRESA_ROL_ID", referencedColumnName="ID_PERSONA_ROL")
    * })
    */
    private $personaEmpresaRolId;

    /**
    * @var integer $comisionVenta
    *
    * @ORM\Column(name="COMISION_VENTA", type="float", nullable=false)
    */
    private $comisionVenta;

    /**
    * @var integer $comisionMantenimiento
    *
    * @ORM\Column(name="COMISION_MANTENIMIENTO", type="float", nullable=true)
    */
    private $comisionMantenimiento;

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
    * @ORM\Column(name="IP_CREACION", type="string", nullable=false)
    */
    private $ipCreacion;

    /**
    * @var string $observacion
    *
    * @ORM\Column(name="OBSERVACION", type="string", nullable=true)
    */
    private $observacion;


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
    * Get servicioComisionId
    *
    * @return telconet\schemaBundle\Entity\InfoServicioComision
    */
    public function getServicioComisionId()
    {
        return $this->servicioComisionId; 
    }

    /**
    * Set servicioComisionId
    *
    * @param telconet\schemaBundle\Entity\InfoServicioComision $servicioComisionId
    */
    public function setServicioComisionId(\telconet\schemaBundle\Entity\InfoServicioComision $servicioComisionId)
    {
        $this->servicioComisionId = $servicioComisionId;
    }

    /**
    * Get servicioId
    *
    * @return telconet\schemaBundle\Entity\InfoServicio
    */
    public function getServicioId()
    {
        return $this->servicioId; 
    }

    /**
    * Set servicioId
    *
    * @param telconet\schemaBundle\Entity\InfoServicio $servicioId
    */
    public function setServicioId(\telconet\schemaBundle\Entity\InfoServicio $servicioId)
    {
        $this->servicioId = $servicioId;
    }

    /**
    * Get comisionDetId
    *
    * @return telconet\schemaBundle\Entity\AdmiComisionDet
    */
    public function getComisionDetId()
    {
        return $this->comisionDetId; 
    }

    /**
    * Set comisionDetId
    *
    * @param telconet\schemaBundle\Entity\AdmiComisionDet $comisionDetId
    */
    public function setComisionDetId(\telconet\schemaBundle\Entity\AdmiComisionDet $comisionDetId)
    {
        $this->comisionDetId = $comisionDetId;
    }

    /**
    * Get personaEmpresaRolId
    *
    * @return telconet\schemaBundle\Entity\InfoPersonaEmpresaRol
    */
    public function getPersonaEmpresaRolId()
    {
        return $this->personaEmpresaRolId; 
    }

    /**
    * Set personaEmpresaRolId
    *
    * @param telconet\schemaBundle\Entity\InfoPersonaEmpresaRol $personaEmpresaRolId
    */
    public function setPersonaEmpresaRolId(\telconet\schemaBundle\Entity\InfoPersonaEmpresaRol $personaEmpresaRolId)
    {
        $this->personaEmpresaRolId = $personaEmpresaRolId;
    }

    /**
    * Get comisionVenta
    *
    * @return integer
    */
    public function getComisionVenta()
    {
        return $this->comisionVenta; 
    }

    /**
    * Set comisionVenta
    *
    * @param integer $comisionVenta
    */
    public function setComisionVenta($comisionVenta)
    {
        $this->comisionVenta = $comisionVenta;
    }

    /**
    * Get comisionMantenimiento
    *
    * @return integer
    */
    public function getComisionMantenimiento()
    {
        return $this->comisionMantenimiento; 
    }

    /**
    * Set comisionMantenimiento
    *
    * @param integer $comisionMantenimiento
    */
    public function setComisionMantenimiento($comisionMantenimiento)
    {
        $this->comisionMantenimiento = $comisionMantenimiento;
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
    * Get observacion
    *
    * @return string
    */

    public function getObservacion()
    {
        return $this->observacion; 
    }

    /**
    * Set observacion
    *
    * @param string $observacion
    */
    public function setObservacion($observacion)
    {
        $this->observacion = $observacion;
    }
}
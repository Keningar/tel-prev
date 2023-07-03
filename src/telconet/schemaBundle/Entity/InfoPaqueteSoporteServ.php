<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoPaqueteSoporteServ
 *
 * @ORM\Table(name="INFO_PAQUETE_SOPORTE_SERV")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoPaqueteSoporteServRepository")
 */
class InfoPaqueteSoporteServ
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID_PAQUETE_SOPORTE_SERV", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_PAQUETE_SOPORTE_SERV", allocationSize=1, initialValue=1)
     */

    private $id;

    /**
     * @var string $paqueteSoporteCabId
     *
     * @ORM\Column(name="PAQUETE_SOPORTE_CAB_ID", type="string", nullable=false)
     */

    private $paqueteSoporteCabId;

    /**
     * @var string $loginPuntoSoporte
     *
     * @ORM\Column(name="LOGIN_PUNTO_SOPORTE", type="string", nullable=false)
     */

    private $loginPuntoSoporte;

    /**
     * @var integer $servicioId
     *
     * @ORM\Column(name="SERVICIO_ID", type="integer", nullable=false)
     */

    private $servicioId;

    /**
     *
     * @var string $loginServicioSoporte
     *      @ORM\Column(name="LOGIN_SERVICIO_SOPORTE", type="string", nullable=true)
     */
    private $loginServicioSoporte;

    /**
     *
     * @var string $permiteActivarPaquete
     *      @ORM\Column(name="PERMITE_ACTIVAR_PAQUETE", type="string", nullable=true)
     */
    private $permiteActivarPaquete;
    
    /**
     *
     * @var string $estado
     *      @ORM\Column(name="ESTADO", type="string", nullable=false)
     */
    private $estado;
    
    /**
     *
     * @var string $usuarioCreacion
     *      @ORM\Column(name="USUARIO_CREACION", type="date", nullable=false)
     */
    private $usuarioCreacion;
    
    /**
     *
     * @var \Date $fechaCreacion
     *      @ORM\Column(name="FECHA_CREACION", type="date", nullable=false)
     */
    private $fechaCreacion;
    
    /**
     *
     * @var string $usuarioModificacion
     *      @ORM\Column(name="USUARIO_MODIFICACION", type="string", nullable=true)
     */
    private $usuarioModificacion;
    
    /**
     *
     * @var \Date $fechaModificacion
     *      @ORM\Column(name="FECHA_MODIFICACION", type="date", nullable=true)
     */
    private $fechaModificacion;









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
     * Get paqueteSoporteCabId
     *
     * @return integer
     */

    public function getPaqueteSoporteCabId()
    {
        return $this->paqueteSoporteCabId;
    }

    /**
     * Set paqueteSoporteCabId
     *
     * @param integer $intPaqueteSoporteCabId
     */
    public function setPaqueteSoporteCabId($intPaqueteSoporteCabId)
    {
        $this->paqueteSoporteCabId = $intPaqueteSoporteCabId;
    }

     /**
     * Get loginPuntoSoporte
     *
     * @return string
     */

    public function getLoginPuntoSoporte()
    {
        return $this->loginPuntoSoporte;
    }

    /**
     * Set loginPuntoSoporte
     *
     * @param string $strLoginPuntoSoporte
     */
    public function setLoginPuntoSoporte($strLoginPuntoSoporte)
    {
        $this->loginPuntoSoporte = $strLoginPuntoSoporte;
    }

    /**
     * Get servicioId
     *
     * @return integer
     */

    public function getServicioId()
    {
        return $this->servicioId;
    }

    /**
     * Set servicioId
     *
     * @param integer $strServicioId
     */
    public function setServicioId($strServicioId)
    {
        $this->servicioId = $strServicioId;
    }

    /**
     * Get loginServicioSoporte
     * 
     * @param string
     */
    public function getLoginServicioSoporte()
    {
        return $this->loginServicioSoporte;
    }

    /**
     * Set loginServicioSoporte
     * 
     * @param  string $loginServicioSoporte            
     */
    public function setLoginServicioSoporte($dateLoginServicioSoporte)
    {
        $this->loginServicioSoporte = $dateLoginServicioSoporte;
    }

    /**
     * Get permiteActivarPaquete
     * 
     * @return string
     */
    public function getPermiteActivarPaquete()
    {
        return $this->permiteActivarPaquete;
    }

    /**
     * Set permiteActivarPaquete
     * 
     * @param string $permiteActivarPaquete            
     */
    public function setPermiteActivarPaquete($datePermiteActivarPaquete)
    {
        $this->permiteActivarPaquete = $datePermiteActivarPaquete;
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
    public function setEstado($strEstado)
    {
        $this->estado = $strEstado;
    }

    /**
     * Get usuarioCreacion
     * 
     * @return string
     */
    public function getUsuarioCreacion()
    {
        return $this->usuarioCreacion;
    }

    /**
     * Set usuarioCreacion
     * 
     * @param string $usuarioCreacion            
     */
    public function setUsuarioCreacion($strUsuarioCreacion)
    {
        $this->usuarioCreacion = $strUsuarioCreacion;
    }

    /**
     * Get fechaCreacion
     * 
     * @return \Date
     */
    public function getFechaCreacion()
    {
        return $this->fechaCreacion;
    }

    /**
     * Set fechaCreacion
     * 
     * @param \Date
     */
    public function setFechaCreacion($strFechaCreacion)
    {
        $this->fechaCreacion = $strFechaCreacion;
    }


    /**
     * Get usuarioModificacion
     * 
     * @return string
     */
    public function getUsuarioModificacion()
    {
        return $this->usuarioModificacion;
    }

    /**
     * Set usuarioModificacion
     * 
     * @param string $usuarioModificacion            
     */
    public function setUsuarioModificacion($strUsuarioModificacion)
    {
        $this->usuarioModificacion = $strUsuarioModificacion;
    }

    /**
     * Get fechaModificacion
     * 
     * @return \Date
     */
    public function getFechaModificacion()
    {
        return $this->fechaModificacion;
    }

    /**
     * Set fechaModificacion
     * 
     * @param \Date
     */
    public function setFechaModificacion($strFechaModificacion)
    {
        $this->fechaModificacion = $strFechaModificacion;
    }
}

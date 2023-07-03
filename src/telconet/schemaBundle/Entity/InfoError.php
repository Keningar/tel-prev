<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoError
 *
 * @ORM\Table(name="INFO_ERROR")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoErrorRepository")
 */
class InfoError
{

    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID_ERROR", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_ERROR", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string $aplicacion
     *
     * @ORM\Column(name="APLICACION", type="string", nullable=true)
     */
    private $aplicacion;

    /**
     * @var string $proceso
     *
     * @ORM\Column(name="PROCESO", type="string", nullable=true)
     */
    private $proceso;

    /**
     * @var string $detalleError
     *
     * @ORM\Column(name="DETALLE_ERROR", type="string", nullable=true)
     */
    private $detalleError;

    /**
     * @var string $usrCreacion
     *
     * @ORM\Column(name="USR_CREACION", type="string", nullable=true)
     */
    private $usrCreacion;

    /**
     * @var datetime $feCreacion
     *
     * @ORM\Column(name="FE_CREACION", type="datetime", nullable=true)
     */
    private $feCreacion;
    
    /**
     * @var string $ipCreacion
     *
     * @ORM\Column(name="IP_CREACION", type="string", nullable=true)
     */
    private $ipCreacion;

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
     * Get aplicacion
     *
     * @return 
     */
    public function getAplicacion()
    {
        return $this->aplicacion;
    }

    /**
     * Set aplicacion
     *
     * @param  $aplicacion
     */
    public function setAplicacion($aplicacion)
    {
        $this->aplicacion = $aplicacion;
    }

    /**
     * Get proceso
     *
     * @return 
     */
    public function getProceso()
    {
        return $this->proceso;
    }

    /**
     * Set proceso
     *
     * @param  $proceso
     */
    public function setProceso($proceso)
    {
        $this->proceso = $proceso;
    }

    /**
     * Get detalleError
     *
     * @return 
     */
    public function getDetalleError()
    {
        return $this->detalleError;
    }

    /**
     * Set detalleError
     *
     * @param  detalleError
     */
    public function setDetalleError($detalleError)
    {
        $this->detalleError = $detalleError;
    }

    /**
     * Get usrCreacion
     *
     * @return 
     */
    public function getUsrCreacion()
    {
        return $this->usrCreacion;
    }

    /**
     * Set usrCreacion
     *
     * @param  usrCreacion
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
     * @return 
     */
    public function getIpCreacion()
    {
        return $this->ipCreacion;
    }

    /**
     * Set ipCreacion
     *
     * @param  ipCreacion
     */
    public function setIpCreacion($ipCreacion)
    {
        $this->ipCreacion = $ipCreacion;
    }
}

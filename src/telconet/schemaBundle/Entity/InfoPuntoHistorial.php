<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoPuntoHistorial
 *
 * @ORM\Table(name="INFO_PUNTO_HISTORIAL")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoPuntoHistorialRepository")
 */
class InfoPuntoHistorial
{

    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID_PUNTO_HISTORIAL", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_PUNTO_HISTORIAL", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var InfoPunto
     *
     * @ORM\ManyToOne(targetEntity="InfoPunto")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="PUNTO_ID", referencedColumnName="ID_PUNTO", nullable=false)
     * })
     */
    private $puntoId;

    /**
     * @var string $valor
     *
     * @ORM\Column(name="VALOR", type="string", nullable=true)
     */
    private $valor;

    /**
     * @var string $accion
     *
     * @ORM\Column(name="ACCION", type="string", nullable=true)
     */
    private $accion;

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
     * Get puntoId
     *
     * @return InfoPunto
     */
    public function getPuntoId()
    {
        return $this->puntoId;
    }
    
    /**
     * Set puntoId
     *
     * @param InfoPunto $puntoId
     */
    public function setPuntoId(InfoPunto $puntoId)
    {
        $this->puntoId = $puntoId;
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
     * @param string $accion
     */
    public function setValor($accion)
    {
        $this->valor = $accion;
    }

    /**
     * Get accion
     *
     * @return string
     */
    public function getAccion()
    {
        return $this->accion;
    }

    /**
     * Set accion
     *
     * @param string $accion
     */
    public function setAccion($accion)
    {
        $this->accion = $accion;
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
  
}

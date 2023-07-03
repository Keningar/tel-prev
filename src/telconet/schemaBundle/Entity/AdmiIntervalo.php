<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiIntervalo
 *
 * @ORM\Table(name="ADMI_INTERVALO")
 * @ORM\Entity
 *
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiIntervaloRepository")
 */
class AdmiIntervalo
{

    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID_INTERVALO", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_INTERVALO", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var datetime $horaIni
     *
     * @ORM\Column(name="HORA_INI", type="datetime", nullable=false)
     */
    private $horaIni;

    /**
     * @var datetime $horaFin
     *
     * @ORM\Column(name="HORA_FIN", type="datetime", nullable=false)
     */
    private $horaFin;

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
     * @var string $usrModificacion
     *
     * @ORM\Column(name="USR_MODIFICACION", type="string", nullable=false)
     */
    private $usrModificacion;

    /**
     * @var datetime $feModificacion
     *
     * @ORM\Column(name="FE_MODIFICACION", type="datetime", nullable=false)
     */
    private $feModificacion;

    /**
     * @var string $ipModificacion
     *
     * @ORM\Column(name="IP_MODIFICACION", type="string", nullable=false)
     */
    private $ipModificacion;

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
     * Get horaIni
     *
     * @return horaIni
     */
    public function getHoraIni()
    {
        return $this->horaIni;
    }

    /**
     * Set horaIni
     *
     * @param string $horaIni
     */
    public function setHoraIni($horaIni)
    {
        $this->horaIni = $horaIni;
    }

    /**
     * Get horaFin
     *
     * @return string
     */
    public function getHoraFin()
    {
        return $this->horaFin;
    }

    /**
     * Set horaFin
     *
     * @param string $horaFin
     */
    public function setHoraFin($horaFin)
    {
        $this->horaFin = $horaFin;
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
     * Get usrModificacion
     *
     * @return string
     */
    public function getUsrModificacion()
    {
        return $this->usrModificacion;
    }

    /**
     * Set usrModificacion
     *
     * @param string $usrModificacion
     */
    public function setUsrModificacion($usrModificacion)
    {
        $this->usrModificacion = $usrModificacion;
    }

    /**
     * Get feModificacion
     *
     * @return datetime
     */
    public function getFeModificacion()
    {
        return $this->feModificacion;
    }

    /**
     * Set feModificacion
     *
     * @param datetime $feModificacion
     */
    public function setFeModificacion($feModificacion)
    {
        $this->feModificacion = $feModificacion;
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
     * Get ipModificacion
     *
     * @return string
     */
    public function getIpModificacion()
    {
        return $this->ipModificacion;
    }

    /**
     * Set ipModificacion
     *
     * @param string $ipModificacion
     */
    public function setIpModificacion($ipModificacion)
    {
        $this->ipModificacion = $ipModificacion;
    }
    
}

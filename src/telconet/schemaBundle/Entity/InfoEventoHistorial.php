<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoEventoHistorial
 *
 * @ORM\Table(name="INFO_EVENTO_HISTORIAL")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoEventoHistorial")
 */
class InfoEventoHistorial
{
/**
* @var integer $id
*
* @ORM\Column(name="ID_EVENTO_HISTORIAL", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_EVENTO_HISTORIAL", allocationSize=1, initialValue=1)
*/		
		
private $id;

/**
 * @var InfoEvento
 *
 * @ORM\ManyToOne(targetEntity="InfoEvento")
 * @ORM\JoinColumns({
 *   @ORM\JoinColumn(name="EVENTO_ID", referencedColumnName="ID_EVENTO")
 * })
*/
private $eventoId;


/**
 * @var string $observacion
 *
 * @ORM\Column(name="OBSERVACION", type="string", nullable=true)
 */

private $observacion;

/**
 * @var string $estado
 *
 * @ORM\Column(name="ESTADO", type="string", nullable=false)
 */

private $estado;

/**
 * @var string usrCreacion
 * @ORM\Column(name="USR_CREACION",type="string",nullable=true)
 *
 */
private  $usrCreacion;
/**
 * @var string usrUltMod
 * @ORM\Column(name="USR_ULT_MOD",type="string",nullable=true)
 *
 */
private  $usrUltMod;


/**
 * @var datetime $feCreacion
 *
 * @ORM\Column(name="FE_CREACION", type="datetime", nullable=false)
 */

private $feCreacion;


/**
 * @var datetime $feUltMod
 *
 * @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=false)
 */

private $feUltMod;

/**
 * @var string $ipCreacion
 *
 * @ORM\Column(name="IP_CREACION", type="string", nullable=false)
 */
private $ipCreacion;

/**
 * @var string $ipUltMod
 *
 * @ORM\Column(name="IP_ULT_MOD", type="string", nullable=true)
 */
private $ipUltMod;

    /**
     * @return InfoEvento
     *
     */
    public function getEventoId()
    {
        return $this->eventoId;
    }

    /**
     * @param InfoEvento $eventoId
     */
    public function setEventoId($eventoId)
    {
        $this->eventoId = $eventoId;
    }

    /**
     * @return string
     */
    public function getObservacion()
    {
        return $this->observacion;
    }

    /**
     * @param string $observacion
     */
    public function setObservacion($observacion)
    {
        $this->observacion = $observacion;
    }

    /**
     * @return string
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * @param string $estado
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    /**
     * @return string
     */
    public function getUsrCreacion()
    {
        return $this->usrCreacion;
    }

    /**
     * @param string $usrCreacion
     */
    public function setUsrCreacion($usrCreacion)
    {
        $this->usrCreacion = $usrCreacion;
    }

    /**
     * @return string
     */
    public function getUsrUltMod()
    {
        return $this->usrUltMod;
    }

    /**
     * @param string $usrUltMod
     */
    public function setUsrUltMod($usrUltMod)
    {
        $this->usrUltMod = $usrUltMod;
    }

    /**
     * @return datetime
     */
    public function getFeCreacion()
    {
        return $this->feCreacion;
    }

    /**
     * @param datetime $feCreacion
     */
    public function setFeCreacion($feCreacion)
    {
        $this->feCreacion = $feCreacion;
    }

    /**
     * @return datetime
     */
    public function getFeUltMod()
    {
        return $this->feUltMod;
    }

    /**
     * @param datetime $feUltMod
     */
    public function setFeUltMod($feUltMod)
    {
        $this->feUltMod = $feUltMod;
    }

    /**
     * @return string
     */
    public function getIpCreacion()
    {
        return $this->ipCreacion;
    }

    /**
     * @param string $ipCreacion
     */
    public function setIpCreacion($ipCreacion)
    {
        $this->ipCreacion = $ipCreacion;
    }

    /**
     * @return string
     */
    public function getIpUltMod()
    {
        return $this->ipUltMod;
    }

    /**
     * @param string $ipUltMod
     */
    public function setIpUltMod($ipUltMod)
    {
        $this->ipUltMod = $ipUltMod;
    }

/**
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}


}
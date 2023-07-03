<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoPuntoCaracteristica
 *
 * @ORM\Table(name="INFO_PUNTO_CARACTERISTICA")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoPuntoCaracteristicaRepository")
 */
class InfoPuntoCaracteristica
{

    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID_PUNTO_CARACTERISTICA", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_PUNTO_CARACTERISTICA", allocationSize=1, initialValue=1)
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
     * @var AdmiCaracteristica
     *
     * @ORM\ManyToOne(targetEntity="AdmiCaracteristica")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="CARACTERISTICA_ID", referencedColumnName="ID_CARACTERISTICA", nullable=false)
     * })
     */
    private $caracteristicaId;

    /**
     * @var string $valor
     *
     * @ORM\Column(name="VALOR", type="string", nullable=false)
     */
    private $valor;

    /**
     * @var string $feCreacion
     *
     * @ORM\Column(name="FE_CREACION", type="datetime", nullable=false)
     */
    private $feCreacion;

    /**
     * @var datetime $feUltMod
     *
     * @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=true)
     */
    private $feUltMod;

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
     * @var string $ipCreacion
     *
     * @ORM\Column(name="IP_CREACION", type="string", nullable=false)
     */
    private $ipCreacion;

    /**
     * @var string $estado
     *
     * @ORM\Column(name="ESTADO", type="string", nullable=false)
     */
    private $estado;

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
    * Set Id
    *
    * @param integer $id
    */
    public function setId($id)
    {
        $this->id = $id;
    }
    
    
     /**
     * Get puntoId
     *
     * @return telconet\schemaBundle\Entity\InfoPunto
     */
    public function getPuntoId()
    {
        return $this->puntoId;
    }

    /**
    * Set puntoId
    *
    * @param telconet\schemaBundle\Entity\InfoPunto $puntoId
    */
    public function setPuntoId(\telconet\schemaBundle\Entity\InfoPunto $puntoId)
    {
        $this->puntoId = $puntoId;
    }
    
     /**
     * Get caracteristicaId
     *
     * @return telconet\schemaBundle\Entity\AdmiCaracteristica
     */
    public function getCaracteristicaId()
    {
        return $this->caracteristicaId;
    }
    

    /**
    * Set caracteristicaId
    *
    * @param telconet\schemaBundle\Entity\AdmiCaracteristica $caracteristicaId
    */
    public function setCaracteristicaId(\telconet\schemaBundle\Entity\AdmiCaracteristica $caracteristicaId)
    {
        $this->caracteristicaId = $caracteristicaId;
    }

    /**
     * Get Valor
     *
     * @return string
     */
    public function getValor()
    {
        return $this->valor;
    }
    

    /**
    * Set Valor
    *
    * @param string $valor
    */
    public function setValor($valor)
    {
        $this->valor = $valor;
    }
    
    
     /**
     * Get FeCreacion
     *
     * @return date
     */
    public function getFeCreacion()
    {
        return $this->feCreacion;
    }

    /**
    * Set FeCreacion
    *
    * @param date $feCreacion
    */
    public function setFeCreacion($feCreacion)
    {
        $this->feCreacion = $feCreacion;
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
    * Get UsrCreacion
    *
    * @return string
    */
    public function getUsrCreacion()
    {
        return $this->usrCreacion;
    }
    

    /**
    * Set UsrCreacion
    *
    * @param string $usrCreacion
    */
    public function setUsrCreacion($usrCreacion)
    {
        $this->usrCreacion = $usrCreacion;
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
    * Get IpCreacion
    *
    * @return string
    */
    public function getIpCreacion()
    {
        return $this->ipCreacion;
    }
    
    /**
    * Set IpCreacion
    *
    * @param string $ipCreacion
    */
    public function setIpCreacion($ipCreacion)
    {
        $this->ipCreacion = $ipCreacion;
    }
    
    /**
    * Get Estado
    *
    * @return string
    */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
    * Set Estado
    *
    * @param string $estado
    */
    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

}

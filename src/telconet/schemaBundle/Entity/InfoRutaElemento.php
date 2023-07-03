<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoRutaEstatica
 *
 * @ORM\Table(name="INFO_RUTA_ELEMENTO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoRutaElementoRepository")
 */
class InfoRutaElemento
{

    /**
    * @var integer $id
    *
    * @ORM\Column(name="ID_RUTA_ELEMENTO", type="integer", nullable=false)
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="SEQUENCE")
    * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_RUTA_ELEMENTO", allocationSize=1, initialValue=1)
    */		
    private $id;	
    
    /**
    * @var InfoElemento $elementoId
    *
    * @ORM\ManyToOne(targetEntity="InfoElemento")
    * @ORM\JoinColumns({
    *   @ORM\JoinColumn(name="ELEMENTO_ID", referencedColumnName="ID_ELEMENTO")
    * })
    */
    private $elementoId;
    
    /**
    * @var InfoServicio $servicioId
    *
    * @ORM\ManyToOne(targetEntity="InfoServicio")
    * @ORM\JoinColumns({
    *   @ORM\JoinColumn(name="SERVICIO_ID", referencedColumnName="ID_SERVICIO")
    * })
    */	
    private $servicioId;
    
    /**
    * @var InfoSubred $subredId
    *
    * @ORM\ManyToOne(targetEntity="InfoSubred")
    * @ORM\JoinColumns({
    *   @ORM\JoinColumn(name="SUBRED_ID", referencedColumnName="ID_SUBRED", nullable=true)
    * })
    */
    private $subredId;
    
    /**
    * @var InfoIp $ipId
    *
    * @ORM\ManyToOne(targetEntity="InfoIp")
    * @ORM\JoinColumns({
    *   @ORM\JoinColumn(name="IP_ID", referencedColumnName="ID_IP")
    * })
    */	
    private $ipId;

    /**
    * @var string $nombre
    *
    * @ORM\Column(name="NOMBRE", type="string", nullable=false)
    */		
    private $nombre;
    
    /**
    * @var string $estado
    *
    * @ORM\Column(name="ESTADO", type="string", nullable=false)
    */		
    private $estado;
    
    /**
    * @var string $tipo
    *
    * @ORM\Column(name="tipo", type="string", nullable=false)
    */		
    private $tipo;

    /**
    * @var string $redLan
    *
    * @ORM\Column(name="RED_LAN", type="string", nullable=false)
    */		
    private $redLan;

    /**
    * @var string $mascaraRedLan
    *
    * @ORM\Column(name="MASCARA_RED_LAN", type="string", nullable=true)
    */		
    private $mascaraRedLan;
    
    /**
    * @var int $distanciaAdmin
    *
    * @ORM\Column(name="DISTANCIA_ADMIN", type="string", nullable=true)
    */		
    private $distanciaAdmin;

    /**
    * @var string $usrCreacion
    *
    * @ORM\Column(name="USR_CREACION", type="string", nullable=true)
    */		
    private $usrCreacion;

    /**
    * @var $feCreacion
    *
    * @ORM\Column(name="FE_CREACION", type="datetime", nullable=false)
    */		
    private $feCreacion;
    
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
    * @var string $ipCreacion
    *
    * @ORM\Column(name="IP_CREACION", type="string", nullable=false)
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
    * Set elementoId
    *
    * @param telconet\schemaBundle\Entity\InfoElemento $elementoId
    */
    public function setElementoId($elementoId)
    {
        $this->elementoId = $elementoId;
    
        return $this;
    }

    /**
    * Get elementoId
    *
    * @return telconet\schemaBundle\Entity\InfoElemento
    */
    public function getElementoId()
    {
        return $this->elementoId;
    }

    /**
    * Set servicioId
    *
    * @param telconet\schemaBundle\Entity\InfoServicio $servicioId
    */
    public function setServicioId($servicioId)
    {
        $this->servicioId = $servicioId;
    
        return $this;
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
    * Set subredId
    *
    * @param telconet\schemaBundle\Entity\InfoSubred
    */
    public function setSubredId($subredId)
    {
        $this->subredId = $subredId;
    
        return $this;
    }

    /**
    * Get subredId
    *
    * @return telconet\schemaBundle\Entity\InfoSubred
    */
    public function getSubredId()
    {
        return $this->subredId;
    }
    
    /**
     * Set ipId
     *
     * @param telconet\schemaBundle\Entity\InfoIp
     * @return InfoRutaElemento
     */
    public function setIpId($ipId)
    {
        $this->ipId = $ipId;
        return $this;
    }

    /**
    * Get ipId
    *
    * @return telconet\schemaBundle\Entity\InfoIp
    */
    public function getIpId()
    {
        return $this->ipId;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return InfoRutaElemento
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    
        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
    }
    
    /**
     * Set estado
     *
     * @param string $estado
     * @return InfoRutaEstatica
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
    
        return $this;
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
     * Set tipo
     *
     * @param string $tipo
     * @return InfoRutaEstatica
     */
    public function setTipo($tipo)
    {
        $this->tipo= $tipo;
    
        return $this;
    }

    /**
     * Get tipo
     *
     * @return string 
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set redLan
     *
     * @param string $redLan
     * @return InfoRutaEstatica
     */
    public function setRedLan($redLan)
    {
        $this->redLan = $redLan;
    
        return $this;
    }

    /**
     * Get redLan
     *
     * @return string 
     */
    public function getRedLan()
    {
        return $this->redLan;
    }

    /**
     * Set mascaraRedLan
     *
     * @param string $mascaraRedLan
     * @return InfoRutaEstatica
     */
    public function setMascaraRedLan($mascaraRedLan)
    {
        $this->mascaraRedLan = $mascaraRedLan;
    
        return $this;
    }

    /**
     * Get mascaraRedLan
     *
     * @return string 
     */
    public function getMascaraRedLan()
    {
        return $this->mascaraRedLan;
    }
    
     /**
     * Set distanciaAdmin
     *
     * @param string $distanciaAdmin
     * @return InfoRutaElemento
     */
    public function setDistanciaAdmin($distanciaAdmin)
    {
        $this->distanciaAdmin = $distanciaAdmin;
    
        return $this;
    }

    /**
     * Get distanciaAdmin
     *
     * @return string 
     */
    public function getDistanciaAdmin()
    {
        return $this->distanciaAdmin;
    }    
            
    /**
     * Set usrCreacion
     *
     * @param string $usrCreacion
     * @return InfoRutaEstatica
     */
    public function setUsrCreacion($usrCreacion)
    {
        $this->usrCreacion = $usrCreacion;
        return $this;
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
     * Set feCreacion
     *
     * @param \DateTime $feCreacion
     * @return InfoRutaEstatica
     */
    public function setFeCreacion($feCreacion)
    {
        $this->feCreacion = $feCreacion;
        return $this;
    }

    /**
     * Get feCreacion
     *
     * @return \DateTime 
     */
    public function getFeCreacion()
    {
        return $this->feCreacion;
    }
    
    /**
     * Set usrUltMod
     *
     * @param string $usrUltMod
     * @return InfoRutaElemento
     */
    public function setUsrUltMod($usrUltMod)
    {
        $this->usrUltMod = $usrUltMod;
        return $this;
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
     * Set feUltMod
     *
     * @param \DateTime $feUltMod
     * @return InfoRutaElemento
     */
    public function setFeUltMod($feUltMod)
    {
        $this->feUltMod = $feUltMod;
        return $this;
    }

    /**
     * Get feUltMod
     *
     * @return \DateTime 
     */
    public function getFeUltMod()
    {
        return $this->feUltMod;
    }
    
    /**
    * Get ipCreacion
    *
    * @return string
    */		

    public function getIpCreacion(){
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
     * toString
     *
     * @return string
     */
    public function __toString()
    {
        return $this->nombre;
    }
}
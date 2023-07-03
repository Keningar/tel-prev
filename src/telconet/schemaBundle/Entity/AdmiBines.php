<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiBines
 *
 * @ORM\Table(name="ADMI_BINES")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiBinesRepository")
 */
class AdmiBines
{

    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID_BIN", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_BINES", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string $binAntiguo
     *
     * @ORM\Column(name="BIN_ANTIGUO", type="string", nullable=false)
     */
    private $binAntiguo;

    /**
     * @var string $binNuevo
     *
     * @ORM\Column(name="BIN_NUEVO", type="string", nullable=false)
     */
    private $binNuevo;

    /**
     * @var string $bancoTipoCuentaId
     *
     * @ORM\Column(name="BANCO_TIPO_CUENTA_ID", type="integer", nullable=false)
     */
    private $bancoTipoCuentaId;

    /**
     * @var string $descripcion
     *
     * @ORM\Column(name="DESCRIPCION", type="string", nullable=false)
     */
    private $descripcion;

    /**
     * @var string $banco
     *
     * @ORM\Column(name="BANCO", type="string", nullable=false)
     */
    private $banco;

    /**
     * @var string $tarjeta
     *
     * @ORM\Column(name="TARJETA", type="string", nullable=false)
     */
    private $tarjeta;

    /**
     * @var AdmiMotivo $motivoId
     *
     * @ORM\ManyToOne(targetEntity="AdmiMotivo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="MOTIVO_ID", referencedColumnName="ID_MOTIVO")
     * })
     */ 
    private $motivoId;

    /**
     * @var string $motivoDescripcion
     *
     * @ORM\Column(name="MOTIVO_DESCRIPCION", type="string", nullable=true)
     */
    private $motivoDescripcion;

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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set binAntiguo
     *
     * @param string $binAntiguo
     */
    public function setBinAntiguo($binAntiguo)
    {
        $this->binAntiguo = $binAntiguo;
    }

    /**
     * Get binAntiguo
     *
     * @return string 
     */
    public function getBinAntiguo()
    {
        return $this->binAntiguo;
    }

    /**
     * Set binNuevo
     *
     * @param string $binNuevo
     */
    public function setBinNuevo($binNuevo)
    {
        $this->binNuevo = $binNuevo;
    }

    /**
     * Get binNuevo
     *
     * @return string 
     */
    public function getBinNuevo()
    {
        return $this->binNuevo;
    }

    /**
     * Set bancoTipoCuentaId
     *
     * @param integer $bancoTipoCuentaId
     */
    public function setBancoTipoCuentaId($bancoTipoCuentaId)
    {
        $this->bancoTipoCuentaId = $bancoTipoCuentaId;
    }

    /**
     * Get bancoTipoCuentaId
     *
     * @return integer 
     */
    public function getBancoTipoCuentaId()
    {
        return $this->bancoTipoCuentaId;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    }

    /**
     * Get descripcion
     *
     * @return string 
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set banco
     *
     * @param string $banco
     */
    public function setBanco($banco)
    {
        $this->banco = $banco;
    }

    /**
     * Get banco
     *
     * @return string 
     */
    public function getBanco()
    {
        return $this->banco;
    }

    /**
     * Set tarjeta
     *
     * @param string $tarjeta
     */
    public function setTarjeta($tarjeta)
    {
        $this->tarjeta = $tarjeta;
    }

    /**
     * Get tarjeta
     *
     * @return string 
     */
    public function getTarjeta()
    {
        return $this->tarjeta;
    }

      /**
     * Get motivoId
     *
     * @return telconet\schemaBundle\Entity\AdmiMotivo
     */
    public function getMotivoId()
    {
        return $this->motivoId;
    }

    /**
     * Set motivoId
     *
     * @param telconet\schemaBundle\Entity\AdmiMotivo $motivoId
     */
    public function setMotivoId(\telconet\schemaBundle\Entity\AdmiMotivo $motivoId)
    {
        $this->motivoId = $motivoId;
    }
    
     /**
     * Get motivoDescripcion
     *
     * @return string
     */
    public function getMotivoDescripcion()
    {
        return $this->motivoDescripcion;
    }

    /**
     * Set motivoDescripcion
     *
     * @param string $motivoDescripcion
     */
    public function setMotivoDescripcion($motivoDescripcion)
    {
        $this->motivoDescripcion = $motivoDescripcion;
    }
    
    /**
      /**
     * Set estado
     *
     * @param string estado
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
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
     * Set usrCreacion
     *
     * @param string $usrCreacion
     */
    public function setUsrCreacion($usrCreacion)
    {
        $this->usrCreacion = $usrCreacion;
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
     */
    public function setFeCreacion($feCreacion)
    {
        $this->feCreacion = $feCreacion;
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
     * Set ipCreacion
     *
     * @param string $ipCreacion
     */
    public function setIpCreacion($ipCreacion)
    {
        $this->ipCreacion = $ipCreacion;
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
     * Set usrUltMod
     *
     * @param string $usrUltMod
     */
    public function setUsrUltMod($usrUltMod)
    {
        $this->usrUltMod = $usrUltMod;
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
     */
    public function setFeUltMod($feUltMod)
    {
        $this->feUltMod = $feUltMod;
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

}
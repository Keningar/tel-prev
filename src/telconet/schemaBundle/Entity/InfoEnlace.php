<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoEnlace
 *
 * @ORM\Table(name="INFO_ENLACE")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoEnlaceRepository")
 */
class InfoEnlace
{

    /**
     * @var float $capacidadFinIni
     *
     * @ORM\Column(name="CAPACIDAD_FIN_INI", type="float", nullable=true)
     */
    private $capacidadFinIni;

    /**
     * @var string $unidadMedidaDown
     *
     * @ORM\Column(name="UNIDAD_MEDIDA_DOWN", type="string", nullable=true)
     */
    private $unidadMedidaDown;

    /**
     * @var string $tipoEnlace
     *
     * @ORM\Column(name="TIPO_ENLACE", type="string", nullable=true)
     */
    private $tipoEnlace;

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
     * @var integer $id
     *
     * @ORM\Column(name="ID_ENLACE", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_ENLACE", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var InfoInterfaceElemento
     *
     * @ORM\ManyToOne(targetEntity="InfoInterfaceElemento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="INTERFACE_ELEMENTO_INI_ID", referencedColumnName="ID_INTERFACE_ELEMENTO")
     * })
     */
    private $interfaceElementoIniId;

    /**
     * @var InfoInterfaceElemento
     *
     * @ORM\ManyToOne(targetEntity="InfoInterfaceElemento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="INTERFACE_ELEMENTO_FIN_ID", referencedColumnName="ID_INTERFACE_ELEMENTO")
     * })
     */
    private $interfaceElementoFinId;

    /**
     * @var AdmiTipoMedio
     *
     * @ORM\ManyToOne(targetEntity="AdmiTipoMedio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="TIPO_MEDIO_ID", referencedColumnName="ID_TIPO_MEDIO")
     * })
     */
    private $tipoMedioId;

    /**
     * @var float $capacidadInput
     *
     * @ORM\Column(name="CAPACIDAD_INPUT", type="float", nullable=true)
     */
    private $capacidadInput;

    /**
     * @var string $unidadMedidaInput
     *
     * @ORM\Column(name="UNIDAD_MEDIDA_INPUT", type="string", nullable=true)
     */
    private $unidadMedidaInput;

    /**
     * @var float $capacidadOutput
     *
     * @ORM\Column(name="CAPACIDAD_OUTPUT", type="float", nullable=true)
     */
    private $capacidadOutput;

    /**
     * @var string $unidadMedidaOutput
     *
     * @ORM\Column(name="UNIDAD_MEDIDA_OUTPUT", type="string", nullable=true)
     */
    private $unidadMedidaOutput;

    /**
     * @var float $capacidadIniFin
     *
     * @ORM\Column(name="CAPACIDAD_INI_FIN", type="float", nullable=true)
     */
    private $capacidadIniFin;

    /**
     * @var string $unidadMedidaUp
     *
     * @ORM\Column(name="UNIDAD_MEDIDA_UP", type="string", nullable=true)
     */
    private $unidadMedidaUp;

    /**
     * @var InfoBufferHilo
     *
     * @ORM\ManyToOne(targetEntity="InfoBufferHilo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="BUFFER_HILO_ID", referencedColumnName="ID_BUFFER_HILO")
     * })
     */
    private $bufferHiloId;

    /**
     * Get capacidadFinIni
     *
     * @return 
     */
    public function getCapacidadFinIni()
    {
        return $this->capacidadFinIni;
    }

    /**
     * Set capacidadFinIni
     *
     * @param  $capacidadFinIni
     */
    public function setCapacidadFinIni($capacidadFinIni)
    {
        $this->capacidadFinIni = $capacidadFinIni;
    }

    /**
     * Get unidadMedidaDown
     *
     * @return string
     */
    public function getUnidadMedidaDown()
    {
        return $this->unidadMedidaDown;
    }

    /**
     * Set unidadMedidaDown
     *
     * @param string $unidadMedidaDown
     */
    public function setUnidadMedidaDown($unidadMedidaDown)
    {
        $this->unidadMedidaDown = $unidadMedidaDown;
    }

    /**
     * Get tipoEnlace
     *
     * @return string
     */
    public function getTipoEnlace()
    {
        return $this->tipoEnlace;
    }

    /**
     * Set tipoEnlace
     *
     * @param string $tipoEnlace
     */
    public function setTipoEnlace($tipoEnlace)
    {
        $this->tipoEnlace = $tipoEnlace;
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get interfaceElementoIniId
     *
     * @return \telconet\schemaBundle\Entity\InfoInterfaceElemento
     */
    public function getInterfaceElementoIniId()
    {
        return $this->interfaceElementoIniId;
    }

    /**
     * Set interfaceElementoIniId
     *
     * @return \telconet\schemaBundle\Entity\InfoInterfaceElemento
     */
    public function setInterfaceElementoIniId(\telconet\schemaBundle\Entity\InfoInterfaceElemento $interfaceElementoIniId)
    {
        $this->interfaceElementoIniId = $interfaceElementoIniId;
    }

    /**
     * Get interfaceElementoFinId
     *
     * @return \telconet\schemaBundle\Entity\InfoInterfaceElemento
     */
    public function getInterfaceElementoFinId()
    {
        return $this->interfaceElementoFinId;
    }

    /**
     * Set interfaceElementoFinId
     *
     * @return \telconet\schemaBundle\Entity\InfoInterfaceElemento
     */
    public function setInterfaceElementoFinId(\telconet\schemaBundle\Entity\InfoInterfaceElemento $interfaceElementoFinId)
    {
        $this->interfaceElementoFinId = $interfaceElementoFinId;
    }

    /**
     * Get tipoMedioId
     *
     * @return telconet\schemaBundle\Entity\AdmiTipoMedio
     */
    public function getTipoMedioId()
    {
        return $this->tipoMedioId;
    }

    /**
     * Set tipoMedioId
     *
     * @param telconet\schemaBundle\Entity\AdmiTipoMedio $tipoMedioId
     */
    public function setTipoMedioId(\telconet\schemaBundle\Entity\AdmiTipoMedio $tipoMedioId)
    {
        $this->tipoMedioId = $tipoMedioId;
    }

    /**
     * Get capacidadInput
     *
     * @return 
     */
    public function getCapacidadInput()
    {
        return $this->capacidadInput;
    }

    /**
     * Set capacidadInput
     *
     * @param  $capacidadInput
     */
    public function setCapacidadInput($capacidadInput)
    {
        $this->capacidadInput = $capacidadInput;
    }

    /**
     * Get unidadMedidaInput
     *
     * @return string
     */
    public function getUnidadMedidaInput()
    {
        return $this->unidadMedidaInput;
    }

    /**
     * Set unidadMedidaInput
     *
     * @param string $unidadMedidaInput
     */
    public function setUnidadMedidaInput($unidadMedidaInput)
    {
        $this->unidadMedidaInput = $unidadMedidaInput;
    }

    /**
     * Get capacidadOutput
     *
     * @return 
     */
    public function getCapacidadOutput()
    {
        return $this->capacidadOutput;
    }

    /**
     * Set capacidadOutput
     *
     * @param  $capacidadOutput
     */
    public function setCapacidadOutput($capacidadOutput)
    {
        $this->capacidadOutput = $capacidadOutput;
    }

    /**
     * Get unidadMedidaOutput
     *
     * @return string
     */
    public function getUnidadMedidaOutput()
    {
        return $this->unidadMedidaOutput;
    }

    /**
     * Set unidadMedidaOutput
     *
     * @param string $unidadMedidaOutput
     */
    public function setUnidadMedidaOutput($unidadMedidaOutput)
    {
        $this->unidadMedidaOutput = $unidadMedidaOutput;
    }

    /**
     * Get capacidadIniFin
     *
     * @return 
     */
    public function getCapacidadIniFin()
    {
        return $this->capacidadIniFin;
    }

    /**
     * Set capacidadIniFin
     *
     * @param  $capacidadIniFin
     */
    public function setCapacidadIniFin($capacidadIniFin)
    {
        $this->capacidadIniFin = $capacidadIniFin;
    }

    /**
     * Get unidadMedidaUp
     *
     * @return string
     */
    public function getUnidadMedidaUp()
    {
        return $this->unidadMedidaUp;
    }

    /**
     * Set unidadMedidaUp
     *
     * @param string $unidadMedidaUp
     */
    public function setUnidadMedidaUp($unidadMedidaUp)
    {
        $this->unidadMedidaUp = $unidadMedidaUp;
    }
    
    /**
     * Get bufferHiloId
     *
     * @return telconet\schemaBundle\Entity\InfoBufferHilo
     */
    public function getBufferHiloId()
    {
        return $this->bufferHiloId;
    }

    /**
     * Set bufferHiloId
     *
     * @param telconet\schemaBundle\Entity\InfoBufferHilo $bufferHiloId
     */
    public function setBufferId(\telconet\schemaBundle\Entity\InfoBufferHilo $bufferHiloId)
    {
        $this->bufferHiloId = $bufferHiloId;
    }

}

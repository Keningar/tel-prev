<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * telconet\schemaBundle\Entity\InfoPuntoDatoAdicional
 *
 * @ORM\Table(name="INFO_PUNTO_DATO_ADICIONAL")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoPuntoDatoAdicionalRepository")
 */
class InfoPuntoDatoAdicional
{

    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID_PUNTO_DATO_ADICIONAL", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_PUNTO_DATO_ADICIONAL", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var InfoPunto
     *
     * @ORM\ManyToOne(targetEntity="InfoPunto")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="PUNTO_ID", referencedColumnName="ID_PUNTO")
     * })
     */
    private $puntoId;

    /**
     * @var string $esEdificio
     *
     * @ORM\Column(name="ES_EDIFICIO", type="string", nullable=true)
     */
    private $esEdificio;

    /**
     * @var string $esElectronica
     *
     * @ORM\Column(name="ES_ELECTRONICA", type="string", nullable=false)
     */
    //se agrega campo para almacenar bandera de facturacion electronica     		
    private $esElectronica = 'S';

    /**
     * @var string $dependeDeEdificio
     *
     * @ORM\Column(name="DEPENDE_DE_EDIFICIO", type="string", nullable=true)
     */
    private $dependeDeEdificio;

    /**
     * @var integer $puntoEdificioId
     *
     * @ORM\Column(name="PUNTO_EDIFICIO_ID", type="integer", nullable=true)
     */
    private $puntoEdificioId;

    /**
     * @var datetime $feCreacion
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
     * @ORM\Column(name="USR_CREACION", type="string", nullable=true)
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
     * @ORM\Column(name="IP_CREACION", type="string", nullable=true)
     */
    private $ipCreacion;

    /**
     * @var string $esPadreFacturacion
     *
     * @ORM\Column(name="ES_PADRE_FACTURACION", type="string", nullable=true)
     */
    private $esPadreFacturacion;

    /**
     * @var string $datosEnvio
     *
     * @ORM\Column(name="DATOS_ENVIO", type="string", nullable=true)
     */
    private $datosEnvio;

    /**
     * @var string $nombreEnvio
     *
     * @ORM\Column(name="NOMBRE_ENVIO", type="string", nullable=true)
     */
    private $nombreEnvio;

    /**
     * @var string $direccionEnvio
     *
     * @ORM\Column(name="DIRECCION_ENVIO", type="string", nullable=true)
     */
    private $direccionEnvio;

    /**
     * @var string $emailEnvio
     *
     * @ORM\Column(name="EMAIL_ENVIO", type="string", nullable=true)
     */
    private $emailEnvio;

    /**
     * @var string $telefonoEnvio
     *
     * @ORM\Column(name="TELEFONO_ENVIO", type="string", nullable=true)
     */
    private $telefonoEnvio;

    /**
     * @var AdmiSector
     *
     * @ORM\ManyToOne(targetEntity="AdmiSector")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="SECTOR_ID", referencedColumnName="ID_SECTOR")
     * })
     */
    private $sectorId;

    /**
     * @var string $nombreEdificio
     *
     * @ORM\Column(name="NOMBRE_EDIFICIO", type="string", nullable=true)
     * @Assert\Length(
     *   max=60,
     *   maxMessage="El nombre del edificio no puede tener más de {{ limit }} caracteres"
     * )
     */
    private $nombreEdificio;

    /**
     * @var string $gastoAdministrativo
     *
     * @ORM\Column(name="GASTO_ADMINISTRATIVO", type="string", nullable=false)
     */   		
    private $gastoAdministrativo = 'N';

    /**
     * @var InfoElemento
     *
     * @ORM\ManyToOne(targetEntity="InfoElemento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ELEMENTO_ID", referencedColumnName="ID_ELEMENTO")
     * })
     */
    private $elementoId;
    
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
     * Get esEdificio
     *
     * @return string
     */
    public function getEsEdificio()
    {
        return $this->esEdificio;
    }

    /**
     * Set esEdificio
     *
     * @param string $esEdificio
     */
    public function setEsEdificio($esEdificio)
    {
        $this->esEdificio = $esEdificio;
    }

    /**
     * Get esElectronica
     *
     * @return string
     */
    //se agrega metodo get para recuperar el valor de bandera de facturacion electronica  		
    public function getEsElectronica()
    {
        return $this->esElectronica;
    }

    /**
     * Set esElectronica
     *
     * @param string $esElectronica
     */
    //se agrega metodo set para setear el valor de bandera de facturacion electronica  		
    public function setEsElectronica($esElectronica)
    {
        $this->esElectronica = $esElectronica;
    }

    /**
     * Get dependeDeEdificio
     *
     * @return string
     */
    public function getDependeDeEdificio()
    {
        return $this->dependeDeEdificio;
    }

    /**
     * Set dependeDeEdificio
     *
     * @param string $dependeDeEdificio
     */
    public function setDependeDeEdificio($dependeDeEdificio)
    {
        $this->dependeDeEdificio = $dependeDeEdificio;
    }

    /**
     * Get puntoEdificioId
     *
     * @return integer
     */
    public function getPuntoEdificioId()
    {
        return $this->puntoEdificioId;
    }

    /**
     * Set puntoEdificioId
     *
     * @param integer $puntoEdificioId
     */
    public function setPuntoEdificioId($puntoEdificioId)
    {
        $this->puntoEdificioId = $puntoEdificioId;
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
     * Get esPadreFacturacion
     *
     * @return string
     */
    public function getEsPadreFacturacion()
    {
        return $this->esPadreFacturacion;
    }

    /**
     * Set esPadreFacturacion
     *
     * @param string $esPadreFacturacion
     */
    public function setEsPadreFacturacion($esPadreFacturacion)
    {
        $this->esPadreFacturacion = $esPadreFacturacion;
    }

    /**
     * Get datosEnvio
     *
     * @return string
     */
    public function getDatosEnvio()
    {
        return $this->datosEnvio;
    }

    /**
     * Set datosEnvio
     *
     * @param string $datosEnvio
     */
    public function setDatosEnvio($datosEnvio)
    {
        $this->datosEnvio = $datosEnvio;
    }

    /**
     * Get nombreEnvio
     *
     * @return string
     */
    public function getNombreEnvio()
    {
        return $this->nombreEnvio;
    }

    /**
     * Set nombreEnvio
     *
     * @param string $nombreEnvio
     */
    public function setNombreEnvio($nombreEnvio)
    {
        $this->nombreEnvio = $nombreEnvio;
    }

    /**
     * Get direccionEnvio
     *
     * @return string
     */
    public function getDireccionEnvio()
    {
        return $this->direccionEnvio;
    }

    /**
     * Set direccionEnvio
     *
     * @param string $direccionEnvio
     */
    public function setDireccionEnvio($direccionEnvio)
    {
        $this->direccionEnvio = $direccionEnvio;
    }

    /**
     * Get emailEnvio
     *
     * @return string
     */
    public function getEmailEnvio()
    {
        return $this->emailEnvio;
    }

    /**
     * Set emailEnvio
     *
     * @param string $emailEnvio
     */
    public function setEmailEnvio($emailEnvio)
    {
        $this->emailEnvio = $emailEnvio;
    }

    /**
     * Get telefonoEnvio
     *
     * @return string
     */
    public function getTelefonoEnvio()
    {
        return $this->telefonoEnvio;
    }

    /**
     * Set telefonoEnvio
     *
     * @param string $telefonoEnvio
     */
    public function setTelefonoEnvio($telefonoEnvio)
    {
        $this->telefonoEnvio = $telefonoEnvio;
    }

    /**
     * Get sectorId
     *
     * @return telconet\schemaBundle\Entity\AdmiSector
     */
    public function getSectorId()
    {
        return $this->sectorId;
    }

    /**
     * Set sectorId
     *
     * @param telconet\schemaBundle\Entity\AdmiSector $sectorId
     */
    public function setSectorId(\telconet\schemaBundle\Entity\AdmiSector $sectorId)
    {
        $this->sectorId = $sectorId;
    }

    /**
     * Get nombreEdificio
     *
     * @return string
     */
    public function getNombreEdificio()
    {
        return $this->nombreEdificio;
    }

    /**
     * Set nombreEdificio
     *
     * @param string $nombreEdificio
     */
    public function setNombreEdificio($nombreEdificio)
    {
        $this->nombreEdificio = $nombreEdificio;
    }

    public function __clone()
    {
        $this->id = null;
    }
    
     /**
     * Get gastoAdministrativo
     *
     * @return string
     */		
    public function getGastoAdministrativo()
    {
        return $this->gastoAdministrativo;
    }

    /**
     * Set gastoAdministrativo
     *
     * @param string $gastoAdministrativo
     */
    public function setGastoAdministrativo($gastoAdministrativo)
    {
        $this->gastoAdministrativo = $gastoAdministrativo;
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
     * Set elementoId
     *
     * @param telconet\schemaBundle\Entity\InfoElemento $elementoId
     */
    public function setElementoId(\telconet\schemaBundle\Entity\InfoElemento $elementoId=NULL)
    {
        $this->elementoId = $elementoId;
    }

}

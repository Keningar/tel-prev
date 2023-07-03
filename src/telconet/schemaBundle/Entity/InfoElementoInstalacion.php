<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoElementoInstalacion
 *
 * @ORM\Table(name="INFO_ELEMENTO_INSTALACION")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoElementoInstalacionRepository")
 */
class InfoElementoInstalacion
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID_ELEMENTO_INSTALACION", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_ELEMENTO_INSTALACION", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var integer $personaEmpresaRolId
     *
     * @ORM\Column(name="PERSONA_EMPRESA_ROL_ID", type="integer", nullable=false)
     */
    private $personaEmpresaRolId;

    /**
     * @var integer $puntoId
     *
     * @ORM\Column(name="PUNTO_ID", type="integer", nullable=false)
     */
    private $puntoId;

    /**
     * @var integer $tipoElementoId
     *
     * @ORM\Column(name="TIPO_ELEMENTO_ID", type="integer", nullable=false)
     */
    private $tipoElementoId;

    /**
     * @var string $serieElemento
     *
     * @ORM\Column(name="SERIE_ELEMENTO", type="string", nullable=false)
     */
    private $serieElemento;

    /**
     * @var integer $elementoId
     *
     * @ORM\Column(name="ELEMENTO_ID", type="integer", nullable=true)
     */
    private $elementoId;

    /**
     * @var string $ubicacion
     *
     * @ORM\Column(name="UBICACION", type="string", nullable=true)
     */
    private $ubicacion;

    /**
     * @var string $propietario
     *
     * @ORM\Column(name="PROPIETARIO", type="string", nullable=true)
     */
    private $propietario;

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
     * @var string $usrUltMod
     *
     * @ORM\Column(name="USR_ULT_MOD", type="string", nullable=false)
     */
    private $usrUltMod;

    /**
     * @var datetime $feUltMod
     *
     * @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=false)
     */
    private $feUltMod;
    

    /**
     * @var string $modeloElemento
     *
     * @ORM\Column(name="MODELO_ELEMENTO", type="string", nullable=true)
     */
    private $modeloElemento;
    
    
    /**
     * @var string $macElemento
     *
     * @ORM\Column(name="MAC_ELEMENTO", type="string", nullable=true)
     */
    private $macElemento;
    
    
    /**
     * @var string $ipElemento
     *
     * @ORM\Column(name="IP_ELEMENTO", type="string", nullable=true)
     */
    private $ipElemento;
    
    
    /**
     * @var string $servicioId
     *
     * @ORM\Column(name="SERVICIO_ID", type="integer", nullable=true)
     */
    private $servicioId;
    
    
    /**
     * Get $id
     *
     * @return  integer
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set $id
     *
     * @param  integer  $id  $id
     *
     */ 
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get $personaEmpresaRolId
     *
     * @return  integer
     */ 
    public function getPersonaEmpresaRolId()
    {
        return $this->personaEmpresaRolId;
    }

    /**
     * Set $personaEmpresaRolId
     *
     * @param  integer  $personaEmpresaRolId  $personaEmpresaRolId
     *
     */ 
    public function setPersonaEmpresaRolId($personaEmpresaRolId)
    {
        $this->personaEmpresaRolId = $personaEmpresaRolId;
    }

    /**
     * Get $puntoId
     *
     * @return  integer
     */ 
    public function getPuntoId()
    {
        return $this->puntoId;
    }

    /**
     * Set $puntoId
     *
     * @param  integer  $puntoId  $puntoId
     *
     */ 
    public function setPuntoId($puntoId)
    {
        $this->puntoId = $puntoId;
    }

    /**
     * Get $tipoElementoId
     *
     * @return  integer
     */ 
    public function getTipoElementoId()
    {
        return $this->tipoElementoId;
    }

    /**
     * Set $tipoElementoId
     *
     * @param  integer  $tipoElementoId  $tipoElementoId
     *
     */ 
    public function setTipoElementoId($tipoElementoId)
    {
        $this->tipoElementoId = $tipoElementoId;
    }

    /**
     * Get $serieElemento
     *
     * @return  string
     */ 
    public function getSerieElemento()
    {
        return $this->serieElemento;
    }

    /**
     * Set $serieElemento
     *
     * @param  string  $serieElemento  $serieElemento
     *
     */ 
    public function setSerieElemento($serieElemento)
    {
        $this->serieElemento = $serieElemento;
    }

    /**
     * Get $elementoId
     *
     * @return  integer
     */ 
    public function getElementoId()
    {
        return $this->elementoId;
    }

    /**
     * Set $elementoId
     *
     * @param  integer  $elementoId  $elementoId
     *
     */ 
    public function setElementoId($elementoId)
    {
        $this->elementoId = $elementoId;
    }

    /**
     * Get $ubicacion
     *
     * @return  string
     */ 
    public function getUbicacion()
    {
        return $this->ubicacion;
    }

    /**
     * Set $ubicacion
     *
     * @param  string  $ubicacion  $ubicacion
     *
     */ 
    public function setUbicacion($ubicacion)
    {
        $this->ubicacion = $ubicacion;
    }

    /**
     * Get $propietario
     *
     * @return  string
     */ 
    public function getPropietario()
    {
        return $this->propietario;
    }

    /**
     * Set $propietario
     *
     * @param  string  $propietario  $propietario
     *
     */ 
    public function setPropietario($propietario)
    {
        $this->propietario = $propietario;
    }

    /**
     * Get $estado
     *
     * @return  string
     */ 
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set $estado
     *
     * @param  string  $estado  $estado
     *
     */ 
    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    /**
     * Get $usrCreacion
     *
     * @return  string
     */ 
    public function getUsrCreacion()
    {
        return $this->usrCreacion;
    }

    /**
     * Set $usrCreacion
     *
     * @param  string  $usrCreacion  $usrCreacion
     *
     */ 
    public function setUsrCreacion($usrCreacion)
    {
        $this->usrCreacion = $usrCreacion;
    }

    /**
     * Get $feCreacion
     *
     * @return  datetime
     */ 
    public function getFeCreacion()
    {
        return $this->feCreacion;
    }

    /**
     * Set $feCreacion
     *
     * @param  datetime  $feCreacion  $feCreacion
     *
     */ 
    public function setFeCreacion($feCreacion)
    {
        $this->feCreacion = $feCreacion;
    }

    /**
     * Get $usrUltMod
     *
     * @return  string
     */ 
    public function getUsrUltMod()
    {
        return $this->usrUltMod;
    }

    /**
     * Set $usrUltMod
     *
     * @param  string  $usrUltMod  $usrUltMod
     *
     */ 
    public function setUsrUltMod($usrUltMod)
    {
        $this->usrUltMod = $usrUltMod;
    }

    /**
     * Get $feUltMod
     *
     * @return  datetime
     */ 
    public function getFeUltMod()
    {
        return $this->feUltMod;
    }

    /**
     * Set $feUltMod
     *
     * @param  datetime  $feUltMod  $feUltMod
     *
     */ 
    public function setFeUltMod($feUltMod)
    {
        $this->feUltMod = $feUltMod;
    }
    
    /**
     * Get $modeloElemento
     *
     * @return  string
     */ 
    public function getModeloElemento()
    {
        return $this->modeloElemento;
    }
    
    /**
     * Set $modeloElemento
     *
     * @param  string  $modeloElemento  $modeloElemento
     *
     */ 
    public function setModeloElemento($modeloElemento)
    {
        $this->modeloElemento = $modeloElemento;
    }
    
    /**
     * Get $macElemento
     *
     * @return  string
     */ 
    public function getMacElemento()
    {
        return $this->macElemento;
    }
    
    /**
     * Set $macElemento
     *
     * @param  string  $macElemento  $macElemento
     *
     */ 
    public function setMacElemento($macElemento)
    {
        $this->macElemento = $macElemento;
    }
    
    
    
    /**
     * Get $ipElemento
     *
     * @return  string
     */ 
    public function getIpElemento()
    {
        return $this->ipElemento;
    }
    
    /**
     * Set $ipElemento
     *
     * @param  string  $ipElemento  $ipElemento
     *
     */ 
    public function setIpElemento($ipElemento)
    {
        $this->ipElemento = $ipElemento;
    }
    
     /**
     * Get $servicioId
     *
     * @return  integer
     */ 
    public function getServicioId()
    {
        return $this->servicioId;
    }
    
    /**
     * Set $servicioId
     *
     * @param  integer  $servicioId  $servicioId
     *
     */ 
    public function setServicioId($servicioId)
    {
        $this->servicioId = $servicioId;
    }

    
}

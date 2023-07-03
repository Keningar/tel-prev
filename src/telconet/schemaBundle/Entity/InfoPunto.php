<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * telconet\schemaBundle\Entity\InfoPunto
 *
 * @ORM\Table(name="INFO_PUNTO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoPuntoRepository")
 */
class InfoPunto
{

    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID_PUNTO", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_PUNTO", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string $login
     *
     * @ORM\Column(name="LOGIN", type="string", nullable=false)
     */
    private $login;

    /**
     * @var string $password
     *
     * @ORM\Column(name="PASSWORD", type="string", nullable=true)
     */
    private $password;

    /**
     * @var InfoPersonaEmpresaRol
     *
     * @ORM\ManyToOne(targetEntity="InfoPersonaEmpresaRol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="PERSONA_EMPRESA_ROL_ID", referencedColumnName="ID_PERSONA_ROL")
     * })
     */
    private $personaEmpresaRolId;

    /**
     * @var FLOAT $latitud
     *
     * @ORM\Column(name="LATITUD", type="float", nullable=true)
     */
    private $latitud;

    /**
     * @var AdmiJurisdiccion
     *
     * @ORM\ManyToOne(targetEntity="AdmiJurisdiccion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="PUNTO_COBERTURA_ID", referencedColumnName="ID_JURISDICCION")
     * })
     */
    private $puntoCoberturaId;

    /**
     * @var AdmiTipoNegocio
     *
     * @ORM\ManyToOne(targetEntity="AdmiTipoNegocio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="TIPO_NEGOCIO_ID", referencedColumnName="ID_TIPO_NEGOCIO")
     * })
     */
    private $tipoNegocioId;

    /**
     * @var AdmiTipoUbicacion
     *
     * @ORM\ManyToOne(targetEntity="AdmiTipoUbicacion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="TIPO_UBICACION_ID", referencedColumnName="ID_TIPO_UBICACION")
     * })
     */
    private $tipoUbicacionId;

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
     * @var string $direccion
     *
     * @ORM\Column(name="DIRECCION", type="string", nullable=false)
     */
    private $direccion;

    /**
     * @var string $descripcionPunto
     *
     * @ORM\Column(name="DESCRIPCION_PUNTO", type="string", nullable=false)
     */
    private $descripcionPunto;

    /**
     * @var string $nombrePunto
     *
     * @ORM\Column(name="NOMBRE_PUNTO", type="string", nullable=false)
     */
    private $nombrePunto;

    /**
     * @var string $usrCobranzas
     *
     * @ORM\Column(name="USR_COBRANZAS", type="string", nullable=true)
     */
    private $usrCobranzas;

    /**
     * @var LONG $observacion
     *
     * @ORM\Column(name="OBSERVACION", type="string", nullable=true)
     */
    private $observacion;

    /**
     * @var FLOAT $longitud
     *
     * @ORM\Column(name="LONGITUD", type="float", nullable=true)
     */
    private $longitud;

    /**
     * @var string $usrVendedor
     *
     * @ORM\Column(name="USR_VENDEDOR", type="string", nullable=false)
     */
    private $usrVendedor;

    /**
     * @var string $estado
     *
     * @ORM\Column(name="ESTADO", type="string", nullable=false)
     */
    private $estado;

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
     * @ORM\Column(name="RUTA_CROQUIS", type="string", length=255, nullable=false)
     */
    private $path;

    /**
     * @Assert\File(maxSize="2000000")
     */
    private $file;

    /**
     * @ORM\Column(name="ARCHIVO_DIGITAL", type="string", length=255, nullable=false)
     */
    private $pathDigital;

    /**
     * @Assert\File(maxSize="2000000")
     */
    private $fileDigital;

    /**
     * @var string $accion
     *
     * @ORM\Column(name="ACCION", type="string", nullable=true)
     */
    private $accion;

    /**
     * @var string $ipUltMod
     *
     * @ORM\Column(name="IP_ULT_MOD", type="string", nullable=true)
     */
    private $ipUltMod;

    /**
     * @var string $origenWeb
     *
     * @ORM\Column(name="ORIGEN_WEB", type="string", nullable=true)
     */
    private $origenWeb;

    /**
     * Get login
     *
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Set login
     *
     * @param string $login
     */
    public function setLogin($login)
    {
        $this->login = $login;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set password
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
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
    * Set id
    * 
    * @param integer $id 
    */

    public function setId($id){
       $this->id = $id;
    }

    /**
     * Get personaEmpresaRolId
     *
     * @return \telconet\schemaBundle\Entity\InfoPersonaEmpresaRol
     */
    public function getPersonaEmpresaRolId()
    {
        return $this->personaEmpresaRolId;
    }

    /**
     * Set personaEmpresaRolId
     *
     * @param telconet\schemaBundle\Entity\InfoPersonaEmpresaRol $personaEmpresaRolId
     */
    public function setPersonaEmpresaRolId(\telconet\schemaBundle\Entity\InfoPersonaEmpresaRol $personaEmpresaRolId)
    {
        $this->personaEmpresaRolId = $personaEmpresaRolId;
    }

    /**
     * Get latitud
     *
     * @return 
     */
    public function getLatitud()
    {
        return $this->latitud;
    }

    /**
     * Set latitud
     *
     * @param  $latitud
     */
    public function setLatitud($latitud)
    {
        $this->latitud = $latitud;
    }

    /**
     * Get puntoCoberturaId
     *
     * @return \telconet\schemaBundle\Entity\AdmiJurisdiccion
     */
    public function getPuntoCoberturaId()
    {
        return $this->puntoCoberturaId;
    }

    /**
     * Set puntoCoberturaId
     *
     * @param telconet\schemaBundle\Entity\AdmiJurisdiccion $puntoCoberturaId
     */
    public function setPuntoCoberturaId(\telconet\schemaBundle\Entity\AdmiJurisdiccion $puntoCoberturaId)
    {
        $this->puntoCoberturaId = $puntoCoberturaId;
    }

    /**
     * Get tipoNegocioId
     *
     * @return \telconet\schemaBundle\Entity\AdmiTipoNegocio
     */
    public function getTipoNegocioId()
    {
        return $this->tipoNegocioId;
    }

    /**
     * Set tipoNegocioId
     *
     * @param telconet\schemaBundle\Entity\AdmiTipoNegocio $tipoNegocioId
     */
    public function setTipoNegocioId(\telconet\schemaBundle\Entity\AdmiTipoNegocio $tipoNegocioId)
    {
        $this->tipoNegocioId = $tipoNegocioId;
    }

    /**
     * Get tipoUbicacionId
     *
     * @return \telconet\schemaBundle\Entity\AdmiTipoUbicacion
     */
    public function getTipoUbicacionId()
    {
        return $this->tipoUbicacionId;
    }

    /**
     * Set tipoUbicacionId
     *
     * @param telconet\schemaBundle\Entity\AdmiTipoUbicacion $tipoUbicacionId
     */
    public function setTipoUbicacionId(\telconet\schemaBundle\Entity\AdmiTipoUbicacion $tipoUbicacionId)
    {
        $this->tipoUbicacionId = $tipoUbicacionId;
    }

    /**
     * Get sectorId
     *
     * @return \telconet\schemaBundle\Entity\AdmiSector
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
     * Get direccion
     *
     * @return string
     */
    public function getDireccion()
    {
        return $this->direccion;
    }

    /**
     * Set direccion
     *
     * @param string $direccion
     */
    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;
    }

    /**
     * Get descripcionPunto
     *
     * @return string
     */
    public function getDescripcionPunto()
    {
        return $this->descripcionPunto;
    }

    /**
     * Set descripcionPunto
     *
     * @param string $descripcionPunto
     */
    public function setDescripcionPunto($descripcionPunto)
    {
        $this->descripcionPunto = $descripcionPunto;
    }

    /**
     * Get nombrePunto
     *
     * @return string
     */
    public function getNombrePunto()
    {
        return $this->nombrePunto;
    }

    /**
     * Set nombrePunto
     *
     * @param string $nombrePunto
     */
    public function setNombrePunto($nombrePunto)
    {
        $this->nombrePunto = $nombrePunto;
    }

    /**
     * Get usrCobranzas
     *
     * @return string
     */
    public function getUsrCobranzas()
    {
        return $this->usrCobranzas;
    }

    /**
     * Set usrCobranzas
     *
     * @param string $usrCobranzas
     */
    public function setUsrCobranzas($usrCobranzas)
    {
        $this->usrCobranzas = $usrCobranzas;
    }

    /**
     * Get observacion
     *
     * @return 
     */
    public function getObservacion()
    {
        return $this->observacion;
    }

    /**
     * Set observacion
     *
     * @param  $observacion
     */
    public function setObservacion($observacion)
    {
        $this->observacion = $observacion;
    }

    /**
     * Get longitud
     *
     * @return 
     */
    public function getLongitud()
    {
        return $this->longitud;
    }

    /**
     * Set longitud
     *
     * @param  $longitud
     */
    public function setLongitud($longitud)
    {
        $this->longitud = $longitud;
    }

    /**
     * Get usrVendedor
     *
     * @return string
     */
    public function getUsrVendedor()
    {
        return $this->usrVendedor;
    }

    /**
     * Set usrVendedor
     *
     * @param string $usrVendedor
     */
    public function setUsrVendedor($usrVendedor)
    {
        $this->usrVendedor = $usrVendedor;
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
     * Get origenWeb
     *
     * @return string
     */
    public function getOrigenWeb()
    {
        return $this->origenWeb;
    }

    /**
     * Set origenWeb
     *
     * @param string $origenWeb
     */
    public function setOrigenWeb($origenWeb)
    {
        $this->origenWeb = $origenWeb;
    }

//metodos para File 
    /**
     * Set file
     *
     * @param string $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

//metodos para FileDigital 
    /**
     * Set fileDigital
     *
     * @param string $fileDigital
     */
    public function setFileDigital($fileDigital)
    {
        $this->fileDigital = $fileDigital;
    }

    /**
     * Get file
     *
     * @return string 
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Get fileDigital
     *
     * @return string 
     */
    public function getFileDigital()
    {
        return $this->fileDigital;
    }

    /**
     * Set path
     *
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Set pathDigital
     *
     * @param string $pathDigital
     */
    public function setPathDigital($pathDigital)
    {
        $this->pathDigital = $pathDigital;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get pathDigital
     *
     * @return string 
     */
    public function getPathDigital()
    {
        return $this->pathDigital;
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
     * Get ipUltMod
     *
     * @return string
     */
    public function getIpUltMod()
    {
        return $this->ipUltMod;
    }

    /**
     * Set ipUltMod
     *
     * @param string $ipUltMod
     */
    public function setIpUltMod($ipUltMod)
    {
        $this->ipUltMod = $ipUltMod;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if(null !== $this->file)
        {
            $this->path = $this->login . "_croquis." . $this->file->guessExtension(); //generamos un nombre único de archivo
        }
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUploadDigital()
    {
        if(null !== $this->fileDigital)
        {
            $this->pathDigital = $this->login . "_digital." . $this->fileDigital->guessExtension(); //generamos un nombre único de archivo
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if(null === $this->file)
        {
            return; // El archivo no es obligatorio, por si viene vacío
        }

        //Se lanza una excepción si el archivo no se puede mover para que la entidad no persista en la base de datos
        // labor que realizar automáticamente move()
        $this->file->move($this->getUploadRootDir(), $this->login . "_croquis." . $this->file->guessExtension());
        unset($this->file);
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function uploadDigital()
    {
        if(null === $this->fileDigital)
        {
            return; // El archivo no es obligatorio, por si viene vacío
        }

        //Se lanza una excepción si el archivo no se puede mover para que la entidad no persista en la base de datos
        // labor que realizar automáticamente move()
        $this->fileDigital->move($this->getUploadRootDirDigital(), $this->login . "_digital." . $this->fileDigital->guessExtension());
        unset($this->fileDigital);
    }

    /**
     * @ORM\PostRemove()
     * 
     */
    public function removeUpload()
    {
        if($file = $this->getAbsolutePath())
        {
            unlink($file);
        }
    }

    /**
     * @ORM\PostRemove()
     * 
     */
    public function removeUploadDigital()
    {
        if($file = $this->getAbsolutePathDigital())
        {
            unlink($file);
        }
    }

//Métodos básicos de subida
    public function getAbsolutePath()
    {
        return null === $this->path ? null : $this->getUploadRootDir() . '/' . $this->login . "_croquis." . $this->path;
    }

//Métodos básicos de subida
    public function getAbsolutePathDigital()
    {
        return null === $this->pathDigital ? null : $this->getUploadRootDirDigital() . '/' . $this->pathDigital;
    }

    public function getWebPath()
    {
        return null === $this->path ? null : $this->getUploadDir() . '/' . $this->path;
    }

    public function getWebPathDigital()
    {
        return null === $this->pathDigital ? null : $this->getUploadDirDigital() . '/' . $this->pathDigital;
    }

    protected function getUploadRootDir()
    {
        return __DIR__ . '/../../../../web/public/' . $this->getUploadDir();
    }

    protected function getUploadRootDirDigital()
    {
        return __DIR__ . '/../../../../web/public' . $this->getUploadDirDigital();
    }

    protected function getUploadDir()
    {
        return '/uploads/croquis';
    }

    protected function getUploadDirDigital()
    {
        return '/uploads/archivo_punto';
    }

    public function getWebPath1()
    {
        return null === $this->path ? null : 'public' . $this->getUploadDir() . '/' . $this->path;
    }

    public function getWebPath1Digital()
    {
        return null === $this->pathDigital ? null : 'public' . $this->getUploadDirDigital() . '/' . $this->pathDigital;
    }

    public function __toString()
    {
        return $this->login;
    }

    public function __clone()
    {
        $this->id = null;
    }

}

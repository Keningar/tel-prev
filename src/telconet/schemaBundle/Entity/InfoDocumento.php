<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert; 
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * telconet\schemaBundle\Entity\InfoDocumento
 *
 * @ORM\Table(name="INFO_DOCUMENTO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoDocumentoRepository")
 */
class InfoDocumento
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_DOCUMENTO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_DOCUMENTO", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var AdmiTipoDocumento
*
* @ORM\ManyToOne(targetEntity="AdmiTipoDocumento")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="TIPO_DOCUMENTO_ID", referencedColumnName="ID_TIPO_DOCUMENTO")
* })
*/
		
private $tipoDocumentoId;

/**
* @var AdmiClaseDocumento
*
* @ORM\ManyToOne(targetEntity="AdmiClaseDocumento")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="CLASE_DOCUMENTO_ID", referencedColumnName="ID_CLASE_DOCUMENTO")
* })
*/
		
private $claseDocumentoId;

/**
* @var string $nombreDocumento
*
* @ORM\Column(name="NOMBRE_DOCUMENTO", type="string", nullable=true)
*/		
     		
private $nombreDocumento;

/**
* @var string $ubicacionLogicaDocumento
* 
* @ORM\Column(name="UBICACION_LOGICA_DOCUMENTO", type="string", length=255, nullable=false)
*/
private $ubicacionLogicaDocumento;
private  $path;

/**
* @Assert\File(maxSize="6000000")
*/

private  $file;

/**
* @var string $ubicacionfisicaDocumento
*
* @ORM\Column(name="UBICACION_FISICA_DOCUMENTO", type="string", nullable=true)
*/		
     		
private $ubicacionFisicaDocumento;

/**
* @var string $documento
*
* @ORM\Column(name="DOCUMENTO", type="text", nullable=true)
*/		
     		
private $documento;

/**
* @var string $fechaDocumento
*
* @ORM\Column(name="FECHA_DOCUMENTO", type="date", nullable=true)
*/		
     		
private $fechaDocumento;

/**
* @var string $modeloElementoId
*
* @ORM\Column(name="MODELO_ELEMENTO_ID", type="integer", nullable=true)
*/		
     		
private $modeloElementoId;

/**
* @var string $elementoId
*
* @ORM\Column(name="ELEMENTO_ID", type="integer", nullable=true)
*/		
     		
private $elementoId;

/**
* @var string $contratoId
*
* @ORM\Column(name="CONTRATO_ID", type="integer", nullable=true)
*/		
     		
private $contratoId;

/**
* @var string $documentoFinancieroId
*
* @ORM\Column(name="DOCUMENTO_FINANCIERO_ID", type="integer", nullable=true)
*/		
     		
private $documentoFinancieroId;

/**
* @var string $tareaInterfaceModeloTraId
*
* @ORM\Column(name="TAREA_INTERFACE_MODELO_TRA_ID", type="integer", nullable=true)
*/		
     		
private $tareaInterfaceModeloTraId;

/**
* @var string $mensaje
*
* @ORM\Column(name="MENSAJE", type="string", nullable=false)
*/		
     		
private $mensaje;

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
* @ORM\Column(name="FE_CREACION", type="datetime", nullable=true)
*/		
     		
private $feCreacion;

/**
* @var string $ipCreacion
*
* @ORM\Column(name="IP_CREACION", type="string", nullable=false)
*/		
     		
private $ipCreacion;

/**
* @var string $empresaCod
*
* @ORM\Column(name="EMPRESA_COD", type="string", nullable=false)
*/		
     		
private $empresaCod;

/**

* @var string $fechaPublicacionDesde
*
* @ORM\Column(name="FECHA_DESDE", type="datetime", nullable=true)
*/		
     		
private $fechaPublicacionDesde;

/**
* @var string $fechaPublicacionHasta
*
* @ORM\Column(name="FECHA_HASTA", type="datetime", nullable=true)
*/		
     		
private $fechaPublicacionHasta;

/**
* Get id

* @var integer $tipoDocumentoGeneralId
*
* @ORM\Column(name="TIPO_DOCUMENTO_GENERAL_ID", type="integer", nullable=false)
*/	
private $tipoDocumentoGeneralId;


/**
* @var datetime $feUltMod
*
* @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=true)
*/		
     		
private $feUltMod;

/**
* @var string $usrUltMod
*
* @ORM\Column(name="USR_ULT_MOD", type="string", nullable=true)
*/		
     		
private $usrUltMod;


/*
  Variable para uso exclusivo del Type -> InfoDocumentoGestionType
*/
private $modulo;

/*
  Variable para uso exclusivo de rutas para cargar archivos/Imagenes
*/
private $strFilePath;


/**
 * @var FLOAT $floatLatitud
 *
 * @ORM\Column(name="LATITUD", type="float", nullable=true)
 */
private $floatLatitud;


/**
 * @var FLOAT $floatLongitud
 *
 * @ORM\Column(name="LONGITUD", type="float", nullable=true)
 */
private $floatLongitud;

/**
* @var string $strEtiquetaDocumento
*
* @ORM\Column(name="ETIQUETA_DOCUMENTO", type="string", nullable=false)
*/		
     		
private $strEtiquetaDocumento;

/**
* @var integer $intCuadrillaHistorialId
*
* @ORM\Column(name="CUADRILLA_HISTORIAL_ID", type="integer", nullable=true)
*/		
     		
private $intCuadrillaHistorialId;


/**
* @var InfoPersonaEmpresaRol
*
* @ORM\ManyToOne(targetEntity="InfoPersonaEmpresaRol")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="PERSONA_EMPRESA_ROL_ID", referencedColumnName="ID_PERSONA_ROL")
* })
*/
		 
public function getId()
{
    return $this->id; 
}


/**
* Set nombreDocumento
*
* @param string $nombreDocumento
* @return InfoDocumento
*/
public function setNombreDocumento($nombreDocumento)
{
    $this->nombreDocumento = $nombreDocumento;
    
    return $this;
}

/**
* Get nombreDocumento
*
* @return string 
*/
public function getNombreDocumento()
{
    return $this->nombreDocumento;
}
  
/**
* Set ubicacionLogicaDocumento
*
* @param string $ubicacionLogicaDocumento
* @return InfoDocumento
*/
public function setUbicacionLogicaDocumento($ubicacionLogicaDocumento)
{
    $this->ubicacionLogicaDocumento = $ubicacionLogicaDocumento;
    return $this;
}

/**
* Get ubicacionLogicaDocumento
*
* @return string
*/
public function getUbicacionLogicaDocumento()
{
    return $this->ubicacionLogicaDocumento;
}

/**
* Set ubicacionFisicaDocumento
*
* @param string $ubicacionFisicaDocumento
* @return InfoDocumento
*/
public function setUbicacionFisicaDocumento($ubicacionFisicaDocumento)
{
    $this->ubicacionFisicaDocumento = $ubicacionFisicaDocumento;
    
    return $this;
}

/**
* Get ubicacionFisicaDocumento
*
* @return string 
*/
public function getUbicacionFisicaDocumento()
{
    return $this->ubicacionFisicaDocumento;
}

/**
* Set documento
*
* @param string $documento
* @return InfoDocumento
*/
public function setDocumento($documento)
{
    $this->documento = $documento;
    
    return $this;
}

/**
* Get documento
*
* @return string 
*/
public function getDocumento()
{
    return $this->documento;
}

/**
* Set fechaDocumento
*
* @param \DateTime $fechaDocumento
* @return InfoDocumento
*/
public function setFechaDocumento($fechaDocumento)
{
    $this->fechaDocumento = $fechaDocumento;
    
    return $this;
}

/**
* Get fechaDocumento
*
* @return \DateTime 
*/
public function getFechaDocumento()
{
    return $this->fechaDocumento;
}

/**
* Set modeloElementoId
*
* @param integer $modeloElementoId
* @return InfoDocumento
*/
public function setModeloElementoId($modeloElementoId)
{
    $this->modeloElementoId = $modeloElementoId;
    
    return $this;
}

/**
* Get modeloElementoId
*
* @return integer 
*/
public function getModeloElementoId()
{
    return $this->modeloElementoId;
}

/**
* Set elementoId
*
* @param integer $elementoId
* @return InfoDocumento
*/
public function setElementoId($elementoId)
{
    $this->elementoId = $elementoId;
    
    return $this;
}

/**
* Get elementoId
*
* @return integer 
*/
public function getElementoId()
{
    return $this->elementoId;
}

/**
* Set contratoId
*
* @param integer $contratoId
* @return InfoDocumento
*/
public function setContratoId($contratoId)
{
    $this->contratoId = $contratoId;
    
    return $this;
}

/**
* Get contratoId
*
* @return integer 
*/
public function getContratoId()
{
    return $this->contratoId;
}

/**
* Set documentoFinancieroId
*
* @param integer $documentoFinancieroId
* @return InfoDocumento
*/
public function setDocumentoFinancieroId($documentoFinancieroId)
{
    $this->documentoFinancieroId = $documentoFinancieroId;
    
    return $this;
}

/**
* Get documentoFinancieroId
*
* @return integer 
*/
public function getDocumentoFinancieroId()
{
    return $this->documentoFinancieroId;
}

/**
* Set tareaInterfaceModeloTraId
*
* @param integer $tareaInterfaceModeloTraId
* @return InfoDocumento
*/
public function setTareaInterfaceModeloTraId($tareaInterfaceModeloTraId)
{
    $this->tareaInterfaceModeloTraId = $tareaInterfaceModeloTraId;
    
    return $this;
}

/**
* Get tareaInterfaceModeloTraId
*
* @return integer 
*/
public function getTareaInterfaceModeloTraId()
{
    return $this->tareaInterfaceModeloTraId;
}

/**
* Set mensaje
*
* @param string $mensaje
* @return InfoDocumento
*/
public function setMensaje($mensaje)
{
    $this->mensaje = $mensaje;
    
    return $this;
}

/**
* Get mensaje
*
* @return string 
*/
public function getMensaje()
{
    return $this->mensaje;
}

/**
* Set estado
*
* @param string $estado
* @return InfoDocumento
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
* Set usrCreacion
*
* @param string $usrCreacion
* @return InfoDocumento
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
* @return InfoDocumento
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
* Set ipCreacion
*
* @param string $ipCreacion
* @return InfoDocumento
*/
public function setIpCreacion($ipCreacion)
{
    $this->ipCreacion = $ipCreacion;
    
    return $this;
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
* Set tipoDocumentoId
*
* @param \telconet\schemaBundle\Entity\AdmiTipoDocumento $tipoDocumentoId
* @return InfoDocumento
*/
public function setTipoDocumentoId(\telconet\schemaBundle\Entity\AdmiTipoDocumento $tipoDocumentoId = null)
{
    $this->tipoDocumentoId = $tipoDocumentoId;
    
    return $this;
}

/**
* Get tipoDocumentoId
*
* @return \telconet\schemaBundle\Entity\AdmiTipoDocumento 
*/
public function getTipoDocumentoId()
{
    return $this->tipoDocumentoId;
}

/**
* Set claseDocumentoId
*
* @param \telconet\schemaBundle\Entity\AdmiClaseDocumento $claseDocumentoId
* @return InfoDocumento
*/
public function setClaseDocumentoId(\telconet\schemaBundle\Entity\AdmiClaseDocumento $claseDocumentoId = null)
{
    $this->claseDocumentoId = $claseDocumentoId;
    
    return $this;
}

 /**
 * Get claseDocumentoId
 *
 * @return \telconet\schemaBundle\Entity\AdmiClaseDocumento 
 */
 public function getClaseDocumentoId()
 {
     return $this->claseDocumentoId;
 }
        
 /**
 * Set empresaCod
 *
 * @param string $empresaCod     
 */
 public function setEmpresaCod($empresaCod)
 {
     $this->empresaCod = $empresaCod;
    
     return $this;
 }

 /**
 * Get empresaCod
 *
 * @return string 
 */
 public function getEmpresaCod()
 {
     return $this->empresaCod;
 }         
    
/**
* Set tipoDocumentoGeneralId
*
* @param integer $tipoDocumentoGeneralId
*/
public function setTipoDocumentoGeneralId($tipoDocumentoGeneralId)
{
    $this->tipoDocumentoGeneralId = $tipoDocumentoGeneralId;
    return $this;
}

/**
* Get tipoDocumentoGeneralId
*
* @return integer
*/
public function getTipoDocumentoGeneralId()
{
    return $this->tipoDocumentoGeneralId;
}
     
/**
* @var string
*/
public $extension;

/**
* Set extension
*
* @param string $extension
*/
public function setExtension($extension)
{
    $this->extension = $extension;
}

/**
* Get extension
*
* @return string
*/
public function getExtension()
{
    return $this->extension;
} 

/**
* @var array
*/
public $imagenes;

/**
* @var array
*/
public $fechasPublicacionHasta;

/**
* @var array
*/
public $tipos;

public $tags;

public function __construct()
{
    $this->imagenes = new ArrayCollection();
    $this->tipos = new ArrayCollection();
    $this->tags = new ArrayCollection();
    $this->fechasPublicacionHasta = new ArrayCollection();
}
/**
* Set imagenes
*
* @param string $imagenes
*/
public function setImagenes(ArrayCollection $imagenes)
{
    $this->imagenes = $imagenes;
}

/**
* Get imagenes
*
* @return string
*/
public function getImagenes()
{
    return $this->imagenes;
} 

/**
* Set fechasPublicacionHasta
*
* @param string $fechasPublicacionHasta
*/
public function setFechasPublicacionHasta(ArrayCollection $fechasPublicacionHasta)
{
    $this->fechasPublicacionHasta = $fechasPublicacionHasta;
}

/**
* Get fechasPublicacionHasta
*
* @return string
*/
public function getFechasPublicacionHasta()
{
    return $this->fechasPublicacionHasta;
} 

/**
* Set tipos
*
* @param string $tipos
*/
public function setTipos(ArrayCollection $tipos)
{
    $this->tipos = $tipos;
}

/**
* Get tipos
*
* @return string
*/
public function getTipos()
{
    return $this->tipos;
}     
       
/**
* Set file
*
* @param string $file
*/
public function setFile($file)
{
    $this->file = $file;
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
* Set path
*
* @param string $path
*/
public function setPath($path)
{
    $this->path = $path;
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
* Set modulo
*
* @param string $modulo
*/
public function setModulo($modulo)
{
    $this->modulo = $modulo;
}

/**
* Get modulo
*
* @return string 
*/
public function getModulo()
{
    return $this->modulo;
}

/**
* @ORM\PrePersist()
* @ORM\PreUpdate()
*/
public function preUpload()
{       
    if (null !== $this->file) 
    {           
        $extension             = $this->file->guessExtension();                              
        $this->extension       = $extension;
        $nombre                = $this->nombreDocumento."_".uniqid();        
        //Nombre final con extension                
        $nombreFinal                    = $nombre.'.'.$extension;        
        $this->path                     = $nombreFinal;      
        $this->ubicacionLogicaDocumento = $nombreFinal;
        $this->nombreDocumento          = $nombre;
    }

}

/**
* @ORM\PostPersist()
* @ORM\PostUpdate()
*/
public function upload()
{       
    if ( null === $this->file )
    { // El archivo no es obligatorio, por si viene vacio
        return;
    }        
    $extension       = $this->file->guessExtension();    
    $this->extension = $extension;
    //Nombre final con extension                
    $nombreFinal           = $this->nombreDocumento.'.'.$extension;
    $this->nombreDocumento = $nombreFinal;
    $this->file->move($this->getUploadRootDir(),$nombreFinal);  
    unset($this->file);
}
	
/**
* @ORM\PostRemove()
* 
*/
public function removeUpload()
{
    if ( $file = $this->getAbsolutePath() )
    {
        unlink($file);
    }
}
	
    //Metodos basicos de subida 
    public function getAbsolutePath()
    {
        return null === $this->path ? null : $this->getUploadRootDir().'/'.$this->path;
    }
    public function getWebPath()
    {
        return null === $this->path ? null : $this->getUploadDir().'/'.$this->path;
    }

    protected function getUploadRootDir()
    {
        // se va a concatenar con $path_telcos
        $this->ubicacionFisicaDocumento = $this->getUploadDir().'/'.$this->nombreDocumento;
        
        //Se elimina telcos/web ( si existe ) antes de generar guardado en directorio existente
        $this->setUploadDir(str_replace("telcos/web/", "", $this->getUploadDir()));        
        
        //Ruta de directorio que ya contiene ../telcos/web/ , se completa con la ruta de acuerdo al archivo a guardar
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {               
        return $this->strFilePath;
    }

    public function setUploadDir($strFilePath)
    {       
        $this->strFilePath = $strFilePath;
    }
    
    /**
    * Get fechaPublicacionDesde
    *
    * @return \DateTime 
    */
    public function getFechaPublicacionDesde() {
        return $this->fechaPublicacionDesde;
    }

    /**
    * Get fechaPublicacionHasta
    *
    * @return \DateTime 
    */
    public function getFechaPublicacionHasta() {
        return $this->fechaPublicacionHasta;
    }

    /**
    * Set fechaPublicacionDesde
    *
    * @param string $fechaPublicacionDesde
    * @return InfoDocumento
    */
    public function setFechaPublicacionDesde($fechaPublicacionDesde) {
        $this->fechaPublicacionDesde = $fechaPublicacionDesde;
        return $this;
    }

    /**
    * Set fechaPublicacionHasta
    *
    * @param string $fechaPublicacionHasta
    * @return InfoDocumento
    */
    public function setFechaPublicacionHasta($fechaPublicacionHasta) {
        $this->fechaPublicacionHasta = $fechaPublicacionHasta;
        return $this;
    }
    
    
    /**
    * Set tags
    *
    * @param string $tags
    */
    public function setTags(ArrayCollection $tags)
    {
        $this->tags = $tags;
    }

    /**
    * Get tags
    *
    * @return string
    */
    public function getTags()
    {
        return $this->tags;
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
     * Get latitud
     *
     * @return 
     */
    public function getLatitud()
    {
        return $this->floatLatitud;
    }

    /**
     * Set latitud
     *
     * @param $floatLatitud
     */
    public function setLatitud($floatLatitud)
    {
        $this->floatLatitud = $floatLatitud;
    }
    
    /**
     * Get longitud
     *
     * @return 
     */
    public function getLongitud()
    {
        return $this->floatLongitud;
    }

    /**
     * Set longitud
     *
     * @param $floatLongitud
     */
    public function setLongitud($floatLongitud)
    {
        $this->floatLongitud = $floatLongitud;
    }

    /**
     * Get strEtiquetaDocumento
     */ 
    public function getStrEtiquetaDocumento()
    {
    return $this->strEtiquetaDocumento;
    }

    /**
     * Set strEtiquetaDocumento
     *
     * @return  self
     */ 
    public function setStrEtiquetaDocumento($strEtiquetaDocumento)
    {
    $this->strEtiquetaDocumento = $strEtiquetaDocumento;

    return $this;
    }

     /**
     * Get intCuadrillaHistorialId
     */ 
    public function getIntCuadrillaHistorialId()
    {
        return $this->$intCuadrillaHistorialId;
    }

    /**
     * Set intCuadrillaHistorialId
     *
     * @return  self
     */ 
    public function setIntCuadrillaHistorialId($intCuadrillaHistorialId)
    {
        $this->intCuadrillaHistorialId = $intCuadrillaHistorialId;

        return $this;
    }
}
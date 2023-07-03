<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiCanalRecaudacion
 *
 * @ORM\Table(name="ADMI_CANAL_RECAUDACION")
 * @ORM\Entity
 */
class AdmiCanalRecaudacion
{
    
    /**
     *
     * @var integer $id
     *      @ORM\Column(name="ID_CANAL_RECAUDACION", type="integer", nullable=false)
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="SEQUENCE")
     *      @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_CANAL_RECAUDACION", allocationSize=1, initialValue=1)
     */
    private $id;
    
    /**
     *
     * @var string $empresaCod
     *      @ORM\Column(name="EMPRESA_COD", type="string", nullable=false)
     *     
     */
    private $empresaCod;
    
    /**
     *
     * @var integer $bancoTipoCuentaId
     *      @ORM\Column(name="BANCO_TIPO_CUENTA_ID", type="integer", nullable=false)
     */
    private $bancoTipoCuentaId;
    
    /**
     *
     * @var integer $bancoCtaContableId
     *      @ORM\Column(name="BANCO_CTA_CONTABLE_ID", type="integer", nullable=false)
     */
    private $bancoCtaContableId;
    
    /**
     *
     * @var string $nombreCanalRecaudacion
     *      @ORM\Column(name="NOMBRE_CANAL_RECAUDACION", type="string", nullable=false)
     */
    private $nombreCanalRecaudacion;
    
    /**
     *
     * @var string $descripcionCanalRecaudacion
     *      @ORM\Column(name="DESCRIPCION_CANAL_RECAUDACION", type="string", nullable=false)
     */
    private $descripcionCanalRecaudacion;
    
    /**
     *
     * @var string $estadoCanalRecaudacion
     *      @ORM\Column(name="ESTADO_CANAL_RECAUDACION", type="string", nullable=false)
     */
    private $estadoCanalRecaudacion;
    
    /**
     *
     * @var string $usrCreacion
     *      @ORM\Column(name="USR_CREACION", type="string", nullable=false)
     */
    private $usrCreacion;
    
    /**
     *
     * @var \DateTime $feCreacion
     *      @ORM\Column(name="FE_CREACION", type="datetime", nullable=false)
     */
    private $feCreacion;
    
    /**
     *
     * @var string $usrUltMod
     *      @ORM\Column(name="USR_ULT_MOD", type="string", nullable=false)
     */
    private $usrUltMod;
    
    /**
     *
     * @var \DateTime $feUltMod
     *      @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=false)
     */
    private $feUltMod;
    
    /**
     *
     * @var string $tituloHoja
     *      @ORM\Column(name="TITULO_HOJA", type="string", nullable=false)
     */
    private $tituloHoja;
    
    /**
     *
     * @var integer $filaInicio
     *      @ORM\Column(name="FILA_INICIO", type="integer", nullable=false)
     */
    private $filaInicio;
    
    /**
     *
     * @var string $colValidacion
     *      @ORM\Column(name="COL_VALIDACION", type="string", nullable=false)
     */
    private $colValidacion;
    
    /**
     *
     * @var string $colValor
     *      @ORM\Column(name="COL_VALOR", type="string", nullable=false)
     */
    private $colValor;
    
    /**
     *
     * @var string $colIdentificacion
     *      @ORM\Column(name="COL_IDENTIFICACION", type="string", nullable=false)
     */
    private $colIdentificacion;
    
    /**
     *
     * @var string $colFecha
     *      @ORM\Column(name="COL_FECHA", type="string", nullable=false)
     */
    private $colFecha;
    
    /**
     *
     * @var string $colReferencia
     *      @ORM\Column(name="COL_REFERENCIA", type="string", nullable=false)
     */
    private $colReferencia;
    
    /**
     *
     * @var string $colRespuesta
     *      @ORM\Column(name="COL_RESPUESTA", type="string", nullable=false)
     */
    private $colRespuesta;
    
    /**
     *
     * @var string $colNombre
     *      @ORM\Column(name="COL_NOMBRE", type="string", nullable=false)
     */
    private $colNombre;    
    
    /**
     *
     * @var string $sepIdentificacion
     *      @ORM\Column(name="SEP_IDENTIFICACION", type="string", nullable=true)
     */
    private $sepIdentificacion;
    
    /**
     *
     * @var string $padIdentificacion
     *      @ORM\Column(name="PAD_IDENTIFICACION", type="string", nullable=true)
     */
    private $padIdentificacion;
    
    /**
     *
     * @var string $remIdentificacion
     *      @ORM\Column(name="REM_IDENTIFICACION", type="string", nullable=true)
     */
    private $remIdentificacion;
    
    /**
     *
     * @var integer $batchSize
     *      @ORM\Column(name="BATCH_SIZE", type="integer", nullable=false)
     */
    private $batchSize;

    /**
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @param integer $id            
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @return string
     */
    public function getEmpresaCod()
    {
        return $this->empresaCod;
    }

    /**
     *
     * @param string $empresaCod            
     */
    public function setEmpresaCod($empresaCod)
    {
        $this->empresaCod = $empresaCod;
    }

    /**
     *
     * @return integer
     */
    public function getBancoTipoCuentaId()
    {
        return $this->bancoTipoCuentaId;
    }

    /**
     *
     * @param integer $bancoTipoCuentaId            
     */
    public function setBancoTipoCuentaId($bancoTipoCuentaId)
    {
        $this->bancoTipoCuentaId = $bancoTipoCuentaId;
    }

    /**
     *
     * @return integer
     */
    public function getBancoCtaContableId()
    {
        return $this->bancoCtaContableId;
    }

    /**
     *
     * @param integer $bancoCtaContableId            
     */
    public function setBancoCtaContableId($bancoCtaContableId)
    {
        $this->bancoCtaContableId = $bancoCtaContableId;
    }

    /**
     *
     * @return string
     */
    public function getNombreCanalRecaudacion()
    {
        return $this->nombreCanalRecaudacion;
    }

    /**
     *
     * @param string $nombreCanalRecaudacion            
     */
    public function setNombreCanalRecaudacion($nombreCanalRecaudacion)
    {
        $this->nombreCanalRecaudacion = $nombreCanalRecaudacion;
    }

    /**
     *
     * @return string
     */
    public function getDescripcionCanalRecaudacion()
    {
        return $this->descripcionCanalRecaudacion;
    }

    /**
     *
     * @param string $descripcionCanalRecaudacion            
     */
    public function setDescripcionCanalRecaudacion($descripcionCanalRecaudacion)
    {
        $this->descripcionCanalRecaudacion = $descripcionCanalRecaudacion;
    }

    /**
     *
     * @return string
     */
    public function getEstadoCanalRecaudacion()
    {
        return $this->estadoCanalRecaudacion;
    }

    /**
     *
     * @param string $estadoCanalRecaudacion            
     */
    public function setEstadoCanalRecaudacion($estadoCanalRecaudacion)
    {
        $this->estadoCanalRecaudacion = $estadoCanalRecaudacion;
    }

    /**
     *
     * @return string
     */
    public function getUsrCreacion()
    {
        return $this->usrCreacion;
    }

    /**
     *
     * @param string $usrCreacion            
     */
    public function setUsrCreacion($usrCreacion)
    {
        $this->usrCreacion = $usrCreacion;
    }

    /**
     *
     * @return \DateTime
     */
    public function getFeCreacion()
    {
        return $this->feCreacion;
    }

    /**
     *
     * @param \DateTime $feCreacion            
     */
    public function setFeCreacion(\DateTime $feCreacion)
    {
        $this->feCreacion = $feCreacion;
    }

    /**
     *
     * @return string
     */
    public function getUsrUltMod()
    {
        return $this->usrUltMod;
    }

    /**
     *
     * @param string $usrUltMod            
     */
    public function setUsrUltMod($usrUltMod)
    {
        $this->usrUltMod = $usrUltMod;
    }

    /**
     *
     * @return \DateTime
     */
    public function getFeUltMod()
    {
        return $this->feUltMod;
    }

    /**
     *
     * @param \DateTime $feUltMod            
     */
    public function setFeUltMod(\DateTime $feUltMod)
    {
        $this->feUltMod = $feUltMod;
    }

    /**
     *
     * @return string
     */
    public function getTituloHoja()
    {
        return $this->tituloHoja;
    }

    /**
     *
     * @param string $tituloHoja            
     */
    public function setTituloHoja($tituloHoja)
    {
        $this->tituloHoja = $tituloHoja;
    }

    /**
     *
     * @return integer
     */
    public function getFilaInicio()
    {
        return $this->filaInicio;
    }

    /**
     *
     * @param integer $filaInicio            
     */
    public function setFilaInicio($filaInicio)
    {
        $this->filaInicio = $filaInicio;
    }

    /**
     *
     * @return string
     */
    public function getColValidacion()
    {
        return $this->colValidacion;
    }

    /**
     *
     * @param string $colValidacion            
     */
    public function setColValidacion($colValidacion)
    {
        $this->colValidacion = $colValidacion;
    }

    /**
     *
     * @return string
     */
    public function getColValor()
    {
        return $this->colValor;
    }

    /**
     *
     * @param string $colValor            
     */
    public function setColValor($colValor)
    {
        $this->colValor = $colValor;
    }

    /**
     *
     * @return string
     */
    public function getColIdentificacion()
    {
        return $this->colIdentificacion;
    }

    /**
     *
     * @param string $colIdentificacion            
     */
    public function setColIdentificacion($colIdentificacion)
    {
        $this->colIdentificacion = $colIdentificacion;
    }

    /**
     *
     * @return string
     */
    public function getColFecha()
    {
        return $this->colFecha;
    }

    /**
     *
     * @param string $colFecha            
     */
    public function setColFecha($colFecha)
    {
        $this->colFecha = $colFecha;
    }

    /**
     *
     * @return string
     */
    public function getColReferencia()
    {
        return $this->colReferencia;
    }

    /**
     *
     * @param string $colReferencia            
     */
    public function setColReferencia($colReferencia)
    {
        $this->colReferencia = $colReferencia;
    }

    /**
     *
     * @return string
     */
    public function getColRespuesta()
    {
        return $this->colRespuesta;
    }

    /**
     *
     * @param string $colRespuesta            
     */
    public function setColRespuesta($colRespuesta)
    {
        $this->colRespuesta = $colRespuesta;
    }
    
    /**
     *
     * @return string
     */
    public function getColNombre()
    {
        return $this->colNombre;
    }

    /**
     *
     * @param string $colNombre            
     */
    public function setColNombre($colNombre)
    {
        $this->colNombre = $colNombre;
    }    

    /**
     *
     * @return string
     */
    public function getSepIdentificacion()
    {
        return $this->sepIdentificacion;
    }

    /**
     *
     * @param string $sepIdentificacion            
     */
    public function setSepIdentificacion($sepIdentificacion)
    {
        $this->sepIdentificacion = $sepIdentificacion;
    }

    /**
     *
     * @return string
     */
    public function getPadIdentificacion()
    {
        return $this->padIdentificacion;
    }

    /**
     *
     * @param string $padIdentificacion            
     */
    public function setPadIdentificacion($padIdentificacion)
    {
        $this->padIdentificacion = $padIdentificacion;
    }

    /**
     *
     * @return string
     */
    public function getRemIdentificacion()
    {
        return $this->remIdentificacion;
    }

    /**
     *
     * @param string $remIdentificacion            
     */
    public function setRemIdentificacion($remIdentificacion)
    {
        $this->remIdentificacion = $remIdentificacion;
    }

    /**
     *
     * @return integer
     */
    public function getBatchSize()
    {
        return $this->batchSize;
    }

    /**
     *
     * @param integer $batchSize
     */
    public function setBatchSize($batchSize)
    {
        $this->batchSize = $batchSize;
    }

}

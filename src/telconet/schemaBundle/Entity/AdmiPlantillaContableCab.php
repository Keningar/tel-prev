<?php
namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiPlantillaContableCab
 *
 * @ORM\Table(name="ADMI_PLANTILLA_CONTABLE_CAB")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiPlantillaContableCabRepository")
 */
class AdmiPlantillaContableCab
{
    /**
    * @var integer $id
    *
    * @ORM\Column(name="ID_PLANTILLA_CONTABLE_CAB", type="integer", nullable=false)
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="SEQUENCE")
    * @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_PLANTILLA_CONTAB_CAB", allocationSize=1, initialValue=1)
    */
    private $id;

    /**
    * @var string $formaPagoId
    *
    * @ORM\Column(name="FORMA_PAGO_ID", type="integer", nullable=false)
    */
    private $formaPagoId;

    /**
    * @var integer $tipoDocumentoId
    *
    * @ORM\Column(name="TIPO_DOCUMENTO_ID", type="integer", nullable=false)
    */
    private $tipoDocumentoId;

    /**
    * @var string $descripcion
    *
    * @ORM\Column(name="DESCRIPCION", type="string", nullable=false)
    */
    private $descripcion;

    /**
    * @var string $empresaCod
    *
    * @ORM\Column(name="EMPRESA_COD", type="string", nullable=false)
    */
    private $empresaCod;

    /**
    * @var string $ipCreacion
    *
    * @ORM\Column(name="IP_CREACION", type="string", nullable=false)
    */
    private $ipCreacion;

    /**
    * @var string $tablaCabecera
    *
    * @ORM\Column(name="TABLA_CABECERA", type="string", nullable=false)
    */
    private $tablaCabecera;

    /**
    * @var datetime $feCreacion
    *
    * @ORM\Column(name="FE_CREACION", type="datetime", nullable=false)
    */

    private $feCreacion;

    /**
    * @var string $usrCreacion
    *
    * @ORM\Column(name="USR_CREACION", type="string", nullable=false)
    */

    private $usrCreacion;

    /**
    * @var string $tablaDetalle
    *
    * @ORM\Column(name="TABLA_DETALLE", type="string", nullable=true)
    */
    private $tablaDetalle;

    /**
    * @var string $tipoProceso
    *
    * @ORM\Column(name="TIPO_PROCESO", type="string", nullable=false)
    */
    private $tipoProceso;

    /**
    * @var string $codDiario
    *
    * @ORM\Column(name="COD_DIARIO", type="string", nullable=false)
    */
    private $codDiario;

    /**
    * @var string $formatoNoDocuAsiento
    *
    * @ORM\Column(name="FORMATO_NO_DOCU_ASIENTO", type="string", nullable=false)
    */
    private $formatoNoDocuAsiento;

    /**
    * @var string $estado
    *
    * @ORM\Column(name="ESTADO", type="string", nullable=false)
    */
    private $estado;

    /**
    * @var string $formatoGlosa
    *
    * @ORM\Column(name="FORMATO_GLOSA", type="string", nullable=false)
    */
    private $formatoGlosa;

    /**
    * @var string $nombrePaqueteSql
    *
    * @ORM\Column(name="NOMBRE_PAQUETE_SQL", type="string", nullable=false)
    */
    private $nombrePaqueteSql;

    /**
    * @var string $tipoDoc
    *
    * @ORM\Column(name="TIPO_DOC", type="string", nullable=false)
    */
    private $tipoDoc;

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
    * Get formaPagoId
    *
    * @return integer
    */
    public function getFormaPagoId()
    {
        return $this->formaPagoId;
    }

    /**
    * Get tipoDocumentoId
    *
    * @return integer
    */
    public function getTipoDocumentoId()
    {
        return $this->tipoDocumentoId;
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
    * Get empresaCod
    *
    * @return string
    */
    public function getEmpresaCod()
    {
        return $this->empresaCod;
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
    * Get tablaCabecera
    *
    * @return string
    */
    public function getTablaCabecera()
    {
        return $this->tablaCabecera;
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
    * Get usrCreacion
    *
    * @return string
    */
    public function getUsrCreacion()
    {
        return $this->usrCreacion;
    }

    /**
    * Get tablaDetalle
    *
    * @return string
    */
    public function getTablaDetalle()
    {
        return $this->tablaDetalle;
    }

    /**
    * Get string
    *
    * @return tipoProceso
    */
    public function getTipoProceso()
    {
        return $this->tipoProceso;
    }

    /**
    * Get codDiario
    *
    * @return string
    */
    public function getCodDiario()
    {
        return $this->codDiario;
    }

    /**
    * Get formatoNoDocuAsiento
    *
    * @return string
    */
    public function getFormatoNoDocuAsiento()
    {
        return $this->formatoNoDocuAsiento;
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
    * Get formatoGlosa
    *
    * @return string
    */
    public function getFormatoGlosa()
    {
        return $this->formatoGlosa;
    }

    /**
    * Get nombrePaqueteSql
    *
    * @return string
    */
    public function getNombrePaqueteSql()
    {
        return $this->nombrePaqueteSql;
    }

    /**
    * Get tipoDoc
    *
    * @return string
    */
    public function getTipoDoc()
    {
        return $this->tipoDoc;
    }


    /**
    * Set formaPagoId
    *
    * @param integer $formaPagoId
    */
    public function setFormaPagoId($formaPagoId)
    {
        $this->formaPagoId = $formaPagoId;
    }

    /**
    * Set tipoDocumentoId
    *
    * @param integer $tipoDocumentoId
    */
    public function setTipoDocumentoId($tipoDocumentoId)
    {
        $this->tipoDocumentoId = $tipoDocumentoId;
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
    * Set empresaCod
    *
    * @param string $empresaCod
    */
    public function setEmpresaCod($empresaCod)
    {
        $this->empresaCod = $empresaCod;
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
    * Set tablaCabecera
    *
    * @param string $tablaCabecera
    */
    public function setTablaCabecera($tablaCabecera)
    {
        $this->tablaCabecera = $tablaCabecera;
    }

    /**
    * Set feCreacion
    *
    * @param string $feCreacion
    */
    public function setFeCreacion($feCreacion)
    {
        $this->feCreacion = $feCreacion;
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
    * Set tablaDetalle
    *
    * @param string $tablaDetalle
    */
    public function setTablaDetalle($tablaDetalle)
    {
        $this->tablaDetalle = $tablaDetalle;
    }

    /**
    * Set tipoProceso
    *
    * @param string $tipoProceso
    */
    public function setTipoProceso($tipoProceso)
    {
        $this->tipoProceso = $tipoProceso;
    }

    /**
    * Set codDiario
    *
    * @param string $codDiario
    */
    public function setCodDiario($codDiario)
    {
        $this->codDiario = $codDiario;
    }

    /**
    * Set formatoNoDocuAsiento
    *
    * @param string $formatoNoDocuAsiento
    */
    public function setFormatoNoDocuAsiento($formatoNoDocuAsiento)
    {
        $this->formatoNoDocuAsiento = $formatoNoDocuAsiento;
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
    * Set formatoGlosa
    *
    * @param string $formatoGlosa
    */
    public function setFormatoGlosa($formatoGlosa)
    {
        $this->formatoGlosa = $formatoGlosa;
    }

    /**
    * Set nombrePaqueteSql
    *
    * @param string $nombrePaqueteSql
    */
    public function setNombrePaqueteSql($nombrePaqueteSql)
    {
        $this->nombrePaqueteSql = $nombrePaqueteSql;
    }

    /**
    * Set tipoDoc
    *
    * @param string $tipoDoc
    */
    public function setTipoDoc($tipoDoc)
    {
        $this->tipoDoc = $tipoDoc;
    }
}

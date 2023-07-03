<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\MigraArckmm
 *
 * @ORM\Table(name="MIGRA_ARCKMM")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\MigraArckmmRepository")
 */
class MigraArckmm
{

    /**
    * @ORM\Column(name="NO_CIA", type="string", nullable=false)
    * @ORM\Id
    */	

    private $noCia;	

    /**
    * @var string $noCta
    *
    * @ORM\Column(name="NO_CTA", type="string", nullable=false)
    */	

    private $noCta;	

    /**
    * @var string $procedencia
    *
    * @ORM\Column(name="PROCEDENCIA", type="string", nullable=false)
    */	

    private $procedencia;

    /**
    * @var string $tipoDoc
    *
    * @ORM\Column(name="TIPO_DOC", type="string", nullable=false)
    */	

    private $tipoDoc;	

    /**
    * @var integer $noDocu
    *
    * @ORM\Column(name="NO_DOCU", type="integer", nullable=false)
    */	

    private $noDocu;

    /**
    * @var date $fecha
    *
    * @ORM\Column(name="FECHA", type="datetime", nullable=true)
    */		

    private $fecha;

    /**
    * @var string $beneficiario
    *
    * @ORM\Column(name="BENEFICIARIO", type="string", nullable=true)
    */	

    private $beneficiario;

    /**
    * @var string $comentario
    *
    * @ORM\Column(name="COMENTARIO", type="string", nullable=true)
    */		

    private $comentario;

    /**
    * @var float $monto
    *
    * @ORM\Column(name="MONTO", type="float", nullable=true)
    */		

    private $monto;

    /**
    * @var float $descuentoPP
    *
    * @ORM\Column(name="DESCUENTO_PP", type="float", nullable=true)
    */		

    private $descuentoPP;

    /**
    * @var string $estado
    *
    * @ORM\Column(name="ESTADO", type="string", nullable=true)
    */		

    private $estado;

    /**
    * @var string $conciliado
    *
    * @ORM\Column(name="CONCILIADO", type="string", nullable=true)
    */		

    private $conciliado;

    /**
    * @var integer $ano
    *
    * @ORM\Column(name="ANO", type="integer", nullable=true)
    */	

    private $ano;	

    /**
    * @var integer $mes
    *
    * @ORM\Column(name="MES", type="integer", nullable=true)
    */	

    private $mes;

    /**
    * @var date $fechaAnulado
    *
    * @ORM\Column(name="FECHA_ANULADO", type="date", nullable=true)
    */		

    private $fechaAnulado;

    /**
    * @var string $indBorrado
    *
    * @ORM\Column(name="IND_BORRADO", type="string", nullable=true)
    */		

    private $indBorrado;

    /**
    * @var string $indOtromov
    *
    * @ORM\Column(name="IND_OTROMOV", type="string", nullable=true)
    */		

    private $indOtromov;

    /**
    * @var string $monedaCta
    *
    * @ORM\Column(name="MONEDA_CTA", type="string", nullable=false)
    */		

    private $monedaCta;	

    /**
    * @var integer $tipoCambio
    *
    * @ORM\Column(name="TIPO_CAMBIO", type="integer", nullable=true)
    */		

    private $tipoCambio;

    /**
    * @var string $tipoAjuste
    *
    * @ORM\Column(name="TIPO_AJUSTE", type="string", nullable=true)
    */		

    private $tipoAjuste;	

    /**
    * @var string $indDist
    *
    * @ORM\Column(name="IND_DIST", type="string", nullable=true)
    */		

    private $indDist;

    /**
    * @var string $tCambCV
    *
    * @ORM\Column(name="T_CAMB_C_V", type="string", nullable=false)
    */		

    private $tCambCV;	

    /**
    * @var string $indOtrosMeses
    *
    * @ORM\Column(name="IND_OTROS_MESES", type="string", nullable=false)
    */		

    private $indOtrosMeses;

    /**
    * @var integer $mesConciliado
    *
    * @ORM\Column(name="MES_CONCILIADO", type="integer", nullable=true)
    */		

    private $mesConciliado;

    /**
    * @var integer $anoConciliado
    *
    * @ORM\Column(name="ANO_CONCILIADO", type="integer", nullable=true)
    */		

    private $anoConciliado;

    /**
    * @var string $noFisico
    *
    * @ORM\Column(name="NO_FISICO", type="string", nullable=true)
    */		

    private $noFisico;	

    /**
    * @var string $serieFisico
    *
    * @ORM\Column(name="SERIE_FISICO", type="string", nullable=false)
    */		

    private $serieFisico;

    /**
    * @var string $indCon
    *
    * @ORM\Column(name="IND_CON", type="string", nullable=false)
    */		

    private $indCon;

    /**
    * @var string $numeroCtl
    *
    * @ORM\Column(name="NUMERO_CTRL", type="integer", nullable=true)
    */

    private $numeroCtl;

    /**
    * @var string $origen
    *
    * @ORM\Column(name="ORIGEN", type="string", nullable=true)
    */		

    private $origen;	

    /**
    * @var string $usuarioCreacion
    *
    * @ORM\Column(name="USUARIO_CREACION", type="string", nullable=true)
    */		

    private $usuarioCreacion;

    /**
    * @var string $usuarioAnula
    *
    * @ORM\Column(name="USUARIO_ANULA", type="string", nullable=true)
    */		

    private $usuarioAnula;

    /**
    * @var string $usuarioProcesa
    *
    * @ORM\Column(name="USUARIO_PROCESA", type="string", nullable=true)
    */		

    private $usuarioProcesa;

    /**
    * @var date $fechaProcesa
    *
    * @ORM\Column(name="FECHA_PROCESA", type="datetime", nullable=true)
    */		

    private $fechaProcesa;

    /**
    * @var date $fechaDoc
    *
    * @ORM\Column(name="FECHA_DOC", type="datetime", nullable=true)
    */		

    private $fechaDoc;

    /**
    * @var string $indDivision
    *
    * @ORM\Column(name="IND_DIVISION", type="string", nullable=true)
    */		

    private $indDivision;

    /**
    * @var string $codDivision
    *
    * @ORM\Column(name="COD_DIVISION", type="string", nullable=true)
    */		

    private $codDivision;

    /**
    * @var string $procesado
    *
    * @ORM\Column(name="PROCESADO", type="string", nullable=true)
    */		

    private $procesado;

    /**
    * @var date $fechaCreacion
    *
    * @ORM\Column(name="FECHA_CREACION", type="datetime", nullable=true)
    */		

    private $fechaCreacion;

    /**
    * @var integer $idFormaPago
    *
    * @ORM\Column(name="ID_FORMA_PAGO", type="integer", nullable=true)
    */		

    private $idFormaPago;

    /**
    * @var integer $idOficinaFacturacion
    *
    * @ORM\Column(name="ID_OFICINA_FACTURACION", type="integer", nullable=true)
    */		

    private $idOficinaFacturacion;

    /**
    * @var integer $idMigracion
    *
    * @ORM\Column(name="ID_MIGRACION", type="integer", nullable=false)
    */		

    private $idMigracion;

    /**
    * @var integer $codDiario
    *
    * @ORM\Column(name="COD_DIARIO", type="string", nullable=false)
    */	
                
    private $codDiario;

    public function getNoCia(){
        return $this->noCia;
    }

    public function setNoCia($noCia){
        $this->noCia = $noCia;
    }

    public function getNoCta(){
        return $this->noCta;
    }

    public function setNoCta($noCta){
        $this->noCta = $noCta;
    }

    public function getProcedencia(){
        return $this->procedencia;
    }

    public function setProcedencia($procedencia){
        $this->procedencia = $procedencia;
    }

    public function getTipoDoc(){
        return $this->tipoDoc;
    }

    public function setTipoDoc($tipoDoc){
        $this->tipoDoc = $tipoDoc;
    }

    public function getNoDocu(){
        return $this->noDocu;
    }

    public function setNoDocu($noDocu){
        $this->noDocu = $noDocu;
    }

    public function getFecha(){
        return $this->fecha;
    }

    public function setFecha($fecha){
        $this->fecha = $fecha;
    }

    public function getBeneficiario(){
        return $this->beneficiario;
    }

    public function setBeneficiario($beneficiario){
        $this->beneficiario = $beneficiario;
    }

    public function getComentario(){
        return $this->comentario;
    }

    public function setComentario($comentario){
        $this->comentario = $comentario;
    }

    public function getMonto(){
        return $this->monto;
    }

    public function setMonto($monto){
        $this->monto = $monto;
    }

    public function getDescuentoPP(){
        return $this->descuentoPP;
    }

    public function setDescuentoPP($descuentoPP){
        $this->descuentoPP = $descuentoPP;
    }

    public function getEstado(){
        return $this->estado;
    }

    public function setEstado($estado){
        $this->estado = $estado;
    }

    public function getConciliado(){
        return $this->conciliado;
    }

    public function setConciliado($conciliado){
        $this->conciliado = $conciliado;
    }

    public function getAno(){
        return $this->ano;
    }

    public function setAno($ano){
        $this->ano = $ano;
    }

    public function getMes(){
        return $this->mes;
    }

    public function setMes($mes){
        $this->mes = $mes;
    }

    public function getFechaAnulado(){
        return $this->fechaAnulado;
    }

    public function setFechaAnulado($fechaAnulado){
        $this->fechaAnulado = $fechaAnulado;
    }

    public function getIndBorrado(){
        return $this->indBorrado;
    }

    public function setIndBorrado($indBorrado){
        $this->indBorrado = $indBorrado;
    }

    public function getIndOtromov(){
        return $this->indOtromov;
    }

    public function setIndOtromov($indOtromov){
        $this->indOtromov = $indOtromov;
    }

    public function getMonedaCta(){
        return $this->monedaCta;
    }

    public function setMonedaCta($monedaCta){
        $this->monedaCta = $monedaCta;
    }

    public function getTipoCambio(){
        return $this->tipoCambio;
    }

    public function setTipoCambio($tipoCambio){
        $this->tipoCambio = $tipoCambio;
    }

    public function getTipoAjuste(){
        return $this->tipoAjuste;
    }

    public function setTipoAjuste($tipoAjuste){
        $this->tipoAjuste = $tipoAjuste;
    }

    public function getIndDist(){
        return $this->indDist;
    }

    public function setIndDist($indDist){
        $this->indDist = $indDist;
    }

    public function getTCambCV(){
        return $this->tCambCV;
    }

    public function setTCambCV($tCambCV){
        $this->tCambCV = $tCambCV;
    }

    public function getIndOtrosMeses(){
        return $this->indOtrosMeses;
    }

    public function setIndOtrosMeses($indOtrosMeses){
        $this->indOtrosMeses = $indOtrosMeses;
    }

    public function getMesConciliado(){
        return $this->mesConciliado;
    }

    public function setMesConciliado($mesConciliado){
        $this->mesConciliado = $mesConciliado;
    }

    public function getAnoConciliado(){
        return $this->anoConciliado;
    }

    public function setAnoConciliado($anoConciliado){
        $this->anoConciliado = $anoConciliado;
    }

    public function getNoFisico(){
        return $this->noFisico;
    }

    public function setNoFisico($noFisico){
        $this->noFisico = $noFisico;
    }

    public function getSerieFisico(){
        return $this->serieFisico;
    }

    public function setSerieFisico($serieFisico){
        $this->serieFisico = $serieFisico;
    }

    public function getIndCon(){
        return $this->indCon;
    }

    public function setIndCon($indCon){
        $this->indCon = $indCon;
    }

    public function getNumeroCtl(){
        return $this->numeroCtl;
    }

    public function setNumeroCtl($numeroCtl){
        $this->numeroCtl = $numeroCtl;
    }

    public function getOrigen(){
        return $this->origen;
    }

    public function setOrigen($origen){
        $this->origen = $origen;
    }

    public function getUsuarioCreacion(){
        return $this->usuarioCreacion;
    }

    public function setUsuarioCreacion($usuarioCreacion){
        $this->usuarioCreacion = $usuarioCreacion;
    }

    public function getUsuarioAnula(){
        return $this->usuarioAnula;
    }

    public function setUsuarioAnula($usuarioAnula){
        $this->usuarioAnula = $usuarioAnula;
    }

    public function getUsuarioProcesa(){
        return $this->usuarioProcesa;
    }

    public function setUsuarioProcesa($usuarioProcesa){
        $this->usuarioProcesa = $usuarioProcesa;
    }

    public function getFechaProcesa(){
        return $this->fechaProcesa;
    }

    public function setFechaProcesa($fechaProcesa){
        $this->fechaProcesa = $fechaProcesa;
    }

    public function getFechaDoc(){
        return $this->fechaDoc;
    }

    public function setFechaDoc($fechaDoc){
        $this->fechaDoc = $fechaDoc;
    }

    public function getIndDivision(){
        return $this->indDivision;
    }

    public function setIndDivision($indDivision){
        $this->indDivision = $indDivision;
    }

    public function getCodDivision(){
        return $this->codDivision;
    }

    public function setCodDivision($codDivision){
        $this->codDivision = $codDivision;
    }

    public function getProcesado(){
        return $this->procesado;
    }

    public function setProcesado($procesado){
        $this->procesado = $procesado;
    }

    public function getFechaCreacion(){
        return $this->fechaCreacion;
    }

    public function setFechaCreacion($fechaCreacion){
        $this->fechaCreacion = $fechaCreacion;
    }

    public function getIdFormaPago(){
        return $this->idFormaPago;
    }

    public function setIdFormaPago($idFormaPago){
        $this->idFormaPago = $idFormaPago;
    }

    public function getIdOficinaFacturacion(){
        return $this->idOficinaFacturacion;
    }

    public function setIdOficinaFacturacion($idOficinaFacturacion){
        $this->idOficinaFacturacion = $idOficinaFacturacion;
    }

    public function getIdMigracion(){
        return $this->idMigracion;
    }

    public function setIdMigracion($idMigracion){
        $this->idMigracion = $idMigracion;
    }

    public function getCodDiario(){
        return $this->codDiario;
    }

    public function setCodDiario($codDiario){
        $this->codDiario = $codDiario;
    }
}

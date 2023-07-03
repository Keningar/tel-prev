<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\MigraArckml
 *
 * @ORM\Table(name="MIGRA_ARCKML")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\MigraArckmlRepository")
 */
class MigraArckml
{

    /**
    * @ORM\Column(name="NO_CIA", type="string", nullable=false)
    * @ORM\Id
    */	

    private $noCia;	

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
    * @var string $codCont
    *
    * @ORM\Column(name="COD_CONT", type="string", nullable=false)
    */	

    private $codCont;

    /**
    * @var string $centroCosto
    *
    * @ORM\Column(name="CENTRO_COSTO", type="string", nullable=true)
    */	

    private $centroCosto;

    /**
    * @var string $tipoMov
    *
    * @ORM\Column(name="TIPO_MOV", type="string", nullable=false)
    */	

    private $tipoMov;

    /**
    * @var float $monto
    *
    * @ORM\Column(name="MONTO", type="float", nullable=false)
    */		

    private $monto;

    /**
    * @var float $montoDol
    *
    * @ORM\Column(name="MONTO_DOL", type="float", nullable=true)
    */		

    private $montoDol;

    /**
    * @var float $tipoCambio
    *
    * @ORM\Column(name="TIPO_CAMBIO", type="integer", nullable=true)
    */		

    private $tipoCambio;

    /**
    * @var string $moneda
    *
    * @ORM\Column(name="MONEDA", type="string", nullable=true)
    */		

    private $moneda;

    /**
    * @var string $noAsiento
    *
    * @ORM\Column(name="NO_ASIENTO", type="string", nullable=true)
    */		

    private $noAsiento;

    /**
    * @var string $modificable
    *
    * @ORM\Column(name="MODIFICABLE", type="string", nullable=false)
    */		

    private $modificable;

    /**
    * @var string $codigoTercero
    *
    * @ORM\Column(name="CODIGO_TERCERO", type="string", nullable=true)
    */		

    private $codigoTercero;

    /**
    * @var string $indCon
    *
    * @ORM\Column(name="IND_CON", type="string", nullable=true)
    */		

    private $indCon;

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
    * @var float $montoDc
    *
    * @ORM\Column(name="MONTO_DC", type="float", nullable=true)
    */		

    private $montoDc;

    /**
    * @var string $glosa
    *
    * @ORM\Column(name="GLOSA", type="string", nullable=true)
    */		

    private $glosa;

    /**
    * @var integer $excedePresupuesto
    *
    * @ORM\Column(name="EXCEDE_PRESUPUESTO", type="integer", nullable=true)
    */		

    private $excedePresupuesto;

    /**
    * @var integer $migracionId
    *
    * @ORM\Column(name="MIGRACION_ID", type="integer", nullable=false)
    */		

    private $migracionId;

    /**
    * @var integer $linea
    *
    * @ORM\Column(name="LINEA", type="integer", nullable=false)
    */		

    private $linea;

    /**
    * @var string $codDiario
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

    public function getCodCont(){
        return $this->codCont;
    }

    public function setCodCont($codCont){
        $this->codCont = $codCont;
    }

    public function getCentroCosto(){
        return $this->centroCosto;
    }

    public function setCentroCosto($centroCosto){
        $this->centroCosto = $centroCosto;
    }

    public function getTipoMov(){
        return $this->tipoMov;
    }

    public function setTipoMov($tipoMov){
        $this->tipoMov = $tipoMov;
    }

    public function getMonto(){
        return $this->monto;
    }

    public function setMonto($monto){
        $this->monto = $monto;
    }

    public function getMontoDol(){
        return $this->montoDol;
    }

    public function setMontoDol($montoDol){
        $this->montoDol = $montoDol;
    }

    public function getTipoCambio(){
        return $this->tipoCambio;
    }

    public function setTipoCambio($tipoCambio){
        $this->tipoCambio = $tipoCambio;
    }

    public function getMoneda(){
        return $this->moneda;
    }

    public function setMoneda($moneda){
        $this->moneda = $moneda;
    }

    public function getNoAsiento(){
        return $this->noAsiento;
    }

    public function setNoAsiento($noAsiento){
        $this->noAsiento = $noAsiento;
    }

    public function getModificable(){
        return $this->modificable;
    }

    public function setModificable($modificable){
        $this->modificable = $modificable;
    }

    public function getCodigoTercero(){
        return $this->codigoTercero;
    }

    public function setCodigoTercero($codigoTercero){
        $this->codigoTercero = $codigoTercero;
    }

    public function getIndCon(){
        return $this->indCon;
    }

    public function setIndCon($indCon){
        $this->indCon = $indCon;
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

    public function getMontoDc(){
        return $this->montoDc;
    }

    public function setMontoDc($montoDc){
        $this->montoDc = $montoDc;
    }

    public function getGlosa(){
        return $this->glosa;
    }

    public function setGlosa($glosa){
        $this->glosa = $glosa;
    }

    public function getExcedePresupuesto(){
        return $this->excedePresupuesto;
    }

    public function setExcedePresupuesto($excedePresupuesto){
        $this->excedePresupuesto = $excedePresupuesto;
    }

    public function getMigracionId(){
        return $this->migracionId;
    }

    public function setMigracionId($migracionId){
        $this->migracionId = $migracionId;
    }

    public function getLinea(){
        return $this->linea;
    }

    public function setLinea($linea){
        $this->linea = $linea;
    }

    public function getCodDiario(){
        return $this->codDiario;
    }

    public function setCodDiario($codDiario){
        $this->codDiario = $codDiario;
    }

}

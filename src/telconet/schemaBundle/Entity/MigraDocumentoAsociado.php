<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\MigraDocumentoAsociado
 *
 * @ORM\Table(name="MIGRA_DOCUMENTO_ASOCIADO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\MigraDocumentoAsociadoRepository")
 */
class MigraDocumentoAsociado
{
    /**
    * @ORM\Column(name="DOCUMENTO_ORIGEN_ID", type="integer", nullable=false)
    * @ORM\Id
    */
    private $documentoOrigenId;

    /**
    * @ORM\Column(name="TIPO_DOC_MIGRACION", type="string", nullable=false)
    */
    private $tipoDocMigracion;

    /**
    * @ORM\Column(name="MIGRACION_ID", type="integer", nullable=false)
    */
    private $migracionId;

    /**
    * @ORM\Column(name="TIPO_MIGRACION", type="string", nullable=false)
    */
    private $tipoMigracion;

    /**
    * @ORM\Column(name="NO_CIA", type="string", nullable=false)
    */
    private $noCia;

    /**
    * @ORM\Column(name="FORMA_PAGO_ID", type="integer")
    */
    private $formaPagoId;

    /**
    * @ORM\Column(name="TIPO_DOCUMENTO_ID", type="integer")
    */
    private $tipoDocumentoId;

    /**
    * @ORM\Column(name="ESTADO", type="string", nullable=false)
    */
    private $estado;

    /**
    * @ORM\Column(name="USR_CREACION", type="string", nullable=false)
    */
    private $usrCreacion;

    /**
    * @ORM\Column(name="FE_CREACION", type="datetime", nullable=false)
    */
    private $feCreacion;

    /**
    * @ORM\Column(name="USR_ULT_MOD", type="string")
    */
    private $usrUltMod;

    /**
    * @ORM\Column(name="FE_ULT_MOD", type="date")
    */
    private $feUltMod;

    public function getDocumentoOrigenId(){
        return $this->documentoOrigenId;
    }

    public function setDocumentoOrigenId($documentoOrigenId){
        $this->documentoOrigenId = $documentoOrigenId;
    }

    public function getTipoDocMigracion(){
        return $this->tipoDocMigracion;
    }

    public function setTipoDocMigracion($tipoDocMigracion){
        $this->tipoDocMigracion = $tipoDocMigracion;
    }

    public function getMigracionId(){
        return $this->migracionId;
    }

    public function setMigracionId($migracionId){
        $this->migracionId = $migracionId;
    }

    public function getTipoMigracion(){
        return $this->tipoMigracion;
    }

    public function setTipoMigracion($tipoMigracion){
        $this->tipoMigracion = $tipoMigracion;
    }

    public function getNoCia(){
        return $this->noCia;
    }

    public function setNoCia($noCia){
        $this->noCia = $noCia;
    }

    public function getFormaPagoId(){
        return $this->formaPagoId;
    }

    public function setFormaPagoId($formaPagoId){
        $this->formaPagoId = $formaPagoId;
    }

    public function getTipoDocumentoId(){
        return $this->tipoDocumentoId;
    }

    public function setTipoDocumentoId($tipoDocumentoId){
        $this->tipoDocumentoId = $tipoDocumentoId;
    }

    public function getEstado(){
        return $this->estado;
    }

    public function setEstado($estado){
        $this->estado = $estado;
    }

    public function getUsrCreacion(){
        return $this->usrCreacion;
    }

    public function setUsrCreacion($usrCreacion){
        $this->usrCreacion = $usrCreacion;
    }

    public function getFeCreacion(){
        return $this->feCreacion;
    }

    public function setFeCreacion($feCreacion){
        $this->feCreacion = $feCreacion;
    }

    public function getUsrUltMod(){
        return $this->usrUltMod;
    }

    public function setUsrUltMod($usrUltMod){
        $this->usrUltMod = $usrUltMod;
    }

    public function getFeUltMod(){
        return $this->feUltMod;
    }

    public function setFeUltMod($feUltMod){
        $this->feUltMod = $feUltMod;
    }

}

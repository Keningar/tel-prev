<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert; 
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * telconet\schemaBundle\Entity\InfoContrato
 *
 * @ORM\Table(name="INFO_CONTRATO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoContratoRepository")
 */
class InfoContrato
{


    /**
    * @var AdmiTipoContrato
    *
    * @ORM\ManyToOne(targetEntity="AdmiTipoContrato")
    * @ORM\JoinColumns({
    *   @ORM\JoinColumn(name="TIPO_CONTRATO_ID", referencedColumnName="ID_TIPO_CONTRATO")
    * })
    */

    private $tipoContratoId;

    /**
    * @var datetime $feFinContrato
    *
    * @ORM\Column(name="FE_FIN_CONTRATO", type="datetime", nullable=true)
    */		

    private $feFinContrato;

    /**
    * @var float $valorContrato
    *
    * @ORM\Column(name="VALOR_CONTRATO", type="float", nullable=true)
    */		

    private $valorContrato;

    /**
    * @var integer $valorAnticipo
    *
    * @ORM\Column(name="VALOR_ANTICIPO", type="integer", nullable=true)
    */		

    private $valorAnticipo;

    /**
    * @var integer $valorGarantia
    *
    * @ORM\Column(name="VALOR_GARANTIA", type="integer", nullable=true)
    */		

    private $valorGarantia;

    /**
    * @var integer $id
    *
    * @ORM\Column(name="ID_CONTRATO", type="integer", nullable=false)
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="SEQUENCE")
    * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_CONTRATO", allocationSize=1, initialValue=1)
    */		

    private $id;	

    /**
    * @var string $numeroContrato
    *
    * @ORM\Column(name="NUMERO_CONTRATO", type="string", nullable=false)
    */		

    private $numeroContrato;

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
    * @var string $numeroContratoEmpPub
    *
    * @ORM\Column(name="NUMERO_CONTRATO_EMP_PUB", type="string", nullable=true)
    */		

    private $numeroContratoEmpPub;

    /**
    * @var AdmiFormaPago
    *
    * @ORM\ManyToOne(targetEntity="AdmiFormaPago")
    * @ORM\JoinColumns({
    *   @ORM\JoinColumn(name="FORMA_PAGO_ID", referencedColumnName="ID_FORMA_PAGO")
    * })
    */

    private $formaPagoId;

    /**
    * @var string $estado
    *
    * @ORM\Column(name="ESTADO", type="string", nullable=true)
    */		

    private $estado;

    /**
    * @var string $origen
    *
    * @ORM\Column(name="ORIGEN", type="string", nullable=true)
    */

    private $origen;

    /**
    * @var datetime $feCreacion
    *
    * @ORM\Column(name="FE_CREACION", type="datetime", nullable=false)
    */		

    private $feCreacion;

    /**
    * @var datetime $feAprobacion
    *
    * @ORM\Column(name="FE_APROBACION", type="datetime", nullable=false)
    */		

    private $feAprobacion;

    /**
    * @var string $usrCreacion
    *
    * @ORM\Column(name="USR_CREACION", type="string", nullable=false)
    */		

    private $usrCreacion;

    /**
    * @var string $usrAprobacion
    *
    * @ORM\Column(name="USR_APROBACION", type="string", nullable=false)
    */		

    private $usrAprobacion;

    /**
    * @var string $ipCreacion
    *
    * @ORM\Column(name="IP_CREACION", type="string", nullable=true)
    */		

    private $ipCreacion;


    /**
    * @ORM\Column(name="ARCHIVO_DIGITAL", type="string", length=255, nullable=false)
    */
    private $path;


    /**
    * @Assert\File(maxSize="6000000")
    */

    private $file;

    /**
    * @var datetime $feRechazo
    *
    * @ORM\Column(name="FE_RECHAZO", type="datetime", nullable=false)
    */		

    private $feRechazo;

    /**
    * @var string $usrRechazo
    *
    * @ORM\Column(name="USR_RECHAZO", type="string", nullable=false)
    */		

    private $usrRechazo;

    /**
    * @var string $motivoRechazoId
    *
    * @ORM\Column(name="MOTIVO_RECHAZO_ID", type="integer", nullable=false)
    */		

    private $motivoRechazoId;

    /**
    * @var string $usrRepLegal
    *
    * @ORM\Column(name="USR_REP_LEGAL", type="string", nullable=true)
    */		

    private $usrRepLegal;

    /**
    * @var string $incrementoAnual
    *
    * @ORM\Column(name="INCREMENTO_ANUAL", type="integer", nullable=true)
    */		

    private $incrementoAnual;

    /**
    * @var string $oficinaRepLegal
    *
    * @ORM\Column(name="OFICINA_REP_LEGAL", type="string", nullable=true)
    */		

    private $oficinaRepLegal;

    /**
    * @var datetime $feIniContrato
    *
    * @ORM\Column(name="FE_INI_CONTRATO", type="datetime", nullable=true)
    */		

    private $feIniContrato;


    /**
    * Get tipoContratoId
    *
    * @return telconet\schemaBundle\Entity\AdmiTipoContrato
    */		

    public function getTipoContratoId(){
        return $this->tipoContratoId; 
    }

    /**
    * Set tipoContratoId
    *
    * @param telconet\schemaBundle\Entity\AdmiTipoContrato $tipoContratoId
    */
    public function setTipoContratoId(\telconet\schemaBundle\Entity\AdmiTipoContrato $tipoContratoId)
    {
        $this->tipoContratoId = $tipoContratoId;
    }


    /**
    * Get feFinContrato
    *
    * @return datetime
    */		

    public function getFeFinContrato(){
        return $this->feFinContrato; 
    }

    /**
    * Set feFinContrato
    *
    * @param datetime $feFinContrato
    */
    public function setFeFinContrato($feFinContrato)
    {
        $this->feFinContrato = $feFinContrato;
    }


    /**
    * Get valorContrato
    *
    * @return float
    */		

    public function getValorContrato(){
        return $this->valorContrato; 
    }

    /**
    * Set valorContrato
    *
    * @param float $valorContrato
    */
    public function setValorContrato($valorContrato)
    {
        $this->valorContrato = $valorContrato;
    }


    /**
    * Get valorAnticipo
    *
    * @return integer
    */		

    public function getValorAnticipo(){
        return $this->valorAnticipo; 
    }

    /**
    * Set valorAnticipo
    *
    * @param integer $valorAnticipo
    */
    public function setValorAnticipo($valorAnticipo)
    {
        $this->valorAnticipo = $valorAnticipo;
    }


    /**
    * Get valorGarantia
    *
    * @return integer
    */		

    public function getValorGarantia(){
        return $this->valorGarantia; 
    }

    /**
    * Set valorGarantia
    *
    * @param integer $valorGarantia
    */
    public function setValorGarantia($valorGarantia)
    {
        $this->valorGarantia = $valorGarantia;
    }


    /**
    * Get id
    *
    * @return integer
    */		

    public function getId(){
        return $this->id; 
    }

    /**
    * Get numeroContrato
    *
    * @return string
    */		

    public function getNumeroContrato(){
        return $this->numeroContrato; 
    }

    /**
    * Set numeroContrato
    *
    * @param string $numeroContrato
    */
    public function setNumeroContrato($numeroContrato)
    {
        $this->numeroContrato = $numeroContrato;
    }


    /**
    * Get personaEmpresaRolId
    *
    * @return telconet\schemaBundle\Entity\InfoPersonaEmpresaRol
    */		

    public function getPersonaEmpresaRolId(){
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
    * Get numeroContratoEmpPub
    *
    * @return string
    */		

    public function getNumeroContratoEmpPub(){
        return $this->numeroContratoEmpPub; 
    }

    /**
    * Set numeroContratoEmpPub
    *
    * @param string $numeroContratoEmpPub
    */
    public function setNumeroContratoEmpPub($numeroContratoEmpPub)
    {
        $this->numeroContratoEmpPub = $numeroContratoEmpPub;
    }


    /**
    * Get formaPagoId
    *
    * @return telconet\schemaBundle\Entity\AdmiFormaPago
    */		

    public function getFormaPagoId(){
        return $this->formaPagoId; 
    }

    /**
    * Set formaPagoId
    *
    * @param telconet\schemaBundle\Entity\AdmiFormaPago $formaPagoId
    */
    public function setFormaPagoId(\telconet\schemaBundle\Entity\AdmiFormaPago $formaPagoId)
    {
        $this->formaPagoId = $formaPagoId;
    }


    /**
    * Get estado
    *
    * @return string
    */		

    public function getEstado(){
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
    * Get origen
    *
    * @return string
    */

    public function getOrigen(){
        return $this->origen; 
    }

    /**
    * Set origen
    *
    * @param string $origen
    */
    public function setOrigen($origen)
    {
        $this->origen = $origen;
    }


    /**
    * Get feAprobacion
    *
    * @return datetime
    */		

    public function getFeAprobacion(){
        return $this->feAprobacion; 
    }

    /**
    * Set feAprobacion
    *
    * @param datetime $feAprobacion
    */
    public function setFeAprobacion($feAprobacion)
    {
        $this->feAprobacion = $feAprobacion;
    }

    /**
    * Get feCreacion
    *
    * @return datetime
    */		

    public function getFeCreacion(){
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
    * Get usrAprobacion
    *
    * @return string
    */		

    public function getUsrAprobacion(){
        return $this->usrAprobacion; 
    }

    /**
    * Set usrAprobacion
    *
    * @param string $usrAprobacion
    */
    public function setUsrAprobacion($usrAprobacion)
    {
        $this->usrAprobacion = $usrAprobacion;
    }

    /**
    * Get usrCreacion
    *
    * @return string
    */		

    public function getUsrCreacion(){
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
    * Get oficinaRepLegal
    *
    * @return string
    */		

    public function getOficinaRepLegal(){
        return $this->oficinaRepLegal; 
    }

    /**
    * Set oficinaRepLegal
    *
    * @param string $oficinaRepLegal
    */
    public function setOficinaRepLegal($oficinaRepLegal)
    {
        $this->oficinaRepLegal = $oficinaRepLegal;
    }


    /**
    * Get ipCreacion
    *
    * @return string
    */		

    public function getIpCreacion(){
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
    * @ORM\PrePersist()
    * @ORM\PreUpdate()
    */
    public function preUpload()
    {
        if (null !== $this->file) {
            //generamos un nombre \FAnico de archivo
            $this->path = $this->numeroContrato."_digital.".$this->file->guessExtension();
        }

    }

    /**
    * @ORM\PostPersist()
    * @ORM\PostUpdate()
    */
    public function upload()
    {
        if (null === $this->file) { // El archivo no es obligatorio, por si viene vac\EDo
            return;
        }

        //Se lanza una excepci\F3n si el archivo no se puede mover para que la entidad no persista en la base de datos
        // labor que realizar autom\E1ticamente move()
        $this->file->move($this->getUploadRootDir(), $this->numeroContrato."_digital.".$this->file->guessExtension());
        unset($this->file);
    }

    /**
    * @ORM\PostRemove()
    * 
    */
    public function removeUpload()
    {
        if ($file = $this->getAbsolutePath()) {
            unlink($file);
        }
    }

    //M\E9todos b\E1sicos de subida
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
        return __DIR__.'/../../../../web/public/'.$this->getUploadDir();
        //return '/var/www/telconet/web/public/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        return '/uploads/digitales_contrato';
    }

    public function getWebPath1()
    {
        return null === $this->path ? null : '/telconet/web/public'.$this->getUploadDir().'/'.$this->path;
    }


    /**
    * Get feRechazo
    *
    * @return datetime
    */		

    public function getFeRechazo(){
        return $this->feRechazo; 
    }

    /**
    * Set feRechazo
    *
    * @param datetime $feRechazo
    */
    public function setFeRechazo($feRechazo)
    {
        $this->feRechazo = $feRechazo;
    }


    /**
    * Get usrRechazo
    *
    * @return string
    */		

    public function getUsrRechazo(){
        return $this->usrRechazo; 
    }

    /**
    * Set usrRechazo
    *
    * @param string $usrRechazo
    */
    public function setUsrRechazo($usrRechazo)
    {
        $this->usrRechazo = $usrRechazo;
    }


    /**
    * Get motivoRechazoId
    *
    * @return integer
    */		

    public function getMotivoRechazoId(){
        return $this->motivoRechazoId; 
    }

    /**
    * Set motivoRechazoId
    *
    * @param string $motivoRechazoId
    */
    public function setMotivoRechazoId($motivoRechazoId)
    {
        $this->motivoRechazoId = $motivoRechazoId;
    }


    /**
    * Get usrRepLegal
    *
    * @return string
    */		

    public function getUsrRepLegal(){
        return $this->usrRepLegal; 
    }

    /**
    * Set usrRepLegal
    *
    * @param string $usrRepLegal
    */
    public function setUsrRepLegal($usrRepLegal)
    {
        $this->usrRepLegal = $usrRepLegal;
    }


    /**
    * Get incrementoAnual
    *
    * @return string
    */		

    public function getIncrementoAnual(){
        return $this->incrementoAnual; 
    }

    /**
    * Set incrementoAnual
    *
    * @param string $incrementoAnual
    */
    public function setIncrementoAnual($incrementoAnual)
    {
        $this->incrementoAnual = $incrementoAnual;
    }

    /**
    * Get feIniContrato
    *
    * @return datetime
    */
    public function getFeIniContrato()
    {
        return $this->feIniContrato;
    }

    /**
    * Set feIniContrato
    *
    * @param datetime $feIniContrato
    */
    public function setFeIniContrato($feIniContrato)
    {
        $this->feIniContrato = $feIniContrato;
    }

}

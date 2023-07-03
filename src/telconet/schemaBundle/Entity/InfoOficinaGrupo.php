<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoOficinaGrupo
 *
 * @ORM\Table(name="INFO_OFICINA_GRUPO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoOficinaGrupoRepository")
 */
class InfoOficinaGrupo
{

/**
* @var integer $cantonId
*
* @ORM\Column(name="CANTON_ID", type="string", nullable=false)
*/
		
private $cantonId;

/**
* @var string $nombreOficina
*
* @ORM\Column(name="NOMBRE_OFICINA", type="string", nullable=false)
*/		
     		
private $nombreOficina;

/**
* @var string $direccionOficina
*
* @ORM\Column(name="DIRECCION_OFICINA", type="string", nullable=false)
*/		
     		
private $direccionOficina;

/**
* @var string $telefonoFijoOficina
*
* @ORM\Column(name="TELEFONO_FIJO_OFICINA", type="string", nullable=false)
*/		
     		
private $telefonoFijoOficina;

/**
* @var string $extensionOficina
*
* @ORM\Column(name="EXTENSION_OFICINA", type="string", nullable=false)
*/		
     		
private $extensionOficina;

/**
* @var string $faxOficina
*
* @ORM\Column(name="FAX_OFICINA", type="string", nullable=true)
*/		
     		
private $faxOficina;

/**
* @var string $codigoPostalOfi
*
* @ORM\Column(name="CODIGO_POSTAL_OFI", type="string", nullable=false)
*/		
     		
private $codigoPostalOfi;

/**
* @var string $esMatriz
*
* @ORM\Column(name="ES_MATRIZ", type="string", nullable=false)
*/		
     		
private $esMatriz;

/**
* @var string $esOficinaFacturacion
*
* @ORM\Column(name="ES_OFICINA_FACTURACION", type="string", nullable=false)
*/		
     		
private $esOficinaFacturacion;

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
* @var string $ipCreacion
*
* @ORM\Column(name="IP_CREACION", type="string", nullable=true)
*/		
     		
private $ipCreacion;

/**
* @var string $esVirtual
*
* @ORM\Column(name="ES_VIRTUAL", type="string", nullable=true)
*/		
     		
private $esVirtual;

/**
* @var string $territorio
*
* @ORM\Column(name="TERRITORIO", type="string", nullable=true)
*/		
     		
private $territorio;

/**
* @var string $ctaContableClientes
*
* @ORM\Column(name="CTA_CONTABLE_CLIENTES", type="string", nullable=true)
*/		
     		
private $ctaContableClientes;

/**
* @var string $ctaContableAnticipos
*
* @ORM\Column(name="CTA_CONTABLE_ANTICIPOS", type="string", nullable=true)
*/		
     		
private $ctaContableAnticipos;

/**
* @var string $ctaContablePagos
*
* @ORM\Column(name="CTA_CONTABLE_PAGOS", type="string", nullable=true)
*/		
     		
private $ctaContablePagos;

/**
* @var string $noCta
*
* @ORM\Column(name="NO_CTA", type="string", nullable=true)
*/		
     		
private $noCta;

/**
* @var string $ctaContableCargo
*
* @ORM\Column(name="CTA_CONTABLE_CARGO", type="string", nullable=true)
*/		
     		
private $ctaContableCargo;

/**
* @var integer $id
*
* @ORM\Column(name="ID_OFICINA", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_OFICINA_GRUPO", allocationSize=1, initialValue=1)
*/		
		
private $id;	


/**
* @var InfoEmpresaGrupo
*
* @ORM\ManyToOne(targetEntity="InfoEmpresaGrupo", inversedBy="empresagrupo_oficina")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="EMPRESA_ID", referencedColumnName="COD_EMPRESA")
* })
*/		
private $empresaId;

/**
* @var string $numEstabSri
*
* @ORM\Column(name="NUM_ESTAB_SRI", type="string", nullable=true)
*/		
     		
private $numEstabSri;


/**
* @var $refOficinaId
*
* @ORM\Column(name="REF_OFICINA_ID", type="integer", nullable=true)
*/	
 private $refOficinaId;

 

/**
* Get cantonId
*
* @return integer
*/		
     		
public function getCantonId(){
	return $this->cantonId; 
}

/**
* Set cantonId
*
* @param integer $cantonId
*/
public function setCantonId($cantonId)
{
        $this->cantonId = $cantonId;
}


/**
* Get nombreOficina
*
* @return string
*/		
     		
public function getNombreOficina(){
	return $this->nombreOficina; 
}

/**
* Set nombreOficina
*
* @param string $nombreOficina
*/
public function setNombreOficina($nombreOficina)
{
        $this->nombreOficina = $nombreOficina;
}


/**
* Get direccionOficina
*
* @return string
*/		
     		
public function getDireccionOficina(){
	return $this->direccionOficina; 
}

/**
* Set direccionOficina
*
* @param string $direccionOficina
*/
public function setDireccionOficina($direccionOficina)
{
        $this->direccionOficina = $direccionOficina;
}


/**
* Get telefonoFijoOficina
*
* @return string
*/		
     		
public function getTelefonoFijoOficina(){
	return $this->telefonoFijoOficina; 
}

/**
* Set telefonoFijoOficina
*
* @param string $telefonoFijoOficina
*/
public function setTelefonoFijoOficina($telefonoFijoOficina)
{
        $this->telefonoFijoOficina = $telefonoFijoOficina;
}


/**
* Get extensionOficina
*
* @return string
*/		
     		
public function getExtensionOficina(){
	return $this->extensionOficina; 
}

/**
* Set extensionOficina
*
* @param string $extensionOficina
*/
public function setExtensionOficina($extensionOficina)
{
        $this->extensionOficina = $extensionOficina;
}


/**
* Get faxOficina
*
* @return string
*/		
     		
public function getFaxOficina(){
	return $this->faxOficina; 
}

/**
* Set faxOficina
*
* @param string $faxOficina
*/
public function setFaxOficina($faxOficina)
{
        $this->faxOficina = $faxOficina;
}


/**
* Get codigoPostalOfi
*
* @return string
*/		
     		
public function getCodigoPostalOfi(){
	return $this->codigoPostalOfi; 
}

/**
* Set codigoPostalOfi
*
* @param string $codigoPostalOfi
*/
public function setCodigoPostalOfi($codigoPostalOfi)
{
        $this->codigoPostalOfi = $codigoPostalOfi;
}


/**
* Get esMatriz
*
* @return string
*/		
     		
public function getEsMatriz(){
	return $this->esMatriz; 
}

/**
* Set esMatriz
*
* @param string $esMatriz
*/
public function setEsMatriz($esMatriz)
{
        $this->esMatriz = $esMatriz;
}


/**
* Get esOficinaFacturacion
*
* @return string
*/		
     		
public function getEsOficinaFacturacion(){
	return $this->esOficinaFacturacion; 
}

/**
* Set esOficinaFacturacion
*
* @param string $esOficinaFacturacion
*/
public function setEsOficinaFacturacion($esOficinaFacturacion)
{
        $this->esOficinaFacturacion = $esOficinaFacturacion;
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


/**
* Get esVirtual
*
* @return string
*/		
     		
public function getEsVirtual(){
	return $this->esVirtual; 
}

/**
* Set esVirtual
*
* @param string $esVirtual
*/
public function setEsVirtual($esVirtual)
{
        $this->esVirtual = $esVirtual;
}


/**
* Get territorio
*
* @return string
*/		
     		
public function getTerritorio(){
	return $this->territorio; 
}

/**
* Set territorio
*
* @param string $territorio
*/
public function setTerritorio($territorio)
{
        $this->territorio = $territorio;
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
* Get empresaId
*
* @return telconet\schemaBundle\Entity\InfoEmpresaGrupo
*/		
     		
public function getEmpresaId(){
	return $this->empresaId; 
}

/**
* Set empresaId
*
* @param telconet\schemaBundle\Entity\InfoEmpresaGrupo $empresaId
*/
public function setEmpresaId(\telconet\schemaBundle\Entity\InfoEmpresaGrupo $empresaId)
{
        $this->empresaId = $empresaId;
}

/**
* Get ctaContableClientes
*
* @return string
*/		
     		
public function getCtaContableClientes(){
	return $this->ctaContableClientes; 
}

/**
* Set ctaContableClientes
*
* @param string $ctaContableClientes
*/
public function setCtaContableClientes($ctaContableClientes)
{
        $this->ctaContableClientes = $ctaContableClientes;
}

/**
* Get ctaContableAnticipos
*
* @return string
*/		
     		
public function getCtaContableAnticipos(){
	return $this->ctaContableAnticipos; 
}

/**
* Set ctaContableAnticipos
*
* @param string $ctaContableAnticipos
*/
public function setCtaContableAnticipos($ctaContableAnticipos)
{
        $this->ctaContableAnticipos = $ctaContableAnticipos;
}

/**
* Get ctaContableCargo
*
* @return string
*/		
     		
public function getCtaContableCargo(){
	return $this->ctaContableCargo; 
}

/**
* Set ctaContableCargo
*
* @param string $ctaContableCargo
*/
public function setCtaContableCargo($ctaContableCargo)
{
        $this->ctaContableCargo = $ctaContableCargo;
}

/**
* Get ctaContablePagos
*
* @return string
*/		
     		
public function getCtaContablePagos(){
	return $this->ctaContablePagos; 
}

/**
* Set ctaContableClientes
*
* @param string $ctaContableClientes
*/
public function setCtaContablePagos($ctaContablePagos)
{
        $this->ctaContablePagos = $ctaContablePagos;
}

/**
* Get noCta
*
* @return string
*/		
     		
public function getNoCta(){
	return $this->noCta; 
}

/**
* Set noCta
*
* @param string $noCta
*/
public function setNoCta($noCta)
{
        $this->noCta = $noCta;
}    

/**
* Get numEstabSri
*
* @return string
*/	
public function getNumEstabSri(){
	return $this->numEstabSri; 
}

/**
* Set numEstabSri
*
* @param string $numEstabSri
*/
public function setNumEstabSri($numEstabSri)
{
        $this->numEstabSri = $numEstabSri;
}

public function __toString(){
  return $this->nombreOficina;
}

    /**
     * Get getRefOficinaId
     *
     * @return integer
     */
    public function getRefOficinaId()
    {
        return $this->refOficinaId;
    }

    /**
     * Get setRefOficinaId
     *
     * @return integer
     */
    public function setRefOficinaId($refOficinaId)
    {
        $this->refOficinaId = $refOficinaId;
    }
}

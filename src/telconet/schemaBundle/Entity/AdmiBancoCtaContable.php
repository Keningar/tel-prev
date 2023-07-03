<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiBancoCtaContable
 *
 * @ORM\Table(name="ADMI_BANCO_CTA_CONTABLE")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiBancoCtaContableRepository")
 */
class AdmiBancoCtaContable
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_BANCO_CTA_CONTABLE", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_BANCO_CTA_CONTABLE", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var AdmiBancoTipoCuenta
*
* @ORM\ManyToOne(targetEntity="AdmiBancoTipoCuenta")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="BANCO_TIPO_CUENTA_ID", referencedColumnName="ID_BANCO_TIPO_CUENTA")
* })
*/
		
private $bancoTipoCuentaId;


/**
* @var string $descripcion
*
* @ORM\Column(name="DESCRIPCION", type="string", nullable=true)
*/		
     		
private $descripcion;

/**
* @var string $ctaContable
*
* @ORM\Column(name="CTA_CONTABLE", type="string", nullable=true)
*/		
     		
private $ctaContable;

/**
* @var string $ctaContableAntSinClientes
*
* @ORM\Column(name="CTA_CONTABLE_ANT_SIN_CLIENTES", type="string", nullable=true)
*/		
     		
private $ctaContableAntSinClientes;

/**
* @var string $noCta
*
* @ORM\Column(name="NO_CTA", type="string", nullable=true)
*/		
     		
private $noCta;

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
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=false)
*/		
     		
private $estado;


/**
* @var string $empresaCod
*
* @ORM\Column(name="EMPRESA_COD", type="string", nullable=true)
*/		
     		
private $empresaCod;

public function getEmpresaCod() {
    return $this->empresaCod;
}

public function setEmpresaCod($empresaCod) {
    $this->empresaCod = $empresaCod;
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
* Get bancoTipoCuentaId
*
* @return telconet\schemaBundle\Entity\AdmiBancoTipoCuenta
*/		
     		
public function getBancoTipoCuentaId(){
	return $this->bancoTipoCuentaId; 
}

/**
* Set bancoTipoCuentaId
*
* @param telconet\schemaBundle\Entity\AdmiBancoTipoCuenta $bancoTipoCuentaId
*/
public function setBancoTipoCuentaId(\telconet\schemaBundle\Entity\AdmiBancoTipoCuenta $bancoTipoCuentaId)
{
        $this->bancoTipoCuentaId = $bancoTipoCuentaId;
}


/**
* Get descripcion
*
* @return string
*/		
     		
public function getDescripcion(){
	return $this->descripcion; 
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
* Get ctaContable
*
* @return string
*/		
     		
public function getCtaContable(){
	return $this->ctaContable; 
}

/**
* Set ctaContable
*
* @param string $ctaContable
*/
public function setCtaContable($ctaContable)
{
        $this->ctaContable = $ctaContable;
}

/**
* Get ctaContableAntSinClientes
*
* @return string
*/		
     		
public function getCtaContableAntSinClientes(){
	return $this->ctaContableAntSinClientes; 
}

/**
* Set ctaContableAntSinClientes
*
* @param string $ctaContableAntSinClientes
*/
public function setCtaContableSinClientes($ctaContableAntSinClientes)
{
        $this->ctaContableAntSinClientes = $ctaContableAntSinClientes;
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

public function __toString()
{
    return $this->getDescripcion();
}

}

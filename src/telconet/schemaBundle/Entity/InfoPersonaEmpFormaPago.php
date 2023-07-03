<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoPersonaEmpFormaPago
 *
 * @ORM\Table(name="INFO_PERSONA_EMP_FORMA_PAGO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoPersonaEmpFormaPagoRepository")
 */
class InfoPersonaEmpFormaPago
{

/**
* @var integer $id
*
* @ORM\Column(name="ID_DATOS_PAGO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_PERSONA_EMP_FORMA_PAG", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
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
* @var AdmiBancoTipoCuenta
*
* @ORM\ManyToOne(targetEntity="AdmiBancoTipoCuenta")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="BANCO_TIPO_CUENTA_ID", referencedColumnName="ID_BANCO_TIPO_CUENTA")
* })
*/
		
private $bancoTipoCuentaId;


/**
* @var AdmiTipoCuenta
*
* @ORM\ManyToOne(targetEntity="AdmiTipoCuenta")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="TIPO_CUENTA_ID", referencedColumnName="ID_TIPO_CUENTA")
* })
*/

private $tipoCuentaId;


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
* @ORM\Column(name="USR_CREACION", type="string", nullable=false)
*/		
     		
private $usrCreacion;

/**
* @var string $usrUltMod
*
* @ORM\Column(name="USR_ULT_MOD", type="string", nullable=true)
*/		
     		
private $usrUltMod;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=true)
*/		
     		
private $estado;

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
* @var string $ipCreacion
*
* @ORM\Column(name="IP_CREACION", type="string", nullable=true)
*/		
 

private $ipCreacion;


/**
* Get id
*
* @return integer
*/		
 

public function getId(){
	return $this->id; 
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
public function setBancoTipoCuentaId(\telconet\schemaBundle\Entity\AdmiBancoTipoCuenta $bancoTipoCuentaId = NULL)
{
        $this->bancoTipoCuentaId = $bancoTipoCuentaId;
}

/**
* Get tipoCuentaId
*
* @return telconet\schemaBundle\Entity\AdmiTipoCuenta
*/		
     		
public function getTipoCuentaId(){
	return $this->tipoCuentaId; 
}

/**
* Set tipoCuentaId
*
* @param telconet\schemaBundle\Entity\AdmiTipoCuenta $TipoCuentaId
*/
public function setTipoCuentaId(\telconet\schemaBundle\Entity\AdmiTipoCuenta $TipoCuentaId = NULL)
{
        $this->tipoCuentaId = $TipoCuentaId;
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
* Get feUltMod
*
* @return datetime
*/		
     		
public function getFeUltMod(){
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
* Get usrUltMod
*
* @return string
*/		
     		
public function getUsrUltMod(){
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

}
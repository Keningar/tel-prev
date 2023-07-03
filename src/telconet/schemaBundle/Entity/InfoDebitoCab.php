<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoDebitoCab
 *
 * @ORM\Table(name="INFO_DEBITO_CAB")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoDebitoCabRepository")
 */
class InfoDebitoCab
{


/**
* @var integer $oficinaId
*
* @ORM\Column(name="OFICINA_ID", type="integer", nullable=false)
*/		
     		
private $oficinaId;

/**
* @var integer $empresaId
*
* @ORM\Column(name="EMPRESA_ID", type="integer", nullable=false)
*/		
     		
private $empresaId;

/**
* @var integer $id
*
* @ORM\Column(name="ID_DEBITO_CAB", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_DEBITO_CAB", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var InfoDebitoGeneral
*
* @ORM\ManyToOne(targetEntity="InfoDebitoGeneral")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="DEBITO_GENERAL_ID", referencedColumnName="ID_DEBITO_GENERAL")
* })
*/
		
private $debitoGeneralId;

/**
* @var integer $bancoTipoCuentaId
*
* @ORM\Column(name="BANCO_TIPO_CUENTA_ID", type="integer", nullable=true)
*/			
private $bancoTipoCuentaId;

/**
* @var integer $valorTotal
*
* @ORM\Column(name="VALOR_TOTAL", type="integer", nullable=true)
*/		
     		
private $valorTotal;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=true)
*/		
     		
private $estado;

/**
* @var string $procesado
*
* @ORM\Column(name="PROCESADO", type="string", nullable=true)
*/		
     		
private $procesado;

/**
* @var datetime $feCreacion
*
* @ORM\Column(name="FE_CREACION", type="datetime", nullable=true)
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
* @ORM\Column(name="USR_CREACION", type="string", nullable=true)
*/		
     		
private $usrCreacion;

/**
* @var string $usrUltMod
*
* @ORM\Column(name="USR_ULT_MOD", type="string", nullable=true)
*/		
     		
private $usrUltMod;

/**
* @var string $tipoEscenario
*
* @ORM\Column(name="TIPO_ESCENARIO", type="string", nullable=true)
*/		
     		
private $tipoEscenario;

/**
* @var string $filtroEscenario
*
* @ORM\Column(name="FILTRO_ESCENARIO", type="string", nullable=true)
*/		
     		
private $filtroEscenario;

/**
* Get oficinaId
*
* @return integer
*/		
     		
public function getOficinaId(){
	return $this->oficinaId; 
}

/**
* Set oficinaId
*
* @param integer $oficinaId
*/
public function setOficinaId($oficinaId)
{
        $this->oficinaId = $oficinaId;
}


/**
* Get empresaId
*
* @return integer
*/		
     		
public function getEmpresaId(){
	return $this->empresaId; 
}

/**
* Set empresaId
*
* @param integer $empresaId
*/
public function setEmpresaId($empresaId)
{
        $this->empresaId = $empresaId;
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
* Get debitoGeneralId
*
* @return telconet\schemaBundle\Entity\InfoDebitoGeneral
*/		
     		
public function getDebitoGeneralId(){
	return $this->debitoGeneralId; 
}

/**
* Set debitoGeneralId
*
* @param telconet\schemaBundle\Entity\InfoDebitoGeneral $debitoGeneralId
*/
public function setDebitoGeneralId(\telconet\schemaBundle\Entity\InfoDebitoGeneral $debitoGeneralId)
{
        $this->debitoGeneralId = $debitoGeneralId;
}


/**
* Get bancoTipoCuentaId
*
* @return integer
*/		
     		
public function getBancoTipoCuentaId(){
	return $this->bancoTipoCuentaId; 
}

/**
* Set bancoTipoCuentaId
*
* @param integer $bancoTipoCuentaId
*/
public function setBancoTipoCuentaId($bancoTipoCuentaId)
{
        $this->bancoTipoCuentaId = $bancoTipoCuentaId;
}


/**
* Get valorTotal
*
* @return integer
*/		
     		
public function getValorTotal(){
	return $this->valorTotal; 
}

/**
* Set valorTotal
*
* @param integer $valorTotal
*/
public function setValorTotal($valorTotal)
{
        $this->valorTotal = $valorTotal;
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
* Get procesado
*
* @return string
*/		
     		
public function getProcesado(){
	return $this->procesado; 
}

/**
* Set procesado
*
* @param string $procesado
*/
public function setProcesado($procesado)
{
        $this->procesado = $procesado;
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
* Get tipoEscenario
*
* @return string
*/		
     		
public function getTipoEscenario(){
	return $this->tipoEscenario; 
}

/**
* Set tipoEscenario
*
* @param string $tipoEscenario
*/
public function setTipoEscenario($tipoEscenario)
{
        $this->tipoEscenario = $tipoEscenario;
}

/**
* Get filtroEscenario
*
* @return string
*/		
     		
public function getFiltroEscenario(){
	return $this->filtroEscenario; 
}

/**
* Set filtroEscenario
*
* @param string $filtroEscenario
*/
public function setFiltroEscenario($filtroEscenario)
{
        $this->filtroEscenario = $filtroEscenario;
}

}
<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoDebitoGeneral
 *
 * @ORM\Table(name="INFO_DEBITO_GENERAL")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoDebitoGeneralRepository")
 */
class InfoDebitoGeneral
{


/**
* @var integer $cuentaContableId
*
* @ORM\Column(name="CUENTA_CONTABLE_ID", type="integer", nullable=true)
*/		
     		
private $cuentaContableId;

/**
* @var string $ipCreacion
*
* @ORM\Column(name="IP_CREACION", type="string", nullable=true)
*/		
     		
private $ipCreacion;

/**
* @var integer $id
*
* @ORM\Column(name="ID_DEBITO_GENERAL", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_DEBITO_GENERAL", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var integer $oficinaId
*
* @ORM\Column(name="OFICINA_ID", type="integer", nullable=true)
*/		
     		
private $oficinaId;
	
/**
* @var integer $impuestoId
*
* @ORM\Column(name="IMPUESTO_ID", type="integer", nullable=true)
*/		
     		
private $impuestoId;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=true)
*/		
     		
private $estado;

/**
* @var datetime $feCreacion
*
* @ORM\Column(name="FE_CREACION", type="datetime", nullable=true)
*/		
     		
private $feCreacion;

/**
* @var string $usrCreacion
*
* @ORM\Column(name="USR_CREACION", type="string", nullable=true)
*/		
     		
private $usrCreacion;

/**
* @var string $ejecutando
*
* @ORM\Column(name="EJECUTANDO", type="string", nullable=true)
*/		
     		
private $ejecutando;

/**
* @ORM\Column(name="ARCHIVO", type="string", length=255, nullable=false)
*/
private $archivo;

/**
* @var integer $grupoDebitoId
*
* @ORM\Column(name="GRUPO_DEBITO_ID", type="integer", nullable=true)
*/		
     		
private $grupoDebitoId;

/**
* @var string $planificado
*
* @ORM\Column(name="PLANIFICADO", type="string", nullable=true)
*/		
     		
private $planificado;

/**
* @var datetime $fePlanificado
*
* @ORM\Column(name="FE_PLANIFICADO", type="datetime", nullable=true)
*/		
     		
private $fePlanificado;

/**
* @var string $parametrosPlanificado
*
* @ORM\Column(name="PARAMETROS_PLANIFICADO", type="string", length=400, nullable=true)
*/		
     		
private $parametrosPlanificado;

/**
* Get cuentaContableId
*
* @return integer
*/		
     		
public function getCuentaContableId(){
	return $this->CuentaContableId; 
}

/**
* Set cuentaContableId
*
* @param string $cuentaContableId
*/
public function setCuentaContableId($cuentaContableId)
{
        $this->cuentaContableId = $cuentaContableId;
}

    /**
     * @var $cicloId
     *
     * @ORM\ManyToOne(targetEntity="AdmiCiclo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="CICLO_ID", referencedColumnName="ID_CICLO")
     * })
     */
    private $cicloId;

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
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

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
* Get impuestoId
*
* @return integer
*/		   		
public function getImpuestoId()
{
	return $this->impuestoId; 
}

/**
* Set impuestoId
*
* @param integer $impuestoId
*/
public function setImpuestoId($impuestoId)
{
    $this->impuestoId = $impuestoId;
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
* Get feCreacion
*
* @return 
*/		
     		
public function getFeCreacion(){
	return $this->feCreacion; 
}

/**
* Set feCreacion
*
* @param  $feCreacion
*/
public function setFeCreacion($feCreacion)
{
        $this->feCreacion = $feCreacion;
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
* Set archivo
*
* @param string $archivo
*/
public function setArchivo($archivo)
{
        $this->archivo = $archivo;
}

/**
* Get archivo
*
* @return string
*/		

public function getArchivo()
{
        return $this->archivo;
}


/**
* Get ejecutando
*
* @return string
*/		
     		
public function getEjecutando(){
	return $this->ejecutando; 
}

/**
* Set ejecutando
*
* @param string $ejecutando
*/
public function setEjecutando($ejecutando)
{
        $this->ejecutando = $ejecutando;
}


/**
* Get grupoDebitoId
*
* @return integer
*/		
     		
public function getGrupoDebitoId(){
	return $this->grupoDebitoId; 
}

/**
* Set grupoDebitoId
*
* @param integer $grupoDebitoId
*/
public function setGrupoDebitoId($grupoDebitoId)
{
        $this->grupoDebitoId = $grupoDebitoId;
}


/**
* Get fePlanificado
*
* @return 
*/		
     		
public function getFePlanificado(){
	return $this->fePlanificado; 
}

/**
* Set feCreacion
*
* @param  $fePlanificado
*/
public function setFePlanificado($fePlanificado)
{
        $this->fePlanificado = $fePlanificado;
}


/**
* Get planificado
*
* @return string
*/		
     		
public function getPlanificado(){
	return $this->planificado; 
}

/**
* Set planificado
*
* @param string $planificado
*/
public function setPlanificado($planificado)
{
        $this->planificado = $planificado;
}

/**
* Get parametrosPlanificado
*
* @return string
*/		
     		
public function getParametrosPlanificado(){
	return $this->parametrosPlanificado; 
}

/**
* Set parametrosPlanificado
*
* @param string $parametrosPlanificado
*/
public function setParametrosPlanificado($parametrosPlanificado)
{
        $this->parametrosPlanificado = $parametrosPlanificado;
}


/**
     * Get cicloId
     *
     * @return \telconet\schemaBundle\Entity\AdmiCiclo
     */
    public function getCicloId()
    {
        return $this->cicloId;
    }

    /**
     * Set cicloId
     *
     * @param telconet\schemaBundle\Entity\AdmiCiclo $cicloId
     */
    public function setCicloId(\telconet\schemaBundle\Entity\AdmiCiclo $cicloId)
    {
        $this->cicloId = $cicloId;
    }

}


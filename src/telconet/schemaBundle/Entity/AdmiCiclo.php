<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiCiclo
 *
 * @ORM\Table(name="ADMI_CICLO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiCicloRepository")
 */
class AdmiCiclo
{


/**
* @var datetime $feInicio
*
* @ORM\Column(name="FE_INICIO", type="datetime", nullable=true)
*/		
     		
private $feInicio;

/**
* @var datetime $feFin
*
* @ORM\Column(name="FE_FIN", type="datetime", nullable=true)
*/		
     		
private $feFin;

/**
* @var string $observacion
*
* @ORM\Column(name="OBSERVACION", type="string", nullable=true)
*/		
     		
private $observacion;

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
* @var string $ipCreacion
*
* @ORM\Column(name="IP_CREACION", type="string", nullable=true)
*/		
     		
private $ipCreacion;

/**
* @var string $empresaCod
*
* @ORM\Column(name="EMPRESA_COD", type="string", nullable=false)
*/		
     		
private $empresaCod;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=true)
*/		
     		
private $estado;

/**
* @var integer $id
*
* @ORM\Column(name="ID_CICLO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_CICLO", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var string $nombreCiclo
*
* @ORM\Column(name="NOMBRE_CICLO", type="string", nullable=true)
*/		
     		
private $nombreCiclo;

/**
* @var string $codigo
*
* @ORM\Column(name="CODIGO", type="string", nullable=true)
*/
private $codigo;

/**
* Get feInicio
*
* @return datetime
*/		
     		
public function getFeInicio(){
	return $this->feInicio; 
}

/**
* Set feInicio
*
* @param datetime $feInicio
*/
public function setFeInicio($feInicio)
{
        $this->feInicio = $feInicio;
}


/**
* Get feFin
*
* @return datetime
*/		
     		
public function getFeFin(){
	return $this->feFin; 
}

/**
* Set feFin
*
* @param  datetime $feFin
*/
public function setFeFin($feFin)
{
        $this->feFin = $feFin;
}


/**
* Get observacion
*
* @return string
*/		
     		
public function getObservacion(){
	return $this->observacion; 
}

/**
* Set observacion
*
* @param string $observacion
*/
public function setObservacion($observacion)
{
        $this->observacion = $observacion;
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
     * Get codigo
     *
     * @return string
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set codigo
     *
     * @param string $codigo
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
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
 * Set empresaCod
 *
 * @param string $empresaCod     
 */
 public function setEmpresaCod($empresaCod)
 {
     $this->empresaCod = $empresaCod;
    
     return $this;
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
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Get nombreCiclo
*
* @return string
*/		
     		
public function getNombreCiclo(){
	return $this->nombreCiclo; 
}

/**
* Set nombreCiclo
*
* @param string $nombreCiclo
*/
public function setNombreCiclo($nombreCiclo)
{
        $this->nombreCiclo = $nombreCiclo;
}

}
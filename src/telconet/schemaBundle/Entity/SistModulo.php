<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\SistModulo
 *
 * @ORM\Table(name="SIST_MODULO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\SistModuloRepository")
 */
class SistModulo
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_MODULO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_SIST_MODULO", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var string $nombreModulo
*
* @ORM\Column(name="NOMBRE_MODULO", type="string", nullable=true)
*/		
     		
private $nombreModulo;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=true)
*/		
     		
private $estado;

/**
* @var integer $codigo
*
* @ORM\Column(name="CODIGO", type="integer", nullable=true)
*/		
     		
private $codigo;

/**
* @var string $usrCreacion
*
* @ORM\Column(name="USR_CREACION", type="string", nullable=true)
*/		
     		
private $usrCreacion;

/**
* @var datetime $feCreacion
*
* @ORM\Column(name="FE_CREACION", type="datetime", nullable=true)
*/		
     		
private $feCreacion;

/**
* @var string $usrUltMod
*
* @ORM\Column(name="USR_ULT_MOD", type="string", nullable=true)
*/		
     		
private $usrUltMod;

/**
* @var datetime $feUltMod
*
* @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=true)
*/		
     		
private $feUltMod;

/**
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Get nombreModulo
*
* @return string
*/		
     		
public function getNombreModulo(){
	return $this->nombreModulo; 
}

/**
* Set nombreModulo
*
* @param string $nombreModulo
*/
public function setNombreModulo($nombreModulo)
{
        $this->nombreModulo = $nombreModulo;
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
* Get codigo
*
* @return integer
*/		
     		
public function getCodigo(){
	return $this->codigo; 
}

/**
* Set codigo
*
* @param integer $codigo
*/
public function setCodigo($codigo)
{
        $this->codigo = $codigo;
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

public function __toString()
{
        return $this->nombreModulo;
}

    /**
    * @ORM\OneToMany(targetEntity="SeguRelacionSistema", mappedBy="moduloId")
    */
    private $relacion_modulo;
    public function __construct()
    {
        $this->relacion_modulo = new \Doctrine\Common\Collections\ArrayCollection();
    }
    public function addRelacionModulo(\telconet\schemaBundle\Entity\SeguRelacionSistema $relacion_modulo)
    {
        $this->relacion_modulo[] = $relacion_modulo;
    }

    public function getRelacionModulo()
    {
        return $this->relacion_modulo;
    }

}
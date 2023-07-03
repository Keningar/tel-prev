<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\SistAccion
 *
 * @ORM\Table(name="SIST_ACCION")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\SistAccionRepository")
 */
class SistAccion
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_ACCION", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_SIST_ACCION", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var string $nombreAccion
*
* @ORM\Column(name="NOMBRE_ACCION", type="string", nullable=true)
*/		
     		
private $nombreAccion;

/**
* @var string $urlImagen
*
* @ORM\Column(name="URL_IMAGEN", type="string", nullable=true)
*/		
     		
private $urlImagen;

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
* Get nombreAccion
*
* @return string
*/		
     		
public function getNombreAccion(){
	return $this->nombreAccion; 
}

/**
* Set nombreAccion
*
* @param string $nombreAccion
*/
public function setNombreAccion($nombreAccion)
{
        $this->nombreAccion = $nombreAccion;
}


/**
* Get urlImagen
*
* @return string
*/		
     		
public function getUrlImagen(){
	return $this->urlImagen; 
}

/**
* Set urlImagen
*
* @param string $urlImagen
*/
public function setUrlImagen($urlImagen)
{
        $this->urlImagen = $urlImagen;
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
        return $this->nombreAccion;
}

    /**
    * @ORM\OneToMany(targetEntity="SeguRelacionSistema", mappedBy="accionId")
    */
    private $relacion_accion;
    public function __construct()
    {
        $this->relacion_accion = new \Doctrine\Common\Collections\ArrayCollection();
    }
    public function addRelacionAccion(\telconet\schemaBundle\Entity\SeguRelacionSistema $relacion_accion)
    {
        $this->relacion_accion[] = $relacion_accion;
    }

    public function getRelacionAccion()
    {
        return $this->relacion_accion;
    }
    
}
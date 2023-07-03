<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoCuadrilla
 *
 * @ORM\Table(name="INFO_CUADRILLA")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoCuadrillaRepository")
 */
class InfoCuadrilla
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_CUADRILLA", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_CUADRILLA", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var string $nombreCuadrilla
*
* @ORM\Column(name="NOMBRE_CUADRILLA", type="string", nullable=false)
*/		
     		
private $nombreCuadrilla;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=false)
*/		
     		
private $estado;

/**
* @var datetime $feCreacion
*
* @ORM\Column(name="FE_CREACION", type="datetime", nullable=false)
*/		
     		
private $feCreacion;

/**
* @var string $ipCreacion
*
* @ORM\Column(name="IP_CREACION", type="string", nullable=false)
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
* Get nombreCuadrilla
*
* @return string
*/		
     		
public function getNombreCuadrilla(){
	return $this->nombreCuadrilla; 
}

/**
* Set nombreCuadrilla
*
* @param string $nombreCuadrilla
*/
public function setNombreCuadrilla($nombreCuadrilla)
{
        $this->nombreCuadrilla = $nombreCuadrilla;
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
     * Set ipCreacion
     *
     * @param string $ipCreacion
     */
    public function setIpCreacion($ipCreacion)
    {
        $this->ipCreacion = $ipCreacion;
    
        return $this;
    }

    /**
     * Get ipCreacion
     *
     * @return string 
     */
    public function getIpCreacion()
    {
        return $this->ipCreacion;
    }

public function __toString()
{
        return $this->nombreCuadrilla;
}

}
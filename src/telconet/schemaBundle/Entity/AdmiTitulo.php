<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiTitulo
 *
 * @ORM\Table(name="ADMI_TITULO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiTituloRepository")
 */
class AdmiTitulo
{


/**
* @var string $genero
*
* @ORM\Column(name="GENERO", type="string", nullable=true)
*/		
     		
private $genero;

/**
* @var integer $id
*
* @ORM\Column(name="ID_TITULO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_TITULO", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var string $codigoTitulo
*
* @ORM\Column(name="CODIGO_TITULO", type="string", nullable=false)
*/		
     		
private $codigoTitulo;

/**
* @var string $descripcionTitulo
*
* @ORM\Column(name="DESCRIPCION_TITULO", type="string", nullable=false)
*/		
     		
private $descripcionTitulo;

/**
* @var datetime $feCreacion
*
* @ORM\Column(name="FE_CREACION", type="datetime", nullable=false)
*/		
     		
private $feCreacion;

/**
* @var string $usrCreacion
*
* @ORM\Column(name="USR_CREACION", type="string", nullable=false)
*/		
     		
private $usrCreacion;

/**
* @var datetime $feUltMod
*
* @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=true)
*/		
     		
private $feUltMod;

/**
* @var string $usrUltMod
*
* @ORM\Column(name="USR_ULT_MOD", type="string", nullable=true)
*/		
     		
private $usrUltMod;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=false)
*/		
     		
private $estado;

/**
* Get genero
*
* @return string
*/		
     		
public function getGenero(){
	return $this->genero; 
}

/**
* Set genero
*
* @param string $genero
*/
public function setGenero($genero)
{
        $this->genero = $genero;
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
* Get codigoTitulo
*
* @return string
*/		
     		
public function getCodigoTitulo(){
	return $this->codigoTitulo; 
}

/**
* Set codigoTitulo
*
* @param string $codigoTitulo
*/
public function setCodigoTitulo($codigoTitulo)
{
        $this->codigoTitulo = $codigoTitulo;
}


/**
* Get descripcionTitulo
*
* @return string
*/		
     		
public function getDescripcionTitulo(){
	return $this->descripcionTitulo; 
}

/**
* Set descripcionTitulo
*
* @param string $descripcionTitulo
*/
public function setDescripcionTitulo($descripcionTitulo)
{
        $this->descripcionTitulo = $descripcionTitulo;
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

public function __toString() {
    return $this->getDescripcionTitulo();
}

}
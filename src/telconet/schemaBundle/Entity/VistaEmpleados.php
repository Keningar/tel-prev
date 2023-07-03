<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\VistaEmpleados
 *
 * @ORM\Table(name="VISTA_EMPLEADOS")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\VistaEmpleadosRepository")
 */
class VistaEmpleados
{

/**
* @var integer $id
*
* @ORM\Column(name="ID_PERSONA", type="integer", nullable=false)
* @ORM\Id
*/		
		
private $id;	
	
/**
    * @var string $codEmpresa
*
* @ORM\Column(name="COD_EMPRESA", type="string", nullable=false)
*/		
     		
private $codEmpresa;

/**
* @var string $razonSocial
*
* @ORM\Column(name="RAZON_SOCIAL", type="string", nullable=false)
*/		
     		
private $razonSocial;

/**
* @var string $nombreCompleto
*
* @ORM\Column(name="NOMBRE_COMPLETO", type="string", nullable=false)
*/		
     		
private $nombreCompleto;

/**
* @var datetime $login
*
* @ORM\Column(name="LOGIN", type="string", nullable=false)
*/		
     		
private $login;

/**
* @var datetime $idRol
*
* @ORM\Column(name="ID_ROL", type="integer", nullable=false)
*/		
     		
private $idRol;

/**
* @var datetime $descripcionRol
*
* @ORM\Column(name="DESCRIPCION_ROL", type="string", nullable=false)
*/		
     		
private $descripcionRol;


public function getId(){
	return $this->id; 
}

/**
* Get codEmpresa
*
* @return string
*/		
     		
public function getCodEmpresa(){
	return $this->codEmpresa; 
}

/**
* Get razonSocial
*
* @return string
*/		
     		
public function getRazonSocial(){
	return $this->razonSocial; 
}

/**
* Get nombreCompleto
*
* @return string
*/		
     		
public function getNombreCompleto(){
	return $this->nombreCompleto; 
}

/**
* Get login
*
* @return string
*/		
     		
public function getLogin(){
	return $this->login; 
}

/**
* Get idRol
*
* @return string
*/		
     		
public function getIdRol(){
	return $this->idRol; 
}

/**
* Get descripcionRol
*
* @return string
*/		
     		
public function getDescripcionRol(){
	return $this->descripcionRol; 
}

}
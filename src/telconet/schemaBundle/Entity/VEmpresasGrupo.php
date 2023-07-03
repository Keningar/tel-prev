<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\VEmpresasGrupo
 *
 * @ORM\Table(name="V_EMPRESAS_GRUPO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\VEmpresasGrupoRepository")
 */
class VEmpresasGrupo
{


/**
* @var string $id
*
* @ORM\Column(name="NO_CIA", type="string", nullable=false)
* @ORM\Id
*/		
		
private $id;	
	
/**
* @var string $nombre
*
* @ORM\Column(name="NOMBRE", type="string", nullable=false)
*/		
     		
private $nombre;
	
/**
* @var string $nombre_largo
*
* @ORM\Column(name="NOMBRE_LARGO", type="string", nullable=false)
*/		
     		
private $nombre_largo;
	
/**
* @var string $razon_social
*
* @ORM\Column(name="RAZON_SOCIAL", type="string", nullable=false)
*/		
     		
private $razon_social;
	
/**
* @var string $id_tributario
*
* @ORM\Column(name="ID_TRIBUTARIO", type="string", nullable=false)
*/		
     		
private $id_tributario;
	
/**
* @var string $repre
*
* @ORM\Column(name="REPRE", type="string", nullable=false)
*/		
     		
private $repre;
	
/**
* @var string $direccion
*
* @ORM\Column(name="DIRECCION", type="string", nullable=false)
*/		
     		
private $direccion;
	
/**
* @var string $telefono
*
* @ORM\Column(name="TELEFONO", type="string", nullable=false)
*/		
     		
private $telefono;
	
/**
* @var string $fax
*
* @ORM\Column(name="FAX", type="string", nullable=false)
*/		
     		
private $fax;
	
/**
* @var string $e_mail
*
* @ORM\Column(name="E_MAIL", type="string", nullable=false)
*/		
     		
private $e_mail;

/**
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Get nombre
*
* @return string
*/		
     		
public function getNombre(){
	return $this->nombre; 
}

/**
* Set nombre
*
* @param string $nombre
*/
public function setNombre($nombre)
{
        $this->nombre = $nombre;
}

/**
* Get nombre_largo
*
* @return string
*/		
     		
public function getNombreLargo(){
	return $this->nombre_largo; 
}

/**
* Set nombre_largo
*
* @param string $nombre_largo
*/
public function setNombreLargo($nombre_largo)
{
        $this->nombre_largo = $nombre_largo;
}

/**
* Get razon_social
*
* @return string
*/		
     		
public function getRazonSocial(){
	return $this->razon_social; 
}

/**
* Set razon_social
*
* @param string $razon_social
*/
public function setRazonSocial($razon_social)
{
        $this->razon_social = $razon_social;
}

/**
* Get id_tributario
*
* @return string
*/		
     		
public function getIdTributario(){
	return $this->id_tributario; 
}

/**
* Set id_tributario
*
* @param string $id_tributario
*/
public function setIdTributario($id_tributario)
{
        $this->id_tributario = $id_tributario;
}

/**
* Get repre
*
* @return string
*/		
     		
public function getRepre(){
	return $this->repre; 
}

/**
* Set repre
*
* @param string $repre
*/
public function setRepre($repre)
{
        $this->repre = $repre;
}

/**
* Get direccion
*
* @return string
*/		
     		
public function getDireccion(){
	return $this->direccion; 
}

/**
* Set direccion
*
* @param string $direccion
*/
public function setDireccion($direccion)
{
        $this->direccion = $direccion;
}

/**
* Get telefono
*
* @return string
*/		
     		
public function getTelefono(){
	return $this->telefono; 
}

/**
* Set telefono
*
* @param string $telefono
*/
public function setTelefono($telefono)
{
        $this->telefono = $telefono;
}

/**
* Get fax
*
* @return string
*/		
     		
public function getFax(){
	return $this->fax; 
}

/**
* Set fax
*
* @param string $fax
*/
public function setFax($fax)
{
        $this->fax = $fax;
}

/**
* Get e_mail
*
* @return string
*/		
     		
public function getEMail(){
	return $this->e_mail; 
}

/**
* Set e_mail
*
* @param string $e_mail
*/
public function setEMail($e_mail)
{
        $this->e_mail = $e_mail;
}


public function __toString()
{
        return $this->nombre;
}

}
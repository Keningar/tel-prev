<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiCatalogos
 *
 * @ORM\Table(name="ADMI_CATALOGOS")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiCatalogosRepository")
 */
class AdmiCatalogos
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_CATALOGOS", type="integer", nullable=false)
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
* @var string $tipo
*
* @ORM\Column(name="TIPO", type="string", nullable=false)
*/		
     		
private $tipo;


/**
* @var text $jsonCatalogo
*
* @ORM\Column(name="JSON_CATALOGO", type="text", nullable=false)
*/		
     		
private $jsonCatalogo;

/**
* @var string $hashCatalogo
*
* @ORM\Column(name="HASH_CATALOGO", type="string", nullable=true)
*/		
     		
private $hashCatalogo;

/**
* @var datetime $feUltMod
*
* @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=false)
*/		
     		
private $feUltMod;

/**
* Get id
*
* @return integer
*/		
     		
public function getId()
{
	return $this->id; 
}

/**
* Get codEmpresa
*
* @return string
*/		
     		
public function getCodEmpresa()
{
	return $this->codEmpresa; 
}

/**
* Set codEmpresa
*
* @param string $strCodEmpresa
*/
public function setCodEmpresa($strCodEmpresa)
{
        $this->codEmpresa = $strCodEmpresa;
}

/**
* Get tipo
*
* @return string
*/		
     		
public function getTipo()
{
	return $this->tipo; 
}

/**
* Set tipo
*
* @param string $strTipo
*/
public function setTipo($strTipo)
{
        $this->tipo = $strTipo;
}


/**
* Get jsonCatalogo
*
* @return text
*/		
     		
public function getJsonCatalogo()
{
	return $this->jsonCatalogo; 
}

/**
* Set jsonCatalogo
*
* @param text $strJsonCatalogo
*/
public function setJsonCatalogo($strJsonCatalogo)
{
        $this->jsonCatalogo = $strJsonCatalogo;
}


/**
* Get hashCatalogo
*
* @return string
*/		
     		
public function getHashCatalogo()
{
	return $this->hashCatalogo; 
}

/**
* Set hashCatalogo
*
* @param string $strHashCatalogo
*/
public function setHashCatalogo($strHashCatalogo)
{
        $this->hashCatalogo = $strHashCatalogo;
}




/**
* Get feUltMod
*
* @return datetime
*/		
     		
public function getFeUltMod()
{
	return $this->feUltMod; 
}

/**
* Set feUltMod
*
* @param string $strFeUltMod
*/
public function setFeUltMod($strFeUltMod)
{
        $this->feUltMod = $strFeUltMod;
}

}

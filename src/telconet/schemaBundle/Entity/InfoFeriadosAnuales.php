<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoFeriadosAnuales
 *
 * @ORM\Table(name="DB_GENERAL.INFO_FERIADOS_ANUALES")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoFeriadosAnualesRepository")
 */
class InfoFeriadosAnuales
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_FERIADOS_ANUALES", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_FERIADOS_ANUALES", allocationSize=1, initialValue=1)
*/		
		
private $id;
	
/**
* @var integer $feriadosId
*
* @ORM\Column(name="FERIADOS_ID", type="integer", nullable=false)
*/		
     		
private $feriadosId;

/**
* @var string $tipo
*
* @ORM\Column(name="TIPO", type="string", nullable=false)
*/		
     		
private $tipo;


/**
* @var string $feDesde
*
* @ORM\Column(name="FE_DESDE", type="datetime", nullable=true)
*/		
     		
private $feDesde;

/**
* @var string $feHasta
*
* @ORM\Column(name="FE_HASTA", type="datetime", nullable=true)
*/		
     		
private $feHasta;

/**
* @var string $canton_id
*
* @ORM\Column(name="CANTON_ID", type="integer", nullable=true)
*/		
     		
private $cantonId;

/**
* @var string $comentario
*
* @ORM\Column(name="COMENTARIO", type="string", nullable=true)
*/		
     		
private $comentario;


/**
* @var string $usrCreacion
*
* @ORM\Column(name="USR_CREACION", type="string", nullable=false)
*/		
     		
private $usrCreacion;

/**
* @var datetime $feCreacion
*
* @ORM\Column(name="FE_CREACION", type="datetime", nullable=false)
*/		
     		
private $feCreacion;

/**
* @var string $usrUltMod
*
* @ORM\Column(name="USR_ULT_MOD", type="string", nullable=false)
*/		
     		
private $usrUltMod;

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
* Get feriadosId
*
* @return feriadosId
*/		
     		
public function getFeriadosId()
{
    return $this->feriadosId; 
}

/**
* Set feriadosId
*
* @param integer $intFeriadosId
*/
public function setFeriadosId($intFeriadosId)
{
    $this->intFeriadosId = $intFeriadosId;
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
* Get feDesde
*
* @return datetime
*/		
     		
public function getFeDesde()
{
    return $this->feDesde; 
}

/**
* Set feDesde
*
* @param datetime $objFeDesde
*/
public function setFeDesde($objFeDesde)
{
    $this->feDesde = $objFeDesde;
}


/**
* Get feHasta
*
* @return datetime
*/		
     		
public function getFeHasta()
{
    return $this->feHasta; 
}

/**
* Set feHasta
*
* @param datetime $objFeHasta
*/
public function setFeHasta($objFeHasta)
{
    $this->feHasta = $objFeHasta;
}

/**
* Get comentario
*
* @return string
*/		
     		
public function getComentario()
{
    return $this->comentario; 
}

/**
* Set comentario
*
* @param string $strComentario
*/
public function setComentario($strComentario)
{
    $this->comentario = $strComentario;
}

/**
* Get cantonId
*
* @return integer
*/		
     		
public function getCantonId()
{
    return $this->cantonId; 
}

/**
* Set cantonId
*
* @param integer $intCantonId
*/
public function setCantonId($intCantonId)
{
    $this->cantonId = $intCantonId;
}

/**
* Get usrCreacion
*
* @return string
*/		
     		
public function getUsrCreacion()
{
    return $this->usrCreacion; 
}

/**
* Set usrCreacion
*
* @param string $strUsrCreacion
*/
public function setUsrCreacion($strUsrCreacion)
{
    $this->usrCreacion = $strUsrCreacion;
}


/**
* Get feCreacion
*
* @return datetime
*/		
     		
public function getFeCreacion()
{
    return $this->feCreacion; 
}

/**
* Set feCreacion
*
* @param datetime $objFeCreacion
*/
public function setFeCreacion($objFeCreacion)
{
    $this->feCreacion = $objFeCreacion;
}


/**
* Get usrUltMod
*
* @return string
*/		
     		
public function getUsrUltMod()
{
    return $this->usrUltMod; 
}

/**
* Set usrUltMod
*
* @param string $strUsrUltMod
*/
public function setUsrUltMod($strUsrUltMod)
{
    $this->usrUltMod = $strUsrUltMod;
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
* @param datetime $objFeUltMod
*/
public function setFeUltMod($objFeUltMod)
{
    $this->feUltMod = $objFeUltMod;
}

public function __toString()
{
    return $this->valor;
}

}

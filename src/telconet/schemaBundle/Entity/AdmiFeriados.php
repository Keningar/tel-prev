<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiFeriados
 *
 * @ORM\Table(name="DB_GENERAL.ADMI_FERIADOS")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiFeriadosRepository")
 */
class AdmiFeriados
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_FERIADOS", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_FERIADOS", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var string $descripcion
*
* @ORM\Column(name="DESCRIPCION", type="string", nullable=false)
*/		
     		
private $descripcion;

/**
* @var string $tipo
*
* @ORM\Column(name="TIPO", type="string", nullable=false)
*/		
     		
private $tipo;


/**
* @var string $mes
*
* @ORM\Column(name="MES", type="string", nullable=true)
*/		
     		
private $mes;

/**
* @var string $dia
*
* @ORM\Column(name="DIA", type="string", nullable=true)
*/		
     		
private $dia;

/**
* @var string $comentario
*
* @ORM\Column(name="COMENTARIO", type="string", nullable=true)
*/		
     		
private $comentario;

/**
* @var string $canton_id
*
* @ORM\Column(name="CANTON_ID", type="integer", nullable=true)
*/		
     		
private $cantonId;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=true)
*/		
     		
private $estado;


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
* Get descripcion
*
* @return descripcion
*/		
     		
public function getDescripcion()
{
    return $this->descripcion; 
}

/**
* Set descripcion
*
* @param string $strDescripcion
*/
public function setDescripcion($strDescripcion)
{
    $this->descripcion = $strDescripcion;
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
* Get mes
*
* @return string
*/		
     		
public function getMes()
{
    return $this->mes; 
}

/**
* Set mes
*
* @param string $strMes
*/
public function setMes($strMes)
{
    $this->mes = $strMes;
}


/**
* Get dia
*
* @return string
*/		
     		
public function getDia()
{
    return $this->dia; 
}

/**
* Set dia
*
* @param string $strDia
*/
public function setDia($strDia)
{
    $this->dia = $strDia;
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
* Get estado
*
* @return string
*/		
     		
public function getEstado()
{
    return $this->estado; 
}

/**
* Set estado
*
* @param string $strEstado
*/
public function setEstado($strEstado)
{
    $this->estado = $strEstado;
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

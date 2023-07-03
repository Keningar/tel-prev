<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiHipotesis
 *
 * @ORM\Table(name="ADMI_HIPOTESIS")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiHipotesisRepository")
 */
class AdmiHipotesis
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_HIPOTESIS", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_HIPOTESIS", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var string $nombreHipotesis
*
* @ORM\Column(name="NOMBRE_HIPOTESIS", type="string", nullable=false)
*/		
     		
private $nombreHipotesis;

/**
* @var string $descripcionHipotesis
*
* @ORM\Column(name="DESCRIPCION_HIPOTESIS", type="string", nullable=true)
*/		
     		
private $descripcionHipotesis;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=false)
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
* @var string $empresaCod
*
* @ORM\Column(name="EMPRESA_COD", type="string", nullable=false)
*/
		
private $empresaCod;

/**
* @var integer $tipoCasoId
*
* @ORM\Column(name="TIPO_CASO_ID", type="integer", nullable=false)
*/

private $tipoCasoId;

/**
* @var integer $hipotesisId
*
* @ORM\Column(name="HIPOTESIS_ID", type="integer", nullable=false)
*/

private $hipotesisId;

/**
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Get nombreHipotesis
*
* @return string
*/		
     		
public function getNombreHipotesis(){
	return $this->nombreHipotesis; 
}

/**
* Set nombreHipotesis
*
* @param string $nombreHipotesis
*/
public function setNombreHipotesis($nombreHipotesis)
{
        $this->nombreHipotesis = $nombreHipotesis;
}


/**
* Get descripcionHipotesis
*
* @return string
*/		
     		
public function getDescripcionHipotesis(){
	return $this->descripcionHipotesis; 
}

/**
* Set descripcionHipotesis
*
* @param string $descripcionHipotesis
*/
public function setDescripcionHipotesis($descripcionHipotesis)
{
        $this->descripcionHipotesis = $descripcionHipotesis;
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

////////////////////////////////////////////////////////////////////////////

/**
* Get empresaCod
*
* @return string $empresaCod
*/		
     		
public function getEmpresaCod(){
	return $this->empresaCod; 
}

/**
* Set empresaCod
*
* @param string $empresaCod
*/
public function setEmpresaCod($empresaCod)
{
        $this->empresaCod = $empresaCod;
}

/**
* Get tipoCasoId
*
* @return integer $tipoCasoId
*/

public function getTipoCasoId()
{
	return $this->tipoCasoId;
}

/**
* Set tipoCasoId
*
* @param integer  $tipoCasoId
*/
public function setTipoCasoId($tipoCasoId)
{
    $this->tipoCasoId = $tipoCasoId;
}

/**
* Get hipotesisId
*
* @return integer $hipotesisId
*/
public function getHipotesisId()
{
	return $this->hipotesisId;
}

/**
* Set hipotesisId
*
* @param integer  $hipotesisId
*/
public function setHipotesisId($hipotesisId)
{
    $this->hipotesisId = $hipotesisId;
}

}
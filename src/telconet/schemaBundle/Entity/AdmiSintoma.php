<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiSintoma
 *
 * @ORM\Table(name="ADMI_SINTOMA")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiSintomaRepository")
 */
class AdmiSintoma
{


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
* @var integer $id
*
* @ORM\Column(name="ID_SINTOMA", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_SINTOMA", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var string $nombreSintoma
*
* @ORM\Column(name="NOMBRE_SINTOMA", type="string", nullable=false)
*/		
     		
private $nombreSintoma;

/**
* @var string $descripcionSintoma
*
* @ORM\Column(name="DESCRIPCION_SINTOMA", type="string", nullable=true)
*/		
     		
private $descripcionSintoma;

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


/**
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Get nombreSintoma
*
* @return string
*/		
     		
public function getNombreSintoma(){
	return $this->nombreSintoma; 
}

/**
* Set nombreSintoma
*
* @param string $nombreSintoma
*/
public function setNombreSintoma($nombreSintoma)
{
        $this->nombreSintoma = $nombreSintoma;
}


/**
* Get descripcionSintoma
*
* @return string
*/		
     		
public function getDescripcionSintoma(){
	return $this->descripcionSintoma; 
}

/**
* Set descripcionSintoma
*
* @param string $descripcionSintoma
*/
public function setDescripcionSintoma($descripcionSintoma)
{
        $this->descripcionSintoma = $descripcionSintoma;
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
* @param integer $tipoCasoId
*/
public function setTipoCasoId($tipoCasoId)
{
    $this->tipoCasoId = $tipoCasoId;
}


}
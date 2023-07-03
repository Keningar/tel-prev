<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiTipoDocumentoFinanciero
 *
 * @ORM\Table(name="ADMI_TIPO_DOCUMENTO_FINANCIERO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiTipoDocumentoFinancieroRepository")
 */
class AdmiTipoDocumentoFinanciero
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_TIPO_DOCUMENTO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_TIPO_DOCUMENTO", allocationSize=1, initialValue=1)
*/		
		
private $id;	

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=true)
*/		
     		
private $estado;

/**
* @var string $codigoTipoDocumento
*
* @ORM\Column(name="CODIGO_TIPO_DOCUMENTO", type="string", nullable=true)
*/		
     		
private $codigoTipoDocumento;

/**
* @var string $nombreTipoDocumento
*
* @ORM\Column(name="NOMBRE_TIPO_DOCUMENTO", type="string", nullable=true)
*/		
     		
private $nombreTipoDocumento;

/**
* @var DATE $feCreacion
*
* @ORM\Column(name="FE_CREACION", type="datetime", nullable=true)
*/		
     		
private $feCreacion;

/**
* @var DATE $feUltMod
*
* @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=true)
*/		
     		
private $feUltMod;

/**
* @var string $usrCreacion
*
* @ORM\Column(name="USR_CREACION", type="string", nullable=true)
*/		
     		
private $usrCreacion;

/**
* @var string $usrUltMod
*
* @ORM\Column(name="USR_ULT_MOD", type="string", nullable=true)
*/		
     		
private $usrUltMod;

/**
* @var string $movimiento
*
* @ORM\Column(name="MOVIMIENTO", type="string", nullable=true)
*/		
     		
private $movimiento;

/**
* @var float $sumatoria
*
* @ORM\Column(name="SUMATORIA", type="float", nullable=true)
*/		
     		
private $sumatoria;

/**
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
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
* @return 
*/		
     		
public function getFeCreacion(){
	return $this->feCreacion; 
}

/**
* Set feCreacion
*
* @param  $feCreacion
*/
public function setFeCreacion($feCreacion)
{
        $this->feCreacion = $feCreacion;
}


/**
* Get feUltMod
*
* @return 
*/		
     		
public function getFeUltMod(){
	return $this->feUltMod; 
}

/**
* Set feUltMod
*
* @param  $feUltMod
*/
public function setFeUltMod($feUltMod)
{
        $this->feUltMod = $feUltMod;
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
* Get usrUltMod
*
* @return string
*/		
     		
public function getCodigoTipoDocumento(){
	return $this->codigoTipoDocumento; 
}

/**
* Set codigoTipoDocumento
*
* @param string $codigoTipoDocumento
*/
public function setCodigoTipoDocumento($codigoTipoDocumento)
{
        $this->codigoTipoDocumento = $codigoTipoDocumento;
}

public function getNombreTipoDocumento(){
	return $this->nombreTipoDocumento; 
}

/**
* Set nombreTipoDocumento
*
* @param string $nombreTipoDocumento
*/
public function setNombreTipoDocumento($nombreTipoDocumento)
{
        $this->nombreTipoDocumento = $nombreTipoDocumento;
}


public function getMovimiento(){
	return $this->movimiento; 
}

/**
* Set movimiento
*
* @param string $movimiento
*/
public function setMovimiento($movimiento)
{
        $this->movimiento = $movimiento;
}

public function getSumatoria(){
	return $this->sumatoria; 
}

/**
* Set sumatoria
*
* @param float $sumatoria
*/
public function setSumatoria($sumatoria)
{
        $this->sumatoria = $sumatoria;
}


}

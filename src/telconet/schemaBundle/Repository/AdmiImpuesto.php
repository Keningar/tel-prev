<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiImpuesto
 *
 * @ORM\Table(name="ADMI_IMPUESTO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiImpuestoRepository")
 */
class AdmiImpuesto
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_IMPUESTO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_IMPUESTO", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var string $codigoSri
*
* @ORM\Column(name="CODIGO_SRI", type="string", nullable=false)
*/		
     		
private $codigoSri;

/**
* @var integer $porcentajeImpuesto
*
* @ORM\Column(name="PORCENTAJE_IMPUESTO", type="integer", nullable=false)
*/		
     		
private $porcentajeImpuesto;

/**
* @var string $descripcionImpuesto
*
* @ORM\Column(name="DESCRIPCION_IMPUESTO", type="string", nullable=false)
*/		
     		
private $descripcionImpuesto;

/**
* @var datetime $fechaVigenciaImpuesto
*
* @ORM\Column(name="FECHA_VIGENCIA_IMPUESTO", type="datetime", nullable=false)
*/		
     		
private $fechaVigenciaImpuesto;

/**
* @var string $tipoImpuesto
*
* @ORM\Column(name="TIPO_IMPUESTO", type="string", nullable=false)
*/		
     		
private $tipoImpuesto;

/**
* @var string $ctaContable
*
* @ORM\Column(name="CTA_CONTABLE", type="string", nullable=false)
*/		
     		
private $ctaContable;

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
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Get codigoSri
*
* @return string
*/		
     		
public function getCodigoSri(){
	return $this->codigoSri; 
}

/**
* Set codigoSri
*
* @param string $codigoSri
*/
public function setCodigoSri($codigoSri)
{
        $this->codigoSri = $codigoSri;
}


/**
* Get porcentajeImpuesto
*
* @return integer
*/		
     		
public function getPorcentajeImpuesto(){
	return $this->porcentajeImpuesto; 
}

/**
* Set porcentajeImpuesto
*
* @param integer $porcentajeImpuesto
*/
public function setPorcentajeImpuesto($porcentajeImpuesto)
{
        $this->porcentajeImpuesto = $porcentajeImpuesto;
}


/**
* Get descripcionImpuesto
*
* @return string
*/		
     		
public function getDescripcionImpuesto(){
	return $this->descripcionImpuesto; 
}

/**
* Set descripcionImpuesto
*
* @param string $descripcionImpuesto
*/
public function setDescripcionImpuesto($descripcionImpuesto)
{
        $this->descripcionImpuesto = $descripcionImpuesto;
}


/**
* Get fechaVigenciaImpuesto
*
* @return datetime
*/		
     		
public function getFechaVigenciaImpuesto(){
	return $this->fechaVigenciaImpuesto; 
}

/**
* Set fechaVigenciaImpuesto
*
* @param datetime $fechaVigenciaImpuesto
*/
public function setFechaVigenciaImpuesto($fechaVigenciaImpuesto)
{
        $this->fechaVigenciaImpuesto = $fechaVigenciaImpuesto;
}


/**
* Get tipoImpuesto
*
* @return string
*/		
     		
public function getTipoImpuesto(){
	return $this->tipoImpuesto; 
}

/**
* Set tipoImpuesto
*
* @param string $tipoImpuesto
*/
public function setTipoImpuesto($tipoImpuesto)
{
        $this->tipoImpuesto = $tipoImpuesto;
}


/**
* Get ctaContable
*
* @return string
*/		
     		
public function getCtaContable(){
	return $this->ctaContable; 
}

/**
* Set ctaContable
*
* @param string $ctaContable
*/
public function setCtaContable($ctaContable)
{
        $this->ctaContable = $ctaContable;
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

}

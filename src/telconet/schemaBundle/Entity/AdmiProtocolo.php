<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiProtocolo
 *
 * @ORM\Table(name="ADMI_PROTOCOLO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiProtocoloRepository")
 */
class AdmiProtocolo
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_PROTOCOLO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_PROTOCOLO", allocationSize=1, initialValue=1)
*/		
		
private $id;	

/**
* @var string $nombreProtocolo
*
* @ORM\Column(name="NOMBRE_PROTOCOLO", type="string", nullable=true)
*/		
     		
private $nombreProtocolo;

/**
* @var string $descripcionProtocolo
*
* @ORM\Column(name="DESCRIPCION_PROTOCOLO", type="string", nullable=true)
*/		
     		
private $descripcionProtocolo;

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
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Get nombreProtocolo
*
* @return string
*/		
     		
public function getNombreProtocolo(){
	return $this->nombreProtocolo; 
}

/**
* Set nombreProtocolo
*
* @param string $nombreProtocolo
*/
public function setNombreProtocolo($nombreProtocolo)
{
        $this->nombreProtocolo = $nombreProtocolo;
}


/**
* Get descripcionProtocolo
*
* @return string
*/		
     		
public function getDescripcionProtocolo(){
	return $this->descripcionProtocolo; 
}

/**
* Set descripcionProtocolo
*
* @param string $descripcionProtocolo
*/
public function setDescripcionProtocolo($descripcionProtocolo)
{
        $this->descripcionProtocolo = $descripcionProtocolo;
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

public function __toString()
{
    return $this->nombreProtocolo;
}

}
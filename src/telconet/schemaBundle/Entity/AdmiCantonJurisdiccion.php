<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiCantonJurisdiccion
 *
 * @ORM\Table(name="ADMI_CANTON_JURISDICCION")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiCantonJurisdiccionRepository")
 */
class AdmiCantonJurisdiccion
{


/**
* @var integer $segundoOcteto
*
* @ORM\Column(name="SEGUNDO_OCTETO", type="integer", nullable=true)
*/		
     		
private $segundoOcteto;

/**
* @var string $mailTecnico
*
* @ORM\Column(name="MAIL_TECNICO", type="string", nullable=true)
*/		
     		
private $mailTecnico;

/**
* @var integer $ipReserva
*
* @ORM\Column(name="IP_RESERVA", type="integer", nullable=true)
*/		
     		
private $ipReserva;

/**
* @var string $nombreMst
*
* @ORM\Column(name="NOMBRE_MST", type="string", nullable=true)
*/		
     		
private $nombreMst;

/**
* @var integer $revisionMst
*
* @ORM\Column(name="REVISION_MST", type="integer", nullable=true)
*/		
     		
private $revisionMst;

/**
* @var string $instanceMst
*
* @ORM\Column(name="INSTANCE_MST", type="string", nullable=true)
*/		
     		
private $instanceMst;

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
* @var integer $id
*
* @ORM\Column(name="ID_CANTON_JURISDICCION", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_CANTON_JURISDICCION", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var integer $cantonId
*
* @ORM\Column(name="CANTON_ID", type="integer", nullable=false)
*/
		
private $cantonId;

/**
* @var integer $jurisdiccionId
*
* @ORM\Column(name="JURISDICCION_ID", type="integer", nullable=false)
*/		
     		
private $jurisdiccionId;

/**
* Get segundoOcteto
*
* @return integer
*/		
     		
public function getSegundoOcteto(){
	return $this->segundoOcteto; 
}

/**
* Set segundoOcteto
*
* @param integer $segundoOcteto
*/
public function setSegundoOcteto($segundoOcteto)
{
        $this->segundoOcteto = $segundoOcteto;
}


/**
* Get mailTecnico
*
* @return string
*/		
     		
public function getMailTecnico(){
	return $this->mailTecnico; 
}

/**
* Set mailTecnico
*
* @param string $mailTecnico
*/
public function setMailTecnico($mailTecnico)
{
        $this->mailTecnico = $mailTecnico;
}


/**
* Get ipReserva
*
* @return integer
*/		
     		
public function getIpReserva(){
	return $this->ipReserva; 
}

/**
* Set ipReserva
*
* @param integer $ipReserva
*/
public function setIpReserva($ipReserva)
{
        $this->ipReserva = $ipReserva;
}


/**
* Get nombreMst
*
* @return string
*/		
     		
public function getNombreMst(){
	return $this->nombreMst; 
}

/**
* Set nombreMst
*
* @param string $nombreMst
*/
public function setNombreMst($nombreMst)
{
        $this->nombreMst = $nombreMst;
}


/**
* Get revisionMst
*
* @return integer
*/		
     		
public function getRevisionMst(){
	return $this->revisionMst; 
}

/**
* Set revisionMst
*
* @param integer $revisionMst
*/
public function setRevisionMst($revisionMst)
{
        $this->revisionMst = $revisionMst;
}


/**
* Get instanceMst
*
* @return string
*/		
     		
public function getInstanceMst(){
	return $this->instanceMst; 
}

/**
* Set instanceMst
*
* @param string $instanceMst
*/
public function setInstanceMst($instanceMst)
{
        $this->instanceMst = $instanceMst;
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


/**
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Get cantonId
*
* @return integer
*/		
     		
public function getCantonId(){
	return $this->cantonId; 
}

/**
* Set cantonId
*
* @param integer $cantonId
*/
public function setCantonId($cantonId)
{
        $this->cantonId = $cantonId;
}


/**
* Get jurisdiccionId
*
* @return integer
*/		
     		
public function getJurisdiccionId(){
	return $this->jurisdiccionId; 
}

/**
* Set jurisdiccionId
*
* @param integer $jurisdiccionId
*/
public function setJurisdiccionId($jurisdiccionId)
{
        $this->jurisdiccionId = $jurisdiccionId;
}

public function __toString()
{
    return $this->id;
}

}
<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiCanton
 *
 * @ORM\Table(name="ADMI_CANTON")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiCantonRepository")
 */
class AdmiCanton
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_CANTON", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_CANTON", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var AdmiProvincia
*
* @ORM\ManyToOne(targetEntity="AdmiProvincia")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="PROVINCIA_ID", referencedColumnName="ID_PROVINCIA")
* })
*/
		
private $provinciaId;

/**
* @var string $nombreCanton
*
* @ORM\Column(name="NOMBRE_CANTON", type="string", nullable=false)
*/		
     		
private $nombreCanton;

/**
* @var string $esCapital
*
* @ORM\Column(name="ES_CAPITAL", type="string", nullable=false)
*/		
     		
private $esCapital;

/**
* @var string $esCabecera
*
* @ORM\Column(name="ES_CABECERA", type="string", nullable=false)
*/		
     		
private $esCabecera;

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
* @var string $sigla
*
* @ORM\Column(name="SIGLA", type="string", nullable=false)
*/
     		
private $sigla;

/**
* @var string $region
*
* @ORM\Column(name="REGION", type="string", nullable=true)
*/
     		
private $region;

/**
* @var string $zona
*
* @ORM\Column(name="ZONA", type="string", nullable=false)
*/		
     		
private $zona;

/**
 *
 * @var string $codigoInec
 * 
 * @ORM\Column(name="CODIGO_INEC_CANTON", type="string", nullable=false) 
 */
private $codigoInec;

/**
 *
 * @var string $jurisdiccion
 * 
 * @ORM\Column(name="JURISDICCION", type="string", nullable=false) 
 */
private $jurisdiccion;

/**
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Get provinciaId
*
* @return telconet\schemaBundle\Entity\AdmiProvincia
*/		
     		
public function getProvinciaId(){
	return $this->provinciaId; 
}

/**
* Set provinciaId
*
* @param telconet\schemaBundle\Entity\AdmiProvincia $provinciaId
*/
public function setProvinciaId(\telconet\schemaBundle\Entity\AdmiProvincia $provinciaId)
{
        $this->provinciaId = $provinciaId;
}

/**
* Get jurisdiccion
*
* @return string
*/		
     		
public function getJurisdiccion(){
	return $this->jurisdiccion; 
}

/**
* Set jurisdiccion
*
* @param string $jurisdiccion
*/
public function setJurisdiccion($jurisdiccion)
{
        $this->jurisdiccion = $jurisdiccion;
}

/**
* Get nombreCanton
*
* @return string
*/		
     		
public function getNombreCanton(){
	return $this->nombreCanton; 
}

/**
* Set nombreCanton
*
* @param string $nombreCanton
*/
public function setNombreCanton($nombreCanton)
{
        $this->nombreCanton = $nombreCanton;
}

/**
* Get esCapital
*
* @return string
*/		
     		
public function getEsCapital(){
	return $this->esCapital; 
}

/**
* Set esCapital
*
* @param string $esCapital
*/
public function setEsCapital($esCapital)
{
        $this->esCapital = $esCapital;
}

/**
* Get esCabecera
*
* @return string
*/		
     		
public function getEsCabecera(){
	return $this->esCabecera; 
}

/**
* Set esCabecera
*
* @param string $esCabecera
*/
public function setEsCabecera($esCabecera)
{
        $this->esCabecera = $esCabecera;
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
* Get sigla
*
* @return string
*/		
     		
public function getSigla(){
	return $this->sigla; 
}

/**
* Set sigla
*
* @param string $sigla
*/
public function setSigla($sigla)
{
        $this->sigla = $sigla;
}

/**
* Get region
*
* @return string
*/		
     		
public function getRegion(){
	return $this->region; 
}

/**
* Set region
*
* @param string $region
*/
public function setRegion($region)
{
        $this->region = $region;
}

public function __toString()
{
        return $this->nombreCanton;
}
/**
* Get zona
*
* @return string
*/		
     		
public function getZona(){
	return $this->zona; 
}

/**
* Set zona
*
* @param string $zona
*/
public function setZona($zona)
{
        $this->zona = $zona;
}

/**
 * Get codigoInec
 * 
 * @return string
 */
function getCodigoInec()
{
    return $this->codigoInec;
}

/**
 * Set codigoInec
 * 
 * @param string $codigoInec
 */
function setCodigoInec($codigoInec)
{
    $this->codigoInec = $codigoInec;
}


}
<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoTramo
 *
 * @ORM\Table(name="INFO_TRAMO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoTramoRepository")
 */
class InfoTramo
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_TRAMO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_TRAMO", allocationSize=1, initialValue=1)
*/		
		
private $id;

/**
* @var string $nombreTramo
*
* @ORM\Column(name="NOMBRE_TRAMO", type="string", nullable=true)
*/		
     		
private $nombreTramo;

/**
* @var integer $elementoAId
*
* @ORM\Column(name="ELEMENTO_A_ID", type="integer", nullable=false)
*/		
     		
private $elementoAId;

/**
* @var integer $elementoBId
*
* @ORM\Column(name="ELEMENTO_B_ID", type="integer", nullable=false)
*/		
     		
private $elementoBId;

/**
* @var AdmiTipoMedio
*
* @ORM\ManyToOne(targetEntity="AdmiTipoMedio")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="TIPO_MEDIO_ID", referencedColumnName="ID_TIPO_MEDIO")
* })
*/
		
private $tipoMedioId;

/**
* @var integer $marcaA
*
* @ORM\Column(name="MARCA_A", type="integer", nullable=true)
*/		
     		
private $marcaA;

/**
* @var integer $marcaB
*
* @ORM\Column(name="MARCA_B", type="integer", nullable=true)
*/		
     		
private $marcaB;

/**
* @var integer $distancia
*
* @ORM\Column(name="DISTANCIA", type="integer", nullable=true)
*/		
     		
private $distancia;

/**
* @var string $usrCreacion
*
* @ORM\Column(name="USR_CREACION", type="string", nullable=true)
*/		
     		
private $usrCreacion;

/**
* @var datetime $feCreacion
*
* @ORM\Column(name="FE_CREACION", type="datetime", nullable=false)
*/		
     		
private $feCreacion;

/**
* @var string $ipCreacion
*
* @ORM\Column(name="IP_CREACION", type="string", nullable=false)
*/		
     		
private $ipCreacion;

/**
* @var string $usrModifica
*
* @ORM\Column(name="USR_MODIFICA", type="string", nullable=true)
*/		
     		
private $usrModifica;

/**
* @var datetime $feModifica
*
* @ORM\Column(name="FE_MODIFICA", type="datetime", nullable=true)
*/		
     		
private $feModifica;

/**
* @var string $ipModifica
*
* @ORM\Column(name="IP_MODIFICA", type="string", nullable=true)
*/		
     		
private $ipModifica;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=false)
*/		
     		
private $estado;

/**
* @var string $tipoTramo
*
* @ORM\Column(name="TIPO_TRAMO", type="string", nullable=false)
*/		
     		
private $tipoTramo;

/**
* @var string $rutaId
*
* @ORM\Column(name="RUTA_ID", type="integer", nullable=false)
*/		
     		
private $rutaId;

/**
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Get elementoAId
*
* @return integer
*/		
     		
public function getElementoAId(){
	return $this->elementoAId; 
}

/**
* Set elementoAId
*
* @param integer $elementoAId
*/
public function setElementoAId($elementoAId)
{
        $this->elementoAId = $elementoAId;
}


/**
* Get elementoBId
*
* @return integer
*/		
     		
public function getElementoBId(){
	return $this->elementoBId; 
}

/**
* Set elementoBId
*
* @param integer $elementoBId
*/
public function setElementoBId($elementoBId)
{
        $this->elementoBId = $elementoBId;
}


/**
* Get tipoMedioId
*
* @return telconet\schemaBundle\Entity\AdmiTipoMedio
*/		
     		
public function getTipoMedioId(){
	return $this->tipoMedioId; 
}

/**
* Set tipoMedioId
*
* @param telconet\schemaBundle\Entity\AdmiTipoMedio $tipoMedioId
*/
public function setTipoMedioId(\telconet\schemaBundle\Entity\AdmiTipoMedio $tipoMedioId)
{
        $this->tipoMedioId = $tipoMedioId;
}


/**
* Get marcaA
*
* @return integer
*/		
     		
public function getMarcaA(){
	return $this->marcaA; 
}

/**
* Set marcaA
*
* @param integer $marcaA
*/
public function setMarcaA($marcaA)
{
        $this->marcaA = $marcaA;
}


/**
* Get marcaB
*
* @return integer
*/		
     		
public function getMarcaB(){
	return $this->marcaB; 
}

/**
* Set marcaB
*
* @param integer $marcaB
*/
public function setMarcaB($marcaB)
{
        $this->marcaB = $marcaB;
}


/**
* Get distancia
*
* @return integer
*/		
     		
public function getDistancia(){
	return $this->distancia; 
}

/**
* Set distancia
*
* @param integer $distancia
*/
public function setDistancia($distancia)
{
        $this->distancia = $distancia;
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
* Get ipCreacion
*
* @return string
*/		
     		
public function getIpCreacion(){
	return $this->ipCreacion; 
}

/**
* Set ipCreacion
*
* @param string $ipCreacion
*/
public function setIpCreacion($ipCreacion)
{
        $this->ipCreacion = $ipCreacion;
}

/**
* Get usrModifica
*
* @return string
*/		
     		
public function getUsrModifica()
{
	return $this->usrModifica; 
}

/**
* Set usrModifica
*
* @param string $usrModifica
*/
public function setUsrModifica($usrModifica)
{
    $this->usrModifica = $usrModifica;
}


/**
* Get feModifica
*
* @return datetime
*/		
     		
public function getFeModifica()
{
	return $this->feModifica; 
}

/**
* Set feModifica
*
* @param datetime $feModifica
*/
public function setFeModifica($feModifica)
{
    $this->feModifica = $feModifica;
}


/**
* Get ipModifica
*
* @return string
*/		
     		
public function getIpModifica()
{
	return $this->ipModifica; 
}

/**
* Set ipModifica
*
* @param string $ipModifica
*/
public function setIpModifica($ipModifica)
{
    $this->ipModifica = $ipModifica;
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
* @param string $estado
*/
public function setEstado($estado)
{
    $this->estado = $estado;
}

/**
* Get tipoTramo
*
* @return string
*/		

public function getTipoTramo()
{
	return $this->tipoTramo; 
}

/**
* Set tipoTramo
*
* @param string $tipoTramo
*/
public function setTipoTramo($tipoTramo)
{
    $this->tipoTramo = $tipoTramo;
}

/**
* Get rutaId
*
* @return string
*/		

public function getRutaId()
{
	return $this->rutaId; 
}

/**
* Set rutaId
*
* @param string $rutaId
*/
public function setRutaId($rutaId)
{
    $this->rutaId = $rutaId;
}

/**
* Get nombreTramo
*
* @return string
*/		

public function getNombreTramo()
{
	return $this->nombreTramo; 
}

/**
* Set nombreTramo
*
* @param string $nombreTramo
*/
public function setNombreTramo($nombreTramo)
{
    $this->nombreTramo = $nombreTramo;
}


}
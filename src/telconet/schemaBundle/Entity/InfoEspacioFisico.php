<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoEspacioFisico
 *
 * @ORM\Table(name="INFO_ESPACIO_FISICO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoEspacioFisicoRepository")
 */
class InfoEspacioFisico
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_ESPACIO_FISICO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_ESPACIO_FISICO", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var integer $tipoEspacioFisicoId
*
* @ORM\Column(name="TIPO_ESPACIO_FISICO_ID", type="integer", nullable=false)
*/		
     		
private $tipoEspacioFisicoId;

/**
* @var integer $nodoId
*
* @ORM\Column(name="NODO_ID", type="integer", nullable=false)
*/		
     		
private $nodoId;

/**
* @var float $largo
*
* @ORM\Column(name="LARGO", type="float", nullable=false)
*/		
     		
private $largo;

/**
* @var float $ancho
*
* @ORM\Column(name="ANCHO", type="float", nullable=false)
*/		
     		
private $ancho;

/**
* @var float $alto
*
* @ORM\Column(name="ALTO", type="float", nullable=true)
*/		
     		
private $alto;

/**
* @var float $valor
*
* @ORM\Column(name="VALOR", type="float", nullable=false)
*/		
     		
private $valor;

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
* @var string $ipCreacion
*
* @ORM\Column(name="IP_CREACION", type="string", nullable=false)
*/		
     		
private $ipCreacion;

/**
 * @var string $estado
 *
 * @ORM\Column(name="ESTADO", type="string", nullable=true)
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
* Get tipoEspacioFisicoId
*
* @return integer
*/		
     		
public function getTipoEspacioFisicoId(){
	return $this->tipoEspacioFisicoId; 
}

/**
* Set tipoEspacioFisicoId
*
* @param integer $tipoEspacioFisicoId
*/
public function setTipoEspacioFisicoId($tipoEspacioFisicoId)
{
        $this->tipoEspacioFisicoId = $tipoEspacioFisicoId;
}


/**
* Get nodoId
*
* @return integer
*/		
     		
public function getNodoId(){
	return $this->nodoId; 
}

/**
* Set nodoId
*
* @param integer $nodoId
*/
public function setNodoId($nodoId)
{
        $this->nodoId = $nodoId;
}


/**
* Get largo
*
* @return 
*/		
     		
public function getLargo(){
	return $this->largo; 
}

/**
* Set largo
*
* @param  $largo
*/
public function setLargo($largo)
{
        $this->largo = $largo;
}


/**
* Get ancho
*
* @return 
*/		
     		
public function getAncho(){
	return $this->ancho; 
}

/**
* Set ancho
*
* @param  $ancho
*/
public function setAncho($ancho)
{
        $this->ancho = $ancho;
}


/**
* Get alto
*
* @return 
*/		
     		
public function getAlto(){
	return $this->alto; 
}

/**
* Set alto
*
* @param  $alto
*/
public function setAlto($alto)
{
        $this->alto = $alto;
}


/**
* Get valor
*
* @return 
*/		
     		
public function getValor(){
	return $this->valor; 
}

/**
* Set valor
*
* @param  $valor
*/
public function setValor($valor)
{
        $this->valor = $valor;
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


}
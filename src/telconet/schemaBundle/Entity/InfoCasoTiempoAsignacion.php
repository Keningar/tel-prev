<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoCasoTiempoAsignacion
 *
 * @ORM\Table(name="INFO_CASO_TIEMPO_ASIGNACION")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoCasoTiempoAsignacionRepository")
 */
class InfoCasoTiempoAsignacion
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_CASO_TIEMPO_ASIGNACION", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_CASO_TIEMPO_ASIG", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var integer $casoId
*
* @ORM\Column(name="CASO_ID", type="integer", nullable=false)
*/		
     		
private $casoId;

/**
* @var string $tiempoTotalCaso
*
* @ORM\Column(name="TIEMPO_TOTAL_CASO", type="integer", nullable=false)
*/		
     		
private $tiempoTotalCaso;

/**
* @var string $tiempoTotalCasoSolucion
*
* @ORM\Column(name="TIEMPO_TOTAL_CASO_SOLUCION", type="integer", nullable=false)
*/		
     		
private $tiempoTotalCasoSolucion;

/**
* @var string $tiempoClienteAsignado
*
* @ORM\Column(name="TIEMPO_CLIENTE_ASIGNADO", type="integer", nullable=false)
*/		
     		
private $tiempoClienteAsignado;

/**
* @var string $tiempoEmpresaAsignado
*
* @ORM\Column(name="TIEMPO_EMPRESA_ASIGNADO", type="integer", nullable=false)
*/		
     		
private $tiempoEmpresaAsignado;

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
* @var integer $tiempoTotal
*
* @ORM\Column(name="TIEMPO_TOTAL", type="integer", nullable=false)
*/

private $tiempoTotal;

/**
* Get tiempoTotal
*
* @return integer
*/

public function getTiempoTotal()
{
    return $this->tiempoTotal;
}

/**
* Set tiempoTotal
*
* @param integer $intTiempoTotal
*/
public function setTiempoTotal($intTiempoTotal)
{
    $this->tiempoTotal = $intTiempoTotal;
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
* Get casoId
*
* @return integer
*/		
     		
public function getCasoId(){
	return $this->casoId; 
}

/**
* Set casoId
*
* @param integer $casoId
*/
public function setCasoId($casoId)
{
        $this->casoId = $casoId;
}

/**
* Get tiempoTotalCaso
*
* @return integer
*/		
     		
public function getTiempoTotalCaso(){
	return $this->tiempoTotalCaso; 
}

/**
* Set tiempoTotalCaso
*
* @param integer $tiempoTotalCaso
*/
public function setTiempoTotalCaso($tiempoTotalCaso)
{
        $this->tiempoTotalCaso = $tiempoTotalCaso;
}



/**
* Get tiempoTotalCasoSolucion
*
* @return integer
*/		
     		
public function getTiempoTotalCasoSolucion(){
	return $this->tiempoTotalCasoSolucion; 
}

/**
* Set tiempoTotalCasoSolucion
*
* @param integer $tiempoTotalCasoSolucion
*/
public function setTiempoTotalCasoSolucion($tiempoTotalCasoSolucion)
{
        $this->tiempoTotalCasoSolucion = $tiempoTotalCasoSolucion;
}



/**
* Get tiempoClienteAsignado
*
* @return integer
*/		
     		
public function getTiempoClienteAsignado(){
	return $this->tiempoClienteAsignado; 
}

/**
* Set tiempoClienteAsignado
*
* @param integer $tiempoClienteAsignado
*/
public function setTiempoClienteAsignado($tiempoClienteAsignado)
{
        $this->tiempoClienteAsignado = $tiempoClienteAsignado;
}



/**
* Get tiempoEmpresaAsignado
*
* @return integer
*/		
     		
public function getTiempoEmpresaAsignado(){
	return $this->tiempoEmpresaAsignado; 
}

/**
* Set tiempoEmpresaAsignado
*
* @param integer $tiempoEmpresaAsignado
*/
public function setTiempoEmpresaAsignado($tiempoEmpresaAsignado)
{
        $this->tiempoEmpresaAsignado = $tiempoEmpresaAsignado;
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


}

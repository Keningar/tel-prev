<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoMedidor
 *
 * @ORM\Table(name="INFO_MEDIDOR")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoMedidorRepository")
 */
class InfoMedidor
{


/**
* @var string $ipCreacion
*
* @ORM\Column(name="IP_CREACION", type="string", nullable=false)
*/		
     		
private $ipCreacion;

/**
* @var integer $id
*
* @ORM\Column(name="ID_MEDIDOR", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_MEDIDOR", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var integer $nodoId
*
* @ORM\Column(name="NODO_ID", type="integer", nullable=false)
*/		
     		
private $nodoId;

/**
* @var AdmiTipoMedidor
*
* @ORM\ManyToOne(targetEntity="AdmiTipoMedidor")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="TIPO_MEDIDOR_ID", referencedColumnName="ID_TIPO_MEDIDOR")
* })
*/
		
private $tipoMedidorId;

/**
* @var AdmiClaseMedidor
*
* @ORM\ManyToOne(targetEntity="AdmiClaseMedidor")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="CLASE_MEDIDOR_ID", referencedColumnName="ID_CLASE_MEDIDOR")
* })
*/
		
private $claseMedidorId;

/**
* @var string $numeroMedidor
*
* @ORM\Column(name="NUMERO_MEDIDOR", type="string", nullable=false)
*/		
     		
private $numeroMedidor;

/**
* @var float $valorGarantia
*
* @ORM\Column(name="VALOR_GARANTIA", type="float", nullable=true)
*/		
     		
private $valorGarantia;

/**
* @var AdmiProveedorMedidor
*
* @ORM\ManyToOne(targetEntity="AdmiProveedorMedidor")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="PROVEEDOR_MEDIDOR_ID", referencedColumnName="ID_PROVEEDOR_MEDIDOR")
* })
*/
		
private $proveedorMedidorId;

/**
* @var string $proveedorMedidorNombre
*
* @ORM\Column(name="PROVEEDOR_MEDIDOR_NOMBRE", type="string", nullable=true)
*/		
     		
private $proveedorMedidorNombre;

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
* @var string $medidorElectrico
*
* @ORM\Column(name="MEDIDOR_ELECTRICO", type="string", nullable=false)
*/		
     		
private $medidorElectrico;

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
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
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
* Get tipoMedidorId
*
* @return telconet\schemaBundle\Entity\AdmiTipoMedidor
*/		
     		
public function getTipoMedidorId(){
	return $this->tipoMedidorId; 
}

/**
* Set tipoMedidorId
*
* @param telconet\schemaBundle\Entity\AdmiTipoMedidor $tipoMedidorId
*/
public function setTipoMedidorId(\telconet\schemaBundle\Entity\AdmiTipoMedidor $tipoMedidorId)
{
        $this->tipoMedidorId = $tipoMedidorId;
}


/**
* Get claseMedidorId
*
* @return telconet\schemaBundle\Entity\AdmiClaseMedidor
*/		
     		
public function getClaseMedidorId(){
	return $this->claseMedidorId; 
}

/**
* Set claseMedidorId
*
* @param telconet\schemaBundle\Entity\AdmiClaseMedidor $claseMedidorId
*/
public function setClaseMedidorId(\telconet\schemaBundle\Entity\AdmiClaseMedidor $claseMedidorId)
{
        $this->claseMedidorId = $claseMedidorId;
}


/**
* Get numeroMedidor
*
* @return string
*/		
     		
public function getNumeroMedidor(){
	return $this->numeroMedidor; 
}

/**
* Set numeroMedidor
*
* @param string $numeroMedidor
*/
public function setNumeroMedidor($numeroMedidor)
{
        $this->numeroMedidor = $numeroMedidor;
}


/**
* Get valorGarantia
*
* @return 
*/		
     		
public function getValorGarantia(){
	return $this->valorGarantia; 
}

/**
* Set valorGarantia
*
* @param  $valorGarantia
*/
public function setValorGarantia($valorGarantia)
{
        $this->valorGarantia = $valorGarantia;
}


/**
* Get proveedorMedidorId
*
* @return telconet\schemaBundle\Entity\AdmiProveedorMedidor
*/		
     		
public function getProveedorMedidorId(){
	return $this->proveedorMedidorId; 
}

/**
* Set proveedorMedidorId
*
* @param telconet\schemaBundle\Entity\AdmiProveedorMedidor $proveedorMedidorId
*/
public function setProveedorMedidorId(\telconet\schemaBundle\Entity\AdmiProveedorMedidor $proveedorMedidorId)
{
        $this->proveedorMedidorId = $proveedorMedidorId;
}


/**
* Get proveedorMedidorNombre
*
* @return string
*/		
     		
public function getProveedorMedidorNombre(){
	return $this->proveedorMedidorNombre; 
}

/**
* Set proveedorMedidorNombre
*
* @param string $proveedorMedidorNombre
*/
public function setProveedorMedidorNombre($proveedorMedidorNombre)
{
        $this->proveedorMedidorNombre = $proveedorMedidorNombre;
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
* Get medidorElectrico
*
* @return string
*/		
     		
public function getMedidorElectrico(){
	return $this->medidorElectrico; 
}

/**
* Set medidorElectrico
*
* @param string $medidorElectrico
*/
public function setMedidorElectrico($medidorElectrico)
{
        $this->medidorElectrico = $medidorElectrico;
}

}
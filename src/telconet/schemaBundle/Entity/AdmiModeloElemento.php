<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiModeloElemento
 *
 * @ORM\Table(name="ADMI_MODELO_ELEMENTO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiModeloElementoRepository")
 */
class AdmiModeloElemento
{


/**
* @var string $usrUltMod
*
* @ORM\Column(name="USR_ULT_MOD", type="string", nullable=false)
*/		
     		
private $usrUltMod;

/**
* @var integer $id
*
* @ORM\Column(name="ID_MODELO_ELEMENTO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_MODELO_ELEMENTO", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var AdmiMarcaElemento
*
* @ORM\ManyToOne(targetEntity="AdmiMarcaElemento")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="MARCA_ELEMENTO_ID", referencedColumnName="ID_MARCA_ELEMENTO")
* })
*/
		
private $marcaElementoId;

/**
* @var datetime $feUltMod
*
* @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=false)
*/		
     		
private $feUltMod;

/**
* @var decimal $pesoModelo
*
* @ORM\Column(name="PESO_MODELO", type="decimal", nullable=true)
*/		
     		
private $pesoModelo;

/**
* @var AdmiTipoElemento
*
* @ORM\ManyToOne(targetEntity="AdmiTipoElemento")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="TIPO_ELEMENTO_ID", referencedColumnName="ID_TIPO_ELEMENTO")
* })
*/
		
private $tipoElementoId;

/**
* @var string $nombreModeloElemento
*
* @ORM\Column(name="NOMBRE_MODELO_ELEMENTO", type="string", nullable=false)
*/		
     		
private $nombreModeloElemento;

/**
* @var string $unidadMedidaPeso
*
* @ORM\Column(name="UNIDAD_MEDIDA_PESO", type="string", nullable=true)
*/		
     		
private $unidadMedidaPeso;

/**
* @var string $descripcionModeloElemento
*
* @ORM\Column(name="DESCRIPCION_MODELO_ELEMENTO", type="string", nullable=true)
*/		
     		
private $descripcionModeloElemento;

/**
* @var decimal $mttr
*
* @ORM\Column(name="MTTR", type="decimal", nullable=true)
*/		
     		
private $mttr;

/**
* @var string $unidadMedidaMttr
*
* @ORM\Column(name="UNIDAD_MEDIDA_MTTR", type="string", nullable=true)
*/		
     		
private $unidadMedidaMttr;

/**
* @var decimal $mtbf
*
* @ORM\Column(name="MTBF", type="decimal", nullable=true)
*/		
     		
private $mtbf;

/**
* @var string $unidadMedidaMtbf
*
* @ORM\Column(name="UNIDAD_MEDIDA_MTBF", type="string", nullable=true)
*/		
     		
private $unidadMedidaMtbf;

/**
* @var decimal $anchoModelo
*
* @ORM\Column(name="ANCHO_MODELO", type="decimal", nullable=true)
*/		
     		
private $anchoModelo;

/**
* @var string $unidadMedidaAncho
*
* @ORM\Column(name="UNIDAD_MEDIDA_ANCHO", type="string", nullable=true)
*/		
     		
private $unidadMedidaAncho;

/**
* @var decimal $largoModelo
*
* @ORM\Column(name="LARGO_MODELO", type="decimal", nullable=true)
*/		
     		
private $largoModelo;

/**
* @var string $unidadMedidaLargo
*
* @ORM\Column(name="UNIDAD_MEDIDA_LARGO", type="string", nullable=true)
*/		
     		
private $unidadMedidaLargo;

/**
* @var decimal $altoModelo
*
* @ORM\Column(name="ALTO_MODELO", type="decimal", nullable=true)
*/		
     		
private $altoModelo;

/**
* @var string $unidadMedidaAlto
*
* @ORM\Column(name="UNIDAD_MEDIDA_ALTO", type="string", nullable=true)
*/		
     		
private $unidadMedidaAlto;

/**
* @var decimal $uRack
*
* @ORM\Column(name="U_RACK", type="decimal", nullable=true)
*/		
     		
private $uRack;

/**
* @var integer $capacidadEntrada
*
* @ORM\Column(name="CAPACIDAD_ENTRADA", type="integer", nullable=true)
*/		
     		
private $capacidadEntrada;

/**
* @var string $unidadMedidaEntrada
*
* @ORM\Column(name="UNIDAD_MEDIDA_ENTRADA", type="string", nullable=true)
*/		
     		
private $unidadMedidaEntrada;

/**
* @var integer $capacidadSalida
*
* @ORM\Column(name="CAPACIDAD_SALIDA", type="integer", nullable=true)
*/		
     		
private $capacidadSalida;

/**
* @var string $unidadMedidaSalida
*
* @ORM\Column(name="UNIDAD_MEDIDA_SALIDA", type="string", nullable=true)
*/		
     		
private $unidadMedidaSalida;

/**
* @var decimal $capacidadVaFabrica
*
* @ORM\Column(name="CAPACIDAD_VA_FABRICA", type="decimal", nullable=true)
*/		
     		
private $capacidadVaFabrica;

/**
* @var string $unidadVaFabrica
*
* @ORM\Column(name="UNIDAD_VA_FABRICA", type="string", nullable=true)
*/		
     		
private $unidadVaFabrica;

/**
* @var decimal $capacidadVaPromedio
*
* @ORM\Column(name="CAPACIDAD_VA_PROMEDIO", type="decimal", nullable=true)
*/		
     		
private $capacidadVaPromedio;

/**
* @var string $unidadVaPromedio
*
* @ORM\Column(name="UNIDAD_VA_PROMEDIO", type="string", nullable=true)
*/		
     		
private $unidadVaPromedio;

/**
* @var decimal $precioPromedio
*
* @ORM\Column(name="PRECIO_PROMEDIO", type="decimal", nullable=true)
*/		
     		
private $precioPromedio;

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
* Get usrUltMod
*
* @return string
*/


/**
* @var string $reqAprovisionamiento
*
* @ORM\Column(name="REQ_APROVISIONAMIENTO", type="string", nullable=false)
*/		
     		
private $reqAprovisionamiento;

public function getReqAprovisionamiento(){
	return $this->reqAprovisionamiento; 
}

/**
* Set usrUltMod
*
* @param string $usrUltMod
*/
public function setReqAprovisionamiento($reqAprovisionamiento)
{
        $this->reqAprovisionamiento = $reqAprovisionamiento;
}

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
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Get marcaElementoId
*
* @return telconet\schemaBundle\Entity\AdmiMarcaElemento
*/		
     		
public function getMarcaElementoId(){
	return $this->marcaElementoId; 
}

/**
* Set marcaElementoId
*
* @param telconet\schemaBundle\Entity\AdmiMarcaElemento $marcaElementoId
*/
public function setMarcaElementoId(\telconet\schemaBundle\Entity\AdmiMarcaElemento $marcaElementoId)
{
        $this->marcaElementoId = $marcaElementoId;
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
* Get pesoModelo
*
* @return 
*/		
     		
public function getPesoModelo(){
	return $this->pesoModelo; 
}

/**
* Set pesoModelo
*
* @param  $pesoModelo
*/
public function setPesoModelo($pesoModelo)
{
        $this->pesoModelo = $pesoModelo;
}


/**
* Get tipoElementoId
*
* @return telconet\schemaBundle\Entity\AdmiTipoElemento
*/		
     		
public function getTipoElementoId(){
	return $this->tipoElementoId; 
}

/**
* Set tipoElementoId
*
* @param telconet\schemaBundle\Entity\AdmiTipoElemento $tipoElementoId
*/
public function setTipoElementoId(\telconet\schemaBundle\Entity\AdmiTipoElemento $tipoElementoId)
{
        $this->tipoElementoId = $tipoElementoId;
}


/**
* Get nombreModeloElemento
*
* @return string
*/		
     		
public function getNombreModeloElemento(){
	return $this->nombreModeloElemento; 
}

/**
* Set nombreModeloElemento
*
* @param string $nombreModeloElemento
*/
public function setNombreModeloElemento($nombreModeloElemento)
{
        $this->nombreModeloElemento = $nombreModeloElemento;
}


/**
* Get unidadMedidaPeso
*
* @return string
*/		
     		
public function getUnidadMedidaPeso(){
	return $this->unidadMedidaPeso; 
}

/**
* Set unidadMedidaPeso
*
* @param string $unidadMedidaPeso
*/
public function setUnidadMedidaPeso($unidadMedidaPeso)
{
        $this->unidadMedidaPeso = $unidadMedidaPeso;
}


/**
* Get descripcionModeloElemento
*
* @return string
*/		
     		
public function getDescripcionModeloElemento(){
	return $this->descripcionModeloElemento; 
}

/**
* Set descripcionModeloElemento
*
* @param string $descripcionModeloElemento
*/
public function setDescripcionModeloElemento($descripcionModeloElemento)
{
        $this->descripcionModeloElemento = $descripcionModeloElemento;
}


/**
* Get mttr
*
* @return 
*/		
     		
public function getMttr(){
	return $this->mttr; 
}

/**
* Set mttr
*
* @param  $mttr
*/
public function setMttr($mttr)
{
        $this->mttr = $mttr;
}


/**
* Get unidadMedidaMttr
*
* @return string
*/		
     		
public function getUnidadMedidaMttr(){
	return $this->unidadMedidaMttr; 
}

/**
* Set unidadMedidaMttr
*
* @param string $unidadMedidaMttr
*/
public function setUnidadMedidaMttr($unidadMedidaMttr)
{
        $this->unidadMedidaMttr = $unidadMedidaMttr;
}


/**
* Get mtbf
*
* @return 
*/		
     		
public function getMtbf(){
	return $this->mtbf; 
}

/**
* Set mtbf
*
* @param  $mtbf
*/
public function setMtbf($mtbf)
{
        $this->mtbf = $mtbf;
}


/**
* Get unidadMedidaMtbf
*
* @return string
*/		
     		
public function getUnidadMedidaMtbf(){
	return $this->unidadMedidaMtbf; 
}

/**
* Set unidadMedidaMtbf
*
* @param string $unidadMedidaMtbf
*/
public function setUnidadMedidaMtbf($unidadMedidaMtbf)
{
        $this->unidadMedidaMtbf = $unidadMedidaMtbf;
}


/**
* Get anchoModelo
*
* @return 
*/		
     		
public function getAnchoModelo(){
	return $this->anchoModelo; 
}

/**
* Set anchoModelo
*
* @param  $anchoModelo
*/
public function setAnchoModelo($anchoModelo)
{
        $this->anchoModelo = $anchoModelo;
}


/**
* Get unidadMedidaAncho
*
* @return string
*/		
     		
public function getUnidadMedidaAncho(){
	return $this->unidadMedidaAncho; 
}

/**
* Set unidadMedidaAncho
*
* @param string $unidadMedidaAncho
*/
public function setUnidadMedidaAncho($unidadMedidaAncho)
{
        $this->unidadMedidaAncho = $unidadMedidaAncho;
}


/**
* Get largoModelo
*
* @return 
*/		
     		
public function getLargoModelo(){
	return $this->largoModelo; 
}

/**
* Set largoModelo
*
* @param  $largoModelo
*/
public function setLargoModelo($largoModelo)
{
        $this->largoModelo = $largoModelo;
}


/**
* Get unidadMedidaLargo
*
* @return string
*/		
     		
public function getUnidadMedidaLargo(){
	return $this->unidadMedidaLargo; 
}

/**
* Set unidadMedidaLargo
*
* @param string $unidadMedidaLargo
*/
public function setUnidadMedidaLargo($unidadMedidaLargo)
{
        $this->unidadMedidaLargo = $unidadMedidaLargo;
}


/**
* Get altoModelo
*
* @return 
*/		
     		
public function getAltoModelo(){
	return $this->altoModelo; 
}

/**
* Set altoModelo
*
* @param  $altoModelo
*/
public function setAltoModelo($altoModelo)
{
        $this->altoModelo = $altoModelo;
}


/**
* Get unidadMedidaAlto
*
* @return string
*/		
     		
public function getUnidadMedidaAlto(){
	return $this->unidadMedidaAlto; 
}

/**
* Set unidadMedidaAlto
*
* @param string $unidadMedidaAlto
*/
public function setUnidadMedidaAlto($unidadMedidaAlto)
{
        $this->unidadMedidaAlto = $unidadMedidaAlto;
}


/**
* Get uRack
*
* @return 
*/		
     		
public function getURack(){
	return $this->uRack; 
}

/**
* Set uRack
*
* @param  $uRack
*/
public function setURack($uRack)
{
        $this->uRack = $uRack;
}


/**
* Get capacidadEntrada
*
* @return integer
*/		
     		
public function getCapacidadEntrada(){
	return $this->capacidadEntrada; 
}

/**
* Set capacidadEntrada
*
* @param integer $capacidadEntrada
*/
public function setCapacidadEntrada($capacidadEntrada)
{
        $this->capacidadEntrada = $capacidadEntrada;
}


/**
* Get unidadMedidaEntrada
*
* @return string
*/		
     		
public function getUnidadMedidaEntrada(){
	return $this->unidadMedidaEntrada; 
}

/**
* Set unidadMedidaEntrada
*
* @param string $unidadMedidaEntrada
*/
public function setUnidadMedidaEntrada($unidadMedidaEntrada)
{
        $this->unidadMedidaEntrada = $unidadMedidaEntrada;
}


/**
* Get capacidadSalida
*
* @return integer
*/		
     		
public function getCapacidadSalida(){
	return $this->capacidadSalida; 
}

/**
* Set capacidadSalida
*
* @param integer $capacidadSalida
*/
public function setCapacidadSalida($capacidadSalida)
{
        $this->capacidadSalida = $capacidadSalida;
}


/**
* Get unidadMedidaSalida
*
* @return string
*/		
     		
public function getUnidadMedidaSalida(){
	return $this->unidadMedidaSalida; 
}

/**
* Set unidadMedidaSalida
*
* @param string $unidadMedidaSalida
*/
public function setUnidadMedidaSalida($unidadMedidaSalida)
{
        $this->unidadMedidaSalida = $unidadMedidaSalida;
}


/**
* Get capacidadVaFabrica
*
* @return 
*/		
     		
public function getCapacidadVaFabrica(){
	return $this->capacidadVaFabrica; 
}

/**
* Set capacidadVaFabrica
*
* @param  $capacidadVaFabrica
*/
public function setCapacidadVaFabrica($capacidadVaFabrica)
{
        $this->capacidadVaFabrica = $capacidadVaFabrica;
}


/**
* Get unidadVaFabrica
*
* @return string
*/		
     		
public function getUnidadVaFabrica(){
	return $this->unidadVaFabrica; 
}

/**
* Set unidadVaFabrica
*
* @param string $unidadVaFabrica
*/
public function setUnidadVaFabrica($unidadVaFabrica)
{
        $this->unidadVaFabrica = $unidadVaFabrica;
}


/**
* Get capacidadVaPromedio
*
* @return 
*/		
     		
public function getCapacidadVaPromedio(){
	return $this->capacidadVaPromedio; 
}

/**
* Set capacidadVaPromedio
*
* @param  $capacidadVaPromedio
*/
public function setCapacidadVaPromedio($capacidadVaPromedio)
{
        $this->capacidadVaPromedio = $capacidadVaPromedio;
}


/**
* Get unidadVaPromedio
*
* @return string
*/		
     		
public function getUnidadVaPromedio(){
	return $this->unidadVaPromedio; 
}

/**
* Set unidadVaPromedio
*
* @param string $unidadVaPromedio
*/
public function setUnidadVaPromedio($unidadVaPromedio)
{
        $this->unidadVaPromedio = $unidadVaPromedio;
}


/**
* Get precioPromedio
*
* @return 
*/		
     		
public function getPrecioPromedio(){
	return $this->precioPromedio; 
}

/**
* Set precioPromedio
*
* @param  $precioPromedio
*/
public function setPrecioPromedio($precioPromedio)
{
        $this->precioPromedio = $precioPromedio;
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

public function __toString()
{
    return $this->nombreModeloElemento;
}

}
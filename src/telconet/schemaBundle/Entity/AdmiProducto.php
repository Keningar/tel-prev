<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiProducto
 *
 * @ORM\Table(name="ADMI_PRODUCTO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiProductoRepository")
 */
class AdmiProducto
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_PRODUCTO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_PRODUCTO", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var string $codigoProducto
*
* @ORM\Column(name="CODIGO_PRODUCTO", type="string", nullable=false)
*/		
     		
private $codigoProducto;

/**
* @var string $descripcionProducto
*
* @ORM\Column(name="DESCRIPCION_PRODUCTO", type="string", nullable=false)
*/		
     		
private $descripcionProducto;

/**
* @var string $funcionPrecio
*
* @ORM\Column(name="FUNCION_PRECIO", type="string", nullable=true)
*/		
     		
private $funcionPrecio;

/**
* @var string $funcionCosto
*
* @ORM\Column(name="FUNCION_COSTO", type="string", nullable=true)
*/		
     		
private $funcionCosto;

/**
* @var integer $instalacion
*
* @ORM\Column(name="INSTALACION", type="integer", nullable=true)
*/		
     		
private $instalacion;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=true)
*/		
     		
private $estado;

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
* @var string $ipCreacion
*
* @ORM\Column(name="IP_CREACION", type="string", nullable=true)
*/		
     		
private $ipCreacion;


/**
* @var string $requierePlanificacion
*
* @ORM\Column(name="REQUIERE_PLANIFICACION", type="string", nullable=true)
*/		
     		
private $requierePlanificacion;

/**
* @var string $requiereInfoTecnica
*
* @ORM\Column(name="REQUIERE_INFO_TECNICA", type="string", nullable=true)
*/		
     		
private $requiereInfoTecnica;

/**
* @var string $esEnlace
*
* @ORM\Column(name="ES_ENLACE", type="string", nullable=true)
*/		
     		
private $esEnlace;

/**
* @var string $esConcentrador
*
* @ORM\Column(name="ES_CONCENTRADOR", type="string", nullable=true)
*/		
     		
private $esConcentrador;

/**
* @var string $subgrupo
*
* @ORM\Column(name="SUBGRUPO", type="string", nullable=false)
*/		
     		
private $subgrupo='OTROS';

/**
* @var InfoEmpresaGrupo
*
* @ORM\ManyToOne(targetEntity="InfoEmpresaGrupo")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="EMPRESA_COD", referencedColumnName="COD_EMPRESA")
* })
*/
		
private $empresaCod;

/**
* @var string $esPreferencia
*
* @ORM\Column(name="ES_PREFERENCIA", type="string", nullable=false)
*/		
     		
private $esPreferencia;

/**
* @var string $nombreTecnico
*
* @ORM\Column(name="NOMBRE_TECNICO", type="string", nullable=false)
*/		
     		
private $nombreTecnico;

/**
* @var string $tipo
*
* @ORM\Column(name="TIPO", type="string", nullable=true)
*/		
     		
private $tipo;

/**
* @var string $estadoInicial
*
* @ORM\Column(name="ESTADO_INICIAL", type="string", nullable=true)
*/		
     		
private $estadoInicial;

/** @var string $soporteMasivo
*
* @ORM\Column(name="SOPORTE_MASIVO", type="string", nullable=true)
*/		
     		
private $soporteMasivo;

/**
* @var string $grupo
*
* @ORM\Column(name="GRUPO", type="string", nullable=true)
*/		
     		
private $grupo;

/**
* @var float $comisionVenta
*
* @ORM\Column(name="COMISION_VENTA", type="float", nullable=true)
*/		
     		
private $comisionVenta;

/**
* @var float $comisionMantenimiento
*
* @ORM\Column(name="COMISION_MANTENIMIENTO", type="float", nullable=true)
*/		
     		
private $comisionMantenimiento;

/**
* @var string $usrGerente
*
* @ORM\Column(name="USR_GERENTE", type="string", nullable=true)
*/		
     		
private $usrGerente;

/**
* @var string $clasificacion
*
* @ORM\Column(name="CLASIFICACION", type="string", nullable=true)
*/		
     		
private $clasificacion;

/**
* @var string $requiereComisionar
*
* @ORM\Column(name="REQUIERE_COMISIONAR", type="string", nullable=true)
*/
     
private $requiereComisionar;

/**
* @var string $lineaNegocio
*
* @ORM\Column(name="LINEA_NEGOCIO", type="string", nullable=false)
*/
     
private $lineaNegocio='OTROS';

/**
* @var string $frecuencia
*
* @ORM\Column(name="FRECUENCIA", type="string", nullable=true)
*/

private $frecuencia;

/**
* @var string $terminoCondicion
*
* @ORM\Column(name="TERMINO_CONDICION", type="string", nullable=true)
*/
private $terminoCondicion;

/**
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Get codigoProducto
*
* @return string
*/		
     		
public function getCodigoProducto(){
	return $this->codigoProducto; 
}

/**
* Set codigoProducto
*
* @param string $codigoProducto
*/
public function setCodigoProducto($codigoProducto)
{
        $this->codigoProducto = $codigoProducto;
}


/**
* Get descripcionProducto
*
* @return string
*/		
     		
public function getDescripcionProducto(){
	return $this->descripcionProducto; 
}

/**
* Set descripcionProducto
*
* @param string $descripcionProducto
*/
public function setDescripcionProducto($descripcionProducto)
{
        $this->descripcionProducto = $descripcionProducto;
}


/**
* Get funcionPrecio
*
* @return string
*/		
     		
public function getFuncionPrecio(){
	return $this->funcionPrecio; 
}

/**
* Set funcionPrecio
*
* @param string $funcionPrecio
*/
public function setFuncionPrecio($funcionPrecio)
{
        $this->funcionPrecio = $funcionPrecio;
}


/**
* Get funcionCosto
*
* @return string
*/		
     		
public function getFuncionCosto(){
	return $this->funcionCosto; 
}

/**
* Set funcionCosto
*
* @param string $funcionCosto
*/
public function setFuncionCosto($funcionCosto)
{
        $this->funcionCosto = $funcionCosto;
}


/**
* Get instalacion
*
* @return integer
*/		
     		
public function getInstalacion(){
	return $this->instalacion; 
}

/**
* Set instalacion
*
* @param integer $instalacion
*/
public function setInstalacion($instalacion)
{
        $this->instalacion = $instalacion;
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
* Get empresaCod
*
* @return telconet\schemaBundle\Entity\InfoEmpresaGrupo
*/		
     		
public function getEmpresaCod(){
	return $this->empresaCod; 
}

/**
* Set empresaCod
*
* @param telconet\schemaBundle\Entity\InfoEmpresaGrupo $empresaCod
*/
public function setEmpresaCod(\telconet\schemaBundle\Entity\InfoEmpresaGrupo $empresaCod)
{
    $this->empresaCod = $empresaCod;
}

public function __toString()
{
    return $this->descripcionProducto;
}

/**
* Get esPreferencia
*
* @return string
*/		
     		
public function getEsPreferencia(){
	return $this->esPreferencia; 
}

/**
* Set esPreferencia
*
* @param string $esPreferencia
*/
public function setEsPreferencia($esPreferencia)
{
    $this->esPreferencia = $esPreferencia;
}

/**
* Get esEnlace
*
* @return string
*/		
     		
public function getEsEnlace(){
	return $this->esEnlace; 
}

/**
* Set esEnlace
*
* @param string $esEnlace
*/
public function setEsEnlace($esEnlace)
{
    $this->esEnlace = $esEnlace;
}

/**
* Get esConcentrador
*
* @return string
*/		
     		
public function getEsConcentrador(){
	return $this->esConcentrador; 
}

/**
* Set esConcentrador
*
* @param string $esConcentrador
*/
public function setEsConcentrador($esConcentrador)
{
    $this->esConcentrador = $esConcentrador;
}

/**
* Get requierePlanificacion
*
* @return string
*/		
     		
public function getRequierePlanificacion(){
	return $this->requierePlanificacion; 
}

/**
* Set requierePlanificacion
*
* @param string $requierePlanificacion
*/
public function setRequierePlanificacion($requierePlanificacion)
{
    $this->requierePlanificacion = $requierePlanificacion;
}

/**
* Get requiereInfoTecnica
*
* @return string
*/		
     		
public function getRequiereInfoTecnica(){
	return $this->requiereInfoTecnica; 
}

/**
* Set requiereInfoTecnica
*
* @param string $requiereInfoTecnica
*/
public function setRequiereInfoTecnica($requiereInfoTecnica)
{
        $this->requiereInfoTecnica = $requiereInfoTecnica;
}


/**
* Get nombreTecnico
*
* @return string
*/		
     		
public function getNombreTecnico(){
	return $this->nombreTecnico; 
}

/**
* Set nombreTecnico
*
* @param string $nombreTecnico
*/
public function setNombreTecnico($nombreTecnico)
{
        $this->nombreTecnico = $nombreTecnico;
}

/**
* Get tipo
*
* @return string
*/		
     		
public function getTipo(){
	return $this->tipo; 
}

/**
* Set nombreTecnico
*
* @param string $tipo
*/
public function setTipo($tipo)
{
    $this->tipo = $tipo;
}

/**
* Get estadoInicial
*
* @return string
*/		
     		
public function getEstadoInicial(){
	return $this->estadoInicial; 
}

/**
* Set estadoInicial
*
* @param string $estadoInicial
*/
public function setEstadoInicial($estadoInicial)
{
    $this->estadoInicial = $estadoInicial;
}

/**
* Get soporteMasivo
*
* @return string
*/		
     		
public function getSoporteMasivo(){
	return $this->soporteMasivo; 
}

/**
* Set soporteMasivo
*
* @param string $soporteMasivo
*/
public function setSoporteMasivo($soporteMasivo)
{
        $this->soporteMasivo = $soporteMasivo;
}

/**
* Get grupo
*
* @return string
*/		
     		
public function getGrupo(){
	return $this->grupo; 
}

/**
* Set grupo
*
* @param string $grupo
*/
public function setGrupo($grupo)
{
        $this->grupo = $grupo;
}

/**
* Get comisionVenta
*
* @return float
*/		
     		
public function getComisionVenta(){
	return $this->comisionVenta; 
}

/**
* Set comisionVenta
*
* @param float $comisionVenta
*/
public function setComisionVenta($comisionVenta)
{
        $this->comisionVenta = $comisionVenta;
}

/**
* Get comisionMantenimiento
*
* @return float
*/		
     		
public function getComisionMantenimiento(){
	return $this->comisionMantenimiento; 
}

/**
* Set comisionMantenimiento
*
* @param float $comisionMantenimiento
*/
public function setComisionMantenimiento($comisionMantenimiento)
{
        $this->comisionMantenimiento = $comisionMantenimiento;
}

/**
* Get usrGerente
*
* @return string
*/		
     		
public function getUsrGerente(){
	return $this->usrGerente; 
}

/**
* Set usrGerente
*
* @param string $usrGerente
*/
public function setUsrGerente($usrGerente)
{
        $this->usrGerente = $usrGerente;
}

/**
* Get clasificacion
*
* @return string
*/		
     		
public function getClasificacion(){
	return $this->clasificacion; 
}

/**
* Set clasificacion
*
* @param string $clasificacion
*/
public function setClasificacion($clasificacion)
{
        $this->clasificacion = $clasificacion;
}

/**
 * Get requiereComisionar
 *
 * @return string
 */
 public function getRequiereComisionar()
 {
     return $this->requiereComisionar;
 }

 /**
  * Set requiereComisionar
  *
  * @param string $requiereComisionar
  */
  public function setRequiereComisionar($requiereComisionar)
  {
      $this->requiereComisionar = $requiereComisionar;
  }

/**
 * Get subgrupo
 *
 * @return string
 */
 public function getSubgrupo()
 {
     return $this->subgrupo;
 }

 /**
  * Set subgrupo
  *
  * @param string $subgrupo
  */
  public function setSubgrupo($subgrupo)
  {
      $this->subgrupo = $subgrupo;
  }
  
 /**
  * Get lineaNegocio
  *
  * @return string
  */
  public function getLineaNegocio()
  {
      return $this->lineaNegocio;
  }

 /**
  * Set lineaNegocio
  *
  * @param string $lineaNegocio
  */
  public function setLineaNegocio($lineaNegocio)
  {
      $this->lineaNegocio = $lineaNegocio;
  }

 /**
  * Get frecuencia
  *
  * @return string
  */
  public function getFrecuencia()
  {
      return $this->frecuencia;
  }

 /**
  * Set frecuencia
  *
  * @param string $frecuencia
  */
  public function setFrecuencia($frecuencia)
  {
      $this->frecuencia = $frecuencia;
  }

  /**
  * Get terminoCondicion
  *
  * @return string
  */
  public function getTerminoCondicion()
  {
      return $this->terminoCondicion;
  }

 /**
  * Set terminoCondicion
  *
  * @param string $terminoCondicion
  */
  public function setTerminoCondicion($terminoCondicion)
  {
      $this->terminoCondicion = $terminoCondicion;
  }

}

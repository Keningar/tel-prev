<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiTarea
 *
 * @ORM\Table(name="ADMI_TAREA")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiTareaRepository")
 */

class AdmiTarea
{

    
/**
* @var AdmiTarea
*
* @ORM\ManyToOne(targetEntity="AdmiTarea")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="TAREA_ANTERIOR_ID", referencedColumnName="ID_TAREA")
* })
*/		
     		
private $tareaAnteriorId;

/**
* @var AdmiTarea
*
* @ORM\ManyToOne(targetEntity="AdmiTarea")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="TAREA_SIGUIENTE_ID", referencedColumnName="ID_TAREA")
* })
*/		
     		
private $tareaSiguienteId;

/**
* @var string $esAprobada
*
* @ORM\Column(name="ES_APROBADA", type="string", nullable=true)
*/		
     		
private $esAprobada;

/**
* @var string $nombreTarea
*
* @ORM\Column(name="NOMBRE_TAREA", type="string", nullable=false)
*/		
     		
private $nombreTarea;

/**
* @var integer $rolAutorizaId
*
* @ORM\Column(name="ROL_AUTORIZA_ID", type="integer", nullable=true)
*/		
     		
private $rolAutorizaId;

/**
* @var string $descripcionTarea
*
* @ORM\Column(name="DESCRIPCION_TAREA", type="string", nullable=true)
*/		
     		
private $descripcionTarea;

/**
* @var integer $tiempoMax
*
* @ORM\Column(name="TIEMPO_MAX", type="integer", nullable=true)
*/		
     		
private $tiempoMax;

/**
* @var string $unidadMedidaTiempo
*
* @ORM\Column(name="UNIDAD_MEDIDA_TIEMPO", type="string", nullable=true)
*/		
     		
private $unidadMedidaTiempo;

/**
* @var decimal $costo
*
* @ORM\Column(name="COSTO", type="decimal", nullable=true)
*/		
     		
private $costo;

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
* @var decimal $peso
*
* @ORM\Column(name="PESO", type="decimal", nullable=true)
*/		
     		
private $peso;

/**
* @var integer $id
*
* @ORM\Column(name="ID_TAREA", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_TAREA", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var AdmiProceso
*
* @ORM\ManyToOne(targetEntity="AdmiProceso")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="PROCESO_ID", referencedColumnName="ID_PROCESO")
* })
*/
		
private $procesoId;

/**
* @var string $automaticaWs
*
* @ORM\Column(name="AUTOMATICA_WS", type="string", nullable=false)
*/

private $automaticaWs;

/**
* @var integer $categoriaTareaId
*
* @ORM\Column(name="CATEGORIA_TAREA_ID", type="integer", nullable=true)
*/	

private $categoriaTareaId;

/**
* @var string $requiereFibra
*
* @ORM\Column(name="REQUIERE_FIBRA", type="string", nullable=true)
*/

private $requiereFibra;

/**
* @var string $visualizarMovil
*
* @ORM\Column(name="VISUALIZAR_MOVIL", type="string", nullable=true)
*/

private $visualizarMovil;


/**
* Get visualizarMovil
*
* @return telconet\schemaBundle\Entity\AdmiTarea
*/		
  

public function getVisualizarMovil(){
	return $this->visualizarMovil; 
}

/**
* Set tareaAnteriorId
*
* @param telconet\schemaBundle\Entity\AdmiTarea $visualizarMovil
*/
public function setVisualizarMovil(\telconet\schemaBundle\Entity\AdmiTarea $visualizarMovil)
{
        $this->visualizarMovil = $visualizarMovil;
}


/**
* Get requiereFibra
*
* @return telconet\schemaBundle\Entity\AdmiTarea
*/		
  

public function getRequiereFibra(){
	return $this->requiereFibra; 
}

/**
* Set tareaAnteriorId
*
* @param telconet\schemaBundle\Entity\AdmiTarea $requiereFibra
*/
public function setRequiereFibra(\telconet\schemaBundle\Entity\AdmiTarea $requiereFibra)
{
        $this->requiereFibra = $requiereFibra;
}

/**
* Get tareaAnteriorId
*
* @return telconet\schemaBundle\Entity\AdmiTarea
*/		
  

public function getTareaAnteriorId(){
	return $this->tareaAnteriorId; 
}

/**
* Set tareaAnteriorId
*
* @param telconet\schemaBundle\Entity\AdmiTarea $tareaAnteriorId
*/
public function setTareaAnteriorId(\telconet\schemaBundle\Entity\AdmiTarea $tareaAnteriorId)
{
        $this->tareaAnteriorId = $tareaAnteriorId;
}


/**
* Get tareaSiguienteId
*
* @return telconet\schemaBundle\Entity\AdmiTarea
*/		
     		
public function getTareaSiguienteId(){
	return $this->tareaSiguienteId; 
}

/**
* Set tareaSiguienteId
*
* @param telconet\schemaBundle\Entity\AdmiTarea $tareaSiguienteId
*/
public function setTareaSiguienteId(\telconet\schemaBundle\Entity\AdmiTarea $tareaSiguienteId)
{
        $this->tareaSiguienteId = $tareaSiguienteId;
}


/**
* Get esAprobada
*
* @return string
*/		
     		
public function getEsAprobada(){
	return $this->esAprobada; 
}

/**
* Set esAprobada
*
* @param string $esAprobada
*/
public function setEsAprobada($esAprobada)
{
        $this->esAprobada = $esAprobada;
}


/**
* Get nombreTarea
*
* @return string
*/		
     		
public function getNombreTarea(){
	return $this->nombreTarea; 
}

/**
* Set nombreTarea
*
* @param string $nombreTarea
*/
public function setNombreTarea($nombreTarea)
{
        $this->nombreTarea = $nombreTarea;
}


/**
* Get rolAutorizaId
*
* @return integer
*/		
     		
public function getRolAutorizaId(){
	return $this->rolAutorizaId; 
}

/**
* Set rolAutorizaId
*
* @param integer $rolAutorizaId
*/
public function setRolAutorizaId($rolAutorizaId)
{
        $this->rolAutorizaId = $rolAutorizaId;
}


/**
* Get descripcionTarea
*
* @return string
*/		
     		
public function getDescripcionTarea(){
	return $this->descripcionTarea; 
}

/**
* Set descripcionTarea
*
* @param string $descripcionTarea
*/
public function setDescripcionTarea($descripcionTarea)
{
        $this->descripcionTarea = $descripcionTarea;
}


/**
* Get tiempoMax
*
* @return integer
*/		
     		
public function getTiempoMax(){
	return $this->tiempoMax; 
}

/**
* Set tiempoMax
*
* @param integer $tiempoMax
*/
public function setTiempoMax($tiempoMax)
{
        $this->tiempoMax = $tiempoMax;
}


/**
* Get unidadMedidaTiempo
*
* @return string
*/		
     		
public function getUnidadMedidaTiempo(){
	return $this->unidadMedidaTiempo; 
}

/**
* Set unidadMedidaTiempo
*
* @param string $unidadMedidaTiempo
*/
public function setUnidadMedidaTiempo($unidadMedidaTiempo)
{
        $this->unidadMedidaTiempo = $unidadMedidaTiempo;
}


/**
* Get costo
*
* @return 
*/		
     		
public function getCosto(){
	return $this->costo; 
}

/**
* Set costo
*
* @param  $costo
*/
public function setCosto($costo)
{
        $this->costo = $costo;
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
* Get peso
*
* @return 
*/		
     		
public function getPeso(){
	return $this->peso; 
}

/**
* Set peso
*
* @param  $peso
*/
public function setPeso($peso)
{
        $this->peso = $peso;
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
* Get procesoId
*
* @return telconet\schemaBundle\Entity\AdmiProceso
*/		
     		
public function getProcesoId(){
	return $this->procesoId; 
}

/**
* Set procesoId
*
* @param telconet\schemaBundle\Entity\AdmiProceso $procesoId
*/
public function setProcesoId(\telconet\schemaBundle\Entity\AdmiProceso $procesoId)
{
        $this->procesoId = $procesoId;
}

/**
* Get automaticaWs
*
* @return
*/

public function getAutomaticaWs(){
	return $this->automaticaWs;
}

/**
* Set automaticaWs
*
* @param  $automaticaWs
*/
public function setAutomaticaWs($automaticaWs)
{
        $this->automaticaWs = $automaticaWs;
}


/**
 * Get categoriaTareaId
 *
 * @return integer
 */
public function getCategoriaTareaId()
{
    return $this->categoriaTareaId;
}

/**
 * Set categoriaTareaId
 *
 * @param integer $categoriaTareaId
 */
public function setCategoriaTareaId($categoriaTareaId)
{
    $this->categoriaTareaId = $categoriaTareaId;
}

public function __toString()
{
        return $this->nombreTarea;
}

}
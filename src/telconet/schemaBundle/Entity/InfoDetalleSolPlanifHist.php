<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoDetalleSolPlanifHist
 *
 * @ORM\Table(name="INFO_DETALLE_SOL_PLANIF_HIST")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoDetalleSolPlanifHistRepository")
 */
class InfoDetalleSolPlanifHist
{


	/**
	* @var integer $id
	*
	* @ORM\Column(name="ID_DETALLE_SOL_PLANIF_HIST", type="integer", nullable=false)
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="SEQUENCE")
	* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_DETALLE_SOL_PLANIF_HI", allocationSize=1, initialValue=1)
	*/		
			
	private $id;	
		
	/**
	* @var InfoDetalleSolPlanif
	*
	* @ORM\ManyToOne(targetEntity="InfoDetalleSolPlanif")
	* @ORM\JoinColumns({
	*   @ORM\JoinColumn(name="DETALLE_SOL_PLANIF_ID", referencedColumnName="ID_DETALLE_SOL_PLANIF")
	* })
	*/

	private $detalleSolPlanifId;

	/**
	* @var datetime $feIniPlan
	*
	* @ORM\Column(name="FE_INI_PLAN", type="datetime", nullable=true)
	*/		
				
	private $feIniPlan;

	/**
	* @var datetime $feFinPlan
	*
	* @ORM\Column(name="FE_FIN_PLAN", type="datetime", nullable=true)
	*/		
				
	private $feFinPlan;

	/**
	* @var integer $asignadoId
	*
	* @ORM\Column(name="ASIGNADO_ID", type="integer", nullable=true)
	*/		
			
	private $asignadoId;

	/**
	* @var string $tipoAsignado
	*
	* @ORM\Column(name="TIPO_ASIGNADO", type="string", nullable=true)
	*/		
				
	private $tipoAsignado;

	/**
	* @var integer $tareaId
	*
	* @ORM\Column(name="TAREA_ID", type="integer", nullable=true)
	*/		
	
	private $tareaId;


	/**
	* @var integer $motivoId
	*
	* @ORM\Column(name="MOTIVO_ID", type="integer", nullable=true)
	*/		
			
	private $motivoId;

	/**
	* @var string $usrCreacion
	*
	* @ORM\Column(name="USR_CREACION", type="string", nullable=true)
	*/		
				
	private $usrCreacion;

	/**
	* @var datetime $feCreacion
	*
	* @ORM\Column(name="FE_CREACION", type="datetime", nullable=true)
	*/		
				
	private $feCreacion;

	/**
	* @var string $ipCreacion
	*
	* @ORM\Column(name="IP_CREACION", type="string", nullable=false)
	*/		
				
	private $ipCreacion;

	/**
	* @var string $observacion
	*
	* @ORM\Column(name="OBSERVACION", type="string", nullable=true)
	*/		

	private $observacion;

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
	* Get detalleSolPlanifId
	*
	* @return telconet\schemaBundle\Entity\InfoDetalleSolPlanif
	*/		
				
	public function getDetalleSolPlanifId(){
		return $this->detalleSolPlanifId; 
	}

	/**
	* Set detalleSolPlanifId
	*
	* @param telconet\schemaBundle\Entity\InfoDetalleSolicitud $detalleSolPlanifId
	*/
	public function setDetalleSolPlanifId(\telconet\schemaBundle\Entity\InfoDetalleSolPlanif $detalleSolPlanifId)
	{
			$this->detalleSolPlanifId = $detalleSolPlanifId;
	}


	/**
	* Get asignadoId
	*
	* @return integer
	*/		
				
	public function getAsignadoId(){
		return $this->asignadoId; 
	}

	/**
	* Set asignadoId
	*
	* @param integer $asignadoId
	*/
	public function setAsignadoId($asignadoId)
	{
			$this->asignadoId = $asignadoId;
	}

	/**
	* Get tipoAsignado
	*
	* @return string
	*/		
				
	public function getTipoAsignado(){
		return $this->tipoAsignado; 
	}

	/**
	* Set tipoAsignado
	*
	* @param string $tipoAsignado
	*/
	public function setTipoAsignado($tipoAsignado)
	{
			$this->tipoAsignado = $tipoAsignado;
	}

	/**
	* Get feIniPlan
	*
	* @return datetime
	*/		
				
	public function getFeIniPlan(){
		return $this->feIniPlan; 
	}

	/**
	* Set feIniPlan
	*
	* @param datetime $feIniPlan
	*/
	public function setFeIniPlan($feIniPlan)
	{
			$this->feIniPlan = $feIniPlan;
	}

	/**
	* Get feFinPlan
	*
	* @return datetime
	*/		
				
	public function getFeFinPlan(){
		return $this->feFinPlan; 
	}

	/**
	* Set feFinPlan
	*
	* @param datetime $feFinPlan
	*/
	public function setFeFinPlan($feFinPlan)
	{
			$this->feFinPlan = $feFinPlan;
	}


	/**
	* Get tareaId
	*
	* @return integer
	*/		
				
	public function getTareaId(){
		return $this->tareaId; 
	}

	/**
	* Set tareaId
	*
	* @param integer $tareaId
	*/
	public function setTareaId($tareaId)
	{
			$this->tareaId = $tareaId;
	}

	/**
	* Get motivoId
	*
	* @return integer
	*/		
				
	public function getMotivoId(){
		return $this->motivoId; 
	}

	/**
	* Set motivoId
	*
	* @param integer $motivoId
	*/
	public function setMotivoId($motivoId)
	{
			$this->motivoId = $motivoId;
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
	* Get observacion
	*
	* @return string
	*/
	public function getObservacion()
	{
		return $this->observacion;
	}
 
	/**
	 * Set observacion
	 *
	 * @param string $observacion
	 */
	public function setObservacion($observacion)
	{
		$this->observacion = $observacion;
	}

}
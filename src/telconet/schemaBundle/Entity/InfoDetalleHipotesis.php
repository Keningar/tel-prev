<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoDetalleHipotesis
 *
 * @ORM\Table(name="INFO_DETALLE_HIPOTESIS")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoDetalleHipotesisRepository")
 */
class InfoDetalleHipotesis
{


	/**
	* @var text $observacion
	*
	* @ORM\Column(name="OBSERVACION", type="text", nullable=true)
	*/		
	     		
	private $observacion;

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
	* @ORM\Column(name="ESTADO", type="string", nullable=false)
	*/		
	     		
	private $estado;

	/**
	* @var integer $id
	*
	* @ORM\Column(name="ID_DETALLE_HIPOTESIS", type="integer", nullable=false)
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="SEQUENCE")
	* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_DETALLE_HIPOTESIS", allocationSize=1, initialValue=1)
	*/		
			
	private $id;	
		
	/**
	* @var InfoCaso
	*
	* @ORM\ManyToOne(targetEntity="InfoCaso")
	* @ORM\JoinColumns({
	*   @ORM\JoinColumn(name="CASO_ID", referencedColumnName="ID_CASO")
	* })
	*/
			
	private $casoId;

	/**
	* @var AdmiSintoma
	*
	* @ORM\ManyToOne(targetEntity="AdmiSintoma")
	* @ORM\JoinColumns({
	*   @ORM\JoinColumn(name="SINTOMA_ID", referencedColumnName="ID_SINTOMA")
	* })
	*/
			
	private $sintomaId;

	/**
	* @var AdmiHipotesis
	*
	* @ORM\ManyToOne(targetEntity="AdmiHipotesis")
	* @ORM\JoinColumns({
	*   @ORM\JoinColumn(name="HIPOTESIS_ID", referencedColumnName="ID_HIPOTESIS")
	* })
	*/
			
	private $hipotesisId;


	/**
	* Get observacion
	*
	* @return 
	*/		
	     		
	public function getObservacion(){
		return $this->observacion; 
	}

	/**
	* Set observacion
	*
	* @param  $observacion
	*/
	public function setObservacion($observacion)
	{
	        $this->observacion = $observacion;
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
	* @return telconet\schemaBundle\Entity\InfoCaso
	*/		
	     		
	public function getCasoId(){
		return $this->casoId; 
	}

	/**
	* Set casoId
	*
	* @param telconet\schemaBundle\Entity\InfoCaso $casoId
	*/
	public function setCasoId(\telconet\schemaBundle\Entity\InfoCaso $casoId)
	{
	        $this->casoId = $casoId;
	}


	/**
	* Get sintomaId
	*
	* @return telconet\schemaBundle\Entity\AdmiSintoma
	*/		
	     		
	public function getSintomaId(){
		return $this->sintomaId; 
	}

	/**
	* Set sintomaId
	*
	* @param telconet\schemaBundle\Entity\AdmiSintoma $sintomaId
	*/
	public function setSintomaId(\telconet\schemaBundle\Entity\AdmiSintoma $sintomaId)
	{
	        $this->sintomaId = $sintomaId;
	}


	/**
	* Get hipotesisId
	*
	* @return telconet\schemaBundle\Entity\AdmiHipotesis
	*/		
	     		
	public function getHipotesisId(){
		return $this->hipotesisId; 
	}

	/**
	* Set hipotesisId
	*
	* @param telconet\schemaBundle\Entity\AdmiHipotesis $hipotesisId
	*/
	public function setHipotesisId(\telconet\schemaBundle\Entity\AdmiHipotesis $hipotesisId)
	{
	        $this->hipotesisId = $hipotesisId;
	}

}
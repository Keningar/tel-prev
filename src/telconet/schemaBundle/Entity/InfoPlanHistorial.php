<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoPlanHistorial
 *
 * @ORM\Table(name="INFO_PLAN_HISTORIAL")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoPlanHistorialRepository")
 */
class InfoPlanHistorial
{

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
* @var string $observacion
*
* @ORM\Column(name="OBSERVACION", type="string", nullable=true)
*/		
     		
private $observacion;
	
/**
* @var InfoPlanCab
*
* @ORM\ManyToOne(targetEntity="InfoPlanCab")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="PLAN_ID", referencedColumnName="ID_PLAN")
* })
*/		
     		
private $planId;

/**
* @var integer $id
*
* @ORM\Column(name="ID_PLAN_HISTORIAL", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_PLAN_HISTORIAL", allocationSize=1, initialValue=1)
*/		
		
private $id;	


    /**
     * Set estado
     *
     * @param string $estado
     * @return InfoServicioHistorial
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
    
        return $this;
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
     * Set feCreacion
     *
     * @param \DateTime $feCreacion
     * @return InfoServicioHistorial
     */
    public function setFeCreacion($feCreacion)
    {
        $this->feCreacion = $feCreacion;
    
        return $this;
    }

    /**
     * Get feCreacion
     *
     * @return \DateTime 
     */
    public function getFeCreacion()
    {
        return $this->feCreacion;
    }

    /**
     * Set usrCreacion
     *
     * @param string $usrCreacion
     * @return InfoServicioHistorial
     */
    public function setUsrCreacion($usrCreacion)
    {
        $this->usrCreacion = $usrCreacion;
    
        return $this;
    }

    /**
     * Get usrCreacion
     *
     * @return string 
     */
    public function getUsrCreacion()
    {
        return $this->usrCreacion;
    }

    /**
     * Set ipCreacion
     *
     * @param string $ipCreacion
     * @return InfoServicioHistorial
     */
    public function setIpCreacion($ipCreacion)
    {
        $this->ipCreacion = $ipCreacion;
    
        return $this;
    }

    /**
     * Get ipCreacion
     *
     * @return string 
     */
    public function getIpCreacion()
    {
        return $this->ipCreacion;
    }

    /**
    * Get planId
    *
    * @return telconet\schemaBundle\Entity\InfoPlanCab
    */		

    public function getPlanId(){
            return $this->planId; 
    }

    /**
    * Set planId
    *
    * @param telconet\schemaBundle\Entity\InfoPlanCab $planId
    */
    public function setPlanId(\telconet\schemaBundle\Entity\InfoPlanCab $planId)
    {
            $this->planId = $planId;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Set observacion
     *
     * @param string $observacion
     * @return InfoServicioHistorial
     */
    public function setObservacion($observacion)
    {
        $this->observacion = $observacion;
    
        return $this;
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
	
    public function __clone() 
    {
        $this->id = null;
    }
	
}

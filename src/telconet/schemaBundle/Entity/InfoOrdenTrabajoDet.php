<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoOrdenTrabajoDet
 *
 * @ORM\Table(name="INFO_ORDEN_TRABAJO_DET")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoOrdenTrabajoDetRepository")
 */
class InfoOrdenTrabajoDet
{
    
    /**
    * @var integer $id
    *
    * @ORM\Column(name="ID_ORDEN_TRABAJO_DET", type="integer", nullable=false)
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="SEQUENCE")
    * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_ORDEN_TRABAJO_DET", allocationSize=1, initialValue=1)
    */		

    private $id;	

    /**
    * @var InfoOrdenTrabajo
    *
    * @ORM\ManyToOne(targetEntity="InfoOrdenTrabajo")
    * @ORM\JoinColumns({
    *   @ORM\JoinColumn(name="ORDEN_TRABAJO_ID", referencedColumnName="ID_ORDEN_TRABAJO")
    * })
    */

    private $ordenTrabajoId;

    /**
    * @var integer $tareaId
    *
    * @ORM\Column(name="TAREA_ID", type="integer", nullable=true)
    */	
    private $tareaId;

    /**
    * @var integer $categoriaTareaId
    *
    * @ORM\Column(name="CATEGORIA_TAREA_ID", type="integer", nullable=true)
    */		

    private $categoriaTareaId;

    /**
    * @var string $estado
    *
    * @ORM\Column(name="ESTADO", type="string", nullable=true)
    */		

    private $estado;

    /**
    * @var datetime $feCreacion
    *
    * @ORM\Column(name="FE_CREACION", type="datetime", nullable=true)
    */		

    private $feCreacion;

    /**
    * @var date $feUltMod
    *
    * @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=true)
    */		

    private $feUltMod;

    /**
    * @var string $usrCreacion
    *
    * @ORM\Column(name="USR_CREACION", type="string", nullable=true)
    */		

    private $usrCreacion;

    /**
    * @var string $usrUltMod
    *
    * @ORM\Column(name="USR_ULT_MOD", type="string", nullable=true)
    */		

    private $usrUltMod;

    /**
    * @var string $ipCreacion
    *
    * @ORM\Column(name="IP_CREACION", type="string", nullable=true)
    */		

    private $ipCreacion;

    /**
    * Get id
    *
    * @return integer
    */		

    public function getId(){
        return $this->id; 
    }



    /**
    * Get ordenTrabajoId
    *
    * @return telconet\schemaBundle\Entity\InfoOrdenTrabajo
    */		

    public function getOrdenTrabajoId(){
        return $this->ordenTrabajoId; 
    }

    /**
    * Set ordenTrabajoId
    *
    * @param telconet\schemaBundle\Entity\InfoOrdenTrabajo $ordenTrabajoId
    */
    public function setOrdenTrabajoId(\telconet\schemaBundle\Entity\InfoOrdenTrabajo $ordenTrabajoId)
    {
            $this->ordenTrabajoId = $ordenTrabajoId;
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
    * Get categoriaTareaId
    *
    * @return integer
    */		

    public function getCategoriaTareaId(){
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
    * Get feUltMod
    *
    * @return 
    */		

    public function getFeUltMod(){
        return $this->feUltMod; 
    }

    /**
    * Set feUltMod
    *
    * @param  $feUltMod
    */
    public function setFeUltMod($feUltMod)
    {
            $this->feUltMod = $feUltMod;
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
}

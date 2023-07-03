<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiNumeracionOficina
 *
 * @ORM\Table(name="ADMI_NUMERACION_OFICINA")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiNumeracionOficinaRepository")
 */
class AdmiNumeracionOficina
{
    /**
    * @var integer $id
    *
    * @ORM\Column(name="ID_NUMERACION_OFICINA", type="integer", nullable=false)
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="SEQUENCE")
    * @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_NUMERACION_OFICINA", allocationSize=1, initialValue=1)
    */			
    private $id;
    
    /**
     * @var InfoOficinaGrupo
     *
     * @ORM\ManyToOne(targetEntity="InfoOficinaGrupo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="OFICINA_ID", referencedColumnName="ID_OFICINA", nullable=false)
     * })
     */
    private $oficinaId;

    /**
     * @var AdmiNumeracion
     *
     * @ORM\ManyToOne(targetEntity="AdmiNumeracion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="NUMERACION_ID", referencedColumnName="ID_NUMERACION", nullable=false)
     * })
     */	
    private $numeracionId;	

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
    * Get id
    *
    * @return integer
    */		   		
    public function getId()
    {
        return $this->id; 
    }
    
    /**
     * Get personaEmpresaRolId
     *
     * @return telconet\schemaBundle\Entity\InfoOficinaGrupo
     */
    public function getOficinaId()
    {
        return $this->oficinaId;
    }
    

    /**
    * Set oficinaId
    *
    * @param telconet\schemaBundle\Entity\InfoOficinaGrupo $oficinaId
    */
    public function setOficinaId(\telconet\schemaBundle\Entity\InfoOficinaGrupo $oficinaId)
    {
        $this->oficinaId = $oficinaId;
    }


    /**
     * Get numeracionId
     *
     * @return telconet\schemaBundle\Entity\AdmiNumeracion
     */
    public function getNumeracionId()
    {
        return $this->numeracionId;
    }
    

    /**
     * Set numeracionId
     *
     * @param telconet\schemaBundle\Entity\AdmiNumeracion $numeracionId
     */
    public function setNumeracionId(\telconet\schemaBundle\Entity\AdmiNumeracion $numeracionId)
    {
        $this->numeracionId = $numeracionId;
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
    public function getFeCreacion()
    {
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
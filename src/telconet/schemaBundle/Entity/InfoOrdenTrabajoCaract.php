<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoOrdenTrabajoCaract
 *
 * @ORM\Table(name="INFO_ORDEN_TRABAJO_CARACT")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoOrdenTrabajoCaractRepository")
 */
class InfoOrdenTrabajoCaract
{
    
    /**
    * @var integer $id
    *
    * @ORM\Column(name="ID_ORDEN_TRABAJO_CARACT", type="integer", nullable=false)
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="SEQUENCE")
    * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_ORDEN_TRABAJO_CARACT", allocationSize=1, initialValue=1)
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
    private $ordenTrabajo;

	/**
	* @var AdmiCaracteristica
	*
	* @ORM\ManyToOne(targetEntity="AdmiCaracteristica")
	* @ORM\JoinColumns({
	*   @ORM\JoinColumn(name="CARACTERISTICA_ID", referencedColumnName="ID_CARACTERISTICA")
	* })
	*/
    private $caracteristica;
    

    /**
    * @var string $valor
    *
    * @ORM\Column(name="VALOR", type="string", nullable=false)
    */		
    private $valor;


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
    * Get ordenTrabajo
    *
    * @return telconet\schemaBundle\Entity\InfoOrdenTrabajo
    */		

    public function getOrdenTrabajo(){
        return $this->ordenTrabajo; 
    }

    /**
    * Set ordenTrabajo
    *
    * @param telconet\schemaBundle\Entity\InfoOrdenTrabajo $ordenTrabajo
    */
    public function setOrdenTrabajo(\telconet\schemaBundle\Entity\InfoOrdenTrabajo $ordenTrabajo)
    {
            $this->ordenTrabajo = $ordenTrabajo;
    }



    /**
	* Get caracteristica
	*
	* @return telconet\schemaBundle\Entity\AdmiCaracteristica
	*/				
    public function getCaracteristica(){
        return $this->caracteristica; 
    }

    /**
	* Set caracteristica
	*
	* @param telconet\schemaBundle\Entity\AdmiCaracteristica $caracteristica
	*/
    public function setCaracteristica($caracteristica)
    {
            $this->caracteristica = $caracteristica;
    }


    
    
    /**
    * Get valor
    *
    * @return string
    */		

    public function getValor(){
        return $this->valor; 
    }

    /**
    * Set valor
    *
    * @param string $valor
    */
    public function setValor($valor)
    {
            $this->valor = $valor;
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

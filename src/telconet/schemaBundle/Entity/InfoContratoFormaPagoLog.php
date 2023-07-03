<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoContratoFormaPagoLog
 *
 * @ORM\Table(name="INFO_CONTRATO_FORMA_PAGO_LOG")
 * @ORM\Entity
 */
class InfoContratoFormaPagoLog
{

    /**
    * @var integer $id
    *
    * @ORM\Column(name="ID_CONTRATO_LOG", type="integer", nullable=false)
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="SEQUENCE")
    * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_CONTRATO_FP_LOG", allocationSize=1, initialValue=1)
    */		

    private $id;


    /**
    * @var InfoContrato
    *
    * @ORM\ManyToOne(targetEntity="InfoContrato")
    * @ORM\JoinColumns({
    *   @ORM\JoinColumn(name="CONTRATO_ID", referencedColumnName="ID_CONTRATO")
    * })
    */

    private $contratoId;

    /**
    * @var InfoPersona
    *
    * @ORM\ManyToOne(targetEntity="InfoPersona")
    * @ORM\JoinColumns({
    *   @ORM\JoinColumn(name="PERSONA_ID", referencedColumnName="ID_PERSONA")
    * })
    */

    private $personaId;


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
    * Get contratoId
    *
    * @return telconet\schemaBundle\Entity\InfoContrato
    */		

    public function getContratoId(){
        return $this->contratoId; 
    }

    /**
    * Set contratoId
    *
    * @param telconet\schemaBundle\Entity\InfoContrato $contratoId
    */
    public function setContratoId(\telconet\schemaBundle\Entity\InfoContrato $contratoId)
    {
        $this->contratoId = $contratoId;
    }

    /**
    * Get personaId
    *
    * @return telconet\schemaBundle\Entity\InfoPersona
    */		

    public function getPersonaId(){
        return $this->personaId; 
    }

    /**
    * Set personaId
    *
    * @param telconet\schemaBundle\Entity\InfoPersona $personaId
    */
    public function setPersonaId(\telconet\schemaBundle\Entity\InfoPersona $personaId)
    {
        $this->personaId = $personaId;
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
}

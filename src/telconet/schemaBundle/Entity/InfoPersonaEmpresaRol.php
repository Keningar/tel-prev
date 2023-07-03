<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoPersonaEmpresaRol
 *
 * @ORM\Table(name="INFO_PERSONA_EMPRESA_ROL")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoPersonaEmpresaRolRepository")
 */
class InfoPersonaEmpresaRol
{


    /**
    * @var integer $id
    *
    * @ORM\Column(name="ID_PERSONA_ROL", type="integer", nullable=false)
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="SEQUENCE")
    * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_PERSONA_EMPRESA_ROL", allocationSize=1, initialValue=1)
    */		

    private $id;	

    /**
    * @var InfoPersona
    *
    * @ORM\ManyToOne(targetEntity="InfoPersona", inversedBy="persona_rol")
    * @ORM\JoinColumns({
    *   @ORM\JoinColumn(name="PERSONA_ID", referencedColumnName="ID_PERSONA")
    * })
    */	
    private $personaId;

    /**
    * @var InfoEmpresaRol
    *
    * @ORM\ManyToOne(targetEntity="InfoEmpresaRol", inversedBy="persona_rol")
    * @ORM\JoinColumns({
    *   @ORM\JoinColumn(name="EMPRESA_ROL_ID", referencedColumnName="ID_EMPRESA_ROL")
    * })
    */

    private $empresaRolId;

    /**

    * @var InfoOficinaGrupo
    *
    * @ORM\ManyToOne(targetEntity="InfoOficinaGrupo")
    * @ORM\JoinColumns({
    *   @ORM\JoinColumn(name="OFICINA_ID", referencedColumnName="ID_OFICINA")
    * })
    */

    private $oficinaId;

    /**

    * @var AdmiCuadrilla
    *
    * @ORM\ManyToOne(targetEntity="AdmiCuadrilla")
    * @ORM\JoinColumns({
    *   @ORM\JoinColumn(name="CUADRILLA_ID", referencedColumnName="ID_CUADRILLA")
    * })
    */

    private $cuadrillaId;

    /**
    * @var string $usrCreacion
    *
    * @ORM\Column(name="USR_CREACION", type="string", nullable=false)
    */		

    private $usrCreacion;

    /**
    * @var string $feCreacion
    *
    * @ORM\Column(name="FE_CREACION", type="datetime", nullable=false)
    */		

    private $feCreacion;

    /**
    * @var string $estado
    *
    * @ORM\Column(name="ESTADO", type="string", nullable=false)
    */		

    private $estado;

    /**
    * @var string $departamentoId
    *
    * @ORM\Column(name="DEPARTAMENTO_ID", type="integer", nullable=false)
    */		

    private $departamentoId;


    /**
    * @var integer $personaEmpresaRolId
    *
    * @ORM\Column(name="PERSONA_EMPRESA_ROL_ID", type="integer", nullable=false)
    */		

    private $personaEmpresaRolId;

    /**
    * @var string $ipCreacion
    *
    * @ORM\Column(name="IP_CREACION", type="string", nullable=false)
    */		

    private $ipCreacion;

    //se agrega declaracion del valor de campo PersonaId para ser utilizado en Consulta
    /**
    * @var string $personaIdValor
    *
    * @ORM\Column(name="PERSONA_ID", type="integer", nullable=false)
    */	
    private $personaIdValor;

    //se agrega declaracion del valor de campo reportaPersonaEmpresaRolId para ser utilizado en Consulta
    /**
    * @var string $reportaPersonaEmpresaRolId
    *
    * @ORM\Column(name="REPORTA_PERSONA_EMPRESA_ROL_ID", type="integer", nullable=false)
    */	
    private $reportaPersonaEmpresaRolId;
    
    /**
    * @var string $esPrepago
    *
    * @ORM\Column(name="ES_PREPAGO", type="string", nullable=false)
    */		

    private $esPrepago;    


    /**
    * Get id
    *
    * @return integer
    */		

    public function getId(){
        return $this->id; 
    }

    /**
    * Get personaId
    *
    * @return integer
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
    * Get empresaRolId
    *
    * @return integer
    */		

    public function getEmpresaRolId(){
        return $this->empresaRolId; 
    }

    /**
    * Set empresaRolId
    *
    * @param telconet\schemaBundle\Entity\InfoEmpresaRol $empresaRolId
    */
    public function setEmpresaRolId(\telconet\schemaBundle\Entity\InfoEmpresaRol $empresaRolId)
    {
            $this->empresaRolId = $empresaRolId;
    }


    /**
    * Get oficinaId
    *
    * @return integer
    */		

    public function getOficinaId(){
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
    * Get cuadrillaId
    *
    * @return integer
    */		

    public function getCuadrillaId(){
        return $this->cuadrillaId; 
    }

    /**
    * Set cuadrillaId
    *
    * @param telconet\schemaBundle\Entity\AdmiCuadrilla $cuadrillaId
    */
    public function setCuadrillaId(\telconet\schemaBundle\Entity\AdmiCuadrilla $cuadrillaId = null)
    {
            $this->cuadrillaId = $cuadrillaId;
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
    * @return 
    */		

    public function getFeCreacion(){
        return $this->feCreacion; 
    }

    /**
    * Set feCreacion
    *
    * @param  $feCreacion
    */
    public function setFeCreacion($feCreacion)
    {
            $this->feCreacion = $feCreacion;
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

    /**integer
    * Get estado
    *
    * @return string
    */		

    public function getDepartamentoId(){
        return $this->departamentoId; 
    }

    /**
    * Set departamentoId
    *
    * @param string $departamentoId
    */
    public function setDepartamentoId($departamentoId)
    {
            $this->departamentoId = $departamentoId;
    }


    /**
    * Get personaEmpresaRolId
    *
    * @return integer
    */		

    public function getPersonaEmpresaRolId(){
        return $this->personaEmpresaRolId; 
    }

    /**
    * Set personaEmpresaRolId
    *
    * @param integer $personaEmpresaRolId
    */
    public function setPersonaEmpresaRolId($personaEmpresaRolId)
    {
            $this->personaEmpresaRolId = $personaEmpresaRolId;
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

    //se agrega declaracion del valor de campo PersonaId para ser utilizado en Consulta
    /**
     * Get personaIdValor
     *
     * @return integer
     */
    public function getPersonaIdValor() {
        return $this->personaIdValor;
    }

    /**
     * Set personaIdValor
     *
     * @param integer $personaIdValor
     */
    public function setPersonaIdValor($personaIdValor) {
        $this->personaIdValor = $personaIdValor;
        return $this;
    }
    
    
    //se agrega declaracion del valor de campo reportaPersonaEmpresaRolId para ser utilizado en Consulta
    /**
     * Get reportaPersonaEmpresaRolId
     *
     * @return integer
     */
    public function getReportaPersonaEmpresaRolId()
    {
        return $this->reportaPersonaEmpresaRolId;
    }

    /**
     * Set reportaPersonaEmpresaRolId
     *
     * @param integer $reportaPersonaEmpresaRolId
     */
    public function setReportaPersonaEmpresaRolId($reportaPersonaEmpresaRolId)
    {
        $this->reportaPersonaEmpresaRolId = $reportaPersonaEmpresaRolId;
        return $this;
    }
    
    /**
    * Get esPrepago
    *
    * @return string
    */		

    public function getEsPrepago(){
        return $this->esPrepago; 
    }

    /**
    * Set esPrepago
    *
    * @param string $esPrepago
    */
    public function setEsPrepago($esPrepago)
    {
            $this->esPrepago = $esPrepago;
    }    

}
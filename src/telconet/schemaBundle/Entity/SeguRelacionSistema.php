<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\SeguRelacionSistema
 *
 * @ORM\Table(name="SEGU_RELACION_SISTEMA")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\SeguRelacionSistemaRepository")
 */
class SeguRelacionSistema
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_RELACION_SISTEMA", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_SEGU_RELACION_SISTEMA", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var SeguRelacionSistema
*
* @ORM\ManyToOne(targetEntity="SeguRelacionSistema")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="RELACION_SISTEMA_ID", referencedColumnName="ID_RELACION_SISTEMA")
* })
*/
		
private $relacionSistemaId;

/**
* @var string $usrCreacion
*
* @ORM\Column(name="USR_CREACION", type="string", nullable=true)
*/		
     		
private $usrCreacion;

/**
* @var SistModulo
*
* @ORM\ManyToOne(targetEntity="SistModulo", inversedBy="relacion_modulo")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="MODULO_ID", referencedColumnName="ID_MODULO")
* })
*/
		
private $moduloId;

/**
* @var SistAccion
*
* @ORM\ManyToOne(targetEntity="SistAccion", inversedBy="relacion_accion")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="ACCION_ID", referencedColumnName="ID_ACCION")
* })
*/
		
private $accionId;

/**
* @var SistItemMenu
*
* @ORM\ManyToOne(targetEntity="SistItemMenu", inversedBy="relacion_itenmenu")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="ITEM_MENU_ID", referencedColumnName="ID_ITEM_MENU")
* })
*/		
     		
private $itemMenuId;

/**
* @var integer $tareaInterfaceModeloTraId
*
* @ORM\Column(name="TAREA_INTERFACE_MODELO_TRA_ID", type="integer", nullable=true)
*/		
     		
private $tareaInterfaceModeloTraId;

/**
* @var datetime $feCreacion
*
* @ORM\Column(name="FE_CREACION", type="datetime", nullable=true)
*/		
     		
private $feCreacion;

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
* Get relacionSistemaId
*
* @return telconet\schemaBundle\Entity\SeguRelacionSistema
*/		
     		
public function getRelacionSistemaId(){
	return $this->relacionSistemaId; 
}

/**
* Set relacionSistemaId
*
* @param telconet\schemaBundle\Entity\SeguRelacionSistema $relacionSistemaId
*/
public function setRelacionSistemaId(\telconet\schemaBundle\Entity\SeguRelacionSistema $relacionSistemaId)
{
        $this->relacionSistemaId = $relacionSistemaId;
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
* Get moduloId
*
* @return telconet\schemaBundle\Entity\SistModulo
*/		
     		
public function getModuloId(){
	return $this->moduloId; 
}

/**
* Set moduloId
*
* @param telconet\schemaBundle\Entity\SistModulo $moduloId
*/
public function setModuloId(\telconet\schemaBundle\Entity\SistModulo $moduloId)
{
        $this->moduloId = $moduloId;
}


/**
* Get accionId
*
* @return telconet\schemaBundle\Entity\SistAccion
*/		
     		
public function getAccionId(){
	return $this->accionId; 
}

/**
* Set accionId
*
* @param telconet\schemaBundle\Entity\SistAccion $accionId
*/
public function setAccionId(\telconet\schemaBundle\Entity\SistAccion $accionId)
{
        $this->accionId = $accionId;
}


/**
* Get itemMenuId
*
* @return \telconet\schemaBundle\Entity\SistItemMenu 
*/		
     		
public function getItemMenuId(){
	return $this->itemMenuId; 
}

/**
* Set itemMenuId
*
* @param \telconet\schemaBundle\Entity\SistItemMenu $itemMenuId
*/
public function setItemMenuId(\telconet\schemaBundle\Entity\SistItemMenu $itemMenuId)
{
        $this->itemMenuId = $itemMenuId;
}

/**
* Get tareaInterfaceModeloTraId
*
* @return integer
*/		
     		
public function getTareaInterfaceModeloTrId(){
	return $this->tareaInterfaceModeloTraId; 
}

/**
* Set tareaInterfaceModeloTraId
*
* @param integer $tareaInterfaceModeloTraId
*/
public function setTareaInterfaceModeloTrId($tareaInterfaceModeloTraId)
{
        $this->tareaInterfaceModeloTraId = $tareaInterfaceModeloTraId;
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
    * @ORM\OneToMany(targetEntity="SeguAsignacion", mappedBy="relacionSistemaId")
    */
    private $asignacion_relacion;
    public function __construct()
    {
        $this->asignacion_relacion = new \Doctrine\Common\Collections\ArrayCollection();
    }
    public function addAsignacionRelacion(\telconet\schemaBundle\Entity\SeguAsignacion $asignacion_relacion)
    {
        $this->asignacion_relacion[] = $asignacion_relacion;
    }

    public function getAsignacionRelacion()
    {
        return $this->asignacion_relacion;
    }
}
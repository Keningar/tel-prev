<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoServicioTecnico
 *
 * @ORM\Table(name="INFO_SERVICIO_TECNICO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoServicioTecnicoRepository")
 */
class InfoServicioTecnico
{
    /**
    * @var integer $tercerizadoraId
    *
    * @ORM\Column(name="TERCERIZADORA_ID", type="integer", nullable=true)
    */

    private $tercerizadoraId;
    
    /**
    * @var integer $ultimaMillaId
    *
    * @ORM\Column(name="ULTIMA_MILLA_ID", type="integer", nullable=true)
    */

    private $ultimaMillaId;


    /**
    * @var integer $interfaceElementoId
    *
    * @ORM\Column(name="INTERFACE_ELEMENTO_ID", type="integer", nullable=true)
    */		

    private $interfaceElementoId;

    /**
    * @var integer $elementoId
    *
    * @ORM\Column(name="ELEMENTO_ID", type="integer", nullable=true)
    */		

    private $elementoId;

    /**
    * @var integer $interfaceElementoClienteId
    *
    * @ORM\Column(name="INTERFACE_ELEMENTO_CLIENTE_ID", type="integer", nullable=true)
    */		

    private $interfaceElementoClienteId;

    /**
    * @var integer $elementoClienteId
    *
    * @ORM\Column(name="ELEMENTO_CLIENTE_ID", type="integer", nullable=true)
    */		

    private $elementoClienteId;


    /**
    * @var integer $id
    *
    * @ORM\Column(name="ID_SERVICIO_TECNICO", type="integer", nullable=false)
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="SEQUENCE")
    * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_SERVICIO_TECNICO", allocationSize=1, initialValue=1)
    */		

    private $id;	
    
    /**
    * @var InfoServicio
    *
    * @ORM\ManyToOne(targetEntity="InfoServicio")
    * @ORM\JoinColumns({
    *   @ORM\JoinColumn(name="SERVICIO_ID", referencedColumnName="ID_SERVICIO")
    * })
    */

    private $servicioId;

    /**
    * @var integer $elementoContenedorId
    *
    * @ORM\Column(name="ELEMENTO_CONTENEDOR_ID", type="integer", nullable=true)
    */		

    private $elementoContenedorId;
    
    /**
    * @var integer $elementoConectorId
    *
    * @ORM\Column(name="ELEMENTO_CONECTOR_ID", type="integer", nullable=true)
    */		

    private $elementoConectorId;
    
    /**
    * @var integer $elementoConectorId
    *
    * @ORM\Column(name="INTERFACE_ELEMENTO_CONECTOR_ID", type="integer", nullable=true)
    */		

    private $interfaceElementoConectorId;
    
    /**
    * @var string $tipoEnlace
    *
    * @ORM\Column(name="TIPO_ENLACE", type="string", nullable=true)
    */		

    private $tipoEnlace;
    
    /**
    * Get interfaceElementoId
    *
    * @return integer
    */		

    public function getInterfaceElementoId(){
            return $this->interfaceElementoId; 
    }

    /**
    * Set interfaceElementoId
    *
    * @param integer $interfaceElementoId
    */
    public function setInterfaceElementoId($interfaceElementoId)
    {
            $this->interfaceElementoId = $interfaceElementoId;
    }


    /**
    * Get elementoId
    *
    * @return integer
    */		

    public function getElementoId(){
            return $this->elementoId; 
    }

    /**
    * Set elementoId
    *
    * @param integer $elementoId
    */
    public function setElementoId($elementoId)
    {
            $this->elementoId = $elementoId;
    }
    
    /**
    * Get interfaceElementoClienteId
    *
    * @return integer
    */		

    public function getInterfaceElementoClienteId(){
            return $this->interfaceElementoClienteId; 
    }

    /**
    * Set interfaceElementoClienteId
    *
    * @param integer $interfaceElementoClienteId
    */
    public function setInterfaceElementoClienteId($interfaceElementoClienteId)
    {
            $this->interfaceElementoClienteId = $interfaceElementoClienteId;
    }


    /**
    * Get elementoClienteId
    *
    * @return integer
    */		

    public function getElementoClienteId(){
            return $this->elementoClienteId; 
    }

    /**
    * Set elementoClienteId
    *
    * @param integer $elementoClienteId
    */
    public function setElementoClienteId($elementoClienteId)
    {
            $this->elementoClienteId = $elementoClienteId;
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
    * Get tercerizadoraId
    *
    * @return int
    */		

    public function getTercerizadoraId(){
            return $this->tercerizadoraId; 
    }

    /**
    * Set tercerizadoraId
    *
    * @param int $tercerizadoraId
    */
    public function setTercerizadoraId($tercerizadoraId)
    {
            $this->tercerizadoraId = $tercerizadoraId;
    }
    
    /**
    * Get ultimaMillaId
    *
    * @return int
    */		

    public function getUltimaMillaId(){
            return $this->ultimaMillaId; 
    }

    /**
    * Set ultimaMillaId
    *
    * @param int $ultimaMillaId
    */
    public function setUltimaMillaId($ultimaMillaId)
    {
            $this->ultimaMillaId = $ultimaMillaId;
    }
    
    /**
    * Get servicioId
    *
    * @return telconet\schemaBundle\Entity\InfoServicio
    */		

    public function getServicioId(){
            return $this->servicioId; 
    }

    /**
    * Set servicioId
    *
    * @param telconet\schemaBundle\Entity\InfoServicio $servicioId
    */
    public function setServicioId(\telconet\schemaBundle\Entity\InfoServicio $servicioId)
    {
            $this->servicioId = $servicioId;
    }
    
    /**
    * Get elementoConectorId
    *
    * @return integer
    */		

    public function getElementoConectorId(){
            return $this->elementoConectorId; 
    }

    /**
    * Set elementoConectorId
    *
    * @param integer $elementoConectorId
    */
    public function setElementoConectorId($elementoConectorId)
    {
            $this->elementoConectorId = $elementoConectorId;
    }
    
    /**
    * Get elementoContenedorId
    *
    * @return integer
    */		

    public function getElementoContenedorId(){
            return $this->elementoContenedorId; 
    }

    /**
    * Set elementoContenedorId
    *
    * @param integer $elementoContenedorId
    */
    public function setElementoContenedorId($elementoContenedorId)
    {
            $this->elementoContenedorId = $elementoContenedorId;
    }
    /**
    * Get interfaceElementoConectorId
    *
    * @return integer
    */		

    public function getInterfaceElementoConectorId(){
            return $this->interfaceElementoConectorId; 
    }

    /**
    * Set interfaceElementoConectorId
    *
    * @param integer $interfaceElementoConectorId
    */
    public function setInterfaceElementoConectorId($interfaceElementoConectorId)
    {
            $this->interfaceElementoConectorId = $interfaceElementoConectorId;
    }
    
   /**
    * Get tipoEnlace
    *
    * @return string
    */

    public function getTipoEnlace()
    {
        return $this->tipoEnlace;
    }

    /**
    * Set tipoEnlace
    *
    * @param string $tipoEnlace
    */

    public function setTipoEnlace($tipoEnlace)
    {
        $this->tipoEnlace = $tipoEnlace;
    }
    
    public function __toString(){
        return $this->id;
    }
}

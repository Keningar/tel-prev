<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * telconet\schemaBundle\Entity\AdmiPuntoAtencion
 *
 * @ORM\Table(name="ADMI_PUNTO_ATENCION")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiPuntoAtencionRepository")
 */
class AdmiPuntoAtencion
{

    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID_PUNTO_ATENCION", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_PUNTO_ATENCION", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string $nombrePuntoAtencion
     *
     * @ORM\Column(name="NOMBRE_PUNTO_ATENCION", type="string", nullable=false)
     */
    private $nombrePuntoAtencion;

    /**
     * @var string $estado
     *
     * @ORM\Column(name="ESTADO", type="string", nullable=true)
     */
    private $estado;

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
     * @var string $usrModificacion
     *
     * @ORM\Column(name="USR_MODIFICACION", type="string", nullable=true)
     */
    private $usrModificacion;

    /**
     * @var datetime $feModificacion
     *
     * @ORM\Column(name="FE_MODIFICACION", type="datetime", nullable=true)
     */
    private $feModificacion;

    /**
     * @var string $ipCreacion
     *
     * @ORM\Column(name="IP_CREACION", type="string", nullable=true)
     */
    private $ipCreacion;

    /**
     * @var string $empresaCod
     *
     * @ORM\Column(name="EMPRESA_COD", type="string", nullable=true)
     */
    private $empresaCod;


  /**
   * Get getId
   *
   * @return integer
   */
    public function getId() 
    {
        return $this->id;
    }
    
    /**
    * Set id
    * 
    * @param $id 
    */
    public function setId($id) {
        $this->id = $id;
    }

  /**
   * Get getNombrePuntoAtencion
   *
   * @return string
   */
    public function getNombrePuntoAtencion() 
    {
        return $this->nombrePuntoAtencion;
    }
    
    
   /**
    * Set setNombrePuntoAtencion
    * 
    * @param $nombrePuntoAtencion 
    */
    public function setNombrePuntoAtencion($nombrePuntoAtencion) 
    {
        $this->nombrePuntoAtencion = $nombrePuntoAtencion;
    }

  
   /**
    * Get getEstado
    *
    * @return string
    */
    public function getEstado() 
    {
        return $this->estado;
    }
    
    /**
    * Set setEstado
    * 
    * @param $estado 
    */
    public function setEstado($estado) 
    {
        $this->estado = $estado;
    }

   /**
    * Get getUsrCreacion
    *
    * @return string
    */
    public function getUsrCreacion() 
    {
        return $this->usrCreacion;
    }
    
    /**
    * Set setUsrCreacion
    * 
    * @param $usrCreacion 
    */
    public function setUsrCreacion($usrCreacion) 
    {
        $this->usrCreacion = $usrCreacion;
    }

    /**
    * Get getFeCreacion
    *
    * @return datetime
    */
    public function getFeCreacion()
    {
        return $this->feCreacion;
    }
    
   /**
    * Set setFeCreacion
    * 
    * @param $feCreacion 
    */
    public function setFeCreacion($feCreacion) 
    {
        $this->feCreacion = $feCreacion;
    }

   /**
    * Get getUsrModificacion
    *
    * @return string
    */
    public function getUsrModificacion() 
    {
        return $this->usrModificacion;
    }
    
   /**
    * Set setUsrModificacion
    * 
    * @param $usrModificacion 
    */
    public function setUsrModificacion($usrModificacion) 
    {
        $this->usrModificacion = $usrModificacion;
    }

   /**
    * Get getFeModificacion
    *
    * @return datetime
    */
    public function getFeModificacion() 
    {
        return $this->feModificacion;
    }
    
    /**
    * Set setFeModificacion
    * 
    * @param $feModificacion 
    */
    public function setFeModificacion($feModificacion) 
    {
        $this->feModificacion = $feModificacion;
    }

   /**
    * Get getIpCreacion
    *
    * @return string
    */
    public function getIpCreacion()
    {
        return $this->ipCreacion;
    }

   /**
    * Set setIpCreacion
    * 
    * @param $ipCreacion 
    */
    public function setIpCreacion($ipCreacion) 
    {
        $this->ipCreacion = $ipCreacion;
    }
    
    /**
    * Get getEmpresaCod
    *
    * @return string
    */
    public function getEmpresaCod() 
    {
        return $this->empresaCod;
    }

   /**
    * Set setEmpresaCod
    * 
    * @param $ipCreacion 
    */
    public function setEmpresaCod($empresaCod) 
    {
        $this->empresaCod = $empresaCod;
    }


    

}

<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiZona
 *
 * @ORM\Table(name="ADMI_ZONA")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiZonaRepository")
 */
class AdmiZona
{
    /**
    * @var integer $id
    *
    * @ORM\Column(name="ID_ZONA", type="integer", nullable=false)
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="SEQUENCE")
    * @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_ZONA", allocationSize=1, initialValue=1)
    */		

    private $id;	

    /**
    * @var string $nombreZona
    *
    * @ORM\Column(name="NOMBRE_ZONA", type="string", nullable=false)
    */		

    private $nombreZona;

    /**
    * @var string $latitud
    *
    * @ORM\Column(name="LATITUD", type="string", nullable=false)
    */		

    private $latitud;

    /**
    * @var string $centroY
    *
    * @ORM\Column(name="LONGITUD", type="string", nullable=false)
    */		

    private $longitud;

    /**
    * @var string $radio
    *
    * @ORM\Column(name="RADIO", type="string", nullable=false)
    */		

    private $radio;

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
    * @var string $usrModificacion
    *
    * @ORM\Column(name="USR_MODIFICACION", type="string", nullable=false)
    */		

    private $usrModificacion;

    /**
    * @var datetime $feUltMod
    *
    * @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=false)
    */		

    private $feUltMod;

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
    * Get nombreZona
    *
    * @return string
    */		
    public function getNombreZona()
    {
        return $this->nombreZona; 
    }

    /**
    * Set nombreZona
    *
    * @param string $nombreZona
    */
    public function setNombreZona($nombreZona)
    {
            $this->nombreZona = $nombreZona;
    }

    /**
    * Get latitud
    *
    * @return string
    */		
    public function getLatitud()
    {
        return $this->latitud; 
    }

    /**
    * Set centroX
    *
    * @param string $latitud
    */
    public function setLatitud($latitud)
    {
        $this->latitud = $latitud;
    }

    /**
    * Get longitud
    *
    * @return string
    */		
    public function getLongitud()
    {
        return $this->longitud; 
    }

    /**
    * Set longitud
    *
    * @param string $longitud
    */
    public function setLongitud($longitud)
    {
        $this->longitud = $longitud;
    }

    /**
    * Get radio
    *
    * @return string
    */		

    public function getRadio()
    {
        return $this->radio; 
    }

    /**
    * Set radio
    *
    * @param string $radio
    */
    public function setRadio($radio)
    {
            $this->radio = $radio;
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


    /**
    * Get usrModificacion
    *
    * @return string
    */		
    public function getUsrModificacion()
    {
        return $this->usrModificacion; 
    }

    /**
    * Set usrModificacion
    *
    * @param string $usrModificacion
    */
    public function setUsrModificacion($usrModificacion)
    {
        $this->usrModificacion = $usrModificacion;
    }


    /**
    * Get feUltMod
    *
    * @return datetime
    */		
    public function getFeUltMod()
    {
        return $this->feUltMod; 
    }

    /**
    * Set feUltMod
    *
    * @param datetime $feUltMod
    */
    public function setFeUltMod($feUltMod)
    {
        $this->feUltMod = $feUltMod;
    }

    public function __toString()
    {
        return $this->nombreZona;
    }

}
<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoRegistroEmpleado
 *
 * @ORM\Table(name="INFO_REGISTRO_EMPLEADO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoRegistroEmpleadoRepository")
 */
class InfoRegistroEmpleado
{
    /**
    * @var integer $id
    *
    * @ORM\Column(name="ID_REGISTRO_EMPLEADO", type="integer", nullable=false)
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="SEQUENCE")
    * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_REGISTRO_EMPLEADO", allocationSize=1, initialValue=1)
    */		

    private $id;	

    /**
    * @var integer $personaEmpresaRolId
    *
    * @ORM\Column(name="PERSONA_EMPRESA_ROL_ID", type="integer", nullable=false)
    * 
    */

    private $personaEmpresaRolId;

    /**
    * @var string $tipoRegistro
    *
    * @ORM\Column(name="TIPO_REGISTRO", type="string", nullable=false)
    */		

    private $tipoRegistro;

    /**
    * @var string $latitud
    *
    * @ORM\Column(name="LATITUD", type="string", nullable=false)
    */		

    private $latitud;    

    /**
    * @var string $longitud
    *
    * @ORM\Column(name="LONGITUD", type="string", nullable=false)
    */		

    private $longitud;

    /**
    * @var string $permiso
    *
    * @ORM\Column(name="PERMISO", type="string", nullable=true)
    */		

    private $permiso;
    
    /**
    * @var datetime $feRegistro
    *
    * @ORM\Column(name="FE_REGISTRO", type="datetime", nullable=false)
    */		

    private $feRegistro;      
    

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
    * @var string $estado
    *
    * @ORM\Column(name="ESTADO", type="string", nullable=true)
    */		

    private $estado;
    
    /**
    * @var string $imei
    *
    * @ORM\Column(name="IMEI", type="string", nullable=true)
    */		

    private $imei;

    /**
    * @var datetime $feUltMod
    *
    * @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=false)
    */		

    private $feUltMod;

    /**
    * @var string $usrUltMod
    *
    * @ORM\Column(name="USR_ULT_MOD", type="string", nullable=false)
    */		

    private $usrUltMod;

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
    * @return integer
    */

    public function getPersonaEmpresaRolId()
    {
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
    * Get ipCreacion
    *
    * @return string
    */		

    public function getIpCreacion()
    {
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
    * Get imei
    *
    * @return string
    */		

    public function getImei()
    {
        return $this->imei; 
    }

    /**
    * Set imei
    *
    * @param string $imei
    */
    public function setImei($imei)
    {
        $this->imei = $imei;
    }

    /**
    * Get feRegistro
    *
    * @return datetime
    */		

    public function getFeRegistro()
    {
        return $this->feRegistro; 
    }

    /**
    * Set feRegistro
    *
    * @param datetime $feRegistro
    */
    public function setFeRegistro($feRegistro)
    {
        $this->feRegistro = $feRegistro;
    }    
    
    /**
    * Get tipoRegistro
    *
    * @return string
    */		

    public function getTipoRegistro()
    {
        return $this->tipoRegistro; 
    }

    /**
    * Set tipoRegistro
    *
    * @param string $tipoRegistro
    */
    public function setTipoRegistro($tipoRegistro)
    {
        $this->tipoRegistro = $tipoRegistro;
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
    * Set latitud
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
    * Get permiso
    *
    * @return string
    */		

    public function getPermiso()
    {
        return $this->permiso;
    }

    /**
    * Set permiso
    *
    * @param string $permiso
    */
    public function setPermiso($permiso)
    {
        $this->permiso = $permiso;
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



    /**
    * Get usrUltMod
    *
    * @return string
    */		

    public function getUsrUltMod()
    {
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


}

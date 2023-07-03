<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\SistPerfil
 *
 * @ORM\Table(name="SIST_PERFIL")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\SistPerfilRepository")
 */
class SistPerfil
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_PERFIL", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_SIST_PERFIL", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var string $nombrePerfil
*
* @ORM\Column(name="NOMBRE_PERFIL", type="string", nullable=true)
*/		
     		
private $nombrePerfil;

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
* @var string $usrUltMod
*
* @ORM\Column(name="USR_ULT_MOD", type="string", nullable=true)
*/		
     		
private $usrUltMod;

/**
* @var datetime $feUltMod
*
* @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=true)
*/		
     		
private $feUltMod;

/**
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Get nombrePerfil
*
* @return string
*/		
     		
public function getNombrePerfil(){
	return $this->nombrePerfil; 
}

/**
* Set nombrePerfil
*
* @param string $nombrePerfil
*/
public function setNombrePerfil($nombrePerfil)
{
        $this->nombrePerfil = $nombrePerfil;
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
* Get feUltMod
*
* @return datetime
*/		
     		
public function getFeUltMod(){
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
    * @ORM\OneToMany(targetEntity="SeguPerfilPersona", mappedBy="perfilId")
    */
    private $perfil_persona;
    public function __construct()
    {
        $this->perfil_persona = new \Doctrine\Common\Collections\ArrayCollection();
    }
    public function addPerfilPersona(\telconet\schemaBundle\Entity\SeguPerfilPersona $perfil_persona)
    {
        $this->perfil_persona[] = $perfil_persona;
    }

    public function getPerfilPersona()
    {
        return $this->perfil_persona;
    }
     
}
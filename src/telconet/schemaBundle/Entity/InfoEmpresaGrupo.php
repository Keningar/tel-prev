<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoEmpresaGrupo
 *
 * @ORM\Table(name="INFO_EMPRESA_GRUPO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoEmpresaGrupoRepository")
 */
class InfoEmpresaGrupo
{

    /**
* @var string $id
*
* @ORM\Column(name="COD_EMPRESA", type="string", nullable=false)
* @ORM\Id
*/		
		
private $id;

/**
* @var string $nombreEmpresa
*
* @ORM\Column(name="NOMBRE_EMPRESA", type="string", nullable=true)
*/		
     		
private $nombreEmpresa;

/**
* @var string $razonSocial
*
* @ORM\Column(name="RAZON_SOCIAL", type="string", nullable=false)
*/		
     		
private $razonSocial;

/**
* @var string $ruc
*
* @ORM\Column(name="RUC", type="string", nullable=false)
*/		
     		
private $ruc;

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
* @var string $ipCreacion
*
* @ORM\Column(name="IP_CREACION", type="string", nullable=true)
*/		
     		
private $ipCreacion;

/**
* @var string $ldapDn;
*
* @ORM\Column(name="LDAP_DN", type="string", nullable=true)
*/		
     		
private $ldapDn;

/**
* @var string $prefijo;
*
* @ORM\Column(name="PREFIJO", type="string", nullable=true)
*/		
     		
private $prefijo;

/**
* @var string $facturaElectronico;
*
* @ORM\Column(name="FACTURA_ELECTRONICO", type="string", nullable=true)
*/		
     		
private $facturaElectronico;

/**
* Get id
*
* @return string 
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Set id
*
* @param string $id
*/		
     		
public function setId($id){
        $this->id = $id;
}

/**
* Get nombreEmpresa
*
* @return string
*/		
     		
public function getNombreEmpresa(){
	return $this->nombreEmpresa; 
}

/**
* Set nombreEmpresa
*
* @param string $nombreEmpresa
*/
public function setNombreEmpresa($nombreEmpresa)
{
        $this->nombreEmpresa = $nombreEmpresa;
}


/**
* Get razonSocial
*
* @return string
*/		
     		
public function getRazonSocial(){
	return $this->razonSocial; 
}

/**
* Set razonSocial
*
* @param string $razonSocial
*/
public function setRazonSocial($razonSocial)
{
        $this->razonSocial = $razonSocial;
}


/**
* Get ruc
*
* @return string
*/		
     		
public function getRuc(){
	return $this->ruc; 
}

/**
* Set ruc
*
* @param string $ruc
*/
public function setRuc($ruc)
{
        $this->ruc = $ruc;
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
* Get prefijo
*
* @return string
*/		
     		
public function getPrefijo(){
	return $this->prefijo; 
}

/**
* Set prefijo
*
* @param string $prefijo
*/
public function setPrefijo($prefijo)
{
        $this->prefijo = $prefijo;
}

/**
* Get facturaElectronico
*
* @return string
*/  		
public function getFacturaElectronico()
{
	return $this->facturaElectronico; 
}

/**
* Set facturaElectronico
*
* @param string $facturaElectronico
*/
public function setFacturaElectronico($facturaElectronico)
{
    $this->facturaElectronico = $facturaElectronico;
}

/**
* Get ldapDn
*
* @return string
*/		
     		
public function getLdapDn(){ 
	return $this->ldapDn; 
}

/**
* Set ldapDn
*
* @param string $ldapDn
*/
public function setLdapDn($ldapDn)
{
        $this->ldapDn = $ldapDn;
}

public function __toString()
{
    return $this->nombreEmpresa;
}

    /**
    * @ORM\OneToMany(targetEntity="InfoEmpresaRol", mappedBy="empresaId")
    */
    private $empresagrupo_rol;
    /**
    * @ORM\OneToMany(targetEntity="InfoOficinaGrupo", mappedBy="empresaId")
    */
    private $empresagrupo_oficina;
    
    public function __construct()
    {
        $this->empresagrupo_rol = new \Doctrine\Common\Collections\ArrayCollection();
        $this->empresagrupo_oficina = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    public function addEmpresagrupoRol(\telconet\schemaBundle\Entity\InfoEmpresaRol $empresagrupo_rol)
    {
        $this->empresagrupo_rol[] = $empresagrupo_rol;
    }

    public function getEmpresagrupoRol()
    {
        return $this->empresagrupo_rol;
    }

    public function addEmpresagrupoOficina(\telconet\schemaBundle\Entity\InfoOficinaGrupo $empresagrupo_oficina)
    {
        $this->empresagrupo_oficina[] = $empresagrupo_oficina;
    }

    public function getEmpresagrupoOficina()
    {
        return $this->empresagrupo_oficina;
    }
}

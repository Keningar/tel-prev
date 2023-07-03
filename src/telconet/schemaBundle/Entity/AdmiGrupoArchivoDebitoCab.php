<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiGrupoArchivoDebitoCab
 *
 * @ORM\Table(name="ADMI_GRUPO_ARCHIVO_DEBITO_CAB")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiGrupoArchivoDebitoCabRepository")
 */
class AdmiGrupoArchivoDebitoCab
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_GRUPO_DEBITO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_GRUPO_ARCHIVO_DEB_CAB", allocationSize=1, initialValue=1)
*/		
		
private $id;	

/**
* @var AdmiBancoTipoCuenta
*
* @ORM\ManyToOne(targetEntity="AdmiBancoTipoCuenta")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="BANCO_TIPO_CUENTA_ID", referencedColumnName="ID_BANCO_TIPO_CUENTA")
* })
*/
		
private $bancoTipoCuentaId;


/**
* @var string $nombreGrupo
*
* @ORM\Column(name="NOMBRE_GRUPO", type="string", nullable=true)
*/		
     		
private $nombreGrupo;


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
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=false)
*/		
     		
private $estado;


/**
* @var string $tipoGrupo
*
* @ORM\Column(name="TIPO_GRUPO", type="string", nullable=false)
*/			
private $tipoGrupo;



/**
* @var integer $empresaCod
*
* @ORM\Column(name="EMPRESA_COD", type="integer", nullable=true)
*/		
     		
private $empresaCod;

public function getEmpresaCod() {
    return $this->empresaCod;
}

public function setEmpresaCod($empresaCod) {
    $this->empresaCod = $empresaCod;
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
* Get bancoTipoCuentaId
*
* @return \telconet\schemaBundle\Entity\AdmiBancoTipoCuenta
*/		
     		
public function getBancoTipoCuentaId(){
	return $this->bancoTipoCuentaId; 
}

/**
* Set bancoTipoCuentaId
*
* @param \telconet\schemaBundle\Entity\AdmiBancoTipoCuenta $bancoTipoCuentaId
*/
public function setBancoTipoCuentaId(\telconet\schemaBundle\Entity\AdmiBancoTipoCuenta $bancoTipoCuentaId)
{
        $this->bancoTipoCuentaId = $bancoTipoCuentaId;
}


/**
* Get nombreGrupo
*
* @return string
*/		
     		
public function getNombreGrupo(){
	return $this->nombreGrupo; 
}

/**
* Set nombreGrupo
*
* @param string $nombreGrupo
*/
public function setNombreGrupo($nombreGrupo)
{
        $this->nombreGrupo = $nombreGrupo;
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
* Get tipoGrupo
*
* @return string
*/		   		
public function getTipoGrupo()
{
    return $this->tipoGrupo; 
}

/**
* Set tipoGrupo
*
* @param string $tipoGrupo
*/
public function setTipoGrupo($tipoGrupo)
{
    $this->tipoGrupo = $tipoGrupo;
}

}

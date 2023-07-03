<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert; 
use Symfony\Component\HttpFoundation\File\UploadedFile;


/**
 * telconet\schemaBundle\Entity\InfoDebitoRespuesta
 *
 * @ORM\Table(name="INFO_DEBITO_RESPUESTA")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoDebitoRespuestaRepository")
 */
class InfoDebitoRespuesta
{

/**
* @var integer $id
*
* @ORM\Column(name="ID_RESPUESTA_DEBITO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_RESPUESTA_DEBITO", allocationSize=1, initialValue=1)
*/

private $id;

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
* @var integer $bancoTipoCuentaId
*
* @ORM\Column(name="BANCO_TIPO_CUENTA_ID", type="integer", nullable=true)
*/			
private $bancoTipoCuentaId;

/**
* @var string $nombreBanco
*
* @ORM\Column(name="NOMBRE_BANCO", type="string", nullable=false)
*/		
     		
private $nombreBanco;

/**
* @var string $nombreTipoCuenta
*
* @ORM\Column(name="NOMBRE_TIPO_CUENTA", type="string", nullable=false)
*/		

private $nombreTipoCuenta;

/**
* @var integer $debitoCabId
*
* @ORM\Column(name="DEBITO_CAB_ID", type="integer", nullable=false)
*/		
     		
private $debitoCabId;

/**
* @var integer $debitoGeneralId
*
* @ORM\Column(name="DEBITO_GENERAL_ID", type="integer", nullable=false)
*/		
     		
private $debitoGeneralId;

/**
* @var string $usrCreacion
*
* @ORM\Column(name="USR_CREACION", type="string", nullable=false)
*/		
     		
private $usrCreacion;


/**
* @ORM\Column(name="ARCHIVO", type="string", length=255, nullable=false)
*/
private $path;

/**
* @ORM\Column(name="ARCHIVO_NO_ENCONTRADOS", type="string", length=255, nullable=false)
*/
private $pathNoEncontrados;

/**
* @Assert\File(maxSize="6000000")
*/
private $file;


/**
* @var integer $valorArchivo
*
* @ORM\Column(name="VALOR_ARCHIVO", type="integer", nullable=true)
*/		
     		
private $valorArchivo;


/**
* @var string $estadoCierre
*
* @ORM\Column(name="ESTADO_CIERRE", type="string", length=255, nullable=true)
*/		
     		
private $estadoCierre;

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
* Get pathNoEncontrados
*
* @return string
*/		
     		
public function getPathNoEncontrados(){
	return $this->pathNoEncontrados; 
}

/**
* Set pathNoEncontrados
*
* @param string $pathNoEncontrados
*/
public function setPathNoEncontrados($pathNoEncontrados)
{
        $this->pathNoEncontrados = $pathNoEncontrados;
}

/**
* Get debitoCabId
*
* @return integer
*/		
     		
public function getDebitoCabId(){
	return $this->debitoCabId; 
}

/**
* Set debitoCabId
*
* @param integer $debitoCabId
*/
public function setDebitoCabId($debitoCabId)
{
        $this->debitoCabId = $debitoCabId;
}



/**
* Get debitoGeneralId
*
* @return integer
*/		
     		
public function getDebitoGeneralId(){
	return $this->debitoGeneralId; 
}

/**
* Set debitoGeneralId
*
* @param integer $debitoGeneralId
*/
public function setDebitoGeneralId($debitoGeneralId)
{
        $this->debitoGeneralId = $debitoGeneralId;
}


/**
* Get bancoTipoCuentaId
*
* @return integer
*/		
     		
public function getBancoTipoCuentaId(){
	return $this->bancoTipoCuentaId; 
}

/**
* Set bancoTipoCuentaId
*
* @param integer $bancoTipoCuentaId
*/
public function setBancoTipoCuentaId($bancoTipoCuentaId)
{
        $this->bancoTipoCuentaId = $bancoTipoCuentaId;
}

/**
* Get nombreBanco
*
* @return string
*/		
     		
public function getNombreBanco(){
	return $this->nombreBanco; 
}

/**
* Set nombreBanco
*
* @param string $nombrebanco
*/
public function setNombreBanco($nombreBanco)
{
        $this->nombreBanco = $nombreBanco;
}


/**
* Get nombreTipoCuenta
*
* @return string
*/		
     		
public function getNombreTipoCuenta(){
	return $this->nombreTipoCuenta; 
}

/**
* Set nombreTipoCuenta
*
* @param string $nombreTipoCuenta
*/
public function setNombreTipoCuenta($nombreTipoCuenta)
{
        $this->nombreTipoCuenta = $nombreTipoCuenta;
}

/**
* Get valorArchivo
*
* @return integer
*/		     		
public function getValorArchivo()
{
    return $this->valorArchivo; 
}

/**
* Set valorArchivo
*
* @param integer $valorArchivo
*/
public function setValorArchivo($valorArchivo)
{
    $this->valorArchivo = $valorArchivo;
}

/**
* Get estadoCierre
*
* @return string
*/		     		
public function getEstadoCierre()
{
    return $this->estadoCierre; 
}

/**
* Set estadoCierre
*
* @param string $estadoCierre
*/
public function setEstadoCierre($estadoCierre)
{
    $this->estadoCierre = $estadoCierre;
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

//metodos para File 
/**
* Set file
*
* @param string $file
*/
public function setFile($file)
{
        $this->file = $file;
}

/**
* Get file
*
* @return string 
*/
public function getFile()
{
        return $this->file;
}

public function setPath($path)
{
        $this->path = $path;
}

public function getPath()
{
        return $this->path;
}
	
/**
* @ORM\PrePersist()
* @ORM\PreUpdate()
*/
public function preUpload($prefijoEmpresa)
{
        if (null !== $this->file) {
            //generamos un nombre �nico de archivo
            $this->path = $prefijoEmpresa."_".$this->nombreBanco."_respuesta_".date("Ymd_His").".".$this->file->getClientOriginalExtension();
        }
		
}

/**
* @ORM\PostPersist()
* @ORM\PostUpdate()
*/
public function upload($prefijoEmpresa)
{
        if (null === $this->file) { // El archivo no es obligatorio, por si viene vac�o
            return;
        }
		
        //Se lanza una excepci�n si el archivo no se puede mover para que la entidad no persista en la base de datos
        // labor que realizar autom�ticamente move()
        $this->file->move($this->getUploadRootDir(), $prefijoEmpresa."_".$this->nombreBanco."_respuesta_".date("Ymd_His").".".$this->file->getClientOriginalExtension());
        unset($this->file);
}
	
/**
* @ORM\PostRemove()
* 
*/
public function removeUpload($prefijoEmpresa)
{
        if ($file = $this->getAbsolutePath($prefijoEmpresa)) {
            unlink($file);
        }
}
	
//M�todos b�sicos de subida
public function getAbsolutePath($prefijoEmpresa)
{
        return null === $this->path ? null : $this->getUploadRootDir().'/'.$prefijoEmpresa."_".$this->nombreBanco."_respuesta.".$this->path;
}
	
public function getWebPath()
{
        return null === $this->path ? null : $this->getUploadDir().'/'.$this->path;
}
	
protected function getUploadRootDir()
{
        return __DIR__.'/../../../../web/public/'.$this->getUploadDir();
    //return '/var/www/telconet/web/public/'.$this->getUploadDir();
}

protected function getUploadDir()
{
        return '/uploads/respuesta_debitos';
}

public function getWebPath1()
{
        return null === $this->path ? null : '/telcos/web/public'.$this->getUploadDir().'/'.$this->path;
}

}

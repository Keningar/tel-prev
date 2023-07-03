<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert; 
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * telconet\schemaBundle\Entity\InfoRecaudacion
 *
 * @ORM\Table(name="INFO_RECAUDACION")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoRecaudacionRepository")
 */
class InfoRecaudacion
{

/**
* @var integer $id
*
* @ORM\Column(name="ID_RECAUDACION", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_RECAUDACION", allocationSize=1, initialValue=1)
*/
private $id;

/**
 * @var string $empresaCod
 *
 * @ORM\Column(name="EMPRESA_COD", type="string", nullable=false)
 */
private $empresaCod;

/**
 * @var integer $oficinaId
 *
 * @ORM\Column(name="OFICINA_ID", type="integer", nullable=true)
 */
private $oficinaId;

/**
 * @var integer $procesoMasivoId
 *
 * @ORM\Column(name="PROCESO_MASIVO_ID", type="integer", nullable=true)
 */
private $procesoMasivoId;

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
* @var string $usrCreacion
*
* @ORM\Column(name="USR_CREACION", type="string", nullable=false)
*/		
private $usrCreacion;

/**
 * @var string $ipCreacion
 *
 * @ORM\Column(name="IP_CREACION", type="string", nullable=false)
 */
private $ipCreacion;

/**
 * @var string $archivoEnvio
 *
 * @ORM\Column(name="ARCHIVO_ENVIO", type="string", nullable=false)
 */
private $archivoEnvio;

/**
 * @var integer $canalRecaudacionId
 *
 * @ORM\Column(name="CANAL_RECAUDACION_ID", type="integer", nullable=true)
 */
private $canalRecaudacionId;

/**
* @var string $path
* @ORM\Column(name="ARCHIVO", type="string", length=255, nullable=false)
* @Assert\NotBlank(message="Debe especificarse un archivo válido")
*/
private $path;

/**
* @var string $pathNoEncontrado
* @ORM\Column(name="ARCHIVO_NFS_NO_ENCONTRADO", type="string", length=3200, nullable=true)
*/
private $pathNoEncontrado;

/**
* @Assert\File(maxSize="6000000")
*/
private $file;

/**
*
* @return integer
*/		
     		
public function getId()
{
	return $this->id; 
}

/**
*
* @return string
*/
public function getEmpresaCod()
{
    return $this->empresaCod;
}

/**
*
* @param string $empresaCod
*/
public function setEmpresaCod($empresaCod)
{
    $this->empresaCod = $empresaCod;
}

/**
 *
 * @return integer
 */
public function getOficinaId()
{
    return $this->oficinaId;
}

/**
 *
 * @param integer $oficinaId
 */
public function setOficinaId($oficinaId)
{
    $this->oficinaId = $oficinaId;
}
	
/**
 *
 * @return integer
 */
public function getProcesoMasivoId()
{
    return $this->procesoMasivoId;
}

/**
 *
 * @param integer $procesoMasivoId
 */
public function setProcesoMasivoId($procesoMasivoId)
{
    $this->procesoMasivoId = $procesoMasivoId;
}

/**
*
* @return string
*/		
     		
public function getEstado()
{
	return $this->estado; 
}

/**
*
* @param string $estado
*/
public function setEstado($estado)
{
    $this->estado = $estado;
}

/**
*
* @return datetime
*/		
     		
public function getFeCreacion()
{
	return $this->feCreacion; 
}

/**
*
* @param datetime $feCreacion
*/
public function setFeCreacion($feCreacion)
{
    $this->feCreacion = $feCreacion;
}


/**
*
* @return string
*/		
     		
public function getUsrCreacion()
{
	return $this->usrCreacion; 
}

/**
*
* @param string $usrCreacion
*/
public function setUsrCreacion($usrCreacion)
{
    $this->usrCreacion = $usrCreacion;
}

/**
 *
 * @return string
 */
public function getIpCreacion()
{
    return $this->ipCreacion;
}

/**
 *
 * @param string $ipCreacion
 */
public function setIpCreacion($ipCreacion)
{
    $this->ipCreacion = $ipCreacion;
}


/**
 *
 * @return integer
 */
public function getCanalRecaudacionId()
{
    return $this->canalRecaudacionId;
}

/**
 *
 * @param integer $canalRecaudacionId
 */
public function setCanalRecaudacionId($canalRecaudacionId)
{
    $this->canalRecaudacionId = $canalRecaudacionId;
}


public function getPath()
{
    return $this->path;
}

public function setPath($path)
{
    $this->path = $path;
}

public function getPathNoEncontrados()
{
    return $this->pathNoEncontrado;
}

public function setPathNoEncontrados($path)
{
    $this->pathNoEncontrado = $path;
}

//metodos para File 
/**
*
* @param string $file
*/
public function setFile($file)
{
    $this->file = $file;
}

/**
*
* @return string 
*/
public function getFile()
{
    return $this->file;
}

public function getArchivoEnvio()
{
    return $this->archivoEnvio;
}

public function setArchivoEnvio($archivoEnvio)
{
    $this->archivoEnvio = $archivoEnvio;
}

/**
* @ORM\PrePersist()
* @ORM\PreUpdate()
*/
public function preUpload()
{
    if (null !== $this->file && ' ' !== $this->file)
    {
        //generamos un nombre unico de archivo
        $this->path = "recaudacion_".date("Ymd_His").".".$this->file->getClientOriginalExtension();
    }
    else
    {
        $this->path = "recaudacion_tpm_".date("Ymd_His").".xls";
    }
}


/**
* @ORM\PostPersist()
* @ORM\PostUpdate()
*/
public function upload()
{
    if (null === $this->file)
    {
        // El archivo no es obligatorio, por si viene vacio
        return;
    }
    //Se lanza una excepcion si el archivo no se puede mover para que la entidad no persista en la base de datos
    // labor que realizar automaticamente move()
    $this->file->move($this->getUploadRootDir(), "recaudacion_".date("Ymd_His").".".$this->file->getClientOriginalExtension());
    unset($this->file);
}

public function uploadEnvio()
{
    if (null === $this->fileEnvio)
    {
        // El archivo no es obligatorio, por si viene vacio
        return;
    }
    //Se lanza una excepcion si el archivo no se puede mover para que la entidad no persista en la base de datos
    // labor que realizar automaticamente move()
    $this->fileEnvio->move($this->getUploadRootDir(), "recaudacion_env".date("Ymd_His").".".$this->fileEnvio->getClientOriginalExtension());
    unset($this->fileEnvio);
}
	
/**
* @ORM\PostRemove()
* 
*/
public function removeUpload()
{
    if ($file = $this->getAbsolutePath())
    {
        unlink($file);
    }
}
	
//Metodos basicos de subida
public function getAbsolutePath()
{
    return null === $this->path ? null : $this->getPath();
}

//Metodos basicos de subida
public function getAbsoluteNoEncontradosPath()
{
    return null === $this->path ? null : $this->getPathNoEncontrados();
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
    return '/uploads/recaudacion_pagos';
}

public function getWebPath1()
{
    return null === $this->path ? null : '/telcos/web/public'.$this->getUploadDir().'/'.$this->path;
}

}

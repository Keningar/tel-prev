<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoDocumentoComunMasiva
 *
 * @ORM\Table(name="INFO_DOCUMENTO_COMUN_MASIVA")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoDocumentoComunMasivaRepository")
 */
class InfoDocumentoComunMasiva
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_DOC_COM_MASIVA", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_DOCUMENTO_COMUN_MASIVA", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var $comunicacionId
*
* @ORM\ManyToOne(targetEntity="InfoComunicacion")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="COMUNICACION_ID", referencedColumnName="ID_COMUNICACION")
* })
*/
		
private $comunicacionId;

/**
* @var $documentoId
*
* @ORM\ManyToOne(targetEntity="InfoDocumento")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="DOCUMENTO_ID", referencedColumnName="ID_DOCUMENTO")
* })
*/
		
private $documentoId;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=false)
*/		
     		     		
private $estado;

/**
* @var string $tipoEnvio
*
* @ORM\Column(name="TIPO_ENVIO", type="string", nullable=true)
*/

private $tipoEnvio;


/**
* @var datetime $feCreacion
*
* @ORM\Column(name="FE_CREACION", type="datetime", nullable=true)
*/		
     		
private $feCreacion;

/**
* @var string $usrCreacion
*
* @ORM\Column(name="USR_CREACION", type="string", nullable=true)
*/		
     		
private $usrCreacion;

/**
* @var string $empresaCod
*
* @ORM\Column(name="EMPRESA_COD", type="string", nullable=true)
*/		
     		
private $empresaCod;

/**
* @var string $puntosEnviar
*
* @ORM\Column(name="PUNTOS_ENVIAR", type="string", nullable=true)
*/		
     		
private $puntosEnviar;


/**
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
  * Set comunicacionId
  *
  * @param \telconet\schemaBundle\Entity\InfoComunicacion $comunicacionId
  * 
  */
public function setComunicacionId(\telconet\schemaBundle\Entity\InfoComunicacion $comunicacionId = null)
{
    $this->comunicacionId = $comunicacionId;  
}

/**
  * Get comunicacionId
  *
  * @return \telconet\schemaBundle\Entity\InfoComunicacion
  */
public function getComunicacionId()
{
    return $this->comunicacionId;
}



/**
  * Set documentoId
  *
  * @param \telconet\schemaBundle\Entity\InfoDocumento $documentoId
  * 
  */
public function setDocumentoId(\telconet\schemaBundle\Entity\InfoDocumento $documentoId = null)
{
    $this->documentoId = $documentoId;  
}

/**
  * Get documentoId
  *
  * @return \telconet\schemaBundle\Entity\InfoDocumento
  */
public function getDocumentoId()
{
    return $this->documentoId;
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
* Get tipoEnvio
*
* @return string
*/		
     		
public function getTipoEnvio(){
	return $this->tipoEnvio; 
}

/**
* Set tipoEnvio
*
* @param string $tipoEnvio
*/
public function setTipoEnvio($tipoEnvio)
{
        $this->tipoEnvio = $tipoEnvio;
}



/**
* Get empresaCod
*
* @return string
*/		
     		
public function getEmpresaCod(){
	return $this->empresaCod; 
}

/**
* Set empresaCod
*
* @param string $empresaCod
*/
public function setEmpresaCod($empresaCod)
{
        $this->empresaCod = $empresaCod;
}


/**
* Get puntosEnviar
*
* @return string
*/		
     		
public function getPuntosEnviar(){
	return $this->puntosEnviar; 
}

/**
* Set puntosEnviar
*
* @param string $puntosEnviar
*/
public function setPuntosEnviar($puntosEnviar)
{
        $this->puntosEnviar = $puntosEnviar;
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


}
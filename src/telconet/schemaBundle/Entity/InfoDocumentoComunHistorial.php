<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoDocumentoComunHistorial
 *
 * @ORM\Table(name="INFO_DOCUMENTO_COMUN_HIST")
 * @ORM\Entity 
 */
class InfoDocumentoComunHistorial
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_DOC_COM_HISTORIAL", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_DOCUMENTO_COMUN_HIST", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var $documentoComunicacionId
*
* @ORM\ManyToOne(targetEntity="InfoDocumentoComunicacion")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="DOCUMENTO_COMUNICACION_ID", referencedColumnName="ID_DOCUMENTO_COMUNICACION")
* })
*/
		
private $documentoComunicacionId;

/**
* @var $documComunMasivaId
*
* @ORM\ManyToOne(targetEntity="InfoDocumentoComunMasiva")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="DOCUM_COMUN_MASIVA_ID", referencedColumnName="ID_DOC_COM_MASIVA")
* })
*/
		
private $documComunMasivaId;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=false)
*/		
     		     		
private $estado;

/**
* @var string $observacion
*
* @ORM\Column(name="OBSERVACION", type="string", nullable=true)
*/

private $observacion;

/**
* @var string $seguimiento
*
* @ORM\Column(name="SEGUIMIENTO", type="string", nullable=true)
*/

private $seguimiento;

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
* @var string $equipoOcupado
*
* @ORM\Column(name="EQUIPO_OCUPADO", type="string", nullable=true)
*/		
     		
private $equipoOcupado;


/**
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
  * Set documentoComunicacionId
  *
  * @param \telconet\schemaBundle\Entity\InfoDocumentoComunicacion $documentoComunicacionId
  * 
  */
public function setDocumentoComunicacionId(\telconet\schemaBundle\Entity\InfoDocumentoComunicacion $documentoComunicacionId = null)
{
    $this->documentoComunicacionId = $documentoComunicacionId;  
}

/**
  * Get documentoComunicacionId
  *
  * @return \telconet\schemaBundle\Entity\InfoDocumentoComunicacion
  */
public function getDocumentoComunicacionId()
{
    return $this->documentoComunicacionId;
}
          

/**
  * Set documComunMasivaId
  *
  * @param \telconet\schemaBundle\Entity\InfoDocumentoComunMasiva $documComunMasivaId
  * 
  */
public function setDocumentoComunMasivaId(\telconet\schemaBundle\Entity\InfoDocumentoComunMasiva $documComunMasivaId = null)
{
    $this->documComunMasivaId = $documComunMasivaId;  
}

/**
  * Get documentoComunicacionId
  *
  * @return \telconet\schemaBundle\Entity\InfoDocumentoComunMasiva
  */
public function getDocumentoComunMasivaId()
{
    return $this->documComunMasivaId;
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
* Get observacion
*
* @return string
*/		
     		
public function getObservacion(){
	return $this->observacion; 
}

/**
* Set observacion
*
* @param string $observacion
*/
public function setObservacion($observacion)
{
        $this->observacion = $observacion;
}



/**
* Get seguimiento
*
* @return string
*/		
     		
public function getSeguimiento(){
	return $this->seguimiento; 
}

/**
* Set seguimiento
*
* @param string $seguimiento
*/
public function setSeguimiento($seguimiento)
{
        $this->seguimiento = $seguimiento;
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


/**
* Get equipoOcupado
*
* @return string
*/		
     		
public function getEquipoOcupado(){
	return $this->equipoOcupado; 
}

/**
* Set usrCreacion
*
* @param string $equipoOcupado
*/
public function setEquipoOcupado($equipoOcupado)
{
        $this->equipoOcupado = $equipoOcupado;
}


}
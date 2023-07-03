<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoCertificadoDocumento
 *
 * @ORM\Table(name="INFO_CERTIFICADO_DOCUMENTO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoCertificadoDocumentoRepository")
 */
class InfoCertificadoDocumento
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_CERTIFICADO_DOCUMENTO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_CERTIFICADO_DOCUMENTO", allocationSize=1, initialValue=1)
*/

private $id;

/**
* @var integer $certificadoId
*
* @ORM\Column(name="CERTIFICADO_ID", type="integer", nullable=false)
*/

private $certificadoId;

/**
* @var string $src
*
* @ORM\Column(name="SRC", type="string", nullable=false)
*/
     
private $src;

/**
* @var string $tipoDocumento
*
* @ORM\Column(name="TIPO_DOCUMENTO", type="string", nullable=false)
*/

private $tipoDocumento;

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
* @var string $usrUltMod
*
* @ORM\Column(name="USR_ULT_MOD", type="string", nullable=false)
*/
     
private $usrUltMod;

/**
* @var datetime $feUltMod
*
* @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=false)
*/
     
private $feUltMod;

/**
* @var string $ipCreacion
*
* @ORM\Column(name="IP_CREACION", type="string", nullable=false)
*/
     
private $ipCreacion;

/**
* @var string $documentado
*
* @ORM\Column(name="DOCUMENTADO", type="string", nullable=false)
*/
     
private $documentado;


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
* Get certificadoId
*
* @return integer
*/

public function getCertificadoId()
{
    return $this->certificadoId; 
}

/**
* Set certificadoId
*
* @param integer $intCertificadoId
*/
public function setCertificadoId($intCertificadoId)
{
    $this->certificadoId = $intCertificadoId;
}

/**
* Get src
*
* @return string
*/

public function getSrc()
{
    return $this->src; 
}

/**
* Set src
*
* @param string $strSrc
*/
public function setSrc($strSrc)
{
    $this->src = $strSrc;
}

/**
* Get tipoDocumento
*
* @return string
*/

public function getTipoDocumento()
{
    return $this->tipoDocumento; 
}

/**
* Set tipoDocumento
*
* @param string $strTipoDocumento
*/
public function setTipoDocumento($strTipoDocumento)
{
    $this->tipoDocumento = $strTipoDocumento;
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
* @param string $strUsrCreacion
*/
public function setUsrCreacion($strUsrCreacion)
{
    $this->usrCreacion = $strUsrCreacion;
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
* @param array $arrayFeCreacion
*/
public function setFeCreacion($arrayFeCreacion)
{
    $this->feCreacion = $arrayFeCreacion;
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
* @param string $strUsrUltMod
*/
public function setUsrUltMod($strUsrUltMod)
{
    $this->usrUltMod = $strUsrUltMod;
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
* @param datetime $arrayFeUltMod
*/
public function setFeUltMod($arrayFeUltMod)
{
    $this->feCreacion = $arrayFeUltMod;
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
* @param string $strIpCreacion
*/
public function setIpCreacion($strIpCreacion)
{
    $this->ipCreacion = $strIpCreacion;
}

/**
* Get documentado
*
* @return string
*/

public function getDocumentado()
{
    return $this->documentado; 
}

/**
* Set documentado
*
* @param string $strDocumentado
*/
public function setDocumentado($strDocumentado)
{
    $this->documentado = $strDocumentado;
}

}

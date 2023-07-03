<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoTransaccion
 *
 * @ORM\Table(name="INFO_TRANSACCION")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoTransaccionRepository")
 */
class InfoTransaccion
{

/**
* @var integer $id
*
* @ORM\Column(name="ID_TRANSACCION", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_TRANSACCION", allocationSize=1, initialValue=1)
*/

private $id;

/**
* @var number $certificadoId
*
* @ORM\Column(name="CERTIFICADO_ID", type="number", nullable=false)
*/

private $certificadoId;

/**
* @var number $json
*
* @ORM\Column(name="JSON", type="string", nullable=false)
*/

private $json;

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
* @ORM\Column(name="FE_CREACION", type="string", nullable=false)
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
* @var string $rubrica
*
* @ORM\Column(name="RUBRICA", type="string", nullable=false)
*/

private $rubrica;

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
* @return number
*/

public function getCertificadoId()
{
    return $this->certificadoId; 
}

/**
* Set certificadoId
*
* @param number $intCertificadoId
*/
public function setCertificadoId($intCertificadoId)
{
    $this->plantillaId = $intCertificadoId;
}

/**
* Get json
*
* @return string
*/

public function getJson()
{
    return $this->json; 
}

/**
* Set json
*
* @param string $strJson
*/
public function setJson($strJson)
{
    $this->json = $strJson;
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
* @param string $strEstado
*/
public function setEstado($strEstado)
{
    $this->estado = $strEstado;
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
* @param array $arrayFeUltMod
*/
public function setFeUltMod($arrayFeUltMod)
{
    $this->feUltMod = $arrayFeUltMod;
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
* Get rubrica
*
* @return string
*/

public function getRubrica()
{
    return $this->rubrica; 
}

/**
* Set rubrica
*
* @param string $strRubrica
*/
public function setRubrica($strRubrica)
{
    $this->rubrica = $strRubrica;
}

}

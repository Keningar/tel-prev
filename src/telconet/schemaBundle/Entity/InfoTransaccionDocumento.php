<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoTransaccionDocumento
 *
 * @ORM\Table(name="INFO_TRANSACCION_DOCUMENTO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoTransaccionDocumentoRepository")
 */
class InfoTransaccionDocumento
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_TRANSACCION_DOCUMENTO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_TRANSACION_DOCUMENTO", allocationSize=1, initialValue=1)
*/

private $id;

/**
* @var number $transaccionId
*
* @ORM\Column(name="TRANSACCION_ID", type="number", nullable=false)
*/

private $transaccionId;

/**
* @var string $src
*
* @ORM\Column(name="SRC", type="string", nullable=false)
*/

private $src;

/**
* @var number $empresaPlantillaId
*
* @ORM\Column(name="EMPRESA_PLANTILLA_ID", type="string", nullable=false)
*/

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
* Get transaccionId
*
* @return number
*/

public function getTransaccionId()
{
    return $this->transaccionId; 
}

/**
* Set transaccionId
*
* @param number $intTransaccionId
*/
public function setTransaccionId($intTransaccionId)
{
    $this->transaccionId = $intTransaccionId;
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
* Get empresaPlantillaId
*
* @return number
*/

public function getEmpresaPlantillaId()
{
    return $this->empresaPlantillaId; 
}

/**
* Set empresaPlantillaId
*
* @param number $intEmpresaPlantillaId
*/
public function setEmpresaPlantillaId($intEmpresaPlantillaId)
{
    $this->empresaPlantillaId = $intEmpresaPlantillaId;
}

}

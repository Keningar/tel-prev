<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmEmpresaPlantilla
 *
 * @ORM\Table(name="ADM_EMPRESA_PLANTILLA")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmEmpresaPlantillaRepository")
 */
class AdmEmpresaPlantilla
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_EMPRESA_PLANTILLA", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADM_EMPRESA_PLANTILLA", allocationSize=1, initialValue=1)
*/

private $id;

/**
* @var string $codPlantilla
*
* @ORM\Column(name="COD_PLANTILLA", type="string", nullable=false)
*/

private $codPlantilla;

/**
* @var integer $empresaId
*
* @ORM\Column(name="EMPRESA_ID", type="integer", nullable=false)
*/
     
private $empresaId;

/**
* @var string $descripcion
*
* @ORM\Column(name="DESCRIPCION", type="string", nullable=false)
*/

private $descripcion;

/**
* @var string $html
*
* @ORM\Column(name="HTML", type="string", nullable=false)
*/

private $html;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=false)
*/
private $estado;

/**
* @var string $propiedades
*
* @ORM\Column(name="PROPIEDADES", type="string", nullable=false)
*/
private $propiedades;

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
* Get codPlantilla
*
* @return string
*/

public function getCodPlantilla()
{
    return $this->codPlantilla; 
}

/**
* Set codPlantilla
*
* @param string $strCodPlantilla
*/
public function setCodPlantilla($strCodPlantilla)
{
    $this->codPlantilla = $strCodPlantilla;
}

/**
* Get empresaId
*
* @return integer
*/

public function getEmpresaId()
{
    return $this->empresaId; 
}

/**
* Set empresaId
*
* @param integer $intEmpresaId
*/
public function setEmpresaId($intEmpresaId)
{
    $this->empresaId = $intEmpresaId;
}

/**
* Get descripcion
*
* @return string
*/

public function getDescripcion()
{
    return $this->descripcion; 
}

/**
* Set descripcion
*
* @param string $strDescripcion
*/
public function setDescripcion($strDescripcion)
{
    $this->descripcion = $strDescripcion;
}

/**
* Get html
*
* @return string
*/

public function getHtml()
{
    return $this->html; 
}

/**
* Set html
*
* @param string $strHtml
*/
public function setTipo($strHtml)
{
    $this->html = $strHtml;
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
* Get propiedades
*
* @return string
*/

public function getPropiedades()
{
    return $this->propiedades; 
}

/**
* Set propiedades
*
* @param string $strPropiedades
*/
public function setPropiedades($strPropiedades)
{
    $this->propiedades = $strPropiedades;
}

}

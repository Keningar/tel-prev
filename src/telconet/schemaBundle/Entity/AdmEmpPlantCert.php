<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmEmpPlantCert
 *
 * @ORM\Table(name="ADM_EMP_PLANT_CERT")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmEmpPlantCertRepository")
 */
class AdmEmpPlantCert
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_EMP_PLANT_CERT", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADM_EMP_PLANT_CERT", allocationSize=1, initialValue=1)
*/

private $id;

/**
* @var number $plantillaId
*
* @ORM\Column(name="PLANTILLA_ID", type="integer", nullable=false)
*/

private $plantillaId;

/**
* @var number $certificadoId
*
* @ORM\Column(name="CERTIFICADO_ID", type="integer", nullable=false)
*/
     
private $certificadoId;

/**
* @var string $propiedades
*
* @ORM\Column(name="PROPIEDADES", type="string", nullable=false)
*/

private $propiedades;

/**
* @var string $tipo
*
* @ORM\Column(name="TIPO", type="string", nullable=false)
*/
     
private $tipo;

/**
* @var string $codigo
*
* @ORM\Column(name="CODIGO", type="string", nullable=false)
*/
     
private $codigo;

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
* Get plantillaId
*
* @return number
*/

public function getPlantillaId()
{
    return $this->plantillaId; 
}

/**
* Set plantillaId
*
* @param number $intPlantillaId
*/
public function setPlantillaId($intPlantillaId)
{
    $this->plantillaId = $intPlantillaId;
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
    $this->certificadoId = $intCertificadoId;
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

/**
* Get tipo
*
* @return string
*/

public function getTipo()
{
    return $this->tipo; 
}

/**
* Set tipo
*
* @param string $strTipo
*/
public function setTipo($strTipo)
{
    $this->tipo = $strTipo;
}

/**
* Get codigo
*
* @return string
*/

public function getCodigo()
{
    return $this->codigo; 
}

/**
* Set codigo
*
* @param string $strCodigo
*/
public function setCodigo($strCodigo)
{
    $this->codigo = $strCodigo;
}

}

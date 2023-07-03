<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmEmpresa
 *
 * @ORM\Table(name="ADM_EMPRESA")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmEmpresaRepository")
 */
class AdmEmpresa
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_EMPRESA", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADM_EMPRESA", allocationSize=1, initialValue=1)
*/

private $id;

/**
* @var string $nombre
*
* @ORM\Column(name="NOMBRE", type="string", nullable=false)
*/

private $nombre;

/**
* @var string $razonSocial
*
* @ORM\Column(name="RAZON_SOCIAL", type="string", nullable=false)
*/

private $razonSocial;

/**
* @var string $ruc
*
* @ORM\Column(name="RUC", type="string", nullable=false)
*/

private $ruc;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=false)
*/

private $estado;

/**
* @var string $referenciaEmpresa
*
* @ORM\Column(name="REFERENCIA_EMPRESA", type="string", nullable=false)
*/

private $referenciaEmpresa;

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
* Get nombre
*
* @return string
*/

public function getNombre()
{
    return $this->nombre; 
}

/**
* Set nombre
*
* @param string $strNombre
*/
public function setNombre($strNombre)
{
    $this->nombre = $strNombre;
}

/**
* Get razonSocial
*
* @return string
*/

public function getRazonSocial()
{
    return $this->razonSocial; 
}

/**
* Set razonSocial
*
* @param string $strRazonSocial
*/
public function setRazonSocial($strRazonSocial)
{
    $this->razonSocial = $strRazonSocial;
}

/**
* Get ruc
*
* @return string
*/

public function getRuc()
{
    return $this->ruc; 
}

/**
* Set ruc
*
* @param string $strRuc
*/
public function setRuc($strRuc)
{
    $this->ruc = $strRuc;
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
* Get referenciaEmpresa
*
* @return string
*/

public function getReferenciaEmpresa()
{
    return $this->referenciaEmpresa; 
}

/**
* Set referenciaEmpresa
*
* @param string $strReferenciaEmpresa
*/
public function setReferenciaEmpresa($strReferenciaEmpresa)
{
    $this->referenciaEmpresa = $strReferenciaEmpresa;
}

}

<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\VArticulosEmpresas
 *
 * @ORM\Table(name="V_ARTICULOS_EMPRESAS")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\VArticulosEmpresasRepository")
 */
class VArticulosEmpresas
{


/**
* @var string $id
*
* @ORM\Column(name="NO_ARTI", type="string", nullable=false)
* @ORM\Id
*/		
		
private $id;	

/**
* @var string $noCia
*
* @ORM\Column(name="NO_CIA", type="string", nullable=false)
*/		
		
private $noCia;	
	
/**
* @var string $descripcion
*
* @ORM\Column(name="DESCRIPCION", type="string", nullable=false)
*/		
     		
private $descripcion;
	
/**
* @var string $unidad
*
* @ORM\Column(name="UNIDAD", type="string", nullable=false)
*/		
     		
private $unidad;
	
/**
* @var string $nom
*
* @ORM\Column(name="NOM", type="string", nullable=false)
*/		
     		
private $nom;
	
/**
* @var string $codigo
*
* @ORM\Column(name="CODIGO", type="string", nullable=false)
*/		
     		
private $codigo;
	
/**
* @var string $modelo
*
* @ORM\Column(name="MODELO", type="string", nullable=false)
*/		
     		
private $modelo;
	
/**
* @var string $subgrupo
*
* @ORM\Column(name="SUBGRUPO", type="string", nullable=false)
*/		
     		
private $subgrupo;
	
/**
* @var string $nombre_marca
*
* @ORM\Column(name="NOMBRE_MARCA", type="string", nullable=false)
*/		
     		
private $nombre_marca;
	
/**
* @var string $marca
*
* @ORM\Column(name="MARCA", type="string", nullable=false)
*/		
     		
private $marca;
	
/**
* @var string $costo_unitario
*
* @ORM\Column(name="COSTO_UNITARIO", type="string", nullable=false)
*/		
     		
private $costo_unitario;
	
/**
* @var string $ultimo_costo
*
* @ORM\Column(name="ULTIMO_COSTO", type="string", nullable=false)
*/		
     		
private $ultimo_costo;
	
/**
* @var string $costo2_unitario
*
* @ORM\Column(name="COSTO2_UNITARIO", type="string", nullable=false)
*/		
     		
private $costo2_unitario;
	
/**
* @var string $ultimo_costo2
*
* @ORM\Column(name="ULTIMO_COSTO2", type="string", nullable=false)
*/		
     		
private $ultimo_costo2;	

/**
* @var string $precio_base
*
* @ORM\Column(name="PRECIO_BASE", type="string", nullable=false)
*/		
     		
private $precio_base;

/**
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Get noCia
*
* @return string
*/		
     		
public function getNoCia(){
	return $this->noCia; 
}

/**
* Set noCia
*
* @param string $noCia
*/
public function setNoCia($noCia)
{
        $this->noCia = $noCia;
}

/**
* Get descripcion
*
* @return string
*/		
     		
public function getDescripcion(){
	return $this->descripcion; 
}

/**
* Set descripcion
*
* @param string $descripcion
*/
public function setDescripcion($descripcion)
{
        $this->descripcion = $descripcion;
}

/**
* Get unidad
*
* @return string
*/		
     		
public function getUnidad(){
	return $this->unidad; 
}

/**
* Set unidad
*
* @param string $unidad
*/
public function setUnidad($unidad)
{
        $this->unidad = $unidad;
}

/**
* Get nom
*
* @return string
*/		
     		
public function getNom(){
	return $this->nom; 
}

/**
* Set nom
*
* @param string $nom
*/
public function setNom($nom)
{
        $this->nom = $nom;
}

/**
* Get codigo
*
* @return string
*/		
     		
public function getCodigo(){
	return $this->codigo; 
}

/**
* Set codigo
*
* @param string $codigo
*/
public function setCodigo($codigo)
{
        $this->codigo = $codigo;
}

/**
* Get modelo
*
* @return string
*/		
     		
public function getModelo(){
	return $this->modelo; 
}

/**
* Set modelo
*
* @param string $modelo
*/
public function setModelo($modelo)
{
        $this->modelo = $modelo;
}

/**
* Get subgrupo
*
* @return string
*/		
     		
public function getSubgrupo(){
	return $this->subgrupo; 
}

/**
* Set subgrupo
*
* @param string $subgrupo
*/
public function setSubgrupo($subgrupo)
{
        $this->subgrupo = $subgrupo;
}
/**
* Get nombre_marca
*
* @return string
*/		
     		
public function getNombreMarca(){
	return $this->nombre_marca; 
}

/**
* Set nombre_marca
*
* @param string $nombre_marca
*/
public function setNombreMarca($nombre_marca)
{
        $this->nombre_marca = $nombre_marca;
}

/**
* Get marca
*
* @return string
*/		
     		
public function getMarca(){
	return $this->marca; 
}

/**
* Set marca
*
* @param string $marca
*/
public function setMarca($marca)
{
        $this->marca = $marca;
}

/**
* Get costo_unitario
*
* @return string
*/		
     		
public function getCostoUnitario(){
	return $this->costo_unitario; 
}

/**
* Set costo_unitario
*
* @param string $costo_unitario
*/
public function setCostoUnitario($costo_unitario)
{
        $this->costo_unitario = $costo_unitario;
}

/**
* Get ultimo_costo
*
* @return string
*/		
     		
public function getUltimoCosto(){
	return $this->ultimo_costo; 
}

/**
* Set ultimo_costo
*
* @param string $ultimo_costo
*/
public function setUltimoCosto($ultimo_costo)
{
        $this->ultimo_costo = $ultimo_costo;
}

/**
* Get costo2_unitario
*
* @return string
*/		
     		
public function getCosto2Unitario(){
	return $this->costo2_unitario; 
}

/**
* Set costo2_unitario
*
* @param string $costo2_unitario
*/
public function setCosto2Unitario($costo2_unitario)
{
        $this->costo2_unitario = $costo2_unitario;
}

/**
* Get ultimo_costo2
*
* @return string
*/		
     		
public function getUltimoCosto2(){
	return $this->ultimo_costo2; 
}

/**
* Set ultimo_costo2
*
* @param string $ultimo_costo2
*/
public function setUltimoCosto2($ultimo_costo2)
{
        $this->ultimo_costo2 = $ultimo_costo2;
}

/**
* Get precio_base
*
* @return string
*/		
     		
public function getPrecioBase(){
	return $this->precio_base; 
}

/**
* Set precio_base
*
* @param string $precio_base
*/
public function setPrecioBase($precio_base)
{
        $this->precio_base = $precio_base;
}


public function __toString()
{
        return $this->descripcion;
}

}
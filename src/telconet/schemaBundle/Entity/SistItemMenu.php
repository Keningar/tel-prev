<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\SistItemMenu
 *
 * @ORM\Table(name="SIST_ITEM_MENU")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\SistItemMenuRepository")
 */
class SistItemMenu
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_ITEM_MENU", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_SIST_ITEM_MENU", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var SistItemMenu
*
* @ORM\ManyToOne(targetEntity="SistItemMenu")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="ITEM_MENU_ID", referencedColumnName="ID_ITEM_MENU")
* })
*/
		
private $itemMenuId;

/**
* @var string $nombreItemMenu
*
* @ORM\Column(name="NOMBRE_ITEM_MENU", type="string", nullable=true)
*/		
     		
private $nombreItemMenu;

/**
* @var string $descripcionItemMenu
*
* @ORM\Column(name="DESCRIPCION_ITEM_MENU", type="string", nullable=true)
*/		
     		
private $descripcionItemMenu;

/**
* @var string $titleHtml
*
* @ORM\Column(name="TITLE_HTML", type="string", nullable=true)
*/		
     		
private $titleHtml;

/**
* @var string $descripcionHtml
*
* @ORM\Column(name="DESCRIPCION_HTML", type="string", nullable=true)
*/		
     		
private $descripcionHtml;

/**
* @var string $urlImagen
*
* @ORM\Column(name="URL_IMAGEN", type="string", nullable=true)
*/		
     		
private $urlImagen;

/**
* @var integer $posicion
*
* @ORM\Column(name="POSICION", type="integer", nullable=true)
*/		
     		
private $posicion;

/**
* @var string $html
*
* @ORM\Column(name="HTML", type="string", nullable=true)
*/		
     		
private $html;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=true)
*/		
     		
private $estado;

/**
* @var integer $codigo
*
* @ORM\Column(name="CODIGO", type="integer", nullable=true)
*/		
     		
private $codigo;

/**
* @var string $usrCreacion
*
* @ORM\Column(name="USR_CREACION", type="string", nullable=true)
*/		
     		
private $usrCreacion;

/**
* @var datetime $feCreacion
*
* @ORM\Column(name="FE_CREACION", type="datetime", nullable=true)
*/		
     		
private $feCreacion;

/**
* @var string $usrUltMod
*
* @ORM\Column(name="USR_ULT_MOD", type="string", nullable=true)
*/		
     		
private $usrUltMod;

/**
* @var datetime $feUltMod
*
* @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=true)
*/		
     		
private $feUltMod;

/**
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Get itemMenuId
*
* @return telconet\schemaBundle\Entity\SistItemMenu
*/		
     		
public function getItemMenuId(){
	return $this->itemMenuId; 
}

/**
* Set itemMenuId
*
* @param integer $itemMenuId
*/
public function setItemMenuId(SistItemMenu $itemMenuId)
{
        $this->itemMenuId = $itemMenuId;
}


/**
* Get nombreItemMenu
*
* @return string
*/		
     		
public function getNombreItemMenu(){
	return $this->nombreItemMenu; 
}

/**
* Set nombreItemMenu
*
* @param string $nombreItemMenu
*/
public function setNombreItemMenu($nombreItemMenu)
{
        $this->nombreItemMenu = $nombreItemMenu;
}


/**
* Get titleHtml
*
* @return string
*/		
     		
public function getTitleHtml(){
	return $this->titleHtml; 
}

/**
* Set titleHtml
*
* @param string $titleHtml
*/
public function setTitleHtml($titleHtml)
{
        $this->titleHtml = $titleHtml;
}


/**
* Get descripcionHtml
*
* @return string
*/		
     		
public function getDescripcionHtml(){
	return $this->descripcionHtml; 
}

/**
* Set descripcionHtml
*
* @param string $descripcionHtml
*/
public function setDescripcionHtml($descripcionHtml)
{
        $this->descripcionHtml = $descripcionHtml;
}

/**
* Get descripcionItemMenu
*
* @return string
*/		
     		
public function getDescripcionItemMenu(){
	return $this->descripcionItemMenu; 
}

/**
* Set descripcionItemMenu
*
* @param string $descripcionItemMenu
*/
public function setDescripcionItemMenu($descripcionItemMenu)
{
        $this->descripcionItemMenu = $descripcionItemMenu;
}

/**
* Get urlImagen
*
* @return string
*/		
     		
public function getUrlImagen(){
	return $this->urlImagen; 
}

/**
* Set urlImagen
*
* @param string $urlImagen
*/
public function setUrlImagen($urlImagen)
{
        $this->urlImagen = $urlImagen;
}


/**
* Get posicion
*
* @return integer
*/		
     		
public function getPosicion(){
	return $this->posicion; 
}

/**
* Set posicion
*
* @param integer $posicion
*/
public function setPosicion($posicion)
{
        $this->posicion = $posicion;
}


/**
* Get html
*
* @return string
*/		
     		
public function getHtml(){
	return $this->html; 
}

/**
* Set html
*
* @param string $html
*/
public function setHtml($html)
{
        $this->html = $html;
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
* Get codigo
*
* @return integer
*/		
     		
public function getCodigo(){
	return $this->codigo; 
}

/**
* Set codigo
*
* @param integer $codigo
*/
public function setCodigo($codigo)
{
        $this->codigo = $codigo;
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
* Get usrUltMod
*
* @return string
*/		
     		
public function getUsrUltMod(){
	return $this->usrUltMod; 
}

/**
* Set usrUltMod
*
* @param string $usrUltMod
*/
public function setUsrUltMod($usrUltMod)
{
        $this->usrUltMod = $usrUltMod;
}


/**
* Get feUltMod
*
* @return datetime
*/		
     		
public function getFeUltMod(){
	return $this->feUltMod; 
}

/**
* Set feUltMod
*
* @param datetime $feUltMod
*/
public function setFeUltMod($feUltMod)
{
        $this->feUltMod = $feUltMod;
}

public function __toString()
{
        return $this->nombreItemMenu.'-'.$this->descripcionItemMenu;
}

    /**
    * @ORM\OneToMany(targetEntity="SeguRelacionSistema", mappedBy="itemMenuId")
    */
    private $relacion_itenmenu;
    public function __construct()
    {
        $this->relacion_itenmenu = new \Doctrine\Common\Collections\ArrayCollection();
    }
    public function addRelacionItenmenu(\telconet\schemaBundle\Entity\SeguRelacionSistema $relacion_itenmenu)
    {
        $this->relacion_itenmenu[] = $relacion_itenmenu;
    }

    public function getRelacionItenmenu()
    {
        return $this->relacion_itenmenu;
    }
    
}
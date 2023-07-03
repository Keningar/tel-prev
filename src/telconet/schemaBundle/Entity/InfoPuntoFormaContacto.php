<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoPuntoFormaContacto
 *
 * @ORM\Table(name="INFO_PUNTO_FORMA_CONTACTO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoPuntoFormaContactoRepository")
 */
class InfoPuntoFormaContacto
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_PUNTO_FORMA_CONTACTO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_PUNTO_FORMA_CONTACTO", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var InfoPunto
*
* @ORM\ManyToOne(targetEntity="InfoPunto")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="PUNTO_ID", referencedColumnName="ID_PUNTO")
* })
*/
		
private $puntoId;

/**
* @var AdmiFormaContacto
*
* @ORM\ManyToOne(targetEntity="AdmiFormaContacto")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="FORMA_CONTACTO_ID", referencedColumnName="ID_FORMA_CONTACTO")
* })
*/
		
private $formaContactoId;

/**
* @var string $valor
*
* @ORM\Column(name="VALOR", type="string", nullable=true)
*/		
     		
private $valor;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=true)
*/		
     		
private $estado;

/**
* @var datetime $feCreacion
*
* @ORM\Column(name="FE_CREACION", type="datetime", nullable=false)
*/		
     		
private $feCreacion;


/**
* @var string $usrCreacion
*
* @ORM\Column(name="USR_CREACION", type="string", nullable=false)
*/		
     		
private $usrCreacion;



/**
* @var string $ipCreacion
*
* @ORM\Column(name="IP_CREACION", type="string", nullable=true)
*/		
     		
private $ipCreacion;

/**
 * @var string $estadoWs
 *
 * @ORM\Column(name="ESTADO_WS", type="string", nullable=true)
 */
private $estadoWs;

/**
 * Get estadoWs
 *
 * @return string
 */
public function getEstadoWs(){
    return $this->estadoWs;
}

/**
 * Set estadoWs
 *
 * @param string $estadoWs
 */
public function setEstadoWs($estadoWs)
{
    $this->estadoWs = $estadoWs;
}

/**
 * @var datetime $feCreacionWs
 *
 * @ORM\Column(name="FE_CREACION_WS", type="datetime", nullable=true)
 */
private $feCreacionWs;

/**
 * Get feCreacionWs
 *
 * @return datetime
 */
public function getFeCreacionWs(){
    return $this->feCreacionWs;
}

/**
 * Set feCreacionWs
 *
 * @param datetime $feCreacionWs
 */
public function setFeCreacionWs($feCreacionWs)
{
    $this->feCreacionWs = $feCreacionWs;
}

/**
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Get puntoId
*
* @return telconet\schemaBundle\Entity\InfoPunto
*/		
     		
public function getPuntoId(){
	return $this->puntoId; 
}

/**
* Set puntoId
*
* @param telconet\schemaBundle\Entity\InfoPunto $puntoId
*/
public function setPuntoId(\telconet\schemaBundle\Entity\InfoPunto $puntoId)
{
        $this->puntoId = $puntoId;
}


/**
* Get formaContactoId
*
* @return telconet\schemaBundle\Entity\AdmiFormaContacto
*/		
     		
public function getFormaContactoId(){
	return $this->formaContactoId; 
}

/**
* Set formaContactoId
*
* @param telconet\schemaBundle\Entity\AdmiFormaContacto $formaContactoId
*/
public function setFormaContactoId(\telconet\schemaBundle\Entity\AdmiFormaContacto $formaContactoId)
{
        $this->formaContactoId = $formaContactoId;
}


/**
* Get valor
*
* @return string
*/		
     		
public function getValor(){
	return $this->valor; 
}

/**
* Set valor
*
* @param string $valor
*/
public function setValor($valor)
{
        $this->valor = $valor;
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
* Get ipCreacion
*
* @return string
*/		
     		
public function getIpCreacion(){
	return $this->ipCreacion; 
}

/**
* Set ipCreacion
*
* @param string $ipCreacion
*/
public function setIpCreacion($ipCreacion)
{
        $this->ipCreacion = $ipCreacion;
}

}
<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiModeloTecnologia
 *
 * @ORM\Table(name="ADMI_MODELO_TECNOLOGIA")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiModeloTecnologiaRepository")
 */
class AdmiModeloTecnologia
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_MODELO_TECNOLOGIA", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_MODELO_TECNOLOGIA", allocationSize=1, initialValue=1)
*/		
		
private $id;	

/**
* @var AdmiModeloElemento
*
* @ORM\ManyToOne(targetEntity="AdmiModeloElemento")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="MODELO_ELEMENTO_ID", referencedColumnName="ID_MODELO_ELEMENTO")
* })
*/		
     		
private $modeloElementoId;

/**
* @var AdmiTecnologia
*
* @ORM\ManyToOne(targetEntity="AdmiTecnologia")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="TECNOLOGIA_ID", referencedColumnName="ID_TECNOLOGIA")
* })
*/		
     		
private $protocoloId;

/**
* @var string $esPreferido
*
* @ORM\Column(name="ES_PREFERIDO", type="string", nullable=false)
*/		
     		
private $esPreferido;

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
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Get modeloElementoId
*
* @return telconet\schemaBundle\Entity\AdmiModeloElemento
*/		
     		
public function getModeloElementoId(){
	return $this->modeloElementoId; 
}

/**
* Set modeloElementoId
*
* @param telconet\schemaBundle\Entity\AdmiModeloElemento $modeloElementoId
*/
public function setModeloElementoId(\telconet\schemaBundle\Entity\AdmiModeloElemento $modeloElementoId)
{
        $this->modeloElementoId = $modeloElementoId;
}

/**
* Get esPreferido
*
* @return string
*/		
     		
public function getEsPreferido(){
	return $this->esPreferido; 
}

/**
* Set esPreferido
*
* @param string $esPreferido
*/
public function setEsPreferido($esPreferido)
{
        $this->esPreferido = $esPreferido;
}

/**
* Get protocoloId
*
* @return telconet\schemaBundle\Entity\AdmiTecnologia
*/		
     		
public function getTecnologiaId(){
	return $this->protocoloId; 
}

/**
* Set protocoloId
*
* @param telconet\schemaBundle\Entity\AdmiTecnologia $protocoloId
*/
public function setTecnologiaId(\telconet\schemaBundle\Entity\AdmiTecnologia $protocoloId)
{
        $this->protocoloId = $protocoloId;
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
    return $this->id;
}

}
<?php
namespace telconet\schemaBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
 * telconet\schemaBundle\Entity\InfoAdendumCaracteristica
 *
 * @ORM\Table(name="INFO_ADENDUM_CARACTERISTICA")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoAdendumCaracteristicaRepository")
 */
class InfoAdendumCaracteristica
{

    /**
    * @var integer $id
    *
    * @ORM\Column(name="ID_ADENDUM_CARACTERISTICA", type="integer", nullable=false)
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="SEQUENCE")
    * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_ADENDUM_CARAC", allocationSize=1, initialValue=1)
    */
    private $id;
    /**
    * @var number $adendumId
    *
    * @ORM\Column(name="ADENDUM_ID", type="integer", nullable=false)
    */
    private $adendumId;
    /**
    * @var number $caractiristicaId
    *
    * @ORM\Column(name="CARACTERISTICA_ID", type="integer", nullable=false)
    */
    private $caracteristicaId;
    /**
    * @var string $valor1
    *
    * @ORM\Column(name="VALOR1", type="string", nullable=false)
    */

    private $valor1;
    /**
    * @var string $valor2
    *
    * @ORM\Column(name="VALOR2", type="string", nullable=false)
    */
    private $valor2;
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
    * @var string $ipCreacion
    *
    * @ORM\Column(name="IP_CREACION", type="string", nullable=false)
    */
    private $IP_CREACION;
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

    public function getId()
    {
        return $this->id; 
    }

     /**
    * Get AdendumId
    *
    * @return \telconet\schemaBundle\Entity\AdmiCaracteristica
    */
    public function getAdendumId()
    {
        return $this->adendumId; 
    }

    /**
    * Set AdendumId
    *
    * @param telconet\schemaBundle\Entity\InfoAdendum $caracteristicaId
    */
    public function setAdendumId(\telconet\schemaBundle\Entity\InfoAdendum $adendumId)
    {
        $this->adendumId = $adendumId;
    }


    /**
    * Get caracteristicaId
    *
    * @return \telconet\schemaBundle\Entity\AdmiCaracteristica
    */
    public function getCaracteristicaId()
    {
        return $this->caracteristicaId; 
    }

    /**
    * Set caracteristicaId
    *
    * @param telconet\schemaBundle\Entity\AdmiCaracteristica $caracteristicaId
    */
    public function setCaracteristicaId(\telconet\schemaBundle\Entity\AdmiCaracteristica $caracteristicaId)
    {
        $this->caracteristicaId = $caracteristicaId;
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
    * Get valor2
    *
    * @return string
    */

    public function getValor2()
    {
        return $this->valor2; 
    }

    /**
    * Set valor2
    *
    * @param string $strValor2
    */
    public function setValor2($strValor2)
    {
        $this->valor2 = $strValor2;
    }

    /**
    * Get valor1
    *
    * @return string
    */

    public function getValor1()
    {
        return $this->valor1; 
    }

    /**
    * Set valor1
    *
    * @param string $strValor1
    */
    public function setValor1($strValor1)
    {
        $this->valor1 = $strValor1;
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



}
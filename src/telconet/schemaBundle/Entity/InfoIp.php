<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoIp
 *
 * @ORM\Table(name="INFO_IP")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoIpRepository")
 */
class InfoIp
{


    /**
    * @var integer $id
    *
    * @ORM\Column(name="ID_IP", type="integer", nullable=false)
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="SEQUENCE")
    * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_IP", allocationSize=1, initialValue=1)
    */		

    private $id;	

    /**
    * @var integer $elementoId
    *
    * @ORM\Column(name="ELEMENTO_ID", type="integer", nullable=true)
    */

    private $elementoId;
    
    /**
    * @var integer $subredId
    *
    * @ORM\Column(name="SUBRED_ID", type="integer", nullable=true)
    */

    private $subredId;

    /**
    * @var string $ip
    *
    * @ORM\Column(name="IP", type="string", nullable=false)
    */		

    private $ip;

    /**
    * @var string $mascara
    *
    * @ORM\Column(name="MASCARA", type="string", nullable=false)
    */		

    private $mascara;

    /**
    * @var string $gateway
    *
    * @ORM\Column(name="GATEWAY", type="string", nullable=false)
    */		

    private $gateway;

    /**
    * @var string $versionIp
    *
    * @ORM\Column(name="VERSION_IP", type="string", nullable=false)
    */		

    private $versionIp;

    /**
    * @var string $tipoIp
    *
    * @ORM\Column(name="TIPO_IP", type="string", nullable=true)
    */		

    private $tipoIp;
    
    /**
    * @var string $estado
    *
    * @ORM\Column(name="ESTADO", type="string", nullable=true)
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

    private $ipCreacion;	

    
    /**
    * @var integer $interfaceElementoId
    *
    * @ORM\Column(name="INTERFACE_ELEMENTO_ID", type="integer", nullable=true)
    */
    private $interfaceElementoId;
	
    
    /**
    * @var integer $servicioId
    *
    * @ORM\Column(name="SERVICIO_ID", type="integer", nullable=true)
    */

    private $servicioId;
    
    
    /**
    * @var integer $refIpId
    *
    * @ORM\Column(name="REF_IP_ID", type="integer", nullable=true)
    */
    private $refIpId;
    
    
    /**
    * Get id
    *
    * @return integer
    */	

    public function getId(){
            return $this->id; 
    }

    /**
    * Get elementoId
    *
    * @return integer
    */		

    public function getElementoId(){
            return $this->elementoId; 
    }

    /**
    * Set elementoId
    *
    * @param integer $elementoId
    */
    public function setElementoId($elementoId)
    {
            $this->elementoId = $elementoId;
    }
    
    /**
    * Get servicioId
    *
    * @return integer
    */		

    public function getServicioId(){
            return $this->servicioId; 
    }

    /**
    * Set servicioId
    *
    * @param integer $servicioId
    */
    public function setServicioId($servicioId)
    {
            $this->servicioId = $servicioId;
    }
    
    /**
    * Get subredId
    *
    * @return integer
    */		

    public function getSubredId(){
            return $this->subredId; 
    }

    /**
    * Set subredId
    *
    * @param integer $subredId
    */
    public function setSubredId($subredId)
    {
            $this->subredId = $subredId;
    }


    /**
    * Get ip
    *
    * @return string
    */		

    public function getIp(){
            return $this->ip; 
    }

    /**
    * Set ip
    *
    * @param string $ip
    */
    public function setIp($ip)
    {
            $this->ip= $ip;
    }
    
    /**
    * Get mascara
    *
    * @return string
    */		

    public function getMascara(){
            return $this->mascara; 
    }

    /**
    * Set mascara
    *
    * @param string $mascara
    */
    public function setMascara($mascara)
    {
            $this->mascara= $mascara;
    }

    /**
    * Get gateway
    *
    * @return string
    */		

    public function getGateway(){
            return $this->gateway; 
    }

    /**
    * Set gateway
    *
    * @param string $gateway
    */
    public function setGateway($gateway)
    {
            $this->gateway= $gateway;
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
            $this->estado= $estado;
    }
    
    /**
    * Get versionIp
    *
    * @return string
    */		

    public function getVersionIp(){
            return $this->versionIp; 
    }

    /**
    * Set versionIp
    *
    * @param string $versionIp
    */
    public function setVersionIp($versionIp)
    {
            $this->versionIp= $versionIp;
    }

    /**
    * Get tipoIp
    *
    * @return string
    */		

    public function getTipoIp(){
            return $this->tipoIp; 
    }

    /**
    * Set tipoIp
    *
    * @param string $tipoIp
    */
    public function setTipoIp($tipoIp)
    {
            $this->tipoIp = $tipoIp;
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

    
    /**
     * Get interfaceElementoId
     *
     * @return integer
     */		
    public function getInterfaceElementoId()
    {
        return $this->interfaceElementoId; 
    }

    /**
     * Set interfaceElementoId
     *
     * @param integer $interfaceElementoId
     */
    public function setInterfaceElementoId($interfaceElementoId)
    {
        $this->interfaceElementoId = $interfaceElementoId;
    }
    
    
    /**
    * Get refIpId
    *
    * @return integer
    */		

    public function getRefIpId(){
            return $this->refIpId; 
    }

    /**
    * Set refIpId
    *
    * @param integer $refIpId
    */
    public function setRefIpId($refIpId)
    {
            $this->refIpId = $refIpId;
    }

}
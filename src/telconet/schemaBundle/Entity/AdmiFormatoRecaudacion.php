<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiFormatoRecaudacion
 *
 * @ORM\Table(name="ADMI_FORMATO_RECAUDACION")
 * @ORM\Entity
 */
class AdmiFormatoRecaudacion
{
    
    /**
     *
     * @var integer $id
     * @ORM\Column(name="ID_FORMATO_RECAUDACION", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_FORMATO_RECAUDACION", allocationSize=1, initialValue=1)
     */
    private $id;
    
    /**
     *
     * @var string $descripcion
     * @ORM\Column(name="DESCRIPCION", type="string", nullable=true)
     */
    private $descripcion;
    
    /**
     *
     * @var string $tipoCampo
     * @ORM\Column(name="TIPO_CAMPO", type="string", nullable=false)
     */
    private $tipoCampo; 
    
    /**
     *
     * @var string $contenido
     * @ORM\Column(name="CONTENIDO", type="string", nullable=true)
     */
    private $contenido;     
    
    /**
     *
     * @var integer $longitud
     *      @ORM\Column(name="LONGITUD", type="integer", nullable=false)
     */
    private $longitud;

    /**
     *
     * @var string $caracterRelleno
     * @ORM\Column(name="CARACTER_RELLENO", type="string", nullable=true)
     */
    private $caracterRelleno;
    
    /**
     *
     * @var string $estado
     * @ORM\Column(name="ESTADO", type="string", nullable=false)
     */
    private $estado;
    
    /**
     *
     * @var integer $canalRecaudacionId
     * @ORM\Column(name="CANAL_RECAUDACION_ID", type="integer", nullable=false)
     */
    private $canalRecaudacionId;
    
    /**
     *
     * @var string $tipoDato
     * @ORM\Column(name="TIPO_DATO", type="string", nullable=true)
     *     
     */
    private $tipoDato;    
    
    /**
     *
     * @var string $orientacionCaracterRelleno
     * @ORM\Column(name="ORIENTACION_CARACTER_RELLENO", type="string", nullable=true)
     */
    private $orientacionCaracterRelleno;
    
    /**
     *
     * @var string $empresaCod
     * @ORM\Column(name="EMPRESA_COD", type="string", nullable=false)
     *     
     */
    private $empresaCod;
    
    /**
     *
     * @var integer $longitudTotal
     * @ORM\Column(name="LONGITUD_TOTAL", type="integer", nullable=true)
     */
    private $longitudTotal;  
    
    /**
     *
     * @var string $esCabecera
     * @ORM\Column(name="ES_CABECERA", type="string", nullable=false)
     */
    private $esCabecera;    
    
    /**
     *
     * @var \DateTime $feCreacion
     * @ORM\Column(name="FE_CREACION", type="datetime", nullable=false)
     */
    private $feCreacion;
    
    /**
     *
     * @var string $usrCreacion
     * @ORM\Column(name="USR_CREACION", type="string", nullable=false)
     */
    private $usrCreacion;
    
    /**
     *
     * @var string $ipCreacion
     * @ORM\Column(name="IP_CREACION", type="string", nullable=true)
     */
    private $ipCreacion;    
 
    /**
     *
     * @var \DateTime $feUltMod
     * @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=true)
     */
    private $feUltMod;    
    /**
     *
     * @var string $usrUltMod
     * @ORM\Column(name="USR_ULT_MOD", type="string", nullable=true)
     */
    private $usrUltMod;
    
    /**
     *
     * @var string $ipUltMod
     * @ORM\Column(name="IP_ULT_MOD", type="string", nullable=true)
     */
    private $ipUltMod;    

    /**
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @param integer $id            
     */
    public function setId($id)
    {
        $this->id = $id;
    }
    
    /**
     *
     * @return string
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     *
     * @param string $descripcion           
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    }
    
    /**
     *
     * @return string
     */
    public function getTipoCampo()
    {
        return $this->tipoCampo;
    }

    /**
     *
     * @param string $tipoCampo           
     */
    public function setTipoCampo($tipoCampo)
    {
        $this->tipoCampo = $tipoCampo;
    }    

    /**
     *
     * @return string
     */
    public function getContenido()
    {
        return $this->contenido;
    }

    /**
     *
     * @param string $contenido            
     */
    public function setContenido($contenido)
    {
        $this->contenido = $contenido;
    }
    
    /**
     *
     * @return integer
     */
    public function getLongitud()
    {
        return $this->longitud;
    }

    /**
     *
     * @param integer $longitud            
     */
    public function setLongitud($longitud)
    {
        $this->longitud = $longitud;
    }    
    
    /**
     *
     * @return string
     */
    public function getCaracterRelleno()
    {
        return $this->caracterRelleno;
    }

    /**
     *
     * @param string $caracterRelleno            
     */
    public function setCaracterRelleno($caracterRelleno)
    {
        $this->caracterRelleno = $caracterRelleno;
    }
    
    /**
     *
     * @return integer
     */
    public function getPosicion()
    {
        return $this->posicion;
    }

    /**
     *
     * @param integer $posicion            
     */
    public function setPosicion($posicion)
    {
        $this->posicion = $posicion;
    }
    
    /**
     *
     * @return string
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     *
     * @param string $estadoCanalRecaudacion            
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
    }
    
    /**
     *
     * @return integer
     */
    public function getCanalRecaudacionId()
    {
        return $this->canalRecaudacionId;
    }

    /**
     *
     * @param integer $canalRecaudacionId            
     */
    public function setCanalRecaudacionId($canalRecaudacionId)
    {
        $this->canalRecaudacionId = $canalRecaudacionId;
    }

    /**
     *
     * @return string
     */
    public function getTipoDato()
    {
        return $this->tipoDato;
    }

    /**
     *
     * @param string $tipoDato           
     */
    public function setTipoDato($tipoDato)
    {
        $this->tipoCampo = $tipoDato;
    }
    
    /**
     *
     * @return string
     */
    public function getOrientacionCaracterRelleno()
    {
        return $this->orientacionCaracterRelleno;
    }

    /**
     *
     * @param string $orientacionCaracterRelleno           
     */
    public function setOrientacionCaracterRelleno($orientacionCaracterRelleno)
    {
        $this->orientacionCaracterRelleno = $orientacionCaracterRelleno;
    }   
    
    /**
     *
     * @return string
     */
    public function getEmpresaCod()
    {
        return $this->empresaCod;
    }    
    
    /**
     *
     * @param string $empresaCod            
     */
    public function setEmpresaCod($empresaCod)
    {
        $this->empresaCod = $empresaCod;
    }
    
    /**
     *
     * @return integer
     */
    public function getLongitudTotal()
    {
        return $this->longitudTotal;
    }

    /**
     *
     * @param integer $longitudTotal            
     */
    public function setLongitudTotal($longitudTotal)
    {
        $this->longitudTotal = $longitudTotal;
    }     

    
    /**
     *
     * @return string
     */
    public function getEsCabecera()
    {
        return $this->esCabecera;
    }

    /**
     *
     * @param string $esCabecera            
     */
    public function setEsCabecera($esCabecera)
    {
        $this->esCabecera = $esCabecera;
    }
    
    /**
     *
     * @return string
     */
    public function getUsrCreacion()
    {
        return $this->usrCreacion;
    }

    /**
     *
     * @param string $usrCreacion            
     */
    public function setUsrCreacion($usrCreacion)
    {
        $this->usrCreacion = $usrCreacion;
    }

    /**
     *
     * @return \DateTime
     */
    public function getFeCreacion()
    {
        return $this->feCreacion;
    }

    /**
     *
     * @param \DateTime $feCreacion            
     */
    public function setFeCreacion($feCreacion)
    {
        $this->feCreacion = $feCreacion;
    }

    /**
     *
     * @return string
     */
    public function getIpCreacion()
    {
        return $this->ipCreacion;
    }

    /**
     *
     * @param string $ipCreacion            
     */
    public function setIpCreacion($ipCreacion)
    {
        $this->ipCreacion = $ipCreacion;
    }
    
    /**
     *
     * @return string
     */
    public function getUsrUltMod()
    {
        return $this->usrUltMod;
    }

    /**
     *
     * @param string $usrUltMod            
     */
    public function setUsrUltMod($usrUltMod)
    {
        $this->usrUltMod = $usrUltMod;
    }

    /**
     *
     * @return \DateTime
     */
    public function getFeUltMod()
    {
        return $this->feUltMod;
    }

    /**
     *
     * @param \DateTime $feUltMod            
     */
    public function setFeUltMod($feUltMod)
    {
        $this->feUltMod = $feUltMod;
    }
    
    /**
     *
     * @return string
     */
    public function getIpUltMod()
    {
        return $this->ipUltMod;
    }

    /**
     *
     * @param string $ipUltMod            
     */
    public function setIpUltMod($ipUltMod)
    {
        $this->ipUltMod = $ipUltMod;
    }    
}

<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiArea
 *
 * @ORM\Table(name="INFO_TAREA_TIEMPO_PARCIAL")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoTareaTiempoParcialRepository")
 */
class InfoTareaTiempoParcial
{

    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID_TIEMPO_PARCIAL", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_TAREA_TIEMPO_PARCIAL", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var integer $detalleId
     *
     * @ORM\Column(name="DETALLE_ID", type="integer", nullable=false)
     */
    private $detalleId; 

    /**
     * @var integer $valorTiempo
     *
     * @ORM\Column(name="VALOR_TIEMPO", type="integer", nullable=false)
     */
    private $valorTiempo;
    
    
    /**
     * @var integer $valorTiempoPausa
     *
     * @ORM\Column(name="VALOR_TIEMPO_PAUSA", type="integer", nullable=false)
     */
    private $valorTiempoPausa;    
    
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
     * @var string $ipCreacion
     *
     * @ORM\Column(name="IP_CREACION", type="string", nullable=false)
     */
    private $ipCreacion;
    
    /**
     * @var string $feCreacion
     *
     * @ORM\Column(name="FE_CREACION", type="datetime", nullable=false)
     */
    private $feCreacion;    

    /**
     * @var string $tipo
     *
     * @ORM\Column(name="TIPO", type="string", nullable=false)
     */
    private $tipo;

    /**
     * @var integer $tiempo
     *
     * @ORM\Column(name="TIEMPO", type="integer", nullable=false)
     */
    private $tiempo;

    /**
     * @var integer $tiempoEmpresa
     *
     * @ORM\Column(name="TIEMPO_EMPRESA", type="integer", nullable=false)
     */
    private $tiempoEmpresa;

    /**
     * Get tiempo
     *
     * @return integer
     */
    public function getTiempo()
    {
        return $this->tiempo;
    }

    /**
     * Set tiempo
     *
     * @param integer $intTiempo
     */
    public function setTiempo($intTiempo)
    {
        $this->tiempo = $intTiempo;
    }

    /**
     * Get tiempoEmpresa
     *
     * @return integer
     */
    public function getTiempoEmpresa()
    {
        return $this->tiempoEmpresa;
    }

    /**
     * Set tiempoEmpresa
     *
     * @param integer $intTiempoEmpresa
     */
    public function setTiempoEmpresa($intTiempoEmpresa)
    {
        $this->tiempoEmpresa = $intTiempoEmpresa;
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get detalleId
     *
     * @return integer
     */
    public function getDetalleId()
    {
        return $this->detalleId;
    }

    /**
     * Set detalleId
     *
     * @param integer $detalleId
     */
    public function setDetalleId($detalleId)
    {
        $this->detalleId = $detalleId; 
    }

    /**
     * Get valorTiempo
     *
     * @return integer   
     */
    public function getValorTiempo()
    {
        return $this->valorTiempo;
    }

    /**
     * Set valorTiempo
     *
     * @param integer $valorTiempo
     */
    public function setValorTiempo($valorTiempo)
    {
        $this->valorTiempo = $valorTiempo;
    }
    
    
    /**
     * Get valorTiempoPausa
     *
     * @return integer   
     */
    public function getValorTiempoPausa()
    {
        return $this->valorTiempoPausa;
    }

    /**
     * Set valorTiempoPausa
     *
     * @param integer $valorTiempoPausa
     */
    public function setValorTiempoPausa($valorTiempoPausa)
    {
        $this->valorTiempoPausa = $valorTiempoPausa;
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
    public function getUsrCreacion()
    {
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
    public function getIpCreacion()
    {
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
     * Get feCreacion
     *
     * @return datetime
     */
    public function getFeCreacion()
    {
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
}

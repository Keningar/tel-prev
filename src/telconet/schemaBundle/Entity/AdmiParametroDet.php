<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiParametroDet
 *
 * @ORM\Table(name="ADMI_PARAMETRO_DET")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiParametroDetRepository")
 */
class AdmiParametroDet
{

    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID_PARAMETRO_DET", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_PARAMETRO_DET", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var AdmiParametroCab
     *
     * @ORM\ManyToOne(targetEntity="AdmiParametroCab")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="PARAMETRO_ID", referencedColumnName="ID_PARAMETRO", nullable=false)
     * })
     */
    private $parametroId;

    /**
     * @var string $descripcion
     *
     * @ORM\Column(name="DESCRIPCION", type="string", nullable=true)
     */
    private $descripcion;

    /**
     * @var string $valor1
     *
     * @ORM\Column(name="VALOR1", type="string", nullable=true)
     */
    private $valor1;

    /**
     * @var string $valor2
     *
     * @ORM\Column(name="VALOR2", type="string", nullable=true)
     */
    private $valor2;

    /**
     * @var string $valor3
     *
     * @ORM\Column(name="VALOR3", type="string", nullable=true)
     */
    private $valor3;

    /**
     * @var string $valor4
     *
     * @ORM\Column(name="VALOR4", type="string", nullable=true)
     */
    private $valor4;

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
    private $ipCreacion;

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
     * @var string $ipUltMod
     *
     * @ORM\Column(name="IP_ULT_MOD", type="string", nullable=true)
     */
    private $ipUltMod;

    /**
     * @var string $valor5
     *
     * @ORM\Column(name="VALOR5", type="string", nullable=true)
     */
    private $valor5;

    /**
     * @var string $valor6
     *
     * @ORM\Column(name="VALOR6", type="string", nullable=true)
     */
    private $valor6;    

    /**
     * @var string $valor7
     *
     * @ORM\Column(name="VALOR7", type="string", nullable=true)
     */
    private $valor7;        
    /**
     * @var string $observacion
     *
     * @ORM\Column(name="OBSERVACION", type="string", nullable=true)
     */
    private $observacion;        
    /**
     * @var string $empresaCod
     *
     * @ORM\Column(name="EMPRESA_COD", type="string", nullable=true)
     */
    private $empresaCod;  
    /**
     * @var string $valor8
     *
     * @ORM\Column(name="VALOR8", type="string", nullable=true)
     */
    private $valor8;    
    /**
     * @var string $valor9
     *
     * @ORM\Column(name="VALOR9", type="string", nullable=true)
     */
    private $valor9; 

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
     * Get parametroId
     *
     * @return AdmiParametroCab
     */
    public function getParametroId()
    {
        return $this->parametroId;
    }

    /**
     * Set parametroId
     *
     * @param AdmiParametroCab $parametroId
     */
    public function setParametroId(AdmiParametroCab $parametroId)
    {
        $this->parametroId = $parametroId;
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
     * @param string $valor1
     */
    public function setValor1($valor1)
    {
        $this->valor1 = $valor1;
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
     * @param string $valor2
     */
    public function setValor2($valor2)
    {
        $this->valor2 = $valor2;
    }

    /**
     * Get valor3
     *
     * @return string
     */
    public function getValor3()
    {
        return $this->valor3;
    }

    /**
     * Set valor3
     *
     * @param string $valor3
     */
    public function setValor3($valor3)
    {
        $this->valor3 = $valor3;
    }

    /**
     * Get valor4
     *
     * @return string
     */
    public function getValor4()
    {
        return $this->valor4;
    }

    /**
     * Set valor4
     *
     * @param string $valor4
     */
    public function setValor4($valor4)
    {
        $this->valor4 = $valor4;
    }

    /**
     * Get descripcion
     *
     * @return string
     */
    public function getDescripcion()
    {
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
    public function getFeUltMod()
    {
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
     * Get ipUltMod
     *
     * @return string
     */
    public function getIpUltMod()
    {
        return $this->ipUltMod;
    }
    
    /**
     * Get valor5
     *
     * @return string
     */
    public function getValor5()
    {
        return $this->valor5;
    }

    /**
     * Set valor5
     *
     * @param string $valor5
     */
    public function setValor5($valor5)
    {
        $this->valor5 = $valor5;
    }

    /**
     * Get valor6
     *
     * @return string
     */
    public function getValor6()
    {
        return $this->valor6;
    }

    /**
     * Set valor6
     *
     * @param string $valor6
     */
    public function setValor6($strValor6)
    {
        $this->valor6 = $strValor6;
    }

    /**
     * Get valor7
     *
     * @return string
     */
    public function getValor7()
    {
        return $this->valor7;
    }

    /**
     * Set valor7
     *
     * @param string $valor7
     */
    public function setValor7($strValor7)
    {
        $this->valor7 = $strValor7;
    }

    /**
     * Get observacion
     *
     * @return string
     */
    public function getObservacion()
    {
        return $this->observacion;
    }

    /**
     * Set observacion
     *
     * @param string $observacion
     */
    public function setObservacion($strObservacion)
    {
        $this->observacion = $strObservacion;
    }    

    /**
     * Get empresaCod
     *
     * @return string
     */
    public function getEmpresaCod()
    {
        return $this->empresaCod;
    }

    /**
     * Set empresaCod
     *
     * @param string $empresaCod
     */
    public function setEmpresaCod($empresaCod)
    {
        $this->empresaCod = $empresaCod;
    }
    
    /**
     * Get valor8
     *
     * @return string
     */
    public function getValor8()
    {
        return $this->valor8;
    }

    /**
     * Set valor8
     *
     * @param string $valor8
     */
    public function setValor8($strValor8)
    {
        $this->valor8 = $strValor8;
    }  
           
    /**
     * Get valor9
     *
     * @return string
     */
    public function getValor9()
    {
        return $this->valor9;
    }

    /**
     * Set valor9
     *
     * @param string $valor9
     */
    public function setValor9($strValor9)
    {
        $this->valor9 = $strValor9;
    }

    /**
     * Set ipUltMod
     *
     * @param string $ipUltMod
     */
    public function setIpUltMod($ipUltMod)
    {
        $this->ipUltMod = $ipUltMod;
    }
    
    public function __toString()
    {
        return $this->valor1;
    }
}

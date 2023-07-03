<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmEmpresaParametro
 *
 * @ORM\Table(name="ADM_EMPRESA_PARAMETRO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmEmpresaParametroRepository")
 */
class AdmEmpresaParametro
{
    
    /**
    * @var integer $id
    *
    * @ORM\Column(name="ID_EMPRESA_PARAMETRO", type="integer", nullable=false)
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="SEQUENCE")
    * @ORM\SequenceGenerator(sequenceName="SEQ_ADM_EMPRESA_PARAMETRO", allocationSize=1, initialValue=1)
    */		

    private $id;
    
    /**
    * @var integer $empresaId
    *
    * @ORM\Column(name="EMPRESA_ID", type="integer", nullable=false)
    */		

    private $empresaId;
    
    /**
    * @var string $clave
    *
    * @ORM\Column(name="CLAVE", type="string", nullable=false)
    */		

    private $clave;
    
    /**
    * @var string $valor
    *
    * @ORM\Column(name="VALOR", type="string", nullable=false)
    */		

    private $valor;    
    
    /**
    * @var string $descripcion
    *
    * @ORM\Column(name="DESCRIPCION", type="string", nullable=false)
    */		

    private $descripcion;     

    /**
    * @var string $esConfig
    *
    * @ORM\Column(name="ES_CONFIG", type="string", nullable=false)
    */		

    private $esConfig;      
    
    /**
    * @var string $esDefault
    *
    * @ORM\Column(name="ES_DEFAULT", type="string", nullable=false)
    */		

    private $esDefault;   
    
    /**
    * @var string $enviaPorMail
    *
    * @ORM\Column(name="ENVIA_POR_MAIL", type="string", nullable=false)
    */		

    private $enviaPorMail;       
    
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
    * Get empresaId
    *
    * @return integer
    */		

    public function getEmpresaId()
    {
        return $this->empresaId; 
    }

    /**
    * Set empresaId
    *
    * @param integer $intEmpresaId
    */
    public function setEmpresaId($intEmpresaId)
    {
        $this->empresaId = $intEmpresaId;
    }
    
    /**
    * Get clave
    *
    * @return string
    */		

    public function getClave()
    {
        return $this->clave; 
    }

    /**
    * Set clave
    *
    * @param string $strClave
    */
    public function setClave($strClave)
    {
        $this->clave = $strClave;
    }
    
    /**
    * Get valor
    *
    * @return string
    */		

    public function getValor()
    {
        return $this->valor; 
    }

    /**
    * Set valor
    *
    * @param string $strValor
    */
    public function setValor($strValor)
    {
        $this->valor = $strValor;
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
    * @param string $strDescripcion
    */
    public function setDescripcion($strDescripcion)
    {
        $this->descripcion = $strDescripcion;
    }    
    
    /**
    * Get esConfig
    *
    * @return string
    */		

    public function getEsConfig()
    {
        return $this->esConfig; 
    }

    /**
    * Set esConfig
    *
    * @param string $strEsConfig
    */
    public function setEsConfig($strEsConfig)
    {
        $this->esConfig = $strEsConfig;
    }    

    /**
    * Get esDefault
    *
    * @return string
    */		

    public function getEsDefault()
    {
        return $this->esDefault; 
    }

    /**
    * Set esDefault
    *
    * @param string $strEsDefault
    */
    public function setEsDefault($strEsDefault)
    {
        $this->esDefault = $strEsDefault;
    }    

    /**
    * Get enviaPorMail
    *
    * @return string
    */		

    public function getEnviaPorMail()
    {
        return $this->enviaPorMail; 
    }

    /**
    * Set enviaPorMail
    *
    * @param string $strEnviaPorMail
    */
    public function setEnviaPorMail($strEnviaPorMail)
    {
        $this->esDefault = $strEnviaPorMail;
    }    
    
}


<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiFormatoDebitoCaract
 *
 * @ORM\Table(name="ADMI_FORMATO_DEBITO_CARACT")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiFormatoDebitoCaractRepository")
 */

class AdmiFormatoDebitoCaract
{

    /**
    * @var integer $id
    *
    * @ORM\Column(name="ID_FORMATO_DEBITO_CARACT", type="integer", nullable=false)
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="SEQUENCE")
    * @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_FORMATO_DEBITO_CARACT", allocationSize=1, initialValue=1)
    */		

    private $id;


    /**
    * @var integer $bancoTipoCuentaId
    *
    * @ORM\Column(name="BANCO_TIPO_CUENTA_ID", type="integer", nullable=true)
    */		

    private $bancoTipoCuentaId;


    /**
    * @var integer $empresaCod
    *
    * @ORM\Column(name="EMPRESA_COD", type="integer", nullable=true)
    */		

    private $empresaCod;


    /**
    * @var integer $caracteristicaId
    *
    * @ORM\Column(name="CARACTERISTICA_ID", type="integer", nullable=true)
    */		

    private $caracteristicaId;


    /**
    * @var string $valor
    *
    * @ORM\Column(name="VALOR", type="string", nullable=false)
    */		

    private $valor;


    /**
    * @var string $proceso
    *
    * @ORM\Column(name="PROCESO", type="string", nullable=false)
    */		

    private $proceso;


    /**
    * @var string $estado
    *
    * @ORM\Column(name="ESTADO", type="string", nullable=false)
    */		

    private $estado;


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
    * Get bancoTipoCuentaId
    *
    * @return integer
    */		

    public function getBancoTipoCuentaId()
    {
        return $this->bancoTipoCuentaId; 
    }


    /**
    * Get empresaCod
    *
    * @return integer
    */

    public function getEmpresaCod()
    {
        return $this->empresaCod;
    }


    /**
    * Get caracteristicaId
    *
    * @return integer
    */

    public function getCaracteristicaId()
    {
        return $this->caracteristicaId;
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
    * Get proceso
    *
    * @return string
    */		

    public function getProceso()
    {
        return $this->proceso;
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
    * Set bancoTipoCuentaId
    *
    * @param integer $bancoTipoCuentaId
    */

    public function setBancoTipoCuentaId($bancoTipoCuentaId)
    {
        $this->bancoTipoCuentaId = $bancoTipoCuentaId;
    }


    /**
    * Set empresaCod
    *
    * @param integer $empresaCod
    */

    public function setEmpresaCod($empresaCod)
    {
        $this->empresaCod = $empresaCod;
    }


    /**
    * Set caracteristicaId
    *
    * @param integer $caracteristicaId
    */

    public function setCaracteristicaId($caracteristicaId)
    {
        $this->caracteristicaId = $caracteristicaId;
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
    * Set proceso
    *
    * @param string $proceso
    */

    public function setProceso($proceso)
    {
        $this->proceso = $proceso;
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

}

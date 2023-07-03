<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoMigraAdData
 *
 * @ORM\Table(name="INFO_MIGRA_AD_DATA")
 * @ORM\Entity
 */
class InfoMigraAdData
{

    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID_MIGRACION_ERROR", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_MIGRA_AD_DATA", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var integer $migracionCabId
     *
     * @ORM\Column(name="MIGRACION_CAB_ID", type="integer")
     */
    private $migracionCabId;

    /**
     * @var integer $migracionDetId
     *
     * @ORM\Column(name="MIGRACION_DET_ID", type="integer")
     */
    private $migracionDetId;
    
    /**
     * @var string $tipoProceso
     *
     * @ORM\Column(name="TIPO_PROCESO", type="string")
     */
    private $tipoProceso;

    /**
     * @var integer $identificador
     *
     * @ORM\Column(name="IDENTIFICADOR", type="integer")
     */
    private $identificador;

    /**
     * @var string $informacion
     *
     * @ORM\Column(name="INFORMACION", type="string")
     */
    private $informacion;

    /**
    * @var integer $linea
    *
    * @ORM\Column(name="LINEA", type="integer")
    */
    private $linea;

    /**
     * @var string $login
     *
     * @ORM\Column(name="LOGIN", type="string")
     */
    private $login;

    /**
     * @var string $estado
     *
     * @ORM\Column(name="ESTADO", type="string")
     */
    private $estado;

    /**
    * @var string $observacion
    *
    * @ORM\Column(name="OBSERVACION", type="string")
    */
    private $observacion;

    /**
     * @var string $usrCreacion
     *
     * @ORM\Column(name="USR_CREACION", type="string")
     */
    private $usrCreacion;

    /**
    * @var string $feCreacion
    *
    * @ORM\Column(name="FE_CREACION", type="datetime")
    */
    private $feCreacion;

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
     * Set id
     *
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get migracionCabId
     *
     * @return integer
     */
    public function getMigracionCabId()
    {
        return $this->migracionCabId;
    }

    /**
     * Set migracionCabId
     *
     * @param integer $migracionCabId
     */
    public function setMigracionCabId($migracionCabId)
    {
        $this->migracionCabId = $migracionCabId;
    }

    /**
     * Get migracionDetId
     *
     * @return integer
     */
    public function getMigracionDetId()
    {
        return $this->migracionDetId;
    }

    /**
     * Set migracionDetId
     *
     * @param integer $migracionDetId
     */
    public function setMigracionDetId($migracionDetId)
    {
        $this->migracionDetId = $migracionDetId;
    }

    /**
     * Get tipoProceso
     *
     * @return string
     */
    public function getTipoProceso()
    {
        return $this->tipoProceso;
    }

    /**
     * Set tipoProceso
     *
     * @param string $tipoProceso
     */
    public function setTipoProceso($tipoProceso)
    {
        $this->tipoProceso = $tipoProceso;
    }

    /**
     * Get identificador
     *
     * @return integer
     */
    public function getIdentificador()
    {
        return $this->identificador;
    }

    /**
     * Set identificador
     *
     * @param integer $identificador
     */
    public function setIdentificador($identificador)
    {
        $this->identificador = $identificador;
    }

    /**
     * Get informacion
     *
     * @return string
     */
    public function getInformacion()
    {
        return $this->informacion;
    }

    /**
     * Set informacion
     *
     * @param string $informacion
     */
    public function setInformacion($informacion)
    {
        $this->informacion = $informacion;
    }

    /**
     * Get linea
     *
     * @return integer
     */
    public function getLinea()
    {
        return $this->linea;
    }

    /**
     * Set linea
     *
     * @param integer $linea
     */
    public function setLinea($linea)
    {
        $this->linea = $linea;
    }

    /**
     * Get login
     *
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Set login
     *
     * @param string $login
     */
    public function setLogin($login)
    {
        $this->login = $login;
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
    public function setObservacion($observacion)
    {
        $this->observacion = $observacion;
    }

    /**
     * Get usrCreacion
     * @return string
     */
    public function getUsrCreacion()
    {
        return $this->usrCreacion;
    }

    /**
     * Set usrCreacion 
     * @param string $usrCreacion
     */
    public function setUsrCreacion($usrCreacion)
    {
        $this->usrCreacion = $usrCreacion;
    }

    /**
     * Get feCreacion
     * @return string
     */
    public function getFeCreacion()
    {
        return $this->feCreacion;
    }

    /**
     * Set feCreacion
     * @param string $feCreacion
     */
    public function setFeCreacion($feCreacion)
    {
        $this->feCreacion = $feCreacion;
    }

}

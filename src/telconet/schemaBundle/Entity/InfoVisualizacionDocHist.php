<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoVisualizacionDocHist
 *
 * @ORM\Table(name="INFO_VISUALIZACION_DOC_HIST")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoVisualizacionDocHistRepository")
 */
class InfoVisualizacionDocHist
{


    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID_VISUALIZACION_DOC_HIST", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_VISUAL_DOC_HIST", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string $empresaCod
     *
     * @ORM\Column(name="EMPRESA_COD", type="string", nullable=false)
     */
    private $empresaCod;


    /**
     * @var string $accion
     *
     * @ORM\Column(name="ACCION", type="string", nullable=false)
     */
    private $accion;


    /**
     * @var string $observacion
     *
     * @ORM\Column(name="OBSERVACION", type="string", nullable=true)
     */
    private $observacion;


    /**
     * @var string $estadoServicio
     *
     * @ORM\Column(name="ESTADO_SERVICIO", type="string", nullable=false)
     */
    private $estadoServicio;


    /**
     * @var string $identificacion
     *
     * @ORM\Column(name="IDENTIFICACION", type="string", nullable=false)
     */
    private $identificacion;

    /**
     * @var string $tipoDocumento
     *
     * @ORM\Column(name="TIPO_DOCUMENTO", type="string", nullable=false)
     */
    private $tipoDocumento;

    /**
     * @var string $loginCliente
     *
     * @ORM\Column(name="LOGIN_CLIENTE", type="string", nullable=false)
     */
    private $loginCliente;


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
     * Get id
     *
     * @return integer
     */

    public function getId()
    {
        return $this->id;
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
     * @param string $strEmpresaCod
     */
    public function setEmpresaCod($strEmpresaCod)
    {
        $this->empresaCod = $strEmpresaCod;
    }

    /**
     * Get accion
     *
     * @return string
     */

    public function getAccion()
    {
        return $this->accion;
    }

    /**
     * Set accion
     *
     * @param string $strAccion
     */

    public function setAccion($strAccion)
    {
        $this->accion = $strAccion;
    }



    /**
     * Get observacion
     *
     * @return  string
     */
    public function getObservacion()
    {
        return $this->observacion;
    }

    /**
     * Set observacion
     *
     * @param  string  $observacion  $observacion
     *
     * @return  self
     */
    public function setObservacion($observacion)
    {
        $this->observacion = $observacion;

        return $this;
    }


    /**
     * Get estadoServicio
     *
     * @return  string
     */
    public function getEstadoServicio()
    {
        return $this->estadoServicio;
    }

    /**
     * Set estadoServicio
     *
     * @param  string  $estadoServicio  $estadoServicio
     *
     * @return  self
     */
    public function setEstadoServicio($estadoServicio)
    {
        $this->estadoServicio = $estadoServicio;

        return $this;
    }

    /**
     * Get identificacion
     *
     * @return  string
     */
    public function getIdentificacion()
    {
        return $this->identificacion;
    }

    /**
     * Set identificacion
     *
     * @param  string  $identificacion  $identificacion
     *
     * @return  self
     */
    public function setIdentificacion($identificacion)
    {
        $this->identificacion = $identificacion;

        return $this;
    }



    /**
     * Get tipoDocumento
     *
     * @return  string
     */
    public function getTipoDocumento()
    {
        return $this->tipoDocumento;
    }

    /**
     * Set tipoDocumento
     *
     * @param  string  $tipoDocumento  $tipoDocumento
     *
     * @return  self
     */
    public function setTipoDocumento($tipoDocumento)
    {
        $this->tipoDocumento = $tipoDocumento;

        return $this;
    }

    /**
     * Get loginCliente
     *
     * @return string
     */

    public function getLoginCliente()
    {
        return $this->loginCliente;
    }

    /**
     * Set loginCliente
     *
     * @param string $loginCliente
     */
    public function setLoginCliente($loginCliente)
    {
        $this->loginCliente = $loginCliente;
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

    public function getFeCreacion()
    {
        return $this->feCreacion;
    }

    /**
     * Set feCreacion
     *
     * @param datetime $arrayFeCreacion
     */
    public function setFeCreacion($arrayFeCreacion)
    {
        $this->feCreacion = $arrayFeCreacion;
    }

    /**
     * Get ipCreacion
     *
     * @return  string
     */
    public function getIpCreacion()
    {
        return $this->ipCreacion;
    }

    /**
     * Set ipCreacion
     *
     * @param  string  $ipCreacion  $ipCreacion
     *
     * @return  self
     */
    public function setIpCreacion($ipCreacion)
    {
        $this->ipCreacion = $ipCreacion;

        return $this;
    }
}

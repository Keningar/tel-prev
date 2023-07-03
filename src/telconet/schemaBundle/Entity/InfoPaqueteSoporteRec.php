<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoPaqueteSoporteRec
 *
 * @ORM\Table(name="INFO_PAQUETE_SOPORTE_REC")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoPaqueteSoporteRecRepository")
 */
class InfoPaqueteSoporteRec
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID_PAQUETE_SOPORTE_REC", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_PAQUETE_SOPORTE_REC", allocationSize=1, initialValue=1)
     */

    private $id;

    /**
     * @var string $paqueteSoporteCabId
     *
     * @ORM\Column(name="PAQUETE_SOPORTE_CAB_ID", type="string", nullable=false)
     */

    private $paqueteSoporteCabId;

    /**
     * @var integer $personaEmpresaRolId
     *
     * @ORM\Column(name="UUID_PAQUETE_SOPORTE_REC", type="integer", nullable=false)
     */

    private $uuidPaqueteSoporteRec;

    /**
     * @var integer $minutosContratados
     *
     * @ORM\Column(name="MINUTOS_CONTRATADOS", type="integer", nullable=false)
     */

    private $minutosContratados;

    /**
     * @var integer $minutosRestantes
     *
     * @ORM\Column(name="MINUTOS_RESTANTES", type="integer", nullable=false)
     */

    private $minutosRestantes;

    /**
     *
     * @var \Date $fechaInicio
     *      @ORM\Column(name="FECHA_INICIO", type="date", nullable=false)
     */
    private $fechaInicio;

    /**
     *
     * @var \Date $fechaFin
     *      @ORM\Column(name="FECHA_FIN", type="date", nullable=false)
     */
    private $fechaFin;
    
    /**
     *
     * @var string $estado
     *      @ORM\Column(name="ESTADO", type="string", nullable=false)
     */
    private $estado;
    
    /**
     *
     * @var string $usuarioCreacion
     *      @ORM\Column(name="USUARIO_CREACION", type="date", nullable=false)
     */
    private $usuarioCreacion;
    
    /**
     *
     * @var \Date $fechaCreacion
     *      @ORM\Column(name="FECHA_CREACION", type="date", nullable=false)
     */
    private $fechaCreacion;
    
    /**
     *
     * @var string $usuarioModificacion
     *      @ORM\Column(name="USUARIO_MODIFICACION", type="string", nullable=true)
     */
    private $usuarioModificacion;
    
    /**
     *
     * @var \Date $fechaModificacion
     *      @ORM\Column(name="FECHA_MODIFICACION", type="date", nullable=true)
     */
    private $fechaModificacion;









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
     * Get paqueteSoporteCabId
     *
     * @return integer
     */

    public function getPaqueteSoporteCabId()
    {
        return $this->paqueteSoporteCabId;
    }

    /**
     * Set paqueteSoporteCabId
     *
     * @param integer $intPaqueteSoporteCabId
     */
    public function setPaqueteSoporteCabId($intPaqueteSoporteCabId)
    {
        $this->paqueteSoporteCabId = $intPaqueteSoporteCabId;
    }

    /**
     * Get uuidPaqueteSoporteRec
     *
     * @return string
     */

    public function getUuIdPaqueteSoporteRec()
    {
        return $this->uuidPaqueteSoporteRec;
    }

    /**
     * Set uuidPaqueteSoporteRec
     *
     * @param string $intUuIdPaqueteSoporteRec
     */
    public function setUuIdPaqueteSoporteRec($intUuIdPaqueteSoporteRec)
    {
        $this->uuidPaqueteSoporteRec = $intUuIdPaqueteSoporteRec;
    }

    /**
     * Get minutosContratados
     *
     * @return integer
     */

    public function getMinutosContratados()
    {
        return $this->minutosContratados;
    }

    /**
     * Set minutosContratados
     *
     * @param integer $strMinutosContratados
     */
    public function setMinutosContratados($strMinutosContratados)
    {
        $this->minutosContratados = $strMinutosContratados;
    }

    /**
     * Get minutosRestantes
     *
     * @return integer
     */

    public function getMinutosRestantes()
    {
        return $this->minutosRestantes;
    }

    /**
     * Set minutosRestantes
     *
     * @param integer $strMinutosRestantes
     */
    public function setMinutosRestantes($strMinutosRestantes)
    {
        $this->minutosRestantes = $strMinutosRestantes;
    }

    /**
     * Get fechaInicio
     * 
     * @param \Date
     */
    public function getFechaInicio()
    {
        return $this->fechaInicio;
    }

    /**
     * Set fechaInicio
     * 
     * @param \Date $fechaInicio            
     */
    public function setFechaInicio($dateFechaInicio)
    {
        $this->fechaInicio = $dateFechaInicio;
    }

    /**
     * Get fechaFin
     * 
     * @return \Date
     */
    public function getFechaFin()
    {
        return $this->fechaFin;
    }

    /**
     * Set fechaFin
     * 
     * @param \Date $fechaFin            
     */
    public function setFechaFin($dateFechaFin)
    {
        $this->fechaFin = $dateFechaFin;
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
    public function setEstado($strEstado)
    {
        $this->estado = $strEstado;
    }

    /**
     * Get usuarioCreacion
     * 
     * @return string
     */
    public function getUsuarioCreacion()
    {
        return $this->usuarioCreacion;
    }

    /**
     * Set usuarioCreacion
     * 
     * @param string $usuarioCreacion            
     */
    public function setUsuarioCreacion($strUsuarioCreacion)
    {
        $this->usuarioCreacion = $strUsuarioCreacion;
    }

    /**
     * Get fechaCreacion
     * 
     * @return \Date
     */
    public function getFechaCreacion()
    {
        return $this->fechaCreacion;
    }

    /**
     * Set fechaCreacion
     * 
     * @param \Date
     */
    public function setFechaCreacion($strFechaCreacion)
    {
        $this->fechaCreacion = $strFechaCreacion;
    }


    /**
     * Get usuarioModificacion
     * 
     * @return string
     */
    public function getUsuarioModificacion()
    {
        return $this->usuarioModificacion;
    }

    /**
     * Set usuarioModificacion
     * 
     * @param string $usuarioModificacion            
     */
    public function setUsuarioModificacion($strUsuarioModificacion)
    {
        $this->usuarioModificacion = $strUsuarioModificacion;
    }

    /**
     * Get fechaModificacion
     * 
     * @return \Date
     */
    public function getFechaModificacion()
    {
        return $this->fechaModificacion;
    }

    /**
     * Set fechaModificacion
     * 
     * @param \Date
     */
    public function setFechaModificacion($strFechaModificacion)
    {
        $this->fechaModificacion = $strFechaModificacion;
    }
}

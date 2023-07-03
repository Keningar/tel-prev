<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoHorarioEmpleados
 *
 * @ORM\Table(name="INFO_HORARIO_EMPLEADOS")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoHorarioEmpleadosRepository")
 */
class InfoHorarioEmpleados
{

/**
* @var integer $id
*
* @ORM\Column(name="ID_HORARIO_EMPLEADO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_HORARIO_EMPLEADOS", allocationSize=1, initialValue=1)
*/		
		
private $id;	

    /**
     * @var string $fechaInicio
     *
     * @ORM\Column(name="FECHA_INICIO", type="string", nullable=false)
     */		        
    private $fechaInicio;

    /**
     * @var string $horaInicio
     *
     * @ORM\Column(name="HORA_INICIO", type="string", nullable=false)
     */		        
    private $horaInicio;

    /**
     * @var string $fechaFin
     *
     * @ORM\Column(name="FECHA_FIN", type="string", nullable=false)
     */		            
    private $fechaFin;

    /**
     * @var string $horaFin
     *
     * @ORM\Column(name="HORA_FIN", type="string", nullable=false)
     */		            
    private $horaFin;

    /**
     * @var integer $noEmple
     *
     * @ORM\Column(name="NO_EMPLE", type="integer", nullable=false)
     */
    private $noEmple;

    /**
     * @var integer $cuadrillaId
     *
     * @ORM\Column(name="CUADRILLA_ID", type="integer", nullable=true)
     */
    private $cuadrillaId;

    /**
     * @var integer $tipoHorarioId
     *
     * @ORM\Column(name="TIPO_HORARIO_ID", type="integer", nullable=false)
     */
    private $tipoHorarioId;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=false)
*/		
     		
private $estado;

/**
* @var string $observacion
*
* @ORM\Column(name="OBSERVACION", type="string", nullable=true)
*/		
     		
private $observacion;

/**
* @var datetime $feCreacion
*
* @ORM\Column(name="FE_CREACION", type="datetime", nullable=false)
*/		
     		
private $feCreacion;

/**
* @var string $usrCreacion
*
* @ORM\Column(name="USR_CREACION", type="string", nullable=false)
*/		

private $usrCreacion;

/**
* @var string $usrModificacion
*
* @ORM\Column(name="USR_MODIFICACION", type="string", nullable=false)
*/		
     		
private $usrModificacion;

/**
* @var datetime $feModificacion
*
* @ORM\Column(name="FE_MODIFICACION", type="datetime", nullable=false)
*/		
     		
private $feModificacion;

/**
* @return int
*/
public function getId()
{
   return $this->id;
}

/**
* @param int $id
*/
public function setId($id)
{
    $this->id = $id;
}

/**
* Get nombreTipoHorario
*
* @return string
*/		
     		
public function getNombreTipoHorario(){
	return $this->nombreTipoHorario; 
}

/**
* Set nombreTipoHorario
*
* @param string $nombreTipoHorario
*/
public function setNombreTipoHorario($nombreTipoHorario)
{
        $this->nombreTipoHorario = $nombreTipoHorario;
}
    /**
     * @return string
     */
    public function getFechaInicio()
    {
        return $this->fechaInicio;
    }

    /**
     * @param string $fechaInicio
     */
    public function setFechaInicio($fechaInicio)
    {
        $this->fechaInicio = $fechaInicio;
    }

    /**
     * @return string
     */
    public function getHoraInicio()
    {
        return $this->horaInicio;
    }

    /**
     * @param string $horaInicio
     */
    public function setHoraInicio($horaInicio)
    {
        $this->horaInicio = $horaInicio;
    }

    /**
     * @return string
     */
    public function getFechaFin()
    {
        return $this->fechaFin;
    }

    /**
     * @param string $fechaFin
     */
    public function setFechaFin($fechaFin)
    {
        $this->fechaFin = $fechaFin;
    }

    /**
     * @return string
     */
    public function getHoraFin()
    {
        return $this->horaFin;
    }

    /**
     * @param string $horaFin
     */
    public function setHoraFin($horaFin)
    {
        $this->horaFin = $horaFin;
    }

    /**
     * @return integer $noEmple
     */
    public function getNoEmple()
    {
        return $this->noEmple;
    }

    /**
     * @param integer $noEmple
     */
    public function setNoEmple($noEmple)
    {
        $this->noEmple = $noEmple;
    }

    /**
     * @return integer $cuadrillaId
     */
    public function getCuadrillaId()
    {
        return $this->cuadrillaId;
    }

    /**
     * @param integer $cuadrillaId
     */
    public function setCuadrillaId($cuadrillaId)
    {
        $this->cuadrillaId = $cuadrillaId;
    }
    
    /**
     * @return integer $tipoHorarioId
     */
    public function getTipoHorarioId()
    {
        return $this->tipoHorarioId;
    }

    /**
     * @param integer $tipoHorarioId
     */
    public function setTipoHorarioId($tipoHorarioId)
    {
        $this->tipoHorarioId = $tipoHorarioId;
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
        $this->estado = $estado;
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
* Get usrModificacion
*
* @return string
*/		
     		
public function getUsrModificacion(){
	return $this->usrModificacion; 
}

/**
* Set usrModificacion
*
* @param string $usrModificacion
*/
public function setUsrModificacion($usrModificacion)
{
        $this->usrModificacion = $usrModificacion;
}


}

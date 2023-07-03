<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoTarea
 *
 * @ORM\Table(name="INFO_TAREA")
 * @ORM\Entity
 */
class InfoTarea
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_INFO_TAREA", type="integer", nullable=false)
* @ORM\Id
*/		
		
private $id;


/**
* @var string $numero
*
* @ORM\Column(name="NUMERO", type="string")
*/

private $numero;


/**
 * @var string $usrCreacionDetalle
 *
 * @ORM\Column(name="USR_CREACION_DETALLE", type="string")
 */

private $usrCreacionDetalle;

/**
* @var string $detalleIdRelacionado
*
* @ORM\Column(name="DETALLE_ID_RELACIONADO", type="string")
*/

private $detalleIdRelacionado;

/**
* @var string $feCreacionDetalle
*
* @ORM\Column(name="FE_CREACION_DETALLE", type="datetime")
*/

private $feCreacionDetalle;


/**
* @var string $feSolicitada
*
* @ORM\Column(name="FE_SOLICITADA", type="datetime")
*/

private $feSolicitada;


/**
* @var string $observacion
*
* @ORM\Column(name="OBSERVACION", type="string")
*/

private $observacion;


/**
* @var integer $detalleHipotesisId
*
* @ORM\Column(name="DETALLE_HIPOTESIS_ID", type="integer")
*/

private $detalleHipotesisId;


/**
* @var integer $tareaId
*
* @ORM\Column(name="TAREA_ID", type="integer")
*/

private $tareaId;


/**
* @var string $nombreTarea
*
* @ORM\Column(name="NOMBRE_TAREA", type="string")
*/

private $nombreTarea;


/**
* @var string $nombreProceso
*
* @ORM\Column(name="NOMBRE_PROCESO", type="string")
*/

private $nombreProceso;

/**
 * @var string $procesoId
 *
 * @ORM\Column(name="PROCESO_ID", type="string")
 */

private $procesoId;

/**
* @var string $asignadoId
*
* @ORM\Column(name="ASIGNADO_ID", type="string")
*/

private $asignadoId;

/**
* @var string $asignadoNombre
*
* @ORM\Column(name="ASIGNADO_NOMBRE", type="string")
*/

private $asignadoNombre;

/**
* @var string $refAsignadoId
*
* @ORM\Column(name="REF_ASIGNADO_ID", type="string")
*/

private $refAsignadoId;

/**
* @var string $refAsignadoNombre
*
* @ORM\Column(name="REF_ASIGNADO_NOMBRE", type="string")
*/

private $refAsignadoNombre;

/**
* @var integer $personaEmpresaRolId
*
* @ORM\Column(name="PERSONA_EMPRESA_ROL_ID", type="integer")
*/

private $personaEmpresaRolId;

/**
* @var integer $detalleAsignacionId
*
* @ORM\Column(name="DETALLE_ASIGNACION_ID", type="integer")
*/

private $detalleAsignacionId;

/**
* @var string $feCreacionAsignacion
*
* @ORM\Column(name="FE_CREACION_ASIGNACION", type="datetime")
*/

private $feCreacionAsignacion;


/**
* @var integer $departamentoId
*
* @ORM\Column(name="DEPARTAMENTO_ID", type="integer")
*/

private $departamentoId;


/**
* @var string $tipoAsignado
*
* @ORM\Column(name="TIPO_ASIGNADO", type="string")
*/

private $tipoAsignado;


/**
* @var integer $cantonId
*
* @ORM\Column(name="CANTON_ID", type="integer")
*/

private $cantonId;


/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string")
*/

private $estado;

/**
* @var integer $detalleHistorialId
*
* @ORM\Column(name="DETALLE_HISTORIAL_ID", type="integer")
*/

private $detalleHistorialId;

/**
* @var string $feCreacionHis
*
* @ORM\Column(name="FE_CREACION_HIS", type="datetime")
*/

private $feCreacionHis;

/**
* @var string $usrCreacionHis
*
* @ORM\Column(name="USR_CREACION_HIS", type="string")
*/

private $usrCreacionHis;

/**
* @var string $observacionHistorial
*
* @ORM\Column(name="OBSERVACION_HISTORIAL", type="string")
*/

private $observacionHistorial;

/**
* @var integer $departamentoOrigenId
*
* @ORM\Column(name="DEPARTAMENTO_ORIGEN_ID", type="integer")
*/

private $departamentoOrigenId;

/**
* @var integer $personaEmpresaRolIdHis
*
* @ORM\Column(name="PERSONA_EMPRESA_ROL_ID_HIS", type="integer")
*/

private $personaEmpresaRolIdHis;

/**
* @var integer $asignadoIdHis
*
* @ORM\Column(name="ASIGNADO_ID_HIS", type="integer")
*/

private $asignadoIdHis;

/**
* @var integer $numeroTarea
*
* @ORM\Column(name="NUMERO_TAREA", type="integer")
*/

private $numeroTarea;


/**
* @var integer $detalleId
*
* @ORM\Column(name="DETALLE_ID", type="integer")
*/

private $detalleId;

/**
* @var string $feCreacion
*
* @ORM\Column(name="FE_CREACION", type="datetime")
*/

private $feCreacion;

/**
* @var string $feUltMod
*
* @ORM\Column(name="FE_ULT_MOD", type="datetime")
*/

private $feUltMod;

/**
* @var string $usrCreacion
*
* @ORM\Column(name="USR_CREACION", type="string")
*/

private $usrCreacion;

/**
* @var string $ipCreacion
*
* @ORM\Column(name="IP_CREACION", type="string")
*/

private $ipCreacion;

/**
* @var string $usrUltMod
*
* @ORM\Column(name="USR_ULT_MOD", type="string")
*/

private $usrUltMod;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * @return string
     */
    public function getUsrCreacionDetalle()
    {
        return $this->usrCreacionDetalle;
    }

    /**
     * @return string
     */
    public function getDetalleIdRelacionado()
    {
        return $this->detalleIdRelacionado;
    }

    /**
     * @return string
     */
    public function getFeCreacionDetalle()
    {
        return $this->feCreacionDetalle;
    }

    /**
     * @return string
     */
    public function getFeSolicitada()
    {
        return $this->feSolicitada;
    }

    /**
     * @return string
     */
    public function getObservacion()
    {
        return $this->observacion;
    }

    /**
     * @return int
     */
    public function getDetalleHipotesisId()
    {
        return $this->detalleHipotesisId;
    }

    /**
     * @return int
     */
    public function getTareaId()
    {
        return $this->tareaId;
    }

    /**
     * @return string
     */
    public function getNombreTarea()
    {
        return $this->nombreTarea;
    }

    /**
     * @return string
     */
    public function getNombreProceso()
    {
        return $this->nombreProceso;
    }

    /**
     * @return string
     */
    public function getProcesoId()
    {
        return $this->procesoId;
    }

    /**
     * @return string
     */
    public function getAsignadoId()
    {
        return $this->asignadoId;
    }

    /**
     * @return string
     */
    public function getAsignadoNombre()
    {
        return $this->asignadoNombre;
    }

    /**
     * @return string
     */
    public function getRefAsignadoId()
    {
        return $this->refAsignadoId;
    }

    /**
     * @return string
     */
    public function getRefAsignadoNombre()
    {
        return $this->refAsignadoNombre;
    }

    /**
     * @return int
     */
    public function getPersonaEmpresaRolId()
    {
        return $this->personaEmpresaRolId;
    }

    /**
     * @return int
     */
    public function getDetalleAsignacionId()
    {
        return $this->detalleAsignacionId;
    }

    /**
     * @return string
     */
    public function getFeCreacionAsignacion()
    {
        return $this->feCreacionAsignacion;
    }

    /**
     * @return int
     */
    public function getDepartamentoId()
    {
        return $this->departamentoId;
    }

    /**
     * @return string
     */
    public function getTipoAsignado()
    {
        return $this->tipoAsignado;
    }

    /**
     * @return int
     */
    public function getCantonId()
    {
        return $this->cantonId;
    }

    /**
     * @return string
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * @return int
     */
    public function getDetalleHistorialId()
    {
        return $this->detalleHistorialId;
    }

    /**
     * @return string
     */
    public function getFeCreacionHis()
    {
        return $this->feCreacionHis;
    }

    /**
     * @return string
     */
    public function getUsrCreacionHis()
    {
        return $this->usrCreacionHis;
    }

    /**
     * @return string
     */
    public function getObservacionHistorial()
    {
        return $this->observacionHistorial;
    }

    /**
     * @return int
     */
    public function getDepartamentoOrigenId()
    {
        return $this->departamentoOrigenId;
    }

    /**
     * @return int
     */
    public function getPersonaEmpresaRolIdHis()
    {
        return $this->personaEmpresaRolIdHis;
    }

    /**
     * @return int
     */
    public function getAsignadoIdHis()
    {
        return $this->asignadoIdHis;
    }

    /**
     * @return int
     */
    public function getNumeroTarea()
    {
        return $this->numeroTarea;
    }

    /**
     * @return int
     */
    public function getDetalleId()
    {
        return $this->detalleId;
    }

    /**
     * @return string
     */
    public function getFeCreacion()
    {
        return $this->feCreacion;
    }

    /**
     * @return string
     */
    public function getFeUltMod()
    {
        return $this->feUltMod;
    }

    /**
     * @return string
     */
    public function getUsrCreacion()
    {
        return $this->usrCreacion;
    }

    /**
     * @return string
     */
    public function getIpCreacion()
    {
        return $this->ipCreacion;
    }

    /**
     * @return string
     */
    public function getUsrUltMod()
    {
        return $this->usrUltMod;
    }

    public function setEstado($estado)
    {
        $this->estado = $estado;
    }
}
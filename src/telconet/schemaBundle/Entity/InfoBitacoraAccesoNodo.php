<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoElemento
 *
 * @ORM\Table(name="INFO_BITACORA_ACCESO_NODO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoBitacoraAccesoNodoRepository")
 */
class InfoBitacoraAccesoNodo
{

    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID_BITACORA_ACCESO_NODO", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_BITACORA_ACCESO_NODO", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var InfoElemento
     *
     * @ORM\ManyToOne(targetEntity="InfoElemento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ELEMENTO_ID", referencedColumnName="ID_ELEMENTO")
     * })
     */
    private $elemento;

    /**
     * @var InfoElemento
     *
     * @ORM\ManyToOne(targetEntity="InfoElemento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="NODO_ID", referencedColumnName="ID_ELEMENTO")
     * })
     */
    private $elementoNodo;

    /**
     * @var string $empresaCod
     *
     * @ORM\Column(name="EMPRESA_COD", type="integer", nullable=true)
     */
    private $empresaCod;
    
    /**
     * @var string $nombreNodo
     *
     * @ORM\Column(name="NOMBRE_NODO", type="string", nullable=true)
     */
    private $nombreNodo;

    /**
     * @var string $departmento
     *
     * @ORM\Column(name="DEPARTAMENTO", type="string", nullable=true)
     */
    private $departamento;
    

    /**
     * @var string $tareaId
     *
     * @ORM\Column(name="TAREA_ID", type="integer", nullable=true)
     */
    private $tareaId;

    /**
     * @var string $loginAux
     *
     * @ORM\Column(name="LOGIN_AUX", type="string", nullable=false)
     */
    private $loginAux;

     /**
     * @var string $tecnicoAsignado
     *
     * @ORM\Column(name="TECNICO_ASIGNADO", type="string", nullable=false)
     */
    private $tecnicoAsignado;
    /**
     * @var string $canton
     *
     * @ORM\Column(name="CANTON", type="string", nullable=false)
     */
    private $canton;

    /**
     * @var string $codigos
     *
     * @ORM\Column(name="CODIGOS", type="string", nullable=true)
     */
    private $codigos;

    /**
     * @var string $motivo
     *
     * @ORM\Column(name="MOTIVO", type="string", nullable=true)
     */
    private $motivo;

    /**
     *
     * @var string $observacion
     *
     * @ORM\Column(name="OBSERVACION", type="string", nullable=true)
     */
    private $observacion;

    /**
     *
     * @var string $estadoNodoInicial
     *
     * @ORM\Column(name="ESTADO_NODO_INICIAL", type="string", nullable=true)
     */
    private $estadoNodoInicial;

    /**
     *
     * @var string $estadoNodoFinal
     *
     * @ORM\Column(name="ESTADO_NODO_FINAL", type="string", nullable=true)
     */
    private $estadoNodoFinal;

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
     * @var string $usrUltMod
     *
     * @ORM\Column(name="USR_ULT_MOD", type="string", nullable=true)
     */
    private $usrUltMod;

    /**
     * @var datetime $feCreacion
     *
     * @ORM\Column(name="FE_CREACION", type="datetime", nullable=false)
     */
    private $feCreacion;

    /**
     * @var string $telefono
     *
     * @ORM\Column(name="TELEFONO", type="string", nullable=true)
     */
    private $telefono;

    /**
     * @var datetime $feUltMod
     *
     * @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=true)
     */
    private $feUltMod;

    /**
     * @var string $asginadoPIN
     *
     */
    private $asignadoPIN;

    /**
     * @var boolean $verificarEstadoNodo
     *
     */
    private $verificarEstadoNodo;

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
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get elementoNombre
     *
     * @return string
     */
    public function getElementoNombre()
    {
        if (!empty($this->elemento))
        {
            return $this->elemento->getNombreElemento();
        }
        return 'NA';
    }

    /**
     * Get elemento
     *
     * @return telconet\schemaBundle\Entity\InfoElemento
     */
    public function getElemento()
    {
        if (!empty($this->elemento))
        {
            return $this->elemento->getNombreElemento();
        }
        return '';
    }

    /**
     * Set elemento
     *
     * @param telconet\schemaBundle\Entity\InfoElemento $elemento
     */
    public function setElemento($elemento)
    {
        $this->elemento = $elemento;
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
     * Set empresaCod
     *
     * @param integer
     */
    public function setEmpresaCod($empresaCod)
    {
        $this->empresaCod = $empresaCod;
    }

    /**
     * Get departamento
     *
     * @return string
     */
    public function getDepartamento()
    {
        return $this->departamento;
    }

    /**
     * Set departamento
     *
     * @param string $departamentoNombre
     */
    public function setDepartamento($departamentoNombre)
    {
        $this->departamento = $departamentoNombre;
    }

    /**
     * Get tareaId
     *
     * @return integer
     */
    public function getTareaId()
    {
        return $this->tareaId;
    }

    /**
     * Set tareaId
     *
     * @param integer $tareaId
     */
    public function setTareaId($tareaId)
    {
        $this->tareaId = $tareaId;
    }

    /**
     * Get loginAux
     *
     * @return string
     */
    public function getLoginAux()
    {
        return $this->loginAux;
    }

    /**
     * Set loginAux
     *
     * @param string $loginAux
     */
    public function setLoginAux($loginAux)
    {
        $this->loginAux = $loginAux;
    }
    /**
     * Get tecnicoAsignado
     *
     * @return string
     */
    public function getTecnicoAsignado()
    {
        return $this->tecnicoAsignado;
    }

    /**
     * Set tecnicoAsignado
     *
     * @param string $loginAux
     */
    public function setTecnicoAsignado($tecnicoAsignado)
    {
        $this->tecnicoAsignado = $tecnicoAsignado;
    }

    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * Set telefono
     *
     * @param string $telefono
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;
    }

    /**
     * Get motivo
     *
     * @return string
     */
    public function getMotivo()
    {
        return $this->motivo;
    }

    /**
     * Set motivo
     *
     * @param string $motivo
     */
    public function setMotivo($motivo)
    {
        $this->motivo = $motivo;
    }

    /**
     * Get estado
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
     * Get estadoNodoInicial
     *
     * @return string
     */
    public function getEstadoNodoInicial()
    {
        return $this->estadoNodoInicial;
    }

    /**
     * Set estadoNodoInicial
     *
     * @param string $estadoNodoInicial
     */
    public function setEstadoNodoInicial($data)
    {
        $this->estadoNodoInicial = $data;
    }

    /**
     * Get estadoNodoFinal
     *
     * @return string
     */
    public function getEstadoNodoFinal()
    {
        return $this->estadoNodoFinal;
    }

    /**
     * Set estadoNodoFinal
     *
     * @param string $estadoNodoFinal
     */
    public function setEstadoNodoFinal($data)
    {
        $this->estadoNodoFinal = $data;
    }

    /**
     * Get estado
     *
     * Cerrada | Abierta
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
     * @param string $motivo
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    /**
     * Get canton
     *
     * @return string
     */
    public function getCanton()
    {
        return $this->canton;
    }

    /**
     * Set canton
     *
     * @param string $motivo
     */
    public function setCanton($canton)
    {
        $this->canton = $canton;
    }

    /**
     * Get codigos
     *
     * @return string
     *
     */
    public function getCodigos()
    {
        return $this->codigos;
    }

    /**
     * Set codigos
     *
     * @param string
     */
    public function setCodigos($codigos)
    {
        $this->codigos = $codigos;
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
     * @param string
     */
    public function setUsrCreacion($usrCreacion)
    {
        $this->usrCreacion = $usrCreacion;
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
     * @param string
     */
    public function setUsrUltMod($usrUltMod)
    {
        $this->usrUltMod = $usrUltMod;
    }

    /**
     * Get feCreacion
     *
     * @return string
     */
    public function getFeCreacion()
    {
        return $this->feCreacion->format('Y-m-d H:i:s');
    }

    /**
     * Set feCreacion
     *
     * @param datetime
     */
    public function setFeCreacion($feCreacion)
    {
        $this->feCreacion = $feCreacion;
    }

    /**
     * Get feUltMod
     *
     * @return string
     */
    public function getFeUltMod()
    {
        if (!empty($this->feUltMod))
        {
            return $this->feUltMod->format('Y-m-d H:i:s');
        }
        return '';
    }

    /**
     * Set feUltMod
     *
     * @param datetime
     */
    public function setFeUltMod($feUltMod)
    {
        $this->feUltMod = $feUltMod;
    }


    /**
     * Get asignadoPIN
     *
     * @return string
     */
    public function getAsignadoPIN()
    {
        return $this->asignadoPIN; 
    }

    public function getVerificarEstadoNodo()
    {
        return $this->verificarEstadoNodo;
    }

    /**
     * Get elementoNodoNombre
     *
     * @return string
     */
    public function getElementoNodoNombre()
    {
        if (!empty($this->elementoNodo))
        {
            return $this->elementoNodo->getNombreElemento();
        }
        return '';
    }

    /**
     * Get elementoNodo
     *
     * @return telconet\schemaBundle\Entity\InfoElemento
     */
    public function getElementoNodo()
    {
        return $this->elementoNodo;
    }

    /**
     * Set elementoNodo
     *
     * @param telconet\schemaBundle\Entity\InfoElemento $elementoNodo
     */
    public function setElementoNodo($elementoNodo)
    {
        $this->elementoNodo = $elementoNodo;
    }

    /**
     * Get nombreNodo
     *
     * @return string
     */
    public function getNombreNodo()
    {
        return $this->nombreNodo;
    }

    /**
     * Set nombreNodo
     *
     * @param string $nombreNodo
     */
    public function setnombreNodo($nombreNodo)
    {
        $this->nombreNodo = $nombreNodo;
    }
}

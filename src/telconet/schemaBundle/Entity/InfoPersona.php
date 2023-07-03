<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoPersona
 *
 * @ORM\Table(name="INFO_PERSONA")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoPersonaRepository")
 */
class InfoPersona
{

    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID_PERSONA", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_PERSONA", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var AdmiTitulo
     *
     * @ORM\ManyToOne(targetEntity="AdmiTitulo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="TITULO_ID", referencedColumnName="ID_TITULO")
     * })
     */
    private $tituloId;

    /**
     * @var string $calificacionCrediticia
     *
     * @ORM\Column(name="CALIFICACION_CREDITICIA", type="string", nullable=true)
     */
    private $calificacionCrediticia;

    /**
     * @var string $origenProspecto
     *
     * @ORM\Column(name="ORIGEN_PROSPECTO", type="string", nullable=false)
     */
    private $origenProspecto;

    /**
     * @var string $tipoIdentificacion
     *
     * @ORM\Column(name="TIPO_IDENTIFICACION", type="string", nullable=true)
     */
    private $tipoIdentificacion;

    /**
     * @var string $identificacionCliente
     *
     * @ORM\Column(name="IDENTIFICACION_CLIENTE", type="string", nullable=true)
     */
    private $identificacionCliente;

    /**
     * @var string $tipoEmpresa
     *
     * @ORM\Column(name="TIPO_EMPRESA", type="string", nullable=true)
     */
    private $tipoEmpresa;

    /**
     * @var string $estadoCivil
     *
     * @ORM\Column(name="ESTADO_CIVIL", type="string", nullable=true)
     */
    private $estadoCivil;

    /**
     * @var string $tipoTributario
     *
     * @ORM\Column(name="TIPO_TRIBUTARIO", type="string", nullable=true)
     */
    private $tipoTributario;

    /**
     * @var string $nombres
     *
     * @ORM\Column(name="NOMBRES", type="string", nullable=true)
     */
    private $nombres;

    /**
     * @var string $apellidos
     *
     * @ORM\Column(name="APELLIDOS", type="string", nullable=true)
     */
    private $apellidos;

    /**
     * @var string $razonSocial
     *
     * @ORM\Column(name="RAZON_SOCIAL", type="string", nullable=true)
     */
    private $razonSocial;

    /**
     * @var string $representanteLegal
     *
     * @ORM\Column(name="REPRESENTANTE_LEGAL", type="string", nullable=true)
     */
    private $representanteLegal;

    /**
     * @var string $nacionalidad
     *
     * @ORM\Column(name="NACIONALIDAD", type="string", nullable=true)
     */
    private $nacionalidad;

    /**
     * @var DATE $fechaNacimiento
     *
     * @ORM\Column(name="FECHA_NACIMIENTO", type="date", nullable=true)
     */
    private $fechaNacimiento;

    /**
     * @var string $direccion
     *
     * @ORM\Column(name="DIRECCION", type="string", nullable=true)
     */
    private $direccion;

    /**
     * @var string $login
     *
     * @ORM\Column(name="LOGIN", type="string", nullable=true)
     */
    private $login;

    /**
     * @var string $cargo
     *
     * @ORM\Column(name="CARGO", type="string", nullable=true)
     */
    private $cargo;

    /**
     * @var string $direccionTributaria
     *
     * @ORM\Column(name="DIRECCION_TRIBUTARIA", type="string", nullable=true)
     */
    private $direccionTributaria;

    /**
     * @var string $genero
     *
     * @ORM\Column(name="GENERO", type="string", nullable=true)
     */
    private $genero;

    /**
     * @var string $estado
     *
     * @ORM\Column(name="ESTADO", type="string", nullable=true)
     */
    private $estado;

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
     * @var string $ipCreacion
     *
     * @ORM\Column(name="IP_CREACION", type="string", nullable=true)
     */
    private $ipCreacion;
//cambios DINARDARP - se agrega campo origenes de ingresos
    /**
     * @var string $origenIngresos
     *
     * @ORM\Column(name="ORIGEN_INGRESOS", type="string", nullable=true)
     */
    private $origenIngresos;

    /**
     * @var string $origenWeb
     *
     * @ORM\Column(name="ORIGEN_WEB", type="string", nullable=true)
     */
    private $origenWeb;

    /**
     * @var string $empresaExterna
     *
     */
    private $empresaExterna;
    
    /**
     * @var string $contribuyenteEspecial
     *
     * @ORM\Column(name="CONTRIBUYENTE_ESPECIAL", type="string", nullable=true)
     */
    private $contribuyenteEspecial;  
    
    /**
     * @var string $pagaIva
     *
     * @ORM\Column(name="PAGA_IVA", type="string", nullable=true)
     */
    private $pagaIva;  
    
    /**
     * @var string $numeroConadis
     *
     * @ORM\Column(name="NUMERO_CONADIS", type="string", nullable=true)
     */
    private $numeroConadis;
    
    /**
     * @var AdmiPais
     *
     * @ORM\ManyToOne(targetEntity="AdmiPais")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="PAIS_ID", referencedColumnName="ID_PAIS")
     * })
     */
    private $paisId;

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
     * Get tituloId
     *
     * @return telconet\schemaBundle\Entity\AdmiTitulo
     */
    public function getTituloId()
    {
        return $this->tituloId;
    }

    /**
     * Set tituloId
     *
     * @param telconet\schemaBundle\Entity\AdmiTitulo $tituloId
     */
    public function setTituloId(\telconet\schemaBundle\Entity\AdmiTitulo $tituloId)
    {
        $this->tituloId = $tituloId;
    }

    /**
     * Get calificacionCrediticia
     *
     * @return string
     */
    public function getCalificacionCrediticia()
    {
        return $this->calificacionCrediticia;
    }

    /**
     * Set calificacionCrediticia
     *
     * @param string $calificacionCrediticia
     */
    public function setCalificacionCrediticia($calificacionCrediticia)
    {
        $this->calificacionCrediticia = $calificacionCrediticia;
    }

    /**
     * Get origenProspecto
     *
     * @return string
     */
    public function getOrigenProspecto()
    {
        return $this->origenProspecto;
    }

    /**
     * Set origenProspecto
     *
     * @param string $origenProspecto
     */
    public function setOrigenProspecto($origenProspecto)
    {
        $this->origenProspecto = $origenProspecto;
    }

    /**
     * Get tipoIdentificacion
     *
     * @return string
     */
    public function getTipoIdentificacion()
    {
        return $this->tipoIdentificacion;
    }

    /**
     * Set tipoIdentificacion
     *
     * @param string $tipoIdentificacion
     */
    public function setTipoIdentificacion($tipoIdentificacion)
    {
        $this->tipoIdentificacion = $tipoIdentificacion;
    }

    /**
     * Get identificacionCliente
     *
     * @return string
     */
    public function getIdentificacionCliente()
    {
        return $this->identificacionCliente;
    }

    /**
     * Set identificacionCliente
     *
     * @param string $identificacionCliente
     */
    public function setIdentificacionCliente($identificacionCliente)
    {
        $this->identificacionCliente = $identificacionCliente;
    }

    /**
     * Get tipoEmpresa
     *
     * @return string
     */
    public function getTipoEmpresa()
    {
        return $this->tipoEmpresa;
    }

    /**
     * Set tipoEmpresa
     *
     * @param string $tipoEmpresa
     */
    public function setTipoEmpresa($tipoEmpresa)
    {
        $this->tipoEmpresa = $tipoEmpresa;
    }

    /**
     * Get estadoCivil
     *
     * @return string
     */
    public function getEstadoCivil()
    {
        return $this->estadoCivil;
    }

    /**
     * Set estadoCivil
     *
     * @param string $estadoCivil
     */
    public function setEstadoCivil($estadoCivil)
    {
        $this->estadoCivil = $estadoCivil;
    }

    /**
     * Get tipoTributario
     *
     * @return string
     */
    public function getTipoTributario()
    {
        return $this->tipoTributario;
    }

    /**
     * Set tipoTributario
     *
     * @param string $tipoTributario
     */
    public function setTipoTributario($tipoTributario)
    {
        $this->tipoTributario = $tipoTributario;
    }

    /**
     * Get nombres
     *
     * @return string
     */
    public function getNombres()
    {
        return $this->nombres;
    }

    /**
     * Set nombres
     *
     * @param string $nombres
     */
    public function setNombres($nombres)
    {
        $this->nombres = $nombres;
    }

    /**
     * Get apellidos
     *
     * @return string
     */
    public function getApellidos()
    {
        return $this->apellidos;
    }

    /**
     * Set apellidos
     *
     * @param string $apellidos
     */
    public function setApellidos($apellidos)
    {
        $this->apellidos = $apellidos;
    }

    /**
     * Get razonSocial
     *
     * @return string
     */
    public function getRazonSocial()
    {
        return $this->razonSocial;
    }

    /**
     * Set razonSocial
     *
     * @param string $razonSocial
     */
    public function setRazonSocial($razonSocial)
    {
        $this->razonSocial = $razonSocial;
    }

    /**
     * Get representanteLegal
     *
     * @return string
     */
    public function getRepresentanteLegal()
    {
        return $this->representanteLegal;
    }

    /**
     * Set representanteLegal
     *
     * @param string $representanteLegal
     */
    public function setRepresentanteLegal($representanteLegal)
    {
        $this->representanteLegal = $representanteLegal;
    }

    /**
     * Get nacionalidad
     *
     * @return string
     */
    public function getNacionalidad()
    {
        return $this->nacionalidad;
    }

    /**
     * Set nacionalidad
     *
     * @param string $nacionalidad
     */
    public function setNacionalidad($nacionalidad)
    {
        $this->nacionalidad = $nacionalidad;
    }

    /**
     * Get fechaNacimiento
     *
     * @return 
     */
    public function getFechaNacimiento()
    {
        return $this->fechaNacimiento;
    }

    /**
     * Set fechaNacimiento
     *
     * @param  $fechaNacimiento
     */
    public function setFechaNacimiento($fechaNacimiento)
    {
        $this->fechaNacimiento = $fechaNacimiento;
    }

    /**
     * Get direccion
     *
     * @return string
     */
    public function getDireccion()
    {
        return $this->direccion;
    }

    /**
     * Set direccion
     *
     * @param string $direccion
     */
    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;
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
     * Get cargo
     *
     * @return string
     */
    public function getCargo()
    {
        return $this->cargo;
    }

    /**
     * Set cargo
     *
     * @param string $cargo
     */
    public function setCargo($cargo)
    {
        $this->cargo = $cargo;
    }

    /**
     * Get direccionTributaria
     *
     * @return string
     */
    public function getDireccionTributaria()
    {
        return $this->direccionTributaria;
    }

    /**
     * Set direccionTributaria
     *
     * @param string $direccionTributaria
     */
    public function setDireccionTributaria($direccionTributaria)
    {
        $this->direccionTributaria = $direccionTributaria;
    }

    /**
     * Get genero
     *
     * @return string
     */
    public function getGenero()
    {
        return $this->genero;
    }

    /**
     * Set genero
     *
     * @param string $genero
     */
    public function setGenero($genero)
    {
        $this->genero = $genero;
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
     * Get origenIngresos
     *
     * @return string
     */
    public function getOrigenIngresos()
    {
        return $this->origenIngresos;
    }

    /**
     * Set origenIngresos
     *
     * @param string $origenIngresos
     */
    public function setOrigenIngresos($origenIngresos)
    {
        $this->origenIngresos = $origenIngresos;
        return $this;
    }

    /**
     * Get origenWeb
     *
     * @return string
     */
    public function getOrigenWeb()
    {
        return $this->origenWeb;
    }

    /**
     * Set origenWeb
     *
     * @param string $origenWeb
     */
    public function setOrigenWeb($origenWeb)
    {
        $this->origenWeb = $origenWeb;
    }

    /**
     * Get empresaExterna
     *
     * @return string
     */
    public function getEmpresaExterna()
    {
        return $this->empresaExterna;
    }

    /**
     * Set empresaExterna
     *
     * @param string $empresaExterna
     */
    public function setEmpresaExterna($empresaExterna)
    {
        $this->empresaExterna = $empresaExterna;
    }
    
    /**
     * Get contribuyenteEspecial
     *
     * @return string
     */
    public function getContribuyenteEspecial()
    {
        return $this->contribuyenteEspecial;
    }

    /**
     * Set contribuyenteEspecial
     *
     * @param string $contribuyenteEspecial
     */
    public function setContribuyenteEspecial($contribuyenteEspecial)
    {
        $this->contribuyenteEspecial = $contribuyenteEspecial;
    }    

    
    /**
     * Get pagaIva
     *
     * @return string
     */
    public function getPagaIva()
    {
        return $this->pagaIva;
    }

    /**
     * Set pagaIva
     *
     * @param string $pagaIva
     */
    public function setPagaIva($pagaIva)
    {
        $this->pagaIva = $pagaIva;
    }
    
    
    /**
     * Get numeroConadis
     *
     * @return string
     */
    public function getNumeroConadis()
    {
        return $this->numeroConadis;
    }

    /**
     * Set numeroConadis
     *
     * @param string $numeroConadis
     */
    public function setNumeroConadis($numeroConadis)
    {
        $this->numeroConadis = $numeroConadis;
    }

    /**
     * Get paisId
     *
     * @return telconet\schemaBundle\Entity\AdmiPais
     */
    public function getPaisId()
    {
        return $this->paisId;
    }
    
    /**
     * Set paisId
     *
     * @param telconet\schemaBundle\Entity\AdmiPais $paisId
     */
    public function setPaisId(\telconet\schemaBundle\Entity\AdmiPais $paisId)
    {
        $this->paisId = $paisId;
    }
    
    /**
     * Get toString
     *
     * @return String 
     */
    public function __toString()
    {
        if($this->razonSocial != "")
        {
            return $this->razonSocial;
        }
        else if($this->nombres != "" && $this->apellidos != "")
        {
            return $this->nombres . " " . $this->apellidos;
        }
        else if($this->representanteLegal != "")
        {
            return $this->representanteLegal;
        }
        else
        {
            return "";
        }
    }

    /**
     * @ORM\OneToMany(targetEntity="InfoPersonaEmpresaRol", mappedBy="personaId")
     */
    private $persona_rol;

    public function __construct()
    {
        $this->persona_rol = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function addPersonaRol(\telconet\schemaBundle\Entity\InfoPersonaEmpresaRol $persona_rol)
    {
        $this->persona_rol[] = $persona_rol;
    }

    public function getPersonaRol()
    {
        return $this->persona_rol;
    }

    public function getInformacionPersona()
    {
        if($this->nombres != "" && $this->apellidos != "")
        {
            return $this->nombres . " " . $this->apellidos;
        }
        else
        {
            return $this->razonSocial;
        }
    }

}

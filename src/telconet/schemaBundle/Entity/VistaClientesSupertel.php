<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\VistaClientesSupertel
 *
 * @ORM\Table(name="VISTA_CLIENTES_SUPERTEL")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\VistaClientesSupertelRepository")
 */
class VistaClientesSupertel
{
	 
	/**
	* @var integer $id
	*
	* @ORM\Column(name="ID_SERVICIO_HISTORIAL", type="integer", nullable=false)
	* @ORM\Id
	*/		
			
	private $id;	

	/**
	* @var integer $idServicio
	*
	* @ORM\Column(name="ID_SERVICIO", type="integer", nullable=false)
	*/		
			
	private $idServicio;

	/**
	* @var integer $idActivacion
	*
	* @ORM\Column(name="ID_ACTIVACION", type="integer", nullable=false)
	*/		
			
	private $idActivacion;
	
	/**
	* @var integer $idPunto
	*
	* @ORM\Column(name="ID_PUNTO", type="integer", nullable=false)
	*/		
			
	private $idPunto;

	/**
	* @var integer $idPersona
	*
	* @ORM\Column(name="ID_PERSONA", type="integer", nullable=false)
	*/		
			
	private $idPersona;

	/**
	* @var integer $idPersonaRol
	*
	* @ORM\Column(name="ID_PERSONA_ROL", type="integer", nullable=false)
	*/		
			
	private $idPersonaRol;

	/**
	* @var integer $idEmpresaRol
	*
	* @ORM\Column(name="ID_EMPRESA_ROL", type="integer", nullable=false)
	*/		
			
	private $idEmpresaRol;

	/**
	* @var integer $empresaCod
	*
	* @ORM\Column(name="EMPRESA_COD", type="integer", nullable=false)
	*/		
			
	private $empresaCod;

	/**
	* @var integer $idTipoNegocio
	*
	* @ORM\Column(name="ID_TIPO_NEGOCIO", type="integer", nullable=false)
	*/		
			
	private $idTipoNegocio;

	/**
	* @var integer $idSector
	*
	* @ORM\Column(name="ID_SECTOR", type="integer", nullable=false)
	*/		
			
	private $idSector;

	/**
	* @var integer $idParroquia
	*
	* @ORM\Column(name="ID_PARROQUIA", type="integer", nullable=false)
	*/		
			
	private $idParroquia;
	  
	/**
	* @var integer $idCanton
	*
	* @ORM\Column(name="ID_CANTON", type="integer", nullable=false)
	*/		
			
	private $idCanton;

	/**
	* @var integer $idContrato
	*
	* @ORM\Column(name="ID_CONTRATO", type="integer", nullable=false)
	*/		
			
	private $idContrato;

	/**
	* @var integer $idTipoContrato
	*
	* @ORM\Column(name="ID_TIPO_CONTRATO", type="integer", nullable=false)
	*/		
			
	private $idTipoContrato;

	/**
	* @var integer $idPlan
	*
	* @ORM\Column(name="ID_PLAN", type="integer", nullable=false)
	*/		
			
	private $idPlan;	

	/**
	* @var integer $login
	*
	* @ORM\Column(name="LOGIN", type="string", nullable=false)
	*/		
			
	private $login;	
		
	/**
	* @var string $nombreCanton
	*
	* @ORM\Column(name="NOMBRE_CANTON", type="string", nullable=false)
	*/		
				
	private $nombreCanton;
		
	/**
	* @var string $nombreParroquia
	*
	* @ORM\Column(name="NOMBRE_PARROQUIA", type="string", nullable=false)
	*/		
				
	private $nombreParroquia;
		
	/**
	* @var string $nombreSector
	*
	* @ORM\Column(name="NOMBRE_SECTOR", type="string", nullable=false)
	*/		
				
	private $nombreSector;
		
	/**
	* @var string $nombres
	*
	* @ORM\Column(name="NOMBRES", type="string", nullable=false)
	*/		
				
	private $nombres;
		
	/**
	* @var string $apellidos
	*
	* @ORM\Column(name="APELLIDOS", type="string", nullable=false)
	*/		
				
	private $apellidos;
		
	/**
	* @var string $razonSocial
	*
	* @ORM\Column(name="RAZON_SOCIAL", type="string", nullable=false)
	*/		
				
	private $razonSocial;	
		
	/**
	* @var string $direccionPunto
	*
	* @ORM\Column(name="DIRECCION_PUNTO", type="string", nullable=false)
	*/		
				
	private $direccionPunto;
		
	/**
	* @var string $tipoContrato
	*
	* @ORM\Column(name="TIPO_CONTRATO", type="string", nullable=false)
	*/		
				
	private $tipoContrato;
		
	/**
	* @var string $ultimoEstado
	*
	* @ORM\Column(name="ULTIMO_ESTADO", type="string", nullable=false)
	*/		
				
	private $ultimoEstado;
		
	/**
	* @var string $tipoCuenta
	*
	* @ORM\Column(name="TIPO_CUENTA", type="string", nullable=false)
	*/		
				
	private $tipoCuenta;
	
	/**
	* @var integer $interfaceElementoId
	*
	* @ORM\Column(name="INTERFACE_ELEMENTO_ID", type="integer", nullable=false)
	*/		
			
	private $interfaceElementoId;
	
	/**
	* @var integer $ultimaMillaId
	*
	* @ORM\Column(name="ULTIMA_MILLA_ID", type="integer", nullable=false)
	*/		
			
	private $ultimaMillaId;
		
	/**
	* @var string $servicio
	*
	* @ORM\Column(name="SERVICIO", type="string", nullable=false)
	*/		
				
	private $servicio;
	
	/**
	* @var datetime $fechaUltimoEstado
	*
	* @ORM\Column(name="FECHA_ULTIMO_ESTADO", type="datetime", nullable=false)
	*/		
				
	private $fechaUltimoEstado;
	
	/**
	* @var string $usuarioUltimoEstado
	*
	* @ORM\Column(name="USUARIO_ULTIMO_ESTADO", type="string", nullable=false)
	*/		
				
	private $usuarioUltimoEstado;
		
	/**
	* @var string $observacion
	*
	* @ORM\Column(name="OBSERVACION", type="string", nullable=false)
	*/		
				
	private $observacion;
		
	/**
	* @var string $usrVendedor
	*
	* @ORM\Column(name="USR_VENDEDOR", type="string", nullable=false)
	*/		
				
	private $usrVendedor;		
		
	/**
	* @var string $latitud
	*
	* @ORM\Column(name="LATITUD", type="string", nullable=false)
	*/		
				
	private $latitud;
		
	/**
	* @var string $longitud
	*
	* @ORM\Column(name="LONGITUD", type="string", nullable=false)
	*/		
				
	private $longitud;
	
	/**
	* @var datetime $fechaActivacion
	*
	* @ORM\Column(name="FECHA_ACTIVACION", type="datetime", nullable=false)
	*/		
				
	private $fechaActivacion;
	
	/**
	* @var string $usuarioActivacion
	*
	* @ORM\Column(name="USUARIO_ACTIVACION", type="string", nullable=false)
	*/		
				
	private $usuarioActivacion;
		
	/**
	* @var string $observacionActivacion
	*
	* @ORM\Column(name="OBSERVACION_ACTIVACION", type="string", nullable=false)
	*/		
				
	private $observacionActivacion;
	
	/**
	* Get id
	*
	* @return integer
	*/		
				
	public function getId(){
		return $this->id; 
	}

	/**
	* Get idServicio
	*
	* @return integer
	*/		
				
	public function getIdServicio(){
		return $this->idServicio; 
	}

	/**
	* Get idActivacion
	*
	* @return integer
	*/		
				
	public function getIdActivacion(){
		return $this->idActivacion; 
	}

	/**
	* Get idPunto
	*
	* @return integer
	*/		
				
	public function getIdPunto(){
		return $this->idPunto; 
	}

	/**
	* Get idPersona
	*
	* @return integer
	*/		
				
	public function getIdPersona(){
		return $this->idPersona; 
	}

	/**
	* Get idPersonaRol
	*
	* @return integer
	*/		
				
	public function getIdPersonaRol(){
		return $this->idPersonaRol; 
	}

	/**
	* Get idEmpresaRol
	*
	* @return integer
	*/		
				
	public function getIdEmpresaRol(){
		return $this->idEmpresaRol; 
	}

	/**
	* Get empresaCod
	*
	* @return string
	*/		
				
	public function getEmpresaCod(){
		return $this->empresaCod; 
	}

	/**
	* Get idTipoNegocio
	*
	* @return integer
	*/		
				
	public function getIdTipoNegocio(){
		return $this->idTipoNegocio; 
	}

	/**
	* Get idSector
	*
	* @return integer
	*/		
				
	public function getIdSector(){
		return $this->idSector; 
	}

	/**
	* Get idParroquia
	*
	* @return integer
	*/		
				
	public function getIdParroquia(){
		return $this->idParroquia; 
	}

	/**
	* Get idCanton
	*
	* @return integer
	*/		
				
	public function getIdCanton(){
		return $this->idCanton; 
	}

	/**
	* Get idContrato
	*
	* @return integer
	*/		
				
	public function getIdContrato(){
		return $this->idContrato; 
	}

	/**
	* Get idTipoContrato
	*
	* @return integer
	*/		
				
	public function getIdTipoContrato(){
		return $this->idTipoContrato; 
	}

	/**
	* Get idPlan
	*
	* @return integer
	*/		
				
	public function getIdPlan(){
		return $this->idPlan; 
	}

	/**
	* Get login
	*
	* @return string
	*/		
				
	public function getLogin(){
		return $this->login; 
	}

	/**
	* Get nombreCanton
	*
	* @return string
	*/		
				
	public function getNombreCanton(){
		return $this->nombreCanton; 
	}

	/**
	* Get nombreParroquia
	*
	* @return string
	*/		
				
	public function getNombreParroquia(){
		return $this->nombreParroquia; 
	}

	/**
	* Get nombreSector
	*
	* @return string
	*/		
				
	public function getNombreSector(){
		return $this->nombreSector; 
	}

	/**
	* Get nombres
	*
	* @return string
	*/		
				
	public function getNombres(){
		return $this->nombres; 
	}

	/**
	* Get apellidos
	*
	* @return string
	*/		
				
	public function getApellidos(){
		return $this->apellidos; 
	}

	/**
	* Get razonSocial
	*
	* @return string
	*/		
				
	public function getRazonSocial(){
		return $this->razonSocial; 
	}

	/**
	* Get direccionPunto
	*
	* @return string
	*/		
				
	public function getDireccionPunto(){
		return $this->direccionPunto; 
	}

	/**
	* Get tipoContrato
	*
	* @return string
	*/		
				
	public function getTipoContrato(){
		return $this->tipoContrato; 
	}

	/**
	* Get ultimoEstado
	*
	* @return string
	*/		
				
	public function getUltimoEstado(){
		return $this->ultimoEstado; 
	}

	/**
	* Get tipoCuenta
	*
	* @return string
	*/		
				
	public function getTipoCuenta(){
		return $this->tipoCuenta; 
	}

	/**
	* Get interfaceElementoId
	*
	* @return integer
	*/		
				
	public function getInterfaceElementoId(){
		return $this->interfaceElementoId; 
	}

	/**
	* Get ultimaMillaId
	*
	* @return integer
	*/		
				
	public function getUltimaMillaId(){
		return $this->ultimaMillaId; 
	}

	/**
	* Get servicio
	*
	* @return string
	*/		
				
	public function getServicio(){
		return $this->servicio; 
	}

	/**
	* Get fechaUltimoEstado
	*
	* @return datetime
	*/		
				
	public function getFechaUltimoEstado(){
		return $this->fechaUltimoEstado; 
	}
	
	/**
	* Get usuarioUltimoEstado
	*
	* @return string
	*/		
				
	public function getUsuarioUltimoEstado(){
		return $this->usuarioUltimoEstado; 
	}
	
	/**
	* Get observacion
	*
	* @return string
	*/		
				
	public function getObservacion(){
		return $this->observacion; 
	}

	/**
	* Get usrVendedor
	*
	* @return string
	*/		
				
	public function getUsrVendedor(){
		return $this->usrVendedor; 
	}

	/**
	* Get latitud
	*
	* @return string
	*/		
				
	public function getLatitud(){
		return $this->latitud; 
	}

	/**
	* Get longitud
	*
	* @return string
	*/		
				
	public function getLongitud(){
		return $this->longitud; 
	}
	
	/**
	* Get fechaActivacion
	*
	* @return datetime
	*/		
				
	public function getFechaActivacion(){
		return $this->fechaActivacion; 
	}
	
	/**
	* Get usuarioActivacion
	*
	* @return string
	*/		
				
	public function getUsuarioActivacion(){
		return $this->usuarioActivacion; 
	}
	
	/**
	* Get observacionActivacion
	*
	* @return string
	*/		
				
	public function getObservacionActivacion(){
		return $this->observacionActivacion; 
	}
	
	public function __toString()
	{
			return $this->ultimoEstado;
	}

}
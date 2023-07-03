<?php

namespace telconet\comercialBundle\WebService\ComercialMobileWSResponse;

use BeSimple\SoapBundle\ServiceDefinition\Annotation as Soap;
use telconet\comercialBundle\WebService\ComercialMobileWSController;

/**
 * Entidad PersonaResponseNew
 * @see ComercialMobileWSController
 * @author epin
 */
 
 class PersonaResponseNew 
{
 
    /**
    * @Soap\ComplexType("int")
    */
    private $id;
    
    /**
    * @Soap\ComplexType("string")
    */
    private $tituloId;
      
    /**
    * @Soap\ComplexType("string")
    */
    private $tipoIdentificacion;
    
    /**
    * @Soap\ComplexType("string")
    */
    private $identificacionCliente;
    
    /**
    * @Soap\ComplexType("string")
    */
    private $tipoEmpresa;
    
    /**
    * @Soap\ComplexType("string")
    */
    private $estadoCivil;
    
    /**
    * @Soap\ComplexType("string")
    */
    private $tipoTributario;
    
    /**
    * @Soap\ComplexType("string")
    */
    private $nombres;
    
    /**
    * @Soap\ComplexType("string")
    */
    private $apellidos; 
    
    /**
    * @Soap\ComplexType("string")
    */
    private $razonSocial;
    
    /**
    * @Soap\ComplexType("string")
    */
    private $representanteLegal;
    
    /**
    * @Soap\ComplexType("string")
    */
    private $nacionalidad;
    
    /**
    * @Soap\ComplexType("string")
    */
    private $fechaNacimiento;
    
    /**
    * @Soap\ComplexType("string")
    */
    private $direccion;
    
    /**
    * @Soap\ComplexType("string")
    */
    
    /**
    * @Soap\ComplexType("string")
    */
    private $direccionTributaria;
    
    /**
    * @Soap\ComplexType("string")
    */
    private $genero;
    
    /**
    * @Soap\ComplexType("string")
    */
    private $estado;
    
    /**
    * @Soap\ComplexType("string")
    */
    private $feCreacion;
    
    /**
    * @Soap\ComplexType("string")
    */
    private $usrCreacion;
    
    /**
    * @Soap\ComplexType("string")
    */
    private $ipCreacion;

    /**
    * @Soap\ComplexType("telconet\comercialBundle\WebService\ComercialMobileWSResponse\FormaContactoResponse[]")
    */
    private $formasContacto;
    
    /**
    * @Soap\ComplexType("[]")
    */
    private $puntos;
    
    /**
    * @Soap\ComplexType("[]")
    */
    private $contrato;    
        
    public function  getId() 
    {
        return $this->id;
    }

    public function setId($intId) 
    {
	$this->id = $intId;
    }

    public function getTituloId() 
    {
	return $this->tituloId;
    }

    public function setTituloId($intTituloId) 
    {
        $this->tituloId = $intTituloId;
    }

    public function  getTipoIdentificacion() 
    {
        return $this->tipoIdentificacion;
    }

    public function setTipoIdentificacion($strTipoIdentificacion) 
    {
        $this->tipoIdentificacion = $strTipoIdentificacion;
    }

    public function getIdentificacionCliente() 
    {
        return $this->identificacionCliente;
    }

    public function setIdentificacionCliente($strIdentificacionCliente) 
    {
        $this->identificacionCliente = $strIdentificacionCliente;
    }

    public function getTipoEmpresa() 
    {
        return $this->tipoEmpresa;
    }

    public function setTipoEmpresa($strTipoEmpresa) 
    {
        $this->tipoEmpresa = $strTipoEmpresa;
    }

    public function getEstadoCivil() 
    {
        return $this->estadoCivil;
    }

    public function setEstadoCivil($strEstadoCivil) 
    {
        $this->estadoCivil = $strEstadoCivil;
    }

    public function getTipoTributario() 
    {
        return $this->tipoTributario;
    }

    public function setTipoTributario($strTipoTributario) 
    {
        $this->tipoTributario = $strTipoTributario;
    }

    public function getNombres() 
    {
        return $this->nombres;
    }

    public function setNombres($strNombres) 
    {
        $this->nombres = $strNombres;
    }

    public function getApellidos() 
    {
        return $this->apellidos;
    }

    public function setApellidos($strApellidos) 
    {
        $this->apellidos = $strApellidos;
    }

    public function getRazonSocial() 
    {
	return $this->razonSocial;
    }

    public function setRazonSocial($strRazonSocial) 
    {
        $this->razonSocial = $strRazonSocial;
    }

    public function getRepresentanteLegal() 
    {
        return $this->representanteLegal;
    }

    public function setRepresentanteLegal($strRepresentanteLegal) 
    {
        $this->representanteLegal = $strRepresentanteLegal;
    }

    public function getNacionalidad() 
    {
        return $this->nacionalidad;
    }

    public function setNacionalidad($strNacionalidad) 
    {
        $this->nacionalidad = $strNacionalidad;
    }

    public function getFechaNacimiento() 
    {
        return $this->fechaNacimiento;
    }

    public function setFechaNacimiento($strFechaNacimiento) 
    {
        $this->fechaNacimiento = $strFechaNacimiento;
    }

    public function getDireccion() 
    {
        return $this->direccion;
    }

    public function setDireccion($strDireccion) 
    {
        $this->direccion = $strDireccion;
    }

    public function getDireccionTributaria() 
    {
        return $this->direccionTributaria;
    }

    public function setDireccionTributaria($strDireccionTributaria) 
    {
        $this->direccionTributaria = $strDireccionTributaria;
    }

    public function getGenero() 
    {
        return $this->genero;
    }

    public function setGenero($strGenero) 
    {
        $this->genero = $strGenero;
    }

    public function getEstado() 
    {
	return $this->estado;
    }

    public function setEstado($strEstado) 
    {
        $this->estado = $strEstado;
    }

    public function getFeCreacion() 
    {
        return $this->feCreacion;
    }

    public function setFeCreacion($strFeCreacion) 
    {
	$this->feCreacion = $strFeCreacion;
    }

    public function getUsrCreacion() 
    {
        return $this->usrCreacion;
    }

    public function setUsrCreacion($strUsrCreacion) 
    {
        $this->usrCreacion = $strUsrCreacion;
    }

    public function getIpCreacion() 
    {
        return $this->ipCreacion;
    }

    public function setIpCreacion($strIpCreacion) 
    {
        $this->ipCreacion = $strIpCreacion;
    }

    public function getFormasContacto() 
    {
        return $this->formasContacto;
    }

    public function setFormasContacto($strFormasContacto) 
    {
        $this->formasContacto = $strFormasContacto;
    } 
    
    public function getPuntos() 
    {
        return $this->puntos;
    }

    public function setPuntos($intPuntos) 
    {
        $this->puntos = $intPuntos;
    }  

    public function getContrato() 
    {
	return $this->contrato;
    }

    public function  setContrato($intContrato) 
    {
	$this->contrato = $intContrato;
    }
 }

<?php

namespace telconet\comercialBundle\WebService\ComercialMobileWSResponse;

use BeSimple\SoapBundle\ServiceDefinition\Annotation as Soap;
use telconet\comercialBundle\WebService\ComercialMobileWSController;

/**
 * Entidad PersonaResponse
 * @see ComercialMobileWSController
 * @author wsanchez
 */
 
 class PersonaResponse {
 
    /**
    * @Soap\ComplexType("int")
    */
    private $id;
    
    /**
    * @Soap\ComplexType("string")
    */
    private $tituloId;
    
    
    //private $calificacionCrediticia;
    //private $origenProspecto;
    
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
    //private $login;
    //private $cargo;
    
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

    
    
    public function  getId() {
	    return $this->id;
    }

    public function setId($id) {
	    $this->id = $id;
    }

    public function  getTituloId() {
	    return $this->tituloId;
    }

    public function  setTituloId($tituloId) {
	    $this->tituloId = $tituloId;
    }

    public function  getTipoIdentificacion() {
	    return $this->tipoIdentificacion;
    }

    public function  setTipoIdentificacion($tipoIdentificacion) {
	    $this->tipoIdentificacion = $tipoIdentificacion;
    }

    public function  getIdentificacionCliente() {
	    return $this->identificacionCliente;
    }

    public function  setIdentificacionCliente($identificacionCliente) {
	    $this->identificacionCliente = $identificacionCliente;
    }

    public function  getTipoEmpresa() {
	    return $this->tipoEmpresa;
    }

    public function  setTipoEmpresa($tipoEmpresa) {
	    $this->tipoEmpresa = $tipoEmpresa;
    }

    public function  getEstadoCivil() {
	    return $this->estadoCivil;
    }

    public function  setEstadoCivil($estadoCivil) {
	    $this->estadoCivil = $estadoCivil;
    }

    public function  getTipoTributario() {
	    return $this->tipoTributario;
    }

    public function  setTipoTributario($tipoTributario) {
	    $this->tipoTributario = $tipoTributario;
    }

    public function  getNombres() {
	    return $this->nombres;
    }

    public function  setNombres($nombres) {
	    $this->nombres = $nombres;
    }

    public function  getApellidos() {
	    return $this->apellidos;
    }

    public function  setApellidos($apellidos) {
	    $this->apellidos = $apellidos;
    }

    public function  getRazonSocial() {
	    return $this->razonSocial;
    }

    public function  setRazonSocial($razonSocial) {
	    $this->razonSocial = $razonSocial;
    }

    public function  getRepresentanteLegal() {
	    return $this->representanteLegal;
    }

    public function  setRepresentanteLegal($representanteLegal) {
	    $this->representanteLegal = $representanteLegal;
    }

    public function  getNacionalidad() {
	    return $this->nacionalidad;
    }

    public function  setNacionalidad($nacionalidad) {
	    $this->nacionalidad = $nacionalidad;
    }

    public function  getFechaNacimiento() {
	    return $this->fechaNacimiento;
    }

    public function  setFechaNacimiento($fechaNacimiento) {
	    $this->fechaNacimiento = $fechaNacimiento;
    }

    public function  getDireccion() {
	    return $this->direccion;
    }

    public function  setDireccion($direccion) {
	    $this->direccion = $direccion;
    }

    public function  getDireccionTributaria() {
	    return $this->direccionTributaria;
    }

    public function  setDireccionTributaria($direccionTributaria) {
	    $this->direccionTributaria = $direccionTributaria;
    }

    public function  getGenero() {
	    return $this->genero;
    }

    public function  setGenero($genero) {
	    $this->genero = $genero;
    }

    public function  getEstado() {
	    return $this->estado;
    }

    public function  setEstado($estado) {
	    $this->estado = $estado;
    }

    public function  getFeCreacion() {
	    return $this->feCreacion;
    }

    public function  setFeCreacion($feCreacion) {
	    $this->feCreacion = $feCreacion;
    }

    public function  getUsrCreacion() {
	    return $this->usrCreacion;
    }

    public function  setUsrCreacion($usrCreacion) {
	    $this->usrCreacion = $usrCreacion;
    }

    public function  getIpCreacion() {
	    return $this->ipCreacion;
    }

    public function  setIpCreacion($ipCreacion) {
	    $this->ipCreacion = $ipCreacion;
    }

    public function  getFormasContacto() {
	    return $this->formasContacto;
    }

    public function  setFormasContacto($formasContacto) {
	    $this->formasContacto = $formasContacto;
    } 
    
 }
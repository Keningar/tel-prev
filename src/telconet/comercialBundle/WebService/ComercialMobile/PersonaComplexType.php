<?php

namespace telconet\comercialBundle\WebService\ComercialMobile;

/**
 * ComplexType: Persona encontrada por identificacion, con sus roles y formas de contacto
 *
 * @author ltama
 */
class PersonaComplexType {
    public $id;
    public $tipoEmpresa;
    public $tipoIdentificacion;
    public $tipoTributario;
    public $nacionalidad;
    public $identificacionCliente;
    public $nombres;
    public $apellidos;
    public $tituloId;
    public $direccionTributaria;
    public $genero;
    public $estadoCivil;
    public $fechaNacimiento;
    public $razonSocial;
    public $representanteLegal;
    public $referidoId;
    public $referidoNombre;
    public $formaPagoId;
    public $tipoCuentaId;
    public $bancoTipoCuentaId;
    public $roles;
    public $formasContacto;
    public $datosPersonaEmpresaRol;
    //cambios DINARDARP - se agrega campo origenes de ingresos
    public $origenIngresos;
    public $estado;
    
    /**
     *
     * @param array $datos
     */
    public function __construct($datos) {
        $this->id = $datos['id'];
        $this->nombres = $datos['nombres'];
        $this->apellidos = $datos['apellidos'];
        $this->razonSocial = $datos['razonSocial'];
        $this->tituloId = $datos['tituloId'];
        $this->tipoIdentificacion = $datos['tipoIdentificacion'];
        $this->identificacionCliente = $datos['identificacionCliente'];
        $this->tipoEmpresa = $datos['tipoEmpresa'];
        $this->tipoTributario = $datos['tipoTributario'];
        $this->representanteLegal = $datos['representanteLegal'];
        $this->nacionalidad = $datos['nacionalidad'];
        $this->genero = $datos['genero'];
        $this->direccionTributaria = $datos['direccionTributaria'];
        $this->estadoCivil = $datos['estadoCivil'];
        $this->fechaNacimiento = $datos['fechaNacimiento'];
        $this->referidoId = $datos['referidoId'];
        $this->referidoNombre = $datos['referidoNombre'];
        $this->formaPagoId = $datos['formaPagoId'];
        $this->tipoCuentaId = $datos['tipoCuentaId'];
        $this->bancoTipoCuentaId = $datos['bancoTipoCuentaId'];
        $this->estado = $datos['estado'];
        //cambios DINARDARP - se agrega campo origenes de ingresos
        $this->origenIngresos = $datos['origenIngresos'];
        $this->gravaIva = 'S';
        $this->setRoles(empty($datos['roles']) ? null : $datos['roles']);
        $this->setFormasContacto(empty($datos['formasContacto']) ? null : $datos['formasContacto']);
        $this->setDatosPersonaEmpresaRol(empty($datos['datosPersonaEmpresaRol']) ? null : $datos['datosPersonaEmpresaRol']);
    }
    
    public function setRoles($roles)
    {
        $this->roles = array();
        if (is_array($roles))
        {
            $this->roles = $roles;
        }
        else
        {
            // $this->roles = explode("|", $roles);
            $this->roles = preg_split("[\|]", $roles, -1, PREG_SPLIT_NO_EMPTY);
        }
    }
    
    public function setFormasContacto($formasContacto)
    {
        $this->formasContacto = array();
        if (is_array($formasContacto))
        {
            if ($formasContacto[0] instanceof FormaContactoComplexType)
            {
                $this->formasContacto = $formasContacto;
            }
            else
            {
                foreach ($formasContacto as $formaContacto)
                {
                    $this->formasContacto[] = FormaContactoComplexType::fromPersonaFormaContacto($formaContacto);
                }
            }
        }
    }
    
    public function setDatosPersonaEmpresaRol($datosPersonaEmpresaRol)
    {
        $this->datosPersonaEmpresaRol = array();
        if (is_array($datosPersonaEmpresaRol))
        {
            if ($datosPersonaEmpresaRol[0] instanceof RolComplexType)
            {
                $this->datosPersonaEmpresaRol = $datosPersonaEmpresaRol;
            }
            else
            {
                foreach ($datosPersonaEmpresaRol as $personaEmpresaRol)
                {
                    $this->datosPersonaEmpresaRol[] = RolComplexType::fromPersonaEmpresaRol($personaEmpresaRol);
                }
            }
        }
    }
}
<?php

namespace telconet\comercialBundle\WebService\ComercialMobile;

/**
 * ComplexType: Persona encontrada por identificacion, con sus roles y formas de contacto
 *
 * @author ltama
 *
 * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
 * @version 1.1 18-06-2020 - Se agrega parámetro para los datos de persona jurídica
 */
class PersonaComplexTypeNew 

{
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
    public $arrayRepresentanteLegal;
    public $referidoId;
    public $referidoNombre;
    public $formaPagoId;
    public $tipoCuentaId;
    public $bancoTipoCuentaId;
    public $roles;
    public $formasContacto;
    public $datosPersonaEmpresaRol;
    public $puntos;
    public $contrato;
    //cambios DINARDARP - se agrega campo origenes de ingresos
    public $origenIngresos;
    public $estado;
    public $strConadis;
    public $strContribuyente;
    public $validations;
    public $cargo;
    public $razonComercial;
    public $representanteLegalJuridico;
    public $pointValidations;
    public $totalPuntos;

    /**
     *
     * @param array $datos
     */
    public function __construct($arrayDatos) 
    {
        $this->id                    = $arrayDatos['id'];
        $this->nombres               = $arrayDatos['nombres'];
        $this->apellidos             = $arrayDatos['apellidos'];
        $this->razonSocial           = $arrayDatos['razonSocial'];
        $this->tituloId              = $arrayDatos['tituloId'];
        $this->tipoIdentificacion    = $arrayDatos['tipoIdentificacion'];
        $this->identificacionCliente = $arrayDatos['identificacionCliente'];
        $this->tipoEmpresa           = $arrayDatos['tipoEmpresa'];
        $this->tipoTributario        = $arrayDatos['tipoTributario'];
        $this->representanteLegal    = $arrayDatos['representanteLegal'];
        $this->arrayRepresentanteLegal= $arrayDatos['arrayRepresentanteLegal'];
        $this->nacionalidad          = $arrayDatos['nacionalidad'];
        $this->genero                = $arrayDatos['genero'];
        $this->direccionTributaria   = $arrayDatos['direccionTributaria'];
        $this->estadoCivil           = $arrayDatos['estadoCivil'];
        $this->fechaNacimiento       = $arrayDatos['fechaNacimiento'];
        $this->referidoId            = $arrayDatos['referidoId'];
        $this->referidoNombre        = $arrayDatos['referidoNombre'];
        $this->formaPagoId           = $arrayDatos['formaPagoId'];
        $this->tipoCuentaId          = $arrayDatos['tipoCuentaId'];
        $this->bancoTipoCuentaId     = $arrayDatos['bancoTipoCuentaId'];
        $this->estado                = $arrayDatos['estado'];
        $this->strConadis            = $arrayDatos['numeroConadis'];
        $this->strContribuyente      = $arrayDatos['contribuyenteEspecial'];
        //cambios DINARDARP - se agrega campo origenes de ingresos
        $this->origenIngresos        = $arrayDatos['origenIngresos'];
        $this->gravaIva = 'S';
        $this->cargo                 = $arrayDatos['cargo'];
        $this->razonComercial        = $arrayDatos['razonComercial'];
        $this->fechaRegistroMercantil = $arrayDatos['fechaRegistroMercantil'];
        $this->representanteLegalJuridico = $arrayDatos['representanteLegalJuridico'];
        $this->totalPuntos           = $arrayDatos['totalPuntos'];

        $this->setRoles(empty($arrayDatos['roles']) ? null : $arrayDatos['roles']);
        $this->setFormasContacto(empty($arrayDatos['formasContacto']) ? null : $arrayDatos['formasContacto']);
        $this->setDatosPersonaEmpresaRol(empty($arrayDatos['datosPersonaEmpresaRol']) ? null : $arrayDatos['datosPersonaEmpresaRol']);
        $this->setValidations(empty($arrayDatos['validations']) ? null : $arrayDatos['validations']);
        $this->setPointValidations(empty($arrayDatos['pointValidations']) ? null : $arrayDatos['pointValidations']);

    }
    public function setRoles($arrayRoles)
    {
        $this->roles = array();
        if (is_array($arrayRoles))
        {
            $this->roles = $arrayRoles;
        }
        else
        {
            $this->roles = preg_split("[\|]", $arrayRoles, -1, PREG_SPLIT_NO_EMPTY);
        }
    }
    
    public function setFormasContacto($arrayFormasContacto)
    {
        $this->formasContacto = array();
        if (is_array($arrayFormasContacto))
        {
            if ($arrayFormasContacto[0] instanceof FormaContactoComplexType)
            {
                $this->formasContacto = $arrayFormasContacto;
            }
            else
            {
                foreach ($arrayFormasContacto as $arrayFormaContacto)
                {
                    $this->formasContacto[] = FormaContactoComplexType::fromPersonaFormaContacto($arrayFormaContacto);
                }
            }
        }
    }
    
    public function setDatosPersonaEmpresaRol($arrayDatosPersonaEmpresaRol)
    {
        $this->datosPersonaEmpresaRol = array();
        if (is_array($arrayDatosPersonaEmpresaRol))
        {
            if ($arrayDatosPersonaEmpresaRol[0] instanceof RolComplexType)
            {
                $this->datosPersonaEmpresaRol = $arrayDatosPersonaEmpresaRol;
            }
            else
            {
                foreach ($arrayDatosPersonaEmpresaRol as $arrayPersonaEmpresaRol)
                {
                    $this->datosPersonaEmpresaRol[] = RolComplexType::fromPersonaEmpresaRol($arrayPersonaEmpresaRol);
                }
            }
        }
    }
    public function setPuntos($intPuntos)
    {
        $this->puntos = $intPuntos;    
    }

    public function setContrato($intContrato)
    {
        $this->contrato = $intContrato;    
    }

    public function setValidations($arrayValidations)
    {
        $this->validations = array();
        if (is_array($arrayValidations))
        {
            $this->validations = $arrayValidations;
        }
        else
        {
            $this->validations = preg_split("[\|]", $arrayValidations, -1, PREG_SPLIT_NO_EMPTY);
        }
    }

    public function setPointValidations($arrayValidations)
    {
        $this->pointValidations = array();
        if (is_array($arrayValidations))
        {
            $this->pointValidations = $arrayValidations;
        }
        else
        {
            $this->pointValidations = preg_split("[\|]", $arrayValidations, -1, PREG_SPLIT_NO_EMPTY);
        }
    }
}

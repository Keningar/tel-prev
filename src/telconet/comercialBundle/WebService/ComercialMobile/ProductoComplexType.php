<?php

namespace telconet\comercialBundle\WebService\ComercialMobile;

/**
 * ComplexType: Producto, con sus caracteristicas
 *
 * @author ltama
 */
class ProductoComplexType {
    public $id;
    public $nombreProducto;
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
    public $roles;
    public $formasContacto;
    
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
        $this->setRoles($datos['roles']);
        $this->setFormasContacto($datos['formasContacto']);
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
}
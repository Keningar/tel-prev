<?php

namespace telconet\comercialBundle\WebService\ComercialMobile;

/**
 * ComplexType: Empresa a la que tiene acceso la persona de un login dado
 *
 * @see telconet\schemaBundle\Repository\InfoPersonaEmpresaRolRepository -> getEmpresasByPersona($userPersona, $descRol)
 * @author ltama
 */
class EmpresaPersonaComplexType {
//     public $idPersonaEmpresaRol;
    public $codEmpresa;
    public $nombreEmpresa;
//     public $razonSocial;
    public $idOficina;
    public $nombreOficina;
    public $idDepartamento;
    public $nombreDepartamento;
    public $prefijoEmpresa;
    
    /**
     *
     * @param array $value            
     */
    public function __construct($value) {
//         $this->idPersonaEmpresaRol = $value ['IdPersonaEmpresaRol'];
        $this->codEmpresa = $value ['CodEmpresa'];
        $this->nombreEmpresa = $value ['nombreEmpresa'];
//         $this->razonSocial = $value ['razonSocial'];
        $this->idOficina = $value ['IdOficina'];
        $this->nombreOficina = $value ['nombreOficina'];
        $this->idDepartamento = $value ['IdDepartamento'];
        $this->nombreDepartamento = $value ['nombreDepartamento'];
        $this->prefijoEmpresa = $value['prefijo'];
    }
    
//     /**
//      *
//      * @return integer
//      */
//     public function getIdPersonaEmpresaRol() {
//         return $this->idPersonaEmpresaRol;
//     }
    
//     /**
//      *
//      * @param integer $idPersonaEmpresaRol            
//      */
//     public function setIdPersonaEmpresaRol($idPersonaEmpresaRol) {
//         $this->idPersonaEmpresaRol = $idPersonaEmpresaRol;
//         return $this;
//     }
    
    /**
     *
     * @return string
     */
    public function getCodEmpresa() {
        return $this->codEmpresa;
    }
    
    /**
     *
     * @param string $codEmpresa            
     */
    public function setCodEmpresa($codEmpresa) {
        $this->codEmpresa = $codEmpresa;
        return $this;
    }
    
    /**
     *
     * @return string
     */
    public function getNombreEmpresa() {
        return $this->nombreEmpresa;
    }
    
    /**
     *
     * @param string $nombreEmpresa            
     */
    public function setNombreEmpresa($nombreEmpresa) {
        $this->nombreEmpresa = $nombreEmpresa;
        return $this;
    }
    
//     /**
//      *
//      * @return string
//      */
//     public function getRazonSocial() {
//         return $this->razonSocial;
//     }
    
//     /**
//      *
//      * @param string $razonSocial            
//      */
//     public function setRazonSocial($razonSocial) {
//         $this->razonSocial = $razonSocial;
//         return $this;
//     }
    
    /**
     *
     * @return integer
     */
    public function getIdOficina() {
        return $this->idOficina;
    }
    
    /**
     *
     * @param integer $idOficina            
     */
    public function setIdOficina($idOficina) {
        $this->idOficina = $idOficina;
        return $this;
    }
    
    /**
     *
     * @return string
     */
    public function getNombreOficina() {
        return $this->nombreOficina;
    }
    
    /**
     *
     * @param string $nombreOficina            
     */
    public function setNombreOficina($nombreOficina) {
        $this->nombreOficina = $nombreOficina;
        return $this;
    }
    
    /**
     *
     * @return integer
     */
    public function getIdDepartamento() {
        return $this->idDepartamento;
    }
    
    /**
     *
     * @param integer $idDepartamento            
     */
    public function setIdDepartamento($idDepartamento) {
        $this->idDepartamento = $idDepartamento;
        return $this;
    }
    
    /**
     *
     * @return string
     */
    public function getNombreDepartamento() {
        return $this->nombreDepartamento;
    }
    
    /**
     *
     * @param string $nombreDepartamento            
     */
    public function setNombreDepartamento($nombreDepartamento) {
        $this->nombreDepartamento = $nombreDepartamento;
        return $this;
    }
    
    /**
     *
     * @return string
     */
    public function getPrefijoEmpresa() {
        return $this->prefijoEmpresa;
    }
    
    /**
     *
     * @param string $prefijoEmpresa            
     */
    public function setPrefijoEmpresa($prefijoEmpresa) {
        $this->prefijoEmpresa = $prefijoEmpresa;
        return $this;
    }
	
    
    
}
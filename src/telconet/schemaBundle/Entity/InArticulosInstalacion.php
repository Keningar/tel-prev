<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InArticulosInstalacion
 *
 * @ORM\Table(name="IN_ARTICULOS_INSTALACION")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InArticulosInstalacionRepository")
 */
class InArticulosInstalacion
{


/**
* @ORM\Column(name="ID_COMPANIA", type="integer", nullable=false)
* @ORM\Id
*/	
		
private $idCompania;	

/**
* @var integer $idCentro
*
* @ORM\Column(name="ID_CENTRO", type="integer", nullable=false)
*/	
		
private $idCentro;	

/**
* @var integer $secuencia
*
* @ORM\Column(name="SECUENCIA", type="integer", nullable=false)
* @ORM\Id
*/	
		
private $secuencia;

/**
* @var string $idArticulo
*
* @ORM\Column(name="ID_ARTICULO", type="string", nullable=false)
*/	
		
private $idArticulo;
	
/**
* @var string $descripcion
*
* @ORM\Column(name="DESCRIPCION", type="string", nullable=false)
*/		
     		
private $descripcion;

/**
* @var string $modelo
*
* @ORM\Column(name="MODELO", type="string", nullable=false)
*/		
     		
private $modelo;

/**
* @var string $numeroSerie
*
* @ORM\Column(name="NUMERO_SERIE", type="string", nullable=false)
*/		
     		
private $numeroSerie;

/**
* @var string $tipoArticulo
*
* @ORM\Column(name="TIPO_ARTICULO", type="string", nullable=false)
*/		
     		
private $tipoArticulo;

/**
* @var integer $cantidad
*
* @ORM\Column(name="CANTIDAD", type="integer", nullable=false)
*/	
		
private $cantidad;

/**
* @var integer $saldo
*
* @ORM\Column(name="SALDO", type="integer", nullable=false)
*/	
		
private $saldo;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=false)
*/		
     		
private $estado;

/**
* @var integer $costo
*
* @ORM\Column(name="COSTO", type="integer", nullable=false)
*/	
		
private $costo;

/**
* @var datetime $fecha
*
* @ORM\Column(name="FECHA", type="datetime", nullable=false)
*/		
     		
private $fecha;

/**
* @var integer $id_custodio
*
* @ORM\Column(name="ID_CUSTODIO", type="integer", nullable=false)
*/	
		
private $id_custodio;

/**
* @var string $cedula
*
* @ORM\Column(name="CEDULA", type="string", nullable=false)
*/	
		
private $cedula;

/**
* @var integer $id_documento_origen
*
* @ORM\Column(name="ID_DOCUMENTO_ORIGEN", type="integer", nullable=false)
*/	
		
private $id_documento_origen;

/**
* @var string $tipo_documento_origen
*
* @ORM\Column(name="TIPO_DOCUMENTO_ORIGEN", type="string", nullable=false)
*/	
		
private $tipo_documento_origen;

/**
* @var integer $linea_documento_origen
*
* @ORM\Column(name="LINEA_DOCUMENTO_ORIGEN", type="integer", nullable=false)
*/	
		
private $linea_documento_origen;

/**
* @var string $usr_creacion
*
* @ORM\Column(name="USR_CREACION", type="string", nullable=false)
*/	
		
private $usr_creacion;

/**
* @var datetime $fe_creacion
*
* @ORM\Column(name="FE_CREACION", type="datetime", nullable=false)
*/		
     		
private $fe_creacion;

/**
* @var integer $precio_venta
*
* @ORM\Column(name="PRECIO_VENTA", type="integer", nullable=false)
*/	
		
private $precio_venta;

/**
* @var integer $idInstalacion
*
* @ORM\Column(name="ID_INSTALACION", type="integer", nullable=false)
* @ORM\Id
*/	
		
private $idInstalacion;

/**
* @var integer $id_bodega
*
* @ORM\Column(name="ID_BODEGA", type="integer", nullable=false)
*/	
		
private $id_bodega;

/**
* @var string $nombre_bodega
*
* @ORM\Column(name="NOMBRE_BODEGA", type="string", nullable=false)
*/	
		
private $nombre_bodega;

/**
* @var integer $provincia
*
* @ORM\Column(name="PROVINCIA", type="integer", nullable=false)
*/	
		
private $provincia;

/**
* @var integer $canton
*
* @ORM\Column(name="CANTON", type="integer", nullable=false)
*/	
		
private $canton;

/**
* @var integer $parroquia
*
* @ORM\Column(name="PARROQUIA", type="integer", nullable=false)
*/	
		
private $parroquia;

/**
* @var integer $idGrupoCliente
*
* @ORM\Column(name="ID_GRUPO_CLIENTE", type="integer", nullable=false)
*/	
		
private $idGrupoCliente;

/**
* @var integer $idCliente
*
* @ORM\Column(name="ID_CLIENTE", type="integer", nullable=false)
*/	
		
private $idCliente;

/**
* @var string $usrUltMod
*
* @ORM\Column(name="USR_ULT_MOD", type="string", nullable=false)
*/		
     		
private $usrUltMod;

/**
* @var datetime $feUltMod
*
* @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=false)
*/		
     		
private $feUltMod;

/**
* @var string $mac
*
* @ORM\Column(name="MAC", type="string", nullable=false)
*/	
		
private $mac;



    /**
     * Set idCompania
     *
     * @param integer $idCompania
     * @return InArticulosInstalacion
     */
    public function setIdCompania($idCompania)
    {
        $this->idCompania = $idCompania;
    
    }

    /**
     * Get idCompania
     *
     * @return integer 
     */
    public function getIdCompania()
    {
        return $this->idCompania;
    }

    /**
     * Set idCentro
     *
     * @param integer $idCentro
     * @return InArticulosInstalacion
     */
    public function setAno($idCentro)
    {
        $this->idCentro = $idCentro;
    
    }

    /**
     * Get idCentro
     *
     * @return integer 
     */
    public function getIdCentro()
    {
        return $this->idCentro;
    }

    /**
     * Set secuencia
     *
     * @param integer $secuencia
     * @return InArticulosInstalacion
     */
    public function setMes($secuencia)
    {
        $this->secuencia = $secuencia;
    
    }

    /**
     * Get secuencia
     *
     * @return integer 
     */
    public function getSecuencia()
    {
        return $this->secuencia;
    }
    
    /**
     * Set idArticulo
     *
     * @param integer $idArticulo
     * @return InArticulosInstalacion
     */
    public function setIdArticulo($idArticulo)
    {
        $this->idArticulo = $idArticulo;
    
    }

    /**
     * Get idArticulo
     *
     * @return string 
     */
    public function getIdArticulo()
    {
        return $this->idArticulo;
    }
    /**
     * Set id_custodio
     *
     * @param integer $id_custodio
     * @return InArticulosInstalacion
     */
    public function setIdCustodio($id_custodio)
    {
        $this->id_custodio = $id_custodio;
    
    }

    /**
     * Get id_custodio
     *
     * @return integer 
     */
    public function getIdCustodio()
    {
        return $this->id_custodio;
    }    
    /**
     * Set cedula
     *
     * @param string $cedula
     * @return InArticulosInstalacion
     */
    public function setCedula($cedula)
    {
        $this->cedula = $cedula;
    
    }

    /**
     * Get cedula
     *
     * @return string 
     */
    public function getCedula()
    {
        return $this->cedula;
    }
    
    /**
     * Set mac
     *
     * @param string $mac
     * @return InArticulosInstalacion
     */
    public function setMac($mac)
    {
        $this->mac = $mac;
    
    }

    /**
     * Get mac
     *
     * @return string 
     */
    public function getMac()
    {
        return $this->mac;
    }
    
    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return InArticulosInstalacion
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    
    }

    /**
     * Get descripcion
     *
     * @return string 
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set modelo
     *
     * @param string $modelo
     * @return InArticulosInstalacion
     */
    public function setModelo($modelo)
    {
        $this->modelo = $modelo;
    
    }

    /**
     * Get modelo
     *
     * @return string 
     */
    public function getModelo()
    {
        return $this->modelo;
    }

    /**
     * Set numeroSerie
     *
     * @param string $numeroSerie
     * @return InArticulosInstalacion
     */
    public function setNumeroSerie($numeroSerie)
    {
        $this->numeroSerie = $numeroSerie;
    
    }

    /**
     * Get numeroSerie
     *
     * @return string 
     */
    public function getNumeroSerie()
    {
        return $this->numeroSerie;
    }

    /**
     * Set estado
     *
     * @param string $estado
     * @return InArticulosInstalacion
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
    
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
     * Set tipoArticulo
     *
     * @param string $tipoArticulo
     * @return InArticulosInstalacion
     */
    public function setTipoArticulo($tipoArticulo)
    {
        $this->tipoArticulo = $tipoArticulo;
    
    }

    /**
     * Get tipoArticulo
     *
     * @return string 
     */
    public function getTipoArticulo()
    {
        return $this->tipoArticulo;
    }
    
    /**
     * Set cantidad
     *
     * @param integer $cantidad
     * @return InArticulosInstalacion
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;
    
    }

    /**
     * Get cantidad
     *
     * @return integer 
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }
    
    /**
     * Set saldo
     *
     * @param integer $saldo
     * @return InArticulosInstalacion
     */
    public function setSaldo($saldo)
    {
        $this->saldo = $saldo;
    
    }

    /**
     * Get saldo
     *
     * @return integer 
     */
    public function getSaldo()
    {
        return $this->saldo;
    }
    
    /**
     * Set provincia
     *
     * @param integer $provincia
     * @return InArticulosInstalacion
     */
    public function setProvincia($provincia)
    {
        $this->provincia = $provincia;
    
    }

    /**
     * Get provincia
     *
     * @return integer 
     */
    public function getProvincia()
    {
        return $this->provincia;
    }
    
    /**
     * Set canton
     *
     * @param integer $canton
     * @return InArticulosInstalacion
     */
    public function setCanton($canton)
    {
        $this->canton = $canton;
    
    }

    /**
     * Get canton
     *
     * @return integer 
     */
    public function getCanton()
    {
        return $this->canton;
    }
    
    /**
     * Set parroquia
     *
     * @param integer $parroquia
     * @return InArticulosInstalacion
     */
    public function setParroquia($parroquia)
    {
        $this->parroquia = $parroquia;
    
    }

    /**
     * Get parroquia
     *
     * @return integer 
     */
    public function getParroquia()
    {
        return $this->parroquia;
    }
    
    /**
     * Set idGrupoCliente
     *
     * @param integer $idGrupoCliente
     * @return InArticulosInstalacion
     */
    public function setIdGrupoCliente($idGrupoCliente)
    {
        $this->idGrupoCliente = $idGrupoCliente;
    
    }

    /**
     * Get idGrupoCliente
     *
     * @return integer 
     */
    public function getIdGrupoCliente()
    {
        return $this->idGrupoCliente;
    }

    
    /**
     * Set idCliente
     *
     * @param integer $idCliente
     * @return InArticulosInstalacion
     */
    public function setIdCliente($idCliente)
    {
        $this->idCliente = $idCliente;
    
    }

    /**
     * Get idCliente
     *
     * @return integer 
     */
    public function getIdCliente()
    {
        return $this->idCliente;
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
     * @param string $usrUltMod
     */
    public function setUsrUltMod($usrUltMod)
    {
        $this->usrUltMod = $usrUltMod;
    }

    /**
     * Get feUltMod
     *
     * @return datetime
     */
    public function getFeUltMod()
    {
        return $this->feUltMod;
    }

    /**
     * Set feUltMod
     *
     * @param datetime $feUltMod
     */
    public function setFeUltMod($feUltMod)
    {
        $this->feUltMod = $feUltMod;
    }

    /**
     * Get fecha
     *
     * @return datetime
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set feUltMod
     *
     * @param datetime $fecha
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    }

    /**
     * Set idInstalacion
     *
     * @param integer $idInstalacion
     */
    public function setIdInstalacion($idInstalacion)
    {
        $this->idInstalacion = $idInstalacion;
    
    }

    /**
     * Get idInstalacion
     *
     * @return integer 
     */
    public function getIdInstalacion()
    {
        return $this->idInstalacion;
    }
}

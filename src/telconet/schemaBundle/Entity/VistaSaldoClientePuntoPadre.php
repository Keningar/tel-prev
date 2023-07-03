<?php



namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\VistaSaldoClientePuntoPadre
 *
 * @ORM\Table(name="VISTA_SALDO_CLIENTE_PUNTOPADRE")
 * @ORM\Entity
 */ 



class VistaSaldoClientePuntoPadre {
    //put your code here
    
    
    
    /**
    * @var integer $id
    *
    * @ORM\Column(name="ID_VISTA", type="integer", nullable=false)
    * @ORM\Id
    */		

    private $id;
    
    
    
    /**
    * @var integer $id
    *
    * @ORM\Column(name="ID_PERSONA", type="integer", nullable=false)
    * @ORM\Id
    */		

    private $idPersona;


    /**
    * @var integer $puntoFacturacionId
    *
    * @ORM\Column(name="PUNTO_FACTURACION_ID", type="integer", nullable=false)
    */		

    private $puntoFacturacionId;

   

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
    * @var string $login
    *
    * @ORM\Column(name="LOGIN", type="string", nullable=false)
    */		

    private $login;
    
    /**
    * @var string $nombreOficina
    *
    * @ORM\Column(name="NOMBRE_OFICINA", type="string", nullable=false)
    */		

    private $nombreOficina;
    
    /**
    * @var string $descripcionFormaPago
    *
    * @ORM\Column(name="DESCRIPCION_FORMA_PAGO", type="string", nullable=false)
    */		

    private $descripcionFormaPago;
    
    /**
    * @var string $saldo
    *
    * @ORM\Column(name="SALDO", type="float", nullable=false)
    */		

    private $saldo;
    
    /**
    * @var string $oficinaId
    *
    * @ORM\Column(name="OFICINA_ID", type="integer", nullable=false)
    */		

    private $oficinaId;
    
    /**
    * @var string $formaPagoId
    *
    * @ORM\Column(name="FORMA_PAGO_ID", type="integer", nullable=false)
    */		

    private $formaPagoId;
    
    /**
    * @var string $empresaCod
    *
    * @ORM\Column(name="EMPRESA_COD", type="integer", nullable=false)
    */		

    private $empresaCod;
    
    /**
    * @var interger $idTipoNegocio
    *
    * @ORM\Column(name="TIPO_NEGOCIO_ID", type="integer", nullable=false)
    */
    private $tipoNegocioId;
    
    /**
    * @var String $nombretipoNegocio
    *
    * @ORM\Column(name="NOMBRE_TIPO_NEGOCIO", type="string", nullable=false)
    */

    private $nombreTipoNegocio;
    
    
    
    /**
    * Get id
    *
    * @return integer
    */	

    public function getId(){
            return $this->id; 
    }
    
    /**
    * Get puntoId
    *
    * @return integer
    */		

    public function getPuntoFacturacionId(){
            return $this->puntoFacturacionId; 
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
    * Get estado
    *
    * @return string
    */		

    public function getEstado(){
            return $this->estado; 
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
    * Get nombrePlan
    *
    * @return string
    */		

    public function getNombrePlan(){
            return $this->nombrePlan; 
    }
    
    /**
    * Get nombreOficina
    *
    * @return string
    */		

    public function getNombreOficina(){
            return $this->nombreOficina; 
    }
    
    /**
    * Get descripcionFormaPago
    *
    * @return string
    */		

    public function getDescripcionFormaPago(){
            return $this->descripcionFormaPago; 
    }
    
    /**
    * Get saldo
    *
    * @return float
    */		

    public function getSaldo(){
            return $this->saldo; 
    }
    
    /**
    * Get oficinaId
    *
    * @return integer
    */		

    public function getOficinaId(){
            return $this->oficinaId; 
    }
    
    /**
    * Get formaPagoId
    *
    * @return integer
    */		

    public function getFormaPagoId(){
            return $this->formaPagoId; 
    }
    
    /**
    * Get empresaCod
    *
    * @return string
    */		

    public function getEmpresaCod(){
            return $this->empresaCod; 
    }
    
    
    public function getTipoNegocioId() {
        return $this->tipoNegocioId;
    }
    
    public function getNombreTipoNegocio() {
        return $this->nombreTipoNegocio;
    }
    
    
    
}

?>

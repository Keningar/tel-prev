<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoReporteHistorial
 *
 * @ORM\Table(name="INFO_REPORTE_HISTORIAL")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoReporteHistorialRepository")
 */
class InfoReporteHistorial
{


    /**
    * @var integer $id
    *
    * @ORM\Column(name="ID_REPORTE_HISTORIAL", type="integer", nullable=false)
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="SEQUENCE")
    * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_REPORTE_HISTORIAL", allocationSize=1, initialValue=1)
    */		

    private $id;

    /**
    * @var string $empresaCod
    *
    * @ORM\Column(name="EMPRESA_COD", type="string", nullable=false)
    */
    private $empresaCod;

    /**
    * @var string $codigoTipoReporte
    *
    * @ORM\Column(name="CODIGO_TIPO_REPORTE", type="string", nullable=false)
    */    

    private $codigoTipoReporte;

    /**
    * @var string $usrCreacion
    *
    * @ORM\Column(name="USR_CREACION", type="string", nullable=false)
    */    

    private $usrCreacion;

    /**
    * @var datetime $feCreacion
    *
    * @ORM\Column(name="FE_CREACION", type="datetime", nullable=false)
    */		

    private $feCreacion;

    /**
    * @var string $emailUsrCreacion
    *
    * @ORM\Column(name="EMAIL_USR_CREACION", type="string", nullable=false)
    */    

    private $emailUsrCreacion;

    /**
    * @var string $aplicacion
    *
    * @ORM\Column(name="APLICACION", type="string", nullable=false)
    */    

    private $aplicacion;

    /**
    * @var string $observacion
    *
    * @ORM\Column(name="OBSERVACION", type="string", nullable=true)
    */		

    private $observacion;

    /**
    * @var string $estado
    *
    * @ORM\Column(name="ESTADO", type="string", nullable=false)
    */		

    private $estado;
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
    * Get empresaCod
    *
    * @return string 
    */    

    public function getEmpresaCod(){
      return $this->empresaCod; 
    }

    /**
    * Set $empresaCod
    *
    * @param $empresaCod
    */
    public function setEmpresaCod($empresaCod)
    {
        $this->empresaCod = $empresaCod;
    }

    /**
    * Get codigoTipoReporte
    *
    * @return string
    */    

    public function getCodigoTipoReporte(){
      return $this->codigoTipoReporte; 
    }

    /**
    * Set codigoTipoReporte
    *
    * @param string $codigoTipoReporte
    */
    public function setCodigoTipoReporte($codigoTipoReporte)
    {
        $this->codigoTipoReporte = $codigoTipoReporte;
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
    * Get emailUsrCreacion
    *
    * @return string
    */    

    public function getEmailUsrCreacion()
    {
      return $this->emailUsrCreacion; 
    }

    /**
    * Set emailUsrCreacion
    *
    * @param string $emailUsrCreacion
    */
    public function setEmailUsrCreacion($emailUsrCreacion)
    {
        $this->emailUsrCreacion = $emailUsrCreacion;
    }

    /**
    * Get aplicacion
    *
    * @return string
    */    

    public function getAplicacion()
    {
        return $this->aplicacion; 
    }

    /**
    * Set aplicacion
    *
    * @param string $aplicacion
    */
    public function setAplicacion($aplicacion)
    {
        $this->aplicacion = $aplicacion;
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
    * Get observacion
    *
    * @return string
    */
    public function getObservacion()
    {
        return $this->observacion;
    }

    /**
    * Set observacion
    *
    * @param string $observacion
    */
    public function setObservacion($observacion)
    {
        $this->observacion = $observacion;
    }

}

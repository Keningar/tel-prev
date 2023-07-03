<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiFormaContacto
 *
 * @ORM\Table(name="ADMI_FORMA_CONTACTO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiFormaContactoRepository")
 */
class AdmiFormaContacto
{


  /**
   * @var integer $id
   *
   * @ORM\Column(name="ID_FORMA_CONTACTO", type="integer", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="SEQUENCE")
   * @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_FORMA_CONTACTO", allocationSize=1, initialValue=1)
   */

  private $id;

  /**
   * @var string $descripcionFormaContacto
   *
   * @ORM\Column(name="DESCRIPCION_FORMA_CONTACTO", type="string", nullable=false)
   */

  private $descripcionFormaContacto;

  /**
   * @var datetime $feCreacion
   *
   * @ORM\Column(name="FE_CREACION", type="datetime", nullable=false)
   */

  private $feCreacion;

  /**
   * @var string $usrCreacion
   *
   * @ORM\Column(name="USR_CREACION", type="string", nullable=false)
   */

  private $usrCreacion;

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
   * @var string $estado
   *
   * @ORM\Column(name="ESTADO", type="string", nullable=false)
   */

  private $estado;

  /**
   * @var string $codigo
   *
   * @ORM\Column(name="CODIGO", type="string", nullable=true)
   */
     
  private $codigo;
  
    /**
   * @var string $mostrarApp
   *
   * @ORM\Column(name="MOSTRAR_APP", type="string", nullable=false)
   */

  private $mostrarApp;

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
   * Get descripcionFormaContacto
   *
   * @return string
   */
  public function getDescripcionFormaContacto()
  {
    return $this->descripcionFormaContacto;
  }

  /**
   * Set descripcionFormaContacto
   *
   * @param string $descripcionFormaContacto
   */
  public function setDescripcionFormaContacto($descripcionFormaContacto)
  {
    $this->descripcionFormaContacto = $descripcionFormaContacto;
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
   * Get codigo
   *
   * @return string
   */
  public function getCodigo()
  {
    return $this->codigo;
  }

  /**
   * Set codigo
   *
   * @param string $codigo
   */
  public function setCodigo($codigo)
  {
    $this->codigo = $codigo;
  }

  /**
   * Get strMostrarApp
   *
   * @return string
   */
  public function getMostrarApp()
  {
    return $this->mostrarApp;
  }

  /**
   * Set strMostrarApp
   *
   * @param string $mostrarApp
   */
  public function setMostrarApp($strMostrarApp)
  {
    $this->mostrarApp = $strMostrarApp;
  }


  public function __toString()
  {
    return $this->descripcionFormaContacto;
  }

}

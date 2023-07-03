<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * telconet\schemaBundle\Entity\InfoCertificado
 *
 * @ORM\Table(name="INFO_CERTIFICADO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoCertificadoRepository")
 */
class InfoCertificado
{
/**
* @var integer $id
*
* @ORM\Column(name="ID_CERTIFICADO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_CERTIFICADO", allocationSize=1, initialValue=1)
*/		
		
private $id;	    

/**
* @var integer $empresaId
*
* @ORM\Column(name="EMPRESA_ID", type="integer", nullable=true)
* })
*/
		
private $empresaId;

/**
* @var string $serialNumber
*
* @ORM\Column(name="SERIAL_NUMBER", type="string", nullable=false)
*/		
     		
private $serialNumber;

/**
* @var string $email
*
* @ORM\Column(name="EMAIL", type="string", nullable=false)
*/		
     		
private $email;

/**
* @var string $numCedula
*
* @ORM\Column(name="NUM_CEDULA", type="string", nullable=false)
*/		
     		
private $numCedula;

/**
* @var string $nombres
*
* @ORM\Column(name="NOMBRES", type="string", nullable=false)
*/		
     		
private $nombres;

/**
* @var string $primerApellido
*
* @ORM\Column(name="PRIMER_APELLIDO", type="string", nullable=false)
*/		
     		
private $primerApellido;

/**
* @var string $segundoApellido
*
* @ORM\Column(name="SEGUNDO_APELLIDO", type="string", nullable=false)
*/		
     		
private $segundoApellido;

/**
* @var string $direccion
*
* @ORM\Column(name="DIRECCION", type="string", nullable=false)
*/		
     		
private $direccion;

/**
* @var string $telefono
*
* @ORM\Column(name="TELEFONO", type="string", nullable=false)
*/		
     		
private $telefono;

/**
* @var string $ciudad
*
* @ORM\Column(name="CIUDAD", type="string", nullable=false)
*/		
     		
private $ciudad;

/**
* @var string $pais
*
* @ORM\Column(name="PAIS", type="string", nullable=false)
*/		
     		
private $pais;

/**
* @var string $provincia
*
* @ORM\Column(name="PROVINCIA", type="string", nullable=false)
*/		
     		
private $provincia;

/**
* @var string $numFactura
*
* @ORM\Column(name="NUM_FACTURA", type="string", nullable=false)
*/		
     		
private $numFactura;

/**
* @var string $numSerieToken
*
* @ORM\Column(name="NUM_SERIE_TOKEN", type="string", nullable=false)
*/		
     		
private $numSerieToken;

/**
* @var string $password
*
* @ORM\Column(name="PASSWORD", type="string", nullable=false)
*/		
     		
private $password;

/**
* @var string $enterprise
*
* @ORM\Column(name="ENTERPRISE", type="string", nullable=false)
*/		
     		
private $enterprise;

/**
* @var string $personaNatural
*
* @ORM\Column(name="PERSONA_NATURAL", type="string", nullable=false)
*/		
     		
private $personaNatural;

/**
* @var integer $numDiasVigencia
*
* @ORM\Column(name="NUM_DIAS_VIGENCIA", type="integer", nullable=false)
*/		
     		
private $numDiasVigencia;

/**
* @var string $gruposPertenencia
*
* @ORM\Column(name="GRUPOS_PERTENENCIA", type="string", nullable=false)
*/		
     		
private $gruposPertenencia;

/**
* @var string $respuesta
*
* @ORM\Column(name="RESPUESTA", type="string", nullable=false)
*/		
     		
private $respuesta;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=false)
*/		
     		
private $estado;

/**
* @var string $usrCreacion
*
* @ORM\Column(name="USR_CREACION", type="string", nullable=false)
*/		
     		
private $usrCreacion;

/**
* @var datetime $feCreacion
*
* @ORM\Column(name="FE_CREACION", type="datetime", nullable=true)
*/		
     		
private $feCreacion;

/**
* @var string $usrUltMod
*
* @ORM\Column(name="USR_ULT_MOD", type="string", nullable=true)
*/		
     		
private $usrUltMod;

/**
* @var datetime $feUltMod
*
* @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=true)
*/		
     		
private $feUltMod;

/**
* @var string $ipCreacion
*
* @ORM\Column(name="IP_CREACION", type="string", nullable=false)
*/		
     		
private $ipCreacion;

/**
* @var string $recuperado
*
* @ORM\Column(name="RECUPERADO", type="string", nullable=false)
*/		
     		
private $recuperado;

/**
* @var string $documentado
*
* @ORM\Column(name="DOCUMENTADO", type="string", nullable=false)
*/		
     		
private $documentado;

/**
* @var string $rubrica
*
* @ORM\Column(name="RUBRICA", type="string", nullable=false)
*/		
     		
private $rubrica;

/**
* @var datetime $fechaCreacion
*
* @ORM\Column(name="FECHA_CREACION", type="datetime", nullable=true)
*/		
     		
private $fechaCreacion;

/**
* @var string $usuarioCreacion
*
* @ORM\Column(name="USUARIO_CREACION", type="string", nullable=false)
*/		
     		
private $usuarioCreacion;

/**
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
 * Set empresaId
 *
 * @param integer $empresaId
 * @return infoCertificado
 */
public function setEmpresaId($empresaId)
{
    $this->empresaId = $empresaId;

    //return $this;
}

/**
 * Get casoId
 *
 * @return integer 
 */
public function getEmpresaId()
{
    return $this->empresaId;

}

/**
 * Set serialNumber
 *
 * @param string $serialNumber
 * @return infoCertificado
 */
public function setSerialNumber($serialNumber)
{
    $this->serialNumber = $serialNumber;
    //return $this;
}

/**
 * Get serialNumber
 *
 * @return string 
 */
public function getSerialNumber()
{
    return $this->serialNumber;
}

/**
 * Set email
 *
 * @param string $email
 * @return infoCertificado
 */
public function setEmail($email)
{
    $this->email = $email;
    //return $this;
}

/**
 * Get email
 *
 * @return string 
 */
public function getEmail()
{
    return $this->email;
}

/**
 * Set numCedula
 *
 * @param string $numCedula
 * @return infoCertificado
 */
public function setNumCedula($numCedula)
{
    $this->numCedula = $numCedula;
    //return $this;
}

/**
 * Get numCedula
 *
 * @return string 
 */
public function getNumCedula()
{
    return $this->numCedula;
}

/**
 * Set nombres
 *
 * @param string $nombres
 * @return infoCertificado
 */
public function setNombres($nombres)
{
    $this->nombres = $nombres;
    //return $this;
}

/**
 * Get nombres
 *
 * @return string 
 */
public function getNombres()
{
    return $this->nombres;
}

/**
 * Set primerApellido
 *
 * @param string $primerApellido
 * @return infoCertificado
 */
public function setPrimerApellido($primerApellido)
{
    $this->primerApellido = $primerApellido;
    //return $this;
}

/**
 * Get primerApellido
 *
 * @return string 
 */
public function getPrimerApellido()
{
    return $this->primerApellido;
}

/**
 * Set segundoApellido
 *
 * @param string $segundoApellido
 * @return infoCertificado
 */
public function setSegundoApellido($segundoApellido)
{
    $this->segundoApellido = $segundoApellido;
    //return $this;
}

/**
 * Get segundoApellido
 *
 * @return string 
 */
public function getSegundoApellido()
{
    return $this->segundoApellido;
}

/**
 * Set direccion
 *
 * @param string $direccion
 * @return infoCertificado
 */
public function setDireccion($direccion)
{
    $this->direccion = $direccion;
    //return $this;
}

/**
 * Get direccion
 *
 * @return string 
 */
public function getDireccion()
{
    return $this->direccion;
}

/**
 * Set telefono
 *
 * @param string $telefono
 * @return infoCertificado
 */
public function setTelefono($telefono)
{
    $this->telefono = $telefono;
    //return $this;
}

/**
 * Get telefono
 *
 * @return string 
 */
public function getTelefono()
{
    return $this->telefono;
}

/**
 * Set ciudad
 *
 * @param string $ciudad
 * @return infoCertificado
 */
public function setCiudad($ciudad)
{
    $this->ciudad = $ciudad;
    //return $this;
}

/**
 * Get ciudad
 *
 * @return string 
 */
public function getCiudad()
{
    return $this->ciudad;
}

/**
 * Set pais
 *
 * @param string $pais
 * @return infoCertificado
 */
public function setPais($pais)
{
    $this->pais = $pais;
    //return $this;
}

/**
 * Get pais
 *
 * @return string 
 */
public function getPais()
{
    return $this->pais;
}

/**
 * Set provincia
 *
 * @param string $provincia
 * @return infoCertificado
 */
public function setProvincia($provincia)
{
    $this->provincia = $provincia;
    //return $this;
}

/**
 * Get provincia
 *
 * @return string 
 */
public function getProvincia()
{
    return $this->provincia;
}

/**
 * Set numFactura
 *
 * @param string $numFactura
 * @return infoCertificado
 */
public function setNumFactura($numFactura)
{
    $this->numFactura = $numFactura;
    //return $this;
}

/**
 * Get numFactura
 *
 * @return string 
 */
public function getNumFactura()
{
    return $this->numFactura;
}

/**
 * Set numSerieToken
 *
 * @param string $numSerieToken
 * @return infoCertificado
 */
public function setNumSerieToken($numSerieToken)
{
    $this->numSerieToken = $numSerieToken;
    //return $this;
}

/**
 * Get numSerieToken
 *
 * @return string 
 */
public function getNumSerieToken()
{
    return $this->numSerieToken;
}

/**
 * Set password
 *
 * @param string $password
 * @return infoCertificado
 */
public function setPassword($password)
{
    $this->password = $password;
    //return $this;
}

/**
 * Get password
 *
 * @return string 
 */
public function getPassword()
{
    return $this->password;
}

/**
 * Set enterprise
 *
 * @param string $enterprise
 * @return infoCertificado
 */
public function setEnterprise($enterprise)
{
    $this->enterprise = $enterprise;
    //return $this;
}

/**
 * Get enterprise
 *
 * @return string 
 */
public function getEnterprise()
{
    return $this->enterprise;
}

/**
 * Set personaNatural
 *
 * @param string $personaNatural
 * @return infoCertificado
 */
public function setPersonaNatural($personaNatural)
{
    $this->personaNatural = $personaNatural;
    //return $this;
}

/**
 * Get personaNatural
 *
 * @return string 
 */
public function getPersonaNatural()
{
    return $this->personaNatural;
}

/**
 * Set numDiasVigencia
 *
 * @param integer $numDiasVigencia
 * @return infoCertificado
 */
public function setNumDiasVigencia($numDiasVigencia)
{
    $this->numDiasVigencia = $numDiasVigencia;
    //return $this;
}

/**
 * Get numDiasVigencia
 *
 * @return integer 
 */
public function getNumDiasVigencia()
{
    return $this->numDiasVigencia;
}

/**
 * Set gruposPertenencia
 *
 * @param string $gruposPertenencia
 * @return infoCertificado
 */
public function setGruposPertenencia($gruposPertenencia)
{
    $this->gruposPertenencia = $gruposPertenencia;
    //return $this;
}

/**
 * Get gruposPertenencia
 *
 * @return string 
 */
public function getGruposPertenencia()
{
    return $this->gruposPertenencia;
}

/**
 * Set respuesta
 *
 * @param string $respuesta
 * @return infoCertificado
 */
public function setRespuesta($respuesta)
{
    $this->respuesta = $respuesta;
    //return $this;
}

/**
 * Get respuesta
 *
 * @return string 
 */
public function getRespuesta()
{
    return $this->respuesta;
}

/**
 * Set estado
 *
 * @param string $estado
 * @return infoCertificado
 */
public function setEstado($estado)
{
    $this->estado = $estado;
    //return $this;
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
 * Set usrCreacion
 *
 * @param string $usrCreacion
 * @return infoCertificado
 */
public function setUsrCreacion($usrCreacion)
{
    $this->usrCreacion = $usrCreacion;
    //return $this;
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
 * Set feCreacion
 *
 * @param \DateTime $feCreacion
 * @return infoCertificado
 */
public function setFeCreacion($feCreacion)
{
    $this->feCreacion = $feCreacion;
    //return $this;
}

/**
 * Get feCreacion
 *
 * @return \DateTime 
 */
public function getFeCreacion()
{
    return $this->feCreacion;
}

/**
 * Set usrUltMod
 *
 * @param string $usrUltMod
 * @return infoCertificado
 */
public function setUsrUltMod($usrUltMod)
{
    $this->usrUltMod = $usrUltMod;
    //return $this;
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
 * Set feUltMod
 *
 * @param \DateTime $feUltMod
 * @return infoCertificado
 */
public function setFeUltMod($feUltMod)
{
    $this->feUltMod = $feUltMod;
    //return $this;
}

/**
 * Get feUltMod
 *
 * @return \DateTime 
 */
public function getFeUltMod()
{
    return $this->feUltMod;
}

/**
 * Set ipCreacion
 *
 * @param string $ipCreacion
 * @return infoCertificado
 */
public function setIpCreacion($ipCreacion)
{
    $this->ipCreacion = $ipCreacion;
    //return $this;
}

/**
 * Get ipCreacion
 *
 * @return string 
 */
public function getIpCreacion()
{
    return $this->ipCreacion;
}

/**
 * Set recuperado
 *
 * @param string $recuperado
 * @return infoCertificado
 */
public function setRecuperado($recuperado)
{
    $this->recuperado = $recuperado;
    //return $this;
}

/**
 * Get recuperado
 *
 * @return string 
 */
public function getRecuperado()
{
    return $this->recuperado;
}

/**
 * Set documentado
 *
 * @param string $documentado
 * @return infoCertificado
 */
public function setDocumentado($documentado)
{
    $this->documentado = $documentado;
    //return $this;
}

/**
 * Get documentado
 *
 * @return string 
 */
public function getDocumentado()
{
    return $this->documentado;
}

/**
 * Set rubrica
 *
 * @param string $rubrica
 * @return infoCertificado
 */
public function setRubrica($rubrica)
{
    $this->rubrica = $rubrica;
    //return $this;
}

/**
 * Get rubrica
 *
 * @return string 
 */
public function getRubrica()
{
    return $this->rubrica;
}

/**
 * Set fechaCreacion
 *
 * @param \DateTime $fechaCreacion
 * @return infoCertificado
 */
public function setFechaCreacion($fechaCreacion)
{
    $this->fechaCreacion = $fechaCreacion;
    //return $this;
}

/**
 * Get fechaCreacion
 *
 * @return \DateTime 
 */
public function getFechaCreacion()
{
    return $this->fechaCreacion;
}

/**
 * Set usuarioCreacion
 *
 * @param string $usuarioCreacion
 * @return infoCertificado
 */
public function setUsuarioCreacion($usuarioCreacion)
{
    $this->usuarioCreacion = $usuarioCreacion;
    //return $this;
}

/**
 * Get usuarioCreacion
 *
 * @return string 
 */
public function getUsuarioCreacion()
{
    return $this->usuarioCreacion;
}

}


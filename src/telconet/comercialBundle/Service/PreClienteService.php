<?php

namespace telconet\comercialBundle\Service;

use Doctrine\ORM\EntityManager;

use telconet\schemaBundle\Entity\InfoPersona;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRol;
use telconet\schemaBundle\Entity\InfoPersonaReferido;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolHisto;
use telconet\schemaBundle\Entity\InfoPersonaFormaContacto;
use telconet\schemaBundle\Entity\InfoPersonaEmpFormaPago;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolCarac;
use telconet\schemaBundle\Entity\AdmiTipoCuenta;
use telconet\schemaBundle\Entity\AdmiBancoTipoCuenta;
use telconet\schemaBundle\Entity\InfoServicio;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\tecnicoBundle\Service\InfoInterfaceElementoService;

class PreClienteService {
    private $serviceInfoPersonaFormaContacto;
    private $serviceInfoInterfaceElemento;
    private $serviceUtil;
    private $emcom;
    private $strUrlMsGestionaPrefactibilidad;
    private $restClient;
    private $strUrlFormasContactoProspectoGuardarMs;
     
    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $objContainer) 
    {
        $this->serviceInfoPersonaFormaContacto = $objContainer->get('comercial.InfoPersonaFormaContacto'); 
        $this->serviceInfoInterfaceElemento    = $objContainer->get('tecnico.InfoInterfaceElemento');
        $this->serviceUtil                     = $objContainer->get('schema.Util');
        $this->strUrlMsGestionaPrefactibilidad = $objContainer->getParameter('ws_ms_gestionaprefactibilidad_url');
        $this->restClient                      = $objContainer->get('schema.RestClient');
        $this->emcom                           = $objContainer->get('doctrine.orm.telconet_entity_manager');
        $this->strUrlFormasContactoProspectoGuardarMs = $objContainer->getParameter('ws_ms_forma_contacto_prospecto_guardar');
    

    }
    /**
     * Documentación para la función crearPreClienteOrigenCRM.
     *
     * Función que realiza la creación de un Pre-cliente con origen de TelcoCRM.
     *
     * @param array $arrayParametros [
     *                                  "strPrefijoEmpresa"     => Prefijo de la empresa, ingresada en la aplicación TelcoCRM.
     *                                  "strCodEmpresa"         => Código de la empresa, ingresada en la aplicación TelcoCRM.
     *                                  "strTipoIdentificacion" => Tipo de identificación del cliente, ingresada en la aplicación TelcoCRM.
     *                                  "strRuc"                => Ruc de la cuenta, ingresada en la aplicación TelcoCRM.
     *                                  "strTipoEmpresa"        => Tipo de empresa del cliente, ingresada en la aplicación TelcoCRM.
     *                                  "strDireccionCuenta"    => Dirección de la cuenta, ingresada en la aplicación TelcoCRM.
     *                                  "strTipoCliente"        => Tipo tributario(Natural/Juridico), ingresada en la aplicación TelcoCRM.
     *                                  "strPagaIva"            => 'S' si paga Iva el cliente, ingresada en la aplicación TelcoCRM.
     *                                  "strOficinaFacturacion" => Oficina de facturación, ingresada en la aplicación TelcoCRM.
     *                                  "strNacionalidad"       => Nacionalidad del cliente, ingresada en la aplicación TelcoCRM.
     *                                  "strNombreCuenta"       => Nombre de la cuenta, ingresada en la aplicación TelcoCRM.
     *                                  "strRepresentanteLegal" => Representante legal del cliente, ingresada en la aplicación TelcoCRM.
     *                                  "strGenero"             => Género del cliente, ingresada en la aplicación TelcoCRM.
     *                                  "strNombreCliente"      => Nombre del cliente, ingresada en la aplicación TelcoCRM.
     *                                  "strApellidoCliente"    => Apellido del cliente, ingresada en la aplicación TelcoCRM.
     *                                  "arrayFormaContactos"   => Formas de contactos del cliente, ingresada en la aplicación TelcoCRM.
     *                                  "strNombrePais"         => Nombre del pais.
     *                                  "intIdPais"             => Id del pais.
     *                                  "strContribuyente"      => Indica si es contribuyente especial.
     *                                  "strConadis"            => Número conadis del cliente.
     *                                  "strEstadoCivil"        => Estado civil del cliente.
     *                                  "strPrepago"            => Valida si es prepago.
     *                                  "intTitulo"             => Título del cliente, ingresada en la aplicación TelcoCRM.
     *                                  "strFechaNacimiento"    => Fecha de nacimiento del cliente, ingresada en la aplicación TelcoCRM.
     *                                  "strUsuarioCreacion"    => Usuario en sessión en la aplicación TelcoCRM.
     *                                  "aplication"            => Nombre de la aplicación.
     *                               ]
     *
     * @return string $strMensaje Mensaje que índica si se creó el Pre-Cliente o hubo un error en el proceso.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 27-05-2019
     *
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.1 29-10-2020 - Se agrega validación para el campo Holding.
     * 
     */
    public function crearPreClienteOrigenCRM($arrayParametros)
    {
        $strPrefijoEmpresa     = $arrayParametros['strPrefijoEmpresa'] ? $arrayParametros['strPrefijoEmpresa']:"";
        $strCodEmpresa         = $arrayParametros['strCodEmpresa'] ? $arrayParametros['strCodEmpresa']:"";
        $strTipoIdentificacion = $arrayParametros['strTipoIdentificacion'] ? $arrayParametros['strTipoIdentificacion']:"";
        $strRuc                = $arrayParametros['strRuc'] ? $arrayParametros['strRuc']:"";
        $strTipoEmpresa        = $arrayParametros['strTipoEmpresa'] ? $arrayParametros['strTipoEmpresa']:"";
        $strDireccionCuenta    = $arrayParametros['strDireccionCuenta'] ? $arrayParametros['strDireccionCuenta']:"";
        $strTipoCliente        = $arrayParametros['strTipoCliente'] ? $arrayParametros['strTipoCliente']:"";
        $strPagaIva            = $arrayParametros['strPagaIva'] ? $arrayParametros['strPagaIva']:"S";
        $strOficinaFacturacion = $arrayParametros['strOficinaFacturacion'] ? $arrayParametros['strOficinaFacturacion']:"";
        $strNacionalidad       = $arrayParametros['strNacionalidad'] ? $arrayParametros['strNacionalidad']:"";
        $strNombreCuenta       = $arrayParametros['strNombreCuenta'] ? $arrayParametros['strNombreCuenta']:"";
        $strRepresentanteLegal = $arrayParametros['strRepresentanteLegal'] ? $arrayParametros['strRepresentanteLegal'] :"";
        $strGenero             = $arrayParametros['strGenero'] ? $arrayParametros['strGenero'] : "";
        $strNombreCliente      = $arrayParametros['strNombreCliente'] ? $arrayParametros['strNombreCliente'] : "";
        $strApellidoCliente    = $arrayParametros['strApellidoCliente'] ? $arrayParametros['strApellidoCliente'] : "";
        $arrayFormaContactos   = $arrayParametros['arrayFormaContactos'] ? $arrayParametros['arrayFormaContactos'] : "";
        $strNombrePais         = $arrayParametros['strNombrePais'] ? $arrayParametros['strNombrePais'] : "";
        $intIdPais             = $arrayParametros['intIdPais'] ? $arrayParametros['intIdPais'] : "";
        $strOrigenIngreso      = $arrayParametros['strOrigenIngreso'] ? $arrayParametros['strOrigenIngreso'] : "";
        $strContribuyente      = $arrayParametros['strContribuyente'] ? $arrayParametros['strContribuyente'] : "";
        $strConadis            = $arrayParametros['strConadis'] ? $arrayParametros['strConadis'] : "";
        $strEstadoCivil        = $arrayParametros['strEstadoCivil'] ? $arrayParametros['strEstadoCivil'] : "";
        $strPrepago            = $arrayParametros['strPrepago'] ? $arrayParametros['strPrepago'] : "";
        $intTitulo             = $arrayParametros['intTitulo'] ? $arrayParametros['intTitulo'] : "";
        $strFechaNacimiento    = $arrayParametros['strFechaNacimiento'] ? $arrayParametros['strFechaNacimiento'] : "";
        $strUsuarioCreacion    = $arrayParametros['strUsuarioCreacion'];
        $strHolding            = $arrayParametros['strHolding'] ? $arrayParametros['strHolding'] :"";
        $strEstadoActivo       = 'Activo';
        $strEstadoPreCliente   = 'Pendiente';
        $strRolPreCliente      = 'Pre-cliente';
        $strObservacionOrigen  = "Creado desde TelcoCRM";
        $strIp                 = '127.0.0.1';
        $strMensaje            = "";
        try
        {
            $objInfoPersona        = $this->emcom->getRepository('schemaBundle:InfoPersona')->findOneBy(array('identificacionCliente' => $strRuc));
            $entityPersona         = new InfoPersona();
            $arrayParametrosCarac  = array( 'descripcionCaracteristica' => 'PROSPECTO_ORIGEN_TELCOCRM',
                                            'estado'                    => $strEstadoActivo );
            if(is_object($objInfoPersona))
            {
                $strMensaje ="Cliente existente en TelcoS+";
            }
            else
            {
                $this->emcom->getConnection()->beginTransaction();
                $entityPersona->setTipoIdentificacion($strTipoIdentificacion);
                $entityPersona->setIdentificacionCliente($strRuc);
                $entityPersona->setTipoEmpresa($strTipoEmpresa);
                $entityPersona->setDireccionTributaria($strDireccionCuenta);
                $entityPersona->setDireccion($strDireccionCuenta);
                $entityPersona->setTipoTributario($strTipoCliente);
                $entityPersona->setPagaIva($strPagaIva);
                $entityPersona->setNacionalidad($strNacionalidad);
                if(!empty($strNombreCuenta))
                {
                    $entityPersona->setRazonSocial($strNombreCuenta);
                    $entityPersona->setRepresentanteLegal($strRepresentanteLegal);
                }
                else
                {
                    $entityPersona->setNombres($strNombreCliente);
                    $entityPersona->setApellidos($strApellidoCliente);
                    $entityPersona->setGenero($strGenero);
                    if(!empty($strFechaNacimiento))
                    {
                        $arrayFechaNacimientoTemp = explode('-', $strFechaNacimiento);
                        $arrayFechaNacimiento     = array('day'  => $arrayFechaNacimientoTemp[2],
                                                          'month' => $arrayFechaNacimientoTemp[1],
                                                          'year'   => $arrayFechaNacimientoTemp[0]);
                        $entityPersona->setFechaNacimiento(date_create($arrayFechaNacimiento['year'] . '-' . 
                                                                $arrayFechaNacimiento['month'] . '-' . 
                                                                $arrayFechaNacimiento['day']));
                    }
                    if(!empty($intTitulo))
                    {
                        $entityAdmiTitulo = $this->emcom->getRepository('schemaBundle:AdmiTitulo')->find($intTitulo);
                        if(is_object($entityAdmiTitulo))
                        {
                            $entityPersona->setTituloId($entityAdmiTitulo);
                        }
                    }
                }
                $entityPersona->setOrigenProspecto('N');
                $entityPersona->setOrigenIngresos($strOrigenIngreso);
                $entityPersona->setOrigenWeb("S");
                $entityPersona->setFeCreacion(new \DateTime('now'));
                $entityPersona->setUsrCreacion($strUsuarioCreacion);
                $entityPersona->setIpCreacion($strIp);
                $entityPersona->setEstado($strEstadoPreCliente);
                $entityPersona->setContribuyenteEspecial($strContribuyente);
                $entityPersona->setNumeroConadis($strConadis);
                $entityPersona->setEstadoCivil($strEstadoCivil);

                $this->emcom->persist($entityPersona);

                $entityEmpresaRol = $this->emcom->getRepository('schemaBundle:InfoEmpresaRol')
                                                ->findPorNombreTipoRolPorEmpresa($strRolPreCliente, $strCodEmpresa);
                if(!is_object($entityEmpresaRol))
                {
                    throw new \Exception('No existe registro en la empresa rol');
                }
                $entityOficina = $this->emcom->getRepository('schemaBundle:InfoOficinaGrupo')->find($strOficinaFacturacion);
                if(!is_object($entityOficina))
                {
                    throw new \Exception('No existe registro en la oficina de facturación');
                }
                $objAdmiCaracteristica = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')->findOneBy($arrayParametrosCarac);
                if(!is_object($objAdmiCaracteristica))
                {
                    throw new \Exception('No existe caracteristica PROSPECTO_ORIGEN_TELCOCRM');
                }
                $entityPersonaEmpresaRol = new InfoPersonaEmpresaRol();
                $entityPersonaEmpresaRol->setEmpresaRolId($entityEmpresaRol);
                $entityPersonaEmpresaRol->setPersonaId($entityPersona);
                $entityPersonaEmpresaRol->setOficinaId($entityOficina);
                $entityPersonaEmpresaRol->setEsPrepago($strPrepago);
                $entityPersonaEmpresaRol->setFeCreacion(new \DateTime('now'));
                $entityPersonaEmpresaRol->setUsrCreacion($strUsuarioCreacion);
                $entityPersonaEmpresaRol->setEstado($strEstadoPreCliente);

                $this->emcom->persist($entityPersonaEmpresaRol);

                $entityPersonaHistorial = new InfoPersonaEmpresaRolHisto();
                $entityPersonaHistorial->setEstado($entityPersona->getEstado());
                $entityPersonaHistorial->setObservacion($strObservacionOrigen);
                $entityPersonaHistorial->setFeCreacion(new \DateTime('now'));
                $entityPersonaHistorial->setIpCreacion($strIp);
                $entityPersonaHistorial->setPersonaEmpresaRolId($entityPersonaEmpresaRol);
                $entityPersonaHistorial->setUsrCreacion($strUsuarioCreacion);

                $this->emcom->persist($entityPersonaHistorial);

                $entityInfoPersonaEmpresaRolCarac = new InfoPersonaEmpresaRolCarac();
                $entityInfoPersonaEmpresaRolCarac->setEstado($strEstadoActivo);
                $entityInfoPersonaEmpresaRolCarac->setFeCreacion(new \DateTime('now'));
                $entityInfoPersonaEmpresaRolCarac->setIpCreacion($strIp);
                $entityInfoPersonaEmpresaRolCarac->setUsrCreacion($strUsuarioCreacion);
                $entityInfoPersonaEmpresaRolCarac->setPersonaEmpresaRolId($entityPersonaEmpresaRol);
                $entityInfoPersonaEmpresaRolCarac->setCaracteristicaId($objAdmiCaracteristica);
                $entityInfoPersonaEmpresaRolCarac->setValor($objAdmiCaracteristica->getDescripcionCaracteristica());

                $this->emcom->persist($entityInfoPersonaEmpresaRolCarac);

                $arrayParamFormasContac                        = array ();
                $arrayParamFormasContac['strPrefijoEmpresa']   = $strPrefijoEmpresa;
                $arrayParamFormasContac['arrayFormasContacto'] = $arrayFormaContactos;
                $arrayParamFormasContac['strOpcionPermitida']  = 'NO';
                $arrayParamFormasContac['strNombrePais']       =  $strNombrePais;
                $arrayParamFormasContac['intIdPais']           =  $intIdPais;
                $arrayValidaciones                             = $this->serviceInfoPersonaFormaContacto
                                                                      ->validarFormasContactos($arrayParamFormasContac);

                if(!empty($arrayValidaciones))
                {
                    for($intContador=0; $intContador<count($arrayValidaciones);$intContador++)
                    {
                        $strError = $strError.$arrayValidaciones[$intContador]['mensaje_validaciones'].".\n";
                    }
                    throw new \Exception($strError);
                }
                for($intContador = 0; $intContador < count($arrayFormaContactos); $intContador ++)
                {
                    $entityPersonaFormaContacto = new InfoPersonaFormaContacto();
                    $entityPersonaFormaContacto->setValor($arrayFormaContactos [$intContador] ['valor']);
                    $entityPersonaFormaContacto->setEstado("Activo");
                    $entityPersonaFormaContacto->setFeCreacion(new \DateTime('now'));
                    if(isset($arrayFormaContactos[$intContador]['idFormaContacto']))
                    {
                        $entityAdmiFormaContacto = $this->emcom->getRepository('schemaBundle:AdmiFormaContacto')
                                                                ->find($arrayFormaContactos[$intContador]['idFormaContacto']);
                    }
                    else
                    {
                        $entityAdmiFormaContacto = $this->emcom->getRepository('schemaBundle:AdmiFormaContacto')
                                                               ->findPorDescripcionFormaContacto($arrayFormaContactos[$intContador]['formaContacto']);
                    }
                    $entityPersonaFormaContacto->setFormaContactoId($entityAdmiFormaContacto);
                    $entityPersonaFormaContacto->setIpCreacion($strIp);
                    $entityPersonaFormaContacto->setPersonaId($entityPersona);
                    $entityPersonaFormaContacto->setUsrCreacion($strUsuarioCreacion);
                    $this->emcom->persist($entityPersonaFormaContacto);
                }
                
                if($strHolding!='' && $strPrefijoEmpresa == 'TN')
                {
                    $objAdmiParametroCab    = $this->emcom->getRepository('schemaBundle:AdmiParametroCab')
                                                ->findOneBy(array('nombreParametro'   => 'HOLDING DE EMPRESAS',
                                                                  'estado'            => 'Activo'));
                    if(is_object($objAdmiParametroCab) && !empty($objAdmiParametroCab))
                    {
                        $objAdmiParametroDet    = $this->emcom->getRepository('schemaBundle:AdmiParametroDet')
                                                ->findOneBy(array('parametroId'   => $objAdmiParametroCab->getId(),
                                                                  'valor1'        => $strHolding));
                        
                        if(is_object($objAdmiParametroDet) && !empty($objAdmiParametroDet))
                        {
                            $strCaracteristica     = 'HOLDING EMPRESARIAL';
                            $strEstado             = 'Activo';
                            $objCaracteristicaHol  = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                                ->getCaracteristicaPorDescripcionPorEstado($strCaracteristica, $strEstado);
                            if(is_object($objCaracteristicaHol))
                            {
                                $objPersonaEmpresaRolCarac = new InfoPersonaEmpresaRolCarac();
                                $objPersonaEmpresaRolCarac->setPersonaEmpresaRolId($entityPersonaEmpresaRol);
                                $objPersonaEmpresaRolCarac->setCaracteristicaId($objCaracteristicaHol);
                                $objPersonaEmpresaRolCarac->setFeCreacion(new \DateTime('now'));
                                $objPersonaEmpresaRolCarac->setUsrCreacion($strUsuarioCreacion);
                                $objPersonaEmpresaRolCarac->setIpCreacion($strIp);
                                $objPersonaEmpresaRolCarac->setValor($objAdmiParametroDet->getId());
                                $objPersonaEmpresaRolCarac->setEstado($strEstado);
                                $this->emcom->persist($objPersonaEmpresaRolCarac);
                            }
                            else
                            {
                                throw new \Exception("No se ha definido la característica".$strCaracteristica."");
                            }
                        }
                    }
                    
                }

                $this->emcom->flush();

                if($this->emcom->getConnection()->isTransactionActive())
                {
                    $strMensaje ="Pre-Cliente creado con exito en Telcos+";
                    $this->emcom->getConnection()->commit();
                    $this->emcom->getConnection()->close();
                }
            }
        }
        catch(\Exception $ex)
        {
            $strMensaje ="Fallo al crear un Pre-Cliente, intente nuevamente.\n". $ex->getMessage();
            if($this->emcom->getConnection()->isTransactionActive())
            {
                $this->emcom->getConnection()->rollback();
                $this->emcom->getConnection()->close();
            }
        }
        return $strMensaje;
    }
    /**
     * Documentación para el método 'crearPreCliente'.
     * Crea un nuevo Pre-cliente
     * 
     * Consideracion: Se aumenta campo origen WEB ya que se requiere que se identifiquen los Clientes que han sido ingresados 
     * por la versión Web y los que se ingresaron mediante el Mobil.
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>       
     * @version 1.1 26-03-2015
     * 
     * @param array $arrayParametros [  'objInfoPersona'      => referencia a datos de la persona,
     *                                  'strCodEmpresa'       => código de empresa en sessión,
     *                                  'intOficinaId'        => id de la oficina en sesión,
     *                                  'strUsrCreacion'      => usuario de creación,
     *                                  'strClientIp'         => ip del usuario,
     *                                  'arrayDatosForm'      => datos del formulario,
     *                                  'strPrefijoEmpresa'   => prefijo de empresa en sessión,
     *                                  'arrayFormasContacto' => formas de contacto, si es null se obtiene de arrayDatosForm
     *                                  'arrayDataLogTmc'     => datos para registro de logs TMC,
     *                               ]
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>     
     * @version 1.2 31-08-2015  
     * Se agrega Validacion a nivel de service para el correcto ingreso de las formas de Contacto
     * 
     * Descripcion: Se agrega Validacion de Campo Fecha de Nacimiento, campo obligatorio y solo se acepta
     * personas mayores de edad (Mayores de 18 anios)
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>          
     * @version 1.3 03-09-2015 
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>  
     * @version 1.4 17-08-2015  
     * conversion desde el mobile DATE -> ARRAY del Campo Fecha de Nacimiento
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>  
     * @version 1.5 15-06-2016
     * Se Valida para que permita el reingreso de un Cliente ya Cancelado, considerando que ya existe con ROL de Pre-cliente en Telcos
     *  
     * @author Edgar Holguin <eholguin@telconet.ec>  
     * @version 1.6 12-08-2016  
     * Se agrega validación para no permitir ingreso de campo identificación vacio.
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.7 08-11-2016
     * Se envia array de Parametros $arrayParamFormasContac a la funcion "validarFormasContactos" se agrega strOpcionPermitida y strPrefijoEmpresa, 
     * Prefijo de empresa en sesion para validar que para empresa MD no se obligue el ingreso de al menos 1 correo.
     * 
     * @author Edgar Holguin <eholguin@telconet.ec>  
     * @version 1.8 15-11-2016  
     * Se agrega seteo de campo dirección con el mismo valor de campo dirección tributaria en creación de prospecto.
     * 
     * @author Juan Lafuente <jlafuente@telconet.ec>  
     * @version 1.9 16-01-2017 
     * Se genera la IPERCaracteristica Login para la autenticacion del usuario en Extranet y Mobile Netlife
     * 
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.10 03-07/2017
     * Se agregan las variables strNombrePais e intIdPais para determinar qué país se valida en la forma de contactos.
     *  
     * @author Anabelle Peñaherrera<apenaherrera@telconet.ec>
     * @version 1.11 02-10-2017
     * Se agrega que para la empresa MD se asigne el CICLO_FACTURACION al cliente a nivel de caracteristica en INFO_PERSONA_EMPRESA_ROL_CARACT
     * considerando asignar por default el ciclo que se encuentre  en estado ACTIVO.
     * 
     * Se valida que el precliente tenga un ciclo asignado y se lo inactiva. Debido a que es mandatorio que el precliente tenga sólo un ciclo activo.
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.12
     * @since 08-05-2018
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.13 05-07-2019
     * Se modifica función para que reciba array de parámetros, se renombran variables según estándar establecido.
     * Se agrega registro de logs de errores para TMC.
     *  
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 2.0 03-05-2021 - Se agrega lógica para guardar si el clientes es es_distribuidor.
     *
     * @author Katherine Solis <ksolis@telconet.ec>
     * @version 3.0 17-05-2022 - Se registra observación para identificar el usuario responsable de verificar ruc inválido
     * @throws Exception
     */    
    public function crearPreCliente(&$objInfoPersona,$arrayParametros)        
    {
        $strCodEmpresa        = $arrayParametros['strCodEmpresa'];
        $intOficinaId         = $arrayParametros['intOficinaId'];
        $strUsrCreacion       = $arrayParametros['strUsrCreacion'];
        $strClientIp          = $arrayParametros['strClientIp'];
        $arrayDatosForm       = $arrayParametros['arrayDatosForm'];
        $strPrefijoEmpresa    = $arrayParametros['strPrefijoEmpresa'];        
        $arrayFormasContacto  = $arrayParametros['arrayFormasContacto'];
       
        $intIdPais            = $arrayDatosForm['intIdPais'];
        $strNombrePais        = $arrayDatosForm['strNombrePais'];
        
        if(is_null($arrayDatosForm['identificacionCliente']) || trim($arrayDatosForm['identificacionCliente'])=="")
        {
            throw new \Exception('Identificación no válida.Favor verificar. ');
        }        
        if($arrayDatosForm['yaexiste'] == 'S')
        {
            // FIXME: si desde la pagina no se permite crear existente, aca deberia lanzarse excepcion
            $objInfoPersona = $this->emcom->getRepository('schemaBundle:InfoPersona')->find($arrayDatosForm['id']);
        }
        else
        {
            $objInfoPersona = new InfoPersona();
        }

        if(empty($arrayFormasContacto))
        {
            $arrayFormasContacto = array();
            // si no se ha especificado formas de contacto, obtenerlas de $datos_form
            if(isset($arrayDatosForm['formas_contacto']))
            {
                if(is_array($arrayDatosForm['formas_contacto']))
                {
                    $arrayFormasContacto = $arrayDatosForm['formas_contacto'];
                }
                else
                {
                    $arrayFormContacto = explode(',', $arrayDatosForm['formas_contacto']);
                    for($intContador = 0; $intContador < count($arrayFormContacto); $intContador+=3)
                    {
                        $arrayFormasContacto[] = array(
                            'formaContacto' => $arrayFormContacto[$intContador + 1],
                            'valor'         => $arrayFormContacto[$intContador + 2]
                        );
                    }
                }
            }
        }
        
        $strError          = '';
        $this->emcom->getConnection()->beginTransaction();

        try
        {
            $strExistePreCliente = 'N';
            //Valido que no registre Prospecto si este ya existe con Rol de Cliente o de PreCliente           
            $strDescRoles = array ('Cliente');
            $strEstados   = array ('Activo');           
            $objRolesCliente = $this->emcom->getRepository('schemaBundle:InfoPersonaEmpresaRol')                                            
            ->buscaClientesPorIdentificacionTipoRolEmpresaEstados($arrayDatosForm['identificacionCliente'],$strDescRoles,$strCodEmpresa,$strEstados);
            if($objRolesCliente!= null)
            {
                throw new \Exception('Identificacion ya existente como un Cliente, Por favor ingrese otra Identificacion.');  
            }
            else
            {
                $strDescRoles = array ('Pre-cliente');
                $strEstados   = array ('Activo','Pendiente'); 
                $objRolesPreCliente = $this->emcom->getRepository('schemaBundle:InfoPersonaEmpresaRol')                                            
                ->buscaClientesPorIdentificacionTipoRolEmpresaEstados($arrayDatosForm['identificacionCliente'],
                                                                      $strDescRoles,
                                                                      $strCodEmpresa,
                                                                      $strEstados);
                if($objRolesPreCliente!= null)
                {
                    throw new \Exception('Identificacion ya existente como un Pre-cliente, Por favor ingrese otra Identificacion.');   
                }
                else
                {
                    // Se cancela en el Historial el Rol de Pre-cliente Inactivo para el caso que tenga Rol de Clientes Cancelado
                    // para poder permitir el regingreso de un cliente Cancelado.
                    $strDescRoles        = array ('Pre-cliente');
                    $strEstados          = array ('Inactivo');           
                    $arrayRolesPreClienteI = $this->emcom->getRepository('schemaBundle:InfoPersonaEmpresaRol')                                            
                    ->buscaClientesPorIdentificacionTipoRolEmpresaEstados($arrayDatosForm['identificacionCliente'],
                                                                          $strDescRoles,$strCodEmpresa,$strEstados);
                    
                    $strDescRoles     = array ('Cliente');
                    $strEstados       = array ('Cancelado');           
                    $arrayRolesClienteC = $this->emcom->getRepository('schemaBundle:InfoPersonaEmpresaRol')                                            
                    ->buscaClientesPorIdentificacionTipoRolEmpresaEstados($arrayDatosForm['identificacionCliente'],
                                                                          $strDescRoles,$strCodEmpresa,$strEstados);
                    
                    if($arrayRolesPreClienteI!= null && $arrayRolesClienteC!= null)
                    {
                        $strExistePreCliente = 'S';
                        foreach( $arrayRolesPreClienteI as $arrayRolesPreClienteI )
                        {       
                            $objInfoPersonaEmpresaRol = $arrayRolesPreClienteI;       
                            break;                      
                        }   
                         // Genero registro en el Historial del Pre-cliente en estado Cancelado
                        $objInfoPersonaEmpresaRolHistoC = $this->emcom->getRepository('schemaBundle:InfoPersonaEmpresaRolHisto')                                            
                        ->getObtienePersEmpRolHisto($arrayDatosForm['identificacionCliente'],
                                                    $strDescRoles,$strCodEmpresa,$strEstados);                                           
                       
                        // Si existe Historial del Cliente Cancelado, clono y creo registro de Historial para Pre-cliente Cancelado 
                        // en base al ultimo Historial del Cliente
                        if($objInfoPersonaEmpresaRolHistoC!=null)
                        {                            
                            $objInfoPersonaEmpresaRolHisto   = new InfoPersonaEmpresaRolHisto();
                            $objInfoPersonaEmpresaRolHisto   = clone $objInfoPersonaEmpresaRolHistoC;
                            $objInfoPersonaEmpresaRolHisto->setPersonaEmpresaRolId($objInfoPersonaEmpresaRol);                            
                            $this->emcom->persist($objInfoPersonaEmpresaRolHisto);            
                        }
                                
                        // Cancelo la Forma de Pago del Pre-cliente 
                        $objInfoPersonaEmpFormaPago = $this->emcom->getRepository('schemaBundle:InfoPersonaEmpFormaPago')
                            ->findByPersonaEmpresaRolId($objInfoPersonaEmpresaRol); 
                        foreach( $objInfoPersonaEmpFormaPago as $objInfoPersonaEmpFormaPago )
                        {
                            $objInfoPersonaEmpFormaPago->setEstado('Cancelado'); 
                            $this->emcom->persist($objInfoPersonaEmpFormaPago);       
                        }                        
                    }
                }
            }
            if(!isset($arrayDatosForm['tipoEmpresa']))
            {
                $arrayDatosForm['tipoEmpresa'] = null;
            }
            $objInfoPersona->setIdentificacionCliente($arrayDatosForm['identificacionCliente']);
            $objInfoPersona->setTipoEmpresa($arrayDatosForm['tipoEmpresa']);
            $objInfoPersona->setTipoTributario($arrayDatosForm['tipoTributario']);
            $objInfoPersona->setRazonSocial($arrayDatosForm['razonSocial']);
            $objInfoPersona->setRepresentanteLegal($arrayDatosForm['representanteLegal']);
            $objInfoPersona->setNacionalidad($arrayDatosForm['nacionalidad']);
            $objInfoPersona->setDireccion($arrayDatosForm['direccionTributaria']);
            $objInfoPersona->setDireccionTributaria($arrayDatosForm['direccionTributaria']);
            if($strPrefijoEmpresa == 'TN')
            {             
                $objInfoPersona->setContribuyenteEspecial($arrayDatosForm['contribuyenteEspecial']);
                $objInfoPersona->setPagaIva($arrayDatosForm['pagaIva']);  
                $objInfoPersona->setNumeroConadis($arrayDatosForm['numeroConadis']); 
            }
            else
            {
                $objInfoPersona->setPagaIva('S');    
            }
            // conversion desde el mobile DATE -> ARRAY
            if(!is_array($arrayDatosForm['fechaNacimiento']) && !isset($arrayDatosForm['tipoEmpresa']))
            {
                $objFechaNacimiento                = $arrayDatosForm['fechaNacimiento']->format('Y-m-d');
                $arrayFechaNacimiento              = explode('-', $objFechaNacimiento);
                $arrayDatosForm['fechaNacimiento'] = array('year'  => $arrayFechaNacimiento[0],
                                                           'month' => $arrayFechaNacimiento[1],
                                                           'day'   => $arrayFechaNacimiento[2]);
            }
            if(!$arrayDatosForm['tipoEmpresa'])
            {
                if(!$arrayDatosForm['fechaNacimiento'] 
                    || (!$arrayDatosForm['fechaNacimiento'] ['year'] 
                        && !$arrayDatosForm['fechaNacimiento'] ['month'] 
                        && !$arrayDatosForm['fechaNacimiento'] ['day']))
                {
                    if($strPrefijoEmpresa == 'MD' || $strPrefijoEmpresa == 'EN')
                    {
                        throw new \Exception('La Fecha de Nacimiento es un campo obligatorio - No se pudo guardar el Prospecto'); 
                    }
                }                                                
                else
                {
                    if(is_array($arrayDatosForm['fechaNacimiento']) &&
                        ($arrayDatosForm['fechaNacimiento'] ['year'] && 
                        $arrayDatosForm['fechaNacimiento'] ['month'] && 
                        $arrayDatosForm['fechaNacimiento'] ['day']))
                    {
                         $intEdad = $this->devuelveEdadPorFecha($arrayDatosForm['fechaNacimiento'] ['year'] .
                                    '-' . $arrayDatosForm['fechaNacimiento'] ['month'] .
                                    '-' . $arrayDatosForm['fechaNacimiento'] ['day']);       
                         if($intEdad<18)
                         {
                             throw new \Exception('La Fecha de Nacimiento ingresada corresponde a un menor de edad - '
                                 . 'No se pudo guardar el Prospecto : '.$arrayDatosForm['fechaNacimiento'] ['year'] .
                                    '-' . $arrayDatosForm['fechaNacimiento'] ['month'] .
                                    '-' . $arrayDatosForm['fechaNacimiento'] ['day']); 
                         }
                    }
                }
                $objInfoPersona->setNombres($arrayDatosForm['nombres']);
                $objInfoPersona->setApellidos($arrayDatosForm['apellidos']);
                if($arrayDatosForm['fechaNacimiento'] instanceof \DateTime)
                {
                    $objInfoPersona->setFechaNacimiento($arrayDatosForm['fechaNacimiento']);
                }
                else if(is_array($arrayDatosForm['fechaNacimiento']) &&
                        $arrayDatosForm['fechaNacimiento'] ['year']  && 
                        $arrayDatosForm['fechaNacimiento'] ['month'] && 
                        $arrayDatosForm['fechaNacimiento'] ['day'])
                {
                        $strFechaNacimiento = $arrayDatosForm['fechaNacimiento'] ['year'] . '-' . 
                                              $arrayDatosForm['fechaNacimiento'] ['month'] . '-' . 
                                              $arrayDatosForm['fechaNacimiento'] ['day'];
                        $objInfoPersona->setFechaNacimiento(date_create($strFechaNacimiento));
                    
                }
                $objAdmiTitulo = $this->emcom->getRepository('schemaBundle:AdmiTitulo')->find($arrayDatosForm['tituloId']);
                if(is_object($objAdmiTitulo))
                {
                    $objInfoPersona->setTituloId($objAdmiTitulo);
                }
                $objInfoPersona->setGenero($arrayDatosForm['genero']);
                $objInfoPersona->setEstadoCivil($arrayDatosForm['estadoCivil']);                
            }
            //cambios DINARDARP - se agrega campo origenes de ingresos
            $objInfoPersona->setOrigenIngresos($arrayDatosForm['origenIngresos']);
            $objInfoPersona->setOrigenProspecto('N');
            
            //Campo para marcar si el registro de una persona se origino en la aplicacion WEB Telcos "S"
            if($arrayDatosForm['origen_web'] && ($arrayDatosForm['origen_web']=="S" || $arrayDatosForm['origen_web']=="N"))
            {
                $objInfoPersona->setOrigenWeb($arrayDatosForm['origen_web']);               
            }
            if($arrayDatosForm['yaexiste'] == 'N')
            {
                if(!$arrayDatosForm['tipoIdentificacion'])
                {
                    throw new \Exception('El tipo de Identificacion es un campo obligatorio - No se pudo Ingresar el Prospecto'); 
                }
                $objInfoPersona->setTipoIdentificacion($arrayDatosForm['tipoIdentificacion']);
                $objInfoPersona->setFeCreacion(new \DateTime('now'));
                $objInfoPersona->setUsrCreacion($strUsrCreacion);
                $objInfoPersona->setIpCreacion($strClientIp);
            }
            $objInfoPersona->setEstado('Pendiente');
            $this->emcom->persist($objInfoPersona);
            // $this->emcom->flush (); // demasiado flush reduce performance
            // ASIGNA ROL DE PRE-CLIENTE A LA PERSONA
            if($strExistePreCliente == 'S')
            {
                $objPersonaEmpresaRol = $objInfoPersonaEmpresaRol;
            }
            else
            {
                $objPersonaEmpresaRol = new InfoPersonaEmpresaRol();
            }
                        
            $entityEmpresaRol = $this->emcom->getRepository('schemaBundle:InfoEmpresaRol')
                                            ->findPorNombreTipoRolPorEmpresa('Pre-cliente', $strCodEmpresa);
            $objPersonaEmpresaRol->setEmpresaRolId($entityEmpresaRol);
            $objPersonaEmpresaRol->setPersonaId($objInfoPersona);

            if($strPrefijoEmpresa == 'TN')
            { 
                $objInfoOficinaGrupo = $this->emcom->getRepository('schemaBundle:InfoOficinaGrupo')
                                                   ->find((int)$arrayDatosForm['idOficinaFacturacion']);                
                if(is_object($objInfoOficinaGrupo))
                {
                    $objPersonaEmpresaRol->setOficinaId($objInfoOficinaGrupo);
                }
                $objPersonaEmpresaRol->setEsPrepago($arrayDatosForm['esPrepago']);                
            }
            else 
            {                 
                $objInfoOficinaGrupo = $this->emcom->getRepository('schemaBundle:InfoOficinaGrupo')->find($intOficinaId);
                if(is_object($objInfoOficinaGrupo))
                {
                    $objPersonaEmpresaRol->setOficinaId($objInfoOficinaGrupo);
                }
                $objPersonaEmpresaRol->setEsPrepago('S'); 
                
                //Obtengo Caracteristica de CICLO_FACTURACION
                $objCaracteristicaCiclo = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                      ->findOneBy(array("descripcionCaracteristica" => "CICLO_FACTURACION", "estado" => "Activo"));
                if (!is_object($objCaracteristicaCiclo))
                {
                    throw new \Exception('No existe Caracteristica CICLO_FACTURACION - No se pudo Ingresar el Prospecto'); 
                }
                //Obtengo el Ciclo de Facturacion Activo por empresa en sesion
                $objCicloFacturacion = $this->emcom->getRepository('schemaBundle:AdmiCiclo')
                                                   ->findOneBy(array("empresaCod" => $strCodEmpresa, "estado" => "Activo"));
                if (!is_object($objCicloFacturacion))
                {
                    throw new \Exception('No existe Ciclo de Facturación Activo - No se pudo Ingresar el Prospecto'); 
                }
                //Se busca si el precliente tiene un ciclo asignado anteriormente ya sea por recontratación o inconsistencia de migración.
                $arrayParametrosBuscaCiclo = array("estado"              => "Activo",
                                                   "personaEmpresaRolId" => $objPersonaEmpresaRol->getId(),
                                                   "caracteristicaId"    => $objCaracteristicaCiclo->getId());
                $arrayListPersEmpRolCarac  = $this->emcom->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                         ->findBy($arrayParametrosBuscaCiclo);
                foreach($arrayListPersEmpRolCarac as $objInfoPerEmpRolCarac)
                {
                    //Se actualiza el estado del registro
                    $objInfoPerEmpRolCarac->setEstado("Inactivo");
                    $objInfoPerEmpRolCarac->setFeUltMod(new \DateTime("now"));
                    $objInfoPerEmpRolCarac->setUsrUltMod("telcos_ciclo");
                    $this->emcom->persist($objInfoPerEmpRolCarac);

                    //Se crea el historial por cambio de estado en caracteristica del ciclo.
                    $objInfoPerEmpRolHisto = new InfoPersonaEmpresaRolHisto();
                    $objInfoPerEmpRolHisto->setUsrCreacion("telcos_ciclo");
                    $objInfoPerEmpRolHisto->setFeCreacion(new \DateTime('now'));
                    $objInfoPerEmpRolHisto->setIpCreacion('127.0.0.1');
                    $objInfoPerEmpRolHisto->setEstado("Inactivo");
                    $objInfoPerEmpRolHisto->setPersonaEmpresaRolId($objPersonaEmpresaRol);
                    $objInfoPerEmpRolHisto->setObservacion('Se inactiva el ciclo anteriormente asignado');
                    $this->emcom->persist($objInfoPerEmpRolHisto);
                }
                //Inserto Caracteristica de CICLO_FACTURACION en el Pre_Cliente
                $objPersEmpRolCaracCiclo = new InfoPersonaEmpresaRolCarac();
                $objPersEmpRolCaracCiclo->setPersonaEmpresaRolId($objPersonaEmpresaRol);
                $objPersEmpRolCaracCiclo->setCaracteristicaId($objCaracteristicaCiclo);
                $objPersEmpRolCaracCiclo->setValor($objCicloFacturacion->getId());
                $objPersEmpRolCaracCiclo->setFeCreacion(new \DateTime('now'));
                $objPersEmpRolCaracCiclo->setUsrCreacion($strUsrCreacion);
                $objPersEmpRolCaracCiclo->setEstado('Activo'); 
                $objPersEmpRolCaracCiclo->setIpCreacion($strClientIp);                
                $this->emcom->persist($objPersEmpRolCaracCiclo);
                
                //Inserto Historial de creacion de caracteristica de CICLO_FACTURACION en el Pre_cliente                
                $objPersEmpRolCaracCicloHisto = new InfoPersonaEmpresaRolHisto(); 
                $objPersEmpRolCaracCicloHisto->setUsrCreacion($strUsrCreacion);
                $objPersEmpRolCaracCicloHisto->setFeCreacion(new \DateTime('now'));
                $objPersEmpRolCaracCicloHisto->setIpCreacion($strClientIp);                
                $objPersEmpRolCaracCicloHisto->setEstado($objInfoPersona->getEstado());
                $objPersEmpRolCaracCicloHisto->setPersonaEmpresaRolId($objPersonaEmpresaRol);                
                $objPersEmpRolCaracCicloHisto->setObservacion('Se creo Pre-cliente con Ciclo de Facturación: '
                                                              .$objCicloFacturacion->getNombreCiclo());
                $this->emcom->persist($objPersEmpRolCaracCicloHisto);    
            }            
            $objPersonaEmpresaRol->setFeCreacion(new \DateTime('now'));
            $objPersonaEmpresaRol->setUsrCreacion($strUsrCreacion);
            $objPersonaEmpresaRol->setEstado('Pendiente');   
            $this->emcom->persist($objPersonaEmpresaRol);
            // $this->emcom->flush (); // demasiado flush reduce performance
            // GUARDA LA FORMA DE PAGO POR PERSONA_EMPRESA_ROL_ID
            if($strPrefijoEmpresa == 'MD' || $strPrefijoEmpresa == 'EN')
            {
                $entityFormaPago = $this->emcom->getRepository('schemaBundle:AdmiFormaPago')->find($arrayDatosForm['formaPagoId']);
                $entityAdmiTipoCuenta = null;
                $entityAdmiBancoTipoCuenta = null;
                if(!empty($arrayDatosForm['tipoCuentaId']))
                {
                    $entityAdmiTipoCuenta = $this->emcom->getRepository('schemaBundle:AdmiTipoCuenta')->find($arrayDatosForm['tipoCuentaId']);
                }
                if(!empty($arrayDatosForm['bancoTipoCuentaId']))
                {
                    $entityAdmiBancoTipoCuenta = $this->emcom->getRepository('schemaBundle:AdmiBancoTipoCuenta')
                                                             ->find($arrayDatosForm['bancoTipoCuentaId']);
                }
                $entityPersonaEmpFormaPago = new InfoPersonaEmpFormaPago();
                $entityPersonaEmpFormaPago->setFormaPagoId($entityFormaPago);
                $entityPersonaEmpFormaPago->setTipoCuentaId($entityAdmiTipoCuenta);
                $entityPersonaEmpFormaPago->setBancoTipoCuentaId($entityAdmiBancoTipoCuenta);
                $entityPersonaEmpFormaPago->setEstado('Activo');
                $entityPersonaEmpFormaPago->setUsrCreacion($strUsrCreacion);
                $entityPersonaEmpFormaPago->setFeCreacion(new \DateTime('now'));
                $entityPersonaEmpFormaPago->setIpCreacion($strClientIp);
                $entityPersonaEmpFormaPago->setPersonaEmpresaRolId($objPersonaEmpresaRol);
                $this->emcom->persist($entityPersonaEmpFormaPago);
            }
            if(!empty($arrayDatosForm['idperreferido']))
            {
                // GRABA RELACION ENTRE REFERIDO Y PRE-CLIENTE
                $entityPersonaRef = new InfoPersonaReferido();
                $entityPersonaRef->setPersonaEmpresaRolId($objPersonaEmpresaRol);
                $entityReferido = $this->emcom->getRepository('schemaBundle:InfoPersona')->find($arrayDatosForm['idreferido']);
                $entityPersonaRef->setReferidoId($entityReferido);
                $entityReferidoPer = $this->emcom->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($arrayDatosForm['idperreferido']);
                $entityPersonaRef->setRefPersonaEmpresaRolId($entityReferidoPer);
                $entityPersonaRef->setFeCreacion(new \DateTime('now'));
                $entityPersonaRef->setUsrCreacion($strUsrCreacion);
                $entityPersonaRef->setIpCreacion($strClientIp);
                $entityPersonaRef->setEstado('Activo');
                $this->emcom->persist($entityPersonaRef);
                // $this->emcom->flush (); // demasiado flush reduce performance
            }

            // REGISTRA EN LA TABLA DE PERSONA_EMPRESA_ROL_HISTO
            $objInfoPersonaEmpresaRolHisto = new InfoPersonaEmpresaRolHisto();
            $objInfoPersonaEmpresaRolHisto->setEstado($objInfoPersona->getEstado());
            $objInfoPersonaEmpresaRolHisto->setFeCreacion(new \DateTime('now'));
            $objInfoPersonaEmpresaRolHisto->setIpCreacion($strClientIp);
            $objInfoPersonaEmpresaRolHisto->setPersonaEmpresaRolId($objPersonaEmpresaRol);
            $objInfoPersonaEmpresaRolHisto->setUsrCreacion($strUsrCreacion);
            //validacion Ruc verificado en portal del SRI por parte del usuario
            if($arrayDatosForm["observacion"]=='true')
            {
                $strMensajeInfoPersonaEmpresaRolHisto = 'Ruc verificado en portal del SRI por parte del usuario';
            }
            else
            {
                $strMensajeInfoPersonaEmpresaRolHisto = null;
            }
            $objInfoPersonaEmpresaRolHisto->setObservacion($strMensajeInfoPersonaEmpresaRolHisto);

            $this->emcom->persist($objInfoPersonaEmpresaRolHisto);            


            /* Se envia array de Parametros y se agrega strOpcionPermitida y strPrefijoEmpresa, Prefijo de empresa en sesion para validar
             * que para empresa MD no se obligue el ingreso de al menos 1 correo */
            $arrayParamFormasContac                        = array ();
            $arrayParamFormasContac['strPrefijoEmpresa']   = $strPrefijoEmpresa;
            $arrayParamFormasContac['arrayFormasContacto'] = $arrayFormasContacto;
            $arrayParamFormasContac['strOpcionPermitida']  = 'NO';
            $arrayParamFormasContac['strNombrePais']       =  $strNombrePais;
            $arrayParamFormasContac['intIdPais']           =  $intIdPais;
            $arrayValidaciones   = $this->serviceInfoPersonaFormaContacto->validarFormasContactos($arrayParamFormasContac);

            if($arrayValidaciones)
            {    
                foreach($arrayValidaciones as $key => $mensaje_validaciones)
                {
                    foreach($mensaje_validaciones as $key_msj => $value)
                    {                      
                        $strError = $strError.$value.".\n";                        
                    }
                }
                throw new \Exception("No se pudo guardar el Prospecto - " . $strError);
            } 
            if($arrayDatosForm['yaexiste'] == 'S')
            {
                //PONE ESTADO INACTIVO A TODOS LAS FORMAS DE CONTACTO DE LA PERSONA QUE tengan estado ACTIVO
                $this->serviceInfoPersonaFormaContacto->inactivarPersonaFormaContactoActivasPorPersona($objInfoPersona->getId(), $strUsrCreacion);
            }
            // ReGISTRA LAS FORMAS DE CONTACTO DEL PRE-CLIENTE
            for($intCont = 0; $intCont < count($arrayFormasContacto); $intCont ++)
            {
                $objInfoPersonaFormaContacto = new InfoPersonaFormaContacto();
                $objInfoPersonaFormaContacto->setValor($arrayFormasContacto [$intCont] ['valor']);
                $objInfoPersonaFormaContacto->setEstado("Activo");
                $objInfoPersonaFormaContacto->setFeCreacion(new \DateTime('now'));
                if(isset($arrayFormasContacto[$intCont]['idFormaContacto']))
                {
                    $entityAdmiFormaContacto = $this->emcom->getRepository('schemaBundle:AdmiFormaContacto')
                                                           ->find($arrayFormasContacto[$intCont]['idFormaContacto']);
                }
                else
                {
                    $entityAdmiFormaContacto = $this->emcom->getRepository('schemaBundle:AdmiFormaContacto')
                                                           ->findPorDescripcionFormaContacto($arrayFormasContacto [$intCont] ['formaContacto']);
                }
                $objInfoPersonaFormaContacto->setFormaContactoId($entityAdmiFormaContacto);
                $objInfoPersonaFormaContacto->setIpCreacion($strClientIp);
                $objInfoPersonaFormaContacto->setPersonaId($objInfoPersona);
                $objInfoPersonaFormaContacto->setUsrCreacion($strUsrCreacion);
                $this->emcom->persist($objInfoPersonaFormaContacto);
                // $this->emcom->flush (); // demasiado flush reduce performance
            }
            // =========================================================================================================
            // Se genera la IPERCaracteristica Login para la autenticacion del usuario en Extranet y Mobile Netlife
            // Nuevo Cambio por Juan Lafuente
            // =========================================================================================================
            $strCaracteristica    = 'USUARIO';
            $strEstado            = 'Activo';
            $objCaracteristica    = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->getCaracteristicaPorDescripcionPorEstado($strCaracteristica, $strEstado);
            if(!is_object($objCaracteristica))
            {
                throw new \Exception("No se ha definido la característica".$strCaracteristica."");
            }
            // Se verifica que ya tenga una caracteristica "LOGIN" asignada, si no se crea una nueva.
            $arrayCriterios = array (
                                     'caracteristicaId'    => $objCaracteristica->getId(),
                                     'personaEmpresaRolId' => $objPersonaEmpresaRol->getId()
                                    );
            $objPersonaEmpresaRolCarac = $this->emcom->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')->findOneBy($arrayCriterios);
            if(!is_object($objPersonaEmpresaRolCarac))
            {
                $objPersonaEmpresaRolCarac = new InfoPersonaEmpresaRolCarac();
                $objPersonaEmpresaRolCarac->setPersonaEmpresaRolId($objPersonaEmpresaRol);
                $objPersonaEmpresaRolCarac->setCaracteristicaId($objCaracteristica);
                $objPersonaEmpresaRolCarac->setFeCreacion(new \DateTime('now'));
                $objPersonaEmpresaRolCarac->setUsrCreacion($strUsrCreacion);
                $objPersonaEmpresaRolCarac->setIpCreacion($strClientIp);
            }
            $objPersonaEmpresaRolCarac->setValor($arrayDatosForm['identificacionCliente']);
            $objPersonaEmpresaRolCarac->setEstado($strEstado);
          
            $this->emcom->persist($objPersonaEmpresaRolCarac);
            //======================================================================
            //===================HAGREGAMOS HOLDING PARA CLIENTES TN
            if(isset($arrayDatosForm['holding']) && !empty($arrayDatosForm['holding']) && $strPrefijoEmpresa == 'TN')
            {
                $strCaracteristica     = 'HOLDING EMPRESARIAL';
                $strEstado             = 'Activo';
                $objCaracteristicaHol  = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                    ->getCaracteristicaPorDescripcionPorEstado($strCaracteristica, $strEstado);
                if(is_object($objCaracteristicaHol))
                {
                    $objPersonaEmpresaRolCarac = new InfoPersonaEmpresaRolCarac();
                    $objPersonaEmpresaRolCarac->setPersonaEmpresaRolId($objPersonaEmpresaRol);
                    $objPersonaEmpresaRolCarac->setCaracteristicaId($objCaracteristicaHol);
                    $objPersonaEmpresaRolCarac->setFeCreacion(new \DateTime('now'));
                    $objPersonaEmpresaRolCarac->setUsrCreacion($strUsrCreacion);
                    $objPersonaEmpresaRolCarac->setIpCreacion($strClientIp);
                    $objPersonaEmpresaRolCarac->setValor($arrayDatosForm['holding']);
                    $objPersonaEmpresaRolCarac->setEstado($strEstado);
                    $this->emcom->persist($objPersonaEmpresaRolCarac);
                }
                else
                {
                    throw new \Exception("No se ha definido la característica".$strCaracteristica."");
                }
            }
            if(isset($arrayDatosForm['es_distribuidor']) && !empty($arrayDatosForm['es_distribuidor']) && $strPrefijoEmpresa == 'TN')
            {
                $strCaracteristicaDistribuidor = 'ES_DISTRIBUIDOR';
                $strEstado                     = 'Activo';
                $objCaracteristicaDistribuidor = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                      ->getCaracteristicaPorDescripcionPorEstado($strCaracteristicaDistribuidor, $strEstado);
                if(is_object($objCaracteristicaDistribuidor))
                {
                    $objPersonaEmpresaRolCarac = new InfoPersonaEmpresaRolCarac();
                    $objPersonaEmpresaRolCarac->setPersonaEmpresaRolId($objPersonaEmpresaRol);
                    $objPersonaEmpresaRolCarac->setCaracteristicaId($objCaracteristicaDistribuidor);
                    $objPersonaEmpresaRolCarac->setFeCreacion(new \DateTime('now'));
                    $objPersonaEmpresaRolCarac->setUsrCreacion($strUsrCreacion);
                    $objPersonaEmpresaRolCarac->setIpCreacion($strClientIp);
                    $objPersonaEmpresaRolCarac->setValor($arrayDatosForm['es_distribuidor']);
                    $objPersonaEmpresaRolCarac->setEstado($strEstado);
                    $this->emcom->persist($objPersonaEmpresaRolCarac);
                }
                else
                {
                    throw new \Exception("No se ha definido la característica ".$strCaracteristicaDistribuidor ."");
                }
            }
            //======================================================================

            $this->emcom->flush(); // demasiado flush reduce performance, es mejor un solo flush luego de un grupo de operaciones
            $this->emcom->getConnection()->commit();

            return $objPersonaEmpresaRol;
        }
        catch(\Exception $e)
        {
            if($this->emcom->getConnection()->isTransactionActive())
            {
                $this->emcom->getConnection()->rollBack();
            }
            $this->emcom->getConnection()->close();
            error_log($e->getMessage());
          
            if($objInfoPersona->getOrigenWeb() === 'N')
            {
                $strMensaje = 'Error al ingresar Prospecto: ';
                $arrayParametrosLog['enterpriseCode']   = $strCodEmpresa;
                $arrayParametrosLog['logType']          = "1";
                $arrayParametrosLog['logOrigin']        = "TELCOS";
                $arrayParametrosLog['application']      = "PreClienteService";
                $arrayParametrosLog['appClass']         = "PreClienteService";
                $arrayParametrosLog['appMethod']        = "crearPreCliente";
                $arrayParametrosLog['appAction']        = "crearPreCliente";
                $arrayParametrosLog['messageUser']      = "ERROR";
                $arrayParametrosLog['status']           = "Error";
                $arrayParametrosLog['descriptionError'] = $strMensaje.$e->getMessage();
                $arrayParametrosLog['inParameters']     = json_encode($arrayParametros);
                $arrayParametrosLog['creationUser']     = $strUsrCreacion;    

                $this->serviceUtil->insertLog($arrayParametrosLog);
            }
            
            throw $e;
        }
    }

    /**
     *  Saca la forma de pago de la persona empresa rol
     *  @param integer $id_persona_empresa_rol
     */
    function getDatosPersonaEmpFormaPago($id_persona,$id_empresa)
    {
        $resultado= $this->emcom->getRepository('schemaBundle:InfoPersonaEmpFormaPago')->findDatosPersonaEmpFormaPago($id_persona,$id_empresa);
        return $resultado;
    }
    
    /**     
     * Crea un actualizar Pre-cliente
     * @param InfoPersona &$entity (by reference)
     * @param string $codEmpresa
     * @param integer $idOficina
     * @param string $usrCreacion
     * @param string $clientIp
     * @param array $datos_form     
     * @param array $formas_contacto nullable, si es null se obtiene de $datos_form
     * @param string $prefijoEmpresa
     * 
     * Descripcion: Se agrega metodo en service encargado de validar las formas de contactos ingresadas
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>          
     * @version 1.1 01-09-2015 
     * 
     * Descripcion: Se agrega Validacion de Campo Fecha de Nacimiento, campo obligatorio y solo se acepta
     * personas mayores de edad (Mayores de 18 anios)
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>          
     * @version 1.2 03-09-2015 
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.3 08-11-2016
     * Se envia array de Parametros $arrayParamFormasContac a la funcion "validarFormasContactos" se agrega strOpcionPermitida y strPrefijoEmpresa, 
     * Prefijo de empresa en sesion para validar que para empresa MD no se obligue el ingreso de al menos 1 correo.
     *  
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.4 03-07/2017
     * Se agregan las variables strNombrePais e intIdPais para determinar qué país se valida en la forma de contactos.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.5 10-05-2021 Se modifican los parámetros enviados a la función liberarInterfaceSplitter
     *  
     * @throws Exception
     */    
    public function ActualizarPreCliente(InfoPersona &$entity, 
                                         $codEmpresa, 
                                         $idOficina, 
                                         $usrUltMod, 
                                         $clientIp, 
                                         array $datos_form, 
                                         $prefijoEmpresa, 
                                         array $formas_contacto = NULL)
    {
        $strNombrePais  = $datos_form['strNombrePais'];
        $intIdPais      = $datos_form['intIdPais'];
        $estadoI = 'Inactivo';
        if(empty($formas_contacto))
        {
            // si no se ha especificado formas de contacto, obtenerlas de $datos_form
            if(is_array($datos_form['formas_contacto']))
            {
                $formas_contacto = $datos_form['formas_contacto'];
            }
            else
            {
                $array_formas_contacto = explode(',', $datos_form['formas_contacto']);
                $formas_contacto = array();
                for($i = 0; $i < count($array_formas_contacto); $i+=3)
                {
                    $formas_contacto[] = array(
                        'formaContacto' => $array_formas_contacto[$i + 1],
                        'valor' => $array_formas_contacto[$i + 2]
                    );
                }
            }
        }

        $personaEmpresaRol = $this->emcom->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                  ->getPersonaEmpresaRolPorPersonaPorTipoRol($entity->getId(), 'Pre-cliente', $codEmpresa);

        $strError          = '';
        $this->emcom->getConnection()->beginTransaction();
        try
        {

            if(!$datos_form ['tipoIdentificacion'])
            {
                throw new \Exception('El tipo de Identificacion es un campo obligatorio - No se pudo Actualizar el Prospecto'); 
            }
            $entity->setTipoIdentificacion($datos_form['tipoIdentificacion']);
            $entity->setIdentificacionCliente($datos_form['identificacionCliente']);
            $entity->setTipoEmpresa($datos_form['tipoEmpresa']);
            $entity->setTipoTributario($datos_form['tipoTributario']);
            //cambios DINARDARP - se agrega campo origenes de ingresos
            $entity->setOrigenIngresos($datos_form['origenIngresos']);
            if($datos_form['tipoEmpresa'])
            {
                $entity->setRazonSocial($datos_form['razonSocial']);
            }
            else
            {
                if(!$datos_form['fechaNacimiento'] 
                    || (!$datos_form ['fechaNacimiento'] ['year'] 
                        && !$datos_form ['fechaNacimiento'] ['month'] 
                        && !$datos_form ['fechaNacimiento'] ['day']))
                {
                    if($prefijoEmpresa == 'MD' || $prefijoEmpresa == 'EN')
                    {
                        throw new \Exception('La Fecha de Nacimiento es un campo obligatorio - No se pudo guardar el Prospecto'); 
                    }
                }                                                
                else
                {
                    if(is_array($datos_form ['fechaNacimiento']) &&
                        ($datos_form ['fechaNacimiento'] ['year'] && 
                        $datos_form ['fechaNacimiento'] ['month'] && 
                        $datos_form ['fechaNacimiento'] ['day']))
                    {
                         $intEdad = $this->devuelveEdadPorFecha($datos_form ['fechaNacimiento'] ['year'] .
                                    '-' . $datos_form ['fechaNacimiento'] ['month'] .
                                    '-' . $datos_form ['fechaNacimiento'] ['day']);       
                         if($intEdad<18)
                         {
                             throw new \Exception('La Fecha de Nacimiento ingresada corresponde a un menor de edad - '
                                 . 'No se pudo guardar el Prospecto : '.$datos_form ['fechaNacimiento'] ['year'] .
                                    '-' . $datos_form ['fechaNacimiento'] ['month'] .
                                    '-' . $datos_form ['fechaNacimiento'] ['day']); 
                         }
                    }
                }
                
                $entity->setNombres($datos_form['nombres']);
                $entity->setApellidos($datos_form['apellidos']);
                $entityAdmiTitulo = $this->emcom->getRepository('schemaBundle:AdmiTitulo')->find($datos_form['tituloId']);
                if($entityAdmiTitulo)
                    $entity->setTituloId($entityAdmiTitulo);
                $entity->setGenero($datos_form['genero']);
                $entity->setEstadoCivil($datos_form['estadoCivil']);

                if($datos_form['fechaNacimiento']['year'] && $datos_form['fechaNacimiento']['month'] && $datos_form['fechaNacimiento']['day'])
                    $entity->setFechaNacimiento(date_create($datos_form['fechaNacimiento']['year'] . '-' . 
                                                            $datos_form['fechaNacimiento']['month'] . '-' . 
                                                            $datos_form['fechaNacimiento']['day']));
            }
            $entity->setRepresentanteLegal($datos_form['representanteLegal']);
            $entity->setNacionalidad($datos_form['nacionalidad']);
            $entity->setDireccion($datos_form['direccionTributaria']);
            $entity->setDireccionTributaria($datos_form['direccionTributaria']);
            if($prefijoEmpresa == 'TN')
            {             
                $entity->setContribuyenteEspecial($datos_form ['contribuyenteEspecial']);
                $entity->setPagaIva($datos_form ['pagaIva']);     
                $entity->setNumeroConadis($datos_form ['numeroConadis']); 
            }
            else
            {
                $entity->setPagaIva('S');      
            }
            $this->emcom->persist($entity);
            //$this->emcom->flush();

            if($datos_form['idperreferido'])
            {
                //INACTIVA REFERIDO ANTERIOR
                $entityPersonaRef = $this->emcom->getRepository('schemaBundle:InfoPersonaReferido')
                                         ->findByPersonaEmpresaRolId($personaEmpresaRol->getId());
                foreach($entityPersonaRef as $referidoobj)
                {
                    $referidoobj->setEstado('Inactivo');
                    $this->emcom->persist($referidoobj);
                    // $this->emcom->flush();                    
                }
                //GRABA RELACION ENTRE REFERIDO Y PRE-CLIENTE                
                $entityPersonaRefNew = new InfoPersonaReferido();
                $entityReferido = $this->emcom->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($datos_form['idperreferido']);
                $entityPersonaRefNew->setPersonaEmpresaRolId($personaEmpresaRol);
                $entityPersonaRefNew->setRefPersonaEmpresaRolId($entityReferido);
                $entityPersonaRefNew->setReferidoId($entityReferido->getPersonaId());
                $entityPersonaRefNew->setFeCreacion(new \DateTime('now'));
                $entityPersonaRefNew->setUsrCreacion($usrUltMod);
                $entityPersonaRefNew->setIpCreacion($clientIp);
                $entityPersonaRefNew->setEstado('Activo');
                $this->emcom->persist($entityPersonaRefNew);
                //$this->emcom->flush();     
            }

            // GUARDA LA FORMA DE PAGO POR PERSONA_EMPRESA_ROL_ID
            if($prefijoEmpresa == 'MD' || $prefijoEmpresa == 'EN' )
            {
                $entityPersonaEmpFormaPago = $this->emcom->getRepository('schemaBundle:InfoPersonaEmpFormaPago')
                                                  ->findByPersonaEmpresaRolId($personaEmpresaRol->getId());
                if($entityPersonaEmpFormaPago)
                {
                    foreach($entityPersonaEmpFormaPago as $entityPersonaEmpFormaPago)
                    {
                        $entityPersonaEmpFormaPago->setEstado('Inactivo');
                        $entityPersonaEmpFormaPago->setUsrUltMod($usrUltMod);
                        $entityPersonaEmpFormaPago->setFeUltMod(new \DateTime('now'));
                        $this->emcom->persist($entityPersonaEmpFormaPago);                        
                    }
                }

                $entityFormaPago = $this->emcom->getRepository('schemaBundle:AdmiFormaPago')->find($datos_form['formaPagoId']);
                $entityAdmiTipoCuenta = null;
                $entityAdmiBancoTipoCuenta = null;
                if(!empty($datos_form['tipoCuentaId']))
                {
                    $entityAdmiTipoCuenta = $this->emcom->getRepository('schemaBundle:AdmiTipoCuenta')->find($datos_form['tipoCuentaId']);
                }
                if(!empty($datos_form['bancoTipoCuentaId']))
                {
                    $entityAdmiBancoTipoCuenta = $this->emcom->getRepository('schemaBundle:AdmiBancoTipoCuenta')
                                                      ->find($datos_form['bancoTipoCuentaId']);
                }
                $entityPersonaEmpFormaPago = new InfoPersonaEmpFormaPago();
                $entityPersonaEmpFormaPago->setFormaPagoId($entityFormaPago);
                $entityPersonaEmpFormaPago->setTipoCuentaId($entityAdmiTipoCuenta);
                $entityPersonaEmpFormaPago->setBancoTipoCuentaId($entityAdmiBancoTipoCuenta);
                $entityPersonaEmpFormaPago->setEstado('Activo');
                $entityPersonaEmpFormaPago->setUsrCreacion($usrUltMod);
                $entityPersonaEmpFormaPago->setFeCreacion(new \DateTime('now'));
                $entityPersonaEmpFormaPago->setIpCreacion($clientIp);
                $entityPersonaEmpFormaPago->setPersonaEmpresaRolId($personaEmpresaRol);
                $this->emcom->persist($entityPersonaEmpFormaPago);

                /* Eliminamos los servicios (Pre-servicio, Factible, PreFactibilidad, Pendiente) 
                   que no esten acorde a la nueva forma de pago que edito */
                $listado_servicios_incumplidos = $this->emcom->getRepository('schemaBundle:InfoServicio')
                                                      ->findByPlanCondicionIncumplida($personaEmpresaRol, 
                                                                                      $entityFormaPago, 
                                                                                      $entityAdmiTipoCuenta, 
                                                                                      $entityAdmiBancoTipoCuenta);
                if(!empty($listado_servicios_incumplidos))
                {
                    foreach($listado_servicios_incumplidos as $entityInfoServicio)
                    {
                        $entityInfoServicio->setEstado('Eliminado');
                        $this->emcom->persist($entityInfoServicio);
                        //Genero historial por cada servicio
                        $entityServicioHist = new InfoServicioHistorial();
                        $entityServicioHist->setServicioId($entityInfoServicio);
                        $entityServicioHist->setObservacion('Se elimino el servicio del Prospecto por '.
                                                            'incumplir condicion en el cambio de Forma de pago');
                        $entityServicioHist->setIpCreacion($clientIp);
                        $entityServicioHist->setFeCreacion(new \DateTime('now'));
                        $entityServicioHist->setUsrCreacion($usrUltMod);
                        $entityServicioHist->setEstado($entityInfoServicio->getEstado());
                        $this->emcom->persist($entityServicioHist);
                        $this->emcom->flush();
                        //SE AGREGA CODIGO PARA REALIZAR LIBERACION DE RECURSOS DE RED
                        /* @var $serviceInterfaceElemento InfoInterfaceElementoService */
                        $arrayRespuestaLiberaSplitter   = $this->serviceInfoInterfaceElemento
                                                               ->liberarInterfaceSplitter(array("objServicio"           => $entityInfoServicio,
                                                                                                "strUsrCreacion"        => $usrUltMod,
                                                                                                "strIpCreacion"         => $clientIp,
                                                                                                "strProcesoLibera"      => " por actualización de "
                                                                                                . "Pre-cliente",
                                                                                                "strVerificaLiberacion" => "SI",
                                                                                                "strPrefijoEmpresa"     => $prefijoEmpresa));
                        $strStatusLiberaSplitter        = $arrayRespuestaLiberaSplitter["status"];
                        $strMensajeLiberaSplitter       = $arrayRespuestaLiberaSplitter["mensaje"];
                        if($strStatusLiberaSplitter === "ERROR")
                        {
                            if($this->emcom->getConnection()->isTransactionActive())
                            {
                                $this->emcom->getConnection()->rollBack();
                            }
                            $this->emcom->getConnection()->close();
                            throw new Exception($strMensajeLiberaSplitter);
                        }
                    }
                }
            }

            //Obtiene el historial del prospecto(pre-cliente)
            $ultimoEstado = $this->emcom->getRepository('schemaBundle:InfoPersonaEmpresaRolHisto')
                                 ->findUltimoEstadoPorPersonaEmpresaRol($personaEmpresaRol->getId());
            $idUltimoEstado = $ultimoEstado[0]['ultimo'];
            $entityUltimoEstado = $this->emcom->getRepository('schemaBundle:InfoPersonaEmpresaRolHisto')->find($idUltimoEstado);
            $estado = $entityUltimoEstado->getEstado();

            $entity_persona_historial = new InfoPersonaEmpresaRolHisto();
            $entity_persona_historial->setEstado($estado);
            $entity_persona_historial->setFeCreacion(new \DateTime('now'));
            $entity_persona_historial->setIpCreacion($clientIp);
            $entity_persona_historial->setPersonaEmpresaRolId($personaEmpresaRol);
            $entity_persona_historial->setUsrCreacion($usrUltMod);
            $this->emcom->persist($entity_persona_historial);
            
            /* Se envia array de Parametros y se agrega strOpcionPermitida y strPrefijoEmpresa, Prefijo de empresa en sesion para validar
             * que para empresa MD no se obligue el ingreso de al menos 1 correo */
            $arrayParamFormasContac                        = array ();
            $arrayParamFormasContac['strPrefijoEmpresa']   = $prefijoEmpresa;
            $arrayParamFormasContac['arrayFormasContacto'] = $formas_contacto;
            $arrayParamFormasContac['strOpcionPermitida']  = 'NO';
            $arrayParamFormasContac['strNombrePais']       = $strNombrePais;
            $arrayParamFormasContac['intIdPais']           = $intIdPais;
            $arrayValidaciones   = $this->serviceInfoPersonaFormaContacto->validarFormasContactos($arrayParamFormasContac);
            if($arrayValidaciones)
            {    
                foreach($arrayValidaciones as $key => $mensaje_validaciones)
                {
                    foreach($mensaje_validaciones as $key_msj => $value)
                    {                      
                        $strError = $strError.$value.".\n";                        
                    }
                }
                throw new \Exception("No se pudo editar el Prospecto - " . $strError);
            } 
            //PONE ESTADO INACTIVO A TODOS LAS FORMAS DE CONTACTO DE LA PERSONA QUE tengan estado ACTIVO
            $this->serviceInfoPersonaFormaContacto->inactivarPersonaFormaContactoActivasPorPersona($entity->getId(), $usrUltMod);

            //ReGISTRA LAS FORMAS DE CONTACTO DEL CLIENTE
            for($i = 0; $i < count($formas_contacto); $i++)
            {
                $entity_persona_forma_contacto = new InfoPersonaFormaContacto();
                $entity_persona_forma_contacto->setValor($formas_contacto[$i]["valor"]);
                $entity_persona_forma_contacto->setEstado("Activo");
                $entity_persona_forma_contacto->setFeCreacion(new \DateTime('now'));
                $entityAdmiFormaContacto = $this->emcom->getRepository('schemaBundle:AdmiFormaContacto')
                                                ->findPorDescripcionFormaContacto($formas_contacto[$i]["formaContacto"]);
                $entity_persona_forma_contacto->setFormaContactoId($entityAdmiFormaContacto);
                $entity_persona_forma_contacto->setIpCreacion($clientIp);
                $entity_persona_forma_contacto->setPersonaId($entity);
                $entity_persona_forma_contacto->setUsrCreacion($usrUltMod);
                $this->emcom->persist($entity_persona_forma_contacto);
                //$this->emcom->flush();
            }
            //Actualizo oficina de facturacion del precliente
            if($prefijoEmpresa == 'TN')
            {  
                $entityOficina = $this->emcom->getRepository('schemaBundle:InfoOficinaGrupo')->find((int)$datos_form['idOficinaFacturacion']);
                $personaEmpresaRol->setOficinaId($entityOficina);
                $personaEmpresaRol->setEsPrepago($datos_form ['esPrepago']);
            }  
            else
            {
                $personaEmpresaRol->setEsPrepago('S');  
            }
            $this->emcom->persist($personaEmpresaRol);  
            
            $this->emcom->flush();
            $this->emcom->getConnection()->commit();
            return $personaEmpresaRol;
        }
        catch(\Exception $e)
        {
            if($this->emcom->getConnection()->isTransactionActive())
            {
                $this->emcom->getConnection()->rollBack();
            }
            $this->emcom->getConnection()->close();
            throw $e;
        }
    }

     /** 
     * Descripcion: Metodo encargado de devolver edad en base a la fecha recibida
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     *
     * @param string $fechaNacimiento
     * 
     * @throws Exception
     * @version 1.0 03-09-2015 
     * @return integer
     */
    
    public function devuelveEdadPorFecha($fechaNacimiento)
    {        
        $intAnoDiferencia = 0;
        $intAno           = 0;
        $intMes           = 0;
        $intDia           = 0;            
        list($intAno, $intMes, $intDia) = explode("-", $fechaNacimiento);
        $intAnoDiferencia = date("Y") - $intAno;
        $intMesDiferencia = date("m") - $intMes;
        $intDiaDiferencia = date("d") - $intDia;       
        if(($intDiaDiferencia < 0 && $intMesDiferencia==0) || $intMesDiferencia < 0)
        {
            $intAnoDiferencia--;
        }       
        return $intAnoDiferencia;
    }
    /**
     * Documentación para la función validarIdentificacion.
     *
     * Función que realiza validaciones de la identificación ingresadas en TelcoCRM.
     *
     * @param array $arrayParametros [
     *                                  "strTipoIdentificacion" => Tipo de identificación del cliente, ingresada en la aplicación TelcoCRM.
     *                                  "strRuc"                => Ruc de la cuenta, ingresada en la aplicación TelcoCRM.
     *                                  "intIdPais"             => Id del pais.
     *                                  "aplication"            => Nombre de la aplicación.
     *                               ]
     *
     * @return array $arrayRespuesta [
     *                                  "status"  => Estado de la respuesta
     *                                  "mensaje" => Mensaje de la respuesta
     *                                  "success" => Indica el éxito de la transacción
     *                               ]
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 23-07-2019
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.1 10-01-2022 - Se agrega busqueda de codEmpresa al momento de validar la identificación
     */
    public function validarIdentificacion($arrayParametros)
    {
        $strTipoIdentificacion    = $arrayParametros['strTipoIdentificacion'] ? $arrayParametros['strTipoIdentificacion']:"";
        $strIdentificacionCliente = $arrayParametros['strRuc'] ? $arrayParametros['strRuc']:"";
        $intIdPais                = $arrayParametros['intIdPais'] ? $arrayParametros['intIdPais']:"";
        $intIdEmpresa             = $arrayParametros['strCodEmpresa'] ? $arrayParametros['strCodEmpresa']:"";
        try
        {
            $arrayParamValidaIdentifica = array(
                                                    'strTipoIdentificacion'     => $strTipoIdentificacion,
                                                    'strIdentificacionCliente'  => $strIdentificacionCliente,
                                                    'intIdPais'                 => $intIdPais,
                                                    'strCodEmpresa'             => $intIdEmpresa
                                                );
            $strMensaje = $this->emcom->getRepository('schemaBundle:InfoPersona')
                                    ->validarIdentificacionTipo($arrayParamValidaIdentifica);
            $strMensaje = ($strMensaje!='')?$strMensaje:'OK';
        }
        catch(\Exception $ex)
        {
            $strMensaje ="Fallo al validar la identificación, intente nuevamente.\n". $ex->getMessage();
        }
        return $strMensaje;
    }

     /**
     *  Documentación para el método callWebServicePrefactibilidad
     *
     * Función para llamar al Web service Prefactibilidad
     *
     * @author Andrea Cárdenas <ascardenas@telconet.ec>
     * @version 1.0 07-07-2021
     *
     * @param  Array $arrayPeticiones [ información requerida por el WS segun el tipo del método a ejecutar ]
     *
     * @return Array $arrayRespuesta  [ información que retorna el método llamado ]
     */
    public function callWebServicePrefactibilidad($arrayPeticiones)
    {
        $arrayRespuesta = $this->conexionWsPrefactibilidad($arrayPeticiones);

        return $arrayRespuesta;
    }

    /**
     * Documentación para el método 'conexionWsPrefactibilidad'
     *
     * Ee encarga de realizar la conexión contra el WS de Prefactibilidad
     *
     * @author Andrea Cárdenas <ascardenas@telconet.ec>
     * @version 1.0 07-07-2021
     *
     * @param  Array $arrayParametros [ información requerida por el WS segun el tipo del método a ejecutar ]
     *
     * @return Array $arrayResultado [ información que retorna el método llamado ]
     */
    public function conexionWsPrefactibilidad($arrayParametros)
    {
        $arrayResultado = array();
        $strDataString = $this->generateJsonWsPrefactibilidad($arrayParametros);
        $strUrl        = $this->strUrlMsGestionaPrefactibilidad;

        
            //Se obtiene el resultado de la ejecucion via rest hacia el ws
            $arrayOptions = array(  CURLOPT_SSL_VERIFYPEER => false,
                                    CURLOPT_HTTPHEADER     => array(
                                        'Content-Type: application/json',
                                        'tokenCas: ' . $arrayParametros['token'])
                                );

            $arrayResponseJson = $this->restClient->postJSON($strUrl, $strDataString , $arrayOptions);
            $arrayResponse     = json_decode($arrayResponseJson['result'],true);

            if($arrayResponse['status'] == "OK")
            {
                $arrayResultado['data']       = $arrayResponse['data'];
                $arrayResultado['message']    = $arrayResponse['message'];
                $arrayResultado['status']     = $arrayResponse['status'];
                $arrayResultado['statusCode'] = $arrayResponse['code'];
            }
            else
            {
                $arrayResultado['status']     = "ERROR";
                $arrayResultado['statusCode'] = 500;
                $arrayResultado['data']       = "";

               
                if(isset($arrayResponse['message']) && !empty($arrayResponse['message']))
                {
                    $strMensajeError = $arrayResponse['message'];
                    $arrayResultado['mensaje'] = "Error en el WS del MicroServicio : ".$strMensajeError;
                }else 
                {
                    $arrayResultado['mensaje'] = "No Existe Conectividad con el WS MircroServicio.";
                }

                  
           
            }

        return $arrayResultado;
    }

    /**
     * Documentación para el método 'generateJsonWsPrefactibilidad'
     *
     * Función encargada de generar el array a enviar para el consumo del Web Service Rest del 
     * microServicio  Prefactibilidad
     *
     * @author Andrea Cárdenas <ascardenas@telconet.ec>
     * @version 1.0 07-07-2021
     *
     * @param  Array $arrayParametros [ informacion requerida por el WS segun el tipo del metodo a ejecutar ]
     * 
     */
    public function generateJsonWsPrefactibilidad($arrayParametros)
    {
        $strCodEmpresa        = $arrayParametros['strCodEmpresa'];
        $strUsrCreacion       = $arrayParametros['strUsrCreacion'];
        $strPrefijoEmpresa    = $arrayParametros['strPrefijoEmpresa'];
        $arrayDatosForm       = $arrayParametros['arrayDatosForm'];
        $arrayFormasContacto  = array(); 

        $arrayFormContacto = explode(',', $arrayDatosForm['formas_contacto']);
        $this->emcom->getConnection()->beginTransaction();

        try
        {
            for($intContador = 0; $intContador < count($arrayFormContacto); $intContador+=2)
            {
                
                $entityAdmiFormaContacto = $this->emcom->getRepository('schemaBundle:AdmiFormaContacto')
                                                           ->findPorDescripcionFormaContacto($arrayFormContacto[$intContador]);
                
                $arrayFormasContacto[]     = array(
                    'idFormaContacto'   => $entityAdmiFormaContacto->getId(),
                    'valorFormaContacto'=> $arrayFormContacto[$intContador + 1]
                );
            }

            $objJsonArray = array
            (   
                'usrCreacion'               => $strUsrCreacion,
                'ipCreacion'                => $arrayParametros ['strClientIp'],
                'canal'                     => $arrayParametros ['strCanal'],
                'esOrigenWeb'               => $arrayParametros ['strOrigenWeb'], 
                'tipoIdentificacionPersona' => $arrayDatosForm['tipoIdentificacion'],
                'identificacionPersona'     => $arrayDatosForm['identificacionCliente'],
                'nombresPersona'            => $arrayDatosForm['nombres'],
                'apellidosPersona'          => $arrayDatosForm['apellidos'],
                'razonSocialPersona'        => $arrayDatosForm['razonSocial'],
                'infoFormasContactoPersona' => $arrayFormasContacto,
                'idPuntoCobertura'          => $arrayDatosForm['ptoCoberturaId'],
                'idCanton'                  => $arrayDatosForm['cantonId'],
                'idParroquia'               => $arrayDatosForm['parroquia'],
                'idSector'                  => $arrayDatosForm['sectorId'],
                'longitud'                  => $arrayDatosForm['longitud'],
                'latitud'                   => $arrayDatosForm['latitud'],
                'dependeDeEdificio'         => $arrayDatosForm['dep_edificio'],
                'idEmpresa'                 => $strCodEmpresa,
                'prefijoEmpresa'            => $strPrefijoEmpresa
            );


            if($this->emcom->getConnection()->isTransactionActive())
            {
                $strMensaje ="Formas de Contacto agregadas con exito";
                $this->emcom->getConnection()->close();
            }

        }
        catch(\Exception $ex)
        {
            
            if($this->emcom->getConnection()->isTransactionActive())
            {
                $this->emcom->getConnection()->close();
            }
           
            $strMensaje ="Fallo al registrar Formas de Contacto de cliente prefactible.\n". $ex->getMessage();
            $strStatus  = 500;
            error_log('Error en PreClienteService->generateJsonWsPrefactibilidad() => '.$strMensaje);
            $this->utilServicio->insertError( 'Telcos+',
                                              'Error en PreClienteService->generateJsonWsPrefactibilidad() => ',
                                              $strMensaje,
                                              $strUser,
                                              $strIp
                                            );
        }

        return json_encode($objJsonArray);
    }

    /**
     * guardarFormasContactoProspecto, guarda las formas de contacto del prospecto consumiendo el ms de credenciales.
     * 
     * @author  Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 
     * 
     * @return array
     * 
     */
    public function guardarFormasContactoProspecto($arrayParametros) 
    {    
        try
        {
            $objOptions         = array(CURLOPT_SSL_VERIFYPEER => false,
                                        CURLOPT_HTTPHEADER     => array('Content-Type: application/json',
                                                                        'tokencas: '.$arrayParametros['token'])
                                       ); 
            $strJsonData        = json_encode($arrayParametros['data']);

            $arrayResponseJson  = $this->restClient->postJSON($this->strUrlFormasContactoProspectoGuardarMs, $strJsonData , $objOptions);
            $strJsonRespuesta   = json_decode($arrayResponseJson['result'],true);

            if(isset($strJsonRespuesta['code']) && $strJsonRespuesta['code']==0 
            && isset($strJsonRespuesta['status'])
            && isset($strJsonRespuesta['message']) )
            {   
                $arrayResponse = array(
                                       'status' => $strJsonRespuesta['code'],
                                       'message'=> $strJsonRespuesta['message']);
                $arrayResultado = $arrayResponse;
            }
            else
            {
                $arrayResultado['status']      = "ERROR";
                if(empty($strJsonRespuesta['message']))
                {
                    $arrayResultado['message']  = "No Existe Conectividad con el WS MS COMP CREDENCIALES COMERCIAL.";
                }
                else
                {
                    $arrayResultado['message']  = $strJsonRespuesta['message'];
                }
            }

            return $arrayResultado;
        }
        catch(\Exception $e)
        {
            $strRespuesta   = "Error al ejecutar el re procesamiento del registro. Favor Notificar a Sistemas".$e->getMessage();
            $arrayResultado = array ('message'     =>$strRespuesta);
            $this->serviceUtil->insertError('Telcos+',
                                            'PreClienteService.guardarFormasContactoProspecto',
                                            'Error PreClienteService.guardarFormasContactoProspecto:'.$e->getMessage(),
                                            'epin',
                                            '127.0.0.1'); 
            return $arrayResultado;
        }
        
    }
}

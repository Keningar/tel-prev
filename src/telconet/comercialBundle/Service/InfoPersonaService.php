<?php

namespace telconet\comercialBundle\Service;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Acl\Exception\Exception;
use Symfony\Component\Validator\Constraints\True;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolHisto;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRol;
use telconet\schemaBundle\Entity\InfoOficinaGrupo;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolCarac;

class InfoPersonaService {
    const CARACTERISTICA_URL_BASE_HALL    = 'URL_BASE_HALL';
    const CARACTERISTICA_CABECERA_PLANIF  = 'ID_CABECERA_PLANIF';
    const CARACTERISTICA_GENERALES_MOVIL  = 'PARAMETROS_GENERALES_MOVIL';
    private $serviceSoporte;
     /**
     *
     * @var \telconet\schemaBundle\Service\RestClientService
     */
    private $serviceRestClient;

    /**
     * @var \Doctrine\ORM\EntityManager
     */

    /**
     * service $serviceUtil
     */
    private $serviceUtil;
    private $emcom;
    private $emInfraestructura;
    private $emSeguridad;
    private $session;
    private $emgen;
    private $objContainer;

    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $container) {
        $this->emcom                = $container->get('doctrine.orm.telconet_entity_manager');
        $this->emInfraestructura    = $container->get('doctrine.orm.telconet_infraestructura_entity_manager');
        $this->emSeguridad          = $container->get('doctrine.orm.telconet_seguridad_entity_manager');
        $this->session              = $container->get('session');
        $this->serviceRestClient    = $container->get('schema.RestClient');
        $this->serviceSoporte       = $container->get('soporte.SoporteService');
        $this->emgen                = $container->get('doctrine')->getManager('telconet_general');
        $this->serviceUtil          = $container->get('schema.Util');
        $this->objContainer         = $container;
    }
    
    /**
     * Determina la validez de una identificacion segun su tipo
     * @param string $strTipoIdentificacion
     * @param string $strIdentificacionCliente
     * @return string mensaje de error, null en caso contrario
     *
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.1 21-09-2017 - Se obtiene de la sesión el idPais. En caso que no exista sesión, por defecto se asigna el idPais de Ecuador.
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.2 10-01-2022 - Se agrega busqueda de codEmpresa al momento de validar la identificación
     */
    public function validarIdentificacionTipo($arrayParam)
    {
        //POR DEFECTO EL idPais de Ecuador
        $intIdPais = 1;
        if ($this->session)
        {
            $intIdPais      = $this->session->get('intIdPais');
            $intIdEmpresa   = $this->session->get('idEmpresa');
        }
        $arrayParamValidaIdentifica = array(
                                                'strTipoIdentificacion'     => $arrayParam['strTipoIdentificacion'],
                                                'strIdentificacionCliente'  => $arrayParam['strIdentificacionCliente'],
                                                'intIdPais'                 => $intIdPais,
                                                'strCodEmpresa'             => $intIdEmpresa
                                            );
        return $this->emcom->getRepository('schemaBundle:InfoPersona')
                        ->validarIdentificacionTipo($arrayParamValidaIdentifica);
    }
    

    /**
     * Edita nombre , apellido o razon social de la persona    
     * @version 1.0
     * @since 25/08/2014
     * 
     * @author : Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.1 15-07-2016 
     * Se agrega a la opcion de edicion de nombre o Razon Social la edicion de Representante Legal
     * Tipo Tributario, y Tipo Empresa, se envia en arreglo los parametros para la actualizacion de la informacion.
     * 
     * @author : Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.2 08-08-2016 
     * Se agrega generacion del Historico en la opcion de edicion, se aumenta edicion de Oficina de Facturacion
     * Se envia en arreglo de parametros el id_persona_rol, usuario_creacion, ip_creacion
     * @param array $arrayParametros    - intIdPersona
     *                                  - intIdPersonaRol
     *                                  - intIdOficina
     *                                  - strTipoEmpresa
     *                                  - strNombres            
     *                                  - strApellidos
     *                                  - strRazonSocial
     *                                  - strRepresentanteLegal
     *                                  - strTipoEmpresaNuevo
     *                                  - strTipoTributarioNuevo        
     *                                  - strUsrCreacion
     *                                  - strIpCreacion 
     *  
     * @return array (success=>[bolean],msg=>[mensaje del resultado])
     */
    public function editaNombrePersona($arrayParametros)       
    {   $objRequest = $this->objContainer->get('request');
        $objSession = $objRequest->getSession();
        $strIdEmpresa          = $objSession->get('idEmpresa');
        $strPrefijoEmpresa     = $objSession->get('prefijoEmpresa');
        $strNombresCompletos   = $objSession->get('empleado');
        $strIdPersonaEmpresaRol= $objSession->get('idPersonaEmpresaRol');
        

        $this->emcom->getConnection()->beginTransaction();
        try
        {

            $msg = "Se edito nombre persona Correctamente";
            $success = true;
            
            $objInfoPersona   = $this->emcom->getRepository('schemaBundle:InfoPersona')->find($arrayParametros['intIdPersona']);
            $objPersonaEmpRol = $this->emcom->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($arrayParametros['intIdPersonaRol']);
            
            $objMotivo        = $this->emcom->getRepository('schemaBundle:AdmiMotivo')
                                     ->findOneBy(array('nombreMotivo' => 'CAMBIO DATOS FACTURACION'));
            
            if(!$objMotivo)
            {
                throw new \Exception("No encontro motivo de Edición de Nombre o Razón Social");
            }
            $strNombreOficina = "";
            if($objPersonaEmpRol && $objPersonaEmpRol->getOficinaId())
            {
                $objInfoOficinaGrupo = $this->emcom->getRepository('schemaBundle:InfoOficinaGrupo')
                                            ->find($objPersonaEmpRol->getOficinaId()->getId());
                if($objInfoOficinaGrupo)
                {
                    $strNombreOficina = $objInfoOficinaGrupo->getNombreOficina();
                }               
            }
            $objOficinaNueva= $this->emcom->getRepository('schemaBundle:InfoOficinaGrupo')->find($arrayParametros['intIdOficina']);
            if(!$objOficinaNueva)
            {
                throw new \Exception("No encontro Oficina de Facturación a actualizar");
            }
            
            if($objInfoPersona && $objPersonaEmpRol)
            {                
                if($arrayParametros['strTipoEmpresa']!="" && $arrayParametros['strTipoEmpresa']!=null)
                {
                    $strClienteAnterior = $objInfoPersona->getRazonSocial();
                    $strClienteNuevo = $arrayParametros['strRazonSocial'];
                    if($objInfoPersona->getRazonSocial() != $arrayParametros['strRazonSocial'])
                    {
                        // Guardo Historial de informacion editada
                        $objInfoPersonaEmpresaRolHisto = new InfoPersonaEmpresaRolHisto();
                        $objInfoPersonaEmpresaRolHisto->setEstado($objPersonaEmpRol->getEstado());
                        $objInfoPersonaEmpresaRolHisto->setFeCreacion(new \DateTime('now'));
                        $objInfoPersonaEmpresaRolHisto->setUsrCreacion($arrayParametros['strUsrCreacion']);
                        $objInfoPersonaEmpresaRolHisto->setIpCreacion($arrayParametros['strIpCreacion']);
                        $objInfoPersonaEmpresaRolHisto->setPersonaEmpresaRolId($objPersonaEmpRol);
                        $objInfoPersonaEmpresaRolHisto->setMotivoId($objMotivo->getId());
                        $objInfoPersonaEmpresaRolHisto->setObservacion("Razón Social anterior: " . $objInfoPersona->getRazonSocial());
                        $this->emcom->persist($objInfoPersonaEmpresaRolHisto);
                        
                        $objInfoPersona->setRazonSocial($arrayParametros['strRazonSocial']);                    
                    }
                    if($objInfoPersona->getRepresentanteLegal() != $arrayParametros['strRepresentanteLegal'])
                    {
                        // Guardo Historial de informacion editada
                        $objInfoPersonaEmpresaRolHisto = new InfoPersonaEmpresaRolHisto();
                        $objInfoPersonaEmpresaRolHisto->setEstado($objPersonaEmpRol->getEstado());
                        $objInfoPersonaEmpresaRolHisto->setFeCreacion(new \DateTime('now'));
                        $objInfoPersonaEmpresaRolHisto->setUsrCreacion($arrayParametros['strUsrCreacion']);
                        $objInfoPersonaEmpresaRolHisto->setIpCreacion($arrayParametros['strIpCreacion']);
                        $objInfoPersonaEmpresaRolHisto->setPersonaEmpresaRolId($objPersonaEmpRol);
                        $objInfoPersonaEmpresaRolHisto->setMotivoId($objMotivo->getId());
                        $objInfoPersonaEmpresaRolHisto->setObservacion("Representante Legal anterior: ".$objInfoPersona->getRepresentanteLegal());
                        $this->emcom->persist($objInfoPersonaEmpresaRolHisto);
                        
                        $objInfoPersona->setRepresentanteLegal($arrayParametros['strRepresentanteLegal']);  
                    }
                    if($objInfoPersona->getTipoEmpresa() != $arrayParametros['strTipoEmpresaNuevo'])
                    {
                        // Guardo Historial de informacion editada
                        $objInfoPersonaEmpresaRolHisto = new InfoPersonaEmpresaRolHisto();
                        $objInfoPersonaEmpresaRolHisto->setEstado($objPersonaEmpRol->getEstado());
                        $objInfoPersonaEmpresaRolHisto->setFeCreacion(new \DateTime('now'));
                        $objInfoPersonaEmpresaRolHisto->setUsrCreacion($arrayParametros['strUsrCreacion']);
                        $objInfoPersonaEmpresaRolHisto->setIpCreacion($arrayParametros['strIpCreacion']);
                        $objInfoPersonaEmpresaRolHisto->setPersonaEmpresaRolId($objPersonaEmpRol);
                        $objInfoPersonaEmpresaRolHisto->setMotivoId($objMotivo->getId());
                        $objInfoPersonaEmpresaRolHisto->setObservacion("Tipo de Empresa anterior: ".$objInfoPersona->getTipoEmpresa());
                        $this->emcom->persist($objInfoPersonaEmpresaRolHisto);
                        
                        $objInfoPersona->setTipoEmpresa($arrayParametros['strTipoEmpresaNuevo']);  
                    }                                                                                                                       
                }    
                else
                {  
                    $strClienteAnterior = $objInfoPersona->getNombres(). 
                    " ".$objInfoPersona->getApellidos();
                    $strClienteNuevo = $arrayParametros['strNombres']. 
                    " ".$arrayParametros['strApellidos'];
                                    
                    if($objInfoPersona->getNombres() != $arrayParametros['strNombres'] || 
                       $objInfoPersona->getApellidos() != $arrayParametros['strApellidos'])
                    {
                     // Guardo Historial de informacion editada
                        $objInfoPersonaEmpresaRolHisto = new InfoPersonaEmpresaRolHisto();
                        $objInfoPersonaEmpresaRolHisto->setEstado($objPersonaEmpRol->getEstado());
                        $objInfoPersonaEmpresaRolHisto->setFeCreacion(new \DateTime('now'));
                        $objInfoPersonaEmpresaRolHisto->setUsrCreacion($arrayParametros['strUsrCreacion']);
                        $objInfoPersonaEmpresaRolHisto->setIpCreacion($arrayParametros['strIpCreacion']);
                        $objInfoPersonaEmpresaRolHisto->setPersonaEmpresaRolId($objPersonaEmpRol);
                        $objInfoPersonaEmpresaRolHisto->setMotivoId($objMotivo->getId());
                        $objInfoPersonaEmpresaRolHisto->setObservacion("(Nombres) anterior: ".$objInfoPersona->getNombres().
                                                                       " - (Apellidos) anterior: ".$objInfoPersona->getApellidos());
                        $this->emcom->persist($objInfoPersonaEmpresaRolHisto);
                        
                        $objInfoPersona->setNombres($arrayParametros['strNombres']);
                        $objInfoPersona->setApellidos($arrayParametros['strApellidos']);
                    }                                           
                }
                
                if($objInfoPersona->getTipoTributario() != $arrayParametros['strTipoTributarioNuevo'])
                {
                    // Guardo Historial de informacion editada
                    $objInfoPersonaEmpresaRolHisto = new InfoPersonaEmpresaRolHisto();
                    $objInfoPersonaEmpresaRolHisto->setEstado($objPersonaEmpRol->getEstado());
                    $objInfoPersonaEmpresaRolHisto->setFeCreacion(new \DateTime('now'));
                    $objInfoPersonaEmpresaRolHisto->setUsrCreacion($arrayParametros['strUsrCreacion']);
                    $objInfoPersonaEmpresaRolHisto->setIpCreacion($arrayParametros['strIpCreacion']);
                    $objInfoPersonaEmpresaRolHisto->setPersonaEmpresaRolId($objPersonaEmpRol);
                    $objInfoPersonaEmpresaRolHisto->setMotivoId($objMotivo->getId());
                    $objInfoPersonaEmpresaRolHisto->setObservacion("Tipo Tributario anterior: " . $objInfoPersona->getTipoTributario());
                    $this->emcom->persist($objInfoPersonaEmpresaRolHisto);

                    $objInfoPersona->setTipoTributario($arrayParametros['strTipoTributarioNuevo']);
                }
                
                if($strNombreOficina != $objOficinaNueva->getNombreOficina())
                {
                    // Guardo Historial de informacion editada
                    $objInfoPersonaEmpresaRolHisto = new InfoPersonaEmpresaRolHisto();
                    $objInfoPersonaEmpresaRolHisto->setEstado($objPersonaEmpRol->getEstado());
                    $objInfoPersonaEmpresaRolHisto->setFeCreacion(new \DateTime('now'));
                    $objInfoPersonaEmpresaRolHisto->setUsrCreacion($arrayParametros['strUsrCreacion']);
                    $objInfoPersonaEmpresaRolHisto->setIpCreacion($arrayParametros['strIpCreacion']);
                    $objInfoPersonaEmpresaRolHisto->setPersonaEmpresaRolId($objPersonaEmpRol);
                    $objInfoPersonaEmpresaRolHisto->setMotivoId($objMotivo->getId());
                    $objInfoPersonaEmpresaRolHisto->setObservacion("Oficina Facturación anterior: " . $strNombreOficina);
                    $this->emcom->persist($objInfoPersonaEmpresaRolHisto);

                    $objPersonaEmpRol->setOficinaId($objOficinaNueva);
                }

                $this->emcom->persist($objPersonaEmpRol);
                $this->emcom->persist($objInfoPersona);
            
                
                //Generar tareas automáticas SOLICITUD DE ACTUALIZACIÓN Y RECTIFICACIÓN
                if ($strPrefijoEmpresa === "MD")
                {
                    //Consulto el Rol de la Persona para permitir o no la ejecucion de tareas automaticas y bitacoras
                    
                    $objAdmiRol = $this->emcom->getRepository('schemaBundle:AdmiRol')->find($objPersonaEmpRol->getEmpresaRolId()->getRolId());
                    if(!is_object($objAdmiRol) || empty($objAdmiRol))
                    {   
                        throw new \Exception("Registros No Actualizados. No se encontro Rol de la Persona");  
                    }
                    
                    $arrayTipoPersonaPermitidos = array();
                    $strParamCabTareaAut = 'PROCESOS_DERECHOS_DEL_TITULAR';
                    $arrayParamTipoPersona  = $this->emgen->getRepository('schemaBundle:AdmiParametroDet')
                                    ->get($strParamCabTareaAut, 'COMERCIAL', '', 
                                    'TIPO PERSONA PERMITIDO PARA REGISTRAR BITACORA Y TAREA AUTOMATICA',
                                                '', '', '', '', '', $strIdEmpresa, '');
                
                    if (is_array($arrayParamTipoPersona) && !empty($arrayParamTipoPersona))
                    {
                        $arrayTipoPersonaPermitidos = $this->serviceUtil->obtenerValoresParametro($arrayParamTipoPersona);
                    }else
                    {
                        throw new \Exception('No existen datos de persona permitida para generar la tarea automatica'); 
                    }
                    
                    if (in_array($objAdmiRol->getDescripcionRol(), $arrayTipoPersonaPermitidos))
                    {  
                        //Obtiene Parametros de la tarea y el proceso de la tarea Automatica
                        $strParamCabTareaAut = 'PROCESOS_DERECHOS_DEL_TITULAR';
                        $arrayParamTareaAut  = $this->emgen->getRepository('schemaBundle:AdmiParametroDet')
                                            ->get($strParamCabTareaAut, 'COMERCIAL', '', 'TAREA_AUTOMATICA_ACTUALIZACION_Y_RECTIFICACION',
                                                    '', '', '', '', '', $strIdEmpresa, '');

                        if( empty($arrayParamTareaAut) )
                        {
                            throw new \Exception('No existen datos para generar la tarea automatica');
                        }

                        //obtiene punto del cliente
                        $arrayInfoPunto = $this->emcom->getRepository("schemaBundle:InfoPunto")
                                                ->findBy(array('personaEmpresaRolId' => $objPersonaEmpRol->getId(),
                                                            'estado' => 'Activo'));
                        
                        if(!empty($arrayInfoPunto))
                        { 
                            foreach($arrayInfoPunto as $objPunto)
                            {
                                $intPunto = $objPunto->getId();
                            }
                        }else
                        {
                            throw new \Exception('No existe punto del cliente');
                        }

                        $strObservacioTarea = "Se realizó el proceso de actualización y rectificación del titular: "
                                            .$objInfoPersona->getIdentificacionCliente().". ";
                        $strObservacioNombres = "Nombre actual: ".$strClienteAnterior.", ".
                                                "Nombre nuevo: ".$strClienteNuevo;
                        //Se crea la tarea Automatica
                        $arrayTarea = $this->serviceSoporte
                                    ->crearTareaCasoSoporte(array (
                                    "intIdPersonaEmpresaRol" => $strIdPersonaEmpresaRol,
                                    "intIdEmpresa"           => $strIdEmpresa,
                                    "strPrefijoEmpresa"      => $strPrefijoEmpresa,
                                    "strNombreTarea"         => $arrayParamTareaAut[0]['valor2'],
                                    "strNombreProceso"       => $arrayParamTareaAut[0]['valor3'],
                                    "strUserCreacion"        => $arrayParametros['strUsrCreacion'],
                                    "strIpCreacion"          => $arrayParametros['strIpCreacion'],
                                    "strObservacionTarea"    => $strObservacioTarea.$strObservacioNombres,
                                    "strUsuarioAsigna"       => $strNombresCompletos,
                                    "strTipoAsignacion"      => $arrayParamTareaAut[0]['valor6'],
                                    "strTipoTarea"           => "T",
                                    "strTareaRapida"         => "S",
                                    "boolAsignarTarea"       => true,
                                    "intPuntoId"             => $intPunto,
                                    "strFechaHoraSolicitada" => null,
                                    "strObsHistorial"        => "Tarea fue Finalizada Obs: Tarea Rapida",
                                    "strObsSeguimiento"      => "Tarea fue Finalizada Obs: Tarea Rapida",
                                    "intFormaContacto"       => 5,
                                    "strNombreClaseDocParam" => $arrayParamTareaAut[0]['valor4']));
                        
                        if ($arrayTarea['mensaje'] !== 'ok' )
                        {
                            throw new \Exception('Ocurrió un error al generar la tarea en la solicitud de ACTUALIZACION_Y_RECTIFICACION');
                        } 

                        //Registrar Bitacora de los cambios realizados en los datos del cliente.
                        
                        //Obtener DatosContactoPersona
                        $arrayContactosCliente = $this->emcom->getRepository('schemaBundle:InfoPersonaFormaContacto')
                        ->getFormasContactoParaSession($objInfoPersona->getId());              
                        
                        //Obtener DatosContactoPunto
                        $arrayContactosPunto = $this->emcom->getRepository('schemaBundle:InfoPuntoFormaContacto')
                                                                    ->getFormasContactoPunto($intPunto);
                        

                        //Obtener formas de Pago
                        $entityContrato = $this->emcom->getRepository('schemaBundle:InfoContrato')
                                          ->findOneByPersonaEmpresaRolId($objPersonaEmpRol->getId());
                
                        if( is_object($entityContrato) && $entityContrato->getEstado() == 'Activo'
                        && $entityContrato->getFormaPagoId())
                        {                          
                                $intTmpIdFormaPago = $entityContrato->getFormaPagoId();      
                                $strDescripcionFormaPago = $intTmpIdFormaPago->getDescripcionFormaPago();

                        }

                        $arrayDiscapacidad = $this->emcom->getRepository('schemaBundle:InfoPersona')
                        ->getDiscapacidadByIdPersonaRol(['intIdServicio' => null,
                                                                    'intIdPersonaRol' => $arrayParametros['intIdPersonaRol']]);
                         
                        $arrayRespuesta = $this->serviceUtil->guardarBitacora(array (
                        "strTipoIdentificacion"     => $objInfoPersona->getTipoIdentificacion(),
                        "strIdentificacion"         => $objInfoPersona->getIdentificacionCliente(),
                        "strNombres"                => $objInfoPersona->getNombres(),
                        "strApellidos"              => $objInfoPersona->getApellidos(),
                        "strGenero"                 => $objInfoPersona->getGenero(),
                        "strOrigenIngresos"         => $objInfoPersona->getOrigenIngresos(),
                        "strDiscapacidad"           => $arrayDiscapacidad[0][DISCAPACIDAD],
                        "strRepresentanteLegal"     => $objInfoPersona->getRepresentanteLegal(),
                        "arrayDatosContactoPersona" => $arrayContactosCliente,
                        "arrayDatosContactoPunto"   => $arrayContactosPunto,
                        "strFormaDePago"            => $strDescripcionFormaPago,
                        "strUsuario"                => $arrayParametros['strUsrCreacion'],
                        "strfechaHoraActualizacion" => date("Y-m-d").'T'.date("H:i:s"),
                        "strMetodo"                 => "ACTUALIZACIONYRECTIFICACION_CLIENTE"
                        ));
                        if ($arrayRespuesta['intStatus'] !== 0 )
                        {
                            throw new \Exception('Ocurrió un error al guardar la bitacora en la solicitud de ACTUALIZACION_Y_RECTIFICACION'
                                                .$arrayRespuesta['strMensaje']);
                        }    

                                  
                    }       
                    
                }
                $this->emcom->flush();
                $this->emcom->getConnection()->commit();
   
            }
            else
            {
                $msg = "No se encontro persona.";
                $success = false;
            }
            
            return array('success' => $success, 'msg' => $msg);
        }
        catch(\Exception $e)
        {
            $this->emcom->getConnection()->rollback();
            $this->emcom->getConnection()->close();
            $success = false;
            $msg = $e->getMessage();
            return array('success' => $success, 'msg' => $msg);
        }
    }

    /**
     * Funcion que permite editar la informacion de la InfoPersona
     * 
     * @author Juan Lafuente <jlafuente@telconet.ec>
     * @version 1.0 
     * @since 24/03/2016
     * 
     * @param array $arrayParametros
     * @param array $arrayData
     *
     * Actualización: 
     * - Se recepta parametros strIpCreacion y strUsrCreacion los cuales seran usados en el catch
     *   para que sean insertados en BD
     * - Se corrige en el catch reemplazando Exception por \Exception
     * - Se corrige en el catch mensaje que se retorna al usuario que sea mas entendible
     * - Se corrige en el catch insertar error en BD
     * @author Andrés Montero<amontero@telconet.ec>
     * @version 1.1 14-03-2017
     *
     */
    public function editarPersona($arrayParametros)
    {
        $strUsrCreacion = $this->validarParametro('strUsrCreacion', $arrayParametros) ? $arrayParametros['strUsrCreacion'] : "telcos";
        $strIpCreacion  = $this->validarParametro('strIpCreacion', $arrayParametros) ? $arrayParametros['strIpCreacion'] : "127.0.0.1";
        try
        {
            $arrayData = array();
            // ...
            if(!array_key_exists('identificacionCliente', $arrayParametros))
            {
                $arrayData['status']  = 'ERROR_SERVICE';
                $arrayData['mensaje'] = 'La Identificacion es un campo obligatorio ';
                return $arrayData;
            }
            if(!array_key_exists('tipoIdentificacion', $arrayParametros))
            {
                $arrayData['status']  = 'ERROR_SERVICE';
                $arrayData['mensaje'] = 'El tipo de Identificacion es un campo obligatorio ';
                return $arrayData;
            }
            
            if(array_key_exists('tipoIdentificacion', $arrayParametros))
            {
                $objPersona =$this->emcom->getRepository('schemaBundle:InfoPersona')->find($arrayParametros['intIdPersona']);
                
                if(isset($objPersona))
                {
                    if( $this->validarParametro('tipoEmpresa', $arrayParametros) )
                    {
                        $objPersona->setTipoEmpresa($arrayParametros['tipoEmpresa']);
                    }
                    if( $this->validarParametro('tipoTributario', $arrayParametros) )
                    {
                        $objPersona->setTipoTributario($arrayParametros['tipoTributario']);
                    }
                    if( $this->validarParametro('razonSocial', $arrayParametros) )
                    {
                        $objPersona->setRazonSocial($arrayParametros['razonSocial']);
                    }
                    else
                    {
                        if( $this->validarParametro('tituloId', $arrayParametros) )
                        {
                            $objTitulo = $this->emcom->getRepository('schemaBundle:AdmiTitulo')->find($arrayParametros['tituloId']);
                            if( $objTitulo )
                            {
                                $objPersona->setTituloId($objTitulo);
                            }
                        }
                        if( $this->validarParametro('nombres', $arrayParametros) )
                        {
                            $objPersona->setNombres($arrayParametros['nombres']);
                        }
                        if( $this->validarParametro('apellidos', $arrayParametros) )
                        {
                            $objPersona->setApellidos($arrayParametros['apellidos']);
                        }
                        if( $this->validarParametro('genero', $arrayParametros) )
                        {
                            $objPersona->setGenero($arrayParametros['genero']);
                        }
                        if( $this->validarParametro('estadoCivil', $arrayParametros) )
                        {
                            $objPersona->setEstadoCivil($arrayParametros['estadoCivil']);
                        }
                        {
                            $objPersona->setOrigenIngresos($arrayParametros['origenIngresos']);
                        }

                        if(!$arrayParametros['fechaNacimiento'] 
                            || (!$arrayParametros ['fechaNacimiento'] ['year'] 
                                && !$arrayParametros ['fechaNacimiento'] ['month'] 
                                && !$arrayParametros ['fechaNacimiento'] ['day']))
                        {
                            $arrayData['status']  = 'ERROR_SERVICE';
                            $arrayData['mensaje'] = 'La Fecha de Nacimiento es un campo obligatorio';
                            return $arrayData;
                        }          

                        if($arrayParametros['fechaNacimiento']['year'] && 
                           $arrayParametros['fechaNacimiento']['month'] && 
                           $arrayParametros['fechaNacimiento']['day'])
                        {
                            $objPersona->setFechaNacimiento(date_create($arrayParametros['fechaNacimiento']['year'] . '-'
                                                                      . $arrayParametros['fechaNacimiento']['month'] . '-'
                                                                      . $arrayParametros['fechaNacimiento']['day']));
                        }
                    }

                    if( $this->validarParametro('representanteLegal', $arrayParametros) )
                    {
                        $objPersona->setRepresentanteLegal($arrayParametros['representanteLegal']);
                    }
                    if( $this->validarParametro('nacionalidad', $arrayParametros) )
                    {
                        $objPersona->setNacionalidad($arrayParametros['nacionalidad']);
                    }
                    if( $this->validarParametro('direccionTributaria', $arrayParametros) )
                    {
                        $objPersona->setDireccionTributaria($arrayParametros['direccionTributaria']);
                    }
                    if( $this->validarParametro('origenProspecto', $arrayParametros) )
                    {
                        $objPersona->setOrigenProspecto($arrayParametros['origenProspecto']);
                    }
                    if( $this->validarParametro('estado', $arrayParametros) )
                    {
                        $objPersona->setEstado($arrayParametros['estado']);
                    }
                    if( $this->validarParametro('contribuyenteEspecial', $arrayParametros) )
                    {
                        $objPersona->setContribuyenteEspecial($arrayParametros ['contribuyenteEspecial']);
                    }     
                    if( $this->validarParametro('pagaIva', $arrayParametros) )
                    {
                        $objPersona->setPagaIva($arrayParametros ['pagaIva']);     
                    }                                
                    if( $this->validarParametro('tieneCarnetConadis', $arrayParametros) )
                    {
                        if( $arrayParametros ['tieneCarnetConadis'] == 'S')
                        {
                            if( $this->validarParametro('numeroConadis', $arrayParametros) )
                            {
                                $objPersona->setNumeroConadis($arrayParametros ['numeroConadis']); 
                            } 
                        }
                        else
                        {
                            $objPersona->setNumeroConadis(null); 
                        }
                    }
                    $this->emcom->persist($objPersona);
                    $this->emcom->flush();
                    
                    $arrayData['objPersona'] = $objPersona;
                    return $arrayData;
                }
                else
                {
                    $arrayData['status']  = 'ERROR_SERVICE';
                    $arrayData['mensaje'] = '"No se encontro la información del cliente - ' . 
                                            $arrayParametros['identificacionCliente'] . 
                                            ', Favor Revisar!';
                    return $arrayData;
                }
            }
            else
            {
                $arrayData['status']  = 'ERROR_SERVICE';
                $arrayData['mensaje'] = '"No se encontro la información del cliente - ' . 
                                        $arrayParametros['identificacionCliente'] . 
                                        ', Favor Revisar!';
                return $arrayData;
            }
        } 
        catch(\Exception $e)
        {
            $arrayData['status']  = 'ERROR_SERVICE';
            $arrayData['mensaje'] = "No se pudo editar persona, error inesperado";
            $this->serviceUtil->insertError(
                                            "Telcos+",
                                            "InfoPersonaService->editarPersona", 
                                            $e->getMessage(), 
                                            $strUsrCreacion, 
                                            $strIpCreacion
                                           );
            return $arrayData;
        }
    }
    
    /**
     * Funcion que permite validar si el array contiene el campo clave por el parametro descrito
     * 
     * @author Juan Lafuente <jlafuente@telconet.ec>
     * @version 1.0 
     * @since 24/03/2016
     * 
     * @param array $strParametro
     * @param array $arrayParametros
     */
    private function validarParametro($strParametro, $arrayParametros)
    {
        if( array_key_exists($strParametro,$arrayParametros) 
            && ( is_null($arrayParametros[$strParametro]) || !empty($arrayParametros[$strParametro])) )
        {
            return true;
        }
        else 
        {
            return false;
        }
    }
    
    /**
     * Documentación para el método editaTipoEmpresaTributario 
     * Función utilizada para editar los campos Tipo Tributario, y Tipo Empresa.
     * Se envia en arreglo los parametros para la actualizacion de la informacion. 
     * 
     * @author : Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 17-11-2016 
     * @param array $arrayParametros    - intIdPersona
     *                                  - intIdPersonaRol
     *                                  - strTipoEmpresaNuevo
     *                                  - strTipoTributarioNuevo        
     *                                  - strUsrCreacion
     *                                  - strIpCreacion 
     *  
     * @return array (success=>[bolean],msg=>[mensaje del resultado])
     */
    public function editaTipoEmpresaTributario($arrayParametros)       
    {       
        $this->emcom->getConnection()->beginTransaction();
        try
        {
            $strMsg      = "Se edito nombre persona Correctamente";
            $boolSuccess = true;
            
            $objInfoPersona   = $this->emcom->getRepository('schemaBundle:InfoPersona')->find($arrayParametros['intIdPersona']);
            $objPersonaEmpRol = $this->emcom->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($arrayParametros['intIdPersonaRol']);
            $objMotivo        = $this->emcom->getRepository('schemaBundle:AdmiMotivo')
                                     ->findOneBy(array('nombreMotivo' => 'CAMBIO DATOS FACTURACION'));
            
            if(!is_object($objMotivo))
            {
                throw new \Exception("No encontro motivo de Edición de Datos");
            }
            
            if(is_object($objInfoPersona) && is_object($objPersonaEmpRol))
            {                
                if(!empty($arrayParametros['strTipoEmpresaNuevo']) && !empty($arrayParametros['strTipoTributarioNuevo']))
                {

                    if($objInfoPersona->getTipoEmpresa() != $arrayParametros['strTipoEmpresaNuevo'])
                    {
                        // Guardo Historial de informacion editada
                        $objInfoPersonaEmpresaRolHisto = new InfoPersonaEmpresaRolHisto();
                        $objInfoPersonaEmpresaRolHisto->setEstado($objPersonaEmpRol->getEstado());
                        $objInfoPersonaEmpresaRolHisto->setFeCreacion(new \DateTime('now'));
                        $objInfoPersonaEmpresaRolHisto->setUsrCreacion($arrayParametros['strUsrCreacion']);
                        $objInfoPersonaEmpresaRolHisto->setIpCreacion($arrayParametros['strIpCreacion']);
                        $objInfoPersonaEmpresaRolHisto->setPersonaEmpresaRolId($objPersonaEmpRol);
                        $objInfoPersonaEmpresaRolHisto->setMotivoId($objMotivo->getId());
                        $objInfoPersonaEmpresaRolHisto->setObservacion("Tipo de Empresa anterior: ".$objInfoPersona->getTipoEmpresa());
                        $this->emcom->persist($objInfoPersonaEmpresaRolHisto);
                        
                        $objInfoPersona->setTipoEmpresa($arrayParametros['strTipoEmpresaNuevo']);  
                    }
                    
                    if($objInfoPersona->getTipoTributario() != $arrayParametros['strTipoTributarioNuevo'])
                    {
                        // Guardo Historial de informacion editada
                        $objInfoPersonaEmpresaRolHisto = new InfoPersonaEmpresaRolHisto();
                        $objInfoPersonaEmpresaRolHisto->setEstado($objPersonaEmpRol->getEstado());
                        $objInfoPersonaEmpresaRolHisto->setFeCreacion(new \DateTime('now'));
                        $objInfoPersonaEmpresaRolHisto->setUsrCreacion($arrayParametros['strUsrCreacion']);
                        $objInfoPersonaEmpresaRolHisto->setIpCreacion($arrayParametros['strIpCreacion']);
                        $objInfoPersonaEmpresaRolHisto->setPersonaEmpresaRolId($objPersonaEmpRol);
                        $objInfoPersonaEmpresaRolHisto->setMotivoId($objMotivo->getId());
                        $objInfoPersonaEmpresaRolHisto->setObservacion("Tipo Tributario anterior: " . $objInfoPersona->getTipoTributario());
                        $this->emcom->persist($objInfoPersonaEmpresaRolHisto);

                        $objInfoPersona->setTipoTributario($arrayParametros['strTipoTributarioNuevo']);
                    }                    
                }    

                $this->emcom->persist($objPersonaEmpRol);
                $this->emcom->persist($objInfoPersona);
                $this->emcom->flush();
            }
            else
            {
                $strMsg      = "No se encontró persona.";
                $boolSuccess = false;
            }
            
            $this->emcom->getConnection()->commit();           
        }
        catch(\Exception $e)
        {
            if ($this->emcom->getConnection()->isTransactionActive())
            {            
                $this->emcom->getConnection()->rollback();
            }
            $this->emcom->getConnection()->close();
            $boolSuccess = false;
            $strMsg      = $e->getMessage();
        }
        return array('success' => $boolSuccess, 'msg' => $strMsg);
    }

    /**
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0 07-07-2017
     * Determina la validez de una identificación panameña RUC o CED
     * @version 1.0
     * 
     * @param string $strTipoIdentificacion
     * @param string $strIdentificacionCliente
     * @return string mensaje de error, null en caso contrario
     */
    public function validarFormatoPanama($strIdentificacionCliente, $strTipoIdentificacion)
    {
        return $this->emcom->getRepository('schemaBundle:InfoPersona')->validarFormatoPanama($strIdentificacionCliente, $strTipoIdentificacion);
    }

    /**
     * @author Wilmer Vera <wvera@telconet.ec>
     * @version 1.0 22-12-2017
     * Obtiene la información requerida para login en Android
     * @param array $arrayData
     * @return array datos del usuario, null en caso contrario.
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.1 11-11-2019 - Se agrega parámetro para enviar el rol
     */
    public function getInfoUsuarioMobile($strUserLogin, $intRol = 1)
    {
        try{
            /* @var $repositoryInfoPersona \telconet\comercialBundle\Service\InfoPersonaService*/
            $repositoryInfoPersona = $this->emSeguridad->getRepository('schemaBundle:InfoPersona');

            if ($repositoryInfoPersona->tienePerfilMovilOperaciones($strUserLogin))
            {
            
                $objDataRetorno = $repositoryInfoPersona->getArrayInfoUsuarioByLogin($strUserLogin, $intRol);

                if(is_null($objDataRetorno))
                {

                    $objDataRetorno = array('status'     => 'ERROR_SERVICE' ,
                                            'success'    => false ,
                                            'mensaje'    => "No se pudo consultar al usuario: ".$strUserLogin);                   
                }
            }
            else
            {
                $objDataRetorno = array('status'     => 'ERROR_SERVICE' ,
                                        'success'    => false ,
                                        'mensaje'    => "No tiene perfil el usuario: ".$strUserLogin);
            }

        }
        catch (Exception $e)
        {
            if ($this->emcom->getConnection()->isTransactionActive())
            {
                $this->emcom->getConnection()->rollback();
            }
            $this->emcom->getConnection()->close();
            

            $objDataRetorno = array('status'     => "ERROR" ,
                                    'success'    => false ,
                                    'mensaje'    => $e->getMessage());
        }
        
        return $objDataRetorno;
    }

    /**
     * @author Wilmer Vera <wvera@telconet.ec>
     * @version 1.0 26-12-2017
     * Obtiene la información personal del usuario
     * @param array $arrayData
     * @return object datos del usuario, null en caso contrario.
     */
    public function getInfoUsuarioLogin($strUserLogin)
    {
        try{
            /* @var $repositoryInfoPersona \telconet\comercialBundle\Service\InfoPersonaService*/
            $repositoryInfoPersona = $this->emcom->getRepository('schemaBundle:InfoPersona');

            $objDataRetorno = $repositoryInfoPersona->getResultadoInfoEmpleado($strUserLogin);

            if(is_null($objDataRetorno))
            {

               $objDataRetorno = array( 'status'     => 'ERROR' ,
                                        'success'    => false ,
                                        'mensaje'    => "No se pudo consultar al usuario: ".$strUserLogin);      

            }

        }catch (Exception $e){
            if ($this->emcom->getConnection()->isTransactionActive())
            {
                $this->emcom->getConnection()->rollback();
            }
            $this->emcom->getConnection()->close();

             $objDataRetorno = array('status'     => 'ERROR' ,
                                     'success'    => false ,
                                     'mensaje'    => $e->getMessage());              
        }
        
        return $objDataRetorno;
    }

    /**
     * @author Wilmer Vera <wvera@telconet.ec>
     * @version 1.0 26-12-2017
     * Consume el servicio de Hal para saber si la cuadrilla esta activa y puede 
     * realizar login. 
     * @param array $arrayData
     * @return object datos del usuario, null en caso contrario.
     */
    public function getInicioJornadaHal($arrayData)
    {
        $strIdPersonaRol     = $arrayData['id_empleado'];
        $strUsuario          = $arrayData['login'];
        $strIp               = '127.0.0.1';
        $objCaractEmpresaRol = null;
        $this->emcom->getConnection()->beginTransaction();
        try
        {
            $arrayParametroUrlBase   = $this->emcom->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->getOne(self::CARACTERISTICA_GENERALES_MOVIL, 
                                                                '', 
                                                                '', 
                                                                '', 
                                                                self::CARACTERISTICA_URL_BASE_HALL, 
                                                                '', 
                                                                '', 
                                                                ''
                                                                );
            $strUrlBase = $arrayParametroUrlBase['valor2'];
           

            $objDataRetorno = $this->serviceSoporte->comunicacionWsRestClient(
                                    array ('strUrl'       => $strUrlBase,
                                           'arrayData'    => $arrayData,
                                           'arrayOptions' => array(CURLOPT_SSL_VERIFYPEER => false)));
         
            if($objDataRetorno != null && $objDataRetorno['result']['status'] == 200)
            {
               $strIdCabecera = $objDataRetorno['result']['id_cabecera'];

               $objCaractEmpresaRol  = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                              ->findOneBy(array(
                                                                'descripcionCaracteristica'   => self::CARACTERISTICA_CABECERA_PLANIF,
                                                                'estado'                      => 'Activo')
                                                        );
               $objInfoPersonaEmpresaRolCarac = $this->emcom->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                        ->findBy(array('personaEmpresaRolId'       => $strIdPersonaRol,
                                                                       'caracteristicaId'          => $objCaractEmpresaRol));

                if (is_object($objInfoPersonaEmpresaRolCarac))
                {
                    $objInfoPersonaEmpresaRolCarac->setFeUltMod(new \DateTime('now'));
                    $objInfoPersonaEmpresaRolCarac->setUsrUltMod($strUsuario);
                    $objInfoPersonaEmpresaRolCarac->setValor($strIdCabecera);
                    $objPersonaEmpresaRolCarac->setIpCreacion($strIp);
                    $this->emcom->persist($objInfoPersonaEmpresaRolCarac);
                    $this->emcom->flush();
                }
                else
                {
                    $entityPersonaEmpresaRol = $this->emcom->getRepository('schemaBundle:InfoPersonaEmpresaRol')->findOneById($strIdPersonaRol);
                    $objPersonaEmpresaRolCarac = new InfoPersonaEmpresaRolCarac();
                    $objPersonaEmpresaRolCarac->setPersonaEmpresaRolId($entityPersonaEmpresaRol);
                    $objPersonaEmpresaRolCarac->setCaracteristicaId($objCaractEmpresaRol);
                    $objPersonaEmpresaRolCarac->setValor($strIdCabecera);
                    $objPersonaEmpresaRolCarac->setFeCreacion(new \DateTime('now'));
                    $objPersonaEmpresaRolCarac->setUsrCreacion($strUsuario);
                    $objPersonaEmpresaRolCarac->setIpCreacion($strIp);
                    $objPersonaEmpresaRolCarac->setEstado('Activo');
                    $this->emcom->persist($objPersonaEmpresaRolCarac);
                    $this->emcom->flush();
                }

                $this->emcom->commit();
            }else if ( $objDataRetorno['result']['status'] == 0)
            {
                $objDataRetorno['result'] = array(
                                        'status'          => 0,
                                        'tieneIntervalos' => false,
                                        "esHal"           => false,
                                        'id_cabecera'     => 0,
                                        'msg'             => "Existen problemas de comunicación con el servidor HAL.");  
            }
              
        }
        catch (Exception $e)
        {
            if ($this->emcom->getConnection()->isTransactionActive())
            {
                $this->emcom->getConnection()->rollback();
            }
            $this->emcom->getConnection()->close();

            $objDataRetorno['result'] = array(  'status'     => '0' ,
                                                'tieneIntervalos' => false,
                                                "esHal" => false,
                                                'id_cabecera'=> null,
                                                'msg'        => $e->getMessage());              
        }
        return $objDataRetorno['result'];
    }

         /**
     * Documentación para getValidaTipoTributario
     * 
     * Función que valida el tipo tributario en base a parametro: Natural, devuelve mensaje de validacion parametrizado
     * 
     * @param array $arrayParametros['intIdPersona'  => 'Id de la Persona'  
     *                               'strCodEmpresa' => 'Empresa en sesion'
     *                              ]
     * 
     * @return Retorna mensaje de validación
     * 
     * @author Anabelle Peñaherrera<apenaherrera@telconet.ec>
     * 
     * @version 1.0 11-03-2021
     */
    public function getValidaTipoTributario($arrayParametros)
    {        
        $arrayTipoTributario = $this->emgen->getRepository('schemaBundle:AdmiParametroDet')
                                           ->getOne('PARAM_FLUJO_ADULTO_MAYOR', 'COMERCIAL', '',
                                                    'TIPO_PERSONA', '', '', '', '', '', $arrayParametros['strCodEmpresa']);
          
        $strTipoTributario   = (isset($arrayTipoTributario["valor1"]) && !empty($arrayTipoTributario["valor1"]))
                                ? $arrayTipoTributario["valor1"] : 'NAT';

        //Se obtiene mensaje de validación si  cliente no cumple ser Adulto Mayor
        $arrayMsjValidaTipoTributario = $this->emgen->getRepository('schemaBundle:AdmiParametroDet')
                                                     ->getOne('PARAM_FLUJO_ADULTO_MAYOR', 'COMERCIAL', '', '', 
                                                              'MENSAJE_VALIDACION_TIPO_TRIBUTARIO',
                                                              '', '', '', '', $arrayParametros['strCodEmpresa']);

        $strMsjValidaTipoTributario = (isset($arrayMsjValidaTipoTributario["valor2"]) && !empty($arrayMsjValidaTipoTributario["valor2"])) 
                                           ? $arrayMsjValidaTipoTributario["valor2"] : 'Cliente no es Persona Natural.';
        
        $strMsg     =  "";
        $objPersona =  $this->emcom->getRepository('schemaBundle:InfoPersona')->find( $arrayParametros['intIdPersona']);
        if(is_object($objPersona) && $objPersona->getTipoTributario() != $strTipoTributario)
        {
            $strMsg = $strMsjValidaTipoTributario;
        }
        return $strMsg;
    }               
    
    /**
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.0 05-11-2020
     * cambiar cargo tecnicos
     * @param array $arrayParametros
     * @return array $arrayResponse
     */
    public function cambiarCargoTelcos($arrayParametros)
    {
        $arrayResponse = array();

        try
        {
            $strIpUserSession       = $arrayParametros['clientIp'];
            $strUserSession         = $arrayParametros['user'];
            $intIdPersonaEmpresaRol = $arrayParametros['intIdPersonaEmpresaRol'];
            $strCaracteristica      = $arrayParametros['strCaracteristica'];
            $strNombreArea          = $arrayParametros['strNombreArea'];
            $strValor               = $arrayParametros['strValor'];
            $strAccion              = $arrayParametros['strAccion'];
            $strEstadoActivo        = 'Activo';
            $strEstadoEliminado     = 'Eliminado';
            $intDatetimeActual      = new \DateTime('now');
            $emComercial            = $this->emcom;

            if($intIdPersonaEmpresaRol)
            {
                $objPersonaEmpresaRol  = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                        ->findOneBy( array('id' => $intIdPersonaEmpresaRol, 'estado' => $strEstadoActivo) );
                                
                $objInfoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                        ->findOneById($intIdPersonaEmpresaRol);


                $objCaracteristica = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                    ->findOneBy( array( 'descripcionCaracteristica' => $strCaracteristica,
                                                                        'estado'                    => $strEstadoActivo ) );

                $arrayParametros = array( 'estado'                => $strEstadoActivo,
                                            'personaEmpresaRolId' => $objInfoPersonaEmpresaRol,
                                            'caracteristicaId'    => $objCaracteristica );

                $arrayPersonaEmpresaRolCarac = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                            ->findBy($arrayParametros);
            
                if( !empty($arrayPersonaEmpresaRolCarac) )
                {
                    foreach($arrayPersonaEmpresaRolCarac as $entityPersonaEmpresaRolCarac)
                    {
                        $entityPersonaEmpresaRolCarac->setFeUltMod($intDatetimeActual);
                        $entityPersonaEmpresaRolCarac->setUsrUltMod($strUserSession);
                        $entityPersonaEmpresaRolCarac->setEstado($strEstadoEliminado);

                        $emComercial->persist($entityPersonaEmpresaRolCarac);
                    }
                }
                
                if( $strAccion == 'Guardar')
                {
                    $entityPersonaEmpresaRolCaracNew = new InfoPersonaEmpresaRolCarac();
                    $entityPersonaEmpresaRolCaracNew->setEstado($strEstadoActivo);
                    $entityPersonaEmpresaRolCaracNew->setFeCreacion($intDatetimeActual);
                    $entityPersonaEmpresaRolCaracNew->setIpCreacion($strIpUserSession);
                    $entityPersonaEmpresaRolCaracNew->setUsrCreacion($strUserSession);
                    $entityPersonaEmpresaRolCaracNew->setPersonaEmpresaRolId($objInfoPersonaEmpresaRol);
                    $entityPersonaEmpresaRolCaracNew->setCaracteristicaId($objCaracteristica);
                    $entityPersonaEmpresaRolCaracNew->setValor($strValor);

                    $emComercial->persist($entityPersonaEmpresaRolCaracNew);
                }
                
                /*
                    * Bloque que elimina la relación que existe entre la persona que se le cambia el cargo y un elemento de tipo Tablet
                    */
                if( $strNombreArea == 'Tecnico' )
                {
                    $objDetalleElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                            ->findOneBy( array( 'estado'        => $strEstadoActivo, 
                                                                                'detalleNombre' => 'LIDER', 
                                                                                'detalleValor'  => $intIdPersonaEmpresaRol ) 
                                                                        );

                    if( $objDetalleElemento )
                    {
                        $strElementoActual   = 'Sin asignaci&oacute;n';
                        $intIdElementoActual = $objDetalleElemento->getElementoId();
                        $objElementoActual   = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                    ->findOneBy( array( 'id'     => $intIdElementoActual,
                                                                                        'estado' => $strEstadoActivo ) 
                                                                            );
                        if( $objElementoActual )
                        {
                            $strElementoActual = $objElementoActual->getNombreElemento();
                        }

                        $objDetalleElemento->setEstado($strEstadoEliminado);
                        $this->emInfraestructura->persist($objDetalleElemento);
                        $this->emInfraestructura->flush();
                        
                        
                        $strMotivoElemento  = 'Se elimina tablet asociada';
                        $objMotivo          = $emGeneral->getRepository('schemaBundle:AdmiMotivo')
                                                        ->findOneByNombreMotivo($strMotivoElemento);
                        $intIdMotivo        = $objMotivo ? $objMotivo->getId() : 0;
            
                        $strMensajeObservacion = $strMotivoElemento.": ".$strElementoActual;

                        $objInfoPersonaEmpresaRolHistorial = new InfoPersonaEmpresaRolHisto();
                        $objInfoPersonaEmpresaRolHistorial->setEstado($objPersonaEmpresaRol->getEstado());
                        $objInfoPersonaEmpresaRolHistorial->setFeCreacion($intDatetimeActual);
                        $objInfoPersonaEmpresaRolHistorial->setIpCreacion($strIpUserSession);
                        $objInfoPersonaEmpresaRolHistorial->setPersonaEmpresaRolId($objPersonaEmpresaRol);
                        $objInfoPersonaEmpresaRolHistorial->setUsrCreacion($strUserSession);
                        $objInfoPersonaEmpresaRolHistorial->setObservacion($strMensajeObservacion);
                        $objInfoPersonaEmpresaRolHistorial->setMotivoId($intIdMotivo);
                        $emComercial->persist($objInfoPersonaEmpresaRolHistorial);
                        $emComercial->flush();
                    }
                }

                $emComercial->flush();                
            }

            $arrayResponse = array("status" => 200, "mensaje" => "Se realizó el cambio de cargo exitosamente.");
        }
        catch (Exception $ex)
        {

            $arrayResponse = array("status" => 500, "mensaje" => "No se pudo realizar el cambio de cargo.");

            $strClass                   = "InfoPersonaService";
            $strAppMethod               = "cambiarCargoTelcos";

            $this->insertLog(array(
                                                    'enterpriseCode'   => "10",
                                                    'logType'          => 1,
                                                    'logOrigin'        => 'TELCOS',
                                                    'application'      => 'TELCOS',
                                                    'appClass'         => $strClass,
                                                    'appMethod'        => $strAppMethod,
                                                    'descriptionError' => $ex->getMessage(),
                                                    'status'           => 'Fallido',
                                                    'inParameters'     => json_encode($arrayParametros),
                                                    'creationUser'     => 'TELCOS'));
        }
        
        return $arrayResponse;
    }
}

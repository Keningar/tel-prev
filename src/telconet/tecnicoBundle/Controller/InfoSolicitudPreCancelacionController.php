<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace telconet\tecnicoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\schemaBundle\Entity\InfoElemento;
use telconet\schemaBundle\Entity\AdmiModeloElemento;
use telconet\schemaBundle\Entity\InfoEmpresaElemento;
use telconet\schemaBundle\Entity\InfoEmpresaElementoUbica;
use telconet\schemaBundle\Entity\InfoIp;
use telconet\schemaBundle\Entity\InfoHistorialElemento;
use telconet\schemaBundle\Entity\InfoUbicacion;
use telconet\tecnicoBundle\Service\InfoServicioTecnicoService;
use telconet\tecnicoBundle\Service\MigracionHuaweiService;
use telconet\schemaBundle\Entity\InfoRelacionElemento;
use telconet\schemaBundle\Entity\InfoDocumento;
use telconet\schemaBundle\Entity\AdmiTipoDocumento;
use telconet\schemaBundle\Form\InfoElementoServidorType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

use telconet\schemaBundle\Entity\InfoContrato;
/**
 * Clase que sirve para la administracion de los elementos Servidor
 * 
 * @author John Vera <javera@telconet.ec>
 * @version 1.0 19-02-2015
 */
class InfoSolicitudPreCancelacionController extends Controller
{

    /**
     * @Secure(roles="ROLE_476-1")
     * Funcion que sirve para cargar la pagina inicial
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 21-08-2015
     * 
     * @author Jonathan Mazon Sanchez <jmazon@telconet.ec>
     * @version 1.1 15-03-2023 Se agrego envio de prefijo empresa para validar que presente contenido especifico por empresa.
     */
    public function indexAction()
    {

        $objRequest           = $this->getRequest();
        $objSesion    = $objRequest->getSession();
        $emComercial               = $this->getDoctrine()->getManager('telconet');
        $emGeneral   = $this->getDoctrine()->getManager();
        $emSeguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $emFinanciero   = $this->get('doctrine')->getManager("telconet_financiero");
        $intIdEmpresa      = $objSesion->get('idEmpresa');
        $strPrefijoEmpresa = $objSesion->get('prefijoEmpresa');

        $emInfraestructura    = $this->container->get('doctrine')->getManager('telconet_infraestructura');
        $arrayRolesPermitidos = array();
        $strParametroCabCancelacion ="FLUJO_ACTA_CANCELACION";
        $strParametroModelos="PARAMETROS_ASOCIADOS_A_SERVICIOS_MD";
        $strParametroEstado ="Activo";
        $strEstadoServicosActivo ="Activo";
        $strEstadoServicosIncorte ="In-Corte";
        $strModulo ="FINANCIERO";
        $strBanderaEquipo = "";
        $objPuntoCliente = $objSesion->get('ptoCliente');
        $objPuntoCliente['strPrefijoEmpresa'] = $strPrefijoEmpresa;
        foreach ($objSesion->get('serviciosPunto') as $objServicio):
            if($objServicio['estado']==$strEstadoServicosActivo||$objServicio['estado']==$strEstadoServicosIncorte)
            {

                $objServiciosPunto[] = array('nombre'=>$objServicio['nombre'],'estado'=>$objServicio['estado']);
            }
        endforeach;
        $objPuntosFacturacion = $objSesion->get('datosFinancierosPunto');
        $objPuntoContactos = $objSesion->get('puntoContactos'); 
        $objClienteContactos = $objSesion->get('clienteContactos');
        $objCicloFacturaCliente = $objSesion->get('cicloFacturacionCliente');
        $objCliente = $objSesion->get('cliente');
        $strFechaActual =date_format(new \DateTime('now'), 'Y-m-d');
        $strDescripcionParametroEstados='estadosEquiposCancelacion';
        $strFlujo ="PreCancelacion";
        $strDescripcionParametroEquipos='equiposFacturacion';
        $strValor1Modelos ='MODELOS_EQUIPOS';
        $strBanderaEquipoExtender = "EXTENDER DUAL BAND";
        if($objCliente)
        {

            //obtener mensaje de error parametrizado
            $arrayParametrosCabCancelacion = $emComercial->getRepository('schemaBundle:AdmiParametroCab')
                                            ->findOneBy(array('nombreParametro'  => $strParametroCabCancelacion,
                                                                'estado'           => $strParametroEstado,
                                                                'modulo'           => $strModulo));
            //obtener mensaje de error parametrizado
            $arrayParametrosModeloEquipos = $emComercial->getRepository('schemaBundle:AdmiParametroCab')
                                        ->findOneBy(array('nombreParametro'  => $strParametroModelos,
                                        'estado'           => $strParametroEstado));

            $arrayParametrosDetCancelacion = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                            ->findOneBy(array('parametroId'  => $arrayParametrosCabCancelacion->getId(),
                                                                'estado'       => $strParametroEstado,
                                                                'empresaCod'       => $intIdEmpresa,
                                                                'descripcion'       => $strDescripcionParametroEstados,
                                                                'valor2'       => $strFlujo));

            $arrayParametrosDetFactura = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                            ->findOneBy(array('parametroId'  => $arrayParametrosCabCancelacion->getId(),
                                                                'estado'       => $strParametroEstado,
                                                                'empresaCod'       => $intIdEmpresa,
                                                                'descripcion'       => $strDescripcionParametroEquipos,
                                                                'valor2'       => $strFlujo));

            
            $objEstadoEquiposCancelacion = explode("|",$arrayParametrosDetCancelacion->getValor1());

            $strIdPersonaEmpresaRol = $objCliente['id_persona_empresa_rol'];
            $objContratoActivo = $emComercial->getRepository('schemaBundle:InfoContrato')
                                                ->findBy(array("personaEmpresaRolId" => $strIdPersonaEmpresaRol,
                                                       "estado"              => array($strParametroEstado)));
            $strContratoId = $objContratoActivo[0]->getNumeroContrato();
            
            $arrayParamsServicioInternet    = array('estadosServicios'          => array($strEstadoServicosActivo,$strEstadoServicosIncorte),
                                        'productoInternetPorLogin'  => 'S',
                                        'estadoActivo'              => 'Activo',
                                        'empresaCod'                => $intIdEmpresa,
                                        'intIdPuntoCliente'         => $objPuntoCliente['id'],
                                        'nombreTecnicoProducto'     => $arrayParametrosDetCancelacion->getValor3(),
                                        'omiteEstadoPunto'          => "SI");
            $objServicioPunto = $emComercial->getRepository('schemaBundle:InfoServicio')
                                ->getServiciosByCriterios($arrayParamsServicioInternet)['registros'][0]; 

            $intIdServicio          = $objServicioPunto->getId();
            $intEmpresaId           = $objSesion->get('idEmpresa'); 
            $arrayPtoSession        = $objSesion->get('ptoCliente');
            $serviceSolicitudes     = $this->get('comercial.Solicitudes');
    
            $arrayParametros                           = array();
            $arrayParametros['intServicioId']          = $intIdServicio;          
            $arrayParametros['strEmpresaCod']          = $intEmpresaId;
            $arrayParametros['serviceSolicitud']       = $serviceSolicitudes;  
            $arrayParametros['intPtoSessionId']        = $arrayPtoSession['id'];
            $arrayParametros['descripcionProducto']    = $arrayParametrosDetCancelacion->getValor3(); 
            $arrayParametros['strParametro']           = $arrayParametrosDetFactura->getValor1();
            $arrayParametros['strProceso']             = $arrayParametrosDetFactura->getValor3();
            $arrayParametros['strModulo']              = $arrayParametrosDetFactura->getValor4();
            

            $objEquiposJson   = $emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                        ->getJsonEquiposFacturar($arrayParametros);
            $objEquiposTemp = json_decode($objEquiposJson);


            $objDataTecnica  = $this->get('tecnico.DataTecnica');
                        
            $arrayParametrosDataTecnica = array(   'idServicio'    => $intIdServicio,
                                        'idEmpresa'     => $intIdEmpresa,
                                        'prefijoEmpresa'=> $strPrefijoEmpresa);

            $arrayDatosTecnicos         = $objDataTecnica->getDataTecnica($arrayParametrosDataTecnica);

            $objElementoCpe = $arrayDatosTecnicos['elementoCpe'];
            $objElementoCliente = $arrayDatosTecnicos['elementoCliente'];
            $objMacCliente = $arrayDatosTecnicos['macCliente'];
            $arrayElementosExtenderDualBand = $arrayDatosTecnicos['arrayElementosExtenderDualBand'];
            if($objElementoCliente)
            {
                $arrayParametrosDetModelos = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                ->findOneBy(array('parametroId'  => $arrayParametrosModeloEquipos->getId(),
                                    'estado'       => $strParametroEstado,
                                    'empresaCod'       => $intIdEmpresa,
                                    'valor1'       => $strValor1Modelos,
                                    'valor5'       => $objElementoCliente->getModeloElementoId()
                                    ->getNombreModeloElemento()));
            }
            else if($objElementoCpe)
            {
                $arrayParametrosDetModelos = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                ->findOneBy(array('parametroId'  => $arrayParametrosModeloEquipos->getId(),
                                    'estado'       => $strParametroEstado,
                                    'empresaCod'       => $intIdEmpresa,
                                    'valor1'       => $strValor1Modelos,
                                    'valor5'       => $objElementoCpe->getModeloElementoId()
                                    ->getNombreModeloElemento()));
            }

            if($arrayParametrosDetModelos)
            {
                $strBanderaEquipo = $arrayParametrosDetModelos->getValor4() =="WIFI DUAL BAND"? $arrayParametrosDetModelos->getValor4():"CPE ONT";
            
            }else
            {
                $strBanderaEquipo = "CPE ONT";
            }
            

            $objEquiposList = $objEquiposTemp->equipos;
            foreach ($objEquiposList as $objEquipo):
                if($objEquipo->descripcion==$strBanderaEquipo)
                {

                    $objEquipos[] = array(  'id'         => $objEquipo->id,
                    'descripcion'     => $objEquipo->descripcion,
                    'tecnologia'     => $objEquipo->tecnologia,
                    'precio'     => $objEquipo->precio,
                    'cantidad'     => $objEquipo->cantidad,
                    'estados'     => $objEstadoEquiposCancelacion,
                    'estado'     => "Bueno",
                    'serie'     => $objElementoCliente->getSerieFisica(),
                    'mac'     => $objMacCliente&&$objMacCliente->getValor()?$objMacCliente->getValor():$arrayParametrosDetCancelacion->getValor4(),
                );
            }
            else if($objEquipo->descripcion==$strBanderaEquipoExtender)
            {
               if($arrayElementosExtenderDualBand)
               {
                $intSecuencialExtender=0;
                foreach ($arrayElementosExtenderDualBand as $objExtender):
                    $intSecuencialExtender=$intSecuencialExtender+1;
                    $objEquipos[] = array(  'id'         => $objEquipo->id,
                    'descripcion'     => $objEquipo->descripcion.$intSecuencialExtender,
                        'tecnologia'     => $objEquipo->tecnologia,
                        'precio'     => $objEquipo->precio,
                        'cantidad'     => $objEquipo->cantidad,
                        'estados'     => $objEstadoEquiposCancelacion,
                        'estado'     => "Bueno",
                        'serie'     => $objExtender->getSerieFisica(),
                        'mac'     => $objExtender->getMacElemento(),
                        );
                    endforeach;
                   }else
                   {
                    $objEquipos[] = array(  'id'         => $objEquipo->id,
                    'descripcion'     => $objEquipo->descripcion,
                    'tecnologia'     => $objEquipo->tecnologia,
                    'precio'     => $objEquipo->precio,
                    'cantidad'     => $objEquipo->cantidad,
                    'estados'     => $objEstadoEquiposCancelacion,
                    'estado'     => "Bueno",
                    'serie'     => $arrayParametrosDetCancelacion->getValor4(),
                    'mac'     => $arrayParametrosDetCancelacion->getValor4(),
                    );
                   }
                    
                }
                else
                {

                    $objEquipos[] = array(  'id'         => $objEquipo->id,
                    'descripcion'     => $objEquipo->descripcion,
                    'tecnologia'     => $objEquipo->tecnologia,
                    'precio'     => $objEquipo->precio,
                    'cantidad'     => $objEquipo->cantidad,
                    'estados'     => $objEstadoEquiposCancelacion,
                    'estado'     => "Bueno",
                    'serie'     => $arrayParametrosDetCancelacion->getValor4(),
                    'mac'     => $arrayParametrosDetCancelacion->getValor4(),
                    );
                }
            endforeach;

            $strIdServicio = strval($intIdServicio);                                  
            $arrayParametros2                      = array();
            $arrayParametros2['intIdServicio']     = $strIdServicio;
            $arrayParametros2['strEmpresaCod']     = $intEmpresaId;
            $arrayParametros2['intPtoSessionId']   = $arrayPtoSession['id'];
            $arrayParametros2['descripProducto']   = $arrayParametrosDetCancelacion->getValor3();        
            $arrayParametros2['emFinanciero']      = $emFinanciero;  
            $arrayParametros2['strParametro']      = $arrayParametrosDetFactura->getValor1();
            $arrayParametros2['strDescripcion']    = $arrayParametrosDetFactura->getValor3();
            $arrayParametros2['strModulo']         = $arrayParametrosDetFactura->getValor4();
            
            $objValoresFacturarJson   = $emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                     ->getJsonValoresFacturar($arrayParametros2);

            $serviceTecnico    = $emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                     ->findOneBy(array( "servicioId" => $strIdServicio));

            $objValoresFacturar = json_decode($objValoresFacturarJson);

            
        }
        else
        {
            $strIdPersonaEmpresaRol ='';
            $objContratoActivo = '';
            $objServicioPunto = '';
            $objEquipos = '';
            $strContratoId = '';
            $objValoresFacturar ='';
            $objEstadoEquiposCancelacion='';
        }
         
        $objSistModulo = $emSeguridad->getRepository('schemaBundle:SistModulo')
                                    ->findOneBy(array( 'nombreModulo' => 'clientes','estado' => 'Modificado'));
        $objSistAccion = $emSeguridad->getRepository('schemaBundle:SistAccion')
                                    ->findOneBy(array( 'nombreAccion' => 'cancelarCliente','estado' => 'Activo',
                                   'usrCreacion' => 'fadum'));
        $objMotivos;
        if(is_object($objSistModulo))
        {        
            $objSeguRelacionSistema = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')
                                                  ->findOneBy(array( 'moduloId' => $objSistModulo->getId(),
                                                  'accionId' =>$objSistAccion->getId()));

            if(is_object($objSeguRelacionSistema))
            {
                $arrayResultado = $emGeneral->getRepository('schemaBundle:AdmiMotivo')
                                            ->loadMotivos($objSeguRelacionSistema->getId());

                    foreach($arrayResultado as $objMotivo):
                        $objMotivos[] = array(  'intIdMotivo'         => $objMotivo->getId(),
                        'strDescripcion'     => $objMotivo->getNombreMotivo()
                        );
                    
                    endforeach;
            }
        }

       
        return $this->render('tecnicoBundle:InfoSolicitudPreCancelacion:index.html.twig', array(
                'rolesPermitidos' => $arrayRolesPermitidos,
                'objPuntoCliente' => $objPuntoCliente,
                'objServiciosPunto' => $objServiciosPunto,
                'objPuntosFacturacion' => $objPuntosFacturacion,
                'objPuntoContactos' => $objPuntoContactos,
                'objClienteContactos' => $objClienteContactos,
                'objCicloFacturaCliente' => $objCicloFacturaCliente,
                'objCliente' => $objCliente,
                'strFechaActual' => $strFechaActual,
                'strContratoId' => $strContratoId,
                'objMotivos'  => $objMotivos,
                'objEquipos' => $objEquipos,
                'objValoresFacturar' => $objValoresFacturar,
                'objEstadoEquiposCancelacion' => $objEstadoEquiposCancelacion

        ));
    }

     /**
     * @Secure(roles="ROLE_476-1")
     * enviarTareaRapidaAction()
     * Función que crea acta precancelacion y envia tarea rapida
     * 
     * @author Ivan Romero <icromero@telconet.ec>
     * @version 1.0 05-12-2021
     * 
     * @author José Candelario <jcandelario@telconet.ec>
     * @version 1.1 06-10-2022 - Se agrega login de cancelación en la excepción de error.
     * 
     * @author José Candelario <jcandelario@telconet.ec>
     * @version 1.2 02-12-2022 - Se realiza mejora en el proceso al momento de obtener la información del usuario asignado
     *                           que lo realice por tipo rol empleado.
     * 
     * @author Jonathan Mazon Sanchez <jmazon@telconet.ec>
     * @version 1.3 15-03-2023 Se agrego validacion de plantilla de correo para flujo de ECUANET.
     * 
     * @since 1.0
     *
     * @return $objResponse - envia tarea rapida
     */
    public function enviarTareaRapidaAction()
    {   
    
        $arrayRespuesta                = "";
        $strLoginCancelacion           = "";
        $emComercial                   = $this->getDoctrine()->getManager();
        $emComunicacion                = $this->getDoctrine()->getManager('telconet_comunicacion');
        $objServicioRepository         = $emComercial->getRepository('schemaBundle:InfoServicio');
        $serviceSoporte                = $this->get('soporte.SoporteService');
        $serviceProceso                = $this->get('soporte.ProcesoService');
        $servicePlantilla              = $this->get('administracion.Plantilla'); 
        $serviceTokenCas               = $this->get('seguridad.TokenCas');
        $serviceUtil                   = $this->get('schema.Util');

        $strPathTelcos                 = $this->container->getParameter('path_telcos');
        $strRutaContrato               = $this->container->getParameter('contrato_digital_ruta');
        $strRutaBase                   = $strPathTelcos . $strRutaContrato;

        $strPathPlantilla              = $strPathTelcos."telcos/src/telconet/soporteBundle/Resources/views/Default/";
        $serviceTemplating                    = $this->get('templating');
        $serviceEnvioMail              = $this->get('soporte.EnvioPlantilla');
        $arrayParametrosCabCancelacion = "";
        $objRequest                    = $this->getRequest();
        $objSesion                     = $objRequest->getSession();

        $strNombresCliente            = $objRequest->get('strNombresCliente');
        $strContratoCliente           = $objRequest->get('strContratoCliente');
        $strCategoriaCliente          = $objRequest->get('strCategoriaCliente');
        $strLoginCancelacion          = $objRequest->get('strLoginCliente');
        $strServicioCliente           = $objRequest->get('strServicioCliente');
        $strDeudaCliente              = $objRequest->get('strDeudaCliente');
        $strDireccionCliente          = $objRequest->get('strDireccionCliente');
        $strMotivoCliente             = $objRequest->get('strMotivoCliente');
        $strNombresApellidosCompletos = $objRequest->get('strNombresApellidosCompletos');
        $strObservacionCancelacion    = $objRequest->get('strObservacionesCancelacion');
        $strOficinaCliente            = $objRequest->get('strOficinaCliente');
        $strFechaActualCliente        = $objRequest->get('strFechaActualCliente');
        $strValorEquipos              = $objRequest->get('strValorEquipos');
        $strValorInstalacion          = $objRequest->get('strValorInstalacion');
        $strValorPromociones          = $objRequest->get('strValorPromociones');
        $strValorSubtotalFactura      = $objRequest->get('strValorSubtotalFactura');
        $strIdentificacion            = $objRequest->get('strIdentificacion');
        $strFechaVigencia             = $objRequest->get('strFechaVigencia');
        $strEntregaEquipos            = $objRequest->get('entregaEquipos');
        
        $objCliente                   = $objRequest->get('objCliente');
        $objEquipos                   = $objRequest->get('objEquipos');

        $intIdEmpresa                        = $objSesion->get('idEmpresa');
        $strUsuario                          = $objSesion->get('user');
        $strPrefijoEmpresa                   = $objSesion->get('prefijoEmpresa');

        $objPuntoContactos = $objSesion->get('puntoContactos'); 

        $strParametroCabCancelacion          = "FLUJO_ACTA_CANCELACION";
        $strParametroEstado                  = "Activo";
        $strModulo                           = "FINANCIERO";
        $strParametroMensajeCancelacion      = "MensajeCancelacion";
        $strParametroNombreTarea             = "NombreTarea";
        $strFlujo                            = "PreCancelacion";
        $strParametroTarea                   = "TareaCancelacion";
        $strParametroMensajes                = "MensajesPredeterminados";
        $strIpCliente                        = $objRequest->getClientIp();
        $strParametroOrigenTarea             = "OrigenTarea";
        $strParametroOrigenComunicacionTarea = "OrigenComunicacionTarea";
        $strParametroClaseTarea              = "ClaseTarea";
        $intIdPersonEmpRolEmpl               = $objSesion->get('idPersonaEmpresaRol');
        $strParametroNombrePlantilla         = "codigoPlantillaCancelacion";
        $arrayToCliente = array();
        $strPlantillaCorreo = ($strPrefijoEmpresa=="EN")?'ACTA_PRECAN_EN':'ACTA_PRE_CANCEL';
        try 
        {
            
            
            //obtener mensaje de error parametrizado
            $arrayParametrosCabCancelacion = $emComercial->getRepository('schemaBundle:AdmiParametroCab')
                                            ->findOneBy(array('nombreParametro'  => $strParametroCabCancelacion,
                                                                'estado'           => $strParametroEstado,
                                                                'modulo'           => $strModulo));

            $objPlantillaCancelacion = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                            ->findOneBy(array('parametroId'  => $arrayParametrosCabCancelacion->getId(),
                                                                'estado'       => $strParametroEstado,
                                                                'empresaCod'       => $intIdEmpresa,
                                                                'descripcion'       => $strParametroNombrePlantilla,
                                                                'valor2'       => $strFlujo));
            
            $objNombreTarea = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                            ->findOneBy(array('parametroId'  => $arrayParametrosCabCancelacion->getId(),
                                                                'estado'       => $strParametroEstado,
                                                                'empresaCod'       => $intIdEmpresa,
                                                                'descripcion'       => $strParametroNombreTarea,
                                                                'valor2'       => $strFlujo));

            $objMensajeCancelacion = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                            ->findOneBy(array('parametroId'  => $arrayParametrosCabCancelacion->getId(),
                                                                'estado'       => $strParametroEstado,
                                                                'empresaCod'       => $intIdEmpresa,
                                                                'descripcion'       => $strParametroMensajeCancelacion,
                                                                'valor2'       => $strFlujo));

            $objOrigenTarea = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                            ->findOneBy(array('parametroId'  => $arrayParametrosCabCancelacion->getId(),
                                                                'estado'       => $strParametroEstado,
                                                                'empresaCod'       => $intIdEmpresa,
                                                                'descripcion'       => $strParametroOrigenTarea,
                                                                'valor2'       => $strFlujo));  
                                                                
            $objOrigenComunicacionTarea = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                            ->findOneBy(array('parametroId'  => $arrayParametrosCabCancelacion->getId(),
                                                                'estado'       => $strParametroEstado,
                                                                'empresaCod'       => $intIdEmpresa,
                                                                'descripcion'       => $strParametroOrigenComunicacionTarea,
                                                                'valor2'       => $strFlujo));  

            $objClaseTarea = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                            ->findOneBy(array('parametroId'  => $arrayParametrosCabCancelacion->getId(),
                                                                'estado'       => $strParametroEstado,
                                                                'empresaCod'       => $intIdEmpresa,
                                                                'descripcion'       => $strParametroClaseTarea,
                                                                'valor2'       => $strFlujo)); 

            $objTarea = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                            ->findOneBy(array('parametroId'  => $arrayParametrosCabCancelacion->getId(),
                                                                'estado'       => $strParametroEstado,
                                                                'empresaCod'       => $intIdEmpresa,
                                                                'descripcion'       => $strParametroTarea,
                                                                'valor2'       => $strFlujo)); 
            $objMensajesPredeterminados = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                            ->findOneBy(array('parametroId'  => $arrayParametrosCabCancelacion->getId(),
                                                                'estado'       => $strParametroEstado,
                                                                'empresaCod'       => $intIdEmpresa,
                                                                'descripcion'       => $strParametroMensajes,
                                                                'valor2'       => $strFlujo));                                                    
            $objAdmiPlantilla = $emComunicacion->getRepository('schemaBundle:AdmiPlantilla')
                                            ->getPlantillaXCodigoYEmpresa($strPlantillaCorreo);
            
            $objInfoPersona = $emComercial->getRepository('schemaBundle:InfoPersona')->findByLogin($objNombreTarea->getValor5());
           
            $arrayEstados   = array('Activo');
            $arrayDatosPer  = $emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                          ->getInfoDatosPersona(array ('strRol'                     => 'Empleado',
                                                                       'strPrefijo'                 => $strPrefijoEmpresa,
                                                                       'strEstadoPersona'           => $arrayEstados,
                                                                       'strEstadoPersonaEmpresaRol' => 'Activo',
                                                                       'strLogin'                   => $objNombreTarea->getValor5(),
                                                                       'intIdPersona'               => $objInfoPersona[0]->getId()));
            if(empty($arrayDatosPer) || !is_array($arrayDatosPer) ||
            (isset($arrayDatosPer['status']) && $arrayDatosPer['status'] === 'fail') ||
            ($arrayDatosPer['status'] === 'ok' && empty($arrayDatosPer['result'])))
            {
                throw new \Exception('Error al obtener los datos del asignado, por favor comunicar a Sistemas. '.$arrayDatosPer['message']);
            }                        
            
            $objInfoPersonaEmpresa = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                 ->find($arrayDatosPer['result'][0]['idPersonaEmpresaRol']);
            
            if (!is_object($objInfoPersonaEmpresa) 
                || $objInfoPersonaEmpresa->getDepartamentoId() == "" 
                || $objInfoPersonaEmpresa->getDepartamentoId() == null)
            {
                throw new \Exception('Error al obtener el departamento de la persona asignada, por favor comunicar a Sistemas.');
            }

            $strAsunto = $objTarea->getValor6();

            $arrayTokenCas = $serviceTokenCas->generarTokenCas();

            $strCodigoPlantilla               = $objPlantillaCancelacion->getValor1();

            $objContrato  = array('clave'   => 'login',
                                  'valor'   => $strLoginCancelacion);
            $arrayContrato[] = $objContrato;
            $objContrato  = array('clave'   => 'cedula',
                                  'valor'   => $strIdentificacion);
            $arrayContrato[] = $objContrato;
            $objContrato  = array('clave'   => 'nombrePersona',
                                  'valor'   => $strNombresCliente);
            $arrayContrato[] = $objContrato;
            $objContrato  = array('clave'   => 'numeroContrato',
                                  'valor'   => $strContratoCliente);
            $arrayContrato[] = $objContrato;
            $objContrato  = array('clave'   => 'categoria',
                                  'valor'   => $strCategoriaCliente);
            $arrayContrato[] = $objContrato;
            $objContrato  = array('clave'   => 'direccionPunto',
                                  'valor'   => $strDireccionCliente);
            $arrayContrato[] = $objContrato;
            $objContrato  = array('clave'   => 'servicios',
                                  'valor'   => $strServicioCliente);
            $arrayContrato[] = $objContrato;
            $objContrato  = array('clave'   => 'valorDeuda',
                                  'valor'   => $strDeudaCliente);
            $arrayContrato[] = $objContrato;
            $objContrato  = array('clave'   => 'motivoCancelacion',
                                  'valor'   => $strMotivoCliente);
            $arrayContrato[] = $objContrato;
            $objContrato  = array('clave'   => 'observaciones',
                                  'valor'   => $strObservacionCancelacion);
            $arrayContrato[] = $objContrato;
            $objContrato  = array('clave'   => 'nombreCompleto',
                                  'valor'   => $strNombresApellidosCompletos);
            $arrayContrato[] = $objContrato;
            $objContrato  = array('clave'   => 'oficina',
                                  'valor'   => $strOficinaCliente);
            $arrayContrato[] = $objContrato;
            $objContrato  = array('clave'   => 'fechaActual',
                                  'valor'   => $strFechaActualCliente);
            $arrayContrato[] = $objContrato;
            $objContrato  = array('clave'   => 'fechaVigencia',
                                  'valor'   => $strFechaVigencia);
            $arrayContrato[] = $objContrato;
            $objContrato  = array('clave'   => 'valorEquipos',
                                  'valor'   => $strValorEquipos);
            $arrayContrato[] = $objContrato;
            $objContrato  = array('clave'   => 'valorInstalacion',
                                  'valor'   => $strValorInstalacion);
            $arrayContrato[] = $objContrato;
            $objContrato  = array('clave'   => 'valorPromociones',
                                  'valor'   => $strValorPromociones);
            $arrayContrato[] = $objContrato;
            $objContrato  = array('clave'   => 'valorSubtotalFactura',
                                  'valor'   => $strValorSubtotalFactura);
            $arrayContrato[] = $objContrato;
            $objContrato  = array('clave'   => 'entregaEquipo',
                                  'valor'   => $strEntregaEquipos);
            $arrayContrato[] = $objContrato;
            $objContrato  = array('clave'   => 'equipos',
                                  'valor'   => $objEquipos);
            $arrayContrato[] = $objContrato;
            $objContrato  = array('clave'   => 'masIva',
                                  'valor'   => $objMensajesPredeterminados->getValor1());
            $arrayContrato[] = $objContrato;
            
            $arrayParametros =array('token'           => $arrayTokenCas['strToken'],
                                    'codigoPlantilla' => $strCodigoPlantilla,
                                    'propiedades'     => $arrayContrato,
                                    'login'           => $strLoginCancelacion,
                                    'strIpCliente'    => $strIpCliente);
            
            $objPlantillasResponse = $servicePlantilla->usarPlantillaMs($arrayParametros);
           
            $arrayParametros['html'] = $objPlantillasResponse['objData']['resultadoTemplate'];
            $objConvertDocsResponse = $servicePlantilla->convertDocsMs($arrayParametros);


            $entityPuntoDestino = $emComercial->getRepository('schemaBundle:InfoPunto')
                                                 ->findOneBy(array('login'  => $strLoginCancelacion));
            
            $objInfoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                ->find($intIdPersonEmpRolEmpl);
                if(is_object($objInfoPersonaEmpresaRol))
                {
                    $strValorFormaContacto = $emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                ->getValorFormaContactoPorCodigo($objInfoPersonaEmpresaRol
                                                ->getPersonaId(),'MAIL');
                    $strValorFormaContactoAsigna = $emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                ->getValorFormaContactoPorCodigo($objInfoPersonaEmpresa->getPersonaId()->getId(),'MAIL');
                    if(!is_null($strValorFormaContacto))
                    {
                        $strEmailUsrSesion = strtolower($strValorFormaContacto);
                        if(isset($strEmailUsrSesion))
                        {
                            $arrayDestinatarios[] = $strEmailUsrSesion ;
                        }                        
                    } 
                    if(!is_null($strValorFormaContactoAsigna))
                    {
                        $strEmailUsrAsigna = strtolower($strValorFormaContactoAsigna);                      
                    }               
                
                }
                if($objPuntoContactos)
                {
                    foreach($objPuntoContactos as $objPuntoContacto)
                    {
                        if($objPuntoContacto['idFormaContacto'] == "5")
                        {
                            $arrayToCliente[] = $objPuntoContacto['valor'];
                        }
                    }
                }                                                 
            $arrayTo        = array();
            $arrayTo[]      = $strEmailUsrAsigna;
            $arrayParametros["strNombreTarea"]         = $objNombreTarea->getValor1();
            $arrayParametros["strNombreProceso"]       = $objNombreTarea->getValor3();
            $arrayParametros["intIdCaso"]              = null;
            $arrayParametros["intIdPersonaEmpresaRol"] = $objInfoPersonaEmpresa->getId();
            $arrayParametros["arrayTo"]                = $arrayTo;  
            $arrayParametros["strObservacionTarea"]    = $strObservacionCancelacion;
            $arrayParametros["objDetalleHipotesis"]    = $strObservacionCancelacion;
            $arrayParametros["strMotivoTarea"]    = $strObservacionCancelacion;
            
            $arrayParametros["strTipoTarea"]           = $objTarea->getValor7();
            $arrayParametros["strTareaRapida"]         = $objNombreTarea->getValor4();
            $arrayParametros["boolAsignarTarea"]       = true ;
            $arrayParametros["strUserCreacion"]        = $strUsuario;
            $arrayParametros["strIpCreacion"]          = $objRequest->getClientIp();
            $arrayParametros["strTipoAsignacion"]      = $objOrigenTarea->getValor3();
            $arrayParametros["strAplicacion"]          = $objOrigenTarea->getValor1();
            $arrayParametros["intAsignarTareaPersona"] = $intIdPersonEmpRolEmpl;
            $arrayParametros["intIdEmpresa"]           = $intIdEmpresa;
            $arrayParametros["intPuntoId"]             = $entityPuntoDestino->getId();
            $arrayParametros['intFormaContacto']       = intVal($objTarea->getValor5());

            $arrayParametros['strObsAsignaTarea']      =  $objTarea->getValor1();
            $arrayParametros['strObsHistorial']        =  $objTarea->getValor3();
            $arrayParametros['strObsSeguimiento']      =  $objTarea->getValor4();
            $arrayParametros['strAgregaAsunto']        =  $objOrigenTarea->getValor4();

            $arrayRespuesta = $serviceSoporte->crearTareaCasoSoporte($arrayParametros); 

            if($arrayRespuesta['mensaje'] === 'fail')
            {
                throw new \Exception('Error al crear la tarea, por favor comunicar a Sistemas. '.$arrayRespuesta['descripcion']);
            }
            if(!empty($arrayRespuesta['numeroTarea']) && isset($arrayRespuesta['numeroTarea']))
            {

                $serviceProceso->putFile(array('strFileBase64'     => $objConvertDocsResponse['objData']['base64'],
                                                     'strFileName'       => $strCodigoPlantilla,
                                                     'strFileExtension'  => "pdf",
                                                     'intNumeroTarea'    => $arrayRespuesta['numeroTarea'],
                                                     'strOrigen'         => "t",
                                                     'strPrefijoEmpresa' => $strPrefijoEmpresa,
                                                     'strUsuario'        => $strUsuario,
                                                     'strIp'             => $strIpCliente));

                $strTokenIdentificador = date("G") . '' . date('i') . '' . date('s') . substr(md5(uniqid(rand())),0,6);
                $objFile = $strPathPlantilla."plantillaCorreo" . $strTokenIdentificador . ".html.twig";

                $arrayParametrosCorreo =array('cliente' => $strNombresApellidosCompletos,
                            'numeroTarea' => $arrayRespuesta['numeroTarea'],
                            'loginCliente' => $strLoginCancelacion,
                            'direccionCliente' => $strDireccionCliente);
                $strArchivoAdjunto = $strPathPlantilla.$strCodigoPlantilla.".pdf";
                if($objAdmiPlantilla)
                {
                    $strHtml = $objAdmiPlantilla[0]->getPlantilla();

                    $objArchivo = fopen($objFile, "w");
                    if($objArchivo)
                    {
                        try
                        {

                            chmod($objFile, 777);
                            fwrite($objArchivo, $strHtml);
                            fclose($objArchivo);
                            $strPlantillaHtml = $serviceTemplating
                                                ->render('soporteBundle:Default:plantillaCorreo' . $strTokenIdentificador . '.html.twig',
                                                $arrayParametrosCorreo);
                            unlink($objFile);
                        }
                        catch(Exception $e)
                        {
                            unlink($objFile);
                        }

                    }
                    $objArchivoAdjunto = fopen($strArchivoAdjunto, "w");
                    if($objArchivoAdjunto)
                    {
                        try
                        {
                            chmod($strArchivoAdjunto, 777);
                            fwrite($objArchivoAdjunto, base64_decode($objConvertDocsResponse['objData']['base64']));
                            fclose($objArchivoAdjunto);

                            $serviceEnvioMail->enviarCorreo($strAsunto, $arrayToCliente, $strPlantillaHtml, $strArchivoAdjunto);
                            unlink($strArchivoAdjunto);
                        }
                        catch(Exception $e)
                        {
                            unlink($strArchivoAdjunto);
                        }
                     }
                }
                $arrayRespuesta["strStatus"]  = "Ok";
                $arrayRespuesta["strMensaje"] = $objMensajeCancelacion->getValor1()." Tarea: ".$arrayRespuesta["numeroTarea"];
                $objResponse = new Response(json_encode($arrayRespuesta));
                $objResponse->headers->set('Content-type', 'text/json');
                return $objResponse;
            }
            else
            {
                throw new \Exception('Error al crear la tarea, por favor comunicar a Sistemas. '.$arrayRespuesta['descripcion']);
            }

        } 
        catch (\Exception $e) 
        {
            
            $arrayRespuesta["strStatus"]  = "Error";
            $arrayRespuesta["strMensaje"] = "Error al crear la tarea, por favor comunicar a Sistemas.";
            $arrayRespuesta["objData"]    = $arrayParametrosCabCancelacion;
            
            $serviceUtil->insertError('Telcos+',
                                      'ComercialMobileWsControllerRest->enviarTareaRapidaAction',
                                      'ERROR en el login: '.$strLoginCancelacion . ' - ' .$e->getMessage(),
                                      $strUsuario,
                                      $strIpCliente);
            $objResponse = new Response(json_encode($arrayRespuesta));
            $objResponse->headers->set('Content-type', 'text/json');
            return $objResponse;
        }        
    }



    /**
     * @Secure(roles="ROLE_476-1")
     * enviarTareaCancelacionRapidaAction()
     * Función que crea acta cancelacion y envia tarea rapida
     * 
     * @author Ivan Romero <icromero@telconet.ec>
     * @version 1.0 05-12-2021
     * 
     * @author José Candelario <jcandelario@telconet.ec>
     * @version 1.1 06-10-2022 - Se modifica el valor total adeudado que se visualiza en la notificación compuesto por
     *                           valores de deuda + valor equipos + valor instalacion + valor promociones en facturacion 
     *                           + valor promociones en servicios adicionales - subtotal nota de credito. Adicional se 
     *                           agrega login de cancelación en la excepción de error.
     * 
     * @since 1.0
     * 
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.1 21-09-2022 - Se modifica funcionalidad para obtener los descuentos en formato de arreglo. 
     *                         - Se obtienen los equipos seleccionados y el codigoPlantillaCancelacion enviados en el Objeto Request.
     *
     * @author Jonathan Mazon Sanchez <jmazon@telconet.ec>
     * @version 1.2 15-03-2023 Se agrego validacion de plantilla de correo para flujo de ECUANET.
     * 
     * @return $objResponse - envia tarea rapida
     */
    public function enviarTareaCancelacionRapidaAction()
    {   
    
        $arrayRespuesta                = "";
        $strLoginCancelacion           = "";
        $emComercial                   = $this->getDoctrine()->getManager();
        $emComunicacion                = $this->getDoctrine()->getManager('telconet_comunicacion');
        $objServicioRepository         = $emComercial->getRepository('schemaBundle:InfoServicio');
        $serviceSoporte                = $this->get('soporte.SoporteService');
        $serviceProceso                = $this->get('soporte.ProcesoService');
        $servicePlantilla              = $this->get('administracion.Plantilla'); 
        $serviceTokenCas               = $this->get('seguridad.TokenCas');
        $serviceUtil                   = $this->get('schema.Util');
        $emFinanciero                  = $this->get('doctrine')->getManager("telconet_financiero");
        $strPathTelcos                 = $this->container->getParameter('path_telcos');
        $strRutaContrato               = $this->container->getParameter('contrato_digital_ruta');
        $strRutaBase                   = $strPathTelcos . $strRutaContrato;
        $strPathPlantilla              = $strPathTelcos."telcos/src/telconet/soporteBundle/Resources/views/Default/";
        $serviceTemplating             = $this->get('templating');
        $serviceEnvioMail              = $this->get('soporte.EnvioPlantilla');
        $arrayParametrosCabCancelacion = "";
        $objRequest                    = $this->getRequest();
        $objSesion                     = $objRequest->getSession();

        $objPuntoCliente = $objSesion->get('ptoCliente');
        $strEstadoServicosActivo ="Activo";
        $strEstadoServicosIncorte ="In-Corte";
        foreach ($objSesion->get('serviciosPunto') as $objServicio):
            if($objServicio['estado']==$strEstadoServicosActivo||$objServicio['estado']==$strEstadoServicosIncorte)
            {

                $objServiciosPunto[] = array('nombre'=>$objServicio['nombre'],'estado'=>$objServicio['estado']);
            }
        endforeach;
        $objPuntosFacturacion                = $objSesion->get('datosFinancierosPunto');
        $objPuntoContactos                   = $objSesion->get('puntoContactos'); 
        $objClienteContactos                 = $objSesion->get('clienteContactos');
        $objCicloFacturaCliente              = $objSesion->get('cicloFacturacionCliente');
        $objCliente                          = $objSesion->get('cliente');
        $strParametroCabCancelacion          = "FLUJO_ACTA_CANCELACION";
        $strParametroModelos                 = "PARAMETROS_ASOCIADOS_A_SERVICIOS_MD";
        $strParametroEstado                  = "Activo";
        $strModulo                           = "FINANCIERO";
        $strParametroMensajeCancelacion      = "MensajeCancelacion";
        $strParametroNombreTarea             = "NombreTarea";
        $strDescripcionParametroEstados      = 'estadosEquiposCancelacion';
        $strFlujo                            = "Cancelacion";
        $strDescripcionParametroEquipos      = 'equiposFacturacion';
        $strParametroTarea                   = "TareaCancelacion";
        $strParametroMensajes                = "MensajesPredeterminados";
        $strIpCliente                        = $objRequest->getClientIp();
        $strParametroOrigenTarea             = "OrigenTarea";
        $strParametroOrigenComunicacionTarea = "OrigenComunicacionTarea";
        $strParametroClaseTarea              = "ClaseTarea";
        $intIdPersonEmpRolEmpl               = $objSesion->get('idPersonaEmpresaRol');
        $strParametroNombrePlantilla         = "codigoPlantillaCancelacion";
        $arrayToCliente                      = array();
        $strPrefijoEmpresa                   = $objSesion->get('prefijoEmpresa');
        $strPlantillaCorreo                  = ($strPrefijoEmpresa=="EN")?'ACTA_CANCEL_EN':'ACTA_SOL_CANCEL';
        $strValor1Modelos                    = 'MODELOS_EQUIPOS';
        $strBanderaEquipoExtender            = "EXTENDER DUAL BAND";
        $strFechaActual                      = date_format(new \DateTime('now'), 'Y-m-d');
        $strBanderaEquipo                    = "";
        $strIdPersonaEmpresaRol              = $objCliente['id_persona_empresa_rol'];
        $objContratoActivo                   = $emComercial->getRepository('schemaBundle:InfoContrato')
                                                           ->findBy(array("personaEmpresaRolId" => $strIdPersonaEmpresaRol,
                                                                          "estado" => array('PorAutorizar', $strParametroEstado,'Cancelado')));
        $strContratoId                       = $objContratoActivo[0]->getNumeroContrato();
        $intIdEmpresa                        = $objSesion->get('idEmpresa');
        $strUsuario                          = $objSesion->get('user');
        $objPuntoContactos                   = $objSesion->get('puntoContactos'); 
              
        $strValorEquipos                     = $objRequest->get('floatEquipos');
        
        if($strValorEquipos > 0)
        {
            $strEntregaEquipos = "NO";
        }
        else
        {
            $strEntregaEquipos = "SI";
        }
        
        try 
        {
                          
            //obtener mensaje de error parametrizado
            $arrayParametrosCabCancelacion = $emComercial->getRepository('schemaBundle:AdmiParametroCab')
                                            ->findOneBy(array('nombreParametro'  => $strParametroCabCancelacion,
                                                              'estado'           => $strParametroEstado,
                                                              'modulo'           => $strModulo));
            //obtener mensaje de error parametrizado
            $arrayParametrosModeloEquipos = $emComercial->getRepository('schemaBundle:AdmiParametroCab')
                                    ->findOneBy(array('nombreParametro'  => $strParametroModelos,
                                                      'estado'           => $strParametroEstado));

            $arrayParametrosDetCancelacion = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                            ->findOneBy(array('parametroId'  => $arrayParametrosCabCancelacion->getId(),
                                                              'estado'       => $strParametroEstado,
                                                              'empresaCod'   => $intIdEmpresa,
                                                              'descripcion'  => $strDescripcionParametroEstados,
                                                              'valor2'       => $strFlujo));

            $objEstadoEquiposCancelacion = explode("|",$arrayParametrosDetCancelacion->getValor1());
            $arrayParametrosDetFactura = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                            ->findOneBy(array('parametroId'  => $arrayParametrosCabCancelacion->getId(),
                                                              'estado'       => $strParametroEstado,
                                                              'empresaCod'   => $intIdEmpresa,
                                                              'descripcion'  => $strDescripcionParametroEquipos,
                                                              'valor2'       => $strFlujo));
            
            $objNombreTarea = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                            ->findOneBy(array('parametroId'  => $arrayParametrosCabCancelacion->getId(),
                                                              'estado'       => $strParametroEstado,
                                                              'empresaCod'   => $intIdEmpresa,
                                                              'descripcion'  => $strParametroNombreTarea,
                                                              'valor2'       => $strFlujo));

            $objMensajeCancelacion = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                            ->findOneBy(array('parametroId'  => $arrayParametrosCabCancelacion->getId(),
                                                              'estado'       => $strParametroEstado,
                                                              'empresaCod'   => $intIdEmpresa,
                                                              'descripcion'  => $strParametroMensajeCancelacion,
                                                              'valor2'       => $strFlujo));

            $objOrigenTarea = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                            ->findOneBy(array('parametroId'  => $arrayParametrosCabCancelacion->getId(),
                                                              'estado'       => $strParametroEstado,
                                                              'empresaCod'   => $intIdEmpresa,
                                                              'descripcion'  => $strParametroOrigenTarea,
                                                              'valor2'       => $strFlujo));  
                                                                
            $objOrigenComunicacionTarea = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                            ->findOneBy(array('parametroId'  => $arrayParametrosCabCancelacion->getId(),
                                                              'estado'       => $strParametroEstado,
                                                              'empresaCod'   => $intIdEmpresa,
                                                              'descripcion'  => $strParametroOrigenComunicacionTarea,
                                                              'valor2'       => $strFlujo));  

            $objClaseTarea = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                            ->findOneBy(array('parametroId'  => $arrayParametrosCabCancelacion->getId(),
                                                              'estado'       => $strParametroEstado,
                                                              'empresaCod'   => $intIdEmpresa,
                                                              'descripcion'  => $strParametroClaseTarea,
                                                              'valor2'       => $strFlujo)); 

            $objTarea = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                            ->findOneBy(array('parametroId'  => $arrayParametrosCabCancelacion->getId(),
                                                              'estado'       => $strParametroEstado,
                                                              'empresaCod'   => $intIdEmpresa,
                                                              'descripcion'  => $strParametroTarea,
                                                              'valor2'       => $strFlujo)); 
            
            $objMensajesPredeterminados = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                            ->findOneBy(array('parametroId'  => $arrayParametrosCabCancelacion->getId(),
                                                              'estado'       => $strParametroEstado,
                                                              'empresaCod'   => $intIdEmpresa,
                                                              'descripcion'  => $strParametroMensajes,
                                                              'valor2'       => $strFlujo)); 
            
            $arrayParamsServicioInternet    = array('estadosServicios'          => array($strEstadoServicosActivo,$strEstadoServicosIncorte),
                                                    'productoInternetPorLogin'  => 'S',
                                                    'estadoActivo'              => 'Activo',
                                                    'empresaCod'                => $intIdEmpresa,
                                                    'intIdPuntoCliente'         => $objPuntoCliente['id'],
                                                    'nombreTecnicoProducto'     => $arrayParametrosDetCancelacion->getValor3(),
                                                    'omiteEstadoPunto'          => "SI");
            $objServicioPunto = $emComercial->getRepository('schemaBundle:InfoServicio')
                                ->getServiciosByCriterios($arrayParamsServicioInternet)['registros'][0]; 

            $intIdServicio    = $objServicioPunto->getId();

            $objDataTecnica   = $this->get('tecnico.DataTecnica');
                        
            $arrayParametrosDataTecnica = array('idServicio'    => $intIdServicio,
                                                'idEmpresa'     => $intIdEmpresa,
                                                'prefijoEmpresa'=> $strPrefijoEmpresa);

            $arrayDatosTecnicos = $objDataTecnica->getDataTecnica($arrayParametrosDataTecnica);

            $objElementoCpe                 = $arrayDatosTecnicos['elementoCpe'];
            $objElementoCliente             = $arrayDatosTecnicos['elementoCliente'];
            $objMacCliente                  = $arrayDatosTecnicos['macCliente'];
            $arrayElementosExtenderDualBand = $arrayDatosTecnicos['arrayElementosExtenderDualBand'];

            if($objElementoCliente)
            {
                $arrayParametrosDetModelos = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                ->findOneBy(array('parametroId'    => $arrayParametrosModeloEquipos->getId(),
                                    'estado'       => $strParametroEstado,
                                    'empresaCod'   => $intIdEmpresa,
                                    'valor1'       => $strValor1Modelos,
                                    'valor5'       => $objElementoCliente->getModeloElementoId()->getNombreModeloElemento()));
            }
            else if($objElementoCpe)
            {
                $arrayParametrosDetModelos = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                ->findOneBy(array('parametroId'  => $arrayParametrosModeloEquipos->getId(),
                                    'estado'       => $strParametroEstado,
                                    'empresaCod'       => $intIdEmpresa,
                                    'valor1'       => $strValor1Modelos,
                                    'valor5'       => $objElementoCpe->getModeloElementoId()->getNombreModeloElemento()));
            }
            if($arrayParametrosDetModelos)
            {
                $strBanderaEquipo = $arrayParametrosDetModelos->getValor4() =="WIFI DUAL BAND"? $arrayParametrosDetModelos->getValor4():"CPE ONT";
            
            }else
            {
                $strBanderaEquipo = "CPE ONT";
            }
            
            $intEmpresaId           = $objSesion->get('idEmpresa'); 
            $arrayPtoSession        = $objSesion->get('ptoCliente');
            $serviceSolicitudes     = $this->get('comercial.Solicitudes');    

            $strIdServicio = strval($intIdServicio);                                  
            $arrayParametros2                      = array();
            $arrayParametros2['intIdServicio']     = $strIdServicio;
            $arrayParametros2['strEmpresaCod']     = $intEmpresaId;
            $arrayParametros2['intPtoSessionId']   = $arrayPtoSession['id'];
            $arrayParametros2['descripProducto']   = $arrayParametrosDetCancelacion->getValor3();        
            $arrayParametros2['emFinanciero']      = $emFinanciero;  
            $arrayParametros2['strParametro']      = $arrayParametrosDetFactura->getValor1();
            $arrayParametros2['strDescripcion']    = $arrayParametrosDetFactura->getValor3();
            $arrayParametros2['strModulo']         = $arrayParametrosDetFactura->getValor4();
            
            $objValoresFacturarJson   = $emComercial->getRepository('schemaBundle:InfoServicioTecnico')->getJsonValoresFacturar($arrayParametros2);

            $objValoresFacturar = json_decode($objValoresFacturarJson);                                      
            $objValoresFacturar = json_decode(json_encode($objValoresFacturar), true);
            $intIdServicio          = $objServicioPunto->getId();
            $intEmpresaId           = $objSesion->get('idEmpresa'); 
            $arrayPtoSession        = $objSesion->get('ptoCliente');
            $serviceSolicitudes     = $this->get('comercial.Solicitudes');
    
            $arrayParametros                           = array();
            $arrayParametros['intServicioId']          = $intIdServicio;          
            $arrayParametros['strEmpresaCod']          = $intEmpresaId;
            $arrayParametros['serviceSolicitud']       = $serviceSolicitudes;  
            $arrayParametros['intPtoSessionId']        = $arrayPtoSession['id'];
            $arrayParametros['descripcionProducto']    = $arrayParametrosDetCancelacion->getValor3(); 
            $arrayParametros['strParametro']           = $arrayParametrosDetFactura->getValor1();
            $arrayParametros['strProceso']             = $arrayParametrosDetFactura->getValor3();
            $arrayParametros['strModulo']              = $arrayParametrosDetFactura->getValor4();

            
            if($strEntregaEquipos=='NO')
            {
                $arrayEquiposListSelect = explode(",",$objRequest->get('equiposSeleccionados'));
                
                foreach($arrayEquiposListSelect as $strEquipoSeleccionado):
                    
                    if(!empty($strEquipoSeleccionado) || $strEquipoSeleccionado != '' || $strEquipoSeleccionado != null )
                    {
                        $objEquipos[] = array('descripcion' => $strEquipoSeleccionado,
                                              'estados'     => $objEstadoEquiposCancelacion,
                                              'estado'      => "Dañado o No Entrega",
                                              'serie'       => $arrayParametrosDetCancelacion->getValor4(),
                                              'mac'         => $arrayParametrosDetCancelacion->getValor4(),
                                             );                    
                    }
                                   
                endforeach;
            }
            else
            {
                $objEquipos = array();
                $arrayParamEquipo                           = array();   
                $arrayParamEquipo['strEmpresaCod']          = $intEmpresaId;
                $arrayParamEquipo['serviceSolicitud']       = $serviceSolicitudes;  
                $arrayParamEquipo['intPtoSessionId']        = $arrayPtoSession['id'];
                $arrayParamEquipo['strParametro']           = 'RETIRO_EQUIPOS_SOPORTE';
                $arrayParamEquipo['strProceso']             = 'FACTURACION_RETIRO_EQUIPOS';
                $arrayParamEquipo['strModulo']              = 'FINANCIERO';               
                
                $arrayServiciosDctos       = json_decode($objRequest->get('arrayGeneralDescuentos'),true);
                        
                foreach ($arrayServiciosDctos as $arrayServicioDcto):
                    
                    $arrayParamEquipo['intServicioId']       = $arrayServicioDcto['idServicio']; 
                    $arrayParamEquipo['descripcionProducto'] = $arrayServicioDcto['nombreProducto']; 
                                                  
                    $objEquiposJson  = $emComercial->getRepository('schemaBundle:InfoServicioTecnico')->getJsonEquiposFacturar($arrayParamEquipo);
                    $objEquiposTemp  = json_decode($objEquiposJson);
                    $objEquiposList  = $objEquiposTemp->equipos;
                                        
                    foreach ($objEquiposList as $objEquipo): 
                        
                        if(!empty($objEquipo->descripcion) )
                        {
                            $arrayTemporal = array('descripcion'  => $objEquipo->descripcion,
                                                   'estados'      => $objEstadoEquiposCancelacion,
                                                   'estado'       => "Bueno",
                                                   'serie'        => $arrayParametrosDetCancelacion->getValor4(),
                                                   'mac'          => $arrayParametrosDetCancelacion->getValor4(),
                                                  );
                                
                            array_push($objEquipos,$arrayTemporal);
                           
                        }
                    endforeach;

                endforeach;   
            }
                            
            $objEquiposTemp2 = $objEquipos;
            $objEquipos = array();
            foreach ($objEquiposTemp2 as $objEquipo):
                if($objEquipo['descripcion']==$strBanderaEquipo)
                {

                    $objEquipos[] = array( 
                    'descripcion' => $objEquipo['descripcion'],
                    'estados'     => $objEquipo['estados'],
                    'estado'      => $objEquipo['estado'],
                    'serie'       => $objElementoCliente->getSerieFisica(),
                    'mac'         => $objMacCliente?$objMacCliente->getValor():$arrayParametrosDetCancelacion->getValor4(),
                    );
                }
                else if($objEquipo['descripcion']==$strBanderaEquipoExtender)
                {
                  if($arrayElementosExtenderDualBand)
                  {
                    foreach ($arrayElementosExtenderDualBand as $objExtender):
                        $objEquipos[] = array( 
                        'descripcion' => $objEquipo['descripcion'],
                        'estados'     => $objEquipo['estados'],
                        'estado'      => $objEquipo['estado'],
                        'serie'       => $objExtender->getSerieFisica(),
                        'mac'         => $objExtender->getMacElemento(),
                        );
                    endforeach;
                  }else
                  {
                    $objEquipos[] = array( 
                    'descripcion' => $objEquipo['descripcion'],
                    'estados'     => $objEquipo['estados'],
                    'estado'      => $objEquipo['estado'],
                    'serie'       => $arrayParametrosDetCancelacion->getValor4(),
                    'mac'         => $arrayParametrosDetCancelacion->getValor4(),
                    );
                  }
                    
                }
                else
                {

                    $objEquipos[] = array(  
                    'descripcion' => $objEquipo['descripcion'],
                    'estados'     => $objEquipo['estados'],
                    'estado'      => $objEquipo['estado'],
                    'serie'       => $arrayParametrosDetCancelacion->getValor4(),
                    'mac'         => $arrayParametrosDetCancelacion->getValor4(),
                    );
                }
            endforeach;


            $strNombresCliente            = $objCliente['nombres']?$objCliente['nombres']:$objCliente['razon_social'];
            $strContratoCliente           = $strContratoId;
            $strCategoriaCliente          = $objPuntoCliente['tipo_negocio'];
            $strLoginCancelacion          = $objPuntoCliente['login'];
            $strServicioCliente           = $objServiciosPunto[0]['nombre'];
            $strDeudaCliente              = $objPuntosFacturacion['saldoCliente'];
            $strDireccionCliente          = $objPuntoCliente['direccion'];
            $strMotivoCliente             = $objRequest->get('motivoCancelacionText');
            $strNombresApellidosCompletos = $objCliente['nombres']?$objCliente['nombres']." ".$objCliente['apellidos']:$objCliente['razon_social'];
            $strObservacionCancelacion    = $objRequest->get('strObservacionesCancelacion');
            $strOficinaCliente            = $objCliente['nombre_oficina'];
            $strFechaActualCliente        = $strFechaActual;            
            $strValorInstalacion          = $objRequest->get('floatInstalacion');
            $strFloatSubtotal             = $objRequest->get('floatSubtotal');
            $strFloatSubtotalnc           = $objRequest->get('floatSubtotalnc');
            $strIdentificacion            = $objCliente['identificacion'];
            $strFechaVigencia             = $objValoresFacturar['fechaActivacion'];
            $arrayGeneralDescuentos       = json_decode($objRequest->get('arrayGeneralDescuentos'),true);
                        
            foreach ($arrayGeneralDescuentos as $arrayDescuento):
                        $objDescuentos[] = array('idServicio' => $arrayDescuento['idServicio'],
                                        'idProducto'          => $arrayDescuento['idProducto'],
                                        'nombreProducto'      => $arrayDescuento['nombreProducto'],
                                        'valorPromociones'    => number_format($arrayDescuento['descPromo']+$arrayDescuento['descPromoAdicional'],2),
                                        'porDescInstNC'       => $arrayDescuento['porDescInstNC'],
                                        'porDescPromoNC'      => $arrayDescuento['porDescPromoNC'],
                                        'fechaActivacionProd' => $arrayDescuento['fechaActivacionProd']    
                                        );
           
            endforeach;
            
            $arrayGeneralProdFacturar = json_decode($objRequest->get('arrayGeneralProdFacturar'),true);
            
            foreach ($arrayGeneralProdFacturar as $arrayProdFacturar):
                        $objProdFacturar[] = array('idServicio' => $arrayProdFacturar['idServicio'],
                                          'nombreProducto'      => $arrayProdFacturar['nombreProducto'],
                                          'valorFacturar'       => number_format($arrayProdFacturar['valorFacturar'],2)    
                                          );
           
            endforeach;            
            
            $objAdmiPlantilla = $emComunicacion->getRepository('schemaBundle:AdmiPlantilla')
                                            ->getPlantillaXCodigoYEmpresa($strPlantillaCorreo);

            $strAsunto = $objTarea->getValor6();

            $arrayTokenCas = $serviceTokenCas->generarTokenCas();

            $strCodigoPlantilla = $objRequest->get('codigoPlantillaCancelacion');
            
            $objContrato  = array('clave'   => 'valorSubtotalNc',
                                  'valor'   => $strFloatSubtotalnc);
            $arrayContrato[] = $objContrato;
            $objContrato  = array('clave'   => 'valorSubtotalFactura',
                                  'valor'   => round(floatval($strFloatSubtotal),2)); 
            $arrayContrato[] = $objContrato;
            $objContrato  = array('clave'   => 'login',
                                  'valor'   => $strLoginCancelacion);
            $arrayContrato[] = $objContrato;
            $objContrato  = array('clave'   => 'cedula',
                                  'valor'   => $strIdentificacion);
            $arrayContrato[] = $objContrato;
            $objContrato  = array('clave'   => 'nombrePersona',
                                  'valor'   => $strNombresCliente);
            $arrayContrato[] = $objContrato;
            $objContrato  = array('clave'   => 'numeroContrato',
                                  'valor'   => $strContratoCliente);
            $arrayContrato[] = $objContrato;
            $objContrato  = array('clave'   => 'categoria',
                                  'valor'   => $strCategoriaCliente);
            $arrayContrato[] = $objContrato;
            $objContrato  = array('clave'   => 'direccionPunto',
                                  'valor'   => $strDireccionCliente);
            $arrayContrato[] = $objContrato;
            $objContrato  = array('clave'   => 'servicios',
                                  'valor'   => $strServicioCliente);
            $arrayContrato[] = $objContrato;
            $objContrato  = array('clave'   => 'valorDeuda',
                                  'valor'   => $strDeudaCliente);
            $arrayContrato[] = $objContrato;
            $objContrato  = array('clave'   => 'motivoCancelacion',
                                  'valor'   => $strMotivoCliente);
            $arrayContrato[] = $objContrato;
            $objContrato  = array('clave'   => 'observaciones',
                                  'valor'   => $strObservacionCancelacion);
            $arrayContrato[] = $objContrato;
            $objContrato  = array('clave'   => 'nombreCompleto',
                                  'valor'   => $strNombresApellidosCompletos);
            $arrayContrato[] = $objContrato;
            $objContrato  = array('clave'   => 'oficina',
                                  'valor'   => $strOficinaCliente);
            $arrayContrato[] = $objContrato;
            $objContrato  = array('clave'   => 'fechaActual',
                                  'valor'   => $strFechaActualCliente);
            $arrayContrato[] = $objContrato;
            $objContrato  = array('clave'   => 'fechaVigencia',
                                  'valor'   => $strFechaVigencia);
            $arrayContrato[] = $objContrato;
            $objContrato  = array('clave'   => 'valorEquipos',         
                                  'valor'   => round(floatval($strValorEquipos),2));
            $arrayContrato[] = $objContrato;
            $objContrato  = array('clave'   => 'valorInstalacion',
                                  'valor'   => $strValorInstalacion);
            $arrayContrato[] = $objContrato;
            $objContrato  = array('clave'   => 'valorPromociones',
                                  'valor'   => $objDescuentos);
            $arrayContrato[] = $objContrato;
            $objContrato  = array('clave'   => 'entregaEquipo',
                                  'valor'   => $strEntregaEquipos);
            $arrayContrato[] = $objContrato;
            $objContrato  = array('clave'   => 'equipos',
                                  'valor'   => $objEquipos);
            $arrayContrato[] = $objContrato;
            $objContrato  = array('clave'   => 'masIva',
                                  'valor'   => $objMensajesPredeterminados->getValor1());
            $arrayContrato[] = $objContrato;
            $objContrato  = array('clave'   => 'valoresFactProd',
                                  'valor'   => $objProdFacturar);            
                        
            $arrayContrato[] = $objContrato;
            
            $arrayParametros =array('token'           => $arrayTokenCas['strToken'],
                                    'codigoPlantilla' => $strCodigoPlantilla,
                                    'propiedades'     => $arrayContrato,
                                    'login'           => $strLoginCancelacion,
                                    'strIpCliente'    => $strIpCliente);

            $objPlantillasResponse = $servicePlantilla->usarPlantillaMs($arrayParametros);

            $arrayParametros['html'] = $objPlantillasResponse['objData']['resultadoTemplate'];
            $objConvertDocsResponse = $servicePlantilla->convertDocsMs($arrayParametros);

            $entityPuntoDestino = $emComercial->getRepository('schemaBundle:InfoPunto')
                                                 ->findOneBy(array('login'  => $strLoginCancelacion));
            
            $objInfoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                ->find($intIdPersonEmpRolEmpl);
                if(is_object($objInfoPersonaEmpresaRol))
                {
                    $strValorFormaContacto = $emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                ->getValorFormaContactoPorCodigo($objInfoPersonaEmpresaRol
                                                ->getPersonaId(),'MAIL');
                    if(!is_null($strValorFormaContacto))
                    {
                        $strEmailUsrSesion = strtolower($strValorFormaContacto);
                        if(isset($strEmailUsrSesion))
                        {
                            $arrayDestinatarios[] = $strEmailUsrSesion ;
                        }                        
                    }                
                
                }
                if($objPuntoContactos)
                {
                    foreach($objPuntoContactos as $objPuntoContacto)
                    {
                        if($objPuntoContacto['idFormaContacto'] == "5")
                        {
                            $arrayToCliente[] = $objPuntoContacto['valor'];
                        }
                    }
                }                                                 
            $arrayTo        = array();
            $arrayTo[]      = $strEmailUsrSesion;
            $arrayParametros["strNombreTarea"]         = $objNombreTarea->getValor1();
            $arrayParametros["strNombreProceso"]       = $objNombreTarea->getValor3();
            $arrayParametros["intIdCaso"]              = null;
            $arrayParametros["intIdPersonaEmpresaRol"] = $intIdPersonEmpRolEmpl;
            $arrayParametros["arrayTo"]                = $arrayTo;  
            $arrayParametros["strObservacionTarea"]    = $objMensajesPredeterminados->getValor3()." ".$objRequest->get('motivoCancelacionText')." - ".
                                                         $objRequest->get('strObservacionesCancelacion').". ".
                                                         $objMensajesPredeterminados->getValor4();
            $arrayParametros["objDetalleHipotesis"]    = $objMensajesPredeterminados->getValor3()." ".$objRequest->get('motivoCancelacionText')." - ".
                                                         $objRequest->get('strObservacionesCancelacion').". ".
                                                         $objMensajesPredeterminados->getValor4();
            $arrayParametros["strMotivoTarea"]    = $strObservacionCancelacion;
            
            $arrayParametros["strTipoTarea"]           = $objTarea->getValor7();
            $arrayParametros["strTareaRapida"]         = $objNombreTarea->getValor4();
            $arrayParametros["boolAsignarTarea"]       = true ;
            $arrayParametros["strUserCreacion"]        = $strUsuario;
            $arrayParametros["strIpCreacion"]          = $objRequest->getClientIp();
            $arrayParametros["strTipoAsignacion"]      = $objOrigenTarea->getValor3();
            $arrayParametros["strAplicacion"]          = $objOrigenTarea->getValor1();
            $arrayParametros["intAsignarTareaPersona"] = $intIdPersonEmpRolEmpl;
            $arrayParametros["intIdEmpresa"]           = $intIdEmpresa;
            $arrayParametros["intPuntoId"]             = $entityPuntoDestino->getId();
            $arrayParametros['intFormaContacto']       = intVal($objTarea->getValor5());

                $arrayParametros['strObsAsignaTarea']      =  $objTarea->getValor1();
                $arrayParametros['strObsHistorial']        =  $objTarea->getValor3();
                $arrayParametros['strObsSeguimiento']      =  $objTarea->getValor4();
            
            $arrayRespuesta = $serviceSoporte->crearTareaCasoSoporte($arrayParametros); 

            if($arrayRespuesta['mensaje'] === 'fail')
                {
                    throw new \Exception('Error al crear la tarea, por favor comunicar a Sistemas.');
                }
                if(!empty($arrayRespuesta['numeroTarea']) && isset($arrayRespuesta['numeroTarea']))
                {
                    
                        $serviceProceso->putFile(array('strFileBase64'     => $objConvertDocsResponse['objData']['base64'],
                                                       'strFileName'       => $strCodigoPlantilla,
                                                       'strFileExtension'  => "pdf",
                                                       'intNumeroTarea'    => $arrayRespuesta['numeroTarea'],
                                                       'strOrigen'         => "t",
                                                       'strPrefijoEmpresa' => $strPrefijoEmpresa,
                                                       'strUsuario'        => $strUsuario,
                                                       'strIp'             => $strIpCliente));

                        $strTokenIdentificador = date("G") . '' . date('i') . '' . date('s') . substr(md5(uniqid(rand())),0,6);

                        $objFile               = $strPathPlantilla."plantillaCorreo" . $strTokenIdentificador . ".html.twig";
                        $strFloatValortotal    = round(floatval($strDeudaCliente)+floatval($strFloatSubtotal)-floatval($strFloatSubtotalnc),2);
                        $arrayParametrosCorreo = array('cliente'           => $strNombresApellidosCompletos,
                                                       'valorTotalPagal'   => $strFloatValortotal,
                                                       'numeroTarea'       => $arrayRespuesta['numeroTarea'],
                                                       'loginCliente'      => $strLoginCancelacion,
                                                       'direccionCliente'  => $strDireccionCliente);
                        $strArchivoAdjunto     = $strPathPlantilla.$strCodigoPlantilla.".pdf";

                        if($objAdmiPlantilla)
                        {
                            $strHtml = $objAdmiPlantilla[0]->getPlantilla();

                            $objArchivo = fopen($objFile, "w");
                            if($objArchivo)
                            {
                                try
                                {

                                    chmod($objFile, 777);
                                    fwrite($objArchivo, $strHtml);
                                    fclose($objArchivo);
                                    $strPlantillaHtml = $serviceTemplating
                                                        ->render('soporteBundle:Default:plantillaCorreo' . $strTokenIdentificador . '.html.twig',
                                                        $arrayParametrosCorreo);
                                    unlink($objFile);
                                }
                                catch(Exception $e)
                                {
                                    unlink($objFile);
                                }

                            }
                            $objArchivoAdjunto = fopen($strArchivoAdjunto, "w");
                            if($objArchivoAdjunto)
                            {
                                try
                                { 
                                    chmod($strArchivoAdjunto, 777);
                                    fwrite($objArchivoAdjunto, base64_decode($objConvertDocsResponse['objData']['base64']));
                                    fclose($objArchivoAdjunto);

                                    $serviceEnvioMail->enviarCorreo($strAsunto, $arrayToCliente, $strPlantillaHtml, $strArchivoAdjunto);
                                    unlink($strArchivoAdjunto);
                                }
                                catch(Exception $e)
                                {
                                    unlink($strArchivoAdjunto);
                                }
                             }
                        }
                }                                                   
            
        } 
        catch (\Exception $e) 
        {
            error_log('catch ERROR');
            
            $arrayRespuesta["strStatus"]  = "Error";
            $arrayRespuesta["strMensaje"] = $objMensajeCancelacion->getValor1();
            $arrayRespuesta["objData"] = $arrayParametrosCabCancelacion;
            
            $serviceUtil->insertError('Telcos+',
                                      'ComercialMobileWsControllerRest->enviarTareaCancelacionRapidaAction',
                                      'ERROR en el login: '.$strLoginCancelacion . ' - ' .$e->getMessage(),
                                      $strUsuario,
                                      $strIpCliente);
            
        }
        
        $arrayRespuesta["strStatus"]  = "Ok";
        $arrayRespuesta["strMensaje"] = $objMensajeCancelacion->getValor1()." Tarea: ".$arrayRespuesta["numeroTarea"];
        $objResponse = new Response(json_encode($arrayRespuesta));
        $objResponse->headers->set('Content-type', 'text/json');
       return $objResponse;
    }

    /**
     * Permite obtener los para la cancelacion por empresa
     * @author Edgar Holguín <icromero@telconet.ec>
     * @version 1.0 30-12-2021
     * @return $jsonResponse
     */
    public function ajaxGetParametrosCancelacionAction()
    {
        $objRequest         = $this->getRequest();
        $objSession         = $objRequest->getSession();
        $emGeneral          = $this->getDoctrine()->getManager("telconet_general");
        
        $arrayParametros                          = array();
        $arrayParametros['strEmpresaCod']         = $objSession->get('idEmpresa');
        $arrayParametros['strNombreParametroCab'] = 'FLUJO_ACTA_CANCELACION';
        $arrayParametros['valor2']                = 'Cancelacion'; 
        $arrayParametros['estado']                = 'Activo'; 

        
        $objUsers = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->getJSONParametrosByCriterios($arrayParametros);  
        
        $objJsonResponse      = new JsonResponse();
        $objJsonResponse->setContent($objUsers);
        return $objJsonResponse;
    }
    
    
}

<?php
namespace telconet\comercialBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


/**
 * 
 * Ver Logs Documentos controller.
 *
 * Controlador que se encargará de la información de los logs de consultas de información de cliente.
 *
 * @author Edgar Holguin <eholguin@telconet.ec>
 * @version 1.0 05-01-2022
 */
class InfoLogsController extends Controller
{

     /**
     *
     * Documentación para el método 'indexAction'.
     *
     * Muestra la pantalla inicial de consulta de logs.
     *
     * @return Response 
     *
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.0 05-01-2022
     */
    public function indexAction()
    {
        return $this->render( 'comercialBundle:infoLogs:index.html.twig');
    }

     /**
     * gridInfoLogsAction()
     * Función que obtiene un listado de logs según los filtros enviados como parámetros..
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 05-01-2022
     * @since 1.0
     *
     * @return $objResponse - Listado de Logs
     */
    public function gridInfoLogsAction()
    {
        $serviceInfoLog         = $this->get('comercial.InfoLog');
        $serviceTokenCas        = $this->get('seguridad.TokenCas');
        $objRequest             = $this->get('request');
        $strIpCreacion          = $objRequest->getClientIp();
        $objSession             = $objRequest->getSession();        
        $strUsrCreacion         = $objSession->get('user');
        $strCodEmpresa          = $objSession->get('idEmpresa'); 
        $emGeneral              = $this->getDoctrine()->getManager('telconet_general');
        $strFiltroFechaDesde    = $objRequest->get('strFechaDesde') ? $objRequest->get('strFechaDesde') : "";
        $strFiltroFechaHasta    = $objRequest->get('strFechaHasta') ? $objRequest->get('strFechaHasta') : "";
        $strFiltroMetodo        = $objRequest->get('strMetodo') ? $objRequest->get('strMetodo') : '';          
        $strOrigen              = '';
        $strMetodo              = '';
        $strHoraIni             = '';
        $strHoraFin             = '';
        
        $objAdmiParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                        ->findOneBy(array('nombreParametro' => 'VISUALIZACION LOGS', 
                                                          'estado'          => 'Activo'));
        if(is_object($objAdmiParametroCab))
        {              
            $objParamDetOrigen = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                             ->findOneBy(array('parametroId' => $objAdmiParametroCab,
                                                               'descripcion' => 'ORIGEN',
                                                               'empresaCod'  => $strCodEmpresa,
                                                               'estado'      => 'Activo'));
            
            $objParamDetMetodo = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                             ->findOneBy(array('parametroId'     => $objAdmiParametroCab,
                                                               'observacion'     => 'VER LOGS',
                                                               'empresaCod'      => $strCodEmpresa,
                                                               'estado'          => 'Activo')); 
            
            $objAdmiParamDetRango = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                             ->findOneBy(array('parametroId' => $objAdmiParametroCab,
                                                               'descripcion' => 'RANGO CONSULTA',
                                                               'empresaCod'  => $strCodEmpresa,
                                                               'estado'      => 'Activo'));
            if(is_object($objAdmiParamDetRango))
            {
                $strHoraIni  = $objAdmiParamDetRango->getValor2();
                $strHoraFin  = $objAdmiParamDetRango->getValor3();
            }
            
            if(is_object($objParamDetOrigen))
            {
                $strOrigen  = $objParamDetOrigen->getValor1();
            }
            
            if(is_object($objParamDetMetodo))
            {
                $strMetodo  = $objParamDetMetodo->getValor1();
            }             
        }         

        $arrayParametrosLog                   = array();
        $arrayParametrosLog['strOrigen']      = $strOrigen;
        $arrayParametrosLog['strMetodo']      = $strMetodo;
        $arrayParametrosLog['strTipoEvento']  = 'INFO';
        $arrayParametrosLog['strIpUltMod']    = $strIpCreacion;
        $arrayParametrosLog['strUsrUltMod']   = $strUsrCreacion;
        $arrayParametrosLog['dateFechaEvento']= date("Y-m-d h:i:s");
        $arrayParametrosLog['strIdKafka']     = '';

        
        $arrayTokenCas               = $serviceTokenCas->generarTokenCas();
        $arrayParametrosLog['token'] = $arrayTokenCas['strToken'];
        $serviceInfoLog->registrarLogsMs($arrayParametrosLog);       
        
        $arrayLogs     = array();
 
        $arrayParametros                  = array();
        $arrayParametros['token']         = $arrayTokenCas['strToken'];
        $arrayParametros['origen']        = $strOrigen;
        $arrayParametros['metodo']        = $strFiltroMetodo;        
        $arrayParametros['tipoEvento']    = 'INFO';
        $arrayParametros['ipEvento']      = ''; 
        $arrayParametros['usuarioEvento'] = '';
        $arrayParametros['fechaDesde']    = $strFiltroFechaDesde.' '.$strHoraIni; 
        $arrayParametros['fechaHasta']    = $strFiltroFechaHasta.' '.$strHoraFin;
        $arrayParametros['idKafka']       = '';        
        $arrayParametros['strIpUltMod']   = $strIpCreacion;         
        
        $arrayInfoLogsResponse = $serviceInfoLog->consultarLogsMs($arrayParametros);
        
        foreach($arrayInfoLogsResponse['objData'] as $arrayInfoLog)
        {
            $strCliente        = '';
            $strIdentificacion = '';
            $strLoginPto       = '';
            if(!empty($arrayInfoLog['request']))
            {   
                $arrayLogs[]    = array('strOrigen'              => $arrayInfoLog['origen'],
                                        'strMetodo'              => $arrayInfoLog['metodo'],
                                        'strNombre'              => $arrayInfoLog['request']['nombres'],
                                        'strApellido'            => $arrayInfoLog['request']['apellidos'],
                                        'strRazonSocial'         => $arrayInfoLog['request']['razon_social'],
                                        'strIdentificacion'      => $arrayInfoLog['request']['identificacion'],
                                        'strTipoIdentificacion'  => $arrayInfoLog['request']['tipoIdentificacion'],
                                        'strTipoTributario'      => $arrayInfoLog['request']['tipoTributario'],                
                                        'strLoginPto'            => $arrayInfoLog['request']['login'],
                                        'strUsrEvento'           => $arrayInfoLog['usuarioEvento'],
                                        'strFechaEvento'         => $arrayInfoLog['fechaEvento'],
                                        'strIpEvento'            => $arrayInfoLog['ipEvento']
                                        );
            }
            else
            {
                $arrayLogs[]    = array('strOrigen'              => $arrayInfoLog['origen'],
                                        'strMetodo'              => $arrayInfoLog['metodo'],
                                        'strNombre'              => '',
                                        'strApellido'            => '',
                                        'strRazonSocial'         => '',
                                        'strIdentificacion'      => '',
                                        'strTipoIdentificacion'  => '',
                                        'strTipoTributario'      => '',                
                                        'strLoginPto'            => '',
                                        'strUsrEvento'           => $arrayInfoLog['usuarioEvento'],
                                        'strFechaEvento'         => $arrayInfoLog['fechaEvento'],
                                        'strIpEvento'            => $arrayInfoLog['ipEvento']
                                        );                
            }
        }
        

        $objResponse = new Response(json_encode(array( 'data' => $arrayLogs)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    /**
    * getParametroRangoFechasAction Función que valida si el código promocional ingresado es único.
    * 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.0 13-01-2023
    * 
    * @return JsonResponse
    */
    public function getParametroRangoFechasAction()
    {
        $objRequest     = $this->getRequest();
        $objSession     = $objRequest->getSession();
        $strCodEmpresa  = $objSession->get('idEmpresa');        
        $emGeneral      = $this->getDoctrine()->getManager('telconet_general');
        try
        {
            $objAdmiParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                            ->findOneBy(array('nombreParametro' => 'VISUALIZACION LOGS', 
                                                              'estado'          => 'Activo'));
            if(is_object($objAdmiParametroCab))
            {              
                $objAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                 ->findOneBy(array('parametroId' => $objAdmiParametroCab,
                                                                   'descripcion' => 'RANGO CONSULTA',
                                                                   'empresaCod'  => $strCodEmpresa,
                                                                   'estado'      => 'Activo'));
                if(is_object($objAdmiParametroDet))
                {
                    $intParametro  = $objAdmiParametroDet->getValor1();                        

                }
                else
                {
                    throw new \Exception('Error, no existe la configuración requerida para RANGO CONSULTA ');
                }                          
            } 
            $arrayResponse = array('intRangoFechas'  => $intParametro,
                                   'strMensaje' => '');
             
        }
        catch(\Exception $e)
        {
            $strMensaje    = 'Ocurrió un error al consultar parámetro.';
            $arrayResponse = array('intRangoFechas' => '0',
                                   'strMensaje'     => $strMensaje);
        }
        $objResponse = new Response(json_encode($arrayResponse));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    /**
     * Documentación para el método 'getMetodos'.
     * Función que retorna listado de metodos asociados a las diferentes casos de uso para visualizacion de logs.
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 14-01-2023
     * 
     * @return object $objResponse
     */    
    public function getMetodosAction()
    {
        $objRequest      = $this->getRequest();        
        $objSession      = $objRequest->getSession();
        $strCodEmpresa   = $objSession->get('idEmpresa');
        $emGeneral       = $this->getDoctrine()->getManager('telconet_general');        
        $objAdmiParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                        ->findOneBy(array('nombreParametro' => 'VISUALIZACION LOGS', 
                                                          'estado'          => 'Activo'));
        if(is_object($objAdmiParametroCab))
        {         
            $arrayResultado = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                        ->findBy(array('parametroId' => $objAdmiParametroCab,
                                                       'descripcion' => 'PERFILES_CONSULTA_CLIENTE_APP',
                                                       'valor4'      => 'S',
                                                       'empresaCod'  => $strCodEmpresa,
                                                       'estado'      => 'Activo'));
            foreach($arrayResultado as $objAdmiParametroDet):
                $arrayMetodos[] = array(
                    'metodo'      => $objAdmiParametroDet->getValor1(),
                    'descripcion' => $objAdmiParametroDet->getObservacion()
                );

            endforeach;
        }        
        

        $objResponse = new Response(json_encode(array('lista_metodos' => $arrayMetodos)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }    
}


<?php

namespace telconet\comercialBundle\Controller;

use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use telconet\schemaBundle\Entity\ReturnResponse;
use \PHPExcel;
use \PHPExcel_IOFactory;
use \PHPExcel_CachedObjectStorageFactory;
use \PHPExcel_Settings;

/**
 * CambioCiclosFacturacionController controller.
 *
 * Controlador que se encargará de las funcionalidades respecto a la opción de Cambio de Ciclos de Facturación
 *
 * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
 * @version 1.0 07-09-2017
 */
class CambioCiclosFacturacionController extends Controller
{
   
   /**   
    * @Secure(roles="ROLE_396-1")
    * 
    * Documentación para el método 'indexAction'.        
    * Metodo que direcciona a la página de Cambio de Ciclo de Facturación
    * Se obtiene el parametro de la cantidad de registros maximos que se enviaran al PMA de Cambio de Ciclo
    * @return render 
    *
    * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
    * @version 1.0 07-09-2017
    */
    public function indexAction()
    {   
        $objRequest           = $this->get('request');        
        $objSession           = $objRequest->getSession();
        $strIdEmpresa         = $objSession->get('idEmpresa');  
        $objGeneral           = $this->get('doctrine')->getManager('telconet_general');
        $strParamPadre        = 'CANTIDAD_PROCESA_PMA_CAMBIOCICLO';
        $strModulo            = 'COMERCIAL';
        $intCantidaRegProcesa = 0;
        $arrayParametroDet    = array();
        $arrayParametroDet    = $objGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                          ->getOne($strParamPadre, $strModulo, '', '', '', '', '', '', '', $strIdEmpresa);
        if(isset($arrayParametroDet["valor1"]) && !empty($arrayParametroDet["valor1"]))
        {
            $intCantidaRegProcesa = $arrayParametroDet["valor1"];
        }
        $arrayParametros                          = array();        
        $arrayParametros['intCantidaRegProcesa'] = $intCantidaRegProcesa;
        return $this->render('comercialBundle:CambioCiclosFacturacion:index.html.twig',$arrayParametros);
    }  
    
    /**
    * getCiclosFacturacionAction, obtiene la informacion de los Ciclos de Facturación por empresa en sesion y estados
    *     
    * Se obtiene los Ciclos de Facturación según array de estados a consultar y se verifica que existan clientes atados al ciclo.
    *      
    * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
    * @version 1.0 07-09-2017
    * 
    * @return JsonResponse 
    */
    public function getCiclosFacturacionAction()
    {
        $objJsonResponse                     = new JsonResponse();
        $objRequest                          = $this->getRequest();
        $objSession                          = $objRequest->getSession();               
        $emComercial                         = $this->getDoctrine()->getManager();
        $arrayParametros                     = array();
        $arrayParametros['strEmpresaCod']    = $objSession->get('idEmpresa'); 
        $arrayParametros['arrayEstadoCiclo'] = array('Activo','Inactivo');   
        
        $strTipo                             = $objRequest->get("strTipo");
        $arrayParametros['strTipo']          = $strTipo;
        $arrayCiclosFactByEmpresaEstado = $emComercial->getRepository('schemaBundle:AdmiCiclo')
                                                      ->getCiclosFactByEmpresaEstado($arrayParametros);
        
        $objCiclosFactByEmpresaEstado   = $arrayCiclosFactByEmpresaEstado['objRegistros'];
        $intTotal                       = $arrayCiclosFactByEmpresaEstado['intTotal'];
        
        foreach($objCiclosFactByEmpresaEstado as $arrayCiclosFacturacion)
        {
            $arrayCiclos[] = array('intIdCiclo'      => $arrayCiclosFacturacion['intIdCiclo'],
                                   'strNombreCiclo'  => $arrayCiclosFacturacion['strNombreCiclo']);                          
        }
        $objJsonResponse->setData( array('registros' => $arrayCiclos,
                                         'intTotal'  => $intTotal));        
        return $objJsonResponse;
    }
    
    /**
    * getFormasPagoParaContratoAction
    *     
    * Se obtiene las Formas de Pago por Empresa en Sesion y estado, Verificando que la forma de pago exista asignado 
    * al menos a un contrato 
    *      
    * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
    * @version 1.0 11-09-2017
    * 
    * @return JsonResponse 
    */
    public function getFormasPagoParaContratoAction()
    {
        $objJsonResponse                     = new JsonResponse();
        $objRequest                          = $this->getRequest();
        $objSession                          = $objRequest->getSession();               
        $emComercial                         = $this->getDoctrine()->getManager();       
        $arrayParametros                     = array();
        $arrayParametros['strEmpresaCod']    = $objSession->get('idEmpresa'); 
        $arrayParametros['strEstadoFp']      = 'Activo';   
        
        $arrayFormasPagoParaContrato = $emComercial->getRepository('schemaBundle:AdmiFormaPago')
                                                   ->getFormasPagoParaContrato($arrayParametros);
        
        $objFormasPagoParaContrato = $arrayFormasPagoParaContrato['objRegistros'];
        $intTotal                  = $arrayFormasPagoParaContrato['intTotal'];
        $arrayFormasPago           = array();
        
        foreach($objFormasPagoParaContrato as $arrayFpParaContrato)
        {
            $arrayFormasPago[] = array('intIdFormaPago'           => $arrayFpParaContrato['intIdFormaPago'],
                                       'strDescripcionFormaPago'  => $arrayFpParaContrato['strDescripcionFormaPago']);                          
        }
        $objJsonResponse->setData( array('encontrados' => $arrayFormasPago,
                                         'total'       => $intTotal));        
        return $objJsonResponse;
    }
    
    /**
     * getTipoCuentaAction
     *     
     * Se obtiene los tipos de cuenta ADMI_TIPO_CUENTA en base al parametro ES_TARJETA (S o N)
     * y por estado Activo
     *      
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 13-09-2017
     *
     * @author Jorge Guerrero <jguerrerop@telconet.ec>
     * @version 1.1 27-02-2018 - Se agrega el codigo del pais en sesion en el arreglo $arrayParametros
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 31-08-2021 Se agrega la obtención del parámetro strInCuentaTarjeta que obtiene la concatenación de los filtros seleccionados
     *                         Cuenta/Tarjeta en la pantalla de corte masivo
     *
     * @param  string  strEsCuentaTarjetaSelected  Identifica si el Tipo de Cuenta es TARJETA o CUENTA BANCARIA
     * @return JsonResponse 
     */
    public function getTipoCuentaAction()
    {
        $objJsonResponse                        = new JsonResponse();
        $objRequest                             = $this->getRequest();        
        $objSession                             = $objRequest->getSession();
        $emGeneral                              = $this->getDoctrine()->getManager('telconet_general');
        $arrayParametros                        = array();        
        $strEsCuentaTarjeta                     = $objRequest->get('strEsCuentaTarjetaSelected');
        $strInCuentaTarjeta                     = $objRequest->get('strInCuentaTarjeta');
        $arrayParametros['strEstado']           = 'Activo';   
        $strEsTarjeta                           = '';
        $intIdPais                              = $objSession->get('intIdPais');
        $arrayInValoresEsTarjetaTipoCuenta      = array();
        if(isset($strInCuentaTarjeta) && !empty($strInCuentaTarjeta))
        {
            $arrayInCuentaTarjeta = explode(",", $strInCuentaTarjeta);
            foreach($arrayInCuentaTarjeta as $strArrayInCuentaTarjeta)
            {
                if($strArrayInCuentaTarjeta == 'Tarjeta')
                {
                    $arrayInValoresEsTarjetaTipoCuenta[] = 'S';
                }
                else
                {
                    $arrayInValoresEsTarjetaTipoCuenta[] = 'N';
                }
            }
        }
        else
        {
            if($strEsCuentaTarjeta == 'Tarjeta')
            {
                $strEsTarjeta = 'S';
            }
            else
            {
                $strEsTarjeta = 'N';
            }
        }
        
        $arrayParametros['strEsTarjeta']                        = $strEsTarjeta;
        $arrayParametros['intIdPais']                           = $intIdPais;
        $arrayParametros['arrayInValoresEsTarjetaTipoCuenta']   = $arrayInValoresEsTarjetaTipoCuenta;
        $arrayTiposCuentaPorEstado  = $emGeneral->getRepository('schemaBundle:AdmiTipoCuenta')
                                                ->getTiposCuenta($arrayParametros);      
        $objTiposCuenta             = $arrayTiposCuentaPorEstado['objRegistros'];
        $intTotal                   = $arrayTiposCuentaPorEstado['intTotal'];
        $arrayTiposCuenta           = array();
        
        foreach($objTiposCuenta as $arrayTiposCta)
        {
            $arrayTiposCuenta[] = array('intIdTipoCuenta'       => $arrayTiposCta['intIdTipoCuenta'],
                                        'strDescripcionCuenta'  => $arrayTiposCta['strDescripcionCuenta']);                          
        }
        $objJsonResponse->setData( array('encontrados' => $arrayTiposCuenta,
                                         'total'       => $intTotal));        
        return $objJsonResponse;
    }
    
    /**
     * getBancosAction
     * 
     * Se obtiene los BANCOS asociados al TIPO DE CUENTA en ADMI_BANCO_TIPO_CUENTA en base al parametro ID_TIPO_CUENTA 
     * y estado, Considerando los Bancos Asociados a las Cuentas Bancarias AHORRO y CORRIENTE
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 14-09-2017
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 04-08-2019 Se permite la obtención del parámetro valorCuentaTarjetaSelected que indica si el tipo de cuenta es tarjeta
     *                         o cuenta y a su vez se envía el respectivo parámetro strEsTarjetaTipoCuenta a la función getBancosPorTiposCuenta 
     *                         para consultar los bancos.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 01-09-2021 Se agrega la programación para permitir obtener los bancos tanto de tarjeta y cuenta bancaria
     * 
     * @param  integer  idTipoCuentaSelected  Identifica el id del Tipo de cuenta
     * @return JsonResponse 
     */
    public function getBancosAction()
    {
        $objJsonResponse            = new JsonResponse();
        $objRequest                 = $this->getRequest();        
        $emGeneral                  = $this->getDoctrine()->getManager('telconet_general');
        $arrayParametros            = array();        
        $strIdTipoCuentaSelected    = $objRequest->get('idTipoCuentaSelected');
        $arrayTiposCuentaSelected   = explode(",",$strIdTipoCuentaSelected);
        $arrayTiposCuenta   = array();
        foreach($arrayTiposCuentaSelected as $intIdTipoCuenta)
        {
            $arrayTiposCuenta[] = $intIdTipoCuenta;
        }
        $arrayParametros['arrayTiposCuenta']    = $arrayTiposCuenta;            
        $arrayParametros['strEstado']           = 'Activo';
        $strEsTarjetaTipoCuenta                 = "";
        $arrayInValoresEsTarjetaTipoCuenta      = array();
        $arrayInValoresCuentaTarjeta            = array();
        $strValorCuentaTarjetaSeleccionado      = $objRequest->get('valorCuentaTarjetaSelected');
        if(isset($strValorCuentaTarjetaSeleccionado) && !empty($strValorCuentaTarjetaSeleccionado))
        {
            $arrayInCuentaTarjeta = explode(",", $strValorCuentaTarjetaSeleccionado);
            foreach($arrayInCuentaTarjeta as $strArrayInCuentaTarjeta)
            {
                if($strArrayInCuentaTarjeta == 'Tarjeta')
                {
                    $arrayInValoresEsTarjetaTipoCuenta[] = 'S';
                }
                else
                {
                    $arrayInValoresEsTarjetaTipoCuenta[] = 'N';
                }
                $arrayInValoresCuentaTarjeta[] = strtoupper($strArrayInCuentaTarjeta);
            }
        }
        else
        {
            $strEsTarjetaTipoCuenta = 'N';
        }
        
        $strProcesoEjecutante   = $objRequest->get('procesoEjecutante');
        if(isset($strProcesoEjecutante) && !empty($strProcesoEjecutante))
        {
            $arrayParametros["strProcesoEjecutante"]  = $strProcesoEjecutante;
        }
        $arrayParametros["strEsTarjetaTipoCuenta"]              = $strEsTarjetaTipoCuenta;
        $arrayParametros["arrayInValoresEsTarjetaTipoCuenta"]   = $arrayInValoresEsTarjetaTipoCuenta;
        $arrayParametros["arrayInValoresCuentaTarjeta"]         = $arrayInValoresCuentaTarjeta;
        
        $arrayBancosPorTipoCuenta = $emGeneral->getRepository('schemaBundle:AdmiBancoTipoCuenta')
                                              ->getBancosPorTiposCuenta($arrayParametros);      
        $objBancos                = $arrayBancosPorTipoCuenta['objRegistros'];
        $intTotal                 = $arrayBancosPorTipoCuenta['intTotal'];
        $arrayBancos              = array();
        
        foreach($objBancos as $arrayBancosTiposCta)
        {
            $arrayBancos[] = array('intIdBanco'           => $arrayBancosTiposCta['intIdBanco'],
                                   'strDescripcionBanco'  => $arrayBancosTiposCta['strDescripcionBanco']);                          
        }
        $objJsonResponse->setData( array('encontrados' => $arrayBancos,
                                         'total'       => $intTotal));        
        return $objJsonResponse;
    }        
    /**
    * getEstadosServCambioCicloAction
    * Método encargado de obtener los estados de los servicios definidos para filtro.
    * registrados en la tabla ADMI_PARAMETRO_DET con el parametro padre 'ESTADOS_SERVICIOS_CAMBIO_CICLO'
    *
    * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
    * @version 1.0 20-09-2017      
    * 
    * @return \Symfony\Component\HttpFoundation\JsonResponse
    */
    public function getEstadosServCambioCicloAction()
    {
        $objRequest         = $this->get('request');        
        $objSession         = $objRequest->getSession();        
        $strIdEmpresa       = $objSession->get('idEmpresa');        
        $emGeneral          = $this->get('doctrine')->getManager('telconet_general');                
        $strParamPadre      = 'ESTADOS_SERVICIOS_CAMBIO_CICLO';
        $strModulo          = 'COMERCIAL';
        $objReturnResponse  = new ReturnResponse();        
        
        $arrayParametros                  = array();
        $arrayParametros['strParamPadre'] = $strParamPadre; 
        $arrayParametros['strModulo']     = $strModulo; 
        $arrayParametros['strIdEmpresa']  = $strIdEmpresa; 
        $strAppendEstados                 = $objRequest->get('strAppendDatos');
                
        $objListaEstados  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                      ->getEstadosServCambioCiclo($arrayParametros);
        $arrayEstadosServ = array();        
        if(!empty($strAppendEstados))
        {
            $arrayEstadosServ[0] = array('intIdObj'          => 0,
                                         'strDescripcionObj' => $strAppendEstados);
        }
        $arrayEstadosServ = array_merge($arrayEstadosServ,$objListaEstados->getRegistros());
        $objReturnResponse->setRegistros($arrayEstadosServ);
        $objReturnResponse->setTotal(count($arrayEstadosServ));
        $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS);
        
        $objJsonResponse = new JsonResponse((array) $objReturnResponse);
        return $objJsonResponse;
    }

    /**
    * @Secure(roles="ROLE_396-1")
    * 
    * Documentación para el método 'getClientesACambiarCicloFactAction'
    * Metodo que obtiene Listado de clientes a los cuales se realizara Cambio de Ciclo de Facturación
    * Consulta se realiza en base a filtros de Busqueda enviados por parametros.
    * 
    * @param strIdentificacion, Numero de Identificación del Cliente
    * @param strCliente, Nombre o Razon Social del Cliente 
    * @param idCicloFacturacion, id del Ciclo de Facturación
    * @param strIdsEstadoServicio, estado del servicio 
    * @param cbxIdPtoCobertura, ids para filtrar por punto de cobertura
    * @param idFormaPago, id de la forma de Pago
    * @param strEsCuentaTarjeta, Identifica si CUENTA o TARJETA 
    * @param idsTipoCuenta, ids para filtrar por Tipos de Cuenta 
    * @param idsBancos, ids para filtrar por Bancos 
    * 
    * @return JsonResponse 
    * 
    * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
    * @version 1.0 15-09-2017       
    */
    public function getClientesACambiarCicloFactAction()
    {
        ini_set('max_execution_time', 999999999999);
        $emComercial        = $this->getDoctrine()->getManager("telconet");        
        $objRequest         = $this->getRequest();
        $objSession         = $objRequest->getSession();
        $serviceUtil        = $this->get('schema.Util');
        $strIpClient        = $objRequest->getClientIp();
        $strUsrSesion       = $objSession->get('user'); 
        $arrayParametros    = array();        
        $objOciCon          = oci_connect(
                                          $this->container->getParameter('user_comercial'),
                                          $this->container->getParameter('passwd_comercial'),
                                          $this->container->getParameter('database_dsn')
                                         );        
        $objCursor          = oci_new_cursor($objOciCon);
        $strEsCuentaTarjeta = $objRequest->get("strEsCuentaTarjeta");
        $strEsTarjeta       = '';
        if($strEsCuentaTarjeta == 'Tarjeta')
        {
            $strEsTarjeta = 'S';
        }
        else
        {
            if($strEsCuentaTarjeta == 'Cuenta')
            {
                $strEsTarjeta = 'N';
            }
        }        
        $arrayParametros['strIdentificacion']     = trim($objRequest->get("strIdentificacion"));
        $arrayParametros['strCliente']            = trim($objRequest->get("strCliente"));
        $arrayParametros['intIdCicloFacturacion'] = $objRequest->get("idCicloFacturacion");
        $arrayParametros['strIdsEstadoServicio']  = $objRequest->get("cbxIdEstadoServicio");
        $arrayParametros['strIdsPtoCobertura']    = $objRequest->get("cbxIdPtoCobertura");        
        $arrayParametros['intIdFormaPago']        = $objRequest->get("idFormaPago");
        $arrayParametros['strEsTarjeta']          = $strEsTarjeta;
        $arrayParametros['strIdsTipoCuenta']      = $objRequest->get("idsTipoCuenta");
        $arrayParametros['strIdsBancos']          = $objRequest->get("idsBancos");
        $arrayParametros['strEmpresaId']          = $objSession->get('idEmpresa');
        $arrayParametros['strPrefijoEmpresa']     = $objSession->get('prefijoEmpresa');
        $arrayParametros['strUsrSesion']          = trim($strUsrSesion);
        $arrayParametros['intStart']              = $objRequest->get('start');
        $arrayParametros['intLimit']              = $objRequest->get('limit');
        $arrayParametros['objOciCon']             = $objOciCon;
        $arrayParametros['objCursor']             = $objCursor;
        
        $arrayListClientesACambiarCicloFact = array();
        $objJsonResponse                    = new JsonResponse($arrayListClientesACambiarCicloFact);
             
        try
        {        
            $arrayListClientesACambiarCicloFact  = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                               ->getJsonClientesACambiarCicloFact($arrayParametros);
            $objJsonResponse->setData($arrayListClientesACambiarCicloFact);
              
        }
        catch (\Exception $e) 
        {                
            $serviceUtil->insertError('Telcos+', 
                                      'CambioCiclosFacturacionController.getClientesACambiarCicloFactAction',
                                      $e->getMessage(), 
                                      $strUsrSesion, 
                                      $strIpClient
                                     );                
        }  
                                
        return $objJsonResponse;                              
    }  
    /**
    * @Secure(roles="ROLE_396-1")
    * 
    * Documentación para el método 'getAsignarCicloFactAction'
    * Metodo que genera un Proceso Masivo de Cambio de Ciclo de Facturacion, en base a parametros enviados.
    * El metodo incluira en el PMA de Cambio de Ciclo a todos los Clientes que hayan sido previamente escogidos o
    * marcados en el proceso, asignando el nuevo Ciclo escogido.
    *   
    * @param idCicloFacturacionNuevo, id del Nuevo Ciclo de Facturación
    * @param idsPersonaRol, ids de Clientes a los cuales se realizara el cambio de ciclo.
    * 
    * @return JsonResponse 
    * 
    * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
    * @version 1.0 22-09-2017       
    */
    public function getAsignarCicloFactAction()
    {
        ini_set('max_execution_time', 9999999999);
        $emComercial        = $this->getDoctrine()->getManager("telconet");        
        $objRequest         = $this->getRequest();
        $objSession         = $objRequest->getSession();
        $serviceUtil        = $this->get('schema.Util');
        $strIpClient        = $objRequest->getClientIp();
        $strUsrSesion       = $objSession->get('user'); 
        $arrayParametros    = array();        
               
        $arrayParametros['strIdsPersonaRol']           = $objRequest->get("idsPersonaRol");
        $arrayParametros['intIdCicloFacturacionNuevo'] = $objRequest->get("idCicloFacturacionNuevo");
        $arrayParametros['strIdsPtoCobertura']         = $objRequest->get("cbxIdPtoCobertura");        
        $arrayParametros['strEmpresaId']               = $objSession->get('idEmpresa');
        $arrayParametros['strPrefijoEmpresa']          = $objSession->get('prefijoEmpresa');
        $arrayParametros['strUsrSesion']               = trim($strUsrSesion);        
            
        try
        {
            $strResultado = $emComercial->getRepository('schemaBundle:InfoPersona')->getAsignarCicloFacturacion($arrayParametros);
        }
        catch (\Exception $e) 
        {
            $serviceUtil->insertError('Telcos+', 
                                      'CambioCiclosFacturacionController.getAsignarCicloFactAction',
                                      $e->getMessage(), 
                                      $strUsrSesion, 
                                      $strIpClient
                                     );
        }
        
        $objJsonResponse = new JsonResponse($strResultado);
        return $objJsonResponse;        
    }      
     /**
    * @Secure(roles="ROLE_396-1")
    * 
    * Documentación para el método 'getAsignarCicloFactTodosAction'
    * Metodo que genera un Proceso Masivo de Cambio de Ciclo de Facturacion, en base a parametros enviados.
    * El metodo incluira en el PMA de Cambio de Ciclo a todos los Clientes que esten incluidos en los criterios 
    * o filtros seleccionados por pantalla, asignando el nuevo Ciclo escogido.
    *   
    * @param idCicloFacturacionNuevo, id del Nuevo Ciclo de Facturación
    * @param strIdentificacion, identificacion del cliente Ced/Ruc/pas
    * @param strCliente , Nombre del Cliente o Razon Social
    * @param idCicloFacturacion , Id de Ciclo Actual del cliente
    * @param cbxIdEstadoServicio , Estado de los servicios 
    * @param cbxIdPtoCobertura , Puntos de Cobertura o Jurisdicciones 
    * @param idFormaPago , Forma de Pago del cliente
    * @param strEsCuentaTarjeta, Es Cuenta bancaria o tarjeta de credito
    * @param idsTipoCuenta , Tipo de cuenta del cliente
    * @param idsBancos   , Ids de los bancos asociados a la cuenta bancaria.
    * 
    * @return JsonResponse 
    * 
    * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
    * @version 1.0 13-10-2017       
    */
    public function getAsignarCicloFactTodosAction()
    {
        ini_set('max_execution_time', 9999999999);
        $emComercial        = $this->getDoctrine()->getManager("telconet");        
        $objRequest         = $this->getRequest();
        $objSession         = $objRequest->getSession();
        $serviceUtil        = $this->get('schema.Util');
        $strIpClient        = $objRequest->getClientIp();
        $strUsrSesion       = $objSession->get('user'); 
        $arrayParametros    = array();        

        $strEsCuentaTarjeta = $objRequest->get("strEsCuentaTarjeta");
        $strEsTarjeta       = '';
        if($strEsCuentaTarjeta == 'Tarjeta')
        {
            $strEsTarjeta = 'S';
        }
        else
        {
            if($strEsCuentaTarjeta == 'Cuenta')
            {
                $strEsTarjeta = 'N';
            }
        }     
        $arrayParametros['intIdCicloFacturacionNuevo'] = $objRequest->get("idCicloFacturacionNuevo");
        $arrayParametros['strIdentificacion']          = trim($objRequest->get("strIdentificacion"));
        $arrayParametros['strCliente']                 = trim($objRequest->get("strCliente"));
        $arrayParametros['intIdCicloFacturacion']      = $objRequest->get("idCicloFacturacion");
        $arrayParametros['strIdsEstadoServicio']       = $objRequest->get("cbxIdEstadoServicio");
        $arrayParametros['strIdsPtoCobertura']         = $objRequest->get("cbxIdPtoCobertura");        
        $arrayParametros['intIdFormaPago']             = $objRequest->get("idFormaPago");
        $arrayParametros['strEsTarjeta']               = $strEsTarjeta;
        $arrayParametros['strIdsTipoCuenta']           = $objRequest->get("idsTipoCuenta");
        $arrayParametros['strIdsBancos']               = $objRequest->get("idsBancos");
        $arrayParametros['strEmpresaId']               = $objSession->get('idEmpresa');
        $arrayParametros['strPrefijoEmpresa']          = $objSession->get('prefijoEmpresa');
        $arrayParametros['strUsrSesion']               = trim($strUsrSesion);                   
            
        try
        {
            $strResultado = $emComercial->getRepository('schemaBundle:InfoPersona')->getAsignarCicloFactTodos($arrayParametros);
        }
        catch (\Exception $e) 
        {
            $serviceUtil->insertError('Telcos+', 
                                      'CambioCiclosFacturacionController.getAsignarCicloFactTodosAction',
                                      $e->getMessage(), 
                                      $strUsrSesion, 
                                      $strIpClient
                                     );
        }
        
        $objJsonResponse = new JsonResponse($strResultado);
        return $objJsonResponse;        
    } 
    
    /**
    * @Secure(roles="ROLE_396-1")
    * 
    * Documentación para el método 'generarRptCambioCicloAction'
    * Metodo que obtiene reporte de clientes a los cuales se realizara Cambio de Ciclo de Facturación
    * La consulta se realiza en base a los filtros de busqueda enviados por parametros, genera CSV de la informacion 
    * que sera enviado por correo.
    * 
    * @param strIdentificacion, Numero de Identificación del Cliente
    * @param strCliente, Nombre o Razon Social del Cliente 
    * @param idCicloFacturacion, id del Ciclo de Facturación
    * @param strIdsEstadoServicio, estado del servicio 
    * @param cbxIdPtoCobertura, ids para filtrar por punto de cobertura
    * @param idFormaPago, id de la forma de Pago
    * @param strEsCuentaTarjeta, Identifica si CUENTA o TARJETA 
    * @param idsTipoCuenta, ids para filtrar por Tipos de Cuenta 
    * @param idsBancos, ids para filtrar por Bancos 
    * 
    * @return JsonResponse 
    * 
    * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
    * @version 1.0 17-10-2017       
    */
    public function generarRptCambioCicloAction()
    {
        ini_set('max_execution_time', 999999999999);
        $emComercial        = $this->getDoctrine()->getManager("telconet");        
        $objRequest         = $this->getRequest();
        $objSession         = $objRequest->getSession();
        $serviceUtil        = $this->get('schema.Util');
        $strIpClient        = $objRequest->getClientIp();
        $strUsrSesion       = $objSession->get('user'); 
        $arrayParametros    = array();        
        
        $strEsCuentaTarjeta = $objRequest->get("strEsCuentaTarjeta");
        $strEsTarjeta       = '';
        if($strEsCuentaTarjeta == 'Tarjeta')
        {
            $strEsTarjeta = 'S';
        }
        else
        {
            if($strEsCuentaTarjeta == 'Cuenta')
            {
                $strEsTarjeta = 'N';
            }
        }        
        $arrayParametros['strIdentificacion']     = trim($objRequest->get("strIdentificacion"));
        $arrayParametros['strCliente']            = trim($objRequest->get("strCliente"));
        $arrayParametros['intIdCicloFacturacion'] = $objRequest->get("idCicloFacturacion");
        $arrayParametros['strIdsEstadoServicio']  = $objRequest->get("cbxIdEstadoServicio");
        $arrayParametros['strIdsPtoCobertura']    = $objRequest->get("cbxIdPtoCobertura");        
        $arrayParametros['intIdFormaPago']        = $objRequest->get("idFormaPago");
        $arrayParametros['strEsTarjeta']          = $strEsTarjeta;
        $arrayParametros['strIdsTipoCuenta']      = $objRequest->get("idsTipoCuenta");
        $arrayParametros['strIdsBancos']          = $objRequest->get("idsBancos");
        $arrayParametros['strEmpresaId']          = $objSession->get('idEmpresa');
        $arrayParametros['strPrefijoEmpresa']     = $objSession->get('prefijoEmpresa');
        $arrayParametros['strUsrSesion']          = trim($strUsrSesion);
               
        try
        {
            $strResultado = $emComercial->getRepository('schemaBundle:InfoPersona')->generarRptCambioCiclo($arrayParametros);
        }
        catch (\Exception $e) 
        {
            $serviceUtil->insertError('Telcos+', 
                                      'CambioCiclosFacturacionController.generarRptCambioCicloAction',
                                      $e->getMessage(), 
                                      $strUsrSesion, 
                                      $strIpClient
                                     );           
        }        
        $objJsonResponse = new JsonResponse($strResultado);
        return $objJsonResponse;                                      
    }  
}

<?php

namespace telconet\tecnicoBundle\WebService;

use telconet\schemaBundle\DependencyInjection\BaseWSController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Clase que sirve para la gestion de ingreso, edicion mantenimiento de informacion de SWITCH y ROUTER por medio de Networking
 * 
 * @author Allan Suarez <arsuarez@telconet.ec>
 * @version 1.0 29-08-2016
 */
class AdministrarDispositivosWSController extends BaseWSController
{
    const TN = "TN";        
    
    /**
     * Funcion que sirve para procesar las opciones que son solicitadas por Networking
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 29-09-2016  
     * @param $objRequest
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.1 16-11-2016  Se aumentó la actualización y consulta de puertos.
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.2 24-04-2017  Se aumentó funcion para obtener los modelos activos de SW y RO
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.3 16-05-2017  Se aumentaron las opciones para obtener el bw totalizado y detallado
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.4 25-05-2017  Se aumentaron las opciones para obtener la informacion de espacio de un elemento 
     * 
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.5 30-10-2019  Se aumentaron las opciones para la comprobacion/eliminacion de interfaces, 
     *                          cambio de ultima milla y eliminacion de SWITCH 
     * 
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.6 30-03-2021  Se aumento la opción para activar el olt multiplataforma
     * 
     * @author Anthony Santillan <asantillany@telconet.ec>
     * @version 1.7 03-10-2022  Se aumento el nombre_elemento en la consulta por numero de serie del elemento
     */
    public function procesarAction(Request $objRequest)
    {
        $arrayData      = json_decode($objRequest->getContent(),true);       
        $arrayResponse  = null;        
        $objResponse    = new Response();
        $strOpcion      = $arrayData['op'];
                
       $token = $this->validateGenerateToken($arrayData['token'],$arrayData['source'],$arrayData['user']);
       if(!$token)
       {
           return new Response(json_encode(array(
                   'status'  =>  403,
                   'mensaje' => "token invalido"
                   )
               )
           );
       }
        if(isset($strOpcion))
        {
            switch($strOpcion)
            {
                case 'putInformacionDispositivo':                    
                    $arrayResponse = $this->crearElementoSwitchRouterTN($arrayData);
                    break;
                case 'updateInformacionDispositivo':
                    $arrayResponse = $this->editarElementoSwitchRouterTN($arrayData);
                    break;
                case 'getInformacionElementoPorTipo':
                    $arrayResponse = $this->getInformacionElementoPorTipo($arrayData);
                    break;
                case 'getInformacionEmpleados':
                    $arrayResponse = $this->getInformacionEmpleados($arrayData['dataAuditoria']);
                    break;
                case 'actualizarInterfaceElemento':
                    $arrayResponse = $this->actualizarInterfaceElemento($arrayData);
                    break;
                case 'getEstadoInterfaceElemento':
                    $arrayResponse = $this->getEstadoInterfaceElemento($arrayData);
                    break;
                case 'getInformacionModelos':
                    $arrayResponse = $this->getInformacionModeloSwitchRouterTN($arrayData);
                    break;
                case 'getBw':
                    $arrayResponse = $this->getBw($arrayData);
                    break;   
                case 'getInformacionEspacialDispositivo':
                    $arrayResponse = $this->getInformacionEspacialDispositivo($arrayData);
                    break;
                case 'deleteDispositivo':                    
                    $arrayResponse = $this->eliminarElementoSwitchStackTN($arrayData);
                    break;
                case 'updateInterfacesDispositivo':                    
                    $arrayResponse = $this->updateInterfacesElementoSwitchTN($arrayData);
                    break;
                case 'updateCambioDispositivo':                    
                    $arrayResponse = $this->updateCambioDispositivoElementoSwitchTN($arrayData);
                    break;
                case 'activarOltMultiplataforma':
                    $arrayResponse = $this->activarElementoOltMultiplataformaTN($arrayData);
                    break;
                case 'procesarPromocionesBwOlt':
                    $arrayResponse = $this->procesarPromocionesBwOlt($arrayData);
                    break;
                default:
                    $arrayResponse['status']  = "ERROR";
                    $arrayResponse['mensaje'] = "Metodo ".$strOpcion." no valido/inexistente";
            }
        }
        else
        {
            $arrayResponse['status']  = "ERROR";
            $arrayResponse['mensaje'] = "Metodo ".$strOpcion." no valido/inexistente";
        }
                          
        $objResponse->headers->set('Content-Type', 'application/json');
        $objResponse->setContent(json_encode($arrayResponse));
        
        return $objResponse;
    }
    
    /**
     * Funcion que sirve para actualizar el estado de una interface de un elemento
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 15-11-2016
     *
     * @param Array $arrayData [
     *      dataAuditoria 
     *      [
     *       usrCreacion              Usuario que lanza la petición
     *       ipCreacion               Ip de donde nace la petición
     *      ]
     *      nombreElemento           Nombre del elemento
     *      nombreInterfaceElemento  Nuevo nombre que actualizará el elemento actual
     *      estadoInterface          estado del elemento a actualizar
     * ]
     * @return Array $arrayRespuesta [ status , mensaje ]
     */
    private function actualizarInterfaceElemento($arrayData)
    {
        ini_set('max_execution_time', 400000);
                        
        $serviceElemento  = $this->get('tecnico.InfoElemento'); 
        $serviceUtil      = $this->get('schema.Util');
        
        $strUsrCreacion = $arrayData['dataAuditoria']['usrCreacion'];
        $strIpCreacion  = $arrayData['dataAuditoria']['ipCreacion'];
        
        try
        {                                    
            $arrayDatosElementos                = $arrayData['data'];
            $arrayDatosElementos['usrCreacion'] = $strUsrCreacion;
            $arrayDatosElementos['ipCreacion']  = $strIpCreacion;
            
            if(
                (isset($arrayDatosElementos['usrCreacion']) && !empty($arrayDatosElementos['usrCreacion'])) &&
                (isset($arrayDatosElementos['ipCreacion']) && !empty($arrayDatosElementos['ipCreacion']))
              )
            {
                $arrayResponse = $serviceElemento->actualizarInterfaceElemento($arrayDatosElementos);
                
                if($arrayResponse['status'] == 'ERROR')
                {
                    throw new \Exception($arrayResponse['mensaje']);
                }
                
            }
            else
            {
                $arrayResponse['status']  = 'ERROR';
                $arrayResponse['mensaje'] = 'No existe información de Usuario ni Ip de Creación del registro';
            }
        } 
        catch (\Exception $ex) 
        {            
            $arrayResponse['status']  = 'ERROR';
            $arrayResponse['mensaje'] = 'Error al actualizar la interface. '.$ex->getMessage();
            
            $serviceUtil->insertError("Telcos+",
                                      "actualizarInterfaceElemento",
                                      $ex->getMessage(),
                                      $strUsrCreacion,
                                      $strIpCreacion
                                    );
        }
        
        return $arrayResponse;
    }
    

    /**
     * Obtener el estado de la interface
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 15-11-2016
     *
     * @param Array $arrayData [
     *      nombreElemento           Nombre del elemento
     *      nombreInterfaceElemento  Nuevo nombre que actualizará el elemento actual
     * ]
     * @return Array $arrayRespuesta [ status , mensaje ]
     */
    
    private function getEstadoInterfaceElemento($arrayData)
    {
        ini_set('max_execution_time', 400000);

        $serviceElemento = $this->get('tecnico.InfoElemento');

        try
        {
            $arrayDatosElementos = $arrayData['data'];

            $arrayResponse = $serviceElemento->getEstadoInterfaceElemento($arrayDatosElementos);

            if($arrayResponse['status'] == 'ERROR')
            {
                throw new \Exception($arrayResponse['mensaje']);
            }
        }
        catch(\Exception $ex)
        {
            $arrayResponse['status'] = 'ERROR';
            $arrayResponse['mensaje'] = $ex->getMessage();
        }

        return $arrayResponse;
    }
    
    
    /**
     * Funcion que sirve para realizar el ingreso de la informacion para poder guardar un nuevo elemento SWITCH/ROUTER
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 30-08-2016
     * 
     * @param Array $arrayData [tipoElemento , nombre , ip , modelo , prefijoRed , anillo , tipo , serie , numeroModulo , 
     *                          idNodo , idRack , idUdRack]
     * @return Array $arrayResponse
     */
    private function crearElementoSwitchRouterTN($arrayData)
    {
        ini_set('max_execution_time', 400000);
                        
        $elementoService  = $this->get('tecnico.InfoElemento'); 
        $serviceUtil      = $this->get('schema.Util');
        
        $strUsrCreacion = $arrayData['dataAuditoria']['usrCreacion'];
        $strIpCreacion  = $arrayData['dataAuditoria']['ipCreacion'];
        
        try
        {            
            $arrayRespuestaValidacion = $this->validarJsonRequest($arrayData['data']);
           
            if($arrayRespuestaValidacion['status'] == 'OK')
            {
                $arrayDatosElementos                = $arrayData['data'];
                $arrayDatosElementos['usrCreacion'] = $strUsrCreacion;
                $arrayDatosElementos['ipCreacion']  = $strIpCreacion;
                
                if(
                   (isset($arrayDatosElementos['usrCreacion']) && !empty($arrayDatosElementos['usrCreacion'])) &&
                   (isset($arrayDatosElementos['ipCreacion']) && !empty($arrayDatosElementos['ipCreacion']))
                  )
                {
                    $arrayResponse       = $elementoService->crearElementoSwitchRouterTN($arrayDatosElementos);
                }
                else
                {
                    $arrayResponse['status']  = 'ERROR';
                    $arrayResponse['mensaje'] = 'No existe información de Usuario ni Ip de Creación del registro';
                }
            }           
            else
            {
                //Devuelve la informacion de error generado en las validaciones previas
                $arrayResponse = $arrayRespuestaValidacion;
            }
        } 
        catch (\Exception $ex) 
        {            
            $arrayResponse['status']  = 'ERROR';
            $arrayResponse['mensaje'] = 'Error al Crear el Registro del nuevo Switch';
            
            $serviceUtil->insertError("Telcos+",
                                      "crearElementoSwitchRouterTN",
                                      $ex->getMessage(),
                                      $strUsrCreacion,
                                      $strIpCreacion
                                    );
        }
        
        return $arrayResponse;
    }
    
    /**
     * 
     * Funcion que sirve para validar que los campos importantes en el json no sean enviados como vacíos o nulos
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 30-08-2016
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 21-04-2017 - Se valida que key => esEsquemaPseudoPe venga instanciado para poder crear nuevo elemento
     *                         - Se mejora validacion de valores enviado en Json de request
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.2 23-05-2017 - Se valida que key => esHibrido venga instanciado para poder crear nuevo elemento
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.3 21-06-2018 - Se valida que key => tipoPrefijo venga instanciado para poder crear nuevo elemento
     *                         
     * 
     * @param  Array $arrayDatos [
     *                              tipoElemento  Tipo del Elemento ( SWITCH/ROUTER )
     *                              nombre        Nombre del elemento a ser creado
     *                              ip            Ip del elemento a ser creado
     *                              modelo        Nombre del modelo del elemento a ser creado
     *                              prefijoRed    Prefijo Red del ROUTER a ser creado
     *                              anillo        Anillo donde se encuentra ubicado el SWITCH
     *                              serie         Serie del Equipo a ser creado
     *                              tipo          Si el elemento pertenece a Backbone o Edificio
     *                           ]
     * @return Array $arrayRespuestaValidacion [ camposCompletos , mansaje ]
     */
    private function validarValoresVacios($arrayDatos)
    {
        $arrayRespuestaValidacion = array();                
        $strMensaje               = "Errores de Validación existentes : ";
        $booleanCamposCompletos   = true;
        
        $boolEsEsquemaPseudoPeSw  = false;
        
        if((!isset($arrayDatos['tipoElemento']) || $arrayDatos['tipoElemento'] == "") || $arrayDatos['tipoElemento'] == null)
        {
            $booleanCamposCompletos = false;
            $strMensaje             .= "<br>Valor de Nombre de Elemento se encuentra vacío";            
        }
        else
        {
            if($arrayDatos['tipoElemento'] == "SWITCH")
            {
                if(!isset($arrayDatos['anillo']) || $arrayDatos['anillo'] == "")
                {
                    $booleanCamposCompletos = false;
                    $strMensaje             .= "<br>Valor de Anillo se encuentra vacío";
                }                
            }
            else//Router
            {
                if(!isset($arrayDatos['prefijoRed']) || empty($arrayDatos['prefijoRed']))
                {
                    $booleanCamposCompletos = false;
                    $strMensaje             .= "<br>Valor de Prefijo de Red se encuentra vacío";
                } 
                
                if((!isset($arrayDatos['tipoPrefijo']) && $arrayDatos['tipoPrefijo'] == "") || $arrayDatos['tipoPrefijo'] == null)
                {
                    $booleanCamposCompletos = false;
                    $strMensaje             .= "<br>Valor de Tipo Prefijo se encuentra vacío";
                }
            }
        }
        
        if((!isset($arrayDatos['esEsquemaPseudoPe']) && $arrayDatos['esEsquemaPseudoPe'] == "") || $arrayDatos['esEsquemaPseudoPe'] == null)
        {
            $booleanCamposCompletos = false;
            $strMensaje             .= "<br>Debe enviarse si un elemento pertenece a esquema PseudoPe o No";
        }
        else
        {
            //Si es Switch Virtual
            if($arrayDatos['tipoElemento'] == "SWITCH")
            {
                $boolEsEsquemaPseudoPeSw = true;
            }
        }
        
        if((!isset($arrayDatos['esHibrido']) && $arrayDatos['esHibrido'] == "") || $arrayDatos['esHibrido'] == null)
        {
            $booleanCamposCompletos = false;
            $strMensaje             .= "<br>Debe enviarse si un elemento es Hibrido o No ( Switch )";
        }
        
        if((!isset($arrayDatos['nombre']) && $arrayDatos['nombre'] == "") || $arrayDatos['nombre'] == null)
        {
            $booleanCamposCompletos = false;
            $strMensaje             .= "<br>Valor de Nombre de Elemento se encuentra vacío";
        }
        
        if((!isset($arrayDatos['ip']) && $arrayDatos['ip'] == "") || $arrayDatos['ip'] == null)
        {
            $booleanCamposCompletos = false;
            $strMensaje             .= "<br>Valor de IP del Elemento se encuentra vacío";
        }
        
        if((!isset($arrayDatos['modelo']) && $arrayDatos['modelo'] == "") || $arrayDatos['modelo'] == null)
        {
            $booleanCamposCompletos = false;
            $strMensaje             .= "<br>Valor de Modelo del Elemento se encuentra vacío";
        }
        
        //Solo cuando no es esquema PSEUDOPE de SWITCH se verifica la existencia de Serie
        if(!$boolEsEsquemaPseudoPeSw)
        {
            if((!isset($arrayDatos['serie']) && empty($arrayDatos['serie'])) || $arrayDatos['serie'] == null)
            {
                $booleanCamposCompletos = false;
                $strMensaje             .= "<br>Valor de Serie del Elemento se encuentra vacío";
            }
        }
        
        if((!isset($arrayDatos['tipo']) && $arrayDatos['tipo'] == "") || $arrayDatos['tipo'] == null)
        {
            $booleanCamposCompletos = false;
            $strMensaje             .= "<br>Valor de Tipo ( Backbone/Edificio ) se encuentra vacío";
        }
        
        $arrayRespuestaValidacion['camposCompletos'] = $booleanCamposCompletos;
        $arrayRespuestaValidacion['mensaje']         = $booleanCamposCompletos?"":$strMensaje;
       
        return $arrayRespuestaValidacion;
    }
    
    /**
     * Funcion que sirve para validar la información enviada en el json previo a generar el ingreso de un nuevo registro de elemento
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 30-08-2016
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 21-04-2017 Se validad si el tipo elemento SWITCH pertenece a un esquema pseudope o no
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.2 23-05-2017 Se validad si el tipo elemento SWITCH es creado como Hibrido o Switch convencional
     *                         Se valida que el modelo del elemento a ser ingresado de manera convencional tenga configurado la cantidad de
     *                         udracks que necesita para ser ubicado
     * 
     * @param  Array $arrayData [   
     *                              nombre  Nombre del elemento a ser creado
     *                              ip      Ip del elemento a ser creado
     *                              modelo  Nombre del modelo del elemento a ser creado
     *                              serie   Serie del Equipo a ser creado
     *                           ]
     * @return Array $arrayResponse
     */
    private function validarJsonRequest($arrayData)
    {
        $arrayResponse = array();
        
        $emInfraestructura = $this->getDoctrine()->getManager("telconet_infraestructura");
        
        $arrayValidacionCamposVacios = $this->validarValoresVacios($arrayData);
                
        $strMensaje     = "Se produjeron las siguientes advertencias : ";
        $booleanEsError = false;
        
        //Si los campos importantes vienen completos se procede a validar la información existente en BD de Telcos
        if(isset($arrayValidacionCamposVacios['camposCompletos']) && $arrayValidacionCamposVacios['camposCompletos']==true)
        {
            //Se Valida que la información a ingresar exista/sea valida para continuar
            //Validación de MODELO
            $objAdmiModeloElemento = $emInfraestructura->getRepository("schemaBundle:AdmiModeloElemento")
                                                       ->findOneByNombreModeloElemento(trim($arrayData['modelo']));
            
            $boolEsSwitchVirtual = false;
            $boolEsSwitchHibrido = false;
            $boolValidarIpSerie  = true;
            
            //Si el tipo es Switch y su vez es esquema de PseudoPe se establece una bandera
            if($arrayData['tipoElemento'] == 'SWITCH' && $arrayData['esEsquemaPseudoPe'] == 'S')
            {
                $boolEsSwitchVirtual = true;
            }
            
            //Si el tipo es Switch y su vez es esquema de PseudoPe se establece una bandera
            if($arrayData['tipoElemento'] == 'SWITCH' && $arrayData['esHibrido'] == 'S')
            {
                $boolEsSwitchHibrido = true;
            }                        
            
            if(($boolEsSwitchVirtual && !$boolEsSwitchHibrido) || (!$boolEsSwitchVirtual && $boolEsSwitchHibrido))
            {
                $boolValidarIpSerie = false;
            }
            
            if(!is_object($objAdmiModeloElemento))
            {
                $booleanEsError = true;
                $strMensaje    .= "<br>El modelo ".$arrayData['modelo']." no existe o es incorrecto, por favor verificar";
            }
            else
            {
                if(!$boolEsSwitchVirtual)
                {
                    $intURack = $objAdmiModeloElemento->getURack();
                    
                    if($intURack==null)
                    {
                        $booleanEsError = true;
                        $strMensaje    .= "<br>El modelo ".$arrayData['modelo']." no tiene información de las unidades de rack que necesita para "
                                       . "ser ubicado dentro del Rack";
                    }
                }
            }
            
            //Validación de NOMBRE DE ELEMENTO
            $objInfoElemento   = $emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                                   ->findOneByNombreElemento(trim($arrayData['nombre']));
           
            if(is_object($objInfoElemento))
            {
                $booleanEsError = true;
                $strMensaje    .= "<br>El nombre ".$arrayData['nombre']." ya existe ingresado, por favor verificar";
            }
            
            if(!$boolEsSwitchHibrido)
            {
                $objIp = $emInfraestructura->getRepository("schemaBundle:InfoIp")->findOneBy(array('ip'     => $arrayData['ip'], 
                                                                                                   'estado' => 'Activo'));

                if(is_object($objIp))
                {
                    $booleanEsError = true;
                    $strMensaje    .= "<br>La Ip ".$arrayData['ip']." ya existe ingresada, por favor verificar";
                }
            }
            
            //Si es switch virtual o hibrido no debe validarse existencia de Serie
            if($boolValidarIpSerie)
            {
                $objElemento = $emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                                             ->findOneBy(array('serieFisica'        => $arrayData['serie'],
                                                                               'nombreElemento'     => $arrayData['nombre'],
                                                                               'estado'             => 'Activo')
                                                                               );               
                if(is_object($objElemento))
                {
                    $booleanEsError = true;
                    $strMensaje    .= "<br>La Serie ".$arrayData['serie']." ya existe ingresada para un Equipo, por favor verificar";
                }              
            }
        }
        else
        {
            $booleanEsError  = true;
            $strMensaje      = $strMensaje . $arrayValidacionCamposVacios['mensaje'];
        }
        
        $arrayResponse['status']    = $booleanEsError?"ERROR":"OK";
        $arrayResponse['mensaje']   = $booleanEsError?$strMensaje:"";
        
        return $arrayResponse;
    }
    
    /**
     * Funcion que sirve para actualizar la informacion de elemento SWITCH/ROUTER
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 30-08-2016
     * 
     * @param Array $arrayData [
     *                              dataAuditoria [ usrCreacion , ipCreacion ]  Informacion de auditoria enviado
     *                              data [
     *                                  ip             Ip del elemento a actualizar
     *                                  modelo         Modelo del Elemento a actualizar
     *                                  tipoElemento   Tipo del elemento a actualizar SWITCH/ROUTER
     *                                  nombreAnterior Nombre anterior del Equipo a actualizar
     *                                  nombreNuevo    Nombre nuevo a ser actualizado en el Equipo
     *                              ]
     *                         ] 
     */
    private function editarElementoSwitchRouterTN($arrayData)
    {
        ini_set('max_execution_time', 400000);
                        
        $elementoService  = $this->get('tecnico.InfoElemento'); 
        $serviceUtil      = $this->get('schema.Util');
        
        $strUsrCreacion = $arrayData['dataAuditoria']['usrCreacion'];
        $strIpCreacion  = $arrayData['dataAuditoria']['ipCreacion'];
        
        try
        {                                    
            $arrayDatosElementos                = $arrayData['data'];
            $arrayDatosElementos['usrCreacion'] = $strUsrCreacion;
            $arrayDatosElementos['ipCreacion']  = $strIpCreacion;
            
            if(
                (isset($arrayDatosElementos['usrCreacion']) && !empty($arrayDatosElementos['usrCreacion'])) &&
                (isset($arrayDatosElementos['ipCreacion']) && !empty($arrayDatosElementos['ipCreacion']))
              )
            {
                $arrayResponse = $elementoService->editarElementoSwitchRouterTN($arrayDatosElementos); 
            }
            else
            {
                $arrayResponse['status']  = 'ERROR';
                $arrayResponse['mensaje'] = 'No existe información de Usuario ni Ip de Creación del registro';
            }
        } 
        catch (\Exception $ex) 
        {            
            $arrayResponse['status']  = 'ERROR';
            $arrayResponse['mensaje'] = 'Error al Modificar el Switch';
            
            $serviceUtil->insertError("Telcos+",
                                      "editarElementoSwitchRouterTN",
                                      $ex->getMessage(),
                                      $strUsrCreacion,
                                      $strIpCreacion
                                    );
        }
        
        return $arrayResponse;
    }
    
    /**
     * Funcion que sirve para obtener la información de elementos dado el tipo a buscar
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 30-08-2016
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.1 04-04-2019 - Se obtiene el parámetro esBoc para identificar si la petición proviene del departamento Boc-DataCenter.
     *
     * @param Array $arrayData [ data          Informacion requerida para poder obtener la información de los elementos requeridos
     *                           dataAuditoria Información de usuario e ip que realiza la petición ( cliente )
     *                         ]
     * @return Array $arrayResponse
     */
    private function getInformacionElementoPorTipo($arrayData)
    {
        ini_set('max_execution_time', 400000);
                        
        $arrayResponse   = array();
        
        $serviceUtil     = $this->get('schema.Util');
        
        $emInfraestructura = $this->getDoctrine()->getManager("telconet_infraestructura");
        $emComercial       = $this->getDoctrine()->getManager("telconet");
        
        $objEmpresa = $emComercial->getRepository("schemaBundle:InfoEmpresaGrupo")->findOneByPrefijo(self::TN);

        $strTipo         = isset($arrayData['data']['tipo'])?$arrayData['data']['tipo']:"";
        $idElementoPadre = isset($arrayData['data']['idElementoPadre'])?$arrayData['data']['idElementoPadre']:"";
        $strEsBoc        = isset($arrayData['data']['esBoc']) && !empty($arrayData['data']['esBoc']) ? $arrayData['data']['esBoc'] : "";

        if(!empty($strTipo))
        {
            $objTipoElemento = $emInfraestructura->getRepository("schemaBundle:AdmiTipoElemento")->findOneByNombreTipoElemento($strTipo);
        }
        else
        {
            $arrayResponse['status']     = "ERROR";
            $arrayResponse['mensaje']    = "No existe identificador de Tipo de elemento a Buscar, por favor identificar";
            $arrayResponse['resultado']  = array();
            return $arrayResponse;
        }
        
        if(!is_object($objTipoElemento))
        {
            $arrayResponse['status']     = "ERROR";
            $arrayResponse['mensaje']    = "No existe Información del Tipo de Elemento enviado como parámetro en el Telcos";
            $arrayResponse['resultado']  = array();
            return $arrayResponse;
        }
        
        $strTipoElementoBuscar = $objTipoElemento->getNombreTipoElemento();      
        $strTipoElementoRed    = "";
        
        //Unificando  bloque para obtener informacion de SWITCH o ROUTER
        if($strTipoElementoBuscar == 'SWITCH' || $strTipoElementoBuscar == 'ROUTER')
        {            
            $strTipoElementoRed    = $strTipoElementoBuscar;
            $strTipoElementoBuscar = 'ELEMENTO_RED';
        }       

        $arrayParametros["empresa"]  = $objEmpresa->getId();
        $arrayParametros["strTipo"]  = $strTipoElementoBuscar;
        $arrayParametros["strEsBoc"] = $strEsBoc;

        try
        {
            switch($strTipoElementoBuscar)            
            {
                case "ELEMENTO_RED":
                                       
                    $arrayParametros["tipo"]    = $strTipoElementoRed;
                    
                    $arrayResponse = $emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                                       ->getArrayInformacionSwitchRouter($arrayParametros);                    
                    break;                                    
                    
                case "NODO":                    
                                        
                    $arrayParametros["estado"]  = "Activo";

                    $arrayResponse = $emInfraestructura->getRepository("schemaBundle:InfoElemento")->getArrayElementoNodo($arrayParametros);

                    break;
                     
                case "RACK":
                                                            
                    $arrayResultado = $emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                                        ->getElementosXElementoPadre($idElementoPadre,
                                                                                     $objTipoElemento->getId(),
                                                                                     'Activo'
                                                                                     );    
                    
                    if(isset($arrayResultado['total']) && $arrayResultado['total'] > 0)
                    {                        
                        $arrayElementos = array();
                        
                        foreach($arrayResultado['registros'] as $datos)
                        {
                            $arrayElementos[] = array( 'idElemento'    => $datos->getId(),
                                                       'nombreElemento'=> $datos->getNombreElemento()
                                                     );
                        }
                        $arrayResponse['status']    = "OK";
                        $arrayResponse['mensaje']   = "OK";
                        $arrayResponse['resultado'] = array('total'=>$arrayResultado['total'],'data'=>$arrayElementos);
                    }
                    else
                    {
                        $arrayResponse['status']    = "ERROR";
                        $arrayResponse['mensaje']   = "No existen Racks relacionados al Nodo seleccionado";
                        $arrayResponse['resultado'] = array();
                    }
                        
                    break;
                    
                case "UDRACK":
                                        
                    $arrayResultado = $emInfraestructura->getRepository("schemaBundle:InfoRelacionElemento")
                                                        ->getRegistrosElementosbyPadre($objTipoElemento->getId(),$idElementoPadre,'','');
                    
                    $total = count($arrayResultado);
                    
                    if($total>0)
                    {
                        $arrayElementos = array();
                        
                        foreach($arrayResultado as $objResultado)
                        {
                            $objRelacionElementoUnidad = $emInfraestructura->getRepository('schemaBundle:InfoRelacionElemento')
                                                                           ->findBy(array("elementoIdA" => $objResultado->getId(),
                                                                                          "estado"      => "Activo")
                                                                                   );
                            if($objRelacionElementoUnidad)
                            {
                                $strEstado = 'Ocupado';
                            }
                            else
                            {
                                $strEstado = 'Libre';
                            }
                            
                            $arrayElementos[] = array('idElemento'     => $objResultado->getId(),
                                                      'nombreElemento' => trim($objResultado->getNombreElemento()." / ".$strEstado)
                                                     );
                        }
                        
                        $arrayResponse['status']    = "OK";
                        $arrayResponse['mensaje']   = "OK";
                        $arrayResponse['resultado'] = array('total' => $total , 'data' => $arrayElementos);
                    }
                    else
                    {
                        $arrayResponse['status']    = "ERROR";
                        $arrayResponse['mensaje']   = "No existen Unidades de Rack creadas";
                        $arrayResponse['resultado'] = array();
                    }
                                        
                    break;
                    
                default:
                    $arrayResponse['status']    = "ERROR";
                    $arrayResponse['mensaje']   = "Tipo de Elemento ".$strTipo." no válido/inexistente";
                    $arrayResponse['resultado'] = array();
                    break;
            }                                               
        } 
        catch (\Exception $ex) 
        {     
            $arrayResponse['status']     = 'ERROR';
            $arrayResponse['mensaje']    = 'Error al obtener la información del Tipo de Elemento requerido';            
            $arrayResponse['resultado']  = array();            
        
            $serviceUtil->insertError("Telcos+",
                                      "getInformacionElementoPorTipo",
                                      $ex->getMessage(),
                                      isset($arrayData['dataAuditoria']['usrCreacion'])?$arrayData['dataAuditoria']['usrCreacion']:"root",
                                      isset($arrayData['dataAuditoria']['ipCreacion'])?$arrayData['dataAuditoria']['ipCreacion']:"127.0.0.1"
                                    );         
        }
        
        return $arrayResponse;
    }   
    
   /**
     * Funcion que sirve para devolver los empleados de Telconet para que NW pueda validar los accesos sobre los aplicativos de su juridiscción
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 22-09-2016
     *     
     * @param Array $arrayDataAuditoria [ usrCreacion , ipCreacion ] 
     * @return array [ status , mensaje ]
     */
    public function getInformacionEmpleados($arrayDataAuditoria)
    {
        ini_set('max_execution_time', 400000);
                        
        $arrayResponse   = array();
        
        $serviceUtil     = $this->get('schema.Util');
                
        $emComercial     = $this->getDoctrine()->getManager("telconet");
        
        $objEmpresa      = $emComercial->getRepository("schemaBundle:InfoEmpresaGrupo")->findOneByPrefijo(self::TN);  
        
        $arrayParametros['strTipoRol']    = 'Empleado';
        $arrayParametros['strCodEmpresa'] = $objEmpresa->getId();
        
        try
        {
            $arrayResultado = $emComercial->getRepository("schemaBundle:InfoPersona")->getArrayInformacionEmpleados($arrayParametros);
            
            if($arrayResultado)     
            {
                $arrayResponse['status']    = "OK";
                $arrayResponse['mensaje']   = "OK";
                $arrayResponse['resultado'] = $arrayResultado;
            }
            else
            {
                $arrayResponse['status']    = "ERROR";
                $arrayResponse['mensaje']   = "No se pudo traer la información Empleados";
                $arrayResponse['resultado'] = array();
            }
        } 
        catch (\Exception $ex) 
        {            
            $arrayResponse['status']     = 'ERROR';
            $arrayResponse['mensaje']    = 'Error al obtener la información del Empleados';
            $arrayResponse['resultado']  = array();            
        
            $serviceUtil->insertError("Telcos+",
                                      "getInformacionElementoPorTipo",
                                      $ex->getMessage(),
                                      isset($arrayDataAuditoria['usrCreacion'])?$arrayDataAuditoria['usrCreacion']:"root",
                                      isset($arrayDataAuditoria['ipCreacion'])?$arrayDataAuditoria['ipCreacion']:"127.0.0.1"
                                    );  
        }
        
        return $arrayResponse;
    }       
    
    /**
     * Función que sirve para devolver los modelos posibles activos por SWITCH y ROUTER
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 24-04-2017
     *     
     * @param Array $arrayData [ dataAuditoria , data ] 
     * @return array [ 
     *                 status      Status de la peticion
     *                 mensaje     Mensaje de ERROR en caso de existir
     *                 resultado   Resultado de la petición
     *               ]
     */
    public function getInformacionModeloSwitchRouterTN($arrayData)
    {
        ini_set('max_execution_time', 400000);
        
        $arrayResponse     = array();
        
        $serviceUtil       = $this->get('schema.Util');
        
        $emInfraestructura = $this->getDoctrine()->getManager("telconet_infraestructura");
        
        try
        {
            $objTipoElemento = $emInfraestructura->getRepository("schemaBundle:AdmiTipoElemento")
                                                 ->findOneByNombreTipoElemento($arrayData['data']['tipoElemento']);
            
            if(is_object($objTipoElemento))
            {
                $arrayModelos = $emInfraestructura->getRepository("schemaBundle:AdmiModeloElemento")
                                                  ->getModelosElementos('','',$objTipoElemento->getId(),'Activo','','');

                $arrayModeloSwitchRouter = array();

                foreach($arrayModelos as $objModelos)
                {
                    $arrayModeloSwitchRouter[] = array('idModelo'     => $objModelos->getId(),
                                                       'nombreModelo' => $objModelos->getNombreModeloElemento()
                                                      );
                }

                $arrayResponse['status']    = "OK";
                $arrayResponse['mensaje']   = "OK";
                $arrayResponse['resultado'] = array('total'=>count($arrayModeloSwitchRouter),'data'=>$arrayModeloSwitchRouter);;
            }
            else
            {
                $arrayResponse['status']    = "ERROR";
                $arrayResponse['mensaje']   = "No existe información acerca del Tipo de Elemento enviado";
            }
        } 
        catch (Exception $ex) 
        {
            $arrayResponse['status']     = 'ERROR';
            $arrayResponse['mensaje']    = 'Error al obtener la información del Empleados';
            $arrayResponse['resultado']  = array();            
        
            $serviceUtil->insertError("Telcos+",
                                      "getInformacionModeloSwitchRouterTN",
                                      $ex->getMessage(),
                                      isset($arrayData['dataAuditoria']['usrCreacion'])?$arrayData['dataAuditoria']['usrCreacion']:"root",
                                      isset($arrayData['dataAuditoria']['ipCreacion'])?$arrayData['dataAuditoria']['ipCreacion']:"127.0.0.1"
                                    );
        }
        
        return $arrayResponse;
    }
    

    
    /**
     * Funcion que sirve para obtener el bw de las interfaces de un Elemento de forma totalizada o detallada según el flag
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 09-05-2017
     * 
     * @author Allan Suarez <javera@telconet.ec>
     * @version 1.1 21-07-2017 - Se agrega estado InCorte para obtener servcios que si deben estar en el reporte de configurados
     *                           en determinado puerto
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.2 03-10-2017 - Se agrega nuevo flujo para obtener información mediante el envío de IPS como parametros del ws
     * @since 1.1
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.3 29-11-2017 - Se agregan modificaciones para retornar CAPACIDAD 2 de servicios consultados
     * @since 1.2
     * 
     * @author Ricardo Coello Quezada <rcoello@telconet.ec>
     * @version 1.4 15-01-2018 - Se agregan modificaciones para retornar el LOGIN de servicios consultados
     * @since 1.3
     * 
     * @author Fabricio Bermeo Romero <fbermeo@telconet.ec>
     * @version 1.5 21-02-2018 - Se agregan modificaciones para retornar el estado de los servicios, se elimina el retorno del login
     * @since 1.3
     * 
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.6 21-05-2020 | Se modifica el filtro para que solo valide los servicios activos.
     * 
     * @param array $arrayData
     * @return array $arrayResultado
     */
    private function getBw($arrayData)
    {
        ini_set('max_execution_time', 400000);
        $arrayEncontrados    = array();
        $arrayResultado      = array();        
        $serviceUtil         = $this->get('schema.Util');
        
        try
        {

            $em  = $this->getDoctrine()->getManager();
            //obtengo el parametro del producto marginado
            $objParametro = $em->getRepository('schemaBundle:AdmiParametroDet')->get('PRODUCTO_MARGINADO_REPORTE', '', '', '', '', '', '', '', '', 
                                                                                     '', '');
            foreach ($objParametro as $arrayParametro)
            {
                $arrayNombreParametro [] = $arrayParametro['descripcion'];
            }
            if(is_array($arrayNombreParametro))
            {
                $arrayParametros['arrayProductoMarginado'] = $arrayNombreParametro;
            }
            
            $strFlag  = strtoupper($arrayData['flag']);
            
            if($strFlag != 'TOTAL' && $strFlag != 'DETALLE' )
            {
                throw new \Exception('Favor ingrese el parametro FLAG debe contener TOTAL o DETALLE.');
            }                    
            
            $arrayDataSw = $arrayData['data']['datasws'];
            $arrayDataIps = $arrayData['data']['datasips'];
            
            if (count($arrayDataSw)>0 && count($arrayDataIps) == 0)
            {
                $strProcesoBusqueda = "SW";
            }
            else if (count($arrayDataIps)>0 && count($arrayDataSw) == 0)
            {
                $strProcesoBusqueda = "IP";
            }
            else
            {
                $strProcesoBusqueda = "ERROR";
            }

            if ($strProcesoBusqueda != "ERROR")
            {
                //si el proceso de busqueda es por SW y PTO SW entra por este flujo
                if ($strProcesoBusqueda == "SW")
                {
                    //recorro la data separando por sw
                    foreach($arrayDataSw as $arraySw)
                    {
                        if($arraySw)
                        {
                            $strSw            = $arraySw[0] ;
                            $arrayInterfaces  = $arraySw[1] ;
                            if($strSw)
                            {
                                $objElemento = $em->getRepository('schemaBundle:InfoElemento')->findOneBy(array('nombreElemento'=> $strSw, 
                                                                                                                'estado'        => 'Activo'));
                                //verifico que exista el sw
                                if(is_object($objElemento))
                                {
                                    //recorro las interfaces del sw
                                    foreach ($arrayInterfaces as $strInterface )
                                    {

                                        $objInterfaceElemento = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                   ->findOneBy(array('elementoId'               => $objElemento->getId(),
                                                                                     'nombreInterfaceElemento'  => trim($strInterface)));
                                        //verifico que existan las interfaces
                                        if(is_object($objInterfaceElemento))
                                        {
                                            $arrayParametros['intInterfaceElemento'] = $objInterfaceElemento->getId();
                                            $arrayParametros['arrayEstados']         = array('Activo');
                                            //segun el flag realizo la consulto y armo el array
                                            if($strFlag == 'TOTAL')
                                            {
                                                $arrayParametros['tipo'] = '1';
                                                //Se consulta por los estados Activo, EnPruebas, In-Corte
                                                $arrayCapacidades = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                       ->getCapacidadesPorInterface($arrayParametros);

                                                if(is_array($arrayCapacidades))
                                                {
                                                    $arrayEncontrados[] = array('nombreElemento'  => $strSw,
                                                                                'nombreInterface' => $strInterface,
                                                                                'bwSubida'        => $arrayCapacidades['totalCapacidad1'],
                                                                                'bwBajada'        => $arrayCapacidades['totalCapacidad2'],
                                                                                'estado'          => $arrayCapacidades['estado']);
                                                }
                                                else
                                                {
                                                    //Si no obtengo resultados, busco el ultimo login que estuvo en el SW y Puerto,
                                                    //saco su BW  para revisión de inconsistencias(deberia traer 0)
                                                    $arrayParametros['arrayEstados'] = array('Cancel');
                                                    $arrayParametros['tipo'] = '2';
                                                    $arrayCapacidades = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                       ->getCapacidadesPorInterface($arrayParametros);
                                                    if(is_array($arrayCapacidades))
                                                    {
                                                        $arrayEncontrados[] = array('nombreElemento'  => $strSw,
                                                                                'nombreInterface' => $strInterface,
                                                                                'bwSubida'        => $arrayCapacidades['totalCapacidad1'],
                                                                                'bwBajada'        => $arrayCapacidades['totalCapacidad2'],
                                                                                'estado'          => $arrayCapacidades['estado']);
                                                    }
                                                    else
                                                    {
                                                        $arrayEncontrados[] = array('nombreElemento'  => $strSw,
                                                                                'nombreInterface' => $strInterface,
                                                                                'bwSubida'        => '0',
                                                                                'bwBajada'        => '0',
                                                                                'estado'          => 'CANCELADO');
                                                    }
                                                }
                                            }
                                            else
                                            {

                                                $arrayCapacidades = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                       ->getServiciosPorInterfaceElemento($arrayParametros);

                                                $arrayData = array("nombreElemento"=> $strSw, "nombreInterface"=> $strInterface);

                                                if(count($arrayCapacidades)>0)
                                                {
                                                    foreach ($arrayCapacidades as $arrayCapacidad)
                                                    {
                                                        $arrayEncontrados[] = array_merge($arrayData, $arrayCapacidad);
                                                    }

                                                }
                                                else
                                                {
                                                    $arrayEncontrados[] = array('nombreElemento'  => $strSw,
                                                                                'nombreInterface' => $strInterface,
                                                                                'bwSubida'        => '0',
                                                                                'bwBajada'        => '0',
                                                                                'estado'          => '');                                        
                                                }                                        
                                            }                                   
                                        }
                                        else
                                        {
                                            $arrayEncontrados[] = array('nombreElemento' => $strSw,
                                                                       'nombreInterface' => $strInterface,
                                                                       'bwSubida'        => '-2',
                                                                       'bwBajada'        => '-2',
                                                                       'estado'          => '');
                                        }
                                    }                                                           
                                }
                                else
                                {
                                    $arrayEncontrados[] = array('nombreElemento' => $arraySw[0],
                                                                'nombreInterface' => $arraySw[1],
                                                                'bwSubida'        => '-1',
                                                                'bwBajada'        => '-1',
                                                                'estado'          => '');
                                }
                            }
                            else
                            {
                                $arrayEncontrados[] = array('nombreElemento'  => $arraySw[0],
                                                            'nombreInterface' => $arraySw[1],
                                                            'bwSubida'        => '-1',
                                                            'bwBajada'        => '-1',
                                                            'estado'          => '');
                            }
                        }

                    }
                }
                else//si el proceso de busqueda es IP entra por este flujo
                {
                    //recorro la data separando por sw
                    foreach($arrayDataIps as $strDataIp)
                    {
                        $arrayParametros['strIp']           = $strDataIp;
                        $arrayParametros['arrayEstadosIps'] = array('Activo');
                        $arrayParametros['arrayEstados']    = array('Activo');
                        $arrayData = array("ip" => $strDataIp);
                        if($strFlag == 'TOTAL')
                        {
                            
                            $arrayCapacidades = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                                   ->getCapacidadesPorIp($arrayParametros);
                            if(is_array($arrayCapacidades))
                            {
                                $arrayEncontrados[] = array('ip'       => $strDataIp,
                                                            'bwSubida' => $arrayCapacidades['totalCapacidad1'],
                                                            'bwBajada' => $arrayCapacidades['totalCapacidad2']);
                            }
                            else
                            {
                                $arrayEncontrados[] = array('ip'       => $strDataIp,
                                                            'bwSubida' => '0',
                                                            'bwBajada' => '0');
                            }
                        }
                        else
                        {
                            $arrayCapacidades = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                                   ->getServiciosPorIp($arrayParametros);
                            if(count($arrayCapacidades)>0)
                            {
                                foreach ($arrayCapacidades as $arrayCapacidad)
                                {
                                    $arrayEncontrados[] = array_merge($arrayData, $arrayCapacidad);
                                }

                            }
                            else
                            {
                                $arrayEncontrados[] = array('ip'       => $strDataIp,
                                                            'bwSubida' => '0',
                                                            'bwBajada' => '0');                                        
                            }  
                        }
                    }
                }
            }
            else
            {
                $arrayResultado['status']  = $this->status['ERROR'];
                $arrayResultado['mensaje'] = "Error en parametros ingresados.";
                return $arrayResultado;
            }
        }
        catch(\Exception $e)
        {
            $arrayResultado['status']  = $this->status['ERROR'];
            $arrayResultado['mensaje'] = $e->getMessage();
            
            $serviceUtil->insertError("Telcos+",
                                      "getBw",
                                      $e->getMessage(),
                                      $arrayData['usrCreacion'],
                                      $arrayData['ipCreacion']
                                     );            
            return $arrayResultado;
        }
        
        $arrayResultado['data']               = $arrayEncontrados;
        $arrayResultado['status']             = $this->status['OK'];
        $arrayResultado['mensaje']            = $this->mensaje['OK'];
        return $arrayResultado;
    }    
    
    /**
     * Funcion que sirve para obtener la información física de un Elemento relacionado a Nodo, Rack, UdRack
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 24-05-2017
     * 
     * @param array $arrayData
     * @return array $arrayResultado
     */
    public function getInformacionEspacialDispositivo($arrayData)
    {
        ini_set('max_execution_time', 400000);
        
        $arrayResponse     = array();
        
        $serviceUtil       = $this->get('schema.Util');
        
        $emInfraestructura = $this->getDoctrine()->getManager("telconet_infraestructura");
        
        try
        {
            $objElemento = $emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                             ->findOneByNombreElemento($arrayData['data']['nombreElemento']);
            
            if(is_object($objElemento))
            {
                $arrayParametrosDatos                      = array();
                $arrayParametrosDatos['strNombreElemento'] = $arrayData['data']['nombreElemento'];
                $arrayParametrosDatos['strTipoElemento']   = $arrayData['data']['tipoElemento'];
                
                $arrayDatos = $emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                                ->getInformacionFisicaElemento($arrayParametrosDatos);
                //Se obtiene la informacion a ser enviada
                if(!empty($arrayDatos))
                {
                    $arrayResponse['status']    = "OK";
                    $arrayResponse['mensaje']   = "OK";
                    $arrayResponse['resultado'] = $arrayDatos;
                }
                else
                {
                    $arrayResponse['status']    = "ERROR";
                    $arrayResponse['mensaje']   = "No se encontró información física del elemento requerido, notificar a Sistemas";
                }
            }
            else
            {
                $arrayResponse['status']    = "ERROR";
                $arrayResponse['mensaje']   = "No existe información acerca del Elemento enviado";
            }
        } 
        catch (\Exception $ex) 
        {
            $arrayResponse['status']     = 'ERROR';
            $arrayResponse['mensaje']    = 'Error al obtener la información física del elemento requerido';
            $arrayResponse['resultado']  = array();            
        
            $serviceUtil->insertError("Telcos+",
                                      "getInformacionEspacialDispositivo",
                                      $ex->getMessage(),
                                      isset($arrayData['dataAuditoria']['usrCreacion'])?$arrayData['dataAuditoria']['usrCreacion']:"root",
                                      isset($arrayData['dataAuditoria']['ipCreacion'])?$arrayData['dataAuditoria']['ipCreacion']:"127.0.0.1"
                                    );
        }
        
        return $arrayResponse;
    }

    /**
     * Documentación para el método 'eliminarElementoSwitchStackTN'.
     * 
     * Metodo para eliminar los elementos de tipos SWITCH, STACK o interfaces de SWITCH/STACK
     * 
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 02-10-2019
     * 
     * @param Array $arrayData [
     *                              dataAuditoria [ usrCreacion , ipCreacion ]  Informacion de auditoria enviado
     *                              data [
     *                                  tipoElemento,       Tipo del elemento SWITCH/STACK
     *                                  nombre,             Nombre del elemento SWITCH/STACK
     *                                  interfaces          Arreglo de las interfaces de los elementos SWITCH/STACK
     *                              ]
     *                         ]
     * @return Array $arrayResponse [ status , mensaje ]
     */
    private function eliminarElementoSwitchStackTN($arrayData)
    {
        ini_set('max_execution_time', 400000);
        
        $serviceElemento    = $this->get('tecnico.InfoElemento'); 
        $serviceUtil        = $this->get('schema.Util');
        
        $strUsrCreacion     = $arrayData['dataAuditoria']['usrCreacion'];
        $strIpCreacion      = $arrayData['dataAuditoria']['ipCreacion'];
        
        try
        {               
            $arrayDatosElementos                = $arrayData['data'];
            $arrayDatosElementos['usrCreacion'] = $strUsrCreacion;
            $arrayDatosElementos['ipCreacion']  = $strIpCreacion;

            if(
               (isset($arrayDatosElementos['usrCreacion']) && !empty($arrayDatosElementos['usrCreacion'])) &&
               (isset($arrayDatosElementos['ipCreacion']) && !empty($arrayDatosElementos['ipCreacion']))
              )
            {
                $arrayResponse       = $serviceElemento->eliminarElementoSwitchStackTN($arrayDatosElementos);
            }
            else
            {
                $arrayResponse = array(
                    'status'  => 'ERROR',
                    'mensaje' => 'No existe información de Usuario ni Ip de Creación del registro'
                );
            }
        } 
        catch (\Exception $ex) 
        {
            $serviceUtil->insertError("Telcos+",
                                      "AdministrarDispositivosWSController.eliminarElementoSwitchStackTN",
                                      $ex->getMessage(),
                                      $strUsrCreacion,
                                      $strIpCreacion
                                    );
            $arrayResponse = array(
                'status'  => 'ERROR',
                'mensaje' => 'Error al eliminar el elemento SWITCH, STACK o interfaces de SWITCH/STACK'
            );
        }
        
        return $arrayResponse;
    }

    /**
     * Documentación para el método 'updateInterfacesElementoSwitchTN'.
     * 
     * Metodo para verificar y actualizar los estados de las interfaces del elemento SWITCH
     * 
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 02-10-2019
     * 
     * @param Array $arrayData [
     *                              dataAuditoria [ usrCreacion , ipCreacion ]  Informacion de auditoria enviado
     *                              data [
     *                                  tipoElemento,       Tipo del elemento SWITCH
     *                                  nombre,             Nombre del elemento SWITCH
     *                                  interfaces          Arreglo de las interfaces del elemento SWITCH
     *                              ]
     *                         ]
     * @return Array $arrayResponse [ status , mensaje ]
     */
    private function updateInterfacesElementoSwitchTN($arrayData)
    {
        ini_set('max_execution_time', 400000);
                        
        $serviceElemento    = $this->get('tecnico.InfoElemento'); 
        $serviceUtil        = $this->get('schema.Util');
        
        $strUsrCreacion     = $arrayData['dataAuditoria']['usrCreacion'];
        $strIpCreacion      = $arrayData['dataAuditoria']['ipCreacion'];
        
        try
        {
            $arrayDatosElementos                = $arrayData['data'];
            $arrayDatosElementos['usrCreacion'] = $strUsrCreacion;
            $arrayDatosElementos['ipCreacion']  = $strIpCreacion;

            if(
               (isset($arrayDatosElementos['usrCreacion']) && !empty($arrayDatosElementos['usrCreacion'])) &&
               (isset($arrayDatosElementos['ipCreacion']) && !empty($arrayDatosElementos['ipCreacion']))
              )
            {
                $arrayResponse  = $serviceElemento->updateInterfacesElementoSwitchTN($arrayDatosElementos);
            }
            else
            {
                $arrayResponse  = array(
                    'status'  => 'ERROR',
                    'mensaje' => 'No existe información de Usuario ni Ip de Creación del registro'
                );
            }
        } 
        catch (\Exception $ex) 
        {
            $serviceUtil->insertError("Telcos+",
                                      "AdministrarDispositivosWSController.updateInterfacesElementoSwitchTN",
                                      $ex->getMessage(),
                                      $strUsrCreacion,
                                      $strIpCreacion
                                    );
            $arrayResponse = array(
                'status'  => 'ERROR',
                'mensaje' => 'Error al verificar y actualizar los estados de las interfaces del elemento SWITCH'
            );
        }
        
        return $arrayResponse;
    }

    /**
     * Documentación para el método 'updateCambioDispositivoElementoSwitchTN'.
     * 
     * Metodo para crear la solicitud del cambio de ultima milla de las interfaces de un mismo elemento SWITCH
     * o a otras interfaces de otro elemento SWITCH
     * 
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 02-10-2019
     * 
     * @param Array $arrayData [
     *                              dataAuditoria [ usrCreacion , ipCreacion ]  Informacion de auditoria enviado
     *                              data [
     *                                  tipoElemento,       Tipo del elemento SWITCH
     *                                  nombreAnterior,     Nombre anterior del elemento SWITCH
     *                                  nombreNuevo,        Nombre nuevo del elemento SWITCH
     *                                  interfaces          Arreglo de las interfaces que se actualizaran de los dos elementos SWITCH
     *                              ]
     *                         ]
     * @return Array $arrayResponse [ status , mensaje ]
     */
    private function updateCambioDispositivoElementoSwitchTN($arrayData)
    {
        ini_set('max_execution_time', 400000);
                        
        $serviceElemento    = $this->get('tecnico.InfoElemento'); 
        $serviceUtil        = $this->get('schema.Util');
        
        $strUsrCreacion     = $arrayData['dataAuditoria']['usrCreacion'];
        $strIpCreacion      = $arrayData['dataAuditoria']['ipCreacion'];
        
        try
        {               
            $arrayDatosElementos                = $arrayData['data'];
            $arrayDatosElementos['usrCreacion'] = $strUsrCreacion;
            $arrayDatosElementos['ipCreacion']  = $strIpCreacion;

            if(
               (isset($arrayDatosElementos['usrCreacion']) && !empty($arrayDatosElementos['usrCreacion'])) &&
               (isset($arrayDatosElementos['ipCreacion']) && !empty($arrayDatosElementos['ipCreacion']))
              )
            {
                $arrayResponse       = $serviceElemento->updateCambioDispositivoElementoSwitchTN($arrayDatosElementos);
            }
            else
            {
                $arrayResponse = array(
                    'status'  => 'ERROR',
                    'mensaje' => 'No existe información de Usuario ni Ip de Creación del registro'
                );
            }
        } 
        catch (\Exception $ex) 
        {
            $serviceUtil->insertError("Telcos+",
                                      "AdministrarDispositivosWSController.updateCambioDispositivoElementoSwitchTN",
                                      $ex->getMessage(),
                                      $strUsrCreacion,
                                      $strIpCreacion
                                    );
            $arrayResponse = array(
                'status'  => 'ERROR',
                'mensaje' => 'Error al realizar el cambio de ultima milla de las interfaces del elemento SWITCH'
            );
        }
        
        return $arrayResponse;
    }

    /**
     * Documentación para el método 'activarElementoOltMultiplataformaTN'.
     *
     * Metodo para activar el olt multiplataforma
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 30-03-2021
     *
     * @param Array $arrayData [
     *                              dataAuditoria [ usrCreacion , ipCreacion ]  Informacion de auditoria enviado
     *                              data [
     *                                  nombre_olt,    Nombre del olt
     *                              ]
     *                         ]
     * @return Array $arrayResponse [ status , mensaje ]
     */
    private function activarElementoOltMultiplataformaTN($arrayData)
    {
        ini_set('max_execution_time', 400000);
        $serviceElemento = $this->get('tecnico.InfoElemento');
        $serviceUtil     = $this->get('schema.Util');
        $strUsrCreacion  = $arrayData['dataAuditoria']['usrCreacion'];
        $strIpCreacion   = $arrayData['dataAuditoria']['ipCreacion'];

        try
        {
            $arrayDatosElementos                = $arrayData['data'];
            $arrayDatosElementos['usrCreacion'] = $strUsrCreacion;
            $arrayDatosElementos['ipCreacion']  = $strIpCreacion;

            if(!isset($arrayDatosElementos['usrCreacion']) || empty($arrayDatosElementos['usrCreacion']))
            {
                throw new \Exception('No existe información de usuario de la operación');
            }
            if(!isset($arrayDatosElementos['ipCreacion']) || empty($arrayDatosElementos['ipCreacion']))
            {
                throw new \Exception('No existe información de la dirección ip de la operación');
            }
            if(!isset($arrayDatosElementos['nombre_olt']) || empty($arrayDatosElementos['nombre_olt']))
            {
                throw new \Exception('No existe información del nombre del Olt');
            }
            //activar olt multiplataforma
            $arrayResponse = $serviceElemento->activarElementoOltMultiplataformaTN($arrayDatosElementos);
        } 
        catch (\Exception $ex) 
        {
            $arrayResponse = array(
                'status'  => 'ERROR',
                'mensaje' => $ex->getMessage()
            );
            $serviceUtil->insertError("Telcos+",
                                      "AdministrarDispositivosWSController.activarElementoOltMultiplataformaTN",
                                      $ex->getMessage(),
                                      $strUsrCreacion,
                                      $strIpCreacion
                                    );
        }

        return $arrayResponse;
    }

    /**
     * Documentación para el método 'procesarPromocionesBwOlt'.
     *
     * Metodo encargado de ejecutar los procesos de las promociones de ancho de banda del olt
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 04-12-2021
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.1 29-04-2022 - Se valida el nuevo formato del json con los arreglos de los olts
     *
     * @param Array $arrayData [
     *                              dataAuditoria [ usrCreacion , ipCreacion ]  Informacion de auditoria enviado
     *                              data [
     *                                  opcion,     Opción de la operación Procesar o Detener
     *                                  id_promo,   id de la promoción
     *                                  elemento,   Arreglo de datos de los olt
     *                              ]
     *                         ]
     * @return Array $arrayResponse [ status , mensaje ]
     */
    private function procesarPromocionesBwOlt($arrayData)
    {
        ini_set('max_execution_time', 400000);
        $emComercial     = $this->getDoctrine()->getManager("telconet");
        $serviceUtil     = $this->get('schema.Util');
        $strUsrCreacion  = $arrayData['dataAuditoria']['usrCreacion'];
        $strIpCreacion   = $arrayData['dataAuditoria']['ipCreacion'];

        try
        {
            $arrayDatosElementos = $arrayData['data'];
            if(!isset($arrayDatosElementos['opcion']) || empty($arrayDatosElementos['opcion']))
            {
                throw new \Exception('No existe información de la opción de la operación');
            }
            if(!isset($arrayDatosElementos['id_promo']) || empty($arrayDatosElementos['id_promo']))
            {
                throw new \Exception('No existe información del id de la promoción');
            }
            if(!isset($arrayDatosElementos['elemento']) || !is_array($arrayDatosElementos['elemento']) || empty($arrayDatosElementos['elemento']))
            {
                throw new \Exception('No existe información de los elementos');
            }
            //procesar promociones de ancho de banda
            $arrayResponse = $this->get("comercial.PromocionAnchoBanda")->procesarPromocionesBwOlt($arrayDatosElementos);
        }
        catch (\Exception $ex)
        {
            $arrayResponse = array(
                'status'  => 'ERROR',
                'mensaje' => $ex->getMessage()
            );
            $serviceUtil->insertError("Telcos+",
                                      "AdministrarDispositivosWSController.procesarPromocionesBwOlt",
                                      $ex->getMessage(),
                                      $strUsrCreacion,
                                      $strIpCreacion
                                    );
        }

        return $arrayResponse;
    }
    
}

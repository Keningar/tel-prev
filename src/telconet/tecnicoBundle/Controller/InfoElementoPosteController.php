<?php

namespace telconet\tecnicoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use telconet\schemaBundle\Entity\InfoElemento;
use telconet\schemaBundle\Entity\InfoEmpresaElemento;
use telconet\schemaBundle\Entity\InfoEmpresaElementoUbica;
use telconet\schemaBundle\Entity\InfoRelacionElemento;
use telconet\schemaBundle\Entity\InfoHistorialElemento;
use telconet\schemaBundle\Entity\InfoUbicacion;
use telconet\schemaBundle\Entity\InfoDetalleElemento;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Documentación para la clase 'InfoElementoPoste'.
 *
 * Clase utilizada para manejar metodos que permiten realizar acciones de administracion de elementos Postes
 *
 * @author Sofía Fernández <sfernandez@telconet.ec>          
 * @version 1.0 09-02-2017
 */

class InfoElementoPosteController extends Controller
{   
   /**
    * Documentación para el método 'indexPosteAction'.
    *
    * Metodo utilizado para retornar a la pagina principal de la administracion de Postes
    *
    * @author Sofía Fernández <sfernandez@telconet.ec>          
    * @version 1.0 09-02-2017
    */
    public function indexPosteAction()
    {
        $rolesPermitidos = array();

        //MODULO 373 - POSTE
        //Rol 7063 - Administracion de Postes
        //Accion 5177 - administrarPoste
        $objRequest         = $this->getRequest();
        $objSession         = $objRequest->getSession();
        $strNombreElemento  = $objSession->get('strNombreElemento');
        if(isset($strNombreElemento) && !empty($strNombreElemento))
        {
            $objSession->remove('strNombreElemento');
        }

        if (true === $this->get('security.context')->isGranted('ROLE_373-5177'))
        {
            $rolesPermitidos[] = 'ROLE_373-5177';
        }

        return $this->render('tecnicoBundle:InfoElementoPoste:index.html.twig',
                                array('rolesPermitidos'   => $rolesPermitidos,
                                      'strNombreElemento' => $strNombreElemento));

    }
    
   /**
     * Documentación para el método 'ajaxGetEncontradosPosteAction'.
     *
     * Metodo utilizado para obtener información de los Postes
     * 
     * @param Object  $objRespuesta con informacion del elemento de acuerdo a la petición.
     * 
     * @author Sofía Fernández <sfernandez@telconet.ec>          
     * @version 1.0 11-02-2017
     * 
     * @author Sofía Fernández <sfernandez@telconet.ec>          
     * @version 1.1 11-02-2017 
     * Se agrega a los parametros el código de la empresa.
     */
    public function ajaxGetEncontradosPosteAction()
    {
        $objRespuesta       = new JsonResponse();
        $objSession         = $this->get('session');
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');
        $objTipoElemento    = $emInfraestructura->getRepository('schemaBundle:AdmiTipoElemento')->findOneBy(array("nombreTipoElemento" => "POSTE"));
        $objPeticion        = $this->get('request');
        
        $arrParametros                =  array();
        $arrParametros['TIPO']        =  is_object($objTipoElemento)?$objTipoElemento->getId():'';
        $arrParametros['NOMBRE_TIPO'] =  is_object($objTipoElemento)?$objTipoElemento->getNombreTipoElemento():'';
        $arrParametros['ELEMENTO']    =  $objPeticion->query->get('strCodigo');
        $arrParametros['PROPIETARIO'] =  $objPeticion->query->get('strPropietario');
        $arrParametros['CANTON']      =  $objPeticion->query->get('strCanton');
        $arrParametros['ESTADO']      =  $objPeticion->query->get('strEstado');
        $arrParametros['EMPRESA']     =  $objSession->get('prefijoEmpresa');
        $arrParametros['IDEMPRESA']   =  $objSession->get('idEmpresa');
        $arrParametros['START']       =  $objPeticion->query->get('start');
        $arrParametros['LIMIT']       =  $objPeticion->query->get('limit');

        $strJson = $this->getDoctrine()
                        ->getManager("telconet_infraestructura")
                        ->getRepository('schemaBundle:InfoElemento')
                        ->generarJsonElementosEmpresa($arrParametros);

        $objRespuesta->setContent($strJson);
        
        return $objRespuesta;
    }
    
   /**
    * Documentación para el método 'ajaxGetEncontradosPropietarioAction'.
    *
    * Metodo utilizado para obtener información de los Propietarios
    * 
    * @param Object $objRespuesta con informacion del propietario de acuerdo a la petición.
    *
    * @author Sofía Fernández <sfernandez@telconet.ec>          
    * @version 1.0 16-02-2017
    */
    public function ajaxGetEncontradosPropietarioAction()
    {   
        $objRespuesta                  = new JsonResponse();
        $objSession                    = $this->get('session');
        $arrResult                     = array();
        $arrayResultado                = array();
        $arrParametros                 = array();
        $arrParametros['estado']       = 'Activo';
        $arrParametros['idEmpresa']    = $objSession->get('idEmpresa');
        $arrParametros['tipo_persona'] = 'Propietario';
    
        $arrPersona = $this->getDoctrine()
                           ->getManager("telconet_infraestructura")
                           ->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                           ->findPersonasPorCriterios($arrParametros);
        
        foreach ($arrPersona['registros'] as $arrayPersona)
        {
            $arrayItem                       = array();
            $arrayItem['nombre_propietario'] = $arrayPersona['razon_social'];
            $arrayItem['id_propietario']     = $arrayPersona['id'];   
            $arrResult[]                     = $arrayItem;
        }

        $arrayResultado['total']              = count($arrResult);
        $arrayResultado['encontrados']        = $arrResult;
        $objRespuesta->setData($arrayResultado);

        return $objRespuesta;
        
    }
    
   /**
    * Documentación para el método 'ajaxGetHistorialElementoAction'.
    *
    * Metodo utilizado para obtener historia de los elementos
    * 
    * @param Request $request
    * @param Object $objRespuesta con informacion del historico del elemento.
    *
    * @author Sofía Fernández <sfernandez@telconet.ec>          
    * @version 1.0 22-02-2017
    * 
    * @author John Vera <javera@telconet.ec>          
    * @version 1.1 20-09-2017 Se ordenaron los registros
    */
    public function ajaxGetHistorialElementoAction(Request $request)
    {
        
        $objRespuesta         = new JsonResponse;
        $arrayEncontrados     = array();
        $arrayResultado       = array();
        $emInfraestructura    = $this->getDoctrine()->getManager('telconet_infraestructura');
        $intElementoId        = $request->get('idElemento');
     
        $arrHistorialElemento = $emInfraestructura->getRepository('schemaBundle:InfoHistorialElemento')
                                                  ->findBy(array("elementoId" => $intElementoId),
                                                           array('feCreacion' => 'DESC'));
        
        if (is_array($arrHistorialElemento))
        {
        
            foreach ($arrHistorialElemento as $objElemento)
            {
                $arrayItem                    = array();
                $arrayItem['estado_elemento'] = $objElemento->getEstadoElemento();
                $arrayItem['fe_creacion']     = strval(date_format($objElemento->getFeCreacion(), "d/m/Y G:i"));
                $arrayItem['usr_creacion']    = $objElemento->getUsrCreacion();
                $arrayItem['observacion']     = $objElemento->getObservacion();
                $arrayEncontrados[]           = $arrayItem;
            }
        }
    
        $arrayResultado['total']           = count($arrayEncontrados);
        $arrayResultado['encontrados']     = $arrayEncontrados;
        $objRespuesta->setData($arrayResultado);

        return $objRespuesta;
    }
    
   /**
    * Documentación para el método 'newPosteAction'.
    *
    * Metodo utilizado para direccionar al js de creacion de poste.
    *
    * @author Sofia Fernandez <sfernandez@telconet.ec>
    * @version 1.0 22-02-2017
    */
    public function newPosteAction()
    {
        return $this->render('tecnicoBundle:InfoElementoPoste:new.html.twig');
    }
    
   /**
    * Documentación para el método 'ajaxGetCantonJurisdiccionAction'.
    *
    * Metodo utilizado para obtener información de los Cantones por jurisdiccion
    * 
    * @param Request $request
    * @param Object  $objRespuesta con informacion de cantones por jurisdiccion.
    *
    * @author Sofía Fernández <sfernandez@telconet.ec>          
    * @version 1.0 25-02-2017
    */
    public function ajaxGetCantonJurisdiccionAction(Request $request)
    {
        $objRespuesta      = new JsonResponse();
        $intJurisdiccionId = $request->get('jurisdiccionId');
        
        $strJson = $this->getDoctrine()
                        ->getManager("telconet_infraestructura")
                        ->getRepository('schemaBundle:AdmiCantonJurisdiccion')
                        ->generarJsonCantonesJurisdicciones($intJurisdiccionId,"Activo",'','');
        $objRespuesta->setContent($strJson);
        return $objRespuesta;
    }
    
    /**
    * Documentación para el método 'getTipoElementoAction'.
    *
    * Metodo utilizado para obtener información de los tipos de elemento
    * 
    * @param Request $request
    * @param Object  $objRespuesta con informacion de los tipos de elementos.
    *
    * @author Sofía Fernández <sfernandez@telconet.ec>          
    * @version 1.0 22-03-2017
    */
    public function getTipoElementoAction(Request $request)
    {
        $objRespuesta                = new JsonResponse();
        $objSession                  = $this->get('session');
        $arrParametros               = array();
        $arrParametros['nombre']     = $request->get('nombre');
        $arrParametros['estado']     = $request->get('estado');
        $arrParametros['codEmpresa'] = $objSession->get('idEmpresa');
        
 
        $strJson = $this->getDoctrine()
                        ->getManager("telconet_infraestructura")
                        ->getRepository('schemaBundle:AdmiTipoElemento')
                        ->generarJsonTiposElementos($arrParametros);
        $objRespuesta->setContent($strJson);
        return $objRespuesta;
    }
    
   /**
    * Documentación para el método 'ajaxGetParroquiaCantonAction'.
    *
    * Metodo utilizado para obtener información de Parroquias por Cantones
    * 
    * @param Request $request
    * @param Object  $objRespuesta con informacion de parroquia por canton.
    *
    * @author Sofía Fernández <sfernandez@telconet.ec>          
    * @version 1.0 03-03-2017
    */
    public function ajaxGetParroquiaCantonAction(Request $request)
    {    
        $objRespuesta = new JsonResponse();
        $intCantonId  = $request->get('cantonId');
        
        $strJson = $this->getDoctrine()
                        ->getManager("telconet_general")
                        ->getRepository('schemaBundle:AdmiParroquia')
                        ->generarJsonParroquiasPorCanton($intCantonId,"Activo",'','');
        $objRespuesta->setContent($strJson);
        return $objRespuesta;
    }
    
   /**
    * Documentación para el método 'ajaxCargarElementosContenidosAction'.
    * 
    * Metodo que devuelve los resultados de todos los elementos contenidos en un POSTE determinado
    *
    * @param Object  $objRespuesta con informacion de elementos relacionados al poste
    * 
    * @author Sofia Fernandez <sfernandez@telconet.ec>
    * @version 1.0
    *
    * @author Germán Valenzuela <gvalenzuela@telconet.ec>
    * @version 1.1 08-04-2021 - Se modifica los parámetros al momento de llamar el proceso 'getJsonElementosContenidosNodo'.
    */
    public function ajaxCargarElementosContenidosAction()
    {
        $objRespuesta    = new JsonResponse();
        $objPeticion     = $this->get('request');
        $intIdNodo       = $objPeticion->get('idNodo');
        $strTipoElemento = $objPeticion->get('strTipoElemento');

        $arrayParametros = array();
        $arrayParametros['intIdNodo']       = $intIdNodo;
        $arrayParametros['strTipoElemento'] = $strTipoElemento;

        $strJson = $this->getDoctrine()->getManager("telconet_infraestructura")
                        ->getRepository('schemaBundle:InfoElemento')
                        ->getJsonElementosContenidosNodo($arrayParametros);

        $objRespuesta->setContent($strJson);
        return $objRespuesta;
    }

   /**
    * Documentación para el método 'ajaxSavePosteAction'.
    *
    * Metodo utilizado para crear un poste
    * 
    * @param Object  $objRespuesta mensajes informativos.
    * 
    * @author Sofia Fernandez <sfernandez@telconet.ec>
    * @version 1.0 08-03-2017
    * 
    * @author Lizbeth Cruz <mlcruz@telconet.ec>
    * @version 1.1 18-09-2018 - Se agrega la validación de las coordenadas de latitud y longitud de acuerdo al país en sesión
    */
    public function ajaxSavePosteAction() {
      
        $emInfraestructura = $this->get('doctrine')->getManager('telconet_infraestructura');
        $objResponse       = new JsonResponse();
        $objRequest        = $this->get('request');
        $objSession        = $objRequest->getSession();
        $serviceUtil       = $this->get('schema.Util');
       
        $arrayParametros                           = array();
        $arrayParametros['strNombreElemento']      = $objRequest->get('objTxtNombreElemento');
        $arrayParametros['strPropietario']         = $objRequest->get('objCmbPropietario');
        $arrayParametros['strDescripcionelemento'] = $objRequest->get('objTarDescripcionElemento');
        $arrayParametros['strTipoElemento']        = $objRequest->get('objCmbTipoElemento');
        $arrayParametros['strJurisdiccion']        = $objRequest->get('objCmbJurisdiccion');
        $arrayParametros['strCanton']              = $objRequest->get('objCmbCanton');
        $arrayParametros['strParroquia']           = $objRequest->get('objCmbParroquia');
        $arrayParametros['strDireccion']           = $objRequest->get('objTxtDireccion');
        $arrayParametros['strCosto']               = $objRequest->get('objTxtCosto');
        $arrayParametros['strAltura']              = $objRequest->get('objTxtAltura');
        $arrayParametros['strLatitud']             = $objRequest->get('objTxtLatitudUbicacion');
        $arrayParametros['strLongitud']            = $objRequest->get('objTxtLongitudUbicacion');
        
        //verificar que el nombre del elemento no se repita
        $objModeloElemento = $emInfraestructura->find('schemaBundle:AdmiModeloElemento', $arrayParametros['strTipoElemento']);        
        
        if(is_object($objModeloElemento))
        {
            $objElementoRepetido = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                     ->findOneBy(array("nombreElemento"   => $arrayParametros['strNombreElemento'],
                                                                       "modeloElementoId" => $objModeloElemento->getId(),
                                                                       "estado"           => "Activo"));
            
            if(is_object($objElementoRepetido))
            {
                $arrayResult['strStatus']        = 'ERROR';
                $arrayResult['strMessageStatus'] = 'Nombre ya existe en otro Elemento, favor revisar!';
            }
            else
            {
                $emInfraestructura->getConnection()->beginTransaction();               
                $arrayResult = array();  
                try
                {   
                    $objElemento  = new InfoElemento();
                    $objElemento->setNombreElemento($arrayParametros['strNombreElemento']);
                    $objElemento->setDescripcionElemento($arrayParametros['strDescripcionelemento']);
                    $objElemento->setModeloElementoId($objModeloElemento);
                    $objElemento->setEstado("Activo");
                    $objElemento->setUsrResponsable($objSession->get('user'));
                    $objElemento->setUsrCreacion($objSession->get('user'));
                    $objElemento->setFeCreacion(new \DateTime('now'));
                    $objElemento->setIpCreacion($objRequest->getClientIp());             
                    $emInfraestructura->persist($objElemento);
                    $emInfraestructura->flush();
                    
                    if(is_object($objElemento))
                    {
                        //historial elemento
                        $objHistorialElemento = new InfoHistorialElemento();
                        $objHistorialElemento->setElementoId($objElemento);
                        $objHistorialElemento->setEstadoElemento("Activo");
                        $objHistorialElemento->setObservacion("Se ingresó el  Poste: ". $arrayParametros['strNombreElemento']);
                        $objHistorialElemento->setUsrCreacion($objSession->get('user'));
                        $objHistorialElemento->setFeCreacion(new \DateTime('now'));
                        $objHistorialElemento->setIpCreacion($objRequest->getClientIp());
                        $emInfraestructura->persist($objHistorialElemento);
                        $emInfraestructura->flush();

                        //info ubicacion
                        $objParroquia = $emInfraestructura->find('schemaBundle:AdmiParroquia', $arrayParametros['strParroquia']);
                        if(is_object($objParroquia))
                        {
                            $serviceInfoElemento        = $this->get("tecnico.InfoElemento");
                            $arrayRespuestaCoordenadas  = $serviceInfoElemento->validarLimitesCoordenadasElemento(array(
                                                                                                                    "latitudElemento"   => 
                                                                                                                    $arrayParametros['strLatitud'],
                                                                                                                    "longitudElemento"  => 
                                                                                                                    $arrayParametros['strLongitud'],
                                                                                                                    "msjTipoElemento"   => 
                                                                                                                    "del poste "));
                            if($arrayRespuestaCoordenadas["status"] === "ERROR")
                            {
                                throw new \Exception($arrayRespuestaCoordenadas['mensaje']);
                            }
                            $objUbicacionElemento = new InfoUbicacion();
                            $objUbicacionElemento->setLatitudUbicacion($arrayParametros['strLatitud']);
                            $objUbicacionElemento->setLongitudUbicacion($arrayParametros['strLongitud']);
                            $objUbicacionElemento->setDireccionUbicacion($arrayParametros['strDireccion']);
                            $objUbicacionElemento->setAlturaSnm($arrayParametros['strAltura']);
                            $objUbicacionElemento->setParroquiaId($objParroquia);
                            $objUbicacionElemento->setUsrCreacion($objSession->get('user'));
                            $objUbicacionElemento->setFeCreacion(new \DateTime('now'));
                            $objUbicacionElemento->setIpCreacion($objRequest->getClientIp());
                            $emInfraestructura->persist($objUbicacionElemento);
                            $emInfraestructura->flush();
                            
                            $objEmpresaElementoUbica = new InfoEmpresaElementoUbica();
                            $objEmpresaElementoUbica->setEmpresaCod($objSession->get('idEmpresa'));
                            $objEmpresaElementoUbica->setElementoId($objElemento);
                            $objEmpresaElementoUbica->setUbicacionId($objUbicacionElemento);
                            $objEmpresaElementoUbica->setUsrCreacion($objSession->get('user'));
                            $objEmpresaElementoUbica->setFeCreacion(new \DateTime('now'));
                            $objEmpresaElementoUbica->setIpCreacion($objRequest->getClientIp());
                            $emInfraestructura->persist($objEmpresaElementoUbica);
                            $emInfraestructura->flush();
                            
                            //empresa elemento
                            $objEmpresaElemento = new InfoEmpresaElemento();
                            $objEmpresaElemento->setElementoId($objElemento);
                            $objEmpresaElemento->setEmpresaCod($objSession->get('idEmpresa'));
                            $objEmpresaElemento->setEstado("Activo");
                            $objEmpresaElemento->setUsrCreacion($objSession->get('user'));
                            $objEmpresaElemento->setIpCreacion($objRequest->getClientIp());
                            $objEmpresaElemento->setFeCreacion(new \DateTime('now'));
                            $emInfraestructura->persist($objEmpresaElemento);
                            $emInfraestructura->flush();

                            //caracteristica para saber el propietario del poste
                            $objDetalle = new InfoDetalleElemento();
                            $objDetalle->setElementoId($objElemento->getId());
                            $objDetalle->setDetalleNombre("PROPIETARIO");
                            $objDetalle->setDetalleValor($arrayParametros['strPropietario']);
                            $objDetalle->setDetalleDescripcion("Caracteristicas para indicar propietario del Elemento");
                            $objDetalle->setFeCreacion(new \DateTime('now'));
                            $objDetalle->setUsrCreacion($objSession->get('user'));
                            $objDetalle->setIpCreacion($objRequest->getClientIp());
                            $objDetalle->setEstado('Activo');
                            $emInfraestructura->persist($objDetalle);
                            $emInfraestructura->flush();

                            //caracteristica para saber el costo del poste
                            $objDetalle = new InfoDetalleElemento();
                            $objDetalle->setElementoId($objElemento->getId());
                            $objDetalle->setDetalleNombre("COSTO");
                            $objDetalle->setDetalleValor($arrayParametros['strCosto']);
                            $objDetalle->setDetalleDescripcion("Caracteristicas para indicar costo del Elemento");
                            $objDetalle->setFeCreacion(new \DateTime('now'));
                            $objDetalle->setUsrCreacion($objSession->get('user'));
                            $objDetalle->setIpCreacion($objRequest->getClientIp());
                            $objDetalle->setEstado('Activo');
                            $emInfraestructura->persist($objDetalle);
                            $emInfraestructura->flush();        

                            $arrayResult['strStatus']        = 'OK';
                            $arrayResult['strMessageStatus'] = 'Se ingresa correctamente el elemento poste.';
                        }
                        else
                        {
                            $arrayResult['strStatus']        = 'ERROR';
                            $arrayResult['strMessageStatus'] = 'Parroquia no existe';
                            
                        }
                        $objSession->set('strNombreElemento', $objElemento->getNombreElemento());
                    }
                    else
                    {
                        $arrayResult['strStatus']        = 'ERROR';
                        $arrayResult['strMessageStatus'] = 'Elemento no existe';
                    }
                    $emInfraestructura->commit();
                    
                } 
                catch (\Exception $ex) 
                {   
                    $arrayResult['strStatus']        = 'ERROR';
                    $arrayResult['strMessageStatus'] = 'Error en la creacion de nuevo Poste';

                     if($emInfraestructura->getConnection()->isTransactionActive())
                    {
                        $emInfraestructura->rollback();
                    }

                    $serviceUtil->insertError('Telcos+',
                                              'InfoElementoPosteController->ajaxSavePosteAction',
                                              $ex->getMessage(),
                                              $objSession->get('user'),
                                              $objRequest->getClientIp());

                    $emInfraestructura->close();
                }
            }
        }
        else
        {
            $arrayResult['strStatus']        = 'ERROR';
            $arrayResult['strMessageStatus'] = 'No se encontró modelo del elemento, favor revisar!';
        }
        
        $objResponse->setData($arrayResult);
        return $objResponse;
    }
    
   /**
    * Documentación para el método 'ajaxCargarElementosAction'.
    *
    * Metodo utilizado para obtener información de elementos
    * 
    * @param Request $request
    * @param Object  $objRespuesta Elementos por tipo de material y por canton.
    *
    * @author Sofía Fernández <sfernandez@telconet.ec>          
    * @version 1.0 07-03-2017
    */
    public function ajaxCargarElementosAction(Request $request)
    {    
      
        $objRespuesta = new JsonResponse();
        $objSession   = $this->get('session');
        
        $arrParametros                      = array();       
        $arrParametros['intTipoElementoId'] = $request->get('tipoElementoId');
        $arrParametros['strEstado']         = $request->get('estado');
        $arrParametros['intCantonId']       = $request->get('canton');
        $arrParametros['intEmpresaId']      = $objSession->get('idEmpresa');
      
        $arrRespuesta = $this->getDoctrine()
                             ->getManager("telconet_infraestructura")
                             ->getRepository('schemaBundle:InfoElemento')
                             ->getArrayElementosRelacionPorTipoyCanton($arrParametros);
        $objRespuesta->setData($arrRespuesta);
  
        return $objRespuesta;
    }
    
   /**
    * Documentación para el método 'ajaxDeletePosteAction'.
    *
    * Metodo utilizado para eliminar un poste
    * @param Object  $objResponse mensajes informativos.
    * 
    * @author Sofia Fernandez <sfernandez@telconet.ec>
    * @version 1.0 08-03-2017
    */
    public function ajaxDeletePosteAction() 
    { 
        $objResponse       = new JsonResponse();
        $arrayResult       = array();
        $emInfraestructura = $this->get('doctrine')->getManager('telconet_infraestructura');
        $objRequest        = $this->get('request');
        $objSession        = $objRequest->getSession();
        $serviceUtil       = $this->get('schema.Util');
        
        $arrayParametros                  = array();
        $arrayParametros['intIdElemento'] = $objRequest->get('idElemento');
        
        $emInfraestructura->getConnection()->beginTransaction();
        
        try
        { 
     
            $objElemento = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($arrayParametros['intIdElemento']); 
            
            if (!is_object($objElemento))
            {
                throw $this->createNotFoundException('No se encontró la entidad InfoElemento.');
            }
            
            $arrayInfoRelacionElemento = $emInfraestructura->getRepository('schemaBundle:InfoRelacionElemento')
                                                           ->findBy(array("elementoIdA" => $objElemento->getId()));
            
        
            foreach($arrayInfoRelacionElemento as $objRelacionElemento)
            {
                $strEstadoRelacion = $objRelacionElemento->getEstado();
                
                if ($strEstadoRelacion == 'Activo')
                {
                    $arrayResult['strStatus']        = 'ERROR';
                    $arrayResult['strMessageStatus'] = 'El poste posee elementos relacionados, NO se puede eliminar';
                    $objResponse->setData($arrayResult);
                    return $objResponse;
                }
            }

            //elemento
            $objElemento->setEstado("Eliminado");
            $emInfraestructura->persist($objElemento);

            //historial elemento
            $objHistorialElemento = new InfoHistorialElemento();
            $objHistorialElemento->setElementoId($objElemento);
            $objHistorialElemento->setEstadoElemento("Eliminado");
            $objHistorialElemento->setObservacion("Se ha eliminado el Poste: ".$objElemento->getNombreElemento());
            $objHistorialElemento->setUsrCreacion($objSession->get('user'));
            $objHistorialElemento->setFeCreacion(new \DateTime('now'));
            $objHistorialElemento->setIpCreacion($objRequest->getClientIp());
            $emInfraestructura->persist($objHistorialElemento);
            
            //empresa elemento
            $objEmpresaElemento = $emInfraestructura->getRepository('schemaBundle:InfoEmpresaElemento')
                                                    ->findOneBy(array( "elementoId" =>$arrayParametros['intIdElemento']));
            if(is_object($objEmpresaElemento))
            {
                $objEmpresaElemento->setEstado("Eliminado");
                $emInfraestructura-> persist($objEmpresaElemento); 

                //detalle elemento
                $arrayInfoDetallesElemento = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                               ->findByElementoId($arrayParametros['intIdElemento']);

                foreach($arrayInfoDetallesElemento as $objInfoDetalleElemento)
                {
                    $objInfoDetalleElemento->setEstado('Eliminado');
                    
                    $emInfraestructura->persist($objInfoDetalleElemento);
                    $emInfraestructura->flush();
                }

                $emInfraestructura->flush();
                $emInfraestructura->getConnection()->commit();
                
                $arrayResult['strStatus']        = 'OK';
                $arrayResult['strMessageStatus'] = 'Elemento poste eliminado';
            }
            else
            {
                $arrayResult['strStatus']        = 'ERROR';
                $arrayResult['strMessageStatus'] = 'Error elemento no existe en la empresa.';
            }
        }
        
        catch (\Exception $ex) 
        {   
            $arrayResult['strStatus']        = 'ERROR';
            $arrayResult['strMessageStatus'] = 'Error en la eliminacion de Poste';
           
             if($emInfraestructura->getConnection()->isTransactionActive())
            {
                $emInfraestructura->rollback();
            }
            
            $serviceUtil->insertError('Telcos+',
                                      'InfoElementoPosteController->ajaxDeletePosteAction',
                                      $ex->getMessage(),
                                      $objSession->get('user'),
                                      $objRequest->getClientIp());
            
            $emInfraestructura->close();
        }
        
      
        $objResponse->setData($arrayResult);
        return $objResponse;
        
    }
    
   /**
    * Documentación para el método 'ajaxEditPosteAction'.
    *
    * Metodo utilizado para editar la informacion un poste
    * 
    * @param Object  $objResponse mensajes informativos.
    * 
    * @author Sofia Fernandez <sfernandez@telconet.ec>
    * @version 1.0 08-03-2017
    * 
    * @author Lizbeth Cruz <mlcruz@telconet.ec>
    * @version 1.1 18-09-2018 - Se agrega la validación de las coordenadas de latitud y longitud de acuerdo al país en sesión
    */
    public function ajaxEditPosteAction() 
    {
        $objResponse       = new JsonResponse();  
        $emInfraestructura = $this->get('doctrine')->getManager('telconet_infraestructura');
        $objRequest        = $this->get('request');
        $objSession        = $objRequest->getSession();
        $serviceUtil       = $this->get('schema.Util');
        
        $arrayParametros                           = array();
        $arrayParametros['strNombreElemento']      = $objRequest->get('objTxtNombreElemento');
        $arrayParametros['strPropietario']         = $objRequest->get('objCmbPropietario');
        $arrayParametros['strDescripcionelemento'] = $objRequest->get('objTarDescripcionElemento');
        $arrayParametros['intIdTipoElemento']      = $objRequest->get('objCmbTipoElemento');
        $arrayParametros['strJurisdiccion']        = $objRequest->get('objCmbJurisdiccion');
        $arrayParametros['strCanton']              = $objRequest->get('objCmbCanton');
        $arrayParametros['strDireccion']           = $objRequest->get('objTxtDireccion');
        $arrayParametros['strCosto']               = $objRequest->get('objTxtCosto');
        $arrayParametros['strAltura']              = $objRequest->get('objTxtAltura');
        $arrayParametros['strLatitud']             = $objRequest->get('objTxtLatitudUbicacion');
        $arrayParametros['strLongitud']            = $objRequest->get('objTxtLongitudUbicacion');
        $arrayParametros['intIdUbicacion']         = $objRequest->get('objTxtIdUbicacion');
        $arrayParametros['intIdElemento']          = $objRequest->get('objTxtIdElemento');
        $arrayParametros['intIdParroquia']         = $objRequest->get('objCmbParroquia');
        
        $emInfraestructura->getConnection()->beginTransaction();
        $arrayResult       = array();        
         
        try
        {    
            $objElementoUbica = $emInfraestructura->getRepository('schemaBundle:InfoEmpresaElementoUbica')
                                                  ->findOneByElementoId($arrayParametros['intIdElemento']);
            $intIdParroquia    = 0;
            $strLongitud       = '';
            $strLatitud        = '';
            $strDireccion      = '';
            $strAltura         = '';
            $serviceInfoElemento = $this->get("tecnico.InfoElemento");
                    
            if(is_object($objElementoUbica))
            {
                $objUbicacion = $emInfraestructura->getRepository('schemaBundle:InfoUbicacion')->findOneById($objElementoUbica->getUbicacionId());
                if(is_object($objUbicacion))
                {
                    $intIdParroquia  = $objUbicacion->getParroquiaId()->getId();
                    $strLongitud     = $objUbicacion->getLongitudUbicacion();
                    $strLatitud      = $objUbicacion->getLatitudUbicacion();
                    $strDireccion    = $objUbicacion->getDireccionUbicacion();
                    $strAltura       = $objUbicacion->getAlturaSnm();
                }
            }

            if ($strLongitud    != $arrayParametros['strLongitud']    || $strLatitud != $arrayParametros['strLatitud'] || 
                $intIdParroquia != $arrayParametros['intIdParroquia'] || strtoupper($strDireccion)!= strtoupper($arrayParametros['strDireccion'])|| 
                $strAltura      != $arrayParametros['strAltura']) 
            {
                $objParroquia = $emInfraestructura->find('schemaBundle:AdmiParroquia', $arrayParametros['intIdParroquia']);
                
                if(is_object($objParroquia))
                {
                    $arrayRespuestaCoordenadas  = $serviceInfoElemento->validarLimitesCoordenadasElemento(array(
                                                                                                            "latitudElemento"   => 
                                                                                                            $arrayParametros['strLatitud'],
                                                                                                            "longitudElemento"  => 
                                                                                                            $arrayParametros['strLongitud'],
                                                                                                            "msjTipoElemento"   => 
                                                                                                            "del poste "));
                    if($arrayRespuestaCoordenadas["status"] === "ERROR")
                    {
                        throw new \Exception($arrayRespuestaCoordenadas['mensaje']);
                    }
                    //info ubicacion
                    $objUbicacionElemento = $emInfraestructura->find('schemaBundle:InfoUbicacion', $arrayParametros['intIdUbicacion']);
                    $objUbicacionElemento->setLatitudUbicacion($arrayParametros['strLatitud']);
                    $objUbicacionElemento->setLongitudUbicacion($arrayParametros['strLongitud']);
                    $objUbicacionElemento->setDireccionUbicacion($arrayParametros['strDireccion']);
                    $objUbicacionElemento->setAlturaSnm($arrayParametros['strAltura']);
                    $objUbicacionElemento->setParroquiaId($objParroquia);
                    $objUbicacionElemento->setUsrCreacion($objSession->get('user'));
                    $objUbicacionElemento->setFeCreacion(new \DateTime('now'));
                    $objUbicacionElemento->setIpCreacion($objRequest->getClientIp());
                    $emInfraestructura->persist($objUbicacionElemento);
                }
                else
                {
                    $arrayResult['strStatus']        = 'ERROR';
                    $arrayResult['strMessageStatus'] = 'No se encontro parroquia para la ubicacion.';
                    $objResponse->setData($arrayResult);
                    return $objResponse;
                }
                
                $arrayRelacionElemento = $emInfraestructura->getRepository('schemaBundle:InfoRelacionElemento')
                                                           ->findByElementoIdA($arrayParametros['intIdElemento']);

                    foreach($arrayRelacionElemento as $objElemento)
                    {
                        $objElementoUbicaHijo = $emInfraestructura->getRepository('schemaBundle:InfoEmpresaElementoUbica')
                                                                  ->findOneByElementoId($objElemento->getElementoIdB());

                        if(is_object($objElementoUbicaHijo))
                        {
                            //info ubicacion
                            $objUbicacionElementoHijo = $emInfraestructura->find('schemaBundle:InfoUbicacion', 
                                                                                 $objElementoUbicaHijo->getUbicacionId()->getId());
                            
                            if(is_object($objUbicacionElementoHijo))
                            {
                                if(is_object($objParroquia))
                                {
                                    $arrayRespuestaCoordenadas  = $serviceInfoElemento->validarLimitesCoordenadasElemento(array(
                                                                                                                    "latitudElemento"   => 
                                                                                                                    $arrayParametros['strLatitud'],
                                                                                                                    "longitudElemento"  => 
                                                                                                                    $arrayParametros['strLongitud'],
                                                                                                                    "msjTipoElemento"   => 
                                                                                                                    "del poste "));
                                    if($arrayRespuestaCoordenadas["status"] === "ERROR")
                                    {
                                        throw new \Exception($arrayRespuestaCoordenadas['mensaje']);
                                    }
                                    $objUbicacionElementoHijo->setLatitudUbicacion($arrayParametros['strLatitud']);
                                    $objUbicacionElementoHijo->setLongitudUbicacion($arrayParametros['strLongitud']);
                                    $objUbicacionElementoHijo->setDireccionUbicacion($arrayParametros['strDireccion']);
                                    $objUbicacionElementoHijo->setAlturaSnm($arrayParametros['strAltura']);
                                    $objUbicacionElementoHijo->setParroquiaId($objParroquia);
                                    $emInfraestructura->persist($objUbicacionElementoHijo);
                                    
                                }
                                else
                                {
                                    $arrayResult['strStatus']        = 'ERROR';
                                    $arrayResult['strMessageStatus'] = 'No se encontro parroquia para la ubicacion.';
                                    $objResponse->setData($arrayResult);
                                    return $objResponse;
                                }
                            }
                            else
                            {
                                $arrayResult['strStatus']        = 'ERROR';
                                $arrayResult['strMessageStatus'] = 'No se encontro ubicacion para el elemento relacionado.';
                                $objResponse->setData($arrayResult);
                                return $objResponse;
                            }
                        }
                        else
                        {
                            $arrayResult['strStatus']        = 'ERROR';
                            $arrayResult['strMessageStatus'] = 'Ubicacion para el elemento no existe';
                            $objResponse->setData($arrayResult);
                            return $objResponse;
                        }
                    }
            }    
            $objElemento       = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($arrayParametros['intIdElemento']);
            $objModeloElemento = $emInfraestructura->find('schemaBundle:AdmiModeloElemento', $arrayParametros['intIdTipoElemento']);                    

            if (!is_object($objElemento))
            {
                throw $this->createNotFoundException('No se encontro entidad InfoElemento.');
            }
            if (!is_object($objModeloElemento))
            {
                throw $this->createNotFoundException('No se encontro modelo para el tipo de elemento');
            }
            
            $objElemento->setNombreElemento($arrayParametros['strNombreElemento'] );
            $objElemento->setDescripcionElemento($arrayParametros['strDescripcionelemento']);
            $objElemento->setModeloElementoId($objModeloElemento);
            $emInfraestructura->persist($objElemento);
            

            //historial elemento
            $objHistorialElemento = new InfoHistorialElemento();
            $objHistorialElemento->setElementoId($objElemento);
            $objHistorialElemento->setEstadoElemento("Activo");
            $objHistorialElemento->setObservacion("Actualización de datos del poste.");
            $objHistorialElemento->setUsrCreacion($objSession->get('user'));
            $objHistorialElemento->setFeCreacion(new \DateTime('now'));
            $objHistorialElemento->setIpCreacion($objRequest->getClientIp());
            $emInfraestructura->persist($objHistorialElemento);
      
            //caracteristica para saber el propietario del poste
            $objDetalleElemento = $emInfraestructura->getRepository("schemaBundle:InfoDetalleElemento")
                                                    ->findOneBy(array('detalleNombre'=>'PROPIETARIO',
                                                                      'elementoId'=>$arrayParametros['intIdElemento']));

            if(is_object($objDetalleElemento))
            {
                $objDetalleElemento->setDetalleValor($arrayParametros['strPropietario']);     
                $emInfraestructura->persist($objDetalleElemento);
            }
            //caracteristica para saber el costo del poste
            $objDetalleElemento = $emInfraestructura->getRepository("schemaBundle:InfoDetalleElemento")
                                                    ->findOneBy(array('detalleNombre'=>'COSTO','elementoId'=>$arrayParametros['intIdElemento']));

            if(is_object($objDetalleElemento))
            {
                $objDetalleElemento->setDetalleValor($arrayParametros['strCosto']);       
                $emInfraestructura->persist($objDetalleElemento);
            }
            
            $arrayResult['strStatus']        = 'OK';
            $arrayResult['strMessageStatus'] = 'Se actualizó Poste exitosamente';
            
            $emInfraestructura->flush();
            $emInfraestructura->commit();            
        } 
        catch (\Exception $ex) 
        {   
            $arrayResult['strStatus']        = 'ERROR';
            $arrayResult['strMessageStatus'] = 'Error al actualizar Poste';
           
            if($emInfraestructura->getConnection()->isTransactionActive())
            {
                $emInfraestructura->rollback();
            }
            
            $serviceUtil->insertError('Telcos+',
                                      'InfoElementoPosteController->ajaxSavePosteAction',
                                      $ex->getMessage(),
                                      $objSession->get('user'),
                                      $objRequest->getClientIp());
            
            $emInfraestructura->close();
        }
        
       
        $objResponse->setData($arrayResult);
        return $objResponse;
    }
    
   /**
    * Documentación para el método 'ajaxSaveElementosRelacionadosPosteAction'.
    *
    * Metodo utilizado para agregar elementos relacionados a un poste
    * 
    * @param Object  $objResponse mensajes informativos.
    * 
    * @author Sofia Fernandez <sfernandez@telconet.ec>
    * @version 1.0 10-03-2017
    * 
    * @author Lizbeth Cruz <mlcruz@telconet.ec>
    * @version 1.3 18-09-2018 - Se agrega la validación de las coordenadas de latitud y longitud de acuerdo al país en sesión
    */
    public function ajaxSaveElementosRelacionadosPosteAction() {
      
        $emInfraestructura     = $this->get('doctrine')->getManager('telconet_infraestructura');
        $objRequest            = $this->get('request');
        $objSession            = $objRequest->getSession();
        $serviceUtil           = $this->get('schema.Util');
        $objResponse           = new JsonResponse();
        $objUbicacion          = null;
        $arrElementosContenido = array();
        $arrayParametros       = array();
        $arrayResult           = array();   
       
        $arrayParametros['intElementoIdA'] = $objRequest->get('idElementoA');
        $arrayParametros['strElementoIdB'] = $objRequest->get('strElemntosB');
        $emInfraestructura->getConnection()->beginTransaction();
             
         
        try
        {  
            $objElementoContenedor   = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($arrayParametros['intElementoIdA']); 
            $objEmpresaElementoUbica = $emInfraestructura->getRepository('schemaBundle:InfoEmpresaElementoUbica')
                                                         ->findOneByElementoId($arrayParametros['intElementoIdA']);
            
            if(is_object($objEmpresaElementoUbica))
            {
                $objUbicacion = $objEmpresaElementoUbica->getUbicacionId();
            }
            
            if(isset($arrayParametros['strElementoIdB']) && !empty($arrayParametros['strElementoIdB']) 
                && is_object($objUbicacion) && is_object($objElementoContenedor))
            {
                $arrElementosContenido= json_decode($arrayParametros['strElementoIdB']);
                
                foreach($arrElementosContenido as $intIdElemento) 
                {
                    //relacion elemento
                    $objRelacionElemento = new InfoRelacionElemento();
                    $objRelacionElemento->setElementoIdA($arrayParametros['intElementoIdA']);
                    $objRelacionElemento->setElementoIdB($intIdElemento);
                    $objRelacionElemento->setTipoRelacion("CONTIENE");
                    $objRelacionElemento->setObservacion("Poste contiene elemento: ". $objElementoContenedor->getNombreElemento());
                    $objRelacionElemento->setEstado("Activo");
                    $objRelacionElemento->setUsrCreacion($objSession->get('user'));
                    $objRelacionElemento->setFeCreacion(new \DateTime('now'));
                    $objRelacionElemento->setIpCreacion($objRequest->getClientIp());
                    
                    $emInfraestructura->persist($objRelacionElemento);
                    $emInfraestructura->flush();
                    
                    //historial elemento
                    $objHistorialElemento = new InfoHistorialElemento();
                    $objHistorialElemento->setElementoId($objElementoContenedor);
                    $objHistorialElemento->setEstadoElemento("Activo");
                    $objHistorialElemento->setObservacion("Se ha relacionado un elemento al poste");
                    $objHistorialElemento->setUsrCreacion($objSession->get('user'));
                    $objHistorialElemento->setFeCreacion(new \DateTime('now'));
                    $objHistorialElemento->setIpCreacion($objRequest->getClientIp());
                    $emInfraestructura->persist($objHistorialElemento);
                    $emInfraestructura->flush();
                                                                          
                    $objEmpresaElementoUbicaB = $emInfraestructura->getRepository('schemaBundle:InfoEmpresaElementoUbica')
                                                                  ->findOneBy(array('elementoId'=>$intIdElemento, 
                                                                                    'empresaCod'=>$objSession->get('idEmpresa')));
                    
                    if(is_object($objEmpresaElementoUbicaB))
                    {
                        $objUbicacionElementoB = $objEmpresaElementoUbicaB->getUbicacionId();
                    }
                    if(is_object($objUbicacionElementoB))
                    {
                        $serviceInfoElemento        = $this->get("tecnico.InfoElemento");
                        $arrayRespuestaCoordenadas  = $serviceInfoElemento->validarLimitesCoordenadasElemento(array(
                                                                                                        "latitudElemento"       => 
                                                                                                        $objUbicacionElementoB->getLatitudUbicacion(),
                                                                                                        "longitudElemento"      => 
                                                                                                        $objUbicacion->getLongitudUbicacion(),
                                                                                                        "msjTipoElemento"       => "del poste ",
                                                                                                        "msjTipoElementoPadre"  =>
                                                                                                        "que contiene al elmento ",
                                                                                                        "msjAdicional"          => 
                                                                                                        "por favor regularizar en la administración"
                                                                                                        ." de Postes"
                                                                                                     ));
                        if($arrayRespuestaCoordenadas["status"] === "ERROR")
                        {
                            throw new \Exception($arrayRespuestaCoordenadas['mensaje']);
                        }
                        
                        $objUbicacionElementoB->setLatitudUbicacion($objUbicacion->getLatitudUbicacion());
                        $objUbicacionElementoB->setLongitudUbicacion($objUbicacion->getLongitudUbicacion());
                        $objUbicacionElementoB->setDireccionUbicacion($objUbicacion->getDireccionUbicacion());
                        $objUbicacionElementoB->setAlturaSnm($objUbicacion->getAlturaSnm());
                        $objUbicacionElementoB->setParroquiaId($objUbicacion->getParroquiaId());
                        $objUbicacionElementoB->setUsrCreacion($objSession->get('user'));
                        $objUbicacionElementoB->setFeCreacion(new \DateTime('now'));
                        $objUbicacionElementoB->setIpCreacion($objRequest->getClientIp());
                        $emInfraestructura->persist($objUbicacionElementoB);
                        $emInfraestructura->flush();
                    }
                }
                $emInfraestructura->commit();  
                $arrayResult['strStatus']        = 'OK';
                $arrayResult['strMessageStatus'] = 'Se asociaron los elementos correctamente al Poste';
            }  
            else
            {
                $arrayResult['strStatus']        = 'ERROR';
                $arrayResult['strMessageStatus'] = 'No existen elementos relacionados para guardar.';
            }
            
        } 
        catch (\Exception $ex) 
        {   
            $arrayResult['strStatus']        = 'ERROR';
            $arrayResult['strMessageStatus'] = 'Error al asociar elementos al Poste';
           
             if($emInfraestructura->getConnection()->isTransactionActive())
            {
                $emInfraestructura->rollback();
            }
            
            $serviceUtil->insertError('Telcos+',
                                      'InfoElementoPosteController->ajaxSaveElementosRelacionadosPosteAction',
                                      $ex->getMessage(),
                                      $objSession->get('user'),
                                      $objRequest->getClientIp());
            
            $emInfraestructura->close();
        }
        
        $objResponse->setData($arrayResult);
        return $objResponse;
    }
    
   /**
    * Documentación para el método 'ajaxSaveEliminaRelacionElementoPosteAction'.
    *
    * Metodo utilizado para eliminar elementos relacionados a un poste
    *
    * @param Object  $objResponse mensajes informativos.
    * 
    * @author Sofia Fernandez <sfernandez@telconet.ec>
    * @version 1.0 11-03-2017
    */
    public function ajaxSaveEliminaRelacionElementoPosteAction() {
      
        $emInfraestructura = $this->get('doctrine')->getManager('telconet_infraestructura');
        $objRequest        = $this->get('request');
        $objSession        = $objRequest->getSession();
        $serviceUtil       = $this->get('schema.Util');
        $arrElementosB     = array();
        $arrayParametros   = array();
        $arrayResult      = array();   
        
        $arrayParametros['intElementoIdA'] = $objRequest->get('idElementoA');
        $arrayParametros['strElementoIdB'] = $objRequest->get('strElemntosB');
        
        $emInfraestructura->getConnection()->beginTransaction();
            
        try
        {  
            $objElementoA = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($arrayParametros['intElementoIdA']); 
           
            if (!is_object($objElementoA))
            {
                throw $this->createNotFoundException('No se encontró entidad InfoElemento');
            }
            $arrRelacionElemento = $emInfraestructura->getRepository('schemaBundle:InfoRelacionElemento')
                                                     ->findByElementoIdA($arrayParametros['intElementoIdA']);
           
            if(!is_array($arrRelacionElemento))
            {    
                throw $this->createNotFoundException('No se encontró entidad InfoRelacionElemento.');
            }
            else
            {
                if(isset($arrayParametros['strElementoIdB']) && !empty($arrayParametros['strElementoIdB']))
                {
                    $arrElementosB= json_decode($arrayParametros['strElementoIdB']);
                    foreach($arrElementosB as $intIdElemento) 
                    {
                        $objRelacionElementoB = $emInfraestructura->getRepository('schemaBundle:InfoRelacionElemento')
                                                                  ->findOneBy( array('elementoIdB'=>$intIdElemento, 'estado'=>'Activo'));

                        if(is_object($objRelacionElementoB))
                        {

                            $objRelacionElementoB->setObservacion("Se elimina relación con poste: ".$objElementoA->getNombreElemento());
                            $objRelacionElementoB->setEstado("Eliminado");

                            $emInfraestructura->persist($objRelacionElementoB);
                            $emInfraestructura->flush();

                            $objHistorialElemento = new InfoHistorialElemento();
                            $objHistorialElemento->setElementoId($objElementoA);
                            $objHistorialElemento->setEstadoElemento("Activo");
                            $objHistorialElemento->setObservacion("Se ha eliminado un elemento del poste");
                            $objHistorialElemento->setUsrCreacion($objSession->get('user'));
                            $objHistorialElemento->setFeCreacion(new \DateTime('now'));
                            $objHistorialElemento->setIpCreacion($objRequest->getClientIp());
                            $emInfraestructura->persist($objHistorialElemento);
                            $emInfraestructura->flush();
                        }
                    }
                    $emInfraestructura->commit();  
                    $arrayResult['strStatus']        = 'OK';
                    $arrayResult['strMessageStatus'] = 'Se desasociaron los elementos al Poste';
                }  
                else
                {
                    $arrayResult['strStatus']        = 'ERROR';
                    $arrayResult['strMessageStatus'] = 'No se encontró elemento para desasociar en Poste';

                }
            }
        } 
        catch (\Exception $ex) 
        {   
            $arrayResult['strStatus']        = 'ERROR';
            $arrayResult['strMessageStatus'] = 'Error al desasociar elementos del Poste';
           
             if($emInfraestructura->getConnection()->isTransactionActive())
            {
                $emInfraestructura->rollback();
            }
            
            $serviceUtil->insertError('Telcos+',
                                      'InfoElementoPosteController->ajaxSaveEliminaRelacionElementoPosteAction',
                                      $ex->getMessage(),
                                      $objSession->get('user'),
                                      $objRequest->getClientIp());
            
            $emInfraestructura->close();
        }
        
        $objResponse        = new JsonResponse();
        $objResponse->setData($arrayResult);
        return $objResponse;
    }
    
}

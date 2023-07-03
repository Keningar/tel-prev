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
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * Documentación para la clase 'InfoElementoRedPasivo'.
 *
 * Clase utilizada para manejar metodos que permiten realizar acciones de administracion de elementos s
 *
 * @author John Vera <javera@telconet.ec>          
 * @version 1.0 09-02-2017
 */

class InfoElementoRedPasivoController extends Controller
{   
   /**
    * Documentación para el método 'indexAction'.
    *
    * Metodo utilizado para retornar a la pagina principal de la administracion de s
    *
    * @author John Vera <javera@telconet.ec>          
    * @version 1.0 09-02-2017
    */
    public function indexAction()
    {
        $arrayRoles = array();

        $objRequest         = $this->getRequest();
        $objSession         = $objRequest->getSession();
        $strNombreElemento  = $objSession->get('strNombreElemento');
        if(isset($strNombreElemento) && !empty($strNombreElemento))
        {
            $objSession->remove('strNombreElemento');
        }

        if (true === $this->get('security.context')->isGranted('ROLE_401-5497'))
        {
            $arrayRoles[] = 'ROLE_401-5497';//editar
        }
        if (true === $this->get('security.context')->isGranted('ROLE_401-5499'))
        {
            $arrayRoles[] = 'ROLE_401-5499';//eliminar
        }
        if (true === $this->get('security.context')->isGranted('ROLE_401-5498'))
        {
            $arrayRoles[] = 'ROLE_401-5498';//nuevo
        }        

        return $this->render('tecnicoBundle:InfoElementoRedPasivo:index.html.twig',
                                array('rolesPermitidos'   => $arrayRoles,
                                      'strNombreElemento' => $strNombreElemento));

    }
    
   /**
     * Documentación para el método 'ajaxGetEncontradosAction'.
     *
     * Metodo utilizado para obtener información de los elementos
     * 
     * @param Object  $objRespuesta con informacion del elemento de acuerdo a la petición.
     * 
     * @author John Vera <javera@telconet.ec>          
     * @version 1.0 11-09-2017
     * 
     */
    public function ajaxGetEncontradosAction()
    {
        $objRespuesta       = new JsonResponse();
        $objSession         = $this->get('session');

        $objPeticion        = $this->get('request');
        
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');
        
        $arrayParametros      =  array();
        

        if($objPeticion->query->get('intElemento'))
        {
            $objElemento = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                             ->find($objPeticion->query->get('intElemento'));
            if(is_object($objElemento))
            {
                $objTipoElemento = $objElemento->getModeloElementoId()->getTipoElementoId();
            }
        }
        else
        {
            $objTipoElemento = $emInfraestructura->getRepository('schemaBundle:AdmiTipoElemento')
                                                 ->find($objPeticion->query->get('sltTipoElemento'));
        }
        
        $arrayParametros['TIPO']          = is_object($objTipoElemento) ? $objTipoElemento->getId() : '';
        $arrayParametros['NOMBRE_TIPO']   = is_object($objTipoElemento) ? $objTipoElemento->getNombreTipoElemento() : '';
        $arrayParametros['ID_ELEMENTO'] =  $objPeticion->query->get('intElemento');
        $arrayParametros['ELEMENTO']    =  $objPeticion->query->get('strCodigo');
        $arrayParametros['PROPIETARIO'] =  $objPeticion->query->get('strPropietario');
        $arrayParametros['CANTON']      =  $objPeticion->query->get('strCanton');
        $arrayParametros['ESTADO']      =  $objPeticion->query->get('strEstado');
        $arrayParametros['EMPRESA']     =  $objSession->get('prefijoEmpresa');
        $arrayParametros['IDEMPRESA']   =  $objSession->get('idEmpresa');
        $arrayParametros['UBICACION']   =  'OK';
        $arrayParametros['START']       =  $objPeticion->query->get('start');
        $arrayParametros['LIMIT']       =  $objPeticion->query->get('limit');

        $strJson = $this->getDoctrine()
                        ->getManager("telconet_infraestructura")
                        ->getRepository('schemaBundle:InfoElemento')
                        ->generarJsonElementosEmpresa($arrayParametros);

        $objRespuesta->setContent($strJson);
        
        return $objRespuesta;
    }
    
   /**
    * Documentación para el método 'newAction'.
    *
    * Metodo utilizado para direccionar al js de creacion de poste.
    *
    * @author John Vera <javera@telconet.ec>          
    * @version 1.0 11-09-2017
    */
    public function newAction()
    {
        return $this->render('tecnicoBundle:InfoElementoRedPasivo:new.html.twig');
    }
    
    
    /**
    * Documentación para el método 'getTipoElementoPasivoAction'.
    *
    * Metodo utilizado para obtener los tipos de elemento
    * 
    * @param Request $objRequest
    * @param Object  $objRespuesta con informacion de los tipos de elementos.
    *
    * @author John Vera <javera@telconet.ec>          
    * @version 1.0 12-09-2017
    */
    public function getTipoElementoPasivoAction(Request $objRequest)
    {
        $objRespuesta                = new JsonResponse();
 
        $arrayTiposElemento = $this->getDoctrine()->getManager("telconet_infraestructura")->getRepository('schemaBundle:AdmiTipoElemento')
                                                  ->findBy(array('descripcionTipoElemento' => 'ELEMENTO PASIVO RED',
                                                                 'claseTipoElemento'       => 'PASIVO',
                                                                 'estado'                  => 'Activo'));
        $arrayParametros = array();
        
        $arrayParametros['strNombreParametroCab'] = 'ELEMENTOS_PASIVO_RUTA';
        $arrayParametros['strEstado']             = 'Activo';
        $arrayParametros['strDescripcionDet']     = 'COMBO_ELEMENTOS';
        
        $arrayTiposElemento = $this->getDoctrine()->getManager("telconet_infraestructura")->getRepository('schemaBundle:AdmiParametroDet')
                                                  ->findParametrosDet($arrayParametros);     
        
        if (is_array($arrayTiposElemento))
        {
        
            foreach ($arrayTiposElemento['arrayResultado'] as $arrayresultado)
            {
                $objTipoElemento = $this->getDoctrine()->getManager("telconet_infraestructura")->getRepository('schemaBundle:AdmiTipoElemento')
                                                       ->findOneBy(array('nombreTipoElemento' => $arrayresultado['strValor1'],
                                                                         'estado'             => 'Activo'));             
                if(is_object($objTipoElemento))
                {
                    $arrayItem                      = array();
                    $arrayItem['idTipoElemento']    = $objTipoElemento->getId();
                    $arrayItem['nombreTipoElemento']= $objTipoElemento->getNombreTipoElemento();


                    $arrayEncontrados[]           = $arrayItem;
                }
            }
        }
    
        $arrayResultado['total']           = count($arrayEncontrados);
        $arrayResultado['encontrados']     = $arrayEncontrados;
        $objRespuesta->setData($arrayResultado);        
        
        return $objRespuesta;
    }
 
    
   /**
    * @Secure(roles="ROLE_401-5498")
    * Documentación para el método 'ajaxSaveAction'.
    *
    * Metodo utilizado para crear un poste
    * 
    * @param Object  $objRespuesta mensajes informativos.
    * 
    * @author John Vera <javera@telconet.ec>          
    * @version 1.0 12-09-2017
    * 
    * @author Lizbeth Cruz <mlcruz@telconet.ec>
    * @version 1.2 19-09-2018 - Se agrega la validación de las coordenadas de latitud y longitud de acuerdo al país en sesión
    * 
    */
    public function ajaxSaveAction() 
    {
      
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
        $arrayParametros['intElementoContenedor']  = $objRequest->get('objCmbElementoContenedor');
        $arrayParametros['strTipoLugar']           = $objRequest->get('objCmbTipoLugar');
        $arrayParametros['strNivel']               = $objRequest->get('objCmbNivel');        
        $arrayParametros['strLatitud']             = $objRequest->get('objTxtLatitudUbicacion');
        $arrayParametros['strLongitud']            = $objRequest->get('objTxtLongitudUbicacion');
        $arrayParametros['strUbicadoEn']           = $objRequest->get('objUbicadoEn');
        $arrayParametros['strTipoElemento2']        = $objRequest->get('objCmbTipoElementoRuta');
        
        
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
                $intIdTipoElemento = $objModeloElemento->getTipoElementoId()->getId();
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
                        $objHistorialElemento->setObservacion("Se ingresó el  : ". $arrayParametros['strNombreElemento']);
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
                                                                                                                    "del elemento de Red pasivo "));
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

                            if($arrayParametros['strPropietario'])
                            {
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
                            }
                            
                            if($arrayParametros['strCosto'])
                            {
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
                            }
                            
                            if($arrayParametros['strTipoLugar'])
                            {
                                //caracteristica para saber el tipo de lugar donde esta ubicado el elemento
                                $objDetalle = new InfoDetalleElemento();
                                $objDetalle->setElementoId($objElemento->getId());
                                $objDetalle->setDetalleNombre("TIPO LUGAR");
                                $objDetalle->setDetalleValor($arrayParametros['strTipoLugar']);
                                $objDetalle->setDetalleDescripcion("Caracteristicas para indicar el lugar del Elemento");
                                $objDetalle->setFeCreacion(new \DateTime('now'));
                                $objDetalle->setUsrCreacion($objSession->get('user'));
                                $objDetalle->setIpCreacion($objRequest->getClientIp());
                                $objDetalle->setEstado('Activo');
                                $emInfraestructura->persist($objDetalle);
                                $emInfraestructura->flush();        
                            }
                            
                            if($arrayParametros['intElementoContenedor'])
                            {
                                $objRelacionElemento = new InfoRelacionElemento();
                                $objRelacionElemento->setElementoIdA($arrayParametros['intElementoContenedor']);
                                $objRelacionElemento->setElementoIdB($objElemento->getId());
                                $objRelacionElemento->setTipoRelacion("CONTIENE");
                                $objRelacionElemento->setEstado("Activo");
                                $objRelacionElemento->setUsrCreacion($objSession->get('user'));
                                $objRelacionElemento->setFeCreacion(new \DateTime('now'));
                                $objRelacionElemento->setIpCreacion($objRequest->getClientIp());

                                $emInfraestructura->persist($objRelacionElemento);
                                $emInfraestructura->flush();
                            }
                            
                            if($arrayParametros['strNivel'])
                            {
                                //caracteristica para saber el tipo de lugar donde esta ubicado el elemento
                                $objDetalle = new InfoDetalleElemento();
                                $objDetalle->setElementoId($objElemento->getId());
                                $objDetalle->setDetalleNombre("NIVEL");
                                $objDetalle->setDetalleValor($arrayParametros['strNivel']);
                                $objDetalle->setDetalleDescripcion("Caracteristica para indicar el nivel");
                                $objDetalle->setFeCreacion(new \DateTime('now'));
                                $objDetalle->setUsrCreacion($objSession->get('user'));
                                $objDetalle->setIpCreacion($objRequest->getClientIp());
                                $objDetalle->setEstado('Activo');
                                $emInfraestructura->persist($objDetalle);
                                $emInfraestructura->flush();        
                            }   
                            
                            if($arrayParametros['strUbicadoEn'])
                            {
                                //caracteristica para saber donde esta ubicada la caja (pedestal - edificio)
                                $objDetalle1 = new InfoDetalleElemento();
                                $objDetalle1->setElementoId($objElemento->getId());
                                $objDetalle1->setDetalleNombre("UBICADO EN");
                                $objDetalle1->setDetalleValor($arrayParametros['strUbicadoEn']);
                                $objDetalle1->setDetalleDescripcion("Caracteristicas para indicar donde se ubica el Elemento");
                                $objDetalle1->setFeCreacion(new \DateTime('now'));
                                $objDetalle1->setUsrCreacion($objSession->get('user'));
                                $objDetalle1->setIpCreacion($objRequest->getClientIp());
                                $objDetalle1->setEstado('Activo');
                                $emInfraestructura->persist($objDetalle1);
                                $emInfraestructura->flush();
                            }

                            $arrayResult['strStatus']        = 'OK';
                            $arrayResult['strMessageStatus'] = 'Se ingresa correctamente el elemento.';
                        }
                        else
                        {
                            $arrayResult['strStatus']        = 'ERROR';
                            $arrayResult['strMessageStatus'] = 'Parroquia no existe';
                            
                        }
                        $objSession->set('strNombreElemento', $objElemento->getId());
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
                    $arrayResult['strMessageStatus'] = 'Error en la creacion de nuevo ';

                     if($emInfraestructura->getConnection()->isTransactionActive())
                    {
                        $emInfraestructura->rollback();
                    }

                    $serviceUtil->insertError('Telcos+',
                                              'InfoElementoRedPasivoController->ajaxSaveAction',
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
    * @param Request $objRequest
    * @param Object  $objRespuesta Elementos por tipo de material y por canton.
    *
    * @author John Vera <javera@telconet.ec>          
    * @version 1.0 07-09-2017
    */
    public function ajaxCargarElementosAction(Request $objRequest)
    {    
      
        $objRespuesta = new JsonResponse();
        $objSession   = $this->get('session');
        
        $arrayParametros                      = array();       
        $arrayParametros['intTipoElementoId'] = $objRequest->get('tipoElementoId');
        $arrayParametros['strEstado']         = $objRequest->get('estado');
        $arrayParametros['intCantonId']       = $objRequest->get('canton');
        $arrayParametros['intEmpresaId']      = $objSession->get('idEmpresa');
      
        $arrayRespuesta = $this->getDoctrine()
                             ->getManager("telconet_infraestructura")
                             ->getRepository('schemaBundle:InfoElemento')
                             ->getArrayElementosRelacionPorTipoyCanton($arrayParametros);
        $objRespuesta->setData($arrayRespuesta);
  
        return $objRespuesta;
    }
    
   /**
    * @Secure(roles="ROLE_401-5499")
    * Documentación para el método 'ajaxDeleteAction'.
    *
    * Metodo utilizado para eliminar un poste
    * @param Object  $objResponse mensajes informativos.
    * 
    * @author John Vera <javera@telconet.ec>          
    * @version 1.0 07-09-2017
    */
    public function ajaxDeleteAction() 
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
                throw $this->createNotFoundException('No se encontró la entidad InfoElementoRedPasivo.');
            }
            
            $arrayInfoRelacionElemento = $emInfraestructura->getRepository('schemaBundle:InfoRelacionElemento')
                                                           ->findBy(array("elementoIdA" => $objElemento->getId()));
            
        
            foreach($arrayInfoRelacionElemento as $objRelacionElemento)
            {
                $strEstadoRelacion = $objRelacionElemento->getEstado();
                
                if ($strEstadoRelacion == 'Activo')
                {
                    $arrayResult['strStatus']        = 'ERROR';
                    $arrayResult['strMessageStatus'] = 'Posee elementos relacionados, NO se puede eliminar';
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
            $objHistorialElemento->setObservacion("Se ha eliminado el : ".$objElemento->getNombreElemento());
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
            $arrayResult['strMessageStatus'] = 'Error en la eliminacion de ';
           
             if($emInfraestructura->getConnection()->isTransactionActive())
            {
                $emInfraestructura->rollback();
            }
            
            $serviceUtil->insertError('Telcos+',
                                      'InfoElementoRedPasivoController->ajaxDeleteAction',
                                      $ex->getMessage(),
                                      $objSession->get('user'),
                                      $objRequest->getClientIp());
            
            $emInfraestructura->close();
        }
        
      
        $objResponse->setData($arrayResult);
        return $objResponse;
        
    }
    
   /**
    * @Secure(roles="ROLE_401-5497")
    * Documentación para el método 'ajaxEditAction'.
    *
    * Metodo utilizado para editar la informacion un poste
    * 
    * @param Object  $objResponse mensajes informativos.
    * 
    * @author John Vera <javera@telconet.ec>          
    * @version 1.0 07-09-2017
    * 
    * @author Lizbeth Cruz <mlcruz@telconet.ec>
    * @version 1.1 19-09-2018 - Se agrega la validación de las coordenadas de latitud y longitud de acuerdo al país en sesión y se corrige
    *                           método para obtener la ubicación
    * 
    * @author Antonio Ayala <afayala@telconet.ec>
    * @version 1.2 03-01-2022 - Se agrega la validación de que si el elemento es Manga consulte si tiene factibilidad
    *                           para agregar el Nivel
    * 
    */
    public function ajaxEditAction() 
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
        $arrayParametros['strTipoLugar']           = $objRequest->get('objCmbTipoLugar');
        $arrayParametros['intElementoContenedor']  = $objRequest->get('objCmbElementoContenedor');
        $arrayParametros['strNivel']               = $objRequest->get('objCmbNivel'); 
        $arrayParametros['strUbicadoEn']           = $objRequest->get('objUbicadoEn');
        
        $emInfraestructura->getConnection()->beginTransaction();
        $arrayResult       = array();
        $strObservacion    = 'Actualización de datos del elemento:';
         
        try
        {
            $serviceInfoElemento    = $this->get("tecnico.InfoElemento");
            $objElementoUbica       = $emInfraestructura->getRepository('schemaBundle:InfoEmpresaElementoUbica')
                                                        ->findOneByElementoId($arrayParametros['intIdElemento']);
            $intIdParroquia    = 0;
            $strLongitud       = '';
            $strLatitud        = '';
            $strDireccion      = '';
            $strAltura         = '';

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
                //Ingreso que opciones se modificaron
                if($strLongitud != $arrayParametros['strLongitud'])
                {
                    $strObservacion = $strObservacion.' Se modifico Longitud.';
                } 
                
                if($strLatitud != $arrayParametros['strLatitud'])
                {
                    $strObservacion = $strObservacion.' Se modifico Latitud.';
                } 
                
                if($intIdParroquia != $arrayParametros['intIdParroquia'])
                {
                    $strObservacion = $strObservacion.' Se modifico Parroquia.';
                }
                
                if($strAltura != $arrayParametros['strAltura'])
                {
                    $strObservacion = $strObservacion.' Se modifico altura.';
                }
                
                if(strtoupper($strDireccion) != strtoupper($arrayParametros['strDireccion']))
                {
                    $strObservacion = $strObservacion.' Se modifico direccion.';
                }
                
                $objParroquia = $emInfraestructura->find('schemaBundle:AdmiParroquia', $arrayParametros['intIdParroquia']);
                
                if(is_object($objParroquia))
                {
                    $arrayRespuestaCoordenadas  = $serviceInfoElemento->validarLimitesCoordenadasElemento(array("latitudElemento"   => 
                                                                                                                $arrayParametros['strLatitud'],
                                                                                                                "longitudElemento"  => 
                                                                                                                $arrayParametros['strLongitud'],
                                                                                                                "msjTipoElemento"   => 
                                                                                                                "del elemento pasivo de red "));
                    if($arrayRespuestaCoordenadas["status"] === "ERROR")
                    {
                        throw new \Exception($arrayRespuestaCoordenadas['mensaje']);
                    }
                    
                    if(is_object($objUbicacion))
                    {
                        $objUbicacionElemento = $objUbicacion;
                    }
                    else
                    {
                        $objUbicacionElemento = new InfoUbicacion();
                    }
                    //info ubicacion
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
            
            
            //actualizo si es contenido por otro elemento
            $objRelacionElemento = $emInfraestructura->getRepository('schemaBundle:InfoRelacionElemento')
                                                     ->findOneBy(array('elementoIdB'  => $arrayParametros['intIdElemento'],
                                                                       'estado'       => 'Activo'));

            if(is_object($objRelacionElemento) && $objRelacionElemento->getElementoIdA() != $arrayParametros['intElementoContenedor'] )
            {
                    $objRelacionElemento->setElementoIdA($arrayParametros['intElementoContenedor']);
                    $strObservacion = $strObservacion.' Se modifico elemento contenedor.';
                    $emInfraestructura->persist($objRelacionElemento);
            }            
            
            $objElemento       = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($arrayParametros['intIdElemento']);
            if (!is_object($objElemento))
            {
                throw $this->createNotFoundException('No se encontro entidad InfoElementoRedPasivo.');
            }            
            
            if($arrayParametros['intIdTipoElemento'] != '')
            {
                $objModeloElemento = $emInfraestructura->find('schemaBundle:AdmiModeloElemento', $arrayParametros['intIdTipoElemento']);
                if (!is_object($objModeloElemento))
                {
                    throw $this->createNotFoundException('No se encontro modelo para el tipo de elemento');
                }
                else
                {
                    if ($objElemento->getModeloElementoId() != $objModeloElemento->getId())
                    {
                        $objElemento->setModeloElementoId($objModeloElemento);
                        $strObservacion = $strObservacion.' Se modifico modelo del elemento.';
                    }
                }
            }
            
            if(strtoupper($objElemento->getNombreElemento()) != strtoupper($arrayParametros['strNombreElemento']))
            {
                $strObservacion = $strObservacion.' Se modifico nombre del elemento.';
            }
            
            if(strtoupper($objElemento->getDescripcionElemento()) != strtoupper($arrayParametros['strDescripcionelemento']))
            {
                $strObservacion = $strObservacion.' Se modifico descripcion del elemento.';
            }
            
            if(strtoupper($objElemento->getNombreElemento()) != strtoupper($arrayParametros['strNombreElemento']))
            {
                $strObservacion = $strObservacion.' Se modifico nombre del elemento.';
            }
            
            if(strtoupper($objElemento->getDescripcionElemento()) != strtoupper($arrayParametros['strDescripcionelemento']))
            {
                $strObservacion = $strObservacion.' Se modifico descripcion del elemento.';
            }
            
            $objElemento->setNombreElemento($arrayParametros['strNombreElemento'] );
            $objElemento->setDescripcionElemento($arrayParametros['strDescripcionelemento']);            
            $emInfraestructura->persist($objElemento);
            

            //caracteristicas
            if($arrayParametros['strPropietario'] != '')
            {
                //caracteristica para saber el propietario del poste
                $objDetalleElemento = $emInfraestructura->getRepository("schemaBundle:InfoDetalleElemento")
                                                        ->findOneBy(array('detalleNombre'=> 'PROPIETARIO',
                                                                         'estado'        => 'Activo',
                                                                         'elementoId'    => $arrayParametros['intIdElemento']));


                if(is_object($objDetalleElemento))
                {
                    if($objDetalleElemento->getDetalleValor() != $arrayParametros['strPropietario'])
                    {
                        $strObservacion = $strObservacion.' Se modifico propietario.';
                    }
                    $objDetalleElemento->setDetalleValor($arrayParametros['strPropietario']);     
                    $emInfraestructura->persist($objDetalleElemento);
                }
            }
            
            if($arrayParametros['strCosto'] != '')
            {            
                //caracteristica para saber el costo del poste
                $objDetalleElemento = $emInfraestructura->getRepository("schemaBundle:InfoDetalleElemento")
                                                        ->findOneBy(array(  'detalleNombre' => 'COSTO',
                                                                            'estado'        => 'Activo',
                                                                            'elementoId'    => $arrayParametros['intIdElemento']));

                if(is_object($objDetalleElemento))
                {
                    if($objDetalleElemento->getDetalleValor() != $arrayParametros['strCosto'])
                    {
                        $strObservacion = $strObservacion.' Se modifico costo.';
                    }
                    $objDetalleElemento->setDetalleValor($arrayParametros['strCosto']);       
                    $emInfraestructura->persist($objDetalleElemento);
                }
            }
            
            if($arrayParametros['strTipoLugar'] != '')
            {
                //caracteristica para saber el costo del poste
                $objDetalleElemento = $emInfraestructura->getRepository("schemaBundle:InfoDetalleElemento")
                                                        ->findOneBy(array(  'detalleNombre' => 'TIPO LUGAR',
                                                                            'estado'        => 'Activo',
                                                                            'elementoId'    => $arrayParametros['intIdElemento']));

                if(is_object($objDetalleElemento))
                {
                    if($objDetalleElemento->getDetalleValor() != $arrayParametros['strTipoLugar'])
                    {
                        $strObservacion = $strObservacion.' Se modifico Tipo de lugar.';
                    }
                    $objDetalleElemento->setDetalleValor($arrayParametros['strTipoLugar']);       
                    $emInfraestructura->persist($objDetalleElemento);

                }
                else
                {
                    $objDetalle = new InfoDetalleElemento();
                    $objDetalle->setElementoId($arrayParametros['intIdElemento']);
                    $objDetalle->setDetalleNombre("TIPO LUGAR");
                    $objDetalle->setDetalleValor($arrayParametros['strTipoLugar']);
                    $objDetalle->setDetalleDescripcion("Caracteristicas para indicar el lugar del Elemento");
                    $objDetalle->setFeCreacion(new \DateTime('now'));
                    $objDetalle->setUsrCreacion($objSession->get('user'));
                    $objDetalle->setIpCreacion($objRequest->getClientIp());
                    $objDetalle->setEstado('Activo');
                    $emInfraestructura->persist($objDetalle);
                }
            }
            
            if($arrayParametros['strUbicadoEn'] != '')
            {
                //caracteristica para saber el costo del poste
                $objDetalleElemento = $emInfraestructura->getRepository("schemaBundle:InfoDetalleElemento")
                                                        ->findOneBy(array(  'detalleNombre' => 'UBICADO EN',
                                                                            'estado'        => 'Activo',
                                                                            'elementoId'    => $arrayParametros['intIdElemento']));

                if(is_object($objDetalleElemento))
                {
                        if($objDetalleElemento->getDetalleValor() != $arrayParametros['strUbicadoEn'])
                        {
                            $strObservacion = $strObservacion.' Se modifico Ubicado en.';
                        }
                        $objDetalleElemento->setDetalleValor($arrayParametros['strUbicadoEn']);       
                        $emInfraestructura->persist($objDetalleElemento);

                }   
            }            
            
            //caracteristica para saber el costo del poste
            $objDetalleElemento = $emInfraestructura->getRepository("schemaBundle:InfoDetalleElemento")
                                                    ->findOneBy(array('detalleNombre'   => 'NIVEL',
                                                                      'estado'          => 'Activo',
                                                                      'elementoId'      => $arrayParametros['intIdElemento']));

            if(is_object($objDetalleElemento))
            {
                if($objDetalleElemento->getDetalleValor() != $arrayParametros['strNivel'] && 
                        $arrayParametros['strNivel'] != '')
                {
                    $strObservacion = $strObservacion.' Se modifico nivel.';
                }
                $objDetalleElemento->setDetalleValor($arrayParametros['strNivel']);       
                $emInfraestructura->persist($objDetalleElemento);
            }
            else
            {
                if($arrayParametros['strNivel'])
                {
                    //caracteristica para saber el tipo de lugar donde esta ubicado el elemento
                    $objDetalle = new InfoDetalleElemento();
                    $objDetalle->setElementoId($arrayParametros['intIdElemento']);
                    $objDetalle->setDetalleNombre("NIVEL");
                    $objDetalle->setDetalleValor($arrayParametros['strNivel']);
                    $objDetalle->setDetalleDescripcion("Caracteristica para indicar el nivel");
                    $objDetalle->setFeCreacion(new \DateTime('now'));
                    $objDetalle->setUsrCreacion($objSession->get('user'));
                    $objDetalle->setIpCreacion($objRequest->getClientIp());
                    $objDetalle->setEstado('Activo');
                    $emInfraestructura->persist($objDetalle);
                }
            }
            
            //historial elemento
            $objHistorialElemento = new InfoHistorialElemento();
            $objHistorialElemento->setElementoId($objElemento);
            $objHistorialElemento->setEstadoElemento("Activo");
            $objHistorialElemento->setObservacion($strObservacion);
            $objHistorialElemento->setUsrCreacion($objSession->get('user'));
            $objHistorialElemento->setFeCreacion(new \DateTime('now'));
            $objHistorialElemento->setIpCreacion($objRequest->getClientIp());
            $emInfraestructura->persist($objHistorialElemento);
            
            $arrayResult['strStatus']        = 'OK';
            $arrayResult['strMessageStatus'] = 'Se actualizó  exitosamente';
            
            $emInfraestructura->flush();
            $emInfraestructura->commit();            
        } 
        catch (\Exception $ex) 
        {   
            $arrayResult['strStatus']        = 'ERROR';
            $arrayResult['strMessageStatus'] = 'Error al actualizar ';
           
            if($emInfraestructura->getConnection()->isTransactionActive())
            {
                $emInfraestructura->rollback();
            }
            
            $serviceUtil->insertError('Telcos+',
                                      'InfoElementoRedPasivoController->ajaxSaveAction',
                                      $ex->getMessage(),
                                      $objSession->get('user'),
                                      $objRequest->getClientIp());
            
            $emInfraestructura->close();
        }
        
       
        $objResponse->setData($arrayResult);
        return $objResponse;
    }
    
    
}

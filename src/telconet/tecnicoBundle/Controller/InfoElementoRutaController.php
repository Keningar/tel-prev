<?php

namespace telconet\tecnicoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use telconet\schemaBundle\Entity\InfoElemento;
use telconet\schemaBundle\Entity\InfoEmpresaElemento;
use telconet\schemaBundle\Entity\InfoHistorialElemento;
use telconet\schemaBundle\Entity\InfoDetalleElemento;
use telconet\schemaBundle\Entity\InfoTramo;
use telconet\schemaBundle\Entity\InfoDetalleTramo;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * Documentación para la clase 'InfoElementoRuta'.
 *
 * Clase utilizada para manejar metodos que permiten realizar acciones de administracion de elementos s
 *
 * @author John Vera <javera@telconet.ec>          
 * @version 1.0 09-02-2017
 */

class InfoElementoRutaController extends Controller
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

        if (true === $this->get('security.context')->isGranted('ROLE_400-5500'))
        {
            $arrayRoles[] = 'ROLE_400-5500'; //editar
        }
        if (true === $this->get('security.context')->isGranted('ROLE_400-5501'))
        {
            $arrayRoles[] = 'ROLE_400-5501'; //eliminar
        }
        if (true === $this->get('security.context')->isGranted('ROLE_400-55502'))
        {
            $arrayRoles[] = 'ROLE_400-5502';//nuevo
        }
        if (true === $this->get('security.context')->isGranted('ROLE_400-5503'))
        {
            $arrayRoles[] = 'ROLE_400-5503';//editar tramo
        }
        if (true === $this->get('security.context')->isGranted('ROLE_400-5504'))
        {
            $arrayRoles[] = 'ROLE_400-5504'; //agregar tramo
        } 
        if (true === $this->get('security.context')->isGranted('ROLE_400-8077'))
        {
            $arrayRoles[] = 'ROLE_400-8077'; //subir rutas masivas
        } 
        
        return $this->render('tecnicoBundle:InfoElementoRuta:index.html.twig',
                                array('rolesPermitidos'   => $arrayRoles,
                                      'strNombreElemento' => $strNombreElemento));

    }
    
   /**
     * Documentación para el método 'ajaxGetEncontradosAction'.
     *
     * Metodo utilizado para obtener información de los s
     * 
     * @param Object  $objRespuesta con informacion del elemento de acuerdo a la petición.
     * 
     * @author John Vera <javera@telconet.ec>          
     * @version 1.0 11-02-2017
     * 
     * @author Antonio Ayala <afayala@telconet.ec>          
     * @version 1.1 25-05-2021 Se modifca el tipo de elemento que estaba quemado por el que corresponde en la selección para 
     *                         para la búsqueda 
     * 
     */
    public function ajaxGetEncontradosAction()
    {
        $objRespuesta       = new JsonResponse();
        $objPeticion        = $this->get('request');        
        
        $arrayParametros                     = array();
        $arrayParametros['strEstado']        = $objPeticion->query->get('strEstado');
        $arrayParametros['strDetalleValor']  = $objPeticion->query->get('sltClase');
        $arrayParametros['strDetalleNombre'] = 'CLASE';
        $arrayParametros['strTipoElemento']  = $objPeticion->query->get('sltTipoElemento');
        $arrayParametros['intModeloElemento']= $objPeticion->query->get('sltModeloElemento');
        $arrayParametros['strNombreElemento']= $objPeticion->query->get('sltNombreElemento');
        $arrayParametros['intElemento']      = $objPeticion->query->get('intElemento');
        $arrayParametros['START']            = $objPeticion->query->get('start');
        $arrayParametros['LIMIT']            = $objPeticion->query->get('limit');  
        
        $intTipoRuta = $objPeticion->query->get('sltClase');
        
        //Consulto el tipo de elemento para obtener el nombre del mismo
        $objTipoElemento = $this->getDoctrine()->getManager("telconet_infraestructura")
                                               ->getRepository('schemaBundle:AdmiTipoElemento')
                                               ->findOneBy(array('id'       => $intTipoRuta,
                                                                 'estado'   => 'Activo'));             
        if(is_object($objTipoElemento))
        {
            $arrayParametros['strTipoElemento'] = $objTipoElemento->getNombreTipoElemento();
        }
        
        $arrayResultado = $this->getDoctrine()->getManager("telconet_infraestructura")
                               ->getRepository('schemaBundle:InfoElemento')
                               ->getArrayElementosByDetalleParam($arrayParametros);        

        $objRespuesta->setContent(json_encode($arrayResultado));
        
        return $objRespuesta;
    }
    
    
   /**
     * Metodo utilizado para obtener información de los tramos
     * 
     * @param Object  $objRespuesta con informacion del elemento de acuerdo a la petición.
     * 
     * @author John Vera <javera@telconet.ec>          
     * @version 1.0 11-02-2017
     * 
     */
    public function getTramosAction()
    {
        $objRespuesta       = new JsonResponse();
        $objPeticion        = $this->get('request'); 
        $arrayResultado     = array();
        
        $arrayParametros                     = array();
        $arrayParametros['intElemento']      = $objPeticion->query->get('intElemento');
        
        $arrayResultado = $this->getArrayTramosAction($arrayParametros);

        $objRespuesta->setContent(json_encode($arrayResultado));
        
        return $objRespuesta;
    }  
    
    /**
     * Metodo utilizado para obtener información de los tramos
     * 
     * @param Object  $objRespuesta con informacion del elemento de acuerdo a la petición.
     * 
     * @author John Vera <javera@telconet.ec> 
     * @version 1.0 11-02-2017
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 09-04-2018 Se agrega el parámetro de idEmpresa obtenida de sesión
     *
     * @author Steven Ruano <sruano@telconet.ec>
     * @version 1.2 08-11-2022 Eliminar Tramos que pertenecen a la ruta.
     * 
     */
    public function getArrayTramosAction($arrayParametros)
    {
        $objRequest            = $this->get('request');
        $objSession            = $objRequest->getSession();
        $strCodEmpresa         = $objSession->get('idEmpresa');
        $arrayResultado = array();
        
        $objTramoInicial= $this->getDoctrine()->getManager("telconet_infraestructura")
                               ->getRepository('schemaBundle:InfoTramo')
                               ->findOneBy(array('rutaId'    => $arrayParametros['intElemento'],
                                                 'estado'    => 'Activo',
                                                 'tipoTramo' => 'INICIAL'));  
        
        if(is_object($objTramoInicial))
        {
            $arrayParametros['intElementoInicial'] = $objTramoInicial->getElementoAId();

            $objElemento    = $this->getDoctrine()->getManager("telconet_infraestructura")
                                   ->getRepository('schemaBundle:InfoElemento')
                                   ->find($arrayParametros['intElemento']);
            if(is_object($objElemento))
            {
                $arrayParametros['strEstado']        = $objElemento->getEstado();
                $arrayParametros["strCodEmpresa"]    = $strCodEmpresa;

                $arrayResultado = $this->getDoctrine()->getManager("telconet_infraestructura")
                                       ->getRepository('schemaBundle:InfoElemento')
                                       ->getTramosPorRutas($arrayParametros);       
            }
        }

        return $arrayResultado;
    }    
    
    /**
     * Documentación para el método 'buscarElementoAction'.
     *
     * Metodo utilizado para obtener información de los elementos 
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.1 17-12-2015 Se agrega cambio para filtrado de registros mediante peticiones ajax por parametros 
     * @since 1.0
     * 
     * @author Ricardo Coello Quezada <rcoello@telconet.ec>
     * @version 1.1 15-11-2017  Se agrega parametro idCanton al array con el objetivo de llegar al canton del elemento y empresa en sesion
     *                          con el objetivo de retornar la data de acuerdo a la ubicacion del usuario.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 05-04-2018 Se obtiene la región de la persona en sesión y se lo envía como parámetro a la consulta de elementos
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.3 21-05-2021 Se agrega validación para que se carguen los elementos dependiendo del tipo de ruta y tipo de infraestructura
     *  
     */
    public function buscarElementoAction()
    {
        ini_set('max_execution_time', 400000);
        $objRespuesta           = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        $objPeticion            = $this->get('request');
        $objSession             = $this->get('session');
        $strNombreTipoElemento  = $objPeticion->get('tipoElemento');   
        $strTipoInfraestructura = $objPeticion->get('tipoInfraestructura');
        $strElementoTipo        = $objPeticion->get('elementoTipo');
        $strNuevaRuta           = $objPeticion->get('nuevaRuta') ? $objPeticion->get('nuevaRuta') : null;
        $strNombreElemento      = $objPeticion->query->get('query');
        $serviceUtil            = $this->get('schema.Util');
        $strUser                = $objSession->get("user");
        $strIpClient            = $objPeticion->getClientIp();
        $intIdOficina           = $objSession->get('idOficina');
        $strIdsCantonElemento   = $objPeticion->get('strIdsCantonElemento');
        $emComercial            = $this->getDoctrine()->getManager('telconet');
        $emGeneral              = $this->getDoctrine()->getManager('telconet_general');
        $emInfraestructura      = $this->getDoctrine()->getManager('telconet_infraestructura');
        $strRegistroUnicoRuta   = $objPeticion->get('registroUnico') ? $objPeticion->get('registroUnico') : null;
        $arrayParams            = array();
        
        try
        { 
            $strEstado         = 'Activo';
            if($objPeticion->get('estado'))
            {
                $strEstado   = $objPeticion->get('estado');
            }

            if (!$strNombreElemento)
            {
                $strNombreElemento = $objPeticion->get('nombreElemento');
            }      

            $strRegion           = "";
            $objInfoOficinaGrupo = $emComercial->getRepository('schemaBundle:InfoOficinaGrupo')->find($intIdOficina);
            
            if(is_object($objInfoOficinaGrupo))
            {
                $intCantonId     = $objInfoOficinaGrupo->getCantonId();
                if($intCantonId > 0)
                {
                    $objCanton  = $emGeneral->getRepository('schemaBundle:AdmiCanton')->find($intCantonId);
                    if(is_object($objCanton))
                    {
                        $strRegion  = $objCanton->getRegion();
                    }
                }
            }
            
            $arrayIdsCantonElemento = array();
            if(isset($strIdsCantonElemento) && !empty($strIdsCantonElemento))
            {
                $arrayIdsCantonElementoExplode          = explode(",", $strIdsCantonElemento);
                $arrayIdsCantonElementoExplodeUnique    = array_unique($arrayIdsCantonElementoExplode);
                foreach($arrayIdsCantonElementoExplodeUnique as $intIdCantonElemento)
                {
                    if(isset($intIdCantonElemento) && !empty($intIdCantonElemento) && $intIdCantonElemento > 0)
                    {
                        $arrayIdsCantonElemento[] = $intIdCantonElemento;
                    }
                }
            }
            
            if(empty($strNuevaRuta))
            {
                //Consultamos el tipo de infraestructura para obtener el nombre del modelo del elemento
                $objModeloElemento = $emInfraestructura->find('schemaBundle:AdmiModeloElemento', $strTipoInfraestructura);
                if(!is_object($objModeloElemento))
                {
                    throw new \Exception ('El modelo del elemento no se encuentra registrado.');
                }
                $strNombreModeloElemento = $objModeloElemento->getNombreModeloElemento();
                $intIdTipoElemento       = $objModeloElemento->getTipoElementoId()->getId();

                //Consultamos para saber cual es el elemento Inicio y el elemento Fin del tipo de infraestructura
                $arrayAdmiParametroDetRutas    = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->get('TIPO_DE_INFRAESTRUCTURA_POR_RUTAS',
                                                                'TECNICO',
                                                                '',
                                                                $strNombreModeloElemento,
                                                                $intIdTipoElemento,
                                                                '',
                                                                '',
                                                                '',
                                                                '',
                                                                $objSession->get('idEmpresa'));
                if(is_array($arrayAdmiParametroDetRutas) && !empty($arrayAdmiParametroDetRutas))
                {
                    foreach($arrayAdmiParametroDetRutas as $arrayDetParametro)
                    {
                        if ($strElementoTipo == 'Inicio')
                        {
                            $strNombreTipoElemento = $arrayDetParametro['valor2'];
                        }
                        else
                        {
                            $strNombreTipoElemento = $arrayDetParametro['valor3'];
                        }  
                    }
                }
            }
                       
            $arrayParams['strNombreElemento']       = $strNombreElemento;
            $arrayParams['strTipoElemento']         = $strNombreTipoElemento;
            $arrayParams['arrayIdsCantonElemento']  = $arrayIdsCantonElemento;
            $arrayParams['strRegion']               = $strRegion;
            $arrayParams['strEstado']               = $strEstado;
            $arrayParams['strEmpresaCod']           = $objSession->get('idEmpresa');
            
            if ($strRegistroUnicoRuta == 'S')
            {
                $arrayParams['unicoPorRuta'] = 'OK';
            }
            
            $arrayResult           = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->getArrayElementosPorTipo($arrayParams);

            $objJson = json_encode($arrayResult);

            $objRespuesta->setContent($objJson);
        }
        catch (\Exception $ex)
        {
            $serviceUtil->insertError('Telcos+', 'buscarElementoAction', $ex->getMessage(), $strUser, $strIpClient);
            $objRespuesta->setContent("Se presentaron errores al ejecutar la acción.");
            
        }
        return $objRespuesta;
    }
    
    /**
     * Documentación para el método 'getTipoTramoPorElementos'.
     *
     * Devuelve el tipo del tramo segun los elementos
     * 
     * Array $arrayParametros [intElementoA] [intElementoB]
     *
     * @author John Vera <javera@telconet.ec>
     */        
    
    public function getTipoTramoPorElementos($arrayParametros)
    {
        $intElementoA       = $arrayParametros['intElementoA'];
        $intElementoB       = $arrayParametros['intElementoB'];
        $strTipoElementoA   = '';
        $strTipoElementoB   = '';
        
        $emInfraestructura     = $this->get('doctrine')->getManager('telconet_infraestructura');        
        
        $objDetalleElementoA = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                 ->findOneBy(array( 'elementoId'    => $intElementoA,
                                                                    'estado'        => 'Activo',
                                                                    'detalleNombre' => 'TIPO LUGAR'));

        if(is_object($objDetalleElementoA))
        {
            $strTipoElementoA = $objDetalleElementoA->getDetalleValor();
        }
        
        $objDetalleElementoB = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                 ->findOneBy(array( 'elementoId'    => $intElementoB,
                                                                    'estado'        => 'Activo',
                                                                    'detalleNombre' => 'TIPO LUGAR'));
        
        if(is_object($objDetalleElementoB))
        {
            $strTipoElementoB = $objDetalleElementoB->getDetalleValor();
        }
        
        //consulto en los parametros
        if($strTipoElementoB == $strTipoElementoA && $strTipoElementoA != '' )
        {
            $strTipoLugar = $strTipoElementoB;
        }
        else
        {
            $strTipoLugar = 'DUCTO';
        }
        
        return $strTipoLugar;
    }
    
    /**
     * @Secure(roles="ROLE_400-5502")
     * Documentación para el método 'ajaxSaveRutaAction'.
     *
     * Metodo utilizado para guardar la ruta
     *
     * @author John Vera <javera@telconet.ec>
     */    
    
    public function ajaxSaveRutaAction() 
    {
      
        $emInfraestructura     = $this->get('doctrine')->getManager('telconet_infraestructura');
        $objRequest            = $this->get('request');
        $objSession            = $objRequest->getSession();
        $serviceUtil           = $this->get('schema.Util');
        $objResponse           = new JsonResponse();
        $arrayParametros       = array();
        $arrayResult           = array();   
       
        $arrayParametros['strElementoIdB']          = $objRequest->get('strElemntosB');
        $arrayParametros['strNombreElemento']       = $objRequest->get('objTxtNombreElemento');
        $arrayParametros['strDescripcionelemento']  = $objRequest->get('objTarDescripcionElemento');
        $arrayParametros['strClase']                = $objRequest->get('objCmbTipoLugar');
        $arrayParametros['intModelo']               = $objRequest->get('objCmbTipoElemento');
        $arrayParametros['intElementoIni']          = $objRequest->get('objCmbElementoIni');
        $arrayParametros['intElementoFin']          = $objRequest->get('objCmbElementoFin');
        $arrayParametros['intClaseTipoMedio']       = $objRequest->get('objCmbClaseMedio');
        
        $emInfraestructura->getConnection()->beginTransaction();  

        try
        {  

            if($arrayParametros['intModelo'])
            {            
                $objModeloElemento = $emInfraestructura->find('schemaBundle:AdmiModeloElemento', $arrayParametros['intModelo']);
            }
            else
            {
                throw new \Exception ('No se ha enviado el modelo.');
            }
            
            if(!is_object($objModeloElemento))
            {
                throw new \Exception ('El modelo del elemento no se encuentra registrado.');
            }
            
            //creo el elemento ruta
            $objElemento = new InfoElemento();
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

                //le creo el detalle de la clase
                if($arrayParametros['strClase'])
                {
                    //caracteristica para saber el propietario del ruta
                    $objDetalle = new InfoDetalleElemento();
                    $objDetalle->setElementoId($objElemento->getId());
                    $objDetalle->setDetalleNombre("CLASE");
                    $objDetalle->setDetalleValor($arrayParametros['strClase']);
                    $objDetalle->setDetalleDescripcion("Clase de la ruta.");
                    $objDetalle->setFeCreacion(new \DateTime('now'));
                    $objDetalle->setUsrCreacion($objSession->get('user'));
                    $objDetalle->setIpCreacion($objRequest->getClientIp());
                    $objDetalle->setEstado('Activo');
                    $emInfraestructura->persist($objDetalle);
                    $emInfraestructura->flush();
                }
                
                $arrayElementosContenido= json_decode($arrayParametros['strElementoIdB']);
                
                //agrego el nodo inicio y final al array de los elementos
                if($arrayParametros['intElementoIni'] > 0)
                {
                    array_unshift($arrayElementosContenido, $arrayParametros['intElementoIni']."|".$arrayParametros['intClaseTipoMedio']);
                }
                if($arrayParametros['intElementoFin'] > 0)
                {
                    array_push($arrayElementosContenido, $arrayParametros['intElementoFin']);
                }
                
                $strTipoTramo = 'INICIAL';                
                //ciclo en donde vamos relacionando todos los elementos
                for ($i=0; $i < count($arrayElementosContenido)-1; $i++)
                {
                    $arrayInfoA = explode("|", $arrayElementosContenido[$i]);
                    $arrayInfoB = explode("|", $arrayElementosContenido[$i+1]);
                    
                    $intElementoA       = $arrayInfoA[0];
                    $intElementoB       = $arrayInfoB[0];
                    $intClaseTipoMedio  = $arrayInfoA[1];
                    
                    if($intClaseTipoMedio > 0)
                    {
                        $arrayParametros['intClaseTipoMedio'] = $intClaseTipoMedio;
                    }
                    
                    //consulto los elementos
                    $objElementoA = $emInfraestructura->find('schemaBundle:InfoElemento', $intElementoA);
                    $objElementoB = $emInfraestructura->find('schemaBundle:InfoElemento', $intElementoB);
                    
                    if(is_object($objElementoB) && is_object($objElementoA))
                    {
                        $strNombre = $objElementoA->getNombreElemento().'-'.$objElementoB->getNombreElemento();
                    }
                    
                    $objTramo = new InfoTramo();
                    $objTramo->setElementoAId($intElementoA);
                    $objTramo->setElementoBId($intElementoB);
                    $objTramo->setTipoTramo($strTipoTramo);
                    $objTramo->setEstado("Activo");
                    $objTramo->setRutaId($objElemento->getId());                    
                    $objTramo->setUsrCreacion($objSession->get('user'));
                    $objTramo->setFeCreacion(new \DateTime('now'));
                    $objTramo->setIpCreacion($objRequest->getClientIp());                    
                    
                    $emInfraestructura->persist($objTramo);
                    $emInfraestructura->flush();
                    
                    $strNombreTramo = $strNombre.'-T'.$objTramo->getId();
                    $objTramo->setNombreTramo($strNombreTramo);
                    $emInfraestructura->persist($objTramo);
                    $emInfraestructura->flush();
                    
                    //ingreso el detalle del tramo con el tipo de la fibra
                    $objDetalleTramo = new InfoDetalleTramo();
                    $objDetalleTramo->setTramoId($objTramo->getId());
                    $objDetalleTramo->setNombreDetalle('CLASE_TIPO_MEDIO');
                    $objDetalleTramo->setValorDetalle($arrayParametros['intClaseTipoMedio']);
                    $objDetalleTramo->setEstado("Activo");
                    $objDetalleTramo->setUsrCreacion($objSession->get('user'));
                    $objDetalleTramo->setFeCreacion(new \DateTime('now'));
                    $objDetalleTramo->setIpCreacion($objRequest->getClientIp());
                    $emInfraestructura->persist($objDetalleTramo);
                    $emInfraestructura->flush();
                    
                    $arrayParametrosLugar['intElementoA'] = $intElementoA;
                    $arrayParametrosLugar['intElementoB'] = $intElementoB;
                    $strTipoLugar = $this->getTipoTramoPorElementos($arrayParametrosLugar);
                    //ingreso el detalle del tipo de lugar
                    $objDetalleTramo = new InfoDetalleTramo();
                    $objDetalleTramo->setTramoId($objTramo->getId());
                    $objDetalleTramo->setNombreDetalle("TIPO LUGAR");
                    $objDetalleTramo->setValorDetalle($strTipoLugar);
                    $objDetalleTramo->setEstado("Activo");
                    $objDetalleTramo->setUsrCreacion($objSession->get('user'));
                    $objDetalleTramo->setFeCreacion(new \DateTime('now'));
                    $objDetalleTramo->setIpCreacion($objRequest->getClientIp());
                    $emInfraestructura->persist($objDetalleTramo);
                    $emInfraestructura->flush();   
                    
                    $strTipoTramo = '';

                }   
                
                //historial elemento
                $objHistorialElemento = new InfoHistorialElemento();
                $objHistorialElemento->setElementoId($objElemento);
                $objHistorialElemento->setEstadoElemento("Activo");
                $objHistorialElemento->setObservacion("Se ingresó el  : " . $arrayParametros['strNombreElemento']);
                $objHistorialElemento->setUsrCreacion($objSession->get('user'));
                $objHistorialElemento->setFeCreacion(new \DateTime('now'));
                $objHistorialElemento->setIpCreacion($objRequest->getClientIp());
                $emInfraestructura->persist($objHistorialElemento);
                $emInfraestructura->flush();                

                $emInfraestructura->commit();  
                $arrayResult['strStatus']        = 'OK';
                $arrayResult['strMessageStatus'] = 'Transacción Exitosa';                
                $arrayResult['intElemento']      = $objElemento->getId();
            }             
        } 
        catch (\Exception $ex) 
        {   
            $arrayResult['strStatus']        = 'ERROR';
            $arrayResult['strMessageStatus'] = 'Error al crear la ruta';
           
             if($emInfraestructura->getConnection()->isTransactionActive())
            {
                $emInfraestructura->rollback();
            }
            
            $serviceUtil->insertError('Telcos+',
                                      'InfoElementoRutaController->ajaxSaveRutaAction',
                                      $ex->getMessage(),
                                      $objSession->get('user'),
                                      $objRequest->getClientIp());
            
            $emInfraestructura->close();
        }

        $objResponse->setData($arrayResult);
        return $objResponse;
    }    
    
    
    /**
     * @Secure(roles="ROLE_400-5503")
     * Documentación para el método 'ajaxEditTramoAction'.
     *
     * Metodo utilizado para guardar la ruta
     *
     * @author John Vera <javera@telconet.ec>
     */    
    
    public function ajaxEditTramoAction() 
    {
      
        $emInfraestructura     = $this->get('doctrine')->getManager('telconet_infraestructura');
        $objRequest            = $this->get('request');
        $objSession            = $objRequest->getSession();
        $serviceUtil           = $this->get('schema.Util');
        $objResponse           = new JsonResponse();
        $arrayParametros       = array();
        $arrayResult           = array();   
       
        $arrayParametros['intRelacion']         = $objRequest->get('idTramo');
        $arrayParametros['intRuta']             = $objRequest->get('idRuta');
        $arrayParametros['intElementoA']        = $objRequest->get('idElementoA');
        $arrayParametros['intElementoB']        = $objRequest->get('idElementoB');
        $arrayParametros['intClaseTipoMedio']   = $objRequest->get('idTipoFibra');
        
        $emInfraestructura->getConnection()->beginTransaction();             
         
        try
        {               
            if($arrayParametros['intRelacion'])
            {            
                $objRelacionElemento = $emInfraestructura->find('schemaBundle:InfoTramo', $arrayParametros['intRelacion']);
            }
            else
            {
                throw new \Exception ('No se ha enviado el id de la relacion.');
            }
            
            if(!is_object($objRelacionElemento))
            {
                throw new \Exception ('La relación no se encuentra registrado.');
            }
            
            if($arrayParametros['intElementoA'])
            {
                $objRelacionElementoIni = $emInfraestructura->getRepository('schemaBundle:InfoTramo')
                                                            ->findOneBy(array('elementoBId' => $objRelacionElemento->getElementoAId(),
                                                                              'estado'      => 'Activo', 
                                                                              'rutaId'      => $arrayParametros['intRuta'] ));
                
                $objElementoA = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($arrayParametros['intElementoA']);
                
                $objElementoAnt = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($objRelacionElemento->getElementoAId());
                
                if(is_object($objElementoAnt) && is_object($objElementoA))
                {
                    $strHistorial = 'Elemento Nuevo: '. $objElementoA->getNombreElemento()
                                    .'<br> Elemento Anterior: '.$objElementoAnt->getNombreElemento().' <br>';
                }

                
                if(is_object($objRelacionElementoIni))
                {   
                    $objElementoAntA= $emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($objRelacionElementoIni->getElementoAId());
                    if(is_object($objElementoAntA))
                    {
                        //actualizo el registro con el cual se conecta
                        $objRelacionElementoIni->setElementoBId($arrayParametros['intElementoA']);
                        $objRelacionElementoIni->setNombreTramo($objElementoAntA->getNombreElemento().'-'.$objElementoA->getNombreElemento().
                                                                '-T'.$objRelacionElementoIni->getId());
                        $emInfraestructura->persist($objRelacionElementoIni);
                        $emInfraestructura->flush();                    
                    }
                }
                
                $objRelacionElemento->setElementoAId($arrayParametros['intElementoA']);
                $emInfraestructura->persist($objRelacionElemento);
                $emInfraestructura->flush();
            }
            
            if($arrayParametros['intElementoB'])
            {
                $objRelacionElementoFin = $emInfraestructura->getRepository('schemaBundle:InfoTramo')
                                                            ->findOneBy(array('elementoAId' => $objRelacionElemento->getElementoBId(),
                                                                              'estado'      => 'Activo',
                                                                              'rutaId'      => $arrayParametros['intRuta']));
                
                $objElementoB = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($arrayParametros['intElementoB']);
                
                $objElementoAnt = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($objRelacionElemento->getElementoBId());
                
                if(is_object($objElementoAnt) && is_object($objElementoB))
                {
                    $strHistorial .= 'Elemento Nuevo: '. $objElementoB->getNombreElemento()
                                     .'<br> Elemento Anterior: '.$objElementoAnt->getNombreElemento().' <br>';
                }

                
                if(is_object($objRelacionElementoFin))
                {
                    $objElementoFinB= $emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($objRelacionElementoFin->getElementoBId());
                    if(is_object($objElementoFinB))
                    {
                        //actualizo el registro con el cual se conecta
                        $objRelacionElementoFin->setElementoAId($arrayParametros['intElementoB']);
                        $objRelacionElementoFin->setNombreTramo($objElementoB->getNombreElemento().'-'.$objElementoFinB->getNombreElemento().
                                                                '-T'.$objRelacionElementoFin->getId());
                        $emInfraestructura->persist($objRelacionElementoFin);
                        $emInfraestructura->flush();     
                    }
                }
                
                $objRelacionElemento->setElementoBId($arrayParametros['intElementoB']);
                $emInfraestructura->persist($objRelacionElemento);
                $emInfraestructura->flush();
                
            }
            
            //si existe historial renombre el tramo
            if($strHistorial)
            {
                $objElementA = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($objRelacionElemento->getElementoAId());
                $objElementB = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($objRelacionElemento->getElementoBId());
                
                if(is_object($objElementA) && is_object($objElementB))
                {
                    $objRelacionElemento->setNombreTramo($objElementA->getNombreElemento().'-'.$objElementB->getNombreElemento().
                                                         '-T'.$objRelacionElemento->getId());
                    $emInfraestructura->persist($objRelacionElemento);
                    $emInfraestructura->flush();       
                }
                
                //actualizo el tipo de lugar del tramo segun los elementos
                $objDetalleTramo = $emInfraestructura->getRepository("schemaBundle:InfoDetalleTramo")
                                                     ->findOneBy(array('nombreDetalle'=> 'TIPO LUGAR',
                                                                       'estado'       => 'Activo',
                                                                       'tramoId'      => $objRelacionElemento->getId()));
                if(is_object($objDetalleTramo))
                {
                    $arrayParametrosLugar['intElementoA'] = $objRelacionElemento->getElementoAId();
                    $arrayParametrosLugar['intElementoB'] = $objRelacionElemento->getElementoBId();
                    $strTipoLugar = $this->getTipoTramoPorElementos($arrayParametrosLugar);
                    
                    $objDetalleTramo->setValorDetalle($strTipoLugar);     
                    $emInfraestructura->persist($objDetalleTramo);
                    $emInfraestructura->flush();
                }
                
                if($arrayParametros['intClaseTipoMedio'] != '')
                {                
                    //actualizo el tipo de lugar del tramo segun los elementos
                    $objDetalleTramo = $emInfraestructura->getRepository("schemaBundle:InfoDetalleTramo")
                                                         ->findOneBy(array('nombreDetalle'=> 'CLASE_TIPO_MEDIO',
                                                                           'estado'       => 'Activo',
                                                                           'tramoId'      => $objRelacionElemento->getId()));
                    if(is_object($objDetalleTramo))
                    {

                        $objDetalleTramo->setValorDetalle($arrayParametros['intClaseTipoMedio']);     
                        $emInfraestructura->persist($objDetalleTramo);
                        $emInfraestructura->flush();

                    }   
                }
                
            }
            
            $objRuta = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($arrayParametros['intRuta']);
            
            if(is_object($objRuta))
            {
                //historial elemento
                $objHistorialElemento = new InfoHistorialElemento();
                $objHistorialElemento->setElementoId($objRuta);
                $objHistorialElemento->setEstadoElemento($objRuta->getEstado());
                $objHistorialElemento->setObservacion("Edición de Tramo T".$objRelacionElemento->getId().": <br> ".$strHistorial);
                $objHistorialElemento->setUsrCreacion($objSession->get('user'));
                $objHistorialElemento->setFeCreacion(new \DateTime('now'));
                $objHistorialElemento->setIpCreacion($objRequest->getClientIp());
                $emInfraestructura->persist($objHistorialElemento);
                $emInfraestructura->flush();    
                           
            }

            $emInfraestructura->commit();  
            $arrayResult['strStatus']        = 'OK';
            $arrayResult['strMessageStatus'] = 'Transacción Exitosa';                

        } 
        catch (\Exception $ex) 
        {   
            $arrayResult['strStatus']        = 'ERROR';
            $arrayResult['strMessageStatus'] = 'Error al crear la ruta';
           
             if($emInfraestructura->getConnection()->isTransactionActive())
            {
                $emInfraestructura->rollback();
            }
            
            $serviceUtil->insertError('Telcos+',
                                      'InfoElementoRutaController->ajaxEditTramoAction',
                                      $ex->getMessage(),
                                      $objSession->get('user'),
                                      $objRequest->getClientIp());
            
            $emInfraestructura->close();
        }
        
        $objResponse->setData($arrayResult);
        return $objResponse;
    }       
    
    
    /**
     * @Secure(roles="ROLE_400-5504")
     * Documentación para el método 'ajaxEditTramoAction'.
     *
     * Metodo utilizado para guardar la ruta
     *
     * @author John Vera <javera@telconet.ec>
     */    
    
    public function ajaxAddTramoAction() 
    {
      
        $emInfraestructura     = $this->get('doctrine')->getManager('telconet_infraestructura');
        $objRequest            = $this->get('request');
        $objSession            = $objRequest->getSession();
        $serviceUtil           = $this->get('schema.Util');
        $objResponse           = new JsonResponse();
        $arrayParametros       = array();
        $arrayResult           = array();   
       
        $arrayParametros['intRelacion']         = $objRequest->get('idTramo');
        $arrayParametros['intRuta']             = $objRequest->get('idRuta');
        $arrayParametros['idElemento']          = $objRequest->get('idElemento');
        $arrayParametros['intClaseTipoMedio']   = $objRequest->get('tipoFibra');
        $emInfraestructura->getConnection()->beginTransaction();             

        try
        {               
            if($arrayParametros['intRelacion'])
            {            
                $objRelacionElemento = $emInfraestructura->find('schemaBundle:InfoTramo', $arrayParametros['intRelacion']);
            }
            else
            {
                throw new \Exception ('No se ha enviado el id de la relacion.');
            }
            
            if(!is_object($objRelacionElemento))
            {
                throw new \Exception ('La relación no se encuentra registrado.');
            }
            
            //obtengo la siguiente relacion
            $objRelacionElementoFin = $emInfraestructura->getRepository('schemaBundle:InfoTramo')
                                                        ->findOneBy(array('elementoAId' => $objRelacionElemento->getElementoBId(),
                                                                          'estado'      => 'Activo',
                                                                          'rutaId'      => $arrayParametros['intRuta']));
            
            $objElementoA = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($objRelacionElemento->getElementoBId());
            
            if(!is_object($objElementoA))            
            {
                throw new \Exception ('Elemento B del tramo no se encuentra registrado.');
            }
            
            $objElementoNuevo = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($arrayParametros['idElemento']);
            
            //creo el nuevo tramo que una las relaciones
            $objRelacionElemento = new InfoTramo();
            $objRelacionElemento->setElementoAId($objElementoA->getId());
            $objRelacionElemento->setElementoBId($arrayParametros['idElemento']);
            $objRelacionElemento->setEstado("Activo");
            $objRelacionElemento->setRutaId($arrayParametros['intRuta']);            
            $objRelacionElemento->setUsrCreacion($objSession->get('user'));
            $objRelacionElemento->setFeCreacion(new \DateTime('now'));
            $objRelacionElemento->setIpCreacion($objRequest->getClientIp());   

            $emInfraestructura->persist($objRelacionElemento);
            $emInfraestructura->flush();
            
            if(is_object($objElementoA) && is_object($objElementoNuevo))
            {
                $strNombreTramo = $objElementoA->getNombreElemento().'-'.$objElementoNuevo->getNombreElemento().'-T'.$objRelacionElemento->getId();
                $objRelacionElemento->setNombreTramo($strNombreTramo);
                $emInfraestructura->persist($objRelacionElemento);
                $emInfraestructura->flush();                
            }
            
            
            if(is_object($objRelacionElementoFin))
            {
                $objElementoB = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($objRelacionElementoFin->getElementoBId());
                
                if(is_object($objElementoB))
                {
                    //actualizo el registro con el cual se conecta
                    $objRelacionElementoFin->setElementoAId($arrayParametros['idElemento']);
                    $objRelacionElementoFin->setNombreTramo($objElementoNuevo->getNombreElemento().'-'.$objElementoB->getNombreElemento().
                                                            '-T'.$objRelacionElementoFin->getId());
                    $emInfraestructura->persist($objRelacionElementoFin);
                    $emInfraestructura->flush();     
                }                
            }            
            
            $strHistorial = "Se agrego el tramo: <br>".$strNombreTramo;
           
            $objRuta = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($arrayParametros['intRuta']);
            
            if(is_object($objRuta))
            {
                //historial elemento
                $objHistorialElemento = new InfoHistorialElemento();
                $objHistorialElemento->setElementoId($objRuta);
                $objHistorialElemento->setEstadoElemento($objRuta->getEstado());
                $objHistorialElemento->setObservacion($strHistorial);
                $objHistorialElemento->setUsrCreacion($objSession->get('user'));
                $objHistorialElemento->setFeCreacion(new \DateTime('now'));
                $objHistorialElemento->setIpCreacion($objRequest->getClientIp());
                $emInfraestructura->persist($objHistorialElemento);
                $emInfraestructura->flush();    
                           
            }

            $arrayParametrosLugar['intElementoA'] = $objRelacionElemento->getElementoAId();
            $arrayParametrosLugar['intElementoB'] = $objRelacionElemento->getElementoBId();
            $strTipoLugar = $this->getTipoTramoPorElementos($arrayParametrosLugar);

            $objDetalleTramo = new InfoDetalleTramo();
            $objDetalleTramo->setTramoId($objRelacionElemento->getId());
            $objDetalleTramo->setNombreDetalle("TIPO LUGAR");
            $objDetalleTramo->setValorDetalle($strTipoLugar);
            $objDetalleTramo->setEstado("Activo");
            $objDetalleTramo->setUsrCreacion($objSession->get('user'));
            $objDetalleTramo->setFeCreacion(new \DateTime('now'));
            $objDetalleTramo->setIpCreacion($objRequest->getClientIp());
            $emInfraestructura->persist($objDetalleTramo);
            $emInfraestructura->flush();

            if($arrayParametros['intClaseTipoMedio'] != '')
            {                
                //ingreso el detalle del tramo con el tipo de la fibra
                $objDetalleTramo = new InfoDetalleTramo();
                $objDetalleTramo->setTramoId($objRelacionElemento->getId());
                $objDetalleTramo->setNombreDetalle('CLASE_TIPO_MEDIO');
                $objDetalleTramo->setValorDetalle($arrayParametros['intClaseTipoMedio']);
                $objDetalleTramo->setEstado("Activo");
                $objDetalleTramo->setUsrCreacion($objSession->get('user'));
                $objDetalleTramo->setFeCreacion(new \DateTime('now'));
                $objDetalleTramo->setIpCreacion($objRequest->getClientIp());
                $emInfraestructura->persist($objDetalleTramo);
                $emInfraestructura->flush();                   

            }            

            $emInfraestructura->commit();  
            $arrayResult['strStatus']        = 'OK';
            $arrayResult['strMessageStatus'] = 'Transacción Exitosa';                

        } 
        catch (\Exception $ex) 
        {   
            $arrayResult['strStatus']        = 'ERROR';
            $arrayResult['strMessageStatus'] = 'Error al crear la ruta';
           
             if($emInfraestructura->getConnection()->isTransactionActive())
            {
                $emInfraestructura->rollback();
            }
            
            $serviceUtil->insertError('Telcos+',
                                      'InfoElementoRutaController->ajaxEditTramoAction',
                                      $ex->getMessage(),
                                      $objSession->get('user'),
                                      $objRequest->getClientIp());
            
            $emInfraestructura->close();
        }
        
        $objResponse->setData($arrayResult);
        return $objResponse;
    }        
    
   /**
    * @Secure(roles="ROLE_400-5501")
    * Documentación para el método 'ajaxDeleteAction'.
    *
    * Metodo utilizado para eliminar un ruta
    * @param Object  $objResponse mensajes informativos.
    * 
    * @author John Vera <javera@telconet.ec>  
    * @version 1.0 08-03-2017
    * 
    * @author Lizbeth Cruz <javera@telconet.ec>  
    * @version 1.1 09-04-2018 Se modifica el orden de eliminación del elemento para realizar la consulta de los tramos
    * 
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
                throw $this->createNotFoundException('No se encontró la entidad InfoElementoRuta.');
            }
            
            //elimino los tramos
            $arrayParams                     = array();
            $arrayParams['intElemento']      = $arrayParametros['intIdElemento'];

            $arrayResp = $this->getArrayTramosAction($arrayParams);

            foreach($arrayResp['encontrados'] as $arrayTramo)
            {
                if($arrayTramo['idRelacion'] > 0)
                {
                    $objRelacionElemento = $emInfraestructura->getRepository('schemaBundle:InfoTramo')->find($arrayTramo['idRelacion']);
                    if(is_object($objRelacionElemento))
                    {
                        $objRelacionElemento->setEstado('Eliminado');
                        $emInfraestructura->persist($objRelacionElemento);
                        $emInfraestructura->flush();
                    }
                }
            }            
            //empresa elemento
            $objEmpresaElemento = $emInfraestructura->getRepository('schemaBundle:InfoEmpresaElemento')
                                                    ->findOneBy(array( "elementoId" =>$arrayParametros['intIdElemento']));
            
            //detalle elemento
            $arrayInfoDetallesElemento = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                           ->findByElementoId($arrayParametros['intIdElemento']);

            foreach($arrayInfoDetallesElemento as $objInfoDetalleElemento)
            {
                $objInfoDetalleElemento->setEstado('Eliminado');

                $emInfraestructura->persist($objInfoDetalleElemento);
                $emInfraestructura->flush();
            }

                
            if(is_object($objEmpresaElemento))
            {
                $objEmpresaElemento->setEstado("Eliminado");
                $emInfraestructura->persist($objEmpresaElemento);
            }
            else
            {
                throw new \Exception('Elemento no existe en la empresa.');
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

            $emInfraestructura->flush();
            $emInfraestructura->getConnection()->commit();

            $arrayResult['strStatus'] = 'OK';
            $arrayResult['strMessageStatus'] = 'Elemento ruta eliminado';
        }
        catch (\Exception $ex) 
        {   
            $arrayResult['strStatus']        = 'ERROR';
            $arrayResult['strMessageStatus'] = 'Error en la eliminacion de la ruta';
           
             if($emInfraestructura->getConnection()->isTransactionActive())
            {
                $emInfraestructura->rollback();
            }
            
            $serviceUtil->insertError('Telcos+',
                                      'InfoElementoRutaController->ajaxDeleteAction',
                                      $ex->getMessage(),
                                      $objSession->get('user'),
                                      $objRequest->getClientIp());
            
            $emInfraestructura->close();
        }
        
      
        $objResponse->setData($arrayResult);
        return $objResponse;
        
    }
    
   /**
    * @Secure(roles="ROLE_400-5500")
    * Documentación para el método 'ajaxEditAction'.
    *
    * Metodo utilizado para editar la informacion un ruta
    * 
    * @param Object  $objResponse mensajes informativos.
    * 
    * @author John Vera <javera@telconet.ec>  
    * @version 1.0 08-03-2017
    * 
    * @author Lizbeth Cruz <mlcruz@telconet.ec>
    * @version 1.1 06-04-2018 Se agrega guardar el nombre del elemento ya que se permite editar el nombre desde el formulario
    * 
    * @author Antonio Ayala <afayala@telconet.ec>
    * @version 1.2 18-03-2022 Se agrega el nombre del tipo del elemento en el historial
    */
    public function ajaxEditAction() 
    {
        $objResponse       = new JsonResponse();  
        $emInfraestructura = $this->get('doctrine')->getManager('telconet_infraestructura');
        $objRequest        = $this->get('request');
        $objSession        = $objRequest->getSession();
        $serviceUtil       = $this->get('schema.Util');
        $strHistorial      = '';
        
        $arrayParametros                           = array();
        $arrayParametros['strNombreElemento']      = $objRequest->get('objTxtNombreElemento');
        $arrayParametros['strDetalleClase']        = $objRequest->get('objCmbDetalle');
        $arrayParametros['strDescripcionelemento'] = $objRequest->get('objTarDescripcionElemento');
        $arrayParametros['strModeloElemento']      = $objRequest->get('objCmbModelo');
        $arrayParametros['intIdElemento']          = $objRequest->get('objIdElemento');
        $arrayParametros['intIdTipoElemento']      = $objRequest->get('objIdTipoElemento');

        $emInfraestructura->getConnection()->beginTransaction();
        $arrayResult       = array();        

        try
        {    
            
            $objElemento       = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($arrayParametros['intIdElemento']);
            $objModeloElemento = $emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                                   ->findOneBy(array('nombreModeloElemento'=> $arrayParametros['strModeloElemento'], 
                                                                     'tipoElementoId'      => $arrayParametros['intIdTipoElemento'],
                                                                     'estado'              => 'Activo'));

            if (!is_object($objElemento))
            {
                throw $this->createNotFoundException('No se encontro entidad InfoElementoRuta.');
            }
            if (!is_object($objModeloElemento))
            {
                throw $this->createNotFoundException('No se encontro modelo para el tipo de elemento');
            }
            
            if($objElemento->getNombreElemento() != $arrayParametros['strNombreElemento'] )
            {
                $strHistorial = 'Nombre: <br> Nuevo: '.$arrayParametros['strNombreElemento'].' <br> Anterior:'.
                                $objElemento->getNombreElemento().'<br>';
                
                $objElemento->setNombreElemento($arrayParametros['strNombreElemento']);
            }
            
            if($objElemento->getDescripcionElemento() != $arrayParametros['strDescripcionelemento'] )
            {
                $strHistorial = 'Descripción: <br> Nuevo: '.$arrayParametros['strDescripcionelemento'].' <br> Anterior:'.
                                $objElemento->getDescripcionElemento().'<br>';
                
                $objElemento->setDescripcionElemento($arrayParametros['strDescripcionelemento']);
                

            }
            
            if($objElemento->getModeloElementoId()->getId() != $objModeloElemento->getId() )
            {
                $strHistorial .= 'Modelo: <br> Nuevo: '.$objModeloElemento->getNombreModeloElemento().' <br> Anterior:'.
                                  $objElemento->getModeloElementoId()->getNombreModeloElemento().'<br>';
                
                $objElemento->setModeloElementoId($objModeloElemento);               
             
            }
            
            $emInfraestructura->persist($objElemento);
            
      
            //caracteristica para saber el propietario del ruta
            $objDetalleElemento = $emInfraestructura->getRepository("schemaBundle:InfoDetalleElemento")
                                                    ->findOneBy(array('detalleNombre'   => 'CLASE',
                                                                      'elementoId'      => $objElemento->getId(),
                                                                      'estado'          => 'Activo'));

            if(is_object($objDetalleElemento) && $objDetalleElemento->getDetalleValor() != $arrayParametros['strDetalleClase'])
            {

                $strNombreTipoElemento = '';
                //Consultamos el id del Elemento
                $objTipoElemento = $emInfraestructura->getRepository('schemaBundle:AdmiTipoElemento')
                                                       ->findOneBy(array('id'     => $objDetalleElemento->getDetalleValor(),
                                                                         'estado' => 'Activo'));             
                if(is_object($objTipoElemento))
                {
                    $strNombreTipoElemento = $objTipoElemento->getNombreTipoElemento();
                }
                $strHistorial .= 'Tipo de Ruta: <br> Nuevo: '.$arrayParametros['strDetalleClase'].' <br> Anterior:'.
                                 $strNombreTipoElemento.'<br>';   

                $objDetalleElemento->setDetalleValor($arrayParametros['intIdTipoElemento']);     
                $emInfraestructura->persist($objDetalleElemento);  
            }

            if($strHistorial)
            {
                //historial elemento
                $objHistorialElemento = new InfoHistorialElemento();
                $objHistorialElemento->setElementoId($objElemento);
                $objHistorialElemento->setEstadoElemento($objElemento->getEstado());
                $objHistorialElemento->setObservacion($strHistorial);
                $objHistorialElemento->setUsrCreacion($objSession->get('user'));
                $objHistorialElemento->setFeCreacion(new \DateTime('now'));
                $objHistorialElemento->setIpCreacion($objRequest->getClientIp());
                $emInfraestructura->persist($objHistorialElemento);          
                
                $emInfraestructura->flush();
                $emInfraestructura->commit();                   
            }
          
            $arrayResult['strStatus']        = 'OK';
            $arrayResult['strMessageStatus'] = 'Transacción Exitosa';
            
         
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
                                      'InfoElementoRutaController->ajaxEditAction',
                                      $ex->getMessage(),
                                      $objSession->get('user'),
                                      $objRequest->getClientIp());
            
            $emInfraestructura->close();
        }
        
       
        $objResponse->setData($arrayResult);
        return $objResponse;
    }
    
    /**
    * Documentación para el método 'getTipoElementoRutaAction'.
    *
    * Metodo utilizado para obtener los tipos de elemento dependiendo del tipo de infraestructura seleccionado
    * 
    * @param Request $objRequest
    * @param Object  $objRespuesta con informacion de los tipos de elementos.
    *
    * @author Antonio Ayala <afayala@telconet.ec>          
    * @version 1.0 21-05-2021
    */
    public function getTipoElementoRutaAction()
    {
        ini_set('max_execution_time', 400000);
        $objRespuesta           = new JsonResponse();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        $objPeticion            = $this->get('request');
        $objSession             = $this->get('session');
        $strNombreTipoElemento  = $objPeticion->get('tipoElemento');   
        $strTipoInfraestructura = $objPeticion->get('tipoInfraestructura');
        
        $emInfraestructura      = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emGeneral              = $this->getDoctrine()->getManager('telconet_general');
        $serviceUtil            = $this->get('schema.Util');
        
        try
        {
            //Consultamos el tipo de infraestructura para obtener el nombre del modelo del elemento
            $objModeloElemento = $emInfraestructura->find('schemaBundle:AdmiModeloElemento', $strTipoInfraestructura);
            if(!is_object($objModeloElemento))
            {
                throw new \Exception ('El modelo del elemento no se encuentra registrado.');
            }
            $strNombreModeloElemento = $objModeloElemento->getNombreModeloElemento();
            $intIdTipoElemento       = $objModeloElemento->getTipoElementoId()->getId();

            //Consultamos los tipos de elementos que pertenecen a la ruta y al tipo de infraestructura
            $arrayAdmiParametroDetRutas    = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                       ->get('TIPO_DE_INFRAESTRUCTURA_POR_RUTAS',
                                                             'TECNICO',
                                                             '',
                                                             $strNombreModeloElemento,
                                                             $intIdTipoElemento,
                                                             '',
                                                             '',
                                                             '',
                                                             '',
                                                             $objSession->get('idEmpresa'));
            if(is_array($arrayAdmiParametroDetRutas) && !empty($arrayAdmiParametroDetRutas))
            {
                $arrayTipoElemento = explode(",",$arrayAdmiParametroDetRutas[0]['valor4']);
                    
                foreach($arrayTipoElemento as $arrayElemento)
                {
                    //Consultamos el id del Elemento
                    $objTipoElemento = $emInfraestructura->getRepository('schemaBundle:AdmiTipoElemento')
                                                       ->findOneBy(array('nombreTipoElemento' => $arrayElemento,
                                                                         'estado'             => 'Activo'));             
                    if(is_object($objTipoElemento))
                    {
                        $arrayItem                      = array();
                        $arrayItem['idTipoElemento']    = $objTipoElemento->getId();
                        $arrayItem['nombreTipoElemento']= $objTipoElemento->getNombreTipoElemento();
                        $arrayEncontrados[]             = $arrayItem;
                    }
                }
            }
        
        }
        catch (\Exception $ex) 
        {   
            $serviceUtil->insertError('Telcos+', 'buscarElementoAction', $ex->getMessage(), $strUser, $strIpClient);
            $objRespuesta->setContent("Se presentaron errores al ejecutar la acción.");
        }
        
        $arrayResultado['total']           = count($arrayEncontrados);
        $arrayResultado['encontrados']     = $arrayEncontrados;
        
        $objRespuesta->setData($arrayResultado);        
        
        return $objRespuesta;
    }
    
    /**
     * @Secure(roles="ROLE_400-5503")
     * Documentación para el método 'ajaxEliminarTramoAction'.
     *
     * Metodo utilizado para eliminar el tramo de esa ruta
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.0 24-05-2021
     */    
    
    public function ajaxEliminarTramoAction() 
    {
      
        $emInfraestructura     = $this->get('doctrine')->getManager('telconet_infraestructura');
        $objRequest            = $this->get('request');
        $objSession            = $objRequest->getSession();
        $serviceUtil           = $this->get('schema.Util');
        $objResponse           = new JsonResponse();
        $arrayParametros       = array();
        $arrayResult           = array();   
       
        $arrayParametros['intRelacion']         = $objRequest->get('idTramo');
        $arrayParametros['intRuta']             = $objRequest->get('idRuta');
        $arrayParametros['intElementoA']        = $objRequest->get('idElementoA');
        $arrayParametros['intElementoB']        = $objRequest->get('idElementoB');
                
        $emInfraestructura->getConnection()->beginTransaction();             
         
        try
        {               
            if($arrayParametros['intRelacion'])
            {            
                $objRelacionElemento = $emInfraestructura->find('schemaBundle:InfoTramo', $arrayParametros['intRelacion']);
            }
            else
            {
                throw new \Exception ('No se ha enviado el id de la relacion.');
            }
            
            if(!is_object($objRelacionElemento))
            {
                throw new \Exception ('La relación no se encuentra registrado.');
            }
            
            if($arrayParametros['intElementoA'])
            {
                $objRelacionElementoIni = $emInfraestructura->getRepository('schemaBundle:InfoTramo')
                                                            ->findOneBy(array('elementoBId' => $objRelacionElemento->getElementoAId(),
                                                                              'estado'      => 'Activo', 
                                                                              'rutaId'      => $arrayParametros['intRuta'] ));
                
                $objElementoB = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($arrayParametros['intElementoB']);
                
                if(is_object($objRelacionElementoIni))
                {   
                    $objElementoA = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($objRelacionElementoIni->getElementoAId());

                    //modifico el elemento que se elimina al nuevo elemento
                    $objRelacionElementoIni->setElementoBId($objRelacionElemento->getElementoBId());
                    //actualizo el nombre del tramo con el nuevo elemento
                    $objRelacionElementoIni->setNombreTramo($objElementoA->getNombreElemento().'-'.$objElementoB->getNombreElemento().
                                                                '-T'.$objRelacionElementoIni->getId());
                    $emInfraestructura->persist($objRelacionElementoIni);
                    $emInfraestructura->flush(); 
                }
                
                $objRelacionElemento->setEstado('Eliminado');
                $emInfraestructura->persist($objRelacionElemento);
                $emInfraestructura->flush();
                
                //actualizo el estado a eliminado en la info_detalle_tramo
                $objDetalleTramoLugar = $emInfraestructura->getRepository("schemaBundle:InfoDetalleTramo")
                                                     ->findOneBy(array('nombreDetalle'=> 'TIPO LUGAR',
                                                                       'estado'       => 'Activo',
                                                                       'tramoId'      => $objRelacionElemento->getId()));
                if(is_object($objDetalleTramoLugar))
                {
                    $objDetalleTramoLugar->setEstado('Eliminado');     
                    $emInfraestructura->persist($objDetalleTramoLugar);
                    $emInfraestructura->flush();
                }
                
                $objDetalleTramoClase = $emInfraestructura->getRepository("schemaBundle:InfoDetalleTramo")
                                                     ->findOneBy(array('nombreDetalle'=> 'CLASE_TIPO_MEDIO',
                                                                       'estado'       => 'Activo',
                                                                       'tramoId'      => $objRelacionElemento->getId()));
                if(is_object($objDetalleTramoClase))
                {
                    $objDetalleTramoClase->setEstado('Eliminado');     
                    $emInfraestructura->persist($objDetalleTramoClase);
                    $emInfraestructura->flush();
                }
            }
            
            $objElementoEliminado = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($arrayParametros['intElementoA']);
            if(is_object($objElementoEliminado))
            {
                $strHistorial = 'Elemento: '. $objElementoEliminado->getNombreElemento();
            }
            
            //si existe historial renombre el tramo
            if($strHistorial)
            {
                //actualizo el tipo de lugar del tramo segun los elementos
                $objDetalleTramo = $emInfraestructura->getRepository("schemaBundle:InfoDetalleTramo")
                                                     ->findOneBy(array('nombreDetalle'=> 'TIPO LUGAR',
                                                                       'estado'       => 'Activo',
                                                                       'tramoId'      => $objRelacionElementoIni->getId()));
                if(is_object($objDetalleTramo))
                {
                    $arrayParametrosLugar['intElementoA'] = $objRelacionElementoIni->getElementoAId();
                    $arrayParametrosLugar['intElementoB'] = $objRelacionElementoIni->getElementoBId();
                    $strTipoLugar = $this->getTipoTramoPorElementos($arrayParametrosLugar);
                    
                    $objDetalleTramo->setValorDetalle($strTipoLugar);
                    $objetoDetalleTramo->setEstado("Eliminado");     
                    $emInfraestructura->persist($objDetalleTramo);
                    $emInfraestructura->flush();
                }
            }
            
            $objRuta = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($arrayParametros['intRuta']);
            
            if(is_object($objRuta))
            {
                //historial elemento
                $objHistorialElemento = new InfoHistorialElemento();
                $objHistorialElemento->setElementoId($objRuta);
                $objHistorialElemento->setEstadoElemento($objRuta->getEstado());
                $objHistorialElemento->setObservacion("Eliminación de Tramo T".$objRelacionElemento->getId().": <br> ".$strHistorial);
                $objHistorialElemento->setUsrCreacion($objSession->get('user'));
                $objHistorialElemento->setFeCreacion(new \DateTime('now'));
                $objHistorialElemento->setIpCreacion($objRequest->getClientIp());
                $emInfraestructura->persist($objHistorialElemento);
                $emInfraestructura->flush();    
                           
            }

            $emInfraestructura->commit();  
            $arrayResult['strStatus']        = 'OK';
            $arrayResult['strMessageStatus'] = 'Transacción Exitosa';                

        } 
        catch (\Exception $ex) 
        {   
            $arrayResult['strStatus']        = 'ERROR';
            $arrayResult['strMessageStatus'] = 'Error al eliminar la ruta';
           
             if($emInfraestructura->getConnection()->isTransactionActive())
            {
                $emInfraestructura->rollback();
            }
            
            $serviceUtil->insertError('Telcos+',
                                      'InfoElementoRutaController->ajaxEliminarTramoAction',
                                      $ex->getMessage(),
                                      $objSession->get('user'),
                                      $objRequest->getClientIp());
            
            $emInfraestructura->close();
        }
        
        $objResponse->setData($arrayResult);
        return $objResponse;
    }
}

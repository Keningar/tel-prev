<?php

namespace telconet\tecnicoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\schemaBundle\Entity\InfoEmpresaElemento;
use telconet\schemaBundle\Entity\InfoEmpresaElementoUbica;
use telconet\schemaBundle\Entity\InfoRelacionElemento;
use telconet\schemaBundle\Entity\InfoHistorialElemento;
use telconet\schemaBundle\Entity\InfoElemento;
use Symfony\Component\HttpFoundation\JsonResponse;

class ElementosDataCenterController extends Controller
{
    /**
     * 
     * Método que redirecciona al índice de la pantalla para editar los elementos de Data Center 
     * 
     * @author Allan Suárez C <arsuarez@telconet.ec>
     * @version 1.0 
     * @since 06-12-2018
     * 
     * @return type
     */
    public function indexAction()
    {        
        $arrayRolesPermitidos = array();        
        
        if (true === $this->get('security.context')->isGranted('ROLE_422-1'))
        {
            $arrayRolesPermitidos[] = 'ROLE_422-1'; //index pantalla de administración
        }
        
        if (true === $this->get('security.context')->isGranted('ROLE_422-6197'))
        {
            $arrayRolesPermitidos[] = 'ROLE_422-6197'; //permiso para modificar/agregar elementos virtuales
        }
        
        if (true === $this->get('security.context')->isGranted('ROLE_422-6198'))
        {
            $arrayRolesPermitidos[] = 'ROLE_422-6198'; //permiso para modificar puertos de nexus ( soporte )
        }
        
        if (true === $this->get('security.context')->isGranted('ROLE_422-6199'))
        {
            $arrayRolesPermitidos[] = 'ROLE_422-6199'; //permiso para modificar unidades de rack ( soporte )
        }
        
        $emSeguridad       = $this->getDoctrine()->getManager('telconet_seguridad');
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        
        $arrayParametros                      = array();
        $arrayParametros['strNombreElemento'] = 'Data Center';
        $arrayParametros['strEstadoElemento'] = 'Activo';
        $arrayParametros['intStart']          = '0';
        $arrayParametros['intLimit']          = '10';
        
        $arrayRespuesta    = $emInfraestructura->getRepository("schemaBundle:InfoElemento")->getElementos($arrayParametros);
        $objItemMenu       = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("422", "1");
        
        return $this->render('tecnicoBundle:ElementosDataCenter:index.html.twig', array(
            'item'                 => $objItemMenu,
            'arrayRolesPermitidos' => $arrayRolesPermitidos,
            'arrayDataCenters'     => json_encode($arrayRespuesta)
        ));
    }
    
    /**
     * 
     * Método que redirecciona a la pantalla de mantenimiento de las unidades de rack que deseen ser regularizadas
     * 
     * @author Karen Rodríguez <kyrodriguez@telconet.ec>
     * @version 1.1
     * @since 21-03-2019 
     * Se realiza llamada de la función genérica para obtener la información general del cuarto Ti
     * 
     * @author Allan Suárez C <arsuarez@telconet.ec>
     * @version 1.0 
     * @since 10-12-2018
     * 
     * @return type
     */
     public function elementoPasivoAction()
    {
        $objRequest        = $this->get('request');
        $objSession        = $objRequest->getSession();
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        
        $arrayParametros                      = array();
        $arrayParametros['strNombreElemento'] = 'Data Center';
        $arrayParametros['strEstadoElemento'] = 'Activo';
        $arrayParametros['intStart']          = '0';
        $arrayParametros['intLimit']          = '10';
        $arrayResultadoGye                    = array();
        $arrayResultadoUio                    = array();
        
        $arrayRespuesta    = $emInfraestructura->getRepository("schemaBundle:InfoElemento")->getElementos($arrayParametros);
        $arrayElementos    = array();
        foreach($arrayRespuesta['arrayRegistros'] as $array)
        {                        
            $strCanton                     = '';
            $objInfoEmpresaElementoUbicaDC =  $emInfraestructura->getRepository("schemaBundle:InfoEmpresaElementoUbica")
                                                                ->findOneBy(array('elementoId' => $array['id'],
                                                                                  'empresaCod' => $objSession->get('idEmpresa')));
            if(is_object($objInfoEmpresaElementoUbicaDC))
            {
                $objUbicacion = $objInfoEmpresaElementoUbicaDC->getUbicacionId();

                if(is_object($objUbicacion))
                {
                    $strCanton = $objUbicacion->getParroquiaId()->getCantonId()->getNombreCanton();
                }
            }
            
            $arrayElementos[] = array(
                'id'             => $array['id'],
                'nombreElemento' => $array['nombreElemento'],
                'canton'         => $strCanton
            );
        }

        //Obtener la posicion de los racks y devolverlos en forma de arreglo     
        $arrayResultadoGye = $this->getArrayInformacionInfraestructuraDC('GUAYAQUIL');
        $arrayResultadoUio = $this->getArrayInformacionInfraestructuraDC('QUITO');
        
        return $this->render('tecnicoBundle:ElementosDataCenter:elementosPasivos.html.twig', array(            
            'arrayDataCenters'     => json_encode($arrayElementos),
            'arrayPosicionesGye'   => json_encode($arrayResultadoGye['arrayPosiciones']),
            'arrayPosicionesUio'   => json_encode($arrayResultadoUio['arrayPosiciones']),
            'arrayFilasJaulasGye'  => json_encode($arrayResultadoGye['arrayFilasJaulas']),
            'arrayFilasJaulasUio'  => json_encode($arrayResultadoUio['arrayFilasJaulas'])
        ));
    }
    
    /**
     * 
     * Método que muestra de forma general el cuarto de TI para consulta de disponibilidad mediante petición ajax y la información de ubicación
     * del servicio en caso de haberse asignado la respectiva factibilidad.
     * 
     * @author Karen Rodríguez <kyrodriguez@telconet.ec>
     * @version 1.0
     * @since 21-03-2019 
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.1 03-07-2020 - Se agrega la llamada al web-service encargado de obtener la información de la ubicación en la
     *                           que se encuentra el servicio en el cuarto de TI.
     *
     * @return JsonResponse
     */
    public function ajaxGetInformacionCuartoTiAction()
    {
        $objRequest          = $this->get('request');
        $objSession          = $objRequest->getSession();
        $intIdServicio       = $objRequest->get('idServicio');
        $serviceGeneral      = $this->get('tecnico.InfoServicioTecnico');
        $emComercial         = $this->getDoctrine()->getManager('telconet');
        $serviceInfoSolucion = $this->get('comercial.InfoSolucion');
        $objServicio         = $emComercial->getRepository("schemaBundle:InfoServicio")->find($intIdServicio);

        $arrayResultado                        = array();
        $arrayResultado['tipoHousing']         = '';
        $arrayResultado['factibilidadHousing'] = '';

        if (is_object($objServicio))
        {
            //Obtener región del punto sobre el cual se realiza la consulta
            $strCiudad                = $serviceGeneral->getCiudadRelacionadaPorRegion($objServicio,$objSession->get('idEmpresa'));
            $arrayResultado           = $this->getArrayInformacionInfraestructuraDC($strCiudad);
            $arrayResultado['ciudad'] = $strCiudad;

            //Llamada al web-service que consulta la información del cuarto de TI.
            $arrayRequest    = array ('servicioId' => $objServicio->getId());
            $arrayResponseWs = $serviceInfoSolucion->WsPostDc(array('strUser'      =>  $objSession->get('user'),
                                                                    'strIp'        =>  $objRequest->getClientIp(),
                                                                    'strOpcion'    => 'soluciondc',
                                                                    'strEndPoint'  => 'listarCuartoTiServicio',
                                                                    'arrayRequest' =>  $arrayRequest));

            if ($arrayResponseWs['status'] && !empty($arrayResponseWs['data']))
            {
                $arrayResultado['tipoHousing']         = $arrayResponseWs['data'][0]['descripcionRecurso'];
                $arrayResultado['factibilidadHousing'] = json_encode($arrayResponseWs['data']);
            }
        }

        $objResponse = new JsonResponse();
        $objResponse->setData($arrayResultado);
        return $objResponse;
    }
    
    /**
     * 
     * Método privado encargado de obtener la información básica del cuarto de Ti para consulta/edición dada la ciudad donde se encuentre
     * el DataCenter
     * 
     * @author Allan Suárez C <arsuarez@telconet.ec>
     * @version 1.0 
     * @since 26-12-2018
     * 
     * @param type $strCiudad
     * @return type
     */
    private function getArrayInformacionInfraestructuraDC($strCiudad)
    {
        $arrayParametros                    = array();
        $objRequest                         = $this->get('request');
        $emInfraestructura                  = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emGeneral                          = $this->getDoctrine()->getManager('telconet_general');
        $arrayParametros['strNombreCanton'] = $strCiudad;
        $arrayPosiciones                    = $emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                                                ->getArrayInformacionFilaRacks($arrayParametros);
        
        $arrayFilasJaulas    = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                         ->get('FILAS RESERVADAS PARA JAULAS DC', 
                                            'TECNICO', 
                                            '',
                                            $strCiudad,
                                            '',
                                            '',
                                            '',
                                            '', 
                                            '', 
                                            $objRequest->getSession()->get('idEmpresa'));
        
        $arrayParametros['arrayPosiciones']  = $arrayPosiciones;
        $arrayParametros['arrayFilasJaulas'] = $arrayFilasJaulas;
        return $arrayParametros;
    }
    
    /**
     * 
     * Método que se encarga de devolver todos los tipos de elementos que están marcados para ser utilizdos en Data Center 
     * 
     * @author Allan Suárez C <arsuarez@telconet.ec>
     * @version 1.0 
     * @since 06-12-2018
     * 
     * @return JsonResponse
     */
    public function ajaxGetTiposElementosDataCenterAction()
    {
        $objRequest        = $this->get('request');
        $objSession        = $objRequest->getSession();
        $emSoporte         = $this->getDoctrine()->getManager('telconet_soporte');
        $arrayResultado    = array();
        
        $arrayParametrosResultado = $emSoporte->getRepository("schemaBundle:AdmiParametroDet")
                                              ->get('TIPO ELEMENTOS DE DATACENTER',
                                                       'TECNICO',
                                                       '',
                                                       '',
                                                       '','','','','',
                                                       $objSession->get('idEmpresa'),
                                                       null
                                                       );
        
        $arrayResultado[] = array('tipo' => 'Todos','orden' => 0, 'padre' => '', 'clasificacion' => '');
        
        if(!empty($arrayParametrosResultado))
        {
            foreach($arrayParametrosResultado as $array)
            {
                $arrayResultado[] = array('tipo'          => $array['valor1'],
                                          'orden'         => $array['valor2'],
                                          'padre'         => $array['valor3'],
                                          'clasificacion' => $array['descripcion']);
            }
        }
        
        $objResponse = new JsonResponse();
        
        $objResponse->setData($arrayResultado);
        
        return $objResponse;
    }
    
    /**
     * 
     * Método encargado de obtener todos los elementos para Data Center de acuerdo al filtro enviado por referencia
     * 
     * @author Allan Suárez C <arsuarez@telconet.ec>
     * @version 1.0 
     * @since 06-12-2018
     * 
     * @return JsonResponse
     */
    public function ajaxGetElementosDataCenterAction()
    {
        $objRequest        = $this->get('request');
        $objSession        = $objRequest->getSession();
        $strTipo           = $objRequest->get('tipo');
        $strNombre         = $objRequest->get('nombre');
        $intDataCenter     = $objRequest->get('dataCenter');        
        
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emGeneral         = $this->getDoctrine()->getManager('telconet_general');
        
        $arrayParametrosResultado = $emGeneral->getRepository("schemaBundle:AdmiParametroDet")
                                              ->get('TIPO ELEMENTOS DE DATACENTER',
                                                    'TECNICO',
                                                    '',
                                                    'VIRTUAL',
                                                    '','','','','',
                                                    $objSession->get('idEmpresa'),
                                                    null
                                                    );

        $arrayParametros                  = array();
        $arrayParametros['strTipo']       = $strTipo;
        $arrayParametros['strNombre']     = $strNombre;
        $arrayParametros['strDataCenter'] = 'Todos';        
        $arrayParametros['strCodEmpresa'] = $objSession->get('idEmpresa');
        
        if($intDataCenter != 'Todos')
        {
            $objInfoEmpresaElementoUbicaDC =  $emInfraestructura->getRepository("schemaBundle:InfoEmpresaElementoUbica")
                                                                ->findOneBy(array('elementoId' => $intDataCenter,
                                                                                  'empresaCod' => $objSession->get('idEmpresa')));
            if(is_object($objInfoEmpresaElementoUbicaDC))
            {
                $objUbicacion = $objInfoEmpresaElementoUbicaDC->getUbicacionId();

                if(is_object($objUbicacion))
                {
                    $arrayParametros['strDataCenter'] = $objUbicacion->getParroquiaId()->getCantonId()->getNombreCanton();
                }
            } 
        }
        
        $arrayParametros['arrayTiposDC']  = array();
        
        //Agregar al array de búsqueda los tipo de elementos clasificados como virtuales
        if(!empty($arrayParametrosResultado))
        {
            foreach($arrayParametrosResultado as $array)
            {
                $arrayParametros['arrayTiposDC'][] = $array['valor1'];
            }
        }
        
        $arrayElementosDc = $emInfraestructura->getRepository("schemaBundle:InfoElemento")->getArrayElementosDataCenter($arrayParametros);
        
        $objResponse      = new JsonResponse();
        
        $objResponse->setData($arrayElementosDc);
        
        return $objResponse;
    }
    
    /**
     * 
     * Método encargado de obtener la información relacionada a cada Nexus 5k ( detalle de nexus 2k )
     * 
     * @author Allan Suárez C <arsuarez@telconet.ec>
     * @version 1.0 
     * @since 06-12-2018
     * 
     * @return JsonResponse
     */
    public function ajaxGetInformacionPorSwitchAction()
    {
        $objRequest        = $this->get('request');
        $intIdElemento     = $objRequest->get('idElemento');
        $arrayJson         = array();
        $arraySwitches     = array();
        
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
                
        $arrayJson['interfaces'] = $emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                                     ->getArrayInformacionPuertosDataCenter($intIdElemento);
            
        $arraySwitchesRelacion   = $emInfraestructura->getRepository("schemaBundle:InfoRelacionElemento")->findByElementoIdA($intIdElemento);
         
        foreach($arraySwitchesRelacion as $objRelacion)
        {
            $objElementoSwitch = $emInfraestructura->getRepository("schemaBundle:InfoElemento")->find($objRelacion->getElementoIdB());
            
            if(is_object($objElementoSwitch))
            {
                $arraySwitches[] = array('idElemento'     => $objElementoSwitch->getId(),
                                         'nombreElemento' => $objElementoSwitch->getNombreElemento(),
                                         'estado'         => $objElementoSwitch->getEstado());
            }
            
            $arrayJson['switches'] = $arraySwitches;
        }
        
        $objResponse      = new JsonResponse();
        
        $objResponse->setData($arrayJson);
        
        return $objResponse;
    }
    
    /**
     * 
     * Método encargado de liberar u ocupar los puertos de los Nexus 5k o 2k para regularizaciones
     * 
     * @author Allan Suárez C <arsuarez@telconet.ec>
     * @version 1.0 
     * @since 06-12-2018
     * 
     * @return JsonResponse
     */
    public function ajaxLiberarSwitchDataCenterAction()
    {
        $objResponse       = new JsonResponse();
        $objRequest        = $this->get('request');
        $intIdInterface    = $objRequest->get('idInterface');
        $strAccion         = $objRequest->get('accion');        
        $serviceUtil       = $this->get('schema.Util');
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        
        $emInfraestructura->getConnection()->beginTransaction();
        
        try
        {
            $objInterfaceElemento = $emInfraestructura->getRepository("schemaBundle:InfoInterfaceElemento")->find($intIdInterface);
            
            if(is_object($objInterfaceElemento))
            {
                $objInterfaceElemento->setEstado($strAccion=='conectar'?'connected':'not connect');
                $emInfraestructura->persist($objInterfaceElemento);
                $emInfraestructura->flush();
                
                $emInfraestructura->commit();
                
                $objResponse->setContent('Actualización realizada correctamente');
            }
            else
            {
                $objResponse->setContent('No existe interface a ser actualizada, porfavor revisar');
            }
        } 
        catch (\Exception $ex) 
        {
            if ($emInfraestructura->getConnection()->isTransactionActive())
            {
                $emInfraestructura->rollback();
            }
            
            $emInfraestructura->close();            
            
            $serviceUtil->insertError('Telcos+', 
                                      'ajaxLiberarSwitchDataCenterAction', 
                                      $ex->getMessage(), 
                                      $objRequest->getSession()->get('user'), 
                                      $objRequest->getClientIp()
                                    );
            
            $objResponse->setContent('Error al realizar la actualización de puerto, notificar a Sistemas');            
        }
        
        return $objResponse;
    }
    
    /**
     * 
     * Método encargado de guardar la información de un nuevo elemento virtual para soluciones Hosting
     * 
     * @author Allan Suárez C <arsuarez@telconet.ec>
     * @version 1.0 
     * @since 06-12-2018
     * 
     * @return JsonResponse
     */
    public function ajaxGuardarElementoVirtualDataCenterAction()
    {
        $objResponse       = new JsonResponse();
        $objRequest        = $this->get('request');
        $objSession        = $objRequest->getSession();
        $intIdElementoPadre= $objRequest->get('idElementoPadre');
        $strNombreElemento = $objRequest->get('nombreElemento');
        $strTipoElemento   = $objRequest->get('tipoElemento');
        $intDataCenter     = $objRequest->get('dataCenter');       
        $serviceUtil       = $this->get('schema.Util');
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emGeneral         = $this->getDoctrine()->getManager('telconet_general');
        $objUbicacion      = null;
        $objModeloElemento = null;
        $arrayRespuesta    = array();
        
        $emInfraestructura->getConnection()->beginTransaction();
        
        try
        {
            //Obtener la Ubicación de DataCenter para replicar
            $objInfoEmpresaElementoUbicaDC = $emInfraestructura->getRepository("schemaBundle:InfoEmpresaElementoUbica")
                                                               ->findOneBy(array('elementoId' => $intDataCenter,
                                                                                 'empresaCod' => $objSession->get('idEmpresa')));
            if(is_object($objInfoEmpresaElementoUbicaDC))
            {
                $objUbicacion = $objInfoEmpresaElementoUbicaDC->getUbicacionId();
                
                if(!is_object($objUbicacion))
                {
                    $arrayRespuesta['status']  = 'ERROR';
                    $arrayRespuesta['mensaje'] = 'No existe información de Ubicación del Nodo Data Center, notificar a Sistemas';                    
                }
            }
            
            //Obtener el modelo del tipo seleccionado
            $objAdmiTipoElemento = $emInfraestructura->getRepository("schemaBundle:AdmiTipoElemento")
                                                     ->findOneByNombreTipoElemento($strTipoElemento);
            
            if(is_object($objAdmiTipoElemento))
            {
                $objModeloElemento = $emInfraestructura->getRepository("schemaBundle:AdmiModeloElemento")
                                                       ->findOneByTipoElementoId($objAdmiTipoElemento->getId());
                
                if(!is_object($objModeloElemento))
                {
                    $arrayRespuesta['status']  = 'ERROR';
                    $arrayRespuesta['mensaje'] = 'No existe información del modelo elemento a ser guardado, notificar a Sistemas';                    
                }
            }
            
            //Se crea el elemento
            $objElemento = new InfoElemento();
            $objElemento->setNombreElemento($strNombreElemento);
            $objElemento->setDescripcionElemento($strTipoElemento.' DC-');
            $objElemento->setModeloElementoId($objModeloElemento);
            $objElemento->setUsrResponsable($objSession->get('user'));
            $objElemento->setUsrCreacion($objSession->get('user'));
            $objElemento->setFeCreacion(new \DateTime('now'));
            $objElemento->setIpCreacion($objRequest->getClientIp());
            $objElemento->setEstado('Activo');
            $emInfraestructura->persist($objElemento);
            $emInfraestructura->flush();
            
            $objEmpresaElementoUbica = new InfoEmpresaElementoUbica();
            $objEmpresaElementoUbica->setEmpresaCod($objSession->get('idEmpresa'));
            $objEmpresaElementoUbica->setElementoId($objElemento);
            $objEmpresaElementoUbica->setUbicacionId($objUbicacion);
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
            
            $objHistorialElemento = new InfoHistorialElemento();
            $objHistorialElemento->setElementoId($objElemento);
            $objHistorialElemento->setEstadoElemento('Activo');
            $objHistorialElemento->setObservacion("Se nuevo ".$strTipoElemento);
            $objHistorialElemento->setUsrCreacion($objSession->get('user'));
            $objHistorialElemento->setFeCreacion(new \DateTime('now'));
            $objHistorialElemento->setIpCreacion($objRequest->getClientIp());
            $emInfraestructura->persist($objHistorialElemento);
            $emInfraestructura->flush();
            
            //Verificar si es un tipo que tenga dependencia  ( VCENTER / CLUSTER )
            $arrayParametrosResultado =   $emGeneral->getRepository("schemaBundle:AdmiParametroDet")
                                                    ->getOne('TIPO ELEMENTOS DE DATACENTER',
                                                          'TECNICO',
                                                          '',
                                                          'VIRTUAL',
                                                          $strTipoElemento,'','','','',
                                                          $objSession->get('idEmpresa'),
                                                          null
                                                          );
            
            if(!empty($arrayParametrosResultado) && $arrayParametrosResultado['valor2'] != 0 && !empty($intIdElementoPadre))
            {
                $objRelacionElemento = new InfoRelacionElemento();
                $objRelacionElemento->setElementoIdA($intIdElementoPadre);
                $objRelacionElemento->setElementoIdB($objElemento->getId());
                $objRelacionElemento->setTipoRelacion("CONTIENE");
                $objRelacionElemento->setObservacion($arrayParametrosResultado['valor3']." CONTIENE ".$strTipoElemento);
                $objRelacionElemento->setEstado("Activo");
                $objRelacionElemento->setUsrCreacion($objSession->get('user'));
                $objRelacionElemento->setFeCreacion(new \DateTime('now'));
                $objRelacionElemento->setIpCreacion($objRequest->getClientIp());
                $emInfraestructura->persist($objRelacionElemento);
                $emInfraestructura->flush();
            }            
            
            $emInfraestructura->commit();
            
            $arrayRespuesta['status']  = 'OK';
            $arrayRespuesta['mensaje'] = 'Elemento ingresado correctamente';
        } 
        catch (\Exception $ex) 
        {
            if ($emInfraestructura->getConnection()->isTransactionActive())
            {
                $emInfraestructura->rollback();
            }
            
            $emInfraestructura->close();            
            
            $serviceUtil->insertError('Telcos+', 
                                      'ajaxGuardarElementoVirtualDataCenterAction', 
                                      $ex->getMessage(), 
                                      $objRequest->getSession()->get('user'), 
                                      $objRequest->getClientIp()
                                    );
            
            $arrayRespuesta['status']  = 'ERROR';
            $arrayRespuesta['mensaje'] = 'Error al guardar la información del nuevo elemento, notificar a Sistemas';            
        }
        
        $objResponse->setData($arrayRespuesta);
        
        return $objResponse;
    }
    
    /**
     * 
     * Método encargado de realizar la liberación/ocupación de los espacios en las unidades de rack
     * 
     * @author Allan Suárez C <arsuarez@telconet.ec>
     * @version 1.0 
     * @since 11-12-2018
     * 
     * @return JsonResponse
     */
    public function ajaxEditarElementoPasivoDataCenterAction()
    {
        $objResponse       = new JsonResponse();
        $objRequest        = $this->get('request');
        $objSession        = $objRequest->getSession();
        $serviceUtil       = $this->get('schema.Util');
        $objJsonEdicion    = $objRequest->get('jsonEdicion');
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        $arrayConectados   = array();
        
        $emInfraestructura->getConnection()->beginTransaction();
        
        try
        {
            $arrayJsonEdicion = json_decode($objJsonEdicion);
            
            //Recorrer el array para identificar cuantas unidades de racks se agregan y cuantas son eliminadas por regularizacion
            foreach($arrayJsonEdicion as $objJson)
            {
                $objElementoUdRack = $emInfraestructura->getRepository("schemaBundle:InfoElemento")->find($objJson->id);
                
                if(is_object($objElementoUdRack))
                {
                    if($objJson->estadoN == 'seleccionado')
                    {
                        //Se creará la nueva relación para ocupar la unidad de rack
                        $objElementoDc = $emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                                           ->findOneByNombreElemento('Equipos DC (TN/Clientes)');

                        if(is_object($objElementoDc))
                        {
                            $objRelacionElemento = new InfoRelacionElemento();
                            $objRelacionElemento->setElementoIdA($objElementoUdRack->getId());
                            $objRelacionElemento->setElementoIdB($objElementoDc->getId());
                            $objRelacionElemento->setTipoRelacion("CONTIENE");
                            $objRelacionElemento->setObservacion('Rack contiene Elemento DC');
                            $objRelacionElemento->setEstado("Activo");
                            $objRelacionElemento->setUsrCreacion($objSession->get('user'));
                            $objRelacionElemento->setFeCreacion(new \DateTime('now'));
                            $objRelacionElemento->setIpCreacion($objRequest->getClientIp());
                            $emInfraestructura->persist($objRelacionElemento);
                            $emInfraestructura->flush();
                        }
                    }
                    else//desocupado ( se libera la unidad de rack )
                    {                        
                        //Verificar que la unidad de rack no este siendo usada
                        $arrayConectados = $emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                                             ->getArrayServicioPorUnidadDeRack($objElementoUdRack->getId());
                        
                        if(empty($arrayConectados))                        
                        {
                            $objRelacionElemento = $emInfraestructura->getRepository("schemaBundle:InfoRelacionElemento")
                                                                     ->findOneBy(array('elementoIdA' => $objElementoUdRack->getId(),
                                                                                       'estado'      => 'Activo'));

                           if(is_object($objRelacionElemento))
                           {
                               $objRelacionElemento->setEstado('Eliminado');
                               $emInfraestructura->persist($objRelacionElemento);
                               $emInfraestructura->flush();
                           }
                        }                        
                    }
                }
            }
            
            $emInfraestructura->commit();
            
            $arrayRespuesta['status']          = 'OK';
            $arrayRespuesta['mensaje']         = 'Proceso culminado correctamente'; 
            $arrayRespuesta['arrayConectados'] = json_encode($arrayConectados);
        } 
        catch (\Exception $ex)
        {
            if ($emInfraestructura->getConnection()->isTransactionActive())
            {
                $emInfraestructura->rollback();
            }
            
            $emInfraestructura->close();            
            
            $serviceUtil->insertError('Telcos+', 
                                      'ajaxEditarElementoPasivoDataCenterAction', 
                                      $ex->getMessage(), 
                                      $objSession->get('user'), 
                                      $objRequest->getClientIp()
                                    );
            
            $arrayRespuesta['status']  = 'ERROR';
            $arrayRespuesta['mensaje'] = 'Error al editar la información de Racks, notificar a Sistemas'; 
        }
        
        $objResponse->setData($arrayRespuesta);
        
        return $objResponse;
    }
}

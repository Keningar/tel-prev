<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\tecnicoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\InfoElemento;
use telconet\schemaBundle\Entity\AdmiModeloElemento;
use telconet\schemaBundle\Entity\InfoInterfaceElemento;
use telconet\schemaBundle\Entity\InfoEnlace;
use telconet\schemaBundle\Form\InfoEnlaceType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Response;

class InfoEnlaceController extends Controller
{ 
    public function enlaceAction(){
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');
        
//        $entities = $this->getDoctrine()
//            ->getManager("telconet_infraestructura")
//            ->getRepository('schemaBundle:InfoEnlace')
//            ->generarJsonEnlaces('','','','','Activo','0','1000',$em);
        
        return $this->render('tecnicoBundle:enlace:index.html.twig', array(
        ));
    }
    
    public function getEncontradosAction(){
        ini_set('max_execution_time', 400000);
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');
        $session = $this->get('session');
        $peticion = $this->get('request');
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        $idEmpresa = $session->get('idEmpresa');
        $interfaceElementoIniId = $peticion->query->get('interfaceElementoIniId');
        $interfaceElementoFinId = $peticion->query->get('interfaceElementoFinId');
        $elementoNombreIni = $peticion->query->get('elementoIniNombre');
        $elementoNombreFin = $peticion->query->get('elementoFinNombre');
        
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:InfoEnlace')
            ->generarJsonEnlaces($elementoNombreIni, $elementoNombreFin, $interfaceElementoIniId,$interfaceElementoFinId,'','','Activo',$idEmpresa,$start,$limit,$em);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    /**
     * showAction
     * funcion que permite mostrar el enlace creado con su detalle
     * 
     * @author  Allan Suarez <arsuarez@telconet.ec>
     * @version 1.5 04-03-2015 - Se agrega buffer e hilo al enlace en caso de ser escogido
     *      
     * @version 1.0 Version Inicial
     * @return view
     */
    public function showAction($id)
    {
        $peticion = $this->get('request');

        $em = $this->getDoctrine()->getManager("telconet_infraestructura");

        if(null == $enlace = $em->find('schemaBundle:InfoEnlace', $id))
        {
            throw new NotFoundHttpException('No existe el Enlace que se quiere mostrar');
        }
        else
        {
            $interfaceInicio = $em->getRepository('schemaBundle:InfoInterfaceElemento')->find($enlace->getInterfaceElementoIniId());
            $elementoInicio  = $interfaceInicio->getElementoId();
            $interfaceFin    = $em->getRepository('schemaBundle:InfoInterfaceElemento')->find($enlace->getInterfaceElementoFinId());
            $elementoFin     = $interfaceFin->getElementoId();

            $objTipoMedio = $em->getRepository('schemaBundle:AdmiTipoMedio')->find($enlace->getTipoMedioId());

            $tipoMedio = 'N/A';

            if($objTipoMedio)
            {
                $tipoMedio = $objTipoMedio->getNombreTipoMedio();
            }

            $buffer = 'N/A';
            $hilo   = 'N/A';
            $claseTipoMedio = 'N/A';

            if($enlace->getBufferHiloId())
            {
                $bufferHilo = $em->getRepository("schemaBundle:InfoBufferHilo")->find($enlace->getBufferHiloId()->getId());

                if($bufferHilo)
                {
                    $objBuffer = $em->getRepository("schemaBundle:AdmiBuffer")->find($bufferHilo->getBufferId()->getId());
                    $objHilo = $em->getRepository("schemaBundle:AdmiHilo")->find($bufferHilo->getHiloId()->getId());

                    if($objBuffer)
                    {
                        $buffer = $objBuffer->getNumeroBuffer() . ' - ' . $objBuffer->getColorBuffer();
                    }
                    if($objHilo)
                    {
                        $hilo = $objHilo->getNumeroHilo() . ' - ' . $objHilo->getColorHilo();
                        $objClaseTipoMedio = $em->getRepository("schemaBundle:AdmiClaseTipoMedio")->find($objHilo->getClaseTipoMedioId());
                        if($objClaseTipoMedio)
                        {
                            $claseTipoMedio = $objClaseTipoMedio->getNombreClaseTipoMedio();
                        }
                    }
                }
            }
        }

        return $this->render('tecnicoBundle:InfoEnlace:show.html.twig', array(
                'elementoInicio'  => $elementoInicio,
                'elementoFin'     => $elementoFin,
                'interfaceInicio' => $interfaceInicio,
                'interfaceFin'    => $interfaceFin,
                'enlace'          => $enlace,
                'buffer'          => $buffer,
                'hilo'            => $hilo,
                'tipoMedio'       => $tipoMedio,
                'claseTipoMedio'  => $claseTipoMedio,
                'flag'            => $peticion->get('flag')
        ));
    }

    public function newAction(){
        $entity = new InfoEnlace();
        $form   = $this->createForm(new InfoEnlaceType(), $entity);

        return $this->render('tecnicoBundle:InfoEnlace:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }
    
    /**
     * createAction
     * funcion que permite crear un nuevo enlace
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 Version Inicial
     * 
     * @author  Allan Suarez <arsuarez@telconet.ec>
     * @version 1.2 04-03-2015 - Se agrega buffer e hilo al enlace en caso de ser escogido
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.3 02-05-2016 - Se agrega que busque el buffer_hilo por empresa
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.4 01-07-2016 - Se agregan validaciones en creacion de enlace
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.5 05-07-2016 - Se corrige inicio de transaccion de creacion de enlace
     * 
     * @return view
     */
    public function createAction()
    {
        $request = $this->get('request');
        $session = $request->getSession();
        $peticion = $this->get('request');
        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        
        $enlace = new InfoEnlace();
        $form = $this->createForm(new InfoEnlaceType(), $enlace);

        $parametros = $request->request->get('telconet_schemabundle_infoenlacetype');

        $intEmpresaCod          = $session->get('idEmpresa');
        $interfaceElementoIdA   = $parametros['interfaceElementoIdA'];
        $interfaceElementoIdB   = $parametros['interfaceElementoIdB'];
        $interfaceElementoA     = $em->find('schemaBundle:InfoInterfaceElemento', $interfaceElementoIdA);
        $interfaceElementoB     = $em->find('schemaBundle:InfoInterfaceElemento', $interfaceElementoIdB);

        $capacidadInput = $parametros['capacidadInput'];
        $unidadMedidaInput = $parametros['unidadMedidaInput'];

        $capacidadOuput = $parametros['capacidadOutput'];
        $unidadMedidaOuput = $parametros['unidadMedidaOutput'];

        $tipoEnlace = $parametros['tipoEnlace'];
        $tipoMedioId = $parametros['tipoMedioId'];
        $tipoMedio = $em->find('schemaBundle:AdmiTipoMedio', $tipoMedioId);

        $capacidadIniFin = $parametros['capacidadIniFin'];
        $unidadMedidaUp = $parametros['unidadMedidaUp'];
        $capacidadFinIni = $parametros['capacidadFinIni'];
        $unidadMedidaDown = $parametros['unidadMedidaDown'];

        $buffer = $parametros['bufferId'];
        $hilo   = $parametros['hiloId'];

        $em->getConnection()->beginTransaction();
        try
        {
            $objEnlace = $em->getRepository('schemaBundle:InfoEnlace')
                            ->findOneBy( array("interfaceElementoIniId" => $interfaceElementoIdA,
                                               "interfaceElementoFinId" => $interfaceElementoIdB,
                                               "estado"                 => 'Activo' ));
            if ($objEnlace)
            {
                $this->get('session')->getFlashBag()->add('notice', 'Enlace ya existente, favor notificar a Sistemas.');
                return $this->redirect($this->generateUrl('enlace_elemento_new'));   
            }
            
            if(($buffer != 0 || $buffer != 'Seleccione') && ($hilo != 0 || $hilo != 'Seleccione'))
            {
                $bufferHilo = $em->getRepository('schemaBundle:InfoBufferHilo')->findOneBy(array(   'bufferId'  => $buffer, 
                                                                                                    'hiloId'    => $hilo, 
                                                                                                    'empresaCod'=> $intEmpresaCod));
                if($bufferHilo)
                {
                    $enlace->setBufferId($bufferHilo);
                }
            }

            $enlace->setInterfaceElementoIniId($interfaceElementoA);
            $enlace->setInterfaceElementoFinId($interfaceElementoB);
            $enlace->setTipoMedioId($tipoMedio);
            $enlace->setTipoEnlace($tipoEnlace);

            $enlace->setCapacidadInput($capacidadInput);
            $enlace->setCapacidadOutput($capacidadOuput);
            $enlace->setUnidadMedidaInput($unidadMedidaInput);
            $enlace->setUnidadMedidaOutput($unidadMedidaOuput);

            $enlace->setCapacidadIniFin($capacidadIniFin);
            $enlace->setCapacidadFinIni($capacidadFinIni);
            $enlace->setUnidadMedidaUp($unidadMedidaUp);
            $enlace->setUnidadMedidaDown($unidadMedidaDown);
            $enlace->setEstado("Activo");
            $enlace->setUsrCreacion($session->get('user'));
            $enlace->setFeCreacion(new \DateTime('now'));
            $enlace->setIpCreacion($peticion->getClientIp());

            $form->handleRequest($request);

            $interfaceElementoA->setEstado("connected");
            $em->persist($interfaceElementoA);
            $em->flush();

            $interfaceElementoB->setEstado("connected");
            $em->persist($interfaceElementoB);
            $em->flush();

            $em->persist($enlace);
            $em->flush();

            $em->getConnection()->commit();

            return $this->redirect($this->generateUrl('enlace_elemento_show', array('id' => $enlace->getId())));
        }
        catch (\Exception $e) 
        {
            $em->getConnection()->rollback();
            $em->getConnection()->close();
            $mensajeError = "Error: ".$e->getMessage();
            error_log($mensajeError);
        }
        $this->get('session')->getFlashBag()->add('notice', 'Existieron problemas al procesar la transaccion, favor notificar a Sistemas.');
        return $this->redirect($this->generateUrl('enlace_elemento_new'));   
    }

    public function deleteAjaxAction(){
        ini_set('max_execution_time', 3000000);
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $request = $this->get('request');
        $session  = $request->getSession();
        $peticion = $this->get('request');
        
        $parametro = $peticion->get('param');
        
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");
        $emC = $this->getDoctrine()->getManager("telconet");
        $em->getConnection()->beginTransaction();
        
        $array_valor = explode("|",$parametro);
        
        foreach($array_valor as $id):
            if (null == $entity = $em->find('schemaBundle:InfoEnlace', $id)) {
                $respuesta->setContent("No existe la entidad");
            }
            else{
                $entity->setEstado("Eliminado"); 
                $em->persist($entity);
                $em->flush();
                
                $entityInterfaceElementoA = $em->find('schemaBundle:InfoInterfaceElemento', $entity->getInterfaceElementoIniId());
                $entityInterfaceElementoA->setEstado('not connect');
                $em->persist($entityInterfaceElementoA);
                $em->flush();
                $entityInterfaceElementoB = $em->find('schemaBundle:InfoInterfaceElemento', $entity->getInterfaceElementoFinId());
                $entityInterfaceElementoB->setEstado('not connect');
                $em->persist($entityInterfaceElementoB);
                $em->flush();
                $respuesta->setContent("OK");
            }            
        endforeach;
        
        $em->getConnection()->commit();
        
        return $respuesta;
    }

 public function buscarElementoPorTipoElementoAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $peticion = $this->get('request');
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emComercial = $this->get('doctrine')->getManager('telconet');
        $tipoElementoId = $peticion->get('tipoElemento');
        $tipoElemento = $emInfraestructura->find('schemaBundle:AdmiTipoElemento', $tipoElementoId);
        $nombreElemento = $peticion->query->get('query');
        $start = $peticion->get('start');
        $limit = $peticion->get('limit');
        
        $session = $this->get('session');
        $empresa = $session->get('idEmpresa');
        $arrayParametros = array(   'idServicio'            => '',
                                    'nombreElemento'        => $nombreElemento,
                                    'nombreModeloElemento'  => '',
                                    'tipoElemento'          => $tipoElemento->getNombreTipoElemento(),
                                    'empresa'               => $empresa,
                                    'estado'                => "Todos",
                                    'start'                 => $start,
                                    'limit'                 => $limit,
                                    'emInfraestructura'     => $emInfraestructura,
                                    'emComercial'           => $emComercial,
                                    'validaCnr'             => '');
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:InfoServicioTecnico')
            ->generarJsonElementosPorTipo($arrayParametros);
        $respuesta->setContent($objJson);
        
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }

     /**
     * buscarInterfacesPorElementoAction
     * funcion que permite encontrar las interfaces por el nombre de un elemento
     * 
     * @author  Anthony Santillan <asantillany@telconet.ec>
     *      
     * @version 1.0 15-02-2023 Version Inicial
     * @return view
     */

    public function buscarInterfacesPorElementoAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $idElemento = $peticion->get('idElemento');
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:InfoElemento')
            ->generarJsonInterfacesPorElemento($idElemento,"Todos",$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    /**
     * getEnlaceAction
     * funcion que permite mostrar el enlace creado con su detalle
     * 
     * @author  Anthony Santillan <asantillany@telconet.ec>
     *      
     * @version 1.0 15-02-2023 Version Inicial
     * @return view
     */
    public function getEnlaceAction()
    {
        $emInfraestructura = $this->getDoctrine()->getManager("telconet_infraestructura");
        $emComercial = $this->get('doctrine')->getManager('telconet');

        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');

        $objPeticion = $this->get('request');
        $intInterfaceElementoIniId = $objPeticion->query->get('interfaceElementoIniId');
        $intInterfaceElementoFinId = $objPeticion->query->get('interfaceElementoFinId');
        $intItrIni              = $objPeticion->query->get('itrIni');
        $intItrFin              = $objPeticion->query->get('itrFin');

        $arrayRespuestas =array();

        if($intInterfaceElementoIniId != '' && $intItrFin != '')
        {
            $objEnlace = $emInfraestructura->getRepository('schemaBundle:InfoEnlace')
            ->findOneBy( array('interfaceElementoIniId' => $intInterfaceElementoIniId, 
            'estado' => 'Activo'));

            $objInfoEnlaceServicio =  $emInfraestructura->getRepository("schemaBundle:InfoEnlaceServicioBackbone")
                    ->findOneBy(array('enlaceId' => $objEnlace->getId()));
                    
            if(is_object($objInfoEnlaceServicio))
            {
                $strTipoRutaPrincipal = $objInfoEnlaceServicio->getTipoRuta();
            }

            $intOrden = 1;
            
            while($objEnlace !== null)
            {
                $objInterfaceInicio   = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                ->find($objEnlace->getInterfaceElementoIniId());
                $intElementoInicio    = $objInterfaceInicio->getElementoId()->getId();

                $objElementoInicio = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                    ->findOneBy( array('id' => $intElementoInicio, 'estado' => 'Activo') );

                if(is_object($objElementoInicio))
                {
                    $strNombreElementoInicio = $objElementoInicio->getNombreElemento();
                
                    $objAdmiModeloElemento   = $emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                                ->findOneBy( array('id' => $objElementoInicio->getModeloElementoId(), 'estado' => 'Activo') );
                    
                    if(is_object($objAdmiModeloElemento))
                    {
                        $intIdTipoElemento = $objAdmiModeloElemento->getTipoElementoId();
                    }

                    $objAdmiTipoElemento   = $emInfraestructura->getRepository('schemaBundle:AdmiTipoElemento')
                                                ->findOneBy( array('id' => $intIdTipoElemento, 'estado' => 'Activo') );
                    
                    $strTipoElementoInicio = '';
                    if(is_object($objAdmiTipoElemento))
                    {
                        $strTipoElementoInicio    = $objAdmiTipoElemento->getNombreTipoElemento();
                    }

                }

                $objInterfaceFin      = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                ->find($objEnlace->getInterfaceElementoFinId());

                $intElementoFin       = $objInterfaceFin->getElementoId()->getId();

                $objElementoFinal = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                    ->findOneBy( array('id' => $intElementoFin, 'estado' => 'Activo') );

                if(is_object($objElementoFinal))
                {                        
                    $strNombreElementoFinal = $objElementoFinal->getNombreElemento();

                    $objAdmiModeloElemento   = $emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                                ->find($objElementoFinal->getModeloElementoId());
                    
                    if(is_object($objAdmiModeloElemento))
                    {
                        $intIdTipoElemento = $objAdmiModeloElemento->getTipoElementoId();
                    }

                    $objAdmiTipoElemento   = $emInfraestructura->getRepository('schemaBundle:AdmiTipoElemento')
                                                ->findOneBy( array('id' => $intIdTipoElemento, 'estado' => 'Activo') );
                    
                    $strTipoElementoFin = '';
                    if(is_object($objAdmiTipoElemento))
                    {
                        $strTipoElementoFin    = $objAdmiTipoElemento->getNombreTipoElemento();
                    }
                }

                // obtener login a travez de la ip 
                $strLogin = 'LIBRE';

                $objInfoEnlaceServicio =  $emInfraestructura->getRepository("schemaBundle:InfoEnlaceServicioBackbone")
                                                    ->findOneBy(array('enlaceId' => $objEnlace->getId()));
                
                $strJurisdiccion = '';
                if(is_object($objInfoEnlaceServicio))
                {
                    $strLogin = $objInfoEnlaceServicio->getLoginAux();
                   
                    $objInfoServicio   = $emInfraestructura->getRepository("schemaBundle:InfoServicio")
                    ->findOneBy(array('loginAux' => $strLogin));
            
                    $objInfoPunto    = $emInfraestructura->getRepository("schemaBundle:InfoPunto")
                                        ->find($objInfoServicio->getPuntoId());

                    $intPuntoCobertura = $objInfoPunto->getPuntoCoberturaId()->getId();
                    $objJurisdiccion = $emInfraestructura->getRepository('schemaBundle:AdmiJurisdiccion')
                                                ->find($intPuntoCobertura);

                    if(is_object($objJurisdiccion))
                    {
                        $strJurisdiccion = $objJurisdiccion->getNombreJurisdiccion();
                    }
                }
                

                $objTipoMedio = $emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')
                ->find($objEnlace->getTipoMedioId());

                $strTipoMedio = '';

                if($objTipoMedio)
                {
                    $strTipoMedio = $objTipoMedio->getNombreTipoMedio();
                }

                $strBuffer = '';
                $strHilo   = '';
                $strClaseTipoMedio = '';

                if($objEnlace->getBufferHiloId())
                {
                    $objBufferHilo = $emInfraestructura->getRepository("schemaBundle:InfoBufferHilo")
                    ->find($objEnlace->getBufferHiloId()->getId());

                    if($objBufferHilo)
                    {
                        $objBuffer = $emInfraestructura->getRepository("schemaBundle:AdmiBuffer")
                        ->find($objBufferHilo->getBufferId()->getId());
                        $objHilo = $emInfraestructura->getRepository("schemaBundle:AdmiHilo")
                        ->find($objBufferHilo->getHiloId()->getId());

                        if($objBuffer)
                        {
                            $strBuffer = $objBuffer->getNumeroBuffer() . ' - ' . $objBuffer->getColorBuffer();
                        }
                        if($objHilo)
                        {
                            $strHilo = $objHilo->getNumeroHilo();
                            $strColorHilo = $objHilo->getColorHilo();
                            $objClaseTipoMedio = $emInfraestructura->getRepository("schemaBundle:AdmiClaseTipoMedio")
                            ->find($objHilo->getClaseTipoMedioId());
                            if($objClaseTipoMedio)
                            {
                                $strClaseTipoMedio = $objClaseTipoMedio->getNombreClaseTipoMedio();
                            }
                        }
                    }
                }

                $objInfoEnlaceDerivacion = $emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                ->findBy( array('interfaceElementoIniId' => $objInterfaceInicio->getId(), 
                                'estado' => 'Activo'));
            
                $strTipoRuta = 'LIBRE';
                if(count($objInfoEnlaceDerivacion) > 1)
                {
                    $strTipoRuta = 'DERIVACION';
                }

                
                $arrayRespuesta =array(
                                        'orden'           => $intOrden,
                                        'idEnlace'        => $objEnlace->getId(),
                                        'elementoInicio'  => $strNombreElementoInicio,
                                        'elementoFin'     => $strNombreElementoFinal,
                                        'interfaceIniId'  => $objInterfaceInicio->getId(),
                                        'interfaceInicio' => $objInterfaceInicio->getNombreInterfaceElemento(),
                                        'interfaceFin'    => $objInterfaceFin->getNombreInterfaceElemento(),
                                        'hilo'            => $strHilo,
                                        'color'           => $strColorHilo,
                                        'login'           => $strLogin,
                                        'jurisdiccion'    => $strJurisdiccion,
                                        'tipoElementoIni' => $strTipoElementoInicio,
                                        'tipoElementoFin' => $strTipoElementoFin,
                                        'opciones'        => array('tipRuta' => $strTipoRuta,
                                                                    'itrIni' => $objInterfaceInicio->getId())
                                        );
                if($strNombreElementoInicio != $strNombreElementoFinal)
                {
                    array_push($arrayRespuestas,$arrayRespuesta);
                }
                
                if($intItrFin == $objInterfaceFin->getId())
                {
                    break;
                }

                $objEnlaces = $emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                ->findBy( array('interfaceElementoIniId' => $objEnlace->getInterfaceElementoFinId(),
                'estado' => 'Activo'));

                if(empty($objEnlaces))
                {
                    break;
                }
                
                foreach($objEnlaces as $objEnlaceServicio)
                {
                    $objInfoEnlaceServicio =  $emInfraestructura->getRepository("schemaBundle:InfoEnlaceServicioBackbone")
                    ->findOneBy(array('enlaceId' => $objEnlaceServicio->getId()));
                    
                    if(is_object($objInfoEnlaceServicio))
                    {
                        $strTipoRuta = $objInfoEnlaceServicio->getTipoRuta();
                    }
                    if($strTipoRuta == $strTipoRutaPrincipal)
                    {
                        $objEnlace = $emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                        ->find($objEnlaceServicio->getId() );
                    }  
                }

                $intOrden++;
            }
        }
        if($intInterfaceElementoFinId != '' && $intItrIni != '')
        {
            $objEnlace = $emInfraestructura->getRepository('schemaBundle:InfoEnlace')
            ->findOneBy( array('interfaceElementoFinId' => $intInterfaceElementoFinId, 
            'estado' => 'Activo'));

            $objInfoEnlaceServicio =  $emInfraestructura->getRepository("schemaBundle:InfoEnlaceServicioBackbone")
                    ->findOneBy(array('enlaceId' => $objEnlace->getId()));
                    
            if(is_object($objInfoEnlaceServicio))
            {
                $strTipoRutaPrincipal = $objInfoEnlaceServicio->getTipoRuta();
            }

            $intOrden = 1;
            
            while($objEnlace !== null)
            {

                $objInterfaceInicio   = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                ->find($objEnlace->getInterfaceElementoIniId());
                $intElementoInicio    = $objInterfaceInicio->getElementoId()->getId();

                $objElementoInicio = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                    ->findOneBy( array('id' => $intElementoInicio, 'estado' => 'Activo') );

                if(is_object($objElementoInicio))
                {
                    $strNombreElementoInicio = $objElementoInicio->getNombreElemento();
                
                    $objAdmiModeloElemento   = $emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                                ->findOneBy( array('id' => $objElementoInicio->getModeloElementoId(), 'estado' => 'Activo') );
                    
                    if(is_object($objAdmiModeloElemento))
                    {
                        $intIdTipoElemento = $objAdmiModeloElemento->getTipoElementoId();
                    }

                    $objAdmiTipoElemento   = $emInfraestructura->getRepository('schemaBundle:AdmiTipoElemento')
                                                ->findOneBy( array('id' => $intIdTipoElemento, 'estado' => 'Activo') );
                    
                    $strTipoElementoInicio = '';
                    if(is_object($objAdmiTipoElemento))
                    {
                        $strTipoElementoInicio    = $objAdmiTipoElemento->getNombreTipoElemento();
                    }

                }

                $objInterfaceFin      = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                ->find($objEnlace->getInterfaceElementoFinId());

                $intElementoFin       = $objInterfaceFin->getElementoId()->getId();

                $objElementoFinal = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                    ->findOneBy( array('id' => $intElementoFin, 'estado' => 'Activo') );

                if(is_object($objElementoFinal))
                {                        
                    $strNombreElementoFinal = $objElementoFinal->getNombreElemento();

                    $objAdmiModeloElemento   = $emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                                ->find($objElementoFinal->getModeloElementoId());
                    
                    if(is_object($objAdmiModeloElemento))
                    {
                        $intIdTipoElemento = $objAdmiModeloElemento->getTipoElementoId();
                    }

                    $objAdmiTipoElemento   = $emInfraestructura->getRepository('schemaBundle:AdmiTipoElemento')
                                                ->findOneBy( array('id' => $intIdTipoElemento, 'estado' => 'Activo') );
                    
                    $strTipoElementoFin = '';
                    if(is_object($objAdmiTipoElemento))
                    {
                        $strTipoElementoFin    = $objAdmiTipoElemento->getNombreTipoElemento();
                    }
                }

                // obtener login a travez de la ip 
                $strLogin = 'LIBRE';
                $objInfoEnlaceServicio =  $emInfraestructura->getRepository("schemaBundle:InfoEnlaceServicioBackbone")
                                                    ->findOneBy(array('enlaceId' => $objEnlace->getId()));
                
                $strJurisdiccion = '';
                if(is_object($objInfoEnlaceServicio))
                {
                    $strLogin = $objInfoEnlaceServicio->getLoginAux();
                   
                    $objInfoServicio   = $emInfraestructura->getRepository("schemaBundle:InfoServicio")
                    ->findOneBy(array('loginAux' => $strLogin));
            
                    $objInfoPunto    = $emInfraestructura->getRepository("schemaBundle:InfoPunto")
                                        ->find($objInfoServicio->getPuntoId());

                    $intPuntoCobertura = $objInfoPunto->getPuntoCoberturaId()->getId();
                    $objJurisdiccion = $emInfraestructura->getRepository('schemaBundle:AdmiJurisdiccion')
                                                ->find($intPuntoCobertura);

                    if(is_object($objJurisdiccion))
                    {
                        $strJurisdiccion = $objJurisdiccion->getNombreJurisdiccion();
                    }
                }

                $objTipoMedio = $emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')
                ->find($objEnlace->getTipoMedioId());

                $strTipoMedio = '';

                if($objTipoMedio)
                {
                    $strTipoMedio = $objTipoMedio->getNombreTipoMedio();
                }

                $strBuffer = '';
                $strHilo   = '';
                $strClaseTipoMedio = '';

                if($objEnlace->getBufferHiloId())
                {
                    $objBufferHilo = $emInfraestructura->getRepository("schemaBundle:InfoBufferHilo")
                    ->find($objEnlace->getBufferHiloId()->getId());

                    if($objBufferHilo)
                    {
                        $objBuffer = $emInfraestructura->getRepository("schemaBundle:AdmiBuffer")
                        ->find($objBufferHilo->getBufferId()->getId());
                        $objHilo = $emInfraestructura->getRepository("schemaBundle:AdmiHilo")
                        ->find($objBufferHilo->getHiloId()->getId());

                        if($objBuffer)
                        {
                            $strBuffer = $objBuffer->getNumeroBuffer() . ' - ' . $objBuffer->getColorBuffer();
                        }
                        if($objHilo)
                        {
                            $strHilo = $objHilo->getNumeroHilo();
                            $strColorHilo = $objHilo->getColorHilo();
                            $objClaseTipoMedio = $emInfraestructura->getRepository("schemaBundle:AdmiClaseTipoMedio")
                            ->find($objHilo->getClaseTipoMedioId());
                            if($objClaseTipoMedio)
                            {
                                $strClaseTipoMedio = $objClaseTipoMedio->getNombreClaseTipoMedio();
                            }
                        }
                    }
                }

                $objInfoEnlaceDerivacion = $emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                ->findBy( array('interfaceElementoIniId' => $objInterfaceInicio->getId(), 
                                'estado' => 'Activo'));
                $strTipoRuta = 'LIBRE';
                if(count($objInfoEnlaceDerivacion) > 1)
                {
                    $strTipoRuta = 'DERIVACION';
                }

                
                $arrayRespuesta =array(
                                        'orden'           => $intOrden,
                                        'idEnlace'        => $objEnlace->getId(),
                                        'elementoInicio'  => $strNombreElementoInicio,
                                        'elementoFin'     => $strNombreElementoFinal,
                                        'interfaceIniId'  => $objInterfaceInicio->getId(),
                                        'interfaceInicio' => $objInterfaceInicio->getNombreInterfaceElemento(),
                                        'interfaceFin'    => $objInterfaceFin->getNombreInterfaceElemento(),
                                        'hilo'            => $strHilo,
                                        'color'           => $strColorHilo,
                                        'login'           => $strLogin,
                                        'jurisdiccion'    => $strJurisdiccion,
                                        'tipoElementoIni' => $strTipoElementoInicio,
                                        'tipoElementoFin' => $strTipoElementoFin,
                                        'opciones'        => array('tipRuta' => $strTipoRuta,
                                                                    'itrIni' => $objInterfaceInicio->getId())
                                        );

                if($strNombreElementoInicio != $strNombreElementoFinal)
                {
                    array_push($arrayRespuestas,$arrayRespuesta);
                }

                if($intItrIni == $objInterfaceInicio->getId())
                {
                    break;
                }

                $objEnlaces = $emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                        ->findBy( array('interfaceElementoFinId' => $objEnlace->getInterfaceElementoIniId(),
                        'estado' => 'Activo'));

                if(empty($objEnlaces))
                {
                    break;
                }
                
                foreach($objEnlaces as $objEnlaceServicio)
                {
                    $objInfoEnlaceServicio =  $emInfraestructura->getRepository("schemaBundle:InfoEnlaceServicioBackbone")
                    ->findOneBy(array('enlaceId' => $objEnlaceServicio->getId()));
                    
                    if(is_object($objInfoEnlaceServicio))
                    {
                        $strTipoRuta = $objInfoEnlaceServicio->getTipoRuta();
                    }
                    if($strTipoRuta == $strTipoRutaPrincipal)
                    {
                        $objEnlace = $emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                        ->find($objEnlaceServicio->getId() );
                    }  
                }
                $intOrden++;
            }
        }
        
        $objResponse = new Response(json_encode(array('data'  => $arrayRespuestas)));
        $objResponse->headers->set('Content-type', 'text/json');    
                
        return $objResponse;
    }

     /**
     * getDerivacionesAction
     * funcion que permite mostrar la derivacion de una ruta
     * 
     * @author  Anthony Santillan <asantillany@telconet.ec>
     *      
     * @version 1.0 15-02-2023 Version Inicial
     * @return view
     */

    public function getDerivacionesAction()
    {
        $emInfraestructura = $this->getDoctrine()->getManager("telconet_infraestructura");
        $emComercial = $this->get('doctrine')->getManager('telconet');

        $objPeticion = $this->get('request');
        $intInterfaceElementoIniId = $objPeticion->query->get('interfaceElementoIniId');
        $intInterfaceElementoFinId = $objPeticion->query->get('interfaceElementoFinId');

        if($intInterfaceElementoIniId != '')
        {
            $objInfoEnlace = $emInfraestructura->getRepository('schemaBundle:InfoEnlace')
            ->findBy( array('interfaceElementoIniId' => $intInterfaceElementoIniId, 
                               'estado' => 'Activo'));
            $intOrden = 1;
            $arrayRespuestas =array();
            foreach($objInfoEnlace as $objEnlace)
            {
                $strLogin = 'LIBRE';
                $objInfoEnlaceServicio =  $emInfraestructura->getRepository("schemaBundle:InfoEnlaceServicioBackbone")
                                                        ->findOneBy(array('enlaceId' => $objEnlace->getId()));
                                                    
                if(is_object($objInfoEnlaceServicio))
                {
                    $strTipoRuta = $objInfoEnlaceServicio->getTipoRuta();
                }
    
                if($strTipoRuta == 'DERIVACION')
                {
                    while($objEnlace !== null)
                    {

                        $objInterfaceInicio   = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                        ->find($objEnlace->getInterfaceElementoIniId());
                        $intElementoInicio    = $objInterfaceInicio->getElementoId()->getId();

                        $objElementoInicio = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                            ->findOneBy( array('id' => $intElementoInicio, 'estado' => 'Activo') );

                        if(is_object($objElementoInicio))
                        {
                            $strNombreElementoInicio = $objElementoInicio->getNombreElemento();
                        
                            $objAdmiModeloElemento   = $emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                                        ->findOneBy( array('id' => $objElementoInicio->getModeloElementoId(), 'estado' => 'Activo') );
                            
                            if(is_object($objAdmiModeloElemento))
                            {
                                $intIdTipoElemento = $objAdmiModeloElemento->getTipoElementoId();
                            }

                            $objAdmiTipoElemento   = $emInfraestructura->getRepository('schemaBundle:AdmiTipoElemento')
                                                        ->findOneBy( array('id' => $intIdTipoElemento, 'estado' => 'Activo') );
                            
                            $strTipoElementoInicio = '';
                            if(is_object($objAdmiTipoElemento))
                            {
                                $strTipoElementoInicio    = $objAdmiTipoElemento->getNombreTipoElemento();
                            }

                        }

                        $objInterfaceFin      = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                        ->find($objEnlace->getInterfaceElementoFinId());

                        $intElementoFin       = $objInterfaceFin->getElementoId()->getId();

                        $objElementoFinal = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                            ->findOneBy( array('id' => $intElementoFin, 'estado' => 'Activo') );

                        if(is_object($objElementoFinal))
                        {                        
                            $strNombreElementoFinal = $objElementoFinal->getNombreElemento();

                            $objAdmiModeloElemento   = $emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                                        ->find($objElementoFinal->getModeloElementoId());
                            
                            if(is_object($objAdmiModeloElemento))
                            {
                                $intIdTipoElemento = $objAdmiModeloElemento->getTipoElementoId();
                            }

                            $objAdmiTipoElemento   = $emInfraestructura->getRepository('schemaBundle:AdmiTipoElemento')
                                                        ->findOneBy( array('id' => $intIdTipoElemento, 'estado' => 'Activo') );
                            
                            $strTipoElementoFin = '';
                            if(is_object($objAdmiTipoElemento))
                            {
                                $strTipoElementoFin    = $objAdmiTipoElemento->getNombreTipoElemento();
                            }
                        }

                        // obtener login a travez de la ip 
                        $strLogin = 'LIBRE';

                        $objInfoEnlaceServicio =  $emInfraestructura->getRepository("schemaBundle:InfoEnlaceServicioBackbone")
                                                            ->findOneBy(array('enlaceId' => $objEnlace->getId()));
                        
                        $strJurisdiccion = '';
                        if(is_object($objInfoEnlaceServicio))
                        {
                            $strLogin = $objInfoEnlaceServicio->getLoginAux();
                        
                            $objInfoServicio   = $emInfraestructura->getRepository("schemaBundle:InfoServicio")
                            ->findOneBy(array('loginAux' => $strLogin));
                    
                            $objInfoPunto    = $emInfraestructura->getRepository("schemaBundle:InfoPunto")
                                                ->find($objInfoServicio->getPuntoId());

                            $intPuntoCobertura = $objInfoPunto->getPuntoCoberturaId()->getId();
                            $objJurisdiccion = $emInfraestructura->getRepository('schemaBundle:AdmiJurisdiccion')
                                                        ->find($intPuntoCobertura);

                            if(is_object($objJurisdiccion))
                            {
                                $strJurisdiccion = $objJurisdiccion->getNombreJurisdiccion();
                            }
                        }
                        

                        $objTipoMedio = $emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')
                        ->find($objEnlace->getTipoMedioId());

                        $strTipoMedio = '';

                        if($objTipoMedio)
                        {
                            $strTipoMedio = $objTipoMedio->getNombreTipoMedio();
                        }

                        $strBuffer = '';
                        $strHilo   = '';
                        $strClaseTipoMedio = '';

                        if($objEnlace->getBufferHiloId())
                        {
                            $objBufferHilo = $emInfraestructura->getRepository("schemaBundle:InfoBufferHilo")
                            ->find($objEnlace->getBufferHiloId()->getId());

                            if($objBufferHilo)
                            {
                                $objBuffer = $emInfraestructura->getRepository("schemaBundle:AdmiBuffer")
                                ->find($objBufferHilo->getBufferId()->getId());
                                $objHilo = $emInfraestructura->getRepository("schemaBundle:AdmiHilo")
                                ->find($objBufferHilo->getHiloId()->getId());

                                if($objBuffer)
                                {
                                    $strBuffer = $objBuffer->getNumeroBuffer() . ' - ' . $objBuffer->getColorBuffer();
                                }
                                if($objHilo)
                                {
                                    $strHilo = $objHilo->getNumeroHilo();
                                    $strColorHilo = $objHilo->getColorHilo();
                                    $objClaseTipoMedio = $emInfraestructura->getRepository("schemaBundle:AdmiClaseTipoMedio")
                                    ->find($objHilo->getClaseTipoMedioId());
                                    if($objClaseTipoMedio)
                                    {
                                        $strClaseTipoMedio = $objClaseTipoMedio->getNombreClaseTipoMedio();
                                    }
                                }
                            }
                        }

                        
                        $arrayRespuesta =array(
                                                'orden'           => $intOrden,
                                                'idEnlace'        => $objEnlace->getId(),
                                                'elementoInicio'  => $strNombreElementoInicio,
                                                'elementoFin'     => $strNombreElementoFinal,
                                                'interfaceIniId'  => $objInterfaceInicio->getId(),
                                                'interfaceInicio' => $objInterfaceInicio->getNombreInterfaceElemento(),
                                                'interfaceFin'    => $objInterfaceFin->getNombreInterfaceElemento(),
                                                'hilo'            => $strHilo,
                                                'color'           => $strColorHilo,
                                                'login'           => $strLogin,
                                                'jurisdiccion'    => $strJurisdiccion,
                                                'tipoElementoIni' => $strTipoElementoInicio,
                                                'tipoElementoFin' => $strTipoElementoFin
                                                );

                        if($strNombreElementoInicio != $strNombreElementoFinal)
                        {
                            array_push($arrayRespuestas,$arrayRespuesta);
                        }

                        $objEnlace = $emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                ->findOneBy( array('interfaceElementoIniId' => $objEnlace->getInterfaceElementoFinId(),
                                'estado' => 'Activo'));

                        if($intItrFin == $objInterfaceFin->getId())
                        {
                            break;
                        }
                        $intOrden++;
                    }
                }
                if($strTipoRuta == 'PRINCIPAL')
                {
                    continue;
                }
                $objResponse = new Response(json_encode(array('data'  => $arrayRespuestas)));
                $objResponse->headers->set('Content-type', 'text/json');    
                        
                return $objResponse;
            }                                              
        }

        if($intInterfaceElementoIniId == '') 
        {
            throw new NotFoundHttpException('Id de la interface nulo, por favor comunicarse con sistema');
        }
    }
  
     /**
     * getEnlaceEncontradosAction
     * funcion que me retorna los hilos de un nodo incio hasta un nodo fin
     * 
     * @author  Anthony Santillan <asantillany@telconet.ec>
     *      
     * @version 1.0 15-02-2023 Version Inicial
     * @return view
     */
    public function getEnlaceEncontradosAction()
    {
        ini_set('max_execution_time', 400000);
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emComercial = $this->get('doctrine')->getManager('telconet');
        $objPeticion = $this->get('request');
        $objSession = $this->get('session');
        $strStart = $objPeticion->query->get('start');
        $strLimit = $objPeticion->query->get('limit');
        $intIdEmpresa = $objSession->get('idEmpresa');
        $intInterfaceElementoIniId = $objPeticion->query->get('interfaceElementoIniId');
        $intInterfaceElementoFinId = $objPeticion->query->get('interfaceElementoFinId');
        $strElementoNombreIni = $objPeticion->query->get('elementoIniNombre');
        $strElementoNombreFin = $objPeticion->query->get('elementoFinNombre');
        
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:InfoEnlace')
            ->generarJsonEnlacesBackbone($strElementoNombreIni, $strElementoNombreFin,$intInterfaceElementoIniId,$intInterfaceElementoFinId,
                                         '','','Activo',
                                         $intIdEmpresa,$strStart,$strLimit,$emInfraestructura , $emComercial);
        $objRespuesta->setContent($objJson);
        
        return $objRespuesta;
    }
}
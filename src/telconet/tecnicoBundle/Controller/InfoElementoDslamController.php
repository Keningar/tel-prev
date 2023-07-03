<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\tecnicoBundle\Controller;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\InfoElemento;
use telconet\schemaBundle\Entity\AdmiModeloElemento;
use telconet\schemaBundle\Entity\InfoEmpresaElemento;
use telconet\schemaBundle\Entity\InfoEmpresaElementoUbica;
use telconet\schemaBundle\Entity\InfoInterfaceElemento;
use telconet\schemaBundle\Entity\InfoDetalleElemento;
use telconet\schemaBundle\Entity\InfoIpElemento;
use telconet\schemaBundle\Entity\InfoIp;
use telconet\schemaBundle\Entity\InfoHistorialElemento;
use telconet\schemaBundle\Entity\InfoUbicacion;
use telconet\schemaBundle\Entity\InfoRelacionElemento;
use telconet\schemaBundle\Form\InfoElementoDslamType;
use telconet\schemaBundle\Form\InfoElementoPopType;
use telconet\tecnicoBundle\Resources\util\Util;
use JMS\SecurityExtraBundle\Annotation\Secure;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Response;

class InfoElementoDslamController extends Controller implements TokenAuthenticatedController
{ 
    public function indexDslamAction(){
        $session = $this->get('session');
        $session->save();
        session_write_close();
        
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');

        $tipoElemento = $em->getRepository('schemaBundle:AdmiTipoElemento')->findBy(array( "nombreTipoElemento" =>"DSLAM"));
        
        $parametros["nombre"]       = '';
        $parametros["estado"]       = 'Activo';
        $parametros["tipoElemento"] = $tipoElemento[0]->getId();
        $parametros["start"]        = '0';
        $parametros["limit"]        = '1000';                
        
        $entities = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:InfoElemento')
            ->getElementosXTipo($parametros);
        
        $rolesPermitidos = array();

        //MODULO 149 - DSLAM
        
        if (true === $this->get('security.context')->isGranted('ROLE_149-826'))
        {
                $rolesPermitidos[] = 'ROLE_149-826'; //editar elemento dslam
        }
        if (true === $this->get('security.context')->isGranted('ROLE_149-827'))
        {
                $rolesPermitidos[] = 'ROLE_149-827'; //eliminar elemento dslam
        }
        if (true === $this->get('security.context')->isGranted('ROLE_149-6'))
        {
                $rolesPermitidos[] = 'ROLE_149-6'; //ver elemento dslam
        }
        if (true === $this->get('security.context')->isGranted('ROLE_149-828'))
        {
                $rolesPermitidos[] = 'ROLE_149-828'; //administrar puertos elemento dslam
        }
        
        return $this->render('tecnicoBundle:InfoElementoDslam:index.html.twig', array(
            'entities' => $entities,
            'rolesPermitidos' => $rolesPermitidos
        ));
    }
    
    public function newDslamAction(){
        $entity = new InfoElemento();
        $form   = $this->createForm(new InfoElementoDslamType(), $entity);

        return $this->render('tecnicoBundle:InfoElementoDslam:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }
    
    /**
     * Funcion que sirve para insertar los datos del dslam
     * en la base de datos.
     * 
     * @author Francisco Adum
     * @version 1.0 6-11-2014
     * 
     * @author Lizbeth Cruz
     * @version 1.1 18-09-2018 Se agrega la validación de las coordenadas de latitud y longitud de acuerdo al país en sesión
     * 
     */
    public function createDslamAction()
    {
        $request = $this->get('request');
        $session = $request->getSession();
        $peticion = $this->get('request');
        $host = $this->container->getParameter('host');
        $pathTelcos = $this->container->getParameter('path_telcos');
        $pathParameters = $this->container->getParameter('path_parameters');
        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        
        $em->beginTransaction();
        try
        {
            $elementoDslam = new InfoElemento();
            $form = $this->createForm(new InfoElementoDslamType(), $elementoDslam);

            $parametros = $request->request->get('telconet_schemabundle_infoelementodslamtype');

            $nombreElemento = $parametros['nombreElemento'];
            $ipDslam = $parametros['ipElemento'];

            $modeloElementoId = $parametros['modeloElementoId'];
            $popElementoId = $parametros['popElementoId'];
            $descripcionElemento = $parametros['descripcionElemento'];

            //verificar que no se repita la ip
            $ipRepetida = $em->getRepository('schemaBundle:InfoIp')->findOneBy(array("ip" => $ipDslam, "estado" => "Activo"));
            if($ipRepetida)
            {
                throw new \Exception('Ip ya existe en otro Elemento, favor revisar!');
            }

            //verificar que el nombre del elemento no se repita
            $elementoRepetido = $em->getRepository('schemaBundle:InfoElemento')
                ->findOneBy(array("nombreElemento" => $nombreElemento, "estado" => "Activo"));
            if($elementoRepetido)
            {
                throw new \Exception('Nombre ya existe en otro Elemento, favor revisar!');
            }

            $modeloElemento = $em->find('schemaBundle:AdmiModeloElemento', $modeloElementoId);

            $elementoDslam->setNombreElemento($nombreElemento);
            $elementoDslam->setDescripcionElemento($descripcionElemento);
            $elementoDslam->setModeloElementoId($modeloElemento);
            $elementoDslam->setUsrResponsable($session->get('user'));
            $elementoDslam->setUsrCreacion($session->get('user'));
            $elementoDslam->setFeCreacion(new \DateTime('now'));
            $elementoDslam->setIpCreacion($peticion->getClientIp());

            $form->handleRequest($request);

            $em->persist($elementoDslam);
            $em->flush();

            $elementoDslam->setEstado("Activo");
            $em->persist($elementoDslam);
            $em->flush();

            //buscar el interface Modelo
            $interfaceModelo = $em->getRepository('schemaBundle:AdmiInterfaceModelo')->findBy(array("modeloElementoId" => $modeloElementoId));
            foreach($interfaceModelo as $im)
            {
                $cantidadInterfaces = $im->getCantidadInterface();
                $formato = $im->getFormatoInterface();

                for($i = 1; $i <= $cantidadInterfaces; $i++)
                {
                    $interfaceElemento = new InfoInterfaceElemento();

                    $format = explode("?", $formato);
                    $nombreInterfaceElemento = $format[0] . $i;

                    $interfaceElemento->setNombreInterfaceElemento($nombreInterfaceElemento);
                    $interfaceElemento->setElementoId($elementoDslam);
                    $interfaceElemento->setEstado("not connect");
                    $interfaceElemento->setUsrCreacion($session->get('user'));
                    $interfaceElemento->setFeCreacion(new \DateTime('now'));
                    $interfaceElemento->setIpCreacion($peticion->getClientIp());

                    $em->persist($interfaceElemento);
                }
            }

            //relacion elemento
            $relacionElemento = new InfoRelacionElemento();
            $relacionElemento->setElementoIdA($popElementoId);
            $relacionElemento->setElementoIdB($elementoDslam->getId());
            $relacionElemento->setTipoRelacion("CONTIENE");
            $relacionElemento->setObservacion("pop contiene dslam");
            $relacionElemento->setEstado("Activo");
            $relacionElemento->setUsrCreacion($session->get('user'));
            $relacionElemento->setFeCreacion(new \DateTime('now'));
            $relacionElemento->setIpCreacion($peticion->getClientIp());
            $em->persist($relacionElemento);

            //ip elemento
            $ipElemento = new InfoIp();
            $ipElemento->setElementoId($elementoDslam->getId());
            $ipElemento->setIp(trim($ipDslam));
            $ipElemento->setVersionIp("IPV4");
            $ipElemento->setEstado("Activo");
            $ipElemento->setUsrCreacion($session->get('user'));
            $ipElemento->setFeCreacion(new \DateTime('now'));
            $ipElemento->setIpCreacion($peticion->getClientIp());
            $em->persist($ipElemento);

            //historial elemento
            $historialElemento = new InfoHistorialElemento();
            $historialElemento->setElementoId($elementoDslam);
            $historialElemento->setEstadoElemento("Activo");
            $historialElemento->setObservacion("Se ingreso un Dslam");
            $historialElemento->setUsrCreacion($session->get('user'));
            $historialElemento->setFeCreacion(new \DateTime('now'));
            $historialElemento->setIpCreacion($peticion->getClientIp());
            $em->persist($historialElemento);

            //tomar datos pop
            $popEmpresaElementoUbicacion = $em->getRepository('schemaBundle:InfoEmpresaElementoUbica')->findOneBy(array("elementoId" => $popElementoId));
            $popUbicacion = $em->getRepository('schemaBundle:InfoUbicacion')->find($popEmpresaElementoUbicacion->getUbicacionId()->getId());
            
            $serviceInfoElemento        = $this->get("tecnico.InfoElemento");
            $arrayRespuestaCoordenadas  = $serviceInfoElemento->validarLimitesCoordenadasElemento(array(
                                                                                                        "latitudElemento"       => 
                                                                                                        $popUbicacion->getLatitudUbicacion(),
                                                                                                        "longitudElemento"      => 
                                                                                                        $popUbicacion->getLongitudUbicacion(),
                                                                                                        "msjTipoElemento"       => "del pop ",
                                                                                                        "msjTipoElementoPadre"  =>
                                                                                                        "que contiene al dslam ",
                                                                                                        "msjAdicional"          => 
                                                                                                        "por favor regularizar en la administración"
                                                                                                        ." de Pop"
                                                                                                     ));
            if($arrayRespuestaCoordenadas["status"] === "ERROR")
            {
                throw new \Exception($arrayRespuestaCoordenadas['mensaje']);
            }
            //info ubicacion
            $parroquia = $em->find('schemaBundle:AdmiParroquia', $popUbicacion->getParroquiaId());
            $ubicacionElemento = new InfoUbicacion();
            $ubicacionElemento->setLatitudUbicacion($popUbicacion->getLatitudUbicacion());
            $ubicacionElemento->setLongitudUbicacion($popUbicacion->getLongitudUbicacion());
            $ubicacionElemento->setDireccionUbicacion($popUbicacion->getDireccionUbicacion());
            $ubicacionElemento->setAlturaSnm($popUbicacion->getAlturaSnm());
            $ubicacionElemento->setParroquiaId($parroquia);
            $ubicacionElemento->setUsrCreacion($session->get('user'));
            $ubicacionElemento->setFeCreacion(new \DateTime('now'));
            $ubicacionElemento->setIpCreacion($peticion->getClientIp());
            $em->persist($ubicacionElemento);

            //empresa elemento ubicacion
            $empresaElementoUbica = new InfoEmpresaElementoUbica();
            $empresaElementoUbica->setEmpresaCod($session->get('idEmpresa'));
            $empresaElementoUbica->setElementoId($elementoDslam);
            $empresaElementoUbica->setUbicacionId($ubicacionElemento);
            $empresaElementoUbica->setUsrCreacion($session->get('user'));
            $empresaElementoUbica->setFeCreacion(new \DateTime('now'));
            $empresaElementoUbica->setIpCreacion($peticion->getClientIp());
            $em->persist($empresaElementoUbica);

            //empresa elemento
            $empresaElemento = new InfoEmpresaElemento();
            $empresaElemento->setElementoId($elementoDslam);
            $empresaElemento->setEmpresaCod($session->get('idEmpresa'));
            $empresaElemento->setEstado("Activo");
            $empresaElemento->setUsrCreacion($session->get('user'));
            $empresaElemento->setIpCreacion($peticion->getClientIp());
            $empresaElemento->setFeCreacion(new \DateTime('now'));
            $em->persist($empresaElemento);

            $em->flush();
            $em->commit();

            //perfiles
            if($modeloElemento->getNombreModeloElemento() == "A2024" || $modeloElemento->getNombreModeloElemento() == "A2048")
            {
                $comando = "nohup java -jar -Djava.security.egd=file:/dev/./urandom " . $pathTelcos . "telcos/src/telconet/tecnicoBundle/batch/ttco_perfiles.jar '" .
                    $host . "' 'perfilesA20' '" . $ipDslam . "' '" . $pathTelcos . "telcos/src/telconet/tecnicoBundle/batch/' 'loggerA20' '" .
                    $pathParameters . "' &";
                $salida = shell_exec($comando);
            }
            else if($modeloElemento->getNombreModeloElemento() == "MEA1" || $modeloElemento->getNombreModeloElemento() == "MEA3")
            {
                $comando = "nohup java -jar -Djava.security.egd=file:/dev/./urandom " . $pathTelcos . "telcos/src/telconet/tecnicoBundle/batch/ttco_perfiles.jar '" .
                    $host . "' 'perfilesMEA' '" . $ipDslam . "' '" . $pathTelcos . "telcos/src/telconet/tecnicoBundle/batch/' 'loggerMEA' '" .
                    $pathParameters . "' &";
                $salida = shell_exec($comando);
            }
        } 
        catch (\Exception $e)
        {
            if ($em->getConnection()->isTransactionActive())
            {
                $em->rollback();
            }
            $em->close();
            $this->get('session')->getFlashBag()->add('notice', $e->getMessage());
            return $this->redirect($this->generateUrl('elementodslam_newDslam'));
        }
        return $this->redirect($this->generateUrl('elementodslam_showDslam', array('id' => $elementoDslam->getId())));
    }

    public function editDslamAction($id){
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");

        if (null == $elemento = $em->find('schemaBundle:InfoElemento', $id)) {
            throw new NotFoundHttpException('No existe el elemento -dslam- que se quiere modificar');
        }
        else{
            $ipElemento = $em->getRepository('schemaBundle:InfoIp')->findOneBy(array( "elementoId" =>$elemento->getId()));
            $popElemento = $em->getRepository('schemaBundle:InfoRelacionElemento')->findOneBy(array( "elementoIdB" =>$elemento->getId()));
            $elementoUbica = $em->getRepository('schemaBundle:InfoEmpresaElementoUbica')->findOneBy(array( "elementoId" =>$elemento->getId()));
            $ubicacion = $em->getRepository('schemaBundle:InfoUbicacion')->findOneBy(array( "id" =>$elementoUbica->getUbicacionId()));
            $parroquia = $em->getRepository('schemaBundle:AdmiParroquia')->findOneBy(array( "id" =>$ubicacion->getParroquiaId()));
            $canton = $em->getRepository('schemaBundle:AdmiCanton')->findOneBy(array( "id" =>$parroquia->getCantonId()));
            $cantonJurisdiccion = $em->getRepository('schemaBundle:AdmiCantonJurisdiccion')->findOneBy(array( "cantonId" =>$canton->getId()));
        }

        $formulario =$this->createForm(new InfoElementoDslamType(), $elemento);
        
        return $this->render(   'tecnicoBundle:InfoElementoDslam:edit.html.twig', array(
                                'edit_form'             => $formulario->createView(),
                                'dslam'                 => $elemento,
                                'ipElemento'            => $ipElemento,
                                'ubicacion'             => $ubicacion,
                                'cantonJurisdiccion'    => $cantonJurisdiccion,
                                'popElemento'           => $popElemento)
                            );
    }
    
    /**
     * Documentación para el método 'updateDslamAction'.
     * 
     * Método utilizado para actualizar la información del elemento dslam
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 17-09-2018 - Se agrega la validación de las coordenadas de latitud y longitud de acuerdo al país en sesión
     * @since 1.0
     */
    public function updateDslamAction($id){
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emC = $this->getDoctrine()->getManager('telconet');
        
        $request = $this->get('request');
        $peticion = $this->get('request');
        $session  = $request->getSession();
        
        $em->beginTransaction();
        try
        {
            $entity = $em->getRepository('schemaBundle:InfoElemento')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find InfoElemento entity.');
            }

            $parametros = $request->request->get('telconet_schemabundle_infoelementodslamtype');

            $nombreElemento = $parametros['nombreElemento'];
            $popElementoId = $parametros['popElementoId'];
            $descripcionElemento = $parametros['descripcionElemento'];
            $modeloElementoId = $parametros['modeloElementoId'];
    //        $jurisdiccionId = $parametros['jurisdiccionId'];
    //        $cantonId = $parametros['cantonId'];
    //        $parroquiaId = $parametros['parroquiaId'];
            $ipElementoId = $request->request->get('idIpElemento');

            $ipElemento = $request->request->get('ipElemento');
    //        $direccionUbicacion = $request->request->get('direccionUbicacion');
    //        $longitudUbicacion = $request->request->get('longitudUbicacion');
    //        $latitudUbicacion = $request->request->get('latitudUbicacion');
    //        $alturaSnm = $request->request->get('alturaSnm');
            $idUbicacion = $request->request->get('idUbicacion');

            $modeloElemento = $em->find('schemaBundle:AdmiModeloElemento', $modeloElementoId);

            //revisar si es cambio de modelo
            $modeloAnterior = $entity->getModeloElementoId();

            $flag=0;
            if($modeloAnterior->getId()!=$modeloElemento->getId()){
                $interfaceModeloAnterior = $em->getRepository('schemaBundle:AdmiInterfaceModelo')->findOneBy(array( "modeloElementoId" =>$modeloAnterior->getId()));
                $interfaceModeloNuevo = $em->getRepository('schemaBundle:AdmiInterfaceModelo')->findOneBy(array( "modeloElementoId" =>$modeloElemento->getId()));

                $cantAnterior = $interfaceModeloAnterior->getCantidadInterface();
                $cantNueva = $interfaceModeloNuevo->getCantidadInterface();
                $formatoAnterior = $interfaceModeloAnterior->getFormatoInterface();
                $formatoNuevo = $interfaceModeloNuevo->getFormatoInterface();

                if($cantAnterior == $cantNueva){
                    //solo cambiar formato
                    for($i=1;$i<=$cantAnterior;$i++){
                        $format = explode("?", $formatoAnterior);
                        $nombreInterfaceElementoAnterior = $format[0].$i;

                        $interfaceElemento = $em->getRepository('schemaBundle:InfoInterfaceElemento')->findBy(array( "elementoId" =>$entity->getId(), "nombreInterfaceElemento"=>$nombreInterfaceElementoAnterior));

                        for($j=0;$j<count($interfaceElemento);$j++){
                            $interface = $interfaceElemento[$j];

                            if($interface->getEstado()!="deleted"){
                                $formatN = explode("?", $formatoNuevo);
                                $nombreInterfaceElementoNuevo = $formatN[0].$i;

                                $interface->setNombreInterfaceElemento($nombreInterfaceElementoNuevo);
                                $interface->setUsrCreacion($session->get('user'));
                                $interface->setFeCreacion(new \DateTime('now'));
                                $interface->setIpCreacion($peticion->getClientIp());
                                $em->persist($interface);
                            }
                        }
                    }
                }
                else if($cantAnterior > $cantNueva){
                    //revisar puertos restantes si tienen servicio
                    for($i=($cantNueva+1);$i<=$cantAnterior;$i++){
                        $format = explode("?", $formatoAnterior);
                        $nombreInterfaceElementoAnterior = $format[0].$i;
                        $interfaceElemento = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                ->findBy(array( "elementoId" =>$entity->getId(), "nombreInterfaceElemento"=>$nombreInterfaceElementoAnterior));

                        for($j=0;$j<count($interfaceElemento);$j++){
                            $interface = $interfaceElemento[$j];

                            $servicioTecnico = $emC->getRepository('schemaBundle:InfoServicioTecnico')
                                                   ->findOneBy(array( "interfaceElementoId" =>$interface->getId()));

                            if($servicioTecnico!=null || $servicioTecnico!=""){
                                $servicio = $servicioTecnico->getServicioId();
                                if($servicio->getEstado()!="Cancel" && $servicio->getEstado()!="Cancel-SinEje"){
                                    $flag=1;
                                    break;
                                }   
                            }
                        }

                        if($flag==1){
                            break;
                        }

                    }

                    if($flag==0){
                        //actualizar las interfaces
                        for($i=1;$i<=$cantNueva;$i++){
                            $format = explode("?", $formatoAnterior);
                            $nombreInterfaceElementoAnterior = $format[0].$i;

                            $interfaceElemento = $em->getRepository('schemaBundle:InfoInterfaceElemento')->findBy(array( "elementoId" =>$entity->getId(), "nombreInterfaceElemento"=>$nombreInterfaceElementoAnterior));

                            for($j=0;$j<count($interfaceElemento);$j++){
                                $interface = $interfaceElemento[$j];

                                if($interface->getEstado()!="deleted"){
                                    $formatN = explode("?", $formatoNuevo);
                                    $nombreInterfaceElementoNuevo = $formatN[0].$i;

                                    $interface->setNombreInterfaceElemento($nombreInterfaceElementoNuevo);
                                    $interface->setUsrCreacion($session->get('user'));
                                    $interface->setFeCreacion(new \DateTime('now'));
                                    $interface->setIpCreacion($peticion->getClientIp());
                                    $em->persist($interface);
                                }
                            }
                        }//fin de for

                        //cambiar estado a eliminado
                        for($i=($cantNueva+1);$i<=$cantAnterior;$i++){
                            $format = explode("?", $formatoAnterior);
                            $nombreInterfaceElementoAnterior = $format[0].$i;

                            $interfaceElemento = $em->getRepository('schemaBundle:InfoInterfaceElemento')->findBy(array( "elementoId" =>$entity->getId(), "nombreInterfaceElemento"=>$nombreInterfaceElementoAnterior));

                            for($j=0;$j<count($interfaceElemento);$j++){
                                $interface = $interfaceElemento[$j];

                                if($interface->getEstado()!="deleted"){
                                    $formatN = explode("?", $formatoNuevo);
                                    $nombreInterfaceElementoNuevo = $formatN[0].$i;

                                    $interface->setEstado("deleted");
                                    $interface->setUsrCreacion($session->get('user'));
                                    $interface->setFeCreacion(new \DateTime('now'));
                                    $interface->setIpCreacion($peticion->getClientIp());
                                    $em->persist($interface);
                                }
                            }
                        }
                    }
                }
                else if($cantAnterior < $cantNueva){
                    //actualizar las interfaces
                    for($i=1;$i<=$cantAnterior;$i++){
                        $format = explode("?", $formatoAnterior);
                        $nombreInterfaceElementoAnterior = $format[0].$i;

                        $interfaceElemento = $em->getRepository('schemaBundle:InfoInterfaceElemento')->findBy(array( "elementoId" =>$entity->getId(), "nombreInterfaceElemento"=>$nombreInterfaceElementoAnterior));

                        for($j=0;$j<count($interfaceElemento);$j++){
                            $interface = $interfaceElemento[$j];

                            if($interface->getEstado()!="deleted"){
                                $formatN = explode("?", $formatoNuevo);
                                $nombreInterfaceElementoNuevo = $formatN[0].$i;

                                $interface->setNombreInterfaceElemento($nombreInterfaceElementoNuevo);
                                $interface->setUsrCreacion($session->get('user'));
                                $interface->setFeCreacion(new \DateTime('now'));
                                $interface->setIpCreacion($peticion->getClientIp());
                                $em->persist($interface);
                            }
                        }
                    }

                    //crear las nuevas interfaces
                    for($i=($cantAnterior+1);$i<=$cantNueva;$i++){
                        $interfaceElemento = new InfoInterfaceElemento();

                        $formatN = explode("?", $formatoNuevo);
                        $nombreInterfaceElementoNuevo = $formatN[0].$i;

                        $interfaceElemento->setNombreInterfaceElemento($nombreInterfaceElementoNuevo);
                        $interfaceElemento->setElementoId($entity);
                        $interfaceElemento->setEstado("not connect");
                        $interfaceElemento->setUsrCreacion($session->get('user'));
                        $interfaceElemento->setFeCreacion(new \DateTime('now'));
                        $interfaceElemento->setIpCreacion($peticion->getClientIp());

                        $em->persist($interfaceElemento);
                    }
                }
            }


            if($flag==0){
                //elemento
                $entity->setNombreElemento($nombreElemento);
                $entity->setDescripcionElemento($descripcionElemento);
                $entity->setModeloElementoId($modeloElemento);
                $entity->setUsrResponsable($session->get('user'));
                $entity->setUsrCreacion($session->get('user'));
                $entity->setFeCreacion(new \DateTime('now'));
                $entity->setIpCreacion($peticion->getClientIp());   
                $em->persist($entity);

                $relacionElemento = $em->getRepository('schemaBundle:InfoRelacionElemento')->findOneBy(array( "elementoIdB" =>$entity));
                //ver si se cambio de pop
                $popElementoAnterior = $relacionElemento->getElementoIdA();

                if($popElementoAnterior != $popElementoId){
                    //cambiar la relacion elemento
                    $relacionElemento->setElementoIdA($popElementoId);
                    $relacionElemento->setTipoRelacion("CONTIENE");
                    $relacionElemento->setObservacion("pop contiene dslam");
                    $relacionElemento->setEstado("Activo");
                    $relacionElemento->setUsrCreacion($session->get('user'));
                    $relacionElemento->setFeCreacion(new \DateTime('now'));
                    $relacionElemento->setIpCreacion($peticion->getClientIp());
                    $em->persist($relacionElemento);

                    //tomar datos pop
                    $popEmpresaElementoUbicacion = $em->getRepository('schemaBundle:InfoEmpresaElementoUbica')->findOneBy(array("elementoId"=>$popElementoId));
                    $popUbicacion = $em->getRepository('schemaBundle:InfoUbicacion')->find($popEmpresaElementoUbicacion->getUbicacionId()->getId());

                    //cambiar ubicacion del dslam
                    $parroquia = $em->find('schemaBundle:AdmiParroquia', $popUbicacion->getParroquiaId());

                    $serviceInfoElemento        = $this->get("tecnico.InfoElemento");
                    $arrayRespuestaCoordenadas  = $serviceInfoElemento->validarLimitesCoordenadasElemento(array(
                                                                                                                "latitudElemento"       => 
                                                                                                                $popUbicacion->getLatitudUbicacion(),
                                                                                                                "longitudElemento"      => 
                                                                                                                $popUbicacion->getLongitudUbicacion(),
                                                                                                                "msjTipoElemento"       => "del pop ",
                                                                                                                "msjTipoElementoPadre"  =>
                                                                                                                "que contiene al dslam ",
                                                                                                                "msjAdicional"          => 
                                                                                                                "por favor regularizar "
                                                                                                                ."en la administración de Pop"
                                                                                                             ));
                    if($arrayRespuestaCoordenadas["status"] === "ERROR")
                    {
                        throw new \Exception($arrayRespuestaCoordenadas['mensaje']);
                    }
                    $ubicacionElemento = $em->find('schemaBundle:InfoUbicacion', $idUbicacion);
                    $ubicacionElemento->setLatitudUbicacion($popUbicacion->getLatitudUbicacion());
                    $ubicacionElemento->setLongitudUbicacion($popUbicacion->getLongitudUbicacion());
                    $ubicacionElemento->setDireccionUbicacion($popUbicacion->getDireccionUbicacion());
                    $ubicacionElemento->setAlturaSnm($popUbicacion->getAlturaSnm());
                    $ubicacionElemento->setParroquiaId($parroquia);
                    $ubicacionElemento->setUsrCreacion($session->get('user'));
                    $ubicacionElemento->setFeCreacion(new \DateTime('now'));
                    $ubicacionElemento->setIpCreacion($peticion->getClientIp());
                    $em->persist($ubicacionElemento);
                }

                //ip elemento
                $ipElementoObj = $em->getRepository('schemaBundle:InfoIp')->find($ipElementoId);
                $ipElementoObj->setIp($ipElemento);
                $ipElementoObj->setUsrCreacion($session->get('user'));
                $ipElementoObj->setFeCreacion(new \DateTime('now'));
                $ipElementoObj->setIpCreacion($peticion->getClientIp());
                $em->persist($ipElementoObj);

                //historial elemento
                $historialElemento = new InfoHistorialElemento();
                $historialElemento->setElementoId($entity);
                $historialElemento->setEstadoElemento("Modificado");
                $historialElemento->setObservacion("Se modifico el Dslam");
                $historialElemento->setUsrCreacion($session->get('user'));
                $historialElemento->setFeCreacion(new \DateTime('now'));
                $historialElemento->setIpCreacion($peticion->getClientIp());
                $em->persist($historialElemento);

                $em->flush();
                $em->commit();

                return $this->redirect($this->generateUrl('elementodslam_showDslam', array('id' => $entity->getId())));
            }
            else
            {
                throw new \Exception('El elemento aun tiene servicios en puertos que ya no se van a usar, favor regularice!');
            }
        }
        catch (\Exception $e)
        {
            if ($em->getConnection()->isTransactionActive())
            {
                $em->rollback();
            }
            $em->close();
            $this->get('session')->getFlashBag()->add('notice', $e->getMessage());
            return $this->redirect($this->generateUrl('elementodslam_editDslam', array('id' => $id)));
        }
    }
    
    public function deleteDslamAction($id){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $request = $this->getRequest();
        $peticion = $this->get('request');
        $session  = $request->getSession();
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emC = $this->getDoctrine()->getManager('telconet');
        $entity = $em->getRepository('schemaBundle:InfoElemento')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InfoElemento entity.');
        }
        
        $serviciosTec = $emC->getRepository('schemaBundle:InfoServicioTecnico')->findBy(array( "elementoId" =>$entity->getId()));
        for($i=0;$i<count($serviciosTec);$i++){
            
            $servicioId = $serviciosTec[$i]->getServicioId()->getId();
            
            $servicio = $emC->getRepository('schemaBundle:InfoServicio')->find($servicioId);
            $estadoServ = $servicio->getEstado();
            
            if($estadoServ=="Activo" || $estadoServ=="In-Corte" || $estadoServ=="In-Temp"){
                return $respuesta->setContent("SERVICIOS ACTIVOS");
            }
        }
        
        $em->getConnection()->beginTransaction();

        //elemento
        $entity->setEstado("Eliminado");
        $entity->setUsrCreacion($session->get('user'));
        $entity->setFeCreacion(new \DateTime('now'));
        $entity->setIpCreacion($peticion->getClientIp());  
        $em->persist($entity);
        
        //ip
        $ip = $em->getRepository('schemaBundle:InfoIp')->findOneBy(array( "elementoId" =>$entity->getId()));
        $ip->setEstado("Eliminado");
        $em->persist($ip);
        
        //interfaces
        $interfaceElemento = $em->getRepository('schemaBundle:InfoInterfaceElemento')->findBy(array( "elementoId" =>$entity->getId()));
        for($i=0;$i<count($interfaceElemento);$i++){
            $interface = $em->getRepository('schemaBundle:InfoInterfaceElemento')->find($interfaceElemento[$i]->getId());
            $interface->setEstado("Eliminado");
            $em->persist($interface);
        }
        
        //relacion elemento
        $relacionElemento = $em->getRepository('schemaBundle:InfoRelacionElemento')->findBy(array( "elementoIdB" =>$entity));
        $relacionElemento[0]->setEstado("Eliminado");
        $relacionElemento[0]->setUsrCreacion($session->get('user'));
        $relacionElemento[0]->setFeCreacion(new \DateTime('now'));
        $relacionElemento[0]->setIpCreacion($peticion->getClientIp());
        $em->persist($relacionElemento[0]);

        //historial elemento
        $historialElemento = new InfoHistorialElemento();
        $historialElemento->setElementoId($entity);
        $historialElemento->setEstadoElemento("Eliminado");
        $historialElemento->setObservacion("Se elimino un Dslam");
        $historialElemento->setUsrCreacion($session->get('user'));
        $historialElemento->setFeCreacion(new \DateTime('now'));
        $historialElemento->setIpCreacion($peticion->getClientIp());
        $em->persist($historialElemento);
            
        $em->flush();
        $em->getConnection()->commit();

        return $this->redirect($this->generateUrl('elementodslam'));
    }
    
    public function deleteAjaxDslamAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $peticion = $this->get('request');
        $session  = $peticion->getSession();
        $parametro = $peticion->get('param');
        
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");
        $emC = $this->getDoctrine()->getManager("telconet");
        
        $array_valor = explode("|",$parametro);
        
        //validar que no existan servicios activos al elemento
        foreach($array_valor as $id){
            $serviciosTec = $emC->getRepository('schemaBundle:InfoServicioTecnico')->findBy(array( "elementoId" =>$id));
            for($i=0;$i<count($serviciosTec);$i++){

                $servicioId = $serviciosTec[$i]->getServicioId()->getId();

                $servicio = $emC->getRepository('schemaBundle:InfoServicio')->find($servicioId);
                $estadoServ = $servicio->getEstado();

                if($estadoServ=="Activo"){
                    return $respuesta->setContent("SERVICIOS ACTIVOS");
                }
            }
        }
        
        foreach($array_valor as $id):
            if (null == $entity = $em->find('schemaBundle:InfoElemento', $id)) {
                $respuesta->setContent("No existe la entidad");
            }
            else{
                //elemento
                $entity->setEstado("Eliminado");
                $entity->setUsrCreacion($session->get('user'));
                $entity->setFeCreacion(new \DateTime('now'));
                $entity->setIpCreacion($peticion->getClientIp());  
                $em->persist($entity);

                //ip
                $ip = $em->getRepository('schemaBundle:InfoIp')->findOneBy(array( "elementoId" =>$entity->getId()));
                $ip->setEstado("Eliminado");
                $em->persist($ip);

                //interfaces
                $interfaceElemento = $em->getRepository('schemaBundle:InfoInterfaceElemento')->findBy(array( "elementoId" =>$entity->getId()));
                for($i=0;$i<count($interfaceElemento);$i++){
                    $interface = $em->getRepository('schemaBundle:InfoInterfaceElemento')->find($interfaceElemento[$i]->getId());
                    $interface->setEstado("Eliminado");
                    $em->persist($interface);
                }

                //relacion elemento
                $relacionElemento = $em->getRepository('schemaBundle:InfoRelacionElemento')->findBy(array( "elementoIdB" =>$entity));
                $relacionElemento[0]->setEstado("Eliminado");
                $relacionElemento[0]->setUsrCreacion($session->get('user'));
                $relacionElemento[0]->setFeCreacion(new \DateTime('now'));
                $relacionElemento[0]->setIpCreacion($peticion->getClientIp());
                $em->persist($relacionElemento[0]);

                //historial elemento
                $historialElemento = new InfoHistorialElemento();
                $historialElemento->setElementoId($entity);
                $historialElemento->setEstadoElemento("Eliminado");
                $historialElemento->setObservacion("Se elimino un Dslam");
                $historialElemento->setUsrCreacion($session->get('user'));
                $historialElemento->setFeCreacion(new \DateTime('now'));
                $historialElemento->setIpCreacion($peticion->getClientIp());
                $em->persist($historialElemento);
                
                $em->flush();
                
            }
        endforeach;
        //        $respuesta->setContent($id);
        $respuesta->setContent("OK");
        
        return $respuesta;
    }
    
    public function showDslamAction($id){
        $peticion = $this->get('request');
        $session  = $peticion->getSession();
        $empresaId=$session->get('idEmpresa');

        $em = $this->getDoctrine()->getManager("telconet_infraestructura");

        if(null == $elemento = $em->find('schemaBundle:InfoElemento', $id))
        {
            throw new NotFoundHttpException('No existe el Elemento que se quiere mostrar');
        }
        else
        {
            /* @var $datosElemento InfoServicioTecnico */
            $datosElemento = $this->get('tecnico.InfoServicioTecnico');
            //---------------------------------------------------------------------*/
            $respuestaElemento = $datosElemento->obtenerDatosElemento($id, $empresaId);
            
            $ipElemento         = $respuestaElemento['ipElemento'];
            $arrayHistorial     = $respuestaElemento['historialElemento'];
            $ubicacion          = $respuestaElemento['ubicacion'];
            $jurisdiccion       = $respuestaElemento['jurisdiccion'];
        }

        return $this->render('tecnicoBundle:InfoElementoDslam:show.html.twig', array(
            'elemento'              => $elemento,
            'ipElemento'            => $ipElemento,
            'historialElemento'     => $arrayHistorial,
            'ubicacion'             => $ubicacion,
            'jurisdiccion'          => $jurisdiccion,
            'flag'                  => $peticion->get('flag')
        ));
    }
    
    public function buscarInterfacesAction(){
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
    
    public function getEncontradosDslamAction(){
       $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $session = $this->get('session');
        $session->save();
        session_write_close();
        
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');
        $tipoElemento = $em->getRepository('schemaBundle:AdmiTipoElemento')->findBy(array( "nombreTipoElemento" =>"DSLAM"));
        
        $peticion = $this->get('request');
        
        $nombreElemento = $peticion->query->get('nombreElemento');
        $ipElemento = $peticion->query->get('ipElemento');        
        $modeloElemento = $peticion->query->get('modeloElemento');
        $marcaElemento = $peticion->query->get('marcaElemento');
        $canton = $peticion->query->get('canton');
        $jurisdiccion = $peticion->query->get('jurisdiccion');
        $popElemento = $peticion->query->get('popElemento');
        $estado = $peticion->query->get('estado');
        $idEmpresa = $session->get('idEmpresa');
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:InfoElemento')
            ->generarJsonDslams(strtoupper($nombreElemento),$ipElemento,$modeloElemento,$marcaElemento,$tipoElemento[0]->getId(),$canton,$jurisdiccion,$popElemento,$estado,$start,$limit,$em,$idEmpresa);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    public function cargarDatosDslamAction(){
       $respuesta = new Response();
       $em = $this->getDoctrine()->getManager('telconet_infraestructura');
       
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $idDslam = $peticion->get('idDslam');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:InfoElemento')
            ->generarJsonCargarDatosDslam($idDslam,$em);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    public function getDocumentoPorModeloAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        
        $peticion = $this->get('request');
        
        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array( "nombreModeloElemento" =>$modeloElemento));
        
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarConfiguracionDslamA2024",$modelo[0]->getId(),$emSop,$emCom);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    /**
     * Funcion que sirve para la administracion de puertos de un dslam.
     * 
     * @author Francisco Adum
     * @version 1.0 6-11-2014
     */
    public function administrarPuertosAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $emInfraestructura = $this->get('doctrine')->getManager('telconet_infraestructura');

        $peticion = $this->get('request');
        $session = $peticion->getSession();

        $jsonInterfaces = $peticion->get('interfaces');
        $json_interfaces = json_decode($jsonInterfaces);
        $arrayInterfaces = $json_interfaces->interfaces;

        $emInfraestructura->getConnection()->beginTransaction();
        
        try
        {
            //recorrer los puertos
            for($i = 0; $i < count($arrayInterfaces); $i++)
            {
                $idInterface = $arrayInterfaces[$i]->idInterfaceElemento;
                $estado = $arrayInterfaces[$i]->estado;

                if($estado == "Activo")
                {
                    $estado = "not connect";
                }
                else if($estado == "Online")
                {
                    $estado = "connected";
                }
                else if($estado == "Dañado")
                {
                    $estado = "err-disabled";
                }
                else if($estado == "Inactivo")
                {
                    $estado = "disabled";
                }

                //buscar el objeto interface por id
                $interface = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')->find($idInterface);
                
                //verificar si el estado cambio
                if($interface->getEstado() != $estado)
                {
                    $interface->setEstado($estado);
                    $emInfraestructura->persist($interface);
                }
            }
            $emInfraestructura->flush();
        }
        catch(\Exception $e)
        {
            if($emInfraestructura->getConnection()->isTransactionActive())
            {
                $emInfraestructura->getConnection()->rollback();
            }
            $respuesta->setContent("Error: " . $e->getMessage() . ", <br> Favor Notificar a Sistemas.");
            return $respuesta;
        }
        
        //*DECLARACION DE COMMITS*/
        if($emInfraestructura->getConnection()->isTransactionActive())
        {
            $emInfraestructura->getConnection()->commit();
        }

        $result = "OK";

        return $respuesta->setContent($result);
    }

    /**
     * Funcion que sirve para la actualizacion masiva de los perfiles de los dslams.
     * 
     * @author Francisco Adum
     * @version 1.0 10-11-2014
     */
    public function actualizarPerfilesAction()
    {
        $pathTelcos = $this->container->getParameter('path_telcos');
        $host = $this->container->getParameter('host');
        $pathParameters = $this->container->getParameter('path_parameters');
        ini_set('max_execution_time', 3000000);
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $comando = "nohup java -jar -Djava.security.egd=file:/dev/./urandom " . $pathTelcos . "telcos/src/telconet/tecnicoBundle/batch/ttco_perfiles.jar '" .
            $host . "' 'todosPerfiles' '0' '" . $pathTelcos . "telcos/src/telconet/tecnicoBundle/batch/' 'loggerPerfiles' '" .
            $pathParameters . "' &";
        $salida = shell_exec($comando);

        return $respuesta->setContent("OK");
    }

    /**
     * Funcion que sirve para actualizar indivualmente los perfiles de los dslams
     * 
     * @author Francisco Adum
     * @version 1.0 6-11-2014
     * @param int $id
     */
    public function actualizarPerfilesDslamAction($id)
    {
        ini_set('max_execution_time', 3000000);
        $pathTelcos = $this->container->getParameter('path_telcos');
        $host = $this->container->getParameter('host');
        $pathParameters = $this->container->getParameter('path_parameters');
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->getDoctrine()->getManager('telconet_infraestructura');

        $entity = $em->getRepository('schemaBundle:InfoElemento')->find($id);

        if(!$entity)
        {
            throw $this->createNotFoundException('Unable to find InfoElemento entity.');

            $resultado = json_encode(array('success' => false));
            return $resultado;
        }
        else
        {
            $objAdmiModeloElemento = $entity->getModeloElementoId();

            $objInfoIp = $em->getRepository('schemaBundle:InfoIp')->findBy(array("elementoId" => $id, "estado" => "Activo"));
            $ipDslam = $objInfoIp[0]->getIp();

            //perfiles
            if($objAdmiModeloElemento->getNombreModeloElemento() == "A2024" || $objAdmiModeloElemento->getNombreModeloElemento() == "A2048")
            {
                $comando = "java -jar -Djava.security.egd=file:/dev/./urandom " . $pathTelcos . "telcos/src/telconet/tecnicoBundle/batch/ttco_perfiles.jar '" .
                    $host . "' 'perfilesA20' '" . $ipDslam . "' '" . $pathTelcos . "telcos/src/telconet/tecnicoBundle/batch/' 'loggerA20' '" .
                    $pathParameters . "'";
                $salida = shell_exec($comando);
            }
            else if($objAdmiModeloElemento->getNombreModeloElemento() == "MEA1" || $objAdmiModeloElemento->getNombreModeloElemento() == "MEA3")
            {
                $comando = "java -jar -Djava.security.egd=file:/dev/./urandom " . $pathTelcos . "telcos/src/telconet/tecnicoBundle/batch/ttco_perfiles.jar '" .
                    $host . "' 'perfilesMEA' '" . $ipDslam . "' '" . $pathTelcos . "telcos/src/telconet/tecnicoBundle/batch/' 'loggerMEA' '" .
                    $pathParameters . "'";
                $salida = shell_exec($comando);
            }
            
            $pos = strpos($salida, "{"); 
            $jsonObj= substr($salida, $pos);
            $resultadJson = json_decode($jsonObj);

            if($resultadJson->mensaje == "OK")
            {
                $array = json_encode(array('success' => true));   
            }
            else
            {
                $array = json_encode(array('success' => false));
            }
            
            $resultado = $respuesta->setContent($array);
            return $resultado;
        }
    }

    /********************************************************************************
     *                          ACCCIONES PARA DSLAM A20                            *
     ********************************************************************************/
    
    /**
     * Funcion que sirve para mostrar la configuracion general del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarConfiguracionDslamA2024Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarConfiguracionDslamA2024", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, "");
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para mostrar la operatividad de los puertos 
     * del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarTodosPuertosDslamA2024Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));


        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarTodosPuertosDslamA2024", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, "");
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para mostrar un listado de las
     * macs en el olt.
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarMacsDslamA2024Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));


        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarMacsDslamA2024", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, "");
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para mostrar el rendimiento
     * del dslam.
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarRendimientoDslamA2024Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));


        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarRendimientoDslamA2024", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, "");
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para mostrar la temperatura 
     * del dslam.
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarTemperaturaDslamA2024Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));


        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarTemperaturaDslamA2024", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, "");
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para ver la operatividad y velocidad
     * de un puerto en especifico del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarConfiguracionInterfaceDslamA2024Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $interfaceElemento = $peticion->get('interfaceElemento');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarConfiguracionInterfaceDslamA2024", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, $interfaceElemento);
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para ver la velocidad real de un puerto 
     * en especifico del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarVelocidadRealDslamA2024Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $interfaceElemento = $peticion->get('interfaceElemento');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarVelocidadRealDslamA2024", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, $interfaceElemento);
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para ver la señal extremo lejano
     * de un puerto en especifico del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarNivelesSenalExtremoLejanoDslamA2024Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $interfaceElemento = $peticion->get('interfaceElemento');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarNivelesSenalExtremoLejanoDslamA2024", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, $interfaceElemento);
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para ver la señal extremo cercano
     * de un puerto en especifico del dslam.
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarNivelesSenalExtremoCercanoDslamA2024Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $interfaceElemento = $peticion->get('interfaceElemento');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarNivelesSenalExtremoCercanoDslamA2024", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, $interfaceElemento);
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para ver la configuracion bridge de
     * un puerto en especifico del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarConfiguracionBridgeDslamA2024Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $interfaceElemento = $peticion->get('interfaceElemento');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarConfiguracionBridgeDslamA2024", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, $interfaceElemento);
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para ver la configuracion del circuito
     * virtual de un puerto en especifico del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarCircuitoVirtualDslamA2024Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $interfaceElemento = $peticion->get('interfaceElemento');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarCircuitoVirtualDslamA2024", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, $interfaceElemento);
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para mostrar los contadores de un 
     * puerto en especifico del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarContadoresDslamA2024Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $interfaceElemento = $peticion->get('interfaceElemento');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarContadoresDslamA2024", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, $interfaceElemento);
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para ver el desempeño diario de
     * un puerto en 
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarDesempenoPuertoDiarioDslamA2024Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $interfaceElemento = $peticion->get('interfaceElemento');
        $interf = explode(" ", $interfaceElemento);

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarDesempenoPuertoDiarioDslamA2024", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, $interf[1]);
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para ver el desempeño del puerto por un
     * intervalo de tiempo definido en el olt.
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarDesempenoPuertoIntervaloDslamA2024Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $interfaceElemento = $peticion->get('interfaceElemento');
        $interf = explode(" ", $interfaceElemento);

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarDesempenoPuertoIntervaloDslamA2024", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, $interf[1]);
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para resetear los contadores de un puerto
     * en especifico de un dslam.
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function resetearPuertoDslamA20Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $interfaceElemento = $peticion->get('interfaceElemento');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("resetearPuertoDslamA20", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, $interfaceElemento);
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }
    
    /********************************************************************************
     *                          ACCCIONES PARA DSLAM R1                             *
     ********************************************************************************/
    /**
     * Funcion que sirve para mostrar la configuracion general
     * del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarConfiguracionDslamR1Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarConfiguracionDslamR1", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, "");
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para mostrar la operatividad de los puertos
     * del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarTodosPuertosDslamR1Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));


        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarTodosPuertosDslamR1", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, "");
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para mostrar las macs que se
     * encuentran en el dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarMacsDslamR1Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));


        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarMacsDslamR1", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, "");
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para ver la configuracion de la interface
     * del dslam.
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarInterfaceDslamR1Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));


        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarInterfaceDslamR1", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, "");
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para mostrar el rendimiento general del
     * dslam.
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarRendimientoDslamR1Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));


        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarRendimientoDslamR1", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, "");
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para ver la temperatura del
     * dslam.
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarTemperaturaDslamR1Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));


        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarTemperaturaDslamR1", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, "");
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }


        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para mostrar los logs del
     * equipo.
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarLogsDslamR1Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));


        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarLogsDslamR1", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, "");
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para mostrar la configuracion
     * de un puerto en especifico del dslam.
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarConfiguracionInterfaceDslamR1Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $interfaceElemento = $peticion->get('interfaceElemento');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarConfiguracionInterfaceDslamR1", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, $interfaceElemento);
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para mostrar las macs de un puerto en especifico
     * del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarMacsPuertosDslamR1Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $interfaceElemento = $peticion->get('interfaceElemento');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarMacsPuertoDslamR1", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, $interfaceElemento);
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para ver la velocidad de un puerto
     * en especifico del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarVelocidadRealDslamR1Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $interfaceElemento = $peticion->get('interfaceElemento');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarVelocidadRealDslamR1", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, $interfaceElemento);
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para mostrar la informacion (parte 1) 
     * de un puerto en especifico del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarMonitoreoPuertoDataIDslamR1Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $interfaceElemento = $peticion->get('interfaceElemento');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarMonitoreoPuertoDataIDslamR1", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, $interfaceElemento);
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para mostrar la informacion (parte 2)
     * de un puerto en especifico del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarMonitoreoPuertoDataIIDslamR1Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $interfaceElemento = $peticion->get('interfaceElemento');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarMonitoreoPuertoDataIIDslamR1", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, $interfaceElemento);
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para mostrar el estado de un puerto en 
     * especifico del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarStatusPuertoDslamR1Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $interfaceElemento = $peticion->get('interfaceElemento');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarStatusPuertoDslamR1", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, $interfaceElemento);
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para ver la codificacion de un puerto
     * en especifico del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarCodificacionDslamR1Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $interfaceElemento = $peticion->get('interfaceElemento');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarCodificacionDslamR1", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, $interfaceElemento);
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para resetear un puerto en especifico del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function resetearPuertoDslamR1Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $interfaceElemento = $peticion->get('interfaceElemento');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("resetearPuertoDslamR1", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, $interfaceElemento);
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para limpiar un puerto
     * en especifico del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function limpiarContadoresPuertoDslamR1Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $interfaceElemento = $peticion->get('interfaceElemento');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("limpiarContadoresPuertoDslamR1", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;
        
        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, $interfaceElemento);
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para cambiar la codificacion
     * de un puerto en especifico de un dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function cambiarCodificacionPuertoDslamR1Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $interfaceElemento = $peticion->get('interfaceElemento');

        $codificacion = $peticion->get('codificacion');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("cambiarCodificacionPuertoDslamR1", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            $datos = $interfaceElemento . "," . $codificacion;
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, $datos);
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /********************************************************************************
     *                          ACCCIONES PARA DSLAM 7224                           *
     ********************************************************************************/
    
  /**
     * Funcion que sirve para mostrar configuracion general
     * de und dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarConfiguracionDslam7224Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarConfiguracionDslam7224", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, "");
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para mostrar las macs en un dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarMacsDslam7224Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarMacsDslam7224", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, "");
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para ver las configuracion de los
     * puertos del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarTodosPuertosDslam7224Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarTodosPuertosDslam7224", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, "");
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para ver el rendimiento
     * del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarRendimientoDslam7224Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarRendimientoDslam7224", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, "");
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para ver los logs del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarLogsDslam7224Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarLogsDslam7224", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, "");
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para ver la interface del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarInterfaceDslam7224Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarInterfaceDslam7224", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, "");
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para mostrar la configuracion de un
     * puerto en especifico del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarConfiguracionInterfaceDslam7224Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $interfaceElemento = $peticion->get('interfaceElemento');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarConfiguracionInterfaceDslam7224", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, $interfaceElemento);
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para mostrar las macs de un puerto
     * en especifico del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarMacsPuertoDslam7224Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $interfaceElemento = $peticion->get('interfaceElemento');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarMacsPuertoDslam7224", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, $interfaceElemento);
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para ver la velocidad de un puerto
     * en especifico del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarVelocidadSeteadaDslam7224Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $interfaceElemento = $peticion->get('interfaceElemento');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarVelocidadSeteadaDslam7224", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);
        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, $interfaceElemento);
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para mostrar el monitoreo de un puerto
     * en especifico del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarMonitorearPuertoDslam7224Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $interfaceElemento = $peticion->get('interfaceElemento');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarMonitorearPuertoDslam7224", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, $interfaceElemento);
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para ver los parametros de linea
     * de un puerto en especifico de un dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarParametrosLineaDslam7224Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $interfaceElemento = $peticion->get('interfaceElemento');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarParametrosLineaDslam7224", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, $interfaceElemento);
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para ver la codificacion de linea
     * de un puerto en especifico del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarCodificacionLineaDslam7224Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $interfaceElemento = $peticion->get('interfaceElemento');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarCodificacionLineaDslam7224", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, $interfaceElemento);
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para resetear un puerto
     * en especifico de un dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function resetearPuertoDslam7224Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $interfaceElemento = $peticion->get('interfaceElemento');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("resetearPuertoDslam7224", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, $interfaceElemento);
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para limpiar los contadores
     * de un puerto en especifico del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function limpiarContadoresPuertoDslam7224Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $interfaceElemento = $peticion->get('interfaceElemento');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("limpiarContadoresPuertoDslam7224", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, $interfaceElemento);
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para cambiar la codificacion 
     * de un puerto en especifico del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function cambiarCodificacionPuertoDslam7224Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $interfaceElemento = $peticion->get('interfaceElemento');

        $codificacion = $peticion->get('codificacion');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("cambiarCodificacionPuertoDslam7224", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            $datos = $interfaceElemento . "," . $codificacion;
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, $datos);
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /********************************************************************************
     *                          ACCCIONES PARA DSLAM 6524                           *
     ********************************************************************************/
    
  /**
     * Funcion que sirve para mostrar la configuracion
     * general de un dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarConfiguracionDslam6524Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));


        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarConfiguracionDslam6524", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, "");
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para mostrar las macs
     * de un dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarMacsDslam6524Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarMacsDslam6524", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, "");
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para mostrar los logs de un dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarLogsDslam6524Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));


        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarLogsDslam6524", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, "");
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para ver la velocidad de un puerto
     * en especifico del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarVelocidadSeteadaDslam6524Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $interfaceElemento = $peticion->get('interfaceElemento');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarVelocidadSeteadaDslam6524", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, $interfaceElemento);
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para ver las macs de un puerto en 
     * especifico del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarMacsPuertoDslam6524Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $interfaceElemento = $peticion->get('interfaceElemento');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarMacsPuertoDslam6524", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, $interfaceElemento);
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para ver la velocidad de un puerto
     * en especifico del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarVelocidadRealDslam6524Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $interfaceElemento = $peticion->get('interfaceElemento');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarVelocidadRealDslam6524", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, $interfaceElemento);
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para ver el monitoreo de un puerto
     * en especifico del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarMonitorearPuertoDslam6524Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $interfaceElemento = $peticion->get('interfaceElemento');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarMonitorearPuertoDslam6524", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);
        
        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, $interfaceElemento);
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para ver el nombre de un puerto
     * en especifico del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarNombrePuertoDslam6524Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $interfaceElemento = $peticion->get('interfaceElemento');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarNombrePuertoDslam6524", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, $interfaceElemento);
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para mostrar el crc de un puerto
     * en especifico del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarCrcDslam6524Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $interfaceElemento = $peticion->get('interfaceElemento');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarCrcDslam6524", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, $interfaceElemento);
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para ver la señal de ruido
     * de un puerto en especifico del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarSenalRuidoDslam6524Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $interfaceElemento = $peticion->get('interfaceElemento');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarSenalRuidoDslam6524", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, $interfaceElemento);
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para ver la atenuacion de un
     * puerto en especifico del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarAtenuacionDslam6524Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $interfaceElemento = $peticion->get('interfaceElemento');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarAtenuacionDslam6524", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, $interfaceElemento);
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para ver la codificacion de un
     * puerto en especifico del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarCodificacionDslam6524Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $interfaceElemento = $peticion->get('interfaceElemento');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarCodificacionDslam6524", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, $interfaceElemento);
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para ver las restricciones de ip
     * en un puerto en especifico del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarRestriccionIpDslam6524Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $interfaceElemento = $peticion->get('interfaceElemento');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarRestriccionIpDslam6524", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, $interfaceElemento);
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para resetear un puerto en especifico
     * del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function resetearPuertoDslam6524Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $interfaceElemento = $peticion->get('interfaceElemento');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("resetearPuertoDslam6524", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;
        
        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, $interfaceElemento);
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para cambiar la codificacion de un puerto
     * en especifico del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function cambiarCodificacionPuertoDslam6524Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $interfaceElemento = $peticion->get('interfaceElemento');

        $codificacion = $peticion->get('codificacion');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("cambiarCodificacionPuertoDslam6524", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            $datos = $interfaceElemento . "," . $codificacion;
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, $datos);
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /********************************************************************************
     *                          ACCCIONES PARA DSLAM MEA                            *
     ********************************************************************************/
    
  /**
     * Funcion que sirve para ver la configuracion general
     * del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarConfiguracionDslamMeaAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarConfiguracionDslamMea", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, "");
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para ver las macs en el dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarMacsDslamMeaAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarMACMEA", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, "");
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para ver la interface virtual 1
     * del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarInterfaceVirtualEth1DslamMeaAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));


        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarInterfaceVirtualEth1DslamMea", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, "");
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para ver la interface virtual 2
     * del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarInterfaceVirtualEth2DslamMeaAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarInterfaceVirtualEth2DslamMea", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, "");
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para ver los puertos conectados
     * en el dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarPuertosConectadosDslamMeaAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarPuertosConectadosDslamMea", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, "");
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para ver el procesamiento del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarProcesamientoDslamMeaAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));


        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarProcesamientoDslamMea", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, "");
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para ver el disco del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarDiscoDslamMeaAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));


        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarDiscoDslamMea", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, "");
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para ver la memoria del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarMemoriaDslamMeaAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));


        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarMemoriaDslamMea", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, "");
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para ver el tiempo de actividad
     * del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarTiempoActividadDslamMeaAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));


        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarTiempoActividadDslamMea", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, "");
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para ver la temperatura del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarTemperaturaDslamMeaAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));


        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarTemperaturaDslamMea", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, "");
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }


        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para ver las macs en un puerto
     * en especifico del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarMacPuertoDslamMeaAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $interfaceElemento = $peticion->get('interfaceElemento');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarMacPuertoDslamMea", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, $interfaceElemento);
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }


        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para ver la configuracion de un puerto
     * en especifico del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarConfiguracionPuertoDslamMeaAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $interfaceElemento = $peticion->get('interfaceElemento');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarConfiguracionPuertoDslamMea", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, $interfaceElemento);
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }


        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para ver el desempeño diario de un puerto
     * en especifico del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarDesempenoPuertoDiarioDslamMeaAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $interfaceElemento = $peticion->get('interfaceElemento');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarDesempenoPuertoDiarioDslamMea", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, $interfaceElemento);
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }


        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para ver el desempeño por un intervalo de 
     * tiempo de un puerto en especifico del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarDesempenoPuertoIntervaloDslamMeaAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $interfaceElemento = $peticion->get('interfaceElemento');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarDesempenoPuertoIntervaloDslamMea", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, $interfaceElemento);
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }


        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para ver los errores en un puerto
     * en especifico del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarErroresPuertoDslamMeaAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $interfaceElemento = $peticion->get('interfaceElemento');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarErroresPuertoDslamMea", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, $interfaceElemento);
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }


        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para ver el estado de un puerto
     * en especifico del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarEstadoPuertoDslamMeaAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $interfaceElemento = $peticion->get('interfaceElemento');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarEstadoPuertoDslamMea", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, $interfaceElemento);
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }


        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para ver la interface de un puerto
     * en especifico del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarInterfacePuertoDslamMeaAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $interfaceElemento = $peticion->get('interfaceElemento');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarInterfacePuertoDslamMea", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, $interfaceElemento);
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }


        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para ver el vci de un puerto
     * en especifico del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarVciPuertoDslamMeaAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $interfaceElemento = $peticion->get('interfaceElemento');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarVciPuertoDslamMea", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, $interfaceElemento);
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }


        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para ver el tiempo de actividad
     * de un puerto en especifico del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function mostrarTiempoActividadPuertoDslamMeaAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $interfaceElemento = $peticion->get('interfaceElemento');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarTiempoActividadPuertoDslamMea", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, $interfaceElemento);
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }


        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para limpiar los contadores de
     * un puerto en especifico del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function limpiarContadoresPuertoDslamMeaAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $interfaceElemento = $peticion->get('interfaceElemento');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("limpiarContadoresPuertoDslamMea", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, $interfaceElemento);
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }
    
    /**
     * @Secure(roles="ROLE_149-827")
     * 
     * Documentación para el método 'quitarOperatividad'.
     *
     * Metodo utilizado para ingresar caracteristica de operatividad al elemento
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 18-05-2016
     * @since 1.0
     */
    public function quitarOperatividadAction()
    {
        $respuesta          = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $peticion           = $this->get('request');
        $session            = $peticion->getSession();
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');
        $intIdElemento      = $peticion->get('idElemento');

        $emInfraestructura->getConnection()->beginTransaction();

        try
        {
            $entityElemento       = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($intIdElemento);
            
            $objDetalleElemento = new InfoDetalleElemento();
            $objDetalleElemento->setElementoId($intIdElemento);
            $objDetalleElemento->setDetalleNombre("RADIO OPERATIVO");
            $objDetalleElemento->setDetalleValor("NO");
            $objDetalleElemento->setDetalleDescripcion("RADIO OPERATIVO");
            $objDetalleElemento->setUsrCreacion($session->get('user'));
            $objDetalleElemento->setFeCreacion(new \DateTime('now'));
            $objDetalleElemento->setIpCreacion($peticion->getClientIp());
            $emInfraestructura->persist($objDetalleElemento);
            
             //historial elemento
            $historialElemento = new InfoHistorialElemento();
            $historialElemento->setElementoId($entityElemento);
            $historialElemento->setEstadoElemento($entityElemento->getEstado());
            $historialElemento->setObservacion("Se registro NO Operatividad del elemento");
            $historialElemento->setUsrCreacion($session->get('user'));
            $historialElemento->setFeCreacion(new \DateTime('now'));
            $historialElemento->setIpCreacion($peticion->getClientIp());
            $emInfraestructura->persist($historialElemento);
                

            $emInfraestructura->flush();
            $emInfraestructura->getConnection()->commit();
            
            return $respuesta->setContent("OK");
        }
        catch(\Exception $e)
        {
            $emInfraestructura->getConnection()->rollback();
            $emInfraestructura->getConnection()->close();
            $mensajeError = "Error: " . $e->getMessage();
            error_log($mensajeError);
            return $respuesta->setContent("PROBLEMAS TRANSACCION");
        }
    }

    /**
     * Funcion que sirve para resetear un puerto en 
     * especifico del dslam
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24 11-24-2014
     */
    public function resetearPuertoDslamMeaAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');

        $modeloElemento = $peticion->get('modelo');
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $interfaceElemento = $peticion->get('interfaceElemento');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("resetearPuertoDslamMea", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            /* @var $comandoEjecucion InfoServicioTecnicoService */
            $comandoEjecucion = $this->get('tecnico.InfoServicioTecnico');
            $salida = $comandoEjecucion->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $idElemento, $interfaceElemento);
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

}
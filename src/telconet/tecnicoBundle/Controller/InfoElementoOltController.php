<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\tecnicoBundle\Controller;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\InfoElemento;
use telconet\schemaBundle\Entity\InfoDetalleElemento;
use telconet\schemaBundle\Entity\AdmiModeloElemento;
use telconet\schemaBundle\Entity\InfoEmpresaElemento;
use telconet\schemaBundle\Entity\InfoEmpresaElementoUbica;
use telconet\schemaBundle\Entity\InfoInterfaceElemento;
use telconet\schemaBundle\Entity\InfoIpElemento;
use telconet\schemaBundle\Entity\InfoIp;
use telconet\schemaBundle\Entity\InfoHistorialElemento;
use telconet\schemaBundle\Entity\InfoUbicacion;
use telconet\schemaBundle\Entity\InfoRelacionElemento;
use telconet\schemaBundle\Entity\InfoDocumento;
use telconet\schemaBundle\Entity\AdmiTipoDocumento;
use telconet\schemaBundle\Entity\InfoMigraAdCab;
use telconet\schemaBundle\Entity\InfoMigraAdData;
use telconet\schemaBundle\Form\InfoElementoOltType;
use telconet\schemaBundle\Form\InfoElementoPopType;
use telconet\tecnicoBundle\Resources\util\Util;
use telconet\tecnicoBundle\Service\InfoServicioTecnicoService;
use telconet\schemaBundle\Entity\InfoSubred;
use telconet\schemaBundle\Entity\InfoSubredTag;
use telconet\schemaBundle\Entity\AdmiPolicy;
use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoDetalleSolCaract;
use telconet\schemaBundle\Entity\AdmiParametroCab;
use telconet\schemaBundle\Entity\AdmiParametroDet;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Response;
use \telconet\schemaBundle\Entity\ReturnResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

class InfoElementoOltController extends Controller implements TokenAuthenticatedController
{ 
    const DETALLE_ELEMENTO_APROVISIONAMIENTO = 'APROVISIONAMIENTO_IP';
    const DETALLE_ELEMENTO_MIDDLEWARE        = 'MIDDLEWARE';
    const ESTADO_ACTIVO                      = 'Activo';
    const ACCION_MONITOREO_CLIENTES          = 'Clientes';
    const ACCION_MONITOREO_HISTORIAL         = 'Historial';
    const VALOR_SI                           = 'SI';
    const OLT_MIGRADO_CNR                    = 'OLT MIGRADO CNR';
    const DETALLE_ELEMENTO_IPV6              = 'IPV6';
    const DETALLE_ELEMENTO_NODO_ASIGNADO     = 'NODO_ASIGNADO';
    const DETALLE_ELEMENTO_PE_ASIGNADO       = 'PE_ASIGNADO';
    const DETALLE_ELEMENTO_MULTIPLATAFORMA   = 'MULTIPLATAFORMA';
    const DETALLE_ELEMENTO_ACTIVO_MULTI      = 'ELEMENTO_ACTIVO_POR_MULTIPLATAFORMA';
    const DETALLE_ELEMENTO_VLAN_PRINCIPAL    = 'VLAN INTERNET GPON PRINCIPAL';
    const DETALLE_ELEMENTO_VLAN_BACKUP       = 'VLAN INTERNET GPON BACKUP';
    const DETALLE_ELEMENTO_VLAN_SAFECITY     = 'VLAN SAFECITY GPON';
    const DETALLE_ELEMENTO_INTERFACES_PE     = 'INTERFACES_PE';
    const DETALLE_ELEMENTO_AGREGADOR_ASIGNADO = 'AGREGADOR_ASIGNADO';
    
    /*
     * 
     * Documentación para el método 'indexOltAction'.
     *
     * Metodo utilizado redireccionar a la pantalla principal de la Administracion de Olt
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.1 06-08-2015
     * @version 1.2 04-09-2015
     * @version 1.3 15-02-2016
     * @version 1.4 07-10-2019 - Se agregó permiso para Generar Migración
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.5 15-05-2020 - Se agregó permiso para la opción de Solicitud de Agregar Equipo Masivo
     * 
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.6 23-03-2021 - Se agregó permiso para la opción de OLTs Multiplataforma
     */
    public function indexOltAction()
    {
        $session = $this->get('session');
        $session->save();
        session_write_close();
        $strIdEmpresa = $session->get('idEmpresa');

        $em = $this->getDoctrine()->getManager('telconet_infraestructura');

        $rolesPermitidos = array();

        //MODULO 227 - OLT

        if (true === $this->get('security.context')->isGranted('ROLE_227-4'))
        {
            $rolesPermitidos[] = 'ROLE_227-4'; //editar elemento olt
        }
        if (true === $this->get('security.context')->isGranted('ROLE_227-8'))
        {
            $rolesPermitidos[] = 'ROLE_227-8'; //eliminar elemento olt
        }
        if (true === $this->get('security.context')->isGranted('ROLE_227-6'))
        {
            $rolesPermitidos[] = 'ROLE_227-6'; //ver elemento olt
        }
        if (true === $this->get('security.context')->isGranted('ROLE_227-828'))
        {
            $rolesPermitidos[] = 'ROLE_227-828'; //administrar puertos elemento olt
        }
        if (true === $this->get('security.context')->isGranted('ROLE_227-1217'))
        {
            $rolesPermitidos[] = 'ROLE_227-1217'; //mostrar subscribers en el olt
        }
        if(true === $this->get('security.context')->isGranted('ROLE_151-1127'))
        {
            $rolesPermitidos[] = 'ROLE_151-1127'; //Administrar pool
        }
        if(true === $this->get('security.context')->isGranted('ROLE_227-837'))
        {
            $rolesPermitidos[] = 'ROLE_227-837'; //Eliminar Ip
        }
        if(true === $this->get('security.context')->isGranted('ROLE_227-1877'))
        {
            $rolesPermitidos[] = 'ROLE_227-1877'; //Actualizar Pool Ip
        }
        if(true === $this->get('security.context')->isGranted('ROLE_227-2237'))
        {
            $rolesPermitidos[] = 'ROLE_227-2237'; //Administrar Scopes
        }
        if(true === $this->get('security.context')->isGranted('ROLE_227-2537'))
        {
            $rolesPermitidos[] = 'ROLE_227-2537'; //Reservar Ips de Olt Migracion
        }
        if(true === $this->get('security.context')->isGranted('ROLE_227-2457'))
        {
            $rolesPermitidos[] = 'ROLE_227-2457'; //Actualizar Caracteristicas Huawei
        }
        if(true === $this->get('security.context')->isGranted('ROLE_227-2777'))
        {
            $rolesPermitidos[] = 'ROLE_227-2777'; //Dejar Sin Operatividad
        }
        if(true === $this->get('security.context')->isGranted('ROLE_227-2877'))
        {
            $rolesPermitidos[] = 'ROLE_227-2877'; //Administrar Tarjeta Olt
        }
        if(true === $this->get('security.context')->isGranted('ROLE_227-3177'))
        {
            $rolesPermitidos[] = 'ROLE_227-3177'; //Aprobar Olt aprovisione en CNR
        }
        if(true === $this->get('security.context')->isGranted('ROLE_227-3497'))
        {
            $rolesPermitidos[] = 'ROLE_227-3497'; //Monitorear Cambio de Plan Masivo
        }
        if(true === $this->get('security.context')->isGranted('ROLE_227-3498'))
        {
            $rolesPermitidos[] = 'ROLE_227-3498'; //Reverso completo de olt migrado a nuevos planes ultravelocidad
        }
        if(true === $this->get('security.context')->isGranted('ROLE_227-3197'))
        {
            $rolesPermitidos[] = 'ROLE_227-3197'; //Configurar Ip CNR Manual
        }
        if(true === $this->get('security.context')->isGranted('ROLE_227-6837'))
        {
            //Generar Migración
            $rolesPermitidos[] = 'ROLE_227-6837'; 
        }
        if(true === $this->get('security.context')->isGranted('ROLE_227-7317'))
        {
            $rolesPermitidos[] = 'ROLE_227-7317'; //Generar Solicitud de agregar equipos masivos
        }
        if(true === $this->get('security.context')->isGranted('ROLE_227-7977'))
        {
            $rolesPermitidos[] = 'ROLE_227-7977'; //Subida masiva policy/scopes
        }
        if(true === $this->get('security.context')->isGranted('ROLE_227-8017'))
        {
            //Vista de olt multiplataforma
            $rolesPermitidos[] = 'ROLE_227-8017';
        }
        if(true === $this->get('security.context')->isGranted('ROLE_227-8018'))
        {
            //Asociacion de olt y nodos
            $rolesPermitidos[] = 'ROLE_227-8018';
        }
        if(true === $this->get('security.context')->isGranted('ROLE_227-8037'))
        {
            //Asignar recursos de olt
            $rolesPermitidos[] = 'ROLE_227-8037';
        }
        if(true === $this->get('security.context')->isGranted('ROLE_227-8038'))
        {
            //Configuración del olt
            $rolesPermitidos[] = 'ROLE_227-8038';
        }
        if(true === $this->get('security.context')->isGranted('ROLE_227-8019'))
        {
            //Reverso de recursos de olt multiplataforma
            $rolesPermitidos[] = 'ROLE_227-8019';
        }
       

        return $this->render('tecnicoBundle:InfoElementoOlt:index.html.twig', array(
                'rolesPermitidos' => $rolesPermitidos,
                'idEmpresa'       => $strIdEmpresa
        ));
    }
    
    public function newOltAction(){
        $entity = new InfoElemento();
        $form   = $this->createForm(new InfoElementoOltType(), $entity);

        return $this->render('tecnicoBundle:InfoElementoOlt:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }
    
    /*
     * 
     * Documentación para el método 'createOltAction'.
     *
     * Metodo utilizado para crear el nuevo Olt
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.5 17-09-2018 - Se agrega la validación de las coordenadas de latitud y longitud de acuerdo al país en sesión
     * 
     * @author Modificado: Jesus Bozada <jbozada@telconet.ec>
     * @version 2.4 29-07-2018 - Se modifica la creación de banderas en OLT's por proyecto ZTE
     * @since 2.3
     * 
     * @author Modificado: Jesus Bozada <jbozada@telconet.ec>
     * @version 2.3 05-12-2016 - Se guarda bandera que marca al Elemento OLT TELLION para trabajar con nuevos perfiles Ultra Velocidad
     *
     * @author Modificado: Jesus Bozada <jbozada@telconet.ec>
     * @version 2.2 30-03-2016 - Se guarda valor de bandera que marca al Elemento OLT para trabajar con nuevos perfiles Ultra Velocidad
     * 
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 2.1 11-11-2015 - Se guarda el valor del 'Aprovisionamiento_IP' en la tabla 'INFO_DETALLE_ELEMENTO'
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 2.0 25-02-2015
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 2.1 12-08-2021 - Se parametriza el tiempo máximo de ejecución, para prevenir que el servidor se bloquee
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 2.2 10-09-2021 - Se realiza la actualización de las características del Olt por solicitud de proceso masivo
     * 
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 2.3 15-06-2022 - Se anexa nuevo proceso para activar las promociones en el Olt si aplica, sin afectar la creacion
     * 
     */
    public function createOltAction()
    {
        $peticion               = $this->get('request');
        $session                = $peticion->getSession();
        $em                     = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emGeneral              = $this->getDoctrine()->getManager('telconet_general');
        $elementoOlt            = new InfoElemento();
        $form                   = $this->createForm(new InfoElementoOltType(), $elementoOlt);
        $parametros             = $peticion->request->get('telconet_schemabundle_infoelementoolttype');
        $nombreElemento         = $parametros['nombreElemento'];
        $ipOlt                  = $parametros['ipElemento'];
        $modeloElementoId       = $parametros['modeloElementoId'];
        $nodoElementoId         = $parametros['nodoElementoId'];
        $intRackElementoId      = $parametros['rackElementoId'];
        $intUnidadRack          = $parametros['unidadRack'];
        $descripcionElemento    = $parametros['descripcionElemento'];
        $strUnidadesOcupadas    = "";
        $intUnidadMaximaU       = 0;
        $arrayMaxTiempoEjecucion = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->getOne('CONTROL_PROCESOS_MAXIMO_TIEMPO_EJECUCION',
                                                                                        'TECNICO',
                                                                                        '',
                                                                                        '',
                                                                                        'createOltAction',
                                                                                        '',
                                                                                        '',
                                                                                        '',
                                                                                        '');
        if(isset($arrayMaxTiempoEjecucion) && !empty($arrayMaxTiempoEjecucion) &&
           isset($arrayMaxTiempoEjecucion['valor2']) && !empty($arrayMaxTiempoEjecucion['valor2']) &&
           intval($arrayMaxTiempoEjecucion['valor2']) > 0)
        {
            ini_set('max_execution_time', intval($arrayMaxTiempoEjecucion['valor2']));
        }

        $strAprovisionamientoIp = $peticion->request->get('aprovisionamiento') ? $peticion->request->get('aprovisionamiento') : '';
        
        $em->beginTransaction();
        
        try
        {
            $form->bind($peticion);
            
            if ($form->isValid()) 
            {
                //verificar que no se repita la ip
                $ipRepetida = $em->getRepository('schemaBundle:InfoIp')->findOneBy(array("ip" => $ipOlt, "estado" => "Activo"));
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
                
                if ( $modeloElemento->getNombreModeloElemento() != 'EP-3116' && $intRackElementoId == "")
                {
                    throw new \Exception('Para el modelo '.$modeloElemento->getNombreModeloElemento()
                                         .' es obligatorio ingresar la posicion del rack!');
                }

                $elementoOlt->setNombreElemento($nombreElemento);
                $elementoOlt->setDescripcionElemento($descripcionElemento);
                $elementoOlt->setModeloElementoId($modeloElemento);
                $elementoOlt->setUsrResponsable($session->get('user'));
                $elementoOlt->setUsrCreacion($session->get('user'));
                $elementoOlt->setFeCreacion(new \DateTime('now'));
                $elementoOlt->setIpCreacion($peticion->getClientIp());
                $elementoOlt->setEstado("Activo");
                $em->persist($elementoOlt);
                $em->flush();
                
                
                //Se guarda el valor del Tipo de Aprovisionamiento IP del Elemento OLT
                $objInfoDetalleElemento = new InfoDetalleElemento();
                $objInfoDetalleElemento->setElementoId($elementoOlt->getId());
                $objInfoDetalleElemento->setDetalleNombre(self::DETALLE_ELEMENTO_APROVISIONAMIENTO);
                $objInfoDetalleElemento->setDetalleValor($strAprovisionamientoIp);
                $objInfoDetalleElemento->setDetalleDescripcion(self::DETALLE_ELEMENTO_APROVISIONAMIENTO);
                $objInfoDetalleElemento->setFeCreacion(new \DateTime('now'));
                $objInfoDetalleElemento->setUsrCreacion($session->get('user'));
                $objInfoDetalleElemento->setIpCreacion($peticion->getClientIp());
                $objInfoDetalleElemento->setEstado(self::ESTADO_ACTIVO);
                $em->persist($objInfoDetalleElemento);
                $em->flush();
                // Fin Se guarda el valor del Tipo de Aprovisionamiento IP del Elemento OLT
                
                //Se guarda valor de bandera que marca al Elemento OLT para trabajar con nuevos perfiles Ultra Velocidad
                $objInfoDetalleElemento = new InfoDetalleElemento();
                $objInfoDetalleElemento->setElementoId($elementoOlt->getId());
                $objInfoDetalleElemento->setDetalleNombre(self::OLT_MIGRADO_CNR);
                $objInfoDetalleElemento->setDetalleValor(self::VALOR_SI);
                $objInfoDetalleElemento->setDetalleDescripcion(self::OLT_MIGRADO_CNR);
                $objInfoDetalleElemento->setFeCreacion(new \DateTime('now'));
                $objInfoDetalleElemento->setUsrCreacion($session->get('user'));
                $objInfoDetalleElemento->setIpCreacion($peticion->getClientIp());
                $objInfoDetalleElemento->setEstado(self::ESTADO_ACTIVO);
                $em->persist($objInfoDetalleElemento);
                $em->flush();
                // Fin Se guarda valor de bandera que marca al Elemento OLT para trabajar con nuevos perfiles Ultra Velocidad

                //Se guarda valor de bandera que marca al Elemento OLT para trabajar con Middleware
                $objInfoDetalleElemento = new InfoDetalleElemento();
                $objInfoDetalleElemento->setElementoId($elementoOlt->getId());
                $objInfoDetalleElemento->setDetalleNombre(self::DETALLE_ELEMENTO_MIDDLEWARE);
                $objInfoDetalleElemento->setDetalleValor(self::VALOR_SI);
                $objInfoDetalleElemento->setDetalleDescripcion(self::DETALLE_ELEMENTO_MIDDLEWARE);
                $objInfoDetalleElemento->setFeCreacion(new \DateTime('now'));
                $objInfoDetalleElemento->setUsrCreacion($session->get('user'));
                $objInfoDetalleElemento->setIpCreacion($peticion->getClientIp());
                $objInfoDetalleElemento->setEstado(self::ESTADO_ACTIVO);
                $em->persist($objInfoDetalleElemento);
                $em->flush();
                // Fin Se guarda valor de bandera que marca al Elemento OLT para trabajar con Middleware
                
                //buscar el interface Modelo
                $interfaceModelo = $em->getRepository('schemaBundle:AdmiInterfaceModelo')->findBy(array("modeloElementoId" => $modeloElementoId));
                foreach($interfaceModelo as $im)
                {
                    $cantidadInterfaces = $im->getCantidadInterface();
                    $formato            = $im->getFormatoInterface();

                    if ($modeloElemento->getMarcaElementoId()->getNombreMarcaElemento() == 'HUAWEI')
                    {
                        $start  = 0;
                        $fin    = $cantidadInterfaces-1;
                    }
                    else
                    {
                        $start  = 1;
                        $fin    = $cantidadInterfaces;
                    }
                    
                    for($i = $start; $i <= $fin; $i++)
                    {
                        $interfaceElemento          = new InfoInterfaceElemento();
                        $format                     = explode("?", $formato);
                        $nombreInterfaceElemento    = $format[0] . $i;
                        $interfaceElemento->setNombreInterfaceElemento($nombreInterfaceElemento);
                        $interfaceElemento->setElementoId($elementoOlt);
                        $interfaceElemento->setEstado("not connect");
                        $interfaceElemento->setUsrCreacion($session->get('user'));
                        $interfaceElemento->setFeCreacion(new \DateTime('now'));
                        $interfaceElemento->setIpCreacion($peticion->getClientIp());

                        $em->persist($interfaceElemento);
                    }
                }

                //se valida si el elemento contenedor es Rack y se asignan recursos de unidades
                if($intRackElementoId != '')
                {
                    $objElementoRack            = $em->getRepository('schemaBundle:InfoElemento')->find($intRackElementoId);
                    $objInterfaceModeloRack     = $em->getRepository('schemaBundle:AdmiInterfaceModelo')
                                                     ->findOneBy(array("modeloElementoId" => $objElementoRack->getModeloElementoId()));
                    $objElementoUnidadRack      = $em->getRepository('schemaBundle:InfoElemento')->find($intUnidadRack);
                    $intUnidadMaximaU           = (int) $objElementoUnidadRack->getNombreElemento() + 
                                                  (int) $modeloElemento->getURack() - 1;
                    if($intUnidadMaximaU > $objInterfaceModeloRack->getCantidadInterface())
                    {
                        throw new \Exception('No se puede ubicar el Olt en el Rack porque se sobrepasa el tamaño de unidades!');
                    }
                    //obtener todas las unidades del rack
                    $objRelacionesElementoUDRack = $em->getRepository('schemaBundle:InfoRelacionElemento')
                                                       ->findBy(array("elementoIdA"              => $intRackElementoId,
                                                                         "estado"                   => "Activo"
                                                                        )
                                                                  );
                    //se verifica disponibilidad de unidades y se asignan recursos
                    for($t = (int)$objElementoUnidadRack->getNombreElemento(); $t <= $intUnidadMaximaU; $t++)
                    {
                        $intElementoUnidadId     = 0;
                        $objRelacionElementoRack = null;
                        foreach($objRelacionesElementoUDRack as $objRelacionElementoUDRack)
                        {
                            $objElementoUnidadRackDet      = $em->getRepository('schemaBundle:InfoElemento')
                                                                ->find($objRelacionElementoUDRack->getElementoIdB());
                            if ((int)$objElementoUnidadRackDet->getNombreElemento() == $t)
                            {
                                $intElementoUnidadId = $objElementoUnidadRackDet->getId();
                            }
                        }
                        $objRelacionElementoRack = $em->getRepository('schemaBundle:InfoRelacionElemento')
                                                      ->findOneBy(array("elementoIdA"             => $intElementoUnidadId,
                                                                        "estado"                  => "Activo"
                                                                       )
                                                                 );
                        if($objRelacionElementoRack)
                        {
                            if ($strUnidadesOcupadas == "")
                            {
                                $strUnidadesOcupadas = $t;
                            }
                            else
                            {
                                $strUnidadesOcupadas = $strUnidadesOcupadas . " , " . $t;
                            }
                        }
                        else
                        {
                            //relacion elemento
                            $objRelacionElemento = new InfoRelacionElemento();
                            $objRelacionElemento->setElementoIdA($intElementoUnidadId);
                            $objRelacionElemento->setElementoIdB($elementoOlt->getId());
                            $objRelacionElemento->setTipoRelacion("CONTIENE");
                            $objRelacionElemento->setObservacion("Rack contiene Olt");
                            $objRelacionElemento->setEstado("Activo");
                            $objRelacionElemento->setUsrCreacion($session->get('user'));
                            $objRelacionElemento->setFeCreacion(new \DateTime('now'));
                            $objRelacionElemento->setIpCreacion($peticion->getClientIp());
                            $em->persist($objRelacionElemento);
                        }
                    }
                    if($strUnidadesOcupadas != "")
                    {
                        throw new \Exception('No se puede ubicar el Olt en el Rack porque estan ocupadas las unidades : ' . $strUnidadesOcupadas);
                    }
                }
                else
                {
                    //relacion elemento
                    $relacionElemento = new InfoRelacionElemento();
                    $relacionElemento->setElementoIdA($nodoElementoId);
                    $relacionElemento->setElementoIdB($elementoOlt->getId());
                    $relacionElemento->setTipoRelacion("CONTIENE");
                    $relacionElemento->setObservacion("Nodo contiene Olt");
                    $relacionElemento->setEstado("Activo");
                    $relacionElemento->setUsrCreacion($session->get('user'));
                    $relacionElemento->setFeCreacion(new \DateTime('now'));
                    $relacionElemento->setIpCreacion($peticion->getClientIp());
                    $em->persist($relacionElemento);
                }


                //ip elemento
                $ipElemento = new InfoIp();
                $ipElemento->setElementoId($elementoOlt->getId());
                $ipElemento->setIp(trim($ipOlt));
                $ipElemento->setVersionIp("IPV4");
                $ipElemento->setEstado("Activo");
                $ipElemento->setUsrCreacion($session->get('user'));
                $ipElemento->setFeCreacion(new \DateTime('now'));
                $ipElemento->setIpCreacion($peticion->getClientIp());
                $em->persist($ipElemento);

                //historial elemento
                $historialElemento = new InfoHistorialElemento();
                $historialElemento->setElementoId($elementoOlt);
                $historialElemento->setEstadoElemento("Activo");
                $historialElemento->setObservacion("Se ingreso un Olt");
                $historialElemento->setUsrCreacion($session->get('user'));
                $historialElemento->setFeCreacion(new \DateTime('now'));
                $historialElemento->setIpCreacion($peticion->getClientIp());
                $em->persist($historialElemento);

                //tomar datos nodo
                $nodoEmpresaElementoUbicacion = $em->getRepository('schemaBundle:InfoEmpresaElementoUbica')
                                                  ->findOneBy(array("elementoId" => $nodoElementoId));
                $nodoUbicacion = $em->getRepository('schemaBundle:InfoUbicacion')->find($nodoEmpresaElementoUbicacion->getUbicacionId()->getId());
                
                $serviceInfoElemento        = $this->get("tecnico.InfoElemento");
                $arrayRespuestaCoordenadas  = $serviceInfoElemento->validarLimitesCoordenadasElemento(array("latitudElemento"   => 
                                                                                                            $nodoUbicacion->getLatitudUbicacion(),
                                                                                                            "longitudElemento"  => 
                                                                                                            $nodoUbicacion->getLongitudUbicacion(),
                                                                                                            "msjTipoElemento"   => "del nodo ",
                                                                                                            "msjTipoElementoPadre"  =>
                                                                                                            "que contiene al olt ",
                                                                                                            "msjAdicional"          => 
                                                                                                            "por favor regularizar en la "
                                                                                                            ."administración de Nodos"
                                                                                                     ));
                if($arrayRespuestaCoordenadas["status"] === "ERROR")
                {
                    throw new \Exception($arrayRespuestaCoordenadas['mensaje']);
                }
                //info ubicacion
                $parroquia = $em->find('schemaBundle:AdmiParroquia', $nodoUbicacion->getParroquiaId());
                $ubicacionElemento = new InfoUbicacion();
                $ubicacionElemento->setLatitudUbicacion($nodoUbicacion->getLatitudUbicacion());
                $ubicacionElemento->setLongitudUbicacion($nodoUbicacion->getLongitudUbicacion());
                $ubicacionElemento->setDireccionUbicacion($nodoUbicacion->getDireccionUbicacion());
                $ubicacionElemento->setAlturaSnm($nodoUbicacion->getAlturaSnm());
                $ubicacionElemento->setParroquiaId($parroquia);
                $ubicacionElemento->setUsrCreacion($session->get('user'));
                $ubicacionElemento->setFeCreacion(new \DateTime('now'));
                $ubicacionElemento->setIpCreacion($peticion->getClientIp());
                $em->persist($ubicacionElemento);

                //empresa elemento ubicacion
                $empresaElementoUbica = new InfoEmpresaElementoUbica();
                $empresaElementoUbica->setEmpresaCod($session->get('idEmpresa'));
                $empresaElementoUbica->setElementoId($elementoOlt);
                $empresaElementoUbica->setUbicacionId($ubicacionElemento);
                $empresaElementoUbica->setUsrCreacion($session->get('user'));
                $empresaElementoUbica->setFeCreacion(new \DateTime('now'));
                $empresaElementoUbica->setIpCreacion($peticion->getClientIp());
                $em->persist($empresaElementoUbica);

                //empresa elemento
                $empresaElemento = new InfoEmpresaElemento();
                $empresaElemento->setElementoId($elementoOlt);
                $empresaElemento->setEmpresaCod($session->get('idEmpresa'));
                $empresaElemento->setEstado("Activo");
                $empresaElemento->setUsrCreacion($session->get('user'));
                $empresaElemento->setIpCreacion($peticion->getClientIp());
                $empresaElemento->setFeCreacion(new \DateTime('now'));
                $em->persist($empresaElemento);

                $em->flush();
                $em->commit();
                
                // Validamos si la jurisdiccion del nuevo Olt posee una promo activa
                $arrayParametrosOltNuevo = array(
                    "strNombreOlt"   => $nombreElemento,
                    "strIpOlt"       => $ipOlt,
                    "strModeloOlt"   => $modeloElemento->getNombreModeloElemento(),
                    "strUsrSesion"   => $session->get('user'),
                    "strIpClient"    => $peticion->getClientIp(),
                    "intIdEmpresa"   => $session->get('idEmpresa'),
                    "strTipoEmpresa" => $session->get('prefijoEmpresa'),
                    "strTecnologia"  => $modeloElemento->getMarcaElementoId()->getNombreMarcaElemento(),
                    "intIdParroquia" => $parroquia->getId()
                );
                $serviceInfoElemento->validarJurisdiccionElementoOlt($arrayParametrosOltNuevo);
                
                //ejecucion de scripts para obtener caracteristicas del olt
                $mensaje = "";
                if ( $elementoOlt->getModeloElementoId()->getNombreModeloElemento() == 'EP-3116')
                {
                    /* @var $script InfoServicioTecnicoService */
                    $script = $this->get('tecnico.InfoServicioTecnico');
                    $scriptArray = $script->obtenerArregloScript("mostrarPerfilesOlt", $elementoOlt->getModeloElementoId());
                    $idDocumento = $scriptArray[0]->idDocumento;

                    //se recupera parametro para ejecucion de jar 
                    $parametroPathTelcos    = $this->container->getParameter('path_telcos');
                    //perfiles
                    $comando = "java -jar -Djava.security.egd=file:/dev/./urandom ".$parametroPathTelcos."telcos/src/telconet/tecnicoBundle/batch/md_datos.jar '" . 
                               $this->container->getParameter('host') . "' 'obtenerPerfiles' '" . 
                               $elementoOlt->getId() . "' 'usuario' 'puerto' '" . $idDocumento . "' 'datos'";
                    $salida = shell_exec($comando);
                    $pos = strpos($salida, "{");
                    $jsonObj = substr($salida, $pos);
                    $resultadJson = json_decode($jsonObj);

                    //pools
                    if($resultadJson->status == "OK")
                    {
                        $mensaje = "Se grabaron los Perfiles.";
                        
                        $comandoPool = "nohup java -jar -Djava.security.egd=file:/dev/./urandom ".$parametroPathTelcos."telcos/src/telconet/tecnicoBundle/batch/md_datos.jar '" . 
                                       $this->container->getParameter('host') . "' 'obtenerPools' '" . $elementoOlt->getId() . 
                                       "' 'usuario' 'puerto' '0' 'datos' &";
                        $salida = shell_exec($comandoPool);
                    }
                    else
                    {
                        $mensaje = "No se pudieron obtener los Perfiles.";
                    }
                }
                else if ($modeloElemento->getMarcaElementoId()->getNombreMarcaElemento() == 'HUAWEI')
                {
                    $serviceElemento = $this->get('tecnico.InfoElemento');
                    $serviceServicioTecnico = $this->get('tecnico.InfoServicioTecnico');
                    $arrayParametros = array(
                        "serviceServicioTecnico" => $serviceServicioTecnico,
                        "objElementoOlt"    => $elementoOlt,
                        "strIdEmpresa"      => $session->get('idEmpresa'),
                        "strUsrSesion"      => $session->get('user'),
                        "strIpClient"       => $peticion->getClientIp(),
                    );
                    //actualizar características
                    $arrayResultado = $serviceElemento->actualizarCaracteristicasOlt($arrayParametros);
                    $this->get('session')->getFlashBag()->add('notice', $arrayResultado['mensaje']);
                }
                if($mensaje != "")
                {
                    $this->get('session')->getFlashBag()->add('notice', $mensaje);
                }
                
                return $this->redirect($this->generateUrl('elementoolt_showOlt', array('id' => $elementoOlt->getId())));
            }
        }
        catch (\Exception $e) 
        {
            if ($em->getConnection()->isTransactionActive())
            {
                $em->rollback();
            }
            $em->close();
            $this->get('session')->getFlashBag()->add('notice', 
                                                      'Existieron problemas al procesar la transacción, favor notificar a Sistemas. '
                                                      ."Error: ".$e->getMessage());
            return $this->render('tecnicoBundle:InfoElementoOlt:new.html.twig', array(
                                                                                        'entity' => $elementoOlt,
                                                                                        'form'   => $form->createView()
                                ));
        }
    }
    
    /*
     * 
     * Documentación para el método 'editOltAction'.
     *
     * Metodo utilizado para redireccionar a pagina de edición de Olt
     *
     * @param $integer $id
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 25-02-2015
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.1 23-03-2021 - Se verifica si el olt es multiplataforma
     */
    public function editOltAction($id)
    {
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");
        $emGeneral = $this->getDoctrine()->getManager('telconet_general');

        if(null == $elemento = $em->find('schemaBundle:InfoElemento', $id))
        {
            throw new NotFoundHttpException('No existe el elemento -dslam- que se quiere modificar');
        }
        else
        {
            $ipElemento             = $em->getRepository('schemaBundle:InfoIp')
                                         ->findOneBy(array("elementoId" => $elemento->getId(),
                                                           "versionIp" => "IPV4"));
            $objElementoPadreOlt    = $em->getRepository('schemaBundle:InfoRelacionElemento')
                                         ->findOneBy(array("elementoIdB" => $elemento->getId(),"estado" => "Activo"));
            $elementoUbica          = $em->getRepository('schemaBundle:InfoEmpresaElementoUbica')
                                         ->findOneBy(array("elementoId" => $elemento->getId()));
            $ubicacion              = $em->getRepository('schemaBundle:InfoUbicacion')
                                         ->findOneBy(array("id" => $elementoUbica->getUbicacionId()));
            $parroquia              = $em->getRepository('schemaBundle:AdmiParroquia')
                                         ->findOneBy(array("id" => $ubicacion->getParroquiaId()));
            $canton                 = $em->getRepository('schemaBundle:AdmiCanton')
                                         ->findOneBy(array("id" => $parroquia->getCantonId()));
            $cantonJurisdiccion     = $em->getRepository('schemaBundle:AdmiCantonJurisdiccion')
                                         ->findOneBy(array("cantonId" => $canton->getId()));
            //se obtiene información de elementos contenedores de Olt para presentar en pantalla
            $objElementoPadre       = $em->find('schemaBundle:InfoElemento', $objElementoPadreOlt->getElementoIdA());
            $objModeloElementoPadre = $em->find('schemaBundle:AdmiModeloElemento', $objElementoPadre->getModeloElementoId());
            $objTipoElementoPadre   = $em->find('schemaBundle:AdmiTipoElemento', $objModeloElementoPadre->getTipoElementoId());
            $elemento->setIpElemento($ipElemento->getIp());
            if($objTipoElementoPadre->getNombreTipoElemento() == "UDRACK")
            {
                //se obtiene unidad donde esta ubicado el OLT
                $objRelacionElementoRack = $em->getRepository('schemaBundle:InfoRelacionElemento')
                                              ->findOneBy(array("elementoIdB" => $objElementoPadre->getId(),"estado" => "Activo"));
                $objRelacionElementoNodo  = $em->getRepository('schemaBundle:InfoRelacionElemento')
                                              ->findOneBy(array("elementoIdB" => $objRelacionElementoRack->getElementoIdA(),"estado" => "Activo"));
                $elemento->setNodoElementoId($objRelacionElementoNodo->getElementoIdA());
                $elemento->setRackElementoId($objRelacionElementoRack->getElementoIdA());
                $elemento->setUnidadRack($objElementoPadre->getId());
            }
            else
            {
                $elemento->setNodoElementoId($objElementoPadreOlt->getElementoIdA());
            }
            //seteo las variables
            $strDetalleMulti         = self::DETALLE_ELEMENTO_MULTIPLATAFORMA;
            $arrayParametrosDetMulti = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->getOne('NUEVA_RED_GPON_TN',
                                                                                            'COMERCIAL',
                                                                                            '',
                                                                                            'NOMBRES PARAMETROS DETALLES MULTIPLATAFORMA',
                                                                                            '',
                                                                                            '',
                                                                                            '',
                                                                                            '',
                                                                                            '');
            if(isset($arrayParametrosDetMulti) && !empty($arrayParametrosDetMulti)
               && isset($arrayParametrosDetMulti['valor1']) && !empty($arrayParametrosDetMulti['valor1']))
            {
                $strDetalleMulti = $arrayParametrosDetMulti['valor1'];
            }
            //verificar si es multiplataforma
            $strOltMultiplataforma  = "NO";
            $objDetMultiplataforma  = $em->getRepository('schemaBundle:InfoDetalleElemento')
                                           ->findOneBy(array("elementoId"    => $elemento->getId(),
                                                             "detalleNombre" => $strDetalleMulti,
                                                             "estado"        => "Activo"));
            if(is_object($objDetMultiplataforma))
            {
                $strOltMultiplataforma = $objDetMultiplataforma->getDetalleValor();
            }
        }

        $formulario =$this->createForm(new InfoElementoOltType(), $elemento);
        
        return $this->render(   'tecnicoBundle:InfoElementoOlt:edit.html.twig', array(
                                'edit_form'             => $formulario->createView(),
                                'olt'                   => $elemento,
                                'ipElemento'            => $ipElemento,
                                'ubicacion'             => $ubicacion,
                                'cantonJurisdiccion'    => $cantonJurisdiccion,
                                'nodoElemento'          => $objElementoPadreOlt,
                                'strOltMultiplataforma' => $strOltMultiplataforma
                                )
                            );
    }
    
    /*
     * 
     * Documentación para el método 'updateOltAction'.
     *
     * Metodo utilizado para actualizar Olt
     *
     * @param $integer $id
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 2.0 25-02-2015
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.1 17-09-2018 - Se agrega la validación de las coordenadas de latitud y longitud de acuerdo al país en sesión
     * 
     */
    public function updateOltAction($id)
    {
        $em         = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emC        = $this->getDoctrine()->getManager('telconet');
        $peticion   = $this->get('request');
        $session    = $peticion->getSession();
        $entity     = $em->getRepository('schemaBundle:InfoElemento')->find($id);

        if(!$entity)
        {
            throw $this->createNotFoundException('Unable to find InfoElemento entity.');
        }

        $request                = $this->get('request');
        $parametros             = $request->request->get('telconet_schemabundle_infoelementoolttype');
        $nombreElemento         = $parametros['nombreElemento'];
        $popElementoId          = $parametros['nodoElementoId'];
        $descripcionElemento    = $parametros['descripcionElemento'];
        $modeloElementoId       = $parametros['modeloElementoId'];
        $intRackElementoId      = $parametros['rackElementoId'];
        $intUnidadRack          = $parametros['unidadRack'];
        $ipElementoId           = $request->request->get('idIpElemento');
        $ipElemento             = $parametros['ipElemento'];
        $idUbicacion            = $request->request->get('idUbicacion');
        $modeloElemento         = $em->find('schemaBundle:AdmiModeloElemento', $modeloElementoId);
        $intNodoElementoAntes   = "";
        $intRackElementoAntes   = "";
        $intUnidadRackAntes     = "";
        $intContadorUnidad      = 0 ;
        $boolMensajeUsuario     = false;

        //revisar si es cambio de modelo
        $modeloAnterior = $entity->getModeloElementoId();

        $em->beginTransaction();
        try
		{
            if ( $modeloElemento->getNombreModeloElemento() != 'EP-3116' && $intRackElementoId == "")
            {
                $boolMensajeUsuario = true;
                throw new \Exception('Para el modelo '.$modeloElemento->getNombreModeloElemento().' es obligatorio ingresar la posicion del rack!');
            }
            $flag = 0;
            if($modeloAnterior->getId() != $modeloElemento->getId())
            {
                $interfaceModeloAnterior = $em->getRepository('schemaBundle:AdmiInterfaceModelo')
                                              ->findOneBy(array("modeloElementoId" => $modeloAnterior->getId()));
                $interfaceModeloNuevo    = $em->getRepository('schemaBundle:AdmiInterfaceModelo')
                                              ->findOneBy(array("modeloElementoId" => $modeloElemento->getId()));

                $cantAnterior            = $interfaceModeloAnterior->getCantidadInterface();
                $cantNueva               = $interfaceModeloNuevo->getCantidadInterface();

                $formatoAnterior         = $interfaceModeloAnterior->getFormatoInterface();
                $formatoNuevo            = $interfaceModeloNuevo->getFormatoInterface();

                if($cantAnterior == $cantNueva)
                {
                    //solo cambiar formato
                    for($i = 1; $i <= $cantAnterior; $i++)
                    {
                        $format                          = explode("?", $formatoAnterior);
                        $nombreInterfaceElementoAnterior = $format[0] . $i;

                        $interfaceElemento = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                                ->findBy(array("elementoId" => $entity->getId(), 
                                                               "nombreInterfaceElemento" => $nombreInterfaceElementoAnterior));

                        for($j = 0; $j < count($interfaceElemento); $j++)
                        {
                            $interface = $interfaceElemento[$j];

                            if($interface->getEstado() != "deleted")
                            {
                                $formatN                      = explode("?", $formatoNuevo);
                                $nombreInterfaceElementoNuevo = $formatN[0] . $i;

                                $interface->setNombreInterfaceElemento($nombreInterfaceElementoNuevo);
                                $interface->setUsrCreacion($session->get('user'));
                                $interface->setFeCreacion(new \DateTime('now'));
                                $interface->setIpCreacion($peticion->getClientIp());
                                $em->persist($interface);
                            }
                        }
                    }
                }
                else if($cantAnterior > $cantNueva)
                {
                    //revisar puertos restantes si tienen servicio
                    for($i = ($cantNueva + 1); $i <= $cantAnterior; $i++)
                    {
                        $format                          = explode("?", $formatoAnterior);
                        $nombreInterfaceElementoAnterior = $format[0] . $i;

                        $interfaceElemento = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                                ->findBy(array("elementoId" => $entity->getId(), 
                                                               "nombreInterfaceElemento" => $nombreInterfaceElementoAnterior));

                        for($j = 0; $j < count($interfaceElemento); $j++)
                        {
                            $interface = $interfaceElemento[$j];

                            $servicioTecnico = $emC->getRepository('schemaBundle:InfoServicioTecnico')
                                                   ->findOneBy(array("interfaceElementoId" => $interface->getId()));

                            if($servicioTecnico != null || $servicioTecnico != "")
                            {
                                $servicio = $servicioTecnico->getServicioId();

                                if($servicio->getEstado() != "Cancel" || $servicio->getEstado() != "Cancel-SinEje")
                                {
                                    $flag = 1;
                                    break;
                                }
                            }
                        }

                        if($flag == 1)
                        {
                            break;
                        }
                    }

                    if($flag == 0)
                    {
                        //actualizar las interfaces
                        for($i = 1; $i <= $cantNueva; $i++)
                        {
                            $format                          = explode("?", $formatoAnterior);
                            $nombreInterfaceElementoAnterior = $format[0] . $i;

                            $interfaceElemento = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                                    ->findBy(array("elementoId" => $entity->getId(), 
                                                                   "nombreInterfaceElemento" => $nombreInterfaceElementoAnterior));

                            for($j = 0; $j < count($interfaceElemento); $j++)
                            {
                                $interface = $interfaceElemento[$j];

                                if($interface->getEstado() != "deleted")
                                {
                                    $formatN                      = explode("?", $formatoNuevo);
                                    $nombreInterfaceElementoNuevo = $formatN[0] . $i;

                                    $interface->setNombreInterfaceElemento($nombreInterfaceElementoNuevo);
                                    $interface->setUsrCreacion($session->get('user'));
                                    $interface->setFeCreacion(new \DateTime('now'));
                                    $interface->setIpCreacion($peticion->getClientIp());
                                    $em->persist($interface);
                                }
                            }
                        }//fin de for
                        //cambiar estado a eliminado
                        for($i = ($cantNueva + 1); $i <= $cantAnterior; $i++)
                        {
                            $format                          = explode("?", $formatoAnterior);
                            $nombreInterfaceElementoAnterior = $format[0] . $i;

                            $interfaceElemento = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                                    ->findBy(array("elementoId" => $entity->getId(), 
                                                                   "nombreInterfaceElemento" => $nombreInterfaceElementoAnterior));

                            for($j = 0; $j < count($interfaceElemento); $j++)
                            {
                                $interface = $interfaceElemento[$j];

                                if($interface->getEstado() != "deleted")
                                {
                                    $formatN                      = explode("?", $formatoNuevo);
                                    $nombreInterfaceElementoNuevo = $formatN[0] . $i;

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
                else if($cantAnterior < $cantNueva)
                {
                    //actualizar las interfaces
                    for($i = 1; $i <= $cantAnterior; $i++)
                    {
                        $format                          = explode("?", $formatoAnterior);
                        $nombreInterfaceElementoAnterior = $format[0] . $i;

                        $interfaceElemento = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                                ->findBy(array("elementoId" => $entity->getId(), 
                                                               "nombreInterfaceElemento" => $nombreInterfaceElementoAnterior));

                        for($j = 0; $j < count($interfaceElemento); $j++)
                        {
                            $interface = $interfaceElemento[$j];

                            if($interface->getEstado() != "deleted")
                            {
                                $formatN                      = explode("?", $formatoNuevo);
                                $nombreInterfaceElementoNuevo = $formatN[0] . $i;

                                $interface->setNombreInterfaceElemento($nombreInterfaceElementoNuevo);
                                $interface->setUsrCreacion($session->get('user'));
                                $interface->setFeCreacion(new \DateTime('now'));
                                $interface->setIpCreacion($peticion->getClientIp());
                                $em->persist($interface);
                            }
                        }
                    }

                    //crear las nuevas interfaces
                    for($i = ($cantAnterior + 1); $i <= $cantNueva; $i++)
                    {
                        $interfaceElemento = new InfoInterfaceElemento();
                        $formatN           = explode("?", $formatoNuevo);
                        $nombreInterfaceElementoNuevo = $formatN[0] . $i;

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


            if($flag == 0)
            {
                //elemento
                $entity->setNombreElemento($nombreElemento);
                $entity->setDescripcionElemento($descripcionElemento);
                $entity->setModeloElementoId($modeloElemento);
                $entity->setUsrResponsable($session->get('user'));
                $entity->setUsrCreacion($session->get('user'));
                $entity->setFeCreacion(new \DateTime('now'));
                $entity->setIpCreacion($peticion->getClientIp());
                $em->persist($entity);

                //se verifica el elemento contedor antiguo del olt
                $objRelacionElemento       = $em->getRepository('schemaBundle:InfoRelacionElemento')
                                             ->findOneBy(array("elementoIdB" => $entity,"estado" => "Activo"));
                $objElementoPadre       = $em->find('schemaBundle:InfoElemento', $objRelacionElemento->getElementoIdA());
                $objModeloElementoPadre = $em->find('schemaBundle:AdmiModeloElemento', $objElementoPadre->getModeloElementoId());
                $objTipoElementoPadre   = $em->find('schemaBundle:AdmiTipoElemento', $objModeloElementoPadre->getTipoElementoId());
                if($objTipoElementoPadre->getNombreTipoElemento() == "UDRACK")
                {
                    //se obtiene unidad donde esta ubicado el OLT
                    $objRelacionElementoRack = $em->getRepository('schemaBundle:InfoRelacionElemento')
                                                  ->findOneBy(array("elementoIdB" => $objElementoPadre->getId(),"estado" => "Activo"));
                    $objRelacionElementoNodo  = $em->getRepository('schemaBundle:InfoRelacionElemento')
                                                   ->findOneBy(array("elementoIdB" => $objRelacionElementoRack->getElementoIdA(),
                                                                     "estado" => "Activo"));
                    $intNodoElementoAntes      = $objRelacionElementoNodo->getElementoIdA();
                    $intRackElementoAntes      = $objRelacionElementoRack->getElementoIdA();
                    $intUnidadRackAntes        = $objElementoPadre->getId();
                    
                }
                else
                {
                    $intNodoElementoAntes      = $objRelacionElemento->getElementoIdA();
                    $intRackElementoAntes      = "";
                    $intUnidadRackAntes        = "";
                }

                //se verifica si cambio el elemento contedor del Olt
                if ( $intNodoElementoAntes!= $popElementoId || 
                     $intRackElementoAntes!= $intRackElementoId || 
                     $intUnidadRackAntes!= $intUnidadRack )
                 {
                    //liberar recursos antiguos
                    if($objTipoElementoPadre->getNombreTipoElemento() == "UDRACK")
                    {
                        $objRelacionesElementoAntes     = $em->getRepository('schemaBundle:InfoRelacionElemento')
                                                             ->findBy(array("elementoIdB" => $entity,"estado" => "Activo"));
                        foreach($objRelacionesElementoAntes as $objRelacionElementoAntes)
                        {
                            $objRelacionElementoAntes->setEstado("Eliminado");
                            $em->persist($objRelacionElementoAntes);   
                            $em->flush();
                        }
                    }
                    else
                    {
                        $objRelacionElemento->setEstado("Eliminado");
                        $em->persist($objRelacionElemento);   
                        $em->flush();
                        
                    }
                    //si el nuevo elemeno contenedor es Rack se asignan nuevos recursos de unidades
                    if($intRackElementoId != "")
                    {
                        $objElementoRack            = $em->getRepository('schemaBundle:InfoElemento')->find($intRackElementoId);
                        $objInterfaceModeloRack     = $em->getRepository('schemaBundle:AdmiInterfaceModelo')
                                                         ->findOneBy(array("modeloElementoId" => $objElementoRack->getModeloElementoId()));
                        $objElementoUnidadRack      = $em->getRepository('schemaBundle:InfoElemento')->findOneBy(array("id" => $intUnidadRack));
                        $intUnidadMaximaU           = (int) $objElementoUnidadRack->getNombreElemento() + 
                                                      (int) $modeloElemento->getURack() - 1;
                        if($intUnidadMaximaU > $objInterfaceModeloRack->getCantidadInterface())
                        {
                            $boolMensajeUsuario = true;
                            throw new \Exception('No se puede ubicar el Olt en el Rack porque se sobrepasa el tamaño de unidades!');
                        }
                        //obtener todas las unidades del rack
                        $objRelacionesElementoUDRack = $em->getRepository('schemaBundle:InfoRelacionElemento')
                                                           ->findBy(array("elementoIdA"              => $intRackElementoId,
                                                                             "estado"                   => "Activo"
                                                                            )
                                                                      );
                        //se verifica disponibilidad de unidades y se asignan recursos
                        for($t = (int)$objElementoUnidadRack->getNombreElemento(); $t <= $intUnidadMaximaU; $t++)
                        {
                            $intElementoUnidadId     = 0;
                            $objRelacionElementoRack = null;
                            foreach($objRelacionesElementoUDRack as $objRelacionElementoUDRack)
                            {
                                $objElementoUnidadRackDet      = $em->getRepository('schemaBundle:InfoElemento')
                                                                    ->find($objRelacionElementoUDRack->getElementoIdB());
                                if ((int)$objElementoUnidadRackDet->getNombreElemento() == $t)
                                {
                                    $intElementoUnidadId = $objElementoUnidadRackDet->getId();
                                }
                            }
                            $objRelacionElementoRack = $em->getRepository('schemaBundle:InfoRelacionElemento')
                                                          ->findOneBy(array("elementoIdA"             => $intElementoUnidadId,
                                                                            "estado"                  => "Activo"
                                                                           )
                                                                     );
                            if($objRelacionElementoRack)
                            {
                                if ($strUnidadesOcupadas == "")
                                {
                                    $strUnidadesOcupadas = $t;
                                }
                                else
                                {
                                    $strUnidadesOcupadas = $strUnidadesOcupadas . " , " . $t;
                                }
                            }
                            else
                            {
                                //relacion elemento
                                $objRelacionElemento = new InfoRelacionElemento();
                                $objRelacionElemento->setElementoIdA($intElementoUnidadId);
                                $objRelacionElemento->setElementoIdB($entity->getId());
                                $objRelacionElemento->setTipoRelacion("CONTIENE");
                                $objRelacionElemento->setObservacion("Rack contiene Olt");
                                $objRelacionElemento->setEstado("Activo");
                                $objRelacionElemento->setUsrCreacion($session->get('user'));
                                $objRelacionElemento->setFeCreacion(new \DateTime('now'));
                                $objRelacionElemento->setIpCreacion($peticion->getClientIp());
                                $em->persist($objRelacionElemento);
                            }
                        }
                        if($strUnidadesOcupadas != "")
                        {
                            $boolMensajeUsuario = true;
                            throw new \Exception('No se puede ubicar el Olt en el Rack porque estan ocupadas las unidades : ' . $strUnidadesOcupadas);
                        }
                    }
                    else
                    {
                        //relacion elemento
                        $objRelacionElementoNueva = new InfoRelacionElemento();
                        $objRelacionElementoNueva->setElementoIdA($popElementoId);
                        $objRelacionElementoNueva->setElementoIdB($entity->getId());
                        $objRelacionElementoNueva->setTipoRelacion("CONTIENE");
                        $objRelacionElementoNueva->setObservacion("Nodo contiene Olt");
                        $objRelacionElementoNueva->setEstado("Activo");
                        $objRelacionElementoNueva->setUsrCreacion($session->get('user'));
                        $objRelacionElementoNueva->setFeCreacion(new \DateTime('now'));
                        $objRelacionElementoNueva->setIpCreacion($peticion->getClientIp());
                        $em->persist($objRelacionElementoNueva);
                    }
                    //se actualiza ubicación del Olt
                    $popEmpresaElementoUbicacion = $em->getRepository('schemaBundle:InfoEmpresaElementoUbica')
                                                      ->findOneBy(array("elementoId" => $popElementoId));
                    $popUbicacion                = $em->getRepository('schemaBundle:InfoUbicacion')
                                                      ->find($popEmpresaElementoUbicacion->getUbicacionId()->getId());
                    
                    $serviceInfoElemento        = $this->get("tecnico.InfoElemento");
                    $arrayRespuestaCoordenadas  = $serviceInfoElemento->validarLimitesCoordenadasElemento(array("latitudElemento"   => 
                                                                                                                $popUbicacion->getLatitudUbicacion(),
                                                                                                                "longitudElemento"  => 
                                                                                                                $popUbicacion->getLongitudUbicacion(),
                                                                                                                "msjTipoElemento"   => "del nodo ",
                                                                                                                "msjTipoElementoPadre"  =>
                                                                                                                "que contiene al olt ",
                                                                                                                "msjAdicional"          => 
                                                                                                                "por favor regularizar en la "
                                                                                                                ."administración de Nodos"
                                                                                                         ));
                    if($arrayRespuestaCoordenadas["status"] === "ERROR")
                    {
                        $boolMensajeUsuario = true;
                        throw new \Exception($arrayRespuestaCoordenadas['mensaje']);
                    }
                    //cambiar ubicacion del Olt
                    $parroquia = $em->find('schemaBundle:AdmiParroquia', $popUbicacion->getParroquiaId());
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
                $historialElemento->setObservacion("Se modifico el Olt");
                $historialElemento->setUsrCreacion($session->get('user'));
                $historialElemento->setFeCreacion(new \DateTime('now'));
                $historialElemento->setIpCreacion($peticion->getClientIp());
                $em->persist($historialElemento);

                $em->flush();
                $em->getConnection()->commit();

                return $this->redirect($this->generateUrl('elementoolt_showOlt', array('id' => $entity->getId())));
            }
            else
            {
                $em->getConnection()->rollback();
                $em->getConnection()->close();
                $this->get('session')->getFlashBag()->add('notice', 
                                                          'El elemento aun tiene servicios en puertos que ya no se van a usar, favor regularice!');
                return $this->redirect($this->generateUrl('elementoolt_editOlt', array('id' => $entity->getId())));
            }
        }
        catch (\Exception $e)
        {
            if($em->getConnection()->isTransactionActive())
            {
                $em->rollback();
            }
            $em->close();
            if($boolMensajeUsuario)
            {
                $strMensajeError = $e->getMessage();
            }
            else
            {
                $strMensajeError = 'Existieron problemas al procesar la transacción, favor notificar a Sistemas.';
            }
            error_log("Error: ".$e->getMessage());
            $this->get('session')->getFlashBag()->add('notice', $strMensajeError);
            return $this->redirect($this->generateUrl('elementoolt_editOlt', array('id' => $id)));
        }
    }

    /*
     * 
     * Documentación para el método 'deleteOltAction'.
     *
     * Metodo utilizado para eliminar Olt
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 22-07-2015
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.2 05-06-2017    Se agregan validaciones de enlaces existentes entre OLT y SPLITTERS
     * @since 1.1
     */
    public function deleteOltAction($id){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $request  = $this->getRequest();
        $peticion = $this->get('request');
        $session  = $request->getSession();
        $em       = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emC      = $this->getDoctrine()->getManager('telconet');
        $entity   = $em->getRepository('schemaBundle:InfoElemento')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InfoElemento entity.');
        }
        
        //se valida si existen servicios Activos que dependan de este OLT
        $serviciosTec = $emC->getRepository('schemaBundle:InfoServicioTecnico')->findBy(array( "elementoId" =>$entity->getId()));
        for($i=0;$i<count($serviciosTec);$i++)
        {
            $servicioId = $serviciosTec[$i]->getServicioId()->getId();
            $servicio   = $emC->getRepository('schemaBundle:InfoServicio')->find($servicioId);
            $estadoServ = $servicio->getEstado();
            
            if( $estadoServ !='Anulado' &&
                $estadoServ !='Cancel' &&
                $estadoServ !='Eliminado' &&
                $estadoServ !='Rechazada' &&
                $estadoServ !='Reubicado' &&
                $estadoServ !='Trasladado' )
            {
                return $respuesta->setContent("SERVICIOS ACTIVOS");
            }
        }
        
        //se recuperan interfaces del elemento OLT
        $arrayInterfaceElemento = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                     ->findBy(array( "elementoId" => $entity->getId()));

        //se valida que no exista ningun elemento(Splitter) activo enlazado al OLT
        for ($i = 0; $i < count($arrayInterfaceElemento); $i++) 
        {
            $arrayEnlacesOut = $em->getRepository('schemaBundle:InfoEnlace')
                                  ->findBy(array("interfaceElementoIniId" => $arrayInterfaceElemento[$i]->getId(),
                                                 "estado"                 => 'Activo'));
            for ($j = 0; $j < count($arrayEnlacesOut); $j++) 
            {
                return $respuesta->setContent("ELEMENTOS ENLAZADOS");
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
        for($i=0;$i<count($arrayInterfaceElemento);$i++){
            $interface = $em->getRepository('schemaBundle:InfoInterfaceElemento')->find($arrayInterfaceElemento[$i]->getId());
            $interface->setEstado("Eliminado");
            $em->persist($interface);
        }
        
        //relacion elemento
        $objRelacionesElementos = $em->getRepository('schemaBundle:InfoRelacionElemento')->findBy(array("elementoIdB" => $entity));
        foreach($objRelacionesElementos as $objRelacionElemento)
        {
            $objRelacionElemento->setEstado("Eliminado");
            $objRelacionElemento->setUsrCreacion($session->get('user'));
            $objRelacionElemento->setFeCreacion(new \DateTime('now'));
            $objRelacionElemento->setIpCreacion($peticion->getClientIp());
            $em->persist($objRelacionElemento);
        }

        //historial elemento
        $historialElemento = new InfoHistorialElemento();
        $historialElemento->setElementoId($entity);
        $historialElemento->setEstadoElemento("Eliminado");
        $historialElemento->setObservacion("Se elimino un Olt");
        $historialElemento->setUsrCreacion($session->get('user'));
        $historialElemento->setFeCreacion(new \DateTime('now'));
        $historialElemento->setIpCreacion($peticion->getClientIp());
        $em->persist($historialElemento);
        
        /*Info detelle elemento -> OLT OPERATIVO
         *Se agrega detalle elemento para agregar registro de validacion de Operatividad de OLT
        */
        $objDetalleElemento = new InfoDetalleElemento();                
        $objDetalleElemento->setElementoId($entity->getId());
        $objDetalleElemento->setDetalleNombre("OLT OPERATIVO");
        $objDetalleElemento->setDetalleValor("NO");
        $objDetalleElemento->setDetalleDescripcion("OLT OPERATIVO");
        $objDetalleElemento->setUsrCreacion($session->get('user'));
        $objDetalleElemento->setFeCreacion(new \DateTime('now'));
        $objDetalleElemento->setIpCreacion($peticion->getClientIp());
        $em->persist($objDetalleElemento);
            
        $em->flush();
        $em->getConnection()->commit();

        return $this->redirect($this->generateUrl('elementoolt'));
    }
    
    /*
     * 
     * Documentación para el método 'deleteAjaxOltAction'.
     *
     * Metodo utilizado para eliminar varios Olt
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 22-07-2015
     */
    public function deleteAjaxOltAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $peticion  = $this->get('request');
        $session   = $peticion->getSession();
        $parametro = $peticion->get('param');
        $em        = $this->getDoctrine()->getManager("telconet_infraestructura");
        $emC       = $this->getDoctrine()->getManager("telconet");
        
        $array_valor = explode("|",$parametro);
        
        //validar que no existan servicios activos al elemento
        foreach($array_valor as $id)
        {
            $serviciosTec = $emC->getRepository('schemaBundle:InfoServicioTecnico')->findBy(array("elementoId" => $id));
            for($i = 0; $i < count($serviciosTec); $i++)
            {
                $servicioId = $serviciosTec[$i]->getServicioId()->getId();
                $servicio   = $emC->getRepository('schemaBundle:InfoServicio')->find($servicioId);
                $estadoServ = $servicio->getEstado();
                if($estadoServ == "Activo")
                {
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
                $historialElemento->setObservacion("Se elimino un Olt");
                $historialElemento->setUsrCreacion($session->get('user'));
                $historialElemento->setFeCreacion(new \DateTime('now'));
                $historialElemento->setIpCreacion($peticion->getClientIp());
                $em->persist($historialElemento);
                
                /*Info detelle elemento -> OLT OPERATIVO
                 *Se agrega detalle elemento para agregar registro de validacion de Operatividad de OLT
                */
                $objDetalleElemento = new InfoDetalleElemento();                
                $objDetalleElemento->setElementoId($entity->getId());
                $objDetalleElemento->setDetalleNombre("OLT OPERATIVO");
                $objDetalleElemento->setDetalleValor("NO");
                $objDetalleElemento->setDetalleDescripcion("OLT OPERATIVO");
                $objDetalleElemento->setUsrCreacion($session->get('user'));
                $objDetalleElemento->setFeCreacion(new \DateTime('now'));
                $objDetalleElemento->setIpCreacion($peticion->getClientIp());
                $em->persist($objDetalleElemento);
                
                $em->flush();
                
            }
        endforeach;
        $respuesta->setContent("OK");
        
        return $respuesta;
    }
    
    
    /*
     * 
     * Documentación para el método 'showOltAction'.
     *
     * Metodo utilizado para mostrar la información del elemento Olt creado
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.1 11-11-2015 - Se modifica para mostrar el tipo de aprovisionamiento ip guardado en el Olt
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 Versión Inicial
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 Se agrega codigo para obtener Operatividad de elemento
     */
    public function showOltAction($id)
    {
        $objRequest           = $this->get('request');
        $objSession           = $objRequest->getSession();
        $intEmpresaId         = $objSession->get('idEmpresa');
        $strFechaOperatividad = "";
        
        $emInfraestructura = $this->getDoctrine()->getManager("telconet_infraestructura");
        $emGeneral = $this->getDoctrine()->getManager('telconet_general');

        if(null == $objElemento = $emInfraestructura->find('schemaBundle:InfoElemento', $id))
        {
            throw new NotFoundHttpException('No existe el Elemento que se quiere mostrar');
        }
        else
        {
            /* @var $datosElemento InfoServicioTecnico */
            $datosElemento = $this->get('tecnico.InfoServicioTecnico');
            //---------------------------------------------------------------------*/
            $respuestaElemento = $datosElemento->obtenerDatosElemento($id, $intEmpresaId);
            
            $ipElemento         = $respuestaElemento['ipElemento'];
            $arrayHistorial     = $respuestaElemento['historialElemento'];
            $ubicacion          = $respuestaElemento['ubicacion'];
            $jurisdiccion       = $respuestaElemento['jurisdiccion'];
            
            $arrayParametrosDetalle = array('elementoId' => $objElemento->getId(), 'detalleNombre' => self::DETALLE_ELEMENTO_APROVISIONAMIENTO);
            $objInfoDetalleElemento = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')->findOneBy($arrayParametrosDetalle);
            
            $strTipoAprovisionamientoIp = 'NO ASIGNADO';
            
            if( $objInfoDetalleElemento )
            {
                $strTipoAprovisionamientoIp = $objInfoDetalleElemento->getDetalleValor();
                
                if( $strTipoAprovisionamientoIp == 'POOL' )
                {
                    $strTipoAprovisionamientoIp = 'CLASICO';
                }
            }//( $objInfoDetalleElemento )
            
            //Se agrega codigo para validar existencia de caracteristica de elemento Operativo
            $entityDetalleElementoOp = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                         ->findOneBy(array( "elementoId"    => $objElemento->getId(), 
                                                                            "detalleNombre" => "OLT OPERATIVO"));
            
            
            if ($objElemento->getEstado() != "Eliminado")
            {
                if ($entityDetalleElementoOp)
                {
                    $strOltOperativo      = $entityDetalleElementoOp->getDetalleValor();
                    $strFechaOperatividad = strval(date_format($entityDetalleElementoOp->getFeCreacion(), "d/m/Y G:i")).
                                            " ".$entityDetalleElementoOp->getUsrCreacion();
                }
                else
                {
                    $strOltOperativo = "SI";
                }
            }
            else
            {
                if ($entityDetalleElementoOp)
                {
                    $strFechaOperatividad = strval(date_format($entityDetalleElementoOp->getFeCreacion(), "d/m/Y G:i")).
                                            " ".$entityDetalleElementoOp->getUsrCreacion();
                }
                $strOltOperativo = "NO";
            }
            //seteo las variables
            $strDetalleMulti         = self::DETALLE_ELEMENTO_MULTIPLATAFORMA;
            $strDetallePeAsignado    = self::DETALLE_ELEMENTO_PE_ASIGNADO;
            $arrayParametrosDetMulti = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->getOne('NUEVA_RED_GPON_TN',
                                                                                            'COMERCIAL',
                                                                                            '',
                                                                                            'NOMBRES PARAMETROS DETALLES MULTIPLATAFORMA',
                                                                                            '',
                                                                                            '',
                                                                                            '',
                                                                                            '',
                                                                                            '');
            if(isset($arrayParametrosDetMulti) && !empty($arrayParametrosDetMulti))
            {
                $strDetalleMulti        = isset($arrayParametrosDetMulti['valor1']) && !empty($arrayParametrosDetMulti['valor1'])
                                          ? $arrayParametrosDetMulti['valor1'] : $strDetalleMulti;
                $strDetallePeAsignado   = isset($arrayParametrosDetMulti['valor2']) && !empty($arrayParametrosDetMulti['valor2'])
                                          ? $arrayParametrosDetMulti['valor2'] : $strDetallePeAsignado;
            }
            //verificar si es multiplataforma
            $strOltMultiplataforma  = "NO";
            $strNombrePeMulti       = "";
            $objDetMultiplataforma  = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                           ->findOneBy(array("elementoId"    => $objElemento->getId(),
                                                             "detalleNombre" => $strDetalleMulti,
                                                             "estado"        => "Activo"));
            if(is_object($objDetMultiplataforma))
            {
                $strOltMultiplataforma = $objDetMultiplataforma->getDetalleValor();
            }
            //obtengo el PE si el olt es multiplataforma
            if($strOltMultiplataforma == "SI")
            {
                $objDetIdPeMulti = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                               ->findOneBy(array("elementoId"    => $objElemento->getId(),
                                                                 "detalleNombre" => $strDetallePeAsignado,
                                                                 "estado"        => "Activo"));
                if(is_object($objDetIdPeMulti))
                {
                    $objElementoPe = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                        ->find($objDetIdPeMulti->getDetalleValor());
                    if(is_object($objElementoPe))
                    {
                        $strNombrePeMulti = $objElementoPe->getNombreElemento();
                    }
                }
            }
        }//(null == $objElemento = $emInfraestructura->find('schemaBundle:InfoElemento', $id))

        return $this->render('tecnicoBundle:InfoElementoOlt:show.html.twig', array(
            'elemento'              => $objElemento,
            'ipElemento'            => $ipElemento,
            'historialElemento'     => $arrayHistorial,
            'ubicacion'             => $ubicacion,
            'jurisdiccion'          => $jurisdiccion,
            'flag'                  => $objRequest->get('flag'),
            'aprovisionamientoIp'   => $strTipoAprovisionamientoIp,
            'operatividad'          => $strOltOperativo,
            'fechaOperatividad'     => $strFechaOperatividad,
            'strOltMultiplataforma' => $strOltMultiplataforma,
            'strNombrePeMulti'      => $strNombrePeMulti
        ));
    }
    
    /**
     * Funcion que sirve para obtener el listado de olt con sus filtros.
     * 
     * Version Inicial
     * @version 1.0
     * 
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.1 23-03-2021 - Se agrega nuevo filtro para los Olt Multiplataforma
     * 
     * @author Alberto Arias <farias@telconet.ec>
     * @version 1.2
     * @since 13-04-2023  Se agrega el prefijo Empresa para concatenar al filtro de busqueda por empresa.
     * 
     */
    public function getEncontradosOltAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $session = $this->get('session');
        $session->save();
        session_write_close();
        
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emComercial  = $this->get('doctrine')->getManager('telconet');
        $tipoElemento = $em->getRepository('schemaBundle:AdmiTipoElemento')->findBy(array( "nombreTipoElemento" =>"OLT"));
        
        $peticion = $this->get('request');
        
        $strNombreElemento = $peticion->query->get('nombreElemento');
        $strIpElemento = $peticion->query->get('ipElemento');        
        $strModeloElemento = $peticion->query->get('modeloElemento');
        $strMarcaElemento = $peticion->query->get('marcaElemento');
        $strCanton = $peticion->query->get('canton');
        $strJurisdiccion = $peticion->query->get('jurisdiccion');
        $strNodoElemento = $peticion->query->get('nodoElemento');
        $strEstado = $peticion->query->get('estado');
        $strMultiplataforma = $peticion->query->get('multiplataforma');
        $strEstadoMultiplataforma = $peticion->query->get('estadoMultiplataforma');
        $booleanMultiplataforma = $peticion->query->get('booleanMultiplataforma');
        $strIdEmpresa = $session->get('idEmpresa');
        $strStart = $peticion->query->get('start');
        $strLimit = $peticion->query->get('limit');
        
        $arrayParametros = array(
            'strNombreElemento' => strtoupper($strNombreElemento),
            'strIpElemento'     => $strIpElemento,
            'strModeloElemento' => $strModeloElemento,
            'strMarcaElemento'  => $strMarcaElemento,
            'strTipoElemento'   => $tipoElemento[0]->getId(),
            'strCanton'         => $strCanton,
            'strJurisdiccion'   => $strJurisdiccion,
            'strNodoElemento'   => $strNodoElemento,
            'strEstado'         => $strEstado,
            'strMultiplataforma' => $strMultiplataforma,
            'strEstadoMultiplataforma' => $strEstadoMultiplataforma,
            'strIdEmpresa'      => $strIdEmpresa,
            'strStart'          => $strStart,
            'strLimit'          => $strLimit,
            'emComercial'       => $emComercial,
            'booleanMultiplataforma' => $booleanMultiplataforma,
            'prefijoEmpresa'    => $session->get('prefijoEmpresa')
        );
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:InfoElemento')
            ->generarJsonOlts($arrayParametros);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    public function cargarDatosOltAction(){
       $respuesta = new Response();
       $em = $this->getDoctrine()->getManager('telconet_infraestructura');
       
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $idOlt = $peticion->get('idOlt');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:InfoElemento')
            ->generarJsonCargarDatosOlt($idOlt,$em);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    /********************************************************************************
     *                          ACCCIONES PARA OLT                                  *
     ********************************************************************************/
    
    /**
     * Funcion que sirve para ver el servicio del suscriptor
     * en el olt
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24-11-2014
     */
    public function verificarServicioOltAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');
        $emComercial = $this->get('doctrine')->getManager('telconet');

        $peticion = $this->get('request');
        $session = $peticion->getSession();
        $idEmpresa = $session->get('idEmpresa');
        $host = $this->container->getParameter('host');
        $pathTelcos = $this->container->getParameter('path_telcos');
        $pathParameters = $this->container->getParameter('path_parameters');
        $modeloElemento = $peticion->get('modelo');
        $idElemento = $peticion->get('idElemento');
        $idServicio = $peticion->get('idServicio');
        $interfaceElemento = $peticion->get('interfaceElemento');

        $servicio = $emComercial->getRepository('schemaBundle:InfoServicio')->find($idServicio);
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $prod = $emComercial->getRepository('schemaBundle:AdmiProducto')
            ->findOneBy(array("nombreTecnico" => "INTERNET", "empresaCod" => $idEmpresa, "estado" => "Activo"));
        $carac1 = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
            ->findOneBy(array("descripcionCaracteristica" => "MAC ONT"));
        $prodCaract1 = $emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
            ->findOneBy(array("productoId" => $prod->getId(), "caracteristicaId" => $carac1->getId()));
        $mac = $emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
            ->findOneBy(array("servicioId" => $servicio->getId(), "productoCaracterisiticaId" => $prodCaract1->getId()));


        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("verificarServiciosOltEP-3116", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

        $arr = $outDocumentoPorModelo->encontrados;

        $script = $arr[0]->script;
        $idDocumento = $arr[0]->idDocumento;
        $usuario = $arr[0]->usuario;
        $protocolo = $arr[0]->protocolo;

        if($script == "0")
        {
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else
        {
            $datos = $interfaceElemento . "," . $mac->getValor();
            $comando = "java -jar -Djava.security.egd=file:/dev/./urandom " . $pathTelcos . "telcos/src/telconet/tecnicoBundle/batch/md_ejecucion.jar '" .
                $host . "' '" . $idDocumento . "' '" . $usuario . "' '" . $protocolo . "' '" . $idElemento . "' '" . 
                $datos . "' '" . $pathParameters . "'";
            $salida = shell_exec($comando);
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para ver el listado de las macs
     * que se encuentran conectadas en el olt.
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24-11-2014
     */
    public function mostrarMacsConectadasOltAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');
        $emComercial = $this->get('doctrine')->getManager('telconet');

        $peticion = $this->get('request');
        $session = $peticion->getSession();
        $idEmpresa = $session->get('idEmpresa');
        $host = $this->container->getParameter('host');
        $pathTelcos = $this->container->getParameter('path_telcos');
        $pathParameters = $this->container->getParameter('path_parameters');
        $modeloElemento = $peticion->get('modelo');
        $idServicio = $peticion->get('idServicio');
        $interfaceElemento = $peticion->get('interfaceElemento');

        $servicio = $emComercial->getRepository('schemaBundle:InfoServicio')->find($idServicio);
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $prod = $emComercial->getRepository('schemaBundle:AdmiProducto')
            ->findOneBy(array("nombreTecnico" => "INTERNET", "empresaCod" => $idEmpresa, "estado" => "Activo"));
        $carac1 = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
            ->findOneBy(array("descripcionCaracteristica" => "INDICE CLIENTE"));
        $prodCaract1 = $emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
            ->findOneBy(array("productoId" => $prod->getId(), "caracteristicaId" => $carac1->getId()));
        $indice = $emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
            ->findOneBy(array("servicioId" => $servicio->getId(), "productoCaracterisiticaId" => $prodCaract1->getId()));


        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarMacsConectadasOltEP-3116", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

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
            $datos = $interfaceElemento . "," . $indice->getValor();
            $comando = "java -jar -Djava.security.egd=file:/dev/./urandom " . $pathTelcos . "telcos/src/telconet/tecnicoBundle/batch/md_ejecucion.jar '" .
                $host . "' '" . $idDocumento . "' '" . $usuario . "' '" . $protocolo . "' '" . $idElemento . "' '" . 
                $datos . "' '" . $pathParameters . "'";
            $salida = shell_exec($comando);
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que sirve para ver el listado de los subscribers por mac ont
     * que se encuentran conectadas en el olt.
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24-11-2014
     */
    public function mostrarSubscriberConectadosOltAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');
        $emComercial = $this->get('doctrine')->getManager('telconet');

        $host = $this->container->getParameter('host');
        $pathTelcos = $this->container->getParameter('path_telcos');
        $pathParameters = $this->container->getParameter('path_parameters');

        $peticion = $this->get('request');
        $session = $peticion->getSession();
        $idEmpresa = $session->get('idEmpresa');
        $modeloElemento = $peticion->get('modelo');
        $idServicio = $peticion->get('idServicio');

        $servicio = $emComercial->getRepository('schemaBundle:InfoServicio')->find($idServicio);
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $prod = $emComercial->getRepository('schemaBundle:AdmiProducto')
            ->findOneBy(array("nombreTecnico" => "INTERNET", "empresaCod" => $idEmpresa, "estado" => "Activo"));
        $carac1 = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
            ->findOneBy(array("descripcionCaracteristica" => "MAC ONT"));
        $prodCaract1 = $emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
            ->findOneBy(array("productoId" => $prod->getId(), "caracteristicaId" => $carac1->getId()));
        $mac = $emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
            ->findOneBy(array("servicioId" => $servicio->getId(), "productoCaracterisiticaId" => $prodCaract1->getId()));


        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarSubscriberConectadosOltEP-3116", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

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
            $datos = $mac->getValor();
            $comando = "java -jar -Djava.security.egd=file:/dev/./urandom " . $pathTelcos . "telcos/src/telconet/tecnicoBundle/batch/md_ejecucion.jar '" .
                $host . "' '" . $idDocumento . "' '" . $usuario . "' '" . $protocolo . "' '" . $idElemento . "' '" .
                $datos . "' '" . $pathParameters . "'";
            $salida = shell_exec($comando);
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    public function eliminarDobleLineaPonAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');
		
        $peticion = $this->get('request');
        $host = $this->container->getParameter('host');
        $modeloElemento = $peticion->get('modelo');
        $idElemento = $peticion->get('idElemento');
        $interfaceElemento = $peticion->get('interfaceElemento');
        $indiceCliente = $peticion->get('indiceCliente');
	
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array( "nombreModeloElemento" =>$modeloElemento));
                
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("cancelarClienteEP-3116",$modelo[0]->getId(),$emSop,$emCom,$emSeg,$em);
			
        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);
        
        $arr = $outDocumentoPorModelo->encontrados;
                
        $script = $arr[0]->script;
        $idDocumento= $arr[0]->idDocumento;
        $usuario= $arr[0]->usuario;
        $protocolo= $arr[0]->protocolo;
        
        if($script=="0"){
            $mensaje = "ERROR, NO EXISTE RELACION TAREA - ACCION";
        }
        else{
            $datos = $interfaceElemento.",".$indiceCliente.",".$indiceCliente;
            $comando = "java -jar -Djava.security.egd=file:/dev/./urandom /home/telcos/src/telconet/tecnicoBundle/batch/md_ejecucion.jar '".$host."' '".$idDocumento."' '".$usuario."' '".$protocolo."' '".$idElemento."' '".$datos."'";
//            print($comando);
//            die();
            $salida= shell_exec($comando);
            $pos = strpos($salida, "{"); 
            $jsonObj= substr($salida, $pos);
            $mensaje = $jsonObj->status;
        }
        
        return $respuesta->setContent($mensaje);
	}
	
    /**
     * Funcion que sirve para mostrar el listado de suscriptores
     * conectados al olt
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24-11-2014
     */
    public function mostrarSubscriberOltAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');
        $host = $this->container->getParameter('host');
        $pathTelcos = $this->container->getParameter('path_telcos');
        $pathParameters = $this->container->getParameter('path_parameters');
        $modeloElemento = $peticion->get('modelo');
        
        

        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("mostrarSubscribersOltEP-3116", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

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
            $comando = "java -jar -Djava.security.egd=file:/dev/./urandom " . $pathTelcos . "telcos/src/telconet/tecnicoBundle/batch/md_ejecucion.jar '" .
                $host . "' '" . $idDocumento . "' '" . $usuario . "' '" . $protocolo . "' '" . $idElemento . "' '' '" . $pathParameters . "'";
            $salida = shell_exec($comando);
            $pos = strpos($salida, "{");
            $jsonObj = substr($salida, $pos);
        }

        return $respuesta->setContent($jsonObj . "&" . $script);
    }

    /**
     * Funcion que ejecuta un script para actualizar
     * pool de ips en la base de datos.
     * 
     * @author Creado: Francisco Adum <fadum@telconet.ec>
     * @version 1.0 20-10-2014
     * @author Modificado: Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 25-04-2016    Se agrega script para recuperar perfiles de OLT previo a la actualización de pools
     * 
     * @Secure(roles="ROLE_227-1877")
     */
    public function actualizarPoolIpAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        //datos necesarios para la ejecucion de scripts
        $em         = $this->get('doctrine')->getManager('telconet_infraestructura');
        $host       = $this->container->getParameter('host');
        $path       = $this->container->getParameter('path_telcos');
        $parameters = $this->container->getParameter('path_parameters');
        $peticion   = $this->get('request');
        $session    = $peticion->getSession();
        $idElemento = $peticion->get('idElemento');
        $elemento   = $em->getRepository('schemaBundle:InfoElemento')->find($idElemento);
        
        $em->getConnection()->beginTransaction();
        
        try
        {
            /* @var $script InfoServicioTecnicoService */
            $script      = $this->get('tecnico.InfoServicioTecnico');
            $scriptArray = $script->obtenerArregloScript("mostrarPerfilesOlt", $elemento->getModeloElementoId());
            $idDocumento = $scriptArray[0]->idDocumento;

            //perfiles
            $comando = "java -jar -Djava.security.egd=file:/dev/./urandom ".$path."telcos/src/telconet/tecnicoBundle/batch/md_datos.jar '" . 
                       $host . "' 'obtenerPerfiles' '" . 
                       $elemento->getId() . "' 'usuario' 'puerto' '" . $idDocumento . "' 'datos'";
            
            $salida    = shell_exec($comando);
            $pos       = strpos($salida, "{");
            $jsonObj   = substr($salida, $pos);
            $resultado = json_decode($jsonObj);
            $status    = $resultado->status;
            if($status=="OK")
            {
                $salida      = null;
                $pos         = null;
                $jsonObj     = null;
                $resultado   = null;
                $status      = null;
                $comandoPool = "java -jar -Djava.security.egd=file:/dev/./urandom ".$path."telcos/src/telconet/tecnicoBundle/batch/md_datos.jar '".
                               $host."' 'obtenerPools' '".$elemento->getId()."' 'usuario' 'puerto' '0' 'datos' '".$parameters."'";
                $salida      = shell_exec($comandoPool);
                $pos         = strpos($salida, "{");
                $jsonObj     = substr($salida, $pos);
                $resultado   = json_decode($jsonObj);
                $status      = $resultado->status;

                if($status=="OK")
                {
                    //historial elemento
                    $historialElemento = new InfoHistorialElemento();
                    $historialElemento->setElementoId($elemento);
                    $historialElemento->setEstadoElemento($elemento->getEstado());
                    $historialElemento->setObservacion("Se actualizaron los pools de ip");
                    $historialElemento->setUsrCreacion($session->get('user'));
                    $historialElemento->setFeCreacion(new \DateTime('now'));
                    $historialElemento->setIpCreacion($peticion->getClientIp());
                    $em->persist($historialElemento);
                    $em->flush();

                    $mensaje = $status;
                }
                else
                {
                    $mensaje = "Error: ". $resultado->mensaje;
                }
            }
            else
            {
                $mensaje = "Error: ". $resultado->mensaje;
            }
        }
        catch(Exception $e)
        {
            if($em->getConnection()->isTransactionActive())
            {
                $em->getConnection()->rollback();
            }
            
            $mensaje = "Error en la LOGICA, <br>"
                     . "Mensaje: ".$e;
        }
        
        if($em->getConnection()->isTransactionActive())
        {
            $em->getConnection()->commit();
        }

        return $respuesta->setContent($mensaje);
    }
    
    /**
     * Funcion AJAX que ejecuta un script para actualizar
     * las caracteristicas (line-profile, service-profile,
     * gemport, traffic-table) de un olt en la base de datos.
     * 
     * @author Creado: Francisco Adum <fadum@telconet.ec>
     * @version 1.0 2-04-2015
     * @Secure(roles="ROLE_227-2457")
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.1 12-08-2021 - Se parametriza el tiempo máximo de ejecución
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.2 10-09-2021 - Se realiza la actualización de las características del Olt por solicitud de proceso masivo
     */
    public function actualizarCaracteristicasHuaweiAjaxAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        //datos necesarios para la ejecucion de scripts
        $em             = $this->get('doctrine')->getManager('telconet_infraestructura');        
        $emGeneral      = $this->getDoctrine()->getManager('telconet_general');
        $peticion       = $this->get('request');
        $session        = $peticion->getSession();
        $idElemento     = $peticion->get('idElemento');
        $elementoOlt    = $em->getRepository('schemaBundle:InfoElemento')->find($idElemento);
        $strIdEmpresa   = $session->get('idEmpresa');
        $strIpClient    = $peticion->getClientIp();
        $strUsrSesion   = $session->get('user');
        $serviceElemento = $this->get('tecnico.InfoElemento');
        $serviceServicioTecnico = $this->get('tecnico.InfoServicioTecnico');

        $arrayMaxTiempoEjecucion = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->getOne('CONTROL_PROCESOS_MAXIMO_TIEMPO_EJECUCION',
                                                                                        'TECNICO',
                                                                                        '',
                                                                                        '',
                                                                                        'actualizarCaracteristicasHuaweiAjaxAction',
                                                                                        '',
                                                                                        '',
                                                                                        '',
                                                                                        '');
        if(isset($arrayMaxTiempoEjecucion) && !empty($arrayMaxTiempoEjecucion) &&
           isset($arrayMaxTiempoEjecucion['valor2']) && !empty($arrayMaxTiempoEjecucion['valor2']) &&
           intval($arrayMaxTiempoEjecucion['valor2']) > 0)
        {
            ini_set('max_execution_time', intval($arrayMaxTiempoEjecucion['valor2']));
        }

        try
        {
            $arrayParametros = array(
                "serviceServicioTecnico" => $serviceServicioTecnico,
                "emInfraestructura" => $em,
                "objElementoOlt"    => $elementoOlt,
                "strIdEmpresa"      => $strIdEmpresa,
                "strUsrSesion"      => $strUsrSesion,
                "strIpClient"       => $strIpClient,
            );
            //actualizar características
            $arrayResultado = $serviceElemento->actualizarCaracteristicasOlt($arrayParametros);
            if($arrayResultado['status'] != "OK")
            {
                throw new \Exception($arrayResultado['mensaje']);
            }
            $strMensaje = $arrayResultado['mensaje'];
        }
        catch(\Exception $e)
        {
            $strMensaje = "Error: ".$e->getMessage();
        }

        return $respuesta->setContent($strMensaje);
    }
    
    /**
     * Funcion que desconfigura la ip del olt
     * 
     * @author Creado: Francisco Adum <fadum@telconet.ec>
     * @version 1.0 23-09-2014
     * 
     */
    
    /**
    * @Secure(roles="ROLE_227-837")
    */ 
    public function desconfigurarIpFijaAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        //datos necesarios para la ejecucion de scripts
        $host = $this->container->getParameter('host');
        $path = $this->container->getParameter('path_telcos');
        $parameters = $this->container->getParameter('path_parameters');

        //obtener los em necesarios
        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');

        $peticion = $this->get('request');
        $session  = $peticion->getSession();
        
        //datos enviados por ajax
        $modeloElemento = $peticion->get('modelo');
        $idElemento = $peticion->get('idElemento');
        $pool = $peticion->get('pool');
        $ip = $peticion->get('ip');
        $mac = $peticion->get('mac');
        
        $em->getConnection()->beginTransaction();
        
        try
        {
            //cambiar formato de mac
            $mac = $this->cambiarMac($mac);

            $elemento = $em->getRepository('schemaBundle:InfoElemento')->find($idElemento);
            $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array("nombreModeloElemento" => $modeloElemento));

            //obtener el script para desconfigurar ip fija
            $objJson = $this->getDoctrine()
                ->getManager("telconet_infraestructura")
                ->getRepository('schemaBundle:AdmiModeloElemento')
                ->generarJsonDocumentoPorModelo("desconfigurarIpFijaEP-3116", $modelo[0]->getId(), $emSop, $emCom, $emSeg, $em);

            $posicion = strpos($objJson, "{");
            $respuestaDocumentoPorModelo = substr($objJson, $posicion);
            $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);

            $arr = $outDocumentoPorModelo->encontrados;

            $script = $arr[0]->script;
            $idDocumento = $arr[0]->idDocumento;
            $usuario = $arr[0]->usuario;
            $protocolo = $arr[0]->protocolo;

            if($script == "0")
            {
                $mensaje = "Error: NO EXISTE RELACION TAREA - ACCION";
            }
            else
            {
                //ejecucion de script
                $datos = $pool . "," . $ip . "," . $mac;
                $comando = "java -jar -Djava.security.egd=file:/dev/./urandom " . $path . "telcos/src/telconet/tecnicoBundle/batch/md_ejecucion.jar '" .
                    $host . "' '" . $idDocumento . "' '" . $usuario . "' '" . $protocolo . "' '" . $idElemento . "' '" . 
                    $datos . "'" . " '" . $parameters . "'";
                error_log($comando);
                $salida = shell_exec($comando);
                $pos = strpos($salida, "{");
                $jsonObj = substr($salida, $pos);
                $resultado = json_decode($jsonObj);
                $status = $resultado->status;

                if($status=="OK")
                {
                    //historial elemento
                    $historialElemento = new InfoHistorialElemento();
                    $historialElemento->setElementoId($elemento);
                    $historialElemento->setEstadoElemento("Activo");
                    $historialElemento->setObservacion("Se elimino la ip, datos: <br>"
                                                     . "Pool:".$pool." <br>Ip:".$ip." <br>Mac:".$mac);
                    $historialElemento->setUsrCreacion($session->get('user'));
                    $historialElemento->setFeCreacion(new \DateTime('now'));
                    $historialElemento->setIpCreacion($peticion->getClientIp());
                    $em->persist($historialElemento);

                    $em->flush();
                    
                    $mensaje = $status;
                }
                else
                {
                    $mensaje = "Error: ". $resultado->mensaje;
                }
            }
        }
        catch(Exception $e)
        {
            if($em->getConnection()->isTransactionActive())
            {
                $em->getConnection()->rollback();
            }
            
            $mensaje = "Error en la LOGICA, <br>"
                     . "Mensaje: ".$e;
        }
        
        if($em->getConnection()->isTransactionActive())
        {
            $em->getConnection()->commit();
        }

        return $respuesta->setContent($mensaje);
    }

    /**
     * Funcion que ejecuta script para ver la tabla
     * de las macs de un puerto de un olt
     * 
     * @author Creado: Francisco Adum <fadum@telconet.ec>
     * @version 1.0 02-10-2014
     * 
     */
    public function mostrarTablaMacsConectadasOltAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSop = $this->get('doctrine')->getManager('telconet_soporte');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeg = $this->get('doctrine')->getManager('telconet_seguridad');
        
        //datos necesarios para la ejecucion de scripts
        $host = $this->container->getParameter('host');
        $path = $this->container->getParameter('path_telcos');
        $parameters = $this->container->getParameter('path_parameters');
        
        $peticion = $this->get('request');
        $modeloElemento = $peticion->get('modelo');
        $interfaceElemento = $peticion->get('interfaceElemento');        
        
        $modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')
                     ->findBy(array( "nombreModeloElemento" =>$modeloElemento));
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonDocumentoPorModelo("obtenerMacPuertoEP-3116",$modelo[0]->getId(),$emSop,$emCom,$emSeg,$em);
        
        $posicion = strpos($objJson, "{");
        $respuestaDocumentoPorModelo = substr($objJson, $posicion);
        $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);
        
        $arr = $outDocumentoPorModelo->encontrados;
        
        $script = $arr[0]->script;
        $idDocumento= $arr[0]->idDocumento;
        $usuario= $arr[0]->usuario;
        $protocolo= $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');
        
        if($script=="0"){
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else{
            $datos = $interfaceElemento;
            $comando = "java -jar -Djava.security.egd=file:/dev/./urandom ".$path."telcos/src/telconet/tecnicoBundle/batch/md_ejecucion.jar '".$host."' '".
                        $idDocumento."' '".$usuario."' '".$protocolo."' '".$idElemento."' '".$datos."' '".$parameters."'";
            $salida= shell_exec($comando);
            $pos = strpos($salida, "{"); 
            $jsonObj= substr($salida, $pos);
        }
        
        return $respuesta->setContent($jsonObj."&".$script);
    }
    
    /**
     * 
     * @param String       $macWifi (mac en formato aaaa.bbbb.cccc)
     * @return mac en formato aa:aa:bb:bb:cc:cc
     */
    public function cambiarMac($macWifi)
    {
        $macWifiNueva = "";
        $macWifi = trim($macWifi);
        $arr2 = explode(".", $macWifi);
        for($i = 0; $i < count($arr2); $i++)
        {
            $arr1 = str_split($arr2[$i]);
            for($j = 0; $j < count($arr1); $j++)
            {
                if($j == 1 || $j == 3 && ($i + 1) != count($arr1) - 1)
                {
                    $macWifiNueva = $macWifiNueva . $arr1[$j] . ":";
                }
                else
                {
                    $macWifiNueva = $macWifiNueva . $arr1[$j] . "";
                }
            }
        }
        return $macWifiNueva;
    }

    /**
     * Funcion que obtiene los pools que se encuentran registrados
     * en la base de datos, descargados del olt en su momento de 
     * creacion.
     * 
     * @author Creado: Francisco Adum <fadum@telconet.ec>
     * @version 1.0 23-09-2014
     * 
     */
    public function mostrarPoolsPorOltAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $request = $this->get('request');

        $peticion = $this->get('request');

        $idElemento = $peticion->get('idElemento');
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:InfoElemento')
            ->generarJsonPoolsPorOlt($idElemento, $start, 1000);

        $respuesta->setContent($objJson);

        return $respuesta;
    }

    /********************************************************************************
     *                    ACCCIONES PARA POOLS Y SCOPES                             *
     ********************************************************************************/
    
    /**
     * getElementoServidorAjaxAction, Obtiene elementos por tipo de servidores 
     *
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 08-03-2015
     * 
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 30-12-2015 Se realizan ajustes para obtener correctamente el codigo de la empresa
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.2 28-11-2018 Se agregan validacioes para gestionar productos de la empresa TNP
     * @since 1.1
     * 
     * @return json  Obtiene un json con los elementos por tipo de servidores 
    */
    public function getElementoServidorAjaxAction()
    {
        $objRequest         = $this->get('request');
        $session            = $objRequest->getSession();
        $strTipoElemento    = $objRequest->get('tipoElemento');
        $parametros         = array();
        $em                 = $this->getDoctrine()->getManager("telconet_infraestructura");
        $objTipoElemento    = $em->getRepository("schemaBundle:AdmiTipoElemento")->findOneBy(array('nombreTipoElemento' => 'SERVIDOR'));

        $parametros["nombre"]       = '';
        $parametros["estado"]       = 'Activo';
        $parametros["tipoElemento"] = $objTipoElemento->getId();
        $parametros["codEmpresa"]   = $session->get('idEmpresa');
        $parametros["start"]        = '';
        $parametros["limit"]        = '';
        if (!empty($strTipoElemento))
        {
            $parametros["codEmpresa"] = "18";
        }
        $objJson            = $this->getDoctrine()->getManager("telconet_infraestructura")
                                                  ->getRepository('schemaBundle:InfoElemento')
                                                  ->generarJsonElementosXTipo($parametros);

        $arrayServidores    = json_decode($objJson, true);
        $intCount           = 0;
        foreach($arrayServidores['encontrados'] as $arrayServidores):
            if($arrayServidores['idElemento'] !== '')
            {
                $intCount                = $intCount + 1;
                $arrayResultServidores[] = array('idElemento' =>$arrayServidores['idElemento'], 'nombreElemento' => $arrayServidores['nombreElemento']);
            }
        endforeach;
//        var_dump($arrayServdiores); die();
        
        $objResponse = new Response();
        $objResponse->setContent(json_encode(array('total' => $intCount, 'encontrados' => $arrayResultServidores)));
        $objResponse->headers->set('Content-Type', 'text/json');
        return $objResponse;
    } //ajaxGetElementoServidorAction

    /**
    * getTagsAjaxAction, Obtiene tags 
    * @author Alexander Samaniego <awsamaniego@telconet.ec>
    * @version 1.0 08-03-2015
    * @return json  Obtiene un json con los tags 
    */
    public function getTagsAjaxAction()
    {
        $em             = $this->getDoctrine()->getManager("telconet_infraestructura");
        $arrayTag       = $em->getRepository('schemaBundle:AdmiTag')->findBy(array('estado' => 'Activo'));
        $arrayTagResult = array();
        $intTag         = 0;
        foreach($arrayTag as $arrayTag):
            $intTag           = $intTag + 1;
            $arrayTagResult[] = array('intIdTag' => $arrayTag->getId(), 'strDescripcionTag' => $arrayTag->getDescripcion());
        endforeach;
        $response    = new Response(json_encode(array('jsonTags' => $arrayTagResult, 'intTotalTags' => $intTag)));
        $response->headers->set('Content-type', 'text/json');
        return $response;
    } //getTagsAjaxAction


    /**
    * getTagsByTipoScopeAction, Obtiene array tags 
    * @author Francisco Gonzalez <fgonzalezh@telconet.ec>
    * @version 1.0 07-06-2021
    * @return json  Obtiene un json con los tags de admi_grupo_tag registrados bajo un scope
    */
    public function getTagsByTipoScopeAction()
    {
        $objResponse = new JsonResponse();
        $objResponse->headers->set('Content-Type', 'text/plain');

        $objRequest     = $this->get('request');
        $strTipoScope = $objRequest->get('tipoScope');
        $intIdEmpresa   = $objRequest->getSession()->get('idEmpresa');
        //
        $emInfraestructura = $this->getDoctrine()->getManager("telconet_infraestructura");

        $arrayParametros["strTipoScope"] = $strTipoScope;
        $arrayParametros["strEmpresa"] = $intIdEmpresa;

        $arrayScopes = $emInfraestructura->getRepository('schemaBundle:AdmiGrupoTag')
            ->getTagsByScope($arrayParametros);
        //

        
        $strResponse = new Response(json_encode(array('total' => count($arrayScopes) ,'data' => $arrayScopes)));
        $strResponse->headers->set('Content-type', 'application/json');
        return $strResponse;
    }



    /**
    * getPolicyAjaxAction, Obtiene policies 
    * @author Alexander Samaniego <awsamaniego@telconet.ec>
    * @version 1.0 08-03-2015
    * @return json  Obtiene un json con los policies 
    */
    public function getPolicyAjaxAction()
    {
        $em                 = $this->getDoctrine()->getManager("telconet_infraestructura");
        $arrayPolicy        = $em->getRepository('schemaBundle:AdmiPolicy')->findBy(array('estado' => 'Activo'));
        $arrayPolicyResult  = array();
        foreach($arrayPolicy as $arrayPolicy):
            $arrayPolicyResult[] = array('intIdPolicy' => $arrayPolicy->getId(), 'strPolicy' => $arrayPolicy->getNombrePolicy());
        endforeach;

        $response = new Response(json_encode(array('jsonPolicy' => $arrayPolicyResult)));
        $response->headers->set('Content-type', 'text/json');
        return $response;
    } //getPolicyAjaxAction

    /**
    * getNumerosScopeAjaxAction, numeros de scopes
    * @author Alexander Samaniego <awsamaniego@telconet.ec>
    * @version 1.0 08-03-2015
    * @return json  Obtiene un json con los policies 
    */
    public function getNumerosScopeAjaxAction()
    {
        $em_general     = $this->getDoctrine()->getManager("telconet_general");
        $arrayTipoScope = $em_general->getRepository('schemaBundle:AdmiParametroDet')
                                     ->getOne('CONFIGURACION_SCOPES', '', '', '', 'SCOPE_HASTA', '', '', '');
        $arrayNumerosScopeResult = array();
        for($intNumero = 1; $intNumero <= $arrayTipoScope['valor2']; $intNumero ++):
            $arrayNumerosScopeResult[] = array('intNumeroScope' => $intNumero);
        endfor;
        $response = new Response(json_encode(array('jsonNumerosScope' => $arrayNumerosScopeResult)));
        $response->headers->set('Content-type', 'text/json');
        return $response;
    } //getNumerosScopeAjaxAction

    /**
    * getTipoScopeAjaxAction, obtiene el tipo de scopes
    * @author Alexander Samaniego <awsamaniego@telconet.ec>
    * @version 1.0 08-03-2015
    * @return json  Obtiene un json con el tipo de scopes
    */
    public function getTipoScopeAjaxAction()
    {
        $em_general     = $this->getDoctrine()->getManager("telconet_general");
        $arrayTipoScope = $em_general->getRepository('schemaBundle:AdmiParametroDet')
                                     ->get('CONFIGURACION_SCOPES', '', '', '', 'TIPO_SCOPE', '', '', '');
        $arrayTipoScopeResult = array();
        $intTotalScope = 0;
        foreach($arrayTipoScope as $arrayTipoScope):
            $intTotalScope = $intTotalScope + 1;
            $arrayTipoScopeResult[] = array('strIdTipoScope' => $arrayTipoScope['valor2'], 'strTipoScope' => $arrayTipoScope['valor3']);
        endforeach;
        $response = new Response(json_encode(array('jsonTipoScope' => $arrayTipoScopeResult, 'intTotalScope' => $intTotalScope)));
        $response->headers->set('Content-type', 'text/json');
        return $response;
    } //getTipoScopeAjaxAction

    /**
     * getMascaraPermitidaAjaxAction, obtiene el rango de mascaras permitidas
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 28-03.2015
     * @return json  Obtiene un json con el rango de mascaras permitidas
     */
    public function getMascaraPermitidaAjaxAction()
    {
        $em_general             = $this->getDoctrine()->getManager("telconet_general");
        $arrayMascaraPermitida  = $em_general->getRepository('schemaBundle:AdmiParametroDet')
                                             ->getOne('MASCARA', '', '', '', 'MASCARA', '', '', '');
        $arrayMascaraPermitidaResult = array();
        for($intNumero = $arrayMascaraPermitida['valor2']; $intNumero <= $arrayMascaraPermitida['valor3']; $intNumero ++):
            $arrayMascaraPermitidaResult[] = array('intMascaraPermitida' => $intNumero);
        endfor;
        $response = new Response(json_encode(array('jsonMascaraPermitida' => $arrayMascaraPermitidaResult)));
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }//getMascaraPermitidaAjaxAction

    /**
     * getRangoIpsAjaxAction, obtiene el rango de ips calculadas segun la mascara
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 28-03.2015
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 01-04-2015
     * @since 1.0
     * @return json  Obtiene un json con el rango de ips calculadas segun la mascara
     */
    public function getRangoIpsAjaxAction()
    {
        $objRequest             = $this->getRequest();
        $em_general             = $this->getDoctrine()->getManager("telconet_general");
        $strSubred_             = $objRequest->get("strSubred_");
        $intMascaraPermitida    = $objRequest->get("intMascaraPermitida");
        $strMesanjeError        = '';
        $strValidMask           = filter_var($strSubred_, FILTER_VALIDATE_IP);
        if(empty($strValidMask))
        {
            $strMesanjeError = $strSubred_ . ' no es un ip valida.';
        }
        $arrayMascaraPermitida = $em_general->getRepository('schemaBundle:AdmiParametroDet')
                                            ->getOne('MASCARA', '', '', '', 'MASCARA', '', '', '');

        if($intMascaraPermitida < $arrayMascaraPermitida['valor2'] && $intMascaraPermitida > $arrayMascaraPermitida['valor3'])
        {
            if(empty($strMesanjeError))
            {
                $strMesanjeError = $intMascaraPermitida . ' esta fuera de rango.';
            }
            else
            {
                $strMesanjeError = ' y ' . $intMascaraPermitida . ' esta fuera de rango.';
            }
        }

        $strOutput      = shell_exec('sipcalc ' . $strSubred_ . '/' . $intMascaraPermitida);
        $arrayOutput    = preg_split("#[\r\n]+#", $strOutput);
        if(empty($arrayOutput[13]))
        {
            $strMesanjeError = ' '.$strOutput;
        }
        $arrayRango     = explode("-", $arrayOutput[13]);
        $arrayIpInicio  = explode(".", trim($arrayRango[1]));
        $strIpInicio    = $arrayIpInicio[0].'.'.$arrayIpInicio[1].'.'.$arrayIpInicio[2].'.'.($arrayIpInicio[3] + 1);
        $objResponse    = new Response(json_encode(array('strIpInicio'      => trim($strIpInicio),
                                                         'strIpFin'         => trim($arrayRango[2]),
                                                         'strMask'          => trim(explode("-", $arrayOutput[6])[1]),
                                                         'strMesanjeError'  => $strMesanjeError)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    /**
    * crearScopeAjaxAction, crea scopes
    * @author Alexander Samaniego <awsamaniego@telconet.ec>
    * @version 1.0 08-03-2015
    * @author Alexander Samaniego <awsamaniego@telconet.ec>
    * @version 1.1 13-03-2015
    * @since 1.0
    * @author Alexander Samaniego <awsamaniego@telconet.ec>
    * @version 1.2 28-03-2015
    * @author Jesus Bozada <jbozada@telconet.ec>
    * @version 1.3 01-10-2015
    * @since 1.2
    * @author Antonio Ayala <afayala@telconet.ec>
    * @version 1.4 17-02-2021 Se agrega el tipo de scope Privado 'P'
    * @since 1.3 
    * 
    * @return json  crea los scopes
    */
    /**
     * @Secure(roles="ROLE_227-2237")
     */
    public function crearScopeAjaxAction()
    {
        $objRequest             = $this->getRequest();
        $objSession             = $objRequest->getSession();
        $em                     = $this->getDoctrine()->getManager('telconet_infraestructura');
        $em_general             = $this->getDoctrine()->getManager("telconet_general");
        $objScripts             = $this->get('tecnico.InfoServicioTecnico');
       
        $jsonScope              = $objRequest->get('jsonTagsScope');
        
        $strNombreScope         = $objRequest->get('strNombreScope');
        $strTipoScope           = $objRequest->get('strTipoScope');
        $strSubred_             = $objRequest->get('strSubred_');
        $strSubred              = $objRequest->get('strSubred');
        $strMask                = $objRequest->get('strMask');
        $intIdPolicy            = $objRequest->get('intIdPolicy');
        $strIpInicio            = $objRequest->get('strIpInicio');
        $strIpFin               = $objRequest->get('strIpFin');
        $intNumeroScope         = $objRequest->get('intNumeroScope');
        $intIdElemento          = $objRequest->get('intIdElemento');
        $strEjecutar            = $objRequest->get('strEjecutar');
        $intIdElementoServidor  = $objRequest->get('intIdElementoServidor');
        $intMascaraPermitida    = $objRequest->get("intMascaraPermitida");
        
        $strIpError         = '';
        $intCounterError    = 0;
        $strMessageStatus   = '';
        //
        $arrayScope         = json_decode($jsonScope);
        //

        $strValidSubred_ = filter_var($strSubred_, FILTER_VALIDATE_IP);
        if(empty($strValidSubred_))
        {
            $strIpError = $strSubred_;
            $intCounterError ++;
        }
        $strValidMask = filter_var($strMask, FILTER_VALIDATE_IP);
        if(empty($strValidMask))
        {
            $strIpError = $strIpError . ', ' . $strMask;
            $intCounterError ++;
        }

        if($strTipoScope == 'D' || $strTipoScope == 'S' || $strTipoScope == 'F' || $strTipoScope == 'P' || 
           $strTipoScope == 'SCGN' || $strTipoScope == 'SFP')
        {
            $strValidIpInicio = filter_var($strIpInicio, FILTER_VALIDATE_IP);
            if(empty($strValidIpInicio))
            {
                $strIpError = $strIpError . ', ' . $strIpInicio;
                $intCounterError ++;
            }
            $strValidIpFin = filter_var($strIpFin, FILTER_VALIDATE_IP);
            if(empty($strValidIpFin))
            {
                $strIpError = $strIpError . ', ' . $strIpFin;
                $intCounterError ++;
            }
        }

        if(!empty($strSubred))
        {
            $arraySubred    = explode('/', $strSubred);
            $strValidSubred = filter_var($arraySubred[0], FILTER_VALIDATE_IP);
            if(empty($strValidSubred) || ($arraySubred[1] < 0 || $arraySubred[1] > 255))
            {
                $strIpError = $strIpError . ', ' . $strSubred;
                $intCounterError ++;
            }
        }

        $strSearchComa = '';
        if($intCounterError > 1)
        {
            $strSearchComa = substr($strIpError, 0, 1);
            if($strSearchComa === ',')
            {
                $strIpError = substr($strIpError, 1, strlen($strIpError));
            }
            $strMessageStatus = $strIpError . " no son IP's validas";
        }
        elseif($intCounterError === 1)
        {
            $strSearchComa = substr($strIpError, 0, 1);
            if($strSearchComa === ',')
            {
                $strIpError = substr($strIpError, 1, strlen($strIpError));
            }
            $strMessageStatus = $strIpError . " no es una IP valida";
        }
        if(0 === $intCounterError)
        {
            $em->getConnection()->beginTransaction();
            try
            {
                
                //validar sobre un array
                if('false' === $jsonScope)
                {
                    throw $this->createNotFoundException('Debe ingresar al menos un tag.');
                }
                else
                {
                    if(!empty($intIdElementoServidor))
                    {
                        $strNombrePolicy    = '';
                        $entityPolicy       = $em->getRepository('schemaBundle:AdmiPolicy')->find($intIdPolicy);
                        if($entityPolicy != '')
                        {
                            $strNombrePolicy = $entityPolicy->getNombrePolicy();
                        }
                        else
                        {
                            throw $this->createNotFoundException('No existe Policy.');
                        }
                        $entityInfoElemento = '';
                        if(!empty($intIdElementoServidor))
                        {
                            $entityInfoElemento = $em->getRepository('schemaBundle:InfoElemento')->find($intIdElementoServidor);
                        }
                        if($entityInfoElemento != '')
                        { //$entityInfoElemento
                            $strAccionSeguridad = '';
                            if($intNumeroScope > 1)
                            {
                                $strAccionSeguridad = 'crearScopeExtra';
                            }
                            else
                            {
                                $strAccionSeguridad = 'crearScopePrimario';
                            }
                            $arrayScripts   = $objScripts->obtenerArregloScript($strAccionSeguridad, $entityInfoElemento->getModeloElementoId());
                            $intIdDocumento = $arrayScripts[0]->idDocumento;
                            $strUsuario     = $arrayScripts[0]->usuario;
                            //array
                            $strTags        = '';
                            foreach($arrayScope->data as $objScope):
                               
                                
                                $strTags = $strTags . $objScope->strDescripcionTagBlanck . '|';//debe mantenerse
                            
                            
                            
                            endforeach;
                            
                            
                            
                            if(!empty($strTags))
                            {
                                $strTags = substr($strTags, 0, strlen($strTags)-1);
                            }
                            
                            
                            //--------------------
                            
                            if($intNumeroScope > 1) //$intNumeroScope > 1
                            {
                                $strParametrosCreateScope = $strNombreScope . "," . 
                                                            $strSubred_ . "," . 
                                                            $strMask . "," . 
                                                            $strNombrePolicy . "," . 
                                                            $strTags . "," . 
                                                            $strSubred;
                            }
                            else
                            {
                                $strParametrosCreateScope = $strNombreScope . "," . 
                                                            $strSubred_ . "," . 
                                                            $strMask . "," . 
                                                            $strNombrePolicy . "," . 
                                                            $strTags;
                            }

                            $objResultadoScripts    = $objScripts->ejecutarComandoPersonalizadoMdDatos($entityInfoElemento->getId(), 
                                                                                                       $strUsuario, 
                                                                                                       $strParametrosCreateScope, 
                                                                                                       $intIdDocumento, 
                                                                                                       $strAccionSeguridad);
                            $strStatusCreateScope   = $objResultadoScripts->status;
                            if($strStatusCreateScope != 'ERROR' && !empty($strStatusCreateScope))
                            {
                                //obtener script para agregar rango
                                $arrayScriptsRango   = $objScripts->obtenerArregloScript("agregarRangoScope", 
                                                                                         $entityInfoElemento->getModeloElementoId());
                                $intIdDocumentoRango = $arrayScriptsRango[0]->idDocumento;
                                
                                if($strTipoScope == 'D' || $strTipoScope == 'S' || $strTipoScope == 'SCGN' || $strTipoScope == 'SFP')
                                {
                                    $strParametrosScopeAddRange = $strNombreScope . "," . $strIpInicio . "," . $strIpFin;                                    
                                    $objResultadoScripts1       = $objScripts->ejecutarComandoMdEjecucion($entityInfoElemento->getId(), 
                                                                                              $strUsuario, 
                                                                                              $strParametrosScopeAddRange, 
                                                                                              $intIdDocumentoRango);
                                    
                                    $strStatusScopeAddRange = $objResultadoScripts1->status;
                                    if($strStatusScopeAddRange == 'ERROR')
                                    {
                                        throw $this->createNotFoundException('Existio un error al tratar de agregar un rango al Scope  -' . 
                                                                              $objScripts->mensaje);
                                    }
                                }
                            }
                            else
                            {
                                throw $this->createNotFoundException('Existio un error al tratar de crear el Scope  -' . $objScripts->mensaje);
                            }
                        } //$entityInfoElemento
                        else
                        {
                            throw $this->createNotFoundException('No existe elemento.');
                        }
                    }

                    $arrayTipoScope = $em_general->getRepository('schemaBundle:AdmiParametroDet')
                                                 ->get('CONFIGURACION_SCOPES', '', '', '', 'TIPO_SCOPE', $strTipoScope, '', '');
                    $strTipoScope = 'No exite Tipo Scope';
                    if(!empty($arrayTipoScope[0]['valor3']))
                    {
                        $strTipoScope = $arrayTipoScope[0]['valor3'];
                    }
                    $entityInfoSubred = new InfoSubred();
                    $entityInfoSubred->setSubred($strSubred_);
                    $entityInfoSubred->setMascara($strMask);
                    $entityInfoSubred->setRedId($intMascaraPermitida);
                    $entityInfoElementoOlt = '';
                    if(!empty($intIdElemento))
                    {
                        $entityInfoElementoOlt = $em->getRepository('schemaBundle:InfoElemento')->find($intIdElemento);
                    }
                    else
                    {
                        throw $this->createNotFoundException('No existe id elemento');
                    }
                    $entityInfoSubred->setElementoId($entityInfoElementoOlt);
                    $entityInfoSubred->setNotificacion($intIdPolicy);
                    $entityInfoSubred->setIpInicial($strIpInicio);
                    $entityInfoSubred->setIpFinal($strIpFin);
                    $entityInfoSubred->setUsrCreacion($objSession->get('user'));
                    $entityInfoSubred->setFeCreacion(new \DateTime('now'));
                    $entityInfoSubred->setIpCreacion($objRequest->getClientIp());
                    $entityInfoSubred->setEstado('Activo');
                    $em->persist($entityInfoSubred);
                    $em->flush();

                    $entityInfoDetalleElementoSubred = new InfoDetalleElemento();
                    $entityInfoDetalleElementoSubred->setElementoId($intIdElemento);
                    $entityInfoDetalleElementoSubred->setDetalleNombre('SUBRED');
                    $entityInfoDetalleElementoSubred->setDetalleValor($entityInfoSubred->getId());
                    $entityInfoDetalleElementoSubred->setDetalleDescripcion('SUBRED');
                    $entityInfoDetalleElementoSubred->setUsrCreacion($objSession->get('user'));
                    $entityInfoDetalleElementoSubred->setFeCreacion(new \DateTime('now'));
                    $entityInfoDetalleElementoSubred->setIpCreacion($objRequest->getClientIp());
                    $em->persist($entityInfoDetalleElementoSubred);

                    $entityInfoDetalleElementoScope = new InfoDetalleElemento();
                    $entityInfoDetalleElementoScope->setElementoId($intIdElemento);
                    $entityInfoDetalleElementoScope->setDetalleNombre('SCOPE');
                    $entityInfoDetalleElementoScope->setDetalleValor($strNombreScope);
                    $entityInfoDetalleElementoScope->setDetalleDescripcion('SCOPE');
                    $entityInfoDetalleElementoScope->setUsrCreacion($objSession->get('user'));
                    $entityInfoDetalleElementoScope->setFeCreacion(new \DateTime('now'));
                    $entityInfoDetalleElementoScope->setIpCreacion($objRequest->getClientIp());
                    $entityInfoDetalleElementoScope->setParent($entityInfoDetalleElementoSubred);
                    $em->persist($entityInfoDetalleElementoScope);

                    if(!empty($strSubred))
                    {
                        $entityInfoDetalleElementoSubRedP = new InfoDetalleElemento();
                        $entityInfoDetalleElementoSubRedP->setElementoId($intIdElemento);
                        $entityInfoDetalleElementoSubRedP->setDetalleNombre('SUBRED PRIMARIA');
                        $entityInfoDetalleElementoSubRedP->setDetalleValor($strSubred);
                        $entityInfoDetalleElementoSubRedP->setDetalleDescripcion('SUBRED PRIMARIA');
                        $entityInfoDetalleElementoSubRedP->setUsrCreacion($objSession->get('user'));
                        $entityInfoDetalleElementoSubRedP->setFeCreacion(new \DateTime('now'));
                        $entityInfoDetalleElementoSubRedP->setIpCreacion($objRequest->getClientIp());
                        $entityInfoDetalleElementoSubRedP->setParent($entityInfoDetalleElementoSubred);
                        $em->persist($entityInfoDetalleElementoSubRedP);
                    }

                    $entityInfoDetalleElementoTipoScope = new InfoDetalleElemento();
                    $entityInfoDetalleElementoTipoScope->setElementoId($intIdElemento);
                    $entityInfoDetalleElementoTipoScope->setDetalleNombre('TIPO SCOPE');
                    $entityInfoDetalleElementoTipoScope->setDetalleValor($strTipoScope);
                    $entityInfoDetalleElementoTipoScope->setDetalleDescripcion('TIPO SCOPE');
                    $entityInfoDetalleElementoTipoScope->setUsrCreacion($objSession->get('user'));
                    $entityInfoDetalleElementoTipoScope->setFeCreacion(new \DateTime('now'));
                    $entityInfoDetalleElementoTipoScope->setIpCreacion($objRequest->getClientIp());
                    $entityInfoDetalleElementoTipoScope->setParent($entityInfoDetalleElementoSubred);
                    $em->persist($entityInfoDetalleElementoTipoScope);

                    //Se debe ousar la relacion de tags por scope configurados ( grupo de tags )
                    
                    
                    //recorrer y obtener los tags conigurados denteo de la info_grupo_tag para obtener lo stags
                    
                    
                    
                    
                    foreach($arrayScope->data as $objScope):

                        $entityInfoSubredTag = new InfoSubredTag();
                        $entityInfoSubredTag->setSubredId($entityInfoSubred->getId());
                        $entityInfoSubredTag->setTagId($objScope->intIdTagBlanck);
                        $entityInfoSubredTag->setUsrCreacion($objSession->get('user'));
                        $entityInfoSubredTag->setFeCreacion(new \DateTime('now'));
                        $entityInfoSubredTag->setEstado('Activo');
                        $em->persist($entityInfoSubredTag);
                    endforeach;
                    
                    //-----------------------------
                    
                    //historial elemento de creacion de scope
                    $historialElemento = new InfoHistorialElemento();
                    $historialElemento->setElementoId($entityInfoElementoOlt);
                    $historialElemento->setEstadoElemento($entityInfoElementoOlt->getEstado());
                    $historialElemento->setObservacion("Se ingreso el Scope: ".$strNombreScope. 
                                                       (!empty($intIdElementoServidor)? ", con ejecución de Scritps en el elemento: ".
                                                        $entityInfoElemento->getNombreElemento().".":", sin ejecución de Scripts."));
                    $historialElemento->setUsrCreacion($objSession->get('user'));
                    $historialElemento->setFeCreacion(new \DateTime('now'));
                    $historialElemento->setIpCreacion($objRequest->getClientIp());
                    $em->persist($historialElemento);

                    $em->flush();
                    $em->getConnection()->commit();
                    $strMessageStatus = 'Se creo el scope correctamente.';
                } //$jsonScope
            }
            catch(\Exception $ex)
            {
                $strMessageStatus = 'Existio un error en crearScopeAjaxAction - ' . $ex->getMessage();
                $em->getConnection()->rollback();
                $em->getConnection()->close();
            }
        }
        $objResponse = new Response(json_encode(array('messageStatus' => $strMessageStatus)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    } //crearScopeAjaxAction

    /**
    * eliminarScopeAjaxAction, elimina scopes
    * @author Alexander Samaniego <awsamaniego@telconet.ec>
    * @version 1.0 08-03-2015
    * @author Alexander Samaniego <awsamaniego@telconet.ec>
    * @version 1.1 13-03-2015
    * @author Jesus Bozada <jbozada@telconet.ec>
    * @version 1.2 30-09-2015 Se agrega registro de historial de eliminación de scopes
    * @since 1.0
    * @return json  elimina los scopes
    */
    /**
     * @Secure(roles="ROLE_227-2237")
     */
    public function eliminarScopeAjaxAction()
    {

        $objRequest          = $this->getRequest();
        $objSession          = $objRequest->getSession();
        $em                  = $this->getDoctrine()->getManager('telconet_infraestructura');
        $objScripts          = $this->get('tecnico.InfoServicioTecnico');
        $intIdSubred         = $objRequest->get("intIdSubred");
        $intServidores       = $objRequest->get("intServidores");
        
        if(!empty($intIdSubred))
        {
            $em->getConnection()->beginTransaction();
            try
            {
                $entitySubred = $em->getRepository('schemaBundle:InfoSubred')->find($intIdSubred);
                if('' !== $entitySubred)
                {
                    $intIdElemento = $entitySubred->getElementoId()->getId();
                    if(!empty($intIdElemento))
                    {
                        $entityInfoDetalleElementoSubred = $em->getRepository('schemaBundle:InfoDetalleElemento')
                            ->findOneBy(array('elementoId' => $intIdElemento, 'detalleNombre' => 'SUBRED', 'detalleValor' => $intIdSubred));
                        $entityInfoDetalleElementoScope = $em->getRepository('schemaBundle:InfoDetalleElemento')
                            ->findOneBy(array('elementoId' => $intIdElemento, 'detalleNombre' => 'SCOPE', 
                                              'parent' => $entityInfoDetalleElementoSubred->getId()));
                        $strNombreScope = '';

                        if($entityInfoDetalleElementoScope != '')
                        {
                            $strNombreScope = $entityInfoDetalleElementoScope->getDetalleValor();
                        }
                        if(!empty($strNombreScope))
                        {   
                                if(!empty($intServidores))
                                {
                                    if($entitySubred->getElementoId()->getModeloElementoId() != '')
                                    {
                                        $entityInfoElemento = $em->getRepository('schemaBundle:InfoElemento')->find($intServidores);
                                    }
                                    if($entityInfoElemento != '')
                                    {
                                    $arrayScripts   = $objScripts->obtenerArregloScript('eliminarScope', $entityInfoElemento->getModeloElementoId());
                                    $intIdDocumento = $arrayScripts[0]->idDocumento;
                                    $strUsuario     = $arrayScripts[0]->usuario;
                                    $objResultado   = $objScripts->ejecutarComandoMdEjecucion($entityInfoElemento->getId(), 
                                                                                              $strUsuario, 
                                                                                              $strNombreScope, 
                                                                                              $intIdDocumento);
                                    $strEstatus     = $objResultado->status;
                                    }
                                    else
                                    {
                                        $strMessageStatus = 'No existe modelo elemento';
                                    }
                                }
                                else
                                {
                                    $strMessageStatus   = 'Se elimino el scope.';
                                    $strEstatus         = 'OK';
                                }

                                if($strEstatus != 'ERROR' && !empty($strEstatus))
                                {
                                    $entitySubred->setEstado('Eliminado');
                                    $em->persist($entitySubred);
                                    $em->flush();
                                    
                                    //historial elemento de creacion de scope
                                    $historialElemento = new InfoHistorialElemento();
                                    $historialElemento->setElementoId($entitySubred->getElementoId());
                                    $historialElemento->setEstadoElemento($entitySubred->getElementoId()->getEstado());
                                    $historialElemento->setObservacion("Se elimino el Scope: ".$strNombreScope. 
                                                                       (!empty($intServidores)? ", con ejecución de Scritps en el elemento: ".
                                                                       $entityInfoElemento->getNombreElemento().".":", sin ejecución de Scripts."));
                                    $historialElemento->setUsrCreacion($objSession->get('user'));
                                    $historialElemento->setFeCreacion(new \DateTime('now'));
                                    $historialElemento->setIpCreacion($objRequest->getClientIp());
                                    $em->persist($historialElemento);
                                    $em->flush();
                                    
                                    $em->getConnection()->commit();
                                    $strMessageStatus = 'Se elimino el scope';
                                }
                                else
                                {
                                    $strMessageStatus = $objResultado->mensaje;
                                }
                        }
                    }
                }
                else
                {
                    $strMessageStatus = 'No existe Elemento';
                }
            }
            catch(\Exception $ex)
            {
                $strMessageStatus = 'No se pudo eliminar el scope' . $ex->getMessage();
            }
        }
        else
        {
            $strMessageStatus = 'No existe elemento para poder eliminar scope';
        }
        $objResponse = new Response(json_encode(array('messageStatus' => $strMessageStatus)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    } //eliminarScopeAjaxAction
    
    public function getTipoScopeFromDetalleAjaxAction(){
        $objRequest                             = $this->getRequest();
        $em                                     = $this->getDoctrine()->getManager('telconet_infraestructura');
        $arrayRequest['intIdSubred']            = $objRequest->get("intIdSubred");
        $arrayRequest['arrayStrDetalleNombre']  = ['SUBRED'];
        $arrayRequest['arrayStrDetalleNombre_'] = ['TIPO SCOPE'];
        if(!empty($arrayRequest['intIdSubred']))
        {
            $arrayInfoDetalleElemento = $em->getRepository('schemaBundle:InfoDetalleElemento')->findDetalleScope($arrayRequest);
            if('100' === $arrayInfoDetalleElemento['strStatus'])
            {
                foreach($arrayInfoDetalleElemento['arrayDatos'] as $objInfoDetalleElemento):
                    $arrayResponse['strTipoScope'] = $objInfoDetalleElemento->getDetalleValor();
                endforeach;
            }
            $arrayResponse['strStatus']  = $arrayInfoDetalleElemento['strStatus'];
            $arrayResponse['strMensaje'] = $arrayInfoDetalleElemento['strMensaje'];
        }
        else
        {
            $arrayResponse['strStatus']  = '001';
            $arrayResponse['strMensaje'] = 'No se ha enviado el ID de la subred.';
        }
        $objResponse = new Response(json_encode( (array) $arrayResponse ));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
    
    public function getActualizaIpInicioFinScopeAjaxAction()
    {
        $objRequest                     = $this->getRequest();
        $objSession                     = $objRequest->getSession();
        $objResponse                    = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');
        $em                             = $this->getDoctrine()->getManager('telconet_infraestructura');
        $objScripts                     = $this->get('tecnico.InfoServicioTecnico');
        $arrayResponse['strStatus']     = '000';
        $arrayResponse['strMensaje']    = 'No se realizó';
        $arrayRequest['intIdSubred']    = $objRequest->get("intIdSubred");
        $arrayRequest['strIpInicial']   = $objRequest->get("strIpInicial");
        $arrayRequest['strIpFinal']     = $objRequest->get("strIpFinal");
        $arrayRequest['intServidores']  = $objRequest->get("intServidores");
        $arrayRequest['strNombreScope'] = $objRequest->get("strNombreScope");
        $em->getConnection()->beginTransaction();
        try
        {
            $strIpInicial = filter_var($arrayRequest['strIpInicial'], FILTER_VALIDATE_IP);
            $strIpFinal   = filter_var($arrayRequest['strIpFinal'], FILTER_VALIDATE_IP);
            if(empty($strIpInicial) || empty($strIpFinal))
            {
                $arrayResponse['strStatus']  = '001';
                $arrayResponse['strMensaje'] = 'El formato de la Ip Inicial o Ip Final no es correcto.';
                $objResponse->setContent(json_encode($arrayResponse));
                return $objResponse;
            }
            if(empty($arrayRequest['intIdSubred']))
            {
                $arrayResponse['strStatus']  = '001';
                $arrayResponse['strMensaje'] = 'No envió la subred.';
                $objResponse->setContent(json_encode($arrayResponse));
                return $objResponse;
            }
            if(empty($arrayRequest['strNombreScope']))
            {
                $arrayResponse['strStatus']  = '001';
                $arrayResponse['strMensaje'] = 'No envió el nombre del scope.';
                $objResponse->setContent(json_encode($arrayResponse));
                return $objResponse;
            }
            $entitySubRed    = $em->getRepository('schemaBundle:InfoSubred')->find($arrayRequest['intIdSubred']);
            $strIpInicialOld = $entitySubRed->getIpInicial();
            $strIpFinalOld   = $entitySubRed->getIpFinal();
            if(!$entitySubRed)
            {
                $arrayResponse['strStatus']  = '001';
                $arrayResponse['strMensaje'] = 'No existe la subred.';
                $objResponse->setContent(json_encode($arrayResponse));
                return $objResponse;
            }
            if(!empty($arrayRequest['intServidores']))
            {
                $entityInfoElemento = $em->getRepository('schemaBundle:InfoElemento')->find($arrayRequest['intServidores']);
            }
            $strEstatus = '';
            if($entityInfoElemento != '')
            {
                $arrayScripts              = $objScripts->obtenerArregloScript('eliminarRangoScope', $entityInfoElemento->getModeloElementoId());
                $intIdDocumentoRemoveRange = $arrayScripts[0]->idDocumento;
                $strUsuarioRemoveRange     = $arrayScripts[0]->usuario;
                $strScopeRemoveRange       = $arrayRequest['strNombreScope'] . "," . 
                                             $entitySubRed->getIpInicial() . "," . 
                                             $entitySubRed->getIpFinal();
                $objResultadoRemoveRange   = $objScripts->ejecutarComandoMdEjecucion($entityInfoElemento->getId(), 
                                                                                     $strUsuarioRemoveRange, 
                                                                                     $strScopeRemoveRange, 
                                                                                     $intIdDocumentoRemoveRange);
                $strEstatus = $objResultadoRemoveRange->status;
                if('ERROR' === $strEstatus)
                {
                    throw $this->createNotFoundException('Existio un error al tratar de remover el rango al Scope - ' . 
                                                         $arrayRequest['strNombreScope'] . ' ' . $strEstatus);
                }

                $arrayScriptsRango      = $objScripts->obtenerArregloScript("agregarRangoScope", 
                                                                            $entityInfoElemento->getModeloElementoId());
                $intIdDocumentoAddRange = $arrayScriptsRango[0]->idDocumento;
                $strUsuarioAddRange     = $arrayScripts[0]->usuario;
                $strScopeAddRange       = $arrayRequest['strNombreScope'] . "," . 
                                          $arrayRequest['strIpInicial'] . "," . 
                                          $arrayRequest['strIpFinal'];
                $objResultadoAddRange   = $objScripts->ejecutarComandoMdEjecucion($entityInfoElemento->getId(), 
                                                                                  $strUsuarioAddRange, 
                                                                                  $strScopeAddRange, 
                                                                                  $intIdDocumentoAddRange);

                $strEstatus = $objResultadoAddRange->status;
                if('ERROR' === $strEstatus)
                {
                    throw $this->createNotFoundException('Existio un error al tratar de agregar un rango al Scope  -' . 
                                                         $arrayRequest['strNombreScope'] . ' ' . $strEstatus);
                }
            }
            
            $entitySubRed->setIpInicial($arrayRequest['strIpInicial']);
            $entitySubRed->setIpFinal($arrayRequest['strIpFinal']);
            $em->persist($entitySubRed);
            $em->flush();
            
            $historialElemento = new InfoHistorialElemento();
            $historialElemento->setElementoId($entitySubRed->getElementoId());
            $historialElemento->setEstadoElemento($entitySubRed->getElementoId()->getEstado());
            $historialElemento->setObservacion("Se actualizo el rango de ip Inicial: " . 
                                               $strIpInicialOld . " - Final: ". $strIpFinalOld . " a " .
                                               "Inicial: " .$arrayRequest['strIpInicial'] . " - Final: " . $arrayRequest['strIpFinal']);
            $historialElemento->setUsrCreacion($objSession->get('user'));
            $historialElemento->setFeCreacion(new \DateTime('now'));
            $historialElemento->setIpCreacion($objRequest->getClientIp());
            $em->persist($historialElemento);
            $em->flush();
            
            $em->getConnection()->commit();
            $arrayResponse['strStatus'] = '100';
            $arrayResponse['strMensaje'] = 'Se actualizaron las Ip\'s correctamente.';
        }
        catch(\Exception $ex)
        {
            $em->getConnection()->rollback();
            $em->getConnection()->close();
            $arrayResponse['strStatus'] = '001';
            $arrayResponse['strMensaje'] = 'Error getActualizaIpInicioFinScopeAjaxAction - ' . $ex->getMessage();
        }
        $objResponse->setContent(json_encode($arrayResponse));
        return $objResponse;
    }

//getActualizaIpInicioFinScopeAjaxAction
    
    /**
    * getScopesByNombreElementoAjaxAction, Obtiene los scopes por nombre o por elemento
    * @author Alexander Samaniego <awsamaniego@telconet.ec>
    * @version 1.0 02-09-2015
    * 
    * @author Alexander Samaniego <awsamaniego@telconet.ec>
    * @version 1.1 06-06-2016 Se modifica variable en la que se itera arrayResultado
    * @since 1.0
    * 
    * @return json retorna informacion de los sopes y la subred
    */
    public function getScopesByNombreElementoAjaxAction()
    {
        $objRequest         = $this->getRequest();
        $intLimit           = $objRequest->get("limit");
        $intStart           = $objRequest->get("start");
        $em                 = $this->getDoctrine()->getManager('telconet_infraestructura');
        $arrayParametros    = array('intIdElemento'         => $objRequest->get("intIdElemento"), 
                                    'strNombreScope'        => $objRequest->get("strNombreScope"), 
                                    'strEstado'             => $objRequest->get("strEstado"), 
                                    'arrayDetalleNombre'    => ['SCOPE'], 
                                    'intStart'              => $intStart, 
                                    'intLimit'              => $intLimit);
        //Obtiene los Scopes por nombre o por OLT
        $arrayGetScopes     = $em->getRepository('schemaBundle:InfoDetalleElemento')->findScopesByNombreElemento($arrayParametros);
        //Pregunta que no exista un error.
        if(empty($arrayGetScopes['strMensajeError']))
        {
            //Itera el resultado de la consulta
            foreach($arrayGetScopes['arrayResultado'] as $itemGetScopes):
                $arraySubredResult[] = array('strNombreScope'   => $itemGetScopes['strDetalleValor'],
                                             'strSubred'        => $itemGetScopes['strSubred'],
                                             'strMascara'       => $itemGetScopes['strMascara'],
                                             'strIpInicial'     => $itemGetScopes['strIpInicial'],
                                             'strEstado'        => $itemGetScopes['strEstado'],
                                             'strIpFinal'       => $itemGetScopes['strIpFinal'],
                                             'intIdSubred'      => $itemGetScopes['intIdSubred'],
                                             'strPolicy'        => $itemGetScopes['intNotificacion']);
            endforeach;
        }
        $objResponse = new Response(json_encode(array('intTotal'        => $arrayGetScopes['intTotal'], 
                                                      'jsonScopesOLT'   => $arraySubredResult, 
                                                      'strMensajeError' => $arrayGetScopes['strMensajeError'])));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    } //getScopesByNombreElementoAjaxAction
    
   /**
    * getTiposDetalleElementoAjaxAction, Obtiene los tipos de detalles que tiene un olt registrado
    * @author Jesus Bozada <jbozada@telconet.ec>
    * @version 1.0 19-01-2016
    * @return json retorna informacion de los tipos de detalles
    */
    public function getTiposDetalleElementoAjaxAction()
    {
        set_time_limit(400000);
        $objRequest         = $this->getRequest();
        $em                 = $this->getDoctrine()->getManager('telconet_infraestructura');
        $arrayParametros    = array('intIdElemento'         => $objRequest->get("idElemento"), 
                                    'strTipoDetalle'        => $objRequest->get("tipoDetalle")
                                   );
        //Obtiene los Tipos de detalles por OLT
        $arrayGetTiposDetalles     = $em->getRepository('schemaBundle:InfoDetalleElemento')->getResultadoTiposDetalleElemento($arrayParametros);
        
        $objResponse = new Response(json_encode(array('total'         => count($arrayGetTiposDetalles['arrayResultado']), 
                                                      'encontrados'   => $arrayGetTiposDetalles['arrayResultado'] )));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    } //getScopesByNombreElementoAjaxAction
    
    /**
    * getDetallesElementoAjaxAction, Obtiene los detalles de elemento que tiene un olt registrado
    * @author Jesus Bozada <jbozada@telconet.ec>
    * @version 1.0 19-01-2016
    * @return json retorna informacion de los tipos de detalles
    */
    public function getDetallesElementoAjaxAction()
    {
        $objRequest         = $this->getRequest();
        $intTotalRegistros  = 0;
        $arrayResult        = null;
        $em                 = $this->getDoctrine()->getManager('telconet_infraestructura');
        $arrayParametros    = array('intIdElemento'         => $objRequest->get("idElemento"), 
                                    'strNombreDetalle'      => $objRequest->get("nombreDetalle"),
                                    'strValorDetalle'       => $objRequest->get("valorDetalle"),
                                    'strDescripcionDetalle' => $objRequest->get("descripcion"),
                                    'strFechaDesde'         => explode('T',$objRequest->get("fechaDesde")),
                                    'strFechaHasta'         => explode('T',$objRequest->get("fechaHasta"))
                                   );
        //Obtiene los Tipos de detalles por OLT
        $arrayGetTiposDetalles     = $em->getRepository('schemaBundle:InfoDetalleElemento')->getResultadoDetallesElemento($arrayParametros);
        
        //Itera el array de los datos obtenidos
            foreach($arrayGetTiposDetalles['arrayResultado'] as $arrayDetalle):
                $arrayResult[] = array('DETALLE_ELEMENTO'    => $arrayDetalle['DETALLE_ELEMENTO'],
                                       'DETALLE_VALOR'       => $arrayDetalle['DETALLE_VALOR'],
                                       'DETALLE_DESCRIPCION' => $arrayDetalle['DETALLE_DESCRIPCION'],
                                       'DETALLE_FECREACION'  => $arrayDetalle['DETALLE_FECREACION']->format('d-M-Y'),
                                       'DETALLE_USRCREACION' => $arrayDetalle['DETALLE_USRCREACION']);
            endforeach;
        
        $objResponse = new Response(json_encode(array('total'         => count($arrayGetTiposDetalles['arrayResultado']), 
                                                      'encontrados'   => $arrayResult)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    } //getDetallesElementoAjaxAction
    
    /**
    * asignarScopeAjaxAction, Clona el Scope y sus caracteristicas a un nuevo OLT
    * @author Alexander Samaniego <awsamaniego@telconet.ec>
    * @version 1.0 02-09-2015
    * @return json retorna un mensaje de notificacion del proceso.
    */
    public function asignarScopeAjaxAction()
    {

        $objRequest          = $this->getRequest();
        $objSession          = $objRequest->getSession();
        $em                  = $this->getDoctrine()->getManager('telconet_infraestructura');
        $intIdSubred         = $objRequest->get("intIdSubred");
        $intIdElementoScope  = $objRequest->get("intIdElementoOLT");
        //Valida que el $intIdSubred y el $intIdElementoScope no sean nulos.
        if(!empty($intIdSubred) && !empty($intIdElementoScope))
        {
            $em->getConnection()->beginTransaction();
            try
            {
                //Busca en la entidad InfoDetalleElemento la Subred
                $entitySubred = $em->getRepository('schemaBundle:InfoDetalleElemento')->findOneBy(array('detalleNombre' => 'SUBRED',
                                                                                                        'detalleValor'  => $intIdSubred));
                //Si no existe Subred lanza una excepcion
                if(!$entitySubred)
                {
                    throw $this->createNotFoundException('No existe la Subred.');
                }
                //Clona la Subred para el nuevo OLT
                $entitySubredClone = clone $entitySubred;
                $entitySubredClone->setElementoId($intIdElementoScope);
                $entitySubredClone->setUsrCreacion($objSession->get('user'));
                $entitySubredClone->setFeCreacion(new \DateTime('now'));
                $entitySubredClone->setIpCreacion($objRequest->getClientIp());
                $em->persist($entitySubredClone);
                $em->flush();
                
                //Busca en la entidad InfoDetalleElemento el nombre del Scope
                $entityScope = $em->getRepository('schemaBundle:InfoDetalleElemento')->findOneBy(array('detalleNombre' => 'SCOPE',
                                                                                                       'parent'        => $entitySubred->getId()));
                //Si no existe Scope lanza una excepcion
                if(!$entityScope)
                {
                    throw $this->createNotFoundException('Scope no existente.');
                }
                //Clona la Scope para el nuevo OLT
                $entityScopeClone = clone $entityScope;
                $entityScopeClone->setElementoId($intIdElementoScope);
                $entityScopeClone->setUsrCreacion($objSession->get('user'));
                $entityScopeClone->setFeCreacion(new \DateTime('now'));
                $entityScopeClone->setIpCreacion($objRequest->getClientIp());
                $entityScopeClone->setParent($entitySubredClone);
                $em->persist($entityScopeClone);
                
                //Busca en la entidad InfoDetalleElemento la Subred Primaria
                $entitySubredPrimaria = $em->getRepository('schemaBundle:InfoDetalleElemento')
                                           ->findOneBy(array('detalleNombre' => 'SUBRED PRIMARIA',
                                                             'parent'        => $entitySubred->getId()));
                //Si existe clona la Subred Primaria
                if($entitySubredPrimaria)
                {
                    //Clona la Subred Primaria
                    $entitySubredPrimariaClone = clone $entitySubredPrimaria;
                    $entitySubredPrimariaClone->setElementoId($intIdElementoScope);
                    $entitySubredPrimariaClone->setUsrCreacion($objSession->get('user'));
                    $entitySubredPrimariaClone->setFeCreacion(new \DateTime('now'));
                    $entitySubredPrimariaClone->setIpCreacion($objRequest->getClientIp());
                    $entitySubredPrimariaClone->setParent($entitySubredClone);
                    $em->persist($entitySubredPrimariaClone);
                }
                //Busca el Tipo de Scope
                $entityTipoScope = $em->getRepository('schemaBundle:InfoDetalleElemento')->findOneBy(array('detalleNombre' => 'TIPO SCOPE',
                                                                                                           'parent'        => $entitySubred->getId()));
                //Si no existe el Tipo de Scope lanza una excepcion
                if(!$entityTipoScope)
                {
                    throw $this->createNotFoundException('No existe el Tipo de Scope.');
                }
                //Clona el Tipo de Scope
                $entityTipoScopeClone = clone $entityTipoScope;
                $entityTipoScopeClone->setElementoId($intIdElementoScope);
                $entityTipoScopeClone->setUsrCreacion($objSession->get('user'));
                $entityTipoScopeClone->setFeCreacion(new \DateTime('now'));
                $entityTipoScopeClone->setIpCreacion($objRequest->getClientIp());
                $entityTipoScopeClone->setParent($entitySubredClone);
                $em->persist($entityTipoScopeClone);
                
                $em->flush();
                $em->getConnection()->commit();
                $strMessageStatus = 'Se asigno el scope correctamente.';
            }
            catch(\Exception $ex)
            {
                $strMessageStatus = 'No se pudo asignar el scope' . $ex->getMessage();
                $em->getConnection()->rollback();
                $em->getConnection()->close();
            }
        }
        else
        {
            $strMessageStatus = 'No está enviando el Scope o el OLT.';
        }
        $objResponse = new Response(json_encode(array('strMessageStatus' => $strMessageStatus)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    } //asignarScopeAjaxAction
    
    /**
     * @author David Montufar <dmontufar@telconet.ec>
     * @since 13 de diciembre del 2013
     * @param type $idOlt
     * @return json Pool de IPs del OLT seleccionado
     */
    public function showPoolIPAction($idElemento) {
        
        $request = $this->getRequest();
        $start = $request->get('start', 0);
        $limit = $request->get('limit', 10);
        
        $response = new Response();
        $response->headers->set('Content-type', 'text/json');
        
        $em = $this->get('doctrine')->getManager('telconet_infraestructura');	
        $reporsitory = $em->getRepository('schemaBundle:InfoSubred');
        $entities = $reporsitory->findBy(array('elementoId' => $idElemento, 'estado'=>'A'),array('id' => 'DESC'),$limit,$start);
        $total = $reporsitory->findBy(array('elementoId' => $idElemento, 'estado'=>'A'));
        
        $arrayResponse = array();
        $arrayResponse['total'] = count($total);
        $arrayResponse['pools'] = array();
        

        $reporsitoryElDet = $em->getRepository('schemaBundle:InfoDetalleElemento');

        foreach($entities as $entity){
	    $arrayEntity = array();
	    $arrayEntity['id'] = $entity->getId();
	    $arrayEntity['ipInicial'] = $entity->getIpInicial();
	    $arrayEntity['ipFinal'] = $entity->getIpFinal();
            $arrayEntity['notificacion'] = $entity->getNotificacion();
            $arrayEntity['idElemento'] = $idElemento;
            
            //Obtengo el padre de la subred en la tabla INFO_DETALLE_ELEMENTO
            $padreSubred = $reporsitoryElDet->findOneBy(
                    array('detalleNombre' => 'SUBRED', 'elementoId' => $idElemento, 
                        'detalleValor'=>$entity->getId()));
            
            if($padreSubred!=null){
               $paquete = $reporsitoryElDet->findOneBy(
                    array('parent' => $padreSubred->getId(), 'detalleNombre' => 'PLAN' )); 
               if($paquete!=null){
                   $arrayEntity['idPaquete'] =  $paquete->getDetalleValor();
               } else {
                   $arrayEntity['idPaquete'] = '';
               }

               $perfil = $reporsitoryElDet->findOneBy(
                    array('parent' => $padreSubred->getId(), 'detalleNombre' => 'PERFIL' )); 
               if($perfil!=null){
                   $arrayEntity['idPerfil'] =  $perfil->getDetalleValor();
               } else {
                   $arrayEntity['idPerfil'] = '';
               }
               
            } else {
               $arrayEntity['idPaquete'] = '';
               $arrayEntity['idPerfil'] = '';
            }
            
            $paquete = null;
            $padreSubred = null;
            
	    $arrayResponse['pools'][]= $arrayEntity;
        }
        $response->setContent(json_encode($arrayResponse));
        return $response;	
    }
    
    /**
     * Retorna el listado de los perfiles en formato json para que sea cargado via ajax 
     * en el combo perfiles
     * @author David Montufar <dmontufar@telconet.ec>
     * @since 19 de diciembre del 2013
     * @return json listado de perfiles
     */
    public function getPerfilesAction($idElemento) {
        
        $response = new Response();
        $response->headers->set('Content-type', 'text/json');
        
        $arrayResponse = array();
        /*
        for($i=1; $i<5; $i++){
	    $arrayEntity = array();
	    $arrayEntity['idPerfil'] = $i;
	    $arrayEntity['nombrePerfil'] = "Perfil ".$i;
	    $arrayResponse['perfiles'][]= $arrayEntity;
        }
         * // SELECT * FROM info_detalle_elemento where elemento_id = 34740 and detalle_nombre = 'PERFIL' and ref_detalle_elemento_id is null;
        */
        
        $em = $this->get('doctrine')->getManager('telconet_infraestructura');	
        $reporsitory = $em->getRepository('schemaBundle:InfoDetalleElemento');
        $perfilesElemento = $reporsitory->findBy(array('elementoId' => $idElemento, 'detalleNombre'=>'PERFIL',
                                                        'parent'=>null), array('detalleValor' => 'ASC'));
        
        foreach ($perfilesElemento as $perfil) {
            $arrayEntity = array();
	    $arrayEntity['idPerfil'] = $perfil->getDetalleValor();
	    $arrayEntity['nombrePerfil'] = $perfil->getDetalleValor();
	    $arrayResponse['perfiles'][]= $arrayEntity;
        }
        
        $response->setContent(json_encode($arrayResponse));
        
        return $response;	
    }
    
    /**
     * Retorna el listado de los perfiles en formato json para que sea cargado via ajax 
     * en el combo perfiles
     * @author David Montufar <dmontufar@telconet.ec>
     * @since 23 de diciembre del 2013
     * @return json tipos de paquetes.
     */
    public function getPaquetesAction() {
        
        $response = new Response();
        $response->headers->set('Content-type', 'text/json');
        
        $arrayResponse = array();
        
        $arrayResponse['paquetes'][] = array('idPaquete'=>'HOME','nombrePaquete'=>'HOME');
        $arrayResponse['paquetes'][] = array('idPaquete'=>'PYME','nombrePaquete'=>'PYME');
        $arrayResponse['paquetes'][] = array('idPaquete'=>'PRO','nombrePaquete'=>'PRO');
            
        $response->setContent(json_encode($arrayResponse));
        
        return $response;	
    }
    
     /**
     * Crea nuevo pool de ip.
     * @author David Montufar <dmontufar@telconet.ec>
     * @since 05 de enero de 2014
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function addPoolAction($idPaquete, $idPerfil, $ipInicial, $ipFinal, $notificacion, $idElemento) {

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');

        $peticion = $this->get('request');
        $session  = $peticion->getSession();
        
        $infoSubredEntity = new \telconet\schemaBundle\Entity\InfoSubred;
        $infoSubredEntity->setUsrCreacion($session->get('user'));
        $infoSubredEntity->setFeCreacion(new \DateTime('now'));
        $infoSubredEntity->setIpCreacion($_SERVER['REMOTE_ADDR']);
        $infoSubredEntity->setEstado('A');
        $infoElementoEntity = $em->getRepository('schemaBundle:InfoElemento')->find($idElemento);
        $infoSubredEntity->setElementoId($infoElementoEntity);
        $infoSubredEntity->setNotificacion($notificacion);
        $infoSubredEntity->setIpInicial($ipInicial);
        $infoSubredEntity->setIpFinal($ipFinal);
        $infoSubredEntity->setIpDisponible($ipInicial);
        $em->persist($infoSubredEntity);
        
        /*  Creacion de padre en InfoDetalleElemento */
        $infoDetalleElemento = new \telconet\schemaBundle\Entity\InfoDetalleElemento;
        $infoDetalleElemento->setElementoId($idElemento);
        $infoDetalleElemento->setDetalleNombre('SUBRED');
        $infoDetalleElemento->setDetalleDescripcion('SUBRED');
        $infoDetalleElemento->setUsrCreacion($session->get('user'));
        $infoDetalleElemento->setFeCreacion(new \DateTime('now'));
        $infoDetalleElemento->setIpCreacion($_SERVER['REMOTE_ADDR']);
        //$infoDetalleElemento->setParent(null);
        $infoDetalleElemento->setDetalleValor($infoSubredEntity->getId());

        
        /* Creacion de hijos en InfoDetalleElemento */
        //Plan
        $infoDetalleElementoPlan = new \telconet\schemaBundle\Entity\InfoDetalleElemento;
        $infoDetalleElementoPlan->setDetalleNombre('PLAN');
        $infoDetalleElementoPlan->setDetalleValor($idPaquete);
        $infoDetalleElementoPlan->setDetalleDescripcion('PLAN');
        $infoDetalleElementoPlan->setUsrCreacion($session->get('user'));
        $infoDetalleElementoPlan->setFeCreacion(new \DateTime('now'));
        $infoDetalleElementoPlan->setIpCreacion($_SERVER['REMOTE_ADDR']);
        $infoDetalleElementoPlan->setParent($infoDetalleElemento);
        $infoDetalleElementoPlan->setElementoId($idElemento);

        //Perfil   
        $infoDetalleElementoPerfil = new \telconet\schemaBundle\Entity\InfoDetalleElemento;
        $infoDetalleElementoPerfil->setDetalleNombre('PERFIL');
        $infoDetalleElementoPerfil->setDetalleValor($idPerfil);
        $infoDetalleElementoPerfil->setDetalleDescripcion('PERFIL');
        $infoDetalleElementoPerfil->setUsrCreacion($session->get('user'));
        $infoDetalleElementoPerfil->setFeCreacion(new \DateTime('now'));
        $infoDetalleElementoPerfil->setIpCreacion($_SERVER['REMOTE_ADDR']);
        $infoDetalleElementoPerfil->setParent($infoDetalleElemento);
        $infoDetalleElementoPerfil->setElementoId($idElemento);
        
        //se agrega codigo para llevar rastro de las actualizaciones de pool de ips
        $idElementoEntidad = $em->getRepository('schemaBundle:InfoElemento')->find($idElemento);
        $historialElemento = new \telconet\schemaBundle\Entity\InfoHistorialElemento;
        $historialElemento->setElementoId($idElementoEntidad);
        $historialElemento->setEstadoElemento("Activo");
        $historialElemento->setObservacion("Se creo pool de ips, Paquete: ".$idPaquete." Perfil: ".$idPerfil." ".", Ip Inicial: ".$ipInicial.", Ip Final: ".$ipFinal.", Notificaciones: ".$notificacion);
        $historialElemento->setUsrCreacion($session->get('user'));
        $historialElemento->setFeCreacion(new \DateTime('now'));
        $historialElemento->setIpCreacion($_SERVER['REMOTE_ADDR']);
        
        //se agrega codigo para llevar rastro de las actualizaciones de pool de ips
        $em->persist($historialElemento);
        $em->persist($infoDetalleElemento);
        $em->persist($infoDetalleElementoPlan);
        $em->persist($infoDetalleElementoPerfil);
        

        $em->flush();
        return new Response("Creado. $idPaquete,$idPerfil,$ipInicial,$ipFinal,$notificacion,$idElemento--" . $_SERVER['REMOTE_ADDR']);
    }

    /**
     * Actualiza los registros de pool de ips.
     * @author David Montufar <dmontufar@telconet.ec>
     * @since 05 de enero de 2014
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updatePoolAction($idElemento) {
        
        $peticion = $this->get('request');
        $session  = $peticion->getSession();
        
        $put_str = $this->getRequest()->getContent();
        parse_str($put_str, $_PUT);
        $idInfoSubred = $_PUT['id'];
        $idPaquete = $_PUT['idPaquete'];
        $idPerfil = $_PUT['idPerfil'];
        $ipInicial = $_PUT['ipInicial'];
        $ipFinal = $_PUT['ipFinal'];
        $notificacion = $_PUT['notificacion'];

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        
        $info_subred = $em->getRepository('schemaBundle:InfoSubred')->find($idInfoSubred);

        if (!$info_subred) { // si es un nuevo pool de ip.
            return $this->addPoolAction($idPaquete,$idPerfil,$ipInicial,$ipFinal,$notificacion,$idElemento);
        } else {
            $info_subred->setIpInicial($ipInicial);
            $info_subred->setIpFinal($ipFinal);
            $info_subred->setNotificacion($notificacion);
            
            $reporsitory = $em->getRepository('schemaBundle:InfoDetalleElemento');

            $padreSubred = $reporsitory->findOneBy(
                    array('detalleNombre' => 'SUBRED', 'elementoId' => $info_subred->getElementoId(),
                        'detalleValor' => $idInfoSubred));

            if ($padreSubred != null) {
                $paquete = $reporsitory->findOneBy(
                        array('parent' => $padreSubred->getId(), 'detalleNombre' => 'PLAN'));
                if ($paquete != null) {
                    $paquete->setDetalleValor($idPaquete);
                    $em->persist($paquete);
                } else {
                    $infoDetalleElementoPlan = new \telconet\schemaBundle\Entity\InfoDetalleElemento;
                    $infoDetalleElementoPlan->setDetalleNombre('PLAN');
                    $infoDetalleElementoPlan->setDetalleValor($idPaquete);
                    $infoDetalleElementoPlan->setDetalleDescripcion('PLAN');
                    $infoDetalleElementoPlan->setUsrCreacion($session->get('user'));
                    $infoDetalleElementoPlan->setFeCreacion(new \DateTime('now'));
                    $infoDetalleElementoPlan->setIpCreacion($_SERVER['REMOTE_ADDR']);
                    $infoDetalleElementoPlan->setParent($padreSubred);
                    $infoDetalleElementoPlan->setElementoId($idElemento);
                }
                
                $perfil = $reporsitory->findOneBy(
                        array('parent' => $padreSubred->getId(), 'detalleNombre' => 'PERFIL'));
                if ($perfil != null) {
                    $perfil->setDetalleValor($idPerfil);
                    $em->persist($perfil);
                } else {
                    $infoDetalleElementoPerfil = new \telconet\schemaBundle\Entity\InfoDetalleElemento;
                    $infoDetalleElementoPerfil->setDetalleNombre('PERFIL');
                    $infoDetalleElementoPerfil->setDetalleValor($idPerfil);
                    $infoDetalleElementoPerfil->setDetalleDescripcion('PERFIL');
                    $infoDetalleElementoPerfil->setUsrCreacion($session->get('user'));
                    $infoDetalleElementoPerfil->setFeCreacion(new \DateTime('now'));
                    $infoDetalleElementoPerfil->setIpCreacion($_SERVER['REMOTE_ADDR']);
                    $infoDetalleElementoPerfil->setParent($padreSubred);
                    $infoDetalleElementoPerfil->setElementoId($idElemento);
                }
                
                //se agrega codigo para llevar rastro de las actualizaciones de pool de ips
                $idElementoEntidad = $em->getRepository('schemaBundle:InfoElemento')->find($idElemento);
                $historialElemento = new \telconet\schemaBundle\Entity\InfoHistorialElemento;
                $historialElemento->setElementoId($idElementoEntidad);
                $historialElemento->setEstadoElemento("Activo");
                $historialElemento->setObservacion("Se actualizo pool de ips, Paquete: ".$idPaquete." Perfil: ".$idPerfil." ".", Ip Inicial: ".$ipInicial.", Ip Final: ".$ipFinal.", Notificaciones: ".$notificacion);
                $historialElemento->setUsrCreacion($session->get('user'));
                $historialElemento->setFeCreacion(new \DateTime('now'));
                $historialElemento->setIpCreacion($_SERVER['REMOTE_ADDR']);
                $em->persist($historialElemento);
                
                $em->flush();
            } else {
                $this->nuevoInfoDetalleElemento($idInfoSubred, $idElemento, $idPaquete, $idPerfil, $session->get('user') , $_SERVER['REMOTE_ADDR']);
            }
        }
        return new Response('Updated');
    }

    /**
     * Deshabilita pool de ip.
     * @author David Montufar <dmontufar@telconet.ec>
     * @since 05 de enero de 2014
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deletePoolAction($idSubred) {

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $info_subred = $em->getRepository('schemaBundle:InfoSubred')->find($idSubred);

        if (!$info_subred) {
            throw $this->createNotFoundException('No fue encontrada la sub-red a eliminar ' . $idSubred);
        } else {
            $info_subred->setEstado("E");
            if ($em->flush()) {
                return new Response("deleted ");
            } else {
                return new Response("error ");
            }
        }
    }
    
     /**
     * Si no existe la información de plan y perfil registrada en la tabla info_detalle_elemento
     * se procede a crear 3 registros:
     * 1) El primer registro (padre), que contiene informacion de la subred y el OLT
     * 2) El segundo registro (hijo), que contiene la información del Plan para la subred
     * 3) El tercer registro (hijo), que contiene la información del Perfil de la subred
     * @author David Montufar <dmontufar@telconet.ec>
     * @since 05 de enero de 2014
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function nuevoInfoDetalleElemento($idSubred, $idElemento, $idPaquete, $idPerfil, $user , $ipRemota) {
        
        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        /*  Creacion de padre en InfoDetalleElemento */
        $infoDetalleElemento = new \telconet\schemaBundle\Entity\InfoDetalleElemento;
        $infoDetalleElemento->setElementoId($idElemento);
        $infoDetalleElemento->setDetalleNombre('SUBRED');
        $infoDetalleElemento->setDetalleDescripcion('SUBRED');
        $infoDetalleElemento->setUsrCreacion($user);
        $infoDetalleElemento->setFeCreacion(new \DateTime('now'));
        $infoDetalleElemento->setIpCreacion($ipRemota);
        //$infoDetalleElemento->setParent(null);
        $infoDetalleElemento->setDetalleValor($idSubred);

        
        /* Creacion de hijos en InfoDetalleElemento */
        //Plan
        $infoDetalleElementoPlan = new \telconet\schemaBundle\Entity\InfoDetalleElemento;
        $infoDetalleElementoPlan->setDetalleNombre('PLAN');
        $infoDetalleElementoPlan->setDetalleValor($idPaquete);
        $infoDetalleElementoPlan->setDetalleDescripcion('PLAN');
        $infoDetalleElementoPlan->setUsrCreacion($user);
        $infoDetalleElementoPlan->setFeCreacion(new \DateTime('now'));
        $infoDetalleElementoPlan->setIpCreacion($ipRemota);
        $infoDetalleElementoPlan->setParent($infoDetalleElemento);
        $infoDetalleElementoPlan->setElementoId($idElemento);

        //Perfil   
        $infoDetalleElementoPerfil = new \telconet\schemaBundle\Entity\InfoDetalleElemento;
        $infoDetalleElementoPerfil->setDetalleNombre('PERFIL');
        $infoDetalleElementoPerfil->setDetalleValor($idPerfil);
        $infoDetalleElementoPerfil->setDetalleDescripcion('PERFIL');
        $infoDetalleElementoPerfil->setUsrCreacion($user);
        $infoDetalleElementoPerfil->setFeCreacion(new \DateTime('now'));
        $infoDetalleElementoPerfil->setIpCreacion($ipRemota);
        $infoDetalleElementoPerfil->setParent($infoDetalleElemento);
        $infoDetalleElementoPerfil->setElementoId($idElemento);
        
        $em->persist($infoDetalleElemento);
        $em->persist($infoDetalleElementoPlan);
        $em->persist($infoDetalleElementoPerfil);

        $em->flush();
    }
    
    /**
     * Después de editar algún pool de ips, mediante el siguiente método
     * se toma los nuevos valores de ip y final, con estos valores se busca 
     * las subredes con las que hay cruce.
     * Función llamada en el método js afteredit del objeto rowediting de del gripPool.
     * @author David Montufar <dmontufar@telconet.ec>
     * @since 10 de enero de 2014
     * @param type $ipInicial
     * @param type $ipFinal
     * @return boolean
     */
    public function validaCruceIpAction($idSubred, $ipInicial, $ipFinal) {
        $octetosIpInicialEdit = explode(".",$ipInicial);
        $octetosIpFinalEdit = explode(".",$ipFinal);	
               
        $em = $this->get('doctrine')->getManager('telconet_infraestructura');	
        $qb = $em->createQueryBuilder();
        $qb->select('a')
            ->from('schemaBundle:InfoSubred', 'a')
            ->where("a.ipInicial LIKE :ip_inicial")
            ->andWhere("a.id <> :id_subred")
            ->andWhere("a.estado = 'A'")
            ->setParameter('ip_inicial', $octetosIpInicialEdit[0].".".$octetosIpInicialEdit[1].".".$octetosIpInicialEdit[2].".".'%')
            ->setParameter('id_subred', $idSubred);

        $query = $qb->getQuery();               
        $pools = $query->getResult();
        $cruce = false;
        $nombreOlt = '';
        
        foreach ($pools as $pool) {
            $octetosIpInicialFind = explode(".", $pool->getIpInicial());
            $octetosIpFinalFind = explode(".", $pool->getIpFinal());
            
            if(($octetosIpInicialEdit[3] >= $octetosIpInicialFind[3] && $octetosIpInicialEdit[3] <= $octetosIpFinalFind[3])
                    || ($octetosIpFinalEdit[3] >= $octetosIpInicialFind[3] && $octetosIpFinalEdit[3] <= $octetosIpFinalFind[3])) {
                $nombreOlt = "Cruece de ip con el elemnto: ".$pool->getElementoId()->getNombreElemento();
                $cruce = true;
            }
            
            if($octetosIpInicialEdit[3] <= $octetosIpInicialFind[3] && $octetosIpFinalEdit[3] >= $octetosIpFinalFind[3]){
                $nombreOlt = "Cruece de ip con el elemnto: ".$pool->getElementoId()->getNombreElemento();
                $cruce = true;
            }
        }
        
        /* Valida si la ip es privada */
        $ipIniPrivada = $this->ip_is_private($ipInicial);
        $ipFinPrivada = $this->ip_is_private($ipFinal);
        
        if($ipIniPrivada){
            $nombreOlt .= "\n<br /> Ip inicial $ipInicial NO válida.";
            $cruce = true;
        }
        
        if($ipFinPrivada){
            $nombreOlt .= "\n<br /> Ip final $ipFinal NO válida.";
            $cruce = true;
        }
        
        $response = new Response();
        $response->headers->set('Content-type', 'text/json');
        $response->setContent(json_encode(array('olt'=>$nombreOlt,'cruce'=>$cruce)));
        
        return $response;	
    }
    
    /**
     * Función que devuelve verdadero si la ip provista como parámetro es privada, caso 
     * contrario devuelve falso.
     * 
     * @param type $ip Ip a validar si es publica o privada
     * @return string
     */
    private function ip_is_private($ip) {
        $pri_addrs = array(
            '1.0.0.0|1.255.255.255',
            '2.0.0.0|2.255.255.255',
            '3.0.0.0|3.255.255.255',
            '4.0.0.0|4.255.255.255',
            '5.0.0.0|5.255.255.255',
            '6.0.0.0|6.255.255.255',
            '7.0.0.0|7.255.255.255',
            '8.0.0.0|8.255.255.255',
            '9.0.0.0|9.255.255.255',
            //siguientes son IPs Publicas
            '10.0.0.0|10.255.255.255',
            '172.16.0.0|172.31.255.255',
            '192.168.0.0|192.168.255.255',
            '169.254.0.0|169.254.255.255',
            '127.0.0.0|127.255.255.255'
        );

        $long_ip = ip2long($ip);
        if ($long_ip != -1) {

            foreach ($pri_addrs AS $pri_addr) {
                list($start, $end) = explode('|', $pri_addr);

                // IF IS PRIVATE
                if ($long_ip >= ip2long($start) && $long_ip <= ip2long($end))
                    return true;
            }
        }

        return false;
    }
    
    /**
     * @Secure(roles="ROLE_227-2537")
     * 
     * Documentación para el método 'reservarIpsOlt'.
     *
     * Metodo utilizado para las ips de clientes que se encuentran en activos o in-corte dentro del Olt
     * @param integer $id
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 02-04-2015
     * @version 1.1 11-09-2015 John Vera
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.2 13-09-2015 Alexander Samaniego
     * @since 1.1
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.3 21-01-2016 Alexander Samaniego
     * @since 1.2
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.4 26-01-2016 Alexander Samaniego
     * @since 1.3
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.5 05-02-2016 Se modifica el metodo de respuesta al ejecutar el jar md_datos ya que se lo hará con nohup, se elimino la parte de 
     * envio de correo cuando se esperaba el resulta de la configuracion de ip
     * @since 1.4
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.6 02-18-2016 Se envia como parametro el elemento OLT a la linea de ejecucion del jar
     * @since 1.5
     * author Eduardo Plua <eplua@telconet.ec>
     * @version 1.6 26-05-2016 - Se recupera elementoPe desde ws networking
     * 
     * author Modificado: Duval Medina C. <dmedina@telconet.ec>
     * @version 1.7 2016-06-03 - Se incluye loginForma en null para llamado de generarJsonClientes()
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.8 01-04-2019 - Se envía el parámetro $serviceCliente a la función generarJsonClientes
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.9 17-04-2019 - Se cambia el estado de "Activo" a "Reservada" de la caracteristica SCOPE creada en la reserva masiva de ips por OLT
     * 
     * @author Angel Reina <areina@telconet.ec>
     * @version 1.10 15-05-2019 - Se agrega validación para no reservar ips por OLT con la misma tecnología. 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.11 21-06-2019 Se agrega envío del parámetro 'filtroXEmpresa' a la función generarJsonClientes para que se obtengan
     *                           los servicios Small Business e Ip Small Business de la consulta.
     *                           Además se modifican las variables para que cumplan con el formato establecido por QA
     * 
     * @author Angel Reina <areina@telconet.ec>
     * @version 1.12 13-06-2019 - Se agrega validación $strMarcaElementoNuevo para identificar la tecnología que se utilizara 
     *                            al agregar las característica RESERVA IP MIGRACION.
     * 
     * @since 1.12
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.13 29-10-2019 Se reestructura el proceso de reserva de ips invocando el procedimiento de Base de Datos creado para consulta
     *                           los servicios ips por migración de tecnología de Tellion a Huawei/ZTE
     * 
     */
    public function reservarIpsOltAction()
    {
        ini_set('max_execution_time', 900000);
        $objRespuesta                   = new JsonResponse();
        $objRequest                     = $this->getRequest();
        $objSession                     = $objRequest->getSession();
        $strUsrCreacion                 = $objSession->get('user');
        $strIpCreacion                  = $objRequest->getClientIp();
        $serviceMigracionHuawei         = $this->get('tecnico.MigracionHuawei');
        $strDatabaseDsn                 = $this->container->getParameter('database_dsn');
        $strUserComercial               = $this->container->getParameter('user_comercial');
        $strPasswordComercial           = $this->container->getParameter('passwd_comercial');
        $intIdElemento                  = $objRequest->get('idElemento');
        $intIdElementoIp                = $objRequest->get('idElementoIp');
        $strMarcaElemento               = $objRequest->get('strMarcaElemento');
        $arrayRespuestaReservaMigracion = $serviceMigracionHuawei->reservarIpsMigracionOlt(array(
                                                                                                "strDatabaseDsn"            => $strDatabaseDsn,
                                                                                                "strUserComercial"          => $strUserComercial,
                                                                                                "strPasswordComercial"      => $strPasswordComercial,
                                                                                                "idElemento"                => $intIdElemento,
                                                                                                "idElementoIp"              => $intIdElementoIp,
                                                                                                "strMarcaElemento"          => $strMarcaElemento,
                                                                                                "usrCreacion"               => $strUsrCreacion,
                                                                                                "ipCreacion"                => $strIpCreacion));
        $objRespuesta->setData($arrayRespuestaReservaMigracion);
        return $objRespuesta;
    }
    
    /**
     * Documentación para el método 'contarIpsOltAction'.
     *
     * Metodo utilizado para contar las ips de servicios dentro del Olt
     * 
     * @return $objRespuesta
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.0 15-05-2019
     * @since 1.0
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 21-06-2019 Se agrega envío del parámetro 'filtroXEmpresa' a la función generarJsonClientes para que se obtengan 
     *                          los servicios Small Business e Ip Small Business de la consulta. 
     *                          Además se modifican las variables para que cumplan con el formato establecido por QA
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 29-10-2019 Se reestructura el proceso de conteo debido a demoras en el tiempo de respuesta de peticiones con ciertos olts.
     *                          Se realiza todo la programación en la consulta en el procedimiento de Base de Datos P_GET_SERV_IPS_MIGRACION 
     *                          del paquete TECNK_SERVICIOS
     * 
     */
    public function contarIpsOltAction()
    {
        ini_set('max_execution_time', 900000);
        $objRespuesta           = new JsonResponse();
        $objRequest             = $this->getRequest();
        $objSession             = $objRequest->getSession();
        $emComercial            = $this->getDoctrine()->getManager('telconet');
        $intIdElemento          = $objRequest->get('idElemento');
        $serviceUtil            = $this->get('schema.Util');
        $strDatabaseDsn         = $this->container->getParameter('database_dsn');
        $strUserComercial       = $this->container->getParameter('user_comercial');
        $strPasswordComercial   = $this->container->getParameter('passwd_comercial');
        
        $arrayParametrosConteo  = array("strDatabaseDsn"            => $strDatabaseDsn,
                                        "strUserComercial"          => $strUserComercial,
                                        "strPasswordComercial"      => $strPasswordComercial,
                                        "intIdElementoOlt"          => $intIdElemento,
                                        "strRetornaDataServicios"   => "NO",
                                        "strRetornaTotalServicios"  => "SI");
        try
        {
            $arrayRespuestaConteo       = $emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                      ->getRespuestaServiciosIpMigracion($arrayParametrosConteo);
            $strStatusRespuestaConteo   = $arrayRespuestaConteo["status"];
            if($strStatusRespuestaConteo === "OK")
            {
                $strRespuestaConteo     = $arrayRespuestaConteo["intTotalServiciosIps"];
            }
            else
            {
                throw new \Exception("No se ha podido realizar el conteo de ips por migración");
            }
        }
        catch (\Exception $e)
        {
            $serviceUtil->insertError(  'TELCOS+',
                                        'InfoElementoOltController.contarIpsOltAction',
                                        $e->getMessage(),
                                        $objSession->get('user'),
                                        $objRequest->getClientIp());
            $strRespuestaConteo = "PROBLEMAS CONTAR";
        }
        $objRespuesta->setContent($strRespuestaConteo);
        return $objRespuesta;
    }

    /**
     * configuraIpCnrTellionAction, metodo envia a configurar ip's al cnr y envia a actualizar o insertar la caracteristica mac o mac wifi
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 13-11-2015
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 02-05-2016 Se modifica el metodo de respuesta al ejecutar el jar md_datos ya que se lo hará con nohup, se elimino la parte de 
     * envio de correo cuando se esperaba el resulta de la configuracion de ip
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.2 02-18-2016 Se envia como parametro el elemento OLT a la linea de ejecucion del jar
     * @since 1.1
     * 
     * @return json con un código de estatus y un mensaje de acción realizada
     * 
     * @Secure(roles="ROLE_227-3197")
     */
    public function configuraIpCnrTellionAction()
    {
        ini_set('max_execution_time', 900000);
        $objRequest                 = $this->getRequest();
        $objSession                 = $objRequest->getSession();
        $objReturnResponse          = new ReturnResponse();
        $emInfraestructura          = $this->getDoctrine()->getManager('telconet_infraestructura');
        $InfoActivarPuertoService   = $this->get('tecnico.InfoActivarPuerto');
        $InfoServicioTecnicoService = $this->get('tecnico.InfoServicioTecnico');
        $intIdElemento              = $objRequest->get('intIdElemento');
        $objConfiguraIpCNRManual    = json_decode($objRequest->get('jsonConfiguraIpCNRManual'));
        $strCodEmpresa              = ($objRequest->getSession()->get('idEmpresa') ? $objRequest->getSession()->get('idEmpresa') : "");
        $objResponse                = new Response();
        $objResponse->headers->set('Content-type', 'text/json');
        $objReturnResponse->setStrStatus($objReturnResponse::NOT_RESULT);
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_NOT_RESULT);
        try
        {
            //Si no viene la empresa termina el metodo con un return.
            if(empty($strCodEmpresa))
            {
                $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
                $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . ' No se esta enviando la empresa.');
                $objResponse->setContent(json_encode((array) $objReturnResponse));
                return $objResponse;
            }
            
            //Si no viene el ID elemento termina el metodo con un return.
            if(empty($intIdElemento))
            {
                $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
                $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . ' No se esta enviando el ID de el elemento.');
                $objResponse->setContent(json_encode((array) $objReturnResponse));
                return $objResponse;
            }

            $entityInfoElemento = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($intIdElemento);
            $strNombreElemento  = $entityInfoElemento->getNombreElemento();

            //Si no encontro el elemento termina el metodo con un return.
            if(!$entityInfoElemento)
            {
                $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
                $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . ' No se encontro el elemento.');
                $objResponse->setContent(json_encode((array) $objReturnResponse));
                return $objResponse;
            }

            $strConfigurarIp             = '';
            $strIpConfigurarNotificacion = '';
            $strIpMacErrorFormato        = '';
            $intRowCounter               = 0;

            //Itera las ip's y mac's enviadas en formato json.
            foreach($objConfiguraIpCNRManual->arrayData as $objConfiguraIpCNRManual):
                $intRowCounter = $intRowCounter + 1;

                //Valida el formato de la IP.
                $strValidIp  = filter_var($objConfiguraIpCNRManual->strIpConfigurar, FILTER_VALIDATE_IP);

                //Valida que la ip y la mac tengan el formato correcto.
                if(!empty($strValidIp) && true === $InfoServicioTecnicoService->isValidMac($objConfiguraIpCNRManual->strMacConfigurar))
                {
                    //Concatena la IP, MAC, IP para enviar a ejecutar en CNR.
                    $strConfigurarIp             .= $strConfigurarIp . $objConfiguraIpCNRManual->strIpConfigurar . ',' . 
                                                    $objConfiguraIpCNRManual->strMacConfigurar . ',' . 
                                                    $objConfiguraIpCNRManual->strIpConfigurar . ';';

                    //Crea fila para el envio de correo de las ip's que se enviaron a configurar.
                    $strIpConfigurarNotificacion .= "<tr><td> " . $intRowCounter . " </td> " . 
                                                    "<td>" . $objConfiguraIpCNRManual->strIpConfigurar . "</td> " . 
                                                    "<td>" . $objConfiguraIpCNRManual->strMacConfigurar . "</td> " .
                                                    "<td> Se envio a configurar </td> </tr>";
                }
                else
                {
                    //Cuando no cumple el formato no se envia al CNR
                    $strIpMacErrorFormato        .= $strIpMacErrorFormato . $objConfiguraIpCNRManual->strIpConfigurar . ',' . 
                                                    $objConfiguraIpCNRManual->strMacConfigurar;

                    //Crea fila par el envio de correo cuando no cumple con el formato de ip o mac, no se envia al CNR.
                    $strIpConfigurarNotificacion .= "<tr> <td> " . $intRowCounter . " </td> " . 
                                                    " <td>" . $objConfiguraIpCNRManual->strIpConfigurar . "</td> " . 
                                                    "<td>" . $objConfiguraIpCNRManual->strMacConfigurar . "</td> " .
                                                    "<td> La Ip o Mac no cumple con el formato </td> </tr>";
                }
            endforeach;

            //Si no hay ip's y mensaje de error termina el metodo
            if(empty($strConfigurarIp) && empty($strIpMacErrorFormato))
            {
                $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
                $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . " No se estan enviando Ip's a configurar.");
                $objResponse->setContent(json_encode((array) $objReturnResponse));
                return $objResponse;
            }

            //Concatena un mensaje cuando la ip o mac no cumplio con el formato.
            if(!empty($strIpMacErrorFormato))
            {
                $strIpMacErrorFormato = " Las Ip's o Mac's no tienen el formato correcto " . $strIpMacErrorFormato . " por lo cual no se enviaron a" .
                                        " configurar en el CNR.";
            }

            $dateFechaActual                               = new \DateTime();
            $arrayConfigurarIp                             = array();
            $arrayConfigurarIp['strConfigurarIp']          = $strConfigurarIp;
            $arrayConfigurarIp['strAccion']                = 'configurarIpMasivaTellion';
            $arrayConfigurarIp['strNombreElemento']        = $strNombreElemento;
            $arrayConfigurarIp['strNombreElementoOutput']  = $strNombreElemento . '-' . $dateFechaActual->format('d-m-Y_H:i:s');
            $arrayConfigurarIp['entityAdmiModeloElemento'] = $entityInfoElemento->getModeloElementoId();

            //Se envia a configurar las Ip's en el CNR.
            $InfoActivarPuertoService->configurarIpFijaTellion($arrayConfigurarIp);
            $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS);
            
        }
        catch(\Exception $ex)
        {
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . ' ' . $ex->getMessage());
        }
        $objResponse->setContent(json_encode((array) $objReturnResponse));
        return $objResponse;
    } //configuraIpCnrTellionAction

    /**
     * @Secure(roles="ROLE_227-2777")
     * 
     * Documentación para el método 'quitarOperatividad'.
     *
     * Metodo utilizado para ingresar caracteristica de operatividad al elemento
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 06-08-2015
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 21-12-2015 Se agrego historial del elemento
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
            $objDetalleElemento->setDetalleNombre("OLT OPERATIVO");
            $objDetalleElemento->setDetalleValor("NO");
            $objDetalleElemento->setDetalleDescripcion("OLT OPERATIVO");
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
     * @Secure(roles="ROLE_227-6")
     * Documentación para el método 'getInterfacesOltAction'.
     *
     * Metodo utilizado para obtener tarjetas OLT Huawei
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 06-08-2015
     */
    public function getInterfacesOltAction()
    {
        $respuesta      = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $peticion       = $this->get('request');
        $idElemento     = $peticion->query->get('idElemento');
        
        $objJson = $this->getDoctrine()
                        ->getManager("telconet_infraestructura")
                        ->getRepository('schemaBundle:InfoInterfaceElemento')
                        ->generarJsonInterfacesOtl($idElemento);
        $respuesta->setContent($objJson);

        return $respuesta;
    }
    
    /**
     * @Secure(roles="ROLE_227-6")
     * Documentación para el método 'ajaxGetJsonInterfacesByTarjetaAction'.
     *
     * Metodo utilizado para obtener las interfaces de una tarjetas OLT Huawei
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 01-09-2015
     */
    public function ajaxGetJsonInterfacesByTarjetaAction()
    {
        $respuesta        = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $peticion         = $this->get('request');
        $intIdElemento    = $peticion->get('idElemento');
        $strNombreTarjeta = $peticion->get('nombreTarjeta');
        
        if($intIdElemento)
        {
            
                $objJson = $this->getDoctrine()
                                ->getManager("telconet_infraestructura")
                                ->getRepository('schemaBundle:InfoInterfaceElemento')
                                ->generarJsonInterfacesTarjeta($intIdElemento, $strNombreTarjeta);

                $respuesta->setContent($objJson);
            
        }
        else
        {
	  $respuesta->setContent("No hay idElemento");
        }
        return $respuesta;
    }
    
    /**
     * @Secure(roles="ROLE_227-2877")
     * Documentación para el método 'aumentarCapacidadTarjetaAction'.
     *
     * Metodo utilizado para aumentar 8 puertos adicionales a la tarjeta del OLT Huawei seleccionada
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 02-09-2015
     */
    public function aumentarCapacidadTarjetaAction()
    {
        $respuesta          = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $peticion           = $this->get('request');
        $session            = $peticion->getSession();
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');
        $intIdElemento      = $peticion->get('idElemento');
        $strNombreTarjeta   = $peticion->get('nombreTarjeta');

        $emInfraestructura->getConnection()->beginTransaction();

        try
        {
            $entityElemento = $emInfraestructura->find('schemaBundle:InfoElemento', $intIdElemento);
            //Se ingresan los nuevos puertos de la tarjeta del ?/8 - ?/15        
            for($i = 8; $i <= 15; $i++)
            {
                $interfaceElemento          = new InfoInterfaceElemento();
                $nombreInterfaceElemento    = $strNombreTarjeta . $i;
                $interfaceElemento->setNombreInterfaceElemento($nombreInterfaceElemento);
                $interfaceElemento->setElementoId($entityElemento);
                $interfaceElemento->setEstado("not connect");
                $interfaceElemento->setUsrCreacion($session->get('user'));
                $interfaceElemento->setFeCreacion(new \DateTime('now'));
                $interfaceElemento->setIpCreacion($peticion->getClientIp());

                $emInfraestructura->persist($interfaceElemento);
            }

            $emInfraestructura->flush();
            $emInfraestructura->getConnection()->commit();
            
            return $respuesta->setContent("Puertos agregados exitosamente!");
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
     * @Secure(roles="ROLE_227-2877")
     * 
     * Documentación para el método 'reducirCapacidadTarjetaAction'.
     *
     * Metodo utilizado para reducir 8 puertos adicionales a la tarjeta del OLT Huawei seleccionada
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 02-09-2015
     */
    public function reducirCapacidadTarjetaAction()
    {
        $respuesta              = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $peticion               = $this->get('request');
        $emInfraestructura      = $this->getDoctrine()->getManager('telconet_infraestructura');
        $intIdElemento          = $peticion->get('idElemento');
        $strNombreTarjeta       = $peticion->get('nombreTarjeta');
        $strTipoOperacion       = $peticion->get('tipoOperacion');
        $strInterfacesConnected = "NO";
        $strMensajeRespuesta    = "";
        $emInfraestructura->getConnection()->beginTransaction();

        try
        {
            $objJson = $this->getDoctrine()
                            ->getManager("telconet_infraestructura")
                            ->getRepository('schemaBundle:InfoInterfaceElemento')
                            ->generarJsonInterfacesTarjeta($intIdElemento, $strNombreTarjeta);

            $respuesta->setContent($objJson);
            $registros = json_decode($objJson);
            //se recorren puertos de la tarjeta
            foreach ($registros->encontrados as $registro ) 
            {
                //si el tipo de operacion es reducir solo se validan los puertos superiores al ?/7
                if ($strTipoOperacion == "reducir")
                {
                    if (substr($registro->nombreInterfaceElemento,2) > 7 )
                    {
                        //se valida que no existan puertos con estado connected
                        if ($registro->estado == "connected")
                        {
                            $strInterfacesConnected = "SI";
                        }
                    }
                }
                //si el tipo de orden es eliminar se validan todos los puertos de la tarjeta
                else
                {
                    //se valida que no existan puertos con estado connected
                    if ($registro->estado == "connected")
                    {
                        $strInterfacesConnected = "SI";
                    }                    
                }
            }
            //si la bandera $strInterfacesConnected es igual a NO se reducen o eliminan los puertos de acuerdo al tipo de operacion
            if ($strInterfacesConnected == "NO")
            {
                foreach ($registros->encontrados as $registro ) 
                {
                    if ($strTipoOperacion == "reducir")
                    {
                        if (substr($registro->nombreInterfaceElemento,2) > 7 )
                        {
                            $entityInterfaceElemento = $emInfraestructura->find('schemaBundle:InfoInterfaceElemento', $registro->idInterfaceElemento);
                            $entityInterfaceElemento->setEstado("Eliminado");
                            $emInfraestructura->persist($entityInterfaceElemento);
                            $emInfraestructura->flush();
                        }
                    }
                    else
                    {
                        $entityInterfaceElemento = $emInfraestructura->find('schemaBundle:InfoInterfaceElemento', $registro->idInterfaceElemento);
                        $entityInterfaceElemento->setEstado("Eliminado");
                        $emInfraestructura->persist($entityInterfaceElemento);
                        $emInfraestructura->flush();
                        
                    }
                    
                }                
                $emInfraestructura->getConnection()->commit();
            
                $strMensajeRespuesta = "Puertos eliminados exitosamente!";
            }
            else
            {
                $strMensajeRespuesta = "Existen Puertos conectados, no se pudieron eliminar puertos!";
            }
            return $respuesta->setContent($strMensajeRespuesta);
            
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
     * @Secure(roles="ROLE_227-2877")
     * 
     * Documentación para el método 'agregarTarjetaAction'.
     *
     * Metodo utilizado para agregar tarjeta del OLT Huawei 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 04-09-2015
     */
    public function agregarTarjetaAction()
    {
        $respuesta          = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $peticion           = $this->get('request');
        $session            = $peticion->getSession();
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');
        $intIdElemento      = $peticion->get('idElemento');
        $strNombreTarjeta   = $peticion->get('nombreTarjeta');
        $intCandidadPuertos = $peticion->get('cantidadPuertos');

        $emInfraestructura->getConnection()->beginTransaction();

        try
        {
            $entityElemento = $emInfraestructura->find('schemaBundle:InfoElemento', $intIdElemento);
            $objJson = $this->getDoctrine()
                            ->getManager("telconet_infraestructura")
                            ->getRepository('schemaBundle:InfoInterfaceElemento')
                            ->generarJsonInterfacesTarjeta($intIdElemento, $strNombreTarjeta);
            $objJson = json_decode($objJson);

            if ($objJson->total == '0')
            {
                for($i = 0; $i <= ($intCandidadPuertos-1); $i++)
                {
                    $interfaceElemento          = new InfoInterfaceElemento();
                    $nombreInterfaceElemento    = $strNombreTarjeta . $i;
                    $interfaceElemento->setNombreInterfaceElemento($nombreInterfaceElemento);
                    $interfaceElemento->setElementoId($entityElemento);
                    $interfaceElemento->setEstado("not connect");
                    $interfaceElemento->setUsrCreacion($session->get('user'));
                    $interfaceElemento->setFeCreacion(new \DateTime('now'));
                    $interfaceElemento->setIpCreacion($peticion->getClientIp());

                    $emInfraestructura->persist($interfaceElemento);
                }
                $emInfraestructura->flush();
                $emInfraestructura->getConnection()->commit();
                return $respuesta->setContent("Tarjeta agregada exitosamente!");
            }
            else
            {
                return $respuesta->setContent("Tarjeta ya existe!");
            }

            
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
     * @Secure(roles="ROLE_227-3177")
     * 
     * Documentación para el método 'actualizaCaracteristicaOLTAction'.
     *
     * Metodo utilizado para actualizar una caracteristica al OLT
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 30-10-2015
     * 
     * @return json con un código de estatus y un mensaje de acción realizada
     */
    public function actualizaCaracteristicaOLTAction()
    {
        $objRequest                     = $this->getRequest();
        $objReturnResponse              = new ReturnResponse();
        $emInfraestructura              = $this->getDoctrine()->getManager('telconet_infraestructura');
        $intIdElemento                  = $objRequest->get('intIdElemento');
        $strDetalleValorCaracteristica  = $objRequest->get('strDetalleValorCaracteristica');
        $strDetalleNombreBusqueda       = $objRequest->get('strDetalleNombreBusqueda');
        $objResponse                    = new Response();
        $objResponse->headers->set('Content-type', 'text/json');
        $objReturnResponse->setStrStatus($objReturnResponse::NOT_RESULT);
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_NOT_RESULT);

        $emInfraestructura->getConnection()->beginTransaction();

        try
        {
            //Si no se envia el Id del elemento termina el metodo con un return
            if(empty($intIdElemento))
            {
                $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
                $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . ' No se esta enviando el Id del elemento.');
                $objResponse->setContent(json_encode((array) $objReturnResponse));
                $emInfraestructura->getConnection()->close();
                return $objResponse;
            }

            //Si no se envia la caracteristica a buscar termina el metodo con un return
            if(empty($strDetalleNombreBusqueda))
            {
                $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
                $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . ' No se esta enviando el detalle para buscar' . 
                                                        ' la caracteristica.');
                $objResponse->setContent(json_encode((array) $objReturnResponse));
                $emInfraestructura->getConnection()->close();
                return $objResponse;
            }

            //Busca la caracteristica
            $entityInfoDetalleElemento = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                           ->findOneBy(array("elementoId"    => $intIdElemento, 
                                                                             "detalleNombre" => $strDetalleNombreBusqueda));

            //Valida que el elemento tenga una caracteristica ingresada, para poderla actualizar
            if(!$entityInfoDetalleElemento)
            {
                $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
                $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . ' Este elemento no tiene' . 
                                                        ' una caracteristica ingresada.');
                $objResponse->setContent(json_encode((array) $objReturnResponse));
                $emInfraestructura->getConnection()->close();
                return $objResponse;
            }
            
            //Actualiza la caracteristica del elemento
            $entityInfoDetalleElemento->setDetalleValor($strDetalleValorCaracteristica);
            $emInfraestructura->persist($entityInfoDetalleElemento);

            $emInfraestructura->flush();
            $emInfraestructura->getConnection()->commit();

            $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS . ' Se actualizó la carecteristica.');

        }
        catch(\Exception $ex)
        {
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . ' ' . $ex->getMessage());
            $emInfraestructura->getConnection()->rollback();
        }

        $emInfraestructura->getConnection()->close();
        $objResponse->setContent(json_encode((array) $objReturnResponse));
        return $objResponse;
    } //actualizaCaracteristicaOLTAction
    

    /**
     * verificaIpReservada, verifica que la ip este en estado reservada y pertenezca al elemento donde fue reservada
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 18-11-2015
     * 
     * @return json $objResponse retorna un mensaje y condigo en formato json
     */
    public function verificaIpReservadaAction()
    {
        $objRequest              = $this->getRequest();
        $strIp                   = $objRequest->get('strIp');
        $strEstadoIp             = $objRequest->get('strEstadoIp');
        $strNombreElemento       = $objRequest->get('strNombreElemento');
        $strValidarMismoElemento = $objRequest->get('strValidarMismoElemento');
        $objReturnResponse       = new ReturnResponse();
        $objResponse             = new Response();
        $emInfraestructura       = $this->getDoctrine()->getManager('telconet_infraestructura');
        $objResponse->headers->set('Content-type', 'text/json');
        $objReturnResponse->setStrStatus($objReturnResponse::NOT_RESULT);
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_NOT_RESULT);
        try
        {
            //Busca la ip
            $entityInfoIp = $emInfraestructura->getRepository('schemaBundle:InfoIp')
                                              ->findOneBy(array('ip'     => $strIp,
                                                                'estado' => $strEstadoIp));

            //Si no encontro el objeto termina el metodo
            if(!$entityInfoIp)
            {
                $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
                $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . ' La ip no existe o no está reservada.');
                $objResponse->setContent(json_encode((array) $objReturnResponse));
                $emInfraestructura->getConnection()->close();
                return $objResponse;
            }

            //Busca la relacion del servicio y elemento en la entidad InfoServicioTecnico
            $entityInfoServicioTecnico = $emInfraestructura->getRepository('schemaBundle:InfoServicioTecnico')
                                                           ->findOneBy(array('servicioId' => $entityInfoIp->getServicioId()));

            //Si no encontro el objeto termina el metodo
            if(!$entityInfoServicioTecnico)
            {
                $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
                $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . ' No tiene servicio tecnico.');
                $objResponse->setContent(json_encode((array) $objReturnResponse));
                $emInfraestructura->getConnection()->close();
                return $objResponse;
            }

            //Busca el elemento
            $entityInfoElemento = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($entityInfoServicioTecnico->getElementoId());

            //Si no encontro el objeto termina el metodo
            if(!$entityInfoElemento)
            {
                $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
                $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . ' La ip no esta relacionada a un OLT.');
                $objResponse->setContent(json_encode((array) $objReturnResponse));
                $emInfraestructura->getConnection()->close();
                return $objResponse;
            }

            //Entra cuando $strValidarMismoElemento es = a SI
            if("SI" === $strValidarMismoElemento)
            {
                //Valida que la ip encontrada pertenezca al mismo elemento donde se reservo
                if($strNombreElemento !== $entityInfoElemento->getNombreElemento())
                {
                    $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
                    $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . ' La ip no esta reservada en este elemento.');
                    $objResponse->setContent(json_encode((array) $objReturnResponse));
                    $emInfraestructura->getConnection()->close();
                    return $objResponse;
                }
            }

            $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS);
        } 
        catch (Exception $ex) 
        {
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . ' ' . $ex->getMessage());
        }
        $objResponse->setContent(json_encode((array) $objReturnResponse));
        return $objResponse;
    } //verificaIpReservadaAction


    /**
     * Documentación para el método 'getOpcionesAprovisionamientoIpAction'.
     *
     * Retorna un string que contiene las opciones del aprovisionamiento dependiendo de la marca del modelo del elemento
     *
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 11-11-2015
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 05-12-2016      Se agrega filtrado en recuperación de parametros para solo recuperar aprovisionamiento CNR
     * @since 1.0
     */
    public function getOpcionesAprovisionamientoIpAction()
    {
        $response              = new Response();
        $emGeneral             = $this->getDoctrine()->getManager('telconet_general');
        $emInfraestructura     = $this->getDoctrine()->getManager('telconet_infraestructura');
        $arrayResultados       = array();
        $objRequest            = $this->get('request');
        $intIdModeloElementoId = $objRequest->request->get('modeloElementoId') ? $objRequest->request->get('modeloElementoId') : 0;
        $strMarcaElemento      = '';
        $strRepuesta           = '<select id ="aprovisionamiento" name="aprovisionamiento" >';
        
        $objModeloElementoId = $emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')->findOneById($intIdModeloElementoId);
        
        if( $objModeloElementoId )
        {
            $strMarcaElemento = $objModeloElementoId->getMarcaElementoId() 
                                ? $objModeloElementoId->getMarcaElementoId()->getNombreMarcaElemento() : '';
        }
        
        if( $strMarcaElemento )
        {
            $arrayResultados = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                         ->get( self::DETALLE_ELEMENTO_APROVISIONAMIENTO, '', '', '', $strMarcaElemento, 'CNR', '', '');
        }
            

        if($arrayResultados)
        {
            foreach($arrayResultados as $arrayAprovisionamientoIp)
            {
                $strRepuesta .= '<option value="'.$arrayAprovisionamientoIp['valor2'].'">'.$arrayAprovisionamientoIp['descripcion'].'</option>';
            }//foreach($arrayResultados as $arrayTipoMedioTransporte)
        }//($arrayResultados)
        
        $strRepuesta .= '</select>';
        
        $response->setContent( $strRepuesta );
        
        return $response;
    }
    
    
    /**
     * Documentación para el método 'iniciarMigracionAction'.
     *
     * Método que guarda un detalle al elemento indicado que la Migración del OLT ha iniciado
     *
     * @return Response 
     *      
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 26-01-2016 - Se valida que si el olt ya fue migrado y tiene la caracteristica esta se actualice para iniciar
     *                           una nueva migracion
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 19-01-2016
     * 
     * @author Lizbeth Cruz <mlc@telconet.ec>
     * @version 1.0 21-11-2018 Se modifica el detalle nombre usado para el cambio de plan masivo de MIGRACION a CAMBIO_PLAN_MASIVO_MD
     * 
     * @author Lizbeth Cruz <mlc@telconet.ec>
     * @version 1.2 04-12-2019 Se agrega validación para actualizar el valor del detalle a INICIO sólo si el olt tiene solicitudes 
     *                          en estado Pendiente
     */
    public function iniciarMigracionAction()
    {
        $objJsonResponse        = new JsonResponse();
        $emInfraestructura      = $this->getDoctrine()->getManager('telconet_infraestructura');
        $objRequest             = $this->get('request');
        $objSession             = $objRequest->getSession();
        $strUserSession         = $objSession->get('user');
        $strIpUserSession       = $objRequest->getClientIp();
        $intIdElemento          = $objRequest->request->get('intIdElemento') ? $objRequest->request->get('intIdElemento') : 0;
        $strDatabaseDsn         = $this->container->getParameter('database_dsn');
        $strUserComercial       = $this->container->getParameter('user_comercial');
        $strPasswordComercial   = $this->container->getParameter('passwd_comercial');
        $strMuestraMsjAlUsuario = "NO";
        $strStatus              = str_repeat(' ', 5);
        $strMensaje             = str_repeat(' ', 2000);
        $intIdEmpresa           = $objSession->get('idEmpresa');
        $strPrefijoEmpresa      = $objSession->get('prefijoEmpresa');
        try
        {
            if(isset($intIdElemento) && !empty($intIdElemento) && intval($intIdElemento) > 0)
            {
                $objInfoElemento    = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                        ->findOneBy( array( 'estado' => 'Activo',
                                                                            'id'     => $intIdElemento ) );
                if(!is_object($objInfoElemento))
                {
                    $strMuestraMsjAlUsuario = "SI";
                    throw new \Exception("No se ha podido obtener la información del olt");
                }

                $arrayParamsVerifSolsCPM    = array("strDatabaseDsn"            => $strDatabaseDsn,
                                                    "strUserComercial"          => $strUserComercial,
                                                    "strPasswordComercial"      => $strPasswordComercial,
                                                    "strRetornaTotalOltsCpm"    => "SI",
                                                    "intIdOlt"                  => $intIdElemento,
                                                    "idEmpresa"                 => $intIdEmpresa,
                                                    "strPrefijoEmpresa"         => $strPrefijoEmpresa);
                $arrayRespuestaVerifSolsCPM = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                ->getRespuestaOltsInicioCpm($arrayParamsVerifSolsCPM);
                $strStatusVerifSolsCPM      = $arrayRespuestaVerifSolsCPM["status"];
                $intTotalOlts               = $arrayRespuestaVerifSolsCPM["intTotalOltsCpm"];
                if($strStatusVerifSolsCPM === "ERROR")
                {
                    $strMuestraMsjAlUsuario = "SI";
                    throw new \Exception("No se ha podido consultar el número de solicitudes de cambio de plan masivo asociadas al olt");
                }
                if(intval($intTotalOlts) === 0)
                {
                    $strMuestraMsjAlUsuario = "SI";
                    throw new \Exception("El olt no tiene solicitudes de cambio de plan masivo en estado Pendiente");
                }

                $strSql                 = "BEGIN INFRK_TRANSACCIONES.P_EJECUTA_CPM_OLTS(:intIdOlt, :strUsrCreacion, :strIpCreacion, "
                                                                                        .":strStatus, :strMensaje, :intIdEmpresa, "
                                                                                        .":strPrefijoEmpresa); END;";
                $objStmt                = $emInfraestructura->getConnection()->prepare($strSql);
                $objStmt->bindParam('intIdOlt', $intIdElemento);
                $objStmt->bindParam('strUsrCreacion', $strUserSession);
                $objStmt->bindParam('strIpCreacion', $strIpUserSession);
                $objStmt->bindParam('strStatus', $strStatus);
                $objStmt->bindParam('strMensaje', $strMensaje);
                $objStmt->bindParam('intIdEmpresa', $intIdEmpresa);
                $objStmt->bindParam('strPrefijoEmpresa', $strPrefijoEmpresa);
                $objStmt->execute();
                if(strlen(trim($strStatus)) > 0)
                {
                    $strStatusRespuesta     = $strStatus;
                    $strMensajeRespuesta    = $strMensaje;
                }
                else
                {
                    $strStatusRespuesta     = "ERROR";
                    $strMensajeRespuesta    = 'Ha ocurrido un error inesperado. Por favor comuníquese con Sistemas!';
                }
            }
            else
            {
                $strMuestraMsjAlUsuario = "SI";
                throw new \Exception("No se ha enviado correctamente el parámetro con el id del olt");
            }
        }
        catch (\Exception $e)
        {
            error_log("Error al intentar iniciar el cambio de plan por olt ".$e->getMessage());
            $strStatusRespuesta = "ERROR";
            if($strMuestraMsjAlUsuario === "SI")
            {
                $strMensajeRespuesta = $e->getMessage();
            }
            else
            {
                $strMensajeRespuesta = "Ha ocurrido un problema al iniciar el cambio de plan. Por favor comuníquese con Sistemas!";
            }
        }
        
        $arrayRespuesta = array("status"    => $strStatusRespuesta, 
                                "mensaje"   => $strMensajeRespuesta);
        $objJsonResponse->setData($arrayRespuesta);
        return $objJsonResponse;
    }
    
    
    /**
     * Documentación para el método 'eliminarDetallesPerfilesAction'.
     *
     * Método que elimina los detalles de perfiles de los elementos OLT
     *
     * @return Response 
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 04-02-2016
     */
    public function eliminarDetallesPerfilesAction()
    {
        $response            = new Response();
        $emInfraestructura   = $this->getDoctrine()->getManager('telconet_infraestructura');
        $objRequest          = $this->get('request');
        $objSession          = $objRequest->getSession();
        $strUserSession      = $objSession->get('user');
        $strIpUserSession    = $objRequest->getClientIp();
        $datetimeActual      = new \DateTime('now');
        $intIdElemento       = $objRequest->request->get('idElemento') ? $objRequest->request->get('idElemento') : 0;
        $strRepuesta         = 'Elemento no se ha encontrado en estado Activo';
        
        $objInfoElemento = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                             ->findOneBy( array( 'estado' => 'Activo',
                                                                 'id'     => $intIdElemento ) );

        if( $objInfoElemento )
        {
            $emInfraestructura->getConnection()->beginTransaction();	
        
            try
            {
                $arrayDetalleNombres = array('TRAFFIC-TABLE', 'GEM-PORT', 'LINE-PROFILE-NAME', 'LINE-PROFILE-ID');
                
                foreach( $arrayDetalleNombres as $strDetalle )
                {
                    $objInfoDetallesElemento = $emInfraestructura->getRepository("schemaBundle:InfoDetalleElemento")
                                                                 ->findBy( array( "elementoId" => $intIdElemento, "detalleNombre" => $strDetalle ) );
                    
                    if( $objInfoDetallesElemento)
                    {
                        foreach( $objInfoDetallesElemento as $objInfoDetalleElemento )
                        {
                            $emInfraestructura->remove($objInfoDetalleElemento);
                            $emInfraestructura->flush();
                        }//foreach( $objInfoDetallesElemento as $objInfoDetalleElemento )
                    }//( $objInfoDetallesElemento)
                }//foreach( $arrayDetalleNombres as $strDetalle )
                
                                
                /*
                 * Bloque que guarda el historial del InfoElemento
                 */
                $objInfoHistorialElemento = new InfoHistorialElemento();
                $objInfoHistorialElemento->setElementoId($objInfoElemento);
                $objInfoHistorialElemento->setObservacion('Se elimina perfiles por inicio de migración OLT');
                $objInfoHistorialElemento->setFeCreacion($datetimeActual);
                $objInfoHistorialElemento->setUsrCreacion($strUserSession);
                $objInfoHistorialElemento->setIpCreacion($strIpUserSession);
                $objInfoHistorialElemento->setEstadoElemento('Activo');
                
                $emInfraestructura->persist($objInfoHistorialElemento);
                $emInfraestructura->flush();
                /*
                 * Fin del Bloque que guarda el historial del InfoElemento
                 */
                
                $emInfraestructura->getConnection()->commit();
                $emInfraestructura->getConnection()->close();
                
                $strRepuesta = 'OK';
            }
            catch (\Exception $e)
            {
                error_log($e->getMessage());

                $emInfraestructura->getConnection()->rollback();
                $emInfraestructura->getConnection()->close();
                
                $strRepuesta = 'Hubo un problema al guardar la migración en la base de datos, por favor volver a intentar';
            }//try
        }//( $objInfoElemento )
        
        $response->setContent( $strRepuesta );
        
        return $response;
    }
    
    /**
     * 
     * Documentación para el método 'monitoreoCambioPlanAction'.
     * 
     * Metodo que redirecciona a pantalla de monitoreo de proceso de cambio de plan masivo de olts
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0.0 
     * @since 10-02-2016
     * 
     * @return type
     */
    public function monitoreoCambioPlanAction()
    {
        return $this->render('tecnicoBundle:InfoElementoOlt:monitoreoCambioPlan.html.twig');
    }
    
    /**
     * 
     * Documentación para el método 'ajaxMonitoreoGridAction'.
     * 
     * Metodo que realiza la consulta de como el proceso masivo de cambio de plan va avanzando con sus detalles
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0.0 
     * @since 10-02-2016
     * 
     * @author Aberto Arias <farias@telconet.ec>
     * @version 1.1
     * @since 12-04-2023 Se agrega filtro por empresa en sesión
     * 
     * @return json
     * 
     * @Secure(roles="ROLE_227-3497")
     */
    public function ajaxMonitoreoGridAction()
    {
        $response            = new Response();
        $emInfraestructura   = $this->getDoctrine()->getManager('telconet_infraestructura');
        $objRequest          = $this->get('request');
        $strNombreElemento   = $objRequest->get('nombreElemento');
        $objSession          = $this->get('session');
        $strIdEmpresa        = $objSession->get('idEmpresa');
        $strPrefijoEmpresa   = $objSession->get('prefijoEmpresa');
        
        $objRespuesta = $emInfraestructura->getRepository("schemaBundle:InfoElemento")->getJsonOltsMigrando($strNombreElemento, $strPrefijoEmpresa);
        
        $response->setContent( $objRespuesta );
        
        return $response;
    }
    
    /**
     * 
     * Documentación para el método 'ajaxAccionesMonitoreoOltAction'.
     * 
     * Metodo que realiza la consulta dado una accion, los clientes no configurados o el historial de procesamientos previos de un olt
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0.0 
     * @since 11-02-2016
     * 
     *  @author Aberto Arias <farias@telconet.ec>
     * @version 1.1
     * @since 12-04-2023 Se agrega filtro por empresa en sesión
     * 
     * @return json
     */
    public function ajaxAccionesMonitoreoOltAction()
    {
        $response            = new Response();
        $emInfraestructura   = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emComercial         = $this->getDoctrine()->getManager('telconet');
        $objRequest          = $this->get('request');
        $intIdElemento       = $objRequest->get('idElemento');
        $strAccion           = $objRequest->get('accion');
        $strLogin            = $objRequest->get('login');
        $intStart            = $objRequest->get('start');
        $intLimit            = $objRequest->get('limit');
        $objSession          = $objRequest->getSession();
        $strCodEmpresa       = $objSession->get('idEmpresa');
        $strPrefijoEmpresa   = $objSession->get('prefijoEmpresa');
        switch($strAccion)
        {
            case self::ACCION_MONITOREO_CLIENTES:
                
                $intIdLogin = '';
                if($strLogin!='')
                {
                    $objPunto = $emComercial->getRepository("schemaBundle:InfoPunto")->findOneByLogin($strLogin);
                    if($objPunto)
                    {
                        $intIdLogin = $objPunto->getId();
                    }
                }
                $jsonRespuesta = $emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                           ->getJsonClientesNoConfiguradosPorOlt($intIdElemento,$intIdLogin,$intStart,$intLimit,$strCodEmpresa);
                break;
            case self::ACCION_MONITOREO_HISTORIAL:
                $jsonRespuesta = $emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                           ->getJsonHistorialCambioPlan($intIdElemento,$intStart,$intLimit, $strPrefijoEmpresa);
                break;
        }
              
        $response->setContent( $jsonRespuesta );
        
        return $response;
    }        

    /** @Secure(roles="ROLE_227-3498")
     * 
     * Documentación para el método 'reversarOltMigrado'.
     *
     * Metodo utilizado para reversar la información de clientes procesados en migracion de planes ultra velocidad
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 13-02-2016
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 17-02-2016   Se agrega envio de notificación de clientes que fueron reversados por cambio de plan masivo
     * 
     */
    public function reversarOltMigradoAction()
    {
        $respuesta              = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $objPeticion            = $this->get('request');
        $emInfraestructura      = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emComercial            = $this->getDoctrine()->getManager('telconet');
        $intIdElemento          = $objPeticion->get('idElemento');
        $strMarcaOlt            = $objPeticion->get('marcaOlt');
        $strMensajeResponse     = "";
        $arrayParametros        = array();
        $objSession             = $objPeticion->getSession();
        $datetimeActual         = new \DateTime('now');
        $strUserSession         = $objSession->get('user');
        $strIpUserSession       = $objPeticion->getClientIp();
        $strServiciosPlanesHtml = "";
        $contLoginReversado     = 0;
        $emInfraestructura->getConnection()->beginTransaction();

        try
        {
            $objInfoElemento = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($intIdElemento);
            /*
             * Bloque que verifica si el OLT ha sido migrado o no
             */
            $objDetallesElemento = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                     ->findOneBy(array("elementoId"    => $intIdElemento, 
                                                                       "detalleNombre" => "OLT MIGRADO CNR"));
            if ($objDetallesElemento)
            {           
                $arrayParametros['intIdOlt']    =  $intIdElemento;
                $arrayParametros['strMarcaOlt'] =  $strMarcaOlt;
                $strMensajeResponse = $this->getDoctrine()
                                           ->getManager("telconet_infraestructura")
                                           ->getRepository('schemaBundle:InfoElemento')
                                           ->reversarOltMigradoNuevosPlanes($arrayParametros);
            }  
            else
            {
                $strMensajeResponse = 'NO MIGRADO';
            }
            
            if ($strMensajeResponse == 'OK')
            {
                /*
                 * Bloque que guarda el historial del InfoElemento
                 */
                $objInfoHistorialElemento = new InfoHistorialElemento();
                $objInfoHistorialElemento->setElementoId($objInfoElemento);
                $objInfoHistorialElemento->setObservacion('Se reversa migración OLT nuevos planes ultravelocidad');
                $objInfoHistorialElemento->setFeCreacion($datetimeActual);
                $objInfoHistorialElemento->setUsrCreacion($strUserSession);
                $objInfoHistorialElemento->setIpCreacion($strIpUserSession);
                $objInfoHistorialElemento->setEstadoElemento('Activo');
                
                $emInfraestructura->persist($objInfoHistorialElemento);
                $emInfraestructura->flush();
                
                $arrayRespuesta = $emComercial
                                           ->getRepository('schemaBundle:InfoElemento')
                                           ->getResultadoServiciosReversadoPorOlt($intIdElemento);
                
                foreach($arrayRespuesta as $servicioReversado)
                {
                    $contLoginReversado++;
                    $strServiciosPlanesHtml = $strServiciosPlanesHtml
                                              . '<tr>'
                                              . '<td>'. $contLoginReversado  . '</td>'   
                                              . '<td>'. $servicioReversado['login']  . '</td>'    
                                              . '<td>'. $servicioReversado['planAnterior']  . '</td>'
                                              . '<td>'. $servicioReversado['precioAnterior']  . '</td>'
                                              . '<td>'. $servicioReversado['planActual']  . '</td>'
                                              . '<td>'. $servicioReversado['precioActual']  . '</td>'
                                              . '</tr>';
                }
                
                $asunto     = "Notificación de clientes que fueron reversados por problemas en cambio de plan masivo";
                $parametros = array("registrosReversados"      => $strServiciosPlanesHtml,
                                    "nombreOlt"                => $objInfoElemento->getNombreElemento());

                /*Si se crea la entidad hacemos el envio de la notificacion*/
                $envioPlantilla = $this->get('soporte.EnvioPlantilla'); 
                $envioPlantilla->generarEnvioPlantilla($asunto, '', 'CNROLT', $parametros, '', '', '');   
                
                $emInfraestructura->getConnection()->commit();
                $emInfraestructura->getConnection()->close();
            }
            
            

            return $respuesta->setContent($strMensajeResponse);
        }
        catch (\Exception $e)
        {
            $emInfraestructura->getConnection()->rollback();
            $emInfraestructura->getConnection()->close();
            $mensajeError = "Error: " . $e->getMessage();
            error_log($mensajeError);
            return $respuesta->setContent("PROBLEMAS TRANSACCION");
        }
    }
    
    /**
     * 
     * Documentación para el método 'migracionAction'.
     * 
     * Metodo que redirecciona a pantalla migracion olts Huawei - Zte
     * 
     * @author Angel Reina <areina@telconet.ec>
     * @version 1.0
     * @since 07-03-2019
     * 
     * @return type
     */
    public function migracionAction()
    {
        return $this->render('tecnicoBundle:InfoElementoOlt:migracion.html.twig');
    }

    /**
     * 
     * Documentación para el método 'migracionOltAltaDensidadAction'.
     * 
     * Metodo que redirecciona a pantalla migracion olts alta densidad
     * 
     * @author Jonathan Burgos <jsburgos@telconet.ec>
     * @version 1.0
     * @since 15-02-2023
     * 
     * @return type
     */
    public function migracionOltAltaDensidadAction()
    {
        return $this->render('tecnicoBundle:InfoElementoOlt:migracionOltAltaDensidad.html.twig');
    }
    
    /**
     * 
     * Documentación para el método 'getOltMigracionGridAction'.
     * 
     * Metodo que realiza la consulta de los olts Tellion para sus posterior migración
     * 
     * @author Angel Reina <areina@telconet.ec>
     * @version 1.0
     * @since 07-03-2019
     * 
     * @author Jonathan Burgos <jsburgos@telconet.ec>
     * @version 2.0
     * @since 10-03-2023  Se elimina el perfil debido a que se valida la credencial desde el botón principal
     * 
     * @author Alberto Arias <farias@telconet.ec>
     * @version 2.1
     * @since 13-04-2023  Se agrega el prefijo Empresa para concatenar al filtro de busqueda por empresa.
     * 
     * @author Alberto Arias <farias@telconet.ec>
     * @version 2.2
     * @since 19-06-2023  Corrección del filtro de prefijo Empresa que se encuentra en sesión.
     * 
     * @return $objRespuesta
     * 
     */
    public function getOltMigracionGridAction()
    {
        $objRespuesta               = new JsonResponse();
        $objRequest                 = $this->getRequest();
        $objSession                 = $objRequest->getSession();
        $emInfraestructura          = $this->getDoctrine()->getManager('telconet_infraestructura');
        $objSession->save();
        session_write_close();
        
        $objTipoElemento            = $emInfraestructura->getRepository('schemaBundle:AdmiTipoElemento')->findBy(array( "nombreTipoElemento" =>"OLT"));
        $strNombreElemento          = $objRequest->query->get('nombreElemento');
        $strMarcaElemento           = $objRequest->query->get('marcaElemento');
        $strCanton                  = $objRequest->query->get('canton');
        $strEstado                  = $objRequest->query->get('estado');
        $intIdEmpresa               = $objRequest->get('idEmpresa');
        $intStart                   = $objRequest->query->get('start');
        $intLimit                   = $objRequest->query->get('limit');

        if(!$strMarcaElemento)
        {
            $objMarcaElemento = $emInfraestructura->getRepository('schemaBundle:AdmiMarcaElemento')->findBy(array("nombreMarcaElemento" => "TELLION"));
            $strMarcaElemento = $objMarcaElemento[0]->getId();
        }
        
        $arrayParametros = array(
            'strNombreElemento' => strtoupper($strNombreElemento),
            'strMarcaElemento'  => $strMarcaElemento,
            'strTipoElemento'   => $objTipoElemento[0]->getId(),
            'strCanton'         => $strCanton,
            'strEstado'         => $strEstado,
            'strIdEmpresa'      => $intIdEmpresa,
            'strStart'          => $intStart,
            'strLimit'          => $intLimit,
            'prefijoEmpresa'    => $objSession->get('prefijoEmpresa')
        );
        $objJson = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                    ->generarJsonOlts($arrayParametros);        
        
        $objRespuesta->setContent($objJson);
        
        return $objRespuesta;
    }    
    
    /**
     * 
     * Documentación para el método 'ajaxOltSelectMigracionAction'.
     * 
     * Metodo crea o actualiza los olt ha migrar HUAWEI - ZTE
     * 
     * @author Angel Reina <areina@telconet.ec>
     * @version 1.0 
     * @since 07-03-2019
     * 
     * @return $objRespuesta
     * 
     * @Secure(roles="ROLE_227-3497") 
     * 
     */
    
    public function ajaxOltSelectMigracionAction()
    {
        $objRespuesta               = new JsonResponse();
        $objReturnResponse          = new ReturnResponse();
        $objRequest                 = $this->getRequest();
        $objSession                 = $objRequest->getSession();
        $emInfraestructura          = $this->getDoctrine()->getManager('telconet_infraestructura');
        $objParametrosMigrar        = json_decode($objRequest->get('jsonListaOltMigrar'));
        $intCont                    = 0;
        $serviceUtil                = $this->get('schema.Util');
        
        /*
         * Lista los olt a migrar Huawei - Zte
         */
        foreach($objParametrosMigrar->arrayData as $objParametros)
        {
            /*
             * Variable contiene la funcion que se va ha realizar ( actualizar o registro )
             */
            $strFlatTrans = "";
            $intCont ++;

            /*
             * Bloque que verifica si el OLT TELLION ha sido migrado o no HUAWEI - ZTE
             */
            $objDetalleElemento = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                    ->findOneBy(array("elementoId"    => $objParametros->idElemento, 
                                                                      "detalleNombre" => "MIGRACION HUAWEI-ZTE",
                                                                      "estado"        => "Activo"));
            
            /*
             * Apertura Iniciar Transacción
             */
            
            $emInfraestructura->getConnection()->beginTransaction();
                
            
            /*
             * Entra en un try{} catch(){} para el control de errrores creacion y actualización
             */
            try
            {
                
                if ($objDetalleElemento)
                {
                    $objDetalleElemento->setDetalleValor($objParametros->strmigracionHuaweiZte);
                    $emInfraestructura->persist($objDetalleElemento);
                    $emInfraestructura->flush();
                    $strFlatTrans = "actualizar";
                }
                else
                {
                    $objDetalleElementoMigra = new InfoDetalleElemento();
                    $objDetalleElementoMigra->setElementoId($objParametros->idElemento);
                    $objDetalleElementoMigra->setDetalleNombre("MIGRACION HUAWEI-ZTE");
                    $objDetalleElementoMigra->setDetalleValor($objParametros->strmigracionHuaweiZte);
                    $objDetalleElementoMigra->setDetalleDescripcion("MIGRACION OLT TELLION A HUAWEI-ZTE");
                    $objDetalleElementoMigra->setUsrCreacion($objSession->get('user'));
                    $objDetalleElementoMigra->setFeCreacion(new \DateTime('now'));
                    $objDetalleElementoMigra->setIpCreacion($objRequest->getClientIp());
                    $objDetalleElementoMigra->setEstado(self::ESTADO_ACTIVO);
                    $emInfraestructura->persist($objDetalleElementoMigra);
                    $emInfraestructura->flush();
                    $strFlatTrans = "Registro";
                }
                
                $emInfraestructura->getConnection()->commit();
                $objReturnResponse->add($objReturnResponse::MSN_PROCESS_SUCCESS.$intCont." ".$objParametros->nombreElemento." ".$strFlatTrans, "EXITO");
            } 
            catch (Exception $ex)
            {
                if($emInfraestructura->getConnection()->isTransactionActive())
                {
                    $emInfraestructura->getConnection()->rollback();
                    $emInfraestructura->getConnection()->close();
                }
                
                $serviceUtil->insertError('TELCOS+',
                                          'TecnicoBundle.DefaultController.ajaxOltSelectMigracionAction',
                                          $ex->getMessage(),
                                          $objSession->get('user'),
                                          $objRequest->getClientIp());
                
                $objReturnResponse->add($objParametros->idElemento." ".$objParametros->nombreElemento." ".$strFlatTrans." ".$objReturnResponse::MSN_ERROR , "ERROR");
            }
        }
        
        $objRespuesta->setContent(json_encode((array) $objReturnResponse));
        return $objRespuesta;
    }
    
    /**
     * 
     * Documentación para el método 'getOltMigracionAction'.
     * 
     * Metodo que migra Mcafee a Kaspersky
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.0 
     * @since 02-10-2019
     * 
     * @return $objRespuesta
     * 
     * @Secure(roles="ROLE_227-6837") 
     * 
     */
    public function getOltMigracionAction()
    {
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        
        $objEm           = $this->getDoctrine()->getManager('telconet_infraestructura');
        $strTipoElemento = $objEm->getRepository('schemaBundle:AdmiTipoElemento')->findBy(array( "nombreTipoElemento" =>"OLT"));
        
        $objPeticion = $this->get('request');
        
        $strNombreElemento  = $objPeticion->query->get('nombreElemento');
        $intIdElemento      = $objPeticion->query->get('idElemento');        
        $intStart           = $objPeticion->query->get('start');
        $intLimit           = $objPeticion->query->get('limit');
        
        $strIndElemento     = $objPeticion->query->get('arrayElemento');
        $arrayIndElemento   = $strIndElemento ? explode('|', $strIndElemento) : array();
        
        $arrayParametros    = array();
        $arrayParametros['strTipoElemento']        = $strTipoElemento[0]->getId();
        $arrayParametros['strNombreElemento']      = $strNombreElemento;
        $arrayParametros['intIdElemento']          = $intIdElemento;
        $arrayParametros['intStart']               = $intStart;
        $arrayParametros['intLimit']               = $intLimit;
        
        
              
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:InfoElemento')
            ->generarJsonOltsMigracion($arrayParametros,$arrayIndElemento);
        $objRespuesta->setContent($objJson);
        
        return $objRespuesta;
    }
    
    /**
     * 
     * Documentación para el método 'grabaMigracionOltAction'.
     * 
     * Método que graba olts con Mcafee para su posterior migración a Kaspersky
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.0 
     * @since 07-10-2019
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 29-11-2019 Se modifica el mensaje descriptivo en el detalle elemento del olt por sugerencia en la revisión de Calidad y 
     *                          tomando en cuenta que no existen afectaciones por dicho cambio
     * 
     * @return $objRespuesta
     * 
     */
    public function grabaMigracionOltAction()
    {
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        
        $objEm              = $this->getDoctrine()->getManager('telconet_infraestructura');
                
        $objPeticion        = $this->get('request');
        $objSession         = $objPeticion->getSession();
        $strUserSession     = $objPeticion->get('user');
        $strIpCreacion      = $objPeticion->getClientIp();
        $objServiceUtil     = $this->get('schema.Util');
        
        $strIndElemento     = $objPeticion->get('arrayListado');
        $arrayIndElemento   = $strIndElemento ? explode('|', $strIndElemento) : array();
        
        $objEm->getConnection()->beginTransaction();
        
        try
        {
            foreach( $arrayIndElemento as $intIdElemento)
            {    
                if ($intIdElemento <> '')
                {
                    //Pregunto si ya existe el elemento con migración masiva
                    $objDetalleElementoId = $objEm->getRepository('schemaBundle:InfoDetalleElemento')
                                                         ->findOneBy(array( "elementoId"    => $intIdElemento, 
                                                                            "detalleNombre" => "MIGRACION MASIVA",
                                                                            "detalleValor"  => "INICIO"));
                    
                    if (!$objDetalleElementoId)
                    {
                        $objDetalleElemento = new InfoDetalleElemento();
                        $objDetalleElemento->setElementoId($intIdElemento);
                        $objDetalleElemento->setDetalleNombre("MIGRACION MASIVA");
                        $objDetalleElemento->setDetalleValor("INICIO");
                        $objDetalleElemento->setDetalleDescripcion("Migración masiva Kaspersky Iniciada");
                        $objDetalleElemento->setUsrCreacion($objSession->get('user'));
                        $objDetalleElemento->setFeCreacion(new \DateTime('now'));
                        $objDetalleElemento->setIpCreacion($objPeticion->getClientIp());
                        $objDetalleElemento->setEstado('Activo');
                        $objEm->persist($objDetalleElemento);
                        $objEm->flush();
                    }
                }    
            }        
        
            $objEm->commit();
            $objRespuesta->setContent("OK");
        } 
        catch (\Exception $objEx) 
        {
            if ($objEm->getConnection()->isTransactionActive())
            {
                $objEm->rollback();
            }
                
            $objEm->close();
            
            $objServiceUtil->insertError('Telcos+',
                                      'InfoElementoOltController->grabaMigracionOltAction',
                                      $objEx->getMessage(),
                                      $strUserSession,
                                      $strIpCreacion);
            
            $objResultado = json_encode(array('success' => false, 'mensaje' => 'Error al Ingresar Elemento. Notificar a Sistemas'));
            $objRespuesta->setContent($objResultado);
        }
                                
        return $objRespuesta;
    }
    
    /**
     * @Secure(roles="ROLE_227-6957")
     * 
     * Documentación para el método 'getOltsInicioCpmAction'.
     *
     * Método utilizado para obtener los olts que están listos para la ejecución de un cambio de plan masivo
     * 
     * @return $objJsonResponse
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 27-11-2019
     * 
     */
    public function getOltsInicioCpmAction()
    {
        $emInfraestructura      = $this->getDoctrine()->getManager('telconet_infraestructura');
        $strDatabaseDsn         = $this->container->getParameter('database_dsn');
        $strUserComercial       = $this->container->getParameter('user_comercial');
        $strPasswordComercial   = $this->container->getParameter('passwd_comercial');
        $objRequest             = $this->getRequest();
        $intStart               = $objRequest->get('start') ? $objRequest->get('start') : 0;
        $intLimit               = $objRequest->get('limit') ? $objRequest->get('limit') : 0;
        $objSession             = $objRequest->getSession();
        $strIdEmpresa           = $objSession->get('idEmpresa');
        $strPrefijoEmpresa      = $objSession->get('prefijoEmpresa');
        $objJsonResponse        = new JsonResponse();
        $arrayParametros        = array("strDatabaseDsn"            => $strDatabaseDsn,
                                        "strUserComercial"          => $strUserComercial,
                                        "strPasswordComercial"      => $strPasswordComercial,
                                        "strRetornaDataOltsCpm"     => "SI",
                                        "strRetornaTotalOltsCpm"    => "SI",
                                        "intStart"                  => $intStart,
                                        "intLimit"                  => $intLimit,
                                        "idEmpresa"                 => $strIdEmpresa,
                                        "strPrefijoEmpresa"         => $strPrefijoEmpresa);
        $strJsonResponse        = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->getJsonOltsInicioCpm($arrayParametros);
        $objJsonResponse->setContent($strJsonResponse);
        return $objJsonResponse;
    }
    
    /**
     * @Secure(roles="ROLE_227-6957")
     * 
     * Documentación para el método 'getOltsInicioCpmAction'.
     *
     * Método utilizado para ejecutar el cambio de plan masivo de todos los olts
     * 
     * @return $objJsonResponse
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 28-11-2019
     * 
     * @author Aberto Arias <farias@telconet.ec>
     * @version 1.1
     * @since 12-04-2023 Se agrega filtro por empresa en sesión
     */
    public function ejecutarCpmOltsAction()
    {
        $objJsonResponse        = new JsonResponse();
        $emInfraestructura      = $this->getDoctrine()->getManager('telconet_infraestructura');
        $strMensajeRespuesta    = "";
        $objRequest             = $this->getRequest();
        $objSession             = $objRequest->getSession();
        $strUsrCreacion         = $objSession->get('user');
        $strIpCreacion          = $objRequest->getClientIp();
        $strStatus              = str_repeat(' ', 5);
        $strMensaje             = str_repeat(' ', 2000);
        $intIdOlt               = 0;
        $intIdEmpresa           = $objSession->get('idEmpresa');
        $strPrefijoEmpresa      = $objSession->get('prefijoEmpresa');
        $strSql                 = "BEGIN INFRK_TRANSACCIONES.P_EJECUTA_CPM_OLTS(:intIdOlt, :strUsrCreacion, :strIpCreacion, "
                                                                                .":strStatus, :strMensaje, :intIdEmpresa, :strPrefijoEmpresa); END;";
        $objStmt                = $emInfraestructura->getConnection()->prepare($strSql);
        $objStmt->bindParam('intIdOlt', $intIdOlt);
        $objStmt->bindParam('strUsrCreacion', $strUsrCreacion);
        $objStmt->bindParam('strIpCreacion', $strIpCreacion);
        $objStmt->bindParam('strStatus', $strStatus);
        $objStmt->bindParam('strMensaje', $strMensaje);
        $objStmt->bindParam('intIdEmpresa', $intIdEmpresa);
        $objStmt->bindParam('strPrefijoEmpresa', $strPrefijoEmpresa);
        $objStmt->execute();
        if(strlen(trim($strStatus)) > 0)
        {
            $strMensajeRespuesta = $strMensaje;
        }
        else
        {
            $strStatus              = "ERROR";
            $strMensajeRespuesta    = 'Ha ocurrido un error inesperado. Por favor comuníquese con Sistemas!';
        }
        $arrayRespuesta = array("status" => $strStatus, "mensaje" => $strMensajeRespuesta);
        $objJsonResponse->setData($arrayRespuesta);
        return $objJsonResponse;
    }
        
    /**
     * 
     * Documentación para el método 'getAgregarSolicitudAction'.
     * 
     * Método que lista Olts para generar solicitud de agregar equipo
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.0 
     * @since 11-03-2020
     * 
     * @return type
     * 
     * @Secure(roles="ROLE_227-7317") 
     * 
     */
    public function getAgregarSolicitudAction()
    {
        $emGeneral             = $this->getDoctrine()->getManager('telconet_general');
        $intCantidadMaximaOlts = 2000;

        $arrayParametros = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                     ->getOne('CANTIDAD_MAXIMA_SOL_AGREGAR_EQUIPO',
                                              'TECNICO',
                                              'SOLICITUD DE AGREGAR EQUIPO',
                                              'CANTIDAD MAXIMA',
                                              '',
                                              '',
                                              '',
                                              '',
                                              '',
                                              '');

        if(isset($arrayParametros["valor1"]) && !empty($arrayParametros["valor1"]))
        {
            $intCantidadMaximaOlts = $arrayParametros["valor1"];
        }

        return $this->render('tecnicoBundle:InfoElementoOlt:agregarSolicitudEquipo.html.twig',array("cantidadMaximaOlts" => $intCantidadMaximaOlts));
    }

    /**
     * 
     * Documentación para el método 'getOltSolAgregarEquipoGridAction'.
     * 
     * Metodo que realiza la consulta de los olts para crear la solicitud de agregar equipo
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.0
     * @since 13-03-2020
     * 
     * @return $objRespuesta
     * 
     * @Secure(roles="ROLE_227-7317") 
     */
    public function getOltSolAgregarEquipoGridAction()
    {
        $objRespuesta               = new JsonResponse();
        $objRequest                 = $this->getRequest();
        $objSession                 = $objRequest->getSession();
        $emInfraestructura          = $this->getDoctrine()->getManager('telconet_infraestructura');
        $objSession->save();
        session_write_close();
        $arrayParametros= array();
        
        $strNombreElemento          = $objRequest->query->get('nombreElemento');
        $strModeloElemento          = $objRequest->query->get('modeloElemento');
        $strPlanNombre              = $objRequest->query->get('planNombre');
        $intIdElemento              = $strNombreElemento;
        
        $arrayParametros['intIdElemento']       = $intIdElemento;
        $arrayParametros['intIdModeloElemento'] = $strModeloElemento;
        $arrayParametros['intIdPlan']           = $strPlanNombre;
        $arrayParametros['intStart']            = $intStart;
        $arrayParametros['intLimit']            = $intLimit;
        
        $objJson = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                    ->generarJsonAgregarSolicitud($arrayParametros);
        $objRespuesta->setContent($objJson);
        
        return $objRespuesta;
    }
    
    /**
     * Documentación para el método 'listadoPlanesAction'.
     * 
     * Método que obtiene un listado de planes.
     * 
     * @return Response Listado detallado de planes.
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.1 16-03-2020 - Se realiza consulta para obtener el listado de planes y por su nombre.
     *  
     */
	public function listadoPlanesAction()
	{
        $objRequest         = $this->getRequest();		    
		$strFechaDesde      = explode('T',$objRequest->get("fechaDesde"));
		$strFechaHasta      = explode('T',$objRequest->get("fechaHasta"));
		$strEstado          = $objRequest->get("estado");
		$intLimit           = $objRequest->get("limit");
        $strPage            = $objRequest->get("page");
        $intStart           = $objRequest->get("start");
        $strNombrePlan      = $objRequest->get("strNombrePlan");
        $arrayParametros    = array();
        
		$objSession         = $objRequest->getSession();
		$intIdEmpresa       = $objSession->get('idEmpresa');
		
        $objEm              = $this->get('doctrine')->getManager('telconet');
		
		if (!$strEstado)
        {
            $strEstado = 'Activo';
        }
		 
		if ((!$strFechaDesde[0])&&(!$strFechaHasta[0]))
		{
            $arrayParametros['strEstado']       = $strEstado;
            $arrayParametros['intIdEmpresa']    = $intIdEmpresa;
            $arrayParametros['intStart']        = $intStart;
            $arrayParametros['strNombrePlan']   = $strNombrePlan;
            
			$objResultado = $objEm->getRepository('schemaBundle:InfoPlanCab')->find30PlanesPorEmpresaPorEstado( $arrayParametros );
			$objDatos = $objResultado['registros'];
			$intTotal = $objResultado['total'];
		}
		else
		{
			$objResultado= $objEm->getRepository('schemaBundle:InfoPlanCab')
                            ->findPlanesPorCriterios($strEstado,$intIdEmpresa,$strFechaDesde[0],$strFechaHasta[0],$intLimit, $strPage, $intStart);
			$objDatos = $objResultado['registros'];
			$intTotal = $objResultado['total'];
		}
		
		
		$intContador=1;
		foreach ($objDatos as $objDatos):
				$strUrlVer      = $this->generateUrl('infoplancab_show', array('id' => $objDatos->getId()));
				$strUrlEditar   = $this->generateUrl('infoplancab_edit', array('id' => $objDatos->getId()));
				$strUrlEliminar = $this->generateUrl('infoplancab_delete', array('id' => $objDatos->getId()));
						
				$strLinkVer         = $strUrlVer;
				$strLinkEliminar    = $strUrlEliminar;
				$strLinkEditar      = $strUrlEditar;
				
				$arrayListado[]= array(
				'id_plan'=> $objDatos->getId(),
                'nombre_plan'=> $objDatos->getNombrePlan(),
				'Codigo'=> $objDatos->getCodigoPlan(),
				'Descripcion'=> $objDatos->getDescripcionPlan(),
				'Fecreacion'=> strval(date_format($objDatos->getFeCreacion(),"d/m/Y G:i")),
				'estado'=> $objDatos->getEstado(),
				'linkVer'=> $strLinkVer,
				'linkEditar'=> $strLinkEditar,
				'linkEliminar'=> $strLinkEliminar,
                 );             
                 
                $intContador++;     
		endforeach;
        
		if (!empty($arrayListado))
        {    
			$objResponse = new Response(json_encode(array('total' => $intTotal, 'encontrados' => $arrayListado)));
        }
        else
		{
			$arrayListado[]= array(
				'id_plan'=> "",
                'nombre_plan'=> "",
				'Codigo'=> "",
				'Descripcion'=> "",
				'Fecreacion'=> "",
				'estado'=> "",
				'linkVer'=> "",
				'linkEditar'=> "",
				'linkEliminar'=> ""
			);
			$objResponse = new Response(json_encode(array('total' => 0, 'encontrados' => $arrayListado)));
		}
		
		$objResponse->headers->set('Content-type', 'text/json');
		return $objResponse;
		
    }
    
    /**
     * 
     * Documentación para el método 'ajaxSolAgregarEquipo'.
     * 
     * Metodo para generar solicitud de agregar equipo del grid de asignaciones
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.0 
     * @since 23-03-2020
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.1 29-05-2020  - Se obtiene el usuario que genera la solicitud
     * 
     * @return $objRespuesta
     * 
     * @Secure(roles="ROLE_227-7317") 
     * 
     */
    
    public function ajaxSolAgregarEquipoAction()
    {
        $objRespuesta               = new JsonResponse();
        $objReturnResponse          = new ReturnResponse();
        $objRequest                 = $this->getRequest();
        $objSession                 = $objRequest->getSession();
        $strUsrCreacion             = $objSession->get('user');
        
        $objParametrosAgregar       = json_decode($objRequest->get('jsonListaAgregarEquipo'));
        $intModeloAsig              = $objRequest->get('modeloElementoAsig');
        $objElementoService         = $this->get("tecnico.InfoElemento");
        $intCont                    = 0;
        $arrayParametros            = array();
        $arrayRespuestaTotal        = array();
        $arrayParametrosVariables   = array();
        $strJsonSolicitud           = '';
        
                
        //Recorremos el listado del grid de Asignaciones
        foreach($objParametrosAgregar->arrayData as $objParametros)
        {
            $intCont ++;
            
            $arrayParametros['intIdElemento']       = $objParametros->intIdElemento;
            $arrayParametros['intIdServicio']       = $objParametros->intIdServicio;
            $arrayParametros['strNombreElemento']   = $objParametros->strNombreElemento;
            $arrayParametros['strModeloNombre']     = $objParametros->strModeloNombre;
            $arrayParametros['strNombrePlan']       = $objParametros->strNombrePlan;
            $arrayParametros['strLogin']            = $objParametros->strLogin;
            $arrayParametros['strEstado']           = $objParametros->strEstado;
            $arrayParametros['intIdSolicitud']      = $objParametros->intIdSolicitud;
            $arrayParametros['intIdModAsig']        = $intModeloAsig;
            
            if ($strJsonSolicitud == '')
            {
                $strJsonSolicitud     = '['.json_encode($arrayParametros);
            }
            else
            {
                $strJsonSolicitud     = $strJsonSolicitud.','.json_encode($arrayParametros);
            }
            
        }
        
        $strJsonSolicitud       = $strJsonSolicitud.']';
        $strNumeroRegis         = $intCont;
        
        $arrayParametrosVariables['strNumeroRegis']       = $strNumeroRegis;
        $arrayParametrosVariables['strUsrCreacion']       = $strUsrCreacion;
        
        $arrayRespuesta         = $objElementoService->generarSolAgregarEquipo($strJsonSolicitud,$arrayParametrosVariables);
        $arrayRespuestaTotal    = array('mensaje'        => $arrayRespuesta['strMensaje'],
                                        'respuesta'      => $arrayRespuesta['strRespuesta'],
                                        'status'         => $arrayRespuesta['strStatus']);
        
        if ($arrayRespuestaTotal['mensaje'] == 'PROCESO EXITOSO')
        {
            $objReturnResponse->add($objReturnResponse::MSN_PROCESS_SUCCESS, "EXITO");
        }
        else
        {
            $objReturnResponse->add($objReturnResponse::MSN_ERROR, "ERROR");
        }
        
        $objRespuesta->setContent(json_encode((array) $objReturnResponse));
        return $objRespuesta;
         
    }
    
    /**
     * Documentación para el método 'listadoOltAction'.
     * 
     * Método que obtiene un listado de olts.
     * 
     * @return Response Listado detallado de olts.
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.1 16-03-2020 - Se realiza consulta para obtener el listado de olts y por su nombre.
     *  
     */
	public function listadoOltAction()
	{
        $objRequest     = $this->getRequest();		    
		        
		$objSession     = $objRequest->getSession();
		$intIdEmpresa   = $objSession->get('idEmpresa');
		
        $objEm          = $this->getDoctrine()->getManager('telconet_infraestructura');
		
        $arrayParametros['intIdEmpresa']    = $intIdEmpresa;
                   
		$objJson        = $objEm->getRepository('schemaBundle:InfoElemento')->generarJsonListadoOlt( $arrayParametros );
        $objDatos       = $objJson['encontrados'];
        $intTotal       = $objJson['total'];
        
        $intContador=1;
		foreach ($objDatos as $objDatos):
			$arrayListado[]= array(
				'idElemento'=> $objDatos['idElemento'],
                'nombreElemento'=> $objDatos['nombreElemento']
            );             
            $intContador++;     
		endforeach;
        
		if (!empty($arrayListado))
        {    
			$objResponse = new Response(json_encode(array('total' => $intTotal, 'encontrados' => $arrayListado)));
        }
        else
		{
			$arrayListado[]= array(
				'idElemento'=> "",
                'nombreElemento'=> ""
			);
			$objResponse = new Response(json_encode(array('total' => 0, 'encontrados' => $arrayListado)));
		}
		
		$objResponse->headers->set('Content-type', 'text/json');
		return $objResponse;
    }

    /**
     * @Secure(roles="ROLE_227-8017")
     *
     * Documentación para el método 'ajaxOltMultiplataformaAction'.
     *
     * Obtiene el listado de los Olt y Nodos asignados para multiplataforma
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 26-04-2021
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.1 27-07-2021 - Se agrega filtro del agregador.
     *
     * @return Response $objResponse - Lista de los Olt y Nodos asignados para multiplataforma
     */
    public function ajaxOltMultiplataformaAction()
    {
        ini_set('max_execution_time', 3000000);
        $objRequest   = $this->getRequest();
        $objSesion    = $objRequest->getSession();
        $strIpClient  = $objRequest->getClientIp();
        $strUsrSesion = $objSesion->get('user');
        $serviceUtil  = $this->get('schema.Util');
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        $strNombreOlt      = $objRequest->get("strNombreOlt");
        $strNombreNodo     = $objRequest->get("strNombreNodo");
        $strNombrePe       = $objRequest->get("strNombrePe");
        $strIpv6           = $objRequest->get("strIpv6");
        $strNombreAgregador = $objRequest->get("strNombreAgregador");

        try
        {
            $arrayParametros = [
                'strNombreOlt'  => $strNombreOlt,
                'strNombreNodo' => $strNombreNodo,
                'strNombrePe'   => $strNombrePe,
                'strIpv6'       => $strIpv6,
                'strNombreAgregador' => $strNombreAgregador,
            ];
            $arrayDatosOlt = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->getOltsMultiplatafroma($arrayParametros);
            if( $arrayDatosOlt['status'] == 'OK' )
            {
                $arrayResult = $arrayDatosOlt['result'];
            }
            else
            {
                throw new \Exception($arrayDatosOlt['result']);
            }
            //se formula el json de respuesta
            $strJsonResultado   = '{"total":"' . count($arrayResult) . '","registros":' . json_encode($arrayResult) . '}';
        }
        catch (\Exception $e)
        {
            $strJsonResultado   = '{"total":"0", "registros":[], "error":[' . $e->getMessage() . ']}';
            
            $serviceUtil->insertError('Telcos+',
                                      'InfoElementoOltController.ajaxOltMultiplataformaAction',
                                      $e->getMessage(),
                                      $strUsrSesion,
                                      $strIpClient
                                     );
        }

        $objResponse = new Response();
        $objResponse->headers->set('Content-type', 'text/json');
        $objResponse->setContent($strJsonResultado);

        return $objResponse;
    }

    /**
     * @Secure(roles="ROLE_227-8017")
     *
     * Documentación para el método 'ajaxGetDatosOltMultiplataformaAction'.
     *
     * Obtiene los datos de recursos asignados al Olt Multiplataforma.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 26-04-2021
     *
     * @return Response $objResponse - Lista de los datos de recursos asignados al Olt Multiplataforma.
     */
    public function ajaxGetDatosOltMultiplataformaAction()
    {
        ini_set('max_execution_time', 3000000);
        $objRequest   = $this->getRequest();
        $objSesion    = $objRequest->getSession();
        $strIpClient  = $objRequest->getClientIp();
        $strUsrSesion = $objSesion->get('user');
        $serviceUtil  = $this->get('schema.Util');
        $emComercial       = $this->getDoctrine()->getManager();
        $strIdSolicitud    = $objRequest->get("strIdSolicitud");

        try
        {
            //seteo el arreglo de respuesta
            $arrayResult         = array();
            //obtengo los historiales de la solicitud
            $arrayDetalleSolHist = $emComercial->getRepository('schemaBundle:InfoDetalleSolHist')
                                                      ->findBy(array("detalleSolicitudId"    => $strIdSolicitud),
                                                               array("id" => "ASC"));
            foreach($arrayDetalleSolHist as $objDetalleSolHist)
            {
                //obtengo el historial
                $arrayResult[]['historial'] = $objDetalleSolHist->getObservacion();
            }

            //se formula el json de respuesta
            $strJsonResultado   = '{"total":"' . count($arrayResult) . '","registros":' . json_encode($arrayResult) . '}';
        }
        catch (\Exception $e)
        {
            $strJsonResultado   = '{"total":"0", "registros":[], "error":[' . $e->getMessage() . ']}';
            
            $serviceUtil->insertError('Telcos+',
                                      'InfoElementoOltController.ajaxGetDatosOltMultiplataformaAction',
                                      $e->getMessage(),
                                      $strUsrSesion,
                                      $strIpClient
                                     );
        }

        $objResponse = new Response();
        $objResponse->headers->set('Content-type', 'text/json');
        $objResponse->setContent($strJsonResultado);

        return $objResponse;
    }

    /**
     * @Secure(roles="ROLE_227-8018")
     *
     * Documentación para el método 'ajaxGetOltNodoMultiplataformaAction'.
     *
     * Obtiene el listado de los olt o nodos para asignación multiplataforma
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 26-04-2021
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.1 27-07-2021 - Se obtiene el elemento Nodo con la relación del Olt
     *
     * @return Response $objResponse - Lista de los olt o nodos para asignación multiplataforma
     */
    public function ajaxGetOltNodoMultiplataformaAction()
    {
        ini_set('max_execution_time', 3000000);
        $objRequest   = $this->getRequest();
        $objSesion    = $objRequest->getSession();
        $strIpClient  = $objRequest->getClientIp();
        $strUsrSesion = $objSesion->get('user');
        $serviceUtil  = $this->get('schema.Util');
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emGeneral         = $this->getDoctrine()->getManager('telconet_general');
        $serviceElemento   = $this->get('tecnico.InfoElemento');
        $strTipo      = $objRequest->get("tipo");
        $strEstado    = $objRequest->get("estado");
        $strEstadoNot = $objRequest->get("estadoNot");
        $strNombre    = $objRequest->get("query");
        $intLimit     = $objRequest->get("limit");
        $strIdOlt     = $objRequest->get("strIdOlt");

        try
        {
            //seteo variable de id de la jurisdicciones permitidas
            $arrayIdJurisdiccion  = array();
            //obtengo el parametro de la jurisdicciones disponibles
            $objParametroCab      = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')->findOneBy(
                                                array('nombreParametro' => 'CIUDADES_DISPONIBLES_OLT_MULTIPLATAFORMA',
                                                      'estado'          => 'Activo'));
            if(is_object($objParametroCab))
            {
                //obtengo los detalles del parametro de la jurisdicciones disponibles
                $arrayParDetalles = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findBy(
                                                                array("parametroId" => $objParametroCab->getId(),
                                                                      "estado"      => "Activo"));
                foreach($arrayParDetalles as $objParDetalles)
                {
                    $arrayIdJurisdiccion[] = $objParDetalles->getValor1();
                }
            }
            //se verifica si se seteo el olt
            $intIdElemento = null;
            if(!empty($strIdOlt))
            {
                //verifico si el Olt esta en el mismo nodo
                $objElementoNodoOlt = $serviceElemento->getElementoNodoPorOlt(array("intIdOlt"     => $strIdOlt,
                                                                                    "strUsrSesion" => $strUsrSesion,
                                                                                    "strIpClient"  => $strIpClient));
                if(is_object($objElementoNodoOlt))
                {
                    $intIdElemento = $objElementoNodoOlt->getId();
                }
                if(empty($intIdElemento))
                {
                    throw new \Exception("No se encuentra el Nodo asignado a este Olt, por favor notificar a Sistemas.");
                }
            }
            //se setea los parámetros
            $arrayParametros = [
                'intIdElemento'       => $intIdElemento,
                'strTipo'             => $strTipo,
                'strNombre'           => $strNombre,
                'strEstado'           => $strEstado,
                'strEstadoNot'        => $strEstadoNot,
                'arrayIdJurisdiccion' => $arrayIdJurisdiccion,
                'intLimit'            => $intLimit,
            ];
            $arrayDatosElementos = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->getEleOltNodoMultiplataforma($arrayParametros);
            if( $arrayDatosElementos['status'] == 'OK' )
            {
                $arrayResult = $arrayDatosElementos['result'];
            }
            else
            {
                throw new \Exception($arrayDatosElementos['result']);
            }
            //se formula el json de respuesta
            $strJsonResultado   = '{"total":"' . count($arrayResult) . '","registros":' . json_encode($arrayResult) . '}';
        }
        catch (\Exception $e)
        {
            $strJsonResultado   = '{"total":"0", "registros":[], "error":"' . $e->getMessage() . '"}';
            $serviceUtil->insertError('Telcos+',
                                      'InfoElementoOltController.ajaxGetOltNodoMultiplataformaAction',
                                      $e->getMessage(),
                                      $strUsrSesion,
                                      $strIpClient
                                     );
        }

        $objResponse = new Response();
        $objResponse->headers->set('Content-type', 'text/json');
        $objResponse->setContent($strJsonResultado);

        return $objResponse;
    }

    /**
     * @Secure(roles="ROLE_227-8018")
     *
     * Documentación para el método 'ajaxAsignarOltMultiplataformaAction'.
     *
     * Función que agrega el olt y el nodo multiplataforma
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 26-04-2021
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.1 27-07-2021 - Se obtiene el agregador y el pe, se envía al ws de networking
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.2 11-03-2022 - Se envia el usuario de la acción
     *
     * @return Response $objResponse - Estado de la operación y el mensaje de resultado o error
     */
    public function ajaxAsignarOltMultiplataformaAction()
    {
        ini_set('max_execution_time', 3000000);
        $objRequest   = $this->getRequest();
        $objSesion    = $objRequest->getSession();
        $serviceUtil  = $this->get('schema.Util');
        $strIpClient  = $objRequest->getClientIp();
        $strUsrSesion = $objSesion->get('user');
        $serviceNetworking = $this->get('tecnico.NetworkingScripts');
        $serviceSoporte    = $this->get('soporte.soporteservice');
        $serviceElemento   = $this->get('tecnico.InfoElemento');
        $emComercial       = $this->getDoctrine()->getManager();
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emGeneral         = $this->getDoctrine()->getManager('telconet_general');
        $strIdEmpresa = $objSesion->get('idEmpresa');
        $strIdOlt     = $objRequest->get("strIdOlt");
        $strIdNodo    = $objRequest->get("strIdNodo");
        $strIpv6      = trim($objRequest->get("strIpv6"));

        try
        {
            $emInfraestructura->getConnection()->beginTransaction();
            $emComercial->getConnection()->beginTransaction();

            //seteo las variables
            $strDetalleAgregadorAsig = self::DETALLE_ELEMENTO_AGREGADOR_ASIGNADO;
            $strDetallePeAsignado    = self::DETALLE_ELEMENTO_PE_ASIGNADO;
            $strDetalleNodoAsignado  = self::DETALLE_ELEMENTO_NODO_ASIGNADO;
            $strDetalleIpv6          = self::DETALLE_ELEMENTO_IPV6;
            $arrayParametrosDetMulti = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->getOne('NUEVA_RED_GPON_TN',
                                                                                                    'COMERCIAL',
                                                                                                    '',
                                                                                                    'NOMBRES PARAMETROS DETALLES MULTIPLATAFORMA',
                                                                                                    '',
                                                                                                    '',
                                                                                                    '',
                                                                                                    '',
                                                                                                    '');
            if(isset($arrayParametrosDetMulti) && !empty($arrayParametrosDetMulti))
            {
                $strDetallePeAsignado   = isset($arrayParametrosDetMulti['valor2']) && !empty($arrayParametrosDetMulti['valor2'])
                                          ? $arrayParametrosDetMulti['valor2'] : $strDetallePeAsignado;
                $strDetalleNodoAsignado = isset($arrayParametrosDetMulti['valor3']) && !empty($arrayParametrosDetMulti['valor3'])
                                          ? $arrayParametrosDetMulti['valor3'] : $strDetalleNodoAsignado;
                $strDetalleIpv6         = isset($arrayParametrosDetMulti['valor4']) && !empty($arrayParametrosDetMulti['valor4'])
                                          ? $arrayParametrosDetMulti['valor4'] : $strDetalleIpv6;
                $strDetalleAgregadorAsig = isset($arrayParametrosDetMulti['valor7']) && !empty($arrayParametrosDetMulti['valor7'])
                                          ? $arrayParametrosDetMulti['valor7'] : $strDetalleAgregadorAsig;
            }

            //validar ip
            if(!filter_var($strIpv6, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6))
            {
                throw new \Exception("La dirección IP ($strIpv6) es incorrecta.");
            }
            //obtengo el elemento olt
            $objOltElemento = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($strIdOlt);
            if(!is_object($objOltElemento))
            {
                throw new \Exception("No se encontró el elemento Olt, por favor notificar a Sistemas.");
            }
            //obtengo los datos del olt multiplataforma
            $arrayParOltMulti = array(
                'strNombreOlt'      => $objOltElemento->getNombreElemento(),
                'booleanLikeNombre' => false
            );
            $arrayResOltMulti = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->getOltsMultiplatafroma($arrayParOltMulti);
            if( $arrayResOltMulti['status'] == 'OK' && is_array($arrayResOltMulti['result']) && count($arrayResOltMulti['result']) > 0 )
            {
                throw new \Exception('Ya se encuentra este elemento ('.$objOltElemento->getNombreElemento().') en el proceso multiplataforma.');
            }
            //obtengo el elemento nodo
            $objNodoElemento = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($strIdNodo);
            if(!is_object($objNodoElemento))
            {
                throw new \Exception("No se encontró el elemento Nodo, por favor notificar a Sistemas.");
            }
            //verifico si el Olt esta en el mismo nodo
            $objElementoNodoOlt    = $serviceElemento->getElementoNodoPorOlt(array("intIdOlt"     => $strIdOlt,
                                                                                   "strUsrSesion" => $strUsrSesion,
                                                                                   "strIpClient"  => $strIpClient));
            if(!is_object($objElementoNodoOlt) || $objElementoNodoOlt->getId() != $objNodoElemento->getId())
            {
                throw new \Exception("El olt seleccionado no se encuentra en el mismo nodo asignado, por favor notificar a Sistemas.");
            }
            //obtener id del agregador
            $objDetEleIdAgregador = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                ->findOneBy(array('elementoId'    => $objNodoElemento->getId(),
                                                                  'detalleNombre' => $strDetalleAgregadorAsig,
                                                                  'estado'        => self::ESTADO_ACTIVO));
            if(!is_object($objDetEleIdAgregador))
            {
                throw new \Exception("No se encontró el detalle elemento ID Agregador del Nodo, por favor notificar a Sistemas.");
            }
            //obtengo el elemento agregador
            $objAgregadorElemento = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($objDetEleIdAgregador->getDetalleValor());
            if(!is_object($objAgregadorElemento))
            {
                throw new \Exception("No se encontró el elemento Agregador, por favor notificar a Sistemas.");
            }

            //obtengo el tipo de solicitud de asignación de olt multiplataforma
            $objTipoSolicitud = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                    ->findOneBy(array("descripcionSolicitud" => 'SOLICITUD OLT MULTIPLATAFORMA',
                                                      "estado"               => "Activo"));
            if(!is_object($objTipoSolicitud))
            {
                throw new \Exception("No se encontró el tipo de solicitud olt multiplataforma, por favor notificar a Sistemas.");
            }
            //obtengo la característica del elemento id del olt
            $objCaractOlt = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                           ->findOneBy(array("descripcionCaracteristica" => "ELEMENTO_OLT_ID",
                                                             "estado"                    => "Activo"));
            if(!is_object($objCaractOlt))
            {
                throw new \Exception("No se encontró la característica del elemento id del olt, por favor notificar a Sistemas.");
            }
            //obtengo la característica del elemento id del nodo
            $objCaractNodo = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                           ->findOneBy(array("descripcionCaracteristica" => "ELEMENTO_NODO_ID",
                                                             "estado"                    => "Activo"));
            if(!is_object($objCaractNodo))
            {
                throw new \Exception("No se encontró la característica del elemento id del nodo, por favor notificar a Sistemas.");
            }
            //obtengo la característica del id de la ip
            $objCaractIp = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                           ->findOneBy(array("descripcionCaracteristica" => "IP WAN",
                                                             "estado"                    => "Activo"));
            if(!is_object($objCaractIp))
            {
                throw new \Exception("No se encontró la característica de la ip wan, por favor notificar a Sistemas.");
            }
            //obtengo la característica el nombre del PE
            $objCaractPe = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                           ->findOneBy(array("descripcionCaracteristica" => "NOMBRE PE",
                                                             "estado"                    => "Activo"));
            if(!is_object($objCaractPe))
            {
                throw new \Exception("No se encontró la característica el nombre del PE, por favor notificar a Sistemas.");
            }

            //se guarda el detalle del nodo asignado
            $objDetalleElementoNodo = new InfoDetalleElemento();
            $objDetalleElementoNodo->setElementoId($objOltElemento->getId());
            $objDetalleElementoNodo->setDetalleNombre($strDetalleNodoAsignado);
            $objDetalleElementoNodo->setDetalleValor($objNodoElemento->getId());
            $objDetalleElementoNodo->setDetalleDescripcion($strDetalleNodoAsignado);
            $objDetalleElementoNodo->setFeCreacion(new \DateTime('now'));
            $objDetalleElementoNodo->setUsrCreacion($strUsrSesion);
            $objDetalleElementoNodo->setIpCreacion($strIpClient);
            $objDetalleElementoNodo->setEstado(self::ESTADO_ACTIVO);
            $emInfraestructura->persist($objDetalleElementoNodo);
            $emInfraestructura->flush();

            //se guarda la ip del elemento
            $objDetalleElementoIpv6 = new InfoDetalleElemento();
            $objDetalleElementoIpv6->setElementoId($objOltElemento->getId());
            $objDetalleElementoIpv6->setDetalleNombre($strDetalleIpv6);
            $objDetalleElementoIpv6->setDetalleValor($strIpv6);
            $objDetalleElementoIpv6->setDetalleDescripcion($strDetalleIpv6);
            $objDetalleElementoIpv6->setFeCreacion(new \DateTime('now'));
            $objDetalleElementoIpv6->setUsrCreacion($strUsrSesion);
            $objDetalleElementoIpv6->setIpCreacion($strIpClient);
            $objDetalleElementoIpv6->setEstado(self::ESTADO_ACTIVO);
            $emInfraestructura->persist($objDetalleElementoIpv6);
            $emInfraestructura->flush();

            //crear solicitud
            $objDetalleSolicitud = new InfoDetalleSolicitud();
            $objDetalleSolicitud->setElementoId($objOltElemento->getId());
            $objDetalleSolicitud->setTipoSolicitudId($objTipoSolicitud);
            $objDetalleSolicitud->setUsrCreacion($strUsrSesion);
            $objDetalleSolicitud->setFeCreacion(new \DateTime('now'));
            $objDetalleSolicitud->setObservacion("Se crea la solicitud de olt multiplataforma.");
            $objDetalleSolicitud->setEstado("Pendiente");
            $emComercial->persist($objDetalleSolicitud);
            $emComercial->flush();

            //ingreso la característica del elemento id del olt
            $objSolicitudCaractOlt = new InfoDetalleSolCaract();
            $objSolicitudCaractOlt->setDetalleSolicitudId($objDetalleSolicitud);
            $objSolicitudCaractOlt->setCaracteristicaId($objCaractOlt);
            $objSolicitudCaractOlt->setValor($objOltElemento->getId());
            $objSolicitudCaractOlt->setEstado('Activo');
            $objSolicitudCaractOlt->setFeCreacion(new \DateTime('now'));
            $objSolicitudCaractOlt->setUsrCreacion($strUsrSesion);
            $emComercial->persist($objSolicitudCaractOlt);
            $emComercial->flush();

            //ingreso la característica del elemento id del nodo
            $objSolicitudCaractNodo = new InfoDetalleSolCaract();
            $objSolicitudCaractNodo->setDetalleSolicitudId($objDetalleSolicitud);
            $objSolicitudCaractNodo->setCaracteristicaId($objCaractNodo);
            $objSolicitudCaractNodo->setValor($objNodoElemento->getId());
            $objSolicitudCaractNodo->setEstado('Activo');
            $objSolicitudCaractNodo->setFeCreacion(new \DateTime('now'));
            $objSolicitudCaractNodo->setUsrCreacion($strUsrSesion);
            $emComercial->persist($objSolicitudCaractNodo);
            $emComercial->flush();

            //ingreso la característica del id de la ip
            $objSolicitudCaractIp = new InfoDetalleSolCaract();
            $objSolicitudCaractIp->setDetalleSolicitudId($objDetalleSolicitud);
            $objSolicitudCaractIp->setCaracteristicaId($objCaractIp);
            $objSolicitudCaractIp->setValor($strIpv6);
            $objSolicitudCaractIp->setEstado('Activo');
            $objSolicitudCaractIp->setFeCreacion(new \DateTime('now'));
            $objSolicitudCaractIp->setUsrCreacion($strUsrSesion);
            $emComercial->persist($objSolicitudCaractIp);
            $emComercial->flush();

            //seteo el arreglo de parametros para asignar el olt en networking
            $arrayParametros = array(
                'url'       => 'manageIPv6',
                'accion'    => 'agregar',
                'pe'        => '',
                'id_olt'    => $objOltElemento->getId(),
                'olt'       => $objOltElemento->getNombreElemento(),
                'ipv6'      => $strIpv6,
                'id_agregador' => $objAgregadorElemento->getId(),
                'agregador'    => $objAgregadorElemento->getNombreElemento(),
                'servicio'  => 'GENERAL',
                'login_aux' => '',
                'user_name' => $strUsrSesion,
                'user_ip'   => $strIpClient
            );
            //se ejecuta script de networking
            $arrayRespuesta = $serviceNetworking->callNetworkingWebService($arrayParametros);
            if($arrayRespuesta['status'] == 'ERROR')
            {
                throw new \Exception($arrayRespuesta['mensaje']);
            }

            //verifico si existe la llave del pe
            if(isset($arrayRespuesta['pe']) && !empty($arrayRespuesta['pe']))
            {
                //obtengo el elemento pe
                $objPeElemento = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                    ->findOneBy(array('nombreElemento' => $arrayRespuesta['pe'],
                                                                      'estado'         => self::ESTADO_ACTIVO));
                if(is_object($objPeElemento))
                {
                    //se guarda el detalle del id del pe asignado al olt
                    $objDetalleElementoIdPeOlt = new InfoDetalleElemento();
                    $objDetalleElementoIdPeOlt->setElementoId($objOltElemento->getId());
                    $objDetalleElementoIdPeOlt->setDetalleNombre($strDetallePeAsignado);
                    $objDetalleElementoIdPeOlt->setDetalleValor($objPeElemento->getId());
                    $objDetalleElementoIdPeOlt->setDetalleDescripcion($strDetallePeAsignado);
                    $objDetalleElementoIdPeOlt->setFeCreacion(new \DateTime('now'));
                    $objDetalleElementoIdPeOlt->setUsrCreacion($strUsrSesion);
                    $objDetalleElementoIdPeOlt->setIpCreacion($strIpClient);
                    $objDetalleElementoIdPeOlt->setEstado(self::ESTADO_ACTIVO);
                    $emInfraestructura->persist($objDetalleElementoIdPeOlt);
                    $emInfraestructura->flush();
                    //verificar detalle del id del pe del nodo
                    $objDetalleElementoIdPeNodo = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                        ->findOneBy(array('elementoId'    => $objNodoElemento->getId(),
                                                                          'detalleNombre' => $strDetallePeAsignado,
                                                                          'detalleValor'  => $objPeElemento->getId(),
                                                                          'estado'        => self::ESTADO_ACTIVO));
                    if(!is_object($objDetalleElementoIdPeNodo))
                    {
                        //se guarda el detalle del id del pe del nodo
                        $objDetalleElementoIdPeNodo = new InfoDetalleElemento();
                        $objDetalleElementoIdPeNodo->setElementoId($objNodoElemento->getId());
                        $objDetalleElementoIdPeNodo->setDetalleNombre($strDetallePeAsignado);
                        $objDetalleElementoIdPeNodo->setDetalleValor($objPeElemento->getId());
                        $objDetalleElementoIdPeNodo->setDetalleDescripcion($strDetallePeAsignado);
                        $objDetalleElementoIdPeNodo->setFeCreacion(new \DateTime('now'));
                        $objDetalleElementoIdPeNodo->setUsrCreacion($strUsrSesion);
                        $objDetalleElementoIdPeNodo->setIpCreacion($strIpClient);
                        $objDetalleElementoIdPeNodo->setEstado(self::ESTADO_ACTIVO);
                        $emInfraestructura->persist($objDetalleElementoIdPeNodo);
                        $emInfraestructura->flush();
                    }
                    //ingreso la característica del nombre del pe
                    $objSolicitudCaractPe = new InfoDetalleSolCaract();
                    $objSolicitudCaractPe->setDetalleSolicitudId($objDetalleSolicitud);
                    $objSolicitudCaractPe->setCaracteristicaId($objCaractPe);
                    $objSolicitudCaractPe->setValor($objPeElemento->getNombreElemento());
                    $objSolicitudCaractPe->setEstado('Activo');
                    $objSolicitudCaractPe->setFeCreacion(new \DateTime('now'));
                    $objSolicitudCaractPe->setUsrCreacion($strUsrSesion);
                    $emComercial->persist($objSolicitudCaractPe);
                    $emComercial->flush();
                }
            }
            else
            {
                //agregar historial al elemento
                $objHistorialElemento = new InfoHistorialElemento();
                $objHistorialElemento->setElementoId($objOltElemento);
                $objHistorialElemento->setObservacion("No se ingresó la información del pe asignado al Olt, no se obtuvo respuesta Networking.");
                $objHistorialElemento->setFeCreacion(new \DateTime('now'));
                $objHistorialElemento->setUsrCreacion($strUsrSesion);
                $objHistorialElemento->setIpCreacion($strIpClient);
                $objHistorialElemento->setEstadoElemento($objOltElemento->getEstado());
                $emInfraestructura->persist($objHistorialElemento);
                $emInfraestructura->flush();
                //agregar historial a la solicitud
                $objDetalleSolHistorial = new InfoDetalleSolHist();
                $objDetalleSolHistorial->setDetalleSolicitudId($objDetalleSolicitud);
                $objDetalleSolHistorial->setIpCreacion($strIpClient);
                $objDetalleSolHistorial->setFeCreacion(new \DateTime('now'));
                $objDetalleSolHistorial->setUsrCreacion($strUsrSesion);
                $objDetalleSolHistorial->setEstado($objDetalleSolicitud->getEstado());
                $objDetalleSolHistorial->setObservacion("No se ingresó la característica del pe asignado al Olt, no se obtuvo respuesta Networking.");
                $emComercial->persist($objDetalleSolHistorial);
                $emComercial->flush();
            }

            //seteo la observación
            $strObservacion         = 'Se generó la Solicitud Olt Multiplataforma con los siguientes datos:<br>'.
                                      '<b>Nombre Olt:</b> '.$objOltElemento->getNombreElemento().'<br>'.
                                      '<b>Nombre Nodo:</b> '.$objNodoElemento->getNombreElemento().'<br>'.
                                      '<b>Nombre Agregador:</b> '.$objAgregadorElemento->getNombreElemento().'<br>'.
                                      '<b>Nombre PE:</b> '.$arrayRespuesta['pe'].'<br>'.
                                      '<b>Ipv6:</b> '.$strIpv6;

            //creación de la tarea interna
            $intIdTarea             = '';
            $objParametroCabTarea   = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')->findOneBy(
                                                array('nombreParametro' => 'PARAMETROS_TAREAS_OLT_MULTIPLATAFORMA',
                                                      'estado'          => 'Activo'));
            if(is_object($objParametroCabTarea))
            {
                $strNombreTarea         = '';
                $strNombreDepartamento  = '';
                $strEmpleado            = '';
                $strNombreCanton        = '';
                $strRegion              = '';
                $strPrefijoEmpresaTarea = '';
                $intIdEmpresaTarea      = '';
                $intIdPersonaEmpresaRol = '';
                //obtengo el nombre de la tarea
                $objParNombreTarea     = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findOneBy(
                                                                array("parametroId" => $objParametroCabTarea->getId(),
                                                                      "valor1"      => "AGREGAR",
                                                                      "estado"      => "Activo"));
                if(is_object($objParNombreTarea))
                {
                    $strNombreDepartamento  = $objParNombreTarea->getValor2();
                    $strNombreTarea         = $objParNombreTarea->getValor3();
                    $strPrefijoEmpresaTarea = $objParNombreTarea->getValor4();
                    $intIdEmpresaTarea      = $objParNombreTarea->getValor5();
                    $intIdPersonaEmpresaRol = $objParNombreTarea->getValor6();
                }
                //obtengo el nombre del usuario
                $objCreationUser        = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                            ->findOneBy(array('login'=>$strUsrSesion));
                if(is_object($objCreationUser))
                {
                    $strEmpleado        = $objCreationUser->getNombres().' '.$objCreationUser->getApellidos();
                }
                //obtengo la ubicacion del elemento
                $objElementoUbica = $emInfraestructura->getRepository('schemaBundle:InfoEmpresaElementoUbica')
                                                    ->findOneBy(array("elementoId" => $objOltElemento->getId()));
                if(is_object($objElementoUbica))
                {
                    $objUbicacion = $objElementoUbica->getUbicacionId();
                    if(is_object($objUbicacion))
                    {
                        $objParroquia = $objUbicacion->getParroquiaId();
                        if(is_object($objParroquia))
                        {
                            $objCanton       = $objParroquia->getCantonId();
                            $strNombreCanton = $objCanton->getNombreCanton();
                            $strRegion       = $objCanton->getRegion();
                        }
                    }
                }
                //seteo el arreglo del jefe responsable
                $arrayJefeResponsable = array();
                if(!empty($intIdPersonaEmpresaRol))
                {
                    //obtengo la persona empresa rol
                    $objPerResponsableTarea = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($intIdPersonaEmpresaRol);
                    if(is_object($objPerResponsableTarea))
                    {
                        $objPersonaResponsableTarea = $objPerResponsableTarea->getPersonaId();
                        if(is_object($objPersonaResponsableTarea))
                        {
                            $strNombreCompleto = $objPersonaResponsableTarea->getNombres()." ".$objPersonaResponsableTarea->getApellidos();
                            $arrayJefeResponsable["idPersonaEmpresaRol"]    = $intIdPersonaEmpresaRol;
                            $arrayJefeResponsable["idPersona"]              = $objPersonaResponsableTarea->getId();
                            $arrayJefeResponsable["nombreCompleto"]         = $strNombreCompleto;
                        }
                    }
                }
                //Se definen los parámetros necesarios para la creación de la tarea
                $arrayParametrosTarea = array(
                    'strIdEmpresaUser'      => $strIdEmpresa,
                    'strIdEmpresa'          => $intIdEmpresaTarea,
                    'strPrefijoEmpresa'     => $strPrefijoEmpresaTarea,
                    'strNombreTarea'        => $strNombreTarea,
                    'strObservacion'        => $strObservacion,
                    'strNombreDepartamento' => $strNombreDepartamento,
                    'strCiudad'             => $strNombreCanton,
                    'strRegion'             => $strRegion,
                    'strNombreCliente'      => 'Cliente',
                    'strEmpleado'           => $strEmpleado,
                    'strUsrCreacion'        => $strUsrSesion,
                    'strIp'                 => $strIpClient,
                    'arrayJefeResponsable'  => $arrayJefeResponsable,
                    'strOrigen'             => 'WEB-TN',
                    'strValidacionTags'     => 'NO'
                );
                //se genera la tarea interna
                $arrayResultadoTarea = $serviceSoporte->ingresarTareaInterna($arrayParametrosTarea);
                if($arrayResultadoTarea['status'] == "OK")
                {
                    $intIdTarea = $arrayResultadoTarea['id'];
                }
            }

            if(!empty($intIdTarea))
            {
                $strObservacion .= "<br><b>Tarea:</b> $intIdTarea";
            }

            //agregar historial al elemento
            $objHistorialElemento = new InfoHistorialElemento();
            $objHistorialElemento->setElementoId($objOltElemento);
            $objHistorialElemento->setObservacion($strObservacion);
            $objHistorialElemento->setFeCreacion(new \DateTime('now'));
            $objHistorialElemento->setUsrCreacion($strUsrSesion);
            $objHistorialElemento->setIpCreacion($strIpClient);
            $objHistorialElemento->setEstadoElemento($objOltElemento->getEstado());
            $emInfraestructura->persist($objHistorialElemento);
            $emInfraestructura->flush();

            //agregar historial a la solicitud
            $objDetalleSolHistorial = new InfoDetalleSolHist();
            $objDetalleSolHistorial->setDetalleSolicitudId($objDetalleSolicitud);
            $objDetalleSolHistorial->setIpCreacion($strIpClient);
            $objDetalleSolHistorial->setFeCreacion(new \DateTime('now'));
            $objDetalleSolHistorial->setUsrCreacion($strUsrSesion);
            $objDetalleSolHistorial->setEstado($objDetalleSolicitud->getEstado());
            $objDetalleSolHistorial->setObservacion($strObservacion);
            $emComercial->persist($objDetalleSolHistorial);
            $emComercial->flush();

            $emComercial->flush();
            $emComercial->getConnection()->commit();
            $emInfraestructura->flush();
            $emInfraestructura->getConnection()->commit();

            $arrayResult = array(
                'status'   => 'OK',
                'mensaje'  => $strObservacion
            );
        }
        catch (\Exception $e)
        {
            $arrayResult = array(
                'status'   => 'ERROR',
                'mensaje'  => $e->getMessage()
            );

            if ($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->getConnection()->rollback();
                $emComercial->getConnection()->close();
            }
            if ($emInfraestructura->getConnection()->isTransactionActive())
            {
                $emInfraestructura->getConnection()->rollback();
                $emInfraestructura->getConnection()->close();
            }

            $serviceUtil->insertError('Telcos+',
                                      'InfoElementoOltController.ajaxAsignarOltMultiplataformaAction',
                                      $e->getMessage(),
                                      $strUsrSesion,
                                      $strIpClient
                                     );
        }

        $objResponse = new Response();
        $objResponse->headers->set('Content-type', 'text/json');
        $objResponse->setContent(json_encode($arrayResult));

        return $objResponse;
    }

    /**
     * @Secure(roles="ROLE_227-8037")
     *
     * Documentación para el método 'ajaxRecursosOltMultiplataformaAction'.
     *
     * Función que asigna los recursos de red al olt multiplataforma
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 26-04-2021
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.1 11-03-2022 - Se realiza la validación de la solicitud del olt y se envia el usuario de la acción
     *
     * @return Response $objResponse - Estado de la operación y el mensaje de resultado o error
     */
    public function ajaxRecursosOltMultiplataformaAction()
    {
        ini_set('max_execution_time', 3000000);
        $objRequest   = $this->getRequest();
        $objSesion    = $objRequest->getSession();
        $serviceUtil  = $this->get('schema.Util');
        $strIpClient  = $objRequest->getClientIp();
        $strUsrSesion = $objSesion->get('user');
        $serviceNetworking = $this->get('tecnico.NetworkingScripts');
        $serviceSoporte    = $this->get('soporte.soporteservice');
        $emComercial       = $this->getDoctrine()->getManager();
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emGeneral         = $this->getDoctrine()->getManager('telconet_general');
        $strIdEmpresa      = $objSesion->get('idEmpresa');
        $strIdSolicitud    = $objRequest->get("strIdSolicitud");
        $arrayInterfaces   = json_decode($objRequest->get('arrayInterfaces'));

        try
        {
            $emInfraestructura->getConnection()->beginTransaction();
            $emComercial->getConnection()->beginTransaction();

            //verifico que no este vacía la variable
            if(!is_array($arrayInterfaces) || count($arrayInterfaces) < 1)
            {
                throw new \Exception("No se han seleccionado las interfaces para los recursos de red del Olt Multiplataforma.");
            }
            //limite de interface
            $intLimitInterfaces = 0;
            $arrayParVerLimite  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                        ->getOne('NUEVA_RED_GPON_TN',
                                                                                'COMERCIAL',
                                                                                '',
                                                                                '',
                                                                                'LIMITE_ASIGNAR_INTERFACES_OLT_MULTIPLATAFORMA',
                                                                                '',
                                                                                '',
                                                                                '',
                                                                                '');
            if( isset($arrayParVerLimite) && !empty($arrayParVerLimite) &&
                isset($arrayParVerLimite['valor2']) && !empty($arrayParVerLimite['valor2']) )
            {
                $intLimitInterfaces = $arrayParVerLimite['valor2'];
            }
            //verificar limite
            if($intLimitInterfaces > 0 && count($arrayInterfaces) > $intLimitInterfaces)
            {
                throw new \Exception("Las interfaces seleccionadas es mayor al límite permitido de $intLimitInterfaces interface(s).");
            }
            //obtengo los datos de la solicitud
            $arrayDatosSolicitud = $this->getDatosOltMultiplataformaPorSolicitud(array('emComercial'       => $emComercial,
                                                                                       'emInfraestructura' => $emInfraestructura,
                                                                                       'strIdSolicitud'    => $strIdSolicitud));
            if($arrayDatosSolicitud['status'] == 'ERROR')
            {
                throw new \Exception($arrayDatosSolicitud['mensaje']);
            }
            $objDetalleSolicitud = $arrayDatosSolicitud['objDetalleSolicitud'];
            $objOltElemento      = $arrayDatosSolicitud['objOltElemento'];
            $objSolCaractPe      = $arrayDatosSolicitud['objSolCaractPe'];

            //validar solicitud
            if($objDetalleSolicitud->getEstado() == "Asignado" || $objDetalleSolicitud->getEstado() == "Configurado"
               || $objDetalleSolicitud->getEstado() == "Activo")
            {
                throw new \Exception("La solicitud del olt ya está asignado para multiplataforma.");
            }
            if($objDetalleSolicitud->getEstado() != "Pendiente")
            {
                throw new \Exception("La solicitud del olt multiplataforma no está en Pendiente ".
                                     "para realizar la asignación de recursos de las interfaces.");
            }

            //seteo el arreglo de parametros para asignar los recursos de red en el olt multiplataforma
            $arrayParametros = array(
                'url'        => 'assignResources',
                'accion'     => 'asignar',
                'pe'         => $objSolCaractPe->getValor(),
                'id_olt'     => $objOltElemento->getId(),
                'olt'        => $objOltElemento->getNombreElemento(),
                'interfaces' => implode(',', $arrayInterfaces),
                'servicio'   => 'GENERAL',
                'login_aux'  => '',
                'user_name'  => $strUsrSesion,
                'user_ip'    => $strIpClient
            );
            //se ejecuta script de networking
            $arrayRespuestaNetworking = $serviceNetworking->callNetworkingWebService($arrayParametros);
            if($arrayRespuestaNetworking['status'] == 'ERROR')
            {
                throw new \Exception($arrayRespuestaNetworking['mensaje']);
            }

            //actualizo el estado de la solicitud
            $objDetalleSolicitud->setEstado("Asignado");
            $emComercial->persist($objDetalleSolicitud);
            $emComercial->flush();

            if($objOltElemento->getEstado() != 'Activo')
            {
                //actualizo el estado del elemento
                $objOltElemento->setEstado("Asignado");
                $emInfraestructura->persist($objOltElemento);
                $emInfraestructura->flush();
            }

            //seteo las variables
            $strDetalleInterfacesPe  = self::DETALLE_ELEMENTO_INTERFACES_PE;
            $arrayParametrosDetMulti = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->getOne('NUEVA_RED_GPON_TN',
                                                                                                    'COMERCIAL',
                                                                                                    '',
                                                                                                    'NOMBRES PARAMETROS DETALLES MULTIPLATAFORMA',
                                                                                                    '',
                                                                                                    '',
                                                                                                    '',
                                                                                                    '',
                                                                                                    '');
            if(isset($arrayParametrosDetMulti) && !empty($arrayParametrosDetMulti)
               && isset($arrayParametrosDetMulti['valor6']) && !empty($arrayParametrosDetMulti['valor6']))
            {
                $strDetalleInterfacesPe = $arrayParametrosDetMulti['valor6'];
            }

            //se guarda el detalle del nodo asignado
            $objDetalleElementoInterfaces = new InfoDetalleElemento();
            $objDetalleElementoInterfaces->setElementoId($objOltElemento->getId());
            $objDetalleElementoInterfaces->setDetalleNombre($strDetalleInterfacesPe);
            $objDetalleElementoInterfaces->setDetalleValor(implode(',', $arrayInterfaces));
            $objDetalleElementoInterfaces->setDetalleDescripcion($strDetalleInterfacesPe);
            $objDetalleElementoInterfaces->setFeCreacion(new \DateTime('now'));
            $objDetalleElementoInterfaces->setUsrCreacion($strUsrSesion);
            $objDetalleElementoInterfaces->setIpCreacion($strIpClient);
            $objDetalleElementoInterfaces->setEstado(self::ESTADO_ACTIVO);
            $emInfraestructura->persist($objDetalleElementoInterfaces);
            $emInfraestructura->flush();

            //seteo la observación
            $strObservacion         = 'Se asignan las siguientes interfaces para el Olt Multiplataforma:<br>'.
                                      '<b>Interfaces:</b> '.implode(',', $arrayInterfaces);

            //agregar historial al elemento
            $objHistorialElemento   = new InfoHistorialElemento();
            $objHistorialElemento->setElementoId($objOltElemento);
            $objHistorialElemento->setObservacion($strObservacion);
            $objHistorialElemento->setFeCreacion(new \DateTime('now'));
            $objHistorialElemento->setUsrCreacion($strUsrSesion);
            $objHistorialElemento->setIpCreacion($strIpClient);
            $objHistorialElemento->setEstadoElemento($objOltElemento->getEstado());
            $emInfraestructura->persist($objHistorialElemento);
            $emInfraestructura->flush();

            //agregar historial a la solicitud
            $objDetalleSolHistorial = new InfoDetalleSolHist();
            $objDetalleSolHistorial->setDetalleSolicitudId($objDetalleSolicitud);
            $objDetalleSolHistorial->setIpCreacion($strIpClient);
            $objDetalleSolHistorial->setFeCreacion(new \DateTime('now'));
            $objDetalleSolHistorial->setUsrCreacion($strUsrSesion);
            $objDetalleSolHistorial->setEstado($objDetalleSolicitud->getEstado());
            $objDetalleSolHistorial->setObservacion($strObservacion);
            $emComercial->persist($objDetalleSolHistorial);
            $emComercial->flush();

            //mensaje configuracion
            $strMensajeConf = "<h3><b>Recursos Red Asignado para el Olt Multiplataforma</b><h3><br>".
                          "<b>Olt:</b> ".$objOltElemento->getNombreElemento()."<br>".
                          "<b>bundle_id:</b> ".$arrayRespuestaNetworking['data']['bundle_id']."<br>".
                          "<b>loopback0:</b> ".$arrayRespuestaNetworking['data']['loopback0']."<br>".
                          "<b>loopback254:</b> ".$arrayRespuestaNetworking['data']['loopback254']."<br>".
                          "<b>mpls:</b><br>".
                          " &nbsp;&nbsp;&nbsp;&nbsp;<b>vlan_id:</b> ".$arrayRespuestaNetworking['data']['mpls']['vlan_id']."<br>".
                          " &nbsp;&nbsp;&nbsp;&nbsp;<b>ip_wan:</b> ".$arrayRespuestaNetworking['data']['mpls']['ip_wan']."<br>".
                          "<b>vrf_gepon:</b><br>".
                          " &nbsp;&nbsp;&nbsp;&nbsp;<b>vlan_id:</b> ".$arrayRespuestaNetworking['data']['vrf_gepon']['vlan_id']."<br>".
                          " &nbsp;&nbsp;&nbsp;&nbsp;<b>ip_wan:</b> ".$arrayRespuestaNetworking['data']['vrf_gepon']['ip_wan']."<br>".
                          "<b>vrf_telconet_pri:</b><br>".
                          " &nbsp;&nbsp;&nbsp;&nbsp;<b>vlan_id:</b> ".$arrayRespuestaNetworking['data']['vrf_telconet_pri']['vlan_id']."<br>".
                          "<b>vrf_telconet_bk:</b><br>".
                          " &nbsp;&nbsp;&nbsp;&nbsp;<b>vlan_id:</b> ".$arrayRespuestaNetworking['data']['vrf_telconet_bk']['vlan_id']."<br>".
                          "<b>vrf_monitoreo:</b><br>".
                          " &nbsp;&nbsp;&nbsp;&nbsp;<b>vlan_id:</b> ".$arrayRespuestaNetworking['data']['vrf_monitoreo']['vlan_id']."<br>".
                          " &nbsp;&nbsp;&nbsp;&nbsp;<b>ip_wan:</b> ".$arrayRespuestaNetworking['data']['vrf_monitoreo']['ip_wan'];
            //creación de la tarea interna
            $intIdTarea             = '';
            $objParametroCabTarea   = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')->findOneBy(
                                                array('nombreParametro' => 'PARAMETROS_TAREAS_OLT_MULTIPLATAFORMA',
                                                      'estado'          => 'Activo'));
            if(is_object($objParametroCabTarea))
            {
                $strNombreTarea         = '';
                $strNombreDepartamento  = '';
                $strEmpleado            = '';
                $strNombreCanton        = '';
                $strRegion              = '';
                $strPrefijoEmpresaTarea = '';
                $intIdEmpresaTarea      = '';
                $intIdPersonaEmpresaRol = '';
                //obtengo el nombre de la tarea
                $objParNombreTarea     = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findOneBy(
                                                                array("parametroId" => $objParametroCabTarea->getId(),
                                                                      "valor1"      => "ASIGNAR",
                                                                      "estado"      => "Activo"));
                if(is_object($objParNombreTarea))
                {
                    $strNombreDepartamento  = $objParNombreTarea->getValor2();
                    $strNombreTarea         = $objParNombreTarea->getValor3();
                    $strPrefijoEmpresaTarea = $objParNombreTarea->getValor4();
                    $intIdEmpresaTarea      = $objParNombreTarea->getValor5();
                    $intIdPersonaEmpresaRol = $objParNombreTarea->getValor6();
                }
                //obtengo el nombre del usuario
                $objCreationUser        = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                            ->findOneBy(array('login'=>$strUsrSesion));
                if(is_object($objCreationUser))
                {
                    $strEmpleado        = $objCreationUser->getNombres().' '.$objCreationUser->getApellidos();
                }
                //obtengo la ubicacion del elemento
                $objElementoUbica       = $emInfraestructura->getRepository('schemaBundle:InfoEmpresaElementoUbica')
                                                    ->findOneBy(array("elementoId" => $objOltElemento->getId()));
                if(is_object($objElementoUbica))
                {
                    $objUbicacion = $objElementoUbica->getUbicacionId();
                    if(is_object($objUbicacion))
                    {
                        $objParroquia = $objUbicacion->getParroquiaId();
                        if(is_object($objParroquia))
                        {
                            $objCanton       = $objParroquia->getCantonId();
                            $strNombreCanton = $objCanton->getNombreCanton();
                            $strRegion       = $objCanton->getRegion();
                        }
                    }
                }
                //seteo el arreglo del jefe responsable
                $arrayJefeResponsable = array();
                if(!empty($intIdPersonaEmpresaRol))
                {
                    //obtengo la persona empresa rol
                    $objPerResponsableTarea = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($intIdPersonaEmpresaRol);
                    if(is_object($objPerResponsableTarea))
                    {
                        $objPersonaResponsableTarea = $objPerResponsableTarea->getPersonaId();
                        if(is_object($objPersonaResponsableTarea))
                        {
                            $strNombreCompleto = $objPersonaResponsableTarea->getNombres()." ".$objPersonaResponsableTarea->getApellidos();
                            $arrayJefeResponsable["idPersonaEmpresaRol"]    = $intIdPersonaEmpresaRol;
                            $arrayJefeResponsable["idPersona"]              = $objPersonaResponsableTarea->getId();
                            $arrayJefeResponsable["nombreCompleto"]         = $strNombreCompleto;
                        }
                    }
                }
                //Se definen los parámetros necesarios para la creación de la tarea
                $arrayParametrosTarea = array(
                    'strIdEmpresaUser'      => $strIdEmpresa,
                    'strIdEmpresa'          => $intIdEmpresaTarea,
                    'strPrefijoEmpresa'     => $strPrefijoEmpresaTarea,
                    'strNombreTarea'        => $strNombreTarea,
                    'strObservacion'        => $strObservacion,
                    'strNombreDepartamento' => $strNombreDepartamento,
                    'strCiudad'             => $strNombreCanton,
                    'strRegion'             => $strRegion,
                    'strNombreCliente'      => 'Cliente',
                    'strEmpleado'           => $strEmpleado,
                    'strUsrCreacion'        => $strUsrSesion,
                    'strIp'                 => $strIpClient,
                    'arrayJefeResponsable'  => $arrayJefeResponsable,
                    'strOrigen'             => 'WEB-TN',
                    'strValidacionTags'     => 'NO'
                );
                //se genera la tarea interna
                $arrayResultadoTarea = $serviceSoporte->ingresarTareaInterna($arrayParametrosTarea);
                if($arrayResultadoTarea['status'] == "OK")
                {
                    $intIdTarea = $arrayResultadoTarea['id'];
                    $arrayParametrosHist                   = array();
                    $arrayParametrosHist["intDetalleId"]   = $arrayResultadoTarea['idDetalle'];
                    $arrayParametrosHist["strCodEmpresa"]  = $intIdEmpresaTarea;
                    $arrayParametrosHist["strUsrCreacion"] = $strUsrSesion;
                    $arrayParametrosHist["strIpCreacion"]  = $strIpClient;
                    $arrayParametrosHist["strOpcion"]      = "Seguimiento";
                    $arrayParametrosHist["strEstadoActual"] = "Asignada";
                    $arrayParametrosHist["strEnviaDepartamento"] = "N";
                    $arrayParametrosHist["strObservacion"] = $strMensajeConf;
                    $arrayParametrosHist["strCodEmpresaUser"] = $strIdEmpresa;
                    $serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);
                }
            }

            //seteo el mensaje
            $strMensaje = "";
            if(!empty($intIdTarea))
            {
                $strMensaje .= "<b>Tarea:</b> $intIdTarea<br><br>";
            }
            $strMensaje .= $strMensajeConf;

            //agregar historial a la solicitud
            $objDetalleSolHistorialRec = new InfoDetalleSolHist();
            $objDetalleSolHistorialRec->setDetalleSolicitudId($objDetalleSolicitud);
            $objDetalleSolHistorialRec->setIpCreacion($strIpClient);
            $objDetalleSolHistorialRec->setFeCreacion(new \DateTime('now'));
            $objDetalleSolHistorialRec->setUsrCreacion($strUsrSesion);
            $objDetalleSolHistorialRec->setEstado($objDetalleSolicitud->getEstado());
            $objDetalleSolHistorialRec->setObservacion($strMensaje);
            $emComercial->persist($objDetalleSolHistorialRec);
            $emComercial->flush();

            //agregar historial al elemento
            $objHistorialElementoRec = new InfoHistorialElemento();
            $objHistorialElementoRec->setElementoId($objOltElemento);
            $objHistorialElementoRec->setObservacion($strMensaje);
            $objHistorialElementoRec->setFeCreacion(new \DateTime('now'));
            $objHistorialElementoRec->setUsrCreacion($strUsrSesion);
            $objHistorialElementoRec->setIpCreacion($strIpClient);
            $objHistorialElementoRec->setEstadoElemento($objOltElemento->getEstado());
            $emInfraestructura->persist($objHistorialElementoRec);
            $emInfraestructura->flush();

            $emComercial->flush();
            $emComercial->getConnection()->commit();
            $emInfraestructura->flush();
            $emInfraestructura->getConnection()->commit();

            $arrayResult = array(
                'status'   => 'OK',
                'mensaje'  => $strMensaje
            );
        }
        catch (\Exception $e)
        {
            $arrayResult = array(
                'status'   => 'ERROR',
                'mensaje'  => $e->getMessage()
            );

            if ($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->getConnection()->rollback();
                $emComercial->getConnection()->close();
            }
            if ($emInfraestructura->getConnection()->isTransactionActive())
            {
                $emInfraestructura->getConnection()->rollback();
                $emInfraestructura->getConnection()->close();
            }

            $serviceUtil->insertError('Telcos+',
                                      'InfoElementoOltController.ajaxRecursosOltMultiplataformaAction',
                                      $e->getMessage(),
                                      $strUsrSesion,
                                      $strIpClient
                                     );
        }

        $objResponse = new Response();
        $objResponse->headers->set('Content-type', 'text/json');
        $objResponse->setContent(json_encode($arrayResult));

        return $objResponse;
    }

    /**
     * @Secure(roles="ROLE_227-8038")
     *
     * Documentación para el método 'ajaxConfigurarOltMultiplataformaAction'.
     *
     * Función que realiza la configuración al olt multiplataforma
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 26-04-2021
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.1 11-03-2022 - Se realiza la validación de la solicitud del olt y se envia el usuario de la acción
     * 
     * @author Jenniffer Mujica <jmujica@telconet.ec>
     * @version 1.2 03-03-2023 - Se modifica el reverso de configuracion multiplataforma.
     *
     * @return Response $objResponse - Estado de la operación y el mensaje de resultado o error
     */
    public function ajaxConfigurarOltMultiplataformaAction()
    {
        ini_set('max_execution_time', 3000000);
        $objRequest   = $this->getRequest();
        $objSesion    = $objRequest->getSession();
        $serviceUtil  = $this->get('schema.Util');
        $strIpClient  = $objRequest->getClientIp();
        $strUsrSesion = $objSesion->get('user');
        $serviceElemento   = $this->get('tecnico.InfoElemento');
        $serviceNetworking = $this->get('tecnico.NetworkingScripts');
        $emComercial       = $this->getDoctrine()->getManager();
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        $strIdSolicitud    = $objRequest->get("strIdSolicitud");
        $strIdEmpresa      = $objSesion->get('idEmpresa');

        try
        {
            $emInfraestructura->getConnection()->beginTransaction();
            $emComercial->getConnection()->beginTransaction();

            //obtengo los datos de la solicitud
            $arrayDatosSolicitud = $this->getDatosOltMultiplataformaPorSolicitud(array('emComercial'       => $emComercial,
                                                                                       'emInfraestructura' => $emInfraestructura,
                                                                                       'strIdSolicitud'    => $strIdSolicitud));
            if($arrayDatosSolicitud['status'] == 'ERROR')
            {
                throw new \Exception($arrayDatosSolicitud['mensaje']);
            }
            $objDetalleSolicitud = $arrayDatosSolicitud['objDetalleSolicitud'];
            $objOltElemento      = $arrayDatosSolicitud['objOltElemento'];
            $objSolCaractPe      = $arrayDatosSolicitud['objSolCaractPe'];

            //validar solicitud
            if($objDetalleSolicitud->getEstado() == "Configurado" || $objDetalleSolicitud->getEstado() == "Activo")
            {
                throw new \Exception("La solicitud del olt ya se encuentra configurado para multiplataforma.");
            }
            if($objDetalleSolicitud->getEstado() != "Asignado")
            {
                throw new \Exception("La solicitud del olt no está asignado para realizar la configuración.");
            }

            //seteo el arreglo de parametros para asignar los recursos de red en el olt multiplataforma
            $arrayParametros = array(
                'url'        => 'setupPE',
                'accion'     => 'Activar',
                'pe'         => $objSolCaractPe->getValor(),
                'id_olt'     => $objOltElemento->getId(),
                'olt'        => $objOltElemento->getNombreElemento(),
                'servicio'   => 'GENERAL',
                'login_aux'  => '',
                'user_name'  => $strUsrSesion,
                'user_ip'    => $strIpClient
            );
            //se ejecuta script de networking
            $arrayRespuesta = $serviceNetworking->callNetworkingWebService($arrayParametros);
            if($arrayRespuesta['status'] == 'ERROR')
            {
                //reversar configuracion multiplataforma
                $arrayParametros = array(
                    'url'        => 'setupPE',
                    'accion'     => 'Cancelar',
                    'pe'         => $objSolCaractPe->getValor(),
                    'id_olt'     => $objOltElemento->getId(),
                    'olt'        => $objOltElemento->getNombreElemento(),
                    'servicio'   => 'GENERAL',
                    'login_aux'  => '',
                    'user_name'  => $strUsrSesion,
                    'user_ip'    => $strIpClient
                );
                //se ejecuta script de networking
                $serviceNetworking->callNetworkingWebService($arrayParametros);
                //retorno el error
                throw new \Exception($arrayRespuesta['mensaje']);
            }

            //actualizo el estado de la solicitud
            $objDetalleSolicitud->setEstado("Configurado");
            $emComercial->persist($objDetalleSolicitud);
            $emComercial->flush();

            //agregar historial a la solicitud
            $objDetalleSolHistorial = new InfoDetalleSolHist();
            $objDetalleSolHistorial->setDetalleSolicitudId($objDetalleSolicitud);
            $objDetalleSolHistorial->setIpCreacion($strIpClient);
            $objDetalleSolHistorial->setFeCreacion(new \DateTime('now'));
            $objDetalleSolHistorial->setUsrCreacion($strUsrSesion);
            $objDetalleSolHistorial->setEstado($objDetalleSolicitud->getEstado());
            $objDetalleSolHistorial->setObservacion("Se configuro el olt multiplataforma.");
            $emComercial->persist($objDetalleSolHistorial);
            $emComercial->flush();

            if($objOltElemento->getEstado() != 'Activo')
            {
                //actualizo el estado del elemento
                $objOltElemento->setEstado("Configurado");
                $emInfraestructura->persist($objOltElemento);
                $emInfraestructura->flush();
            }
            //agregar historial al elemento
            $objHistorialElemento = new InfoHistorialElemento();
            $objHistorialElemento->setElementoId($objOltElemento);
            $objHistorialElemento->setObservacion("Se configuro el olt multiplataforma.");
            $objHistorialElemento->setFeCreacion(new \DateTime('now'));
            $objHistorialElemento->setUsrCreacion($strUsrSesion);
            $objHistorialElemento->setIpCreacion($strIpClient);
            $objHistorialElemento->setEstadoElemento($objOltElemento->getEstado());
            $emInfraestructura->persist($objHistorialElemento);
            $emInfraestructura->flush();

            $emComercial->flush();
            $emComercial->getConnection()->commit();
            $emInfraestructura->flush();
            $emInfraestructura->getConnection()->commit();

            $strNombreOlt  = $objOltElemento->getNombreElemento();
            $arrayResult = array(
                'status'   => 'OK',
                'mensaje'  => "Se configuro el olt multiplataforma ($strNombreOlt) correctamente."
            );
        }
        catch (\Exception $e)
        {
            $arrayResult = array(
                'status'   => 'ERROR',
                'mensaje'  => $e->getMessage()
            );

            if ($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->getConnection()->rollback();
                $emComercial->getConnection()->close();
            }
            if ($emInfraestructura->getConnection()->isTransactionActive())
            {
                $emInfraestructura->getConnection()->rollback();
                $emInfraestructura->getConnection()->close();
            }

            $serviceUtil->insertError('Telcos+',
                                      'InfoElementoOltController.ajaxConfigurarOltMultiplataformaAction',
                                      $e->getMessage(),
                                      $strUsrSesion,
                                      $strIpClient
                                     );
        }

        $objResponse = new Response();
        $objResponse->headers->set('Content-type', 'text/json');
        $objResponse->setContent(json_encode($arrayResult));

        return $objResponse;
    }

    /**
     * @Secure(roles="ROLE_227-8019")
     *
     * Documentación para el método 'ajaxReversarOltMultiplataformaAction'.
     *
     * Función que reversa los recursos de red al olt multiplataforma
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 26-04-2021
     *
     * @return Response $objResponse - Estado de la operación y el mensaje de resultado o error
     */
    public function ajaxReversarOltMultiplataformaAction()
    {
        ini_set('max_execution_time', 3000000);
        $objRequest   = $this->getRequest();
        $objSesion    = $objRequest->getSession();
        $serviceUtil  = $this->get('schema.Util');
        $strIpClient  = $objRequest->getClientIp();
        $strUsrSesion = $objSesion->get('user');
        $serviceElemento = $this->get('tecnico.InfoElemento');
        $strIdSolicitud  = $objRequest->get("strIdSolicitud");
        $strIdEmpresa    = $objSesion->get('idEmpresa');
        $emComercial       = $this->getDoctrine()->getManager();
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');

        try
        {
            //obtengo los datos de la solicitud
            $arrayDatosSolicitud = $this->getDatosOltMultiplataformaPorSolicitud(array('emComercial'       => $emComercial,
                                                                                       'emInfraestructura' => $emInfraestructura,
                                                                                       'strIdSolicitud'    => $strIdSolicitud));
            if($arrayDatosSolicitud['status'] == 'ERROR')
            {
                throw new \Exception($arrayDatosSolicitud['mensaje']);
            }

            $arrayParametros = array(
                "strIdSolicitud" => $strIdSolicitud,
                "intIdOlt"       => $arrayDatosSolicitud['objOltElemento']->getId(),
                "intIdNodo"      => $arrayDatosSolicitud['objNodoElemento']->getId(),
                "objSolCaractPe" => $arrayDatosSolicitud['objSolCaractPe'],
                "objSolCaractIp" => $arrayDatosSolicitud['objSolCaractIp'],
                "strIdEmpresa"   => $strIdEmpresa,
                "strUsrSesion"   => $strUsrSesion,
                "strIpClient"    => $strIpClient,
            );
            //reversar solicitud multiplataforma
            $arrayResult = $serviceElemento->reversarSolicitudOltMultiplataformaTN($arrayParametros);
        }
        catch (\Exception $e)
        {
            $arrayResult = array(
                'status'   => 'ERROR',
                'mensaje'  => $e->getMessage()
            );
            if ($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->getConnection()->rollback();
                $emComercial->getConnection()->close();
            }
            if ($emInfraestructura->getConnection()->isTransactionActive())
            {
                $emInfraestructura->getConnection()->rollback();
                $emInfraestructura->getConnection()->close();
            }
            $serviceUtil->insertError('Telcos+',
                                      'InfoElementoOltController.ajaxReversarOltMultiplataformaAction',
                                      $e->getMessage(),
                                      $strUsrSesion,
                                      $strIpClient
                                     );
        }

        $objResponse = new Response();
        $objResponse->headers->set('Content-type', 'text/json');
        $objResponse->setContent(json_encode($arrayResult));

        return $objResponse;
    }

    /**
     * @Secure(roles="ROLE_227-8037")
     *
     * Documentación para el método 'ajaxGetPuertosPeMultiplataformaAction'.
     *
     * Función que obtiene los puertos del pe para la asignación al olt multiplataforma
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 26-04-2021
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.1 11-03-2022 - Se envia el usuario de la acción
     *
     * @return Response $objResponse - Estado de la operación y el mensaje de resultado o error
     */
    public function ajaxGetPuertosPeMultiplataformaAction()
    {
        $objRequest   = $this->getRequest();
        $objSesion    = $objRequest->getSession();
        $serviceUtil  = $this->get('schema.Util');
        $strIpClient  = $objRequest->getClientIp();
        $strUsrSesion = $objSesion->get('user');
        $serviceNetworking = $this->get('tecnico.NetworkingScripts');
        $strIdSolicitud    = $objRequest->get("strIdSolicitud");
        $emComercial       = $this->getDoctrine()->getManager();
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');

        try
        {
            //obtengo los datos de la solicitud
            $arrayDatosSolicitud = $this->getDatosOltMultiplataformaPorSolicitud(array('emComercial'       => $emComercial,
                                                                                       'emInfraestructura' => $emInfraestructura,
                                                                                       'strIdSolicitud'    => $strIdSolicitud));
            if($arrayDatosSolicitud['status'] == 'ERROR')
            {
                throw new \Exception($arrayDatosSolicitud['mensaje']);
            }
            $objOltElemento = $arrayDatosSolicitud['objOltElemento'];
            $objSolCaractPe = $arrayDatosSolicitud['objSolCaractPe'];
            //seteo el arreglo de parametros para asignar los recursos de red en el olt multiplataforma
            $arrayParametros = array(
                'url'        => 'getInterfacesPE',
                'accion'     => 'consultar',
                'pe'         => $objSolCaractPe->getValor(),
                'id_olt'     => $objOltElemento->getId(),
                'olt'        => $objOltElemento->getNombreElemento(),
                'servicio'   => 'GENERAL',
                'login_aux'  => '',
                'user_name'  => $strUsrSesion,
                'user_ip'    => $strIpClient
            );
            //se ejecuta script de networking
            $arrayRespuesta = $serviceNetworking->callNetworkingWebService($arrayParametros);
            if($arrayRespuesta['status'] == 'ERROR')
            {
                throw new \Exception($arrayRespuesta['mensaje']);
            }
            //seteo el arreglo de resultado
            $arrayResult       = array();
            //seteo el arreglo de interfaces
            $arrayInterfaces   = explode('@@', $arrayRespuesta['data']);
            foreach($arrayInterfaces as $strInterface)
            {
                $arrayResult[] = array(
                    'id'   => $strInterface,
                    'name' => $strInterface
                );
            }
            //se formula el json de respuesta
            $strJsonResultado = '{"total":"' . count($arrayResult) . '","registros":' . json_encode($arrayResult) . '}';
        }
        catch (\Exception $e)
        {
            $strJsonResultado = '{"total":"0", "registros":[], "error":"'.$e->getMessage().'"}';
            $serviceUtil->insertError('Telcos+',
                                      'InfoElementoOltController.ajaxGetPuertosPeMultiplataformaAction',
                                      $e->getMessage(),
                                      $strUsrSesion,
                                      $strIpClient
                                     );
        }

        $objResponse = new Response();
        $objResponse->headers->set('Content-type', 'text/json');
        $objResponse->setContent($strJsonResultado);

        return $objResponse;
    }

    /**
     * Documentación para el método 'getDatosOltMultiplataformaPorSolicitud'.
     *
     * Función que obtiene los datos del olt multiplataforma por solicitud
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 26-04-2021
     *
     * @return Array $arrayDatos [
     *          'status'              - estado de la operación
     *          'mensaje'             - mensaje de la operación
     *          'objDetalleSolicitud' - objeto de la solicitud
     *          'objOltElemento'      - objeto del olt
     *          'objNodoElemento'     - objeto del nodo
     *          'objSolCaractPe'      - objeto de la característica solicitud del pe
     *          'objSolCaractIp'      - objeto de la característica ipv6
     *      ]
     */
    public function getDatosOltMultiplataformaPorSolicitud($arrayParametros)
    {
        $objRequest   = $this->getRequest();
        $objSesion    = $objRequest->getSession();
        $serviceUtil  = $this->get('schema.Util');
        $strIpClient  = $objRequest->getClientIp();
        $strUsrSesion = $objSesion->get('user');
        $emComercial        = $arrayParametros['emComercial'];
        $emInfraestructura  = $arrayParametros['emInfraestructura'];
        $strIdSolicitud     = $arrayParametros['strIdSolicitud'];

        try
        {
            //obtengo la solicitud
            $objDetalleSolicitud = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->find($strIdSolicitud);
            if(!is_object($objDetalleSolicitud))
            {
                throw new \Exception("No se encontró la solicitud del Olt Multiplataforma, por favor notificar a Sistemas.");
            }
            //obtengo la característica del elemento id del olt
            $objCaractOlt = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                           ->findOneBy(array("descripcionCaracteristica" => "ELEMENTO_OLT_ID",
                                                             "estado"                    => "Activo"));
            if(!is_object($objCaractOlt))
            {
                throw new \Exception("No se encontró la característica del elemento id del olt, por favor notificar a Sistemas.");
            }
            //obtengo la característica del elemento id del nodo
            $objCaractNodo = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                           ->findOneBy(array("descripcionCaracteristica" => "ELEMENTO_NODO_ID",
                                                             "estado"                    => "Activo"));
            if(!is_object($objCaractNodo))
            {
                throw new \Exception("No se encontró la característica del elemento id del nodo, por favor notificar a Sistemas.");
            }
            //obtengo la característica el nombre del PE
            $objCaractPe = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                           ->findOneBy(array("descripcionCaracteristica" => "NOMBRE PE",
                                                             "estado"                    => "Activo"));
            if(!is_object($objCaractPe))
            {
                throw new \Exception("No se encontró la característica el nombre del PE, por favor notificar a Sistemas.");
            }
            //obtengo la característica del id de la ip
            $objCaractIp = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                           ->findOneBy(array("descripcionCaracteristica" => "IP WAN",
                                                             "estado"                    => "Activo"));
            if(!is_object($objCaractIp))
            {
                throw new \Exception("No se encontró la característica de la ip wan, por favor notificar a Sistemas.");
            }
            //obtengo la solicitud característica del elemento id del olt
            $objSolCaractOlt = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findOneBy(array('detalleSolicitudId' => $objDetalleSolicitud->getId(),
                                                                      'caracteristicaId'   => $objCaractOlt->getId(),
                                                                      'estado'             => 'Activo'));
            if(!is_object($objSolCaractOlt))
            {
                throw new \Exception("No se encontró la solicitud de la característica del elemento id del olt, por favor notificar a Sistemas.");
            }
            //obtengo el elemento olt
            $objOltElemento = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($objSolCaractOlt->getValor());
            if(!is_object($objOltElemento))
            {
                throw new \Exception("No se encontró el elemento Olt, por favor notificar a Sistemas.");
            }
            //obtengo la solicitud característica del elemento id del nodo
            $objSolCaractNodo = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findOneBy(array('detalleSolicitudId' => $objDetalleSolicitud->getId(),
                                                                      'caracteristicaId'   => $objCaractNodo->getId(),
                                                                      'estado'             => 'Activo'));
            if(!is_object($objSolCaractNodo))
            {
                throw new \Exception("No se encontró la solicitud de la característica del elemento id del nodo, por favor notificar a Sistemas.");
            }
            //obtengo el elemento nodo
            $objNodoElemento = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($objSolCaractNodo->getValor());
            if(!is_object($objNodoElemento))
            {
                throw new \Exception("No se encontró el elemento Nodo, por favor notificar a Sistemas.");
            }
            //obtengo la solicitud característica del nombre del PE
            $objSolCaractPe = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findOneBy(array('detalleSolicitudId' => $objDetalleSolicitud->getId(),
                                                                      'caracteristicaId'   => $objCaractPe->getId(),
                                                                      'estado'             => 'Activo'));
            if(!is_object($objSolCaractPe))
            {
                throw new \Exception("No se encontró la solicitud de la característica del nombre del PE, por favor notificar a Sistemas.");
            }
            //obtengo la solicitud característica de la ip
            $objSolCaractIp = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findOneBy(array('detalleSolicitudId' => $objDetalleSolicitud->getId(),
                                                                      'caracteristicaId'   => $objCaractIp->getId(),
                                                                      'estado'             => 'Activo'));
            if(!is_object($objSolCaractIp))
            {
                throw new \Exception("No se encontró la solicitud de la característica del nombre del PE, por favor notificar a Sistemas.");
            }

            $arrayDatos = array(
                'status'              => 'OK',
                'mensaje'             => 'OK',
                'objDetalleSolicitud' => $objDetalleSolicitud,
                'objOltElemento'      => $objOltElemento,
                'objNodoElemento'     => $objNodoElemento,
                'objSolCaractPe'      => $objSolCaractPe,
                'objSolCaractIp'      => $objSolCaractIp,
            );
        }
        catch (\Exception $e)
        {
            $arrayDatos = array(
                'status'              => 'ERROR',
                'mensaje'             => $e->getMessage(),
                'objDetalleSolicitud' => null,
                'objOltElemento'      => null,
                'objNodoElemento'     => null,
                'objSolCaractPe'      => null,
                'objSolCaractIp'      => null,
            );
            $serviceUtil->insertError('Telcos+',
                                      'InfoElementoOltController.getDatosOltMultiplataformaPorSolicitud',
                                      $e->getMessage(),
                                      $strUsrSesion,
                                      $strIpClient
                                     );
        }

        return $arrayDatos;
    }

    /**
     * Documentación para el método 'ajaxGetOltMultiplataformaAction'.
     *
     * Obtiene el listado de los olt multiplataforma
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 26-04-2021
     *
     * @return Response $objResponse - Lista de los olt multiplataforma
     */
    public function ajaxGetOltMultiplataformaAction()
    {
        ini_set('max_execution_time', 3000000);
        $objRequest   = $this->getRequest();
        $objSesion    = $objRequest->getSession();
        $strIpClient  = $objRequest->getClientIp();
        $strUsrSesion = $objSesion->get('user');
        $serviceUtil  = $this->get('schema.Util');
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        $strNombre    = $objRequest->get("query");
        $intLimit     = $objRequest->get("limit");

        try
        {
            //seteo variable de resultado
            $arrayResult     = array();
            $arrayParametros = [
                'strNombre'  => $strNombre,
                'intLimit'   => $intLimit,
            ];
            $arrayDatosElementos = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->getEleOltMultiplataforma($arrayParametros);
            if( $arrayDatosElementos['status'] == 'OK' )
            {
                $arrayResult = $arrayDatosElementos['result'];
            }
            else
            {
                throw new \Exception($arrayDatosElementos['result']);
            }
            //se formula el json de respuesta
            $strJsonResultado   = '{"total":"' . count($arrayResult) . '","data":' . json_encode($arrayResult) . '}';
        }
        catch (\Exception $e)
        {
            $strJsonResultado   = '{"total":"0", "data":[], "error":"' . $e->getMessage() . '"}';
            $serviceUtil->insertError('Telcos+',
                                      'InfoElementoOltController.ajaxGetOltMultiplataformaAction',
                                      $e->getMessage(),
                                      $strUsrSesion,
                                      $strIpClient
                                     );
        }

        $objResponse = new Response();
        $objResponse->headers->set('Content-type', 'text/json');
        $objResponse->setContent($strJsonResultado);

        return $objResponse;
    }
    
    /**
     * Documentación para el método 'ingresoMasivoScopeAction'.
     *
     * Función que sirve para crear los scopes y policys masivo cuyo método de ejecución se pasará a la base de datos
     *
     * @return Response
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.0 01-03-2021
     * 
     * @Secure(roles="ROLE_227-7977")
     * 
     */
    public function ingresoMasivoScopeAction()
    {
        ini_set('max_execution_time', 3000000);
        $emGeneral                  = $this->get('doctrine')->getManager('telconet_general');
        $strPathTelcos              = $this->container->getParameter('path_telcos');
        $arrayExtensionesPermitidas = array('csv','CSV');
        $objRequest                 = $this->getRequest();
        $objSession                 = $objRequest->getSession();
        $intIdEmpresa               = $objSession->get('idEmpresa');
        $strUsrCreacion             = $objSession->get('user');
        $emComunicacion             = $this->get('doctrine')->getManager('telconet_comunicacion');
        $objArchivo                 = $objRequest->files->get('archivoScope');
        $strMuestraErrorUsuario     = 'NO';
        $strUbicacionFisicaArchivo  = '';
        $strPermiteEliminarArchivo  = 'NO';
        $strCodEmpresa              = $objSession->get('idEmpresa');
                
        $objRespuesta  = new JsonResponse();
        $serviceUtil                = $this->get('schema.Util');
        
        $emComunicacion->getConnection()->beginTransaction();
        try
        {
            if(isset($objArchivo))
            {
                $strNombreArchivoOriginal       = $objArchivo->getClientOriginalName();
                $arrayPartesNombreArchivo       = explode('.', $strNombreArchivoOriginal);
                $strExtensionArchivo            = array_pop($arrayPartesNombreArchivo);
                $strNombreArchivo               = implode('_', $arrayPartesNombreArchivo);
                
                if (!in_array($strExtensionArchivo, $arrayExtensionesPermitidas))
                {
                    $objResultado = json_encode(array('success' => false, 'mensaje' => 'El archivo no tiene una extensión permitida'));
                    $objRespuesta->setContent($objResultado);
                    return $objRespuesta;
                }
                
                $strNombreArchivo           = str_replace(" ", "_", $strNombreArchivo);
                
                $strCadenaRandom            = substr(md5(uniqid(rand())),0,6);
                $strNuevoNombreArchivo      = $strNombreArchivo . "_" . date('Y-m-d') . "_". $strCadenaRandom;
                $strCaracteresAReemplazar   = "#ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ·";
                $strCaracteresReemplazo     = "_AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn-";
                $strNuevoNombreArchivo      = strtr($strNuevoNombreArchivo, $strCaracteresAReemplazar, $strCaracteresReemplazo);
                $strNuevoNombreArchivoYExt  = $strNuevoNombreArchivo . "." . $strExtensionArchivo;
                
                $objDocumentoArchivoCsv = new InfoDocumento();
                $objDocumentoArchivoCsv->setNombreDocumento('Archivo csv subido por creación masiva de policy y scopes');
                $objDocumentoArchivoCsv->setMensaje('Documento que se sube para realizar la creación masiva de policy y scopes');
                
                $objDocumentoArchivoCsv->setUbicacionLogicaDocumento($strNuevoNombreArchivoYExt);
                $objDocumentoArchivoCsv->setEstado('Activo');
                $objDocumentoArchivoCsv->setFeCreacion(new \DateTime('now'));
                $objDocumentoArchivoCsv->setFechaDocumento(new \DateTime('now'));
                $objDocumentoArchivoCsv->setIpCreacion('127.0.0.1');
                $objDocumentoArchivoCsv->setUsrCreacion($strUsrCreacion);
                $objDocumentoArchivoCsv->setEmpresaCod($intIdEmpresa);

                $objTipoDocumentoArchivoCsv = $emComunicacion->getRepository('schemaBundle:AdmiTipoDocumento')
                                                             ->findOneByExtensionTipoDocumento(array(   'extensionTipoDocumento'   => 
                                                                                                        strtoupper($strExtensionArchivo)));
                if(is_object($objTipoDocumentoArchivoCsv))
                {
                    $objDocumentoArchivoCsv->setTipoDocumentoId($objTipoDocumentoArchivoCsv);
                }
                else
                {
                    $objTipoDocumentoArchivoCsv = new AdmiTipoDocumento();
                    $objTipoDocumentoArchivoCsv->setExtensionTipoDocumento(strtoupper($strExtensionArchivo));
                    $objTipoDocumentoArchivoCsv->setTipoMime(strtoupper($strExtensionArchivo));
                    $objTipoDocumentoArchivoCsv->setDescripcionTipoDocumento('ARCHIVO FORMATO '.$strExtensionArchivo);
                    $objTipoDocumentoArchivoCsv->setEstado('Activo');
                    $objTipoDocumentoArchivoCsv->setUsrCreacion($strUsrCreacion);
                    $objTipoDocumentoArchivoCsv->setFeCreacion(new \DateTime('now'));
                    $emComunicacion->persist($objTipoDocumentoArchivoCsv);
                    $emComunicacion->flush();
                    $emComunicacion->setTipoDocumentoId($objTipoDocumentoArchivoCsv);
                }

                $strPermiteEliminarArchivo = 'SI';

                $strNombreApp       = 'TelcosWeb';
                $arrayPathAdicional = [];
                $strSubModulo       = "SolicitudesMasivas";
                
                $strFileBase64      = base64_encode(file_get_contents($objArchivo->getPathName()));
                $arrayParamNfs      = array(
                                          'prefijoEmpresa'       => 'MD',
                                          'strApp'               => $strNombreApp,
                                          'strSubModulo'         => $strSubModulo,
                                          'arrayPathAdicional'   => $arrayPathAdicional,
                                          'strBase64'            => $strFileBase64,
                                          'strNombreArchivo'     => $strNuevoNombreArchivoYExt,
                                          'strUsrCreacion'       => $strUsrCreacion);
                $arrayRespNfs       = $serviceUtil->guardarArchivosNfs($arrayParamNfs);
                
                $intStatusSubidaCsvNfs         = $arrayRespNfs["intStatus"];
                $strMensajeSubidaCsvNfs        = $arrayRespNfs["strMensaje"];
                
                if(isset($arrayRespNfs) && $intStatusSubidaCsvNfs === 200)
                {   
                    $objDocumentoArchivoCsv->setUbicacionFisicaDocumento($arrayRespNfs["strUrlArchivo"]);
                    $emComunicacion->persist($objDocumentoArchivoCsv);
                    $emComunicacion->flush();
                    $intIdDocumentoCsvSubido = $objDocumentoArchivoCsv->getId();
                    $emComunicacion->commit();

                    $arrayParamsSubidaCsv   = array(
                                                "intIdArchivoCsvCpm"        => $intIdDocumentoCsvSubido,
                                                "strUsrCreacion"            => $strUsrCreacion
                                                );
                    $arrayRespuestaSubidaCsv    = $this->ejecutaSubidaCsvPsm($arrayParamsSubidaCsv);

                    $strStatusSubidaCsv         = $arrayRespuestaSubidaCsv["status"];
                    $strMensajeSubidaCsv        = $arrayRespuestaSubidaCsv["mensaje"];

                    if($strStatusSubidaCsv !== "OK")
                    {
                        $strMuestraErrorUsuario = 'SI';
                        throw new \Exception($strMensajeSubidaCsv);
                    }

                    
                    $strMensajeOk = 'Archivo Cargado correctamente, verificar notificación enviada vía correo electrónico donde se encuentra el 
                                     detalle de los registros procesados ';
                    $objResultado = json_encode(array('success' => true, 'mensaje' => $strMensajeOk));
                    $objRespuesta->setContent($objResultado);
                    
                    return $objRespuesta;
                }
                else
                {
                    $strMuestraErrorUsuario = 'SI';
                    throw new \Exception($strMensajeSubidaCsvNfs);
                }
            }
            else
            {
                $strMuestraErrorUsuario = 'SI';
                throw new \Exception('No se ha podido procesar correctamente el archivo');
            }
        }
        catch(\Exception $e)
        {
            if($strMuestraErrorUsuario === "SI")
            {
                $strMensaje = $e->getMessage();
            }
            else
            {
                $strMensaje = "Ha ocurrido un problema inesperado. Por favor comuníquese con Sistemas";
            }
            error_log("Error al subir archivo para generación de scopes y policys ".$e->getMessage());
            if($emComunicacion->getConnection()->isTransactionActive())
            {
                $emComunicacion->getConnection()->rollback();
            }
            $emComunicacion->getConnection()->close();
            $objResultado = json_encode(array('success' => false, 'mensaje' => $strMensaje));
            $objRespuesta->setContent($objResultado);
            return $objRespuesta;
        }
    }
    
    /**
     * Documentación para el método 'reporteMigracionOltAction'.
     *
     * Función que sirve para enviar a ejecutar los reportes previos a la migracion olt alta densidad
     *
     * @return Response
     *
     * @author Jonathan Burgos <jsburgos@telconet.ec>
     * @version 1.0 15-02-2023
     * @since 1.0
     * 
     * @return $objRespuesta
     */
    public function reporteMigracionOltAction()
    {
        $emInfraestructura          = $this->get('doctrine')->getManager('telconet_infraestructura');
        $objRequest                 = $this->getRequest();
        $objSession                 = $objRequest->getSession();
        $strUsrCreacion             = $objSession->get('user');
        $strMuestraErrorUsuario     = 'NO';
        $strMensaje                 = '';
        $objParametrosMigrar        = json_decode($objRequest->get('jsonListaOltMigrar'));
        $objRespuesta  = new JsonResponse();

        try
        {
            $emInfraestructura->getConnection()->beginTransaction();
            $objInfoMigraAdCab = new InfoMigraAdCab();
            $objInfoMigraAdCab->setNombre("REPORTES_PREV_MIGRACION_".date('Y-m-d'));
            $objInfoMigraAdCab->setTipo("ReporteMigracion");
            $objInfoMigraAdCab->setEstado("Pendiente");
            $objInfoMigraAdCab->setUsrCreacion($strUsrCreacion);
            $objInfoMigraAdCab->setFeCreacion(new \DateTime('now'));
            $objInfoMigraAdCab->setIpCreacion("127.0.0.1");
            $emInfraestructura->persist($objInfoMigraAdCab);
            $emInfraestructura->flush();
            foreach($objParametrosMigrar->arrayData as $objParametros)
            {
                $objInfoMigraAdData = new InfoMigraAdData();
                $objInfoMigraAdData->setMigracionCabId($objInfoMigraAdCab->getId());
                $objInfoMigraAdData->setTipoProceso("ReportePrevio");
                $objInfoMigraAdData->setIdentificador($objParametros->idElemento);
                $objInfoMigraAdData->setInformacion($objParametros->nombreElemento);
                $objInfoMigraAdData->setEstado("Pendiente");
                $objInfoMigraAdData->setObservacion("Olt seleccionados para generar reporte pre migracion olt alta densidad");
                $objInfoMigraAdData->setUsrCreacion($strUsrCreacion);
                $objInfoMigraAdData->setFeCreacion(new \DateTime('now'));
                $emInfraestructura->persist($objInfoMigraAdData);
                $emInfraestructura->flush();
            }
            $emInfraestructura->getConnection()->commit();
            $arrayRespuestaReporteCsv    = $this->ejecutarReporteMigracionOlt($strUsrCreacion);
            $strMensajeReporteCsv        = $arrayRespuestaReporteCsv["mensaje"];
            if( $arrayRespuestaReporteCsv["status"] !== "OK" )
            {
                $strMuestraErrorUsuario = "SI";
                throw new \Exception($strMensajeReporteCsv);
            }
            $objResultado = json_encode(array('success' => true, 'mensaje' => 'Reporte generado y enviado exitosamente.'));
            $objRespuesta->setContent($objResultado);
            return $objRespuesta;
        }
        catch(\Exception $e)
        {
            $strMensaje = "Ha ocurrido un problema inesperado. Por favor comuníquese con Sistemas :".$strMensaje = $e->getMessage();
            if($strMuestraErrorUsuario === "SI")
            {
                $strMensaje = $e->getMessage();
            }
            if($emInfraestructura->getConnection()->isTransactionActive())
            {
                $emInfraestructura->getConnection()->rollback();
            }
            $emInfraestructura->getConnection()->close();
            $objResultado = json_encode(array('success' => false, 'mensaje' => $strMensaje));
            $objRespuesta->setContent($objResultado);
            return $objRespuesta;
        }
    }

    /**
     * Documentación para el método 'ejecutarMigracionOltAction'.
     *
     * Función que sirve para cargar los archivos de migracion al servidor nfs, validar la informacion cargada
     * e insertar los registros de los archivos en las tablas de migracion olt alta densidad.
     *
     * @author Jonathan Burgos <jsburgos@telconet.ec>
     * @version 1.0 15-02-2023
     * @since 1.0
     * 
     * @return $objRespuesta
     */
    public function ejecutarMigracionOltAction()
    {

        ini_set('max_execution_time', 3000000);
        $emComunicacion             = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emInfraestructura          = $this->get('doctrine')->getManager('telconet_infraestructura');
        $strPathTelcos              = $this->container->getParameter('path_telcos');
        $arrayExtensionesPermitidas = array('csv','CSV');
        $objRequest                 = $this->getRequest();
        $objSession                 = $objRequest->getSession();
        $intIdEmpresa               = $objSession->get('idEmpresa');
        $strUsrCreacion             = $objSession->get('user');
        $objArchivoOLT              = $objRequest->files->get("formatoOLT");
        $objArchivoSPLITTER         = $objRequest->files->get("formatoSPLITTER");
        $objArchivoENLACE           = $objRequest->files->get("formatoENLACES");
        $objArchivoSCOPE            = $objRequest->files->get("formatoSCOPES");
        $strMuestraErrorUsuario     = 'NO';
        $strUbicacionFisicaArchivo  = '';
        $strPermiteEliminarArchivo  = 'SI';
        $strCodEmpresa              = $objSession->get('idEmpresa');        
        $objRespuesta  = new JsonResponse();
        $serviceUtil                = $this->get('schema.Util');
        
        try
        {
            $emComunicacion->getConnection()->beginTransaction();
            $emInfraestructura->getConnection()->beginTransaction();

            if(empty($objArchivoOLT) || empty($objArchivoSPLITTER) || empty($objArchivoENLACE) || empty($objArchivoSCOPE))
            {
                $strMuestraErrorUsuario = "SI";
                throw new \Exception('No se han seleccionado todos los archivos necesarios para la migracion OLT.');
            }
            
            $arrayObjetoArchivos = [];
            $arrayIdDocumentos   = [];
            array_push($arrayObjetoArchivos, array('nombre' => 'OLT', 'archivo' => $objArchivoOLT));
            array_push($arrayObjetoArchivos, array('nombre' => 'SPLITTER', 'archivo' => $objArchivoSPLITTER));
            array_push($arrayObjetoArchivos, array('nombre' => 'ENLACE', 'archivo' => $objArchivoENLACE));
            array_push($arrayObjetoArchivos, array('nombre' => 'SCOPE', 'archivo' => $objArchivoSCOPE));

            foreach( $arrayObjetoArchivos as $arrayObjetoArchivo )
            {
                $objArchivo                     = $arrayObjetoArchivo["archivo"];
                
                $strNombreArchivoOriginal       = $objArchivo->getClientOriginalName();
                $arrayPartesNombreArchivo       = explode('.', $strNombreArchivoOriginal);
                $strExtensionArchivo            = array_pop($arrayPartesNombreArchivo);
                $strNombreArchivo               = implode('_', $arrayPartesNombreArchivo);
                
                if (!in_array($strExtensionArchivo, $arrayExtensionesPermitidas))
                {
                    $strMuestraErrorUsuario = "SI";
                    throw new \Exception('El archivo no tiene una extensión permitida');
                }
                
                $strNombreArchivo           = str_replace(" ", "_", $strNombreArchivo);
                
                $strCadenaRandom            = substr(md5(uniqid(rand())),0,6);
                $strNuevoNombreArchivo      = $strNombreArchivo . "_" . date('Y-m-d') . "_". $strCadenaRandom;
                $strCaracteresAReemplazar   = "#ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ·";
                $strCaracteresReemplazo     = "_AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn-";
                $strNuevoNombreArchivo      = strtr($strNuevoNombreArchivo, $strCaracteresAReemplazar, $strCaracteresReemplazo);
                $strNuevoNombreArchivoYExt  = $strNuevoNombreArchivo . "." . $strExtensionArchivo;
                
                $objDocumentoArchivoCsv = new InfoDocumento();
                $objDocumentoArchivoCsv->setNombreDocumento($strNombreArchivoOriginal);
                $objDocumentoArchivoCsv->setMensaje('Documento '.$strNombreArchivo.' que se sube para realizar la migracion de olts alta densidad');
                
                $objDocumentoArchivoCsv->setUbicacionLogicaDocumento($strNuevoNombreArchivoYExt);
                $objDocumentoArchivoCsv->setEstado('Activo');
                $objDocumentoArchivoCsv->setFeCreacion(new \DateTime('now'));
                $objDocumentoArchivoCsv->setFechaDocumento(new \DateTime('now'));
                $objDocumentoArchivoCsv->setIpCreacion('127.0.0.1');
                $objDocumentoArchivoCsv->setUsrCreacion($strUsrCreacion);
                $objDocumentoArchivoCsv->setEmpresaCod($intIdEmpresa);

                $objTipoDocumentoArchivoCsv = $emComunicacion->getRepository('schemaBundle:AdmiTipoDocumento')
                                                                ->findOneByExtensionTipoDocumento(array(   'extensionTipoDocumento'   => 
                                                                                                        strtoupper($strExtensionArchivo)));
                if(is_object($objTipoDocumentoArchivoCsv))
                {
                    $objDocumentoArchivoCsv->setTipoDocumentoId($objTipoDocumentoArchivoCsv);
                }
                else
                {
                    $objTipoDocumentoArchivoCsv = new AdmiTipoDocumento();
                    $objTipoDocumentoArchivoCsv->setExtensionTipoDocumento(strtoupper($strExtensionArchivo));
                    $objTipoDocumentoArchivoCsv->setTipoMime(strtoupper($strExtensionArchivo));
                    $objTipoDocumentoArchivoCsv->setDescripcionTipoDocumento('ARCHIVO FORMATO '.$strExtensionArchivo);
                    $objTipoDocumentoArchivoCsv->setEstado('Activo');
                    $objTipoDocumentoArchivoCsv->setUsrCreacion($strUsrCreacion);
                    $objTipoDocumentoArchivoCsv->setFeCreacion(new \DateTime('now'));
                    $emComunicacion->persist($objTipoDocumentoArchivoCsv);
                    $emComunicacion->flush();
                    $emComunicacion->setTipoDocumentoId($objTipoDocumentoArchivoCsv);
                }

                $strPermiteEliminarArchivo = 'SI';

                $strNombreApp       = 'TelcosWeb';
                $arrayPathAdicional = [];
                $strSubModulo       = "MigracionOltAltaDensidad";
                $strFileBase64      = base64_encode(file_get_contents($objArchivo->getPathName()));
                $arrayParamNfs      = array(
                                            'prefijoEmpresa'       => 'MD',
                                            'strApp'               => $strNombreApp,
                                            'strSubModulo'         => $strSubModulo,
                                            'arrayPathAdicional'   => $arrayPathAdicional,
                                            'strBase64'            => $strFileBase64,
                                            'strNombreArchivo'     => $strNuevoNombreArchivoYExt,
                                            'strUsrCreacion'       => $strUsrCreacion);
                $arrayRespNfs       = $serviceUtil->guardarArchivosNfs($arrayParamNfs);
                $intStatusSubidaCsvNfs         = $arrayRespNfs["intStatus"];
                $strMensajeSubidaCsvNfs        = $arrayRespNfs["strMensaje"];
                
                if(isset($arrayRespNfs) && $intStatusSubidaCsvNfs === 200)
                {   
                    $objDocumentoArchivoCsv->setUbicacionFisicaDocumento($arrayRespNfs["strUrlArchivo"]);
                    $emComunicacion->persist($objDocumentoArchivoCsv);
                    $emComunicacion->flush();
                    $intIdDocumentoCsvSubido = $objDocumentoArchivoCsv->getId();
                    array_push($arrayIdDocumentos, array('nombre' => $arrayObjetoArchivo["nombre"], 'idArchivo' => $intIdDocumentoCsvSubido));
                }
                else
                {
                    $strMuestraErrorUsuario = "SI";
                    throw new \Exception($strMensajeSubidaCsvNfs);
                }

            }
            if(empty($arrayIdDocumentos))
            {
                $strMuestraErrorUsuario = "SI";
                throw new \Exception("No se han cargado los archivos en el servidor NFS.");   
            }
            $emComunicacion->getConnection()->commit();
            $emComunicacion->getConnection()->close();
            $objInfoMigraAdCab = new InfoMigraAdCab();
            $objInfoMigraAdCab->setNombre("MIGRACION_OLT_".date('Y-m-d'));
            $objInfoMigraAdCab->setTipo("ArchivosMigracion");
            $objInfoMigraAdCab->setEstado("Pendiente");
            $objInfoMigraAdCab->setUsrCreacion($strUsrCreacion);
            $objInfoMigraAdCab->setFeCreacion(new \DateTime('now'));
            $objInfoMigraAdCab->setIpCreacion("127.0.0.1");
            $emInfraestructura->persist($objInfoMigraAdCab);
            $emInfraestructura->flush();
            foreach ( $arrayIdDocumentos as $arrayIdDocumento ) 
            {
                $intIdDocumento           = $arrayIdDocumento["idArchivo"];
                $strTipoElementoNombre    = $arrayIdDocumento["nombre"];
                $objInfoMigraAdData = new InfoMigraAdData();
                $objInfoMigraAdData->setMigracionCabId($objInfoMigraAdCab->getId());
                $objInfoMigraAdData->setTipoProceso("Archivo");
                $objInfoMigraAdData->setIdentificador($intIdDocumento);
                $objInfoMigraAdData->setInformacion($strTipoElementoNombre);
                $objInfoMigraAdData->setEstado("Cargado");
                $objInfoMigraAdData->setObservacion("Archivo cargado en el servidor NFS para realizar la migracion olt alta densidad");
                $objInfoMigraAdData->setUsrCreacion($strUsrCreacion);
                $objInfoMigraAdData->setFeCreacion(new \DateTime('now'));
                $emInfraestructura->persist($objInfoMigraAdData);
                $emInfraestructura->flush();
                
            }
            $emInfraestructura->getConnection()->commit();
            $emInfraestructura->getConnection()->close();
            $arrayRespuesta              = $this->migrarOltAltaDensidad($strUsrCreacion);
            $strMensajeRespuesta         = $arrayRespuesta["mensaje"];
            if( $arrayRespuesta["status"] !== "OK" )
            {
                $strMuestraErrorUsuario = "SI";
                throw new \Exception($strMensajeRespuesta);
            }
            $objResultado = json_encode(array('success' => true, 'mensaje' => $strMensajeRespuesta));
            $objRespuesta->setContent($objResultado);
            return $objRespuesta;
        }
        catch(\Exception $e)
        {
            if($strMuestraErrorUsuario === "SI")
            {
                $strMensaje = $e->getMessage();
            }
            else
            {
                $strMensaje = "Ha ocurrido un problema inesperado. Por favor comuníquese con Sistemas ".$e->getMessage();
            }            
            if($emComunicacion->getConnection()->isTransactionActive())
            {
                $emComunicacion->getConnection()->rollback();
            }
            $emComunicacion->getConnection()->close();

            if($emInfraestructura->getConnection()->isTransactionActive())
            {
                $emInfraestructura->getConnection()->rollback();
            }
            $emInfraestructura->getConnection()->close();
            $objResultado = json_encode(array('success' => false, 'mensaje' => $strMensaje));
            $objRespuesta->setContent($objResultado);
            return $objRespuesta;
        }
    }

    /**
     * Documentación para el método 'migrarOltAltaDensidad'.
     *
     * Función que sirve para ejecutar el proceso de validacion de archivos de migracion
     * e insertar los registros de los archivos en las tablas de migracion olt alta densidad.
     *
     * @author Jonathan Burgos <jsburgos@telconet.ec>
     * @version 1.0 15-02-2023
     * @since 1.0
     * 
     * @return $arrayRespuesta
     */
    public function migrarOltAltaDensidad($strUsuarioConsulta)
    {
        $strUsrCreacion             = $strUsuarioConsulta;
        $strDatabaseDsn             = $this->container->getParameter('database_dsn');
        $strUserInfraestructura     = $this->container->getParameter('user_infraestructura');
        $strPasswordInfraestructura = $this->container->getParameter('passwd_infraestructura');
        $strMuestraErrorUsuario     = 'NO';
        $strStatus                  = '';
        $strMensaje                 = '';

        try
        {
            $strSql         = " BEGIN DB_INFRAESTRUCTURA.INKG_MIGRACION_ALTA_DENSIDAD.P_UPLOAD_CSV_MIGRACION_OLT(
                                    :Pv_UsrCreacion,
                                    :Pv_Status,
                                    :Pv_Mensaje); 
                                END;";
            $objConn = oci_connect($strUserInfraestructura, $strPasswordInfraestructura, $strDatabaseDsn);
            $objStmt = oci_parse($objConn, $strSql);
            
            oci_bind_by_name($objStmt, ':Pv_UsrCreacion', $strUsrCreacion);
            oci_bind_by_name($objStmt, ':Pv_Status', $strStatus, 25);
            oci_bind_by_name($objStmt, ':Pv_Mensaje', $strMensaje, 2000);
            oci_execute($objStmt);
            
            if ( $strStatus !=  "OK" )
            {
                $strMuestraErrorUsuario = 'SI';
                throw new \Exception ($strMensaje);
            }            
            $arrayRespuesta = array("status"    => $strStatus,
                                    "mensaje"   => 'Se ha enviado a ejecutar el proceso de migracion olt alta densidad.');
            return $arrayRespuesta;

        }
        catch (\Exception $e)
        {
            if($strMuestraErrorUsuario === "SI")
            {
                $strMensaje = $e->getMessage();
            }
            else
            {
                $strMensaje = "Ha ocurrido un problema al intentar consultar el estado de migracion olt alta densidad: ".$e->getMessage();
            }
            $arrayRespuesta = array("status"    => 'ERROR',
                                    "mensaje"   => $strMensaje);
            return $arrayRespuesta;
        }
    }

    /**
     * Documentación para el método 'ejecutarReporteMigracionOlt'.
     *
     * Función que sirve para crear los reportes previos a la migracion olt alta densidad .
     *
     * @author Jonathan Burgos <jsburgos@telconet.ec>
     * @version 1.0 15-02-2023
     * @since 1.0
     * 
     * @return $arrayRespuesta
     */
    public function ejecutarReporteMigracionOlt($strUsuarioConsulta)
    {
        $strUsrCreacion             = $strUsuarioConsulta;
        $strDatabaseDsn             = $this->container->getParameter('database_dsn');
        $strUserInfraestructura     = $this->container->getParameter('user_infraestructura');
        $strPasswordInfraestructura = $this->container->getParameter('passwd_infraestructura');
        $strMuestraErrorUsuario     = 'NO';
        $strStatus                  = '';
        $strMensaje                 = '';

        try
        {
            $strSql         = " BEGIN DB_INFRAESTRUCTURA.INKG_MIGRACION_ALTA_DENSIDAD.P_ENVIA_REPORTE_PREV_MIGRACION(
                                    :Pv_UsrConsulta,
                                    :Pv_Status,
                                    :Pv_Mensaje); 
                                END;";
            $objConn = oci_connect($strUserInfraestructura, $strPasswordInfraestructura, $strDatabaseDsn);
            $objStmt = oci_parse($objConn, $strSql);
            
            oci_bind_by_name($objStmt, ':Pv_UsrConsulta', $strUsrCreacion);
            oci_bind_by_name($objStmt, ':Pv_Status', $strStatus, 25);
            oci_bind_by_name($objStmt, ':Pv_Mensaje', $strMensaje, 2000);
            oci_execute($objStmt);

            if ( $strStatus !=  "OK" )
            {
                $strMuestraErrorUsuario = 'SI';
                throw new \Exception ($strMensaje);
            }            

        }
        catch (\Exception $e)
        {
            if($strMuestraErrorUsuario === "SI")
            {
                $strMensaje = $e->getMessage();
            }
            else
            {
                $strStatus = 'ERROR';
                $strMensaje = "Ha ocurrido un problema al intentar ejecutar el reporte previo a la migración: ".$e->getMessage();
            }
        }
        $arrayRespuesta = array("status"    => $strStatus,
                                "mensaje"   => $strMensaje);
        return $arrayRespuesta;
    }
    
    /**
     * Documentación para el método 'validarMigracionOltAction'.
     *
     * Función que sirve para validar si existe una migracion en ejecucion.
     *
     * @author Jonathan Burgos <jsburgos@telconet.ec>
     * @version 1.0 15-02-2023
     * @since 1.0
     * 
     * @return $objRespuesta
     */
    public function validarMigracionOltAction()
    {

        $strUsrCreacion             = $strUsuarioConsulta;
        $strDatabaseDsn             = $this->container->getParameter('database_dsn');
        $strUserInfraestructura     = $this->container->getParameter('user_infraestructura');
        $strPasswordInfraestructura = $this->container->getParameter('passwd_infraestructura');
        $strMuestraErrorUsuario     = 'NO';
        $strStatus                  = '';
        $strMensaje                 = '';
        $objRespuesta  = new JsonResponse();

        try
        {
            $strSql         = " BEGIN DB_INFRAESTRUCTURA.INKG_MIGRACION_ALTA_DENSIDAD.P_VALIDA_ESTADO_MIGRACION(
                                    :PV_STATUS,
                                    :PV_MENSAJE); 
                                END;";
            $objConn = oci_connect($strUserInfraestructura, $strPasswordInfraestructura, $strDatabaseDsn);
            $objStmt = oci_parse($objConn, $strSql);
            
            oci_bind_by_name($objStmt, ':PV_STATUS', $strStatus, 25);
            oci_bind_by_name($objStmt, ':PV_MENSAJE', $strMensaje, 2000);
            oci_execute($objStmt);

            if ( $strStatus !=  "OK" )
            {
                $strMuestraErrorUsuario = 'SI';
                throw new \Exception ($strMensaje);
            }            

            $objResultado = json_encode(array('success' => true, 'mensaje' => 'No hay migraciones pendiente o en ejecucion.'));
            $objRespuesta->setContent($objResultado);
            return $objRespuesta;

        }
        catch (\Exception $e)
        {
            if($strMuestraErrorUsuario === "SI")
            {
                $strMensaje = $e->getMessage();
            }
            else
            {
                $strMensaje = "Ha ocurrido un problema al intentar consultar el estado de migracion olt alta densidad: ".$e->getMessage();
            }
            $objResultado = json_encode(array('success' => false, 'mensaje' => $strMensaje));
            $objRespuesta->setContent($objResultado);
            return $objRespuesta;
        }
    }

    /**
     * Función que sirve para ejecutar el procedimiento de Base de Datos que ejecuta la validación del archivo csv y creación de scopes 
     * y policys masivos
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.0 05-03-2021
     * 
     * @author Alberto Arias <farias@telconet.ec>
     * @version 1.1 05-10-2022
     * Se modifica el metodo para procesar el archivo que se encuentra guardado en NFS
     */
    public function ejecutaSubidaCsvPsm($arrayParametros)
    {
        $strDatabaseDsn             = $this->container->getParameter('database_dsn');
        $strUserInfraestructura     = $this->container->getParameter('user_infraestructura');
        $strPasswordInfraestructura = $this->container->getParameter('passwd_infraestructura');
        $intIdArchivoCsvCpm         = $arrayParametros["intIdArchivoCsvCpm"];
        $strUsrCreacion             = $arrayParametros["strUsrCreacion"];
        $strMuestraErrorUsuario     = 'NO';
        $strStatus                  = '';
        $strMensaje                 = '';
      
        try
        {
            if(!isset($strUsrCreacion) || empty($strUsrCreacion))
            {
                $strMuestraErrorUsuario = 'SI';
                throw new \Exception ('No se ha podido obtener el usuario en sesión');
            }
            
            $strSql         = " BEGIN DB_INFRAESTRUCTURA.INFRK_TRANSACCIONES.P_UPLOAD_CSV_POLICY_SCOPE(
                                    :Pn_IdArchivoCsvCpm,
                                    :Pv_UsrCreacion,
                                    :Pv_Status,
                                    :Pv_Mensaje); 
                                END;";
            $objConn = oci_connect($strUserInfraestructura, $strPasswordInfraestructura, $strDatabaseDsn);
            $objStmt = oci_parse($objConn, $strSql);
            
            oci_bind_by_name($objStmt, ':Pn_IdArchivoCsvCpm', $intIdArchivoCsvCpm);
            oci_bind_by_name($objStmt, ':Pv_UsrCreacion', $strUsrCreacion);
            oci_bind_by_name($objStmt, ':Pv_Status', $strStatus, 5);
            oci_bind_by_name($objStmt, ':Pv_Mensaje', $strMensaje, 2000);
            oci_execute($objStmt);
        }
        catch (\Exception $e)
        {
            if($strMuestraErrorUsuario === "SI")
            {
                $strMensaje = $e->getMessage();
            }
            else
            {
                $strMensaje = "Ha ocurrido un problema al intentar ejecutar la creación de scopes/policy. Por favor comuníquese con Sistemas!";
            }
            $strStatus  = "ERROR";
        }
        $arrayRespuesta = array("status"    => $strStatus,
                                "mensaje"   => $strMensaje);
        return $arrayRespuesta;
    }
    
     /**
     * @Secure(roles="ROLE_227-8377")
     *
     * Documentación para el método 'ajaxBuscarMacDesconfigurarIp'.
     *
     * Obtiene el listado de todas las IP asociadas a una MAC
     *
     * @author Alberto Arias <farias@telconet.ec>
     * @version 1.0 08-09-2021
     * 
     * @author Alberto Arias <farias@telconet.ec>
     * @version 1.1 06-10-2021
     *
     * @return Response $objResponse - Lista de todas las IP asociadas a una MAC
     */
    public function ajaxBuscarMacDesconfigurarIpAction()
    {
        ini_set('max_execution_time', 3000000);
        $objRequest                     = $this->getRequest();
        $objSesion                      = $objRequest->getSession();
        $strIpClient                    = $objRequest->getClientIp();
        $strUsrSesion                   = $objSesion->get('user');
        $serviceUtil                    = $this->get('schema.Util');
        $emComercial                    = $this->getDoctrine()->getManager('telconet');
        $strMac                         = $objRequest->get("strMac");
        $objManager                     = $this->getDoctrine()->getManager('telconet');

        try
        {
            $arrayParametros = [
              'nombreParametro'     => 'ESTADO_DESCONFIGURACION_IP',
              'modulo'              => 'TECNICO',
              'strLlave'            => 'valor1'
            ];
            // obtener los estados permitidos en el SERVICIO del cliente
            $arrayEstadosServicios  = $objManager->getRepository('schemaBundle:AdmiParametroDet')
            ->getArrayParametrosDetalle($arrayParametros);

            // Obtener los tipos de MAC 
            $arrayParametros['nombreParametro'] = 'TIPO_MAC_DESCONFIGURAR_IP';           
            $arrayTipoMacPermitidos =  $objManager->getRepository('schemaBundle:AdmiParametroDet')
            ->getArrayParametrosDetalle($arrayParametros);

            $arrayParametros = [
                'strMac'    => $strMac,
                'estados'   => $arrayEstadosServicios,
                'tipo_mac'  => $arrayTipoMacPermitidos
            ];
            $arrayDatosIpMac = $emComercial->getRepository('schemaBundle:InfoElemento')->getMacDesconfigurarIp($arrayParametros);
            
            if( $arrayDatosIpMac['status'] == 'OK' )
            {
                $arrayData    = $arrayDatosIpMac['result'];
                $arrayResult  = array();
                $boolPermiso = false;
                if (true === $this->get('security.context')->isGranted('ROLE_227-8378'))
                {
                    $boolPermiso = true; //permiso para procesar la desconfiguración de IP
                }
                foreach($arrayData as $objDato)
                {
                  $objDato["estadosServicios"]  = $arrayEstadosServicios;
                  $objDato["permiso"]           = $boolPermiso;
                  $arrayResult[]                = $objDato;
                }
            }
            else
            {
                throw new \Exception($arrayDatosIpMac['result']);
            }
            //se formula el json de respuesta
            $strJsonResultado   = '{"total":"' . count($arrayResult) . '", "registros":' . json_encode($arrayResult) . '}';
        }
        catch (\Exception $e)
        {
            $strJsonResultado   = '{"total":"0", "registros":[], "error":[' . $e->getMessage() . ']}';
            
            $serviceUtil->insertError('Telcos+',
                                      'InfoElementoOltController.ajaxBuscarMacDesconfigurarIpAction',
                                      $e->getMessage(),
                                      $strUsrSesion,
                                      $strIpClient
                                     );
        }

        $objResponse = new Response();
        $objResponse->headers->set('Content-type', 'text/json');
        $objResponse->setContent($strJsonResultado);

        return $objResponse;
    }

    /**
     * @Secure(roles="ROLE_227-8378")
     *
     * Documentación para el método 'ajaxMacDesconfigurarIp'.
     *
     * Metodo para invocar el ws de netlife
     *
     * @author Alberto Arias <farias@telconet.ec>
     * @version 1.0 12-09-2021
     * 
     * @author Alberto Arias <farias@telconet.ec>
     * @version 1.1 06-10-2021
     *
     * @return Response $objResponse - Respuesta del WS
     */
    public function ajaxMacDesconfigurarIpAction()
    {
      ini_set('max_execution_time', 3000000);
      $objRequest           = $this->getRequest();
      $objSesion            = $objRequest->getSession();
      $strIpClient          = $objRequest->getClientIp();
      $serviceUtil          = $this->get('schema.Util');
      $strUsrSesion         = $objSesion->get('user');
      $intIdIp              = $objRequest->get("idIp");
      $serviceElemento      = $this->get('tecnico.InfoElemento');
      try
      {
          $strNombreCliente       = $objRequest->get("nombre_cliente");
          $strLogin               = $objRequest->get("login");
          $strIdentificacion      = $objRequest->get("identificacion");
          $strSerialOnt           = $objRequest->get("serial_ont");
          $strMacOnt              = $objRequest->get("mac_ont");
          $strIpOnt               = $objRequest->get("ip_ont");
          $strEstadoServicio      = $objRequest->get("estado_servicio");
          $strScope               = $objRequest->get("escope");

          $arrayParametros["nombre_cliente"]            = $strNombreCliente;
          $arrayParametros["login"]                     = $strLogin;
          $arrayParametros["identificacion"]            = $strIdentificacion;
          $arrayParametros["datos"]["serial_ont"]       = $strSerialOnt;
          $arrayParametros["datos"]["mac_ont"]          = $strMacOnt;
          $arrayParametros["datos"]["ip"]               = $strIpOnt;
          $arrayParametros["datos"]["estado_servicio"]  = $strEstadoServicio;
          $arrayParametros["datos"]["scope"]            = $strScope;
          $arrayParametros["opcion"]                    = "ELIMINAR_IP";
          $arrayParametros["ejecutaComando"]            = "SI";
          $arrayParametros["usrCreacion"]               = $strUsrSesion;
          $arrayParametros["ipCreacion"]                = $strIpClient;
          $arrayParametros["empresa"]                   = "MD";
          $arrayParametros["comandoConfiguracion"]      = "SI";
          $arrayParametros["intIdIp"]                   = $intIdIp;

          if (!empty($intIdIp))
          {
              $arrayResultado = $serviceElemento->desconfigurarIpCNR($arrayParametros);
              if( $arrayResultado['status'] != 'OK' )
              {
                  throw new \Exception($arrayResultado['mensaje']);
              }
              //se formula el json de respuesta
              $strJsonResultado   = json_encode($arrayResultado);
          }
          else
          {
              $strMensaje = "Verifíque la data del Telcos, Identificador IP no encontrado";
              $strJsonResultado   = '{"opcion":"ELIMINAR_IP_FIJA", "status":"ERROR-Telcos", "mensaje": "'.$strMensaje.'"}';
          }
      }
      catch (\Exception $e)
      {
           $strJsonResultado   = '{"opcion":"ELIMINAR_IP_FIJA", "status":"ERROR", "mensaje": "'.$e->getMessage().'"}';
           
           $serviceUtil->insertError('Telcos+',
                                     'InfoElementoOltController.ajaxMacDesconfigurarIpAction',
                                     $e->getMessage(),
                                     $strUsrSesion,
                                     $strIpClient
                                    );
      }

      $objResponse = new Response();
      $objResponse->headers->set('Content-type', 'text/json');
      $objResponse->setContent($strJsonResultado);

      return $objResponse;
   }

       /**
     * Funcion que retonar el numero de tarjetas y puertos por cada modelo de OLT.
     * 
     * Version Inicial
     * 
     * @author Alberto Arias <farias@telconet.ec>
     * @version 1.0 14-10-2022
     */
    public function getPuertoTarjetaOltAction()
    {
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        $objSession = $this->get('session');
        $objSession->save();
        session_write_close();

        $objPeticion = $this->get('request');
        
        $strMarcaElemento = $objPeticion->query->get('marcaElemento');     
        $strModeloElemento = $objPeticion->query->get('modeloElemento');
        $strTipoConsulta = $objPeticion->query->get('tipoConsulta');
        
        $arrayParametros = array(
            'strModeloElemento' => strtoupper($strModeloElemento),
            'strMarcaElemento'  => $strMarcaElemento,
            'strTipoConsulta'   => $strTipoConsulta
        );
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:InfoElemento')
            ->getPuertoTarjetaOlt($arrayParametros);
        $objRespuesta->setContent($objJson);
        
        return $objRespuesta;
    }
}

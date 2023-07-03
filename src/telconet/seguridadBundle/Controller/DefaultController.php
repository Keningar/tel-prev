<?php

namespace telconet\seguridadBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use telconet\schemaBundle\Entity\AdmiEmpresa;
use telconet\schemaBundle\Entity\AdmiOficina;
use telconet\schemaBundle\Entity\SistItemMenu;
use telconet\schemaBundle\Entity\SeguMenuPersona;

class DefaultController extends Controller
{
    public function loginAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();
 
        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
        }
 
        return $this->render('seguridadBundle:Default:login.html.twig', array(
            // last username entered by the user
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
        ));
    }
    
    
    /**
     * Documentación para el método 'menuAction'.
     *
     * Método que retorna la vista por default dependiendo de la opción seleccionada en el menú de cada módulo.
     *
     * @return Response 
     *
     * @author Desarrollo Inicial
     * @version 1.0 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 19-10-2015 - Se añade una consulta para que retorne las ciudades a consultarse en la opción de Resumen de
     *                           Instalaciones del módulo Técnico.
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.2 23-08-2016 - Se añade registro de perfil tecnico para regularizacion de servicios radio Tn
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.3 08-09-2016 - Se añade la funcionalidad de cambio de nodo wifi
     * 
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.4 20-09-2016 - Se añade los permisos para actualizacion de macs y consulta de enlaces de un servicio
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.5 28-09-2016 se agregó el permiso para ingresarElementoWifi
     * 
     * @author Duval Medina C. <dmedina@telconet.ec>
     * @version 1.6 2016-10-14 - Se añade para el módulo inicio la carga y envio de últimas actividades del usuario
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.7 17-10-2016 - Se añade la opcion de reenvio de información de la licencia office 365.
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.8 17-02-2017 - Se añade la opcion de activacion de linea telefonica del producto netvoice
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.9 29-02-2017 - Se añade la opcion de reenvio de administracion de enrutamiento BGP para TN
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 2.0 03-03-2017 se agregó el permiso para opcion de cambio de subredes publicas/privadas para productos intmpls
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 2.1 12-06-2017 - Se obtiene el tipo del vendedor del usuario en sessión para que sea enviado al twig
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 2.2 19-06-2017 - Se envía las fechas de inicio y fin con las cuales se va a trabajar para mostrar el DASHBOARD COMERCIAL para
     *                           GERENCIA
     * 
     * @author Francisco Adum <fadum@netlife.net.ec>
     * @version 1.8 06-07-2017  Se agrego el permiso para asignar ipv4 publica / empresa MD
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.9 09-11-2017  Se agrego el permiso para administrar Maquinas Virtuales DC
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.10 27-06-2019  Se agregó nuevo perfil utilizado para la generación de solicitudes de cambio de equipo por soporte
     * @since 1.9
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.10 06-09-2018  Se agrega los permisos para las opciones del producto lineas telefonia fija. Permisos:
     *              |Cambiar equipo linea |Activar linea |Detalle llamada linea |Cortar llamada saliente linea |Activar Llamada Saliente Linea
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.11 06-12-2020 - Se agrega el perfil: crearFormularioSoporteParamountNoggin
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.12 25-01-2021 - Se agrega el perfil: activarSSIDMOVIL y recuperarClaveSSIDMOVIL
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.13 10-09-2021 - Se agrega el perfil: Crear Formulario Soporte ECDF
     * 
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 1.14 29-09-2021 - Se agrega permiso para boton de soporte L1 de Goltv
     * 
     */
    public function menuAction($modulo,$opcion_menu)
    {
        $request     = $this->get('request');
        $session     =  $request->getSession();
        $opcion_menu = $session->get('menu_modulo_activo');
        
        $emGeneral = $this->getDoctrine()->getManager('telconet_general');
        $strActivo = 'Activo';
        $arrayData = array();

        $intIdPersonEmpresaRol = $session->get('idPersonaEmpresaRol');
        $strUsrCreacion        = $session->get('user');
        $strIpCreacion         = $request->getClientIp();
        $serviceComercial      = $this->get('comercial.Comercial');
	  
        switch (strtolower($session->get('modulo_activo')))
        {
            case ('administracion'):
            {
                if(strtolower($opcion_menu) == 'menu')
                {
                    return ( $this->render( 'administracionBundle:Default:'.$opcion_menu.'.html.twig', 
                                            array( 
                                                    'menu_title'      => ucfirst( $session->get('nombre_menu_modulo_activo') ), 
                                                    'menu_imagen'     => $session->get('imagen_menu_modulo_activo'),
                                                    'html_modulos'    => $session->get('html_modulos'),
                                                    'html_submodulos' => $session->get('html_submodulos')
                                                 )
                                          )
                            );
                }
                else
                {
                    return ( $this->render( 'administracionBundle:Default:layout_opcion_admin.html.twig',
                                            array(
                                                    'menu_title'      => ucfirst( $session->get('nombre_menu_modulo_activo')),
                                                    'menu_imagen'     => $session->get('imagen_menu_modulo_activo'),
                                                    'html_modulos'    => $session->get('html_modulos'), 
                                                    'html_submodulos' => $session->get('html_submodulos')
                                                 )
                                          )
                            );
                }		
                
                break;
            }
            
            case ('inicio'):
            {           
                $start                    = '';
                $limit                    = '';
                $descripcion              = "";
                $estado                   = 'Activo';
                $empresa                  = "";
                $respuestaListaPlantillas = "";
                $em_comunicacion          = $this->getDoctrine()->getManager("telconet_comunicacion");
                $entityClaseDocumento     = $em_comunicacion->getRepository('schemaBundle:AdmiClaseDocumento')
                                                            ->findOneBy( array('nombreClaseDocumento' => 'Notificacion Interna Noticia') );
                if ($entityClaseDocumento)
                {
                    // Obtener listado de Noticias servicio PlantillaService
                    /* @var servicioPlantilla PlantillaService */
                    $servicioPlantilla          = $this->get('soporte.ListaPlantilla');
                    $respuestaListaPlantillas   = $servicioPlantilla->listarPlantillas( $entityClaseDocumento->getId(), 
                                                                                        $descripcion, 
                                                                                        $estado, 
                                                                                        $empresa, 
                                                                                        $start, 
                                                                                        $limit, 
                                                                                        "SI" );
                    $respuestaListaPlantillas   = json_decode($respuestaListaPlantillas,true);
                } 
                
                $emSeguridad = $this->getDoctrine()->getManager("telconet_seguridad");
                $arrayActividadesPersona = $emSeguridad->getRepository('schemaBundle:SeguBitacoraPersona')
                                                       ->getUltimasActividades($session->get('id_empleado'));
                
                return ( $this->render( 'adminBundle:Inicio:'.$opcion_menu.'.html.twig',
                                        array(
                                              'menu_title'       => ucfirst( $session->get('nombre_menu_modulo_activo')),
                                              'listaNoticias'    => $respuestaListaPlantillas["encontrados"],
                                              'listaActividades' => $arrayActividadesPersona
                                             )
                                      )
                       );
                
                break;
            }
            case ('planificacion'):
            {
                return ( $this->render( strtolower($session->get('modulo_activo')).'Bundle:Default:'.$opcion_menu.'.html.twig',
                                        array(
                                                'menu_title'  => ucfirst( $session->get('nombre_menu_modulo_activo')),
                                                'menu_imagen' => $session->get('imagen_menu_modulo_activo') 
                                             )
                                      )
                        );
                break;
            }
            case ('soporte'):
            {                
                return ( $this->render( strtolower($session->get('modulo_activo')).'Bundle:Default:'.strtolower($opcion_menu).'.html.twig',
                                        array(
                                                'menu_title'  => ucfirst( $session->get('nombre_menu_modulo_activo')),                                            
                                                'menu_imagen' => $session->get('imagen_menu_modulo_activo') 
                                             )
                                      )
                        );
                break;
            }
            case ('tecnico'):
            { 
                $rolesPermitidos = array();

                if( $opcion_menu == "clientes" )
                {
                    //MODULO 151 - TECNICO/CLIENTES
                    if (true === $this->get('security.context')->isGranted('ROLE_151-315'))
                    {
                        $rolesPermitidos[] = 'ROLE_151-315'; //RECONECTAR SERVICIO
                    }
                    if (true === $this->get('security.context')->isGranted('ROLE_374-5097'))
                    {
                        $rolesPermitidos[] = 'ROLE_374-5097'; //Activar linea telefonica
                    }                    
                    if (true === $this->get('security.context')->isGranted('ROLE_151-3277'))
                    {
                            $rolesPermitidos[] = 'ROLE_151-3277'; //eliminar ldap cliente
                    }        
                    if (true === $this->get('security.context')->isGranted('ROLE_151-311'))
                    {
                        $rolesPermitidos[] = 'ROLE_151-311'; //CORTAR SERVICIO
                    }
                    if (true === $this->get('security.context')->isGranted('ROLE_151-313'))
                    {
                        $rolesPermitidos[] = 'ROLE_151-313'; //CANCELAR SERVICIO
                    }
                    if(true === $this->get('security.context')->isGranted('ROLE_341-3957'))
                    {
                        $rolesPermitidos[] = 'ROLE_341-3957'; //ActivarWifi
                    }
                    if(true === $this->get('security.context')->isGranted('ROLE_341-4617'))
                    {
                        $rolesPermitidos[] = 'ROLE_341-4617'; //cambiarNodoWifi
                    }
                    if(true === $this->get('security.context')->isGranted('ROLE_341-4817'))
                    {
                        $rolesPermitidos[] = 'ROLE_341-4817'; //ingresarElementoWifi
                    }                     
                    if(true === $this->get('security.context')->isGranted('ROLE_341-3977'))
                    {
                        $rolesPermitidos[] = 'ROLE_341-3977'; //CortarWifi
                    }
                    if(true === $this->get('security.context')->isGranted('ROLE_341-3958'))
                    {
                        $rolesPermitidos[] = 'ROLE_341-3958'; //ReconectarWifi
                    }
                    if(true === $this->get('security.context')->isGranted('ROLE_341-3978'))
                    {
                        $rolesPermitidos[] = 'ROLE_341-3978'; //CancelarWifi
                    }
                    if(true === $this->get('security.context')->isGranted('ROLE_341-3979'))
                    {
                        $rolesPermitidos[] = 'ROLE_341-3979'; //CambioEquipoWifi
                    }
                    if (true === $this->get('security.context')->isGranted('ROLE_151-829'))
                    {
                        $rolesPermitidos[] = 'ROLE_151-829'; //CAMBIO VELOCIDAD SERVICIO
                    }
                    if (true === $this->get('security.context')->isGranted('ROLE_151-830'))
                    {
                            $rolesPermitidos[] = 'ROLE_151-830'; //EDITAR INFO TECNICA
                    }
                    if (true === $this->get('security.context')->isGranted('ROLE_151-831'))
                    {
                        $rolesPermitidos[] = 'ROLE_151-831'; //VER INFO TECNICA
                    }
                    if (true === $this->get('security.context')->isGranted('ROLE_151-832'))
                    {
                        $rolesPermitidos[] = 'ROLE_151-832'; //CAMBIO PUERTO SERVICIO
                    }
                    if (true === $this->get('security.context')->isGranted('ROLE_151-833'))
                    {
                        $rolesPermitidos[] = 'ROLE_151-833'; //VER HISTORIAL SERVICIO
                    }
                    if (true === $this->get('security.context')->isGranted('ROLE_151-4637'))
                    {
                        $rolesPermitidos[] = 'ROLE_151-4637'; //REENVIAR INFORMACIÓN AL CLIENTE
                    }
                    if (true === $this->get('security.context')->isGranted('ROLE_151-834'))
                    {
                        $rolesPermitidos[] = 'ROLE_151-834'; //DESCARGAR PDF TECNICO
                    }
                    if (true === $this->get('security.context')->isGranted('ROLE_151-835'))
                    {
                        $rolesPermitidos[] = 'ROLE_151-835'; //VER IP PUBLICA
                    }
                    if (true === $this->get('security.context')->isGranted('ROLE_151-836'))
                    {
                        $rolesPermitidos[] = 'ROLE_151-836'; //NUEVA IP PUBLICA
                    }
                    if (true === $this->get('security.context')->isGranted('ROLE_151-837'))
                    {
                        $rolesPermitidos[] = 'ROLE_151-837'; //ELIMINAR IP PUBLICA
                    }
                    if (true === $this->get('security.context')->isGranted('ROLE_151-846'))
                    {
                        $rolesPermitidos[] = 'ROLE_151-846'; //ACTIVAR PUERTO SERVICIO
                    }
                    if (true === $this->get('security.context')->isGranted('ROLE_151-847'))
                    {
                        $rolesPermitidos[] = 'ROLE_151-847'; //CONFIRMAR SERVICIO
                    }
                    if (true === $this->get('security.context')->isGranted('ROLE_151-848'))
                    {
                        $rolesPermitidos[] = 'ROLE_151-848'; //GRABAR PARAMETROS INICIALES SERVICIO
                    }
                    if (true === $this->get('security.context')->isGranted('ROLE_151-849'))
                    {
                        $rolesPermitidos[] = 'ROLE_151-849'; //VER PARAMETROS INICIALES SERVICIO
                    }
                    if (true === $this->get('security.context')->isGranted('ROLE_151-850'))
                    {
                        $rolesPermitidos[] = 'ROLE_151-850'; //VER DOMINIO
                    }
                    if (true === $this->get('security.context')->isGranted('ROLE_151-6497'))
                    {
                        $rolesPermitidos[] = 'ROLE_151-6497'; // Cambio de elemento por Soporte
                    }
                    if (true === $this->get('security.context')->isGranted('ROLE_151-851'))
                    {
                        $rolesPermitidos[] = 'ROLE_151-851'; //AGREGAR DOMINIO
                    }
                    if (true === $this->get('security.context')->isGranted('ROLE_151-852'))
                    {
                        $rolesPermitidos[] = 'ROLE_151-852'; //ELIMINAR DOMINIO
                    }
                    if (true === $this->get('security.context')->isGranted('ROLE_151-853'))
                    {
                        $rolesPermitidos[] = 'ROLE_151-853'; //VER CORREO
                    }
                    if (true === $this->get('security.context')->isGranted('ROLE_151-854'))
                    {
                        $rolesPermitidos[] = 'ROLE_151-854'; //AGREGAR CORREO
                    }
                    if (true === $this->get('security.context')->isGranted('ROLE_151-7838'))
                    {
                        $rolesPermitidos[] = 'ROLE_151-7838'; //Activar SSID MOVIL
                    }
                    if (true === $this->get('security.context')->isGranted('ROLE_151-7857'))
                    {
                        $rolesPermitidos[] = 'ROLE_151-7857'; //Recuperar Clave SSID MOVIL
                    }
                    if (true === $this->get('security.context')->isGranted('ROLE_151-855'))
                    {
                        $rolesPermitidos[] = 'ROLE_151-855'; //VER PASSWORD CORREO
                    }
                    if (true === $this->get('security.context')->isGranted('ROLE_151-856'))
                    {
                        $rolesPermitidos[] = 'ROLE_151-856'; //ELIMINAR CORREO
                    }

                    if (true === $this->get('security.context')->isGranted('ROLE_151-1107'))
                    {
                        $rolesPermitidos[] = 'ROLE_151-1107'; //CAMBIAR CPE
                    }

                    if (true === $this->get('security.context')->isGranted('ROLE_151-2277'))
                    {
                        $rolesPermitidos[] = 'ROLE_151-2277'; //REVERSO SOLICITUD MIGRACION
                    }
                    
                    if (true === $this->get('security.context')->isGranted('ROLE_151-4597'))
                    {
                        $rolesPermitidos[] = 'ROLE_151-4597'; //HERRAMIENTA DE REGULARIZACION DE SERVICIOS RADIO TN
                    }
                    if (true === $this->get('security.context')->isGranted('ROLE_151-5457'))
                    {
                        $rolesPermitidos[] = 'ROLE_151-5457'; //ASIGNAR IPV4 PUBLICA
                    }
                    if (true === $this->get('security.context')->isGranted('ROLE_151-1298'))
                    {
                        $rolesPermitidos[] = 'ROLE_151-1298'; //CAMBIAR MAC IP FIJA
                    }
                    if (true === $this->get('security.context')->isGranted('ROLE_151-1299'))
                    {
                        $rolesPermitidos[] = 'ROLE_151-1299'; //CANCELAR IP FIJA
                    }
                    if (true === $this->get('security.context')->isGranted('ROLE_151-1297'))
                    {
                        $rolesPermitidos[] = 'ROLE_151-1297'; //ACTIVAR IP FIJA
                    }
                    if (true === $this->get('security.context')->isGranted('ROLE_151-1417'))
                    {
                        $rolesPermitidos[] = 'ROLE_151-1417'; //ACTUALIZAR INDICE CLIENTE
                    }
                    if (true === $this->get('security.context')->isGranted('ROLE_151-1377'))
                    {
                        $rolesPermitidos[] = 'ROLE_151-1377'; //UPDATE PASSWORD
                    }
                    if (true === $this->get('security.context')->isGranted('ROLE_151-1517'))
                    {
                        $rolesPermitidos[] = 'ROLE_151-1517'; //EJECUTAR CAMBIO LINEA PON 
                    }
                    if (true === $this->get('security.context')->isGranted('ROLE_151-1557'))
                    {
                        $rolesPermitidos[] = 'ROLE_151-1557'; //RECONFIGURAR PUERTO
                    }
                    if (true === $this->get('security.context')->isGranted('ROLE_151-2420'))
                    {
                        $rolesPermitidos[] = 'ROLE_151-2420'; //verLdapCliente
                    }
                    if (true === $this->get('security.context')->isGranted('ROLE_151-2421'))
                    {
                        $rolesPermitidos[] = 'ROLE_151-2421'; //configurarLdapCliente
                    }
                    if (true === $this->get('security.context')->isGranted('ROLE_151-2458'))
                    {
                        $rolesPermitidos[] = 'ROLE_151-2458'; //crearClienteLdap
                    }
                    if (true === $this->get('security.context')->isGranted('ROLE_151-1657'))
                    {
                        $rolesPermitidos[] = 'ROLE_151-1657'; //SUBIR ACTA/ENCUESTA POST SERVICIO
                    }

                    if (true === $this->get('security.context')->isGranted('ROLE_151-3779'))
                    {
                        $rolesPermitidos[] = 'ROLE_151-3779'; //cambio de Ultima Milla
                    }  
                    
                    if(true === $this->get('security.context')->isGranted('ROLE_151-3837'))
                    {
                        $rolesPermitidos[] = 'ROLE_151-3837'; //Generar Acta Entrega Recepción
                    }
                    if (true === $this->get('security.context')->isGranted('ROLE_151-4197'))
                    {
                        $rolesPermitidos[] = 'ROLE_151-4197'; //Crear Cacti
                    }
                    if (true === $this->get('security.context')->isGranted('ROLE_151-4697'))
                    {
                        $rolesPermitidos[] = 'ROLE_151-4697'; //Actualizar Mac TN
                    }
                    if (true === $this->get('security.context')->isGranted('ROLE_151-4717'))
                    {
                        $rolesPermitidos[] = 'ROLE_151-4717'; // Consultar información completa de enlace de un servicio
                    }
                    if (true === $this->get('security.context')->isGranted('ROLE_151-5057'))
                    {
                        $rolesPermitidos[] = 'ROLE_151-5057'; // Agregar Servicio a un edificio pseudope
                    }
                    if (true === $this->get('security.context')->isGranted('ROLE_151-5017'))
                    {
                        $rolesPermitidos[] = 'ROLE_151-5017'; // Administracion Enrutamiento BGP
                    }
                    if (true === $this->get('security.context')->isGranted('ROLE_151-5137'))
                    {
                        $rolesPermitidos[] = 'ROLE_151-5137'; // Cambio de subredes publicas/privadas producto IntMpls
                    }
                    if (true === $this->get('security.context')->isGranted('ROLE_151-5577'))
                    {
                        $rolesPermitidos[] = 'ROLE_151-5577'; // Administracion de Maquinas Virtuales
                    }
                    if (true === $this->get('security.context')->isGranted('ROLE_151-7697'))
                    {
                        $rolesPermitidos[] = 'ROLE_151-7697'; // Formulario Soporte L1 Paramount y Noggin
                    }
                    if (true === $this->get('security.context')->isGranted('ROLE_151-8419'))
                    {
                        $rolesPermitidos[] = 'ROLE_151-8419'; // Formulario Soporte L1 GolTv
                    }
                    if (true === $this->get('security.context')->isGranted('ROLE_415-6044'))
                    {
                        $rolesPermitidos[] = 'ROLE_415-6044'; //Cambiar equipo linea
                    }
                    if (true === $this->get('security.context')->isGranted('ROLE_415-6045'))
                    {
                        $rolesPermitidos[] = 'ROLE_415-6045'; //Activar linea
                    }
                    if (true === $this->get('security.context')->isGranted('ROLE_415-6046'))
                    {
                        $rolesPermitidos[] = 'ROLE_415-6046'; //Detalle llamada linea
                    }
                    if (true === $this->get('security.context')->isGranted('ROLE_415-6047'))
                    {
                        $rolesPermitidos[] = 'ROLE_415-6047'; //Cortar llamada saliente linea
                    }
                    if (true === $this->get('security.context')->isGranted('ROLE_415-6048'))
                    {
                        $rolesPermitidos[] = 'ROLE_415-6048'; //Activar Llamada Saliente Linea
                    }
                    if (true === $this->get('security.context')->isGranted('ROLE_151-8357'))
                    {
                        $rolesPermitidos[] = 'ROLE_151-8357'; // Formulario Soporte L2 ECDF
                    }
                    if(true === $this->get('security.context')->isGranted('ROLE_151-8697'))
                    {
                        $rolesPermitidos[] = 'ROLE_151-8697';//actulizar data tecnica
                    }
                    
                    return ( $this->render( strtolower($session->get('modulo_activo')).'Bundle:clientes:index.html.twig',
                                            array(
                                                    'rolesPermitidos' => $rolesPermitidos,
                                                    'menu_title'      => ucfirst( $session->get('nombre_menu_modulo_activo')),  
                                                    'menu_imagen'     => $session->get('imagen_menu_modulo_activo') 
                                                 )
                                          )
                            );
                }
                else
                {
                    if( $opcion_menu == 'resumen_instalaciones' )
                    {
                        $arrayCantones   = array();
                        $objParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                     ->findOneBy( array('estado' => $strActivo, 'nombreParametro' => 'CIUDADES_INSTALACIONES') );
        
                        if( $objParametroCab )
                        {
                            $objParametroCantones = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                              ->findBy( array('estado' => $strActivo, 'parametroId' => $objParametroCab) );

                            if( $objParametroCantones )
                            {
                                foreach($objParametroCantones as $objCanton)
                                {
                                    $arrayCantones[] = $objCanton->getDescripcion();
                                }
                            }
                        }
                        
                        $arrayData['cantones'] = $arrayCantones;
                    }
                    
                    return ( $this->render( strtolower($session->get('modulo_activo')).'Bundle:Default:'.$opcion_menu.'.html.twig',
                                            array(
                                                    'rolesPermitidos' => $rolesPermitidos,
                                                    'menu_title'      => ucfirst( $session->get('nombre_menu_modulo_activo')),  
                                                    'menu_imagen'     => $session->get('imagen_menu_modulo_activo'),
                                                    'data'            => $arrayData
                                                 )
                                           )
                           );
                }

                break;

            }
            
            default:
            {
                if(strtolower($opcion_menu) == 'dashboard')
                {
                    $arrayParametros = array( 'menu_title'      => ucfirst( $session->get('nombre_menu_modulo_activo')),  
                                              'menu_imagen'     => $session->get('imagen_menu_modulo_activo'),
                                              'strTipoVendedor' => '');
                    
                    if( strtolower($session->get('modulo_activo')) == 'comercial' )
                    {
                        $strFechaInicio  = "01-".date("m-Y");
                        $dateFechaInicio = (!empty($strFechaInicio) ) ? new \DateTime($strFechaInicio) : null;
                        $dateFechaFin    = (!empty($strFechaInicio) ) ? new \DateTime($strFechaInicio) : null;
                        $dateFechaFin    = ( is_object($dateFechaFin) ) ? $dateFechaFin->add(new \DateInterval('P1M')) : null;
                        $strFechaInicio  = ( is_object($dateFechaInicio) ) ? $dateFechaInicio->format('d-M-Y') : '';
                        $strFechaFin     = ( is_object($dateFechaFin) ) ? $dateFechaFin->format('d-M-Y') : '';

                        $arrayParametros['strFechaInicio']   = $strFechaInicio;
                        $arrayParametros['strFechaFin']      = $strFechaFin;
                        $arrayResultado                      = $this->getValidarDisponibilidad('TIEMPO_PERMITIDO_DASHBOARD');
                        $arrayParametros['strPermiteAcceso'] = $arrayResultado['strPermiteAcceso'];
                        $arrayParametros['strHoraInicio']    = $arrayResultado['strHoraInicio'];
                        $arrayParametros['strHoraFin']       = $arrayResultado['strHoraFin'];
                        /**
                         * BLOQUE QUE VALIDA LA CARACTERISTICA 'TIPO_VENDEDOR' ASOCIADA A EL USUARIO LOGONEADO 
                         */
                        $arrayPersonalEmpresaRol       = array('intIdPersonEmpresaRol' => $intIdPersonEmpresaRol,
                                                               'strUsrCreacion'        => $strUsrCreacion,
                                                               'strIpCreacion'         => $strIpCreacion);
                        $arrayResultadoCaracteristicas = $serviceComercial->getCaracteristicasPersonalEmpresaRol($arrayPersonalEmpresaRol);

                        if( !empty($arrayResultadoCaracteristicas) )
                        {
                            if( isset($arrayResultadoCaracteristicas['strTipoVendedor']) 
                                && !empty($arrayResultadoCaracteristicas['strTipoVendedor']) )
                            {
                                $arrayParametros['strTipoVendedor'] = $arrayResultadoCaracteristicas['strTipoVendedor'];
                            }//( isset($arrayResultadoCaracteristicas['strTipoVendedor'])...
                        }//( !empty($arrayResultadoCaracteristicas) )
                    }//( strtolower($session->get('modulo_activo')) == 'comercial' )
                        
                    
                    return ( $this->render( strtolower( $session->get('modulo_activo')).'Bundle:Default:'.$opcion_menu.'.html.twig', 
                                                        $arrayParametros ) );
                }
                else
                {
                    return ( $this->render( strtolower(  $session->get('modulo_activo')).'Bundle:'.$opcion_menu.':index.html.twig',
                                                         array( 
                                                                'menu_title' => ucfirst( $session->get('nombre_menu_modulo_activo')),  
                                                                'menu_imagen' => $session->get('imagen_menu_modulo_activo') 
                                                              )
                                         )
                            );
                }
                
                break;
            }
        }		
    }

    /**
     * getValidarDisponibilidad
     *
     * Valida la disponibilidad de una opcion segun la hora de Inicio y Fin parametrizada, esta hora debe estar almacenada en los parametros
     * con formato de 24 horas, ej: inicio: 15:00:00 fin: 17:00:00
     * @param   $strOpcionTelcos: Parametro para consultar la hora de inicio y fin.
     * @author  Kevin Baque <kbaque@telconet.ec>
     * @version 1.0 04-12-2018
     *
     * @return array [
     *                 'strPermiteAcceso'
     *                 'strHoraInicio'
     *                 'strHoraFin'
     *               ]
     */
    public function getValidarDisponibilidad($strOpcionTelcos)
    {
        $emGeneral        = $this->getDoctrine()->getManager("telconet_general");
        $serviceUtil      = $this->get('schema.Util');
        $objRequest       = $this->get('request');
        $objSession       = $objRequest->getSession();
        $strUsrCreacion   = $objSession->get('user');
        $strIpCreacion    = $objRequest->getClientIp();
        $strPermiteAcceso = "NO";
        $strHoras         = "";
        $strMinutos       = "";
        $strSegundos      = "";
        $strHmsInicio     = "";
        $strHmsFin        = "";
        $arrayResultado   = array();
        $arrayRespuesta   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                      ->getOne('TIEMPO_PERMITIDO_OPCION',
                                      '',
                                      '',
                                      $strOpcionTelcos,
                                      '',
                                      '',
                                      '',
                                      '');
        try
        {
            if (is_array($arrayRespuesta))
            {
                $strHmsInicio     = !empty($arrayRespuesta['valor1'])?$arrayRespuesta['valor1']:"";
                $strHmsFin        = !empty($arrayRespuesta['valor2'])?$arrayRespuesta['valor2']:"";
                $strHmsReferencia = date('G:i:s');

                list($strHoras, $strMinutos, $strSegundos) = array_pad(preg_split('/[^\d]+/', $strHmsInicio), 3, 0);
                $intSegundosInicio                  = 3600 * $strHoras + 60 * $strMinutos + $strSegundos;
                list($strHoras, $strMinutos, $strSegundos) = array_pad(preg_split('/[^\d]+/', $strHmsFin), 3, 0);
                $intSegundosFin                     = 3600 * $strHoras + 60 * $strMinutos + $strSegundos;
                list($strHoras, $strMinutos, $strSegundos) = array_pad(preg_split('/[^\d]+/', $strHmsReferencia), 3, 0);
                $intSegundoReferencia               = 3600 * $strHoras + 60 * $strMinutos + $strSegundos;
                if($intSegundosInicio <= $intSegundosFin)
                {
                    if($intSegundoReferencia >= $intSegundosInicio && $intSegundoReferencia <= $intSegundosFin)
                    {
                        $strPermiteAcceso = "SI";
                    }
                    else
                    {
                        $strPermiteAcceso = "NO";
                    }
                }
                else
                {
                    if($intSegundoReferencia >= $intSegundosInicio || $intSegundoReferencia <= $intSegundosFin)
                    {
                        $strPermiteAcceso = "SI";
                    }
                    else
                    {
                        $strPermiteAcceso = "NO";
                    }
                }
            }
            $arrayResultado = array('strPermiteAcceso' => $strPermiteAcceso,
                                    'strHoraInicio'    => $strHmsInicio,
                                    'strHoraFin'       => $strHmsFin);
        }
        catch( \Exception $e )
        {
            $serviceUtil->insertError('TELCOS+',
                                      'SeguridadBundle.DefaultController.validarDisponibilidadOpcionPorHora',
                                      $e->getMessage(),
                                      $strUsrCreacion,
                                      $strIpCreacion);
        }
        return $arrayResultado;
    }

    public function retornaModulos($arrayVariables)
    {        
        $script_name = $_SERVER['SCRIPT_NAME'];
        $nameFinal = ($script_name ? $script_name : $_SERVER['REQUEST_URI']);

        $em = $this->get('doctrine')->getManager('telconet_seguridad');
        
		//ARMA ARREGLOS --> PERFILES PERMITIDOS
		$usuario_perfiles = $em->getRepository('schemaBundle:SeguPerfilPersona')->loadPerfilEmpleado($arrayVariables);
		$arrayPerfiles = false;
		if($usuario_perfiles)
		{
		    foreach($usuario_perfiles as $perfiles){	
			$arrayPerfiles[] = $perfiles->getPerfilId()->getId();
		    }
		}
        
        if($arrayPerfiles && count($arrayPerfiles)>0)
        {
            $menu_modulos = $em->getRepository('schemaBundle:SeguAsignacion')->loadAsignacion_RelacionSistema($arrayPerfiles, null);
               //var_dump($menu_modulos);
               
            $htmlModulos = "";
            $htmlSubModulos = "";
            if($menu_modulos && count($menu_modulos)>0)
            {
                $htmlModulos = "<div id='menu_modulos'><ul>";
                $htmlSubModulos = "";
                foreach($menu_modulos as $key => $value)
                {
                    $nombre_acccion = $value->getRelacionSistemaId()->getAccionId()->getNombreAccion(); 
                    $descripcionOpcion = $value->getRelacionSistemaId()->getItemMenuId()->getNombreItemMenu(); 
                    $urlImg = $value->getRelacionSistemaId()->getItemMenuId()->getUrlImagen(); 
                    $nombre_modulo = $value->getRelacionSistemaId()->getModuloId()->getNombreModulo();                              
                    $idOpcion = $value->getRelacionSistemaId()->getItemMenuId()->getId();
                    
                    $htmlModulos .= "<li id='item_modulo_" . strtolower($descripcionOpcion) . "' " . 
                                "><a href='". $nameFinal . "/" .strtolower($nombre_modulo)."'><span class='item_menu_modulo'>".$descripcionOpcion."</span></a></li>";

                    
                    //SUBMODULOS ->getId()                
                    $htmlSubModulos .= "<div id='menu_modulo_".strtolower($descripcionOpcion)."' style='display:none;'>";
                    $htmlSubModulos .= "    <div id='logo_modulo'><img src='".$this->get('request')->getBasePath()."/public/images/".$urlImg."' alt='".$urlImg."' width='50' height='51' title='Modulo ".$descripcionOpcion."'/></div>";
                    $htmlSubModulos .= "    <div id='logo_sit'><img src='".$this->get('request')->getBasePath()."/public/images/logo.png' alt='logo.png' width='103' height='40' /><p id='nombre_modulo'>".$descripcionOpcion."</p></div>";
                    //$htmlSubModulos .= "    <div id='search_login'><img src='".$this->get('request')->getBasePath()."/public/images/search.png' alt='search.png' /><input type='text' placeholder='Buscar login' label='Buscar login' name='login' maxlength='100' autocomplete='off' id='login' /></div>";
                    $htmlSubModulos .= "    <div id='menu_modulo'>";


                    $menu_modulo = $this->retornaItemsMenu($arrayPerfiles, $idOpcion);
                    
                    $htmlSubModulos .= "        <ul>";
                    if($menu_modulo && count($menu_modulo)>0)
                    {
                        foreach($menu_modulo as $key2 => $value2)
                        {
                            $htmlSubModulos .= "<li id='item_submodulo_".strtolower($value2["descripcionOpcion"])."' class='rounded-corners'>".
                                                "<a href='" . $nameFinal . "/" .strtolower($nombre_modulo)."/".strtolower($value2["href"])."'>".
                                                "<img class='img_menu' src='".$this->get('request')->getBasePath()."/public/images/".$value2["img"]."' alt='".$value2["img"]."' width='30' height='32.5'/>".
                                                "<p class='alignright'>".$value2["descripcionOpcion"]."</p></a></li>";

                        }
                    }
                    $htmlSubModulos .= "        </ul>";
                    $htmlSubModulos .= "    </div>";
                    $htmlSubModulos .= "</div>";
                }                
                $htmlModulos .= "</ul></div>";
            }
            
            return array('modulos' => $htmlModulos, 'submodulos'=>$htmlSubModulos);
                
        }
        return false;
    }
    
    
    public function retornaModulos2($arrayVariables)
    {        
        $script_name = $_SERVER['SCRIPT_NAME'];
        $nameFinal = ($script_name ? $script_name : $_SERVER['REQUEST_URI']);

        $em = $this->get('doctrine')->getManager('telconet_seguridad');
        
		//ARMA ARREGLOS --> PERFILES PERMITIDOS
		$usuario_perfiles = $em->getRepository('schemaBundle:SeguPerfilPersona')->loadPerfilEmpleado($arrayVariables);
		$arrayPerfiles = false;
		if($usuario_perfiles)
		{
		    foreach($usuario_perfiles as $perfiles){	
			$arrayPerfiles[] = $perfiles->getPerfilId()->getId();
		    }
		}
        
        if($arrayPerfiles && count($arrayPerfiles)>0)
        {
            //ARMA ARREGLOS --> ITEMS MENU PERMITIDOS
            $seguasigna = $em->getRepository('schemaBundle:SeguAsignacion')->loadAsignacion($arrayPerfiles);
            $arrayItems = false;
            $arrayItemsTotals = false;
            if($seguasigna)
            {
                foreach($seguasigna as $seguasig){	

                    $sel_itemid = $seguasig->getRelacionSistemaId()->getItemMenuId();
                    echo $sel_itemid;
                    
                    $arrayItems[] = $sel_itemid;

                    $arrayItemsTotals[$sel_itemid]["accion_id"] = $seguasig->getRelacionSistemaId()->getAccionId()->getId();
                    $arrayItemsTotals[$sel_itemid]["accion_nombre"] = $seguasig->getRelacionSistemaId()->getAccionId()->getNombreAccion();
                    $arrayItemsTotals[$sel_itemid]["modulo_id"] = $seguasig->getRelacionSistemaId()->getAccionId()->getId();
                    $arrayItemsTotals[$sel_itemid]["modulo_nombre"] = $seguasig->getRelacionSistemaId()->getModuloId()->getNombreModulo();

                }
            }

            if($arrayItems && count($arrayItems)>0)
            {
                $menu_modulos_query = $em->getRepository('schemaBundle:SistItemMenu')->findListarItemsMenu("", $arrayItems);
                $menu_modulos = false;
                $menu_modulos = $this->retornaArregloMenu($menu_modulos_query, $arrayItemsTotals, "S");

                $htmlModulos = "";
                $htmlSubModulos = "";
                if($menu_modulos && count($menu_modulos)>0)
                {
                    $htmlModulos = "<div id='menu_modulos'><ul>";
                    $htmlSubModulos = "";
                    foreach($menu_modulos as $key => $value)
                    {
                        $htmlModulos .= "<li id='item_modulo_" . strtolower($value["descripcionOpcion"]) . "' " . 
                                    "><a href='". $nameFinal . "/" .strtolower($value["href"])."'><span class='item_menu_modulo'>".$value["descripcionOpcion"]."</span></a></li>";

                        //SUBMODULOS ->getId()                
                        $htmlSubModulos .= "<div id='menu_modulo_".strtolower($value["descripcionOpcion"])."' style='display:none;'>";
                        $htmlSubModulos .= "    <div id='logo_modulo'><img src='".$this->get('request')->getBasePath()."/public/images/".$value["img"]."' alt='".$value["img"]."' width='50' height='51' title='Modulo ".$value["descripcionOpcion"]."'/></div>";
                        $htmlSubModulos .= "    <div id='logo_sit'><img src='".$this->get('request')->getBasePath()."/public/images/logo.png' alt='logo.png' width='103' height='40' /><p id='nombre_modulo'>".$value["descripcionOpcion"]."</p></div>";
                        $htmlSubModulos .= "    <div id='search_login'><img src='".$this->get('request')->getBasePath()."/public/images/search.png' alt='search.png' /><input type='text' placeholder='Buscar login' label='Buscar login' name='login' maxlength='100' autocomplete='off' id='login' /></div>";
                        $htmlSubModulos .= "    <div id='menu_modulo'>";

                        $menu_modulo_query = $em->getRepository('schemaBundle:SistItemMenu')->findListarItemsMenu($value["id"], $arrayItems);
                        $menu_modulo = $this->retornaArregloMenu($menu_modulo_query, $arrayItemsTotals, "N");

                        $htmlSubModulos .= "        <ul>";
                        if($menu_modulo && count($menu_modulo)>0)
                        {
                            foreach($menu_modulo as $key2 => $value2)
                            {
                                $htmlSubModulos .= "<li id='item_submodulo_".strtolower($value2["descripcionOpcion"])."' class='rounded-corners'>".
                                                    "<a href='" . $nameFinal . "/" .strtolower($value["descripcionOpcion"])."/".strtolower($value2["href"])."'>".
                                                    "<img class='img_menu' src='".$this->get('request')->getBasePath()."/public/images/".$value2["img"]."' alt='".$value2["img"]."' width='35' height='36.5'/>".
                                                    "<p class='alignright'>".$value2["descripcionOpcion"]."</p></a></li>";

                            }
                        }
                        $htmlSubModulos .= "        </ul>";
                        $htmlSubModulos .= "    </div>";
                        $htmlSubModulos .= "</div>";
                    }
                    $htmlModulos .= "</ul></div>";
                }

                return array('modulos' => $htmlModulos, 'submodulos'=>$htmlSubModulos);
            }
        }
        return false;
    }
    
    public function retornaArregloMenu($modulo_query, $arrayItemsTotals, $opcion, $opcion_menu="")
    {                
		$menu_modulos = false;
		if($modulo_query && count($modulo_query)>0)
		{ 
		    foreach($modulo_query as $imodulo){
				$menu_modulo = array();

				if($opcion == "S")
				{
				    $id = $imodulo->getId();
				    $menu_modulo['id'] = $id;
				    $menu_modulo['descripcionOpcion'] = $imodulo->getNombreItemMenu();
				    $menu_modulo['img'] = $imodulo->getUrlImagen();
				}
				else
				{
				    $id = $imodulo["id"];
				    $menu_modulo['id'] = $id;
				    $menu_modulo['descripcionOpcion'] = $imodulo['nombreItemMenu'];
				    $menu_modulo['img'] = $imodulo['urlImagen'];

				}

				$modulo_nombre = ($arrayItemsTotals[$id] ? $arrayItemsTotals[$id]["modulo_nombre"] : "");
				$accion_nombre = ($arrayItemsTotals[$id] ? $arrayItemsTotals[$id]["accion_nombre"] : "");
				if($accion_nombre=="index") $href = $modulo_nombre;	
				else $href = $modulo_nombre . "/" . $accion_nombre;
				
				$menu_modulo['href'] =  ($opcion_menu ? $opcion_menu . "/" . $href . "/" : $href);
				$menu_modulos[] = $menu_modulo;
			}
		}
		return $menu_modulos;
    }

    public function retornaItemsMenu($arrayPerfiles, $itemPadre=null)
    { 
        $em = $this->get('doctrine')->getManager('telconet_seguridad');
        
        $seguasigna = $em->getRepository('schemaBundle:SeguAsignacion')->loadAsignacion_RelacionSistema($arrayPerfiles, $itemPadre);
        $arrayItems = false;
        $arrayItemsTotals = false;
        if($seguasigna)
        {
            foreach($seguasigna as $seguasig){	
                $relacionSistemaId = $seguasig->getRelacionSistemaId();
                $itemMenuId = $relacionSistemaId->getItemMenuId();
                
                $sel_itemid =  $itemMenuId->getId();
                $arrayItems[] = $sel_itemid;
                
                $accionId = $relacionSistemaId->getAccionId();
                
                $nombre_accion =  $accionId->getNombreAccion();
                $nombre_modulo =  $relacionSistemaId->getModuloId()->getNombreModulo();
				if($nombre_accion) $href = $nombre_modulo;	
				else $href = $nombre_modulo . "/" . $nombre_accion;
                
                if($nombre_accion && $nombre_accion != "index")
                {
                    $href = $nombre_modulo . "/" . $nombre_accion;
                }
                
                $arrayItemsTotals[$sel_itemid]["accion_id"] =  $accionId->getId();
                $arrayItemsTotals[$sel_itemid]["accion_nombre"] = $nombre_accion;
                $arrayItemsTotals[$sel_itemid]["modulo_id"] =  $accionId->getId();
                $arrayItemsTotals[$sel_itemid]["modulo_nombre"] = $nombre_modulo;
                $arrayItemsTotals[$sel_itemid]["descripcionOpcion"] =  $itemMenuId->getNombreItemMenu();
                $arrayItemsTotals[$sel_itemid]["tituloHTML"] =  $itemMenuId->getTitleHtml();
                $arrayItemsTotals[$sel_itemid]["descripcionHTML"] = $itemMenuId->getDescripcionHtml();
                $arrayItemsTotals[$sel_itemid]["img"] =  $itemMenuId->getUrlImagen();
                $arrayItemsTotals[$sel_itemid]["href"] = $href;
            }
        }
        return $arrayItemsTotals;
    }
        
}

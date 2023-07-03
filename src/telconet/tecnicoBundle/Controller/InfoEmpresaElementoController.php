<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\tecnicoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Response; 
use JMS\SecurityExtraBundle\Annotation\Secure;
use telconet\schemaBundle\Entity\InfoEmpresaElemento;
use telconet\schemaBundle\Entity\InfoEmpresaElementoUbica;

/**
 * Documentación para la clase 'InfoEmpresaElementoController'.
 *
 * Clase utilizada para manejar la asignación masiva de empresa a los elementosde en el módulo Técnico.
 *
 * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
 * @version 1.0 09-01-2016
 */
class InfoEmpresaElementoController extends Controller
{ 
    /**     
     * @Secure(roles="ROLE_325-1")
     * 
     * Documentación para el método 'indexAction'.
     * 
     * Método inicial que renderiza la consulta del listado de Elementos.
     * 
     * @return Response retorna la renderización de lista de Elementos
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 09-01-2016
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 09-07-2016
     * Se modifica el dato de la empresa obtenido de código por prefijo.
     */
    public function indexAction()
    {
        $rolesPermitidos    = array();
        $intTipoDefault     = 0;
        $intJurisdiccionDef = 0;
        $objSesion          = $this->get('request')->getSession();
        $strPrefijoEmpresa  = $objSesion->get('prefijoEmpresa');
        // Se consulta el tipo de elemento incial por defecto para seleccionar en el combo "Tipo" en los filtros de búsqueda. 
        $arrayParametros1   = array('nombreTipoElemento' => 'OLT', 'estado' => 'Activo');
        $entityTipoDefault  = $this->getDoctrine()->getManager("telconet_infraestructura")
                                                  ->getRepository('schemaBundle:AdmiTipoElemento')
                                                  ->findOneBy($arrayParametros1);
        
        $arrayParametros2      = array('oficinaId' => $objSesion->get('idOficina'), 'estado' => array('Activo', 'Modificado'));
        $entityJurisdiccionDef = $this->getDoctrine()->getManager("telconet_infraestructura")
                                                     ->getRepository('schemaBundle:AdmiJurisdiccion')
                                                     ->findOneBy($arrayParametros2, array('feCreacion' => 'ASC'));
                                                 
        if($entityTipoDefault)
        {
            $intTipoDefault = $entityTipoDefault->getId();
        }
        
        if($entityJurisdiccionDef)
        {
            $intJurisdiccionDef = $entityJurisdiccionDef->getId();
        }
        
        if(true === $this->get('security.context')->isGranted('ROLE_325-1'))//Index
        {
            $rolesPermitidos[] = 'ROLE_325-1';
        } 
        if(true === $this->get('security.context')->isGranted('ROLE_325-3377'))//Consultar
        {
            $rolesPermitidos[] = 'ROLE_325-3377';
        } 
        if(true === $this->get('security.context')->isGranted('ROLE_325-3378'))//Asignar 
        {
            $rolesPermitidos[] = 'ROLE_325-3378';
        } 
        return $this->render('tecnicoBundle:InfoEmpresaElemento:index.html.twig', array('rolesPermitidos'     => $rolesPermitidos, 
                                                                                        'empresa'             => $strPrefijoEmpresa,
                                                                                        'tipoDefault'         => $intTipoDefault,
                                                                                        'jurisdiccionDefault' => $intJurisdiccionDef,
                                                                                        ));
    }
    
    /**
     * @Secure(roles="ROLE_325-3377")
     * 
     * Documentación para el método 'gridElementosEmpresaAction'.
     *
     * Método que consulta la lista de elementos
     * 
     * @return Response lista de elementos                                                                       
     *
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 09-01-2016           
     */
    public function gridElementosEmpresaAction()
    {
        $objRequest = $this->get('request');

        $arrayParametros['TIPO']         = $objRequest->get('tipo');
        $arrayParametros['ELEMENTO']     = TRIM($objRequest->get('nombre'));
        $arrayParametros['MARCA']        = $objRequest->get('marca');
        $arrayParametros['EMPRESA']      = $objRequest->get('empresa');
        $arrayParametros['ESTADO']       = $objRequest->get("estado");
        $arrayParametros['MODELO']       = $objRequest->get('modelo');
        $arrayParametros['JURISDICCION'] = $objRequest->get('jurisdiccion');
        $arrayParametros['CANTON']       = $objRequest->get('canton');
        $arrayParametros['START']        = $objRequest->get('start');
        $arrayParametros['LIMIT']        = $objRequest->get('limit');

        $strJsonElementos  = $this->getDoctrine()->getManager("telconet_infraestructura")
                                                 ->getRepository('schemaBundle:InfoElemento')
                                                 ->generarJsonElementosEmpresa($arrayParametros);
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $respuesta->setContent($strJsonElementos);
        return $respuesta;
    }

    /**
     * @Secure(roles="ROLE_325-3378")
     * 
     * Documentación para el método 'asignarEmpresaElementosAction'.
     *
     * Método que asigna masivamente la relacion de los elementos seleccionados a una empresa
     * 
     * @return Response Respuesta del resultado.
     *
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 09-01-2016
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 09-07-2016
     * Se modifica el dato de la empresa obtenido de código por prefijo.         
     */
    public function asignarEmpresaElementosAction()
    {
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        $objRequest        = $this->get('request');
        $objSession        = $objRequest->getSession();
        $strUsrCreacion    = $objSession->get('user');
        $strElementos      = $objRequest->get('elementos');
        $strPrefijoEmpresa = $objRequest->get('empresa');
        
        try
        {
            $arrayElementos = explode('|', $strElementos);

            $emInfraestructura->getConnection()->beginTransaction();
            
            foreach($arrayElementos as $intIdElemento)
            {
                $entityElemento = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($intIdElemento);
                if($entityElemento)
                {
                    $entityEmpresaGrupo = $emInfraestructura->getRepository('schemaBundle:InfoEmpresaGrupo')
                                                            ->findOneBy(array('prefijo' => $strPrefijoEmpresa));
                    $objEmpresaElementoUbicaRepository = $emInfraestructura->getRepository('schemaBundle:InfoEmpresaElementoUbica');
                    $arrayParametros                   = array('elementoId' => $entityElemento, 'empresaCod' => $entityEmpresaGrupo->getId());
                    // Busco la ubicación del elemento por empresa nueva
                    $entityEmpresaElementoUbicaActual  = $objEmpresaElementoUbicaRepository->findOneBy($arrayParametros);

                    // Si el elemento no tiene una ubicación en la nueva empresa se la asigno
                    if(!$entityEmpresaElementoUbicaActual)
                    {
                        $arrayParametros                  = array('elementoId' => $entityElemento);
                        // Encuentro a ubicación actual del elemento para asignársela a la nueva empresa
                        $entityEmpresaElementoUbicaActual = $objEmpresaElementoUbicaRepository->findOneBy($arrayParametros);

                        if($entityEmpresaElementoUbicaActual)
                        {
                            $entityEmpresaElementoUbica = new InfoEmpresaElementoUbica();
                            
                            $entityEmpresaElementoUbica->setEmpresaCod($entityEmpresaGrupo->getId());
                            $entityEmpresaElementoUbica->setElementoId($entityElemento);
                            $entityEmpresaElementoUbica->setUbicacionId($entityEmpresaElementoUbicaActual->getUbicacionId());
                            $entityEmpresaElementoUbica->setUsrCreacion($strUsrCreacion);
                            $entityEmpresaElementoUbica->setFeCreacion(new \DateTime('now'));
                            $entityEmpresaElementoUbica->setIpCreacion('127.0.0.1');
                            
                            $emInfraestructura->persist($entityEmpresaElementoUbica);
                        }
                        else
                        {
                            $strNombreElemento = $entityElemento->getNombreElemento();
                            throw new \Exception("El elemento $strNombreElemento no dispone de una ubicación actual");
                        }
                    }

                    $entityEmpresaElemento = $emInfraestructura->getRepository('schemaBundle:InfoEmpresaElemento')
                                                               ->findOneBy(array('empresaCod' => $entityEmpresaGrupo->getId(), 
                                                                                 'elementoId' => $entityElemento));
                    // Si no existe se crea la nueva relación del elemento a la nueva empresa
                    if(!$entityEmpresaElemento)
                    {
                        $entityEmpresaElemento = new InfoEmpresaElemento();
                        $entityEmpresaElemento->setEmpresaCod($entityEmpresaGrupo->getId());
                        $entityEmpresaElemento->setElementoId($entityElemento);
                        $entityEmpresaElemento->setFeCreacion(new \DateTime('now'));
                        $entityEmpresaElemento->setUsrCreacion($strUsrCreacion);
                        $entityEmpresaElemento->setIpCreacion('127.0.0.1');
                    }
                    $entityEmpresaElemento->setEstado('Activo');

                    $emInfraestructura->persist($entityEmpresaElemento);
                }
                else
                {
                    throw new \Exception("No existe el Elemento (id:$intIdElemento).");
                }
            }
            
            $emInfraestructura->flush();
            $emInfraestructura->getConnection()->commit();
            $strResponse = 'OK';
        }
        catch(Exception $e)
        {
            $emInfraestructura->getConnection()->rollback();
            $emInfraestructura->getConnection()->close();
            $strResponse = $e->getMessage();
        }
        return new Response($strResponse);
    }

    /**
     * @Secure(roles="ROLE_325-1")
     * 
     * Documentación para el método 'getAjaxComboTiposElementoAction'.
     *
     * Método que devuelve json con los tipos de los elementos
     * 
     * @return Response tipos de elemento
     *
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 09-01-2016  
     * 
     * @author Allan Suarez Carvajal <arsuarez@telconet.ec>
     * @version 1.1 14-06-2018 - Se envia el nombre del tipo de elemento en forma de native query por mejora de costos
     */
    public function getAjaxComboTiposElementoAction()
    {
        $arrayParametros = array('estado'     => 'Activo', 
                                 'nombre'     => '',
                                 'codEmpresa' => '',
                                 'start'      => '',
                                 'limit'      => '',
                                 'activoFijo' => '',
                                 'order'      => 'TIPO.NOMBRE_TIPO_ELEMENTO');
        
        $strJsonTiposElemento = $this->getDoctrine()->getManager("telconet_infraestructura")
                                                    ->getRepository('schemaBundle:AdmiTipoElemento')
                                                    ->generarJsonTiposElementos($arrayParametros);
        $objResponse = new Response();
        $objResponse->headers->set('Content-type', 'text/json');
        $objResponse->setContent($strJsonTiposElemento);
        return $objResponse;
    }
    
    /**
     * @Secure(roles="ROLE_325-1")
     * 
     * Documentación para el método 'getAjaxComboMarcasElementoAction'.
     *
     * Método que devuelve json con las marcas de los elementos
     * 
     * @return Response marcas de elemento
     *
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 09-01-2016           
     */
    public function getAjaxComboMarcasElementoAction()
    {
        $intIdTipoElemento     = $this->get('request')->get("tipo");
        $strJsonMarcasElemento = $this->getDoctrine()->getManager("telconet_infraestructura")
                                                     ->getRepository('schemaBundle:AdmiMarcaElemento')
                                                     ->generarJsonMarcasElementosPorTipo($intIdTipoElemento, 'Activo', '', '');
        $objResponse = new Response();
        $objResponse->headers->set('Content-type', 'text/json');
        $objResponse->setContent($strJsonMarcasElemento);
        return $objResponse;
    }
    
    /**
     * @Secure(roles="ROLE_325-1")
     * 
     * Documentación para el método 'getAjaxComboCantonesAction'.
     *
     * Método que devuelve json con los Cantones disponibles
     * 
     * @return Response marcas de elemento
     *
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 09-01-2016           
     */
    public function getAjaxComboCantonesAction()
    {
        $objCantonRepositorio = $this->getDoctrine()->getManager("telconet_general")->getRepository('schemaBundle:AdmiCanton');
        $intIdJurisdiccion    = $this->get('request')->get('jurisdiccion');
        $strJsonCantones      = $objCantonRepositorio->generarJsonCantonesPorJurisdiccion($intIdJurisdiccion, 'Activo', '', '');
        
        $objResponse = new Response();
        $objResponse->headers->set('Content-type', 'text/json');
        $objResponse->setContent($strJsonCantones);
        return $objResponse;
    }
     
    /**
     * @Secure(roles="ROLE_325-1")
     * 
     * Documentación para el método 'getAjaxComboModelosElementoAction'.
     *
     * Método que devuelve json con los modelos de elementos disponibles
     * 
     * @return Response modelos de elemento
     *
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 09-01-2016           
     */
    public function getAjaxComboModelosElementoAction()
    {
        $objRequest = $this->get('request');
        $intTipo    = $objRequest->get("tipo");
        $intMarca   = $objRequest->get('marca');
        
        $strJsonModelosElemento = $this->getDoctrine()->getManager("telconet_infraestructura")
                                                      ->getRepository('schemaBundle:AdmiModeloElemento')
                                                      ->generarJsonModelosElementos('', $intMarca, $intTipo, 'Activo', '', '');
        $objResponse = new Response();
        $objResponse->headers->set('Content-type', 'text/json');
        $objResponse->setContent($strJsonModelosElemento);
        return $objResponse;
    }
     
    /**
     * @Secure(roles="ROLE_325-1")
     * 
     * Documentación para el método 'getAjaxComboJurisdiccionesAction'.
     *
     * Método que devuelve json con las Jurisdicciones disponibles
     * 
     * @return Response Jurisdicciones
     *
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 09-01-2016
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 09-07-2016
     * Se modifica el dato de la empresa obtenido de código por prefijo.  
     */
    public function getAjaxComboJurisdiccionesAction()
    {
        $strPrefijoEmpresa     = $this->get('request')->get('empresa');
        $em                    = $this->getDoctrine()->getManager();
        $entityEmpresaGrupo    = $em->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneBy(array('prefijo' => $strPrefijoEmpresa));
        $strJsonJurisdicciones = $em->getRepository('schemaBundle:AdmiJurisdiccion')
                                    ->generarJsonJurisdiccionesPorEmpresa($entityEmpresaGrupo->getId());
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        $objRespuesta->setContent($strJsonJurisdicciones);
        return $objRespuesta;
    }
    
    /**
     * @Secure(roles="ROLE_325-1")
     * 
     * Documentación para el método 'getAjaxComboEmpresasAction'.
     * 
     * Consulta las empresas 'MD', 'TTCO', 'TN' para mostrar en el combo, excluye la empresa que se envíe por parámetro.
     * 
     * @return Response Lista de Empresas.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 07-03-2016
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 11-07-2016
     * Se cambia cod_empresa por prefijo para la obtención del listado.
     */
    public function getAjaxComboEmpresasAction()
    {
        $arrayParametros['EMPRESASPREF'] = array('MD', 'TTCO', 'TN');
        
        $objRepository = $this->getDoctrine()->getManager()->getRepository('schemaBundle:InfoEmpresaGrupo');
        $strPrefijo    = $this->get('request')->get('empresa');
        
        if($strPrefijo != '')
        {
            $arrayParametros['EMPRESASPREF'] = array_diff($arrayParametros['EMPRESASPREF'], array($strPrefijo));
        }
        
        $strJsonEmpresas = $objRepository->generarJsonEmpresasByPrefijo($arrayParametros);
        
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        $objRespuesta->setContent($strJsonEmpresas);
        
        return $objRespuesta;
    }
    
}
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
use telconet\schemaBundle\Entity\InfoIpElemento;
use telconet\schemaBundle\Entity\InfoIp;
use telconet\schemaBundle\Entity\InfoHistorialElemento;
use telconet\schemaBundle\Entity\InfoUbicacion;
use telconet\schemaBundle\Entity\InfoElementoTrazabilidad;
use telconet\schemaBundle\Entity\InfoRelacionElemento;
use telconet\schemaBundle\Entity\InfoDetalleElemento;
use telconet\tecnicoBundle\Resources\util\Util;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;
use telconet\schemaBundle\Entity\ReturnResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use \PHPExcel;
use \PHPExcel_IOFactory;
use \PHPExcel_Shared_Date;
use \PHPExcel_Style_NumberFormat;
use \PHPExcel_Worksheet_PageSetup;
use \PHPExcel_CachedObjectStorageFactory;
use \PHPExcel_Settings;

/**
 * Clase Controlador InfoElementoCpe
 * 
 * Clase donde se implementara toda consulta y acciones
 * para los elementos cpes
 * 
 * @author Francisco Adum <fadum@telconet.ec>
 * @version 1.0 26-05-2014
 */
class InfoElementoCpeController extends Controller implements TokenAuthenticatedController
{ 
    /**
     * Funcion que sirve para que el grid del index 
     * cargue.
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-05-2014
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 16-03-2018 Se realizan ajustes porque se implementa funcionalidad de carga masiva de series para la auditoria de elementos
     */
    public function indexCpeAction(){
        $session               = $this->get('session');
        $strCodEmpresa         = $session->get('idEmpresa');
        $strBanderaCargaMasiva = "N";
        $session->save();
        session_write_close();

        if($strCodEmpresa == "10")
        {
            $strBanderaCargaMasiva = "S";
        }

        $rolesPermitidos = array();

        if(true === $this->get('security.context')->isGranted('ROLE_247-5737'))
        {
            $rolesPermitidos[] = 'ROLE_247-5737';//carga masiva de series
        }
        if(true === $this->get('security.context')->isGranted('ROLE_247-5758'))
        {
            $rolesPermitidos[] = 'ROLE_247-5758';//ingresar_trazabilidad
        }
        return $this->render('tecnicoBundle:InfoElementoCpe:index.html.twig',
                              array('rolesPermitidos' => $rolesPermitidos,
                                    'strCargaMasiva'  => $strBanderaCargaMasiva));
    }


    /**
    * cambiarElementoAEnTransitoAction
    * Funcion que cambia los elementos a en Transito
    *
    * @return json $arrayRespuesta
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 08-03-2018
	*/
    public function cambiarElementoAEnTransitoAction()
    {
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');
        $objPeticion        = $this->getRequest();
        $objSession         = $objPeticion->getSession();
        $strCodEmpresa      = $objSession->get('idEmpresa');
        $strUserSession     = $objSession->get('user');
        $strIpCreacion      = $objPeticion->getClientIp();
        $strTramaElementos  = $objPeticion->get('tramaSerie');
        $strEstadoElemento  = $objPeticion->get('estadoElemento');
        $strUbicacion       = "";
        $arrayElementos     = explode("|", $strTramaElementos);
        $objResponse        = new JsonResponse();
        $arrayRespuesta     = array();
        $serviceUtil        = $this->get('schema.Util');
        $emInfraestructura->getConnection()->beginTransaction();
        try
        {
            for($i = 0; $i <= count($arrayElementos); $i++)
            {
                $objInfoElementoTrazabilidad = $emInfraestructura->getRepository('schemaBundle:InfoElementoTrazabilidad')
                                                                 ->findOneBy(array("numeroSerie" => $arrayElementos[$i]));

                if(is_object($objInfoElementoTrazabilidad))
                {
                    $objElementoTrazabilidad = new InfoElementoTrazabilidad();
                    $objElementoTrazabilidad->setNumeroSerie($objInfoElementoTrazabilidad->getNumeroSerie());
                    $objElementoTrazabilidad->setEstadoTelcos($objInfoElementoTrazabilidad->getEstadoTelcos());
                    $objElementoTrazabilidad->setEstadoNaf($objInfoElementoTrazabilidad->getEstadoNaf());

                    if($strEstadoElemento == "EnTransito" || $strEstadoElemento == "Perdido")
                    {
                        $strUbicacion = "EnTransito";
                    }

                    $objElementoTrazabilidad->setEstadoActivo($strEstadoElemento);
                    $objElementoTrazabilidad->setUbicacion($strUbicacion);
                    $objElementoTrazabilidad->setLogin($objInfoElementoTrazabilidad->getLogin());

                    $objInfoPersona = $emInfraestructura->getRepository('schemaBundle:InfoPersona')->findOneBy(array("login" => $strUserSession));

                    if(is_object($objInfoPersona))
                    {
                        $objElementoTrazabilidad->setResponsable($objInfoPersona->__toString());
                    }

                    $objElementoTrazabilidad->setCodEmpresa($strCodEmpresa);
                    $objElementoTrazabilidad->setTransaccion('Cambiar a Transito');
                    $objElementoTrazabilidad->setUsrCreacion($objSession->get('user'));
                    $objElementoTrazabilidad->setFeCreacion(new \DateTime('now'));
                    $objElementoTrazabilidad->setIpCreacion($objPeticion->getClientIp());
                    $emInfraestructura->persist($objElementoTrazabilidad);
                    $emInfraestructura->flush();
                }
            }
            $emInfraestructura->getConnection()->commit();
            $arrayRespuesta["estado"]  = "Ok";
            $arrayRespuesta["mensaje"] = "Transaccion Exitosa";

        }
        catch(\Exception $ex)
        {
            if($emInfraestructura->isTransactionActive())
            {
                $emInfraestructura->rollback();
            }

            $emInfraestructura->close();

            $serviceUtil->insertError('Telcos+',
                                      'InfoElementoCpeController->cambiarElementoAEnTransitoAction',
                                      $ex->getMessage(),
                                      $strUserSession,
                                      $strIpCreacion);

            $arrayRespuesta["estado"]  = "Error";
            $arrayRespuesta["mensaje"] = "Error en la transaccion";
        }

        $objResponse->setData($arrayRespuesta);

        return $objResponse;
    }


    /**
    * getTiposElementosAction
    * Funcion que retorna los tipos de elementos
    *
    * @return json $objResponse
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 08-03-2018
	*/
    public function getTiposElementosAction()
    {
        $objPeticion        = $this->get('request');
        $strTipoElemento    = $objPeticion->query->get('query') ? $objPeticion->query->get('query') : "";
        $emInfraestructura  = $this->getDoctrine()->getManager("telconet_infraestructura");
        $arrayEncontrados   = array();
        $arrayRespuesta     = array();
        $intNumeroRegistros = 0;
        $objResponse        = new JsonResponse();

        //Se obtiene los tipos de elementos
        $arrayParametros["strNombreTipoElemento"] = $strTipoElemento;
        $arrayParametros["strEstado"]             = "Activo";
        $objAdmiTiposElementos = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->getTiposElementos($arrayParametros);

        $intNumeroRegistros = count($objAdmiTiposElementos);

        if($intNumeroRegistros > 0)
        {
            foreach($objAdmiTiposElementos as $objAdmiTipoElemento)
            {
                $arrayEncontrados[] = array('idTipoElemento'      => $objAdmiTipoElemento->getId(),
                                            'NombreTipoElemento'  => $objAdmiTipoElemento->getNombreTipoElemento());
            }
        }

        $arrayRespuesta["total"]       = $intNumeroRegistros;
        $arrayRespuesta["encontrados"] = $arrayEncontrados;

        $objResponse->setData($arrayRespuesta);

        return $objResponse;
    }


    /**
    * getOficinasAction
    * Funcion que retorna todas las oficinas por empresa
    *
    * @return json $objResponse
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 16-03-2018
	*/
    public function getOficinasAction()
    {
        $objPeticion        = $this->get('request');
        $objSession         = $objPeticion->getSession();
        $strNombreOficina   = $objPeticion->query->get('query') ? $objPeticion->query->get('query') : "";
        $emInfraestructura  = $this->getDoctrine()->getManager("telconet_infraestructura");
        $arrayEncontrados   = array();
        $arrayRespuesta     = array();
        $intNumeroRegistros = 0;
        $objResponse        = new JsonResponse();

        //Se obtiene las oficinas por ejemplo
        $arrayParametros["strNombreOficina"] = $strNombreOficina;
        $objInfoOficinaGrupo = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->getOficinasPorEmpresa($arrayParametros);

        $intNumeroRegistros = count($objInfoOficinaGrupo);

        if($intNumeroRegistros > 0)
        {
            foreach($objInfoOficinaGrupo as $objInfoOfiGrupo)
            {
                $arrayEncontrados[] = array('idOficina'      => $objInfoOfiGrupo->getId(),
                                            'nombreOficina'  => $objInfoOfiGrupo->getNombreOficina());
            }
        }

        $arrayRespuesta["total"]       = $intNumeroRegistros;
        $arrayRespuesta["encontrados"] = $arrayEncontrados;

        $objResponse->setData($arrayRespuesta);

        return $objResponse;
    }


    /**
    * getModelosElementosAction
    * Funcion que retorna los modelos de los elementos
    *
    * @return json $objResponse
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 08-03-2018
	*/
    public function getModelosElementosAction()
    {
        $objPeticion        = $this->get('request');
        $intTipoElementoId  = $objPeticion->query->get('tipoElementoId') ? $objPeticion->query->get('tipoElementoId') : "";
        $strNombreModeloElemento = $objPeticion->query->get('query') ? $objPeticion->query->get('query') : "";
        $emInfraestructura  = $this->getDoctrine()->getManager("telconet_infraestructura");
        $arrayEncontrados   = array();
        $arrayRespuesta     = array();
        $intNumeroRegistros = 0;
        $objResponse        = new JsonResponse();

        //Se obtiene las tareas configuradas para la tarea
        $arrayParametros["strNombreModeloElemento"] = $strNombreModeloElemento;
        $arrayParametros["intTipoElementoId"]       = $intTipoElementoId;
        $arrayParametros["strEstado"]               = "Activo";
        $objAdmiModelosElementos = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->getModelosElementos($arrayParametros);

        $intNumeroRegistros = count($objAdmiModelosElementos);

        if($intNumeroRegistros > 0)
        {
            foreach($objAdmiModelosElementos as $objAdmiModeloElemento)
            {
                $arrayEncontrados[] = array('idModeloElemento'      => $objAdmiModeloElemento->getId(),
                                            'nombreModeloElemento'  => $objAdmiModeloElemento->getNombreModeloElemento());
            }
        }

        $arrayRespuesta["total"]       = $intNumeroRegistros;
        $arrayRespuesta["encontrados"] = $arrayEncontrados;

        $objResponse->setData($arrayRespuesta);

        return $objResponse;
    }


    /**
    * getElementosAuditoriaAction
    * Funcion que retorna lista de los elementos
    *
    * @return json $objResponse
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 08-03-2018
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.1 12-04-2018 Se realizan ajustes para consultar las series en el NAF, cuando no esten en la trazabilidad ni en el telcos
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.2 23-04-2018 Se realizan ajustes para consultar las series en el NAF, cuando han sido despachadas pero no se encuentran en la
    *                         IN_ARTICULOS_INSTALACION
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.3 02-06-2018 Se realizan ajustes para consultar las series que no tienen trazabilidad y que solo se encuentran en el naf
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.4 02-08-2018 Se realizan ajustes para consultar la descripcion,marca y modelo de las series que estan en la trazabilidad en
    *                         estado: PendienteInstalar y no se encuentran en telcos aun
    *
    * @author Germán Valenzuela <gvalenzuela@telconet.ec>
    * @version 1.5 20-04-2021 - Se agrega en la respuesta el parámetro 'nombreNodo' para detectar los elementos que se
    *                           encuentran fisicamente en el nodo.
	*/
    public function getElementosAuditoriaAction()
    {
        $objPeticion       = $this->get('request');
        $objSession        = $objPeticion->getSession();
        $strCodEmpresa     = $objSession->get('idEmpresa');
        $emInfraestructura = $this->getDoctrine()->getManager("telconet_infraestructura");
        $emGeneral         = $this->getDoctrine()->getManager("telconet_general");
        $emNaf             = $this->getDoctrine()->getManager('telconet_naf');
        $emComercial       = $this->getDoctrine()->getManager('telconet');
        $strCriterio       = $objPeticion->query->get('criterio') ? $objPeticion->query->get('criterio') : "";
        $strValor          = $objPeticion->query->get('valor') ? $objPeticion->query->get('valor') : "";
        $intModeloElemento = $objPeticion->query->get('modelo') ? $objPeticion->query->get('modelo') : "";
        $strSerie          = $objPeticion->query->get('serie') ? $objPeticion->query->get('serie') : "";
        $strEstado         = $objPeticion->query->get('estado') ? $objPeticion->query->get('estado') : "";
        $strResponsable    = $objPeticion->query->get('responsable') ? $objPeticion->query->get('responsable') : "";
        $strUbicacion      = $objPeticion->query->get('ubicacion') ? $objPeticion->query->get('ubicacion') : "";
        $strFechaDesde     = $objPeticion->query->get('fechaDesde') ? $objPeticion->query->get('fechaDesde') : "";
        $strFechaHasta     = $objPeticion->query->get('fechaHasta') ? $objPeticion->query->get('fechaHasta') : "";
        $strOficina        = $objPeticion->query->get('nombreOficina') ? $objPeticion->query->get('nombreOficina') : "";
        $intOficina        = $objPeticion->query->get('idOficina') ? $objPeticion->query->get('idOficina') : "";
        $intStart          = $objPeticion->query->get('start');
        $intLimit          = $objPeticion->query->get('limit');

        $arrayEncontrados       = array();
        $arrayRespuesta         = array();
        $intNumeroRegistros     = 0;
        $objResponse            = new JsonResponse();
        $boolActualizarActivo   = false;
        $strFechaCreacionNaf    = "";
        $strEstadoNafPI         = "PendienteInstalar";

        //Se obtiene las tareas configuradas para la tarea
        $arrayParametros["intStart"]        = $intStart;
        $arrayParametros["intLimit"]        = $intLimit;
        $arrayParametros["strCriterio"]     = $strCriterio;
        $arrayParametros["strValor"]        = $strValor;
        $arrayParametros["intModelo"]       = $intModeloElemento;
        $arrayParametros["strSerie"]        = $strSerie;
        $arrayParametros["strEstado"]       = $strEstado;
        $arrayParametros["strCodEmpresa"]   = $strCodEmpresa;
        $arrayParametros["strResponsable"]  = $strResponsable;
        $arrayParametros["strUbicacion"]    = $strUbicacion;
        $arrayParametros["strFechaDesde"]   = $strFechaDesde;
        $arrayParametros["strFechaHasta"]   = $strFechaHasta;
        $arrayParametros["strOficina"]      = $strOficina;
        $arrayParametros["intOficina"]      = $intOficina;
        $arrayParametros["emNaf"]           = $emNaf;

        //Se consulta el valor del campo Estado Naf configurado como parametro
        $objAdmiParametroDet = $emNaf->getRepository('schemaBundle:AdmiParametroDet')
                                     ->findOneBy(array("descripcion" => "VALOR DECODE CAMPO ESTADO_NAF",
                                                       "estado"      => "Activo"));
        if(is_object($objAdmiParametroDet))
        {
           $strEstadoNafPI = $objAdmiParametroDet->getValor1();
        }

        $objElementos = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->getElementosAuditoria($arrayParametros);

        //Si es igual a cero quiere decir que la serie no se encuentra en la trazabilidad ni en el Telcos
        if($objElementos["total"] == 0)
        {
            $objElementos = $emNaf->getRepository('schemaBundle:InfoElemento')->getElementoDesdeNaf($arrayParametros);
        }

        $intNumeroRegistros = $objElementos["total"];

        if($intNumeroRegistros > 0)
        {
            foreach($objElementos["registros"] as $objElemento)
            {
                $boolActualizarActivo = false;

                if($objElemento["codEmpresa"] == "18" && (($objElemento["estadoActivo"] != "EnOficinaMd" &&
                    $objElemento["estadoActivo"] == "Cancelado" &&
                    $objElemento["estadoNaf"] == "Instalado" && $objElemento["estadoTelcos"] = "Eliminado") ||
                    $objElemento["estadoActivo"] == "EnTransito") )
                {
                    $boolActualizarActivo = true;
                }

                //Se obtiene la fecha de creacion en NAF
                $arrayParametrosNaf["strNumeroSerie"] = $objElemento["numeroSerie"];
                $objElementoNaf                       = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                          ->getRegistroNaf($arrayParametrosNaf);

                $strFechaCreacionNaf = "";
                if(is_object($objElementoNaf))
                {
                    $strFechaCreacionNaf = strval(date_format($objElementoNaf->getFeCreacionNaf(), "d-m-Y H:i"));
                }

                //Cuando la serie no es encontrada en la Trazabilidad pero si en el Telcos, se consulta la fecha del Naf
                if($objElementos["bandera"] == "S")
                {
                    $objArticuloInstalacion = $emNaf->getRepository('schemaBundle:InArticulosInstalacion')
                                                    ->findOneBy(array("numeroSerie" => strtoupper($objElemento["numeroSerie"])),
                                                                array('fecha' => 'DESC','idInstalacion' => 'DESC'));

                    if(is_object($objArticuloInstalacion))
                    {
                        $strEstadoNaf = $objArticuloInstalacion->getEstado();
                        if($strEstadoNaf == "PI")
                        {
                            $objElemento["estadoNaf"] = $strEstadoNafPI;
                        }
                        else if($strEstadoNaf == "RE")
                        {
                            $objElemento["estadoNaf"] = "Retirado";
                        }
                        else if($strEstadoNaf == "IN")
                        {
                            $objElemento["estadoNaf"] = "Instalado";
                        }
                        else
                        {
                            $objElemento["estadoNaf"] = "Desconocido";
                        }
                        $strCedulaResponsable = $objArticuloInstalacion->getCedula();
                        $objResponsable       = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                            ->findOneBy(array("identificacionCliente" => $strCedulaResponsable));

                        if(is_object($objResponsable))
                        {
                            $objElemento["responsable"] = $objResponsable->__toString();
                        }
                    }
                    else
                    {
                        $objElementos = $emNaf->getRepository('schemaBundle:InfoElemento')->getElementoDesdeNaf($arrayParametros);

                        if(count($objElementos) > 0)
                        {
                            foreach($objElementos["registros"] as $objElemento2)
                            {
                                $objElemento["responsable"] = $objElemento2["responsable"];
                                $objElemento["estadoNaf"]   = $objElemento2["estadoNaf"];

                                if($objElemento2["estadoNaf"] == "En Bodega")
                                {
                                    $objElemento["ubicacion"] = 'EnBodega';
                                }
                            }
                        }
                    }
                }
                //Cuando la serie solo fue encontrada en el NAF
                else if ($objElementos["bandera"] == "NAF")
                {
                    if($objElemento["estadoNaf"] == $strEstadoNafPI || $objElemento["estadoNaf"] == "Instalado"
                      || $objElemento["estadoNaf"] == "Retirado" )
                    {
                        $objArticuloInstalacion = $emNaf->getRepository('schemaBundle:InArticulosInstalacion')
                                                        ->findOneBy(array("numeroSerie" => strtoupper($objElemento["numeroSerie"])),
                                                                    array('fecha' => 'DESC','idInstalacion' => 'DESC'));
                        if(is_object($objArticuloInstalacion))
                        {
                            $strCedulaResponsable = $objArticuloInstalacion->getCedula();
                            $objResponsable       = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                                ->findOneBy(array("identificacionCliente" => $strCedulaResponsable));
                            if(is_object($objResponsable))
                            {
                                $objElemento["responsable"] = $objResponsable->__toString();
                            }
                        }
                    }
                    else if($objElemento["estadoNaf"] == "Fuera de Bodega")
                    {
                        $arrayParametrosNaf["strSerie"] = $objElemento["numeroSerie"];
                        $arrayResponsable               = $emNaf->getRepository('schemaBundle:InfoElemento')
                                                                ->getResponsableFueraBodega($arrayParametrosNaf);

                        $objElemento["responsable"] = $arrayResponsable["nombreResponsable"];
                    }
                    else if($objElemento["estadoNaf"] == "En Bodega")
                    {
                        $objElemento["ubicacion"] = 'EnBodega';
                    }
                }

                //Obtenemos el nombre del nodo en caso que la ubicación del elemento o dispositivo del cliente sea en el Nodo.
                $strNombreElementoNodo = '';
                if ($objElemento["ubicacion"] === 'Nodo')
                {
                    $arrayElementoAntecesor = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                            ->obtenerElementoAntecesor(array('strNumeroSerie'    => $objElemento["numeroSerie"],
                                                             'strNombreElemento' => $objElemento["descripcionElemento"],
                                                             'strEstado'         => 'Activo'));

                    if (isset($arrayElementoAntecesor['elementoIdB']) && !empty($arrayElementoAntecesor['elementoIdB']))
                    {
                        $objInfoElementoPrincipal  = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                ->find($arrayElementoAntecesor['elementoIdA']);
                        $strNombreElementoNodo = is_object($objInfoElementoPrincipal) ? $objInfoElementoPrincipal->getNombreElemento() : '';
                    }
                }

                $arrayEncontrados[] = array('estadoActivo'      => $objElemento["estadoActivo"],
                                            'estadoTelcos'      => $objElemento["estadoTelcos"],
                                            'estadoNaf'         => $objElemento["estadoNaf"],
                                            'descripcion'       => $objElemento["descripcionElemento"],
                                            'tipo'              => $objElemento["tipo"],
                                            'marca'             => $objElemento["marca"],
                                            'modelo'            => $objElemento["modelo"],
                                            'serie'             => $objElemento["numeroSerie"],
                                            'responsable'       => $objElemento["responsable"],
                                            'feCreacion'        => ($objElemento["feCreacion"] ?
                                                                    strval(date_format($objElemento["feCreacion"], "d-m-Y H:i")) : ""),
                                            'feCreacionNaf'     => $strFechaCreacionNaf,
                                            'feCreacionTelcos'  => $objElemento["feCreacionElemento"],
                                            'observacion'       => $objElemento["observacion"],
                                            'ubicacion'         => $objElemento["ubicacion"],
                                            'cliente'           => $objElemento["login"],
                                            'nombreNodo'        => $strNombreElementoNodo,
                                            'action1'           => $boolActualizarActivo ? 'button-grid-reconfigurarPuerto' : "icon-invisible");
            }
        }

        $arrayRespuesta["total"]       = $intNumeroRegistros;
        $arrayRespuesta["encontrados"] = $arrayEncontrados;

        $objResponse->setData($arrayRespuesta);

        return $objResponse;
    }


    /**
    * getValorAction
    * Obtiene los distintos criterio de busqueda para la auditoria de elemento
    *
    * @return json $objResponse
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 08-03-2018
    *
    * @author Germán Valenzuela <gvalenzuela@telconet.ec>
    * @version 1.1 20-07-2021 - Se agrega en los parámetros de consulta el id de la empresa en sesión.
	*/
    public function getValorAction()
    {
        $objPeticion        = $this->get('request');
        $strCriterio        = $objPeticion->query->get('criterio') ? $objPeticion->query->get('criterio') : "";
        $strContenidoValor  = $objPeticion->query->get('query') ? $objPeticion->query->get('query') : "";
        $emInfraestructura  = $this->getDoctrine()->getManager("telconet_infraestructura");
        $strIdEmpresa       = $objPeticion->getSession()->get('idEmpresa');
        $arrayEncontrados   = array();
        $arrayRespuesta     = array();
        $intNumeroRegistros = 0;
        $objResponse        = new JsonResponse();

        //Se obtiene las tareas configuradas para la tarea
        $arrayParametros["strValor"]     = $strContenidoValor;
        $arrayParametros["strCriterio"]  = $strCriterio;
        $arrayParametros["strIdEmpresa"] = $strIdEmpresa;
        $objCriterioBusqueda = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->getCriterioBusqueda($arrayParametros);

        $intNumeroRegistros = $objCriterioBusqueda["total"];

        if($intNumeroRegistros > 0)
        {
            foreach($objCriterioBusqueda["registros"] as $objCriterio)
            {
                $arrayEncontrados[] = array('idValor'     => $objCriterio["idValor"],
                                            'nombreValor' => $objCriterio["nombreValor"]);
            }
        }

        $arrayRespuesta["total"]       = $intNumeroRegistros;
        $arrayRespuesta["encontrados"] = $arrayEncontrados;

        $objResponse->setData($arrayRespuesta);

        return $objResponse;
    }



    /**
    * getResponsableAction
    * Obtiene los responsables
    *
    * @return json $objResponse
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 16-03-2018
	*/
    public function getResponsableAction()
    {
        $objPeticion        = $this->get('request');
        $strResponsable     = $objPeticion->query->get('query') ? $objPeticion->query->get('query') : "";
        $emInfraestructura  = $this->getDoctrine()->getManager("telconet_infraestructura");
        $arrayEncontrados   = array();
        $arrayRespuesta     = array();
        $intNumeroRegistros = 0;
        $objResponse        = new JsonResponse();

        //Se obtiene las tareas configuradas para la tarea
        $arrayParametros["strResponsable"] = $strResponsable;
        $arrayResponsables = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->getResponsable($arrayParametros);

        $intNumeroRegistros = $arrayResponsables["total"];

        if($intNumeroRegistros > 0)
        {
            foreach($arrayResponsables["registros"] as $arrayIdxResponsable)
            {
                $arrayEncontrados[] = array('idResponsable'     => $arrayIdxResponsable["idPersona"],
                                            'nombreResponsable' => $arrayIdxResponsable["nombres"]);
            }
        }

        $arrayRespuesta["total"]       = $intNumeroRegistros;
        $arrayRespuesta["encontrados"] = $arrayEncontrados;

        $objResponse->setData($arrayRespuesta);

        return $objResponse;
    }


    /**
    * getTrazabilidadElementoAction
    * Se ontiene la trazabilidad de un elemento
    *
    * @return json $objResponse
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 08-03-2018
	*/
    public function getTrazabilidadElementoAction()
    {
        $objPeticion       = $this->get('request');
        $emInfraestructura = $this->getDoctrine()->getManager("telconet_infraestructura");
        $strSerie          = $objPeticion->query->get('serie') ? $objPeticion->query->get('serie') : "";
        $strMarca          = $objPeticion->query->get('marca') ? $objPeticion->query->get('marca') : "";
        $strModelo         = $objPeticion->query->get('modelo') ? $objPeticion->query->get('modelo') : "";


        $arrayEncontrados    = array();
        $arrayRespuesta      = array();
        $intNumeroRegistros  = 0;
        $objResponse         = new JsonResponse();

        $arrayParametros["strNumeroSerie"] = $strSerie;

        $objElementos = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->getTrazabilidadElemento($arrayParametros);

        $intNumeroRegistros = count($objElementos);

        if($intNumeroRegistros > 0)
        {
            foreach($objElementos as $objElemento)
            {
                $arrayEncontrados[] = array('serie'             => $objElemento->getNumeroSerie(),
                                            'marca'             => $strMarca,
                                            'modelo'            => $strModelo,
                                            'estadoActivo'      => $objElemento->getEstadoActivo(),
                                            'estadoTelcos'      => $objElemento->getEstadoTelcos(),
                                            'estadoNaf'         => $objElemento->getEstadoNaf(),
                                            'ubicacion'         => $objElemento->getUbicacion(),
                                            'feCreacion'        => strval(date_format($objElemento->getFeCreacion(), "d-m-Y H:i")),
                                            'responsable'       => $objElemento->getResponsable(),
                                            'clienteTelcos'     => $objElemento->getLogin(),
                                            'observacion'       => $objElemento->getObservacion());
            }
        }

        $arrayRespuesta["total"]       = $intNumeroRegistros;
        $arrayRespuesta["encontrados"] = $arrayEncontrados;

        $objResponse->setData($arrayRespuesta);

        return $objResponse;
    }


    /**
    * exportarReporteAction
    * Funcion que exporta el reporte de la consulta de elementos
    *
    * @return Documento de Excel
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 08-03-2018
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.1 02-08-2018 Se realizan ajustes para consultar la descripcion,marca y modelo de las series que estan en la trazabilidad en
    *                         estado: PendienteInstalar y no se encuentran en telcos aun
    *
    * @author Germán Valenzuela <gvalenzuela@telconet.ec>
    * @version 1.2 21-04-2021 - Se agrega el manager de 'infraestructura' en los párametros para exportar.
	*/
    public function exportarReporteAction()
    {
        $objSession     = $this->get('session');
        $strCodEmpresa  = $objSession->get('idEmpresa');
        $objPeticion    = $this->get('request');
        $strUsrCreacion = $objSession->get('user');

        $emInfraestructura   = $this->getDoctrine()->getManager("telconet_infraestructura");
        $emNaf             = $this->getDoctrine()->getManager('telconet_naf');
        $strCriterio       = $objPeticion->query->get('criterio') ? $objPeticion->query->get('criterio') : "";
        $strValor          = $objPeticion->query->get('valor') ? $objPeticion->query->get('valor') : "";
        $intTipo           = $objPeticion->query->get('tipo') ? $objPeticion->query->get('tipo') : "";
        $intModeloElemento = $objPeticion->query->get('modelo') ? $objPeticion->query->get('modelo') : "";
        $strSerie          = $objPeticion->query->get('serie') ? $objPeticion->query->get('serie') : "";
        $strResponsable    = $objPeticion->query->get('responsable') ? $objPeticion->query->get('responsable') : "";
        $strUbicacion      = $objPeticion->query->get('ubicacion') ? $objPeticion->query->get('ubicacion') : "";
        $strEstado         = $objPeticion->query->get('estado') ? $objPeticion->query->get('estado') : "";
        $strNomrbreTipo    = $objPeticion->query->get('nombreTipo') ? $objPeticion->query->get('nombreTipo') : "";
        $strNombreModelo   = $objPeticion->query->get('nombreModelo') ? $objPeticion->query->get('nombreModelo') : "";
        $strFechaDesde     = $objPeticion->query->get('fechaDesde') ? $objPeticion->query->get('fechaDesde') : "";
        $strFechaHasta     = $objPeticion->query->get('fechaHasta') ? $objPeticion->query->get('fechaHasta') : "";
        $strOficina        = $objPeticion->query->get('nombreOficina') ? $objPeticion->query->get('nombreOficina') : "";
        $intOficina        = $objPeticion->query->get('idOficina') ? $objPeticion->query->get('idOficina') : "";

        //Se obtiene las tareas configuradas para la tarea
        $arrayParametros["intStart"]        = '';
        $arrayParametros["intLimit"]        = '';
        $arrayParametros["strCriterio"]     = $strCriterio;
        $arrayParametros["strValor"]        = $strValor;
        $arrayParametros["intModelo"]       = $intModeloElemento;
        $arrayParametros["intTipo"]         = $intTipo;
        $arrayParametros["strSerie"]        = $strSerie;
        $arrayParametros["strEstado"]       = $strEstado;
        $arrayParametros["strUsrCreacion"]  = $strUsrCreacion;
        $arrayParametros["strCodEmpresa"]   = $strCodEmpresa;
        $arrayParametros["strNombreTipo"]   = $strNomrbreTipo;
        $arrayParametros["strNombreModelo"] = $strNombreModelo;
        $arrayParametros["strResponsable"]  = $strResponsable;
        $arrayParametros["strUbicacion"]    = $strUbicacion;
        $arrayParametros["strFechaDesde"]   = $strFechaDesde;
        $arrayParametros["strFechaHasta"]   = $strFechaHasta;
        $arrayParametros["strOficina"]      = $strOficina;
        $arrayParametros["intOficina"]      = $intOficina;
        $arrayParametros["emNaf"]           = $emNaf;
        $arrayParametros["emInfraestructura"] = $emInfraestructura;

        $objElementos = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->getElementosAuditoria($arrayParametros);

        $arrayParametros["arrayRegistros"] = $objElementos["registros"];

        $this->generateExcelConsulta($arrayParametros);
    }


    /**
    * exportarTrazabilidadAction
    * Funcion que exporta el reporte de la trazabilidad del elemento
    *
    * @return Documento de Excel
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 08-03-2018
    *
    * @author Germán Valenzuela <gvalenzuela@telconet.ec>
    * @version 1.1 21-04-2021 - Se recibe el nuevo atributo 'nombreNodo', para enviarlo junto a
    *                           los parámetros de exportar.
	*/
    public function exportarTrazabilidadAction()
    {
        $objSession          = $this->get('session');
        $objPeticion         = $this->get('request');
        $strUsrCreacion      = $objSession->get('user');

        $emInfraestructura      = $this->getDoctrine()->getManager("telconet_infraestructura");
        $strSerie               = $objPeticion->query->get('serie') ? $objPeticion->query->get('serie') : "";
        $strNombreNodo          = $objPeticion->query->get('nombreNodo') ? $objPeticion->query->get('nombreNodo') : "";
        $strTipo                = $objPeticion->query->get('tipo') ? $objPeticion->query->get('tipo') : "";
        $strMarca               = $objPeticion->query->get('marca') ? $objPeticion->query->get('marca') : "";
        $strModelo              = $objPeticion->query->get('modelo') ? $objPeticion->query->get('modelo') : "";
        $strDescripcion         = $objPeticion->query->get('descripcion') ? $objPeticion->query->get('descripcion') : "";
        $strfechaCreacionNaf    = $objPeticion->query->get('fechaCreacionNaf') ? $objPeticion->query->get('fechaCreacionNaf') : "";
        $strfechaCreacionTelcos = $objPeticion->query->get('fechaCreacionTelcos') ? $objPeticion->query->get('fechaCreacionTelcos') : "";

        //Se obtiene la trazabilidad del elemento
        $objElementos = $emInfraestructura->getRepository('schemaBundle:InfoElementoTrazabilidad')->findBy(array("numeroSerie" => $strSerie));

        $arrayParametros["arrayRegistros"]          = $objElementos;
        $arrayParametros["strSerie"]                = $strSerie;
        $arrayParametros["strTipo"]                 = $strTipo;
        $arrayParametros["strMarca"]                = $strMarca;
        $arrayParametros["strModelo"]               = $strModelo;
        $arrayParametros["strFechaCreacionTelcos"]  = $strfechaCreacionTelcos;
        $arrayParametros["strFechaCreacionNaf"]     = $strfechaCreacionNaf;
        $arrayParametros["strDescripcion"]          = $strDescripcion;
        $arrayParametros["strUsrCreacion"]          = $strUsrCreacion;
        $arrayParametros["strNombreNodo"]           = $strNombreNodo;

        $this->generateExcelTrazabilidad($arrayParametros);
    }


    /**
    * generateExcelConsulta
    * Funcion que genera el excel para el reporte de consulta de elementos
    *
    * @param array $arrayParametros[ "arrayRegistros"   => array de registros a generarse
    *                                "strCriterio"      => filtro criterio de busqueda Cliente seleccionado
    *                                "strValor"         => filtro valor ingresado
    *                                "intModelo"        => filtro modelo de elemento seleccionado
    *                                "intTipo"          => filtro tipo de elemento seleccionado
    *                                "strSerie"         => filtro serie ingresado
    *                                "strEstado"        => estado del telcos
    *                                "strUsrCreacion"   => usuario en sesion
    *                                "strFechaDesde"    => fecha inicio de la trazabilidad
    *                                "strFechaHasta"    => fecha fin de la trazabilidad
    *                                "strOficina"       => oficina del cliente
    *                                "strCodEmpresa"    => codigo de la empresa en sesion
    *                                "strNombreTipo"    => nombre del tipo de elemento seleccionado
    *                                "strNombreModelo"  => nombre del modelo elemento seleccionado
    *                                "strResponsable"   => filtro responsable ingresado
    *                                "strUbicacion"     => filtro ubicacion seleccionado ]
    *
    * @return Documento de Excel
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 08-03-2018
    *
    * @author Germán Valenzuela <gvalenzuela@telconet.ec>
    * @version 1.1 20-04-2021 - Se agrega un nuevo método, para obtener el nombre del NODO de los elementos
    *                           que se encuentra fisicamentee en el nodo.
	*/
    public static function generateExcelConsulta($arrayParametros)
    {
        $objPHPExcel       = new PHPExcel();
        $emInfraestructura = $arrayParametros['emInfraestructura'];
        $objCacheMethod    = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $objCacheSettings  = array( ' memoryCacheSize ' => '1024MB');

        PHPExcel_Settings::setCacheStorageMethod($objCacheMethod, $objCacheSettings);

        $objReader     = PHPExcel_IOFactory::createReader('Excel5');
        $objPHPExcel   = $objReader->load(__DIR__."/../Resources/templatesExcel/templateAuditoriaElementos.xls");

        // Set properties
        $objPHPExcel->getProperties()->setCreator("TELCOS++");
        $objPHPExcel->getProperties()->setLastModifiedBy($arrayParametros["strUsrCreacion"]);
        $objPHPExcel->getProperties()->setTitle("Reporte Auditoria de Elementos");
        $objPHPExcel->getProperties()->setSubject("Reporte Auditoria de Elementos");
        $objPHPExcel->getProperties()->setDescription("Resultado de busqueda de reporte (Reporte Auditoria de Elementos).");
        $objPHPExcel->getProperties()->setKeywords("Tecnico");
        $objPHPExcel->getProperties()->setCategory("Reporte");

        $objPHPExcel->getActiveSheet()->setCellValue('C3',$arrayParametros["strUsrCreacion"]);
        $objPHPExcel->getActiveSheet()->setCellValue('C4', strval(date_format(new \DateTime('now'), "d/m/Y  H:i")) );
        $objPHPExcel->getActiveSheet()->getStyle('C4')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);
        $objPHPExcel->getActiveSheet()->setCellValue('B8',''.$arrayParametros["strCriterio"]);
        $objPHPExcel->getActiveSheet()->setCellValue('B9',''.$arrayParametros["strNombreTipo"]);
        $objPHPExcel->getActiveSheet()->setCellValue('B10',''.$arrayParametros["strSerie"]);
        $objPHPExcel->getActiveSheet()->setCellValue('B11',''.$arrayParametros["strUbicacion"]);
        $objPHPExcel->getActiveSheet()->setCellValue('D8',''.$arrayParametros["strValor"]);
        $objPHPExcel->getActiveSheet()->setCellValue('D9',''.$arrayParametros["strNombreModelo"]);
        $objPHPExcel->getActiveSheet()->setCellValue('D10',''.$arrayParametros["strEstado"]);
        $objPHPExcel->getActiveSheet()->setCellValue('D11',''.$arrayParametros["strResponsable"]);
        $objPHPExcel->getActiveSheet()->setCellValue('B12',''.$arrayParametros["strFechaDesde"]);
        $objPHPExcel->getActiveSheet()->setCellValue('B13',''.$arrayParametros["strFechaHasta"]);
        $objPHPExcel->getActiveSheet()->setCellValue('D12',''.$arrayParametros["strOficina"]);

        $i=16;
        foreach ($arrayParametros["arrayRegistros"] as $arrayElemento)
        {
            //Obtenemos el nombre del nodo en caso que la ubicación del elemento o dispositivo del cliente sea en el Nodo.
            $strNombreElementoNodo = '';
            if ($arrayElemento["ubicacion"] === 'Nodo')
            {
                $arrayElementoAntecesor = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                        ->obtenerElementoAntecesor(array('strNumeroSerie'    => $arrayElemento["numeroSerie"],
                                                         'strNombreElemento' => $arrayElemento["descripcionElemento"],
                                                         'strEstado'         => 'Activo'));

                if (isset($arrayElementoAntecesor['elementoIdB']) && !empty($arrayElementoAntecesor['elementoIdB']))
                {
                    $objInfoElementoPrincipal  = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                            ->find($arrayElementoAntecesor['elementoIdA']);
                    $strNombreElementoNodo = is_object($objInfoElementoPrincipal) ? $objInfoElementoPrincipal->getNombreElemento() : '';
                }
            }

            $objPHPExcel->getActiveSheet()->setCellValue('A'.$i, trim($arrayElemento["numeroSerie"]));
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, trim($arrayElemento["descripcionElemento"]));
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$i, trim($arrayElemento["tipo"]));
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, trim($arrayElemento["marca"]));
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, trim($arrayElemento["modelo"]));
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$i, trim($arrayElemento["estadoActivo"]));
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$i, trim($arrayElemento["estadoTelcos"]));
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$i, trim($arrayElemento["estadoNaf"]));
            $objPHPExcel->getActiveSheet()->setCellValue('I'.$i, strval(date_format($arrayElemento["feCreacion"], "d-m-Y H:i")));
            $objPHPExcel->getActiveSheet()->setCellValue('J'.$i, trim($arrayElemento["ubicacion"]));
            $objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $strNombreElementoNodo);
            $objPHPExcel->getActiveSheet()->setCellValue('L'.$i, trim($arrayElemento["responsable"]));
            $objPHPExcel->getActiveSheet()->setCellValue('M'.$i, trim($arrayElemento["login"]));

            $i=$i+1;
        }

        // Merge cells
        // Set document security
        $objPHPExcel->getSecurity()   ->setWorkbookPassword("PHPExcel");

        // Set page orientation and size
        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle('Reporte');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        //Redirect output to a clients web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Reporte_Auditoria_Elementos.xls"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter ->save('php://output');
    }


    /**
    * generateExcelTrazabilidad
    * Funcion que genera el excel de la trazabilidad de un elemento
    *
    * @param array $arrayParametros[ "arrayRegistros"           => array de registros a generarse
    *                                "strSerie"                 => serie del elemento
    *                                "strTipo"                  => tipo de elemento
    *                                "strMarca"                 => marca del elemento
    *                                "strModelo"                => modelo del elemento
    *                                "strFechaCreacionTelcos"   => fecha de creacion del telcos
    *                                "strFechaCreacionNaf"      => fecha de creacion del naf
    *                                "strDescripcion"           => descripcion del elemento
    *                                "strUsrCreacion"           => usuario en sesion ]
    *
    * @return Documento de Excel
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 08-03-2018
    *
    * @author Germán Valenzuela <gvalenzuela@telconet.ec>
    * @version 1.1 20-04-2021 - Se agrega el parámetro 'nombreNodo' para detectar los elementos que se
    *                           encuentran fisicamente en el nodo.
	*/
    public static function generateExcelTrazabilidad($arrayParametros)
    {
        $objPHPExcel      = new PHPExcel();
        $objCacheMethod   = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $objCacheSettings = array( ' memoryCacheSize ' => '1024MB');

        PHPExcel_Settings::setCacheStorageMethod($objCacheMethod, $objCacheSettings);

        $objReader     = PHPExcel_IOFactory::createReader('Excel5');
        $objPHPExcel   = $objReader->load(__DIR__."/../Resources/templatesExcel/templateTrazabilidadElemento.xls");

        // Set properties
        $objPHPExcel->getProperties()->setCreator("TELCOS++");
        $objPHPExcel->getProperties()->setLastModifiedBy($arrayParametros["strUsrCreacion"]);
        $objPHPExcel->getProperties()->setTitle("Reporte Tazabilidad Elemento");
        $objPHPExcel->getProperties()->setSubject("Reporte Trazabilad de Elemento");
        $objPHPExcel->getProperties()->setDescription("Resultado de busqueda de reporte (Reporte Trazabidad de Elemento).");
        $objPHPExcel->getProperties()->setKeywords("Tecnico");
        $objPHPExcel->getProperties()->setCategory("Reporte");

        $objPHPExcel->getActiveSheet()->setCellValue('B3',($arrayParametros["strUsrCreacion"]));
        $objPHPExcel->getActiveSheet()->setCellValue('B4', strval(date_format(new \DateTime('now'), "d/m/Y  H:i")) );
        $objPHPExcel->getActiveSheet()->getStyle('B4')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);
        $objPHPExcel->getActiveSheet()->setCellValue('B8',''.$arrayParametros["strSerie"]);
        $objPHPExcel->getActiveSheet()->setCellValue('B9',''.$arrayParametros["strModelo"]);
        $objPHPExcel->getActiveSheet()->setCellValue('B10',''.$arrayParametros["strFechaCreacionNaf"]);
        $objPHPExcel->getActiveSheet()->setCellValue('B11',''.$arrayParametros["strDescripcion"]);
        $objPHPExcel->getActiveSheet()->setCellValue('D8',''.$arrayParametros["strTipo"]);
        $objPHPExcel->getActiveSheet()->setCellValue('D9',''.$arrayParametros["strMarca"]);
        $objPHPExcel->getActiveSheet()->setCellValue('D10',''.$arrayParametros["strFechaCreacionTelcos"]);
        $objPHPExcel->getActiveSheet()->setCellValue('D11',''.$arrayParametros["strNombreNodo"]);

        $i=15;
        foreach ($arrayParametros["arrayRegistros"] as $arrayElemento)
        {
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, trim($arrayElemento->getEstadoActivo()));
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, trim($arrayElemento->getEstadoTelcos()));
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$i, trim($arrayElemento->getEstadoNaf()));
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, trim($arrayElemento->getUbicacion()));
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, trim($arrayElemento->getResponsable()));
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$i, trim($arrayElemento->getLogin()));
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$i, strval(date_format($arrayElemento->getFeCreacion(), "d-m-Y H:i")));
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$i, trim($arrayElemento->getObservacion()));

            $i=$i+1;
        }

        // Merge cells
        // Set document security
        $objPHPExcel->getSecurity()   ->setWorkbookPassword("PHPExcel");

        // Set page orientation and size
        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle('Reporte');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        //Redirect output to a clients web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Reporte_Trazailidad_Elemento.xls"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter ->save('php://output');
    }



    /**
    * actualizarEstadoAction
    * Funcion que actualiza un elemento
    *
    * @return json $objResponse
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 08-03-2018
	*/
    public function actualizarEstadoAction()
    {
        $emInfraestructura   = $this->getDoctrine()->getManager('telconet_infraestructura');
        $objPeticion         = $this->getRequest();
        $objSession          = $objPeticion->getSession();
        $strUserSession      = $objSession->get('user');
        $strIpCreacion       = $objPeticion->getClientIp();
        $strCodEmpresa       = $objSession->get('idEmpresa');
        $strSerie            = $objPeticion->get('serie');
        $strEstadoElemento   = $objPeticion->get('nuevoEstado');
        $strObservacion      = $objPeticion->get('observacion');
        $strEstadoActual     = $objPeticion->get('estadoActivo');
        $strUbicacion        = "";
        $objResponse         = new JsonResponse();
        $arrayRespuesta      = array();
        $strMensajeRespuesta = "";
        $serviceUtil         = $this->get('schema.Util');

        $emInfraestructura->getConnection()->beginTransaction();

        try
        {
            if(($strEstadoActual == "EnOficinaMd" && $strEstadoElemento == "Perdido") ||
                ($strEstadoActual == "EnTransito" && $strEstadoElemento == "EnOficinaMd"))
            {
                $strMensajeRespuesta = "No tiene permitido cambiar a estado:  ".$strEstadoElemento;
            }
            else
            {
                $objInfoElementoTrazabilidad = $emInfraestructura->getRepository('schemaBundle:InfoElementoTrazabilidad')
                                                                 ->findOneBy(array("numeroSerie" => $strSerie));

                if(is_object($objInfoElementoTrazabilidad))
                {
                    $objElementoTrazabilidad = new InfoElementoTrazabilidad();
                    $objElementoTrazabilidad->setNumeroSerie($objInfoElementoTrazabilidad->getNumeroSerie());
                    $objElementoTrazabilidad->setEstadoTelcos($objInfoElementoTrazabilidad->getEstadoTelcos());
                    $objElementoTrazabilidad->setEstadoNaf($objInfoElementoTrazabilidad->getEstadoNaf());
                    $objElementoTrazabilidad->setEstadoActivo($strEstadoElemento);

                    if($strEstadoElemento == "EnOficinaMd")
                    {
                        $strUbicacion = "EnOficina";
                    }
                    if($strEstadoElemento == "Perdido")
                    {
                        $strUbicacion = "EnTransito";
                    }

                    $objElementoTrazabilidad->setUbicacion($strUbicacion);
                    $objElementoTrazabilidad->setLogin($objInfoElementoTrazabilidad->getLogin());

                    $objInfoPersona = $emInfraestructura->getRepository('schemaBundle:InfoPersona')->findOneBy(array("login" => $strUserSession));

                    if(is_object($objInfoPersona))
                    {
                        $objElementoTrazabilidad->setResponsable($objInfoPersona->__toString());
                    }

                    $objElementoTrazabilidad->setCodEmpresa($strCodEmpresa);
                    $objElementoTrazabilidad->setObservacion($strObservacion);
                    $objElementoTrazabilidad->setOficinaId(0);
                    $objElementoTrazabilidad->setTransaccion("Actualizar Estado");
                    $objElementoTrazabilidad->setUsrCreacion($objSession->get('user'));
                    $objElementoTrazabilidad->setFeCreacion(new \DateTime('now'));
                    $objElementoTrazabilidad->setIpCreacion($objPeticion->getClientIp());
                    $emInfraestructura->persist($objElementoTrazabilidad);
                    $emInfraestructura->flush();
                }

                $emInfraestructura->getConnection()->commit();

                $strMensajeRespuesta = "Elemento Actualizado Correctamente";
            }

            $arrayRespuesta["estado"]  = "Ok";
            $arrayRespuesta["mensaje"] = $strMensajeRespuesta;
        }
        catch(\Exception $ex)
        {
            if($emInfraestructura->isTransactionActive())
            {
                $emInfraestructura->rollback();
            }

            $emInfraestructura->close();

            $serviceUtil->insertError('Telcos+',
                                      'InfoElementoCpeController->actualizarEstadoAction',
                                      $ex->getMessage(),
                                      $strUserSession,
                                      $strIpCreacion);

            $arrayRespuesta["estado"]  = "Ok";
            $arrayRespuesta["mensaje"] = "Error en la transaccion";
        }

        $objResponse->setData($arrayRespuesta);

        return $objResponse;
    }


    /**
    * ingresarTrazabilidadAction
    * Funcion que permite ingresar una trazabilidad
    *
    * @return json $objResponse
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 16-03-2018
	*/
    public function ingresarTrazabilidadAction()
    {
        $emInfraestructura   = $this->getDoctrine()->getManager('telconet_infraestructura');
        $objPeticion         = $this->getRequest();
        $objSession          = $objPeticion->getSession();
        $strUserSession      = $objSession->get('user');
        $strIpCreacion       = $objPeticion->getClientIp();
        $strCodEmpresa       = $objSession->get('idEmpresa');
        $strObservacion      = $objPeticion->get('observacion');
        $strSerie            = $objPeticion->get('serie');
        $objResponse         = new JsonResponse();
        $arrayRespuesta      = array();
        $serviceUtil         = $this->get('schema.Util');

        $emInfraestructura->getConnection()->beginTransaction();

        try
        {
            $objInfoElementoTrazabilidad = $emInfraestructura->getRepository('schemaBundle:InfoElementoTrazabilidad')
                                                             ->findOneBy(array("numeroSerie" => $strSerie));

            if(is_object($objInfoElementoTrazabilidad))
            {
                $objElementoTrazabilidad = new InfoElementoTrazabilidad();
                $objElementoTrazabilidad->setNumeroSerie($strSerie);
                $objElementoTrazabilidad->setEstadoTelcos($objInfoElementoTrazabilidad->getEstadoTelcos());
                $objElementoTrazabilidad->setEstadoNaf($objInfoElementoTrazabilidad->getEstadoNaf());
                $objElementoTrazabilidad->setEstadoActivo($objInfoElementoTrazabilidad->getEstadoActivo());
                $objElementoTrazabilidad->setUbicacion($objInfoElementoTrazabilidad->getUbicacion());
                $objElementoTrazabilidad->setLogin($objInfoElementoTrazabilidad->getLogin());

                $objInfoPersona = $emInfraestructura->getRepository('schemaBundle:InfoPersona')->findOneBy(array("login" => $strUserSession));

                if(is_object($objInfoPersona))
                {
                    $objElementoTrazabilidad->setResponsable($objInfoPersona->__toString());
                }

                $objElementoTrazabilidad->setCodEmpresa($strCodEmpresa);
                $objElementoTrazabilidad->setObservacion($strObservacion);
                $objElementoTrazabilidad->setOficinaId(0);
                $objElementoTrazabilidad->setTransaccion("Ingresar Trazabilidad");
                $objElementoTrazabilidad->setUsrCreacion($strUserSession);
                $objElementoTrazabilidad->setFeCreacion(new \DateTime('now'));
                $objElementoTrazabilidad->setIpCreacion($objPeticion->getClientIp());
                $emInfraestructura->persist($objElementoTrazabilidad);
                $emInfraestructura->flush();
            }

            $emInfraestructura->getConnection()->commit();

            $arrayRespuesta["estado"]  = "Ok";
            $arrayRespuesta["mensaje"] = "Trazabilidad ingresada Exitosamente";
        }
        catch(\Exception $ex)
        {
            if($emInfraestructura->isTransactionActive())
            {
                $emInfraestructura->rollback();
            }

            $emInfraestructura->close();

            $serviceUtil->insertError('Telcos+',
                                      'InfoElementoCpeController->ingresarTrazabilidadAction',
                                      $ex->getMessage(),
                                      $strUserSession,
                                      $strIpCreacion);

            $arrayRespuesta["estado"]  = "Ok";
            $arrayRespuesta["mensaje"] = "Error en la transaccion";
        }

        $objResponse->setData($arrayRespuesta);

        return $objResponse;
    }

    /**
     * Funcion que consulta los cpes tanto en 
     * Telcos como en el Naf, con sus respectivos estados.
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-05-2014
     */
    public function getEncontradosCpeAction(){
       $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $session = $this->get('session');
        $session->save();
        session_write_close();
        
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emNaf = $this->getDoctrine()->getManager('telconet_naf');
        $emComercial = $this->getDoctrine()->getManager('telconet');
        $tipoElemento = "CPE";
        
        $peticion = $this->get('request');
        
        $serial = $peticion->query->get('serial');
        $modeloElemento = $peticion->query->get('modeloElemento');
        $idEmpresa = $session->get('idEmpresa');
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:InfoElemento')
            ->generarJsonCpes($modeloElemento,$tipoElemento,strtoupper($serial),$start,$limit,
                              $em,$idEmpresa,$emNaf,$emComercial);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }

    /**
     * Funcion que retorna data tecnica del cpe, como: 
     * serie elemento, nombre, mac,ip, custodio,  
     * estado en Telcos como en Naf.
     * 
     * @author Jenniffer Mujica <jmujica@telconet.ec>
     * @version 1.0 28-09-2022
     */
    public function getDataTecnicaCpeAction()
    {
        $objRequest    = $this->get('request'); 
        $objResponse = new JsonResponse();             
        $emInfraestructura = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emComercial = $this->get('doctrine')->getManager("telconet");
        $emNaf = $this->get('doctrine')->getManager('telconet_naf');
        $strStart = $objRequest->query->get('start');
        $strLimit = $objRequest->query->get('limit');

        $strIdEmpresa = $objRequest->get('idEmpresa');
        $strIdServicio = $objRequest->get('idServicio');
        $strPrefijoEmpresa = $objRequest->get('prefijoEmpresa');
                
        $objCpe = $emInfraestructura->getRepository('schemaBundle:InfoElemento');
        $serviceDataTecnica  = $this->get('tecnico.DataTecnica');
        
        $arrayParams  = array(); 
        $arrayElementoCpe = array();           

        $arrayPeticiones = array('idServicio'    => $strIdServicio,
                                 'idEmpresa'     => $strIdEmpresa,
                                 'prefijoEmpresa'=> $strPrefijoEmpresa);

        //obtengo data tecnica   
        $arrayDataTecnica = $serviceDataTecnica->getDataTecnica($arrayPeticiones);        

        if($arrayDataTecnica)
        {
            //obtengo info del elemento Cpe 
            $objElementoCpe = $arrayDataTecnica['elementoCpe'];
            
            //obtengo interface elemento cliente 
            $objInterfaceElementoCliente = $arrayDataTecnica['interfaceElementoCliente'];

            if(isset($objElementoCpe))
            {
                $strSerie  = $objElementoCpe->getSerieFisica();
                $strModeloElementoId = $objElementoCpe->getModeloElementoId();
                $strTipoElemento = $objElementoCpe->getModeloElementoId()->getTipoElementoId()->getNombreTipoElemento();

                if($strTipoElemento == 'CPE' || $strTipoElemento == 'ROUTER' )
                {
                    $objJson = $objCpe->generarJsonCpes($strModeloElementoId,$strTipoElemento,
                                                    strtoupper($strSerie),$strStart,$strLimit,
                                                    $emInfraestructura,$strIdEmpresa,
                                                    $emNaf,$emComercial);
                }
                
                //Obtengo estados en naf, telcos y custodio
                $arrayData = json_decode($objJson, true);
                if($arrayData['total'] != 0)
                {
                    $arrayDataCpe                    = $arrayData['encontrados'][0];
                    $arrayParams['estadoNaf']        = $arrayDataCpe['estadoNaf'] == 'NA' ? '' : $arrayDataCpe['estadoNaf'];
                    $arrayParams['macCpe']           = $arrayDataCpe['macCpeNaf']; 
                    $arrayParams['custodio']         = $arrayDataCpe['responsable'] == 'NA' ? '' :$arrayDataCpe['responsable'];
                    $arrayParams['usuarioCustodio']  = $arrayDataCpe['loginResponsable'];
                }
                $arrayParams['estadoTelcos']          = $objElementoCpe->getestado();
                $arrayParams['idElemento']            = $objElementoCpe->getId();
                $arrayParams['nombreElemento']        = $objElementoCpe->getNombreElemento();
            }
            
            $arrayParams['serieCpe']         = $strSerie;
            $arrayParams['idInterface']      = $objInterfaceElementoCliente->getId();
            $arrayParams['ipCpe']            = $arrayDataTecnica['ipCpe'] ? $arrayDataTecnica['ipCpe'] : 'N/A';
        }
                                       
        $objResponse->setData($arrayParams);
        
        return $objResponse; 
    }

    /**
     * Funcion que sirve para ejecutar la actualizacion
     * de data tecnica del cpe, como: 
     * mac, custodio, estado en Naf.
     * 
     * @author Jenniffer Mujica <jmujica@telconet.ec>
     * @version 1.0 04-10-2022
     */
    public function actualizarDataTecnicaCpeAction()
    {
        $objJsonResponse = new JsonResponse();
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        $objRequest        = $this->get('request');
        $objSession        = $objRequest->getSession();
        $strUsrCreacion    = $objSession->get('user');
        $strIpCreacion     = $objRequest ->getClientIp();
        $intIdEmpresa      = $objRequest ->get('idEmpresa');
        $strSerie          = $objRequest->get('serie');
        $strSerieNueva     = $objRequest->get('serieNueva');
        $intIdServicio     = $objRequest->get('idServicio');
        $intIdElemento     = $objRequest->get('idElemento');
        $strMacAnterior    = $objRequest->get('macAnterior');
        $strMacNueva       = $objRequest->get('macNueva');
        $strEstadoTelcos   = $objRequest->get('estado');
        $strNafAnterior    = $objRequest->get('nafAnterior');
        $strNafNueva       = $objRequest->get('nafNueva');
        $intIdResponsable  = $objRequest->get('idResponsable');
        $intIdInterface    = $objRequest->get('idInterface');

        $arrayPeticiones[] = array( 'idServicio'             => $intIdServicio,
                                    'idEmpresa'              => $intIdEmpresa,
                                    'idElemento'             => $intIdElemento,
                                    'idInterface'            => $intIdInterface,
                                    'idResponsable'          => $intIdResponsable,
                                    'macCpe'                 => $strMacAnterior,
                                    'macCpeNUeva'            => $strMacNueva,
                                    'serieCpe'               => $strSerie,
                                    'serieCpeNueva'          => $strSerieNueva,
                                    'estadoTelcos'           => $strEstadoTelcos,
                                    'usrCreacion'            => $strUsrCreacion,
                                    'ipCreacion'             => $strIpCreacion,
                                    'estadoNaf'              => $strNafAnterior,
                                    'estadoNafNUevo'         => $strNafNueva,
                                    'empleadoSesion'         => $objSession->get('empleado')
                                  );
        
        $arrayRespuesta      = array();

        try 
        {
            /* @var $serviceCambioDataTecnica DataTecnicaService */
            $serviceCambioDataTecnica = $this->get('tecnico.DataTecnica');

            if($strNafAnterior == 'Instalado' && $strEstadoTelcos == 'Activo' && $strNafNueva != '')
            {
                $strStatus  = "ERROR";
                $strMensaje = "No se puede actualizar dicha informacion, Estado:  ".$strEstadoTelcos;
            } 
            else 
            {
                $arrayRespuesta = $serviceCambioDataTecnica->cambioDataTecnica($arrayPeticiones);
                $strStatus  = $arrayRespuesta['status'];
                $strMensaje = $arrayRespuesta['message']; 
            }
        } 
        catch (Exception $e) 
        {            
            $strStatus  = "ERROR";
            $strMensaje = 'Se presentaron errores al realizar la actualizacion del elemento cpe.';
            $serviceUtil->insertError('Telcos+', 
                                      'InfoElementoCpeController.actualizarDataTecnicaCpeAction', 
                                      $e->getMessage(), 
                                      $strUsrCreacion, 
                                      $strIpCreacion
                                     );
        }
                        
        $objJsonResponse->setData(array('status' => $strStatus, 'mensaje' => $strMensaje));
        
        return $objJsonResponse;

    }
    
}
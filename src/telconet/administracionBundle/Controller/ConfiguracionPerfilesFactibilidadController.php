<?php

namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolCarac;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ConfiguracionPerfilesFactibilidadController extends Controller implements TokenAuthenticatedController
{
    public function indexAction()
    {
        $objPeticion = $this->get('request');
        $objSession = $objPeticion->getSession();

        $strCodEmpresa = ($objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : "");
        $strPrefijoEmpresa = ($objSession->get('prefijoEmpresa') ? $objSession->get('prefijoEmpresa') : "");

        $arrayRolesPermitidos = array();
        $emSeguridad          = $this->getDoctrine()->getManager('telconet_seguridad');
        //Módulo 479 - configuracionPerfilFactibilidad
        if (true === $this->get('security.context')->isGranted('ROLE_481-1')) 
        {
            $arrayRolesPermitidos[] = 'ROLE_481-1'; 
            //481 produccion
        }
        if (true === $this->get('security.context')->isGranted('ROLE_481-7')) 
        {
            $arrayRolesPermitidos[] = 'ROLE_481-7';
        }


        $entityItemMenu = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("481", "1");

        return $this->render(
            'administracionBundle:ConfiguracionPerfilesFactibilidad:index.html.twig',
            array('item'            => $entityItemMenu,
                  'rolesPermitidos' => $arrayRolesPermitidos,
                  'codEmpresa'    => $strCodEmpresa,
                  'prefijoEmpresa' => $strPrefijoEmpresa)
        );
    }


    public function gridAction()
    {
        $objPeticion        = $this->get('request');
        $emInfraestructura  = $this->getDoctrine()->getManager("telconet_infraestructura");
        $strEstadoElemento  = $objPeticion->get('estado');
        $strNombreElemento  = $objPeticion->get('nombreElemento') ? $objPeticion->get('nombreElemento') : "";
        $strModeloElemento  = $objPeticion->get('modeloElemento') ? $objPeticion->get('modeloElemento') : "";
        $intZona            = $objPeticion->get('zona') ? $objPeticion->get('zona') : "";
        $strTipoElemento    = $objPeticion->get('tipoElemento');
        $intStart           = $objPeticion->query->get('start');
        $intLimit           = $objPeticion->query->get('limit');
        $arrayEncontrados   = array();
        $arrayRespuesta     = array();
        $strZona            = "";
        $objResponse        = new JsonResponse();

        $arrayParametros["intStart"]          = $intStart;
        $arrayParametros["intLimit"]          = $intLimit;
        $arrayParametros["strEstadoElemento"] = $strEstadoElemento;
        $arrayParametros["strNombreElemento"] = $strNombreElemento;
        $arrayParametros["strModeloElemento"] = $strModeloElemento;
        $arrayParametros["strTipoElemento"]   = $strTipoElemento;
        $arrayParametros["intZona"]           = $intZona;

        $arrayElementos = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->getElementos($arrayParametros);

        if ($arrayElementos["intTotal"] > 0) 
        {
            foreach ($arrayElementos["arrayRegistros"] as $arrayIdxElemento) 
            {

                //Se obtiene la Zona del elemento
                $arrayParametros["strIdElemento"] = $arrayIdxElemento["id"];
                $strZona = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->getZonaPorElemento($arrayParametros);

                //Se obtiene el objeto elemento
                $objInfoElemento = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($arrayIdxElemento["id"]);

                if (is_object($objInfoElemento)) 
                {
                    //Se obtiene el modelo del elemento
                    $strNombreModelo = "";
                    $strNombreModelo = $objInfoElemento->getModeloElementoId()->getNombreModeloElemento();

                    //Se obtiene el tipo del elemento
                    $strNombreTipo = "";
                    $strNombreTipo = $objInfoElemento->getModeloElementoId()->getTipoElementoId()->getNombreTipoElemento();
                }

                $arrayEncontrados[] = array('id_elemento'     => $arrayIdxElemento["id"],
                                            'nombre_elemento' => $arrayIdxElemento["nombreElemento"],
                                            'nombre_zona'     => $strZona["nombreZona"] ? $strZona["nombreZona"] : "",
                                            'estado'          => $arrayIdxElemento["estado"],
                                            'nombre_tipo'     => $strNombreTipo,
                                            'nombre_modelo'   => $strNombreModelo);
            }
        }

        $arrayRespuesta["total"]       = $arrayElementos["intTotal"];
        $arrayRespuesta["encontrados"] = $arrayEncontrados;

        $objResponse->setData($arrayRespuesta);

        return $objResponse;
    }

    public function enviarABaseAction()
    {
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');

        $objPeticion = $this->get('request');

        $objSession = $objPeticion->getSession();

        $emComercial = $this->getDoctrine()->getManager('telconet');
        $emComercial->getConnection() ->beginTransaction();
        
        $serviceUtil  = $this->get('schema.Util');
        
        $arrayEmpleados = json_decode($objPeticion->get('array'));

        $arrayEliminados = json_decode($objPeticion->get('arrayEliminados'));

        $intIdDepartamento = $objPeticion->get('id_departamento');

        $objDepartamento = $emComercial->getRepository("schemaBundle:AdmiDepartamento")->find($intIdDepartamento);

        $objEmpresa = $emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneById($objDepartamento->getEmpresaCod());

        $strEmpresaGrupo = ($objEmpresa ? ($objEmpresa->getNombreEmpresa() ? $objEmpresa->getNombreEmpresa()  : '') : '');

        $arrayUsuarios = $objPeticion->get('arrayUsuarios');
        if (count($arrayEmpleados) > 0) 
        {
            try 
            {
                $strCaracteristica    = 'CONFIGURACION_PERFILES_FACTIBILIDAD';
                $strEstado            = 'Activo';
                $entityCaracteristica = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                ->getCaracteristicaPorDescripcionPorEstado($strCaracteristica, $strEstado);
                $arrayOfRecords = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                ->findInfoPersonaEmpresaRolCaracByListOfPersonAndCabecera($arrayUsuarios, $entityCaracteristica->getId());

                $arrayRegistros = array();
                foreach ($arrayOfRecords as $registro) 
                {
                    $arrayRegistros[] = array("id_jurisdiccion" => intval($registro->getValor()),
                                        "id_usuario" => $registro->getPersonaEmpresaRolId()->getId());
                }

                foreach ($arrayEmpleados as $empleado) 
                {
                    if (!$this->containsElement($arrayRegistros, $empleado)) 
                    {
                        $objPersonaEmpresaRolCaracId = $empleado->id_empleado;
                        $intIdEmpleado = explode("@@", $objPersonaEmpresaRolCaracId)[1];
                        $objInfoPersonaEmpresaRolCarac = new InfoPersonaEmpresaRolCarac();
                        $objUsrCreacion = $objSession->get('user');
                        $intJurisdiccion = $empleado->id_jurisdiccion;
                        $objInfoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($intIdEmpleado);
                        $strIpCreacion = $objPeticion -> getClientIp();
                        $objInfoPersonaEmpresaRolCarac->setCaracteristicaId($entityCaracteristica);
                        $objInfoPersonaEmpresaRolCarac->setPersonaEmpresaRolId($objInfoPersonaEmpresaRol);
                        $objInfoPersonaEmpresaRolCarac->setValor($intJurisdiccion);
                        $objInfoPersonaEmpresaRolCarac->setFeCreacion(new \DateTime('now'));
                        $objInfoPersonaEmpresaRolCarac->setUsrCreacion($objUsrCreacion);
                        $objInfoPersonaEmpresaRolCarac->setFeUltMod(null);
                        $objInfoPersonaEmpresaRolCarac->setUsrUltMod(null);
                        $objInfoPersonaEmpresaRolCarac->setIpCreacion($strIpCreacion);
                        $objInfoPersonaEmpresaRolCarac->setEstado('Activo');
                        $emComercial->persist($objInfoPersonaEmpresaRolCarac);
                        $emComercial->flush();
                        $objResultado = json_encode(array('success'=>true,
                        'id'=>$strEmpresaGrupo
                        ));
                    }
                }
            } catch (Exception $e) 
            {
                $serviceUtil->insertError('TELCOS', 
                                      'enviarABaseAction', 
                                      $e->getMessage(), 
                                      $objPeticion->getSession()->get('user'), 
                                      $objPeticion->getClientIp()
                                     );
                $emComercial->getConnection()->rollback();
                $emComercial->getConnection()->close();
                $objRespuesta = json_encode(array('success'=>false,'mensaje'=>$e));
            }
        }
        if (count($arrayEliminados) > 0) 
        {
            try 
            {
                foreach ($arrayEliminados as $registroEliminado) 
                {
                    if ($registroEliminado->en_base != "") 
                    {
                        $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                            ->deleteInfoPersonaEmpresaRolCaracById($registroEliminado->en_base);
                    }
                }
            } catch (Exception $e) 
            {
                $serviceUtil->insertError('TELCOS', 
                                      'enviarABaseAction', 
                                      $e->getMessage(), 
                                      $objPeticion->getSession()->get('user'), 
                                      $objPeticion->getClientIp()
                                     );
                $emComercial->getConnection()->rollback();
                $emComercial->getConnection()->close();
                $objRespuesta = json_encode(array('success'=>false,'mensaje'=>$e));
            }
        }
        $emComercial->getConnection()->commit();
        $emComercial ->getConnection() ->close();
        $objRespuesta->setContent($objResultado);
        return $objRespuesta;
    }

    public function listarAllCaracteristicasAction()
    {
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        $objPeticion = $this->get('request');
        $emComercial = $this->getDoctrine()->getManager('telconet');
        $emComercial->getConnection()->beginTransaction();
        $strCaracteristica    = 'CONFIGURACION_PERFILES_FACTIBILIDAD';
        $strEstado            = 'Activo';
        $entityCaracteristica = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                               ->getCaracteristicaPorDescripcionPorEstado($strCaracteristica, $strEstado);
        $strIdEmpleado = $objPeticion->get('idEmpleado');
        $intIdCaracteristica = $entityCaracteristica->getId();
        $intIdEmpleado = explode("@@", $strIdEmpleado)[1];
        $arrayInfoEmpresaRolCarac = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                        ->findInfoPersonaEmpresaRolCaracByPersonaDescripcion($intIdEmpleado, $intIdCaracteristica);



        $arrayRegistros = array();

        foreach ($arrayInfoEmpresaRolCarac as $registro) 
        {
            $arrayRegistros[] = array("id_jurisdiccion" => $registro->getValor(),
                                        "id_registro" => $registro->getId());
        }

        $objJson = '{"total":"' . count($arrayRegistros) . '","encontrados":' . json_encode($arrayRegistros) . '}';

        $objRespuesta->setContent($objJson);

        return $objRespuesta;
    }

    public function getJurisdiccionesAction()
    {
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');

        $objPeticion = $this->get('request');

        $intJurisdiccion = $objPeticion->query->get('query');

        $intIdDepartamento = $objPeticion->query->get('id_departamento');

         

        $emInfraestructura = $this->getDoctrine()->getManager("telconet");

        $objDepartamento = $emInfraestructura->getRepository("schemaBundle:AdmiDepartamento")->find($intIdDepartamento);

        $objJson =  $emInfraestructura->getRepository("schemaBundle:AdmiJurisdiccion")
        ->generarJsonJurisdiccionesTodasLasEmpresa($intJurisdiccion, $objDepartamento->getEmpresaCod());

        $objRespuesta->setContent($objJson);

        return $objRespuesta;
    }

    public function getDepartamentosAction()
    {
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');

        $objPeticion = $this->get('request');

        $strDepartamento = $objPeticion->query->get('query');

        $emGeneral = $this->getDoctrine()->getManager("telconet");

        $arrayParametros = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
        ->getTodasLasEmpresas('PERFILES_FACTIBILIDAD_NACIONAL', '', '', $strDepartamento, '', '', '', '', '');

        $arrayDepartamentos = array();

        foreach ($arrayParametros as $entityParametro) 
        {
            $arrayDepartamentos[] = array("id_departamento"     => $entityParametro["valor1"],
                                           "nombre_departamento" => $entityParametro["descripcion"]);
        }

        $objJson = '{"total":"' . count($arrayDepartamentos) . '","encontrados":' . json_encode($arrayDepartamentos) . '}';

        $objRespuesta->setContent($objJson);

        return $objRespuesta;
    }

    public function getEmpleadosPorDepartamentoCiudadAction()
    {
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');

        $objPeticion = $this->get('request');

        $emComercial = $this->getDoctrine()->getManager("telconet");

        $strNombre = $objPeticion->query->get('query');

        $strIdDepartamento = $objPeticion->query->get('id_departamento'); 
        $strIdCanton = $objPeticion->query->get('id_canton'); 
        $intDepartamentoCaso = $objPeticion->query->get('departamento_caso') ? $objPeticion->query->get('departamento_caso') : 0;  
        $strSoloJefesSINO = $objPeticion->query->get('es_jefe') ? $objPeticion->query->get('es_jefe') : 'no';

        if ($strSoloJefesSINO=='si') 
        {
            $strSoloJefes = true;
        } else 
        {
            $strSoloJefes=false;
        }

        $strEmpresas = '';

        if (!$strSoloJefes) 
        {
            $strSoloJefes = true;

            $strEmpresas='';

            if ($intDepartamentoCaso!=0) 
            {
                if ($strIdDepartamento == $intDepartamentoCaso) 
                {
                    $strSoloJefes = false;
                } 
            } else 
            {
                $strSoloJefes = false;
            }
        }


        $strCodEmpresa = "";

        $strParamEmpresa = $objPeticion->query->get('empresa') ? $objPeticion->query->get('empresa') : "";

        if ($strParamEmpresa!="") 
        {
            $strEmpresa = $emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneBy(array('prefijo'=>$strParamEmpresa));
            if ($strEmpresa) 
            {
                $strCodEmpresa = $strEmpresa->getId();
            }
        }

        if ($strEmpresas!='') 
        {
            $strCodEmpresa = $strEmpresas;
        }

        $arrayCantones = array();

        $objJson = $this->getDoctrine()
                ->getManager("telconet")
                ->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                ->generarJsonEmpleadosXDepartamento($strIdDepartamento, '', $strNombre, 
                $strSoloJefes, true, $strCodEmpresa, $arrayCantones, 'no', $strIdCanton);

        $objRespuesta->setContent($objJson);

        return $objRespuesta;
    }

    private function containsElement($arrayData, $objElement)
    {
        foreach ($arrayData as $item) 
        {
            $objPersonaEmpresaRolCaracId = $objElement->id_empleado;
            $intIdEmpleado = intval(explode("@@", $objPersonaEmpresaRolCaracId)[1]);
            
            //Id's del item
            $intIdJurisdiccion = $item["id_jurisdiccion"];            
            $intIdUsuario = $item["id_usuario"];

            if (($intIdEmpleado == $intIdUsuario) && ($intIdJurisdiccion) == $objElement->id_jurisdiccion) 
            {
                return true;
            }
        }
        return false;
    }
}

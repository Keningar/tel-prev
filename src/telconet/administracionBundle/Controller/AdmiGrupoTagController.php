<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\schemaBundle\Entity\AdmiGrupoTag;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use telconet\schemaBundle\Entity\AdmiModeloTecnologia;

use JMS\SecurityExtraBundle\Annotation\Secure;

class AdmiGrupoTagController extends Controller
{

    /**
     * indexAction
     *
     * Metodo metodo que carga el grid inicial de la administracion de grupos tags
     *
     * @return twig
     *
     * @author Roberth Cobena <rcobena@telconet.ec>
     * @author Francisco Gonzales <fgonzalezh@telconet.ec>
     * @version 1.0 03-06-2021
     *
     * @Secure(roles="ROLE_461-1")
     */
    public function indexAction()
    {
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        
        $arrayRolesPermitidos = array();

        if (true === $this->get('security.context')->isGranted('ROLE_461-1')) 
        {
            $arrayRolesPermitidos[] = 'ROLE_461-1'; //delete
        }


        $arrayGrupos = $emInfraestructura->getRepository('schemaBundle:AdmiGrupoTag')->findAll();

        return $this->render('administracionBundle:AdmiGrupoTag:index.html.twig', array(
            'entities' => $arrayGrupos,
            'rolesPermitidos' => $arrayRolesPermitidos,
        ));
    }

    /**
     * showAction
     *
     * Metodo que muestra los detalles de grupo tag 
     *
     * @return twig
     *
     * @author Roberth Cobena <rcobena@telconet.ec>
     * @author Francisco Gonzales <fgonzalezh@telconet.ec>
     * @version 1.0 03-06-2021
     *
     */
    public function showAction($intTipoScope)
    {
        $emGeneral      = $this->getDoctrine()->getManager("telconet_general");
        $arrayTipoScope = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
            ->get('CONFIGURACION_SCOPES', '', '', '', 'TIPO_SCOPE', $intTipoScope, '', '');

        $strTipoScope = '';

        if (!empty($arrayTipoScope[0]['valor3'])) 
        {
            $strTipoScope = $arrayTipoScope[0]['valor3'];
        }


        return $this->render('administracionBundle:AdmiGrupoTag:show.html.twig', array(
            'tipoScope'       => $intTipoScope,
            'nombreTipoScope' => $strTipoScope
        ));
    }
    /**
     * newAction
     *
     * Metodo que renderiza vista para creacion de grupos
     *
     * @return twig
     *
     * @author Roberth Cobena <rcobena@telconet.ec>
     * @author Francisco Gonzales <fgonzalezh@telconet.ec>
     * @version 1.0 03-06-2021
     *
     */
    public function newAction()
    {
        return $this->render('administracionBundle:AdmiGrupoTag:new.html.twig');
    }

    /**
     * editAction
     *
     * Metodo que renderiza vista para creacion de grupos
     *
     * @return twig
     *
     * @author Roberth Cobena <rcobena@telconet.ec>
     * @author Francisco Gonzales <fgonzalezh@telconet.ec>
     * @version 1.0 03-06-2021
     *
     */
    public function editAction($intTipoScope)
    {
        $emGeneral      = $this->getDoctrine()->getManager("telconet_general");
        $arrayTipoScope = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
            ->get('CONFIGURACION_SCOPES', '', '', '', 'TIPO_SCOPE', $intTipoScope, '', '');

        $strTipoScope = '';

        if (!empty($arrayTipoScope[0]['valor3'])) 
        {
            $strTipoScope = $arrayTipoScope[0]['valor3'];
        }

        return $this->render('administracionBundle:AdmiGrupoTag:edit.html.twig', array(
            'tipoScope'       => $intTipoScope,
            'nombreTipoScope' => $strTipoScope
        ));
    }

    public function getGrupoTagAjaxAction()
    {
        $objResponse = new JsonResponse();
        $objResponse->headers->set('Content-Type', 'text/plain');

        $objRequest     = $this->get('request');
        $strTipoScope = $objRequest->get('tipoScope');
        $intIdEmpresa   = $objRequest->getSession()->get('idEmpresa');
        $serviceUtil    = $this->get('schema.Util');

        $emInfraestructura = $this->getDoctrine()->getManager("telconet_infraestructura");

        $arrayParametros["strTipoScope"] = $strTipoScope;
        $arrayParametros["strEmpresa"] = $intIdEmpresa;

        $arrayTagId = $emInfraestructura->getRepository('schemaBundle:AdmiGrupoTag')
            ->findTagsporScope($arrayParametros);

        $objResponse->setData($arrayTagId);
        return $objResponse;
    }

    /**
     * deleteAjaxAction
     *
     * Metodo que elimina los grupos tags
     *
     * @author Francisco Gonzalez fgonzalezh@telconet.ec>
     * @version 1.0 03-06-2021
     *
     */
    public function deleteAjaxAction()
    {
        $arrayRespuesta = new Response();
        $arrayRespuesta->headers->set('Content-Type', 'text/plain');
        $strPeticion = $this->get('request');
        $strParametro = $strPeticion->get('param');
        $emInfraestructura = $this->getDoctrine()->getManager("telconet_infraestructura");
        $emInfraestructura->getConnection()->beginTransaction();
        $objRequest = $this->getRequest();
        $objSession = $objRequest->getSession();
        error_log($strParametro);
        try 
        {

            $objGrupoTags = $emInfraestructura->getRepository('schemaBundle:AdmiGrupoTag')
                ->findBy(array(
                    'scope' => $strParametro,
                    'estado' => 'Activo'
                ));

            if (empty($objGrupoTags)) 
            {
                $arrayRespuesta->setContent("No existen tags");
            } else 
            {

                foreach ($objGrupoTags as $objGrpTags) 
                {
                    $objGrpTags->setUsrUltMod($objSession->get('user'));
                    $objGrpTags->setFeUltMod(new \DateTime('now'));
                    $objGrpTags->setEstado('Inactivo');
                    $emInfraestructura->persist($objGrpTags);
                    $emInfraestructura->flush();
                }
                $arrayRespuesta->setContent("Se cambio de estado a la entidad");
            }

            if ($emInfraestructura->getConnection()->isTransactionActive()) 
            {
                $emInfraestructura->getConnection()->commit();
            }
        } 
        catch (\Exception $e) 
        {
            $emInfraestructura->getConnection()->rollback();
            throw $this->createNotFoundException('Debe seleccionar el elemento.');
        }
        return $arrayRespuesta;
    }


    /**
     * getScopesRegistrados
     *
     * @param mixed $arrayParametros 
     * @return json
     *
     * @author Francisco Gonzalez <fgonzalezh@telconet.ec>
     * @version 1.0 03-06-2021
     *
     */
    public function getScopesRegistradosAction()
    {
        $objResponse = new JsonResponse();
        $objResponse->headers->set('Content-Type', 'text/plain');

        $objRequest     = $this->get('request');
        $strEstado = $objRequest->get('estado');
        $serviceUtil    = $this->get('schema.Util');

        //
        $emInfraestructura = $this->getDoctrine()->getManager("telconet_infraestructura");

        $arrayParametros["strEstado"] = $strEstado;

        $arrayScopes = $emInfraestructura->getRepository('schemaBundle:AdmiGrupoTag')
            ->findScopeByTags($arrayParametros);
        //


        $arrayResponse = [];

        foreach ($arrayScopes as $ar) 
        {
            $arrayRespuesta = [];
            $arrayRespuesta['scope'] = $ar['scope'];
            $arrayRespuesta['prefixscope'] = $ar['prefixscope'];
            $arrayRespuesta['estado'] = $ar['estado'];
            $arrayRespuesta['action1'] = 'button-grid-show';
            if ($ar['estado'] == 'Inactivo') 
            {
                $arrayRespuesta['action3'] = 'button-grid-invisible';
                $arrayRespuesta['action2'] = 'button-grid-invisible';
            } else 
            {
                $arrayRespuesta['action3'] = 'button-grid-delete';
                $arrayRespuesta['action2'] = 'button-grid-edit';
            }
            array_push($arrayResponse, $arrayRespuesta);
        }
        $objResponse->setData($arrayResponse);
        return $objResponse;
    }


    /**
     * crearGrupoTagAjax
     *
     * Metodo que crea grupos de tags 
     *
     * @return twig
     *
     * @author Roberth Cobena <rcobena@telconet.ec>
     * @author Francisco Gonzalez <fgonzalezh@telconet.ec>
     * @version 1.0 03-06-2021
     *
     */
    public function crearGrupoTagAjaxAction()
    {
        $objResponse = new JsonResponse();
        $objResponse->headers->set('Content-Type', 'text/plain');

        $objRequest     = $this->get('request');
        $strIdTipoScope = $objRequest->get('intTipoScope');
        $strJsonTags    = $objRequest->get('jsonTagsScope');
        $strIpCreacion  = $objRequest->getClientIp();
        $strUsrCreacion = $objRequest->getSession()->get('user');
        $intIdEmpresa   = $objRequest->getSession()->get('idEmpresa');
        $serviceUtil    = $this->get('schema.Util');
        $strTipoScope   = '';
        $strStatus      = 'OK';
        $strMensaje     = 'OK';

        $emInfraestructura = $this->getDoctrine()->getManager("telconet_infraestructura");
        $emGeneral         = $this->getDoctrine()->getManager("telconet_general");

        $emInfraestructura->getConnection()->beginTransaction();

        try 
        {
            $arrayTagsGrid     = json_decode($strJsonTags);
            $arrayTipoScope = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                ->get('CONFIGURACION_SCOPES', '', '', '', 'TIPO_SCOPE', $strIdTipoScope, '', '');

            if (!empty($arrayTipoScope[0]['valor3'])) 
            {
                $strTipoScope = $arrayTipoScope[0]['valor3'];
            } else 
            {
                throw new \Exception("No se encuentra el tipo de Scope enviado");
            }

            //busco si no existe un grupo creado para el scope que se envia
            $objAdmiGrupoTags = $emInfraestructura->getRepository('schemaBundle:AdmiGrupoTag')->findOneBy(array(
                'scope' => $strTipoScope,
                'estado' => 'Activo'
            ));

            if (!is_object($objAdmiGrupoTags)) 
            {

                //Decodificar el json de tags
                foreach ($arrayTagsGrid->data as $objScope) :

                    $objAdmiTag = $emInfraestructura->getRepository('schemaBundle:AdmiTag')
                        ->findOneBy(array(
                            'descripcion' => $objScope->strDescripcionTagBlanck,
                            'estado'     => 'Activo'
                        ));

                    if (is_object($objAdmiTag)) 
                    {
                        $objAdmiGrupoTag = new AdmiGrupoTag();
                        $objAdmiGrupoTag->setScope($strTipoScope);
                        $objAdmiGrupoTag->setTagId($objAdmiTag->getId());
                        $objAdmiGrupoTag->setFeCreacion(new \DateTime('now'));
                        $objAdmiGrupoTag->setUsrCreacion($strUsrCreacion);
                        $objAdmiGrupoTag->setEstado('Activo');
                        $objAdmiGrupoTag->setEmpresaCod($intIdEmpresa);
                        $emInfraestructura->persist($objAdmiGrupoTag);
                        $emInfraestructura->flush();
                    }

                endforeach;

                $strMensaje     = 'Grupo creado correctamente al Tipo de Scope : ' . $strTipoScope;

                $emInfraestructura->getConnection()->commit();
            } else 
            {
                throw new \Exception("Ya existe una agrupacion creado por el Tipo de Scope enviado");
            }
        } 
        catch (\Exception $objEx) 
        {
            $strStatus      = 'ERROR';
            $strMensaje     = $objEx->getMessage();

            if ($emInfraestructura->getConnection()->isTransactionActive()) 
            {
                $emInfraestructura->getConnection()->rollback();
            }

            $serviceUtil->insertError(
                'Telcos+',
                'AdmiGrupoTagController->crearGrupoTagAjaxAction',
                $objEx->getMessage(),
                $strUsrCreacion,
                $strIpCreacion
            );
        }

        $emInfraestructura->getConnection()->close();

        $objResponse->setData(
            array(
                'status'   => $strStatus,
                'mensaje'  => $strMensaje,
                'intTipoScope' => $strIdTipoScope
            )
        );
        return $objResponse;
    }

    /**
     * editarGrupoTagAjaxAction
     *
     * Metodo que edita grupos de tags 
     *
     * @return twig
     *
     * @author Roberth Cobena <rcobena@telconet.ec>
     * @author Francisco Gonzales <fgonzalezh@telconet.ec>
     * @version 1.0 03-06-2021
     *
     */
    public function editarGrupoTagAjaxAction()
    {
        $objResponse = new JsonResponse();
        $objResponse->headers->set('Content-Type', 'text/plain');

        $objRequest     = $this->get('request');
        $strIpCreacion  = $objRequest->getClientIp();
        $strIdTipoScope = $objRequest->get('intTipoScope');
        $strJsonTags    = $objRequest->get('jsonTagsScope');
        $strUsrCreacion = $objRequest->getSession()->get('user');
        $strUsrUltMod   = $objRequest->getSession()->get('user');
        $intIdEmpresa   = $objRequest->getSession()->get('idEmpresa');
        $serviceUtil    = $this->get('schema.Util');
        $strStatus      = '';
        $strMensaje     = '';

        $emInfraestructura = $this->getDoctrine()->getManager("telconet_infraestructura");
        $emGeneral         = $this->getDoctrine()->getManager("telconet_general");

        $emInfraestructura->getConnection()->beginTransaction();
        try 
        {
            //VALIDAMOS EXISTENCIA SCOPE
            $arrayTipoScope = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                ->get('CONFIGURACION_SCOPES', '', '', '', 'TIPO_SCOPE', $strIdTipoScope, '', '');


            if (!empty($arrayTipoScope[0]['valor3'])) 
            {
                $strTipoScope = $arrayTipoScope[0]['valor3'];

                //FORMATEA DATOS DE GRID
                $arrayTagsGrid     = json_decode($strJsonTags);
                $intTagRecibidos = $arrayTagsGrid->total;


                //INACTIVAMOS TAGS DE BD Q NO VENGAN EN EL GRID
                $arrayAdmiGrupoTags = $emInfraestructura->getRepository('schemaBundle:AdmiGrupoTag')
                    ->findBy(
                        array(
                            'scope'          => $strTipoScope,
                            'estado'          => 'Activo'
                        )
                    );

                $boolExiste = false;

                foreach ($arrayAdmiGrupoTags as $objAdmiGrupoTag) 
                {
                    $boolExiste = false;

                    foreach ($arrayTagsGrid->data as $objScope) :
                        if ($objAdmiGrupoTag->getTagId() == $objScope->idtag) 
                        {
                            $boolExiste = true;
                        }
                    endforeach;

                    if (!$boolExiste) 
                    {
                        $objAdmiGrupoTag->setEstado('Inactivo');
                        $objAdmiGrupoTag->setFeUltMod(new \DateTime('now'));
                        $objAdmiGrupoTag->setUsrUltMod($strUsrUltMod);
                        $emInfraestructura->persist($objAdmiGrupoTag);
                        $emInfraestructura->flush();
                    }
                } //INACTIVAMOS TAGS DE BD Q NO VENGAN EN EL GRID

                foreach ($arrayTagsGrid->data as $objScopeN) :

                    $boolExiste = false;

                    $objAdmiGrupoTags = $emInfraestructura->getRepository('schemaBundle:AdmiGrupoTag')
                        ->findOneBy(
                            array(
                                'tagId'       => $objScopeN->idtag,
                                'scope'          => $strTipoScope,
                                'estado'          => 'Activo'
                            )
                        );


                    for ($intCount = 0; $intCount < $intTagRecibidos; $intCount++) 
                    {

                        if (!is_object($objAdmiGrupoTags)) 
                        {
                            $objAdmiGrupoTags = new AdmiGrupoTag();
                            $objAdmiGrupoTags->setScope($strTipoScope);
                            $objAdmiGrupoTags->setTagId($objScopeN->idtag);
                            $objAdmiGrupoTags->setFeCreacion(new \DateTime('now'));
                            $objAdmiGrupoTags->setUsrCreacion($strUsrCreacion);
                            $objAdmiGrupoTags->setEstado('Activo');
                            $objAdmiGrupoTags->setEmpresaCod($intIdEmpresa);
                            $emInfraestructura->persist($objAdmiGrupoTags);
                        }
                    }
                endforeach;
                $emInfraestructura->flush();
                $emInfraestructura->getConnection()->commit();

                $strStatus      = 'OK';
                $strMensaje     = 'DATOS ACTUALIZADOS CORRECTAMENTE';
            } 
            else 
            {
                throw new \Exception("No se encuentra el tipo de Scope enviado");
            }
        } 
        catch (\Exception $objEx) 
        {
            $strStatus      = 'ERROR';
            $strMensaje     = $objEx->getMessage();

            if ($emInfraestructura->getConnection()->isTransactionActive()) 
            {
                $emInfraestructura->getConnection()->rollback();
            }

            $serviceUtil->insertError(
                'Telcos+',
                'AdmiGrupoTagController->editarGrupoTagAjaxAction',
                $objEx->getMessage(),
                $strUsrCreacion,
                $strIpCreacion
            );
        }

        $emInfraestructura->getConnection()->close();

        $objResponse->setData(
            array(
                'status'   => $strStatus,
                'mensaje'  => $strMensaje,
                'intTipoScope' => $strIdTipoScope
            )
        );
        return $objResponse;
    }
}

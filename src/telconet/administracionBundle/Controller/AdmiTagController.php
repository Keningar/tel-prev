<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\schemaBundle\Entity\AdmiTag;
use telconet\schemaBundle\Form\AdmiTagType;
use telconet\schemaBundle\Form\InfoElemento;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

class AdmiTagController extends Controller
{

    /**
     * indexAction
     *
     * Metodo metodo que carga el grid inicial de la administracion de tags
     *
     * @return twig
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 05-03-2015
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');

        $rolesPermitidos = array();

        if(true === $this->get('security.context')->isGranted('ROLE_278-2177'))
        {
            $rolesPermitidos[] = 'ROLE_278-2177';
        }
        if(true === $this->get('security.context')->isGranted('ROLE_278-2178'))
        {
            $rolesPermitidos[] = 'ROLE_278-2178';
        }

        $entities = $em->getRepository('schemaBundle:AdmiTag')->findAll();

        return $this->render('administracionBundle:AdmiTag:index.html.twig', array(
                'entities' => $entities,
                'rolesPermitidos' => $rolesPermitidos
        ));
    }

    /**
     * showAction
     *
     * Metodo que muestra los detalles del tag
     *
     * @return twig
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 05-03-2015
     *
     */
    public function showAction($id)
    {
        $peticion = $this->get('request');

        $em = $this->getDoctrine()->getManager("telconet_infraestructura");

        if(null == $tag = $em->find('schemaBundle:AdmiTag', $id))
        {
            throw new NotFoundHttpException('No existe el tag que se quiere mostrar');
        }

        return $this->render('administracionBundle:AdmiTag:show.html.twig', array(
                'tag' => $tag,
                'flag' => $peticion->get('flag')
        ));
    }

    /**
     * newAction
     *
     * Metodo que carga el formulario para la creación de un nuevo tag
     *
     * @return twig
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 05-03-2015
     *
     */
    public function newAction()
    {
        $entity = new AdmiTag();
        $form = $this->createForm(new AdmiTagType(), $entity);

        return $this->render('administracionBundle:AdmiTag:new.html.twig', array(
                'form' => $form->createView()
        ));
    }

    /**
     * createAction
     *
     * Metodo que crea el tag
     *
     * @return twig
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 05-03-2015
     *
     */
    public function createAction()
    {
        $request = $this->get('request');
        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $parametros = $request->request->get('telconet_schemabundle_admitagtype');
        $entity = new AdmiTag();
        $form = $this->createForm(new AdmiTagType(), $entity);
        $em->getConnection()->beginTransaction();
        $seEjecutaInmediato = $request->get('seEjecuta');
        $statusMdEjecucion = '';

        try
        {
            if($parametros['elementoId'])
            {
                $form->bind($request);

                if($form->isValid())
                {
                    $entity->setEstado('Activo');
                    $entity->setFeCreacion(new \DateTime('now'));
                    $entity->setUsrCreacion($request->getSession()->get('user'));
                    $entity->setFeUltMod(new \DateTime('now'));

                    $em->persist($entity);
                   
                    if($seEjecutaInmediato == 'SI')
                    {
                        $objElemento = $em->getRepository('schemaBundle:InfoElemento')->find($parametros['elementoId']);

                        /* @var $script InfoServicioTecnicoService */
                        $script = $this->get('tecnico.InfoServicioTecnico');
                        $scriptArray = $script->obtenerArregloScript("crearTag", $objElemento->getModeloElementoId());

                        $idDocumento = $scriptArray[0]->idDocumento;
                        $usuario = $scriptArray[0]->usuario;

                        $resultado = $script->ejecutarComandoMdEjecucion($parametros['elementoId'], $usuario, $parametros['descripcion'], $idDocumento);
                       
                        //ejecucion script parte 2
                        /* @var $script InfoServicioTecnicoService */
                        $script2 = $this->get('tecnico.InfoServicioTecnico');
                        $script2Array = $script2->obtenerArregloScript("crearTagSelection", $objElemento->getModeloElementoId());

                        $idDocumento2 = $script2Array[0]->idDocumento;
                        $usuario2 = $script2Array[0]->usuario;
                        $datos2 = $parametros['descripcion'].",".$parametros['descripcion'];
                        $resultado2 = $script2->ejecutarComandoMdEjecucion($parametros['elementoId'], $usuario2, $datos2, $idDocumento2);
                        $statusMdEjecucion = $resultado2->status;
                    }
                    else
                    {
                        $statusMdEjecucion = "OK";
                    }
                    if($statusMdEjecucion != "OK")
                    {
                        throw $this->createNotFoundException('Se generó el error: ' . $resultado->mensaje);
                    }
                    else
                    {
                        $em->flush();
                        $em->getConnection()->commit();

                        return $this->redirect($this->generateUrl('admitag_show', array('id' => $entity->getId())));
                    }
                }
            }
            else
            {
                throw $this->createNotFoundException('Debe seleccionar el elemento.');
            }
        }
        catch(\Exception $e)
        {
            $em->getConnection()->rollback();
            throw $this->createNotFoundException('Debe seleccionar el elemento.');
        }
    }

    /**
     * getElementoServidorAction
     *
     * Metodo que carga el combo con los tipo de elementos servidor
     *
     * @return twig
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 05-03-2015
     *
     */
    public function getElementoServidorAction()
    {
        $respuesta  = new Response();
        $parametros = array();
        $respuesta->headers->set('Content-Type', 'text/json');

        $peticion = $this->get('request');

        $em = $this->getDoctrine()->getManager("telconet_infraestructura");

        $objTipoElemento = $em->getRepository("schemaBundle:AdmiTipoElemento")->findOneBy(array('nombreTipoElemento' => 'SERVIDOR'));

        $parametros["nombre"]       = '';
        $parametros["estado"]       = 'Activo';
        $parametros["tipoElemento"] = $objTipoElemento->getId();
        $parametros["codEmpresa"]   = $peticion->get('idEmpresa');
        $parametros["start"]        = '';
        $parametros["limit"]        = '';

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:InfoElemento')
            ->generarJsonElementosXTipo($parametros);
        $respuesta->setContent($objJson);

        return $respuesta;
    }

    /**
     * deleteAjaxAction
     *
     * Metodo que elimina los tags
     *
     * @return twig
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 05-03-2015
     *
     */
    public function deleteAjaxAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $peticion = $this->get('request');
        $parametro = $peticion->get('param');
        $elemento = $peticion->get('elemento');
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");
        $array_valor = explode("|", $parametro);
        
        try
        {
            foreach($array_valor as $id):
                if(null == $entity = $em->find('schemaBundle:AdmiTag', $id))
                {
                    $respuesta->setContent("No existe la entidad");
                }
                else
                {
                    if(strtolower($entity->getEstado()) != "eliminado")
                    {
                        $entity->setEstado("Eliminado");
                        $entity->setFeUltMod(new \DateTime('now'));
                        $entity->setUsrUltMod($peticion->getSession()->get('user'));

                        $objElemento = $em->getRepository('schemaBundle:InfoElemento')->find($elemento);

                        /* @var $script InfoServicioTecnicoService */
                        $script = $this->get('tecnico.InfoServicioTecnico');
                        $scriptArray = $script->obtenerArregloScript("eliminarTag", $objElemento->getModeloElementoId());

                        $idDocumento = $scriptArray[0]->idDocumento;
                        $usuario = $scriptArray[0]->usuario;

                        $resultado = $script->ejecutarComandoMdEjecucion($elemento, $usuario, $entity->getDescripcion(), $idDocumento);

                        $statusMdEjecucion = $resultado->status;

                        if($statusMdEjecucion == "ERROR")
                        {
                            return $respuesta->setContent("Error " . $resultado->mensaje);
                        }
                        $em->persist($entity);
                        $em->flush();
                    }

                    $respuesta->setContent("Se elimino correctamente el tag.");
                }
            endforeach;
        }
        catch(\Exception $e)
        {
            $em->getConnection()->rollback();
            throw $this->createNotFoundException('Debe seleccionar el elemento.');
        }
        return $respuesta;
    }

    /**
     * getEncontradosAction
     *
     * Metodo que consulta loss datos para el grid principal
     *
     * @return twig
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 05-03-2015
     *
     */
    public function getEncontradosAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $peticion = $this->get('request');

        $queryNombre = $peticion->query->get('query') ? $peticion->query->get('query') : "";
        $nombre = ($queryNombre != '' ? $queryNombre : $peticion->query->get('nombre'));
        $codigo = $peticion->query->get('codigo');
        $estado = $peticion->query->get('estado');

        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiTag')
            ->generarJsonTags($codigo, $nombre, $estado, $start, $limit);
        $respuesta->setContent($objJson);

        return $respuesta;
    }

}

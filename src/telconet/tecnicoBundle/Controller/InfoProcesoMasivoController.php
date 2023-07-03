<?php
/**
* Controlador utilizado para las transacciones en la pantalla de consulta de procesos masivos
* 
* @author John Vera         <javera@telconet.ec>
* @version 1.0 04-12-2014
* 
*/
namespace telconet\tecnicoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use telconet\schemaBundle\Entity\InfoProcesoMasivoDet;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

class InfoProcesoMasivoController extends Controller {
    
    
    /**
     * Funcion que llama la pantalla de consulta procesos masivos
     * 
     * @return mixed $respuesta Retorna el json con los registros consultados
     *
     * @author John Vera         <javera@telconet.ec>
     * @version 1.0 04-12-2014
     * @version 1.1 13-02-2016 John Vera Cambio plan masivo
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.2 2021-09-10 - Se agrega el filtro tipo procesos para el grid Procesos Masivos.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 04-10-2021 Se parametrizan los estados mostrados en la consulta de procesos masivos
     * 
     */
    public function indexAction() {
        
        $rolesPermitidos = array();
        if(true === $this->get('security.context')->isGranted('ROLE_333-3477'))
        {
            $rolesPermitidos[] = 'ROLE_333-3477';//ejecuta cambio de plan masivo
        }

        $arrayListaTipoProceso    = array();
        $arrayListaEstadosPmDet   = array();
        $emGeneral                = $this->get('doctrine')->getManager('telconet_general');
        $strEmpresaCod            = $this->getRequest()->getSession()->get('idEmpresa');
        $arrayTipoProcesoDetalles = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->get('TIPO_PROCESOS_MASIVOS_TELCOS',
                                                                                                'TECNICO',
                                                                                                '',
                                                                                                '',
                                                                                                '',
                                                                                                '',
                                                                                                '',
                                                                                                '',
                                                                                                '',
                                                                                                $strEmpresaCod);
        foreach($arrayTipoProcesoDetalles as $arrayItemDet)
        {
            $arrayListaTipoProceso[] = array(
                'id'   => $arrayItemDet['valor1'],
                'name' => $arrayItemDet['valor2']
            );
        }
        
        $arrayEstadosProcesoDetalles    = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->get(  'ESTADOS_PROCESOS_MASIVOS_DET_TELCOS',
                                                            'TECNICO',
                                                            '',
                                                            '',
                                                            '',
                                                            '',
                                                            '',
                                                            '',
                                                            '',
                                                            $strEmpresaCod);
        foreach($arrayEstadosProcesoDetalles as $arrayItemDet)
        {
            $arrayListaEstadosPmDet[]   = array(
                                                    'id'   => $arrayItemDet['valor1'],
                                                    'name' => $arrayItemDet['valor2']
                                                );
        }
        
        return $this->render('tecnicoBundle:InfoServicioCorte:index.html.twig', array(
                'rolesPermitidos'           => $rolesPermitidos,
                'arrayListaTipoProceso'     => $arrayListaTipoProceso,
                'arrayListaEstadosPmDet'    => $arrayListaEstadosPmDet
        ));
    }
    
    /**
    * Funcion que obtiene los parametros y ejecuta la consulta de los procesos masivos
    * 
    * @return mixed $respuesta Retorna el json con los registros consultados
    *
    * @author John Vera         <javera@telconet.ec>
    * @version 1.0 04-12-2014
    * @version 1.1 13-02-2016 John Vera Cambio plan masivo
    *
    * @author Richard Cabrera  <rcabrera@telconet.ec>
    * @version 1.2 16-02-2018 Los procesos se consultaran por la empresa en session
    */

    public function getConsultaProcesoMasivoAction() 
    {
        $respuesta  = new Response();
        $respuesta  ->headers->set('Content-Type', 'text/json');
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager('telconet');
        $emInfraestructura = $this->get('doctrine')->getManager('telconet_infraestructura');
        $objSession = $request->getSession();

        $arrayParametros['strCodEmpresa']  = $objSession->get('idEmpresa');
        $arrayParametros['login']          = $request->get('login');
        $arrayParametros['idElemento']     = $request->get('idElemento');
        $arrayParametros['estado']         = $request->get('estado');
        $arrayParametros['fechaDesde']     = $request->get('fechaDesde');
        $arrayParametros['fechaHasta']     = $request->get('fechaHasta');
        $arrayParametros['ultimaMilla']    = $request->get('ultimaMilla');
        $arrayParametros['tipo']           = $request->get('tipo');
        $arrayParametros['idProcesoMasivo']= $request->get('idProcesoMasivo');
        $arrayParametros['start']          = $request->get('start');
        $arrayParametros['limit']          = $request->get('limit');
        
        $objJson = $em->getRepository('schemaBundle:InfoProcesoMasivoDet')
                    ->generarJsonConsultaProcesoMasivo($arrayParametros, $emInfraestructura);
                
        $respuesta->setContent($objJson);

        return $respuesta;
    }
    
    
    /**
    * ejecutaCambioPlanMasivoAction 
    * funcion que ejecuta el cambio de estado del registro y de esta forma es considerado en el proceso de cambio de plan masivo
    * 
    *
    * @author John Vera         <javera@telconet.ec>
    * @version 1.0 13-02-2016
    * 
    */
    
    public function ejecutaCambioPlanMasivoAction()
    {

        try
        {
            $respuesta = new Response();
            $respuesta->headers->set('Content-Type', 'text/json');
            $request = $this->getRequest();
            $session = $request->getSession();

            $emInfraestructura = $this->get('doctrine')->getManager('telconet_infraestructura');
            $param = $request->get('param');
            $arrayDetalle = explode("|", $param);
            $emInfraestructura->getConnection()->beginTransaction();
            for($i = 0; $i <= count($arrayDetalle); $i++)
            {
                $objProcesoMasivoDet = $emInfraestructura->getRepository('schemaBundle:InfoProcesoMasivoDet')->findOneById($arrayDetalle[$i]);

                if($objProcesoMasivoDet)
                {
                    $objProcesoMasivoDet->setUsrUltMod($session->get('user'));
                    $objProcesoMasivoDet->setFeUltMod(new \DateTime('now'));
                    $objProcesoMasivoDet->setEstado("Pendiente");
                    $emInfraestructura->persist($objProcesoMasivoDet);
                    $emInfraestructura->flush();

                }
            }
            $emInfraestructura->getConnection()->commit();
            $respuesta->setContent("OK");
        }
        catch(\Exception $e)
        {
            if($emInfraestructura->getConnection()->isTransactionActive())
            {
                $emInfraestructura->getConnection()->rollback();
                $emInfraestructura->getConnection()->close();
            }

            $respuesta->setContent($e->getMessage());
        }
        return $respuesta;
    }

}

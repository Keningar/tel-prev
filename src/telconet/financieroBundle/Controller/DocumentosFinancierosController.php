<?php

namespace telconet\financieroBundle\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;


/**
 * DocumentosFinancieros controller.
 *
 * Controlador que se encargará de administrar las funcionalidades respecto a la opción de Documentos Financieros
 *
 * @author Edson Franco <efranco@telconet.ec>
 * @version 1.0 17-06-2016
 */
class DocumentosFinancierosController extends Controller
{
    /**
     * @Secure(roles="ROLE_354-1")
     * 
     * Documentación para el método 'indexAction'.
     *
     * Redireccion a la pantalla principal de la administración de documentos financieros para un cliente
     * @return render.
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 17-06-2016
     */
    public function indexAction()
    {
        $objRequest         = $this->get('request');
        $objSession         = $objRequest->getSession();
        $arrayPtoCliente    = $objSession->get('ptoCliente');
        $intIdPunto         = 0;
        
        if(!empty($arrayPtoCliente['id']))
        {
            $intIdPunto = $arrayPtoCliente['id'];
        }
        
        return $this->render('financieroBundle:DocumentosFinancieros:index.html.twig', array( 'intIdPunto' => $intIdPunto ));
    }

    
    /**
     * gridAction 
     * 
     * Crea el objeto que se muestra en el grid de los documentos financieros
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0
     * @since 17-04-2016
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.1 06-03-2018 - Se agrega al Grid la FeAutorizacion
     * @return Response     Retorna los datos que se muestran el grid de Documentos Financieros
     */
    public function gridAction()
    {
        $objRequest             = $this->get('request');
        $objSession             = $objRequest->getSession();
        $arrayPtoCliente        = $objSession->get('ptoCliente');
        $intIdEmpresa           = $objSession->get('idEmpresa');
        $strPrefijoEmpresa      = $objSession->get('prefijoEmpresa');
        $intIdOficina           = $objSession->get('idOficina');
        $emFinanciero           = $this->get('doctrine')->getManager('telconet_financiero');
        $arrayFeDesdeCreacion   = explode('T', $objRequest->get("fechaDesdeCreacion"));
        $arrayFeHastaCreacion   = explode('T', $objRequest->get("fechaHastaCreacion"));
        $arrayFeDesdeEmision    = explode('T', $objRequest->get("fechaDesdeEmision"));
        $arrayFeHastaEmision    = explode('T', $objRequest->get("fechaHastaEmision"));
        $strNumeroDocumento     = $objRequest->get("strNumeroDocumento");
        $strTipoDocumento       = $objRequest->get("strTipoDocumento");
        $strEstado              = $objRequest->get("estado");
        $intLimite              = $objRequest->get("limit");
        $intPagina              = $objRequest->get("page");
        $intInicio              = $objRequest->get("start");

        if($strEstado)
        {
            $strEstado = $strEstado;
        }
        else
        {
            $strEstado = '';
        }
        
        $intIdPunto = "";
        
        if(!empty($arrayPtoCliente))
        {
            $intIdPunto = $arrayPtoCliente['id'];
        }
        
        $arrayParametros['strEstado']           = $strEstado;
        $arrayParametros['intLimit']            = $intLimite;
        $arrayParametros['intPage']             = $intPagina;
        $arrayParametros['intStart']            = $intInicio;
        $arrayParametros['intIdPunto']          = $intIdPunto;
        $arrayParametros['intIdEmpresa']        = $intIdEmpresa;
        $arrayParametros['strNumeroDocumento']  = $strNumeroDocumento;
        $arrayParametros['arrayTipoDoc']        = $strTipoDocumento ? array($strTipoDocumento) : array();
        
        if( ( (!$arrayFeDesdeCreacion[0]) && (!$arrayFeHastaCreacion[0]) ) && ( (!$arrayFeDesdeEmision[0]) && (!$arrayFeHastaEmision[0]) ) )
        {
            $arrayInfoDocumentoFinanacieroCab       = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                                   ->find30FacturasPorEmpresaPorEstado($arrayParametros);
            $objInfoDocumentoFinancieroCab          = $arrayInfoDocumentoFinanacieroCab['registros'];
            $intTotalRegistros                      = $arrayInfoDocumentoFinanacieroCab['total'];
        }
        else
        {
            $arrayParametros['intIdOficina']        = $intIdOficina;
            $arrayParametros['strFeCreacionDesde']  = $arrayFeDesdeCreacion[0];
            $arrayParametros['strFeCreacionHasta']  = $arrayFeHastaCreacion[0];
            $arrayParametros['strFeEmisionDesde']   = $arrayFeDesdeEmision[0];
            $arrayParametros['strFeEmisionHasta']   = $arrayFeHastaEmision[0];
            $arrayInfoDocumentoFinanacieroCab       = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                                   ->find30FacturasPorEmpresaPorEstado($arrayParametros);
            $objInfoDocumentoFinancieroCab          = $arrayInfoDocumentoFinanacieroCab['registros'];
            $intTotalRegistros                      = $arrayInfoDocumentoFinanacieroCab['total'];
        }

        foreach($objInfoDocumentoFinancieroCab as $objInfoDocumentoFinancieroCab)
        {
            $strCodigoTipoDocumento = $objInfoDocumentoFinancieroCab->getTipoDocumentoId()->getCodigoTipoDocumento();
            
            switch($strCodigoTipoDocumento)
            {
                case 'FAC':
                    $strUrlShow  = $this->generateUrl('infodocumentofinancierocab_show', array('id' => $objInfoDocumentoFinancieroCab->getId()));
                    break;
                
                case 'FACP':
                    $strUrlShow  = $this->generateUrl('facturasproporcionales_show', array('id' => $objInfoDocumentoFinancieroCab->getId()));
                    break;
                
                case 'NC':
                    $strUrlShow  = $this->generateUrl('infodocumentonotacredito_show', array('id' => $objInfoDocumentoFinancieroCab->getId()));
                    break;
                
                case 'NCI':
                    $strUrlShow  = $this->generateUrl('infodocumentonotacreditointerna_show', array('id' => $objInfoDocumentoFinancieroCab->getId()));
                    break;
                
                case 'ND':
                    $strUrlShow  = $this->generateUrl('infodocumentonotadebito_show', array('id' => $objInfoDocumentoFinancieroCab->getId()));
                    break;
                
                case 'NDI':
                    $strUrlShow  = $this->generateUrl('infodocumentonotadebitointerna_show', array('id' => $objInfoDocumentoFinancieroCab->getId()));
                    break;
                
                case 'DEV':
                    $strUrlShow  = $this->generateUrl('infodocumentonotadebito_devolucion_show', array('id' => $objInfoDocumentoFinancieroCab->getId()));
                    break;
                
                default:
                    $strUrlShow  = $this->generateUrl('infodocumentofinancierocab_show', array('id' => $objInfoDocumentoFinancieroCab->getId()));
            }
            
            $strLinkShow = $strUrlShow;

            $emComercial              = $this->get('doctrine')->getManager('telconet');
            $objInfoPunto             = $emComercial->getRepository('schemaBundle:InfoPunto')->find($objInfoDocumentoFinancieroCab->getPuntoId());
            $objInfoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                       ->find($objInfoPunto->getPersonaEmpresaRolId()->getId());

            $objAdmiTipoNegocio = $emComercial->getRepository('schemaBundle:AdmiTipoNegocio')->find($objInfoPunto->getTipoNegocioId());

            if($objAdmiTipoNegocio)
            {
                $strTipoNegocio = $objAdmiTipoNegocio->getCodigoTipoNegocio();
            }
            else
            {
                $strTipoNegocio = '';
            }

            if($objInfoPersonaEmpresaRol->getPersonaId()->getNombres() != "" && $objInfoPersonaEmpresaRol->getPersonaId()->getApellidos() != "")
            {
                $strNombreRazonSocialCliente = $objInfoPersonaEmpresaRol->getPersonaId()->getNombres() . " " .
                    $objInfoPersonaEmpresaRol->getPersonaId()->getApellidos();
            }

            if($objInfoPersonaEmpresaRol->getPersonaId()->getRazonSocial() != "")
            {
                $strNombreRazonSocialCliente = $objInfoPersonaEmpresaRol->getPersonaId()->getRazonSocial();
            }

            if($objInfoDocumentoFinancieroCab->getNumeroFacturaSri() == null || $objInfoDocumentoFinancieroCab->getNumeroFacturaSri() == "")
            {
                $strNumeroFacturaSri = $objInfoDocumentoFinancieroCab->getNumFactMigracion();
            }
            else
            {
                $strNumeroFacturaSri = $objInfoDocumentoFinancieroCab->getNumeroFacturaSri();
            }

            if($objInfoDocumentoFinancieroCab->getFeEmision() != null)
            {
                $strFeEmision = date_format($objInfoDocumentoFinancieroCab->getFeEmision(), "d/m/Y G:i");
            }
            else
            {
                $strFeEmision = "";
            }

            if($objInfoDocumentoFinancieroCab->getFeAutorizacion() != null)
            {
                $strFeAutorizacion = date_format($objInfoDocumentoFinancieroCab->getFeAutorizacion(), "d/m/Y G:i");
            }
            else
            {
                $strFeAutorizacion = "";
            }
            
            $arrayResultado[] = array
            (
                'Numerofacturasri'          => $strNumeroFacturaSri,
                'Punto'                     => $objInfoPunto->getLogin(),
                'Cliente'                   => $strNombreRazonSocialCliente,
                'Esautomatica'              => ($objInfoDocumentoFinancieroCab->getEsAutomatica()== 'S')? 'Si' : 'No',
                'Estado'                    => $objInfoDocumentoFinancieroCab->getEstadoImpresionFact(),
                'Fecreacion'                => strval(date_format($objInfoDocumentoFinancieroCab->getFeCreacion(), "d/m/Y G:i")),
                'Feemision'                 => $strFeEmision,
                'strFeAutorizacion'         => $strFeAutorizacion,
                'Total'                     => $objInfoDocumentoFinancieroCab->getValorTotal(),
                'linkVer'                   => $strLinkShow,
                'id'                        => $objInfoDocumentoFinancieroCab->getId(),
                'strCodigoDocumento'        => $strCodigoTipoDocumento,
                'intIdTipoDocumento'        => $objInfoDocumentoFinancieroCab->getTipoDocumentoId()->getId(),
                'empresa'                   => $strPrefijoEmpresa,
                'strEsElectronica'          => ($objInfoDocumentoFinancieroCab->getEsElectronica() == 'S')? 'Si' : 'No',
                'negocio'                   => $strTipoNegocio
            );
        }

        if(!empty($arrayResultado))
        {
            $objResponse = new Response(json_encode(array('total' => $intTotalRegistros, 'documentos' => $arrayResultado)));
        }
        else
        {
            $arrayResultado[] = array(
                                        'Numerofacturasri'   => "",
                                        'Punto'              => "",
                                        'Cliente'            => "",
                                        'Esautomatica'       => "",
                                        'Estado'             => "",
                                        'Fecreacion'         => "",
                                        'Feemision'          => "",
                                        'strFeAutorizacion'  => "",
                                        'Total'              => "",
                                        'linkVer'            => "",
                                        'id'                 => "",
                                        'strCodigoDocumento' => '',
                                        'intIdTipoDocumento' => '',
                                        'empresa'            => "",
                                        'strEsElectronica'   => '',
                                        'negocio'            => ""
                                     );
            
            $objResponse = new Response(json_encode(array('total' => $intTotalRegistros, 'documentos' => $arrayResultado)));
        }
        
        $objResponse->headers->set('Content-type', 'text/json');
        
        return $objResponse;
    }

}

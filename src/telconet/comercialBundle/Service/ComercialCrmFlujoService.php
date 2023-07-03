<?php

namespace telconet\comercialBundle\Service;

use Doctrine\ORM\EntityManager;
use telconet\schemaBundle\Entity\ReturnResponse;
use telconet\schemaBundle\Entity\InfoPersonaFormaContacto;
use telconet\schemaBundle\Entity\InfoPersonaContacto;
use telconet\schemaBundle\Entity\InfoPersona;
use telconet\schemaBundle\Entity\AdmiProductoCaracteristica;
use telconet\schemaBundle\Entity\InfoServicio;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoServicioProdCaract;
use telconet\schemaBundle\Entity\AdmiCaracteristica;
use telconet\schemaBundle\Entity\AdmiDepartamento;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRol;
use telconet\schemaBundle\Entity\AdmiParametroDet;
use Symfony\Component\HttpFoundation\File\File;

class ComercialCrmFlujoService
{
    private $emGeneral;
    private $emComercial;
    private $emFinanciero;
    private $emInfraestructura;
    private $serviceUtil;
    private $serviceTecnico;
    private $serviceInfoServicio;
    private $serviceUtilidades;
    private $serviceJefesComercial;
    private $servicePunto;
    private $serviceCliente;
    private $servicePersonaFormaContacto;
    private $serviceFoxPremium;
    private $serviceLicenciasKaspersky;
    private $serviceInternetProtegido;

    /**
     * Documentación para la función 'setDependencies'.
     *
     * Función encargada de setear los entities manager de los esquemas de base de datos.
     *
     * @author David León <mdleon@telconet.ec>
     * @version 1.0 11-05-2021
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $objContainer - objeto contenedor
     *
     */
    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $objContainer )
    {
        $this->emComercial    = $objContainer->get('doctrine.orm.telconet_entity_manager');
        $this->emGeneral      = $objContainer->get('doctrine.orm.telconet_general_entity_manager');
        $this->emFinanciero   = $objContainer->get('doctrine.orm.telconet_financiero_entity_manager');
        $this->serviceUtil    = $objContainer->get('schema.Util');
        $this->serviceTecnico = $objContainer->get('tecnico.InfoServicioTecnico');
        $this->emInfraestructura     = $objContainer->get('doctrine.orm.telconet_infraestructura_entity_manager');
        $this->serviceInfoServicio   = $objContainer->get('comercial.InfoServicio');
        $this->serviceUtilidades     = $objContainer->get('administracion.Utilidades');
        $this->serviceJefesComercial = $objContainer->get('administracion.JefesComercial');
        $this->servicePunto          = $objContainer->get('comercial.InfoPunto');
        $this->serviceCliente        = $objContainer->get('comercial.Cliente');
        $this->servicePersonaFormaContacto = $objContainer->get('comercial.InfopersonaFormaContacto');
        $this->serviceFoxPremium           = $objContainer->get('tecnico.FoxPremium');
        $this->serviceLicenciasKaspersky   = $objContainer->get('tecnico.LicenciasKaspersky');
        $this->serviceInternetProtegido    = $objContainer->get('tecnico.InternetProtegido');
        
    }

    /**
     * Documentación para la función getCaracteristicasProducto,permite obtener la relacion del producto entre Crm y Telcos y retorna las
     * caracteristicas.
     *
     * @param array $arrayParametros [
     *                                 $strUsrCreacion     => Usuario de consulta.
     *                                 $strIpCreacion      => Ip de consulta.
     *                                 $strEmpresa         => Empresa a consultar.
     *                                 $strProducto        => Nombre del Producto.
     *                                 $strGrupo           => Nombre del Grupo.
     *                                 $strSubGrupo        => Nombre del SubGrupo.
     *                               ]
     * @return array $arrayResultado
     *
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.0 08-04-2021
     * 
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.0 08-04-2021 - Se corrige funcionalidad para que devuelva el vendedor por defecto.
     */
    public function getCaracteristicasProducto($arrayParametrosWs)
    {
        $strUsuario          = ( isset($arrayParametrosWs['strUsuario']) && !empty($arrayParametrosWs['strUsuario']) )
                                   ? $arrayParametrosWs['strUsuario'] : 'TELCOS +';
        $strIpCreacion       = ( isset($arrayParametrosWs['strIpCreacion']) && !empty($arrayParametrosWs['strIpCreacion']) )
                               ? $arrayParametrosWs['strIpCreacion'] : '127.0.0.1';
        $strCodEmpresa       = ( isset($arrayParametrosWs['strCodEmpresa']) && !empty($arrayParametrosWs['strCodEmpresa']) )
                               ? $arrayParametrosWs['strCodEmpresa'] : '10';
        $strPrefijoEmpresa   = ( isset($arrayParametrosWs['strPrefijoEmpresa']) && !empty($arrayParametrosWs['strPrefijoEmpresa']) )
                               ? $arrayParametrosWs['strPrefijoEmpresa'] : 'TN';
        $strProducto         = ( isset($arrayParametrosWs['intIdProducto']) && !empty($arrayParametrosWs['intIdProducto']) )
                               ? $arrayParametrosWs['intIdProducto'] : '';
        $strUrLoginVendedor  = ( isset($arrayParametrosWs['usrLoginVendedor']) && !empty($arrayParametrosWs['usrLoginVendedor']) )
                               ? $arrayParametrosWs['usrLoginVendedor'] : '';
        $strCaracteristicaCargo = ( $strCodEmpresa == 10 ) ? 'CARGO_GRUPO_ROLES_PERSONAL' : 'CARGO';
        $strHtml             = "<br>";
        $strCombosValidar    = "";
        $strTipoPersonal     = 'Otros';
        $arrayVendedores     = array();
        $arrayGerentes       = array();
        $strMensajeError     = "";
        $strStatus           = 200;
        try
        {
            if(!empty($strProducto) || $strProducto == '')
            {
                $objProducto = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->find($strProducto);
            }
            if(!is_object($objProducto) || empty($objProducto))
            {
                throw new \Exception("No existe el Producto.");
            }
            $arrayDatos = array('intEmpresa'        => $strCodEmpresa,
                                'strDescProducto'   => $objProducto->getDescripcionProducto(),
                                'strGrupo'          => $objProducto->getGrupo(),
                                'strSubGrupo'       => $objProducto->getSubGrupo()
                                );
            $arrayProductos  = $this->emComercial->getRepository('schemaBundle:InfoServicio')->getProductoByDescripcion($arrayDatos);
            if(is_array($arrayProductos) && !empty($arrayProductos))
            {
                foreach($arrayProductos as $arrayItem)
                {
                    if(isset($arrayItem['id']) && !empty($arrayItem['id']))
                    {
                       $arrayDepartamentos = $this->emComercial->getRepository('schemaBundle:AdmiDepartamento')
                                                          ->getDepartamentosPorLogin(array("strLogin"              => $strUsuario,
                                                                                           "intIdEmpresa"          => $strCodEmpresa,
                                                                                           "strEstadoDepartamento" => "Activo"));
                       if(empty($arrayDepartamentos['registros']) || !is_array($arrayDepartamentos))
                       {
                           throw new \Exception("No existe departamento en estado activo");
                       }
                       $arrayItemDepartamentos = $arrayDepartamentos['registros'];
                       $intIdDepartamento   = $arrayItemDepartamentos[0]["ID_DEPARTAMENTO"];
                       
                       $objPersona = $this->emComercial->getRepository('schemaBundle:InfoPersona')->findOneBy(array('login' => $strUsuario,
                                                                                                                    'estado'=> 'Activo'));
                       
                       if(!is_object($objPersona) && empty($objPersona))
                       {
                           throw new \Exception("No existe el empleado en estado activo");
                       }
                       
                       $objPersonaEmpresaRol = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->findOneBy(
                                                                                                        array('personaId' => $objPersona->getId(),
                                                                                                              'departamentoId' => $intIdDepartamento,
                                                                                                              'estado'=> 'Activo'));
                       if(!is_object($objPersonaEmpresaRol) && empty($objPersonaEmpresaRol))
                       {
                           throw new \Exception("No se encuentra el rol del empleado, favor comunicarse con sistemas.");
                       }
                       if(isset($arrayItem['requiere_comisionar']) && isset($arrayItem['requiere_comisionar']) == "SI" )
                        {
                            $arrayParametros                          = array();
                            $arrayParametros['usuario']               = $objPersonaEmpresaRol->getId();
                            $arrayParametros['empresa']               = $strCodEmpresa;
                            $arrayParametros['estadoActivo']          = 'Activo';
                            $arrayParametros['caracteristicaCargo']   = $strCaracteristicaCargo;
                            $arrayParametros['departamento']          = $intIdDepartamento;
                            $arrayParametros['nombreArea']            = 'Comercial';
                            $arrayParametros['strTipoRol']            = array('Empleado', 'Personal Externo');

                        $arrayRolesNoIncluidos = array();
                        $arrayParametrosRoles  = array( 'strCodEmpresa'     => $strCodEmpresa,
                                                        'strValorRetornar'  => 'descripcion',
                                                        'strNombreProceso'  => 'JEFES',
                                                        'strNombreModulo'   => 'COMERCIAL',
                                                        'strNombreCabecera' => 'ROLES_NO_PERMITIDOS',
                                                        'strUsrCreacion'    => $strUsuario,
                                                        'strIpCreacion'     => $strIpCreacion );

                        $arrayResultadosRolesNoIncluidos = $this->serviceUtilidades->getDetallesParametrizables($arrayParametrosRoles);

                        if( isset($arrayResultadosRolesNoIncluidos['resultado']) && !empty($arrayResultadosRolesNoIncluidos['resultado']) )
                        {
                            foreach( $arrayResultadosRolesNoIncluidos['resultado'] as $strRolNoIncluido )
                            {
                                $arrayRolesNoIncluidos[] = $strRolNoIncluido;
                            }
                            
                            $arrayParametros['rolesNoIncluidos'] = $arrayRolesNoIncluidos;
                        }
                        
                        $arrayRolesIncluidos                       = array();
                        $arrayParametrosRoles['strNombreCabecera'] = 'ROLES_PERMITIDOS';

                        $arrayResultadosRolesIncluidos = $this->serviceUtilidades->getDetallesParametrizables($arrayParametrosRoles);

                        if( isset($arrayResultadosRolesIncluidos['resultado']) && !empty($arrayResultadosRolesIncluidos['resultado']) )
                        {
                            foreach( $arrayResultadosRolesIncluidos['resultado'] as $strRolIncluido )
                            {
                                $arrayRolesIncluidos[] = $strRolIncluido;
                            }
                            
                            $arrayParametros['strTipoRol'] = $arrayRolesIncluidos;
                        }
                        

                        if( !empty($strPrefijoEmpresa) && $strPrefijoEmpresa == 'TN' )
                        {
                            $arrayParametros['strPrefijoEmpresa']     = $strPrefijoEmpresa;
                            $arrayParametros['strTipoPersonal']       = $strTipoPersonal;
                            $arrayParametros['intIdPersonEmpresaRol'] = $objPersonaEmpresaRol->getId();
                            $arrayParametrosDepartamentos = array('strCodEmpresa'     => $strCodEmpresa,
                                                                  'strValorRetornar'  => 'valor1',
                                                                  'strNombreProceso'  => 'ADMINISTRACION_JEFES',
                                                                  'strNombreModulo'   => 'COMERCIAL',
                                                                  'strNombreCabecera' => 'GRUPO_DEPARTAMENTOS',
                                                                  'strValor2Detalle'  => 'COMERCIAL',
                                                                  'strUsrCreacion'    => $strUsuario,
                                                                  'strIpCreacion'     => $strIpCreacion);

                            $arrayResultadosDepartamentos = $this->serviceUtilidades->getDetallesParametrizables($arrayParametrosDepartamentos);

                            if( isset($arrayResultadosDepartamentos['resultado']) && !empty($arrayResultadosDepartamentos['resultado']) )
                            {
                                $arrayParametros['departamento'] = $arrayResultadosDepartamentos['resultado'];
                            }
                        }
            
                        $arrayPlantillaProductos = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                               ->getResultadoComisionPlantilla( array('intIdProducto' => $arrayItem['id'],
                                                                                                      'strCodEmpresa' => $strCodEmpresa) );

                            if( isset($arrayPlantillaProductos['objRegistros']) && !empty($arrayPlantillaProductos['objRegistros']) )
                            {
                                foreach($arrayPlantillaProductos['objRegistros'] as $arrayItem)
                                {
                                    if( isset($arrayItem['valor3']) && !empty($arrayItem['valor3']) )
                                    {
                                        $strMarcaCampoRequerido = "";
                                        $strFuncionOnChange     = "";
                                        $strCampoRequerido      = "";
                                        $intIdRelacionCombo     = 0;
                                        $intIdComisionDet       = ( isset($arrayItem['idComisionDet']) && !empty($arrayItem['idComisionDet']) )
                                                                  ? $arrayItem['idComisionDet'] : 0;

                                        if( $intIdComisionDet > 0 )
                                        {
                                            $arrayParametroRelacionCombos = array('strCodEmpresa'     => $strCodEmpresa,
                                                                                  'strValorRetornar'  => 'valor1',
                                                                                  'strNombreProceso'  => 'SERVICIOS',
                                                                                  'strNombreModulo'   => 'COMERCIAL',
                                                                                  'strNombreCabecera' => 'RELACION_GRUPO_ROLES_PERSONAL',
                                                                                  'strValor2Detalle'  => $arrayItem['valor3'],
                                                                                  'strValor3Detalle'  => 'LABEL',
                                                                                  'strUsrCreacion'    => $strUsuario,
                                                                                  'strIpCreacion'     => $strIpCreacion);

                                            $arrayResultados =  $this->serviceUtilidades->getDetallesParametrizables($arrayParametroRelacionCombos);

                                            if( isset($arrayResultados['resultado']) && !empty($arrayResultados['resultado']) )
                                            {
                                                $intIdRelacionCombo = $arrayResultados['resultado'];

                                                $objAdmiComisionDet = $this->emComercial->getRepository('schemaBundle:AdmiComisionDet')
                                                                                  ->findOneById($intIdComisionDet);

                                                if( !is_object($objAdmiComisionDet) )
                                                {
                                                    throw new \Exception('No se encontró detalle de la plantilla de comisionistas');
                                                }
                                                else
                                                {
                                                    $objAdmiComisionCab = $objAdmiComisionDet->getComisionId();

                                                    if( is_object($objAdmiComisionCab) )
                                                    {
                                                        $objAdmiComisionRelacion = $this->emComercial->getRepository('schemaBundle:AdmiComisionDet')
                                                                                        ->findOneBy( array('comisionId'     => $objAdmiComisionCab,
                                                                                                           'parametroDetId' => $intIdRelacionCombo,
                                                                                                           'estado'         => 'Activo') );
                                                        if( is_object($objAdmiComisionRelacion) )
                                                        {
                                                            $intIdComisionDetRelacion = $objAdmiComisionRelacion->getId();

                                                            if( $intIdComisionDetRelacion > 0 )
                                                            {
                                                                $strFuncionOnChange = ' onchange = "agregarLabel(\''.$intIdComisionDet.'\', \''.
                                                                                      $intIdComisionDetRelacion.'\');" ';
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                            if( isset($arrayItem['comisionVenta']) && !empty($arrayItem['comisionVenta']) )
                                            {
                                                $floatComisionVenta = round(floatval($arrayItem['comisionVenta']), 2);

                                                if( $floatComisionVenta > 0 )
                                                {
                                                    $strMarcaCampoRequerido = "*";
                                                    $strCampoRequerido      = " required='required' ";
                                                }
                                            }

                                            if( $arrayItem['valor3'] == "GERENTE_PRODUCTO" )
                                            {
                                                $strLabelGerenteProducto = ( isset($arrayItem['descripcion']) && !empty($arrayItem['descripcion']) )
                                                                           ? $arrayItem['descripcion'] : 'Gerente de Producto';
                                                $strGrupoProducto        = $objProducto->getGrupo() ? $objProducto->getGrupo() : '';

                                                $arrayParametrosGerenteProducto                         = $arrayParametros;
                                                $arrayParametrosGerenteProducto['strAsignadosProducto'] = $strGrupoProducto;

                                                $arrayGerenteProducto = $this->serviceJefesComercial
                                                                                               ->getListadoEmpleados($arrayParametrosGerenteProducto);

                                                if( isset($arrayGerenteProducto['usuarios']) && !empty($arrayGerenteProducto['usuarios']) )
                                                {
                                                    if( !empty($strCombosValidar) )
                                                    {
                                                        $strCombosValidar .= '|';
                                                    }

                                                    $strCombosValidar .= $intIdComisionDet;

                                                    $strHtml .= '<div class="row"><div style="clear:both;" class="content-comisionistas">
                                                                    <div style="float:left; width: 217px;">
                                                                        <label>'.$strMarcaCampoRequerido.$strLabelGerenteProducto.'</label>
                                                                    </div>
                                                                    <div style="float:left; width: 300px;">
                                                                        <select id="cmb'.$intIdComisionDet.'" name="cmb'.$intIdComisionDet.'" '.
                                                                                $strCampoRequerido.$strFuncionOnChange.'>
                                                                            <option value="0">Seleccione</option>';

                                                    foreach( $arrayGerenteProducto['usuarios'] as $arrayUsuario )
                                                    {
                                                        if( isset($arrayUsuario['intIdPersonaEmpresaRol']) && 
                                                            !empty($arrayUsuario['intIdPersonaEmpresaRol'])
                                                            && isset($arrayUsuario['strEmpleado']) && !empty($arrayUsuario['strEmpleado']) )
                                                        {
                                                            $arrayGerentes[] = array('id'     => $arrayUsuario['intIdPersonaEmpresaRol'], 
                                                                                     'nombre' => $arrayUsuario['strEmpleado']);
                                                            
                                                            $strHtml .= '<option value="'.$arrayUsuario['intIdPersonaEmpresaRol'].'">'.
                                                                        $arrayUsuario['strEmpleado'].'</option>';
                                                        }
                                                    }
                                                    $strHtml .= '       </select></div></div></div>';
                                                }
                                                else
                                                {
                                                    $strMensajeError = 'No se encontró Gerentes de Producto para el producto seleccionado.';

                                                    throw new \Exception( $strMensajeError.' ('.$arrayItem['id'].')');
                                                }
                                            }
                                            elseif( $arrayItem['valor3'] == "SUBGERENTE" )
                                            {
                                                if( !empty($strCombosValidar) )
                                                {
                                                    $strCombosValidar .= '|';
                                                }

                                                $strCombosValidar .= $intIdComisionDet;

                                                $strLabelSubgerente = ( isset($arrayItem['descripcion']) && !empty($arrayItem['descripcion']) )
                                                                      ? $arrayItem['descripcion'] : 'Subgerente';
                                                
                                                $strHtml .= '<div style="clear:both;" class="content-comisionistas">
                                                            <div style="float:left; width: 217px;">
                                                                <label>'.$strMarcaCampoRequerido.$strLabelSubgerente.'</label></div>
                                                            <div style="form-control">
                                                                <input type="hidden" id="cmb'.$intIdComisionDet.'" name="cmb'.$intIdComisionDet.'" />
                                                                <input type="text" id="str'.$intIdComisionDet.'" name="str'.$intIdComisionDet.'" '.
                                                                'readonly="true" /></div></div>';
                                            }
                                            else
                                            {
                                                $arrayParametrosOtros = $arrayParametros;

                                                $intIdCargo = ( isset($arrayItem['idParametroDet']) && !empty($arrayItem['idParametroDet']) )
                                                              ? $arrayItem['idParametroDet'] : 0;

                                                if( !empty($intIdCargo) )
                                                {
                                                    $strLabelPersonal = ( isset($arrayItem['descripcion']) && !empty($arrayItem['descripcion']) )
                                                                         ? $arrayItem['descripcion'] : ucwords(strtolower($arrayItem['valor3']));

                                                    $arrayParametrosOtros['criterios']['cargo'] = $intIdCargo;

                                                    $arrayPersonal = $this->serviceJefesComercial->getListadoEmpleados( $arrayParametrosOtros );

                                                    if( isset($arrayPersonal['usuarios']) && !empty($arrayPersonal['usuarios']) )
                                                    {
                                                        if( !empty($strCombosValidar) )
                                                        {
                                                            $strCombosValidar .= '|';
                                                        }

                                                        $strCombosValidar .= $intIdComisionDet;

                                                        $strHtml .= '<div class="row"><div  style="form-control" class="content-comisionistas">
                                                                        <div style="float:left; width: 217px;">
                                                                            <label>'.$strMarcaCampoRequerido.$strLabelPersonal.'</label></div>
                                                                        <div class="col-lg-3" >';

                                                        if( $arrayItem['valor3'] == "VENDEDOR" )
                                                        {
                                                            $strHtml .= '<input type="hidden" id="inputVendedor'.$intIdComisionDet.'"'
                                                                        . ' name="inputVendedor'.$intIdComisionDet.'" value="S" />';
                                                        }

                                                        $strHtml .= '<select id="cmb'.$intIdComisionDet.'" style="form-control" name="cmb'
                                                                                .$intIdComisionDet.'" '.
                                                                             $strCampoRequerido.$strFuncionOnChange.'>';

                                                        foreach( $arrayPersonal['usuarios'] as $arrayUsuario )
                                                        {
                                                            if( isset($arrayUsuario['intIdPersonaEmpresaRol']) 
                                                                && isset($arrayUsuario['strEmpleado']) && !empty($arrayUsuario['strEmpleado'])
                                                                && ($arrayUsuario['strEmpleado'] == $strUrLoginVendedor))
                                                            {
                                                                $strHtml .= '<option value="'.$arrayUsuario['intIdPersonaEmpresaRol'].'">'.
                                                                            $arrayUsuario['strEmpleado'].'</option>';
                                                            }
                                                        }
                                                        foreach( $arrayPersonal['usuarios'] as $arrayUsuario )
                                                        {
                                                            if( isset($arrayUsuario['intIdPersonaEmpresaRol']) 
                                                                && !empty($arrayUsuario['intIdPersonaEmpresaRol']) 
                                                                && isset($arrayUsuario['strEmpleado'])
                                                                && !empty($arrayUsuario['strEmpleado']) )
                                                            {
                                                                $arrayVendedores[] = array('id'     => $arrayUsuario['intIdPersonaEmpresaRol'], 
                                                                                           'nombre' => $arrayUsuario['strEmpleado']);
                                                                $strHtml .= '<option value="'.$arrayUsuario['intIdPersonaEmpresaRol'].'">'.
                                                                            $arrayUsuario['strEmpleado'].'</option>';
                                                            }
                                                        }

                                                        $strHtml .= '       </select></div></div></div>';
                                                    }
                                                    else
                                                    {
                                                        $strMensajeError = "No se encontró personal para el cargo de ".$strLabelPersonal;

                                                        throw new \Exception( $strMensajeError );
                                                    }
                                                }
                                                else
                                                {
                                                    $strMensajeError = 'No se ha encontrado cargo para el personal a buscar. '
                                                        . '('.$arrayItem['valor3'].')';

                                                    throw new \Exception( $strMensajeError );
                                                }
                                            }
                                        }
                                    }
                                }

                                $arrayRespuesta['strMensaje']               = 'OK';
                                $arrayRespuesta['strCombosValidar']         = $strCombosValidar;
                                $arrayRespuesta['arrayEmpleados']           = $arrayVendedores;
                                $arrayRespuesta['arrayGerentes']            = $arrayGerentes;
                                $arrayRespuesta['strPlantillaComisionista'] = $strHtml;
                            }
                    }
                        //FIN DE COMISIONISTA
                       
                    }
                }
                
            }
            
        }
        catch (\Exception $ex) 
        {
            $strMensajeError = "Falló la comunicación entre TelcoS+ y TelcoCRM.\n ".$ex->getMessage();
            $strStatus       = 400;
            $this->serviceUtil->insertError('TELCOS+',
                                            'ComercialCrmService.getCaracteristicasProducto',
                                            $strMensajeError,
                                            $strUsrCreacion,
                                            $strIpCreacion);
        }
        $arrayResultado = array(
                                'Comisionista' => $arrayRespuesta,
                                'status'    => $strStatus,
                                'error'     => $strMensajeError);
        return $arrayResultado;
    }
    
    /**
     * Documentación para la función creaPunto, permite crear el punto en telcos con los datos proporcionados en el crm.
     *
     * @return array $arrayResultado
     *
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.0 08-04-2021
     * 
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.1 27/01/2022 - Se modifica validación para consulta del Pre Cliente.
     */
    public function creaPunto($arrayDatosWs)
    {   
        $strUsuario          = ( isset($arrayDatosWs['strUsuario']) && !empty($arrayDatosWs['strUsuario']) )
                                   ? $arrayDatosWs['strUsuario'] : 'TELCOS +';
        $intCodEmpresa       = ( isset($arrayDatosWs['strCodEmpresa']) && !empty($arrayDatosWs['strCodEmpresa']) )
                                   ? $arrayDatosWs['strCodEmpresa'] : 10;
        $strPrefijoEmpresa   = ( isset($arrayDatosWs['strPrefijoEmpresa']) && !empty($arrayDatosWs['strPrefijoEmpresa']) )
                                   ? $arrayDatosWs['strPrefijoEmpresa'] : 'TN';
        $strNombrePais       = ( isset($arrayDatosWs['strNombrePais']) && !empty($arrayDatosWs['strNombrePais']) )
                                   ? $arrayDatosWs['strNombrePais'] : 'ECUADOR';
        $strIpCreacion       = ( isset($arrayDatosWs['strIpCreacion']) && !empty($arrayDatosWs['strIpCreacion']) )
                                   ? $arrayDatosWs['strIpCreacion'] : '127.0.0.1';
        $strRuc                 = ( isset($arrayDatosWs['strRuc']) && !empty($arrayDatosWs['strRuc']) )
                                                                           ? $arrayDatosWs['strRuc'] : '';
        $strLogin               = ( isset($arrayDatosWs['strLogin']) && !empty($arrayDatosWs['strLogin']) )
                                                                           ? $arrayDatosWs['strLogin'] : '';
        $strNombrePunto         = ( isset($arrayDatosWs['strNombrePunto']) && !empty($arrayDatosWs['strNombrePunto']) )
                                                                           ? $arrayDatosWs['strNombrePunto'] : '';
        $strDireccion           = ( isset($arrayDatosWs['strDireccion']) && !empty($arrayDatosWs['strDireccion']) )
                                                                           ? $arrayDatosWs['strDireccion'] : '';
        $strReferencia          = ( isset($arrayDatosWs['strReferencia']) && !empty($arrayDatosWs['strReferencia']) )
                                                                           ? $arrayDatosWs['strReferencia'] : '';
        $strObservacion         = ( isset($arrayDatosWs['strObservacion']) && !empty($arrayDatosWs['strObservacion']) )
                                                                           ? $arrayDatosWs['strObservacion'] : '';
        $intIdPadreF            = ( isset($arrayDatosWs['intIdPadreF']) && !empty($arrayDatosWs['intIdPadreF']) )
                                                                           ? $arrayDatosWs['intIdPadreF'] : '';
        $strLoginVendedor       = ( isset($arrayDatosWs['intIdVendedor']) && !empty($arrayDatosWs['intIdVendedor']) )
                                                                           ? $arrayDatosWs['intIdVendedor'] : '';
        $intIdPuntosCob         = ( isset($arrayDatosWs['intIdPuntosCob']) && !empty($arrayDatosWs['intIdPuntosCob']) )
                                                                           ? $arrayDatosWs['intIdPuntosCob'] : '';
        $intIdCanton            = ( isset($arrayDatosWs['intIdCanton']) && !empty($arrayDatosWs['intIdCanton']) )
                                                                           ? $arrayDatosWs['intIdCanton'] : '';
        $intIdParroquia         = ( isset($arrayDatosWs['intIdParroquia']) && !empty($arrayDatosWs['intIdParroquia']) )
                                                                           ? $arrayDatosWs['intIdParroquia'] : '';
        $intIdSector            = ( isset($arrayDatosWs['intIdSector']) && !empty($arrayDatosWs['intIdSector']) )
                                                                           ? $arrayDatosWs['intIdSector'] : '';
        $intIdTipoNeg           = ( isset($arrayDatosWs['intIdTipoNeg']) && !empty($arrayDatosWs['intIdTipoNeg']) )
                                                                           ? $arrayDatosWs['intIdTipoNeg'] : '';
        $intIdTipoUbi           = ( isset($arrayDatosWs['intIdTipoUbi']) && !empty($arrayDatosWs['intIdTipoUbi']) )
                                                                           ? $arrayDatosWs['intIdTipoUbi'] : '';
        $intIdDepEdif           = ( isset($arrayDatosWs['intIdDepEdif']) && !empty($arrayDatosWs['intIdDepEdif']) )
                                                                           ? $arrayDatosWs['intIdDepEdif'] : '';
        $intEdificioId          = ( isset($arrayDatosWs['puntoedificioid']) && !empty($arrayDatosWs['puntoedificioid']) )
                                                                           ? $arrayDatosWs['puntoedificioid'] : '';
        $strNombreEdificio      = ( isset($arrayDatosWs['puntoedificio']) && !empty($arrayDatosWs['puntoedificio']) )
                                                                           ? $arrayDatosWs['puntoedificio'] : '';
        $strLatitud             = ( isset($arrayDatosWs['latitud']) && !empty($arrayDatosWs['latitud']) )
                                                                           ? $arrayDatosWs['latitud'] : '';
        $strLongitud            = ( isset($arrayDatosWs['longitud']) && !empty($arrayDatosWs['longitud']) )
                                                                           ? $arrayDatosWs['longitud'] : '';
        $strFormaContac         = ( isset($arrayDatosWs['strFormaContac']) && !empty($arrayDatosWs['strFormaContac']) )
                                                                           ? $arrayDatosWs['strFormaContac'] : '';   
        $strNombreDatoEnvio     = ( isset($arrayDatosWs['nombreDatoEnvio']) && !empty($arrayDatosWs['nombreDatoEnvio']) )
                                                                           ? $arrayDatosWs['nombreDatoEnvio'] : '';
        $strDireccionDatoEnvio  = ( isset($arrayDatosWs['direccionDatoEnvio']) && !empty($arrayDatosWs['direccionDatoEnvio']) )
                                                                           ? $arrayDatosWs['direccionDatoEnvio'] : '';
        $strSectorDatoEnvio     = ( isset($arrayDatosWs['sectorDatoEnvio']) && !empty($arrayDatosWs['sectorDatoEnvio']) )
                                                                           ? $arrayDatosWs['sectorDatoEnvio'] : '';
        $strCorreoElectronicoDatoEnvio = ( isset($arrayDatosWs['correoElectronicoDatoEnvio']) && !empty($arrayDatosWs['correoElectronicoDatoEnvio']) )
                                                                           ? $arrayDatosWs['correoElectronicoDatoEnvio'] : '';
        $strTelefonoDatoEnvio          = ( isset($arrayDatosWs['telefonoDatoEnvio']) && !empty($arrayDatosWs['telefonoDatoEnvio']) )
                                                                           ? $arrayDatosWs['telefonoDatoEnvio'] : '';
        $strRol = '';
        $arrayPunto             = array();
        try
        {
            if(empty($strRuc))
            {
                throw new \Exception("Ruc no puede estar vacio, favor verificar");
            }
            $objPersona = $this->emComercial->getRepository('schemaBundle:InfoPersona')->findOneBy(array('identificacionCliente' => $strRuc));
            if(!is_object($objPersona) && !empty($objPersona))
            {
                throw new \Exception("No se encontro la persona, favor verificar");
            }
            $objPersonaEmpresaRol = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                ->findByIdentificacionTipoRolEmpresa($strRuc, 'Cliente', $intCodEmpresa);
            
            if(is_object($objPersonaEmpresaRol))
            {
                $strRol = 'Cliente';
            }
            else
            {
                $arrayEstado = array('Pendiente', 'Activo');
                $arrayRol    = array('Pre-cliente');
                $arrayInfoPerEmpRolCli = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                ->buscaClientesPorIdentificacionTipoRolEmpresaEstados($strRuc, $arrayRol, $intCodEmpresa, $arrayEstado);
                $objPersonaEmpresaRol2 = $arrayInfoPerEmpRolCli[0];
                if(is_object($objPersonaEmpresaRol2))
                {
                    $strRol = 'Pre-cliente';
                }
            }
            if($strRol == '')
            {
                throw new \Exception("No se encontro el Rol de la persona, favor verificar");
            }
            
            $arrayDatosForm = array(
                                        'tipoUbicacionId'       =>  $intIdTipoUbi,
                                        'tipoNegocioId'         =>  $intIdTipoNeg,
                                        'strNombrePais'         =>  $strNombrePais,
                                        'sectorId'              =>  $intIdSector,
                                        'rol'                   =>  $strRol,
                                        'ptoCoberturaId'        =>  $intIdPuntosCob	,
                                        'prefijoEmpresa'        =>  $strPrefijoEmpresa,	
                                        'personaId'             =>  $objPersona,	
                                        'parroquia'             =>	$intIdParroquia,	
                                        'origen_web'            =>	"N",
                                        'oficina'               =>	2,
                                        'observacion'           =>	$strObservacion,
                                        'nombreDatoEnvio'       =>	"",	
                                        'longitudFloat'         =>	$strLongitud,
                                        'login'                 =>	$strLogin,	
                                        'latitudFloat'          =>	$strLatitud,	
                                        'intIdPais'             =>	1,	
                                        'formas_contacto'       =>	$strFormaContac,	
                                        'esPadreFacturacion'	=>	$intIdPadreF,	
                                        'direccion'             =>	$strDireccion,	
                                        'direccionDatoEnvio'	=>	$strDireccion,	
                                        'descripcionpunto'      =>	$strReferencia,	
                                        'dependedeedificio'     =>	$intIdDepEdif	,
                                        'puntoedificioid'       =>  $intEdificioId,
                                        'puntoedificio'         =>  $strNombreEdificio,
                                        'loginVendedor'         =>	$strLoginVendedor	,
                                        'cantonId'              =>	$intIdCanton	,
                                        'nombrepunto'           =>  $strNombrePunto,
                                        'nombreDatoEnvio'       =>  $strNombreDatoEnvio,
                                        'direccionDatoEnvio'    =>  $strDireccionDatoEnvio,
                                        'sectorDatoEnvio'       =>  $strSectorDatoEnvio,
                                        'correoElectronicoDatoEnvio' => $strCorreoElectronicoDatoEnvio,
                                        'telefonoDatoEnvio'     => $strTelefonoDatoEnvio,
                                        "file"                  =>  (empty($arrayDatosWs['arrayFileCroquies']) ? null : 
                                                                                    $this->writeAndGetFile($arrayDatosWs['arrayFileCroquies'])),
                                        "fileDigital"           =>  (empty($arrayDatosWs['arrayFileDigital']) ? null : 
                                                                                    $this->writeAndGetFile($arrayDatosWs['arrayFileDigital']))
                );
                $arrayParametrosPunto =  array('strCodEmpresa'        => $intCodEmpresa,
                                               'strUsrCreacion'       => $strUsuario,
                                               'strClientIp'          => $strIpCreacion,
                                               'arrayDatosForm'       => $arrayDatosForm,
                                               'arrayFormasContacto'  => null);

                $objEntityPunto = $this->servicePunto->crearPunto($arrayParametrosPunto);
                $strMensaje = 'Punto creado con login; '.$objEntityPunto->getLogin().'. ';
                $arrayPunto = array('Punto'     => $objEntityPunto->getId(),
                                    'Login'     => $objEntityPunto->getLogin(),
                                    'Mensaje'   => $strMensaje,
                                    'Status'    => '200');
        }
        catch (\Exception $ex) 
        {
            $strMensaje = "Error al crear el Punto.\n ".$ex->getMessage();
            $arrayPunto = array('status' => '500',
                                'error'  => $strMensaje
                               );
            $this->serviceUtil->insertError('TELCOS+',
                                            'ComercialCrmService.creaPunto',
                                            $strMensaje,
                                            $strUsuario,
                                            '172.0.0.1');
        }
        return $arrayPunto;
    }
    
     /**
     * Escribe un archivo con la data base64 y devuelve una referencia al mismo
     * @param string $data (encodado en base64)
     * @return \Symfony\Component\HttpFoundation\File\File
     */
    private function writeAndGetFile($objData)
    {
        if (empty($objData))
        {
            return null;
        }
        return $this->getFile($this->writeTempFile($objData));
    }
    /**
     * Devuelve una referencia a un archivo existente en el servidor segun la ruta dada
     * @param string $path
     * @return \Symfony\Component\HttpFoundation\File\File
     */
    private function getFile($objPath)
    {
        $objFile = new File($objPath);
        return $objFile;
    }
    /**
     * Escribe un archivo temporal con la data que debe estar encodada en base64,
     * devuelve la ruta del archivo creado en /tmp, con prefijo "telcosws_"
     * @param string $data (encodado en base64)
     * @return string ruta del archivo creado
     */
    private function writeTempFile($objData)
    {
        try
        {
            $objPath = tempnam('/tmp', 'telcosws_');
            $objIfp = fopen($objPath, "wb");
            if (strpos($objData,',') !== false)
            {
                $objData = explode(',', $objData)[1];
            }
            fwrite($objIfp, base64_decode($objData));
            fclose($objIfp);
        }
        catch (\Exception $ex) 
        {
            $strMensaje   = "Error al Guardar archivo.\n ".$ex->getMessage();
            $this->serviceUtil->insertError('TELCOS+',
                                            'ComercialCrmService.writeTempFile',
                                            $strMensaje,
                                            'Telcos+',
                                            '172.0.0.1');
        }
        return $objPath;
    }
    
     /**
     * Documentación para el método getListaServicio
     *
     * Funcion que permite obtener los diferentes servicios que se pueden crear en Telcos.
     *
     * @author David León <mdleon@telconet.ec>
     * @version 1.0 01-07-2021
     *
     * @return $arrayArreglo
     */
    public function getListaServicio($arrayDatosWs)
    {
        $strUsuario          = ( isset($arrayDatosWs['strUsuario']) && !empty($arrayDatosWs['strUsuario']) )
                                   ? $arrayDatosWs['strUsuario'] : 'TELCOS +';
        $intCodEmpresa       = ( isset($arrayDatosWs['strCodEmpresa']) && !empty($arrayDatosWs['strCodEmpresa']) )
                                   ? $arrayDatosWs['strCodEmpresa'] : 10;
        $arrayProductosCrm   = ( isset($arrayDatosWs['arrayProductos']) && !empty($arrayDatosWs['arrayProductos']) )
                                   ? $arrayDatosWs['arrayProductos'] : '';
        $arrayProductos      = array();
        try
        {
            $arrayDatos  = array('arrayProductos' => $arrayProductosCrm,
                                 'strModulo'      => 'Comercial',
                                 'strEstado'      => 'Activo',
                                 'strEmpresaCod'  => $intCodEmpresa);
            $arrayListadoProductos = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->findPorEmpresaYNombre($arrayDatos);
            if ( empty($arrayListadoProductos) )
            {
                throw new \Exception("No existen datos");
            }
            else
            {
                foreach($arrayListadoProductos as $objProducto)
                {
                    $strAgregarProducto = 'SI';
                    //se obtiene el parametro si se agrega el producto
                    $arrayParametroAgregarProducto = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->getOne('CONFIG_PRODUCTO_DIRECT_LINK_MPLS',
                                                                 'TECNICO',
                                                                 '',
                                                                 '',
                                                                 $objProducto->getId(),
                                                                 'VISIBLE_PRODUCTO_AGREGAR_SERVICIO',
                                                                 '',
                                                                 '',
                                                                 '',
                                                                 $intCodEmpresa);
                    if( isset($arrayParametroAgregarProducto) && !empty($arrayParametroAgregarProducto) )
                    {
                        $strAgregarProducto = $arrayParametroAgregarProducto['valor3'];
                    }
                    if( $strAgregarProducto == 'SI' )
                    {
                        $arrayProducto = array('id'             => $objProducto->getId(),
                                               'descripcion'    => $objProducto->getDescripcionProducto());
                        array_push($arrayProductos,$arrayProducto);
                    }
                }
                $arrayFrecuencias = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->get('FRECUENCIA_FACTURACION',
                                                                 '',
                                                                 '',
                                                                 '',
                                                                 '',
                                                                 '',
                                                                 '',
                                                                 '',
                                                                 '',
                                                                 $intCodEmpresa);
                $arrayArreglo = array('msg'         =>'OK',
                                      'productos'   => $arrayProductos,
                                      'frecuencias' => $arrayFrecuencias,
                                      'info'        =>'catalogo');
            }

        }
        catch (\Exception $ex) 
        {
            $strMensaje   = "Error al Listar los servicios.\n ".$ex->getMessage();
            $arrayArreglo = array(  'msg'       => $strMensaje,
                                    'info'      =>'catalogo');
            $this->serviceUtil->insertError('TELCOS+',
                                            'ComercialCrmService.getListaServicio',
                                            $strMensaje,
                                            $strUsuario,
                                            '172.0.0.1');
        }
            return $arrayArreglo;
    }
    
    /**
     * Documentación para el método getFormasContacto
     *
     * Funcion que permite obtener las diferentes formas de contacto.
     *
     * @author David León <mdleon@telconet.ec>
     * @version 1.0 01-07-2021
     *
     * @return $arrayFormasContacto
     */
    public function getFormasContacto()
    {
        try
        {
            $arrayListaFormasContacto = $this->emComercial->getRepository('schemaBundle:AdmiFormaContacto')->findFormasContactoPorEstado('Activo');
            foreach($arrayListaFormasContacto as $entityFormaContacto)
            {
                $arrayFormasContacto[] = array('id'          => $entityFormaContacto->getId(),
                                               'descripcion' => $entityFormaContacto->getDescripcionFormaContacto());
            }
        }
        catch (\Exception $ex) 
        {
            $strMensajeError = "Falló la comunicación entre TelcoS+ y TelcoCRM.\n ".$ex->getMessage();
            $this->serviceUtil->insertError('TELCOS+',
                                            'ComercialCrmService.getFormasContacto',
                                            $strMensajeError,
                                            'TelcosCrm',
                                            '172.0.0.1');
        }
        return $arrayFormasContacto;
    }
    
    /**
     * Documentación para el método getPuntosByCliente
     *
     * Funcion que permite obtener los puntos del cliente con su ruc.
     *
     * @author David León <mdleon@telconet.ec>
     * @version 1.0 01-07-2021
     * 
     * @author David León <mdleon@telconet.ec>
     * @version 1.1 11-06-2022 - Devolvemos un arreglo con los puntos a los que se puede crear el servicio para los proyectos.
     *
     * @param Array $arrayDatosWs[
     *                                  "strRuc"          => Ruc a consultar.
     *                                  "strRol"          => Rol del cliente. 

     *                               ] 
     *
     * @return $objJsonPuntos
     */
    public function getPuntosByCliente($arrayDatosWs)
    {
        $strRuc         = ( isset($arrayDatosWs['strRuc']) && !empty($arrayDatosWs['strRuc']) )? $arrayDatosWs['strRuc'] : '';
        $strRol         = ( isset($arrayDatosWs['strRol']) && !empty($arrayDatosWs['strRol']) )? $arrayDatosWs['strRol'] : 'Pre-cliente';
        $arrayPuntoCoor = array();
        $arrayJsonPuntos = array();
        try
        {
            if(empty($strRuc))
            {
                throw new \Exception("Ruc no puede estar vacio, favor verificar");
            }
            //Buscamos las persona
            $objPersona = $this->emComercial->getRepository('schemaBundle:InfoPersona')->findOneBy(array('identificacionCliente' => $strRuc));
            if(!is_object($objPersona) && !empty($objPersona) || is_null($objPersona))
            {
                throw new \Exception("No se encontro la persona, favor verificar");
            }
            $objPersonaEmpresa = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->
                                                     findOneBy(array('personaId' => $objPersona->getId(),'estado'=> 'Pendiente'));
            if(!is_object($objPersonaEmpresa) && empty($objPersonaEmpresa))
            {
                $objPersonaEmpresa = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->
                                                         findOneBy(array('personaId' => $objPersona->getId(),'estado'=> 'Activo'));
            }
            if(!is_object($objPersonaEmpresa) && empty($objPersonaEmpresa))
            {
                $strRol = 'Cliente';
                $objPersonaEmpresa = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->
                                                         findOneBy(array('personaId' => $objPersona->getId(),'estado'=> 'Activo'));
            }
            if(!is_object($objPersonaEmpresa) && empty($objPersonaEmpresa))
            {
                throw new \Exception("Error al Obtener la PersonaEmpresaRol");
            }
            $arrayParametros = array('idper' => $objPersonaEmpresa->getId(),'rol' => $strRol,'serviceInfoPunto' => $this->servicePunto);
            $objJsonPuntos = $this->emComercial->getRepository('schemaBundle:InfoPunto')->getJsonFindPtosPorPersonaEmpresaRol($arrayParametros);
            if(!empty($objJsonPuntos))
            {
                $arrayJsonPuntos = json_decode($objJsonPuntos);
            }
            $arrayPuntoCoor = $this->emComercial->getRepository('schemaBundle:InfoPunto')->getResultadoFindPtosPorPersonaEmpresaRol($arrayParametros);
            $arrayPuntos = array('Punto'     => $arrayJsonPuntos,'Punto_Coord'  => $arrayPuntoCoor);
        }
        catch (\Exception $ex) 
        {
            $strMensajeError = "Falló la comunicación entre TelcoS+ y TelcoCRM.\n ".$ex->getMessage().$strRol;
            $this->serviceUtil->insertError('TELCOS+',
                                            'ComercialCrmService.getPuntosByCliente',
                                            $strMensajeError,
                                            'TelcosCrm',
                                            '172.0.0.1');
        }
        return $arrayPuntos;
    }
    
    /**
     * Documentación para el método getLoginPunto
     *
     * Funcion que permite generar un login nuevo.
     *
     * @author David León <mdleon@telconet.ec>
     * @version 1.0 01-07-2021
     *
     * @param Array $arrayDatosWs[
     *                                  "strUsuario"             => Usuario que realiza la petición.
     *                                  "strIpCreacion"          => Ip del cliente. 
     *                                  "strPrefijoEmpresa"      => Prefijo de la empresa. 
     *                                  "strCodEmpresa"          => Codigo de la empresa. 
     *                               ] 
     *
     * @return array $arrayResultado [
     *                                  'nombre'                 => Nombre del Vendedor, 
                                        'login'                  => Login del vendedor, 
                                        'idPersona'              => Id persona del vendedor,
     *                               ]
     */
    public function getLoginPunto($arrayDatosWs)
    {
        $strCodEmpresa          = ( isset($arrayDatosWs['strCodEmpresa']) && !empty($arrayDatosWs['strCodEmpresa']) )
                                                                                    ? $arrayDatosWs['strCodEmpresa'] : '10';
        $intIdCanton            = ( isset($arrayDatosWs['intIdCanton']) && !empty($arrayDatosWs['intIdCanton']) )
                                                                                ? $arrayDatosWs['intIdCanton'] : '';
        $strRuc                 = ( isset($arrayDatosWs['strRuc']) && !empty($arrayDatosWs['strRuc']) )? $arrayDatosWs['strRuc'] : '';
        $strLogin               = '';
        try
        {
            if(empty($strRuc))
            {
                throw new \Exception("Ruc no puede estar vacio, favor verificar");
            }
            //Buscamos las persona
            $objPersona = $this->emComercial->getRepository('schemaBundle:InfoPersona')->findOneBy(array('identificacionCliente' => $strRuc));
            if(!is_object($objPersona) && !empty($objPersona))
            {
                throw new \Exception("No se encontro la persona, favor verificar");
            }
            $strLogin  = $this->servicePunto->generarLogin($strCodEmpresa,$intIdCanton,$objPersona->getId(), $tipoNegocio);
        }
        catch (\Exception $ex) 
        {
            $strMensajeError = "Falló la comunicación entre TelcoS+ y TelcoCRM.\n ".$ex->getMessage();
            $this->serviceUtil->insertError('TELCOS+',
                                            'ComercialCrmService.getCaracteristicasProducto',
                                            $strMensajeError,
                                            $strUsrCreacion,
                                            $strIpCreacion);
        }
        return $strLogin;
    }
    
    /**
     * Documentación para el método getComboVendedores
     *
     * Funcion que permite obtener los vendedores del telcos.
     *
     * @author David León <mdleon@telconet.ec>
     * @version 1.0 01-07-2021
     *
     * @param Array $arrayDatosWs[
     *                                  "strUsuario"             => Usuario que realiza la petición.
     *                                  "strIpCreacion"          => Ip del cliente. 
     *                                  "strPrefijoEmpresa"      => Prefijo de la empresa. 
     *                                  "strCodEmpresa"          => Codigo de la empresa. 
     *                               ] 
     *
     * @return array $arrayResultado [
     *                                  'nombre'                     => Mensaje de error, 
                                        'login'                     => Html con las caracteristicas, 
                                        'idPersona'                  => Si el producto es core,
     *                               ]
     */
    public function getComboVendedores($arrayDatosWs)
    {
        $strPrefijoEmpresa          = ( isset($arrayDatosWs['strPrefijoEmpresa']) && !empty($arrayDatosWs['strPrefijoEmpresa']) )
                                                            ? $arrayDatosWs['strPrefijoEmpresa'] : 'TN';
        $strCodEmpresa              = $arrayDatosWs['strCodEmpresa'];
        $strUsuarioSession          = $arrayDatosWs['strUsuario'];
        $strIpCreacion              = ( isset($arrayDatosWs['strIpCreacion']) && !empty($arrayDatosWs['strIpCreacion']) )
                                                            ? $arrayDatosWs['strIpCreacion'] : '127.0.0.1';
        $arrayParametros['EMPRESA'] = $strCodEmpresa;
        $strFiltrarTodosEstados     = 'N';
        $arrayResponse              = array();
        
        try
        {
            $arrayDepartamentos = $this->emComercial->getRepository('schemaBundle:AdmiDepartamento')
                                                              ->getDepartamentosPorLogin(array("strLogin"              => $strUsuarioSession,
                                                                                               "intIdEmpresa"          => $strCodEmpresa,
                                                                                               "strEstadoDepartamento" => "Activo"));
            if(empty($arrayDepartamentos['registros']) || !is_array($arrayDepartamentos))
            {
                throw new \Exception("No existe departamento en estado activo");
            }
            $arrayItemDepartamentos = $arrayDepartamentos['registros'];
            $intIdDepartamento   = $arrayItemDepartamentos[0]["ID_DEPARTAMENTO"];

            //Buscamos las persona
            $objPersona = $this->emComercial->getRepository('schemaBundle:InfoPersona')->findOneBy(array('login' => $strUsuarioSession,
                                                                                                         'estado'=> 'Activo'));

            if(!is_object($objPersona) && empty($objPersona))
            {
                throw new \Exception("No existe el empleado en estado activo");
            }

            //Buscamos persona empresa rol
            $objPersonaEmpresaRol = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->findOneBy(
                                                                                             array('personaId' => $objPersona->getId(),
                                                                                                   'departamentoId' => $intIdDepartamento,
                                                                                                   'estado'=> 'Activo'));    

            if(!is_object($objPersonaEmpresaRol) && empty($objPersonaEmpresaRol))
            {
                throw new \Exception("No se encuentra el rol del empleado, favor comunicarse con sistemas.");
            }
            $arrayResultadoCaracteristicas = $this->emComercial->getRepository('schemaBundle:InfoPersona')->getCargosPersonas($strUsuarioSession);
            if( !empty($arrayResultadoCaracteristicas) )
            {
                $arrayResultadoCaracteristicas = $arrayResultadoCaracteristicas[0];
                $strTipoPersonal               = $arrayResultadoCaracteristicas['STRCARGOPERSONAL'] ? 
                                                                                $arrayResultadoCaracteristicas['STRCARGOPERSONAL'] : 'Otros';
            }
            if( $strPrefijoEmpresa == "TN" )
            {
                $arrayParametros                        = array();
                $arrayParametros['usuario']             = $objPersonaEmpresaRol->getId();
                $arrayParametros['empresa']             = $strCodEmpresa;
                $arrayParametros['estadoActivo']        = 'Activo';
                $arrayParametros['caracteristicaCargo'] = 'CARGO_GRUPO_ROLES_PERSONAL';
                $arrayParametros['nombreArea']          = 'Comercial';
                $arrayParametros['strTipoRol']          = array('Empleado', 'Personal Externo');

                $arrayRolesNoIncluidos = array();
                $arrayParametrosRoles  = array( 'strCodEmpresa'     => $strCodEmpresa,
                                                'strValorRetornar'  => 'descripcion',
                                                'strNombreProceso'  => 'JEFES',
                                                'strNombreModulo'   => 'COMERCIAL',
                                                'strNombreCabecera' => 'ROLES_NO_PERMITIDOS',
                                                'strUsrCreacion'    => $strUsuarioSession,
                                                'strIpCreacion'     => $strIpCreacion );

                $arrayResultadosRolesNoIncluidos = $this->serviceUtilidades->getDetallesParametrizables($arrayParametrosRoles);

                if( isset($arrayResultadosRolesNoIncluidos['resultado']) && !empty($arrayResultadosRolesNoIncluidos['resultado']) )
                {
                    foreach( $arrayResultadosRolesNoIncluidos['resultado'] as $strRolNoIncluido )
                    {
                        $arrayRolesNoIncluidos[] = $strRolNoIncluido;
                    }
                    $arrayParametros['rolesNoIncluidos'] = $arrayRolesNoIncluidos;
                }

                $arrayRolesIncluidos                       = array();
                $arrayParametrosRoles['strNombreCabecera'] = 'ROLES_PERMITIDOS';

                $arrayResultadosRolesIncluidos = $this->serviceUtilidades->getDetallesParametrizables($arrayParametrosRoles);

                if( isset($arrayResultadosRolesIncluidos['resultado']) && !empty($arrayResultadosRolesIncluidos['resultado']) )
                {
                    foreach( $arrayResultadosRolesIncluidos['resultado'] as $strRolIncluido )
                    {
                        $arrayRolesIncluidos[] = $strRolIncluido;
                    }

                    $arrayParametros['strTipoRol'] = $arrayRolesIncluidos;
                }
                $arrayParametrosDepartamentos = array('strCodEmpresa'     => $strCodEmpresa,
                                                      'strValorRetornar'  => 'valor1',
                                                      'strNombreProceso'  => 'ADMINISTRACION_JEFES',
                                                      'strNombreModulo'   => 'COMERCIAL',
                                                      'strNombreCabecera' => 'GRUPO_DEPARTAMENTOS',
                                                      'strValor2Detalle'  => 'COMERCIAL',
                                                      'strUsrCreacion'    => $strUsuarioSession,
                                                      'strIpCreacion'     => $strIpCreacion);

                $arrayResultadosDepartamentos = $this->serviceUtilidades->getDetallesParametrizables($arrayParametrosDepartamentos);

                if( isset($arrayResultadosDepartamentos['resultado']) && !empty($arrayResultadosDepartamentos['resultado']) )
                {
                    $arrayParametros['departamento'] = $arrayResultadosDepartamentos['resultado'];
                }
                $arrayParametrosCargoVendedor = array('strCodEmpresa'     => $strCodEmpresa,
                                                      'strValorRetornar'  => 'id',
                                                      'strNombreProceso'  => 'ADMINISTRACION_JEFES',
                                                      'strNombreModulo'   => 'COMERCIAL',
                                                      'strNombreCabecera' => 'GRUPO_ROLES_PERSONAL',
                                                      'strValor3Detalle'  => 'VENDEDOR',
                                                      'strUsrCreacion'    => $strUsuarioSession,
                                                      'strIpCreacion'     => $strIpCreacion);
                $arrayResultadosCargoVendedor = $this->serviceUtilidades->getDetallesParametrizables($arrayParametrosCargoVendedor);
                if( isset($arrayResultadosCargoVendedor['resultado']) && !empty($arrayResultadosCargoVendedor['resultado']) )
                {
                    foreach( $arrayResultadosCargoVendedor['resultado'] as $intIdCargoVendedor )
                    {
                        $arrayParametros['criterios']['cargo'] = $intIdCargoVendedor;
                    }
                }
                $arrayParametros['strPrefijoEmpresa']       = $strPrefijoEmpresa;
                $arrayParametros['strTipoPersonal']         = $strTipoPersonal;
                $arrayParametros['intIdPersonEmpresaRol']   = $objPersonaEmpresaRol->getId();
                $arrayParametros['strFiltrarTodosEstados']  = $strFiltrarTodosEstados;

                $arrayPersonalVendedor = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                     ->findPersonalByCriterios($arrayParametros);
                if( isset($arrayPersonalVendedor['registros']) && !empty($arrayPersonalVendedor['registros']) 
                    && isset($arrayPersonalVendedor['total']) && $arrayPersonalVendedor['total'] > 0 )
                {
                    foreach($arrayPersonalVendedor['registros'] as $arrayVendedor)
                    {
                        $strNombreVendedor      = ( isset($arrayVendedor['nombres']) && !empty($arrayVendedor['nombres']) )
                            ? ucwords(strtolower($arrayVendedor['nombres'])).' ' : '';
                        $strNombreVendedor      .= ( isset($arrayVendedor['apellidos']) && !empty($arrayVendedor['apellidos']) )
                            ? ucwords(strtolower($arrayVendedor['apellidos'])) : '';
                        $strLoginVendedor       = ( isset($arrayVendedor['login']) && !empty($arrayVendedor['login']) )
                            ? $arrayVendedor['login'] : '';
                        $intIdPersona           = ( isset($arrayVendedor['id']) && !empty($arrayVendedor['id']) )
                            ? $arrayVendedor['id'] : 0;
                        $intIdPersonaEmpresaRol = ( isset($arrayVendedor['idPersonaEmpresaRol']) && !empty($arrayVendedor['idPersonaEmpresaRol']) )
                            ? $arrayVendedor['idPersonaEmpresaRol'] : 0;

                        $arrayItemVendedor                           = array();
                        $arrayItemVendedor['nombre']                 = $strNombreVendedor;
                        $arrayItemVendedor['login']                  = $strLoginVendedor;
                        $arrayItemVendedor['intIdPersona']           = $intIdPersona;
                        $arrayItemVendedor['intIdPersonaEmpresaRol'] = $intIdPersonaEmpresaRol;
                        $arrayVendedores[]                           = $arrayItemVendedor;
                    }

                    $arrayResponse = $arrayVendedores;
                }
            }
            else
            {
                throw new \Exception("Por favor verificar la empresa a consultar.");
            }
        }
        catch (\Exception $ex) 
        {
            $strMensajeError = "Falló la comunicación entre TelcoS+ y TelcoCRM.\n ".$ex->getMessage();
            $this->serviceUtil->insertError('TELCOS+',
                                            'ComercialCrmService.getComboVendedoresAction',
                                            $strMensajeError,
                                            $strUsuarioSession,
                                            $strIpCreacion);
        }
        $arrayResultado =  $arrayResponse;
        return $arrayResultado;	        
    }	
    
    /**
     * Documentación para el método crearServicio
     *
     * Funcion que permite agregar los servicios de formas masiva.
     *
     * @author David León <mdleon@telconet.ec>
     * @version 1.0 02-07-2021
     * 
     * @author David León <mdleon@telconet.ec>
     * @version 1.1 28-05-2022 - Se modifica para devolver datos del punto para realizar el pedido automatico.
     *
     * @param Array $arrayDatosWs[
     *                                  "strUsuario"         => Usuario que realiza la petición.
     *                                  "strCodEmpresa"      => Código de la empresa.
     *                                  "strPrefijoEmpresa"  => Prefijo de la empresa.
     *                                  "strIpCreacion"      => Ip de creación.
     *                                  "arrayPuntos"        => Listado de puntos a los que se le creara el servicio.
     *                                  "arrayDatos"         => Datos para el nuevo servicio.
     *                                  "strTipoOrden"       => Tipo de Orden (N-> nueva). 
     *                               ]
     *
     * @return array $arrayResultado [
     *                                 Servicios   =>  arreglo de los servicios creados con su respectivo punto.
     *                                 error       =>  mensaje de error.
     *                               ]
     */
    public function crearServicio($arrayDatosWs)
    {
        $strUsuario          = ( isset($arrayDatosWs['strUsuario']) && !empty($arrayDatosWs['strUsuario']) )
                                   ? $arrayDatosWs['strUsuario'] : 'TELCOS +';
        $intCodEmpresa       = ( isset($arrayDatosWs['strCodEmpresa']) && !empty($arrayDatosWs['strCodEmpresa']) )
                                   ? $arrayDatosWs['strCodEmpresa'] : 10;
        $strPrefijoEmpresa   = ( isset($arrayDatosWs['strPrefijoEmpresa']) && !empty($arrayDatosWs['strPrefijoEmpresa']) )
                                   ? $arrayDatosWs['strPrefijoEmpresa'] : 'TN';
        $strIpCreacion       = ( isset($arrayDatosWs['strIpCreacion']) && !empty($arrayDatosWs['strIpCreacion']) )
                                   ? $arrayDatosWs['strIpCreacion'] : '127.0.0.1';
        $arrayPuntos         = ( isset($arrayDatosWs['arrayPuntos']) && !empty($arrayDatosWs['arrayPuntos']) )? $arrayDatosWs['arrayPuntos'] : '';
        $arrayServicio       = ( isset($arrayDatosWs['arrayDatos']) && !empty($arrayDatosWs['arrayDatos']) )? $arrayDatosWs['arrayDatos'] : '';
        $strTipoOrden        = ( isset($arrayDatosWs['strTipoOrden']) && !empty($arrayDatosWs['strTipoOrden']) )? $arrayDatosWs['strTipoOrden'] : 'N';
        $strError            = "";
        $strEstado           = "OK";        
        $arrayPuntoServicios = array();
        try
        {
            if(empty($arrayPuntos))
            {
                throw new \Exception("No hay Puntos seleccionados, favor verificar");
            }
            //Buscamos los puntos del array
            foreach($arrayPuntos as $arrayPunto)
            {
                $objPunto = $this->emComercial->getRepository('schemaBundle:InfoPunto')->find($arrayPunto);
                
                if(is_object($objPunto) && !empty($objPunto))
                {
                    $objPersEmpRol = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                                                ->find($objPunto->getPersonaEmpresaRolId()->getId());
                    $objRol = $this->emComercial->getRepository('schemaBundle:AdmiRol')
                                                                           ->find($objPunto->getPersonaEmpresaRolId()->getEmpresaRolId()->getRolId());
                    
                    if(is_object($objPersEmpRol) && !empty($objPersEmpRol))
                    {
                        $intOficinaId  = $objPersEmpRol->getOficinaId()->getId();
                        $intIdProducto = $arrayServicio[0]['codigo'];
                        $intFrecuencia = $arrayServicio[0]['frecuencia'];
                        
                        $arrayParametrosCaracteristicas = array('intIdProducto'         => $intIdProducto,
                                                    'strDescCaracteristica' => 'FACTURACION_UNICA',
                                                    'strEstado'             => 'Activo');
                        $strEsFacturacionUnica = $this->serviceUtilidades->validarCaracteristicaProducto($arrayParametrosCaracteristicas);

                        $arrayParametrosCaracteristicas = array('intIdProducto'         => $intIdProducto,
                                                                'strDescCaracteristica' => 'RENTA_MENSUAL',
                                                                'strEstado'             => 'Activo');
                        $strEsRentaMensual = $this->serviceUtilidades->validarCaracteristicaProducto($arrayParametrosCaracteristicas);

                        $objAdmiProducto   = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->findOneById($intIdProducto);

                        if((!empty($strEsFacturacionUnica) && $strEsFacturacionUnica == "S" )
                            && (empty($strEsRentaMensual) || (!empty($strEsRentaMensual) && $strEsRentaMensual == "N"))
                          )
                        {
                            if($intFrecuencia != "0")
                            {                                                                               
                                $strError = 'No se puede agregar producto '. $objAdmiProducto->getDescripcionProducto().' ya que es de '
                                    . '[FACTURACION_UNICA] y la Frecuencia que debe escoger es [UNICA]';                                   
                            }
                        } 
                        else
                        {   if(!empty($strEsFacturacionUnica) && $strEsFacturacionUnica == "N"  && $intFrecuencia == "0")
                            {
                                $strError = 'No se puede agregar producto '. $objAdmiProducto->getDescripcionProducto(). ' ya que no es de '.
                                                '[FACTURACION_UNICA] no puede escoger Frecuencia [UNICA]';
                            }
                        }
                        if(empty($strError))
                        {
                            $arrayParamsServicio = array(   "codEmpresa"            => $intCodEmpresa,
                                                        "idOficina"             => $intOficinaId,
                                                        "entityPunto"           => $objPunto,
                                                        "entityRol"             => $objRol,
                                                        "usrCreacion"           => $strUsuario,
                                                        "clientIp"              => $strIpCreacion,
                                                        "tipoOrden"             => $strTipoOrden,
                                                        "ultimaMillaId"         => null,
                                                        "servicios"             => $arrayServicio,
                                                        "strPrefijoEmpresa"     => $strPrefijoEmpresa,
                                                        "session"               => null,
                                                        "intIdSolFlujoPP"       =>  0
                                                );
                        $arrayRespuesta = $this->serviceInfoServicio->crearServicio($arrayParamsServicio);
                        }
                    }
                    else
                    {
                        $strError = "No se encuentra Persona Empresa Rol del Punto seleccionado.";
                    }
                    if((!empty($strError) &&  $strError != "")|| !isset($arrayRespuesta['intIdServicio']))
                    {
                        $strEstado = 'Error';
                    }
                    else
                    {
                        $objServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($arrayRespuesta['intIdServicio']);
                        $this->emComercial->beginTransaction();
                        $objInfoServicioHistorial = new InfoServicioHistorial();
                        $objInfoServicioHistorial->setServicioId($objServicio);
                        $objInfoServicioHistorial->setObservacion("Servicio creado desde el TelcoCRM");
                        $objInfoServicioHistorial->setEstado($objServicio->getEstado());
                        $objInfoServicioHistorial->setUsrCreacion($strUsuario);
                        $objInfoServicioHistorial->setFeCreacion(new \DateTime('now'));
                        $objInfoServicioHistorial->setIpCreacion($strIpCreacion);
                        $this->emComercial->persist($objInfoServicioHistorial);
                        $this->emComercial->flush(); 
                        $this->emComercial->commit();
                    }
                    $arrayPuntoServicio = array('Punto'     => $objPunto->getLogin(),
                                                'Punto_id'  => $objPunto->getId(),
                                                'Latitud'   => $objPunto->getLatitud(),
                                                'Longitud'  => $objPunto->getLongitud(),
                                                'Producto'  => $objAdmiProducto->getDescripcionProducto(),
                                                'Servicio_id'  => $arrayRespuesta['intIdServicio'],
                                                'Estado'    => $strEstado,
                                                'Error'     => $strError,
                                                'Usuario'   => $strUsuario);
                    array_push($arrayPuntoServicios,$arrayPuntoServicio);
                }
            }
        }
        catch (\Exception $ex) 
        {
            $strMensajeError = "Falló la comunicación entre TelcoS+ y TelcoCRM.\n ".$ex->getMessage();
            $arrayPuntoServicios = array('status' => '500',
                                         'error'  => $strMensajeError
                                        );
            $this->serviceUtil->insertError('TELCOS+',
                                            'ComercialCrmService.crearServicio',
                                            $strMensajeError,
                                            $strUsuario,
                                            $strIpCreacion);
        }
        return $arrayPuntoServicios;
    }
        
     /**
     * Documentación para el método listaCiudades
     *
     * Funcion que permite listar las ciudades para el padre de facturación
     *
     * @author David León <mdleon@telconet.ec>
     * @version 1.0 28-10-2021
     *
     * @param Array $arrayDatosWs[
     *                                  "strNombre"         => nombre del Cantón.
     *                                  "intIdPais"         => Id del país.
     *                               ]
     *
     * @return array $arrayResultado [
     *                                 Id                 =>  Id del cantón.
     *                                 NombreCanton       =>  Nombre del cantón.
     *                               ]
     */
    public function listaCiudades($arrayDatosWs)
    {
        $strNombre           = ( isset($arrayDatosWs['strNombre']) && !empty($arrayDatosWs['strNombre']) )
                                   ? $arrayDatosWs['strNombre'] : '';
        $intIdPais          = ( isset($arrayDatosWs['intIdPais']) && !empty($arrayDatosWs['intIdPais']) )
                                   ? $arrayDatosWs['intIdPais'] : 1;
        $arrayParametros               = array();
        $arrayParametros['strNombre']  = $strNombre;
        $arrayParametros['intIdPais']  = $intIdPais;
        
        $arrayCiudad                        = $this->emComercial->getRepository('schemaBundle:AdmiCanton')->getCantonesPorNombre($arrayParametros);
        $arrayArreglo                       = array();
        if($arrayCiudad)
        {
            foreach ($arrayCiudad as $objDato):
                    $arrayArreglo[] = array('id'=> $objDato->getId(),'nombre'=>$objDato->getNombreCanton());
            endforeach;
        }
        return $arrayArreglo;
    }
    
    /**
     * Documentación para el método listaParroquias
     *
     * Funcion que permite listar las parroquias para el padre de facturación
     *
     * @author David León <mdleon@telconet.ec>
     * @version 1.0 28-10-2021
     *
     * @param Array $arrayDatosWs[
     *                                  "strNombre"         => nombre de la ciudad.
     *                                  "$intIdCanton"      => Id del Cantón.
     *                               ]
     *
     * @return array $arrayResultado 
     */
    public function listaParroquias($arrayDatosWs)
    {
        $strNombre           = ( isset($arrayDatosWs['strNombre']) && !empty($arrayDatosWs['strNombre']) )
                                   ? $arrayDatosWs['strNombre'] : '';
        $intIdCanton         = ( isset($arrayDatosWs['intIdCiudadFact']) && !empty($arrayDatosWs['intIdCiudadFact']) )
                                   ? $arrayDatosWs['intIdCiudadFact'] : 1;

        $arrayArreglo = $this->serviceCliente->obtenerParroquiasCanton($intIdCanton, $strNombre);
        return $arrayArreglo;	
        
    }   
    
    /**
     * Documentación para el método listaSectores
     *
     * Funcion que permite listar los sectores para el padre de facturación
     *
     * @author David León <mdleon@telconet.ec>
     * @version 1.0 28-10-2021
     *
     * @param Array $arrayDatosWs[
     *                                  "strNombre"         => nombre de la parroquia.
     *                                  "intIdParroquia"    => Id de la parroquia.
     *                                  "intCodEmpresa"     => codigo de la empresa
     *                               ]
     *
     * @return array $arrayResultado 
     */
    public function listaSectores($arrayDatosWs)
    {
        $strNombre           = ( isset($arrayDatosWs['strNombre']) && !empty($arrayDatosWs['strNombre']) )
                                   ? $arrayDatosWs['strNombre'] : '';
        $intIdParroquia      = ( isset($arrayDatosWs['intIdParroquia']) && !empty($arrayDatosWs['intIdParroquia']) )
                                   ? $arrayDatosWs['intIdParroquia'] : 1;
        $intCodEmpresa       = ( isset($arrayDatosWs['strCodEmpresa']) && !empty($arrayDatosWs['strCodEmpresa']) )
                                   ? $arrayDatosWs['strCodEmpresa'] : 10;
        
        $arrayArreglo = $this->serviceCliente->obtenerSectoresParroquia($intCodEmpresa,$intIdParroquia, $strNombre);
        return $arrayArreglo;	
        
    }
    
     /**
     * Documentación para el método NodosClientes
     *
     * Funcion que permite listar los edificios para la opción de Depende de Edificio.
     *
     * @author David León <mdleon@telconet.ec>
     * @version 1.0 28-10-2021
     *
     * @param Array $arrayDatosWs[
     *                                  "strNombreElemento"         => nombre del elemento o edificio.
     *                                  "strCanton"                 => Cantón.
     *                                  "strEstado"                 => Estado del elemento.
     *                                  "intIdEmpresa"              => Id Empresa.
     *                                  "strModeloElemento"         => Modelo del elemento.
     *                                  "strDireccion"              => Dirección.
     *                               ]
     *
     * @return array $arrayResultado 
     */
    public function nodosClientes($arrayDatosWs)
    {
        $strNombreElemento = ( isset($arrayDatosWs['strNombre']) && !empty($arrayDatosWs['strNombre']) )
                                   ? $arrayDatosWs['strNombre'] : '';
        $strCanton         = ( isset($arrayDatosWs['intIdCanton']) && !empty($arrayDatosWs['intIdCanton']) )
                                   ? $arrayDatosWs['intIdCanton'] : '';
        $strEstado         = ( isset($arrayDatosWs['strEstado']) && !empty($arrayDatosWs['strEstado']) )
                                   ? $arrayDatosWs['strEstado'] : 'Activo';
        $intIdEmpresa      = ( isset($arrayDatosWs['strCodEmpresa']) && !empty($arrayDatosWs['strCodEmpresa']) )
                                   ? $arrayDatosWs['strCodEmpresa'] : 10;
        $strModeloElemento = ( isset($arrayDatosWs['strModelo']) && !empty($arrayDatosWs['strModelo']) )
                                   ? $arrayDatosWs['strModelo'] : '';    
        $strDireccion      = ( isset($arrayDatosWs['strDireccion']) && !empty($arrayDatosWs['strDireccion']) )
                                   ? $arrayDatosWs['strDireccion'] : '';
        $strModelo      = "";
        
        $objSolicitud = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                          ->findOneBy(array('descripcionSolicitud' => 'SOLICITUD EDIFICACION',
                                                                            'estado' => 'Activo'));

        $arrayParametros = array();
        $arrayParametros["idSolicitud"]     = $objSolicitud->getId();
        $arrayParametros["codEmpresa"]      = $intIdEmpresa;
        $arrayParametros["nombreNodo"]      = $strNombreElemento;
        $arrayParametros["idCanton"]        = $strCanton;
        $arrayParametros["modeloElemento"]  = $strModeloElemento;
        $arrayParametros["estadoElemento"]  = $strEstado;
        $arrayParametros["direccion"]       = $strDireccion;
        
        $objModeloElemento = $this->emComercial->getRepository("schemaBundle:AdmiModeloElemento")->find($strModeloElemento);
        
        if(is_object($objModeloElemento))
        {
            $strModelo = $objModeloElemento->getNombreModeloElemento();
        }
        
        $arrayParametros["nombreModelo"]    = $strModelo;
        
        $arrayRespSolicitudes = $this->emComercial->getRepository('schemaBundle:InfoElemento')
                                                    ->getRegistrosEdificacion($arrayParametros);

        return $arrayRespSolicitudes;
    }
}

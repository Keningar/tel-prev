<?php
namespace telconet\comercialBundle\Service;

use telconet\schemaBundle\Entity\AdmiGrupoPromocion;
use telconet\schemaBundle\Entity\AdmiGrupoPromocionRegla;
use telconet\schemaBundle\Entity\AdmiGrupoPromocionHisto;
use telconet\schemaBundle\Entity\AdmiTipoPromocion;
use telconet\schemaBundle\Entity\AdmiTipoPromocionRegla;
use telconet\schemaBundle\Entity\AdmiTipoPlanProdPromocion;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class PromocionAnchoBandaService
{ 
    private $emcom;
    private $emInfraestructura;
    private $serviceUtil;
    private $emGeneral;
    private $servicePromocion;
    private $rdaMiddleware;
    private $rdaEjecutaComando;
    private $rdaEjecutaConfiguracion;
    
    public function setDependencies(Container $objContainer)
    {
        $this->emcom                        = $objContainer->get('doctrine.orm.telconet_entity_manager');
        $this->emInfraestructura            = $objContainer->get('doctrine.orm.telconet_infraestructura_entity_manager');
        $this->emGeneral                    = $objContainer->get('doctrine.orm.telconet_general_entity_manager');
        $this->serviceUtil                  = $objContainer->get('schema.Util');
        $this->servicePromocion             = $objContainer->get('comercial.Promocion');
        $this->rdaMiddleware                = $objContainer->get('tecnico.RedAccesoMiddleware');
        $this->rdaEjecutaComando            = $objContainer->getParameter('ws_rda_ejecuta_scripts');
        $this->rdaEjecutaConfiguracion      = $objContainer->getParameter('ws_rda_ejecuta_config');    
        $this->strUrsrComercial             = $objContainer->getParameter('user_comercial');
        $this->strPassComercial             = $objContainer->getParameter('passwd_comercial');
        $this->strDnsComercial              = $objContainer->getParameter('database_dsn');
    }    
    /**
    * crearPromoAnchoBanda, crea una promoción por ancho de banda enviando la siguiente información por parámetro.
    * @author José Candelario <jcandelario@telconet.ec>
    * @version 1.0 18-03-2019    
    * @param array $arrayParametros []
    *              'strNombrePromocion'      => Nombre de la promoción.
    *              'strFeIniVigencia'        => Fecha de Inicio de Vigencia de la promoción.
    *              'strFeFinVigencia'        => Fecha de Fin de Vigencia de la promoción.
    *              'strTipoNegocio'          => Ids de Tipos de Negocios separados por caracter(,).
    *              'strFormaPago'            => Ids de las Formas de Pago separadas por caracter(,).
    *              'strUltimaMilla'          => Ids de las últimas millas separadas por caracter(,).
    *              'strEstadoServicio'       => Estados del Servicio separado por caracter(,.
    *              'strPeriodo'              => Período separado por caracter(,).
    *              'strAntiguedad'           => Antigüedad del cliente.
    *              'strTipoCliente'          => Tipo de Cliente "Nuevo" ó "Existente".
    *              'strUsrCrea'              => Usuario en sesión.
    *              'strFeCreacion'           => Fecha en sesión.
    *              'strIpCreacion'           => Ip de creación.
    *              'strCodEmpresa'           => Código de empresa en sesión.
    *              'arrayEmisores'           => idTipoCuenta e idBanco serapado por caracter(|).
    *              'arrayPlanes'             => idPlan e idPlanSuperior serapado por caracter(|).
    *              'arraySectorizacion'      => idPlan de sectorizaciones.   
    * 
    * @author Katherine Yager <kyager@telconet.ec>
    * @version 1.1 12-10-2020 - Se agrega tipo promoción regla PROM_CODIGO.
    *
    * @author Daniel Reyes <djreyes@telconet.ec>
    * @version 1.2 01-12-2021 - Se mejoran las validaciones de objetos para que proceso no se caiga al guardar
    *                       la promocion, adicional se modifica para que estas promociones inicien en Pendiente.
    *
    * @return JsonResponse
    */
    public function crearPromoAnchoBanda($arrayParametros)
    {
        $strNombrePromocion     = $arrayParametros['strNombrePromocion'];
        $strFeIniVigencia       = $arrayParametros['strFeIniVigencia'];
        $strFeFinVigencia       = $arrayParametros['strFeFinVigencia'];
        $strTipoNegocio         = $arrayParametros['strTipoNegocio'];
        $strFormaPago           = $arrayParametros['strFormaPago'];
        $strUltimaMilla         = $arrayParametros['strUltimaMilla'];
        $strEstadoServicio      = $arrayParametros['strEstadoServicio'];
        $strPeriodo             = $arrayParametros['strPeriodo'];
        $strAntiguedad          = $arrayParametros['strAntiguedad'];
        $strTipoCliente         = $arrayParametros['strTipoCliente'];
        $strUsrCreacion         = $arrayParametros['strUsrCrea'];
        $strFeCreacion          = $arrayParametros['strFeCreacion'];
        $strIpCreacion          = $arrayParametros['strIpCreacion'];
        $strCodEmpresa          = $arrayParametros['strCodEmpresa'];
        $arrayEmisores          = $arrayParametros['arrayEmisores'];
        $arrayPlanes            = $arrayParametros['arrayPlanes'];
        $arraySectorizacion     = $arrayParametros['arraySectorizacion'];
        $strCodigoPromocion     = $arrayParametros['strCodigoPromocion'];
        $strTipoEdicion         = $arrayParametros['strTipoEdicion'];
        $intIdPromocionOrigen   = $arrayParametros['intIdPromocionOrigen'];
        $arrayDescomponerEmisor = [];
        $this->emcom->beginTransaction();
        try
        {
            $arrayParametroDet       = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                       ->getOne('PROM_TIPO_PROMOCIONES', 
                                                                'COMERCIAL', 
                                                                'ADMI_TIPO_PROMOCION',
                                                                '', 
                                                                'Descuento por Ancho de Banda', 
                                                                '', 
                                                                '', 
                                                                '', 
                                                                '', 
                                                                $strCodEmpresa);
            $strTipoPromocion       = ( isset($arrayParametroDet["valor1"]) && !empty($arrayParametroDet["valor1"]) )
                                        ? $arrayParametroDet["valor1"] : "";
            $strCodTipoPromocion    = ( isset($arrayParametroDet["valor2"]) && !empty($arrayParametroDet["valor2"]) )
                                        ? $arrayParametroDet["valor2"] : "";
            if($strTipoPromocion == '')
            {
                throw new \Exception("No se pudo crear Promoción, No se encontró el Tipo de Promoción parametizada."); 
            }
            if($strCodTipoPromocion == '')
            {
                throw new \Exception("No se pudo crear Promoción, No se encontró el Código Tipo de Promoción parametizada."); 
            }

            //verificar planes en otra promocion
            for ($intCont=0; $intCont<count($arrayPlanes); $intCont++)
            {
                $strDescomponerPlanes    = $arrayPlanes[$intCont];
                $arrayDescomponerPlanes  = explode("|", $strDescomponerPlanes);
                $intIdPlan               = $arrayDescomponerPlanes[0];
                $arrayParametrosCompPlan = array(
                    "intIdPromocion"     => 0,
                    "arraySectorizacion" => $arraySectorizacion,
                    "strFeIniVigencia"   => date_format(new \DateTime($strFeIniVigencia),'Y-m-d H:i'),
                    "strFeFinVigencia"   => date_format(new \DateTime($strFeFinVigencia),'Y-m-d H:i'),
                    "intIdPlan"          => $intIdPlan
                );
                $booleanCompPlan  = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')
                                                ->isExistePlanPromocionBw($arrayParametrosCompPlan);
                if($booleanCompPlan)
                {
                    throw new \Exception("No se pudo crear Promoción, Ya existen otras promociones con el mismo plan");
                }
            }

            $objAdmiGrupoPromocion  = new \telconet\schemaBundle\Entity\AdmiGrupoPromocion();
            $objInfoEmpresaGrupo    =$this->emcom->getRepository("schemaBundle:InfoEmpresaGrupo")
                                                 ->find($strCodEmpresa);
            if(!is_object($objInfoEmpresaGrupo))
            {
                throw new \Exception("No se pudo crear Promoción, No se encontró empresa en sesión");
            }
            // Obtenemos el estado inicial de la promocion
            $strEstadoInicial = 'Activo';
            $arrayDatosPromocion = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                        ->getOne('PROMOCION ANCHO BANDA', 'COMERCIAL','PROMO_ANCHO_BANDA',
                                            'Valores de inicio de la promocion',
                                            $strCodTipoPromocion,'','','','',
                                            $strCodEmpresa);
            if (!empty($arrayDatosPromocion))
            {
                $strEstadoInicial = $arrayDatosPromocion['valor2'];
                if (empty($strTipoCliente))
                {
                    $strTipoCliente = $arrayDatosPromocion['valor3'];
                }
                if (empty($strPeriodo))
                {
                    $strPeriodo = $arrayDatosPromocion['valor4'];
                }   
            }
            $objAdmiGrupoPromocion->setNombreGrupo(substr($strNombrePromocion, 0, 4000));
            $objAdmiGrupoPromocion->setFeInicioVigencia(new \DateTime($strFeIniVigencia));
            $objAdmiGrupoPromocion->setFeFinVigencia(new \DateTime($strFeFinVigencia));
            $objAdmiGrupoPromocion->setFeCreacion($strFeCreacion);
            $objAdmiGrupoPromocion->setUsrCreacion($strUsrCreacion);
            $objAdmiGrupoPromocion->setIpCreacion($strIpCreacion);
            $objAdmiGrupoPromocion->setEmpresaCod($objInfoEmpresaGrupo);
            $objAdmiGrupoPromocion->setEstado($strEstadoInicial );
            if($strTipoEdicion=='ED')
            {
                $objAdmiGrupoPromocion->setGrupoPromocionId($intIdPromocionOrigen);
         
            }
            $this->emcom->persist($objAdmiGrupoPromocion);
            $this->emcom->flush();
            $objAdmiTipoPromocion   = new \telconet\schemaBundle\Entity\AdmiTipoPromocion();
            $objAdmiTipoPromocion->setGrupoPromocionId($objAdmiGrupoPromocion);
            $objAdmiTipoPromocion->setCodigoTipoPromocion($strCodTipoPromocion);
            $objAdmiTipoPromocion->setTipo($strTipoPromocion);
            $objAdmiTipoPromocion->setFeCreacion($strFeCreacion);
            $objAdmiTipoPromocion->setUsrCreacion($strUsrCreacion);
            $objAdmiTipoPromocion->setIpCreacion($strIpCreacion);
            $objAdmiTipoPromocion->setEstado('Activo');
            $this->emcom->persist($objAdmiTipoPromocion);
            $this->emcom->flush();
            $arrayParamPromoGrupoHisto = array('intIdGrupoPromocion' => $objAdmiGrupoPromocion->getId(),
                                               'strFeCreacion'       => $strFeCreacion,
                                               'strUsrCreacion'      => $strUsrCreacion,
                                               'strIpCreacion'       => $strIpCreacion,
                                               'strObservacion'      => 'Se Creó nueva Promoción '.$strTipoPromocion,
                                               'strEstado'           => 'Activo');
            $this->servicePromocion->ingresarPromocionGrupoHisto($arrayParamPromoGrupoHisto);
            
            
              if($strTipoEdicion=='ED')
            {
                 $arrayParamReglas = array('intIdTipoPromocion'  => $objAdmiTipoPromocion->getId(),
                                                  'strRegla'            => 'ORIGEN_PROMOCION_EDITADA',
                                                  'strValorRegla'       => $intIdPromocionOrigen,
                                                  'strFeCreacion'       => $strFeCreacion,
                                                  'strUsrCreacion'      => $strUsrCreacion,
                                                  'strIpCreacion'       => $strIpCreacion
                                                 );
                  $this->servicePromocion->ingresarTipoPromocionRegla($arrayParamReglas); 
            }

            if(isset($arraySectorizacion) && !empty($arraySectorizacion))
            {
                foreach($arraySectorizacion as $objSectorizacion)
                {
                    $arraySecuencia     = $this->emcom->getRepository("schemaBundle:AdmiGrupoPromocion")
                                                      ->creaSecuencia();
                    $intNumeroSecuencia = $arraySecuencia['secuencia'];

                    if(isset($objSectorizacion['intJurisdiccion']) && !empty($objSectorizacion['intJurisdiccion']) 
                       && $objSectorizacion['intJurisdiccion'] !== "0")
                    {
                        $arrayParamReglas = array('intIdTipoPromocion'  => $objAdmiTipoPromocion->getId(),
                                                  'strRegla'            => 'PROM_JURISDICCION',
                                                  'strValorRegla'       => $objSectorizacion['intJurisdiccion'],
                                                  'strFeCreacion'       => $strFeCreacion,
                                                  'strUsrCreacion'      => $strUsrCreacion,
                                                  'strIpCreacion'       => $strIpCreacion,
                                                  'intNumeroSecuencia'  => $intNumeroSecuencia);
                        $this->servicePromocion->ingresarTipoPromocionRegla($arrayParamReglas);
                    }
                    if(isset($objSectorizacion['intCanton']) && !empty($objSectorizacion['intCanton']) 
                       && $objSectorizacion['intCanton'] !== "0")
                    {
                        $arrayParamReglas = array('intIdTipoPromocion'  => $objAdmiTipoPromocion->getId(),
                                                  'strRegla'            => 'PROM_CANTON',
                                                  'strValorRegla'       => $objSectorizacion['intCanton'],
                                                  'strFeCreacion'       => $strFeCreacion,
                                                  'strUsrCreacion'      => $strUsrCreacion,
                                                  'strIpCreacion'       => $strIpCreacion,
                                                  'intNumeroSecuencia'  => $intNumeroSecuencia);
                        $this->servicePromocion->ingresarTipoPromocionRegla($arrayParamReglas);
                    }
                    if(isset($objSectorizacion['intParroquia']) && !empty($objSectorizacion['intParroquia']) 
                       && $objSectorizacion['intParroquia'] !== "0")
                    {
                        $arrayParamReglas = array('intIdTipoPromocion'  => $objAdmiTipoPromocion->getId(),
                                                  'strRegla'            => 'PROM_PARROQUIA',
                                                  'strValorRegla'       => $objSectorizacion['intParroquia'],
                                                  'strFeCreacion'       => $strFeCreacion,
                                                  'strUsrCreacion'      => $strUsrCreacion,
                                                  'strIpCreacion'       => $strIpCreacion,
                                                  'intNumeroSecuencia'  => $intNumeroSecuencia);
                        $this->servicePromocion->ingresarTipoPromocionRegla($arrayParamReglas);
                    }
                    if(isset($objSectorizacion['strOptSectOltEdif']) && !empty($objSectorizacion['strOptSectOltEdif']) 
                       && $objSectorizacion['strOptSectOltEdif'] !== "TODOS")
                    {
                        $strReglaPromocion = "";
                        if($objSectorizacion['strOptSectOltEdif'] === 'sector')
                        {
                            $strReglaPromocion = 'PROM_SECTOR';
                        }
                        elseif($objSectorizacion['strOptSectOltEdif'] === 'olt')
                        {
                            $strReglaPromocion = 'PROM_ELEMENTO';
                        }
                        else
                        {
                            $strReglaPromocion = 'PROM_EDIFICIO';
                        }
                        $arrayParamReglas = array('intIdTipoPromocion'  => $objAdmiTipoPromocion->getId(),
                                                  'strRegla'            => $strReglaPromocion,
                                                  'strValorRegla'       => $objSectorizacion['intSectOltEdif'],
                                                  'strFeCreacion'       => $strFeCreacion,
                                                  'strUsrCreacion'      => $strUsrCreacion,
                                                  'strIpCreacion'       => $strIpCreacion,
                                                  'intNumeroSecuencia'  => $intNumeroSecuencia);
                        $this->servicePromocion->ingresarTipoPromocionRegla($arrayParamReglas);
                    }
                }
            }
            
            if(isset($strCodigoPromocion) && !empty($strCodigoPromocion))
            {
                $arrayParamReglas = array('intIdTipoPromocion'  => $objAdmiTipoPromocion->getId(),
                                            'strRegla'            => 'PROM_CODIGO',
                                            'strValorRegla'       => $strCodigoPromocion,
                                            'strFeCreacion'       => $strFeCreacion,
                                            'strUsrCreacion'      => $strUsrCreacion,
                                            'strIpCreacion'       => $strIpCreacion
                                           );
                $this->servicePromocion->ingresarTipoPromocionRegla($arrayParamReglas);
            }
            if(isset($strTipoNegocio) && !empty($strTipoNegocio))
            {
                $arrayParamReglaTipoNegocio = array('intIdTipoPromocion'  => $objAdmiTipoPromocion->getId(),
                                                    'strRegla'            => 'PROM_TIPO_NEGOCIO',
                                                    'strValorRegla'       => $strTipoNegocio,
                                                    'strFeCreacion'       => $strFeCreacion,
                                                    'strUsrCreacion'      => $strUsrCreacion,
                                                    'strIpCreacion'       => $strIpCreacion);
                $this->servicePromocion->ingresarTipoPromocionRegla($arrayParamReglaTipoNegocio);
            }
            if(isset($strPeriodo) && !empty($strPeriodo))
            {
                $arrayParamReglaPeriodo = array('intIdTipoPromocion'  => $objAdmiTipoPromocion->getId(),
                                                'strRegla'            => 'PROM_PERIODO',
                                                'strValorRegla'       => $strPeriodo,
                                                'strFeCreacion'       => $strFeCreacion,
                                                'strUsrCreacion'      => $strUsrCreacion,
                                                'strIpCreacion'       => $strIpCreacion);
                $this->servicePromocion->ingresarTipoPromocionRegla($arrayParamReglaPeriodo);
            }
            if(isset($strUltimaMilla) && !empty($strUltimaMilla))
            {
                $arrayParamReglaUltimaMilla = array('intIdTipoPromocion'  => $objAdmiTipoPromocion->getId(),
                                                    'strRegla'            => 'PROM_ULTIMA_MILLA',
                                                    'strValorRegla'       => $strUltimaMilla,
                                                    'strFeCreacion'       => $strFeCreacion,
                                                    'strUsrCreacion'      => $strUsrCreacion,
                                                    'strIpCreacion'       => $strIpCreacion,
                                                );
                $this->servicePromocion->ingresarTipoPromocionRegla($arrayParamReglaUltimaMilla);
            }
            if(isset($strFormaPago) && !empty($strFormaPago))
            {
                $arrayParamReglaformaPago = array('intIdTipoPromocion'  => $objAdmiTipoPromocion->getId(),
                                                'strRegla'            => 'PROM_FORMA_PAGO',
                                                'strValorRegla'       => $strFormaPago,
                                                'strFeCreacion'       => $strFeCreacion,
                                                'strUsrCreacion'      => $strUsrCreacion,
                                                'strIpCreacion'       => $strIpCreacion);
                $this->servicePromocion->ingresarTipoPromocionRegla($arrayParamReglaformaPago);
            }
            if(isset($strAntiguedad) && !empty($strAntiguedad))
            {
                $arrayParamReglas = array('intIdTipoPromocion'  => $objAdmiTipoPromocion->getId(),
                                          'strRegla'            => 'PROM_ANTIGUEDAD',
                                          'strValorRegla'       => $strAntiguedad,
                                          'strFeCreacion'       => $strFeCreacion,
                                          'strUsrCreacion'      => $strUsrCreacion,
                                          'strIpCreacion'       => $strIpCreacion);
                $this->servicePromocion->ingresarTipoPromocionRegla($arrayParamReglas);
            }           
            if(isset($strEstadoServicio) && !empty($strEstadoServicio))
            {
                $arrayParamReglas = array('intIdTipoPromocion' => $objAdmiTipoPromocion->getId(),
                                          'strRegla'           => 'PROM_ESTADO_SERVICIO',
                                          'strValorRegla'      => $strEstadoServicio,
                                          'strFeCreacion'      => $strFeCreacion,
                                          'strUsrCreacion'     => $strUsrCreacion,
                                          'strIpCreacion'      => $strIpCreacion);
                $this->servicePromocion->ingresarTipoPromocionRegla($arrayParamReglas);
            }
            if(isset($strTipoCliente) && !empty($strTipoCliente))
            {
                $arrayParamReglas = array('intIdTipoPromocion' => $objAdmiTipoPromocion->getId(),
                                          'strRegla'            => 'PROM_TIPO_CLIENTE',
                                          'strValorRegla'       => $strTipoCliente,
                                          'strFeCreacion'       => $strFeCreacion,
                                          'strUsrCreacion'      => $strUsrCreacion,
                                          'strIpCreacion'       => $strIpCreacion);
                $this->servicePromocion->ingresarTipoPromocionRegla($arrayParamReglas);
            }
            if(isset($arrayEmisores) && !empty($arrayEmisores))
            {
                $arrayParametros           = array();
                $arrayParamBancoTipoCuenta = array();
                $arrayBancoTipoCuenta      = array();
                $arrayIdBancoTipoCuenta    = [];
                for ($i=0; $i<count($arrayEmisores); $i++)
                {
                    $strDescomponerEmisor           = ($arrayEmisores[$i]);
                    $arrayDescomponerEmisor         = explode("|", $strDescomponerEmisor);
                    $strIdCuenta                    = $arrayDescomponerEmisor[0];
                    $strIdBanco                     = $arrayDescomponerEmisor[1];
                    $arrayParametros['strIdCuenta'] = $strIdCuenta;
                    if($strIdBanco === '0')
                    {
                        $arrayParametros['strIdBanco']     = '';
                        $arrayParametros['strEsTarjeta']   = 'S';
                    }else
                    {
                        $arrayParametros['strIdBanco']     = $strIdBanco;
                        $arrayParametros['strEsTarjeta']   = 'N';
                    }
                    $arrayParamBancoTipoCuenta = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')
                                                      ->getBancoTipoCuenta($arrayParametros);
                    $arrayBancoTipoCuenta = array_merge($arrayBancoTipoCuenta, $arrayParamBancoTipoCuenta);
                }
                foreach($arrayBancoTipoCuenta as $strIdBancoTipoCuenta)
                {
                    $arrayIdBancoTipoCuenta[] = $strIdBancoTipoCuenta['idBancoTipoCuenta'];
                }
                $strIdBancoTipoCuenta   = implode(",", $arrayIdBancoTipoCuenta);
                $arrayParamReglas       = array('intIdTipoPromocion' => $objAdmiTipoPromocion->getId(),
                                                'strRegla'           => 'PROM_EMISOR',
                                                'strValorRegla'      => $strIdBancoTipoCuenta,
                                                'strFeCreacion'      => $strFeCreacion,
                                                'strUsrCreacion'     => $strUsrCreacion,
                                                'strIpCreacion'      => $strIpCreacion);
                $this->servicePromocion->ingresarTipoPromocionRegla($arrayParamReglas);
            }
            for ($i=0; $i<count($arrayPlanes); $i++)
            {
                $strDescomponerPlanes    = $arrayPlanes[$i];
                $arrayDescomponerPlanes  = explode("|", $strDescomponerPlanes);
                $intIdPlan               = $arrayDescomponerPlanes[0];
                $intIdPlanSuperior       = $arrayDescomponerPlanes[1];
                $arrayParamTipoPlanPromo = array('intIdTipoPromocion' => $objAdmiTipoPromocion->getId(),
                                                 'intIdPlan'          => $intIdPlan,
                                                 'intIdPlanSuperior'  => $intIdPlanSuperior,
                                                 'strValorRegla'      => $strEstadoServicio,
                                                 'strFeCreacion'      => $strFeCreacion,
                                                 'strUsrCreacion'     => $strUsrCreacion,
                                                 'strIpCreacion'      => $strIpCreacion);
                $this->ingresarTipoPlanPromocion($arrayParamTipoPlanPromo);
            }
            $strRespuesta = 'OK';
            $this->emcom->getConnection()->commit();
        }
        catch(\Exception $e)
        {
            $this->emcom->rollback();
            $this->emcom->close();
            $strRespuesta = "No se pudo guardar Promoción de Ancho de Banda <br>". $e->getMessage() . ". Favor notificar a Sistemas.";
            $this->serviceUtil->insertError('Telcos+',
                                            'PromocionService.crearPromoAnchoBanda',
                                             'Error PromocionService.crearPromoAnchoBanda:'.$e->getMessage(),
                                             $strUsrCreacion,
                                             $strIpCreacion);
            return str_replace(' ','_',$e->getMessage());
        }
        return $strRespuesta;
    }
    /**
    * editarPromoAnchoBanda, actualiza una promoción por ancho de banda enviando la siguiente información por parámetro.
    * @author José Candelario <jcandelario@telconet.ec>
    * @version 1.0 18-03-2019    
    * @param array $arrayParametros []
    *              'intIdPromocion'          => Id de la promoción.
    *              'strNombrePromocion'      => Nombre de la promoción.
    *              'strFeIniVigencia'        => Fecha de Inicio de Vigencia de la promoción.
    *              'strFeFinVigencia'        => Fecha de Fin de Vigencia de la promoción.
    *              'strTipoNegocio'          => Ids de Tipos de Negocios separados por caracter(,).
    *              'strFormaPago'            => Ids de las Formas de Pago separadas por caracter(,).
    *              'strUltimaMilla'          => Ids de las últimas millas separadas por caracter(,).
    *              'strEstadoServicio'       => Estados del Servicio separado por caracter(,).
    *              'strPeriodo'              => Período separado por caracter(,).
    *              'strAntiguedad'           => Antigüedad del cliente.
    *              'strTipoCliente'          => Tipo de Cliente "Nuevo" ó "Existente".
    *              'strUsrMod'               => Usuario en sesión.
    *              'strFeMod'                => Fecha en sesión.
    *              'strIpMod'                => Ip de creación.
    *              'strCodEmpresa'           => Código de empresa en sesión.
    *              'arrayEmisores'           => idTipoCuenta e idBanco serapado por caracter(|).
    *              'arrayPlanes'             => idPlan e idPlanSuperior serapado por caracter(|).
    *              'arraySectorizacion'      => idPlan de sectorizaciones.
    * @return JsonResponse
    * 
    * @author Katherine Yager <kyager@telconet.ec>
    * @version 1.3 24-11-2020 - Se modifica función para guardado de códigos promocionales.
    *
    * @author Daniel Reyes <djreyes@telconet.ec>
    * @version 1.4 01-12-2021 - Se mejoran las validaciones de objetos para evitar se caiga el proceso si no traen informacion,
    *                           y se pueda actualizar la promocion sin problemas.
    *
    * @author Daniel Reyes <djreyes@telconet.ec>
    * @version 1.5 12-05-2022 - Se mejora validacion para el estado inicial de los promociones editadas cuando vienen por clonacion.
    *
    */
    public function editarPromoAnchoBanda($arrayParametros)
    {
        $intIdPromocion         = $arrayParametros['intIdPromocion'];
        $strNombrePromocion     = $arrayParametros['strNombrePromocion'];
        $strFeIniVigencia       = $arrayParametros['strFeIniVigencia'];
        $strFeFinVigencia       = $arrayParametros['strFeFinVigencia'];
        $strTipoNegocio         = $arrayParametros['strTipoNegocio'];
        $strFormaPago           = $arrayParametros['strFormaPago'];
        $strUltimaMilla         = $arrayParametros['strUltimaMilla'];
        $strEstadoServicio      = $arrayParametros['strEstadoServicio'];
        $strPeriodo             = $arrayParametros['strPeriodo'];
        $strAntiguedad          = $arrayParametros['strAntiguedad'];
        $strTipoCliente         = $arrayParametros['strTipoCliente'];
        $strUsrMod              = $arrayParametros['strUsrMod'];
        $strFeMod               = $arrayParametros['strFeMod'];
        $strIpMod               = $arrayParametros['strIpMod'];
        $strCodEmpresa          = $arrayParametros['strCodEmpresa'];
        $arrayEmisores          = $arrayParametros['arrayEmisores'];
        $arrayPlanes            = $arrayParametros['arrayPlanes'];
        $arraySectorizacion     = $arrayParametros['arraySectorizacion'];
        $strCodigoPromocion     = $arrayParametros['strCodigoPromocion'];
        $strCodigoPromocionIng  = $arrayParametros['strCodigoPromocionIng'];
        $arrayDescomponerEmisor = [];
        $this->emcom->beginTransaction();
        try
        {
            $arrayParametroDet  = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                  ->getOne(
                                                           'PROM_TIPO_PROMOCIONES', 
                                                           'COMERCIAL', 
                                                           'ADMI_TIPO_PROMOCION',
                                                           '', 
                                                           'Descuento por Ancho de Banda', 
                                                           '', 
                                                           '', 
                                                           '', 
                                                           '', 
                                                           $strCodEmpresa);
            $strTipoPromocion   = ( isset($arrayParametroDet["valor2"]) && !empty($arrayParametroDet["valor2"]) )
                                          ? $arrayParametroDet["valor2"] : "";
            if($strTipoPromocion === '')
            {
                throw new \Exception("No se pudo editar Promoción, No se encontró el Tipo de Promoción parametizada."); 
            }

            //verificar planes en otra promocion
            for ($intCont=0; $intCont<count($arrayPlanes); $intCont++)
            {
                $strDescomponerPlanes    = $arrayPlanes[$intCont];
                $arrayDescomponerPlanes  = explode("|", $strDescomponerPlanes);
                $intIdPlan               = $arrayDescomponerPlanes[0];
                $arrayParametrosCompPlan = array(
                    "intIdPromocion"     => $intIdPromocion,
                    "arraySectorizacion" => $arraySectorizacion,
                    "strFeIniVigencia"   => date_format(new \DateTime($strFeIniVigencia),'Y-m-d H:i'),
                    "strFeFinVigencia"   => date_format(new \DateTime($strFeFinVigencia),'Y-m-d H:i'),
                    "intIdPlan"          => $intIdPlan
                );
                $booleanCompPlan = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')
                                                ->isExistePlanPromocionBw($arrayParametrosCompPlan);
                if($booleanCompPlan)
                {
                    throw new \Exception("No se pudo guardar la Promoción, Ya existen otras promociones con el mismo plan");
                }
            }

            $objAdmiGrupoPromocion = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')
                                                 ->find($intIdPromocion);
            if(is_object($objAdmiGrupoPromocion))
            {
                $objAdmiGrupoPromocion->setNombreGrupo(substr($strNombrePromocion, 0, 4000));
                $objAdmiGrupoPromocion->setFeInicioVigencia(new \DateTime($strFeIniVigencia));
                $objAdmiGrupoPromocion->setFeFinVigencia(new \DateTime($strFeFinVigencia));
                $objAdmiGrupoPromocion->setFeUltMod($strFeMod);
                $objAdmiGrupoPromocion->setUsrUltMod($strUsrMod);
                $objAdmiGrupoPromocion->setIpUltMod($strIpMod);
                // Se validara el estado actual de la promocion y a que estado debe pasar
                $arrayParametroDet = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                        ->getOne('PROMOCION ANCHO BANDA', 'COMERCIAL','PROMO_ANCHO_BANDA',
                                                'Estado inicial para editar promociones',
                                                $strTipoPromocion,'EDITAR',
                                                $objAdmiGrupoPromocion->getEstado(),
                                                '','',
                                                $strCodEmpresa);
                if(!empty($arrayParametroDet))
                {
                    $objAdmiGrupoPromocion->setEstado($arrayParametroDet['valor4']);
                }
                $this->emcom->persist($objAdmiGrupoPromocion);
                $this->emcom->flush();
            }
            $objAdmiTipoPromocion = $this->emcom->getRepository("schemaBundle:AdmiTipoPromocion")
                                                ->findOneBy(array('grupoPromocionId' => $objAdmiGrupoPromocion->getId()));
            if(is_object($objAdmiTipoPromocion))
            {
                $objAdmiTipoPromocion->setFeUltMod($strFeMod);
                $objAdmiTipoPromocion->setUsrUltMod($strUsrMod);
                $objAdmiTipoPromocion->setIpUltMod($strIpMod);
                if($objAdmiTipoPromocion->getEstado() == 'Pendiente')
                {
                    $objAdmiTipoPromocion->setEstado('Activo');
                }
                $this->emcom->persist($objAdmiTipoPromocion);
                $this->emcom->flush();
            }
            $arrayParamPromoGrupoHisto = array('intIdGrupoPromocion' => $objAdmiGrupoPromocion->getId(),
                                               'strFeCreacion'       => $strFeMod,
                                               'strUsrCreacion'      => $strUsrMod,
                                               'strIpCreacion'       => $strIpMod,
                                               'strObservacion'      => 'Se Modifica Promoción '.$strTipoPromocion,
                                               'strEstado'           => $objAdmiGrupoPromocion->getEstado());
            $this->servicePromocion->ingresarPromocionGrupoHisto($arrayParamPromoGrupoHisto);
            
            $arrayParamSectorizacion['arraySectorizacion']  = array('PROM_JURISDICCION',
                                                                    'PROM_CANTON',
                                                                    'PROM_PARROQUIA',
                                                                    'PROM_SECTOR',
                                                                    'PROM_ELEMENTO',
                                                                    'PROM_EDIFICIO');
            $arrayParamSectorizacion['intIdTipoPromocion']  = $objAdmiTipoPromocion->getId();
            $arrayReglasSectorizacionByTipo                 = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')
                                                                          ->getTipoPromocionReglaSectorizacion($arrayParamSectorizacion);

            foreach($arrayReglasSectorizacionByTipo as $objReglasSectorizacionByTipo)
            {
                $objReglasSectorizacionByTipo->setEstado('Eliminado');
                $this->emcom->persist($objReglasSectorizacionByTipo);
            }
            if(isset($arraySectorizacion) && !empty($arraySectorizacion))
            {
                foreach($arraySectorizacion as $objSectorizacion)
                {
                    if($objSectorizacion['intSectorizacion'] === "0")
                    {
                        $arraySecuencia     = $this->emcom->getRepository("schemaBundle:AdmiGrupoPromocion")->creaSecuencia();
                        $intNumeroSecuencia = $arraySecuencia['secuencia'];
                        $strAccion          = 'NUEVO';
                    }
                    else
                    {
                        $intNumeroSecuencia = $objSectorizacion['intSectorizacion'];
                        $strAccion          = 'EDITAR';
                    }
                    if(isset($objSectorizacion['intJurisdiccion']) && !empty($objSectorizacion['intJurisdiccion']) 
                       && $objSectorizacion['intJurisdiccion'] !== "0")
                    {
                        $arrayParamReglas = array('intIdTipoPromocion'  => $objAdmiTipoPromocion->getId(),
                                                  'strRegla'            => 'PROM_JURISDICCION',
                                                  'strAccion'           => $strAccion,
                                                  'strValorRegla'       => $objSectorizacion['intJurisdiccion'],
                                                  'strFeCreacion'       => $strFeMod,
                                                  'strUsrCreacion'      => $strUsrMod,
                                                  'strIpCreacion'       => $strIpMod,
                                                  'intNumeroSecuencia'  => $intNumeroSecuencia);
                        $this->servicePromocion->actualizarTipoPromocionReglaSectorizacion($arrayParamReglas);
                    }
                    if(isset($objSectorizacion['intCanton']) && !empty($objSectorizacion['intCanton']) 
                       && $objSectorizacion['intCanton'] !== "0")
                    {
                        $arrayParamReglas = array('intIdTipoPromocion'  => $objAdmiTipoPromocion->getId(),
                                                  'strRegla'            => 'PROM_CANTON',
                                                  'strAccion'           => $strAccion,
                                                  'strValorRegla'       => $objSectorizacion['intCanton'],
                                                  'strFeCreacion'       => $strFeMod,
                                                  'strUsrCreacion'      => $strUsrMod,
                                                  'strIpCreacion'       => $strIpMod,
                                                  'intNumeroSecuencia'  => $intNumeroSecuencia);
                        $this->servicePromocion->actualizarTipoPromocionReglaSectorizacion($arrayParamReglas);
                    }
                    if(isset($objSectorizacion['intParroquia']) && !empty($objSectorizacion['intParroquia']) 
                       && $objSectorizacion['intParroquia'] !== "0")
                    {
                        $arrayParamReglas = array('intIdTipoPromocion'  => $objAdmiTipoPromocion->getId(),
                                                  'strRegla'            => 'PROM_PARROQUIA',
                                                  'strAccion'           => $strAccion,
                                                  'strValorRegla'       => $objSectorizacion['intParroquia'],
                                                  'strFeCreacion'       => $strFeMod,
                                                  'strUsrCreacion'      => $strUsrMod,
                                                  'strIpCreacion'       => $strIpMod,
                                                  'intNumeroSecuencia'  => $intNumeroSecuencia);
                        $this->servicePromocion->actualizarTipoPromocionReglaSectorizacion($arrayParamReglas);
                    }
                    if(isset($objSectorizacion['strOptSectOltEdif']) && !empty($objSectorizacion['strOptSectOltEdif']) 
                       && $objSectorizacion['strOptSectOltEdif'] !== "TODOS")
                    {
                        $strReglaPromocion = "";
                        if($objSectorizacion['strOptSectOltEdif'] === 'sector')
                        {
                            $strReglaPromocion = 'PROM_SECTOR';
                        }
                        elseif($objSectorizacion['strOptSectOltEdif'] === 'olt')
                        {
                            $strReglaPromocion = 'PROM_ELEMENTO';
                        }
                        else
                        {
                            $strReglaPromocion = 'PROM_EDIFICIO';
                        }
                        $arrayParamReglas = array('intIdTipoPromocion'  => $objAdmiTipoPromocion->getId(),
                                                  'strRegla'            => $strReglaPromocion,
                                                  'strAccion'           => $strAccion,
                                                  'strValorRegla'       => $objSectorizacion['intSectOltEdif'],
                                                  'strFeCreacion'       => $strFeMod,
                                                  'strUsrCreacion'      => $strUsrMod,
                                                  'strIpCreacion'       => $strIpMod,
                                                  'intNumeroSecuencia'  => $intNumeroSecuencia);
                        $this->servicePromocion->actualizarTipoPromocionReglaSectorizacion($arrayParamReglas);
                    }
                }
            }

            if($strCodigoPromocionIng=='S' &&  (isset($strCodigoPromocion) && !empty($strCodigoPromocion)))
            {
                $strAccion='EDITAR';
                $arrayParamReglaTipoNegocio = array('intIdTipoPromocion'  => $objAdmiTipoPromocion->getId(),
                                                'strRegla'            => 'PROM_CODIGO',
                                                'strAccion'           => $strAccion,
                                                'strValorRegla'       => $strCodigoPromocion,
                                                'strFeCreacion'       => $strFeMod,
                                                'strUsrCreacion'      => $strUsrMod,
                                                'strIpCreacion'       => $strIpMod);
                $this->servicePromocion->actualizarTipoPromocionRegla($arrayParamReglaTipoNegocio);
           
            }  
            else if ($strCodigoPromocionIng=='N' && (isset($strCodigoPromocion) && !empty($strCodigoPromocion)))
            {
                 
                  $arrayParamReglas = array('intIdTipoPromocion'  => $objAdmiTipoPromocion->getId(),
                                                  'strRegla'            => 'PROM_CODIGO',
                                                  'strValorRegla'       => $strCodigoPromocion,
                                                  'strFeCreacion'       => $strFeMod,
                                                  'strUsrCreacion'      => $strUsrMod,
                                                  'strIpCreacion'       => $strIpMod
                                                 );
                $this->servicePromocion->ingresarTipoPromocionRegla($arrayParamReglas);
                 
            }
            else if ($strCodigoPromocionIng=='S' && empty($strCodigoPromocion))
            {
                $strAccion='ELIMINAR';
                $arrayParamReglaTipoNegocio = array('intIdTipoPromocion'  => $objAdmiTipoPromocion->getId(),
                                                'strRegla'            => 'PROM_CODIGO',
                                                'strAccion'           => $strAccion,
                                                'strFeCreacion'       => $strFeMod,
                                                'strUsrCreacion'      => $strUsrMod,
                                                'strIpCreacion'       => $strIpMod);
                $this->servicePromocion->actualizarTipoPromocionRegla($arrayParamReglaTipoNegocio);
            }

            if (isset($strTipoNegocio) && !empty($strTipoNegocio))
            {
                $strAccion = ( isset($strTipoNegocio) && !empty($strTipoNegocio) ) ? 'EDITAR' : 'ELIMINAR';
                $arrayParamReglaTipoNegocio = array('intIdTipoPromocion'  => $objAdmiTipoPromocion->getId(),
                                                    'strRegla'            => 'PROM_TIPO_NEGOCIO',
                                                    'strAccion'           => $strAccion,
                                                    'strValorRegla'       => $strTipoNegocio,
                                                    'strFeCreacion'       => $strFeMod,
                                                    'strUsrCreacion'      => $strUsrMod,
                                                    'strIpCreacion'       => $strIpMod);
                $this->servicePromocion->actualizarTipoPromocionRegla($arrayParamReglaTipoNegocio);
            }
            
            if (isset($strPeriodo) && !empty($strPeriodo))
            {
                $strAccion = ( isset($strPeriodo) && !empty($strPeriodo) ) ? 'EDITAR' : 'ELIMINAR';
                $arrayParamReglaPeriodo = array('intIdTipoPromocion'  => $objAdmiTipoPromocion->getId(),
                                                'strRegla'            => 'PROM_PERIODO',
                                                'strAccion'           => $strAccion,
                                                'strValorRegla'       => $strPeriodo,
                                                'strFeCreacion'       => $strFeMod,
                                                'strUsrCreacion'      => $strUsrMod,
                                                'strIpCreacion'       => $strIpMod);
                $this->servicePromocion->actualizarTipoPromocionRegla($arrayParamReglaPeriodo);
            }
            
            if (isset($strUltimaMilla) && !empty($strUltimaMilla))
            {
                $strAccion = ( isset($strUltimaMilla) && !empty($strUltimaMilla) ) ? 'EDITAR' : 'ELIMINAR';
                $arrayParamReglasUltimaMilla = array('intIdTipoPromocion'  => $objAdmiTipoPromocion->getId(),
                                                    'strRegla'            => 'PROM_ULTIMA_MILLA',
                                                    'strAccion'           => $strAccion,
                                                    'strValorRegla'       => $strUltimaMilla,
                                                    'strFeCreacion'       => $strFeMod,
                                                    'strUsrCreacion'      => $strUsrMod,
                                                    'strIpCreacion'       => $strIpMod);
                $this->servicePromocion->actualizarTipoPromocionRegla($arrayParamReglasUltimaMilla);
            }
            
            if (isset($strFormaPago) && !empty($strFormaPago))
            {
                $strAccion = ( isset($strFormaPago) && !empty($strFormaPago) ) ? 'EDITAR' : 'ELIMINAR';
                $arrayParamReglaFormaPago = array('intIdTipoPromocion'  => $objAdmiTipoPromocion->getId(),
                                                'strRegla'            => 'PROM_FORMA_PAGO',
                                                'strAccion'           => $strAccion,
                                                'strValorRegla'       => $strFormaPago,
                                                'strFeCreacion'       => $strFeMod,
                                                'strUsrCreacion'      => $strUsrMod,
                                                'strIpCreacion'       => $strIpMod);
                $this->servicePromocion->actualizarTipoPromocionRegla($arrayParamReglaFormaPago);
            }

            if (isset($strAntiguedad) && !empty($strAntiguedad))
            {
                $strAccion = ( isset($strAntiguedad)) ? 'EDITAR' : 'ELIMINAR';
                $arrayParamReglasAntiguedad = array('intIdTipoPromocion'  => $objAdmiTipoPromocion->getId(),
                                                    'strRegla'            => 'PROM_ANTIGUEDAD',
                                                    'strAccion'           => $strAccion,
                                                    'strValorRegla'       => $strAntiguedad,
                                                    'strFeCreacion'       => $strFeMod,
                                                    'strUsrCreacion'      => $strUsrMod,
                                                    'strIpCreacion'       => $strIpMod);
                $this->servicePromocion->actualizarTipoPromocionRegla($arrayParamReglasAntiguedad);
            }

            if (isset($strEstadoServicio) && !empty($strEstadoServicio))
            {
                $strAccion = ( isset($strEstadoServicio) && !empty($strEstadoServicio) ) ? 'EDITAR' : 'ELIMINAR';
                $arrayParamReglasEstados = array('intIdTipoPromocion' => $objAdmiTipoPromocion->getId(),
                                                'strRegla'           => 'PROM_ESTADO_SERVICIO',
                                                'strAccion'          => $strAccion,
                                                'strValorRegla'      => $strEstadoServicio,
                                                'strFeCreacion'      => $strFeMod,
                                                'strUsrCreacion'     => $strUsrMod,
                                                'strIpCreacion'      => $strIpMod);
                $this->servicePromocion->actualizarTipoPromocionRegla($arrayParamReglasEstados);
            }
            
            if (isset($strAccionTipoCliente) && !empty($strAccionTipoCliente))
            {
                $strAccionTipoCliente        = isset($strTipoCliente) && !empty($strTipoCliente) ? 'EDITAR' : 'ELIMINAR';
                $arrayParamReglasTipoCliente = array('intIdTipoPromocion' => $objAdmiTipoPromocion->getId(),
                                                    'strRegla'            => 'PROM_TIPO_CLIENTE',
                                                    'strAccion'           => $strAccionTipoCliente,
                                                    'strValorRegla'       => $strTipoCliente,
                                                    'strFeCreacion'       => $strFeMod,
                                                    'strUsrCreacion'      => $strUsrMod,
                                                    'strIpCreacion'       => $strIpMod);
                $this->servicePromocion->actualizarTipoPromocionRegla($arrayParamReglasTipoCliente);
            }

            $strAccion                 = ( isset($arrayEmisores) && !empty($arrayEmisores) ) ? 'EDITAR' : 'ELIMINAR';
            $arrayParametros           = array();
            $arrayParamBancoTipoCuenta = array();
            $arrayBancoTipoCuenta      = array();
            $arrayIdBancoTipoCuenta    = [];
            for ($i=0; $i<count($arrayEmisores); $i++)
            {
                $strDescomponerEmisor           = ($arrayEmisores[$i]);
                $arrayDescomponerEmisor         = explode("|", $strDescomponerEmisor);
                $strIdCuenta                    = $arrayDescomponerEmisor[0];
                $strIdBanco                     = $arrayDescomponerEmisor[1];
                $arrayParametros['strIdCuenta'] = $strIdCuenta;

                if($strIdBanco === '0')
                {
                    $arrayParametros['strIdBanco']     = '';
                    $arrayParametros['strEsTarjeta']   = 'S';
                }else
                {
                    $arrayParametros['strIdBanco']     = $strIdBanco;
                    $arrayParametros['strEsTarjeta']   = 'N';
                }
                $arrayParamBancoTipoCuenta = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')
                                                  ->getBancoTipoCuenta($arrayParametros);
                $arrayBancoTipoCuenta = array_merge($arrayBancoTipoCuenta, $arrayParamBancoTipoCuenta);
            }
            foreach($arrayBancoTipoCuenta as $strIdBancoTipoCuenta)
            {
                $arrayIdBancoTipoCuenta[] = $strIdBancoTipoCuenta['idBancoTipoCuenta'];
            }
            
            $strIdBancoTipoCuenta = implode(",", $arrayIdBancoTipoCuenta);
            if (isset($strIdBancoTipoCuenta) && !empty($strIdBancoTipoCuenta))
            {
                $arrayParamReglasEmisor = array('intIdTipoPromocion' => $objAdmiTipoPromocion->getId(),
                                                'strRegla'           => 'PROM_EMISOR',
                                                'strAccion'          => $strAccion,
                                                'strValorRegla'      => $strIdBancoTipoCuenta,
                                                'strFeCreacion'      => $strFeMod,
                                                'strUsrCreacion'     => $strUsrMod,
                                                'strIpCreacion'      => $strIpMod);
                $this->servicePromocion->actualizarTipoPromocionRegla($arrayParamReglasEmisor);
            }

            $arrayPlanProdPromo['intTipoPromocionId']   = $objAdmiTipoPromocion->getId();
            $arrayPlanProdPromo['intTipoPlanPromoId']   = '';
            $arrayPlanProdPromo['estado']               = 'Activo';
            $arrayPlanProdPromocion                     = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')
                                                                      ->getTipoPlanProdPromo($arrayPlanProdPromo);
            foreach($arrayPlanProdPromocion as $objPlanProdPromocion)
            {
                $objPlanProdPromocion->setEstado('Eliminado');
                $this->emcom->persist($objPlanProdPromocion);
            }
            for ($i=0; $i<count($arrayPlanes); $i++)
            {
                $strDescomponerPlanes    = $arrayPlanes[$i];
                $arrayDescomponerPlanes  = explode("|", $strDescomponerPlanes);
                $intIdPlan               = $arrayDescomponerPlanes[0];
                $intIdPlanSuperior       = $arrayDescomponerPlanes[1];
                $intIdTipoPlanPromo      = $arrayDescomponerPlanes[2];
                if($intIdTipoPlanPromo === "0")
                {
                    $strAccion  = 'NUEVO';
                }
                else
                {
                    $strAccion  = 'EDITAR';
                }
                $arrayParamTipoPlanPromo = array('intIdTipoPromocion'   => $objAdmiTipoPromocion->getId(),
                                                 'intIdPlan'            => $intIdPlan,
                                                 'intIdPlanSuperior'    => $intIdPlanSuperior,
                                                 'intIdPlanProdPromo'   => $intIdTipoPlanPromo,
                                                 'strAccion'            => $strAccion,
                                                 'strFeCreacion'        => $strFeMod,
                                                 'strUsrCreacion'       => $strUsrMod,
                                                 'strIpCreacion'        => $strIpMod);
                $this->actualizaTipoPlanPromocion($arrayParamTipoPlanPromo);
            }
            $strRespuesta = 'OK';
            $this->emcom->getConnection()->commit();
        }
        catch(\Exception $e)
        {
            $this->emcom->rollback();
            $this->emcom->close();
            $strRespuesta = "No se pudo actualizar Promoción de Ancho de Banda <br>". $e->getMessage() . ". Favor notificar a Sistemas.";
            $this->serviceUtil->insertError('Telcos+',
                                            'PromocionService.crearPromoAnchoBanda',
                                             'Error PromocionService.crearPromoAnchoBanda:'.$e->getMessage(),
                                             $strUsrMod,
                                             $strIpMod);
            return str_replace(' ','_',$e->getMessage());
        }
        return $strRespuesta;
    }
    /**
    * getPromocionAnchoBanda, Función que consulta una promoción por ancho de banda.
    *
    * @author José Candelario <jcandelario@telconet.ec>
    * @version 1.0 02-04-2019   
    * @param array $arrayParametros []
    *              'intIdPromocion'      => Id de la promoción.
    * 
    * @author Katherine Yager <kyager@telconet.ec>
    * @version 1.1 12-10-2020 - Se envía obtiene característica PROM_CODIGO,
    */
    public function getPromocionAnchoBanda($arrayParametros)
    {
        $intIdPromocion                         = $arrayParametros['intIdPromocion'];
        $arrayParametrosPromo['intIdPromocion'] = $arrayParametros['intIdPromocion'];
        $arrayParametrosPromo['intIdEmpresa']   = $arrayParametros['intIdEmpresa'];
        $arrayParametrosPromo['strEstado']      = 'Activo';
        $arrayParametros['arraySectorizacion']  = array('PROM_JURISDICCION',
                                                        'PROM_CANTON',
                                                        'PROM_PARROQUIA',
                                                        'PROM_SECTOR',
                                                        'PROM_ELEMENTO',
                                                        'PROM_EDIFICIO');
        $arrayPlanes                            = [];
        $arraySectorizacion                     = [];
        $arrayEmisores                          = [];
        $objPromocion                           = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')
                                                              ->getPromocionInstalacion($arrayParametros);
        foreach($objPromocion['objTipoPromocion'] as $tipoPromocion)
        {
            if ($tipoPromocion['descCaracteristica'] === "PROM_ANTIGUEDAD")
            {   
                $strAntiguedad = $tipoPromocion['valorCaracteristica'].' mes(es)';
            }
            if ($tipoPromocion['descCaracteristica'] === "PROM_ESTADO_SERVICIO")
            {
                $strEstadoServicio = $tipoPromocion['valorCaracteristica'];
            }
            if ($tipoPromocion['descCaracteristica'] === "PROM_PERIODO")
            {
                $strPeriodo         = "";
                $arrayParametros    = explode(",", $tipoPromocion['valorCaracteristica']);
                for ($i=0; $i<count($arrayParametros); $i++)
                {
                    $strPeriodoPorcentaje   = $arrayParametros[$i];
                    $arrayPeriodo           = explode("|", $strPeriodoPorcentaje);
                    $strPeriodo             = $strPeriodo. $arrayPeriodo[0].', ';
                }
                $strPeriodo         = substr( $strPeriodo , 0 , -2);
            }
            if ($tipoPromocion['descCaracteristica'] === "PROM_FORMA_PAGO")
            {
                $arrayParametros    = array('arrayFormaPago' => explode(",", $tipoPromocion['valorCaracteristica']));
                $objFormasPagos     = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')
                                                  ->getFormasPagos($arrayParametros);
                $strFormaPago       = "";
                foreach ($objFormasPagos as $objFormaPago)
                {
                    $strFormaPago = $strFormaPago. $objFormaPago['descripcionFormaPago'].', ';

                }
                $strFormaPago = substr( $strFormaPago , 0 , -2);
            }
            if($tipoPromocion['descCaracteristica'] === "PROM_CODIGO")
            {
                $strCodigoPromocion = $tipoPromocion['valorCaracteristica'];
            }
            if ($tipoPromocion['descCaracteristica'] === "PROM_EMISOR")
            {
                $arrayParametrosPromo['arrayEstado'] = array('Eliminado');
                $arrayEmisores  = $this->getEmisoresPromoAnchoBanda($arrayParametrosPromo);
            }
            if ($tipoPromocion['descCaracteristica'] === "PROM_TIPO_NEGOCIO")
            {
                $arrayParametros    = array('arrayTipoNegocio' => explode(",", $tipoPromocion['valorCaracteristica']));
                $objTipoNegocios    = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')
                                                  ->getTipoNegocios($arrayParametros);
                $strTipoNegocio     = "";
                foreach ($objTipoNegocios as $objTipoNegocio)
                {
                    $strTipoNegocio = $strTipoNegocio. $objTipoNegocio['nombreTipoNegocio'].', ';
                }
                $strTipoNegocio     = substr( $strTipoNegocio , 0 , -2);
            }
            if ($tipoPromocion['descCaracteristica'] === "PROM_ULTIMA_MILLA")
            {
                $arrayParametros    = array('arrayUltimaMilla' => explode(",", $tipoPromocion['valorCaracteristica']));
                $objUltimasMillas   = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')
                                                  ->getUltimasMillas($arrayParametros);
                $strUltimaMilla     = "";
                foreach ($objUltimasMillas as $objUltimaMilla)
                {
                    $strUltimaMilla = $strUltimaMilla. $objUltimaMilla['nombreTipoMedio'].', ';
                }
                $strUltimaMilla     = substr( $strUltimaMilla , 0 , -2);
            }
            if($tipoPromocion['descCaracteristica'] === "PROM_TIPO_CLIENTE")
            {
                $strTipoCliente = $tipoPromocion['valorCaracteristica'];
            }
        }
        $objAdmiGrupoPromocion  = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')
                                              ->find($intIdPromocion);
        $arrayCaracteristicas   = array('strEstadoServicio'   => $strEstadoServicio,
                                        'strFormaPago'        => $strFormaPago,
                                        'strTipoNegocio'      => $strTipoNegocio,
                                        'strUltimaMilla'      => $strUltimaMilla,
                                        'strPeriodo'          => $strPeriodo,
                                        'strAntiguedad'       => $strAntiguedad,
                                        'strTipoCliente'      => $strTipoCliente,
                                        'strCodigoPromocion'  => $strCodigoPromocion);
        $arraySectorizacion     = $this->servicePromocion->getSectorizacion($arrayParametrosPromo);
        $arrayPlanes            = $this->getPlanesPromoAnchoBanda($arrayParametrosPromo);
        $arrayRespuesta         = array('objAdmiGrupoPromocion' => $objAdmiGrupoPromocion,
                                        'arrayCaracteristicas'  => $arrayCaracteristicas,
                                        'arrayPlanes'           => $arrayPlanes,
                                        'arrayEmisores'         => $arrayEmisores,
                                        'arraySectorizacion'    => $arraySectorizacion);
        return $arrayRespuesta;
    }
    /**
    * ingresarTipoPlanPromocion, Función que insertar los planes de promoción por ancho de banda.
    *
    * @author José Candelario <jcandelario@telconet.ec>
    * @version 1.0 02-04-2019
    * @param array $arrayParamTipoPlanPromo []
    *              'intIdTipoPromocion'      => Id tipo de la promoción.
    *              'intIdPlan'               => Id plan.
    *              'intIdPlanSuperior'       => Id plan superior.
    *              'strUsrCreacion'          => Usuario en sesión.
    *              'strFeCreacion'           => Fecha en sesión.
    *              'strIpCreacion'           => Ip de sesión.
    */
    public function ingresarTipoPlanPromocion($arrayParamTipoPlanPromo)
    {
        $objAdmiTipoPromocion = $this->emcom->getRepository('schemaBundle:AdmiTipoPromocion')
                                            ->find($arrayParamTipoPlanPromo['intIdTipoPromocion']);
        if(!is_object($objAdmiTipoPromocion))
        {
            throw new \Exception("No se pudo crear Promoción, No se encontró el Tipo de Promocion");
        }
        $objInfoPlanCab = $this->emcom->getRepository('schemaBundle:InfoPlanCab')
                                      ->find($arrayParamTipoPlanPromo['intIdPlan']);
        if(!is_object($objInfoPlanCab))
        {
            throw new \Exception("No se pudo crear Promoción, No se encontró información del Plan");
        }
        $objInfoPlanCabSup = $this->emcom->getRepository('schemaBundle:InfoPlanCab')
                                         ->find($arrayParamTipoPlanPromo['intIdPlanSuperior']);
        if(!is_object($objInfoPlanCabSup))
        {
            throw new \Exception("No se pudo crear Promoción, No se encontró información del Plan Nuevo");
        }
        $objAdmiTipoPlanProdPromocion = new \telconet\schemaBundle\Entity\AdmiTipoPlanProdPromocion();
        $objAdmiTipoPlanProdPromocion->setTipoPromocionId($objAdmiTipoPromocion);
        $objAdmiTipoPlanProdPromocion->setPlanId($objInfoPlanCab);
        $objAdmiTipoPlanProdPromocion->setPlanIdSuperior($objInfoPlanCabSup);
        $objAdmiTipoPlanProdPromocion->setFeCreacion($arrayParamTipoPlanPromo['strFeCreacion']);
        $objAdmiTipoPlanProdPromocion->setUsrCreacion($arrayParamTipoPlanPromo['strUsrCreacion']);
        $objAdmiTipoPlanProdPromocion->setIpCreacion($arrayParamTipoPlanPromo['strIpCreacion']);
        $objAdmiTipoPlanProdPromocion->setEstado('Activo');
        $this->emcom->persist($objAdmiTipoPlanProdPromocion);
        $this->emcom->flush();
    }
    /**
     * actualizaTipoPlanPromocion, actualizar ó inserta un registro de planes asociados a un tipo de promoción
     * en la estructura AdmiPlanProdPromocion.
     * 
     * @author José Candelario <jcandelario@telconet.ec>
     * @version 1.0 26-04-2019     
     * @param array $arrayParamTipoPlanPromo []
     *              'intIdTipoPromocion'      => Id tipo de la promoción.
     *              'intIdPlan'               => Id plan.
     *              'intIdPlanSuperior'       => Id plan superior.
     *              'intTipoPlanPromoId'      => Id tipo plan producto promoción.
     *              'strAccion'               => Accion  NUEVO, EDITAR.
     *              'strUsrCreacion'          => Usuario en sesión.
     *              'strFeCreacion'           => Fecha en sesión.
     *              'strIpCreacion'           => Ip de sesión.
     */
    public function actualizaTipoPlanPromocion($arrayParamTipoPlanPromo)
    {
        $objAdmiTipoPromocion = $this->emcom->getRepository('schemaBundle:AdmiTipoPromocion')
                                            ->find($arrayParamTipoPlanPromo['intIdTipoPromocion']);
        if(!is_object($objAdmiTipoPromocion))
        {
            throw new \Exception("No se pudo crear Promoción, No se encontró el Tipo de Promoción");
        }
        $objInfoPlanCab = $this->emcom->getRepository('schemaBundle:InfoPlanCab')
                                      ->find($arrayParamTipoPlanPromo['intIdPlan']);
        if(!is_object($objInfoPlanCab))
        {
            throw new \Exception("No se pudo crear Promoción, No se encontró información del Plan");
        }
        $objInfoPlanCabSup = $this->emcom->getRepository('schemaBundle:InfoPlanCab')
                                         ->find($arrayParamTipoPlanPromo['intIdPlanSuperior']);
        if(!is_object($objInfoPlanCabSup))
        {
            throw new \Exception("No se pudo crear Promoción, No se encontró información del Plan Nuevo");
        }

        if($arrayParamTipoPlanPromo['strAccion'] === "NUEVO")
        {
            $objAdmiTipoPlanProdPromocion = new \telconet\schemaBundle\Entity\AdmiTipoPlanProdPromocion();
            $objAdmiTipoPlanProdPromocion->setTipoPromocionId($objAdmiTipoPromocion);
            $objAdmiTipoPlanProdPromocion->setPlanId($objInfoPlanCab);
            $objAdmiTipoPlanProdPromocion->setPlanIdSuperior($objInfoPlanCabSup);
            $objAdmiTipoPlanProdPromocion->setFeCreacion($arrayParamTipoPlanPromo['strFeCreacion']);
            $objAdmiTipoPlanProdPromocion->setUsrCreacion($arrayParamTipoPlanPromo['strUsrCreacion']);
            $objAdmiTipoPlanProdPromocion->setIpCreacion($arrayParamTipoPlanPromo['strIpCreacion']);
            $objAdmiTipoPlanProdPromocion->setEstado('Activo');
            $this->emcom->persist($objAdmiTipoPlanProdPromocion);
            $this->emcom->flush();
        }
        else
        {
            $objAdmiTipoPromocionRegla = $this->emcom->getRepository('schemaBundle:AdmiTipoPlanProdPromocion')
                                              ->findOneBy(array('id'                 => (int)$arrayParamTipoPlanPromo['intIdPlanProdPromo'],
                                                                'tipoPromocionId'    => $arrayParamTipoPlanPromo['intIdTipoPromocion']));
            if(is_object($objAdmiTipoPromocionRegla))
            {
                $objAdmiTipoPromocionRegla->setFeUltMod($arrayParamTipoPlanPromo['strFeCreacion']);
                $objAdmiTipoPromocionRegla->setUsrUltMod($arrayParamTipoPlanPromo['strUsrCreacion']);
                $objAdmiTipoPromocionRegla->setIpUltMod($arrayParamTipoPlanPromo['strIpCreacion']);  
                $objAdmiTipoPromocionRegla->setEstado('Activo');
                $this->emcom->persist($objAdmiTipoPromocionRegla);
                $this->emcom->flush();
            }
        }
    }
    
   /**
    * getDatosGenePromoAnchoBanda, obtiene información básica por tipo promoción ancho de banda.
    *
    * @author José Candelario <jcandelario@telconet.ec>
    * @version 1.0 19-03-2019    
    * @param array $arrayParametros []
    *              'intIdPromocion' => Id de la promoción.
    *              'strEstado'      => Estado de la promoción.   
    * @return Response lista de información básica por tipo promoción ancho de banda.
    */
    public function getDatosGenePromoAnchoBanda($arrayParametros)
    {
        return $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')->getDatosGenePromoAnchoBanda($arrayParametros);
    }
    /**
    * getPlanesPromoAnchoBanda, obtiene los planes por tipo promoción ancho de banda.
    *
    * @author José Candelario <jcandelario@telconet.ec>
    * @version 1.0 19-03-2019    
    * @param array $arrayParametros []
    *              'intIdPromocion' => Id de la promoción.
    *              'strEstado'      => Estado de la promoción.    
    * @return Response lista de planes por promoción.
    */
    public function getPlanesPromoAnchoBanda($arrayParametros)
    {
        return $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')->getPlanesPromoAnchoBanda($arrayParametros);
    }
    /**
    * getEmisoresPromoAnchoBanda, obtiene los emisores por tipo promoción ancho de banda.
    *
    * @author José Candelario <jcandelario@telconet.ec>
    * @version 1.0 19-03-2019    
    * 
    * @author Hector Lozano <hlozano@telconet.ec>
    * @version 1.1 27-09-2021 - Se agrega función para obtener las reglas promocionales de Emisores.
    * 
    * @param array $arrayParametros []
    *              'intIdPromocion' => Id de la promoción.
    *              'strEstado'      => Estado de la promoción.    
    *              'arrayEstado'    => Estados que no se consideran   
    * @return Response lista de emisores por tipo promoción ancho de banda.
    */    
    public function getEmisoresPromoAnchoBanda($arrayParametros)
    {
        $objEmisorReglaPromo   = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')->getEmisorReglaPromoAnchoBanda($arrayParametros);
        $arrayEmisorReglaPromo = explode(",",$objEmisorReglaPromo[0]['valorRegla']);
        
        $arrayParametros ['arrayEmisorReglaPromo'] = $arrayEmisorReglaPromo;
                
        return $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')->getEmisoresPromoAnchoBanda($arrayParametros);
    }
    /**
    * getSelectTiposNegocio, obtiene los tipos de Negocio por promoción.
    *
    * @param array $arrayParametros []
    *              'intIdPromocion' => Id de la promoción.
    *              'strEstado'      => Estado de la promoción.    
    * @author José Candelario <jcandelario@telconet.ec>
    * @version 1.0 19-03-2019    
    * @return Response lista de Tipos de Negocio por promoción.
    */
    public function getSelectTiposNegocio($arrayParametros)
    {
        return $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')->getSelectTiposNegocio($arrayParametros);
    }
    /**
    * getSelectUltimaMillas, obtiene las últimas millas por promoción.
    *
    * @author José Candelario <jcandelario@telconet.ec>
    * @version 1.0 19-03-2019    
    * @param array $arrayParametros []
    *              'intIdPromocion' => Id de la promoción.
    *              'strEstado'      => Estado de la promoción.
    * @return Response lista de últimas millas por promoción.
    */
    public function getSelectUltimaMillas($arrayParametros)
    {
        return $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')->getSelectUltimaMillas($arrayParametros);
    }
    /**
    * getSelectFormaPagos, obtiene las formas de pagos por promoción.
    *
    * @author José Candelario <jcandelario@telconet.ec>
    * @version 1.0 19-03-2019    
    * @param array $arrayParametros []
    *              'intIdPromocion' => Id de la promoción.
    *              'strEstado'      => Estado de la promoción.
    * @return Response lista de formas de pagos por promoción.
    */
    public function getSelectFormaPagos($arrayParametros)
    {
        return $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')->getSelectFormaPagos($arrayParametros);
    }
    /**
    * getSelectEstados, obtiene los estados que aplica la promoción.
    *
    * @author José Candelario <jcandelario@telconet.ec>
    * @version 1.0 19-03-2019    
    * @param array $arrayParametros []
    *              'intIdPromocion' => Id de la promoción.
    *              'strEstado'      => Estado de la promoción.
    * @return Response lista de estados que aplica la promoción.
    */
    public function getSelectEstados($arrayParametros)
    {
        return $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')->getSelectEstados($arrayParametros);
    }
    /**
    * getSelectPeriodos, obtiene los períodos que aplica la promoción.
    *
    * @author José Candelario <jcandelario@telconet.ec>
    * @version 1.0 19-03-2019    
    * @param array $arrayParametros []
    *              'intIdPromocion' => Id de la promoción.
    *              'strEstado'      => Estado de la promoción.
    * @return Response lista de períodos que aplica la promoción.
    */
    public function getSelectPeriodos($arrayParametros)
    {
        return $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')->getSelectPeriodos($arrayParametros);
    }

    /**
     * Documentación para el método 'procesarPromocionesBwOlt'.
     *
     * Metodo encargado de ejecutar los procesos de las promociones de ancho de banda del olt
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 29-04-2022
     *
     * @param Array $arrayParametros [
     *                                  opcion,     Opción de la operación Procesar o Detener
     *                                  id_promo,   id de la promoción
     *                                  elemento,   arreglo de los olt confirmados
     *                               ]
     *
     * @return Array $arrayResultado [
     *                                  'status'  => estado de respuesta de la operación 'OK' o 'ERROR',
     *                                  'mensaje' => mensaje de la operación
     *                               ]
     *
     * costoQuery: 19
     */
    public function procesarPromocionesBwOlt($arrayDatosElementos)
    {
        try
        {
            $strStatus  = null;
            $strStatus  = str_pad($strStatus, 3000, " ");
            $strMensaje = null;
            $strMensaje = str_pad($strMensaje, 3000, " ");
            $strSql = " BEGIN
                            DB_COMERCIAL.CMKG_PROMOCIONES_BW.P_PROCESAR_PROMOCIONES_BW(Pcl_JsonRespuesta => :jsonRespuesta,
                                                                                       Pv_Status         => :status,
                                                                                       Pv_Mensaje        => :mensaje);
                        END; ";
            $objConn = oci_connect($this->strUrsrComercial,
                                    $this->strPassComercial,
                                    $this->strDnsComercial);
            $objStmt = oci_parse($objConn, $strSql);
            $strJsonRespuesta = oci_new_descriptor($objConn);
            $strJsonRespuesta->writetemporary(json_encode($arrayDatosElementos));
            oci_bind_by_name($objStmt, ':jsonRespuesta', $strJsonRespuesta, -1, OCI_B_CLOB);
            oci_bind_by_name($objStmt, ':status',        $strStatus);
            oci_bind_by_name($objStmt, ':mensaje',       $strMensaje);
            oci_execute($objStmt);
            //respuesta
            $arrayResultado = array(
                'status'  => $strStatus,
                'mensaje' => $strMensaje
            );
        }
        catch (\Exception $e)
        {
            $arrayResultado = array(
                'status'  => 'ERROR',
                'mensaje' => $e->getMessage()
            );
        }
        return $arrayResultado;
    }

    /**
     * Función que permitira detener una promocion seleccionada a traves de un webservice
     * 
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 1.0 26-11-2021
     * @param array $arrayParametros["intIdPromocion"  => Id Empresa,
     *                               "strCodPromocion" => Codigo del tipo de Promocion,
     *                               "intIdEmpresa"    => Codigo del tipo de empresa",
     *                               "strPreEmpresa"   => Prefijo de la empresa,
     *                               "strUsrDetiene"   => Usuario de sesion,
     *                               "strIpDetiene"    => Ip de sesion]
     * @return objeto Planes
    */
    public function detenerPromoAnchoBanda($arrayParametros)
    {
        $intIdPromocion    = $arrayParametros['intIdPromocion'];
        $strTipoPromocion  = $arrayParametros['strTipoPromocion'];
        $intIdEmpresa      = $arrayParametros['intIdEmpresa'];
        $strPreEmpresa     = $arrayParametros['strPreEmpresa'];
        $strUsrDetiene     = $arrayParametros['strUsrDetiene'];
        $strIpDetiene      = $arrayParametros['strIpDetiene'];
        $strEjecutaComando = "";
        $strEjecutaConfig  = "";
        $strRespuesta      = "OK";
        // Obtenemos los datos adicionales para inactivar
        $arrayDatosDetener = $this->emcom->getRepository('schemaBundle:AdmiParametroDet')
                                    ->getOne('PROMOCION ANCHO BANDA', 'COMERCIAL','PROMO_ANCHO_BANDA',
                                            'Datos para webservices de detener promocion',
                                            $strTipoPromocion,
                                            '','','','',
                                            $intIdEmpresa);
        if(!empty($arrayDatosDetener))
        {
            $strOpcionValida   = $arrayDatosDetener["valor2"];
            $strOpcionDetiene  = $arrayDatosDetener["valor3"];
            $strEjecutaComando = $this->rdaEjecutaComando;
            $strEjecutaConfig  = $this->rdaEjecutaConfiguracion;

            // Ejecutamos webservice validacion de proceso
            $arrayDatosMiddleware = array(
                            "id_promo"             => $intIdPromocion,
                            "opcion"               => $strOpcionValida,
                            "ejecutaComando"       => $strEjecutaComando,
                            "usrCreacion"          => $strUsrDetiene,
                            "ipCreacion"           => $strIpDetiene,
                            "comandoConfiguracion" => $strEjecutaConfig,
                            "empresa"              => $strPreEmpresa);
            // LLamamos al proceso de middleware para enviar el webservice
            $arrayRespuesta = $this->rdaMiddleware->middleware(json_encode($arrayDatosMiddleware));
            if ($arrayRespuesta['status'] != 'OK')
            {
                $strRespuesta = "Problemas al ejecutar Middleware";
            }
            else
            {
                // Enviamos a cambiar el estado de los procesos de la promocion
                $arrayParametros = array("intIdPromocion" => $intIdPromocion);
                $arrayRespuesta = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocionRegla')
                                            ->detenerProcesosPromocion($arrayParametros);
                if ($arrayRespuesta['status'] != 'OK')
                {
                    $strRespuesta = "Problemas al actualizar los estados de los procesos de la promocion";
                }
                else
                {
                    $arrayDatosMiddleware = array(
                        "id_promo"             => $intIdPromocion,
                        "opcion"               => $strOpcionDetiene,
                        "ejecutaComando"       => $strEjecutaComando,
                        "usrCreacion"          => $strUsrDetiene,
                        "ipCreacion"           => $strIpDetiene,
                        "comandoConfiguracion" => $strEjecutaConfig,
                        "empresa"              => $strPreEmpresa);
                    $this->rdaMiddleware->middleware(json_encode($arrayDatosMiddleware));
                }
            }
        }
        else
        {
            $strRespuesta = "Problemas al obtener datos para Validar el detener la promocion";
        }
        
        return $strRespuesta;
    }

    /**
     * Función que permitira anular una promocion seleccionada
     * 
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 1.0 11-04-2022
     * @param array $arrayParametros["intIdPromocion"  => Id Empresa,
     *                               "strCodPromocion" => Codigo del tipo de Promocion,
     *                               "intIdEmpresa"    => Codigo del tipo de empresa",
     *                               "strPreEmpresa"   => Prefijo de la empresa,
     *                               "strUsrDetiene"   => Usuario de sesion,
     *                               "strIpDetiene"    => Ip de sesion]
     * @return objeto Planes
    */
    public function anularPromoAnchoBanda($arrayParametros)
    {
        $intIdPromocion     = $arrayParametros['intIdPromocion'];
        $strTipoPromocion   = $arrayParametros['strTipoPromocion'];
        $strEstadoPromocion = $arrayParametros['strEstadoPromocion'];
        $intIdEmpresa       = $arrayParametros['intIdEmpresa'];
        $strUsrAnula        = $arrayParametros['strUsrDetiene'];
        $strIpAnula         = $arrayParametros['strIpDetiene'];
        $strRespuesta       = "OK";
        try
        {
            // Obtenemos los datos adicionales para inactivar
            $objDatosAnular = $this->emcom->getRepository('schemaBundle:AdmiParametroDet')
                                        ->getOne('PROMOCION ANCHO BANDA', 'COMERCIAL','PROMO_ANCHO_BANDA',
                                                'Estados permitidos para anular la promocion',
                                                $strTipoPromocion,$strEstadoPromocion,
                                                '','','',
                                                $intIdEmpresa);
            if(!empty($objDatosAnular))
            {
                $strEstadoFinal  = $objDatosAnular["valor3"];
                $objAdmiGrupoPromocion  = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')
                                                ->find($intIdPromocion);
                if (!empty($objAdmiGrupoPromocion))
                {
                    $objAdmiGrupoPromocion->setEstado($strEstadoFinal);
                    $this->emcom->persist($objAdmiGrupoPromocion);
                    $this->emcom->flush();
                }
            }
            else
            {
                $strRespuesta = "Problemas al obtener datos para anular la promocion";
            }
        }
        catch(\Exception $e)
        {
            $this->emcom->rollback();
            $this->emcom->close();
            $strRespuesta = "No se pudo anular la Promoción por franja horaria <br>". $e->getMessage() . ". Favor notificar a Sistemas.";
            $this->serviceUtil->insertError('Telcos+',
                                            'PromocionService.anularPromoAnchoBanda',
                                            'Error PromocionService.anularPromoAnchoBanda:'.$e->getMessage(),
                                            $strUsrAnula,
                                            $strIpAnula);
            return str_replace(' ','_',$e->getMessage());
        }
        
        return $strRespuesta;
    }

    /**
     * Función que permitira anular una promocion seleccionada
     * 
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 1.0 11-04-2022
     * @param array $arrayParametros["intJurisdiccion"  => Id de jurisdiccion,
     *                               "intIdCanton"      => Id del canton,
     *                               "intIdParroquia"   => Id de la parroquia,
     *                               "arrayIdSector"    => Arreglo de id sectores,
     *                               "arrayLineProfile" => Arreglo con los line profiles,
     *                               "intIdEmpresa"     => Codigo de la empresa,
     *                               "strTipoPromocion" => El tipo de promocion enviada,
     *                               "strUsr"           => Usuario de sesion,
     *                               "strIp"            => Ip de sesion]
     * @return objeto Respuesta con cantidad de beneficiarios
    */
    public function getBeneficiariosOlt($arrayParametros)
    {
        $strUsr            = $arrayParametros['strUsr'];
        $strIp             = $arrayParametros['strIp'];
        $intIdEmpresa      = $arrayParametros['intIdEmpresa'];
        $strTipoPromocion  = $arrayParametros['strTipoPromocion'];
        $objResultado      = array();
        $strRespuesta      = "OK";
        $intCantidad       = 0;
        $strProducto       = 'INTERNET DEDICADO';
        $strCaracteristica = 'LINE-PROFILE-NAME';
        $strEstadoActivo   = 'Activo';
        try
        {
            $arrayDatosConsulta = $this->emcom->getRepository('schemaBundle:AdmiParametroDet')
                                ->getOne('PROMOCION ANCHO BANDA', 'COMERCIAL','PROMO_ANCHO_BANDA',
                                        'Datos para consultas de planes para promocion',
                                        $strTipoPromocion,
                                        '','','','',
                                        $intIdEmpresa);
            if (!empty($arrayDatosConsulta))
            {
                $strProducto       = $arrayDatosConsulta["valor2"];
                $strCaracteristica = $arrayDatosConsulta["valor3"];
            }
            $arrayParametros['strProducto'] = $strProducto;
            $arrayParametros['strCaracteristica'] = $strCaracteristica;
            $arrayParametros['strEstadoActivo'] = $strEstadoActivo;
            $intCantidad  = $this->emcom->getRepository('schemaBundle:InfoEmpresaElementoUbica')
                                    ->getBeneficiariosOlt($arrayParametros);
        }
        catch(\Exception $e)
        {
            $strRespuesta = "Error";
            $intCantidad  = 0;
            $this->serviceUtil->insertError('Telcos+',
                                            'PromocionService.anularPromoAnchoBanda',
                                            'Error PromocionService.anularPromoAnchoBanda:'.$e->getMessage(),
                                            $strUsr,
                                            $strIp);
        }
        
        $objResultado['resultado'] = $strRespuesta;
        $objResultado['cantidad']  = $intCantidad;
        return $objResultado;
    }

}

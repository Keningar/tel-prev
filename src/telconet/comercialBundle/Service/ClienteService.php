<?php

namespace telconet\comercialBundle\Service;

use telconet\schemaBundle\Entity\InfoServicioProdCaract;

use telconet\schemaBundle\Entity\AdmiTipoDocumento;
use telconet\schemaBundle\Entity\InfoDocumento;
use telconet\schemaBundle\Entity\InfoDocumentoRelacion;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
class ClienteService {
    
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emcom;
    /**
     * @var \telconet\comercialBundle\Service\PreClienteService
     */
    private $servicePreCliente;
    /**
     * @var \telconet\comercialBundle\Service\InfoPersonaService
     */
    private $emComunicacion;
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emGeneral;

    /**
     * string $host
     */
    private $serviceInfoPersona;

    private $serviceUtil;
    private $serviceSoporte;
    private $serviceAutorizaciones;
    private $serviceSolicitud ; 

    private $strUrlTipoFormaContactoProspecto;

    private $restClient;
    
    private $strUrlRegularizacionPersonaMs;
    private $strUrlgenerarCredencialMs;


    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $container) {
        $this->emcom = $container->get('doctrine.orm.telconet_entity_manager');
        $this->servicePreCliente    = $container->get('comercial.PreCliente');
        $this->serviceInfoPersona   = $container->get('comercial.InfoPersona');
        $this->serviceUtil          = $container->get('schema.Util');
        $this->emComunicacion       = $container->get('doctrine.orm.telconet_comunicacion_entity_manager');
        $this->emGeneral            = $container->get('doctrine.orm.telconet_general_entity_manager');
        $this->serviceSoporte        = $container->get('soporte.SoporteService');
        $this->serviceAutorizaciones = $container->get('comercial.Autorizaciones');
        $this->serviceSolicitud      = $container->get('comercial.Solicitudes'); 
        $this->restClient                      = $container->get('schema.RestClient');
        $this->strUrlTipoFormaContactoProspecto = $container->getParameter('ws_ms_tipo_forma_contacto_prospecto');
        $this->strUrlRegularizacionPersonaMs   = $container->getParameter('ws_ms_regularizacion_persona');
        $this->strUrlgenerarCredencialMs   = $container->getParameter('ws_ms_generarCredencial');
        
    }


    /**
     * Busca un cliente en la empresa dada con la identificacion indicada
     * @param string $codEmpresa
     * @param string $identificacionCliente
     * @param string $prefijoEmpresa
     * @return array con datos del cliente si se encuentra, null caso contrario
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.1 10-06-2020 - Se adiciona el retorno del estado y cargo de la persona
     * 
     * @author Jean Pierre Nazareno Martinez <jnazareno@telconet.ec>
     * @version 1.2 01-02-2022 - Se agrega validación para controlar objeto nulo
     *          
     */
    public function obtenerDatosClientePorIdentificacion($codEmpresa, $identificacionCliente, $prefijoEmpresa) {
        /* @var $persona \telconet\schemaBundle\Entity\InfoPersona */
        $persona = $this->emcom->getRepository('schemaBundle:InfoPersona')->findOneByIdentificacionCliente($identificacionCliente);
        if (!$persona) 
        {
            return null;
        } 
        else
        {
            $fechaNac = "";
            if ($persona->getFechaNacimiento()) 
            {
                $fechaNac = date_format($persona->getFechaNacimiento(), "d/m/Y G:i");
            }

            //obtiene roles activos o pendientes de la persona
            $objRoles = $this->emcom->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                ->getPersonaEmpresaRolPorPersonaPorEmpresaActivos($persona->getId(), $codEmpresa);

            $roles = "";
            $datosPersonaEmpresaRol = array(); 
            /* @var $rol \telconet\schemaBundle\Entity\InfoPersonaEmpresaRol */
            foreach ($objRoles as $rol) {                
                $entityRol = $this->emcom->getRepository('schemaBundle:AdmiRol')->find($rol->getEmpresaRolId()->getRolId());
                $roles .= $entityRol->getTipoRolId()->getDescripcionTipoRol() . "|";
                if ($prefijoEmpresa == 'TN')
                {
                    $datosPersonaEmpresaRol[] = array(
                        'personaEmpresaRolId'  => $rol->getId(),
                        'rol'                  => $entityRol->getTipoRolId()->getDescripcionTipoRol(),
                        'idOficinaFacturacion' => $rol->getOficinaId()?$rol->getOficinaId()->getid():"",                        
                        'esPrepago'            => $rol->getEsPrepago());
                }    
                else
                {
                    $datosPersonaEmpresaRol[] = array(
                        'personaEmpresaRolId' => $rol->getId(),
                        'rol' => $entityRol->getTipoRolId()->getDescripcionTipoRol(),
                        'idOficinaFacturacion' => $rol->getOficinaId()?$rol->getOficinaId()->getid():"");
                }
            }


            $entityPersonaRef = $this->emcom->getRepository('schemaBundle:InfoPersonaReferido')->findPorPersona($persona->getId());
            $referidoId = 0;
            $referidoNombre = "";
            if ($entityPersonaRef)
             {
                $referidoId = $entityPersonaRef->getReferidoId()->getId();
                if ($entityPersonaRef->getReferidoId()->getRazonSocial()) 
                {
                    $referidoNombre = $entityPersonaRef->getReferidoId()->getRazonSocial();
                }
                 else 
                {
                    $referidoNombre = $entityPersonaRef->getReferidoId()->getNombres() . " " . $entityPersonaRef->getReferidoId()->getApellidos();
                }
            }

            $formaPagoId = null;
            $tipoCuentaId = null;
            $bancoTipoCuentaId = null;
            if ($prefijoEmpresa != 'TN') 
            {
                /* @var $entityPersonaEmpFormaPago \telconet\schemaBundle\Entity\InfoPersonaEmpFormaPago */
                $entityPersonaEmpFormaPago = $this->servicePreCliente->getDatosPersonaEmpFormaPago($persona->getId(), $codEmpresa);
                if ($entityPersonaEmpFormaPago)
                 {
                    $formaPagoId = (is_object($entityPersonaEmpFormaPago->getFormaPagoId()) ? 
                                                $entityPersonaEmpFormaPago->getFormaPagoId()->getId() : null);
                    $tipoCuentaId = ($entityPersonaEmpFormaPago->getTipoCuentaId() ? $entityPersonaEmpFormaPago->getTipoCuentaId()->getId() : null);
                    $bancoTipoCuentaId = ($entityPersonaEmpFormaPago->getBancoTipoCuentaId() ? $entityPersonaEmpFormaPago->getBancoTipoCuentaId()->getId() : null);
                }
            }

   
            $arreglo = array(
                'id'                     => $persona->getId(),
                'nombres'                => $persona->getNombres(),
                'apellidos'              => $persona->getApellidos(),
                'razonSocial'            => $persona->getRazonSocial(),
                'tituloId'               => ($persona->getTituloId() ? $persona->getTituloId()->getId() : null),
                'tipoIdentificacion'     => $persona->getTipoIdentificacion(),
                'identificacionCliente'  => $persona->getIdentificacionCliente(),
                'tipoEmpresa'            => $persona->getTipoEmpresa(),
                'tipoTributario'         => $persona->getTipoTributario(),
                'representanteLegal'     => $persona->getRepresentanteLegal(),
                'nacionalidad'           => $persona->getNacionalidad(),
                'genero'                 => $persona->getGenero(),
                'direccionTributaria'    => $persona->getDireccionTributaria(),
                'estadoCivil'            => $persona->getEstadoCivil(),
                //cambios DINARDARP - se agrega campo origenes de ingresos
                'origenIngresos'         => $persona->getOrigenIngresos(),
                'fechaNacimiento'        => $fechaNac,
                'referidoId'             => $referidoId,
                'referidoNombre'         => $referidoNombre,
                'roles'                  => $roles,
                'datosPersonaEmpresaRol' => $datosPersonaEmpresaRol,
                'formaPagoId'            => $formaPagoId,
                'tipoCuentaId'           => $tipoCuentaId,
                'bancoTipoCuentaId'      => $bancoTipoCuentaId,  
                'estado'                 => $persona->getEstado(),
                'cargo'                  => $persona->getCargo()
            );
            if ($prefijoEmpresa == 'TN')
            {
                $arreglo = array_merge($arreglo,array('numeroConadis'         =>  $persona->getNumeroConadis(),
                                                      'contribuyenteEspecial' =>  $persona->getContribuyenteEspecial(),
                                                      'pagaIva'               =>  $persona->getPagaIva(),                   
                                      ));    
            }
            return $arreglo;
        }
    }

    /**
     * Devuelve las formas de contacto de una persona
     * @param integer $idPersona
     * @param integer $limit
     * @param integer $page
     * @param integer $start
     * @param boolean $isIdFormaContacto TRUE si se debe agregar al array el id de la forma contacto (default FALSE)
     * @return array con total(integer) y registros(array)
     * 
     * Se agrega campo valor al retorno de la forma de contacto, con el fin de identificar la forma de contacto por
     * su codigo.
     * @author Fabricio Bermeo <fbermeo@telconet.ec>
     * @version 1.1 31-08-2016
     * 
     */
    public function obtenerFormasContactoPorPersona($idPersona, $limit, $page, $start, $isIdFormaContacto = FALSE)
    {
        $resultado = $this->emcom->getRepository('schemaBundle:InfoPersonaFormaContacto')->findPorEstadoPorPersona($idPersona, 'Activo', $limit, $page, $start);
        $datos = $resultado['registros'];
        $arreglo = array();
        /* @var $value \telconet\schemaBundle\Entity\InfoPersonaFormaContacto */
        foreach ($datos as $value):
        $arreglo[] = array(
                           'idPersonaFormaContacto' => $value->getId(),
                           'idPersona'              => $value->getPersonaId()->getId(),
                           'formaContacto'          => $value->getFormaContactoId()->getDescripcionFormaContacto(),
                           'valor'                  => $value->getValor(),
                           'codigo'                 => $value->getFormaContactoId()->getCodigo()
                     ) + ($isIdFormaContacto ?
                             array('idFormaContacto' => $value->getFormaContactoId()->getId()) : array());
        endforeach;
        return array('total' => $resultado['total'], 'registros' => $arreglo);
    }


    /**
     * Documentación de la función consultaServicioProdCaract - retorna 'S' si una caracteristica existe
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 21-03-2019
     *
     * @author $arrayParametros [ strCaracteristica => nombre de la  caracteristica
     *                            objProducto       => entidad InfoProducto
     *                            objServicio       => entidad InfoServicio ]
     */
    public function consultaServicioProdCaract($arrayParametros)
    {
        $strBanderaServicioProdCarct = "N";

        $objAdmiCaracteristica = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                             ->findOneBy(array("descripcionCaracteristica" => $arrayParametros["strCaracteristica"],
                                                               "estado"                    => "Activo"));

        if(is_object($objAdmiCaracteristica) && is_object($arrayParametros["objProducto"]))
        {
            $objAdmiProductoCaract = $this->emcom->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                 ->findOneBy(array("caracteristicaId" => $objAdmiCaracteristica->getId(),
                                                                   "productoId"       => $arrayParametros["objProducto"]->getId()));
        }

        if(is_object($objAdmiProductoCaract) && is_object($arrayParametros["objServicio"]))
        {
            $objServProdCaractVlan = $this->emcom->getRepository('schemaBundle:InfoServicioProdCaract')
                                                 ->findOneBy(array("servicioId"                => $arrayParametros["objServicio"]->getId(),
                                                                   "productoCaracterisiticaId" => $objAdmiProductoCaract->getId(),
                                                                   "estado"                    => "Activo"));

            if(is_object($objServProdCaractVlan))
            {
                $strBanderaServicioProdCarct = "S";
            }
        }

        return $strBanderaServicioProdCarct;
    }

    /**
     * Devuelve las formas de pago activas
     * @param string $idKey key a usar en el array para el id de la forma pago
     * @param string $descripcionKey key a usar en el array para la descripcion de la forma pago
     * @return array de arrays id/descripcion
     * @see \telconet\schemaBundle\Entity\AdmiFormaPago
     */
    public function obtenerFormasPago($idKey = 'id', $descripcionKey = 'descripcion')
    {
        /* @var $repoAdmiFormaPago \telconet\schemaBundle\Repository\AdmiFormaPagoRepository */
        $repoAdmiFormaPago = $this->emcom->getRepository('schemaBundle:AdmiFormaPago');
        $list = $repoAdmiFormaPago->findFormasPagoXEstado('Activo')->getQuery()->getResult();
        $arreglo = array();
        /* @var $value \telconet\schemaBundle\Entity\AdmiFormaPago */
        foreach ($list as $value):
        $arreglo[] = array($idKey => $value->getId(), $descripcionKey => $value->getDescripcionFormaPago());
        endforeach;
        return $arreglo;
    }
    
    /**
     * Devuelve los tipo cuenta
     * @param string $idKey key a usar en el array para el id del tipo cuenta
     * @param string $descripcionKey key a usar en el array para la descripcion del tipo cuenta
     * @return array de arrays id/descripcion
     * @see \telconet\schemaBundle\Entity\AdmiTipoCuenta
     */
    public function obtenerTiposCuenta($idKey = 'id', $descripcionKey = 'descripcion')
    {
        $list = $this->emcom->getRepository('schemaBundle:AdmiTipoCuenta')->findAll();
        $arreglo = array();
        /* @var $value \telconet\schemaBundle\Entity\AdmiTipoCuenta */
        foreach ($list as $value):
        $arreglo[] = array($idKey => $value->getId(), $descripcionKey => $value->getDescripcionCuenta());
        endforeach;
        return $arreglo;
    }
    
    /**
     * Devuelve los banco tipo cuenta del tipo cuenta dado
     * @param integer $idTipoCuenta
     * @param string $idKey key a usar en el array para el id del banco tipo cuenta
     * @param string $descripcionKey key a usar en el array para la descripcion del banco del banco tipo cuenta
     * @return array de arrays id/descripcion
     * @see \telconet\schemaBundle\Entity\AdmiBancoTipoCuenta
     */
    public function obtenerBancosTipoCuenta($idTipoCuenta, $idKey = 'id', $descripcionKey = 'descripcion')
    {
        $list = $this->emcom->getRepository('schemaBundle:AdmiBancoTipoCuenta')->findListarBancosSegunTipoCuenta($idTipoCuenta);
        $arreglo = array();
        /* @var $value \telconet\schemaBundle\Entity\AdmiBancoTipoCuenta */
        foreach ($list as $value):
        $arreglo[] = array($idKey => $value->getId(), $descripcionKey => $value->getBancoId()->getDescripcionBanco());
        endforeach;
        return $arreglo;
    }
    
    /**
     * Devuelve las formas de contacto activas
     * @param string $idKey key a usar en el array para el id de la forma contacto
     * @param string $descripcionKey key a usar en el array para la descripcion de la forma contacto
     * @return array de arrays id/descripcion
     * @see \telconet\schemaBundle\Entity\AdmiFormaContacto
     */
    public function obtenerFormasContacto($idKey = 'id', $descripcionKey = 'descripcion')
    {
        $list = $this->emcom->getRepository('schemaBundle:AdmiFormaContacto')->findFormasContactoPorEstado('Activo');
        $arreglo = array();
        /* @var $value \telconet\schemaBundle\Entity\AdmiFormaContacto */
        foreach ($list as $value):
        $arreglo[] = array($idKey => $value->getId(), $descripcionKey => $value->getDescripcionFormaContacto());
        endforeach;
        return $arreglo;
    }
    
    /**
     * Función que retorna las formas de contacto en estado Activo y según los códigos enviados comomparámetro.
     * 
     * @author  Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 22-02-2018
     * 
     * @param   array $arrayCodFormasContacto Arreglo con códigos de formas de contacto
     * @return  array $arrayFormasContacto
     */
    public function getFormasContactoByCodigo($arrayCodFormasContacto)
    {
        $arrayAdmiFormaContacto = $this->emcom->getRepository('schemaBundle:AdmiFormaContacto')
                                              ->findBy(array("codigo" => $arrayCodFormasContacto, 
                                                             "estado" => 'Activo'));
        
        $arrayFormasContacto = array();
        foreach ($arrayAdmiFormaContacto as $objAdmiFormaContacto):
            $arrayFormasContacto[] = array('id'          => $objAdmiFormaContacto->getId(), 
                                           'descripcion' => $objAdmiFormaContacto->getDescripcionFormaContacto());
        endforeach;

        return $arrayFormasContacto;
    }    
    
    /**
     * Devuelve las parroquias del canton dado, filtradas por nombre
     * @param integer $intIdCanton
     * @param string $strNombre (nullable)
     * @param string $idKey key a usar en el array para el id de la parroquia
     * @param string $nombreKey key a usar en el array para la descripcion de la parroquia
     * @return array de arrays id/descripcion
     * @see \telconet\schemaBundle\Entity\AdmiParroquia
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
     * @version 1.1 11-04-2016
     * Se cambia listado de resultado de AdmiCantonJurisdiccion a arreglo [id-nombreParroquia] de la entidad AdmiParroquia.
     * Renombrado de variables.
     */
    public function obtenerParroquiasCanton($intIdCanton, $strNombre = NULL, $strIdKey = 'id', $strNombreKey = 'nombre')
    {
        $arrayParametros['CANTONID'] = $intIdCanton;
        $arrayParametros['NOMBRE']   = strtoupper(trim($strNombre));
        $arrayParametros['ESTADO'] = 'Activo';
        $arrayParametros['VALUE']    = $strIdKey;
        $arrayParametros['DISPLAY']  = $strNombreKey;
        
        return $this->emcom->getRepository('schemaBundle:AdmiParroquia')->getParroquiasPorCantonPorNombre($arrayParametros);
    }
    
    /**
     * Devuelve los sectores de la parroquia dada, filtrados por nombre
     * @param integer $intIdParroquia
     * @param string $strNombre (nullable)
     * @param string $strIdKey key a usar en el array para el id del sector
     * @param string $strNombreKey key a usar en el array para la descripcion del sector
     * @return array de arrays id/descripcion
     * @see \telconet\schemaBundle\Entity\AdmiSector
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
     * @version 1.1 11-04-2016
     * Se cambia listado de resultado de AdmiCantonJurisdiccion a arreglo [id-nombreParroquia] de la entidad AdmiParroquia.
     * Renombrado de variables.
     */
    public function obtenerSectoresParroquia($empresaCod,$intIdParroquia, $strNombre = NULL, $strIdKey = 'id', $strNombreKey = 'nombre')
    {
        $arrayParametros['PARROQUIAID'] = $intIdParroquia;
        $arrayParametros['NOMBRE']      = strtoupper(trim($strNombre));
        $arrayParametros['EMPRESA']     = $empresaCod;
        $arrayParametros['ESTADO']      = 'Activo';
        $arrayParametros['VALUE']       = $strIdKey;
        $arrayParametros['DISPLAY']     = $strNombreKey;
        
        return $this->emcom->getRepository('schemaBundle:AdmiSector')->getSectoresPorParroquiaPorNombre($arrayParametros);
    }

    /**
     * Devuelve las formas de contacto filtrado por
     * @param string $idKey key a usar en el array para el id de la forma contacto
     * @param string $descripcionKey key a usar en el array para la descripcion de la forma contacto
     * @return array de arrays id/descripcion
     * @see \telconet\schemaBundle\Entity\AdmiFormaContacto
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 01/05/2019
     */
    public function getFormasContacto($arrayParametros)
    {
        $arrayFormaContacto = $this->emcom->getRepository('schemaBundle:AdmiFormaContacto')->getFormasContactoParametros($arrayParametros);
        $arrayRetorno = array();
        /* @var $value \telconet\schemaBundle\Entity\AdmiFormaContacto */
        foreach ($arrayFormaContacto as $arrayValue)
        {
            $arrayRetorno[] = array($arrayParametros['strKey']   => $arrayValue->getId(), 
                                    $arrayParametros['strValue'] => $arrayValue->getDescripcionFormaContacto());
        }
        return $arrayRetorno;
    }    


    /**
     * Devuelve las formas de contacto filtrado por
     * @param array $arrayParametro
     * @return array de arrays 
     * 
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.0 15/11/2021
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.1 18/01/2023 - Se agrega el parametro strEsLogin para validar si se debe realizar actualizar la función precio
     *                           solo del servicio a realizar el CRS.
     */
    public function funcionPrecioRegularizar($arrayParametro)
    {
        $this->emcom->getConnection()->beginTransaction();
        $strUsrCreacion = $arrayParametro['strUsrCreacion'];
        $intIdCliente   = $arrayParametro['intIdCliente'];
        $intIdEmpresa   = $arrayParametro['intIdEmpresa'];
        $strEsLogin     = $arrayParametro['strEsLogin'];
        $arrayLoginCRS  = $arrayParametro['arrayLogin'];
        $strValorSi     = "SI";
        $strValorNo     = "NO";
        $strValorDefault= "NO";
        try
        {
            if($strEsLogin == "S")
            {
                foreach($arrayLoginCRS as $strLoginCRS)
                {
                    $arrayLogin = $this->emcom->getRepository('schemaBundle:InfoPunto')
                                               ->findPtosPorEmpresaPorClientePorRol($intIdEmpresa,
                                                                                    $intIdCliente,
                                                                                    $strLoginCRS,
                                                                                    "Cliente",
                                                                                    99999999,
                                                                                    1,
                                                                                    0,
                                                                                    '');
                    $arrayPuntos['registros'][] = $arrayLogin['registros'][0];
                }
            }
            else
            {
                $arrayPuntos = $this->emcom->getRepository('schemaBundle:InfoPunto')
                                           ->findPtosPorEmpresaPorClientePorRol($intIdEmpresa,
                                                                                $intIdCliente,
                                                                                "",
                                                                                "Cliente",
                                                                                99999999,
                                                                                1,
                                                                                0,
                                                                                '');
            }

            $arrayPtos  = $arrayPuntos['registros'];
            foreach($arrayPtos as $objPuntos)
            {
                $arrayServicio = $this->emcom->getRepository('schemaBundle:InfoServicio')
                                             ->findServiciosPorEmpresaPorPunto($intIdEmpresa, 
                                                                               $objPuntos['id'],
                                                                               99999999,
                                                                               1,
                                                                               0);
                $arrayServ = $arrayServicio['registros'];

                foreach($arrayServ as $objServicio)
                {
                    if(is_object($objServicio->getProductoId()) )
                    {
                        $objProducto = $objServicio->getProductoId();
                    }
                    else
                    {
                        continue;
                    }

                    $arrayProductoCaracteristica = $this->emcom->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                               ->findBy(array("productoId" => $objProducto->getId()));
                    foreach($arrayProductoCaracteristica as $objProdCarac)
                    {
                        $strFuncionPrecio = $objProducto->getFuncionPrecio();
                        if(strpos($strFuncionPrecio, $objProdCarac->getCaracteristicaId()->getDescripcionCaracteristica()) !== false)
                        {
                            $arrayValorRequerido[] = array('key' => $objProdCarac->getId());
                        }
                    }

                    if(!empty($arrayValorRequerido))
                    {
                        foreach($arrayValorRequerido as $objValorRequerido)
                        {
                            $objServicioCaracteristica = $this->emcom->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                    ->findOneBy(array("servicioId"                 => $objServicio->getId(),
                                                                                    "productoCaracterisiticaId"  => $objValorRequerido['key'],
                                                                                    "estado"                     => 'Activo'));

                            $objAdmiProdCaracComp = $this->emcom->getRepository('schemaBundle:AdmiProdCaracComp')
                                                                                    ->findOneBy(array(
                                                                                        'productoCaracteristicaId' => $objValorRequerido['key']
                                                                                    ));
                            if(is_object($objAdmiProdCaracComp))
                            {
                                $strValoresSeleccionable    =   $objAdmiProdCaracComp->getValoresSeleccionable();
                                $strValorDefault            =   $objAdmiProdCaracComp->getValoresDefault();
                            }

                            if(!is_object($objServicioCaracteristica))
                            {
                                $objServicioProdCaractNew = new InfoServicioProdCaract();
                                $objServicioProdCaractNew->setServicioId($objServicio->getId());
                                $objServicioProdCaractNew->setProductoCaracterisiticaId($objValorRequerido['key']);
                                $intPrecioVenta = $objServicio->getPrecioVenta();
                                if(!empty($intPrecioVenta) && $intPrecioVenta >= 0)
                                {
                                    if (strpos($strValoresSeleccionable, $strValorNo)) 
                                    {
                                        $objServicioProdCaractNew->setValor($strValorNo);
                                    }
                                    else
                                    {
                                        $objServicioProdCaractNew->setValor($strValorDefault);
                                    }
                                }
                                else
                                {
                                    if (strpos($strValoresSeleccionable, $strValorSi)) 
                                    {
                                        $objServicioProdCaractNew->setValor($strValorSi);
                                    }
                                    else
                                    {
                                        $objServicioProdCaractNew->setValor($strValorDefault);
                                    }
                                }
                                $objServicioProdCaractNew->setEstado('Activo');
                                $objServicioProdCaractNew->setUsrCreacion($strUsrCreacion);
                                $objServicioProdCaractNew->setFeCreacion(new \DateTime('now'));
                                $this->emcom->persist($objServicioProdCaractNew);
                                $this->emcom->flush();
                            }
                            else
                            {
                                $strValorCaracter = $objServicioCaracteristica->getValor();
                                if(empty($strValorCaracter))
                                {
                                    $intPrecioVenta = $objServicio->getPrecioVenta();
                                    if(!empty($intPrecioVenta) && $intPrecioVenta >= 0)
                                    {
                                        if (strpos($strValoresSeleccionable, $strValorNo)) 
                                        {
                                            $objServicioCaracteristica->setValor($strValorNo);
                                        }
                                        else
                                        {
                                            $objServicioCaracteristica->setValor($strValorDefault);
                                        }
                                    }
                                    else 
                                    {
                                        if (strpos($strValoresSeleccionable, $strValorSi)) 
                                        {
                                            $objServicioCaracteristica->setValor($strValorSi);
                                        }
                                        else
                                        {
                                            $objServicioCaracteristica->setValor($strValorDefault);
                                        }
                                    }
                                    $objServicioCaracteristica->setUsrUltMod($strUsrCreacion);
                                }
                                $this->emcom->persist($objServicioCaracteristica);
                                $this->emcom->flush();
                            }
                        }
                    }
                }
            }
            if($this->emcom->getConnection()->isTransactionActive())
            {
                $this->emcom->getConnection()->commit();
                $this->emcom->getConnection()->close();
            }
        }
        catch(\Exception $ex)
        {
            if($this->emcom->getConnection()->isTransactionActive())
            {
                $this->emcom->getConnection()->rollback();
                $this->emcom->getConnection()->close();
            }
            $this->serviceUtil->insertError('TelcoS+',
                                            'ClienteController.funcionPrecioRegularizar',
                                            $ex->getMessage(),
                                            $strUsrCreacion,
                                            "127.0.0.1");
        }
    }

    /**
     * Devuelve las formas de pago, ó los tipos de cuenta por país, según el tipo de proceso envíado por parámetro.
     * @param string $idKey key a usar en el array para el id.
     * @param string $descripcionKey key a usar en el array para la descripcion.
     * @return array de arrays id/descripcion
     * 
     * @author Alex Arreaga <atarreaga@telconet.ec>
     * @version 1.0 
     * @since 25-11-2021
     */
    public function obtenerDatosTipoCtaFormaPago($arrayParametros)
    {
        if($arrayParametros['strTipoProceso'] == "TIPO_CUENTA")
        {
            $arrayList = $this->emcom->getRepository('schemaBundle:AdmiTipoCuenta')
                            ->findBy(array("paisId" => $arrayParametros['strCodigoPais'], "estado" =>"Activo"));

            $arrayTipoCuenta = array();
            /* @var $value \telconet\schemaBundle\Entity\AdmiTipoCuenta */
            foreach ($arrayList as $value):
            $arrayTipoCuenta[] = array($arrayParametros['strKey'] => $value->getId(),
                                       $arrayParametros['strValue'] => $value->getDescripcionCuenta());
            endforeach;
            return $arrayTipoCuenta; 
        }
        else if($arrayParametros['strTipoProceso'] == "FORMA_PAGO")
        {
            /* @var $objRepoAdmiFormaPago \telconet\schemaBundle\Repository\AdmiFormaPagoRepository */
            $objRepoAdmiFormaPago = $this->emcom->getRepository('schemaBundle:AdmiFormaPago');
            $arrayList = $objRepoAdmiFormaPago->findFormasPagoXEstado('Activo',$arrayParametros["arrayFormasPago"])->getQuery()->getResult();
            $arrayFormaPago = array();
            /* @var $value \telconet\schemaBundle\Entity\AdmiFormaPago */
            foreach ($arrayList as $value):
            $arrayFormaPago[] = array($arrayParametros['strKey'] => $value->getId(), 
                                      $arrayParametros['strValue'] => $value->getDescripcionFormaPago());
            endforeach;
            return $arrayFormaPago;
        }
    }
    
    /* Función para subir uno o varios archivos como evidencias para el excedente de materiales.
     *
     * @author Liseth Candelario <lcandelario@telconet.ec>
     * @version 1.0 08/03/2021
     * 
     * @param type $arrayParametros   
     *                  trae el id_servicio, el PrefijoEmpresa, el Usuario, y el IdEmpresa
     *  
     */
    public function guardarArchivosMultiplesEvidencias($arrayParametros)
    {
        $strFechaCreacion     = new \DateTime('now');
        $strServerRoot        = $this->path . "telcos/web";

        $intIdServicio      = $arrayParametros['intIdServicio'];
        $strPrefijoEmpresa  = $arrayParametros['strPrefijoEmpresa'];
        $strUser            = $arrayParametros['strUser'];
        $strIdEmpresa       = $arrayParametros['strIdEmpresa'];
        $arrayArchivos      = $arrayParametros['arrayArchivos'];

        $arrayRespuesta     = array();
        $arrayRutasArchivosSubidos = array();
        $this->emComunicacion->getConnection()->beginTransaction();

        try
        {
            foreach ($arrayArchivos as $objArchivo) 
            {
                if (is_object($objArchivo))
                {
                    $strNameFile                        = $objArchivo->getClientOriginalName();
                    //Se divide para obtener nombre y extension de archivo
                    $arrayPartsNombreArchivo            = explode('.', $strNameFile);
                    $strLast                            = array_pop($arrayPartsNombreArchivo);
                    $arrayPartsNombreArchivo            = array(implode('_', $arrayPartsNombreArchivo), $strLast);

                    $strNombreArchivo                   = $arrayPartsNombreArchivo[0];
                    $strExtArchivo                      = $arrayPartsNombreArchivo[1];
                    $strTipo                            = $strExtArchivo;
                    $strPrefijo                            = substr(md5(uniqid(rand())), 0, 6);
                    $strNuevoNombre                     = $strNombreArchivo . "_" . $strPrefijo . "." . $strExtArchivo;
                    $strTofind                             = "#ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ·";
                    $strReplac                             = "_AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn-";
                    $strNuevoNombre                     = strtr($strNuevoNombre, $strTofind, $strReplac);
                    $strDestino                            = $strServerRoot . "/public/uploads/" . $strPrefijoEmpresa . "/";
                    $strPath                            = "public/uploads/" . $strPrefijoEmpresa . "/";
                    $strModulo                             = "";
                    $strFuncion                            = "";
                    $strDirectorioFechaActual              = "";


                    if($strPrefijoEmpresa=="TN" && $intIdServicio)
                    {
                        // Se verifica si existe directorio creado por fecha actual
                        $strModulo                 = "comercial/exdenteMateriales/";
                        $strFuncion                = "evidencias/";
                        $strDirectorioFechaActual  = $this->serviceSoporte->generarDirectorioFechaActual($strDestino);

                    }

                    $strDestino .= $strModulo . $strFuncion;
                    $strPath .= $strModulo . $strFuncion;
                    if ($strDirectorioFechaActual != "") 
                    {
                        $strDestino .= $strDirectorioFechaActual;
                        $strPath .= $strDirectorioFechaActual;
                    }

                    $strFicheroSubido = $strDestino . $strNuevoNombre;

                    //Guardar en base         
                    $entityInfoDocumento = new InfoDocumento();

                    $entityInfoDocumento->setNombreDocumento('Adjunto Archivo de Evidencia');
                    $entityInfoDocumento->setMensaje('Documento que se adjunta como evidencia por acuerdo para excedente de materiales');

                    $entityInfoDocumento->setUbicacionFisicaDocumento($strFicheroSubido);
                    $entityInfoDocumento->setUbicacionLogicaDocumento($strNuevoNombre);

                    $entityInfoDocumento->setEstado('Activo');
                    $entityInfoDocumento->setFeCreacion($strFechaCreacion);
                    $entityInfoDocumento->setFechaDocumento($strFechaCreacion);
                    $entityInfoDocumento->setIpCreacion('127.0.0.1');
                    $entityInfoDocumento->setUsrCreacion($strUser);
                    $entityInfoDocumento->setEmpresaCod($strIdEmpresa);

                    $strTipoDoc =  strtoupper($strTipo);
                    if ($strTipoDoc == 'JPG' || $strTipo == 'JPEG') 
                    {
                        $strTipoDoc = "JPG";
                    }

                    $objTipoDocumento = $this->emComunicacion->getRepository('schemaBundle:AdmiTipoDocumento')
                    ->findOneByExtensionTipoDocumento(array('extensionTipoDocumento' => $strTipoDoc));

                    if ($objTipoDocumento != null) 
                    {
                        $entityInfoDocumento->setTipoDocumentoId($objTipoDocumento);
                    }
                     else
                     {

                        //Inserto registro con la extension del archivo a subirse
                        $objAdmiTipoDocumento = new AdmiTipoDocumento();
                        $objAdmiTipoDocumento->setExtensionTipoDocumento(strtoupper($strTipoDoc));
                        $objAdmiTipoDocumento->setTipoMime(strtoupper($strTipoDoc));
                        $objAdmiTipoDocumento->setDescripcionTipoDocumento('ARCHIVO FORMATO ' . $strTipoDoc);
                        $objAdmiTipoDocumento->setEstado('Activo');
                        $objAdmiTipoDocumento->setUsrCreacion($strUser);
                        $objAdmiTipoDocumento->setFeCreacion($strFechaCreacion);

                        $this->emComunicacion->persist($objAdmiTipoDocumento);
                        $this->emComunicacion->flush();
                        $entityInfoDocumento->setTipoDocumentoId($objAdmiTipoDocumento);
                    }

                    //Mueve un archivo cargado a una nueva ubicación
                    move_uploaded_file($objArchivo->getPathName(), $strFicheroSubido);
                    $arrayRutasArchivosSubidos[] = $strFicheroSubido;

                    $this->emComunicacion->persist($entityInfoDocumento);
                    $this->emComunicacion->flush();

                    //Entidad de la tabla INFO_DOCUMENTO_RELACION donde se relaciona el documento cargado con el ServicioId
                    $entityRelacion = new InfoDocumentoRelacion();
                    $entityRelacion->setModulo('COMERCIAL');
                    $entityRelacion->setEstado('Activo');
                    $entityRelacion->setFeCreacion($strFechaCreacion);
                    $entityRelacion->setServicioId($intIdServicio);
                    $entityRelacion->setUsrCreacion($strUser);

                    $entityRelacion->setDocumentoId($entityInfoDocumento->getId());

                    $this->emComunicacion->persist($entityRelacion);
                    $this->emComunicacion->flush();
                }
            }

            if ($this->emComunicacion->getConnection()->isTransactionActive()) 
            {
                $this->emComunicacion->getConnection()->commit();
            }

            $this->emComunicacion->getConnection()->close();
            $arrayRespuesta     = array('status' => 'Ok', 'mensaje' => 'Los archivos se subieron exitosamente', 'success' => true);
            return $arrayRespuesta;
            } 
            catch (\Exception $e) 
            {
            $strMensajeError  = 'Ha ocurrido un error, por favor reporte a Sistemas';

            //Eliminar Archivos subidos
            foreach ($arrayRutasArchivosSubidos as $rutaEliminar) 
            {
                unlink($rutaEliminar);
            }

            if ($this->emComunicacion->getConnection()->isTransactionActive()) 
            {
                $this->emComunicacion->getConnection()->rollback();
            }
            $this->emComunicacion->getConnection()->close();

            if (strpos(strtolower($e->getMessage()), strtolower("Archivo con extensión")) >= 0) 
            {
                $strMensajeError =  $e->getMessage();
            }
            $arrayRespuesta     = array('status' => 'Error', 'mensaje' => $strMensajeError, 'success' => 'false');
            return $arrayRespuesta;
        }
    }

    /**
     * Función para subir uno o varios archivos como evidencias para el excedente de materiales
     *             mediante la funcion guardarArchivosNfs que está en schemaBunle/service.
     *
     * @author Liseth Candelario <lcandelario@telconet.ec>
     * @version 1.0 08/03/2021
     * 
     * @param type $arrayParametros   
     *                  trae el id_servicio, el PrefijoEmpresa, el Usuario, y el IdEmpresa
     *  
     */

    public function guardarArchivosMultiplesEvidenciasEnNfs($arrayParametros)
    {
        $strFechaCreacion     = new \DateTime('now');

        $intIdServicio      = $arrayParametros['intIdServicio'];
        $strPrefijoEmpresa  = $arrayParametros['strPrefijoEmpresa'];
        $strUser            = $arrayParametros['strUser'];
        $strIdEmpresa       = $arrayParametros['strIdEmpresa'];
        $arrayArchivos      = $arrayParametros['arrayArchivos'];
        $strApp             = "";
        $arrayPathAdicional = [];
        $strSubModulo       = "";
        $this->emcom->getConnection()->beginTransaction();
        $this->emComunicacion->getConnection()->beginTransaction();
        $strSeguimientoDocumento = 'Se adjunta archivo de evidencia por acuerdo para materiales excedentes';
        $entityServicio          = $this->emcom->getRepository('schemaBundle:InfoServicio')
                                                ->findOneById(array("id"       => $intIdServicio));
        $strIpCreacion      = '127.0.0.1';
        $serviceAutorizacion = $this->serviceAutorizaciones;
        $serviceSolicitudes  = $this->serviceSolicitud;

        $arrayRespuesta     = array();

        try
        {
            foreach ($arrayArchivos as $objArchivo)
            {
                if (is_object($objArchivo))
                {
                    $strNameFile                        = $objArchivo->getClientOriginalName();
                    $arrayPartsNombreArchivo            = explode('.', $strNameFile);
                    $strLast                            = array_pop($arrayPartsNombreArchivo);
                    $arrayPartsNombreArchivo            = array(implode('_', $arrayPartsNombreArchivo), $strLast);

                    $strNombreArchivo                   = $arrayPartsNombreArchivo[0];
                    $strExtArchivo                      = $arrayPartsNombreArchivo[1];
                    $strTipo                            = $strExtArchivo;
                    $strPrefijo                         = substr(md5(uniqid(rand())),0,6);
                    $strNuevoNombre                     = $strNombreArchivo . "_" . $strPrefijo . "." . $strExtArchivo;

                    // Se reemplazan caracteres que no cumplen con el patron definido para el nombre del archivo
                    $strPatronABuscar = '/[^a-zA-Z0-9._-]/';
                    $strCaracterReemplazo = '_';
                    $strNuevoNombre = preg_replace($strPatronABuscar,$strCaracterReemplazo,$strNuevoNombre);
                    
                    $strApp       = "TelcosWeb";
                    $strSubModulo = "Tareas";

                    //####################################
                    //INICIO DE SUBIR ARCHIVO AL NFS >>>>>
                    //####################################

                    $strFile         = base64_encode(file_get_contents($objArchivo->getPathName()));
                    $arrayParamNfs   = array(
                                            'prefijoEmpresa'       => $strPrefijoEmpresa,
                                            'strApp'               => $strApp,
                                            'arrayPathAdicional'   => $arrayPathAdicional,
                                            'strBase64'            => $strFile,
                                            'strNombreArchivo'     => $strNuevoNombre,
                                            'strUsrCreacion'       => $strUser,
                                            'strSubModulo'         => $strSubModulo);

                    $arrayRespNfs = $this->serviceUtil->guardarArchivosNfs($arrayParamNfs);
                    if ($arrayRespNfs['intStatus'] == 500 )
                    {
                        throw new \Exception('Ocurrió un error al subir archivo al servidor Nfs : '.$arrayRespNfs['strMensaje']);
                    }
                    else
                    {
                        $strFicheroSubido = $arrayRespNfs['strUrlArchivo'];
                    }

                    //##################################
                    //<<<<< FIN DE SUBIR ARCHIVO AL NFS
                    //##################################


                    //Guardar en base
                    $objInfoDocumento = new InfoDocumento();

                    $objInfoDocumento->setNombreDocumento('Adjunto Archivo de Evidencia');
                    $objInfoDocumento->setMensaje('Documento que se adjunta como evidencia de un acuerdo para excedente de materiales');

                    $objInfoDocumento->setUbicacionFisicaDocumento($strFicheroSubido);
                    $objInfoDocumento->setUbicacionLogicaDocumento($strNuevoNombre);

                    $objInfoDocumento->setEstado('Activo');
                    $objInfoDocumento->setFeCreacion($strFechaCreacion);
                    $objInfoDocumento->setFechaDocumento($strFechaCreacion);
                    $objInfoDocumento->setIpCreacion('127.0.0.1');
                    $objInfoDocumento->setUsrCreacion($strUser);
                    $objInfoDocumento->setEmpresaCod($strIdEmpresa);

                    $strTipoDoc =  strtoupper($strTipo);
                    if ($strTipoDoc == 'JPG' || $strTipo == 'JPEG') 
                    {
                        $strTipoDoc = "JPG";
                    }

                    $objTipoDocumento = $this->emComunicacion->getRepository('schemaBundle:AdmiTipoDocumento')
                    ->findOneByExtensionTipoDocumento(array('extensionTipoDocumento' => $strTipoDoc));
                    if ($objTipoDocumento == null) 
                    {
                        throw new \Exception(': PROBLEMA CON EL ARCHIVO, <br>   <b> '.$strTipoDoc.', NO ES UNA EXTENCIÓN VÀLIDA</b>');
                    }

                    if ($objTipoDocumento != null) 
                    {
                        $objInfoDocumento->setTipoDocumentoId($objTipoDocumento);
                                       
                        //Inserto registro con la extension del archivo a subirse
                        $objAdmiTipoDocumento = new AdmiTipoDocumento();
                        $objAdmiTipoDocumento->setExtensionTipoDocumento(strtoupper($strTipoDoc));
                        $objAdmiTipoDocumento->setTipoMime(strtoupper($strTipoDoc));
                        $objAdmiTipoDocumento->setDescripcionTipoDocumento('ARCHIVO FORMATO ' . $strTipoDoc);
                        $objAdmiTipoDocumento->setEstado('Activo');
                        $objAdmiTipoDocumento->setUsrCreacion($strUser);
                        $objAdmiTipoDocumento->setFeCreacion($strFechaCreacion);
                        $this->emComunicacion->persist($objAdmiTipoDocumento);
                        $this->emComunicacion->flush();
                        $objInfoDocumento->setTipoDocumentoId($objAdmiTipoDocumento);
                    }
                    else
                    {
                        throw new \Exception(': PROBLEMA CON EL ARCHIVO, <br>   <b> '.$strTipoDoc.', NO ES UNA EXTENCIÓN VÀLIDA</b>');
                    }

                    if ($strTipoDoc === "JPG" || $strTipo === 'JPEG' || $strTipo === 'PNG') 
                    {

                        $arrayExif      = exif_read_data($objArchivo->getPathName());
                        $floatLatitud   = 0;
                        $floatLongitud  = 0;
                        if (
                            isset($arrayExif["GPSLatitude"]) && !empty($arrayExif["GPSLatitude"])
                            && isset($arrayExif["GPSLatitudeRef"]) && !empty($arrayExif["GPSLatitudeRef"])
                        ) 
                        {
                            $floatLatitud   = $this->serviceSoporte->getCoordenadaGps($arrayExif["GPSLatitude"], $arrayExif['GPSLatitudeRef']);
                        }

                        if (
                            isset($arrayExif["GPSLongitude"]) && !empty($arrayExif["GPSLongitude"])
                            && isset($arrayExif["GPSLongitudeRef"]) && !empty($arrayExif["GPSLongitudeRef"])
                        ) 
                        {
                            $floatLongitud  = $this->serviceSoporte->getCoordenadaGps($arrayExif["GPSLongitude"], $arrayExif['GPSLongitudeRef']);
                        }

                        $arrayCoordenadas["floatLatitud"]     = $floatLatitud;
                        $arrayCoordenadas["floatLongitud"]    = $floatLongitud;

                        if (isset($arrayCoordenadas["floatLatitud"]) && !empty($arrayCoordenadas["floatLatitud"])) 
                        {
                            $floatLatitud   = $arrayCoordenadas["floatLatitud"];
                            $objInfoDocumento->setLatitud($floatLatitud);
                        }

                        if (isset($arrayCoordenadas["floatLongitud"]) && !empty($arrayCoordenadas["floatLongitud"])) 
                        {
                            $floatLongitud  = $arrayCoordenadas["floatLongitud"];
                            $objInfoDocumento->setLongitud($floatLongitud);
                        }
                    }

                    unlink($objArchivo->getPathName());

                    $this->emComunicacion->persist($objInfoDocumento);
                    $this->emComunicacion->flush();

                    //Entidad de la tabla INFO_DOCUMENTO_RELACION donde se relaciona el documento cargado con el IdCaso
                    $objInfoDocumentoRelacion = new InfoDocumentoRelacion();
                    $objInfoDocumentoRelacion->setModulo('COMERCIAL');
                    $objInfoDocumentoRelacion->setEstado('Activo');
                    $objInfoDocumentoRelacion->setFeCreacion($strFechaCreacion);
                    $objInfoDocumentoRelacion->setUsrCreacion($strUser);
                    $objInfoDocumentoRelacion->setServicioId($intIdServicio);

                    $objInfoDocumentoRelacion->setDocumentoId($objInfoDocumento->getId());

                    $this->emComunicacion->persist($objInfoDocumentoRelacion);
                    $this->emComunicacion->flush();

                    //GUARDAR INFO SERVICIO HISTORIAL - InfoServicioHistorial,
                    $strEstadoEnviado = $entityServicio->getEstado();
                    $arrayParametrosTraServ = array(
                                "emComercial"                => $this->emcom,
                                "strClienteIp"               => $strIpCreacion,
                                "objServicio"                => $entityServicio,
                                "strSeguimiento"             => $strSeguimientoDocumento,
                                "strUsrCreacion"             => $strUser,
                                "strAccion"                  => '',
                                "strEstadoEnviado"           => $strEstadoEnviado );
                    $arrayVerificar = $serviceAutorizacion ->registroTrazabilidadDelServicio($arrayParametrosTraServ);
                    if($arrayVerificar['status'] == 'ERROR' )
                    {
                    throw new \Exception(': NO SE REALIZÓ EL PROCESO COMPLETO:registroTrazabilidadDelServicio
                                            <br/> <b>'.$arrayVerificar['mensaje'].'</b>');
                    }
                }
            }

            if ($this->emComunicacion->getConnection()->isTransactionActive())
            {
                $this->emComunicacion->getConnection()->commit();
                
            }
            if ($this->emcom->getConnection()->isTransactionActive())
            {                
                $this->emcom->getConnection()->commit();
                
            }
            $this->emComunicacion->getConnection()->close();
            $this->emcom->getConnection()->close();
            $arrayRespuesta     = array('status' => 'Ok', 'mensaje' => 'Los archivos se subieron exitosamente', 'success' => true);

            //REGISTRAMOS EN LOG
            $arrayParametrosLog['enterpriseCode']   = $strIdEmpresa; 
            $arrayParametrosLog['logType']          = "1";
            $arrayParametrosLog['logOrigin']        = "TELCOS";
            $arrayParametrosLog['application']      = "TELCOS";
            $arrayParametrosLog['appClass']         = "ClienteService";
            $arrayParametrosLog['appMethod']        = "guardarArchivosMultiplesEvidenciasEnNfs";
            $arrayParametrosLog['messageUser']      = "No aplica.";
            $arrayParametrosLog['status']           = "OK";
            $arrayParametrosLog['descriptionError'] = "Se guarda archivo correctamente a través de microservicio de Nfs (".$strFicheroSubido.")";
            $arrayParametrosLog['inParameters']     = json_encode($arrayParametros);
            $arrayParametrosLog['creationUser']     = "TELCOS";

            $this->serviceUtil->insertLog($arrayParametrosLog);
            return $arrayRespuesta;
       }
       catch(\Exception $objE)
       {
           $strMensajeError  = 'Ha ocurrido un error, por favor reporte a Sistemas';

           if ($this->emComunicacion->getConnection()->isTransactionActive())
           {
               $this->emComunicacion->getConnection()->rollback();
           }
           $this->emComunicacion->getConnection()->close();

           if (strpos(strtolower($objE->getMessage()), strtolower("Archivo con extensión")) >= 0)
           {
               $strMensajeError =  $objE->getMessage();
           }
           $this->serviceUtil->insertError('Telcos+',
                        'ClienteService.guardarArchivosMultiplesEvidenciasEnNfs',
                        'Error ClienteService.guardarArchivosMultiplesEvidenciasEnNfs:'.$objE->getMessage(),
                                           $strUser,
                                           '127.0.0.1');
           error_log($objE->getMessage());
           $arrayRespuesta     = array('status' => 'Error', 'mensaje' => $strMensajeError, 'success' => 'false');
           return $arrayRespuesta;
       }
    }

    /**
     * getFormasContactoMS, obtiene las formas de contacto para el prospecto consumiendo el ms de credenciales
     *             
     *
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 23/10/2022
     * 
     * @param array $arrayParametros   
     *                  trae el token
     *  
     */

    public function getFormasContactoMS($arrayParametros)
    {
        try 
        {

            $objOptions = array(
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'tokencas: ' . $arrayParametros['token']
                )
            );

            $strJsonData = json_encode(
                [
                    "usrCreacion" => $arrayParametros['usrCreacion'],
                    "empresaCod" => $arrayParametros['empresaCod']
                ]
            );

            $arrayResponseJson  = $this->restClient->postJSON($this->strUrlTipoFormaContactoProspecto, $strJsonData, $objOptions); 
            $strJsonRespuesta = json_decode($arrayResponseJson['result'], true);
            error_log(json_encode($strJsonRespuesta));
            if (
                isset($strJsonRespuesta['code']) && $strJsonRespuesta['code'] == 0
                && isset($strJsonRespuesta['status'])
                && isset($strJsonRespuesta['message'])
            )
            {
                $arrayResponse = $strJsonRespuesta['data'];
                $arrayResultado = $arrayResponse;
            } 
            else 
            {
                $arrayResultado['status'] = "ERROR";
                if (empty($strJsonRespuesta['message'])) 
                {
                    $arrayResultado['message'] = "No Existe Conectividad con el WS MS COMP CREDENCIALES COMERCIAL.";
                } 
                else 
                {
                    $arrayResultado['message'] = $strJsonRespuesta['message'];
                }
            }

            return $arrayResultado;
        } 
        catch (\Exception $e) 
        {
            error_log("Error " . json_encode($e));
            $strRespuesta = "Error al ejecutar el re procesamiento del registro. Favor Notificar a Sistemas" . $e->getMessage();
            $arrayResultado = array('message' => $strRespuesta);
            $this->serviceUtil->insertError(
                'Telcos+',
                'ClienteService.getFormasContactoMS',
                'Error ClienteService.getFormasContactoMS:' . $e->getMessage(),
                'epin',
                '127.0.0.1'
            );
            return $arrayResultado;
        }

    }


    /**
     * personaRegularizacion, guarda las formas de contacto del prospecto consumiendo el ms de credenciales.
     * 
     * @author  Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 
     * 
     * @return array
     * 
     */
    public function personaRegularizacion($arrayParametros) 
    {    
        try
        {
            $objOptions         = array(CURLOPT_SSL_VERIFYPEER => false,
                                        CURLOPT_HTTPHEADER     => array('Content-Type: application/json',
                                                                        'tokencas: '.$arrayParametros['token'])
                                       ); 
            $strJsonData        = json_encode($arrayParametros['data']);

            $arrayResponseJson  = $this->restClient->postJSON($this->strUrlRegularizacionPersonaMs, $strJsonData , $objOptions);
            $strJsonRespuesta   = json_decode($arrayResponseJson['result'],true);

            if(isset($strJsonRespuesta['code']) && $strJsonRespuesta['code']==0 
            && isset($strJsonRespuesta['status'])
            && isset($strJsonRespuesta['message']) )
            {   
                $arrayResponse = array(
                                       'status' => $strJsonRespuesta['code'],
                                       'message'=> $strJsonRespuesta['message'],
                                       'data' =>  $strJsonRespuesta['data']);
                $arrayResultado = $arrayResponse;
            }
            else
            {
                $arrayResultado['status']      = "ERROR";
                if(empty($strJsonRespuesta['message']))
                {
                    $arrayResultado['message']  = "No Existe Conectividad con el WS MS COMP CREDENCIALES COMERCIAL.";
                }
                else
                {
                    $arrayResultado['message']  = $strJsonRespuesta['message'];
                }
            }

            return $arrayResultado;
        }
        catch(\Exception $e)
        {
            $strRespuesta   = "Error al ejecutar el re procesamiento del registro. Favor Notificar a Sistemas".$e->getMessage();
            $arrayResultado = array ('message'     =>$strRespuesta);
            $this->serviceUtil->insertError('Telcos+',
                                            'PreClienteService.guardarFormasContactoProspecto',
                                            'Error PreClienteService.guardarFormasContactoProspecto:'.$e->getMessage(),
                                            'epin',
                                            '127.0.0.1'); 
            return $arrayResultado;
        }
        
    }

        /**
     * guardarFormasContactoProspecto, guarda las formas de contacto del prospecto consumiendo el ms de credenciales.
     * 
     * @author  Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 
     * 
     * @return array
     * 
     */
    public function generarCredencial($arrayParametros) 
    {    
        try
        {
            $objOptions         = array(CURLOPT_SSL_VERIFYPEER => false,
                                        CURLOPT_HTTPHEADER     => array('Content-Type: application/json',
                                                                        'tokencas: '.$arrayParametros['token'])
                                       ); 
            $strJsonData        = json_encode($arrayParametros['data']);

            $arrayResponseJson  = $this->restClient->postJSON($this->strUrlgenerarCredencialMs , $strJsonData , $objOptions);
            $strJsonRespuesta   = json_decode($arrayResponseJson['result'],true);

            if(isset($strJsonRespuesta['code']) && $strJsonRespuesta['code']==0 
            && isset($strJsonRespuesta['status'])
            && isset($strJsonRespuesta['message']) )
            {   
                $arrayResponse = array(
                                       'status' => $strJsonRespuesta['code'],
                                       'message'=> $strJsonRespuesta['message']);
                $arrayResultado = $arrayResponse;
            }
            else
            {
                $arrayResultado['status']      = "ERROR";
                if(empty($strJsonRespuesta['message']))
                {
                    $arrayResultado['message']  = "No Existe Conectividad con el WS MS COMP CREDENCIALES COMERCIAL.";
                }
                else
                {
                    $arrayResultado['message']  = $strJsonRespuesta['message'];
                }
            }

            return $arrayResultado;
        }
        catch(\Exception $e)
        {
            $strRespuesta   = "Error al ejecutar el re procesamiento del registro. Favor Notificar a Sistemas".$e->getMessage();
            $arrayResultado = array ('message'     =>$strRespuesta);
            $this->serviceUtil->insertError('Telcos+',
                                            'PreClienteService.guardarFormasContactoProspecto',
                                            'Error PreClienteService.guardarFormasContactoProspecto:'.$e->getMessage(),
                                            'epin',
                                            '127.0.0.1'); 
            return $arrayResultado;
        }
        
    }

}

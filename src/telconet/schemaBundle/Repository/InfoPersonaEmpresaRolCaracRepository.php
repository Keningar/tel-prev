<?php

namespace telconet\schemaBundle\Repository;

use Doctrine\ORM\Query\ResultSetMappingBuilder;
use telconet\schemaBundle\DependencyInjection\BaseRepository;

class InfoPersonaEmpresaRolCaracRepository extends BaseRepository
{
    /**
     * Documentacion para el método 'getJsonVrfsClientePorVlan'
     * 
     * Método que devuelve un json de las vrfs de un cliente por vlan
     * 
     * 
     * @author Kenneth Jimenez <kjimenez@telconet.ec>
     * @version 1.0 28-03-2016
     *
     * @author Duval Medina C. <dmedina@telconet.ec>
     * @version 1.1 2016-08-22 - Recibir el codigo de empresoa en la función
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 26-08-2019 - Por proyecto migracion de vlan se agrega parametro 'strMigracionVlan' que permite identificar si se debe retornar
     *                           solo las vrf mapeadas Nedetel
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.3 10-05-2021 - Se configura un solo arreglo de parámetros para el método y se agrega el filtro de las vpn para la red GPON.
     *
     * @return $objResultado
     */
    public function getJsonVrfsClientePorVlan($arrayParametros)
    {
        $objResultado = $this->getVrfsClientePorVlan($arrayParametros);
        
        return json_encode($objResultado);
    }
    
    /**
     * Documentacion para el método 'getVrfsClientePorVlan'
     * 
     * Método que devuelve un array de las vrfs de un cliente por vlan
     * 
     * 
     * @author Kenneth Jimenez <kjimenez@telconet.ec>
     * @version 1.0 28-03-2016
     *
     * @author Duval Medina C. <dmedina@telconet.ec>
     * @version 1.1 2016-08-22 - Recibir y sar el codigo de empresoa en la función
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 26-08-2019 - Por proyecto migracion de vlan se agrega parametro 'strMigracionVlan' que permite identificar si se debe retornar
     *                           solo las vrf mapeadas Nedetel
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.3 10-05-2021 - Se configura un solo arreglo de parámetros para el método y se agrega el filtro de las vpn para la red GPON.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.4 11-05-2022 - Se agrega el id del servicio para la validación de vrf de cámaras.
     *
     * @return $objResultado
     */
    public function getVrfsClientePorVlan($arrayParametros)
    {
        $intIdPersonaEmpresaRol = $arrayParametros['idPersonaEmpresaRol'];
        $intIdVlan              = $arrayParametros['idVlan'];
        $strEmpresaCod          = $arrayParametros['strEmpresaCod'];
        $strMigracionVlan       = $arrayParametros['strMigracionVlan'];
        $strTipoRed             = isset($arrayParametros['strTipoRed']) ? $arrayParametros['strTipoRed'] : "MPLS";
        $intIdServicio          = isset($arrayParametros['intIdServicio']) ? $arrayParametros['intIdServicio'] : null;
        $arrayVrfs              = array();
        $arrayParametrosCliente = array();

        $objServProdCaractVlan = $this->_em->getRepository('schemaBundle:InfoServicioProdCaract')
                                            ->getVrfsClientePorVlanAndEmpresa($intIdVlan,$strEmpresaCod,"Activo");
        
        if($objServProdCaractVlan)
        {
            $objServicioVlan       = $this->_em->getRepository('schemaBundle:InfoServicio')->find($objServProdCaractVlan->getServicioId());
            $objCaract   = $this->_em->getRepository('schemaBundle:AdmiCaracteristica')
                                     ->findOneBy(array( "descripcionCaracteristica" => 'VRF',"estado" => "Activo"));

            $objProdCaractVrf = $this->_em->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                           ->findOneBy(array( "productoId"          => $objServicioVlan->getProductoId()->getId(),
                                                              "caracteristicaId"    => $objCaract->getId(),
                                                              "estado"              => "Activo"));
            
            $objServProdCaractVrf = $this->_em->getRepository('schemaBundle:InfoServicioProdCaract')
                                              ->findOneBy(array( "servicioId"                 => $objServicioVlan->getId(),
                                                                "productoCaracterisiticaId"   => $objProdCaractVrf->getId(),
                                                                "estado"                      => "Activo"));
            
            $objVrf = $this->_em->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')->find($objServProdCaractVrf->getValor());
            
            if($objVrf->getCaracteristicaId()->getDescripcionCaracteristica()=="VRF_IMPORTADA")
            {
                $objVrfImport = $this->_em->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')->find($objVrf->getValor());
                
                $arrayVrf["id_vrf"]  = $objVrfImport->getId();
                $arrayVrf["vrf"] = $objVrfImport->getValor();
            }
            else
            {
                $arrayVrf["id_vrf"]  = $objVrf->getId();
                $arrayVrf["vrf"] = $objVrf->getValor();
            }
            
            $arrayVrfs[] = $arrayVrf;
        }
        else
        {
            $arrayParametrosCliente["intPersonaEmpresaRol"] = $intIdPersonaEmpresaRol;
            $arrayParametrosCliente["strMigracionVlan"]     = $strMigracionVlan;
            $arrayParametrosCliente["strTipoRed"]           = $strTipoRed;
            //verificar si el tipo de red es GPON
            $booleanTipoRedGpon = false;
            if(!empty($strTipoRed))
            {
                $arrayParVerTipoRed = $this->_em->getRepository('schemaBundle:AdmiParametroDet')->getOne('NUEVA_RED_GPON_TN',
                                                                                                'COMERCIAL',
                                                                                                '',
                                                                                                'VERIFICAR TIPO RED',
                                                                                                'VERIFICAR_GPON',
                                                                                                $strTipoRed,
                                                                                                '',
                                                                                                '',
                                                                                                '');
                if(isset($arrayParVerTipoRed) && !empty($arrayParVerTipoRed))
                {
                    $booleanTipoRedGpon = true;
                }
            }
            //verificar servicio camara gpon
            $booleanCamaraGpon  = false;
            $arrayListaFormatos = array();
            if($booleanTipoRedGpon && !empty($intIdServicio))
            {
                $objServicio = $this->_em->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
                if(is_object($objServicio))
                {
                    $arrayProdCamaraGpon = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                                                                        ->getOne('CONFIG_PRODUCTO_DATOS_SAFE_CITY',
                                                                                'COMERCIAL',
                                                                                '',
                                                                                '',
                                                                                'PRODUCTO_ADICIONAL_CAMARA',
                                                                                $objServicio->getProductoId()->getId(),
                                                                                '',
                                                                                '',
                                                                                '',
                                                                                $strEmpresaCod);
                    if(isset($arrayProdCamaraGpon) && !empty($arrayProdCamaraGpon))
                    {
                        $booleanCamaraGpon    = true;
                        $arrayFormatoDetalles = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                                                                    ->get('NUEVA_RED_GPON_TN',
                                                                        'COMERCIAL',
                                                                        '',
                                                                        'FORMATO_VRF_SERVICIOS',
                                                                        '',
                                                                        '',
                                                                        '',
                                                                        '',
                                                                        '',
                                                                        $strEmpresaCod);
                        foreach($arrayFormatoDetalles as $arrayItemDet)
                        {
                            $arrayListaFormatos[] = $arrayItemDet['valor2'].'%';
                        }
                    }
                }
            }
            $arrayParametrosCliente["arrayListaFormatos"] = $arrayListaFormatos;
            $arrayVrfsCliente       = $this->getVpnsCliente($arrayParametrosCliente);

            if($strMigracionVlan === "N" && !$booleanCamaraGpon)
            {
                $arrayParametrosVpn = array(
                    "intIdPersonaEmpresaRol" => $intIdPersonaEmpresaRol,
                    "strMigracionVlan"       => $strMigracionVlan
                );
                $arrayVpnsImportCliente = $this->getVpnsImportCliente($arrayParametrosVpn);
                $arrayVrfs              = array_merge($arrayVrfsCliente['data'],$arrayVpnsImportCliente['data']);
            }
            else
            {
                $arrayVrfs = $arrayVrfsCliente;
            }
        }

        return $arrayVrfs;
    }
    
    /**
     * Documentacion para el método 'getValidaVrfsClientePorVlan'
     * 
     * Método que devuelve array de información con respecto a la asociación de la vlan con alguna vrf en el sistema
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 30-08-2016
     * @since 1.0 30-08-2016
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 30-09-2019 Se valida los objetos: $objServProdCaractVrf y $objVrf con la funciòn is_object
     * @since 1.0
     *
     * @author Richard Cabrera <rcabrera@telconet.ec> - Se usa la función is_object para validar los objetos: $objServProdCaractVrf, $objVrf para
     *                                                  corregir error en la asignación de recursos de red que no muestra las vlans reservadas.
     * @version 1.2 06-12-2019
     * @since 1.1 30-08-2016
     *
     * @param Integer $idVlan          Identificador de vlan a validar
     * @param String  $strEmpresaCod   Codigo de Empresa en sesion
     * 
     * @return $arrayRespuesta  array [asociada, id_vrf, vrf] asociada: con valores SI y NO para marcar registro asociado
     *                                                        id_vrf  : identificador de registro VRF
     *                                                        vrf     : valor de la VRF 
     */
    public function getValidaVrfsClientePorVlan($idVlan,$strEmpresaCod)
    {
        $arrayRespuesta        = array();
        $objServProdCaractVlan = $this->_em->getRepository('schemaBundle:InfoServicioProdCaract')
                                           ->getVrfsClientePorVlanAndEmpresa($idVlan,$strEmpresaCod,"Activo");
        //se valida que la vlan consultada se encuentre siendo usada por algun servicio en el telcos
        if($objServProdCaractVlan)
        {
            $objServicioVlan = $this->_em->getRepository('schemaBundle:InfoServicio')->find($objServProdCaractVlan->getServicioId());
            $objCaract       = $this->_em->getRepository('schemaBundle:AdmiCaracteristica')
                                    ->findOneBy(array( "descripcionCaracteristica" => 'VRF',"estado" => "Activo"));

            //se recupera producto caracteristica con la caracteristica VRF y con el producto del servicio encontrado
            $objProdCaractVrf = $this->_em->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                          ->findOneBy(array( "productoId"          => $objServicioVlan->getProductoId()->getId(),
                                                             "caracteristicaId"    => $objCaract->getId(),
                                                             "estado"              => "Activo"));
            //se recupera caracteristica VRF correspondiente al servicio encontrado
            $objServProdCaractVrf = $this->_em->getRepository('schemaBundle:InfoServicioProdCaract')
                                              ->findOneBy(array( "servicioId"                 => $objServicioVlan->getId(),
                                                                 "productoCaracterisiticaId"  => $objProdCaractVrf->getId(),
                                                                 "estado"                     => "Activo"));

            if(is_object($objServProdCaractVrf))
            {
                //se recupera registro del la tabla InfoPersonaEmpresaRolCarac correspondiente a la VRF sel servicio
                $objVrf = $this->_em->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')->find($objServProdCaractVrf->getValor());
            }

            //en caso de pertenecer a una vrf importada se procede a recuperar información correspondiente al registro
            if(is_object($objVrf))
            {
                if($objVrf->getCaracteristicaId()->getDescripcionCaracteristica()=="VRF_IMPORTADA")
                {
                    $objVrfImport = $this->_em->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')->find($objVrf->getValor());

                    $arrayRespuesta["id_vrf"]  = $objVrfImport->getId();
                    $arrayRespuesta["vrf"]     = $objVrfImport->getValor();
                }
                else
                {
                    $arrayRespuesta["id_vrf"] = $objVrf->getId();
                    $arrayRespuesta["vrf"]    = $objVrf->getValor();
                }
            }

            $arrayRespuesta['asociada'] = "SI";
        }
        else
        {    
            $arrayRespuesta['asociada'] = "NO";
            $arrayRespuesta["id_vrf"]   = "";
            $arrayRespuesta["vrf"]      = "";
        }
        
        return $arrayRespuesta;
    }

    /**
     * Documentación para el método 'getJsonVlansCliente'.
     *
     * Método utilizado para obtener las Vlans de un cliente
     *
     * @param string idPersonaEmpresaRol Rol del cliente en session
     * @param string nombre nombre del pe en donde buscar vlans
     * @param string vlan numero de la vlan a buscar
     * @param string start min de registros de vlans a buscar.
     * @param string limit max de registros de vlans a buscar.
     *
     * @return json $objResultado
     *
     * @author Kenneth Jimenez <kjimenez@telconet.ec>
     * @version 1.0 08-12-2015
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 03-10-2017 - Se envia parametrizado los valores para obtener las VLANS de los clientes
    */
    public function getJsonVlansCliente($idPersonaEmpresaRol,$nombre="",$vlan="",$start="",$limit="")
    {
        $arrayParametrosVlan                           = array();
        $arrayParametrosVlan['intIdPersonaEmpresaRol'] = $idPersonaEmpresaRol;
        $arrayParametrosVlan['intAnillo']              = '';
        $arrayParametrosVlan['strNombre']              = $nombre;
        $arrayParametrosVlan['intVlan']                = $vlan;
        $arrayParametrosVlan['strCaractVlan']          = 'VLAN';
        $arrayParametrosVlan['intStart']               = $start;
        $arrayParametrosVlan['intLimit']               = $limit;
                
        $objResultado = $this->getVlansCliente($arrayParametrosVlan);
        
        return json_encode($objResultado);
    }
    /**
     * Documentación para el método 'getVlansCliente'.
     *
     * Método utilizado para obtener las Vlans de un cliente
     *
     * @param string idPersonaEmpresaRol Rol del cliente en session
     * @param string nombre nombre del pe en donde buscar vlans
     * @param string vlan numero de la vlan a buscar
     * @param string start min de registros de vlans a buscar.
     * @param string limit max de registros de vlans a buscar.
     *
     * @return array arrayVlans
     *
     * @author Kenneth Jimenez <kjimenez@telconet.ec>
     * @version 1.0 08-12-2015
     *
     * @author Modificado: Duval Medina C. <dmedina@telconet.ec>
     * @version 1.1 2016-05-23 Ajuste en Query por Full Access a tabla INFO_DETALLE_ELEMENTO (TO_CHAR -> TO_NUMBER)
     *                         y reducción de costo de 3.412 a 128
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.2 2-06-2016
     * Se agrega la variable anillo, para validar que la vlan este en el rango correcto
     *
     * @author Modificado: Duval Medina C. <dmedina@telconet.ec>
     * @version 1.3 2016-06-24 Parametrizacion de variable para evitar error 'not all variables bound'
     * 
     * @author Modificado: Allan Suarez C. <arsuarez@telconet.ec>
     * @version 1.4 2017-09-27 Se agrega parametro adicional donde se envia la caracteristica VLAN de acuerdo al proceso, dado
     *                         que para flujos de DC existen vlans de tipo LAN y de tipo WAN
     *                         Se convierte parametros para que sean recibidos via ARRAY
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.5 2019-08-28 Se agrega validacion para que las vlans reservadas para nedetel no se retornen
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.6 20-11-2019 Se agrega lógica para obtener las vlans en caso de que el tipo de red sea GPON.
     */
    public function getVlansCliente($arrayParametros)
    {
        $arrayVlans  = array();
        $totalVlans  = 0;
        $strValorMin = "";
        $strValorMax = "";
        $strTipoRed    = isset($arrayParametros['strTipoRed']) ? $arrayParametros['strTipoRed']:'MPLS';
        $strEmpresaCod = $arrayParametros['strEmpresaCod'] ? $arrayParametros['strEmpresaCod']:'';
        try
        {
            if($arrayParametros['intIdPersonaEmpresaRol'] > 0)
            {
                //verificar si el tipo de red es GPON
                $booleanTipoRedGpon = false;
                $arrayParVerTipoRed = $this->_em->getRepository('schemaBundle:AdmiParametroDet')->getOne('NUEVA_RED_GPON_TN',
                                                                                                'COMERCIAL',
                                                                                                '',
                                                                                                'VERIFICAR TIPO RED',
                                                                                                'VERIFICAR_GPON',
                                                                                                $strTipoRed,
                                                                                                '',
                                                                                                '',
                                                                                                '');
                if(isset($arrayParVerTipoRed) && !empty($arrayParVerTipoRed))
                {
                    $booleanTipoRedGpon = true;
                }
                if($booleanTipoRedGpon)
                {
                    $arrayParametrosGpon = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->get('NUEVA_RED_GPON_TN',
                                                                'COMERCIAL',
                                                                '',
                                                                '',
                                                                '',
                                                                '',
                                                                '',
                                                                '',
                                                                'CATALOGO_VLANS_DATOS',
                                                                $strEmpresaCod);
                                            
                    if(!empty($arrayParametrosGpon) && is_array($arrayParametrosGpon))
                    {
                        $strValorMin = $arrayParametrosGpon[0]['valor1'];
                        $strValorMax = $arrayParametrosGpon[0]['valor2'];
                    }
                }
                if(!empty($arrayParametros['intAnillo']) && !$booleanTipoRedGpon)
                {
                    $arrayParametrosAnillo = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                                                ->getOne("ANILLOS_MPLS", 
                                                        "TECNICO", 
                                                        "", 
                                                        $arrayParametros['intAnillo'], 
                                                        "", 
                                                        "", 
                                                        "", 
                                                        ""
                                                    );
                    $strValorMin  = $arrayParametrosAnillo['valor1'];
                    $strValorMax  = $arrayParametrosAnillo['valor2'];
                }
                
                $rsm = new ResultSetMappingBuilder($this->_em);                
                $query = $this->_em->createNativeQuery(null, $rsm);

                $dql = "SELECT 
                            IPERC.ID_PERSONA_EMPRESA_ROL_CARACT ,
                            IDE.DETALLE_VALOR AS VLAN,
                            IE.ID_ELEMENTO,
                            IE.NOMBRE_ELEMENTO,
                            IPERC.FE_CREACION,
                            IPERC.USR_CREACION
                        FROM
                            ADMI_CARACTERISTICA AC,
                            INFO_PERSONA_EMPRESA_ROL_CARAC IPERC,
                            INFO_DETALLE_ELEMENTO IDE,
                            INFO_ELEMENTO IE
                        WHERE 
                            IPERC.ESTADO = ?
                            AND IE.ESTADO    = ?
                            AND IPERC.PERSONA_EMPRESA_ROL_ID = ?
                            AND IPERC.CARACTERISTICA_ID = AC.ID_CARACTERISTICA
                            AND AC.DESCRIPCION_CARACTERISTICA = ?
                            AND IDE.ID_DETALLE_ELEMENTO = COALESCE(TO_NUMBER(REGEXP_SUBSTR(IPERC.VALOR,'^\d+')),0)
                            AND IDE.ELEMENTO_ID = IE.ID_ELEMENTO ";

                $rsm->addScalarResult('ID_PERSONA_EMPRESA_ROL_CARACT',  'id_persona_empresa_rol_caract','integer');
                $rsm->addScalarResult('VLAN',                           'vlan',                         'integer');
                $rsm->addScalarResult('ID_ELEMENTO',                    'id_elemento',                  'integer');
                $rsm->addScalarResult('NOMBRE_ELEMENTO',                'elemento',                     'string');
                $rsm->addScalarResult('FE_CREACION',                    'fe_creacion',                  'datetime');
                $rsm->addScalarResult('USR_CREACION',                   'usr_creacion',                 'string');
                
                $query->setParameter(1, "Activo");
                $query->setParameter(2, "Activo");
                $query->setParameter(3, $arrayParametros['intIdPersonaEmpresaRol']);
                $query->setParameter(4, $arrayParametros['strCaractVlan']);//Se envia VLAN ( defecto ) , VLAN_LAN, VLAN_WAN
                
                if($arrayParametros['strNombre'] != "")
                {
                    $dql .= " AND IE.NOMBRE_ELEMENTO like ? ";
                    $query->setParameter(5, "%".$arrayParametros['strNombre']."%");
                }
                
                if($strValorMin != "" && $strValorMax != "")
                {
                    $dql .= " AND COALESCE(TO_NUMBER(REGEXP_SUBSTR(IDE.DETALLE_VALOR,'^\d+')),0) BETWEEN ? AND ? ";
                    $query->setParameter(6, $strValorMin);
                    $query->setParameter(7, $strValorMax);
                }

                if($arrayParametros['intVlan'] != "")
                {
                    $dql .= " AND IDE.DETALLE_VALOR = ? ";
                    $query->setParameter(8,trim($arrayParametros['intVlan']));
                }

                $dql .= " AND IDE.DETALLE_VALOR NOT IN ( SELECT DISTINCT(VALOR3) FROM ADMI_PARAMETRO_DET WHERE PARAMETRO_ID =
                            (SELECT ID_PARAMETRO FROM ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = ?) AND DESCRIPCION = ? AND ESTADO = ? ) ";

                $query->setParameter(9,"PARAMETROS PROYECTO SEGMENTACION VLAN");
                $query->setParameter(10,"MAPEO VRF - VLAN Nedetel");
                $query->setParameter(11,"Activo");

                if($booleanTipoRedGpon)
                {
                    $dql .= " AND IDE.DETALLE_NOMBRE = ?";
                }
                else
                {
                    $dql .= " AND IDE.DETALLE_NOMBRE != ?";
                }
                $query->setParameter(12,"VLAN GPON");

                $query->setSQL($dql);
                $arrVlans = $query->getResult();
                
                $totalVlans = count($arrVlans);
                
                if($arrayParametros['intStart']!='' && $arrayParametros['intLimit']!='') 
                {    
                    $query = $this->setQueryLimitWithBindVariables($query,$arrayParametros['intLimit'],$arrayParametros['intStart']);
                    $arrVlans = $query->getResult();
                }
                
                foreach($arrVlans as $arrVlan)
                {
                    $arrayVlan = array();
                    
                    $arrayVlan['id']            = $arrVlan['id_persona_empresa_rol_caract'];
                    $arrayVlan['vlan']          = $arrVlan['vlan'];
                    $arrayVlan['id_elemento']   = $arrVlan['id_elemento'];
                    $arrayVlan['elemento']      = $arrVlan['elemento'];
                    $arrayVlan['fe_creacion']   = strval(date_format($arrVlan['fe_creacion'],"d/m/Y G:i"));
                    $arrayVlan['usr_creacion']  = $arrVlan['usr_creacion'];
                    
                    $arrayVlans[] = $arrayVlan;
                }
            }    
            
            $objResultado = array(
                                'total' => $totalVlans ,
                                'data'  => $arrayVlans
                                );
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
            $objResultado = $arrayVlans;
        }
        
        return $objResultado;
        
    }

    /**
     *
     * Método utilizado para obtener las Vlans de un cliente para
     * el Servicio Clear Channel Punto a Punto
     *
     * @param string idPersonaEmpresaRol Rol del cliente en session
     * @param string nombre nombre del pe en donde buscar vlans
     * @param string vlan numero de la vlan a buscar
     * @param string start min de registros de vlans a buscar.
     * @param string limit max de registros de vlans a buscar.
     *
     * @return array arrayVlans
     *
     * @author Josue Valencia <ajvalencia@telconet.ec>
     * @version 1.0 08-12-2022
     * 
     **/
    
    public function getVlansClienteClearChannel($arrayParametros)
    {
        $arrayListaVlans  = array();
        $intTotalVlans  = 0;
        $strValorMin = "";
        $strValorMax = "";
        $strTipoRed    = isset($arrayParametros['strTipoRed']) ? $arrayParametros['strTipoRed']:'MPLS';
        $strEmpresaCod = $arrayParametros['strEmpresaCod'] ? $arrayParametros['strEmpresaCod']:'';
        try
        {
            if($arrayParametros['intIdPersonaEmpresaRol'] > 0)
            {
                
                $strSql = "SELECT 
                EL.NOMBRE_ELEMENTO,
                DE.ID_DETALLE_ELEMENTO,
                PE.RAZON_SOCIAL,
                DE.DETALLE_VALOR AS VLAN,
                DE.ESTADO
                FROM 
                        DB_INFRAESTRUCTURA.INFO_ELEMENTO EL 
                        LEFT JOIN DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO DE       ON DE.ELEMENTO_ID = EL.ID_ELEMENTO
                        LEFT JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC PERC  ON TO_CHAR(DE.ID_DETALLE_ELEMENTO) = PERC.VALOR
                        LEFT JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL PER         ON PERC.PERSONA_EMPRESA_ROL_ID =PER.ID_PERSONA_ROL
                        LEFT JOIN DB_COMERCIAL.INFO_EMPRESA_ROL ER                  ON PER.EMPRESA_ROL_ID = ER.ID_EMPRESA_ROL
                        LEFT JOIN DB_COMERCIAL.ADMI_ROL ROL                         ON ER.ROL_ID=ROL.ID_ROL
                        LEFT JOIN DB_COMERCIAL.INFO_PERSONA PE                      ON PE.ID_PERSONA = PER.PERSONA_ID
                WHERE 
                        PERC.ESTADO = :ESTADO
                        AND DE.DETALLE_NOMBRE = :DESCRIP_CARACT
                        AND PERC.PERSONA_EMPRESA_ROL_ID = :ID_PERSONA_EMPRESA_ROL
                        AND DE.ESTADO = :ESTADO_VLAN
                        AND DE.ELEMENTO_ID      =
                            (SELECT ID_ELEMENTO
                            FROM    DB_INFRAESTRUCTURA.INFO_ELEMENTO
                            WHERE   ID_ELEMENTO = :ID_NOMBRE_PE
                            AND     ESTADO              = :ESTADO
                            AND     LPAD(DE.DETALLE_VALOR, 4,'0') BETWEEN :INI_VLAN AND :FIN_VLAN
                            )
                AND     EL.ID_ELEMENTO=DE.ELEMENTO_ID 
                AND     ROL.ID_ROL= 1
                ORDER BY to_number(de.detalle_valor)";
                
                $objStmt = $this->_em->getConnection()->prepare($strSql);
                $objStmt->bindValue('ESTADO',"Activo");
                $objStmt->bindValue('ESTADO_VLAN',"Reservada");
                $objStmt->bindValue('ID_PERSONA_EMPRESA_ROL',$arrayParametros['intIdPersonaEmpresaRol']);
                $objStmt->bindValue('DESCRIP_CARACT',$arrayParametros['strCaractVlan']);//Se envia VLAN ( defecto ) , VLAN_LAN, VLAN_WAN
                $objStmt->bindValue('ID_NOMBRE_PE',$arrayParametros['intIdElemento']);
                $objStmt->bindValue('INI_VLAN',$arrayParametros['intStart']);
                $objStmt->bindValue('FIN_VLAN',$arrayParametros['intLimit']);

                
                $objStmt->execute();
                $arrayTotalVlans  = $objStmt->fetchAll();
                
                $intTotalVlans = count($arrayTotalVlans);
                
                foreach($arrayTotalVlans as $arrayVlanObtenida)
                {
                    $arrayVlan = array();
                    
                    $arrayVlan['nombre_elemento']      = $arrayVlanObtenida['NOMBRE_ELEMENTO'];
                    $arrayVlan['id_detalle_elemento']  = $arrayVlanObtenida['ID_DETALLE_ELEMENTO'];
                    $arrayVlan['razon']                = $arrayVlanObtenida['RAZON_SOCIAL'];
                    $arrayVlan['vlan']                 = $arrayVlanObtenida['VLAN'];
                    $arrayVlan['estado']               = $arrayVlanObtenida['ESTADO'];
                    
                    $arrayListaVlans[] = $arrayVlan;
                }
            }    
            
            $objResultado = array(
                                'total' => $intTotalVlans ,
                                'data'  => $arrayListaVlans
                                );
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
            $objResultado = $arrayListaVlans;
        }
        
        return $objResultado;
        
    }

    /**
     * Documentación para el método 'getServiciosByIdVlan'.
     *
     * Método utilizado para obtener los Servicios relacionados a la Vlans de un cliente
     *
     * @param integer $idPersonaEmpresaRolCarac Id de VLAN
     *
     * @return array arraySevicios
     *
     * @author Duval Medina C. <dmedina@telconet.ec>
     * @version 1.0 2016-05-22
    */
    public function getServiciosByIdVlan($idPersonaEmpresaRolCarac)
    {
        $arrayServicios = array();

        try
        {
            if($idPersonaEmpresaRolCarac > 0)
            {
                $rsm = new ResultSetMappingBuilder($this->_em);
                $rsm->addScalarResult('ID_SERVICIO_PROD_CARACT','id_servicio_prod_caract','integer');
                $rsm->addScalarResult('PRODUCTO_CARACTERISITICA_ID','id_producto_caracteristica','integer');
                $rsm->addScalarResult('ID_SERVICIO','id_servicio','integer');
                $rsm->addScalarResult('PRODUCTO_ID', 'id_producto','integer');
                
                $query = $this->_em->createNativeQuery(null, $rsm);

                $dql = "SELECT ISPC.ID_SERVICIO_PROD_CARACT,ISPC.PRODUCTO_CARACTERISITICA_ID,
                            S.ID_SERVICIO,S.PUNTO_ID,S.PRODUCTO_ID
                        FROM INFO_SERVICIO_PROD_CARACT ISPC,
                            INFO_SERVICIO S
                        WHERE ISPC.VALOR = TO_CHAR(:idPersonaEmpresaRolCarac)
                            AND ISPC.ESTADO = :estado
                            AND S.ID_SERVICIO = ISPC.SERVICIO_ID
                            AND S.ESTADO = :estado";

                $query->setParameter('idPersonaEmpresaRolCarac', $idPersonaEmpresaRolCarac);
                $query->setParameter('estado', "Activo");

                $query->setSQL($dql);      

                $arrayServicios = $query->getResult();
                
            }    
            
            $objResultado = array(
                                'status' => 'OK' ,
                                'data'   =>  $arrayServicios
                                );
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
            $objResultado = array(
                                'status' => 'ERROR' ,
                                'data'  => $e->getMessage()
                                );
        }
        
        return $objResultado;
        
    }
    
    /**
     * getCaracteristicaValor
     *
     * Método que retorna el valor de la característica del empleado, 
     * la cual puede ser Meta o Cargo                                 
     *      
     * @param array $arrayParametros ['criterios'    => 'Criterios de búsqueda de la función',
     *                                'moduloActivo' => 'Módulo con el cual se está realizando el llamado de la función',
     *                                'esJefe'       => 'Parámetro que indica si el usuario seleccionado tiene un cargo de jefe',
     *                                'tipo'         => 'Tipo de consulta a realizar']
     * 
     * @return array $arrayResultados['strValor'   => 'Valor de la caracteristica que se requiere buscar',
     *                                'intIdValor' => 'Id al que pertenece la característica consultada' ]
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 28-08-2015
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 13-03-2017 - Se valida con el caso 'CargoGrupoRolesPersonal' que el valor almacenado en la tabla 'INFO_PERSONA_EMPRESA_ROL_CARAC'
     *                           corresponda al 'ID_PARAMETRO_DET' de los grupos de roles del personal que se pueden asignar. Adicional se cambia
     *                           el valor de retorno de la función a un arreglo.
     */
    public function getCaracteristicaValor($arrayParametros)
    {
        $arrayResultados = array('strValor' => '', 'intIdValor' => 0);
        
        if( isset($arrayParametros['criterios']) )
        {
            $objCaracteristica = null;
            $objCaracteristica = $this->_em->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')->findOneBy($arrayParametros['criterios']);
            
            switch( $arrayParametros['tipo'] )
            {
                case 'Cargo':
                    
                    if($arrayParametros['moduloActivo'] == 'Comercial')
                    {
                        $arrayResultados['strValor'] = 'Vendedor';
                    }
                    elseif($arrayParametros['moduloActivo'] == 'Tecnico')
                    {
                        $arrayResultados['strValor'] = 'Operativo';
                    } 
                    else
                    {
                        $arrayResultados['strValor'] = 'Empleado';
                    }
                    
                    if( $objCaracteristica )
                    {
                        $arrayResultados['strValor'] = ucwords(strtolower($objCaracteristica->getValor()));
                    }
                    else
                    {
                        if( isset($arrayParametros['esJefe']) )
                        {
                            if( $arrayParametros['esJefe'] == 'S' )
                            {
                                $arrayResultados['strValor'] = 'Jefe';
                            }
                        }
                    }
                    
                    break;
                    
                case 'CargoGrupoRolesPersonal':
                    
                    $arrayResultados['strValor'] = 'Empleado';
                    
                    if( is_object($objCaracteristica) )
                    {
                        $intIdParametroDet = $objCaracteristica->getValor();
                        
                        if( !empty($intIdParametroDet) )
                        {
                            $objAdmiParametroDet = $this->_em->getRepository('schemaBundle:AdmiParametroDet')->findOneById($intIdParametroDet);
                            
                            if( is_object($objAdmiParametroDet) )
                            {
                                $arrayResultados['intIdValor'] = $intIdParametroDet;
                                $arrayResultados['strValor']   = ucwords(strtolower($objAdmiParametroDet->getDescripcion()));
                            }//( is_object($objAdmiParametroDet) )
                        }//( !empty($intIdParametroDet) )
                    }//( is_object($objCaracteristica) )
                    
                    break;

                    
                case 'Meta':
                    
                    $arrayResultados['strValor'] = '0';
                    
                    if( $objCaracteristica )
                    {
                        $arrayResultados['strValor'] = $objCaracteristica->getValor(); 
                    }

                    break;
                    
            }//switch( $arrayParametros['tipo'] )
        }//( isset($arrayParametros['criterios']) )
        
        return $arrayResultados;
    }

    /**
     * Costo: 60
     *
     * Documentacion para el método 'getVlansMigracionManual'
     *
     * Funcion que retorna las vlans reservadas para la herramienta de mirgación de vlan
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 28-08-2019
     *
     * @param array $arrayParametros [ 'intPersonaEmpresaRol' => id persona empresa rol del cliente en sesion
     *                                 'intAnillo'            => nombre de la Vpn a buscar
     *                                 'strNombreElemento'    => min de registros de vpns a buscar
     *                                 'intIdVrf'             => id de la vrf ]
     *
     * @return array $arrayResultado
     */
    public function getVlansMigracionManual($arrayParametros)
    {
        $arrayVlans     = array();
        $intTotalVlans  = 0;
        $strValorMin    = "";
        $strValorMax    = "";

        try
        {
            if($arrayParametros['intIdPersonaEmpresaRol'] > 0)
            {
                if(!empty($arrayParametros['intAnillo']))
                {
                    $arrayParametrosAnillo = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                                                       ->getOne("ANILLOS_MPLS",
                                                               "TECNICO",
                                                               "",
                                                               $arrayParametros['intAnillo'],
                                                               "",
                                                               "",
                                                               "",
                                                               "");

                    $strValorMin  = $arrayParametrosAnillo['valor1'];
                    $strValorMax  = $arrayParametrosAnillo['valor2'];
                }

                $objRsm   = new ResultSetMappingBuilder($this->_em);
                $objQuery = $this->_em->createNativeQuery(null, $objRsm);

                $strSql = "SELECT
                            IPERC.ID_PERSONA_EMPRESA_ROL_CARACT ,
                            IDE.DETALLE_VALOR AS VLAN,
                            IE.ID_ELEMENTO,
                            IE.NOMBRE_ELEMENTO,
                            IPERC.FE_CREACION,
                            IPERC.USR_CREACION
                        FROM
                            DB_COMERCIAL.ADMI_CARACTERISTICA AC,
                            DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC IPERC,
                            DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO IDE,
                            DB_INFRAESTRUCTURA.INFO_ELEMENTO IE
                        WHERE
                            IPERC.ESTADO = ?
                            AND IE.ESTADO    = ?
                            AND IPERC.PERSONA_EMPRESA_ROL_ID = ?
                            AND IPERC.CARACTERISTICA_ID = AC.ID_CARACTERISTICA
                            AND AC.DESCRIPCION_CARACTERISTICA = ?
                            AND IDE.ID_DETALLE_ELEMENTO = COALESCE(TO_NUMBER(REGEXP_SUBSTR(IPERC.VALOR,'^\d+')),0)
                            AND IDE.ELEMENTO_ID = IE.ID_ELEMENTO";

                $objRsm->addScalarResult('ID_PERSONA_EMPRESA_ROL_CARACT',  'id_persona_empresa_rol_caract','integer');
                $objRsm->addScalarResult('VLAN',                           'vlan',                         'integer');
                $objRsm->addScalarResult('ID_ELEMENTO',                    'id_elemento',                  'integer');
                $objRsm->addScalarResult('NOMBRE_ELEMENTO',                'elemento',                     'string');
                $objRsm->addScalarResult('FE_CREACION',                    'fe_creacion',                  'datetime');
                $objRsm->addScalarResult('USR_CREACION',                   'usr_creacion',                 'string');

                $objQuery->setParameter(1, "Activo");
                $objQuery->setParameter(2, "Activo");
                $objQuery->setParameter(3, $arrayParametros['intIdPersonaEmpresaRol']);
                $objQuery->setParameter(4, $arrayParametros['strCaractVlan']);

                if($arrayParametros['strNombre'] != "")
                {
                    $strSql .= " AND IE.NOMBRE_ELEMENTO like ? ";
                    $objQuery->setParameter(5, "%".$arrayParametros['strNombre']."%");
                }

                if($strValorMin != "" && $strValorMax != "")
                {
                    $strSql .= " AND COALESCE(TO_NUMBER(REGEXP_SUBSTR(IDE.DETALLE_VALOR,'^\d+')),0) BETWEEN ? AND ? ";
                    $objQuery->setParameter(6, $strValorMin);
                    $objQuery->setParameter(7, $strValorMax);
                }

                if($arrayParametros['intVlan'] != "")
                {
                    $strSql .= " AND IDE.DETALLE_VALOR = ? ";
                    $objQuery->setParameter(8,trim($arrayParametros['intVlan']));
                }

                if($arrayParametros['intIdVrf'] != "")
                {
                    $strSql .= " AND IDE.DETALLE_VALOR IN (SELECT VALOR3 FROM DB_GENERAL.ADMI_PARAMETRO_DET WHERE VALOR2 = (
                                SELECT VALOR FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC WHERE ID_PERSONA_EMPRESA_ROL_CARACT = ? )) ";

                    $objQuery->setParameter(9,trim($arrayParametros['intIdVrf']));
                }

                $objQuery->setSQL($strSql);

                $arrayResultadoVlans = $objQuery->getResult();

                $intTotalVlans = count($arrayResultadoVlans);

                if($arrayParametros['intStart']!='' && $arrayParametros['intLimit']!='')
                {
                    $objQuery = $this->setQueryLimitWithBindVariables($objQuery,$arrayParametros['intLimit'],$arrayParametros['intStart']);
                    $arrayResultadoVlans = $objQuery->getResult();
                }

                foreach($arrayResultadoVlans as $arrayIdxVlan)
                {
                    $arrayVlan = array();

                    $arrayVlan['id']            = $arrayIdxVlan['id_persona_empresa_rol_caract'];
                    $arrayVlan['vlan']          = $arrayIdxVlan['vlan'];
                    $arrayVlan['id_elemento']   = $arrayIdxVlan['id_elemento'];
                    $arrayVlan['elemento']      = $arrayIdxVlan['elemento'];
                    $arrayVlan['fe_creacion']   = strval(date_format($arrayIdxVlan['fe_creacion'],"d/m/Y G:i"));
                    $arrayVlan['usr_creacion']  = $arrayIdxVlan['usr_creacion'];

                    $arrayVlans[] = $arrayVlan;
                }
            }

            $arrayResultado = array(
                                'total' => $intTotalVlans ,
                                'data'  => $arrayVlans
                                );
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
            $arrayResultado = $arrayVlans;
        }

        return $arrayResultado;
    }


    /**
     * Documentación para el método 'getJsonVpnsCliente'.
     *
     * Método utilizado para obtener las Vpns de un cliente
     *
     * @param string idPersonaEmpresaRol Rol del cliente en session
     * @param string nombre nombre de la Vpn a buscar
     * @param string start min de registros de vpns a buscar.
     * @param string limit max de registros de vpns a buscar.
     * @param string flag bandera para obtener las vpns del cliente o las que no son del cliente
     *
     * @return json $objResultado
     *
     * @author Kenneth Jimenez <kjimenez@telconet.ec>
     * @version 1.0 08-12-2015
     *
     * @author Duval Medina C. <dmedina@telconet.ec>
     * @version 1.1 2016-05-23 Incluir el cliente en los criterios de busqueda
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 27-08-2019 Se agrega el parámetro 'strMigracionVlan' por ende se transorman a un array
     *
     * @param array $arrayParametros [ 'intPersonaEmpresaRol' => Rol del cliente en session
     *                                 'strNombre'            => nombre de la Vpn a buscar
     *                                 'intStart'             => min de registros de vpns a buscar
     *                                 'intLimit'             => max de registros de vpns a buscar
     *                                 'strFlag'              => bandera para obtener las vpns del cliente o las que no son del cliente
     *                                 'strCliente'           => nombre de la razon social
     *                                 'strMigracionVlan'     => bandera para identificar si se tiene que retornar las vrf mapeadas para Nedetel  ]
     */
    public function getJsonVpnsCliente($arrayParametros)
    {
        $objResultado = $this->getVpnsCliente($arrayParametros);
        
        return json_encode($objResultado);
    }
        
    /**
     * Documentación para el método 'getVpnsCliente'.
     *
     * Método utilizado para obtener las Vpns de un cliente
     *
     * @param string idPersonaEmpresaRol Rol del cliente en session
     * @param string nombre nombre de la Vpn a buscar
     * @param string start min de registros de vpns a buscar.
     * @param string limit max de registros de vpns a buscar.
     * @param string flag bandera para obtener las vpns del cliente o las que no son del cliente
     *
     * @return array arrayVpns
     *
     * @author Kenneth Jimenez <kjimenez@telconet.ec>
     * @version 1.0 08-12-2015
     *
     * @author Duval Medina C. <dmedina@telconet.ec>
     * @version 1.1 2016-05-23 Incluir el Cliente en la búsqueda, puede ser: razon Social, nombre o apellido
     *              Incluir Native Query para contabilizar los resultados para la paginacion
     *
     * @author Duval Medina C. <dmedina@telconet.ec>
     * @version 1.2 2016-06-24 Presentar la información del cliente aún cuando busque por nombre VPN
     *                         Se agrega 'nombre_' en la caracteristica principal porque hay sub-caracteristicas
     *                              con el mismo nombre
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.3 2019-08-14 Se obtiene la vlan mapeada que esta asociada a la vrf y se agrega el parámetro 'strMigracionVlan' por ende se
     *                         transorman a un array
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.4 2021-05-10 Se agrega el filtro de las vpn para la red GPON
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.5 2021-07-20 Se agrega validaciones para los tipos de red
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.6 2022-05-11 Se elimina validaciones para los tipos de red
     *
     * @param array $arrayParametros [ 'intPersonaEmpresaRol' => Rol del cliente en session
     *                                 'strNombre'            => nombre de la Vpn a buscar
     *                                 'intStart'             => min de registros de vpns a buscar
     *                                 'intLimit'             => max de registros de vpns a buscar
     *                                 'strFlag'              => bandera para obtener las vpns del cliente o las que no son del cliente
     *                                 'strCliente'           => nombre de la razon social
     *                                 'strMigracionVlan'     => bandera para identificar si se tiene que retornar las vrf mapeadas para Nedetel  ]
     */
    public function getVpnsCliente($arrayParametrosVpn)
    {
        $intIdPersonaEmpresaRol = $arrayParametrosVpn["intPersonaEmpresaRol"]?$arrayParametrosVpn["intPersonaEmpresaRol"]:"";
        $strNombre              = $arrayParametrosVpn["strNombre"]?$arrayParametrosVpn["strNombre"]:"";
        $intStart               = $arrayParametrosVpn["intStart"]?$arrayParametrosVpn["intStart"]:"";
        $intLimit               = $arrayParametrosVpn["intLimit"]?$arrayParametrosVpn["intLimit"]:"";
        $strFlag                = $arrayParametrosVpn["strFlag"]?$arrayParametrosVpn["strFlag"]:"cliente";
        $strCliente             = $arrayParametrosVpn["strCliente"]?$arrayParametrosVpn["strCliente"]:"";
        $strMigracionVlan    = $arrayParametrosVpn["strMigracionVlan"]?$arrayParametrosVpn["strMigracionVlan"]:"N";
        $arrayListaFormatos     = isset($arrayParametrosVpn["arrayListaFormatos"])?$arrayParametrosVpn["arrayListaFormatos"]:array();

        $strBandCliente = "N";

        //Se verifica si el cliente esta configurado para obtener vlans por vrf
        $arrayValoresParametros = $this->_em->getRepository('schemaBundle:AdmiParametroDet')->getOne('PARAMETROS PROYECTO SEGMENTACION VLAN',
                                                                                                     'INFRAESTRUCTURA',
                                                                                                     'ASIGNAR RECURSOS DE RED',
                                                                                                     'CLIENTE_PERMITIDO',
                                                                                                     $intIdPersonaEmpresaRol,
                                                                                                     '',
                                                                                                     '',
                                                                                                     '',
                                                                                                     '',
                                                                                                     '');

        if(isset($arrayValoresParametros["valor1"]) && !empty($arrayValoresParametros["valor1"]))
        {
            $strBandCliente = "S";
        }

        $arrayVpns = array();

        $rsm            = new ResultSetMappingBuilder($this->_em);
        $rsmCount       = new ResultSetMappingBuilder($this->_em);
        
        $query          = $this->_em->createNativeQuery(null, $rsm);
        $ntvQueryCount  = $this->_em->createNativeQuery(null, $rsmCount);
        
        $strSelectCount     = " SELECT COUNT(*) AS TOTAL ";
        
        $strSelect = "SELECT iperc.ID_PERSONA_EMPRESA_ROL_CARACT ,
                             ac.DESCRIPCION_CARACTERISTICA,
                             iperc.VALOR,
                             iperc.FE_CREACION,
                             iperc.USR_CREACION";
        $strFrom = " FROM
                        ADMI_CARACTERISTICA ac,
                        INFO_PERSONA_EMPRESA_ROL_CARAC iperc";
        $strWhere = " WHERE 
                            iperc.ESTADO = :estado 
                        AND iperc.CARACTERISTICA_ID = ac.ID_CARACTERISTICA
                        AND ac.DESCRIPCION_CARACTERISTICA = :CARACT_VPN ";

        $query->setParameter('CARACT_VPN', "VPN");
        $query->setParameter('estado', "Activo");
        $ntvQueryCount->setParameter('CARACT_VPN', "VPN");
        $ntvQueryCount->setParameter('estado', "Activo");
        
        $rsm->addScalarResult('ID_PERSONA_EMPRESA_ROL_CARACT', 'id','integer');
        $rsm->addScalarResult('DESCRIPCION_CARACTERISTICA', 'caracteristica','string');
        $rsm->addScalarResult('VALOR', 'valor','string');
        $rsm->addScalarResult('FE_CREACION', 'feCreacion');
        $rsm->addScalarResult('USR_CREACION', 'usrCreacion','string');
        $rsmCount->addScalarResult('TOTAL', 'total', 'integer');

        
        if($strMigracionVlan === "S")
        {
            $strWhere .= " AND iperc.ID_PERSONA_EMPRESA_ROL_CARACT IN (SELECT VALOR1 FROM ADMI_PARAMETRO_DET WHERE PARAMETRO_ID =
                                (SELECT ID_PARAMETRO FROM ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = :nombreParametro )
                                AND DESCRIPCION = :descripcionVpn AND ESTADO = :estado ) ";

            $query->setParameter('nombreParametro', "PARAMETROS PROYECTO SEGMENTACION VLAN");
            $query->setParameter('descripcionVpn',  "VPN");
            $query->setParameter('estado', "Activo");
            $ntvQueryCount->setParameter('nombreParametro', "PARAMETROS PROYECTO SEGMENTACION VLAN");
            $ntvQueryCount->setParameter('descripcionVpn',  "VPN");
            $ntvQueryCount->setParameter('estado', "Activo");
        }

        if($intIdPersonaEmpresaRol > 0)
        {
            if($strFlag=="cliente")
            {
                $strWhere .= " AND iperc.PERSONA_EMPRESA_ROL_ID = :personaEmpresaRolId";
            }
            if($strFlag=="import")
            {
                $strWhere .= " AND iperc.PERSONA_EMPRESA_ROL_ID != :personaEmpresaRolId";
            }
            $query->setParameter('personaEmpresaRolId', $intIdPersonaEmpresaRol);
            $ntvQueryCount->setParameter('personaEmpresaRolId', $intIdPersonaEmpresaRol);
        }

        if($strNombre != '' || $strCliente !='')
        {
            $strSelect .= ",IP.RAZON_SOCIAL,
                            IP.NOMBRES,
                            IP.APELLIDOS";
            $strFrom .= ",INFO_PERSONA IP,
                          INFO_PERSONA_EMPRESA_ROL IPER";
            $strWhere .= " AND IPER.ID_PERSONA_ROL = iperc.PERSONA_EMPRESA_ROL_ID
                           AND IP.ID_PERSONA       = IPER.PERSONA_ID 
                           AND IPER.ESTADO         = :estado";
            if($strNombre != '')
            {
                $strWhere .= " AND LOWER(iperc.VALOR) like :nombre ";
                $query->setParameter('nombre', '%' . strtolower($strNombre) . '%');
                $ntvQueryCount->setParameter('nombre', '%' . strtolower($strNombre) . '%');
            }

            if($strCliente !='')
            {
                $strWhere .= " AND ( (LOWER(IP.RAZON_SOCIAL) like :cliente)
                                  OR ((LOWER(IP.NOMBRES)    like :cliente
                                   OR LOWER(IP.APELLIDOS)   like :cliente))
                                     )";
                 $query->setParameter("cliente", '%'.strtolower($strCliente).'%');
                 $ntvQueryCount->setParameter("cliente", '%'.strtolower($strCliente).'%');
            }
            $rsm->addScalarResult('RAZON_SOCIAL', 'razonSocial','string');
            $rsm->addScalarResult('NOMBRES', 'nombres','string');
            $rsm->addScalarResult('APELLIDOS', 'apellidos','string');
        }

        if(!empty($arrayListaFormatos))
        {
            $strWhere .= " AND ( ";
            $intCountFormat = 0;
            foreach($arrayListaFormatos as $strFormato)
            {
                $strWhere = $intCountFormat > 0 ? $strWhere." OR " : $strWhere;
                $strWhere .= " iperc.VALOR LIKE :strFormato_$intCountFormat ";
                $query->setParameter("strFormato_$intCountFormat", $strFormato);
                $ntvQueryCount->setParameter("strFormato_$intCountFormat", $strFormato);
                $intCountFormat++;
            }
            $strWhere .= " ) ";
        }

        $dql = $strSelect . $strFrom . $strWhere;
        if($intStart!='' && $intLimit!='')
        {
            $query = $this->setQueryLimit($query->setSQL($dql),$intLimit,$intStart);
        }
        else
        {
            $query->setSQL($dql);
        }

        $strSqlCount   = $strSelectCount . $strFrom . $strWhere;
        $ntvQueryCount->setSQL($strSqlCount);
        $intTotal = $ntvQueryCount->getSingleScalarResult();

                     
        $vpns = $query->getResult();
        
        foreach($vpns as $vpn)
        {
            $arrayVpn = array();
            $query = $this->_em->createQuery(null);

            $dql = "SELECT 
                        iperc.id ,
                        ac.descripcionCaracteristica as caracteristica,
                        iperc.valor
                    FROM
                        schemaBundle:AdmiCaracteristica ac,
                        schemaBundle:InfoPersonaEmpresaRolCarac iperc
                    WHERE 
                        iperc.estado = :estado 
                    AND iperc.caracteristicaId = ac.id
                    AND iperc.personaEmpresaRolCaracId = :personaEmpresaRolCaracId ";

            $query->setParameter('personaEmpresaRolCaracId', $vpn['id']);
            $query->setParameter('estado', "Activo");
            
            $query->setDQL($dql);              
            $caracsVpn = $query->getResult();
            
            $arrayVpn['id']                   = $vpn['id'];
            $arrayVpn['nombre_'.strtolower($vpn['caracteristica'])] = $vpn['valor'];
            $arrayVpn['fe_creacion']          = strval(date_format(date_create($vpn['feCreacion']),"d/m/Y G:i"));
            $arrayVpn['usr_creacion']         = $vpn['usrCreacion'];
            if($strCliente=="" && $strNombre=="")
            {
                $arrayVpn['cliente']     = $strCliente;
            }
            else
            {
                $arrayVpn['cliente']     = $vpn['razonSocial']?
                                                $vpn['razonSocial']:$vpn['nombres']." ".$vpn['apellidos'];
            }

            foreach($caracsVpn as $caracVpn)
            {
                $arrayVpn['id_'.strtolower($caracVpn['caracteristica'])] = $caracVpn['id'];
                $arrayVpn[strtolower($caracVpn['caracteristica'])] = $caracVpn['valor'];
            }

            $arrayParametros["strVrf"] = $arrayVpn["vrf"];
            $strVlanActual             = "";

            if(!empty($arrayParametros["strVrf"]))
            {
                $strVlanActual = $this->getVlanPorVrf($arrayParametros);
            }

            if(empty($strVlanActual))
            {
                $arrayVpn['strVlan']       = "No existe VLAN mapeada";
                $arrayVpn['strExisteVlan'] = "N";
            }
            else
            {
                $arrayVpn['strVlan']       = $strVlanActual["vlan"];
                $arrayVpn['strExisteVlan'] = "S";
            }

            $arrayVpn['strBandCliente'] = $strBandCliente;

           $arrayVpns[] = $arrayVpn;
        }

        $objResultado = array(
                              'total' => $intTotal ,
                              'data'  => $arrayVpns
                             );

        return $objResultado;
    }
    


     /**
     * Costo: 1
     * getVlanPorVrf
     *
     * Función que retorna la vlan asociada a la vrf enviada
     *
     * @param array arrayParametros [ strVrf => nombre de la vrf ]
     *
     * @return string $strVLan retorna la vlan asociada a la vrf
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 14-08-2019
     */
    public function getVlanPorVrf($arrayParametros)
    {
        $strVLan             = "";
        $strEstado           = "Activo";
        $strNombreParametro  = "PARAMETROS PROYECTO SEGMENTACION VLAN";
        $strDescripcion      = "MAPEO VRF - VLAN Nedetel";
        $objRsmb             = new ResultSetMappingBuilder($this->_em);
        $objQuery            = $this->_em->createNativeQuery(null,$objRsmb);

        $strSql = " SELECT
                        APD.VALOR3 AS VLAN
                    FROM
                        DB_GENERAL.ADMI_PARAMETRO_DET APD
                    WHERE
                        APD.PARAMETRO_ID = (
                            SELECT
                                APC.ID_PARAMETRO
                            FROM
                                DB_GENERAL.ADMI_PARAMETRO_CAB APC
                            WHERE
                                APC.NOMBRE_PARAMETRO = :paramNombre
                        )
                        AND APD.DESCRIPCION = :paramDescripcion
                        AND APD.VALOR2 = :paramVrf
                        AND APD.ESTADO = :paramEstado";

        $objQuery->setParameter('paramNombre',$strNombreParametro);
        $objQuery->setParameter('paramDescripcion',$strDescripcion);
        $objQuery->setParameter('paramVrf',$arrayParametros["strVrf"]);
        $objQuery->setParameter('paramEstado',$strEstado);

        $objRsmb->addScalarResult('VLAN', 'vlan', 'string');

        $objQuery->setSQL($strSql);

        $strVLan = $objQuery->getResult();

        return $strVLan[0];
    }

    /**
     * 
     * getReutilizableRecursoRed
     *
     * Función que retorna la vlan asociada a la vrf enviada
     *
     * @param array arrayParametros 
     *
     * @return string retorna cnatidad de servicios asociados a un olt
     *                que utilizan los recursos de red vlan o vrf
     *
     * @author Manuel Carpio <mcarpio@telconet.ec>
     * @version 1.0 07-11-2022
     * 
     * @author Leonardo Mero <lemero@telconet.ec>
     * @version 1.1  06-01-2023 - Se agrega un valor por defecto cuando el parametro valor se ingresa como null
     * 
     */
    public function getReutilizableRecursoRed($arrayParametros)
    {
        $strIdPersonaEmpresaRol = $arrayParametros['idPerosnaEmpresaRol'];
        $strEstado              = "Activo";
        $strValor               = $arrayParametros['valor'];
        $arrayNorServicios      = $arrayParametros['arrayServicios'];
        $strElementoId          = $arrayParametros['elementoId'];
        $objRsmb                = new ResultSetMappingBuilder($this->_em);
        $objQuery               = $this->_em->createNativeQuery(null,$objRsmb);

        $strSql = " SELECT COUNT(ISE.ID_SERVICIO) AS SERVICIOS
                      FROM DB_COMERCIAL.INFO_SERVICIO ISE 
                    LEFT JOIN DB_COMERCIAL.INFO_SERVICIO_TECNICO IST ON IST.SERVICIO_ID = ISE.ID_SERVICIO
                    WHERE ISE.PUNTO_ID IN (SELECT IPU.ID_PUNTO FROM DB_COMERCIAL.INFO_PUNTO IPU 
                    WHERE IPU.PERSONA_EMPRESA_ROL_ID IN (SELECT IPER.ID_PERSONA_ROL FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPER
                    WHERE IPER.PERSONA_ID IN (SELECT IPE.ID_PERSONA FROM DB_COMERCIAL.INFO_PERSONA IPE 
                        WHERE IPER.id_persona_rol IN(:paramIdPersonaRol))) AND IPU.ESTADO IN (:paramEstado))
                    AND ISE.ID_SERVICIO IN(SELECT SPC.SERVICIO_ID FROM DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT SPC 
                                                    where SPC.VALOR = :paramValor AND SPC.SERVICIO_ID NOT IN(:paramNotServicios))
                    AND IST.ELEMENTO_ID = :paramElementoId
                    AND ISE.ESTADO IN (:paramEstado) ";

        $objQuery->setParameter('paramIdPersonaRol',$strIdPersonaEmpresaRol);
        $objQuery->setParameter('paramValor', isset($strValor) ? $strValor : '');
        $objQuery->setParameter('paramNotServicios',$arrayNorServicios);
        $objQuery->setParameter('paramElementoId',$strElementoId);
        $objQuery->setParameter('paramEstado', $strEstado);

        $objRsmb->addScalarResult('SERVICIOS', 'servicios', 'integer');

        $objQuery->setSQL($strSql);

        $intTotal = $objQuery->getResult();

        return $intTotal;
    }


    /**
     * Documentación para el método 'getJsonVpnsImportCliente'.
     *
     * Método utilizado para obtener las Vpns Importadas de un cliente
     *
     * @param string idPersonaEmpresaRol Rol del cliente en session
     * @param string start min de registros de vpns a buscar.
     * @param string limit max de registros de vpns a buscar.
     *
     * @return json $objResultado
     *
     * @author Kenneth Jimenez <kjimenez@telconet.ec>
     * @version 1.0 08-12-2015
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 2019-08-14 Se obtiene la vlan mapeada que esta asociada a la vrf y se agrega parámetro '$strMigracionVlan'
     */
    public function getJsonVpnsImportCliente($intIdPersonaEmpresaRol,$strMigracionVlan,$intStart="",$intLimit="")
    {
        $arrayParametrosVpn = array(
            "intIdPersonaEmpresaRol" => $intIdPersonaEmpresaRol,
            "strMigracionVlan"       => $strMigracionVlan,
            "intStart"               => $intStart,
            "intLimit"               => $intLimit
        );
        $objResultado = $this->getVpnsImportCliente($arrayParametrosVpn);
        
        return json_encode($objResultado);
    }
    
    /**
     * Documentación para el método 'getVpnsImportCliente'.
     *
     * Método utilizado para obtener las Vpns Importadas de un cliente
     *
     * @param string idPersonaEmpresaRol Rol del cliente en session
     * @param string start min de registros de vpns a buscar.
     * @param string limit max de registros de vpns a buscar.
     *
     * @return array arrayVpns
     *
     * @author Kenneth Jimenez <kjimenez@telconet.ec>
     * @version 1.0 08-12-2015
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 27-08-2019 Se agrega el parámetro 'strMigracionVlan' para determinar si se tiene que retornar solo las vrf mapeadas
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.2 20-07-2021 Se agrega validaciones para los tipos de red
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.3 11-05-2022 Se elimina validaciones para los tipos de red
     */
    public function getVpnsImportCliente($arrayParametrosVpn)
    {
        $intIdPersonaEmpresaRol = $arrayParametrosVpn['intIdPersonaEmpresaRol'];
        $strMigracionVlan       = $arrayParametrosVpn['strMigracionVlan'];
        $intStart               = isset($arrayParametrosVpn['intStart']) ? $arrayParametrosVpn['intStart'] : "";
        $intLimit               = isset($arrayParametrosVpn['intLimit']) ? $arrayParametrosVpn['intLimit'] : "";
        $strBandCliente = "N";

        //Se verifica si el cliente esta configurado para obtener vlans por vrf
        $arrayValoresParametros = $this->_em->getRepository('schemaBundle:AdmiParametroDet')->getOne('PARAMETROS PROYECTO SEGMENTACION VLAN',
                                                                                                     'INFRAESTRUCTURA',
                                                                                                     'ASIGNAR RECURSOS DE RED',
                                                                                                     'CLIENTE_PERMITIDO',
                                                                                                     $intIdPersonaEmpresaRol,
                                                                                                     '',
                                                                                                     '',
                                                                                                     '',
                                                                                                     '',
                                                                                                     '');

        if(isset($arrayValoresParametros["valor1"]) && !empty($arrayValoresParametros["valor1"]))
        {
            $strBandCliente = "S";
        }

        $arrayVpns = array();

        $query = $this->_em->createQuery(null);

        $dql = "SELECT 
                    iperc.id ,
                    ac.descripcionCaracteristica as caracteristica,
                    iperc.valor,
                    iperc.feCreacion,
                    iperc.usrCreacion
                FROM
                    schemaBundle:AdmiCaracteristica ac,
                    schemaBundle:InfoPersonaEmpresaRolCarac iperc
                WHERE iperc.caracteristicaId = ac.id 
                AND iperc.id in (
                            SELECT 
                                ipercvpn.valor 
                            FROM
                                schemaBundle:AdmiCaracteristica acvpn,
                                schemaBundle:InfoPersonaEmpresaRolCarac ipercvpn
                            WHERE 
                                ipercvpn.estado = :estado 
                            AND ipercvpn.caracteristicaId = acvpn.id
                            AND ipercvpn.personaEmpresaRolId = :personaEmpresaRolId
                            AND acvpn.descripcionCaracteristica = :vpn_import
                ) ";

        $query->setParameter('personaEmpresaRolId', $intIdPersonaEmpresaRol);
        $query->setParameter('vpn_import', "VPN_IMPORTADA");
        $query->setParameter('estado', "Activo");    
        
        if($intStart!='' && $intLimit!='') 
        {
            $query->setFirstResult($intStart)->setMaxResults($intLimit);        
        }

        $query->setDQL($dql);              
        $vpns = $query->getResult();
        
        foreach($vpns as $vpn)
        {
            $arrayVpn = array();
            $query = $this->_em->createQuery(null);

            $dql = "SELECT 
                        iperc.id ,
                        ac.descripcionCaracteristica as caracteristica,
                        iperc.valor,
                        iper.id as personaEmpresaRolId
                    FROM
                        schemaBundle:AdmiCaracteristica ac,
                        schemaBundle:InfoPersonaEmpresaRolCarac iperc,
                        schemaBundle:InfoPersonaEmpresaRol iper
                    WHERE 
                        iperc.estado = :estado 
                    AND iperc.caracteristicaId = ac.id
                    AND iperc.personaEmpresaRolId = iper.id
                    AND iperc.personaEmpresaRolCaracId = :personaEmpresaRolCaracId";

            $query->setParameter('personaEmpresaRolCaracId', $vpn['id']);
            $query->setParameter('estado', "Activo");

            $query->setDQL($dql);              
            $caracsVpn = $query->getResult();
            
            $arrayVpn['id']                   = $vpn['id'];
            $arrayVpn['fe_creacion']          = strval(date_format($vpn['feCreacion'],"d/m/Y G:i"));
            $arrayVpn['usr_creacion']         = $vpn['usrCreacion'];
            $arrayVpn[strtolower($vpn['caracteristica'])] = $vpn['valor'];

            foreach($caracsVpn as $caracVpn)
            {
                $arrayVpn['id_'.strtolower($caracVpn['caracteristica'])] = $caracVpn['id'];
                $arrayVpn[strtolower($caracVpn['caracteristica'])] = $caracVpn['valor'];
                $arrayVpn['personaEmpresaRolId'] = $caracVpn['personaEmpresaRolId'];
            }

            $arrayParametros["strVrf"] = $arrayVpn["vrf"];
            $strVlanActual             = "";

            if(!empty($arrayParametros["strVrf"]))
            {
                $strVlanActual = $this->getVlanPorVrf($arrayParametros);
            }

            if(empty($strVlanActual))
            {
                $arrayVpn['strVlan']       = "No existe VLAN mapeada";
                $arrayVpn['strExisteVlan'] = "N";
            }
            else
            {
                $arrayVpn['strVlan']       = $strVlanActual["vlan"];
                $arrayVpn['strExisteVlan'] = "S";
            }

            $arrayVpn['strBandCliente'] = $strBandCliente;

            $arrayVpns[] = $arrayVpn;
        }

        $objResultado = array(
                              'total' => count($arrayVpns) ,
                              'data'  => $arrayVpns
                             );

        return $objResultado;
    }

    
    /**
     * Documentación para el método 'getOneByCaracteristica'.
     *
     * @param string idPersonaEmpresaRol Rol del cliente
     * @param string caracteristica nombre de la caracteristica a buscar
     *
     * @return array arrayresultado
     *
     * @author Kenneth Jimenez <kjimenez@telconet.ec>
     * @version 1.0 10-01-2016
    */
    public function getOneByCaracteristica($idPersonaEmpresaRol,$caracteristica)
    {
        $arrayresultado = null;
        
        if($idPersonaEmpresaRol > 0)
        {
            $objCaracteristica = $this->_em->getRepository('schemaBundle:AdmiCaracteristica')
                                            ->findOneBy(array("descripcionCaracteristica"=>$caracteristica,"estado"=>"Activo"));
                                            
            if($objCaracteristica)
            {
                $arrayresultado = $this->_em->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                        ->findOneBy(array(
                                                                        "personaEmpresaRolId" => $idPersonaEmpresaRol,
                                                                        "caracteristicaId"    => $objCaracteristica->getId(),
                                                                        "estado"              => "Activo"
                                                                        )
                                                                    );
            }

        }    
        
        return $arrayresultado;
    }

    /**
     * Metodo que obtiene un arreglo con información de caracteristica dependendiendo del parametro enviado
     * 
     * @param array $arrayParametros
     * [
     *     caracteristicaId    => id de la caracteristica,
     *     empresaCod          => codigo de empresa,
     *     personaEmpresaRolId => persona empresa rol id,
     *     valor               => valor
     *     estado              => estado persona empresa rol caracteristica caracteristica
     *     intStart            => registro inicio de la consulta
     *     intLimit            => cantidad de registros para la consulta
     *     
     * ]
     * @return array InfoPersonaEmpresaRolCarac
     * 
     * @author Juan Carlos Lafuente <jlafuente@telconet.ec>
     * @version 1.0 03-12-2015
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 12-04-2017 Se agrega el parámetro notPersonaEmpresaRolId a la consulta
     */
    public function getCaracteristicasByParametros($arrayParametros) 
    {
        $arrayDatos = array();
        try
        {
            $objQuery = $this->_em->createQuery();

            $strSql = "SELECT
                        pRC               
                    FROM
                        schemaBundle:InfoPersonaEmpresaRolCarac         pRC
                        LEFT JOIN schemaBundle:InfoPersonaEmpresaRol    pR 
                            WITH pRC.personaEmpresaRolId = pR.id
                        LEFT JOIN schemaBundle:InfoEmpresaRol           eR 
                            WITH pR.empresaRolId = eR.id
                    WHERE
                        pRC.caracteristicaId = :caracteristicaId ";

            $objQuery->setParameter('caracteristicaId', $arrayParametros['caracteristicaId']);

            $strWhere = '';

            if(isset($arrayParametros['empresaCod']))
            {
                $strWhere .= " AND eR.empresaCod = :empresaCod";
                $objQuery->setParameter('empresaCod', $arrayParametros['empresaCod']);
            }

            if(isset($arrayParametros['personaEmpresaRolId']))
            {
                $strWhere .= " AND pRC.personaEmpresaRolId = :personaEmpresaRolId";
                $objQuery->setParameter('personaEmpresaRolId', $arrayParametros['personaEmpresaRolId']);
            }
            
            if(isset($arrayParametros['notPersonaEmpresaRolId']))
            {
                $strWhere .= " AND pRC.personaEmpresaRolId <> :notPersonaEmpresaRolId";
                $objQuery->setParameter('notPersonaEmpresaRolId', $arrayParametros['notPersonaEmpresaRolId']);
            }
            
            if(isset($arrayParametros['valor']))
            {
                $strWhere .= " AND pRC.valor = :valor";
                $objQuery->setParameter('valor', $arrayParametros['valor']);
            }

            if(isset($arrayParametros['estado']))
            {
                $strWhere .= " AND pRC.estado = :estado";
                $objQuery->setParameter('estado', $arrayParametros['estado']);
            }

            $strOrderBy = ' ORDER BY pRC.id DESC';

            $strSql .= $strWhere;
            $strSql .= $strOrderBy;
            $objQuery->setDQL($strSql);
            
            if(isset($arrayParametros['intStart']))
            {
                $objQuery->setFirstResult($arrayParametros['intStart']);   
            }
            if(isset($arrayParametros['intLimit']))
            {
                $objQuery->setMaxResults($arrayParametros['intLimit']);            
            }
            $arrayDatos = $objQuery->getResult();
            
        } 
        catch(\Exception $ex)
        {
            error_log($ex->getMessage());
        }
        return $arrayDatos;
    }

    
    
    /**
     * Documentación para el método 'findCaracteristicaPorCriterios'.
     * Obtiene información de una caracteristica según criterios enviados por parametros
     * @param array  $arrayParametros    
     * [
     *      caracteristicaId    => id de la caracteristica,
     *      empresaCod          => codigo de empresa
     *      personaEmpresaRolId => persona empresa rol id del cliente
     *      valor               => valor de la persona empresa rol caracteristica
     *      estado              => estado de la persona empresa rol caracteristica
     *      intStart            => valor inicio de los registros
     *      intLimit            => valor limite de la consulta
     * ]
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 18-01-2017
     * @return Object $objCaracteristica InfoPersonaEmpresaRolCarac
     */
    public function findCaracteristicaPorCriterios($arrayParametros)
    {
        $objCaracteristica   = null;
        $arrayCaracteristica = $this->getCaracteristicasByParametros($arrayParametros);
        if (isset($arrayCaracteristica) && !empty($arrayCaracteristica))
        {
            $objCaracteristica = $arrayCaracteristica[0];
        }
        return $objCaracteristica;
    }
     /**
     * getCaractCicloFacturacion
     *
     * Metodo para obtener la caracteristica CICLO_FACTURACION asignada a un cliente
     *
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 02-10-2017
     * costoQuery: 6     
     * @param  array $arrayParametros [                                       
     *                                  "intIdPersonaRol"     : Id del Cliente o Pre-cliente
     *                                ]          
     * @return $objDatos
     */   
    public function getCaractCicloFacturacion($arrayParametros)
    {
        $objRsm      = new ResultSetMappingBuilder($this->_em);
        $objNtvQuery = $this->_em->createNativeQuery(null, $objRsm);

        $strSql= " SELECT CI.NOMBRE_CICLO,
                   IPERC.ID_PERSONA_EMPRESA_ROL_CARACT,
                   IPERC.PERSONA_EMPRESA_ROL_ID,
                   IPERC.CARACTERISTICA_ID,
                   IPERC.VALOR,
                   IPERC.ESTADO
                   FROM 
                   DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPER,
                   DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC IPERC, 
                   DB_COMERCIAL.ADMI_CARACTERISTICA CA,
                   DB_COMERCIAL.ADMI_CICLO CI
                   WHERE
                   IPER.ID_PERSONA_ROL                                          = :intIdPersonaRol
                   AND IPERC.PERSONA_EMPRESA_ROL_ID                             = IPER.ID_PERSONA_ROL
                   AND IPERC.CARACTERISTICA_ID                                  = CA.ID_CARACTERISTICA
                   AND CA.DESCRIPCION_CARACTERISTICA                            =:strCaracteristica 
                   AND COALESCE(TO_NUMBER(REGEXP_SUBSTR(IPERC.VALOR,'^\d+')),0) = CI.ID_CICLO
                   AND IPERC.ESTADO                                             = :strEstado 
                   AND ROWNUM                                                   = 1 ";
                
        $objRsm->addScalarResult('NOMBRE_CICLO', 'strNombreCiclo', 'string');
        $objRsm->addScalarResult('ID_PERSONA_EMPRESA_ROL_CARACT', 'intIdPersonaEmpresaRolCaract', 'integer');
        $objRsm->addScalarResult('PERSONA_EMPRESA_ROL_ID', 'intPersonaEmpresaRolId', 'integer');
        $objRsm->addScalarResult('CARACTERISTICA_ID', 'intCaracteristicaId', 'integer');
        $objRsm->addScalarResult('VALOR', 'strValor', 'string');
        $objRsm->addScalarResult('ESTADO', 'strEstado', 'string');
        
        $objNtvQuery->setParameter("intIdPersonaRol", $arrayParametros['intIdPersonaRol']);        
        $objNtvQuery->setParameter("strCaracteristica", 'CICLO_FACTURACION');
        $objNtvQuery->setParameter("strEstado", 'Activo');

        $objNtvQuery->setSQL($strSql);
        $objDatos = $objNtvQuery->getOneOrNullResult();
        return $objDatos;
    }

    /**
     * Método que obtiene listado de las razones sociales que tiene monitoreo zabbix
     *
     * costoQuery: 417
     *
     * @author Walther Joao Gaibor<wgaibor@telconet.ec>
     * @version 1.0 20-12-2018
     *
     * @param array $arrayParametros
     * @return array
     */
    public function getPersonasEmpresaRS($arrayParametros)
    {
        $objQuery = $this->_em->createQuery();
        $strQuery = " SELECT per
                      FROM schemaBundle:InfoPersonaEmpresaRolCarac      per
                      WHERE per.personaEmpresaRolId not in (SELECT DISTINCT sipe.id
                                                            FROM schemaBundle:InfoPersonaEmpresaRolCarac  sper,
                                                                 schemaBundle:InfoPersonaEmpresaRol sipe
                                                            WHERE sipe.id               = sper.personaEmpresaRolId
                                                            AND sper.caracteristicaId   = :intCaracteristica
                                                            AND sper.estado             = :strEstadoVip)
                        AND per.estado                      = :strEstado
                        AND per.caracteristicaId            = :intCaracteristicaZabbix ";
        $objQuery->setParameter('intCaracteristica',       $arrayParametros['intCaracteristica']);
        $objQuery->setParameter('strEstadoVip',            $arrayParametros['strEstadoVip']);
        $objQuery->setParameter('strEstado',               $arrayParametros['strEstado']);
        $objQuery->setParameter('intCaracteristicaZabbix', $arrayParametros['intCaracteristicaZabbix']);
        $objQuery->setDQL($strQuery);

        return $objQuery->getResult();
    }

    /**
     * Método que obtiene listado de las razones sociales que tiene monitoreo zabbix de los asesores comerciales
     *
     * costoQuery: 227
     *
     * @author Walther Joao Gaibor<adominguez@telconet.ec>
     * @version 1.0 26-12-2018
     *
     * @param array $arrayParametros
     * @return array
     */
    public function getPersonasEmpresaAsesorComeRS($arrayParametros)
    {
        $objRsm    = new ResultSetMappingBuilder($this->_em);
        $objQuery  = $this->_em->createNativeQuery(null, $objRsm);
        $strQuery  = " SELECT IPER.ID_PERSONA_ROL AS ID_PERSONA_ROL
                        FROM  DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC IPRC,
                              DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPER,
                              DB_COMERCIAL.INFO_PUNTO IPTO
                        WHERE IPTO.PERSONA_EMPRESA_ROL_ID = IPRC.PERSONA_EMPRESA_ROL_ID
                        AND IPER.ID_PERSONA_ROL           = IPRC.PERSONA_EMPRESA_ROL_ID
                        AND IPRC.ESTADO                   = :strEstadoPunto
                        AND IPTO.ESTADO                   = :strEstado
                        AND IPRC.CARACTERISTICA_ID        = :intCaracteristica
                        AND IPTO.USR_VENDEDOR             = :strUsrVendedor
                        GROUP BY IPER.ID_PERSONA_ROL";
        $objRsm->addScalarResult('ID_PERSONA_ROL', 'intIdPersonaEmpresaRol', 'integer');

        $objQuery->setParameter('intCaracteristica',       $arrayParametros['intCaracteristica']);
        $objQuery->setParameter('strEstadoPunto',          $arrayParametros['strEstadoVip']);
        $objQuery->setParameter('strEstado',               $arrayParametros['strEstado']);
        $objQuery->setParameter('strUsrVendedor',          $arrayParametros['strUsrVendedor']);
        $objQuery->setSQL($strQuery);
        return $objQuery->getResult();
    }
    
    /**
     * 
     * Metodo que verifica atraves del id_cliente_contacto, si se encuentra registrado en la tabla 
     * INFO_PERSONA_EMPRESA_ROL_CARAC
     * 
     * costoQuery: 14
     * 
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.0 10-04-2019
     * 
     * @param string $strIdPersona
     * @return array
     */
    public function findPersonaEmpresaRolCarac($strIdPersona)
    {
        $arrayResultado = array();
        try
        {
            $objQuery = $this->_em->createQuery("SELECT iperc "
                . " FROM schemaBundle:InfoPersonaEmpresaRol iper,"
                . " schemaBundle:InfoPersonaEmpresaRolCarac iperc,"
                . " schemaBundle:AdmiCaracteristica ac"
                . " WHERE iper.personaId=:IdPersona"
                . " AND iperc.caracteristicaId=ac.id"
                . " AND ac.descripcionCaracteristica in (:Caracteristica1,:Caracteristica2)"
                . " AND iper.id=iperc.personaEmpresaRolId");
            $objQuery->setParameter("IdPersona", $strIdPersona);
            $objQuery->setParameter("Caracteristica1", 'HORARIO ESCALABILIDAD');
            $objQuery->setParameter("Caracteristica2", 'NIVEL ESCALABILIDAD');
            $arrayDatos = $objQuery->getResult();
            $arrayResultado['status']        = 'ok';
            $arrayResultado['datos'] = $arrayDatos;
        }
        catch(\Exception $ex)
        {
            $arrayResultado["status"]      = 'fail';
            $arrayResultado["descripcion"] = 'Existion un error en findPersonaEmpresaRolCarac -'.$ex->getMessage();
        }
        return $arrayResultado;
    }

    /**
     * 
     * Metodo que verifica atraves del Persona_empresa_Rol_id, si se encuentra registrado en la tabla 
     * INFO_PERSONA_EMPRESA_ROL_CARAC
     * 
     * costoQuery: 16
     * 
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.0 10-04-2019
     * 
     * @param int $intPersonaEmpresaRolId
     * @return array
     */
    public function findPersonaEmpresaRolCaracByPersona($intPersonaEmpresaRolId)
    {
            $objQuery = $this->_em->createQuery("SELECT iperc "
                . " FROM schemaBundle:InfoPersonaContacto ipc, "
                . " schemaBundle:InfoPersonaEmpresaRol iper, "
                . " schemaBundle:InfoPersonaEmpresaRolCarac iperc"
                . " WHERE ipc.contactoId=iper.personaId "
                . " AND iper.id=iperc.personaEmpresaRolId"
                . " AND iperc.estado=:Estado"
                . " AND ipc.personaEmpresaRolId=:RolEmpresaId");
            $objQuery->setParameter("RolEmpresaId", $intPersonaEmpresaRolId);
            $objQuery->setParameter("Estado", 'Activo');
            $arrayDatos = $objQuery->getResult();
        
        return $arrayDatos;
    }

    public function findInfoPersonaEmpresaRolCaracByPersonaDescripcion($intPersonaEmpresaRolId, $intCaracteristicaId)
    {
            $objQuery = $this->_em->createQuery("SELECT iperc "
                . " FROM schemaBundle:InfoPersonaEmpresaRolCarac iperc"
                . " WHERE iperc.personaEmpresaRolId=:RolEmpresaId "
                . " AND iperc.estado=:Estado"
                . " AND iperc.caracteristicaId=:CaracteristicaId");
            $objQuery->setParameter("RolEmpresaId", $intPersonaEmpresaRolId);
            $objQuery->setParameter("CaracteristicaId", $intCaracteristicaId);
            $objQuery->setParameter("Estado", 'Activo');
            $arrayDatos = $objQuery->getResult();
        
        return $arrayDatos;
    }

    public function findInfoPersonaEmpresaRolCaracByUsrCreacionDescripcion($strUsrCreacion, $intCaracteristicaId)
    {
            $objQuery = $this->_em->createQuery("SELECT iperc "
                . " FROM schemaBundle:InfoPersonaEmpresaRolCarac iperc"
                . " WHERE iperc.usrCreacion=:usrCreacion "
                . " AND iperc.estado=:Estado"
                . " AND iperc.caracteristicaId=:CaracteristicaId");
            $objQuery->setParameter("usrCreacion", $strUsrCreacion);
            $objQuery->setParameter("CaracteristicaId", $intCaracteristicaId);
            $objQuery->setParameter("Estado", 'Activo');
            $arrayDatos = $objQuery->getResult();
        
        return $arrayDatos;
    }

    public function findInfoPersonaEmpresaRolCaracByListOfPersonAndCabecera($arrayUsers, $intCaracteristicaId) 
    {
        $objQuery = $this->_em->createQuery("SELECT iperc "
                . " FROM schemaBundle:InfoPersonaEmpresaRolCarac iperc"
                . " WHERE iperc.caracteristicaId=:CaracteristicaId"
                . " AND iperc.personaEmpresaRolId in (:usuarios)"
                . " AND iperc.estado=:Estado");
            $objQuery->setParameter("CaracteristicaId", $intCaracteristicaId);
            $objQuery->setParameter("Estado", 'Activo');
            $objQuery->setParameter("usuarios", $arrayUsers);
            $arrayDatos = $objQuery->getResult();
        
        return $arrayDatos;
    }

    public function deleteInfoPersonaEmpresaRolCaracById($intIdRegistro)
    {
           

            $objRsm  = new ResultSetMappingBuilder($this->_em);          
            $strSql  = " UPDATE DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC "
                     . " SET ESTADO = :Estado "
                     . " WHERE ID_PERSONA_EMPRESA_ROL_CARACT = :idRegistro ";           
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);

            $objQuery->setParameter("idRegistro", $intIdRegistro);
            $objQuery->setParameter("Estado", 'Eliminado'); 
            $objQuery->setSQL($strSql);
            
            $arrayResult = $objQuery->getResult();
        
        return $arrayResult;
    }

    /**
     * 
     * Metodo que verifica atraves del punto_id, si se encuentra registrado en la tabla 
     * INFO_PERSONA_EMPRESA_ROL_CARAC
     * 
     * costoQuery: 16
     *
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.0 10-04-2019
     * 
     * @param int $intPuntoId
     * @return array
     */
    public function findPersonaEmpresaRolCaracByPunto($intPuntoId)
    {
            $objQuery = $this->_em->createQuery("SELECT iperc "
                . " FROM schemaBundle:InfoPuntoContacto ipc, "
                . " schemaBundle:InfoPersonaEmpresaRolCarac iperc"
                . " WHERE ipc.puntoId=:PuntoId"
                . " AND iperc.estado=:Estado"
                . " AND ipc.personaEmpresaRolId=iperc.personaEmpresaRolId");
            $objQuery->setParameter("PuntoId", $intPuntoId);
            $objQuery->setParameter("Estado", 'Activo');
            $arrayDatos = $objQuery->getResult();
        
        return $arrayDatos;
    }
    /**
     * 
     * Metodo que devuelve la entidad INFO_PERSONA_EMPRESA_ROL_CARAC atraves del PersonaEmpresaId para su eliminado logico 
     * 
     * costoQuery: 7
     * 
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.0 10-04-2019
     * 
     * @param int $intPersonaEmpresaId
     * @return array
     */
    public function findPersonaEmpresaRolCaracByPerEmp($intPersonaEmpresaId)
    {
        $arrayResultado = array();
        try
        {
            $objQuery = $this->_em->createQuery("SELECT iperc "
                . " FROM  schemaBundle:InfoPersonaEmpresaRolCarac iperc,"
                . " schemaBundle:AdmiCaracteristica ac"
                . " WHERE iperc.personaEmpresaRolId=:PersonaEmpresaId"
                . " AND iperc.caracteristicaId=ac.id"
                . " AND ac.descripcionCaracteristica in (:Caracteristica1,:Caracteristica2)"
                . " AND iperc.estado=:Estado");
            $objQuery->setParameter("PersonaEmpresaId", $intPersonaEmpresaId);
            $objQuery->setParameter("Estado", 'Activo');
            $objQuery->setParameter("Caracteristica1", 'HORARIO ESCALABILIDAD');
            $objQuery->setParameter("Caracteristica2", 'NIVEL ESCALABILIDAD');
            $arrayDatos = $objQuery->getResult();
            $arrayResultado['status']        = 'ok';
            $arrayResultado['datos'] = $arrayDatos;
        }
        catch(\Exception $ex)
        {
            $arrayResultado["status"]      = 'fail';
            $arrayResultado["descripcion"] = 'Existion un error en findPersonaEmpresaRolCaracByPerEmp -'.$ex->getMessage();
        }
        return $arrayResultado;
    }

    /**
     *
     * Método encargado de obtener al lider de cuadrilla al cual pertenece una persona.
     *
     * Costo 10
     *
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.0 04-11-2020
     *
     * @param Array $arrayParametros [personaEmpresaRolId]
     * @return Array
     */
    public function getLiderCuadrillaByPersonaEmpresaRol($arrayParametros)
    {
        try
        {
            $arrayResultado     = array();
            $objResultSetMap    = new ResultSetMappingBuilder($this->_em);
            $objNativeQuery     = $this->_em->createNativeQuery(null, $objResultSetMap);

            $strSql = "SELECT "
                       . "IPERC.PERSONA_EMPRESA_ROL_ID "
                     . "FROM "
                        . "DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC IPERC "
                    . "WHERE PERSONA_EMPRESA_ROL_ID IN (SELECT IPER2.ID_PERSONA_ROL 
                    FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPER2 "
                    . "WHERE IPER2.CUADRILLA_ID = (SELECT IPER3.CUADRILLA_ID " 
                    . "FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPER3 " 
                    . "WHERE IPER3.ID_PERSONA_ROL = :personaEmpresaRolId AND IPER3.ESTADO = :estadoActivo)
                      AND IPER2.ESTADO = :estadoActivo) "
                      . "AND IPERC.ESTADO = :estadoActivo "
                      . "AND IPERC.VALOR = :cargoLider ";

            $objNativeQuery->setParameter("estadoActivo", 'Activo');
            $objNativeQuery->setParameter("cargoLider", 'Lider');
            $objNativeQuery->setParameter("personaEmpresaRolId", $arrayParametros['personaEmpresaRolId']);

            $objResultSetMap->addScalarResult('PERSONA_EMPRESA_ROL_ID', 'personaEmpresaRolId',   'integer');

            $objNativeQuery->setSQL($strSql);

            $arrayResultado['status'] = 'OK';
            $arrayResultado['result'] = $objNativeQuery->getArrayResult();
        }
        catch (\Exception $objException)
        {
            $arrayResultado["status"]      = 'ERROR';
            $arrayResultado["descripcion"] = $objException->getMessage();
        }

        return $arrayResultado;
    }
    
     /**
     * 
     * Metodo que obtienene la caraceteristica cifrado de un cliente por identificacion
     * 
     * costoQuery: 10
     *
     * @author Eduardo Montenegro <emontenegro@telconet.ec>
     * @version 1.0 Version Inicial
     * 
     * @param array $arrayParametros
     * @return array
     */
    public function getCaracteristicaClienteIdentificacion($arrayParametros)
    {
        $arrayResultado         = array();
        $objResultSetMap    = new ResultSetMappingBuilder($this->_em);
        $objNativeQuery     = $this->_em->createNativeQuery(null, $objResultSetMap);
        $strSql             = "";
        if(isset($arrayParametros['tipoIdentificacion']))
        {
            $strSql = "SELECT iperc.* 
            FROM DB_COMERCIAL.info_persona ipe,
                DB_COMERCIAL.info_persona_empresa_rol iper,
                DB_COMERCIAL.info_persona_empresa_rol_carac iperc,
                DB_COMERCIAL.admi_caracteristica aca
            WHERE ipe.identificacion_cliente = :Lv_IdentificacionCliente
                AND ipe.tipo_identificacion = :Lv_TipoIdentificacion
                AND iper.persona_id = ipe.id_persona
                AND iperc.persona_empresa_rol_id = iper.id_persona_rol
                AND iperc.valor = 'Y'
                AND iperc.estado = 'Activo'
                AND iperc.caracteristica_id = aca.id_caracteristica
                AND aca.descripcion_caracteristica = 'CLIENTE CIFRADO' ";

            $objNativeQuery->setParameter("Lv_IdentificacionCliente", $arrayParametros['identificacion']);
            $objNativeQuery->setParameter("Lv_TipoIdentificacion", $arrayParametros['tipoIdentificacion']);    
        }else
        {
            $strSql = "SELECT iperc.* 
            FROM DB_COMERCIAL.info_persona ipe,
                DB_COMERCIAL.info_persona_empresa_rol iper,
                DB_COMERCIAL.info_persona_empresa_rol_carac iperc,
                DB_COMERCIAL.admi_caracteristica aca
            WHERE ipe.identificacion_cliente = :Lv_IdentificacionCliente
                AND iper.persona_id = ipe.id_persona
                AND iperc.persona_empresa_rol_id = iper.id_persona_rol
                AND iperc.valor = 'Y'
                AND iperc.estado = 'Activo'
                AND iperc.caracteristica_id = aca.id_caracteristica
                AND aca.descripcion_caracteristica = 'CLIENTE CIFRADO' ";
            $objNativeQuery->setParameter("Lv_IdentificacionCliente", $arrayParametros['identificacion']);    
        }
        $objResultSetMap->addScalarResult('ID_PERSONA_EMPRESA_ROL_CARACT', 'id',   'integer');
        $objNativeQuery->setSQL($strSql);
        $arrayResultado['status'] = 'OK';
        $arrayResultado['result'] = $objNativeQuery->getArrayResult();
        return $arrayResultado;
    }
}

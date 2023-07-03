<?php

namespace telconet\schemaBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class InfoSubredRepository extends EntityRepository 
{

    public function encontrarPoolsxOlt($elementoId) {
//        return $this->getEntityManager()->createQuery('select ir from schemaBundle:InfoSubred ir, schemaBundle:InfoSubred ir where ir.elementoId =:elementoId')
//                ->setParameter('elementoId', $elementoId)->getResult();
        
        $em = $this->get('doctrine')->getManager('telconet_infraestructura');	
        //$entities = $em->getRepository('schemaBundle:InfoSubred')->encontrarPoolsxOlt(34515);
        
        return $this->getEntityManager()->findBy(array('elementoId' => $elementoId));
    }
    
    /**
     * Funcion que sirve para obtener el nombre del scope a la que
     * pertenece una ip, de un olt.
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 13-04-2015
     * @version 1.1 16-09-2015 John Vera <javera@telconet.ec>
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.2 19-02-2021 Sacamos el tipo de ip (Fija o Privada) por medio de la $ipFija
     * 
     * @param $ipFija String
     * @param $idElemento int
     */
    public function getScopePorIpFija($ipFija, $idElemento)
    {
        $strTipoIp = 'Fija';
        
        $objIp = $this->_em->getRepository('schemaBundle:InfoIp')->findOneBy(array("ip" => $ipFija, "estado" => "Activo", "tipoIp" => "PRIVADA"));
        if(is_object($objIp))
        {
            $strTipoIp = ucfirst(strtolower($objIp->getTipoIp()));
        }
        
        list($ip1, $ip2, $ip3, $ip4) = split('\\.',$ipFija);
        $ipOctetos = $ip1.".".$ip2.".".$ip3;
        $sql = "SELECT NOMBRE_SCOPE,
                    TIPO_SCOPE,
                    IP_SCOPE_INI,
                    IP_SCOPE_FIN,
                    ESTADO_RED,
                    NOMBRE_POLICY
                  FROM
                    (SELECT ID_SUBRED,
                      (SELECT EEE.DETALLE_VALOR
                      FROM INFO_DETALLE_ELEMENTO EEE
                      WHERE REF_DETALLE_ELEMENTO_ID=REFERENCIA_DETALLE_ELEMENTO_ID
                      AND DETALLE_NOMBRE           = :tipoDetalleScopeParam
                      ) AS NOMBRE_SCOPE,
                      (SELECT RRR.DETALLE_VALOR
                      FROM INFO_DETALLE_ELEMENTO RRR
                      WHERE REF_DETALLE_ELEMENTO_ID=REFERENCIA_DETALLE_ELEMENTO_ID
                      AND DETALLE_NOMBRE           = :tipoDetalleTipoSParam
                      ) AS TIPO_SCOPE,
                      IP_SCOPE_INI,
                      IP_SCOPE_FIN,
                      ESTADO_RED,
                      (SELECT NOMBRE_POLICY FROM ADMI_POLICY WHERE ID_POLICY=POLICESCOPE
                      ) AS NOMBRE_POLICY
                    FROM
                      (SELECT ISD.ID_SUBRED AS ID_SUBRED,
                        ISD.ESTADO          AS ESTADO_RED,
                        ISD.IP_INICIAL      AS IP_SCOPE_INI,
                        ISD.IP_FINAL        AS IP_SCOPE_FIN,
                        ISD.NOTIFICACION    AS POLICESCOPE,
                        (SELECT WWW.ID_DETALLE_ELEMENTO
                        FROM INFO_DETALLE_ELEMENTO WWW
                        WHERE WWW.ELEMENTO_ID=IDE.ELEMENTO_ID
                        AND WWW.DETALLE_VALOR= TO_CHAR(ISD.ID_SUBRED)
                        ) AS REFERENCIA_DETALLE_ELEMENTO_ID
                      FROM INFO_SUBRED ISD,
                        INFO_DETALLE_ELEMENTO IDE
                      WHERE IDE.ELEMENTO_ID   = :idElementoParam
                      AND IDE.DETALLE_NOMBRE IN (:tipoDetalleTipoSubRParam)
                      AND IDE.DETALLE_VALOR   = ISD.ID_SUBRED
                      )
                    )
                  WHERE TIPO_SCOPE  = :tipoIpsScopeParam
                  AND ESTADO_RED    = :estadoScopeParam
                  AND NOMBRE_SCOPE IS NOT NULL
                  AND ID_SUBRED     =
                    (SELECT SUBRED.ID_SUBRED
                    FROM INFO_SUBRED SUBRED,
                      INFO_DETALLE_ELEMENTO DETALLE_ELEMENTO,
                      INFO_ELEMENTO ELEMENTO
                    WHERE (SUBRED.IP_INICIAL LIKE :ipOctetoParam||'%'
                    OR SUBRED.IP_FINAL LIKE :ipOctetoParam||'%')
                    AND DETALLE_ELEMENTO.DETALLE_NOMBRE                              = :tipoDetalleTipoSubRParam
                    AND DETALLE_ELEMENTO.DETALLE_VALOR                               = SUBRED.ID_SUBRED
                    AND SUBRED.ESTADO                                                = :estadoSubRed
                    AND DETALLE_ELEMENTO.ELEMENTO_ID                                 = ELEMENTO.ID_ELEMENTO
                    AND ELEMENTO.ID_ELEMENTO                                         = :idElementoParam
                    AND TO_NUMBER(REGEXP_SUBSTR (SUBRED.IP_INICIAL, '[0-9]+', 1, 3))>=TO_NUMBER(REGEXP_SUBSTR (:ipFijaParam, '[0-9]+', 1, 3))
                    AND TO_NUMBER(REGEXP_SUBSTR (SUBRED.IP_FINAL, '[0-9]+', 1, 3))  <=TO_NUMBER(REGEXP_SUBSTR (:ipFijaParam, '[0-9]+', 1, 3))
                    AND TO_NUMBER(REGEXP_SUBSTR (SUBRED.IP_INICIAL, '[0-9]+', 1, 4))<=TO_NUMBER(REGEXP_SUBSTR (:ipFijaParam, '[0-9]+', 1, 4))
                    AND TO_NUMBER(REGEXP_SUBSTR (SUBRED.IP_FINAL, '[0-9]+', 1, 4))  >=TO_NUMBER(REGEXP_SUBSTR (:ipFijaParam, '[0-9]+', 1, 4))
                    )";
        $stmt = $this->_em->getConnection()->prepare($sql);
        $stmt->bindValue('idElementoParam',             $idElemento);
        $stmt->bindValue('tipoDetalleScopeParam',       'SCOPE');
        $stmt->bindValue('tipoDetalleTipoSParam',       'TIPO SCOPE');
        $stmt->bindValue('tipoDetalleTipoSubRParam',    'SUBRED');
        $stmt->bindValue('tipoIpsScopeParam',           $strTipoIp);
        $stmt->bindValue('estadoScopeParam',            'Activo');
        $stmt->bindValue('estadoSubRed',                'Activo');
        $stmt->bindValue('ipFijaParam',                 $ipFija);
        $stmt->bindValue('ipOctetoParam',               $ipOctetos);        
        $stmt->execute();
        $arrayResult = $stmt->fetchAll();
        return $arrayResult[0];
    }
    
    /**
     * Documentación para el método 'getJsonSubredesDisponiblesCliente'.
     *
     * Método utilizado para obtener las subredes de un cliente
     *
      * @param Array  $arrayParametros [
     *                                  intIdPersonaEmpresaRol       Rol del cliente a generar asignacion de recursos de red
     *                                  strNombreElemento            Nombre del elemento Pe
     *                                  strNombreTecnico             Nombre tecnico del producto ligado al servicio
     *                                  strTipoEnlace                Tipo enlace del servicio
     *                                  intAnillo                    Numero de anillo
     *                                  strEsPseudoPe                Determina si el servicio depende de pseudoPe administrado por Cliente
     *                                ]
     *
     * @return json $objResultado
     *
     * @author Kenneth Jimenez <kjimenez@telconet.ec>
     * @version 1.0 08-12-2015
    */
    public function getJsonSubredesDisponiblesCliente($arrayParametros)
    {
        $objResultado = $this->getSubredesDisponiblesCliente($arrayParametros);
        
        return json_encode($objResultado);
    }
    
    /**
     * Documentación para el método 'getJsonSubredesDisponiblesClienteFWA'.
     * Costo: 12
     * Método utilizado para obtener las subredes de un cliente de un producto FWA.
     *
      * @param Array  $arrayParametros [
     *                                  strEstado       Estado de la subred
     *                                  strUsoSubred    Referencia de uso de la SubRed
     *                                  strAnillo       Anillo al que pertenece subRed
     *                                  strIdElemento   Elemento Id al que hace referencia la subRed
     *                                  strTipo         Tipo de red: WAN, LAN, etc.
     *                                ]
     *
     * @return json $objResultado
     *
     * @author Walther Joao Gaibor C. <wgaibor@telconet.ec>
     * @version 1.0 05-09-2019
    */
    public function getJsonSubredesDisponiblesClienteFWA($arrayParametros)
    {
        $objRsm    = new ResultSetMappingBuilder($this->_em);
        $objQuery  = $this->_em->createNativeQuery(null, $objRsm);

        try
        {
            $strSelect = " SELECT infoSubred.ID_SUBRED,
                                    infoSubred.SUBRED";
            $strFrom = " FROM INFO_SUBRED infoSubred";
            $strWhere = " WHERE infoSubred.ESTADO  = :strEstado
                          AND infoSubred.USO       = :strUsoSubred ";

            $objQuery->setParameter("strEstado"    , $arrayParametros['strEstado']);
            $objQuery->setParameter("strUsoSubred" , $arrayParametros['strUsoSubred']);

            if(!empty($arrayParametros['strAnillo']))
            {
                $strWhere .= ' AND infoSubred.ANILLO = :strAnillo ';
                $objQuery->setParameter("strAnillo"      , $arrayParametros['strAnillo']);
            }
            if(!empty($arrayParametros['strIdElemento']))
            {
                $strWhere .= ' AND infoSubred.ELEMENTO_ID = :strIdElemento ';
                $objQuery->setParameter("strIdElemento", $arrayParametros['strIdElemento']);
            }
            if(!empty($arrayParametros['strTipo']))
            {
                $strWhere .= ' AND infoSubred.TIPO        = :strTipo ';
                $objQuery->setParameter("strTipo"      , $arrayParametros['strTipo']);
            }
            $objRsm->addScalarResult('SUBRED'       , 'subred' , 'string');
            $objRsm->addScalarResult('ID_SUBRED'    , 'id'     , 'integer');

            $strSqlFinal = $strSelect . $strFrom . $strWhere;

            $objQuery->setSQL($strSqlFinal);
            $arraySubredes = $objQuery->getResult();
            if(!empty($arraySubredes))
            {
                $arrayResultado = array(
                                        "strStatus"  => "OK" ,
                                        "arrayData"  => $arraySubredes,
                                        "strMensaje" => "Información recuperada existosamente"
                                       );
            }
            else
            {
                $arrayResultado = array(
                                        "strStatus"  => "ERROR" ,
                                        "arrayData"  => array(),
                                        "strMensaje" => "No se encontro información"
                                       );
            }
        }
        catch (\Exception $ex)
        {
            $arrayResultado = array(
                                    "strStatus"  => "ERROR" ,
                                    "arrayData"  => array(),
                                    "strMensaje" => $ex->getMessage()
                                   );
        }

        return json_encode($arrayResultado);
    }
     /*
     * Documentación para el método 'getPersonaEmpresaRolImportada'.
     *
     * Método utilizado para obtener los id personas relacionados del cliente mediante la vpn importada
     *
     * @param int $intIdPersonaEmpresaRol Rol del cliente en session
     * @return array $arrayPersonaEmpresaRol
     * @costoQuery 7
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 20-10-2016
     */
    public function getPersonaEmpresaRolImportada($intIdPersonaEmpresaRol)
    {
        $objQuery = $this->_em->createQuery(null);

        $strDql = "SELECT IDENTITY(iperc.personaEmpresaRolId)
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
                            AND acvpn.descripcionCaracteristica = :vpn_import )";

        $objQuery->setParameter('personaEmpresaRolId', $intIdPersonaEmpresaRol);
        $objQuery->setParameter('vpn_import', "VPN_IMPORTADA");
        $objQuery->setParameter('estado', "Activo");        
        
        $objQuery->setDQL($strDql);         
        $arrayPersonaEmpresaRol = $objQuery->getResult();
        
        return $arrayPersonaEmpresaRol;
    }
    
    /**
     * Documentación para el método 'getSubredesDisponiblesCliente'.
     *
     * Método utilizado para obtener las subredes de un cliente
     *   
     * 
     * @param Array  $arrayParametros [
     *                                  intIdPersonaEmpresaRol       Rol del cliente a generar asignacion de recursos de red
     *                                  strNombreElemento            Nombre del elemento Pe
     *                                  strNombreTecnico             Nombre tecnico del producto ligado al servicio
     *                                  strTipoEnlace                Tipo enlace del servicio
     *                                  intAnillo                    Numero de anillo
     *                                  strEsPseudoPe                Determina si el servicio depende de pseudoPe administrado por Cliente
     *                                ]
     *
     * @return array arrayVlans
     *
     * @author Kenneth Jimenez <kjimenez@telconet.ec>
     * @version 1.0 08-12-2015
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.1 20-10-2016 Se incluyen en las subredes la de las vpns importadas
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.2 16-11-2016 Validacion para obtener las subredes de pe vinculado a un edificio cliente ( pseudope )
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.3 08-03-2017 Validacion para obtener las subredes de los clientes importados correctamente
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.4 29-03-2017 se cambia validación para que se fusionen el array de las subredes bajo cualquier escenario
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.5 13-07-2017 Se versiona cambios realizados en producción manualmente
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.6 21-07-2017 Se versiona cambios realizados en producción manualmente
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.7 27-02-2018 Se valida que cuando sea flujos de interconexion de clientes solo busque las subredes marcada como tal
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.8 14-03-2018 Se realiza cambio en la estructuracion de los querys usando NativeQuery para poder cambiar la forma de la consulta
     *                         utilizando EXIST dado que habia una delay muy grande en traer el resultado de la consulta de subredes
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.9 18-04-2018 Se realiza ajuste para que soporte flujos para DATOSDC
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.0 16-03-2021 Se realiza el cambio de L3MPLS SDWAN por L3MPLS, con el objetivo de obtener tambien para el producto de SDWAN las
     *                         subredes existentes de L3MPLS
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 2.1 25-05-2021 Se valida si el tipo de red es GPON se agrega el uso de la red GPON y no se agrega la validación del anillo.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 2.2 19-07-2021 - Se valida tipo red por deafult MPLS
     * 
     * @author Manuel Adrian <mcarpio@telconet.ec>
     * @version 2.2.1 13-12-2022 - Se agrega a la validacion nombre Tecnico es L3MPLS SDWAN para poder 
     *                             tomar la sub redes de esos servicios y se agrega la query IN ("L3MPLS", "L3MPLS SDWAN")
    */
    public function getSubredesDisponiblesCliente($arrayParametros)
    {
        $arraySubredes       = array();   
        $arrayQueryImport    = array();
        $strWhere            = '';
        $boolEsInterconexion = false;
        //verifico tipo de red
        $arrayParametros['strTipoRed'] = isset($arrayParametros['strTipoRed']) ? $arrayParametros['strTipoRed'] : "MPLS";
        
        //Si existe o s envia el parametro , la varible bool tomara ese valor
        if(isset($arrayParametros['boolEsInterconexion']))
        {
            $boolEsInterconexion = $arrayParametros['boolEsInterconexion'];
        }
        
        if(isset($arrayParametros['intIdPersonaEmpresaRol']) && $arrayParametros['intIdPersonaEmpresaRol'] > 0 && 
           isset($arrayParametros['strNombreElemento']) &&
           isset($arrayParametros['strNombreTecnico']) &&
           isset($arrayParametros['strTipoEnlace'])
          )
        {
            $arrayPersonaEmpresaRol = $this->getPersonaEmpresaRolImportada($arrayParametros['intIdPersonaEmpresaRol']);

            $objRsmSubredes           = new ResultSetMappingBuilder($this->_em);
            $objNtvQuerySubredes      = $this->_em->createNativeQuery(null, $objRsmSubredes);
            
            $objRsmSubredesImp        = new ResultSetMappingBuilder($this->_em);
            $objNtvQuerySubredesImp   = $this->_em->createNativeQuery(null, $objRsmSubredesImp);

            $strFrom       = "";
            $strWhere      = "";

            //1.- Validar si el nombre Tecnico es L3MPLS SDWAN sea cambiada por L3MPLS
            //2.- Se agrega a la validacion nombre Tecnico es L3MPLS SDWAN para poder 
            //    tomar la sub redes de esos servicios
            if($arrayParametros['strNombreTecnico'] == "L3MPLS SDWAN")
            {
                $arrayParametros['strNombreTecnico'] = array("L3MPLS SDWAN");
                $arrayParametros['strTipoEnlace']    = 'PRINCIPAL';
            }
            else if($arrayParametros['strNombreTecnico'] == "L3MPLS")
            {
                $arrayParametros['strNombreTecnico'] = array("L3MPLS", "L3MPLS SDWAN");
                $arrayParametros['strTipoEnlace']    = 'PRINCIPAL';
            }

            //verificar si el tipo de red es GPON
            $booleanTipoRedGpon = false;
            if(isset($arrayParametros['strTipoRed']) && !empty($arrayParametros['strTipoRed']))
            {
                $arrayParVerTipoRed = $this->_em->getRepository('schemaBundle:AdmiParametroDet')->getOne('NUEVA_RED_GPON_TN',
                                                                                                'COMERCIAL',
                                                                                                '',
                                                                                                'VERIFICAR TIPO RED',
                                                                                                'VERIFICAR_GPON',
                                                                                                $arrayParametros['strTipoRed'],
                                                                                                '',
                                                                                                '',
                                                                                                '');
                if(isset($arrayParVerTipoRed) && !empty($arrayParVerTipoRed))
                {
                    $booleanTipoRedGpon = true;
                }
            }
            if(!isset($arrayParametros['strEsPseudoPe']) && $arrayParametros['strNombreTecnico']!='DATOSDC' && !$booleanTipoRedGpon)
            {
                $strFrom  .= " ,DB_INFRAESTRUCTURA.INFO_ELEMENTO      ELEMENTO_SW  ";
                $strWhere .= "  AND SERVICIO_TECNICO.ELEMENTO_ID    = ELEMENTO_SW.ID_ELEMENTO 
                                AND EXISTS(
                                     SELECT
                                      1 
                                     FROM 
                                      DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO DETALLE_ELEMENTO_SW 
                                    WHERE
                                       ELEMENTO_SW.ID_ELEMENTO               = DETALLE_ELEMENTO_SW.ELEMENTO_ID
                                      AND DETALLE_ELEMENTO_SW.DETALLE_NOMBRE = :nombreDetalle
                                      AND DETALLE_ELEMENTO_SW.DETALLE_VALOR  = :anillo
                                    )
                                ";
                
                $objNtvQuerySubredes->setParameter('anillo',        $arrayParametros['intAnillo']);
                $objNtvQuerySubredes->setParameter('nombreDetalle', "ANILLO");
                
                $objNtvQuerySubredesImp->setParameter('anillo',       $arrayParametros['intAnillo']);
                $objNtvQuerySubredesImp->setParameter('nombreDetalle',"ANILLO");                
            }
            
            if(isset($arrayParametros['strUsoSubred']) && !empty($arrayParametros['strUsoSubred']))
            {
                if($boolEsInterconexion)
                {
                    $strWhere .= " AND SUBRED.USO = :uso ";
                }
                else
                {                
                    $strWhere .= " AND SUBRED.USO <> :uso ";
                }
                
                $objNtvQuerySubredes->setParameter('uso', $arrayParametros['strUsoSubred']);
                $objNtvQuerySubredesImp->setParameter('uso', $arrayParametros['strUsoSubred']);
            }

            if($booleanTipoRedGpon && isset($arrayParametros['strUsosGponSubred']) && !empty($arrayParametros['strUsosGponSubred']))
            {
                $strWhere .= " AND SUBRED.USO IN (:usosGpon) ";
                $objNtvQuerySubredes->setParameter('usosGpon', $arrayParametros['strUsosGponSubred']);
                $objNtvQuerySubredesImp->setParameter('usosGpon', $arrayParametros['strUsosGponSubred']);
            }
            elseif(isset($arrayParametros['strUsosGponSubred']) && !empty($arrayParametros['strUsosGponSubred']))
            {
                $strWhere .= " AND SUBRED.USO NOT IN (:usosGpon) ";
                $objNtvQuerySubredes->setParameter('usosGpon', $arrayParametros['strUsosGponSubred']);
                $objNtvQuerySubredesImp->setParameter('usosGpon', $arrayParametros['strUsosGponSubred']);
            }

            //obtengo las vpn importadas del cliente, para luego usar como filtro
            $objRsmImportada        = new ResultSetMappingBuilder($this->_em);
            $objNtvQueryImportada   = $this->_em->createNativeQuery(null, $objRsmImportada);
            
            $strSqlVrfImportada = "SELECT (SELECT PERC3.ID_PERSONA_EMPRESA_ROL_CARACT 
                                             FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC PERC3,
                                                  DB_COMERCIAL.ADMI_CARACTERISTICA C1 
                                            WHERE PERC3.PERSONA_EMPRESA_ROL_CARAC_ID = PERC2.ID_PERSONA_EMPRESA_ROL_CARACT 
                                            AND C1.ID_CARACTERISTICA = PERC3.CARACTERISTICA_ID 
                                            AND C1.DESCRIPCION_CARACTERISTICA = :caracteristica2) VRF 
                                     FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC PERC, 
                                          DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC PERC2, 
                                          DB_COMERCIAL.ADMI_CARACTERISTICA C 
                                    WHERE C.ID_CARACTERISTICA          = PERC.CARACTERISTICA_ID 
                                      AND PERC.PERSONA_EMPRESA_ROL_ID = :personaEmpresaRolId 
                                      AND PERC2.ID_PERSONA_EMPRESA_ROL_CARACT      = COALESCE(TO_NUMBER(REGEXP_SUBSTR(PERC.VALOR,'^\d+')),0) 
                                      AND C.DESCRIPCION_CARACTERISTICA = :caracteristica 
                                      AND PERC.ESTADO   = :estado" ;
            
            
            $objNtvQueryImportada->setParameter('personaEmpresaRolId',  $arrayParametros['intIdPersonaEmpresaRol']);
            $objNtvQueryImportada->setParameter('caracteristica', 'VPN_IMPORTADA');
            $objNtvQueryImportada->setParameter('caracteristica2', 'VRF');
            $objNtvQueryImportada->setParameter('estado', 'Activo');
            
            $objRsmImportada->addScalarResult('VRF', 'vrf', 'string');
            
            //obtengo las subredes segun las vpn importadas y los id persona empresa rol de los clientes asociados
            $objNtvQueryImportada->setSQL($strSqlVrfImportada);
            
            $arrayVrfImportada = $objNtvQueryImportada->getResult();
            
            if($arrayPersonaEmpresaRol && $arrayVrfImportada)
            {
                $strSqlImportada = "  SELECT 
                                        SUBRED.SUBRED,
                                        SUBRED.ID_SUBRED,
                                        SERVICIO.ID_SERVICIO
                                      FROM 
                                        DB_COMERCIAL.INFO_PUNTO                   PUNTO,
                                        DB_COMERCIAL.ADMI_PRODUCTO                PRODUCTO,
                                        DB_COMERCIAL.INFO_SERVICIO                SERVICIO,
                                        DB_COMERCIAL.INFO_SERVICIO_TECNICO        SERVICIO_TECNICO,
                                        DB_INFRAESTRUCTURA.INFO_IP                IP,
                                        DB_INFRAESTRUCTURA.INFO_SUBRED            SUBRED,
                                        DB_INFRAESTRUCTURA.INFO_ELEMENTO          ELEMENTO_RO,
                                        DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT    SERVICIO_PROD_CARACT,
                                        DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA PROD_CARACT,
                                        DB_COMERCIAL.ADMI_CARACTERISTICA          CARACTERISTICA
                                        $strFrom
                                      WHERE 
                                            SERVICIO.ESTADO                        IN (:estadoServicios) 
                                      AND PROD_CARACT.ID_PRODUCTO_CARACTERISITICA   = SERVICIO_PROD_CARACT.PRODUCTO_CARACTERISITICA_ID
                                      AND PROD_CARACT.PRODUCTO_ID                   = SERVICIO.PRODUCTO_ID
                                      AND PROD_CARACT.CARACTERISTICA_ID             = CARACTERISTICA.ID_CARACTERISTICA
                                      AND CARACTERISTICA.DESCRIPCION_CARACTERISTICA = :caracteristica
                                      AND SERVICIO_PROD_CARACT.SERVICIO_ID          = SERVICIO.ID_SERVICIO
                                      AND SERVICIO_PROD_CARACT.ESTADO               = :estado
                                      AND SERVICIO_PROD_CARACT.VALOR                IN (:arrayVrfImportada)   
                                      AND PRODUCTO.NOMBRE_TECNICO                   IN (:nombreTecnico)
                                      AND IP.ESTADO                                 = :estado
                                      AND ELEMENTO_RO.ESTADO                        = :estado
                                      AND ELEMENTO_RO.NOMBRE_ELEMENTO               = :nombreElemento
                                      AND SERVICIO.ID_SERVICIO                      = SERVICIO_TECNICO.SERVICIO_ID
                                      AND PUNTO.ID_PUNTO                            = SERVICIO.PUNTO_ID
                                      AND SERVICIO.PRODUCTO_ID                      = PRODUCTO.ID_PRODUCTO
                                      AND IP.SERVICIO_ID                            = SERVICIO.ID_SERVICIO
                                      AND SERVICIO_TECNICO.TIPO_ENLACE              = :tipoEnlace
                                      AND IP.SUBRED_ID                              = SUBRED.ID_SUBRED
                                      AND SUBRED.ELEMENTO_ID                        = ELEMENTO_RO.ID_ELEMENTO
                                      $strWhere";
                
                $objNtvQuerySubredesImp->setParameter('arrayVrfImportada',   $arrayVrfImportada);
                $objNtvQuerySubredesImp->setParameter('caracteristica',      'VRF');
                $objNtvQuerySubredesImp->setParameter('nombreTecnico',       $arrayParametros['strNombreTecnico']);
                $objNtvQuerySubredesImp->setParameter('tipoEnlace',          $arrayParametros['strTipoEnlace']);
                $objNtvQuerySubredesImp->setParameter('nombreElemento',      $arrayParametros['strNombreElemento']);
                $objNtvQuerySubredesImp->setParameter('estadoServicios',     array("Activo","In-Corte","EnPruebas"));
                $objNtvQuerySubredesImp->setParameter('estado',              "Activo");
                
                $objRsmSubredesImp->addScalarResult('SUBRED',     'subred',     'string');
                $objRsmSubredesImp->addScalarResult('ID_SUBRED',  'id',         'integer');
                $objRsmSubredesImp->addScalarResult('ID_SERVICIO','idServicio', 'integer');

                //obtengo las subredes del cliente
                $objNtvQuerySubredesImp->setSQL($strSqlImportada);                             
                $arrayQueryImport = $objNtvQuerySubredesImp->getResult(); 
            }
            
            $arrayPersonaEmpresaRol[] = $arrayParametros['intIdPersonaEmpresaRol'];
            
            $strSqlSubredes = "
                          SELECT 
                            SUBRED.SUBRED,
                            SUBRED.ID_SUBRED,
                            SERVICIO.ID_SERVICIO
                          FROM 
                            DB_COMERCIAL.INFO_PUNTO            PUNTO,
                            DB_COMERCIAL.ADMI_PRODUCTO         PRODUCTO,
                            DB_COMERCIAL.INFO_SERVICIO         SERVICIO,
                            DB_COMERCIAL.INFO_SERVICIO_TECNICO SERVICIO_TECNICO,
                            DB_INFRAESTRUCTURA.INFO_IP         IP,
                            DB_INFRAESTRUCTURA.INFO_SUBRED     SUBRED,
                            DB_INFRAESTRUCTURA.INFO_ELEMENTO   ELEMENTO_RO
                            $strFrom
                          WHERE SERVICIO.ESTADO                  IN (:estadoServicios)
                          AND PRODUCTO.NOMBRE_TECNICO            IN (:nombreTecnico)
                          AND IP.ESTADO                          = :estado
                          AND ELEMENTO_RO.ESTADO                 = :estado
                          AND ELEMENTO_RO.NOMBRE_ELEMENTO        = :nombreElemento
                          AND PUNTO.PERSONA_EMPRESA_ROL_ID       IN (:personaEmpresaRolId)
                          AND SERVICIO.ID_SERVICIO               = SERVICIO_TECNICO.SERVICIO_ID
                          AND PUNTO.ID_PUNTO                     = SERVICIO.PUNTO_ID
                          AND SERVICIO.PRODUCTO_ID               = PRODUCTO.ID_PRODUCTO
                          AND IP.SERVICIO_ID                     = SERVICIO.ID_SERVICIO
                          AND SERVICIO_TECNICO.TIPO_ENLACE       = :tipoEnlace
                          AND IP.SUBRED_ID                       = SUBRED.ID_SUBRED
                          AND SUBRED.ELEMENTO_ID                 = ELEMENTO_RO.ID_ELEMENTO
                          $strWhere";
            
            $objNtvQuerySubredes->setParameter('personaEmpresaRolId', $arrayPersonaEmpresaRol);
            $objNtvQuerySubredes->setParameter('nombreTecnico',       $arrayParametros['strNombreTecnico']);
            $objNtvQuerySubredes->setParameter('tipoEnlace',          $arrayParametros['strTipoEnlace']);
            $objNtvQuerySubredes->setParameter('nombreElemento',      $arrayParametros['strNombreElemento']);
            $objNtvQuerySubredes->setParameter('estadoServicios',     array("Activo","In-Corte","EnPruebas"));
            $objNtvQuerySubredes->setParameter('estado',              "Activo");   
            
            $objRsmSubredes->addScalarResult('SUBRED',     'subred',     'string');
            $objRsmSubredes->addScalarResult('ID_SUBRED',  'id',         'integer');
            $objRsmSubredes->addScalarResult('ID_SERVICIO','idServicio', 'integer');

            $objNtvQuerySubredes->setSQL($strSqlSubredes);
            $arrayResultSubredes = $objNtvQuerySubredes->getResult();

            //fusiona las subredes del cliente con las de los clientes vpn importadas
            $arrayResultSubredesFinal= array();
            if(is_array($arrayQueryImport))
            {
                $arrayResultSubredesFinal = array_merge($arrayResultSubredesFinal, $arrayQueryImport);
            }
            if(is_array($arrayResultSubredes ))
            {
                $arrayResultSubredesFinal = array_merge($arrayResultSubredesFinal, $arrayResultSubredes);
            }
            
            foreach($arrayResultSubredesFinal as $subred)
            {
                $ipDisponible = $this->_em->getRepository('schemaBundle:InfoIp')->getIpDisponibleBySubred($subred['id']);
                
                if($ipDisponible!="NoDisponible")
                {
                    $arraySubredes[] = $subred;
                }
            }
        }                    
        
        return $arraySubredes;
    }

    /**
     * Funcion que ejecuta un sql para obtener las subredes disponibles
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 17-12-2015
     *
     * @author Juan Lafuente <jlafuente@telconet.ec>
     * @version 1.1 07-05-2016
     * 
     * @author Josue Valencia <ajvalencia@telconet.ec>
     * @version 1.2 22-12-2022 - Se añade mascara para realizar un filtro de las subredes obtenidas por PE
     *
     * @param $arrayParametros [idElemento, tipo, uso, anillo, mascara]
     * @return $array [subred, mascara, gateway, ipInicial, ipFinal, ipDisponible]
     */
    public function getSubredByElementoTipoUso($arrayParametros)
    {
        $rsm = new ResultSetMappingBuilder($this->_em);
        $query = $this->_em->createNativeQuery(null, $rsm);

        $sql = "SELECT ID_SUBRED, 
                    SUBRED, 
                    MASCARA, 
                    GATEWAY, 
                    IP_INICIAL, 
                    IP_FINAL, 
                    IP_DISPONIBLE
                FROM INFO_SUBRED
                WHERE ESTADO        = :estado
                    AND ELEMENTO_ID = :elementoId
                    AND TIPO        = :tipo
                    AND USO         = :uso";

        $query->setParameter("estado"    , 'Activo');
        $query->setParameter("elementoId", $arrayParametros['idElemento']);
        $query->setParameter("tipo"      , $arrayParametros['tipo']);
        $query->setParameter("uso"       , $arrayParametros['uso']);

        // jlafuente: se agrega filtro por anillo 
        if(!empty($arrayParametros['anillo']))
        {
            $sql .= ' AND ANILLO = :ANILLO ';
            $query->setParameter("ANILLO"      , $arrayParametros['anillo']);
        }
        // ajvalencia: se agrega filtro por mascara 
        if(!empty($arrayParametros['mascara']))
        {
            $sql .= ' AND MASCARA = :MASCARA ';
            $query->setParameter("MASCARA", $arrayParametros['mascara']);
        }
        // ajvalencia: se agrega filtro por subred
        if(!empty($arrayParametros['subred']))
        {
            $sql .= ' AND SUBRED = :SUBRED';
            $query->setParameter("SUBRED", $arrayParametros['subred']);
        }
        $rsm->addScalarResult('ID_SUBRED'    , 'idSubred'    , 'integer');
        $rsm->addScalarResult('SUBRED'       , 'subred'      , 'string');
        $rsm->addScalarResult('MASCARA'      , 'mascara'     , 'string');
        $rsm->addScalarResult('GATEWAY'      , 'gateway'     , 'string');
        $rsm->addScalarResult('IP_INICIAL'   , 'ipInicial'   , 'string');
        $rsm->addScalarResult('IP_FINAL'     , 'ipFinal'     , 'string');
        $rsm->addScalarResult('IP_DISPONIBLE', 'ipDisponible', 'string');
        
        $query->setSQL($sql);
        $datos = $query->getResult();

        return $datos;
    }


     /**
     * Función que retorna las subredes disponibles por clientes especificos
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 04-01-2019
     *
     * @param $arrayParametros [ idElemento => id elemento del PE
     *                           tipo       => WAN o LAN
     *                           uso        => tipo de uso de las subredes
     *                           anillo     => 0,1,2,3,4,
     *                           idPersona  => id persona de la razon social ]
     *
     * @return array $arraySubredes
     */
    public function getSubredByCliente($arrayParametros)
    {
        $objRsm    = new ResultSetMappingBuilder($this->_em);
        $objQuery  = $this->_em->createNativeQuery(null, $objRsm);

        $strSelect = " SELECT infoSubred.ID_SUBRED,
                    infoSubred.SUBRED,
                    infoSubred.MASCARA,
                    infoSubred.GATEWAY,
                    infoSubred.IP_INICIAL,
                    infoSubred.IP_FINAL,
                    infoSubred.IP_DISPONIBLE ";
        $strFrom = " FROM INFO_SUBRED infoSubred,"
                 . " ADMI_PARAMETRO_DET admiParametroDet ";
        $strWhere = " WHERE infoSubred.ESTADO        = :estado
                    AND infoSubred.ELEMENTO_ID = :elementoId
                    AND infoSubred.TIPO        = :tipo
                    AND infoSubred.USO         = :uso
                    AND infoSubred.SUBRED = admiParametroDet.VALOR1
                    AND admiParametroDet.VALOR2 = :PERSONA ";

        $objQuery->setParameter("PERSONA"   , $arrayParametros['idPersona']);
        $objQuery->setParameter("estado"    , 'Activo');
        $objQuery->setParameter("elementoId", $arrayParametros['idElemento']);
        $objQuery->setParameter("tipo"      , $arrayParametros['tipo']);
        $objQuery->setParameter("uso"       , $arrayParametros['uso']);

        if(!empty($arrayParametros['anillo']))
        {
            $strWhere .= ' AND infoSubred.ANILLO = :ANILLO ';
            $objQuery->setParameter("ANILLO"      , $arrayParametros['anillo']);
        }

        $objRsm->addScalarResult('ID_SUBRED'    , 'idSubred'    , 'integer');
        $objRsm->addScalarResult('SUBRED'       , 'subred'      , 'string');
        $objRsm->addScalarResult('MASCARA'      , 'mascara'     , 'string');
        $objRsm->addScalarResult('GATEWAY'      , 'gateway'     , 'string');
        $objRsm->addScalarResult('IP_INICIAL'   , 'ipInicial'   , 'string');
        $objRsm->addScalarResult('IP_FINAL'     , 'ipFinal'     , 'string');
        $objRsm->addScalarResult('IP_DISPONIBLE', 'ipDisponible', 'string');

        $strSqlFinal = $strSelect . $strFrom . $strWhere;

        $objQuery->setSQL($strSqlFinal);
        $arraySubredes = $objQuery->getResult();

        return $arraySubredes;
    }

    /**
     * Funcion que sirve para obtener una subred disponible basados en la generacion 
     * del arbol de las subredes por uso (INTMPLS,RUTASINTMPLS,L3MPLS)
     * 
     * @author Juan Lafuente <jlafuente@telconet.ec>
     * @version 1.0 13-04-2016
     *
     * @param array $arrayParametros [ tipoAccion, prefijoRed, mascara, subred, estadoParaBuscar, estadoParaCambiar, uso, elementoId]
     * @param array $arrayResponse 
     */
    public function provisioningSubred($arrayParametros)
    {     
        $pv_mensaje_error      = '';
        $pv_mensaje_error      = str_pad($pv_mensaje_error, 3000, " ");
        $pv_subred_encontrada  = '';
        $pv_subred_encontrada  = str_pad($pv_subred_encontrada, 3000, " ");

        $sql = "BEGIN INFRK_TRANSACCIONES.INFRP_SUBNETING( :Pv_TipoAccion, "
             . "                                           :Pv_SubredId,"
             . "                                           :Pv_ElementoId,"
             . "                                           :Pv_SubredPrefijo,"
             . "                                           :Pv_Uso,"
             . "                                           :Pv_Mascara,"
             . "                                           :Pv_SubredEncontrada,"
             . "                                           :Lv_MensaError"
             . "                                           ); "
             . "END;";
       
        $stmt = $this->_em->getConnection()->prepare($sql);
        
        $stmt->bindParam('Pv_TipoAccion',        $arrayParametros['tipoAccion']);
        $stmt->bindParam('Pv_SubredId',          $arrayParametros['subredId']);
        $stmt->bindParam('Pv_ElementoId',        $arrayParametros['elementoId']);
        $stmt->bindParam('Pv_SubredPrefijo',     $arrayParametros['subredPrefijo']);
        $stmt->bindParam('Pv_Uso',               $arrayParametros['uso']);
        $stmt->bindParam('Pv_Mascara',           $arrayParametros['mascara']);
        $stmt->bindParam('Pv_SubredEncontrada',  $pv_subred_encontrada);
        $stmt->bindParam('Lv_MensaError',        $pv_mensaje_error);
        $stmt->execute();
        
        $arrayResponse              = array();
        $arrayResponse['msg']       = $pv_mensaje_error;
        $arrayResponse['subredId']  = $pv_subred_encontrada;
        
        return $arrayResponse;
    }       
    
    /**
     * Funcion que sirve para crear subred dado un nuevo prefijo de red
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 22-09-2016
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 28-06-2017 - Se parametria primer y tercer octeto asi como el uso de la subred a ser creada
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.2 21-06-2018 - Se parametriza el tipo de prefijo al momento de crear una nueva subred
     *
     * @param array $arrayParametros [ prefijoRed, inicioRed, finRed, primerOcteto, tercerOcteto, uso ]
     * @param array $arrayResponse 
     */
    public function crearRedYSubred($arrayParametros)
    {             
        $strMensajeError      = str_pad($strMensajeError, 3000, " ");        

        $sql = "BEGIN INFRK_TRANSACCIONES.INFRP_CREAR_REDES_Y_SUBREDES( "
             . "                                           :Pv_PrefijoRed, "
             . "                                           :Pv_InicioRed,"
             . "                                           :Pv_FinRed,"
             . "                                           :Pv_PrimerOct,"
             . "                                           :Pv_TercerOct,"
             . "                                           :Pv_tipoUso,"
             . "                                           :Pv_tipoPrefijo,"
             . "                                           :Lv_MensaError"
             . "                                           ); "
             . "END;";
       
        $stmt = $this->_em->getConnection()->prepare($sql);
        
        $stmt->bindParam('Pv_PrefijoRed',        $arrayParametros['prefijoRed']);
        $stmt->bindParam('Pv_InicioRed',         $arrayParametros['inicioRed']);
        $stmt->bindParam('Pv_FinRed',            $arrayParametros['finRed']); 
        $stmt->bindParam('Pv_PrimerOct',         $arrayParametros['primerOcteto']);
        $stmt->bindParam('Pv_TercerOct',         $arrayParametros['tercerOcteto']);
        $stmt->bindParam('Pv_tipoUso',           $arrayParametros['uso']); 
        $stmt->bindParam('Pv_tipoPrefijo',       $arrayParametros['tipoPrefijo']); 
        $stmt->bindParam('Lv_MensaError',        $strMensajeError);
        $stmt->execute();        
    }       
    
    /**
     * Metodo encargado de obtener el array de todas las subredes existintes en un cliente de tipo enviado por parametro
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 05-10-2017
     * 
     * Costo 11
     * 
     * @param Array $arrayParametros [ intIdPersonaEmpresaRol , strUso ]
     * @return Array [ idSubred, subred]
     */
    public function getArraySubredesInternetDC($arrayParametros)
    {
        $objRsm    = new ResultSetMappingBuilder($this->_em);
        $objQuery  = $this->_em->createNativeQuery(null, $objRsm);

        $strSql = " SELECT SUBRED.ID_SUBRED,
                           SUBRED.SUBRED
                    FROM DB_COMERCIAL.INFO_PUNTO        PUNTO,
                         DB_COMERCIAL.INFO_SERVICIO     SERVICIO,
                         DB_INFRAESTRUCTURA.INFO_IP     IP,
                         DB_INFRAESTRUCTURA.INFO_SUBRED SUBRED
                    WHERE PUNTO.PERSONA_EMPRESA_ROL_ID = :personaEmpresaRol
                    AND PUNTO.ID_PUNTO                 = SERVICIO.PUNTO_ID
                    AND SERVICIO.ID_SERVICIO           = IP.SERVICIO_ID
                    AND IP.SUBRED_ID                   = SUBRED.ID_SUBRED
                    AND SUBRED.USO                     = :uso
                    AND SERVICIO.ESTADO                IN (:arrayEstado)
                    AND PUNTO.ESTADO                   = :estado
                    AND IP.ESTADO                      = :estado";

        $objQuery->setParameter("arrayEstado"      , array('EnPruebas','Activo'));
        $objQuery->setParameter("estado"           , 'Activo');
        $objQuery->setParameter("personaEmpresaRol", $arrayParametros['intIdPersonaEmpresaRol']);     
        $objQuery->setParameter("uso"              , $arrayParametros['strUso']);

        $objRsm->addScalarResult('ID_SUBRED'    , 'idSubred'    , 'integer');
        $objRsm->addScalarResult('SUBRED'       , 'subred'      , 'string');
        
        $objQuery->setSQL($strSql);
        $arrayDatos = $objQuery->getResult();

        return $arrayDatos;
    }
    
    /**
     * 
     * Metodo encargado de obtener la subred en base a la conincidencia de la subred y la mascara enviada ( util para comparar si las subredes
     * de las rutas estaticas existen dentro del pool de subredes del sistema )
     * 
     * Costo 4
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 19-06-2018
     * 
     * @param type $arrayParametros
     * @return type
     */
    public function getSubredByIpYMascara($arrayParametros)
    {
        $objRsm    = new ResultSetMappingBuilder($this->_em);
        $objQuery  = $this->_em->createNativeQuery(null, $objRsm);

        $strSql = " SELECT SUBRED.ID_SUBRED,
                           SUBRED.SUBRED
                    FROM 
                         DB_INFRAESTRUCTURA.INFO_SUBRED SUBRED
                    WHERE 
                        SUBRED.SUBRED LIKE :ipSubred AND
                        SUBRED.MASCARA =   :mascara  AND
                        SUBRED.USO     =   :uso      AND
                        SUBRED.ESTADO  =   :estado
                        ";
        
        $objQuery->setParameter("ipSubred" ,$arrayParametros['strIpSubred'].'/%');
        $objQuery->setParameter("mascara" , $arrayParametros['strMascara']);     
        $objQuery->setParameter("uso"     , $arrayParametros['strUso']);
        $objQuery->setParameter("estado"  , $arrayParametros['strEstado']);

        $objRsm->addScalarResult('ID_SUBRED'    , 'idSubred'    , 'integer');
        $objRsm->addScalarResult('SUBRED'       , 'subred'      , 'string');
        
        $objQuery->setSQL($strSql);
        $arrayDatos = $objQuery->getOneOrNullResult();

        return $arrayDatos;
    }

    /**
     * Función que sirve para subnetear subredes hijas por un nuevo elemento y uso
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 07-08-2021
     *
     * @param array $arrayParametros [
     *                                  intIdElementoAnt,
     *                                  intIdElementoNuevo,
     *                                  strSubredIp,
     *                                  strPrefijoSubred,
     *                                  strUsoAnterior,
     *                                  strUsoNuevo,
     *                                  strTipo
     *                                ]
     * @param array $arrayResponse [
     *                                  status,
     *                                  mensaje
     *                             ]
     */
    public function subnetingSubredHijas($arrayParametros)
    {
        $strStatus  = '';
        $strMensaje = '';
        $strStatus  = str_pad($strStatus, 3000, " ");
        $strMensaje = str_pad($strMensaje, 3000, " ");

        $strSql = "BEGIN DB_INFRAESTRUCTURA.INFRK_TRANSACCIONES.SUBNETEAR_SUBRED_HIJAS(:pn_idElementoAnt,"
                . "                                           :pn_idElementoNuevo,"
                . "                                           :pv_subred_ip,"
                . "                                           :pv_subred_mascara,"
                . "                                           :pv_usoAnterior,"
                . "                                           :pv_usoNuevo,"
                . "                                           :pv_tipo,"
                . "                                           :pv_status,"
                . "                                           :pv_mensaje); "
                . "END;";
        $objSmtp = $this->_em->getConnection()->prepare($strSql);
        $objSmtp->bindParam('pn_idElementoAnt',    $arrayParametros['intIdElementoAnt']);
        $objSmtp->bindParam('pn_idElementoNuevo',  $arrayParametros['intIdElementoNuevo']);
        $objSmtp->bindParam('pv_subred_ip',        $arrayParametros['strSubredIp']);
        $objSmtp->bindParam('pv_subred_mascara',   $arrayParametros['strPrefijoSubred']);
        $objSmtp->bindParam('pv_usoAnterior',      $arrayParametros['strUsoAnterior']);
        $objSmtp->bindParam('pv_usoNuevo',         $arrayParametros['strUsoNuevo']);
        $objSmtp->bindParam('pv_tipo',             $arrayParametros['strTipo']);
        $objSmtp->bindParam('pv_status',           $strStatus);
        $objSmtp->bindParam('pv_mensaje',          $strMensaje);
        $objSmtp->execute();

        $arrayResponse            = array();
        $arrayResponse['status']  = $strStatus;
        $arrayResponse['mensaje'] = $strMensaje;

        return $arrayResponse;
    }
}

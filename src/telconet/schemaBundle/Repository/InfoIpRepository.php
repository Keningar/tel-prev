<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use telconet\schemaBundle\Entity\ReturnResponse;
use telconet\schemaBundle\DependencyInjection\BaseRepository;

class InfoIpRepository extends EntityRepository
{
    
    /**
     * Obtiene la Ip en caso de encontrarse activa o reservada
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 10-04-2015
     * 
     * @param string ipPool
     * 
     * @return string $data
     **/
    public function getIpExistente($ipPool) {
        
        $em  = $this->_em;
        $sql = "SELECT
                    iip
                FROM
                    schemaBundle:InfoIp iip
                WHERE 
                    iip.ip = :ipPool
                AND ( iip.estado = :estadoA or iip.estado = :estadoB)";

        $query = $em->createQuery($sql);
        $query->setParameter('ipPool', $ipPool);
        $query->setParameter('estadoA', 'Activo');
        $query->setParameter('estadoB', 'Reservada');

        return $query->getResult();
    }
    
    /**
     * Documentación para el método 'getIpsReservadasOlt'.
     *
     * Obtiene ip que fueron reservadas para el servicio por objeto de migracion
     * 
     * @param integer $nro
     * @param integer $id_elemento
     * @param integer $id_servicio
     * @param integer $id_punto
     * @param string  $esPlan
     * @param integer $id_plan
     * 
     * @return array.
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 08-04-2015
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 02-12-2015 Se agrega parametro $estado para obtener IP activas de clientes TELLION CNR
     */
    public function getIpsReservadasOlt($id_servicio, $estado = 'Reservada')
    {
        $emInf                = $this->_em;
        $arrayResponse        = array();
        $arrayResponse['ips'] = array();

        try
        {
            $sql = "SELECT
                        iip
                    FROM
                        schemaBundle:InfoIp iip
                    WHERE iip.estado = :estadoP
                    AND iip.servicioId = :idServicio";

            $query = $emInf->createQuery($sql);
            $query->setParameter('idServicio', $id_servicio);
            $query->setParameter('estadoP', $estado);

            $ipsExistente = $query->getResult();

            if($ipsExistente)
            {
                foreach($ipsExistente as $ipExistente)
                {
                    $arrayIp                = array();
                    $arrayIp['ip']          = $ipExistente->getIp();
                    $arrayIp['tipo']        = $ipExistente->getTipoIp();
                    $arrayResponse['ips'][] = $arrayIp;
                }
            }
            else
            {
                $arrayResponse['error'] = "No existen Ips Reservadas para este servicio, favor notificar a Sistemas.";
            }

        }
        catch(\Exception $e)
        {
            // Rollback the failed transaction attempt
            $arrayResponse['error'] = $e->getMessage();
        }

        return $arrayResponse;
    }
    
    /**
     * Obtiene la Ips del servicio por estados
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 28-10-2015
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.1 13-09-2018 Se aumenta un filtro para que no considere las IPS de telefonia [noTipoIp]
     * 
     * @param array   $arrayParametros
     * 
     * @return result
     **/
    public function getIpsPorServicioPorEstados( $arrayParametros ) {
        
        $em  = $this->_em;
        $sql = "SELECT
                    iip
                FROM
                    schemaBundle:InfoIp iip
                WHERE 
                iip.servicioId = :servicioIdParam
                AND iip.estado in (:estadosParam) ";
        
        if($arrayParametros['noTipoIp'])
        {
            $sql .=    ' AND iip.tipoIp not in (:noTipoIp)';
        }

        $query = $em->createQuery($sql);
        $query->setParameter('servicioIdParam', $arrayParametros['idServicio']);
        $query->setParameter('estadosParam',    $arrayParametros['arrayEstados']);
        
        if($arrayParametros['noTipoIp'])
        {
            $query->setParameter('noTipoIp',    $arrayParametros['noTipoIp']);
        }

        return $query->getResult();
    }

    /**
     * Funcion que sirve para obtener la ip siguiente disponible de una subred 
     * Ejemplo.
     *        Subred: 		192.168.15.0/24
     *        IPs Reservadas: 	5
     *        IP Inicial:	192.168.15.6
     *        IP Final:		192.168.15.254
     * 
     * y mantenemos una tabla de ips para esta subred de la siguiente manera
     * 192.168.15.6	->	Activa, Asignada a un cliente X.
     * 192.168.15.7	->	Activa, Asignada a un cliente C.
     * 192.168.15.9	->	Activa, Asignada a un cliente A.
     * 
     * La IP Disponible seria
     * 192.168.15.8
     * La IP Disponible seria
     * 192.168.15.10
     * 
     * Si existen vacios en la secuencia de IPs el aprovicionamiento seria 
     * completando primeramente la secuencia con limites definidos en la subred
     * 
     * @author Juan Lafuente <jlafuente@telconet.ec>
     * @version 1.0 29-03-2013
     * @param string $intIdSubred
     * @return string $strIpDisponible
     */
    public function getIpDisponibleBySubred($intIdSubred) 
    {
        $strIpDisponible = 'NoDisponible';
        
        // Se obtiene los datos de la subred
        $objSubred = $this->_em->getRepository('schemaBundle:InfoSubred')->find($intIdSubred);
        
        $ipInicial = explode( '.', $objSubred->getIpInicial())[3];
        $ipFinal   = explode( '.', $objSubred->getIpFinal())[3];
        // Se obtiene toas las ips activas para la subred
        $objIp = $this->_em->getRepository('schemaBundle:InfoIp')
                                    ->createQueryBuilder('p')
                                    ->where('p.subredId = :subredId')
                                    ->andWhere("p.estado = :estadoActivo OR p.estado = :estadoReservada")
                                    ->setParameter('subredId', $intIdSubred)
                                    ->setParameter('estadoActivo','Activo')
                                    ->setParameter('estadoReservada','Reservada')
                                    ->getQuery()
                                    ->getResult();
        // se verifican si existen ips activas para la subred
        if(count($objIp)>0)
        {
            // Se segmenta todas las ips activas en un solo array
            $arrayOnlyIp = array();
            foreach ($objIp as $ip) 
            {
                $arrayOnlyIp[] = $ip->getIp();
            }
                        
            // Se almacena los primeros octetos de la subred
            $subredIp = explode( '.', $objSubred->getIpInicial())[0].'.'.
                        explode( '.', $objSubred->getIpInicial())[1].'.'.
                        explode( '.', $objSubred->getIpInicial())[2].'.';

            // Se procede a aislar el ultimo octeto de la ip en un array ($arrayIpActivas)
            $arrayIpActivas = array();
            foreach ($arrayOnlyIp as $ipActive) 
            {
                 $arrayIpActivas[] = explode( '.', $ipActive )[3];
            }

            // Se procede a generar un array con todos los ultimos octetos del rango de ips 
            $arrayIpTodas = array();
            for ($x = $ipInicial; $x <= $ipFinal; $x++) 
            {
                $arrayIpTodas[] = $x;
            }
            
            // Se calcula la diferencia antre ambos arreglos, dando como resultado las ips disponibles
            $resultado = array_diff($arrayIpTodas, $arrayIpActivas );
            natsort($resultado); 
            
            // Se verifica que exista ip disponibles para la subred
            if(count($resultado)>0)
            {
                $strIpDisponible = $subredIp.array_values($resultado)[0];
            }
            else
            {
                $strIpDisponible = 'NoDisponible';
            }
        }
        else
        {
            $strIpDisponible = $objSubred->getIpInicial();
        }
        return $strIpDisponible;
    }
    
    /**
     * getClientePorIp
     * Función que obtiene que usuario corresponde la ip enviada
     *  
     * @author Néstor Naula <nnaulal@telconet.ec>
     * @version 1.0 20-02-2018
     * 
     * @param array $arrayParametros
     * [
     *     strIp            => Ip pública del cliente
     *     strEstadoIp      => Estado de la IP
     *     strEstadoIpClie  => Estado del cliente
     * ]
     * 
     * @return array $arrayResultado [
     *     intIdPersonaRol          => Id persona empresa rol de la persona
     *     srtDescripcionRol        => Descipción del rol de la persona
     *     strLogin                 => Login de la persona
     *     intCodEmpresa            => Código de la empresa de la persona
     *     strRegion                => Región de la persona
     *     intIdPunto               => Id del Punto de la persona
     *     intIdServicio            => Id del Servico de la persona
     *     strIdentificacion        => Cédula de la persona
     *     strTipoIdentificacion    => Tipo de identificación de la persona
     *     strNombres               => Nombres completos de la persona]
     * 
     * costoQuery: 18
     * 
     */
    public function getClientePorIp($arrayParametros)
    {
        $objRsm             = new ResultSetMappingBuilder($this->_em);
        $objQuery           = $this->_em->createNativeQuery(null, $objRsm);
        
        $strIp              = $arrayParametros['strIp'];
        $strEstadoIp        = $arrayParametros['strEstadoIp'];
        $strEstadoIpClie    = $arrayParametros['strEstadoIpClie'];
 
        try
        {
            $strSql = " SELECT IPER.ID_PERSONA_ROL,AR.DESCRIPCION_ROL,IPO.LOGIN,IEG.COD_EMPRESA,ACO.REGION,IPO.ID_PUNTO,
                                ISE.ID_SERVICIO,IPE.IDENTIFICACION_CLIENTE,IPE.TIPO_IDENTIFICACION,
                                (CASE  WHEN IPE.NOMBRES IS NOT NULL THEN IPE.NOMBRES||' '||IPE.APELLIDOS ELSE IPE.RAZON_SOCIAL END) AS NOMBREUSUARIO
                        FROM DB_INFRAESTRUCTURA.INFO_IP IP 
                        INNER JOIN DB_COMERCIAL.INFO_SERVICIO ISE ON IP.SERVICIO_ID=ISE.ID_SERVICIO
                        INNER JOIN DB_COMERCIAL.INFO_PUNTO IPO ON IPO.ID_PUNTO=ISE.PUNTO_ID
                        INNER JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPER ON IPER.ID_PERSONA_ROL=IPO.PERSONA_EMPRESA_ROL_ID 
                        INNER JOIN DB_COMERCIAL.INFO_EMPRESA_ROL IER ON IER.ID_EMPRESA_ROL=IPER.EMPRESA_ROL_ID
                        INNER JOIN DB_GENERAL.ADMI_ROL AR ON AR.ID_ROL=IER.ROL_ID
                        INNER JOIN DB_COMERCIAL.INFO_PERSONA IPE ON IPE.ID_PERSONA=IPER.PERSONA_ID
                        INNER JOIN DB_COMERCIAL.INFO_EMPRESA_GRUPO IEG ON IEG.COD_EMPRESA=IER.EMPRESA_COD
                        INNER JOIN DB_COMERCIAL.INFO_OFICINA_GRUPO IOG ON IOG.ID_OFICINA=IPER.OFICINA_ID
                        INNER JOIN DB_GENERAL.ADMI_CANTON ACO ON ACO.ID_CANTON=IOG.CANTON_ID
                        WHERE IP.IP=:strIp AND IP.ESTADO IN (:strEstadoIp) AND IPO.ESTADO NOT IN (:strEstado)
                                AND IPER.ESTADO NOT IN (:strEstado)
                        ORDER BY IP.IP desc ";

            $objQuery->setSQL($strSql);
            $objQuery->setParameter('strIp', $strIp);
            $objQuery->setParameter('strEstado',$strEstadoIpClie);
            $objQuery->setParameter('strEstadoIp',$strEstadoIp);
            
            $objRsm->addScalarResult('ID_PERSONA_ROL'           ,'intIdPersonaRol'      ,'string');
            $objRsm->addScalarResult('DESCRIPCION_ROL'          ,'srtDescripcionRol'    ,'string');
            $objRsm->addScalarResult('LOGIN'                    ,'strLogin'             ,'string');
            $objRsm->addScalarResult('COD_EMPRESA'              ,'intCodEmpresa'        ,'string');
            $objRsm->addScalarResult('REGION'                   ,'strRegion'            ,'string');
            $objRsm->addScalarResult('ID_PUNTO'                 ,'intIdPunto'           ,'string');
            $objRsm->addScalarResult('ID_SERVICIO'              ,'intIdServicio'        ,'string');
            $objRsm->addScalarResult('IDENTIFICACION_CLIENTE'   ,'strIdentificacion'    ,'string');
            $objRsm->addScalarResult('TIPO_IDENTIFICACION'      ,'strTipoIdentificacion','string');
            $objRsm->addScalarResult('NOMBREUSUARIO'            ,'strNombres'           ,'string');
            
            $arrayResultado = $objQuery->getOneOrNullResult();
                
        }
        catch(\Exception $e)
        {
            $strRespuesta   = " Error al obtener el cliente por su IP. Favor Notificar a Sistemas";
            $arrayResultado = array ('strMensaje'           =>$strRespuesta);

            return $arrayResultado;
        }
        return $arrayResultado;
    }
    
    /**
     * getClientePorLogin
     * Función que obtiene el usuario mediante el Login
     * 
     * @author Néstor Naula <nnaulal@telconet.ec>
     * @version 1.0 20-02-2018
     * 
     * @param array $arrayParametros
     * [
     *     strlogin       => Login de la persona
     *     strEstado      => Estado de la persona y punto
     * ]
     * 
     * @return array $arrayResultado [
     *     intIdPersonaRol          => Id persona empresa rol de la persona
     *     srtDescripcionRol        => Descipción del rol de la persona
     *     strLogin                 => Login de la persona
     *     intCodEmpresa            => Código de la empresa de la persona
     *     strRegion                => Región de la persona
     *     intIdPunto               => Id del Punto de la persona
     *     intIdServicio            => Id del Servico de la persona
     *     strIdentificacion        => Cédula de la persona
     *     strTipoIdentificacion    => Tipo de identificación de la persona
     *     strNombres               => Nombres completos de la persona]
     * 
     * costoQuery: 17
     * 
     */
    public function getClientePorLogin($arrayParametros)
    {
        $objRsm             = new ResultSetMappingBuilder($this->_em);
        $objQuery           = $this->_em->createNativeQuery(null, $objRsm);
        
        $strLogin           = $arrayParametros['strlogin'];
        $strEstadoIpClie    = $arrayParametros['strEstado'];
 
        try
        {
            $strSql = " SELECT IPER.ID_PERSONA_ROL,AR.DESCRIPCION_ROL,IPO.LOGIN,IEG.COD_EMPRESA,ACO.REGION,IPO.ID_PUNTO,
                                ISE.ID_SERVICIO,IPE.IDENTIFICACION_CLIENTE,IPE.TIPO_IDENTIFICACION,
                                (CASE  WHEN IPE.NOMBRES IS NOT NULL THEN IPE.NOMBRES||' '||IPE.APELLIDOS ELSE IPE.RAZON_SOCIAL END) AS NOMBREUSUARIO
                        FROM DB_COMERCIAL.INFO_PUNTO IPO
                        INNER JOIN DB_COMERCIAL.INFO_SERVICIO ISE ON IPO.ID_PUNTO=ISE.PUNTO_ID
                        INNER JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPER ON IPER.ID_PERSONA_ROL=IPO.PERSONA_EMPRESA_ROL_ID 
                        INNER JOIN DB_COMERCIAL.INFO_EMPRESA_ROL IER ON IER.ID_EMPRESA_ROL=IPER.EMPRESA_ROL_ID
                        INNER JOIN DB_COMERCIAL.INFO_PERSONA IPE ON IPER.PERSONA_ID=IPE.ID_PERSONA
                        INNER JOIN DB_GENERAL.ADMI_ROL AR ON AR.ID_ROL=IER.ROL_ID
                        INNER JOIN DB_COMERCIAL.INFO_EMPRESA_GRUPO IEG ON IEG.COD_EMPRESA=IER.EMPRESA_COD
                        INNER JOIN DB_COMERCIAL.INFO_OFICINA_GRUPO IOG ON IOG.ID_OFICINA=IPER.OFICINA_ID
                        INNER JOIN DB_GENERAL.ADMI_CANTON ACO ON ACO.ID_CANTON=IOG.CANTON_ID
                        WHERE IPO.LOGIN=:Lv_login AND IPO.ESTADO NOT IN (:strEstado)
                              AND IPER.ESTADO NOT IN (:strEstado) ";
            
            $objQuery->setSQL($strSql);
            $objQuery->setParameter('Lv_login', $strLogin);
            $objQuery->setParameter('strEstado',$strEstadoIpClie);
            
            $objRsm->addScalarResult('ID_PERSONA_ROL'           ,'intIdPersonaRol'      ,'string');
            $objRsm->addScalarResult('DESCRIPCION_ROL'          ,'srtDescripcionRol'    ,'string');
            $objRsm->addScalarResult('LOGIN'                    ,'strLogin'             ,'string');
            $objRsm->addScalarResult('COD_EMPRESA'              ,'intCodEmpresa'        ,'string');
            $objRsm->addScalarResult('REGION'                   ,'strRegion'            ,'string');
            $objRsm->addScalarResult('ID_PUNTO'                 ,'intIdPunto'           ,'string');
            $objRsm->addScalarResult('ID_SERVICIO'              ,'intIdServicio'        ,'string');
            $objRsm->addScalarResult('IDENTIFICACION_CLIENTE'   ,'strIdentificacion'    ,'string');
            $objRsm->addScalarResult('TIPO_IDENTIFICACION'      ,'strTipoIdentificacion','string');
            $objRsm->addScalarResult('NOMBREUSUARIO'            ,'strNombres'           ,'string');
            
            $arrayResultado = $objQuery->getOneOrNullResult();
                
        }
        catch(\Exception $e)
        {
            $strRespuesta   = " Error al obtener el cliente por su login. Favor Notificar a Sistemas";
            $arrayResultado = array ('strMensaje'           =>$strRespuesta);
            
            return $arrayResultado;
        }
        return $arrayResultado;
    }

       /**
    * getIpViejasPorServicio
    * Funcion que se encarga de obtener ips por id de servicio
    *
    * @author Creado: Manuel Carpio <mcarpio@telconet.ec>
    * @version 1.0 2-10-2022
    */
    public function getIpViejasPorServicio($arrayParametros)
    {

        $objSubRedId    = "";
        $strSubRedId    = "";
        $strRespuesta   = "";
        $arrayResultado = array();
        $arrayIp        = array();

        $strIdServicio = $arrayParametros['idServicio'];
  
        try
        {
            // Se obtiene toas las ips activas para la subred
            $arrayIp = $this->_em->getRepository('schemaBundle:InfoIp')
            ->createQueryBuilder('p')
            ->where('p.servicioId = :idServicio')
            ->andWhere("p.estado = :estadoActivo")
            ->setParameter('idServicio', $strIdServicio)
            ->setParameter('estadoActivo','Activo')
            ->getQuery()
            ->getResult();

            if(count($arrayIp) > 0 && is_array($arrayIp))
            {
                $objSubRedId = $arrayIp[0];
                $strSubRedId = $objSubRedId->getSubredId();

                // Se obtiene los datos de la subred
                $objSubred = $this->_em->getRepository('schemaBundle:InfoSubred')->find($strSubRedId);

                if(is_object($objSubred))
                {
                    $strRespuesta   = " Consulta realizada con exito";
                    $arrayResultado = array ('strMensaje' =>$strRespuesta, 'objSubred' =>$objSubred);
                }
            } 
        }
        catch(\Exception $e)
        {
            $strRespuesta   = " Error al obtener la ip por su servicio_id. Favor Notificar a Sistemas";
            $arrayResultado = array ('strMensaje'           =>$strRespuesta);

            return $arrayResultado;
        }
        return $arrayResultado;
    }
    
    /**
     * Funcion que sirve para obtener el tipo de ip por servicio
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.0 11-10-2021
     * @param array  $arrayParametros
     * @return string $strIp
    */
    public function getTipoIpServicio($arrayParametros) 
    {
        $strIp = '';
        $intIdServicio = $arrayParametros['intIdServicio'];
        $emComercial   = $arrayParametros['emComercial'];
        $emGeneral     = $arrayParametros['emGeneral'];
        
        $objServicio = $emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
        if (is_object($objServicio))
        {
            //Si el servicio que se va a trasladar tiene IP fija y el parametro de ese producto está activado para
            //aprovisionamiento con Ip Privada se debe crear la característica para IP Privada
                                                                                   
            $objParametroCabIpPrivada = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                        ->findOneBy(array('nombreParametro' => 'IP_PRIVADA_FIJA_GPON',
                                                                        'estado'            => 'Activo'));
            if (is_object($objParametroCabIpPrivada))
            {
                $arrayParDetIpPrivada = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->findBy(array('parametroId'   => $objParametroCabIpPrivada->getId(),
                                                                       'estado'        => 'Activo'));
                if (is_array($arrayParDetIpPrivada) && !empty($arrayParDetIpPrivada))
                {
                    $arrayIpPrivada = explode(",",$arrayParDetIpPrivada[0]->getValor1());
                }
            }
                    
            if(in_array($objServicio->getProductoId()->getId(),$arrayIpPrivada))
            {
                $objCaracteristicaIpPrivada = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                ->findOneBy(array( "descripcionCaracteristica" => "TIPO_ENRUTAMIENTO"));
                if(is_object($objCaracteristicaIpPrivada))
                {
                    $objProducto = $emComercial->getRepository('schemaBundle:AdmiProducto')->find($objServicio->getProductoId()->getId());

                    $objProdCaracteristicaIpPrivada = $emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                           ->findOneBy(array( "productoId"       => $objProducto->getId(), 
                                                                              "caracteristicaId" => $objCaracteristicaIpPrivada->getId()
                                                                            )
                                                                      );
                    if(is_object($objProdCaracteristicaIpPrivada))
                    {
                        $objInfoServicioProdCaractIpPrivada = $emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                   ->findOneBy(array( "servicioId"   => $objServicio->getId(), 
                                                                         "productoCaracterisiticaId" => $objProdCaracteristicaIpPrivada->getId()
                                                                          )
                                                                    );
                        if (is_object($objInfoServicioProdCaractIpPrivada))
                        {
                            $strIp = ($objInfoServicioProdCaractIpPrivada)?$objInfoServicioProdCaractIpPrivada->getValor():"";
                        }
                    }
                }
            }
        }
        return $strIp;
    }
    

    /**
     * Obtiene la Ips y subredes del servicio 
     * 
     * @author Brenyx Giraldo <agiraldo@telconet.ec>
     * @version 1.0 14-06-2022 
     * 
     * @param array   $arrayParametros
     * 
     * @return result
     **/
    public function getIpsSubredPorServicio( $arrayParametros ) 
    {
        
        $objRsm             = new ResultSetMappingBuilder($this->_em);
        $objQuery           = $this->_em->createNativeQuery(null, $objRsm);

        $intIdServicio      = (int)$arrayParametros['idServicio'];
        $strEstadoIp        = $arrayParametros['strEstadoIp'];

        
        try
        {
            $strSql = "  SELECT INFP.IP,INFP.FE_CREACION,INFR.SUBRED,INFR.MASCARA,INFR.IP_INICIAL,
            INFR.IP_FINAL,INFR.TIPO,IE.NOMBRE_ELEMENTO,APD.VALOR2
            FROM DB_INFRAESTRUCTURA.INFO_IP INFP
            INNER JOIN DB_INFRAESTRUCTURA.INFO_SUBRED INFR ON INFR.ID_SUBRED = INFP.SUBRED_ID
            INNER JOIN DB_INFRAESTRUCTURA.INFO_ELEMENTO IE ON INFR.ELEMENTO_ID = IE.ID_ELEMENTO
            INNER JOIN DB_GENERAL.ADMI_PARAMETRO_DET APD ON TO_CHAR(INFP.SERVICIO_ID) = APD.VALOR1
            WHERE INFP.ESTADO = :Lv_Estado 
            AND INFP.SERVICIO_ID = :Ln_IdServicio ";
            
            $objQuery->setSQL($strSql);
            $objQuery->setParameter('Ln_IdServicio',$intIdServicio);
            $objQuery->setParameter('Lv_Estado',$strEstadoIp);
          
            
            $objRsm->addScalarResult('IP'                          ,'strIp'               ,'string');
            $objRsm->addScalarResult('FE_CREACION'                 ,'srtFechaCreacion'    ,'string');
            $objRsm->addScalarResult('SUBRED'                      ,'strSubred'           ,'string');
            $objRsm->addScalarResult('MASCARA'                     ,'strMascara'          ,'string');
            $objRsm->addScalarResult('IP_INICIAL'                  ,'strIpInicial'        ,'string');
            $objRsm->addScalarResult('IP_FINAL'                    ,'strIpFinal'          ,'string');
            $objRsm->addScalarResult('TIPO'                        ,'strTipoIp'          ,'string');
            $objRsm->addScalarResult('NOMBRE_ELEMENTO'                ,'strNombrePe'          ,'string');
            $objRsm->addScalarResult('VALOR2'                ,'strVlan'          ,'string');
            
            $arrayResultado = $objQuery->getArrayResult();
                
        }
        catch(\Exception $e)
        {
            $strRespuesta   = " Error al obtener el cliente por su login. Favor Notificar a Sistemas";
            $arrayResultado = array ('strMensaje'           =>$strRespuesta);
            
            return $arrayResultado;
        }

        return $arrayResultado;
    }



}


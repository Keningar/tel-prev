<?php

namespace telconet\schemaBundle\Repository;

use telconet\schemaBundle\DependencyInjection\BaseRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityRepository;

class InfoCasoRepository extends BaseRepository
{
    
    /**
     * Funcion que sirve para obtener los casos, asignados al 
     * departamento y usuario. Costo=268
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 16-07-2015
     * 
     * Se quitan filtros por fecha de apertura y código de empresa del caso.
     * Costo = 22
     * @author Ronny Morán <rmoranc@telconet.ec>
     * @version 1.1 09-02-2021
     * 
     * @param int $personaId
     * @param String $empresaCod
     * @param int $idCaso
     * @return array $arrayResultado (idCaso, nombreTipoCaso, numeroCaso, tituloInicial, versionInicial, fechaApertura, usuarioCreacion,
     *                                estadoCaso, idDetalleHipotesis, idSintoma, nombreSintoma, idHipotesis, nombreHipotesis, 
     *                                departamentoId, departamentoNombre, usuarioAsignadoId, usuarioAsignadoNombre)
     */
    public function getCasosPorDepartamento($personaId, $empresaCod, $idCaso)
    {
        $rsm = new ResultSetMappingBuilder($this->_em);
        $query = $this->_em->createNativeQuery(null, $rsm);
        
        $sql = "SELECT 
                CASO.ID_CASO ID_CASO,
                TIPO_CASO.NOMBRE_TIPO_CASO NOMBRE_TIPO_CASO,
                CASO.NUMERO_CASO NUMERO_CASO,
                CASO.TITULO_INI TITULO_INICIAL, 
                CASO.VERSION_INI VERSION_INICIAL,
                CASO.FE_APERTURA FECHA_APERTURA,
                CASO.USR_CREACION USUARIO_CREACION,
                (SELECT HISTORIAL.ESTADO
                FROM DB_SOPORTE.INFO_CASO_HISTORIAL HISTORIAL
                WHERE HISTORIAL.ID_CASO_HISTORIAL =
                  (SELECT MAX(CASO_HISTORIAL.ID_CASO_HISTORIAL)
                  FROM DB_SOPORTE.INFO_CASO_HISTORIAL CASO_HISTORIAL
                  WHERE CASO_HISTORIAL.CASO_ID = CASO.ID_CASO
                  )
                ) ESTADO_CASO,
                DETALLE_HIPOTESIS.ID_DETALLE_HIPOTESIS ID_DETALLE_HIPOTESIS,
                DETALLE_HIPOTESIS.SINTOMA_ID ID_SINTOMA,
                (SELECT SINTOMA.NOMBRE_SINTOMA 
                FROM DB_SOPORTE.ADMI_SINTOMA SINTOMA 
                WHERE SINTOMA.ID_SINTOMA = DETALLE_HIPOTESIS.SINTOMA_ID 
                ) NOMBRE_SINTOMA,
                DETALLE_HIPOTESIS.HIPOTESIS_ID ID_HIPOTESIS,
                (SELECT HIPOTESIS.NOMBRE_HIPOTESIS 
                FROM DB_SOPORTE.ADMI_HIPOTESIS HIPOTESIS 
                WHERE HIPOTESIS.ID_HIPOTESIS = DETALLE_HIPOTESIS.HIPOTESIS_ID
                ) NOMBRE_HIPOTESIS,
                CASO_ASIGNACION.ASIGNADO_ID DEPARTAMENTO_ID,
                CASO_ASIGNACION.ASIGNADO_NOMBRE DEPARTAMENTO_NOMBRE,
                CASO_ASIGNACION.REF_ASIGNADO_ID USUARIO_ASIGNADO_ID,
                CASO_ASIGNACION.REF_ASIGNADO_NOMBRE USUARIO_ASIGNADO_NOMBRE
                FROM DB_SOPORTE.INFO_CASO CASO,
                DB_SOPORTE.INFO_DETALLE_HIPOTESIS DETALLE_HIPOTESIS,
                DB_SOPORTE.INFO_CASO_ASIGNACION CASO_ASIGNACION,
                DB_SOPORTE.ADMI_TIPO_CASO TIPO_CASO,
                DB_SOPORTE.INFO_DETALLE DETALLE,
                DB_SOPORTE.INFO_DETALLE_ASIGNACION DETALLE_ASIGNACION";
        
        $sqlWhere = "WHERE CASO.ID_CASO     = DETALLE_HIPOTESIS.CASO_ID
              AND CASO.TIPO_CASO_ID         = TIPO_CASO.ID_TIPO_CASO
              AND DETALLE_HIPOTESIS.ID_DETALLE_HIPOTESIS = CASO_ASIGNACION.DETALLE_HIPOTESIS_ID
              AND DETALLE.DETALLE_HIPOTESIS_ID = DETALLE_HIPOTESIS.ID_DETALLE_HIPOTESIS
              AND DETALLE.ID_DETALLE = DETALLE_ASIGNACION.DETALLE_ID
              AND DETALLE_ASIGNACION.REF_ASIGNADO_ID     = :personaId ";
        $sql = $sql . " " . $sqlWhere;
              
        if($idCaso != 0)
        {
            $sql .= "AND CASO.ID_CASO = :idCaso";
        }
        
        $sqlOrder = "ORDER BY CASO.ID_CASO DESC";
        $sql = $sql . " " . $sqlOrder;
        
        $rsm->addScalarResult('ID_CASO',                'idCaso',               'integer');
        $rsm->addScalarResult('NOMBRE_TIPO_CASO',       'nombreTipoCaso',       'string');
        $rsm->addScalarResult('NUMERO_CASO',            'numeroCaso',           'string');
        $rsm->addScalarResult('TITULO_INICIAL',         'tituloInicial',        'string');
        $rsm->addScalarResult('VERSION_INICIAL',        'versionInicial',       'string');
        $rsm->addScalarResult('FECHA_APERTURA',         'fechaApertura',        'string');
        $rsm->addScalarResult('USUARIO_CREACION',       'usuarioCreacion',      'string');
        $rsm->addScalarResult('ESTADO_CASO',            'estadoCaso',           'string');
        $rsm->addScalarResult('ID_DETALLE_HIPOTESIS',   'idDetalleHipotesis',   'integer');
        $rsm->addScalarResult('ID_SINTOMA',             'idSintoma',            'integer');
        $rsm->addScalarResult('NOMBRE_SINTOMA',         'nombreSintoma',        'string');
        $rsm->addScalarResult('ID_HIPOTESIS',           'idHipotesis',          'integer');
        $rsm->addScalarResult('NOMBRE_HIPOTESIS',       'nombreHipotesis',      'string');
        $rsm->addScalarResult('DEPARTAMENTO_ID',        'departamentoId',       'integer');
        $rsm->addScalarResult('DEPARTAMENTO_NOMBRE',    'departamentoNombre',   'string');
        $rsm->addScalarResult('USUARIO_ASIGNADO_ID',    'usuarioAsignadoId',    'integer');
        $rsm->addScalarResult('USUARIO_ASIGNADO_NOMBRE','usuarioAsignadoNombre','string');
        if($idCaso != 0)
        {
            $query->setParameter("idCaso",      $idCaso);
        }
        
        $query->setParameter("personaId",       $personaId);
        $query->setParameter("empresaCod",      $empresaCod); 
        
        $query->setSQL($sql);
        $arrayResultado = $query->getResult();

        return $arrayResultado;
    }
    
    /**
     * Funcion que sirve para obtener los casos, asignados al 
     * departamento y usuario. Costo=268
     * 
     * @author Wilmer Vera <wvera@telconet.ec>
     * @version 1.0 20-07-2020
     * 
     * Se le elimina estado del elemento, dado que es necesario
     * enviar el nombre sin validación de estado.
     * 
     * @author Wilmer Vera <wvera@telconet.ec>
     * @version 1.1 14-01-2021  
     * @param int $intIdServicio
     * @return array $arrayResultado (NOMBRE_ELEMENTO)
     */
    public function getSwitchPorIdServicio($intIdServicio)
    {
        $objRsm    = new ResultSetMappingBuilder($this->_em);
        $objQuery  = $this->_em->createNativeQuery(null, $objRsm);
        
        $strSql    = "SELECT NOMBRE_ELEMENTO 
                    FROM DB_SOPORTE.INFO_ELEMENTO 
                    WHERE ID_ELEMENTO = 
                        (SELECT ELEMENTO_ID 
                                FROM DB_SOPORTE.INFO_SERVICIO_TECNICO 
                                WHERE SERVICIO_ID  = :idServicio AND ROWNUM <= 1 
                                )" ;
        
      
        $objRsm->addScalarResult('NOMBRE_ELEMENTO','nombreElemento','string');
        
        $objQuery->setParameter("idServicio",       $intIdServicio);
        
        $objQuery->setSQL($strSql);
        $arrayResultado = $objQuery->getResult();

        return $arrayResultado;
    }
    /**
     * Funcion que sirve para obtener el vrf de un servicio 
     * 
     * @author Wilmer Vera <wvera@telconet.ec>
     * @version 1.0 20-07-2020
     * @param int $intIdServicio
     * @return array $arrayResultado (Valor)
     */
    public function getVrfPorIdServicio($intIdServicio)
    {
        $objRsm = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsm);
        
        $strSql = "SELECT VALOR FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC where Id_Persona_Empresa_Rol_Caract 
        IN (SELECT ISPC.VALOR
            FROM DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT ISPC,DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA APC,DB_COMERCIAL.ADMI_CARACTERISTICA AC
            WHERE SERVICIO_ID = :idServicio
            AND ISPC.PRODUCTO_CARACTERISITICA_ID = APC.ID_PRODUCTO_CARACTERISITICA 
            AND APC.CARACTERISTICA_ID = AC.ID_CARACTERISTICA 
            AND AC.DESCRIPCION_CARACTERISTICA = 'VRF'
            AND ISPC.ESTADO = 'Activo') ";
        
        $objRsm->addScalarResult('VALOR',   'valor',   'string');
      
        $objQuery->setParameter("idServicio",  $intIdServicio);
        
        $objQuery->setSQL($strSql);
        $arrayResultado = $objQuery->getResult();

        return $arrayResultado;
    }
    
            
    /**
    * 
    * getEventos
    * obtiene los casos del cliente
    * 
    * @param array $arrayParametros      
    * 
    * @return json $array
    *
    * @author John Vera <javera@telconet.ec>
    * @version 1.0 18-12-2017
    *
    */  
    
    public function getCasosPorPunto($arrayParametros)
    {

        $objRsm = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsm);
       
        $strSql = " SELECT C.NUMERO_CASO,
                    C.TITULO_INI,
                    C.VERSION_INI,
                    C.VERSION_FIN,
                    C.FE_APERTURA,
                    C.FE_CIERRE,
                    C.ID_CASO,
                    (SELECT NOMBRE_TIPO_CASO FROM ADMI_TIPO_CASO WHERE ID_TIPO_CASO = C.TIPO_CASO_ID) TIPO_CASO,
                    C.USR_CREACION
                   FROM INFO_PARTE_AFECTADA PA,
                    INFO_DETALLE D,
                    INFO_DETALLE_HIPOTESIS DH,
                    INFO_CASO C
                  WHERE PA.TIPO_AFECTADO        = :tipoAfectado
                    AND PA.afectado_id          = :puntoId
                    AND DH.ID_DETALLE_HIPOTESIS = D.DETALLE_HIPOTESIS_ID
                    AND C.ID_CASO               = DH.CASO_ID
                    AND PA.DETALLE_ID           = D.ID_DETALLE ";        
        
        $objQuery->setParameter("tipoAfectado", "Cliente");
        $objQuery->setParameter("puntoId", $arrayParametros['intPuntoId']);        
        
        $objRsm->addScalarResult('NUMERO_CASO', 'numeroCaso', 'string');
        $objRsm->addScalarResult('TITULO_INI', 'tituloIni', 'string');
        $objRsm->addScalarResult('VERSION_INI', 'versionIni', 'string');
        $objRsm->addScalarResult('VERSION_FIN', 'versionFin', 'string');
        $objRsm->addScalarResult('FE_APERTURA', 'feApertura', 'string');
        $objRsm->addScalarResult('FE_CIERRE', 'feCierre', 'string');
        $objRsm->addScalarResult('ID_CASO', 'idCaso', 'string');
        $objRsm->addScalarResult('TIPO_CASO', 'tipoCaso', 'string');
        $objRsm->addScalarResult('USR_CREACION', 'usrCreacion', 'string');
        
        $objQuery->setSQL($strSql);

        return $objQuery->getResult();

    }   

   

    /**
     * getUltimosCasosLogin funcion que sirve para obtener los ultimos 3 casos que tenga un login
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 26-04-2016
     *
     * @param String $login
     * @param String $empresaCod
     *
     * @return array $arrayResultado
     *
     */
    public function getUltimosCasosLogin($login,$empresaCod)
    {
        $rsm = new ResultSetMappingBuilder($this->_em);
        $query = $this->_em->createNativeQuery(null, $rsm);

        $sql = " select LISTAGG(A.casos, ' | ') WITHIN GROUP (ORDER BY A.fe_creacion DESC) LISTADO_CASOS
                from (
                SELECT 'Numero Caso: '||infoCaso.numero_caso||' - Version Final: '||infoCaso.version_fin as casos,infoCaso.fe_creacion
                FROM DB_SOPORTE.info_caso infoCaso
                WHERE infoCaso.id_caso IN
                  ( SELECT DISTINCT(infoDetalleHipotesis.caso_id)
                  FROM DB_SOPORTE.info_detalle_hipotesis infoDetalleHipotesis
                  WHERE infoDetalleHipotesis.ID_DETALLE_HIPOTESIS IN
                    (SELECT infoDetalle.detalle_hipotesis_id
                    FROM DB_SOPORTE.INFO_DETALLE infoDetalle
                    WHERE infoDetalle.id_detalle IN
                      (SELECT infoParteAfectada.detalle_id
                      FROM DB_SOPORTE.INFO_PARTE_AFECTADA infoParteAfectada
                      WHERE infoParteAfectada.AFECTADO_ID IN
                        (SELECT infoPunto.id_punto
                        FROM DB_COMERCIAL.info_punto infoPunto
                        WHERE infoPunto.login = :login
                          and infoPunto.estado = :estado
                        )
                      )
                    )
                  )
                  and infoCaso.empresa_cod = :empresaCod
                  order by infoCaso.fe_creacion desc) A
                  where rownum < 4 ";

        $rsm->addScalarResult('LISTADO_CASOS', 'listadoCasos', 'string');

        $query->setParameter("login", $login);
        $query->setParameter("estado", 'Activo');
        $query->setParameter("empresaCod", $empresaCod);
        $query->setParameter("limite", "4");

        $query->setSQL($sql);
        $arrayResultado = $query->getResult();

        return $arrayResultado;
    }

    /**
     * getTipoDeEnlacesPorPunto - Funcion que obtiene los tipos de enlaces que se vieron afectados por caso
     *                            Costo del Query: 21
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 16-10-2016
     *
     * @param integer $intPunto  ID del punto del cual se necesita saber el tipo de enlace o enlaces que este
     *                           tiene relacionado
     *
     * @return String $strTipoDeEnlaces  Se retorna el tipo de enlace: PRINCIPAL, BACKUP o ambos.
     *
     */
    public function getTipoDeEnlacesPorPunto($intPunto)
    {
        $objRsmb  = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsmb);

        $strSql = " SELECT LISTAGG(A.TIPO_ENLACE, ';') WITHIN GROUP (ORDER BY A.FE_CREACION DESC) TIPO_ENLACES
                    FROM (SELECT PROD.DESCRIPCION_PRODUCTO,
                          PROD.ID_PRODUCTO,
                          ISR.ID_SERVICIO,
                          IST.TIPO_ENLACE,
                          ISR.FE_CREACION
                        FROM DB_SOPORTE.INFO_SERVICIO ISR,
                          DB_SOPORTE.INFO_PLAN_CAB IPC,
                          DB_SOPORTE.INFO_PLAN_DET IPD,
                          DB_SOPORTE.ADMI_PRODUCTO PROD,
                          DB_SOPORTE.INFO_SERVICIO_TECNICO IST
                        WHERE ISR.PLAN_ID   = IPC.ID_PLAN
                        AND IPC.ID_PLAN     = IPD.PLAN_ID
                        AND IPD.PRODUCTO_ID = PROD.ID_PRODUCTO
                        AND ISR.ID_SERVICIO = IST.SERVICIO_ID
                        AND PROD.ESTADO     = :paramEstado
                        AND ISR.PUNTO_ID    = :paramPunto
                      UNION
                      SELECT PROD.DESCRIPCION_PRODUCTO,
                        PROD.ID_PRODUCTO,
                        ISR.ID_SERVICIO,
                        IST.TIPO_ENLACE,
                        ISR.FE_CREACION
                      FROM DB_SOPORTE.INFO_SERVICIO ISR,
                          DB_SOPORTE.ADMI_PRODUCTO PROD,
                          DB_SOPORTE.INFO_SERVICIO_TECNICO IST
                      WHERE ISR.PRODUCTO_ID = PROD.ID_PRODUCTO
                      AND ISR.ID_SERVICIO   = IST.SERVICIO_ID
                      AND PROD.ESTADO       = :paramEstado
                      AND ISR.PUNTO_ID      = :paramPunto) A ";

        $objRsmb->addScalarResult('TIPO_ENLACES', 'tipoEnlaces', 'string');
        $objQuery->setParameter("paramEstado", "Activo");
        $objQuery->setParameter("paramPunto", $intPunto);

        $objQuery->setSQL($strSql);
        $strTipoDeEnlaces = $objQuery->getSingleScalarResult();

        return $strTipoDeEnlaces;
    }

     /**
     * getTipoEnlacesPorCasoAfectadoElementos - Funcion que obtiene los tipos de enlaces que se vieron afectados en un tipo
     *                                          de caso Backbone
     *                                          Costo del Query: 15
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 16-10-2016
     *
     * @param array arrayParametros [ String  strLogin     => Login relacionado a la Razon Social del Detallado del SLA,
     *                                integer intDetalleId => Detalle id relacionado al caso encontrado por login
     *                              ]
     *
     * @return String $strTipoDeEnlaces  Se retorna el tipo de enlace: PRINCIPAL, BACKUP o ambos.
     */
    public function getTipoEnlacesPorCasoAfectadoElementos($arrayParametros)
    {
        $objRsmb   = new ResultSetMappingBuilder($this->_em);
        $objQuery  = $this->_em->createNativeQuery(null, $objRsmb);
        $strEstadoPunto  = "Activo";
        $strTipoAfectado = "Elemento";

        $strSql = " SELECT LISTAGG(A.TIPO_ENLACE, ';') WITHIN GROUP (ORDER BY A.TIPO_ENLACE DESC) TIPO_ENLACES
                        FROM (
                          SELECT DISTINCT(ist.TIPO_ENLACE)
                          FROM DB_INFRAESTRUCTURA.INFO_SERVICIO_TECNICO ist WHERE ist.SERVICIO_ID IN (
                        SELECT ise.ID_SERVICIO
                        FROM DB_COMERCIAL.INFO_SERVICIO ise
                        WHERE ise.ID_SERVICIO IN
                          (SELECT ist1.SERVICIO_ID
                          FROM DB_INFRAESTRUCTURA.INFO_SERVICIO_TECNICO ist1
                          WHERE ist1.INTERFACE_ELEMENTO_ID IN
                            (SELECT iie.ID_INTERFACE_ELEMENTO FROM DB_INFRAESTRUCTURA.INFO_INTERFACE_ELEMENTO iie
                            WHERE iie.ELEMENTO_ID IN (SELECT DISTINCT(ipa.AFECTADO_ID) FROM DB_SOPORTE.INFO_PARTE_AFECTADA ipa
                              WHERE ipa.DETALLE_ID = :paramDetalleId AND ipa.TIPO_AFECTADO =:paramTipoAfectado)
                            )
                          )
                        AND ise.PUNTO_ID =
                          (SELECT ip.ID_PUNTO
                          FROM DB_COMERCIAL.INFO_PUNTO ip
                          WHERE ip.LOGIN = :paramPunto
                          AND ip.ESTADO  = :paramEstadoPunto
                          ))) A ";

        $objRsmb->addScalarResult('TIPO_ENLACES', 'tipoEnlaces', 'string');

        $objQuery->setParameter("paramDetalleId", $arrayParametros["intDetalleId"]);
        $objQuery->setParameter("paramTipoAfectado", $strTipoAfectado);
        $objQuery->setParameter("paramPunto", $arrayParametros["strLogin"]);
        $objQuery->setParameter("paramEstadoPunto", $strEstadoPunto);

        $objQuery->setSQL($strSql);
        $strTipoDeEnlaces = $objQuery->getSingleScalarResult();

        return $strTipoDeEnlaces;
    }

     /**
     * getTipoEnlacesPorCasoAfectadoServicio - Funcion que obtiene el tipo de enlace relacionado a los servicios que se vieron
     *                                         afectados en un caso donde se agregaron afectados tipo Servicio
     *                                         Costo del Query: 10
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 16-10-2016
     *
     * @param array arrayParametros [ String  strLogin     => Login del punto al que se le esta calculando el SLA Detallado,
     *                                integer intDetalleId => Detalle_id del caso asociado al login seleccionado en el rango
     *                                                        de fechas escogido
     *                              ]
     *
     * @return String $strTipoDeEnlaces  Se retorna el tipo de enlace: PRINCIPAL, BACKUP o ambos.
     */
    public function getTipoEnlacesPorCasoAfectadoServicio($arrayParametros)
    {
        $objRsmb   = new ResultSetMappingBuilder($this->_em);
        $objQuery  = $this->_em->createNativeQuery(null, $objRsmb);
        $strEstadoPunto  = "Activo";
        $strTipoAfectado = "Servicio";

        $strSql = " SELECT LISTAGG(A.TIPO_ENLACE, ';') WITHIN GROUP (ORDER BY A.TIPO_ENLACE DESC) TIPO_ENLACES
                        FROM (
                          SELECT DISTINCT(ist.TIPO_ENLACE)
                          FROM DB_INFRAESTRUCTURA.INFO_SERVICIO_TECNICO ist WHERE ist.SERVICIO_ID IN (
                          SELECT ise.ID_SERVICIO FROM DB_COMERCIAL.INFO_SERVICIO ise WHERE ise.ID_SERVICIO IN (
                          SELECT DISTINCT(ipa.AFECTADO_ID) FROM DB_SOPORTE.INFO_PARTE_AFECTADA ipa
                              WHERE ipa.DETALLE_ID = :paramDetalleId AND ipa.TIPO_AFECTADO = :paramTipoAfectado)
                              AND ise.PUNTO_ID =
                          (SELECT ip.ID_PUNTO
                          FROM DB_COMERCIAL.INFO_PUNTO ip
                          WHERE ip.LOGIN = :paramPunto
                          AND ip.ESTADO  = :paramEstadoPunto
                          ))) A ";

        $objRsmb->addScalarResult('TIPO_ENLACES', 'tipoEnlacesAfectadosPorCaso', 'string');

        $objQuery->setParameter("paramDetalleId", $arrayParametros["intDetalleId"]);
        $objQuery->setParameter("paramTipoAfectado", $strTipoAfectado);
        $objQuery->setParameter("paramPunto", $arrayParametros["strLogin"]);
        $objQuery->setParameter("paramEstadoPunto", $strEstadoPunto);

        $objQuery->setSQL($strSql);
        $strTipoDeEnlaces = $objQuery->getSingleScalarResult();

        return $strTipoDeEnlaces;
    }

    /**
     * Funcion que sirve para obtener los detalles hipotesis por caso.
     * Costo=4
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 16-07-2015
     * @param int $idCaso
     * @return array $arrayResultado (idDetalleHipotesis, idSintoma, nombreSintoma, idHipotesis, nombreHipotesis)
     */
    public function getDetalleHipotesisPorCaso($idCaso)
    {
        $rsm = new ResultSetMappingBuilder($this->_em);
        $query = $this->_em->createNativeQuery(null, $rsm);
        
        $sql = "SELECT 
                DETALLE_HIPOTESIS.ID_DETALLE_HIPOTESIS ID_DETALLE_HIPOTESIS,
                DETALLE_HIPOTESIS.SINTOMA_ID ID_SINTOMA,
                  (SELECT SINTOMA.NOMBRE_SINTOMA
                  FROM DB_SOPORTE.ADMI_SINTOMA SINTOMA
                  WHERE SINTOMA.ID_SINTOMA = DETALLE_HIPOTESIS.SINTOMA_ID
                  ) NOMBRE_SINTOMA,
                  DETALLE_HIPOTESIS.HIPOTESIS_ID ID_HIPOTESIS,
                  (SELECT HIPOTESIS.NOMBRE_HIPOTESIS
                  FROM DB_SOPORTE.ADMI_HIPOTESIS HIPOTESIS
                  WHERE HIPOTESIS.ID_HIPOTESIS = DETALLE_HIPOTESIS.HIPOTESIS_ID
                  ) NOMBRE_HIPOTESIS
                FROM DB_SOPORTE.INFO_CASO CASO,
                DB_SOPORTE.INFO_DETALLE_HIPOTESIS DETALLE_HIPOTESIS
                WHERE CASO.ID_CASO = DETALLE_HIPOTESIS.CASO_ID
                AND CASO.ID_CASO = :idCaso";
        
        $rsm->addScalarResult('ID_DETALLE_HIPOTESIS',   'idDetalleHipotesis',   'integer');
        $rsm->addScalarResult('ID_SINTOMA',             'idSintoma',            'integer');
        $rsm->addScalarResult('NOMBRE_SINTOMA',         'nombreSintoma',        'string');
        $rsm->addScalarResult('ID_HIPOTESIS',           'idHipotesis',          'integer');
        $rsm->addScalarResult('NOMBRE_HIPOTESIS',       'nombreHipotesis',      'string');
        
        $query->setParameter("idCaso",  $idCaso);
        
        $query->setSQL($sql);
        $arrayResultado = $query->getResult();

        return $arrayResultado;
    }

    /**
     * Funcion que consulta los casos según el login del cliente
     * @param array $arrayParametros [  'strLogin'      => login de cliente,
     *                                  'strCodEmpresa' => código de la empresa,
     *                                  'strEsIsb'      => es un servicio Internet Small Business]
     *
     * @return array $data Retorna los casos.
     *
     * @author Creado: John Vera <javera@telconet.ec>
     * @version 1.0 08-08-2014
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 22-02-2018 Se agrega validación para omitir el código de la empresa cuando el servicio sea Internet Small Business
     * 
     */
    public function getCasoPorLogin($arrayParametros)
    {
        $query = $this->_em->createQuery();
        $strWhereEmpresa = "";
        if(empty($arrayParametros["strEsIsb"]))
        {
            $strWhereEmpresa .= "AND CA.empresaCod      = :empresa ";
            $query->setParameter('empresa', $arrayParametros["strCodEmpresa"]);
        }
        $dql = "SELECT CA.id idCaso,
                  CONCAT(CONCAT(CA.numeroCaso, ' '),CA.tituloIni) caso  
                FROM schemaBundle:InfoParteAfectada PA,
                  schemaBundle:InfoDetalle DE,
                  schemaBundle:InfoDetalleHipotesis DH,
                  schemaBundle:InfoCaso CA,
                  schemaBundle:InfoCasoHistorial CH
                WHERE PA.detalleId          = DE.id
                AND DE.detalleHipotesisId = DH.id
                AND CA.id              = DH.casoId
                AND CA.id              = CH.casoId
                AND CH.feCreacion      =(SELECT MAX(CH1.feCreacion)
                                           FROM schemaBundle:InfoCasoHistorial CH1
                                          WHERE CH1.casoId = CA.id)
                AND CH.estado          IN (:estado)
                ".$strWhereEmpresa."
                AND PA.tipoAfectado    =  :tipoAfectado
                AND PA.afectadoNombre  = :login ";

        $query->setParameter('estado', array('Creado','Asignado'));
        $query->setParameter('tipoAfectado', 'Cliente');
        $query->setParameter('login', $arrayParametros["strLogin"]);

        $query->setDQL($dql);

        $data = $query->getResult();

        return $data;
    
    }


    /**
     * @author Creado: Jorge Gómez <jigomez@telconet.ec>
     * @version 1.0 23-01-2023 - Funcion que obtiene número de secuencia del caso
     * @return string $strNumeroCaso retorna el numero de caso.
     */

    public function getNumeroCasoSec($objAdmiTipoCaso)
    {
        $strStatus  = '';
        $strStatus  = str_pad($strStatus, 3000, " ");
        $strMessage = '';
        $strMessage = str_pad($strMessage, 3000, " ");
        $strNumeroCaso = '';
        $strNumeroCaso = str_pad($strNumeroCaso, 3000, " ");
        $strTipoCaso = $objAdmiTipoCaso->getNombreTipoCaso();
        
        $strSql= "BEGIN  DB_SOPORTE.SPKG_CASOS_INSERT_TRANSACCION.P_OBTENER_NUMERO_CASO(
            :Pv_TipoCaso, 
            :Pv_NumeroCaso,                                    
            :Pv_Status,
            :Pv_Mensaje); 
            END;";

            $objStmt = $this->_em->getConnection()->prepare($strSql);

            $objStmt->bindParam('Pv_TipoCaso', $strTipoCaso);
            $objStmt->bindParam('Pv_NumeroCaso', $strNumeroCaso);
            $objStmt->bindParam('Pv_Status', $strStatus);
            $objStmt->bindParam('Pv_Mensaje', $strMessage);
            $objStmt->execute();

            return $strNumeroCaso;
    }
    
    /**
     * @author  Jorge Gómez <jigomez@telconet.ec>
     * @version 1.3 - Se automatiza función que inserta el número de caso. 
     * 
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 - Se realizan ajustes en la funcion para solucionar el problema de las numeraciones que se repiten
     * Metodo que sirve para determinar el numero de Caso a asignar
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 - Se realizan ajustes en la funcion para solucionar el problema de las numeraciones que se repiten
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 - Se mejora tiempo de respuesta de busqueda para asignacion de numero de Caso
     *             - Se agrega post-fijo dependiendo del tipo de Caso a la numeracion
     * @since 18-05-2016
     * 
     * @since 1.0
     *
     * @param AdmiTipoCaso $objAdmiTipoCaso
     * @return string
     */
    
    public function getNumeroCasoNext($objAdmiTipoCaso)
    {
        do
        {
            $strNumeroCaso=$this->getNumeroCasoSec($objAdmiTipoCaso);
            
        }while($this->validarNumeracionExiste($strNumeroCaso) > 0);

        
        return $strNumeroCaso;
    }
    
    public function validarNumeracionExiste($numeroCaso){
	    
	    $qb = $this->_em->createQueryBuilder();
	    $qb->select('infoCaso')->from('schemaBundle:InfoCaso','infoCaso');
	    $qb->where("infoCaso.numeroCaso = ?1");	   
	   // $qb->andWhere("infoCaso.empresaCod = ?2");	   
	    $qb->andWhere("infoCaso.feCreacion like ?2");	 
	    $qb->setParameter(1, $numeroCaso);
	  //  $qb->setParameter(2, $empresaCod);
	    $qb->setParameter(2, date('Y-m-d').' %');
    
	    $query = $qb->getQuery();
	    $rs = $query->getResult();	    	   
	    
	    return count($rs);
    
    }
    
    public function generarInfoOficinaGrupo($id,$nombre){
    
           $arr_encontrados = array();         
           
           $where = "";  
           
           if($nombre && $nombre!=''){
		   $where = "AND UPPER(ofi.nombreOficina) like UPPER(:nombre)";		    		   
	   }
       
	   $sql = "SELECT  
		   ofi.id,
		   ofi.nombreOficina
		   FROM                         					
                   schemaBundle:InfoOficinaGrupo ofi					
                   WHERE  
                   ofi.empresaId=:empresa
                   $where";
			
          $query = $this->_em->createQuery($sql); 
          
          if($nombre!='')$query->setParameter('nombre','%' . $nombre . '%');		   		   	  
          
          $query->setParameter('empresa',$id);                    
      
          $results = $query->getResult();
      
          $total=count($results);
      
	  if ($total > 0) {
	      	      
		  foreach ($results as $entidad) 
		  {	              		
		      $arr_encontrados[]=array('id_oficina' =>$entidad['id'],
						'nombre_oficina' =>$entidad['nombreOficina'],
					      );            
		  }
		  $data=json_encode($arr_encontrados);
		  $resultado= '{"total":"'.$total.'","encontrados":'.$data.'}';	  

	  }else{
	          $resultado= '{"total":"'.$total.'","encontrados":[]}';
	  } 
	    
	  return $resultado;
    
    }


    /**
    * generarJsonCasos
    *
    * Esta funcion retorna la lista de los casos en base a los parametros de entrada
    *
    * @param String  $parametros
    * @param integer $start
    * @param integer $limit
    * @param String  $session
    * @param String  $em
    * @param String  $ids
    * @param String  $emInfraestructura
    * @param String  $emGeneral
    *
    * @version 1.0
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.1 29-12-2015
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.2 27-04-2016 Se realiza ajuste para que se presente el boton de cerrar caso cuando existan tareas que esten canceladas
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.3 19-05-2016 Se realiza ajuste para que cualquier departamento pueda crear una tarea de cualquier caso, solo aplica para TN
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.4 25-05-2016 Se corrige una condicion, se cambia la validacion del estado de Cerrada a Cerrado
    *
    * @author Lizbeth Cruz <mlcruz@telconet.ec>
    * @version 1.5 22-06-2016 Se incluyen cambios para cerrar casos en TN
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.6 11-11-2016 Se realizan calculos de tiempo de casos, en base al nuevo esquema de iniciar,pausar,reanudar tareas
    *
    * @author John Vera <javera@telconet.ec>
    * @version 1.7 22-05-2018 Se agrega el informe del caso
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.8 06-06-2018 Se valida que solo el departamento responsable de la tarea de reporte ejecutivo pueda editarlo.
    *
    * @author Walther Joao Gaibor <wgaibor@telconet.ec>
    * @version 1.9 06-08-2018 Verificar si el caso fue creado desde la app móvil, se elimina línea que no permite afectar servicio si
    *                         no eres el que creo el caso, para que cualquiera del departamento pueda afectarlo en un caso.
    *
    * @author Germán Valenzuela <gvalenzuela@telconet.ec>
    * @version 2.0 22-04-2019 - En el return del método se agrega los tiempos totales del caso una vez que se encuentre cerrado
    *                           en la variable *$arr_encontrados*.
    * 
    * @author José Guamán <jaguamanp@telconet.ec>
    * @version 2.1 05-09-2022 - .Se Actualizo el objecto que enviaba el metodo getCasos por un array 
    *
    * @return array $resultado  Objeto en formato JSON
    *
    */
    public function generarJsonCasos($parametros, $start, $limit, $session, $em, $ids = null, $emInfraestructura, $emGeneral, $emComunicacion)
    {
        $arr_encontrados    = array();
        $arrayTarea         = array();
        $intIdDepartamentoTareaInforme = "";
        $strEdicionReporteEjecutivo = "N";
        $resultado          = $em->getRepository('schemaBundle:InfoCaso')->getCasos($parametros, $start, $limit, $session);

        $empresa            = $session->get('prefijoEmpresa');

        $num                = $resultado['total'];
        $rs                 = $resultado['registros'];

        $intTiempoCliente     = 0;
        $intTiempoEmpresa     = 0;
        $intTiempoIncidencia  = 0;
        $intTiempoTotalCierre = 0;

        if(isset($rs))
        {

            foreach($rs as $entidad)
            {                
                $objInfoCaso        = $this->_em->getRepository('schemaBundle:InfoCaso')->find($entidad['idCaso']);  
                $objUltimaAsignacion  = $this->getUltimaAsignacion($entidad['idCaso']);   //departamento                      

                $arrayTareasTodas                = $this->getCountTareasAbiertas($entidad['idCaso'], 'Todas');
                $arrayTareasAbiertas             = $this->getCountTareasAbiertas($entidad['idCaso'], 'Abiertas');
                $arrayTareasFinalizadasSolucion  = $this->getCountTareasAbiertas($entidad['idCaso'], 'FinalizadasSolucion');
                $arrayTareasCanceladas           = $this->getCountTareasAbiertas($entidad['idCaso'], 'Canceladas');

                $arrayUltimoEstado = $this->getUltimoEstado($entidad['idCaso']);    //estado ultimo del caso

                $objAdmiSintoma     = $this->_em->getRepository('schemaBundle:AdmiSintoma')
                                                ->findOneByNombreSintoma('Caso creado por el cliente desde la App Móvil.');
                $arrayCasoMovil     = array('idSintoma'     => $objAdmiSintoma->getId(),
                                            'idCaso'        => $entidad['idCaso']);
                $boolCasosCreadoApp = $this->isHipotesisDesdeAPPMovil($arrayCasoMovil);

                $boolFlag1 = $this->tieneHipotesisSinSintomas($entidad['idCaso']); //Bandera para saber si tiene hipotesis agregadas sin sintomas
                $boolFlag2 = $this->tieneHipotesis($entidad['idCaso']); //Bandera para saber si tiene hipotesis agregadas se puede ingresar tarea
                $boolFlag3 = $this->tieneTareas($entidad['idCaso']); //Bandera para saber si tiene tareas agregadas                               		

                $boolFlagCreador  = true;

                $boolFlagBoolAsignado = ($objUltimaAsignacion && count($objUltimaAsignacion) > 0 ? 
                                    ($objUltimaAsignacion->getAsignadoId() != '' ? true : false) : false);

                $flagAsignado = true;

                $flagAsignado = ($boolFlagBoolAsignado ? ($session->get("idDepartamento") == $objUltimaAsignacion->getAsignadoId() ? 
                                true : false) : false);

                $arrayParametrosCaso["intIdCaso"]      = $entidad['idCaso'];
                $arrayParametrosCaso["strNombreTarea"] = "Realizar Informe Ejecutivo de Incidente";

                //************************Se valida si el departamento en session es igual al ultimo departamento asignado******************
                $arrayTarea = $em->getRepository('schemaBundle:InfoDetalle')->getTareaPorCasoId($arrayParametrosCaso);

                $intIdDepartamentoTareaInforme = "";
                $strEdicionReporteEjecutivo    = "N";
                foreach($arrayTarea as $arrayIdxTarea)
                {
                    if($arrayIdxTarea["tipoAsignado"] == "CUADRILLA")
                    {
                        $objInfoPersonaEmpresaRol = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                       ->find($arrayIdxTarea["personaEmpresaRolId"]);

                        if(is_object($objInfoPersonaEmpresaRol))
                        {
                            $intIdDepartamentoTareaInforme = $objInfoPersonaEmpresaRol->getDepartamentoId();
                        }
                    }
                    else
                    {
                        $intIdDepartamentoTareaInforme = $arrayIdxTarea["asignadoId"];
                    }
                }

                if($session->get("idDepartamento") == $intIdDepartamentoTareaInforme)
                {
                    $strEdicionReporteEjecutivo = "S";
                }
                //************************Se valida si el departamento en session es igual al ultimo departamento asignado******************

                //Si es true, el departamento en session es igual al ultimo departamento asignado

                $boolFlagTareasTodas    = ($arrayTareasTodas > 0 ? true : false);
                $boolFlagTareasAbiertas = ($arrayTareasAbiertas > 0 ? false : true);
                $flagCerrarCasoTN   = true; 
                
                $boolFlagTareasSolucionadas = ($arrayTareasFinalizadasSolucion > 0 ? false : true);
                //Si existen mas de una tarea abierta		***************************************************										
                if(count($this->getTareasSinSolucion($entidad['idCaso'])) > 0)
                {
                    $boolFlagTareasSolucionadas = true;
                    $flagCerrarCasoTN       = false;
                }

                if($boolFlagBoolAsignado && $flagAsignado)
                {
                    $esDepartamento = true;
                }else
                {
                    $esDepartamento = false;
                }
                $flagVistaSintomas = true;
                $flagVistaHipotesis = true;
                $flagVistaTareas = true;
                $flagVistaCerrar = true;
                $flagVistaCerrarTN = true;
                $flagTodasCanceladas=false;
                $strEsCasoNuevoEsquema = "N";
                
                if(!$boolFlagCreador || $boolFlagBoolAsignado)
                {
                    $flagVistaSintomas = false;
                }
                
                
                if(((!$boolFlagCreador || $boolFlagBoolAsignado) && (!$boolFlagBoolAsignado || !$flagAsignado)) || ( !$boolFlagTareasSolucionadas))
                {
                    $flagVistaHipotesis = false;
                }
                
                
                if(((!$boolFlagCreador || $boolFlagBoolAsignado) && (!$boolFlagBoolAsignado || !$flagAsignado)))
                {
                     $flagVistaTareas = false;
                }
                
                if(((!$boolFlagCreador || $boolFlagBoolAsignado) && (!$boolFlagBoolAsignado || !$flagAsignado)) ||
                    ($boolFlagTareasTodas && (!$boolFlagTareasTodas || !$boolFlagTareasAbiertas || $boolFlagTareasSolucionadas)))
                {
                    $flagVistaCerrar = false;
                }
                
        
                if(((!$boolFlagCreador || $boolFlagBoolAsignado) && (!$boolFlagBoolAsignado || !$flagAsignado)) ||
                    ($boolFlagTareasTodas && (!$boolFlagTareasTodas || !$boolFlagTareasAbiertas || !$flagCerrarCasoTN)))
                {
                    $flagVistaCerrarTN = false;
                }
                        
                $nombreOficina = "";
                $nombreEmpresa = "";
                $nombresAsignadoPor = "";
                $feAsignacion = '';

                if($objUltimaAsignacion)
                {
                    if($objUltimaAsignacion->getPersonaEmpresaRolId())
                    {

                        $InfoPersonaEmpresaRol = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                    ->findOneById($objUltimaAsignacion->getPersonaEmpresaRolId());

                        if($InfoPersonaEmpresaRol && count($InfoPersonaEmpresaRol) > 0)
                        {
                            $oficinaEntity = $InfoPersonaEmpresaRol->getOficinaId();
                            $empresaEntity = $oficinaEntity->getEmpresaId();



                            $nombreOficina = ($oficinaEntity ? ($oficinaEntity->getNombreOficina() ? 
                                                                $oficinaEntity->getNombreOficina() : "") : "");
                            $nombreEmpresa = ($empresaEntity ? ($empresaEntity->getNombreEmpresa() ? 
                                                                $empresaEntity->getNombreEmpresa() : "") : "");
                        }
                    }

                    $feAsignacion = $objUltimaAsignacion->getFeCreacion() ? date_format($objUltimaAsignacion->getFeCreacion(), "d-m-Y G:i") : "";
                    $strUsrAsignadoPor = $objUltimaAsignacion->getUsrCreacion() ? $objUltimaAsignacion->getUsrCreacion() : "";
                    
                    if($strUsrAsignadoPor)
                    {
                        $objEmpleado = $em->getRepository('schemaBundle:InfoPersona')->getPersonaPorLogin($strUsrAsignadoPor);
                        if($objEmpleado && count($objEmpleado) > 0)
                        {
                             $nombresAsignadoPor = $objEmpleado->__toString() ? 
                                                   $objEmpleado->__toString() : "";
                        }
                    }
                }

                $usuarioApertura = "";
                $usuarioCierre = "";
                
                if($entidad['usrCreacion'])
                {
                    $empleadoCreacion = $em->getRepository('schemaBundle:InfoPersona')
                                           ->getPersonaPorLogin($entidad['usrCreacion']);
                    if($empleadoCreacion && count($empleadoCreacion) > 0)
                    {
                        $usuarioApertura = $empleadoCreacion->__toString() ? 
                                           $empleadoCreacion->__toString() : "";
                    }
                }

                if($arrayUltimoEstado == "Cerrado")
                {
                    $objEntidadUltimoEstado = $this->getUltimoEstado($entidad['idCaso'], 'entidad');

                    if($objEntidadUltimoEstado->getUsrCreacion())
                    {
                        $empleadoCierre = $em->getRepository('schemaBundle:InfoPersona')
                                             ->getPersonaPorLogin($objEntidadUltimoEstado->getUsrCreacion());
                        if($empleadoCierre && count($empleadoCierre) > 0)
                        {
                            $usuarioCierre = $empleadoCierre->__toString() ? 
                                             $empleadoCierre->__toString() : "";
                        }
                    }
                }

                $titulo_fin = "N/A";
                if($entidad['tituloFinHip'])
                {
                    $arrayHipotesis = $this->_em->getRepository('schemaBundle:AdmiHipotesis')->findOneById($entidad['tituloFinHip']);
                    $titulo_fin = ($arrayHipotesis ? ($arrayHipotesis->getNombreHipotesis() ? $arrayHipotesis->getNombreHipotesis() : "N/A") : "N/A");
                }
                else
                {
                    $titulo_fin = ($entidad['tituloFin'] ? $entidad['tituloFin'] : "N/A");
                }

                $strContCriterios          = $this->getRegistrosCriteriosTotalXCaso($entidad['idCaso'], 'Cont');
                $strContAfectados          = $this->getRegistrosAfectadosTotalXCaso($entidad['idCaso'], '', 'Cont');
                $boolCriteriosAfectados = (($strContCriterios > 0 || $strContAfectados > 0) ? true : false);

                $fechaFinal = '';
                $horaFinal = '';
                $tiempoTotal = '';

                $objInfoCasoTiempoAsignacion = $this->getTiempoCaso($entidad['idCaso']);

                if($objInfoCasoTiempoAsignacion)
                {
                    $tiempoTotal = $objInfoCasoTiempoAsignacion[0]['tiempoTotalCasoSolucion'] . ' minutos';
                }

                if($boolFlagTareasTodas)
                { //Si el caso tiene Tareas
                    if(count($this->getTareasSinSolucion($entidad['idCaso'])) == 0)
                    { // tiene tareas cerradas
                        $strFechaFinalizacion = $this->getFechaTareaSolucion($entidad['idCaso']);

                        if($strFechaFinalizacion && $strFechaFinalizacion[0]['fecha'] != "")
                        {                           
                            $strFechaFinA = explode(" ", $strFechaFinalizacion[0]['fecha']);

                            $strFechaFin   = $strFechaFinA[0];
                            $strHoraFin    = $strFechaFinA[1];

                            $strFechaS = explode("-", $strFechaFin);
                            $strHoraS  = explode(":", $strHoraFin);

                            $strFechaFinal = $strFechaS[2] . '-' . $strFechaS[1] . '-' . $strFechaS[0];
                            $strHoraFinal  = $strHoraS[0] . ':' . $strHoraS[1];
                        }

                        if($tiempoTotal == '')
                        {
                            $tiempoTotal = 'Finalizada';
                        }
                    }
                    else
                    {
                        $tiempoTotal = 'Sin Finalizar';
                    }                        
                }
                else if($tiempoTotal == '')
                {
                    $tiempoTotal = 'Sin Finalizar';
                }
                    
                if(($arrayTareasCanceladas == $arrayTareasTodas) && $arrayTareasTodas != 0)
                {
                    $flagVistaCerrar     = true;
                    $flagTodasCanceladas = true;
                    
                    $strFechaUltima = $this->getFechaUltimaTareaFinalizada($entidad['idCaso'], 'Cancelada');                    

                    $strFechaFinA = explode(" ", $strFechaUltima[0]['fecha']);

                    $strFechaFin = $strFechaFinA[0];
                    $strHoraFin  = $strFechaFinA[1];

                    $strFechaS = explode("-", $strFechaFin);
                    $strHoraS  = explode(":", $strHoraFin);

                    $strFechaFinal = $strFechaS[2] . '-' . $strFechaS[1] . '-' . $strFechaS[0];
                    $strHoraFinal  = $strHoraS[0] . ':' . $strHoraS[1];
                }

                $elementoAfectado = '';

                $detalleHipotesis = $this->_em->getRepository('schemaBundle:InfoDetalleHipotesis')
                                              ->findByCasoId($entidad['idCaso']);

                $hipotesisIniciales = '';
                
                if($detalleHipotesis)
                {
                    foreach($detalleHipotesis as $detalle)
                    {
                        if($detalle->getHipotesisId() != null)
                        {
                            $arrayHipotesis = $this->_em->getRepository('schemaBundle:AdmiHipotesis')
                                              ->find($detalle->getHipotesisId()->getId());

                            $hipotesisIniciales .= $arrayHipotesis->getNombreHipotesis() . ', ';
                        } 
                        $intIdDetalleHipotesis = $detalle->getId();
                    }
                }             

                if(isset($intIdDetalleHipotesis) && !empty($intIdDetalleHipotesis))
                {
                    $objDetalle                     = $this->_em->getRepository('schemaBundle:InfoDetalle')
                                                                ->findOneByDetalleHipotesisId($intIdDetalleHipotesis);
                    $arrayParametrosParteAfectada   = array('detalleId'     => $objDetalle->getId(),
                                                            'tipoAfectado'  => 'Cliente');
                    $arrayParteAfectada             = $this->getClienteAfectado($arrayParametrosParteAfectada);
                }
                //Se coloca fecha Actual para que se gestione al momento de asignar tarea
                $fechaActual    = new \DateTime('now');
                $date           = $fechaActual->format('Y-m-d H:i');

                //Se Verifica si se debe presentar la accion de Cerrar Caso
                $IdPersonaEmpresaRol = $session->get('idPersonaEmpresaRol');
                $strBandCerrarCaso      = $this->getPresentarVentanaCerrarCaso($entidad['idCaso'],$em,$IdPersonaEmpresaRol);

                if($strBandCerrarCaso == "0")
                {
                   $flagVistaCerrar     = false;
                   $flagVistaCerrarTN   = false;
                }

                if($empresa == "TN"  && $arrayUltimoEstado != "Cerrado" && $arrayUltimoEstado != "Creado")
                {
                    $action4   = "button-grid-agregarTarea";
                    $action5   = "button-grid-administrarTareas";
                }
                else if($arrayUltimoEstado != "Cerrado" && ($flag2 || $flag3 ) && $flagVistaTareas && $boolFlagTareasSolucionadas)
                {
                   $action4   = "button-grid-agregarTarea";
                   $action5   = "button-grid-administrarTareas";
                }
                else
                {
                   $action4   = "icon-invisible";
                   $action5   = "icon-invisible";
                }

                $arrayParametros["intIdCaso"]   = $entidad['idCaso'];
                $arrayParametros["strTipoRepo"] = "COUNT";
                //Se realiza el calculo del tiempo de casos, en base a los nuevos botones de iniciar,pausar y reanudar tareas
                $intNumeroTareas = $this->_em->getRepository('schemaBundle:InfoDetalle')->getTiempoTotalTareasDeCasos($arrayParametros);

                if($intNumeroTareas > 0)
                {
                    $arrayParametros["intIdCaso"]   = $entidad['idCaso'];
                    $arrayParametros["strTipoRepo"] = "SUM";

                    $intTiempoTotalCaso    = $this->_em->getRepository('schemaBundle:InfoDetalle')->getTiempoTotalTareasDeCasos($arrayParametros);
                    $strEsCasoNuevoEsquema = "S";
                }
                
                $objEncuesta = $emComunicacion->getRepository('schemaBundle:InfoEncuesta')->findOneBy(array('codigo'            => $entidad['idCaso'],
                                                                                                           'descripcionEncuesta'=> 'CASO'));
                $strEstadoInforme = '';
                if(is_object($objEncuesta))
                {
                    $strEstadoInforme = $objEncuesta->getEstado();
                }

                //Obtenemos los tiempo totales del caso una vez cerrado.
                if ($arrayUltimoEstado === 'Cerrado')
                {
                    $intTiempoCliente     = $objInfoCasoTiempoAsignacion[0]['tiempoClienteAsignado'] . ' minutos';
                    $intTiempoEmpresa     = $objInfoCasoTiempoAsignacion[0]['tiempoEmpresaAsignado'] . ' minutos';
                    $intTiempoIncidencia  = $objInfoCasoTiempoAsignacion[0]['tiempoTotalCaso']       . ' minutos';
                    $intTiempoTotalCierre = $objInfoCasoTiempoAsignacion[0]['tiempoTotal']           . ' minutos';
                }

                $arr_encontrados[] = array(
                    'tiempoCliente'             => $intTiempoCliente,
                    'tiempoEmpresa'             => $intTiempoEmpresa,
                    'tiempoIncidencia'          => $intTiempoIncidencia,
                    'tiempoTotalCierre'         => $intTiempoTotalCierre,
                    'id_caso'                   => $entidad['idCaso'],
                    'numero_caso'               => $entidad['numeroCaso'],
                    'nuevo_esquema'             => $strEsCasoNuevoEsquema,
                    'tiempo_total_caso'         => $intTiempoTotalCaso,
                    'tipo_afectacion'           => $entidad['tipoAfectacion'],
                    'titulo_ini'                => ($entidad['tituloIni'] ? $entidad['tituloIni'] : "N/A"),
                    'titulo_fin'                => ($titulo_fin),
                    'version_ini'               => ($entidad['versionIni'] ? $entidad['versionIni'] : "N/A"),
                    'version_fin'               => ($entidad['versionFin'] ? $entidad['versionFin'] : "N/A"),
                    'departamento_asignado'     => ($objUltimaAsignacion ? ($objUltimaAsignacion->getAsignadoNombre() ? 
                                                    ucwords(strtolower($objUltimaAsignacion->getAsignadoNombre())) : "N/A") : "N/A"),
                    'empleado_asignado'         => ($objUltimaAsignacion ? ($objUltimaAsignacion->getRefAsignadoNombre() ? 
                                                    ucwords(strtolower($objUltimaAsignacion->getRefAsignadoNombre())) : "N/A") : "N/A"),
                    'oficina_asignada'          => ($nombreOficina ? ucwords(strtolower($nombreOficina)) : "N/A"),
                    'empresa_asignada'          => ($nombreEmpresa ? ucwords(strtolower($nombreEmpresa)) : "N/A"),
                    'asignado_por'              => ($nombresAsignadoPor ? ucwords(strtolower($nombresAsignadoPor)) : "N/A"),
                    'fecha_asignacionCaso'      => ($feAsignacion ? $feAsignacion : "N/A"),
                    'fecha_apertura'            => ($entidad['feApertura']) ? date("d-m-Y", strtotime($entidad['feApertura'])) : "N/A",
                    'hora_apertura'             => ($entidad['feApertura']) ? date("G:i", strtotime($entidad['feApertura'])) : "",
                    'fecha_cierre'              => ($entidad['feCierre']) ? date("d-m-Y", strtotime($entidad['feCierre'])) : "N/A",
                    'hora_cierre'               => ($entidad['feCierre']) ? date("G:i", strtotime($entidad['feCierre'])) : "",
                    'estado'                    => $arrayUltimoEstado,
                    'usuarioApertura'           => ($usuarioApertura ? ucwords(strtolower($usuarioApertura)) : "N/A"),
                    'usuarioCierre'             => ($usuarioCierre ? ucwords(strtolower($usuarioCierre)) : "N/A"),
                    'date'                      => $date,
                    'flag1'                     => $boolFlag1,
                    'flag2'                     => $boolFlag2,
                    'flag3'                     => $boolFlag3,
                    'flagCreador'               => $boolFlagCreador,
                    'flagBoolAsignado'          => $boolFlagBoolAsignado,
                    'flagAsignado'              => $flagAsignado,
                    'flagTareasTodasCanceladas' => $flagTodasCanceladas,
                    'flagTareasTodas'           => $boolFlagTareasTodas,
                    'flagTareasAbiertas'        => $boolFlagTareasAbiertas,
                    'flagTareasSolucionadas'    => $boolFlagTareasSolucionadas,
                    'siTareasTodas'             => ($boolFlagTareasTodas ? "Si" : "No"),
                    'siTareasAbiertas'          => ($boolFlagTareasAbiertas ? "No" : "Si"),
                    'siTareasSolucionadas'      => ($boolFlagTareasSolucionadas ? "No" : "Si"),
                    'tiempo_total'              => $tiempoTotal,
                    'edicionReporteEjecutivo'   => $strEdicionReporteEjecutivo,
                    'empresa'                   => $empresa,
                    'fechaFin'                  => $strFechaFinal,
                    'horaFin'                   => $strHoraFinal,
                    'esDepartamento'            => $esDepartamento,
                    'elementoAfectado'          => $elementoAfectado,
                    'hipotesisIniciales'        => $hipotesisIniciales != '' ? $hipotesisIniciales : "Sin Hipotesis Inicial",
                    'tipoCaso'                  => $objInfoCaso->getTipoCasoId()->getNombreTipoCaso(),
                    'action1'                   => 'button-grid-show',
                    'action2'                   => ($arrayUltimoEstado != "Cerrado") ? (($flag1) ? 'icon-invisible' : 
                                                                                    (($flagVistaSintomas && $boolFlagTareasSolucionadas) || 
                                                                                    $boolCasosCreadoApp ? 
                                                                                    'button-grid-agregarSintoma' : 
                                                                                    'icon-invisible')) : 'icon-invisible',
                    'action3'                   => ($arrayUltimoEstado != "Cerrado") ? (($flagVistaHipotesis && $boolFlagTareasSolucionadas) ||
                                                                                    $boolCasosCreadoApp ? 
                                                                                    'button-grid-agregarHipotesis' : 
                                                                                    'icon-invisible') : 'icon-invisible',
                    'action4'                   => $action4,
                    'action5'                   => $action5,
                    'action6'                   => ($arrayUltimoEstado != "Cerrado") ? 'icon-invisible' : 'icon-invisible',
                    'action7'                   => ($arrayUltimoEstado != "Cerrado") ? 
                                                    ( ( ($flagVistaCerrar && $empresa != "TN")  || ($flagVistaCerrarTN && $empresa == "TN")) ||
                                                    $boolCasosCreadoApp ? 
                                                    'button-grid-cerrarCaso' : 'icon-invisible') : 'icon-invisible',
                    'action8'                   => ($boolCriteriosAfectados ? 'button-grid-VerAfectadoCaso' : 'icon-invisible'),
                    'action9'                   => ($arrayUltimoEstado != "Cerrado") ? 'button-grid-agregarArchivoCaso' : 'icon-invisible',
                    'action10'                  => ($arrayUltimoEstado != "Cerrado") ? 'button-grid-pdf' : 'icon-invisible',
                    'action11'                  => ($empresa == "TN") ? 'button-grid-verSeguimientoTareasCaso' : 'icon-invisible',
                    'action12'                  => ($arrayUltimoEstado != "Cerrado") ? 'button-grid-afectados' : 'icon-invisible',
                    'estadoInforme'             => $strEstadoInforme,
                    'action13'                  => ($arrayUltimo_estado == "Creado") ? 'button-grid-afectados-cliente-empleado' : 'icon-invisible',
                    'idPunto'                   => isset($arrayParteAfectada[0]['afectadoId']) ? $arrayParteAfectada[0]['afectadoId'] : 0
                );
            }
            $data = json_encode($arr_encontrados);
            $resultado = '{"total":"' . $num . '","encontrados":' . $data . '}';

            return $resultado;
        }
        else
        {
            $resultado = '{"total":"0","acciones":[]}';

            return $resultado;
        }
    }

     /**
     * getJsonDocumentosCaso
     *
     * Esta funcion retorna el JSON de todos los documentos que fueron cargados en la creacion del caso
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 15-01-2016
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 30-03-2016 Se realiza ajustes por requerimiento que permite subir archivos a nivel de tareas
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 22-09-2016 Se agrega el parámetro del usuario en sesión.
     *                         Además se agrega en el json el id del documento y el correspondiente bool para verificar si el usuario puede o no
     *                         eliminar adjuntos
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec> 
     * @version 1.3 08-02-2017 Se modifica la función enviando un arreglo de parámetros para reutilizar la misma función cuando se desee obtener
     *                         los archivos asociados a una incidencia, auditoría o mantenimiento enviada desde el móvil 
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec> 
     * @version 1.4 11-11-2020 Los archivos que se encuentren en el servidor NFS remoto deben poder ser visualizados en telcos.
     *
     *
     * @param array $arrayParametros[
     *                                  "intIdCaso"             => id del caso relacionado con los archivos adjuntos
     *                                  "intIdDetalle"          => id del detalle relacionado con los archivos adjuntos 
     *                                  "strTareaIncAudMant"    => string que indica si una tarea es una incidencia, mantenimiento o auditoría,
     *                                  "strPathTelcos"         => string con el path de Telcos
     *                              ] 
     * @param entity $emInfraestructura
     * @param string $usrSession 
     *
     * @return array $objResultado
     *
     */
    public function getJsonDocumentosCaso($arrayParametros,$emInfraestructura,$usrSession)
    {
        $arrayEncontrados        = array();
        $strDatos                = $this->getDocumentosCaso($arrayParametros,$emInfraestructura);

        $intCantidad             = $strDatos['total'];
        $arrayRegistros          = $strDatos['registros'];
        if ($arrayRegistros)
        {
            foreach ($arrayRegistros as $data)
            {
                $strNFS     = substr($data["linkVerDocumento"], 0, strrpos($data["linkVerDocumento"], '/'));
                $strUrlNFS  = $data["linkVerDocumento"];
                if(strrpos($data["linkVerDocumento"], $arrayParametros["strPathTelcos"]) === false) 
                {
                    $data["linkVerDocumento"]   = $arrayParametros["strPathTelcos"]."/".$data["linkVerDocumento"];
                }

                $boolNfs = (filter_var($strNFS, FILTER_VALIDATE_URL) !== false);
                if($boolNfs)
                {
                    $data["linkVerDocumento"]   = $strUrlNFS;
                }
                $arrayEncontrados[] = array(
                                               'idDocumento'           => $data["id"],
                                               'ubicacionLogica'       => $data["ubicacionLogica"],
                                               'feCreacion'            => ($data["feCreacion"] ? 
                                                                           strval(date_format($data["feCreacion"],"d-m-Y H:i")) : ""),
                                               'linkVerDocumento'      => $data["linkVerDocumento"],
                                               'usrCreacion'           => $data["usrCreacion"],
                                               'boolEliminarDocumento' => $data["usrCreacion"]==$usrSession ? true : false);
            }

            $objData        = json_encode($arrayEncontrados);

            $objResultado   = '{"total":"'.$intCantidad.'","encontrados":'.$objData.'}';

            return $objResultado;
        }
        else
        {
            $objResultado = '{"total":"0","encontrados":[]}';
            return $objResultado;
        }
    }

     /**
     * getDocumentosCaso
     *
     * Esta funcion ejecuta el Query que retorna todos los documentos que fueron cargados en la creacion del caso
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 15-01-2016
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 30-03-2016 Se realiza ajustes por requerimiento que permite subir archivos a nivel de tareas
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 22-09-2016 Se modifica la consulta para obtener únicamente los archivos cuyo estado sea diferente a Eliminado. 
     *                         Además se agrega el id del documento 
     *                         y el usuario de creacion del documento
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec> 
     * @version 1.3 08-02-2017 Se modifica la función y la consulta enviando un arreglo de parámetros para reutilizar la misma función cuando 
     *                         se desee obtener los archivos asociados a una incidencia, auditoría o mantenimiento enviada desde el móvil 
     *  
     * @param array $arrayParametros[
     *                                  "intIdCaso"             => id del caso relacionado con los archivos adjuntos
     *                                  "intIdDetalle"          => id del detalle relacionado con los archivos adjuntos 
     *                                  "strTareaIncAudMant"    => string que indica si una tarea es una incidencia, mantenimiento o auditoría
     *                              ]
     * @param string  $emInfraestructura
     *
     * @return array $strDatos
     *
     */
    public function getDocumentosCaso($arrayParametros,$emInfraestructura)
    {

        $strQuery = $emInfraestructura->createQuery();
        $strCount = $emInfraestructura->createQuery();

        $sql = " SELECT infoDocumento.id,
                  infoDocumento.ubicacionLogicaDocumento as ubicacionLogica,infoDocumento.feCreacion as feCreacion,infoDocumento.usrCreacion,
                  infoDocumento.ubicacionFisicaDocumento as linkVerDocumento ";

        $from = " FROM
                  schemaBundle:InfoDocumento infoDocumento,schemaBundle:InfoDocumentoRelacion infoDocumentoRelacion
                 WHERE
                  infoDocumento.id = infoDocumentoRelacion.documentoId ";
        
        $where= " AND infoDocumento.estado <> :strEstadoDocumento ";
        $strQuery->setParameter("strEstadoDocumento", 'Eliminado');
        $strCount->setParameter("strEstadoDocumento", 'Eliminado');
        
        if(isset($arrayParametros["intIdCaso"]) && !empty($arrayParametros["intIdCaso"]))
        {
            $where .= " AND infoDocumentoRelacion.casoId = :varCasoId ";

            $strQuery->setParameter(":varCasoId", $arrayParametros["intIdCaso"]);
            $strCount->setParameter(":varCasoId", $arrayParametros["intIdCaso"]);
        }
        else if(isset($arrayParametros["intIdDetalle"]) && !empty($arrayParametros["intIdDetalle"]))
        {
            
            $where .= " AND (infoDocumentoRelacion.detalleId = :varDetalleId OR infoDocumentoRelacion.actividadId = :varDetalleId) ";
           
            $strQuery->setParameter(":varDetalleId", $arrayParametros["intIdDetalle"]);
            $strCount->setParameter(":varDetalleId", $arrayParametros["intIdDetalle"]);
        }
        
        $order = " ORDER BY
                  infoDocumento.feCreacion DESC ";

        $sql = $sql . $from . $where . $order;
        $strQuery->setDQL($sql);
        $strDatos['registros'] = $strQuery->getResult();
        $sqlCount = " SELECT COUNT(infoDocumento) ";
        $sqlCount = $sqlCount . $from . $where . $order;

        $strCount->setDQL($sqlCount);

        $strDatos['total'] = $strCount->getSingleScalarResult();

        return $strDatos;
    }

     /**
     * getDepartamentoPorLoginYEmpresa
     *
     * Método que obtiene el departamento  en base a un login y a una empresa
     *
     * @param $strLogin       Login del creador del caso
     * @param $strEmpresaCod  Empresa bajo la cual se creo el caso
     *
     * @return integer $intDepartamentoId
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 14-11-2017 Se coloca bloque try-catch para cuando el query no devuelva nada pueda seguir el flujo sin problema
      *                        Dado que es un show, debe permitir mostrar el resto de información del caso
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 29-09-2016 Se agrega validacion para que solo considere los info_persona_empresa_rol que
     *                         esten activos y que sean tipo de rol Empleado
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 16-09-2016
     */
    public function getDepartamentoPorLoginYEmpresa($strLogin,$strEmpresaCod)
    {
        $intDepartamentoId = 0;
        
        try
        {
            $objQuery = $this->_em->createQuery();

            $strSql   = " SELECT DISTINCT iper.departamentoId
                            FROM schemaBundle:InfoPersonaEmpresaRol iper,schemaBundle:InfoOficinaGrupo iog,
                                 schemaBundle:InfoEmpresaRol ier,schemaBundle:AdmiRol ar,schemaBundle:AdmiTipoRol atr
                            WHERE iper.oficinaId = iog.id
                            AND iper.empresaRolId = ier.id
                            AND ier.rolId = ar.id
                            AND ar.tipoRolId = atr.id
                            AND iper.personaId = (SELECT DISTINCT ip.id FROM schemaBundle:InfoPersona ip WHERE ip.login = :paramLogin
                                                  AND ip.estado = :paramEstado)
                            AND iog.empresaId = :paramEmpresaCod
                            AND iper.estado = :paramEstado
                            AND atr.descripcionTipoRol = :paramTipoRol ";

            $objQuery->setParameter('paramLogin',$strLogin);
            $objQuery->setParameter('paramEstado',"Activo");
            $objQuery->setParameter('paramEmpresaCod',$strEmpresaCod);
            $objQuery->setParameter('paramTipoRol',"Empleado");

            $objQuery->setDQL($strSql);
            $objQuery->getMaxResults(1);
            $intDepartamentoId = $objQuery->getSingleScalarResult();
        
        }
        catch(\Exception $e)
        {
            error_log('Fn: getDepartamentoPorLoginYEmpresa : '.$e->getMessage());
        }

        return $intDepartamentoId;
    }

    /**
    * getPresentarVentanaCerrarCaso
    *
    * Esta funcion retorna un bandera para determinar si se debe o no presentar la accion de Cerrar Caso, dependiendo del Rol del usuario
    * que este en session, solo aplica para tipo de caso Movilizacion
    *
    * @param integer $id_caso
    * @param String  $emComercial
    * @param integer $idPersonaEmpresaRol
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 29-12-2015
    *
    * @return String $bandera
    *
    */
    public function getPresentarVentanaCerrarCaso($id_caso,$emComercial,$idPersonaEmpresaRol)
    {
        $caso      = $this->_em->getRepository("schemaBundle:InfoCaso")->find($id_caso);
        $bandera   = "1";
        //Se obtiene parametro para verificar si se puede o no presentar la ventana para cerrar el caso
        $paramTipoCaso = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                         ->getOne('TIPO CASO FINALIZAR TAREA','','','','','','','');

        if($caso->getTipoCasoId() == $paramTipoCaso['valor1'])
        {
            $personaEmpresaRol   = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($idPersonaEmpresaRol);

            //Se consulta si el tipo de rol de Jefe
            if($personaEmpresaRol)
                $empresaRol          = $emComercial->getRepository('schemaBundle:InfoEmpresaRol')->find($personaEmpresaRol->getEmpresaRolId());
            if($empresaRol)
                $rol                 = $emComercial->getRepository('schemaBundle:AdmiRol')->find($empresaRol->getRolId());

            //Si el Tipo de Caso es Movilizacion y el rol no es jefe, se setea la bandera en false para que no se presente la
            //accion de Cerrar Caso
            if($rol->getEsJefe() == "N")
            {
                $bandera = "0";
            }
        }

        return $bandera;
    }

    public function getDescripcionAfectadoCaso($id_caso){
    
	    $sql = "SELECT e.afectadoId , e.tipoAfectado , e.afectadoNombre         
			FROM 
			schemaBundle:InfoDetalle a,
			schemaBundle:InfoDetalleHipotesis b,
			schemaBundle:InfoCaso c,			
			schemaBundle:InfoParteAfectada e 
			WHERE 
			c.id = :idCaso and
			b.casoId = c.id and
			a.detalleHipotesisId = b.id and
			a.feCreacion = (select min(f.feCreacion) from schemaBundle:InfoDetalle f, schemaBundle:InfoDetalleHipotesis g 
			where
			g.casoId = :idCaso and f.detalleHipotesisId = g.id) and			
			a.id = e.detalleId 
		      ";
		
	      $query = $this->_em->createQuery($sql);	
	      $query->setParameter('idCaso',$id_caso);
	      	      
	      $total=count($query->getResult());	
	      $datos = $query->getResult();   	      	      
    
	      return $datos;
    
    }
    
    /**
    * obtenerDatosCierreCaso
    *
    * Esta funcion retorna informacion relacionado a un caso, esta funcion se ejecuta antes de cerrar un caso
    *
    * @param object  $caso
    * @param String  $prefijoEmpresa
    * @param object  $em
    * @param boolean $es_cancelado
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.1 11-11-2016 Se realiza el calculo del tiempo de casos, en base a los nuevos botones de
    *                         iniciar,pausar y reanudar tareas
    *
    * @author Desarrollo Inicial
    * @version 1.0
    *
    * @return String $bandera
    *
    */
    public function obtenerDatosCierreCaso($caso,$prefijoEmpresa,$em,$es_cancelado=false){

	    $id_caso = $caso->getId();	    	    
	    	   	    
	    $fechaFinal = ''; $horaFinal = ''; $fechaFinalizacion = '';
	    
	    if(!$es_cancelado){
	    
		     $tareasTodas =  $this->getCountTareasAbiertas($id_caso, 'Todas'); 
	    
		    if($tareasTodas>0){
		    
			  $fechaFinalizacion = $this->getFechaTareaSolucion($id_caso);			 			 
		    
		    }
	    
	    }else{
	    
		  $tareasTodas =  $this->getCountTareasAbiertas($id_caso, 'Canceladas'); 
	    
		  if($tareasTodas>0){
			  
			  $fechaFinalizacion = $this->getFechaUltimaTareaFinalizada($id_caso,$estado = 'Cancelada');
			  			 		    
		    }	    	    	    
	    }
	    
	     if($fechaFinalizacion && $fechaFinalizacion[0]['fecha']!=""){
								  
		      $fechaFin = $fechaFinalizacion[0]['fecha'];
												      
		      $fechaFinA = explode(" ",$fechaFin);
												      
		      $fechaFin = $fechaFinA[0];
		      $horaFin  = $fechaFinA[1];
																      
		      $fechaS = explode("-",$fechaFin);
		      $horaS  = explode(":",$horaFin);
		      
		      $fechaFinal = $fechaS[2].'-'.$fechaS[1].'-'.$fechaS[0];
		      $horaFinal  = $horaS[0].':'.$horaS[1];			    			    
																	    
	    }
	    
	    $detalleHipotesis = $em->getRepository('schemaBundle:InfoDetalleHipotesis')->findByCasoId($id_caso);
				
	
	    $hipotesisIniciales='';
	    if($detalleHipotesis){
					    
		
		  foreach($detalleHipotesis as $detalle){				      				      
														      
		      if($detalle->getHipotesisId()!=null){
		      $hipotesis = $em->getRepository('schemaBundle:AdmiHipotesis')->find($detalle->getHipotesisId()->getId());					  					  
							      
		      $hipotesisIniciales .= $hipotesis->getNombreHipotesis().', ';
		      }
		      else $hipotesisIniciales='';
		    
		  
		  }
	    
	    }else $hipotesisIniciales='';
        
        /*
         * Obtener fecha y hora actual para cerrar el caso
         */
        $fechaActual = new \DateTime('now');
        $fecha       = $fechaActual->format('Y-m-d');
        $hora        = $fechaActual->format('H:i');
        $strEsCasoNuevoEsquema = "N";

        $arrayParametros["intIdCaso"]   = $id_caso;
        $arrayParametros["strTipoRepo"] = "COUNT";
        //Se realiza el calculo del tiempo de casos, en base a los nuevos botones de iniciar,pausar y reanudar tareas
        $intNumeroTareas = $em->getRepository('schemaBundle:InfoDetalle')->getTiempoTotalTareasDeCasos($arrayParametros);

        if($intNumeroTareas > 0)
        {
            $arrayParametros["intIdCaso"]   = $id_caso;
            $arrayParametros["strTipoRepo"] = "SUM";

            $intTiempoTotalCaso    = $em->getRepository('schemaBundle:InfoDetalle')->getTiempoTotalTareasDeCasos($arrayParametros);
            $strEsCasoNuevoEsquema = "S";
        }

        $arr_encontrados[] = array(
            'id_caso'        => $id_caso,
            'empresa'        => $prefijoEmpresa,
            'tiempoTotalCaso'=> $intTiempoTotalCaso,
            'nuevoEsquema'   => $strEsCasoNuevoEsquema,
            'numero_caso'    => $caso->getNumeroCaso(),
            'tipoAfectacion' => $caso->getTipoAfectacion(),
            'fecha_apertura' => ($caso->getFeApertura()) ? date_format($caso->getFeApertura(), "d-m-Y") : "N/A",
            'hora_apertura'  => ($caso->getFeApertura()) ? date_format($caso->getFeApertura(), "G:i") : "",
            'version_inicial'=> $caso->getVersionIni(),
            'tiene_tareas'   => $tareasTodas > 0 ? true : false,
            'fecha_final'    => $fechaFinal,
            'hora_final'     => $horaFinal,
            'fechaActual'    => $fecha,
            'horaActual'     => $hora,
            'hipotesisIniciales' => $hipotesisIniciales
        );

        $data = json_encode($arr_encontrados);
        $resultado = '{"encontrados":' . $data . '}';

        return $resultado;
    }
    
    /**
     * generarJsonSeguimientoXTarea
     * Costo: 7
     * Esta funcion retorna la informacion de los seguimientos de una tarea
     *
     * @author  Desarrollo Inicial
     * @version 1.0
     *
     * @author  Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 20-09-2016 Se realizan ajustes por concepto de seguimientos internos
     *
     * @author  Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 23-10-2017 Se lleva el query a SQL nativo y se convierte el campo observacion de CLOB a VARCHAR2
     *
     * @author  David De La Cruz <ddelacruz@telconet.ec>
     * @version 1.3 18-01-2022 Se valida que si el departamento de  la sesión llega nulo, no ejecute el query
     * 
     * @author  Fernando López <filopez@telconet.ec>
     * @version 1.4 10-08-2022 Se realiza modificación para corregir seguimientos con departamento 'Empresa' en la creación de tareas CSOC,
     *                         y mostrar el departamento correspondiente a la empresa del user que ingreso el seguimiento.
     * 
     * @param array $arrayParametros[ $emComercial        => conexion esquema DB_COMERCIAL,
     *                                $emGeneral          => conexion esquema DB_GENERAL,
     *                                $idDetalle          => id_detalle de la tarea a consultarle los seguimientos,
     *                                $codEmpresa         => codigo de la empresa en session,
     *                                departamentoSession => id del departamento en session
     *                              ]
     *
     * @return json $objRespuesta
     *
     */
    public function generarJsonSeguimientoXTarea($arrayParametros)
    {
        $objRsmb     = new ResultSetMappingBuilder($this->_em);
        $objQuery    = $this->_em->createNativeQuery(null,$objRsmb);
        $intTotal    = 0;
        $emComercial = $arrayParametros["emComercial"];
        $emGeneral   = $arrayParametros["emGeneral"];
        $strNombreDepartamento   = "";
        $strSeguimientosPublicos = "N"; // valor para obtener todos los seguimientos publicos
        $strSeguimientosInternos = "S"; // valor para obtener todos los seguimientos internos

        if ($arrayParametros["departamentoSession"] == null)
        {
            return '{"total":"0","acciones":[]}';
        }

        $strSql = " SELECT ITS.FE_CREACION,ITS.USR_CREACION,ITS.EMPRESA_COD,SPKG_UTILIDADES.F_GET_CLOB_TO_VARCHAR(ROWID) AS OBSERVACION
                        FROM INFO_TAREA_SEGUIMIENTO ITS
                        WHERE ITS.DETALLE_ID = :paramDetalleId
                        AND (ITS.INTERNO = :paramSeguimientosPublicos
                        OR ITS.INTERNO IS NULL
                        OR ( ITS.INTERNO = :paramSeguimientosInternos
                             AND ITS.DEPARTAMENTO_ID = :paramDepartamento ))
                        ORDER BY ITS.FE_CREACION DESC ";

        $objQuery->setParameter('paramDetalleId', $arrayParametros["idDetalle"]);
        $objQuery->setParameter('paramSeguimientosPublicos', $strSeguimientosPublicos);
        $objQuery->setParameter('paramSeguimientosInternos', $strSeguimientosInternos);
        $objQuery->setParameter('paramDepartamento', $arrayParametros["departamentoSession"]);


        $objRsmb->addScalarResult('FE_CREACION', 'feCreacion', 'datetime');
        $objRsmb->addScalarResult('USR_CREACION', 'usrCreacion', 'string');
        $objRsmb->addScalarResult('EMPRESA_COD', 'empresaCod', 'string');
        $objRsmb->addScalarResult('OBSERVACION', 'observacion', 'string');

        $objQuery->setSQL($strSql);
        $arrayDatosSeguimientos = $objQuery->getResult();

        if(isset($arrayDatosSeguimientos))
        {
            foreach($arrayDatosSeguimientos as $arrayItemSeguimiento)
            {
                $intTotal = $intTotal + 1;
                $strUsuarioCreacion = $arrayItemSeguimiento["usrCreacion"];
                $arrayDepartamentoId = array();
                $objInfoPersona = $emComercial->getRepository('schemaBundle:InfoPersona')
                                              ->findOneByLogin($arrayItemSeguimiento["usrCreacion"]);

                if($objInfoPersona)
                {
                    $arrayDepartamentoId = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                       ->getDepartamentoPersonaLogueada($objInfoPersona->getId(),
                                                                                        $arrayItemSeguimiento["empresaCod"] ?
                                                                                        $arrayItemSeguimiento["empresaCod"]
                                                                                        : $arrayParametros["codEmpresa"]);
                    if($arrayDepartamentoId[0]['departamento'])
                    {
                        $objDepartamento = $emGeneral->getRepository('schemaBundle:AdmiDepartamento')
                                                        ->find($arrayDepartamentoId[0]['departamento']);
                        if($objDepartamento)
                        {
                            $strNombreDepartamento = $objDepartamento->getNombreDepartamento();
                        }
                    }
                    else
                    {
                        $arrayDepartamentoId = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                        ->getNombreDepartamentoPersonaLogueada($objInfoPersona->getId());

                        $strNombreDepartamento = isset($arrayDepartamentoId[0]['departamento']) ? $arrayDepartamentoId[0]['departamento'] : "Empresa";
                    }

                }
                else if (strpos($strUsuarioCreacion,'@') > 0 && is_object($arrayParametros['objSoporteService']))
                {
                    $arrayParteAfectada = $this->getClienteAfectado(array('detalleId'     =>  $arrayParametros["idDetalle"],
                                                                          'tipoAfectado'  => 'Cliente'));
                    $intPuntoId = isset($arrayParteAfectada[0]['afectadoId']) ? $arrayParteAfectada[0]['afectadoId'] : 0;
                    $objPunto = $emComercial->getRepository('schemaBundle:InfoPunto')->find($intPuntoId);

                    if (is_object($objPunto))
                    {
                        $arrayCuentaExtranet = $arrayParametros['objSoporteService']->getConsultaCuentaExtranet(
                            array(
                                    'usuario'=>$strUsuarioCreacion,
                                    'contexto' => $objPunto->getPersonaEmpresaRolId()->getId()));
                        $strUsuarioCreacion = $arrayCuentaExtranet['nombres'].' '.$arrayCuentaExtranet['apellidos'] 
                                                .' ('.$strUsuarioCreacion.')';
                    }                   
                }

                $strFecha = date_format($arrayItemSeguimiento["feCreacion"], 'Y-m-d H:i');

                $arrayEncontrados[] = array('id_detalle'   => $arrayParametros["idDetalle"],
                                            'observacion'  => $arrayItemSeguimiento["observacion"],
                                            'departamento' => $strNombreDepartamento,
                                            'empleado'     => $strUsuarioCreacion,
                                            'fecha'        => $strFecha);
            }

            $objData = json_encode($arrayEncontrados);
            $objResultado = '{"total":"' . $intTotal . '","encontrados":' . $objData . '}';
        }
        else
        {
            $objResultado = '{"total":"0","acciones":[]}';
        }
        return $objResultado;
    }

    /**
     * 
     * Metodo encargado de devolver el array con la fecha de finalizacion de las tareas segun caso enviado com parametro
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 06-10-2017 - Se instancia de manera dinamica la variable $prefijoEmpresa por regularizacion en caliente, para que
     *                           cuando no sea enviado ningun valor por default tome VACIO
     * 
     * @since 1.0
     * 
     * @param int    $casoId
     * @param string $prefijoEmpresa
     * @return Array
     */
    public function getFechaTareaSolucion($casoId,$prefijoEmpresa=""){
        $sqlSelect="";
        if($prefijoEmpresa=="TN")
        {
            $sqlSelect.="SELECT max(a.feFinalizacion) as fecha  ";
        }
        else
        {
            $sqlSelect.="SELECT min(a.feFinalizacion) as fecha  ";
        }
	
		$sql = $sqlSelect.
                " FROM 
                schemaBundle:InfoTareaTiempoAsignacion a,
                schemaBundle:InfoDetalle b  ,
                schemaBundle:InfoDetalleHipotesis d,
                schemaBundle:InfoCaso c
                WHERE 
                c.id = $casoId 
                AND c.id = d.casoId
                AND b.detalleHipotesisId = d.id
                AND a.detalleId = b.id			
                AND b.esSolucion = 'S'               
                  ";
		
	      $query = $this->_em->createQuery($sql);
	      	      
	      $rs = $query->getResult();	
	      
	      return $rs;	
	
	}
	public function getFechaUltimaTareaFinalizada($casoId,$estado = 'Finalizada'){
	
		$sql = "SELECT max(a.feCreacion) as fecha        
			FROM 
			schemaBundle:InfoDetalleHistorial a,
			schemaBundle:InfoDetalle b  ,
			schemaBundle:InfoDetalleHipotesis d,
			schemaBundle:InfoCaso c
			WHERE 
			c.id = $casoId 
			AND c.id = d.casoId
			AND b.detalleHipotesisId = d.id
			AND a.detalleId = b.id			
			AND a.estado = '$estado'  and b.tareaId is not null              
		      ";
		
	      $query = $this->_em->createQuery($sql);
	      	      
	      $rs = $query->getResult();	
	      
	      return $rs;	
	
	}	
	public function getFechaPrimeraTareaEjecucion($casoId){
	
		$sql = "SELECT min(b.feSolicitada)       
			FROM 			
			schemaBundle:InfoDetalle b  ,
			schemaBundle:InfoDetalleHipotesis d,
			schemaBundle:InfoCaso c
			WHERE 
			c.id = $casoId 
			AND c.id = d.casoId
			AND b.detalleHipotesisId = d.id					
			AND b.tareaId is not null                   
		      ";
		
	      $query = $this->_em->createQuery($sql);
	      	      
	      $rs = $query->getResult();	
	      
	      return $rs;	
	}
	
	public function getTareasSinSolucion($casoId){
	
		$sql = "SELECT b        
			FROM 			
			schemaBundle:InfoDetalle b  ,
			schemaBundle:InfoDetalleHipotesis d,
			schemaBundle:InfoCaso c
			WHERE 
			c.id = $casoId 
			AND c.id = d.casoId
			AND b.detalleHipotesisId = d.id					
			AND b.esSolucion is null and b.tareaId is not null        
		      ";
		
	      $query = $this->_em->createQuery($sql);
	      	      
	      $rs = $query->getResult();	
	      
	      return $rs;	
	
	}
	public function getTiemposTareasXCaso($casoId){
	
		$sql = "SELECT sum(a.tiempoCliente) as cliente , sum(a.tiempoEmpresa) as empresa     
			FROM 			
			schemaBundle:InfoTareaTiempoAsignacion a  
			WHERE 
			a.casoId = $casoId 			   
		      ";
		
	      $query = $this->_em->createQuery($sql);
	      	      
	      $rs = $query->getResult();	
	      
	      return $rs;	
	
	}

        /*
         * @since 1.0
         *
         * Costo 4
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.1 22-04-2019 - Se modifica la consulta para que devuelva tambien el campo *tiempoTotal*.
         */
	public function getTiempoCaso($idCaso){
	  
	    $query = "SELECT itc.tiempoTotalCaso, itc.tiempoClienteAsignado, itc.tiempoEmpresaAsignado , itc.tiempoTotalCasoSolucion 
                            ,itc.tiempoTotal
                            FROM 
                            schemaBundle:InfoCasoTiempoAsignacion itc                                              
                            WHERE 
			    itc.casoId = $idCaso
                            ";
                            
            $query1 = $this->_em->createQuery($query);                        
                                  
	    $datos = $query1->getResult();
               
	    return $datos;
        
    }
	
    
    /**
     * Documentación para el método 'getCasos'.
     *
     * Retorna los casos guardados en base que correspondan a los criterios
     * ingresados por el usuario.
     *
     * @param array $parametros Los criterios ingresado por el usuario
     * @param integer $start    EL índice inicial donde se desea empezar la 
     *                          búsqueda en la base de datos
     * @param integer $limit    La cantidad de registros que se desea retornar
     * @param object $session   Objeto que tiene los datos guardados en sesion 
     *                          del usuario
     * @return array $resultado 
     * 
     * @version 1.0 Version Inicial
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.1 19-08-2015 - Se incluye una variable llamada '$boolContinuar'
     *                           que ayudará a controlar que no se tengan que 
     *                           enviar de manera obligatoria al arreglo de 
     *                           parámetros datos vacíos para que retorne información.
     * 
     * @author Modificado: Duval Medina C. <dmedina@telconet.ec>
     * @version 1.2 2016-05-25 - Se retira el like del parámetro de 'numero' para mejorar el costo de la base de 11.380 a 10
     *                           haciendo obligatorio cuando se busca por el Numero del Caso no sea parcial
     * 
     * @author Modificado: John Vera <javera@telconet.ec>
     * @version 1.3 2016-06-20   Se optimizo el query segun el caso indicado por el dba de 2.288.183 a 104
     *
     * @author Modificado: Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.4 2018-11-06 - Se adiciona nuevo filtro de busqueda, donde se extrae todos los casos creados desde el móvil.
     * 
     * @author José Guamán <jaguamanp@telconet.ec>
     * @version 2.1 05-09-2022 - .Se Modifico el query añadiendo WITH temporales en la consulta, con la finalidad de mejorar 
     *                            el plan de ejecución del costo del mismo.
     *
     * @author Modificado: David De La Cruz <ddelacruz@telconet.ec>
     * @version 1.5 2022-03-14 - Se adiciona nuevo filtro de busqueda, donde se extrae los casos creados desde la extranet pendientes de cerrar.
     * 
     */
    public function getCasos($arrayParametros, $strStart, $strLimit, $strSession)
    {
        
        $objRsm         = new ResultSetMappingBuilder($this->_em);
        $emQuery       = $this->_em->createNativeQuery(null, $objRsm);
       
        $whereVar       = "";
        $fromAdicional  = "";
        $whereAdicional = "";
        $boolContinuar  = false;
        try
        {
            $strWithsql = "SELECT i7.CASO_ID, MAX (i7.ID_CASO_HISTORIAL) AS ID 
                               FROM DB_SOPORTE.INFO_CASO_HISTORIAL i7
                               GROUP BY i7.CASO_ID";
            $strSqlWithMinMax = "WITH TMP_INFO_CASO_HISTORIAL AS( ".$strWithsql." ),
                                TMP_INFO_DETALLE_HIPOTESIS
                                AS ( SELECT i8.CASO_ID, MAX (i8.ID_DETALLE_HIPOTESIS) AS ID 
                                    FROM DB_SOPORTE.INFO_DETALLE_HIPOTESIS i8 GROUP BY i8.CASO_ID),
                                TMP_INFO_CASO_ASIGNACION
                                AS ( SELECT i9.DETALLE_HIPOTESIS_ID, MAX (i9.ID_CASO_ASIGNACION) AS ID 
                                     FROM DB_SOPORTE.INFO_CASO_ASIGNACION i9  GROUP BY i9.DETALLE_HIPOTESIS_ID ) ";
            if($arrayParametros && count($arrayParametros) > 0)
            {            
                if(isset($arrayParametros["numero"]) && $arrayParametros["numero"] && $arrayParametros["numero"] != "")
                {
                    $boolContinuar = true;

                    $whereVar .= "AND caso.NUMERO_CASO = :numeroCaso ";
                    $emQuery->setParameter('numeroCaso', $arrayParametros["numero"]);
                }
                if(isset($arrayParametros["usrApertura"]) && $arrayParametros["usrApertura"] && $arrayParametros["usrApertura"] != "")
                {
                    $boolContinuar = true;
                        
                    $whereVar .= "AND lower(caso.USR_CREACION) like lower(:usrCreacion) ";
                    $emQuery->setParameter('usrCreacion','%' . $arrayParametros["usrApertura"] . '%');
                }
                if(isset($arrayParametros["usrCierre"]) && $arrayParametros["usrCierre"] && $arrayParametros["usrCierre"] != "")
                {
                    $boolContinuar = true;
                        
                    $whereVar .= "AND lower(casoHist.ESTADO) like lower(:estadoCerrado) 
                                    AND lower(casoHist.USR_CREACION) like lower(usrCreacion) ";
                    $emQuery->setParameter('estadoCerrado','Cerrado');
                    $emQuery->setParameter('usrCreacion','%' . $arrayParametros["usrCierre"] . '%');
                }
                if(isset($arrayParametros["estado"]) && $arrayParametros["estado"] && $arrayParametros["estado"] != "Todos")
                {
                    $boolContinuar = true;
                        
                    $whereVar .= "AND lower(casoHist.ESTADO) like lower(:estado) ";
                    $emQuery->setParameter('estado','%'.$arrayParametros["estado"].'%');
                }
                if(isset($arrayParametros["tituloInicial"]) && $arrayParametros["tituloInicial"] && $arrayParametros["tituloInicial"] != "")
                {
                    $boolContinuar = true;
                        
                    $whereVar .= "AND lower(caso.TITULO_INI) like lower(:tituloInicial) ";
                    $emQuery->setParameter('tituloInicial','%'.$arrayParametros["tituloInicial"].'%');
                }
                if(isset($arrayParametros["versionInicial"]) && $arrayParametros["versionInicial"] && $arrayParametros["versionInicial"] != "")
                {             
                    $boolContinuar = true;
                            
                    $whereVar .= "AND lower(caso.VERSION_INI) like lower(:versionInicial) ";                    
                    $emQuery->setParameter('versionInicial','%'.$arrayParametros["versionInicial"].'%');
                }
                if(isset($arrayParametros["tituloFinal"]) || isset($arrayParametros["tituloFinalHip"]))
                {
                    $where1 = '';
                    if(isset($arrayParametros["tituloFinal"]) && $arrayParametros["tituloFinal"] && $arrayParametros["tituloFinal"] != "")
                    {
                        $boolContinuar = true;
                        
                        $where1 = "lower(caso.TITULO_FIN) like lower(:tituloFinal) ";
                        $emQuery->setParameter('tituloFinal','%'.$arrayParametros["tituloFinal"].'%');
                    }

                    $where2 = '';
                    if(isset($arrayParametros["tituloFinalHip"]) && $arrayParametros["tituloFinalHip"] && $arrayParametros["tituloFinalHip"] != "")
                    {
                        $boolContinuar = true;
                        
                        $where2 = "caso.TITULO_FIN_HIP = :tituloFinalHip ";
                        $emQuery->setParameter('tituloFinalHip',$parametros["tituloFinalHip"]);
                    }

                    if($where1 != '' && $where2 != '')
                    {
                        $whereVar .= "AND ($where1 OR $where2) ";
                    }
                    else if($where1 != '')
                    {
                        $whereVar .= "AND $where1 ";
                    }
                    else if($where2 != '')
                    {
                        $whereVar .= "AND $where2 ";
                    }
                }

                if(isset($arrayParametros["versionFinal"]) && $arrayParametros["versionFinal"] && $arrayParametros["versionFinal"] != "")
                {
                    $boolContinuar = true;
                        
                    $whereVar .= "AND lower(caso.VERSION_FIN) like lower(:versionFinal) ";
                    $emQuery->setParameter('versionFinal',$arrayParametros["versionFinal"]);
                }
                if(isset($arrayParametros["nivelCriticidad"]) && $arrayParametros["nivelCriticidad"] && $arrayParametros["nivelCriticidad"] != "")
                {
                    $boolContinuar = true;
                        
                    $whereVar .= "AND caso.NIVEL_CRITICIDAD_ID = :nivelCriticidad ";
                    $emQuery->setParameter('nivelCriticidad',$arrayParametros["nivelCriticidad"]);
                }
                if(isset($arrayParametros["tipoCaso"]) && $arrayParametros["tipoCaso"] && $arrayParametros["tipoCaso"] != "")
                {
                    $boolContinuar = true;
                        
                    $whereVar .= "AND caso.TIPO_CASO_ID = :tipoCaso ";
                    $emQuery->setParameter('tipoCaso',$arrayParametros["tipoCaso"]);
                }
                if(isset($arrayParametros["strOrigen"]) && $arrayParametros["strOrigen"] != "")
                {
                    if ($arrayParametros["strOrigen"] !== 'E')
                    {                    
                        $boolContinuar = true;

                        $whereVar .= "AND caso.ORIGEN = :strOrigen "
                                . "AND casoHist.ESTADO NOT IN (:strEstado) ";
                        $emQuery->setParameter('strOrigen','M');
                        $emQuery->setParameter('strEstado',array("Asignado","Cerrado"));
                    } else 
                    {
                        $boolContinuar = true;

                        $whereVar .= "AND caso.origen = :strOrigen "
                                   . "AND casoHist.estado = :strEstado ";
    
                        if ($arrayParametros["strTipoConsulta"] == '1')
                        {
                            $whereVar .= "AND caso.id NOT IN ";
                        }
                        else
                        {
                            $whereVar .= "AND caso.id IN ";
                        }
                         
                        $whereVar .= " (SELECT Ico.casoId "
                                   . "                FROM "
                                   . "                  schemaBundle:InfoComunicacion Ico, "
                                   . "                  schemaBundle:InfoDetalleAsignacion Ida "
                                   . "                WHERE Ico.detalleId = Ida.detalleId "
                                   . "                AND Ico.casoId IN ( "
                                   . "                      SELECT Ic.id "
                                   . "                      FROM schemaBundle:InfoCaso Ic "
                                   . "                      WHERE "
                                   . "                         Ic.empresaCod = :idEmpresaSeleccion "
                                   . "                      AND Ic.origen = :strOrigen "
                                   . "                      AND Ic.feCierre IS NULL "
                                   . "                    ) "
                                   . "                )";
    
    
                                   
                        $query->setParameter('strOrigen','E');
                        $query->setParameter('strEstado','Asignado');
                    }
                }

                $strFeAperturaDesde = (isset($arrayParametros["feAperturaDesde"]) ? $arrayParametros["feAperturaDesde"] : 0);
                $strFeAperturaHasta = (isset($arrayParametros["feAperturaHasta"]) ? $arrayParametros["feAperturaHasta"] : 0);
                $strFeCierreDesde   = (isset($arrayParametros["feCierreDesde"])   ? $arrayParametros["feCierreDesde"]   : 0);
                $strFeCierreHasta   = (isset($arrayParametros["feCierreHasta"])   ? $arrayParametros["feCierreHasta"]   : 0);

                if($strFeAperturaDesde && $strFeAperturaDesde != "0")
                {
                    $strDateF = explode("-", $strFeAperturaDesde);
                    $strFeAperturaDesde = date("Y/m/d", strtotime($strDateF[2] . "-" . $strDateF[1] . "-" . $strDateF[0]));
                }
                if($strFeAperturaHasta && $strFeAperturaHasta != "0")
                {
                    $strDateF = explode("-", $strFeAperturaHasta);
                    $strFechaSqlAdd = strtotime(date("Y-m-d", strtotime($strDateF[2] . "-" . $strDateF[1] . "-" . $strDateF[0])) . " +1 day");
                    $strFeAperturaHasta = date("Y/m/d", $strFechaSqlAdd);
                }
                if($strFeCierreDesde && $strFeCierreDesde != "0")
                {
                    $strDateF = explode("-", $strFeCierreDesde);
                    $strFeCierreDesde = date("Y/m/d", strtotime($strDateF[2] . "-" . $strDateF[1] . "-" . $strDateF[0]));
                }
                if($strFeCierreHasta && $strFeCierreHasta != "0")
                {
                    $strDateF = explode("-", $strFeCierreHasta);
                    $strFechaSqlAdd = strtotime(date("Y-m-d", strtotime($strDateF[2] . "-" . $strDateF[1] . "-" . $strDateF[0])) . " +1 day");
                    $strFeCierreHasta = date("Y/m/d", $strFechaSqlAdd);
                }

                if($strFeAperturaDesde && $strFeAperturaDesde != "0")
                {
                    $boolContinuar = true;
                    $whereVar .= "AND caso.FE_APERTURA >= :feAperturaDesde ";
                    $emQuery->setParameter('feAperturaDesde',trim($strFeAperturaDesde));
                }
                if($strFeAperturaHasta && $strFeAperturaHasta != "0")
                {
                    $boolContinuar = true;
                    $whereVar .= "AND caso.FE_APERTURA <= :feAperturaHasta ";
                    $emQuery->setParameter('feAperturaHasta',trim($strFeAperturaHasta));
                }
                if($strFeCierreDesde && $strFeCierreDesde != "0")
                {
                    $boolContinuar = true;
                        
                    $whereVar .= "AND caso.FE_CIERRE >= :feCierreDesde ";
                    $emQuery->setParameter('feCierreDesde',trim($strFeCierreDesde));
                }
                if($strFeCierreHasta && $strFeCierreHasta != "0")
                {
                    $boolContinuar = true;
                        
                    $whereVar .= "AND caso.FE_CIERRE <= :feCierreHasta ";
                    $emQuery->setParameter('feCierreHasta',trim($strFeCierreHasta));
                }

                if(isset($arrayParametros["clienteAfectado"]) && $arrayParametros["loginAfectado"] == "")
                {
                        $boolValida = $arrayParametros["clienteAfectado"] && $arrayParametros["clienteAfectado"] != "";
                        if ($boolValida) 
                        {
                            $boolContinuar = true;
                        
                            $whereVar .= "AND exists( 
                                                SELECT 1
                                                FROM 
                                                    DB_SOPORTE.INFO_DETALLE_HIPOTESIS dh1, 
                                                    DB_SOPORTE.INFO_DETALLE d1, 
                                                    DB_SOPORTE.INFO_PARTE_AFECTADA pa1  
                                                WHERE 
                                                dh1.CASO_ID = caso.ID_CASO  
                                                AND dh1.ID_DETALLE_HIPOTESIS = d1.DETALLE_HIPOTESIS_ID
                                                AND d1.ID_DETALLE  = pa1.DETALLE_ID 
                                                AND pa1.TIPO_AFECTADO = :tipoAfectado
                                                AND pa1.AFECTADO_DESCRIPCION like :clienteAfectado 
                                            ) ";
                            $emQuery->setParameter('tipoAfectado','Cliente');
                            $emQuery->setParameter('clienteAfectado', $arrayParametros["clienteAfectado"].'%');
                        }
                        
                }

                if(isset($arrayParametros["loginAfectado"]) && $arrayParametros["loginAfectado"] && $arrayParametros["loginAfectado"] != "")
                {
                        $boolContinuar = true;
                        $whereVar .= "AND ( SELECT COUNT(pa2.ID_PARTE_AFECTADA)
                                    FROM 
                                    DB_SOPORTE.INFO_DETALLE_HIPOTESIS dh2, 
                                    DB_SOPORTE.INFO_DETALLE d2, 
                                    DB_SOPORTE.INFO_PARTE_AFECTADA pa2 
                                    WHERE 
                                    dh2.CASO_ID = caso.ID_CASO  
                                    AND dh2.ID_DETALLE_HIPOTESIS = d2.DETALLE_HIPOTESIS_ID 
                                    AND d2.ID_DETALLE  = pa2.DETALLE_ID 
                                    AND lower(pa2.TIPO_AFECTADO) = lower(:tipoAfectado) 
                                    AND lower(pa2.AFECTADO_NOMBRE) = lower(:loginAfectado) ) > 0 ";
                        $emQuery->setParameter('tipoAfectado','Cliente');                    
                        $emQuery->setParameter('loginAfectado',$arrayParametros["loginAfectado"]);
                }

                if(isset($arrayParametros["departamento_id"]) || isset($arrayParametros["empleado_id"]) || isset($arrayParametros["canton_id"]))
                {
                    if(($arrayParametros["departamento_id"] && $arrayParametros["departamento_id"] != "") ||
                        ($arrayParametros["empleado_id"] && $arrayParametros["empleado_id"] != "") ||
                        ($arrayParametros["canton_id"] && $arrayParametros["canton_id"] != "")
                    )
                    {
                        $boolContinuar = true;
                        

                        $fromAdicional  .=" DB_SOPORTE.INFO_DETALLE_HIPOTESIS idh, "
                                        . " DB_SOPORTE.INFO_CASO_ASIGNACION ica, "
                                        . " DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL iper, ";
                        $whereAdicional .= "	AND caso.ID_CASO = idh.CASO_ID 
                                                AND idh.ID_DETALLE_HIPOTESIS = (	
                                                            SELECT 
                                                                idhMax.ID
                                                            FROM 
                                                                TMP_INFO_DETALLE_HIPOTESIS idhMax
                                                            WHERE 
                                                                idhMax.CASO_ID = idh.CASO_ID
                                                            ) 
                                                AND	idh.ID_DETALLE_HIPOTESIS = ica.DETALLE_HIPOTESIS_ID
                                                AND ica.ID_CASO_ASIGNACION = (	
                                                            SELECT 
                                                                icaMax.ID
                                                            FROM 
                                                                TMP_INFO_CASO_ASIGNACION icaMax
                                                            WHERE 
                                                                icaMax.DETALLE_HIPOTESIS_ID = ica.DETALLE_HIPOTESIS_ID
                                                            )
                                                AND iper.ID_PERSONA_ROL = ica.PERSONA_EMPRESA_ROL_ID ";
                    }
                    if(isset($arrayParametros["departamento_id"]) && $arrayParametros["departamento_id"] && $arrayParametros["departamento_id"] != "")
                    {
                        $boolContinuar = true;
                        
                        $fromAdicional  .= "DB_GENERAL.ADMI_DEPARTAMENTO ad , ";

                        $whereAdicional .= " AND ad.ID_DEPARTAMENTO = :departamento_id "
                                            . " and ad.ID_DEPARTAMENTO = iper.DEPARTAMENTO_ID ";
                            
                        $emQuery->setParameter('departamento_id',$arrayParametros["departamento_id"]);
                    }
                    if(isset($arrayParametros["empleado_id"]) && $arrayParametros["empleado_id"] && $arrayParametros["empleado_id"] != "")
                    {
                            $boolContinuar = true;
                        
                            $arrayEmployee = explode('@@', $arrayParametros["empleado_id"]);
                            $idPersona     = $arrayEmployee[0];                        

                            $fromAdicional  .= "    DB_COMERCIAL.INFO_PERSONA ip, ";
                            $whereAdicional .= "	AND ip.ID_PERSONA = iper.PERSONA_ID 						                        
                                                    AND ip.ID_PERSONA = :idPersona ";
                            
                            $emQuery->setParameter('idPersona',$idPersona);
                    }
                    if(isset($arrayParametros["canton_id"]) && $arrayParametros["canton_id"] && $arrayParametros["canton_id"] != "")
                    {
                            $boolContinuar = true;
                        
                            $fromAdicional .= "INFO_OFICINA_GRUPO iog , 
                                                ADMI_CANTON ac , ";

                            $whereAdicional .= "	AND iog.CANTON_ID = ac.ID_CANTON
                                                    AND iog.ID_OFICINA = iper.OFICINA_ID 
                                                    AND ac.ID_CANTON = :cantonId  ";
                            
                            $emQuery->setParameter('cantonId',$arrayParametros["canton_id"]);
                    }
                }
            }

            if( $boolContinuar )
            {
                $strSql =" SELECT DISTINCT(caso.ID_CASO), caso.EMPRESA_COD, caso.TIPO_CASO_ID,  ".
                "caso.FORMA_CONTACTO_ID, caso.NIVEL_CRITICIDAD_ID, caso.NUMERO_CASO,  ".
                "caso.TITULO_INI, caso.VERSION_INI, caso.VERSION_FIN, caso.FE_APERTURA,  ".
                "caso.IP_CREACION, caso.TITULO_FIN_HIP, caso.TIPO_AFECTACION, caso.USR_CREACION, caso.FE_CIERRE, caso.TITULO_FIN ".
                "        FROM $fromAdicional ".
                "        DB_SOPORTE.INFO_CASO caso, ".
                "        DB_SOPORTE.INFO_CASO_HISTORIAL casoHist  ".
                "        WHERE  ".
                "        caso.ID_CASO IS NOT NULL ".
                "        AND caso.ID_CASO = casoHist.CASO_ID  ".
                "        AND casoHist.ID_CASO_HISTORIAL = ( SELECT casoHistMax.ID ".
                " FROM TMP_INFO_CASO_HISTORIAL casoHistMax WHERE casoHistMax.CASO_ID = casoHist.CASO_ID) ".
                "        AND caso.EMPRESA_COD = :idEmpresaSeleccion     ".             
                "        $whereAdicional       ".                                                     
                "        $whereVar  ".
                "        ORDER BY caso.ID_CASO DESC";
                $emQuery->setParameter('idEmpresaSeleccion',$arrayParametros['idEmpresaSeleccion']);            
            }
            else
            {

                $current_month = date("m");
                $current_year = date("Y");

                $mes = mktime(0, 0, 0, $current_month, 1, $current_year);
                $numeroDeDias = intval(date("t", $mes));

                $strSql = "	SELECT  caso.ID_CASO, caso.EMPRESA_COD, caso.TIPO_CASO_ID, 
                            caso.FORMA_CONTACTO_ID, caso.NIVEL_CRITICIDAD_ID, caso.NUMERO_CASO, 
                            caso.TITULO_INI, caso.VERSION_INI, caso.VERSION_FIN, caso.FE_APERTURA, 
                            caso.IP_CREACION, caso.TITULO_FIN_HIP, caso.TIPO_AFECTACION, 
                            caso.USR_CREACION, caso.FE_CIERRE, caso.TITULO_FIN
                            FROM 
                            $fromAdicional
                            DB_SOPORTE.INFO_CASO caso,
                            DB_SOPORTE.INFO_CASO_HISTORIAL casoHist 
                            WHERE 
                            caso.ID_CASO is not null	
                            AND caso.ID_CASO = casoHist.CASO_ID 
                            AND casoHist.ID_CASO_HISTORIAL = (SELECT casoHistMax.ID
                                            FROM TMP_INFO_CASO_HISTORIAL casoHistMax
                                            WHERE casoHistMax.CASO_ID = casoHist.CASO_ID)    
                                            AND casoHist.ESTADO!='Cerrado'
                                            
                                            AND caso.FE_APERTURA >= :feAperturaIni
                                            AND caso.FE_APERTURA <= :feAperturaFin   
                                            AND caso.EMPRESA_COD  = :idEmpresaSeleccion                 
                                            
                        $whereAdicional                                                                                       
                        $whereVar 
                        
                        ORDER BY caso.ID_CASO DESC";
                    $emQuery->setParameter('feAperturaIni',$current_year . "/" . $current_month . "/01");
                    $emQuery->setParameter('feAperturaFin',$current_year . "/" . $current_month . "/" . $numeroDeDias);
                    $emQuery->setParameter('idEmpresaSeleccion',$arrayParametros['idEmpresaSeleccion']);
            }
            $strQueryFinal = $strSqlWithMinMax. $strSql;
            $emQuery->setSQL($strQueryFinal);
            $objRsm->addScalarResult('ID_CASO', 'idCaso','string');
            $objRsm->addScalarResult('EMPRESA_COD', 'empresaCod','string');
            $objRsm->addScalarResult('TIPO_CASO_ID', 'tipoCasoId','string');
            $objRsm->addScalarResult('FORMA_CONTACTO_ID', 'FormaContactoId','string');
            $objRsm->addScalarResult('NIVEL_CRITICIDAD_ID', 'nivelCriticidadId','string');
            $objRsm->addScalarResult('NUMERO_CASO', 'numeroCaso','string');
            $objRsm->addScalarResult('TITULO_INI', 'tituloIni','string');
            $objRsm->addScalarResult('VERSION_INI', 'versionIni','string');
            $objRsm->addScalarResult('VERSION_FIN', 'versionFin','string');
            $objRsm->addScalarResult('FE_APERTURA', 'feApertura','string');
            $objRsm->addScalarResult('IP_CREACION', 'ipCreacion','string');
            $objRsm->addScalarResult('TITULO_FIN_HIP', 'tituloFinHip','string');
            $objRsm->addScalarResult('TIPO_AFECTACION', 'tipoAfectacion','string');
            $objRsm->addScalarResult('USR_CREACION', 'usrCreacion','string');
            $objRsm->addScalarResult('FE_CIERRE', 'feCierre','string');
            $objRsm->addScalarResult('TITULO_FIN', 'tituloFin','string');

            $strPage = ! empty( $arrayParametros['page'] ) ? (int) $arrayParametros['page'] : 1;
            $strTotal = count( $emQuery->getScalarResult() ); 
            $strTotalPages = ceil( $strTotal/ $strLimit ); 
            $strPage = max($strPage, 1); 
            $strPage = min($strPage, $strTotalPages); 
            $strOffset = ($strPage - 1) * $strLimit;
            if( $strOffset < 0 ) 
            {
                $strOffset = 0;
            }
            $objEncontrados = array_slice($emQuery->getScalarResult(), $strOffset, $strLimit);

            $intTotal = count($objEncontrados);      
            $arrayResultado['registros'] = $objEncontrados;
            $arrayResultado['total'] = $strTotal;
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
        }
        return $arrayResultado;
    }

    /**
     * Función que verifica si la hipótesis del caso es la que fue creada desde el móvil
     *
     * @author Walther Joao Gaibor C. <mailto:wgaibor@telconet.ec>
     * @version 1.0
     * @since 16-07-2018
     *
     * @param array $arrayParametros[
     *                               'idSintoma':   integer:    id sintoma creado desde la app móvil
     *                               'idCaso':      integer:    id caso creado
     *                              ]
     * @return boolean
     */
    public function isHipotesisDesdeAPPMovil($arrayParametros)
    {
        if(isset($arrayParametros))
        {
            $objQb = $this->_em->createQueryBuilder();
            $objQb->select('detalle')
                  ->from('schemaBundle:InfoDetalle','detalle')
                  ->from('schemaBundle:InfoDetalleHipotesis', 'detalleHipotesis')
                  ->where('detalleHipotesis.casoId = ?1')
                  ->andWhere('detalle.detalleHipotesisId = detalleHipotesis.id')
                  ->setParameter(1, $arrayParametros['idCaso'])
                  ->andWhere('detalleHipotesis.sintomaId = ?2')
                  ->setParameter(2, $arrayParametros['idSintoma']);
            $objQuery = $objQb->getQuery();
            if(count($objQuery->getResult())>0)
            {
                return true;
            }
            return false;
        }
        else
        {
            return false;
        }
    }
    public function tieneHipotesisSinSintomas($id_caso){
        $qb = $this->_em->createQueryBuilder();
        $qb->select('detalle')
           ->from('schemaBundle:InfoDetalle','detalle')
		   ->from('schemaBundle:InfoDetalleHipotesis', 'detalleHipotesis')
           ->where('detalleHipotesis.casoId = ?1')
		   ->andWhere('detalle.detalleHipotesisId = detalleHipotesis.id')
           ->setParameter(1, $id_caso)  
           ->andWhere('detalleHipotesis.sintomaId is null');
        $query = $qb->getQuery();
        if(count($query->getResult())>0)
            return true;
        return false;
    }
	
    public function tieneHipotesis($id_caso){
        $qb = $this->_em->createQueryBuilder();
        $qb->select('detalle')
           ->from('schemaBundle:InfoDetalle','detalle')
		   ->from('schemaBundle:InfoDetalleHipotesis', 'detalleHipotesis')
           ->where('detalleHipotesis.casoId = ?1')
		   ->andWhere('detalle.detalleHipotesisId = detalleHipotesis.id')
           ->setParameter(1, $id_caso)  
           ->andWhere('detalleHipotesis.hipotesisId is not null');
        $query = $qb->getQuery();
        if(count($query->getResult())>0)
            return true;
        return false;
    }
	
    public function tieneTareas($id_caso){
        $qb = $this->_em->createQueryBuilder();
        $qb->select('detalle')
           ->from('schemaBundle:InfoDetalle','detalle')
		   ->from('schemaBundle:InfoDetalleHipotesis', 'detalleHipotesis')
           ->where('detalleHipotesis.casoId = ?1')
		   ->andWhere('detalle.detalleHipotesisId = detalleHipotesis.id')
           ->setParameter(1, $id_caso)  
           ->andWhere('detalle.tareaId is not null');
        $query = $qb->getQuery();
        if(count($query->getResult())>0)
            return true;
        return false;
    }
	
    /**
     * Documentación para la funcion 'getCountTareasAbiertas'
     *
     * Funcion que retorna la cantidad de tareas segun el estado que se envie
     *
     * @param integer $id_caso
     * @param string  $estado  Se envia el estado Abiertas,FinalizadasSolucion,Canceladas
     *
     * @return array $arrayDatos
     *
     * @version 1.0
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 11-08-2016 Se considera como tarea abierta a las reprogramadas
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 11-08-2016 Se agrega validacion, para que cuando el estado enviado sea: Todas, la variable
     *                         $strWhere sea vacia y no se valla por la ultima condicion, porque este comportamiento esta incorrecto,
     *                         esta provocando que por ejemplo cuando se cierre un caso de TN no muestre la ventana emergente con las
     *                         tareas finalizadas para poder determinar cual es solucion
     *
     */
    public function getCountTareasAbiertas($id_caso, $estado)
    {
        $strWhere = "";
        $objQuery = $this->_em->createQuery();
        if($estado == "Abiertas")
        {
            $strWhere .= "AND (LOWER(detalleHistorial.estado) like LOWER(:paramAsig) OR
                                LOWER(detalleHistorial.estado) like LOWER(:paramAcep) OR
                                LOWER(detalleHistorial.estado) like LOWER(:paramRepr) ) ";

            $objQuery->setParameter("paramAsig", "Asignada");
            $objQuery->setParameter("paramAcep", "Aceptada");
            $objQuery->setParameter("paramRepr", "Reprogramada");
        }
        else if($estado == "FinalizadasSolucion")
        {
            $strWhere .= " AND LOWER(detalleHistorial.estado) like LOWER(:paramFina)
                           AND LOWER(detalle.esSolucion) like LOWER(:paramSolu) ";

            $objQuery->setParameter("paramFina", "Finalizada");
            $objQuery->setParameter("paramSolu", "S");
        }
        else if($estado == "Canceladas")
        {
            $strWhere .= " AND LOWER(detalleHistorial.estado) like LOWER(:paramCanc) ";

            $objQuery->setParameter("paramCanc", "Cancelada");
        }
        else if($estado == "Todas")
        {
            $strWhere = "";
        }
        else
        {
            $strWhere .= "AND (LOWER(detalleHistorial.estado) like LOWER(:paramAsig) OR
                        LOWER(detalleHistorial.estado) like LOWER(:paramAcep) OR
                        ( LOWER(detalleHistorial.estado) like LOWER(:paramFina) AND
                        LOWER(detalle.esSolucion) like LOWER(:paramSolu))) ";

            $objQuery->setParameter("paramAsig", "Asignada");
            $objQuery->setParameter("paramAcep", "Aceptada");
            $objQuery->setParameter("paramFina", "Finalizada");
            $objQuery->setParameter("paramSolu", "S");
        }

        $strSql = "SELECT detalleHistorial
                    FROM
                    schemaBundle:InfoCaso caso,
                    schemaBundle:InfoDetalleHipotesis detalleHipotesis,
                    schemaBundle:InfoDetalle detalle,
                    schemaBundle:InfoDetalleHistorial detalleHistorial
                    WHERE caso.id = :paramIdCaso
                    AND caso = detalleHipotesis.casoId
                    AND detalleHipotesis = detalle.detalleHipotesisId
                    AND detalle = detalleHistorial.detalleId
                    AND detalleHistorial.id = (SELECT MAX(detalleHistorialMax.id)
                                               FROM schemaBundle:InfoDetalleHistorial detalleHistorialMax
                                                WHERE detalleHistorialMax.detalleId = detalleHistorial.detalleId) " .
                    $strWhere;


        $objQuery->setParameter('paramIdCaso', $id_caso);
        $objQuery->setDQL($strSql);

        $arrayDatos = $objQuery->getResult();

        return $arrayDatos ? count($arrayDatos) : 0;
    }

    public function getUltimaAsignacion($id_caso)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('asignacion')
           ->from('schemaBundle:InfoCasoAsignacion','asignacion')
           ->from('schemaBundle:InfoDetalleHipotesis','detalleHipotesis')
           ->where('detalleHipotesis.casoId = ?1')
           ->setParameter(1, $id_caso)
	   ->andWhere('asignacion.detalleHipotesisId = detalleHipotesis.id')
           ->orderBy('asignacion.id','DESC')
           ->setMaxResults(1);
        $query = $qb->getQuery();                
        
        $results = $query->getResult();		
        return ($results && count($results)>0) ? $results[0] : false;
    }
	
    public function generarJsonMateriales($em_naf, $codEmpresa , $prefijoEmpresa="")
    {
        $arr_encontrados = array();
        
        $qb = $this->_em->createQueryBuilder();
        $qb->select('entidad')
           ->from('schemaBundle:AdmiTareaMaterial','entidad')
           ->where("entidad.estado not like 'Eliminado'");
            
        if($codEmpresa!=""){
            $qb ->where("entidad.empresaCod = ?1 ");
            $qb->setParameter(1, $codEmpresa);
        }
		
        $query = $qb->getQuery();
        $results = $query->getResult();
        
        if ($results) {
            
            $num = count($results);
            
            foreach ($results as $entidad)
            {		
		    
		if($prefijoEmpresa == 'MD' ) $codEmpresa = '10';
            
		$descripcionArticulo = "";
		$vArticulo = $em_naf->getRepository('schemaBundle:VArticulosEmpresas')->getOneArticulobyEmpresabyCodigo($codEmpresa, $entidad->getMaterialCod()); 
		if($vArticulo && count($vArticulo)>0)
		{
			$descripcionArticulo =  (isset($vArticulo) ? ($vArticulo->getDescripcion() ? $vArticulo->getDescripcion() : "") : ""); 
		}
					
                $arr_encontrados[]=array('id_material' =>$entidad->getId(),
                                         'cod_material' =>$entidad->getMaterialCod(),
                                         'nombre_material' =>$descripcionArticulo,
                                         'costo_material' =>$entidad->getCostoMaterial(),
                                         'cant_material' =>$entidad->getCantidadMaterial());
            }
            $data=json_encode($arr_encontrados);
            $resultado= '{"total":"'.$num.'","encontrados":'.$data.'}';

            return $resultado;
        }
        else
        {
            $resultado= '{"total":"0","encontrados":[]}';

            return $resultado;
        }
        
    }
   
   
    /* ******************************************** FINALIZAR ******************************************* */
    public function generarJsonMaterialesByTarea($em, $em_naf, $start, $limit, $id_detalle, $codEmpresa, $prefijoEmpresa="")
    {
        $arr_encontrados = array();
        
        $resultado = $this->getRegistrosMaterialesByTarea($start, $limit, $id_detalle, $codEmpresa);
		
		if($resultado)
		{
			$num = $resultado['total'];
			$registros = $resultado['registros'];
			
			if ($registros && count($registros)>0) 
			{          
				foreach ($registros as $data)
				{        
					$material_cod = $data["materialCod"];                
					$descripcionArticulo = "";
					
					if($prefijoEmpresa == 'MD') $codEmpresa = '10';					
					
					$vArticulo = $em_naf->getRepository('schemaBundle:VArticulosEmpresas')->getOneArticulobyEmpresabyCodigo($codEmpresa, $material_cod); 
					if($vArticulo && count($vArticulo)>0)
					{
						$descripcionArticulo =  (isset($vArticulo) ? ($vArticulo->getDescripcion() ? $vArticulo->getDescripcion() : "") : ""); 
					}
										    
					$nombreTarea =  ($data["nombreTarea"] ? $data["nombreTarea"]  : "");    
					$costoMaterial =  ($data["costoMaterial"] ? number_format($data["costoMaterial"], 2, '.', '') : 0.00);  
					$precioVentaMaterial =  ($data["precioVentaMaterial"] ? number_format($data["precioVentaMaterial"], 2, '.', '')  : 0.00);  
					$cantidadMaterial =  ($data["cantidadMaterial"] ? $data["cantidadMaterial"]  : 0);                  
					$idDetalle =  (isset($data["idDetalle"]) ? ($data["idDetalle"] ? $data["idDetalle"] : 0) : 0);     
					$idTarea =  (isset($data["idTarea"]) ? ($data["idTarea"] ? $data["idTarea"] : 0) : 0);   
					$idTareaMaterial =  (isset($data["idTareaMaterial"]) ? ($data["idTareaMaterial"] ? $data["idTareaMaterial"] : 0) : 0);   
					$materialCod =  (isset($data["materialCod"]) ? ($data["materialCod"] ? $data["materialCod"] : 0) : 0); 
								   
					$arr_encontrados[]=array(
											 'id_detalle' => $idDetalle,
											 'id_tarea' =>$idTarea,
											 'id_material' =>$idTareaMaterial,
											 'cod_material' => $materialCod,
											 'nombre_tarea' =>trim($nombreTarea),
											 'nombre_material' =>trim($descripcionArticulo),
											 'costo' => $costoMaterial,
											 'precio_venta_material' => $precioVentaMaterial,
											 'cant' => $cantidadMaterial,
											 'cant_nf' => 0,
											 'cant_f' => 0,
											 'valor' => 0,
											 'fin_origen' => 'BD'
											);
				}

				$dataF =json_encode($arr_encontrados);
				$resultado= '{"total":"'.$num.'","encontrados":'.$dataF.'}';
				return $resultado;
			}
			else
			{
				$resultado= '{"total":"0","encontrados":[]}';
				return $resultado;
			}
		}
        else
        {
            $resultado= '{"total":"0","encontrados":[]}';
            return $resultado;
        }
        
    }
    
    public function getRegistrosMaterialesByTarea($start, $limit, $id_detalle, $codEmpresa)
    {
        $boolBusqueda = false; 
        $where = "";  
        		 
        if($codEmpresa!=""){
			$where .= "AND tm.empresaCod = $codEmpresa ";
        }
		
		$selectedCont = " count(tm) as cont ";
		$selectedData = "
							d.id as idDetalle, tm.id as idTareaMaterial,  t.id as idTarea, 
							tm.materialCod, tm.cantidadMaterial, t.nombreTarea, tm.costoMaterial, tm.precioVentaMaterial
						";
		$from = "FROM 
					schemaBundle:InfoDetalle d, schemaBundle:AdmiTareaMaterial tm, schemaBundle:AdmiTarea t  ";				
		$wher = "WHERE 
					t.id = d.tareaId
					AND t.id = tm.tareaId 
					AND d.id = $id_detalle 
					AND LOWER(t.estado) not like LOWER('Eliminado') 
					AND LOWER(tm.estado) not like LOWER('Eliminado') 
					$where 
                ";
				
		$sql = "SELECT $selectedData $from $wher ";
		$sqlC = "SELECT $selectedCont $from $wher ";
		
        $queryC = $this->_em->createQuery($sqlC); 
        $query = $this->_em->createQuery($sql); 
		
		$resultTotal = $queryC->getOneOrNullResult();
		$total = ($resultTotal ? ($resultTotal["cont"] ? $resultTotal["cont"] : 0) : 0);
		//$total=count($query->getResult());
		
		//echo $query->getSql();
		
        $datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
        $resultado['registros']=$datos;
        $resultado['total']=$total;

        return $resultado;
    }
    
	
    public function getUltimoEstado($idCaso="", $retorna="estado")
    {
	  $qb = $this->_em->createQueryBuilder();
	  $qb->select('casoHistorial')
	      ->from('schemaBundle:InfoCasoHistorial','casoHistorial')
	      ->where('casoHistorial.casoId = ?1')
	      ->setParameter(1, $idCaso)
	      ->orderBy('casoHistorial.feCreacion','desc');
	      
	  $query = $qb->getQuery();
	  $rs = $query->getResult();						
	  
	  if(count($rs)>0)
	  {
		if($retorna == "entidad")
			return $rs[0];
		else
			return $rs[0]->getEstado();
	  }
	  else  return "";
    }    
    
    public function generarJsonCriteriosXCaso($id_caso, $id_sintoma, $id_hipotesis,$start='',$limit='')
    {
        $arr_encontrados = array();
        $where = "";
		
		if($id_sintoma && $id_sintoma != "" && $id_sintoma > 0)
		{
			$where .= "AND dh.sintomaId = '$id_sintoma' ";
		}
		else if($id_sintoma == "NO")
		{
			//NADA
		}
		else
		{
			$where .= "AND dh.sintomaId IS NULL ";
		}
		
		if($id_hipotesis && $id_hipotesis != "" && $id_hipotesis > 0)
		{
			$where .= "AND dh.hipotesisId = '$id_hipotesis' ";
		}
		else if($id_hipotesis == "NO")
		{
			//NADA
		}
		else
		{
			$where .= "AND dh.hipotesisId IS NULL ";
		}
		
        $sql = "SELECT ca  
                FROM 
                schemaBundle:InfoCriterioAfectado ca, 
                schemaBundle:InfoDetalle de,  
                schemaBundle:InfoDetalleHipotesis dh, 
                schemaBundle:InfoCaso c
        
                WHERE 
                ca.detalleId = de.id 
                AND de.detalleHipotesisId = dh.id 
                AND dh.casoId = c.id
                AND c.id = '$id_caso' 
				$where 
               ";
        
        $query = $this->_em->createQuery($sql);
        
        if($start != '' && $limit != ''){
	    $registros = $query->setFirstResult($start)->setMaxResults($limit)->getResult();	   
	}else{
	    $registros = $query->getResult();				   
	}	
        
        //$registros = $query->getResult();
        
        if ($registros) {
            $num = count($registros);  
            
            foreach ($registros as $data)
            {
				$EntityDetalleHipotesis = $this->_em->getRepository('schemaBundle:InfoDetalleHipotesis')->findOneById($data->getDetalleId()->getDetalleHipotesisId()); 
                
                $arr_encontrados[]=array('id_criterio_afectado' =>$data->getId(),
                                         'caso_id' => $EntityDetalleHipotesis->getCasoId()->getId(),
                                         'criterio' =>$data->getCriterio(),
                                         'opcion' =>$data->getOpcion());
            }
            $dataF =json_encode($arr_encontrados);
            $resultado= '{"total":"'.$num.'","encontrados":'.$dataF.'}';
            return $resultado;
        }
        else
        {
            $resultado= '{"total":"0","encontrados":[]}';
            return $resultado;
        }        
    }
	
	public function getRegistrosCriteriosTotalXCaso($id_caso, $retorna="Data",$start='',$limit='')
	{
		$fromWhere = " 
			      FROM 
			      schemaBundle:InfoCriterioAfectado ca, 
			      schemaBundle:InfoDetalle de, 
			      schemaBundle:InfoDetalleHipotesis dh, 
			      schemaBundle:InfoCaso c
	      
			      WHERE 
			      ca.detalleId = de.id 
			      AND de.detalleHipotesisId = dh.id 
			      AND dh.casoId = c.id
			      AND c.id = :idCaso
			      AND (dh.sintomaId is not null or (dh.hipotesisId is not null and dh.sintomaId is null)) 
			  ";
        
		if($retorna == "Data")
		{
			$selectedData = "ca.id as id_criterio, ca.criterio, ca.opcion, c.id as id_caso, de.id as id_detalle, dh.id as id_detalle_hipotesis ";
			$sql = "SELECT $selectedData $fromWhere ";		
			$query = $this->_em->createQuery($sql);
			
			$query->setParameter('idCaso',$id_caso);
			
			if($start != '' && $limit != ''){
			    $registros = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
			    return $registros;
			}else{
			    $registros = $query->getResult();			
			    return $registros;
			}	
														
			return $registros;
		}			
		else
		{
			$selectedCont = "count(ca) as cont ";
			$sql = "SELECT $selectedCont $fromWhere ";
			$query = $this->_em->createQuery($sql); 
			
			$query->setParameter('idCaso',$id_caso);
			
			$resultTotal = $query->getOneOrNullResult();
			$total = ($resultTotal ? ($resultTotal["cont"] ? $resultTotal["cont"] : 0) : 0);
		
			return $total;
		}		
	}
	
    public function generarJsonCriteriosTotalXCaso($id_caso,$start,$limit)
    {
        $arr_encontrados = array();
		
        $registros = $this->getRegistrosCriteriosTotalXCaso($id_caso, "Data" ,$start,$limit);
        $numero    = $this->getRegistrosCriteriosTotalXCaso($id_caso, "count" ,$start,$limit);
        
        if ($registros) {
           // $num = count($registros);  
            
            $id[] = array();
            
            foreach ($registros as $data)
            {
				$tipo = "";
				$nombre = "";	

				$detalle_hipotesis = $this->_em->getRepository('schemaBundle:InfoDetalleHipotesis')->findOneById($data["id_detalle_hipotesis"]); 					
				if($detalle_hipotesis->getSintomaId() != null)
				{
					$sintoma = $this->_em->getRepository('schemaBundle:AdmiSintoma')->findOneById($detalle_hipotesis->getSintomaId()); 
					
					$tipo = "Sintoma";
					$nombre = $sintoma ? $sintoma->getNombreSintoma() : "";
				}				
				if($detalle_hipotesis->getHipotesisId() != null && $detalle_hipotesis->getSintomaId() == null)
				{
					$hipotesis = $this->_em->getRepository('schemaBundle:AdmiHipotesis')->findOneById($detalle_hipotesis->getHipotesisId()); 
					
					$tipo = "Hipotesis";
					$nombre = $hipotesis ? $hipotesis->getNombreHipotesis() : "";
				}
		
		if(!in_array($data["opcion"],$id)){
				
			$arr_encontrados[]=array(
						'tipo' =>$tipo,
						'nombre' =>$nombre,
						'id_criterio_afectado' =>$data["id_criterio"],
						'caso_id' => $data["id_caso"],
						'criterio' =>$data["criterio"],
						'opcion' =>$data["opcion"]
						);
					 
		}
		
		$id[] = $data["opcion"];
            }
            $dataF =json_encode($arr_encontrados);
            $resultado= '{"total":"'.$numero.'","encontrados":'.$dataF.'}';
            return $resultado;
        }
        else
        {
            $resultado= '{"total":"0","encontrados":[]}';
            return $resultado;
        }        
    }
    
    public function generarJsonAfectadosXCaso($id_caso, $id_sintoma, $id_hipotesis)
    {
        $arr_encontrados = array();        
        $where = "";
		
		if($id_sintoma && $id_sintoma != "" && $id_sintoma > 0)
		{
			$where .= "AND dh.sintomaId  = '$id_sintoma' ";
		}
		else if($id_sintoma == "NO")
		{
			//NADA
		}
		else
		{
			$where .= "AND dh.sintomaId IS NULL ";
		}
		
		if($id_hipotesis && $id_hipotesis != "" && $id_hipotesis > 0)
		{
			$where .= "AND dh.hipotesisId = '$id_hipotesis' ";
		}
		else if($id_hipotesis == "NO")
		{
			//NADA
		}
		else
		{
			$where .= "AND dh.hipotesisId IS NULL ";
		}
				
        $sql = "SELECT pa  
                FROM 
                schemaBundle:InfoParteAfectada pa, 
                schemaBundle:InfoCriterioAfectado ca, 
                schemaBundle:InfoDetalle de, 
                schemaBundle:InfoDetalleHipotesis dh, 
                schemaBundle:InfoCaso c
        
                WHERE 
                pa.criterioAfectadoId = ca.id 
                AND ca.detalleId = de.id 
				AND pa.detalleId = ca.detalleId
				AND pa.detalleId = de.id
                AND de.detalleHipotesisId = dh.id 
                AND dh.casoId = c.id
                AND c.id = '$id_caso' 
				$where
               ";
			   
        $query = $this->_em->createQuery($sql);
        $registros = $query->getResult();
        
        if ($registros) {
            $num = count($registros);  
            
            foreach ($registros as $data)
            {
                $entityCriterioAfectado = $this->_em->find('schemaBundle:InfoCriterioAfectado', $data->getCriterioAfectadoId());
                $idCriterio = ($entityCriterioAfectado ? $entityCriterioAfectado->getId() : "");        

                $arr_encontrados[]=array('id' =>$data->getId(),
                                         'id_afectado' =>$data->getAfectadoId(),
                                         'id_criterio' =>$idCriterio,
                                         'id_afectado_descripcion' =>$data->getAfectadoDescripcionId(),
                                         'nombre_afectado' =>$data->getAfectadoNombre(),
                                         'descripcion_afectado' =>$data->getAfectadoDescripcion(),
                                         'json_afectados'=>''
                                        );
            }
            $dataF =json_encode($arr_encontrados);
            $resultado= '{"total":"'.$num.'","encontrados":'.$dataF.'}';
            return $resultado;
        }
        else
        {
            $resultado= '{"total":"0","encontrados":[]}';
            return $resultado;
        }        
    }
    
	
    public function getRegistrosAfectadosTotalXCaso($id_caso="", $tipoAfectado="", $retorna="Data",$start='',$limit='')
    {        
		$sql = "";	
		
		$query = $this->_em->createQuery();
		
		if($retorna == 'Data')
		{
			$sql     =    "SELECT 
					  pa.id as id_parte_afectada, 
					  pa.afectadoId, 
					  pa.afectadoNombre, 
					  pa.afectadoDescripcionId, 
					  pa.afectadoDescripcion, 
					  pa.tipoAfectado, 
					  ca.id as id_criterio, 
					  c.id as id_caso, 
					  de.id as id_detalle, 
					  dh.id as id_detalle_hipotesis  ";
		}
		else
		{
			$sql     =    "SELECT count(ca) as cont ";
		}
		
		$sql .= " 
			    FROM 
			    schemaBundle:InfoParteAfectada pa, 
			    schemaBundle:InfoCriterioAfectado ca, 
			    schemaBundle:InfoDetalle de, 
			    schemaBundle:InfoDetalleHipotesis dh, 
			    schemaBundle:InfoCaso c
	    
			    WHERE 
			    pa.criterioAfectadoId = ca.id 
			    AND ca.detalleId = de.id 
			    AND pa.detalleId = ca.detalleId
			    AND pa.detalleId = de.id
			    AND de.detalleHipotesisId = dh.id 
			    AND dh.casoId = c.id
			    AND c.id = :idCaso 				
			    AND (dh.sintomaId is not null or (dh.hipotesisId is not null and dh.sintomaId is null))   			     
		      ";																		 								
			
		$query->setParameter('idCaso',$id_caso);	
			
		if($tipoAfectado && $tipoAfectado!="")
		{
			$sql .= "AND lower(pa.tipoAfectado) = lower(:tipoAfectado) ";
			$query->setParameter('tipoAfectado',$tipoAfectado);
		}
		
		$query->setDQL($sql);
		
		if($retorna == 'Data')
		{			
			if($start != '' && $limit != ''){
			    $registros = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
			    return $registros;
			}else{
			    $registros = $query->getResult();			
			    return $registros;
			}			
		}
		else
		{		
			$resultTotal = $query->getOneOrNullResult();
			return ($resultTotal ? ($resultTotal["cont"] ? $resultTotal["cont"] : 0) : 0);
		}		
	}	
    
    /**
     * Metodo que devuelve la cantidad de ocurrencias de afectados en los casos de tipo:
     * 
     *  - Elemento
     *  - Cliente
     *  - Servicio
     *  - Empleado
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @since 09-01-2016
     * @version 1.0 
     * 
     * @param integer $intIdCaso
     * @return $arrayResultado
     */
    public function getTotalAfectadosPorTipo($intIdCaso)
    {        
        try
        {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $query = $this->_em->createNativeQuery(null, $rsm);           

            $strSql   = "SELECT
                            (SELECT COUNT(*)
                            FROM INFO_PARTE_AFECTADA PA
                            WHERE PA.TIPO_AFECTADO = :elemento
                            AND PA.DETALLE_ID      = DETALLE.ID_DETALLE
                            ) ELEMENTO,
                            (SELECT COUNT(*)
                            FROM INFO_PARTE_AFECTADA PA
                            WHERE PA.TIPO_AFECTADO = :cliente
                            AND PA.DETALLE_ID      = DETALLE.ID_DETALLE
                            ) CLIENTE,
                            (SELECT COUNT(*)
                            FROM INFO_PARTE_AFECTADA PA
                            WHERE PA.TIPO_AFECTADO = :empleado
                            AND PA.DETALLE_ID      = DETALLE.ID_DETALLE
                            ) EMPLEADO,
                            (SELECT COUNT(*)
                            FROM INFO_PARTE_AFECTADA PA
                            WHERE PA.TIPO_AFECTADO = :servicio
                            AND PA.DETALLE_ID      = DETALLE.ID_DETALLE
                            ) SERVICIO
                          FROM INFO_CASO CASO,
                            INFO_DETALLE_HIPOTESIS HIP,
                            INFO_DETALLE DETALLE,
                            INFO_PARTE_AFECTADA PARTE
                          WHERE CASO.ID_CASO           = HIP.CASO_ID
                          AND HIP.ID_DETALLE_HIPOTESIS = DETALLE.DETALLE_HIPOTESIS_ID
                          AND DETALLE.ID_DETALLE       = PARTE.DETALLE_ID
                          AND PARTE.DETALLE_ID        IS NOT NULL
                          AND (HIP.SINTOMA_ID         IS NOT NULL
                          OR (HIP.HIPOTESIS_ID        IS NOT NULL
                          AND HIP.SINTOMA_ID          IS NULL))
                          AND DETALLE.ID_DETALLE       =
                            (SELECT MIN(D.ID_DETALLE)
                            FROM INFO_DETALLE D
                            WHERE D.DETALLE_HIPOTESIS_ID = HIP.ID_DETALLE_HIPOTESIS
                            )
                          AND HIP.ID_DETALLE_HIPOTESIS =
                            (SELECT MIN(H.ID_DETALLE_HIPOTESIS)
                            FROM INFO_DETALLE_HIPOTESIS H
                            WHERE H.CASO_ID = CASO.ID_CASO
                            )
                          AND CASO.ID_CASO = :caso
                          GROUP BY DETALLE.ID_DETALLE";                        

            $rsm->addScalarResult('ELEMENTO', 'numElemento', 'integer');
            $rsm->addScalarResult('CLIENTE',  'numCliente',  'integer');
            $rsm->addScalarResult('SERVICIO', 'numServicio', 'integer');
            $rsm->addScalarResult('EMPLEADO', 'numEmpleado', 'integer');

            $query->setParameter('caso',$intIdCaso);  
            $query->setParameter('elemento','Elemento'); 
            $query->setParameter('cliente','Cliente'); 
            $query->setParameter('servicio','Servicio'); 
            $query->setParameter('empleado','Empleado'); 

            $query->setSQL($strSql);

            $arrayResultado = $query->getOneOrNullResult();           
        
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
        }
        
        return $arrayResultado;
    }

    /**
     * Método encargado de retornar los afectados del caso
     *
     * Costo 21
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 20-11-2018
     *
     * @param Array $arrayParametros [
     *                                  intIdCaso : Id del caso,
     *                               ]
     * @return Array
     */
    public function getAfectadosCaso($arrayParametros)
    {
        try
        {
            $objResultSetMap = new ResultSetMappingBuilder($this->_em);
            $objNativeQuery  = $this->_em->createNativeQuery(null, $objResultSetMap);

            $strSql = "SELECT IPA.ID_PARTE_AFECTADA       AS ID, ".
                             "IPA.AFECTADO_ID             AS ID_AFECTADO, ".
                             "ICA.ID_CRITERIO_AFECTADO    AS ID_CRITERIO, ".
                             "IPA.AFECTADO_DESCRIPCION_ID AS ID_AFECTADO_DESCRIPCION, ".
                             "INC.ID_CASO                 AS CASO_ID, ".
                             "IPA.TIPO_AFECTADO           AS TIPO_AFECTADO, ".
                             "IPA.AFECTADO_NOMBRE         AS NOMBRE_AFECTADO, ".
                             "IPA.AFECTADO_DESCRIPCION    AS DESCRIPCION_AFECTADO, ".
                             "CASE WHEN ASI.NOMBRE_SINTOMA IS NULL THEN ".
                                  "CASE WHEN AHI.NOMBRE_HIPOTESIS IS NOT NULL THEN 'Hipotesis' ".
                                  "ELSE NULL END ".
                             "ELSE 'Sintoma' END AS TIPO, ".
                             "CASE WHEN ASI.NOMBRE_SINTOMA IS NULL THEN ".
                                  "CASE WHEN AHI.NOMBRE_HIPOTESIS IS NOT NULL THEN AHI.NOMBRE_HIPOTESIS ".
                                  "ELSE NULL END ".
                             "ELSE ASI.NOMBRE_SINTOMA END AS NOMBRE ".
                          "FROM DB_SOPORTE.INFO_PARTE_AFECTADA    IPA, ".
                               "DB_SOPORTE.INFO_CRITERIO_AFECTADO ICA, ".
                               "DB_SOPORTE.INFO_DETALLE           IDE, ".
                               "DB_SOPORTE.INFO_DETALLE_HIPOTESIS IDH, ".
                               "DB_SOPORTE.INFO_CASO              INC, ".
                               "DB_SOPORTE.ADMI_SINTOMA           ASI, ".
                               "DB_SOPORTE.ADMI_HIPOTESIS         AHI ".
                      "WHERE IPA.CRITERIO_AFECTADO_ID = ICA.ID_CRITERIO_AFECTADO ".
                        "AND ICA.DETALLE_ID           = IDE.ID_DETALLE ".
                        "AND IPA.DETALLE_ID           = ICA.DETALLE_ID ".
                        "AND IPA.DETALLE_ID           = IDE.ID_DETALLE ".
                        "AND IDE.DETALLE_HIPOTESIS_ID = IDH.ID_DETALLE_HIPOTESIS ".
                        "AND IDH.CASO_ID              = INC.ID_CASO ".
                        "AND INC.ID_CASO              = :intIdCaso ".
                        "AND IDH.SINTOMA_ID           = ASI.ID_SINTOMA(+) ".
                        "AND IDH.HIPOTESIS_ID         = AHI.ID_HIPOTESIS(+) ".
                        "ORDER BY IPA.TIPO_AFECTADO, ICA.ID_CRITERIO_AFECTADO ASC";

            $objResultSetMap->addScalarResult('id'                      , 'id'                      , 'integer');
            $objResultSetMap->addScalarResult('ID_AFECTADO'             , 'id_afectado'             , 'integer');
            $objResultSetMap->addScalarResult('ID_CRITERIO'             , 'id_criterio'             , 'integer');
            $objResultSetMap->addScalarResult('ID_AFECTADO_DESCRIPCION' , 'id_afectado_descripcion' , 'integer');
            $objResultSetMap->addScalarResult('CASO_ID'                 , 'caso_id'                 , 'integer');
            $objResultSetMap->addScalarResult('TIPO_AFECTADO'           , 'tipo_afectado'           , 'string');
            $objResultSetMap->addScalarResult('NOMBRE_AFECTADO'         , 'nombre_afectado'         , 'string');
            $objResultSetMap->addScalarResult('DESCRIPCION_AFECTADO'    , 'descripcion_afectado'    , 'string');
            $objResultSetMap->addScalarResult('TIPO'                    , 'tipo'                    , 'string');
            $objResultSetMap->addScalarResult('NOMBRE'                  , 'nombre'                  , 'string');
            $objNativeQuery->setSQL($strSql);

            $objNativeQuery->setParameter("intIdCaso", $arrayParametros['intIdCaso']);

            $arrayResultado = $objNativeQuery->getResult();

            if (!empty($arrayResultado))
            {
                $strJsonEncontrados = json_encode($arrayResultado);
            }
            else
            {
                $strJsonEncontrados = '[]';
            }

            $intTotal = count($arrayResultado);

            $strResultado = '{"total":"'.$intTotal.'","encontrados":'.$strJsonEncontrados.'}';
        }
        catch (\Exception $objException)
        {
            error_log("Error en el metodo InfoCasoRepository.getAfectadosCaso -> ".$objException->getMessage());
            $strResultado = '{"total":"0","encontrados":[]}';
        }
        return $strResultado;
    }

    /**
     * Método encargado de retornar los criterios afectados de un caso
     *
     * Costo 16
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 20-11-2018
     *
     * @param Array $arrayParametros [
     *                                  intIdCaso : Id del caso,
     *                               ]
     * @return Array
     */
    public function getCriteriosAfectadosCaso($arrayParametros)
    {
        try
        {
            $objResultSetMap = new ResultSetMappingBuilder($this->_em);
            $objNativeQuery  = $this->_em->createNativeQuery(null, $objResultSetMap);

            $strSql = "SELECT ICA.ID_CRITERIO_AFECTADO AS ID_CRITERIO_AFECTADO, ".
                             "INC.ID_CASO              AS CASO_ID, ".
                             "ICA.CRITERIO             AS CRITERIO, ".
                             "ICA.OPCION               AS OPCION, ".
                             "CASE WHEN ASI.NOMBRE_SINTOMA IS NULL THEN ".
                                 "CASE WHEN AHI.NOMBRE_HIPOTESIS IS NOT NULL THEN 'Hipotesis' ".
                                 "ELSE NULL END ".
                             "ELSE 'Sintoma' END AS TIPO, ".
                             "CASE WHEN ASI.NOMBRE_SINTOMA IS NULL THEN ".
                                 "CASE WHEN AHI.NOMBRE_HIPOTESIS IS NOT NULL THEN AHI.NOMBRE_HIPOTESIS ".
                                 "ELSE NULL END ".
                             "ELSE ASI.NOMBRE_SINTOMA END AS NOMBRE ".
                          "FROM DB_SOPORTE.INFO_CRITERIO_AFECTADO ICA, ".
                               "DB_SOPORTE.INFO_DETALLE           IDE, ".
                               "DB_SOPORTE.INFO_DETALLE_HIPOTESIS IDH, ".
                               "DB_SOPORTE.INFO_CASO              INC, ".
                               "DB_SOPORTE.ADMI_SINTOMA           ASI, ".
                               "DB_SOPORTE.ADMI_HIPOTESIS         AHI ".
                      "WHERE ICA.DETALLE_ID           = IDE.ID_DETALLE ".
                        "AND IDE.DETALLE_HIPOTESIS_ID = IDH.ID_DETALLE_HIPOTESIS ".
                        "AND IDH.CASO_ID              = INC.ID_CASO ".
                        "AND INC.ID_CASO              = :intIdCaso ".
                        "AND IDH.SINTOMA_ID           = ASI.ID_SINTOMA(+) ".
                        "AND IDH.HIPOTESIS_ID         = AHI.ID_HIPOTESIS(+)";

            $objNativeQuery->setParameter("intIdCaso", $arrayParametros['intIdCaso']);

            $objResultSetMap->addScalarResult('ID_CRITERIO_AFECTADO' , 'id_criterio_afectado' , 'integer');
            $objResultSetMap->addScalarResult('CASO_ID'              , 'caso_id'              , 'integer');
            $objResultSetMap->addScalarResult('CRITERIO'             , 'criterio'             , 'string');
            $objResultSetMap->addScalarResult('OPCION'               , 'opcion'               , 'string');
            $objResultSetMap->addScalarResult('TIPO'                 , 'tipo'                 , 'string');
            $objResultSetMap->addScalarResult('NOMBRE'               , 'nombre'               , 'string');

            $objNativeQuery->setSQL($strSql);
            $arrayResultado = $objNativeQuery->getResult();

            if (!empty($arrayResultado))
            {
                $strJsonEncontrados = json_encode($arrayResultado);
            }
            else
            {
                $strJsonEncontrados = '[]';
            }

            $intTotal = count($arrayResultado);

            $strResultado = '{"total":"'.$intTotal.'","encontrados":'.$strJsonEncontrados.'}';
        }
        catch (\Exception $objException)
        {
            error_log("Error en el metodo InfoCasoRepository.getAfectadosCaso -> ".$objException->getMessage());
            $strResultado = '{"total":"0","encontrados":[]}';
        }
        return $strResultado;
    }
	
    public function generarJsonAfectadosTotalXCaso($id_caso,$start,$limit)
    {
        $arr_encontrados = array();   
			
        $registros = $this->getRegistrosAfectadosTotalXCaso($id_caso,'','Data',$start,$limit);
        $numero    = $this->getRegistrosAfectadosTotalXCaso($id_caso,'','count',$start,$limit);
		
        if ($registros) {                      
            
            $id[] = array();
            
            foreach ($registros as $data)
            {
			$tipo = "";
			$nombre = "";	
			
			$detalle_hipotesis = $this->_em->getRepository('schemaBundle:InfoDetalleHipotesis')->findOneById($data["id_detalle_hipotesis"]); 					
			if($detalle_hipotesis->getSintomaId() != null)
			{
				$sintoma = $this->_em->getRepository('schemaBundle:AdmiSintoma')->findOneById($detalle_hipotesis->getSintomaId()); 
				
				$tipo = "Sintoma";
				$nombre = $sintoma ? $sintoma->getNombreSintoma() : "";
			}				
			if($detalle_hipotesis->getHipotesisId() != null && $detalle_hipotesis->getSintomaId() == null)
			{
				$hipotesis = $this->_em->getRepository('schemaBundle:AdmiHipotesis')->findOneById($detalle_hipotesis->getHipotesisId()); 
				
				$tipo = "Hipotesis";
				$nombre = $hipotesis ? $hipotesis->getNombreHipotesis() : "";
			}	
										
			
			//if (!in_array($data["afectadoId"], $id)) {
					   
			      $arr_encontrados[]=array(
					      'tipo' =>$tipo,
					      'nombre' =>$nombre,
					      'id' =>$data["id_parte_afectada"],
					      'id_afectado' =>$data["afectadoId"],
					      'id_criterio' =>$data["id_criterio"],
					      'id_afectado_descripcion' =>$data["afectadoDescripcionId"],
					      'caso_id' => $data["id_caso"],
					      'tipo_afectado' =>$data["tipoAfectado"],
					      'nombre_afectado' =>$data["afectadoNombre"],
					      'descripcion_afectado' =>$data["afectadoDescripcion"]
					      );
					
// 			}
// 			
// 			$id[] = $data["afectadoId"];
            }
            $dataF =json_encode($arr_encontrados);
            $resultado= '{"total":"'.$numero.'","encontrados":'.$dataF.'}';
            return $resultado;
        }
        else
        {
            $resultado= '{"total":"0","encontrados":[]}';
            return $resultado;
        }        
    }
    

    /**
     * obtenerInfoElementoAfectadoPorCaso
     *
     * Metodo que obtiene la informacion de tipo/elemento de la afectación del caso creado
     *
     * @param integer  $idCaso
     * @param entityManager $emComercial
     *
     * @return string con respuesta de informacion concatenada
     * 
     * @author Allan Suarez  <arsuarez@telconet.ec>
     * @version 1.2 20-06-2016 - Elemento/Tramo afectado , se agrega condicional a subquery
     * 
     * @author Allan Suarez  <arsuarez@telconet.ec>
     * @version 1.1 22-05-2016 - Elemento/Tramo afectado para informacion de enlace de TN
     *
     * @author Allan Suarez  <arsuarez@telconet.ec>
     * @version 1.0 29-09-2014
     */
    public function obtenerInfoElementoAfectadoPorCaso($arrayParams)
    {
        $rsm   = new ResultSetMappingBuilder($this->_em);	      
	    $query = $this->_em->createNativeQuery(null, $rsm);
        
        $resultado = "";

        try
        {        
            if($arrayParams['prefijo'] == 'MD' || ( $arrayParams['tipoCaso'] != 'Tecnico' && $arrayParams['tipoCaso'] != 'Arcotel'))
            {
                $sql = "SELECT GET_AFECTADO_POR_CASO(:caso) as UM FROM DUAL";                
            }
            else
            {
                $sql = "SELECT 
                            DISTINCT(SELECT NVL (C.NOMBRE_TIPO_ELEMENTO, 'TIPO ELEMENTO')
                                || ':'
                                || NVL (C.ID_TIPO_ELEMENTO, 0)
                                || ':'
                                || NVL (A.NOMBRE_ELEMENTO, 'ELEMENTO')
                                || ':'
                                || NVL (A.ID_ELEMENTO, 0)
                              FROM INFO_ELEMENTO A,
                                ADMI_MODELO_ELEMENTO B,
                                ADMI_TIPO_ELEMENTO C
                              WHERE A.MODELO_ELEMENTO_ID = B.ID_MODELO_ELEMENTO
                              AND B.TIPO_ELEMENTO_ID     = C.ID_TIPO_ELEMENTO
                              AND A.ID_ELEMENTO          = ST.ELEMENTO_CLIENTE_ID) UM,
                              ST.INTERFACE_ELEMENTO_CONECTOR_ID OUT_ROSETA
                          FROM INFO_CASO A,
                            INFO_DETALLE_HIPOTESIS B,
                            INFO_DETALLE C,
                            INFO_PARTE_AFECTADA D,
                            INFO_SERVICIO S,
                            INFO_SERVICIO_TECNICO ST
                          WHERE A.ID_CASO            = B.CASO_ID
                          AND B.ID_DETALLE_HIPOTESIS = C.DETALLE_HIPOTESIS_ID
                          AND C.ID_DETALLE           = D.DETALLE_ID
                          AND A.ID_CASO              = :caso
                          AND S.PUNTO_ID             = D.AFECTADO_ID
                          AND S.ESTADO               IN (:estado)
                          AND S.ID_SERVICIO          = ST.SERVICIO_ID   
                          AND ST.TIPO_ENLACE         = :tipoEnlace                         
                            ";
                            
                
                $rsm->addScalarResult('OUT_ROSETA','rosetaOutid','integer');
                $query->setParameter('estado',array('Activo','In-Corte')); 
                $query->setParameter('tipoEnlace','PRINCIPAL'); 
            }            

            $rsm->addScalarResult('UM','ultimaMilla','string');
            $query->setParameter('caso',$arrayParams['idCaso'] );	        
            $query->setSQL($sql);	

            $arrayResultado = $query->getOneOrNullResult();

            if($arrayParams['prefijo'] == 'MD' || $arrayParams['tipoCaso'] != 'Tecnico')
            {
                $resultado = $arrayResultado['ultimaMilla'];
            }
            else
            {
                if($arrayResultado)
                {
                    if($arrayResultado['rosetaOutid'])
                    {
                        $arrayParamGetElemento['interfaceElementoConectorId'] = $arrayResultado['rosetaOutid'];
                        $arrayParamGetElemento['tipoElemento']                = "CPE";
                        $arrayResultado = $arrayParams['em']->getRepository("schemaBundle:InfoElemento")
                                                            ->getElementoClienteByTipoElemento($arrayParamGetElemento);
                        if($arrayResultado['msg']=='FOUND')
                        {
                            $objInfoElemento     = $arrayParams['em']->getRepository("schemaBundle:InfoElemento")->find($arrayResultado['idElemento']);
                            $objAdmiTipoElemento = $arrayParams['em']->getRepository("schemaBundle:AdmiTipoElemento")
                                                 ->findOneBy(array('id'=>$objInfoElemento->getModeloElementoId()->getTipoElementoId()->getId()));
                            
                            if($objAdmiTipoElemento)
                            {
                                $resultado = $objAdmiTipoElemento->getNombreTipoElemento().":".
                                             $objAdmiTipoElemento->getId().":".
                                             $objInfoElemento->getNombreElemento().":".
                                             $objInfoElemento->getId();
                            }                                                        
                        }                        
                    }
                    else
                    {
                        $resultado = $arrayResultado['ultimaMilla'];
                    }
                }
            }        
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());            
        }
        
        return $resultado;
}
    
        /**
      * getJsonClientesNotificarCasos
      *
      * Controlador que obtiene el json de los registros a mostrar en el grid de clientes por modulo Soporte a notificar                                       
      *
      * @return json con registros 
      *
      * @author Allan Suárez <arsuarez@telconet.ec>
      * @version 1.0 13-10-2014
      */
    public function getJsonClientesNotificarCasos($parametros, $start , $limit)
    {
        $arr_encontrados = array();   
			
        $registros = $this->getClientesNotificarCasos($parametros,$start,$limit,'data');
        $numero    = $this->getClientesNotificarCasos($parametros,$start,$limit,'cont');
		
        if ($registros) {                      
            
            $arr_encontrados = array();
            
            foreach ($registros as $data)
            {														   
			      $arr_encontrados[]=array(
					      'idPunto'     =>$data["idPunto"],
					      'login'       =>$data["login"],
					      'cliente'     =>$data["cliente"],
					      'numeroCaso'  =>$data["numeroCaso"],
					      'estadoCaso'  =>$data["estadoCaso"],
					      'oficina'     =>$data["oficina"],
					      'departamento'=>$data["departamento"]					      
					      );

            }
            $json =json_encode($arr_encontrados);
            $resultado= '{"total":"'.$numero[0]['cont'].'","encontrados":'.$json.'}';
            return $resultado;
        }
        else
        {
            $resultado= '{"total":"0","encontrados":[]}';
            return $resultado;
        }  
    }
    
     /**
      * getClientesNotificarCasos
      *
      * Método que obtiene los registros del query de busqueda de los clientes a notificar por Casos                                      
      *
      * @return json con registros 
      *
      * @author Allan Suárez <arsuarez@telconet.ec>
      * @version 1.0 13-10-2014
      */
     public function getClientesNotificarCasos($parametros, $start, $limit, $tipo)
    {
        $sql = "";
        $query = $this->_em->createQuery();

        switch($tipo)
        {
            case 'cont':
                $sql .= " SELECT count(a) as cont ";
                break;
            case 'data':
                $sql .= " SELECT 
                           a.id as idCaso ,
                           a.numeroCaso as numeroCaso,
                           g.estado as estadoCaso ,
                           i.afectadoId as idPunto,
                           i.afectadoNombre as login,
                           i.afectadoDescripcion as cliente,
                           e.nombreOficina as oficina,
                           d.nombreDepartamento as departamento ";
                break;
            default :
                $sql .= " SELECT a ";
        }

        $sql .= " FROM  
                  schemaBundle:InfoCaso a,
                  schemaBundle:InfoDetalleHipotesis b,
                  schemaBundle:InfoCasoAsignacion c,
                  schemaBundle:AdmiDepartamento d,
                  schemaBundle:InfoOficinaGrupo e,
                  schemaBundle:AdmiCanton f,
                  schemaBundle:InfoCasoHistorial g,
                  schemaBundle:InfoDetalle h,
                  schemaBundle:InfoParteAfectada i,
                  schemaBundle:InfoPersonaEmpresaRol j 
                  
                  WHERE
                  
                  a.id is not null and
                  a.id        =     g.casoId and
                  a.id        =     b.casoId and
                  g.id        =     (SELECT 
                                        max(hist.id)
                                     FROM 
                                        schemaBundle:InfoCasoHistorial hist 
                                     WHERE 
                                        hist.casoId = g.casoId
                                     ) and
                  b.id        =     (SELECT 
                                        max(hip.id) 
                                     FROM 
                                        schemaBundle:InfoDetalleHipotesis hip 
                                     WHERE 
                                        hip.casoId = b.casoId and
                                        hip.hipotesisId is not null and
                                        hip.sintomaId is not null
                                     ) and
                  b.id         =    c.detalleHipotesisId and
                  c.id         =    (SELECT 
                                        max(asig.id) 
                                     FROM 
                                        schemaBundle:InfoCasoAsignacion asig 
                                     WHERE 
                                        asig.detalleHipotesisId = c.detalleHipotesisId                                                                                
                                     ) and
                  h.detalleHipotesisId = b.id and
                  h.id                 = i.detalleId and
                  d.id                 = j.departamentoId and
                  j.id                 = c.personaEmpresaRolId and
                  e.cantonId           = f.id and
                  e.id                 = j.oficinaId and 
                  a.empresaCod         = :empresa ";

        $query->setParameter('empresa', $parametros['empresaUsuario']);

        if($parametros['departamentoAsignado'] && $parametros['departamentoAsignado'] != '')
        {
            $sql .= ' and  d.id = :departamento ';
            $query->setParameter('departamento', $parametros['departamentoAsignado']);
        }

        if($parametros['ciudadAsignado'] && $parametros['ciudadAsignado'] != '')
        {
            $sql .= ' and  f.id = :canton ';
            $query->setParameter('canton', $parametros['ciudadAsignado']);
        }

        if($parametros['caso'] && $parametros['caso'] != '')
        {
            $sql .= ' and  a.id = :caso ';
            $query->setParameter('caso', $parametros['caso']);
        }

        $estado = 'Asignado';
        $sql.= " and lower(g.estado) like lower(:estado) ";

        if($parametros['estadoCaso'] && $parametros['estadoCaso'] != '')
        {
            $query->setParameter('estado', $parametros['estadoCaso']);
        }
        else
        {
            $query->setParameter('estado', $estado);
        }

        $sql.= " and i.tipoAfectado = :cliente order by a.id DESC ";

        $query->setParameter('cliente', 'Cliente');

        $query->setDQL($sql);

        if($tipo == 'data')
        {
            if($start != '' && $limit != '')
            {
                $datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
            }
            else
            {
                $datos = $query->getResult();
            }
        }
        else
        {
            $datos = $query->getResult();
        }

        return $datos;

    }
    
    /**
     * 
     * Metodo que obtiene el Json de la informacion de:
     * 
     *  - Casos relacionados a los clientes seleccionados (casos)
     *  - Lista de la disponibilidad general de cada cliente en el rango de tiempo seleccionado (disponibilidad)
     *  - Resumen Total de procentaje y tiempos de todos los clientes procesados (resumen)
     * 
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 
     * @since 17-12-2015
     * 
     * @param $arrayParametros [ rangoDesde , rangoHasta , start , limit , puntos , servicios , service, accion ]     
     * @return $jsonData
     */
    public function getJsonInfoSla($arrayParametros)
    {
        switch($arrayParametros['accion'])
        {
            case 'casos':
                $arrayResultado = $this->getCasosPorClienteSla($arrayParametros);                  
                break;
            case 'disponibilidad':
                $arrayResultado = $this->getDisponibilidadClientesSla($arrayParametros);                                   
                break;
            case 'resumen':
                $arrayResultado = $this->getResumenPorcentajesClientesSla($arrayParametros);                                
                break;                       
        }
                
        $total = $arrayResultado['total'];
        
        if($total>0)
        {                    
            $resultado = $arrayResultado['resultado'];

            if($resultado)
            {
                foreach($resultado as $data)
                {
                    switch($arrayParametros['accion'])
                    {
                        case 'casos':
                            $arrayEncontrados[] = array( 
                                                "idCaso"           => $data['idCaso'],
                                                "numeroCaso"       => $data['numeroCaso'],
                                                "fechaIncidencia"  => $data['fechaIncidencia'],
                                                "tituloInicial"    => $data['tituloInicial'],
                                                "loginCaso"        => $data['loginCaso'],
                                                "estadoCaso"       => $data['estadoCaso'],                                            
                                                "tiempoSolucion"   => $data['tiempoSolucion'],
                                                "servicioAfectado" => $data['servicioAfectado'],
                                                "nombrePunto"      => $data['nombrePunto']                                            
                            );
                            break;
                        case 'disponibilidad':                                                
                            $arrayEncontrados[] = array( 
                                                "puntoDisponibilidad"        => $data['puntoDisponibilidad'],
                                                "loginDisponibilidad"        => $data['loginDisponibilidad'],
                                                "porcentajeDisponibilidad"   => 
                                                $arrayParametros['service']->completarDecimalesPorcentajes($data['porcentajeDisponibilidad']).'%',
                                                "minutosTotalDisponibilidad"=> $data['minutosTotalDisponibilidad']                                                                                      
                            );
                            break;
                        case 'resumen':
                             $arrayEncontrados[] = array( 
                                                "rango"          => $data['rango'],
                                                "tiempo"         => $data['tiempo'],
                                                "totalPuntos"   => $data['totalPuntos'],
                                                "perdida"        => $arrayParametros['service']
                                                                    ->completarDecimalesPorcentajes($data['perdida']).'%',
                                                "disponibilidad" => $arrayParametros['service']
                                                                    ->completarDecimalesPorcentajes($data['disponibilidad']).'%'                                                                                     
                             );
                            break;                       
                        default :
                            $arrayEncontrados = array();
                            break;
                    }               
                }

                $arrayRespuesta = array('total'=> $total , 'encontrados' => $arrayEncontrados);                                                
            }
            else
            {
                $arrayRespuesta = array('total'=> 0 , 'encontrados' => '[]');                                                          
            }
        }
        else
        {
            $arrayRespuesta = array('total'=> 0 , 'encontrados' => '[]');                                            
        }
        
        $jsonData       = json_encode($arrayRespuesta);
        return $jsonData; 
    }
        
    
    /**
     * Metodo que obtiene el resultado de los casos de los clientes a calcular el Sla 
     * 
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 
     * @since 15-12-2015  
     * 
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.1 - Se corrije el parseo de fecha de consulta
     * @since 10-07-2020
     * 
     * @param Array $arrayParametros [ rangoDesde , rangoHasta , start , limit , puntos , servicios , service, accion ]       
     * @return Array $arrayResultado [ total, resultado ]
     */
    public function getCasosPorClienteSla($arrayParametros)
    {
        $arrayResultado = array();
        
        try
        {
            $objRsm   = new ResultSetMappingBuilder($this->_em);	      
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);	             

            $strSelectWhere = "";        

            if(isset($arrayParametros['servicios']) && $arrayParametros['servicios'] != 0 )
            {
                $strSelectWhere .= " AND PARTE.AFECTADO_ID  IN (:servicios) ";
                $objQuery->setParameter('servicios', array_values($arrayParametros['servicios']));  
            }

            $strSelectCont = "SELECT COUNT(*) CONT ";

            $strSelectData = "SELECT 
                                DISTINCT(CASO.ID_CASO) ID_CASO,
                                CASO.NUMERO_CASO,                    
                                HISTORIAL.ESTADO,
                                CASO.FE_APERTURA,
                                NVL((SELECT HIST.FE_CREACION
                                FROM INFO_DETALLE DET,
                                  INFO_DETALLE_HISTORIAL HIST
                                WHERE DET.DETALLE_HIPOTESIS_ID = DETALLE_HIPOTESIS.ID_DETALLE_HIPOTESIS
                                AND DET.ID_DETALLE             = HIST.DETALLE_ID
                                AND HIST.ESTADO                = 'Finalizada'
                                AND DET.ES_SOLUCION            = 'S'
                                AND DET.ID_DETALLE             =
                                  (SELECT MIN(ID_DETALLE)
                                  FROM INFO_DETALLE D
                                  WHERE D.DETALLE_HIPOTESIS_ID = DET.DETALLE_HIPOTESIS_ID
                                  AND D.ES_SOLUCION            = 'S'
                                  )
                                ),HISTORIAL.FE_CREACION) FE_SOLUCION,
                                CASO.TITULO_INI,
                                HIPOTESIS.NOMBRE_HIPOTESIS VERSION_FINAL,
                                PUNTO.LOGIN,
                                TIEMPO.TIEMPO_TOTAL_CASO_SOLUCION TIEMPO,                    
                                NVL(
                                (SELECT PARTE.AFECTADO_ID
                                FROM INFO_DETALLE DET,
                                  INFO_PARTE_AFECTADA PARTE
                                WHERE DET.ID_DETALLE    = PARTE.DETALLE_ID
                                AND DET.ID_DETALLE      = DETALLE.ID_DETALLE
                                AND PARTE.TIPO_AFECTADO = :servicio
                                $strSelectWhere
                                ), 0) SERVICIO_AFECTADO,
                                NVL(PERSONA.RAZON_SOCIAL,PERSONA.NOMBRES || ' ' || PERSONA.APELLIDOS) NOMBRE_PUNTO";      

            $strSql = "
                      FROM INFO_CASO CASO,
                        INFO_CASO_HISTORIAL HISTORIAL,
                        INFO_PUNTO PUNTO,
                        INFO_DETALLE_HIPOTESIS DETALLE_HIPOTESIS,
                        INFO_DETALLE DETALLE,
                        INFO_PARTE_AFECTADA PARTE_AFECTADA,
                        INFO_CASO_TIEMPO_ASIGNACION TIEMPO,
                        INFO_PERSONA_EMPRESA_ROL EMPRESA_ROL,
                        INFO_PERSONA PERSONA,
                        ADMI_HIPOTESIS HIPOTESIS
                      WHERE CASO.ID_CASO                         = DETALLE_HIPOTESIS.CASO_ID
                      AND DETALLE_HIPOTESIS.ID_DETALLE_HIPOTESIS = DETALLE.DETALLE_HIPOTESIS_ID
                      AND DETALLE.ID_DETALLE                     = PARTE_AFECTADA.DETALLE_ID
                      AND EMPRESA_ROL.ID_PERSONA_ROL             = PUNTO.PERSONA_EMPRESA_ROL_ID
                      AND EMPRESA_ROL.PERSONA_ID                 = PERSONA.ID_PERSONA
                      AND CASO.ID_CASO                           = TIEMPO.CASO_ID
                      AND CASO.ID_CASO                           = HISTORIAL.CASO_ID
                      AND CASO.TITULO_FIN_HIP                    = HIPOTESIS.ID_HIPOTESIS
                      AND PUNTO.ID_PUNTO                         = PARTE_AFECTADA.AFECTADO_ID
                      AND TRUNC(CASO.FE_APERTURA)               >= :rangoDesde
                      AND TRUNC(CASO.FE_APERTURA)               <= :rangoHasta               
                      AND PARTE_AFECTADA.TIPO_AFECTADO = :cliente
                      AND PUNTO.ID_PUNTO              IN (:puntos)   
                      AND HISTORIAL.ESTADO             = :estado ORDER BY PUNTO.LOGIN";                                    

            $objRsm->addScalarResult('ID_CASO','idCaso','integer');
            $objRsm->addScalarResult('NUMERO_CASO','numeroCaso','string');
            $objRsm->addScalarResult('ESTADO','estadoCaso','string');
            $objRsm->addScalarResult('FE_APERTURA','fechaIncidencia','string');
            $objRsm->addScalarResult('FE_SOLUCION','fechaSolucion','string');
            $objRsm->addScalarResult('TITULO_INI','tituloInicial','string');
            $objRsm->addScalarResult('VERSION_FINAL','versionFinal','string');
            $objRsm->addScalarResult('LOGIN','loginCaso','string');
            $objRsm->addScalarResult('TIEMPO','tiempoSolucion','integer');
            $objRsm->addScalarResult('SERVICIO_AFECTADO','servicioAfectado','integer');
            $objRsm->addScalarResult('NOMBRE_PUNTO','nombrePunto','string');
            $objRsm->addScalarResult('CONT','cont','integer');

            $objQuery->setParameter('servicio', 'Servicio'); 
            $objQuery->setParameter('cliente', 'Cliente'); 
            $objQuery->setParameter('estado', 'Cerrado'); 
            $objQuery->setParameter('esSolucion', 'S'); 
            $objQuery->setParameter('finalizada', 'Finalizada'); 
            $objQuery->setParameter('puntos', array_values($arrayParametros['puntos']));  

            $arrayDateDesde   = explode("-", $arrayParametros['rangoDesde']);
            $objFechaSqlDesde = strtotime(date("Y-m-d", strtotime($arrayDateDesde[2] . "-" . $arrayDateDesde[1] . "-" . $arrayDateDesde[0])));
            $strRangoDesde = date("Y/m/d", $objFechaSqlDesde);
            $objQuery->setParameter('rangoDesde', trim($strRangoDesde)); 

            $arrayDateHasta = explode("-", $arrayParametros['rangoHasta']);
            $objFechaSqlHasta = strtotime(date("Y-m-d", strtotime($arrayDateHasta[2] . "-" . $arrayDateHasta[1] . "-" . $arrayDateHasta[0])));
            $strRangoHasta = date("Y/m/d", $objFechaSqlHasta);
            $objQuery->setParameter('rangoHasta', trim($strRangoHasta)); 

            $objQuery->setSQL($strSelectCont.$strSql);	   

            $arrayResultado['total'] = $objQuery->getSingleScalarResult();        

            $objQuery->setSQL($strSelectData.$strSql);	   

            $objQuery = $this->setQueryLimit($objQuery,$arrayParametros['start'],$arrayParametros['limit']);

            $arrayResultado['resultado'] = $objQuery->getArrayResult();
        
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
        }
        
        return $arrayResultado;
    }
        
    /**
     * Metodo que obtiene los registros del porcentaje de disponibilidad general de lo clientes para Sla
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2
     * @since 23-11-2016 - Se realiza ajustes en el calculo de la disponibilidad que muestra el SLA, para que no se tome en
     *                     cuenta los casos que fueron SIN AFECTACION
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 Se cambia el valor del parametro de la variable tipoAfectacion de "caida" por "CAIDA"
     *
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 
     * @since 17-12-2015
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.3 09-07-2020 - Se agrega un filtro adicional en el query para no considerar
     *                           los tipo de afectación (SINAFECTACION).
     * 
     * @param Array $arrayParametros [ rangoDesde , rangoHasta , start , limit , puntos , servicios , service, accion ]       
     * @return Array $arrayResultado [ total , resultado ]
     */
    public function getDisponibilidadClientesSla($arrayParametros)
    {        
        $arrayResultado = array();
        
        try
        {
            $rsm   = new ResultSetMappingBuilder($this->_em);	      
            $query = $this->_em->createNativeQuery(null, $rsm);	                             

            $strSelectCont = "SELECT COUNT(*) CONT FROM  ";

            $strSql = "SELECT NVL(PERSONA.RAZON_SOCIAL,PERSONA.NOMBRES
                                || ' '
                                || PERSONA.APELLIDOS) NOMBRE_PUNTO,
                                PUNTO.LOGIN,
                                SPKG_GENERACION_SLA.FN_CALCULAR_DISPONIBILIDAD_SLA(SUM(NVL(TIEMPO.TIEMPO_TOTAL_CASO_SOLUCION,0)),
                                    PUNTO.ID_PUNTO,:tipoReporte,:tipoAfectacion,:desde,:hasta) DISPONIBILIDAD,
                                SUM(NVL(TIEMPO.TIEMPO_TOTAL_CASO_SOLUCION,0)) TIEMPO_TOTAL,
                                SPKG_GENERACION_SLA.FN_GET_CASOS_CLIENTE_SLA(PUNTO.ID_PUNTO,:desde,:hasta) CASOS ";
            $strSql .= "
                  FROM INFO_PUNTO PUNTO
                    LEFT JOIN INFO_PERSONA_EMPRESA_ROL EMPRESA_ROL
                    ON EMPRESA_ROL.ID_PERSONA_ROL = PUNTO.PERSONA_EMPRESA_ROL_ID
                    LEFT JOIN INFO_PERSONA PERSONA
                    ON PERSONA.ID_PERSONA = EMPRESA_ROL.PERSONA_ID
                    LEFT JOIN INFO_PARTE_AFECTADA PARTE_AFECTADA
                    ON PUNTO.ID_PUNTO                = PARTE_AFECTADA.AFECTADO_ID
                    AND PARTE_AFECTADA.TIPO_AFECTADO = :cliente
                    LEFT JOIN INFO_DETALLE DETALLE
                    ON PARTE_AFECTADA.DETALLE_ID = DETALLE.ID_DETALLE
                    LEFT JOIN INFO_DETALLE_HIPOTESIS DETALLE_HIPOTESIS
                    ON DETALLE_HIPOTESIS.ID_DETALLE_HIPOTESIS = DETALLE.DETALLE_HIPOTESIS_ID
                    LEFT JOIN INFO_CASO CASO
                    ON CASO.ID_CASO              = DETALLE_HIPOTESIS.CASO_ID
                    AND TRUNC(CASO.FE_APERTURA) >= :rangoDesde
                    AND TRUNC(CASO.FE_APERTURA) <= :rangoHasta
                    AND CASO.TIPO_AFECTACION    != :sinTipoAfectacion
                    LEFT JOIN INFO_CASO_HISTORIAL HISTORIAL
                    ON HISTORIAL.CASO_ID = CASO.ID_CASO
                    AND HISTORIAL.ESTADO = :estado
                    LEFT JOIN INFO_CASO_TIEMPO_ASIGNACION TIEMPO
                    ON TIEMPO.CASO_ID     = CASO.ID_CASO                
                    WHERE PUNTO.ID_PUNTO IN (:puntos)
                  GROUP BY PUNTO.ID_PUNTO,
                    PUNTO.LOGIN,
                    PERSONA.RAZON_SOCIAL,
                    PERSONA.NOMBRES,
                    PERSONA.APELLIDOS";                

            $rsm->addScalarResult('NOMBRE_PUNTO','puntoDisponibilidad','string');
            $rsm->addScalarResult('LOGIN','loginDisponibilidad','string');
            $rsm->addScalarResult('DISPONIBILIDAD','porcentajeDisponibilidad','float');
            $rsm->addScalarResult('TIEMPO_TOTAL','minutosTotalDisponibilidad','integer');
            $rsm->addScalarResult('CASOS','casos','string');
            $rsm->addScalarResult('CONT','cont','integer');

            $query->setParameter('cliente', 'Cliente'); 
            $query->setParameter('estado', 'Cerrado');                 
            $query->setParameter('tipoReporte', 'consolidado');                 
            $query->setParameter('tipoAfectacion', 'CAIDA');
            $query->setParameter('sinTipoAfectacion', 'SINAFECTACION');
            $query->setParameter('puntos', array_values($arrayParametros['puntos']));          
            $query->setParameter('desde', $arrayParametros['rangoDesde']);  
            $query->setParameter('hasta', $arrayParametros['rangoHasta']);  
 
            $dateDesde = explode("-", $arrayParametros['rangoDesde']);
            $fechaSqlDesde = strtotime(date("Y-m-d", strtotime($dateDesde[2] . "-" . $dateDesde[1] . "-" . $dateDesde[0])));
            $rangoDesde = date("Y/m/d", $fechaSqlDesde);
            $query->setParameter('rangoDesde', trim($rangoDesde)); 

            $dateHasta = explode("-", $arrayParametros['rangoHasta']);
            $fechaSqlHasta = strtotime(date("Y-m-d", strtotime($dateHasta[2] . "-" . $dateHasta[1] . "-" . $dateHasta[0])));
            $rangoHasta = date("Y/m/d", $fechaSqlHasta);
            $query->setParameter('rangoHasta', trim($rangoHasta)); 

            $query->setSQL($strSelectCont." ( ".$strSql." )");	   

            $arrayResultado['total'] = $query->getSingleScalarResult();

            $query->setSQL($strSql);	   

            $objQuery = $this->setQueryLimit($query,$arrayParametros['start'],$arrayParametros['limit']);

            $arrayResultado['resultado'] = $objQuery->getArrayResult();
        
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
        }
        
        return $arrayResultado;
    }        
            
    /**
     * Metodo que obtiene el resultado los porcentajes totales de la disponibilidad de los clientes en calculo de Sla
     * 
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 
     * @since 17-12-2015
     * 
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1
     * @since 23-11-2016 - Se realiza ajustes en el calculo de la disponibilidad que muestra el SLA, para que no se tome en
     *                     cuenta los casos que fueron SIN AFECTACION
     * 
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.2 - Se corrije el parseo de fecha de consulta
     * @since 10-07-2020
     *
     * @param Array $arrayParametros [ rangoDesde , rangoHasta , start , limit , puntos , servicios , service, accion ]       
     * @return Array $arrayResultado [ total , resultado ]
     */
    public function getResumenPorcentajesClientesSla($arrayParametros)
    {
        $arrayResultado = array();
        
        try
        {
            $objRsm   = new ResultSetMappingBuilder($this->_em);	      
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);	           

            $strSelectCont = "SELECT COUNT(*) CONT ";

            $strSelectData = "SELECT 
                                NVL(SUM(RESUMEN.TIEMPO_TOTAL),0) TIEMPO_TOTAL,
                                ROUND((TO_DATE(:hasta,'YYYY/MM/DD') - TO_DATE(:desde,'YYYY/MM/DD'))*1440) RANGO,
                                COUNT(RESUMEN.ID_PUNTO) NUMERO_PUNTOS,
                                SPKG_GENERACION_SLA.FN_CALCULAR_DISPONIBILIDAD_SLA(SUM(NVL(RESUMEN.TIEMPO_TOTAL_AFECTACION,0)),0,
                                    :tipoReporte,:tipoAfectacion,:desde,:hasta) DISPONIBILIDAD, 
                                100 - SPKG_GENERACION_SLA.FN_CALCULAR_DISPONIBILIDAD_SLA(SUM(NVL(RESUMEN.TIEMPO_TOTAL_AFECTACION,0)),0,
                                    :tipoReporte,:tipoAfectacion,:desde,:hasta) PERDIDA";                
            $strSql = "               
                      FROM
                        (SELECT PUNTO.ID_PUNTO,
                          (SUM(NVL(TIEMPO.TIEMPO_TOTAL_CASO_SOLUCION,0))) TIEMPO_TOTAL,
                          (SUM(NVL(TIEMPO.TIEMPO_TOTAL_CASO_SOLUCION,0)) -
                          (
                                SELECT NVL(SUM(NVL(INFOCASOTIEMPOASIGNACION.TIEMPO_TOTAL_CASO_SOLUCION,0)),0)
                                FROM INFO_CASO INFOCASO,
                                INFO_DETALLE_HIPOTESIS INFODETALLEHIPOTESIS,INFO_DETALLE INFODETALLE,
                                INFO_CASO_HISTORIAL INFOCASOHISTORIAL,INFO_CASO_TIEMPO_ASIGNACION INFOCASOTIEMPOASIGNACION
                                WHERE INFOCASO.ID_CASO = INFODETALLEHIPOTESIS.CASO_ID
                                AND INFODETALLEHIPOTESIS.ID_DETALLE_HIPOTESIS = INFODETALLE.DETALLE_HIPOTESIS_ID
                                AND INFOCASO.ID_CASO = INFOCASOHISTORIAL.CASO_ID
                                AND INFOCASOTIEMPOASIGNACION.CASO_ID = INFOCASO.ID_CASO
                                AND INFODETALLE.ID_DETALLE IN (
                                  SELECT DETALLE_ID FROM INFO_PARTE_AFECTADA A
                                WHERE AFECTADO_ID = PUNTO.ID_PUNTO
                                AND TIPO_AFECTADO = :cliente)
                                AND INFOCASOHISTORIAL.ESTADO = :estado
                                AND INFOCASO.FE_APERTURA >= :rangoDesde
                                AND INFOCASO.FE_APERTURA <= :rangoHasta
                                AND INFOCASO.TIPO_AFECTACION = :afectacion

                           )) TIEMPO_TOTAL_AFECTACION
                        FROM INFO_PUNTO PUNTO
                        LEFT JOIN INFO_PERSONA_EMPRESA_ROL EMPRESA_ROL
                        ON EMPRESA_ROL.ID_PERSONA_ROL = PUNTO.PERSONA_EMPRESA_ROL_ID
                        LEFT JOIN INFO_PERSONA PERSONA
                        ON PERSONA.ID_PERSONA = EMPRESA_ROL.PERSONA_ID
                        LEFT JOIN INFO_PARTE_AFECTADA PARTE_AFECTADA
                        ON PUNTO.ID_PUNTO                = PARTE_AFECTADA.AFECTADO_ID
                        AND PARTE_AFECTADA.TIPO_AFECTADO = :cliente
                        LEFT JOIN INFO_DETALLE DETALLE
                        ON PARTE_AFECTADA.DETALLE_ID = DETALLE.ID_DETALLE
                        LEFT JOIN INFO_DETALLE_HIPOTESIS DETALLE_HIPOTESIS
                        ON DETALLE_HIPOTESIS.ID_DETALLE_HIPOTESIS = DETALLE.DETALLE_HIPOTESIS_ID
                        LEFT JOIN INFO_CASO CASO
                        ON CASO.ID_CASO              = DETALLE_HIPOTESIS.CASO_ID
                        AND TRUNC(CASO.FE_APERTURA) >= :rangoDesde
                        AND TRUNC(CASO.FE_APERTURA) <= :rangoHasta
                        LEFT JOIN INFO_CASO_HISTORIAL HISTORIAL
                        ON HISTORIAL.CASO_ID = CASO.ID_CASO
                        AND HISTORIAL.ESTADO = :estado
                        LEFT JOIN INFO_CASO_TIEMPO_ASIGNACION TIEMPO
                        ON TIEMPO.CASO_ID     = CASO.ID_CASO                
                        WHERE PUNTO.ID_PUNTO IN (:puntos)
                        GROUP BY PUNTO.ID_PUNTO
                        ) RESUMEN";

            $objRsm->addScalarResult('RANGO','rango','integer');
            $objRsm->addScalarResult('PERDIDA','perdida','float');
            $objRsm->addScalarResult('DISPONIBILIDAD','disponibilidad','float');
            $objRsm->addScalarResult('NUMERO_PUNTOS','totalPuntos','integer');
            $objRsm->addScalarResult('TIEMPO_TOTAL','tiempo','integer');
            $objRsm->addScalarResult('CONT','cont','integer');

            $objQuery->setParameter('cliente', 'Cliente'); 
            $objQuery->setParameter('estado', 'Cerrado');          
            $objQuery->setParameter('tipoReporte', 'consolidado');                 
            $objQuery->setParameter('tipoAfectacion', 'caida');
            $objQuery->setParameter('afectacion', 'SINAFECTACION');
            $objQuery->setParameter('puntos', array_values($arrayParametros['puntos']));          
            $objQuery->setParameter('desde', $arrayParametros['rangoDesde']);  
            $objQuery->setParameter('hasta', $arrayParametros['rangoHasta']);  

            $arrayDateDesde   = explode("-", $arrayParametros['rangoDesde']);
            $objFechaSqlDesde = strtotime(date("Y-m-d", strtotime($arrayDateDesde[2] . "-" . $arrayDateDesde[1] . "-" . $arrayDateDesde[0])));
            $strRangoDesde = date("Y/m/d", $objFechaSqlDesde);
            $objQuery->setParameter('rangoDesde', trim($strRangoDesde)); 

            $arrayDateHasta = explode("-", $arrayParametros['rangoHasta']);
            $objFechaSqlHasta = strtotime(date("Y-m-d", strtotime($arrayDateHasta[2] . "-" . $arrayDateHasta[1] . "-" . $arrayDateHasta[0])));
            $strRangoHasta = date("Y/m/d", $objFechaSqlHasta);
            $objQuery->setParameter('rangoHasta', trim($strRangoHasta));             

            $objQuery->setSQL($strSelectCont.$strSql);	   

            $arrayResultado['total'] = $objQuery->getSingleScalarResult();        

            $objQuery->setSQL($strSelectData.$strSql);	   
            
            $objQuery = $this->setQueryLimit($objQuery,$arrayParametros['start'],$arrayParametros['limit']);

            $arrayResultado['resultado'] = $objQuery->getArrayResult();
        
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
        }
        
        return $arrayResultado;
    }       
    
    /**
     * Metodo que obtiene el resultado detallado en un rango de fechas dados por cliente
     * 
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 
     * @since 18-12-2015
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 13-09-2016 Se realizan ajustes en el calculo de la fecha de solucion del caso, actualmente se esta considerando
     *                         la fecha de finalizacion de la primera tarea que da solucion, se cambia para que ahora considere la
     *                         ultima tarea
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 21-06-2017 Se realizan ajustes en el query para que considere el primer afectado.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.3 09-07-2020 - Se realizan ajustes en el query en los valores de (UPTIME y MINUTOS) para
     *                           mostrar 100 y 0 en caso que el tipo de afectación sea (SINAFECTACION).
     * 
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.4 - Se corrije el parseo de fecha de consulta
     * @since 10-07-2020
     * 
     * @author Néstor Naula <nnaulal@telconet.ec>
     * @version 1.5 14-07-2020 - Se cambia el cambio detalle hipotesis por version final del caso
     * @since 1.4
     *
     * @param $arrayParametros [ rangoDesde , rangoHasta , start , limit , puntos , servicios , service, accion ]  
     * @return $arrayResultado [ total , resultado ]
     */
    public function getResultadoResumenDetalladoClientesSla($arrayParametros)
    {
        $arrayResultado = array();
        
        try
        {
            $objRsm   = new ResultSetMappingBuilder($this->_em);	      
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);	           

            $strDateFormat = 'YYYY-MM-DD';                

            $strSelectCont = "SELECT COUNT(*) CONT ";

            $strSelectData = "SELECT TRUNC(RANGO.RANGO) RANGO,
                                    CASE WHEN RESUMEN.TIPO_AFECTACION = 'SINAFECTACION'
                                    THEN '100'
                                    ELSE
                                    SPKG_GENERACION_SLA.FN_REPORTE_DETALLADO_SLA(RESUMEN.TIEMPO,:desde,:hasta,RESUMEN.FE_APERTURA,
                                    RESUMEN.FE_SOLUCION,'uptime',RESUMEN.TIPO_AFECTACION,RANGO.RANGO)
                                    END UPTIME,
                                    CASE WHEN RESUMEN.TIPO_AFECTACION = 'SINAFECTACION'
                                    THEN '0'
                                    ELSE
                                    SPKG_GENERACION_SLA.FN_REPORTE_DETALLADO_SLA(RESUMEN.TIEMPO,:desde,:hasta,RESUMEN.FE_APERTURA,
                                    RESUMEN.FE_SOLUCION,'minutos',RESUMEN.TIPO_AFECTACION,RANGO)
                                    END MINUTOS,
                                    SPKG_GENERACION_SLA.FN_REPORTE_DETALLADO_SLA(RESUMEN.TIEMPO,:desde,:hasta,RESUMEN.FE_APERTURA,
                                    RESUMEN.FE_SOLUCION,'inicioIncidencia',RESUMEN.TIPO_AFECTACION,RANGO) INICIO_INDICENCIA,
                                    SPKG_GENERACION_SLA.FN_REPORTE_DETALLADO_SLA(RESUMEN.TIEMPO,:desde,:hasta,RESUMEN.FE_APERTURA,
                                    RESUMEN.FE_SOLUCION,'finIncidencia',RESUMEN.TIPO_AFECTACION,RANGO) FIN_INCIDENCIA,
                                    CASE
                                      WHEN RANGO.RANGO = TRUNC(RESUMEN.FE_APERTURA)
                                      OR RANGO.RANGO   = TRUNC(RESUMEN.FE_SOLUCION)
                                      THEN NVL(CASE WHEN RESUMEN.TIPO_AFECTACION = 'SINAFECTACION'
                                                THEN 'SIN AFECTACION'
                                                ELSE RESUMEN.TIPO_AFECTACION
                                               END,'CAIDA')
                                      ELSE NULL
                                    END AFECTACION,
                                    CASE
                                      WHEN RANGO.RANGO = TRUNC(RESUMEN.FE_APERTURA)
                                      OR RANGO.RANGO   = TRUNC(RESUMEN.FE_SOLUCION)
                                      THEN RESUMEN.NUMERO_CASO
                                      ELSE NULL
                                    END CASO,
                                    CASE
                                      WHEN RANGO.RANGO = TRUNC(RESUMEN.FE_APERTURA)
                                      OR RANGO.RANGO   = TRUNC(RESUMEN.FE_SOLUCION)
                                      THEN RESUMEN.LOGIN
                                      ELSE NULL
                                    END LOGIN,
                                    CASE
                                      WHEN RANGO.RANGO = TRUNC(RESUMEN.FE_APERTURA)
                                      OR RANGO.RANGO   = TRUNC(RESUMEN.FE_SOLUCION)
                                      THEN RESUMEN.DESCRIPCION_HIPOTESIS
                                      ELSE NULL
                                    END HIPOTESIS,
                                    CASE
                                      WHEN RANGO.RANGO = TRUNC(RESUMEN.FE_APERTURA)
                                      OR RANGO.RANGO   = TRUNC(RESUMEN.FE_SOLUCION)
                                      THEN SPKG_GENERACION_SLA.FN_GET_SERVICIO_CLIENTE_SLA(RESUMEN.ID_PUNTO,RESUMEN.SERVICIO_AFECTADO)
                                      ELSE NULL
                                    END SERVICIOS_AFECTADOS";       

            $strSql = "
                      FROM
                        (SELECT to_date(:desde, :format) + rownum -1 RANGO
                        FROM dual
                          CONNECT BY level <= to_date(:hasta, :format) - to_date(:desde, :format) + 1
                        ) RANGO
                      LEFT JOIN
                        (SELECT CASO.FE_APERTURA,
                          CASO.TIPO_AFECTACION,
                          TIEMPO.TIEMPO_TOTAL_CASO_SOLUCION TIEMPO,
                          CASO.NUMERO_CASO,
                          PUNTO.LOGIN,
                          PUNTO.ID_PUNTO,
                          CASO.VERSION_FIN AS DESCRIPCION_HIPOTESIS,
                          NVL(
                          (SELECT HIST.FE_CREACION
                          FROM INFO_DETALLE DET,
                            INFO_DETALLE_HISTORIAL HIST
                          WHERE DET.DETALLE_HIPOTESIS_ID = DETALLE_HIPOTESIS.ID_DETALLE_HIPOTESIS
                          AND DET.ID_DETALLE             = HIST.DETALLE_ID
                          AND HIST.ESTADO                = 'Finalizada'
                          AND DET.ES_SOLUCION            = 'S'
                          AND DET.ID_DETALLE             =
                            (SELECT MAX(ID_DETALLE)
                            FROM INFO_DETALLE D
                            WHERE D.DETALLE_HIPOTESIS_ID = DET.DETALLE_HIPOTESIS_ID
                            AND D.ES_SOLUCION            = 'S'
                            )
                          ),HISTORIAL.FE_CREACION) FE_SOLUCION,
                          NVL(
                          (SELECT MAX(PARTE.AFECTADO_ID)
                          FROM INFO_DETALLE DET,
                            INFO_PARTE_AFECTADA PARTE
                          WHERE DET.ID_DETALLE    = PARTE.DETALLE_ID
                          AND DET.ID_DETALLE      = DETALLE.ID_DETALLE
                          AND PARTE.TIPO_AFECTADO = 'Servicio'
                          ), 0) SERVICIO_AFECTADO
                        FROM INFO_CASO CASO,
                          INFO_CASO_HISTORIAL HISTORIAL,
                          INFO_PUNTO PUNTO,
                          INFO_DETALLE_HIPOTESIS DETALLE_HIPOTESIS,
                          INFO_DETALLE DETALLE,
                          INFO_PARTE_AFECTADA PARTE_AFECTADA,
                          INFO_CASO_TIEMPO_ASIGNACION TIEMPO,
                          ADMI_HIPOTESIS HIPOTESIS
                        WHERE CASO.ID_CASO                         = DETALLE_HIPOTESIS.CASO_ID
                        AND CASO.ID_CASO                           = TIEMPO.CASO_ID
                        AND DETALLE_HIPOTESIS.ID_DETALLE_HIPOTESIS = DETALLE.DETALLE_HIPOTESIS_ID
                        AND DETALLE.ID_DETALLE                     = PARTE_AFECTADA.DETALLE_ID
                        AND CASO.ID_CASO                           = HISTORIAL.CASO_ID
                        AND PUNTO.ID_PUNTO                         = PARTE_AFECTADA.AFECTADO_ID
                        AND CASO.TITULO_FIN_HIP                    = HIPOTESIS.ID_HIPOTESIS
                        AND TRUNC(CASO.FE_APERTURA)               >= :rangoDesde
                        AND TRUNC(CASO.FE_APERTURA)               <= :rangoHasta
                        AND PARTE_AFECTADA.TIPO_AFECTADO           = :cliente
                        AND PUNTO.ID_PUNTO                         = :punto
                        AND HISTORIAL.ESTADO                       = :estado
                        ) RESUMEN
                      ON RANGO.RANGO >= TRUNC(RESUMEN.FE_APERTURA)
                      AND RANGO.RANGO <= TRUNC(RESUMEN.FE_SOLUCION)";

            $objRsm->addScalarResult('RANGO','rango','string');
            $objRsm->addScalarResult('UPTIME','uptime','float');
            $objRsm->addScalarResult('MINUTOS','minutos','integer');
            $objRsm->addScalarResult('INICIO_INDICENCIA','inicioIncidencia','string');
            $objRsm->addScalarResult('FIN_INCIDENCIA','finIncidencia','string');
            $objRsm->addScalarResult('AFECTACION','afectacion','string');
            $objRsm->addScalarResult('CASO','caso','string');
            $objRsm->addScalarResult('LOGIN','login','string');
            $objRsm->addScalarResult('HIPOTESIS','hipotesis','string');
            $objRsm->addScalarResult('SERVICIOS_AFECTADOS','servicios','string');
            $objRsm->addScalarResult('HIPOTESIS','hipotesis','string');
            $objRsm->addScalarResult('CONT','cont','integer');

            $objQuery->setParameter('cliente', 'Cliente'); 
            $objQuery->setParameter('estado', 'Cerrado');                          
            $objQuery->setParameter('punto', $arrayParametros['punto']);          
            $objQuery->setParameter('desde', $arrayParametros['rangoDesde']);  
            $objQuery->setParameter('hasta', $arrayParametros['rangoHasta']);  
            $objQuery->setParameter('format', $strDateFormat);  

            $arrayDateDesde   = explode("-", $arrayParametros['rangoDesde']);
            $objFechaSqlDesde = strtotime(date("Y-m-d", strtotime($arrayDateDesde[2] . "-" . $arrayDateDesde[1] . "-" . $arrayDateDesde[0])));
            $strRangoDesde = date("Y/m/d", $objFechaSqlDesde);
            $objQuery->setParameter('rangoDesde', trim($strRangoDesde)); 

            $arrayDateHasta = explode("-", $arrayParametros['rangoHasta']);
            $objFechaSqlHasta = strtotime(date("Y-m-d", strtotime($arrayDateHasta[2] . "-" . $arrayDateHasta[1] . "-" . $arrayDateHasta[0])));
            $strRangoHasta = date("Y/m/d", $objFechaSqlHasta);
            $objQuery->setParameter('rangoHasta', trim($strRangoHasta)); 

            $objQuery->setSQL($strSelectCont.$strSql);	   

            $arrayResultado['total'] = $objQuery->getSingleScalarResult();

            $objQuery->setSQL($strSelectData.$strSql);

            $arrayResultado['resultado'] = $objQuery->getArrayResult();           
        
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
        }
        
        return $arrayResultado;
    }               
    
    /**
     * Metodo que obtiene informacion de primer detalle que contenga afectados, el id del ultimo criterio ingresado y la fecha de apertura
     * del caso para poder agregar nuevos afectados
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0
     * @since 25-05-2016
     * 
     * @param integer $intIdCaso
     * @return arrayResultado
     */
    public function obtenerDetalleAfectadoCaso($intIdCaso)
    {
        $query = $this->_em->createQuery();
        $dql = "SELECT 
                    DE.id idDetalle,
                    PA.id criterio,
                    CA.feApertura
                FROM 
                  schemaBundle:InfoCriterioAfectado PA,
                  schemaBundle:InfoDetalle DE,
                  schemaBundle:InfoDetalleHipotesis DH,
                  schemaBundle:InfoCaso CA                  
                WHERE 
                    PA.detalleId          = DE.id
                AND DE.detalleHipotesisId = DH.id
                AND CA.id              = DH.casoId                                                     
                AND DH.id              = (select min(h.id) FROM schemaBundle:InfoDetalleHipotesis h WHERE h.casoId = CA.id)
                AND PA.id              = (select max(a.id) FROM schemaBundle:InfoCriterioAfectado a WHERE a.detalleId = DE.id)
                AND CA.id              = :caso";
       
        $query->setParameter('caso', $intIdCaso);

        $query->setDQL($dql);                

        $data = $query->getOneOrNullResult();

        return $data;    
    }

    /**
     * Metodo que obtiene el array de todos los seguimientos perteneciente a las tareas abiertas dentro de un CASO
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0
     * @since 26-05-2016
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 22-06-2016 Se incluyen cambios para los estados de las tareas cuando ingresan un seguimiento
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 19-08-2016 Se retorna el id del historial del detalle
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.3 14-09-2016 Se retorna el id_persona_empresa_rol del responsable asignado
     *
     * @param integer $id_caso
     * @return $arrayRespuesta
     */
    public function getResultadoSeguimientoTareasXCaso($id_caso)
    {
        $arrayRespuesta['resultado']   = "";
        $arrayRespuesta['total']       = 0;
        try
        {
            $query          = $this->_em->createQuery();
            $queryCount     = $this->_em->createQuery();
            $sqlSelect      = "SELECT dh.id as idHistorial,t.nombreTarea as tarea,ts.observacion, ts.usrCreacion, ".
                              "ts.empresaCod as empresaTareaSeguimiento, ts.feCreacion, ts.estadoTarea as estado , da.asignadoNombre, ".
                              "da.personaEmpresaRolId, d.id as idDetalle  ";
            $sqlSelectCount = "SELECT COUNT(ts) ";

            $sqlFrom = "FROM 
                        schemaBundle:InfoDetalle d,
                        schemaBundle:InfoDetalleHistorial dh,
                        schemaBundle:InfoDetalleHipotesis dhi,  
                        schemaBundle:InfoTareaSeguimiento ts,
                        schemaBundle:AdmiTarea t,
                        schemaBundle:InfoDetalleAsignacion da 
                        WHERE d.tareaId = t.id 
                        AND dhi.casoId = :idCaso 
                        AND d = dh.detalleId 
                        AND dh.id = (SELECT MAX(dhMax.id) 
								FROM schemaBundle:InfoDetalleHistorial dhMax
								WHERE dhMax.detalleId = dh.detalleId)
                        AND d.detalleHipotesisId = dhi.id 
                        AND ts.detalleId = d.id 
                        AND da.detalleId = d.id
                        AND da.id        = (select MAX(da1.id) from schemaBundle:InfoDetalleAsignacion da1 where da1.detalleId = d.id ) 
                        order by t.nombreTarea,ts.feCreacion ASC";

            $sql=$sqlSelect.$sqlFrom;
            $sqlCount=$sqlSelectCount.$sqlFrom;
            

            $query->setParameter("idCaso", $id_caso);
            $queryCount->setParameter("idCaso", $id_caso);

            $query->setDQL($sql);

            $arrayRespuesta['resultado'] = $query->getResult();

            $queryCount->setDQL($sqlCount);
            $arrayRespuesta['total'] = $queryCount->getSingleScalarResult();
        } 
        catch (Exception $e) 
        {
            error_log($e->getMessage());
        }
        return $arrayRespuesta;
    }
    
     /**
     * Metodo que obtiene el json de todos los seguimientos perteneciente a las tareas abiertas dentro de un CASO
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0
     * @since 26-05-2016
     * 
     * @param entityManager $emComercial
     * @param entityManager $emGeneral
     * @param integer $id_caso
     * @param string $codEmpresa
      * 
     * @return $jsonData
     */
    public function getJSONSeguimientoTareasXCaso($id_caso)
    {
        $arrayEncontrados = array();
        $arrayResultado = $this->getResultadoSeguimientoTareasXCaso($id_caso);
        $resultado  = $arrayResultado['resultado'];
        $intTotal   = $arrayResultado['total'];
        $total = 0;

        if($resultado)
        {
            $total = $intTotal;
            foreach($resultado as $data)
            {             
                $arrayEncontrados[]=array(
                                        'tarea'         => $data["tarea"],
                                        'estado'        => $data["estado"],
                                        'observacion'   => $data["observacion"],
                                        'departamento'  => $data["asignadoNombre"],
                                        'empleado'      => $data["usrCreacion"],
                                        'fecha'         => date_format($data["feCreacion"],'Y-m-d H:i')
                                    );	
            }
        }

        $arrayRespuesta = array('total' => $total, 'encontrados' => $arrayEncontrados);
        $jsonData = json_encode($arrayRespuesta);
        return $jsonData;
    }
    
     /**
     * Metodo que genera el JSON de los casos relacionados a un elemento
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0
     * @since 04-08-2016
     * 
     * @param array $arrayParametros
      * 
     * @return $jsonData
     */
    public function getJSONCasosXAfectado($arrayParametros)
    {
        $arrayEncontrados = array();
        $arrayResultado = $this->getResultadoCasosXAfectado($arrayParametros);
        $resultado  = $arrayResultado['resultado'];
        $intTotal   = $arrayResultado['total'];
        $total = 0;

        if($resultado)
        {
            $total = $intTotal;
            foreach($resultado as $data)
            {             
                $arrayEncontrados[]=array(
                                        'id_caso'       => $data["id_caso"],
                                        'numero_caso'   => $data["numero_caso"]
                                    );	
            }
        }

        $arrayRespuesta = array('total' => $total, 'encontrados' => $arrayEncontrados);
        $jsonData = json_encode($arrayRespuesta);
        return $jsonData;
    }
    
    
    /**
    * 
    * getPersonaCasoSolucion
    * obtiene a la persona y al jefe de la misma que solucionaron el caso
    * 
    * @param array $arrayParametros      
    * 
    * @return json $array
    *
    * @author John Vera <javera@telconet.ec>
    * @version 1.0 16-05-2018
    *
    */  
    
    public function getPersonaCasoSolucion($arrayParametros)
    { 

        $objRsm = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsm);
        
       
        $strSql =   "SELECT                         
                            PER.PERSONA_ID,
                            PER.DEPARTAMENTO_ID,
                            R.ES_JEFE,
                            DA.TIPO_ASIGNADO,
                            DB_COMERCIAL.COMEK_CONSULTAS.F_GET_JEFE_BY_ID_PERSONA(DA.REF_ASIGNADO_ID) ID_PERSONA_JEFE,
                            DE.ID_DETALLE,
                            C.NUMERO_CASO,
                            C.VERSION_INI,
                            C.VERSION_FIN,
                            C.FE_APERTURA,
                            C.FE_CIERRE
                       FROM INFO_CASO C,
                            DB_SOPORTE.INFO_DETALLE_HIPOTESIS DH,
                            DB_SOPORTE.INFO_DETALLE DE,
                            DB_SOPORTE.INFO_DETALLE_ASIGNACION DA,
                            DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL PER,
                            DB_COMERCIAL.INFO_EMPRESA_ROL ER,
                            DB_COMERCIAL.ADMI_ROL R
                      WHERE DH.CASO_ID               = C.ID_CASO
                        AND C.ID_CASO                = :idCaso
                        AND DE.ES_SOLUCION           = :esSolucion
                        AND DA.DETALLE_ID            = DE.ID_DETALLE
                        AND DA.ID_DETALLE_ASIGNACION =
                            (SELECT MAX(ID_DETALLE_ASIGNACION)
                            FROM INFO_DETALLE_ASIGNACION
                            WHERE DETALLE_ID = DE.ID_DETALLE
                            )
                        AND DE.DETALLE_HIPOTESIS_ID = DH.ID_DETALLE_HIPOTESIS
                        AND PER.ID_PERSONA_ROL      = DA.PERSONA_EMPRESA_ROL_ID
                        AND ER.ID_EMPRESA_ROL       = PER.EMPRESA_ROL_ID
                        AND R.ID_ROL                = ER.ROL_ID ";        
        
        $objQuery->setParameter("esSolucion", "S");
        $objQuery->setParameter("idCaso", $arrayParametros['intCaso']);        
        $objRsm->addScalarResult('PERSONA_ID', 'idPersona', 'string');
        $objRsm->addScalarResult('DEPARTAMENTO_ID', 'idDepartamento', 'string');
        $objRsm->addScalarResult('ES_JEFE', 'esJefe', 'string');
        $objRsm->addScalarResult('DEPARTAMENTO_EMPLEADO_ID', 'idDepartamentoEmpleado', 'string');
        $objRsm->addScalarResult('ID_PERSONA_JEFE', 'idPersonaJefe', 'string');
        $objRsm->addScalarResult('ID_DETALLE', 'idDetalle', 'string');
        $objRsm->addScalarResult('NUMERO_CASO', 'numeroCaso', 'string');
        $objRsm->addScalarResult('VERSION_INI', 'versionIni', 'string');
        $objRsm->addScalarResult('VERSION_FIN', 'versionFin', 'string');
        $objRsm->addScalarResult('FE_APERTURA', 'feApertura', 'string');
        $objRsm->addScalarResult('FE_CIERRE', 'feCierre', 'string');
        $objRsm->addScalarResult('TIPO_ASIGNADO', 'tipoAsignado', 'string');
        
        $objQuery->setSQL($strSql);

        return $objQuery->getResult();

    }   
    
    /**
     * Metodo que obtiene los casos relacionados a un elemento
     * Costo=47
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0
     * @since 04-08-2016
     * 
     * @param array $arrayParametros[
     *                                  "idElemento"    : id del afectado en un caso
     *                                  "numeroCaso"    : número del caso
     *                              ]
     * @return $arrayRespuesta
     */
    public function getResultadoCasosXAfectado($arrayParametros)
    {
        $arrayRespuesta['resultado']   = "";
        $arrayRespuesta['total']       = 0;
        try
        {
            $query          = $this->_em->createQuery();
            $queryCount     = $this->_em->createQuery();
            
																 								
			$sqlSelect      =   "SELECT DISTINCT c.id as id_caso, c.numeroCaso as numero_caso ";
            $sqlSelectCount =   "SELECT COUNT(c.id) ";

            $sqlFrom        = " FROM 
                                schemaBundle:InfoParteAfectada pa, 
                                schemaBundle:InfoCriterioAfectado ca, 
                                schemaBundle:InfoDetalle de, 
                                schemaBundle:InfoDetalleHipotesis dh, 
                                schemaBundle:InfoCaso c 
                                WHERE pa.criterioAfectadoId = ca.id 
                                AND ca.detalleId = de.id 
                                AND pa.detalleId = ca.detalleId
                                AND pa.detalleId = de.id
                                AND de.detalleHipotesisId = dh.id 
                                AND dh.casoId = c.id 				
                                AND (dh.sintomaId is not null or (dh.hipotesisId is not null and dh.sintomaId is null))  ";	

            
            
            $strWhere="";
            if(isset($arrayParametros['idElemento']) )
            {
                if($arrayParametros['idElemento'])
                {
                    $strWhere .= 'AND pa.afectadoId = :idElemento ';        
                    $query->setParameter('idElemento', $arrayParametros['idElemento']);
                    $queryCount->setParameter('idElemento', $arrayParametros['idElemento']);
                }
            }
            if(isset($arrayParametros['numeroCaso']) )
            {
                if($arrayParametros['numeroCaso'])
                {
                    $strWhere .= 'AND c.numeroCaso LIKE :numeroCaso ';        
                    $query->setParameter('numeroCaso', '%'.$arrayParametros['numeroCaso'].'%');
                    $queryCount->setParameter('numeroCaso', '%'.$arrayParametros['numeroCaso'].'%');
                }
            }
            
            $sql=$sqlSelect.$sqlFrom.$strWhere;
            $sqlCount=$sqlSelectCount.$sqlFrom.$strWhere;

            $query->setDQL($sql);
            
            $arrayRespuesta['resultado'] = $query->getResult();
            
            $queryCount->setDQL($sqlCount);
            $arrayRespuesta['total'] = $queryCount->getSingleScalarResult();
        } 
        catch (\Exception $e) 
        {
            error_log($e->getMessage());
        }
        return $arrayRespuesta;
    }


    /**
     * Función que obtiene el elemento al que pertenece un cliente
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0
     * @since 02-03-2018
     *
     * @param array $arrayParametros[
     *                               "arrayPuntos"       : Punto cliente,
     *                               "intIdElemento"     : Id del elemento,
     *                               "strCriterio"       : El resultado podra ser por punto o elemento,
     *                               "strEstadoServicio" : Estado del servicio,
     *                               "strEstadoElemento" : Estado del elemento
     *                              ]
     * @return $arrayRespuesta
     */
    public function getElementosPuntos($arrayParametros)
    {
        $strBody        = "";
        $strHead        = "";
        $strGroupBy     = "";
        $strAddElement  = "";

        try
        {
            $objRsm   = new ResultSetMappingBuilder($this->_em);
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);

            $strBody = "FROM DB_COMERCIAL.INFO_PUNTO            punto, "
                           ."DB_COMERCIAL.INFO_SERVICIO         servicio, "
                           ."DB_COMERCIAL.INFO_SERVICIO_TECNICO serviTec, "
                           ."DB_INFRAESTRUCTURA.INFO_ELEMENTO   elemento "
                        ."WHERE servicio.PUNTO_ID    = punto.ID_PUNTO "
                          ."AND servicio.ID_SERVICIO = serviTec.SERVICIO_ID "
                          ."AND serviTec.ELEMENTO_ID = elemento.ID_ELEMENTO "
                          ."AND punto.ID_PUNTO       in (:arrayPuntos) "
                          ."AND servicio.ESTADO      = :strEstadoServicio "
                          ."AND elemento.ESTADO      = :strEstadoServicio ";

            $objQuery->setParameter('arrayPuntos', array_values($arrayParametros['arrayPuntos']));
            $objQuery->setParameter('strEstadoServicio', $arrayParametros['strEstadoServicio']);
            $objQuery->setParameter('strEstadoElemento', $arrayParametros['strEstadoElemento']);

            if ($arrayParametros["strCriterio"] == "elemento")
            {
                $strHead = "SELECT elemento.ID_ELEMENTO     ID_ELEMENTO, "
                                . "elemento.NOMBRE_ELEMENTO NOMBRE_ELEMENTO ";

                $strGroupBy = "GROUP BY elemento.ID_ELEMENTO, elemento.NOMBRE_ELEMENTO";

                $objRsm->addScalarResult('ID_ELEMENTO','idElemento','integer');
                $objRsm->addScalarResult('NOMBRE_ELEMENTO','nombreElemento','string');
            }
            elseif ($arrayParametros["strCriterio"] == "punto")
            {
                $strHead = "SELECT punto.ID_PUNTO ID_PUNTO, "
                                . "punto.LOGIN    LOGIN ";

                $objRsm->addScalarResult('ID_PUNTO','idPunto','integer');
                $objRsm->addScalarResult('LOGIN','login','string');

                $strAddElement = "AND  elemento.ID_ELEMENTO = :intIdElemento ";

                $objQuery->setParameter('intIdElemento', $arrayParametros["intIdElemento"]);
            }
            else
            {
                //Por implementar
            }

            $strSql = $strHead.$strBody.$strAddElement.$strGroupBy;

            $objQuery->setSQL($strSql);

            $arrayResultado = $objQuery->getResult();
        }
        catch (\Exception $e)
        {
            error_log("Error: InfoCasoRepository.getElementosPuntos -> ".$e->getMessage());
        }
        return $arrayResultado;
    }

    /**
     * 
     * Método que devuelve los casos según los parámetros asignados
     * 
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0
     * @since 10-04-2018
     * 
     * Costo 4
     * 
     * @param Array $arrayParametros [ 
     *                                intIdElemento,
     *                                strTipoElemento,
     *                                strEstado
     *                               ]
     * @return Array $arrayResultado
     */
    public function getCaso($arrayParametros)
    {
        $objRsm   = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsm);

        $strWhere = "";
        $strSql   = " SELECT
                        T1.ID_CASO ID_CASO,
                        T1.NUMERO_CASO NUMERO_CASO,
                        T1.LOGIN LOGIN,
                        T1.FE_CREACION FE_CREACION,
                        T1.FE_CIERRE FE_CIERRE,
                        T1.NOMBRE_TIPO_CASO,
                        T1.ESTADO ESTADO,
                        T1.VERSION_INICIAL VERSION_INICIAL,
                        T1.VERSION_FINAL VERSION_FINAL,
                        T1.USR_CREACION,
                        T1.OFICINA,
                        T1.DEPARTAMENTO_ASIGNADO,
                        T1.PERSONA_ASIGNADA,
                        (SELECT IPUN.DIRECCION
                        FROM DB_COMERCIAL.INFO_PUNTO IPUN
                        WHERE IPUN.LOGIN = T1.LOGIN
                        ) DIRECCION
                      FROM
                        (SELECT INFOCASO.ID_CASO,
                          INFOCASO.NUMERO_CASO,
                          INFOCASO.FE_CREACION,
                          INFOCASO.FE_CIERRE,
                          infoCaso.EMPRESA_COD,
                          ATC.NOMBRE_TIPO_CASO,
                          INFOCASOHISTORIAL.ESTADO,
                          (SELECT xcxc.afectado_nombre
                          FROM db_soporte.info_parte_afectada xcxc
                          WHERE xcxc.id_parte_afectada = (
                            (SELECT MIN(jbjb.id_parte_afectada)
                            FROM db_soporte.info_parte_afectada jbjb
                            WHERE jbjb.detalle_id =
                              (SELECT MIN(id_detalle)
                              FROM db_soporte.info_detalle lmlm
                              WHERE lmlm.detalle_hipotesis_id =
                                (SELECT MIN(dd.id_detalle_hipotesis)
                                FROM db_soporte.info_detalle_hipotesis dd
                                WHERE dd.caso_id = INFOCASO.id_caso
                                )
                              )
                            AND jbjb.tipo_afectado = 'Cliente'
                            ))
                          ) LOGIN,
                          SUBSTR(INFOCASO.version_ini,1,150) VERSION_INICIAL,
                          (SELECT nombre_hipotesis
                          FROM db_soporte.admi_hipotesis admihi
                          WHERE admihi.id_hipotesis =
                            (SELECT MIN(dd.hipotesis_id)
                            FROM db_soporte.info_detalle_hipotesis dd
                            WHERE dd.caso_id = INFOCASO.id_caso
                            )
                          ) hipotesis,
                          SUBSTR(INFOCASO.VERSION_FIN,1,150) VERSION_FINAL,
                          (SELECT nn.nombres
                            ||' '
                            ||nn.apellidos
                          FROM db_soporte.info_persona nn
                          WHERE nn.id_persona =
                            (SELECT MAX(bb.id_persona)
                            FROM db_soporte.info_persona bb
                            WHERE bb.login = INFOCASO.USR_CREACION
                            )
                          ) USR_CREACION,
                          (SELECT jkjke.nombre_departamento
                          FROM db_soporte.admi_departamento jkjke
                          WHERE jkjke.id_departamento =
                            (SELECT JJJ.DEPARTAMENTO_ID
                            FROM db_soporte.info_persona_empresa_rol jjj
                            WHERE jjj.id_persona_rol =
                              (SELECT MIN(hh.id_persona_rol)
                              FROM db_soporte.info_persona_empresa_rol hh
                              WHERE hh.persona_id =
                                (SELECT MAX(bb.id_persona)
                                FROM db_soporte.info_persona bb
                                WHERE bb.login = INFOCASO.USR_CREACION
                                )
                              AND hh.oficina_id IN
                                (SELECT CCC.ID_OFICINA
                                FROM db_soporte.INFO_OFICINA_GRUPO ccc
                                WHERE CCC.EMPRESA_ID = :strCodEmpresa
                                )
                              )
                            )
                          ) departamento,
                          (SELECT nombre_oficina
                          FROM db_soporte.info_oficina_grupo jkjk
                          WHERE jkjk.id_oficina =
                            (SELECT jjj.oficina_id
                            FROM db_soporte.info_persona_empresa_rol jjj
                            WHERE jjj.id_persona_rol =
                              (SELECT MIN(hh.id_persona_rol)
                              FROM db_soporte.info_persona_empresa_rol hh
                              WHERE hh.persona_id =
                                (SELECT MAX(bb.id_persona)
                                FROM db_soporte.info_persona bb
                                WHERE bb.login = INFOCASO.USR_CREACION
                                )
                              AND hh.oficina_id IN
                                (SELECT CCC.ID_OFICINA
                                FROM db_soporte.INFO_OFICINA_GRUPO ccc
                                WHERE CCC.EMPRESA_ID = :strCodEmpresa
                                )
                              )
                            )
                          ) OFICINA,
                          ICA.ASIGNADO_NOMBRE DEPARTAMENTO_ASIGNADO,
                          ICA.REF_ASIGNADO_NOMBRE PERSONA_ASIGNADA
                        FROM db_soporte.info_caso infoCaso,
                          DB_SOPORTE.INFO_DETALLE_HIPOTESIS IDH,
                          DB_SOPORTE.INFO_CASO_ASIGNACION ICA,
                          INFO_CASO_HISTORIAL infoCasoHistorial,
                          ADMI_TIPO_CASO ATC
                        WHERE infoCaso.id_caso                  = infoCasoHistorial.caso_id
                        AND infoCaso.id_caso                    = IDH.CASO_ID
                        AND IDH.ID_DETALLE_HIPOTESIS            = ICA.DETALLE_HIPOTESIS_ID
                        AND ATC.ID_TIPO_CASO                    = infoCaso.TIPO_CASO_ID
                        AND INFOCASOHISTORIAL.ID_CASO_HISTORIAL =
                          (SELECT MAX(INFOCASOHISTORIAL2.ID_CASO_HISTORIAL)
                          FROM db_soporte.INFO_CASO_HISTORIAL infoCasoHistorial2
                          WHERE infoCasoHistorial2.CASO_ID = INFOCASO.id_caso
                          )
                        ORDER BY INFOCASO.FE_CREACION
                        ) T1
                      WHERE 1            =1
                      AND T1.EMPRESA_COD = :strCodEmpresa ";
        
        $objQuery->setParameter("strCodEmpresa", $arrayParametros["strCodEmpresa"]);
        
        if(isset($arrayParametros["strFeInicial"]) && !empty($arrayParametros["strFeInicial"]) &&
                isset($arrayParametros["strFeFinal"]) && !empty($arrayParametros["strFeFinal"]))
        {
            $strWhere .= " AND T1.FE_CREACION >= TO_DATE (:strFeInicial, 'dd/mm/yyyy') ";
            $strWhere .= " AND T1.FE_CREACION  <= TO_DATE (:strFeFinal, 'dd/mm/yyyy hh24:mi:ss') ";
            $objQuery->setParameter("strFeInicial", $arrayParametros["strFeInicial"]);
            $objQuery->setParameter("strFeFinal", $arrayParametros["strFeFinal"].' 23:59:59');
        }
        
        if(isset($arrayParametros["strLogin"]) && !empty($arrayParametros["strLogin"]))
        {
            $strWhere .= " AND T1.LOGIN = :strLogin ";
            $objQuery->setParameter("strLogin", $arrayParametros["strLogin"]); 
        }
        
        if(isset($arrayParametros["strEstado"]) && !empty($arrayParametros["strEstado"]))
        {
            $strWhere .= " AND T1.ESTADO = :strEstado ";
            $objQuery->setParameter("strEstado", $arrayParametros["strEstado"]); 
        }
        
        $strSql .= $strWhere;
        
        $objRsm->addScalarResult('ID_CASO', 'idCaso', 'integer');
        $objRsm->addScalarResult('NUMERO_CASO', 'numeroCaso', 'string');
        $objRsm->addScalarResult('LOGIN', 'login', 'string');
        $objRsm->addScalarResult('FE_CREACION', 'feCreacion', 'string');
        $objRsm->addScalarResult('FE_CIERRE', 'feCierre', 'string');
        $objRsm->addScalarResult('NOMBRE_TIPO_CASO', 'nombreTipoCaso', 'string');
        $objRsm->addScalarResult('ESTADO', 'estado', 'string');
        $objRsm->addScalarResult('VERSION_INICIAL', 'versionInicial', 'string');
        $objRsm->addScalarResult('VERSION_FINAL', 'versionFinal', 'string');
        $objRsm->addScalarResult('USR_CREACION', 'usrCreacion', 'string');
        $objRsm->addScalarResult('OFICINA', 'oficina', 'string');
        $objRsm->addScalarResult('DEPARTAMENTO_ASIGNADO', 'departamentoAsignado', 'string');
        $objRsm->addScalarResult('PERSONA_ASIGNADA', 'personaAsignada',      'string');
        $objRsm->addScalarResult('DIRECCION', 'direccion', 'string');

        $objQuery->setSQL($strSql);

        $arrayResultado = $objQuery->getArrayResult();
                
        return $arrayResultado;
    }

    /**
     * 
     * Método que devuelve los casos asignados a un cliente en específico, quien es la cuadrilla que los 
     * atendie, el departamento responsable
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.0
     * @since 07-05-2018
     *
     * 
     * @author Modificado: Ronny Moran <rmoranc@telconet.ec>
     * @version 1.1 10-05-2018 - Se agrega en la respuesta el tipo de origen del caso.
     * 
     * Costo 131
     * 
     * @author Modificado: Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.2 24-06-2018 - Compatibilidad para filtrar casos por estados,
     *                           cobertura, login, rango de fechas o direccion.
     *
     * @author Modificado: Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.3 18-07-2018 - Se baja el costo del query
     * 
     * Costo 71
     *
     * @author Modificado: Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.4 18-07-2018 - Se agrega como salida el campo CANTON_ID,
     *                           para saber a que cantón pertenece el caso.
     *
     * @author Modificado: Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.4 03-12-2018 - Se coloca el usuario de base de datos en la tabla info_punto.
     *
     * Costo 125
     * @author Modificado: Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.5 07-07-2020 - Se solicita no visualizar los casos de ECUCERT
     *
     * @param Array $arrayParametros [ 
     *                                strLogin,
                                      strEstados,
                                      strCodEmpresa,
                                      strFeInicial,
                                      strFeFinal,
                                      strDireccion,
                                      intIdCiudad,
                                      intPersonaEmpresaRolId
     *                               ]
     * @return Array $arrayResultado
     */
    public function getCasoTipoOrigen($arrayParametros)
    {
        $objRsm   = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsm);
        $strWhere = "";
        $strSql   = "SELECT IDCASO,
                        NUMEROCASO,
                        LOGIN,
                        PUNTO_COBERTURA_ID,
                        FE_CREACION,
                        FE_CIERRE,
                        NOMBRE_TIPO_CASO,
                        ESTADO_TAREA,
                        VERSION_INICIAL,
                        VERSION_FINAL,
                        PERSONACREACION,
                        NOMBRE_OFICINA,
                        ASIGNADO_NOMBRE,
                        REF_ASIGNADO_NOMBRE,
                        DIRECCION,
                        ORIGEN
                      FROM
                        (SELECT CASO.ID_CASO IDCASO,
                          CASO.NUMERO_CASO NUMEROCASO,
                          INF_PU.LOGIN,
                          INF_PU.PUNTO_COBERTURA_ID,
                          CASO.FE_CREACION FE_CREACION,
                          CASO.FE_CIERRE FE_CIERRE,
                          TIPO_CASO.NOMBRE_TIPO_CASO NOMBRE_TIPO_CASO,
                          T2.ESTADO AS ESTADO_TAREA,
                          CASO.VERSION_INI VERSION_INICIAL,
                          CASO.VERSION_FIN VERSION_FINAL,
                          CONCAT(INFO_PERSONA.NOMBRES,CONCAT(' ',INFO_PERSONA.APELLIDOS)) PERSONACREACION,
                          OFICINA.NOMBRE_OFICINA NOMBRE_OFICINA,
                          ASIGNACION.ASIGNADO_NOMBRE ASIGNADO_NOMBRE,
                          ASIGNACION.REF_ASIGNADO_NOMBRE REF_ASIGNADO_NOMBRE,
                          INF_PU.DIRECCION,
                          CASO.ORIGEN ORIGEN
                        FROM DB_SOPORTE.INFO_CASO CASO
                        INNER JOIN DB_SOPORTE.INFO_DETALLE_HIPOTESIS DETALLE_HIPOTESIS
                        ON CASO.ID_CASO = DETALLE_HIPOTESIS.CASO_ID
                        INNER JOIN DB_SOPORTE.INFO_DETALLE DETALLE
                        ON DETALLE_HIPOTESIS.ID_DETALLE_HIPOTESIS = DETALLE.DETALLE_HIPOTESIS_ID
                        INNER JOIN DB_SOPORTE.ADMI_TIPO_CASO TIPO_CASO
                        ON CASO.TIPO_CASO_ID = TIPO_CASO.ID_TIPO_CASO
                        LEFT JOIN DB_SOPORTE.INFO_CASO_ASIGNACION ASIGNACION
                        ON ASIGNACION.DETALLE_HIPOTESIS_ID = DETALLE_HIPOTESIS.ID_DETALLE_HIPOTESIS
                        LEFT JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL EMPRESA_ROL
                        ON EMPRESA_ROL.ID_PERSONA_ROL = ASIGNACION.PERSONA_EMPRESA_ROL_ID
                        LEFT JOIN DB_COMERCIAL.INFO_OFICINA_GRUPO OFICINA
                        ON OFICINA.ID_OFICINA=EMPRESA_ROL.OFICINA_ID
                        LEFT JOIN DB_COMERCIAL.INFO_PERSONA
                        ON INFO_PERSONA.LOGIN = ASIGNACION.USR_CREACION
                        JOIN DB_SOPORTE.ADMI_SINTOMA    admisintoma
                        ON admisintoma.id_sintoma = detalle_hipotesis.sintoma_id
                        INNER JOIN
                          (SELECT ID_CASO,
                            DETALLE_HISTORIAL.ESTADO,
                            INPU.LOGIN,
                            INPU.ID_PUNTO,
                            INPU.DIRECCION
                          FROM
                            (SELECT MAX(DETALLE_HISTORIAL.ID_CASO_HISTORIAL) AS ID_HISTORIAL_DETALLE,
                              DETALLE_HISTORIAL.CASO_ID                      AS ID_CASO,
                              AFECTADA.AFECTADO_ID                           AS AFECTADO_ID
                            FROM DB_SOPORTE.INFO_CASO_HISTORIAL DETALLE_HISTORIAL,
                              DB_SOPORTE.INFO_CASO CASO,
                              DB_SOPORTE.INFO_DETALLE_HIPOTESIS HIPOTESIS,
                              DB_SOPORTE.INFO_DETALLE DETALLE,
                              DB_SOPORTE.INFO_PARTE_AFECTADA AFECTADA
                            WHERE CASO.ID_CASO                 = DETALLE_HISTORIAL.CASO_ID
                            AND HIPOTESIS.CASO_ID              = CASO.ID_CASO
                            AND HIPOTESIS.ID_DETALLE_HIPOTESIS = DETALLE.DETALLE_HIPOTESIS_ID
                            AND AFECTADA.DETALLE_ID            = DETALLE.ID_DETALLE";

        if(isset($arrayParametros["strFeInicial"]) && !empty($arrayParametros["strFeInicial"]) &&
                isset($arrayParametros["strFeFinal"]) && !empty($arrayParametros["strFeFinal"]))
        {
            $strWhere .= " AND CASO.FE_CREACION >= TO_DATE(:strFeInicial, 'dd/mm/yyyy') ";
            $strWhere .= " AND CASO.FE_CREACION  <= TO_DATE(:strFeFinal, 'dd/mm/yyyy hh24:mi:ss') ";
            $objQuery->setParameter("strFeInicial", $arrayParametros["strFeInicial"]);
            $objQuery->setParameter("strFeFinal", $arrayParametros["strFeFinal"].' 23:59:59');
        }
        else if(isset($arrayParametros["strFeInicial"]) && !empty($arrayParametros["strFeInicial"]) &&
                (!isset($arrayParametros["strFeFinal"]) || empty($arrayParametros["strFeFinal"])))
        {
            $strWhere .= " AND CASO.FE_CREACION >= TO_DATE (:strFeInicial, 'dd/mm/yyyy') ";
            $strWhere .= " AND CASO.FE_CREACION  <= SYSDATE ";
            $objQuery->setParameter("strFeInicial", $arrayParametros["strFeInicial"]);
        }
        else if((!isset($arrayParametros["strFeInicial"]) || empty($arrayParametros["strFeInicial"])) &&
                isset($arrayParametros["strFeFinal"]) && !empty($arrayParametros["strFeFinal"]))
        {
            $strWhere .= " AND CASO.FE_CREACION >= (SYSDATE - 365) ";
            $strWhere .= " AND CASO.FE_CREACION  <= TO_DATE (:strFeFinal, 'dd/mm/yyyy hh24:mi:ss') ";
            $objQuery->setParameter("strFeInicial", $arrayParametros["strFeInicial"]);
            $objQuery->setParameter("strFeFinal", $arrayParametros["strFeFinal"].' 23:59:59');
        }
        else if(isset($arrayParametros["strLogin"]) && !empty($arrayParametros["strLogin"]) )
        {
            $strWhere .= " AND CASO.FE_CREACION >= (SYSDATE - 365) ";
            $strWhere .= " AND CASO.FE_CREACION  <= SYSDATE ";
        }

        $strWhere .= " AND AFECTADA.AFECTADO_NOMBRE      IN
                        (SELECT IPUN.LOGIN
                        FROM DB_COMERCIAL.INFO_OFICINA_GRUPO IOGR,
                          DB_COMERCIAL.ADMI_JURISDICCION AJUR,
                          DB_COMERCIAL.INFO_PUNTO IPUN
                        WHERE IOGR.ID_OFICINA           = AJUR.OFICINA_ID
                        AND IPUN.PUNTO_COBERTURA_ID     = AJUR.ID_JURISDICCION ";

        if(isset($arrayParametros["strCodEmpresa"]) && !empty($arrayParametros["strCodEmpresa"]))
        {
             $strWhere .= " AND IOGR.EMPRESA_ID = TO_NUMBER(:strCodEmpresa) ";
             $objQuery->setParameter("strCodEmpresa", $arrayParametros["strCodEmpresa"]);
        }

        if(isset($arrayParametros["intIdCiudad"]) && !empty($arrayParametros["intIdCiudad"]))
        {
            $strWhere .= " AND IOGR.CANTON_ID = :intIdCiudad ";
            $objQuery->setParameter("intIdCiudad", $arrayParametros["intIdCiudad"]);
        }

        if(isset($arrayParametros["intPersonaEmpresaRolId"]) && !empty($arrayParametros["intPersonaEmpresaRolId"]))
        {
                $strWhere .= " AND IPUN.PERSONA_EMPRESA_ROL_ID = :intPersonaEmpresaRolId ";
                $objQuery->setParameter("intPersonaEmpresaRolId", $arrayParametros["intPersonaEmpresaRolId"]);
        }

        if(isset($arrayParametros["strDireccion"]) && !empty($arrayParametros["strDireccion"]))
        {
            $strWhere .= " AND IPUN.DIRECCION = :strDireccion ";
            $objQuery->setParameter("strDireccion", $arrayParametros["strDireccion"]);
        }

        $strWhere .= ") ";

        if(isset($arrayParametros["strLogin"]) && !empty($arrayParametros["strLogin"]))
        {
            $strWhere .= " AND AFECTADA.AFECTADO_NOMBRE = :strLogin ";
            $objQuery->setParameter("strLogin", $arrayParametros["strLogin"]);
        }

        $strWhere .= " AND AFECTADA.TIPO_AFECTADO = 'Cliente'
                        GROUP BY DETALLE_HISTORIAL.CASO_ID,
                          AFECTADA.AFECTADO_ID
                        ORDER BY DETALLE_HISTORIAL.CASO_ID DESC
                        ) T1
                      INNER JOIN DB_SOPORTE.INFO_CASO_HISTORIAL DETALLE_HISTORIAL
                      ON T1.ID_HISTORIAL_DETALLE = DETALLE_HISTORIAL.ID_CASO_HISTORIAL
                      INNER JOIN DB_COMERCIAL.INFO_PUNTO INPU
                      ON INPU.ID_PUNTO                = T1.AFECTADO_ID ";

        if(!isset($arrayParametros["strLogin"]) && empty($arrayParametros["strLogin"]))
        {
            $strWhere .= " WHERE ROWNUM < 11 ";
            $boolLogin = true;
        }

        if(isset($arrayParametros["strEstados"]) && count($arrayParametros["strEstados"]) > 0)
        {   
            if($boolLogin)
            {
                $strWhere .= " AND DETALLE_HISTORIAL.ESTADO IN (:arrayCasoEstados) ";
            }
            else
            {
                $strWhere .= " WHERE DETALLE_HISTORIAL.ESTADO IN (:arrayCasoEstados) ";
            }
            $objQuery->setParameter("arrayCasoEstados", $arrayParametros["strEstados"]);
        }
        else if(isset($arrayParametros["strLogin"]) && !empty($arrayParametros["strLogin"]))
        {
            $strWhere .= " WHERE DETALLE_HISTORIAL.ESTADO  IN ('Asignado','Creado') ";
        }

        $strWhere .= " ORDER BY ID_CASO DESC
                        ) T2 ON T2.ID_CASO = CASO.ID_CASO
                      JOIN DB_COMERCIAL.INFO_PUNTO INF_PU
                      ON INF_PU.ID_PUNTO = T2.ID_PUNTO ";
        if(isset($arrayParametros["strLogin"]) && !empty($arrayParametros["strLogin"]))
        {
            $strWhere .=" WHERE INF_PU.LOGIN = :strLogin ";
            $objQuery->setParameter("strLogin", $arrayParametros["strLogin"]);
        }

        if(!isset($arrayParametros["strLogin"]) && empty($arrayParametros["strLogin"]))
        {
            $strWhere .=" WHERE INF_PU.PERSONA_EMPRESA_ROL_ID = :intPersonaEmpresaRolId 
                          AND NOT EXISTS (SELECT 1 FROM 
                                           DB_GENERAL.ADMI_PARAMETRO_CAB parametroCab, DB_GENERAL.ADMI_PARAMETRO_DET parametroDet
                                         WHERE parametrocab.id_parametro = parametroDet.parametro_id
                                         AND parametrocab.nombre_parametro = :strNombreParametro
                                         AND parametrodet.valor1 = admisintoma.id_sintoma)";
            $objQuery->setParameter("intPersonaEmpresaRolId", $arrayParametros["intPersonaEmpresaRolId"]);
            $objQuery->setParameter("strNombreParametro", 'SINTOMAS_CASO_TELCO_MANAGER');
        }
        
        $strWhere .= " GROUP BY CASO.ID_CASO,
                        CASO.NUMERO_CASO,
                        INF_PU.LOGIN,
                        INF_PU.PUNTO_COBERTURA_ID,
                        CASO.FE_CREACION,
                        CASO.FE_CIERRE,
                        TIPO_CASO.NOMBRE_TIPO_CASO,
                        T2.ESTADO,
                        CASO.VERSION_INI,
                        CASO.VERSION_FIN,
                        CONCAT(INFO_PERSONA.NOMBRES,CONCAT(' ',INFO_PERSONA.APELLIDOS)),
                        OFICINA.NOMBRE_OFICINA,
                        ASIGNACION.ASIGNADO_NOMBRE,
                        ASIGNACION.REF_ASIGNADO_NOMBRE,
                        INF_PU.DIRECCION,
                        CASO.ORIGEN) ";

        $strSql .= $strWhere;

        $objRsm->addScalarResult('IDCASO', 'idCaso', 'integer');
        $objRsm->addScalarResult('NUMEROCASO', 'numeroCaso', 'string');
        $objRsm->addScalarResult('LOGIN', 'login', 'string');
        $objRsm->addScalarResult('FE_CREACION', 'feCreacion', 'string');
        $objRsm->addScalarResult('FE_CIERRE', 'feCierre', 'string');
        $objRsm->addScalarResult('NOMBRE_TIPO_CASO', 'nombreTipoCaso', 'string');
        $objRsm->addScalarResult('ESTADO_TAREA', 'estado', 'string');
        $objRsm->addScalarResult('VERSION_INICIAL', 'versionInicial', 'string');
        $objRsm->addScalarResult('VERSION_FINAL', 'versionFinal', 'string');
        $objRsm->addScalarResult('PERSONACREACION', 'usrCreacion', 'string');
        $objRsm->addScalarResult('NOMBRE_OFICINA', 'oficina', 'string');
        $objRsm->addScalarResult('ASIGNADO_NOMBRE', 'departamentoAsignado', 'string');
        $objRsm->addScalarResult('REF_ASIGNADO_NOMBRE', 'personaAsignada',      'string');
        $objRsm->addScalarResult('DIRECCION', 'direccion', 'string');
        $objRsm->addScalarResult('ORIGEN', 'casoOrigen', 'string');
        $objRsm->addScalarResult('PUNTO_COBERTURA_ID', 'puntoCoberturaId', 'integer');

        $objQuery->setSQL($strSql);
        $arrayResultado = $objQuery->getArrayResult();
        return $arrayResultado;
    }

    /**
     * Función que obtiene el id de detalle de hipotesis de acuerdo a los afectados y al sintoma
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0
     * @since 02-03-2018
     *
     * @param array $arrayParametros[
     *                               "arrayAfectados"  : Array de afectados
     *                               "sintoma"         : Nombre del sistoma
     * *                             "caso"            : Id del caso
     *                              ]
     * @return $arrayRespuesta
     */
    public function getDetalleHipotesisAfectadosCaso($arrayParametros)
    {
        $strSql         = "";

        try
        {
            $objRsm   = new ResultSetMappingBuilder($this->_em);
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);

	    $strSql =   "SELECT hipotesis.ID_DETALLE_HIPOTESIS ID_DETALLE_HIPOTESIS"
                        . " FROM DB_SOPORTE.INFO_PARTE_AFECTADA    afectados, "
                              . "DB_SOPORTE.INFO_DETALLE           detalle, "
                              . "DB_SOPORTE.INFO_DETALLE_HIPOTESIS hipotesis, "
                              . "DB_SOPORTE.ADMI_SINTOMA           sintoma,"
                              . "DB_SOPORTE.INFO_CASO              caso "
                        . "WHERE afectados.DETALLE_ID         = detalle.ID_DETALLE "
                         . " AND detalle.DETALLE_HIPOTESIS_ID = hipotesis.ID_DETALLE_HIPOTESIS "
                         . " AND hipotesis.SINTOMA_ID         = sintoma.ID_SINTOMA "
                         . " AND hipotesis.CASO_ID            = caso.ID_CASO "
                         . " AND afectados.AFECTADO_ID        in (:idAfectados) "
                         . " AND sintoma.NOMBRE_SINTOMA       = :nombreSintoma "
                         . " AND caso.ID_CASO                 = :idCaso "
                        . " group by hipotesis.ID_DETALLE_HIPOTESIS ";

            $objRsm->addScalarResult('ID_DETALLE_HIPOTESIS','idDetalleHipotesis','integer');

            $objQuery->setParameter('idAfectados', array_values($arrayParametros['arrayAfectados']));
            $objQuery->setParameter('nombreSintoma', $arrayParametros['sintoma']);
            $objQuery->setParameter('idCaso', $arrayParametros['caso']);

            $objQuery->setSQL($strSql);

            $arrayResultado = $objQuery->getResult();
        }
        catch (\Exception $ex)
        {
            error_log("Error: InfoCasoRepository.getDetalleHipotesisAfectadosCaso -> ".$ex->getMessage());
        }
        return $arrayResultado;
    }

    /*
     * Método que devuelve los casos por estado del Dashboard segun los parámetros asignados
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 2.0
     * @since 22-06-2018
     *
     * Costo 58
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 2.1 - 08/07/2020 - No se debe contabilizar los casos de Ecucert.
     *
     * @param Array $arrayParametros [
     *                                intIdElemento,
     *                                strTipoElemento,
     *                                strEstado,
     *                                strDias,
     *                                strMes,
     *                                strAnio
     *                               ]
     * @return Array $arrayResultado
     */
    public function getCasosClientePorEstado($arrayParametros)
    {
        $objRsm        = new ResultSetMappingBuilder($this->_em);
        $objQuery      = $this->_em->createNativeQuery(null, $objRsm);
        $strSql        = "SELECT ICHIS_PRIN.ESTADO ESTADO, COUNT(ICHIS_PRIN.ESTADO) COUNTESTADO FROM DB_SOPORTE.INFO_CASO_HISTORIAL ICHIS_PRIN
                        WHERE ICHIS_PRIN.ID_CASO_HISTORIAL IN(
                        SELECT MAX(ICHIS.ID_CASO_HISTORIAL) FROM DB_SOPORTE.INFO_CASO_HISTORIAL ICHIS
                        WHERE ICHIS.CASO_ID IN (SELECT ICAS.ID_CASO
                        FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IEMPR,
                          DB_COMERCIAL.INFO_PUNTO IPUN,
                          DB_SOPORTE.INFO_PARTE_AFECTADA IPAF,
                          DB_SOPORTE.INFO_DETALLE IDETA,
                          DB_SOPORTE.INFO_DETALLE_HIPOTESIS IDHI,
                          DB_SOPORTE.INFO_CASO ICAS
                        WHERE IPUN.PERSONA_EMPRESA_ROL_ID = IEMPR.ID_PERSONA_ROL
                        AND IPAF.AFECTADO_ID              = IPUN.ID_PUNTO
                        AND IPAF.DETALLE_ID               = IDETA.ID_DETALLE
                        AND IDHI.ID_DETALLE_HIPOTESIS     = IDETA.DETALLE_HIPOTESIS_ID
                        AND ICAS.ID_CASO                  = IDHI.CASO_ID
                        AND IPAF.TIPO_AFECTADO            = :strTipoAfectado
                        AND IEMPR.PERSONA_ID              = :intPersonaId
                        AND IEMPR.ESTADO                  = :strEstadoPers
                        AND IPUN.ESTADO                   = :strEstadoPunto
                        AND TRUNC(ICAS.FE_APERTURA) BETWEEN TO_DATE(:strFeInicio,'dd/mm/yyyy') and TO_DATE(:strFeFin,'dd/mm/yyyy'))
                        AND NOT EXISTS (SELECT 1 FROM 
                                           DB_SOPORTE.INFO_CASO inCasos,
                                           DB_SOPORTE.INFO_DETALLE_HIPOTESIS inDetHip,
                                           DB_SOPORTE.ADMI_SINTOMA admSintoma,
                                           DB_GENERAL.ADMI_PARAMETRO_CAB parametroCab,
                                           DB_GENERAL.ADMI_PARAMETRO_DET parametroDet
                                         WHERE indethip.caso_id = incasos.id_caso
                                         AND indethip.sintoma_id = admSintoma.id_sintoma
                                         AND parametrocab.id_parametro = parametroDet.parametro_id
                                         AND incasos.id_caso = ICHIS.CASO_ID
                                         AND parametrocab.nombre_parametro = :strNombreParametro
                                         AND parametrodet.valor1 = admSintoma.id_sintoma)
                        GROUP BY ICHIS.CASO_ID)
                        GROUP BY ICHIS_PRIN.ESTADO";

        $objQuery->setParameter("strFeInicio", $arrayParametros['strFeInicio']);
        $objQuery->setParameter("strFeFin", $arrayParametros['strFeFin']);
        $objQuery->setParameter("strEstadoPunto", 'Activo');
        $objQuery->setParameter("strEstadoPers", 'Activo');
        $objQuery->setParameter("intPersonaId", $arrayParametros['intIdPersona']);
        $objQuery->setParameter("strTipoAfectado", 'Cliente');
        $objQuery->setParameter("strNombreParametro", 'SINTOMAS_CASO_TELCO_MANAGER');
        $objRsm->addScalarResult('COUNTESTADO', 'conteoCasos', 'string');
        $objRsm->addScalarResult('ESTADO', 'estado', 'string');
        $objQuery->setSQL($strSql);
        $arrayResultadoCasos = $objQuery->getArrayResult();

        $strEstadoCaso ="";
        foreach ($arrayResultadoCasos as $objResultadoCasos)
        {
            $strEstadoCaso = $strEstadoCaso." ". $objResultadoCasos["estado"];
            $arrayRespuesta[] = $objResultadoCasos;
        }
        
        $booleanExisteCreado   = strpos($strEstadoCaso, 'Creado');
        $booleanExisteAsignado = strpos($strEstadoCaso, 'Asignado');
        $booleanExisteCerrado  = strpos($strEstadoCaso, 'Cerrado');
        

        if ($booleanExisteCreado === false)
        {
            $arrayRespuesta[] = array(
                                        "estado"  => "Creado",
                                        "conteoCasos"  => "0");
        }
        if ($booleanExisteAsignado === false)
        {
            $arrayRespuesta[] = array(
                                        "estado"  => "Asignado",
                                        "conteoCasos"  => "0");
        }
        if ($booleanExisteCerrado === false)
        {
            $arrayRespuesta[] = array(
                                        "estado"  => "Cerrado",
                                        "conteoCasos"  => "0");
        }

        return $arrayRespuesta;
    }
    
    /**
     * Método que obtiene los registros del porcentaje de disponibilidad general de lo clientes para Sla
     *
     * @author Walther Joao Gaibor C. <wgaibor@telconet.ec>
     * @version 1.0
     * @since 29-06-2018
     *
     * Costo:188
     * @param Array $arrayParametros [ rangoDesde , rangoHasta , start , limit , puntos , servicios , service, accion ]
     * @return Array $arrayResultado [ total , resultado ]
     */
    public function getDisponibilidadClientesSlaMovil($arrayParametros)
    {
        $arrayResultado = array();
        try
        {
            $objRsm   = new ResultSetMappingBuilder($this->_em);
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);
            $strSql   = "SELECT NVL(PERSONA.RAZON_SOCIAL,PERSONA.NOMBRES
                                || ' '
                                || PERSONA.APELLIDOS) NOMBRE_PUNTO,
                                PUNTO.LOGIN,
                                SPKG_GENERACION_SLA.FN_CALCULAR_DISPONIBILIDAD_SLA(SUM(NVL(TIEMPO.TIEMPO_TOTAL_CASO_SOLUCION,0)),
                                    PUNTO.ID_PUNTO,:tipoReporte,:tipoAfectacion,:desde,:hasta) DISPONIBILIDAD,
                                SUM(NVL(TIEMPO.TIEMPO_TOTAL_CASO_SOLUCION,0)) TIEMPO_TOTAL,
                                SPKG_GENERACION_SLA.FN_GET_CASOS_CLIENTE_SLA(PUNTO.ID_PUNTO,:desde,:hasta) CASOS ";
            $strSql  .= "
                        FROM INFO_PUNTO PUNTO
                          LEFT JOIN INFO_PERSONA_EMPRESA_ROL EMPRESA_ROL
                          ON EMPRESA_ROL.ID_PERSONA_ROL = PUNTO.PERSONA_EMPRESA_ROL_ID
                          LEFT JOIN INFO_PERSONA PERSONA
                          ON PERSONA.ID_PERSONA = EMPRESA_ROL.PERSONA_ID
                          LEFT JOIN INFO_PARTE_AFECTADA PARTE_AFECTADA
                          ON PUNTO.ID_PUNTO                = PARTE_AFECTADA.AFECTADO_ID
                          AND PARTE_AFECTADA.TIPO_AFECTADO = :cliente
                          LEFT JOIN INFO_DETALLE DETALLE
                          ON PARTE_AFECTADA.DETALLE_ID = DETALLE.ID_DETALLE
                          LEFT JOIN INFO_DETALLE_HIPOTESIS DETALLE_HIPOTESIS
                          ON DETALLE_HIPOTESIS.ID_DETALLE_HIPOTESIS = DETALLE.DETALLE_HIPOTESIS_ID
                          LEFT JOIN INFO_CASO CASO
                          ON CASO.ID_CASO              = DETALLE_HIPOTESIS.CASO_ID
                          AND TRUNC(CASO.FE_APERTURA) >= :rangoDesde
                          AND TRUNC(CASO.FE_APERTURA) <= :rangoHasta
                          LEFT JOIN INFO_CASO_HISTORIAL HISTORIAL
                          ON HISTORIAL.CASO_ID = CASO.ID_CASO
                          AND HISTORIAL.ESTADO = :estado
                          LEFT JOIN INFO_CASO_TIEMPO_ASIGNACION TIEMPO
                          ON TIEMPO.CASO_ID     = CASO.ID_CASO
                          WHERE PUNTO.ID_PUNTO IN (:puntos)
                          GROUP BY PUNTO.ID_PUNTO,
                          PUNTO.LOGIN,
                          PERSONA.RAZON_SOCIAL,
                          PERSONA.NOMBRES,
                          PERSONA.APELLIDOS";

            $objRsm->addScalarResult('NOMBRE_PUNTO','puntoDisponibilidad','string');
            $objRsm->addScalarResult('LOGIN','loginDisponibilidad','string');
            $objRsm->addScalarResult('DISPONIBILIDAD','porcentajeDisponibilidad','float');
            $objRsm->addScalarResult('TIEMPO_TOTAL','minutosTotalDisponibilidad','integer');
            $objRsm->addScalarResult('CASOS','casos','string');
            $objRsm->addScalarResult('CONT','cont','integer');

            $objQuery->setParameter('cliente', 'Cliente');
            $objQuery->setParameter('estado', 'Cerrado');
            $objQuery->setParameter('tipoReporte', 'consolidado');
            $objQuery->setParameter('tipoAfectacion', 'CAIDA');
            $objQuery->setParameter('puntos', array_values($arrayParametros['puntos']));
            $objQuery->setParameter('desde', $arrayParametros['rangoDesde']);
            $objQuery->setParameter('hasta', $arrayParametros['rangoHasta']);

            $strDesde          = explode("-", $arrayParametros['rangoDesde']);
            $strFechaSqlDesde  = strtotime(date("Y-m-d", strtotime($strDesde[2] . "-" . $strDesde[1] . "-" . $strDesde[0])) . " +1 day");
            $strRangoDesde     = date("Y/m/d", $strFechaSqlDesde);
            $objQuery->setParameter('rangoDesde', trim($strRangoDesde));

            $strHasta          = explode("-", $arrayParametros['rangoHasta']);
            $strFechaSqlHasta  = strtotime(date("Y-m-d", strtotime($strHasta[2] . "-" . $strHasta[1] . "-" . $strHasta[0])) . " +1 day");
            $strRangoHasta     = date("Y/m/d", $strFechaSqlHasta);
            $objQuery->setParameter('rangoHasta', trim($strRangoHasta));

            $objQuery->setSQL($strSql);
            $arrayResultado = $objQuery->getArrayResult();
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
        }
        return $arrayResultado;
    }

    /**
     * Método que obtiene el cliente que es afectado por un caso
     *
     * @author Walther Joao Gaibor C. <wgaibor@telconet.ec>
     * @version 1.0
     * @since 29-07-2018
     *
     * Costo:4
     * @param Array $arrayParametros [
     *                                  detalleId:      Integer:        Id detalle,
     *                                  tipoAfectado:   String:         tipo afectado cliente ]
     * @return Array $arrayResultado [ total , resultado ]
     */
    public function getClienteAfectado($arrayParametros)
    {
        $arrayResultado = array();
        try
        {
            $objRsm     = new ResultSetMappingBuilder($this->_em);
            $objQuery   = $this->_em->createNativeQuery(null, $objRsm);
            $strSql     = "SELECT IPF.AFECTADO_ID, IPF.AFECTADO_NOMBRE
                            FROM INFO_PARTE_AFECTADA IPF
                            WHERE IPF.DETALLE_ID = :detalleId
                            and IPF.TIPO_AFECTADO = :tipoAfectado
                            and ROWNUM <= 1";
            $objRsm->addScalarResult('AFECTADO_ID','afectadoId','integer');
            $objRsm->addScalarResult('AFECTADO_NOMBRE','afectadoNombre','string');

            $objQuery->setParameter('detalleId', $arrayParametros['detalleId']);
            $objQuery->setParameter('tipoAfectado', $arrayParametros['tipoAfectado']);

            $objQuery->setSQL($strSql);
            $arrayResultado = $objQuery->getArrayResult();
        } catch (\Exception $e)
        {
            error_log($e->getMessage());
        }
        return $arrayResultado;
    }

    /**
     * Método que obtiene el cliente que es afectado por un caso
     *
     * @author Walther Joao Gaibor C. <wgaibor@telconet.ec>
     * @version 1.0
     * @since 29-07-2018
     *
     * Costo:2
     * @param Array $arrayParametros [
     *                                  detalleId:      Integer:        Id detalle,
     *                                  tipoAfectado:   String:         tipo afectado cliente ]
     * @return Array $arrayResultado [ total , resultado ]
     */
    public function getCantidadCasoMovil()
    {
        $objRsm   = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsm);
        $strSql = " SELECT COUNT(*) CANTIDAD
                    FROM DB_SOPORTE.INFO_CASOS_MOVIL";

        $objRsm->addScalarResult('CANTIDAD', 'cantCasosMovil', 'integer');
        $objQuery->setSQL($strSql);
        $arrayResult = $objQuery->getResult();
        return $arrayResult[0]['cantCasosMovil'];
    }

    /**
     * Método que obtiene la cantidad de casos realizado por una razon social
     *
     * @author Walther Joao Gaibor C. <wgaibor@telconet.ec>
     * @version 1.0
     * @since 20-01-2019
     *
     * Costo:2
     * @param Array $arrayParametros [
     *                                  strOrigen:         String:        Origen de creación del caso,
     *                                  strTipoAfectado:   String:        Tipo afectado cliente,
     *                                  strFeInicial:      String:        Fecha inicial de reporte de caso,
     *                                  strFeFinal:        String:        Fecha final de reporte de caso ]
     * @return Array $arrayResultado [ total , resultado ]
     */
    public function getCantCasoMovilPorRazonSocial($arrayParametros)
    {
        $objRsm   = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsm);
        $strSql = " SELECT TMP1.ID_PERSONA_ROL ID_PERSONA_EMPRESA_ROL,
                        COUNT(*) CANTIDAD_CASO
                      FROM
                        (SELECT IPER.ID_PERSONA_ROL,
                          COUNT(ICAS.ID_CASO)
                        FROM DB_SOPORTE.INFO_CASO ICAS,
                          DB_SOPORTE.INFO_DETALLE_HIPOTESIS IDHI,
                          DB_SOPORTE.INFO_DETALLE IDET,
                          DB_SOPORTE.INFO_PARTE_AFECTADA IPAF,
                          DB_COMERCIAL.INFO_PUNTO IPUN,
                          DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPER
                        WHERE ICAS.ID_CASO            = IDHI.CASO_ID
                        AND IDHI.ID_DETALLE_HIPOTESIS = IDET.DETALLE_HIPOTESIS_ID
                        AND IDET.ID_DETALLE           = IPAF.DETALLE_ID
                        AND IPAF.AFECTADO_ID          = IPUN.ID_PUNTO
                        AND IPER.ID_PERSONA_ROL       = IPUN.PERSONA_EMPRESA_ROL_ID
                        AND ICAS.ORIGEN               = :strOrigen
                        AND IPAF.TIPO_AFECTADO        = :strTipoAfectado
                        AND TRUNC(ICAS.FE_CREACION) BETWEEN TO_DATE(:strFeInicial,'dd/mm/yyyy')
                        AND TO_DATE(:strFeFinal,'dd/mm/yyyy')
                        GROUP BY ICAS.ID_CASO,
                          IPER.ID_PERSONA_ROL
                        ) TMP1
                      GROUP BY TMP1.ID_PERSONA_ROL";

        $objRsm->addScalarResult('ID_PERSONA_EMPRESA_ROL', 'idPersonaEmpresaRol', 'integer');
        $objRsm->addScalarResult('CANTIDAD_CASO',          'cantCasosMovil',      'integer');

        $objQuery->setParameter('strOrigen',       $arrayParametros['strOrigen']);
        $objQuery->setParameter('strTipoAfectado', $arrayParametros['strTipoAfectado']);
        $objQuery->setParameter('strFeInicial',    $arrayParametros['strFeInicial']);
        $objQuery->setParameter('strFeFinal',      $arrayParametros['strFeFinal']);
        $objQuery->setSQL($strSql);
        $arrayResult = $objQuery->getResult();
        return $arrayResult;
    }

    /**
     * Proceso para detectar y enviar la notificación de todo los clientes con
     * casos aperturados, previo al masivo creado.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 26-04-2019
     *
     * @param  Array $arrayParametros [intIdCaso = Id del caso]
     * @return Array $arrayRespuesta
     */
    public function ejecutarCasosAperturados($arrayParametros)
    {
        $intIdCaso = $arrayParametros['intIdCaso'];

        try
        {
            if ($intIdCaso === '' || $intIdCaso === null)
            {
                throw new \Exception('Número de caso Inválido');
            }

            $strSqlJ = "DECLARE
                            Lv_IdCaso VARCHAR(500) := '$intIdCaso';
                        BEGIN
                            DBMS_SCHEDULER.CREATE_JOB(job_name   => '\"DB_SOPORTE\".\"JOB_CASOS_CLIENTES_'||Lv_IdCaso||'\"',
                                                      job_type   => 'PLSQL_BLOCK',
                                                      job_action => '
                                                        DECLARE
                                                            Lc_ResultJson CLOB;
                                                            Lv_Error      VARCHAR2(1000);
                                                        BEGIN
                                                            DB_SOPORTE.SPKG_SOPORTE.P_DETECTAR_CLIENTES_CASOS(
                                                                    Pn_IdCaso     => $intIdCaso,
                                                                    Pb_Notificar  => TRUE, --True envia la notificación
                                                                    Pc_ResultJson => Lc_ResultJson,
                                                                    Pv_Error      => Lv_Error);
                                                        END;',
                                                      number_of_arguments => 0,
                                                      start_date          => NULL,
                                                      repeat_interval     => NULL,
                                                      end_date            => NULL,
                                                      enabled             => FALSE,
                                                      auto_drop           => TRUE,
                                                      comments            => 'Proceso para ejecutar los casos aperturados de un cliente.');

                            DBMS_SCHEDULER.SET_ATTRIBUTE(name      => '\"DB_SOPORTE\".\"JOB_CASOS_CLIENTES_'||Lv_IdCaso||'\"',
                                                         attribute => 'logging_level',
                                                         value     => DBMS_SCHEDULER.LOGGING_OFF);

                            DBMS_SCHEDULER.enable(name => '\"DB_SOPORTE\".\"JOB_CASOS_CLIENTES_'||Lv_IdCaso||'\"');

                        END;";

            $objStmt = $this->_em->getConnection()->prepare($strSqlJ);

            $objStmt->execute();

            $arrayRespuesta = array ('status' => 'ok',
                                     'message' => 'Proceso ejecutandose');
        }
        catch (\Exception $objException)
        {
            $arrayRespuesta = array ('status' => 'fail',
                                     'message' => $objException->getMessage());
        }
        return $arrayRespuesta;
    }

    /**
     * Función encargada de obtener los casos aperturados de un cliente.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 29-04-2019
     *
     * @param  Array $arrayParametros
     * @return Array $arrayRespuesta;
     * 
     * @author David De La Cruz <ddelacruz@telconet.ec>
     * @version 1.1 26-05-2022 Se cambia la invocación a la base de datos para definir variable tipo CLOB
     * y evitar desbordamiento de variable de un String de 10000 caracteres
     */
    public function getObtenerCasosClientes($arrayParametros)
    {
        $objContainer = $arrayParametros['objContainer'];
        $strUserSoporte = $objContainer->getParameter('user_soporte');
        $strPassSoporte = $objContainer->getParameter('passwd_soporte');
        $strDsn = $objContainer->getParameter('database_dsn');
        $arrayParametros['objContainer'] = '';

        try
        {
            $strSql = "BEGIN ".
                        "DB_SOPORTE.SPKG_SOPORTE.P_DETECTAR_CLIENTES_CASOS(Pn_IdCaso     => :Pn_IdCaso,".
                                                                          "Pb_Notificar  =>  FALSE,".
                                                                          "Pc_ResultJson => :Pc_ResultJson,".
                                                                          "Pv_Error      => :Pv_Error); ".
                      "END;";

            $objConexion = oci_connect($strUserSoporte,$strPassSoporte,$strDsn,'AL32UTF8');
            $objStmt     = oci_parse($objConexion,$strSql);
            $strJson = oci_new_descriptor($objConexion, OCI_D_LOB);

            oci_bind_by_name($objStmt,':Pn_IdCaso' ,$arrayParametros['intIdCaso']);
            oci_bind_by_name($objStmt,':Pv_Error'  ,$strMensajeError,4000);
            oci_bind_by_name($objStmt,':Pc_ResultJson', $strJson, -1, OCI_B_CLOB);
            
            oci_execute($objStmt);

            $arrayRespuesta = array ('status'  => 'ok',
                                     'message' => trim($strMensajeError),
                                     'result'  => trim($strJson->load()));
        }
        catch (\Exception $objException)
        {
            $arrayRespuesta = array('status'  => 'fail',
                                    'message' => $objException->getMessage());
        }
        return $arrayRespuesta;
    }

    /**
     * Método encargado de realizar el reporte de casos.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 19-06-2019
     *
     * @param  Array $arrayParametros => Se convierte en Json y se envía al procedimiento.
     * @return Array $arrayRespuesta
     */
    public function reporteCasos($arrayParametros)
    {
        $strStatus  = '';
        $strStatus  = str_pad($strStatus, 3000, " ");
        $strMessage = '';
        $strMessage = str_pad($strMessage, 3000, " ");

        try
        {
            $strSql = "BEGIN DB_SOPORTE.SPKG_REPORTES.P_REPORTE_CASOS(:Pcl_Json,".
                                                                     ":Pv_Status,".
                                                                     ":Pv_Message); END;";

            $objStmt = $this->_em->getConnection()->prepare($strSql);

            $objStmt->bindParam('Pcl_Json'   , json_encode($arrayParametros));
            $objStmt->bindParam('Pv_Status'  , $strStatus);
            $objStmt->bindParam('Pv_Message' , $strMessage);
            $objStmt->execute();

            $arrayRespuesta = array ('status'  => $strStatus,
                                     'message' => $strMessage);
        }
        catch (\Exception $objException)
        {
            $arrayRespuesta = array ('status'  => 'fail',
                                     'message' => $objException->getMessage());
        }
        return $arrayRespuesta;
    }

    /**
     * Método que crea el Job con auto drop para la generación del reporte de casos.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 19-06-2019
     *
     * @param  Array $arrayParametros => Se convierte en Json y se envía al procedimiento.
     * @return Array $arrayRespuesta
     */
    public function jobReporteCasos($arrayParametros)
    {
        $strUsuarioSolicita = $arrayParametros['strUsuarioSolicita'];

        try
        {
            if ($strUsuarioSolicita === '' || $strUsuarioSolicita === null)
            {
                throw new \Exception('No se pudo obtener el usuario en sesión');
            }

            $strUsuario = strtoupper($strUsuarioSolicita);
            $strJson    = json_encode($arrayParametros);

            $strSqlJ = "DECLARE
                            Lv_usuario VARCHAR(50) := '$strUsuario';
                        BEGIN
                            DBMS_SCHEDULER.CREATE_JOB(job_name   => '\"DB_SOPORTE\".\"JOB_REPORTE_CASOS_'||Lv_usuario||'\"',
                                                      job_type   => 'PLSQL_BLOCK',
                                                      job_action => '
                                                        DECLARE
                                                            Lv_Status  VARCHAR2(50);
                                                            Lv_Message VARCHAR2(3000);
                                                        BEGIN
                                                            DB_SOPORTE.SPKG_REPORTES.P_REPORTE_CASOS(
                                                                Pcl_Json   => ''$strJson'',
                                                                Pv_Status  => Lv_Status,
                                                                Pv_Message => Lv_Message);
                                                        END;',
                                                      number_of_arguments => 0,
                                                      start_date          => NULL,
                                                      repeat_interval     => NULL,
                                                      end_date            => NULL,
                                                      enabled             => FALSE,
                                                      auto_drop           => TRUE,
                                                      comments            => 'Proceso para ejecutar el reporte de casos.');

                            DBMS_SCHEDULER.SET_ATTRIBUTE(name      => '\"DB_SOPORTE\".\"JOB_REPORTE_CASOS_'||Lv_usuario||'\"',
                                                         attribute => 'logging_level',
                                                         value     => DBMS_SCHEDULER.LOGGING_OFF);

                            DBMS_SCHEDULER.enable(name => '\"DB_SOPORTE\".\"JOB_REPORTE_CASOS_'||Lv_usuario||'\"');

                        END;";

            $objStmt = $this->_em->getConnection()->prepare($strSqlJ);
            $objStmt->execute();

            $arrayRespuesta = array ('status'  => 'ok',
                                     'message' => 'Proceso Ejecutándose');
        }
        catch (\Exception $objException)
        {
            $arrayRespuesta = array ('status'  => 'fail',
                                     'message' => $objException->getMessage());
        }
        return $arrayRespuesta;
    }
    
    /**
     * Método encargado de obtener el SLA de acuerdo a la razón social/nombre y apellido
     * y el rango de fecha
     * Costo: 2
     *
     * @author Karen Rodríguez V. <kyrodriguez@telconet.ec>
     * @version 1.0 01-08-2019
     *
     * @param Array $arrayParametros [
     *                                  strRazonSocial     : Razon Social del cliente,
     *                                  strNombres         : Nombres del cliente,
     *                                  strApellidos       : Apellidos del cliente,
     *                                  strFechaInicio     : Fecha de inicio a consultar SLA,
     *                                  strFechaFin        : Fecha de fin a consultar SLA
     *                               ]
     * @return Array
     */
    public function getSLATelcograf($arrayParametros)
    {
        $strRazonSocial     = $arrayParametros['strRazonSocial'];
        $strNombres         = $arrayParametros['strNombres'];
        $strApellidos       = $arrayParametros['strApellidos'];
        $strFechaInicio     = $arrayParametros['strFechaInicio'];
        $strFechaFin        = $arrayParametros['strFechaFin'];
        
        $objRsm   = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null,$objRsm);        
        
        try
        {
            $strSql = "SELECT DB_SOPORTE.SPKG_GENERACION_SLA.FN_CONSULTAR_SLA( :strRazonSocial, "
                                                                            . ":strNombres, "
                                                                            . ":strApellidos, "
                                                                            . ":strFechaInicio, "
                                                                            . ":strFechaFin ) AS SLA FROM DUAL";
            
            $objQuery->setParameter('strRazonSocial', $strRazonSocial);
            $objQuery->setParameter('strNombres', $strNombres);
            $objQuery->setParameter('strApellidos', $strApellidos);
            $objQuery->setParameter('strFechaInicio', $strFechaInicio);
            $objQuery->setParameter('strFechaFin', $strFechaFin);
            $objQuery->setSQL($strSql);
            $objRsm->addScalarResult('SLA', 'sla', 'array');
            
            $strDatos = $objQuery->getScalarResult();
        }
        catch(\Exception $e)
        {
            throw($e);
        }
        return $strDatos[0]['sla'];
    }

    /**
     * Obtiene el historial de las tareas por Caso
     * 
     * @author Jose Bedon <jobedon@telconet.ec>
     * @version 1.0 15-01-2021
     * @param array $arrayParametros idCaso
     * @return array $arrayResultado
     * 
     */
    public function getTareasSolucionPorCaso($arrayParametros)
    {
        $intIdCaso = $arrayParametros['idCaso'];

        $objRsm   = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null,$objRsm);  

        try
        {
            $strSql = "SELECT AT.NOMBRE_TAREA
                        FROM DB_SOPORTE.INFO_CASO IC,
                        DB_SOPORTE.INFO_DETALLE_HIPOTESIS IDH,
                        DB_SOPORTE.INFO_DETALLE IDE,
                        DB_SOPORTE.INFO_DETALLE_HISTORIAL IDHT,
                        DB_SOPORTE.ADMI_TAREA AT
                        WHERE IC.ID_CASO             = IDH.CASO_ID
                        AND IDH.ID_DETALLE_HIPOTESIS = IDE.DETALLE_HIPOTESIS_ID
                        AND IDE.ID_DETALLE           = IDHT.DETALLE_ID
                        AND IDHT.TAREA_ID            = AT.ID_TAREA
                        AND IDHT.ES_SOLUCION         = 'S'
                        AND IDHT.ACCION             IN ('Reasignada','Finalizada')
                        AND IC.ID_CASO               = :idCaso
                        AND IDE.TAREA_ID            IS NOT NULL
                        ORDER BY IDHT.ID_DETALLE_HISTORIAL DESC";

            $objRsm->addScalarResult('NOMBRE_TAREA','nombreTarea','string');

            $objQuery->setParameter('idCaso', $intIdCaso);

            $objQuery->setSQL($strSql);
            $arrayResultado = $objQuery->getArrayResult();
            
        }
        catch(\Exception $e)
        {
            throw($e);
        }

        return $arrayResultado;

    }

    /**
     * Obtiene las Actividades que afectan al punto segun la fecha de busqueda
     * 
     * @author David De La Cruz <ddelacruz@telconet.ec>
     * @version 1.0 10-08-2021
     * @param array $arrayParametros [
     *                                  login       : Login del Punto,
     *                                  fechaPivote : Fecha de Busqueda,
     *                                  limit       : Limite de registros para paginacion,
     *                                  start       : Indice inicial segun paginacion
     *                               ]
     * @author Pedro Velez <psvelez@telconet.ec>
     * @version 1.1 08-11-2021 -  Se elimina la funcion lower en los campos login para mejorar tiempo de respuesta 
     *                            de la consulta.
     * 
     * @return array $arrayResultado
     * 
     */
    public function getActividadesPuntoAfectado($arrayParametros)
    {
        $strLogin       = $arrayParametros['login'] ? $arrayParametros['login'] : '';
        $strFechaPivote = $arrayParametros['fechaPivote'];
        $intLimit       = $arrayParametros['limit'] ? intval($arrayParametros['limit']) : 0;
        $intStart       = $arrayParametros['start'] ? intval($arrayParametros['start']) : 0;

        $objRsm   = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null,$objRsm);

        $objRsmCount = new ResultSetMappingBuilder($this->_em);
        $objQueryCount = $this->_em->createNativeQuery(null, $objRsmCount);       

        try
        {
            $strSelectCount = " SELECT COUNT(*) AS TOTAL ";
            $objRsmCount->addScalarResult('TOTAL', 'total', 'integer');

            $strSql = "SELECT to_char(iapa.ACTIVIDAD_ID) ACTIVIDAD_ID,iapa.TITULO_ACTIVIDAD,to_char(iapa.MOTIVO_ACTIVIDAD) MOTIVO_ACTIVIDAD,
                            iapa.RESPONSABLE,iapa.AREA_RESPONSABLE,iapa.CONTACTO_NOTIFICACION,
                            decode(iapa.NOTIFICADO,'S','Si','No') NOTIFICADO,iapa.FECHA_NOTIFICACION,
                            iapa.ASUNTO_NOTIFICACION,iapa.TIPO_AFECTACION,iapa.SERVICIOS_AFECTADOS,
                            iapa.ORIGEN_ACTIVIDAD,iapa.FE_INI_ACTIVIDAD,iapa.FE_FIN_ACTIVIDAD,iapa.ESTADO,
                            0 ID_CASO_TAREA, '' TIPO_CASO_TAREA 
                        FROM DB_SOPORTE.INFO_ACTIV_PUNTO_AFECTADO iapa 
                        WHERE (iapa.FE_INI_ACTIVIDAD >= to_date(:fechaPivote,'dd/mm/rrrr hh24:mi:ss')
                        OR iapa.FE_FIN_ACTIVIDAD >= to_date(:fechaPivote,'dd/mm/rrrr hh24:mi:ss'))
                        AND iapa.LOGIN_AFECTADO = :login
                        UNION
                        SELECT ic.NUMERO_CASO, ic.TITULO_INI||nvl2(ic.TITULO_FIN,' - '||ic.TITULO_FIN,''),
                            ic.VERSION_INI||nvl2(ic.VERSION_FIN,' - '||ic.VERSION_FIN,''),
                            ica.REF_ASIGNADO_NOMBRE,ica.ASIGNADO_NOMBRE,null,
                            null,null,
                            null,ic.TIPO_AFECTACION,(SELECT nombre_tipo_caso 
                                FROM DB_SOPORTE.ADMI_TIPO_CASO atc 
                                WHERE atc.ID_TIPO_CASO = ic.TIPO_CASO_ID),
                            'Telcos - Caso',ipaf.FE_INI_INCIDENCIA,ic.FE_CIERRE,
                            (select ichi.estado from  DB_SOPORTE.INFO_CASO_HISTORIAL ichi
                                where ichi.ID_CASO_HISTORIAL = (select max(ich.ID_CASO_HISTORIAL) 
                                    from  DB_SOPORTE.INFO_CASO_HISTORIAL ich 
                                    where ich.CASO_ID = ic.ID_CASO)) ESTADO,
                            ic.ID_CASO, 'C'
                            FROM (SELECT icas.*
                                FROM DB_SOPORTE.INFO_CASO icas
                                WHERE (icas.FE_APERTURA >=  to_date(:fechaPivote,'dd/mm/rrrr hh24:mi:ss')
                                OR nvl(icas.FE_CIERRE,sysdate) >= to_date(:fechaPivote,'dd/mm/rrrr hh24:mi:ss'))
                                AND icas.EMPRESA_COD = '10') ic
                            INNER JOIN DB_SOPORTE.INFO_DETALLE_HIPOTESIS idh ON ic.ID_CASO = idh.CASO_ID
                            INNER JOIN DB_SOPORTE.INFO_DETALLE ide ON idh.ID_DETALLE_HIPOTESIS = ide.DETALLE_HIPOTESIS_ID
                            INNER JOIN DB_SOPORTE.INFO_PARTE_AFECTADA ipaf ON ide.ID_DETALLE = ipaf.DETALLE_ID
                            LEFT OUTER JOIN DB_SOPORTE.INFO_CASO_ASIGNACION ica ON idh.ID_DETALLE_HIPOTESIS = ica.DETALLE_HIPOTESIS_ID
                            WHERE lower(ipaf.TIPO_AFECTADO) = 'cliente'
                            AND ipaf.AFECTADO_NOMBRE = :login
                        UNION
                        SELECT To_Char(Ico.Id_Comunicacion), Ata.Descripcion_Tarea, To_Char(Ide.Observacion), Ida.Ref_Asignado_Nombre,
                          Ida.Asignado_Nombre, NULL, NULL, NULL, NULL, Ap.Nombre_Proceso, '', 'Telcos - Tarea', Ico.Fecha_Comunicacion,
                          NULL,
                          (
                            SELECT
                              Idhi.Estado
                            FROM
                              Db_Soporte.Info_Detalle_Historial Idhi
                            WHERE
                              Idhi.Id_Detalle_Historial = (
                                SELECT
                                  MAX(Idh.Id_Detalle_Historial)
                                FROM
                                  Db_Soporte.Info_Detalle_Historial Idh
                                WHERE
                                  Idh.Detalle_Id = Ico.Detalle_Id
                              )
                          ),
                          Ico.Id_Comunicacion,
                          'T'
                        FROM
                               Db_Comunicacion.Info_Comunicacion Ico
                          INNER JOIN Db_Soporte.Info_Detalle               Ide ON Ico.Detalle_Id = Ide.Id_Detalle
                          INNER JOIN Db_Soporte.Admi_Tarea                 Ata ON Ide.Tarea_Id = Ata.Id_Tarea
                          INNER JOIN Db_Soporte.Admi_Proceso               Ap ON Ata.Proceso_Id = Ap.Id_Proceso
                          INNER JOIN Db_Soporte.Info_Criterio_Afectado     Ica ON Ide.Id_Detalle = Ica.Detalle_Id
                          INNER JOIN Db_Soporte.Info_Parte_Afectada        Ipa ON Ica.Detalle_Id = Ipa.Detalle_Id
                                                                           AND Ica.Id_Criterio_Afectado = Ipa.Criterio_Afectado_Id
                          INNER JOIN Db_Comercial.Info_Punto               Ipu ON Ipa.Afectado_Id = Ipu.Id_Punto
                          INNER JOIN Db_Comercial.Info_Persona_Empresa_Rol Iper ON Ipu.Persona_Empresa_Rol_Id = Iper.Id_Persona_Rol
                          INNER JOIN Db_Comercial.Admi_Forma_Contacto      Afc ON Ico.Forma_Contacto_Id = Afc.Id_Forma_Contacto
                          INNER JOIN Db_Soporte.Info_Detalle_Asignacion    Ida ON Ide.Id_Detalle = Ida.Detalle_Id
                        WHERE
                            Ico.Fecha_Comunicacion >= TO_DATE(:fechaPivote, 'dd/mm/rrrr hh24:mi:ss')
                          AND Ico.Empresa_Cod = '10'
                          AND Ipa.Afectado_Nombre = :login
                        ORDER BY 13";

            $objRsm->addScalarResult('ACTIVIDAD_ID','idActividad','string');
            $objRsm->addScalarResult('TITULO_ACTIVIDAD','titulo','string');
            $objRsm->addScalarResult('MOTIVO_ACTIVIDAD','motivo','string');
            $objRsm->addScalarResult('RESPONSABLE','responsable','string');
            $objRsm->addScalarResult('AREA_RESPONSABLE','areaResponsable','string');
            $objRsm->addScalarResult('CONTACTO_NOTIFICACION','contacto','string');
            $objRsm->addScalarResult('NOTIFICADO','notificado','string');
            $objRsm->addScalarResult('FECHA_NOTIFICACION','fechaNotificacion','string');
            $objRsm->addScalarResult('ASUNTO_NOTIFICACION','asuntoNotificacion','string');
            $objRsm->addScalarResult('TIPO_AFECTACION','tipoAfectacion','string');
            $objRsm->addScalarResult('SERVICIOS_AFECTADOS','serviciosAfectados','string');
            $objRsm->addScalarResult('ORIGEN_ACTIVIDAD','origen','string');
            $objRsm->addScalarResult('FE_INI_ACTIVIDAD','fechaInicio','string');
            $objRsm->addScalarResult('FE_FIN_ACTIVIDAD','fechaFin','string');
            $objRsm->addScalarResult('ESTADO','estado','string');
            $objRsm->addScalarResult('ID_CASO_TAREA','idCasoTarea','string');
            $objRsm->addScalarResult('TIPO_CASO_TAREA','tipoCasoTarea','string');

            $objQuery->setParameter('login', $strLogin);
            $objQuery->setParameter('fechaPivote', $strFechaPivote);
            
            $strSqlCount = $strSelectCount ." FROM (".$strSql.")";
            $objQueryCount->setParameter('login', $strLogin);
            $objQueryCount->setParameter('fechaPivote', $strFechaPivote);
            $objQueryCount->setSQL($strSqlCount);      
            $intTotalActividades = $objQueryCount->getSingleScalarResult();
            
            $objQuery->setSQL($strSql);
            $arrayActividades = $this->setQueryLimit($objQuery,$intLimit,$intStart)->getArrayResult();
            
            $arrayResultado = array ('status'      => 'ok',
                                     'total'       => $intTotalActividades,
                                     'actividades' => $arrayActividades);
            
        }
        catch(\Exception $objException)
        {
            error_log($objException->getMessage());
            $arrayResultado = array ('status'  => 'fail',
                                     'message' => $objException->getMessage());
        }

        return $arrayResultado;

    }

    /**
     * Función encargada de obtener datos para envio de informacion al ws ACTUALIZAR_TRACKING.
     *
     * @author Pedro Velez <psvelez@telconet.ec>
     * @version 1.0 06-09-2021
     *
     * @param  Array $arrayParametros
     * @return Array $arrayRespuesta;
     */
    public function getDatosActualizarTracking($arrayParametros)
    {
        $strString       = '';
        $strJson         = str_pad($strString, 10000, " ");
        $intMensajeError = 0;

        try  
        {
            $strSql = "BEGIN ".
                      "DB_SOPORTE.SPKG_SOPORTE.P_OBTIENE_DATOS_TRACKING(Pn_IdDetalle   => :Pn_IdDetalle,".
                                                                        "Pv_UsrCreacion => :Pv_UsrCreacion,".
                                                                        "Pc_ResultClob  => :Pc_ResultClob,".
                                                                        "Pn_Error       => :Pn_Error); ".
                      "END;";

            $objStmt = $this->_em->getConnection()->prepare($strSql);

            $objStmt->bindParam('Pn_IdDetalle'   , $arrayParametros['intIdDetalle']);
            $objStmt->bindParam('Pv_UsrCreacion' , $arrayParametros['strUsrCreacion']);
            $objStmt->bindParam('Pc_ResultClob'  , $strJson);
            $objStmt->bindParam('Pn_Error'       , $intMensajeError);
            $objStmt->execute();
            
            if ($intMensajeError == 0)
            {
                $arrayRespuesta = array ('status'  => 'ok',
                                         'result'  => trim($strJson));
            }
            else
            {
                $arrayRespuesta = array ('status'  => 'fail',
                                         'message' => 'Error al obtener datos');
            }

        }
        catch (\Exception $ex)
        {
            $arrayRespuesta = array('status'  => 'fail',
                                    'message' => $ex->getMessage());
        }
        return $arrayRespuesta;
    }

    /**
     * Función encargada de insertar datos enviados al ws ACTUALIZAR_TRACKING cuando se produce algun error. 
     *
     * @author Pedro Velez <psvelez@telconet.ec>
     * @version 1.0 06-09-2021
     * 
     * @author Pedro Velez <psvelez@telconet.ec>
     * @version 1.1 07-01-2022
     * Se agrega envio de parametro de dispositivo id 
     * 
     * @param  Array $arrayParametros
     * @return String $strRespuesta;
     */
    public function ingresaDatosTracking($arrayParametros)
    {
        $intMensajeError = 0;
        $strDatos = "";

        try
        {
            $strDatos = $arrayParametros['intIdDetalle'].'|'.$arrayParametros['strNumeroCaso'].'|'.$arrayParametros['intNumeroTarea'];
            $strDatos = $strDatos."|".$arrayParametros['strTipoEvento']."|".$arrayParametros['strIdentificacion'];
            $strDatos = $strDatos."|".$arrayParametros['strTituloInicial']."|".$arrayParametros['strTituloFinal'];
            $strDatos = $strDatos."|".$arrayParametros['strLogin'];
            $strDatos = $strDatos."|".$arrayParametros['floatLatitud']."|".$arrayParametros['floatLongitud'];
            $strDatos = $strDatos."|".$arrayParametros['strFechaEvento']."|".$arrayParametros['floatDistancia'];
            $strDatos = $strDatos."|".$arrayParametros['strEmpresa']."|".$arrayParametros['strOpcion'];
            $strDatos = $strDatos."|".$arrayParametros['strUsrCreacion']."|".$arrayParametros['strIpCreacion'];
            $strDatos = $strDatos."|".$arrayParametros['floatLatitudTec']."|".$arrayParametros['floatLongitudTec'];
            $strDatos = $strDatos."|".$arrayParametros['strFechaAgenda']."|".$arrayParametros['strHoraAgenda'];
            $strDatos = $strDatos."|".$arrayParametros['strDispositivoId']."|";

            $strSql = "BEGIN ".
                      "DB_SOPORTE.SPKG_SOPORTE.P_INSERTA_ERROR_TRACKING(Pv_Datos  => :Pv_Datos,".
                                                                      "Pv_Direccion => :Pv_Direccion,".
                                                                      "Pv_Observacion => :Pv_Observacion,".
                                                                      "Pv_LiderCuadrilla => :Pv_LiderCuadrilla,";
            $strSql  .= "Pv_NombreTecnico => :Pv_NombreTecnico,".
                        "Pv_cedulaTecnico => :Pv_cedulaTecnico,".                                                                      
                        "Pv_codigoTrabajo => :Pv_codigoTrabajo,".
                        "Pn_Error  => :Pn_Error); ".
                      "END;";
            
            $objStmt = $this->_em->getConnection()->prepare($strSql);
            $objStmt->bindParam('Pv_Datos' , $strDatos);    
            $objStmt->bindParam('Pv_Direccion' , $arrayParametros['strDireccion']); 
            $objStmt->bindParam('Pv_Observacion' , $arrayParametros['strObservacion']); 
            $objStmt->bindParam('Pv_LiderCuadrilla' , $arrayParametros['strLiderCuadrilla']);      
            $objStmt->bindParam('Pv_NombreTecnico' , $arrayParametros['strNombreTecnico']);  
            $objStmt->bindParam('Pv_cedulaTecnico' , $arrayParametros['strCedulaTecnico']);   
            $objStmt->bindParam('Pv_codigoTrabajo' , $arrayParametros['strCodigoTrabajo']);     
            $objStmt->bindParam('Pn_Error' , $intMensajeError);

            $objStmt->execute();
            
            if ($intMensajeError == 0)
            {
                $strRespuesta = 'ok';
            }
            else    
            {
                $strRespuesta = 'fail';
            }                     

        }
        catch (\Exception $ex)
        {
            $strRespuesta = 'error';
            $strPruebas = $ex->getMessage();
        }
        return $strRespuesta;
    }

        /**
     * Función encargada de actualizar estado de datos enviados al ws ACTUALIZAR_TRACKING cuando se produce algun error. 
     *
     * @author Pedro Velez <psvelez@telconet.ec>
     * @version 1.0 06-09-2021
     *
     * @param  Array $arrayParametros
     * @return String $strRespuesta;
     */
    public function actualizaDatosTracking($arrayParametros)
    {
        $intMensajeError = 0;
        $strEstado = "Finalizado";
        try
        {
            $strSql = "BEGIN ".
                      "DB_SOPORTE.SPKG_SOPORTE.P_ACTUALIZA_ERROR_TRACKING(Pn_IdDetalle => :Pn_IdDetalle,".
                                                                        "Pv_Estado    => :Pv_Estado,".
                                                                        "Pn_Error     => :Pn_Error); ".
                      "END;";

            $objStmt = $this->_em->getConnection()->prepare($strSql);

            $objStmt->bindParam('Pn_IdDetalle' , $arrayParametros['intIdDetalle']);            
            $objStmt->bindParam('Pv_Estado' , $strEstado);
            $objStmt->bindParam('Pn_Error' , $intMensajeError);
            $objStmt->execute();
            
            if ($intMensajeError == 0)
            {
                $strRespuesta = 'ok';
            }
            else    
            {
                $strRespuesta = 'fail';
            }                     

        }
        catch (\Exception $ex)
        {
            $strRespuesta = 'error';
            $strPruebas = $ex->getMessage();
        }
        return $strRespuesta;
    }

            /**
     * Función encargada de obtener informacion no enviada al web service ACTUALIZAR_TRACKING. 
     *
     * @author Pedro Velez <psvelez@telconet.ec>
     * @version 1.0 06-09-2021
     *
     * @param  Array $arrayParametros
     * @return String $arrayRespuesta;
     */
    public function obtenerDatosErrorTracking($arrayParametros)
    {
        $intMensajeError = 0;
        $strString       = '';
        $strJson         = str_pad($strString, 10000, " ");
        $intNumregistro  = 0;
        $arrayEvento = array();
        $arrayRespuesta = array();
        $strEstado = "Error";
        try
        {
            $strSql = "BEGIN ".
                      "DB_SOPORTE.SPKG_SOPORTE.P_OBTIENE_ERROR_TRACKING(Pn_IdDetalle => :Pn_IdDetalle,".
                                                                        "Pv_Estado  => :Pv_Estado,".
                                                                        "Pc_ResultClob => :Pc_ResultClob,".
                                                                        "Pn_NumeroReg => :Pn_NumeroReg,".
                                                                        "Pn_Error   => :Pn_Error); ".
                      "END;";

            $objStmt = $this->_em->getConnection()->prepare($strSql);

            $objStmt->bindParam('Pn_IdDetalle' , $arrayParametros['intIdDetalle']);            
            $objStmt->bindParam('Pv_Estado' , $strEstado );
            $objStmt->bindParam('Pc_ResultClob' , $strJson);
            $objStmt->bindParam('Pn_NumeroReg' , $intNumregistro);
            $objStmt->bindParam('Pn_Error' , $intMensajeError);
            $objStmt->execute();
             
            if ($intMensajeError == 0 && $intNumregistro > 0)
            {
                $arrayEvento = explode('|',substr(trim($strJson),0, -1));
                foreach( $arrayEvento as $strEvento)
                {
                    $arrayRespuesta[] = (array) json_decode($strEvento);

                }

            }

        }
        catch (\Exception $ex)
        {
            $strPruebas = $ex->getMessage();
        }
        return $arrayRespuesta;
    }  

    /**
     * Función que retorna si la tarea pertenece a un caso tecnico.
     *
     * @param Array $arrayParametros [
     *                                 intDetalleId : id del detalle de tarea.
     *                               ]
     *
     * @return boolean $boolEsCasoTec
     *
     * Costo query: 36
     *
     * @author Pedro Velez <psvelez@telconet.ec>
     * @version 1.0 15-10-2021
     * 
     * Se agrega validacion para casos TN
     * @author Pedro Velez <psvelez@telconet.ec>
     * @version 1.1 05-01-2021
     * 
     * Se agrega validacion para casos EN
     * @author Wiliam Sanchez <wdsanchez@telconet.ec>
     * @version 1.1 05-01-2021
     */
    public function isCasoTecnico($arrayParametros)
    {
        $intCasoTec = 0;
        $boolEsCasoTec = false;
        try
        {
            $objRsmb  = new ResultSetMappingBuilder($this->_em);
            $objQuery = $this->_em->createNativeQuery(null, $objRsmb);

            $strSql   = " SELECT COUNT(IPA.ID_PARTE_AFECTADA) AS CASO ".
                        "   FROM DB_SOPORTE.INFO_DETALLE_HIPOTESIS  DH, ".
                        "        DB_SOPORTE.INFO_DETALLE ID, ".
                        "        DB_SOPORTE.INFO_PARTE_AFECTADA  IPA ".
                        "  WHERE DH.ID_DETALLE_HIPOTESIS = ID.DETALLE_HIPOTESIS_ID ".
                        "    AND DH.CASO_ID  = ( ".
                        "                  SELECT B.ID_CASO ".
                        "                     FROM DB_SOPORTE.INFO_DETALLE A, ".
                        "                          DB_SOPORTE.INFO_DETALLE_HIPOTESIS S, ".
                        "                          DB_SOPORTE.INFO_CASO B ".
                        "                    WHERE A.DETALLE_HIPOTESIS_ID = S.ID_DETALLE_HIPOTESIS ".
                        "                      AND S.CASO_ID         = B.ID_CASO ".
                        "                      AND A.ID_DETALLE      = :idDetalle  ".
                        "                      AND B.TIPO_CASO_ID    = 1 ".
                        "                      AND b.empresa_cod in ('18','33')".
                        "                ) ".
                        "    AND IPA.DETALLE_ID  = ID.ID_DETALLE ".
                        "    AND IPA.TIPO_AFECTADO = 'Cliente'";

            $objQuery->setParameter('idDetalle' ,  $arrayParametros['intDetalleId']);
            $objRsmb->addScalarResult('CASO' , 'caso' , 'integer');
            $objQuery->setSQL($strSql);

            $intCasoTec = $objQuery->getSingleScalarResult();
            if ($intCasoTec > 0)
            {
                $boolEsCasoTec = true;
            }
        }
        catch(\Exception $ex)
        {
            $boolEsCasoTec = false;
        }
        return $boolEsCasoTec;
    }

    /**
     * Función encargada de obtener la cantidad de casos aperturados segun tareas
     *
     * @author David De La Cruz <ddelacruz@telconet.ec>
     * @version 1.0 11-03-2022
     *
     * @param  Array $arrayParametros
     * @return String $arrayResponse;
     */
    public function getCantidadCasosSegunTareas($arrayParametros)
    {
        $strResponse   = str_pad('', 10000, " ");
        $strEstado = str_pad('', 200, " ");
        $strMensaje  = str_pad('', 1000, " ");
        $arrayResponse = array();
        try
        {
            $strSql = "BEGIN ".
                      "DB_SOPORTE.SPKG_CASOS_CONSULTA.P_GET_CANT_CASOS_SEGUN_TAREAS(Pcl_Request => :Pcl_Request,".
                                                                                    "Pv_Status  => :Pv_Status,".
                                                                                    "Pv_Mensaje => :Pv_Mensaje,".
                                                                                    "Pcl_Response => :Pcl_Response); ".
                      "END;";

            $objStmt = $this->_em->getConnection()->prepare($strSql);

            $objStmt->bindParam('Pcl_Request' , json_encode($arrayParametros));            
            $objStmt->bindParam('Pv_Status', $strEstado);
            $objStmt->bindParam('Pv_Mensaje', $strMensaje);
            $objStmt->bindParam('Pcl_Response', $strResponse);
            $objStmt->execute();
            
            $arrayResponse = (array) json_decode($strResponse,true);
            
        }
        catch (\Exception $ex)
        {
            $arrayResponse['mensaje'] = 'Error al consultar casos según tareas';
        }
        return $arrayResponse;
    }  

}

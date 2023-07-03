<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class InfoDocumentoRepository extends EntityRepository
{
    /**
     * getResultadoContratosExternosDigitales
     * 
     * Obtiene los documentos asociados a un Punto y sus servicios de Tipo Venta Externa
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 14-02-2017
     * 
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.1 24-07-2018 - Se envía el tipo de documento general por parámetros dado que es usado para flujos de telconet Cloudform
     * 
     * costoQuery: 8
     * @param  array $arrayParametros [
     *                                  "intIdPunto"        : Id del Punto
     *                                  "intStart"          : inicio el rownum,
     *                                  "intLimit"          : fin del rownum
     *                                  "serviceRouter"     : Service Router
     *                                ]
     * 
     * @return json $arrayResultado
     */
    public function getResultadoContratosExternosDigitales($arrayParametros)
    {        
        $strSqlDatos      = ' SELECT IDC.id, ATDG.descripcionTipoDocumento,
                              IDC.ubicacionFisicaDocumento, IDC.ubicacionLogicaDocumento,
                              IDC.usrCreacion, IDC.feCreacion '; 
         
        $strSqlCantidad   = ' SELECT count(IDC) '; 
        
        $strSqlFrom       = ' FROM schemaBundle:InfoPunto PTO,
                                   schemaBundle:InfoDocumentoRelacion IDR,
                                   schemaBundle:InfoDocumento IDC,
                                   schemaBundle:AdmiTipoDocumentoGeneral ATDG 
                              WHERE 
                              IDR.estado = :strEstadoActivo 
                              AND IDC.estado = :strEstadoActivo
                              AND IDR.puntoId = :intIdPunto 
                              AND ATDG.codigoTipoDocumento = :strCodigoTipoDoc
                              AND IDR.puntoId = PTO.id
                              AND IDR.documentoId = IDC.id 
                              AND IDC.tipoDocumentoGeneralId = ATDG.id ';
        
        $strSqlGroupBy    = " GROUP BY IDC.id, ATDG.descripcionTipoDocumento,
                              IDC.ubicacionFisicaDocumento, IDC.ubicacionLogicaDocumento,
                              IDC.usrCreacion, IDC.feCreacion ";
        $strSqlOrderBy    = " ORDER BY IDC.id DESC ";
        
        $strQueryDatos   = '';
        $strQueryDatos   = $this->_em->createQuery();
        $strQueryDatos->setParameter('intIdPunto', $arrayParametros['intIdPunto']);
        $strQueryDatos->setParameter('strEstadoActivo', 'Activo');
        $strQueryDatos->setParameter('strCodigoTipoDoc', $arrayParametros['strCodigoTipoDoc']);
        
        $strSqlDatos    .= $strSqlFrom;
        $strSqlDatos    .= $strSqlGroupBy; 
        $strSqlDatos    .= $strSqlOrderBy;
        $strQueryDatos->setDQL($strSqlDatos);       
        $objDatos        = $strQueryDatos->setFirstResult($arrayParametros['intStart'])->setMaxResults($arrayParametros['intLimit'])->getResult();
        
        $strQueryCantidad = '';
        $strQueryCantidad = $this->_em->createQuery();
        $strQueryCantidad->setParameter('intIdPunto', $arrayParametros['intIdPunto']);
        $strQueryCantidad->setParameter('strEstadoActivo', 'Activo');
        $strQueryCantidad->setParameter('strCodigoTipoDoc', $arrayParametros['strCodigoTipoDoc']);
        
        $strSqlCantidad .= $strSqlFrom;
        $strQueryCantidad->setDQL($strSqlCantidad);
        $intTotal        = $strQueryCantidad->getSingleScalarResult();
        
        $arrayResultado['objRegistros'] = $objDatos;
        $arrayResultado['intTotal']     = $intTotal;
       
        return $arrayResultado;
    }
        
     /**
     * getContratosExternosDigitalesXPunto
     *
     * Metodo encargado de obtener los servicios asociados a un documento Digital de Tipo VENTA EXTERNA
     *     
     * costoQuery: 10
     * @param  array $arrayParam [
     *                                  "intIdPunto"        : Id del Punto
     *                                  "intIdDocumento"    : Id del Documento                                
     *                                ]              
     *
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 16-02-2017
     * @return $arrayResultado arreglo con servicios Asociados a un documento de tipo VENTA EXTERNA
 
     */  
     public function getContratosExternosDigitalesXPunto($arrayParam)
    {
        $objRsm      = new ResultSetMappingBuilder($this->_em);
        $objNtvQuery = $this->_em->createNativeQuery(null, $objRsm);

        $strSelect   = "SELECT S.ID_SERVICIO, P.DESCRIPCION_PRODUCTO, S.PRECIO_VENTA, S.ESTADO
                       FROM
                       DB_COMUNICACION.INFO_DOCUMENTO A,
                       DB_COMUNICACION.INFO_DOCUMENTO_RELACION B,
                       DB_GENERAL.ADMI_TIPO_DOCUMENTO_GENERAL C, 
                       DB_COMERCIAL.INFO_SERVICIO  S,
                       DB_COMERCIAL.ADMI_PRODUCTO  P    
                           
                       WHERE  
                       A.ESTADO                    = :strEstadoActivo AND
                       B.ESTADO                    = :strEstadoActivo AND
                       B.MODULO                    = :strModulo AND
                       B.PUNTO_ID                  = :intIdPunto AND 
                       A.ID_DOCUMENTO              = :intIdDocumento AND
                       C.CODIGO_TIPO_DOCUMENTO     = :strCodigoTipoDoc AND
                       A.ID_DOCUMENTO              = B.DOCUMENTO_ID AND 
                       A.TIPO_DOCUMENTO_GENERAL_ID = C.ID_TIPO_DOCUMENTO AND
                       S.ID_SERVICIO               = B.SERVICIO_ID AND
                       P.ID_PRODUCTO               = S.PRODUCTO_ID ";

        $objRsm->addScalarResult('ID_SERVICIO', 'idServicio', 'integer');
        $objRsm->addScalarResult('DESCRIPCION_PRODUCTO', 'descripcionProducto', 'string');
        $objRsm->addScalarResult('PRECIO_VENTA', 'precioVenta', 'float');
        $objRsm->addScalarResult('ESTADO', 'estado', 'string');

        $objNtvQuery->setParameter('strEstadoActivo', 'Activo');
        $objNtvQuery->setParameter('strModulo', 'COMERCIAL');
        $objNtvQuery->setParameter('intIdPunto', $arrayParam['intIdPunto']);
        $objNtvQuery->setParameter('intIdDocumento', $arrayParam['intIdDocumento']);
        $objNtvQuery->setParameter('strCodigoTipoDoc', 'VTAEX');

        $objNtvQuery->setSQL($strSelect);
        $arrayResultado = $objNtvQuery->getResult();
        return $arrayResultado;
    }

    /**
     * getContratosExternosDigitales
     * 
     * Obtiene los archivos digitales asociados a un Login y sus servicios de Tipo Venta Externa
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 14-02-2017
     * 
     * @param  array $arrayParametros [
     *                                  "intIdPunto"        : Id del Punto
     *                                  "intStart"          : inicio el rownum,
     *                                  "intLimit"          : fin del rownum
     *                                  "serviceRouter"     : $this->container->get('router')
     *                                ]     
     * 
     * @return array $arrayRespuesta
     */
    public function getContratosExternosDigitales($arrayParametros)
    {
        $arrayEncontrados = array();
        $arrayResultado   = $this->getResultadoContratosExternosDigitales($arrayParametros);
        $intIdPunto       = $arrayParametros['intIdPunto'];
        $serviceRouter    = $arrayParametros["serviceRouter"];
        $objRegistros     = $arrayResultado['objRegistros'];
        $intTotal         = $arrayResultado['intTotal'];        
        
        if(($objRegistros))
        {
            foreach($objRegistros as $arrayDocumentos)
            {
                $boolElimina                  = true;
                $arrayParam                   = array();
                $arrayParam['intIdPunto']     = $intIdPunto;
                $arrayParam['intIdDocumento'] = $arrayDocumentos['id'];
                $strDescripcion               = '';
                $arrayInfoContratoExternoxPto = $this->getContratosExternosDigitalesXPunto($arrayParam);  
                foreach ($arrayInfoContratoExternoxPto as $arrayDataDocxPto)
                {                  
                    $strDescripcion .=  ' <ul>';    
                    $strDescripcion .=  ' <li><b>Servicio:</b> '.$arrayDataDocxPto['descripcionProducto']
                                       .' <b>Precio:</b> '.$arrayDataDocxPto['precioVenta']
                                       .' <b>Estado:</b> '.$arrayDataDocxPto['estado'];
                    $strDescripcion .=  ' </li>';
                    if($arrayDataDocxPto['estado']!='Factible')
                    {
                       $boolElimina = false; 
                    }
                }
                if( $boolElimina )
                {
                   $linkEliminarDocumento = $serviceRouter->generate('infopunto_eliminarContratoExternoDigital',
                                                                     array('intIdDocumento' => $arrayDocumentos['id'])
                                                                    ); 
                }
                else
                {
                    $linkEliminarDocumento = '';
                }
                
                $arrayEncontrados[] = array(
                   'id'                      => $arrayDocumentos['id'],
                   'ubicacionLogicaDocumento'=> $arrayDocumentos['ubicacionLogicaDocumento'],
                   'tipoDocumentoGeneral'    => $arrayDocumentos['descripcionTipoDocumento'],
                   'feCreacion'              => $arrayDocumentos['feCreacion'] ? strval(date_format($arrayDocumentos['feCreacion'],"d-m-Y H:i")):"",
                   'usrCreacion'             => $arrayDocumentos['usrCreacion'],
                   'descripcion'             => $strDescripcion,
                   'linkVerDocumento'        => $serviceRouter->generate('infopunto_descargarContratoExternoDigital',
                                                                         array('intIdDocumento' => $arrayDocumentos['id'])),
                   'linkEliminarDocumento'   => $linkEliminarDocumento,
                    
                );
            }
        }        
        $arrayRespuesta = array('total' => $intTotal, 'logs' => $arrayEncontrados);
        return $arrayRespuesta;
        
    }
    
    /**
     * getResultadoOrdenesTrabajoVehiculo
     * 
     * Obtiene los documentos de un vehículo
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 22-08-2016
     * 
     * @param  array $arrayParametros [
     *                                  "idElemento"        : id del vehículo
     *                                  "visibleEnElemento" : tipos de documentos dependiendo si están o no visibles en la subida de archivos 
     *                                                        para transportes
     *                                  "intStart"          : inicio el rownum,
     *                                  "intLimit"          : fin del rownum
     *                                ]
     * 
     * @return json $arrayRespuesta
     */
    public function getResultadoDocumentosTransporte($arrayParametros)
    {
        $arrayRespuesta['total']     = 0;
        $arrayRespuesta['resultado'] = "";
        try
        {
            $rsmCount = new ResultSetMappingBuilder($this->_em);
            $ntvQueryCount = $this->_em->createNativeQuery(null, $rsmCount);
            
            $strSelectCount = " SELECT COUNT(*) AS TOTAL ";
            
            $rsm = new ResultSetMappingBuilder($this->_em);
            $ntvQuery = $this->_em->createNativeQuery(null, $rsm);
            
            $strSelect = " SELECT idr.ID_DOCUMENTO_RELACION,elemento.ID_ELEMENTO,atdg.DESCRIPCION_TIPO_DOCUMENTO, "
                        . " idc.ID_DOCUMENTO,idc.UBICACION_FISICA_DOCUMENTO, idc.UBICACION_LOGICA_DOCUMENTO,idr.USR_CREACION, "
                        . " idr.FE_CREACION, idc.FECHA_HASTA ";
            
            $strFrom =" FROM 
                        DB_INFRAESTRUCTURA.INFO_ELEMENTO elemento  
                        INNER JOIN DB_COMUNICACION.INFO_DOCUMENTO_RELACION idr ON elemento.ID_ELEMENTO = idr.ELEMENTO_ID
                        INNER JOIN DB_COMUNICACION.INFO_DOCUMENTO idc ON idc.ID_DOCUMENTO = idr.DOCUMENTO_ID 
                        INNER JOIN DB_GENERAL.ADMI_TIPO_DOCUMENTO_GENERAL atdg ON atdg.ID_TIPO_DOCUMENTO = idc.TIPO_DOCUMENTO_GENERAL_ID
                        WHERE elemento.ESTADO= :estadoActivo 
                        AND idr.estado = :estadoActivo
                        ";

            $rsm->addScalarResult('ID_DOCUMENTO_RELACION','idDocumentoRelacion','integer');
            $rsm->addScalarResult('ID_ELEMENTO','idElemento','integer');
            $rsm->addScalarResult('ID_DOCUMENTO','idDocumento','integer');
            $rsm->addScalarResult('UBICACION_FISICA_DOCUMENTO', 'ubicacionFisicaDocumento','string');
            $rsm->addScalarResult('UBICACION_LOGICA_DOCUMENTO', 'ubicacionLogicaDocumento','string');
            $rsm->addScalarResult('DESCRIPCION_TIPO_DOCUMENTO', 'tipoDocumentoGeneral','string');
            $rsm->addScalarResult('USR_CREACION', 'usrCreacion','string');
            $rsm->addScalarResult('FE_CREACION', 'feCreacion','datetime');
            $rsm->addScalarResult('FECHA_HASTA', 'feCaducidad','datetime');
            $rsm->addScalarResult('FE_FIN', 'feFin','datetime');
            
            $rsmCount->addScalarResult('TOTAL','total','integer');
            
            $strWhere = "";
            

            if(isset($arrayParametros['idElemento']) )
            {
                if($arrayParametros['idElemento'])
                {
                    $strWhere .= 'AND idr.ELEMENTO_ID = :idElemento ';        
                    $ntvQuery->setParameter('idElemento', $arrayParametros['idElemento']);
                    $ntvQueryCount->setParameter('idElemento', $arrayParametros['idElemento']);
                }
            }
            
            if(isset($arrayParametros['visibleEnElemento']) )
            {
                if($arrayParametros['visibleEnElemento'])
                {
                    $strWhere .= 'AND atdg.ELEMENTO = :visibleEnElemento  ';        
                    $ntvQuery->setParameter('visibleEnElemento', $arrayParametros['visibleEnElemento']);
                    $ntvQueryCount->setParameter('visibleEnElemento', $arrayParametros['visibleEnElemento']);
                }
            }
            
            $ntvQuery->setParameter('estadoActivo', 'Activo');
            $ntvQueryCount->setParameter('estadoActivo', 'Activo');
            
            
            $strOrderBy=" ORDER BY idr.ID_DOCUMENTO_RELACION DESC ";
            
            $strSqlPrincipal = $strSelect.$strFrom.$strWhere.$strOrderBy;

            $strSqlFinal = '';

            if(isset($arrayParametros['intStart']) && isset($arrayParametros['intLimit']))
            {
                if($arrayParametros['intStart'] && $arrayParametros['intLimit'])
                {
                    $intInicio = $arrayParametros['intStart'];
                    $intFin = $arrayParametros['intStart'] + $arrayParametros['intLimit'];
                    $strSqlFinal = '  SELECT * FROM 
                                        (
                                            SELECT consultaPrincipal.*,rownum AS consultaPrincipal_rownum 
                                            FROM (' . $strSqlPrincipal . ') consultaPrincipal 
                                            WHERE rownum<=' . $intFin . '
                                        ) WHERE consultaPrincipal_rownum >' . $intInicio;
                }
                else
                {
                    $strSqlFinal = '  SELECT consultaPrincipal.* 
                                        FROM (' . $strSqlPrincipal . ') consultaPrincipal 
                                        WHERE rownum<=' . $arrayParametros['intLimit'];
                }
            }
            else
            {
                $strSqlFinal = $strSqlPrincipal;
            }

            $ntvQuery->setSQL($strSqlFinal);
            $arrayResultado = $ntvQuery->getResult();
            
            $strSqlCount = $strSelectCount . " FROM (" . $strSqlPrincipal . ")";
            $ntvQueryCount->setSQL($strSqlCount);
            
            $intTotal = $ntvQueryCount->getSingleScalarResult();


            $arrayRespuesta['resultado'] = $arrayResultado;
            $arrayRespuesta['total'] = $intTotal;
            
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
        }
        return $arrayRespuesta;
    }
     
    
    
    /**
     * getJSONDocumentosTransporte, Obtiene los archivos subidos a un vehículo 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 22-08-2016
     * 
     * @param  array $arrayParametros
     * 
     * @return json $jsonData
     */
    public function getJSONDocumentosTransporte($arrayParametros)
    {
        $arrayEncontrados = array();
        $arrayResultado = $this->getResultadoDocumentosTransporte($arrayParametros);
        $container = $arrayParametros["container"];
        $resultado = $arrayResultado['resultado'];
        $intTotal = $arrayResultado['total'];
        $total = 0;

        if($resultado)
        {
            $total = $intTotal;
            foreach($resultado as $data)
            {
                $arrayEncontrados[] = array(
                    "id"                        => $data['idDocumento'],
                    "ubicacionLogicaDocumento"  => $data['ubicacionLogicaDocumento'],
                    "ubicacionFisicaDocumento"  => $data['ubicacionFisicaDocumento'],
                    "tipoDocumentoGeneral"      => $data['tipoDocumentoGeneral'],
                    "feCreacion"                => $data["feCreacion"] ? strval(date_format($data["feCreacion"],"d-m-Y H:i")):"",
                    "feCaducidad"               => $data["feCaducidad"] ? strval(date_format($data["feCaducidad"],"d-m-Y")):"",
                    "usrCreacion"               => $data['usrCreacion'],
                    "linkVerDocumento"          => $container->get('router')->generate('elementotransporte_descargarDocumento',
                                                                                        array('id' => $data['idDocumento'])),
                    
                );
            }
        }

        $arrayRespuesta = array('total' => $total, 'encontrados' => $arrayEncontrados);
        $jsonData = json_encode($arrayRespuesta);
        return $jsonData;
        
    }
    
    /**
     * Funcion que sirve para obtener documentos (acta entrega, encuestas, fotos) por
     * caso
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24-05-2015
     * @param int $idCaso,
     * @param int $tipoDocumento
     * @param string $modulo
     * @return int $idDocumento
     */
    public function getDocumentoPorCaso($idCaso, $tipoDocumento, $modulo)
    {
        $rsm = new ResultSetMappingBuilder($this->_em);
        $query = $this->_em->createNativeQuery(null, $rsm);
        
        $sql = "  SELECT
                    IDC.ID_DOCUMENTO AS ID_DOCUMENTO
                  FROM
                    DB_COMUNICACION.INFO_DOCUMENTO_RELACION IDR,
                    DB_COMUNICACION.INFO_DOCUMENTO IDC,
                    DB_GENERAL.ADMI_TIPO_DOCUMENTO_GENERAL ATDG
                  WHERE
                    IDR.DOCUMENTO_ID             = IDC.ID_DOCUMENTO
                  AND ATDG.ID_TIPO_DOCUMENTO     = IDC.TIPO_DOCUMENTO_GENERAL_ID
                  AND ATDG.CODIGO_TIPO_DOCUMENTO = :tipoDocumento
                  AND IDR.MODULO                 = :modulo
                  AND IDR.CASO_ID                = :idCaso";
        
        $rsm->addScalarResult('ID_DOCUMENTO',   'idDocumento',   'integer');
        
        $query->setParameter("idCaso",          $idCaso);
        $query->setParameter("modulo",          $modulo);
        $query->setParameter("tipoDocumento",   $tipoDocumento);
        
        $query->setSQL($sql);
        $arrayDocumentos = $query->getResult();
        if(count($arrayDocumentos)<1)
        {
            return null;
        }
        $idDocumento = $arrayDocumentos[0];

        return $idDocumento;
    }
    
    /**
     * generarJsonDocumentos
     *
     * Metodo encargado de generar listado de documentos
     * @param array   $parametros
     * @param integer $start
     * @param integer $limit
     *
     * @return arreglo con listado de documentos
     *
     * @author Jesus Bozada P. <jbozada@telconet.ec>
     * @version 1.1 05-03-2014
     * 
     * @author Modificado: Duval Medina C. <dmedina@telconet.ec>
     * @version 1.2 2016-06-15 Incluir validación por exitencia de parametro['filtarNoticia']
     *                         Cambiar nombre de método en comentario
     */
    public function generarJsonDocumentos($parametros, $start, $limit)
    {
        $arr_encontrados    = array();
        $resultado          = $this->getDocumentos($parametros, $start, $limit);
        $rsTotal            = $resultado['total'];
        $rs                 = $resultado['registros'];
        $strFechaDesde      = "";
        $strFechaHasta      = "";
        $hayNoticias        = false;
        if(isset($parametros["filtrarNoticia"]))
        {
            if($parametros["filtrarNoticia"] == "SI")
            {
                $hayNoticias = true;
            }
        }
        if(isset($rs))
        {
            foreach($rs as $entidad)
            {
                $claseDocumento = $this->_em->getRepository('schemaBundle:AdmiClaseDocumento')->find($entidad->getClaseDocumentoId()->getId());
                //se agregan validacion de filtro noticia para enviar arreglo con campos adecuados
                if($hayNoticias)
                {
                    $arr_encontrados[] = array('id_documento'       => $entidad->getId(),
                                               'nombre'             => $entidad->getNombreDocumento(),
                                               'usuario'            => $entidad->getUsrCreacion(),
                                               'estado'             => $entidad->getEstado(),
                                               'tipo'               => $claseDocumento ? $claseDocumento->getNombreClaseDocumento() : 'N/A',
                                               'fechaPublicacion'   => "Publicado el día " .
                                                                       $entidad->getFechaPublicacionDesde()->format('d-m-Y') . " a las " .
                                                                       $entidad->getFechaPublicacionDesde()->format('H:i:s A'),
                                               'action1'            => 'button-grid-show',
                                               'action2'            => (trim($entidad->getEstado()) == 'Eliminado' ? 'button-grid-invisible' : 
                                                                                                          'button-grid-edit'),
                                               'action3'            => (trim($entidad->getEstado()) == 'Eliminado' ? 'button-grid-invisible' : 
                                                                                                          'button-grid-delete'));
                }
                else
                {
                    $strFechaDesde = "";
                    if ($entidad->getFechaPublicacionDesde())
                    {
                        $strFechaDesde = $entidad->getFechaPublicacionDesde()->format('d-m-Y H:i:s A');
                    }
                    
                    $strFechaHasta = "";
                    if ($entidad->getFechaPublicacionHasta())
                    {
                        $strFechaHasta = $entidad->getFechaPublicacionHasta()->format('d-m-Y H:i:s A');
                    }
                    
                    $arr_encontrados[] = array('id_documento'           => $entidad->getId(),
                                               'nombre'                 => $entidad->getNombreDocumento(),
                                               'usuario'                => $entidad->getUsrCreacion(),
                                               'estado'                 => $entidad->getEstado(),
                                               'tipo'                   => $claseDocumento ? $claseDocumento->getNombreClaseDocumento() : 'N/A',
                                               'action1'                => 'button-grid-show',
                                               'action2'                => (trim($entidad->getEstado()) == 'Eliminado' ? 'button-grid-invisible' : 
                                                                                                                         'button-grid-edit'),
                                               'action3'                => (trim($entidad->getEstado()) == 'Eliminado' ? 'button-grid-invisible' : 
                                                                                                                         'button-grid-delete'),
                                               'fechaPublicacionDesde'  => $strFechaDesde,
                                               'fechaPublicacionHasta'  => $strFechaHasta );
                }
            }
            $data      = json_encode($arr_encontrados);
            $resultado = '{"total":"' . $rsTotal . '","encontrados":' . $data . '}';

            return $resultado;
        }
        else
        {
            $resultado = '{"total":"0","acciones":[]}';

            return $resultado;
        }
    }
    
     /**
     * getDocumentos
     *
     * Metodo encargado de generar listado de documentos
     *
     * @param array  $parametros
     * @param integer $start
     * @param integer $limit
     *
     * @return arreglo con listado de documentos
     *
     * @version 1.0
     * 
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 13-05-2016
     *
     * @author Modificado: Duval Medina C. <dmedina@telconet.ec>
     * @version 1.2 2016-06-15 Incluir validación por exitencia de parametro['filtarNoticia']
     *                         Cambiar nombre de funcion en comentario
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.3 26-07-2016
     * Se maneja el parámetro LOGIN para filtrar por usuario creación.
     * El estado 'Activo' incluye a estados 'Modificados'
     */
    public function getDocumentos($parametros,$start,$limit)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('documento')
           ->from('schemaBundle:InfoDocumento','documento')
           ->from('schemaBundle:AdmiClaseDocumento','clase')
           ->andWhere('documento.claseDocumentoId = clase');
                  
        //Me determina si el tipo es un id de la AdmiClaseDocumento o el nombre y me permitira consultar segun la validacion
        $patron = "/^[[:digit:]]+$/";

        /****************************** Tipo de Documento ******************************/
        if ($parametros["tipo"] != "") 
        {
            if (!preg_match($patron, $parametros["tipo"]))
            {

                $parametroProceso = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                                              ->get("PLANTILLAS NOTIFICACION EXTERNA",
                                                    "SOPORTE",
                                                    "NOTIFICACIONES",
                                                    "CODIGO DE PLANTILLA","", "", "", "","","");

                foreach($parametroProceso as $arrayAdmiParamDet)
                {
                    $arrayAdmiParametroDetResult[] = $arrayAdmiParamDet['valor1'];
                }

                $qb->andWhere('documento.claseDocumentoId IN (?1)');
                $qb->setParameter(1,$arrayAdmiParametroDetResult);
            }
            else 
            {
                $qb->andWhere('documento.claseDocumentoId = ?2');
                $qb->setParameter(2, $parametros["tipo"]);
            }
        }
        /********************************************************************************/
        //se agrega UPPER para que la consulta no sea sensitiva a mayusculas o minusculas                
        if($parametros["nombre"]!="")
        {
            $qb ->andWhere( 'UPPER(documento.nombreDocumento) like ?3');
            $qb->setParameter(3, '%'.strtoupper(trim($parametros["nombre"])).'%');
        }
        if($parametros["estado"]!="Todos")
        {
            if($parametros["estado"]=="Activo")
            {
                $qb ->andWhere("documento.estado != 'Eliminado'");
            }
            else
            {
                $qb ->andWhere('documento.estado = ?4');
                $qb->setParameter(4, $parametros["estado"]);
            }
        }              
        if($parametros["empresa"]!="")
        {
            $qb ->andWhere( 'documento.empresaCod = ?5');
            $qb->setParameter(5, $parametros["empresa"]);
        }
        //se agregan filtros para opcion noticias
        if(isset($parametros["filtrarNoticia"]))
        {
           if($parametros["filtrarNoticia"] == "SI")
            {
                $fechaActual = new \DateTime('now');
                $qb ->andWhere( 'documento.fechaPublicacionDesde <= ?6');
                $qb->setParameter(6, $fechaActual);
                $qb ->andWhere( 'documento.fechaPublicacionHasta >= ?7');
                $qb->setParameter(7, $fechaActual);
            }
        }
        
        // Búsqueda por usuario creación
        if($parametros["login"] != "")
        {
            $qb->andWhere('documento.usrCreacion = ?8');
            $qb->setParameter(8, strtolower(trim($parametros["login"])));
        }

        $qb->orderBy('documento.id','desc');
        $total=count($qb->getQuery()->getResult());
        $resultado['total']=$total;
        if($start!='')
        {
            $qb->setFirstResult($start);   
        }
        if($limit!='')
        {
            $qb->setMaxResults($limit);
        }
        $resultado['registros'] = $qb->getQuery()->getResult();
               
        return $resultado;
    }
    
    /**
     * getJSONPlantillas
     *
     * Metodo encargado de obtener el JSON de las plantillas
     * @param array  $arrayParametros
     *
     * @return json $jsonData
     *
     * @author Lizbeth Cruz Tomalá <mlcruz@telconet.ec>
     * @version 1.0 11-08-2016
     */
    public function getJSONPlantillas($arrayParametros)
    {
        $arrayEncontradosPlantillas = array();
        $arrayResultadoPlantillas   = $this->getResultadoPlantillas($arrayParametros);
        $arrayRegistrosPlantillas   = $arrayResultadoPlantillas['registros'];
        $intTotalPlantillas         = $arrayResultadoPlantillas['total'];

        if( $arrayRegistrosPlantillas )
        {
            foreach( $arrayRegistrosPlantillas as $data )
            {
                $arrayPlantilla  = array();
                $arrayPlantilla["id_documento"]     = $data["id"];
                $arrayPlantilla["nombre"]           = $data["nombreDocumento"];
                $arrayEncontradosPlantillas[]       = $arrayPlantilla;
            }
        }
        $arrayRespuesta = array('total' => $intTotalPlantillas, 'encontrados' => $arrayEncontradosPlantillas);
        $jsonData       = json_encode($arrayRespuesta);

        return $jsonData;
    }
    
    /**
     * getResultadoPlantillas
     *
     * Metodo encargado de generar listado de plantillas dependiendo de los parámetros enviados
     *
     * @param array $arrayParametros
     *
     * @return array $arrayResultado
     *
     * @version 1.0 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * 
     */
    public function getResultadoPlantillas($arrayParametros)
    {
        $arrayResultado['registros'] = '';
        $arrayResultado['total']     = 0;
        

        try
        {
            $query              = $this->_em->createQuery();
            $queryCount         = $this->_em->createQuery();
            
            $strSelectCount     = "SELECT COUNT(doc.id) ";
            $strSelect          = "SELECT doc.id, doc.nombreDocumento ";
            $strFromAndWhere    = " FROM schemaBundle:InfoDocumento doc 
                                    INNER JOIN schemaBundle:AdmiClaseDocumento claseDoc WITH doc.claseDocumentoId = claseDoc.id
                                    WHERE doc.estado <> :estado 
                                    AND doc.empresaCod = :empresaCod ";
            
            
            $query->setParameter("estado", 'Eliminado');
            $queryCount->setParameter("estado", 'Eliminado');

            $query->setParameter("empresaCod", $arrayParametros["empresa"]);
            $queryCount->setParameter("empresaCod", $arrayParametros["empresa"]);
            
            $strWhere="";
            
            if(isset($arrayParametros["nombre"]))
            {
                if($arrayParametros["nombre"])
                {
                    $strWhere.=" AND UPPER(doc.nombreDocumento) like :nombrePlantilla ";
                    $query->setParameter("nombrePlantilla", '%'.strtoupper(trim($arrayParametros["nombre"])).'%');
                    $queryCount->setParameter("nombrePlantilla", '%'.strtoupper(trim($arrayParametros["nombre"])).'%');
                }
            }
            
            
            if(isset($arrayParametros["tipo"]))
            {
                if($arrayParametros["tipo"])
                {
                    $strWhere .= 'AND claseDoc.nombreClaseDocumento LIKE :tipoNotificacion ';
                
                    $query->setParameter("tipoNotificacion", $arrayParametros['tipo']) ;
                    $queryCount->setParameter("tipoNotificacion", $arrayParametros['tipo']);
                }
                
            }

            $strOrderBy= " ORDER BY doc.nombreDocumento ASC ";
            

            $strSql      = $strSelect.$strFromAndWhere.$strWhere.$strOrderBy;
            $strSqlCount = $strSelectCount.$strFromAndWhere.$strWhere;

            $query->setDQL($strSql);
            $queryCount->setDQL($strSqlCount);

            $arrayResultado['registros'] = $query->getResult();
            $arrayResultado['total']     = $queryCount->getSingleScalarResult();
            
        }
        catch(\Exception $ex)
        {
            error_log($ex->getMessage());
        }
        
        return $arrayResultado;
    }
    
    public function getClaseDocumentoByInfoDocumentoId($id)
    {
        $db = 'select a.claseDocumentoId as id from schemaBundle:InfoDocumento a where a.id ='.$id;
        $query = $this->_em->createQuery($db); 		
		
        $datos = $query->getResult();       
        return $datos['id'];
    }

    public function generarJsonEnvioNotificacionPorCriterios($em, $em_financiero, $em_infraestructura, $parametros,$start,$limit)
    {
        
        $arr_encontrados = array();
      
        if($parametros['tipoFiltrosModulo']=='Tecnico')$resultados= $this->getEnvioNotificacionPorCriteriosTecnicos($parametros,$start,$limit);
        else if($parametros['tipoFiltrosModulo']=='Financiero')$resultados= $this->getEnvioNotificacionPorCriteriosFinancieros($parametros,$start,$limit);
        else $resultados= $this->getEnvioNotificacionPorCriteriosComerciales($parametros,$start,$limit);
   
        if($resultados)
	{
			$num = $resultados['total'];
			$registros = $resultados['registros'];						
			
			if ($registros && count($registros)>0) 
			{  
				foreach ($registros as $datos)
				{
					$Servicio = $em->getRepository('schemaBundle:InfoServicio')->findOneById($datos["idServicio"]); 
					$nombreProducto =  ($Servicio->getProductoId() ? $Servicio->getProductoId()->getDescripcionProducto() : "");  
					$nombrePlan =  ($Servicio->getPlanId() ? $Servicio->getPlanId()->getNombrePlan() : "");  
					$nombreProductoPlan = $nombreProducto . $nombrePlan;									
					
					$cliente =  ($datos["razonSocial"] ? $datos["razonSocial"] : ($datos["nombres"] || $datos["apellidos"] ? $datos["nombres"] . " " . $datos["apellidos"] : "")); 
				
				
					$arr_encontrados[]=array('id_punto'=> $datos["idPunto"],
								'id_servicio'=> $datos["idServicio"],
								'id_persona'=> $datos["idPersona"],
								'id_persona_empresa_rol'=> $datos["idPersonaEmpresaRol"],
								'cliente'=> $cliente ? $cliente : "",								
								'direccion_cliente'=> $datos["direccionCliente"] ? $datos["direccionCliente"] : "",
								'nombre_oficina'=> $datos["nombreOficina"] ? $datos["nombreOficina"] : "",
								'login2'=> $datos["login"] ? $datos["login"] : "",
								'ciudad_punto'=> $datos["nombreCanton"] ? $datos["nombreCanton"] : "",
								'direccion_punto'=> $datos["direccionPunto"] ? $datos["direccionPunto"] : "",
								'estado_cliente'=> $datos["estadoCliente"] ? $datos["estadoCliente"] : "",							    
								'estado_servicio'=> $datos["estadoServicio"] ? $datos["estadoServicio"] : "",
								'servicio'=> $nombreProductoPlan ? $nombreProductoPlan : "");							    
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
        else
        {
            $resultado= '{"total":"0","encontrados":[]}';
            return $resultado;
        }
    }

    /**
     * Costo: 1600
     * getEnvioNotificacionPorCriteriosTecnicos
     *
     * Método encargado de obtener el JSON con los servicios a notificar
     *
     * @param array    $parametros
     * @param integer  $start
     * @param integer  $limit
     *
     * @return json $resultado
     *
     * @author Desarrollo Inicial
     * @version 1.0
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 27-02-2019 - Se agregan filtros adicionales como el: nombre del PE,tipo de servicio,protocolo y la ciudad del punto
     */
    public function getEnvioNotificacionPorCriteriosTecnicos($parametros,$start,$limit)
    {
        $boolBusqueda  = false; 
        $strWhere      = "";  
        $objQueryCount = $this->_em->createQuery();
        $objQuery      = $this->_em->createQuery();
                                        
        if($parametros["empresaCod"]!="" && $parametros["empresaCod"]){
	    $boolBusqueda = true;
			$strWhere .= "AND eg.id = '".$parametros["empresaCod"]."' ";
        }
        if($parametros["oficina"]!="" && $parametros["oficina"])
        {
            $boolBusqueda = true;
			$strWhere .= "AND og.id = '".$parametros["oficina"]."' ";
        }           
        if($parametros["login2"]!="" && $parametros["login2"])
        {
            $boolBusqueda = true;
			$strWhere .= "AND LOWER(pu.login) like '%".strtolower($parametros["login2"])."%' ";
        }            
        if($parametros["estado_punto"]!="" && $parametros["estado_punto"]!="Todos"){
            $boolBusqueda = true;
            $strWhere .= "AND (LOWER(pu.estado) = LOWER('".$parametros["estado_punto"]."')) ";
        } 
        if($parametros["estado_servicio"]!="" && $parametros["estado_servicio"]!="Todos"){
            $boolBusqueda = true;
            $strWhere .= "AND (LOWER(s.estado) = LOWER('".$parametros["estado_servicio"]."')) ";
        }
        if($parametros["ciudad_punto"]!="" && $parametros["ciudad_punto"]!=""){
            $boolBusqueda = true;
            $strWhere .= "AND (LOWER(ca.nombreCanton) like LOWER('%".$parametros["ciudad_punto"]."%')) ";
        }	
        if($parametros["direccion_punto"]!="" && $parametros["direccion_punto"]!=""){
            $boolBusqueda = true;
            $strWhere .= "AND (LOWER(pu.direccion) like LOWER('%".$parametros["direccion_punto"]."%')) ";
        }        
        if($parametros['elementos'] && $parametros['elementos']!=""){
	    $boolBusqueda = true;
	    $strWhere .= "AND ist.elementoId in ".$parametros['elementos']." ";
        }
        if($parametros['nombrePe'] && $parametros['nombrePe']!="")
        {
            $strWhere .= " AND s.id IN ( SELECT s1.id FROM schemaBundle:InfoServicio s1 WHERE s1.id IN (SELECT i1.servicioId "
                            . " FROM schemaBundle:InfoIp i1 WHERE i1.subredId IN (SELECT is1.id FROM schemaBundle:InfoSubred is1 "
                            . " WHERE is1.elementoId IN ( SELECT io.id FROM schemaBundle:InfoElemento io "
                            . " WHERE io.nombreElemento = :nombrePe)))) ";

            $objQuery->setParameter("nombrePe", $parametros['nombrePe']);
            $objQueryCount->setParameter("nombrePe", $parametros['nombrePe']);
        }
        if($parametros['servicio'] && $parametros['servicio']!="")
        {
            $strWhere .= " AND s.productoId = :productoId ";

            $objQuery->setParameter("productoId", $parametros['servicio']);
            $objQueryCount->setParameter("productoId", $parametros['servicio']);
        }
        if($parametros['ciudad'] && $parametros['ciudad']!="")
        {
            $strWhere .= " AND ca.id = :ciudad";

            $objQuery->setParameter("ciudad", $parametros['ciudad']);
            $objQueryCount->setParameter("ciudad", $parametros['ciudad']);
        }
        if($parametros['protocolo'] && $parametros['protocolo']!="")
        {
            $strWhere .= " AND s.id IN ( SELECT isp.servicioId FROM schemaBundle:InfoServicioProdCaract isp WHERE isp.productoCaracterisiticaId IN "
                        . " ( SELECT apc.id FROM schemaBundle:AdmiProductoCaracteristica apc WHERE apc.caracteristicaId = "
                        . " ( SELECT ac.id FROM schemaBundle:AdmiCaracteristica ac WHERE ac.descripcionCaracteristica = 'PROTOCOLO_ENRUTAMIENTO' )) "
                        . " AND UPPER(isp.valor) = UPPER(:protocolo)) ";

            $objQuery->setParameter("protocolo", $parametros['protocolo']);
            $objQueryCount->setParameter("protocolo", $parametros['protocolo']);
        }

		$selectedCont = " count(s) as cont ";
		$selectedData = "
		s.id as idServicio, pu.id as idPunto, p.id as idPersona, per.id as idPersonaEmpresaRol, 
		p.razonSocial, p.nombres, p.apellidos, p.direccion as direccionCliente, p.estado as estadoCliente,ca.nombreCanton,		 
		pu.login, pu.direccion as direccionPunto,
		og.nombreOficina, s.estado as estadoServicio 
		";
		$from = "
                FROM 
                schemaBundle:InfoServicio s, schemaBundle:InfoPunto pu, 
                schemaBundle:InfoPersona p, schemaBundle:InfoPersonaEmpresaRol per, 
                schemaBundle:InfoEmpresaRol er, schemaBundle:InfoOficinaGrupo og, 
		schemaBundle:InfoEmpresaGrupo eg, 
		schemaBundle:AdmiSector se, schemaBundle:AdmiParroquia pa, 
		schemaBundle:AdmiCanton ca, 					
                schemaBundle:AdmiRol r, schemaBundle:AdmiTipoRol tr,
                schemaBundle:InfoServicioTecnico ist";
		
		$wher = "
                WHERE 
                pu.id = s.puntoId  
                AND s = ist.servicioId
                AND pu.personaEmpresaRolId = per.id 
                AND per.personaId = p.id  
                AND per.empresaRolId = er.id  
                AND per.oficinaId = og.id  
                AND og.empresaId = eg.id  
                AND er.empresaCod = eg.id  
                AND er.rolId = r.id  
                AND r.tipoRolId = tr.id  
                AND tr.descripcionTipoRol = 'Cliente'                 
		AND pu.sectorId = se.id 
		AND se.parroquiaId = pa.id 
		AND pa.cantonId = ca.id 
		$strWhere ";
				
		$strSql      = "SELECT $selectedData $from $wher ";
		$strSqlCount = "SELECT $selectedCont $from $wher ";

        $objQueryCount->setDQL($strSqlCount);
        $objQuery->setDQL($strSql);

        $arrayResultTotal = $objQueryCount->getOneOrNullResult();
        $arrayTotal = ($arrayResultTotal ? ($arrayResultTotal["cont"] ? $arrayResultTotal["cont"] : 0) : 0);
	
        $arrayDatos                  = $objQuery->setFirstResult($start)->setMaxResults($limit)->getResult();
        $arrayResultado['registros'] = $arrayDatos;
        $arrayResultado['total']     = $arrayTotal;

        return $arrayResultado;
	
    }
    public function getEnvioNotificacionPorCriteriosComerciales($parametros,$start,$limit){
     
	$porServicio = $parametros['servicios_por'];
	
	$boolBusqueda = false; 
        $where = "";                
                  
        if($parametros["empresaCod"]!="" && $parametros["empresaCod"]){
	    $boolBusqueda = true;
			$where .= "AND eg.id = '".$parametros["empresaCod"]."' ";
        }
        if( $porServicio && $porServicio == 'Catalogo' && $parametros["producto_plan"] && $parametros["producto_plan"]!='' )
        {
            $boolBusqueda = true;
			$where .= "AND s.productoId = ".$parametros["producto_plan"]." ";			
        }           
        if($porServicio && $porServicio == 'Portafolio' && $parametros["producto_plan"] && $parametros["producto_plan"]!='')
        {
            $boolBusqueda = true;
			$where .= "AND s.planId = ".$parametros["producto_plan"]." ";			
        }    
        
        
         if($parametros['estado_servicio_comercial']!='' && $parametros['estado_servicio_comercial'])
        {
            $boolBusqueda = true;			
			 $where .= "AND (LOWER(s.estado) = LOWER('".$parametros["estado_servicio_comercial"]."')) ";
        } 
         if($parametros['forma_pago']!='' && $parametros['forma_pago'])
        {
            $boolBusqueda = true;
			$where .= "AND ic.formaPagoId = ".$parametros["forma_pago"]." ";
        } 
        
		$selectedCont = " count(s) as cont ";
		$selectedData = "
							s.id as idServicio, pu.id as idPunto, p.id as idPersona, per.id as idPersonaEmpresaRol, 
							p.razonSocial, p.nombres, p.apellidos, p.direccion as direccionCliente, p.estado as estadoCliente,ca.nombreCanton," .						
							"pu.login, pu.direccion as direccionPunto,
							og.nombreOficina, s.estado as estadoServicio 
						";
		$from = "
                FROM 
                schemaBundle:InfoServicio s, schemaBundle:InfoPunto pu,		   
                schemaBundle:InfoPersona p, schemaBundle:InfoPersonaEmpresaRol per, 
                schemaBundle:InfoEmpresaRol er, schemaBundle:InfoOficinaGrupo og, 
		schemaBundle:InfoEmpresaGrupo eg, 
		schemaBundle:AdmiSector se, schemaBundle:AdmiParroquia pa, 
		schemaBundle:AdmiCanton ca, 					
                schemaBundle:AdmiRol r, schemaBundle:AdmiTipoRol tr,                
		schemaBundle:InfoContrato ic";
		
		$wher = "
                WHERE                 
                pu.id = s.puntoId   	
                AND ic.personaEmpresaRolId = pu.personaEmpresaRolId              
                AND pu.personaEmpresaRolId = per.id
                AND per.personaId = p.id  
                AND per.empresaRolId = er.id  
                AND per.oficinaId = og.id  
                AND og.empresaId = eg.id  
                AND er.empresaCod = eg.id  
                AND er.rolId = r.id  
                AND r.tipoRolId = tr.id  
                AND tr.descripcionTipoRol = 'Cliente' 
		AND pu.sectorId = se.id 
		AND se.parroquiaId = pa.id 
		AND pa.cantonId = ca.id 			     		
		$where ";
				
		$sql = "SELECT $selectedData $from $wher ";
		$sqlC = "SELECT $selectedCont $from $wher ";
		
        $queryC = $this->_em->createQuery($sqlC);
       
        $query = $this->_em->createQuery($sql);
	
	$resultTotal = $queryC->getOneOrNullResult();
	$total = ($resultTotal ? ($resultTotal["cont"] ? $resultTotal["cont"] : 0) : 0);
	
        $datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
                        
        $resultado['registros']=$datos;
        $resultado['total']=$total;

        return $resultado;
	          
     }
     public function getEnvioNotificacionPorCriteriosFinancieros($parametros,$start,$limit){
     	
	$boolBusqueda = false; 
        $where = "";    
        
        $patron = "/^[[:digit:]]+$/";
          
        if($parametros["empresaCod"]!="" && $parametros["empresaCod"])
        {
	    $boolBusqueda = true;
			$where .= "AND eg.id = '".$parametros["empresaCod"]."' ";
        }
        if($parametros["oficina_cliente"] && $parametros["oficina_cliente"]!='' && $parametros["oficina_cliente"]!='Todos')
        {
            $boolBusqueda = true;
			$where .= "AND og.id = ".$parametros["oficina_cliente"]." ";			
        }           
  
        if($parametros['direccion_punto_financiero']!='' && $parametros['direccion_punto_financiero'])
        {
            $boolBusqueda = true;			
			 $where .= "AND LOWER(pu.direccion) like LOWER('%".$parametros["direccion_punto_financiero"]."%')  ";
        } 
        if($parametros['ciudad_punto_financiero']!='' && $parametros['ciudad_punto_financiero'])
        {
            $boolBusqueda = true;
			$where .= "AND ca.nombreCanton like '%".$parametros["ciudad_punto_financiero"]."%' ";
        }
        if($parametros['tipo_negocio_punto']!='' && $parametros['tipo_negocio_punto'])
        {
            $boolBusqueda = true;
			$where .= "AND pu.tipoNegocioId = ".$parametros["tipo_negocio_punto"]." ";
        }
        if($parametros['estado_punto_financiero']!='' && $parametros['estado_punto_financiero'] && $parametros['estado_punto_financiero']!='-' )
        {
            $boolBusqueda = true;					
			$where .= "AND pu.estado = '".$parametros["estado_punto_financiero"]."' ";
        }
        if($parametros['saldo']!='' && $parametros['saldo'] && preg_match($patron, $parametros["saldo"]))
        {
            $boolBusqueda = true;
			$where .= "AND vecr.saldo >= ".$parametros["saldo"]." ";
        }else if($parametros['saldo']=='') $where .= "AND vecr.saldo >= 0 ";
      
        if($parametros['estado_servicio_financiero']!='' && $parametros['estado_servicio_financiero'] && $parametros['estado_servicio_financiero']!='Todos')
        {
            $boolBusqueda = true;			
			$where .= "AND s.estado = '".$parametros["estado_servicio_financiero"]."' ";
        }
        
		$selectedCont = " count(s) as cont ";
		$selectedData = "
							s.id as idServicio, pu.id as idPunto, p.id as idPersona, per.id as idPersonaEmpresaRol, 
							p.razonSocial, p.nombres, p.apellidos, p.direccion as direccionCliente, p.estado as estadoCliente,ca.nombreCanton," .						
							"pu.login, pu.direccion as direccionPunto,
							og.nombreOficina, s.estado as estadoServicio 
						";
		$from = "
                FROM 
                schemaBundle:InfoServicio s, schemaBundle:InfoPunto pu,		   
                schemaBundle:InfoPersona p, schemaBundle:InfoPersonaEmpresaRol per, 
                schemaBundle:InfoEmpresaRol er, schemaBundle:InfoOficinaGrupo og, 
		schemaBundle:InfoEmpresaGrupo eg, 
		schemaBundle:AdmiSector se, schemaBundle:AdmiParroquia pa, 
		schemaBundle:AdmiCanton ca, 					
                schemaBundle:AdmiRol r, schemaBundle:AdmiTipoRol tr,		
		schemaBundle:VistaEstadoCuentaResumido vecr";
		
		$wher = "
                WHERE                 
                pu.id = s.puntoId   	  
                AND pu.id = vecr.id               
                AND pu.personaEmpresaRolId = per.id
                AND per.personaId = p.id  
                AND per.empresaRolId = er.id  
                AND per.oficinaId = og.id  
                AND og.empresaId = eg.id  
                AND er.empresaCod = eg.id  
                AND er.rolId = r.id  
                AND r.tipoRolId = tr.id  
                AND tr.descripcionTipoRol = 'Cliente' 
		AND pu.sectorId = se.id 
		AND se.parroquiaId = pa.id 
		AND pa.cantonId = ca.id 		    		  
		$where ";
				
		$sql = "SELECT $selectedData $from $wher ";
		$sqlC = "SELECT $selectedCont $from $wher ";
		
        $queryC = $this->_em->createQuery($sqlC);
       
        $query = $this->_em->createQuery($sql);
				
	$resultTotal = $queryC->getOneOrNullResult();
	$total = ($resultTotal ? ($resultTotal["cont"] ? $resultTotal["cont"] : 0) : 0);
	
        $datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
                        
        $resultado['registros']=$datos;
        $resultado['total']=$total;                

        return $resultado;
      
      
     }	         
      
    /**
     * generarJsonDocumentoRelacion
     *
     * Método que obtiene los documentos generados
     *                         
     * @param array   $parametros        
     * @param integer $start  
     * @param integer $limit   
     * @param \Symfony\Component\Routing\Router $router           
     *
     * @return JSON con valores a mostrar 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 24-09-2015 - Se agrega para que reciba el parámetro de '$router' enviado desde la clase 
     *                           'SoporteService', el cual será usado para generar las rutas de 'Descargar'
     *                           y 'Ver Documento' al mostrar los documentos técnicos que se presentan en el
     *                           Módulo de Comunicaciones
     *    
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 23-07-2014
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.2 21-02-2020 - Se agrega en el **$arrayItem** la *feCreacion*.
     */
    public function generarJsonDocumentoRelacion($parametros , $start='' , $limit = '', $router = null)
    {	    
        $query      = $this->getGestionDocumentosRelacion($parametros, $start, $limit, 'data');	      
        $queryTotal = $this->getGestionDocumentosRelacion($parametros, $start, $limit, 'count');

        $total = $queryTotal->getSingleResult()['total'];	      	            	       	     
      	       
        if ($limit > 0)
        {
            $query->setSQL('SELECT a.*, rownum AS doctrine_rownum FROM (' . $query->getSQL() . ') a WHERE rownum <= :doctrine_limit');
            $query->setParameter('doctrine_limit', $limit + $start);
		  
            if ($start > 0)
            {
                $query->setSQL('SELECT * FROM (' . $query->getSQL() . ') WHERE doctrine_rownum >= :doctrine_start');
                $query->setParameter('doctrine_start', $start + 1);
            }
        }	      	     
	      	      
        $resultado = $query->getArrayResult();		      	      
	      
        if($resultado) 
        {           
            $boolFlagDescarga = true;
            $boolFlagVisualiza = true;
            if($parametros["DescargarDocumentosPersonales"] == 0)
            {
                $boolFlagDescarga = false;
            }

            if ($parametros["VerDocumentoPersonal"] == 0)
            {
                $boolFlagVisualiza = false;
            }

            foreach($resultado as $data)
            {		    			
                $arrayItem = array(
                                    'id'                 => $data['id_documento'],
                                    'nombre'             => $data['nombre_documento'],
                                    'estado'             => $data['estado'],
                                    'feCreacion'         => $data['feCreacion'],
                                    'modulo'             => $data['modulo'],
                                    'extension'          => $data['extension_tipo_documento'],
                                    'tipoDocumento'      => $data['descripcion_tipo_documento'],
                                    'punto'              => $data['login'],
                                    'tipoElemento'       => $data['nombre_tipo_elemento'],
                                    'modelElemento'      => $data['nombre_modelo_elemento'],
                                    'elemento'           => $data['nombre_elemento'],
                                    'ubicacionLogica'    => $data['ubicacion_logica_documento'],
                                    'ubicacionFisica'    => $data['ubicacion_fisica_documento'],
                                    'contrato'           => $data['numero_contrato'],
                                    'documentoFinan'     => $data['numero_factura_sri'],
                                    'caso'               => $data['numero_caso'],
                                    'actividad'          => $data['actividad_id']!=0?$data['actividad_id']:'N/A',
                                    'nombreEncuesta'     => $data['nombre_encuesta'],
                                    'action1'            => 'button-grid-show',
                                    'action2'            => ( trim($data['estado'])=='Eliminado' ? 'button-grid-invisible':'button-grid-edit' ),
                                    'action3'            => ( trim($data['estado'])=='Eliminado' ? 'button-grid-invisible':'button-grid-delete' ),
                                    'action4'            => ( trim($data['estado'])=='Eliminado' ? 'button-grid-invisible'
                                                              :'button-grid-verParametrosIniciales')
                                  );

                if(($boolFlagDescarga == 1 || $boolFlagVisualiza == 1) && ($parametros["DescargarDocumentosPersonales"] == 0) )
                {
                    $arrayItem['action4'] = 'button-grid-invisible';
                }

                if( $router )
                {
                    $arrayItem['strUrlShow'] = $router->generate( 'gestion_documentos_show', array(
                                                                                                     'id'     => $data['id_documento'], 
                                                                                                     'modulo' => $data['modulo']
                                                                                                  )
                                                                );

                    $arrayItem['strUrlDescargar'] = $router->generate( 'gestion_documentos_descargar', array( 'id' => $data['id_documento'] ) );
                }

                $arr_encontrados[] = $arrayItem;

            }//foreach($resultado as $data)

            $data=json_encode($arr_encontrados);
            $resultado= '{"total":"'.$total.'","encontrados":'.$data.'}';

            return $resultado;
        }
        else
        {
            $resultado = '{"total":"0","encontrados":[]}';
            
            return $resultado;
        }//($resultado) 
    }
      
    /**
     * getGestionDocumentosRelacion
     * 
     * Método que devuelve el string con la consulta generada por los parametros enviados
     *
     * @param array   $arrayParametros
     * @param integer $intStart
     * @param integer $intLimit
     * @param string  $strTipo
     *
     * @return string con query generado
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 24-09-2015 - Se agregan las validaciones de 'fechaDesde' y 'fechaHasta' para que el método 
     *                           retorne información dentro de un rango de fechas
     *
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 23-07-2014
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.2 21-02-2020 - Se mejora el rendimiento del query filtrando por defecto los documentos
     *                           registrados desde hace 6 meses en adelante.
     *                         - Se aplica los estandares de calidad.
     */
    public function getGestionDocumentosRelacion($arrayParametros, $intStart, $intLimit , $strTipo)
    {
        $objRsm     = new ResultSetMappingBuilder($this->_em);
        $objQuery   = $this->_em->createNativeQuery(null, $objRsm);
        $strSelect  = "";
        $strOrderBy = "";

        if ($strTipo == 'count')
        {
            $strSelect = "SELECT COUNT(*) AS TOTAL ";
            $objRsm->addScalarResult('TOTAL', 'total', 'integer');
        }
        else
        {
            $strSelect = "SELECT ".
                           "DOCUMENTO.ID_DOCUMENTO, ".
                           "DOCUMENTO.NOMBRE_DOCUMENTO, ".
                           "DOCUMENTO.UBICACION_FISICA_DOCUMENTO, ".
                           "DOCUMENTO.UBICACION_LOGICA_DOCUMENTO, ".
                           "DOCUMENTO.MENSAJE, ".
                           "DOCUMENTO.ESTADO, ".
                           "TO_CHAR(DOCUMENTO.FE_CREACION,'DD-MM-RRRR') AS FE_CREACION, ".
                           "TIPO_DOCUMENTO.EXTENSION_TIPO_DOCUMENTO, ".
                           "TIPO_DOCUMENTO_GENERAL.DESCRIPCION_TIPO_DOCUMENTO, ".
                           "RELACION.MODULO, ".
                           "RELACION.PERSONA_EMPRESA_ROL_ID, ".
                           "RELACION.PUNTO_ID, ".
                           "RELACION.SERVICIO_ID, ".
                           "NVL(PUNTO.LOGIN, 'N/A')                             AS LOGIN, ".
                           "NVL(CASO.NUMERO_CASO, 'N/A')                        AS NUMERO_CASO, ".
                           "NVL(COMUNICACION.ID_COMUNICACION, 0)                AS ACTIVIDAD_ID, ".
                           "NVL(CONTRATO.NUMERO_CONTRATO, 'N/A')                AS NUMERO_CONTRATO, ".
                           "NVL(ENCUESTA.NOMBRE_ENCUESTA, 'N/A')                AS NOMBRE_ENCUESTA, ".
                           "NVL(DOCUMENTO_FINANCIERO.NUMERO_FACTURA_SRI, 'N/A') AS NUMERO_FACTURA_SRI, ".
                           "NVL(TIPO_ELEMENTO.NOMBRE_TIPO_ELEMENTO, 'N/A')      AS NOMBRE_TIPO_ELEMENTO, ".
                           "NVL(MODELO_ELEMENTO.NOMBRE_MODELO_ELEMENTO, 'N/A')  AS NOMBRE_MODELO_ELEMENTO, ".
                           "NVL(ELEMENTO.NOMBRE_ELEMENTO, 'N/A')                AS NOMBRE_ELEMENTO ";

            $strOrderBy = "ORDER BY DOCUMENTO.FE_CREACION DESC";

            $objRsm->addScalarResult(strtoupper('id_documento'),'id_documento','integer');
            $objRsm->addScalarResult(strtoupper('modulo'),'modulo','string');
            $objRsm->addScalarResult(strtoupper('nombre_documento'),'nombre_documento','string');
            $objRsm->addScalarResult(strtoupper('ubicacion_fisica_documento'),'ubicacion_fisica_documento','string');
            $objRsm->addScalarResult(strtoupper('ubicacion_logica_documento'),'ubicacion_logica_documento','string');
            $objRsm->addScalarResult(strtoupper('mensaje'),'mensaje','string');
            $objRsm->addScalarResult(strtoupper('estado'),'estado','string');
            $objRsm->addScalarResult(strtoupper('extension_tipo_documento'),'extension_tipo_documento','string');
            $objRsm->addScalarResult(strtoupper('descripcion_tipo_documento'),'descripcion_tipo_documento','string');
            $objRsm->addScalarResult(strtoupper('login'),'login','string');
            $objRsm->addScalarResult(strtoupper('numero_factura_sri'),'numero_factura_sri','string');
            $objRsm->addScalarResult(strtoupper('numero_contrato'),'numero_contrato','string');
            $objRsm->addScalarResult(strtoupper('nombre_encuesta'),'nombre_encuesta','string');
            $objRsm->addScalarResult(strtoupper('numero_caso'),'numero_caso','string');
            $objRsm->addScalarResult(strtoupper('actividad_id'),'actividad_id','integer');
            $objRsm->addScalarResult(strtoupper('nombre_tipo_elemento'),'nombre_tipo_elemento','string');
            $objRsm->addScalarResult(strtoupper('nombre_modelo_elemento'),'nombre_modelo_elemento','string');
            $objRsm->addScalarResult(strtoupper('nombre_elemento'),'nombre_elemento','string');
            $objRsm->addScalarResult(strtoupper('fe_creacion'),'feCreacion','string');
        }

        $strFrom = "FROM ".
                       "DB_COMUNICACION.INFO_DOCUMENTO               DOCUMENTO, ".
                       "DB_COMUNICACION.ADMI_TIPO_DOCUMENTO          TIPO_DOCUMENTO, ".
                       "DB_GENERAL.ADMI_TIPO_DOCUMENTO_GENERAL       TIPO_DOCUMENTO_GENERAL, ".
                       "DB_COMUNICACION.INFO_DOCUMENTO_RELACION      RELACION, ".
                       "DB_COMERCIAL.INFO_PUNTO                      PUNTO, ".
                       "DB_SOPORTE.INFO_CASO                         CASO, ".
                       "DB_COMUNICACION.INFO_COMUNICACION            COMUNICACION, ".
                       "DB_COMERCIAL.INFO_CONTRATO                   CONTRATO, ".
                       "DB_COMUNICACION.INFO_ENCUESTA                ENCUESTA, ".
                       "DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB  DOCUMENTO_FINANCIERO, ".
                       "DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO        TIPO_ELEMENTO, ".
                       "DB_INFRAESTRUCTURA.ADMI_MODELO_ELEMENTO      MODELO_ELEMENTO, ".
                       "DB_INFRAESTRUCTURA.INFO_ELEMENTO             ELEMENTO ";

        $strWhere = "WHERE ".
                       "DOCUMENTO.ID_DOCUMENTO                   = RELACION.DOCUMENTO_ID ".
                       "AND DOCUMENTO.TIPO_DOCUMENTO_ID          = TIPO_DOCUMENTO.ID_TIPO_DOCUMENTO ".
                       "AND DOCUMENTO.TIPO_DOCUMENTO_GENERAL_ID  = TIPO_DOCUMENTO_GENERAL.ID_TIPO_DOCUMENTO ".
                       "AND RELACION.PUNTO_ID                    = PUNTO.ID_PUNTO(+) ".
                       "AND RELACION.CASO_ID                     = CASO.ID_CASO(+) ".
                       "AND RELACION.ACTIVIDAD_ID                = COMUNICACION.ID_COMUNICACION(+) ".
                       "AND RELACION.CONTRATO_ID                 = CONTRATO.ID_CONTRATO(+) ".
                       "AND RELACION.ENCUESTA_ID                 = ENCUESTA.ID_ENCUESTA(+) ".
                       "AND RELACION.DOCUMENTO_FINANCIERO_ID     = DOCUMENTO_FINANCIERO.ID_DOCUMENTO(+) ".
                       "AND RELACION.TIPO_ELEMENTO_ID            = TIPO_ELEMENTO.ID_TIPO_ELEMENTO(+) ".
                       "AND RELACION.MODELO_ELEMENTO_ID          = MODELO_ELEMENTO.ID_MODELO_ELEMENTO(+) ".
                       "AND RELACION.ELEMENTO_ID                 = ELEMENTO.ID_ELEMENTO(+) ";

        //DB_COMUNICACION.INFO_DOCUMENTO
        if (isset($arrayParametros['fechaDesde']) && $arrayParametros['fechaDesde'])
        {
            $arrayFechaDesde   = explode("-", $arrayParametros['fechaDesde']);
            $intTimeFechaDesde = strtotime($arrayFechaDesde[2]."-".$arrayFechaDesde[1]."-".$arrayFechaDesde[0]);
            $objDateFechaDesde = date("Y/m/d", $intTimeFechaDesde);

            $strWhere.= "AND documento.fe_creacion >= :fechaDesde ";
            $objQuery->setParameter('fechaDesde', $objDateFechaDesde);
        }

        //DB_COMUNICACION.INFO_DOCUMENTO
        if (isset($arrayParametros['fechaHasta']) && $arrayParametros['fechaHasta'])
        {
            $arrayFechaHasta   = explode("-", $arrayParametros['fechaHasta']);
            $intTimeFechaHasta = strtotime($arrayFechaHasta[2]."-".$arrayFechaHasta[1]."-".$arrayFechaHasta[0]);
            $intTimeFechaHasta = strtotime(date("d-m-Y", $intTimeFechaHasta)." +1 day");
            $objDateFechaHasta = date("Y/m/d", $intTimeFechaHasta);

            $strWhere.= "AND documento.fe_creacion < :fechaHasta ";
            $objQuery->setParameter('fechaHasta', $objDateFechaHasta);
        }

        //DB_COMUNICACION.INFO_DOCUMENTO_RELACION
        if ($arrayParametros['modulo'] && $arrayParametros['modulo'] != '' && $arrayParametros['modulo'] != 'TODOS')
        {
            $strWhere.= "AND relacion.modulo = :modulo ";
            $objQuery->setParameter('modulo',$arrayParametros['modulo']);
        }

        //DB_COMUNICACION.INFO_DOCUMENTO
        if ($arrayParametros['nombreDocumento'] && $arrayParametros['nombreDocumento'] != '')
        {
            $strWhere.= "AND upper(documento.nombre_documento) like upper(:nombre) ";
            $objQuery->setParameter('nombre','%'.$arrayParametros['nombreDocumento'].'%');
        }

        //DB_COMUNICACION.INFO_DOCUMENTO && DB_COMUNICACION.ADMI_TIPO_DOCUMENTO
        if ($arrayParametros['tipoDocumento'] && $arrayParametros['tipoDocumento'] != '')
        {
            $strWhere.= "AND documento.tipo_documento_id = :tipoDocumento ";
            $objQuery->setParameter('tipoDocumento',$arrayParametros['tipoDocumento']);
        }

        //DB_COMUNICACION.INFO_DOCUMENTO && DB_GENERAL.ADMI_TIPO_DOCUMENTO_GENERAL
        if ($arrayParametros['tipoDocumentoGeneral'] && $arrayParametros['tipoDocumentoGeneral'] != '')
        {
            $strWhere.= "AND documento.tipo_documento_general_id = :tipoDocumentoGeneral ";
            $objQuery->setParameter('tipoDocumentoGeneral',$arrayParametros['tipoDocumentoGeneral']);
        }

        //DB_COMUNICACION.INFO_DOCUMENTO
        if ($arrayParametros['empresa'] && $arrayParametros['empresa'] != '')
        {
            $strWhere.= "AND documento.empresa_cod = :empresa ";
            $objQuery->setParameter('empresa',$arrayParametros['empresa']);
        }

        //DB_COMUNICACION.INFO_DOCUMENTO_RELACION && DB_COMUNICACION.INFO_ENCUESTA
        if ($arrayParametros['encuesta'] && $arrayParametros['encuesta'] != '')
        {
            $strWhere.= "AND relacion.encuesta_id = :encuesta ";
            $objQuery->setParameter('encuesta',$arrayParametros['encuesta']);
	    }

        //DB_COMUNICACION.INFO_DOCUMENTO
        if ($arrayParametros['estado'] && $arrayParametros['estado'] != '' && $arrayParametros['estado'] != 'Todos')
	    {
            $strWhere.= "AND documento.estado = :estado ";
            $objQuery->setParameter('estado',$arrayParametros['estado']);
        }

        //Tecnico--------------------------------------------------------------------

        if ($arrayParametros['tipoElemento'] && $arrayParametros['tipoElemento'] != '')
        {
            $strWhere.= "AND relacion.tipo_elemento_id = :tipoElemento ";
            $objQuery->setParameter('tipoElemento',$arrayParametros['tipoElemento']);
        }

        if ($arrayParametros['modeloElemento'] && $arrayParametros['modeloElemento'] != '')
        {
            $strWhere.= "AND relacion.modelo_elemento_id = :modeloElemento ";
            $objQuery->setParameter('modeloElemento',$arrayParametros['modeloElemento']);
        }

        if ($arrayParametros['elemento'] && $arrayParametros['elemento'] != '')
        {
            $strWhere.= "AND relacion.elemento_id = :elemento ";
            $objQuery->setParameter('elemento',$arrayParametros['elemento']);
        }

        //Comercial/Financiero-----------------------------------------------------------

        if ($arrayParametros['punto'] && $arrayParametros['punto'] != '')
        {
            $strWhere.= "AND relacion.punto_id = :punto ";
            $objQuery->setParameter('punto',$arrayParametros['punto']);
        }

        if ($arrayParametros['contrato'] && $arrayParametros['contrato'] != '')
        {
            $strWhere.= "AND relacion.contrato_id = :contrato ";
            $objQuery->setParameter('contrato',$arrayParametros['contrato']);
        }

        if ($arrayParametros['documentoFinanciero'] && $arrayParametros['documentoFinanciero'] != '')
        {
            $strWhere.= "AND relacion.documento_financiero_id = :documentoFinanciero ";
            $objQuery->setParameter('documentoFinanciero',$arrayParametros['documentoFinanciero']);
        }

        if ($arrayParametros['servicio'] && $arrayParametros['servicio'] != '')
        {
            $strWhere .= "AND relacion.servicio_id = :servicio ";
            $objQuery->setParameter('servicio',$arrayParametros['servicio']);
        }

        if ($arrayParametros['personaRol'] && $arrayParametros['personaRol']!='')
        {
            $strWhere .= "AND relacion.persona_empresa_rol_id = :personaRol ";
            $objQuery->setParameter('personaRol',$arrayParametros['personaRol']);
        }

        //Soporte-------------------------------------------------------------------------------

        if ($arrayParametros['caso'] && $arrayParametros['caso'] != '')
        {
            $strWhere.= "AND relacion.caso_id = :caso ";
            $objQuery->setParameter('caso',$arrayParametros['caso']);
        }

        if ($arrayParametros['actividad'] && $arrayParametros['actividad'] != '')
        {
            $strWhere.= "AND relacion.actividad_id = :actividad ";
            $objQuery->setParameter('actividad',$arrayParametros['actividad']);
        }

        $objQuery->setSQL($strSelect.$strFrom.$strWhere.$strOrderBy);
        return $objQuery;
      }
      
       /**
     * getDocumentoServicio
     *
     * Metodo encargado de obtener las ACTAS o ENCUESTAS generadas por un servicio dado
     *
     * @param integer $idServicio
     * @param string $tipo
     * @param integer $tipoDocGeneral
     *          
     * @return json con resultado del proceso
     *
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 15-08-2014
     */  
      public function getDocumentoServicio($idServicio,$tipo,$tipoDocGeneral = '')
      {
	    $query = $this->_em->createQuery(null);
      
	    $dql = "SELECT a
		    FROM
		    schemaBundle:InfoDocumento a,
		    schemaBundle:InfoDocumentoRelacion b
		    WHERE
		    a.id         = b.documentoId and 
		    b.modulo     = :modulo and 		    
		    b.servicioId = :servicio and ";
		    
            $query->setParameter('modulo','TECNICO');
	    $query->setParameter('servicio',$idServicio);		    
		    
            if($tipo == 'ACTA' && $tipoDocGeneral!='')
            {
		  $dql .= " a.tipoDocumentoGeneralId = :tipo";
		  $query->setParameter('tipo',$tipoDocGeneral);		
            }
            if($tipo == 'ENCUESTA')
            {             
		  $dql .= " b.encuestaId is not null";             
            }
      	    
	    $query->setDQL($dql);		    	    
	    
	    $resultado = $query->getResult();
	    
	    return $resultado;
            
      }
      
      
     /**
     * getDocumentoImagenesNodo
     *
     * Metodo encargado de obtener todas las referencias de las imagenes de cada nodo
     *
     * @param integer $idNodo
     
     * @return json con resultado del proceso
     *
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 09-03-2015
     */  
    public function getDocumentoImagenesNodo($idNodo)
    {
        $query = $this->_em->createQuery(null);

        $dql = "SELECT a.id , a.nombreDocumento , a.ubicacionFisicaDocumento , d.tagDocumento , a.feCreacion , d.id as idTag
		    FROM
		    schemaBundle:InfoDocumento a,
		    schemaBundle:InfoDocumentoRelacion b,
            schemaBundle:InfoDocumentoTag c,
            schemaBundle:AdmiTagDocumento d
		    WHERE
		    a.id         = b.documentoId and 
		    a.id         = c.documentoId and
            d.id         = c.tagDocumentoId and
            b.elementoId = :elemento and
            a.estado     <> :estado";

        $query->setParameter('elemento', $idNodo);
        $query->setParameter('estado', 'Eliminado');

        $query->setDQL($dql);                

        $resultado = $query->getResult();
        
        $arr_encontrados    = array();
        
        if($resultado)
        {
            foreach($resultado as $data)
            {
                //Se deja solo el path de la imagen para acceder a la url
                $urlImagen = str_replace("telcos/web","",$data['ubicacionFisicaDocumento']);

                $arr_encontrados[] = array(
                                            'id'           => $data['id'],
                                            'nombre'       => $data['nombreDocumento'],
                                            'url'          => $urlImagen,
                                            'idTag'        => $data['idTag'],
                                            'tag'          => ucwords(strtolower($data['tagDocumento'])),
                                            'fechaMod'     => $data['feCreacion']->format('Y-m-d H:i:s')
                );
            }                        
            
            $total = count($arr_encontrados);
            
            $data      = json_encode($arr_encontrados);
            $resultado = '{"total":"' . $total . '","encontrados":' . $data . '}';
        }
        else
        {
            $resultado = '{"total":"0","encontrados":[]}';
        }

        return $resultado;
    }


    /**
     * getImagenesByCriterios
     *
     * Metodo encargado de obtener las imágenes guardadas de acuerdo a los
     * criterios ingresados por el usuario.
     *
     * @param array $arrayParametros
     *          
     * @return array $arrayResultados
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 27-07-2015
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 19-07-2017 Se modifica la consulta para las imágenes adjuntadas en casos y soportes desde el web y móvil
     *                         tomando en cuenta la forma diferente en que se guardan desde el web y el móvil
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 07-11-2017 Se modifica la consulta para los filtros de tipo y modelo de elemento, ya que el único campo que se está 
     *                         guardando es el id del elemento
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 10-11-2017 Se modifica la consulta para no filtrar por empresa los elementos consultados. Además se agrega el filtro
     *                         del estado de la evaluación del elemento
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.4 16-11-2017 Se modifica la consulta para obtener los datos de la evaluación, elemento y casos sin importar si se eligen o no
     *                         filtros relacionados a criterios Comerciales, Técnicos y Soporte.
     *                         Para filtrar por región se verifica la región del elemento en caso de no existir la región del usuario de creación
     * 
     * @author Jean Nazareno <jnazareno@telconet.ec>
     * @version 1.5 24-06-2019 Se agregan 2 campos al query (floatLatitud, floatLongitud)
     * cost 118
     * 
     * @author Jean Nazareno <jnazareno@telconet.ec>
     * @version 1.6 12-11-2019 Se agregan 2 campos al query (PORCENTAJE_EVALUACION_BASE, PORCENTAJE_EVALUADO)
     * cost 118
     *  
     */  
    public function getImagenesByCriterios( $arrayParametros )
    {
        $arrayResultados                = array();
        $arrayResultados['registros']   = array();
        $arrayResultados['total']       = 0;
        try
        {
            $arrayDatos             = array();
            $intTotal               = 0;
            $boolFechaDesdeSoporte  = false;

            $query = $this->_em->createQuery();

            $strSelect  = "SELECT DISTINCT ido.id, ip.nombres, ip.apellidos, ido.nombreDocumento, 
                                  ido.feCreacion, ido.ubicacionFisicaDocumento as urlImagen, ido.floatLatitud, ido.floatLongitud, 
                                  ip.login, idr.id as idDocumentoRelacion, idr.tipoElementoId, 
                                  idr.strEstadoEvaluacion as estadoEvaluacion, idr.strEvaluacionTrabajo as evaluacionTrabajo,
                                  idr.strUsrEvaluacion as usrEvaluacion, idr.floatPorcentajeEvaluacionBase as porcentajeEvaluacionBase,
                                  idr.floatPorcentajeEvaluado as porcentajeEvaluado,
                                  CONCAT(evaluador.nombres,CONCAT(' ', evaluador.apellidos)) as nombreEvaluador,
                                  ic.numeroCaso,
                                  elemento.id as idElemento, 
                                  tipoElemento.id as idTipoElemento, 
                                  tipoElemento.nombreTipoElemento as nombreTipoElemento,
                                  icomunicacion.id as numeroTarea ";
            $strFrom    = "FROM schemaBundle:InfoDocumento ido
                           LEFT JOIN schemaBundle:AdmiTipoDocumentoGeneral atdg WITH ido.tipoDocumentoGeneralId = atdg.id,
                           schemaBundle:InfoDocumentoRelacion idr
                           LEFT JOIN schemaBundle:InfoPersona evaluador WITH evaluador.login = idr.strUsrEvaluacion 
                           LEFT JOIN schemaBundle:InfoCaso ic WITH ic.id = idr.casoId 
                           LEFT JOIN schemaBundle:InfoElemento elemento WITH idr.elementoId = elemento.id 
                           LEFT JOIN schemaBundle:AdmiModeloElemento modeloElemento WITH modeloElemento.id = elemento.modeloElementoId 
                           LEFT JOIN schemaBundle:AdmiTipoElemento tipoElemento WITH tipoElemento.id = modeloElemento.tipoElementoId 
                           LEFT JOIN schemaBundle:InfoEmpresaElementoUbica empresaElementoUbica WITH empresaElementoUbica.elementoId = elemento.id 
                           LEFT JOIN schemaBundle:InfoUbicacion ubicacion WITH ubicacion.id = empresaElementoUbica.ubicacionId
                           LEFT JOIN schemaBundle:AdmiParroquia parroquia WITH parroquia.id = ubicacion.parroquiaId 
                           LEFT JOIN schemaBundle:AdmiCanton canton WITH canton.id = parroquia.cantonId 
                           LEFT JOIN schemaBundle:InfoDetalle idt WITH ( idr.detalleId = idt.id OR idr.actividadId = idt.id) 
                           LEFT JOIN schemaBundle:InfoComunicacion icomunicacion WITH icomunicacion.detalleId = idt.id ";
            
            $strWhere   = "WHERE ido.id = idr.documentoId
                             AND ido.estado <> :estadoEliminado
                             AND idr.estado <> :estadoEliminado ";

            $strOrderBy = "ORDER BY ido.feCreacion DESC";


            if( $arrayParametros['fechaDesde'] )
            {
                $arrayFechaDesde = explode("-", $arrayParametros['fechaDesde']);
                $timeFechaDesde  = strtotime($arrayFechaDesde[2]."-".$arrayFechaDesde[1]."-".$arrayFechaDesde[0]);
                $dateFechaDesde  = date("Y/m/d", $timeFechaDesde);

                $strWhere .= "AND ido.feCreacion >= :fechaDesde ";
                $query->setParameter("fechaDesde", trim($dateFechaDesde));
            }

            if( $arrayParametros['fechaHasta'] )
            {
                $arrayFechaHasta = explode("-", $arrayParametros['fechaHasta']);
                $timeFechaHasta  = strtotime($arrayFechaHasta[2]."-".$arrayFechaHasta[1]."-".$arrayFechaHasta[0]);
                $dateFechaHasta  = strtotime(date("d-m-Y", $timeFechaHasta)." +1 day");
                $dateFechaHasta  = date("Y/m/d", $dateFechaHasta);

                $strWhere .= "AND ido.feCreacion <= :fechaHasta ";
                $query->setParameter("fechaHasta", trim($dateFechaHasta));
            }

            if((isset($arrayParametros['tipoElemento']) && !empty($arrayParametros['tipoElemento'])) 
                || (isset($arrayParametros['modeloElemento']) && !empty($arrayParametros['modeloElemento'])) 
                || (isset($arrayParametros['elemento']) && !empty($arrayParametros['elemento']))
                )
            {
                if(isset($arrayParametros['tipoElemento']) && !empty($arrayParametros['tipoElemento']))
                {
                    $strWhere .= "AND tipoElemento.id = :tipoElemento ";
                    $query->setParameter("tipoElemento", $arrayParametros['tipoElemento']);
                }

                if(isset($arrayParametros['modeloElemento']) && !empty($arrayParametros['modeloElemento']))
                {
                    $strWhere .= "AND modeloElemento.id = :modeloElemento ";
                    $query->setParameter("modeloElemento", $arrayParametros['modeloElemento']);
                }

                if(isset($arrayParametros['elemento']) && !empty($arrayParametros['elemento']))
                {
                    $strWhere .= "AND elemento.id = :elemento ";
                    $query->setParameter("elemento", $arrayParametros['elemento']);
                }
            }
            
            if(isset($arrayParametros['strEstadoEvaluacion']) && !empty($arrayParametros['strEstadoEvaluacion']))
            {
                if($arrayParametros['strEstadoEvaluacion'] === 'Pendiente')
                {
                    $strWhere .= "AND (idr.strEstadoEvaluacion = :strEstadoEvaluacion 
                                       OR (idr.strEstadoEvaluacion IS NULL AND (idr.casoId IS NOT NULL OR idr.elementoId IS NOT NULL))) ";
                }
                else
                {
                    $strWhere .= "AND idr.strEstadoEvaluacion = :strEstadoEvaluacion ";
                }
                $query->setParameter("strEstadoEvaluacion", trim($arrayParametros['strEstadoEvaluacion']));
            }

            if( $arrayParametros['login'] )
            {
                $strFrom  .= ", schemaBundle:InfoServicio infs,
                                schemaBundle:InfoPunto infp ";

                $strWhere .= "AND idr.servicioId = infs.id
                              AND infs.puntoId = infp.id
                              AND infp.login like :login ";

                $query->setParameter("login", '%'.strtolower(trim($arrayParametros['login']).'%'));
            }

            if( $arrayParametros['tipoSoporte'] )
            {
                if( $arrayParametros['tipoSoporte'] == 'caso' )
                {
                    if( $arrayParametros['numeroSoporte'] )
                    {
                        $strWhere .= "AND ic.numeroCaso = :caso ";
                        $query->setParameter("caso", trim($arrayParametros['numeroSoporte']));
                    }

                    if( $arrayParametros['fechaDesdeSoporte'] )
                    {
                        $boolFechaDesdeSoporte = true;

                        $arrayFechaDesde = explode("-", $arrayParametros['fechaDesdeSoporte']);
                        $timeFechaDesde  = strtotime($arrayFechaDesde[2]."-".$arrayFechaDesde[1]."-".$arrayFechaDesde[0]);
                        $dateFechaDesde  = date("Y/m/d", $timeFechaDesde);

                        $strWhere .= "AND ic.feCreacion >= :fechaDesdeSoporte ";

                        $query->setParameter("fechaDesdeSoporte", trim($dateFechaDesde));
                    }

                    if( $arrayParametros['fechaHastaSoporte'] )
                    {
                        $arrayFechaHasta = explode("-", $arrayParametros['fechaHastaSoporte']);
                        $timeFechaHasta  = strtotime($arrayFechaHasta[2]."-".$arrayFechaHasta[1]."-".$arrayFechaHasta[0]);
                        $dateFechaHasta  = strtotime(date("d-m-Y", $timeFechaHasta)." +1 day");
                        $dateFechaHasta  = date("Y/m/d", $dateFechaHasta);

                        $strWhere .= "AND ic.feCreacion <= :fechaHastaSoporte ";

                        $query->setParameter("fechaHastaSoporte", trim($dateFechaHasta));
                    }

                    if( $arrayParametros['empresa'] )
                    {
                        $strWhere .= "AND ic.empresaCod = :empresa ";

                        $query->setParameter("empresa", trim($arrayParametros['empresa']));
                    }

                    if( $arrayParametros['departamento'] )
                    {
                        $strFrom  .= ", schemaBundle:InfoDetalleHipotesis idh,
                                      schemaBundle:InfoCasoAsignacion ica ";

                        $strWhere .= "AND ic.id = idh.casoId 
                                      AND idh.id = ( SELECT MAX(idhMax.id)  
                                                     FROM schemaBundle:InfoDetalleHipotesis idhMax
                                                     WHERE idhMax.casoId = idh.casoId )
                                      AND idh.id = ica.detalleHipotesisId
                                      AND ica.asignadoId = :departamento ";

                        $query->setParameter('departamento', trim($arrayParametros['departamento']));
                    }  
                }//( $arrayParametros['tipoSoporte'] == 'caso' )
                elseif( $arrayParametros['tipoSoporte'] == 'tarea' )
                {
                    /*
                     * Desde web se adjuntan imágenes a una tarea por medio del campo detalleId en la infoDocumentoRelacion que contiene 
                     * el idDetalle de la tarea
                     * 
                     * Desde el móvil se adjuntan imágenes a tareas(incidencias) relacionada a un elemento. El campo que asocia 
                     * la tarea es actividadId en la infoDocumentoRelacion con el idDetalle de la tarea
                     */
                    
                    if( $arrayParametros['numeroSoporte'] )
                    {
                        $strWhere .= "AND icomunicacion.id = :tarea ";
                        $query->setParameter("tarea", trim($arrayParametros['numeroSoporte']));
                    }

                    if( $arrayParametros['fechaDesdeSoporte'] )
                    {
                        $boolFechaDesdeSoporte = true;

                        $arrayFechaDesde = explode("-", $arrayParametros['fechaDesdeSoporte']);
                        $timeFechaDesde  = strtotime($arrayFechaDesde[2]."-".$arrayFechaDesde[1]."-".$arrayFechaDesde[0]);
                        $dateFechaDesde  = date("Y/m/d", $timeFechaDesde);

                        $strWhere .= "AND idt.feCreacion >= :fechaDesdeSoporte ";

                        $query->setParameter("fechaDesdeSoporte", trim($dateFechaDesde));
                    }

                    if( $arrayParametros['fechaHastaSoporte'] )
                    {
                        $arrayFechaHasta = explode("-", $arrayParametros['fechaHastaSoporte']);
                        $timeFechaHasta  = strtotime($arrayFechaHasta[2]."-".$arrayFechaHasta[1]."-".$arrayFechaHasta[0]);
                        $dateFechaHasta  = strtotime(date("d-m-Y", $timeFechaHasta)." +1 day");
                        $dateFechaHasta  = date("Y/m/d", $dateFechaHasta);

                        $strWhere .= "AND idt.feCreacion <= :fechaHastaSoporte ";

                        $query->setParameter("fechaHastaSoporte", trim($dateFechaHasta));
                    }

                    if( $arrayParametros['empresa'] )
                    {
                        $strFrom  .= ", schemaBundle:InfoDetalleAsignacion ida ";
                        $strWhere .= "AND idt.id = ida.detalleId ";

                        if( $arrayParametros['departamento'] )
                        {
                            $strWhere .= "AND ida.asignadoId = :departamento ";

                            $query->setParameter('departamento', trim($arrayParametros['departamento']));
                        }
                        else
                        {                            
                            $strFrom  .= ", schemaBundle:AdmiDepartamento ad ";

                            $strWhere .= "AND ad.id = ida.asignadoId 
                                          AND ad.empresaCod = :empresa ";

                            $query->setParameter("empresa", trim($arrayParametros['empresa']));
                        }
                    }
                }//( $arrayParametros['tipoSoporte'] == 'tarea' )
            }//( isset($arrayParametros['tipoSoporte']) )
            
            /*
             * Se verifica las imágenes subidas desde web o móvil.
             * Desde web no se guarda el tipoDocumentoGeneralId de 'IMÁGENES' debido a que se pueden adjuntar cualquier tipo de documentos
             * pero si se guarda el tipoDocumentoId desde donde se pueden obtener las imágenes por la extensión JPEG, JPG, PNG
             * Desde el móvil, las imágenes se guardan con el tipo de documento general id de 'IMAGENES' y no guardan el tipoDocumentoId
             */
            $strWhere   .= "AND ( ( atdg.descripcionTipoDocumento = :descripcionTipoDoc AND atdg.estado = :estadoActivo )
                                  OR (ido.tipoDocumentoId IN (  select atd.id 
                                                                from schemaBundle:AdmiTipoDocumento atd
                                                                where (atd.extensionTipoDocumento = :tipoJpeg 
                                                                OR atd.extensionTipoDocumento = :tipoJpg
                                                                OR atd.extensionTipoDocumento = :tipoPng )
                                                                AND atd.estado = :estadoActivo ))) ";
            $query->setParameter("descripcionTipoDoc", 'IMAGENES');
            $query->setParameter("estadoActivo", 'Activo');
            $query->setParameter("tipoJpeg", 'JPEG');
            $query->setParameter("tipoJpg", 'JPG');
            $query->setParameter("tipoPng", 'PNG');
            
            $strFrom    .= ", schemaBundle:InfoPersona ip 
                            LEFT JOIN schemaBundle:InfoPersonaEmpresaRol iper WITH (ip.id = iper.personaId AND iper.estado <> :estadoEliminado)
                            LEFT JOIN schemaBundle:InfoOficinaGrupo oficina WITH oficina.id = iper.oficinaId
                            LEFT JOIN schemaBundle:AdmiCanton cantonUsrCreacionImg WITH cantonUsrCreacionImg.id = oficina.cantonId ";
            $strWhere   .= "AND ip.login = ido.usrCreacion  
                            AND ip.estado <> :estadoEliminado ";
            
            if(isset($arrayParametros['strRegionSession']) && !empty($arrayParametros['strRegionSession']))
            {
                $strWhere .= "AND ( cantonUsrCreacionImg.region = :strRegionSession 
                                    OR (canton.region = :strRegionSession AND idr.elementoId IS NOT NULL)) ";
                $query->setParameter("strRegionSession", $arrayParametros['strRegionSession']);
            }
            
            if( $arrayParametros['identificacion'] )
            {
                $strWhere .= "AND ip.identificacionCliente = :identificacion ";
                $query->setParameter("identificacion", trim($arrayParametros['identificacion']));
            }

            if( $arrayParametros['nombres'] )
            {
                $strWhere .= "AND ip.nombres like :nombres  ";
                $query->setParameter("nombres", '%'.strtoupper(trim($arrayParametros['nombres']).'%'));
            }

            if( $arrayParametros['apellidos'] )
            {
                $strWhere .= "AND ip.apellidos like :apellidos  ";
                $query->setParameter("apellidos", '%'.strtoupper(trim($arrayParametros['apellidos']).'%'));
            }
            
            if( $arrayParametros['razonSocial'] )
            {
                $strWhere .= "AND ip.razonSocial like :razonSocial  ";
                $query->setParameter("razonSocial", '%'.strtoupper(trim($arrayParametros['razonSocial']).'%'));
            }

            $query->setParameter('estadoEliminado', 'Eliminado');
            
            $strDql = $strSelect.$strFrom.$strWhere.$strOrderBy;
            
            $query->setDQL($strDql);

            $arrayTmpDatos = array();
            $arrayTmpDatos = $query->getResult();

            $intTmpTotal = 0;
            $intTmpTotal = count($arrayTmpDatos);

            if( $intTmpTotal > 0 )
            {            
                if( isset($arrayParametros['inicio']) && isset($arrayParametros['limite']) )
                {
                    $arrayDatos = $query->setFirstResult($arrayParametros['inicio'])
                                        ->setMaxResults($arrayParametros['limite'])
                                        ->getResult();
                }
                else
                {
                    $arrayDatos = $arrayTmpDatos;
                }
            }
            $arrayResultados['registros'] = $arrayDatos;
            $arrayResultados['total']     = $intTmpTotal;
        } 
        catch (\Exception $e)
        {
            error_log('InfoDocumentoRepository->getImagenesByCriterios'.$e->getMessage());
        }

        return $arrayResultados;
    }
    
    
    /**
     * getDocumentosByCriterios
     *
     * Metodo encargado de obtener los documentos guardados de acuerdo a los
     * criterios ingresados por el usuario.
     *
     * @param array $arrayParametros[ 'strCodigoTipoDocumento'     => 'Codigo del tipo de documento general'
     *                                'intIdPuntoCliente'          => 'Id del punto cliente'
     *                                'descripcionTipoDocumento'   => 'Descripcion del tipo de documento general'
     *                                'empresa'                    => 'Código de la empresa en sessión del usuario'
     *                                'estadoDocumento'            => 'Estado del documento'
     *                                'estadoDocumentoRelacion'    => 'Estado de la relación del documento'
     *                                'estadoTipoDocumentoGeneral' => 'Estado del tipo de documento general'
     *                                'servicios'                  => 'Id con los servicios a consultar'
     *                                'nombreDocumento'            => 'Nombre del documento que se desea consultar' 
     *                                'inicio'                     => 'Fila donde inicia la consulta del query'
     *                                'limite'                     => 'Limite o cantidad de registros que se desea obtener' ]
     *          
     * @return array $arrayResultados[ 'registros' => 'Información consultada',
     *                                 'total'     => 'Cantidad de registros consultados' ]
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 29-09-2015
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 17-02-2017 - Se agregan los siguientes parámetros:
     *                           - 'strCodigoTipoDocumento' Para consultar por tipo de código de la tabla 'DB_GENERAL.ADMI_TIPO_DOCUMENTO_GENERAL'
     *                           - 'intIdPuntoCliente' Para consultar por id punto del cliente en la tabla 'DB_COMUNICACION.INFO_DOCUMENTO_RELACION'
     * Costo del Query: 8
     */  
    public function getDocumentosByCriterios( $arrayParametros )
    {
        $arrayResultados = array();
        
        $query      = $this->_em->createQuery();
        $queryCount = $this->_em->createQuery();
        
        $strSelect      = "SELECT DISTINCT ifdr.id ";
        $strSelectCount = "SELECT COUNT(DISTINCT ifdr.id) ";
        
        $strFrom    = "FROM schemaBundle:InfoDocumento ifd,
                            schemaBundle:InfoDocumentoRelacion ifdr,
                            schemaBundle:AdmiTipoDocumentoGeneral atdg ";
        
        $strWhere   = "WHERE ifd.id = ifdr.documentoId
                         AND ifd.tipoDocumentoGeneralId = atdg.id ";
        
        
        if( isset($arrayParametros['strCodigoTipoDocumento']) && !empty($arrayParametros['strCodigoTipoDocumento']) )
        {
            $strWhere .= "AND atdg.codigoTipoDocumento = :strCodigoTipoDocumento ";

            $query->setParameter("strCodigoTipoDocumento",      $arrayParametros['strCodigoTipoDocumento']);
            $queryCount->setParameter("strCodigoTipoDocumento", $arrayParametros['strCodigoTipoDocumento']);
        }
        
        
        if( isset($arrayParametros['intIdPuntoCliente']) && !empty($arrayParametros['intIdPuntoCliente']) )
        {
            $strWhere .= "AND ifdr.puntoId = :intIdPuntoCliente ";

            $query->setParameter("intIdPuntoCliente",      $arrayParametros['intIdPuntoCliente']);
            $queryCount->setParameter("intIdPuntoCliente", $arrayParametros['intIdPuntoCliente']);
        }
        
        
        if( isset($arrayParametros['descripcionTipoDocumento']) )
        {
            if($arrayParametros['descripcionTipoDocumento'])
            {
                $strWhere .= "AND atdg.descripcionTipoDocumento = :descripcionTipoDocumento ";
                
                $query->setParameter("descripcionTipoDocumento", $arrayParametros['descripcionTipoDocumento']);
                
                $queryCount->setParameter("descripcionTipoDocumento", $arrayParametros['descripcionTipoDocumento']);
            }
        }
        
        if( isset($arrayParametros['empresa']) )
        {
            if($arrayParametros['empresa'])
            {
                $strWhere .= "AND ifd.empresaCod = :empresa ";
                
                $query->setParameter("empresa", $arrayParametros['empresa']);
                
                $queryCount->setParameter("empresa", $arrayParametros['empresa']);
            }
        }
        
        if( isset($arrayParametros['estadoDocumento']) )
        {
            if($arrayParametros['estadoDocumento'])
            {
                $strWhere .= "AND ifd.estado IN (:estadoDocumento) ";
                
                $query->setParameter("estadoDocumento", array_values($arrayParametros['estadoDocumento']));
                
                $queryCount->setParameter("estadoDocumento", array_values($arrayParametros['estadoDocumento']));
            }
        }
        
        if( isset($arrayParametros['estadoDocumentoRelacion']) )
        {
            if($arrayParametros['estadoDocumentoRelacion'])
            {
                $strWhere .= "AND ifdr.estado IN (:estadoDocumentoRelacion) ";
                
                $query->setParameter("estadoDocumentoRelacion", array_values($arrayParametros['estadoDocumentoRelacion']));
                
                $queryCount->setParameter("estadoDocumentoRelacion", array_values($arrayParametros['estadoDocumentoRelacion']));
            }
        }
        
        if( isset($arrayParametros['estadoTipoDocumentoGeneral']) )
        {
            if($arrayParametros['estadoTipoDocumentoGeneral'])
            {
                $strWhere .= "AND atdg.estado IN (:estadoTipoDocumentoGeneral) ";
                
                $query->setParameter("estadoTipoDocumentoGeneral", array_values($arrayParametros['estadoTipoDocumentoGeneral']));
                
                $queryCount->setParameter("estadoTipoDocumentoGeneral", array_values($arrayParametros['estadoTipoDocumentoGeneral']));
            }
        }
        
        if( isset($arrayParametros['servicios']) )
        {
            if($arrayParametros['servicios'])
            {
                $strWhere .= "AND ifdr.servicioId IN (:servicios) ";
                
                $query->setParameter("servicios", array_values($arrayParametros['servicios']));
                
                $queryCount->setParameter("servicios", array_values($arrayParametros['servicios']));
            }
        }
        
        if( isset($arrayParametros['nombreDocumento']) )
        {
            if( $arrayParametros['nombreDocumento'] )
            {
                $strWhere .= "AND ifd.nombreDocumento LIKE :nombreDocumento ";

                $query->setParameter("nombreDocumento", '%'.trim($arrayParametros['nombreDocumento']).'%');
                
                $queryCount->setParameter("nombreDocumento", '%'.trim($arrayParametros['nombreDocumento']).'%');
            }
        }
        
        $strDql      = $strSelect.$strFrom.$strWhere;
        $strDqlCount = $strSelectCount.$strFrom.$strWhere;
        
        $query->setDQL($strDql);
        $queryCount->setDQL($strDqlCount);

        if( isset($arrayParametros['inicio']) )
        {
            if($arrayParametros['inicio'])
            {
                $query->setFirstResult($arrayParametros['inicio']);
            }
        }
        
        if( isset($arrayParametros['limite']) )
        {
            if($arrayParametros['limite'])
            {
                $query->setMaxResults($arrayParametros['limite']);
            }
        }
            
        $arrayResultados['registros'] = $query->getResult();
        $arrayResultados['total']     = $queryCount->getSingleScalarResult();
        
        return $arrayResultados;
        
    }

    
    
    
    
    /**
     * getIdsTiposDocumentosArchivosSubidos
     *
     * Metodo encargado de obtener los distintos ids de AdmiTipoDocumentoGeneral por idPersonaEmpresaRol
     *
     * @param int $idPersonaEmpresaRol
     *          
     * @return array $arrayResultados
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 18-12-2015
     */ 
    function getIdsTiposDocumentosArchivosSubidosByPersonaEmpresaRol($idPersonaEmpresaRol)
    {
        
        $query = $this->_em->createQuery();
        $strSql = " SELECT DISTINCT atdg.id
                    FROM 
                        schemaBundle:InfoDocumento ifd,
                        schemaBundle:InfoDocumentoRelacion ifdr,
                        schemaBundle:AdmiTipoDocumentoGeneral atdg
                    WHERE 
                        ifdr.personaEmpresaRolId = :idPersonaEmpresaRol AND
                        ifdr.estado = :estado AND
                        ifdr.documentoId = ifd.id AND
                        ifd.tipoDocumentoGeneralId = atdg.id";

        $query->setParameter('idPersonaEmpresaRol', $idPersonaEmpresaRol);
        $query->setParameter('estado', 'Activo');
        $query->setDQL($strSql);
        $arrayResultados = $query->getResult();

        return $arrayResultados;
    }
    
    
    /**
     * getDocumentosByCriterios
     *
     * Metodo encargado de obtener los distintos ids de AdmiTipoDocumentoGeneral por idElemento
     *
     * @param int $idPersonaEmpresaRol
     *          
     * @return array $arrayResultados
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 18-12-2015
     */ 
    function getIdsTiposDocumentosArchivosSubidosByElemento($idElemento)
    {
        
        $query = $this->_em->createQuery();
        $strSql = " SELECT DISTINCT atdg.id
                    FROM 
                        schemaBundle:InfoDocumento ifd,
                        schemaBundle:InfoDocumentoRelacion ifdr,
                        schemaBundle:AdmiTipoDocumentoGeneral atdg
                    WHERE 
                        ifdr.elementoId = :idElemento AND
                        ifdr.estado = :estado AND
                        ifdr.documentoId = ifd.id AND
                        ifd.tipoDocumentoGeneralId = atdg.id";

        $query->setParameter('idElemento', $idElemento);
        $query->setParameter('estado', 'Activo');
        $query->setDQL($strSql);
        $arrayResultados = $query->getResult();

        return $arrayResultados;
    }
    
    
    
    /**
      * getJsonDetallesMantenimientosXParametros
      *
      * Método que retornará los detalles de los mantenimientos ingresados al transporte                        
      *
      * @param array $parametros[
      *                             "idMantenimientoTransporte": id de la orden de mantenimiento realizada al vehículo
      *                         ]
      * @return json $resultado
      *
      * @author Lizbeth Cruz <mlcruz@telconet.ec>
      * @version 1.0 07-07-2016
      */
    public function getJsonAdjuntosMantenimientosXParametros($parametros)
    {
        $arrayEncontrados = array();
        $arrayResultado = $this->getResultadoAdjuntosMantenimientosXParametros($parametros);
        $resultado  = $arrayResultado['resultado'];
        $intTotal   = $arrayResultado['total'];
        $total = 0;

        if($resultado)
        {
            $total = $intTotal;
            foreach($resultado as $data)
            {
                $arrayEncontrados[]=array(
                                            'id'                        => $data["id"],
                                            'ubicacionLogicaDocumento'  => $data["ubicacionLogicaDocumento"],
                                            'tipoDocumentoGeneral'      => $data["tipoDocumentoGeneral"],
                                            'usrCreacion'               => $data["usrCreacion"],
                                            'fechaCreacion'             => date_format($data["feCreacion"], "Y-m-d G:i")
                );
            }
        }

        $arrayRespuesta = array('total' => $total, 'encontrados' => $arrayEncontrados);
        $jsonData = json_encode($arrayRespuesta);
        return $jsonData;
    }
    
    
    /**
      * getResultadoDetallesMantenimientosXParametros
      *
      * Método que obtendrá los mantenimientos de los transportes                                
      *
      * @param array $parametros[
      *                             "idMantenimientoTransporte": id de la orden de mantenimiento realizada al vehículo
      *                         ]
      * @return json $resultado
      *
      * @author Lizbeth Cruz <mlcruz@telconet.ec>
      * @version 1.0 01-06-2016
      * 
      * @author Lizbeth Cruz <mlcruz@telconet.ec>
      * @version 1.1 22-06-2016 Se realizan cambios de acuerdo a los formatos de calidad establecidos
      */
    public function getResultadoAdjuntosMantenimientosXParametros($parametros)
    {
        $arrayRespuesta['resultado']   = "";
        $arrayRespuesta['total']       = 0;
        try
        {
            $query          = $this->_em->createQuery();
            $queryCount     = $this->_em->createQuery();
            $strWhere       = "";
            
            $sqlSelect      = " SELECT doc.id, doc.ubicacionLogicaDocumento,doc.tipoDocumentoGeneral,doc.usrCreacion,doc.feCreacion ";
            $sqlSelectCount = "SELECT COUNT(doc.id) ";

            $sqlFrom        = " FROM 
                                schemaBundle:InfoDocumentoRelacion docRel, 
                                schemaBundle:InfoDocumento doc  
                                WHERE docRel.documentoId=doc.id ";
            
            if(isset($parametros["idMantenimientoTransporte"]))
            {
                if($parametros["idMantenimientoTransporte"])
                {
                    $strWhere .= "AND docRel.mantenimientoTransporteId= :idMantenimientoTransporte ";
                    $query->setParameter("idMantenimientoTransporte", $parametros["idMantenimientoTransporte"]);
                    $queryCount->setParameter("idMantenimientoTransporte", $parametros["idMantenimientoTransporte"]);
                }
            }
            
            
            $sqlOrderBy =" ORDER BY det.feCreacion ASC";

            $sql        = $sqlSelect.$sqlFrom.$strWhere.$sqlOrderBy;       
            $sqlCount   = $sqlSelectCount.$sqlFrom.$strWhere;

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
     * Documentación para el método 'getResultadoFirmaEmpleados'
     * Costo=168
     * 
     * Esta función ejecuta el query que retorna el documento FIRMA asociado a un empleado
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 18-07-2016 
     * 
     * @param array $arrayParametros[   "esJefe"                : string que identifica si la persona en sesión es o no jefe
     *                                  "region"                : region a la que pertenece el canton del empleado en sesión
     *                                  "nombres"               : nombres del empleado
     *                                  "apellidos"             : apellidos del empleado
     *                                  "identificacion"        : identificacion del empleado
     *                                  "departamento"          : departamento del empleado
     *                                  "canton"                : canton a la que pertenece el empleado
     *                                  "tipoDocumentoGeneralId": id del tipo de documento general
     *                                  "empresaCod"            : id de la empresa
     *                              ]
     * 
     * @return array $arrayRespuesta ['registros','total']
     * 
     */
    public function getResultadoFirmaEmpleados($arrayParametros){

        $arrayRespuesta['total']     = 0;
        $arrayRespuesta['resultado'] = "";

        try
        {
            $rsmCount = new ResultSetMappingBuilder($this->_em);
            $ntvQueryCount = $this->_em->createNativeQuery(null, $rsmCount);
            
            $strSelectCount = " SELECT COUNT(*) AS TOTAL ";
            
            $rsm = new ResultSetMappingBuilder($this->_em);
            $ntvQuery = $this->_em->createNativeQuery(null, $rsm);
            
            $strSelect = " SELECT idr.ID_DOCUMENTO_RELACION, idc.ID_DOCUMENTO, per.ID_PERSONA_ROL, persona.IDENTIFICACION_CLIENTE, "
                        . "persona.NOMBRES, persona.APELLIDOS, idc.UBICACION_FISICA_DOCUMENTO, idc.UBICACION_LOGICA_DOCUMENTO ";
            
            $strFrom =" FROM
                        DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL per 
                        INNER JOIN DB_COMERCIAL.INFO_PERSONA persona ON persona.ID_PERSONA = per.PERSONA_ID
                        INNER JOIN DB_COMERCIAL.INFO_EMPRESA_ROL er ON er.ID_EMPRESA_ROL = per.EMPRESA_ROL_ID 
                        INNER JOIN DB_COMERCIAL.INFO_EMPRESA_GRUPO eg ON eg.COD_EMPRESA = er.EMPRESA_COD
                        INNER JOIN DB_COMERCIAL.INFO_OFICINA_GRUPO og ON og.ID_OFICINA = per.OFICINA_ID 
                        INNER JOIN DB_COMERCIAL.ADMI_ROL r ON r.ID_ROL = er.ROL_ID
                        INNER JOIN DB_COMERCIAL.ADMI_TIPO_ROL tr ON tr.ID_TIPO_ROL = r.TIPO_ROL_ID
                        INNER JOIN DB_COMERCIAL.ADMI_DEPARTAMENTO d ON d.ID_DEPARTAMENTO = per.DEPARTAMENTO_ID
                        INNER JOIN DB_COMERCIAL.ADMI_CANTON c ON c.ID_CANTON = og.CANTON_ID 
                        LEFT JOIN DB_COMUNICACION.INFO_DOCUMENTO_RELACION idr ON per.ID_PERSONA_ROL = idr.PERSONA_EMPRESA_ROL_ID
                        LEFT JOIN DB_COMUNICACION.INFO_DOCUMENTO idc ON idc.ID_DOCUMENTO = idr.DOCUMENTO_ID 
                        LEFT JOIN DB_GENERAL.ADMI_TIPO_DOCUMENTO_GENERAL atdg ON atdg.ID_TIPO_DOCUMENTO = idc.TIPO_DOCUMENTO_GENERAL_ID
                        WHERE tr.DESCRIPCION_TIPO_ROL = :descripcionTipoRol 
                        AND er.EMPRESA_COD = :empresaCod 
                        AND per.ESTADO= :estado ";
            
            $rsm->addScalarResult('ID_DOCUMENTO_RELACION','ID_DOCUMENTO_RELACION','integer');
            $rsm->addScalarResult('ID_DOCUMENTO','ID_DOCUMENTO','integer');
            $rsm->addScalarResult('ID_PERSONA_ROL','ID_PERSONA_ROL','integer');
            $rsm->addScalarResult('ID_PERSONA','ID_PERSONA','integer');
            $rsm->addScalarResult('IDENTIFICACION_CLIENTE','IDENTIFICACION_CLIENTE','integer');
            $rsm->addScalarResult('NOMBRES','NOMBRES','integer');
            $rsm->addScalarResult('APELLIDOS', 'APELLIDOS','string');
            $rsm->addScalarResult('UBICACION_FISICA_DOCUMENTO', 'UBICACION_FISICA_DOCUMENTO','string');
            $rsm->addScalarResult('UBICACION_LOGICA_DOCUMENTO', 'UBICACION_LOGICA_DOCUMENTO','string');
            
            $rsmCount->addScalarResult('TOTAL','total','integer');
            
            
            $strWhere = "";
            if(isset($arrayParametros['esJefe']) )
            {
                if($arrayParametros['esJefe'])
                {
                    $strWhere .= 'AND per.ES_JEFE = :esJefe ';        
                    $ntvQuery->setParameter('esJefe', $arrayParametros['esJefe']);
                    $ntvQueryCount->setParameter('esJefe', $arrayParametros['esJefe']);
                }
            }
            
            if(isset($arrayParametros['region']) )
            {
                if($arrayParametros['region'])
                {
                    $strWhere .= 'AND c.REGION = :region ';        
                    $ntvQuery->setParameter('region', $arrayParametros['region']);
                    $ntvQueryCount->setParameter('region', $arrayParametros['region']);
                }
            }
            
            if(isset($arrayParametros['nombres']) )
            {
                if($arrayParametros['nombres'])
                {
                    $strWhere .= 'AND p.NOMBRES LIKE :nombres ';        
                    $ntvQuery->setParameter('nombres', '%'.strtoupper($arrayParametros['nombres']).'%');
                    $ntvQueryCount->setParameter('nombres', '%'.strtoupper($arrayParametros['nombres']).'%');
                }
            }
            if(isset($arrayParametros['apellidos']) )
            {
                if($arrayParametros['apellidos'])
                {
                    $strWhere .= 'AND p.APELLIDOS LIKE :apellidos ';        
                    $ntvQuery->setParameter('apellidos', '%'.strtoupper($arrayParametros['apellidos']).'%');
                    $ntvQueryCount->setParameter('apellidos', '%'.strtoupper($arrayParametros['apellidos']).'%');

                }
            }

            if(isset($arrayParametros['identificacion']) )
            {
                if($arrayParametros['identificacion'])
                {
                    $strWhere .= 'AND p.IDENTIFICACION_CLIENTE LIKE :identificacion ';        
                    $ntvQuery->setParameter('identificacion', '%'.$arrayParametros['identificacion'].'%');
                    $ntvQueryCount->setParameter('identificacion', '%'.$arrayParametros['identificacion'].'%');

                }
            }
            
            if(isset($arrayParametros['departamento']))
            {
                if($arrayParametros['departamento'])
                {
                    if($arrayParametros['departamento']!="Todos")
                    {
                        $strWhere .= 'AND per.DEPARTAMENTO_ID = :idDepartamento ';        
                        $ntvQuery->setParameter('idDepartamento', $arrayParametros['departamento']);
                        $ntvQueryCount->setParameter('idDepartamento', $arrayParametros['departamento']);

                    }
                }
            }
            
            if(isset($arrayParametros['canton']) )
            {
                if($arrayParametros['canton'])
                {
                    if($arrayParametros['canton']!="Todos")
                    {
                        $strWhere .= 'AND og.CANTON_ID = :idCanton ';        
                        $ntvQuery->setParameter('idCanton', $arrayParametros['canton']);
                        $ntvQueryCount->setParameter('idCanton', $arrayParametros['canton']);
                    }
                } 
            }
            
            if(isset($arrayParametros["tipoDocumentoGeneralId"]))
            {
                if($arrayParametros['tipoDocumentoGeneralId'])
                {
                    $strWhere  .=    " AND idc.TIPO_DOCUMENTO_GENERAL_ID = :tipoDocumentoGeneralId ";
                    $ntvQuery->setParameter('tipoDocumentoGeneralId', $arrayParametros["tipoDocumentoGeneralId"]);
                    $ntvQueryCount->setParameter('tipoDocumentoGeneralId', $arrayParametros["tipoDocumentoGeneralId"]);
                }
            }
            
            $ntvQuery->setParameter('estado', 'Activo');
            $ntvQueryCount->setParameter('estado', 'Activo');
            
            $ntvQuery->setParameter('descripcionTipoRol', 'Empleado');
            $ntvQueryCount->setParameter('descripcionTipoRol', 'Empleado');
            
            $ntvQuery->setParameter('empresaCod', $arrayParametros["codEmpresa"]);
            $ntvQueryCount->setParameter('empresaCod', $arrayParametros["codEmpresa"]);
            
            $strOrderBy=" ORDER BY p.NOMBRES, p.APELLIDOS ";
            
            $strSqlPrincipal = $strSelect.$strFrom.$strWhere.$strOrderBy;

            $strSqlFinal = '';

            if(isset($arrayParametros['intStart']) && isset($arrayParametros['intLimit']))
            {
                if($arrayParametros['intStart'] && $arrayParametros['intLimit'])
                {
                    $intInicio = $arrayParametros['intStart'];
                    $intFin = $arrayParametros['intStart'] + $arrayParametros['intLimit'];
                    $strSqlFinal = '  SELECT * FROM 
                                        (
                                            SELECT consultaPrincipal.*,rownum AS consultaPrincipal_rownum 
                                            FROM (' . $strSqlPrincipal . ') consultaPrincipal 
                                            WHERE rownum<=' . $intFin . '
                                        ) WHERE consultaPrincipal_rownum >' . $intInicio;
                }
                else
                {
                    $strSqlFinal = '  SELECT consultaPrincipal.* 
                                        FROM (' . $strSqlPrincipal . ') consultaPrincipal 
                                        WHERE rownum<=' . $arrayParametros['intLimit'];
                }
            }
            else
            {
                $strSqlFinal = $strSqlPrincipal;
            }

            $ntvQuery->setSQL($strSqlFinal);
            $arrayResultado = $ntvQuery->getResult();

            $strSqlCount = $strSelectCount . " FROM (" . $strSqlPrincipal . ")";
            $ntvQueryCount->setSQL($strSqlCount);

            $intTotal = $ntvQueryCount->getSingleScalarResult();


            $arrayRespuesta['resultado'] = $arrayResultado;
            $arrayRespuesta['total'] = $intTotal;
            
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
        }
        return $arrayRespuesta;
    }
    

    /**
     * getJSONFirmaEmpleados
     *
     * Método que consulta las firmas de los empleados                              
     *
     * @param array $arrayParametros [  "esJefe"                : string que identifica si la persona en sesión es o no jefe
     *                                  "region"                : region a la que pertenece el canton del empleado en sesión
     *                                  "nombres"               : nombres del empleado
     *                                  "apellidos"             : apellidos del empleado
     *                                  "identificacion"        : identificacion del empleado
     *                                  "departamento"          : departamento del empleado
     *                                  "canton"                : canton a la que pertenece el empleado
     *                                  "tipoDocumentoGeneralId": id del tipo de documento general
     *                                  "empresaCod"            : id de la empresa
     *                               ]
     * 
     * @return array $datos
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 08-07-2016
     * 
     */
    public function getJSONFirmaEmpleados($arrayParametros)
    {
        $arrayEncontrados       = array();
        $arrayResultado         = $this->getResultadoFirmaEmpleados($arrayParametros);
        $arrayRegistros         = $arrayResultado['registros'];
        $intTotal               = $arrayResultado['total'];

        if( $arrayRegistros )
        {
            foreach( $arrayRegistros as $arrayRegistro )
            {
                
                $arrayEncontrados[] = array(
                                            "idPersonaEmpresaRol"   => $arrayRegistro["ID_PERSONA_EMPRESA_ROL"],
                                            "idPersona"             => $arrayRegistro["ID_PERSONA"],
                                            "identificacionCliente" => $arrayRegistro["IDENTIFICACION_CLIENTE"],
                                            "nombres"               => $arrayRegistro["NOMBRES"],
                                            "apellidos"             => $arrayRegistro["APELLIDOS"],
                                            "idDocumentoRelacion"   => $arrayRegistro["ID_DOCUMENTO_RELACION"],
                                            "idDocumento"           => $arrayRegistro["ID_DOCUMENTO"],
                                            "ubicacionFisicaDoc"    => $arrayRegistro["UBICACION_FISICA_DOCUMENTO"],
                                            "ubicacionLogicaDoc"    => $arrayRegistro["UBICACION_LOGICA_DOCUMENTO"]
                                        );
            }
        }

        $arrayRespuesta = array('total' => $intTotal, 'encontrados' => $arrayEncontrados);
        $jsonData       = json_encode($arrayRespuesta);

        return $jsonData;
    }
    
    /**
     * getJSONDocumentosElemento, Obtiene el json de los documentos de un elemento 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 
     * 
     * @param  array $arrayParametros[
     *                                  "idElemento"                : id del vehículo
     *                                  "tipoDocumentoGeneralId"    : id del tipo de documento general
     *                                  "intStart"                  : inicio del rownum
     *                                  "intLimit"                  : fin del rownum
     *                               ]
     * 
     * 
     * @return json $jsonData
     */
    public function getJSONDocumentosElemento($arrayParametros)
    {
        $arrayEncontrados = array();
        $arrayResultado = $this->getResultadoDocumentosElemento($arrayParametros);
        $resultado = $arrayResultado['resultado'];
        $intTotal = $arrayResultado['total'];
        $total = 0;

        if($resultado)
        {
            $total = $intTotal;
            foreach($resultado as $data)
            {
                $arrayEncontrados[] = array(
                                            "idDocumentoRelacion"       => $data['idDocumentoRelacion'],
                                            "idElemento"                => $data['idElemento'],
                                            "idDocumento"               => $data['idDocumento'],
                                            "ubicacionLogicaDocumento"  => $data['ubicacionLogicaDocumento'],
                                            "ubicacionFisicaDocumento"  => $data['ubicacionFisicaDocumento'],
                                            "usrCreacion"               => $data['usrCreacion'],
                                            "feCreacion"                => $data["feCreacion"] ? 
                                                                           strval(date_format($data["feCreacion"],"d-m-Y H:i")):""
                                        );
            }
        }

        $arrayRespuesta = array('total' => $total, 'encontrados' => $arrayEncontrados);
        $jsonData = json_encode($arrayRespuesta);
        return $jsonData;
    }
    
    /**
     * getResultadoDocumentosElemento
     * 
     * Obtiene los documentos de un elemento 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 02-08-2016
     * 
     * @param  array $arrayParametros
     * 
     * @return json $arrayRespuesta
     */
    public function getResultadoDocumentosElemento($arrayParametros)
    {

        $arrayRespuesta['total']     = 0;
        $arrayRespuesta['resultado'] = "";

        try
        {
            $rsmCount = new ResultSetMappingBuilder($this->_em);
            $ntvQueryCount = $this->_em->createNativeQuery(null, $rsmCount);
            
            $strSelectCount = " SELECT COUNT(*) AS TOTAL ";
            
            $rsm = new ResultSetMappingBuilder($this->_em);
            $ntvQuery = $this->_em->createNativeQuery(null, $rsm);
            
            $strSelect = " SELECT idr.ID_DOCUMENTO_RELACION, idc.ID_DOCUMENTO, elemento.ID_ELEMENTO, "
                        . " idc.UBICACION_FISICA_DOCUMENTO, idc.UBICACION_LOGICA_DOCUMENTO,idc.USR_CREACION,idc.FE_CREACION ";
            
            $strFrom =" FROM
                        DB_INFRAESTRUCTURA.INFO_ELEMENTO elemento  
                        INNER JOIN DB_COMUNICACION.INFO_DOCUMENTO_RELACION idr ON elemento.ID_ELEMENTO = idr.ELEMENTO_ID
                        INNER JOIN DB_COMUNICACION.INFO_DOCUMENTO idc ON idc.ID_DOCUMENTO = idr.DOCUMENTO_ID 
                        INNER JOIN DB_GENERAL.ADMI_TIPO_DOCUMENTO_GENERAL atdg ON atdg.ID_TIPO_DOCUMENTO = idc.TIPO_DOCUMENTO_GENERAL_ID
                        WHERE elemento.ESTADO= :estado ";
            
            $rsm->addScalarResult('ID_DOCUMENTO_RELACION','idDocumentoRelacion','integer');
            $rsm->addScalarResult('ID_ELEMENTO','idElemento','integer');
            $rsm->addScalarResult('ID_DOCUMENTO','idDocumento','integer');
            $rsm->addScalarResult('UBICACION_FISICA_DOCUMENTO', 'ubicacionFisicaDocumento','string');
            $rsm->addScalarResult('UBICACION_LOGICA_DOCUMENTO', 'ubicacionLogicaDocumento','string');
            $rsm->addScalarResult('USR_CREACION', 'usrCreacion','string');
            $rsm->addScalarResult('FE_CREACION', 'feCreacion','datetime');
            
            
            $rsmCount->addScalarResult('TOTAL','TOTAL','integer');
            
            
            $strWhere = "";
            
            
            if(isset($arrayParametros['idElemento']) )
            {
                if($arrayParametros['idElemento'])
                {
                    $strWhere .= 'AND idr.ELEMENTO_ID = :idElemento ';        
                    $ntvQuery->setParameter('idElemento', $arrayParametros['idElemento']);
                    $ntvQueryCount->setParameter('idElemento', $arrayParametros['idElemento']);
                }
            }
            
            if(isset($arrayParametros["tipoDocumentoGeneralId"]))
            {
                if($arrayParametros['tipoDocumentoGeneralId'])
                {
                    $strWhere  .=    " AND idc.TIPO_DOCUMENTO_GENERAL_ID = :tipoDocumentoGeneralId ";
                    $ntvQuery->setParameter('tipoDocumentoGeneralId', $arrayParametros["tipoDocumentoGeneralId"]);
                    $ntvQueryCount->setParameter('tipoDocumentoGeneralId', $arrayParametros["tipoDocumentoGeneralId"]);
                }
            }
            
            $ntvQuery->setParameter('estado', 'Activo');
            $ntvQueryCount->setParameter('estado', 'Activo');
            
            $strOrderBy=" ORDER BY idc.ID_DOCUMENTO ASC ";
            
            $strSqlPrincipal = $strSelect.$strFrom.$strWhere.$strOrderBy;

            $strSqlFinal = '';

            if(isset($arrayParametros['intStart']) && isset($arrayParametros['intLimit']))
            {
                if($arrayParametros['intStart'] && $arrayParametros['intLimit'])
                {
                    $intInicio = $arrayParametros['intStart'];
                    $intFin = $arrayParametros['intStart'] + $arrayParametros['intLimit'];
                    $strSqlFinal = '  SELECT * FROM 
                                        (
                                            SELECT consultaPrincipal.*,rownum AS consultaPrincipal_rownum 
                                            FROM (' . $strSqlPrincipal . ') consultaPrincipal 
                                            WHERE rownum<=' . $intFin . '
                                        ) WHERE consultaPrincipal_rownum >' . $intInicio;
                }
                else
                {
                    $strSqlFinal = '  SELECT consultaPrincipal.* 
                                        FROM (' . $strSqlPrincipal . ') consultaPrincipal 
                                        WHERE rownum<=' . $arrayParametros['intLimit'];
                }
            }
            else
            {
                $strSqlFinal = $strSqlPrincipal;
            }

            $ntvQuery->setSQL($strSqlFinal);
            $arrayResultado = $ntvQuery->getResult();

            $strSqlCount = $strSelectCount . " FROM (" . $strSqlPrincipal . ")";
            $ntvQueryCount->setSQL($strSqlCount);
            
            $intTotal = $ntvQueryCount->getSingleScalarResult();


            $arrayRespuesta['resultado'] = $arrayResultado;
            $arrayRespuesta['total'] = $intTotal;
            
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
        }
        return $arrayRespuesta;
    }
     
    /**
     * getResultadoFotosAntesDespues
     * 
     * Obtiene las fotos tomadas en validación antes y despues 
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.0 06-11-2019
     * 
     * cost 7
     * 
     * @param  array $arrayParametros
     * 
     * @return array $arrayRespuesta
     */
    public function getResultadoFotosAntesDespues($arrayParametros)
    {
        try
        {
            $arrayRespuesta['total']     = 0;
            $arrayRespuesta['resultado'] = "";
            $strNombreDocumento = $arrayParametros['nombreDocumento'];
            $strUsuario         = $arrayParametros['usuario'];

            $objCount       = new ResultSetMappingBuilder($this->_em);
            $objQueryCount  = $this->_em->createNativeQuery(null, $objCount);
            
            $objGeneral            = new ResultSetMappingBuilder($this->_em);
            $objQuery       = $this->_em->createNativeQuery(null, $objGeneral);

            $strSelectCount = " SELECT COUNT(*) AS TOTAL ";

            $strSelect  = " SELECT INFD.ID_DOCUMENTO, 
                                    INFD.NOMBRE_DOCUMENTO, 
                                    INFD.UBICACION_FISICA_DOCUMENTO, 
                                    INFDR.ID_DOCUMENTO_RELACION,
                                    INFDR.ESTADO_EVALUACION,
                                    INFDR.EVALUACION_TRABAJO ";
            $strFrom    = " FROM DB_COMUNICACION.INFO_DOCUMENTO INFD, 
                                    DB_COMUNICACION.INFO_DOCUMENTO_RELACION INFDR ";
            $strWhere   = " WHERE INFD.NOMBRE_DOCUMENTO LIKE :nombreDocumento
                                    AND INFD.USR_CREACION  = :usuario
                                    AND INFDR.DOCUMENTO_ID = INFD.ID_DOCUMENTO ";

            if(!empty($strNombreDocumento))
            {
                $strNombreDocumento = $strNombreDocumento.'%';
            }

            $objQuery->setParameter('nombreDocumento',      $strNombreDocumento);
            $objQueryCount->setParameter('nombreDocumento', $strNombreDocumento);

            $objQuery->setParameter('usuario',      $strUsuario);
            $objQueryCount->setParameter('usuario', $strUsuario);

            $objGeneral->addScalarResult('ID_DOCUMENTO',               'idDocumento',              'integer');
            $objGeneral->addScalarResult('ID_DOCUMENTO_RELACION',      'idDocumentoRelacion',      'integer');
            $objGeneral->addScalarResult('NOMBRE_DOCUMENTO',           'nombreDocumento',          'string');
            $objGeneral->addScalarResult('UBICACION_FISICA_DOCUMENTO', 'ubicacionFisicaDocumento', 'string');
            $objGeneral->addScalarResult('ESTADO_EVALUACION',          'estadoEvaluacion',         'string');
            $objGeneral->addScalarResult('EVALUACION_TRABAJO',         'evaluacionTrabajo',        'string');

            $objCount->addScalarResult('TOTAL','total','integer');

            $strOrderBy = " ORDER BY INFD.ID_DOCUMENTO DESC ";

            $strSqlPrincipal = $strSelect.$strFrom.$strWhere.$strOrderBy;
            $objQuery->setSQL($strSqlPrincipal);
            $arrayResultado = $objQuery->getResult();

            $strSqlCount = $strSelectCount . " FROM (" . $strSqlPrincipal . ")";
            $objQueryCount->setSQL($strSqlCount);
            $intTotal = $objQueryCount->getSingleScalarResult();
        }
        catch(\Exception $e)
        {
            $arrayRespuesta['status']       = 'ERROR';
            $arrayRespuesta['resultado']    = 'Error al realizar consulta: ' . $e->getMessage();
            return $arrayRespuesta;
        }

        $arrayRespuesta['status']       = 'OK';
        $arrayRespuesta['resultado']    = $arrayResultado;
        $arrayRespuesta['total']        = $intTotal; 

        return $arrayRespuesta;
    }

    /**
     * getFotosPorTareaUsuarioAntesDespues
     * 
     * Obtiene las fotos totales tomadas en validación antes y despues 
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.0 27-11-2019
     * 
     * cost 15
     * 
     * @param  array $arrayParametros
     * 
     * @return array $arrayRespuesta
     */
    public function getFotosPorTareaUsuarioAntesDespues($arrayParametros)
    {
        try
        {
            $arrayRespuesta['resultado']    = "";
            $intIdDetalle                   = $arrayParametros['idDetalle'];
            $strUsuario                     = $arrayParametros['usuario'];
            $strCronologiaFotoDespues       = $arrayParametros['cronologiaFotoDespues'];
            $strCronologiaFotoAntes         = $arrayParametros['cronologiaFotoAntes'];
            $strNombreElemento              = $arrayParametros['nombreElemento'];
            
            $objGeneral     = new ResultSetMappingBuilder($this->_em);
            $objQuery       = $this->_em->createNativeQuery(null, $objGeneral);

            $strSelect  = " SELECT DATOS.ID_DOCUMENTO, 
                                   SUBSTR(INFD2.NOMBRE_DOCUMENTO, (INSTR(INFD2.NOMBRE_DOCUMENTO, '_', -1)+1)) AS INTENTOS,
                                   INFD2.NOMBRE_DOCUMENTO,
                                   INFD2.UBICACION_FISICA_DOCUMENTO, 
                                   INFD2.ETIQUETA_DOCUMENTO,
                                   INFDR2.ESTADO_EVALUACION,
                                   INFDR2.EVALUACION_TRABAJO
                            FROM(                             
                                    SELECT SUBSTR(INFD.NOMBRE_DOCUMENTO, 0, INSTR(INFD.NOMBRE_DOCUMENTO, '_', 1, 2)) AS NOMBRE_FOTO, 
                                            MAX(INFD.ID_DOCUMENTO) AS ID_DOCUMENTO
                                    FROM  DB_COMUNICACION.INFO_DOCUMENTO INFD, DB_COMUNICACION.INFO_DOCUMENTO_RELACION INFDR ";

            $strWhere = " WHERE INFDR.DOCUMENTO_ID    = INFD.ID_DOCUMENTO
                            AND INFDR.DETALLE_ID      = :idDetalle
                            AND INFD.USR_CREACION     = :usuario
                            AND INFDR.PORCENTAJE_EVALUACION_BASE IS NOT NULL ";

            if(!empty($strNombreElemento))
            {
                $strWhere .= " AND INFD.ETIQUETA_DOCUMENTO = :nombreElemento ";
                $objQuery->setParameter('nombreElemento',   $strNombreElemento);
            }


            $strGroupOrderBy = " GROUP BY SUBSTR(INFD.NOMBRE_DOCUMENTO, 0, INSTR(INFD.NOMBRE_DOCUMENTO, '_', 1, 2))
                                    ORDER BY ID_DOCUMENTO DESC ";

            $strOthers = " ) DATOS
            LEFT JOIN DB_COMUNICACION.INFO_DOCUMENTO INFD2 ON DATOS.ID_DOCUMENTO = INFD2.ID_DOCUMENTO
            LEFT JOIN DB_COMUNICACION.INFO_DOCUMENTO_RELACION INFDR2 ON DATOS.ID_DOCUMENTO = INFDR2.DOCUMENTO_ID ";

            $objQuery->setParameter('idDetalle',        $intIdDetalle);
            $objQuery->setParameter('usuario',          $strUsuario);

            $objGeneral->addScalarResult('ID_DOCUMENTO',                'idDocumento',              'integer');
            $objGeneral->addScalarResult('INTENTOS',                    'intentos',                 'integer');
            $objGeneral->addScalarResult('NOMBRE_DOCUMENTO',            'nombreDocumento',          'string');
            $objGeneral->addScalarResult('UBICACION_FISICA_DOCUMENTO',  'ubicacionFisicaDocumento', 'string');
            $objGeneral->addScalarResult('ETIQUETA_DOCUMENTO',          'etiquetaDocumento',        'string');
            $objGeneral->addScalarResult('ESTADO_EVALUACION',           'estadoEvaluacion',         'string');
            $objGeneral->addScalarResult('EVALUACION_TRABAJO',          'evaluacionTrabajo',        'string');

            $objQuery->setSQL($strSelect .$strWhere .$strGroupOrderBy .$strOthers);
            $arrayResultado = $objQuery->getArrayResult();

            $arrayFinish    = [];

            if(!empty($arrayResultado))
            {
                for ($intPosicion = 0; $intPosicion < count($arrayResultado); $intPosicion++)
                {
                    $arrayData[$arrayResultado[$intPosicion]['etiquetaDocumento']][]=
                    array(
                            'nombreDocumento'   => $arrayResultado[$intPosicion]['nombreDocumento']             
                                                    ? $arrayResultado[$intPosicion]['nombreDocumento'] : "",
                            'ubicacionFoto'     => $arrayResultado[$intPosicion]['ubicacionFisicaDocumento']    
                                                    ? $arrayResultado[$intPosicion]['ubicacionFisicaDocumento'] : "",
                            'intentos'          => $arrayResultado[$intPosicion]['intentos']                    
                                                    ? $arrayResultado[$intPosicion]['intentos'] : "",
                            'estadoEvaluacion'  => $arrayResultado[$intPosicion]['estadoEvaluacion']            
                                                    ? $arrayResultado[$intPosicion]['estadoEvaluacion'] : "",
                            'evaluacionTrabajo' => $arrayResultado[$intPosicion]['evaluacionTrabajo']           
                                                    ? $arrayResultado[$intPosicion]['evaluacionTrabajo'] : ""
                         );
                }

                foreach($arrayData as $strEtiqueta=>$arrayItem)
                {
                    $arrayChildrens = [];
                    
                    foreach($arrayItem as $arrayValue)
                    {
                        if(strpos($arrayValue['nombreDocumento'], $strCronologiaFotoDespues))
                        {
                            $arrayChildrens['ubicacionFotoDespues'] = $arrayValue['ubicacionFoto'];
                            $arrayChildrens['intentos']             = $arrayValue['intentos'];
                            $arrayChildrens['estadoEvaluacion']     = $arrayValue['estadoEvaluacion'];
                            $arrayChildrens['evaluacionTrabajo']    = $arrayValue['evaluacionTrabajo'];
                        }

                        if(strpos($arrayValue['nombreDocumento'], $strCronologiaFotoAntes))
                        {
                            $arrayChildrens['ubicacionFotoAntes'] = $arrayValue['ubicacionFoto'];
                        }
                    }
                    
                    $arrayChildrens['nombreElemento'] = $strEtiqueta;

                    $arrayFinish[] = $arrayChildrens;
                }
            }

        }
        catch(\Exception $e)
        {
            $arrayRespuesta['status']       = 'ERROR';
            $arrayRespuesta['resultado']    = 'Error al realizar consulta: ' . $e->getMessage();
            return $arrayRespuesta;
        }

        $arrayRespuesta['status']       = 'OK';
        $arrayRespuesta['resultado']    = $arrayFinish;

        return $arrayRespuesta;
    }

    /**
     * getDocumentosDigitalesATransferir
     *
     * Obtiene los documentos digitales a enviar a Security Data
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.0 16-07-2020
     *
     * cost 9
     *
     * @param  array $arrayParametros
     *
     * @return array $arrayRespuesta
     */
    public function getDocumentosDigitalesATransferir($arrayParametros)
    {
        try
        {
            $arrayRespuesta['total']     = 0;
            $arrayRespuesta['resultado'] = "";
            
            $intIdContrato = $arrayParametros['intIdContrato'];
            $strNumAdendum = is_null($arrayParametros['strNumeroAdendum']) ? '' : $arrayParametros['strNumeroAdendum'];

            $objCount       = new ResultSetMappingBuilder($this->_em);
            $objQueryCount  = $this->_em->createNativeQuery(null, $objCount);

            $objGeneral            = new ResultSetMappingBuilder($this->_em);
            $objQuery       = $this->_em->createNativeQuery(null, $objGeneral);

            $strSelectCount = " SELECT COUNT(*) AS TOTAL ";

            $strSelect  = " SELECT idoc.ID_DOCUMENTO, idoc.TIPO_DOCUMENTO_GENERAL_ID, idoc.TIPO_DOCUMENTO_ID, idoc.CONTRATO_ID,
                                   idor.NUMERO_ADENDUM, idoc.FE_CREACION, idoc.NOMBRE_DOCUMENTO, idoc.UBICACION_FISICA_DOCUMENTO  ";
            $strFrom    = " FROM DB_COMUNICACION.INFO_DOCUMENTO idoc,
                                 DB_COMUNICACION.INFO_DOCUMENTO_RELACION idor ";
            $strWhere   = " WHERE idoc.CONTRATO_ID = :idContrato
                                  AND idor.CONTRATO_ID = idoc.CONTRATO_ID
                                  AND ((idoc.TIPO_DOCUMENTO_ID = 13 
                                        AND (idoc.TIPO_DOCUMENTO_GENERAL_ID = 1 
                                            OR (idoc.TIPO_DOCUMENTO_GENERAL_ID = 3 
                                                and lower(idoc.NOMBRE_DOCUMENTO) like'%security%')))
                                        OR (idoc.TIPO_DOCUMENTO_GENERAL_ID = 134 AND idoc.TIPO_DOCUMENTO_ID IN (10,11)))
                                  AND idor.DOCUMENTO_ID = idoc.ID_DOCUMENTO
                                  AND ((idor.NUMERO_ADENDUM IS NULL and :numAdendum is null) 
                                  OR (:numAdendum is not null and idor.NUMERO_ADENDUM = :numAdendum)) ";

            $objQuery->setParameter('idContrato',      $intIdContrato, 'integer');
            $objQueryCount->setParameter('idContrato', $intIdContrato, 'integer');

            $objQuery->setParameter('numAdendum',      $strNumAdendum, 'string');
            $objQueryCount->setParameter('numAdendum', $strNumAdendum, 'string');

            $objGeneral->addScalarResult('ID_DOCUMENTO',              'idDocumento',            'integer');
            $objGeneral->addScalarResult('TIPO_DOCUMENTO_GENERAL_ID', 'tipoDocumentoGeneralId', 'integer');
            $objGeneral->addScalarResult('TIPO_DOCUMENTO_ID',         'tipoDocumentoId',        'integer');
            $objGeneral->addScalarResult('CONTRATO_ID',               'contratoId',             'integer');
            $objGeneral->addScalarResult('FE_CREACION',               'feCreacion',             'datetime');
            $objGeneral->addScalarResult('NOMBRE_DOCUMENTO',          'nombreDocumento',        'string');
            $objGeneral->addScalarResult('UBICACION_FISICA_DOCUMENTO','ubiacionFisicaDocumento','string');

            $objCount->addScalarResult('TOTAL','total','integer');

            $strOrderBy = " ORDER BY idoc.ID_DOCUMENTO DESC ";

            $strSqlPrincipal = $strSelect.$strFrom.$strWhere.$strOrderBy;
            $objQuery->setSQL($strSqlPrincipal);
            $arrayResultado = $objQuery->getResult();

            $strSqlCount = $strSelectCount . " FROM (" . $strSqlPrincipal . ")";
            $objQueryCount->setSQL($strSqlCount);
            $intTotal = $objQueryCount->getSingleScalarResult();
        }
        catch(\Exception $e)
        {
            $arrayRespuesta['status']       = 'ERROR';
            $arrayRespuesta['resultado']    = 'Error al realizar consulta: ' . $e->getMessage();
            return $arrayRespuesta;
        }

        $arrayRespuesta['status']       = 'OK';
        $arrayRespuesta['resultado']    = $arrayResultado;
        $arrayRespuesta['total']        = $intTotal;

        return $arrayRespuesta;
    }

    /**
     * Función encargada de obtener informacion de las imágenes registradas para soporte
     *
     * @author David De La Cruz <ddelacruz@telconet.ec>
     * @version 1.0 25-02-2022
     *
     * @param  Array $arrayParametros
     * @return String $arrayRespuesta;
     */
    public function getImagenesPorCriterios($arrayParametros)
    {
        $objContainer = $arrayParametros['objContainer'];
        $strUserComunicacion = $objContainer->getParameter('user_comunicacion');
        $strPassComunicacion = $objContainer->getParameter('passwd_comunicacion');
        $strDsn = $objContainer->getParameter('database_dsn');
        $arrayParametros['objContainer'] = '';
        $intTotalRegistros = 0;
        $arrayResponse = array();
        $arrayImagenes = array();
        try
        {
            $strSql = "BEGIN ".
                      "DB_COMUNICACION.CUKG_CONSULTS.P_GET_IMAGENES_POR_CRITERIOS(Pcl_Request => :Pcl_Request,".
                                                                                  "Pv_Status  => :Pv_Status,".
                                                                                  "Pv_Mensaje => :Pv_Mensaje,".
                                                                                  "Pcl_Response => :Pcl_Response,".
                                                                                  "Pn_TotalRegistros => :Pn_TotalRegistros); ".
                      "END;";

            $objConexion = oci_connect($strUserComunicacion,$strPassComunicacion,$strDsn,'AL32UTF8');
            $objStmt     = oci_parse($objConexion,$strSql);
            $strResponse = oci_new_descriptor($objConexion, OCI_D_LOB);

            oci_bind_by_name($objStmt,':Pcl_Request' ,json_encode($arrayParametros));
            oci_bind_by_name($objStmt,':Pv_Status'   ,$strStatus,50);
            oci_bind_by_name($objStmt,':Pv_Mensaje'  ,$strMensaje,4000);
            oci_bind_by_name($objStmt,':Pcl_Response', $strResponse, -1, OCI_B_CLOB);
            oci_bind_by_name($objStmt,':Pn_TotalRegistros'  ,$intTotalRegistros,5);
           
            oci_execute($objStmt);

            $arrayJson = explode('|',substr(trim($strResponse->load()),0, -1));
            
            if ($arrayJson[0] !== "") 
            {
                foreach( $arrayJson as $strJson)
                {
                    $arrayImagenes[] = (array) json_decode($strJson,true);
                }
            }
            
        }
        catch (\Exception $ex)
        {
            $arrayResponse['arrayImagenes'] = [];
        }
        $arrayResponse['intTotal'] = ($intTotalRegistros*1);
        $arrayResponse['arrayImagenes'] = $arrayImagenes;
        return $arrayResponse;
    }  
    /**
     * getDocumentosByContrato
     *
     * Metodo encargado de obtener los documentos guardados de acuerdo a los
     * criterios ingresados por el usuario.
     *
     * @param array $arrayParametros[ 'strDescripcionTipoDocumento'     => 'Descripcion del tipo de documento',
     *                                'intContrato'                     => 'Id del contrato' ]
     *        
     * @return array $arrayResultados[ 'registros' => 'Información consultada',
     *                                 'total'     => 'Cantidad de registros consultados' ]
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.0 05-09-2022
     *
     * Costo del Query: 8
     */  
    public function getDocumentosByContrato( $arrayParametros )
    {
        $arrayResultados = array();
        
        $objQuery      = $this->_em->createQuery();
        $objQueryCount = $this->_em->createQuery();
        
        $strSelect      = "SELECT DISTINCT ifdr.id ";
        $strSelectCount = "SELECT COUNT(DISTINCT ifdr.id) ";
        
        $strFrom    = "FROM schemaBundle:InfoDocumento ifd,
                            schemaBundle:InfoDocumentoRelacion ifdr,
                            schemaBundle:AdmiTipoDocumentoGeneral atdg ";
        
        $strWhere   = "WHERE ifd.id = ifdr.documentoId
                         AND ifd.tipoDocumentoGeneralId = atdg.id ";

        if( isset($arrayParametros['intContratoId']) && !empty($arrayParametros['intContratoId']) )
        {
            $strWhere .= "AND ifdr.contratoId = :intContratoId ";

            $objQuery->setParameter("intContratoId",      $arrayParametros['intContratoId']);
            $objQueryCount->setParameter("intContratoId", $arrayParametros['intContratoId']);
        }

        if( isset($arrayParametros['strDescripcionTipoDocumento']) && !empty($arrayParametros['strDescripcionTipoDocumento']) )
        {
            $strWhere .= "AND atdg.descripcionTipoDocumento = :strDescripcionTipoDocumento ";

            $objQuery->setParameter("strDescripcionTipoDocumento",      $arrayParametros['strDescripcionTipoDocumento']);
            $objQueryCount->setParameter("strDescripcionTipoDocumento", $arrayParametros['strDescripcionTipoDocumento']);
        }

        $strDql      = $strSelect.$strFrom.$strWhere;
        $strDqlCount = $strSelectCount.$strFrom.$strWhere;
        
        $objQuery->setDQL($strDql);
        $objQueryCount->setDQL($strDqlCount);

        if( isset($arrayParametros['inicio']) && $arrayParametros['inicio'])
        {
                $objQuery->setFirstResult($arrayParametros['inicio']);
        }
        
        if( isset($arrayParametros['limite']) && $arrayParametros['limite'] )
        {
             $objQuery->setMaxResults($arrayParametros['limite']);    
        }
            
        $arrayResultados['registros'] = $objQuery->getResult();
        $arrayResultados['total']     = $objQueryCount->getSingleScalarResult();
        
        return $arrayResultados;
        
    }
}

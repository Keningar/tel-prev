<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository; 
use Doctrine\ORM\QueryBuilder;

class AdmiTipoDocumentoRepository extends EntityRepository
{
    public function generarJsonEntidades($nombre,$estado,$start,$limit)
    {
        $arr_encontrados = array();
        
        $entidadesTotal = $this->getEntidades($nombre, $estado, '', '');
        
        
        $entidades = $this->getEntidades($nombre, $estado, $start, $limit);
 
        if ($entidades) {
            
            $num = count($entidadesTotal);
            
            foreach ($entidades as $entidad)
            {
                $arr_encontrados[]=array('id_tipo_documento' =>$entidad->getId(),
                                         'extension_tipo_documento' =>trim($entidad->getExtensionTipoDocumento()),
                                         'estado' =>(trim($entidad->getEstado())=='Eliminado' ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (trim($entidad->getEstado())=='Eliminado' ? 'icon-invisible':'button-grid-edit'),
                                         'action3' => (trim($entidad->getEstado())=='Eliminado' ? 'icon-invisible':'button-grid-delete'));
            }

            if($num == 0)
            {
               $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_tipo_documento' => 0 , 'nombre_tipo_comunicacion' => 'Ninguno','tipo_comunicacion_id' => 0 , 'tipo_comunicacion_nombre' => 'Ninguno', 'estado' => 'Ninguno'));
                $resultado = json_encode( $resultado);

                return $resultado;
            }
            else
            {
                $data=json_encode($arr_encontrados);
                $resultado= '{"total":"'.$num.'","encontrados":'.$data.'}';

                return $resultado;
            }
        }
        else
        {
            $resultado= '{"total":"0","encontrados":[]}';

            return $resultado;
        }
        
    }
    public function getEntidades($nombre,$estado,$start,$limit){
        $qb = $this->_em->createQueryBuilder();
            $qb->select('e')
               ->from('schemaBundle:AdmiTipoDocumento','e');
               
            
        if($nombre!=""){
            $qb ->where( 'LOWER(e.extensionTipoDocumento) like LOWER(?1)');
            $qb->setParameter(1, '%'.$nombre.'%');
        }
        
        if($estado!="Todos"){
            $boolBusqueda = true;
            if($estado=="Activo"){
                $qb ->andWhere("LOWER(e.estado) not like LOWER('Eliminado')");
            }
            else{
                $qb ->andWhere('LOWER(e.estado) = LOWER(?2)');
                $qb->setParameter(2, $estado);
            }
        }
        
        if($start!='')
            $qb->setFirstResult($start);   
        if($limit!='')
            $qb->setMaxResults($limit);
        
        $qb->orderBy('e.id');
        $query = $qb->getQuery();
        
        return $query->getResult();
    }
    
    /**
     * getAdmiTipoDocumentoByCriterios
     *
     * Metodo encargado de obtener los tipos de documentos por criterios enviados
     *
     * @param array  $arrayParametros [ 'estado', 'inicio', 'limite' ]
     *
     * @return $arrayResultados [ 'registros', 'total' ]
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 24-09-2015
     */
    public function getAdmiTipoDocumentoByCriterios( $arrayParametros )
    {
        $arrayResultados = array();
        
        $query      = $this->_em->createQuery();
        $queryCount = $this->_em->createQuery();
        
        $strSelect      = "SELECT atd ";
        $strSelectCount = "SELECT COUNT(atd) ";
        $strFrom        = "FROM schemaBundle:AdmiTipoDocumento atd ";
        $strWhere       = "WHERE atd.estado = :estado
                             AND atd.extensionTipoDocumento IS NOT NULL ";
        $strOrderBy     = "ORDER BY atd.extensionTipoDocumento ";
        
        if( isset($arrayParametros['estado']) )
        {
            if($arrayParametros['estado'])
            {
                $query->setParameter("estado", $arrayParametros['estado']);
                
                $queryCount->setParameter("estado", $arrayParametros['estado']);
            }
        }
        
        $strDql      = $strSelect.$strFrom.$strWhere.$strOrderBy;
        $strDqlCount = $strSelectCount.$strFrom.$strWhere;
        
        $query->setDQL($strDql);
        $queryCount->setDQL($strDqlCount);
        
        if( isset($arrayParametros['inicio']) )
        {
            if( $arrayParametros['inicio'] )
            {
                $query->setFirstResult($arrayParametros['inicio']);
            }
        }
        
        if( isset($arrayParametros['limite']) )
        {
            if( $arrayParametros['limite'] )
            {
                $query->setMaxResults($arrayParametros['limite']);
            }
        }
        
        $arrayResultados['registros'] = $query->getResult();
        $arrayResultados['total']     = $queryCount->getSingleScalarResult();
        
        return $arrayResultados;
    }

    /**
     * Documentación para la función 'putSubirArchivoGD'.
     *
     * Función encargada de subir los archivos al Gestor Documental.
     *
     * @param array $arrayParametros [
     *                                "strPrefijoEmpresa"     => Prefijo de la empresa.
     *                                "intCodEmpresa"         => Código de la empresa.
     *                                "strNombreDocumento"    => Nombre del documento.
     *                                "strLoginUsuario"       => Login de la usuario en sesión.
     *                                "strIpCreacion"         => IP del usuario en sesión.
     *                                "strUbicacionArchivo"   => Ubicación del archivo nfs.
     *                                "strTipoDocumento"      => Tipo del documento('ORDEN DE SERVICIO',
     *                                                                              'ADEMDUM',
     *                                                                              'ESCRITURA',
     *                                                                              'CONTRATO CLIENTE',
     *                                                                              'CÉDULA REPRESENTANTE',
     *                                                                              'NOMBRAMIENTO',
     *                                                                              'RUC',
     *                                                                              'CARTA DE COMPROMISO',
     *                                                                              'CÓDIGO DE CONDUCTA' ).
     *                                "strRazonSocial"        => Nombre del cliente.
     *                                "strRuc"                => Identificación del cliente.
     *                                "strNumContrato"        => Número de contrato.
     *                                "strDatabaseDsn"        => Configuración de la BD.
     *                                "strUserDocumental"     => Usuario.
     *                                "strPasswordDocumental" => Contraseña.
     *                               ]
     *
     * @return array $arrayResultado [
     *                                "message"  =>  Mensaje de respuesta.
     *                                "status"   =>  Estado de respuesta.
     *                               ]
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 03-09-2021
     *
     */
    public function putSubirArchivoGD($arrayParametros)
    {
        try
        {
            $strPrefijoEmpresa     = ( isset($arrayParametros['strPrefijoEmpresa']) && !empty($arrayParametros['strPrefijoEmpresa']) )
                                     ? $arrayParametros['strPrefijoEmpresa'] : null;
            $strDatabaseDsn        = ( isset($arrayParametros['strDatabaseDsn']) && !empty($arrayParametros['strDatabaseDsn']) )
                                     ? $arrayParametros['strDatabaseDsn'] : null;
            $strUserDocumental     = ( isset($arrayParametros['strUserDocumental']) && !empty($arrayParametros['strUserDocumental']) )
                                     ? $arrayParametros['strUserDocumental'] : null;
            $strPasswordDocumental = ( isset($arrayParametros['strPasswordDocumental']) && !empty($arrayParametros['strPasswordDocumental']) )
                                     ? $arrayParametros['strPasswordDocumental'] : null;
            $arrayRespuesta        = array();
            $strStatus             = "";
            $strMensaje            = "";
            if(!empty($strPrefijoEmpresa) && !empty($strDatabaseDsn) && !empty($strUserDocumental) && !empty($strPasswordDocumental))
            {
                $strSql = "BEGIN DB_DOCUMENTAL.GDKG_TRANSACCION.P_INSERT_INFO_DOCUMENTO(:PCL_EXTRA_PARAMS,
                                                                                        :PV_STATUS,
                                                                                        :PV_MENSAJE);
                                                                                        END;";
                $objOciConexion     = oci_connect($strUserDocumental, $strPasswordDocumental, $strDatabaseDsn);
                $objStmt            = oci_parse($objOciConexion, $strSql);
                $objExtraParamsClob = oci_new_descriptor($objOciConexion);
                $objExtraParamsClob->writetemporary(json_encode($arrayParametros, JSON_NUMERIC_CHECK));
                oci_bind_by_name($objStmt,':PCL_EXTRA_PARAMS', $objExtraParamsClob, -1, SQLT_CLOB);
                oci_bind_by_name($objStmt,':PV_STATUS', $strStatus, 32*1024, SQLT_CHR);
                oci_bind_by_name($objStmt,':PV_MENSAJE', $strMensaje, 32*1024, SQLT_CHR);
                if(!oci_execute($objStmt))
                {
                    $strOCIError = oci_error($objStmt);
                    $strMensaje  = trim($strMensaje);
                    if(empty($strMensaje))
                    {
                        throw new \Exception($strOCIError['message']);
                    }
                    throw new \Exception($strMensaje);
                }
                $arrayRespuesta['message']   = $strMensaje;
                $arrayRespuesta['status']    = $strStatus;
            }
            else
            {
                throw new \Exception("No se han enviado los parámetros obligatorios para subir el archivo.");
            }
        }
        catch(\Exception $e)
        {
            $arrayRespuesta['message'] = $ex->getMessage();
            $arrayRespuesta['status']  = "ERROR";
        }
        return $arrayRespuesta;
    }
}

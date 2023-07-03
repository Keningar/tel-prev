<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;

class InfoDocumentoComunMasivaRepository extends EntityRepository
{     

    /**
      * getEstadoEnviosCursor
      *
      * Método que devuele los envios realizados con su informacion de estados, envios, fechas, nombres
      *                                                    
      * @param array   $parametros                            
      * @param array   $conexion   
      * @param intger   $start   
      * @param intger   $limit   
      *
      * @return json con resultado
      *
      * @author Allan Suárez <arsuarez@telconet.ec>
      * @version 1.0 02-09-2014
      */      
    public function getEstadoEnvios($parametros, $conexion, $start = 0, $limit = 15)
    {
        $strSql = "BEGIN ESTADO_ENVIOS(:NOMBRE, :CLASE, :ESTADO_ENVIO, :TIPO, :EMPRESA , :Q_START, :Q_LIMIT, :TOTAL ,:ENVIOS); END;";
        
        $intTamanio = 0;
        
        if($limit > 0)
        {
            $limit =  $limit + $start;
            if($start > 0)
            {
                $start = $start + 1;
            }
        }

        try
        {
            /*
             *  Se usa la Funcion OCI nativa de PHP ya que el Doctrine no soporta CURSORES en los OUT parameters
             *  al momento de realizar los bind en el statement que ejecuta el procedure
             * 
             */
            //Se obtiene la conexion
            $rscCon = oci_connect($conexion['user'],$conexion['pass'], $conexion['dsn']) or $this->throw_exceptionOci(oci_error());
//
            //Prepara la sentencia                           
            $refCursor = oci_new_cursor($rscCon);
            $rscStmt = oci_parse($rscCon, $strSql) or $this->throw_exceptionOci(oci_error());

            oci_bind_by_name($rscStmt, ':NOMBRE', $parametros['nombre']);
            oci_bind_by_name($rscStmt, ':CLASE', $parametros['clase']);
            oci_bind_by_name($rscStmt, ':ESTADO_ENVIO', $parametros['estado']);
            oci_bind_by_name($rscStmt, ':TIPO', $parametros['tipo']);
            oci_bind_by_name($rscStmt, ':EMPRESA', $parametros['empresa']);
            oci_bind_by_name($rscStmt, ':Q_START', $start);
            oci_bind_by_name($rscStmt, ':Q_LIMIT', $limit);
            oci_bind_by_name($rscStmt, ':TOTAL',  $intTamanio , 10);
            oci_bind_by_name($rscStmt, ':ENVIOS', $refCursor, -1, OCI_B_CURSOR);
            oci_execute($rscStmt);

            oci_execute($refCursor); 
            
            $array = array();
            
            while(($row = oci_fetch_array($refCursor, OCI_ASSOC + OCI_RETURN_NULLS)) != false)
            {
                $array[] = array(
                    "id"          => $row['ID_DOC_COM_MASIVA'],
                    "idComun"     => $row['COMUNICACION_ID'],
                    "nombre"      => $row['NOMBRE_DOCUMENTO'],
                    "clase"       => $row['NOMBRE_CLASE_DOCUMENTO']!='Notificacion Externa SMS'?'Correo':'SMS',
                    "observacion" => $row['OBSERVACION'],
                    "estado"      => $row['ESTADO'],
                    "enviados"    => $row['ENVIADOS']?$row['ENVIADOS']:0,
                    "noEnviados"  => $row['NOENVIADOS']?$row['NOENVIADOS']:0,
                    "tipo"        => $row['TIPO_ENVIO'],                    
                    "feCreacion"  => $row['FE_CREACION'],
                    "usrCreacion" => $row['USR_CREACION'],
                    "feFinaliza"  => $row['FECHA_FINALIZACION'],
                    "isOcupado"   => $parametros['isOcupado'] 
                    
                );
            }

            oci_free_statement($rscStmt);
            oci_free_statement($refCursor);
            oci_close($rscCon);
            
            if($array)
            {
                
               $data = json_encode($array);
               $resultado = '{"total":"' . $intTamanio . '","encontrados":' . $data . '}';

               return $resultado;
            }
            else
            {
                $resultado = '{"total":"0","encontrados":[]}';
                return $resultado;
            }
        }
        catch(\Exception $ex)
        {
            throw new \Exception($ex->getMessage());
        }
    }

    /**
      * getComunicacionMasivaSinEnvio
      *
      * Método que devuele las notificaciones SMS/correo que no fueron enviadas por formas de contacto no validas o 
     *  inexistentes
      *                                                        
      * @param integer   $intComunicacion          
      *
      * @return array con resultado
      *
      * @author Allan Suárez <arsuarez@telconet.ec>
      * @version 1.0 02-09-2014
      */         
    public function getNotificacionesSinEnviar($intComunicacion)
    {
        $query = $this->_em->createQuery("
                        SELECT a
                        FROM 
                        schemaBundle:InfoDocumentoComunMasiva a,
                        schemaBundle:InfoDocumentoComunHistorial b
                        WHERE    
                        a.id               =  b.documComunMasivaId AND
                        a.comunicacionId   =  :comunicacion AND
                        b.estado           =  :estado");

        $query->setParameter('comunicacion', $intComunicacion);
        $query->setParameter('estado', 'No Enviado');

        $datos = $query->getResult();

        return $datos;
    }

    
    /**
      * getEstadoEquipo
      *
      * Método que devuele un valor S o N dependiendo si existe en algun envio realizado un estado 'S' que refiere a que el 
      * equipo está siendo utilizado en ese instante.      
      *                                                        
      * @param intger   $intComunicacion          
      *
      * @return array con resultado
      *
      * @author Allan Suárez <arsuarez@telconet.ec>
      * @version 1.0 02-09-2014
      */     
    public function getEstadoEquipo()
    {
        $query = $this->_em->createQuery("
                        SELECT a
                        FROM                         
                        schemaBundle:InfoDocumentoComunHistorial a
                        WHERE    
                        a.equipoOcupado  =  :estadoEquipo
                        ");
        
        $query->setParameter('estadoEquipo', 'S');
        
        $datos = $query->getResult();
        
        $intNumeroReg = count($datos);
        
        if($intNumeroReg>0)return 'S';
        else return 'N';
        
    }
    
    
     /**
      * getRegistroEquipoOcupado
      *
      * Método que devuele todos los registros con EQUIPO_OCUPADO = 'S' cada que algun proceso falle y
      * se necesite liberar el equipo para seguir con el flujo
      *                                                                             
      * @return array con resultado
      *
      * @author Allan Suárez <arsuarez@telconet.ec>
      * @version 1.0 15-10-2014
      */     
    public function getRegistroEquipoOcupado()
    {
        $query = $this->_em->createQuery("
                        SELECT a
                        FROM                         
                        schemaBundle:InfoDocumentoComunHistorial a
                        WHERE    
                        a.equipoOcupado  =  :estadoEquipo
                        ");
        
        $query->setParameter('estadoEquipo', 'S');
        
        $datos = $query->getResult();
        
        return $datos;
        
    }
    
       /**
    * Documentación para el método 'throw_exceptionBind'.
    *
    * Este metodo captura la excepcion al enlazar las variables de entrada con las variables del procedimiento
    *
    * @param  string   $strMessage Contiene el mensaje enviado desde la funcion de la cual se requiere capturar la axcepcion
    *
    * @author Alexander Samaniego <awsamaniego@telconet.ec>
    * @version 1.0 15-08-2014
    */
    public function throw_exceptionBind($strMessage) {
        throw new \Exception($strMessage);
    }

    /**
    * Documentación para el método 'throw_ExceptionOci'.
    *
    * Este metodo captura la excepcion al conectar y preparar la sentencia.
    *
    * @param  string   $strMessage Contiene el mensaje enviado desde la funcion de la cual se requiere capturar la axcepcion
    *
    * @author Alexander Samaniego <awsamaniego@telconet.ec>
    * @version 1.0 15-08-2014
    */
    public function throw_ExceptionOci($objMessage) {
        throw new \Exception($objMessage['message']);
    }
    
}

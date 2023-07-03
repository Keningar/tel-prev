<?php
namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class InfoDetalleRepository extends EntityRepository
{   
    
    /**
     * Funcion que sirve para ejecutar un query que obtiene
     * un array de Detalles (Tareas) por Detalle Hipotesis y por id de la persona
     * Asignada la tarea. Costo=17
     * 
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.3 24-08-2017 Se extrae el id_tarea_inicial, empresa_rol_id, canton_id.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 17-01-2017 Se agrega en la consulta la relación para obtener el número de la tarea y además se obtiene la última asignación
     *                         por cada tarea ya que cuando la tarea es reasignada, ésta se sigue mostrando para la asignación anterior.
     * 
     * @author Luis Tama <ltama@telconet.ec>
     * @version 1.1 12-08-2015 Se filtra tareas por id de persona y se muestra solo si corresponde a la asignacion con id maximo
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 16-07-2015
     * @param int $idDetalleHipotesis
     * @param int $idPersona
     * @return array $arrayResultado (idDetalleHipotesis, idSintoma, nombreSintoma, idHipotesis, 
     *                               nombreHipotesis, idDetalle, nombreTarea, departamentoId, departamentoNombre,
     *                               usuarioAsignadoId, usuarioAsignadoNombre, estadoTarea, fechaTarea, esSolucion)
     */
    public function getDetallesPorDetalleHipotesisPersona($idDetalleHipotesis, $idPersona)
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
                ) NOMBRE_HIPOTESIS,
                DETALLE.ID_DETALLE ID_DETALLE,
                DETALLE.TAREA_ID ID_TAREA_INICIAL,
                (SELECT TAREA.NOMBRE_TAREA 
                FROM DB_SOPORTE.ADMI_TAREA TAREA 
                WHERE TAREA.ID_TAREA = DETALLE.TAREA_ID
                ) NOMBRE_TAREA,
                DETALLE_ASIGNACION.ASIGNADO_ID DEPARTAMENTO_ID,
                DETALLE_ASIGNACION.ASIGNADO_NOMBRE DEPARTAMENTO_NOMBRE,
                DETALLE_ASIGNACION.REF_ASIGNADO_ID USUARIO_ASIGNADO_ID,
                DETALLE_ASIGNACION.REF_ASIGNADO_NOMBRE USUARIO_ASIGNADO_NOMBRE,
                DETALLE_ASIGNACION.PERSONA_EMPRESA_ROL_ID PERSONA_EMPRESA_ROL_ID,
                (SELECT HISTORIAL.ESTADO
                FROM DB_SOPORTE.INFO_DETALLE_HISTORIAL HISTORIAL
                WHERE HISTORIAL.ID_DETALLE_HISTORIAL =
                  (SELECT MAX(DETALLE_HISTORIAL.ID_DETALLE_HISTORIAL)
                  FROM DB_SOPORTE.INFO_DETALLE_HISTORIAL DETALLE_HISTORIAL
                  WHERE DETALLE_HISTORIAL.DETALLE_ID = DETALLE.ID_DETALLE
                  )
                AND HISTORIAL.ESTADO NOT IN (:estadoTarea)
                ) ESTADO_TAREA,
                DETALLE.FE_SOLICITADA FECHA_TAREA,
                DETALLE_ASIGNACION.CANTON_ID CANTON_ID,
                DETALLE.ES_SOLUCION ES_SOLUCION,
                IC.ID_COMUNICACION 
                FROM DB_SOPORTE.INFO_DETALLE_HIPOTESIS DETALLE_HIPOTESIS,
                DB_SOPORTE.INFO_DETALLE DETALLE,
                DB_COMUNICACION.INFO_COMUNICACION IC,
                DB_SOPORTE.INFO_DETALLE_ASIGNACION DETALLE_ASIGNACION
                WHERE DETALLE_HIPOTESIS.ID_DETALLE_HIPOTESIS = DETALLE.DETALLE_HIPOTESIS_ID
                AND DETALLE.ID_DETALLE = DETALLE_ASIGNACION.DETALLE_ID
                AND DETALLE_HIPOTESIS.ID_DETALLE_HIPOTESIS = :idDetalleHipotesis 
                AND IC.DETALLE_ID = DETALLE.ID_DETALLE 
                AND IC.ID_COMUNICACION        = (SELECT MIN(icMin.ID_COMUNICACION) 
                                                 FROM DB_COMUNICACION.INFO_COMUNICACION icMin
                                                 WHERE icMin.DETALLE_ID = IC.DETALLE_ID) 
                AND DETALLE_ASIGNACION.ID_DETALLE_ASIGNACION = (SELECT MAX(daMax.ID_DETALLE_ASIGNACION) 
                                                                FROM DB_SOPORTE.INFO_DETALLE_ASIGNACION daMax
                                                                WHERE daMax.DETALLE_ID = DETALLE_ASIGNACION.DETALLE_ID) ";
        
        
        $rsm->addScalarResult('ID_DETALLE_HIPOTESIS',   'idDetalleHipotesis',   'integer');
        $rsm->addScalarResult('ID_COMUNICACION',        'idComunicacion',       'integer');
        $rsm->addScalarResult('ID_SINTOMA',             'idSintoma',            'integer');
        $rsm->addScalarResult('NOMBRE_SINTOMA',         'nombreSintoma',        'string');
        $rsm->addScalarResult('ID_HIPOTESIS',           'idHipotesis',          'integer');
        $rsm->addScalarResult('NOMBRE_HIPOTESIS',       'nombreHipotesis',      'string');
        $rsm->addScalarResult('ID_DETALLE',             'idDetalle',            'integer');
        $rsm->addScalarResult('ID_TAREA_INICIAL',       'idTareaInicial',       'integer');
        $rsm->addScalarResult('NOMBRE_TAREA',           'nombreTarea',          'string');
        $rsm->addScalarResult('DEPARTAMENTO_ID',        'departamentoId',       'integer');
        $rsm->addScalarResult('DEPARTAMENTO_NOMBRE',    'departamentoNombre',   'string');
        $rsm->addScalarResult('USUARIO_ASIGNADO_ID',    'usuarioAsignadoId',    'integer');
        $rsm->addScalarResult('USUARIO_ASIGNADO_NOMBRE','usuarioAsignadoNombre','string');
        $rsm->addScalarResult('PERSONA_EMPRESA_ROL_ID', 'personaEmpresaRolId',  'integer');
        $rsm->addScalarResult('ESTADO_TAREA',           'estadoTarea',          'string');
        $rsm->addScalarResult('FECHA_TAREA',            'fechaTarea',           'string');
        $rsm->addScalarResult('ES_SOLUCION',            'esSolucion',           'string');
        $rsm->addScalarResult('CANTON_ID',              'cantonId',             'integer');
        
        $query->setParameter("idDetalleHipotesis",  $idDetalleHipotesis);
        $query->setParameter("estadoTarea",         array('Rechazada','Anulada','Cancelada'));
        
        $query->setSQL($sql);
        $arrayResultado = $query->getResult();
        return $arrayResultado;
    }
    
    /**
     * Funcion que sirve para ejecutar un query que obtiene
     * un array de Tareas por id de caso y por id de la persona asignada la tarea.
     * Costo=19
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 16-07-2015
     * @param int $idCaso
     * @param int $idPersona
     * @return array $servicios (idDetalle, nombreTarea, departamentoId, departamentoNombre, 
     *                          usuarioAsignadoId, usuarioAsignadoNombre, estadoTarea, 
     *                          fechaTArea, esSolucion)
     */
    public function getTareasPorCaso($idCaso, $idPersona)
    {
        $rsm = new ResultSetMappingBuilder($this->_em);
        $query = $this->_em->createNativeQuery(null, $rsm);
        
        $sql = "SELECT 
                DETALLE.ID_DETALLE,
                (SELECT TAREA.NOMBRE_TAREA 
                  FROM DB_SOPORTE.ADMI_TAREA TAREA 
                  WHERE TAREA.ID_TAREA = DETALLE.TAREA_ID
                  ) NOMBRE_TAREA,
                  DETALLE_ASIGNACION.ASIGNADO_ID DEPARTAMENTO_ID,
                  DETALLE_ASIGNACION.ASIGNADO_NOMBRE DEPARTAMENTO_NOMBRE,
                  DETALLE_ASIGNACION.REF_ASIGNADO_ID USUARIO_ASIGNADO_ID,
                  DETALLE_ASIGNACION.REF_ASIGNADO_NOMBRE USUARIO_ASIGNADO_NOMBRE,
                  (SELECT HISTORIAL.ESTADO
                  FROM DB_SOPORTE.INFO_DETALLE_HISTORIAL HISTORIAL
                  WHERE HISTORIAL.ID_DETALLE_HISTORIAL =
                    (SELECT MAX(DETALLE_HISTORIAL.ID_DETALLE_HISTORIAL)
                    FROM DB_SOPORTE.INFO_DETALLE_HISTORIAL DETALLE_HISTORIAL
                    WHERE DETALLE_HISTORIAL.DETALLE_ID = DETALLE.ID_DETALLE
                    )
                  AND HISTORIAL.ESTADO NOT IN (:estadoTarea)
                  ) ESTADO_TAREA,
                  DETALLE.FE_SOLICITADA FECHA_TAREA,
                  DETALLE.ES_SOLUCION ES_SOLUCION
                FROM DB_SOPORTE.INFO_CASO CASO,
                DB_SOPORTE.INFO_DETALLE_HIPOTESIS DETALLE_HIPOTESIS,
                DB_SOPORTE.INFO_DETALLE DETALLE,
                DB_SOPORTE.INFO_DETALLE_ASIGNACION DETALLE_ASIGNACION
                WHERE 
                CASO.ID_CASO = DETALLE_HIPOTESIS.CASO_ID
                AND DETALLE_HIPOTESIS.ID_DETALLE_HIPOTESIS = DETALLE.DETALLE_HIPOTESIS_ID
                AND DETALLE.ID_DETALLE = DETALLE_ASIGNACION.DETALLE_ID
                AND CASO.ID_CASO = :idCaso
                AND DETALLE_ASIGNACION.REF_ASIGNADO_ID = :usuarioAsignadoId";
        
        $rsm->addScalarResult('ID_DETALLE',             'idDetalle',            'integer');
        $rsm->addScalarResult('NOMBRE_TAREA',           'nombreTarea',          'string');
        $rsm->addScalarResult('DEPARTAMENTO_ID',        'departamentoId',       'integer');
        $rsm->addScalarResult('DEPARTAMENTO_NOMBRE',    'departamentoNombre',   'string');
        $rsm->addScalarResult('USUARIO_ASIGNADO_ID',    'usuarioAsignadoId',    'integer');
        $rsm->addScalarResult('USUARIO_ASIGNADO_NOMBRE','usuarioAsignadoNombre','string');
        $rsm->addScalarResult('ESTADO_TAREA',           'estadoTarea',          'string');
        $rsm->addScalarResult('FECHA_TAREA',            'fechaTarea',           'string');
        $rsm->addScalarResult('ES_SOLUCION',            'esSolucion',           'string');
        
        $query->setParameter("idCaso",              $idCaso);
        $query->setParameter("usuarioAsignadoId",   $idPersona);
        $query->setParameter("estadoTarea",         array('Rechazada','Anulada','Cancelada','Finalizada'));
        
        $query->setSQL($sql);
        $servicios = $query->getResult();

        return $servicios;
    }


     /**
     * getTareaPorCasoId - Funcion que retorna la ultima asignacion de una tarea por caso
     * Costo=3
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 06-06-2018
     *
     * Costo=5
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.1 27-06-2018 - Se modifica el query colocando un IN al momento de hacer match con la tabla INFO_DETALLE_HIPOTESIS,
     *                           para evitar el error (subquery returns more than 1 row).
     *
     * @param array $arrayParametros [ intIdCaso      => id del caso,
     *                                 strNombreTarea => nombre de la tarea ]
     *
     * @return array $arrayTarea
     */
    public function getTareaPorCasoId($arrayParametros)
    {
        $objRsm     = new ResultSetMappingBuilder($this->_em);
        $objQuery   = $this->_em->createNativeQuery(null, $objRsm);
        $arrayTarea = "";

        $strSql = " SELECT
                        IDEASI.ASIGNADO_ID,
                        IDEASI.PERSONA_EMPRESA_ROL_ID,
                        IDEASI.TIPO_ASIGNADO

                    FROM INFO_DETALLE_ASIGNACION IDEASI WHERE IDEASI.ID_DETALLE_ASIGNACION = (
                    SELECT MAX(IDAS.ID_DETALLE_ASIGNACION) FROM INFO_DETALLE IDE,INFO_DETALLE_ASIGNACION IDAS
                    WHERE IDE.ID_DETALLE = IDAS.DETALLE_ID
                    AND IDE.TAREA_ID = (SELECT ATA.ID_TAREA FROM ADMI_TAREA ATA WHERE ATA.NOMBRE_TAREA = :paramNombreTarea)
                    AND IDAS.ID_DETALLE_ASIGNACION =
                    (SELECT MAX(IDAS2.ID_DETALLE_ASIGNACION) FROM INFO_DETALLE_ASIGNACION IDAS2 WHERE IDAS2.DETALLE_ID = IDE.ID_DETALLE)
                    AND IDE.DETALLE_HIPOTESIS_ID IN (SELECT IDH.ID_DETALLE_HIPOTESIS FROM INFO_DETALLE_HIPOTESIS IDH WHERE IDH.CASO_ID = (
                    SELECT ICA.ID_CASO FROM INFO_CASO ICA WHERE ICA.ID_CASO = :paramIdCaso ))
                    AND IDE.TAREA_ID IS NOT NULL) ";

        $objRsm->addScalarResult('ASIGNADO_ID','asignadoId','integer');
        $objRsm->addScalarResult('PERSONA_EMPRESA_ROL_ID','personaEmpresaRolId','integer');
        $objRsm->addScalarResult('TIPO_ASIGNADO','tipoAsignado','string');

        $objQuery->setParameter("paramIdCaso",$arrayParametros["intIdCaso"]);
        $objQuery->setParameter("paramNombreTarea",$arrayParametros["strNombreTarea"]);

        $objQuery->setSQL($strSql);
        $arrayTarea = $objQuery->getResult();

        return $arrayTarea;
    }
    
    
     /**
     * getDetallesPorIdCaso - Funcion que retorna los id detalle por id caso
     * Costo=10
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 06-06-2018
     *
     * @param array $arrayParametros [ intIdCaso => id del caso ]
     *
     * @return array $arrayDetalles
     */
    public function getDetallesPorIdCaso($arrayParametros)
    {
        $objRsm     = new ResultSetMappingBuilder($this->_em);
        $objQuery   = $this->_em->createNativeQuery(null, $objRsm);
        $arrayDetalles = "";

        $strSql = " SELECT IDE.ID_DETALLE FROM INFO_DETALLE IDE
                        WHERE IDE.DETALLE_HIPOTESIS_ID IN (SELECT IDH.ID_DETALLE_HIPOTESIS 
                        FROM INFO_DETALLE_HIPOTESIS IDH WHERE IDH.CASO_ID = :paramIdCaso)
                        AND IDE.TAREA_ID IS NOT NULL ";

        $objRsm->addScalarResult('ID_DETALLE','idDetalle','integer');

        $objQuery->setParameter("paramIdCaso",$arrayParametros["intIdCaso"]);

        $objQuery->setSQL($strSql);
        $arrayDetalles = $objQuery->getResult();

        return $arrayDetalles;
    }    


     /**
     * getDepartamentoOrigenPorTarea - Funcion que retorna el departamento que creo la tarea
     * Costo=3
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 07-06-2018
     *
     * @param array $arrayParametros [ intIdDetalle => id del detalle ]
     *
     * @return integer $intDepartamentoId
     */
    public function getDepartamentoOrigenPorTarea($arrayParametros)
    {
        $objRsm             = new ResultSetMappingBuilder($this->_em);
        $objQuery           = $this->_em->createNativeQuery(null, $objRsm);
        $intDepartamentoId  = "";

        $strSql = " SELECT IDEHI2.DEPARTAMENTO_ORIGEN_ID FROM INFO_DETALLE_HISTORIAL IDEHI2
                    WHERE IDEHI2.ID_DETALLE_HISTORIAL = (
                    SELECT MIN(IDEHI.ID_DETALLE_HISTORIAL) FROM INFO_DETALLE_HISTORIAL IDEHI
                    WHERE IDEHI.DETALLE_ID = :paramIdDetalle ) ";

        $objRsm->addScalarResult('DEPARTAMENTO_ORIGEN_ID','departamamentoOrigenId','integer');

        $objQuery->setParameter("paramIdDetalle",$arrayParametros["intIdDetalle"]);

        $objQuery->setSQL($strSql);
        $intDepartamentoId = $objQuery->getSingleScalarResult();

        return $intDepartamentoId;
    }

        
    /**
     * Función que retorna un arreglo de tareas con la información de la última persona asignada a la tarea.
     * Costo=35
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.0 04-05-2018
     *
     * Costo=20
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.1 21-08-2018
     *
     * @param array $arrayParametros[
     *                                  intIdCaso:  integer:    Id del caso.
     *                              ]
     * @return array $servicios (idDetalle, nombreTarea, departamentoId, departamentoNombre, 
     *                          usuarioAsignadoId, usuarioAsignadoNombre, estadoTarea, 
     *                          fechaTArea, esSolucion)
     */
    public function getTareasCaso($arrayParametros)
    {
        $objRsm    = new ResultSetMappingBuilder($this->_em);
        $objQuery  = $this->_em->createNativeQuery(null, $objRsm);
        $strSql    = "SELECT INDE.ID_DETALLE ID_DETALLE,
                        ADTA.NOMBRE_TAREA NOMBRE_TAREA,
                        IDASIG.DEPARTAMENTO_ID DEPARTAMENTO_ID,
                        IDASIG.ASIGNADO_NOMBRE DEPARTAMENTO_NOMBRE,
                        IDASIG.REF_ASIGNADO_ID USUARIO_ASIGNADO_ID,
                        IDASIG.REF_ASIGNADO_NOMBRE USUARIO_ASIGNADO_NOMBRE,
                        IDEHIS.ESTADO ESTADO_TAREA,
                        INDE.FE_SOLICITADA FECHA_TAREA,
                        INDE.ES_SOLUCION ES_SOLUCION
                      FROM DB_SOPORTE.INFO_DETALLE INDE,
                        DB_SOPORTE.INFO_DETALLE_HIPOTESIS IDHI,
                        DB_SOPORTE.ADMI_TAREA ADTA,
                        DB_SOPORTE.INFO_DETALLE_HISTORIAL IDEHIS,
                        DB_SOPORTE.INFO_DETALLE_ASIGNACION IDASIG
                      WHERE IDHI.ID_DETALLE_HIPOTESIS = INDE.DETALLE_HIPOTESIS_ID
                      AND INDE.TAREA_ID               = ADTA.ID_TAREA
                      AND INDE.TAREA_ID              IS NOT NULL
                      AND IDEHIS.ID_DETALLE_HISTORIAL =
                        (SELECT MAX(IDETH.ID_DETALLE_HISTORIAL)
                        FROM DB_SOPORTE.INFO_DETALLE_HISTORIAL IDETH
                        WHERE IDETH.DETALLE_ID = INDE.ID_DETALLE
                        )
                      AND IDASIG.ID_DETALLE_ASIGNACION =
                        (SELECT MAX(ID_DETALLE_ASIGNACION)
                        FROM INFO_DETALLE_ASIGNACION IDEASI
                        WHERE IDEASI.DETALLE_ID = INDE.ID_DETALLE
                        )
                      AND IDHI.CASO_ID = :intIdCaso";
        $objRsm->addScalarResult('ID_DETALLE',             'idDetalle',            'integer');
        $objRsm->addScalarResult('NOMBRE_TAREA',           'nombreTarea',          'string');
        $objRsm->addScalarResult('DEPARTAMENTO_ID',        'departamentoId',       'integer');
        $objRsm->addScalarResult('DEPARTAMENTO_NOMBRE',    'departamentoNombre',   'string');
        $objRsm->addScalarResult('USUARIO_ASIGNADO_ID',    'usuarioAsignadoId',    'integer');
        $objRsm->addScalarResult('USUARIO_ASIGNADO_NOMBRE','usuarioAsignadoNombre','string');
        $objRsm->addScalarResult('ESTADO_TAREA',           'estadoTarea',          'string');
        $objRsm->addScalarResult('FECHA_TAREA',            'fechaTarea',           'string');
        $objRsm->addScalarResult('ES_SOLUCION',            'esSolucion',           'string');


        $objQuery->setParameter("intIdCaso", $arrayParametros['intIdCaso']);
        $objQuery->setSQL($strSql);
        $arrayServicios = $objQuery->getArrayResult();
        return $arrayServicios;
    }

    /**
     * Metodo que obtiene las tareas asignadas 
     *
     * @version 1.0 version inicial
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 17-05-2016 - Se obtiene el tipo asignado: CUADRILLA, EMPLEADO, EMPRESAEXTERNA
     * 
     * @return json $resultado
     */
    public function generarArrayTareasAsignadas($em, $start,$limit, $idDetalleSolicitud)
    {
        $arr_encontrados = array();
        $registrosTotal = $this->getRegistrosTareasAsignadas('', '', $idDetalleSolicitud);
        $registros = $this->getRegistrosTareasAsignadas($start, $limit, $idDetalleSolicitud);
        
        if ($registros) {
            $num = count($registrosTotal);            
            foreach ($registros as $data)
            {                                                   
                $nombreProceso =  ($data["nombreProceso"] ? $data["nombreProceso"]  : ""); 
                $nombreTarea =  ($data["nombreTarea"] ? $data["nombreTarea"]  : "");  
                $coordenadas = $data["longitud"] . ", ". $data["latitud"];              
                $latitud =  ($data["latitud"] ? $data["latitud"]  : "");  
                $longitud =  ($data["longitud"] ? $data["longitud"]  : "");  
                
				$idAsignacion = 0;
                $idAsignado = 0; $nombreAsignado = "No Asignado";
                $ref_idAsignado = 0; $ref_nombreAsignado = "No Asignado"; $tipo_asignado="";
                $infoAsignaciones = $em->getRepository("schemaBundle:InfoDetalleAsignacion")->getUltimaAsignacion($data["idDetalle"]);
                if($infoAsignaciones)
                {
                    $idAsignacion       = $infoAsignaciones->getId();
                    $idAsignado         = $infoAsignaciones->getAsignadoId();
                    $nombreAsignado     = $infoAsignaciones->getAsignadoNombre();
                    $ref_idAsignado     = $infoAsignaciones->getRefAsignadoId();
                    $ref_nombreAsignado = $infoAsignaciones->getRefAsignadoNombre();
                    $tipo_asignado      = $infoAsignaciones->getTipoAsignado();
                }
                
                $arr_encontrados[]=array(
                                         'id_info_detalle' =>$data["idDetalle"],
                                         'id_detalle_solicitud' =>$data["idDetalleSolicitud"],
                                         'id_proceso' =>$data["idProceso"],
                                         'id_tarea' =>$data["idTarea"],
                                         'id_asignacion' =>$idAsignacion,
                                         'id_asignado' =>$idAsignado,
                                         'ref_id_asignado' =>$ref_idAsignado,
                                         'nombre_proceso' =>trim($nombreProceso),
                                         'nombre_tarea' =>trim($nombreTarea),
                                         'nombre_asignado' =>trim($nombreAsignado),
                                         'ref_nombre_asignado' =>trim($ref_nombreAsignado),
                                         'tipo_asignado'=>$tipo_asignado,
                                         'coordenadas' =>trim($coordenadas),
                                         'latitud' =>trim($latitud),
                                         'longitud' =>trim($longitud)
                                        );
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_info_detalle' => 0 , 'id_detalle_solicitud' => 0 , 'id_proceso' => 0 ,
                                                        'id_tarea' => 0 , 'id_asignado' => 0 , 'nombre_proceso' => "Ninguno",
                                                        'nombre_tarea' => 'Ninguno', 'nombre_asignado' => 'Ninguno', 
                                                        'coordenadas' => 'Ninguno', 'latitud' => 'Ninguno', 'longitud' => 'Ninguno', 
                                                        'factibilidad_id' => 0 , 'factibilidad_nombre' => 'Ninguno', 'estado' => 'Ninguno'));
                // $resultado = json_encode( $resultado);
                return $resultado;
            }
            else
            {
                // $dataF =json_encode($arr_encontrados);
                // $resultado= '{"total":"'.$num.'","encontrados":'.$dataF.'}';
                return $arr_encontrados;
            }
        }
        else
        {
            $resultado= '{"total":"0","encontrados":[]}';
            return $resultado;
        }
    }

     /**
     * getUltimoDetalleSolicitud
     *
     * Esta funcion retorna el ultimo ID_DETALLE de la solicitud de PLANIFICACION o MIGRACION que tenga asociado el servicio
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 04-12-2015
     *
     * @param integer  $servicioId
     *
     * @return array $strDatos
     *
     */
    public function getUltimoDetalleSolicitud($servicioId)
    {
        $strFuncion = "SELECT TECNK_SERVICIOS.GET_ID_DETALLE_ULTIMA_SOL(:varServicio) as idDetalle FROM DUAL";
        $stmt = $this->_em->getConnection()->prepare($strFuncion);
        $stmt->bindValue('varServicio',   $servicioId);
        $stmt->execute();
        $arraResult = $stmt->fetchAll();

        return $arraResult[0];
    }

    public function getOneDetalleByCasoSintoma($id_caso, $id_sintoma)
    {
        $sql = "SELECT d         
                FROM schemaBundle:InfoDetalle d        
                WHERE d.casoId = '$id_caso' 
                AND d.sintomaId = '$id_sintoma'               
                ORDER BY d.id ASC
               ";           
        $query = $this->_em->createQuery($sql);   
        $datos = $query->setFirstResult(0)->setMaxResults(1)->getOneOrNullResult();          
        return $datos;                	             
    }
	
    public function getOneDetalleByCasoHipotesis($id_caso, $id_hipotesis)
    {
        $sql = "SELECT d         
                FROM schemaBundle:InfoDetalle d        
                WHERE d.casoId = '$id_caso' 
                AND d.hipotesisId = '$id_hipotesis'   
				AND d.sintomaId is null  
                ORDER BY d.id ASC
               ";           
        $query = $this->_em->createQuery($sql);   
        $datos = $query->setFirstResult(0)->setMaxResults(1)->getOneOrNullResult();          
        return $datos;                	             
    }

    /**
     * Funcion que consulta el detalle de las tareas de un caso
     *
     * @author modificado Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.5 30-07-2019 - Se modifica la validación de la característica ATENDER_ANTES, por motivos que no se estaba
     *                           filtrando de manera correcta y mostrada una información erronea.
     *                         - Se modifica la manera de obtener los tiempos totales de una tarea, por
     *                           motivos que se detecto un mal cálculo en los tiempos.
     *
     * @author modificado Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.4 29-08-2018 -  Se agrega una nueva validación para identificar las tareas con la característica ATENDER_ANTES.
     *
     * @author modificado Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.3 14-06-2018 Se realizan ajustes para identificar si la tarea proviene de Hal.
     *
     * @author modificado Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 11-11-2016 Se realizan ajustes para calcular el tiempo de la tarea, en base al nuevo esquema de
     *                         iniciar,pausar y reanudar tareas
     *
     * @author modificado Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 04-07-2016 Se obtiene la observacion de la tarea, desde el campo OBSERVACION de la tabla INFO_DETALLE
     *
     * @version 1.0
     *
     * @return array $resultado
     */
	public function generarJsonDetallesXCaso($id_caso = '')
    {
        $arr_encontrados = array();

        $sql = "SELECT dh 
        
                FROM 
                schemaBundle:InfoDetalle d,
                schemaBundle:InfoDetalleHipotesis dhi,  
                schemaBundle:InfoDetalleHistorial dh,  
                schemaBundle:AdmiTarea t   
        
                WHERE d.tareaId = t.id 
				AND dhi.casoId = '$id_caso' 
				AND d.detalleHipotesisId = dhi.id 
				AND dh.detalleId = d.id 
                AND dh.id = (SELECT MAX(dhMax.id) 
                              FROM schemaBundle:InfoDetalleHistorial dhMax
                              WHERE dhMax.detalleId = dh.detalleId)
               ";

        $query = $this->_em->createQuery($sql);
        $rs = $query->getResult();

        if(isset($rs))
        {
            $num = count($rs);
            foreach($rs as $entidad)
            {
                $EntityDetalle          = $entidad->getDetalleId();
                $objInfoComunicacion    = $this->_em->getRepository('schemaBundle:InfoComunicacion')
                    ->findOneBy(array ('detalleId' => $EntityDetalle->getId()),
                                array ("id"        => "ASC"));

                $EntityDetalleHipotesis = $this->_em->getRepository('schemaBundle:InfoDetalleHipotesis')->findOneById($EntityDetalle->getDetalleHipotesisId());
                $EntitySintoma          = $this->_em->getRepository('schemaBundle:AdmiSintoma')->findOneById($EntityDetalleHipotesis->getSintomaId());
                $EntityHipotesis        = $this->_em->getRepository('schemaBundle:AdmiHipotesis')->findOneById($EntityDetalleHipotesis->getHipotesisId());
                $EntityTarea            = $this->_em->getRepository('schemaBundle:AdmiTarea')->findOneById($EntityDetalle->getTareaId());
                $EntityDetAsig          = $this->_em->getRepository('schemaBundle:InfoDetalleAsignacion')->findOneBy(array('detalleId' => $entidad->getDetalleId()));

                $id_sintoma             = ($EntitySintoma ? ($EntitySintoma->getId() ? $EntitySintoma->getId() : "") : "");
                $nombre_sintoma         = ($EntitySintoma ? ($EntitySintoma->getNombreSintoma() ? $EntitySintoma->getNombreSintoma() : "") : "");

                $id_hipotesis           = ($EntityHipotesis ? ($EntityHipotesis->getId() ? $EntityHipotesis->getId() : "") : "");
                $nombre_hipotesis       = ($EntityHipotesis ? ($EntityHipotesis->getNombreHipotesis() ? $EntityHipotesis->getNombreHipotesis() : "") : "");

                $id_tarea               = ($EntityTarea ? ($EntityTarea->getId() ? $EntityTarea->getId() : "") : "");
                $nombre_tarea           = ($EntityTarea ? ($EntityTarea->getNombreTarea() ? $EntityTarea->getNombreTarea() : "") : "");

                $id_detAsig             = ($EntityDetAsig ? ($EntityDetAsig->getId() ? $EntityDetAsig->getId() : "") : "");
                $entityInfoDetalle      = $this->_em->getRepository('schemaBundle:InfoDetalle')->find($entidad->getDetalleId()->getId());
                $motivo_detAsig         = $entityInfoDetalle->getObservacion() ? $entityInfoDetalle->getObservacion() : "";
                $fechaEstado            = strval(date_format($entidad->getFeCreacion(), "d-m-Y H:i"));

                $caso                   = $this->_em->getRepository('schemaBundle:InfoCaso')->find($id_caso);

                $tiempoTarea            = $this->getTiemposTareaTotales($EntityDetalle->getId());



                $tiempoCliente      = 0;
                $tiempoEmpresa      = 0;
                $tiempoTotal        = 0;
                $fechaEjecucion     = '';
                $fechaFinalizacion  = '';
                $observacion        = '';
                $strFeCreacionTarea = strval(date_format($EntityDetalle->getFeCreacion(), "d-m-Y H:i"));

                if($tiempoTarea)
                {
                    $tiempoCliente = $tiempoTarea[0]->getTiempoCliente() . ' minutos';
                    $tiempoEmpresa = $tiempoTarea[0]->getTiempoEmpresa() . ' minutos';

                    $fechaEjecucion     = strval(date_format($tiempoTarea[0]->getFeEjecucion(), "d-m-Y H:i"));
                    $fechaFinalizacion  = strval(date_format($tiempoTarea[0]->getFeFinalizacion(), "d-m-Y H:i"));

                    $observacion        = $tiempoTarea[0]->getObservacion() ? $tiempoTarea[0]->getObservacion() : '';

                    $tiempoTotal        = $tiempoTarea[0]->getTiempoCliente() + $tiempoTarea[0]->getTiempoEmpresa() . ' minutos';
                }
                else
                {
                    $arrayTiemposTarea = $this->_em->getRepository('schemaBundle:InfoTareaTiempoParcial')
                            ->getTiemposTarea(array('intIdDetalle' => $EntityDetalle->getId()));

                    if ($arrayTiemposTarea['status'] === 'ok' && !empty($arrayTiemposTarea['result']))
                    {
                        $arrayTiempos  = $arrayTiemposTarea['result'][0];
                        $tiempoCliente = $arrayTiempos['cliente'] . ' minutos';
                        $tiempoEmpresa = $arrayTiempos['empresa'] . ' minutos';
                        $tiempoTotal   = $arrayTiempos['total']   . ' minutos';

                        /**
                         * Se realiza esta validación, para evitar un cálculo erróneo por motivos
                         * que existen tareas abiertas que no cuentan con esta nueva modalidad, que
                         * almacena cada uno de los tiempos cuando el estado de la tarea cambia.
                         */
                        $objFechaCreacionTarea = new \DateTime(date_format($EntityDetalle->getFeCreacion(), "d-m-Y H:i"));
                        $objFechaFinalizaTarea = new \DateTime(date_format($arrayTiempos['feFinaliza']    , "d-m-Y H:i"));

                        $objDiferenciaFechas = $objFechaFinalizaTarea->diff($objFechaCreacionTarea);
                        $intMinutos         += $objDiferenciaFechas->days * 24 * 60;
                        $intMinutos         += $objDiferenciaFechas->h * 60;
                        $intMinutos         += $objDiferenciaFechas->i;

                        if ($tiempoTotal <> $intMinutos)
                        {
                            $tiempoTotal   = $intMinutos;
                            $tiempoEmpresa = $tiempoTotal - $tiempoCliente;
                        }
                    }
                    else
                    {
                        //Se calcula el tiempo de las tareas, si acaso fueran creadas a partir del nuevo esquema de iniciar,pausar
                        //y reanudar tareas
                        $objInfoTareaTiempoParcial = $this->_em->getRepository('schemaBundle:InfoTareaTiempoParcial')
                                                               ->findOneBy(array('detalleId' => $EntityDetalle->getId(),
                                                                                 'estado'    => 'Finalizada'));

                        if(is_object($objInfoTareaTiempoParcial))
                        {
                            $tiempoTotal   = $objInfoTareaTiempoParcial->getValorTiempo().' minutos';

                            if($caso->getTipoAfectacion() == "SINAFECTACION")
                            {
                                $tiempoCliente = $tiempoTotal;
                                $tiempoEmpresa = '0 minutos';
                            }
                            else
                            {
                                $tiempoCliente = '0 minutos';
                                $tiempoEmpresa = $tiempoTotal;
                            }
                        }
                    }
                }

                $esSolucion = ($EntityDetalle ? ($EntityDetalle->getEsSolucion() ? ($EntityDetalle->getEsSolucion() == 'S' ? "SI" : "NO") 
                                                                                    : "NO") : "NO");

                // Verificamos si la tarea proviene de hal
                $boolEsHal        = false;
                $boolAtenderAntes = false;
                if (is_object($objInfoComunicacion))
                {
                    $arrayResulExisteHal = $this->_em->getRepository('schemaBundle:InfoCuadrillaPlanifCab')
                        ->tareaExisteEnHal(array ('intNumeroTarea' => $objInfoComunicacion->getId(),
                                                  'strEstadoCab'   => 'Activo',
                                                  'strEstadoDet'   => 'Activo'));

                    if (!empty($arrayResulExisteHal) && count($arrayResulExisteHal) > 0
                        && $arrayResulExisteHal['resultado'] === 'ok')
                    {
                        $boolEsHal = $arrayResulExisteHal['existeTarea'];
                    }

                    //Verificamos si la tarea tiene característica ATENDER_ANTES Activa.
                    $objAdmiCaracteristicaAA = $this->_em->getRepository('schemaBundle:AdmiCaracteristica')
                                    ->findOneBy(array ('descripcionCaracteristica' => 'ATENDER_ANTES',
                                                       'estado'                    => 'Activo'));

                    if (is_object($objAdmiCaracteristicaAA))
                    {
                        $objInfoTareaCaracteristicaAA = $this->_em->getRepository('schemaBundle:InfoTareaCaracteristica')
                                ->findOneBy( array('tareaId'          => $objInfoComunicacion->getId(),
                                                   'caracteristicaId' => $objAdmiCaracteristicaAA->getId(),
                                                   'valor'            => 'S',
                                                   'estado'           => 'Activo'));

                        if (is_object($objInfoTareaCaracteristicaAA))
                        {
                            $boolAtenderAntes = true;
                        }
                    }
                }

                $arr_encontrados[] = array(
                    'fechaCreacion'     => $strFeCreacionTarea,
                    'id'                => $EntityDetalle->getId(),
                    'id_caso'           => $caso->getId(),
                    'numero_caso'       => $caso->getNumeroCaso(),
                    'id_sintoma'        => $id_sintoma,
                    'nombre_sintoma'    => $nombre_sintoma,
                    'id_hipotesis'      => $id_hipotesis,
                    'nombre_hipotesis'  => $nombre_hipotesis,
                    'id_tarea'          => $id_tarea,
                    'nombre_tarea'      => $nombre_tarea,
                    'fecha_estado'      => $fechaEstado,
                    'motivo_detAsig'    => $motivo_detAsig,
                    'estado'            => $entidad->getEstado(),
                    'esSolucion'        => $esSolucion,
                    'tiempoCliente'     => $tiempoCliente,
                    'tiempoEmpresa'     => $tiempoEmpresa,
                    'tiempoTotal'       => $tiempoTotal,
                    'fechaEjecucion'    => $fechaEjecucion,
                    'fechaFinalizacion' => $fechaFinalizacion,
                    'observacion'       => $observacion,
                    'accion'            => ($entidad->getEstado() == 'Finalizada' || $entidad->getEstado() == 'Cancelada') 
                                            ? "icon-invisible" : "button-grid-agregarSeguimiento",
                    'accion1'           => ($entidad->getEstado() == 'Finalizada' || $entidad->getEstado() == 'Cancelada')
                                            ? "button-grid-show" : "icon-invisible",
                    'tareaEsHal'        => $boolEsHal,
                    'esHal'             => ($boolEsHal ? 'SI' : 'NO'),
                    'atenderAntes'      => ($boolAtenderAntes ? 'SI' : 'NO')
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

    public function getTiemposTareaTotales($detalleId){
    
	  $qb = $this->_em->createQueryBuilder();
        $qb->select('tiempo')
           ->from('schemaBundle:InfoTareaTiempoAsignacion','tiempo')
           ->where('tiempo.detalleId = ?1')
           ->setParameter(1, $detalleId);          
        $query = $qb->getQuery();
        $rs = $query->getResult();
    
	return $rs;
    
    
    }
	
	
    public function generarJsonSintomasXCaso($id_caso='', $retornaCriteriosAfectados=true)
    {
        $arr_encontrados = array();
      
        $qb = $this->_em->createQueryBuilder();
        $qb->select('distinct sintoma.id')
           ->from('schemaBundle:InfoDetalleHipotesis','detalleHipotesis')
           ->where('detalleHipotesis.casoId = ?1')
           ->setParameter(1, $id_caso)
           ->join('detalleHipotesis.sintomaId','sintoma')     
           ->orderBy('sintoma.id','asc');
        $query = $qb->getQuery();
        $rs = $query->getResult();
        
        if(isset($rs))
        {
            $num = count($rs);
            foreach ($rs as $entidad)
            {
                $sintoma = $this->_em->getRepository('schemaBundle:AdmiSintoma')->find($entidad['id']); 		
				if(!$retornaCriteriosAfectados)
				{	
	                $arr_encontrados[]=array('id_sintoma'=> $sintoma->getId(),
	                                         'nombre_sintoma' =>$sintoma->getNombreSintoma(),
	                                         'criterios_sintoma' =>"",
	                                         'afectados_sintoma' =>"");
				}
				else
				{
					$criterios = $this->generarJsonCriteriosXCaso($id_caso);
	                $afectados = $this->generarJsonAfectadosXCaso($id_caso);
	                $arr_encontrados[]=array('id_sintoma'=> $sintoma->getId(),
	                                         'nombre_sintoma' =>$sintoma->getNombreSintoma(),
	                                         'criterios_sintoma' =>$criterios,
	                                         'afectados_sintoma' =>$afectados);
				}
            }
            $data=json_encode($arr_encontrados);
            $resultado= '{"total":"'.$num.'","encontrados":'.$data.'}';

            return $resultado;
        }
        else
        {
            $resultado= '{"total":"0","acciones":[]}';

            return $resultado;
        }
    }
	
	public function generarJsonCriteriosXCaso($id_caso)
    {
        $arr_encontrados = array();
        
        if($id_caso)
        {
            
            $rsTotal= $this->getCriteriosXCaso($id_caso);
            $rs= $this->getCriteriosXCaso($id_caso);
        }

        if(isset($rs))
        {
            $num = count($rsTotal);
            $i=1;
            foreach ($rs as $entidad)
            {
                $arr_encontrados[]=array('id_criterio_afectado'=> $entidad->getId(),
                                         'detalle_id' =>$entidad->getDetalleId()->getId(),
                                         'criterio' =>$entidad->getCriterio(),
                                         'opcion' =>$entidad->getOpcion());
                $i++;
            }
            $data=json_encode($arr_encontrados);
            $resultado= '{"total":"'.$num.'","encontrados":'.$data.'}';

            return $resultado;
        }
        else
        {
            $resultado= '{"total":"0","acciones":[]}';

            return $resultado;
        }
    }
    public function getCriteriosXCaso($id_caso)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('criterio')
           ->from('schemaBundle:InfoCriterioAfectado','criterio')
           ->from('schemaBundle:InfoDetalle','detalle')
		   ->from('schemaBundle:InfoDetalleHipotesis', 'detalleHipotesis')
           ->where('detalleHipotesis.casoId = ?1')
           ->setParameter(1, $id_caso)
		   ->andWhere('detalle.detalleHipotesisId = detalleHipotesis.id')
           ->andWhere('criterio.detalleId = detalle');
        
        $query = $qb->getQuery();
        return $query->getResult();
    }
    
    public function generarJsonAfectadosXCaso($id_caso)
    {
        $arr_encontrados = array();
        
        if($id_caso)
        {
            
            $rsTotal= $this->getAfectadosXCaso($id_caso);
            $rs= $this->getAfectadosXCaso($id_caso);
        }

        if(isset($rs))
        {
            $num = count($rsTotal);
            $i=1;
            foreach ($rs as $entidad)
            {
                $arr_encontrados[]=array('id'=> $entidad->getId(),
                                         'id_afectado' =>$entidad->getAfectadoId(),
                                         'id_criterio' =>$entidad->getCriterioAfectadoId(),
                                         'caso_id_detalle' =>$entidad->getDetalleId(),
                                         'nombre_afectado' =>$entidad->getAfectadoNombre(),
                                         'descripcion_afectado' =>$entidad->getAfectadoDescripcion());
                $i++;
            }
            $data=json_encode($arr_encontrados);
            $resultado= '{"total":"'.$num.'","encontrados":'.$data.'}';

            return $resultado;
        }
        else
        {
            $resultado= '{"total":"0","acciones":[]}';

            return $resultado;
        }
    }
    
    public function getAfectadosXCaso($id_caso)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('afectado')
           ->from('schemaBundle:InfoParteAfectada','afectado')
           ->from('schemaBundle:InfoDetalle','detalle')
		   ->from('schemaBundle:InfoDetalleHipotesis', 'detalleHipotesis')
           ->where('detalleHipotesis.casoId = ?1')
           ->setParameter(1, $id_caso)
		   ->andWhere('detalle.detalleHipotesisId = detalleHipotesis.id')
           ->andWhere('afectado.detalleId = detalle');
        
        $query = $qb->getQuery();
        return $query->getResult();
    }

    public function getUltimaAsignacionCasoByDetalleHipotesis($id_detalle_hipotesis)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('asignacion')
           ->from('schemaBundle:InfoCasoAsignacion','asignacion')
           ->from('schemaBundle:InfoDetalleHipotesis','detalleHipotesis')
           ->where('detalleHipotesis.id = ?1')
           ->setParameter(1, $id_detalle_hipotesis)
		   ->andWhere('asignacion.detalleHipotesisId = detalleHipotesis.id')
           ->orderBy('asignacion.id','DESC')
           ->setMaxResults(1);       
        $query = $qb->getQuery();
        $results = $query->getResult();
        return ($results && count($results)>0) ? $results[0] : false;
    }
	
    public function generarJsonHipotesisXCaso($id_caso='', $emComercial='',$idPunto='')
    {
        $arr_encontrados = array();
	  /*
        $query =  "SELECT detalleHipotesis, hipotesis ".
				  "FROM schemaBundle:InfoDetalleHipotesis detalleHipotesis, schemaBundle:AdmiHipotesis hipotesis ".
				  "WHERE 
				  detalleHipotesis.casoId = $id_caso AND 
				  detalleHipotesis.hipotesisId is not null  AND 
				  detalleHipotesis.hipotesisId = hipotesis.id  AND 				  
				  hipotesis.estado not like 'Eliminado' ".
				  "ORDER BY detalleHipotesis.id";         
		$rs = $this->_em->createQuery($query)->getResult();
		*/
		
        $qb = $this->_em->createQueryBuilder();
        $qb->select('detalleHipotesis')
		   ->from('schemaBundle:InfoDetalleHipotesis', 'detalleHipotesis')
           ->where('detalleHipotesis.casoId = ?1')
           ->setParameter(1, $id_caso)
           ->andWhere('detalleHipotesis.hipotesisId is not null')
           ->orderBy('detalleHipotesis.id','asc');
        $query = $qb->getQuery();
        $rs = $query->getResult();
		
        if(isset($rs))
        {				
            $num = count($rs);
			$arrayHipotesis = array();
			$arrayTodo = array();
			
			$cont = 0;
            foreach ($rs as $data)
            {
				$sintomaData = $data->getSintomaId();
				$hipotesisData = $data->getHipotesisId();
				
				
				$idDepartamento = ""; $nombreDepartamento = ""; $idEmpleado = ""; $nombreEmpleado = ""; 
				$observacion = ""; $nombresAsignadoPor = ""; $feAsignacion = '';				
				$nombreOficina = ""; $nombreEmpresa = "";
				
				$objAsignacion = $this->getUltimaAsignacionCasoByDetalleHipotesis($data->getId());
				if($objAsignacion && count($objAsignacion)>0)
				{
					$idDepartamento = $objAsignacion->getAsignadoId() ? $objAsignacion->getAsignadoId() : "";
					$nombreDepartamento = $objAsignacion->getAsignadoNombre() ? $objAsignacion->getAsignadoNombre() : "";
					$idEmpleado = $objAsignacion->getRefAsignadoId() ? $objAsignacion->getRefAsignadoId() : "";
					$nombreEmpleado = $objAsignacion->getRefAsignadoNombre() ? $objAsignacion->getRefAsignadoNombre() : "";
					$observacion = $objAsignacion->getMotivo() ? $objAsignacion->getMotivo() : "";
					
					if($objAsignacion->getPersonaEmpresaRolId())
					{
						$InfoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->findOneById($objAsignacion->getPersonaEmpresaRolId());
						
						if($InfoPersonaEmpresaRol && count($InfoPersonaEmpresaRol)>0)
						{
							$oficinaEntity = $InfoPersonaEmpresaRol->getOficinaId();
							$empresaEntity = $oficinaEntity->getEmpresaId();
							
							$nombreOficina = ($oficinaEntity ? ($oficinaEntity->getNombreOficina() ? $oficinaEntity->getNombreOficina() : "") : "");
							$nombreEmpresa = ($empresaEntity ? ($empresaEntity->getNombreEmpresa() ? $empresaEntity->getNombreEmpresa() : "") : "");
						}
					}
					
					$feAsignacion = $objAsignacion->getFeCreacion() ? date_format($objAsignacion->getFeCreacion(), "d-m-Y G:i") : "";
					
					$usrAsignadoPor = $objAsignacion->getUsrCreacion() ? $objAsignacion->getUsrCreacion() : "";
					if($usrAsignadoPor)
					{
						$empleado = $emComercial->getRepository('schemaBundle:InfoPersona')->getPersonaPorLogin($usrAsignadoPor);
						if($empleado && count($empleado)>0)
						{
							$nombresAsignadoPor = (($empleado->getNombres() && $empleado->getApellidos()) ? $empleado->getNombres() . " " . $empleado->getApellidos() : "");
						}
					}
				} 
				/*
				$idDepartamento = ""; $idEmpleado = ""; $nombreEmpleado = ""; $observacion = ""; $nombresAsignadoPor = "";
				$objAsignacion = $this->getUltimaAsignacionCasoByDetalleHipotesis($data->getId());
				if($objAsignacion && count($objAsignacion)>0)
				{
					$idDepartamento = $objAsignacion->getAsignadoId() ? $objAsignacion->getAsignadoId() : "";
					$idEmpleado = $objAsignacion->getRefAsignadoId() ? $objAsignacion->getRefAsignadoId() : "";
					$nombreEmpleado = $objAsignacion->getRefAsignadoNombre() ? $objAsignacion->getRefAsignadoNombre() : "";
					$observacion = $objAsignacion->getMotivo() ? $objAsignacion->getMotivo() : "";
					
					$usrAsignadoPor = $objAsignacion->getUsrCreacion() ? $objAsignacion->getUsrCreacion() : "";
					if($usrAsignadoPor)
					{
						$empleado = $emComercial->getRepository('schemaBundle:InfoPersona')->getPersonaPorLogin($usrAsignadoPor);
						if($empleado && count($empleado)>0)
						{
							$nombresAsignadoPor = (($empleado->getNombres() && $empleado->getApellidos()) ? $empleado->getNombres() . " " . $empleado->getApellidos() : "");
						}
					}
				} 
				*/
				
				$arrayValida = "";
				$arrayValida["id_hipotesis"] = $hipotesisData->getId();
				$arrayValida["id_empleado"] = $idEmpleado;
				$arrayValida["asignadoPor_asignacionCaso"] = strtolower($nombresAsignadoPor);
				$arrayValida["observacion"] = $observacion;
				$arrayValida["feAsignacion"] = $feAsignacion;
				
				if($this->multi_in_array($arrayValida, $arrayTodo))
				{		
					$cont++;
					
					$arrayHipotesis[] = $hipotesisData->getId();
					
					$arrayNow = "";
					$arrayNow["id_hipotesis"] = $hipotesisData->getId();
					$arrayNow["id_empleado"] = $idEmpleado;
					$arrayNow["asignadoPor_asignacionCaso"] = strtolower($nombresAsignadoPor);
					$arrayNow["observacion"] = $observacion;
					$arrayNow["feAsignacion"] = $feAsignacion;
					$arrayTodo[] = $arrayNow;
					
					$arr_encontrados[] = array(
							      'id_sintomaHipotesis'=> ($sintomaData ? ($sintomaData->getId() ? $sintomaData->getId() : "") : ""),
							      'nombre_sintomaHipotesis' =>($sintomaData ? ($sintomaData->getNombreSintoma() ? $sintomaData->getNombreSintoma() : "Ninguno") : "Ninguno"),
							      /*'criterios_sintomaHipotesis' =>$criteriosXSintoma,
							      'afectados_sintomaHipotesis' =>$afectadosXSintoma,*/
							      'id_hipotesis' =>($hipotesisData->getId() ? $hipotesisData->getId() : ""),
							      'nombre_hipotesis' =>($hipotesisData->getNombreHipotesis() ? $hipotesisData->getNombreHipotesis() : ""),
							      'criterios_hipotesis' =>"",
							      'afectados_hipotesis' =>"",
							      'departamento_asignacionCaso' =>$idDepartamento,
							      'nombreDepartamento_asignacionCaso' => ucwords(strtolower($nombreDepartamento)),
							      'oficina_asignacionCaso' => ($nombreOficina ? ucwords(strtolower($nombreOficina)) : "N/A"),
							      'empresa_asignacionCaso' => ($nombreEmpresa ? ucwords(strtolower($nombreEmpresa)) : "N/A"),
							      'empleado_asignacionCaso' => $idEmpleado,
							      'nombre_asignacionCaso' => ucwords(strtolower($nombreEmpleado)),
							      'observacion_asignacionCaso' =>$observacion,
							      'asignadoPor_asignacionCaso' => ucwords(strtolower($nombresAsignadoPor)),
							      'fecha_asignacionCaso' => ($feAsignacion ? $feAsignacion : "N/A"),
							      'asunto_asignacionCaso' =>"Autoasignacion del Caso",
							      'origen' =>"BD"
							);
				}
				
				
            }
            $data=json_encode($arr_encontrados);
            $resultado= '{"total":"'.$cont.'","encontrados":'.$data.'}';

            return $resultado;
        }
        else
        {
            $resultado= '{"total":"0","acciones":[]}';

            return $resultado;
        }
    }
    
	public function multi_in_array($valueArray, $arrayTotal)
	{
		if(count( $arrayTotal) > 0)
		{
			foreach ($arrayTotal AS $arrayUno)
			{
				if( $valueArray["id_hipotesis"] == $arrayUno["id_hipotesis"] &&
					$valueArray["id_empleado"] == $arrayUno["id_empleado"] &&
					$valueArray["asignadoPor_asignacionCaso"] == $arrayUno["asignadoPor_asignacionCaso"] &&
					$valueArray["observacion"] == $arrayUno["observacion"] &&
					$valueArray["feAsignacion"] == $arrayUno["feAsignacion"])
				{
					return false;
				}
			}
			return true;
		}
		else
		{
			return true;
		}	
	} 

    public function generarJsonHipotesisXCaso_Tarea($id_caso='', $emComercial='',$idPunto='',$emI='')
    {
        $arr_encontrados = array();
      
        $qb = $this->_em->createQueryBuilder();
        
        $qb->select('detalleHipotesis')
		->from('schemaBundle:InfoDetalleHipotesis', 'detalleHipotesis')
		->where('detalleHipotesis.casoId = ?1')
		->setParameter(1, $id_caso)
		->andWhere('detalleHipotesis.hipotesisId is not null')
		->orderBy('detalleHipotesis.id','asc');
		
        $query = $qb->getQuery();
        $rs = $query->getResult();
        
        if(isset($rs))
        {
            $num = count($rs);
            foreach ($rs as $entidad)
            {
				$sintomaData = $entidad->getSintomaId();
				$hipotesisData = $entidad->getHipotesisId();
				
				$idDepartamento = ""; $nombreDepartamento = ""; $idEmpleado = ""; $nombreEmpleado = ""; 
				$observacion = ""; $nombresAsignadoPor = ""; $feAsignacion = '';				
				$nombreOficina = ""; $nombreEmpresa = "";
				
				$objAsignacion = $this->getUltimaAsignacionCasoByDetalleHipotesis($entidad->getId());
				if($objAsignacion && count($objAsignacion)>0)
				{
					$idDepartamento = $objAsignacion->getAsignadoId() ? $objAsignacion->getAsignadoId() : "";
					$nombreDepartamento = $objAsignacion->getAsignadoNombre() ? $objAsignacion->getAsignadoNombre() : "";
					$idEmpleado = $objAsignacion->getRefAsignadoId() ? $objAsignacion->getRefAsignadoId() : "";
					$nombreEmpleado = $objAsignacion->getRefAsignadoNombre() ? $objAsignacion->getRefAsignadoNombre() : "";
					$observacion = $objAsignacion->getMotivo() ? $objAsignacion->getMotivo() : "";
					
					if($objAsignacion->getPersonaEmpresaRolId())
					{
						$InfoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->findOneById($objAsignacion->getPersonaEmpresaRolId());
						
						if($InfoPersonaEmpresaRol && count($InfoPersonaEmpresaRol)>0)
						{
							$oficinaEntity = $InfoPersonaEmpresaRol->getOficinaId();
							$empresaEntity = $oficinaEntity->getEmpresaId();
							
							$nombreOficina = ($oficinaEntity ? ($oficinaEntity->getNombreOficina() ? $oficinaEntity->getNombreOficina() : "") : "");
							$nombreEmpresa = ($empresaEntity ? ($empresaEntity->getNombreEmpresa() ? $empresaEntity->getNombreEmpresa() : "") : "");
						}
					}
					
					$feAsignacion = $objAsignacion->getFeCreacion() ? date_format($objAsignacion->getFeCreacion(), "d-m-Y G:i") : "";
					
					$usrAsignadoPor = $objAsignacion->getUsrCreacion() ? $objAsignacion->getUsrCreacion() : "";
					if($usrAsignadoPor)
					{
						$empleado = $emComercial->getRepository('schemaBundle:InfoPersona')->getPersonaPorLogin($usrAsignadoPor);
						if($empleado && count($empleado)>0)
						{
							$nombresAsignadoPor = (($empleado->getNombres() && $empleado->getApellidos()) ? $empleado->getNombres() . " " . $empleado->getApellidos() : "");
						}
					}
				} 
						
				
                $arr_encontrados[]=array(
					'id_sintoma'=> ($sintomaData ? ($sintomaData->getId() ? $sintomaData->getId() : "") : ""),
					'nombre_sintoma' =>($sintomaData ? ($sintomaData->getNombreSintoma() ? $sintomaData->getNombreSintoma() : "Ninguno") : "Ninguno"),
					'id_hipotesis' =>($hipotesisData->getId() ? $hipotesisData->getId() : ""),
					'nombre_hipotesis' =>($hipotesisData->getNombreHipotesis() ? $hipotesisData->getNombreHipotesis() : ""),
					'departamento_asignacionCaso' =>$idDepartamento,
					'nombreDepartamento_asignacionCaso' => ucwords(strtolower($nombreDepartamento)),
					'oficina_asignacionCaso' => ($nombreOficina ? ucwords(strtolower($nombreOficina)) : "N/A"),
					'empresa_asignacionCaso' => ($nombreEmpresa ? ucwords(strtolower($nombreEmpresa)) : "N/A"),
					'empleado_asignacionCaso' => $idEmpleado,
					'nombre_asignacionCaso' => ucwords(strtolower($nombreEmpleado)),
					'observacion_asignacionCaso' =>$observacion,
					'asignadoPor_asignacionCaso' => ucwords(strtolower($nombresAsignadoPor)),
					'fecha_asignacionCaso' => ($feAsignacion ? $feAsignacion : "N/A"),
					'asunto_asignacionCaso' =>"Autoasignacion del Caso",
					'origen' =>"BD"											
				);		
            }
            $data=json_encode($arr_encontrados);
            $resultado= '{"total":"'.$num.'","encontrados":'.$data.'}';

            return $resultado;
        }
        else
        {
            $resultado= '{"total":"0","acciones":[]}';

            return $resultado;
        }
    }

    /**
     * Funcion que retorna las tareas del caso
     *
     * @version 1.0
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 30-05-2016 Se realizan ajustes para incluir el Login Afectado al finalizar la tarea
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 08-07-2016 - Se realizan ajustes para determinar si se presenta la opcion de seleccionar coordenadas en la herramienta de
     *                           Finalizar Tarea
     *
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 20-11-2016 - Se agrega la acción de anular cuando corresponda en lugar de mostrar la opción rechazar
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.4 14-09-2017 - Se retira la accion de aceptar tarea desde los casos
     *
     * @author modificado Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.5 14-06-2018 Se realizan ajustes para identificar si la tarea proviene de Hal.
     *
     * @author modificado Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.6 29-08-2018 -  Se agrega una nueva validación para identificar las tareas con la característica ATENDER_ANTES.
     *
     * @author modificado Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.7 24-04-2019 -  Se modifica la validación de la característica ATENDER_ANTES, por motivos que no se estaba
     *                            filtrando de manera correcta y mostrada una información erronea.
     *
     * @param array $id_caso
     * @param array $emInfraestructura
     * @param array $session
     * @param array $emComercial
     * @param array $agregarTareas
     *
     * @return array $resultado
     */
    public function generarJsonTareasXCaso($id_caso,$emInfraestructura, $session,$emComercial,$agregarTareas='N')
    {
        $arr_encontrados    = array();
        $cod_empresa_caso   = "";
        $string_clientes    = "";
        $idsDeps            = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                          ->getDepartamentosRolXLoginEmpleado($session->get('user'));

        foreach($idsDeps as $id)
        {

            $ids[] = $id['departamentoId'];
        }

        if($id_caso)
        {
            $qb = $this->_em->createQueryBuilder();
            $qb->select('detalle')
               ->from('schemaBundle:InfoDetalle','detalle')
			   ->from('schemaBundle:InfoDetalleHipotesis', 'detalleHipotesis')
	           ->where('detalleHipotesis.casoId = ?1')
               ->setParameter(1, $id_caso)
			   ->andWhere('detalle.detalleHipotesisId = detalleHipotesis.id')
               ->andWhere('detalle.tareaId is not null')
               ->orderBy('detalle.id','asc');
            $query = $qb->getQuery();
            $rs = $query->getResult();
        }

        $detalleInicial = $this->getDetalleInicialCaso($id_caso);

        if(isset($rs))
        {
            $entidad_caso=$this->_em->getRepository('schemaBundle:InfoCaso')->find($id_caso);
            if($entidad_caso)
            {
                $cod_empresa_caso =$entidad_caso->getEmpresaCod();
            }
            $num = count($rs);
            foreach ($rs as $entidad)
            {
                $estado = $this->getUltimoEstado($entidad->getId());
                
                if($detalleInicial[0]["detalleInicial"])
                {
                    $parteAfectada = $this->_em->getRepository('schemaBundle:InfoParteAfectada')
                                               ->findByDetalleId($detalleInicial[0]["detalleInicial"]);

                    if(count($parteAfectada) > 0)
                    {
                        $string_clientes = $parteAfectada[0]->getAfectadoNombre() ? $parteAfectada[0]->getAfectadoNombre() : '';
                    }
                }
                $tramo = $this->_em->getRepository('schemaBundle:InfoDetalleTareaTramo')->findOneByDetalleId($entidad->getId());
                if($tramo)
                {
                    $infoTramo = $emInfraestructura->getRepository("schemaBundle:InfoTramo")->find($tramo->getTramoId());
                    $elementoA = $emInfraestructura->getRepository("schemaBundle:InfoElemento")->find($infoTramo->getElementoAId());
                    $nombreA = explode(".", $elementoA->getNombreElemento());
                    $elementoB = $emInfraestructura->getRepository("schemaBundle:InfoElemento")->find($infoTramo->getElementoBId());
                    $nombreB = explode(".", $elementoB->getNombreElemento());
                    $tipo = "Tramo";
                    $nombreTipo = $nombreA[0] . '-' . $nombreB[0];
                }
                else
                {

                    $nombreTipo = "";

                    $InfoDetalleTareaElemento = $this->_em->getRepository('schemaBundle:InfoDetalleTareaElemento')->findOneByDetalleId($entidad->getId());
                    if($InfoDetalleTareaElemento && count($InfoDetalleTareaElemento) > 0)
                    {

                        $elemento = $emInfraestructura->getRepository("schemaBundle:InfoElemento")->find($InfoDetalleTareaElemento->getElementoId());
                        $nombreTipo = ($elemento ? ($elemento->getNombreElemento() ? $elemento->getNombreElemento() : "") : "");

                        /*                         * ********************************************
                          Obtener el Tipo de Elemento
                         * ********************************************* */

                        $modeloElemento = $emInfraestructura->getRepository("schemaBundle:AdmiModeloElemento")
                            ->find($elemento->getModeloElementoId()->getId());

                        $tipoElemento = $emInfraestructura->getRepository("schemaBundle:AdmiTipoElemento")
                            ->find($modeloElemento->getTipoElementoId()->getId());

                        $idTipoElemento = $tipoElemento->getId();
                        $nombreTipoElemento = $tipoElemento->getNombreTipoElemento();
                    }
                }



                $infoDetalleHipotesis = $this->_em->getRepository('schemaBundle:InfoDetalleHipotesis')->findOneById($entidad->getDetalleHipotesisId());
                $asignacion     = $this->_em->getRepository('schemaBundle:InfoDetalleAsignacion')->findOneByDetalleId($entidad->getId());
                
                //Se obtiene la ultima asignacion
                $ultimaAsignacion     = $this->_em->getRepository('schemaBundle:InfoDetalleAsignacion')->getUltimaAsignacionTarea($entidad->getId());

            
                 /*************************************************************************************************
			  OBTENER EL DETALLE HISTORIAL PARA HORAS DE CIERRE POR EMPLEADO CON TAREA ASIGNADA
                ***************************************************************************************************/
                               
                                
                $fechaAsignacion = $this->getUltimaFechaAsignacion($entidad->getId(), $asignacion->getRefAsignadoId(), $asignacion->getAsignadoId());

                if($fechaAsignacion[0]['fecha'] != "")
                    $fechaEjecucion = $fechaAsignacion[0]['fecha'];
                else
                {
                    $fechaEjecucion = "";
                }

                if($fechaEjecucion != "")
                {
                    $fecha = explode(" ", $fechaEjecucion);
                    $fech = explode("-", $fecha[0]);
                    $hora = explode(":", $fecha[1]);
                    $fechaEjecucion = $fech[2] . "-" . $fech[1] . "-" . $fech[0];
                    $horaEjecucion = $hora[0] . ":" . $hora[1];
                }
                else
                {
                    $fechaEjecucion = "";
                    $horaEjecucion = "";
                }

                $fechaEjecucionTotal = $fechaEjecucion . " " . $horaEjecucion;

                if($session->get('idDepartamento') == $asignacion->getAsignadoId())
                    $esDepartamento = true;
                else
                    $esDepartamento = false;

                if($asignacion->getTipoAsignado() == "CUADRILLA")
                    $esDepartamento = true;

                $tareasAbiertas = $this->esUltimaTareaAbierta($id_caso);

                $numeroTareas = $this->_em->getRepository('schemaBundle:InfoCaso')->getCountTareasAbiertas($id_caso, 'Todas');


                $parametroTarea     = "";
                $mostrarCoordenadas = "N";
                $tareasManga        = "N";
                if($entidad->getTareaId()->getId())
                {
                    $parametroTarea = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                                                ->getOne('TAREAS_PERMITIDAS_INGRESAR_COORDENADAS','SOPORTE','CASO','TAREA SELECCIONAR COORDENADA',
                                                         $entidad->getTareaId()->getId(),'','','','','');
                    if($parametroTarea)
                    {
                        if ($entidad->getTareaId()->getId() == $parametroTarea["valor1"])
                        {
                            $mostrarCoordenadas = "S";

                            if($mostrarCoordenadas == "S" && $parametroTarea["valor3"] == "S")
                            {
                                $tareasManga = "S";
                            }
                        }
                    }
                }
                
                $boolVerAnularTarea     = false;
                
                    
                $arrayRespuestaAsignacionesTareaAnulada = $this->_em->getRepository('schemaBundle:InfoDetalleAsignacion')
                                                                    ->getAsignacionesVerTareaAnulada( array( "intIdDetalle"     => 
                                                                                                             $entidad->getId(),
                                                                                                             "intIdRefAsignado" => 
                                                                                                             $session->get('id_empleado')
                                                                                                           )
                                                                                                    );

                /* Se valida que el botón de anular tarea sólo aparezca cuando la tarea se haya autoasignado a la persona que la creó
                 * y que a dicha tarea solo se le haya realizado una asignación
                 * Si existiera más de una asignación, el botón que deberá aparecer en lugar del botón anular sería el botón de rechazar
                 */
                if($estado=="Asignada" 
                    && (isset($arrayRespuestaAsignacionesTareaAnulada['resultado']) && !empty($arrayRespuestaAsignacionesTareaAnulada['resultado']))
                    && (isset($arrayRespuestaAsignacionesTareaAnulada['total']) && !empty($arrayRespuestaAsignacionesTareaAnulada['total']) 
                        && $arrayRespuestaAsignacionesTareaAnulada['total']==1)
                    )
                {
                    $boolVerAnularTarea = true;
                }

                // Verificamos si la tarea proviene de hal
                $objInfoComunicacion = $this->_em->getRepository('schemaBundle:InfoComunicacion')
                    ->findOneBy(array ('detalleId' => $entidad->getId()),
                                array ('id'        => 'ASC'));

                $boolEsHal        = false;
                $boolAtenderAntes = false;
                if (is_object($objInfoComunicacion))
                {
                    $arrayResulExisteHal = $this->_em->getRepository('schemaBundle:InfoCuadrillaPlanifCab')
                        ->tareaExisteEnHal(array ('intNumeroTarea' => $objInfoComunicacion->getId(),
                                                  'strEstadoCab'   => 'Activo',
                                                  'strEstadoDet'   => 'Activo'));

                    if (!empty($arrayResulExisteHal) && count($arrayResulExisteHal) > 0
                        && $arrayResulExisteHal['resultado'] === 'ok')
                    {
                        $boolEsHal = $arrayResulExisteHal['existeTarea'];
                    }

                    //Verificamos si la tarea tiene característica ATENDER_ANTES Activa.
                    $objAdmiCaracteristicaAA = $this->_em->getRepository('schemaBundle:AdmiCaracteristica')
                                    ->findOneBy(array ('descripcionCaracteristica' => 'ATENDER_ANTES',
                                                       'estado'                    => 'Activo'));

                    if (is_object($objAdmiCaracteristicaAA))
                    {
                        $objInfoTareaCaracteristicaAA = $this->_em->getRepository('schemaBundle:InfoTareaCaracteristica')
                                ->findOneBy( array('tareaId'          => $objInfoComunicacion->getId(),
                                                   'caracteristicaId' => $objAdmiCaracteristicaAA->getId(),
                                                   'valor'            => 'S',
                                                   'estado'           => 'Activo'));

                        if (is_object($objInfoTareaCaracteristicaAA))
                        {
                            $boolAtenderAntes = true;
                        }
                    }
                }

                /*****************************************************/
                $arr_encontrados[]=array('id_sintomaTarea'       =>$entidad->getId(),
                                         'nombre_sintomaTarea'   =>($infoDetalleHipotesis->getSintomaId())?
                                                                    $infoDetalleHipotesis->getSintomaId()->getNombreSintoma():'Ninguno',
                                         'id_hipotesisTarea'     =>($infoDetalleHipotesis->getHipotesisId())?
                                                                    $infoDetalleHipotesis->getHipotesisId()->getNombreHipotesis():'',
                                         'nombre_hipotesisTarea' =>($infoDetalleHipotesis->getHipotesisId())?
                                                                    $infoDetalleHipotesis->getHipotesisId()->getNombreHipotesis():'Ninguno',
                                         'id_tarea'              =>$entidad->getTareaId()->getId(),
                                         'mostrarCoordenadas'    =>$mostrarCoordenadas,
                                         'nombre_tarea'          =>$entidad->getTareaId()->getNombreTarea(),
                                         'idTipoElemento'        =>$idTipoElemento,
                                         'nombreTipoElemento'    =>$nombreTipoElemento,
                                         'idTramo'               =>'',
                                         'nombreTipo'            =>$nombreTipo,
                                         'id_asignado'           =>($asignacion)?$asignacion->getAsignadoNombre():'',
                                         'id_refAsignado'        =>($asignacion)?$asignacion->getRefAsignadoNombre():'',
                                         'tipoAsignado'          =>($ultimaAsignacion[0]['asignado'])?$ultimaAsignacion[0]['asignado']:'',
                                         'id_cuadrilla'          =>($ultimaAsignacion[0]['asignadoId'])?$ultimaAsignacion[0]['asignadoId']:'',
                                         'observacion'           =>'',
                                         'casoPerteneceTN'       =>$cod_empresa_caso == "10" ? true : false,
                                         'tareasManga'           =>$tareasManga,
                                         'fechaEjecucion'        =>$fechaEjecucion,
                                         'horaEjecucion'         =>$horaEjecucion,
                                         'clientes'              =>$string_clientes,
                                         'fechaEjecucionTotal'   =>$fechaEjecucionTotal,
                                         'estado'                =>($estado)?$estado:"Asignada",
					                     'tareasAbiertas'        =>$tareasAbiertas[0]['numeroTareas'],
					                     'numero_tareas'         =>$numeroTareas,
					                     'id_caso'               =>$id_caso,
                                         'action0'               => "button-grid-verAsignado",
                                         'action1'               => ($estado=="Aceptada" || $estado=="Reprogramada")?'button-grid-agregarSeguimiento':
                                                                    "icon-invisible",
                                         'action2'               => ( ($estado=="Aceptada" || $estado=="Reprogramada") && $esDepartamento)?
                                                                    'button-grid-finalizarTarea':"icon-invisible",
                                         'action4'               => ( ($estado=="Aceptada" || $estado=="Reprogramada") && $esDepartamento)?
                                                                    'button-grid-reprogramarTarea':"icon-invisible",
                                         'action5'              => (($estado=="Aceptada" || $estado=="Reprogramada") && $esDepartamento)?
                                                                    'button-grid-rechazarTarea':"icon-invisible",
                                         'action8'              => $boolVerAnularTarea ? 'button-grid-rechazarTarea' : "icon-invisible",
                                         'tareaEsHal'           => $boolEsHal,
                                         'esHal'                => ($boolEsHal ? 'SI' : 'NO'),
                                         'atenderAntes'         => ($boolAtenderAntes ? 'SI' : 'NO')
                                        );
            }
            $data=json_encode($arr_encontrados);
            $resultado= '{"total":"'.$num.'","encontrados":'.$data.'}';

            return $resultado;
        }
        else
        {
            $resultado= '{"total":"0","acciones":[]}';

            return $resultado;
        }
    }
    
    /**     
     *
     * Documentación para el método 'getUltimaFechaAsignacion'.
     *
     * Devuelva la fecha de asignacion de cada tarea por persona o cuadrilla
     *
     * @return Result
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 13-10-2015 ( Modificacion de query confirmado busqueda por referencia o por asignado ( Cuadrillas, personal externo ))
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 Version Inicial    
     */   
    public function getUltimaFechaAsignacion($detalleId, $refAsignadoId, $asignadoId)
    {	  
        $strAndWhere = "";

        $query = $this->_em->createQuery();

        if($refAsignadoId != null || $refAsignadoId!=0)
        {
            $strAndWhere = " b.refAsignadoId = :refAsignadoId ";
            $query->setParameter("refAsignadoId", $refAsignadoId);
        }
        else if($asignadoId != null)
        {
            $strAndWhere = " b.asignadoId = :asignadoId ";
            $query->setParameter("asignadoId", $asignadoId);
        }
        $strSql = "SELECT max(a.feSolicitada) as fecha                      
                FROM 
                schemaBundle:InfoDetalle a, 
                schemaBundle:InfoDetalleAsignacion b        
                WHERE                 
                a.id = :detalle and
                a.id = b.detalleId and
                $strAndWhere
               ";

        $query->setParameter("detalle", $detalleId);
        $query->setDQL($strSql);

        $rs = $query->getResult();
        return $rs;
    }
    
     public function getUltimoEstadoDetalle($detalleId){
	  
             $sql = "SELECT a.estado                   
                FROM 
                schemaBundle:InfoDetalleHistorial a                   
                WHERE                 
                a.id =  (select max(b.id) from schemaBundle:InfoDetalleHistorial b where b.detalleId = $detalleId)                
               ";      
               
             
               $query = $this->_em->createQuery($sql);               	
             
		$rs = $query->getResult();
                      
            
            return $rs;

    }
    /*
	OBTENGO ULTIMA ACEPTACION DE LA TAREA PARA CALCULO DE TIEMPO TOTAL DE LA MISMA
    */
     public function getFechaAceptacionTarea($detalleId,$tieneReprogramacion=false){
	  
	    if($tieneReprogramacion) $select = "SELECT max(a.feCreacion) as fecha ";
	    else $select = "SELECT min(a.feCreacion) as fecha ";
	  
             $sql = $select."                   
                FROM 
                schemaBundle:InfoDetalleHistorial a                
                WHERE                 
                a.detalleId = $detalleId and a.estado = 'Aceptada'             
               ";      
               
             
               $query = $this->_em->createQuery($sql);
             
		$rs = $query->getResult();
                      
            
            return $rs;

    }
    
    public function getNumeroAceptacionesTarea($detalleId,$estado){
	  
             $sql = "SELECT count(a) as cont
                FROM 
                schemaBundle:InfoDetalleHistorial a                
                WHERE                 
                a.detalleId = $detalleId and a.estado = '$estado'            
               ";      
               
             
               $query = $this->_em->createQuery($sql);
             
		$rs = $query->getResult();
                      
            
            return $rs;

    }
    
     public function getMotivoUltimaReprogramacion($detalleId){
	  
             $sql = "SELECT a.motivo                     
                FROM 
                schemaBundle:InfoDetalleHistorial a  
                where              
                a.detalleId = $detalleId and a.estado = 'Reprogramada'  
                and a.id in (select max(b.id) from  schemaBundle:InfoDetalleHistorial b  
                where              
                b.detalleId = $detalleId and b.estado = 'Reprogramada' )             
               ";                     
             
               $query = $this->_em->createQuery($sql);
                          
		$rs = $query->getResult();
                      
            
            return $rs;

    }
    
    public function getUltimoEstado($idDetalle){
       $qb = $this->_em->createQueryBuilder();
            $qb->select('detalleHistorial')
               ->from('schemaBundle:InfoDetalleHistorial','detalleHistorial')
               ->where('detalleHistorial.detalleId = ?1')
               ->setParameter(1, $idDetalle)
               ->orderBy('detalleHistorial.id','desc');
            $query = $qb->getQuery();
            $rs = $query->getResult();
            
            if(count($rs)>0)
                return $rs[0]->getEstado();
            else
                return "";
    }
    
       public function getUltimoEstadoCaso($idCaso){
       $qb = $this->_em->createQueryBuilder();
            $qb->select('detalleHistorial')
               ->from('schemaBundle:InfoCasoHistorial','detalleHistorial')
               ->where('detalleHistorial.casoId = ?1')
               ->setParameter(1, $idCaso)
               ->orderBy('detalleHistorial.id','desc');
            $query = $qb->getQuery();
            $rs = $query->getResult();
            
            if(count($rs)>0)
                return $rs[0]->getEstado();
            else
                return "";
    }
     
    public function generarJsonVerTareasAsignadas($em, $start,$limit, $idDetalleSolicitud)
    {
        $arr_encontrados = array();
        $registrosTotal = $this->getRegistrosTareasAsignadas('', '', $idDetalleSolicitud);
        $registros = $this->getRegistrosTareasAsignadas($start, $limit, $idDetalleSolicitud);
        
        if ($registros) {
            $num = count($registrosTotal);            
            foreach ($registros as $data)
            {                                                   
                $nombreProceso =  ($data["nombreProceso"] ? $data["nombreProceso"]  : ""); 
                $nombreTarea =  ($data["nombreTarea"] ? $data["nombreTarea"]  : "");  
                $coordenadas = $data["longitud"] . ", ". $data["latitud"];              
                $latitud =  ($data["latitud"] ? $data["latitud"]  : "");  
                $longitud =  ($data["longitud"] ? $data["longitud"]  : "");  
                
                $idAsignado = 0; $nombreAsignado = "No Asignado";
                $ref_idAsignado = 0; $ref_nombreAsignado = "No Asignado";
                $infoAsignaciones = $em->getRepository("schemaBundle:InfoDetalleAsignacion")->getUltimaAsignacion($data["idDetalle"]);
                if($infoAsignaciones)
                {
                    $idAsignado = $infoAsignaciones->getAsignadoId();
                    $nombreAsignado = $infoAsignaciones->getAsignadoNombre();
                    $ref_idAsignado = $infoAsignaciones->getRefAsignadoId();
                    $ref_nombreAsignado = $infoAsignaciones->getRefAsignadoNombre();
                }
                
                $arr_encontrados[]=array(
                                         'id_info_detalle' =>$data["idDetalle"],
                                         'id_detalle_solicitud' =>$data["idDetalleSolicitud"],
                                         'id_proceso' =>$data["idProceso"],
                                         'id_tarea' =>$data["idTarea"],
                                         'id_asignado' =>$idAsignado,
                                         'ref_id_asignado' =>$ref_idAsignado,
                                         'nombre_proceso' =>trim($nombreProceso),
                                         'nombre_tarea' =>trim($nombreTarea),
                                         'nombre_asignado' =>trim($nombreAsignado),
                                         'ref_nombre_asignado' =>trim($ref_nombreAsignado),
                                         'coordenadas' =>trim($coordenadas),
                                         'latitud' =>trim($latitud),
                                         'longitud' =>trim($longitud)
                                        );
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_info_detalle' => 0 , 'id_detalle_solicitud' => 0 , 'id_proceso' => 0 ,
                                                        'id_tarea' => 0 , 'id_asignado' => 0 , 'nombre_proceso' => "Ninguno",
                                                        'nombre_tarea' => 'Ninguno', 'nombre_asignado' => 'Ninguno', 
                                                        'coordenadas' => 'Ninguno', 'latitud' => 'Ninguno', 'longitud' => 'Ninguno', 
                                                        'factibilidad_id' => 0 , 'factibilidad_nombre' => 'Ninguno', 'estado' => 'Ninguno'));
                $resultado = json_encode( $resultado);
                return $resultado;
            }
            else
            {
                $dataF =json_encode($arr_encontrados);
                $resultado= '{"total":"'.$num.'","encontrados":'.$dataF.'}';
                return $resultado;
            }
        }
        else
        {
            $resultado= '{"total":"0","encontrados":[]}';
            return $resultado;
        }
    }
    
    public function getRegistrosTareasAsignadas($start, $limit, $idDetalleSolicitud)
    {
        $boolBusqueda = false; 
        
        $sql = "SELECT 
                d.detalleSolicitudId as idDetalleSolicitud, d.id as idDetalle, 
                t.id as idTarea, pr.id as idProceso, 
                t.nombreTarea, pr.nombreProceso, d.longitud, d.latitud 
        
                FROM 
                schemaBundle:InfoDetalle d, 
                schemaBundle:AdmiTarea t, schemaBundle:AdmiProceso pr
        
                WHERE d.tareaId = t.id 
                AND pr.id = t.procesoId  
                AND d.detalleSolicitudId = '$idDetalleSolicitud' 
                 
               ";      
        
        $query = $this->_em->createQuery($sql);
        
        if($start!='' && !$boolBusqueda && $limit!='')
            $datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
        else if($start!='' && !$boolBusqueda && $limit=='')
            $datos = $query->setFirstResult($start)->getResult();
        else if(($start=='' || $boolBusqueda) && $limit!='')
            $datos = $query->setMaxResults($limit)->getResult();
        else
            $datos = $query->getResult();
        
        return $datos;
    }
       
    public function getOneDetalleByDetalleSolicitudTarea($idDetalleSolicitud, $idTarea)
    {
        $sql = "SELECT d         
                FROM schemaBundle:InfoDetalle d, schemaBundle:AdmiTarea t, schemaBundle:AdmiProceso pr         
                WHERE d.tareaId = t.id 
                AND pr.id = t.procesoId  
                AND d.detalleSolicitudId = '$idDetalleSolicitud' 
                AND t.id = '$idTarea'                 
                ORDER BY d.id DESC
               ";           
        $query = $this->_em->createQuery($sql);   
        $datos = $query->setFirstResult(0)->setMaxResults(1)->getOneOrNullResult();          
        return $datos;
    }
    

    /**
     * getInvolucradosTarea
     *
     * Método que obtiene todos los departamentos involucrados en una tarea de un caso
     *
     * @param $idCaso
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 30-05-2016 Se realiza un ajuste para presentar tambien los departamentos que crearon la tarea
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 25-05-2016
     */
    public function getInvolucradosTarea($idCaso)
    {
        $query         = $this->_em->createQuery();
        $queryCreador  = $this->_em->createQuery();
        $sql  = " SELECT DISTINCT infoDetalleAsignacion.asignadoId ";
        $from = " FROM schemaBundle:InfoCaso infoCaso,
                  schemaBundle:InfoDetalleHipotesis infoDetalleHipotesis,
                  schemaBundle:InfoDetalle infoDetalle,
                  schemaBundle:InfoDetalleHistorial infoDetalleHistorial,
                  schemaBundle:InfoDetalleAsignacion infoDetalleAsignacion
                WHERE infoCaso.id               = infoDetalleHipotesis.casoId
                AND infoDetalle.detalleHipotesisId = infoDetalleHipotesis.id
                AND infoDetalleHistorial.detalleId = infoDetalle.id
                AND infoDetalleAsignacion.detalleId = infoDetalle.id
                AND infoCaso.id               = :casoId
                AND infoDetalleHistorial.estado      = :estadoTarea
                AND infoDetalle.tareaId            IS NOT NULL ";

        $sql = $sql . $from;

        $query->setParameter('casoId',$idCaso);
        $query->setParameter('estadoTarea',"Finalizada");

        $query->setDQL($sql);

        $arrayDatos = $query->getResult();


        $sqlCreador = " SELECT DISTINCT infoDetalleAsignacion.departamentoId asignadoId";

        $sqlCreador = $sqlCreador . $from;

        $queryCreador->setParameter('casoId',$idCaso);
        $queryCreador->setParameter('estadoTarea',"Finalizada");

        $queryCreador->setDQL($sqlCreador);

        $arrayDatosCreador = $queryCreador->getResult();

        $array = array_merge($arrayDatos,$arrayDatosCreador);

        return $array;
    }

    /**
     * getTiempoTotalTareasDeCasos
     *
     * Método que obtiene el tiempo total de los casos
     *
     * @param array $arrayParametros[ 'intIdCaso'   => ID del caso a consultar,
     *                                'strTipoRepo' => Tipo de Consulta
     *                              ]
     *
     * @return integer $intTiempoTotalCaso
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 11-11-2016
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 27-01-2017 Se modifica la consulta para los casos que tienen más de una hipótesis
     * 
     */
    public function getTiempoTotalTareasDeCasos($arrayParametros)
    {
        $objQuery    = $this->_em->createQuery();
        $strEstado   = "Finalizada";
        $strSqlSum   = " SELECT SUM( infoTareaTiempoParcial.valorTiempo ) ";

        $strSqlCount = " SELECT COUNT( infoTareaTiempoParcial.detalleId ) ";

        $strSql      = " FROM schemaBundle:InfoTareaTiempoParcial infoTareaTiempoParcial
                               WHERE infoTareaTiempoParcial.detalleId IN
                               (SELECT infoDetalle.id FROM schemaBundle:InfoDetalle infoDetalle
                                   WHERE infoDetalle.detalleHipotesisId IN (SELECT infoDetalleHipotesis.id
                                       FROM schemaBundle:InfoDetalleHipotesis infoDetalleHipotesis
                                       WHERE infoDetalleHipotesis.casoId = :paramCasoId)
                                       AND infoDetalle.tareaId IS NOT NULL) ";
        $strSqlEstado = " AND infoTareaTiempoParcial.estado = :paramEstado ";

        $objQuery->setParameter('paramCasoId',$arrayParametros["intIdCaso"]);

        if($arrayParametros["strTipoRepo"] == "SUM")
        {
            $strSql = $strSqlSum . $strSql . $strSqlEstado;
            $objQuery->setParameter('paramEstado',$strEstado);

            $objQuery->setDQL($strSql);
        }
        else if($arrayParametros["strTipoRepo"] == "COUNT")
        {
            $strSql = $strSqlCount . $strSql;
            $objQuery->setDQL($strSql);
        }

        $intTiempoTotalCaso = $objQuery->getSingleScalarResult();

        return $intTiempoTotalCaso;
    }

    /**
     * getVecesTareasIniciadas
     *
     * Método que obtiene el numero de veces que la tarea fue iniciada
     *
     * @param array $arrayParametros[ 'intDetalleId' => ID del detalle id de la tarea ]
     *
     * @return integer $intNumeroVecesIniciada
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 05-12-2016
     */
    public function getVecesTareasIniciadas($arrayParametros)
    {
        $objQuery               = $this->_em->createQuery();
        $strEstado              = "Iniciada";
        $intNumeroVecesIniciada = 0;

        $strSql      = " SELECT COUNT( infoTareaSeguimiento.id )
                            FROM schemaBundle:InfoTareaSeguimiento infoTareaSeguimiento
                                WHERE infoTareaSeguimiento.detalleId = :paramDetalleId
                                AND infoTareaSeguimiento.observacion like :paramObservacion ";

        $objQuery->setParameter('paramDetalleId',$arrayParametros["intDetalleId"]);
        $objQuery->setParameter('paramObservacion','%'.$strEstado.'%');

        $objQuery->setDQL($strSql);

        $intNumeroVecesIniciada = $objQuery->getSingleScalarResult();

        return $intNumeroVecesIniciada;
    }


     /**
     * getTareaIniciada
     *
     * Método que obtiene la primera vez que una tarea fue iniciada
     *
     * @param array $arrayParametros[ 'intDetalleId' => ID del detalle id de la tarea ]
     *
     * @return date $dateTareaFechaInicio
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 05-12-2016
     */
    public function getTareaIniciada($arrayParametros)
    {
        $objQuery             = $this->_em->createQuery();
        $strEstado            = "Iniciada";
        $dateTareaFechaInicio = "";

        $strSql      = " SELECT infoTareaTiempoParcia.feCreacion as fechaInicioTarea
                            FROM schemaBundle:InfoTareaTiempoParcial infoTareaTiempoParcia
                                WHERE infoTareaTiempoParcia.feCreacion = (
                                    SELECT MIN( infoTareaTiempoParcial.feCreacion ) feCreacion
                                        FROM schemaBundle:InfoTareaTiempoParcial infoTareaTiempoParcial
                                            WHERE infoTareaTiempoParcial.detalleId = :paramDetalleId
                                            AND infoTareaTiempoParcial.estado = :paramEstado ) ";

        $objQuery->setParameter('paramDetalleId',$arrayParametros["intDetalleId"]);
        $objQuery->setParameter('paramEstado',$strEstado);

        $objQuery->setDQL($strSql);

        $arrayFechaTareaIniciada = $objQuery->getResult();
        $dateTareaFechaInicio    = $arrayFechaTareaIniciada[0]["fechaInicioTarea"];

        return $dateTareaFechaInicio;
    }

    /**
     * getDetalleInicialCaso
     *
     * Método que obtiene el detalle_id inicial de un caso
     *
     * @param $idCaso
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 30-05-2016 Se realiza un ajuste para presentar tambien los departamentos que crearon la tarea
     */
    public function getDetalleInicialCaso($idCaso)
    {
        $query = $this->_em->createQuery();

        $sql   = " SELECT MIN( infoDetalle.id ) as detalleInicial
                    FROM  schemaBundle:InfoDetalleHipotesis infoDetalleHipotesis,schemaBundle:InfoDetalle infoDetalle
                    WHERE infoDetalleHipotesis.id = infoDetalle.detalleHipotesisId
                    AND infoDetalleHipotesis.casoId = :casoId ";

        $query->setParameter('casoId',$idCaso);

        $query->setDQL($sql);

        $arrayDatos = $query->getScalarResult();

        return $arrayDatos;
    }

    public function generarJsonVerHistorialTareasAsignadas($em, $start,$limit, $idDetalleSolicitud)
    {
        $arr_encontrados = array();
        $registrosTotal = $this->getRegistrosHistorialTareasAsignadas('', '', $idDetalleSolicitud);
        $registros = $this->getRegistrosHistorialTareasAsignadas($start, $limit, $idDetalleSolicitud);
        
        if ($registros) {
            $num = count($registrosTotal);   
            $globalNombreTarea = "";
            
            foreach ($registros as $data)
            {                                                   
                $nombreProceso =  ($data["nombreProceso"] ? $data["nombreProceso"]  : ""); 
                //$nombreTarea =  ($data["nombreTarea"] ? $data["nombreTarea"]  : "");  
                $coordenadas = $data["longitud"] . ", ". $data["latitud"];              
                $latitud =  ($data["latitud"] ? $data["latitud"]  : "");  
                $longitud =  ($data["longitud"] ? $data["longitud"]  : "");  
                
                if($globalNombreTarea != $data["nombreTarea"])
                {
                    $globalNombreTarea = $nombreTarea = $data["nombreTarea"];
                }
                else
                {
                    $nombreTarea = "";
                }
                
                $idAsignado =  ($data["asignadoId"] ? $data["asignadoId"]  : 0);  
                $nombreAsignado =  ($data["asignadoNombre"] ? $data["asignadoNombre"]  : "No Asignado");  
                $ref_idAsignado =  ($data["refAsignadoId"] ? $data["refAsignadoId"]  : 0);  
                $ref_nombreAsignado =  ($data["refAsignadoNombre"] ? $data["refAsignadoNombre"]  : "No Asignado");  
                $fechaAsignada = strval(date_format($data["feCreacion"], "d/m/Y h:i")); 
                                        
                $arr_encontrados[]=array(
                                         'id_info_detalle' =>$data["idDetalle"],
                                         'id_detalle_solicitud' =>$data["idDetalleSolicitud"],
                                         'id_proceso' =>$data["idProceso"],
                                         'id_tarea' =>$data["idTarea"],
                                         'id_asignacion' =>$data["idDetalleAsignacion"],
                                         'id_asignado' =>$idAsignado,
                                         'ref_id_asignado' =>$ref_idAsignado,
                                         'nombre_proceso' =>trim($nombreProceso),
                                         'nombre_tarea' =>trim($nombreTarea),
                                         'nombre_asignado' =>trim($nombreAsignado),
                                         'ref_nombre_asignado' =>trim($ref_nombreAsignado),
                                         'fecha_asignada' =>$fechaAsignada,
                                         'coordenadas' =>trim($coordenadas),
                                         'latitud' =>trim($latitud),
                                         'longitud' =>trim($longitud)
                                        );
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_info_detalle' => 0 , 'id_detalle_solicitud' => 0 , 'id_proceso' => 0 ,
                                                        'id_tarea' => 0 , 'id_asignado' => 0 , 'nombre_proceso' => "Ninguno",
                                                        'nombre_tarea' => 'Ninguno', 'nombre_asignado' => 'Ninguno', 
                                                        'coordenadas' => 'Ninguno', 'latitud' => 'Ninguno', 'longitud' => 'Ninguno', 
                                                        'factibilidad_id' => 0 , 'factibilidad_nombre' => 'Ninguno', 'estado' => 'Ninguno'));
                $resultado = json_encode( $resultado);
                return $resultado;
            }
            else
            {
                $dataF =json_encode($arr_encontrados);
                $resultado= '{"total":"'.$num.'","encontrados":'.$dataF.'}';
                return $resultado;
            }
        }
        else
        {
            $resultado= '{"total":"0","encontrados":[]}';
            return $resultado;
        }
    }
    
    public function getRegistrosHistorialTareasAsignadas($start, $limit, $idDetalleSolicitud)
    {
        $boolBusqueda = false; 
        
        $sql = "SELECT 
                d.detalleSolicitudId as idDetalleSolicitud, d.id as idDetalle, 
                t.id as idTarea, pr.id as idProceso, 
                t.nombreTarea, pr.nombreProceso, d.longitud, d.latitud,
                da.id as idDetalleAsignacion, da.asignadoId, da.asignadoNombre, 
                da.refAsignadoId, da.refAsignadoNombre, da.feCreacion 
        
                FROM 
                schemaBundle:InfoDetalle d, schemaBundle:InfoDetalleAsignacion da, 
                schemaBundle:AdmiTarea t, schemaBundle:AdmiProceso pr
        
                WHERE 
                d.tareaId = t.id 
                AND d.id = da.detalleId 
                AND pr.id = t.procesoId  
                AND d.detalleSolicitudId = '$idDetalleSolicitud' 
                 
                ORDER BY t.id ASC, da.id DESC
               ";      
        
        $query = $this->_em->createQuery($sql);
        
        if($start!='' && !$boolBusqueda && $limit!='')
            $datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
        else if($start!='' && !$boolBusqueda && $limit=='')
            $datos = $query->setFirstResult($start)->getResult();
        else if(($start=='' || $boolBusqueda) && $limit!='')
            $datos = $query->setMaxResults($limit)->getResult();
        else
            $datos = $query->getResult();
        
        return $datos;
    }
    
    
	/**
     * generarJsonMisTareas
     *
     * Método que obtiene los registros de todas las tareas segun filtros enviados por parametros
     *
     * @version 1.0 Version Inicial
     * 
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.5 16-01-2015 Se valida que efectivamente las tareas consultadas sean del departamento enviado como parametro
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.6 07-08-2015 - Se cambia la función getPersonaPorLogin por 
     *                           getDatosPersonaPorLogin para corregir problema
     *                           al exportar las tareas 
     * 
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.7 13-08-2015 - Mejora a la función para que acepte el total y los datos 
     *                           retornados en una sola llamada a la función 'getRegistrosMisTareas'. 
     * 
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.8 19-08-2015 - Se modifica añadiendo el campo 'descripcionInicial' al resultado
     *                           para conocer el motivo por el cual se abrió dicha tarea.
     * 
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.9 11-12-2015 - Se modifica para que se envie si la tarea tiene o no una solicitud de factura, y el tiempo que ha transcurrido
     *                           desde que la tarea se creo.
     * 
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.0 06-05-2015 - Se modifica por opcion Ver Archivos

     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.1 16-05-2016 - Se realizan ajustes para validar que no se puedan gestionar tareas de otros departamentos, cuando no se consulte
     *                           por departamento
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.2 25-05-2016 - Se realizan ajustepara presentar el ultimo estado del caso al que este relacionada la tarea
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.3 30-05-2016 - Se realizan ajustes para presentar el login afectado cuando es una tarea de un caso
     * 
     * @author Modificado: Allan Suarez <arsuarez@telconet.ec>
     * @version 2.4 20-06-2016 - Se realizan ajustes para tomar en cuenta validacion a tareas Rechazadas para que realice el calculo correcto
     *                           del tiempo a mostrar al usuario ( tiempo transcurrido )
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.5 23-06-2016 - Se realizan ajustes para que aparezca la opción de subir archivos al departamento creador sim importar el estado
     * de la tarea
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.6 30-06-2016 - Se agrega el campo Num. Tarea en el grid de tareas
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.7 08-07-2016 - Se realizan ajustes para determinar si se presenta la opcion de seleccionar coordenadas en la herramienta de
     *                           Finalizar Tarea
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.8 22-07-2016 - Se realizan ajustes para poder reutilizar esta funcion desde el modulo de Tareas
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.9 27-07-2016 - Se agrega el nombre del proceso al que pertenecen las tareas
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 3.0 09-08-2016 - Se realiza ajuste en el calculo del tiempo transcurrido de la tarea, debe de tomarse encuenta desde que
     *                           se crea la tarea hasta que la tarea esta abierta
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 3.1 15-08-2016 - Se realizan ajustes por cambios en la funcion getMinimaComunicacionPorDetalleId
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 3.2 19-09-2016 Se realizan ajustes para incluir el concepto de ingresar seguimientos internos
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 3.3 11-11-2016 Se realizan ajustes en el calculo del tiempo transcurrido, se presentara en base al nuevo esquema
     *                         de los botones de iniciar,pausar,reanudar tareas. Adicional cuando la tarea se encuentre Pausada
     *                         solo se habilitaran los botones de reprogramar tarea, reasignar tarea y de consulta.>
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 3.4 13-11-2016 Se ralizan ajustes para setear el campo numero de tarea padre en vacio
     *
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 3.5 19-11-2016 Se agrega la opción Anular Tarea cuando la tarea tiene una autoasignación y no existen más asignaciones
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 3.6 05-12-2016 Se realiza ajustes por cambios sobre: cuando se reasigne una tarea a un departamento distinto al actual
     *                         el estado de la tarea quedara Asignada caso contrario Aceptada
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 3.7 06-12-2016 Se realiza ajustes para mostrar la opcion de ingresar seguimientos en todos los estados por los que pasa
     *                         una tarea, siempre y cuando no este cerrada.
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 3.8 15-12-2016 Se realiza ajuste para que cuando se busquen tareas seleccionando el filtro por departamento, tambien
     *                         valide el departamento en session y con esto determinar si se tiene o no gestion sobre la tarea
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 3.9 13-01-2017 - Se realizan ajustes para determinar si se esta iniciando la ejecución de la tarea
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 4.0 02-02-2017 - Se modifica la función para verificar si la tarea ha sido reasignada automáticamente por cambio de departamento del
     *                           empleado
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 4.1 08-02-2017 - Se aumenta en el array de respuesta un boolean que indique si es o no una tarea que se encuentre parametrizada
     *                           para ver la información adicional, que en la actualidad serán tareas de tipo Incidencia, Auditoria o Mantenimiento
     *
     * @author modificado Richard Cabrera <rcabrera@telconet.ec>
     * @version 4.2 17-04-2017 - Se realizan ajustes para determinar si un empleado tiene una tarea en estado Aceptada
     *
     * @author modificado Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 4.3 26-02-2018 - Se obtiene el cliente afectado en base al punto.
     *
     * @author modificado Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 4.4 12-04-2018 - Se agrega una nueva validacion para considerar si la tarea proviene de hal o es una manual.
     *
     * @author modificado Richard Cabrera <rcabrera@telconet.ec>
     * @version 4.5 07-06-2018 -  Se realiza cambios para validar que solo el que solicita el reporte pueda finalizar la tarea
     *
     * @author modificado Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 4.6 14-06-2018 -  Se agrega un nuevo OR en la accion de reprogramacion, para mostrar el icono si la tarea es de hal.
     *
     * @author modificado Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 4.7 10-07-2018 -  Se agrega una nueva validación para considerar si la tarea se encuentra parametrizada y en caso
     *                            de estarlo se mostrará la pestaña de hal en la reasignación de tareas.
     *
     * @author modificado Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 4.8 09-08-2018 -  Se agrega un nuevo OR en la action4 para mostrar el ícono de reasignación si la tarea es de hal.
     *
     * @author modificado Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 4.9 29-08-2018 -  Se agrega una nueva validación para identificar las tareas con la característica ATENDER_ANTES.
     *
     * @author modificado Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 5.0 04-12-2018 -  Se agrega la fecha por defecto en caso que no exista ningún filtro por fecha.
     *
     * @author modificado Andrés Montero <amontero@telconet.ec>
     * @version 5.1 05-12-2018 -  Se agrega una nueva validación para mostrar acciones si es del mismo 
     * departamento o si tiene perfil verTareasTodasEmpresas.
     *
     * @author modificado Andrés Montero <amontero@telconet.ec>
     * @version 5.2 10-12-2018 -  Se valida que el número de tarea exista antes de obtener el prefijo de la empresa
     * departamento o si tiene perfil verTareasTodasEmpresas.
     * 
     * 
     * @author Modificado: Andrés Montero <amontero@telconet.ec>
     * @version 5.3 20-12-2018 - Se agrega condición para poder gestionar las tareas que si tiene perfil verTareasTodasEmpresas 
     *                           adicional el departamento asignado a la tarea debe ser igual a algún departamento
     *                           de las empresas a la que pertenece el usuario en sesión.
     *
     * @author modificado Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 5.4 12-12-2018 - Se valida que no se muestren los botones de gestión si la tarea fue creada vía WS por Sys Cloud Center.
     *
     * @author modificado Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 5.5 18-09-2018 - Se agrega una nueva validación para mostrar el botón reprogramar HAL si es que el departamento
     *                           se encuentra configurado.
     *
     * @author modificado Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 5.6 17-04-2019 - Se valida para que el estado "Reprogramar" tenga el mismo comportamiento que el estado "Asignada".
     *                         - Se agrega una nueva funcionalidad, para actualizar los estados de manera automática de aquellos estados
     *                           que ya cumplieron con su reprogramación.
     *
     * @author modificado Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 5.7 15-06-2019 - Se agrega un for para obtener los nombres de las cuadrillas.
     *
     * @author modificado Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 5.8 04-07-2019 - Se cambia la función 'getRegistrosMisTareas' por 'getTareasSoporte' encargada
     *                           de obtener las tareas desde la base de datos con un sysrefcursor.
     *                         - Se cambia la manera de recorrer las tareas devueltas por motivos que ahora es un record.
     *
     * @author modificado Ronny Morán Chancay <rmoranc@telconet.ec>
     * @version 5.9 20-09-2019 - Se agregan parámetros utilizados en el control de Fibra y materiales.
     *
     *
     * @author modificado Ronny Morán Chancay <rmoranc@telconet.ec>
     * @version 5.10 08-11-2019 - Se modifica orden de variable $strRequiereControlActivo.
     *   
     *
     * @author modificado Ronny Morán Chancay <rmoranc@telconet.ec>
     * @version 5.11 13-11-2019 - Se establece número de bobinas a visualizar para una tarea de instalación TN o Soporte.
     * @since 5.10
     *  
     * @author modificado Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 6.0 26-12-2019 - Se agrega el valor del ID_DETALLE_HISTORIAL de la tarea.
     *
     * 
     * @author modificado Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 7.0 10-06-2019 - Se agrega un nuevo parámetro para validar el botón de reintento de creación
     *                           de la tarea en el sistema de Sys Cloud Center de DC.
     * 
     * @author modificado Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 8.0 13-12-2019 - Se modifica para que no se puedan visualizar las acciones 
     *                           de gestión de una tarea para el departamento de data center.
     *
     *
     * @author modificado Ronny Morán <rmoranc@telconet.ec>
     * @version 9.0 31-03-2020 - Se agrega parámetro para indicar si la tarea es interdepartamental 
     *                           
     * 
     * @author modificado Ronny Morán <rmoranc@telconet.ec>
     * @version 10.0 09-07-2020 - Se realizan verificaciones para botón de confirmación del enlace en tareas 
     *                            de soporte Tn con última milla Fibra óptica 
     *
     *    
     * @param $parametros [$em,$emComunicacion,$parametros,$start,$limit,$isDepartamento,$departamentoSession,$existeFiltro]
     *
     * @author modificado Wilmer Vera <wvera@telconet.ec>
     * @version 11.0 29-07-2020 - Se realizan verificaciones para botón de validar servicio en tareas 
     *                            de soporte Tn con última milla Fibra óptica diferentes de backbone 
     *
     * @author modificado Wilmer Vera <wvera@telconet.ec>
     * @version 11.1 02-10-2020 - Se agrega variable idServicioVRf para uso de la validación de enlaces. 
     * Dicho idServicio corresponde al servicio activo del afectado.  
     * 
     * @author modificado Pedro Velez <psvelez@telconet.ec>
     * @version 11.2 02-10-2021 - Se agrega parametro en la respuesta del metodo 
     *                            donde se envia el id y nombre de la tarea anterior 
     * 
     * @param $parametros [$em,$emComunicacion,$parametros,$start,$limit,$isDepartamento,$departamentoSession,$existeFiltro]
     */
    public function generarJsonMisTareas($parametros)
    {
        $arr_encontrados = array();
        $arrayResultados = array();

        $em                  = $parametros["emComercial"];
        $emComunicacion      = $parametros["emComunicacion"];
        $isDepartamento      = $parametros["isDepartamento"];
        $departamentoSession = $parametros["departamentoSession"];
        $existeFiltro        = $parametros["existeFiltro"];
        $prefijoEmpresa      = $parametros["prefijoEmpresa"];
        $boolPermiteRegAct   = $parametros["permiteRegistroActivos"];
        $strLoginSesion      = $parametros["strUser"];
        $boolConfirIpSopTn   = $parametros["permiteConfirIpSopTn"]; 
        $boolValEnlaSopTn    = $parametros["permiteValidarEnlaceSopTn"];

        $nombre_proceso     = "";
        $nombreDepartamento = '';

        $parametros['nombreAsignado'] = '';
        $presentarSubtarea            = "icon-invisible";
        $cerrarTarea                  = "S";
        $strBanderaFinalizarInformeEjecutivo = "S";
        $intTareaPadre                = "";
        $strMostrarOpcionSeguiInterno = "N";
        $intMinutosTareaPausada       = 0;
        $intTiempoTareaPausada        = 0;
        $intMinutosInicio             = 0;
        $intPersonaEmpresaRol         = $parametros["intPersonaEmpresaRol"];
        
        //Se obtiene el nombre del departamento en el cual se esta buscando
        if($parametros["idDepartamento"] && $parametros["idCuadrilla"]==null)
        {
            $objDepartamento = $this->_em->getRepository('schemaBundle:AdmiDepartamento')->find($parametros["idDepartamento"]);
            if($objDepartamento)
            {
                $nombreDepartamento = $objDepartamento->getNombreDepartamento();
                $parametros['nombreAsignado'] = $nombreDepartamento;
            }
        }     

        //Se obtiene el nombre de la cuadrilla en la cual se esta buscando
        if($parametros["idCuadrilla"] && $parametros["idDepartamento"]==null && $parametros["idCuadrilla"]!='Todos')
        {
            foreach ($parametros["idCuadrilla"] as $intIdcuadrilla)
            {
                $objCuadrilla = $em->getRepository('schemaBundle:AdmiCuadrilla')->find($intIdcuadrilla);
                if(is_object($objCuadrilla))
                {
                    $parametros['nombreAsignado'][] = $objCuadrilla->getNombreCuadrilla();
                }
            }
        }

        //Obtenemos el parámetro de la fecha por defecto
        if ( (!isset($parametros["feFinalizadaHasta"]) || $parametros["feFinalizadaHasta"] === '') &&
             (!isset($parametros["feFinalizadaDesde"]) || $parametros["feFinalizadaDesde"] === '') &&
             (!isset($parametros["feSolicitadaHasta"]) || $parametros["feSolicitadaHasta"] === '') &&
             (!isset($parametros["feSolicitadaDesde"]) || $parametros["feSolicitadaDesde"] === ''))
        {
            $arrayFechaDefecto = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                    ->getOne('TAREAS_FECHA_DEFECTO','SOPORTE','','','','','','','','');

            if (!empty($arrayFechaDefecto) && count($arrayFechaDefecto) > 0 &&
                checkdate($arrayFechaDefecto['valor2'],$arrayFechaDefecto['valor3'],$arrayFechaDefecto['valor1']))
            {
                $strFechaDefecto = $arrayFechaDefecto['valor1'].'-'. //Año
                                   $arrayFechaDefecto['valor2'].'-'. //Mes
                                   $arrayFechaDefecto['valor3'];     //Día

                $parametros['strFechaDefecto'] = $strFechaDefecto;
            }
        }

        $arrayResultados = $this->getTareasSoporte($parametros);
        $intCantidad     = $arrayResultados['total'] ? $arrayResultados['total'] : 0;

        $arrayDptosEmpleadoEmpresas     = $parametros['arrayDepartamentos'];
        $booleanVerTareasTodasEmpresas  = $parametros['booleanVerTareasTodasEmpresa'];
        $booleanEsDptoAutorizadoGestion = false;
        $isDepartamento                 = true;

        if (!empty($arrayResultados) && $intCantidad > 0)
        {
            while ($arrayCsrResult = oci_fetch_array($arrayResultados['objCsrResult'], OCI_ASSOC + OCI_RETURN_NULLS))
            {
                $strRequiereControlActivo     = 'NO'; 
                $intNumBobinaVisualizar       = "";
                $strEstadoNumBobinaVisual     = "";
                
                if (is_object($arrayCsrResult['OBSERVACION_HISTORIAL']))
                {
                    $arrayCsrResult['OBSERVACION_HISTORIAL'] = $arrayCsrResult['OBSERVACION_HISTORIAL']->load();
                }

                if (is_object($arrayCsrResult['OBSERVACION']))
                {
                    $arrayCsrResult['OBSERVACION'] = $arrayCsrResult['OBSERVACION']->load();
                }

                if (is_object($arrayCsrResult['NOMBRE_TAREA']))
                {
                    $arrayCsrResult['NOMBRE_TAREA'] = $arrayCsrResult['NOMBRE_TAREA']->load();
                }

                if (is_object($arrayCsrResult['DESCRIPCION_TAREA']))
                {
                    $arrayCsrResult['DESCRIPCION_TAREA'] = $arrayCsrResult['DESCRIPCION_TAREA']->load();
                }

                $datos = array ('asignadoIdHis'           => $arrayCsrResult['ASIGNADO_ID_HIS'],
                                'departamentoOrigenIdHis' => $arrayCsrResult['DEPARTAMENTO_ORIGEN_ID'],
                                'idDetalle'               => $arrayCsrResult['ID_DETALLE'],
                                'latitud'                 => $arrayCsrResult['LATITUD'],
                                'longitud'                => $arrayCsrResult['LONGITUD'],
                                'usrCreacionDetalle'      => $arrayCsrResult['USR_CREACION_DETALLE'],
                                'detalleIdRelacionado'    => $arrayCsrResult['DETALLE_ID_RELACIONADO'],
                                'idTarea'                 => $arrayCsrResult['ID_TAREA'],
                                'nombreTarea'             => $arrayCsrResult['NOMBRE_TAREA'],
                                'descripcionTarea'        => $arrayCsrResult['DESCRIPCION_TAREA'],
                                'asignadoId'              => $arrayCsrResult['ASIGNADO_ID'],
                                'asignadoNombre'          => $arrayCsrResult['ASIGNADO_NOMBRE'],
                                'refAsignadoId'           => $arrayCsrResult['REF_ASIGNADO_ID'],
                                'refAsignadoNombre'       => $arrayCsrResult['REF_ASIGNADO_NOMBRE'],
                                'personaEmpresaRolId'     => $arrayCsrResult['PERSONA_EMPRESA_ROL_ID'],
                                'idDepartamentoCreador'   => $arrayCsrResult['DEPARTAMENTO_ID'],
                                'estado'                  => $arrayCsrResult['ESTADO'],
                                'usrTareaHistorial'       => $arrayCsrResult['USR_CREACION'],
                                'observacionHistorial'    => $arrayCsrResult['OBSERVACION_HISTORIAL'],
                                'tipoAsignado'            => $arrayCsrResult['TIPO_ASIGNADO'],
                                'observacion'             => $arrayCsrResult['OBSERVACION']);

                $feTareaCreada    = $arrayCsrResult['FE_CREACION_DETALLE'];
                $feSolicitada     = $arrayCsrResult['FE_SOLICITADA'];
                $feTareaAsignada  = $arrayCsrResult['FE_CREACION_ASIGNACION'];
                $feTareaHistorial = $arrayCsrResult['FE_CREACION'];
                $numeroTarea      = $arrayCsrResult['NUMERO_TAREA'];
                $nombre_proceso   = $arrayCsrResult['NOMBRE_PROCESO'];
                $intIdDetalleHist = $arrayCsrResult['ID_DETALLE_HISTORIAL'];

                $intDepartamentoAsignado = null;
                $isDepartamentoCreador   = false;

                if($datos["idDepartamentoCreador"] == $departamentoSession)
                {
                    $isDepartamentoCreador=true;
                }

                if($datos["tipoAsignado"] == "EMPLEADO")
                {
                    $intDepartamentoAsignado = $datos["asignadoId"];
                    if($datos["asignadoId"] != $departamentoSession)
                        $isDepartamento = false;
                    else
                        $isDepartamento = true;
                    //si tiene perfil verTodasTareasEmpresas
                    //busca si el departamento asignado se encuentra en los departamentos del empleado
                    if ($booleanVerTareasTodasEmpresas && in_array($datos["asignadoId"], $arrayDptosEmpleadoEmpresas))
                    {
                        $booleanEsDptoAutorizadoGestion = true;
                    }
                }
                else if($datos["tipoAsignado"] == "CUADRILLA")
                {
                    $personaEmpresaRol = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                            ->findOneBy(array("id"          => $datos["personaEmpresaRolId"],
                                                              "cuadrillaId" => $datos["asignadoId"],
                                                              "estado"      => "Activo"));
                    if($personaEmpresaRol)
                    {
                        if($personaEmpresaRol->getDepartamentoId() != $departamentoSession)
                            $isDepartamento = false;
                        else
                            $isDepartamento = true;
                    }
                    else
                    {
                        $isDepartamento = true;
                    }
                }
                $ClientesAfectados = $this->getRegistrosAfectadosTotal($datos["idDetalle"], 'Cliente', 'Data');
                $string_clientes = "";
                if($ClientesAfectados && count($ClientesAfectados) > 0)
                {
                    $arrayClientes = false;
                    foreach($ClientesAfectados as $clientAfect)
                    {
                        $arrayClientes[] = $clientAfect["afectadoNombre"];
                    }

                    $string_clientes_1 = implode(",", $arrayClientes);
                    $string_clientes   = "" . $string_clientes_1 . "";
                }
                if($string_clientes == "")
                {
                    $arrayParametroCliente = array(
                                                    'intIdDetalle'   =>  $datos["idDetalle"]
                                                    );
                    $objClienteAfectado = $this->getClienteAfectadoTarea($arrayParametroCliente);
                    if(is_object($objClienteAfectado))
                    {
                        $string_clientes = $objClienteAfectado->getLogin();
                    }
                }

                $strObsHistorial  = ($datos["observacionHistorial"] ? $datos["observacionHistorial"] : "");

                $nombreActualizadoPor = "";
                $usrTareaHistorial = ($datos["usrTareaHistorial"] ? $datos["usrTareaHistorial"] : "");

                if($usrTareaHistorial)
                {
                    $empleado = $em->getRepository('schemaBundle:InfoPersona')->getDatosPersonaPorLogin($usrTareaHistorial);
                    if($empleado && count($empleado) > 0)
                    {                               
                        $nombreActualizadoPor = sprintf("%s",$empleado);

                        if($nombreActualizadoPor)
                        {
                            $nombreActualizadoPor = ucwords(strtolower($nombreActualizadoPor));
                        }

                        /*
                         * Verificar si la observación del último historial coincide con el parámetro guardado para las tareas reasignadas
                         * automáticamente por cambio de departamento del empleado
                         */
                        if($parametros["strMsgReasignacionAutomaticaCambioDep"] != "" && $strObsHistorial != "" 
                            && $parametros["strMsgReasignacionAutomaticaCambioDep"] == $strObsHistorial)
                        {
                            $nombreActualizadoPor   = $nombreActualizadoPor." (Proceso Automático por cambio de departamento)";
                        }
                    }
                }

                // SE VERIFICA SI LA TAREA PERTENECE A UN CASO O ES INDEPENDIENTE
                $caso = $this->tareaPerteneceACaso($datos["idDetalle"]);

                if($caso[0]['caso'] != 0)
                {
                    $detalleHip = $this->getCasoPadreTarea($datos["idDetalle"]);
                    $casoId = $detalleHip[0]->getCasoId()->getId();                           

                    //Se realiza el cambio de estado automático de la tarea
                    $objInfoTareaTiempoParcial= $this->_em->getRepository('schemaBundle:InfoTareaTiempoParcial')
                            ->findOneBy(array('detalleId' => $datos["idDetalle"]), array('feCreacion' => 'DESC'));

                    if (is_object($objInfoTareaTiempoParcial)
                            && $objInfoTareaTiempoParcial->getEstado() === $datos["estado"]
                            && $objInfoTareaTiempoParcial->getTiempo() !== null
                            && is_object($objInfoTareaTiempoParcial->getFeCreacion())
                            && strtoupper($objInfoTareaTiempoParcial->getTipo() === 'C'))
                    {
                        $objFechaEjecucion = new \DateTime(date_format($objInfoTareaTiempoParcial->getFeCreacion(), "d-m-Y H:i"));
                        $objFechaEjecucion->modify('+'.$objInfoTareaTiempoParcial->getTiempo().' minute');
                        $objFechaActual = new \DateTime(date_format(new \DateTime('now'), "d-m-Y H:i"));

                        if ($objFechaEjecucion < $objFechaActual && is_object($parametros['serviceSoporte']))
                        {
                            $arrayParametrosHist["intDetalleId"]            = $datos["idDetalle"];
                            $arrayParametrosHist["intAsignadoId"]           = $datos['asignadoIdHis'];
                            $arrayParametrosHist["intIdDepartamentoOrigen"] = $datos['departamentoOrigenIdHis'];
                            $arrayParametrosHist["strUsrCreacion"]          = $parametros['strUser'];
                            $arrayParametrosHist["strIpCreacion"]           = $parametros['strIp'];
                            $arrayParametrosHist["strCodEmpresa"]           = $parametros['intIdEmpresa'];
                            $arrayParametrosHist["strObservacion"]          = 'Cambio de estado Automático a Asignada';
                            $arrayParametrosHist["strAccion"]               = 'Asignada';
                            $arrayParametrosHist["strEstadoActual"]         = 'Asignada';
                            $arrayParametrosHist["boolHisSeg"]              = true;
                            $arrayResultCalculo = $parametros['serviceSoporte']->calcularTiempoEstado($arrayParametrosHist);
                            if ($arrayResultCalculo['status'] === 'ok')
                            {
                                $datos["estado"] = 'Asignada';
                            }
                        }
                    }
                }
                else
                {
                    $casoId = 0;         // Significa que no pertenece a ningun Caso                            
                }

                //SE DETERMINA EN ESTE CASO EL TIEMPO DE INICIO DE LA TAREA





                $fechaAsignacion = $this->getUltimaFechaAsignacion($datos["idDetalle"], $datos["refAsignadoId"],$datos["asignadoId"]);

                if($fechaAsignacion[0]['fecha'] != "")
                {
                    $fechaAsignacion = $this->getUltimaFechaAsignacion($datos["idDetalle"], $datos["refAsignadoId"],$datos["asignadoId"]);

                    if($fechaAsignacion[0]['fecha'] != "")
                    {
                        $fechaEjecucion = $fechaAsignacion[0]['fecha'];
                    }
                    else
                    {
                        $fechaEjecucion = "";
                    }
                }
                else
                {
                   $fechaEjecucion = ""; 
                }
                if($fechaEjecucion != "")
                {
                    $fecha = explode(" ", $fechaEjecucion);
                    $fech = explode("-", $fecha[0]);
                    $hora = explode(":", $fecha[1]);
                    $fechaEjecucion = $fech[2] . "-" . $fech[1] . "-" . $fech[0];
                    $horaEjecucion = $hora[0] . ":" . $hora[1];
                }
                else
                {
                    $fechaEjecucion = "";
                    $horaEjecucion = "";
                }

                $numero_caso   = '';
                $ultimo_estado = '';
                $cod_empresa_caso   = '';

                if($casoId != 0)
                {                          
                    $infoCaso      = $this->_em->getRepository('schemaBundle:InfoCaso')->find($casoId);
                    $numero_caso   = $infoCaso->getNumeroCaso();
                    $cod_empresa_caso = $infoCaso->getEmpresaCod();

                    $ultimo_estado  = $this->_em->getRepository('schemaBundle:InfoCaso')->getUltimoEstado($casoId);

                    $parteAfectada = $this->_em->getRepository('schemaBundle:InfoParteAfectada')->findByDetalleId($datos["idDetalle"]);

                    if(count($parteAfectada) > 0)
                    {
                        $string_clientes = $parteAfectada[0]->getAfectadoNombre() ? $parteAfectada[0]->getAfectadoNombre() : '';
                    }
                    else
                    {
                        $detalleInicial = $this->_em->getRepository('schemaBundle:InfoDetalle')->getDetalleInicialCaso($casoId);
                        if($detalleInicial[0]["detalleInicial"])
                        {
                            $parteAfectada = $this->_em->getRepository('schemaBundle:InfoParteAfectada')
                                                       ->findByDetalleId($detalleInicial[0]["detalleInicial"]);
                            if($parteAfectada)
                            {
                                $string_clientes = $parteAfectada[0]->getAfectadoNombre() ? $parteAfectada[0]->getAfectadoNombre() : '';
                            }
                        }
                    }
                }    

                //Se verifica si se factura o no la tarea
                $strSeFactura          = "NO";
                $objAdmiCaracteristica    = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                               ->findOneByDescripcionCaracteristica( $parametros["caracteristicaSolicitud"] );
                $objDetalleSolCarac       = $em->getRepository('schemaBundle:InfoDetalleSolCaract')
                                               ->findOneBy( array( 'valor'            => $datos["idDetalle"], 
                                                                   'caracteristicaId' => $objAdmiCaracteristica) );

                if( $objDetalleSolCarac )
                {
                    $strSeFactura = "SI";
                }//( $objDetalleSolCarac )
                //Fin Se verifica si se factura o no la tarea


                //Se obtiene el tiempo transcurrido en minutos de la tarea
                $strEstado              = $datos["estado"];
                $strFechaCreacionTarea  = "";
                
                if($strEstado == 'Asignada' || $casoId != 0)
                {
                    $strFechaCreacionTarea = new \DateTime($feTareaCreada);
                }
                else
                {
                    if(!empty($datos["idDetalle"]))
                    {
                        $arrayParametros["intDetalleId"] = $datos["idDetalle"];
                        $intTareasIniciadas = $this->getVecesTareasIniciadas($arrayParametros);
                        
                        if($intTareasIniciadas < 2)
                        {
                            $objInfoTareaSeguimiento = $this->_em->getRepository('schemaBundle:InfoTareaSeguimiento')
                                                                 ->getFechaInicioTarea($datos["idDetalle"]);

                            if($objInfoTareaSeguimiento)
                            {
                                $strFeCreacionTareaAceptada = ($objInfoTareaSeguimiento[0]["FechaInicioTarea"] ?
                                                               strval(date_format($objInfoTareaSeguimiento[0]["FechaInicioTarea"], "d-m-Y H:i")) : "");

                                $strFechaCreacionTarea = new \DateTime($strFeCreacionTareaAceptada);
                            }
                        }
                        else if($intTareasIniciadas > 1)
                        {
                            $dateTareaFechaInicio = $this->getTareaIniciada($arrayParametros);

                            $strFeCreacionTareaAceptada = strval(date_format($dateTareaFechaInicio, "d-m-Y H:i"));

                            $strFechaCreacionTarea      = new \DateTime($strFeCreacionTareaAceptada);
                        }
                    }
                }
                if( $strEstado == 'Cancelada' || $strEstado == 'Finalizada' || $strEstado == 'Rechazada' || $strEstado == 'Anulada')
                {
                    $datetimeFinal = new \DateTime($feTareaHistorial);
                }
                else
                {
                    $datetimeFinal = new \DateTime();
                }

                $datetimeDiferenciaFechas = $datetimeFinal->diff($strFechaCreacionTarea);

                $intMinutos = $datetimeDiferenciaFechas->days * 24 * 60;
                $intMinutos += $datetimeDiferenciaFechas->h * 60;
                $intMinutos += $datetimeDiferenciaFechas->i;
                $strMinutos = $intMinutos.' minutos';

                if($datos["estado"] == "Pausada")
                {
                    $objInfoTareaTiempoParcial = $this->_em->getRepository('schemaBundle:InfoTareaTiempoParcial')
                                                           ->findOneBy(array('detalleId' => $datos["idDetalle"],
                                                                             'estado'    => 'Pausada'));

                    if(is_object($objInfoTareaTiempoParcial))
                    {
                        $strMinutos = $objInfoTareaTiempoParcial->getValorTiempo() . ' minutos';
                    }
                }
                else if($datos["estado"] <> 'Cancelada' && $datos["estado"] <> 'Finalizada' && $datos["estado"] <> 'Rechazada')
                {
                    $objInfoTareaTiempoParcial = $this->_em->getRepository('schemaBundle:InfoTareaTiempoParcial')
                                                           ->findOneBy(array('detalleId' => $datos["idDetalle"],
                                                                             'estado'    => 'Reanudada'));

                    $objInfoTareaPausaParcial = $this->_em->getRepository('schemaBundle:InfoTareaTiempoParcial')
                                                          ->findOneBy(array('detalleId' => $datos["idDetalle"],
                                                                            'estado'    => 'Pausada'));

                    if(is_object($objInfoTareaTiempoParcial))
                    {
                        $strFeCreacionReanudada = strval(date_format($objInfoTareaTiempoParcial->getFeCreacion(), "d-m-Y H:i"));

                        $dateFechaReanudada = new \DateTime($strFeCreacionReanudada);
                        $dateFechaActual    = new \DateTime();

                        $datetimeDiferenciaFechas = $dateFechaActual->diff($dateFechaReanudada);

                        $intMinutos = $datetimeDiferenciaFechas->days * 24 * 60;
                        $intMinutos += $datetimeDiferenciaFechas->h * 60;
                        $intMinutos += $datetimeDiferenciaFechas->i;

                        if(is_object($objInfoTareaPausaParcial))
                        {
                            $intTiempoTareaPausada = $objInfoTareaPausaParcial->getvalorTiempo();
                        }

                        $strMinutos = $intMinutos + $intTiempoTareaPausada;
                        $strMinutos = $strMinutos.' minutos';
                    }
                }

                $objTiempoReanudadaParcial = $this->_em->getRepository('schemaBundle:InfoTareaTiempoParcial')
                                                       ->findOneBy(array('detalleId' => $datos["idDetalle"],
                                                                         'estado'    => 'Reanudada'));

                if(is_object($objTiempoReanudadaParcial))
                {
                    $objTiempoIniciadaParcial = $this->_em->getRepository('schemaBundle:InfoTareaTiempoParcial')
                                                          ->findOneBy(array('detalleId' => $datos["idDetalle"],
                                                                            'estado'    => 'Iniciada'));

                    if(is_object($objTiempoIniciadaParcial))
                    {
                        $objInfoDetalle = $this->_em->getRepository('schemaBundle:InfoDetalle')
                                                    ->find($datos["idDetalle"]);

                        if(is_object($objInfoDetalle))
                        {
                            $strFeCreacionIniciada = strval(date_format($objTiempoIniciadaParcial->getFeCreacion(), "d-m-Y H:i"));
                            $strFeCreacionDetalle = strval(date_format($objInfoDetalle->getFeCreacion(), "d-m-Y H:i"));

                            $dateFeCreacionIniciada = new \DateTime($strFeCreacionIniciada);
                            $dateFeCreacionDetalle  = new \DateTime($strFeCreacionDetalle);

                            $datetimeDiferenciaFechasPausa = $dateFeCreacionDetalle->diff($dateFeCreacionIniciada);

                            $intMinutosInicio = $datetimeDiferenciaFechasPausa->days * 24 * 60;
                            $intMinutosInicio += $datetimeDiferenciaFechasPausa->h * 60;
                            $intMinutosInicio += $datetimeDiferenciaFechasPausa->i;
                        }
                    }

                    $intMinutosTareaPausada = $objTiempoReanudadaParcial->getValorTiempoPausa() + $intMinutosInicio;
                }

                //Fin Se obtiene el tiempo transcurrido en minutos de la tarea
                $parametroTarea     = "";
                $mostrarCoordenadas = "N";
                $tareasManga        = "N";
                if($datos["idTarea"])
                {
                    $parametroTarea = $em->getRepository('schemaBundle:AdmiParametroDet')
                                         ->getOne('TAREAS_PERMITIDAS_INGRESAR_COORDENADAS','SOPORTE','CASO','TAREA SELECCIONAR COORDENADA',
                                                  $datos["idTarea"],'','','','','');
                    if($parametroTarea)
                    {
                        if ($datos["idTarea"] == $parametroTarea["valor1"])
                        {
                            $mostrarCoordenadas = "S";

                            if($mostrarCoordenadas == "S" && $parametroTarea["valor3"] == "S")
                            {
                                $tareasManga = "S";
                            }
                        }
                    }
                }

                $strPrefijoEmpresaTarea = "";
                $intEmpresaId;
                if (!is_null($numeroTarea))
                {
                    $objInfoComunicacion    = $emComunicacion->getRepository('schemaBundle:InfoComunicacion')->findOneById($numeroTarea);
                    $objInfoEmpresaGrupo    = $em->getRepository('schemaBundle:InfoEmpresaGrupo')
                                                 ->findOneById($objInfoComunicacion->getEmpresaCod());
                    $strPrefijoEmpresaTarea = $objInfoEmpresaGrupo->getPrefijo();
                    $intEmpresaId           = $objInfoEmpresaGrupo->getId();   
                }
                $presentarSubtarea = "icon-invisible";
                //Se determina si se debe mostrar el boton de agregar tarea
                if(($isDepartamento || $booleanEsDptoAutorizadoGestion) 
                    && ($datos["estado"] <> "Finalizada" && $datos["estado"] <> "Cancelada"  && $datos["estado"] <> "Rechazada"
                    && $datos["estado"] <> "Asignada" && $datos["estado"] <> "Pausada"  && $datos["estado"] <> "Anulada")
                    && !$casoId)
                {
                    $presentarSubtarea = "button-grid-agregarTarea";
                }

                if($datos["idDetalle"])
                {
                    //Se obtiene las tareas en base al id_detalle y se verifica si tiene tareas abiertas
                    $entityDetallesRelacionados = $this->_em->getRepository('schemaBundle:InfoDetalle')
                                                            ->findByDetalleIdRelacionado($datos["idDetalle"]);
                }
                $cerrarTarea = "S";
                foreach($entityDetallesRelacionados as $entity)
                {
                    $entityUltimoEstado = $this->_em->getRepository('schemaBundle:InfoDetalleAsignacion')
                                                    ->getUltimoEstado($entity->getId());
                    if($entityUltimoEstado)
                    {
                        if($entityUltimoEstado->getEstado() <> "Finalizada" && $entityUltimoEstado->getEstado() <> "Rechazada" &&
                            $entityUltimoEstado->getEstado() <> "Cancelada" && $entityUltimoEstado->getEstado() <> "Anulada")
                        {
                            $cerrarTarea = "N";
                        }
                    }
                }

                $intTareaPadre = "";
                //Se obtiene el numero de tarea padre
                if($datos["detalleIdRelacionado"])
                {
                    //Se obtiene el numero de la tarea en base al id_detalle
                    $intTareaPadre = $emComunicacion->getRepository('schemaBundle:InfoComunicacion')
                                                    ->getMinimaComunicacionPorDetalleId($datos["detalleIdRelacionado"]);
                }

                if($prefijoEmpresa == "TN")
                {
                    //Se valida si el departamento del usuario el session tiene permitido ingresar seguimientos internos
                    $strDepartamentoSeguiInterno = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                                                             ->getOne('SEGUIMIENTO INTERNO',
                                                                      'SOPORTE',
                                                                      'CASOS',
                                                                      'DEPARTAMENTOS TN',
                                                                      '',$departamentoSession,'','','','');

                    if($strDepartamentoSeguiInterno)
                    {
                        $strMostrarOpcionSeguiInterno = "S";
                    }
                }

                $intMinutos = substr($strMinutos,0,-8);

                $boolVerAnularTarea     = false;

                $arrayRespuestaAsignacionesTareaAnulada = $this->_em->getRepository('schemaBundle:InfoDetalleAsignacion')
                                                                    ->getAsignacionesVerTareaAnulada( array( "intIdDetalle"     => 
                                                                                                             $datos["idDetalle"],
                                                                                                             "intIdRefAsignado" => 
                                                                                                             $parametros["idUsuario"]
                                                                                                           )
                                                                                                    );
                /* Se valida que el botón de anular tarea sólo aparezca cuando la tarea se haya autoasignado a la persona que la creó
                 * y que a dicha tarea solo se le haya realizado una asignación
                 * Si existiera más de una asignación, el botón que deberá aparecer en lugar del botón anular sería el botón de rechazar
                 */
                if($datos["estado"]=="Asignada" 
                    && (isset($arrayRespuestaAsignacionesTareaAnulada['resultado']) 
                        && !empty($arrayRespuestaAsignacionesTareaAnulada['resultado']))
                    && (isset($arrayRespuestaAsignacionesTareaAnulada['total']) && !empty($arrayRespuestaAsignacionesTareaAnulada['total']) 
                        && $arrayRespuestaAsignacionesTareaAnulada['total']==1)
                    )
                {
                    $boolVerAnularTarea = true;
                }

                //Se valida si la tarea fue iniciada desde el mobil
                $strIniciadaDesdeMobil = "S";
                $intTareaInstalacion   = "";

                //Se obtiene la ultima asignacion de la tarea
                if($datos["idDetalle"])
                {
                    $objUltimaAsignacion = $this->_em->getRepository('schemaBundle:InfoDetalleAsignacion')->getUltimaAsignacion($datos["idDetalle"]);
                }

                if(is_object($objUltimaAsignacion))
                {
                    $arrayParametroTareaInstalacion = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                                                                ->getOne('TAREA INSTALACION','SOPORTE',
                                                                         'TAREAS','ID_TAREA_INSTALACION','','','','','','');

                    if(!empty($arrayParametroTareaInstalacion))
                    {
                        $intTareaInstalacion = $arrayParametroTareaInstalacion["valor1"];
                    }

                    if($objUltimaAsignacion->getTipoAsignado() == "CUADRILLA" && ($casoId != 0 || $datos["idTarea"] == $intTareaInstalacion))
                    {
                        //Se obtiene mensaje configurado para el inicio de las tareas desde el mobil
                        $arrayParametroMsgInicioTarea = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                                                                  ->getOne('MSG_INICIO_EJECUCION_TAREA','SOPORTE',
                                                                           'TAREAS','MSG_INICIO_TAREA_MOBIL','','','','','','');
                        if(!empty($arrayParametroMsgInicioTarea))
                        {
                            $arrayParametrosTareaIniciada["intDetalleId"]   = $datos["idDetalle"];
                            $arrayParametrosTareaIniciada["strObservacion"] = $arrayParametroMsgInicioTarea["valor1"];

                            $strTareaIniciada = $this->_em->getRepository('schemaBundle:InfoDetalleAsignacion')
                                                          ->getTareaFueIniciada($arrayParametrosTareaIniciada);
                        }

                        $strIniciadaDesdeMobil = $strTareaIniciada;
                    }
                }


                $boolMostrarInfoAdicional   = false;
                $strNombreElementoTarea     = "N/A";
                $strTipoElementoTarea       = "N/A";
                $strLatitudTarea            = $datos["latitud"] ? $datos["latitud"] : "N/A";
                $strLongitudTarea           = $datos["longitud"] ? $datos["longitud"] : "N/A";
                $strUsrCreacionDetalle      = $datos["usrCreacionDetalle"] ? $datos["usrCreacionDetalle"] : "N/A";
                $strObservacionDetalle      = $datos["observacion"] ? $datos["observacion"] : "N/A";
                $strInfoAdicional           = "";
                if(isset($datos["nombreTarea"]) && !empty($datos["nombreTarea"]))
                {
                    $arrayAdmiParamDetTareasInfoAdic  = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                                                                  ->getOne( 'TAREAS_MOSTRAR_BTN_INFO_ADICIONAL', 
                                                                            '', 
                                                                            '', 
                                                                            '', 
                                                                            $datos["nombreTarea"], 
                                                                            '', 
                                                                            '', 
                                                                            '', 
                                                                            '', 
                                                                            '' );
                    if( $arrayAdmiParamDetTareasInfoAdic )
                    {
                        $boolMostrarInfoAdicional = true;

                        $objDetalleTarea = $this->_em->getRepository('schemaBundle:InfoDetalle')->find($datos["idDetalle"]);
                        if(is_object($objDetalleTarea))
                        {
                            $objDetalleTareaElemento = $this->_em->getRepository('schemaBundle:InfoDetalleTareaElemento')
                                                                 ->findOneBy(array("detalleId" => $objDetalleTarea));
                            if(is_object($objDetalleTareaElemento))
                            {
                                $intIdElementoTarea = $objDetalleTareaElemento->getElementoId();
                                if($intIdElementoTarea)
                                {
                                    $objElementoTarea       = $this->_em->getRepository('schemaBundle:InfoElemento')->find($intIdElementoTarea);
                                    if(is_object($objElementoTarea))
                                    {
                                        $strNombreElementoTarea = $objElementoTarea->getNombreElemento();
                                        $objModeloElementoTarea = $objElementoTarea->getModeloElementoId();
                                        if(is_object($objModeloElementoTarea))
                                        {
                                            $objTipoElementoTarea = $objModeloElementoTarea->getTipoElementoId();
                                            if(is_object($objTipoElementoTarea))
                                            {
                                                $strTipoElementoTarea = $objTipoElementoTarea->getNombreTipoElemento();
                                            }

                                        }
                                    }
                                }
                            }
                        }
                        $strInfoAdicional = "<b>Información Adicional</b>"
                                          . "<table>"
                                          . "<tr><td>Tipo de Elemento</td><td class='margenInfoAdicional'>:</td>"
                                          . "<td>".$strTipoElementoTarea."</td></tr>"
                                          . "<tr><td>Elemento</td><td class='margenInfoAdicional'>:</td>"
                                          . "<td>".$strNombreElementoTarea."</td></tr>"
                                          . "<tr><td>Latitud</td><td class='margenInfoAdicional'>:</td>"
                                          . "<td>".$strLatitudTarea."</td></tr>"
                                          . "<tr><td>Longitud</td><td class='margenInfoAdicional'>:</td>"
                                          . "<td>".$strLongitudTarea."</td></tr>"
                                          . "<tr><td>Usr. Creación</td><td class='margenInfoAdicional'>:</td>"
                                          . "<td>".$strUsrCreacionDetalle."</td></tr>"
                                          . "<tr><td>Observación</td><td class='margenInfoAdicional'>:</td>"
                                          . "<td>".$strObservacionDetalle."</td></tr>"
                                          . "</table>";
                    }
                }

                // Verificamos si la tarea proviene de hal
                $boolEsHal = false;
                if (!is_null($numeroTarea))
                {
                    $arrayResulExisteHal = $this->_em->getRepository('schemaBundle:InfoCuadrillaPlanifCab')
                        ->tareaExisteEnHal(array ('intNumeroTarea' => $numeroTarea,
                                                  'strEstadoCab'   => 'Activo',
                                                  'strEstadoDet'   => 'Activo'));

                    if (!empty($arrayResulExisteHal) && count($arrayResulExisteHal) > 0
                        && $arrayResulExisteHal['resultado'] === 'ok')
                    {
                        $boolEsHal = $arrayResulExisteHal['existeTarea'];
                    }
                }

                /*
                 * Verificamos si el id de la tarea se encuentra parametrizada para mostrar la pestalla hal
                 * siempre y cuando la tarea no sea hal
                 */
                $boolTareaParametro = false;
                if (!$boolEsHal)
                {
                    $arrayAdmiParametroDet = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                            ->getOne('PLANIFICACION_TAREAS_HAL',
                                     'SOPORTE',
                                     '',
                                     '',
                                     $datos["idTarea"],
                                     '',
                                     '',
                                     '',
                                     '',
                                     '');

                    if (!empty($arrayAdmiParametroDet) && count($arrayAdmiParametroDet) > 0)
                    {
                        $boolTareaParametro = true;
                    }
                }

                //*****************Validar si la persona en session puede finalizar la tarea de generacion de informe ejecutivo*****************
                $strBanderaFinalizarInformeEjecutivo = "S";
                if($datos["nombreTarea"] == "Realizar Informe Ejecutivo de Incidente")
                {
                    $arrayParametrosOrigen["intIdDetalle"] = $datos["idDetalle"];
                    $intDepartamentoOrigenInforme = $this->getDepartamentoOrigenPorTarea($arrayParametrosOrigen);

                    if($intDepartamentoOrigenInforme != $departamentoSession)
                    {
                        $strBanderaFinalizarInformeEjecutivo = "N";
                    }
                }

                //Validamos que el botón reprogramar se habilite de acuerdo a los estado mencionados.
                $booleanReprogramarHal     = false;
                $boolMostrarReprogramarDep = false;

                if ($boolEsHal)
                {
                    //Verificamos si el departamento está configurado para mostrar el botón reprogramar
                    $arrayAdmiParametroDep= $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                        ->getOne('REPROGRAMAR_DEPARTAMENTO_HAL','SOPORTE','','',$departamentoSession,'','','','','');

                    if (!empty($arrayAdmiParametroDep) && count($arrayAdmiParametroDep) > 0)
                    {
                        if ($datos["estado"] == "Aceptada"     ||
                            $datos["estado"] == "Reprogramada" ||
                            $datos["estado"] == "Pausada"      ||
                            $datos["estado"] == "Asignada")
                        {
                            $boolMostrarReprogramarDep = true;
                        }
                    }

                    if ($datos["estado"] == "Aceptada"     ||
                        $datos["estado"] == "Reprogramada" ||
                        $datos["estado"] == "Pausada"      ||
                        $datos["estado"] == "Asignada")
                    {
                        $booleanReprogramarHal = true;
                    }
                }

                //Validamos que el botón reasignar se habilite de acuerdo a los estado mencionados.
                $booleanReasignarHal = false;
                if ($boolTareaParametro)
                {
                    if ($datos["estado"] == "Aceptada"     ||
                        $datos["estado"] == "Reprogramada" ||
                        $datos["estado"] == "Pausada"      ||
                        $datos["estado"] == "Asignada")
                    {
                        $booleanReasignarHal = true;
                    }
                }

                $boolGestionCompleta = true;
                $boolRenviarSysCloud = false;

                if ($intDepartamentoAsignado !== null && $intDepartamentoAsignado !== '')
                {
                    $arrayAdmiParametroUsrs = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->getOne('USUARIOS LIMITADORES DE GESTION DE TAREAS',
                                                                 'SOPORTE','','',$intDepartamentoAsignado,'','','','','');

                    //Validamos la respuesta para identificar si el departamento esta limitado a la gestion completa de tareas.
                    if(!empty($arrayAdmiParametroUsrs))
                    {
                        $boolGestionCompleta = false;

                        //Verificamos si la tarea fue replicada en el Sys Cloud-Center.
                        if ($datos['usrCreacionDetalle'] !== 'telcoSys')
                        {
                            //Obtenemos la caracteristica TAREA_SYS_CLOUD_CENTER.
                            $objAdmiCaracteristicaTSC = $this->_em->getRepository('schemaBundle:AdmiCaracteristica')
                                    ->findOneBy(array ('descripcionCaracteristica' => 'TAREA_SYS_CLOUD_CENTER',
                                                       'estado'                    => 'Activo'));

                            if (is_object($objAdmiCaracteristicaTSC) && $numeroTarea !== null && $numeroTarea !== ''
                                    && $arrayAdmiParametroUsrs['valor1'] == $departamentoSession)
                            {
                                $objInfoTareaCaracteristicaTSC = $this->_em->getRepository('schemaBundle:InfoTareaCaracteristica')
                                        ->findOneBy(array ('tareaId'          => $numeroTarea,
                                                           'caracteristicaId' => $objAdmiCaracteristicaTSC->getId(),
                                                           'estado'           => 'Activo'));

                                if (!is_object($objInfoTareaCaracteristicaTSC)
                                        || strtoupper($objInfoTareaCaracteristicaTSC->getValor()) === 'N')
                                {
                                    $boolRenviarSysCloud = true;
                                }
                            }
                        }
                    }
                }

                $boolAtenderAntes = false;
                if (!is_null($numeroTarea))
                {
                    //Verificamos si la tarea tiene característica ATENDER_ANTES Activa.
                    $objAdmiCaracteristicaAA = $this->_em->getRepository('schemaBundle:AdmiCaracteristica')
                                    ->findOneBy(array ('descripcionCaracteristica' => 'ATENDER_ANTES',
                                                       'estado'                    => 'Activo'));

                    if (is_object($objAdmiCaracteristicaAA))
                    {
                        $objInfoTareaCaracteristicaAA = $this->_em->getRepository('schemaBundle:InfoTareaCaracteristica')
                                ->findOneBy( array('tareaId'          => intval($numeroTarea),
                                                   'caracteristicaId' => $objAdmiCaracteristicaAA->getId(),
                                                   'valor'            => 'S',
                                                   'estado'           => 'Activo'));

                        if (is_object($objInfoTareaCaracteristicaAA))
                        {
                            $boolAtenderAntes = true;
                        }
                    }
                }

                    $boolEsInterdep                         = true;
                                        
                    $strIdsTareasNoReqActivos   = "";
                    $arrayIdTareasNoReqActivo 	= $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                                    ->getOne('IDS_TAREAS_NO_REG_ACTIVOS', 
                                                '', 
                                                '', 
                                                '', 
                                                '', 
                                                '', 
                                                '', 
                                                ''
                                            );

                        if (is_array($arrayIdTareasNoReqActivo))
                        {
                            $strIdsTareasNoReqActivos = !empty($arrayIdTareasNoReqActivo['valor1']) ? $arrayIdTareasNoReqActivo['valor1'] : "";
                        }

                        $arrayIdsTareasNoReqActivo = explode (",", $strIdsTareasNoReqActivos);  
                        foreach($arrayIdsTareasNoReqActivo as $intIdTarea) 
                        {
                            if($datos["idTarea"] == $intIdTarea || $casoId != 0)
                            {
                                $boolEsInterdep = false;
                                break;
                            }
                        }
                    
                    $arrayGetInfoTarea['idDetalle']         =  $datos["idDetalle"];
                    $arrayGetInfoTarea['codEmpresa']        =  $strPrefijoEmpresaTarea;
                    $arrayGetInfoTarea['intIdCaso']         =  $casoId;
                    $arrayGetInfoTarea['serviceUtil']       =  $parametros['serviceUtil'];
                    $arrayGetInfoTarea['strUser']           =  $parametros['strUser'];
                    $arrayGetInfoTarea['strIp']             =  $parametros['strIp'];
                    $arrayGetInfoTarea['esInterdep']        =  $boolEsInterdep;
                    $arrayIdServicioVRF                     =  $this->getIdServicioVRF($arrayGetInfoTarea);
                                       
                    $arrayInfoTareaTN                       = $this->getInfoTareaByDetalle($arrayGetInfoTarea);
                    $intServicioId                          = $arrayInfoTareaTN['servicioId'];
                    $intIdServicioVrf                       = $arrayIdServicioVRF['idServicio'];
                    $intPersonaId                           = $arrayInfoTareaTN['personaId'];
                    
                        
                        $arrayInfServicio['intServicioId']      = $intServicioId;
                        $arrayInfServicio['intEmpresaCod']      = $intEmpresaId;        
                        $arrayInfServicio['tipoProgreso']       = "INGRESO_FIBRA";
                        $arrayInfServicio['detalleId']          = $datos["idDetalle"];
                        $arrayInfServicio['casoId']             = $casoId;   
                        $arrayInfServicio['serviceUtil']        = $parametros['serviceUtil'];
                        $arrayInfServicio['strUser']            = $parametros['strUser'];
                        $arrayInfServicio['strIp']              = $parametros['strIp'];
                        $arrayInfServicio['esInterdep']         = $boolEsInterdep;
                        
                        $strTieneFibra                          = $this->_em->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                       ->validaProgresoTarea($arrayInfServicio);
                        
                        $arrayInfServicio['tipoProgreso']       = "INGRESO_MATERIALES";
                        $strTieneMateriales                     = $this->_em->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                        ->validaProgresoTarea($arrayInfServicio);
                        
                        $arrayInfServicio['tipoProgreso']       = "CONFIRMA_IP_SERVICIO";
                        $strTieneConfirmacionIPserv             = $this->_em->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                        ->validaProgresoTarea($arrayInfServicio);

                        $strIdsTareasReqActivos   = "";
                        
                        
                        $arrayIdTareasReqActivo = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                                    ->getOne('IDS_TAREAS_REASIGNACION_REG_ACTIVOS', 
                                                '', 
                                                '', 
                                                '', 
                                                '', 
                                                '', 
                                                '', 
                                                ''
                                            );

                        if (is_array($arrayIdTareasReqActivo))
                        {
                            $strIdsTareasReqActivos = !empty($arrayIdTareasReqActivo['valor1']) ? $arrayIdTareasReqActivo['valor1'] : "";
                        }

                        
                        $arrayIdsTareasReqActivo = explode (",", $strIdsTareasReqActivos);  
                        
                        
                        foreach ($arrayIdsTareasReqActivo as $intIdTarea) 
                        {
                            if($datos["idTarea"] === $intIdTarea)
                            {
                                $strRequiereControlActivo = 'SI';
                            }
                        }
                    
                    
                    if(!empty($intServicioId))
                    {
                        $arrayTipoMedio =    $this->getTipoMedioTarea($arrayInfServicio);
                    }
                    
                        
                
                    $strPersonaEmpresaRol                     = $this->_em->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                        ->getIdDepartCoordinador($datos["refAsignadoId"]);

                    
                    $arrayNumBobinaInstalacion              = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                                                               ->getOne('PARAMETROS_GENERALES_MOVIL', 
                                                                        '', 
                                                                        '', 
                                                                        '', 
                                                                        'NUMERO_BOBINAS_INSTALACION', 
                                                                        '', 
                                                                        '', 
                                                                        ''
                                                                        );
                    
                    $arrayNumBobinaSoporte              = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                                                               ->getOne('PARAMETROS_GENERALES_MOVIL', 
                                                                        '', 
                                                                        '', 
                                                                        '', 
                                                                        'NUMERO_BOBINAS_SOPORTE', 
                                                                        '', 
                                                                        '', 
                                                                        ''
                                                                        );
                    
                    if($casoId != 0)
                    {
                        if(is_array($arrayNumBobinaSoporte))
                        {
                            $intNumBobinaVisualizar     = !empty($arrayNumBobinaSoporte['valor2']) ? $arrayNumBobinaSoporte['valor2'] : "";
                            $strEstadoNumBobinaVisual   = !empty($arrayNumBobinaSoporte['estado']) ? $arrayNumBobinaSoporte['estado'] : "";
                        }    
                        
                        $arrayDataUmSoporte = array (
                                                        'casoId'          => $casoId
                                                    );
                        $arrayResultUmSoporte           = $this->_em->getRepository('schemaBundle:InfoTareaCaracteristica')
                                                                    ->getUltimaMillaSoporte($arrayDataUmSoporte);
                        $arrayDataUltimaMillaSoporte    = $arrayResultUmSoporte["result"][0];
                        
                        //CAMBIO PARA UTILIZAR EL TIPO DE CASO PARA VALIDACIÓN DE BOTONERA 
                        $strTipoCasoEnlace  = "";
                        $objInfoCasoEnlace  = $this->_em->getRepository('schemaBundle:InfoCaso')->findOneById($casoId);
                        if(isset($objInfoCasoEnlace) && !empty($objInfoCasoEnlace))
                        {
                            $strTipoCasoEnlace = $objInfoCasoEnlace->getTipoCasoId()->getNombreTipoCaso();
                        }
                    }
                    else
                    {
                        if(is_array($arrayNumBobinaInstalacion))
                        {
                            $intNumBobinaVisualizar     = !empty($arrayNumBobinaInstalacion['valor2']) ? $arrayNumBobinaInstalacion['valor2'] : "";
                            $strEstadoNumBobinaVisual   = !empty($arrayNumBobinaInstalacion['estado']) ? $arrayNumBobinaInstalacion['estado'] : "";
                        }    
                    }
                    //*****Validar si contiene la caracteristica de Crear KML, por medio del idDetalle */
                    $arrayData = array( 'idDetalle'            => $datos["idDetalle"] ,
                                        'nombreCaracteristica' => "AUTH_CREACION_KML",
                                        'idComunicacion'       => $numeroTarea);

                    $strPermiteCrearKml = $this->validarCaracteristicaIdDetalle($arrayData);

                    $arrayTareaAnterior = $this->_em->getRepository('schemaBundle:InfoDetalleHistorial')
                                                    ->getMotivoPorTarea(array('idDetalle' => $datos["idDetalle"]));
                   
                //*****************Validar si la persona en session puede finalizar la tarea de generacion de informe ejecutivo*****************

                $arr_encontrados[] = array(
                    'strEmpresaTarea'     => $strPrefijoEmpresaTarea,
                    'id_detalle'          => $datos["idDetalle"],
                    'id_tarea'            => $datos["idTarea"],
                    'iniciadaDesdeMobil'  => $strIniciadaDesdeMobil,
                    'strBanderaFinalizarInformeEjecutivo' => $strBanderaFinalizarInformeEjecutivo,
                    'mostrarCoordenadas'  => $mostrarCoordenadas,
                    'tareasManga'         => $tareasManga,
                    'numero_tarea_Padre'  => $intTareaPadre?$intTareaPadre : "",
                    'nombre_proceso'      => $nombre_proceso ? $nombre_proceso : "",
                    'numero_tarea'        => $numeroTarea ? $numeroTarea : "",
                    'nombre_tarea'        => ($datos["nombreTarea"] ? $datos["nombreTarea"] : "N/A"),
                    'descripcionInicial'  => ($datos["descripcionTarea"] ? $datos["descripcionTarea"] : ""),
                    'cerrarTarea'         => $cerrarTarea,
                    'seguimientoInterno'  => $strMostrarOpcionSeguiInterno,
                    'asignado_id'         => $datos["asignadoId"],
                    'asignado_nombre'     => ($datos["asignadoNombre"] ? ucwords(strtolower($datos["asignadoNombre"])) : "N/A"),
                    'ref_asignado_id'     => $datos["refAsignadoId"],
                    'ref_asignado_nombre' => ($datos["refAsignadoNombre"] ? 
                                             ucwords(strtolower($datos["refAsignadoNombre"])) : $datos["asignadoNombre"]),
                    'clientes'            => $string_clientes,
                    'observacion'         => $datos["observacion"] ? ($boolMostrarInfoAdicional ? $strInfoAdicional 
                                                                                                : $datos["observacion"]) : "",
                    'strTareaIncAudMant'  => $boolMostrarInfoAdicional ? 'S' : 'N',
                    'feTareaCreada'       => $feTareaCreada ? $feTareaCreada : "",
                    'feSolicitada'        => $feSolicitada ? $feSolicitada : "",
                    'feTareaAsignada'     => $feTareaAsignada ? $feTareaAsignada : "",
                    'feTareaHistorial'    => $feTareaHistorial ? $feTareaHistorial : "",
                    'actualizadoPor'      => $nombreActualizadoPor ? $nombreActualizadoPor : "N/A",
                    'perteneceCaso'       => $caso[0]['caso'] == 0 ? false : true,
                    'sessionTN'           => $prefijoEmpresa == "TN" ? true : false,
                    'casoPerteneceTN'     => $cod_empresa_caso == "10" ? true : false,
                    'fechaEjecucion'      => $fechaEjecucion,
                    'horaEjecucion'       => $horaEjecucion,
                    'id_caso'             => $casoId,
                    'estado_caso'         => $ultimo_estado,
                    'numero_caso'         => $numero_caso,
                    'seFactura'           => $strSeFactura,
                    'duracionTarea'       => $strMinutos,
                    'duracionMinutos'     => $intMinutos,
                    'tiempoPausada'       => $intMinutosTareaPausada,
                    'personaEmpresaRolId' => $intPersonaEmpresaRol,
                    'estado'              => $datos["estado"] ? $datos["estado"] : "",
                    'action1'             => $boolGestionCompleta ? (($isDepartamento || $booleanVerTareasTodasEmpresas ||
                                             $boolMostrarReprogramarDep) ?
                                             (($datos["estado"] == "Aceptada" || $datos["estado"] == "Pausada"
                                                     || $booleanReprogramarHal) ?
                                               'button-grid-reprogramarTarea' : "icon-invisible") : "icon-invisible"
                                             ) : "icon-invisible", //action1 Reprogramar Tarea
                    'action2'             => $boolGestionCompleta ? (($isDepartamento || $booleanVerTareasTodasEmpresas) ?
                                             (($datos["estado"] == "Aceptada") ?
                                               'button-grid-rechazarTarea' : "icon-invisible") : "icon-invisible"
                                             ) : "icon-invisible", //action2 Cancelar Tarea
                    'action3'             => $boolGestionCompleta ? (($isDepartamento || $booleanVerTareasTodasEmpresas) ?
                                             (($datos["estado"] == "Aceptada") ?
                                               'button-grid-detenerTarea' : "icon-invisible") : "icon-invisible"
                                             ) : "icon-invisible", //action3 Finalizar Tarea
                    'action4'             => $boolGestionCompleta ? (($isDepartamento || $booleanVerTareasTodasEmpresas) ?
                                             (($datos["estado"] == "Aceptada" || $datos["estado"] == "Pausada"
                                                     || $booleanReasignarHal) ?
                                               'button-grid-finalizarTarea' : "icon-invisible") : "icon-invisible"
                                             ) : "icon-invisible", //action4 Reasignar Tarea
                    'action5'             => $boolGestionCompleta ? (($isDepartamento || $booleanVerTareasTodasEmpresas) ?
                                             (($datos["estado"] == "Aceptada") ?
                                                  'button-grid-finalizarTarea' : "icon-invisible") : "icon-invisible"
                                             ) : "icon-invisible", //action5 No existe en el index.js de tareas
                    'action6'             => $boolGestionCompleta ? (($isDepartamento || $booleanVerTareasTodasEmpresas) ?
                                             (($datos["estado"] == "Asignada" || $datos["estado"] == "Reprogramada") ?
                                               "button-grid-iniciarTarea" : "icon-invisible") : "icon-invisible"
                                             ) : "icon-invisible", //action6 Ejecutar Tarea
                    'action7'             => $boolGestionCompleta ?
                                             (($datos["estado"] != "Finalizada" &&
                                               $datos["estado"] != "Cancelada"  &&
                                               $datos["estado"] != "Rechazada"  &&
                                               $datos["estado"] != "Anulada")  ? 'button-grid-agregarSeguimiento' : "icon-invisible"
                                             ) : "icon-invisible", //action7 Agregar Seguimiento
                    'action8'             => 'button-grid-show', //action8 Ver Seguimientos
                    'action9'             => $boolGestionCompleta ?
                                             ($boolVerAnularTarea ? "icon-invisible" : (
                                              $isDepartamento ? (
                                                     ($datos["estado"] == "Asignada" || $datos["estado"] == "Reprogramada") ?
                                              'button-grid-rechazarTarea' : "icon-invisible") : "icon-invisible")
                                             ) : "icon-invisible", //action9 Rechazar Tarea
                    'action10'            => $boolGestionCompleta ?
                                             ((($isDepartamento || $isDepartamentoCreador ||
                                                $booleanVerTareasTodasEmpresas) && ($datos["estado"] <> "Pausada")) ?
                                                "button-grid-agregarArchivoCaso":"icon-invisible"
                                             ) : "icon-invisible", //action10 Cargar Archivo
                    'action11'            => 'button-grid-pdf', //action11 Ver Archivos
                    'action12'            => $boolGestionCompleta ? $presentarSubtarea
                                                : "icon-invisible", //action12 Crear Tarea
                    'action13'            => $boolGestionCompleta ?
                                             (($isDepartamento || $booleanVerTareasTodasEmpresas) ?
                                             (($datos["estado"] == "Aceptada") ?
                                                  "button-grid-pausarTarea" : "icon-invisible") : "icon-invisible"
                                             ) : "icon-invisible", //action13 Pausar Tarea
                    'action14'            => $boolGestionCompleta ?
                                             (($isDepartamento || $booleanVerTareasTodasEmpresas) ?
                                             (($datos["estado"] == "Pausada") ?
                                               "button-grid-reanudarTarea" : "icon-invisible") : "icon-invisible"
                                             ) : "icon-invisible", //action14 Reanudar Tarea
                    'action15'            => $boolGestionCompleta ? ($boolVerAnularTarea ? 'button-grid-anularTarea'
                                                : "icon-invisible") : "icon-invisible", // action15 Anular Tarea
                    'tareaEsHal'          => $boolEsHal,
                    'tipoAsignado'        => $datos['tipoAsignado'],
                    'esHal'               => ($boolEsHal ? '<b style="color:green">SI</b>' : 'NO'),
                    'tareaParametro'      => $boolTareaParametro,
                    'atenderAntes'        => ($boolAtenderAntes ? '<b style="color:green">SI</b>' : 'NO'),
                    'tieneProgresoRuta'   => $strTieneFibra,
                    'tieneProgresoMateriales' => $strTieneMateriales,
                    'idServicioVrf'       => $intIdServicioVrf,
                    'requiereControlActivo' => $strRequiereControlActivo,
                    'personaId'           => $intPersonaId,
                    'servicioId'          => $intServicioId,
                    'tipoMedioId'         => $arrayTipoMedio['tipoMedioId'],
                    'permiteRegistroActivos' => $boolPermiteRegAct,
                    'departamentoId'      => $strPersonaEmpresaRol['idDepartamento'],
                    'loginSesion'         => $strLoginSesion,
                    'intIdDetalleHist'    => $intIdDetalleHist,
                    'numBobinaVisualizar' => $intNumBobinaVisualizar,
                    'estadoNumBobinaVisual' => $strEstadoNumBobinaVisual,
                    'boolRenviarSysCloud' => $boolRenviarSysCloud,
                    'esInterdepartamental'=> $boolEsInterdep,
                    'permiteConfirIpSopTn'=> $boolConfirIpSopTn,
                    'permiteValidarEnlaceSopTn'  => $boolValEnlaSopTn,
                    'permiteCrearKml'     => $strPermiteCrearKml,
                    'strTieneConfirIpServ'=> $strTieneConfirmacionIPserv,
                    'ultimaMillaSoporte'  => $arrayDataUltimaMillaSoporte["ultimaMillaSoporte"],
                    'tipoCasoEnlace'      => $strTipoCasoEnlace,
                    'idTareaAnterior'     => ($arrayTareaAnterior[0]["idTarea"]?$arrayTareaAnterior[0]["idTarea"]
                                             :$datos["idTarea"]),
                    'nombreTareaAnterior' => ($arrayTareaAnterior[0]["nombreTarea"]?$arrayTareaAnterior[0]["nombreTarea"]
                                             :($datos["nombreTarea"] ? $datos["nombreTarea"] : "N/A"))
                );
            }

            $data = json_encode($arr_encontrados);
            $resultado = '{"total":"' . $intCantidad . '","encontrados":' . $data . '}';

            return $resultado;
        }
        else
        {
            $resultado = '{"total":"0","encontrados":[]}';
            return $resultado;
        }
    }
    /**
     * validarCaracteristicaIdDetalle
     *
     * Método validar si un detalle Id cuenta con una caracteristica 
     * en particular.
     *
     * @author Wilmer Vera <wvera@telconet.ec>
     * @version 1.0 23-11-2020
     * 
     */
    public function validarCaracteristicaIdDetalle($arrayData)
    {    
        $intIdDetalle            = $arrayData['idDetalle'];
        $strNombreCaracteristica = $arrayData['nombreCaracteristica'];
        $strIdComunicacion       = $arrayData['idComunicacion'];
        //se obtiene la caracteristica que se desea poner a nivel de tarea.
        $objAdmiCaracteristica = $this->_em->getRepository('schemaBundle:AdmiCaracteristica')
                                ->findOneBy(array ('descripcionCaracteristica' => $strNombreCaracteristica,
                                                    'estado'                    => 'Activo'));
        if (!is_object($objAdmiCaracteristica))
        {
            return "N";
        }
        $objInfoTareaCaracteristica = $this->_em->getRepository('schemaBundle:InfoTareaCaracteristica')
                    ->findOneBy(array ('detalleId'     => $intIdDetalle,
                                    'tareaId'          => $strIdComunicacion,
                                    'caracteristicaId' => $objAdmiCaracteristica->getId()));
        if(is_object($objInfoTareaCaracteristica))
        {
            return "S";
        }
    
        return "N";
    }
    /**
    * getRegistrosSubtareas
    *
    * Método que obtiene todas las subtareas de una tarea
    *
    * @param $comunicacionId Numero de la tarea principal
    * @param $emComunicacion Objeto de coneccion del esquema de comunicacion
    *
    * @return array $arrayResultado
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 22-07-2016
    *
    * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.1 15-08-2016 - Se realizan ajustes por cambios en la funcion getMinimaComunicacionPorDetalleId
    */
    public function generarJsonSubtareas($comunicacionId,$emComunicacion)
    {
        $arrayEncontrados = array();
        $arrayRegistros   = array();
        $arrayRegistros = $this->getRegistrosSubtareas($comunicacionId);

        $registros = $arrayRegistros["registros"];
        $total     = $arrayRegistros["total"];

        if ($registros) {

            foreach ($registros as $subTarea)
            {
                //Se obtiene el numero de la tarea en base al id_detalle
                $numeroTarea = $emComunicacion->getRepository('schemaBundle:InfoComunicacion')
                                              ->getMinimaComunicacionPorDetalleId($subTarea["idDetalle"]);

                $feSolicitada = ($subTarea["feSolicitada"] ? strval(date_format($subTarea["feSolicitada"], "d-m-Y H:i")) : "");

                $arrayEncontrados[]=array('numero_tarea' => $numeroTarea,
                                          'nombre_tarea' => $subTarea["nombreTarea"],
                                          'observacion'  => $subTarea["observacion"],
                                          'responsable'  => $subTarea["refAsignadoNombre"],
                                          'fecha_ejecu'  => $feSolicitada,
                                          'estado'       => $subTarea["estado"]);
            }

            $objSubtareas = json_encode($arrayEncontrados);
            $resultado = '{"total":"'.$total.'","encontrados":'.$objSubtareas.'}';
            return $resultado;
        }
        else
        {
            $resultado= '{"total":"0","encontrados":[]}';
            return $resultado;
        }
    }

    /**
     * getClienteAfectadoTarea
     *
     * Método para obtener el login del cliente afectado de una tarea.
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.0 26-02-2018
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 03-04-2018 Se regularizan cambios realizados en caliente
     *
     * @param array $arrayParametroCliente[
     *                                     intDetalleId:
     */
    public function getClienteAfectadoTarea($arrayParametroCliente)
    {
        $query     = $this->_em->createQuery();
        $strSelect = "SELECT infoPunto
                        FROM schemaBundle:InfoPunto infoPunto
                        WHERE infoPunto.id =
                          (SELECT infCom.puntoId
                          FROM schemaBundle:InfoComunicacion infCom
                          WHERE infCom.id =
                            (SELECT MIN(iCom.id)
                            FROM schemaBundle:InfoComunicacion iCom
                            WHERE iCom.detalleId = :intIdDetalle
                            AND iCom.puntoId    IS NOT NULL
                            )
                          )
                        AND infoPunto.estado = :strEstado";
        $query->setParameter('strEstado', 'Activo');
        $query->setParameter('intIdDetalle', $arrayParametroCliente['intIdDetalle']);
        $query->setDQL($strSelect);
        $objClienteAfectado = $query->getOneOrNullResult();
        return $objClienteAfectado;
    }

    /**
    * getRegistrosSubtareas
    *
    * Método que ejecuta el query para obtener todas las subtareas de una tarea
    *
    * @param $comunicacionId
    *
    * @return array $arrayResultado
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 22-07-2016
    *
    */
    public function getRegistrosSubtareas($comunicacionId)
    {
        $arrayResultado = array();
        $intTotal       = 0;
        $arrayDatos     = array();

        $query      = $this->_em->createQuery();
        $queryCount = $this->_em->createQuery();

        $campos = " SELECT d.id    AS idDetalle,
                    d.feCreacion AS feTareaCreada,
                    d.feSolicitada,
                    t.id AS idTarea,
                    t.nombreTarea,
                    t.descripcionTarea,
                    da.asignadoId,
                    da.asignadoNombre,
                    da.refAsignadoId,
                    da.refAsignadoNombre,
                    da.personaEmpresaRolId,
                    da.feCreacion     AS feTareaAsignada,
                    da.departamentoId AS idDepartamentoCreador,
                    dh.estado,
                    dh.feCreacion  AS feTareaHistorial,
                    dh.usrCreacion AS usrTareaHistorial,
                    da.tipoAsignado,
                    d.observacion ";

       $from = " FROM schemaBundle:InfoDetalle d,
                    schemaBundle:InfoDetalleAsignacion da,
                    schemaBundle:InfoDetalleHistorial dh,
                    schemaBundle:AdmiTarea t
                  WHERE d   = da.detalleId
                  AND d     = dh.detalleId
                  AND t     = d.tareaId
                  AND da.id =
                    (SELECT MAX(daMax.id)
                    FROM schemaBundle:InfoDetalleAsignacion daMax
                    WHERE daMax.detalleId = da.detalleId
                    )
                  AND dh.id =
                    (SELECT MAX(dhMax.id)
                    FROM schemaBundle:InfoDetalleHistorial dhMax
                    WHERE dhMax.detalleId = dh.detalleId
                    )
                  AND d.detalleIdRelacionado =
                    (SELECT infoComunicacion.detalleId
                    FROM schemaBundle:InfoComunicacion infoComunicacion
                    WHERE infoComunicacion.id = :tareaPadre
                    )
                  ORDER BY d.feSolicitada DESC ";

        $query->setParameter('tareaPadre', $comunicacionId);
        $queryCount->setParameter('tareaPadre', $comunicacionId);

        $sql = $campos . $from;

        $query->setDQL($sql);

        $arrayDatos = $query->getResult();

        $camposCount = " SELECT COUNT(d.id) ";

        $sqlCount = $camposCount . $from;

        $queryCount->setDQL($sqlCount);

        $intTotal = $queryCount->getScalarResult();

        $arrayResultado['total']      = $intTotal;
        $arrayResultado['registros']  = $arrayDatos;

        return $arrayResultado;
    }

    /**
    * getTiempoTotalTarea
    *
    * Método encargado de obtener el tiempo total de una tarea
    *
    * @param array $arrayParametros[ 'intDetalleId' => ID del detalle de la tarea ]
    *
    * @return integer $intMinutos
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 10-11-2016
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.1 05-12-2016 - Se realizan ajustes para considerar la primera vez que fue iniciada una tarea
    */
    public function getTiempoTotalTarea($arrayParametros)
    {
        $strFechaInicioTarea   = "";
        $intTiempoTareaPausada = 0;

        // Se verifica si la tarea pertenece a un caso
        $arrayCaso = $this->tareaPerteneceACaso($arrayParametros["intDetalleId"]);

        if($arrayCaso[0]['caso'] != 0)
        {
            $strBandCaso = "S";
        }
        else
        {
            $strBandCaso = "N";
        }

        $dateTareaFechaInicio = $this->getTareaIniciada($arrayParametros);

        $objTiempoTareaReanudada = $this->_em->getRepository('schemaBundle:InfoTareaTiempoParcial')
                                             ->findOneBy(array('detalleId' => $arrayParametros["intDetalleId"],
                                                               'estado'    => 'Reanudada'));

        if($dateTareaFechaInicio != "" && $strBandCaso == "N")
        {
            $strFechaInicioTarea = strval(date_format($dateTareaFechaInicio, "d-m-Y H:i"));
        }
        else
        {
            $objInfoDetalle = $this->_em->getRepository('schemaBundle:InfoDetalle')
                                         ->find($arrayParametros["intDetalleId"]);

            $strFechaInicioTarea = strval(date_format($objInfoDetalle->getFeCreacion(), "d-m-Y H:i"));
        }

        if(is_object($objTiempoTareaReanudada))
        {
            $intTiempoTareaPausada = $objTiempoTareaReanudada->getValorTiempoPausa();
        }

        $dateFechaCreacionTarea = new \DateTime($strFechaInicioTarea);
        $dateFechaActual        = new \DateTime();

        $dateTimeDiferenciaFechas = $dateFechaActual->diff($dateFechaCreacionTarea);

        $intMinutos = $dateTimeDiferenciaFechas->days * 24 * 60;
        $intMinutos += $dateTimeDiferenciaFechas->h * 60;
        $intMinutos += $dateTimeDiferenciaFechas->i;

        $intMinutos = $intMinutos - $intTiempoTareaPausada;

        return $intMinutos;
    }

    /**
     * getRegistrosMisTareas
     *
     * Método que ejecuta el query para obtener todas las tareas por filtro ingresado
     *
     * @param $parametros
     * @param $start
     * @param $limit
     * @param $tipo
     * @return array $arrayResultado
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 3.7 15-06-2019 - Se agrega un IN en la consulta de cuadrillas y estados.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 3.6 09-05-2019 - En la consulta se devuelve los campos *asignadoIdHis* y *departamentoOrigenIdHis*.
     *
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 3.5 06-12-2018 Se cambia parametro intPersonaEmpresaRol por arrayPersonaEmpresaRol para recibir mas de un idPersonaEmpresaRol
     * y que se pueda consultar tareas de varias empresas del usuario.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 3.4 05-12-2018 - Se valida si la fecha por defecto es nula para considerar todo los registros que se encuentren.
     *
     * @author Germán Valenzuela <mlcruz@telconet.ec>
     * @version 3.3 04-12-2018 - Se agrega la fecha por defecto para obtener las tareas por el año indicado.
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 3.2 25-06-2018 Se agrega el filtro estadosTareaNotIn para buscar tareas que no se encuentren en dichos estados
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 3.1 31-05-2018 Se agrega el filtro de proceso en la consulta de tareas
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 3.0 19-01-2018 Se realizan ajustes por indicador de tareas pendientes por departamento
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.9 08-02-2017 Se agrega la información de la longitud y latitud en la consulta
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.8 02-02-2017 - Se modifica la consulta para obtener la observación del último historial de la tarea
     *
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.7 20-11-2016 - Se agrega el estado Anulada en el arreglo de estados que no se deben consultar en el listado principal de tareas
     * 
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.6 25-10-2016 - Se realizan ajustes para presentar las tareas pendientes del usuario en session
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.5 22-07-2016 - Se realizan ajustes para implementar el concepto de subtareas
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.4 24-05-2016 - Se realizan ajustes para poder buscar a que ciudad fue asignada la tarea
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.3 24-05-2016 - Se realizan ajustes para poder buscar por el departamento que creo la tarea
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.2 17-05-2016 - Se hacen ajustes para validar que no se pueda gestionar la tarea cuando no pertenesca al departamento
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.1 12-05-2016 - Se quita filtro de empresaCod en join de tabla ADMIPROCESOEMPRESA
     * 
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.0 06-05-2016 - Se modifica por opcion Ver Archivos
     * 
     * @author Modificado: Jesus Bozada <jbozada@telconet.ec>
     * @version 1.9 10-05-2016 - Se agrega filtro de empresaCod en join de tabla ADMIPROCESOEMPRESA
     * 
     * @author Modificado: Allan Suárez <arsuarez@telconet.ec>
     * @version 1.8 13-10-2015 - Se modifica el SELECT para que se muestren todas las tareas
     *                           creadas, incluyendo las que se asignen a CUADRILLA
     * 
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.7 19-08-2015 - Se modifica el SELECT añadiendo el campo 't.descripcionTarea'
     *                           para conocer el motivo por el cual se abrió dicha tarea. 
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.6 11-08-2015 - Mejora a la función para que retorne el total y los 
     *                           datos en una sola llamada a la función. Además se envía 
     *                           el parámetro 'filtroUsuario' para que busque las tareas 
     *                           relacionadas al usuario logueado, y el parámetro 
     *                           'filtroGroupBy' para que agrupe la consulta. 
     * 
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.5 16-01-2015 Se parametriza query
     * 
     * @version 1.0 Version Inicial
     */
    public function getRegistrosMisTareas($parametros, $start, $limit , $tipo)
    {
        $boolBusqueda   = false;
        $where          = "";
        $fromAdicional  = "";
        $select         = "";
        $strGroupBy     = "";
        $strOrderBy     = "";
        $strTablas      = "";
        $strWhere       = "";
        $arrayResultado = array();
        $intTotal       = 0;
        $arrayDatos     = array();                
        
        $query = $this->_em->createQuery();

        if($parametros && count($parametros) > 0)
        {
            if(isset($parametros["tarea"]))
            {
                if($parametros["tarea"] && $parametros["tarea"] != "")
                {
                    $where .= "AND t.id = :tarea ";
                    $query->setParameter('tarea', $parametros["tarea"]);
                }
            }

            if(isset($parametros["departamentoOrig"]))
            {
                if($parametros["departamentoOrig"] && $parametros["departamentoOrig"] != "")
                {
                    $where .= "AND da.departamentoId = :departamentoId ";
                    $query->setParameter('departamentoId', $parametros["departamentoOrig"]);
                }
            }


            if(isset($parametros["ciudadOrigen"]))
            {
                if($parametros["ciudadOrigen"] && $parametros["ciudadOrigen"] != "")
                {
                    $where .= " AND da.cantonId = :cantonIdO ";
                    $query->setParameter('cantonIdO', $parametros["ciudadOrigen"]);
                }
            }

            if(isset($parametros["estado"]))
            {
                if($parametros["estado"] && $parametros["estado"] != "" && $parametros["estado"] != "Todos")
                {
                    if (is_array($parametros['estado']))
                    {
                        $parametros['estado'] = array_map('strtoupper', $parametros['estado']);
                        $where .= "AND UPPER(dh.estado) IN (:estado) ";
                        $query->setParameter('estado', $parametros["estado"]);
                    }
                    else
                    {
                        $where .= "AND UPPER(dh.estado) like UPPER(:estado) ";
                        $query->setParameter('estado', '%' . trim($parametros["estado"]) . '%');
                    }
                }
            }

            if(isset($parametros["estadosTareaNotIn"]) && !empty($parametros["estadosTareaNotIn"]))
            {
                $where .= "AND dh.estado NOT IN ( :paramEstadosTareaNotIn ) ";
                $query->setParameter('paramEstadosTareaNotIn', $parametros["estadosTareaNotIn"]);
            }

            if(isset($parametros["tareaPadre"]))
            {
                if($parametros["tareaPadre"] && $parametros["tareaPadre"] != "")
                {
                    $where .= "AND d.detalleIdRelacionado = (SELECT infoComunicacion.detalleId FROM schemaBundle:InfoComunicacion infoComunicacion "
                           .  " WHERE infoComunicacion.id = :tareaPadre) ";
                    $query->setParameter('tareaPadre',$parametros["tareaPadre"]);
                }
            }

            if(isset($parametros["feSolicitadaDesde"]))
            {
                if($parametros["feSolicitadaDesde"] != "")
                {
                    $dateF = explode("-", $parametros["feSolicitadaDesde"]);
                    $fechaSql = date("Y/m/d", strtotime($dateF[2] . "-" . $dateF[1] . "-" . $dateF[0]));

                    $boolBusqueda = true;
                    
                    $where .= "AND d.feSolicitada >= :feSolicitadaMin ";
                    $query->setParameter('feSolicitadaMin',  trim($fechaSql) );
                }
            }

            if(isset($parametros["feSolicitadaHasta"]))
            {
                if($parametros["feSolicitadaHasta"] != "")
                {
                    $dateF = explode("-", $parametros["feSolicitadaHasta"]);
                    $fechaSqlAdd = strtotime(date("Y-m-d", strtotime($dateF[2] . "-" . $dateF[1] . "-" . $dateF[0])) . " +1 day");
                    $fechaSql = date("Y/m/d", $fechaSqlAdd);

                    $boolBusqueda = true;
                    
                    $where .= "AND d.feSolicitada <= :feSolicitadaMax ";
                    $query->setParameter('feSolicitadaMax',  trim($fechaSql) );
                }
            }

            if(isset($parametros["feFinalizadaDesde"]))
            {
                if($parametros["feFinalizadaDesde"] != "")
                {
                    $dateF = explode("-", $parametros["feFinalizadaDesde"]);
                    $fechaSql = date("Y/m/d", strtotime($dateF[2] . "-" . $dateF[1] . "-" . $dateF[0]));

                    $boolBusqueda = true;
                    
                    $where .= "AND dh.feCreacion >= :feCreacionMin ";
                    $query->setParameter('feCreacionMin',  trim($fechaSql) );
                }
            }

            if(isset($parametros["feFinalizadaHasta"]))
            {
                if($parametros["feFinalizadaHasta"] != "")
                {
                    $dateF = explode("-", $parametros["feFinalizadaHasta"]);
                    $fechaSqlAdd = strtotime(date("Y-m-d", strtotime($dateF[2] . "-" . $dateF[1] . "-" . $dateF[0])) . " +1 day");
                    $fechaSql = date("Y/m/d", $fechaSqlAdd);

                    $boolBusqueda = true;
                    
                    $where .= "AND dh.feCreacion <= :feCreacionMax ";
                    $query->setParameter('feCreacionMax',  trim($fechaSql) );
                }
            }

            $strWhere = " AND da.id = (SELECT MAX(daMax.id)
                                                FROM schemaBundle:InfoDetalleAsignacion daMax
                                                WHERE daMax.detalleId = da.detalleId)
                        AND dh.id = (SELECT MAX(dhMax.id)
                                                FROM schemaBundle:InfoDetalleHistorial dhMax
                                                WHERE dhMax.detalleId = dh.detalleId) ";

            if(isset($parametros["tipo"]))
            {
                if($parametros["tipo"] == 'ByDepartamento')
                {
                    if(isset($parametros["idDepartamento"]) && isset($parametros['nombreAsignado']))
                    {
                        if($parametros["tipo"] == "ByDepartamento")
                        {
                            if($parametros["strOrigen"] == "tareasPorDepartamento")
                            {
                                $strTablas = " , schemaBundle:InfoDetalleTareas ta ";
                                $strWhere = "   AND ta.id = d.id
                                                AND da.id = ta.detalleAsignacionId
                                                AND dh.id = ta.detalleHistorialId ";

                                if ($parametros["booleanVerTareasTodasEmpresa"])
                                {
                                        $where .= " AND d.id IN 
                                                    ( 
                                                        SELECT a.id 
                                                            FROM schemaBundle:InfoDetalleTareas a
                                                        WHERE a.departamentoId IN (:paramDepartamentosEmpresas)
                                                          AND a.estado NOT IN (:paramEstadoHistorial)
                                                    )
                                                    AND dh.estado NOT IN ( :paramEstadoHistorial )";

                                        if ($parametros["existeFiltro"] === "S")
                                        {
                                            $query->setParameter(':paramDepartamentosEmpresas',array($parametros["idDepartamento"]));
                                        }
                                        else
                                        {
                                            $query->setParameter(':paramDepartamentosEmpresas',$parametros["arrayDepartamentos"]);
                                        }
                                }
                                else
                                {
                                    if($parametros["strTieneCredencial"] == "S")
                                    {
                                        $where .= " AND d.id IN
                                                    (
                                                        SELECT a.id
                                                            FROM schemaBundle:InfoDetalleTareas a
                                                        WHERE a.departamentoId = :paramDepartamentoSession
                                                          AND a.estado NOT IN (:paramEstadoHistorial)
                                                    )
                                                    AND dh.estado NOT IN (:paramEstadoHistorial) ";
                                        
                                        $query->setParameter('paramDepartamentoSession',$parametros["departamentoSession"]);
                                    }
                                    else
                                    {
                                        $where .= " AND d.id IN
                                                    (
                                                        SELECT a.id
                                                            FROM schemaBundle:InfoDetalleTareas a
                                                        WHERE a.departamentoId = :paramDepartamentoSession
                                                          AND a.oficinaId      = :paramOficinaSession
                                                          AND a.estado NOT IN (:paramEstadoHistorial)
                                                    )
                                                    AND dh.estado NOT IN (:paramEstadoHistorial) ";

                                        $query->setParameter('paramDepartamentoSession',$parametros["departamentoSession"]);
                                        $query->setParameter('paramOficinaSession',  $parametros["oficinaSession"] );
                                    }
                                }
                                $query->setParameter('paramEstadoHistorial', array('Finalizada','Cancelada','Rechazada','Anulada'));
                            }
                            else
                            {
                                if($parametros["tareaPadre"] == "" || $parametros["tareaPadre"] == null)
                                {
                                    if($parametros["strOpcionBusqueda"] == "N" && count($parametros['arrayPersonaEmpresaRol']) > 0)
                                    {
                                        $strTablas = " , schemaBundle:InfoDetalleTareas ta ";
                                        $strWhere = " AND ta.id = d.id
                                                      AND da.id = ta.detalleAsignacionId
                                                      AND dh.id = ta.detalleHistorialId ";

                                        $where .= "AND da.personaEmpresaRolId IN ( :paramPersonaEmpresaRol )
                                                   AND dh.estado NOT IN ( :paramEstadoHistorial )";
                                        $query->setParameter('paramPersonaEmpresaRol',  $parametros["arrayPersonaEmpresaRol"] );
                                        $query->setParameter('paramEstadoHistorial', array('Finalizada','Cancelada','Rechazada','Anulada'));
                                    }
                                    else
                                    {
                                        $where .= "AND da.asignadoId = :asignadoId
                                                AND UPPER(da.asignadoNombre) = UPPER(:asignadoNombre) ";
                                        $query->setParameter('asignadoId',  $parametros["idDepartamento"] );
                                        $query->setParameter('asignadoNombre',  $parametros['nombreAsignado'] );
                                    }
                                }
                            }
                        }
                    }
                    
                    if(isset($parametros["idUsuario"]))
                    {
                        if($parametros["filtroUsuario"] == "ByUsuario")
                        {
                            $where .= "AND da.refAsignadoId = :refAsignadoId ";
                            $query->setParameter('refAsignadoId',  $parametros["idUsuario"] );
                        }
                    }

                    if(!empty($parametros["intProceso"]))
                    {
                        $where .= "AND t.procesoId = :paramProcesoId ";
                        $query->setParameter('paramProcesoId',  $parametros["intProceso"] );
                    }

                    if(isset($parametros["asignado"]))
                    {
                        if($parametros["asignado"] && $parametros["asignado"] != "")
                        {
                            $where .= "AND UPPER(da.refAsignadoNombre) like UPPER(:refAsignadoNombre) ";
                            $query->setParameter('refAsignadoNombre',  '%'.trim($parametros["asignado"]).'%' );
                        }
                    }
                }//Se consulta informacion por Cuadrilla
                else if($parametros["tipo"] == 'ByCuadrilla')
                {
                    if(isset($parametros["idCuadrilla"]) && isset($parametros['nombreAsignado']))
                    {                       
                        if($parametros["idCuadrilla"]=='Todos')
                        {
                            $where .= "AND da.tipoAsignado = :tipoAsignado ";
                            $query->setParameter('tipoAsignado',  'CUADRILLA' );
                        }
                        else
                        {
                            if (is_array($parametros['nombreAsignado']))
                            {
                                $parametros['nombreAsignado'] = array_map('strtoupper', $parametros['nombreAsignado']);
                            }
                            else
                            {
                                $parametros['nombreAsignado'] = strtoupper($parametros['nombreAsignado']);
                            }

                            $where .= "AND da.asignadoId IN (:asignadoId)
                                       AND UPPER(da.asignadoNombre) IN (:asignadoNombre)
                                       AND da.tipoAsignado = :tipoAsignado ";
                            $query->setParameter('asignadoId',  $parametros["idCuadrilla"] );
                            $query->setParameter('asignadoNombre',  $parametros['nombreAsignado'] );
                            $query->setParameter('tipoAsignado',  'CUADRILLA' );
                        }
                    }
                }
                else //CASO
                {             
                    $boolFiltroCuadrilla = false;
                    if(isset($parametros["idCuadrilla"]) && $parametros["idCuadrilla"]!='')
                    {
                        if($parametros["idCuadrilla"]=='Todos')
                        {
                            $where .= "AND da.tipoAsignado = :tipoAsignado ";
                            $query->setParameter('tipoAsignado',  'CUADRILLA' );
                        }
                        else
                        {
                            if (is_array($parametros['nombreAsignado']))
                            {
                                $parametros['nombreAsignado'] = array_map('strtoupper', $parametros['nombreAsignado']);
                            }
                            else
                            {
                                $parametros['nombreAsignado'] = strtoupper($parametros['nombreAsignado']);
                            }

                            $where .= "AND da.asignadoId IN (:asignadoId)
                                       AND UPPER(da.asignadoNombre) IN (:asignadoNombre)
                                       AND da.tipoAsignado = :tipoAsignado ";
                            $query->setParameter('asignadoId',  $parametros["idCuadrilla"] );
                            $query->setParameter('asignadoNombre',  $parametros['nombreAsignado'] );
                            $query->setParameter('tipoAsignado',  'CUADRILLA' );
                        }
                        $boolFiltroCuadrilla = true;
                    }
                    if(isset($parametros["asignado"]) && $parametros["asignado"] != "" && !$boolFiltroCuadrilla)
                    {
                        if($parametros["asignado"] && $parametros["asignado"] != "")
                        {
                            $where .= "AND UPPER(da.refAsignadoNombre) like UPPER(:refAsignadoNombre) ";
                            $query->setParameter('refAsignadoNombre',  '%'.trim($parametros["asignado"]).'%' );                                                        
                        }
                        if(isset($parametros["idDepartamento"]) && isset($parametros['nombreAsignado']))
                        {
                            $where .= "AND da.asignadoId = :asignadoId 
                                      AND UPPER(da.asignadoNombre) = UPPER(:asignadoNombre) ";
                            $query->setParameter('asignadoId',  $parametros["idDepartamento"] );
                            $query->setParameter('asignadoNombre',  $parametros['nombreAsignado'] );

                        }
                    }                    
                }
            }
          
            if(isset($parametros["cliente"]))
            {
                if($parametros["cliente"] && $parametros["cliente"] != "")
                {
                    $where .= "AND ( 
									SELECT count(pa1)
									FROM schemaBundle:InfoParteAfectada pa1  
									WHERE d.id = pa1.detalleId 
									AND lower(pa1.tipoAfectado) = lower(:tipoAfectado)
									AND pa1.afectadoId = :afectadoId
								   ) > 0 ";   
                    $query->setParameter('afectadoId',  $parametros["cliente"] );
                    $query->setParameter('tipoAfectado',  'Cliente' );
                }
            }
            
            if(isset($parametros["actividad"]))
            {
                if($parametros["actividad"] && $parametros["actividad"] != "")
                {
                    $fromAdicional .= " , schemaBundle:InfoComunicacion icom ";
                    $where .= "AND icom.detalleId = d.id 
                               AND icom.id = :numeroActividad ";
                    $query->setParameter('numeroActividad',  $parametros["actividad"] );
                }
            }
            
            if(isset($parametros["caso"]))
            {
                if($parametros["caso"] && $parametros["caso"] != "")
                {
                    $fromAdicional .= " , schemaBundle:InfoCaso icaso ,
                                          schemaBundle:InfoDetalleHipotesis idhi  ";
                    
                    $where .= "AND icaso.numeroCaso = :numeroCaso
                               AND icaso.id = idhi.casoId
			                   AND idhi.id  = d.detalleHipotesisId
                               ";
                    
                    $query->setParameter('numeroCaso',  $parametros["caso"] );
                }
            }

            if(isset($parametros["ciudadDestino"]))
            {
                if($parametros["ciudadDestino"] && $parametros["ciudadDestino"] != "")
                {
                    $fromAdicional .= " , schemaBundle:InfoPersonaEmpresaRol infoPersonaEmpresaRol ";

                    $where .= " AND da.personaEmpresaRolId = infoPersonaEmpresaRol.id
                                AND infoPersonaEmpresaRol.oficinaId IN (SELECT infoOficinaGrupo.id FROM schemaBundle:InfoOficinaGrupo infoOficinaGrupo
                                WHERE infoOficinaGrupo.cantonId = :cantonIdD ) ";

                    $query->setParameter('cantonIdD',  $parametros["ciudadDestino"] );
                }
            }
        }

        if ( (!isset($parametros["feFinalizadaHasta"]) || $parametros["feFinalizadaHasta"] === '') &&
             (!isset($parametros["feFinalizadaDesde"]) || $parametros["feFinalizadaDesde"] === '') &&
             (!isset($parametros["feSolicitadaHasta"]) || $parametros["feSolicitadaHasta"] === '') &&
             (!isset($parametros["feSolicitadaDesde"]) || $parametros["feSolicitadaDesde"] === ''))
        {
            if (isset($parametros["strFechaDefecto"]) && $parametros["strFechaDefecto"] !== '')
            {
                $strFechaDefault = date("Y/m/d", strtotime($parametros['strFechaDefecto']));
            }

            if (!empty($strFechaDefault) && $strFechaDefault !== '' && $strFechaDefault !== null)
            {
                $where .= " AND d.feCreacion >= :strFechaDefault ";
                $query->setParameter('strFechaDefault', $strFechaDefault);
            }
        }
        $from = "FROM 
					schemaBundle:InfoDetalle d,
					schemaBundle:InfoDetalleAsignacion da,
					schemaBundle:InfoDetalleHistorial dh,
					schemaBundle:AdmiTarea t $strTablas
		$fromAdicional";

        $wher = "WHERE
					d = da.detalleId 
					AND d = dh.detalleId 
					AND t = d.tareaId
                $strWhere
                $where ";
        
        $strOrderBy = " ORDER BY d.feSolicitada DESC ";
           
        if( isset($parametros["filtroGroupBy"]) )
        {
            if( $parametros["filtroGroupBy"] == "estados" )
            {
                $select .= " SELECT COUNT(d.id) as total, dh.estado ";

                $strGroupBy .= " GROUP BY dh.estado ";

                $strOrderBy = " ORDER BY dh.estado ASC ";
            }        
        }
        else //Devuelve los registros de acuerdo al filtro
        {
            $select .= " SELECT 
                            dh.asignadoId            as asignadoIdHis,
                            dh.departamentoOrigenId  as departamentoOrigenIdHis,
                            d.id as idDetalle,
                            d.latitud,
                            d.longitud,
                            d.usrCreacion as usrCreacionDetalle,
                            d.detalleIdRelacionado,
                            d.feCreacion as feTareaCreada, 
                            d.feSolicitada, 
                            t.id as idTarea, 
                            t.nombreTarea,
                            t.descripcionTarea, 
							da.asignadoId, 
                            da.asignadoNombre, 
                            da.refAsignadoId, 
                            da.refAsignadoNombre,
                            da.personaEmpresaRolId,
                            da.feCreacion as feTareaAsignada, 
                            da.departamentoId as idDepartamentoCreador,
                                                       dh.estado,
                            dh.feCreacion as feTareaHistorial, 
                            dh.usrCreacion as usrTareaHistorial,
                            dh.observacion as observacionHistorial,
                            da.tipoAsignado,
                            d.observacion ";
        }

        $sql = $select.$from.$wher.$strGroupBy.$strOrderBy;

        $query->setDQL($sql);
        $arrayDatos = $query->getResult(); 
        $intTotal   = count($arrayDatos);
        if( $intTotal > 0 )
        {
            if( $start != '' && $limit != '' )
            {
                $arrayDatos = array();
                $arrayDatos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
            }
        }
        
        $arrayResultado['total']      = $intTotal;
        $arrayResultado['resultados'] = $arrayDatos;
        
        return $arrayResultado;
    }

    public function tareaPerteneceACaso($id_detalle){
    
	    $sql = "select count(a.detalleHipotesisId) as caso 
		    from 
		    schemaBundle:InfoDetalle a 
		    where a.id = $id_detalle ";
	    
	    $query = $this->_em->createQuery($sql);
	    	    
	    $registros = $query->getResult();			
	    return $registros;
    }
    
    public function getCasoPadreTarea($id_detalle){
    
	    $sql = "select b
		    from 
		    schemaBundle:InfoDetalle a ,
		    schemaBundle:InfoDetalleHipotesis b
		    where a.id = $id_detalle  
		    and a.detalleHipotesisId = b.id";
	    
	    $query = $this->_em->createQuery($sql);	    	    
	    	    
	    $registros = $query->getResult();			
	    return $registros;
    }

    /**
     * Función que obtiene la información del caso según una tarea.
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0
     * @since 13-11-2018
     */
    public function getCasoPadreDesdeTarea($intIdDetalle)
    {
        $objRsm   = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsm);
        $strSql   = "SELECT
                        C.ID_CASO, NUMERO_CASO
                    FROM
                        DB_SOPORTE.INFO_DETALLE A,
                        DB_SOPORTE.INFO_DETALLE_HIPOTESIS B,
                        DB_SOPORTE.INFO_CASO C
                    WHERE
                        A.ID_DETALLE = :intIdDetalle
                        AND A.DETALLE_HIPOTESIS_ID = B.ID_DETALLE_HIPOTESIS
                        AND B.CASO_ID = C.ID_CASO
                        AND ROWNUM = 1";
        $objQuery->setParameter("intIdDetalle", $intIdDetalle);

        $objRsm->addScalarResult('NUMERO_CASO', 'strNumeroCaso', 'string');
        $objRsm->addScalarResult('ID_CASO', 'intIdCaso', 'integer');
        $objQuery->setSQL($strSql);
        $arrayRespuesta = $objQuery->getScalarResult();

        return $arrayRespuesta[0];
    }
    
    
	
	
    public function getRegistrosAfectadosTotal($id_detalle="", $tipoAfectado="Cliente", $retorna="Data")
    {
		$where = "";
		if($tipoAfectado && $tipoAfectado!="")
		{
			$where .= "AND lower(pa.tipoAfectado) = lower('$tipoAfectado') ";
		}		
		
        $fromWhere = " 
						FROM 
						schemaBundle:InfoParteAfectada pa, 
						schemaBundle:InfoCriterioAfectado ca, 
						schemaBundle:InfoDetalle de 
				
						WHERE 
						pa.criterioAfectadoId = ca.id 
						AND ca.detalleId = de.id 
						AND pa.detalleId = ca.detalleId
						AND pa.detalleId = de.id
						AND de.id = '$id_detalle' 				
						$where 
					 ";							
							
		if($retorna == "Data")
		{
			$selectedData = "pa.id as id_parte_afectada, pa.afectadoId, pa.afectadoNombre, pa.afectadoDescripcionId, pa.afectadoDescripcion, pa.tipoAfectado  ";
			$sql = "SELECT $selectedData $fromWhere ";		
			$query = $this->_em->createQuery($sql);
			
			$registros = $query->getResult();			
			return $registros;
		}			
		else
		{
			$selectedCont = "count(ca) as cont ";
			$sql = "SELECT $selectedCont $fromWhere ";
			$query = $this->_em->createQuery($sql); 
			
			$resultTotal = $query->getOneOrNullResult();
			$total = ($resultTotal ? ($resultTotal["cont"] ? $resultTotal["cont"] : 0) : 0);
		
			return $total;
		}
	}
	
	
	public function esUltimaTareaAbierta($casoId){
	
	       $sql = "select count(a) as numeroTareas
		    from 
		    schemaBundle:InfoDetalle a ,
		    schemaBundle:InfoDetalleHipotesis b,
		    schemaBundle:InfoCaso c
		    where c.id = $casoId 
		    and b.casoId = c.id 
		    and b.id = a.detalleHipotesisId
		    and a.esSolucion is null and a.tareaId is not null";
	    
	      $query = $this->_em->createQuery($sql);	    	    
		      
	      $registros = $query->getResult();			
	      return $registros;
	
	
	}
	
	
	/**
      * getNumeroTareasByFormaContacto
      *
      * Método que retornará el total de las tareas de un cliente de acuerdo a las formas 
      * de contacto                                   
      *
      * @param integer  $intFormaContacto La forma de contacto
      * @param integer  $intPuntoCliente  Id del punto cliente
      * @return array   $intResultados
      *
      * @author Edson Franco <efranco@telconet.ec>
      * @version 1.0 21-08-2015
      */
    public function getNumeroTareasByFormaContacto($intFormaContacto, $intPuntoCliente)
    {
        $strSelect = "SELECT COUNT(idet.id) as contador ";
        $strFrom   = "FROM schemaBundle:InfoParteAfectada ipa,
                           schemaBundle:InfoDetalle idet,
                           schemaBundle:InfoComunicacion ic ";

        $strWhere  = "WHERE ipa.detalleId = idet.id
                        AND idet.id = ic.detalleId
                        AND idet.tareaId IS NOT NULL
                        AND ipa.afectadoId = :puntoCliente
                        AND ic.formaContactoId = :formaContacto ";

        $strSql    = $strSelect.$strFrom.$strWhere;

        $query = $this->_em->createQuery($strSql);	
        
        $query->setParameter('formaContacto', $intFormaContacto );
        $query->setParameter('puntoCliente',  $intPuntoCliente );    	    

        $intResultados = $query->getOneOrNullResult();

        return $intResultados;
    }
	
    /**
      * generarJsonDetallesTareasTNXParametros
      *
      * Método que retornará las tareas para cerrar un caso TN de acuerdo a los parámetros enviados                                   
      *
      * @param array $parametros
      * @return json $resultado
      *
      * @author Lizbeth Cruz <mlcruz@telconet.ec>
      * @version 1.0 23-05-2016
      * 
      * @author Lizbeth Cruz <mlcruz@telconet.ec>
      * @version 1.1 22-06-2016 Se realizan cambios de acuerdo a los formatos de calidad establecidos
      */
    public function generarJsonDetallesTareasTNXParametros($parametros)
    {
        $arrayEncontrados = array();
        $arrayResultado = $this->getResultadoDetallesTareasTNXParametros($parametros);
        $resultado  = $arrayResultado['resultado'];
        $intTotal   = $arrayResultado['total'];
        $total = 0;

        if($resultado)
        {
            $total = $intTotal;
            foreach($resultado as $data)
            {
                $esSolucion =  ($data["esSolucion"] ? ($data["esSolucion"]=='S' ? "SI" : "NO") : "NO") ;

                $arrayEncontrados[]=array(
                                        'id_detalle'            => $data["idDetalle"],
                                        'id_tarea'              => $data["idTarea"],
                                        'nombre_tarea'          => $data["nombreTarea"],
                                        'estado'                => $data["estado"],
                                        'esSolucion'            => $esSolucion,
                                        'es_solucion_TN'        => ($esSolucion =="SI" ? "1" : "0"), 
                                        'observacion_detalle'   => $data["observacion"],
                                        'asignado_nombre'       => $data["asignadoNombre"],
                                        'action1'               => 'button-grid-show',
                                        'numero_tarea'          => $data["numeroTarea"],
                                        );	
            }
        }

        $arrayRespuesta = array('total' => $total, 'encontrados' => $arrayEncontrados);
        $jsonData = json_encode($arrayRespuesta);
        return $jsonData;
    }
    
    
    /**
      * getResultadoDetallesTareasTNXParametros
      *
      * Método que retornará las tareas para cerrar un caso TN de acuerdo a los parámetros enviados                                   
      *
      * @param array $parametros
      * @return json $resultado
      *
      * @author Lizbeth Cruz <mlcruz@telconet.ec>
      * @version 1.0 01-06-2016
      * 
      * @author Lizbeth Cruz <mlcruz@telconet.ec>
      * @version 1.1 22-06-2016 Se realizan cambios de acuerdo a los formatos de calidad establecidos
      * 
      * @author Lizbeth Cruz <mlcruz@telconet.ec>
      * @version 1.2 05-08-2016 Se parametriza el estado de la tarea
      */
    public function getResultadoDetallesTareasTNXParametros($parametros)
    {
        $arrayRespuesta['resultado']   = "";
        $arrayRespuesta['total']       = 0;
        
        try
        {
            $query          = $this->_em->createQuery();
            $queryCount     = $this->_em->createQuery();
            
            $strSelectTarea = ", (SELECT MIN(ic.id) FROM schemaBundle:InfoComunicacion ic WHERE ic.detalleId = d.id ) as numeroTarea ";
            $sqlSelect      = " SELECT dh.id as idDetalleHistorial, d.id as idDetalle, t.id as idTarea, t.nombreTarea, dh.estado, d.esSolucion,
                                da.asignadoNombre, dh.observacion " . $strSelectTarea;
            $sqlSelectCount = "SELECT COUNT(dh.id) ";

            $sqlFrom = "FROM 
                        schemaBundle:InfoDetalle d,
                        schemaBundle:InfoDetalleHipotesis dhi,  
                        schemaBundle:InfoDetalleHistorial dh,
                        schemaBundle:InfoDetalleAsignacion da, 
                        schemaBundle:AdmiTarea t 
                        WHERE d.tareaId = t.id 
                        AND dhi.casoId = :idCaso
                        AND d.detalleHipotesisId = dhi.id 
                        AND dh.detalleId = d.id 
                        AND dh.id = (SELECT MAX(dhMax.id) 
                                      FROM schemaBundle:InfoDetalleHistorial dhMax
                                      WHERE dhMax.detalleId = dh.detalleId) 
                        AND da.detalleId = d.id
                        AND da.id        = (select MAX(da1.id) from schemaBundle:InfoDetalleAsignacion da1 where da1.detalleId = d.id ) ";

            $sqlOrderBy =" ORDER BY dh.feCreacion ASC ";
            
            $query->setParameter("idCaso", $parametros["idCaso"]);
            $queryCount->setParameter("idCaso", $parametros["idCaso"]);
            
            $strWhere   = "";
            if(isset($parametros["estado"]))
            {
                if($parametros["estado"] && $parametros["estado"]!="")
                {
                    $strWhere .= "AND dh.estado    = :estadoTarea ";
                    $query->setParameter("estadoTarea", $parametros["estado"]);
                    $queryCount->setParameter("estadoTarea", $parametros["estado"]);
                }
            }
            
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
     * 
     * Metodo encargado de consultar el detalle segun la solicitud y nombre de tarea para procesos de DC
     * 
     * Costo 7
     * 
     * @author Allan Suarez <arsuarez@telconet.ec> 
     * @version 1.0 15-05-2018
     * 
     * @param Array $arrayParametros
     * @return Array Object 
     */
    public function getDetalleAsignadoPorSolicitudYTarea($arrayParametros)
    {
        $objResultSetMap = new ResultSetMappingBuilder($this->_em);
        $objNativeQuery = $this->_em->createNativeQuery(null, $objResultSetMap);
        
        $strSql = "   SELECT 
                        DETALLE.*
                      FROM 
                        DB_COMERCIAL.ADMI_TIPO_SOLICITUD    TIPO,
                        DB_COMERCIAL.INFO_DETALLE_SOLICITUD SOLICITUD,
                        DB_SOPORTE.INFO_DETALLE             DETALLE,
                        DB_SOPORTE.ADMI_TAREA               TAREA
                      WHERE 
                      TIPO.ID_TIPO_SOLICITUD = SOLICITUD.TIPO_SOLICITUD_ID 
                      AND SOLICITUD.ID_DETALLE_SOLICITUD = DETALLE.DETALLE_SOLICITUD_ID
                      AND SOLICITUD.ID_DETALLE_SOLICITUD   = :solicitud
                      AND DETALLE.TAREA_ID                 = TAREA.ID_TAREA
                      AND TAREA.DESCRIPCION_TAREA         LIKE :tarea
                      AND SOLICITUD.ESTADO                 = :estado
                      AND TIPO.DESCRIPCION_SOLICITUD       = :tipo ";
        
        $objResultSetMap->addRootEntityFromClassMetadata('\telconet\schemaBundle\Entity\InfoDetalle', 'objDetalle');
        
        $objNativeQuery->setParameter("solicitud", $arrayParametros['intIdSolicitud']);            
        $objNativeQuery->setParameter("estado",    $arrayParametros['strEstado']);
        $objNativeQuery->setParameter("tarea",     '%'.$arrayParametros['strTarea'].'%');
        $objNativeQuery->setParameter("tipo",      $arrayParametros['strTipo']);
        
        $objNativeQuery->setSQL($strSql);
        
        return $objNativeQuery->getOneOrNullResult();
    }

    /**
     * Método encargado de obtener las partes afectadas de las tareas internas.
     *
     * Costo 7
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 16-07-2018
     *
     * @author Jose Daniel Giler <jdgiler@telconet.ec>
     * @version 1.1 11-05-2022 - Se modifica query para tareas de instalaciones en nodos
     * 
     * @param  $arrayParametros [intDetalleId : Id del detalle]
     * @return $arrayRespuesta
     */
    public function getPartesAfectadasTareas($arrayParametros)
    {
        try
        {
            $objResultSetMap = new ResultSetMappingBuilder($this->_em);
            $objNativeQuery  = $this->_em->createNativeQuery(null, $objResultSetMap);
            $strWhere        = '';
            $strWhere2       = '';

            if(isset($arrayParametros['intDetalleId']) && !empty($arrayParametros['intDetalleId']))
            {
                $strWhere .= 'AND IDE.ID_DETALLE = :intDetalleId ';
                $strWhere2 .= 'and a.DETALLE_ID = :intDetalleId ';
                $objNativeQuery->setParameter("intDetalleId", $arrayParametros['intDetalleId']);
            }

            $strSql = "SELECT distinct "
                       . "case "
                       . "when IPA.TIPO_AFECTADO = 'Elemento' and z.tipo_solicitud_id = 8 and e.nombre_tipo_elemento = 'NODO' then null "
                       . "else IPA.AFECTADO_ID "
                       . "end ID_PUNTO, "
                       . "IDTE.ELEMENTO_ID ID_ELEMENTO "
                     . "FROM "
                        . "DB_SOPORTE.INFO_DETALLE                IDE, "
                        . "DB_SOPORTE.INFO_PARTE_AFECTADA         IPA, "
                        . "DB_SOPORTE.INFO_DETALLE_TAREA_ELEMENTO IDTE "
                        . ", db_infraestructura.info_elemento c "
                        . ", DB_INFRAESTRUCTURA.admi_modelo_elemento d " 
                        . ", DB_INFRAESTRUCTURA.admi_tipo_elemento e "
                        . ", ( "
                            . "select a.detalle_id, a.valor, b.tipo_solicitud_id  "
                            . "from DB_SOPORTE.INFO_TAREA_CARACTERISTICA a "
                            . "inner join DB_SOPORTE.INFO_DETALLE_SOLICITUD b on a.valor = b.id_detalle_solicitud "
                            . "and a.caracteristica_id= 1747 "
                            . $strWhere2
                            . ") z "
                    . "WHERE IDE.ID_DETALLE = IPA.DETALLE_ID (+) "
                      . "AND IDE.ID_DETALLE = IDTE.DETALLE_ID (+) "   
                      . "and ipa.afectado_id  = c.id_elemento (+) "
                      . "and c.modelo_elemento_id = d.id_modelo_elemento (+) "
                      . "and d.tipo_elemento_id = e.id_tipo_elemento (+) "
                      . "and IDE.ID_DETALLE = z.detalle_id (+) "
                      . $strWhere;

            $objResultSetMap->addScalarResult('ID_PUNTO'   , 'idPunto'    , 'integer');
            $objResultSetMap->addScalarResult('ID_ELEMENTO', 'idElemento' , 'integer');

            $objNativeQuery->setSQL($strSql);

            $arrayRespuesta['status'] = 'ok';
            $arrayRespuesta['result'] = $objNativeQuery->getResult();
        }
        catch (\Exception $objException)
        {
            error_log("Error InfoDetalleRepository.getPartesAfectadasTareas -> Error: ".$objException->getMessage());
            $arrayRespuesta = array();
            $arrayRespuesta["status"]  = 'fail';
            $arrayRespuesta["mensaje"] = $objException->getMessage();
        }
        return $arrayRespuesta;
    }
	
	/**
     * Función que obtiene los resumen de tareas por persona
     * 
     * @author: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 05-12-2018 
     *	 
	 * Costo=1234
     * 
     * @param array $arrayParametros
     * @return string $strResultado
     */   
	public function getResumenTareasPersona($arrayRequest)
    {        
        $objRsmb  = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsmb);
		
		$objReturnResponse 			    = [];
		$objReturnResponse['registros'] = [];
		$objReturnResponse['total'] 	= 0;

        $objRsmb->addScalarResult('NOMBRE_TAREA', 'strNombreTarea','string');
		$objRsmb->addScalarResult('EMPRESA_COD', 'strEmpresaCod','string');
        $objRsmb->addScalarResult('CANTIDAD_NO_FINALIZADAS', 'intCantidadNoFinalizadas','integer');
		$objRsmb->addScalarResult('CANTIDAD_FINALIZADAS', 'intCantidadFinalizadas','integer');
        $objRsmb->addScalarResult('ID_TAREA', 'intIdTarea','integer');
		$objRsmb->addScalarResult('ID_CASO', 'intIdCaso','integer');
			
        $strSQL = "SELECT "
                        . "    T.NOMBRE_TAREA, "
						. "    ER.EMPRESA_COD, "
						. "    SUM(CASE WHEN IDH.ESTADO <> 'Finalizada' THEN 1 ELSE 0 END) CANTIDAD_NO_FINALIZADAS, "
						. "    SUM(CASE WHEN IDH.ESTADO = 'Finalizada' THEN 1 ELSE 0 END) CANTIDAD_FINALIZADAS, "
						. "    T.ID_TAREA, "
						. "    IC.ID_CASO "
                        . "   FROM "
                        . "     DB_SOPORTE.INFO_DETALLE_ASIGNACION IDA "
                        . "   JOIN DB_SOPORTE.INFO_DETALLE IDET ON IDET.ID_DETALLE = IDA.DETALLE_ID "
						. "   JOIN DB_SOPORTE.INFO_DETALLE_HISTORIAL IDH ON IDH.DETALLE_ID = IDET.ID_DETALLE "
    					. "   JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL PER ON PER.ID_PERSONA_ROL = IDA.PERSONA_EMPRESA_ROL_ID "
    					. "   JOIN DB_SOPORTE.ADMI_TAREA T ON T.ID_TAREA = IDET.TAREA_ID "
    					. "   JOIN DB_COMERCIAL.INFO_EMPRESA_ROL ER ON ER.ID_EMPRESA_ROL = PER.EMPRESA_ROL_ID "
    					. "   LEFT JOIN DB_SOPORTE.INFO_DETALLE_HIPOTESIS IDHIP ON IDHIP.ID_DETALLE_HIPOTESIS = IDET.DETALLE_HIPOTESIS_ID "
    					. "   LEFT JOIN DB_SOPORTE.INFO_CASO IC ON IC.ID_CASO = IDHIP.CASO_ID "
						. "   INNER JOIN (
								  SELECT MAX(IDH2.ID_DETALLE_HISTORIAL) AS DETALLE_ID_HISTORIAL,IDH2.DETALLE_ID
								  FROM db_soporte.INFO_DETALLE_HISTORIAL IDH2
								  GROUP BY IDH2.DETALLE_ID
								)T2  ON IDH.ID_DETALLE_HISTORIAL=T2.DETALLE_ID_HISTORIAL AND T2.DETALLE_ID=IDA.DETALLE_ID"
						. "   INNER JOIN 
								(SELECT MAX(IDA2.ID_DETALLE_ASIGNACION) AS DETALLE_ASIGNACION_ID,IDA2.DETALLE_ID AS DETALLE_ID
								FROM db_soporte.INFO_DETALLE_ASIGNACION IDA2
								GROUP BY IDA2.DETALLE_ID
								)T3  ON IDA.ID_DETALLE_ASIGNACION=T3.DETALLE_ASIGNACION_ID AND T3.DETALLE_ID=IDA.DETALLE_ID "
						
                        . " WHERE IDH.ESTADO NOT IN (:arrEstados) ";

		$objQuery->setParameter('arrEstados', ['Cancelada','Rechazada','Anulada']);
		
		if(isset($arrayRequest['intPersonaId']) && !empty($arrayRequest['intPersonaId']))
		{
			$strSQL .= ' AND PER.PERSONA_ID = :intPersonaId ';
			$objQuery->setParameter('intPersonaId', $arrayRequest['intPersonaId']);
		}
		
		if(isset($arrayRequest['strFechaInicio']) && !empty($arrayRequest['strFechaInicio']) && 
		   isset($arrayRequest['strFechaFin']) && !empty($arrayRequest['strFechaFin']))
		{
			$strSQL .= ' AND TRUNC(IDET.FE_SOLICITADA)  BETWEEN TO_DATE(:strFechaInicio, :strFormatoFecha) AND TO_DATE(:strFechaFin, :strFormatoFecha) ';
			$objQuery->setParameter('strFormatoFecha', 'yyyy-mm-dd');
			$objQuery->setParameter('strFechaInicio', $arrayRequest['strFechaInicio']);
			$objQuery->setParameter('strFechaFin', $arrayRequest['strFechaFin']);
		}
		
		$strSQL .= ' GROUP BY T.NOMBRE_TAREA, T.ID_TAREA, IDA.PERSONA_EMPRESA_ROL_ID, ER.EMPRESA_COD, IC.ID_CASO ';
		
		$objQuery->setSQL($strSQL);
		$intTotal   = count($objQuery->getResult());
		if(isset($arrayRequest['intStart']) && isset($arrayRequest['intLimit']) && $arrayRequest['intLimit'] > 0)
		{
			$objQuery->setParameter('intStart', intval($arrayRequest['intStart']));
			$objQuery->setParameter('intLimit', intval($arrayRequest['intLimit']));
			$strSQL .= ' LIMIT :intStart, :intLimit ';
		}
		$objQuery->setSQL($strSQL);
		$objReturnResponse['registros'] = $objQuery->getResult();
		$objReturnResponse['total'] 	= $intTotal;

        return $objReturnResponse;
    }
	
	/**
     * Función que obtiene el resumen de tipos tareas por tiempo
     * 
     * @author: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 12-12-2018  
	 *
	 * Cambio en el Ordenamiento por fechas de manera ascedente
	 * @author: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.1 15-01-2018  
	 *
     * Costo=1234
	 *
     * @param array $arrayParametros
     * @return string $strResultado
     */   
	public function getResumenTipoTareasTiempo($arrayRequest)
    {        
        $objRsmb  = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsmb);
		
		$objReturnResponse 			    = [];
		$objReturnResponse['registros'] = [];
		$objReturnResponse['total'] 	= 0;

        $objRsmb->addScalarResult('FECHA', 'strFecha','string');
		$objRsmb->addScalarResult('TIPO_TAREA', 'strTipoTarea','string');
        $objRsmb->addScalarResult('CANTIDAD_NO_FINALIZADAS', 'intCantidadNoFinalizadas','integer');
		$objRsmb->addScalarResult('CANTIDAD_FINALIZADAS', 'intCantidadFinalizadas','integer');
        $objRsmb->addScalarResult('TOTAL', 'intTotal','integer');
			
        $strSQL = "SELECT ";
		if(isset($arrayRequest['strPeriodo']) && !empty($arrayRequest['strPeriodo']) && $arrayRequest['strPeriodo'] == 'ANUAL')
		{
			$strSQL     .=" TO_CHAR(T1.FE_SOLICITADA, 'MM-YYYY') AS FECHA, ";
		}
		else if(isset($arrayRequest['strPeriodo']) && !empty($arrayRequest['strPeriodo']) && $arrayRequest['strPeriodo'] == 'SEMANAL')
		{
			$strSQL     .=" (TO_CHAR(TRUNC(T1.FE_SOLICITADA, 'IW'), 'DD-MM-YYYY') || ' a ' || TO_CHAR(TRUNC(T1.FE_SOLICITADA, 'IW')+7-1/86400, 'DD-MM-YYYY')) AS FECHA, ";
			$strSQL     .=" TO_DATE(TO_CHAR(TRUNC(T1.FE_SOLICITADA, 'IW'), 'DD-MM-YYYY'), 'DD-MM-YYYY')  AS FECHA_INI, ";
			
		}
		else
		{
			$strSQL     .=" TO_CHAR(T1.FE_SOLICITADA, 'DD-MM-YYYY') AS FECHA, ";
			$strSQL     .=" TO_DATE(TO_CHAR(T1.FE_SOLICITADA, 'DD-MM-YYYY'), 'DD-MM-YYYY')  AS FECHA_INI, ";
		}
		$strSQL  		.="    T1.TIPO_TAREA,  "
						. "    SUM(CASE WHEN T1.ESTADO <> 'Finalizada' THEN 1 ELSE 0 END) CANTIDAD_NO_FINALIZADAS, "
						. "    SUM(CASE WHEN T1.ESTADO = 'Finalizada' THEN 1 ELSE 0 END) CANTIDAD_FINALIZADAS, "
						. "    COUNT(T1.ESTADO) AS TOTAL "
						. "    FROM  "
						. "    (SELECT "
                        . "    T.NOMBRE_TAREA, "
						. "    ER.EMPRESA_COD, "
						. "    T.ID_TAREA, "
						. "    IDH.ESTADO, "
						. "    IDET.FE_SOLICITADA, "
						. "    (CASE  "
						. "    WHEN INSTR(T.NOMBRE_TAREA, 'INSTALACION') > 0 THEN 'INSTALACION' "
						. "    WHEN INSTR(T.NOMBRE_TAREA, 'RETIRAR EQUIPO') > 0 THEN 'RETIRO DE EQUIPO'  "
						. "    WHEN IC.ID_CASO IS NOT NULL THEN 'SOPORTE' "
						. "    ELSE 'INTERDEPARTAMENTAL' END) AS TIPO_TAREA	 "
                        . "   FROM "
                        . "     DB_SOPORTE.INFO_DETALLE_ASIGNACION IDA "
                        . "   JOIN DB_SOPORTE.INFO_DETALLE IDET ON IDET.ID_DETALLE = IDA.DETALLE_ID "
						. "   JOIN DB_SOPORTE.INFO_DETALLE_HISTORIAL IDH ON IDH.DETALLE_ID = IDET.ID_DETALLE "
    					. "   JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL PER ON PER.ID_PERSONA_ROL = IDA.PERSONA_EMPRESA_ROL_ID "
    					. "   JOIN DB_SOPORTE.ADMI_TAREA T ON T.ID_TAREA = IDET.TAREA_ID "
    					. "   JOIN DB_COMERCIAL.INFO_EMPRESA_ROL ER ON ER.ID_EMPRESA_ROL = PER.EMPRESA_ROL_ID "
    					. "   LEFT JOIN DB_SOPORTE.INFO_DETALLE_HIPOTESIS IDHIP ON IDHIP.ID_DETALLE_HIPOTESIS = IDET.DETALLE_HIPOTESIS_ID "
    					. "   LEFT JOIN DB_SOPORTE.INFO_CASO IC ON IC.ID_CASO = IDHIP.CASO_ID "
						. "   INNER JOIN (
								  SELECT MAX(IDH2.ID_DETALLE_HISTORIAL) AS DETALLE_ID_HISTORIAL,IDH2.DETALLE_ID
								  FROM db_soporte.INFO_DETALLE_HISTORIAL IDH2
								  GROUP BY IDH2.DETALLE_ID
								)T2  ON IDH.ID_DETALLE_HISTORIAL=T2.DETALLE_ID_HISTORIAL AND T2.DETALLE_ID=IDA.DETALLE_ID"
						. "   INNER JOIN 
								(SELECT MAX(IDA2.ID_DETALLE_ASIGNACION) AS DETALLE_ASIGNACION_ID,IDA2.DETALLE_ID AS DETALLE_ID
								FROM db_soporte.INFO_DETALLE_ASIGNACION IDA2
								GROUP BY IDA2.DETALLE_ID
								)T3  ON IDA.ID_DETALLE_ASIGNACION=T3.DETALLE_ASIGNACION_ID AND T3.DETALLE_ID=IDA.DETALLE_ID "
						
                        . " WHERE IDH.ESTADO NOT IN (:arrEstados) ";

		$objQuery->setParameter('arrEstados', ['Cancelada','Rechazada','Anulada']);
		
		if(isset($arrayRequest['intPersonaId']) && !empty($arrayRequest['intPersonaId']))
		{
			$strSQL .= ' AND PER.PERSONA_ID = :intPersonaId ';
			$objQuery->setParameter('intPersonaId', $arrayRequest['intPersonaId']);
		}
		
		if(isset($arrayRequest['strFechaInicio']) && !empty($arrayRequest['strFechaInicio']) && 
		   isset($arrayRequest['strFechaFin']) && !empty($arrayRequest['strFechaFin']))
		{
			$strSQL .= " AND TRUNC(IDET.FE_SOLICITADA)  BETWEEN TO_DATE(:strFechaInicio, :strFormatoFecha) AND TO_DATE(:strFechaFin, :strFormatoFecha) ";
			$objQuery->setParameter('strFormatoFecha', 'yyyy-mm-dd');
			$objQuery->setParameter('strFechaInicio', $arrayRequest['strFechaInicio']);
			$objQuery->setParameter('strFechaFin', $arrayRequest['strFechaFin']);
		}
		
		if(isset($arrayRequest['strPeriodo']) && !empty($arrayRequest['strPeriodo']) && 
		   ($arrayRequest['strPeriodo'] == 'DIARIO' || $arrayRequest['strPeriodo'] == 'SEMANAL')&&
		   isset($arrayRequest['intMes']) && !empty($arrayRequest['intMes']) && 
		   isset($arrayRequest['intAnio']) && !empty($arrayRequest['intAnio']))
		{
			$strSQL .= " AND TO_CHAR(IDET.FE_SOLICITADA, 'YYYY-MM') = :intAnio||'-'||:intMes ";
			$objQuery->setParameter('intMes', $arrayRequest['intMes']);
			$objQuery->setParameter('intAnio', $arrayRequest['intAnio']);
		}
		
		if(isset($arrayRequest['strPeriodo']) && !empty($arrayRequest['strPeriodo']) && $arrayRequest['strPeriodo'] == 'ANUAL' &&
		   isset($arrayRequest['intAnio']) && !empty($arrayRequest['intAnio']))
		{
			$strSQL .= " AND TO_CHAR(IDET.FE_SOLICITADA, 'YYYY') = :intAnio ";
			$objQuery->setParameter('intAnio', $arrayRequest['intAnio']);
		}
				
		$strSQL .= " ) T1 ";
		if(isset($arrayRequest['strPeriodo']) && !empty($arrayRequest['strPeriodo']) && $arrayRequest['strPeriodo'] == 'ANUAL')
		{
			$strSQL .= " GROUP BY TO_CHAR(T1.FE_SOLICITADA, 'MM-YYYY'), T1.TIPO_TAREA ";
			$strSQL .= " ORDER BY TO_CHAR(T1.FE_SOLICITADA, 'MM-YYYY') ASC, T1.TIPO_TAREA ";
		}
		else if(isset($arrayRequest['strPeriodo']) && !empty($arrayRequest['strPeriodo']) && $arrayRequest['strPeriodo'] == 'SEMANAL')
		{
			$strSQL .= " GROUP BY (TO_CHAR(TRUNC(T1.FE_SOLICITADA, 'IW'), 'DD-MM-YYYY') || ' a ' || TO_CHAR(TRUNC(T1.FE_SOLICITADA, 'IW')+7-1/86400, 'DD-MM-YYYY')), TO_CHAR(TRUNC(T1.FE_SOLICITADA, 'IW'), 'DD-MM-YYYY'),T1.TIPO_TAREA ";
			$strSQL .= " ORDER BY TO_DATE(TO_CHAR(TRUNC(T1.FE_SOLICITADA, 'IW'), 'DD-MM-YYYY'), 'DD-MM-YYYY') ASC";
		}
		else
		{
			$strSQL .= " GROUP BY TO_CHAR(T1.FE_SOLICITADA, 'DD-MM-YYYY'), T1.TIPO_TAREA ";
			$strSQL .= " ORDER BY TO_DATE(TO_CHAR(T1.FE_SOLICITADA, 'DD-MM-YYYY'), 'DD-MM-YYYY') ASC ";
		}		
		
		$objQuery->setSQL($strSQL);
		$intTotal   = count($objQuery->getResult());
		if(isset($arrayRequest['intStart']) && isset($arrayRequest['intLimit']) && $arrayRequest['intLimit'] > 0)
		{
			$objQuery->setParameter('intStart', intval($arrayRequest['intStart']));
			$objQuery->setParameter('intLimit', intval($arrayRequest['intLimit']));
			$strSQL .= ' LIMIT :intStart, :intLimit ';
		}
		$objQuery->setSQL($strSQL);
		$objReturnResponse['registros'] = $objQuery->getResult();
		$objReturnResponse['total'] 	= $intTotal;

        return $objReturnResponse;
    }

    /**
     * Método encargado de realizar el reporte de tareas.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 - 30-05-2019
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.1 04-07-2019 - Se modifica los parámetros a enviar al procedimiento 'P_REPORTE_TAREAS' por motivos
     *                           que ahora retornará un sysrefcursor de las tareas solicitadas por el usuario.
     * 
     * @author Néstor Naula <nnaulal@telconet.ec>
     * @version 1.2 06-02-2020 - Se agrega la codificación para que los datos se mantengan 
     *                           con sus caracteres especiales.
     * @since 1.1
     *
     * @param  Array $arrayParametros => Se convierte en Json y se envía al procedimiento.
     * @return Array $arrayRespuesta
     */
    public function reporteTareas($arrayParametros)
    {
        $intTotal   = 0;
        $strStatus  = "";
        $strMessage = "";

        try
        {
            $strSql = "BEGIN DB_SOPORTE.SPKG_REPORTES.P_REPORTE_TAREAS(:Pcl_Json,".
                                                                      ":Prf_Tareas,".
                                                                      ":Pn_Total,".
                                                                      ":Pv_Status,".
                                                                      ":Pv_Message); END;";

            $arrayOciCon  = $arrayParametros['ociCon'];
            $objRscCon    = oci_connect($arrayOciCon['userSoporte'], $arrayOciCon['passSoporte'], $arrayOciCon['databaseDsn'],'AL32UTF8');
            $objCsrResult = oci_new_cursor($objRscCon);
            $objStmt      = oci_parse($objRscCon,$strSql);
            $arrayParametros['ociCon'] = null;

            oci_bind_by_name($objStmt,':Pcl_Json'   ,json_encode($arrayParametros));
            oci_bind_by_name($objStmt,':Prf_Tareas' ,$objCsrResult,-1,OCI_B_CURSOR);
            oci_bind_by_name($objStmt,':Pn_Total'   ,$intTotal,10);
            oci_bind_by_name($objStmt,':Pv_Status'  ,$strStatus,50);
            oci_bind_by_name($objStmt,':Pv_Message' ,$strMessage,4000);

            oci_execute($objStmt);
            oci_execute($objCsrResult);

            $arrayRespuesta = array ('status'       => $strStatus,
                                     'message'      => $strMessage,
                                     'total'        => $intTotal,
                                     'objCsrResult' => $objCsrResult);
        }
        catch (\Exception $objException)
        {
            $arrayRespuesta = array ('status'  => 'fail',
                                     'message' => $objException->getMessage());
        }
        return $arrayRespuesta;
    }

    /**
     * Método que crea el Job con auto drop para la generación del reporte de tareas.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 04-06-2019
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.1 04-07-2019 - Se modifica los parámetros a enviar al procedimiento 'P_REPORTE_TAREAS' por motivos
     *                           que ahora retornará las tareas solicitadas por el usuario.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.2 23-04-2020 - Se realiza el control del tamaño en el nombre del job para que no supere un
     *                           tamaño de 28 carácteres.
     * 
     * @author Andrés Montero H. <amontero@telconet.ec>
     * @version 1.3 08-07-2020 - Se realiza validación para obtener datos desde paquete procedimiento
     *                           DB_SOPORTE.SPKG_REPORTES.P_REPORTE_TAREAS por defecto o 
     *                           DB_SOPORTE.SPKG_INFO_TAREA.P_REPORTE_TAREAS si el parámetro tablaConsulta es 'InfoTarea' 
     *
     * @param  Array $arrayParametros => Se convierte en Json y se envía al procedimiento.
     * @return Array $arrayRespuesta
     */
    public function jobReporteTareas($arrayParametros)
    {
        $strUsuarioSolicita = $arrayParametros['strUsuarioSolicita'];

        try
        {
            if ($strUsuarioSolicita === '' || $strUsuarioSolicita === null)
            {
                throw new \Exception('No se puedo obtener el usuario en sesión');
            }

            $strUsuario = strtoupper($strUsuarioSolicita);
            $strJson    = json_encode($arrayParametros);
            $strBloqueReporte ="DECLARE
                                  Lrf_Tareas  SYS_REFCURSOR;
                                  Ln_Total    NUMBER;
                                  Lv_Status   VARCHAR2(50);
                                  Lv_Message  VARCHAR2(3000);
                                BEGIN
                                  DB_SOPORTE.SPKG_REPORTES.P_REPORTE_TAREAS(
                                  Pcl_Json   => ''$strJson'',
                                  Prf_Tareas => Lrf_Tareas,
                                  Pn_Total   => Ln_Total,
                                  Pv_Status  => Lv_Status,
                                  Pv_Message => Lv_Message);
                                END;";
            if($arrayParametros['tablaConsulta'] == 'InfoTarea')
            {
                $strBloqueReporte ="DECLARE
                                        LCL_JSONRESPUESTA CLOB;
                                        Ln_Total    NUMBER;
                                        Lv_Status   VARCHAR2(50);
                                        Lv_Message  VARCHAR2(3000);
                                    BEGIN
                                        DB_SOPORTE.SPKG_INFO_TAREA.P_REPORTE_TAREAS(
                                        Pcl_Json   => ''$strJson'',
                                        Pcl_JsonRespuesta => LCL_JSONRESPUESTA,
                                        Pn_Total   => Ln_Total,
                                        Pv_Status  => Lv_Status,
                                        Pv_Message => Lv_Message);
                                    END;";
            }

            $strSqlJ = "DECLARE
                            Lv_usuario VARCHAR(500) := 'JOB_REPORTE_TAREAS_';
                        BEGIN
                            Lv_usuario := Lv_usuario ||'$strUsuario';
                            Lv_usuario := SUBSTR(Lv_usuario,0,28);
                            DBMS_SCHEDULER.CREATE_JOB(job_name   => '\"DB_SOPORTE\".\"'||Lv_usuario||'\"',
                                                      job_type   => 'PLSQL_BLOCK',
                                                      job_action => '
                                                        ".$strBloqueReporte."',
                                                      number_of_arguments => 0,
                                                      start_date          => NULL,
                                                      repeat_interval     => NULL,
                                                      end_date            => NULL,
                                                      enabled             => FALSE,
                                                      auto_drop           => TRUE,
                                                      comments            => 'Proceso para ejecutar el reporte de tareas.');

                            DBMS_SCHEDULER.SET_ATTRIBUTE(name      => '\"DB_SOPORTE\".\"'||Lv_usuario||'\"',
                                                         attribute => 'logging_level',
                                                         value     => DBMS_SCHEDULER.LOGGING_OFF);

                            DBMS_SCHEDULER.enable(name => '\"DB_SOPORTE\".\"'||Lv_usuario||'\"');

                        END;";

            $objStmt = $this->_em->getConnection()->prepare($strSqlJ);
            $objStmt->execute();

            $arrayRespuesta = array ('status'  => 'ok',
                                     'message' => 'Proceso ejecutándose');
        }
        catch (\Exception $objException)
        {
            $arrayRespuesta = array ('status'  => 'fail',
                                     'message' => $objException->getMessage());
        }
        return $arrayRespuesta;
    }

    /**
     * Proceso que detecta si ya existe un Job creado.
     *
     * Costo 7
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 04-05-2019
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.2 23-04-2020 - Se realiza el control del tamaño en el nombre del job para que no supere un
     *                           tamaño de 28 carácteres.
     *
     * @param  Array $arrayParametros [strNombreJob : Nombre del Job]
     * @return Array $arrayRespuesta
     */
    public function existeJobReporteTarea($arrayParametros)
    {
        try
        {
            $objResultSetMap = new ResultSetMappingBuilder($this->_em);
            $objNativeQuery  = $this->_em->createNativeQuery(null, $objResultSetMap);
            $strSql = "SELECT COUNT(*) AS CANTIDAD "
                        . "FROM SYS.USER_SCHEDULER_JOBS "
                    . "WHERE UPPER(JOB_NAME) = UPPER(:strNombreJob)";
            $objNativeQuery->setParameter('strNombreJob',$arrayParametros['strNombreJob']);
            $objResultSetMap->addScalarResult('CANTIDAD','cantidad','integer');
            $objNativeQuery->setSQL($strSql);
            $arrayResult    = $objNativeQuery->getOneOrNullResult();
            $arrayRespuesta = array('status' => 'ok' , 'cantidad' => $arrayResult['cantidad']);
        }
        catch (\Exception $objException)
        {
            $arrayRespuesta = array('status' => 'fail' , 'message' => $objException->getMessage());
        }
        return $arrayRespuesta;
    }

    /**
     * Proceso para obtener las tareas solicitadas por el usuario.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 02-07-2019
     * 
     * @author Andrés Montero H. <amontero@telconet.ec>
     * @version 1.1 08-07-2020 - Se realiza validación para obtener datos desde la función
     *                           reporteTareas() por defecto o reporteInfoTarea() si el parámetro tablaConsulta es 'InfoTarea' 
     *
     * @param  Array $arrayParametros => Se convierte en Json y se envía al procedimiento.
     * @return Array $arrayRespuesta
     */
    public function getTareasSoporte($arrayParametros)
    {
        $objServiceUtil = $arrayParametros['serviceUtil'];
        $strUser        = $arrayParametros["strUser"];
        $strIp          = $arrayParametros["strIp"];
        $arrayParametros['serviceUtil'] = null;

        try
        {
            if($arrayParametros["idCuadrilla"] && $arrayParametros["idDepartamento"] == null
                    && $arrayParametros["idCuadrilla"] != 'Todos')
            {
                $strNombreCuadrilla = null;
                $arrayParametros["idCuadrilla"]    = implode(",",$arrayParametros["idCuadrilla"]);
                $arrayParametros['nombreAsignado'] = array_map('strtoupper',$arrayParametros['nombreAsignado']);

                foreach ($arrayParametros['nombreAsignado'] as $strValue)
                {
                    if ($strNombreCuadrilla == '' || is_null($strNombreCuadrilla))
                    {
                        $strNombreCuadrilla = "'".$strValue."'";
                    }
                    else
                    {
                        $strNombreCuadrilla = $strNombreCuadrilla .",'".$strValue."'";
                    }
                }

                $arrayParametros['nombreAsignado'] = $strNombreCuadrilla;
            }

            if ($arrayParametros["estado"] !== 'Todos' && $arrayParametros["estado"] !== ''
                    && !empty($arrayParametros["estado"]))
            {
                $strEstados = null;
                $arrayParametros["estado"] = array_map('strtoupper', $arrayParametros["estado"]);

                foreach ($arrayParametros["estado"] as $strValue)
                {
                    if ($strEstados == '' || is_null($strEstados))
                    {
                        $strEstados = "'".$strValue."'";
                    }
                    else
                    {
                        $strEstados = $strEstados .",'".$strValue."'";
                    }
                }

                $arrayParametros["estado"] = $strEstados;
            }
            else
            {
                $arrayParametros["estado"] = null;
            }

            if (isset($arrayParametros["arrayDepartamentos"]) && !empty($arrayParametros["arrayDepartamentos"]))
            {
                $strIdDepartamentos = null;

                foreach ($arrayParametros["arrayDepartamentos"] as $intIdDepartamento)
                {
                    if ($intIdDepartamento !== null && $intIdDepartamento !== '')
                    {
                        if ($strIdDepartamentos == null)
                        {
                            $strIdDepartamentos = $intIdDepartamento;
                        }
                        else
                        {
                            $strIdDepartamentos .= ','.$intIdDepartamento;
                        }
                    }
                }

                $arrayParametros["arrayDepartamentosP"] = $strIdDepartamentos;
            }

            if (isset($arrayParametros["arrayPersonaEmpresaRol"]) && !empty($arrayParametros["arrayPersonaEmpresaRol"]))
            {
                $arrayParametros["arrayPersonaEmpresaRolP"] = implode(",",$arrayParametros["arrayPersonaEmpresaRol"]);
            }

            $booleanVerTareasTodasEmpresas         = $arrayParametros["booleanVerTareasTodasEmpresa"];
            $arrayParametros['strTodaLasEmpresa']  = $booleanVerTareasTodasEmpresas ? 'S' : 'N';
            $arrayParametros["esConsulta"]         = 'S'; //Variable para identificar que la petición es por consulta.
            $arrayParametros["strUsuarioSolicita"] = $arrayParametros["strUser"];
            $arrayParametros["emComercial"]        = null;
            $arrayParametros["emComunicacion"]     = null;
            $arrayParametros['serviceSoporte']     = null;
            $arrayParametros["strUser"]            = null;
            $arrayParametros["arrayPersonaEmpresaRol"]  = null;
            $arrayParametros["arrayDepartamentos"]      = null;
            $arrayParametros["caracteristicaSolicitud"] = null;
            $arrayParametros["strMsgReasignacionAutomaticaCambioDep"] = null;

            //Proceso encargado de obtener las tareas de la base de datos.
            if ($arrayParametros["tablaConsulta"] == "InfoTarea")
            {
                $arrayResultTareas = $this->reporteInfoTarea($arrayParametros);
            }
            else
            {
                $arrayResultTareas = $this->reporteTareas($arrayParametros);
            }

            if (strtoupper($arrayResultTareas['status']) !== 'OK')
            {
                $strMensageError = $arrayResultTareas['message'] ? $arrayResultTareas['message'] : 'Error al obtener las tareas';
                throw new \Exception($strMensageError);
            }

            $arrayRespuesta = array ('total'        => $arrayResultTareas['total'],
                                     'objCsrResult' => $arrayResultTareas['objCsrResult'],
                                     'objJsonRespuesta' => $arrayResultTareas['objJsonRespuesta']);
        }
        catch (\Exception $objException)
        {
            if (is_object($objServiceUtil))
            {
                $objServiceUtil->insertError('Telcos+',
                                             'InfoDetalleRepository->getTareasSoporte',
                                              $objException->getMessage(),
                                              $strUser,
                                              $strIp);
            }

            $arrayRespuesta = array ('total' => 0, 'objCsrResult' => null);
        }
        return $arrayRespuesta;
    }
    
   /**
     * getInfoTareaByDetalle
     *
     * Costo: 13
     * 
     * Función que retorna información de la tarea según EL ID_DETALLE ingresado.
     *
     * @author Ronny Morán <rmoranc@telconet.ec>
     * @version 1.0 17-09-2019
     *
     * 
     * @author Ronny Morán <rmoranc@telconet.ec>
     * @version 1.1 20-04-2020
     * Se agrega validación para tarea interdepartamental.
     * Costo: 9
     *
     *   
     * @param integer  $arrayParametros
     *
     * @return array $arrayResult
     *
     */             
    public function getInfoTareaByDetalle($arrayParametros)
    {
        $intIdCaso          = $arrayParametros['intIdCaso'];
        $strEmpresaId       = '';
        $strEmpresaIdMD     = '18';
        $strEmpresaIdTN     = '10';
        $objServiceUtil     = $arrayParametros['serviceUtil'];
        $strUser            = $arrayParametros["strUser"];
        $strIp              = $arrayParametros["strIp"];
        $boolEsInterdep     = $arrayParametros["esInterdep"];
        
        if($arrayParametros['codEmpresa'] === 'TN')
        {
            $strEmpresaId = $strEmpresaIdTN;
        }
        else
        {
            $strEmpresaId = $strEmpresaIdMD;
        }

        try
        {
            $objResultSetMap = new ResultSetMappingBuilder($this->_em);
            $objNativeQuery  = $this->_em->createNativeQuery(null, $objResultSetMap);

            if($intIdCaso !== 0)
            {
                $strSql = "SELECT
                                INFPER.PERSONA_ID, 
                                INFS.ID_SERVICIO, 
                                (SELECT INFCM.ID_COMUNICACION
                                  FROM DB_COMUNICACION.INFO_COMUNICACION INFCM 
                                  WHERE INFCM.DETALLE_ID    = :idDetalle 
                                    AND INFCM.EMPRESA_COD   = :empresaId
                                    AND ROWNUM < 2) AS ID_COMUNICACION
                            FROM
                                DB_SOPORTE.INFO_DETALLE_HIPOTESIS INFDH,
                                DB_SOPORTE.INFO_CASO INFCA,
                                DB_SOPORTE.INFO_PARTE_AFECTADA INFPA,
                                DB_SOPORTE.INFO_PUNTO INFPT,
                                DB_SOPORTE.INFO_DETALLE INFDT,
                                DB_SOPORTE.INFO_SERVICIO INFS,
                                DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL INFPER
                            WHERE 
                                INFCA.ID_CASO = INFDH.CASO_ID
                                AND INFDH.ID_DETALLE_HIPOTESIS = INFDT.DETALLE_HIPOTESIS_ID
                                AND INFDT.ID_DETALLE = INFPA.DETALLE_ID
                                AND INFPA.TIPO_AFECTADO = :tipoAfectado
                                AND INFPA.AFECTADO_ID = INFPT.ID_PUNTO
                                AND INFPER.ID_PERSONA_ROL = INFPT.PERSONA_EMPRESA_ROL_ID
                                AND INFPT.ID_PUNTO = INFS.PUNTO_ID
                                AND INFCA.ID_CASO = :idCaso ";
                
            $objResultSetMap->addScalarResult('PERSONA_ID','personaId','string');
            $objResultSetMap->addScalarResult('ID_SERVICIO','servicioId','string');
            $objResultSetMap->addScalarResult('ID_COMUNICACION','idComunicacion','string');
            $objNativeQuery->setSQL($strSql);

            }
            else if($boolEsInterdep)
            {
                $strSql = "SELECT 
                                IPER.ID_PERSONA 
                                FROM 
                                DB_SOPORTE.INFO_PARTE_AFECTADA IPA, 
                                DB_SOPORTE.INFO_PUNTO INFPT, 
                                DB_SOPORTE.INFO_PERSONA_EMPRESA_ROL IPEROL, 
                                DB_SOPORTE.INFO_PERSONA IPER
                                WHERE 
                                IPA.DETALLE_ID = :idDetalle
                                AND IPA.TIPO_AFECTADO= :tipoAfectado
                                AND IPA.AFECTADO_NOMBRE = INFPT.LOGIN
                                AND IPEROL.ID_PERSONA_ROL = INFPT.PERSONA_EMPRESA_ROL_ID
                                AND IPER.ID_PERSONA = IPEROL.PERSONA_ID";

                $objResultSetMap->addScalarResult('ID_PERSONA','personaId','string');
                $objNativeQuery->setSQL($strSql);

            }    
            else
            {
                $strSql = "SELECT
                                INFPE.PERSONA_ID, IDS.SERVICIO_ID, INFCM.ID_COMUNICACION
                           FROM
                                DB_SOPORTE.INFO_COMUNICACION INFCM,
                                DB_SOPORTE.INFO_PUNTO IFPT,
                                DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL INFPE,
                                DB_SOPORTE.INFO_DETALLE IFDT,
                                DB_COMERCIAL.INFO_DETALLE_SOLICITUD IDS
                           WHERE
                                IFPT.ID_PUNTO = INFCM.REMITENTE_ID
                                AND INFCM.DETALLE_ID = IFDT.ID_DETALLE
                                AND IFDT.ID_DETALLE = :idDetalle
                                AND INFPE.ID_PERSONA_ROL = IFPT.PERSONA_EMPRESA_ROL_ID
                                AND IDS.ID_DETALLE_SOLICITUD = IFDT.DETALLE_SOLICITUD_ID
                                AND INFCM.EMPRESA_COD = :empresaId ";
                
            $objResultSetMap->addScalarResult('PERSONA_ID','personaId','string');
            $objResultSetMap->addScalarResult('SERVICIO_ID','servicioId','string');
            $objResultSetMap->addScalarResult('ID_COMUNICACION','idComunicacion','string');
            $objNativeQuery->setSQL($strSql);

            }

            $objNativeQuery->setParameter('idDetalle', $arrayParametros['idDetalle']);
            $objNativeQuery->setParameter('empresaId', $strEmpresaId);
            $objNativeQuery->setParameter('tipoAfectado', 'Cliente');
            $objNativeQuery->setParameter('idCaso', $intIdCaso);

            
            $arrayRespuesta = $objNativeQuery->getResult();    
        }
        catch (\Exception $objException)
        {
            if (is_object($objServiceUtil))
            {
                $objServiceUtil->insertError('Telcos+',
                                             'InfoDetalleRepository->getInfoTareaByDetalle',
                                              $objException->getMessage(),
                                              $strUser,
                                              $strIp);
            }
        }
        return $arrayRespuesta[0];
    }

     /**
     * getIdServicioVRF
     *
     * Costo: 13
     * 
     * Función que retorna información de la tarea según EL ID_DETALLE ingresado.
     *
     * @author Wilmer Vera <wvera@telconet.ec>
     * @version 1.0 02-10-2020
     * Se agrega logica para obtener el idServicio.
     *
     * @param integer  $arrayParametros
     *
     * @return array $arrayResult
     *
     */             
    public function getIdServicioVRF($arrayParametros)
    {
        $intIdDetalle       = $arrayParametros['idDetalle'];
        $objServiceUtil     = $arrayParametros['serviceUtil'];
        $strUser            = $arrayParametros["strUser"];
        $strIp              = $arrayParametros["strIp"];
        
        try
        {
            $objResultSetMap = new ResultSetMappingBuilder($this->_em);
            $objNativeQuery  = $this->_em->createNativeQuery(null, $objResultSetMap);
          
            $strSql = " SELECT DISTINCT(AFECTADO_ID)
                        FROM DB_COMERCIAL.INFO_PARTE_AFECTADA 
                        WHERE DETALLE_ID in (
                        select ID_DETALLE 
                        from DB_SOPORTE.INFO_DETALLE 
                        where DETALLE_HIPOTESIS_ID = 
                        (select DETALLE_HIPOTESIS_ID 
                        from DB_SOPORTE.INFO_DETALLE 
                        where ID_DETALLE = :idDetalle))
                        AND TIPO_AFECTADO = 'Servicio' ";
            
            $objResultSetMap->addScalarResult('AFECTADO_ID','idServicio','string');
          
            $objNativeQuery->setSQL($strSql);


            $objNativeQuery->setParameter('idDetalle', $intIdDetalle );
            
            $arrayRespuesta = $objNativeQuery->getResult();    
        }
        catch (\Exception $objException)
        {
            if (is_object($objServiceUtil))
            {
                $objServiceUtil->insertError('Telcos+',
                                             'InfoDetalleRepository->getIdServicioVRF',
                                              $objException->getMessage(),
                                              $strUser,
                                              $strIp);
            }
        }
        return $arrayRespuesta[0];
    }


    /**
     * getTipoMedioTarea
     *
     * Costo: 4
     * 
     * Función que me devuelve el tipo de medio según el Id Servicio.
     *
     * @author Ronny Morán <rmoranc@telconet.ec>
     * @version 1.0 17-09-2019
     *
     * @param integer  $arrayParametros
     *
     * @return array $arrayResult
     *
     */     
    public function getTipoMedioTarea($arrayParametros)
    {
        $objResultSetMap    = new ResultSetMappingBuilder($this->_em);
        $objNativeQuery     = $this->_em->createNativeQuery(null, $objResultSetMap);
        $objServiceUtil     = $arrayParametros['serviceUtil'];
        $strUser            = $arrayParametros["strUser"];
        $strIp              = $arrayParametros["strIp"];
        
        try
        {
                $strSql = "SELECT ATM.ID_TIPO_MEDIO 
                            FROM 
                            DB_SOPORTE.INFO_SERVICIO_TECNICO IFST, 
                            DB_SOPORTE.ADMI_TIPO_MEDIO ATM
                            WHERE
                            IFST.ULTIMA_MILLA_ID = ID_TIPO_MEDIO
                            AND SERVICIO_ID = :idServicio  ";

                $objNativeQuery->setParameter('idServicio',$arrayParametros['intServicioId']);
            
                $objResultSetMap->addScalarResult('ID_TIPO_MEDIO','tipoMedioId','integer');
                $objNativeQuery->setSQL($strSql);
                
                $arrayRespuesta = $objNativeQuery->getResult();
        }
        catch (\Exception $objException)
        {
            if (is_object($objServiceUtil))
            {
                $objServiceUtil->insertError('Telcos+',
                                             'InfoDetalleRepository->getTipoMedioTarea',
                                              $objException->getMessage(),
                                              $strUser,
                                              $strIp);
            }
        }
        return $arrayRespuesta[0];
    }    
    
    /**
     * Método encargado de crear tarea en tabla DB_SOPORTE.INFO_TAREA.
     *
     * @author Andrés Montero H. <amontero@telconet.ec>
     * @version 1.0 - 08-07-2020
     * 
     * @since 1.0
     *
     * @param  Array $arrayParametros:
     *                                intDetalleId   => id detalle de la tarea
     *                                strUsrCreacion => usuario de creación
     * @return Array $arrayRespuesta
     */
    public function creaInfoTarea($arrayParametros)
    {
        $strStatus  = "";
        $strMessage = "";
        try
        {
            $strSql = "BEGIN DB_SOPORTE.SPKG_INFO_TAREA.P_CREA_INFO_TAREA(:Pn_IdDetalle,".
                                                                         ":Pv_UsrCreacion,".
                                                                         ":Pv_Status,".
                                                                         ":Pv_Message); END;";

            $intIdDetalle   = $arrayParametros['intDetalleId'];
            $strUsrCreacion = $arrayParametros['strUsrCreacion'];

            if ($intIdDetalle !== null && !empty($intIdDetalle))
            {
                $arrayOciCon  = $arrayParametros['objOciCon'];
                $objRscCon    = oci_connect($arrayOciCon['userSoporte'], $arrayOciCon['passSoporte'], $arrayOciCon['databaseDsn'],'AL32UTF8');
                $objCsrResult = oci_new_cursor($objRscCon);
                $objStmt      = oci_parse($objRscCon,$strSql);
                $arrayParametros['objOciCon'] = null;
                oci_bind_by_name($objStmt,':Pn_IdDetalle'   ,$intIdDetalle);
                oci_bind_by_name($objStmt,':Pv_UsrCreacion' ,$strUsrCreacion);
                oci_bind_by_name($objStmt,':Pv_Status'  ,$strStatus,50);
                oci_bind_by_name($objStmt,':Pv_Message' ,$strMessage,4000);

                oci_execute($objStmt);
                oci_execute($objCsrResult);

                $arrayRespuesta = array ('status'       => $strStatus,
                                        'message'      => $strMessage);
            }
            else
            {
                $arrayRespuesta = array ('status'       => $strStatus,
                                        'message'      => $strMessage);
                throw new \Exception('Faltan parámetros para enviar.');
            }
        }
        catch (\Exception $objException)
        {
            $arrayRespuesta = array ('status'  => 'fail',
                                     'message' => 'Error en infoDetalleRepository función creaInfoTarea : '.$objException->getMessage());
        }
        return $arrayRespuesta;
    }
    
    /**
     * Método encargado de actualizar datos de tarea en tabla DB_SOPORTE.INFO_TAREA.
     *
     * @author Andrés Montero H. <amontero@telconet.ec>
     * @version 1.0 - 08-07-2020
     * 
     * @since 1.0
     *
     * @param  Array $arrayParametros:
     *                                intDetalleId   => id detalle de la tarea
     *                                strUsrCreacion => usuario de creación
     * @return Array $arrayRespuesta
     */

    public function actualizaInfoTarea($arrayParametros)
    {
        $strStatus  = "";
        $strMessage = "";
        try
        {
            $strSql = "BEGIN DB_SOPORTE.SPKG_INFO_TAREA.P_UPDATE_TAREA(:Pn_IdDetalle,".
                                                                      ":Pv_UsrUltMod,".
                                                                      ":Pv_Status,".
                                                                      ":Pv_Message); END;";
            $intIdDetalle = $arrayParametros['intDetalleId'];
            $strUsrUltMod = $arrayParametros['strUsrUltMod'];

            if ($intIdDetalle !== null && !empty($intIdDetalle))
            {
                $arrayOciCon  = $arrayParametros['objOciCon'];
                $objRscCon    = oci_connect($arrayOciCon['userSoporte'], $arrayOciCon['passSoporte'], $arrayOciCon['databaseDsn'],'AL32UTF8');
                $objCsrResult = oci_new_cursor($objRscCon);
                $objStmt      = oci_parse($objRscCon,$strSql);

                oci_bind_by_name($objStmt,':Pn_IdDetalle'   ,$intIdDetalle);
                oci_bind_by_name($objStmt,':Pv_UsrUltMod' ,$strUsrUltMod);
                oci_bind_by_name($objStmt,':Pv_Status'  ,$strStatus,50);
                oci_bind_by_name($objStmt,':Pv_Message' ,$strMessage,4000);

                oci_execute($objStmt);
                oci_execute($objCsrResult);

                $arrayRespuesta = array ('status'       => $strStatus,
                                        'message'      => $strMessage);
            }
            else
            {
                $arrayRespuesta = array ('status'       => $strStatus,
                                        'message'      => $strMessage);
                throw new \Exception('Faltan parámetros para enviar.');
            }
        }
        catch (\Exception $objException)
        {
            $arrayRespuesta = array ('status'  => 'fail',
                                     'message' => 'Error en infoDetalleRepository función actualizaInfoTarea : '.$objException->getMessage());
        }
        return $arrayRespuesta;
    }

    /**
     * Método encargado de obtiene los registros de tareas desde la tabla INFO_TAREA
     *
     * @author Andrés Montero H. <amontero@telconet.ec>
     * @version 1.0 - 08-07-2020
     * 
     * @since 1.0
     *
     * @param  Array $arrayParametros => Datos en formato json que se envian a procedimiento  DB_SOPORTE.SPKG_INFO_TAREA.P_REPORTE_TAREAS
     * @return Array $arrayRespuesta
     */
    public function reporteInfoTarea($arrayParametros)
    {
        $intTotal   = 0;
        $strStatus  = "";
        $strMessage = "";

        try
        {
            $strSql = "BEGIN DB_SOPORTE.SPKG_INFO_TAREA.P_REPORTE_TAREAS(:Pcl_Json,".
                                                                      ":Pcl_JsonRespuesta,".
                                                                      ":Pn_Total,".
                                                                      ":Pv_Status,".
                                                                      ":Pv_Message); END;";

            $arrayOciCon               = $arrayParametros['ociCon'];
            $objRscCon                 = oci_connect($arrayOciCon['userSoporte'], 
                                                     $arrayOciCon['passSoporte'], 
                                                     $arrayOciCon['databaseDsn'],'AL32UTF8');
            $objStmt                   = oci_parse($objRscCon,$strSql);
            $arrayParametros['ociCon'] = null;
            $objClobJsonRespuesta      = oci_new_descriptor($objRscCon, OCI_D_LOB);
            oci_bind_by_name($objStmt,':Pcl_Json'   ,json_encode($arrayParametros));
            oci_bind_by_name($objStmt, ':Pcl_JsonRespuesta', $objClobJsonRespuesta, -1, OCI_B_CLOB);
            oci_bind_by_name($objStmt,':Pn_Total'   ,$intTotal,10);
            oci_bind_by_name($objStmt,':Pv_Status'  ,$strStatus,50);
            oci_bind_by_name($objStmt,':Pv_Message' ,$strMessage,4000);

            oci_execute($objStmt);

            $arrayRespuesta = array ('status'       => $strStatus,
                                     'message'      => $strMessage,
                                     'total'        => $intTotal,
                                     'objJsonRespuesta'=> $objClobJsonRespuesta->load()
                                    );
        }
        catch (\Exception $objException)
        {
            $arrayRespuesta = array ('status'  => 'fail',
                                     'message' => $objException->getMessage());
        }
        return $arrayRespuesta;
    }

    /**
     * Actualización: Se realizan verificaciones para botón de confirmación del enlace en tareas 
     *                            de soporte Tn con última milla Fibra óptica.
     *                Se agrega corrección de caracteres especiales para mostrar la 
     *                            observación de la tarea: Se reemplaza la cadena '*fff' por '"'
     *                            en el parámetro "observacion" recibido desde función getTareasSoporte().
     * 
     * @author Andrés Montero H. <amontero@telconet.ec>
     * @version 1.1 - 27-07-2020
     * 
     * 
     * Método encargado de obtiene los registros de tareas y genera json de respuesta con las tareas
     *
     * @author Andrés Montero H. <amontero@telconet.ec>
     * @version 1.0 - 08-07-2020
     * 
     * @since 1.0
     *
     * @param  Array $arrayParametros => Datos en formato json que se envian a función reporteInfoTarea()
     * @return Array $arrayRespuesta
     * 
     * @author kevin ortiz. <kcortiz@telconet.ec>
     * @version 1.1 - 31-07-2020 - se cambio el valor de variable $booleanRenviarSysCloud a true
     *  
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.2 - 09-11-2020 - Se inicializa la variable '$booleanIsDepartamento' a true, cada vez que
     *                             en el ciclo for se pase a la siguiente actividad, por motivos que cuando cambiaba
     *                             a false, las demas actividades eran afectadas y no se mostraba las acciones
     *                             en el grid de tareas.
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.3 08-12-2020
     * Se agrega lógica y variable "idServicioVrf" para validar enlaces en nueva función
     * 
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.4 - 01-03-2021 - Se agrega el estado 'Replanificada' en la validación para mostrar el reasignar tarea, cuando
     *                             la actividad proviene de hal.
     * 
     * @author Pedro Velez <psvelez@telconet.ec>
     * @version 1.5 - 01-10-2021 - Se agrega parametro en la respuesta del metodo 
     *                             donde se envia el id y nombre de la tarea anterior.
     * 
     * Actualización: Se realiza modificación para optimizar el tiempo de respuesta de la consulta de tareas en TELCOS.
     *                Se consulta información a nivel de store procedure
     *                Observación: se recomienda para futuras modificaciones realizarla a nivel de base, para no afectar el tiempo de respuesta
     * 
     * @author Fernando López. <filopez@telconet.ec>
     * @version 1.6 - 06-05-2022
     * 
     * Actualización: Se realiza modificación para permitir consultar todas las tareas del user en sesión, siempre y cuando no exceda
     *                el límite configurado.
     * 
     * @author Fernando López. <filopez@telconet.ec>
     * @version 1.7 - 28-07-2022
     * 
     * Actualización: Se regulariza cambio en caliente para poder presentar todos datos de la tarea por departamento.
     * 
     * @author José Guamán. <jaguamanp@telconet.ec>
     * @version 1.8 - 23-11-2022
     * 
     * Actualización: Añadir try/catch en el metodo para contemplar errores en la consulta y detectar futuros errores en la
     * en el json_decode.
     * 
     * @author José Guamán. <jaguamanp@telconet.ec>
     * @version 1.9 - 07-12-2022
     * 
     */
    public function generarJsonInfoTareas($arrayParametros)
    {
        $serviceUtil  = $arrayParametros['serviceUtil'];
        $strIp                               = $arrayParametros["strIp"];
        $arrayEncontrados                    = array();
        $arrayResultados                     = array();
        $objEmComercial                      = $arrayParametros["emComercial"];
        $objEmComunicacion                   = $arrayParametros["emComunicacion"];
        $booleanIsDepartamento               = $arrayParametros["isDepartamento"];
        $intDepartamentoSession              = $arrayParametros["departamentoSession"];
        $booleanExisteFiltro                 = $arrayParametros["existeFiltro"];
        $strPrefijoEmpresa                   = $arrayParametros["prefijoEmpresa"];
        $booleanPermiteRegAct                = $arrayParametros["permiteRegistroActivos"];
        $strLoginSesion                      = $arrayParametros["strUser"];
        $boolConfirIpSopTn                   = $arrayParametros["permiteConfirIpSopTn"]; 
        $boolValEnlaSopTn                    = $arrayParametros["permiteValidarEnlaceSopTn"];
        $strNombreProceso                    = "";
        $strNombreDepartamento               = '';
        $arrayParametros['nombreAsignado']   = '';
        $strPresentarSubtarea                = "icon-invisible";
        $strCerrarTarea                      = "S";
        $strBanderaFinalizarInformeEjecutivo = "S";
        $intTareaPadre                       = "";
        $strMostrarOpcionSeguiInterno        = "N";
        $intMinutosTareaPausada              = 0;
        $intTiempoTareaPausada               = 0;
        $intMinutosInicio                    = 0;
        $intPersonaEmpresaRol                = $arrayParametros["intPersonaEmpresaRol"];
        $strShowAllTask                     = 'N';

        try
        {
        
        //Se obtiene el nombre del departamento en el cual se esta buscando
        if($arrayParametros["idDepartamento"] && $arrayParametros["idCuadrilla"]==null)
        {
            $objDepartamento = $this->_em->getRepository('schemaBundle:AdmiDepartamento')->find($arrayParametros["idDepartamento"]);
            if($objDepartamento)
            {
                $strNombreDepartamento             = $objDepartamento->getNombreDepartamento();
                $arrayParametros['nombreAsignado'] = $strNombreDepartamento;
            }
        }

        //Se obtiene el nombre de la cuadrilla en la cual se esta buscando
        if($arrayParametros["idCuadrilla"] && $arrayParametros["idDepartamento"]==null && $arrayParametros["idCuadrilla"]!='Todos')
        {
            foreach ($arrayParametros["idCuadrilla"] as $intIdcuadrilla)
            {
                $objCuadrilla = $objEmComercial->getRepository('schemaBundle:AdmiCuadrilla')->find($intIdcuadrilla);
                if(is_object($objCuadrilla))
                {
                    $arrayParametros['nombreAsignado'][] = $objCuadrilla->getNombreCuadrilla();
                }
            }
        }

        //Obtenemos el parámetro de la fecha por defecto
        if ( ((!isset($arrayParametros["feFinalizadaHasta"]) || $arrayParametros["feFinalizadaHasta"] === '') &&
             (!isset($arrayParametros["feFinalizadaDesde"]) || $arrayParametros["feFinalizadaDesde"] === '') &&
             (!isset($arrayParametros["feSolicitadaHasta"]) || $arrayParametros["feSolicitadaHasta"] === '') &&
             (!isset($arrayParametros["feSolicitadaDesde"]) || $arrayParametros["feSolicitadaDesde"] === ''))
             || (isset($arrayParametros["queryAllTask"]) && $arrayParametros["queryAllTask"] === 'S'))
        {
            $arrayFechaDefecto = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                    ->getOne('TAREAS_FECHA_DEFECTO','SOPORTE','','','','','','','','');

            if (!empty($arrayFechaDefecto) && count($arrayFechaDefecto) > 0 &&
                checkdate($arrayFechaDefecto['valor2'],$arrayFechaDefecto['valor3'],$arrayFechaDefecto['valor1']))
            {
                $strFechaDefecto                    = $arrayFechaDefecto['valor1'].'-'. //Año
                                                      $arrayFechaDefecto['valor2'].'-'. //Mes
                                                      $arrayFechaDefecto['valor3'];     //Día
                if($arrayParametros["queryAllTask"] != 'S')
                {
                    $arrayParametros['strFechaDefecto'] = $strFechaDefecto;
                }
            }
        }

        //fix para mostrar toda las tareas del user en sesión si no exceden el límite configurado
        $intTotalTareasPerfil = -1;
        if (isset($arrayParametros["arrayPersonaEmpresaRol"]) && !empty($arrayParametros["arrayPersonaEmpresaRol"]) 
                && isset($arrayParametros["queryAllTask"]) && $arrayParametros["queryAllTask"] === 'S' && isset($strFechaDefecto))
        {
            $arrayParametrosQuery["strIdsPersonaEmpresaRol"] = implode(",",$arrayParametros["arrayPersonaEmpresaRol"]);
            $arrayParametrosQuery['strFechaDefecto'] = $strFechaDefecto;
            $arrayTotalTareasPerfil = $this->getTotalTareasPerfil($arrayParametrosQuery);
            $intTotalTareasPerfil = isset($arrayTotalTareasPerfil[0]['total'])?$arrayTotalTareasPerfil[0]['total']:-1;
            $arrayParametroLimiteTarea = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                                                                    ->get('LIMITE_CONSULTA_TAREAS',
                                                                          'SOPORTE','','','','','','','',''
                                                                         );
            $intLimiteConsultaTareas = isset($arrayParametroLimiteTarea[0]['valor1'])?intval($arrayParametroLimiteTarea[0]['valor1']):1000;
            if($intTotalTareasPerfil != -1 && $intTotalTareasPerfil <= $intLimiteConsultaTareas)
            {
                $arrayParametros["feFinalizadaHasta"] = '';
                $arrayParametros["feFinalizadaDesde"] = '';
                $arrayParametros["feSolicitadaHasta"] = '';
                $arrayParametros["feSolicitadaDesde"] = '';
                $arrayParametros['strFechaDefecto'] = $strFechaDefecto;
                $strShowAllTask = 'S';
            }
        }


        //Se obtiene mensaje configurado para el inicio de las tareas desde el mobil
        $arrayParametroMsgInicioTarea = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->getOne('MSG_INICIO_EJECUCION_TAREA','SOPORTE',
                                                                    'TAREAS','MSG_INICIO_TAREA_MOBIL','','','','','','');

        $strObsMovil =   !empty($arrayParametroMsgInicioTarea["valor1"])?$arrayParametroMsgInicioTarea["valor1"]:"";

        $arrayAdmiParamDetTareasInfoAdic  = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->getResultadoDetallesParametro( 'TAREAS_MOSTRAR_BTN_INFO_ADICIONAL', 
                                                                                        '','');
        $strTareasInfoAdic = '';
        if(intval($arrayAdmiParamDetTareasInfoAdic['total']) > 0)
        {
            $arrayTareasInfoAdic = $arrayAdmiParamDetTareasInfoAdic['registros'];
            $strSeparador = ',';
            foreach($arrayTareasInfoAdic as $key=>$arrayTareaAdic) 
            {   if(count($arrayTareasInfoAdic) == $key+1)
                {
                    $strSeparador = '';
                }
                $strTareasInfoAdic = $strTareasInfoAdic.'|'.$arrayTareaAdic['valor1'].'|'.$strSeparador;
            }
        }

        $arrayParametroTareaInstalacion = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->getOne('TAREA INSTALACION','SOPORTE',
                                                    'TAREAS','ID_TAREA_INSTALACION','','','','','','');
        
        $arrayIdTareasNoReqActivo 	= $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->getOne('IDS_TAREAS_NO_REG_ACTIVOS', 
                                                        '','','','','','','');                                            
        
        $arrayParametros["tablaConsulta"] = 'InfoTarea';
        $arrayParametros["newCamposConsulta"] = 'S';
        $arrayParametros['descCaractSolicitud'] = $arrayParametros["caracteristicaSolicitud"];
        $arrayParametros['tareaInfoAdicional'] = $strTareasInfoAdic;
        $arrayParametros['obsTareaIniMovil'] = $strObsMovil;
        $arrayParametros['idTareasNoReqActivo'] =  !empty($arrayIdTareasNoReqActivo['valor1']) ? $arrayIdTareasNoReqActivo['valor1'] : "";
        $arrayParametros['idTareaInstalacion'] = $arrayParametroTareaInstalacion["valor1"];
        $arrayResultados                  = $this->getTareasSoporte($arrayParametros);
        $intCantidad                      = $arrayResultados['total'] ? $arrayResultados['total'] : 0;
        $arrayDptosEmpleadoEmpresas       = $arrayParametros['arrayDepartamentos'];
        $booleanVerTareasTodasEmpresas    = $arrayParametros['booleanVerTareasTodasEmpresa'];
        $booleanEsDptoAutorizadoGestion   = false;

        if (!empty($arrayResultados) && $intCantidad > 0)
        {
            //Consulta datos parametros, modificación para hacer una solaconsulta 
        
            $arrayIdTareasReqActivo  = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                                                 ->getOne('IDS_TAREAS_REASIGNACION_REG_ACTIVOS', 
                                                                '','','','','','','');

            $arrayNumBobinaInstalacion  = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                                                ->getOne('PARAMETROS_GENERALES_MOVIL','','','', 
                                                            'NUMERO_BOBINAS_INSTALACION','','','');

            $arrayNumBobinaSoporte      = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                                                ->getOne('PARAMETROS_GENERALES_MOVIL','','','', 
                                                        'NUMERO_BOBINAS_SOPORTE','','','');
            
            
            $strJsonRespuesta   = trim(preg_replace('/\s+/', ' ', $arrayResultados['objJsonRespuesta']));

            $arrayRespuestaJson = json_decode($strJsonRespuesta);  
            
            if (JSON_ERROR_NONE !== json_last_error()) 
            {
                if (is_object($serviceUtil))
                {
                    $serviceUtil->insertError('InfoDetalleRepository',
                                            'generarJsonInfoTareas-json',
                                            'Unable to parse response body into JSON: ' . json_last_error(),
                                            $strLoginSesion,
                                            $strIp);
                }
                $strResultado = '{"total":"0","showAllTask":"' . $strShowAllTask . '","encontrados":[]}';

                return $strResultado;
            }

            foreach($arrayRespuestaJson as $objDato)
            {
                $arrayDatos = array (
                                     'numero'                  => $objDato->numero,
                                     'asignadoIdHis'           => $objDato->asignado_id_his,
                                     'departamentoOrigenIdHis' => $objDato->departamento_origen_id,
                                     'idDetalle'               => $objDato->detalle_id,
                                     'latitud'                 => $objDato->latitud,
                                     'longitud'                => $objDato->longitud,
                                     'usrCreacionDetalle'      => $objDato->usr_creacion_detalle,
                                     'detalleIdRelacionado'    => $objDato->detalle_id_relacionado,
                                     'idTarea'                 => $objDato->tarea_id,
                                     'nombreTarea'             => $objDato->nombre_tarea,
                                     'descripcionTarea'        => $objDato->descripcion_tarea,
                                     'asignadoId'              => $objDato->asignado_id,
                                     'asignadoNombre'          => $objDato->asignado_nombre,
                                     'refAsignadoId'           => $objDato->ref_asignado_id,
                                     'refAsignadoNombre'       => $objDato->ref_asignado_nombre,
                                     'personaEmpresaRolId'     => $objDato->persona_empresa_rol_id,
                                     'idDepartamentoCreador'   => $objDato->departamento_id,
                                     'estado'                  => $objDato->estado,
                                     'usrTareaHistorial'       => $objDato->usr_creacion,
                                     'observacionHistorial'    => $objDato->observacion_historial,
                                     'tipoAsignado'            => $objDato->tipo_asignado,
                                     'observacion'             => str_replace('*fff','"',$objDato->observacion),
                                     'reenviaSysCloud'         => $objDato->reenviar_syscloud,
                                     'nombreActualizadoPor'    => $objDato->nombre_actualizado_por,
                                     'seMuestraCoordManga'     => $objDato->se_muestra_coord_manga,
                                     'empresaTarea'            => $objDato->empresa_tarea,
                                     'cerrarTarea'             => $objDato->cerrar_tarea,
                                     'numeroTareaPadre'        => $objDato->numero_tarea_padre,
                                     'permiteSeguimiento'      => $objDato->permite_seguimiento,
                                     'permiteAnular'           => $objDato->permite_anular,
                                     'esHal'                   => $objDato->es_hal,
                                     'muestraPestanaHal'       => $objDato->muestra_pestana_hal,
                                     'atenderAntes'            => $objDato->atender_antes,
                                     'permiteFinalizarInforme' => $objDato->permite_finalizar_informe,
                                     'esDepartamento'          => $objDato->es_departamento,
                                     'id_caso'                 => $objDato->idcaso,
                                     'ult_fecha_asignacion'    => $objDato->ult_fecha_asignacion,
                                     'detalle_sol_caract'      => $objDato->detalle_sol_caract,
                                     'ult_tipo_asignado'       => $objDato->ult_tipo_asignado,
                                     'tarea_iniciada_movil'    => $objDato->tarea_iniciada_movil,
                                     'fecha_tiempo_parcial'    => $objDato->fecha_tiempo_parcial,
                                     'id_dep_coordinador'      => $objDato->id_dep_coordinador,
                                     'has_caracteristica_detalle' => $objDato->has_caracteristica_detalle,
                                     'tarea_anterior'          => $objDato->tarea_anterior,
                                     'fecha_creacion_tarea'    => $objDato->fecha_creacion_tarea,
                                     'info_tarea_adic'         => $objDato->info_tarea_adic,
                                     'trunc_obs'               => $objDato->trunc_obs,
                                     'id_servicio_afect'       => $objDato->id_servicio_afect,
                                     'fecha_tiempo_parcial_caso' => $objDato->fecha_tiempo_parcial_caso,
                                     'data_caso'               => $objDato->data_caso,
                                     'ult_estado_caso'         => $objDato->ult_estado_caso,
                                     'cliente_afectado'        => $objDato->cliente_afectado,
                                     'ultima_milla_soporte'    => $objDato->ultima_milla_soporte,
                                     'es_interdepartamental'   => $objDato->es_interdepartamental,
                                     'info_serv_afect_tarea'              => $objDato->info_serv_afect_tarea,
                                     'progreso_tarea'          => $objDato->progreso_tarea
                );

                $objFeTareaCreada             = $objDato->fe_creacion_detalle;
                $objFeSolicitada              = $objDato->fe_solicitada;
                $objFeTareaAsignada           = $objDato->fe_creacion_asignacion;
                $objFeTareaHistorial          = $objDato->fe_creacion;
                $intNumeroTarea               = $objDato->numero_tarea;
                $strNombreProceso             = $objDato->nombre_proceso;
                $intIdDetalleHist             = $objDato->detalle_historial_id;
                $strRequiereControlActivo     = 'NO'; 
                $intNumBobinaVisualizar       = "";
                $strEstadoNumBobinaVisual     = "";
                $booleanIsDepartamento        = true;
                $booleanIsDepartamentoCreador = false;
                $intCasoId                    = intval($arrayDatos['id_caso']);
                $booleanConsultaObs = ($arrayDatos['trunc_obs']->length_obs > $arrayDatos['trunc_obs']->limit_trunc)?true:false;

                if($arrayDatos["idDepartamentoCreador"] == $intDepartamentoSession)
                {
                    $booleanIsDepartamentoCreador=true;
                }

                if (isset($arrayDatos['esDepartamento']->es_departamento_aut_gestion)
                && $arrayDatos['esDepartamento']->es_departamento_aut_gestion === 'S')
                {
                    $booleanEsDptoAutorizadoGestion = true;
                }
                if(isset($arrayDatos['esDepartamento']->es_departamento)&& $arrayDatos['esDepartamento']->es_departamento !== 'S')
                {
                    $booleanIsDepartamento = false;
                }
                
                $strStringClientes      =  isset($arrayDatos['cliente_afectado'])?$arrayDatos['cliente_afectado']:'';
                
                $strNombreActualizadoPor = "";
                $strUsrTareaHistorial    = ($arrayDatos["usrTareaHistorial"] ? $arrayDatos["usrTareaHistorial"] : "");

                if($strUsrTareaHistorial)
                {
                    $strNombreActualizadoPor = (isset($arrayDatos['nombreActualizadoPor']))?$arrayDatos['nombreActualizadoPor']:"";

                    if (strpos($strUsrTareaHistorial,'@') > 0 && is_object($arrayParametros['serviceSoporte']))
                    {
                        $strContexto = '';

                        if ($strStringClientes == '')
                        {
                            //Verificamos si la tarea tiene característica REFERENCIA_PERSONA Activa.
                            $objAdmiCaracteristicaRefPersona = $this->_em->getRepository('schemaBundle:AdmiCaracteristica')
                            ->findOneBy(array ('descripcionCaracteristica' => 'REFERENCIA_PERSONA',
                                             'estado'                    => 'Activo'));
 
                            if (is_object($objAdmiCaracteristicaRefPersona))
                            {
                                $objInfoTareaCaracteristicaRefPersona = $this->_em->getRepository('schemaBundle:InfoTareaCaracteristica')
                                        ->findOneBy( array('tareaId'       => $intNumeroTarea,
                                                        'caracteristicaId' => $objAdmiCaracteristicaRefPersona->getId(),
                                                        'estado'           => 'Activo'));

                                if (is_object($objInfoTareaCaracteristicaRefPersona))
                                {
                                    $intIdPersona = $objInfoTareaCaracteristicaRefPersona->getValor();

                                    $objInfoPersonaEmpresaRol =  $objEmComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                ->getPersonaEmpresaRolPorPersonaPorTipoRol($intIdPersona, 'Cliente', '10');

                                    $strContexto = $objInfoPersonaEmpresaRol->getId();
                                }
                            }
                        }
                        else
                        {
                            $arrayPuntos = $objEmComercial->getRepository('schemaBundle:InfoPunto')
                                                                                    ->findBy(
                                                                                            array('login' => $strStringClientes,
                                                                                                'estado' => array('Activo','In-Corte')));
                            if (!empty($arrayPuntos))
                            {
                                $strContexto =  $arrayPuntos[0]->getPersonaEmpresaRolId()->getId();
                            }
                            
                        }

                        $arrayCuentaExtranet = $arrayParametros['serviceSoporte']
                            ->getConsultaCuentaExtranet(array('usuario'  => $strUsrTareaHistorial,
                                                              'contexto' => $strContexto));
                        $strNombreActualizadoPor = $arrayCuentaExtranet['nombres'].' '.$arrayCuentaExtranet['apellidos'] 
                                                                                        .' ('.$strUsrTareaHistorial.')';
                    }
                }

                // SE VERIFICA SI LA TAREA PERTENECE A UN CASO O ES INDEPENDIENTE
                
                if($intCasoId != 0 && $arrayDatos['fecha_tiempo_parcial_caso']->has_fecha === 'S')
                {
                    $objFechaEjecucion = new \DateTime($arrayDatos['fecha_tiempo_parcial_caso']->fe_creacion);
                    $objFechaEjecucion->modify('+'.$arrayDatos['fecha_tiempo_parcial_caso']->tiempo.' minute');
                    $objFechaActual = new \DateTime(date_format(new \DateTime('now'), "d-m-Y H:i"));

                    if ($objFechaEjecucion < $objFechaActual && is_object($arrayParametros['serviceSoporte']))
                    {

                        $arrayParametrosHist["intDetalleId"]            = $arrayDatos["idDetalle"];
                        $arrayParametrosHist["intAsignadoId"]           = $arrayDatos['asignadoIdHis'];
                        $arrayParametrosHist["intIdDepartamentoOrigen"] = $arrayDatos['departamentoOrigenIdHis'];
                        $arrayParametrosHist["strUsrCreacion"]          = $arrayParametros['strUser'];
                        $arrayParametrosHist["strIpCreacion"]           = $arrayParametros['strIp'];
                        $arrayParametrosHist["strCodEmpresa"]           = $arrayParametros['intIdEmpresa'];
                        $arrayParametrosHist["strObservacion"]          = 'Cambio de estado Automático a Asignada';
                        $arrayParametrosHist["strAccion"]               = 'Asignada';
                        $arrayParametrosHist["strEstadoActual"]         = 'Asignada';
                        $arrayParametrosHist["boolHisSeg"]              = true;
                        
                        $arrayResultCalculo                             = $arrayParametros['serviceSoporte']
                                                                                            ->calcularTiempoEstado($arrayParametrosHist);
                        if ($arrayResultCalculo['status'] === 'ok')
                        {
                            $arrayDatos["estado"] = 'Asignada';

                            //ACTUALIZA TAREA EN INFO_TAREA
                            $arrayParametrosInfoTarea['intDetalleId']   = $arrayDatos["idDetalle"];
                            $arrayParametrosInfoTarea['strUsrCreacion'] = $arrayParametros['strUser'];
                            $arrayParametrosInfoTarea['objOciCon']      = $arrayParametros['ociCon'];
                            $this->creaInfoTarea($arrayParametrosInfoTarea);
                        }
                    }
                }

                //SE DETERMINA EN ESTE CASO EL TIEMPO DE INICIO DE LA TAREA
                $objFechaEjecucion = isset($arrayDatos['ult_fecha_asignacion'])?$arrayDatos['ult_fecha_asignacion']:'';

                if($objFechaEjecucion != "")
                {
                    $arrayFecha        = explode(" ", $objFechaEjecucion);
                    $arrayFech         = explode("-", $arrayFecha[0]);
                    $arrayHora         = explode(":", $arrayFecha[1]);
                    $objFechaEjecucion = $arrayFech[2] . "-" . $arrayFech[1] . "-" . $arrayFech[0];
                    $strHoraEjecucion  = $arrayHora[0] . ":" . $arrayHora[1];
                }
                else
                {
                    $objFechaEjecucion = "";
                    $strHoraEjecucion  = "";
                }

                $strNumeroCaso     = $arrayDatos['data_caso']->numero_caso;
                $strUltimoEstado   = isset($arrayDatos['ult_estado_caso'])?$arrayDatos['ult_estado_caso']:'';
                $strCodEmpresaCaso = $arrayDatos['data_caso']->empresa_cod;

                //Se verifica si se factura o no la tarea
                $strSeFactura          = "NO";
                if( $arrayDatos['detalle_sol_caract'] != 0)
                {
                    $strSeFactura = "SI";
                }

                //Se obtiene el tiempo transcurrido en minutos de la tarea
                $strEstado              = $arrayDatos["estado"];
                $strFechaCreacionTarea  = "";
                
                if($strEstado == 'Asignada' || $intCasoId != 0)
                {
                    $strFechaCreacionTarea = new \DateTime($objFeTareaCreada);
                }
                else
                {
                    $strFeCreacionTareaAceptada = isset($arrayDatos['fecha_creacion_tarea'])?$arrayDatos['fecha_creacion_tarea']:'';
                    $strFechaCreacionTarea      = new \DateTime($strFeCreacionTareaAceptada);

                }
                if( $strEstado == 'Cancelada' || $strEstado == 'Finalizada' || $strEstado == 'Rechazada' || $strEstado == 'Anulada')
                {
                    $objDatetimeFinal = new \DateTime($objFeTareaHistorial);
                }
                else
                {
                    $objDatetimeFinal = new \DateTime();
                }

                if(is_object($strFechaCreacionTarea))
                {
                    $objDatetimeDiferenciaFechas = $objDatetimeFinal->diff($strFechaCreacionTarea);
                    $intMinutos  = $objDatetimeDiferenciaFechas->days * 24 * 60;
                    $intMinutos += $objDatetimeDiferenciaFechas->h * 60;
                    $intMinutos += $objDatetimeDiferenciaFechas->i;
                }
                $strMinutos  = $intMinutos.' minutos';

                $objTareaTiempoParcial = $arrayDatos['fecha_tiempo_parcial'];
                if($arrayDatos["estado"] == "Pausada")
                {   
                    if($objTareaTiempoParcial->has_pausa == 'S')
                    {
                        $strMinutos = $objTareaTiempoParcial->tiempo_pausa . ' minutos';
                    }
                }
                else if($arrayDatos["estado"] <> 'Cancelada' && $arrayDatos["estado"] <> 'Finalizada' && $arrayDatos["estado"] <> 'Rechazada'
                        && $objTareaTiempoParcial->has_reanuda == 'S')
                {  
                    $strFeCreacionReanudada      = strval($objTareaTiempoParcial->fecha_reanuda);
                    $objDateFechaReanudada       = new \DateTime($strFeCreacionReanudada);
                    $objDateFechaActual          = new \DateTime();
                    $objDatetimeDiferenciaFechas = $objDateFechaActual->diff($objDateFechaReanudada);

                    $intMinutos = $objDatetimeDiferenciaFechas->days * 24 * 60;
                    $intMinutos += $objDatetimeDiferenciaFechas->h * 60;
                    $intMinutos += $objDatetimeDiferenciaFechas->i;

                    if($objTareaTiempoParcial->has_pausa == 'S')
                    {
                        $intTiempoTareaPausada = $objTareaTiempoParcial->tiempo_pausa;
                    }

                    $strMinutos = $intMinutos + $intTiempoTareaPausada;
                    $strMinutos = $strMinutos.' minutos';
                }
                
                if($objTareaTiempoParcial->has_reanuda == 'S')
                {   
                    if($objTareaTiempoParcial->has_inicia == 'S' && $objTareaTiempoParcial->has_detalle == 'S')
                    {  
                        $strFeCreacionIniciada            = strval($objTareaTiempoParcial->fecha_inicia);
                        $strFeCreacionDetalle             = strval($objTareaTiempoParcial->fecha_detalle);
                        $objDateFeCreacionIniciada        = new \DateTime($strFeCreacionIniciada);
                        $objDateFeCreacionDetalle         = new \DateTime($strFeCreacionDetalle);
                        $objDatetimeDiferenciaFechasPausa = $objDateFeCreacionDetalle->diff($objDateFeCreacionIniciada);

                        $intMinutosInicio  = $objDatetimeDiferenciaFechasPausa->days * 24 * 60;
                        $intMinutosInicio += $objDatetimeDiferenciaFechasPausa->h * 60;
                        $intMinutosInicio += $objDatetimeDiferenciaFechasPausa->i;
                    }
                    $intMinutosTareaPausada = $objTareaTiempoParcial->tiempo_reanuda_p + $intMinutosInicio;
                }
                //Fin Se obtiene el tiempo transcurrido en minutos de la tarea

                $strMostrarCoordenadas = (isset($arrayDatos['seMuestraCoordManga']->mostrar_coordenada))?
                                          $arrayDatos['seMuestraCoordManga']->mostrar_coordenada:"N";
                $strTareasManga        = (isset($arrayDatos['seMuestraCoordManga']->tareas_manga))?
                                          $arrayDatos['seMuestraCoordManga']->tareas_manga:"N";

                $strPrefijoEmpresaTarea = (isset($arrayDatos['empresaTarea']->prefijo_empresa))?$arrayDatos['empresaTarea']->prefijo_empresa:"";
                $intEmpresaId           = (isset($arrayDatos['empresaTarea']->cod_empresa))?intval($arrayDatos['empresaTarea']->cod_empresa):"";

                $strPresentarSubtarea = "icon-invisible";
                //Se determina si se debe mostrar el boton de agregar tarea
                if(($booleanIsDepartamento|| $booleanEsDptoAutorizadoGestion) 
                    && ($arrayDatos["estado"] <> "Finalizada" && $arrayDatos["estado"] <> "Cancelada"  && $arrayDatos["estado"] <> "Rechazada"
                    && $arrayDatos["estado"] <> "Asignada"    && $arrayDatos["estado"] <> "Pausada"    && $arrayDatos["estado"] <> "Anulada")
                    && !$intCasoId)
                {
                    $strPresentarSubtarea = "button-grid-agregarTarea";
                }

                $strCerrarTarea = $arrayDatos['numeroTareaPadre']==='N'?'N':'S';

                $intTareaPadre = isset($arrayDatos['numeroTareaPadre'])?intval($arrayDatos['numeroTareaPadre']):"";

                if($strPrefijoEmpresa == "TN")
                {
                    //Se valida si el departamento del usuario el session tiene permitido ingresar seguimientos internos
                    $strMostrarOpcionSeguiInterno = ($arrayDatos['permiteSeguimiento'] === 'S')?'S':'N';
                }

                $intMinutos = substr($strMinutos,0,-8);

                $booleanVerAnularTarea = false;
                if ($arrayDatos['permiteAnular'] === 'S')
                {
                    $booleanVerAnularTarea =true;
                }

                //Se valida si la tarea fue iniciada desde el mobil

                $strIniciadaDesdeMobil = isset($arrayDatos['tarea_iniciada_movil'])?$arrayDatos['tarea_iniciada_movil']:"S";

                $booleanMostrarInfoAdicional   = false;
                $strNombreElementoTarea     = "N/A";
                $strTipoElementoTarea       = "N/A";
                $strLatitudTarea            = $arrayDatos["latitud"] ? $arrayDatos["latitud"] : "N/A";
                $strLongitudTarea           = $arrayDatos["longitud"] ? $arrayDatos["longitud"] : "N/A";
                $strUsrCreacionDetalle      = $arrayDatos["usrCreacionDetalle"] ? $arrayDatos["usrCreacionDetalle"] : "N/A";
                $strObservacionDetalle      = $arrayDatos["observacion"] ? $arrayDatos["observacion"] : "N/A";
                $strInfoAdicional           = "";
                if(isset($arrayDatos["nombreTarea"]) && !empty($arrayDatos["nombreTarea"]))
                {                       
                    $strInfoTareaAdicional = $arrayDatos['info_tarea_adic'];

                    if($strInfoTareaAdicional->is_tarea_adic == 'S' )
                    {
                        $booleanMostrarInfoAdicional = true;
                        
                        if($strInfoTareaAdicional->has_info_adic == 'S')
                        {
                            $strTipoElementoTarea = $strInfoTareaAdicional->tipo_elemento_tarea;
                            $strNombreElementoTarea = $strInfoTareaAdicional->nombre_elemento_tarea;
                        }

                        $strInfoAdicional = "<b>Información Adicional</b>"
                                          . "<table>"
                                          . "<tr><td>Tipo de Elemento</td><td class=\"margenInfoAdicional\">:</td>"
                                          . "<td>".$strTipoElementoTarea."</td></tr>"
                                          . "<tr><td>Elemento</td><td class=\"margenInfoAdicional\">:</td>"
                                          . "<td>".$strNombreElementoTarea."</td></tr>"
                                          . "<tr><td>Latitud</td><td class=\"margenInfoAdicional\">:</td>"
                                          . "<td>".$strLatitudTarea."</td></tr>"
                                          . "<tr><td>Longitud</td><td class=\"margenInfoAdicional\">:</td>"
                                          . "<td>".$strLongitudTarea."</td></tr>"
                                          . "<tr><td>Usr. Creación</td><td class=\"margenInfoAdicional\">:</td>"
                                          . "<td>".$strUsrCreacionDetalle."</td></tr>"
                                          . "<tr><td>Observación</td><td class=\"margenInfoAdicional\">:</td>"
                                          . "<td>".$strObservacionDetalle."</td></tr>"
                                          . "</table>";
                    }
                }

                // Verificamos si la tarea proviene de hal
                $booleanEsHal = false;
                if($arrayDatos['esHal'] === 'S')
                {
                    $booleanEsHal = true;
                }

                /*
                 * Verificamos si el id de la tarea se encuentra parametrizada para mostrar la pestalla hal
                 * siempre y cuando la tarea no sea hal
                 */
                $booleanTareaParametro = false;
                if ($arrayDatos['muestraPestanaHal'] === 'S')
                {
                    $booleanTareaParametro = true;
                }
                

                //*****************Validar si la persona en session puede finalizar la tarea de generacion de informe ejecutivo*****************
                $strBanderaFinalizarInformeEjecutivo = ($arrayDatos['permiteFinalizarInforme'] === 'N')?'N':'S';

                //Validamos que el botón reprogramar se habilite de acuerdo a los estado mencionados.
                $booleanReprogramarHal        = false;
                $booleanMostrarReprogramarDep = false;

                if ($booleanEsHal)
                {
                    //Verificamos si el departamento está configurado para mostrar el botón reprogramar
                    if (isset($arrayDatos['muestra_reprogramar']->reprogramar_dep) && $arrayDatos['muestra_reprogramar']->reprogramar_dep == 'S')
                    {
                        $booleanMostrarReprogramarDep = true;
                    }
                    if (isset($arrayDatos['muestra_reprogramar']->reprogramar_hal) && $arrayDatos['muestra_reprogramar']->reprogramar_hal == 'S')
                    {
                        $booleanReprogramarHal = true;
                    }
                }

                //Validamos que el botón reasignar se habilite de acuerdo a los estado mencionados.
                $booleanReasignarHal = false;
                if ($booleanTareaParametro && 
                    ($arrayDatos["estado"] == "Aceptada" || $arrayDatos["estado"] == "Reprogramada" ||
                     $arrayDatos["estado"] == "Pausada"  || $arrayDatos["estado"] == "Asignada"     ||
                     $arrayDatos["estado"] == "Replanificada"))
                {
                    $booleanReasignarHal = true;
                }

                $booleanGestionCompleta = true;
                $booleanRenviarSysCloud = true;
                if ($arrayDatos['reenviaSysCloud']->gestion_completa !== 'S')
                {
                    $booleanGestionCompleta=false;
                }
                if ($arrayDatos['reenviaSysCloud']->reenviar_syscloud !== 'S')
                {
                    $booleanRenviarSysCloud = false;
                }
                $booleanAtenderAntes = false;
                if ($arrayDatos['atenderAntes'] === 'S')
                {
                    $booleanAtenderAntes = true;
                }

                $booleanEsInterdep          = $arrayDatos['es_interdepartamental'] == 'S'?true:false;
                

                $intIdServicioVrf                       = $arrayDatos['id_servicio_afect'] !== '' ?$arrayDatos['id_servicio_afect']:null;

                $objInfoServAfectTarea = $arrayDatos['info_serv_afect_tarea'];
                $intPersonaId  = $objInfoServAfectTarea->persona_id !==''? $objInfoServAfectTarea->persona_id:null;
                $intServicioId = $objInfoServAfectTarea->servicio_id !==''? $objInfoServAfectTarea->servicio_id:null;
                $arrayTipoMedio   = $objInfoServAfectTarea->tipo_medio !== ''?intval($objInfoServAfectTarea->tipo_medio):null;

                $strTieneFibra   = $arrayDatos['progreso_tarea']->tiene_fibra;
                $strTieneMateriales = $arrayDatos['progreso_tarea']->tiene_materiales;
                $strTieneConfirmacionIPserv =$arrayDatos['progreso_tarea']->tiene_confirmacion_ip_serv;

                $strIdsTareasReqActivos                 = "";
                
                if (is_array($arrayIdTareasReqActivo))
                {
                    $strIdsTareasReqActivos = !empty($arrayIdTareasReqActivo['valor1']) ? $arrayIdTareasReqActivo['valor1'] : "";
                }

                $arrayIdsTareasReqActivo = explode (",", $strIdsTareasReqActivos);  

                if(in_array($arrayDatos["idTarea"],$arrayIdsTareasReqActivo))
                {
                    $strRequiereControlActivo = 'SI';
                }

                $strPersonaEmpresaRol  = isset($arrayDatos['id_dep_coordinador'])?intval($arrayDatos['id_dep_coordinador']):'';
                
                if($intCasoId != 0)
                {
                    if(is_array($arrayNumBobinaSoporte))
                    {
                        $intNumBobinaVisualizar     = !empty($arrayNumBobinaSoporte['valor2']) ? $arrayNumBobinaSoporte['valor2'] : "";
                        $strEstadoNumBobinaVisual   = !empty($arrayNumBobinaSoporte['estado']) ? $arrayNumBobinaSoporte['estado'] : "";
                    }    
                }
                else
                {
                    if(is_array($arrayNumBobinaInstalacion))
                    {
                        $intNumBobinaVisualizar     = !empty($arrayNumBobinaInstalacion['valor2']) ? $arrayNumBobinaInstalacion['valor2'] : "";
                        $strEstadoNumBobinaVisual   = !empty($arrayNumBobinaInstalacion['estado']) ? $arrayNumBobinaInstalacion['estado'] : "";
                    }
                }

                $strPerteneceACaso = false;
                if ($intCasoId != 0)
                {
                    $strPerteneceACaso = true;
                }

                $strEsSesionTn = false;
                if ($strPrefijoEmpresa == "TN")
                { 
                    $strEsSesionTn = true;
                }

                $strCasoPerteneceTn = false;
                if ($strCodEmpresaCaso == "10")
                { 
                    $strCasoPerteneceTn = true;
                }

                //*****Validar si contiene la caracteristica de Crear KML, por medio del idDetalle */
                $strPermiteCrearKml = $arrayDatos['has_caracteristica_detalle'];

                $arrayTareaAnterior = $arrayDatos['tarea_anterior'];
                
                $arrayEncontrados[] = array(
                    'strNumero'           => $arrayDatos['numero'],
                    'strEmpresaTarea'     => $strPrefijoEmpresaTarea,
                    'id_detalle'          => $arrayDatos["idDetalle"],
                    'id_tarea'            => $arrayDatos["idTarea"],
                    'iniciadaDesdeMobil'  => $strIniciadaDesdeMobil,
                    'strBanderaFinalizarInformeEjecutivo' => $strBanderaFinalizarInformeEjecutivo,
                    'mostrarCoordenadas'  => $strMostrarCoordenadas,
                    'tareasManga'         => $strTareasManga,
                    'numero_tarea_Padre'  => $intTareaPadre?$intTareaPadre : "",
                    'nombre_proceso'      => $strNombreProceso ? $strNombreProceso : "",
                    'numero_tarea'        => $intNumeroTarea ? $intNumeroTarea : "",
                    'nombre_tarea'        => ($arrayDatos["nombreTarea"] ? $arrayDatos["nombreTarea"] : "N/A"),
                    'descripcionInicial'  => ($arrayDatos["descripcionTarea"] ? $arrayDatos["descripcionTarea"] : ""),
                    'cerrarTarea'         => $strCerrarTarea,
                    'seguimientoInterno'  => $strMostrarOpcionSeguiInterno,
                    'asignado_id'         => $arrayDatos["asignadoId"],
                    'asignado_nombre'     => ($arrayDatos["asignadoNombre"] ? ucwords(strtolower($arrayDatos["asignadoNombre"])) : "N/A"),
                    'ref_asignado_id'     => $arrayDatos["refAsignadoId"],
                    'ref_asignado_nombre' => ($arrayDatos["refAsignadoNombre"] ? 
                                             ucwords(strtolower($arrayDatos["refAsignadoNombre"])) : $arrayDatos["asignadoNombre"]),
                    'clientes'            => $strStringClientes,
                    'observacion'         => $arrayDatos["observacion"] ? ($booleanMostrarInfoAdicional ? $strInfoAdicional 
                                                                                                : $arrayDatos["observacion"]) : "",
                    'strTareaIncAudMant'  => $booleanMostrarInfoAdicional ? 'S' : 'N',
                    'feTareaCreada'       => $objFeTareaCreada ? $objFeTareaCreada : "",
                    'feSolicitada'        => $objFeSolicitada ? $objFeSolicitada : "",
                    'feTareaAsignada'     => $objFeTareaAsignada ? $objFeTareaAsignada : "",
                    'feTareaHistorial'    => $objFeTareaHistorial ? $objFeTareaHistorial : "",
                    'actualizadoPor'      => $strNombreActualizadoPor ? $strNombreActualizadoPor : "N/A",
                    'perteneceCaso'       => $strPerteneceACaso,
                    'sessionTN'           => $strEsSesionTn,
                    'casoPerteneceTN'     => $strCasoPerteneceTn,
                    'fechaEjecucion'      => $objFechaEjecucion,
                    'horaEjecucion'       => $strHoraEjecucion,
                    'id_caso'             => $intCasoId,
                    'estado_caso'         => $strUltimoEstado,
                    'numero_caso'         => $strNumeroCaso,
                    'seFactura'           => $strSeFactura,
                    'duracionTarea'       => $strMinutos,
                    'duracionMinutos'     => $intMinutos,
                    'tiempoPausada'       => $intMinutosTareaPausada,
                    'personaEmpresaRolId' => $intPersonaEmpresaRol,
                    'estado'              => $arrayDatos["estado"] ? $arrayDatos["estado"] : "",
                    'action1'             => $booleanGestionCompleta ? (($booleanIsDepartamento|| $booleanVerTareasTodasEmpresas ||
                                             $booleanMostrarReprogramarDep) ?
                                             (($arrayDatos["estado"] == "Aceptada" || $arrayDatos["estado"] == "Pausada"
                                                     || $booleanReprogramarHal) ?
                                               'button-grid-reprogramarTarea' : "icon-invisible") : "icon-invisible"
                                             ) : "icon-invisible", //action1 Reprogramar Tarea
                    'action2'             => $booleanGestionCompleta ? (($booleanIsDepartamento|| $booleanVerTareasTodasEmpresas) ?
                                             (($arrayDatos["estado"] == "Aceptada") ?
                                               'button-grid-rechazarTarea' : "icon-invisible") : "icon-invisible"
                                             ) : "icon-invisible", //action2 Cancelar Tarea
                    'action3'             => $booleanGestionCompleta ? (($booleanIsDepartamento|| $booleanVerTareasTodasEmpresas) ?
                                             (($arrayDatos["estado"] == "Aceptada") ?
                                               'button-grid-detenerTarea' : "icon-invisible") : "icon-invisible"
                                             ) : "icon-invisible", //action3 Finalizar Tarea
                    'action4'             => $booleanGestionCompleta ? (($booleanIsDepartamento|| $booleanVerTareasTodasEmpresas) ?
                                             (($arrayDatos["estado"] == "Aceptada" || $arrayDatos["estado"] == "Pausada"
                                                     || $booleanReasignarHal) ?
                                               'button-grid-finalizarTarea' : "icon-invisible") : "icon-invisible"
                                             ) : "icon-invisible", //action4 Reasignar Tarea
                    'action5'             => $booleanGestionCompleta ? (($booleanIsDepartamento|| $booleanVerTareasTodasEmpresas) ?
                                             (($arrayDatos["estado"] == "Aceptada") ?
                                                  'button-grid-finalizarTarea' : "icon-invisible") : "icon-invisible"
                                             ) : "icon-invisible", //action5 No existe en el index.js de tareas
                    'action6'             => $booleanGestionCompleta ? (($booleanIsDepartamento|| $booleanVerTareasTodasEmpresas) ?
                                             (($arrayDatos["estado"] == "Asignada" || $arrayDatos["estado"] == "Reprogramada") ?
                                               "button-grid-iniciarTarea" : "icon-invisible") : "icon-invisible"
                                             ) : "icon-invisible", //action6 Ejecutar Tarea
                    'action7'             => $booleanGestionCompleta ?
                                             (($arrayDatos["estado"] != "Finalizada" &&
                                               $arrayDatos["estado"] != "Cancelada"  &&
                                               $arrayDatos["estado"] != "Rechazada"  &&
                                               $arrayDatos["estado"] != "Anulada")  ? 'button-grid-agregarSeguimiento' : "icon-invisible"
                                             ) : "icon-invisible", //action7 Agregar Seguimiento
                    'action8'             => 'button-grid-show', //action8 Ver Seguimientos
                    'action9'             => $booleanGestionCompleta ?
                                             ($booleanVerAnularTarea ? "icon-invisible" : (
                                              $booleanIsDepartamento? (
                                                     ($arrayDatos["estado"] == "Asignada" || $arrayDatos["estado"] == "Reprogramada") ?
                                              'button-grid-rechazarTarea' : "icon-invisible") : "icon-invisible")
                                             ) : "icon-invisible", //action9 Rechazar Tarea
                    'action10'            => $booleanGestionCompleta ?
                                             ((($booleanIsDepartamento|| $booleanIsDepartamentoCreador ||
                                                $booleanVerTareasTodasEmpresas) && ($arrayDatos["estado"] <> "Pausada")) ?
                                                "button-grid-agregarArchivoCaso":"icon-invisible"
                                             ) : "icon-invisible", //action10 Cargar Archivo
                    'action11'            => 'button-grid-pdf', //action11 Ver Archivos
                    'action12'            => $booleanGestionCompleta ? $strPresentarSubtarea
                                                : "icon-invisible", //action12 Crear Tarea
                    'action13'            => $booleanGestionCompleta ?
                                             (($booleanIsDepartamento|| $booleanVerTareasTodasEmpresas) ?
                                             (($arrayDatos["estado"] == "Aceptada") ?
                                                  "button-grid-pausarTarea" : "icon-invisible") : "icon-invisible"
                                             ) : "icon-invisible", //action13 Pausar Tarea
                    'action14'            => $booleanGestionCompleta ?
                                             (($booleanIsDepartamento|| $booleanVerTareasTodasEmpresas) ?
                                             (($arrayDatos["estado"] == "Pausada") ?
                                               "button-grid-reanudarTarea" : "icon-invisible") : "icon-invisible"
                                             ) : "icon-invisible", //action14 Reanudar Tarea
                    'action15'            => $booleanGestionCompleta ? ($booleanVerAnularTarea ? 'button-grid-anularTarea'
                                                : "icon-invisible") : "icon-invisible", // action15 Anular Tarea
                    'tareaEsHal'          => $booleanEsHal,
                    'tipoAsignado'        => $arrayDatos['tipoAsignado'],
                    'esHal'               => ($booleanEsHal ? '<b style="color:green">SI</b>' : 'NO'),
                    'tareaParametro'      => $booleanTareaParametro,
                    'atenderAntes'        => ($booleanAtenderAntes ? '<b style="color:green">SI</b>' : 'NO'),
                    'tieneProgresoRuta'   => $strTieneFibra,
                    'tieneProgresoMateriales' => $strTieneMateriales,
                    'requiereControlActivo' => $strRequiereControlActivo,
                    'personaId'           => $intPersonaId,
                    'servicioId'          => $intServicioId,
                    'tipoMedioId'         => $arrayTipoMedio,
                    'permiteRegistroActivos' => $booleanPermiteRegAct,
                    'departamentoId'      => $strPersonaEmpresaRol,
                    'loginSesion'         => $strLoginSesion,
                    'intIdDetalleHist'    => $intIdDetalleHist,
                    'numBobinaVisualizar' => $intNumBobinaVisualizar,
                    'estadoNumBobinaVisual' => $strEstadoNumBobinaVisual,
                    'boolRenviarSysCloud' => $booleanRenviarSysCloud,
                    'esInterdepartamental'=> $booleanEsInterdep,
                    'permiteConfirIpSopTn'=> $boolConfirIpSopTn,
                    'permiteValidarEnlaceSopTn'  => $boolValEnlaSopTn,
                    'permiteCrearKml'     => $strPermiteCrearKml,
                    'strTieneConfirIpServ'=> $strTieneConfirmacionIPserv,
                    'idServicioVrf'       => $intIdServicioVrf,
                    'ultimaMillaSoporte'  => ($arrayDatos["ultima_milla_soporte"] != '')?$arrayDatos["ultima_milla_soporte"]:null,
                    'idTareaAnterior'     => ($arrayTareaAnterior->has_motivo == 'S')?$arrayTareaAnterior->tarea_id
                                             :$arrayDatos["idTarea"],
                    'nombreTareaAnterior' => ($arrayTareaAnterior->has_motivo == 'S')?$arrayTareaAnterior->nombre_tarea
                                             :($arrayDatos["nombreTarea"] ? $arrayDatos["nombreTarea"] : "N/A"),
                    'acciones'            => ""
                );
            }

            $arrayData      = json_encode($arrayEncontrados);
            $strResultado   = '{"total":"' . $intCantidad . '","showAllTask":"' . $strShowAllTask . '","encontrados":' . $arrayData . '}';
            return $strResultado;
        }
        else
        {
            $strResultado = '{"total":"0","showAllTask":"' . $strShowAllTask . '","encontrados":[]}';
            return $strResultado;
        }

        }
        catch (\Exception $objException)
        {

            if (is_object($serviceUtil))
            {
                $serviceUtil->insertError('InfoDetalleRepository',
                                          'generarJsonInfoTareas',
                                           $objException->getMessage(),
                                           $strLoginSesion,
                                           $strIp);
            }
            $strResultado = '{"total":"0","showAllTask":"' . $strShowAllTask . '","encontrados":[]}';
            return $strResultado;
        }
    }
    
    /**
     * Documentación para el método 'obtieneSolFcSinSolNcReub'.
     *
     * Función que obtiene una solicitud de Factura sin solicitud de Nota de Crédito enlazada a la tarea de reubicación.
     * 
     * @param  Array $arrayParametros: intIdTarea   => id de la tarea
     *
     * @return Array $arrayResultado.
     *
     * Costo: 20
     * 
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 25-10-2020
     * 
     */
    public function obtieneSolFcSinSolNcReub($arrayParametros)
    {
       
        $intIdTarea = $arrayParametros['intIdTarea'];
         
        $strSql = " SELECT IDS_FC.id as idDetalleSolicitud, IDSC_FC.valor as idTarea 
        
                 FROM schemaBundle:InfoDetalleSolCaract IDSC_FC, 
                      schemaBundle:InfoDetalleSolicitud IDS_FC, 
                      schemaBundle:AdmiTipoSolicitud ATS_FC 
        
                 WHERE IDS_FC.id                   =  IDSC_FC.detalleSolicitudId 
                 AND ATS_FC.id                     =  IDS_FC.tipoSolicitudId  
                 AND ATS_FC.descripcionSolicitud   =  'SOLICITUD FACTURACION POR REUBICACION' 
                 AND IDSC_FC.valor                 =  '$intIdTarea' 
                 
                 AND NOT EXISTS (SELECT IDS_NC.id  
                                   FROM schemaBundle:InfoDetalleSolCaract IDSC_NC, 
                                        schemaBundle:InfoDetalleSolicitud IDS_NC, 
                                        schemaBundle:AdmiTipoSolicitud ATS_NC 
                                   WHERE IDS_NC.id=IDSC_NC.detalleSolicitudId 
                                     AND ATS_NC.id = IDS_NC.tipoSolicitudId 
                                     AND ATS_NC.descripcionSolicitud = 'SOLICITUD NOTA CREDITO POR REUBICACION' 
                                     AND IDSC_NC.valor = IDSC_FC.valor ) ";  
 
        $objQuery       = $this->_em->createQuery($strSql);
        $arrayResultado = $objQuery->getResult();
        
        return $arrayResultado;
    }
        
    /**
     * Documentación para el método 'obtieneSolCaractFactReub'.
     *
     * Función que obtiene las características de la solicitud de factura enlazada a la tarea de reubicación.
     *
     * @param  Array $arrayParametros:
     *                                'strDescCaracteristica' => Descripción de la Característica,
     *                                'strEstado'             => estado Activo,
     *                                'intIdTarea'            => id de la tarea
     * 
     * @return Array $arrayResult.
     *
     * Costo: 6
     * 
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 25-10-2020
     */
    public function obtieneSolCaractFactReub($arrayParametros)
    {
       
        $intIdTarea            = $arrayParametros['intIdTarea'];
        $strDescCaracteristica = $arrayParametros['strDescCaracteristica'];
                
         try
        {
            $objRsm  = new ResultSetMappingBuilder($this->_em);
            
            $strSql  = "select idsc.DETALLE_SOLICITUD_ID 

                        from DB_COMERCIAL.INFO_DETALLE_SOL_CARACT idsc, 
                             DB_COMERCIAL.ADMI_CARACTERISTICA ac 

                        where 
                           ac.ID_CARACTERISTICA= idsc.CARACTERISTICA_ID 
                           and ac.DESCRIPCION_CARACTERISTICA = :strDescCaracteristica
                           and idsc.VALOR = :intIdTarea "; 
            
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);

            $objQuery->setParameter('strDescCaracteristica',$strDescCaracteristica);
            $objQuery->setParameter('intIdTarea',$intIdTarea);
             
            $objRsm->addScalarResult('DETALLE_SOLICITUD_ID', 'intIdSolFact', 'integer');     
             
            $objQuery->setSQL($strSql);
            $arrayResult = $objQuery->getResult();

        } 
        catch (\Exception $ex)
        {
            throw($ex);
        }
         return $arrayResult;

    }
    
    /**
     * Documentación para el método 'obtieneFactConNcReub'.
     *
     * Función que obtiene una factura con Nota de Crédito enlazada a la tarea de reubicación.
     * 
     * @param  Array $arrayParametros: ['intIdTarea'  => id de la tarea,
     *                                  'intIdPunto'  => id del punto
     *                                 ]
     * @return Array $arrayResult.
     * 
     * Costo: 18
     *
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 26-10-2020
     */
    public function obtieneFactConNcReub($arrayParametros)
    {
        $intIdTarea = $arrayParametros['intIdTarea'];
        $intIdPunto = $arrayParametros['intIdPunto'];
                
         try
        {
            $objRsm  = new ResultSetMappingBuilder($this->_em);
            
            $strSql  = "SELECT IDFC.ID_DOCUMENTO   
                
                            FROM DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB IDFC,  
                                 DB_FINANCIERO.INFO_DOCUMENTO_CARACTERISTICA IDC,  
                                 DB_COMERCIAL.ADMI_CARACTERISTICA AC  
                                 
                            WHERE IDFC.ID_DOCUMENTO                 = IDC.DOCUMENTO_ID   
                                AND   IDC.CARACTERISTICA_ID         = AC.ID_CARACTERISTICA  
                                AND   AC.DESCRIPCION_CARACTERISTICA = 'SOLICITUD_FACT_REUBICACION'    
                                AND   VALOR IN (SELECT IDSC.DETALLE_SOLICITUD_ID 
                                                  FROM DB_COMERCIAL.INFO_DETALLE_SOL_CARACT IDSC,  
                                                    DB_COMERCIAL.ADMI_CARACTERISTICA AC  
                                                  WHERE IDSC.CARACTERISTICA_ID        = AC.ID_CARACTERISTICA  
                                                  AND   AC.DESCRIPCION_CARACTERISTICA = 'NUMERO_TAREA_REUBICACION'  
                                                  AND   IDSC.VALOR                    = :intIdTarea)  
                                AND EXISTS (SELECT IDFC_NC.ID_DOCUMENTO  
                                              FROM DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB IDFC_NC,  
                                                   DB_FINANCIERO.ADMI_TIPO_DOCUMENTO_FINANCIERO ATDF_NC  
                                              WHERE IDFC_NC.TIPO_DOCUMENTO_ID       = ATDF_NC.ID_TIPO_DOCUMENTO  
                                                AND IDFC_NC.REFERENCIA_DOCUMENTO_ID = IDFC.ID_DOCUMENTO  
                                                AND ATDF_NC.CODIGO_TIPO_DOCUMENTO   = 'NC'
                                                AND IDFC_NC.ESTADO_IMPRESION_FACT   NOT IN ('Eliminado','Anulado'))  
                                AND IDC.USR_CREACION  = 'telcos_reubica'  
                                AND IDFC.USR_CREACION = 'telcos_reubica'  
                                AND IDFC.PUNTO_ID     = :intIdPunto ";  
  
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);

            $objQuery->setParameter('intIdTarea',$intIdTarea);
            $objQuery->setParameter('intIdPunto',$intIdPunto);
            $objRsm->addScalarResult('ID_DOCUMENTO', 'intIdDocumento', 'integer');     
             
            $objQuery->setSQL($strSql);
            $arrayResult = $objQuery->getResult();

        } 
        catch (\Exception $ex)
        {
            throw($ex);
        }
         return $arrayResult;

    }
    
    /**
     * Documentación para el método 'obtieneFactSinNcReub'.
     *
     * Función que obtiene una factura sin Nota de Crédito enlazada a la tarea de reubicación.
     * 
     * @param  Array $arrayParametros: ['intIdTarea'  => id de la tarea,
     *                                  'intIdPunto'  => id del punto
     *                                 ]
     * @return Array $arrayResult.
     * 
     * Costo: 17
     *
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 26-10-2020
     */
    public function obtieneFactSinNcReub($arrayParametros)
    {
       
        $intIdTarea  = $arrayParametros['intIdTarea'];
        $intIdPunto  = $arrayParametros['intIdPunto'];
                
         try
        {
            $objRsm  = new ResultSetMappingBuilder($this->_em);
            
            $strSql  = "SELECT IDFC.ID_DOCUMENTO   
                
                            FROM DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB IDFC,  
                                 DB_FINANCIERO.INFO_DOCUMENTO_CARACTERISTICA IDC,  
                                 DB_COMERCIAL.ADMI_CARACTERISTICA AC  
                                 
                            WHERE IDFC.ID_DOCUMENTO                 = IDC.DOCUMENTO_ID   
                                AND   IDC.CARACTERISTICA_ID         = AC.ID_CARACTERISTICA  
                                AND   AC.DESCRIPCION_CARACTERISTICA = 'SOLICITUD_FACT_REUBICACION'  
                                AND   IDFC.ESTADO_IMPRESION_FACT    = 'Activo'  
                                AND   VALOR IN (SELECT IDSC.DETALLE_SOLICITUD_ID 
                                                  FROM DB_COMERCIAL.INFO_DETALLE_SOL_CARACT IDSC,  
                                                    DB_COMERCIAL.ADMI_CARACTERISTICA AC  
                                                  WHERE IDSC.CARACTERISTICA_ID        = AC.ID_CARACTERISTICA  
                                                  AND   AC.DESCRIPCION_CARACTERISTICA = 'NUMERO_TAREA_REUBICACION'  
                                                  AND   IDSC.VALOR                    = :intIdTarea)  
                                AND NOT EXISTS (SELECT IDFC_NC.ID_DOCUMENTO  
                                              FROM DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB IDFC_NC,  
                                                   DB_FINANCIERO.ADMI_TIPO_DOCUMENTO_FINANCIERO ATDF_NC  
                                              WHERE IDFC_NC.TIPO_DOCUMENTO_ID       = ATDF_NC.ID_TIPO_DOCUMENTO  
                                                AND IDFC_NC.REFERENCIA_DOCUMENTO_ID = IDFC.ID_DOCUMENTO  
                                                AND ATDF_NC.CODIGO_TIPO_DOCUMENTO   = 'NC'
                                                AND IDFC_NC.ESTADO_IMPRESION_FACT   NOT IN ('Eliminado','Anulado'))   
                                AND IDC.USR_CREACION  = 'telcos_reubica'  
                                AND IDFC.USR_CREACION = 'telcos_reubica'  
                                AND IDFC.PUNTO_ID     = :intIdPunto ";  
  
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);

            $objQuery->setParameter('intIdTarea',$intIdTarea);
            $objQuery->setParameter('intIdPunto',$intIdPunto);
            $objRsm->addScalarResult('ID_DOCUMENTO', 'intIdDocumento', 'integer');     
             
            $objQuery->setSQL($strSql);
            $arrayResult = $objQuery->getResult();

        } 
        catch (\Exception $ex)
        {
            throw($ex);
        }
         return $arrayResult;

    }
    
    /**
     * Documentación para el método 'obtienePersonalAutNc'.
     *
     * Función que obtiene el personal autorizado parametrizado para la Nota de Crédito en el proceso de reubicación.
     * 
     * @param  Array $arrayParametros: strSqlPerAutNc   => Query para obtener al personal Autorizado
     *
     * @return Array $arrayResult.
     *
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 25-10-2020
     */
    public function obtienePersonalAutNc($arrayParametros)
    {   
        try
        {
            $objRsm   = new ResultSetMappingBuilder($this->_em);  
            $strSql   = $arrayParametros['strSqlPerAutNc'];
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);

            $objRsm->addScalarResult('NO_EMPLE', 'intIdEmpleado', 'integer');     
            $objRsm->addScalarResult('NOMBRE', 'strNombre', 'string');    
            $objQuery->setSQL($strSql);
            $arrayResult = $objQuery->getResult();

        } 
        catch (\Exception $ex)
        {
            throw($ex);
        }
         return $arrayResult;
    }
    
    /**
     * Documentación para el método 'obtenerPrecioPlanReubicacion'.
     *
     * Función que obtiene el precio del plan de Reubicación.
     * 
     * @param  Array $arrayParametros: strNombrePlan => Nombre del Plan,
     *                                 strCodEmpresa => Código de empresa.   
     *
     * @return Array $arrayResult.
     *
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 25-10-2020
     * Costo Query:5
     */
    public function obtenerPrecioPlanReubicacion($arrayParametros)
    {   
        try
        {
            $objRsm  = new ResultSetMappingBuilder($this->_em);
          
            $strSql  = " SELECT IPC.ID_PLAN, "
                     . " IPD.PRECIO_ITEM AS PRECIO  "
                     . " FROM  "
                     . " DB_COMERCIAL.INFO_PLAN_CAB IPC, "
                     . " DB_COMERCIAL.INFO_PLAN_DET IPD "
                     . " WHERE "
                     . " IPC.ID_PLAN = IPD.PLAN_ID AND "
                     . " IPC.NOMBRE_PLAN = :strNombrePlan AND "
                     . " IPC.EMPRESA_COD     = :strCodEmpresa ";

           $objQuery = $this->_em->createNativeQuery(null, $objRsm);
           
           $objQuery->setParameter('strNombrePlan', $arrayParametros['strNombrePlan']);
           $objQuery->setParameter('strCodEmpresa', $arrayParametros['strCodEmpresa']);
           $objRsm->addScalarResult('ID_PLAN', 'intIdPlan', 'integer');  
           $objRsm->addScalarResult('PRECIO', 'floatPrecioPlan', 'float');    
           $objQuery->setSQL($strSql);
           $arrayResult = $objQuery->getResult();

        } 
        catch (\Exception $ex)
        {
            throw($ex);
        }
         return $arrayResult;
    }   

    /**
     * Método que crea el Job con auto drop para la generación del reporte de SLA.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 21-01-2021
     *
     * @param  Array $arrayParametros => Se convierte en Json y se envía al procedimiento.
     * @return Array $arrayRespuesta
     */
    public function jobReporteSla($arrayParametros)
    {
        $strNombreJob = $arrayParametros['nombreJob'];
        $strTipo      = strtoupper($arrayParametros['tipo']);
        $strJson      = json_encode($arrayParametros);

        try
        {
            $strBloqueReporte = "DECLARE Lv_Status VARCHAR2(50); Lv_Message VARCHAR2(3000); ".
                                "BEGIN DB_SOPORTE.SPKG_GENERACION_SLA.P_REPORTE_SLA_$strTipo( ".
                                    "''$strJson'',Lv_Status,Lv_Message); END;";

            $strSqlJ = "DECLARE
                            Lv_nombreJob VARCHAR(500) := '$strNombreJob';
                        BEGIN
                            DBMS_SCHEDULER.CREATE_JOB(job_name   => '\"DB_SOPORTE\".\"'||Lv_nombreJob||'\"',
                                                      job_type   => 'PLSQL_BLOCK',
                                                      job_action => '
                                                        ".$strBloqueReporte."',
                                                      number_of_arguments => 0,
                                                      start_date          => NULL,
                                                      repeat_interval     => NULL,
                                                      end_date            => NULL,
                                                      enabled             => FALSE,
                                                      auto_drop           => TRUE,
                                                      comments            => 'Proceso para ejecutar el reporte de SLA.');

                            DBMS_SCHEDULER.SET_ATTRIBUTE(name      => '\"DB_SOPORTE\".\"'||Lv_nombreJob||'\"',
                                                         attribute => 'logging_level',
                                                         value     => DBMS_SCHEDULER.LOGGING_OFF);

                            DBMS_SCHEDULER.enable(name => '\"DB_SOPORTE\".\"'||Lv_nombreJob||'\"');

                        END;";

            $objStmt = $this->_em->getConnection()->prepare($strSqlJ);
            $objStmt->execute();

            $arrayRespuesta = array ('status'  =>  true,
                                     'message' => 'Proceso ejecutándose');
        }
        catch (\Exception $objException)
        {
            $arrayRespuesta = array ('status'  => false,
                                     'message' => $objException->getMessage());
        }
        return $arrayRespuesta;
    }

    /**
     * 
     * Función que obtiene la parte afectada por medio del ID Detalle.
     * Recordar que el idDetalle siempre debe ser el minimo.
     * 
     * @return Array $arrayResult.
     *
     * @author Wilmer Vera <wvera@telconet.ec>
     * @version 1.0 11-02-2021
     * Costo Query:5
     */
    public function getParteAfectadaPorDetalleId($intIdDetalleHipotesis)
    {   
        try
        {
            $objRsm  = new ResultSetMappingBuilder($this->_em);
          
            $strSql  = " SELECT
                            TIPO_AFECTADO,
                            AFECTADO_ID
                        FROM
                            DB_SOPORTE.INFO_PARTE_AFECTADA
                        WHERE
                            DETALLE_ID = (
                            SELECT
                                MIN(ID_DETALLE)
                            FROM
                                DB_SOPORTE.INFO_DETALLE
                            WHERE
                                DETALLE_HIPOTESIS_ID = :intIdDetalleHipotesis )";

           $objQuery = $this->_em->createNativeQuery(null, $objRsm);
           
           $objQuery->setParameter('intIdDetalleHipotesis', $intIdDetalleHipotesis);
           $objRsm->addScalarResult('TIPO_AFECTADO', 'strTipoAfectado', 'string');  
           $objRsm->addScalarResult('AFECTADO_ID', 'intAfectadoId', 'integer');    
           $objQuery->setSQL($strSql);
           $arrayResult = $objQuery->getResult();

        } 
        catch (\Exception $ex)
        {
            throw($ex);
        }
         return $arrayResult;
    }

    /**
     * Se utiliza para consultar una tarea por el campo observacion,
     * usuario y el tiempo de creacion
     * 
     *
     * @author David De La Cruz <ddelacruz@telconet.ec>
     * @version 1.0 27-08-2021
     * 
     * @param array $arrayParametros [
     *                                  fechaPivote : Fecha para buscar tarea,
     *                                  user        : Usuario que crea la tarea,
     *                                  observacion : Observacion de la tarea
     *                               ]
     * @return Array $arrayResult.
     * 
     */
    public function getTareaPorObservacion($arrayParametros)
    {   
        try
        {
            $objRsm  = new ResultSetMappingBuilder($this->_em);
          
            $strSql  = " SELECT i.NUMERO_TAREA "
                        ."FROM DB_SOPORTE.INFO_TAREA i "
                        ."WHERE i.fe_creacion >= to_date(:fechaPivote,'dd/mm/rrrr hh24:mi:ss') "
                        ."AND i.USR_CREACION = :usuario "
                        ."AND replace(replace(to_char(i.OBSERVACION),chr(10),''),chr(13),'') = replace(replace(:observacion,chr(10),''),chr(13),'')";

           $objQuery = $this->_em->createNativeQuery(null, $objRsm);
           
           $objQuery->setParameter('fechaPivote', $arrayParametros['fechaPivote']);
           $objQuery->setParameter('usuario', $arrayParametros['user']);
           $objQuery->setParameter('observacion', $arrayParametros['observacion']);
           $objRsm->addScalarResult('NUMERO_TAREA', 'intNumeroTarea', 'integer');  
           $objQuery->setSQL($strSql);
           $arrayResult = $objQuery->getResult();
        } 
        catch (\Exception $ex)
        {
            return array('result'=>'fail');
        }
         return $arrayResult;
    }

    /**
     * Función que devuelve las tareas de un servicio en base al tipo de solicitud.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 04-05-2021
     *
     * @param Array $arrayParametros [
     *                                  serviceUtil             : Objeto de la clase UtilService.
     *                                  strUsuario              : Usuario quien realiza la petición.
     *                                  strIpUsuario            : Ip del usuario quien realiza la petición.
     *                                  intIdServicio           : Id del servicio.
     *                                  strEstadoSolicitud      : Estado de la solicitud.
     *                                  strDescripcionSolicitud : Descripción de la solicitud.
     *                              ]
     * @return Array $arrayResultado
     */
    public function obtenerTareaSolicitudServicio($arrayParametros)
    {
        $serviceUtil  = $arrayParametros['serviceUtil'];
        $strUsuario   = $arrayParametros["strUsuario"]   ? $arrayParametros["strUsuario"]   : "Telcos+";
        $strIpUsuario = $arrayParametros["strIpUsuario"] ? $arrayParametros["strIpUsuario"] : "127.0.0.1";

        try
        {
            $objResultSetMap = new ResultSetMappingBuilder($this->_em);
            $objNativeQuery  = $this->_em->createNativeQuery(null, $objResultSetMap);

            $strSql = "SELECT ".
                         "ICO.ID_COMUNICACION ".
                       "FROM ".
                         "DB_COMUNICACION.INFO_COMUNICACION   ICO, ".
                         "DB_SOPORTE.INFO_DETALLE             IDET, ".
                         "DB_COMERCIAL.INFO_DETALLE_SOLICITUD IDETS, ".
                         "DB_COMERCIAL.ADMI_TIPO_SOLICITUD    ATS ".
                       "WHERE ".
                         "ICO.DETALLE_ID                =  IDET.ID_DETALLE ".
                         "AND ICO.ID_COMUNICACION       =  ( ".
                           "SELECT MIN(ICOMIN.ID_COMUNICACION) ".
                             "FROM DB_COMUNICACION.INFO_COMUNICACION ICOMIN ".
                           "WHERE ICOMIN.DETALLE_ID = IDET.ID_DETALLE ".
                         ") ".
                         "AND IDET.DETALLE_SOLICITUD_ID =  IDETS.ID_DETALLE_SOLICITUD ".
                         "AND IDET.ID_DETALLE           =  ( ".
                           "SELECT MAX(IDETMAX.ID_DETALLE) ".
                             "FROM DB_SOPORTE.INFO_DETALLE IDETMAX ".
                           "WHERE IDETMAX.DETALLE_SOLICITUD_ID = IDETS.ID_DETALLE_SOLICITUD ".
                         ") ".
                         "AND IDETS.TIPO_SOLICITUD_ID   =  ATS.ID_TIPO_SOLICITUD ".
                         "AND IDETS.SERVICIO_ID         = :intIdServicio ".
                         "AND IDETS.ESTADO              = :strEstadoSolicitud ".
                         "AND ATS.DESCRIPCION_SOLICITUD = :strDescripcionSolicitud";

            $objNativeQuery->setParameter('intIdServicio'          , $arrayParametros['intIdServicio']);
            $objNativeQuery->setParameter('strEstadoSolicitud'     , $arrayParametros['strEstadoSolicitud']);
            $objNativeQuery->setParameter('strDescripcionSolicitud', $arrayParametros['strDescripcionSolicitud']);

            $objResultSetMap->addScalarResult('ID_COMUNICACION','idComunicacion','integer');

            $objNativeQuery->setSQL($strSql);
            $arrayRespuesta = array("status" => true, "result" => $objNativeQuery->getResult());
        }
        catch (\Exception $objException)
        {
            if (is_object($serviceUtil))
            {
                $serviceUtil->insertError('InfoDetalleRepository',
                                          'obtenerTareaSolicitudServicio',
                                           $objException->getMessage(),
                                           $strUsuario,
                                           $strIpUsuario);
            }

            $arrayRespuesta = array ('status' => false,'message' => 'Error al obtener los datos');
        }
        return $arrayRespuesta;
    }

    /**
     * getTotalTareasPerfil
     * Método encargado de devolver el total de tareas por perfil.
     * 
     * Costo 2 
     * @author Fernando López <filopez@telconet.ec>
     * @version 1.0
     * @since 28-07-2022
     * 
     * @param Array $arrayParametros [strFechaDefecto , strIdsPersonaEmpresaRol]
     * @return Array [total] 
     */
    public function getTotalTareasPerfil($arrayParametros)
    {   $arrayRespuesta = array();
        try
        {
            $objResultSetMap = new ResultSetMappingBuilder($this->_em);
            $objNativeQuery  = $this->_em->createNativeQuery(null, $objResultSetMap);
        
            $strSql = "SELECT DB_SOPORTE.SPKG_INFO_ADICIONAL_TAREA.F_GET_TOTAL_TAREA_PERFIL(:strFechaDefecto,:strIdsPersonaEmpresaRol) AS TOTAL  
                    FROM DUAL";
        
            $objNativeQuery->setParameter("strFechaDefecto" ,  $arrayParametros['strFechaDefecto']);
            $objNativeQuery->setParameter("strIdsPersonaEmpresaRol" , $arrayParametros['strIdsPersonaEmpresaRol']);
            
            $objResultSetMap->addScalarResult('TOTAL','total','integer');                                                                    
                    
            $objNativeQuery->setSQL($strSql);
            $arrayRespuesta = $objNativeQuery->getArrayResult();
        }
        catch (\Exception $ex)
        {
            $arrayRespuesta = array (-1);
        }
  
        return $arrayRespuesta;              
    }
}

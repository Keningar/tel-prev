<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class AdmiProcesoRepository extends EntityRepository
{
    public function generarJson($parametros, $nombre, $estado, $start, $limit,$empresaCod="09",$esVisible="Todos")
    {
        $arr_encontrados = array();
        
        $registrosTotal = $this->getRegistros($parametros, $nombre, $estado, '', '',$empresaCod,$esVisible);
        $registros = $this->getRegistros($parametros, $nombre, $estado, $start, $limit ,$empresaCod,$esVisible);
 
        if ($registros) {
            $num = count($registrosTotal);            
            foreach ($registros as $data)
            {
                        
                $arr_encontrados[]=array('id_proceso' =>$data->getId(),
                                         'nombre_proceso' =>trim($data->getNombreProceso()),
                                         'nombre_proceso_padre' => trim($data->getProcesoPadreId() ? $data->getProcesoPadreId()->getNombreProceso() : "-" ),
                                         'descripcion_proceso' =>trim($data->getDescripcionProceso()),
                                         'visible' =>trim($data->getVisible()),
                                         'estado' =>(strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-edit'),
                                         'action3' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-delete'));
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_proceso' => 0 , 'nombre_proceso' => 'Ninguno', 'nombre_proceso_padre' => 'Ninguno', 'descripcion_proceso' => 'Ninguno', 'proceso_id' => 0 , 'proceso_nombre' => 'Ninguno', 'estado' => 'Ninguno'));
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
    
    /**
     * getRegistros
     *
     * Esta funcion retorna los procesos que estan configurados en la BD
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 25-03-2016 Se realizan ajustes para presentar los procesos dependiendo si es o no un plan mantenimiento
     * 
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 21-03-2016  Se realizan ajustes para corregir el pagineo en el grid de procesos en el modulo administracion
     *
     * @version 1.0
     *
     * @param array   $parametros
     * @param string  $nombre
     * @param string  $estado
     * @param string  $start
     * @param string  $limit
     * @param string  $empresaCod
     * @param string  $esVisible
     *
     * @return array $datos
     *
     */
    public function getRegistros($parametros, $nombre, $estado, $start, $limit, $empresaCod = "09", $esVisible = "Todos")
    {
        $boolBusqueda = false;
        $where = "";
        $query   = $this->_em->createQuery(null);

        if($estado && $estado != "Todos")
        {
            $boolBusqueda = true;
            if($estado == "Activo")
            {
                $where .= " LOWER(p.estado) not like LOWER(:estado) AND ";
                $query->setParameter('estado','Eliminado');
            }
            else
            {
                $where .= " LOWER(p.estado) like LOWER(:estado) AND ";
                $query->setParameter('estado',$estado);
            }
        }
        else
        {
            $where .= " p.estado is not null AND";
        }

        if($nombre && $nombre != "")
        {
            $boolBusqueda = true;
            $where .= " LOWER(p.nombreProceso) like LOWER(:nombreProceso) AND";
            $query->setParameter('nombreProceso', '%' . $nombre . '%');
        }

        if(isset($parametros["idProcesoActual"]))
        {
            if($parametros["idProcesoActual"] && $parametros["idProcesoActual"] != "")
            {
                $boolBusqueda = true;
                $where .= " p.id NOT IN (:procesoActual) AND";
                $query->setParameter('procesoActual',$parametros["idProcesoActual"]);
            }
        }
    
        if(isset($parametros["esPlanMantenimiento"]))
        {
            if($parametros["esPlanMantenimiento"] && $parametros["esPlanMantenimiento"]!="")
            {
                $where .= " p.esPlanMantenimiento LIKE :esPlanMantenimiento AND";
                $query->setParameter("esPlanMantenimiento", $parametros["esPlanMantenimiento"]);
            }
        }
        
        if(isset($parametros["procesoPadreId"]))
        {
            if($parametros["procesoPadreId"] && $parametros["procesoPadreId"]!="")
            {
                $where .= " p.procesoPadreId = :procesoPadreId AND";
                $query->setParameter("procesoPadreId", $parametros["procesoPadreId"]);
            }
        }


        if($empresaCod && $empresaCod != "")
        {

            $boolBusqueda = true;
            $where .= " b.empresaCod = :empresaCod ";
            $query->setParameter('empresaCod',$empresaCod);
        }
        if($esVisible && $esVisible != "Todos")
        {

            $boolBusqueda = true;
            $where .= " AND p.visible = :esVisible ";
            $query->setParameter('esVisible',$esVisible);
        }
        $sql = "SELECT p
                FROM schemaBundle:AdmiProceso p ,
                     schemaBundle:AdmiProcesoEmpresa b
                WHERE
                p.id = b.procesoId AND
                $where
                ORDER BY p.nombreProceso
               ";

        $query->setDQL($sql);

        if($start != '' && $limit != '')

            $datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
        else if($start != '' && !$boolBusqueda && $limit == '')
            $datos = $query->setFirstResult($start)->getResult();
        else if(($start == '' || $boolBusqueda) && $limit != '')
            $datos = $query->setMaxResults($limit)->getResult();
        else
            $datos = $query->getResult();

        return $datos;
    }



    /**
     * Obtiene los datos del proceso de acuerdo al nombre, estado y codEmpresa
     * 
     * @author David De La Cruz <ddelacruz@telconet.ec>
     * @version 1.0 25-08-2021
     * @param array $arrayParametros [
     *                                  nombre      : Nombre del Proceso,
     *                                  estado      : Estado del Proceso,
     *                                  codEmpresa  : Codigo Empresa del Proceso
     *                               ]
     * @return array $arrayResultado
     * 
     */
    public function getProceso($arrayParametros)
    {
        $strNombre      = $arrayParametros['nombre'] ? $arrayParametros['nombre'] : '';
        $strEstado      = $arrayParametros['estado'] ? $arrayParametros['estado'] : '';
        $strCodEmpresa  = $arrayParametros['codEmpresa'] ? $arrayParametros['codEmpresa'] : '';

        $objRsm   = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null,$objRsm);    

        try
        {

            $strSql = "SELECT AP.*
                        FROM DB_SOPORTE.ADMI_PROCESO AP,
                        DB_SOPORTE.ADMI_PROCESO_EMPRESA APE
                        WHERE AP.ESTADO       = :estado
                        AND AP.NOMBRE_PROCESO = :nombre
                        AND AP.ID_PROCESO     = APE.PROCESO_ID
                        AND APE.EMPRESA_COD   = :codEmpresa";

            $objRsm->addScalarResult('ID_PROCESO','idProceso','string');
            $objRsm->addScalarResult('PROCESO_PADRE_ID','procesoPadreId','string');
            $objRsm->addScalarResult('NOMBRE_PROCESO','nombreProceso','string');
            $objRsm->addScalarResult('DESCRIPCION_PROCESO','descripcionProceso','string');
            $objRsm->addScalarResult('APLICA_ESTADO','aplicaEstado','string');
            $objRsm->addScalarResult('ESTADO','estado','string');
            $objRsm->addScalarResult('VISIBLE','visible','string');
            $objRsm->addScalarResult('PLANMANTENIMIENTO','planMantenimiento','string');

            $objQuery->setParameter('nombre', $strNombre);
            $objQuery->setParameter('estado', $strEstado);
            $objQuery->setParameter('codEmpresa', $strCodEmpresa);
            
            $objQuery->setSQL($strSql);
            $arrayResultado = $objQuery->getArrayResult();
            
            $arrayResultado = array ('status'    => 'ok',
                                     'proceso' => $arrayResultado[0]);
            
        }
        catch(\Exception $objException)
        {
            error_log($objException->getMessage());
            $arrayResultado = array ('status'  => 'fail',
                                     'message' => $objException->getMessage());
        }

        return $arrayResultado;

    }

}

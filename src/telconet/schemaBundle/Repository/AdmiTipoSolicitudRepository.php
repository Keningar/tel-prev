<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class AdmiTipoSolicitudRepository extends EntityRepository
{
    public function generarJson($em_soporte, $em_seguridad, $nombre,$estado,$start,$limit)
    {
        $arr_encontrados = array();
        
        $registrosTotal = $this->getRegistros($nombre, $estado, '', '');
        $registros = $this->getRegistros($nombre, $estado, $start, $limit);
 
        if ($registros) {
            $num = count($registrosTotal);            
            foreach ($registros as $data)
            {                        
                $nombreProceso = "";
                $nombreTarea = "";
                $nombreItemMenu = "";
                if($data->getProcesoId() && $data->getProcesoId()!="")
                {
                    $EntityProceso = $em_soporte->getRepository('schemaBundle:AdmiProceso')->findOneById($data->getProcesoId()); 
                    $nombreProceso = $EntityProceso->getNombreProceso(); 
                }
                if($data->getTareaId() && $data->getTareaId()!="")
                {
                    $EntityTarea = $em_soporte->getRepository('schemaBundle:AdmiTarea')->findOneById($data->getTareaId()); 
                    $nombreTarea = $EntityTarea->getNombreTarea(); 
                }
                if($data->getItemMenuId() && $data->getItemMenuId()!="")
                {
                    $EntityItemMenu = $em_seguridad->getRepository('schemaBundle:SistItemMenu')->findOneById($data->getItemMenuId());
                    $nombreItemMenu = $EntityItemMenu->getNombreItemMenu(); 
                }
        
                $arr_encontrados[]=array('id_tipo_solicitud' =>$data->getId(),
                                         'descripcion_tipo_solicitud' =>trim($data->getDescripcionSolicitud()),
                                         'nombreProceso' =>trim($nombreProceso),
                                         'nombreTarea' =>trim($nombreTarea),
                                         'nombreItemMenu' =>trim($nombreItemMenu),
                                         'estado' =>(strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-edit'),
                                         'action3' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-delete'));
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_tipo_solicitud' => 0 , 'descripcion_tipo_solicitud' => 'Ninguno',  
                                                        'nombreProceso' => 'Ninguno', 'nombreTarea' => 'Ninguno', 'nombreItemMenu' => 'Ninguno', 
                                                        'tipo_solicitud_id' => 0 , 'tipo_solicitud_nombre' => 'Ninguno', 'estado' => 'Ninguno'));
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
    
    public function getRegistros($nombre,$estado,$start,$limit){
        $qb = $this->_em->createQueryBuilder();
            $qb->select('sim')
               ->from('schemaBundle:AdmiTipoSolicitud','sim');
            
        $boolBusqueda = false; 
        if($nombre!=""){
            $boolBusqueda = true;
            $qb ->where( 'LOWER(sim.descripcionTipoSolicitud) like LOWER(?1)');
            $qb->setParameter(1, '%'.$nombre.'%');
        }
        
        if($estado!="Todos"){
            $boolBusqueda = true;
            if($estado=="Activo"){
                $qb ->andWhere("LOWER(sim.estado) not like LOWER('Eliminado')");
            }
            else{
                $qb ->andWhere('LOWER(sim.estado) = LOWER(?2)');
                $qb->setParameter(2, $estado);
            }
        }
        
        if($start!='' && !$boolBusqueda)
            $qb->setFirstResult($start);   
        if($limit!='')
            $qb->setMaxResults($limit);
        
        
        $query = $qb->getQuery();
        
        return $query->getResult();
    }

    /**
     * getEstadosTipoSolicitudes, Realiza el group by de los estados de las solicitudes
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 19-01-2015
     * @return json $jsonEstadoTipoSolicitudes Retorna los estados de las solicitudes
     */
    public function getEstadosTipoSolicitudesJson()
    {
        $intTotal = 0;
        $objQb    = $this->_em->createQueryBuilder();
        $objQb->select('ids.estado')
            ->from('schemaBundle:InfoDetalleSolicitud', 'ids')
            ->where('ids.estado IS NOT NULL')
            ->groupBy('ids.estado');
        $objQuery       = $objQb->getQuery();
        $arrayResult    = $objQuery->getArrayResult();
        foreach($arrayResult as $arrayResult):
            $intTotal = $intTotal + 1;
            $arrayEstadoSolicitudes[] = array('estado' => $arrayResult['estado']);
        endforeach;
        $objDatos                   = json_encode($arrayEstadoSolicitudes);
        $jsonEstadoTipoSolicitudes  = '{"intTotal":"' . $intTotal . '","objDatos":' . $objDatos . '}';
        return $jsonEstadoTipoSolicitudes;
    } //getEstadosTipoSolicitudesJson

}

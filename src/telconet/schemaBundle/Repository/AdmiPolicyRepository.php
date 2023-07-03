<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AdmiPolicyRepository extends EntityRepository
{
    /**
    * generarJson
    *
    * Metodo encargado de obtener el json de los policys ingresados
    * 
    * @param $nombre
    * @param $estado
    * @param $start
    * @param $limit
    * 
    * @return json con resultado
    *
    * @author Allan Suárez <arsuarez@telconet.ec>
    * @version 1.0 05-03-2015
    *         
    */
    public function generarJson($nombre, $estado, $start, $limit)
    {
        $arr_encontrados = array();

        $registrosTotal = $this->getRegistros($nombre, $estado, '', '');
        $registros      = $this->getRegistros($nombre, $estado, $start, $limit);

        if($registros)
        {
            $num = count($registrosTotal);

            foreach($registros as $data)
            {
                $arr_encontrados[] = array( 'idPolicy'     => $data->getId(),
                                            'nombrePolicy' => $data->getNombrePolicy(),
                                            'leaseTime'    => $data->getLeaseTime(),
                                            'mascara'      => $data->getMascara(),
                                            'gateway'      => $data->getGateway(),
                                            'dnsName'      => $data->getDnsName(),
                                            'dnsServers'   => str_replace("|",", ",$data->getDnsServers()),
                                            'estado'       => trim($data->getEstado()),
                                            'action1'      => 'button-grid-show',
                                            'action2'      => (strtolower(trim($data->getEstado())) == strtolower('ELIMINADO') ?
                                                              'icon-invisible' : 'button-grid-delete'),
                );
            }

            if($num == 0)
            {
                $datos = array('total' => 1, 'encontrados' => array('idPolicy' => 0, 'nombrePolicy' => 'Ninguno', 'estado' => 'Ninguno'));
                $resultado = json_encode($datos);
                return $resultado;
            }
            else
            {
                $datos = json_encode($arr_encontrados);
                $resultado = '{"total":"' . $num . '","encontrados":' . $datos . '}';
                return $resultado;
            }
        }
        else
        {
            $resultado = '{"total":"0","encontrados":[]}';
            return $resultado;
        }
    }

    /**
    * getRegistros
    *
    * Metodo encargado de obtener el array de la consulta de los policys ingresados
    * 
    * @param $nombre
    * @param $estado
    * @param $start
    * @param $limit
    * 
    * @return json con resultado
    *
    * @author Allan Suárez <arsuarez@telconet.ec>
    * @version 1.0 05-03-2015
    *         
    */
    public function getRegistros($nombre, $estado, $start, $limit)
    {
        $qb = $this->_em->createQueryBuilder();
        
        $qb->select('policy')
           ->from('schemaBundle:AdmiPolicy', 'policy');

        if($nombre != "")
        {
            $qb->where('LOWER(policy.nombrePolicy) like LOWER(?1)');
            $qb->setParameter(1, '%' . $nombre . '%');
        }

        if($estado != "Todos")
        {
            if($estado == "Activo")
            {
                $qb->andWhere("LOWER(policy.estado) not like LOWER('Eliminado')");
            }
            else
            {
                $qb->andWhere('LOWER(policy.estado) = LOWER(?2)');
                $qb->setParameter(2, $estado);
            }
        }      

        if($start != '')
        {
            $qb->setFirstResult($start);
        }
        if($limit != '')
        {
            $qb->setMaxResults($limit);
        }

        $query = $qb->getQuery();

        return $query->getResult();
    }  

}

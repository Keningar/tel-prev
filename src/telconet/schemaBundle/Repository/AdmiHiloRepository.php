<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AdmiHiloRepository extends EntityRepository
{
    /**
     * Funcion que sirve para generar un json de los objetos obtenidos
     * por medio del filtro
     * 
     * @version 1.0 24-02-2015
     * @author Francisco Adum <fadum@telconet.ec>
     * @param $numeroHilo   int
     * @param $colorHilo    String
     * @param $estado       String
     * @param $start        int
     * @param $limit        int
     * @param $tipoMedioId  int
     */
    public function generarJsonHilos($numeroHilo,$colorHilo,$estado,$start,$limit){
        $arr_encontrados = array();
                
        $result = $this->getHilos($colorHilo, $numeroHilo, $estado, $start, $limit);

        $encontrados = $result['registros'];
        $encontradosTotal = $result['total'];

        if ($encontrados) {
            
            $num = $encontradosTotal;
            
            foreach ($encontrados as $hilo)
            {
                $arr_encontrados[]=array('idHilo'       => $hilo->getId(),
                                         'numeroHilo'   => trim($hilo->getNumeroHilo()),
                                         'colorHilo'    => trim($hilo->getColorHilo()),
                                         'numeroColorHilo'=> trim($hilo->getNumeroHilo()).",".trim($hilo->getColorHilo()),
                                         'estado'       => (trim($hilo->getEstado())=='Eliminado' ? 'Eliminado':'Activo'),
                                         'action1'      => 'button-grid-show',
                                         'action2'      => (trim($hilo->getEstado())=='Eliminado' ? 'button-grid-invisible':'button-grid-edit'),
                                         'action3'      => (trim($hilo->getEstado())=='Eliminado' ? 'button-grid-invisible':'button-grid-delete'));
            }

            if($num == 0)
            {
                $resultado = array('total' => 1,
                                   'encontrados' => array('idHilo' => 0, 'numeroHilo' => 'Ninguno'));
                $resultado = json_encode($resultado);

                return $resultado;
            }
            else
            {
                $data = json_encode($arr_encontrados);
                $resultado = '{"total":"' . $num . '","encontrados":' . $data . '}';

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
     * Funcion que sirve para generar y ejecutar un sql de los parametros
     * obtenidos del filtro
     * 
     * @version 1.5 04-03-2015 - Se agrega condicional por tipoMedioId a la consulta base
     * @author Allan Suarez <arsuarez@telconet.ec>
     * 
     * @version 1.0 24-02-2015
     * @author Francisco Adum <fadum@telconet.ec>
     * @param $numeroHilo   int
     * @param $colorHilo    String
     * @param $estado       String
     * @param $start        int
     * @param $limit        int
     * @param $tipoMedioId        int
     */
    public function getHilos($color, $numero, $estado, $start, $limit)
    {
        $qb = $this->_em->createQueryBuilder();
        $qbC = $this->_em->createQueryBuilder();
        //query para obtener la informacion
        $qb->select('AdmiHilo')
            ->from('schemaBundle:AdmiHilo', 'AdmiHilo');

        //query para obtener la cantidad de registros
        $qbC->select('count(AdmiHilo.id)')
            ->from('schemaBundle:AdmiHilo', 'AdmiHilo');

        if($color != "")
        {
            $qb->where('UPPER(AdmiHilo.colorHilo) like ?1');
            $qb->setParameter(1, "%" . strtoupper($color) . "%");
            $qbC->where('UPPER(AdmiHilo.colorHilo) like ?1');
            $qbC->setParameter(1, "%" . strtoupper($color) . "%");
        }
        if($numero != "")
        {
            $qb->andWhere('AdmiHilo.numeroHilo = ?2');
            $qb->setParameter(2, $numero);
            $qbC->andWhere('AdmiHilo.numeroHilo = ?2');
            $qbC->setParameter(2, $numero);
        }
        if($estado != "Todos")
        {
            $qb->andWhere('UPPER(AdmiHilo.estado) = ?3');
            $qb->setParameter(3, strtoupper($estado));
            $qbC->andWhere('UPPER(AdmiHilo.estado) = ?3');
            $qbC->setParameter(3, strtoupper($estado));
        }                

        if($start != '')
            $qb->setFirstResult($start);
        if($limit != '')
            $qb->setMaxResults($limit);
        
        //total de objetos
        $total = $qbC->getQuery()->getSingleScalarResult();
        //obtener los objetos
        $query = $qb->getQuery();
        $datos = $query->getResult();
        $resultado['registros'] = $datos;
        $resultado['total'] = $total;

        return $resultado;
    }
    
    /**
     * Funcion que sirve para generar un json de los objetos hilos
     * por clase tipo medio y estado
     * 
     * @version 1.0 24-02-2015
     * @author Francisco Adum <fadum@telconet.ec>
     * @param $claseTipoMedio   int
     * @param $estado       String
     * @param $start        int
     * @param $limit        int
     */
    public function generarJsonHilosPorEstado($claseTipoMedio, $estado,$start,$limit){
        $arr_encontrados = array();
                
        $result = $this->getHilosPorEstado($claseTipoMedio,$estado, $start, $limit);

        $encontrados = $result['registros'];
        $encontradosTotal = $result['total'];

        if ($encontrados) {
            
            $num = $encontradosTotal;
            
            foreach ($encontrados as $hilo)
            {
                $arr_encontrados[]=array('idHilo'       => $hilo->getId(),
                                         'numeroColor'  => $hilo->getNumeroHilo().",".$hilo->getColorHilo()
                                        );
            }

            if($num == 0)
            {
                $resultado = array('total' => 1,
                                   'encontrados' => array('idHilo' => 0, 'numeroHilo' => 'Ninguno'));
                $resultado = json_encode($resultado);

                return $resultado;
            }
            else
            {
                $data = json_encode($arr_encontrados);
                $resultado = '{"total":"' . $num . '","encontrados":' . $data . '}';

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
     * Funcion que sirve para generar y ejecutar un sql de los parametros
     * obtenidos del request
     * 
     * @version 1.0 24-02-2015
     * @author Francisco Adum <fadum@telconet.ec>
     * @param $claseTipoMedio   int
     * @param $estado       String
     * @param $start        int
     * @param $limit        int
     */
    public function getHilosPorEstado($claseTipoMedio, $estado, $start, $limit)
    {
        $qb = $this->_em->createQueryBuilder();
        $qbC = $this->_em->createQueryBuilder();
        //query para obtener la informacion
        $qb->select('AdmiHilo')
            ->from('schemaBundle:AdmiHilo', 'AdmiHilo');

        //query para obtener la cantidad de registros
        $qbC->select('count(AdmiHilo.id)')
            ->from('schemaBundle:AdmiHilo', 'AdmiHilo');

        if($claseTipoMedio != "")
        {
            $qb->andWhere('AdmiHilo.claseTipoMedioId = ?1');
            $qb->setParameter(1, $claseTipoMedio);
            $qbC->andWhere('AdmiHilo.claseTipoMedioId = ?1');
            $qbC->setParameter(1, $claseTipoMedio);
        }
        
        if($estado != "Todos")
        {
            $qb->andWhere('UPPER(AdmiHilo.estado) = ?2');
            $qb->setParameter(2, strtoupper($estado));
            $qbC->andWhere('UPPER(AdmiHilo.estado) = ?2');
            $qbC->setParameter(2, strtoupper($estado));
        }
        $qb->orderBy('AdmiHilo.numeroHilo','ASC');

        if($start != '')
            $qb->setFirstResult($start);
        if($limit != '')
            $qb->setMaxResults($limit);
        
        //total de objetos
        $total = $qbC->getQuery()->getSingleScalarResult();
        //obtener los objetos
        $query = $qb->getQuery();
        $datos = $query->getResult();
        $resultado['registros'] = $datos;
        $resultado['total'] = $total;

        return $resultado;
    }
    
    /**
     * Funcion que sirve para generar un json de los hilos por buffer enviado
     * 
     * @version 1.0 04-03-2015
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @param $bufferId     int
     * @param $empresaId    String
     * @param $estado       String     
     */
    public function generarJsonHilosPorBuffer($bufferId,$empresaId,$estado){
        $arr_encontrados = array();
                
        $result = $this->getHilosPorBuffer($bufferId, $empresaId, $estado);
        
        if ($result) 
        {

            $num = count($result);
            
            foreach ($result as $objeto)
            {
                $arr_encontrados[]=array('idHilo'  => $objeto['id'],
                                         'hilo'    => $objeto['numeroHilo'].",".$objeto['colorHilo']);
            }
                   

            if($num == 0)
            {
                $result = array('total' => 1,
                                   'encontrados' => array('idBuffer' => 0, 'buffer' => 'Ninguno'));
                $resultado = json_encode($result);

                return $resultado;
            }
            else
            {
                $data = json_encode($arr_encontrados);
                $resultado = '{"total":"' . $num . '","encontrados":' . $data . '}';

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
     * Funcion que sirve para generar el array con el resultado de los hilos
     * 
     * @version 1.0 04-03-2015
     * @author Allan Suarez <arsuarez@telconet.ec>
     * 
     * Se agrega estado de buffer_hilo
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.1 02-05-2016
     * @param $bufferId     int
     * @param $empresaId    String
     * @param $estado       String   
     */
    function getHilosPorBuffer($bufferId, $empresaId, $estado)
    {
        $sql = "SELECT 
                hilo.id,
                hilo.numeroHilo,
                hilo.colorHilo
                FROM
                schemaBundle:AdmiHilo hilo,       
                schemaBundle:InfoBufferHilo bufferHilo
                WHERE
                hilo.id               = bufferHilo.hiloId and 
                bufferHilo.bufferId   = :buffer and
                hilo.estado           = :estado and
                bufferHilo.empresaCod = :empresa and
                bufferHilo.estado     = :estado
             ";
		  
          $query = $this->_em->createQuery($sql);
          
          $query->setParameter('buffer',$bufferId);
          $query->setParameter('empresa',$empresaId);
          $query->setParameter('estado',$estado);                    
          
          return $query->getResult();      
    }
}

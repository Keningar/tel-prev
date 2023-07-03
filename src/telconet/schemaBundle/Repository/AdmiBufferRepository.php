<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AdmiBufferRepository extends EntityRepository
{
    /**
     * Funcion que sirve para generar un json de los objetos obtenidos
     * por medio del filtro
     * 
     * @version 1.0 24-02-2015
     * @author Francisco Adum <fadum@telconet.ec>
     * @param $numeroBuffer   int
     * @param $colorBuffer    String
     * @param $estado       String
     * @param $start        int
     * @param $limit        int
     */
    public function generarJsonBufferes($numeroBuffer,$colorBuffer,$estado,$start,$limit){
        $arr_encontrados = array();
                
        $result = $this->getBufferes($numeroBuffer, $colorBuffer, $estado, $start, $limit);

        $encontrados = $result['registros'];
        $encontradosTotal = $result['total'];

        if ($encontrados) {
            
            $num = $encontradosTotal;
            
            foreach ($encontrados as $objeto)
            {
                $arr_encontrados[]=array('idBuffer'     => $objeto->getId(),
                                         'numeroBuffer' => trim($objeto->getNumeroBuffer()),
                                         'colorBuffer'  => trim($objeto->getColorBuffer()),
                                         'estado'       => (trim($objeto->getEstado())=='Eliminado' ? 'Eliminado':'Activo'),
                                         'action1'      => 'button-grid-show',
                                         'action2'      => (trim($objeto->getEstado())=='Eliminado' ? 'button-grid-invisible':'button-grid-edit'),
                                         'action3'      => (trim($objeto->getEstado())=='Eliminado' ? 'button-grid-invisible':'button-grid-delete'));
            }

            if($num == 0)
            {
                $resultado = array('total' => 1,
                                   'encontrados' => array('idBuffer' => 0, 'numeroBuffer' => 'Ninguno'));
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
     * @version 1.0 24-02-2015
     * @author Francisco Adum <fadum@telconet.ec>
     * @param $numeroBuffer   int
     * @param $colorBuffer    String
     * @param $estado       String
     * @param $start        int
     * @param $limit        int
     */
    public function getBufferes($numeroBuffer,$colorBuffer,$estado,$start,$limit){
        $qb = $this->_em->createQueryBuilder();
        $qbC = $this->_em->createQueryBuilder();
        //query para obtener la informacion
        $qb->select('AdmiBuffer')
            ->from('schemaBundle:AdmiBuffer', 'AdmiBuffer');

        //query para obtener la cantidad de registros
        $qbC->select('count(AdmiBuffer.id)')
            ->from('schemaBundle:AdmiBuffer', 'AdmiBuffer');

        if($colorBuffer != "")
        {
            $qb->where('UPPER(AdmiBuffer.colorBuffer) like ?1');
            $qb->setParameter(1, "%" . strtoupper($colorBuffer) . "%");
            $qbC->where('UPPER(AdmiBuffer.colorBuffer) like ?1');
            $qbC->setParameter(1, "%" . strtoupper($colorBuffer) . "%");
        }
        if($numeroBuffer != "")
        {
            $qb->andWhere('AdmiBuffer.numeroBuffer = ?2');
            $qb->setParameter(2, $numeroBuffer);
            $qbC->andWhere('AdmiBuffer.numeroBuffer = ?2');
            $qbC->setParameter(2, $numeroBuffer);
        }
        if($estado != "Todos")
        {
            $qb->andWhere('UPPER(AdmiBuffer.estado) = ?3');
            $qb->setParameter(3, strtoupper($estado));
            $qbC->andWhere('UPPER(AdmiBuffer.estado) = ?3');
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
     * Funcion que sirve para generar un json de los objetos obtenidos
     * para cargar grid de edicion de Buffer
     * 
     * @version 1.0 24-02-2015
     * @author Francisco Adum <fadum@telconet.ec>
     * @param $idBuffer     int
     * @param $empresaId    String
     * @param $estado       String
     * @param $start        int
     * @param $limit        int
     */
    public function generarJsonBuffersHilos($idBuffer,$empresaId,$estado,$start,$limit){
        $arr_encontrados = array();
                
        $result = $this->getBuffersHilos($idBuffer, $empresaId, $estado, $start, $limit);

        $encontrados = $result['registros'];
        $encontradosTotal = $result['total'];

        if ($encontrados) {
            
            $num = $encontradosTotal;
            
            foreach ($encontrados as $objeto)
            {
                $arr_encontrados[]=array('hiloId'           => $objeto['idHilo'],
                                         'hiloNumeroColor'  => $objeto['numeroHilo'].",".$objeto['colorHilo'],
                                         'bufferHiloId'     => $objeto['idBufferHilo']);
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
     * Funcion que sirve para generar y ejecutar un sql para obtener
     * la relacion de buffer con los hilos
     * 
     * @version 1.0 24-02-2015
     * @author Francisco Adum <fadum@telconet.ec>
     * @param $idBuffer     int
     * @param $empresaId    String
     * @param $estado       String
     * @param $start        int
     * @param $limit        int
     */
    public function getBuffersHilos($idBuffer,$empresaId,$estado,$start,$limit){
        $qb = $this->_em->createQueryBuilder();
        $qbC = $this->_em->createQueryBuilder();
        //query para obtener la informacion
        $qb->select('AdmiHilo.id idHilo,'
            . 'InfoBufferHilo.id idBufferHilo,'
            . 'AdmiHilo.numeroHilo,'
            . 'AdmiHilo.colorHilo')
           ->from('schemaBundle:AdmiBuffer',     'AdmiBuffer')
           ->from('schemaBundle:AdmiHilo',       'AdmiHilo')
           ->from('schemaBundle:InfoBufferHilo', 'InfoBufferHilo')
           ->where('AdmiBuffer = InfoBufferHilo.bufferId')
           ->andWhere('InfoBufferHilo.hiloId = AdmiHilo')
           ->andWhere('InfoBufferHilo.empresaCod = ?1')
           ->setParameter(1, $empresaId)
           ->andWhere('AdmiBuffer = ?2')
           ->setParameter(2, $idBuffer)
           ->andWhere('InfoBufferHilo.estado = ?3')
           ->setParameter(3, $estado);
        

        //query para obtener la cantidad de registros
        $qbC->select('count(AdmiHilo.id)')
            ->from('schemaBundle:AdmiBuffer',     'AdmiBuffer')
            ->from('schemaBundle:AdmiHilo',       'AdmiHilo')
            ->from('schemaBundle:InfoBufferHilo', 'InfoBufferHilo')
            ->where('AdmiBuffer = InfoBufferHilo.bufferId')
            ->andWhere('InfoBufferHilo.hiloId = AdmiHilo')
            ->andWhere('InfoBufferHilo.empresaCod = ?1')
            ->setParameter(1, $empresaId)
            ->andWhere('AdmiBuffer = ?2')
            ->setParameter(2, $idBuffer)
            ->andWhere('InfoBufferHilo.estado = ?3')
            ->setParameter(3, $estado);

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
     * Funcion que sirve para generar un json de la clase tipo medio
     * por medio de un id buffer
     * 
     * @version 1.0 24-02-2015
     * @author Francisco Adum <fadum@telconet.ec>
     * @param $idBuffer     int
     * @param $empresaId    String
     * @param $estado       String
     * @param $start        int
     * @param $limit        int
     */
    public function generarJsonClaseTipoMedioPorBuffer($idBuffer,$empresaId,$estado,$start,$limit){
        $arr_encontrados = array();
                
        $result = $this->getClaseTipoMedioPorBuffer($idBuffer, $empresaId, $estado, $start, $limit);

        $encontrados = $result['registros'];

        if ($encontrados) {
            
            $num = 1;
            $objeto = $encontrados[0];
            $arr_encontrados[]=array('idClaseTipoMedio'      => $objeto->getId(),
                                     'nombreClaseTipoMedio'  => $objeto->getNombreClaseTipoMedio());

            if($num == 0)
            {
                $resultado = array('total' => 1,
                                   'encontrados' => array('idClaseTipoMedio' => 0, 'nombreClaseTipoMedio' => 'Ninguno'));
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
     * Funcion que sirve para generar y ejecutar un sql para obtener
     * la clase tipo medio por id buffer
     * 
     * @version 1.0 24-02-2015
     * @author Francisco Adum <fadum@telconet.ec>
     * @param $idBuffer     int
     * @param $empresaId    String
     * @param $estado       String
     * @param $start        int
     * @param $limit        int
     */
    public function getClaseTipoMedioPorBuffer($idBuffer,$empresaId,$estado,$start,$limit){
        $qb = $this->_em->createQueryBuilder();
        $qbC = $this->_em->createQueryBuilder();
        //query para obtener la informacion
        $qb->select('AdmiClaseTipoMedio')
           ->from('schemaBundle:AdmiBuffer',         'AdmiBuffer')
           ->from('schemaBundle:AdmiHilo',           'AdmiHilo')
           ->from('schemaBundle:InfoBufferHilo',     'InfoBufferHilo')
           ->from('schemaBundle:AdmiClaseTipoMedio', 'AdmiClaseTipoMedio')
           ->where('AdmiBuffer = InfoBufferHilo.bufferId')
           ->andWhere('InfoBufferHilo.hiloId = AdmiHilo')
           ->andWhere('AdmiHilo.claseTipoMedioId = AdmiClaseTipoMedio')
           ->andWhere('InfoBufferHilo.empresaCod = ?1')
           ->setParameter(1, $empresaId)
           ->andWhere('AdmiBuffer = ?2')
           ->setParameter(2, $idBuffer)
           ->andWhere('InfoBufferHilo.estado = ?3')
           ->setParameter(3, $estado);
        

        //query para obtener la cantidad de registros
        $qbC->select('count(AdmiClaseTipoMedio.id)')
            ->from('schemaBundle:AdmiBuffer',     'AdmiBuffer')
            ->from('schemaBundle:AdmiHilo',       'AdmiHilo')
            ->from('schemaBundle:InfoBufferHilo', 'InfoBufferHilo')
            ->from('schemaBundle:AdmiClaseTipoMedio', 'AdmiClaseTipoMedio')
            ->where('AdmiBuffer = InfoBufferHilo.bufferId')
            ->andWhere('InfoBufferHilo.hiloId = AdmiHilo')
            ->andWhere('AdmiHilo.claseTipoMedioId = AdmiClaseTipoMedio')
            ->andWhere('InfoBufferHilo.empresaCod = ?1')
            ->setParameter(1, $empresaId)
            ->andWhere('AdmiBuffer = ?2')
            ->setParameter(2, $idBuffer)
            ->andWhere('InfoBufferHilo.estado = ?3')
            ->setParameter(3, $estado);

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
     * Funcion que sirve para generar un json de los buffers por tipoMedio enviado
     * 
     * @version 1.0 04-03-2015
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @param $idTipoMedio     int
     * @param $empresaId    String
     * @param $estado       String     
     */
    public function generarJsonBufferPorTipoMedio($idTipoMedio,$empresaId,$estado, $estadoBufferHilo){
        $arr_encontrados = array();
                
        $result = $this->getBufferPorTipoMedio($idTipoMedio, $empresaId, $estado, $estadoBufferHilo);
        
        if ($result) 
        {

            $num = count($result);
            
            foreach ($result as $objeto)
            {
                $arr_encontrados[]=array('idBuffer'  => $objeto['id'],
                                         'buffer'    => $objeto['numeroBuffer'].",".$objeto['colorBuffer']);
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
     * Funcion que sirve para generar el array con el resultado de los buffers
     * 
     * @version 1.0 04-03-2015
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @param $idTipoMedio     int
     * @param $empresaId    String
     * @param $estado       String   
     */
    function getBufferPorTipoMedio($idTipoMedio, $empresaId, $estado, $estadoBufferHilo)
    {
        $sql = "SELECT 
                distinct(buff.id) as id,
                buff.numeroBuffer,
                buff.colorBuffer                
                FROM
                schemaBundle:AdmiClaseTipoMedio clase,
                schemaBundle:AdmiHilo hilo,
                schemaBundle:AdmiBuffer buff,
                schemaBundle:InfoBufferHilo bufferHilo
                WHERE
                clase.id            = hilo.claseTipoMedioId and 
                bufferHilo.hiloId   = hilo.id and
                bufferHilo.bufferId = buff.id and
                clase.id            = :idClaseTipoMedio and
                bufferHilo.empresaCod = :empresa and 
                bufferHilo.estado     = :estadoBufferHilo and 
                buff.estado           = :estado";
		  
          $query = $this->_em->createQuery($sql);
          
          $query->setParameter('idClaseTipoMedio',$idTipoMedio);
          $query->setParameter('empresa',$empresaId);
          $query->setParameter('estado',$estado);
          $query->setParameter('estadoBufferHilo',$estadoBufferHilo);
          
          return $query->getResult();      
    }
   
}

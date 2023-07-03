<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

/**
 * VistaGatewaysRepository : Clase Repository encargada de la gestion con la base de datos
 *
 * El Repositorio se encarga de realizar las transacciones SQL mediante los metodos que el ORM provee
 * Este se conecta con el esquema DB_INFRAESTRUCTURA para realizar las consultas necesarias 
 *
 * @category   SchemaBundle/Repository    
 * @version    1.0
 * @link       https://telcos.telconet.ec/administracion/tecnico/crear_gateway/
 * @author     Allan Suárez Carvajal (arsuarez) 
 */

class VistaGatewaysRepository extends EntityRepository
{
     /**
     * Metodo que genera JSON con elementos obtenidos de la VISTA_GATEWAYS
     * @param string $nombre
     * @param string $estado
     * @param string $start
     * @param string $limit
     * @param string $marcaElemento
     * @return json con la data obtenida
     * @author arsuarez
     */
    public function generarJsonGateways($em_com,$nombre,$estado,$start,$limit,$marcaElemento,$codEmpresa)
    {
        $arr_encontrados = array();                
        
        $entidadesTotal = $this->getEntidades($nombre, $estado, '', '',$marcaElemento,$codEmpresa);                
        $entidades = $this->getEntidades($nombre, $estado, $start, $limit,$marcaElemento,$codEmpresa);
 
        if ($entidades) {
            
            $num = count($entidadesTotal);
            
            foreach ($entidades as $entidad)
            {
		$empresa = $em_com->find('schemaBundle:InfoEmpresaGrupo',$entidad->getEmpresaCod()); 
            
                $arr_encontrados[]=array('id_elemento' =>$entidad->getId(),
                                         'nombre_marca_elemento' =>trim($entidad->getNombreMarcaElemento()),
                                         'nombre_modelo_elemento' =>trim($entidad->getNombreModeloElemento()),
                                         'nombre_elemento' =>trim($entidad->getNombreElemento()),
                                         'ip' =>trim($entidad->getIp()),
                                         'nombre_usuario_acceso' =>trim($entidad->getNombreUsuarioAcceso()),
                                         'contrasena' =>trim($entidad->getContrasena()),
                                         'empresa'=> $empresa->getNombreEmpresa(),
                                         'estado' =>trim($entidad->getEstado()),
                                         'action1' => 'button-grid-show',
                                         'action2' => (trim($entidad->getEstado())=='Eliminado' ? 'button-grid-invisible':'button-grid-edit'),
                                         'action3' => (trim($entidad->getEstado())=='Eliminado' ? 'button-grid-invisible':'button-grid-delete'),
                                         'action4' => (trim($entidad->getEstado())=='Eliminado' ? 'button-grid-invisible':'button-grid-confirmarActivacion')
                                         );
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
    /**
     * Metodo que genera JSON con la marca de los Elementos tipo Gateway  
     * @param string $start
     * @param string $limit     
     * @return json con la data obtenida
     * @author arsuarez
     */
    public function generarJsonMarcaElemento($start,$limit){
	
	$arr_encontrados = array();
        
        $entidadesTotal = $this->getEntidades('', '');                
        $entidades = $this->getMarcaElementos($start, $limit);
 
        if ($entidades) {
            
            $num = count($entidadesTotal);
                      
            foreach ($entidades as $entidad)
            {
                $arr_encontrados[]=array('id_marca' =>$entidad->getId(),
                                         'nombre_marca_elemento' =>trim($entidad->getNombreMarcaElemento()));                                         
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
    /**
     * Metodo que genera JSON con las interfaces y detalle de interfaces de elemento seleccionado   
     * @param string $start
     * @param string $limit
     * @param string $idElemento
     * @return json con la data obtenida
     * @author arsuarez
     */    
     public function generarJsonInterfaceElementos($start,$limit,$idElemento){
	
	$arr_encontrados = array();
        
        $entidadesTotal = $this->getInterfaces('', '',$idElemento);                
        $entidades = $this->getInterfaces($start, $limit,$idElemento);
 
        if ($entidades) {
            
            $num = count($entidadesTotal);                              
            	  
            foreach ($entidades as $entidad)
            {
                $arr_encontrados[]=array('id_interface_elemento' =>$entidad['id_interface_elemento'],
                                         'id_detalle_interface' =>trim($entidad['id_detalle_interface']),
                                         'puertos' =>trim($entidad['puertos']),
                                         'estado_interfaz' =>trim($entidad['estado_interfaz']),
                                         'detalle_nombre' =>trim($entidad['detalleNombre']),
                                         'detalle_valor' =>trim($entidad['detalleValor']),                                                                                                                          
                                         'action2' => (trim($entidad['estado_interfaz'])=='Eliminado' ? 'button-grid-invisible':'button-grid-edit'),
                                         'action3' => (trim($entidad['estado_interfaz'])=='Eliminado' ? 'button-grid-invisible':'button-grid-delete')
                                         );                                        
            }
                $data=json_encode($arr_encontrados);
                
               // echo $data;
                                
                $resultado= '{"total":"'.$num.'","encontrados":'.$data.'}';

                return $resultado;
        }
        else
        {
            $resultado= '{"total":"0","encontrados":[]}';

            return $resultado;
        }	    
    
    }
    /**
     * Metodo que obtiene la Interfaces segun elemento seleccionado   
     * @param string $start
     * @param string $limit
     * @param string $idElemento
     * @return resultado de la consulta
     * @author arsuarez
     */
    public function getInterfaces($start, $limit, $idElemento)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('a.id as id_interface_elemento , a.estado as estado_interfaz, a.nombreInterfaceElemento as puertos,
		     b.id as id_detalle_interface , b.detalleNombre , b.detalleValor')
            ->from('schemaBundle:InfoDetalleInterface', 'b')
            ->from('schemaBundle:InfoInterfaceElemento', 'a')
            ->where('b.interfaceElementoId = a')
            ->andWhere('a.elementoId = ?1 ')
            ->andWhere("a.estado <> ?2 ")
            ->andWhere("b.detalleNombre <> ?3 ");

        $qb->setParameter(1, $idElemento);
        $qb->setParameter(2, 'Eliminado');
        $qb->setParameter(3, 'saldo');

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
    /**
     * Metodo que obtiene la Marca de los elementos tipo GATEWAY
     * @param string $start
     * @param string $limit     
     * @return resultado de la consulta
     * @author arsuarez
     */
    public function getMarcaElementos($start,$limit){
    
	$qb = $this->_em->createQueryBuilder();
	$qb->select('me')
	->from('schemaBundle:AdmiMarcaElemento','me')
	->from('schemaBundle:AdmiTipoElemento','te')
	->from('schemaBundle:AdmiModeloElemento','moe')
	->where('me = moe.marcaElementoId')
	->andWhere('te = moe.tipoElementoId')
	->andWhere("te.nombreTipoElemento = 'GATEWAY' ");
	
	 if($start!='')
            $qb->setFirstResult($start);   
        if($limit!='')
            $qb->setMaxResults($limit);
    
	$query = $qb->getQuery();
	
        return $query->getResult();        
    
    }
       /**
     * Metodo que obtiene la VISTA_GATEWAYS y sus registros
     * @param string $start
     * @param string $limit   
     * @param string $estado
     * @param string $nombre   
     * @param string $marcaElemento   
     * @return resultado de la consulta
     * @author arsuarez
     */
    public function getEntidades($nombre,$estado,$start,$limit,$marcaElemento,$codEmpresa){
    
   
        $qb = $this->_em->createQueryBuilder();
            $qb->select('e')
               ->from('schemaBundle:VistaGateways','e');                       
               
        if($nombre!=""){
            $qb ->where( "lower(e.nombreElemento) like lower('%". $nombre ."%') ");           
        }
		
        if($estado!="Todos"){
            if($estado=="Activo"){
                $qb ->andWhere("lower(e.estado) not like lower('Eliminado')");
            }
            else{
                $qb ->andWhere('lower(e.estado) = lower(?2)');
                $qb->setParameter(2, $estado);
            }
        }
        
        if($marcaElemento!=''){
	     $qb ->andWhere("lower(e.nombreMarcaElemento) = lower('$marcaElemento')");	   
	    
        }
        
        if($codEmpresa!="Todos"){
	       $qb ->andWhere("e.empresaCod = '$codEmpresa'");	   
        
        }
		
        if($start!='')
            $qb->setFirstResult($start);   
        if($limit!='')
            $qb->setMaxResults($limit);              
		
        $query = $qb->getQuery();                
      
        return $query->getResult();
    }
    
    /**
    * generarJsonSaldoInterface
    *
    * Metodo encargado obtener el resultado del query de obtencion de saldos por cada interfaz
    * y lo devueve como json para ser mostrado en el grid respectivo o el array utilizado para generar
    * el reporte respectivo     
    * 
    * @param $intElementoId                                 
    * @param $start
    * @param $limit
    * @param $retornaJson
    *
    * @return json
    *
    * @author Allan Suárez <arsuarez@telconet.ec>
    * @version 1.0 10-12-2014
    */
    public function generarJsonSaldoInterface($intElementoId, $start, $limit,$retornaJson=true)
    {
        $arr_encontrados = array();

        $query = $this->getSaldosInterface($intElementoId);
        $total = count($query->getArrayResult());

        if($limit > 0)
        {
            $query->setSQL('SELECT a.*, rownum AS doctrine_rownum FROM (' . $query->getSQL() . ') a WHERE rownum <= :doctrine_limit');
            $query->setParameter('doctrine_limit', $limit + $start);
            if($start > 0)
            {
                $query->setSQL('SELECT * FROM (' . $query->getSQL() . ') WHERE doctrine_rownum >= :doctrine_start');
                $query->setParameter('doctrine_start', $start + 1);
            }
        }

        $resultado = $query->getArrayResult();

        if($resultado)
        {
            $tmpIdElemento='';

            foreach($resultado as $entidad)
            {
                if($tmpIdElemento!=$entidad['id_elemento'])
                {
                    $tmpIdElemento = $entidad['id_elemento'];
                    
                    $arr_encontrados[] = array( 'id_elemento' => $entidad['id_elemento'],
                                                'puerto' => "",
                                                'ip' => trim($entidad['ip']),
                                                'numero' => "",
                                                'operadora' => "",
                                                'saldo' => "",
                                                'action1' => 'button-grid-cambioCpe');
                    
                    $arr_encontrados[] = array( 'id_elemento' => "",
                                                'puerto' => trim($entidad['puerto']),
                                                'ip' => "",
                                                'numero' => trim($entidad['numero']),
                                                'operadora' => trim($entidad['operadora']),
                                                'saldo' => trim($entidad['saldo']),
                                                'action1' => "button-grid-invisible");
                }
                else
                {
                    $arr_encontrados[] = array( 'id_elemento' => "",
                                                'puerto' => trim($entidad['puerto']),
                                                'ip' => "",
                                                'numero' => trim($entidad['numero']),
                                                'operadora' => trim($entidad['operadora']),
                                                'saldo' => trim($entidad['saldo']),
                                                'action1' => "button-grid-invisible",
                                            );
                }
            }
            $data = json_encode($arr_encontrados);

            $resultado = '{"total":"' . $total . '","encontrados":' . $data . '}';

            if($retornaJson)
            {                
                return $resultado;
            }
            else
            {
                return $arr_encontrados;
            }
        }
        else
        {
            $resultado = '{"total":"0","encontrados":[]}';

            if($retornaJson)
            {
                return $resultado;
            }
            else
            {
                return $arr_encontrados;
            }
        }
    }

    /**
    * getSaldosInterface
    *
    * Metodo encargado de devolver el query formado por el NativeQuery con los parametros enviados  
    *
    * @param $intElementoId                                      
    *
    * @return sql
    *
    * @author Allan Suárez <arsuarez@telconet.ec>
    *
    * @version 1.0 10-12-2014
    */
    public function getSaldosInterface($intElementoId)
    {
        $rsm = new ResultSetMappingBuilder($this->_em);
        $query = $this->_em->createNativeQuery(null, $rsm);

        $where = "";

        if($intElementoId != 'Todos')
        {
            $where = " AND info_elemento.ID_ELEMENTO = :elemento";
            $query->setParameter('elemento', $intElementoId);
        }

        $sql = "SELECT 
                detalle.ELEMENTO_ID               AS ID_ELEMENTO,
                ip.ip                             AS IP,
                detalle.NOMBRE_INTERFACE_ELEMENTO AS PUERTO,
                detalle1.DETALLE_VALOR            AS NUMERO,
                CASE detalle1.DETALLE_NOMBRE
                  WHEN
                    'module1' then 'CLARO'
                    else 'MOVISTAR' 
                END AS OPERADORA,
                detalle.DETALLE_VALOR             AS SALDO  
              FROM
                (SELECT detalle.ID_DETALLE_INTERFACE,
                  interfaze.NOMBRE_INTERFACE_ELEMENTO,
                  detalle.REF_DETALLE_INTERFACE_ID,
                  detalle.DETALLE_VALOR,
                  interfaze.ELEMENTO_ID,
                  detalle.FE_CREACION
                FROM INFO_DETALLE_INTERFACE detalle,
                  INFO_INTERFACE_ELEMENTO interfaze
                WHERE detalle.INTERFACE_ELEMENTO_ID = interfaze.ID_INTERFACE_ELEMENTO
                AND detalle.DETALLE_NOMBRE          = :saldo
                AND interfaze.ELEMENTO_ID          IN
                  (SELECT ID_ELEMENTO
                  FROM info_elemento
                  WHERE MODELO_ELEMENTO_ID IN
                    (SELECT id_modelo_elemento
                    FROM admi_modelo_elemento
                    WHERE MARCA_ELEMENTO_ID =
                      (SELECT id_marca_elemento
                      FROM admi_marca_elemento
                      WHERE nombre_marca_elemento = :marca
                      )
                    )
                  $where
                  )
                ) detalle ,
                INFO_DETALLE_INTERFACE detalle1,
                INFO_IP ip
              WHERE detalle.REF_DETALLE_INTERFACE_ID = detalle1.ID_DETALLE_INTERFACE
              AND detalle.elemento_id                = ip.elemento_id  
              ORDER BY ip.ip,detalle.NOMBRE_INTERFACE_ELEMENTO,detalle.DETALLE_VALOR desc";

        $rsm->addScalarResult('ID_ELEMENTO', 'id_elemento', 'integer');
        $rsm->addScalarResult('IP', 'ip', 'string');
        $rsm->addScalarResult('PUERTO', 'puerto', 'string');
        $rsm->addScalarResult('OPERADORA', 'operadora', 'string');
        $rsm->addScalarResult('NUMERO', 'numero', 'string');
        $rsm->addScalarResult('SALDO', 'saldo', 'string');

        $query->setParameter('marca', 'PORTECH');
        $query->setParameter('saldo', 'saldo');

        $query->setSQL($sql);

        return $query;
    }

}
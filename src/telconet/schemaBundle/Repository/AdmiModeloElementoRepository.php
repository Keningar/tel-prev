<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class AdmiModeloElementoRepository extends EntityRepository
{
    public function generarJsonModelosElementos($nombre,$marca,$tipoElemento,$estado,$start,$limit){
        $arr_encontrados = array();
        
        $modelosElementosTotal = $this->getModelosElementos($nombre,$marca,$tipoElemento,$estado,'','');
        
        $modelosElementos = $this->getModelosElementos($nombre,$marca,$tipoElemento,$estado,$start,$limit);
//        error_log('entra');
        if ($modelosElementos) {
            
            $num = count($modelosElementosTotal);
            
            foreach ($modelosElementos as $modeloElemento)
            {
                $arr_encontrados[]=array('idModeloElemento' =>$modeloElemento->getId(),
                                         'nombreModeloElemento' =>trim($modeloElemento->getNombreModeloElemento()),
                                         'marcaElemento' =>trim($modeloElemento->getMarcaElementoId()->getNombreMarcaElemento()),
                                         'tipoElemento' =>trim($modeloElemento->getTipoElementoId()->getNombreTipoElemento()),
                                         'estado' =>(trim($modeloElemento->getEstado())=='Eliminado' ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (trim($modeloElemento->getEstado())=='Eliminado' ? 'button-grid-invisible':'button-grid-edit'),
                                         'action3' => (trim($modeloElemento->getEstado())=='Eliminado' ? 'button-grid-invisible':'button-grid-delete'));
            }

            if($num == 0)
            {
               $resultado= array('total' => 1 ,
                                 'encontrados' => array('idConectorInterface' => 0 , 'nombreConectorInterface' => 'Ninguno','idConectorInterface' => 0 , 'nombreConectorInterface' => 'Ninguno', 'estado' => 'Ninguno'));
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
   
    public function getModelosElementos($nombre,$marca,$tipoElemento,$estado,$start,$limit){
        $qb = $this->_em->createQueryBuilder();
            $qb->select('e')
               ->from('schemaBundle:AdmiModeloElemento','e');
               
        if($nombre!=""){
            $qb ->where( 'e.nombreModeloElemento like ?1');
            $qb->setParameter(1, '%'.$nombre.'%');
        }
        if($marca!=''){
            $qb ->andWhere( 'e.marcaElementoId =?3');
            $qb->setParameter(3, $marca);
        }
        if($tipoElemento!=''){
            $qb ->andWhere( 'e.tipoElementoId =?4');
            $qb->setParameter(4, $tipoElemento);
        }
        if($estado!="Todos"){
            $qb ->andWhere('e.estado = ?2');
            $qb->setParameter(2, $estado);
        }
        if($start!='')
            $qb->setFirstResult($start);   
        if($limit!='')
            $qb->setMaxResults($limit);
        
        $query = $qb->getQuery();                
        
        return $query->getResult();
    }
    
    
    /**
     * Documentación para el método 'findModeloElementoPorCriterios'.
     * Obtiene información de un modelo elemento según criterios enviados por parametros
     * @param array  $arrayParametros
     * [         
     *     strNombre       => nombre de elemento    
     *     strMarca        => marca de elemento
     *     strTipoElemento => tipo de elemento   
     *     strEstado       => estado de elemento
     * ]
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 18-01-2017
     * @return Object $objModeloElemento AdmiModeloElemento
     */
    public function findModeloElementoPorCriterios($arrayParametros)
    {
        
        $objModeloElemento    = null;
        $arrayModelosElemento = $this->getModelosElementos($arrayParametros['strNombre'],
                                                           $arrayParametros['strMarca'],
                                                           $arrayParametros['strTipoElemento'],
                                                           $arrayParametros['strEstado'],
                                                           0,
                                                           1);
        if (isset($arrayModelosElemento) && !empty($arrayModelosElemento))
        {
            $objModeloElemento = $arrayModelosElemento[0];
        }
        return $objModeloElemento;
    }
    

    
    
    /**
     * Funcion que genera un json para mostrar
     * los modelos de los elementos por tipo de elemento
     * CPE
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-05-2014
     * @param String    $nombre         nombre del modelo
     * @param int       $marca          id de la marca modelo
     * @param String    $tipoElemento   nombre del tipo elemento
     * @param String    $estado         estado de los modelos a consultar
     * @param int       $start          numero de inicio para el limit
     * @param int       $limit          numero de fin para el limit
     */
    public function generarJsonModelosElementosCpe($nombre,$marca,$tipoElemento,$estado,$start,$limit)
    {
        $arr_encontrados = array();
        
        $modelosElementosTotal = $this->getModelosElementosCpe($nombre,$marca,$tipoElemento,$estado,'','');
        
        $modelosElementos = $this->getModelosElementosCpe($nombre,$marca,$tipoElemento,$estado,$start,$limit);
//        error_log('entra');
        if ($modelosElementos) 
        {
            
            $num = count($modelosElementosTotal);
            
            foreach ($modelosElementos as $modeloElemento)
            {
                $arr_encontrados[]=array('idModeloElemento' =>$modeloElemento->getId(),
                                         'nombreModeloElemento' =>trim($modeloElemento->getNombreModeloElemento()),
                                         'marcaElemento' =>trim($modeloElemento->getMarcaElementoId()->getNombreMarcaElemento()),
                                         'tipoElemento' =>trim($modeloElemento->getTipoElementoId()->getNombreTipoElemento()),
                                         'estado' =>(trim($modeloElemento->getEstado())=='Eliminado' ? 'Eliminado':'Activo'));
            }

            if($num == 0)
            {
               $resultado= array('total' => 1 ,
                                 'encontrados' => array('idConectorInterface' => 0 , 
                                                        'nombreConectorInterface' => 'Ninguno',
                                                        'idConectorInterface' => 0 , 
                                                        'nombreConectorInterface' => 'Ninguno', 
                                                        'estado' => 'Ninguno'));
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
   
    /**
     * Funcion que arma y ejecuta el query para
     * obtener los modelos de los cpes (CPE, CPE WIFI, CPE ONT)
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-05-2014
     * 
     * @author Modificado: Duval Medina C. <dmedina@telconet.ec>
     * @version 1.1 2016-06-23 - Ordenar la consulta por nombre
     * 
     * @param String    $nombre         nombre del modelo
     * @param int       $marca          id de la marca
     * @param String    $tipoElemento   tipo elemento
     * @param String    $estado         estado de la marca
     * @param int       $start          numero de inicio para el limit
     * @param int       $limit          numero de fin para el limit
     */
    public function getModelosElementosCpe($nombre,$marca,$tipoElemento,$estado,$start,$limit)
    {
        $qb = $this->_em->createQueryBuilder();
            $qb->select('e')
               ->from('schemaBundle:AdmiModeloElemento','e')
               ->from('schemaBundle:AdmiTipoElemento','admiTipoElemento');
               
        if($nombre!="")
        {
            $qb ->where( 'e.nombreModeloElemento like ?1');
            $qb->setParameter(1, '%'.$nombre.'%');
        }
        if($marca!='')
        {
            $qb ->andWhere( 'e.marcaElementoId =?3');
            $qb->setParameter(3, $marca);
        }
        if($tipoElemento!='')
        {
            $qb->andWhere( 'e.tipoElementoId = admiTipoElemento')
               ->andWhere( 'admiTipoElemento.nombreTipoElemento like ?4');
            $qb->setParameter(4, $tipoElemento."%");
        }
        if($estado!="Todos")
        {
            $qb ->andWhere('e.estado = ?2');
            $qb->setParameter(2, $estado);
        }
        $qb->orderBy('e.nombreModeloElemento', 'ASC');
        if($start!='')
        {
            $qb->setFirstResult($start);
        }
        if($limit!='')
        {
            $qb->setMaxResults($limit);
        }
        
        $query = $qb->getQuery();                
        return $query->getResult();
    }
    
    public function generarJsonModelosElementosParaTarea($nombre,$marca,$tipoElemento,$estado,$start,$limit){
        $arr_encontrados = array();
        
        $modelosElementosTotal = $this->getModelosElementos($nombre,$marca,$tipoElemento,$estado,'','');
        
        $modelosElementos = $this->getModelosElementos($nombre,$marca,$tipoElemento,$estado,$start,$limit);
//        error_log('entra');
        if ($modelosElementos) {
            
            $num = count($modelosElementosTotal);
            
            foreach ($modelosElementos as $modeloElemento)
            {
                $arr_encontrados[]=array('id' =>$modeloElemento->getId(),
                                         'nombre' =>trim($modeloElemento->getNombreModeloElemento()));
            }

            if($num == 0)
            {
               $resultado= array('total' => 1 ,
                                 'encontrados' => array('idConectorInterface' => 0 , 'nombreConectorInterface' => 'Ninguno','idConectorInterface' => 0 , 'nombreConectorInterface' => 'Ninguno', 'estado' => 'Ninguno'));
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
    
    public function generarJsonDocumentoPorModelo($nombreAccion, $modeloElemento, $emSoporte, $emComunicacion, $emSeguridad, $em){
        $arr_encontrados = array();
//        $em = $this->getManager('telconet_infraestructura');
        
//        print($nombreAccion);
//        die(); 
        $accion = null;
        $accion = $emSeguridad->getRepository('schemaBundle:SistAccion')->findOneBy(array( "nombreAccion" => $nombreAccion, "estado"=>"Activo"));
        
//        print($accion->getId());
//        die();
        
        if ($accion)
        {
        $seguRelacionSistema = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->findOneBy(array( "accionId" => $accion->getId()));
        }
//        print($seguRelacionSistema[0]->getTareaInterfaceModeloTrId());
//        die();
        
        if($seguRelacionSistema!=null){
            $tareaInterfaceModeloTramo = $emSoporte->getRepository('schemaBundle:AdmiTareaInterfaceModeloTr')->find($seguRelacionSistema->getTareaInterfaceModeloTrId());
//            print_r($tareaInterfaceModeloTramo->getTareaId()->getId()); die();
//            $idTarea = $tareaInterfaceModeloTramo->getTareaId();
            $modeloUsuario = $em->getRepository('schemaBundle:AdmiModeloUsuarioAcceso')->findOneBy(array( "modeloElementoId" => $modeloElemento, "esPreferencia"=>"SI"));
            $usuario = $em->find('schemaBundle:AdmiUsuarioAcceso', $modeloUsuario->getUsuarioAccesoId());
            $modeloProtocolo = $em->getRepository('schemaBundle:AdmiModeloProtocolo')->findOneBy(array( "modeloElementoId" => $modeloElemento, "esPreferido"=>"SI"));
            $protocolo = $em->find('schemaBundle:AdmiProtocolo', $modeloProtocolo->getProtocoloId());
//            print($protocolo->getNombreProtocolo()); die();

//            $tareaInterfaceModeloTramoPepa = $emSoporte->getRepository('schemaBundle:AdmiTareaInterfaceModeloTr')->findOneBy(array( "tareaId" => $idTarea));
//            $idTareaInterfaceModeloTramo = $tareaInterfaceModeloTramoPepa->getId();

            $documento = $emComunicacion->getRepository('schemaBundle:InfoDocumento')->findOneBy(array( "tareaInterfaceModeloTraId" => $tareaInterfaceModeloTramo->getId()));

            $idDocumento      = $documento == null ? "0" : $documento->getId();
            $script           = $documento == null ? "0" : $documento->getMensaje();
            $nombreUsuario    = $usuario->getNombreUsuarioAcceso();
            $nombreProtocolo  = $protocolo->getNombreProtocolo();
        }
        else{
            $idDocumento = "0" ;
            $script ="0";
            $nombreUsuario = "0";
            $nombreProtocolo = "0";
        }
        
        $arr_encontrados[]=array('idDocumento'=>$idDocumento,
                                 'script' => $script,
                                 'usuario' => $nombreUsuario,
                                 'protocolo'=>$nombreProtocolo
                                );
        
        $num = count($arr_encontrados);
        
        $data=json_encode($arr_encontrados);
        if($num>0)
            $resultado= '{"total":"1","encontrados":'.$data.'}';
        else
            $resultado= '{"total":"0","encontrados":[]}';
        
        return $resultado;
        
        
    }
    
    public function generarJsonDocumentoPorModeloRespaldo($nombreTarea, $modeloElemento, $emSoporte, $emComunicacion){
        $arr_encontrados = array();
        $em = $this->getManager('telconet_infraestructura');
        
        $tareaInterfaceModeloTramo = $emSoporte->getRepository('schemaBundle:AdmiTareaInterfaceModeloTr')->findBy(array( "modeloElementoId" => $modeloElemento));

        foreach($tareaInterfaceModeloTramo as $timt){
            $tarea = $timt->getTareaId();
            if(trim($tarea->getNombreTarea()) == trim($nombreTarea)){
                $idTarea = $tarea->getId();
                break;
            }
        }

        $modeloUsuario = $em->getRepository('schemaBundle:AdmiModeloUsuarioAcceso')->findBy(array( "modeloElementoId" => $modeloElemento, "esPreferencia"=>"SI"));
        $usuario = $em->find('schemaBundle:AdmiUsuarioAcceso', $modeloUsuario[0]->getUsuarioAccesoId());
        $modeloProtocolo = $em->getRepository('schemaBundle:AdmiModeloProtocolo')->findBy(array( "modeloElementoId" => $modeloElemento, "esPreferido"=>"SI"));
        $protocolo = $em->find('schemaBundle:AdmiProtocolo', $modeloProtocolo[0]->getProtocoloId());
        
        $tareaInterfaceModeloTramoPepa = $emSoporte->getRepository('schemaBundle:AdmiTareaInterfaceModeloTr')->findBy(array( "tareaId" => $idTarea));
        $idTareaInterfaceModeloTramo = $tareaInterfaceModeloTramoPepa[0]->getId();
        
        $documento = $emComunicacion->getRepository('schemaBundle:InfoDocumento')->findBy(array( "tareaInterfaceModeloTraId" => $idTareaInterfaceModeloTramo));
        
        $arr_encontrados[]=array('idDocumento'=>$documento[0]->getId(),
                                 'script' => $documento[0]->getMensaje(),
                                 'usuario' => $usuario->getNombreUsuarioAcceso(),
                                 'protocolo'=>$protocolo->getNombreProtocolo()
                                );
        
        $num = count($arr_encontrados);
        
        $data=json_encode($arr_encontrados);
        if($num>0)
            $resultado= '{"total":"1","encontrados":'.$data.'}';
        else
            $resultado= '{"total":"0","encontrados":[]}';
        
        return $resultado;
        
        
    }
    
     /**
      * generarJsonModeloElementosPorTipoEmpresa
      *
      * Método que devuelve el json con los modelos de elementos segun el tipo, la empresa y el estado
      *
      * @param string $nombre         
      * @param string $empresa         
      * @param integer $tipoElemento         
      * @param string $estado         
      *
      * @return JSON con valores a mostrar 
      *
      * @author Allan Suárez <arsuarez@telconet.ec>
      * @version 1.0 03-07-2014
      */
     public function generarJsonModeloElementosPorTipoEmpresa($nombre, $empresa , $tipoElemento , $estado)
     {    	  
	  $sql = "SELECT
		  distinct(a.id),
		  a.nombreModeloElemento
		  FROM
		  schemaBundle:AdmiModeloElemento a,
		  schemaBundle:InfoEmpresaElemento b,
		  schemaBundle:InfoElemento c		 		  
		  WHERE
		  a.id = c.modeloElementoId and
		  b.elementoId = c.id and
		  b.empresaCod = :empresa and		  
		  a.tipoElementoId = :tipoElemento and
		  a.estado = :estado
		  ";			  	  
	  
	  $query = $this->_em->createQuery();	 
	  $query->setParameter('empresa',$empresa);
	  $query->setParameter('tipoElemento',$tipoElemento);
	  $query->setParameter('estado',$estado);
	  
	  if($nombre && $nombre != '')
	  {
		$sql .= "and upper(a.nombreModeloElemento) like upper(:nombre)";
		$query->setParameter('nombre','%'.$nombre.'%');		
	  }
	  
	  $query->setDQL($sql);
	  $registros = $query->getResult();
    
	    if ($registros) {				
		
		foreach ($registros as $data)
		{                    			
			$arr_encontrados[]=array(
			
				'idModeloElemento' =>$data['id'],
				'nombreModeloElemento' => $data['nombreModeloElemento']				
			      );
		}
		
		$data = json_encode($arr_encontrados);
		$resultado= '{"encontrados":'.$data.',"success":"true"}';

		return $resultado;
		
	    }
	    else
	    {
		$resultado= '{"encontrados":[],"success":"false"}';

		return $resultado;
	    }
    
    
    }
    
    
    /**
      * generarJsonElementosPorModelo
      *
      * Método que devuelve el json con los elementos segun el modelo y tipo
      *
      * @param string $nombre         
      * @param integer $modelo         
      * @param integer $tipoElemento         
      * @param string $estado         
      *
      * @return JSON con valores a mostrar 
      *
      * @author Allan Suárez <arsuarez@telconet.ec>
      * @version 1.0 03-07-2014
      */
     public function generarJsonElementosPorModelo($nombre,$estado,$tipoElemento,$modelo)
     {    	  
	  $sql = "SELECT
		  distinct(a.id),
		  a.nombreElemento
		  FROM
		  schemaBundle:InfoElemento a,		  
		  schemaBundle:AdmiModeloElemento b		  
		  WHERE
		  a.modeloElementoId = b.id and
		  b.tipoElementoId = :tipoElemento and
		  b.id = :modeloElemento and		  		  
		  a.estado = :estado
		  ";			  	  
	  
	  $query = $this->_em->createQuery();	 
	  
	  $query->setParameter('tipoElemento',$tipoElemento);
	  $query->setParameter('modeloElemento',$modelo);
	  $query->setParameter('estado',$estado);	
	  
	  if($nombre && $nombre != '')
	  {
		$sql .= "and upper(a.nombreElemento) like upper(:nombre)";
		$query->setParameter('nombre','%'.$nombre.'%');		
	  }
	  
	  $query->setDQL($sql);
	  $registros = $query->getResult();
    
	  if ($registros) 
	  {				
		
		foreach ($registros as $data)
		{                    			
			$arr_encontrados[]=array(
			
				'idElemento' =>$data['id'],
				'nombreElemento' => $data['nombreElemento']				
			      );
		}
		
		$data = json_encode($arr_encontrados);
		$resultado= '{"encontrados":'.$data.',"success":"true"}';

		return $resultado;
		
	    }
	    else
	    {
		$resultado= '{"encontrados":[],"success":"false"}';

		return $resultado;
	    }
    
    
    }
    
    /**
     * Funcion que genera un json con los elementos filtrados por modelo
     * 
     * @param $nombreElemento   string
     * @param $modeloElementoId int
     * @param $empresa          string
     * @param $estado           string
     * @param $start            int
     * @param $limit            int
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 11-02-2015
     */
    public function generarJsonElementosPorModeloElemento($nombreElemento, $modeloElementoId, $empresa, $estado, $start, $limit)
    {
        $arr_encontrados = array();

        $result = $this->getElementosPorModelo($nombreElemento, $modeloElementoId, $estado, $empresa, $start, $limit);
        $encontrados = $result['registros'];
        $encontradosTotal = $result['total'];

        if($encontrados)
        {

            $num = $encontradosTotal;
            foreach($encontrados as $entity)
            {
                $arr_encontrados[] = array( 'idElemento'     => $entity->getId(),
                                            'nombreElemento' => $entity->getNombreElemento()
                );
            }

            if($num == 0)
            {
                $resultado = array('total' => 1,
                    'encontrados' => array('idElemento' => 0, 'nombreElemento' => 'Ninguno'));
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
            $resultado = '{"total":"0","encontrados":[]}';

            return $resultado;
        }
    }

    /**
     * Funcion que genera y ejecuta un sql por los filtros de modelo, nombre del elemento,
     * empresa y estado del elemento
     * 
     * @param $nombreElemento   string
     * @param $modeloElementoId int
     * @param $estado           string
     * @param $empresa          string
     * @param $start            int
     * @param $limit            int
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 11-02-2015
     */
    public function getElementosPorModelo($nombreElemento, $modeloElementoId, $estado, $empresa, $start, $limit)
    {
        $qb  = $this->_em->createQueryBuilder();
        $qbC = $this->_em->createQueryBuilder();
        
        $qb->select('e')
            ->from('schemaBundle:VistaElementos', 'e');
        $qb->where("e.empresaCod = ?1")
           ->setParameter(1, $empresa);
        $qb->andWhere("e.estadoElemento = 'Activo'");
        
        $qbC->select('count(e.id)')
            ->from('schemaBundle:VistaElementos', 'e');
        $qbC->where("e.empresaCod = ?1")
           ->setParameter(1, $empresa);
        $qbC->andWhere("e.estadoElemento = 'Activo'");

        if($modeloElementoId != "")
        {
            $qb->andWhere('e.idModeloElemento = ?2');
            $qb->setParameter(2, $modeloElementoId);
            
            $qbC->andWhere('e.idModeloElemento = ?2');
            $qbC->setParameter(2, $modeloElementoId);
        }
        if($estado != "Todos")
        {
            $qb->andWhere('e.estadoElemento = ?3');
            $qb->setParameter(3, $estado);
            
            $qbC->andWhere('e.estadoElemento = ?3');
            $qbC->setParameter(3, $estado);
        }
        if($nombreElemento != "")
        {
            $qb->andWhere('UPPER(e.nombreElemento) like ?4');
            $qb->setParameter(4, "%" . strtoupper($nombreElemento) . "%");
            
            $qbC->andWhere('UPPER(e.nombreElemento) like ?4');
            $qbC->setParameter(4, "%" . strtoupper($nombreElemento) . "%");
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
     * getModeloElementosByCriterios
     *
     * Método que retornará los modelos dependiendo del tipo de elemento que se desea buscar                                  
     *
     * @param array $arrayParametros ['estadoActivo', 'tipoElemento', 'inicio', 'limite']
     * 
     * @return array $arrayResultados ['registros', 'total']
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 09-11-2015
     */
    public function getModeloElementosByCriterios( $arrayParametros )
    {
        $arrayResultados = array();
        
        $strSelect      = "SELECT ame ";
        $strSelectCount = "SELECT COUNT(ame.id) ";
        $strFrom        = "FROM schemaBundle:AdmiModeloElemento ame,
                                schemaBundle:AdmiTipoElemento ate ";
        $strWhere       = "WHERE ame.tipoElementoId = ate.id
                             AND ame.estado = :estadoActivo
                             AND ate.estado = :estadoActivo
                             AND ate.nombreTipoElemento IN (:tipoElemento) ";

        $strSql      = $strSelect.$strFrom.$strWhere;
        $strSqlCount = $strSelectCount.$strFrom.$strWhere;

        $query = $this->_em->createQuery($strSql);	
        $query->setParameter("estadoActivo", $arrayParametros['estadoActivo'] );
        $query->setParameter("tipoElemento", array_values($arrayParametros['tipoElemento']) );    
        
        $queryCount = $this->_em->createQuery($strSqlCount);	
        $queryCount->setParameter("estadoActivo", $arrayParametros['estadoActivo'] );
        $queryCount->setParameter("tipoElemento", $arrayParametros['tipoElemento'] );
        
        
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
     * getJsonModeloElementosByCriterios
     *
     * Método que retornará los modelos dependiendo del tipo de elemento que se desea buscar                                  
     *
     * @param array $arrayParametros ['estadoActivo', 'tipoElemento', 'inicio', 'limite']
     * 
     * @return array $respuesta Json
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 11-03-2015
     */

    public function getJsonModeloElementosByCriterios( $arrayParametros )
    {
        
        $intTotal               = 0;
        $arrayModelosElementos  = array();
        
        $arrayTmpResultados    = $this->getModeloElementosByCriterios( $arrayParametros );
        
        if( $arrayTmpResultados )
        {
            $arrayTmpModelosElementos = $arrayTmpResultados['registros'];
            
            foreach( $arrayTmpModelosElementos as $objModeloElemento )
            {               
                $arrayModelosElementos[] = array(
                    'id'            => $objModeloElemento->getId(),
                    'descripcion'   => strtoupper($objModeloElemento->getNombreModeloElemento()));

                $intTotal++;
            }//foreach($arrayResultados as $arrayTipoMedioTransporte)
        }//($arrayResultados)

        
        $respuesta = '{"total":"'.$intTotal.'","encontrados":'.json_encode($arrayModelosElementos).'}';

        return $respuesta;
    }

       /**
     * getInterfaceBySerie
     *
     * Método que retornará las interfaces en base a la serie que se da como parametro de busqueda                                  
     *
     * @param array $arrayParametros ['strSerie']
     * 
     * @return array $objRespuesta Json
     *
     * @author Andre Lazo <alazo@telconet.ec>
     * @version 1.0 28-12-2022
     */
    public function getInterfaceBySerie( $arrayParametros )
    {
        /*Obtengo los parametros recibidos*/
        $strSerie=$arrayParametros['strSerie'];
        $strModelProductoParametro=$arrayParametros['modelProducto'];
        /**consulto el tipo de elemento para obtener el id  */
        $objRsm    = new ResultSetMappingBuilder($this->_em);
        $objQuery  = $this->_em->createNativeQuery(null, $objRsm);
        $strSqltipoModelo= " SELECT  ID_TIPO_ELEMENTO FROM ADMI_TIPO_ELEMENTO A 
                            WHERE A.NOMBRE_TIPO_ELEMENTO= :parametro
                            ";
        $objQuery->setParameter("parametro", $strModelProductoParametro);
        $objRsm->addScalarResult('ID_TIPO_ELEMENTO', 'idTipoElemento', 'string');
        $objQuery->setSQL($strSqltipoModelo);
        $objResultadoTipoModelo = $objQuery->getSingleResult();

        /**consulto el modelo del equipo por su serie */
        $strSqlInArticulosINstalacion= " SELECT  * FROM 
                                        IN_ARTICULOS_INSTALACION INAR 
                                        WHERE INAR.NUMERO_SERIE= :serie
                                        ";
        $objQuery->setParameter("serie", $strSerie);
        $objRsm->addScalarResult('MODELO', 'modelo', 'string');
        $objQuery->setSQL($strSqlInArticulosINstalacion);
        $objResultadoModelo = $objQuery->getArrayResult();

        /**consultos los id del tipo de las interfaces ligadas al modelo del equipo */
        $strSqlModelo = " SELECT  * FROM 
                    ADMI_MODELO_ELEMENTO AD 
                    INNER JOIN ADMI_INTERFACE_MODELO INTER 
                    ON AD.ID_MODELO_ELEMENTO=INTER.MODELO_ELEMENTO_ID AND
                    AD.NOMBRE_MODELO_ELEMENTO= :modelo
                    AND AD.TIPO_ELEMENTO_ID = :tipo
                  ";
        $objQuery->setParameter("modelo", $objResultadoModelo[0]['modelo']);
        $objQuery->setParameter("tipo", $objResultadoTipoModelo['idTipoElemento']);
        $objQuery->setSQL($strSqlModelo);
        $objRsm->addScalarResult('TIPO_INTERFACE_ID', 'tipoInterfaceId', 'string');
        $arrayTipoInterfaces= $objQuery->getArrayResult();
        $arrayInterfaces=array();
        foreach ($arrayTipoInterfaces as $value) 
        {
            /** se obtiene el id de la interfaz y nombre */
            $strInterface = " SELECT  * FROM  
                            ADMI_TIPO_INTERFACE TIP WHERE 
                            TIP.ID_TIPO_INTERFACE = :tipo
                  ";
            $objQuery->setParameter("tipo", $value['tipoInterfaceId']);
            $objQuery->setSQL($strInterface);
            $objRsm->addScalarResult('NOMBRE_TIPO_INTERFACE', 'nombreTipoInterface', 'string');
            $objRsm->addScalarResult('ID_TIPO_INTERFACE', 'idTipoInterface', 'integer');
            /**se agregan a un array para ser devueltos por la funcion con resultado */
            array_push($arrayInterfaces, $objQuery->getSingleResult());
        }
        
        $objRespuesta = $arrayInterfaces;

        return $objRespuesta;
    }

}
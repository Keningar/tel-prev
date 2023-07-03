<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class AdmiCuadrillaRepository extends EntityRepository
{
    
    /**
    * generarJsonCuadrillas
    * 
    * Esta funcion retorna las cuadrillas registrados en estado diferente de Eliminado
    * 
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 13-10-2015 
    * 
    * @author modificado Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.1 04-11-2015 Se realizan ajustes para obtener las cuadrillas por departamento
    *
    * @author modificado Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.2 17-05-2015 Se realizan ajustes para que combo busque por cuadrilla
    *
    * @param array  $parametros [$start,$limit,$estado,$departamentoId,$nombreCuadrilla]
    *
    * @return array $objResultado  Objeto en formato JSON
    *
    */
    public function generarJsonCuadrillas($parametros)
    {
        $arrayEncontrados        = array();
        $arrayDatos              = $this->getRegistrosCuadrillas($parametros);
        
        $intCantidad             = $arrayDatos['total'];
        $arrayRegistros          = $arrayDatos['registros'];
        $strError                = "No existen cuadrillas asignadas de este departamento ";

        if ($arrayRegistros) 
        {            
            foreach ($arrayRegistros as $data)
            {                                    
                 
                 $arrayEncontrados[]  = array('idCuadrilla' => $data["idCuadrilla"],
                                              'nombre'      => $data["nombre"]);                  
            }  

            if($intCantidad == 0)
            {
                $objResultado = array('total'       => 1 ,
                                      'encontrados' => array('idCuadrilla' => 0 , 
                                                             'nombre'      => 'Ninguno',));
                $objResultado = json_encode($objResultado);
                return $objResultado;
            }
            else
            {

                $objData        = json_encode($arrayEncontrados);
                $objResultado   = '{"result": {"total":"'.$intCantidad.'","encontrados":'.$objData.'},
                                    "myMetaData": {"boolSuccess": "1", "message":""} }';
                return $objResultado; 
            }
        }
        else
        {
            $objResultado = '{"result": {"total":"0","encontrados":[]}, "myMetaData": {"boolSuccess": "0", "message":"'.$strError.'"} }';
            return $objResultado;
        }        

    }
    
    
     /**
     * getRegistrosCuadrillas
     * 
     * Esta funcion ejecuta el Query que retorna los materiales utilizados en una solicitud
     * 
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 13-10-2015 
     * 
     * @author modificado Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 04-11-2015 Se realizan ajustes para obtener las cuadrillas por departamento  
     *
     * @author modificado Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 17-05-2015 Se realizan ajustes para que combo busque por cuadrilla
     *
     * @author modificado Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.3 09-04-2018 Se agrega el concepto de Hal para las cuadrillas.
     *
     * @author modificado Jose Daniel Giler <jdgiler@telconet.ec>
     * @version 1.4 19-05-2022 Se modifica para que muestre cuadrillas HAL desde pantalla de nodos.
     *
     * @param array  $parametros [$start,$limit,$estado,$departamentoId,$nombreCuadrilla,$origenP]
     *
     * @return array $arrayDatos        Consulta de la BD
     *
     */
    public function getRegistrosCuadrillas($parametros)
    {        
        $arrayDatos     = array();

        $strStart           = $parametros["start"];
        $strLimit           = $parametros["limit"];
        $estado             = $parametros["estado"];
        $departamentoId     = $parametros["departamentoId"];
        $nombreCuadrilla    = $parametros["nombreCuadrilla"];
        $strOrigenP         = $parametros["strOrigenP"];
        $strCuadrillasHall  = "S";
        $addWhere       = "";
        $strQuery       = $this->_em->createQuery();
        $strQueryCount  = $this->_em->createQuery();
        $strCampos      = " SELECT 
                               a.id as idCuadrilla,a.nombreCuadrilla as nombre ";
                          
        $strFrom        = " FROM 
                                schemaBundle:AdmiCuadrilla a
                            
                            WHERE 
                                a.estado <> :varEstado 
                            AND
                                a.departamentoId = :varDepartamento ";
                            
        $strOrder      = " ORDER BY a.nombreCuadrilla DESC ";

        if($nombreCuadrilla)
        {
           $addWhere = " AND a.nombreCuadrilla like :cuadrilla " ;
           $strQuery->setParameter("cuadrilla","%".$nombreCuadrilla."%");
           $strQueryCount->setParameter("cuadrilla","%".$nombreCuadrilla."%");
        }

        $addWhere .= " AND ( a.esHal <> :paramCuadrillaHall OR a.esHal is NULL ) " ;
        
        $strSelect     = $strCampos . $strFrom . $addWhere . $strOrder;

        $strQuery->setParameter("varEstado", $estado);
        $strQuery->setParameter("varDepartamento", $departamentoId);
        $strQuery->setParameter("paramCuadrillaHall", $strCuadrillasHall);
        $strQuery->setDQL($strSelect);
        
        $strDatos = $strQuery->setFirstResult($strStart)->setMaxResults($strLimit)->getResult();  

        $strCount         = " SELECT COUNT(a) ";
        $strSelectCount   = $strCount . $strFrom . $addWhere . $strOrder;
        
        $strQueryCount->setParameter("varEstado", $estado);
        $strQueryCount->setParameter("varDepartamento", $departamentoId);
        $strQueryCount->setParameter("paramCuadrillaHall", $strCuadrillasHall);
        $strQueryCount->setDQL($strSelectCount);

        $intTotal                = $strQueryCount->getSingleScalarResult();

        $arrayDatos['registros'] = $strDatos;
        $arrayDatos['total']     = $intTotal;  

        return $arrayDatos;
    }  
    
    
     /**
     * Costo: 39
     * existeTabletPorCuadrilla
     * 
     * Función que valida si existe una tablet asociada a una cuadrilla
     * 
     * @param array $arrayParametros[ 'intCuadrillaId'  => id de la cuadrilla
     *                                'strTipoElemento' => tipo de elemento TABLET
     *                                'strEstado'       => estado a validar ]
     *
     * @return string $strExisteTablet
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 21-04-2017
     */
    public function existeTabletPorCuadrilla($arrayParametros)
    { 
        $intExisteTablet = 1;
        $strExisteTablet = "S";

        $strSql = " SELECT COUNT(infodetalleelemento.detalle_valor) as TOTAL
                        FROM info_detalle_elemento infodetalleelemento
                        WHERE infodetalleelemento.estado = :estado
                        AND infodetalleelemento.elemento_id IN
                          (SELECT infoelemento.id_elemento
                          FROM info_elemento infoelemento
                          WHERE infoelemento.estado = :estado
                          AND infoelemento.modelo_elemento_id IN
                            (SELECT admimodeloelemento.id_modelo_elemento
                            FROM admi_modelo_elemento admimodeloelemento
                            WHERE admimodeloelemento.tipo_elemento_id =
                              (SELECT admitipoelemento.id_tipo_elemento
                              FROM admi_tipo_elemento admitipoelemento
                              WHERE admitipoelemento.nombre_tipo_elemento = :tipoElemento 
                              AND admitipoelemento.estado = :estado
                              )
                            )
                          )
                          AND infodetalleelemento.detalle_valor IN (SELECT TO_CHAR(infopersonaempresarol.id_persona_rol) 
                      FROM info_persona_empresa_rol infopersonaempresarol WHERE infopersonaempresarol.cuadrilla_id = :idCuadrilla
                      AND infopersonaempresarol.estado = :estado) ";            

        $objStmt = $this->_em->getConnection()->prepare($strSql);
        $objStmt->bindValue('estado',$arrayParametros["strEstado"]);
        $objStmt->bindValue('tipoElemento',$arrayParametros["strTipoElemento"]);
        $objStmt->bindValue('idCuadrilla',$arrayParametros["intCuadrillaId"]);
        $objStmt->execute();            
                             
        $intExisteTablet = $objStmt->fetchColumn();

        if($intExisteTablet == 0)
        {
            $strExisteTablet = "N";
        }

        return $strExisteTablet;
    }      

    
    /** 	
    * generarJsonCuadrillasActivas 	
    *  	
    * Esta funcion retorna las cuadrillas diferentes de Eliminado, en formato JSON para ser mostrado en TELCOS+ 	
    * 
    * @author Richard Cabrera <rcabrera@telconet.ec> 	
    * @version 1.0 27-09-2015  	
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.1 23-05-2016 Se habilita la busqueda del combo de cuadrilla en el modulo de Tareas.
    *
    * @param $strEstado
    * @param $strNombreCuadrilla
    *
    * @return array $objResultado  Objeto en formato JSON     	
    *  	
    */ 	
    public function generarJsonCuadrillasActivas($strEstado,$strNombreCuadrilla)
    { 	
        $arrayEncontrados        = array(); 	
        $strDatos                = $this->getCuadrillasActivas($strEstado,$strNombreCuadrilla);

        $intCantidad             = $strDatos['total']; 	
        $arrayRegistros          = $strDatos['registros'];
        
        if ($arrayRegistros)  	
        {             	
            foreach ($arrayRegistros as $data) 	
            {                                    
                 $arrayEncontrados[]  = array('id_cuadrilla'     => $data["id_cuadrilla"], 	
                                              'nombre_cuadrilla' => $data["nombre_cuadrilla"]);                   	
            }  

            if($intCantidad == 0) 	
            { 	
                $objResultado = array('total'       => 0 , 	
                                      'encontrados' => array('id_cuadrilla'     => 0 ,  	
                                                             'nombre_cuadrilla' => 'Ninguno')); 	
                $objResultado = json_encode($objResultado);
 	
                return $objResultado; 	
            } 	
            else
 	
            {
                $objData        = json_encode($arrayEncontrados);
 	
                $objResultado   = '{"total":"'.$intCantidad.'","encontrados":'.$objData.'}';
 	
                return $objResultado; 	
            } 	
        }
        else 	
        { 	
            $objResultado = '{"total":"0","encontrados":[]}'; 	
            return $objResultado; 	
        }

    }

     /** 	
     * getCuadrillasActivas 	
     *  	
     * Esta funcion ejecuta el Query que retorna todas las cuadrillas diferentes de estado Eliminado 	
     *  	
     * @author Richard Cabrera <rcabrera@telconet.ec> 	
     * @version 1.0 27-10-2015  	
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 23-05-2016 Se habilita la busqueda del combo de cuadrilla en el módulo de Tareas.
     *
     * Costo 14
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.2 18-06-2019 Se añade un UPPER al momento de filtrar por nombre de cuadrilla.
     *
     * @param $strEstado
     * @param $strNombreCuadrilla
     *
     * @return array $strDatos        Consulta de la BD 	
     *  	
     */
 	
    public function getCuadrillasActivas($strEstado,$strNombreCuadrilla)
    {

        $strQuery       = $this->_em->createQuery();        
        $strQueryCount  = $this->_em->createQuery();
        $addWhere       = "";
        $strCampos      = " SELECT AdmiCuadrilla.id as id_cuadrilla,AdmiCuadrilla.nombreCuadrilla as nombre_cuadrilla  ";

        $strFrom        = " FROM schemaBundle:AdmiCuadrilla AdmiCuadrilla ";

        $strWhere       = " WHERE AdmiCuadrilla.estado <> :varEstado ";

        $strOrder        = " ORDER BY AdmiCuadrilla.nombreCuadrilla ";

        if($strNombreCuadrilla)
        {
           $addWhere = " AND UPPER(AdmiCuadrilla.nombreCuadrilla) like UPPER(:cuadrilla) " ;
           $strQuery->setParameter("cuadrilla","%".$strNombreCuadrilla."%");
           $strQueryCount->setParameter("cuadrilla","%".$strNombreCuadrilla."%");
        }

        $strSelect = $strCampos . $strFrom . $strWhere . $addWhere . $strOrder;

        $strQuery->setParameter("varEstado", $strEstado); 	
        $strQuery-> setDQL($strSelect);  

        $strDatos = $strQuery->getResult();        

        $strCount           = " SELECT COUNT(AdmiCuadrilla.id) ";  

        $strSelectCount = $strCount . $strFrom . $strWhere . $addWhere . $strOrder;

        $strQueryCount->setParameter("varEstado", $strEstado); 	
        $strQueryCount->setDQL($strSelectCount);  

        $intTotal = $strQueryCount->getSingleScalarResult();

        $strDatos['registros'] = $strDatos; 	
        $strDatos['total']     = $intTotal;        

        return $strDatos;
    }     
 
     /**     
     *
     * Documentación para el método 'getJsonMiembrosPorCuadrilla'.
     *
     * Obtener los miembros relacionados a una cuadrilla enviada como parametro en formato Json
     *
     * @return Response $respuesta          
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 13-10-2015 
     *      
     */      
    public function getJsonMiembrosPorCuadrilla($idCuadrilla,$codEmpresa)
    {
        $arr_encontrados = array();
       
        $strDatos  = $registros = $this->getMiembrosPorCuadrilla($idCuadrilla,$codEmpresa);

        $registros = $strDatos['registros'];
        $num       = $strDatos['total'];  

        if ($registros) 
        {            
            foreach ($registros as $data)
            {                        
                $arr_encontrados[]=array('id_persona_rol' => $data['idPersonaRol'],
                                         'id_persona'     => $data['idPersona'],
                                         'nombre'         => $data['nombres'].' '.$data['apellidos']);                                           
            }
           
            $dataF =json_encode($arr_encontrados);
            $resultado= '{"total":"'.$num.'","encontrados":'.$dataF.'}';
            return $resultado;            
        }
        else
        {
            $resultado= '{"total":"0","encontrados":[]}';
            return $resultado;
        }
    }
    
    /**     
     *
     * Documentación para el método 'getMiembrosPorCuadrilla'.
     *
     * Obtener los miembros relacionados a una cuadrilla enviada como parametro
     *
     * @return $arrayRegistros          
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 12-10-2015 
     *      
     */   
    public function getMiembrosPorCuadrilla($idCuadrilla,$codEmpresa)
    {
        $query = $this->_em->createQuery();
        
        $strCampos = " SELECT 
                                DISTINCT(infoPersona.id) as idPersona,infoPersona.identificacionCliente as identificacionCliente,
                                infoPersona.nombres as nombres,infoPersona.apellidos as apellidos ";
        $strFrom   = " FROM 
                                schemaBundle:InfoPersona infoPersona,schemaBundle:InfoPersonaEmpresaRol infoPersonaEmpresaRol,
                                schemaBundle:InfoEmpresaRol InfoEmpresaRol,schemaBundle:AdmiRol AdmiRol,schemaBundle:AdmiTipoRol AdmiTipoRol
                                
                              WHERE 
                                infoPersona.id = infoPersonaEmpresaRol.personaId
                                AND infoPersonaEmpresaRol.empresaRolId = InfoEmpresaRol.id
                                AND InfoEmpresaRol.rolId = AdmiRol.id
                                AND AdmiRol.tipoRolId = AdmiTipoRol.id
                                AND infoPersona.estado NOT IN (:varEstado)
                                AND infoPersonaEmpresaRol.estado NOT IN (:paramEstado)
                                AND AdmiTipoRol.descripcionTipoRol = :varTipoRol
                                AND infoPersonaEmpresaRol.cuadrillaId = :varCuadrillaId ";
        
        $strQuery = $strCampos . $strFrom;
        
        $query->setParameter("varEstado", array('Cancelado','Inactivo','Anulado','Eliminado'));
        $query->setParameter("paramEstado", array('Cancelado','Inactivo','Anulado','Eliminado'));
        $query->setParameter("varTipoRol", 'Empleado');
        $query->setParameter("varCuadrillaId", $idCuadrilla);      
        $query->setDQL($strQuery); 
        
        $arrayRegistros = $query->getResult();
        
        
        $queryCount     = $this->_em->createQuery();
        $strCamposCount = " SELECT COUNT(DISTINCT infoPersona.id ) ";        
        
        $strQueryCount  = $strCamposCount . $strFrom;
        
        $queryCount->setParameter("varEstado", array('Cancelado','Inactivo','Anulado','Eliminado'));
        $queryCount->setParameter("paramEstado", array('Cancelado','Inactivo','Anulado','Eliminado'));
        $queryCount->setParameter("varTipoRol", 'Empleado');
        $queryCount->setParameter("varCuadrillaId", $idCuadrilla);      
        $queryCount->setDQL($strQueryCount); 
        
        $intRegistros = $queryCount->getSingleScalarResult();
        

        $arrayRegistros['registros'] = $arrayRegistros;
        $arrayRegistros['total']     = $intRegistros;  
        
        return $arrayRegistros;
    }  
    
    
    /**     
     *
     * Documentación de la funcion 'getRolJefeCuadrilla'.
     *
     * Esta funcion retorna el codigo del rol para Jefe de Cuadrilla
     *
     * @return integer  $intRol          
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 26-10-2015 
     *      
     */   
    public function getRolJefeCuadrilla()
    {
        $query = $this->_em->createQuery();
        
        $strQuery = " SELECT 
                        InfoEmpresaRol.id as rolJefeCuadrilla
                      FROM 
                        schemaBundle:InfoEmpresaRol InfoEmpresaRol
                      WHERE InfoEmpresaRol.rolId IN
                              (SELECT 
                                AdmiRol.id 
                               FROM schemaBundle:AdmiRol AdmiRol 
                                WHERE AdmiRol.descripcionRol = :varDescripcion
                                AND AdmiRol.estado <> :varEstado
                              )
                      AND InfoEmpresaRol.empresaCod = :varEmpresa 
                      AND InfoEmpresaRol.estado <> :varEstado ";

        $query->setParameter("varDescripcion",'Jefe Cuadrilla');
        $query->setParameter("varEstado",'Eliminado');
        $query->setParameter("varEmpresa", 10);    
        $query->setDQL($strQuery); 
        
        $intRol = $query->getSingleScalarResult();

        return $intRol;
    }      
    

    /**
     * generarJson
     *
     * Metodo encargado que devuelve las cuadrillas de acuerdo a parametros enviados
     *     
     * @param string $nombre     
     * @param string $estado    
     * @param string $start    
     * @param string $limit    
     * @param string $boolTodosValue            
     *
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.5 26-01-2015 - Actualizacion (se crea variable boolTodosValue que permite validar si se muestra
     *                                          o no la palabra Todos dentro del combo que lo requiera)
     * 
     * @version 1.0 - Version Inicial
     */  
    public function generarJson($nombre,$estado,$start,$limit,$boolTodosValue=false)
    {
        $arr_encontrados = array();
        
        $registrosTotal = $this->getRegistros($nombre, $estado, '', '');
        $registros = $this->getRegistros($nombre, $estado, $start, $limit);
        
        if($boolTodosValue)
        {
            $arr_encontrados[]=array('id_cuadrilla'=>'Todos','nombre_cuadrilla'=>'Todos');
        }
 
        if ($registros) {
            $num = count($registrosTotal);            
            foreach ($registros as $data)
            {
                        
                $arr_encontrados[]=array('id_cuadrilla' =>$data->getId(),
                                         'nombre_cuadrilla' =>trim($data->getNombreCuadrilla()),
                                         'estado' =>(strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-edit'),
                                         'action3' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-delete'));
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_cuadrilla' => 0 , 'nombre_cuadrilla' => 'Ninguno', 'area_id' => 0 , 'area_nombre' => 'Ninguno', 'estado' => 'Ninguno'));
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
               ->from('schemaBundle:AdmiCuadrilla','sim');
            
        $boolBusqueda = false; 
        if($nombre!=""){
            $boolBusqueda = true;
            $qb ->where( 'LOWER(sim.nombreCuadrilla) like LOWER(?1)');
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
        
        $qb->orderBy('sim.nombreCuadrilla');
        
        if($start!='' && !$boolBusqueda)
            $qb->setFirstResult($start);   
        if($limit!='')
            $qb->setMaxResults($limit);
        
        
        $query = $qb->getQuery();
        
        return $query->getResult();
    }    

    
    /**
    * 
    * Documentación de la funcion 'findJefeCuadrilla'.
    * 
    * Fincion que retorna datos del Jefe de una Cuadrilla 
    * 
    * @param integer $idCuadrilla
    * 
    * @return array $strDatos
    * 
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 30-10-2015
    */    
    public function findJefeCuadrilla($idCuadrilla)
    {

        $intPersonaId            = 0;
        $intPersonaEmpresaRolId  = 0;
        $strNombres              = "N/A";
        $strDatos                = array();
        $bandera                 = 0;
        $strIdentificacionLider = "";
        
        $cuadrillaTarea = $this->_em->getRepository('schemaBundle:InfoCuadrillaTarea')
                                    ->getIntegrantesCuadrilla($idCuadrilla); 
        
        if(count($cuadrillaTarea) > 0)
        {    
            foreach($cuadrillaTarea as $datoCuadrilla)
            {
                $infoCuadrilla = $this->_em->getRepository('schemaBundle:InfoCuadrilla')
                                           ->getLiderCuadrilla($datoCuadrilla['idPersona']); 

                if($infoCuadrilla)
                {
                    $bandera                = 1;
                    $datosLider             = $this->_em->getRepository('schemaBundle:InfoPersona')
                                                        ->find($datoCuadrilla['idPersona']);
                    $intPersonaId           = $datosLider->getId();
                    $intPersonaEmpresaRolId = $infoCuadrilla[0]['personaEmpresaRolId'];                   
                    $strNombres             = $datosLider->getNombres().' '.$datosLider->getApellidos();
                    $strIdentificacionLider = $datosLider->getIdentificacionCliente();
                    break;
                } 
            }
            
            if($bandera == 0)
            {
                foreach($cuadrillaTarea as $datoCuadrilla)
                {                 
                    $intRol                = $this->_em->getRepository('schemaBundle:AdmiCuadrilla')->getRolJefeCuadrilla();  
                    $infoPersonaEmpresaRol = $this->_em->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                       ->findOneBy(array('empresaRolId' => $intRol, 
                                                                         'personaId'    => $datoCuadrilla['idPersona']));  
                    if($infoPersonaEmpresaRol)
                    {
                        $bandera                = 1;
                        $datosLider             = $this->_em->getRepository('schemaBundle:InfoPersona')
                                                            ->find($datoCuadrilla['idPersona']);
                        $intPersonaId           = $datosLider->getId();
                        $intPersonaEmpresaRolId = $infoPersonaEmpresaRol->getId();                             
                        $strNombres             = $datosLider->getNombres().' '.$datosLider->getApellidos();
                        $strIdentificacionLider = $datosLider->getIdentificacionCliente();
                        break;
                    }
                }            
            }
            
            if($bandera == 1)
            {
                $strDatos['idPersona']           = $intPersonaId;
                $strDatos['nombres']             = $strNombres;
                $strDatos['idPersonaEmpresaRol'] = $intPersonaEmpresaRolId;  
                $strDatos['cedulaLider']       = $strIdentificacionLider;    
            }
            else
            {
                $strDatos['idPersona']           = '';
                $strDatos['nombres']             = 'N/A';
                $strDatos['idPersonaEmpresaRol'] = '';   
                $strDatos['cedulaLider']       = '';             
            }
        }        
        else
        {
            $strDatos['idPersona']           = '';
            $strDatos['nombres']             = 'N/A';
            $strDatos['idPersonaEmpresaRol'] = '';     
            $strDatos['cedulaLider']       = '';                     
        } 
        
        return $strDatos;
 
    }

     /**
     * findCuadrillas
     *
     * Esta funcion retorna el listado de cuadrillas
     *
     * @version 1.0 Version Incial
     *
     * @author modificado Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 09-04-2018 Se agrega condicion para cuadrillas asignadas a Hal
     *
     * @author modificado Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.2 02-05-2018 Se agrega condicion para filtrar por zona.
     *
     * @author modificado Antonio Ayala <afayala@telconet.ec>
     * @version 1.3 29-06-2020 Se agrega condicion para filtrar por departamento.
     * 
     * @author modificado Antonio Ayala <afayala@telconet.ec>
     * @version 1.4 18-09-2020 Se agrega validación para que filtre por departamento sólo si tiene id de departamento.
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.5 13-12-2021 - Se agrega bloque para poder ejecutar una busqueda por nombres de cuadrillas mediante "IN".
     *
     * @param array  $arrayParametros [ 
     *                                  strNombreCuadrilla => nombre de la cuadrilla a buscar,
     *                                  strHal             => si es Hall,
     *                                  intIdDepartamento  => id del Departamento
     *                                  intZonaId          => id de la zona
     *                                  arrayCuadrillasNombres => (optional) arreglo con nombres de cuadrillas.
     *                               ]
     *
     * @return array $arrayRespuesta
     */
    public function findCuadrillas($arrayParametros)
    {
        $objQuery           = $this->_em->createQuery();
        $arrayRespuesta     = array();
        $strEstadoCuadrilla = "Eliminado";
        $strEsHal           = $arrayParametros['strHal'];
        $strValorDeHal      = "S";
        $strNombreCuadrilla = $arrayParametros['strNombreCuadrilla'];
        $strWhere           = "";

        $strSql = "SELECT c
                     FROM schemaBundle:AdmiCuadrilla c
                        WHERE c.estado <> :estadoCuadrilla ";

        $objQuery->setParameter("estadoCuadrilla", $strEstadoCuadrilla);

        if($strEsHal == "S")
        {
            $strWhere .= " AND ( c.esHal <> :esHal OR c.esHal is NULL ) ";
            $objQuery->setParameter("esHal", $strValorDeHal);
        }

        if (isset($arrayParametros['arrayCuadrillasNombres']) && empty($strNombreCuadrilla) && $strNombreCuadrilla == "")
        {
            $strWhere .= " AND UPPER(c.nombreCuadrilla) IN (:paramCuadrilla)";

            $objQuery->setParameter("paramCuadrilla", $arrayParametros['arrayCuadrillasNombres']);
        }

        if(!empty($strNombreCuadrilla) && $strNombreCuadrilla != "")
        {
            $strWhere .= " AND LOWER(c.nombreCuadrilla) LIKE :paramCuadrilla";

            $objQuery->setParameter("paramCuadrilla","%".strtolower($strNombreCuadrilla)."%");
        }

        if (!is_null($arrayParametros['intZonaId']))
        {
            $strWhere .= " AND c.zonaId = :intZonaId";
            $objQuery->setParameter("intZonaId", $arrayParametros['intZonaId']);
        }
        
        //Se agrega validación de que ingrese sólo si tiene id de departamento
        if (!is_null($arrayParametros['intIdDepartamento']) && $arrayParametros['intIdDepartamento'] !== "")
        {
            $strWhere .= " AND c.departamentoId = :intIdDepartamento";
            $objQuery->setParameter("intIdDepartamento", $arrayParametros['intIdDepartamento']);
        }

        $strSql = $strSql . $strWhere;
        $objQuery->setDQL($strSql);
        $arrayRespuesta = $objQuery->getResult();

        return $arrayRespuesta;
    }


    /**
     * getCuadrillasByCriterios
     *
     * Método que retorna las cuadrillas dependiendo de los criterios enviados por el usuario                                    
     *      
     * @param array $arrayParametros
     * 
     * @return array $arrayResultados
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 14-10-2015
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 03-04-2018 - Se agrega condicional al query cuando se requiera consultar si una cuadrilla es HAL o no
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.2 17-08-2018 - Se valida el parametro intCoordinadorPrincipal para poder obtener todas las cuadrillas
     *                           sin necesidad de ser coordinador.
     * 
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.3 29-11-2019 - Se agrega el parámetro preferencia para consultar las cuadrillas por preferencias.
     * 
     * @author Daniel Guzmán <ddguzman@telconet.ec>
     * @version 1.4 16-01-2023 - Se agrega nuevas condiciones para que se pueda buscar cuadrillas en base al Departamento u
     *                              Oficina y Departamento, además se añade una condicon para buscar cuadrillas con estado
     *                              'Activo' o 'Prestado' durante la consulta.
     * 
     * @author José Castillo <jmcastillo@telconet.ec>
     * @version 1.5 06-06-2023 - Se validan las nuevas condiciones de busqueda en base al departamento u oficna solo para el personal de OPU.
     */
    public function getCuadrillasByCriterios($arrayParametros)
    {
        $arrayResultados  = array();
        
        $query      = $this->_em->createQuery();
        $queryCount = $this->_em->createQuery();
        
        $strSelect      = "SELECT ac ";
        $strSelectCount = "SELECT COUNT ( ac.id ) ";
        $strFrom        = "FROM schemaBundle:AdmiCuadrilla ac,
                                schemaBundle:AdmiDepartamento ad "; 
        $strWhere       = "WHERE ac.departamentoId = ad.id ";
        $strOrderBy     = "ORDER BY ac.nombreCuadrilla ";

        if (isset ($arrayParametros['intCoordinadorPrincipal']))
        {
            $intCoordinadorPrincipal= trim($arrayParametros['intCoordinadorPrincipal']);

            if( !empty($intCoordinadorPrincipal) )
            {
                $strWhere .= ' AND (ac.coordinadorPrincipalId = :intCoordinador OR ac.coordinadorPrestadoId = :intCoordinador) ';

                $query->setParameter('intCoordinador' , $arrayParametros['intCoordinadorPrincipal']);

                $queryCount->setParameter('intCoordinador' , $arrayParametros['intCoordinadorPrincipal']);
            }
        }

        if( isset($arrayParametros['criterios']) )
        {
            if( isset($arrayParametros['criterios']['nombre']) )
            {
                if($arrayParametros['criterios']['nombre'])
                {
                    $strWhere .= 'AND ac.nombreCuadrilla LIKE :nombre ';
                    
                    $query->setParameter('nombre', '%'.trim($arrayParametros['criterios']['nombre']).'%');

                    $queryCount->setParameter('nombre', '%'.trim($arrayParametros['criterios']['nombre']).'%');
                }  
            }

            if (isset($arrayParametros['criterios']['strNombreDepartamento']) && 
                $arrayParametros['criterios']['strNombreDepartamento'] == 'Operaciones Urbanas')
            {
                if( isset($arrayParametros['criterios']['strBuscarPor']) &&
                    $arrayParametros['criterios']['strBuscarPor'] == 'soloOficina' &&
                    isset($arrayParametros['criterios']['intOficinaId']) )
                {
                    
                    $strFrom  .= ', schemaBundle:InfoPersonaEmpresaRol ipe ';
                    $strWhere .= 'AND (ac.coordinadorPrincipalId = ipe.id OR ac.coordinadorPrestadoId = ipe.id) '.
                                    'AND ipe.oficinaId = :intOficinaId ';
                    
                    $query->setParameter('intOficinaId' , $arrayParametros['criterios']['intOficinaId']);
                    $queryCount->setParameter('intOficinaId' , $arrayParametros['criterios']['intOficinaId']);               
                }

                if( isset($arrayParametros['criterios']['strBuscarPor']) &&
                    $arrayParametros['criterios']['strBuscarPor'] == 'oficina' &&
                    isset($arrayParametros['criterios']['intOficinaId']) &&
                    isset($arrayParametros['criterios']['intDepartamentoId'] ))
                {
                    
                    $strFrom  .= ', schemaBundle:InfoPersonaEmpresaRol ipe ';
                    $strWhere .= 'AND (ac.coordinadorPrincipalId = ipe.id OR ac.coordinadorPrestadoId = ipe.id) '.
                                    'AND ipe.oficinaId = :intOficinaId '.
                                    'AND ad.id = :intDepartamentoId ';
                    
                    
                    $query->setParameter('intDepartamentoId' , $arrayParametros['criterios']['intDepartamentoId']);
                    $queryCount->setParameter('intDepartamentoId' , $arrayParametros['criterios']['intDepartamentoId']);

                    $query->setParameter('intOficinaId' , $arrayParametros['criterios']['intOficinaId']);
                    $queryCount->setParameter('intOficinaId' , $arrayParametros['criterios']['intOficinaId']);               
                }
                if( isset($arrayParametros['criterios']['strBuscarPor']) &&
                    $arrayParametros['criterios']['strBuscarPor'] == 'departamento' &&
                    isset($arrayParametros['criterios']['intDepartamentoId']))
                {
                    $strWhere .= 'AND ad.id = :intDepartamentoId '; 

                    $query->setParameter('intDepartamentoId' , $arrayParametros['criterios']['intDepartamentoId']);
                    $queryCount->setParameter('intDepartamentoId' , $arrayParametros['criterios']['intDepartamentoId']);
                }

                if(isset($arrayParametros['criterios']['intIdZona']))
                {
                    $strWhere .= 'AND ac.zonaId = :intIdZona '; 

                    $query->setParameter('intIdZona' , $arrayParametros['criterios']['intIdZona']);
                    $queryCount->setParameter('intIdZona' , $arrayParametros['criterios']['intIdZona']);
                }

                if( isset($arrayParametros['criterios']['excluirCoordinadorPrestado']))
                {
                    $strWhere .= 'AND (ac.coordinadorPrestadoId IS NULL OR ac.coordinadorPrestadoId != :intCoordinadorPrestado) '; 

                    $query->setParameter('intCoordinadorPrestado' , $arrayParametros['intCoordinadorPrestado']);
                    $queryCount->setParameter('intCoordinadorPrestado' , $arrayParametros['intCoordinadorPrestado']);
                }
            }
 
            if( isset($arrayParametros['criterios']['estado']) )
            {
                $strEstadoCuadrilla = trim($arrayParametros['criterios']['estado']);
                
                if( $strEstadoCuadrilla )
                {
                    if( $strEstadoCuadrilla == "Prestado" )
                    {
                        $strWhere .= 'AND ( ac.estado = :estado AND ac.coordinadorPrincipalId = :intCoordinador ) ';
                    }
                    elseif( $strEstadoCuadrilla == "Es_Prestamo" )
                    {
                        $strEstadoCuadrilla = "Prestado";
                        
                        $strWhere .= 'AND ( ac.estado = :estado AND ac.coordinadorPrestadoId = :intCoordinador ) ';
                    }
                    elseif( $strEstadoCuadrilla == "multiple")
                    {
                        $strWhere .= "AND ac.estado IN ('Activo', 'Prestado') ";
                    }
                    else
                    {
                        $strWhere .= 'AND ac.estado = :estado ';
                    }

                    if( $strEstadoCuadrilla == 'soloPrestado' )
                    {
                        $strEstadoCuadrilla = 'Prestado';
                    }

                    if ($strEstadoCuadrilla != "multiple")
                    {
                        $query->setParameter('estado', $strEstadoCuadrilla);
                        $queryCount->setParameter('estado', $strEstadoCuadrilla);
                    }
                }  
            }
            
            if( isset($arrayParametros['criterios']['esHal']) )
            {
                $strEsHal = trim($arrayParametros['criterios']['esHal']);
                
                if( !empty($strEsHal) )
                {                    
                    $strWhere .= 'AND ac.esHal = :esHal ';
                    
                    $query->setParameter('esHal', $strEsHal);

                    $queryCount->setParameter('esHal', $strEsHal);
                }  
            }
            if( isset($arrayParametros['criterios']['preferencia']) )
            {
                $strPreferencia = trim($arrayParametros['criterios']['preferencia']);
                
                if( !empty($strPreferencia) )
                {                    
                    $strWhere .= 'AND ac.preferencia = :preferencia ';
                    
                    $query->setParameter('preferencia', $strPreferencia);

                    $queryCount->setParameter('preferencia', $strPreferencia);
                }  
            }
        }
        
        $strSql = $strSelect.$strFrom.$strWhere.$strOrderBy;

        $query->setDQL($strSql);  
        
        if( isset($arrayParametros['intStart']) )
        {
            if($arrayParametros['intStart'])
            {
                $query->setFirstResult($arrayParametros['intStart']);
            }  
        }
        
        if( isset($arrayParametros['intLimit']) )
        {
            if($arrayParametros['intLimit'])
            {
                $query->setMaxResults($arrayParametros['intLimit']);
            }
        }
        
        $arrayTmpDatos = $query->getResult();
        
        $strSqlCount = $strSelectCount.$strFrom.$strWhere;
        
        $queryCount->setDQL($strSqlCount);  
        
        $intTotal = $queryCount->getSingleScalarResult();
            
        $arrayResultados['registros'] = $arrayTmpDatos;
        $arrayResultados['total']     = $intTotal;
        
        return $arrayResultados;
    }
    
    
    /**
     * getSecuencialParaCodigoCuadrilla
     *
     * Método que retorna el secuencial para formar el codigo de las cuadrillas       
     * 
     * @return integer $intTotal
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 14-10-2015
     */
    function getSecuencialParaCodigoCuadrilla()
    {
        $rsm   = new ResultSetMappingBuilder($this->_em);
        $rsm->addScalarResult('SECUENCIA', 'secuenciaValor', 'string');
        
        $query = $this->_em->createNativeQuery("SELECT SEQ_ADMI_CUADRILLA_CODIGO.NEXTVAL as SECUENCIA FROM DUAL", $rsm);
        
        $arraySecuencia = $query->getScalarResult();
        
        $intTotal = $arraySecuencia[0]['secuenciaValor'];
        
        return $intTotal;
    }
    
    
    /**
     * getEstadosCuadrillas
     *
     * Método que retorna los estados de las cuadrillas       
     * 
     * @return integer $intTotal
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 14-10-2015
     */
    function getEstadosCuadrillas()
    {
        $query      = $this->_em->createQuery();
        
        $strSelect  = "SELECT DISTINCT ac.estado ";
        $strFrom    = "FROM schemaBundle:AdmiCuadrilla ac ";
        $strOrderBy = "ORDER BY ac.estado ";
        
        $strSql = $strSelect.$strFrom.$strOrderBy;
        
        $query->setDQL($strSql);  
        
        $arrayResultados = $query->getResult();
        
        return $arrayResultados;
    }
    
    
    /**
     * findHistorialCuadrillasByCriterios
     *
     * Método que retorna el último registro del historial de una cuadrilla de acuerdo a los parametros ingresados por el usuario                                    
     *      
     * @param array $arrayParametros
     * 
     * @return array $arrayResultado
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 21-10-2015
     */
    public function findHistorialCuadrillasByCriterios($arrayParametros)
    {
        $query     = $this->_em->createQuery();
        
        $strSelect = 'SELECT ach ';
        $strFrom   = 'FROM schemaBundle:AdmiCuadrilla ac,
                           schemaBundle:AdmiCuadrillaHistorial ach '; 
        $strWhere  = 'WHERE ach.cuadrillaId = ac.id
                        AND ach.id = (
                                            SELECT MAX ( ach2.id )
                                            FROM schemaBundle:AdmiCuadrillaHistorial ach2
                                            WHERE ach2.cuadrillaId = :cuadrillaId
                                              AND ach2.motivoId = :motivoId
                                      ) ';
        
        $query->setParameter('cuadrillaId', $arrayParametros['cuadrillaId']);
        $query->setParameter('motivoId',    $arrayParametros['motivoId']);
        
        $strSql = $strSelect.$strFrom.$strWhere;
        
        $query->setDQL($strSql);  
        
        $arrayResultado = $query->getSingleResult();
        
        return $arrayResultado;
    }
    
    /**
     * costo 210
     * 
     * Función que sirve para obtener el registro de cuadrillas 
     * por medio del usuario y la fecha, esto es usado en el 
     * apartado de fiscalización - TM-Operaciones.
     * 
     * @author Wilmer Vera G. <wvera@telconet.ec>
     * @version 1.0 16-08-2019
     *
     *  @param array $arrayParametros Datos necesiarios en un array
     *                  *userCreacion -> usuario de creación 
     *                  *feCreacion   -> fecha actual.
     * 
     * Se agrega lógica para realizar un filtrado más especifico en la 
     * consulta de cuadrillas fiscalizadas. 
     * @author Wilmer Vera G. <wvera@telconet.ec>
     * @version 1.1, 03-10-2019
     * @since 1.0
     *
     *  @param array $arrayParametros Datos necesiarios en un array
     *                  *userCreacion           -> usuario de creación. 
     *                  *feCreacion             -> fecha actual. 
     *                  *esUsuarioGeneral       -> bandera que identifica si tiene perfil para hacer una 
     *                                             busqueda general.
     *                  *esPorLiderCuadrilla    -> bandera que identifica si desea realizar un filtrado por cuadrilla;
     *                  *idCuadrilla            -> id de la cuadrilla a buscar.;
     */
    public function obtenerCuadrillasFiscalizadas ($arrayParametros)
    {
        $arrayRespuesta = array();
        try
        {
            $objRsm                 = new ResultSetMappingBuilder($this->_em);
            $objQuery               = $this->_em->createNativeQuery(null, $objRsm);

            $strUserCreacion        = $arrayParametros['userCreacion'];
            $strFeCreacion          = $arrayParametros['feCreacion'];
            $boolUsuarioGeneral     = $arrayParametros['esUsuarioGeneral'];
            $boolPorLiderCuadrilla  = $arrayParametros['esPorLiderCuadrilla'];
            $intIdCuadrilla         = $arrayParametros['idCuadrilla'];
           
            $strAnd = " AND ACH.USR_CREACION = :strUserCreacion ";

            if($boolUsuarioGeneral)
            {
                $strAnd = " ";
            }

            if($boolPorLiderCuadrilla)
            {
                $strAnd = $strAnd." AND ACUA.ID_CUADRILLA = :intIdCuadrilla";
            }

            $strSql = 
            "SELECT  
                ACH.ID_CUADRILLA_HISTORIAL,
                ACH.FE_CREACION, 
                ACH.USR_CREACION, 
                ACUA.NOMBRE_CUADRILLA, 
                ACH.OBSERVACION
            FROM 
                DB_COMERCIAL.ADMI_CUADRILLA_HISTORIAL ACH, 
                DB_COMERCIAL.ADMI_CUADRILLA ACUA
            WHERE 
                TRUNC(ACH.FE_CREACION) = TO_DATE(:strFeCreacion, 'dd/mm/YY')
            AND 
                ACH.CUADRILLA_ID = ACUA.ID_CUADRILLA "
                .$strAnd.
                " ORDER BY ACH.FE_CREACION";

            $objQuery->setParameter('strUserCreacion', $strUserCreacion );
            $objQuery->setParameter('strFeCreacion',   $strFeCreacion);
            $objQuery->setParameter('intIdCuadrilla',  $intIdCuadrilla);

    
            $objRsm->addScalarResult('ID_CUADRILLA_HISTORIAL', 'idCuadrillaHistorial', 'integer');
            $objRsm->addScalarResult('FE_CREACION',            'feCreacion',           'string');
            $objRsm->addScalarResult('USR_CREACION',           'usuarioCreacion',      'string');
            $objRsm->addScalarResult('OBSERVACION',            'observacion',          'string');
            $objRsm->addScalarResult('NOMBRE_CUADRILLA',       'nombreCuadrilla',      'string');
            
            $objQuery->setSQL($strSql);

            $arrayRespuesta = $objQuery->getArrayResult();
            
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
        }
        

        return $arrayRespuesta;

    }

    /**
     * costo 83931
     * 
     * Función que sirve para obtener los documentos a nivel de 
     * detalle de las cuadrillas fiscalizadas.
     * 
     * @author Wilmer Vera G. <wvera@telconet.ec>
     * @version 1.0 16-08-2019
     *
     *  @param array $arrayParametros Datos necesiarios en un array
     *                  *userCreacion -> usuario de creación 
     *                  *feCreacion   -> fecha actual.
     * 
     */
    public function obtenerDocumentosCuadrillasFiscalizadas ($arrayParametros, $emGeneral)
    {
        $arrayRespuesta     = array();
        try
        {
            $objRsm                     = new ResultSetMappingBuilder($this->_em);
            $objQuery                   = $this->_em->createNativeQuery(null, $objRsm);
            $intIdCuadrillaHistorial    = $arrayParametros['idCuadrillaHistorial'];
            $strUserCreacion            = $arrayParametros['strUserCreacion'];
           
             //obtener tipo documento general
            $objTipoDocumentoGeneral    = $emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')
            ->findOneBy(array('descripcionTipoDocumento' => 'IMAGENES',
                            'estado'                     => 'Activo'));

            $strSql = "SELECT IFDOC.TIPO_DOCUMENTO_ID, 
                              IFDOC.ID_DOCUMENTO,IFDOC.NOMBRE_DOCUMENTO, 
                              IFDOC.UBICACION_FISICA_DOCUMENTO,
                              IFDOC.USR_CREACION,IFDOC.EMPRESA_COD,
                              IFDOC.LATITUD,IFDOC.LONGITUD 
                       FROM DB_COMUNICACION.INFO_DOCUMENTO IFDOC 
                       WHERE IFDOC.CUADRILLA_HISTORIAL_ID = :intIdCuadrillaHistorial
                       AND ESTADO = 'Activo' 
                       AND USR_CREACION = :strUserCreacion
                       AND EMPRESA_COD = 10
                       AND TIPO_DOCUMENTO_GENERAL_ID = :intIdAdmiTipoDocGeneral
                       AND IFDOC.LATITUD IS NOT NULL 
                       AND IFDOC.LONGITUD IS NOT NULL";
            
            $objQuery->setParameter('intIdCuadrillaHistorial', $intIdCuadrillaHistorial );
            $objQuery->setParameter('intIdAdmiTipoDocGeneral', $objTipoDocumentoGeneral->getId());
            $objQuery->setParameter('strUserCreacion',         $strUserCreacion);
            
    
            $objRsm->addScalarResult('ID_DOCUMENTO',                'idDocumento',                  'integer');
            $objRsm->addScalarResult('NOMBRE_DOCUMENTO',            'nombreDocumento',              'string');
            $objRsm->addScalarResult('UBICACION_FISICA_DOCUMENTO',  'ubicacionFisicaDocumento',     'string');
            $objRsm->addScalarResult('USR_CREACION',                'usuarioCreacion',              'string');
            $objRsm->addScalarResult('EMPRESA_COD',                 'codigoEmpresa',                'integer');
            $objRsm->addScalarResult('LATITUD',                     'latitud',                      'string');
            $objRsm->addScalarResult('LONGITUD',                    'longitud',                     'string');
            
            $objQuery->setSQL($strSql);

            $arrayRespuesta = $objQuery->getArrayResult();
            
        }catch(\Exception $e)
        {
            error_log($e->getMessage());
        }
        

        return $arrayRespuesta;

    }
    
    /**
     * getResultadoCuadrillasAsignacionVehicular, Consulta las cuadrillas de acuerdo a la empresa en sesión y 
     * a los parámetros de búsqueda
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 Se modifica la consulta para obtener los registros de acuerdo al horario escogido en la asignación predefinida del chofer
     * 
     * @param  array $arrayParametros[  'intStart'                      => Inicio del rownum,
     *                                  'intLimit'                      => Fin del rownum,
     *                                  'idEmpresa'                     => id de la empresa en sesión
     *                                  'strEstadoActivo'               => string del estado activo,
     *                                  'strDetalleCuadrilla'           => nombre del detalle del elemento que guarda el id de la cuadrilla,
     *                                  'intIdTipoSolicitudAsignacionPredefinida'   => id del tipo de solicitud de asignación vehicular predefinida,
     *                                   'intIdCaractDepartamentoPredefinido'        => id de la caracteristica del departamento predefinido ,

     *                                  'arrayDetallesFechasYHoras'     => array con los nombres de los detalles de las fechas y horas de la
     *                                                                     asignación vehicular
     *                                                                     fecha inicio, fecha fin, hora inicio, hora fin
     *                                  'criterios'                     => array con los parámetros de búsqueda de una cuadrilla
     *                                                                     nombre, estado, departamento
     *                                  
     *                               ]
     * 
     * @return array $arrayResultados['registros','total']
     */
    public function getResultadoCuadrillasAsignacionVehicular($arrayParametros)
    {
        $arrayRespuesta['total']     = 0;
        $arrayRespuesta['resultado'] = "";
        try
        {
            $rsm            = new ResultSetMappingBuilder($this->_em);
            $rsmCount       = new ResultSetMappingBuilder($this->_em);
            $ntvQuery       = $this->_em->createNativeQuery(null, $rsm);
            $ntvQueryCount  = $this->_em->createNativeQuery(null, $rsmCount);
            
            $strSelectCount = " SELECT COUNT(*) AS TOTAL ";
            $strSelect  = " SELECT DISTINCT (ac.ID_CUADRILLA), ac.CODIGO,ac.NOMBRE_CUADRILLA, ac.ZONA_ID, ac.TAREA_ID, ac.TURNO_HORA_INICIO,ac.TURNO_HORA_FIN,
                            ac.ESTADO,
                            dep.ID_DEPARTAMENTO,dep.NOMBRE_DEPARTAMENTO, ie.ID_ELEMENTO,ie.NOMBRE_ELEMENTO, per.ID_PERSONA_ROL,
                            p.IDENTIFICACION_CLIENTE,p.ID_PERSONA,p.NOMBRES,p.APELLIDOS ";
            $strFrom    = " FROM DB_COMERCIAL.ADMI_CUADRILLA ac
                            INNER JOIN DB_GENERAL.ADMI_DEPARTAMENTO dep ON ac.DEPARTAMENTO_ID = dep.ID_DEPARTAMENTO 
                            LEFT JOIN DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO ide_cuadrilla 
                                  ON ide_cuadrilla.DETALLE_NOMBRE = :strDetalleCuadrillaAsignacionVehicular
                                  AND ide_cuadrilla.DETALLE_VALOR= ac.ID_CUADRILLA
                                  AND ide_cuadrilla.ESTADO = :strEstadoActivo
                                  
                            LEFT JOIN DB_INFRAESTRUCTURA.INFO_ELEMENTO ie 
                              ON ide_cuadrilla.ELEMENTO_ID = ie.ID_ELEMENTO
                                  
                            LEFT JOIN DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO ide_solicitud_AV 
                                  ON ide_solicitud_AV.DETALLE_NOMBRE = :strDetalleSolAsignacionVehicular
                                  AND ide_solicitud_AV.REF_DETALLE_ELEMENTO_ID= ide_cuadrilla.ID_DETALLE_ELEMENTO 
                                  AND ide_solicitud_AV.ESTADO = :strEstadoActivo

                            LEFT JOIN DB_COMERCIAL.INFO_DETALLE_SOLICITUD detalleSolicitud 
                            ON detalleSolicitud.ID_DETALLE_SOLICITUD = ide_solicitud_AV.DETALLE_VALOR 
                                AND detalleSolicitud.TIPO_SOLICITUD_ID = :idTipoSolicitud
                                AND detalleSolicitud.ESTADO = :strEstadoActivo

                            LEFT JOIN DB_COMERCIAL.INFO_DETALLE_SOL_CARACT detalleSolCaractDep
                                ON detalleSolCaractDep.DETALLE_SOLICITUD_ID = detalleSolicitud.ID_DETALLE_SOLICITUD
                                    AND detalleSolCaractDep.CARACTERISTICA_ID = :idCaractDepartamentoPredefinido
                                    AND detalleSolCaractDep.VALOR = ac.DEPARTAMENTO_ID 
                                    AND detalleSolicitud.ESTADO = :strEstadoActivo

                            LEFT JOIN DB_SOPORTE.INFO_DETALLE detalle
                              ON detalle.DETALLE_SOLICITUD_ID = detalleSolicitud.ID_DETALLE_SOLICITUD

                            LEFT JOIN DB_SOPORTE.INFO_DETALLE_ASIGNACION detalleAsignacion 
                              ON detalleAsignacion.DETALLE_ID=detalle.ID_DETALLE 

                            LEFT JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL per 
                                ON per.ID_PERSONA_ROL=detalleAsignacion.PERSONA_EMPRESA_ROL_ID

                            LEFT JOIN DB_COMERCIAL.INFO_PERSONA p 
                                ON per.PERSONA_ID = p.ID_PERSONA
                            ";
            $strWhere       = "WHERE ac.DEPARTAMENTO_ID IS NOT NULL "; 
            $strOrderBy     = "ORDER BY ac.NOMBRE_CUADRILLA ";
            $rsm->addScalarResult('ID_CUADRILLA', 'idCuadrilla','integer');
            $rsm->addScalarResult('CODIGO', 'codigo','string');
            $rsm->addScalarResult('ZONA_ID','idZona','integer');
            $rsm->addScalarResult('TAREA_ID','idTarea','integer');
            $rsm->addScalarResult('NOMBRE_CUADRILLA','nombreCuadrilla','string');
            $rsm->addScalarResult('TURNO_HORA_INICIO','turnoHoraInicio','string');
            $rsm->addScalarResult('TURNO_HORA_FIN','turnoHoraFin','string');
            $rsm->addScalarResult('ESTADO','estado','string');
            
            $rsm->addScalarResult('ID_DEPARTAMENTO','idDepartamento','string');
            $rsm->addScalarResult('NOMBRE_DEPARTAMENTO','nombreDepartamento','string');
            $rsm->addScalarResult('NOMBRE_ELEMENTO','nombreElemento','string');
            $rsm->addScalarResult('ID_PERSONA_ROL','idPerChoferPredefinido','integer');
            $rsm->addScalarResult('ID_PERSONA','idPersonaChoferPredefinido','integer');
            $rsm->addScalarResult('IDENTIFICACION_CLIENTE','identificacionChoferPredefinido','string');
            $rsm->addScalarResult('NOMBRES','nombresChoferPredefinido','string');
            $rsm->addScalarResult('APELLIDOS','apellidosChoferPredefinido','string');
            $rsm->addScalarResult('ID_ELEMENTO','idElementoAsignado','integer');
            
            
            $rsmCount->addScalarResult('TOTAL', 'total', 'integer');
            
            
            $ntvQuery->setParameter('strDetalleCuadrillaAsignacionVehicular', $arrayParametros['strDetalleCuadrilla']);
            $ntvQueryCount->setParameter('strDetalleCuadrillaAsignacionVehicular', $arrayParametros['strDetalleCuadrilla']);
            
            $ntvQuery->setParameter('strDetalleSolAsignacionVehicular', $arrayParametros['strDetalleSolAsignacionVehicular']);
            $ntvQueryCount->setParameter('strDetalleSolAsignacionVehicular', $arrayParametros['strDetalleSolAsignacionVehicular']);
            
            
            $ntvQuery->setParameter('idTipoSolicitud', $arrayParametros['intIdTipoSolicitudAsignacionPredefinida']);
            $ntvQueryCount->setParameter('idTipoSolicitud', $arrayParametros['intIdTipoSolicitudAsignacionPredefinida']);
            
            $ntvQuery->setParameter('idCaractDepartamentoPredefinido', $arrayParametros['intIdCaractDepartamentoPredefinido']);
            $ntvQueryCount->setParameter('idCaractDepartamentoPredefinido', $arrayParametros['intIdCaractDepartamentoPredefinido']);
            
            $ntvQuery->setParameter('strEstadoActivo', $arrayParametros['strEstadoActivo']);
            $ntvQueryCount->setParameter('strEstadoActivo', $arrayParametros['strEstadoActivo']);
            

            if( isset($arrayParametros['criterios']) )
            {
                if(isset($arrayParametros['criterios']['nombre']))
                {
                    if($arrayParametros['criterios']['nombre'])
                    {
                        $strWhere .= 'AND ac.NOMBRE_CUADRILLA LIKE :nombre ';
                        $ntvQuery->setParameter('nombre', '%' . trim($arrayParametros['criterios']['nombre']) . '%');
                        $ntvQueryCount->setParameter('nombre', '%' . trim($arrayParametros['criterios']['nombre']) . '%');
                    }
                }
                
                if( isset($arrayParametros['criterios']['departamento']) )
                {
                    if($arrayParametros['criterios']['departamento'])
                    {
                        $strWhere .= 'AND ac.DEPARTAMENTO_ID = :departamento ';

                        $ntvQuery->setParameter('departamento', $arrayParametros['criterios']['departamento']);
                        $ntvQueryCount->setParameter('departamento', $arrayParametros['criterios']['departamento']);
                    }  
                } 


                if( isset($arrayParametros['criterios']['estado']) )
                {
                    if( $arrayParametros['criterios']['estado'] )
                    {
                        if($strEstadoCuadrilla=="Activo")
                        {
                            $strWhere .= 'AND (ac.ESTADO = :estado OR ac.ESTADO = :estadoPrestado) ';
                            $ntvQuery->setParameter('estadoPrestado', 'Prestado');
                            $ntvQueryCount->setParameter('estadoPrestado', 'Prestado');
                        }
                        else
                        {
                            $strWhere .= 'AND ac.ESTADO = :estado ';
                        }
                        $ntvQuery->setParameter('estado', $arrayParametros['criterios']['estado']);
                        $ntvQueryCount->setParameter('estado', $arrayParametros['criterios']['estado']);
                    }
                }
                
                if(isset($arrayParametros['criterios']['nombresChofer']))
                {
                    if($arrayParametros['criterios']['nombresChofer'])
                    {
                        $strWhere .= 'AND p.NOMBRES LIKE :nombresChofer ';
                        $ntvQuery->setParameter('nombresChofer', '%' . trim(strtoupper($arrayParametros['criterios']['nombresChofer'])) . '%');
                        $ntvQueryCount->setParameter('nombresChofer', '%' . trim(strtoupper($arrayParametros['criterios']['nombresChofer'])) . '%');
                    }
                }
                
                if(isset($arrayParametros['criterios']['apellidosChofer']))
                {
                    if($arrayParametros['criterios']['apellidosChofer'])
                    {
                        $strWhere .= 'AND p.APELLIDOS LIKE :apellidosChofer ';
                        $ntvQuery->setParameter('apellidosChofer', '%' . trim(strtoupper($arrayParametros['criterios']['apellidosChofer'])) . '%');
                        $ntvQueryCount->setParameter('apellidosChofer', '%' . trim(strtoupper($arrayParametros['criterios']['apellidosChofer'])) . '%');
                    }
                }
                
                if(isset($arrayParametros['criterios']['identificacionChofer']))
                {
                    if($arrayParametros['criterios']['identificacionChofer'])
                    {
                        $strWhere .= 'AND p.IDENTIFICACION_CLIENTE LIKE :identificacionChofer ';
                        $ntvQuery->setParameter('identificacionChofer', $arrayParametros['criterios']['identificacionChofer']);
                        $ntvQueryCount->setParameter('identificacionChofer', $arrayParametros['criterios']['identificacionChofer']);
                    }
                }
                
            }

            $strSqlPrincipal=$strSelect.$strFrom.$strWhere.$strOrderBy;
            
            $strSqlFinal='';

            if( isset($arrayParametros['intStart']) && isset($arrayParametros['intLimit']) )
            {
                if($arrayParametros['intStart'] && $arrayParametros['intLimit'])
                {
                    $intInicio=$arrayParametros['intStart'];
                    $intFin=$arrayParametros['intStart'] + $arrayParametros['intLimit'];
                    $strSqlFinal   = '  SELECT * FROM 
                                        (
                                            SELECT consultaPrincipal.*,rownum AS consultaPrincipal_rownum 
                                            FROM ('.$strSqlPrincipal.') consultaPrincipal 
                                            WHERE rownum<='.$intFin.'
                                        ) WHERE consultaPrincipal_rownum >'.$intInicio;
                }
                else
                {
                    $strSqlFinal   = '  SELECT consultaPrincipal.* 
                                        FROM ('.$strSqlPrincipal.') consultaPrincipal 
                                        WHERE rownum<='.$arrayParametros['intLimit'];
                }
            }
            else
            {
                $strSqlFinal=$strSqlPrincipal;
            }

            $ntvQuery->setSQL($strSqlFinal);
            $arrayResultado = $ntvQuery->getResult();
            $strSqlCount   = $strSelectCount." FROM (".$strSqlPrincipal.")";
            $ntvQueryCount->setSQL($strSqlCount);
            
            $intTotal = $ntvQueryCount->getSingleScalarResult();
            
                        
            $arrayRespuesta['resultado'] = $arrayResultado;
            $arrayRespuesta['total']     = $intTotal;
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
        }
        return $arrayRespuesta;
    }
    
    /**
     * getJSONCuadrillasAsignacionVehicular, Devuelve el json con la consulta las cuadrilla con su respectivo vehículo asignado
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 
     * 
     * @param  array $arrayParametros[  'intStart'                      => Inicio del rownum,
     *                                  'intLimit'                      => Fin del rownum,
     *                                  'idEmpresa'                     => id de la empresa en sesión
     *                                  'strEstadoActivo'               => string del estado activo,
     *                                  'strDetalleCuadrilla'           => nombre del detalle del elemento que guarda el id de la cuadrilla,
     *                                  'intIdTipoSolicitudAsignacionPredefinida'   => id del tipo de solicitud de asignación vehicular predefinida,
     *                                   'intIdCaractDepartamentoPredefinido'        => id de la caracteristica del departamento predefinido ,

     *                                  'arrayDetallesFechasYHoras'     => array con los nombres de los detalles de las fechas y horas de la
     *                                                                     asignación vehicular
     *                                                                     fecha inicio, fecha fin, hora inicio, hora fin
     *                                  'criterios'                     => array con los parámetros de búsqueda de una cuadrilla
     *                                                                     nombre, estado, departamento
     *                                  
     *                               ]
     * 
     * @return json $jsonData
     */
    public function getJSONCuadrillasAsignacionVehicular($arrayParametros,$emInfraestructura,$emGeneral,$emSoporte)
    {
        $arrayEncontrados   = array();

        $arrayResultado     = $this->getResultadoCuadrillasAsignacionVehicular($arrayParametros);
        $resultado          = $arrayResultado['resultado'];
        $intTotal           = $arrayResultado['total'];
        $total              = 0;
        
        
        if($resultado)
        {
            $total = $intTotal;
            foreach($resultado as $data)
            {
                $arrayItem      = array();
                $strNombreZona  = "";
                $strNombreTarea = "";
                
                
                if( $data["idZona"] )
                {
                    $strNombreZona = sprintf("%s", $emGeneral->getRepository('schemaBundle:AdmiZona')->find($data["idZona"]));
                }
                elseif( $data["idTarea"] )
                {
                    $strNombreTarea = sprintf("%s", $emSoporte->getRepository('schemaBundle:AdmiTarea')->find($data["idTarea"]));
                }
                
                $strNombreDepartamento = sprintf("%s", $emGeneral->getRepository('schemaBundle:AdmiDepartamento')
                                                                 ->find($data["idDepartamento"]));
                
                $strEstadoCuadrilla ="";
                if($data["estado"])
                {
                    if($data["estado"]=="Prestado")
                    {
                        $strEstadoCuadrilla="Activo";
                    }
                    else
                    {
                        $strEstadoCuadrilla=$data["estado"];
                    }
                }
                
                $arrayItem['strZona']                = $strNombreZona;
                $arrayItem['strTarea']               = $strNombreTarea;
                $arrayItem['strCodigo']              = $data["codigo"] ? $data["codigo"] : '';
                $arrayItem['strEstado']              = $strEstadoCuadrilla;

                $arrayItem['strTurnoInicio']         = $data["turnoHoraInicio"] ? $data["turnoHoraInicio"] : "";
                $arrayItem['strTurnoFin']            = $data["turnoHoraFin"] ? $data["turnoHoraFin"] : "";
                $arrayItem['intIdCuadrilla']         = $data["idCuadrilla"];
                $arrayItem['strDepartamento']        = $strNombreDepartamento;
                $arrayItem['strNombreCuadrilla']     = $data["nombreCuadrilla"];
                $arrayItem['strActivoAsignado']      = $data["nombreElemento"] ? $data["nombreElemento"]: 'Sin Asignación';
                
                $arrayItem['intIdActivoAsignado']    = $data["idElementoAsignado"]?$data["idElementoAsignado"]:"";
                $arrayItem['strTipoActivoAsignado']  = '';
                
                $arrayItem['intIdPersonaEmpresaRolChofer']  = $data["idPerChoferPredefinido"] ? $data["idPerChoferPredefinido"] :"";
                $arrayItem['intIdPersonaChofer']            = $data["idPersonaChoferPredefinido"] ? $data["idPersonaChoferPredefinido"] : "";
                $arrayItem['strNombresChofer']              = $data["nombresChoferPredefinido"] ? $data["nombresChoferPredefinido"] : "";
                $arrayItem['strApellidosChofer']            = $data["apellidosChoferPredefinido"] ? $data["apellidosChoferPredefinido"]: "";
                $arrayItem['strIdentificacionChofer']       = $data["identificacionChoferPredefinido"] ? $data["identificacionChoferPredefinido"]:"";
                
                
                $arrayItem['intIdDetAsignacionVehicular']   = '';

                $objDetalleElemento = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                        ->findOneBy( 
                                                                        array( 
                                                                                'estado'        => $arrayParametros["strEstadoActivo"], 
                                                                                'detalleNombre' => $arrayParametros["strDetalleCuadrilla"],
                                                                                'detalleValor'  => $arrayItem['intIdCuadrilla'],
                                                                             ) 
                                                                );
                
                
                $arrayDetalleAsignacion = array();
                if( $objDetalleElemento )
                {
                    $arrayItem['intIdDetAsignacionVehicular']   = $objDetalleElemento->getId();
                    $intIdActivoActual = $objDetalleElemento->getElementoId();
                    

                    $objActivoActual = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                         ->findOneBy( array('id' => $intIdActivoActual, 
                                                             'estado' => $arrayParametros["strEstadoActivo"]) );

                    if( $objActivoActual )
                    {
                        $arrayItem['intIdActivoAsignado']           = $intIdActivoActual;
                        $arrayItem['strModeloAsignado']             = $objActivoActual->getModeloElementoId()->getNombreModeloElemento();
                    
                        $strNombreTipoElemento = ucwords( strtolower( $objActivoActual->getModeloElementoId()
                                                                                      ->getTipoElementoId()->getNombreTipoElemento() ) );

                        $arrayItem['strTipoActivoAsignado'] = $strNombreTipoElemento;
                        $arrayItem['strActivoAsignado']     = $objActivoActual->getNombreElemento();
                        
                        
                        $arrayParametrosDetalleElemento = array('estado' => $arrayParametros["strEstadoActivo"], 'parent'=>$objDetalleElemento);


                        $objDetallleFechasYHoras           = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                           ->findBy( $arrayParametrosDetalleElemento );
                        
                        

                        if( $objDetallleFechasYHoras )
                        {
                            foreach( $objDetallleFechasYHoras as $objDetallleElemento )
                            {
                                $arrayDetalleAsignacion["'".$objDetallleElemento->getDetalleNombre()."'"] = $objDetallleElemento->getDetalleValor();
                            }
                        }
                        
                        
                        //Cuando se asigna solo guarda la fecha inicio y las respectivas horas
                        $arrayDetallesFechasYHoras=$arrayParametros['arrayDetallesFechasYHoras'];
                        
                        $strFechaInicioAV='';
                        if(isset($arrayDetalleAsignacion["'".$arrayDetallesFechasYHoras['strFechaInicioAV']."'"]))
                        {
                            if($arrayDetalleAsignacion["'".$arrayDetallesFechasYHoras['strFechaInicioAV']."'"])
                            {
                                $strFechaInicioAV=$arrayDetalleAsignacion["'".$arrayDetallesFechasYHoras['strFechaInicioAV']."'"];
                            }
                        }
                        
                        $strHoraInicioAV='';
                        if(isset($arrayDetalleAsignacion["'".$arrayDetallesFechasYHoras['strHoraInicioAV']."'"]))
                        {
                            if($arrayDetalleAsignacion["'".$arrayDetallesFechasYHoras['strHoraInicioAV']."'"])
                            {
                                $strHoraInicioAV=$arrayDetalleAsignacion["'".$arrayDetallesFechasYHoras['strHoraInicioAV']."'"];
                            }
                        }
                        
                        
                        $strHoraFinAV='';
                        if(isset($arrayDetalleAsignacion["'".$arrayDetallesFechasYHoras['strHoraFinAV']."'"]))
                        {
                            if($arrayDetalleAsignacion["'".$arrayDetallesFechasYHoras['strHoraFinAV']."'"])
                            {
                                $strHoraFinAV=$arrayDetalleAsignacion["'".$arrayDetallesFechasYHoras['strHoraFinAV']."'"];
                            }
                        }
                        
                        $arrayItem['strAsignacionFechaInicio']      = $strFechaInicioAV;
                        $arrayItem['strAsignacionHoraInicio']       = $strHoraInicioAV;
                        $arrayItem['strAsignacionHoraFin']          = $strHoraFinAV;
                        
                    }
                }

                $arrayEncontrados[] = $arrayItem;

            }

        }

        $arrayRespuesta = array('total' => $total, 'encontrados' => $arrayEncontrados);
        $jsonData       = json_encode($arrayRespuesta);
        return $jsonData;
    }

    
    /**************************Asignacion Vehicular Predefinida****************************/
    
    /**
     * getCuadrillasXVehiculoAsignado, Obtiene las cuadrillas que tienen asignado un determinado vehículo.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 13-04-2016
     * 
     * @param  array $arrayParametros[
     *                                  'estadoActivo'            => string del estado Activo,
     *                                  'detalleCuadrilla'        => string del nombre del detalle de asignación vehicular a una cuadrilla,
     *                                  'detalleFechaInicio'      => string del nombre del detalle de la fecha de inicio de la 
     *                                                              asignación vehicular a una cuadrilla,
     *                                  'detalleHoraInicio'       => string del nombre del detalle de la hora de inicio de la 
     *                                                              asignación vehicular a una cuadrilla,
     *                                  'detalleHoraFin'          => string del nombre del detalle de la hora fin de la 
     *                                                              asignación vehicular a una cuadrilla,
     *                                  'elementoId'              => id del vehículo de la asignación predefinida
     *                               ]
     * 
     * @return array $arrayResultado Retorna el array obtenido de la consulta 
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 24-08-2016 Se realizan modificaciones para validar que la cuadrillas obtenidas pertenezcan al horario consultado 
     * 
     */
    public function getResultadoCuadrillasXVehiculoAsignado($arrayParametros)
    {
        $arrayResultado       = "";
        try
        {
            $rsm        = new ResultSetMappingBuilder($this->_em);
            $ntvQuery   = $this->_em->createNativeQuery(null, $rsm);
            $strQuery   = " SELECT ac.NOMBRE_CUADRILLA, ac.TURNO_HORA_INICIO, ac.TURNO_HORA_FIN, 
                            ide_fecha_inicio.DETALLE_VALOR AS FECHA_INICIO, 
                            ide_hora_inicio.DETALLE_VALOR AS HORA_INICIO, ide_HORA_fin.DETALLE_VALOR AS HORA_FIN 

                            FROM DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO ide_cuadrilla 

                            INNER JOIN DB_COMERCIAL.ADMI_CUADRILLA ac ON ac.ID_CUADRILLA = ide_cuadrilla.DETALLE_VALOR 

                            INNER JOIN DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO ide_fecha_inicio 
                                 ON ide_cuadrilla.ID_DETALLE_ELEMENTO = ide_fecha_inicio.REF_DETALLE_ELEMENTO_ID 
                                     

                            INNER JOIN DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO ide_hora_inicio 
                                 ON ide_cuadrilla.ID_DETALLE_ELEMENTO = ide_hora_inicio.REF_DETALLE_ELEMENTO_ID 
                                     

                            INNER JOIN DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO ide_hora_fin 
                                 ON ide_cuadrilla.ID_DETALLE_ELEMENTO = ide_hora_fin.REF_DETALLE_ELEMENTO_ID 
                                     
                                     
                            INNER JOIN DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO ide_detalle_sol 
                                 ON ide_cuadrilla.ID_DETALLE_ELEMENTO = ide_detalle_sol.REF_DETALLE_ELEMENTO_ID 

                            WHERE ide_cuadrilla.ELEMENTO_ID= :elementoId 
                            AND ide_cuadrilla.DETALLE_NOMBRE = :detalleCuadrilla 
                            AND ide_cuadrilla.ESTADO= :estadoActivo 

                            AND ide_fecha_inicio.ESTADO= :estadoActivo 
                            AND ide_hora_inicio.ESTADO= :estadoActivo 
                            AND ide_hora_fin.ESTADO= :estadoActivo 
                            AND ide_detalle_sol.ESTADO= :estadoActivo 
                            AND ide_fecha_inicio.DETALLE_NOMBRE = :detalleFechaInicio
                            AND ide_hora_inicio.DETALLE_NOMBRE = :detalleHoraInicio 
                            AND ide_hora_fin.DETALLE_NOMBRE = :detalleHoraFin 
                            AND ide_detalle_sol.DETALLE_NOMBRE = :detalleSolicitud     
                            AND ide_detalle_sol.DETALLE_VALOR = :idDetalleSolicitud";

            
            $rsm->addScalarResult('NOMBRE_CUADRILLA',   'nombreCuadrilla',                  'string');
            $rsm->addScalarResult('TURNO_HORA_INICIO',  'turnoHoraInicioCuadrilla',         'string');
            $rsm->addScalarResult('TURNO_HORA_FIN',     'turnoHoraFinCuadrilla',            'string');
            $rsm->addScalarResult('FECHA_INICIO',       'fechaInicioAsignacionVehicular',   'string');
            $rsm->addScalarResult('HORA_INICIO',        'horaInicioAsignacionVehicular',    'string');
            $rsm->addScalarResult('HORA_FIN',           'horaFinAsignacionVehicular',       'string');
            
 
            $ntvQuery->setParameter('estadoActivo', $arrayParametros['estadoActivo']);
            $ntvQuery->setParameter('detalleCuadrilla', $arrayParametros['detalleCuadrilla']);
            
            $ntvQuery->setParameter('detalleFechaInicio', $arrayParametros['detalleFechaInicio']);
            $ntvQuery->setParameter('detalleHoraInicio', $arrayParametros['detalleHoraInicio']);
            $ntvQuery->setParameter('detalleHoraFin', $arrayParametros['detalleHoraFin']);
            
            $ntvQuery->setParameter('detalleSolicitud', $arrayParametros['detalleSolicitud']);
            $ntvQuery->setParameter('idDetalleSolicitud', $arrayParametros['idDetalleSolicitud']);
            
            $ntvQuery->setParameter('elementoId', $arrayParametros['elementoId']);
            
            $ntvQuery->setSQL($strQuery);
            $arrayResultado = $ntvQuery->getResult();

        }
        catch(\Exception $e)
        {
            
            error_log($e->getMessage());
        }

        return $arrayResultado;
    }
    
    
    
    /********************************************Asignacion Operativa****************************/
    /**
     * getResultadoInfoCuadrillaAsignacionProvisional, Consulta la información de la cuadrilla con su respectivo coordinador y líder
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 
     * 
     * @param  array $arrayParametros[  'idCuadrilla'           => id de la cuadrilla,
     *                                  'strLider'              => Fin del rownum,
     *                                  'strEstadoActivo'       => string del estado activo,
     *                                  'strEstadoModificado'   => string del estado modificado,
     *                                  'strEstadoPrestado'     => string del estado prestado,
     *                                  'intRowNum'             => fin del rownum
     *                                  'strEmpleado'           => string de empleado
     *                                  'strJefeCuadrilla'      => string del jefe de cuadrilla
     *                                  'strCargo'              => string del cargo
     *                                  
     *                               ]
     * 
     * @return array $arrayRespuesta['resultado','total']
     */
    public function getResultadoInfoCuadrillaAsignacionProvisional($arrayParametros)
    {
        $arrayRespuesta['total']     = 0;
        $arrayRespuesta['resultado'] = "";
        try
        {
            $rsm        = new ResultSetMappingBuilder($this->_em);
            $ntvQuery   = $this->_em->createNativeQuery(null, $rsm);
            
            $rsmCount       = new ResultSetMappingBuilder($this->_em);
            $ntvQueryCount  = $this->_em->createNativeQuery(null, $rsmCount);
            
            $strSelectCount="SELECT COUNT (*) AS TOTAL ";
            
            $strSelect="SELECT DISTINCT ac.ID_CUADRILLA,
                        ac.NOMBRE_CUADRILLA,
                        (SELECT CONCAT(ip10.NOMBRES, CONCAT(' ', ip10.APELLIDOS))
                        FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL iper10
                        JOIN DB_COMERCIAL.INFO_PERSONA ip10
                        ON iper10.PERSONA_ID = ip10.ID_PERSONA
                        WHERE iper10.ID_PERSONA_ROL = ac.COORDINADOR_PRINCIPAL_ID
                        ) AS COORDINADOR_PRINCIPAL,
                        NVL(
                               (  
                                  SELECT CONCAT(ip3.NOMBRES,CONCAT(' ',ip3.APELLIDOS)) as LIDER
                                  FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL iper3
                                  JOIN DB_COMERCIAL.INFO_PERSONA ip3
                                  ON ip3.ID_PERSONA = iper3.PERSONA_ID
                                  WHERE iper3.CUADRILLA_ID = ac.ID_CUADRILLA
                                    AND iper3.ID_PERSONA_ROL IN (
                                                                  SELECT DISTINCT iperc4.PERSONA_EMPRESA_ROL_ID
                                                                  FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC iperc4
                                                                  JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL iper4
                                                                  ON iper4.ID_PERSONA_ROL = iperc4.PERSONA_EMPRESA_ROL_ID
                                                                  WHERE iperc4.CARACTERISTICA_ID = (
                                                                                                    SELECT aca2.ID_CARACTERISTICA
                                                                                                    FROM DB_COMERCIAL.ADMI_CARACTERISTICA aca2
                                                                                                    WHERE aca2.DESCRIPCION_CARACTERISTICA = :strCargo
                                                                                                  )
                                                                    AND iperc4.VALOR = :strLider
                                                                    AND iper4.CUADRILLA_ID = ac.ID_CUADRILLA
                                                                    AND iperc4.ESTADO = :strEstadoActivo
                                                                 )
                                     AND ROWNUM < :intRowNum
                              ),
                              (
                                  SELECT CONCAT(ip7.NOMBRES,CONCAT(' ',ip7.APELLIDOS)) as LIDER
                                  FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL iper7
                                  JOIN DB_COMERCIAL.INFO_PERSONA ip7
                                  ON ip7.ID_PERSONA = iper7.PERSONA_ID
                                  WHERE iper7.CUADRILLA_ID = ac.ID_CUADRILLA
                                    AND iper7.ID_PERSONA_ROL IN (
                                                                    SELECT DISTINCT iper8.ID_PERSONA_ROL
                                                                    FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL iper8
                                                                    JOIN DB_COMERCIAL.INFO_PERSONA ip8
                                                                    ON ip8.ID_PERSONA = iper8.PERSONA_ID
                                                                    JOIN DB_COMERCIAL.INFO_EMPRESA_ROL ier8
                                                                    ON ier8.ID_EMPRESA_ROL = iper8.EMPRESA_ROL_ID
                                                                    JOIN DB_COMERCIAL.ADMI_ROL ar8
                                                                    ON ar8.ID_ROL = ier8.ROL_ID
                                                                    JOIN DB_COMERCIAL.ADMI_TIPO_ROL atr8

                ON atr8.ID_TIPO_ROL = ar8.TIPO_ROL_ID
                                                                    WHERE atr8.DESCRIPCION_TIPO_ROL = :strEmpleado
                                                                    AND ar8.DESCRIPCION_ROL = :strJefeCuadrilla
                                                                    AND iper8.CUADRILLA_ID = ac.ID_CUADRILLA
                                                                    AND ( iper8.ESTADO = :strEstadoActivo OR iper8.ESTADO = :strEstadoModificado )
                                                                )
                         AND ROWNUM < :intRowNum
                              )
                        ) as LIDER ";
            $strFromAndWhere=" FROM DB_COMERCIAL.ADMI_CUADRILLA ac
                               WHERE ac.ESTADO IN (:strEstadoActivo, :strEstadoPrestado) ";

            $strOrderBy=' ORDER BY ac.NOMBRE_CUADRILLA ';
            $rsm->addScalarResult('ID_CUADRILLA', 'idCuadrilla', 'integer');
            $rsm->addScalarResult('NOMBRE_CUADRILLA', 'nombreCuadrilla', 'string');
            $rsm->addScalarResult('COORDINADOR_PRINCIPAL', 'coordinadorCuadrilla', 'string');
            $rsm->addScalarResult('LIDER', 'liderCuadrilla', 'string');
            
            $rsmCount->addScalarResult('TOTAL', 'total', 'integer');
            
                                    
            $ntvQuery->setParameter('strLider', $arrayParametros["strLider"]);
            $ntvQueryCount->setParameter('strLider', $arrayParametros["strLider"]);
            
            $ntvQuery->setParameter('strEstadoActivo', $arrayParametros["strEstadoActivo"]);
            $ntvQueryCount->setParameter('strEstadoActivo', $arrayParametros["strEstadoActivo"]);
            
            $ntvQuery->setParameter('strEstadoModificado', $arrayParametros["strEstadoModificado"]);
            $ntvQueryCount->setParameter('strEstadoModificado', $arrayParametros["strEstadoModificado"]);
            
            $ntvQuery->setParameter('strEstadoPrestado', $arrayParametros["strEstadoPrestado"]);
            $ntvQueryCount->setParameter('strEstadoPrestado', $arrayParametros["strEstadoPrestado"]);
            
            $ntvQuery->setParameter('intRowNum', $arrayParametros["intRowNum"]);
            $ntvQueryCount->setParameter('intRowNum', $arrayParametros["intRowNum"]);
            
            $ntvQuery->setParameter('strEmpleado', $arrayParametros["strEmpleado"]);
            $ntvQueryCount->setParameter('strEmpleado', $arrayParametros["strEmpleado"]);
            
            $ntvQuery->setParameter('strJefeCuadrilla', $arrayParametros["strJefeCuadrilla"]);
            $ntvQueryCount->setParameter('strJefeCuadrilla', $arrayParametros["strJefeCuadrilla"]);
            
            $ntvQuery->setParameter('strCargo', $arrayParametros["strCargo"]);
            $ntvQueryCount->setParameter('strCargo', $arrayParametros["strCargo"]);
            
            
            if( isset($arrayParametros["idCuadrilla"]) )
            {
                if($arrayParametros["idCuadrilla"])
                {
                    $strFromAndWhere.=" AND ac.ID_CUADRILLA = :idCuadrilla ";
                    $ntvQuery->setParameter('idCuadrilla', $arrayParametros["idCuadrilla"]);
                    $ntvQueryCount->setParameter('idCuadrilla', $arrayParametros["idCuadrilla"]);
                }
            }
            $strQuery=$strSelect.$strFromAndWhere.$strOrderBy;
            $ntvQuery->setSQL($strQuery);
            $arrayResultado = $ntvQuery->getResult();

            $strQueryCount=$strSelectCount.$strFromAndWhere.$strOrderBy;
            
            $ntvQueryCount->setSQL($strQueryCount);
            $intTotal = $ntvQueryCount->getSingleScalarResult();
            
            $arrayRespuesta['resultado'] = $arrayResultado;
            $arrayRespuesta['total']     = $intTotal;
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
        }
        return $arrayRespuesta;
    }

    /********************************************Asignacion Operativa****************************/
    
    

    /*
    * Función la cual retorna lso registros de cuadrillas activas con sus respectivos
    * lideres.
    *
    * @return array $arrayResultado Retorna el array obtenido de la consulta 
    * 
    * @author : Wilmer Vera G. <wvera@telconet.ec>
    * @version 1.0, 13-09-2019 
    * 
    */
   public function getLideresConCuadrillaAsignada()
   {
       $arrayResultado       = "";
       try
       {
           $objResultMapping        = new ResultSetMappingBuilder($this->_em);
           $objNativeQuery          = $this->_em->createNativeQuery(null, $objResultMapping);
           
           $strQuery   = " SELECT 
                                IPER.CUADRILLA_ID, 
                                IPER.DEPARTAMENTO_ID, 
                                IPER.EMPRESA_ROL_ID,
                                CAC.NOMBRE_CUADRILLA, 
                                IP.LOGIN,
                                IP.NOMBRES|| ' ' ||IP.APELLIDOS AS NOMBRE_COMPLETO 
                            FROM 
                                DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPER,
                                DB_COMERCIAL.INFO_PERSONA IP,
                                DB_COMERCIAL.ADMI_CUADRILLA CAC,
                                DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC IPERC 
                            WHERE 
                                IPER.CUADRILLA_ID IS NOT NULL 
                                AND IPER.PERSONA_ID                 = IP.ID_PERSONA 
                                AND CAC.ID_CUADRILLA                = IPER.CUADRILLA_ID
                                AND IPER.ESTADO                     = 'Activo'
                                AND IPERC.PERSONA_EMPRESA_ROL_ID    = IPER.ID_PERSONA_ROL
                                AND IPERC.VALOR                     = 'Lider'
                                AND IPERC.ESTADO                    = 'Activo'
                                AND CAC.ESTADO                      IN ('Prestado','Activo')";

           
           $objResultMapping->addScalarResult('CUADRILLA_ID',        'cuadrillaId'    ,          'string');
           $objResultMapping->addScalarResult('DEPARTAMENTO_ID',     'departamentoId' ,          'string');
           $objResultMapping->addScalarResult('EMPRESA_ROL_ID',      'empresaRolId'   ,          'string');
           $objResultMapping->addScalarResult('NOMBRE_CUADRILLA',    'nombreCuadrilla',          'string');
           $objResultMapping->addScalarResult('LOGIN',               'liderCuadrilla' ,          'string');
           $objResultMapping->addScalarResult('NOMBRE_COMPLETO',     'nombreCompelto' ,          'string');
           
           
           $objNativeQuery->setSQL($strQuery);
           $arrayResultado = $objNativeQuery->getArrayResult();

       }
       catch(\Exception $e)
       {
           
           error_log($e->getMessage());
       }

       return $arrayResultado;
   }


    /*
    * Función la cual retorna lso registros de cuadrillas activas por zona y que se encuentre planificada por fecha.
    *
    * @return array $arrayResultado Retorna el array obtenido de la consulta 
    * 
    * @author :Andrés Montero H. <amontero@telconet.ec>
    * @version 1.0, 27-11-2020 
    * 
    */
    public function getCuadrillasPorZonaPlanificada($arrayParametros)
    {
        $arrayRespuesta['resultado'] = "";
        try
        {            
            $objResultMapping   = new ResultSetMappingBuilder($this->_em);
            $objNativeQuery     = $this->_em->createNativeQuery(null, $objResultMapping);

            $strSelect      = "SELECT CAC.ID_CUADRILLA, CAC.NOMBRE_CUADRILLA ";
            $strFrom        = " FROM DB_COMERCIAL.ADMI_CUADRILLA CAC ";
            $strWhere       = " WHERE 
                                CAC.ESTADO IN (:arrayEstado)
                                AND CAC.ID_CUADRILLA IN (
                                  SELECT 
                                    cab.CUADRILLA_ID
                                  FROM 
                                      DB_SOPORTE.INFO_CUADRILLA_PLANIF_CAB cab  
                                      JOIN DB_SOPORTE.ADMI_INTERVALO inter ON inter.ID_INTERVALO = cab.INTERVALO_ID
                                  WHERE
                                      cab.ZONA_ID = :idZona
                                      AND 
                                      (SELECT MIN(FE_INICIO) FROM DB_SOPORTE.INFO_CUADRILLA_PLANIF_DET det1 
                                      WHERE det1.CUADRILLA_PLANIF_CAB_ID = cab.ID_CUADRILLA_PLANIF_CAB ) <=
                                      TO_TIMESTAMP(:fechaProgramadaIni,'YYYY-MM-DD hh24:mi:ss')
                                      AND 
                                      (SELECT MAX(FE_FIN) FROM DB_SOPORTE.INFO_CUADRILLA_PLANIF_DET det1 
                                      WHERE det1.CUADRILLA_PLANIF_CAB_ID = cab.ID_CUADRILLA_PLANIF_CAB ) >=
                                      TO_TIMESTAMP(:fechaProgramadaFin,'YYYY-MM-DD hh24:mi:ss')
                                )";

            $objResultMapping->addScalarResult('ID_CUADRILLA',        'cuadrillaId'    ,          'string');
            $objResultMapping->addScalarResult('NOMBRE_CUADRILLA',    'nombreCuadrilla',          'string');

            $objNativeQuery->setParameter('idZona', $arrayParametros['idZona']);
            $objNativeQuery->setParameter('arrayEstado', array('Prestado','Activo'));
            $objNativeQuery->setParameter('fechaProgramadaIni', $arrayParametros['fechaIni']);
            $objNativeQuery->setParameter('fechaProgramadaFin', $arrayParametros['fechaFin']);

            $strQuery = $strSelect.$strFrom.$strWhere;
            $objNativeQuery->setSQL($strQuery);
            $arrayResultado = $objNativeQuery->getResult();
            
            $arrayRespuesta['resultado'] = $arrayResultado;

        }
        catch(\Exception $e)
        {
            
            error_log($e->getMessage());
        }
 
        return $arrayRespuesta;
    }

    /*
    * Función que retorna registros de cuadrillas satelites activas.
    *
    * @return array $arrayResultado Retorna el array obtenido de la consulta
    *
    * @author Jeampier Carriel <jacarriel@telconet.ec>
    * @version 1.0, 23-11-2021
    *
    * @author Modificado: Jeampier Carriel <jacarriel@telconet.ec>
    * @version 1.1 24-01-2022 Se realizan modificaciones para agregar las cuadrillas con estado 'Prestado' y setear variables bind
    *
    */
    public function getCuadrillaSatelitePersonEmpresRol($arrayParametros)
    {
        $arrayRespuesta['resultado'] = "";
        $strQuery="";
        try
        {
            $objResultMapping   = new ResultSetMappingBuilder($this->_em);
            $objNativeQuery     = $this->_em->createNativeQuery(null, $objResultMapping);

            $strQuery   = "SELECT IPER.CUADRILLA_ID,
                               IPER.DEPARTAMENTO_ID,
                               IPER.EMPRESA_ROL_ID,
                               CAC.NOMBRE_CUADRILLA
                          FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPER,
                               DB_COMERCIAL.INFO_PERSONA             IP,
                               DB_COMERCIAL.ADMI_CUADRILLA           CAC
                            WHERE 
                                IPER.CUADRILLA_ID IS NOT NULL 
                                AND IPER.PERSONA_ID                 = IP.ID_PERSONA 
                                AND CAC.ID_CUADRILLA                = IPER.CUADRILLA_ID
                                AND IPER.ESTADO                     = :strEstadoActivo
                                AND CAC.ESTADO                      IN (:strEstadoActivo, :strEstadoPrestado)
                                AND CAC.ES_SATELITE                 = :strEsSatelite";

            if (!empty($arrayParametros['login']))
            {
                $strQuery  .= " AND IP.LOGIN                        = :user";
            }
            if (!empty($arrayParametros['idPersonaEmpresaRol']))
            {
                $strQuery  .= " AND IPER.ID_PERSONA_ROL             = :intIdPersonaEmpresaRol";
            }

            $objResultMapping->addScalarResult('CUADRILLA_ID',        'cuadrillaId'    ,          'string');
            $objResultMapping->addScalarResult('DEPARTAMENTO_ID',     'departamentoId' ,          'string');
            $objResultMapping->addScalarResult('EMPRESA_ROL_ID',      'empresaRolId'   ,          'string');
            $objResultMapping->addScalarResult('NOMBRE_CUADRILLA',    'nombreCuadrilla',          'string');

            if (!empty($arrayParametros['idPersonaEmpresaRol']))
            {
                $objNativeQuery->setParameter('intIdPersonaEmpresaRol', $arrayParametros['idPersonaEmpresaRol']);
            }
            if (!empty($arrayParametros['login']))
            {
                $objNativeQuery->setParameter('user', $arrayParametros['login']);
            }

            $objNativeQuery->setParameter('strEstadoActivo', $arrayParametros['strEstadoActivo']);
            $objNativeQuery->setParameter('strEstadoPrestado', $arrayParametros['strEstadoPrestado']);
            $objNativeQuery->setParameter('strEsSatelite', $arrayParametros['strEsSatelite']);
            
            $objNativeQuery->setSQL($strQuery);
            $arrayResultado = $objNativeQuery->getResult();

            $arrayRespuesta['resultado'] = $arrayResultado;

        }
        catch(\Exception $e)
        {
            $arrayRespuesta['resultado'] = $e;
            error_log($e->getMessage());
        }

        return $arrayRespuesta;
    }
    
    
}

<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class InfoEmpresaGrupoRepository extends EntityRepository
{
    private $currentCodEmpresa ;

    public function setCurrentIdEmpresa($codEmpresa){
            $this->currentCodEmpresa = $codEmpresa;
    }

    public function getCurrentIdEmpresa(){
            return $this->currentCodEmpresa;
    }
    
    public function generarJson($nombre,$estado,$start,$limit)
    {
        $arr_encontrados = array();
        
        $registrosTotal = $this->getRegistros($nombre, $estado, '', '');
        $registros = $this->getRegistros($nombre, $estado, $start, $limit);
 
        if ($registros) {
            $num = count($registrosTotal);            
            foreach ($registros as $data)
            {
                        
                $arr_encontrados[]=array('id_empresa' =>$data->getId(),
                                         'nombre_empresa' =>trim($data->getNombreEmpresa()),
                                         'razon_social' =>trim($data->getRazonSocial()),
                                         'ruc' =>trim($data->getRuc()),
                                         'estado' =>(strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-edit'),
                                         'action3' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-delete'));
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_empresa' => 0 , 'nombre_empresa' => 'Ninguno', 'razon_social' => 'Ninguno', 'ruc' => 'Ninguno', 'empresa_id' => 0 , 'empresa_nombre' => 'Ninguno', 'estado' => 'Ninguno'));
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
               ->from('schemaBundle:InfoEmpresaGrupo','sim');
            
        $boolBusqueda = false;
        if($nombre!=""){
            $boolBusqueda = true;
            $qb ->where( 'LOWER(sim.nombreEmpresa) like LOWER(?1)');
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
    
    public function getPrefijoByCodigo($empresaCod)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('ieg.prefijo');
        $qb->from('schemaBundle:InfoEmpresaGrupo', 'ieg');
        $qb->where('ieg.id = :empresaCod')->setParameter('empresaCod', $empresaCod);
        $prefijo = $qb->getQuery()->getSingleScalarResult();
        return $prefijo;
    }
    
    /**
     * generarJsonEmpresasPorSistema
     *
     * Metodo encargado de obtener el nombre de cada empresa ligada a la aplicacion en gestion
     *     
     * @param string $arrayParametros [
     *     app              => nombre de la aplicación
     *     prefijoExcluido  => prefijo de la empresa que se desea excluir
     * ]
     * 
     * @return json con los alias de la plantilla         
     *
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 - Version Inicial
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 - Se realizan ajustes por cambios en la creacion de una llamada/tarea
     * 
     * Actualización: Se cambia los parametros a arreglo
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.2 - 20/11/2018
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.3 14-05-2021 - Se modifica el método para devolver el id de la empresa.
     */  
    public function generarJsonEmpresasPorSistema($arrayParametros)
    {
        $arrayEncontrados = array();
        
        $arrayRegistros = $this->getEmpresasPorSistemas($arrayParametros);
 
        if ($arrayRegistros) 
        {
            $num = count($arrayRegistros);    
            
            foreach ($arrayRegistros as $objEmpresaGrupo)
            {                        
                $arrayEncontrados[] = array('idEmpresa'      => $objEmpresaGrupo->getId(),
                                            'prefijo'        => $objEmpresaGrupo->getPrefijo(),
                                            'nombre_empresa' => trim($objEmpresaGrupo->getNombreEmpresa()));
            }

            if($num == 0)
            {
                $array= array('total' => 1 ,
                                 'encontrados' => array('id_empresa'     => 0 , 
                                                        'nombre_empresa' => 'Ninguno', 
                                                        'razon_social'   => 'Ninguno', 
                                                        'ruc'            => 'Ninguno', 
                                                        'empresa_id'     => 0 , 
                                                        'empresa_nombre' => 'Ninguno', 
                                                        'estado'         => 'Ninguno')
                              );
                $resultado = json_encode( $array);
                return $resultado;
            }
            else
            {
                $arrayDatos = json_encode($arrayEncontrados);
                $resultado  = '{"total":"'.$num.'","encontrados":'.$arrayDatos.'}';
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
     * getEmpresasPorSistemas
     *
     * Metodo encargado de obtener el nombre de cada empresa ligada a la aplicacion en gestion
     *
     * @param string $arrayParametros [
     *     app              => nombre de la aplicación
     *     prefijoExcluido  => prefijo de la empresa que se desea excluir
     *     pais             => país de la empresa
     * ]
     * @return array con los alias de la plantilla
     *
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 - Version Inicial
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 - Se realizan ajustes por cambios en la creacion de una llamada/tarea
     * 
     * Actualización: Se cambia los parametros a arreglo
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.2 - 20/11/2018
     * 
     */
    public function getEmpresasPorSistemas($arrayParametros)
    {
        $strApp     = $arrayParametros['app'];
        $strPrefijo = $arrayParametros['prefijoExcluido'];
        $intPais    = $arrayParametros['pais'];

        $query = $this->_em->createQuery();
        $sql = "   SELECT 
                   emp   
                   FROM                     
                   schemaBundle:AdmiParametroCab cab,
                   schemaBundle:AdmiParametroDet det,
                   schemaBundle:InfoEmpresaGrupo emp 
                   WHERE  
                   det.valor2    =   emp.id and
                   det.valor1    =   :app and
                   cab.nombreParametro = :nombre and
                   det.estado    = :estado
                 ";

        $query->setParameter('nombre', "SISTEMA_EMPRESAS");
        $query->setParameter('app', $strApp);
        $query->setParameter('estado', 'Activo');

        if($strPrefijo != "")
        {
            $sql = $sql . " and det.valor3 <> :prefijo ";
            $query->setParameter('prefijo', $strPrefijo);
        }
        if($intPais > 0)
        {
            $sql = $sql . " and det.valor4 = :pais ";
            $query->setParameter('pais', $intPais);
        }

        $query->setDQL($sql);

        $resultado = $query->getResult();

        return $resultado;
    }
    
    /**
     * Documentación para el método 'generarJsonEmpresasByPrefijo'.
     *
     * Retorna una lista empresasGrupo paginada
     *
     * @param Array $arrayParametros['START']       : Int   indice donde inicia el listado
     *                              ['LIMIT']       : Int   cantidad máxima de registros a devolver
     *                              ['COUNT']       : Bool  indica si se cuenta la cantidad de registros de la consulta.
     *                              ['EMPRESASPREF']: Array prefijos de la empresas a consultar.
     *
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 16-01-2016
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 06-07-2016
     * Se cambia el valor del ID del listado de las empresas: de id_empresa a prefijo.
     */
    public function generarJsonEmpresasByPrefijo($arrayParametros)
    {
        $arrayEmpresas = $this->getResultadoEmpresasByPrefijo($arrayParametros);
        
        if($arrayEmpresas)
        {
            $arrayParametros['START'] = 0;
            $arrayParametros['COUNT'] = true;
            
            $intTotal       = $this->getResultadoEmpresasByPrefijo($arrayParametros);
            $arrayRegistros = array();
            
            foreach($arrayEmpresas as $entityEmpresa)
            {

                $arrayRegistros[] = array('prefijo'        => $entityEmpresa->getPrefijo(),
                                          'nombre_empresa' => trim($entityEmpresa->getNombreEmpresa())
                                           );
            }
                
            return '{"total":"' . $intTotal . '","registros":' . json_encode($arrayRegistros) . '}';
        }
        else
        {
            return '{"total":"0","registros":[]}';
        }
    }

    /**
     * Documentación para el método 'getResultadoEmpresasByPrefijo'.
     *
     * Retorna una lista empresasGrupo
     *
     * @param Array $arrayParametros['START']       : Int   indice donde inicia el listado
     *                              ['LIMIT']       : Int   cantidad máxima de registros a devolver
     *                              ['COUNT']       : Bool  indica si se cuenta la cantidad de registros de la consulta.
     *                              ['EMPRESASPREF']: Array prefijos de la empresas a consultar.
     *
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 16-01-2016
     */
    public function getResultadoEmpresasByPrefijo($arrayParametros)
    {
        $objQueryBuilder = $this->_em->createQueryBuilder();

        if(isset($arrayParametros['COUNT']) && $arrayParametros['COUNT'])
        {
            $objQueryBuilder->select('COUNT(eg)');
            $objQueryBuilder->from('schemaBundle:InfoEmpresaGrupo', 'eg');
        }
        else
        {
            $objQueryBuilder->select('eg');
            $objQueryBuilder->from('schemaBundle:InfoEmpresaGrupo', 'eg');
        }   

        $objQueryBuilder->where('eg.prefijo IN (:EMPRESASPREF)')->setParameter('EMPRESASPREF', $arrayParametros['EMPRESASPREF']);
           
        if(isset($arrayParametros['START']))
        {
            if($arrayParametros['START'] != '' && $arrayParametros['START'] > 0)
            {
                $objQueryBuilder->setFirstResult($arrayParametros['START']);
                
                if(isset($arrayParametros['LIMIT']))
                {
                    if($arrayParametros['LIMIT'] != '' && $arrayParametros['LIMIT'] > 0)
                    {
                        $objQueryBuilder->setMaxResults($arrayParametros['LIMIT']);
                    }
                }
            }
        }

        if(isset($arrayParametros['COUNT']) && $arrayParametros['COUNT'])
        {
            return $objQueryBuilder->getQuery()->getSingleScalarResult();
        }
        else
        {
            return $objQueryBuilder->getQuery()->getResult();
        }
    }

    /**
     * getEmpresasVisiblesEnTareas
     *
     * Metodo encargado de obtener el nombre de cada empresa que se presentan en las opciones
     * para gestionar las tareas
     *
     * @param string $arrayParametros [
     *     prefijoConsulta  => prefijo de la empresa que se consulta
     *     prefijoExcluido  => prefijo de la empresa que se desea excluir
     *     tienePerfil      => "S" => si tiene perfil para ver las empresas, "N" => no tiene perfil
     * ]
     *
     * @return json con las empresas a presentar
     *
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 20/12/2018
     * @since 1.0
     *
     */
    public function generarJsonEmpresasVisiblesEnTareas($arrayParametros)
    {
        $arrayEncontrados = array();

        $arrayRegistros = $this->getEmpresasVisiblesEnTareas($arrayParametros);

        if ($arrayRegistros)
        {
            $intNum = count($arrayRegistros);

            foreach ($arrayRegistros as $objEmpresaGrupo)
            {
                $arrayEncontrados[] = array('prefijo'        => $objEmpresaGrupo->getPrefijo(),
                                            'nombre_empresa' => trim($objEmpresaGrupo->getNombreEmpresa())
                                           );
            }

            if($intNum == 0)
            {
                $arrayPreResultado = array('total'       => 1 ,
                                           'encontrados' => array('id_empresa'     => 0 ,
                                                                  'nombre_empresa' => 'Ninguno',
                                                                  'razon_social'   => 'Ninguno',
                                                                  'ruc'            => 'Ninguno',
                                                                  'empresa_id'     => 0 ,
                                                                  'empresa_nombre' => 'Ninguno',
                                                                  'estado'         => 'Ninguno')
                                          );
                $objResultado = json_encode( $arrayPreResultado );
                return $objResultado;
            }
            else
            {
                $arrayDatos = json_encode($arrayEncontrados);
                $objResultado  = '{"total":"'.$intNum.'","encontrados":'.$arrayDatos.'}';
                return $objResultado;
            }
        }
        else
        {
            $objResultado= '{"total":"0","encontrados":[]}';
            return $objResultado;
        }
    }

    /**
     * getEmpresasVisiblesEnTareas
     *
     * Metodo encargado de consultar el nombre de cada empresa que se presentan en las opciones
     * para gestionar las tareas
     *
     * @param string $arrayParametros [
     *     prefijoConsulta  => prefijo de la empresa que se consulta
     *     prefijoExcluido  => prefijo de la empresa que se desea excluir
     *     tienePerfil      => "S" => si tiene perfil para ver las empresas, "N" => no tiene perfil
     * ]
     * @return array con las empresas visibles
     *
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 20/12/2018
     * @since 1.0
     *
     */
    public function getEmpresasVisiblesEnTareas($arrayParametros)
    {
        $strPrefijoConsulta   = $arrayParametros['prefijoConsulta'];
        $arrayPrefijoExcluido = $arrayParametros['prefijoExcluido'];
        $strTienePerfil       = $arrayParametros['tienePerfil'];

        $objQuery = $this->_em->createQuery();
        //costo query 4
        $strSql   = "   SELECT".
                    "   emp".
                    "    FROM".
                    "      schemaBundle:AdmiParametroCab cab,".
                    "      schemaBundle:AdmiParametroDet det,".
                    "      schemaBundle:InfoEmpresaGrupo emp".
                    "    WHERE ".
                    "      det.valor2          = emp.id   AND".
                    "      det.valor1          = :prefijo AND".
                    "      cab.nombreParametro = :nombre  AND".
                    "      det.estado          = :estado";

        $objQuery->setParameter('prefijo', $strPrefijoConsulta);
        $objQuery->setParameter('nombre', "EMPRESAS_VISIBLES_EN_TAREAS");
        $objQuery->setParameter('estado', 'Activo');

        if(!empty($arrayPrefijoExcluido))
        {
            $strSql = $strSql . " and emp.prefijo not in (:prefijoExcluido) ";
            $objQuery->setParameter('prefijoExcluido', $arrayPrefijoExcluido);
        }
        if($strTienePerfil === 'S')
        {
            $strSql = $strSql . " and det.valor3 = :tienePerfil ";
            $objQuery->setParameter('tienePerfil', 'S');
        }
        else
        {
            $strSql = $strSql . " and det.valor4 = :tienePerfil ";
            $objQuery->setParameter('tienePerfil', 'S');
        }

        $objQuery->setDQL($strSql);
        $arrayResultado = $objQuery->getResult();
        return $arrayResultado;
    }

}


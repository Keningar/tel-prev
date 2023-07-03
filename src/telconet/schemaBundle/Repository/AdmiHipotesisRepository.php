<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AdmiHipotesisRepository extends EntityRepository
{

    /**
    *
    * @author Jose Bedon <jobedon@telconet.ec>
    * @version 1.3 19-11-2020 Se agrega filtro por departamento
    *
    * Actualización: 
    *      - Se agrega obtener el tipo_caso_id de la función getRegistros.
    *
    * @author Andrés Montero <amontero@telconet.ec>
    * @version 1.2 19-09-2019
    * 
    * generarJson
    *
    * Esta funcion retorna en formato JSON la lista de Hipotesis a presentarse en el grid
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.1 07-12-2015 Se realizan ajustes para presentar las hipotesis por tipo de caso
    *
    * @version 1.0
    *
    * @param array  $arrayParametros
    *
    * @return array $resultado
    *
    */
    public function generarJson($arrayParametros)
    {
        $arr_encontrados = array();
        $arrayRegistros  = array();

        $arrayRegistros = $this->getRegistrosPorDepartamento($arrayParametros);

        if (count($arrayRegistros['registros']) == 0)
        {
            $arrayRegistros  = $this->getRegistros($arrayParametros);

        }

        $registros       = $arrayRegistros['registros'];
        $registrosTotal  = $arrayRegistros['total'];

        if ($registros)
        {
            $num = count($registrosTotal);
            foreach ($registros as $data)
            {

                $arr_encontrados[] =array('id_hipotesis'          => $data->getId(),
                                          'nombre_hipotesis'      => trim($data->getNombreHipotesis()),
                                          'descripcion_hipotesis' => trim($data->getDescripcionHipotesis()),
                                          'tipo_caso_id'          => $data->getTipoCasoId(),
                                          'estado'                => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ?
                                                                                     'Eliminado':'Activo'),
                                          'action1'               => 'button-grid-show',
                                          'action2'               => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ?
                                                                                     'icon-invisible':'button-grid-edit'),
                                          'action3'               => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ?
                                                                                     'icon-invisible':'button-grid-delete'));
            }

            if($num == 0)
            {
                $resultado = array('total' => 1 ,'encontrados' => array('id_hipotesis'          => 0 ,        'nombre_hipotesis' => 'Ninguno',
                                                                        'descripcion_hipotesis' => 'Ninguno', 'hipotesis_id'     => 0 ,
                                                                        'hipotesis_nombre'      => 'Ninguno', 'estado'           => 'Ninguno'));
                $resultado = json_encode( $resultado);
                return $resultado;
            }
            else
            {
                $dataF     = json_encode($arr_encontrados);
                $resultado = '{"total":"'.$num.'","encontrados":'.$dataF.'}';
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
    *
    * 
    * @author Jose Bedon <jobedon@telconet.ec>
    * @version 1.3 19-11-2020 Se excluyen los sintomas agregados por la categorizacion de sintomas
    *
    * Actualización: 
    *            - Se agrega en el query criterio para buscar por hipotesis_id si el parámetro padreHipotesis tiene valor.
    *            - Se agrega si parámetro buscarSinPadre es "S" entonces consulta hipótesis que tengan null en hipotesis_id.
    *
    * @author Andrés Montero <amontero@telconet.ec>
    * @version 1.2 19-09-2019
    *
    * getRegistros
    *
    * Esta funcion retorna la lista de Hipotesis a presentarse en el grid
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.1 07-12-2015  Se realizan ajustes para presentar las hipotesis por tipo de caso
    *
    * @version 1.0
    *
    * @param array  $parametros
    *
    * @return array $resultado
    *
    */
    public function getRegistros($arrayParametros)
    {
        $arrayDatos           = array();
        $strNombre               = $arrayParametros["nombre"];
        $strEstado               = $arrayParametros["estado"];
        $strCodEmpresa        = $arrayParametros["codEmpresa"];
        $intTipoCaso             = $arrayParametros["tipoCaso"];
        $intPadreHipotesis    = $arrayParametros["padreHipotesis"];
        $strBuscarSinPadre    = $arrayParametros["buscarSinPadre"];
        $intStart             = $arrayParametros["start"];
        $intLimit             = $arrayParametros["limit"];

        $strSql = "SELECT
                sim
                FROM
                schemaBundle:AdmiHipotesis sim
                WHERE";

        $objQuery = $this->_em->createQuery(null);

        if($strNombre && $strNombre!="")
        {
            $strSql .= " lower(sim.nombreHipotesis) like lower(:nombre) AND " ;
            $objQuery->setParameter('nombre','%'.$strNombre.'%');
        }
        if($strEstado && $strEstado!="Todos")
        {
            if($strEstado=="Activo")
            {
                $strSql .= " lower(sim.estado) not like lower(:estado) AND";
                $objQuery->setParameter('estado','Eliminado');
            }
            else
            {
                $strSql .= " lower(sim.estado) like lower(:estado) AND";
                $objQuery->setParameter('estado','%'.$strEstado.'%');
            }
        }
        if($strCodEmpresa && $strCodEmpresa!="")
        {
            $strSql .= " sim.empresaCod = :empresa ";
            $objQuery->setParameter('empresa',$strCodEmpresa);
            $arrayAdmiParametro = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                                          ->getOne("EMPRESA_APLICA_PROCESO", "", "", "","CONSULTA_ARBOL_HIPOTESIS", "", "", "","",$strCodEmpresa);
            if($arrayAdmiParametro['valor2']==='S')
            {
                if (isset($intPadreHipotesis) && $intPadreHipotesis >= 0)
                {
                    $strSql .= " AND (sim.hipotesisId = :hipotesisId) ";
                    $objQuery->setParameter('hipotesisId',$intPadreHipotesis);
                }
                elseif($strBuscarSinPadre === 'S')
                {
                    $strSql .= " AND (sim.hipotesisId is null) ";
                }
                else
                {
                    //excluir hipotesis de nivel 2
                    $strSql .= " AND ( NOT EXISTS ( 
                              SELECT hip.id FROM schemaBundle:AdmiHipotesis hip 
                              WHERE hip.estado = 'Activo' 
                              AND hip.hipotesisId = 0 
                              AND hip.id = sim.hipotesisId ) ) ";

                    //excluir hipotesis de nivel 1
                    $strSql .= " AND ( NOT EXISTS (
                              SELECT hipo.id FROM schemaBundle:AdmiHipotesis hipo 
                              WHERE hipo.estado = 'Activo' 
                              AND hipo.hipotesisId = 0 
                              AND hipo.id = sim.id ) ) ";
                }
            }
        }
        if($intTipoCaso && $intTipoCaso!="")
        {
            //Se consulta si existen configuradas hipotesis que esten asociadas unicamente a un tipo de caso y que esten activas,
            //caso contrario deben mostrarse todas las hipotesis
            $hipotesisXTipoCaso = $this->_em->getRepository('schemaBundle:AdmiHipotesis')->findOneBy(array('estado'     => 'Activo',
                                                                                                           'tipoCasoId' => $intTipoCaso));

            $parametro = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                                        ->getOne("TIPO CASO POR PROCESOS", "", "", "","Movilizacion", "", "", "");

            //Se agrega validacion solo para Tipo de Caso Movilizacion
            if($hipotesisXTipoCaso && $intTipoCaso == $parametro['valor2'])
            {
                $strSql .= " AND ( sim.tipoCasoId = :tipoCaso ) ";
                $objQuery->setParameter('tipoCaso',$intTipoCaso);
            }
            else
            {
                $strSql .= " AND ( sim.tipoCasoId = :tipoCaso OR sim.tipoCasoId is null ) ";
                $objQuery->setParameter('tipoCaso',$intTipoCaso);
            }
        }

        $strSql .= " AND NOT EXISTS (
            SELECT 1 
            FROM 
            schemaBundle:AdmiParametroCab apc,
            schemaBundle:AdmiParametroDet apd
            WHERE apc.id = apd.parametroId
            AND apc.nombreParametro = 'CATEGORIA_HIPOTESIS'
            AND apc.estado = 'Activo'
            AND apd.estado = 'Activo'
            AND apd.valor1 = sim.id
            AND apd.valor3 = 'Nuevo') ";

        $strSql .= " order by sim.nombreHipotesis ASC";

        $objQuery->setDQL($strSql);

        $arrayRegistros = $objQuery->getResult();

        $arrayDatos['registros'] = $objQuery->setFirstResult($intStart)->setMaxResults($intLimit)->getResult();
        $arrayDatos['total']     = $arrayRegistros;

        return $arrayDatos;

    }      

    /**
     * getRegistrosPorDepartamento
     * 
     * Funcion que filtra las hipotesis por departamento
     * 
     * @author Jose Bedon <jobedon@telconet.ec>
     * @version 1.0 19-11-2020 
     * 
     */
    public function getRegistrosPorDepartamento($arrayParametros)
    {
        $arrayDatos      = array();
        $strNombre       = $arrayParametros["nombre"];
        $strEstado       = $arrayParametros["estado"];
        $strCodEmpresa   = $arrayParametros["codEmpresa"];
        $intTipoCaso     = $arrayParametros["tipoCaso"];
        $intDepartamento = $arrayParametros["depart"];
        $intStart        = $arrayParametros["start"];
        $intLimit        = $arrayParametros["limit"];
        $strWhere        = '';

        if($strNombre && $strNombre!="")
        {
            $strWhere .= " AND lower(ahi.nombreHipotesis) like lower('%$strNombre%') ";
        }
        if($strEstado && $strEstado!="Todos")
        {
            if($strEstado=="Activo")
            {
                $strWhere .= " AND lower(ahi.estado) not like lower('Eliminado') ";
            }
            else
            {
                $strWhere .= " AND lower(ahi.estado) like lower('%$strEstado%') ";
            }
        }
        if($strCodEmpresa && $strCodEmpresa!="")
        {
	        $strWhere .= " AND lower(ahi.empresaCod) = '$strCodEmpresa' ";
        }

        if($intTipoCaso && $intTipoCaso!="")
        {
            $strWhere .= " AND ahi.tipoCasoId = :tipoCasoId ";
        }

        $strSql = "SELECT ahi FROM
                schemaBundle:AdmiHipotesis ahi,
                schemaBundle:AdmiParametroCab apc,
                schemaBundle:AdmiParametroDet apd
                WHERE  apc.id = apd.parametroId
                AND apc.nombreParametro = :nombreParametro
                AND apc.estado = :estado
                AND apd.estado = :estado
                AND apd.valor1 = ahi.id
                AND apd.valor2 = :idDepartamento
                $strWhere
                ";
        $objQuery = $this->_em->createQuery($strSql);

        $objQuery->setParameter('nombreParametro', 'CATEGORIA_HIPOTESIS');
        $objQuery->setParameter('estado', 'Activo');

        if($intTipoCaso && $intTipoCaso!="")
        {
            $objQuery->setParameter('tipoCasoId', $intTipoCaso);
        }
        
        $objQuery->setParameter('idDepartamento', $intDepartamento);
               
        $arrayRegistros = $objQuery->getResult();

        $arrayDatos['registros'] = $objQuery->setFirstResult($intStart)->setMaxResults($intLimit)->getResult();
        $arrayDatos['total']     = $arrayRegistros;

        return $arrayDatos;


    }
    
     /**
     * getRegistrosPorDepartamento
     * 
     * Funcion que filtra las hipotesis por departamento
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 22-04-2021 
     * 
     */
    public function getHipotesisParaNCIndisponiblidad($arrayParametros)
    {       
        $arrayResultado = array();
        
        try
        {        
            if(!empty($arrayParametros['info']) && $arrayParametros['info'] != 'Todos')
            {
                $strSql = "SELECT
                                h1.id id,
                                h1.nombreHipotesis valor
                            FROM
                                schemaBundle:AdmiHipotesis h1
                            WHERE
                                    h1.empresaCod = :empresaCod
                                AND h1.estado = :estado
                                AND h1.hipotesisId IN (
                                    SELECT
                                        h2.id
                                    FROM
                                        schemaBundle:AdmiHipotesis h2
                                    WHERE
                                             h2.empresaCod = :empresaCod
                                         AND h2.estado = :estado
                                        AND h2.hipotesisId IN (
                                            SELECT
                                                h3.id
                                            FROM
                                                schemaBundle:AdmiHipotesis h3
                                            WHERE
                                                    h3.empresaCod = :empresaCod
                                                AND h3.estado = :estado
                                        )
                                )";
            }
            else
            {
                 $strSql = "SELECT
                                h1.id id,
                                h1.nombreHipotesis valor
                            FROM
                                schemaBundle:AdmiHipotesis h1
                            WHERE
                                   h1.empresaCod = :empresaCod
                                AND h1.estado = :estado
                                ";
            }
            
             $objQuery = $this->_em->createQuery($strSql);
             
            $objQuery->setParameter("estado",       $arrayParametros['estado']);
            $objQuery->setParameter("empresaCod",   $arrayParametros['empresaCod']); 

            
            $arrayResultado = $objQuery->getResult();         
        }
        catch(\Exception $e)
        {
            error_log('Fn: getHipotesisParaNCIndisponiblidad : '.$e->getMessage());
        }

        return $arrayResultado;
    }

}

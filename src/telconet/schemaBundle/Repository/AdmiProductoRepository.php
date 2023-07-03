<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class AdmiProductoRepository extends EntityRepository
{
    /**
     * 
     * Metodo que devuelve los Productos por los criterios de busqueda
     * 
     * @param string  $nombreTecnico
     * @param string  $estado
     * @param string  $idEmpresa
     * @param integer $limit
     * @param integer $page
     * @param integer $start
     * @return array $resultado
     * 
     * @author Telcos
     * @version 1.0 Version Inicial
     * 
     * @author Modificado: Duval Medina C. <dmedina@telconet.ec>
     * @version 1.1 2016-05-27 Remover la sección de nombre técnico 'is not null' para obtener los productos creados
     * 
     * @author Modificado: Duval Medina C. <dmedina@telconet.ec>
     * @version 1.2 2016-06-15 Lo máximo retornado sean 10
     */
    public function findTodosProductosPorEstadoYEmpresa($nombreTecnico, $estado, $idEmpresa, $limit, $page, $start)
    {
        $query = $this->_em->createQuery(null);
        $strSql = "SELECT ap
                   FROM   schemaBundle:AdmiProducto ap
                   WHERE  ap.estado     = :estado
                      AND ap.empresaCod = :empresaCod";
        $query->setParameter('estado', $estado);
        $query->setParameter('empresaCod', $idEmpresa);
        if($nombreTecnico!="Todos"){
            $strSql .= " AND ap.nombreTecnico = :nombreTecnico";
            $query->setParameter('nombreTecnico', $nombreTecnico);
        }
        $strSql .= " ORDER BY ap.descripcionProducto ASC";
        $query->setDQL($strSql);
        $query->setMaxResults(10);
        $total = count($query->getResult());
        $datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
        
        $resultado['registros'] = $datos;
        $resultado['total']     = $total;
        return $resultado;
    }

    /**
     * Documentación para el método 'getProductosAdicionales'.
     *
     * Método encargado de retornar los servicios asociados a la descripción enviada por parámetro.
     *
     * Costo 29
     *
     * @param array $arrayParametros [
     *                                  "arrayDescripcionCaracteristica" => Descripción de la caracteristica a buscar.
     *                                  "arrayValor"                     => valor del servicio a buscar.
     *                               ]
     *
     * @return array $arrayResultado arreglo de los servicios.
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.0 17-05-2021
     */
    public function getProductosAdicionales($arrayParametros)
    {
        $objRsm     = new ResultSetMappingBuilder($this->_em);
        $objQuery   = $this->_em->createNativeQuery(null, $objRsm);

        $strSql="SELECT
                pro.id_producto,
                pro.DESCRIPCION_PRODUCTO,
                REPLACE(pro.FUNCION_PRECIO,'\"','\"') FUNCION_PRECIO,
                pro.NOMBRE_TECNICO,
                CASE
                    WHEN nvl(imp.porcentaje_impuesto, 0) > 0 THEN
                        'S'
                    ELSE
                        'N'
                END AS porcentaje_impuesto,
                pro.GRUPO,
                pro.ESTADO,
                pro.FRECUENCIA,
                pro.REQUIERE_PLANIFICACION
            FROM
                db_comercial.admi_producto            pro
                LEFT JOIN db_comercial.info_producto_impuesto   imp ON pro.id_producto = imp.producto_id
                                                                    AND imp.impuesto_id = :impuestoId
                                                                    AND imp.estado = :estado
            WHERE
                pro.estado = :estado
                AND pro.nombre_tecnico <> :nombreTecnico
                AND pro.es_concentrador <> :esConcentrador
                AND pro.empresa_cod = :empresaCod
            ORDER BY
                pro.descripcion_producto ASC";

        $objQuery->setParameter("estado"        , $arrayParametros['strEstado']);
        $objQuery->setParameter("impuestoId"    , 1);
        $objQuery->setParameter("nombreTecnico" , $arrayParametros['strNombreTecnico']);
        $objQuery->setParameter("esConcentrador", $arrayParametros['strEsConcentrador']);
        $objQuery->setParameter("empresaCod"    , $arrayParametros['strCodEmpresa']);

        $objRsm->addScalarResult('ID_PRODUCTO'            ,'idProducto'             , 'integer');
        $objRsm->addScalarResult('DESCRIPCION_PRODUCTO'   ,'descripcionProducto'    , 'string');
        $objRsm->addScalarResult('FUNCION_PRECIO'         ,'funcionPrecio'          , 'string');
        $objRsm->addScalarResult('NOMBRE_TECNICO'         ,'nombreTecnico'          , 'string');
        $objRsm->addScalarResult('PORCENTAJE_IMPUESTO'    ,'porcentajeImpuesto'     , 'string');
        $objRsm->addScalarResult('GRUPO'                  ,'grupo'                  , 'string');
        $objRsm->addScalarResult('ESTADO'                 ,'estado'                 , 'string');
        $objRsm->addScalarResult('FRECUENCIA'             ,'frecuencia'             , 'string');
        $objRsm->addScalarResult('REQUIERE_PLANIFICACION' ,'requierePlanificacion'  , 'string');

        $objQuery->setSQL($strSql);
        $arrayRespuesta = $objQuery->getResult();

        return $arrayRespuesta;
    }

    /**
     * 
     * Metodo que devuelve los Productos por los criterios de busqueda
     * @param array $arrayParametros ['nombreTecnico','estado','fechaDesde','fechaHasta','strGrupo'
     *                                'empresa_cod','limit','start']
     * @return array $resultado
     * 
     * @author Telcos
     * @version 1.0 Version Inicial
     * 
     * @author Duval Medina C. <dmedina@telconet.ec>
     * @version 1.1 2016-05-26 Ajuste en seteo de parámetros enviados por consultas
     *              2016-05-31 Consolidar los parámetros de busqueda en un arreglo
     *                         Incluir la descripción del producto el los criterios
     *
     * @author Jorge Guerrero <jguerrerop@telconet.ec>
     * @version 1.2 2017-06-09
     * Se agrega el filtro Grupo
     */
    public function findTodosProductosPorEstadoYEmpresaCriterios($arrayParametros)
    {
        $query = $this->_em->createQuery(null);
        $strSql = "SELECT ap
                   FROM   schemaBundle:AdmiProducto ap
                   WHERE  ap.empresaCod =  :empresaCod";
        
        $query->setParameter('empresaCod', $arrayParametros['empresa_cod']);
        
        if($arrayParametros['descripcion']!="")
        {
            $strSql .= " AND UPPER(ap.descripcionProducto) like :descripcion";
            $query->setParameter('descripcion', "%".strtoupper($arrayParametros['descripcion'])."%");
        }
        if($arrayParametros['estado']!="")
        {
            $strSql .= " AND ap.estado = :estado";
            $query->setParameter('estado', $arrayParametros['estado']);
        }
        
        if($arrayParametros['nombreTecnico']!="Todos")
        {
            $strSql .= " AND ap.nombreTecnico = :nombreTecnico";
            $query->setParameter('nombreTecnico', $arrayParametros['nombreTecnico']);
        }
        if ($arrayParametros['strGrupo']!="")
        {
            $strSql .= " AND ap.grupo = :strGrupo";
            $query->setParameter('strGrupo', $arrayParametros['strGrupo']);
        }
        
        if($arrayParametros['fechaDesde']!="")
        {
            $strSql .= " AND ap.feCreacion >= :feDesde";
            $query->setParameter('feDesde', $arrayParametros['fechaDesde']);
        }
        if($arrayParametros['fechaHasta']!="")
        {
            $strSql .= " AND ap.feCreacion <= :feHasta";
            $query->setParameter('feHasta', $arrayParametros['fechaHasta']);
        }
        $strSql .= " ORDER BY ap.descripcionProducto ASC";

        $query->setDQL($strSql);
        $total = count($query->getResult());
        $datos = $query->setFirstResult( $arrayParametros['start'])
                        ->setMaxResults( $arrayParametros['limit'])->getResult();
        
        $resultado['registros'] = $datos;
        $resultado['total']     = $total;
        return $resultado;
    }
    
    /**
     * Documentación para la función 'findPorEmpresaYEstado'
     *
     * Metodo que busca productos por empresa y estado
     *
     * Actualizacion: Si el requerimiento es de Comercial se debe excluir 
     * sin importar la empresa los productos que tengan en campo 
     * nombre_tecnico='FINANCIERO', para esto se agrego el parametro $modulo
     * lo que permite diferenciar de que modulo es requerido los productos. 
     * 
     * @author Telcos
     * @version 1.0 Version Inicial
     * 
     * @author Andres Montero <amontero@telconet.ec>
     * @version 1.1 21/06/2016
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.2 16/01/2017 Para módulo comercial se modifica para que excluya productos que son concentrador.
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.3 16-02-2017 - Se valida cuando es el módulo 'Financiero' que no presente los productos con característica 'NO_FACTURABLE', para
     *                           que no puedan ser facturados al cliente.
     * Costo Query ($strModulo => "Financiero" && !empty($strEmpresaCod)): 19
     *
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.4 12-12-2018 Se valida para el caso del ingreso modulo "Comercial" que no aparezcan los productos de Catalogo que poseen la 
     *                         caracterista NO_VISIBLE_COMERCIAL.
     *                         En el caso del ingreso por modulo "Administracion" se valida que no aparezcan los productos de Catalogo que poseen
     *                         la caracteristica NO_VISIBLE_ADMINISTRACION
     * 
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.5 20-05-2021 - Se agrega un nuevo parámetro para obtener los productos de acuerdo al tipo de red(MPLS/GPON).
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.6 19-07-2021 - Se valida tipo red por deafult MPLS
     *
     * @param string $strEmpresaCod  Código de la empresa a consultar
     * @param string $strEstado      Estado de los productos a consultar
     * @param string $strModulo      Módulo desde el cual se invoca la función.
     * @param string $strTipoRed     Tipo de Red de los productos a consultar.
     * 
     * @return obj $objDatos
     */     
    public function findPorEmpresaYEstado($strEmpresaCod, $strEstado, $strModulo = 'Comercial',$strTipoRed='MPLS')
    {
        $objDatos = null;
            
        try
        {
            // crear query sin string dql
            $objQuery = $this->_em->createQuery();

            // definir string dql basico
            $strSelect  = "SELECT ap ";
            $strFrom    = "FROM schemaBundle:AdmiProducto ap ";
            $strJoin    = "";
            $strWhere   = "WHERE ap.estado = :estado ";
            $strOrderBy = "ORDER BY ap.descripcionProducto ASC ";

            // agregar parametros basicos al query
            $objQuery->setParameter("estado", $strEstado);

            if( !empty($strModulo) && $strModulo == "Financiero" )
            {
                $strWhere .= " AND ap.id NOT IN ( ".
                             "                      SELECT ap_s.id ".
                             "                      FROM schemaBundle:AdmiProductoCaracteristica apc ".
                             "                      JOIN apc.productoId ap_s ".
                             "                      JOIN apc.caracteristicaId ac ".
                             "                      WHERE ac.descripcionCaracteristica = :strDescripcionCaracteristica ".
                             "                      AND ac.estado = :strEstado ".
                             "                   ) ";
                $objQuery->setParameter("strDescripcionCaracteristica", 'NO_FACTURABLE');
                $objQuery->setParameter("strEstado",                    'Activo');  
            }

            //Si es comercial entonces excluye aquellos que tengan nombre_tecnico=FINANCIERO
            if( !empty($strModulo) && $strModulo=="Comercial")
            {
                $strWhere .= " AND ap.nombreTecnico <> :nombreTecnico AND ap.esConcentrador <> :esConcentrador ";
               
                $objQuery->setParameter("nombreTecnico", 'FINANCIERO');
                $objQuery->setParameter("esConcentrador", 'SI');                 
            }

            if( !empty($strEmpresaCod) )
            {
                // agregar condiciones opcionales al dql y parametros al query
                $strWhere .= " AND ap.empresaCod = :empresaId ";
                $objQuery->setParameter("empresaId", $strEmpresaCod);
            }
            
            if( !empty($strModulo) && ($strModulo=="Comercial" || $strModulo=="Financiero"))
            {
                $booleanTipoRedGpon = false;
                if(!empty($strTipoRed))
                {
                    $arrayParVerTipoRed = $this->_em->getRepository('schemaBundle:AdmiParametroDet')->getOne('NUEVA_RED_GPON_TN',
                                                                                                            'COMERCIAL',
                                                                                                            '',
                                                                                                            'VERIFICAR TIPO RED',
                                                                                                            'VERIFICAR_GPON',
                                                                                                            $strTipoRed,
                                                                                                            '',
                                                                                                            '',
                                                                                                            '');
                    if(isset($arrayParVerTipoRed) && !empty($arrayParVerTipoRed))
                    {
                        $booleanTipoRedGpon = true;
                    }
                }
                //se verifica el tipo de red es GPON
                if($booleanTipoRedGpon)
                {
                    $strRelacionProducto     = 'RELACION_PRODUCTO_CARACTERISTICA';
                    $strWhere               .= "AND ap.id IN ";
                }
                else
                {
                    $strTipoRed              = !empty($strTipoRed) ? $strTipoRed : "MPLS";
                    $strRelacionProducto     = 'PRODUCTO_NO_PERMITIDO_MPLS';
                    $strWhere               .= "AND ap.id NOT IN ";
                }
                $strWhere .= "( 
                                SELECT adProdPar.id
                                FROM
                                    schemaBundle:AdmiProducto     adProdPar,
                                    schemaBundle:AdmiParametroDet parDetPro,
                                    schemaBundle:AdmiParametroCab parCabPro
                                where 
                                    parDetPro.valor1              = adProdPar.id
                                    and parCabPro.id              = parDetPro.parametroId
                                    and parCabPro.nombreParametro = :strDescripcionParametro
                                    and parDetPro.valor3          = :strTipoRed
                                    and parDetPro.valor4          = :strEstadoProducto
                                    and parDetPro.valor5          = :strRelacionProducto
                                    and parDetPro.estado          = :strEstado
                               ) ";
                $strDescripcionParametro = 'NUEVA_RED_GPON_TN';
                $strEstadoProducto       = 'S';
                $strEstadoActivo         = 'Activo';
                $objQuery->setParameter("strTipoRed"             , $strTipoRed);
                $objQuery->setParameter("strEstadoProducto"      , $strEstadoProducto);
                $objQuery->setParameter("strEstado"              , $strEstadoActivo);
                $objQuery->setParameter("strRelacionProducto"    , $strRelacionProducto);
                $objQuery->setParameter("strDescripcionParametro", $strDescripcionParametro);

                 $strWhere .= " AND ap.id NOT IN ( " .
                "                      SELECT ap_sf.id " .
                "                      FROM schemaBundle:AdmiProductoCaracteristica apcf " .
                "                      JOIN apcf.productoId ap_sf " .
                "                      JOIN apcf.caracteristicaId acf " .
                "                      WHERE acf.descripcionCaracteristica = :strDescCarac " .
                "                      AND acf.estado = :strEstado " .
                "                   ) ";     
                $objQuery->setParameter("strDescCarac", 'NO_VISIBLE_COMERCIAL');
                $objQuery->setParameter("strEstado", 'Activo');
            }
            
            if( !empty($strModulo) && $strModulo=="Administracion")
            {
                $strWhere .= " AND ap.id NOT IN ( " .
                "                      SELECT ap_s.id " .
                "                      FROM schemaBundle:AdmiProductoCaracteristica apca " .
                "                      JOIN apca.productoId ap_s " .
                "                      JOIN apca.caracteristicaId ac " .
                "                      WHERE ac.descripcionCaracteristica = :strDescCarac " .
                "                      AND ac.estado = :strEstado " .
                "                   ) ";
                $objQuery->setParameter("strDescCarac", 'NO_VISIBLE_ADMINISTRACION');
                $objQuery->setParameter("strEstado", 'Activo');
            }            
            
            $strDql = $strSelect.$strFrom.$strJoin.$strWhere.$strOrderBy;

            // aplicar string dql al query y ejecutar
            $objQuery->setDQL($strDql);
            
            $objDatos = $objQuery->getResult();
        }
        catch( \Exception $e)
        {
            throw ($e);
        }

        return $objDatos;
    }
    
      
	public function findProductosActivadosPorRangoFechas($fechaIni,$fechaFin)
	{
		$query = $this->_em->createQuery(
					'SELECT b.descripcionProducto as producto, count(a.id) AS total
					FROM schemaBundle:InfoServicio a, schemaBundle:AdmiProducto b 
					WHERE a.productoId=b.id and a.feCreacion >= :fechaIni 
                                        AND a.feCreacion <= :fechaFin GROUP BY b.descripcionProducto'
				)
				//->groupBy('b.nombreProducto')
				->setParameter('fechaIni',$fechaIni)
				->setParameter('fechaFin',$fechaFin)
		;
                //echo $query->getSQL();die;
		return $query->getResult();
	}    
        
    public function findProductoXEmpresa($nombre='', $codEmpresa='', $start='', $limit='')
    {
        $arr_encontrados = array();
        
        $where = "";
        if($nombre && $nombre!="")
        {
            $where = "AND LOWER(p.descripcionProducto) like LOWER('%$nombre%') ";
        }
        
        $sql = "SELECT p 
                FROM 
                schemaBundle:AdmiProducto p, schemaBundle:InfoEmpresaGrupo eg 
                WHERE p.empresaCod = eg.id 
                AND eg.id = '$codEmpresa'        
                AND LOWER(p.estado) like LOWER('Activo')  
                AND LOWER(eg.estado) not like LOWER('Eliminado') 
                $where 
               ";
        
        $query = $this->_em->createQuery($sql);
       // $registros = $query->getResult();
        if($start!='' && $limit!='')
            $registros = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
        else if($start!='' && $limit=='')
            $registros = $query->setFirstResult($start)->getResult();
        else if($start=='' && $limit!='')
            $registros = $query->setMaxResults($limit)->getResult();
        else
            $registros = $query->getResult();
			
        if ($registros) {
            $num = count($registros);            
            foreach ($registros as $entity)
            {
                $arr_encontrados[]=array('id_producto' =>$entity->getId(),
                                         'producto' =>($entity->getDescripcionProducto())? $entity->getDescripcionProducto() : "");
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
     * Metodo que devuelve los clientes relacionados a un tipo de producto para efecto de la afectacion por caso       
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @since 10-01-2016
     * @version 1.1 
     *      
     * @author Telcos
     * @version 1.0 Version Inicial
     * 
     * @param integer $id_param
     * @param string $codEmpresa
     * @param integer $start
     * @param integer $limit
     * @return jsonResultado $resultado
     */
    public function generarJsonLoginesXProducto($id_param, $codEmpresa,$start,$limit)
    {
        $arrayEncontrados = array();
        
        $strSelectCont = " SELECT COUNT(pu) CONT ";
        
        $strSelect = "SELECT pu ";
        
        //QueryData Object para obtener total de registros
        $objQueryData = $this->_em->createQuery();
        
        //QueryData Object para obtener los registros
        $objQueryCont = $this->_em->createQuery();
        
        $strSql = "
                FROM 
                schemaBundle:AdmiProducto pr,
                schemaBundle:InfoPersona pe,
                schemaBundle:InfoPersonaEmpresaRol per,
                schemaBundle:InfoPunto pu,
                schemaBundle:InfoServicio s
                
                WHERE 
                s.productoId = pr.id 
                AND per.personaId = pe.id 
                AND pu.personaEmpresaRolId = per.id 
                AND s.puntoId = pu.id 
                AND pr.empresaCod = :empresa  
                AND pr.id = :producto 
        
                AND LOWER(s.estado) not like LOWER('Eliminado') 
                AND LOWER(pu.estado) not like LOWER('Eliminado') 
                AND LOWER(per.estado) not like LOWER('Eliminado') 
                AND LOWER(pe.estado) not like LOWER('Eliminado') 
                AND LOWER(pr.estado) not like LOWER('Eliminado') 
               ";
        
        $objQueryData->setParameter("empresa", $codEmpresa);
        $objQueryData->setParameter("producto", $id_param);
        $objQueryCont->setParameter("empresa", $codEmpresa);
        $objQueryCont->setParameter("producto", $id_param);
        
        //Calculamos el Total
        $objQueryCont->setDQL($strSelectCont.$strSql);
                
        $contResult = $objQueryCont->getResult();                  
        
        //Obtenemos todos los registros
        $objQueryData->setDQL($strSelect.$strSql);
        
        if($start!='' && $limit!='')
        {
            $registros = $objQueryData->setFirstResult($start)->setMaxResults($limit)->getResult();
        }
        else
        {
            $registros = $objQueryData->getResult();        
        }
        
        if ($registros) 
        {                      
            $num = $contResult[0]['CONT'];
            
            foreach ($registros as $data)
            {
                $idCliente = ($data->getPersonaEmpresaRolId() ? ($data->getPersonaEmpresaRolId()->getPersonaId() ?
                             ($data->getPersonaEmpresaRolId()->getPersonaId()->getId() ? $data->getPersonaEmpresaRolId()->getPersonaId()->getId() : "" )  : "") : "");
                $nombreCliente = ($data->getPersonaEmpresaRolId() ?  ($data->getPersonaEmpresaRolId()->getPersonaId() ?
                                 ($data->getPersonaEmpresaRolId()->getPersonaId()->getRazonSocial() ? $data->getPersonaEmpresaRolId()->getPersonaId()->getRazonSocial() : $data->getPersonaEmpresaRolId()->getPersonaId()->getNombres() . " " . $data->getPersonaEmpresaRolId()->getPersonaId()->getApellidos() ) : "") : "");
                
                $arrayEncontrados[]=array('id_parte_afectada' =>$data->getId(),
                                         'nombre_parte_afectada' =>$data->getLogin(),
                                         'id_descripcion_1' =>$idCliente,
                                         'nombre_descripcion_1' =>$nombreCliente,
                                         'id_descripcion_2' => '',
                                         'nombre_descripcion_2' =>'');
            }
            $dataF =json_encode($arrayEncontrados);
            $resultado= '{"total":"'.$num.'","encontrados":'.$dataF.'}';
            return $resultado;
        }
        else
        {
            $resultado= '{"total":"0","encontrados":[]}';
            return $resultado;
        }        
    }
    
    public function generarJsonElementosXProducto($id_param, $codEmpresa)
    {
        $arr_encontrados = array();                         
         
        $sql = "SELECT ie  
                FROM 
                schemaBundle:AdmiProducto pr,
                schemaBundle:InfoPersona pe,
                schemaBundle:InfoPersonaEmpresaRol per,
                schemaBundle:InfoPunto pu,
                schemaBundle:InfoServicio s,
                schemaBundle:InfoInterfaceElemento ie,
                schemaBundle:InfoElemento e,
                schemaBundle:InfoServicioTecnico ift
                
                WHERE 
                s.productoId = pr.id 
                AND per.personaId = pe.id 
                AND pu.personaEmpresaRolId = per.id 
                AND s.puntoId = pu.id 
                AND ift.interfaceElementoId = ie.id               
                AND ift.servicioId = s.id
                AND ie.elementoId = e.id 
                AND pr.empresaCod = '$codEmpresa'   
                AND pr.id = '$id_param'  
        
                AND LOWER(s.estado) not like LOWER('Eliminado') 
                AND LOWER(pu.estado) not like LOWER('Eliminado') 
                AND LOWER(per.estado) not like LOWER('Eliminado') 
                AND LOWER(pe.estado) not like LOWER('Eliminado') 
                AND LOWER(pr.estado) not like LOWER('Eliminado') 
               ";
        
        $query = $this->_em->createQuery($sql);
        $registros = $query->getResult();                
        
        if ($registros) {
            $num = count($registros);  
            
            foreach ($registros as $entity)
            {
                $arr_encontrados[]=array('id_parte_afectada' =>$entity->getElementoId()->getId(),
                                         'nombre_parte_afectada' =>$entity->getElementoId()->getNombreElemento(),
                                         'id_descripcion_1' =>$entity->getId(),
                                         'nombre_descripcion_1' =>$entity->getNombreInterfaceElemento(),
                                         'id_descripcion_2' => '',
                                         'nombre_descripcion_2' =>'');
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

    /*
     * Documentación para el método 'generarJsonProductosPorEstado'.
     *
     * Metodo para obtener el json del listado de los productos
     *
     * @version 1.0 - Función no documentada
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.1 04-03-2020 - Se agrega la variable $strQuery para que la consulta
     *                           se pueda filtrar por la descripción del producto
     *
     * @param Array $arrayParametros [
     *                                  'strEmpresa' => id de la empresa
     *                                  'strQuery'   => filtrar por el nombre del producto
     *                                  'strEstado'  => estado del producto
     *                                  'strStart'   => inicio de la consulta
     *                                  'strLimit'   => limite de la consulta
     *                              ]
     *
     * @return String $strResultado
    */
    public function generarJsonProductosPorEstado($arrayParametros)
    {
        $arrayEncontrados    = array();
        $arrayParametroTotal = array(
            'strEmpresa' => $arrayParametros['strEmpresa'],
            'strQuery'   => $arrayParametros['strQuery'],
            'strEstado'  => $arrayParametros['strEstado'],
            'strStart'   => '',
            'strLimit'   => '',
        );
        $arrayProductosTotal = $this->getProductosPorEstado($arrayParametroTotal);
        $arrayProductos      = $this->getProductosPorEstado($arrayParametros);
        if( isset($arrayProductos) && !empty($arrayProductos) )
        {
            $intTotal = count($arrayProductosTotal);
            foreach ($arrayProductos as $objProducto)
            {
                $arrayEncontrados[] = array(
                                        'idProducto'          => $objProducto->getId(),
                                        'descripcionProducto' => trim($objProducto->getDescripcionProducto())
                                      );
            }

            if( $intTotal == 0 )
            {
                $strResultado = '{"total":"0","encontrados":[]}';
                return $strResultado;
            }
            else
            {
                $strData      = json_encode($arrayEncontrados);
                $strResultado = '{"total":"'.$intTotal.'","encontrados":'.$strData.'}';
                return $strResultado;
            }
        }
        else
        {
            $strResultado = '{"total":"0","encontrados":[]}';
            return $strResultado;
        }
        
    }

     /**
     * Metodo que devuelve los Productos por los criterios de busqueda
     *
     * @param array  $arrayParametro
     * @return array $resultado
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.0
     * @since 20/05/2021
     */
    public function findTodosProductosCaracComp($arrayParametro)
    {
        $objQuery = $this->_em->createQuery(null);
        $strSql = "SELECT ap
                   FROM   schemaBundle:AdmiProducto ap
                   WHERE  ap.estado     = :estado
                      AND ap.empresaCod = :empresaCod
                      AND ap.esEnlace   = :esEnlace";
        $objQuery->setParameter('estado',     $arrayParametro['estado']);
        $objQuery->setParameter('empresaCod', $arrayParametro['idEmpresa']);
        $objQuery->setParameter('esEnlace'  , $arrayParametro['esEnlace']);
        if($arrayParametro['nombreTecnico'] !="Todos")
        {
            $strSql .= " AND ap.nombreTecnico = :nombreTecnico";
            $objQuery->setParameter('nombreTecnico', $arrayParametro['nombreTecnico']);
        }
        $strSql .= " ORDER BY ap.descripcionProducto ASC";
        $objQuery->setDQL($strSql);

        $intTotal   = count($objQuery->getResult());
        $arrayDatos = $objQuery->setFirstResult($arrayParametro['start'])
                          ->setMaxResults($arrayParametro['limit'])
                          ->getResult();

        $arrayResultado['registros'] = $arrayDatos;
        $arrayResultado['total']     = $intTotal;
        return $arrayResultado;
    }

    /*
     * Documentación para el método 'getProductosPorEstado'.
     *
     * Metodo para obtener el listado de los productos
     *
     * @version 1.0 - Función no documentada
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.1 04-03-2020 - Se agrega la variable $strQuery para que la consulta
     *                           se pueda filtrar por la descripción del producto
     *
     * @param Array $arrayParametros [
     *                                  'strEmpresa' => id de la empresa
     *                                  'strQuery'   => filtrar por el nombre del producto
     *                                  'strEstado'  => estado del producto
     *                                  'strStart'   => inicio de la consulta
     *                                  'strLimit'   => limite de la consulta
     *                              ]
     *
     * @return Array $resultados
    */
    public function getProductosPorEstado($arrayParametros)
    {
        $strEmpresa = $arrayParametros['strEmpresa'];
        $strQuery   = $arrayParametros['strQuery'];
        $strEstado  = $arrayParametros['strEstado'];
        $strStart   = $arrayParametros['strStart'];
        $strLimit   = $arrayParametros['strLimit'];
        $objQueryBuilder = $this->_em->createQueryBuilder();
        $objQueryBuilder->select('e')->from('schemaBundle:AdmiProducto','e');
        if( isset($strEmpresa) && !empty($strEmpresa) )
        {
            $objQueryBuilder->andWhere('e.empresaCod = ?2');
            $objQueryBuilder->setParameter(2, $strEmpresa);
        }
        if( isset($strEstado) && !empty($strEstado) )
        {
            $objQueryBuilder->andWhere('e.estado = ?1');
            $objQueryBuilder->setParameter(1, $strEstado);
        }
        if( isset($strQuery) && !empty($strQuery) )
        {
            $objQueryBuilder->andWhere('UPPER(e.descripcionProducto) LIKE ?3');
            $objQueryBuilder->setParameter(3, '%'.strtoupper($strQuery).'%');
        }
        $objQueryBuilder->orderBy('e.descripcionProducto', 'ASC');
        if( isset($strStart) )
        {
            $objQueryBuilder->setFirstResult($strStart);
        }
        if( isset($strLimit) )
        {
            $objQueryBuilder->setMaxResults($strLimit);
        }
        $objQuery = $objQueryBuilder->getQuery();
        return $objQuery->getResult();
    }
    
    /**
     * 
     * Metodo que obtiene el json de los servicios afectados por cliente en un caso creado para calculo de SLA
     * 
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.1 25-05-2016 - Se agrega login Aux en la respuesta de la busqueda
     * 
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 12-12-2015
     * 
     * @param integer $intIdPunto
     * @param integer $intIdServicio
     * @return $jsonData
     */
    public function getJsonServiciosAfectadosSla($intIdPunto,$intIdServicio)    
    {
        $arrayResultado = $this->getResultadoServiciosAfectadosSla($intIdPunto,$intIdServicio);        
        
        $total     = $arrayResultado['total'];                        
        $resultado = $arrayResultado['resultado'];
        
        if($resultado)
        {
            foreach($resultado as $data)
            {
                $arrayEncontrados[] = array( 
                                            "idServicio"    => $data['idServicio'],
                                            "nombreProducto"=> $data['nombreProducto'],
                                            "estado"        => $data['estado'],
                                            "loginAux"      => $data['loginAux']
                );
            }

            $arrayRespuesta = array('total'=> $total , 'encontrados' => $arrayEncontrados);                                            
        }
        else
        {
            $arrayRespuesta = array('total'=> 0 , 'encontrados' => '[]');                                            
        }
        
        $jsonData       = json_encode($arrayRespuesta);
        return $jsonData;
    }
    
    /**
     * 
     * Metodo que obtiene el resultado del query de los servicios afectados por cliente en un caso creado para calculo de SLA
     * 
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.1 25-05-2016 - Se agrega login Aux en la respuesta de la busqueda
     * 
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 12-12-2015
     * 
     * @param $intIdPunto
     * @param $intIdServicio
     * @return $arrayResultado [ total , resultado ]
     */
    public function getResultadoServiciosAfectadosSla($intIdPunto,$intIdServicio)    
    {
        $arrayResultado = array();
        
        try
        {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $query = $this->_em->createNativeQuery(null, $rsm);

            $strWhere = "";

            if(isset($intIdServicio) && $intIdServicio!=null)
            {
                $strWhere .= " AND ISR.ID_SERVICIO = :servicio ";
                $query->setParameter('servicio',$intIdServicio);    
            }

            $strSelectCont = " SELECT COUNT(*) CONT FROM ";

            $strSql   = "(SELECT 
                        AP.DESCRIPCION_PRODUCTO,
                        ISR.ESTADO,
                        ISR.ID_SERVICIO,
                        ISR.LOGIN_AUX
                      FROM INFO_SERVICIO ISR,
                        INFO_PLAN_CAB IPC,
                        INFO_PLAN_DET IPD,
                        ADMI_PRODUCTO AP
                      WHERE ISR.PLAN_ID   = IPC.ID_PLAN
                      AND IPC.ID_PLAN     = IPD.PLAN_ID
                      AND IPD.PRODUCTO_ID = AP.ID_PRODUCTO                  
                      AND ISR.PUNTO_ID    = :punto
                      AND ISR.ESTADO      = :estado
                      $strWhere
                      UNION
                      SELECT 
                        AP.DESCRIPCION_PRODUCTO,    
                        ISR.ESTADO,
                        ISR.ID_SERVICIO,
                        ISR.LOGIN_AUX
                      FROM INFO_SERVICIO ISR,
                        ADMI_PRODUCTO AP
                      WHERE ISR.PRODUCTO_ID = AP.ID_PRODUCTO                   
                      AND ISR.PUNTO_ID      = :punto 
                      AND ISR.ESTADO        = :estado
                      $strWhere
                      )";                        

            $rsm->addScalarResult('ID_SERVICIO', 'idServicio', 'integer');
            $rsm->addScalarResult('DESCRIPCION_PRODUCTO', 'nombreProducto', 'string');
            $rsm->addScalarResult('ESTADO', 'estado', 'string');
            $rsm->addScalarResult('LOGIN_AUX', 'loginAux', 'string');
            $rsm->addScalarResult('CONT', 'cont', 'integer');

            $query->setParameter('punto',$intIdPunto);  
            $query->setParameter('estado','Activo');  

            $query->setSQL($strSelectCont.$strSql);

            $arrayResultado['total'] = $query->getSingleScalarResult();

            $query->setSQL($strSql);                        

            $arrayResultado['resultado'] = $query->getArrayResult();
        
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
        }
        
        return $arrayResultado;
    }
    
    /**
     * Funcion que verifica si el plan a ingresar posee un producto IP, si es el caso valida que se ingrese el numero de IPS maximas permitidas.'     
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 23-05-2014
     * @param integer $intIdProducto     
     * @see \telconet\schemaBundle\Entity\AdmiTipoCuenta
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function validaTieneProductoIp($intIdProducto)
    {   
        $em = $this->_em; 
        $sql = $em->createQuery("SELECT p  
              from                                                           
              schemaBundle:AdmiProducto p         
              where                
              p.id =:intIdProducto                
              and p.nombreTecnico =:nombre_tecnico                                    
                 ");                
             $sql->setParameter( 'intIdProducto' , $intIdProducto);                
             $sql->setParameter( 'nombre_tecnico' , 'IP');    
             $productoIP = $sql->getOneOrNullResult();
             if(!$productoIP)
             {
                 $productoIP = 0;
             }  
             return $productoIP;
    }
    /**
     * 
     * Metodo que devuelve los estados de los Productos
     * 
     * @param string  $empresaCod
     * @return array $resultado
     * 
     * @author Duval Medina C. <dmedina@telconet.ec>
     * @version 1.0 
     * 
     */
    
    public function findEstadosPorEmpresa($empresaCod)
    {
        $query = $this->_em->createQuery(
                    "SELECT DISTINCT ap.estado
                         FROM schemaBundle:AdmiProducto ap
                         WHERE ap.empresaCod = :empresaCod
                         ORDER BY ap.estado"
                )
                ->setParameter("empresaCod",$empresaCod)
        ;
        return $query->getResult();
    }
    
    /**
     * 
     * Costo: 37
     * getProductosTradicionales
     *
     * Método encargado de obtener los productos tradicionales para Demos
     *
     * @param array  $arrayParametros [ strEmpresaCod       => codigo de la empresa
     *                                  strGrupo            => grupo de productos
     *                                  strSoporteMasivo    => bandera que determina si el producto es proceso masivo
     *                                  strEstadoServicio   => estado de los servicios
     *                                  strNombreTecnico    => nombre técnico del producto ]
     *
     * @return array $arrayProductos
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 28-06-2017
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 09-08-2017 - Se realizan ajustes en el query que retorna los productos, ya no se considera el estado de los productos,
     *                           se valida que tengan servicios activos,se valida por proceso masivo y por el nombre técnico
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.2 03-01-2018 - Se agrega parametro para filtrar productos por nombre tecnico OTROS, esto adicional a los filtros ya existentes
     * @since 1.1 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 14-10-2020 Se modifica la consulta evitando realizar comparaciones con el grupo del producto, ya que este campo puede ser
     *                         cambiado por solicitud del área de productos, lo que a su vez afectaría al flujo de traslados y demos
     *                         Costo = 48
     * 
     */
    public function getProductosTradicionales($arrayParametros)
    {
        $arrayProductos     = array();
        $objRsmb            = new ResultSetMappingBuilder($this->_em);
        $objQuery           = $this->_em->createNativeQuery(null,$objRsmb);

        $strSql = " SELECT 
                        PROD.ID_PRODUCTO,
                        PROD.DESCRIPCION_PRODUCTO,
                        PROD.ES_ENLACE,
                        PROD.SOPORTE_MASIVO,
                        PROD.ESTADO,
                        PROD.NOMBRE_TECNICO,
                        PROD.FUNCION_PRECIO,
                        '1' as TOTAL_MISMO_NOMBRE_TECNICO
                    FROM 
                        ADMI_PRODUCTO PROD
                    WHERE 
                        PROD.EMPRESA_COD = :paramCodEmpresa 
                        AND PROD.SOPORTE_MASIVO = :paramProcesoMasivo
                        AND PROD.NOMBRE_TECNICO <> :paramNombreTecnico
                        AND PROD.ID_PRODUCTO IN ( SELECT PRODUCTO_ID FROM INFO_SERVICIO WHERE ESTADO = :paramEstadoServicio ) ";
        
        if (!empty($arrayParametros["strFiltroOtros"]))
        {
            $strSql .= " AND PROD.NOMBRE_TECNICO <> :paramNomTecOtros ";
            $objQuery->setParameter('paramNomTecOtros',$arrayParametros["strFiltroOtros"]);
        }
        
        if(isset($arrayParametros["strEsProductoTradicional"]) && !empty($arrayParametros["strEsProductoTradicional"])
            && $arrayParametros["strEsProductoTradicional"] === "SI")
        {
            $strSql .= " AND EXISTS (
                            SELECT APC.ID_PRODUCTO_CARACTERISITICA
                            FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA APC
                            INNER JOIN DB_COMERCIAL.ADMI_CARACTERISTICA CARACT
                            ON CARACT.ID_CARACTERISTICA = APC.CARACTERISTICA_ID
                            WHERE APC.PRODUCTO_ID = PROD.ID_PRODUCTO
                            AND CARACT.DESCRIPCION_CARACTERISTICA = :paramEsProductoTradicional 
                            AND APC.ESTADO = :paramEstadoApcProdTradicional ) ";
            $objQuery->setParameter('paramEsProductoTradicional', 'ES_PRODUCTO_TRADICIONAL');
            $objQuery->setParameter('paramEstadoApcProdTradicional', 'Activo');
        }
        
        if(isset($arrayParametros["strOmiteProductoRestringidoDemo"]) && !empty($arrayParametros["strOmiteProductoRestringidoDemo"])
            && $arrayParametros["strOmiteProductoRestringidoDemo"] === "SI")
        {
            $strSql .= " AND NOT EXISTS (
                            SELECT APC.ID_PRODUCTO_CARACTERISITICA
                            FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA APC
                            INNER JOIN DB_COMERCIAL.ADMI_CARACTERISTICA CARACT
                            ON CARACT.ID_CARACTERISTICA = APC.CARACTERISTICA_ID
                            WHERE APC.PRODUCTO_ID = PROD.ID_PRODUCTO
                            AND CARACT.DESCRIPCION_CARACTERISTICA = :paramEsProductoRestringidoDemo
                            AND APC.ESTADO = :paramEstadoApcProdRestringDemo ) ";
            $objQuery->setParameter('paramEsProductoRestringidoDemo', 'ES_PRODUCTO_RESTRINGIDO_DEMO');
            $objQuery->setParameter('paramEstadoApcProdRestringDemo', 'Activo');
        }
        
        $objQuery->setParameter('paramCodEmpresa',$arrayParametros["strEmpresaCod"]);
        $objQuery->setParameter('paramProcesoMasivo',$arrayParametros["strSoporteMasivo"]);
        $objQuery->setParameter('paramNombreTecnico',$arrayParametros["strNombreTecnico"]);
        $objQuery->setParameter('paramEstadoServicio',$arrayParametros["strEstadoServicio"]);
        
        $objRsmb->addScalarResult('ID_PRODUCTO', 'idProducto','integer');
        $objRsmb->addScalarResult('DESCRIPCION_PRODUCTO', 'descripcionProducto','string');
        $objRsmb->addScalarResult('ES_ENLACE', 'esEnlace','string');
        $objRsmb->addScalarResult('SOPORTE_MASIVO', 'soporteMasivo','string');
        $objRsmb->addScalarResult('ESTADO', 'estado','integer');
        $objRsmb->addScalarResult('NOMBRE_TECNICO', 'nombreTecnico', 'string');
        $objRsmb->addScalarResult('FUNCION_PRECIO', 'funcionPrecio', 'string');
        $objRsmb->addScalarResult('TOTAL_MISMO_NOMBRE_TECNICO', 'totalMismoNombreTecnico', 'string');

        $objQuery->setSQL($strSql);

        $arrayProductos['registros'] = $objQuery->getScalarResult();


        return $arrayProductos;
    }

    
    /**
     * findProductos, obtiene servicios enviando criterios de busqueda
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 21-06-2016
     * @since 1.0
     * @param array  $arrayParametros Obtiene los criterios de busqueda
     * @return array $arrayResultado  Retorna el array de datos y conteo de datos
     * 
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.1 03-08-2016 - Se añadio conector en caso de no tener arrayComparador
     * 
     */
    public function findProductos($arrayParametros) {
        
        $rsm   = new ResultSetMappingBuilder($this->_em);
        $query = $this->_em->createNativeQuery(null,$rsm);
        //Query que obtiene los Datos
        $sqlSelect      =   "SELECT AP.* ";

        $sqlSubSelect   =   ", (SELECT COUNT(PROD.ID_PRODUCTO) 
                                FROM ADMI_PRODUCTO PROD
                                WHERE PROD.EMPRESA_COD  = :intIdEmpresa                                
                                AND PROD.NOMBRE_TECNICO = AP.NOMBRE_TECNICO ";
        //Cuerpo del Query
        $sqlFrom = "FROM ADMI_PRODUCTO AP  ";
        
        
        $sqlWhere = "WHERE 
                    AP.EMPRESA_COD = :intIdEmpresa ";
        
        $query->setParameter('intIdEmpresa', $arrayParametros['intIdEmpresa']);
        
        $sqlWhere1= "";        
        
        if (!empty($arrayParametros['strCodigoProducto'])){
            //cuerpo del query
            $sqlWhere1 .= empty($sqlWhere1) ? " " : "AND " ;
            $sqlWhere1 .= "AP.CODIGO_PRODUCTO  = :strCodigoProducto ";
            //query de datos
            $query->setParameter('strCodigoProducto', $arrayParametros['strCodigoProducto']);
        }
        
        if (!empty($arrayParametros['strDescripcionProducto'])){
            //cuerpo del query
            $sqlWhere1 .= empty($sqlWhere1) ? " " : "AND " ;
            $sqlWhere1 .= "AP.DESCRIPCION_PRODUCTO like :strDescripcionProducto ";
            //query de datos
            $query->setParameter('strDescripcionProducto', '%' . $arrayParametros['strCodigoProducto']) . '%';
        }
        
        if (!empty($arrayParametros['strEstado'])){
            //cuerpo del query
            $sqlWhere1      .= empty($sqlWhere1) ? " " : "AND " ;
            $sqlWhere1      .= "AP.ESTADO  = :strEstado ";            
            $sqlSubSelect   .= " AND PROD.ESTADO =:strEstado ";
            //query de datos
            $query->setParameter('strEstado', $arrayParametros['strEstado']);
        }
        
        if (!empty($arrayParametros['strEsEnlace'])){
            //cuerpo del query
            $sqlWhere1 .= empty($sqlWhere1) ? " " : "AND " ;
            $sqlWhere1 .= "AP.ES_ENLACE  = :strEsEnlace ";
            //query de datos
            $query->setParameter('strEsEnlace', $arrayParametros['strEsEnlace']);
        }
        
        if (!empty($arrayParametros['strTipo'])){
            //cuerpo del query
            $sqlWhere1 .= empty($sqlWhere1) ? " " : "AND " ;
            $sqlWhere1 .= "AP.TIPO  = :strTipo ";
            //query de datos
            $query->setParameter('strTipo', $arrayParametros['strTipo']);
        }
        
        if (!empty($arrayParametros['strNombreTecnico'])){
            //cuerpo del query
            $sqlWhere1 .= empty($sqlWhere1) ? " " : "AND " ;
            $sqlWhere1 .= "AP.NOMBRE_TECNICO  = :strNombreTecnico ";
            //query de datos
            $query->setParameter('strNombreTecnico', $arrayParametros['strNombreTecnico']);
        }
        
        if (!empty($arrayParametros['strSoporteMasivo'])){
            //cuerpo del query
            $sqlWhere1      .= empty($sqlWhere1) ? " " : "AND " ;
            $sqlWhere1      .= "AP.SOPORTE_MASIVO  = :strSoporteMasivo ";
            $sqlSubSelect   .= " AND PROD.SOPORTE_MASIVO = :strSoporteMasivo ";
            //query de datos
            $query->setParameter('strSoporteMasivo', $arrayParametros['strSoporteMasivo']);
        }
        
        if (!empty($arrayParametros['strEsConcentrador'])){
            //cuerpo del query
            $sqlWhere1 .= empty($sqlWhere1) ? " " : "AND " ;
            $sqlWhere1 .= "AP.ES_CONCENTRADOR  = :strEsConcentrador ";
            //query de datos
            $query->setParameter('strEsConcentrador', $arrayParametros['strEsConcentrador']);
        }
        
        $sqlWhereComplemento = "";
        
        if(!empty($arrayParametros['arrayComparador']) && !empty($sqlWhere1))
        {
            $sqlWhereComplemento .= " AND (" .$sqlWhere1 .") ";
        }
        else if(!empty($sqlWhere1))
        {
            $sqlWhereComplemento .= $sqlWhere1;
        }
        
        $sqlWhere2="";
        $boolTieneElementosComparador = false;
        if(!empty($arrayParametros['arrayComparador']))
        {
            if(empty($arrayParametros['arrayComparador']['strOperador']))
            {
                $arrayParametros['arrayComparador']['strOperador'] = 'AND';
            }
            
            $sqlWhere2.= " ".$arrayParametros['arrayComparador']['strOperador'];
            
            if(!empty($arrayParametros['arrayComparador']['strNombreTecnico']))
            {
                //cuerpo del query
                $sqlWhere2 .= " AP.NOMBRE_TECNICO  = :strNombreTecnico2 ";
                //query de datos
                $query->setParameter('strNombreTecnico2', $arrayParametros['arrayComparador']['strNombreTecnico']);
                $boolTieneElementosComparador = true;
            }
        }
        
        if($boolTieneElementosComparador)
        {
            $sqlWhereComplemento .= $sqlWhere2;
        }
        else
        {
            $sqlWhereComplemento = " AND " . $sqlWhereComplemento;
        }
        
        $sqlSubSelect .= " GROUP BY PROD.NOMBRE_TECNICO) TOTAL_MISMO_NOMBRE_TECNICO ";
        
        $sql = $sqlSelect.$sqlSubSelect.$sqlFrom.$sqlWhere.$sqlWhereComplemento;
        
        $rsm->addScalarResult('ID_PRODUCTO', 'idProducto','integer');
        $rsm->addScalarResult('EMPRESA_COD', 'empresaCod','string');
        $rsm->addScalarResult('DESCRIPCION_PRODUCTO', 'descripcionProducto','string');
        $rsm->addScalarResult('FUNCION_COSTO', 'funcionCosto','string');
        $rsm->addScalarResult('INSTALACION', 'instalacion','integer');
        $rsm->addScalarResult('ESTADO', 'estado', 'string');
        $rsm->addScalarResult('CTA_CONTABLE_PROD', 'ctaContableProd', 'string');
        $rsm->addScalarResult('CTA_CONTABLE_PROD_NC', 'ctaContableProdNc', 'string');
        $rsm->addScalarResult('ES_PREFERENCIA', 'esPreferencia', 'string');
        $rsm->addScalarResult('ES_ENLACE', 'esEnlace', 'string');
        $rsm->addScalarResult('REQUIERE_PLANIFICACION', 'requierePlanificacion', 'string');
        $rsm->addScalarResult('REQUIERE_INFO_TECNICA', 'requiereInfoTecnica', 'string');
        $rsm->addScalarResult('NOMBRE_TECNICO', 'nombreTecnico', 'string');
        $rsm->addScalarResult('CTA_CONTABLE_DESC', 'ctaContableDesc', 'string');
        $rsm->addScalarResult('TIPO', 'tipo', 'string');
        $rsm->addScalarResult('ES_CONCENTRADOR', 'esConcentrador', 'string');
        $rsm->addScalarResult('FUNCION_PRECIO', 'funcionPrecio', 'string');
        $rsm->addScalarResult('SOPORTE_MASIVO', 'soporteMasivo', 'string');
        $rsm->addScalarResult('ESTADO_INICIAL', 'estadoInicial', 'string');
        $rsm->addScalarResult('TOTAL_MISMO_NOMBRE_TECNICO', 'totalMismoNombreTecnico', 'int');

        $query->setSQL($sql);
        $total=count($query->getScalarResult());
        if(!empty($arrayParametros['intStart']) && !empty($arrayParametros['intLimit'])) {
            $query->setParameter('start', $arrayParametros['intStart']+1);
            $query->setParameter('limit', ($arrayParametros['intStart'] + $arrayParametros['intLimit'])); 
            $sql = "SELECT a.*, rownum as intDoctrineRowNum FROM (".$sql.") a WHERE ROWNUM <= :limit";
            if($arrayParametros['intStart'] > 0) {
                $sql = "SELECT * FROM (".$sql.") WHERE intDoctrineRowNum >= :start";
            }
            $query->setSQL($sql);
        }
        $datos                  = $query->getScalarResult();
        $resultado['registros'] = $datos;
        $resultado['total']     = $total;
        return $resultado;
    }
    /**           
     * Documentación para el método 'getJsonTiposConcentradores'.
     *
     * Método utilizado para obtener los Tipos de Concentradores Disponibles segun el campo Clasificacion 
     * del Producto del servicio del Punto extremo
     *     
     * @param int idServicio id del servicio extremo del Enlace
     *
     * @return $arrayResultadoJson 
     *         JsonResponse [{                       
     *                      'encontrados'  : [{
     *                                          'idTipoConcentrador':'',
     *                                          'tipoConcentrador'  :''
     *                                  }]
     *                      }]
     *
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 29-09-2016  
    */
    public function getJsonTiposConcentradores($strClasificacion,$strCodEmpresa)
    {
        $arrayTiposEncontrados = array();
        
        $arrayTiposConcentradores = $this->finTiposConcentradores($strClasificacion,$strCodEmpresa);

        if ($arrayTiposConcentradores)
        {                       
            foreach ($arrayTiposConcentradores as $tipoConcentrador)
            {
                $arrayTiposEncontrados[]   = array('idTipoConcentrador' => trim($tipoConcentrador['tipoConcentrador']),
                                                   'tipoConcentrador'   => trim($tipoConcentrador['tipoConcentrador']));
                                
            }
            $arrayTiposConcentradoresJson = json_encode(array('encontrados' => $arrayTiposEncontrados));
	        
            return $arrayTiposConcentradoresJson;            
        }
        else
        {            
            $arrayTiposEncontrados[]      = array('encontrados' => array());
            $arrayTiposConcentradoresJson = json_encode($arrayTiposEncontrados);

            return $arrayTiposConcentradoresJson;            
        }
        
    }

    /**           
     * Documentación para el método 'finTiposConcentradores'.
     *
     * Método utilizado para obtener los Tipos de Concentradores Disponibles segun el campo Clasificacion 
     * del Producto del servicio del Punto extremo
     *     
     * @param string $strClasificacion 
     * @param string $strCodEmpresa 
     *
     * @return $arrayDatos
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 29-09-2016  
    */
    public function finTiposConcentradores($strClasificacion,$strCodEmpresa)
    {        
        $strQuery  = $this->_em->createQuery(       
                         "SELECT DISTINCT p.nombreTecnico as tipoConcentrador     
                            FROM schemaBundle:AdmiProducto p
                            where p.clasificacion = :strClasificacion
                               AND p.empresaCod = :strCodEmpresa"); 
        
        $strQuery->setParameter('strClasificacion', $strClasificacion);        
        $strQuery->setParameter('strCodEmpresa', $strCodEmpresa);
        $arrayDatos = $strQuery->getResult();
        return $arrayDatos;
    }
    
    /**
     * getResultadoComisionPlantilla
     * 
     * Obtiene la plantilla de Comisionistas por GRUPO_ROLES_PERSONAL a nivel de Parametros y definido para un producto especifico     
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 20-03-2017
     * costoQuery: 14
     * @param  array $arrayParametros [
     *                                  "intIdProducto"     : Id del producto     
     *                                  "strCodEmpresa"     : Empresa Cod   
     *                                ]          
     * 
     * @return json $arrayResultado
     */
    public function getResultadoComisionPlantilla($arrayParametros)
    {     
        $objRsmCount      = new ResultSetMappingBuilder($this->_em);
        $objNtvQueryCount = $this->_em->createNativeQuery(null, $objRsmCount);
        
        $strSqlCantidad   = ' SELECT COUNT(*)  AS TOTAL '; 
                        
        $objRsm           = new ResultSetMappingBuilder($this->_em);
        $objNtvQuery      = $this->_em->createNativeQuery(null, $objRsm);
        
        $strSqlDatos      = 'SELECT COMD.ID_COMISION_DET AS ID_DETALLE,PARD.ID_PARAMETRO_DET,PARD.DESCRIPCION, PARD.VALOR1,'
                          . ' COMD.COMISION_VENTA AS COMISION_VTA, PARD.VALOR2, PARD.VALOR3, PARD.VALOR4, PARD.VALOR5 ';
        
        $strSqlFrom1       = ' FROM DB_COMERCIAL.ADMI_COMISION_CAB COMC,
                                   DB_COMERCIAL.ADMI_COMISION_DET COMD,
                                   DB_COMERCIAL.ADMI_PRODUCTO PROD,
                                   DB_GENERAL.ADMI_PARAMETRO_CAB PARC,
                                   DB_GENERAL.ADMI_PARAMETRO_DET PARD
                              WHERE
                              PROD.ID_PRODUCTO          = :intIdProducto
                              AND PROD.ESTADO           IN (:arrayEstados)
                              AND COMC.ESTADO           = :strEstadoActivo 
                              AND COMD.ESTADO           = :strEstadoActivo 
                              AND PARC.NOMBRE_PARAMETRO = :strDescripcionParametro 
                              AND PARC.ESTADO           = :strEstadoActivo
                              AND PARD.EMPRESA_COD      = :strCodEmpresa   
                              AND PROD.ID_PRODUCTO      = COMC.PRODUCTO_ID
                              AND COMC.ID_COMISION      = COMD.COMISION_ID
                              AND PARC.ID_PARAMETRO     = PARD.PARAMETRO_ID
                              AND COMD.PARAMETRO_DET_ID = PARD.ID_PARAMETRO_DET ';
        
        $strSqlUnion       = ' UNION ';
        $strSqlDatosUnion  = ' SELECT NULL AS ID_DETALLE, DET.ID_PARAMETRO_DET,DET.DESCRIPCION,DET.VALOR1,NULL AS COMISION_VTA,'
                           . ' DET.VALOR2, DET.VALOR3, DET.VALOR4, DET.VALOR5';
        $strSqlFrom2       = ' FROM DB_GENERAL.ADMI_PARAMETRO_CAB CAB,
                                DB_GENERAL.ADMI_PARAMETRO_DET DET
                                WHERE 
                                CAB.NOMBRE_PARAMETRO   = :strDescripcionParametro 
                                AND CAB.ESTADO         = :strEstadoActivo
                                AND DET.ESTADO         = :strEstadoActivo
                                AND DET.EMPRESA_COD    = :strCodEmpresa   
                                AND CAB.ID_PARAMETRO   = DET.PARAMETRO_ID
                                AND NOT EXISTS (SELECT 1 FROM DB_COMERCIAL.ADMI_COMISION_DET CDET,
                                                DB_COMERCIAL.ADMI_COMISION_CAB  CCAB 
                                                WHERE CDET.PARAMETRO_DET_ID = DET.ID_PARAMETRO_DET
                                                AND CCAB.ID_COMISION        = CDET.COMISION_ID
                                                AND CCAB.PRODUCTO_ID        = :intIdProducto
                                                AND CDET.ESTADO             = :strEstadoActivo
                                                )
                              ';                 
        $strSqlOrderBy    = " ORDER BY 9 ASC ";
        
        $objRsm->addScalarResult('ID_DETALLE','idComisionDet','integer');
        $objRsm->addScalarResult('ID_PARAMETRO_DET', 'idParametroDet','integer');
        $objRsm->addScalarResult('DESCRIPCION', 'parametroDet','string');
        $objRsm->addScalarResult('VALOR1','valor1','string');
        $objRsm->addScalarResult('COMISION_VTA','comisionVenta','float');
        $objRsm->addScalarResult('VALOR2','valor2','string');
        $objRsm->addScalarResult('VALOR3','valor3','string');
        $objRsm->addScalarResult('VALOR4','valor4','string');
        $objRsm->addScalarResult('VALOR5','valor5','string');
       
        $objRsmCount->addScalarResult('TOTAL','total','integer');
        
        $objNtvQuery->setParameter('intIdProducto', $arrayParametros['intIdProducto']);        
        $objNtvQuery->setParameter('arrayEstados', array('Pendiente','Activo','Inactivo'));
        $objNtvQuery->setParameter('strEstadoActivo', 'Activo');
        $objNtvQuery->setParameter('strDescripcionParametro', 'GRUPO_ROLES_PERSONAL');
        $objNtvQuery->setParameter('strCodEmpresa', $arrayParametros['strCodEmpresa']);
        
        $strSqlDatos .= $strSqlFrom1;
        $strSqlDatos .= $strSqlUnion;
        $strSqlDatos .= $strSqlDatosUnion;
        $strSqlDatos .= $strSqlFrom2;
        $strSqlDatos .= $strSqlOrderBy;
        
        $objNtvQuery->setSQL($strSqlDatos);
        $objDatos = $objNtvQuery->getResult();
                
        $objNtvQueryCount->setParameter('intIdProducto', $arrayParametros['intIdProducto']);        
        $objNtvQueryCount->setParameter('arrayEstados', array('Pendiente','Activo','Inactivo'));
        $objNtvQueryCount->setParameter('strEstadoActivo', 'Activo');
        $objNtvQueryCount->setParameter('strDescripcionParametro', 'GRUPO_ROLES_PERSONAL');
        $objNtvQueryCount->setParameter('strCodEmpresa', $arrayParametros['strCodEmpresa']);       
                
        $objNtvQueryCount->setSQL($strSqlCantidad.$strSqlFrom1);
        $intTotal        = $objNtvQueryCount->getSingleScalarResult();
                
        $objNtvQueryCount->setSQL($strSqlCantidad.$strSqlFrom2);
        $intTotal        = $intTotal+$objNtvQueryCount->getSingleScalarResult();
        
        $arrayResultado['objRegistros'] = $objDatos;
        $arrayResultado['intTotal']     = $intTotal;
       
        return $arrayResultado;
    }
    
    /**
     * getResultadoParametroPlantilla
     * 
     * Obtiene la plantilla de Comisionistas por GRUPO_ROLES_PERSONAL definido a nivel de Parametros, 
     * Plantilla Parametrizada de ADMI_PARAMETRO_CAB que sera ingresada.
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 22-03-2017
     * costoQuery: 5
     * @param  array $arrayParametros [
     *                                  "intIdProducto"     : Id del producto     
     *                                  "strCodEmpresa"     : Empresa Cod   
     *                                ]          
     * 
     * @return json $arrayResultado
     */
    public function getResultadoParametroPlantilla($arrayParametros)
    {     
    
        $objRsmCount      = new ResultSetMappingBuilder($this->_em);
        $objNtvQueryCount = $this->_em->createNativeQuery(null, $objRsmCount);
        
        $strSqlCantidad   = ' SELECT COUNT(*)  AS TOTAL '; 
                        
        $objRsm           = new ResultSetMappingBuilder($this->_em);
        $objNtvQuery      = $this->_em->createNativeQuery(null, $objRsm);
        
        $strSqlDatos      = ' SELECT NULL AS ID_DETALLE, DET.ID_PARAMETRO_DET,DET.DESCRIPCION,DET.VALOR1,NULL AS COMISION_VTA, '
                          . 'DET.VALOR2, DET.VALOR3, DET.VALOR4, DET.VALOR5 ';
        $strSqlFrom       = ' FROM DB_GENERAL.ADMI_PARAMETRO_CAB CAB,
                                DB_GENERAL.ADMI_PARAMETRO_DET DET
                                WHERE 
                                CAB.NOMBRE_PARAMETRO   = :strDescripcionParametro 
                                AND CAB.ESTADO         = :strEstadoActivo
                                AND DET.ESTADO         = :strEstadoActivo
                                AND DET.EMPRESA_COD    = :strCodEmpresa   
                                AND CAB.ID_PARAMETRO   = DET.PARAMETRO_ID
                              ';
        $strSqlOrderBy    = " ORDER BY 9 ASC ";
        $objRsm->addScalarResult('ID_DETALLE','idComisionDet','integer');
        $objRsm->addScalarResult('ID_PARAMETRO_DET', 'idParametroDet','integer');
        $objRsm->addScalarResult('DESCRIPCION', 'parametroDet','string');
        $objRsm->addScalarResult('VALOR1','valor1','string');
        $objRsm->addScalarResult('COMISION_VTA','comisionVenta','float');
        $objRsm->addScalarResult('VALOR2','valor2','string');
        $objRsm->addScalarResult('VALOR3','valor3','string');
        $objRsm->addScalarResult('VALOR4','valor4','string');
        $objRsm->addScalarResult('VALOR5','valor5','string');
       
        $objRsmCount->addScalarResult('TOTAL','total','integer');
                
        $objNtvQuery->setParameter('strEstadoActivo', 'Activo');
        $objNtvQuery->setParameter('strDescripcionParametro', 'GRUPO_ROLES_PERSONAL');
        $objNtvQuery->setParameter('strCodEmpresa', $arrayParametros['strCodEmpresa']);
        
        $strSqlDatos .= $strSqlFrom; 
        $strSqlDatos .= $strSqlOrderBy;
        $objNtvQuery->setSQL($strSqlDatos);
        $objDatos = $objNtvQuery->getResult();
                
        $objNtvQueryCount->setParameter('strEstadoActivo', 'Activo');
        $objNtvQueryCount->setParameter('strDescripcionParametro', 'GRUPO_ROLES_PERSONAL');
        $objNtvQueryCount->setParameter('strCodEmpresa', $arrayParametros['strCodEmpresa']);
        
        $strSqlCantidad .= $strSqlFrom;
        $objNtvQueryCount->setSQL($strSqlCantidad);
        $intTotal        = $objNtvQueryCount->getSingleScalarResult();
        
        $arrayResultado['objRegistros'] = $objDatos;
        $arrayResultado['intTotal']     = $intTotal;
       
        return $arrayResultado;  
    }
    
    /**
     * getComisionPlantilla
     * 
     * Obtiene la plantilla de Comisionistas por GRUPO_ROLES_PERSONAL a nivel de Parametros segun el caso:
     * 1) Si se tiene intIdProducto en el array de parametros se obtiene la Plantilla definida para el producto especifico de ADMI_COMISION_CAB
     * 2) Si no se tiene intIdProducto en el array de parametros se obtiene la Plantilla Parametrizada de ADMI_PARAMETRO_CAB que sera ingresada.
     *      
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 20-03-2017
     * 
     * @param  array $arrayParametros [
     *                                  "intIdProducto"     : Id del producto     
     *                                  "strCodEmpresa"     : Empresa Cod
     *                                ]     
     * 
     * @return array $arrayRespuesta
     */
    public function getComisionPlantilla($arrayParametros)
    {       
        $arrayEncontrados = array();
        if(isset($arrayParametros['intIdProducto']) && !empty($arrayParametros['intIdProducto']))
        {
            $arrayResultado = $this->getResultadoComisionPlantilla($arrayParametros);   
        } 
        else
        {
            $arrayResultado = $this->getResultadoParametroPlantilla($arrayParametros);   
        }
        $arrayRegistros = $arrayResultado['objRegistros'];
        $intTotal       = $arrayResultado['intTotal'];
        
        if(($arrayRegistros))
        {         
            foreach($arrayRegistros as $arrayComisionPantilla)
            {                
                $arrayEncontrados[] = array(  
                    'idComisionDet' => $arrayComisionPantilla['idComisionDet'],
                    'idParametroDet'=> $arrayComisionPantilla['idParametroDet'],
                    'parametroDet'  => $arrayComisionPantilla['parametroDet'],
                    'requerido'     => $arrayComisionPantilla['valor1'],
                    'comisionVenta' => $arrayComisionPantilla['comisionVenta']
                    
                );
            }
        }
        
        $arrayRespuesta = array('total' => $intTotal, 'listado' => $arrayEncontrados);
        return $arrayRespuesta;        
    }
    
     /**
     * getResultadoProductosComisionan
     * 
     * Metodo que devuelve listado de productos que comisionan segun criterios  
     *     
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 12-04-2017
     * costoQuery: 12
     * @param  array $arrayParametros [
     *                                 'intIdProducto'    : Id del Producto
     *                                 'strGrupo'         : Grupo del producto
     *                                 'strNombreTecnico' : Nombre Tecnico del Producto
     *                                 'strEstado'        : Estado del producto
     *                                 'intLimit'         : Limite
     *                                 'intPage'          : Pagina
     *                                 'intStart'         : Inicio
     *                                 'strCodEmpresa'    : Codigo de empresa en sesion
     *                                ]     
     * 
     * @return array $arrayResultado
     */
    public function getResultadoProductosComisionan($arrayParametros)
    {        
        $objQueryCount = $this->_em->createQuery();
        $objQueryData  = $this->_em->createQuery();
        $strSqlCount   = " SELECT count(ap) ";
        $strSqlDatos   = " SELECT ap ";
        $strSqlFrom    = " FROM   schemaBundle:AdmiProducto ap
                           WHERE  ap.empresaCod =  :strCodEmpresa
                           AND ap.requiereComisionar=:strRequiereComisionar ";
        
        $objQueryData->setParameter('strCodEmpresa', $arrayParametros['strCodEmpresa']);
        $objQueryCount->setParameter('strCodEmpresa', $arrayParametros['strCodEmpresa']);
        $objQueryData->setParameter('strRequiereComisionar','SI');
        $objQueryCount->setParameter('strRequiereComisionar','SI');
 
        if(isset($arrayParametros['intIdProducto']) && !empty($arrayParametros['intIdProducto']))
        {
            $strSqlFrom .= " AND ap.id= :intIdProducto";
            $objQueryData->setParameter('intIdProducto', $arrayParametros['intIdProducto']);
            $objQueryCount->setParameter('intIdProducto', $arrayParametros['intIdProducto']);
        }
        
        if(isset($arrayParametros['strEstado']) && !empty($arrayParametros['strEstado']))
        {
            $strSqlFrom .= " AND ap.estado = :strEstado";
            $objQueryData->setParameter('strEstado', $arrayParametros['strEstado']);
            $objQueryCount->setParameter('strEstado', $arrayParametros['strEstado']);
        }
        
        if(isset($arrayParametros['strNombreTecnico']) && !empty($arrayParametros['strNombreTecnico']))
        {
            $strSqlFrom .= " AND ap.nombreTecnico = :strNombreTecnico";
            $objQueryData->setParameter('strNombreTecnico', $arrayParametros['strNombreTecnico']);
            $objQueryCount->setParameter('strNombreTecnico', $arrayParametros['strNombreTecnico']);
        }
        
        if(isset($arrayParametros['strGrupo']) && !empty($arrayParametros['strGrupo']))
        {
            $strSqlFrom .= " AND ap.grupo = :strGrupo";
            $objQueryData->setParameter('strGrupo', $arrayParametros['strGrupo']);
            $objQueryCount->setParameter('strGrupo', $arrayParametros['strGrupo']);
        }
        
        $strSqlOrder    = " ORDER BY ap.descripcionProducto ASC";
        
        $objQueryCount->setDQL($strSqlCount . $strSqlFrom);
        $intTotal = $objQueryCount->getSingleScalarResult();
                    
        $objQueryData->setDQL($strSqlDatos . $strSqlFrom . $strSqlOrder);
        $objRegistros = $objQueryData->setFirstResult( $arrayParametros['intStart'])->setMaxResults( $arrayParametros['intLimit'])->getResult();                
        
        $arrayResultado['objRegistros'] = $objRegistros;
        $arrayResultado['intTotal']     = $intTotal;
        return $arrayResultado;
    }
    
    /**
     * getProductosComisionan
     * 
     * Metodo que devuelve listado de productos que comisionan segun criterios  
     *     
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 12-04-2017
     * 
     * @param  array $arrayParametros [
     *                                 'intIdProducto'    : Id del Producto
     *                                 'strGrupo'         : Grupo del producto
     *                                 'strNombreTecnico' : Nombre Tecnico del Producto
     *                                 'strEstado'        : Estado del producto
     *                                 'intLimit'         : Limite
     *                                 'intPage'          : Pagina
     *                                 'intStart'         : Inicio
     *                                 'strCodEmpresa'    : Codigo de empresa en sesion
     *                                ]     
     * 
     * @return array $arrayRespuesta
     */
    public function getProductosComisionan($arrayParametros)
    {
        $arrayEncontrados = array();
        $arrayResultado = $this->getResultadoProductosComisionan($arrayParametros);

        $objProductos = $arrayResultado['objRegistros'];
        $intTotal = $arrayResultado['intTotal'];

        foreach($objProductos as $objProductos)
        {
            $arrayEncontrados[] = array(
                                        'intIdProducto'         => $objProductos->getId(),
                                        'strCodigo'             => $objProductos->getCodigoProducto(),
                                        'strDescripcion'        => $objProductos->getDescripcionProducto(),
                                        'strNombreTecnico'      => $objProductos->getNombreTecnico(),
                                        'strGrupo'              => $objProductos->getGrupo(),
                                        'strTipo'               => ($objProductos->getTipo() == null ? null :
                                                                   ($objProductos->getTipo() == 'S' ? 'Servicio' : 'Bien')),
                                        'fltInstalacion'        => $objProductos->getInstalacion(),
                                        'strFuncionPrecio'      => $objProductos->getFuncionPrecio(),
                                        'strFechaCreacion'      => strval(date_format($objProductos->getFeCreacion(), "d/m/Y G:i")),
                                        'strEstado'             => $objProductos->getEstado(),
                                        'strRequiereComisionar' => $objProductos->getRequiereComisionar()
            );
        }

        $arrayRespuesta = array('total' => $intTotal, 'productos' => $arrayEncontrados);
        return $arrayRespuesta;
    }

    /**
     * 
     * Metodo encargado de obtener el array con la informacion del Servicio principal que se desee crear un backup para replicar su informacion
     * dependiendo si es concentrador o simplemente utilizar parte de la informacion para transaccionar el nuevo servicio
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0
     * @since 06-12-2016
     * 
     * Costo 4
     * 
     * @param integer $intIdServicioPrincipal    id del Servicio Principal del cual se desea crear un servicio backup
     * @return Array  $arrayInformacion  [
     *                                      idProducto                id del producto ligado al servicio
     *                                      nombreTecnico             Nombre tecnico del producto
     *                                      funcionPrecio             Funcion precio a calcular de acuerdo al producto
     *                                      precioFormula             Precio calculado por la formula del servicio principal
     *                                      precioInstalacion         Precio de Instalacion base
     *                                      precioVenta               Precio total del servicio
     *                                      descripcion               Descripcion de facturacion del servicio principal
     *                                      esConcentrador            Determina si un servicio es concentrador o no
     *                                      capacidadUno              Capacidad Up del Servicio Principal
     *                                      capacidadDos              Capacidad Down del Servicio Principal
     *                                      instalacion               Valor definido por producto para Instalacion
     *                                      frecuencia                Frecuencia de Facturacion del servicio principal
     *                                   ]
     */
    public function getArrayInformacionParaServicioBackup($intIdServicioPrincipal)
    {
        $objResultSetMap = new ResultSetMappingBuilder($this->_em);
        $objNativeQuery  = $this->_em->createNativeQuery(null, $objResultSetMap);
        
        $strSql = "  SELECT
                        PRODUCTO.ID_PRODUCTO,
                        PRODUCTO.NOMBRE_TECNICO,
                        PRODUCTO.FUNCION_PRECIO,
                        PRODUCTO.DESCRIPCION_PRODUCTO,
                        PRODUCTO.INSTALACION,
                        SERVICIO.PRECIO_FORMULA,
                        SERVICIO.PRECIO_INSTALACION,
                        SERVICIO.PRECIO_VENTA,
                        SERVICIO.FRECUENCIA_PRODUCTO,
                        SERVICIO.DESCRIPCION_PRESENTA_FACTURA DESCRIPCION,
                        SERVICIO.TIPO_ORDEN,
                        PRODUCTO.ES_CONCENTRADOR,
                        PUNTO.ID_PUNTO,
                        DB_COMERCIAL.TECNK_SERVICIOS.GET_VALOR_SERVICIO_PROD_CARACT(SERVICIO.ID_SERVICIO,:CAPACIDAD1) CAPACIDAD1,
                        DB_COMERCIAL.TECNK_SERVICIOS.GET_VALOR_SERVICIO_PROD_CARACT(SERVICIO.ID_SERVICIO,:CAPACIDAD2) CAPACIDAD2
                      FROM 
                        DB_COMERCIAL.INFO_SERVICIO SERVICIO,
                        DB_COMERCIAL.ADMI_PRODUCTO PRODUCTO,
                        DB_COMERCIAL.INFO_PUNTO PUNTO
                      WHERE 
                          SERVICIO.PRODUCTO_ID   = PRODUCTO.ID_PRODUCTO
                      AND SERVICIO.PUNTO_ID      = PUNTO.ID_PUNTO
                      AND SERVICIO.ID_SERVICIO   = :servicio ";


        $objResultSetMap->addScalarResult('ID_PRODUCTO',        'idProducto',        'integer');
        $objResultSetMap->addScalarResult('NOMBRE_TECNICO',     'nombreTecnico',     'string');
        $objResultSetMap->addScalarResult('FUNCION_PRECIO',     'funcionPrecio',     'string');
        $objResultSetMap->addScalarResult('PRECIO_FORMULA',     'precioFormula',     'string');
        $objResultSetMap->addScalarResult('PRECIO_INSTALACION', 'precioInstalacion', 'string');
        $objResultSetMap->addScalarResult('PRECIO_VENTA',       'precioventa',       'string');
        $objResultSetMap->addScalarResult('CAPACIDAD1',         'capacidadUno',      'integer');
        $objResultSetMap->addScalarResult('CAPACIDAD2',         'capacidadDos',      'integer');
        $objResultSetMap->addScalarResult('DESCRIPCION',        'descripcionFactura','string');
        $objResultSetMap->addScalarResult('ES_CONCENTRADOR',    'esConcentrador',    'string');
        $objResultSetMap->addScalarResult('DESCRIPCION_PRODUCTO','descripcion',      'string');
        $objResultSetMap->addScalarResult('INSTALACION',        'instalacion',       'integer');
        $objResultSetMap->addScalarResult('FRECUENCIA_PRODUCTO','frecuencia',        'string');
        $objResultSetMap->addScalarResult('TIPO_ORDEN',         'tipoOrden',         'string');
        $objResultSetMap->addScalarResult('ID_PUNTO',           'idPunto',           'integer');

        $objNativeQuery->setParameter("CAPACIDAD1", 'CAPACIDAD1');
        $objNativeQuery->setParameter("CAPACIDAD2", 'CAPACIDAD2');
        $objNativeQuery->setParameter("servicio", $intIdServicioPrincipal);

        $objNativeQuery->setSQL($strSql);

        $arrayInformacion = $objNativeQuery->getOneOrNullResult();

        return $arrayInformacion;
    }

    /**
     * Metodo que devuelve los grupos por empresa de los productos
     * @param string  $arrayEmpresa
     * @return array $resultado
     *
     * @author Jorge Guerrero P. <jguerrerop@telconet.ec>
     * @version 1.0
     */
    public function findGruposPorEmpresa($arrayEmpresa)
    { 
        $objQuery = $this->_em->createQuery(
                    "SELECT ap.grupo
                     FROM schemaBundle:AdmiProducto ap
                     WHERE ap.empresaCod = :empresaCod
                     GROUP BY ap.grupo"
                )
                ->setParameter("empresaCod", $arrayEmpresa[0]['strCodEmpresa']);
        return $objQuery->getResult();
    }
    
    /**
     * Método que devuelve los grupos de los productos de acuerdo a los parámetros enviados
     * @param array $arrayParametros
     * @return array $arrayResultado
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 04-09-2017
     */
    public function getGruposByParams($arrayParametros)
    {
        $objQuery       = $this->_em->createQuery();
        $strQuery       = " SELECT ap.grupo
                            FROM schemaBundle:AdmiProducto ap
                            WHERE ap.grupo IS NOT NULL ";
        $strWhereAdic   = "";
        if(isset($arrayParametros['strCodEmpresa']) && !empty($arrayParametros['strCodEmpresa']))
        {
            $strWhereAdic.= "AND ap.empresaCod = :strEmpresaCod ";
            $objQuery->setParameter("strEmpresaCod", $arrayParametros['strCodEmpresa']);
        }
        if(isset($arrayParametros['strNombre']) && !empty($arrayParametros['strNombre']))
        {
            $strWhereAdic.= "AND ap.grupo like :strNombre ";
            $objQuery->setParameter("strNombre", '%'.$arrayParametros['strNombre'].'%');
        }
        $strQueryFinal = $strQuery.$strWhereAdic."GROUP BY ap.grupo ";
        $objQuery->setDQL($strQueryFinal);
        return $objQuery->getResult();
    }
    
    /**
     * Método que devuelve los subgrupos de los productos de acuerdo a los parámetros enviados
     * @param array $arrayParametros
     * @return array $arrayResultado
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 04-09-2017
     */
    public function getSubgruposByParams($arrayParametros)
    {
        $objQuery       = $this->_em->createQuery();
        $strQuery       = " SELECT ap.subgrupo
                            FROM schemaBundle:AdmiProducto ap
                            WHERE ap.subgrupo IS NOT NULL ";
        $strWhereAdic   = "";
        if(isset($arrayParametros['strCodEmpresa']) && !empty($arrayParametros['strCodEmpresa']))
        {
            $strWhereAdic.= "AND ap.empresaCod = :strEmpresaCod ";
            $objQuery->setParameter("strEmpresaCod", $arrayParametros['strCodEmpresa']);
        }
        if(isset($arrayParametros['strNombre']) && !empty($arrayParametros['strNombre']))
        {
            $strWhereAdic.= "AND ap.subgrupo like :strNombre ";
            $objQuery->setParameter("strNombre", '%'.$arrayParametros['strNombre'].'%');
        }
        $strQueryFinal = $strQuery.$strWhereAdic."GROUP BY ap.subgrupo ";
        $objQuery->setDQL($strQueryFinal);
        return $objQuery->getResult();
    }
    
    
    /**
     * getProductoPorDetalleSol
     * 
     * Obtiene si la solicitud esta asociada a un producto y si tiene un enlace.
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.0 03-10-2018
     * costoQuery: 6
     * @param  array $arrayParametros [
     *                                  "intIdDetalleSol"     : Id del detalle de la solicitud      
     *                                ]          
     * 
     * @return array $arrayResultado
     */
    public function getProductoPorDetalleSol($arrayParametros)
    {                                
        $objRsm           = new ResultSetMappingBuilder($this->_em);
        $objNtvQuery      = $this->_em->createNativeQuery(null, $objRsm);
        $intIdDetalleSol  = $arrayParametros['intIdDetalleSol'];
        
        $strSql           = "   SELECT APO.ES_ENLACE
                                FROM DB_SOPORTE.INFO_DETALLE_SOLICITUD IDA
                                INNER JOIN DB_COMERCIAL.INFO_SERVICIO ISO ON IDA.SERVICIO_ID=ISO.ID_SERVICIO
                                INNER JOIN DB_COMERCIAL.ADMI_PRODUCTO APO ON APO.ID_PRODUCTO=ISO.PRODUCTO_ID
                                WHERE IDA.ID_DETALLE_SOLICITUD = :idDetaSolicitud
                                AND ROWNUM<2 ";
        
        $objRsm->addScalarResult('ES_ENLACE','esEnlace','string');
                
        $objNtvQuery->setParameter('idDetaSolicitud', $intIdDetalleSol);
        
        $objNtvQuery->setSQL($strSql);
        $arrayResultado = $objNtvQuery->getOneOrNullResult();
                
        return $arrayResultado;  
    }
    
    /**
     * Documentación para la función 'findPorEmpresaYNombre'
     *
     * Metodo que busca productos por empresa y estado
     *
     * @author Telcos
     * @version 1.0 Version Inicial
     * 
     * costoQuery: 10
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.1 18/06/2021
     * 
     * @param array $arrayDatos  Código de la empresa a consultar
     * @param string $strEstado      Estado de los productos a consultar
     * @param string $strModulo      Módulo desde el cual se invoca la función.
     * 
     * @return obj $objDatos
     */     
    public function findPorEmpresaYNombre($arrayDatos)
    {
        $strEstado      = ( isset($arrayDatos['strEstado']) && !empty($arrayDatos['strEstado']) )
                                   ? $arrayDatos['strEstado'] : 'Activo';
        
        $strModulo      = ( isset($arrayDatos['strModulo']) && !empty($arrayDatos['strModulo']) )
                                   ? $arrayDatos['strModulo'] : 'Comercial';
        
        $strEmpresaCod  = ( isset($arrayDatos['strEmpresaCod']) && !empty($arrayDatos['strEmpresaCod']) )
                                   ? $arrayDatos['strEmpresaCod'] : '10';
        
        $arrayProductos = ( isset($arrayDatos['arrayProductos']) && !empty($arrayDatos['arrayProductos']) )
                                   ? $arrayDatos['arrayProductos'] : '';
        $objDatos       = null;
            
        $objQuery = $this->_em->createQuery();

        // definir string dql basico
        $strSelect  = "SELECT ap ";
        $strFrom    = "FROM schemaBundle:AdmiProducto ap ";
        $strJoin    = "";
        $strWhere   = "WHERE ap.estado = :estado ";
        $strOrderBy = "ORDER BY ap.descripcionProducto ASC ";

        // agregar parametros basicos al query
        $objQuery->setParameter("estado", $strEstado);

        //Si es comercial entonces excluye aquellos que tengan nombre_tecnico=FINANCIERO
        if( !empty($strModulo) && $strModulo=="Comercial")
        {
            $strWhere .= " AND ap.nombreTecnico <> :nombreTecnico AND ap.esConcentrador <> :esConcentrador ";

            $objQuery->setParameter("nombreTecnico", 'FINANCIERO');
            $objQuery->setParameter("esConcentrador", 'SI');                 
        }

        if( !empty($strEmpresaCod) )
        {
            // agregar condiciones opcionales al dql y parametros al query
            $strWhere .= " AND ap.empresaCod = :empresaId ";
            $objQuery->setParameter("empresaId", $strEmpresaCod);
        }

        if( !empty($arrayProductos) )
        {
            // agregar condiciones opcionales al dql y parametros al query
            $strWhere .= " AND upper(ap.descripcionProducto) in (:arrayProductos) ";
            $objQuery->setParameter("arrayProductos", $arrayProductos);
        }

        if( !empty($strModulo) && $strModulo=="Comercial")
        {
             $strWhere .= " AND ap.id NOT IN ( " .
            "                      SELECT ap_sf.id " .
            "                      FROM schemaBundle:AdmiProductoCaracteristica apcf " .
            "                      JOIN apcf.productoId ap_sf " .
            "                      JOIN apcf.caracteristicaId acf " .
            "                      WHERE acf.descripcionCaracteristica = :strDescCarac " .
            "                      AND acf.estado = :strEstado " .
            "                   ) ";     
            $objQuery->setParameter("strDescCarac", 'NO_VISIBLE_COMERCIAL');
            $objQuery->setParameter("strEstado", 'Activo');
        }

        $strDql = $strSelect.$strFrom.$strJoin.$strWhere.$strOrderBy;
        // aplicar string dql al query y ejecutar
        $objQuery->setDQL($strDql);
        $objDatos = $objQuery->getResult();

        return $objDatos;
    }
}

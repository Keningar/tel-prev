<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use telconet\schemaBundle\DependencyInjection\BaseRepository;

class AdmiTipoElementoRepository extends BaseRepository
{
    /**
     * Funcion que sirve para generar un json con los tipos de elementos
     * filtrados por nombre y estado
     * 
     * @param $nombre string
     * @param $estado string
     * @param $start int
     * @param $limit int
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 13-02-2015
     */
    public function generarJsonTiposElementosAdministracion($nombre, $estado, $start, $limit)
    {
        $arr_encontrados = array();

        $result = $this->getTiposElementosAdministracion($nombre, $estado, $start, $limit);

        $encontrados = $result['registros'];
        $encontradosTotal = $result['total'];

        if($encontrados)
        {
            $num = $encontradosTotal;

            foreach($encontrados as $objeto)
            {
                $estado = $objeto->getEstado();
                $arr_encontrados[] = array( 'idTipoElemento'        => $objeto->getId(),
                                            'nombreTipoElemento'    => $objeto->getNombreTipoElemento(),
                                            'estado'                => $estado,
                                            'action1'               => 'button-grid-show',
                                            'action2'               => ($estado == 'Eliminado' ? 'button-grid-invisible' : 'button-grid-edit'),
                                            'action3'               => ($estado == 'Eliminado' ? 'button-grid-invisible' : 'button-grid-delete')
                                          );
            }

            if($num == 0)
            {
                $resultado = array('total' => 1, 'encontrados' => array('idTipoElemento' => 0, 'nombreTipoElemento' => 'Ninguno'));
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
     * Funcion que genera y ejecuta un sql por los filtros de nombre y estado del tipo de elemento
     * 
     * @param $nombre string
     * @param $estado string
     * @param $start int
     * @param $limit int
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 13-02-2015
     */
    public function getTiposElementosAdministracion($nombre,$estado,$start,$limit)
    {
        $qb = $this->_em->createQueryBuilder();
        $qbC = $this->_em->createQueryBuilder();
        //query para obtener la informacion
        $qb->select('AdmiTipoElemento')
            ->from('schemaBundle:AdmiTipoElemento', 'AdmiTipoElemento');

        //query para obtener la cantidad de registros
        $qbC->select('count(AdmiTipoElemento.id)')
            ->from('schemaBundle:AdmiTipoElemento', 'AdmiTipoElemento');

        if($nombre != "")
        {
            $qb->where('UPPER(AdmiTipoElemento.nombreTipoElemento) like ?1');
            $qb->setParameter(1, "%" . strtoupper($nombre) . "%");
            $qbC->where('UPPER(AdmiTipoElemento.nombreTipoElemento) like ?1');
            $qbC->setParameter(1, "%" . strtoupper($nombre) . "%");
        }
        if($estado != "Todos")
        {
            $qb->andWhere('UPPER(AdmiTipoElemento.estado) = ?2');
            $qb->setParameter(2, $estado);
            $qbC->andWhere('UPPER(AdmiTipoElemento.estado) = ?2');
            $qbC->setParameter(2, $estado);
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
     * Funcion que sirve para obtener los datos necesarios para la edicion de
     * un objeto AdmiTipoElemento
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 18-02-2015
     */
    public function generarJsonCargarDatosTipoElemento($idTipoElemento, $emGeneral)
    {
        $arr_encontrados = array();
        
        $objeto = $this->_em->find('schemaBundle:AdmiTipoElemento', $idTipoElemento);
                
        $parametroDetObj = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findOneBy(array( "valor1" =>$objeto->getEsDe()));
                
        if($parametroDetObj)
        {
            $arr_encontrados[]=array('idTipoElemento'  => $objeto->getId(),
                                     'parametroDetId'  => $parametroDetObj->getId());
        }
        else
        {
            $arr_encontrados[]=array('idTipoElemento'  => $objeto->getId(),
                                     'parametroDetId'  => "");
        }   

        $data=json_encode($arr_encontrados);
        $resultado= '{"total":"1","encontrados":'.$data.'}';

        return $resultado;
    }
    
    /**
     * Funcion que sirve para generar un json con los tipos de elementos
     * filtrados por nombre, estado, codEmpresa, usado en Casos
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 13-02-2015
     *
     * @param $parametros array
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 18-12-2015 Se realizan ajustes para presentar tipos de elementos en el nuevo panel de movilizacion
     *
     */
    public function generarJsonTiposElementos($parametros)
    {
        $arr_encontrados = array();
        $datos           = array();

        $datos               = $this->getTiposElementos($parametros);
        $tiposElementos      = $datos['registros'];
        $tiposElementosTotal = $datos['total'];

        if($tiposElementos)
        {
            $num = $tiposElementosTotal;
            foreach($tiposElementos as $tipoElemento)
            {
                $arr_encontrados[] = array(
                    'idTipoElemento' => $tipoElemento['id'],
                    'nombreTipoElemento' => trim($tipoElemento['nombreTipoElemento']),
                    'estado' => (trim($tipoElemento['estado']) == 'Eliminado' ? 'Eliminado' : 'Activo'),
                    'action1' => 'button-grid-show',
                    'action2' => (trim($tipoElemento['estado']) == 'Eliminado' ? 'button-grid-invisible' : 'button-grid-edit'),
                    'action3' => (trim($tipoElemento['estado']) == 'Eliminado' ? 'button-grid-invisible' : 'button-grid-delete'));
            }

            if($num == 0)
            {
                $resultado = array('total' => 1,
                    'encontrados' => array('idConectorInterface' => 0, 'nombreConectorInterface' => 'Ninguno', 'idConectorInterface' => 0, 
                                           'nombreConectorInterface' => 'Ninguno', 'estado' => 'Ninguno'));
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
     * Funcion que sirve para obtener los registros sobre los tipos de elementos
     * filtrados por nombre, estado, codEmpresa, usado en Casos
     *
     * @version 1.0
     * 
     * Costo : 345
     *
     * @param $arrayParametros array
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 18-12-2015 Se realizan ajustes para presentar tipos de elementos en el nuevo panel de movilizacion
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 07-01-2016 Se realizan ajustes para presentar tipos de elementos en el nuevo panel de seguridad - servidores
     *
     * @author Alejandro Dominguez Vargas <adominguez@telconet.ec>
     * @version 1.3 07-03-2016 Se agrega el parametro de ordenamiento 'order'
     * @since 1.2
     * 
     * @author Allan Suarez C <arsuarez@telconet.ec>
     * @version 1.4 14-06-2018 Se cambia consulta a native query para mejorar costos de consulta y se cambia las variables a estandar establecido
     * 
     */
    public function getTiposElementos($arrayParametros)
    {
        $strNombre     = isset($arrayParametros['nombre'])?$arrayParametros['nombre']:"";
        $strEstado     = isset($arrayParametros['estado'])?$arrayParametros['estado']:"";
        $strCodEmpresa = isset($arrayParametros['codEmpresa'])?$arrayParametros['codEmpresa']:"";
        $intStart      = isset($arrayParametros['start'])?$arrayParametros['start']:"";
        $intLimit      = isset($arrayParametros['limit'])?$arrayParametros['limit']:"";
        $strActivoFijo = isset($arrayParametros['activoFijo'])?$arrayParametros['activoFijo']:"";

        $objRsm        = new ResultSetMappingBuilder($this->_em);
        $objQuery      = $this->_em->createNativeQuery(null, $objRsm);
        
        $objRsmCount   = new ResultSetMappingBuilder($this->_em);
        $objQueryCount = $this->_em->createNativeQuery(null, $objRsmCount);
            
        $strWwhere     = "";
        $strWhereExists= "";
        $strOrderBy    = "";
        $arrayDatos    = array();
        
        if($strNombre && $strNombre != "")
        {
            $strWwhere .= " AND TIPO.NOMBRE_TIPO_ELEMENTO = :varNombreElemento ";
            $objQuery->setParameter('varNombreElemento',$strNombre);
            $objQueryCount->setParameter('varNombreElemento',$strNombre);
        }
        if($strCodEmpresa && $strCodEmpresa != "")
        {

            if($strCodEmpresa == '33')
            {
                $strWhereExists .= " AND EMPRESA.EMPRESA_COD in ('18','33') ";
            }else
            {
                $strWhereExists .= " AND EMPRESA.EMPRESA_COD = :varEmpresaCod ";
                $objQuery->setParameter('varEmpresaCod',$strCodEmpresa);
                $objQueryCount->setParameter('varEmpresaCod',$strCodEmpresa);
              
            }

           
            
        }
        //Significa que viene desde el panel de Movilizacion
        if($strActivoFijo === "S" && $strActivoFijo != "")
        {
            $strWwhere .= " AND ( TIPO.NOMBRE_TIPO_ELEMENTO = :varTipoElemento1 OR TIPO.NOMBRE_TIPO_ELEMENTO = :varTipoElemento2 )";
            $objQuery->setParameter('varTipoElemento1','VEHICULO');
            $objQuery->setParameter('varTipoElemento2','MOTO');
            $objQueryCount->setParameter('varTipoElemento1','VEHICULO');
            $objQueryCount->setParameter('varTipoElemento2','MOTO');
        }
        //Significa que viene desde el panel de Servidores
        if($strActivoFijo === "Se" && $strActivoFijo != "")
        {
            $strWwhere .= " AND ( TIPO.NOMBRE_TIPO_ELEMENTO = :varTipoElemento )";
            $objQuery->setParameter('varTipoElemento','SERVIDOR');
            $objQueryCount->setParameter('varTipoElemento','SERVIDOR');
        }

        if($strEstado != 'Todos')
        {
            $strWhereExists .= " AND EMPRESA.ESTADO = :varEstado1 ";
            $objQuery->setParameter('varEstado1',$strEstado);
            $objQueryCount->setParameter('varEstado1',$strEstado);
        }
        
        if(isset($arrayParametros['order']))
        {
            if(!empty($arrayParametros['order']))
            {
                $strOrderBy = " order by " . $arrayParametros['order'];
            }
        }
        
        $strSelectCount = "SELECT COUNT(*) CONT ";
        $strSelect      = "SELECT TIPO.NOMBRE_TIPO_ELEMENTO , TIPO.ID_TIPO_ELEMENTO , TIPO.ESTADO ";
        
        $strFrom = "FROM 
                        DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO TIPO
                    WHERE EXISTS 
                    (
                        SELECT 
                            MODELO.ID_MODELO_ELEMENTO
                        FROM
                            DB_INFRAESTRUCTURA.INFO_ELEMENTO         ELEMENTO,
                            DB_INFRAESTRUCTURA.INFO_EMPRESA_ELEMENTO EMPRESA,
                            DB_INFRAESTRUCTURA.ADMI_MODELO_ELEMENTO  MODELO
                        WHERE
                            EMPRESA.ELEMENTO_ID       = ELEMENTO.ID_ELEMENTO AND
                            MODELO.ID_MODELO_ELEMENTO = ELEMENTO.MODELO_ELEMENTO_ID AND
                            MODELO.TIPO_ELEMENTO_ID   = TIPO.ID_TIPO_ELEMENTO
                            $strWhereExists
                        GROUP BY MODELO.ID_MODELO_ELEMENTO
                    ) AND TIPO.ESTADO = :varEstado
                    $strWwhere
                    ";
        
        $objQuery->setParameter('varEstado','Activo');
        $objQueryCount->setParameter('varEstado','Activo');

        $strSqlCount = $strSelectCount . $strFrom ;
        $objQueryCount->setSQL($strSqlCount);
        $objRsmCount->addScalarResult('CONT', 'cont', 'integer');
        
        $arrayDatos['total'] = $objQueryCount->getSingleScalarResult();
        
        $strSql    = $strSelect . $strFrom . $strOrderBy;
        $objRsm->addScalarResult('ID_TIPO_ELEMENTO', 'id', 'integer');
        $objRsm->addScalarResult('NOMBRE_TIPO_ELEMENTO', 'nombreTipoElemento', 'string');
        $objRsm->addScalarResult('ESTADO', 'estado', 'string');
        $objQuery->setSQL($strSql);

        if($intStart != '' && $intLimit != '')
        {
            $objQuery = $this->setQueryLimit($objQuery, $intLimit, $intStart);
        }

        $arrayDatos['registros'] = $objQuery->getResult();
        
        return $arrayDatos;
    }

    public function generarJsonTiposElementosBackbone($esDe,$estado,$start,$limit){
        
        $arr_encontrados = array();
        
        $tiposElementosTotal = $this->getTiposElementosBackbone($esDe,$estado,'','');
        
        $tiposElementos      = $this->getTiposElementosBackbone($esDe,$estado,$start,$limit);
        
        
//        error_log('entra');
        if ($tiposElementos) {
            
            $num = count($tiposElementosTotal);                        
                                    
            foreach ($tiposElementos as $tipoElemento)
            {            		
            
                $arr_encontrados[]=array(//'idTipoElemento' =>$tipoElemento->getId(),
					 'idTipoElemento' =>$tipoElemento->getId(),
                                         //'nombreTipoElemento' =>trim($tipoElemento->getNombreTipoElemento()),
                                         'nombreTipoElemento' =>trim($tipoElemento->getNombreTipoElemento()),
                                         'estado' =>(trim($tipoElemento->getEstado())=='Eliminado' ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (trim($tipoElemento->getEstado())=='Eliminado' ? 'button-grid-invisible':'button-grid-edit'),
                                         'action3' => (trim($tipoElemento->getEstado())=='Eliminado' ? 'button-grid-invisible':'button-grid-delete'));
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

    /**
     * Documentación para el método getTipoElementoPorElementoClienteId
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0
     * @since 03-09-2018
     * Versión inicial.
     */
    public function getTipoElementoPorElementoClienteId($arrayParametros)
    {
        //Costo del query 6
        $strSql  = "SELECT
                        AMAR.NOMBRE_MARCA_ELEMENTO,
                        ATE.NOMBRE_TIPO_ELEMENTO,
                        APD.VALOR2,
                        APD.VALOR3
                    FROM
                        DB_INFRAESTRUCTURA.INFO_ELEMENTO IE,
                        DB_INFRAESTRUCTURA.ADMI_MODELO_ELEMENTO AMOD,
                        DB_INFRAESTRUCTURA.ADMI_MARCA_ELEMENTO AMAR,
                        DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO ATE,
                        DB_GENERAL.ADMI_PARAMETRO_CAB APC,
                        DB_GENERAL.ADMI_PARAMETRO_DET APD
                    WHERE
                        ID_ELEMENTO = :intElementoClienteId
                        AND AMOD.ID_MODELO_ELEMENTO = IE.MODELO_ELEMENTO_ID
                        AND AMOD.MARCA_ELEMENTO_ID = AMAR.ID_MARCA_ELEMENTO
                        AND AMOD.TIPO_ELEMENTO_ID = ATE.ID_TIPO_ELEMENTO
                        AND APC.NOMBRE_PARAMETRO = :strNombreParametro
                        AND APC.ESTADO = :strEstadoActivo
                        AND APD.PARAMETRO_ID = APC.ID_PARAMETRO
                        AND APD.ESTADO = :strEstadoActivo
                        AND APD.DESCRIPCION = ATE.NOMBRE_TIPO_ELEMENTO
                        AND APD.VALOR1 = AMAR.NOMBRE_MARCA_ELEMENTO";

        $objRsm   = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsm);
        $objQuery->setParameter("intElementoClienteId", $arrayParametros["intElementoClienteId"]);
        $objQuery->setParameter("strNombreParametro",   "RETIRO_EQUIPOS_SOPORTE");
        $objQuery->setParameter("strEstadoActivo",      "Activo");

        $objRsm->addScalarResult("NOMBRE_MARCA_ELEMENTO", "strNombreMarcaElemento", "string");
        $objRsm->addScalarResult("NOMBRE_TIPO_ELEMENTO",  "strNombreTipoElemento",  "string");
        $objRsm->addScalarResult("VALOR2",                "floatPrecio",            "float");
        $objRsm->addScalarResult("VALOR3",                "intCaracteristicaId",    "integer");

        $objQuery->setSQL($strSql);
        $arrayRespuesta = $objQuery->getScalarResult();

        return $arrayRespuesta;
    }

    public function getTiposElementosBackbone($esDe,$estado,$start,$limit){
        $qb = $this->_em->createQueryBuilder();
            $qb->select('e')
               ->from('schemaBundle:AdmiTipoElemento','e');
               
        if($esDe!=""){
            $qb ->where( 'e.esDe = ?1');
            $qb->setParameter(1, $esDe);
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
     * Funcion que sirve para generar un json con los tipos de elementos Ruta
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.0 18-05-2021
     *
     * @param $arrayParametros array
     *
     */
    public function generarJsonTiposElementosRuta($arrayParametros)
    {
        $arrayEncontrados = array();
        
        $objDatos             = $this->getTiposElementosRuta($arrayParametros);
        
        if($objDatos)
        {
            $intNum = count($objDatos);
            foreach($objDatos as $objTipoElemento)
            {
                $arrayEncontrados[]      =  array(
                    'idTipoElemento'     => $objTipoElemento->getId(),
                    'nombreTipoElemento' => trim($objTipoElemento->getNombreTipoElemento()),
                    'estado'             =>(trim($objTipoElemento->getEstado())=='Eliminado' ? 'Eliminado':'Activo'),
                    'action1'            => 'button-grid-show',
                    'action2'            => (trim($objTipoElemento->getEstado())=='Eliminado' ? 'button-grid-invisible':'button-grid-edit'),
                    'action3'            => (trim($objTipoElemento->getEstado())=='Eliminado' ? 'button-grid-invisible':'button-grid-delete'));
            }

            if($intNum == 0)
            {
                $objResultado = array('total' => 1,
                    'encontrados' => array('idConectorInterface' => 0, 'nombreConectorInterface' => 'Ninguno', 'idConectorInterface' => 0, 
                                           'nombreConectorInterface' => 'Ninguno', 'estado' => 'Ninguno'));
                $objResultado     = json_encode($objResultado);

                return $objResultado;
            }
            else
            {
                $objData = json_encode($arrayEncontrados);
                $objResultado = '{"total":"' . $intNum . '","encontrados":' . $objData . '}';

                return $objResultado;
            }
        }
        else
        {
            $objResultado = '{"total":"0","encontrados":[]}';

            return $objResultado;
        }
    }
    
    public function getTiposElementosRuta($arrayParametros)
    {
        $strDescripcion = $arrayParametros["descripcion"];
        $strEstado      = $arrayParametros["estado"];
        $objQb          = $this->_em->createQueryBuilder();
            $objQb->select('e')
                  ->from('schemaBundle:AdmiTipoElemento','e');
               
        if($strDescripcion!="")
        {
            $objQb ->where( 'e.descripcionTipoElemento = ?1');
            $objQb->setParameter(1, $strDescripcion);
        }
        if($strEstado!="")
        {
            $objQb ->andWhere('e.estado = ?2');
            $objQb->setParameter(2, $strEstado);
        }
               
        $objQuery = $objQb->getQuery();                
        
        return $objQuery->getResult();
    }


     /**
     * Funcion que sirve para seleccionar los contenedores filtrados por el tipo de elemento y el id del Nodo     
     * 
     * @author Geovanny Cudco<acudco@telconet.ec>
     * @version 1.0 09-03-2023
     *
     * @param $parametros array     
     */
    public function getContenedoresNodo($arrayParametros)
    {
        $objRsm          = new ResultSetMappingBuilder($this->_em);
        $objQuery        = $this->_em->createNativeQuery(null, $objRsm);

        $strTipoElemento = $arrayParametros["strTipoElemento"];
        $intIdNodo       = $arrayParametros["intIdNodo"];
        $strParametro    = $arrayParametros["strParametro"];    //por defecto ingresa 'ELEMENTOS CON CONTENEDORES'    

        $strSql          = "SELECT R_ELM.ELEMENTO_ID_A AS ID_NODO,
                                    INF_ELM.ID_ELEMENTO AS ID_NODO_CONTNR,
                                    INF_ELM.NOMBRE_ELEMENTO AS NOMBRE_CONTENEDOR,       
                                    R_ELM.ELEMENTO_ID_B AS ID_CONTENEDOR, 
                                    TIP.NOMBRE_TIPO_ELEMENTO,
                                    HIJO.NOMBRE_ELEMENTO,
                                    HIJO.DESCRIPCION_ELEMENTO,
                                    MDL.NOMBRE_MODELO_ELEMENTO       
                            FROM DB_INFRAESTRUCTURA.INFO_RELACION_ELEMENTO R_ELM
                            INNER JOIN DB_INFRAESTRUCTURA.INFO_ELEMENTO INF_ELM
                                ON R_ELM.ELEMENTO_ID_A       = INF_ELM.ID_ELEMENTO
                            INNER JOIN DB_INFRAESTRUCTURA.INFO_ELEMENTO HIJO
                                ON R_ELM.ELEMENTO_ID_B       = HIJO.ID_ELEMENTO
                            INNER JOIN DB_INFRAESTRUCTURA.ADMI_MODELO_ELEMENTO MDL
                                ON HIJO.MODELO_ELEMENTO_ID   = MDL.ID_MODELO_ELEMENTO
                            INNER JOIN DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO TIP    
                                ON MDL.TIPO_ELEMENTO_ID      = TIP.ID_TIPO_ELEMENTO
                            WHERE R_ELM.ELEMENTO_ID_A        = :intInIdNodo
                                AND INF_ELM.ESTADO = 'Activo'
                                AND HIJO.ESTADO    = 'Activo'
                                AND TIP.NOMBRE_TIPO_ELEMENTO IN (SELECT VALOR2
                                                                    FROM DB_GENERAL.ADMI_PARAMETRO_DET
                                                                    WHERE VALOR1         = :strInTipoElemento
                                                                        AND PARAMETRO_ID = (SELECT ID_PARAMETRO 
                                                                                            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
                                                                                            WHERE NOMBRE_PARAMETRO = :strInParametro 
                                                                                                AND ESTADO='Activo'))
                            ORDER BY HIJO.NOMBRE_ELEMENTO";
        
        $objQuery->setParameter("strInTipoElemento", $strTipoElemento);   
        $objQuery->setParameter("intInIdNodo", $intIdNodo);  
        $objQuery->setParameter("strInParametro", $strParametro);  

        $objRsm->addScalarResult('ID_CONTENEDOR', 'idPadreElemento', 'int');
        $objRsm->addScalarResult('NOMBRE_ELEMENTO', 'nombrePadreElemento', 'string');
    
        $objQuery->setSQL($strSql);
        $arrayDatos = $objQuery->getScalarResult();
        
        return $arrayDatos;
    }
    
    
     /**
     * Función que sirve para verificar si un elemento tiene contenedor o es un contenedor    
     * 
     * @author Geovanny Cudco<acudco@telconet.ec>
     * @version 1.0 09-03-2023
     *
     * @param $parametros array     
     */
    public function verificarContenedor($arrayParametros)
    {
        $objRsm          = new ResultSetMappingBuilder($this->_em);
        $objQuery        = $this->_em->createNativeQuery(null, $objRsm);

        $strTipoElemento = $arrayParametros["strTipoElemento"];        
        $strParametro    = $arrayParametros["strParametro"];    //por defecto ingresa 'ELEMENTOS CON CONTENEDORES'

        $strSql          = "SELECT VALOR1,VALOR2
                            FROM DB_GENERAL.ADMI_PARAMETRO_DET
                            WHERE VALOR1 = :strInTipoElemento
                                AND PARAMETRO_ID = (SELECT ID_PARAMETRO 
                                                FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
                                                WHERE NOMBRE_PARAMETRO = :strInParametro
                                                    AND ESTADO='Activo')";

        $objQuery->setParameter("strInTipoElemento", $strTipoElemento);            
        $objQuery->setParameter("strInParametro", $strParametro);  

        $objRsm->addScalarResult('VALOR1', 'elemento', 'int');
        $objRsm->addScalarResult('VALOR2', 'contenedor', 'string');
    
        $objQuery->setSQL($strSql);
        $arrayDatos = $objQuery->getScalarResult();
        
        return $arrayDatos;
        
    }
}


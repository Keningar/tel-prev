<?php

namespace telconet\schemaBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class InfoRelacionElementoRepository extends EntityRepository
{
    /**
     * getJsonSplittersEnCajaISB
     * 
     * Obtiene los splitters contenidos en una determinada caja validando que el olt de donde proviene dicho splitter sea HUAWEI 
     * y que tenga activado el uso del middleware
     * Costo = 10
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 25-01-2018
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 16-05-2018 Se permite factibilidad para OLTs HUAWEI y TELLION
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.2 20-07-2018 Se agrega restricción para servicios ISB, no se debe dar factibilidad automática para tecnologías parametrizadas
     * @since 1.1
     * 
     * @param array $arrayParametros[  
     *                                  "intIdTipoElemento"     => id del tipo elemento
     *                                  "intIdElementoCaja"     => id del elemento caja
     *                                  "strEstado"             => estado del splitter            
     *                               ]
     * 
     * @return array $arrayRespuesta['intTotal', 'arrayResultado']
     */
    public function getJsonSplittersEnCajaISB($arrayParametros)
    {
        $intTotal                   = 0;
        $arraySplittersFinal        = array();
        $arrayTecNoPermitidas       = array();

        $arraySplitters             = $this->getRegistrosElementosbyPadre( $arrayParametros["intIdTipoElemento"],
                                                                            $arrayParametros["intIdElementoCaja"],
                                                                            '',
                                                                            '',
                                                                            $arrayParametros["strEstado"]);

        $objRsm                     = new ResultSetMappingBuilder($this->_em);
        $objNtvQuery                = $this->_em->createNativeQuery(null, $objRsm);

        $strQueryVerificacionOlt    = " SELECT E.ID_ELEMENTO 
                                        FROM DB_INFRAESTRUCTURA.INFO_INTERFACE_ELEMENTO IIE
                                        INNER JOIN DB_INFRAESTRUCTURA.INFO_ELEMENTO E
                                        ON E.ID_ELEMENTO = IIE.ELEMENTO_ID
                                        INNER JOIN DB_INFRAESTRUCTURA.ADMI_MODELO_ELEMENTO AME
                                        ON AME.ID_MODELO_ELEMENTO = E.MODELO_ELEMENTO_ID
                                        INNER JOIN DB_INFRAESTRUCTURA.ADMI_MARCA_ELEMENTO AMARCAE
                                        ON AME.MARCA_ELEMENTO_ID    = AMARCAE.ID_MARCA_ELEMENTO
                                        WHERE ID_INTERFACE_ELEMENTO = GET_ELEMENTO_PADRE(:intIdSplitter, :strElemento, :strTipoOlt)
                                        AND EXISTS
                                        (SELECT *
                                        FROM DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO IDE_MIDDLEWARE
                                        WHERE E.ID_ELEMENTO               = IDE_MIDDLEWARE.ELEMENTO_ID
                                        AND IDE_MIDDLEWARE.DETALLE_NOMBRE = :strDetalleNombre
                                        AND IDE_MIDDLEWARE.DETALLE_VALOR  = :strDetalleValor
                                        AND IDE_MIDDLEWARE.ESTADO         = :strEstadoActivo
                                        ) ";
        
        $arrayParametrosDetTec = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                                           ->getOne("ISB_TECNOLOGIAS_NO_PERMITIDAS", 
                                                    "COMERCIAL", 
                                                    "", 
                                                    "", 
                                                    "TECNOLOGIAS", 
                                                    "", 
                                                    "", 
                                                    ""
                                                   );
        if($arrayParametrosDetTec && 
           count($arrayParametrosDetTec) > 0 && 
           isset($arrayParametrosDetTec['valor2']) && 
           !empty($arrayParametrosDetTec['valor2']))
        {
            $strTecnologiasNoPermitidas = $arrayParametrosDetTec['valor2'];
            $arrayTecNoPermitidas       = explode("|",$strTecnologiasNoPermitidas);
            $strQueryVerificacionOlt   .= " AND AMARCAE.NOMBRE_MARCA_ELEMENTO NOT IN (:TECNOLOGIAS_NO_PERMITIDAS)";
            $objNtvQuery->setParameter('TECNOLOGIAS_NO_PERMITIDAS', $arrayTecNoPermitidas);
        }

        $objNtvQuery->setParameter('strElemento', 'ELEMENTO');
        $objNtvQuery->setParameter('strTipoOlt', 'OLT');
        $objNtvQuery->setParameter('strDetalleNombre', 'MIDDLEWARE');
        $objNtvQuery->setParameter('strDetalleValor', 'SI');
        $objNtvQuery->setParameter('strEstadoActivo', 'Activo');

        $objRsm->addScalarResult('ID_ELEMENTO', 'idElemento', 'integer');
         
        try
        {
            if (!empty($arraySplitters))
            {
                foreach ($arraySplitters as $objSplitter)
                {
                    if(is_object($objSplitter))
                    {
                        $objNtvQuery->setParameter('intIdSplitter', $objSplitter->getId());
                        $objNtvQuery->setSQL($strQueryVerificacionOlt);
                        $intIdElementoOlt = $objNtvQuery->getOneOrNullResult();
                        
                        if(isset($intIdElementoOlt) && !empty($intIdElementoOlt) && $intIdElementoOlt>0 )
                        {
                            $arraySplittersFinal[] = array( 'idElemento'        => $objSplitter->getId(),
                                                            'nombreElemento'    => trim($objSplitter->getNombreElemento()));
                            $intTotal++;
                        }
                    }
                }
            }
        } 
        catch (\Exception $e) 
        {
            error_log($e->getMessage());
        }
        $strJsonData    = json_encode(array('total' => $intTotal, 'encontrados' => $arraySplittersFinal));
        return $strJsonData;
    }
    
    public function generarJsonComboElmentosByPadre($nombre,$idTipoElemento, $idElementoPadre, $estado)
    {
        $arr_encontrados = array();
        
        $encontrados = $this->getRegistrosElementosbyPadre($idTipoElemento,$idElementoPadre,'','',$estado);
        
        if ($encontrados) {            
            $num = count($encontrados);
            foreach ($encontrados as $entidad)
            {
                $arr_encontrados[] = array(
                    'idElemento' => $entidad->getId(),
                    'estadoElemento' => trim($entidad->getEstado()),
                    'nombreElemento' => trim($entidad->getNombreElemento()
                    ));
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

    public function generarJsonElmentosByPadre($idTipoElemento, $idElementoPadre, $start, $limit)
    {
        $arr_encontrados = array();
        
        $encontradosTotal = $this->getRegistrosElementosbyPadre($idTipoElemento,$idElementoPadre,'','');
        $encontrados = $this->getRegistrosElementosbyPadre($idTipoElemento,$idElementoPadre,$start,$limit);
        
        if ($encontrados) {            
            $num = count($encontradosTotal);
            foreach ($encontrados as $entidad)
            {
                $arr_encontrados[]=array('idElemento' =>$entidad->getId(),
                                         'nombreElemento' =>trim($entidad->getNombreElemento()));
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

    public function getRegistrosElementosbyPadre($idTipoElemento, $idElementoPadre, $start, $limit,$estado="'Activo'")
    {
        $boolBusqueda = false;
        
        $sql = "SELECT e
        
                FROM
                schemaBundle:InfoRelacionElemento re, schemaBundle:InfoElemento e,
                schemaBundle:AdmiModeloElemento me, schemaBundle:AdmiTipoElemento te
                        
                WHERE re.elementoIdB = e.id
                AND e.modeloElementoId = me.id
                AND me.tipoElementoId = te.id
                AND re.estado = 'Activo'
                AND re.elementoIdA = '$idElementoPadre'
                AND te.id = '$idTipoElemento'
                AND e.estado IN ($estado)
                ";
        
        /*
          AND LOWER(re.estado) not like LOWER('Eliminado')
          AND LOWER(e.estado) not like LOWER('Eliminado')
          AND LOWER(me.estado) not like LOWER('Eliminado')
          AND LOWER(te.estado) not like LOWER('Eliminado') */
        
        $query = $this->_em->createQuery($sql);
        
        if($start!='' && !$boolBusqueda && $limit!='')
            $datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
        else if($start!='' && !$boolBusqueda && $limit=='')
            $datos = $query->setFirstResult($start)->getResult();
        else if(($start=='' || $boolBusqueda) && $limit!='')
            $datos = $query->setMaxResults($limit)->getResult();
        else
            $datos = $query->getResult();
        
        return $datos;
    }

    public function getPadrebyHijo($idElementoHijo)
    {
        $boolBusqueda = false;
        
        $sql = "SELECT e.id,e.nombreElemento
        
FROM
schemaBundle:InfoRelacionElemento re, schemaBundle:InfoElemento e
        
WHERE re.elementoIdA = e.id
AND re.elementoIdB = '$idElementoHijo'
";
        
        /*
          AND LOWER(re.estado) not like LOWER('Eliminado')
          AND LOWER(e.estado) not like LOWER('Eliminado')
          AND LOWER(me.estado) not like LOWER('Eliminado')
          AND LOWER(te.estado) not like LOWER('Eliminado') */
        
        $query = $this->_em->createQuery($sql);
        
        $datos = $query->getSingleResult();
        
        return $datos;
    }

    /**
     * Funcion que genera el json para consultar
     * la relacion elemento (contenedor - contenido).
     *
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 14-10-2014
     * @param int           $elementoIdA        id elemento A
     * @param int           $elementoIdB        id elemento B
     * @param int           $start              numero de inicio para el limit
     * @param int           $limit              numero de fin para el limit
     * @param int           $idEmpresa          id de la empresa en sesion
     * @param EntityManager $em                 entity manager para db_infraestructura
     */
    public function generarJsonRelacionElemento($elementoIdA,$elementoIdB,$start,$limit,$idEmpresa){
        $arr_encontrados = array();
        
        $resultado = $this->getRelacionElemento($elementoIdA, $elementoIdB, $start, $limit);
        
        $encontrados = $resultado['registros'];
        $encontradosTotal = $resultado['total'];
        
        if ($encontrados) {
            
            $num = $encontradosTotal;
                
            foreach ($encontrados as $entidad){
                $empresaElementoA = $this->_em->getRepository('schemaBundle:InfoEmpresaElemento')
                                       ->findOneBy(array( "elementoId" =>$entidad->getElementoIdA(), "empresaCod"=>$idEmpresa));
                if($empresaElementoA)
                {
                    $empresaElementoB = $this->_em->getRepository('schemaBundle:InfoEmpresaElemento')
                                       ->findOneBy(array( "elementoId" =>$entidad->getElementoIdB(), "empresaCod"=>$idEmpresa));
                    if($empresaElementoB)
                    {
                        $elementoA = $this->_em->getRepository('schemaBundle:InfoElemento')->find($entidad->getElementoIdA());
                        $elementoB = $this->_em->getRepository('schemaBundle:InfoElemento')->find($entidad->getElementoIdB());
                        
                        $arr_encontrados[]=array('idRelacionElemento'   =>  $entidad->getId(),
                                                 'elementoIdA'          =>  $elementoA->getId(),
                                                 'nombreElementoA'      =>  $elementoA->getNombreElemento(),
                                                 'tipoElementoA'        =>  $elementoA->getModeloElementoId()->getTipoElementoId()
                                ->getNombreTipoElemento(),
                                                 'elementoIdB'          =>  $elementoB->getId(),
                                                 'nombreElementoB'      =>  $elementoB->getNombreElemento(),
                                                 'tipoElementoB'        =>  $elementoB->getModeloElementoId()->getTipoElementoId()
                                ->getNombreTipoElemento(),
                                                 'estado'               =>  trim($entidad->getEstado()),
                                                 'action3'              =>  (trim($entidad->getEstado())=='Eliminado' ? 
                                                                            'button-grid-invisible':'button-grid-delete')
                        );
                    }
                }
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
     * Funcion que realiza la consulta a la base
     * de datos para obtener los datos de la relacion elemento
     *
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 14-10-2014
     * @param int           $elementoIdA        id elemento A
     * @param int           $elementoIdB        id elemento B
     * @param int           $start              numero de inicio para el limit
     * @param int           $limit              numero de fin para el limit
     */
    public function getRelacionElemento($idElementoA,$idElementoB,$start,$limit){
        $qb = $this->_em->createQueryBuilder();
        $qbC = $this->_em->createQueryBuilder();
        $qb->select('e');
        $qb->from('schemaBundle:InfoRelacionElemento','e');

        $qbC->select('count(e.id)');
        $qbC->from('schemaBundle:InfoRelacionElemento','e');            
        
        if($idElementoA!=""){
            $qb->where( 'e.elementoIdA = ?1');
            $qb->setParameter(1, $idElementoA);
            
            $qbC->where( 'e.elementoIdA = ?1');
            $qbC->setParameter(1, $idElementoA);
        }
        
        if($idElementoB!=""){
            $qb->andWhere( 'e.elementoIdB = ?2');
            $qb->setParameter(2, $idElementoB);
            
            $qbC->andWhere( 'e.elementoIdB = ?2');
            $qbC->setParameter(2, $idElementoB);
        }
        
//contar cuantos datos trae en total
        $total = $qbC->getQuery()->getSingleScalarResult();
        
//datos con limits
        if($start!='')
            $qb->setFirstResult($start);
        if($limit!='')
            $qb->setMaxResults($limit);
        $query = $qb->getQuery();
        
        $datos = $query->getResult();
        $resultado['registros']=$datos;
        $resultado['total']=$total;
        
        return $resultado;
    }
    
    /**
     * Funcion que realiza la generacion de Json de Unidades de Rack
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 26-02-2015
     * @param int           $elementoIdA        id elemento A
     * @param int           $elementoIdB        id elemento B
     * @param int           $start              numero de inicio para el limit
     * @param int           $limit              numero de fin para el limit
     */
    public function generarJsonUnidadesElementosByPadre($idTipoElemento, $idElementoPadre, $start, $limit)
    {        
        $arrayRegistros        = array();
        $arrayEncontradosTotal = $this->getRegistrosElementosbyPadre($idTipoElemento,$idElementoPadre,'','');
        $arrayEncontrados      = $this->getRegistrosElementosbyPadre($idTipoElemento,$idElementoPadre,$start,$limit);
        if ($arrayEncontrados) 
        {            
            $num = count($arrayEncontradosTotal);            
            foreach ($arrayEncontrados as $entidad)
            {
                $strEstado                 = '';
                $strNombreElementoUnidad   = "";
                $objRelacionElementoUnidad = $this->_em->getRepository('schemaBundle:InfoRelacionElemento')
                                                       ->findBy(array("elementoIdA" => $entidad->getId(),
                                                                      "estado"      =>"Activo"));
                if ( $objRelacionElementoUnidad )
                {
                    $strEstado               = 'Ocupado';
                    $intBanderaUnidades      = 0;
                    for($i=0;$i<count($objRelacionElementoUnidad);$i++)
                    {
                        if($intBanderaUnidades!=0)
                        {
                            $strNombreElementoUnidad = $strNombreElementoUnidad." , ";
                        } 
                        $objElemento             = $this->_em->getRepository('schemaBundle:InfoElemento')
                                                        ->find($objRelacionElementoUnidad[$i]->getElementoIdB());
                        $strNombreElementoUnidad = $strNombreElementoUnidad.$objElemento->getNombreElemento();
                        $intBanderaUnidades      = $intBanderaUnidades + 1;
                    }
                        
                    
                }
                else
                {
                    $strEstado = 'Libre';
                }
                $arrayRegistros[]=array('idElemento'          => $entidad->getId(),
                                         'nombreElemento'       => trim($entidad->getNombreElemento()),
                                         'nombreElementoUnidad' => $strNombreElementoUnidad,
                                         'estado'               => $strEstado,
                                         'nombreEstado'         => trim($entidad->getNombreElemento()).' / '.$strEstado
                                        );
            }
            
            $data      = json_encode($arrayRegistros);
            $resultado = '{"total":"'.$num.'","encontrados":'.$data.'}';

            return $resultado;
        }
        else
        {
            $resultado = '{"total":"0","encontrados":[]}';

            return $resultado;
        }        
    }
}

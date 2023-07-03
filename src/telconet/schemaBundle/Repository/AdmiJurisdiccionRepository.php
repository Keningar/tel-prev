<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use \telconet\schemaBundle\Entity\ReturnResponse;

class AdmiJurisdiccionRepository extends EntityRepository
{
    /**
     * Documentación para el método generarJsonJurisdicciones
     * 
     * Función que obtiene un listado en formato JSON de las jurisdicciones por empresa.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 13-07-2016
     * @since   1.0
     * Envío de nuevo parámetro en la obtención de las jurisdicciones.
     * 
     * @param integer $jurisdiccionId código de la jurisdicción.
     * @param integer $idEmpresa      código de la empresa.
     * @param String  $estado         estado de la jurisdicción.
     * @param String  $start          índice de inicio de la paginación
     * @param String  $limit          índice del final de la paginación
     * 
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.2 01-09-2016 Se suprime el ultimo parametro de la función getJurisdicciones
     *                         $boolFromAdmin anteriormente se envia true, se suprime ese parametro
     *                         se encontraba filtrando las jurisdicciones que estaban en estado eliminado
     * 
     * @return String Cadena JSON del listado de jurisdicciones.
     */
    public function generarJsonJurisdicciones($jurisdiccionId, $idEmpresa, $estado, $start, $limit)
    {
        $arr_encontrados   = array();
        $jurisdiccionTotal = $this->getJurisdicciones($jurisdiccionId, $idEmpresa, $estado, '', '');
        $jurisdicciones    = $this->getJurisdicciones($jurisdiccionId, $idEmpresa, $estado, $start, $limit);

        if ($jurisdicciones) {
            
            $num = count($jurisdiccionTotal);
            
            foreach ($jurisdicciones as $jurisdiccion)
            {
                // Se ajusta la descripción del estado de la jurisdicción si aplica.
                $arr_encontrados[]=array('idJurisdiccion' =>$jurisdiccion->getId(),
                                         'nombreJurisdiccion' =>trim($jurisdiccion->getNombreJurisdiccion()),
                                         'estado'  => (trim($jurisdiccion->getEstado()) == 'Eliminado' ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (trim($jurisdiccion->getEstado())=='Eliminado' ? 'button-grid-invisible':'button-grid-edit'),
                                         'action3' => (trim($jurisdiccion->getEstado())=='Eliminado' ? 'button-grid-invisible':'button-grid-delete'));
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
     * getJurisdiccionPorCanton
     * obtiene la juridiccion segun el canton y la empresa
     *
     * @param integer $cantonId
     * @param integer $empresaId
     * @param string $estado
     *
     * @return array $datos
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 12-02-2016
     */
    public function getJurisdiccionPorCanton($cantonId, $empresaId, $estado)
    {
        $query = $this->_em->createQuery();


        $sql = "SELECT j
                FROM schemaBundle:AdmiCantonJurisdiccion cj,
                schemaBundle:AdmiJurisdiccion j,
                schemaBundle:InfoOficinaGrupo og
                WHERE cj.cantonId = :cantonId
                AND j.id = cj.jurisdiccionId
                AND j.oficinaId = og.id
                AND og.empresaId = :empresaId";

        $query->setParameter('cantonId', $cantonId);
        $query->setParameter('empresaId', $empresaId);
        
        if($estado)
        {
            $sql .= " AND j.estado = :estado ";
            $query->setParameter('estado', $estado);
        }

        $query->setDQL($sql);

        $datos = $query->getResult();
        return $datos;
    }

    /**
     * Documentación para el método getJurisdicciones
     * 
     * Función que obtiene un array de las jurisdicciones por empresa.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 13-07-2016
     * @since   1.0
     * Manejo del filtrado del estado por inclusión o exclusión según el origen del consumo del método.
     * 
     * @param integer $jurisdiccionId código de la jurisdicción.
     * @param integer $idEmpresa      código de la empresa.
     * @param String  $estado         estado de la jurisdicción.
     * @param String  $start          índice de inicio de la paginación
     * @param String  $limit          índice del final de la paginación
     * @param boolean $boolFromAdmin  indicador que procede al filtrado del estado por inclusión o exclusión.
     * 
     * @return String Cadena JSON del listado de jurisdicciones.
     */
    public function getJurisdicciones($jurisdiccionId, $idEmpresa, $estado, $start, $limit, $boolFromAdmin = false)
    {
        $qb = $this->_em->createQueryBuilder();
            $qb->select('e')
               ->from('schemaBundle:AdmiJurisdiccion','e')
               ->from('schemaBundle:InfoOficinaGrupo','info_oficina_grupo')
               ->where('e.oficinaId = info_oficina_grupo');
               
        if($jurisdiccionId != "")
        {
            $qb->andWhere('upper(e.nombreJurisdiccion) like ?1');
            $qb->setParameter(1, '%' . strtoupper($jurisdiccionId) . '%');
        }
        
        if($estado != "Todos")
        {
            if($boolFromAdmin) // Si la consulta es para la administración de jurisdicciones.
            {
                if($estado == "Activo")
                {
                    $qb->andWhere('e.estado != ?2');
                    $estado = 'Eliminado';
                }
                else
                {
                    $qb->andWhere('e.estado = ?2');
                }
            }
            else
            {
                $qb->andWhere('e.estado != ?2');
            }
            $qb->setParameter(2, $estado);
        }
        
        if($idEmpresa!=""){
            $qb ->andWhere( 'info_oficina_grupo.empresaId = ?3');
            $qb->setParameter(3, $idEmpresa);
        }
        if($start!='')
            $qb->setFirstResult($start);   
        if($limit!='')
            $qb->setMaxResults($limit);
        $query = $qb->getQuery();               

        return $query->getResult();
    }
    
    public function getJurisdiccionesPorEmpresa($idEmpresa){
        return $qb =$this->createQueryBuilder("admi_jurisdiccion")
            ->select('admi_jurisdiccion')
            ->from('schemaBundle:InfoOficinaGrupo','info_oficina_grupo')
            ->where("admi_jurisdiccion.estado != 'Eliminado'")
            ->andWhere("admi_jurisdiccion.oficinaId =info_oficina_grupo")
            ->andWhere("info_oficina_grupo.empresaId = '".$idEmpresa."'")
            ->orderBy('admi_jurisdiccion.nombreJurisdiccion', 'ASC');
    }

    public function getJurisdiccionesPorEmpresaSinEstado($intIdEmpresa, $strJurisdiccion)
    {
        $objQb = $this->createQueryBuilder("admi_jurisdiccion")
            ->select('admi_jurisdiccion')
            ->from('schemaBundle:InfoOficinaGrupo','info_oficina_grupo')
            // ->where("admi_jurisdiccion.estado != 'Eliminado'")
            ->where("admi_jurisdiccion.oficinaId =info_oficina_grupo")
            ->andWhere("info_oficina_grupo.empresaId = '".$intIdEmpresa."'");
            if ($strJurisdiccion != "") 
            {
                $objQb->andWhere('upper(admi_jurisdiccion.nombreJurisdiccion) like ?1');
                $objQb->setParameter(1, '%' . strtoupper($strJurisdiccion) . '%');
            }
            $objQb->orderBy('admi_jurisdiccion.nombreJurisdiccion', 'ASC');
            return $objQb;
    }

    public function getJurisdiccionesTodasLasEmpresas($strJurisdiccion, $strIdEmpresa)
    {
        $objQb =$this->createQueryBuilder("admi_jurisdiccion")
        ->select('admi_jurisdiccion')
        ->from('schemaBundle:InfoOficinaGrupo','info_oficina_grupo')
        ->where("admi_jurisdiccion.oficinaId =info_oficina_grupo");
        if ($strIdEmpresa != null && $strIdEmpresa != "")
        {
            $objQb ->andWhere("info_oficina_grupo.empresaId = '".$strIdEmpresa."'");
        }
        if ($strJurisdiccion != "") 
        {
            $objQb->andWhere('upper(admi_jurisdiccion.nombreJurisdiccion) like ?1');
            $objQb->setParameter(1, '%' . strtoupper($strJurisdiccion) . '%');
        }
        $objQb->orderBy('admi_jurisdiccion.nombreJurisdiccion', 'ASC');
        return $objQb;
    }

    
    /**
     * Documentación para el método 'getResultadoJurisdiccionesPorEmpresa'.
     *
     * Retorna un listado de empresasGrupo ordenada por nombre de jurisdicción.
     *
     * @param Integer $intCodEmpresa id de la empresa
     *
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 03-08-2016
     */
    public function getResultadoJurisdiccionesPorEmpresa($idEmpresa)
    {
        return $this->getJurisdiccionesPorEmpresa($idEmpresa)->getQuery()->getResult();
    }

    public function getResultadoJurisdiccionesPorEmpresaSinEstado($intIdEmpresa, $strJurisdiccion)
    {
        return $this->getJurisdiccionesPorEmpresaSinEstado($intIdEmpresa, $strJurisdiccion)->getQuery()->getResult();
    }

    /**
     * Documentación para el método 'generarJsonJurisdiccionesPorEmpresa'.
     *
     * Retorna una lista empresasGrupo paginada y ordenada por nombre de jurisdicción.
     *
     * @param Integer $intCodEmpresa id de la empresa
     *
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 16-01-2016
     *
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 26-06-2016
     * Se modifica el nombre del parámetro intIdEmpresa por $intCodEmpresa
     */
    public function generarJsonJurisdiccionesPorEmpresa($intCodEmpresa)
    {
        $listaJurisdicciones = $this->getJurisdiccionesPorEmpresa($intCodEmpresa)->getQuery()->getResult();
        $arrayJurisdicciones = array();
        
        foreach($listaJurisdicciones as $entityJurisdiccion)
        {
            $arrayJurisdicciones[] = array("id_jurisdiccion"     => $entityJurisdiccion->getId(), 
                                           "nombre_jurisdiccion" => $entityJurisdiccion->getNombreJurisdiccion());
        }
        return '{"total":"' . count($arrayJurisdicciones) . '","encontrados":' . json_encode($arrayJurisdicciones) . '}';
    }

    public function generarJsonJurisdiccionesTodasLasEmpresa($strJurisdiccion, $strIdEmpresa)
    {
        $arrayDataJurisdicciones = $this->getJurisdiccionesTodasLasEmpresas($strJurisdiccion, $strIdEmpresa)->getQuery()->getResult();
        $arrayJurisdicciones = array();
        
        $arrayJurisdicciones[] = array("id_jurisdiccion"     => -1, 
        "nombre_jurisdiccion" => "Todas");

        foreach($arrayDataJurisdicciones as $entityJurisdiccion)
        {
            $arrayJurisdicciones[] = array("id_jurisdiccion"     => $entityJurisdiccion->getId(), 
                                           "nombre_jurisdiccion" => $entityJurisdiccion->getNombreJurisdiccion());
        }
        return '{"total":"' . count($arrayJurisdicciones) . '","encontrados":' . json_encode($arrayJurisdicciones) . '}';
    }

    /**
     * Devuelve un query builder para obtener las jurisdicciones (puntos cobertura) activas.
     * NOTA: No se filtra por empresa.
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function findJuridisccionesActivas()
	{
		return $qb =$this->createQueryBuilder("t")
		->select("a")
		->from('schemaBundle:AdmiJurisdiccion a','')->where("a.estado='Activo'");
	}    

    public function getJurisdiccionesPorNombre($nombre){
                $nombre=  strtoupper($nombre);               
                $criterio_nombre='';                    
                if ($nombre){       
                    $criterio_nombre=" UPPER(a.nombreJurisdiccion) like '%$nombre%' AND ";
                }                 
		$query = $this->getEntityManager()->createQuery("
                SELECT a
                FROM
                schemaBundle:AdmiJurisdiccion a
                WHERE
                $criterio_nombre
                a.estado not in ('Eliminado','Inactivo')");
		$datos = $query->getResult();
		return $datos;        
    }     

    public function getJurisdiccionesPorNombrePorEmpresa($nombre, $codEmpresa) {
        $query = $this->_em->createQuery("
                SELECT a
                FROM schemaBundle:AdmiJurisdiccion a, schemaBundle:InfoOficinaGrupo b
                WHERE "
                . ($nombre ? "UPPER(a.nombreJurisdiccion) like :nombre AND " : "") .
                " a.oficinaId = b.id
                AND b.empresaId = :codEmpresa
                AND a.estado in ('Activo','Modificado')");
        $query->setParameter('codEmpresa', $codEmpresa);
        if ($nombre)
        {
            $query->setParameter('nombre', '%'.strtoupper($nombre).'%');
        }
        $datos = $query->getResult();
        return $datos;        
    } 
    
    public function findJurisdiccionXEmpresa($nombre='', $codEmpresa='', $start='', $limit='')
    {
        $arr_encontrados = array();
        
        $where = "";
        if($nombre && $nombre!="")
        {
            $where = "AND LOWER(j.nombreJurisdiccion) like LOWER('%$nombre%') ";
        }
        
        $sql = "SELECT j     
                FROM 
                schemaBundle:AdmiJurisdiccion j, schemaBundle:InfoOficinaGrupo og,
                schemaBundle:InfoEmpresaGrupo eg 
                WHERE j.oficinaId = og.id
                AND og.empresaId = eg.id  
                AND eg.id = '$codEmpresa'        
                AND LOWER(og.estado) not like LOWER('Eliminado') 
                AND LOWER(eg.estado) not like LOWER('Eliminado') 
                AND LOWER(j.estado) not like LOWER('Eliminado') 
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
                $arr_encontrados[]=array('id_segmento' =>$entity->getId(),
                                         'segmento' =>($entity->getNombreJurisdiccion())? $entity->getNombreJurisdiccion() : "");
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
    
    public function generarJsonElementosXSegmento($id_param, $codEmpresa)
    {
        $arr_encontrados = array();
        
        $sql = "SELECT e 
                FROM 
                schemaBundle:InfoElemento e,
                schemaBundle:InfoEmpresaElementoUbica eeu,
                schemaBundle:InfoUbicacion u,
                schemaBundle:AdmiParroquia p,
                schemaBundle:AdmiCanton c,
                schemaBundle:AdmiCantonJurisdiccion cj,
                schemaBundle:AdmiJurisdiccion j 
                
                WHERE cj.jurisdiccionId = j.id 
                AND cj.cantonId = c.id 
                AND p.cantonId = c.id 
                AND u.parroquiaId = p.id 
                AND eeu.ubicacionId = u.id 
                AND eeu.elementoId = e.id 
                AND eeu.empresaCod = '$codEmpresa'   
                AND j.id = '$id_param'  
        
                AND LOWER(j.estado) not like LOWER('Eliminado') 
                AND LOWER(cj.estado) not like LOWER('Eliminado') 
                AND LOWER(c.estado) not like LOWER('Eliminado') 
                AND LOWER(p.estado) not like LOWER('Eliminado') 
               ";
        
        $query = $this->_em->createQuery($sql);
        $registros = $query->getResult();
        
        if ($registros) {
            $num = count($registros);  
            
            foreach ($registros as $entity)
            {
                $arr_encontrados[]=array('id_parte_afectada' =>$entity->getId(),
                                         'nombre_parte_afectada' =>$entity->getNombreElemento(),
                                         'id_descripcion_1' =>'',
                                         'nombre_descripcion_1' =>'',
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
    
    public function generarJsonLoginesXSegmento($id_param, $codEmpresa)
    {
        $arr_encontrados = array();
        
        $sql = "SELECT pu 
                FROM 
                schemaBundle:InfoPersona pe,
                schemaBundle:InfoPersonaEmpresaRol per,
                schemaBundle:InfoPunto pu,
                schemaBundle:InfoServicio s,
                schemaBundle:InfoInterfaceElemento ie,
                schemaBundle:InfoElemento e,
                schemaBundle:InfoEmpresaElementoUbica eeu,
                schemaBundle:InfoUbicacion u,
                schemaBundle:AdmiParroquia p,
                schemaBundle:AdmiCanton c,
                schemaBundle:AdmiCantonJurisdiccion cj,
                schemaBundle:AdmiJurisdiccion j ,
                schemaBundle:InfoServicioTecnico ift
                
                WHERE 
                per.personaId = pe.id 
                AND pu.personaEmpresaRolId = per.id 
                AND s.puntoId = pu.id 
                AND ift.interfaceElementoId = ie.id               
                AND ift.servicioId = s.id
                AND cj.jurisdiccionId = j.id 
                AND cj.cantonId = c.id 
                AND p.cantonId = c.id 
                AND u.parroquiaId = p.id 
                AND eeu.ubicacionId = u.id 
                AND eeu.elementoId = e.id 
                AND ie.elementoId = e.id 
                AND eeu.empresaCod = '$codEmpresa'   
                AND j.id = '$id_param'  
        
                AND LOWER(j.estado) not like LOWER('Eliminado') 
                AND LOWER(cj.estado) not like LOWER('Eliminado') 
                AND LOWER(c.estado) not like LOWER('Eliminado') 
                AND LOWER(p.estado) not like LOWER('Eliminado') 
                AND LOWER(s.estado) not like LOWER('Eliminado') 
                AND LOWER(pu.estado) not like LOWER('Eliminado') 
                AND LOWER(per.estado) not like LOWER('Eliminado') 
                AND LOWER(pe.estado) not like LOWER('Eliminado') 
               ";
        
        $query = $this->_em->createQuery($sql);
        $registros = $query->getResult();
        
        if ($registros) {
            $num = count($registros);  
            
            foreach ($registros as $data)
            {
                $idCliente = ($data->getPersonaEmpresaRolId() ? ($data->getPersonaEmpresaRolId()->getPersonaId() ?
                             ($data->getPersonaEmpresaRolId()->getPersonaId()->getId() ? $data->getPersonaEmpresaRolId()->getPersonaId()->getId() : "" )  : "") : "");
                $nombreCliente = ($data->getPersonaEmpresaRolId() ?  ($data->getPersonaEmpresaRolId()->getPersonaId() ?
                                 ($data->getPersonaEmpresaRolId()->getPersonaId()->getRazonSocial() ? $data->getPersonaEmpresaRolId()->getPersonaId()->getRazonSocial() : $data->getPersonaEmpresaRolId()->getPersonaId()->getNombres() . " " . $data->getPersonaEmpresaRolId()->getPersonaId()->getApellidos() ) : "") : "");
                
                
                $arr_encontrados[]=array('id_parte_afectada' =>$data->getId(),
                                         'nombre_parte_afectada' =>$data->getLogin(),
                                         'id_descripcion_1' =>$idCliente,
                                         'nombre_descripcion_1' =>$nombreCliente,
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
     /**
     * getPtosCoberturaByEmpresa, obtiene la informacion de los Puntos de Cobertura por empresa en sesion y estados.
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 25-07-2017
     * Costo: 7
     * @param array $arrayParametros[]
     *              'strPrefijoEmpresa'          => Recibe el prefijo de la empresa en sesión.
     *              'arrayEstadoJurisdiccion'    => Recibe estados de la Jurisdiccion 
     *              'strEstadoOficina'           => Recibe estado de la oficina
     *                              ]
     * @return \telconet\schemaBundle\Entity\ReturnResponse Retorna un objeto con el resultado de la consulta
     */
    public function getPtosCoberturaByEmpresa($arrayParametros)
    {
        $objReturnResponse = new ReturnResponse();
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_NOT_RESULT);
        $objReturnResponse->setStrStatus($objReturnResponse::NOT_RESULT);
        try
        {
            $objQueryCount = $this->_em->createQuery();
            
            $strQueryCount = "SELECT COUNT(iju) ";
            
            $objQuery = $this->_em->createQuery();
            
            $strQuery = "  SELECT iju.id            intIdObj,    "
                       ."  iju.nombreJurisdiccion strDescripcionObj ";
            $strFromQuery = "  FROM "
                               . "schemaBundle:AdmiJurisdiccion iju, "
                               . "schemaBundle:InfoOficinaGrupo iog, "
                               . "schemaBundle:InfoEmpresaGrupo ieg "
                               . "WHERE "
                               . "iju.oficinaId        = iog.id "
                               . "AND iog.empresaId    = ieg.id "
                               . "AND iju.estado       in (:arrayEstadoJurisdiccion) "
                               . "AND iog.estado       = :strEstadoOficina "
                               . "AND ieg.prefijo      = :strPrefijoEmpresa "; 
                                
            $objQuery->setParameter('arrayEstadoJurisdiccion' , $arrayParametros['arrayEstadoJurisdiccion']);
            $objQuery->setParameter('strEstadoOficina'        , $arrayParametros['strEstadoOficina']);
            $objQuery->setParameter('strPrefijoEmpresa'       , $arrayParametros['strPrefijoEmpresa']);
            $objQuery->setDQL($strQuery . $strFromQuery);
            $objReturnResponse->setRegistros($objQuery->getResult());
            $objReturnResponse->setTotal(0);
            if($objReturnResponse->getRegistros())
            { 
                $strQueryCount = $strQueryCount . $strFromQuery;
                $objQueryCount->setDQL($strQueryCount);
                $objReturnResponse->setTotal($objQueryCount->getSingleScalarResult());
            }
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS);
            $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
        }
        catch(\Exception $ex)
        {
            $objReturnResponse->setStrMessageStatus('Existio un error en getPtosCoberturaByEmpresa - ' . $ex->getMessage());
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
        }
        return $objReturnResponse;
    } 
    
    /**
     * Documentación para el método generarJsonJurisdicciones
     * 
     * Función que obtiene un listado en formato JSON de las jurisdicciones por empresa.
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.1 13-07-2016
     * @since   1.0
     * Envío de nuevo parámetro en la obtención de las jurisdicciones.
     *  
     * @return String Cadena JSON del listado de jurisdicciones.
     */
    public function generarJsonJurisdiccionesPorNombre($strNombre, $strCodEmpresa)
    {
        $arrayEncontrados   = array();
        $objJurisdiccionTotal = $this->getJurisdiccionesPorNombrePorEmpresa($strNombre, $strCodEmpresa);
        $objJurisdicciones    = $this->getJurisdiccionesPorNombrePorEmpresa($strNombre, $strCodEmpresa);

        if ($objJurisdicciones) {
            
            $intNum = count($objJurisdiccionTotal);
            
            foreach ($objJurisdicciones as $objJurisdiccion)
            {
                // Se ajusta la descripción del estado de la jurisdicción si aplica.
                $arrayEncontrados[]=array('idJurisdiccion' =>$objJurisdiccion->getId(),
                                         'nombreJurisdiccion' =>trim($objJurisdiccion->getNombreJurisdiccion()),
                                         'estado'  => (trim($objJurisdiccion->getEstado()) == 'Eliminado' ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (trim($objJurisdiccion->getEstado())=='Eliminado' ? 'button-grid-invisible':'button-grid-edit'),
                                         'action3' => (trim($objJurisdiccion->getEstado())=='Eliminado' ? 'button-grid-invisible':'button-grid-delete'));
            }

            if($intNum == 0)
            {
               $arrayResultado= array('total' => 1 ,
                                 'encontrados' => array('idConectorInterface' => 0 , 'nombreConectorInterface' => 'Ninguno','idConectorInterface' => 0 , 'nombreConectorInterface' => 'Ninguno', 'estado' => 'Ninguno'));
                $arrayResultado = json_encode( $arrayResultado);

                return $arrayResultado;
            }
            else
            {
                $data=json_encode($arrayEncontrados);
                $arrayResultado= '{"total":"'.$intNum.'","encontrados":'.$data.'}';

                return $arrayResultado;
            }
        }
        else
        {
            $arrayResultado= '{"total":"0","encontrados":[]}';

            return $arrayResultado;
        }
        
    }
    
    
    
}

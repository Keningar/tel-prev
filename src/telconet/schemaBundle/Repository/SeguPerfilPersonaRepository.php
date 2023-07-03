<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class SeguPerfilPersonaRepository extends EntityRepository
{
    /**
     * Funcion que sirve para ejecutar un query que verifica
     * si una persona tiene un perfil definido.
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 23-07-2015
     * @param String $nombrePerfil
     * @param integer $idPersona
     */
    public function getAccesoPorPerfilPersona($nombrePerfil, $idPersona)
    {
        $rsm = new ResultSetMappingBuilder($this->_em);
        $query = $this->_em->createNativeQuery(null, $rsm);
        
        $sql = "SELECT PERFIL_PERSONA.PERFIL_ID PERFIL_ID
                FROM SEGU_PERFIL_PERSONA PERFIL_PERSONA,
                SIST_PERFIL PERFIL
                WHERE PERFIL_PERSONA.PERFIL_ID = PERFIL.ID_PERFIL
                AND PERFIL.NOMBRE_PERFIL = :nombrePerfil
                AND PERFIL_PERSONA.PERSONA_ID = :idPersona";
        
        $rsm->addScalarResult('PERFIL_ID',   'idPerfil',   'integer');
        
        $query->setParameter("nombrePerfil",    $nombrePerfil);
        $query->setParameter("idPersona",       $idPersona);
        
        $query->setSQL($sql);
        $servicios = $query->getResult();

        return $servicios;
    }        
    
 public function loadIdPerfilEmpleado($arrayVariables)
    {
	$whereVar = "";
	if($arrayVariables && count($arrayVariables)>0)
	{
            if(isset($arrayVariables["id_persona"]))
            {
                if($arrayVariables["id_persona"] && $arrayVariables["id_persona"]!="")
                {
                    $whereVar .= "AND spe.personaId = '".trim($arrayVariables["id_persona"])."' ";
                }
            }
            if(isset($arrayVariables["departamento_oficina_id"]))
            {
                if($arrayVariables["departamento_oficina_id"] && $arrayVariables["departamento_oficina_id"]!="")
                {
                    $whereVar .= "AND spe.departamentoOficinaId = '".trim($arrayVariables["departamento_oficina_id"])."' ";
                }
            }
            if(isset($arrayVariables["departamento_id"]))
            {
                if($arrayVariables["departamento_id"] && $arrayVariables["departamento_id"]!="")
                {
                    $whereVar .= "AND spe.departamentoId = '".trim($arrayVariables["departamento_id"])."' ";
                }
            }
            if(isset($arrayVariables["area_id"]))
            {
                if($arrayVariables["area_id"] && $arrayVariables["area_id"]!="")
                {
                    $whereVar .= "AND spe.areaId = '".trim($arrayVariables["area_id"])."' ";
                }
            }
            if(isset($arrayVariables["oficina_id"]))
            {
                if($arrayVariables["oficina_id"] && $arrayVariables["oficina_id"]!="")
                {
                    $whereVar .= "AND spe.oficinaId = '".trim($arrayVariables["oficina_id"])."' ";
                }
            }
            if(isset($arrayVariables["empresa_id"]))
            {
                if($arrayVariables["empresa_id"] && $arrayVariables["empresa_id"]!="")
                {
                    $whereVar .= "AND spe.empresaId = '".trim($arrayVariables["empresa_id"])."' ";
                }
            }
	}

	$query =  "SELECT sp.id ".
		  "FROM schemaBundle:SeguPerfilPersona spe ,schemaBundle:SistPerfil sp ".
		  "WHERE spe.perfilId = sp.id $whereVar ";
        
	return $this->_em->createQuery($query)->getResult();
    }
    public function loadPerfilEmpleado($arrayVariables)
    {
	$whereVar = "";
	if($arrayVariables && count($arrayVariables)>0)
	{
            if(isset($arrayVariables["id_persona"]))
            {
                if($arrayVariables["id_persona"] && $arrayVariables["id_persona"]!="")
                {
                    $whereVar .= "spe.personaId = '".trim($arrayVariables["id_persona"])."' ";
                }
            }
            if(isset($arrayVariables["departamento_oficina_id"]))
            {
                if($arrayVariables["departamento_oficina_id"] && $arrayVariables["departamento_oficina_id"]!="")
                {
                    $whereVar .= "AND spe.departamentoOficinaId = '".trim($arrayVariables["departamento_oficina_id"])."' ";
                }
            }
            if(isset($arrayVariables["departamento_id"]))
            {
                if($arrayVariables["departamento_id"] && $arrayVariables["departamento_id"]!="")
                {
                    $whereVar .= "AND spe.departamentoId = '".trim($arrayVariables["departamento_id"])."' ";
                }
            }
            if(isset($arrayVariables["area_id"]))
            {
                if($arrayVariables["area_id"] && $arrayVariables["area_id"]!="")
                {
                    $whereVar .= "AND spe.areaId = '".trim($arrayVariables["area_id"])."' ";
                }
            }
            if(isset($arrayVariables["oficina_id"]))
            {
                if($arrayVariables["oficina_id"] && $arrayVariables["oficina_id"]!="")
                {
                    $whereVar .= "AND spe.oficinaId = '".trim($arrayVariables["oficina_id"])."' ";
                }
            }
            if(isset($arrayVariables["empresa_id"]))
            {
                if($arrayVariables["empresa_id"] && $arrayVariables["empresa_id"]!="")
                {
                    $whereVar .= "AND spe.empresaId = '".trim($arrayVariables["empresa_id"])."' ";
                }
            }
	}

	$query =  "SELECT spe ".
		  "FROM schemaBundle:SeguPerfilPersona spe ".
		  "WHERE $whereVar ";
        
	return $this->_em->createQuery($query)->getResult();
    }
    
    
    
    public function cargarPerfilesGroupPersona()
    {
	$query =  "SELECT spe.personaId ".
		  "FROM schemaBundle:SeguPerfilPersona spe ".
		  "GROUP BY spe.personaId ";
        
	return $this->_em->createQuery($query)->getResult();
    }
    
    /**
     * generarJson
     * 
     * Genera un Json con la informacion correspondiente a las personas encontradas bajo las criterios de búsquedas
     * ingresados por el usuario logueado.
     * 
     * @param entityManager $em
     * @param string        $nombre
     * @param string        $apellido
     * @param integer       $empresa
     * @param integer       $ciudad
     * @param integer       $departamento
     * @param integer       $start
     * @param integer       $limit
     * 
     * @return json $resultado
     * 
     * @author Modificado: Karen Rodríguez Véliz <kyrodriguez@telconet.ec>
     * @version 1.1 15-02-2020 - Al array de respuesta se agrega la action6 para reseteo de clave.
     * 
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.1 23-09-2015 - Se añade a la respuesta del Json la variable '$empresa' que corresponde al id de
     *                           la empresa del usuario seleccionado.
     * 
     * @version 1.0 Version Inicial
     * 
     */    
    public function generarJson($em, $nombre,$apellido,$empresa,$ciudad,$departamento,$start,$limit)
    {
        $arr_encontrados = array();
        
        $registrosTotal = $this->getRegistros($em, $nombre,$apellido,$empresa,$ciudad,$departamento, '', '');
        $registros      = $this->getRegistros($em, $nombre,$apellido,$empresa,$ciudad,$departamento, $start, $limit);
 
        if ($registros) 
        {
            $num = count($registrosTotal);
            
            foreach ($registros as $data)
            {                    
                $nombreSinFormato = trim($data->getNombres() . " " . $data->getApellidos());
                $nombreConFormato = ucwords(strtolower($nombreSinFormato));

                $arr_encontrados[] = array(
                                            'id_persona'     => $data->getId(),
                                            'empresa'        => $empresa,
                                            'nombre_persona' => trim($nombreConFormato),
                                            'estado'         => (false=='ELIMINADO' ? 'Eliminado':'Activo'),
                                            'action1'        => 'button-grid-show',
                                            'action2'        => (false=='ELIMINADO' ? 'icon-invisible':'button-grid-edit'),
                                            'action3'        => (false=='ELIMINADO' ? 'icon-invisible':'button-grid-delete'),
                                            'action4'        => (false=='ELIMINADO' ? 'icon-invisible':'button-grid-editarPerfil'),
                                            'action5'        => (false=='ELIMINADO' ? 'icon-invisible':'button-grid-verParametrosIniciales'),
                                            'action6'        => 'button-grid-reseteoClave',
                                          );
            }

            if($num == 0)
            {
               $resultado= array(
                                  'total'       => 1 ,
                                  'encontrados' => array(
                                                            'id_persona'     => 0 , 
                                                            'empresa'        => 0, 
                                                            'nombre_persona' => 'Ninguno',
                                                            'accion_id'      => 0 , 
                                                            'accion_nombre'  => 'Ninguno', 
                                                            'estado'         => 'Ninguno'
                                                        )
                                );
               
                $resultado = json_encode( $resultado);

                return $resultado;
            }
            else
            {
                $dataF      = json_encode($arr_encontrados);
                $resultado  = '{"total":"'.$num.'","encontrados":'.$dataF.'}';

                return $resultado;
            }
        }
        else
        {
            $resultado = '{"total":"0","encontrados":[]}';

            return $resultado;
        }
        
    }
	
    public function getRegistros($em, $nombre, $apellido , $empresa , $ciudad , $departamento , $start,$limit){
	
        $boolBusqueda = false; 
        $where = ""; 		
	
	if($nombre!="" && $nombre)		
		$where .= "AND UPPER(p.nombres) like UPPER(:nombre) ";
		
	if($apellido!="" && $apellido)		
		$where .= "AND UPPER(p.apellidos) like UPPER(:apellido) ";
        //se agrega codigo para agregar nuevos filtros     
        if (($empresa != "" && $empresa) || ($ciudad != "" && $ciudad) || ($departamento != "" && $departamento)) {
            $where .= "AND p.id IN  (SELECT per.personaIdValor  FROM schemaBundle:InfoPersonaEmpresaRol per, ";
            $where .= "schemaBundle:InfoOficinaGrupo og,    schemaBundle:AdmiCanton ac , ";
            $where .= "schemaBundle:AdmiDepartamento ad  WHERE per.departamentoId=ad.id  AND per.oficinaId     = og.id ";
            $where .= "AND og.cantonId       =ac.id ";
            $where .= "AND per.estado=:estadoPER ";
            if ($empresa != "" && $empresa) {
                $where .= "AND og.empresaId = :empresa ";
            }
            if ($ciudad != "" && $ciudad) {
                $where .= "AND ac.id = :ciudad ";
            }
            if ($departamento != "" && $departamento) {
                $where .= "AND ad.id = :departamento ";
            }
            $where .= " ) ";
        }





        $sql = "SELECT
		p 
                FROM 
		schemaBundle:InfoPersona p 
		WHERE 
		lower(p.estado) not like lower(:estado) 
		$where
		ORDER BY p.nombres, p.apellidos 
               ";  
			   
        $query = $this->_em->createQuery($sql);
        
        if($nombre!="" && $nombre)
	      $query->setParameter('nombre', '%' . $nombre . '%');
        if($apellido!="" && $apellido)
	      $query->setParameter('apellido', '%' . $apellido . '%');
        //se agrega codigo para agregar nuevos filtros
        if (($empresa != "" && $empresa) || ($ciudad != "" && $ciudad) || ($departamento != "" && $departamento)) {

            if ($empresa != "" && $empresa) {
                $query->setParameter('empresa', $empresa);
            }
            if ($ciudad != "" && $ciudad) {
                $query->setParameter('ciudad', $ciudad);
            }
            if ($departamento != "" && $departamento) {
                $query->setParameter('departamento', $departamento);
            }
            $query->setParameter('estadoPER', 'Activo');
        }

        $query->setParameter('estado', '%Eliminado%');
        
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

    /**
     * Documentación para el método 'getJsonAsignacionesPerfil'.
     *
     * consulta los perfiles asignados al cliente
     * @param String $nombre nombre del  perfil a buscar
     * @param int $idPersona identificador de la persona consultada
     * @param int $start inicio de registro a obtener en metodo (paginacion)
     * @param int $limit fin de registro a obtener en metodo (paginacion)
     * @return json.
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 06-11-2014
     */
    public function getJsonAsignacionesPerfil($nombre, $idPersona, $start, $limit)
    {
        $arr_encontrados = array();

        $perfilesTotal = $this->getPerfilesAsginados($nombre, $idPersona, '', '');
        $perfiles = $this->getPerfilesAsginados($nombre, $idPersona, $start, $limit);

        if($perfiles)
        {

            $num = count($perfilesTotal);

            foreach($perfiles as $perfil)
            {
                $arr_encontrados[] = array('id_perfil' => $perfil->getPerfilId()->getId(),
                    'nombre_perfil' => trim($perfil->getPerfilId()->getNombrePerfil()),
                    'estado' => (false == 'ELIMINADO' ? 'Eliminado' : 'Activo'),
                    'action1' => 'button-grid-show',
                    'action2' => (false == 'ELIMINADO' ? 'icon-invisible' : 'button-grid-edit'),
                    'action3' => (false == 'ELIMINADO' ? 'icon-invisible' : 'button-grid-delete'));
            }

            if($num == 0)
            {
                $resultado = array('total' => 1,
                                   'asignaciones' => array('id_perfil' => 0, 'nombre_perfil' => 'Ninguno', 'estado' => 'Ninguno', 
                                   'action1' => 'Ninguno', 'action2' => 'Ninguno', 'action3' => 'Ninguno'));
                $resultado = json_encode($resultado);

                return $resultado;
            }
            else
            {
                $data = json_encode($arr_encontrados);
                $resultado = '{"total":"' . $num . '","asignaciones":' . $data . '}';

                return $resultado;
            }
        }
        else
        {
            $resultado = '{"total":"0","asignaciones":[]}';
            return $resultado;
        }
    }

    /**
     * Documentación para el método 'getPerfilesAsginados'.
     *
     * consulta los registros de perfiles asignados al cliente
     * @param String $nombre nombre del  perfil a buscar
     * @param int $idPersona identificador de la persona consultada
     * @param int $start inicio de registro a obtener en metodo (paginacion)
     * @param int $limit fin de registro a obtener en metodo (paginacion)
     * @return lista de objetos SeguPerfilPersona.
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 06-11-2014
     */
    public function getPerfilesAsginados($nombre, $idPersona, $start, $limit)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('seguPerfilPersona')
            ->from('schemaBundle:SeguPerfilPersona', 'seguPerfilPersona')
            ->from('schemaBundle:SistPerfil', 'sistPerfil');
        $qb->where('seguPerfilPersona.personaId = ?1');
        $qb->andWhere('seguPerfilPersona.perfilId = sistPerfil.id');
        $qb->andWhere('sistPerfil.estado != ?2');
        $qb->setParameter(1, $idPersona);
        $qb->setParameter(2, 'Eliminado');

        if($nombre != "")
        {
            $qb->andWhere('LOWER(sistPerfil.nombrePerfil) like LOWER(?2)');
            $qb->setParameter(2, '%' . $nombre . '%');
        }
        if($start != '')
            $qb->setFirstResult($start);
        if($limit != '')
            $qb->setMaxResults($limit);

        $qb->orderBy('sistPerfil.id');
        $query = $qb->getQuery();

        return $query->getResult();
    }
    /**
     * Elimina los perfiles asignados
     * @param  int $intPesonaId
     * @return string $strMensaje
     *
     * @author Sofía Fernandez <sfernandez@telconet.ec>
     * @version 1.0 09-03-2018
     */
    public function validarPerfilPersonalExterno($intPesonaId)
    {
        $intTotalRegistros = 1;
        $strSql     = 'BEGIN DB_SEGURIDAD.SEKG_TRANSACCION.P_VALIDA_PERFIL_PER_EXTERNO(:intPesonaId,:intTotalRegistros); END;';
        try
        {
            $strStmt    = $this->_em->getConnection()->prepare($strSql);
            $strStmt->bindParam('intPesonaId', $intPesonaId);
            $strStmt->bindParam('intTotalRegistros', $intTotalRegistros);
            $strStmt->execute();
            error_log('$intTotalRegistros: '.$intTotalRegistros);
        } 
        catch (\Exception $ex) 
        {
            error_log("SeguPerfilPersonaRepository->validarPerfilPersonalExterno " . $ex->getMessage());
        }
        return ($intTotalRegistros);
    }
    /**
     * Función que retorna la información de los perfiles  
     * en TMOperaciones asociados a una persona
     * 
     * @param  array $arrayPerfilPersona
     * @return string $strMensaje
     *
     * @author Wilmer Vera G. <wvera@telconet.ec>
     * @version 1.0, 17-09-2019
     */
    public function getPerfilesTMOperaciones($arrayPerfilPersona)
    {
        $objResultMapping   = new ResultSetMappingBuilder($this->_em);
        $objNativeQuery     = $this->_em->createNativeQuery(null, $objResultMapping);
        
        $strSql = "SELECT SSP.NOMBRE_PERFIL, SPP.PERFIL_ID 
                    FROM DB_SEGURIDAD.SEGU_PERFIL_PERSONA SPP, DB_SEGURIDAD.SIST_PERFIL SSP 
                    WHERE PERSONA_ID = 
                    (
                        SELECT ID_PERSONA 
                        FROM INFO_PERSONA IP
                        WHERE IP.ID_PERSONA = :idPersona
                    )
                    AND SPP.PERFIL_ID   = SSP.ID_PERFIL
                    AND SSP.ESTADO      = 'Activo'
                    AND SSP.NOMBRE_PERFIL LIKE '%TMO%' ";
        
        $objResultMapping->addScalarResult( 'PERFIL_ID',       'id_perfil',       'integer' );
        $objResultMapping->addScalarResult( 'NOMBRE_PERFIL',   'nombre_perfil',   'string'  );
        
        $objNativeQuery->setParameter( "idPersona", $arrayPerfilPersona['personaId'] );
        $objNativeQuery->setSQL($strSql);

        $arrayPerfilesTMOperaciones = $objNativeQuery->getArrayResult();
        return $arrayPerfilesTMOperaciones;
    }

}
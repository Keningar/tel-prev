<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class AdmiCantonRepository extends EntityRepository
{

    /**
     * 
     * Genera Json de los cantones
     * 
     * @author Codigo Inicial
     * @version 1.0 
     * 
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.1 - Se adiciona al json los siguientes parametros sigla
     *                codigo inec, región, zona.
     * 
     * @param array $parametros
     * @param string $nombre
     * @param string $estado
     * @param int $start
     * @param int $limit
     * @return json
     */
    public function generarJson($parametros, $nombre, $estado, $start, $limit)
    {
        $arr_encontrados = array();

        $registrosTotal  = $this->getRegistros($parametros, $nombre, $estado, '', '');
        $registros       = $this->getRegistros($parametros, $nombre, $estado, $start, $limit);

        if($registros)
        {
            $num = count($registrosTotal);
            foreach($registros as $data)
            {                
                $arr_encontrados[] = array('id_canton' => $data->getId(),
                    'nombre_canton'    => trim($data->getNombreCanton()),
                    'es_capital'       => trim($data->getEsCapital()),
                    'es_cabecera'      => trim($data->getEsCabecera()),
                    'nombre_provincia' => trim($data->getProvinciaId() ? $data->getProvinciaId()->getNombreProvincia() : "NA" ),
                    'estado'           => (strtolower(trim($data->getEstado())) == strtolower('ELIMINADO') ? 'Eliminado' : 'Activo'),
                    'sigla'            => strtoupper(trim($data->getSigla())),
                    'codigoInec'       => trim($data->getCodigoInec()),
                    'region'           => trim($data->getRegion()),
                    'zona'             => strtoupper(trim($data->getZona())),
                    'action1'          => 'button-grid-show',
                    'action2'          => (strtolower(trim($data->getEstado())) == strtolower('ELIMINADO') ? 'icon-invisible' : 'button-grid-edit'),
                    'action3'          => (strtolower(trim($data->getEstado())) == strtolower('ELIMINADO') ? 'icon-invisible' : 'button-grid-delete'));
            }

            if($num == 0)
            {
                $resultado = array('total' => 1,
                                   'encontrados' => array('id_canton'        => 0, 
                                                          'nombre_canton'    => 'Ninguno', 
                                                          'es_capital'       => 'NO', 
                                                          'es_cabecera'      => 'NO', 
                                                          'nombre_provincia' => 'Ninguno', 
                                                          'canton_id'        => 0, 
                                                          'canton_nombre'    => 'Ninguno',
                                                          'estado'           => 'Ninguno'));
                $resultado = json_encode($resultado);
                return $resultado;
            }
            else
            {
                $dataF     = json_encode($arr_encontrados);
                $resultado = '{"total":"' . $num . '","encontrados":' . $dataF . '}';
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
     * Funcion que sirve para obtener los cantones
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 19-01-2015
     * 
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.1 05-08-2016 Se adiciona validación para el filtro de busqueda de la pantalla de la admin cantones
     *
     * @author Duval Medina C. <dmedina@telconet.ec>
     * @version 1.2 2016-08-10 Incluir palabra reservada DISTINCT en query y ajustes varios
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 2016-12-28 Se agrega el filtro por región R1 o R2
     * 
     * @param array $parametros
     * @param string $nombre
     * @param string $estado
     * @param int $start
     * @param int $limit
     * @return arreglo con los registros de los cantones
     */
    public function getRegistros($parametros, $nombre, $estado, $start, $limit)
    {
        $boolBusqueda     = false;
        $where            = "";
        $query            = $this->_em->createQuery();

        $idPais           = (isset($parametros["idPais"])) ? $parametros["idPais"] : "";
        $idRegion         = (isset($parametros["idRegion"])) ? $parametros["idRegion"] : "";
        $idProvincia      = (isset($parametros["idProvincia"])) ? $parametros["idProvincia"] : "";

        if($nombre != "")
        {
            $boolBusqueda = true;
            $where       .= "AND LOWER(ca.nombreCanton) like LOWER(:nombre) ";
            $query->setParameter('nombre', '%' . $nombre . '%');
        }


        if(isset($idPais))
        {
            if($idPais && $idPais != "")
            {
                $boolBusqueda = true;
                $where       .= "AND pa.id = :idPais ";
                $query->setParameter('idPais', $idPais);
            }
        }
        if(isset($idRegion))
        {
            if($idRegion && $idRegion != "")
            {
                $boolBusqueda = true;
                $where       .= "AND re.id = :idRegion ";
                $query->setParameter('idRegion', $idRegion);
            }
        }
        if(isset($idProvincia))
        {
            if($idProvincia && $idProvincia != "")
            {
                $boolBusqueda = true;
                $where       .= "AND pr.id = :idProvincia ";
                $query->setParameter('idProvincia', $idProvincia);
            }
        }
        
        if(isset($parametros['strRegion']) && !empty($parametros['strRegion']))
        {
            $boolBusqueda = true;
            $where       .= "AND ca.region = :strRegion ";
            $query->setParameter('strRegion', $parametros['strRegion']);
        }

        if($estado != "Todos")
        {
            $boolBusqueda = true;
            if($estado == "Activo")
            {
                $where       .= "AND LOWER(ca.estado) not like LOWER(:estadoCanton) ";
                $query->setParameter('estadoCanton', 'Eliminado');
            }
            else if($estado == "Activo-Todos")
            {
                $where       .= "AND LOWER(ca.estado) not like LOWER(:estadoRegistro) ";
                $where       .= "AND LOWER(pr.estado) not like LOWER(:estadoRegistro) ";
                $where       .= "AND LOWER(re.estado) not like LOWER(:estadoRegistro) ";
                $where       .= "AND LOWER(pa.estado) not like LOWER(:estadoRegistro) ";
                $query->setParameter('estadoRegistro', 'Eliminado');
            }
            else
            {
                if($estado)
                {
                    $where   .= "AND LOWER(ca.estado) like LOWER(:estado) ";
                    $query->setParameter('estado', $estado);
                }
            }
        }

        if(isset($parametros["idEmpresa"]))
        {
            $where           .= " AND og.empresaId = '" . $parametros["idEmpresa"] . "' ";
        }

        if($parametros)
        {
            $sql = "SELECT DISTINCT ca
                    FROM 
                        schemaBundle:AdmiCanton ca, 
                        schemaBundle:AdmiProvincia pr, 
                        schemaBundle:AdmiRegion re, 
                        schemaBundle:AdmiPais pa, 
                        schemaBundle:AdmiCantonJurisdiccion caju,
                        schemaBundle:AdmiJurisdiccion ju,
                        schemaBundle:InfoOficinaGrupo og
                    WHERE 
                        pa.id = re.paisId
                        AND re.id = pr.regionId 
                        AND pr.id = ca.provinciaId 
                        AND caju.cantonId = ca.id
                        AND ju.id = caju.jurisdiccionId 
                        AND og.id = ju.oficinaId
                        $where 
                    ORDER BY ca.nombreCanton";
        }
        else
        {
            $sql = "SELECT ca
                    FROM 
                        schemaBundle:AdmiCanton ca
                    WHERE ca.estado = :estado
                        $where
                    ORDER BY ca.nombreCanton";
            if($estado)
            {
                $query->setParameter('estado', $estado);
            }
        }

        $query->setDQL($sql);

        if($start != '' && !$boolBusqueda && $limit != '')
        {
            $datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
        }
        else if($start != '' && !$boolBusqueda && $limit == '')
        {
            $datos = $query->setFirstResult($start)->getResult();
        }
        else if($start != '' && $boolBusqueda && $limit != '')
        {
            $datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
        }
        else if(($start == '' || $boolBusqueda) && $limit != '')
        {
            $datos = $query->setMaxResults($limit)->getResult();
        }
        else
        {
            $datos = $query->getResult();
        }

        return $datos;
    }

    public function generarJsonCantonesPorJurisdiccion($idJurisdiccion, $estado, $start, $limit){
        $arr_encontrados = array();
        
        $registrosTotal = $this->getCantonesPorJurisdiccion($idJurisdiccion, $estado, '', '');
        $registros = $this->getCantonesPorJurisdiccion($idJurisdiccion, $estado, $start, $limit);
        $em = $this->_em;
        if ($registros) {
            $num = count($registrosTotal);            
            foreach ($registros as $data)
            {
                
                $entity = $em->getRepository('schemaBundle:AdmiCanton')->find($data->getCantonId());
                        
                $nombreCanton = $entity->getNombreCanton();
                
                $arr_encontrados[]=array('id_canton' =>$data->getCantonId(),
                                         'nombre_canton' =>trim($nombreCanton),
                                         'estado' =>(strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-edit'),
                                         'action3' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-delete'));
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_canton' => 0 , 'nombre_canton' => 'Ninguno', 'es_capital'=>'NO', 'es_cabecera'=>'NO', 'nombre_provincia' => 'Ninguno', 'canton_id' => 0 , 'canton_nombre' => 'Ninguno', 'estado' => 'Ninguno'));
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
    
    public function getCantonesPorJurisdiccion($idJurisdiccion,$estado,$start,$limit){
        $qb = $this->_em->createQueryBuilder();
            $qb->select('acj')
               ->from('schemaBundle:AdmiCantonJurisdiccion','acj');
            
        $boolBusqueda = false;
        if($idJurisdiccion!=0){
            $boolBusqueda = true;
            $qb ->where( 'acj.jurisdiccionId = ?1');
            $qb->setParameter(1, $idJurisdiccion);
        }
        if($estado!="Todos"){
            $boolBusqueda = true;
            $qb ->andWhere('LOWER(acj.estado) = LOWER(?2)');
            $qb->setParameter(2, $estado);
        }
        if($start!='' && !$boolBusqueda)
            $qb->setFirstResult($start);   
        if($limit!='')
            $qb->setMaxResults($limit);
        
        
        $query = $qb->getQuery();
        
        return $query->getResult();
    }
    
    /**
     * getCantonesPorNombre
     *
     * Metodo encargado de obtener los cantones por nombre
     * 
     * @param $arrayParametros[
     *     strNombre => nombre del canton
     *     intPaisId => pais del canton
     * ]
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 13-02-2015
     * @since 1.0
     * costoQuery: 10
     * 
     * @return object
     *
     * 
     * Se modifica función para que se pueda obtener los cantones por Pais
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 04-07-2017
     *
     * Se agrega la clausula like para la busqueda de cantones
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 09-02-2021
     */
    public function getCantonesPorNombre($arrayParametros)
    {
        $strCriterios = '';
        $query        = $this->_em->createQuery();
        if (isset($arrayParametros['strNombre']) && $arrayParametros['strNombre']!="")
        {
            $strCriterios.=" AND UPPER(a.nombreCanton) like UPPER(:strNombre) ";
            $query->setParameter('strNombre',$arrayParametros['strNombre']);
        }

        if (isset($arrayParametros['intIdPais']) && $arrayParametros['intIdPais']>0)
        {
            $strCriterios.=" AND r.paisId = :intPaisId ";
            $query->setParameter('intPaisId',$arrayParametros['intIdPais']);
        }

        $strQuery="
        SELECT 
            a
        FROM
            schemaBundle:AdmiCanton a
            JOIN schemaBundle:AdmiProvincia p WITH a.provinciaId=p.id
            JOIN schemaBundle:AdmiRegion r WITH r.id=p.regionId
        WHERE 
            a.estado='Activo' ";
        $strQuery = $strQuery.$strCriterios;
        $query->setDQL($strQuery);
        $datos = $query->getResult();
        return $datos;
    }


    public function getCantonesByElementoId($arrayElementos){ 
		if ($arrayElementos && count($arrayElementos)>0){       
			$elementos_separado_por_comas = implode(",", $arrayElementos);
				
			$sql = "SELECT 
						DISTINCT ac.id, ac.nombreCanton 
					FROM 
						schemaBundle:InfoElemento ie, schemaBundle:InfoEmpresaElementoUbica ieeu, schemaBundle:InfoUbicacion iu,
						schemaBundle:AdmiParroquia ap, schemaBundle:AdmiCanton ac 
					WHERE 
						ie.id = ieeu.elementoId 
						AND iu.id = ieeu.ubicacionId 
						AND ap.id = iu.parroquiaId 
						AND ac.id = ap.cantonId 
						AND ie.id in ($elementos_separado_por_comas) 
					";

			$query = $this->_em->createQuery($sql);
			$datos = $query->getResult();
			return $datos;  
		}      
		else
		{
			return false;
		}
    }
	
    public function getCantonesByPuntoClienteId($arrayPuntos){
		if ($arrayPuntos && count($arrayPuntos)>0){       
			$puntos_separado_por_comas = implode(",", $arrayPuntos);
					
			$sql = "SELECT 
						DISTINCT ac.id, ac.nombreCanton 
					FROM 
						schemaBundle:InfoPunto ip, schemaBundle:AdmiCanton ac, schemaBundle:AdmiParroquia ap, schemaBundle:AdmiSector asec  
					WHERE 
						asec.id = ip.sectorId 
						AND ap.id = asec.parroquiaId 
						AND ac.id = ap.cantonId 
						AND ip.id in ($puntos_separado_por_comas) 
					";

			$query = $this->_em->createQuery($sql);			
			$datos = $query->getResult();
			return $datos;    
		}      
		else
		{
			return false;
		}     
    }
    
    /**
     * getCantonesPorProvincia
     *
     * Metodo encargado de obtener los cantones de acuerdo a la provincia enviada como parametro
     * 
     * @param $idProvincia
     * @param $estado         
     *
     * @return json
     *
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 13-02-2015
     */       
    public function getCantonesPorProvincia($idProvincia,$estado)
    {
        $query = $this->_em->createQuery();
        
        $sql = "SELECT a
                FROM
                schemaBundle:AdmiCanton a
                WHERE
                a.provinciaId = :provincia and
                a.estado=:estado";
        
        $query->setParameter("provincia", $idProvincia);
        $query->setParameter("estado", $estado);
        
        $query->setDQL($sql);
        
        $datos = $query->getResult();

        if($datos)
        {

            $num = count($datos);

            foreach($datos as $data)
            {

                $arr_encontrados[] = array('id_canton' => $data->getId(),
                    'nombre_canton' => trim($data->getNombreCanton()));
            }

            if($num == 0)
            {                
                $resultado = json_encode(array('total' => 1,'encontrados' => array('id_canton' => 0, 'nombre_canton' => 'Ninguno')));
                return $resultado;
            }
            else
            {                
                $resultado = '{"total":"' . $num . '","encontrados":' . json_encode($arr_encontrados) . '}';
                return $resultado;
            }
        }
        else
        {
            $resultado = '{"total":"0","encontrados":[]}';
            return $resultado;
        }
        return $datos;
    }

}

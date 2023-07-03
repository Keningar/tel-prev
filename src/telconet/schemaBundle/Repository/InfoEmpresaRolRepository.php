<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class InfoEmpresaRolRepository extends EntityRepository
{
    /**
     * Documentación para el método 'findPorNombreTipoRolPorEmpresa'.
     *
     * Busca el rol de 'CLIENTE' para asociarlo a la persona con la empresa
     *
     * @param String $strNombreTipoRol  Nombre del rol 'Cliente' que se desea buscar
     * @param String $strCodEmpresa     Código de la empresa del rol que se desea buscar
     *
     * @return object $objInfoEmpresaRol
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 27-01-2017
     * @since 1.0
     */
	public function findPorNombreTipoRolPorEmpresa($strNombreTipoRol, $strCodEmpresa)
    {
        $objInfoEmpresaRol = null;
        
        try
        {
            if( !empty($strNombreTipoRol) && !empty($strCodEmpresa) )
            {
                $strSql = "SELECT ier
                           FROM schemaBundle:InfoEmpresaRol ier, 
                                schemaBundle:AdmiRol ar, 
                                schemaBundle:AdmiTipoRol atr
                           WHERE ier.rolId            = ar.id 
                           AND ar.tipoRolId           = atr.id
                           AND ier.empresaCod         = :strCodEmpresa 
                           AND atr.descripcionTipoRol = :strNombreTipoRol
                           AND ar.descripcionRol      = :strNombreTipoRol ";

                $objQuery = $this->_em->createQuery($strSql)->setMaxResults(1);
                $objQuery->setParameter('strCodEmpresa',    $strCodEmpresa);
                $objQuery->setParameter('strNombreTipoRol', $strNombreTipoRol);

                $objInfoEmpresaRol = $objQuery->getOneOrNullResult();
            }
            else
            {
                throw new \Exception('No se han enviado los parámetros correspondientes para realizar la consulta correspondiente. (NombreTipoRol:'.
                                     ' '.$strNombreTipoRol.'), (CodigoEmpresa: '.$strCodEmpresa.')');
            }
        }
        catch(\Exception $e)
        {
            throw ($e);
        }
        
        return $objInfoEmpresaRol;
	}
    
    
	public function findPorNombreRolPorEmpresa($nombreRol,$idEmpresa){	
		$query = $this->_em->createQuery("SELECT c
		FROM 
                schemaBundle:InfoEmpresaRol c, schemaBundle:AdmiRol d, schemaBundle:AdmiTipoRol e
		WHERE 
                c.rolId=d.id AND
                d.tipoRolId=e.id AND  
                c.empresaCod='$idEmpresa' AND d.descripcionRol='$nombreRol'")->setMaxResults(1);
                //echo $query->getSQL(); die;
		$datos = $query->getOneOrNullResult();
             
		return $datos;
	}        

    public function generarJson($em_general, $nombre,$estado,$start,$limit)
    {
        $arr_encontrados = array();
        
        $registrosTotal = $this->getRegistros($nombre, $estado, '', '');
        $registros = $this->getRegistros($nombre, $estado, $start, $limit);
 
        if ($registros) {
            $num = count($registrosTotal);            
            foreach ($registros as $data)
            {   
                $nombreRol = "";
                if($data->getRolId())
                {    
                    $objRol = $em_general->getRepository('schemaBundle:AdmiRol')->findOneById($data->getRolId());
                    $nombreRol = $objRol ? $objRol->getDescripcionRol() : "";
                }   
                        
                $arr_encontrados[]=array('id_empresa_rol' =>$data->getId(),
                                         'nombre_empresa' =>trim($data->getEmpresaCod()->getNombreEmpresa()),
                                         'descripcion_rol' =>trim($nombreRol),
                                         'estado' =>(strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-edit'),
                                         'action3' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-delete'));
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_empresa_rol' => 0 , 'nombre_empresa' => 'Ninguno', 'descripcion_rol' => 'Ninguno',  'empresa_id' => 0 , 'empresa_nombre' => 'Ninguno', 'estado' => 'Ninguno'));
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
               ->from('schemaBundle:InfoEmpresaRol','sim');
            
        $boolBusqueda = false;
        /*if($nombre!=""){
            $boolBusqueda = true;
            $qb ->where( 'sim.nombreEmpresa like ?1');
            $qb->setParameter(1, '%'.$nombre.'%');
        }*/
        
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
    
	public function findPorIdRolPorEmpresa($idRol,$idEmpresa){	
		$query = $this->_em->createQuery("SELECT c
		FROM 
                schemaBundle:InfoEmpresaRol c, schemaBundle:AdmiRol d, schemaBundle:AdmiTipoRol e
		WHERE 
                c.rolId=d.id AND
                d.tipoRolId=e.id AND  
                c.empresaCod='$idEmpresa' AND d.id='$idRol'")->setMaxResults(1);
                //echo $query->getSQL(); die;
		$datos = $query->getOneOrNullResult();
             
		return $datos;
	} 
        
    public function findRolesPorEmpresa($idEmpresa)
    {
        $query = $this->_em->createQuery("SELECT c
		FROM 
                schemaBundle:InfoEmpresaRol c, schemaBundle:AdmiRol d, schemaBundle:AdmiTipoRol e
                WHERE 
                c.rolId=d.id AND
                d.tipoRolId=e.id AND
                e.descripcionTipoRol='Empleado' AND
                c.empresaCod='$idEmpresa'");
                //echo $query->getSQL(); die;
		$datos = $query->getResult();
             
		return $datos;
    }
    
     /**
     * Documentación para el método 'findPorNombreRolPorNombreTipoRolPorEmpresa'.
     * 
     * Método que obtiene el rol de empresa.
     * 
     * @param String $strNombreRol      Nombre del Rol.
     * @param String $strNombreTipoRol  Nombre del tipo de rol.
     * @param int    $intIdEmpresa      Id de la empresa en sesión.
     * 
     * @return InfoEmpresaRol Rol de la empresa.
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
     * @version 1.0 20-09-2015
     */
    public function findPorNombreRolPorNombreTipoRolPorEmpresa($strNombreRol, $strNombreTipoRol, $intIdEmpresa)
    {
        $objQuery = $this->_em->createQuery("SELECT er
                                             FROM schemaBundle:InfoEmpresaRol er, schemaBundle:AdmiRol r, schemaBundle:AdmiTipoRol tr
                                             WHERE er.rolId            = r.id 
                                             AND r.tipoRolId           = tr.id 
                                             AND er.empresaCod         = :idEmpresa 
                                             AND tr.descripcionTipoRol = :nombreTipoRol 
                                             AND r.descripcionRol      = :nombreRol");
        $objQuery->setParameter('idEmpresa', $intIdEmpresa);
        $objQuery->setParameter('nombreTipoRol', $strNombreTipoRol);
        $objQuery->setParameter('nombreRol', $strNombreRol);
        return $objQuery->getOneOrNullResult();
	}
}

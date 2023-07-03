<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class InfoCuadrillaRepository extends EntityRepository
{
    
     /**
     * getDatosLiderCuadrilla
     * 
     * Esta funcion me retorna la informacion del lider de la cuadrilla
     * 
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 19-10-2015 
     * 
     * @param integer  $idPersona
     * 
     * @return array $strDatos
     * 
     */
    public function getDatosLiderCuadrilla($idPersona) 
    {

        $strQuery        = $this->_em->createQuery();        

        $strSelect = " SELECT  
                            AdmiDepartamento.id as idDepartamento,
                            AdmiDepartamento.nombreDepartamento as nombreDeDepartamento,
                            AdmiArea.id as idArea,AdmiArea.nombreArea as nombreDeArea,
                            InfoEmpresaGrupo.nombreEmpresa as nombreDeEmpresa,InfoOficinaGrupo.nombreOficina as nombreDeOficina

                        FROM  
                            schemaBundle:InfoPersonaEmpresaRol InfoPersonaEmpresaRol,schemaBundle:AdmiDepartamento AdmiDepartamento,
                            schemaBundle:AdmiArea AdmiArea,schemaBundle:InfoEmpresaGrupo InfoEmpresaGrupo,
                            schemaBundle:InfoOficinaGrupo InfoOficinaGrupo

                        WHERE 
                            InfoPersonaEmpresaRol.departamentoId = AdmiDepartamento.id
                        AND AdmiDepartamento.areaId = AdmiArea.id
                        AND AdmiArea.empresaCod = InfoEmpresaGrupo.id
                        AND InfoPersonaEmpresaRol.oficinaId = InfoOficinaGrupo.id                        
                        AND InfoPersonaEmpresaRol.personaId = :varPersona
                        AND InfoPersonaEmpresaRol.estado = :varEstado
                        AND AdmiArea.empresaCod = :varEmpresaCod ";
    
        $strQuery->setParameter("varPersona", $idPersona);
        $strQuery->setParameter("varEstado", 'Activo');        
        $strQuery->setParameter("varEmpresaCod", 10);     
                
        $strQuery-> setDQL($strSelect);  

        $strDatos = $strQuery->getResult();        

        return $strDatos[0];
    }    

     /**
     * getLiderCuadrilla
     * 
     * Esta funcion identifica si una persona es un lider de cuadrilla
     * 
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 19-10-2015 
     * 
     * @param integer  $idPersona
     * 
     * @return array $strDatos
     * 
     */
    public function getLiderCuadrilla($idPersona) 
    {
        $arrayRegistros  = array();
        $strQuery        = $this->_em->createQuery();        
        $strCampos  = " SELECT
                           (SELECT a.id FROM schemaBundle:InfoPersonaEmpresaRol a WHERE a.id = infoPersonaEmpresaRolCarac.personaEmpresaRolId) as
                           personaEmpresaRolId ";
                           
        $strFrom    = " FROM 
                            schemaBundle:InfoPersonaEmpresaRolCarac infoPersonaEmpresaRolCarac
                            
                        WHERE 
                            infoPersonaEmpresaRolCarac.caracteristicaId IN
                            (SELECT 
                                admiCaracteristica.id
                            FROM 
                                schemaBundle:AdmiCaracteristica admiCaracteristica
                            WHERE 
                                admiCaracteristica.descripcionCaracteristica = :varDescripcion)
                                
                        AND infoPersonaEmpresaRolCarac.valor                   = :varValor
                        AND infoPersonaEmpresaRolCarac.personaEmpresaRolId IN
                            (SELECT 
                                infoPersonaEmpresaRol.id
                             FROM 
                                schemaBundle:InfoPersonaEmpresaRol infoPersonaEmpresaRol
                             WHERE 
                                infoPersonaEmpresaRol.personaId = :varPersona
                             AND infoPersonaEmpresaRol.estado NOT IN (:varEstado)) 
                        AND infoPersonaEmpresaRolCarac.estado = :varEstado2 "; 
  
        $strQuery->setParameter("varDescripcion", 'CARGO');
        $strQuery->setParameter("varValor", 'Lider');
        $strQuery->setParameter("varPersona", $idPersona);
        $strQuery->setParameter("varEstado", array('Inactivo','Cancelado','Anulado','Eliminado'));  
        $strQuery->setParameter("varEstado2",'Activo');                  
                
        $strSelect =  $strCampos . $strFrom   ;

        $strQuery-> setDQL($strSelect);  

        $strDatos = $strQuery->getResult();        

        return $strDatos;
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
                                 'encontrados' => array('id_cuadrilla' => 0 , 'nombre_cuadrilla' => 'Ninguno', 
                                                        'cuadrilla_id' => 0 , 'cuadrilla_nombre' => 'Ninguno', 'estado' => 'Ninguno'));
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
               ->from('schemaBundle:InfoCuadrilla','sim');
            
        $boolBusqueda = false;
        if($nombre!=""){
            $boolBusqueda = true;
            $qb ->where( 'LOWER(sim.nombreCuadrilla) like LOWER(?1)');
            $qb->setParameter(1, '%'.$nombre.'%');
        }
        if($estado!="Todos"){
            $boolBusqueda = true;
            $qb ->andWhere('LOWER(sim.estado) = LOWER(?2)');
            $qb->setParameter(2, $estado);
        }
        if($start!='' && !$boolBusqueda)
            $qb->setFirstResult($start);   
        if($limit!='')
            $qb->setMaxResults($limit);
        
        
        $query = $qb->getQuery();
        
        return $query->getResult();
    }
    
    public function findCuadrillas($nombre="")
    {    
        $whereAdd = "";
        if($nombre!="" && $nombre)
        {
            $whereAdd = "AND LOWER(c.nombreCuadrilla) like '%".strtolower($nombre)."%' ";
        }

        $sql = "SELECT c 
                FROM schemaBundle:InfoCuadrilla c 
                WHERE c.estado not like 'Eliminado' 
                $whereAdd ";

        $query = $this->_em->createQuery($sql);
        $datos = $query->getResult();
        return $datos;
    }

}

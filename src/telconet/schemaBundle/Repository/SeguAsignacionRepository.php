<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class SeguAsignacionRepository extends EntityRepository
{

    public function loadAsignacion($arrayPerfiles)
    {   
	$whereVar = "";
	if($arrayPerfiles && count($arrayPerfiles)>0)
	{
	    $string_perfiles_implode = implode("', '", $arrayPerfiles);
	    $string_perfiles = "'".$string_perfiles_implode."'";
	    $whereVar .= "WHERE sa.perfilId IN ($string_perfiles) ";

            $query =  "SELECT sa ".
                    "FROM schemaBundle:SeguAsignacion sa ".
                    "$whereVar";

            return $this->_em->createQuery($query)->getResult();
	}
        return false;
    }

    public function loadAsignacion_RelacionSistema($arrayPerfiles, $idItemPadre)
    {   
	$whereVar = "";
	if($arrayPerfiles && count($arrayPerfiles)>0)
	{
            if($idItemPadre && $idItemPadre!="")
            {
                $wherePadre = "AND im.itemMenuId = '$idItemPadre' ";
            }
            else
            {
                $wherePadre = "AND im.itemMenuId is null ";
            }
            
	    $string_perfiles_implode = implode("', '", $arrayPerfiles);
	    $string_perfiles = "'".$string_perfiles_implode."'";

            $query =  "SELECT a ".
                      "FROM schemaBundle:SeguAsignacion a ".
                      "JOIN a.relacionSistemaId rs ".
                      "JOIN rs.itemMenuId im ".
                      "WHERE a.perfilId IN ($string_perfiles) $wherePadre ".
                      "AND LOWER(im.estado) != LOWER('Eliminado') ".
                      "ORDER BY im.posicion ";
            
//            print($this->_em->createQuery($query)->getSQL());
//            die();
            
            return $this->_em->createQuery($query)->getResult();
	}
        return false;
    }
    
        
    public function borrarDistintosEleccion($arreglo_relaciones, $id_perfil){
        
    	$array_accion = array();
        
        $qb = $this->_em->createQueryBuilder();
        
        $qb->select('srs')
           ->from('schemaBundle:SeguAsignacion','srs')
           ->where( 'srs.perfilId = ?1')
           ->setParameter(1, $id_perfil)
           ->andWhere($qb->expr()->NotIn('srs.relacionSistemaId',$arreglo_relaciones));
        
        $query = $qb->getQuery();
        
        $distintos = $query->getResult();
        
        
        foreach($distintos as $segu_relacion):
            $this->_em->remove($segu_relacion);
            $this->_em->flush();
        endforeach;
    }
    
    /* getRolesXEmpleado
     * 
     * @author John Vera R. <javera@telconet.ec>
     * @version 1.2 06-10-2017 se parametrizaron los valores
     */    

    public function getRolesXEmpleado($id)
    {
        $arr_encontrados = array();
		
        $strDql =  "SELECT sist_modulo.id as modulo_id, sist_accion.id as accion_id ".
				  "FROM schemaBundle:SeguPerfilPersona segu_perfil_persona, schemaBundle:SeguAsignacion segu_asignacion, ".
				  "schemaBundle:SeguRelacionSistema segu_relacion_sistema, schemaBundle:SistModulo sist_modulo, schemaBundle:SistAccion sist_accion ".
				  "WHERE 
				  segu_perfil_persona.perfilId = segu_asignacion.perfilId AND 
				  segu_asignacion.relacionSistemaId = segu_relacion_sistema.id  AND 
				  sist_modulo.id = segu_relacion_sistema.moduloId AND 
				  sist_accion.id = segu_relacion_sistema.accionId AND 
                  segu_perfil_persona.personaId = :idPersona AND
                  sist_modulo.estado != :estadoEliminado AND
                  sist_accion.estado != :estadoEliminado";
         
        $objQuery = $this->_em->createQuery($strDql);
             
        $objQuery->setParameter('idPersona', $id);
        $objQuery->setParameter('estadoEliminado', 'Eliminado');
              
        return $objQuery->getResult();

    }
}

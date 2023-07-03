<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class SistPerfilRepository extends EntityRepository
{

    public function getJsonPerfiles($nombre,$estado,$start,$limit)
    {
        $arr_encontrados = array();
        
        $perfilesTotal = $this->getPerfiles($nombre, $estado, '', '');
        
        
        $perfiles = $this->getPerfiles($nombre, $estado, $start, $limit);
 
        if ($perfiles) {
            
            $num = count($perfilesTotal);
            
            foreach ($perfiles as $perfil)
            {
                $arr_encontrados[]=array('id_perfil' =>$perfil->getId(),
                                         'nombre_perfil' =>trim($perfil->getNombrePerfil()),
                                         'estado' =>(trim($perfil->getEstado())=='Eliminado' ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (trim($perfil->getEstado())=='Eliminado' ? 'icon-invisible':'button-grid-edit'),
                                         'action3' => (trim($perfil->getEstado())=='Eliminado' ? 'icon-invisible':'button-grid-delete'));
            }

            if($num == 0)
            {
               $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_perfil' => 0 , 'nombre_perfil' => 'Ninguno','estado' => 'Ninguno','action1' => 'Ninguno','action2' => 'Ninguno','action3' => 'Ninguno'));
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
    public function getPerfiles($nombre,$estado,$start,$limit){
        $qb = $this->_em->createQueryBuilder();
            $qb->select('p')
               ->from('schemaBundle:SistPerfil','p');
               
            
        if($nombre!=""){
            $qb ->where( 'LOWER(p.nombrePerfil) like LOWER(?1)');
            $qb->setParameter(1, '%'.$nombre.'%');
        }
        
        if($estado!="Todos"){
            $boolBusqueda = true;
            if($estado=="Activo"){
                $qb ->andWhere("LOWER(p.estado) not like LOWER('Eliminado')");
            }
            else{
                $qb ->andWhere('LOWER(p.estado) = LOWER(?2)');
                $qb->setParameter(2, $estado);
            }
        }
        if($start!='')
            $qb->setFirstResult($start);   
        if($limit!='')
            $qb->setMaxResults($limit);
        
        $qb->orderBy('p.id');
        $query = $qb->getQuery();
        
        return $query->getResult();
    }
    public function getAsignacionesPerfil($id_perfil)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('segu_asignacion')
           ->from('schemaBundle:SeguAsignacion','segu_asignacion')
           ->where('segu_asignacion.perfilId = ?1');
        $qb->setParameter(1, $id_perfil);
        
        $query = $qb->getQuery();
        
        return $query->getResult();
    }
    
    public function getJsonAsignacionesPerfil($id_perfil)
    {
        $array_asignaciones = $this->getAsignacionesPerfil($id_perfil);

        $obj_asignaciones['total'] = count($array_asignaciones);
        $obj_asignaciones['asignaciones'] = array();
        
        foreach($array_asignaciones as $tmp_asignacion):
            //print_r($tmp_asignacion->getperfil()->getperfil()->getNombreperfil());die;
            $obj_asignaciones['asignaciones'][] = array("perfil_id"=>$tmp_asignacion->getPerfilId()->getId(),
                                                           "accion_id"=>$tmp_asignacion->getRelacionSistemaId()->getAccionId()->getId(),
                                                           "accion_nombre"=>$tmp_asignacion->getRelacionSistemaId()->getAccionId()->getId()?$tmp_asignacion->getRelacionSistemaId()->getAccionId()->getNombreAccion():'',
                                                           "modulo_id"=>$tmp_asignacion->getRelacionSistemaId()->getModuloId()->getId(),
                                                           "modulo_nombre"=>$tmp_asignacion->getRelacionSistemaId()->getModuloId()?$tmp_asignacion->getRelacionSistemaId()->getModuloId()->getNombreModulo():'');
            
        endforeach;

        return json_encode($obj_asignaciones);
    }

    /**
     * getPerfilesReconexionAbusador
     * 
     * Método que nos indica si el usuario ingresado posee o no perfil con permisos para reconectar 
     * cliente Posible Abusador
     * 
     * @author Javier Hidalgo <jihidalgo@telconet.ec>
     * @version 1.0 09-12-2021
     * @since 1.0
     * 
     * @param string $strLogin Login o user del empleado
     * @return int $intPerfilReconecta '1' si es perfil parametrizado '0' si no lo es
     */
    public function getPerfilesReconexionAbusador($strLogin)
    {
        $arrayRespuesta = array();
        $intPerfilReconecta = 0;
        $objQuery = $this->_em->createQuery(null);
        $strQuery = "SELECT SP.nombrePerfil FROM
                    schemaBundle:SistPerfil         SP,
                    schemaBundle:SeguPerfilPersona SPP,
                    schemaBundle:InfoPersona        IP
                WHERE
                    SP.id = SPP.perfilId
                    AND SPP.personaId = IP.id 
                    AND SP.id IN (
                        SELECT
                            APD.valor1
                        FROM
                            schemaBundle:AdmiParametroCab APC, 
                            schemaBundle:AdmiParametroDet APD
                        WHERE
                            APC.id = APD.parametroId
                            AND APD.estado = 'Activo'
                            AND APC.nombreParametro = 'PERFILES_CONECTAR_POSIBLES_ABUSADORES'
                    )
                    AND IP.login = :usrLogin";

        $objQuery->setParameter("usrLogin", $strLogin);
        $objQuery->setDQL($strQuery);
        $arrayRespuesta = $objQuery->getResult();
        
        if(isset($arrayRespuesta) && !empty($arrayRespuesta))
        {
            $intPerfilReconecta = 1;
            return $intPerfilReconecta;
        }

        return $intPerfilReconecta;
    }

}

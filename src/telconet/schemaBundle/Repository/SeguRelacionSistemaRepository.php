<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class SeguRelacionSistemaRepository extends EntityRepository
{
    public function getRelaciones($modulo){
        
        
        $qb = $this->_em->createQueryBuilder();
            $qb->select('srs')->from('schemaBundle:SeguRelacionSistema','srs');
            $qb ->where( 'srs.moduloId = ?1');
            $qb->setParameter(1, $modulo);
        
        $query = $qb->getQuery();
        
        
        return $query->getResult();
    }
    public function borrarDistintosEleccion($arreglo_relaciones, $admi_modulo){
        
    	$array_accion = array();
        
        $qb = $this->_em->createQueryBuilder();
        
        $qb->select('srs')
           ->from('schemaBundle:SeguRelacionSistema','srs')
           ->where( 'srs.moduloId = ?1')
           ->setParameter(1, $admi_modulo)
           ->andWhere($qb->expr()->NotIn('srs.id',$arreglo_relaciones));
        
        $query = $qb->getQuery();
        
        $distintos = $query->getResult();
        
        
        foreach($distintos as $segu_relacion):
            $this->_em->remove($segu_relacion);
            $this->_em->flush();
        endforeach;
    }
    
    

    public function loadModulos()
    {   
        $query =    "SELECT DISTINCT m.id, m.nombreModulo ".
                    "FROM schemaBundle:SeguRelacionSistema rs ".
                    "JOIN rs.moduloId m ".
                    "ORDER BY m.nombreModulo ";
        
        return $this->_em->createQuery($query)->getResult();
    }
    
    public function loadItemsMenu($modulo_id)
    {   
        $query =    "SELECT DISTINCT im.id, im.nombreItemMenu ".
                    "FROM schemaBundle:SeguRelacionSistema rs ".
                    "JOIN rs.itemMenuId im ".
                    "WHERE rs.moduloId = '$modulo_id' ".
                    "ORDER BY im.nombreItemMenu ";
        
        return $this->_em->createQuery($query)->getResult();
    }
    
    public function loadAcciones($modulo_id, $itemmenu_id)
    {   
        $query =    "SELECT DISTINCT a.id, a.nombreAccion ".
                    "FROM schemaBundle:SeguRelacionSistema rs ".
                    "JOIN rs.accionId a ".
                    "WHERE rs.moduloId = '$modulo_id'  ".
                    "ORDER BY a.nombreAccion ";
		
		//and rs.itemMenuId = '$itemmenu_id'
        
        return $this->_em->createQuery($query)->getResult();
    }
    
    
    public function searchOneRelacionSistema($modulo_id, $itemmenu_id, $accion_id)
    {   
        $query =    "SELECT DISTINCT rs.id ".
                    "FROM schemaBundle:SeguRelacionSistema rs ".
                    "WHERE rs.moduloId = '$modulo_id' and rs.accionId = '$accion_id' ";  //and rs.itemMenuId = '$itemmenu_id'
        
        return $this->_em->createQuery($query)->getOneOrNullResult();
    }

    public function searchAccionByModulo($modulo_id, $nombreAccion)
    {   
        $query =    "SELECT DISTINCT a.id ".
                    "FROM schemaBundle:SeguRelacionSistema rs, schemaBundle:SistAccion a ".
                    "WHERE rs.moduloId = '$modulo_id' and rs.accionId = a.id and a.nombreAccion = '$nombreAccion' ";
        
        return $this->_em->createQuery($query)->getOneOrNullResult();
    }
    
    /**
     * Documentación para el método 'searchItemMenuByModulo'.
     *
     * Método utilizado consultar el ItemMenu
     * 
     * @param   $modulo_id
     * @param   $accion_id
     *
     * @return SistItemMenu
     *
     * @author Desarrollo Inicial
     * @version 1.0 
     * 
     * @author Modifica: Duval Medina C. <dmedina@telconet.ec>
     * @version 1.1 2016-05-15 Remover schemaBundle:SistAccion de la consulta por no afectar a la consulta
     *                         parametrizar variables e identar
    */
    public function searchItemMenuByModulo($modulo_id, $accion_id)
    {   
        $sql = "SELECT sim
                FROM schemaBundle:SistItemMenu sim, schemaBundle:SeguRelacionSistema rs
                WHERE rs.moduloId = :moduloId
                      AND rs.accionId = :accionId
                      AND rs.itemMenuId = sim.id";
        $query = $this->_em->createQuery($sql);
        $query->setParameter("moduloId", $modulo_id);
        $query->setParameter("accionId", $accion_id);
        
        return $query->getOneOrNullResult();
    }
    
    public function getSistModuloSoporte()
    {   
        $query =    "SELECT b ".
                    "FROM schemaBundle:SistModulo a , schemaBundle:SeguRelacionSistema b ".
                    "WHERE a.nombreModulo = 'soporte' and a.id = b.moduloId ";
        
        return $this->_em->createQuery($query)->getResult();
    }
    
    
    /**
     * getRelacionSistemaByCriterios
     *
     * Método que retorna las relaciones con el sistema dependiendo de los criterios enviados por el usuario.                                    
     *      
     * @param array $arrayParametros  [ 'intStart', 'intLimit', 'criterios' => ('nombreModulo', 'nombreAccion', 'estadosModulo', 
     *                                                                          'estadosAcciones') ]
     * 
     * @return array $arrayResultados [ 'registros', 'total' ]
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 
     * @since 18-03-2016
     */
    public function getRelacionSistemaByCriterios($arrayParametros)
    {
        $arrayResultados  = array();
        
        $query      = $this->_em->createQuery();
        $queryCount = $this->_em->createQuery();
        
        $strSelect      = "SELECT srs ";
        $strSelectCount = "SELECT COUNT ( srs.id ) ";
        $strFrom        = "FROM schemaBundle:SeguRelacionSistema srs "; 
        $strJoin        = "JOIN srs.moduloId sm
                           JOIN srs.accionId sa "; 
        $strWhere       = "WHERE srs.id IS NOT NULL ";
        $strOrderBy     = "ORDER BY srs.feCreacion DESC ";
        
        
        $arrayCriterios = $arrayParametros['criterios'];
        
        if( !empty($arrayCriterios) )
        {
            if( !empty($arrayCriterios['nombreModulo']) )
            {
                $strWhere .= 'AND sm.nombreModulo LIKE :nombreModulo ';

                $query->setParameter('nombreModulo',      '%'.trim($arrayCriterios['nombreModulo']).'%');
                $queryCount->setParameter('nombreModulo', '%'.trim($arrayCriterios['nombreModulo']).'%');
            }
            
            
            if( !empty($arrayCriterios['nombreAccion']) )
            {
                $strWhere .= 'AND sa.nombreAccion LIKE :nombreAccion ';

                $query->setParameter('nombreAccion',      '%'.trim($arrayCriterios['nombreAccion']).'%');
                $queryCount->setParameter('nombreAccion', '%'.trim($arrayCriterios['nombreAccion']).'%');
            }
            
            
            if( !empty($arrayCriterios['estadosModulo']) )
            {
                $strWhere .= 'AND sm.estado IN (:estadosModulo) ';

                $query->setParameter('estadosModulo',      array_values($arrayCriterios['estadosModulo']));
                $queryCount->setParameter('estadosModulo', array_values($arrayCriterios['estadosModulo']));
            }
            
            
            if( !empty($arrayCriterios['estadosAcciones']) )
            {
                $strWhere .= 'AND sa.estado IN (:estadosAcciones) ';

                $query->setParameter('estadosAcciones',      array_values($arrayCriterios['estadosAcciones']));
                $queryCount->setParameter('estadosAcciones', array_values($arrayCriterios['estadosAcciones']));
            }
        }
        
        
        $strSql      = $strSelect.$strFrom.$strJoin.$strWhere.$strOrderBy;
        $strSqlCount = $strSelectCount.$strFrom.$strJoin.$strWhere;
        
        $query->setDQL($strSql);
        $queryCount->setDQL($strSqlCount);
        
        if( !empty($arrayParametros['intStart']) )
        {
            $query->setFirstResult($arrayParametros['intStart']);
        }
        
        if( !empty($arrayParametros['intLimit']) )
        {
            $query->setMaxResults($arrayParametros['intLimit']);
        }
        
            
        $arrayResultados['registros'] = $query->getResult();
        $arrayResultados['total']     = $queryCount->getSingleScalarResult();
        
        return $arrayResultados;
    }
    
    /**
     * Documentación para el método 'searchItemMenuByNombreModulo'.
     *
     * Método utilizado para consultar el ItemMenu usando el nombre del módulo
     * 
     * @param   $nombreModulo
     * @param   $accionId
     *
     * @return SistItemMenu
     *
     * @author Duval Medina C. <dmedina@telconet.ec>
     * @version 1.0 2016-06-23 
    */
    public function searchItemMenuByNombreModulo($nombreModulo, $accionId)
    {   
        $sql = "SELECT sim
                FROM schemaBundle:SistItemMenu sim,
                     schemaBundle:SeguRelacionSistema rs,
                     schemaBundle:SistModulo sm
                WHERE rs.moduloId         = sm.id
                      AND rs.accionId     = :accionId
                      AND rs.itemMenuId   = sim.id
                      AND sm.nombreModulo = :nombreModulo";
        $query = $this->_em->createQuery($sql);
        $query->setParameter("nombreModulo", $nombreModulo);
        $query->setParameter("accionId", $accionId);
        
        return $query->getOneOrNullResult();
    }
    
    /**
     * Documentación para el método 'getRelacionSistemaByModuloAndAccion'.
     *
     * Método utilizado para consultar el SeguRelacionSistema usando el nombre del módulo y el nombre de la Accion
     *      Retorna null En caso de ocurrir alguna excepción, ejm: encontrar mas de 1 registro
     * 
     * @param   $strNombreModulo
     * @param   $strNombreAccion
     *
     * @return SeguRelacionSistema
     *
     * @author Duval Medina C. <dmedina@telconet.ec>
     * @version 1.0 2016-10-05
    */
    public function getRelacionSistemaByModuloAndAccion($strNombreModulo, $strNombreAccion)
    {
        try
        {
            $sql = "SELECT srs
                    FROM schemaBundle:SeguRelacionSistema srs,
                         schemaBundle:SistModulo sm,
                         schemaBundle:SistAccion sa
                    WHERE srs.moduloId         = sm.id
                          AND srs.accionId     = sa.id
                          AND sa.nombreAccion = :nombreAccion
                          AND sm.nombreModulo = :nombreModulo";
            $query = $this->_em->createQuery($sql);
            $query->setParameter("nombreModulo", $strNombreModulo);
            $query->setParameter("nombreAccion", $strNombreAccion);

            return $query->getOneOrNullResult();
        }
        catch(\Exception $ex)
        {
            return null;
        }
    }

     /**
     * Costo: 6
     * getPerfilPorPersona
     *
     * Función que retorna si una persona tiene un perfil asociado
     *
     * @param array arrayParametros [ intIdPersonaRol => id de la persona rol
     *                                strNombrePerfil => nombre del perfil a buscar ]
     *
     * @return string $strTienePerfil retorna "S" si tiene el perfil, "N" si no tiene el perfil
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 30-05-2018
     */
    public function getPerfilPorPersona($arrayParametros)
    {
        $intCantidadPerfil   = "";
        $strTienePerfil      = "N";
        $objRsmb             = new ResultSetMappingBuilder($this->_em);
        $objQuery            = $this->_em->createNativeQuery(null,$objRsmb);

        $strSql = " SELECT COUNT(SPP.PERFIL_ID) as EXISTE FROM DB_SEGURIDAD.SEGU_PERFIL_PERSONA SPP
                        WHERE SPP.PERFIL_ID = (SELECT SPE.ID_PERFIL FROM DB_SEGURIDAD.SIST_PERFIL SPE
                        WHERE SPE.NOMBRE_PERFIL = :paramNombrePerfil ) AND SPP.PERSONA_ID =
                        (select IPER.PERSONA_ID from DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPER where IPER.ID_PERSONA_ROL = :paramIdPersonaRol) ";

        $objQuery->setParameter('paramNombrePerfil',$arrayParametros["strNombrePerfil"]);
        $objQuery->setParameter('paramIdPersonaRol',$arrayParametros["intIdPersonaRol"]);

        $objRsmb->addScalarResult('EXISTE', 'existe', 'integer');

        $objQuery->setSQL($strSql);

        $intCantidadPerfil = $objQuery->getSingleScalarResult();

        if($intCantidadPerfil > 0)
        {
            $strTienePerfil = "S";
        }

        return $strTienePerfil;
    }

      /**
     * Costo: 6
     * getPerfilPlanificaicon
     *
     * Función que retorna si una persona tiene un perfil asociado
     *
     * @param array arrayParametros [ intIdPersona => id de la persona
     *                                strNombrePerfil => nombre del perfil a buscar ]
     *
     * @return string $strTienePerfil retorna "S" si tiene el perfil, "N" si no tiene el perfil
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 15-06-2021
     */   public function getPerfilPlanificacion($intIdPersona, $strNombrePerfil)
    {
        $strTienePerfil      = "N";
        $objRsmb             = new ResultSetMappingBuilder($this->_em);
        $objQuery            = $this->_em->createNativeQuery(null,$objRsmb);

        $strSql = "SELECT 
            CASE WHEN COUNT(T1.ACCION_ID) > 0 THEN 'S' ELSE 'N' END  EXISTE 
        FROM
        (SELECT
            SRS.ACCION_ID,SRS.MODULO_ID 
            FROM DB_SEGURIDAD.sist_perfil IPER
            INNER JOIN DB_SEGURIDAD.segu_asignacion SAS ON IPER.ID_PERFIL= SAS.PERFIL_ID 
            INNER JOIN DB_SEGURIDAD.segu_relacion_sistema SRS ON SRS.ID_RELACION_SISTEMA = SAS.RELACION_SISTEMA_ID
            where nombre_perfil = :strnombre) T1
        INNER JOIN (SELECT 
            SRS.ACCION_ID,SRS.MODULO_ID 
            FROM DB_SEGURIDAD.segu_perfil_persona SPP 
            INNER JOIN DB_SEGURIDAD.sist_perfil IPER ON SPP.PERFIL_ID=IPER.ID_PERFIL
            INNER JOIN DB_SEGURIDAD.segu_asignacion SAS ON IPER.ID_PERFIL= SAS.PERFIL_ID
            INNER JOIN DB_SEGURIDAD.segu_relacion_sistema SRS ON SRS.ID_RELACION_SISTEMA = SAS.RELACION_SISTEMA_ID
            where persona_id = :intId) T2 ON T2.ACCION_ID=T1.ACCION_ID AND T2.MODULO_ID=T1.MODULO_ID";

        $objQuery->setParameter('strnombre',$strNombrePerfil);
        $objQuery->setParameter('intId',$intIdPersona);

        $objRsmb->addScalarResult('EXISTE', 'existe', 'string');

        $objQuery->setSQL($strSql);

        $strTienePerfil = $objQuery->getSingleScalarResult();
        return $strTienePerfil;
    }

     /**
     * Costo: 6
     * getPerfilPlanificaicon
     *
     * Función que retorna si una persona tiene un accion y modulo asociado
     *
     * @param array arrayParametros [ intIdPersona => id de la persona
     *                                strModulo => nombre del módulo 
     *                                strAccion => nombre de la accion]
     *
     * @return string $strTienePerfil retorna "S" si tiene el perfil, "N" si no tiene el perfil
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 15-06-2021
     */
    public function getAccesoPorModuloAccion($intIdPersona, $strModulo, $strAccion)
    {
        $strTienePerfil      = "N";
        $objRsmb             = new ResultSetMappingBuilder($this->_em);
        $objQuery            = $this->_em->createNativeQuery(null,$objRsmb);

        $strSql = "SELECT 
            CASE WHEN COUNT(T1.ACCION_ID) > 0 THEN 'S' ELSE 'N' END  EXISTE 
        FROM
        (SELECT
            SRS.ACCION_ID,SRS.MODULO_ID 
            FROM DB_SEGURIDAD.sist_perfil IPER
            INNER JOIN DB_SEGURIDAD.segu_asignacion SAS ON IPER.ID_PERFIL= SAS.PERFIL_ID 
            INNER JOIN DB_SEGURIDAD.segu_relacion_sistema SRS ON SRS.ID_RELACION_SISTEMA = SAS.RELACION_SISTEMA_ID
            INNER JOIN DB_SEGURIDAD.sist_modulo SMO ON SRS.MODULO_ID = SMO.ID_MODULO
            INNER JOIN DB_SEGURIDAD.sist_accion SAC ON SRS.ACCION_ID = SAC.ID_ACCION
            where lower(smo.nombre_modulo) = :strModulo
            and lower(sac.nombre_accion) = :strAccion) T1

        INNER JOIN (SELECT 
            SRS.ACCION_ID,SRS.MODULO_ID 
            FROM DB_SEGURIDAD.segu_perfil_persona SPP 
            INNER JOIN DB_SEGURIDAD.sist_perfil IPER ON SPP.PERFIL_ID=IPER.ID_PERFIL
            INNER JOIN DB_SEGURIDAD.segu_asignacion SAS ON IPER.ID_PERFIL= SAS.PERFIL_ID
            INNER JOIN DB_SEGURIDAD.segu_relacion_sistema SRS ON SRS.ID_RELACION_SISTEMA = SAS.RELACION_SISTEMA_ID
            INNER JOIN DB_SEGURIDAD.sist_modulo SMO ON SRS.MODULO_ID = SMO.ID_MODULO
            INNER JOIN DB_SEGURIDAD.sist_accion SAC ON SRS.ACCION_ID = SAC.ID_ACCION
            where persona_id = :intId
            and lower(smo.nombre_modulo) = :strModulo
            and lower(sac.nombre_accion) = :strAccion) T2 ON T2.ACCION_ID=T1.ACCION_ID AND T2.MODULO_ID=T1.MODULO_ID";

        $objQuery->setParameter('strModulo',$strModulo);
        $objQuery->setParameter('strAccion',$strAccion);
        $objQuery->setParameter('intId',$intIdPersona);

        $objRsmb->addScalarResult('EXISTE', 'existe', 'string');

        $objQuery->setSQL($strSql);

        $strTienePerfil = $objQuery->getSingleScalarResult();
        return $strTienePerfil;

    }    

}

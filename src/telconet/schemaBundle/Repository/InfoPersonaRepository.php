<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\Validator\Constraints\Count;
use telconet\schemaBundle\DependencyInjection\BaseRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

/**
 * InfoPersonaRepository.
 *
 * Repositorio que se encarga de gestionar la información inherente a las InfoPersona
 *
 * @author Unknow
 * @version 1.0 Unknow
 * 
 * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
 * @version 1.1 08-04-2016
 * Cambio de herencia de EntityRepository a BaseRepository
 */
class InfoPersonaRepository extends BaseRepository
{
     /**
     * 
     * Metodo encargado de retornar el correo electronico del empleado en NAF según el login que recibe por parámetro
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 12-09-2022
     * Costo query: 4
     * 
     * @param  array $arrayParametros
     * @return array $arrayInformacion
     * 
     */    
    public function getDestinatarioNaf($arrayParametros)
    {               
        $arrayInformacion = array();
        $objUtilService = $arrayParametros['objUtilService'];
        try
        {
            $strSelect =" SELECT (LISTAGG(NVEE.MAIL_CIA,';') WITHIN GROUP (ORDER BY NVEE.MAIL_CIA)) AS DESTINATARIO ";
            $strFrom   =" FROM NAF47_TNET.V_EMPLEADOS_EMPRESAS NVEE ";
            $strWhere  =" WHERE UPPER(NVEE.LOGIN_EMPLE) = UPPER(:strUser)
                          AND NVEE.ESTADO = 'A' ";
            
            $strSql = $strSelect.$strFrom.$strWhere;
            
            $objStmt = $this->_em->getConnection()->prepare($strSql);
            $objStmt->bindValue('strUser', $arrayParametros['strLogin']);           
            $objStmt->execute();

            $arrayInformacion = $objStmt->fetchAll();       
            
        }
        catch(\Exception $e)
        {
            $objUtilService->insertError('Telcos+',
			                  'InfoPersonaRepository->getDestinatarioNaf',
			                  $e->getMessage(),
                                          'Telcos+',
                                          '');
            throw($e);
        }
       
        return $arrayInformacion[0]['DESTINATARIO'];
    } 

    public function findFechaSistema()
    {
        $objRsm      = new ResultSetMappingBuilder($this->_em);
        $objNtvQuery = $this->_em->createNativeQuery(null, $objRsm);

        $strSQL = "SELECT  TO_CHAR(SYSDATE, 'YYYY-MM-DD HH24:MI:SS') AS SYSTDATE  FROM dual"; 
        $objRsm->addScalarResult('SYSTDATE', 'strSysDate', 'string');
        $objNtvQuery->setSQL($strSQL);
        $objDatos   =   $objNtvQuery->getResult();
        $strSysDate =   $objDatos[0]['strSysDate']; 
        $objSysDate =   new \DateTime($strSysDate); 
		return 	 $objSysDate ;
	}
    
	public function findClientesPorEmpresaPorEstado($estado,$idEmpresa){	
		/*$query = $this->_em->createQuery("SELECT a
		FROM 
                schemaBundle:InfoPersona a, schemaBundle:InfoPersonaEmpresaRol b, 
                schemaBundle:InfoEmpresaRol c, schemaBundle:AdmiRol d, schemaBundle:AdmiTipoRol e
		WHERE 
                a.id=b.personaId AND
                b.empresaRolId=c.id AND
                c.rolId=d.id AND
                d.tipoRolId=e.id AND
                a.estado = '$estado' AND
                c.empresaCod=$idEmpresa");*/
                $query = $this->_em->createQuery("SELECT a
		FROM 
                schemaBundle:InfoPersona a, schemaBundle:InfoPersonaEmpresaRol b,
                schemaBundle:InfoEmpresaRol c, schemaBundle:AdmiRol d
		WHERE 
                a.id=b.personaId AND
                b.empresaRolId=c.id AND                
                c.rolId=d.id AND                
                a.estado = '$estado' AND
                a.codEmpresa='$idEmpresa'
                 ");
                /*$query = $this->_em->createQuery("SELECT a
		FROM 
                schemaBundle:AdmiMotivo a
		WHERE                 
                a.estado = '$estado'
                 ");*/ 
		$datos = $query->getResult();
		return $datos;
	}
    
    /**
     * getDataUsuario
     * Obtiene los datos del cliente segun el id servicio
     * 
     * intServicio
     * 
     * @author John Vera
     * @version 1.0 21/08/2018
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.1 19-12-2018 se aumentó el parámetro empresa para la ejecución de 2 querys diferentes
     * 
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.2 26-03-2019 Se renombra parámetro de "fechaNacimiento" a "fechanacimiento".
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.3 14-04-2019 Se agrega UNION dentro del query que devuelve los correos de contacto, para que
     * ahora nos traiga contactos técnicos además de los comerciales.
     * 
     * @param intServicio $arrayParametros
     * @return $arrayRegistros
     * 
     */
    
    public function getDataUsuario($arrayParametros)
    {
        $objRsm = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsm);
        

        $intServicio        = $arrayParametros['intServicio'];
        $strPrefijoEmpresa  = $arrayParametros['strPrefijoEmpresa'];

        if($strPrefijoEmpresa == 'TN')
        {
        
            $strSql = "
                SELECT
                pe.NOMBRES,
                pe.APELLIDOS,
                pe.RAZON_SOCIAL,
                pe.ESTADO_CIVIL,
                pe.TIPO_IDENTIFICACION,
                pe.IDENTIFICACION_CLIENTE,
                pe.genero,
                pe.fecha_nacimiento,
                p.login,
                p.DIRECCION,
                p.LATITUD,
                p.LONGITUD,
                pa.NOMBRE_PARROQUIA,
                ca.NOMBRE_CANTON,
                pr.NOMBRE_PROVINCIA,
                pa.NOMBRE_PAIS ,
                (SELECT VALOR2
                FROM DB_GENERAL.ADMI_PARAMETRO_DET PD
                WHERE PD.DESCRIPCION = :prodPlanTelefonia
                AND PD.VALOR1        = spc.valor
                AND PD.ESTADO        = :estado ) PLAN_TELEFONIA,
                (SELECT  pfc.valor
                FROM INFO_PERSONA_FORMA_CONTACTO pfc,
                  ADMI_FORMA_CONTACTO fc
                WHERE fc.ID_FORMA_CONTACTO        = pfc.FORMA_CONTACTO_ID
                AND pfc.estado                    = :estado
                AND fc.DESCRIPCION_FORMA_CONTACTO = :contactoEmail
                AND pfc.persona_id                = pe.id_persona
                AND rownum                        < 2 ) correo,
                (SELECT  pfc.valor   
                FROM INFO_PERSONA_FORMA_CONTACTO pfc,
                  ADMI_FORMA_CONTACTO fc
                WHERE fc.ID_FORMA_CONTACTO = pfc.FORMA_CONTACTO_ID
                AND pfc.estado             = :estado
                AND fc.DESCRIPCION_FORMA_CONTACTO LIKE :contactoTelf
                AND pfc.persona_id = pe.id_persona
                AND rownum         < 2 ) telefono,
                to_char(s.FE_CREACION, 'DD/MM/YYYY') FECHA_CREACION
              FROM info_servicio s,
                info_punto p,
                admi_sector se,
                admi_parroquia pa,
                admi_canton ca,
                DB_GENERAL.ADMI_PROVINCIA pr,
                DB_GENERAL.ADMI_REGION re,
                DB_GENERAL.ADMI_PAIS pa,
                INFO_PERSONA_EMPRESA_ROL per,
                info_persona pe,
                info_servicio_prod_caract spc,
                ADMI_PRODUCTO_CARACTERISTICA pc,
                ADMI_CARACTERISTICA car  
              WHERE s.PUNTO_ID       = p.ID_PUNTO
              AND s.ID_SERVICIO      = :idServicio
              AND p.sector_id        = se.id_sector
              AND pa.ID_PARROQUIA    = se.PARROQUIA_ID
              AND ca.ID_CANTON       = pa.CANTON_ID
              AND pr.ID_PROVINCIA    = ca.PROVINCIA_ID
              AND re.ID_REGION       = pr.REGION_ID
              AND pa.ID_PAIS         = re.PAIS_ID
              AND per.ID_PERSONA_ROL = p.PERSONA_EMPRESA_ROL_ID
              AND pe.ID_PERSONA      = per.PERSONA_ID 
              and s.ID_SERVICIO      = spc.SERVICIO_ID
              and spc.PRODUCTO_CARACTERISITICA_ID = pc.ID_PRODUCTO_CARACTERISITICA
              and pc.CARACTERISTICA_ID = car.ID_CARACTERISTICA
              and car.DESCRIPCION_CARACTERISTICA = :planTelefonia
              ";

            $objQuery->setParameter("prodPlanTelefonia",  'PROD_PLAN TELEFONIA');
            $objQuery->setParameter("planTelefonia",  'PLAN TELEFONIA');
        
         }
         else
         {
            $strSql = "
            SELECT
            pe.nombres,
            pe.apellidos,
            pe.razon_social,
            pe.estado_civil,
            pe.tipo_identificacion,
            pe.identificacion_cliente,
            pe.genero,
            pe.fecha_nacimiento,
            p.login,
            p.direccion,
            p.latitud,
            p.longitud,
            pa.nombre_parroquia,
            ca.nombre_canton,
            pr.nombre_provincia,
            pa.nombre_pais,
            (
                SELECT
                    *
                FROM
                    (
                        SELECT
                            pfc.valor
                        FROM
                            db_comercial.info_persona_forma_contacto   pfc,
                            db_comercial.admi_forma_contacto           fc,
                            db_comercial.info_servicio                 s,
                            db_comercial.info_punto                    p,
                            db_general.admi_sector                     se,
                            db_general.admi_parroquia                  pa,
                            db_general.admi_canton                     ca,
                            db_general.admi_provincia                  pr,
                            db_general.admi_region                     re,
                            db_general.admi_pais                       pa,
                            db_comercial.info_persona_empresa_rol      per,
                            db_comercial.info_persona                  pe
                        WHERE
                            fc.id_forma_contacto                = pfc.forma_contacto_id
                            AND pfc.estado                      = :estado
                            AND fc.descripcion_forma_contacto   = :contactoEmail
                            AND pfc.persona_id                  = pe.id_persona
                            AND s.punto_id                      = p.id_punto
                            AND s.id_servicio                   = :idServicio
                            AND p.sector_id                     = se.id_sector
                            AND pa.id_parroquia                 = se.parroquia_id
                            AND ca.id_canton                    = pa.canton_id
                            AND pr.id_provincia                 = ca.provincia_id
                            AND re.id_region                    = pr.region_id
                            AND pa.id_pais                      = re.pais_id
                            AND per.id_persona_rol              = p.persona_empresa_rol_id
                            AND pe.id_persona                   = per.persona_id
                        UNION
                        SELECT
                            pfc.valor
                        FROM
                            db_comercial.info_punto_forma_contacto   pfc,
                            db_comercial.admi_forma_contacto         fc,
                            db_comercial.info_servicio               s,
                            db_comercial.info_punto                  p,
                            db_general.admi_sector                   se,
                            db_general.admi_parroquia                pa,
                            db_general.admi_canton                   ca,
                            db_general.admi_provincia                pr,
                            db_general.admi_region                   re,
                            db_general.admi_pais                     pa,
                            db_comercial.info_persona_empresa_rol    per,
                            db_comercial.info_persona                pe
                        WHERE
                            fc.id_forma_contacto                = pfc.forma_contacto_id
                            AND pfc.estado                      = :estado
                            AND fc.descripcion_forma_contacto   = :contactoEmail
                            AND pfc.punto_id                    = p.id_punto
                            AND s.punto_id                      = p.id_punto
                            AND s.id_servicio                   = :idServicio
                            AND p.sector_id                     = se.id_sector
                            AND pa.id_parroquia                 = se.parroquia_id
                            AND ca.id_canton                    = pa.canton_id
                            AND pr.id_provincia                 = ca.provincia_id
                            AND re.id_region                    = pr.region_id
                            AND pa.id_pais                      = re.pais_id
                            AND per.id_persona_rol              = p.persona_empresa_rol_id
                            AND pe.id_persona                   = per.persona_id
                    )
                WHERE
                    ROWNUM <= 1
            ) AS correo,
            (
                SELECT
                    *
                FROM
                    (
                        SELECT
                            pfc.valor
                        FROM
                            db_comercial.info_persona_forma_contacto   pfc,
                            db_comercial.admi_forma_contacto           fc,
                            db_comercial.info_servicio                 s,
                            db_comercial.info_punto                    p,
                            db_general.admi_sector                     se,
                            db_general.admi_parroquia                  pa,
                            db_general.admi_canton                     ca,
                            db_general.admi_provincia                  pr,
                            db_general.admi_region                     re,
                            db_general.admi_pais                       pa,
                            db_comercial.info_persona_empresa_rol      per,
                            db_comercial.info_persona                  pe
                        WHERE
                            fc.id_forma_contacto        = pfc.forma_contacto_id
                            AND pfc.estado              = :estado
                            AND fc.descripcion_forma_contacto LIKE :contactoTelf
                            AND pfc.persona_id          = pe.id_persona
                            AND s.punto_id              = p.id_punto
                            AND s.id_servicio           = :idServicio
                            AND p.sector_id             = se.id_sector
                            AND pa.id_parroquia         = se.parroquia_id
                            AND ca.id_canton            = pa.canton_id
                            AND pr.id_provincia         = ca.provincia_id
                            AND re.id_region            = pr.region_id
                            AND pa.id_pais              = re.pais_id
                            AND per.id_persona_rol      = p.persona_empresa_rol_id
                            AND pe.id_persona           = per.persona_id
                        UNION
                        SELECT
                            pfc.valor
                        FROM
                            db_comercial.info_punto_forma_contacto   pfc,
                            db_comercial.admi_forma_contacto         fc,
                            db_comercial.info_servicio               s,
                            db_comercial.info_punto                  p,
                            db_general.admi_sector                   se,
                            db_general.admi_parroquia                pa,
                            db_general.admi_canton                   ca,
                            db_general.admi_provincia                pr,
                            db_general.admi_region                   re,
                            db_general.admi_pais                     pa,
                            db_comercial.info_persona_empresa_rol    per,
                            db_comercial.info_persona                pe
                        WHERE
                            fc.id_forma_contacto        = pfc.forma_contacto_id
                            AND pfc.estado              = :estado
                            AND fc.descripcion_forma_contacto LIKE :contactoTelf
                            AND pfc.punto_id            = p.id_punto
                            AND s.punto_id              = p.id_punto
                            AND s.id_servicio           = :idServicio
                            AND p.sector_id             = se.id_sector
                            AND pa.id_parroquia         = se.parroquia_id
                            AND ca.id_canton            = pa.canton_id
                            AND pr.id_provincia         = ca.provincia_id
                            AND re.id_region            = pr.region_id
                            AND pa.id_pais              = re.pais_id
                            AND per.id_persona_rol      = p.persona_empresa_rol_id
                            AND pe.id_persona           = per.persona_id
                    )
                WHERE
                    ROWNUM <= 1
            ) AS telefono,
            TO_CHAR(s.fe_creacion, 'DD/MM/YYYY') fecha_creacion,
            (
                SELECT
                    *
                FROM
                    (
                        SELECT
                            pfc.valor
                        FROM
                            db_comercial.info_persona_forma_contacto   pfc,
                            db_comercial.admi_forma_contacto           fc,
                            db_comercial.info_servicio                 s,
                            db_comercial.info_punto                    p,
                            db_general.admi_sector                     se,
                            db_general.admi_parroquia                  pa,
                            db_general.admi_canton                     ca,
                            db_general.admi_provincia                  pr,
                            db_general.admi_region                     re,
                            db_general.admi_pais                       pa,
                            db_comercial.info_persona_empresa_rol      per,
                            db_comercial.info_persona                  pe
                        WHERE
                            fc.id_forma_contacto    = pfc.forma_contacto_id
                            AND pfc.estado          = 'Activo'
                            AND fc.id_forma_contacto IN (
                                '25',
                                '26',
                                '27'
                            )
                            AND pfc.persona_id          = pe.id_persona
                            AND s.punto_id              = p.id_punto
                            AND s.id_servicio           = :idServicio
                            AND p.sector_id             = se.id_sector
                            AND pa.id_parroquia         = se.parroquia_id
                            AND ca.id_canton            = pa.canton_id
                            AND pr.id_provincia         = ca.provincia_id
                            AND re.id_region            = pr.region_id
                            AND pa.id_pais              = re.pais_id
                            AND per.id_persona_rol      = p.persona_empresa_rol_id
                            AND pe.id_persona           = per.persona_id
                        UNION
                        SELECT
                            pfc.valor
                        FROM
                            db_comercial.info_punto_forma_contacto   pfc,
                            db_comercial.admi_forma_contacto         fc,
                            db_comercial.info_servicio               s,
                            db_comercial.info_punto                  p,
                            db_general.admi_sector                   se,
                            db_general.admi_parroquia                pa,
                            db_general.admi_canton                   ca,
                            db_general.admi_provincia                pr,
                            db_general.admi_region                   re,
                            db_general.admi_pais                     pa,
                            db_comercial.info_persona_empresa_rol    per,
                            db_comercial.info_persona                pe
                        WHERE
                            fc.id_forma_contacto    = pfc.forma_contacto_id
                            AND pfc.estado          = 'Activo'
                            AND fc.id_forma_contacto IN (
                                '25',
                                '26',
                                '27'
                            )
                            AND pfc.punto_id        = p.id_punto
                            AND s.punto_id          = p.id_punto
                            AND s.id_servicio       = :idServicio
                            AND p.sector_id         = se.id_sector
                            AND pa.id_parroquia     = se.parroquia_id
                            AND ca.id_canton        = pa.canton_id
                            AND pr.id_provincia     = ca.provincia_id
                            AND re.id_region        = pr.region_id
                            AND pa.id_pais          = re.pais_id
                            AND per.id_persona_rol  = p.persona_empresa_rol_id
                            AND pe.id_persona       = per.persona_id
                    )
                WHERE
                    ROWNUM <= 1
            ) AS celular
        FROM
            db_comercial.info_servicio              s,
            db_comercial.info_punto                 p,
            db_general.admi_sector                  se,
            db_general.admi_parroquia               pa,
            db_general.admi_canton                  ca,
            db_general.admi_provincia               pr,
            db_general.admi_region                  re,
            db_general.admi_pais                    pa,
            db_comercial.info_persona_empresa_rol   per,
            db_comercial.info_persona               pe
        WHERE
            s.punto_id              = p.id_punto
            AND s.id_servicio       = :idServicio
            AND p.sector_id         = se.id_sector
            AND pa.id_parroquia     = se.parroquia_id
            AND ca.id_canton        = pa.canton_id
            AND pr.id_provincia     = ca.provincia_id
            AND re.id_region        = pr.region_id
            AND pa.id_pais          = re.pais_id
            AND per.id_persona_rol  = p.persona_empresa_rol_id
            AND pe.id_persona       = per.persona_id
            "; 
             
        }
        
        $objQuery->setParameter("estado", 'Activo');
        $objQuery->setParameter("idServicio", $intServicio);
        $objQuery->setParameter("contactoTelf",  'Telefono%');
        $objQuery->setParameter("contactoEmail",  'Correo Electronico');       

        
        $objRsm->addScalarResult(strtoupper('NOMBRES'), 'nombres', 'string');
        $objRsm->addScalarResult(strtoupper('APELLIDOS'), 'apellidos', 'string');
        $objRsm->addScalarResult(strtoupper('RAZON_SOCIAL'), 'razonSocial', 'string');
        $objRsm->addScalarResult(strtoupper('ESTADO_CIVIL'), 'estadoCivil', 'string');
        $objRsm->addScalarResult(strtoupper('TIPO_IDENTIFICACION'), 'tipoIdentificacion', 'string');
        $objRsm->addScalarResult(strtoupper('IDENTIFICACION_CLIENTE'), 'identificacionCliente', 'string');
        $objRsm->addScalarResult(strtoupper('GENERO'), 'genero', 'string');
        $objRsm->addScalarResult(strtoupper('FECHA_NACIMIENTO'), 'fechanacimiento', 'string');
        $objRsm->addScalarResult(strtoupper('LOGIN'), 'login', 'string');        
        $objRsm->addScalarResult(strtoupper('DIRECCION'), 'direccion', 'string');        
        $objRsm->addScalarResult(strtoupper('LATITUD'), 'latitud', 'string');
        $objRsm->addScalarResult(strtoupper('LONGITUD'), 'longitud', 'string');
        $objRsm->addScalarResult(strtoupper('NOMBRE_PARROQUIA'), 'parroquia', 'string');
        $objRsm->addScalarResult(strtoupper('NOMBRE_CANTON'), 'canton', 'string');
        $objRsm->addScalarResult(strtoupper('NOMBRE_PROVINCIA'), 'provincia', 'string');
        $objRsm->addScalarResult(strtoupper('NOMBRE_PAIS'), 'pais', 'string');
        $objRsm->addScalarResult(strtoupper('PLAN_TELEFONIA'), 'planTelefonia', 'string');
        $objRsm->addScalarResult(strtoupper('CORREO'), 'correo', 'string');
        $objRsm->addScalarResult(strtoupper('TELEFONO'), 'telefono', 'string');       
        $objRsm->addScalarResult(strtoupper('FECHA_CREACION'), 'feCreacion', 'string');
        $objRsm->addScalarResult(strtoupper('CELULAR'), 'celular', 'string');
        
        $objQuery->setSQL($strSql);
        
        $arrayRegistros = $objQuery->getResult();
        
        return $arrayRegistros;
    }    
        

	public function find30PorEmpresaPorEstadoPorUsuario($idEmpresa,$usuario,$tipoPersona,$limit,$page,$start,$idCliente){	
                $criterio_usuario='';
                $criterio_cliente='';
                $from_contacto_cli='';
                $objeto_retorna='a';
                if ($usuario!=''){$criterio_usuario=" a.usrCreacion='$usuario' AND ";}
                
                if ($idCliente)
                {
                    $tipoPersona='Cliente';
                    $objeto_retorna='g';                    
                    $from_contacto_cli=" ,schemaBundle:InfoPersonaContacto f, schemaBundle:InfoPersona g ";
                    $criterio_cliente=" b.id=f.personaEmpresaRolId AND f.contactoId=g.id AND b.personaId=$idCliente AND";
                }                 
		$query = $this->_em->createQuery("SELECT $objeto_retorna
		FROM 
                schemaBundle:InfoPersona a, schemaBundle:InfoPersonaEmpresaRol b, 
                schemaBundle:InfoEmpresaRol c, schemaBundle:AdmiRol d, schemaBundle:AdmiTipoRol e $from_contacto_cli
		WHERE 
                a.id=b.personaId AND
                b.empresaRolId=c.id AND
                c.rolId=d.id AND
                d.tipoRolId=e.id "
                ."AND
                $criterio_cliente
                $criterio_usuario    
                c.empresaCod='$idEmpresa' 
                AND LOWER(e.descripcionTipoRol)=LOWER('$tipoPersona') order by a.feCreacion DESC"
                        )->setMaxResults(30);
		$datos = $query->getResult();
                //echo $query->getSQL();
                $total=count($query->getResult());
		$datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
                $resultado['registros']=$datos;
                $resultado['total']=$total;
		return $resultado;
	} 
	public function findPreClientesPorEmpresaPorEstadoPorUsuario($estado,$idEmpresa,$usuario){	    
		$query = $this->_em->createQuery("SELECT a
		FROM 
                schemaBundle:InfoPersona a, schemaBundle:InfoPersonaEmpresaRol b, 
                schemaBundle:InfoEmpresaRol c, schemaBundle:AdmiRol d, schemaBundle:AdmiTipoRol e
		WHERE 
                a.id=b.personaId AND
                b.empresaRolId=c.id AND
                c.rolId=d.id AND
                d.tipoRolId=e.id AND
                a.estado = '$estado' AND 
                a.usrCreacion='$usuario' AND    
                c.empresaCod='$idEmpresa' AND LOWER(e.descripcionTipoRol)=LOWER('Pre-cliente') ");
                //echo $query->getSQL();die;
		$datos = $query->getResult();
		return $datos;
	} 

        
        public function findClientesXEmpresaEstado()
        {
            return $qb =$this->createQueryBuilder("t")
            ->select("a")
            ->from('schemaBundle:InfoPersona a, schemaBundle:InfoPersonaEmpresaRol b, schemaBundle:InfoEmpresaRol c, schemaBundle:AdmiRol d, schemaBundle:AdmiTipoRol e','')
            ->where("a.id=b.personaId AND b.empresaRolId=c.id AND c.rolId=d.id AND d.tipoRolId=e.id AND LOWER(e.descripcionTipoRol)=LOWER('Cliente') ");
        }

    /**
    * generarJsonContratista
    *
    * Esta funcion retorna la lsita de empresas externas
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 13-10-2015
    *
    * @param integer  $codEmpresa
    * @param integer  $perfil
    *
    * @return array $objResultado  Objeto en formato JSON
    *
    */
    public function generarJsonContratistas($codEmpresa,$tipoRol)
    {
        $arrayEncontrados        = array();
        $arrayDatos              = $this->getRegistrosContratistas($codEmpresa,$tipoRol,null,null);
        $intCantidad             = $arrayDatos['total'];
        $arrayRegistros          = $arrayDatos['registros'];
        $strError                = "No existen contratistas";
        $nombre                  = "";

        if ($arrayRegistros)
        {
            foreach ($arrayRegistros as $data)
            {
                if($data["razonSocial"])
                {
                    $nombre = $data["razonSocial"];
                }
                else
                {
                    $nombre = $data["nombres"]." ".$data["apellidos"];
                }
                $arrayEncontrados[]  = array('id_empresa_externa'     => $data["id"],
                                             'nombre_empresa_externa' => $nombre);
            }

            $objData        = json_encode($arrayEncontrados);
            $objResultado   = '{"result": {"total":"'.$intCantidad.'","encontrados":'.$objData.'},
                                "myMetaData": {"boolSuccess": "1", "message":""} }';
            return $objResultado;

        }
        else
        {
            $objResultado = '{"result": {"total":"0","encontrados":[]}, "myMetaData": {"boolSuccess": "0", "message":"'.$strError.'"} }';
            return $objResultado;
        }

    }

     /**
     * getRegistrosContratistas
     *
     * Esta funcion retorna la lsita de empresas externas
     *
     * @author Allan Suarez <arsuarez@telconet.ec> 
     * @version 1.1 16-05-2016 - Se añade filtro de id persona para la consulta de contratistas/proveedores y descripcion de rol
     * 
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 13-10-2015
     *
     * @param integer  $codEmpresa
     * @param integer  $perfil
     * @param integer  $tipoRol
     * @param integer  $rol
     *
     * @return array $arrayDatos        Consulta de la BD
     *
     */
    public function getRegistrosContratistas($codEmpresa,$tipoRol,$rol,$idPersona)
    {
        $arrayDatos     = array();
        $strQuery       = $this->_em->createQuery();
        
        $strWhere = "";
        
        if($idPersona)
        {
            $strWhere .= " AND a.id = :idPersona ";
        }
        
        if($rol)
        {
            $strWhere .= " AND d.id = :rol ";
        }

        $strCampos  = "SELECT a.id as id,a.nombres as nombres,a.apellidos as apellidos,a.razonSocial as razonSocial,a.estado";
        $strFrom    = " FROM
                            schemaBundle:InfoPersona a,
                            schemaBundle:InfoPersonaEmpresaRol b,
                            schemaBundle:InfoEmpresaRol c,
                            schemaBundle:AdmiRol d,
                            schemaBundle:AdmiTipoRol e,
                            schemaBundle:InfoEmpresaGrupo eg
                        WHERE
                            a.id=b.personaId AND
                            b.empresaRolId=c.id AND
                            c.rolId=d.id AND
                            d.tipoRolId=e.id AND
                            eg.id = c.empresaCod AND
                            eg.id = :varEmpresa AND
                            b.empresaRolId = :varEmpresaRol AND
                            a.estado <> :varEstado AND
                            b.estado <> :varEstado AND
                            c.estado <> :varEstado AND
                            d.estado <> :varEstado
                            $strWhere
                            ORDER BY a.razonSocial ";

        $strSelect     = $strCampos . $strFrom;

        $strQuery->setParameter('varEmpresa', $codEmpresa );
        $strQuery->setParameter('varEmpresaRol',$tipoRol);
        $strQuery->setParameter('varEstado', 'Eliminado');
        
        if($idPersona)
        {
            $strQuery->setParameter('idPersona', $idPersona);
        }
        if($rol)
        {
            $strQuery->setParameter('rol', $rol);
        }
        
        $strQuery->setDQL($strSelect);

        $strDatos = $strQuery->getResult();

        $strQueryCount    = $this->_em->createQuery();
        $strCount         = " SELECT COUNT(a) ";
        $strSelectCount   = $strCount . $strFrom;

        $strQueryCount->setParameter('varEmpresa', $codEmpresa );
        $strQueryCount->setParameter('varEmpresaRol', $tipoRol);
        $strQueryCount->setParameter('varEstado', 'Eliminado');
        
        if($idPersona)
        {
            $strQueryCount->setParameter('idPersona', $idPersona);
        }
        if($rol)
        {
            $strQueryCount->setParameter('rol', $rol);
        }
        
        $strQueryCount->setDQL($strSelectCount);

        $intTotal                = $strQueryCount->getSingleScalarResult();

        $arrayDatos['registros'] = $strDatos;
        $arrayDatos['total']     = $intTotal;

        return $arrayDatos;
    }

     /**
     * getRolesEmpresaPorTipo
     *
     * Esta funcion retorna la lista de los perfiles empresa segun el tipo de rol que se envie
     *
     * @author Allan Suarez <asuarez@telconet.ec>
     * @version 1.1 20-05-2016 - Agregar la descripcion de rol para busqueda
     * 
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 19-01-2016
     *
     * @param sting  $codEmpresa
     * @param sting  $tipoRol
     * @param sting  $rol
     *
     * @return array $strDatos        Consulta de la BD
     *
     */
    public function getRolEmpresaPorTipo($codEmpresa,$tipoRol,$rol)
    {
        $strQuery       = $this->_em->createQuery();

        $strCampos  = " SELECT infoEmpresaRol.id as idPerfil";

        $strFrom    = " FROM schemaBundle:InfoEmpresaRol infoEmpresaRol
                        WHERE infoEmpresaRol.rolId IN
                          (SELECT admiRol.id
                          FROM schemaBundle:AdmiRol admiRol                          
                          WHERE admiRol.tipoRolId IN
                            (SELECT admitipoRol.id
                            FROM schemaBundle:AdmiTipoRol admitipoRol
                            WHERE admitipoRol.descripcionTipoRol = :varTipoRol
                            AND admitipoRol.estado <> :varEstado
                            )
                          AND admiRol.descripcionRol    = :rol
                          AND admiRol.estado <> :varEstado
                          )
                        AND infoEmpresaRol.estado <> :varEstado
                        AND infoEmpresaRol.empresaCod = :varEmpresa 
                        ";

        $strSelect     = $strCampos . $strFrom;

        $strQuery->setParameter('varTipoRol',$tipoRol);
        $strQuery->setParameter('varEstado', 'Eliminado');
        $strQuery->setParameter('varEmpresa', $codEmpresa);
        $strQuery->setParameter('rol', $rol);
        $strQuery->setDQL($strSelect);                

        $strDatos = $strQuery->getOneOrNullResult();

        return $strDatos;
    }

    /**
     * Documentación para el método 'findPersonasXTipoRol'.
     *
     * Obtiene registros de personas de diversos tipos de rol
     * @param  String $tipoRol               tipo de rol para el que se obtendra información
     * @param  String $nombre                nombre de las personas que seran filtradas
     * @param  String $codEmpresa            codigo de empresa a consultar
     * @param  String $rol                   Rol de personas a buscar
     * @param  Array  $arrayParamPerfiles    Arreglo de filtros utilizados en listado de empleados en asignacion de perfiles
     * @return List  InfoPersona
     * 
     * @author Sofía Fernández <sfernandez@telconet.ec>
     * @version 1.2 12-02-2017 - Se agrega condicion de busqueda para tipo rol Propietario
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 14-09-2014 - Para busqueda de personas de tipo rol Empleado y rol Tecnico enviado como referencia se
     *                           consulta que estas provengan de un area TECNICA y no por nombre de departamento como estaba inicialmente
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 17-11-2014
     */    
    public function findPersonasXTipoRol($tipoRol = "Empleado", $nombre = "", $codEmpresa = "", $rol = "", $arrayParamPerfiles = "")
    {
        $query = $this->_em->createQuery("");
        $whereAdd = "";
        $fromAdd = "";                
        
        if($tipoRol == "Empleado-Cuadrilla")
        {
            if($nombre != "" && $nombre)
            {
                $whereAdd = "AND CONCAT(LOWER(p.nombres),CONCAT(' ',LOWER(p.apellidos))) like :nombreP ";
                $query->setParameter('nombreP', '%'.strtolower($nombre).'%');
            }

            $sql = "SELECT p 
                        FROM 
                            schemaBundle:InfoPersona p, 
                            schemaBundle:InfoPersonaEmpresaRol per, 
                            schemaBundle:InfoEmpresaRol er,
                            schemaBundle:InfoEmpresaGrupo eg,
                            schemaBundle:AdmiRol r, 
                            schemaBundle:AdmiTipoRol tr 

                        WHERE 
                            p.id = per.personaId AND 
                            er.id = per.empresaRolId AND
                            eg.id = er.empresaCod AND
                            r.id = er.rolId AND
                            tr.id = r.tipoRolId AND               
                            LOWER(tr.descripcionTipoRol) = LOWER(:empleadoP) AND
                            lower(p.estado) not like lower(:eliminadoP1) AND 
                            lower(per.estado) not like lower(:eliminadoP2) AND
                            lower(er.estado) not like lower(:eliminadoP3) AND 
                            lower(r.estado) not like lower(:eliminadoP4) AND 
							per.cuadrillaId is null     " . $whereAdd . "                               
                         ORDER BY p.nombres, p.apellidos 
                        ";
            $query->setParameter('empleadoP', 'Empleado');
            $query->setParameter('eliminadoP1', 'Eliminado');
            $query->setParameter('eliminadoP2', 'Eliminado');
            $query->setParameter('eliminadoP3', 'Eliminado');
            $query->setParameter('eliminadoP4', 'Eliminado');
        }
        if($tipoRol == "Empleado")
        {
            if($nombre != "" && $nombre)
            {    
                $whereAdd .= "AND CONCAT(LOWER(p.nombres),CONCAT(' ',LOWER(p.apellidos))) like :nombreP ";
                $query->setParameter('nombreP', '%'.strtolower($nombre).'%');
            }

            if($codEmpresa != "" && $codEmpresa)
            {
                $whereAdd .= "AND eg.id = :codEmpresaP ";
                $query->setParameter('codEmpresaP', $codEmpresa);
            }

            if($rol != "" && $rol)
            {
                if($rol == "Tecnico")
                {
                    //Se establece las areas de donde se buscan los usuario/tecnicos
                    //designados para realizar las nuevas instalaciones de UM                   
                    $fromAdd  .= ",schemaBundle:AdmiDepartamento ad , schemaBundle:AdmiArea ar ";
                    $whereAdd .= "AND ad.id = per.departamentoId ";
                    $whereAdd .= "AND ad.areaId = ar.id ";
                    $whereAdd .= "AND ar.nombreArea in (:areas) ";
                    $query->setParameter('areas', array('TECNICA','Tecnica.'));                    
                }
                else
                {
                    $whereAdd .= "AND LOWER(r.descripcionRol) like LOWER(:rolP) ";
                    $query->setParameter('rolP', '%'.$rol.'%');
                }
            }

            $sql = "SELECT p 
                        FROM 
                            schemaBundle:InfoPersona p, 
                            schemaBundle:InfoPersonaEmpresaRol per, 
                            schemaBundle:InfoEmpresaRol er,
                            schemaBundle:InfoEmpresaGrupo eg,
                            schemaBundle:AdmiRol r, 
                            schemaBundle:AdmiTipoRol tr 
                            $fromAdd
                        WHERE 
                            p.id = per.personaId AND 
                            er.id = per.empresaRolId AND
                            eg.id = er.empresaCod AND
                            r.id = er.rolId AND
                            tr.id = r.tipoRolId AND               
                            LOWER(tr.descripcionTipoRol) = LOWER(:tipoRolP) AND 
                            lower(p.estado) not like lower(:eliminadoP1) AND 
                            lower(per.estado) not like lower(:eliminadoP2) AND
                            lower(er.estado) not like lower(:eliminadoP3) AND 
                            lower(r.estado) not like lower(:eliminadoP4) 
                            $whereAdd 
                               
                         ORDER BY p.nombres, p.apellidos 
                        ";
            
            $query->setParameter('tipoRolP', $tipoRol);
            $query->setParameter('eliminadoP1', 'Eliminado');
            $query->setParameter('eliminadoP2', 'Eliminado');
            $query->setParameter('eliminadoP3', 'Eliminado');
            $query->setParameter('eliminadoP4', 'Eliminado');
        }
        if($tipoRol == "Empleado-Perfiles")
        {
            $tipoAsignacion = $arrayParamPerfiles["tipoAsignacion"];
            $empresa        = $arrayParamPerfiles["empresa"] ? $arrayParamPerfiles["empresa"] : "";
            $ciudad         = $arrayParamPerfiles["ciudad"] ? $arrayParamPerfiles["ciudad"] : "";
            $departamento   = $arrayParamPerfiles["departamento"] ? $arrayParamPerfiles["departamento"] : "";

            if($nombre != "" && $nombre)
            {
                $whereAdd .= "AND ( CONCAT(LOWER(p.nombres),CONCAT(' ',LOWER(p.apellidos))) like :nombreP ";
                $whereAdd .= "OR lower(p.razonSocial) like :razonSocialP ) ";
                $query->setParameter('nombreP', '%'.strtolower($nombre).'%' );
                $query->setParameter('razonSocialP', '%'.strtolower($nombre).'%');
            }

            if($rol != "" && $rol)
            {
                $whereAdd .= "AND LOWER(r.descripcionRol) like LOWER(:rolP) ";
                $query->setParameter('rolP', '%'.$rol.'%' );
            }

            if($tipoAsignacion == "Normal")
            {
                $whereAdd .= "AND p.id NOT IN (SELECT spp.personaId FROM schemaBundle:SeguPerfilPersona spp GROUP BY spp.personaId) ";
            }

            //se agrega codigo para agregar nuevos filtros     
            if(($empresa != "" && $empresa) || ($ciudad != "" && $ciudad) || ($departamento != "" && $departamento))
            {
                $whereAdd .= "AND p.id IN  (SELECT perRol.personaIdValor  FROM schemaBundle:InfoPersonaEmpresaRol perRol, ";
                $whereAdd .= "schemaBundle:InfoOficinaGrupo og,    schemaBundle:AdmiCanton ac , ";
                $whereAdd .= "schemaBundle:AdmiDepartamento ad  WHERE perRol.departamentoId=ad.id  AND perRol.oficinaId     = og.id ";
                $whereAdd .= "AND og.cantonId       =ac.id ";
                $whereAdd .= "AND perRol.estado=:estadoPER ";
                if($empresa != "" && $empresa)
                {
                    $whereAdd .= "AND og.empresaId = :empresa ";
                }
                if($ciudad != "" && $ciudad)
                {
                    $whereAdd .= "AND ac.id = :ciudad ";
                }
                if($departamento != "" && $departamento)
                {
                    $whereAdd .= "AND ad.id = :departamento ";
                }
                $whereAdd .= " ) ";
            }
            //se agrega codigo para agregar nuevos filtros
            if(($empresa != "" && $empresa) || ($ciudad != "" && $ciudad) || ($departamento != "" && $departamento))
            {

                if($empresa != "" && $empresa)
                {
                    $query->setParameter('empresa', $empresa);
                }
                if($ciudad != "" && $ciudad)
                {
                    $query->setParameter('ciudad', $ciudad);
                }
                if($departamento != "" && $departamento)
                {
                    $query->setParameter('departamento', $departamento);
                }

                $query->setParameter('estadoPER', 'Activo');
            }

            $sql = "SELECT p 
                        FROM 
                            schemaBundle:InfoPersona p, 
                            schemaBundle:InfoPersonaEmpresaRol per, 
                            schemaBundle:InfoEmpresaRol er,
                            schemaBundle:InfoEmpresaGrupo eg,
                            schemaBundle:AdmiRol r, 
                            schemaBundle:AdmiTipoRol tr 

                        WHERE 
                            p.id = per.personaId AND 
                            er.id = per.empresaRolId AND
                            eg.id = er.empresaCod AND
                            r.id = er.rolId AND
                            tr.id = r.tipoRolId AND               
                            (LOWER(tr.descripcionTipoRol) = LOWER(:descripcionTipoRolP1) 
                             OR LOWER(tr.descripcionTipoRol) = LOWER(:descripcionTipoRolP2)) AND    
                            lower(p.estado) not like lower(:eliminadoP1) AND 
                            lower(per.estado) not like lower(:eliminadoP2) AND
                            lower(er.estado) not like lower(:eliminadoP3) AND 
                            lower(r.estado) not like lower(:eliminadoP4) 
                            $whereAdd 
							 
                         ORDER BY p.nombres, p.apellidos 
                        ";
            
            $query->setParameter('descripcionTipoRolP1', 'Empleado');
            $query->setParameter('descripcionTipoRolP2', 'Personal Externo');
            $query->setParameter('eliminadoP1', 'Eliminado');
            $query->setParameter('eliminadoP2', 'Eliminado');
            $query->setParameter('eliminadoP3', 'Eliminado');
            $query->setParameter('eliminadoP4', 'Eliminado');
            
        }
        if($tipoRol == "Proveedor" || $tipoRol == "Propietario")
        {
            if($nombre != "" && $nombre)
            {
                $whereAdd .= "AND( LOWER(a.razonSocial) like :nombreP ";
                $whereAdd .= "OR CONCAT(LOWER(a.nombres),CONCAT(' ',LOWER(a.apellidos))) like :razonSocialP) ";
                $query->setParameter('nombreP', '%'.strtolower($nombre).'%' );
                $query->setParameter('razonSocialP', '%'.strtolower($nombre).'%');
            }

            if($codEmpresa != "" && $codEmpresa)
            {
                $fromAdd .= ",schemaBundle:InfoEmpresaGrupo eg ";
                $whereAdd .= "AND eg.id = c.empresaCod AND eg.id = :codEmpresaP ";
                $query->setParameter('codEmpresaP', $codEmpresa );
            }

            $sql = "SELECT a 
                        FROM 
                            schemaBundle:InfoPersona a,  
                            schemaBundle:InfoPersonaEmpresaRol b,  
                            schemaBundle:InfoEmpresaRol c,  
                            schemaBundle:AdmiRol d,  
                            schemaBundle:AdmiTipoRol e 
			    $fromAdd
                        WHERE 
                            a.id=b.personaId AND 
                            b.empresaRolId=c.id AND 
                            c.rolId=d.id AND
                            d.tipoRolId=e.id AND 
                            LOWER(e.descripcionTipoRol)= LOWER(:tipoRolP) AND
                            lower(a.estado) not like lower(:eliminadoP1) AND 
                            lower(b.estado) not like lower(:eliminadoP2) AND
                            lower(c.estado) not like lower(:eliminadoP3) AND 
                            lower(d.estado) not like lower(:eliminadoP4) 
                            $whereAdd 
                                   
                          ORDER BY a.razonSocial 
                        ";
            $query->setParameter('tipoRolP', $tipoRol);
            $query->setParameter('eliminadoP1', 'Eliminado');
            $query->setParameter('eliminadoP2', 'Eliminado');
            $query->setParameter('eliminadoP3', 'Eliminado');
            $query->setParameter('eliminadoP4', 'Eliminado');
        }
        
        $query->setDQL($sql);
        $datos = $query->getResult();
        return $datos;
    }

    public function findClientesPorEmpresaPorEstadoPorNombre($estado,$idEmpresa,$filtro){
            $filtro=  strtoupper($filtro);
		/*$query = $this->_em->createQuery("SELECT a
		FROM 
                schemaBundle:InfoPersona a, schemaBundle:InfoPersonaEmpresaRol b, 
                schemaBundle:InfoEmpresaRol c, schemaBundle:AdmiRol d, schemaBundle:AdmiTipoRol e
		WHERE 
                a.id=b.personaId AND
                b.empresaRolId=c.id AND
                c.rolId=d.id AND
                d.tipoRolId=e.id AND
                a.estado = '$estado' AND    
                c.empresaCod='$idEmpresa' AND e.descripcionTipoRol='Cliente' AND 
                 (CONCAT(UPPER(a.nombres),CONCAT(' ',UPPER(a.apellidos))) like '%$filtro%' OR UPPER(a.razonSocial) like '%$filtro%')");*/
		$query = $this->_em->createQuery("SELECT a
		FROM 
                schemaBundle:InfoPersona a
		WHERE 
                a.estado = '$estado' AND    
                (CONCAT(UPPER(a.nombres),CONCAT(' ',UPPER(a.apellidos))) like '%$filtro%' OR UPPER(a.razonSocial) like UPPER('%$filtro%') )");                
		$datos = $query->getResult();
                //echo $query->getSQL();die;
		return $datos;
                
	}

	public function findPersonasPorCriterios($estado,$idEmpresa,$usuario,$fechaDesde,$fechaHasta,$nombre,$apellido,$razonSocial,$tipoPersona,$limit,$page,$start,$idCliente){
                $criterio_fecha_desde='';
                $criterio_fecha_hasta='';
                $nombre=  strtoupper($nombre);
				$apellido=  strtoupper($apellido);
				$razonSocial=  strtoupper($razonSocial);
                $objeto_retorna='a';
                $criterio_cliente='';
                $from_contacto_cli=''; 
                //El id cliente es para consulta de contactos
                if ($idCliente)
                {
                    $tipoPersona='Cliente';
                    $objeto_retorna='g';                    
                    $from_contacto_cli=" ,schemaBundle:InfoPersonaContacto f, schemaBundle:InfoPersona g ";
                    $criterio_cliente=" a.id=f.personaId AND f.contactoId=g.id AND f.personaId=$idCliente AND";
                }                  
                if ($fechaDesde){
                    $fechaD = date("Y/m/d", strtotime($fechaDesde));			
                    $fechaDesde = $fechaD ;
                    $criterio_fecha_desde=" $objeto_retorna.feCreacion >= '$fechaDesde' AND ";
                }
                if ($fechaHasta){
                    $fechaH = date("Y/m/d", strtotime($fechaHasta));			
                    $fechaHasta = $fechaH;
                    $criterio_fecha_hasta=" $objeto_retorna.feCreacion <= '$fechaHasta' AND ";
                }
                $criterio_estado='';
                $criterio_nombre='';
				$criterio_apellido='';
				$criterio_razonSocial='';
                $criterio_usuario='';
                if ($usuario){       
                    $criterio_usuario="UPPER($objeto_retorna.usrCreacion) = UPPER('$usuario') AND ";
                }                 
                if ($estado){       
                    $criterio_estado="UPPER($objeto_retorna.estado) = UPPER('$estado') AND ";
                }    
                if ($nombre){       
                    $criterio_nombre=" UPPER($objeto_retorna.nombres) like UPPER('%$nombre%') AND ";
                }  
                if ($apellido){       
                    $criterio_apellido=" UPPER($objeto_retorna.apellidos) like UPPER('%$apellido%') AND ";
                } 
                if ($razonSocial){       
                    $criterio_razonSocial=" UPPER($objeto_retorna.razonSocial) like UPPER('%$razonSocial%') AND ";
                } 				
		$query = $this->_em->createQuery("SELECT $objeto_retorna
		FROM 
                schemaBundle:InfoPersona a, schemaBundle:InfoPersonaEmpresaRol b, 
                schemaBundle:InfoEmpresaRol c, schemaBundle:AdmiRol d, schemaBundle:AdmiTipoRol e $from_contacto_cli
		WHERE 
                a.id=b.personaId AND
                b.empresaRolId=c.id AND
                c.rolId=d.id AND
                d.tipoRolId=e.id AND
                $criterio_cliente
                $criterio_estado
                $criterio_nombre
				$criterio_apellido
				$criterio_razonSocial
                $criterio_usuario    
                $criterio_fecha_desde
                $criterio_fecha_hasta
                c.empresaCod='$idEmpresa' AND LOWER(e.descripcionTipoRol)=LOWER('$tipoPersona')  
                    order by a.feCreacion DESC");
                //echo $query->getSQL();die;
                $total=count($query->getResult());
		$datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
                $resultado['registros']=$datos;
                $resultado['total']=$total;
		return $resultado;
	} 


	public function findReferidosPorCriterios($estado,$idEmpresa,$usuario,$fechaDesde,$fechaHasta,$nombre,$apellido,$razonSocial,$tipoPersona,$limit,$page,$start){
                $criterio_fecha_desde='';
                $criterio_fecha_hasta='';
                $nombre=  strtoupper($nombre);
                $objeto_retorna='refe';
                if ($fechaDesde){
                    $fechaD = date("Y/m/d", strtotime($fechaDesde));			
                    $fechaDesde = $fechaD ;
                    $criterio_fecha_desde=" p1.feCreacion >= '$fechaDesde' AND ";
                }
                if ($fechaHasta){
                    $fechaH = date("Y/m/d", strtotime($fechaHasta));			
                    $fechaHasta = $fechaH;
                    $criterio_fecha_hasta=" p1.feCreacion <= '$fechaHasta' AND ";
                }
                $criterio_estado='';
                $criterio_nombre='';
                $criterio_apellido='';
                $criterio_razonSocial='';				
                $criterio_usuario='';
                if ($usuario){       
                    $criterio_usuario="UPPER(p1.usrCreacion) = UPPER('$usuario') AND ";
                }                 
                if ($estado!='null' && $estado!=''&&$estado!=null){       
                    $criterio_estado="UPPER(p1.estado) = UPPER('$estado') AND ";
                }    
                if ($nombre){       
                    $criterio_nombre=" UPPER(p1.nombres) like UPPER('%$nombre%') AND ";
                } 
                if ($apellido){       
                    $criterio_apellido=" UPPER(p1.apellidos) like UPPER('%$apellido%') AND ";
                }  				
                if ($razonSocial){       
                    $criterio_razonSocial=" UPPER(p1.razonSocial) like UPPER('%$razonSocial%') AND ";
                }  				
		$query = $this->_em->createQuery("SELECT 
                    p1.id as idCliente, p1.estado,p1.razonSocial,p1.nombres, p1.apellidos,
                    p1.feCreacion,p1.usrCreacion,p1.direccion,per.id as id
		FROM 
                schemaBundle:InfoPersonaReferido refe, 
				schemaBundle:InfoPersona p1, 
				schemaBundle:InfoPersonaEmpresaRol per,
				schemaBundle:InfoEmpresaRol empr
		WHERE 
                p1.id=refe.referidoId AND
				refe.personaEmpresaRolId=per.id AND
				per.empresaRolId=empr.id AND
                $criterio_estado
                $criterio_nombre
                $criterio_apellido
                $criterio_razonSocial				
                $criterio_usuario    
                $criterio_fecha_desde
                $criterio_fecha_hasta
                empr.empresaCod='$idEmpresa'  
                order by refe.feCreacion DESC");
		//echo "SQL:".$query->getSQL();
                $total=count($query->getResult());
		$datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
                $resultado['registros']=$datos;
                $resultado['total']=$total;
		return $resultado;
	} 

    /**
     * Documentación para el método 'findPersonaPorIdentificacion'.
     * 
     * Consulta las Info_Personas filtrados por el Tipo de Identificación y el Número de Identificación de la Persona
     * sin considerar el campo estado.
     * 
     * @return Array lista de Info_Persona.
     * 
     * @param String $strTipoIdentificacion Tipo de la identifación de la persona
     * @param String $strIdentificacion     Número de identifación de la persona.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 01-12-2015
     * @since 1.0
     */
    public function findPersonaPorIdentificacion($strTipoIdentificacion, $strIdentificacion)
    {
        $sqlQuery = " SELECT a
                      FROM  schemaBundle:InfoPersona a 
                      WHERE a.tipoIdentificacion    =   :TIPOIDENTIFICACION
                        AND a.identificacionCliente =   :IDENTIFICACION ";
        $objQuery = $this->_em->createQuery($sqlQuery);
        $objQuery->setParameter('TIPOIDENTIFICACION', $strTipoIdentificacion);
        $objQuery->setParameter('IDENTIFICACION',     $strIdentificacion);
        return $objQuery->getResult();
    }

    /**
     * Funcion que verifica si existe Rol de Cliente por numero de identificacion y por Empresa
     * 
     * @author Unknow
     * @version 1.0 Unknow
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.1
     * Se elimina inyeccion sql y se valida por estado el Rol de CLIENTE considerando solo estado Activo
     * @param string $identificacion
     * @param string $idEmpresa     
     * @return object
     */
	public function findClientesPorIdentificacion($identificacion,$idEmpresa)
    {	

        $query = $this->_em->createQuery("SELECT a
		FROM 
                schemaBundle:InfoPersona a, schemaBundle:InfoPersonaEmpresaRol b,
                schemaBundle:InfoEmpresaRol c, schemaBundle:AdmiRol d, schemaBundle:AdmiTipoRol e
		WHERE 
                a.id=b.personaId AND
                b.empresaRolId=c.id AND                
                c.rolId=d.id AND
                d.tipoRolId=e.id AND
                b.estado = 'Activo' AND
                UPPER(e.descripcionTipoRol) = UPPER('Cliente') AND
                a.identificacionCliente =:identificacion AND
                c.empresaCod=:idEmpresa
                 ");
        $query->setParameter('identificacion', $identificacion);
        $query->setParameter('idEmpresa', $idEmpresa);
		$datos = $query->getResult();
		return $datos;
	}
        
       /* public function suma_fechas($fecha,$ndias)
        {
                $anio='';$mes='';$dia='';
            if (preg_match("/([0-9][0-9]){1,2}-[0-9]{1,2}-[0-9]{1,2}/",$fecha))
                    list($anio,$mes,$dia)=split("-",$fecha);
            $nueva = mktime(0,0,0, $mes,$dia,$anio) + $ndias * 24 * 60 * 60;
            $nuevafecha=date("Y-m-d",$nueva);
            return ($nuevafecha); 
        }*/
    /**
     * Función: getPersonaParaSession
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.1 11-01-2018 Se modifica consulta por estado.
     *
     * @author telcos
     * @version 1.0 
     * 
     **/     
    public function getPersonaParaSession($codEmpresa, $idPersona, $nombreTipoRol="Cliente")
    { 
        $persona = array();
        
		$sql = "SELECT b
				FROM 
					schemaBundle:InfoPersona a, schemaBundle:InfoPersonaEmpresaRol b,
					schemaBundle:InfoEmpresaRol c, schemaBundle:AdmiRol d, schemaBundle:AdmiTipoRol e
				WHERE 
					a.id=b.personaId
					AND b.empresaRolId=c.id
					AND c.rolId=d.id 
					AND d.tipoRolId=e.id  
					AND UPPER(e.descripcionTipoRol) = UPPER('$nombreTipoRol') 
					AND a.id = '$idPersona'  
					AND c.empresaCod='$codEmpresa' 
					AND (lower(b.estado) = lower('Pendiente') OR lower(b.estado) = lower('Activo') OR lower(b.estado) = lower('Modificado') 
                    OR   lower(b.estado) = lower('Pend-convertir') ". 
					//" OR lower(b.estado) = lower('Cancel') OR lower(b.estado) = lower('Cancelado') ".
					")".
					" AND (lower(c.estado) = lower('Activo') OR lower(c.estado) = lower('Modificado') OR lower(c.estado) = lower('Pend-convertir') ". 
					//" OR lower(c.estado) = lower('Cancel') OR lower(c.estado) = lower('Cancelado')". 
                 ")";
		$query = $this->_em->createQuery($sql);
			//echo $query->getSQL();die;	 
       // $sql = 'SELECT p FROM schemaBundle:InfoPersona p WHERE p.id = :InfoPersona ';
        //$query = $this->_em->createQuery($sql)->setMaxResults(1)->setParameter('InfoPersona',$idPersona);	
        $entity =  $query->getOneOrNullResult();

        //BUSCAR OFICINA Y EMPRESA
        $oficinaEmpresaGrupo = $this->getOficinaParaSession($codEmpresa, $idPersona, $nombreTipoRol);
        $persona = $oficinaEmpresaGrupo;
        
        if($entity && count($entity)>0)
        {
            $entityPersona = $entity->getPersonaId();
			
            $persona['id'] = $entityPersona->getId();
            $persona['id_persona_empresa_rol'] = $entity->getId();
            $persona['id_persona'] = $entityPersona->getId();
            $persona['razon_social'] = $entityPersona->getRazonSocial();
            $persona['nombres'] = $entityPersona->getNombres();
            $persona['apellidos'] = $entityPersona->getApellidos();
            $persona['identificacion'] = $entityPersona->getIdentificacionCliente();
            $persona['direccion'] = $entityPersona->getDireccion();
            $persona['estado'] = $entityPersona->getEstado();
        }
        else
        {
            $persona['id']  = "";
            $persona['id_persona_empresa_rol']  = "";
            $persona['id_persona']  = "";
            $persona['razon_social']  = "";
            $persona['nombres']  = "";
            $persona['apellidos']  = "";
            $persona['identificacion']  = "";
            $persona['direccion']  = "";
            $persona['estado']  = "";
        }

        return $persona;
    }

    public function getOficinaParaSession($codEmpresa, $idPersona, $nombreTipoRol="Cliente")
    {
        $sql = "SELECT og.id as IdOficina, og.nombreOficina, eg.id as CodEmpresa, eg.nombreEmpresa,
					   r.id as IdRol, r.descripcionRol, tr.id as IdTipoRol, tr.descripcionTipoRol 
                FROM 
                    schemaBundle:InfoOficinaGrupo og, 
                    schemaBundle:InfoPersonaEmpresaRol per, 
                    schemaBundle:InfoEmpresaRol er,
                    schemaBundle:InfoEmpresaGrupo eg,
                    schemaBundle:AdmiRol r,
                    schemaBundle:AdmiTipoRol tr  
                WHERE 
                    og.id = per.oficinaId 
					AND er.id = per.empresaRolId 
					AND eg.id = er.empresaCod 
					AND r.id = er.rolId 
					AND tr.id = r.tipoRolId 
					AND lower(tr.descripcionTipoRol) = lower('$nombreTipoRol') 
                    AND eg.id = '$codEmpresa' AND per.personaId = '$idPersona' 
					AND (lower(per.estado) = lower('Activo') OR lower(per.estado) = lower('Modificado') OR lower(per.estado) = lower('Cancel') OR lower(per.estado) = lower('Cancelado') OR lower(per.estado) = lower('Pend-convertir')) 
					AND (lower(er.estado) = lower('Activo') OR lower(er.estado) = lower('Modificado')) 
                ";

            $query = $this->_em->createQuery($sql)->setMaxResults(1);
			//echo $query->getSQL();die;
            $entity =  $query->getResult();

            $oficina = array();

            if($entity && count($entity)>0)
            {
                $entity = $entity[0];

                $oficina['id_oficina'] = $entity["IdOficina"];
                $oficina['nombre_oficina'] = $entity["nombreOficina"];
                $oficina['id_empresa'] = $entity["CodEmpresa"];
                $oficina['nombre_empresa'] = $entity["nombreEmpresa"];
				
                $oficina['id_rol'] = $entity["IdRol"];
                $oficina['nombre_rol'] = $entity["descripcionRol"];
                $oficina['id_tipo_rol'] = $entity["IdTipoRol"];
                $oficina['nombre_tipo_rol'] = $entity["descripcionTipoRol"];
            }
            else
            {
                $oficina['id_oficina']  = "";
                $oficina['nombre_oficina']  = "";
                $oficina['id_empresa']  = "";
                $oficina['nombre_empresa']  = "";
				
                $oficina['id_rol']  = "";
                $oficina['nombre_rol']  = "";
                $oficina['id_tipo_rol']  = "";
                $oficina['nombre_tipo_rol']  = "";
            }
        return $oficina;
    }    
    
    public function getDepartamentoByEmpleado($codEmpresa, $idPersona, $nombreTipoRol="Empleado")
    {
		/*schemaBundle:InfoOficinaGrupo og,
                    og.id = per.oficinaId AND  */
        $sql = "SELECT d.id, d.nombreDepartamento 
                FROM 
                    schemaBundle:InfoPersonaEmpresaRol per, 
                    schemaBundle:InfoEmpresaRol er,
                    schemaBundle:InfoEmpresaGrupo eg,
                    schemaBundle:AdmiRol r, 
                    schemaBundle:AdmiTipoRol tr, 
                    schemaBundle:AdmiDepartamento d                    
                WHERE 
                    er.id = per.empresaRolId 
					AND d.id = per.departamentoId 
					AND eg.id = er.empresaCod
					AND r.id = er.rolId 
					AND tr.id = r.tipoRolId 
					AND lower(tr.descripcionTipoRol) = lower('$nombreTipoRol') 
                    AND eg.id = '$codEmpresa' AND per.personaId = '$idPersona' 
					AND (lower(per.estado) = lower('Activo') OR lower(per.estado) = lower('Modificado')) 
					AND (lower(er.estado) = lower('Activo') OR lower(er.estado) = lower('Modificado')) 
                ";

            $query = $this->_em->createQuery($sql)->setMaxResults(1);			
            $entity =  $query->getResult();

            $departamento = array();

            if($entity && count($entity)>0)
            {
                $entity = $entity[0];

                $departamento['id_departamento'] = $entity["id"];
                $departamento['nombre_departamento'] = $entity["nombreDepartamento"];
            }
            else
            {
                $departamento['id_departamento']  = 0;
                $departamento['nombre_departamento']  = "";
            }
        return $departamento;
    }
    
    public function findListadoClientesPorEmpresaPorEstadoPorUsuario($estado,$idEmpresa){	    
		$query = $this->_em->createQuery("SELECT a
		FROM 
                schemaBundle:InfoPersona a, schemaBundle:InfoPersonaEmpresaRol b, 
                schemaBundle:InfoEmpresaRol c, schemaBundle:AdmiRol d, schemaBundle:AdmiTipoRol e
		WHERE 
                a.id=b.personaId AND
                b.empresaRolId=c.id AND
                c.rolId=d.id AND
                d.tipoRolId=e.id AND
                a.estado = '$estado' AND 
                c.empresaCod='$idEmpresa' AND e.descripcionTipoRol='Cliente'");
                //echo $query->getSQL();die;
		$datos = $query->getResult();
		return $datos;
	}
    /**
    * Función que retorna listado de clientes
    *
    * @param mixed $arrayEstado estados que no deben tomarse en cuenta.
    * @param string $strCodEmpresa codigo de la empresa en sesión.
    * @param string $strCliente nombre del cliente.
    * @param mixed $arrayRolesPersona roles permitidos.
    *
    * @return mixed $resultados Retorna listado de clientes.
    *       
    * @author Edgar Holguin <eholguintelconet.ec>
    * @version 1.1 02-12-2014
    */ 	
    public function findListadoClientesPorEmpresaPorEstado($arrayEstado,$strCodEmpresa,$strCliente,$arrayRolesPersona){
        $sql="SELECT 
                  a
              FROM 
                  schemaBundle:InfoPersona a, 
		  schemaBundle:InfoPersonaEmpresaRol b, 
		  schemaBundle:InfoEmpresaRol c, 
		  schemaBundle:AdmiRol d, 
		  schemaBundle:AdmiTipoRol e
	      WHERE 
		  a.id=b.personaId AND
		  b.empresaRolId=c.id AND
		  c.rolId=d.id AND
		  d.tipoRolId=e.id AND
		  b.estado not in (:estado) AND 
		  c.empresaCod=:codEmpresa AND 
		  UPPER(e.descripcionTipoRol)  in(:rolesPersona) ";
        if($strCliente!="")
        {
            $sql.=" AND (CONCAT(UPPER(a.nombres),CONCAT(' ',UPPER(a.apellidos))) like UPPER(:cliente) OR UPPER(a.razonSocial) like UPPER(:cliente))";
        }else
            $sql.="";
        
        $query = $this->_em->createQuery($sql);        
        $query->setParameter('estado',$arrayEstado);
        $query->setParameter('codEmpresa',$strCodEmpresa);
        $query->setParameter('rolesPersona',$arrayRolesPersona);
        if($strCliente!=""){
            $query->setParameter('cliente','%'.$strCliente.'%');
        }
        $datos = $query->getResult();
        return $datos;
    }
    /**
    * Función que retorna listado de clientes asociados al punto cliente
    *
    * @param mixed   $arrayEstado estados que no deben tomarse en cuenta.
    * @param string  $strCodEmpresa codigo de la empresa en sesión.
    * @param string  $strCliente nombre del cliente.
    * @param mixed   $arrayRolesPersona roles permitidos.
    * @param integer $idPunto  identificador del punto cliente seleccionado.
    *
    * @return mixed $resultados Retorna listado de clientes asociados al punto.
    *       
    * @author Edgar Holguin <eholguin@telconet.ec>
    * @version 1.0 02-12-2014
    */ 	    
    public function findListadoClientesPorEmpresaPunto($arrayEstado,$strCodEmpresa,$strCliente,$arrayRolesPersona,$idPunto){

        $sql="SELECT 
	          a
	      FROM 
	          schemaBundle:InfoPersona a, 
		  schemaBundle:InfoPersonaEmpresaRol b, 
		  schemaBundle:InfoEmpresaRol c, 
		  schemaBundle:AdmiRol d, 
		  schemaBundle:AdmiTipoRol e,
		  schemaBundle:InfoPunto p
	      WHERE 
		  p.id = (:punto) AND 
		  b.id = p.personaEmpresaRolId AND 
		  a.id = b.personaId AND
		  c.id = b.empresaRolId AND
		  d.id = c.rolId AND
		  e.id = d.tipoRolId AND        
		  b.estado not in (:estado) AND
		  c.empresaCod=:codEmpresa ";

        if($strCliente!=""){
            $sql.=" AND (CONCAT(UPPER(a.nombres),CONCAT(' ',UPPER(a.apellidos))) like UPPER(:cliente) OR UPPER(a.razonSocial) like UPPER(:cliente))";
        }else
            $sql.="";
        
        $query = $this->_em->createQuery($sql);        
        $query->setParameter('estado',$arrayEstado);
        $query->setParameter('codEmpresa',$strCodEmpresa);
        $query->setParameter('punto',$idPunto);
        if($strCliente!=""){
            $query->setParameter('cliente','%'.$strCliente.'%');
        }

        $datos = $query->getResult();
        return $datos;
    }      
	
	public function findBuscarPersona($estado,$idEmpresa,$login)
	{
		$query = $this->_em->createQuery("SELECT b
		FROM 
                schemaBundle:InfoPersona a, schemaBundle:InfoPersonaEmpresaRol b, 
                schemaBundle:InfoEmpresaRol c
		WHERE 
                a.id=b.personaId AND
                b.empresaRolId=c.id AND
                lower(a.login)=lower('$login') AND
                a.estado <> '$estado' AND
                c.empresaCod='$idEmpresa'");
		//echo $query->getSQL();
		$datos = $query->getResult();
		return $datos;
	}
    
	
    public function getMiniPersonaPorLogin($login)
    {   
        $query = "	SELECT p.id, p.login, p.nombres, p.apellidos, p.razonSocial, p.estado 
					FROM schemaBundle:InfoPersona p 
					WHERE lower(p.login) = lower('$login') ";

        return $this->_em->createQuery($query)->getResult();
    }

    public function getOficinaPorLoginPorTipoRol($login,$tipo_rol){
        $qb = $this->_em->createQueryBuilder()
                        ->select('info_oficina_grupo')
                        ->from('schemaBundle:InfoPersona','info_persona')
                        ->from('schemaBundle:InfoPersonaEmpresaRol','info_persona_empresa_rol')
                        ->from('schemaBundle:InfoEmpresaRol','info_empresa_rol')
                        ->from('schemaBundle:InfoEmpresaGrupo','info_empresa_grupo')
                        ->from('schemaBundle:AdmiRol','admi_rol')
                        ->from('schemaBundle:AdmiTipoRol','admi_tipo_rol')
                        ->from('schemaBundle:InfoOficinaGrupo','info_oficina_grupo')
                        ->where( "info_persona = info_persona_empresa_rol.personaId")
                        ->andWhere( "info_empresa_rol = info_persona_empresa_rol.empresaRolId")
                        ->andWhere( "info_empresa_rol.empresaCod = info_empresa_grupo")
                        ->andWhere( "info_empresa_rol.rolId = admi_rol")
                        ->andWhere( "admi_rol.tipoRolId = admi_tipo_rol")
                        ->andWhere( "info_oficina_grupo = info_persona_empresa_rol.oficinaId")
                        ->andWhere( "info_persona.login = ?1")
                        ->andWhere( "admi_tipo_rol.descripcionTipoRol = ?2")
                        ->setParameter(1, $login)
                        ->setParameter(2, $tipo_rol);
        
        $query = $qb->getQuery();
        $oficina = $query->getOneOrNullResult();
        if($oficina)
            return $oficina->getNombreOficina();
        else
            return "";
        
	}
        
        public function findProspectosAgrupadosPorTipoEmpresa($idEmpresa,$feIni,$feFin){
                //echo $feIni.' '.$feFin;
                $query = $this->_em->createQuery(
                "SELECT a.tipoEmpresa as tipoEmpresa, count(a.id) AS total
		FROM 
                schemaBundle:InfoPersona a, schemaBundle:InfoPersonaEmpresaRol b,
                schemaBundle:InfoEmpresaRol c, schemaBundle:AdmiRol d, schemaBundle:AdmiTipoRol e
		WHERE 
                a.id=b.personaId AND
                b.empresaRolId=c.id AND                
                c.rolId=d.id AND
                d.tipoRolId=e.id AND
                a.estado = 'Activo' AND
                lower(e.descripcionTipoRol) = lower('Pre-cliente') AND
                c.empresaCod='$idEmpresa' AND
                a.feCreacion >= :fechaIni AND a.feCreacion <= :fechaFin     
                 GROUP BY a.tipoEmpresa")
		->setParameter('fechaIni',new \DateTime($feIni))
		->setParameter('fechaFin',new \DateTime($feFin))                        
                ;
                //echo $query->getSQL(); die;
		$datos = $query->getResult();
		return $datos;           
        }        
        


		
	//REPORTES DE ADMINISTRACION --  VER EMPLEADOS	
    public function generarJsonPersonaEmpleado($nombres, $apellidos, $identificacion, $estado,$start,$limit)
    {
        $arr_encontrados = array();
        
        $registrosTotal = $this->getRegistrosPersonaEmpleado($nombres, $apellidos, $identificacion, $estado, '', '');
        $registros = $this->getRegistrosPersonaEmpleado($nombres, $apellidos, $identificacion, $estado, $start, $limit);
 
        if ($registros) {
            $num = count($registrosTotal);            
            foreach ($registros as $data)
            {					
                $arr_encontrados[]=array('id_persona' =>$data->getPersonaId()->getId(),
										 'id_persona_empresa_rol' =>$data->getId(),
										 'id_empresa' =>$data->getEmpresaRolId()->getEmpresaCod()->getId(),
										 'nombre_empresa' =>($data->getEmpresaRolId()->getEmpresaCod()->getNombreEmpresa() ? $data->getEmpresaRolId()->getEmpresaCod()->getNombreEmpresa() : ""),
                                         'tipo_identificacion' => ($data->getPersonaId()->getTipoIdentificacion() == "CED" ? "Cedula" : $data->getPersonaId()->getTipoIdentificacion()),
                                         'identificacion' => ($data->getPersonaId()->getIdentificacionCliente() ? $data->getPersonaId()->getIdentificacionCliente() : 0),
                                         'nombres' =>($data->getPersonaId()->getNombres()?$data->getPersonaId()->getNombres():""),
                                         'apellidos' =>($data->getPersonaId()->getApellidos()?$data->getPersonaId()->getApellidos():""),
                                         'nacionalidad' => ($data->getPersonaId()->getNacionalidad() == "EXT" ? "Extranjera" : ($data->getPersonaId()->getNacionalidad() == "NAC" ? "Nacional" : "")),
                                         'direccion' =>($data->getPersonaId()->getDireccion() ? $data->getPersonaId()->getDireccion() : ""),
                                         'estado' =>(strtolower(trim($data->getPersonaId()->getEstado()))==strtolower('ELIMINADO') ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show'
										 );
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_banco_tipo_cuenta' => 0 , 'id_banco' => 0 , 'id_tipo_cuenta' => 0 , 
                                                        'descripcion_banco' => 'Ninguno', 'descripcion_tipo_cuenta' => 'Ninguno', 
                                                        'total_caracteres'=>0, 'total_codseguridad'=>0, 'caracter_empieza'=>0, 
                                                        'es_tarjeta'=>'NO', 
                                                        'banco_id' => 0 , 'banco_nombre' => 'Ninguno', 'estado' => 'Ninguno'));
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
    
    public function getRegistrosPersonaEmpleado($nombres, $apellidos, $identificacion, $estado, $start,$limit){
        $boolBusqueda = false; 
        $where = "";
        if($nombres!=""){
            $boolBusqueda = true;
            $where .= "AND LOWER(p.nombres) like LOWER('%$nombres%') ";
        }
        if($apellidos!=""){
            $boolBusqueda = true;
            $where .= "AND LOWER(p.apellidos) like LOWER('%$apellidos%') ";
        }
        if($identificacion!=""){
            $boolBusqueda = true;
            $where .= "AND p.identificacionCliente like '%$identificacion%' ";
        }

        if($estado!=""){
			if($estado!="Todos"){
				$boolBusqueda = true;
				if($estado=="Activo"){
					$where .= "AND LOWER(p.estado) not like LOWER('Eliminado') ";
				}
				else{
					$where .= "AND LOWER(p.estado) like LOWER('$estado') ";
				}
			}
		}
		
        $tipoRol = "Empleado";
		
        $sql = "SELECT per  
                FROM 
					schemaBundle:InfoPersona p, 
					schemaBundle:InfoPersonaEmpresaRol per, 
					schemaBundle:InfoEmpresaRol er,
					schemaBundle:InfoEmpresaGrupo eg,
					schemaBundle:AdmiRol r, 
					schemaBundle:AdmiTipoRol tr 
                WHERE 
					p.id = per.personaId AND 
					er.id = per.empresaRolId AND
					eg.id = er.empresaCod AND
					r.id = er.rolId AND
					tr.id = r.tipoRolId AND               
					lower(tr.descripcionTipoRol) = lower('$tipoRol') 
					$where
				
                ORDER BY p.nombres, p.apellidos 
                ";   
        
        $query = $this->_em->createQuery($sql);
        
        $datos = null;
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

	
	//REPORTES DE ADMINISTRACION --  VER PROVEEDORES	
    public function generarJsonPersonaProveedor($razon_social, $representante_legal, $identificacion, $tipo_empresa, $estado,$start,$limit)
    {
        $arr_encontrados = array();
        
        $registrosTotal = $this->getRegistrosPersonaProveedor($razon_social, $representante_legal, $identificacion, $tipo_empresa, $estado, '', '');
        $registros = $this->getRegistrosPersonaProveedor($razon_social, $representante_legal, $identificacion, $tipo_empresa, $estado, $start, $limit);
 
        if ($registros) {
            $num = count($registrosTotal);            
            foreach ($registros as $data)
            {															 
                $arr_encontrados[]=array('id_persona' =>$data->getPersonaId()->getId(),
										 'id_persona_empresa_rol' =>$data->getId(),
										 'id_empresa' =>$data->getEmpresaRolId()->getEmpresaCod()->getId(),
										 'nombre_empresa' =>($data->getEmpresaRolId()->getEmpresaCod()->getNombreEmpresa() ? $data->getEmpresaRolId()->getEmpresaCod()->getNombreEmpresa() : ""),
                                         'tipo_identificacion' => ($data->getPersonaId()->getTipoIdentificacion() == "CED" ? "Cedula" : $data->getPersonaId()->getTipoIdentificacion()),
                                         'identificacion' => ($data->getPersonaId()->getIdentificacionCliente() ? $data->getPersonaId()->getIdentificacionCliente() : 0),										 
										 'tipo_empresa' =>($data->getPersonaId()->getTipoEmpresa() ? $data->getPersonaId()->getTipoEmpresa() : ""),
                                         'razon_social' =>($data->getPersonaId()->getRazonSocial() ? $data->getPersonaId()->getRazonSocial() : ""),
                                         'representante_legal' =>($data->getPersonaId()->getRepresentanteLegal() ? $data->getPersonaId()->getRepresentanteLegal() : ""),										 
                                         'nacionalidad' => ($data->getPersonaId()->getNacionalidad() == "EXT" ? "Extranjera" : ($data->getPersonaId()->getNacionalidad() == "NAC" ? "Nacional" : "")),
                                         'direccion' =>($data->getPersonaId()->getDireccion() ? $data->getPersonaId()->getDireccion() : ""),
                                         'estado' =>(strtolower(trim($data->getPersonaId()->getEstado()))==strtolower('ELIMINADO') ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show'
										 );
										 
										 
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_banco_tipo_cuenta' => 0 , 'id_banco' => 0 , 'id_tipo_cuenta' => 0 , 
                                                        'descripcion_banco' => 'Ninguno', 'descripcion_tipo_cuenta' => 'Ninguno', 
                                                        'total_caracteres'=>0, 'total_codseguridad'=>0, 'caracter_empieza'=>0, 
                                                        'es_tarjeta'=>'NO', 
                                                        'banco_id' => 0 , 'banco_nombre' => 'Ninguno', 'estado' => 'Ninguno'));
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
    
    public function getRegistrosPersonaProveedor($razon_social, $representante_legal, $identificacion, $tipo_empresa, $estado, $start,$limit){        
        $boolBusqueda = false; 
        $where = "";
        if($razon_social!=""){
            $boolBusqueda = true;
            $where .= "AND LOWER(p.razonSocial) like LOWER('%$razon_social%') ";
        }
        if($representante_legal!=""){
            $boolBusqueda = true;
            $where .= "AND LOWER(p.representanteLegal) like LOWER('%$representante_legal%') ";
        }
        if($identificacion!=""){
            $boolBusqueda = true;
            $where .= "AND p.identificacionCliente like '%$identificacion%' ";
        }
		
        if($tipo_empresa!=""){
			if($tipo_empresa!="Todos"){
				$boolBusqueda = true;
				$where .= "AND LOWER(p.tipoEmpresa) like LOWER('%$tipo_empresa%') ";
			}
        }
		
        if($estado!=""){
			if($estado!="Todos"){
				$boolBusqueda = true;
				if($estado=="Activo"){
					$where .= "AND LOWER(p.estado) not like LOWER('Eliminado') ";
				}
				else{
					$where .= "AND LOWER(p.estado) like LOWER('$estado') ";
				}
			}
		}
        
        $tipoRol = "Proveedor";
		
        $sql = "SELECT per 
                FROM 
					schemaBundle:InfoPersona p, 
					schemaBundle:InfoPersonaEmpresaRol per, 
					schemaBundle:InfoEmpresaRol er,
					schemaBundle:InfoEmpresaGrupo eg,
					schemaBundle:AdmiRol r, 
					schemaBundle:AdmiTipoRol tr 
                WHERE 
					p.id = per.personaId AND 
					er.id = per.empresaRolId AND
					eg.id = er.empresaCod AND
					r.id = er.rolId AND
					tr.id = r.tipoRolId AND               
					lower(tr.descripcionTipoRol) = lower('$tipoRol')
					$where
				
                ORDER BY p.razonSocial 
                ";   
        
        $query = $this->_em->createQuery($sql);
        
        $datos = null;
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

	
	//REPORTES DE ADMINISTRACION --  VER AGENCIAS	
    public function generarJsonAgencias($nombres, $apellidos, $identificacion, $estado,$start,$limit)
    {
        $arr_encontrados = array();
        
        $registrosTotal = $this->getRegistrosAgencias($nombres, $apellidos, $identificacion, $estado, '', '');
        $registros = $this->getRegistrosAgencias($nombres, $apellidos, $identificacion, $estado, $start, $limit);
 
        if ($registros) {
            $num = count($registrosTotal);            
            foreach ($registros as $data)
            {					
                $arr_encontrados[]=array('id_persona' =>$data->getPersonaId()->getId(),
										 'id_persona_empresa_rol' =>$data->getId(),
										 'id_empresa' =>$data->getEmpresaRolId()->getEmpresaCod()->getId(),
										 'nombre_empresa' =>($data->getEmpresaRolId()->getEmpresaCod()->getNombreEmpresa() ? $data->getEmpresaRolId()->getEmpresaCod()->getNombreEmpresa() : ""),
                                         'tipo_identificacion' => ($data->getPersonaId()->getTipoIdentificacion() == "CED" ? "Cedula" : $data->getPersonaId()->getTipoIdentificacion()),
                                         'identificacion' => ($data->getPersonaId()->getIdentificacionCliente() ? $data->getPersonaId()->getIdentificacionCliente() : 0),
                                         'nombres' =>($data->getPersonaId()->getNombres()?$data->getPersonaId()->getNombres():""),
                                         'apellidos' =>($data->getPersonaId()->getApellidos()?$data->getPersonaId()->getApellidos():""),
                                         'nacionalidad' => ($data->getPersonaId()->getNacionalidad() == "EXT" ? "Extranjera" : ($data->getPersonaId()->getNacionalidad() == "NAC" ? "Nacional" : "")),
                                         'direccion' =>($data->getPersonaId()->getDireccion() ? $data->getPersonaId()->getDireccion() : ""),
                                         'estado' =>(strtolower(trim($data->getPersonaId()->getEstado()))==strtolower('ELIMINADO') ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-edit'),
                                         'action3' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-delete')
										 );
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_banco_tipo_cuenta' => 0 , 'id_banco' => 0 , 'id_tipo_cuenta' => 0 , 
                                                        'descripcion_banco' => 'Ninguno', 'descripcion_tipo_cuenta' => 'Ninguno', 
                                                        'total_caracteres'=>0, 'total_codseguridad'=>0, 'caracter_empieza'=>0, 
                                                        'es_tarjeta'=>'NO', 
                                                        'banco_id' => 0 , 'banco_nombre' => 'Ninguno', 'estado' => 'Ninguno'));
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
    
    public function getRegistrosAgencias($nombres, $apellidos, $identificacion, $estado, $start,$limit){
        $boolBusqueda = false; 
        $where = "";
        if($nombres!=""){
            $boolBusqueda = true;
            $where .= "AND LOWER(p.nombres) like LOWER('%$nombres%') ";
        }
        if($apellidos!=""){
            $boolBusqueda = true;
            $where .= "AND LOWER(p.apellidos) like LOWER('%$apellidos%') ";
        }
        if($identificacion!=""){
            $boolBusqueda = true;
            $where .= "AND p.identificacionCliente like '%$identificacion%' ";
        }

        if($estado!=""){
			if($estado!="Todos"){
				$boolBusqueda = true;
				if($estado=="Activo"){
					$where .= "AND LOWER(p.estado) not like LOWER('Eliminado') ";
				}
				else{
					$where .= "AND LOWER(p.estado) like LOWER('$estado') ";
				}
			}
		}
		
        $tipoRol = "Agencias";
		
        $sql = "SELECT per  
                FROM 
					schemaBundle:InfoPersona p, 
					schemaBundle:InfoPersonaEmpresaRol per, 
					schemaBundle:InfoEmpresaRol er,
					schemaBundle:InfoEmpresaGrupo eg,
					schemaBundle:AdmiRol r, 
					schemaBundle:AdmiTipoRol tr 
                WHERE 
					p.id = per.personaId AND 
					er.id = per.empresaRolId AND
					eg.id = er.empresaCod AND
					r.id = er.rolId AND
					tr.id = r.tipoRolId AND               
					lower(tr.descripcionTipoRol) = lower('$tipoRol') 
					$where
				
                ORDER BY p.nombres, p.apellidos 
                ";   
        
        $query = $this->_em->createQuery($sql);
        
        $datos = null;
        if($start!='' && !$boolBusqueda && $limit!='')
        {
            $datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
        }
        else if($start!='' && !$boolBusqueda && $limit=='')
        {
            $datos = $query->setFirstResult($start)->getResult();
        }
        else if(($start=='' || $boolBusqueda) && $limit!='')
        {   
            $datos = $query->setMaxResults($limit)->getResult();
        }
        else
        {
            $datos = $query->getResult();
        }
        
        return $datos;
    }

    /******************************
     *** FUNCIONES PARA MOBIL *****
     ******************************/
    /**
     * Funcion que sirve para obtener los datos y departamento del usuario por empresa
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 9-06-2015
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 11-01-2017   Se agrega el retorno de la cedula de la persona consultada
     * 
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.2 26-02-2017 Se recupera el id canton
     * @param $usuario 
     * @param $idEmpresa
     * @return array $resultado (ID_PERSONA, NOMBRES, APELLIDOS, ESTADO_PERSONA, ID_DEPARTAMENTO, 
     *                           NOMBRE_DEPARTAMENTO, EMPRESA_COD, ID_PERSONA_EMPRESA_ROL, ID_OFICINA, NOMBRE_CANTON)
     */
    public function getPersonaDepartamentoPorUserEmpresa($usuario, $idEmpresa)
    {
        $sql = "select 
                    persona.ID_PERSONA ID_PERSONA, 
                    persona.NOMBRES NOMBRES, 
                    persona.APELLIDOS APELLIDOS, 
                    persona.estado ESTADO_PERSONA, 
                    dep.ID_DEPARTAMENTO ID_DEPARTAMENTO, 
                    dep.NOMBRE_DEPARTAMENTO NOMBRE_DEPARTAMENTO, 
                    ier.EMPRESA_COD EMPRESA_COD,
                    iper.ID_PERSONA_ROL ID_PERSONA_EMPRESA_ROL,
                    oficina.ID_OFICINA,
                    canton.NOMBRE_CANTON,
                    canton.ID_CANTON,
                    persona.IDENTIFICACION_CLIENTE IDENTIFICACION_CLIENTE
                from 
                    info_persona persona,
                    INFO_PERSONA_EMPRESA_ROL iper,
                    DB_GENERAL.ADMI_DEPARTAMENTO dep,
                    INFO_EMPRESA_ROL ier,
                    INFO_OFICINA_GRUPO oficina,
                    DB_GENERAL.ADMI_CANTON canton
                where 
                    persona.ID_PERSONA = iper.PERSONA_ID
                and iper.EMPRESA_ROL_ID = ier.ID_EMPRESA_ROL
                and iper.DEPARTAMENTO_ID = dep.ID_DEPARTAMENTO
                and iper.OFICINA_ID = oficina.ID_OFICINA
                and oficina.CANTON_ID = canton.ID_CANTON
                and ier.EMPRESA_COD = :empresaCod
                and persona.login = :usuario
                and iper.estado = :estadoPersona";
        
        $stmt = $this->_em->getConnection()->prepare($sql);
        $stmt->bindValue('empresaCod',      $idEmpresa);
        $stmt->bindValue('estadoPersona',   "Activo");
        $stmt->bindValue('usuario',         $usuario);
        $stmt->execute();
        
        $arraResult = $stmt->fetchAll();
        $resultado  = $arraResult[0];
        
        return $resultado;
    }
    
    /**
     * Funcion que sirve para obtener los datos y departamento del usuario por empresa
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 9-06-2015
     * @param $usuario
     * @return array $resultado (ID_PERSONA, NOMBRES, APELLIDOS, ESTADO_PERSONA, ID_DEPARTAMENTO, 
     *                           NOMBRE_DEPARTAMENTO, EMPRESA_COD, ID_PERSONA_EMPRESA_ROL)
     */
    public function getPersonaDepartamentoPorUser($usuario)
    {
        $sql = "select 
                    persona.ID_PERSONA ID_PERSONA, 
                    persona.NOMBRES NOMBRES, 
                    persona.APELLIDOS APELLIDOS, 
                    persona.estado ESTADO_PERSONA, 
                    dep.ID_DEPARTAMENTO ID_DEPARTAMENTO, 
                    dep.NOMBRE_DEPARTAMENTO NOMBRE_DEPARTAMENTO, 
                    ier.EMPRESA_COD EMPRESA_COD,
                    iper.ID_PERSONA_ROL ID_PERSONA_EMPRESA_ROL
                from 
                    info_persona persona,
                    INFO_PERSONA_EMPRESA_ROL iper,
                    DB_GENERAL.ADMI_DEPARTAMENTO dep,
                    INFO_EMPRESA_ROL ier
                where 
                    persona.ID_PERSONA = iper.PERSONA_ID
                and iper.EMPRESA_ROL_ID = ier.ID_EMPRESA_ROL
                and iper.DEPARTAMENTO_ID = dep.ID_DEPARTAMENTO
                and persona.login = :usuario";
        
        $stmt = $this->_em->getConnection()->prepare($sql);
        $stmt->bindValue('usuario',     $usuario);
        $stmt->execute();
        
        $arraResult = $stmt->fetchAll();
        $resultado  = $arraResult[0];
        
        return $resultado;
    }
        
    /* ******************************************************************************* */
    /* *********************  BUSQUEDA AVANZADA COMERCIAL **************************** */
    /* ******************************************************************************* */
    public function findBusquedaAvanzadaComercial($arrayVariables, $start, $limit){	

        $whereVar = "";
        $fromAdicional = "";
        $whereAdicional = "";
        if($arrayVariables && count($arrayVariables)>0)
        {
            if(isset($arrayVariables["login"]))
            {
                if($arrayVariables["login"] && $arrayVariables["login"]!="")
                {
                    $whereVar .= "AND UPPER(pun.login) like '%".strtoupper(trim($arrayVariables["login"]))."%' ";
                }
            }

            if(isset($arrayVariables["direccion_pto"]))
            {
                if($arrayVariables["direccion_pto"] && $arrayVariables["direccion_pto"]!="")
                {
                    $whereVar .= "AND UPPER(pun.direccion) like '%".strtoupper(trim($arrayVariables["direccion_pto"]))."%' ";
                }
            }

            if(isset($arrayVariables["descripcion_pto"]))
            {
                if($arrayVariables["descripcion_pto"] && $arrayVariables["descripcion_pto"]!="")
                {
                    $whereVar .= "AND UPPER(pun.descripcionPunto) like '%".strtoupper(trim($arrayVariables["descripcion_pto"]))."%' ";
                }
            }

            if(isset($arrayVariables["estados_pto"]))
            {
                if($arrayVariables["estados_pto"] && $arrayVariables["estados_pto"]!="" && $arrayVariables["estados_pto"]!="0")
                {
                    $whereVar .= "AND UPPER(pun.estado) = '".strtoupper(trim($arrayVariables["estados_pto"]))."' ";
                }
            }

            if(isset($arrayVariables["negocios_pto"]))
            {
                if($arrayVariables["negocios_pto"] && $arrayVariables["negocios_pto"]!="" && $arrayVariables["negocios_pto"]!="0")
                {
                    $whereVar .= "AND pun.tipoNegocioId = '".trim($arrayVariables["negocios_pto"])."' ";
                }
            }

            if(isset($arrayVariables["vendedor"]))
            {
                if($arrayVariables["vendedor"] && $arrayVariables["vendedor"]!="")
                {
					$whereVar .= "AND CONCAT(LOWER(peVend.nombres),CONCAT(' ',LOWER(peVend.apellidos))) like '%".strtolower(trim($arrayVariables["vendedor"]))."%' ";
				}
            }

            if(isset($arrayVariables["identificacion"]))
            {
                if($arrayVariables["identificacion"] && $arrayVariables["identificacion"]!="")
                {
                    $whereVar .= "AND per.identificacionCliente = '".trim($arrayVariables["identificacion"])."' ";
                }
            }

            if(isset($arrayVariables["nombre"]))
            {
                if($arrayVariables["nombre"] && $arrayVariables["nombre"]!="")
                {
                    $whereVar .= "AND UPPER(per.nombres) like '%".strtoupper(trim($arrayVariables["nombre"]))."%' ";
                }
            }

            if(isset($arrayVariables["apellido"]))
            {
                if($arrayVariables["apellido"] && $arrayVariables["apellido"]!="")
                {
                    $whereVar .= "AND UPPER(per.apellidos) like '%".strtoupper(trim($arrayVariables["apellido"]))."%' ";
                }
            }

            if(isset($arrayVariables["razon_social"]))
            {
                if($arrayVariables["razon_social"] && $arrayVariables["razon_social"]!="")
                {
                    $whereVar .= "AND UPPER(per.razonSocial) like '%".strtoupper(trim($arrayVariables["razon_social"]))."%' ";
                }
            }

            if(isset($arrayVariables["direccion_grl"]))
            {
                if($arrayVariables["direccion_grl"] && $arrayVariables["direccion_grl"]!="")
                {
                    $whereVar .= "AND UPPER(per.direccion) like '%".strtoupper(trim($arrayVariables["direccion_grl"]))."%' ";
                }
            }

            if(isset($arrayVariables["estados_contrato"]))
            {
                if($arrayVariables["estados_contrato"] && $arrayVariables["estados_contrato"]!="" && $arrayVariables["estados_contrato"]!="0")
                {
                    $whereVar .= "AND con.estado = '".trim($arrayVariables["estados_contrato"])."' ";
                }
            }

            if(isset($arrayVariables["formas_pago"]))
            {
                if($arrayVariables["formas_pago"] && $arrayVariables["formas_pago"]!="" && $arrayVariables["formas_pago"]!="0")
                {
                    $whereVar .= "AND con.formaPagoId = '".trim($arrayVariables["formas_pago"])."' ";
                }
            }

            if(isset($arrayVariables["num_contrato"]))
            {
                if($arrayVariables["num_contrato"] && $arrayVariables["num_contrato"]!="")
                {
                    $whereVar .= "AND con.numeroContrato like '%".trim($arrayVariables["num_contrato"])."%' ";
                }
            }

            if(isset($arrayVariables["es_edificio"]) || isset($arrayVariables["depende_edificio"]))
            {
                $boolPDA = false;
                if($arrayVariables["es_edificio"] && $arrayVariables["es_edificio"]!="" && $arrayVariables["es_edificio"]!="0")
                {
                    $boolPDA = true;
                    $whereVar .= "AND pda.esEdificio = '".trim($arrayVariables["es_edificio"])."' ";
                }
                if($arrayVariables["depende_edificio"] && $arrayVariables["depende_edificio"]!="" && $arrayVariables["depende_edificio"]!="0")
                {
                    $boolPDA = true;
                    $whereVar .= "AND pda.dependeDeEdificio = '".trim($arrayVariables["depende_edificio"])."' ";
                }

                if($boolPDA)
                {
                    $fromAdicional .= "schemaBundle:InfoPuntoDatoAdicional pda, ";
                    $whereAdicional .= "AND pun.id = pda.puntoId ";
                }
            }

            if(isset($arrayVariables["es_vip"]))
            {
                if($arrayVariables["es_vip"] && $arrayVariables["es_vip"]!="" && $arrayVariables["es_vip"]!="0")
                {
                    $whereVar .= "AND cda.esVip = '".trim($arrayVariables["es_vip"])."' ";
                    $fromAdicional .= "schemaBundle:InfoContratoDatoAdicional cda, ";
                    $whereAdicional .= "AND con.id = cda.contratoId ";
                }
            }    

            if(isset($arrayVariables["tipo_documento"]))
            {
                $tipo_documento = $arrayVariables["tipo_documento"];
                if($tipo_documento && $tipo_documento!="" && $tipo_documento!="0")
                {
                    $creador = (isset($arrayVariables["creador_documento"]) ? $arrayVariables["creador_documento"] : "");
                    $numero = (isset($arrayVariables["num_documento"]) ? $arrayVariables["num_documento"] : "");
                    $tipoOrden = ($tipo_documento == "OT" && isset($arrayVariables["tipo_orden"]) ? $arrayVariables["tipo_orden"] : 0);

                    $desdeCreacion = (isset($arrayVariables["desde_creacion"]) ? $arrayVariables["desde_creacion"] : 0);
                    $hastaCreacion = (isset($arrayVariables["hasta_creacion"]) ? $arrayVariables["hasta_creacion"] : 0);

                    if($desdeCreacion && $desdeCreacion!="0")
                    {
                        $dateF = explode("-",$desdeCreacion);
                        $desdeCreacion = date("Y/m/d", strtotime($dateF[2]."-".$dateF[1]."-".$dateF[0]));
                    }
                    if($hastaCreacion && $hastaCreacion!="0")
                    {
                        $dateF = explode("-",$hastaCreacion);
                        $hastaCreacion = date("Y/m/d", strtotime($dateF[2]."-".$dateF[1]."-".$dateF[0]));
                    }

                    if($tipo_documento == "OT")
                    {
                        if($creador!="") { $whereVar .= "AND UPPER(ot.usrCreacion) like '%".strtoupper(trim($creador))."%' "; }
                        if($numero!="") {$whereVar .= "AND ot.numeroOrdenTrabajo like '".trim($numero)."%' "; }                           
                        if($tipoOrden!="" && $tipoOrden!="0") { $whereVar .= "AND UPPER(ot.tipoOrden) = '".strtoupper(trim($tipoOrden))."' "; }
                        if($desdeCreacion && $desdeCreacion!="0"){  $whereVar .= "AND ot.feCreacion >= '".trim($desdeCreacion)."' "; }
                        if($hastaCreacion && $hastaCreacion!="0") { $whereVar .= "AND ot.feCreacion <= '".trim($hastaCreacion)."' ";   }

                        $fromAdicional .= "schemaBundle:InfoOrdenTrabajo ot, ";
                        $whereAdicional .= "AND pun.id = ot.puntoId ";
                    }
                    if($tipo_documento == "C")
                    {
                        if($creador!="") { $whereVar .= "AND UPPER(cc.usrCreacion) like '%".strtoupper(trim($creador))."%' "; }
                        if($numero!="") { $whereVar .= "AND cc.numeroCotizacion like '".trim($numero)."%' "; }
                        if($desdeCreacion && $desdeCreacion!="0"){  $whereVar .= "AND cc.feCreacion >= '".trim($desdeCreacion)."' "; }
                        if($hastaCreacion && $hastaCreacion!="0") { $whereVar .= "AND cc.feCreacion <= '".trim($hastaCreacion)."' ";   }

                        $fromAdicional .= "schemaBundle:InfoCotizacionCab cc, ";
                        $whereAdicional .= "AND perol.id = cc.personaEmpresaRolId ";
                    }
                }
            }
        }            

		$sql = "SELECT pun.id as id_punto, con.id as id_contrato, 
                        pun.login, pun.direccion as direccion_pto, pun.descripcionPunto, pun.estado, pun.usrVendedor, 
                        per.id, per.identificacionCliente, per.nombres, per.apellidos, per.razonSocial, 
                        per.direccion as direccion_grl, per.calificacionCrediticia, 
						con.numeroContrato, con.estado as estadoContrato, 
						CONCAT(peVend.nombres,CONCAT(' ',peVend.apellidos)) as nombreVendedor 
				FROM 
                    schemaBundle:InfoPersona per, 
                    schemaBundle:InfoPersonaEmpresaRol perol, 
                    schemaBundle:InfoContrato con, 
					$fromAdicional
                    schemaBundle:InfoPunto pun 
				LEFT JOIN schemaBundle:InfoPersona peVend WITH peVend.login = pun.usrVendedor 				
                WHERE 
                    perol.id = pun.personaEmpresaRolId 
                    AND perol.id = con.personaEmpresaRolId 
                    AND per.id = perol.personaId 
                    $whereAdicional 
                    $whereVar 
                ";
				
        $query = $this->_em->createQuery($sql); 
        //echo $sql;
        $total=count($query->getResult());
		
        $datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
        $resultado['registros']=$datos;
        $resultado['total']=$total;

        return $resultado;
    }
	
    public function findClientesPorFormaPagoYEmpresa($idEmpresa,$bancoId,$tipoCuentaId,$nombreBanco){
        $criterio="";
        if(strtoupper($nombreBanco)=='TARJETAS'){$criterio=" m.id=$tipoCuentaId AND ";}
        else
        {$criterio=" l.id=$bancoId AND  m.descripcionCuenta in ('AHORRO','CORRIENTE') AND ";}	
        $query = $this->_em->createQuery(
            "SELECT a.id, g.id as oficinaId, a.nombres, a.apellidos, a.identificacionCliente, 
            m.id as tipoCuentaId, e.id as contratoFormaPagoId,d.numeroContrato,
            a.tipoIdentificacion, b.id as personaEmpresaRolId, d.id as contratoId,
            e.numeroCtaTarjeta,'' as puntoId,'' as numeroFacturaSri, e.titularCuenta
            FROM 
            schemaBundle:InfoPersona a, schemaBundle:InfoPersonaEmpresaRol b, 
            schemaBundle:InfoEmpresaRol c, schemaBundle:InfoContrato d,
            schemaBundle:InfoContratoFormaPago e,
            schemaBundle:InfoOficinaGrupo g, schemaBundle:AdmiFormaPago h,
            schemaBundle:AdmiBancoTipoCuenta k,
            schemaBundle:AdmiBanco l,
            schemaBundle:AdmiTipoCuenta m				
            WHERE 
            d.estado='Activo' AND
            a.id=b.personaId AND
            b.empresaRolId=c.id AND ".                
            " c.empresaCod='$idEmpresa' AND ".
            " b.id=d.personaEmpresaRolId AND
            b.oficinaId=g.id AND
            b.estado = 'Activo' AND
            d.id = e.contratoId AND
            d.formaPagoId=h.id AND
            e.bancoTipoCuentaId=k.id AND k.bancoId=l.id AND k.tipoCuentaId=m.id AND ".
            $criterio.
            "(h.codigoFormaPago='TARC' OR h.codigoFormaPago='DEB')"
        );
        //echo $query->getSQL(); die;
        //$datos = $query->setFirstResult(0)->setMaxResults(100)->getResult();
        $datos = $query->getResult();
        return $datos;
    }

	public function findClientesPorIdentificacionNoAnulados($identificacion, $empresaId){
                $query = $this->_em->createQuery(
                "SELECT a
				FROM 
                schemaBundle:InfoPersona a, schemaBundle:InfoPersonaEmpresaRol b, 
				schemaBundle:InfoEmpresaRol c ".
				" WHERE 
                a.id=b.personaId AND
				a.identificacionCliente='$identificacion' AND
                b.empresaRolId=c.id AND ".
				"c.empresaCod='$empresaId'
				");
                //echo $query->getSQL(); die;
		$datos =  $query->setFirstResult(0)->setMaxResults(1)->getResult();
		return $datos;		
	}	
	
	public function getContactosByLoginPersonaAndFormaContacto($loginEmpleado, $formaContacto)
    {
		if($formaContacto)
			$whereFormaContacto = " AND	lower(afc.descripcionFormaContacto) = lower('$formaContacto')";
		else
			$whereFormaContacto = "";
			
        $sql = "SELECT afc.descripcionFormaContacto,
					   pfc.valor
                FROM 
                    schemaBundle:InfoPersona p, 
                    schemaBundle:InfoPersonaFormaContacto pfc,
                    schemaBundle:AdmiFormaContacto afc                 
                WHERE 
                    p.id = pfc.personaId
				AND afc.id = pfc.formaContactoId
				AND	lower(p.login) = lower('$loginEmpleado')
				AND lower(pfc.estado) = lower('Activo')	
				AND pfc.valor is not null 
				$whereFormaContacto ";

            $query = $this->_em->createQuery($sql);			
            $formasContacto =  $query->getResult();

        return $formasContacto;
    }
    
	public function findClientesActivosPorCriterios(
            $idEmpresa,$usuario,$fechaDesde,$fechaHasta,
            $nombre,$apellido,$razonSocial,$limit,$page,$start)
        {
                $criterio_fecha_desde='';
                $criterio_fecha_hasta='';
                $nombre=  strtoupper($nombre);
                if ($fechaDesde){
                    $fechaD = date("Y/m/d", strtotime($fechaDesde));			
                    $fechaDesde = $fechaD ;
                    $criterio_fecha_desde=" p1.feCreacion >= '$fechaDesde' AND ";
                }
                if ($fechaHasta){
                    $fechaH = date("Y/m/d", strtotime($fechaHasta));			
                    $fechaHasta = $fechaH;
                    $criterio_fecha_hasta=" p1.feCreacion <= '$fechaHasta' AND ";
                }
                $criterio_estado='';
                $criterio_nombre='';
                $criterio_apellido='';
                $criterio_razonSocial='';				
                $criterio_usuario='';
                if ($usuario){       
                    $criterio_usuario="UPPER(p1.usrCreacion) = UPPER('$usuario') AND ";
                }                 
                if ($estado!='null' && $estado!=''&&$estado!=null){       
                    $criterio_estado="UPPER(p1.estado) = UPPER('$estado') AND ";
                }    
                if ($nombre){       
                    $criterio_nombre=" UPPER(p1.nombres) like UPPER('%$nombre%') AND ";
                } 
                if ($apellido){       
                    $criterio_apellido=" UPPER(p1.apellidos) like UPPER('%$apellido%') AND ";
                }  				
                if ($razonSocial){       
                    $criterio_razonSocial=" UPPER(p1.razonSocial) like UPPER('%$razonSocial%') AND ";
                }  				
		$query = $this->_em->createQuery("SELECT 
                    p1.id as idCliente, p1.estado,p1.razonSocial,p1.nombres, p1.apellidos,
                    p1.feCreacion,p1.usrCreacion,p1.direccion,per.id as id
		FROM 
                    schemaBundle:InfoPersona p1, 
                    schemaBundle:InfoPersonaEmpresaRol per,
                    schemaBundle:InfoEmpresaRol empr,
                    schemaBundle:AdmiRol rol,
                    schemaBundle:AdmiTipoRol trol
		WHERE 
                p1.id=per.personaId AND
		per.empresaRolId=empr.id AND
                empr.rolId=rol.id AND
                rol.tipoRolId=trol.id AND
                UPPER(trol.descripcionTipoRol)='CLIENTE' AND
                per.estado in ('Activo','Pendiente') AND
                $criterio_estado
                $criterio_nombre
                $criterio_apellido
                $criterio_razonSocial				
                $criterio_usuario    
                $criterio_fecha_desde
                $criterio_fecha_hasta
                empr.empresaCod='$idEmpresa'  
                order by per.feCreacion DESC");
		//echo "SQL:".$query->getSQL();
                $total=count($query->getResult());
		$datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
                $resultado['registros']=$datos;
                $resultado['total']=$total;
		return $resultado;
	}

    
        public function findEmpleadosPorEmpresa($nombre="", $codEmpresa="", $rol="")
        {    
            $whereAdd = "";

                if($nombre!="" && $nombre)
                    $whereAdd .= "AND CONCAT(LOWER(p.nombres),CONCAT(' ',LOWER(p.apellidos))) like '%".strtolower($nombre)."%' ";
                
                if($codEmpresa!="" && $codEmpresa){
					$whereAdd .= "AND eg.id = '$codEmpresa' ";
                }

                if($rol!="" && $rol){
                    $whereAdd .= "AND LOWER(r.descripcionRol) like LOWER('%$rol%') ";
                }
				
                $sql = "SELECT p 
                        FROM 
                            schemaBundle:InfoPersona p, 
                            schemaBundle:InfoPersonaEmpresaRol per, 
                            schemaBundle:InfoEmpresaRol er,
                            schemaBundle:InfoEmpresaGrupo eg,
                            schemaBundle:AdmiRol r, 
                            schemaBundle:AdmiTipoRol tr 

                        WHERE 
                            p.id = per.personaId AND 
                            er.id = per.empresaRolId AND
                            eg.id = er.empresaCod AND
                            r.id = er.rolId AND
                            tr.id = r.tipoRolId AND               
                            tr.descripcionTipoRol in  ('Empleado','Personal Externo','Agencias') AND 
                            lower(p.estado) not like lower('Eliminado') AND 
                            lower(per.estado) not like lower('Eliminado') AND
                            lower(er.estado) not like lower('Eliminado') AND 
                            lower(r.estado) not like lower('Eliminado') 
                            $whereAdd 
                               
                         ORDER BY p.nombres, p.apellidos 
                        ";

            $query = $this->_em->createQuery($sql);
			$datos = $query->getResult();
            return $datos;
        }         
    
    /**
     * Documentación para el método 'getJsonVendedoresPorEmpresa'.
     *
     * Método que obtiene esl string Json con el listado de los empleados vendedores por empresa.
     *
     * @param Array $arrayParametros['EMPRESA']       String: Código de la empresa.
     *                              ['ESTADOS']       String: Estados de filtrado.
     *                              ['ROL_MD']        String: Rol que aplica para MD.
     *                              ['ROL']           String: Rol que aplica para TN y MD.
     *                              ['DEPARTAMENTOS'] String: Departamentos de filtrado.
     *                              ['START']         Int   : Inicio de la paginación
     *                              ['LIMIT']         Int   : Rango máximo de la paginación
     * 
     * @return Response Lista de Vendedores.
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>       
     * @version 1.0 14-04-2016
     */
    public function getJsonVendedoresPorEmpresa($arrayParametros)
    {
        $arrayResultado = $this->getResultadoVendedoresPorEmpresa($arrayParametros);
        
        if($arrayResultado['TOTAL'] > 0)
        {
            $strJsonResultado = '{"total":"' . $arrayResultado['TOTAL'] . '","registros":' . json_encode($arrayResultado['REGISTROS']) . '}';
        }
        else
        {
            $strJsonResultado = '{"total":"0", "registros":[], "error":[' . $arrayResultado['ERROR'] . ']}';
        }
        
        return $strJsonResultado;
    }

    /**
     * Documentación para el método 'getResultadoVendedoresPorEmpresa'.
     *
     * Método que obtiene el listado de los empleados vendedores por empresa
     *
     * @param Array $arrayParametros['EMPRESA']       String: Código de la empresa.
     *                              ['ESTADOS']       String: Estados de filtrado.
     *                              ['LOGIN']         String: Login de MD.
     *                              ['ROL_MD']        String: Rol que aplica para MD.
     *                              ['ROL']           String: Rol que aplica para TN y MD.
     *                              ['DEPARTAMENTOS'] String: Departamentos de filtrado.
     *                              ['START']         Int   : Inicio de la paginación
     *                              ['LIMIT']         Int   : Rango máximo de la paginación
     * 
     * @return Response Lista de Vendedores.
     * 
     * costoQuery: Count  13
     *             Data  209
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>       
     * @version 1.0 14-04-2016
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>       
     * @version 1.1 22-06-2016
     * Se definen los parámetros fijos del filtro de la consulta.
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>       
     * @version 1.2 06-07-2016
     * Se agrega el departamento GERENCIA TECNICA NACIONAL a los filtros de vendedores.
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>       
     * @version 1.3 06-07-2016
     * Se obtiene el listado de departamentos de los vendedores desde parámetros.
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>       
     * @version 1.4 31-08-2016
     * Se agregan las cláusulas DISTINCT GROUP BY para la obtención de un único vendedor a pesar de tener más de un rol similar en la misma empresa.
     * Se filtra por Estado del Rol del vendedor cuando el destino de los datos es operacional y no filtra cuando es informativo.
     */
    public function getResultadoVendedoresPorEmpresa($arrayParametros)
    {
        $arrayVendedores = array();
        $arrayResultado  = array();
        
        $arrayParametros['ROL_MD']     = 'PERSONAL EXTERNO'; // Solo Mega Maneja Proveedores Externos.
        $arrayParametros['ROL']        = 'EMPLEADO';
        $arrayParametros['DPTOS_AGEN'] = 'AGENCIA';
        $arrayParametros['ESTADOS']    = array('Activo','Modificado');
            
        // Obtención del listado de departamentos autorizados para realizar ventas
        $arrayDepartamentos = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                                        ->get('DEPARTAMENTOS_VENDEDORES', 'COMERCIAL', '', '', '', '', '', '', '', $arrayParametros['EMPRESA']);
        if(!$arrayDepartamentos)
        {
            throw new \Exception("No se han definido los departamentos de los vendedores.");
        }
        
        foreach($arrayDepartamentos as $objDepartamento)
        {
            $arrayParametros['DEPARTAMENTOS'][] = $objDepartamento['valor1'];
        }

        try
        {
            $objQueryBuilder = $this->_em->createQueryBuilder();

            $objQueryBuilder->select("count(DISTINCT p.login)")
                            ->from('schemaBundle:InfoPersonaEmpresaRol', 'per')
                            ->innerJoin('schemaBundle:InfoPersona',      'p',   'WITH', 'p.id   = per.personaId')
                            ->innerJoin('schemaBundle:AdmiDepartamento', 'd',   'WITH', 'd.id   = per.departamentoId')
                            ->innerJoin('schemaBundle:InfoEmpresaRol',   'er',  'WITH', 'er.id  = per.empresaRolId')
                            ->innerJoin('schemaBundle:InfoEmpresaGrupo', 'eg',  'WITH', 'eg.id  = er.empresaCod')
                            ->innerJoin('schemaBundle:AdmiRol',          'r',   'WITH', 'r.id   = er.rolId')
                            ->innerJoin('schemaBundle:AdmiTipoRol',      'tr',  'WITH', 'tr.id  = r.tipoRolId')
                            ->Where('p.login       is not null')
                            ->andWhere('er.empresaCod =   :EMPRESA')
                            ->andWhere("((eg.prefijo = 'MD' or eg.prefijo = 'EN') and  upper(tr.descripcionTipoRol) = :ROL_MD
                                        or
                                        upper(tr.descripcionTipoRol)  = :ROL AND 
                                        (upper(d.nombreDepartamento) IN (:DEPARTAMENTOS) OR upper(d.nombreDepartamento) LIKE :DPTOS_AGEN) )");
            
            if(!(isset($arrayParametros['TODOS']) && $arrayParametros['TODOS']))
            {
                $objQueryBuilder->andWhere('per.estado       in (:ESTADOS)');
                $objQueryBuilder->setParameter('ESTADOS', $arrayParametros['ESTADOS']);
            }
            
            $objQueryBuilder->setParameter('EMPRESA',       $arrayParametros['EMPRESA']);
            $objQueryBuilder->setParameter('ROL_MD',        $arrayParametros['ROL_MD']);
            $objQueryBuilder->setParameter('ROL',           $arrayParametros['ROL']);
            $objQueryBuilder->setParameter('DEPARTAMENTOS', $arrayParametros['DEPARTAMENTOS']);
            $objQueryBuilder->setParameter('DPTOS_AGEN',    '%'.$arrayParametros['DPTOS_AGEN'].'%');

            if(isset($arrayParametros['NOMBRE']) && $arrayParametros['NOMBRE'] != '')
            {
                $objQueryBuilder->andWhere("concat(upper(p.nombres), concat(' ', upper(p.apellidos))) like :NOMBRE");
                $objQueryBuilder->setParameter('NOMBRE', '%' . $arrayParametros['NOMBRE'] . '%');
            }
            if(isset($arrayParametros['LOGIN']) && $arrayParametros['LOGIN'] != '')
            {
                $objQueryBuilder->andWhere("lower(p.login) = :LOGIN");
                $objQueryBuilder->setParameter('LOGIN', strtolower($arrayParametros['LOGIN']));
            }
          
            $intTotalRegistros = $objQueryBuilder->getQuery()->getSingleScalarResult();

            if($intTotalRegistros > 0)
            {
                $objQueryBuilder->select("p.login, concat(upper(p.nombres),concat(' ',upper(p.apellidos))) nombre");
                $objQueryBuilder->groupBy('p.login, p.nombres, p.apellidos');
                $objQueryBuilder->orderBy('p.nombres, p.apellidos');
          
                if(isset($arrayParametros['LIMIT']) && $arrayParametros['LIMIT'] != '')
                {
                    $objQueryBuilder->setFirstResult($arrayParametros['LIMIT']);

                    if(isset($arrayParametros['START']) && $arrayParametros['START'] != '')
                    {
                        $objQueryBuilder->setMaxResults($arrayParametros['START']);
                    }
                }
                if(isset($arrayParametros['LOGIN']) && $arrayParametros['LOGIN'] != '')
                {
                    $arrayVendedores = $objQueryBuilder->getQuery()->getOneOrNullResult();
                }
                else
                {
                    $arrayVendedores = $objQueryBuilder->getQuery()->getResult();
                }
            }
            $arrayResultado['REGISTROS'] = $arrayVendedores;
            $arrayResultado['TOTAL']     = $intTotalRegistros;
            $arrayResultado['ERROR']     = '';
        }
        catch(Exception $ex)
        {
            $arrayResultado['ERROR'] = 'Error: ' . $ex->getMessage(); // Se almacena el error por excepción
        }
        
        return $arrayResultado;
    }

    public function getPersonaParaSession2($codEmpresa, $idPersonaRol)
    { 
        $persona = array();
        
		$sql = "SELECT a.id, b.id as personaEmpresaRolId, a.razonSocial, a.nombres, a.apellidos,
                                    og.id as IdOficina, og.nombreOficina, eg.id as CodEmpresa, eg.nombreEmpresa, a.estado,
                                    d.id as IdRol, d.descripcionRol, e.id as IdTipoRol, e.descripcionTipoRol  ,a.identificacionCliente ,a.direccion,
                                    a.fechaNacimiento
                         FROM 
                                 schemaBundle:InfoPersona a, schemaBundle:InfoPersonaEmpresaRol b,
                                 schemaBundle:InfoEmpresaRol c, schemaBundle:AdmiRol d, schemaBundle:AdmiTipoRol e,
                                 schemaBundle:InfoOficinaGrupo og, schemaBundle:InfoEmpresaGrupo eg
                         WHERE 
                                 a.id=b.personaId
                                 AND b.empresaRolId=c.id
                                 AND c.rolId=d.id 
                                 AND d.tipoRolId=e.id 
                                 AND b.oficinaId=og.id
                                 AND og.empresaId=eg.id
                                 AND b.id = '$idPersonaRol'  
                                 AND c.empresaCod='$codEmpresa' 
                                 AND b.estado in ('Activo','Modificado','Pend-convertir','Cancel','Cancelado') ".
                                 " AND c.estado in ('Activo','Modificado','Pend-convertir') ORDER BY b.feCreacion DESC";
		$query = $this->_em->createQuery($sql)->setMaxResults(1);
                //echo $query->getSQL();die;
        $entity =  $query->getResult();
        
        if($entity && count($entity)>0)
        {			
            $persona['id'] = $entity[0]['id'];
            $persona['id_persona_empresa_rol'] = $entity[0]['personaEmpresaRolId'];
            $persona['id_persona'] = $entity[0]['id'];
            $persona['razon_social'] = $entity[0]['razonSocial'];
            $persona['nombres'] = $entity[0]['nombres'];
            $persona['apellidos'] = $entity[0]['apellidos'];
            $persona['identificacion'] = $entity[0]['identificacionCliente'];
            $persona['direccion'] = $entity[0]['direccion'];
            $persona['estado'] = $entity[0]['estado'];
            $persona['id_oficina'] = $entity[0]["IdOficina"];
            $persona['nombre_oficina'] = $entity[0]["nombreOficina"];
            $persona['id_empresa'] = $entity[0]["CodEmpresa"];
            $persona['nombre_empresa'] = $entity[0]["nombreEmpresa"];
            $persona['id_rol'] = $entity[0]["IdRol"];
            $persona['nombre_rol'] = $entity[0]["descripcionRol"];
            $persona['id_tipo_rol'] = $entity[0]["IdTipoRol"];
            $persona['nombre_tipo_rol'] = $entity[0]["descripcionTipoRol"]; 
            $persona['fechaNacimiento'] = strval(date_format($entity[0]["fechaNacimiento"], "d-M-Y")); 
            //Se llama a función encargada de obtener la edad de la persona.
            $intEdad = $this->getEdadPersona(array('intIdPersona' => $entity[0]['id']));
            $persona['edad'] = $intEdad > 0 ? $intEdad : "";

        }
        else
        {
            $persona['id']  = "";
            $persona['id_persona_empresa_rol']  = "";
            $persona['id_persona']  = "";
            $persona['razon_social']  = "";
            $persona['nombres']  = "";
            $persona['apellidos']  = "";
            $persona['identificacion']  = "";
            $persona['direccion']  = "";
            $persona['estado']  = "";
            $persona['id_oficina'] = "";
            $persona['nombre_oficina'] = "";
            $persona['id_empresa'] = "";
            $persona['nombre_empresa'] = "";
            $persona['id_rol'] = "";
            $persona['nombre_rol'] = "";
            $persona['id_tipo_rol'] = "";
            $persona['nombre_tipo_rol'] = "";             
            $persona['fechaNacimiento'] = "";
            $persona['edad'] = "";

        }

        return $persona;
    }        

    /**
     * Determina la validez de una identificacion segun su tipo
     * @param string $strTipoIdentificacion
     * @param string $strIdentificacionCliente
     * @param integer $intIdPais
     * @return string mensaje de error, null en caso contrario
     *
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.1 21-09-2017 - Se agrega el parámetro intIdPais para ejecutar la validación correspondiente.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.2 18-03-2019 - Se agrega validación para formato de identificación de Telconet Guatemala.
     *  
     * @author Katherine Yager <kyager@telconet.ec>
     * @version 1.3 06-05-2019 - Se agrega validacion isset para verificar si el valor $intIdPais tiene data.
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.4 10-01-2022 - Se solicita enviar por parámetro la empresa, por este motivo los parámetros de entrada
     *                          de la función cambia a un array.
     */
    public function validarIdentificacionTipo($arrayParametros)
    {
        $strTipoIdentificacion      = $arrayParametros['strTipoIdentificacion'];
        $strIdentificacionCliente   = $arrayParametros['strIdentificacionCliente'];
        $intIdPais                  = $arrayParametros['intIdPais'];
        $strCodEmpresa              = $arrayParametros['strCodEmpresa'];
        $arrayTipoTrib              = $this->_em->getRepository('schemaBundle:InfoPersona')
                                                ->findBy(array('identificacionCliente'      => $strIdentificacionCliente),
                                                         array('feCreacion' => 'DESC'));
        $strTipoTributario          = (!empty($arrayTipoTrib) && $arrayTipoTrib[0]->getTipoTributario()) ?
                                                                 $arrayTipoTrib[0]->getTipoTributario() : '';
        if(!isset($strCodEmpresa))
        {
            $strCodEmpresa = "0";
        }
        if (isset($intIdPais) && !is_null($intIdPais) && !empty($intIdPais))
        {
            $objAdmiPais = $this->_em->getRepository('schemaBundle:AdmiPais')->find($intIdPais);

            if(is_object($objAdmiPais))
            {            
                if($objAdmiPais->getNombrePais()==='PANAMA')
                {
                    return $this->validarFormatoPanama($strIdentificacionCliente, $strTipoIdentificacion);
                }
                else if($objAdmiPais->getNombrePais()==='GUATEMALA')
                {
                    $arrayParametros                             = array();
                    $arrayParametros['strTipoIdentificacion']    = $strTipoIdentificacion;                
                    $arrayParametros['strIdentificacionCliente'] = $strIdentificacionCliente;
                    return $this->validarFormatoGuatemala($arrayParametros);
                }
            }
        }        

        // se debe enviar un string con suficiente espacio para la respuesta
        $strMensaje = str_repeat(' ', 100);
        $strSql     = 'BEGIN VALIDA_IDENTIFICACION.VALIDA(:p_tipo_ident, :p_identificacion, :pv_CodEmpresa, :pv_tipoTributario, :p_mensaje); END;';
        $strStmt    = $this->_em->getConnection()->prepare($strSql);
        $strStmt->bindParam('p_tipo_ident', $strTipoIdentificacion);
        $strStmt->bindParam('p_identificacion', $strIdentificacionCliente);
        $strStmt->bindParam('pv_CodEmpresa', $strCodEmpresa);
        $strStmt->bindParam('pv_tipoTributario', $strTipoTributario);
        //$stmt->bindParam('p_mensaje', $mensaje, \PDO::PARAM_STR|\PDO::PARAM_INPUT_OUTPUT, 100);
        $strStmt->bindParam('p_mensaje', $strMensaje);
        $strStmt->execute();
        return trim($strMensaje);
    }

    /**
     * Documentación para funcion 'findClientesParaDebitosPorPunto'.
     * busca clientes por banco o tarjeta, por empresa y que tengan saldo
     * Se usa procedimiento almacenado CLIENTE_DEBITO_PKG.CLIENTES_DEBITO_FACTURA
     * @param int $idEmpresa
     * @param int $bancoId
     * @param int $tipoCuentaId
     * @param string $nombreBanco
     * @param Object $cursa cursor donde se retorna la respuesta del procedimiento
     * @param Object $oci_con - conexion a la bd
     * @return array $respuesta clientes encontrados
     */    
    public function findClientesParaDebitosPorPunto($idEmpresa,$bancoId,$tipoCuentaId,$nombreBanco,$cursa,$oci_con)
    {
        $arrayDatos="";                
        $s = oci_parse($oci_con, "BEGIN CLIENTE_DEBITO_PKG.CLIENTES_DEBITO_FACTURA(".
        ":idEmpresa, :idBanco, :idTipoCuenta, :nombreBanco,:clientesRec); END;");
        oci_bind_by_name($s, ":idEmpresa", $idEmpresa);
        oci_bind_by_name($s, ":idBanco", $bancoId);
        oci_bind_by_name($s, ":idTipoCuenta", $tipoCuentaId);
        oci_bind_by_name($s, ":nombreBanco", $nombreBanco);
        oci_bind_by_name($s, ":clientesRec", $cursa, -1, OCI_B_CURSOR);
        oci_execute($s);
        oci_execute($cursa);
        $i=0;
        while (($row = oci_fetch_array($cursa, OCI_ASSOC+OCI_RETURN_NULLS)) != false)
        {
            $arrayDatos[$i]['valorTotal']            = $row['VALORTOTAL'];
            $arrayDatos[$i]['subtotal']              = $row['SUBTOTAL'];
            $arrayDatos[$i]['documentoId']           = $row['DOCUMENTO_ID'];
            $arrayDatos[$i]['numeroFacturaSri']      = $row['NUMEROFACTURASRI'];
            $arrayDatos[$i]['feEmision']             = $row['FEEMISION'];
            $arrayDatos[$i]['id']                    = $row['ID_PERSONA'];
            $arrayDatos[$i]['oficinaId']             = $row['OFICINAID'];
            $arrayDatos[$i]['nombres']               = $row['NOMBRES'];
            $arrayDatos[$i]['apellidos']             = $row['APELLIDOS'];
            $arrayDatos[$i]['identificacionCliente'] = $row['IDENTIFICACION_CLIENTE'];
            $arrayDatos[$i]['tipoCuentaId']          = $row['TIPO_CUENTA_ID'];
            $arrayDatos[$i]['contratoFormaPagoId']   = $row['CONTRATO_FORMA_PAGO_ID'];
            $arrayDatos[$i]['numeroContrato']        = $row['NUMERO_CONTRATO'];
            $arrayDatos[$i]['tipoIdentificacion']    = $row['TIPO_IDENTIFICACION'];
            $arrayDatos[$i]['personaEmpresaRolId']   = $row['PERSONAEMPRESAROLID'];
            $arrayDatos[$i]['contratoId']            = $row['CONTRATOID'];
            $arrayDatos[$i]['numeroCtaTarjeta']      = $row['NUMERO_CTA_TARJETA'];
            $arrayDatos[$i]['puntoId']               = $row['PUNTOID'];
            $arrayDatos[$i]['titularCuenta']         = $row['TITULAR_CUENTA'];
            $arrayDatos[$i]['numeroCtaTarjeta']      = $row['NUMERO_CTA_TARJETA'];
            $arrayDatos[$i]['razonSocial']           = $row['RAZON_SOCIAL'];
            $arrayDatos[$i]['saldo']                 = $row['SALDO'];
            $arrayDatos[$i]['login']                 = $row['LOGIN'];
            $i++;
        }         
        return $arrayDatos;
    }
    
    /**
    * generarJsonPersonaXTipoRol
    *
    * Método que obtiene el json de personas segun el tipo rol enviado
    *      
    * @return json
    *
    * @author Allan Suárez <arsuarez@telconet.ec>
    * 
    * @version 1.0 17-03-2015       
    */
    public function generarJsonPersonaXTipoRol($nombres, $apellidos, $identificacion, $tipoRol)
    {
        $arr_encontrados = array();
        
        $registros      = $this->getRegistrosPersonaXTipoRol($nombres, $apellidos, $identificacion, $tipoRol);

        if($registros)
        {            
            foreach($registros as $data)
            {
                $objPersona = $data->getPersonaId();
                
                $nombres = sprintf($objPersona);
                
                $arr_encontrados[] = array( 'id_persona'             => $objPersona->getId(),
                                            'id_persona_empresa_rol' => $data->getId(),                                            
                                            'login'                  => ($objPersona->getLogin()?$objPersona->getLogin():""), 
                                            'id_empresa'             => $data->getEmpresaRolId()->getEmpresaCod()->getId(),
                                            'nombre_empresa'         => ($data->getEmpresaRolId()->getEmpresaCod()->getNombreEmpresa() ? 
                                                                         $data->getEmpresaRolId()->getEmpresaCod()->getNombreEmpresa() : ""),
                                            'tipo_identificacion'    => ($objPersona->getTipoIdentificacion() == "CED" ? 
                                                                        "Cedula" : $objPersona->getTipoIdentificacion()),
                                            'identificacion'         => ($objPersona->getIdentificacionCliente() ? 
                                                                         $objPersona->getIdentificacionCliente() : 0),
                                            'nombres'                => $nombres,
                                            'apellidos'              => ($objPersona->getApellidos() ? 
                                                                         $objPersona->getApellidos() : ""),
                                            'nacionalidad'           => ($objPersona->getNacionalidad() == "EXT" ? 
                                                                        "Extranjera" : ($objPersona->getNacionalidad() == "NAC" ? "Nacional" : "")),
                                            'direccion'              => ($objPersona->getDireccion() ? $objPersona->getDireccion() : ""),
                                            'estado'                 => (strtolower(trim($objPersona->getEstado())) == 
                                                                        strtolower('ELIMINADO') ? 'Eliminado' : 'Activo'),                                           
                                                                        
                );
            }
            
            $data = json_encode($arr_encontrados);
            $resultado = '{"total":"' . count($arr_encontrados) . '","encontrados":' . $data . '}';
            return $resultado;
        }
        else
        {
            $resultado = '{"total":"0","encontrados":[]}';
            return $resultado;
        }
    }

     /**
    * getRegistrosPersonaXTipoRol
    *
    * Método que obtiene el array que retorna el query de las personas segun tipo rol
    *      
    * @return json
    *
    * @author Allan Suárez <arsuarez@telconet.ec>
    * 
    * @version 1.0 17-03-2015       
    */
    public function getRegistrosPersonaXTipoRol($nombres, $apellidos, $identificacion, $tipoRol){
        
        $query = $this->_em->createQuery();        
		
        $dql = "SELECT per  
                FROM 
					schemaBundle:InfoPersona p, 
					schemaBundle:InfoPersonaEmpresaRol per, 
					schemaBundle:InfoEmpresaRol er,
					schemaBundle:InfoEmpresaGrupo eg,
					schemaBundle:AdmiRol r, 
					schemaBundle:AdmiTipoRol tr 
                WHERE 
					p.id = per.personaId AND 
					er.id = per.empresaRolId AND
					eg.id = er.empresaCod AND
					r.id = er.rolId AND
					tr.id = r.tipoRolId AND               
					lower(tr.descripcionTipoRol) = lower(:tipoRol) 					                    
                ";   
        
        $query->setParameter('tipoRol', $tipoRol);
        
        if($nombres!=""){
            
            $dql .= " AND LOWER(p.nombres) like LOWER(:nombres) ";
            $query->setParameter('nombres', '%' . $nombres . '%');
        }
        if($apellidos!=""){
            
            $dql .= " AND LOWER(p.apellidos) like LOWER(:apellidos) ";
            $query->setParameter('apellidos', '%' . $apellidos . '%');
        }
        if($identificacion!=""){
            
            $dql .= " AND p.identificacionCliente = :identificacion ";
            $query->setParameter('identificacion', $identificacion);
        }       

        $dql .= " ORDER BY p.nombres, p.apellidos ";
        
        $query->setDQL($dql);                
                
        $datos = $query->getResult();
        
        return $datos;
    }

     /* Funcion que sirve para obtener los datos del cliente
     * por idServicio, la consulta devuelve un solo registro. 
     * Costo = 8
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 16-06-2015
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 27-10-2015 Se agrega parametro para poder filtrar por servicios de tipo producto
     * 
     * @param integer $idServicio
     * @param integer $esProducto
     * 
     * @param array $resultado (LOGIN, ID_PERSONA, NOMBRES, RAZON_SOCIAL,IDENTIFICACION_CLIENTE, DIRECCION,
     *                          PLAN, LONGITUD, LATITUD)
     */
    public function getDatosClientePorIdServicio($idServicio, $esProducto)
    {
        //en caso de tener valor el parametro se debe realizar el match contra la tabla ADMI_PRODUCTO
        if ($esProducto)
        {
            $strSqlSelect = ' PROD.DESCRIPCION_PRODUCTO PRODUCTO, ';
            $strSqlFrom   = ' ADMI_PRODUCTO PROD, ';
            $strSqlWhere  = ' AND SERVICIO.PRODUCTO_ID = PROD.ID_PRODUCTO ';
        }
        else
        {
            $strSqlSelect = ' PLANC.NOMBRE_PLAN PLAN, ';
            $strSqlFrom   ='INFO_PLAN_CAB PLANC,';
            $strSqlWhere  = ' AND SERVICIO.PLAN_ID = PLANC.ID_PLAN ';
        }
        
        $sql = "SELECT PUNTO.LOGIN LOGIN, 
                PERSONA.ID_PERSONA ID_PERSONA,
                PERSONA.NOMBRES || ' ' || PERSONA.APELLIDOS NOMBRES, 
                PERSONA.RAZON_SOCIAL RAZON_SOCIAL, 
                PERSONA.IDENTIFICACION_CLIENTE IDENTIFICACION_CLIENTE,
                PERSONA.DIRECCION DIRECCION, 
                ".$strSqlSelect."
                PUNTO.LONGITUD LONGITUD, 
                PUNTO.LATITUD LATITUD
                FROM INFO_SERVICIO SERVICIO,
                INFO_PUNTO PUNTO,
                ".$strSqlFrom."
                INFO_PERSONA_EMPRESA_ROL IPER,
                INFO_PERSONA PERSONA
                WHERE SERVICIO.PUNTO_ID = PUNTO.ID_PUNTO
                ".$strSqlWhere."
                AND PUNTO.PERSONA_EMPRESA_ROL_ID = IPER.ID_PERSONA_ROL
                AND IPER.PERSONA_ID = PERSONA.ID_PERSONA
                AND SERVICIO.ID_SERVICIO = :idServicio
                AND ROWNUM < 2";
        
        
        $stmt = $this->_em->getConnection()->prepare($sql);
        $stmt->bindValue('idServicio', $idServicio);
        $stmt->execute();
        
        $arraResult = $stmt->fetchAll();
        $resultado  = $arraResult[0];
        
        return $resultado;

    }
    
    
    /* Función que sirve para obtener los datos del cliente en el punto
     * por Servicio, la consulta devuelve un solo registro. 
     * 
     * Costo = 10
     * 
     * @author Néstor Naula <nnaulaltelconet.ec>
     * @version 1.0 19-09-2018
     * 
     * @param integer $arrayParametros
     * @param array $objResultado
     */
    public function getDatosClienteDelPuntoPorIdServicio($arrayParametros)
    {
        $idServicio = $arrayParametros['idServicio'];
        $esProducto = $arrayParametros['booleanEsProducto'];
        $sql = "";
        
        if ($esProducto)
        {
            $sql = "SELECT PUNTO.LOGIN LOGIN, 
                    PERSONA.ID_PERSONA ID_PERSONA,
                    PERSONA.NOMBRES || ' ' || PERSONA.APELLIDOS NOMBRES, 
                    PERSONA.RAZON_SOCIAL RAZON_SOCIAL, 
                    PERSONA.IDENTIFICACION_CLIENTE IDENTIFICACION_CLIENTE,
                    PUNTO.DIRECCION DIRECCION, 
                    PROD.DESCRIPCION_PRODUCTO PRODUCTO,
                    PUNTO.LONGITUD LONGITUD, 
                    PUNTO.LATITUD LATITUD
                    FROM 
                    DB_COMERCIAL.INFO_SERVICIO SERVICIO
                    INNER JOIN DB_COMERCIAL.INFO_PUNTO PUNTO ON SERVICIO.PUNTO_ID = PUNTO.ID_PUNTO
                    INNER JOIN DB_COMERCIAL.ADMI_PRODUCTO PROD  ON SERVICIO.PRODUCTO_ID = PROD.ID_PRODUCTO 
                    INNER JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPER ON PUNTO.PERSONA_EMPRESA_ROL_ID = IPER.ID_PERSONA_ROL
                    INNER JOIN DB_COMERCIAL.INFO_PERSONA PERSONA ON IPER.PERSONA_ID = PERSONA.ID_PERSONA
                    WHERE  
                    SERVICIO.ID_SERVICIO = :idServicio
                    AND ROWNUM = 1 ";
        }
        else
        {
            $sql = "SELECT PUNTO.LOGIN LOGIN, 
                    PERSONA.ID_PERSONA ID_PERSONA,
                    PERSONA.NOMBRES || ' ' || PERSONA.APELLIDOS NOMBRES, 
                    PERSONA.RAZON_SOCIAL RAZON_SOCIAL, 
                    PERSONA.IDENTIFICACION_CLIENTE IDENTIFICACION_CLIENTE,
                    PUNTO.DIRECCION DIRECCION, 
                    PLANC.NOMBRE_PLAN PLAN,
                    PUNTO.LONGITUD LONGITUD, 
                    PUNTO.LATITUD LATITUD
                    FROM 
                    DB_COMERCIAL.INFO_SERVICIO SERVICIO
                    INNER JOIN DB_COMERCIAL.INFO_PUNTO PUNTO ON SERVICIO.PUNTO_ID = PUNTO.ID_PUNTO
                    INNER JOIN DB_COMERCIAL.INFO_PLAN_CAB PLANC  ON SERVICIO.PLAN_ID = PLANC.ID_PLAN 
                    INNER JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPER ON PUNTO.PERSONA_EMPRESA_ROL_ID = IPER.ID_PERSONA_ROL
                    INNER JOIN DB_COMERCIAL.INFO_PERSONA PERSONA ON IPER.PERSONA_ID = PERSONA.ID_PERSONA
                    WHERE  
                    SERVICIO.ID_SERVICIO = :idServicio
                    AND ROWNUM = 1 ";
        }
        
        $stmt = $this->_em->getConnection()->prepare($sql);
        $stmt->bindValue('idServicio', $idServicio);
        $stmt->execute();
        
        $arraResult = $stmt->fetchAll();
        $objResultado  = $arraResult[0];
        
        return $objResultado;

    }
    
    /**
     * Funcion que sirve para obtener los contactos datos del punto del cliente
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 16-06-2015
     * @param integer $idPunto
     * @param integer $start
     * @param integer $limit
     */
    public function getFormaContactoPorPunto($idPunto,$start,$limit)
    {
        $qb  = $this->_em->createQueryBuilder();
        $qbC = $this->_em->createQueryBuilder();
        
        $qb ->select('formaContacto.descripcionFormaContacto descripcionFormaContacto, puntoFormaContacto.valor valor')
            ->from('schemaBundle:InfoPunto','punto')
            ->from('schemaBundle:InfoPuntoFormaContacto','puntoFormaContacto')
            ->from('schemaBundle:AdmiFormaContacto','formaContacto')
            ->where('punto = puntoFormaContacto.puntoId')
            ->andWhere('puntoFormaContacto.formaContactoId = formaContacto')
            ->andWhere('puntoFormaContacto.estado = ?1')
            ->andWhere('punto.id = ?2')
            ->andWhere('formaContacto.descripcionFormaContacto like ?3');
        $qb ->setParameter(1, 'Activo');
        $qb ->setParameter(2, $idPunto);
        $qb ->setParameter(3, 'Telefono%');
        
        $qbC->select('count(puntoFormaContacto.id)')
            ->from('schemaBundle:InfoPunto','punto')
            ->from('schemaBundle:InfoPuntoFormaContacto','puntoFormaContacto')
            ->from('schemaBundle:AdmiFormaContacto','formaContacto')
            ->where('punto = puntoFormaContacto.puntoId')
            ->andWhere('puntoFormaContacto.formaContactoId = formaContacto')
            ->andWhere('puntoFormaContacto.estado = ?1')
            ->andWhere('punto.id = ?2')
            ->andWhere('formaContacto.descripcionFormaContacto like ?3');
        $qbC->setParameter(1, 'Activo');
        $qbC->setParameter(2, $idPunto);
        $qbC->setParameter(3, 'Telefono%');
        
        if($start!='')
        {
            $qb->setFirstResult($start);   
        }
            
        if($limit!='')
        {
            $qb->setMaxResults($limit);
        }
        
        //total de objetos
        $total = $qbC->getQuery()->getSingleScalarResult();
        
        //obtener los objetos
        $query = $qb->getQuery();
        $datos = $query->getResult();
        
        $resultado['registros'] = $datos;
        $resultado['total']     = $total;
        
        return $resultado;
    }
    
    /**
     * Funcion que sirve para obtener las formas de contacto del cliente
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 16-06-2015
     * @param integer $idpersona
     * @param integer $start
     * @param integer $limit
     */
    public function getFormaContactoPorCliente($idPersona, $start, $limit)
    {
        $qb  = $this->_em->createQueryBuilder();
        $qbC = $this->_em->createQueryBuilder();
        
        $qb ->select('formaContacto.descripcionFormaContacto, personaFormaContacto.valor')
            ->from('schemaBundle:InfoPersona','persona')
            ->from('schemaBundle:InfoPersonaFormaContacto','personaFormaContacto')
            ->from('schemaBundle:AdmiFormaContacto','formaContacto')
            ->where('persona = personaFormaContacto.personaId')
            ->andWhere('personaFormaContacto.formaContactoId = formaContacto')
            ->andWhere('personaFormaContacto.estado = ?1')
            ->andWhere('persona.id = ?2')
            ->andWhere('formaContacto.descripcionFormaContacto like ?3');
        $qb ->setParameter(1, 'Activo');
        $qb ->setParameter(2, $idPersona);
        $qb ->setParameter(3, 'Telefono%');
        
        $qbC->select('count(personaFormaContacto.id)')
            ->from('schemaBundle:InfoPersona','persona')
            ->from('schemaBundle:InfoPersonaFormaContacto','personaFormaContacto')
            ->from('schemaBundle:AdmiFormaContacto','formaContacto')
            ->where('persona = personaFormaContacto.personaId')
            ->andWhere('personaFormaContacto.formaContactoId = formaContacto')
            ->andWhere('personaFormaContacto.estado = ?1')
            ->andWhere('persona.id = ?2')
            ->andWhere('formaContacto.descripcionFormaContacto like ?3');
        $qbC->setParameter(1, 'Activo');
        $qbC->setParameter(2, $idPersona);
        $qbC->setParameter(3, 'Telefono%');
        
        if($start!='')
        {
            $qb->setFirstResult($start);   
        }
            
        if($limit!='')
        {
            $qb->setMaxResults($limit);
        }
        
        //total de objetos
        $total = $qbC->getQuery()->getSingleScalarResult();
        
        //obtener los objetos
        $query = $qb->getQuery();
        $datos = $query->getResult();
        
        $resultado['registros'] = $datos;
        $resultado['total']     = $total;
        
        return $resultado;
    }
    
    /**
     * Funcion que sirve para obtener los contactos del cliente, se deja fijo el
     * limit para que solo devuelva un contacto
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 16-06-2015
     * @param integer $idpersona
     */
    public function getContactosPorCliente($idPersona)
    {
        $sql = "SELECT CONTACTO.NOMBRES || ' ' || CONTACTO.APELLIDOS NOMBRE_CONTACTO 
                FROM INFO_PERSONA CONTACTO WHERE CONTACTO.ID_PERSONA IN (
                  SELECT CONTACTO_ID
                  FROM INFO_PERSONA PERSONA,
                  INFO_PERSONA_EMPRESA_ROL IPER,
                  INFO_PERSONA_CONTACTO IPC
                  WHERE PERSONA.ID_PERSONA = IPER.PERSONA_ID
                  AND IPER.ID_PERSONA_ROL = IPC.PERSONA_EMPRESA_ROL_ID
                  AND IPER.ESTADO = :estado
                  AND PERSONA.ID_PERSONA = :idPersona
                  AND ROWNUM < 2
                )";
        $stmt = $this->_em->getConnection()->prepare($sql);
        $stmt->bindValue('idPersona',   $idPersona);
        $stmt->bindValue('estado',      'Activo');
        $stmt->execute();
        
        $arraResult = $stmt->fetchAll();
        if(count($arraResult)>0)
        {
            $resultado = $arraResult[0];
        }
        else
        {
            $resultado = null;
        }
        
        return $resultado;
    }
    
    public function getPersonaPorLogin($login){
        $qb = $this->_em->createQueryBuilder()
                        ->select('info_persona')
                        ->from('schemaBundle:InfoPersona','info_persona')
                        ->where( "info_persona.login = ?1")
                        ->andWhere("info_persona.estado <>'Eliminado'")
                        ->setParameter(1, $login);
                        //->setParameter(2, $estado);
        
        $query = $qb->getQuery();
        return $query->getOneOrNullResult();
        
    }    
    
    /**
      * getDatosPersonaPorLogin
      *
      * Metodo encargado de obtener la información de la persona asociada a un login.
      * 
      * @param string $strLogin
      *          
      * @return object $objResultado
      * 
      * @author Edson Franco <efranco@telconet.ec>
      * @version 1.0 06-08-2015 
      * 
      */
    public function getDatosPersonaPorLogin($strLogin)
    {
        $arrayResultados = array();
        $objResultado    = null;
        
        $query = $this->_em->createQuery();
        
        $strSelect = 'SELECT ip ';
        $strFrom   = 'FROM schemaBundle:InfoPersona ip,
                           schemaBundle:InfoPersonaEmpresaRol iper ';
        $strWhere  = 'WHERE ip.login = :login 
                        AND ip.id = iper.personaId
                        AND LOWER(iper.estado) = LOWER(:estadoActivo) ';
        
        $query->setParameter('login'        , $strLogin);
        $query->setParameter('estadoActivo' , 'Activo');
        
        $strQuery = $strSelect.$strFrom.$strWhere;
        
        $query->setDQL($strQuery);
        
        $arrayResultados = $query->setMaxResults(1)->getResult();
        
        if( $arrayResultados )
        {
            /*
             * La información retornada por el query viene como arreglo,
             * por lo cual se toma el único valor retornado que está en 
             * la posición '0'
             */
            $objResultado = $arrayResultados[0];
        }
        return $objResultado;
    }
    
    /**
     * Documentación para el método 'generarJsonPersonalExterno'.
     * 
     * Consulta la lista de personal externo.
     * 
     * @param String $strNombres        Nombres del Personal Externo.
     * @param String $strApellidos      Apellidos del Personal Externo.
     * @param String $strIdentificacion Número de identificación del Personal Externo.
     * @param int    $intIdEmpresa      Id de la empresa en sesión.
     * @param String $strEstado         Descripción del estado de la empresa externa.
     * @param int    $intStart          índice inicial de la paginación.
     * @param int    $intLimit          Rango límite de la paginación.
     * 
     * @return Response JSON con la lista de formas de contacto activas.
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
     * @version 1.0 08-09-2015
     */
    public function generarJsonPersonalExterno($strNombres, $strApellidos, $strIdentificacion, $intEmpresaExterna, 
                                               $strEstado,  $intStart,     $intLimit,          $intIdEmpresa)
    {
        $listaPersonalExterno = $this->getRegistrosPersonalExterno($strNombres, $strApellidos, $strIdentificacion, $intEmpresaExterna, 
                                                                   $strEstado,  $intStart,     $intLimit,          $intIdEmpresa);
        $listaRegistrosTotal  = $this->getRegistrosPersonalExterno($strNombres, $strApellidos, $strIdentificacion, $intEmpresaExterna, 
                                                                   $strEstado,  '',            '',                 $intIdEmpresa);
        $arrayRegistros    = array();
        $strEmpresaExterna = 'EMPRESA EXTERNA';
        $strMetaActiva     = 'META ACTIVA';
        $strMetaBruta      = 'META BRUTA';
        $arregloActivo     = array('Activo');
        $arregloEstados    = array('Activo', 'Eliminado');
        $strEliminado      = 'Eliminado';

        if(!empty($listaPersonalExterno))
        {
            $intCantidadRegistros = count($listaRegistrosTotal);
            foreach($listaPersonalExterno as $entityPersonal)
            {
                $strEstado     = $entityPersonal->getEstado();
                $objMetaActiva = $this->getCaracteristicaPersonaEmpresaRol($entityPersonal->getId(), $strMetaActiva,     $arregloActivo, $strEstado);
                $objMetaBruta  = $this->getCaracteristicaPersonaEmpresaRol($entityPersonal->getId(), $strMetaBruta,      $arregloActivo, $strEstado);
                $objEmpresaExt = $this->getCaracteristicaPersonaEmpresaRol($entityPersonal->getId(), $strEmpresaExterna, $arregloEstados, null);
                if($objEmpresaExt)
                {
                    $strRazonSocial = $this->getEmpresaExternaPorPersonaEmpresaRolId($objEmpresaExt->getPersonaEmpresaRolId(), $arregloEstados);
                }
                $objPersona     = $entityPersonal->getPersonaId();
                $objEmpresa     = $entityPersonal->getEmpresaRolId()->getEmpresaCod();
                
                $arrayRegistros[] = array(
                                    'id_persona'             => $objPersona->getId(),
                                    'id_persona_empresa_rol' => $entityPersonal->getId(),
                                    'id_empresa'             => $objEmpresa->getId(),
                                    'nombre_empresa'         => $objEmpresa->getNombreEmpresa() ? $objEmpresa->getNombreEmpresa():'',
                                    'tipo_identificacion'    => $objPersona->getTipoIdentificacion() == "CED" ? 
                                                                "Cédula" : $objPersona->getTipoIdentificacion(),
                                    'identificacion'         => $objPersona->getIdentificacionCliente(),
                                    'nombres'                => $objPersona->getNombres() ? $objPersona->getNombres() : '',
                                    'apellidos'              => $objPersona->getApellidos() ? $objPersona->getApellidos() : '',
                                    'nacionalidad'           => $objPersona->getNacionalidad() == "EXT" ? "Extranjera" :
                                                               ($objPersona->getNacionalidad() == "NAC" ? "Nacional" : ''),
                                    'direccion'              => $objPersona->getDireccion() ? $objPersona->getDireccion() : '',
                                    'estado'                 => $strEstado == $strEliminado ? $strEliminado : $arregloActivo,
                                    'meta_activa'            => $objMetaActiva && $objMetaBruta ? round($objMetaBruta->getValor() * 
                                                                                                       ($objMetaActiva->getValor() / 100)) : 0,
                                    'meta_bruta'             => $objMetaBruta ? $objMetaBruta->getValor() : 0,
                                    'empresa_externa'        => $strRazonSocial,
                                    'action1'                => 'button-grid-show',
                                    'action2'                => $strEstado == 'Eliminado' ? 'icon-invisible' : 'button-grid-edit',
                                    'action3'                => $strEstado == 'Eliminado' ? 'icon-invisible' : 'button-grid-delete'
                                    );
            }
            return '{"total":"' . $intCantidadRegistros . '","encontrados":' . json_encode($arrayRegistros) . '}';
        }
        else
        {
            return '{"total":"0","encontrados":[]}';
        }
    }

    /**
     * Documentación para el método 'getRegistrosPersonalExterno'.
     * 
     * Consulta la lista de personal externo.
     * 
     * @param String $strNombres        Nombres del Personal Externo.
     * @param String $strApellidos      Apellidos del Personal Externo.
     * @param String $strIdentificacion Número de identificación del Personal Externo.
     * @param int    $intIdEmpresa      Id de la empresa en sesión.
     * @param String $strEstado         Descripción del estado de la empresa externa.
     * @param int    $intStart          índice inicial de la paginación.
     * @param int    $intLimit          Rango límite de la paginación.
     * 
     * @return Response JSON con la lista de formas de contacto activas.
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
     * @version 1.0 08-09-2015
     */
    public function getRegistrosPersonalExterno($strNombres, $strApellidos, $strIdentificacion, $intEmpresaExterna, 
                                                $strEstado,  $intStart,     $intLimit,          $intIdEmpresa)
    {
        $objQuery = $this->_em->createQuery();
        $boolBusqueda = false;
        $strWhere = '';
        
        if($strNombres != '')
        {
            $boolBusqueda = true;
            $strNombres   = strtoupper($strNombres);
            $strWhere    .= "AND p.nombres like :nombres ";
            $objQuery->setParameter('nombres', '%' . $strNombres . '%');
        }
        if($strApellidos != '')
        {
            $boolBusqueda = true;
            $strApellidos = strtoupper($strApellidos);
            $strWhere    .= "AND p.apellidos LIKE :apellidos ";
            $objQuery->setParameter('apellidos', '%' . $strApellidos . '%');
        }
        if($strIdentificacion != '')
        {
            $boolBusqueda = true;
            $strWhere    .= "AND p.identificacionCliente like :identificacionCliente ";
            $objQuery->setParameter('identificacionCliente', $strIdentificacion . '%');
        }
        if($intEmpresaExterna != '')
        {
            $boolBusqueda = true;
            $strWhere    .= "AND perc.valor = :empresaExterna ";
            $objQuery->setParameter('empresaExterna', $intEmpresaExterna);
        }
        if($strEstado != '' && $strEstado != "Todos")
        {
            $boolBusqueda = true;
            $strWhere    .= "AND per.estado LIKE :estado ";
            $objQuery->setParameter('estado', $strEstado);
        }
        $strTipoRol = "Personal Externo";
        $strCaracteristicaEmpresaExterna = "EMPRESA EXTERNA";
        
        $sql = "SELECT per
                FROM schemaBundle:InfoPersonaEmpresaRol      per,
                     schemaBundle:InfoPersonaEmpresaRolCarac perc,
                     schemaBundle:InfoPersona        p, 
                     schemaBundle:AdmiCaracteristica ac,
                     schemaBundle:InfoEmpresaRol     er,
                     schemaBundle:InfoEmpresaGrupo   eg,
                     schemaBundle:AdmiRol            r,
                     schemaBundle:AdmiTipoRol        tr 
                WHERE
                     per.personaId   = p.id            AND 
                     per.id = perc.personaEmpresaRolId AND
                     ac.id  = perc.caracteristicaId    AND
                     er.id  = per.empresaRolId         AND
                     eg.id  = er.empresaCod            AND
                     r.id   = er.rolId                 AND
                     tr.id  = r.tipoRolId              AND
                     eg.id  = :idEmpresa               AND
                     tr.descripcionTipoRol        = :tipoRol AND
                     ac.descripcionCaracteristica = :caracteristicaEmpresaExterna
                     $strWhere
                ORDER BY p.nombres, p.apellidos ";
        $objQuery->setParameter('idEmpresa', $intIdEmpresa);
        $objQuery->setParameter('tipoRol', $strTipoRol);
        $objQuery->setParameter('caracteristicaEmpresaExterna', $strCaracteristicaEmpresaExterna);
        
        $objQuery->setDQL($sql);
        $arrayPersonalExterno = null;

        if($intStart != '' && !$boolBusqueda && $intLimit != '')
        {
            $arrayPersonalExterno = $objQuery->setFirstResult($intStart)->setMaxResults($intLimit)->getResult();
        }
        else if($intStart != '' && !$boolBusqueda && $intLimit == '')
        {
            $arrayPersonalExterno = $objQuery->setFirstResult($intStart)->getResult();
        }
        else if(($intStart == '' || $boolBusqueda) && $intLimit != '')
        {
            $arrayPersonalExterno = $objQuery->setMaxResults($intLimit)->getResult();
        }
        else
        {
            $arrayPersonalExterno = $objQuery->getResult();
        }
        return $arrayPersonalExterno;
    }

    /**
     * Documentación para el método 'getCaracteristicaPersonaEmpresaRol'.
     *
     * Metodo que obtiene la característica asociada al personal externo.
     * 
     * @param String $intPersonaEmpresaRolId Id del personal externo.
     * @param Array  $strCaracteristica descricipción de la característica
     * @param String $arregloEstados estado de la persona empresa rol y su característica.
     *          
     * @return object Result entity de tipo InfoPersonaEmpresaRolCarac.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 08-09-2015 
     */
    public function getCaracteristicaPersonaEmpresaRol($intPersonaEmpresaRolId, $strCaracteristica, $arregloEstados, $strEstadoPersonal = null)
    {
        if($strEstadoPersonal == 'Eliminado' || $intPersonaEmpresaRolId == 0)
        {
            return null;
        }
        $objQuery = $this->_em->createQuery();
        $strSQL = " SELECT perc
                    FROM schemaBundle:InfoPersonaEmpresaRolCarac perc,
                         schemaBundle:InfoPersonaEmpresaRol      per,
                         schemaBundle:AdmiCaracteristica         ac
                    WHERE 
                         perc.personaEmpresaRolId     =  per.id                     AND 
                         perc.caracteristicaId        =  ac.id                      AND 
                         per.id                       = :personaEmpresaRolId       AND
                         ac.descripcionCaracteristica = :descripcionCaracteristica AND      
                         perc.estado                 in (:estados)
                    ORDER BY perc.feCreacion desc";
        $objQuery->setParameter('personaEmpresaRolId', $intPersonaEmpresaRolId);
        $objQuery->setParameter('estados', $arregloEstados);
        $objQuery->setParameter('descripcionCaracteristica', $strCaracteristica);
        $objQuery->setDQL($strSQL);
        return $objQuery->getOneOrNullResult();
    }
    

    /**
     * Documentación para el método 'getResultadoCaracteristicaPersonaEmpresaRolMensual'.
     *
     * Metodo que obtiene las características de la personaEmpresaRol dentro de un mes específico.
     * 
     * @param Array $arrayParametros['PERSONAEMPRESAROLID'] Int    Id de la persona empresa rol.
     *              $arrayParametros['CARACTERISTICA']      String Nombre de la característica a buscar(META ACTIVA, META BRUTA). 
     *              $arrayParametros['MES']                 String Fecha para la consulta en formato Mes-Año (ddyyyy).
     *              $arrayParametros['ESTADO']              String Estado de filtrado de la búsqueda.
     * 
     * @return object Result entity de tipo InfoPersonaEmpresaRolCarac.
     * 
     * costoQuery: 5
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 08-09-2015 
     */
    public function getResultadoCaracteristicaPersonaEmpresaRolMensual($arrayParametros)
    {
        $rsmBuilder = new ResultSetMappingBuilder($this->_em);
        $ntvQuery   = $this->_em->createNativeQuery(null, $rsmBuilder);
        $strEstado  = '';
        
        if(isset($arrayParametros['ESTADO']))
        {
            $strEstado = 'AND PERC.ESTADO = :ESTADO';
            $ntvQuery->setParameter('ESTADO', $arrayParametros['ESTADO']);
        }
        
        $strSQL = " SELECT * FROM (
                    SELECT PERC.*
                    FROM INFO_PERSONA_EMPRESA_ROL_CARAC PERC
                    INNER JOIN ADMI_CARACTERISTICA      AC  ON  AC.ID_CARACTERISTICA = PERC.CARACTERISTICA_ID
                                                            AND AC.DESCRIPCION_CARACTERISTICA = :CARACTERISTICA
                    WHERE PERC.FE_CREACION BETWEEN TRUNC( TO_DATE(:MES, 'MMYYYY')) AND TRUNC(LAST_DAY(TO_DATE(:MES, 'MMYYYY'))) + 0.999988403
                    AND PERC.PERSONA_EMPRESA_ROL_ID = :PERSONAEMPRESAROLID
                    $strEstado
                    ORDER BY PERC.FE_CREACION DESC, PERC.ID_PERSONA_EMPRESA_ROL_CARACT DESC
                    ) WHERE ROWNUM = 1";
        
        $rsmBuilder->addScalarResult('CARACTERISTICA_ID', 'caracteristicaId', 'integer');
        $rsmBuilder->addScalarResult('VALOR',             'valor',            'integer');
        $rsmBuilder->addScalarResult('FE_CREACION',       'feCreacion',       'string');
        $rsmBuilder->addScalarResult('ESTADO',            'estado',           'string');

        $ntvQuery->setParameter('PERSONAEMPRESAROLID', $arrayParametros['PERSONAEMPRESAROLID']);
        $ntvQuery->setParameter('CARACTERISTICA',      $arrayParametros['CARACTERISTICA']);
        $ntvQuery->setParameter('MES',                 $arrayParametros['MES']);
        return $ntvQuery->setSQL($strSQL)->getResult();
    }
    
    /**
     * Documentación para el método 'getEmpresaExternaPorPersonaEmpresaRolId'.
     *
     * Método que obtiene la lista de empresas externas asociadas la PersonaEmpresaRol.
     * 
     * @param int    $intPersonaEmpresaExternaId estado para el filtro de la consulta.
     * @param String $arregloEstado estado para el filtro de la consulta.
     *          
     * @return object Result Listado de empresas externas.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 08-09-2015 
     */
    public function getEmpresaExternaPorPersonaEmpresaRolId($intPersonaEmpresaExternaId, $arregloEstado)
    {
        $rsmBuilder = new ResultSetMappingBuilder($this->_em);
        $ntvQuery = $this->_em->createNativeQuery(null, $rsmBuilder);
        $strCaracteristica = 'EMPRESA EXTERNA';
        
        $strSQL = " SELECT P.RAZON_SOCIAL 
                    FROM INFO_PERSONA_EMPRESA_ROL PER,
                         INFO_PERSONA P
                    WHERE P.ID_PERSONA = PER.PERSONA_ID AND
                          PER.ID_PERSONA_ROL IN ( SELECT PERC.VALOR 
                                                  FROM INFO_PERSONA_EMPRESA_ROL_CARAC PERC, 
                                                       INFO_PERSONA_EMPRESA_ROL PER,
                                                       INFO_PERSONA P,
                                                       ADMI_CARACTERISTICA CA 
                                                  WHERE PERC.PERSONA_EMPRESA_ROL_ID = PER.ID_PERSONA_ROL   AND
                                                        P.ID_PERSONA = PER.PERSONA_ID AND
                                                        CA.ID_CARACTERISTICA = PERC.CARACTERISTICA_ID      AND 
                                                        CA.DESCRIPCION_CARACTERISTICA = :caracteristica AND 
                                                        PERC.ESTADO                  in (:estados)         AND 
                                                        PERC.PERSONA_EMPRESA_ROL_ID   = :intIdEmpresa )";
        $rsmBuilder->addScalarResult('RAZON_SOCIAL', 'RAZON_SOCIAL', 'string');

        $ntvQuery->setParameter('estados', $arregloEstado);
        $ntvQuery->setParameter('intIdEmpresa', $intPersonaEmpresaExternaId);
        $ntvQuery->setParameter('caracteristica', $strCaracteristica);
        $objPersona = $ntvQuery->setSQL($strSQL)->getOneOrNullResult();
        return ($objPersona != null) ? $objPersona['RAZON_SOCIAL'] : '';
    }
    
    /**
     * Documentación para el método 'getListaEmpresasExternas'.
     *
     * Método encargado de obtener la lista de empresas externas procesadas para su presentación.
     * 
     * @param String $strIdentificacion Número de RUC de la empresa.
     * @param String $strNombre         Nombre Comercial de la empresa externa.
     * @param String $strRazonSocial    Razón Social de la empresa externa.
     * @param int    $intIdEmpresa      Id de la empresa en sesión.
     * @param String $strEstado         Descripción del estado de la empresa externa.
     * @param int    $intStart          índice inicial de la paginación.
     * @param int    $intLimit          Rango límite de la paginación.
     *          
     * @return String $strResultadoJson Cadena JSON con la lista de empresas externas.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 08-09-2015 
     * 
     */
    public function getListaEmpresasExternas($strIdentificacion, $strNombre, $strRazonSocial, $intIdEmpresa, $strEstado, $intStart, $intLimit)
    {
        $arrayEmpresasExternas = array();
        $listaEmpresasExternas = $this->getRegistrosEmpresaExterna($strIdentificacion, $strNombre, $strRazonSocial, $intIdEmpresa, $strEstado, 
                                                                   $intStart, $intLimit);
        $listaRegistrosTotal   = $this->getRegistrosEmpresaExterna($strIdentificacion, $strNombre, $strRazonSocial, $intIdEmpresa, $strEstado, 
                                                                   '', '');
        if (!empty($listaEmpresasExternas)) 
        {
            $intTotalRegistros = count($listaRegistrosTotal);
            foreach($listaEmpresasExternas as $entityEmpresa)
            {
                $objPersona = $entityEmpresa->getPersonaId();
                $objEmpresa = $entityEmpresa->getEmpresaRolId()->getEmpresaCod();
                $arrayEmpresasExternas[] = array(
                                           'id_persona'             => $objPersona->getId(), 
                                           'id_persona_empresa_rol' => $entityEmpresa->getId(),
                                           'id_empresa'             => $objEmpresa->getId(),
                                           'nombre_empresa'         =>($objEmpresa->getNombreEmpresa() ? $objEmpresa->getNombreEmpresa() : ''),
                                           'tipo_identificacion'    =>($objPersona->getTipoIdentificacion() == "CED" ? 
                                                                       "Cédula" : $objPersona->getTipoIdentificacion()),
                                           'identificacion'         =>($objPersona->getIdentificacionCliente() ? 
                                                                       $objPersona->getIdentificacionCliente() : 0),
                                           'nombres'                =>($objPersona->getNombres() ? $objPersona->getNombres():''),
                                           'razon_social'           =>($objPersona->getRazonSocial() ? $objPersona->getRazonSocial():''),
                                           'nacionalidad'           =>($objPersona->getNacionalidad() == "EXT" ? "Extranjera" : "Nacional"),
                                           'direccion'              =>($objPersona->getDireccion() ? $objPersona->getDireccion() : ''),
                                           'estado'                 =>($entityEmpresa->getEstado() == 'Eliminado' ? 
                                                                       $entityEmpresa->getEstado() : 'Activo'));
            }
            return '{"total":"' . $intTotalRegistros . '","encontrados":' . json_encode($arrayEmpresasExternas) . '}';
        }
        else
        {
            return '{"total":"0","encontrados":[]}';
        }
    }

    /**
     * Documentación para el método 'getRegistrosEmpresaExterna'.
     *
     * Método privado que obtiene la lista de empresas externas filtradas y paginadas.
     * 
     * @param String $strIdentificacion Número de RUC de la empresa.
     * @param String $strNombre         Nombre Comercial de la empresa externa.
     * @param String $strRazonSocial    Razón Social de la empresa externa.
     * @param int    $intIdEmpresa      Id de la empresa en sesión.
     * @param String $strEstado         Descripción del estado de la empresa externa.
     * @param int    $intStart          índice inicial de la paginación.
     * @param int    $intLimit          Rango límite de la paginación.
     *          
     * @return Array $arrayEmpresasExternas Listado de empresas externas.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 08-09-2015 
     */
    private function getRegistrosEmpresaExterna($strIdentificacion, $strNombre, $strRazonSocial, $intIdEmpresa, $strEstado, $intStart, $intLimit)
    {
        $objQuery = $this->_em->createQuery();
        $boolBusqueda = false;
        $strWhere = '';
        
        if($strIdentificacion != '')
        {
            $boolBusqueda = true;
            $strWhere .= "AND p.identificacionCliente LIKE :identificacionCliente ";
            $objQuery->setParameter('identificacionCliente', $strIdentificacion . '%');
        }
        if($strNombre != '')
        {
            $boolBusqueda = true;
            $strNombre = strtoupper($strNombre);
            $strWhere .= "AND p.nombres LIKE :nombres ";
            $objQuery->setParameter('nombres', '%' . $strNombre . '%');
        }
        if($strRazonSocial != '')
        {
            $boolBusqueda = true;
            $strRazonSocial = strtoupper($strRazonSocial);
            $strWhere .= "AND p.razonSocial LIKE :razonSocial ";
            $objQuery->setParameter('razonSocial', '%' . $strRazonSocial . '%');
        }
        if($strEstado != '' && $strEstado != "Todos")
        {
            $boolBusqueda = true;
            $strWhere .= "AND per.estado LIKE :estado ";
            $objQuery->setParameter('estado', $strEstado);
        }
        $strTipoRol = "Empresa Externa";
        $strTipoIdentificacion = "RUC";

        $strSQL = " SELECT per  
                    FROM 
                        schemaBundle:InfoPersonaEmpresaRol per, 
                        schemaBundle:InfoPersona p, 
                        schemaBundle:InfoEmpresaRol er,
                        schemaBundle:InfoEmpresaGrupo eg,
                        schemaBundle:AdmiRol r,
                        schemaBundle:AdmiTipoRol tr
                    WHERE 
                        p.id  = per.personaId    AND 
                        er.id = per.empresaRolId AND
                        eg.id = er. empresaCod   AND
                        r.id  = er. rolId        AND
                        tr.id = r.  tipoRolId    AND
                        eg.id = :idEmpresa       AND
                        p.tipoIdentificacion  = :tipoIdentificacion AND
                        r.descripcionRol      = :tipoRol            AND
                        tr.descripcionTipoRol = :tipoRol             
                        $strWhere
                    ORDER BY p.nombres, p.apellidos";
        
        $objQuery->setParameter('idEmpresa', $intIdEmpresa);
        $objQuery->setParameter('tipoIdentificacion', $strTipoIdentificacion);
        $objQuery->setParameter('tipoRol', $strTipoRol);

        $arregloEmpresasExternas = null;
        $objQuery->setDQL($strSQL);
        
        if($intStart != '' && !$boolBusqueda && $intLimit != '')
        {
            $arregloEmpresasExternas = $objQuery->setFirstResult($intStart)->setMaxResults($intLimit)->getResult();
        }
        else if($intStart != '' && !$boolBusqueda && $intLimit == '')
        {
            $arregloEmpresasExternas = $objQuery->setFirstResult($intStart)->getResult();
        }
        else if(($intStart == '' || $boolBusqueda) && $intLimit != '')
        {
            $arregloEmpresasExternas = $objQuery->setMaxResults($intLimit)->getResult();
        }
        else
        {
            $arregloEmpresasExternas = $objQuery->getResult();
        }
        return $arregloEmpresasExternas;
    }
    
    /**
     * Documentación para el método 'getEmpresasExternas'.
     *
     * Método encargado de obtener la lista de empresas externas por estado, 
     * si se envía el IdPersonaEmpresaRol se obtiene solo el registro relacionado.
     * 
     * @param Integer $intIdEmpresa id de la empresa grupo asociada a la empresa externa.
     * @param String  $strEstado estado para el filtro de la consulta.
     * @param String  $intIdPersona id de la persona.
     *          
     * @return Array Result Listado de empresas externas.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 08-09-2015 
     */
    public function getEmpresasExternas($intIdEmpresa, $strEstado, $intIdPersona = null)
    {
        $objQuery = $this->_em->createQuery();
        $strRolTipoRolDesc = 'Empresa Externa';
        $strTipoIdentificacion = 'RUC';
        $strSqlEstado = '';
        $strSqlPersona = '';
        
        if($strEstado != null && $strEstado != 'Todos')
        {
            $strSqlEstado = "p.estado   = :estado AND per.estado = :estado AND ";
            $objQuery->setParameter('estado', $strEstado);
        }
        if($intIdPersona != null)
        {
            $strSqlPersona = "p.id = :idPersona AND ";
            $objQuery->setParameter('idPersona', $intIdPersona);
        }
        $strSQL = " SELECT per as objPerEmpRol, p.razonSocial
                    FROM 
                        schemaBundle:InfoPersonaEmpresaRol per, 
                        schemaBundle:InfoPersona p, 
                        schemaBundle:InfoEmpresaRol er,
                        schemaBundle:InfoEmpresaGrupo eg,
                        schemaBundle:AdmiRol r,
                        schemaBundle:AdmiTipoRol tr
                    WHERE 
                        p.id  = per.personaId    AND 
                        er.id = per.empresaRolId AND 
                        eg.id = er. empresaCod   AND 
                        r.id  = er. rolId        AND 
                        tr.id = r.  tipoRolId    AND 
                        eg.id = :idEmpresa       AND 
                        $strSqlPersona
                        $strSqlEstado
                        p.tipoIdentificacion  = :tipoIdentificacion AND 
                        r.descripcionRol      = :rolTipoRol         AND 
                        tr.descripcionTipoRol = :rolTipoRol 
                    ORDER BY p.razonSocial";
        $objQuery->setParameter('idEmpresa', $intIdEmpresa);
        $objQuery->setParameter('tipoIdentificacion', $strTipoIdentificacion);
        $objQuery->setParameter('rolTipoRol', $strRolTipoRolDesc);
        
        $objQuery->setDQL($strSQL);
        return $objQuery->getResult();
    }
    
    /**
     * getInfoPersonaByCriterios
     *
     * Método que retorna la información de una persona dependiendo de los criterios enviados por el usuario                                  
     *
     * @param array  $arrayParametros ['intCodEmpresa', 'intIdPersona', 'strNombreTipoRol', 'estados']
     * @return array $arrayPersona ['id', 'idPersonaEmpresaRol', 'idPersona', 'razonSocial', 'nombres', 'apellidos', 'identificacion', 
     *                              'direccion', 'estado']
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 04-11-2015
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 25-04-2016 - Se modifica para que retorne la información de una persona ordenada por el estado y seleccionando el primer registro
     */
    public function getInfoPersonaByCriterios( $arrayParametros )
    { 
        $arrayPersona     = array();
        $intCodEmpresa    = $arrayParametros['intCodEmpresa'];
        $intIdPersona     = $arrayParametros['intIdPersona'];
        $strNombreTipoRol = $arrayParametros['strNombreTipoRol'];
        
        $query = $this->_em->createQuery();
        
        $strSelect  = "SELECT iper ";
        $strFrom    = "FROM schemaBundle:InfoPersona ip, 
                            schemaBundle:InfoPersonaEmpresaRol iper,
                            schemaBundle:InfoEmpresaRol ier, 
                            schemaBundle:AdmiRol ar, 
                            schemaBundle:AdmiTipoRol atr ";
        $strWhere   = "WHERE ip.id = iper.personaId
                         AND iper.empresaRolId = ier.id
                         AND ier.rolId = ar.id 
                         AND ar.tipoRolId = atr.id  
                         AND atr.descripcionTipoRol = :strNombreTipoRol
                         AND ip.id = :intIdPersona 
                         AND ier.empresaCod = :intCodEmpresa
                         AND iper.estado IN ( :estados )
                         AND ier.estado IN ( :estados ) ";
        $strOrderBy = "ORDER BY iper.estado, iper.id DESC ";
        
        $query->setParameter('intIdPersona'    , $intIdPersona);
        $query->setParameter('intCodEmpresa'   , $intCodEmpresa);
        $query->setParameter('strNombreTipoRol', $strNombreTipoRol);
        $query->setParameter('estados'         , array_values($arrayParametros['estados']));
        
        $strSql = $strSelect.$strFrom.$strWhere.$strOrderBy;

        $query->setDQL($strSql);
        $query->setMaxResults(1); 
        
        $objResultado =  $query->getOneOrNullResult();

        $arrayOficinaEmpresaGrupo = $this->getOficinaParaSession($intCodEmpresa, $intIdPersona, $strNombreTipoRol);
        $arrayPersona             = $arrayOficinaEmpresaGrupo;
        
        if( $objResultado && count($objResultado) > 0 )
        {
            $objPersona = $objResultado->getPersonaId();
			
            $arrayPersona['id']                  = $objPersona->getId();
            $arrayPersona['idPersonaEmpresaRol'] = $objResultado->getId();
            $arrayPersona['idPersona']           = $objPersona->getId();
            $arrayPersona['razonSocial']         = $objPersona->getRazonSocial();
            $arrayPersona['nombres']             = $objPersona->getNombres();
            $arrayPersona['apellidos']           = $objPersona->getApellidos();
            $arrayPersona['identificacion']      = $objPersona->getIdentificacionCliente();
            $arrayPersona['direccion']           = $objPersona->getDireccion();
            $arrayPersona['estado']              = $objPersona->getEstado();
        }
        else
        {
            $arrayPersona['id']                  = "";
            $arrayPersona['idPersonaEmpresaRol'] = "";
            $arrayPersona['idPersona']           = "";
            $arrayPersona['razonSocial']         = "";
            $arrayPersona['nombres']             = "";
            $arrayPersona['apellidos']           = "";
            $arrayPersona['identificacion']      = "";
            $arrayPersona['direccion']           = "";
            $arrayPersona['estado']              = "";
        }

        return $arrayPersona;
    }

    /**
    * Permite generar el Json de los clientes segun los parametros de busqueda
    *
    * @param $srtEstado     Parametro correspondiente al estado a filtrar
    * @param $intIdEmpresa  Parametro correspondiente a la empresa a filtrar los clientes
    * @param $strFilter     Parametro correspondiente al cliente a verificar
     *  
    * @author Gina Villalba <gvillalba@telconet.ec>
    * @version 1.0 24-12-2015
    */
    public function getJsonClientes($srtEstado, $intIdEmpresa, $strFilter)
    {
        $arrayEncontrados       = array();
        $arrayResultado         = $this->findListadoClientesPorEmpresaPorEstado($srtEstado, $intIdEmpresa, $strFilter);
        $arrayPersonas          = $arrayResultado['resultado'];
        $strInformacionCliente  ="";
        
        foreach($arrayPersonas as $objPersona):

            if($objPersona->getNombres() != "" && $objPersona->getApellidos() != "")
            {
                $strInformacionCliente = $objPersona->getNombres() . " " . $objPersona->getApellidos();
            }

            if($objPersona->getRazonSocial() != "")
            {
                $strInformacionCliente = $objPersona->getRazonSocial();
            }


            $arrayEncontrados[] = array(
                'idCliente'     => $objPersona->getId(),
                'descripcion'   => $strInformacionCliente,
            );
        endforeach;

        $arrayRespuesta = array('clientes' => $arrayEncontrados);
        $jsonData       = json_encode($arrayRespuesta);

        return $jsonData;
    }

    /**
     * getChoferesVehiculoAsignacionOperativa
     *
     * Método que retorna los choferes con su cuadrilla si es que estuviera asignado a alguna dependiendo de los criterios enviados por el usuario                                    
     *      
     * @param array $arrayParametros
     * 
     * @return array $arrayResultados
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 05-01-2016
     */
    public function getChoferesConySinAsignacionCuadrilla($arrayParametros)
    {
       
        $arrayResultados  = array();
        
        $query      = $this->_em->createQuery();
        $queryCount = $this->_em->createQuery();
        
       
        $strDescripcionRol              = 'Chofer';
        $strEstadoActivo                = 'Activo';
        $strEstadoPrestado              = 'Prestado';
        $strDescripcionCaracteristica   = 'CARGO';
        $strSelect         = "SELECT DISTINCT (per.id) as intIdPersonaEmpresaRol,p.identificacionCliente as identificacionChofer,p.nombres,
                                               p.apellidos,ac.id as idCuadrilla, ac.codigo as codigoCuadrilla,ac.nombreCuadrilla,
                                               ac.turnoInicio,ac.turnoFin,ac.zonaId as idZonaCuadrilla,ac.departamentoId as idDepartamentoCuadrilla,
                                               ac.tareaId as idTareaCuadrilla,ac.estado as estadoCuadrilla,ac.coordinadorPrincipalId,
                                               ac.coordinadorPrestadoId ";
        $strSelectCount    = "SELECT COUNT(DISTINCT per.id) ";
        $strQuerySinSelect = "FROM schemaBundle:InfoPersonaEmpresaRol  per
                              LEFT OUTER JOIN schemaBundle:AdmiCuadrilla ac WITH per.cuadrillaId = ac.id
                              INNER JOIN schemaBundle:InfoPersona p WITH per.personaId = p.id 
                              INNER JOIN schemaBundle:InfoEmpresaRol er WITH per.empresaRolId = er.id 
                              INNER JOIN schemaBundle:AdmiRol r WITH er.rolId =r.id 
                              INNER JOIN schemaBundle:InfoPersonaEmpresaRolCarac perc WITH per.id = perc.personaEmpresaRolId
                              INNER JOIN schemaBundle:Admicaracteristica c WITH c.id = perc.caracteristicaId 
                              WHERE (r.descripcionRol= :descripcionRol
                                     OR (perc.valor= :descripcionRol AND c.descripcionCaracteristica = :descripcionCaracteristica 
                                         AND c.estado = :estadoActivo AND perc.estado = :estadoActivo))
                                     AND (
                                           (
                                            ac.id IS NOT NULL 
                                            AND ( ac.estado like :estadoActivo 
                                                  OR ( ac.estado like :estadoPrestado
                                                       AND ac.coordinadorPrestadoId = :idPersonaEmpresaRolSession 
                                                       )
                                                 )
                                           )
                                           OR ac.id IS NULL
                                          ) 
                                     AND (
                                            (
                                            ac.id IS NOT NULL 
                                            AND ac.coordinadorPrincipalId = :intCoordinador 
                                            OR ac.coordinadorPrestadoId = :intCoordinador 
                                            ) 
                                            OR ac.id IS NULL
                                          ) ";
        $strWhereBusqueda  = "";    
        $strOrderBy        = " ORDER BY p.nombres,p.apellidos ";
       
        $query->setParameter('idPersonaEmpresaRolSession' , $arrayParametros['idPersonaEmpresaRolSession']);
        $queryCount->setParameter('idPersonaEmpresaRolSession' , $arrayParametros['idPersonaEmpresaRolSession']);
        
        $query->setParameter('intCoordinador' , $arrayParametros['intCoordinadorPrincipal']);
        $queryCount->setParameter('intCoordinador' , $arrayParametros['intCoordinadorPrincipal']);
        
        $query->setParameter('descripcionRol' , $strDescripcionRol);
        $queryCount->setParameter('descripcionRol' , $strDescripcionRol);
        
        $query->setParameter('estadoActivo' , $strEstadoActivo);
        $queryCount->setParameter('estadoActivo' , $strEstadoActivo);
        
        $query->setParameter('estadoPrestado' , $strEstadoPrestado);
        $queryCount->setParameter('estadoPrestado' , $strEstadoPrestado);
        
        $query->setParameter('descripcionCaracteristica' , $strDescripcionCaracteristica);
        $queryCount->setParameter('descripcionCaracteristica' , $strDescripcionCaracteristica);
        
        
        
        
        if( isset($arrayParametros['criterios']) )
        {
            if( isset($arrayParametros['criterios']['nombre']) )
            {
                if($arrayParametros['criterios']['nombre'])
                {
                    $strWhereBusqueda .= 'AND ac.nombreCuadrilla LIKE :nombre ';
                    
                    $query->setParameter('nombre', '%'.trim($arrayParametros['criterios']['nombre']).'%');

                    $queryCount->setParameter('nombre', '%'.trim($arrayParametros['criterios']['nombre']).'%');
                }  
            } 
            
            
            if( isset($arrayParametros['criterios']['nombresChofer']) )
            {
                if($arrayParametros['criterios']['nombresChofer'])
                {
                    
                    $strWhereBusqueda .= 'AND p.nombres LIKE :nombresChofer ';
                    
                    $query->setParameter('nombresChofer', '%'.  strtoupper(trim($arrayParametros['criterios']['nombresChofer'])).'%');

                    $queryCount->setParameter('nombresChofer', '%'.strtoupper(trim($arrayParametros['criterios']['nombresChofer'])).'%');
                }  
            }
            
            if( isset($arrayParametros['criterios']['apellidosChofer']) )
            {
                if($arrayParametros['criterios']['apellidosChofer'])
                {
                    
                    $strWhereBusqueda .= 'AND p.apellidos LIKE :apellidosChofer ';
                    
                    $query->setParameter('apellidosChofer', '%'.strtoupper(trim($arrayParametros['criterios']['apellidosChofer'])).'%');

                    $queryCount->setParameter('apellidosChofer', '%'.strtoupper(trim($arrayParametros['criterios']['apellidosChofer'])).'%');
                }  
            }
            
            if( isset($arrayParametros['criterios']['identificacionChofer']) )
            {
                if($arrayParametros['criterios']['identificacionChofer'])
                {
                    $strWhereBusqueda .= 'AND p.identificacionCliente LIKE :identificacionChofer ';
                    
                    $query->setParameter('identificacionChofer', '%'.trim($arrayParametros['criterios']['identificacionChofer']).'%');

                    $queryCount->setParameter('identificacionChofer', '%'.trim($arrayParametros['criterios']['identificacionChofer']).'%');
                }  
            }
            
        }
        
        
        $strSql   = $strSelect.$strQuerySinSelect.$strWhereBusqueda.$strOrderBy;
        $query->setDQL($strSql);
        
        if( isset($arrayParametros['intStart']) )
        {
            if($arrayParametros['intStart'])
            {
                $query->setFirstResult($arrayParametros['intStart']);
            }  
        }
        
        if( isset($arrayParametros['intLimit']) )
        {
            if($arrayParametros['intLimit'])
            {
                $query->setMaxResults($arrayParametros['intLimit']);
            }
        }
        
        $arrayTmpDatos = $query->getResult();

        
        $strSqlCount = $strSelectCount.$strQuerySinSelect.$strWhereBusqueda;
        $queryCount->setDQL($strSqlCount); 
        $intTotal = $queryCount->getSingleScalarResult();
        
        $arrayResultados['registros'] = $arrayTmpDatos;
        $arrayResultados['total']     = $intTotal;
        
        
        return $arrayResultados;
        
        
    }
    
    /**
    * Documentación para el método 'generarJsonEmpleados'
    * 
    * Esta funcion retorna los empleados por empresa, departamento y cantón
    * 
    * @author Lizbeth Cruz <mlcruz@telconet.ec>
    * @version 1.0 09-12-2015    
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.1 17-02-2021 - Se retorna el campo login
    *
    * @param array $arrayParametros ['codEmpresa', 'intStart', 'intLimit', 'criterios'] 
    * 
    * @return array $objResultado  Objeto en formato JSON    
    * 
    */
    public function getJSONEmpleados($arrayParametros)
    {
        $arrayEncontrados = array();
        $arrayResultado   = $this->getResultadoEmpleados($arrayParametros);
        $intTotal         = $arrayResultado['total'];
        $arrayRegistros   = $arrayResultado['resultado'];
        $strEstadoNaf     = "";

        if ($arrayRegistros) {

            foreach ($arrayRegistros as $data)
            {
                $strLogin = "";
                if(!empty($data['idPersonaEmpresaRol']))
                {
                    $objInfoPersona = $this->_em->getRepository('schemaBundle:InfoPersona')
                                                ->findOneBy( array('id'      => $data['idPersona'],
                                                                   'estado'  => array("Activo","Pendiente")) );

                    if(is_object($objInfoPersona))
                    {
                        $strLogin = $objInfoPersona->getLogin();
                    }
                }

                $arrayEncontrados[]=array(  'id_persona'             => $data['idPersona'],
                                            'login'                  => $strLogin,
                                            'id_persona_empresa_rol' => $data['idPersonaEmpresaRol'],
                                            'id_empresa'             => $data['idEmpresa'],
                                            'tipo_identificacion'    => ($data['tipoIdentificacion'] == "CED" ? "Cedula" : 
                                                                                                                $data['tipoIdentificacion']),
                                            'identificacion'         => ($data['identificacion'] ? $data['identificacion'] : 0),
                                            'nombres'                => ($data['nombres'] ? $data['nombres']:""),
                                            'apellidos'              => ($data['apellidos'] ? $data['apellidos']:""),
                                            'nacionalidad'           => ($data['nacionalidad'] == "EXT" ? "Extranjera" : 
                                                                        ($data['nacionalidad'] == "NAC" ? "Nacional" : "")),
                                            'direccion'              => ($data['direccion'] ? $data['direccion'] : ""),
                                            'id_departamento'        => ($data['idDepartamento'] ? $data['idDepartamento'] : ""),
                                            'nombre_departamento'    => ($data['nombreDepartamento'] ? $data['nombreDepartamento'] : ""),
                                            'id_canton'              => ($data['idCanton'] ? $data['idCanton'] : ""),
                                            'nombre_canton'          => ($data['nombreCanton'] ? $data['nombreCanton'] : ""),
                                            'idDocumentoRelacion'    => ($data['idDocumentoRelacion'] ? $data['idDocumentoRelacion'] : ""),
                                            'idDocumento'            => ($data['idDocumento'] ? $data['idDocumento'] : ""),
                                            'action1'                => 'button-grid-show',
                                            'action2'                => (strtolower(trim($data['estado']))!=strtolower('ACTIVO') ? 
                                                                        'icon-invisible':'button-grid-edit'),
                                            'action3'                => (strtolower(trim($data['estado']))!=strtolower('ACTIVO') ? 
                                                                        'icon-invisible':'button-grid-delete'),
                                            'action4'                => (strtolower(trim($data['estado']))!=strtolower('ACTIVO') ? 
                                                                        'icon-invisible':'button-grid-pdf')
                                         );


            }

            if($intTotal == 0)
            {
                $objResultado= array('total' => 1 ,
                                 'encontrados' => array(
                                                        'id_persona' => 0,
                                                        'login'      => 'Ninguno',
                                                        'id_persona_empresa_rol' => 0,
                                                        'id_empresa' => 0,
                                                        'tipo_identificacion' => 'Ninguno',
                                                        'identificacion' => 'Ninguno',
                                                        'nombres' => 'Ninguno',
                                                        'apellidos' => 'Ninguno',
                                                        'nacionalidad' => 'Ninguno',
                                                        'direccion' => 'Ninguno',
                                                        'id_departamento' => 0,
                                                        'nombre_departamento' => 'Ninguno',
                                                        'id_canton' => 0,
                                                        'nombre_canton' => 'Ninguno',
                                                        'estado' => 'Ninguno',
                                                        'action1' => 'button-grid-show',
                                                        'action2' => 'icon-invisible',
                                                        'action3' => 'icon-invisible',
                                                        'action4' => 'icon-invisible'

                                                        ));

                $objResultado = json_encode( $objResultado);
                return $objResultado;

            }
            else
            {

                $objData =json_encode($arrayEncontrados);
                $objResultado= '{"total":"'.$intTotal.'","encontrados":'.$objData.'}';
                return $objResultado;
            }
        }
        else
        {
            $objResultado= '{"total":"0","encontrados":[]}';
            return $objResultado;
        }

    }
    
    /**
     * Documentación para el método 'getResultadoEmpleados'
     * 
     * Esta funcion ejecuta el Query que retorna los empleados de determinada empresa, departamento y cantón 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 09-12-2015 
     * 
     * @param array $arrayParametros ['codEmpresa', 'intStart', 'intLimit', 'criterios'] 
     * 
     * @return array $arrayRespuesta ['registros','total']
     * 
     */
    public function getResultadoEmpleados($arrayParametros){

        $arrayRespuesta['total']     = 0;
        $arrayRespuesta['resultado'] = "";

        try
        {   
            $objQuery        = $this->_em->createQuery();
            $objQueryCont    = $this->_em->createQuery();  

            $strWhere = "";
            if(isset($arrayParametros['criterios']))
            {   
                if(isset($arrayParametros['criterios']['region']) )
                {
                    if($arrayParametros['criterios']['region'])
                    {
                        $strWhere .= 'AND c.region = :region ';        
                        $objQuery->setParameter('region', $arrayParametros['criterios']['region']);
                        $objQueryCont->setParameter('region', $arrayParametros['criterios']['region']);

                    }
                }
                
                if(isset($arrayParametros['criterios']['nombres']) )
                {
                    if($arrayParametros['criterios']['nombres'])
                    {
                        $strWhere .= 'AND p.nombres LIKE :nombres ';        
                        $objQuery->setParameter('nombres', '%'.strtoupper($arrayParametros['criterios']['nombres']).'%');
                        $objQueryCont->setParameter('nombres', '%'.strtoupper($arrayParametros['criterios']['nombres']).'%');

                    }
                }
                if(isset($arrayParametros['criterios']['apellidos']) )
                {
                    if($arrayParametros['criterios']['apellidos'])
                    {
                        $strWhere .= 'AND p.apellidos LIKE :apellidos ';        
                        $objQuery->setParameter('apellidos', '%'.strtoupper($arrayParametros['criterios']['apellidos']).'%');
                        $objQueryCont->setParameter('apellidos', '%'.strtoupper($arrayParametros['criterios']['apellidos']).'%');

                    }
                }

                if(isset($arrayParametros['criterios']['identificacion']) )
                {
                    if($arrayParametros['criterios']['identificacion'])
                    {
                        $strWhere .= 'AND p.identificacionCliente LIKE :identificacion ';        
                        $objQuery->setParameter('identificacion', '%'.$arrayParametros['criterios']['identificacion'].'%');
                        $objQueryCont->setParameter('identificacion', '%'.$arrayParametros['criterios']['identificacion'].'%');

                    }
                }
                if(isset($arrayParametros['criterios']['departamento']))
                {
                    if($arrayParametros['criterios']['departamento'])
                    {
                        if($arrayParametros['criterios']['departamento']!="Todos")
                        {
                            $strWhere .= 'AND per.departamentoId = :idDepartamento ';        
                            $objQuery->setParameter('idDepartamento', $arrayParametros['criterios']['departamento']);
                            $objQueryCont->setParameter('idDepartamento', $arrayParametros['criterios']['departamento']);

                        }
                    }
                }
                if(isset($arrayParametros['criterios']['canton']) )
                {
                    if($arrayParametros['criterios']['canton'])
                    {
                        if($arrayParametros['criterios']['canton']!="Todos")
                        {
                            $strWhere .= 'AND og.cantonId = :idCanton ';        
                            $objQuery->setParameter('idCanton', $arrayParametros['criterios']['canton']);
                            $objQueryCont->setParameter('idCanton', $arrayParametros['criterios']['canton']);

                        }
                    } 
                }
            }

            $strTipoRol      = "Empleado";
            
            $strCampos       = "SELECT per.id as idPersonaEmpresaRol,per.estado,p.id as idPersona,p.tipoIdentificacion,"
                               . "p.identificacionCliente as identificacion,p.nombres,p.apellidos,p.nacionalidad,p.direccion,"
                               . "eg.id as idEmpresa,d.id as idDepartamento,d.nombreDepartamento,c.id as idCanton,c.nombreCanton ";
            
            $strFrom         = "  
                                FROM 
                                    schemaBundle:InfoPersona p, 
                                    schemaBundle:InfoPersonaEmpresaRol per, 
                                    schemaBundle:InfoEmpresaRol er,
                                    schemaBundle:InfoEmpresaGrupo eg,
                                    schemaBundle:InfoOficinaGrupo og,
                                    schemaBundle:AdmiRol r, 
                                    schemaBundle:AdmiTipoRol tr,
                                    schemaBundle:AdmiDepartamento d,
                                    schemaBundle:AdmiCanton c ";
            
            $strWherePrincipal = "WHERE 
                                    p.id = per.personaId AND 
                                    er.id = per.empresaRolId AND
                                    eg.id = er.empresaCod AND
                                    og.id = per.oficinaId AND
                                    r.id = er.rolId AND
                                    tr.id = r.tipoRolId AND
                                    c.id = og.cantonId AND
                                    tr.descripcionTipoRol = :descripcionTipoRol AND
                                    d.id = per.departamentoId AND
                                    er.empresaCod = :empresaCod 
                                    AND per.estado= :estado ";

            $strOrderBy=" ORDER BY p.nombres, p.apellidos ";

            $objQuery->setParameter('estado', 'Activo');
            $objQueryCont->setParameter('estado', 'Activo');
            
            $objQuery->setParameter('descripcionTipoRol', $strTipoRol);
            $objQueryCont->setParameter('descripcionTipoRol', $strTipoRol);
            
            $objQuery->setParameter('empresaCod', $arrayParametros["codEmpresa"]);
            $objQueryCont->setParameter('empresaCod', $arrayParametros["codEmpresa"]);
            
            $strSelect = $strCampos . $strFrom . $strWherePrincipal . $strWhere . $strOrderBy;
            $objQuery->setDQL($strSelect); 
            $arrayResultado = $objQuery->setFirstResult($arrayParametros["intStart"])->setMaxResults($arrayParametros["intLimit"])->getResult();

            $strCount         = " SELECT COUNT(per) ";
            $strSelectCount   = $strCount . $strFrom . $strWherePrincipal . $strWhere . $strOrderBy; 

            $objQueryCont->setDQL($strSelectCount); 


            $intTotal                     = $objQueryCont->getSingleScalarResult();


            $arrayRespuesta['resultado'] = $arrayResultado;
            $arrayRespuesta['total']     = $intTotal;  
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
        }
        return $arrayRespuesta;

    }

    
    /**
     * getChoferesDisponiblesAsignacionOperativa
     *
     * Método que retorna los choferes con su cuadrilla si es que estuviera asignado a alguna dependiendo de los criterios enviados por el usuario                                    
     *      
     * @param array $arrayParametros
     * 
     * @return array $arrayResultados
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 05-02-2016
     */
    public function getChoferesDisponiblesAsignacionOperativa($arrayParametros)
    {
       
        $arrayResultados  = array();
        
        $query      = $this->_em->createQuery();
        $queryCount = $this->_em->createQuery();
        
       
        $strDescripcionRol              = 'Chofer';
        $strEstadoActivo                = 'Activo';
        $strEstadoEliminado             = 'Eliminado';
        $strDescripcionCaracteristica   = 'CARGO';
        
        $strSelect         = "SELECT DISTINCT (per.id) as intIdPersonaEmpresaRolChofer,p.id as intIdPersonaChofer,
                                               p.identificacionCliente as identificacionChofer,p.nombres as nombresChofer,
                                               p.apellidos as apellidosChofer ";

        $strSelectCount    = "SELECT COUNT(DISTINCT per.id) ";
        $strQuerySinSelect = "FROM schemaBundle:InfoPersonaEmpresaRol per 
                              INNER JOIN schemaBundle:InfoPersona p WITH per.personaId = p.id 
                              INNER JOIN schemaBundle:InfoEmpresaRol er WITH per.empresaRolId = er.id 
                              LEFT JOIN schemaBundle:AdmiRol r WITH er.rolId =r.id 
                              LEFT JOIN schemaBundle:AdmiTipoRol tr WITH r.tipoRolId = tr.id 
                              LEFT JOIN schemaBundle:InfoPersonaEmpresaRolCarac perc WITH per.id = perc.personaEmpresaRolId
                              LEFT JOIN schemaBundle:Admicaracteristica c WITH c.id = perc.caracteristicaId 
                              LEFT JOIN schemaBundle:InfoDetalleElemento ide WITH per.id = ide.detalleValor 
                                        and ide.detalleNombre=:detalleChofer and ide.estado = :estadoActivo
                              WHERE ((r.descripcionRol= :descripcionRol AND r.estado NOT LIKE :estadoEliminado 
                                        AND tr.estado NOT LIKE :estadoEliminado)
                                     OR (perc.valor= :descripcionRol AND c.descripcionCaracteristica = :descripcionCaracteristica 
                                         AND c.estado = :estadoActivo AND perc.estado = :estadoActivo
                                         )
                                     ) 
                                     AND er.empresaCod=:codEmpresa 
                                     AND ide.id IS NULL ";
        $strWhereBusqueda  = "";    
        $strOrderBy        = " ORDER BY p.nombres,p.apellidos ";
       
        $query->setParameter('codEmpresa' , $arrayParametros['codEmpresa']);
        $queryCount->setParameter('codEmpresa' , $arrayParametros['codEmpresa']);
        
        $query->setParameter('descripcionRol' , $strDescripcionRol);
        $queryCount->setParameter('descripcionRol' , $strDescripcionRol);
        
        $query->setParameter('estadoActivo' , $strEstadoActivo);
        $queryCount->setParameter('estadoActivo' , $strEstadoActivo);
        
        $query->setParameter('estadoEliminado' , $strEstadoEliminado);
        $queryCount->setParameter('estadoEliminado' , $strEstadoEliminado);

        $query->setParameter('descripcionCaracteristica' , $strDescripcionCaracteristica);
        $queryCount->setParameter('descripcionCaracteristica' , $strDescripcionCaracteristica);
        
        $query->setParameter('detalleChofer' , strtoupper($strDescripcionRol));
        $queryCount->setParameter('detalleChofer' , strtoupper($strDescripcionRol));

        
        if( isset($arrayParametros['idPerChoferAsignadoXVehiculo']) )
        {
            if($arrayParametros['idPerChoferAsignadoXVehiculo'])
            {
                $strWhereBusqueda .= 'AND per.id <> :idPerChoferAsignadoXVehiculo ';

                $query->setParameter('idPerChoferAsignadoXVehiculo', $arrayParametros['idPerChoferAsignadoXVehiculo']);

                $queryCount->setParameter('idPerChoferAsignadoXVehiculo', $arrayParametros['idPerChoferAsignadoXVehiculo']);
            }  
        }
        
        if( isset($arrayParametros['criterios']) )
        {
            if( isset($arrayParametros['criterios']['nombresChofer']) )
            {
                if($arrayParametros['criterios']['nombresChofer'])
                {
                    
                    $strWhereBusqueda .= 'AND p.nombres LIKE :nombresChofer ';
                    
                    $query->setParameter('nombresChofer', '%'.  strtoupper(trim($arrayParametros['criterios']['nombresChofer'])).'%');

                    $queryCount->setParameter('nombresChofer', '%'.strtoupper(trim($arrayParametros['criterios']['nombresChofer'])).'%');
                }  
            }
            
            if( isset($arrayParametros['criterios']['apellidosChofer']) )
            {
                if($arrayParametros['criterios']['apellidosChofer'])
                {
                    
                    $strWhereBusqueda .= 'AND p.apellidos LIKE :apellidosChofer ';
                    
                    $query->setParameter('apellidosChofer', '%'.strtoupper(trim($arrayParametros['criterios']['apellidosChofer'])).'%');

                    $queryCount->setParameter('apellidosChofer', '%'.strtoupper(trim($arrayParametros['criterios']['apellidosChofer'])).'%');
                }  
            }
            
            if( isset($arrayParametros['criterios']['identificacionChofer']) )
            {
                if($arrayParametros['criterios']['identificacionChofer'])
                {
                    $strWhereBusqueda .= 'AND p.identificacionCliente LIKE :identificacionChofer ';
                    
                    $query->setParameter('identificacionChofer', '%'.trim($arrayParametros['criterios']['identificacionChofer']).'%');

                    $queryCount->setParameter('identificacionChofer', '%'.trim($arrayParametros['criterios']['identificacionChofer']).'%');
                }  
            }
            
        }
        
        
        $strSql   = $strSelect.$strQuerySinSelect.$strWhereBusqueda.$strOrderBy;
        $query->setDQL($strSql);
        
        if( isset($arrayParametros['start']) )
        {
            if($arrayParametros['start'])
            {
                $query->setFirstResult($arrayParametros['start']);
            }  
        }
        
        if( isset($arrayParametros['limit']) )
        {
            if($arrayParametros['limit'])
            {
                $query->setMaxResults($arrayParametros['limit']);
            }
        }
        
        $arrayTmpDatos = $query->getResult();
        
        $strSqlCount = $strSelectCount.$strQuerySinSelect.$strWhereBusqueda;
        $queryCount->setDQL($strSqlCount); 
        $intTotal = $queryCount->getSingleScalarResult();
        
        $arrayResultados['registros'] = $arrayTmpDatos;
        $arrayResultados['total']     = $intTotal;
        
        
        return $arrayResultados;
        
        
    }
    /**
     * Documentación para el método 'getJsonIngenierosVIP'.
     *
     * Retorna la cadena Json de los Ingenieros VIP
     *
     * @param Array $arrayParametros['EMPRESA']         String: Código de la empresa.
     *                              ['CARACTERISTICA']  String: Descripción de la Característica del ingeniero VIP como tal.
     *                              ['ES_ING_VIP']      String: Valor [SI/NO] que define como ingeniero VIP o no.
     *                              ['CARACTERISTICA2'] String: Descripción de la Característica del ingeniero VIP asignado al Cliente.
     *                              ['ESTADOS']         String: Estados de filtrado.
     *                              ['ESTADO']          String: Estado de la característica.
     *                              ['ID_PER']          Int   : IdPersonaEmpresaRol del Cliente.
     *                              ['ROL']             String: Rol del Ingeniero VIP.
     *                              ['TIPO_ROL']        String: Tipo de Rol del Ingeniero VIP.
     *                              ['INGENIERO']       String: Nombre del Ingeniero VIP.
     *                              ['START']          Int   : Inicio de la paginación
     *                              ['LIMIT']          Int   : Rango máximo de la paginación
     * 
     * @return Array Listado de Ingenieros VIP
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 04-04-2016
     */
    public function getJsonIngenierosVIP($arrayParametros)
    {
        $objResultado = $this->getResultadoIngenierosVIP($arrayParametros);
       
        if(empty($objResultado['ERROR']))
        {
            $strJsonResultado = '{"total":"' . $objResultado['TOTAL'] . '","registros":' . json_encode($objResultado['REGISTROS']) . '}';
        }
        else
        {
            $strJsonResultado = '{"total":"0", "registros":[], "error":[' . $objResultado['ERROR'] . ']}';
        }

        return $strJsonResultado;
    }
    
    /**
     * Documentación para el método 'getResultadoIngenierosVIP'.
     *
     * Retorna el listado paginado de los ingenieros VIP
     *
     * @param Array $arrayParametros['EMPRESA']         String: Código de la empresa.
     *                              ['CARACTERISTICA']  String: Descripción de la Característica del ingeniero VIP como tal.
     *                              ['ES_ING_VIP']      String: Valor [SI/NO] que define como ingeniero VIP o no.
     *                              ['CARACTERISTICA2'] String: Descripción de la Característica del ingeniero VIP asignado al Cliente.
     *                              ['ESTADOS']         String: Estados de filtrado.
     *                              ['ESTADO']          String: Estado de la característica.
     *                              ['ID_PER']          Int   : IdPersonaEmpresaRol del Cliente.
     *                              ['ROL']             String: Rol del Ingeniero VIP.
     *                              ['TIPO_ROL']        String: Tipo de Rol del Ingeniero VIP.
     *                              ['INGENIERO']       String: Nombre del Ingeniero VIP.
     *                              ['START']          Int   : Inicio de la paginación
     *                              ['LIMIT']          Int   : Rango máximo de la paginación
     * 
     * @return Array Listado de Ingenieros VIP
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 04-04-2016
     */
    public function getResultadoIngenierosVIP($arrayParametros)
    {
        $arrayResult        = array();
        $arrayIngenierosVIP = array();
        
        $intLimit = 0;
        $intStart = 0;
        
        try
        {
            $objIngenierosVipNativeQuery = $this->getIngenierosVIPNativeQuery($arrayParametros);
            
            if(empty($objIngenierosVipNativeQuery['ERROR']))
            {
                $objNativeQuery = $objIngenierosVipNativeQuery['OBJ_QUERY'];
                $strQuery       = $objNativeQuery->getSQL();
                
                $objNativeQuery->setSQL("SELECT COUNT(*) AS TOTAL FROM ($strQuery)");
                
                $intTotalRegistros = $objNativeQuery->getSingleScalarResult();
                
                if($intTotalRegistros > 0)
                {
                    $objNativeQuery->setSQL($strQuery);

                    // Se define el Inicio y el Límite de la paginación.
                    if(isset($arrayParametros['LIMIT']) &&  isset($arrayParametros['START']))
                    {
                        $intStart = $arrayParametros['LIMIT'];
                        $intLimit = $arrayParametros['START'];
                    }
                    
                    $arrayIngenierosVIP = $this->setQueryLimit($objNativeQuery, $intLimit, $intStart)->getResult();
                }
                $arrayResult['REGISTROS'] = $arrayIngenierosVIP;
                $arrayResult['TOTAL']     = $intTotalRegistros;
                $arrayResult['ERROR']     = '';
            }
            else
            {
                $arrayResult['ERROR'] = $objIngenierosVipNativeQuery['ERROR'];
            }
        }
        catch(\Exception $ex)
        {
            $arrayResult['ERROR'] = 'Error: ' . $ex->getMessage();
        }
        return $arrayResult;
    }
    
    /**
     * Documentación para el método 'getIngenierosVIPNativeQuery'.
     *
     * Retorna el objeto NativeQuery para la obtención de los ingenieros VIP
     *
     * @param Array $arrayParametros['EMPRESA']         String: Código de la empresa.
     *                              ['CARACTERISTICA']  String: Descripción de la Característica del ingeniero VIP como tal.
     *                              ['ES_ING_VIP']      String: Valor [SI/NO] que define como ingeniero VIP o no.
     *                              ['CARACTERISTICA2'] String: Descripción de la Característica del ingeniero VIP asignado al Cliente.
     *                              ['ESTADOS']         String: Estados de filtrado.
     *                              ['ESTADO']          String: Estado de la característica.
     *                              ['ID_PER']          Int   : IdPersonaEmpresaRol del Cliente.
     *                              ['ROL']             String: Rol del Ingeniero VIP.
     *                              ['TIPO_ROL']        String: Tipo de Rol del Ingeniero VIP.
     *                              ['INGENIERO']       String: Nombre del Ingeniero VIP.
     * costoQuery: 11  
     * 
     * @return Array Listado de Ingenieros VIP
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 04-04-2016
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 22-05-2016
     * Se convierte a Mayúsculas el nombre del Ingeniero VIP
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.2 24-01-2020 - Se modifica la consulta, ahora los Ingenieros VIP siempre aparecen
     *                           disponibles aunque ya estén asignados al cliente.
     */
    private function getIngenierosVIPNativeQuery($arrayParametros)
    {
        try 
        {
            $objMappingBuilder = new ResultSetMappingBuilder($this->_em);
            $objNativeQuery    = $this->_em->createNativeQuery(null, $objMappingBuilder);
            $strIngenieroVIP   = '';

            if(isset($arrayParametros['INGENIERO']) && $arrayParametros['INGENIERO'] != '')
            {
                $strIngenieroVIP = "AND CONCAT(UPPER(P.NOMBRES),CONCAT(' ',UPPER(P.APELLIDOS))) LIKE UPPER(:INGENIERO)";
                $objNativeQuery->setParameter('INGENIERO', '%' . $arrayParametros['INGENIERO'] . '%');
            }
            
            $strSQL = " SELECT PER.ID_PERSONA_ROL ID_PER, UPPER(P.NOMBRES || ' ' || P.APELLIDOS) INGENIERO
                        FROM       DB_COMERCIAL.INFO_PERSONA                   P
                        INNER JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL       PER  ON PER.PERSONA_ID              = P.ID_PERSONA
                        INNER JOIN DB_COMERCIAL.INFO_EMPRESA_ROL               ER   ON ER.ID_EMPRESA_ROL           = PER.EMPRESA_ROL_ID
                        INNER JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC PERC ON PERC.PERSONA_EMPRESA_ROL_ID = PER.ID_PERSONA_ROL
                        INNER JOIN DB_COMERCIAL.ADMI_CARACTERISTICA            C    ON C.ID_CARACTERISTICA         = PERC.CARACTERISTICA_ID
                        WHERE PERC.ESTADO                  = :ESTADO
                        AND   PER.ESTADO                   = :ESTADO
                        AND   ER.EMPRESA_COD               = :EMPRESA
                        AND   C.DESCRIPCION_CARACTERISTICA = :CARACTERISTICA
                        AND   PERC.VALOR = :ES_ING_VIP
                        $strIngenieroVIP                      
                        ORDER BY P.NOMBRES,  P.APELLIDOS";

            $objNativeQuery->setParameter('EMPRESA',         $arrayParametros['EMPRESA']);
            $objNativeQuery->setParameter('CARACTERISTICA',  $arrayParametros['CARACTERISTICA']);
            $objNativeQuery->setParameter('ES_ING_VIP',      $arrayParametros['ES_ING_VIP']);
            $objNativeQuery->setParameter('ID_CLIENTE',      $arrayParametros['ID_PER']);
            $objNativeQuery->setParameter('CARACTERISTICA2', $arrayParametros['CARACTERISTICA2']);
            $objNativeQuery->setParameter('ESTADO',          $arrayParametros['ESTADO']);

            $objMappingBuilder->addScalarResult('ID_PER',    'id_per',       'string');
            $objMappingBuilder->addScalarResult('INGENIERO', 'ingenieroVip', 'string');
            $objMappingBuilder->addScalarResult('TOTAL',     'total',        'integer');

            $objNativeQuery->setSQL($strSQL);

            $arrayResult['OBJ_QUERY'] = $objNativeQuery->setSQL($strSQL);
            $arrayResult['ERROR']     = '';
        }
        catch(\Exception $ex)
        {
            $arrayResult['ERROR'] = 'ERROR: ' . $ex->getMessage();
        }
        return $arrayResult;
    }

    /**
     * Documentación para el método 'getJsonIngenierosVIPCliente'.
     *
     * Retorna la cadena Json de los Ingenieros VIP asociados al cliente VIP
     *
     * @param Array $arrayParametros['EMPRESA']        String: Código de la empresa
     *                              ['CARACTERISTICA'] String: Descripción de la Característica
     *                              ['ESTADO']         String: Estado de la característica
     *                              ['ID_PER']         Int   : IdPersonaEmpresaRol del Cliente
     * 
     * @return Array Listado de Ingenieros VIP
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 04-04-2016
     */
    public function getJsonIngenierosVIPCliente($arrayParametros)
    {
        $objResultado = $this->getResultadoIngenierosVIPCliente($arrayParametros);
       
        if(empty($objResultado['ERROR']))
        {
            $strJsonResultado = '{"total":"' . $objResultado['TOTAL'] . '","registros":' . json_encode($objResultado['REGISTROS']) . '}';
        }
        else
        {
            $strJsonResultado = '{"total":"0", "registros":[], "error":[' . $objResultado['ERROR'] . ']}';
        }

        return $strJsonResultado;
    }
    
    /**
     * Documentación para el método 'getResultadoIngenierosVIPCliente'.
     *
     * Retorna el listado paginado de los ingenieros VIP relacionados al cliente
     *
     * @param Array $arrayParametros['EMPRESA']        String: Código de la empresa
     *                              ['CARACTERISTICA'] String: Descripción de la Característica
     *                              ['ESTADO']         String: Estado de la característica
     *                              ['ID_PER']         Int   : IdPersonaEmpresaRol del Cliente
     * 
     * @return Array Listado de Ingenieros VIP del cliente
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 04-04-2016
     */
    public function getResultadoIngenierosVIPCliente($arrayParametros)
    {
        $arrayResult               = array();
        $arrayIngenierosVIPCliente = array();
        
        $intStart = 0;
        $intLimit = 0;
        
        try
        {
            $objIngenierosVipClienteNativeQuery = $this->getIngenierosVIPClienteNativeQuery($arrayParametros);
            
            if(empty($objIngenierosVipClienteNativeQuery['ERROR']))
            {
                $objNativeQuery = $objIngenierosVipClienteNativeQuery['OBJ_QUERY'];
                $strQuery       = $objNativeQuery->getSQL();
                
                $objNativeQuery->setSQL("SELECT COUNT(*) AS TOTAL FROM ($strQuery)");
                
                $intTotalRegistros   = $objNativeQuery->getSingleScalarResult();
                
                if($intTotalRegistros > 0)
                {
                    $objNativeQuery->setSQL($strQuery);

                    // Se define el Inicio y el Límite de la paginación.
                    if(isset($arrayParametros['LIMIT']) &&  isset($arrayParametros['START']))
                    {
                        $intStart = $arrayParametros['LIMIT'];
                        $intLimit = $arrayParametros['START'];
                    }
                    
                    $arrayIngenierosVIPCliente = $this->setQueryLimit($objNativeQuery, $intLimit, $intStart)->getResult();
                }
                $arrayResult['REGISTROS'] = $arrayIngenierosVIPCliente;
                $arrayResult['TOTAL']     = $intTotalRegistros;
                $arrayResult['ERROR']     = '';
            }
            else
            {
                $arrayResult['ERROR'] = $objIngenierosVipClienteNativeQuery['ERROR'];
            }
        }
        catch(\Exception $ex)
        {
            $arrayResult['ERROR'] = 'Error: ' . $ex->getMessage();
        }
        return $arrayResult;
    }
    
    /**
     * Documentación para el método 'getIngenierosVIPClienteNativeQuery'.
     *
     * Retorna el objeto NativeQuery para la obtención de los ingenieros VIP relacionados al cliente VIP
     *
     * @param Array $arrayParametros['EMPRESA']        String: Código de la empresa
     *                              ['CARACTERISTICA'] String: Descripción de la Característica
     *                              ['ESTADO']         String: Estado de la característica
     *                              ['ID_PER']         Int   : IdPersonaEmpresaRol del Cliente
     * costoQuery: 19  
     * 
     * @return Array Listado de Ingenieros VIP del cliente
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 04-04-2016
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 15-05-2016
     * Se transforma a  mayúsculas el nombre del Ingeniero con UPPER.
     * 
     * @author Héctor Lozano <hlozano@telconet.ec>
     * @version 1.2 12-09-2018
     * Se transforma a TO_NUMBER el campo valor de la tabla INFO_PERSONA_EMPRESA_ROL_CARAC, para relaizar el JOIN con INFO_PERSONA_EMPRESA_ROL.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.3 24-01-2020 - Se agregó al query la búsqueda de la ciudad y de la extensión del Ingeniero VIP
     *                           si se recibe las características de cualquiera de ellos.
     */
    private function getIngenierosVIPClienteNativeQuery($arrayParametros)
    {
        try 
        {
            $objMappingBuilder = new ResultSetMappingBuilder($this->_em);
            $objNativeQuery    = $this->_em->createNativeQuery(null, $objMappingBuilder);
            $strIngenieroVIP   = '';

            if(isset($arrayParametros['INGENIERO']) && $arrayParametros['INGENIERO'] != '')
            {
                $strIngenieroVIP = "AND CONCAT(UPPER(P.NOMBRES),CONCAT(' ',UPPER(P.APELLIDOS))) LIKE UPPER(:INGENIERO)";
                $objNativeQuery->setParameter('INGENIERO', '%' . $arrayParametros['INGENIERO'] . '%');
            }
            
            $booleanCaractCiudad        = false;
            $booleanCaractExtension     = false;
            if( isset($arrayParametros['strCaractCiudad']) && !empty($arrayParametros['strCaractCiudad']) )
            {
                $booleanCaractCiudad    = true;
                $objMappingBuilder->addScalarResult('CIUDAD',   'ciudad',   'string');
                $objMappingBuilder->addScalarResult('ID_CIUDAD','id_ciudad','string');
                $objNativeQuery->setParameter('ID_CARACT_CIUDAD',$arrayParametros['strCaractCiudad']);
            }
            if( isset($arrayParametros['strCaractExt']) && !empty($arrayParametros['strCaractExt']) )
            {
                $booleanCaractExtension = true;
                $objMappingBuilder->addScalarResult('EXTENSION','extension','string');
                $objNativeQuery->setParameter('ID_CARACT_EXT', $arrayParametros['strCaractExt']);
            }

            $strSQL     = "SELECT PERC.VALOR ID_PER,
                                  UPPER(P.NOMBRES || ' ' || P.APELLIDOS) INGENIERO,
                                  PERC.ID_PERSONA_EMPRESA_ROL_CARACT ID_PER_CARACT";
            if($booleanCaractCiudad)
            {
                $strSQL = $strSQL.", PER_CIU.CIUDAD, PER_CIU.VALOR ID_CIUDAD";
            }
            if($booleanCaractExtension)
            {
                $strSQL = $strSQL.", PER_EXT.VALOR EXTENSION";
            }
            $strSQL     = $strSQL." FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC PERC
                       INNER JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL  PER
                                    ON PER.ID_PERSONA_ROL  = COALESCE(TO_NUMBER(REGEXP_SUBSTR(PERC.VALOR,'^\d+')),0)
                       INNER JOIN DB_COMERCIAL.INFO_EMPRESA_ROL          ER   ON ER.ID_EMPRESA_ROL   = PER.EMPRESA_ROL_ID
                       INNER JOIN DB_COMERCIAL.INFO_PERSONA              P    ON P.ID_PERSONA        = PER.PERSONA_ID
                       INNER JOIN DB_COMERCIAL.ADMI_CARACTERISTICA       C    ON C.ID_CARACTERISTICA = PERC.CARACTERISTICA_ID";
            if($booleanCaractCiudad)
            {
                $strSQL = $strSQL." LEFT JOIN ( SELECT PER_CIUDAD.PERSONA_EMPRESA_ROL_CARAC_ID, CANTON.NOMBRE_CANTON CIUDAD, PER_CIUDAD.VALOR
                                    FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC PER_CIUDAD
                                    INNER JOIN DB_GENERAL.ADMI_CANTON CANTON ON CANTON.ID_CANTON = PER_CIUDAD.VALOR
                                    WHERE PER_CIUDAD.CARACTERISTICA_ID = :ID_CARACT_CIUDAD AND PER_CIUDAD.ESTADO = :ESTADO ) PER_CIU
                                    ON PER_CIU.PERSONA_EMPRESA_ROL_CARAC_ID = PERC.ID_PERSONA_EMPRESA_ROL_CARACT";
            }
            if($booleanCaractExtension)
            {
                $strSQL = $strSQL." LEFT JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC PER_EXT
                                    ON PER_EXT.PERSONA_EMPRESA_ROL_CARAC_ID = PERC.ID_PERSONA_EMPRESA_ROL_CARACT
                                    AND PER_EXT.CARACTERISTICA_ID = :ID_CARACT_EXT
                                    AND PER_EXT.ESTADO = :ESTADO";
            }

            $strSQL     = $strSQL." WHERE PERC.PERSONA_EMPRESA_ROL_ID  = :ID_PER
                                    AND   C.DESCRIPCION_CARACTERISTICA = :CARACTERISTICA
                                    AND   PERC.ESTADO                  = :ESTADO
                                    AND   ER.EMPRESA_COD               = :EMPRESA
                                    ORDER BY P.NOMBRES,  P.APELLIDOS";

            $objNativeQuery->setParameter('ID_PER',         $arrayParametros['ID_PER']);
            $objNativeQuery->setParameter('CARACTERISTICA', $arrayParametros['CARACTERISTICA']);
            $objNativeQuery->setParameter('ESTADO',         $arrayParametros['ESTADO']);
            $objNativeQuery->setParameter('EMPRESA',        $arrayParametros['EMPRESA']);

            $objMappingBuilder->addScalarResult('ID_PER',       'id_per',       'string');
            $objMappingBuilder->addScalarResult('ID_PER_CARACT','id_per_caract','string');
            $objMappingBuilder->addScalarResult('INGENIERO',    'ingenieroVip', 'string');
            $objMappingBuilder->addScalarResult('TOTAL',        'total',        'integer');

            $objNativeQuery->setSQL($strSQL);

            $arrayResult['OBJ_QUERY'] = $objNativeQuery->setSQL($strSQL);
            $arrayResult['ERROR']     = '';
        }
        catch(\Exception $ex)
        {
            $arrayResult['ERROR'] = 'ERROR: ' . $ex->getMessage();
        }
        
        return $arrayResult;
    }
    
    /**
     * Documentación para el método 'getJsonHistorialClienteVIP'.
     *
     * Retorna la cadena Json del historial del cliente VIP: Cambios de Situación y asignaciones/eliminaciones de ingenieros VIP.
     *
     * @param Array $arrayParametros['EMPRESA']        String: Código de la empresa
     *                              ['CARACTERISTICA'] String: Descripción de la Característica
     *                              ['ESTADO']         String: Estado de la característica
     *                              ['ID_PER']         Int   : IdPersonaEmpresaRol del Cliente
     * 
     * @return String cadena Json del historial del cliente VIP
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 04-04-2016
     */
    public function getJsonHistorialClienteVIP($arrayParametros)
    {
        $objResultado = $this->getResultadoHistorialClienteVIP($arrayParametros);
       
        if(empty($objResultado['ERROR']))
        {
            $strJsonResultado = '{"total":"' . $objResultado['TOTAL'] . '","registros":' . json_encode($objResultado['REGISTROS']) . '}';
        }
        else
        {
            $strJsonResultado = '{"total":"0", "registros":[], "error":[' . $objResultado['ERROR'] . ']}';
        }

        return $strJsonResultado;
    }
    
    /**
     * Documentación para el método 'getResultadoHistorialClienteVIP'.
     *
     * Retorna el listado paginado del historial del cliente VIP: Cambios de Situación y asignaciones/eliminaciones de ingenieros VIP.
     *
     * @param Array $arrayParametros['EMPRESA']        String: Código de la empresa
     *                              ['CARACTERISTICA'] String: Descripción de la Característica
     *                              ['ESTADO']         String: Estado de la característica
     *                              ['ID_PER']         Int   : IdPersonaEmpresaRol del Cliente
     * 
     * @return Array Historial del Cliente  VIP
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 04-04-2016
     */                                                                                                                             
    public function getResultadoHistorialClienteVIP($arrayParametros)
    {
        $arrayResult              = array();
        $arrayHistorialClienteVIP = array();
        
        $intStart = 0;
        $intLimit = 0;
    
        try
        {
            // Se obtiene el Objeto Navite Query para reformular el SQL y obtener el total de registros.
            $objHistorialClienteVIPNativeQuery = $this->getHistorialClienteVIPNativeQuery($arrayParametros);
            if(empty($objHistorialClienteVIPNativeQuery['ERROR']))
            {
                $objNativeQuery = $objHistorialClienteVIPNativeQuery['OBJ_QUERY'];
                $strQuery       = $objNativeQuery->getSQL();
                $objNativeQuery->setSQL("SELECT COUNT(*) AS TOTAL FROM ($strQuery)");
                
                $intTotalRegistros   = $objNativeQuery->getSingleScalarResult(); // Se obtiene el total de registros

                if($intTotalRegistros > 0)
                {
                    $objNativeQuery->setSQL($strQuery);

                    // Se define el Inicio y el Límite de la paginación.
                    if(isset($arrayParametros['LIMIT']) &&  isset($arrayParametros['START']))
                    {
                        $intStart = $arrayParametros['LIMIT'];
                        $intLimit = $arrayParametros['START'];
                    }
                    
                    $arrayHistorialClienteVIP = $this->setQueryLimit($objNativeQuery, $intLimit, $intStart)->getResult();
                }
                $arrayResult['REGISTROS'] = $arrayHistorialClienteVIP;
                $arrayResult['TOTAL']     = $intTotalRegistros;
                $arrayResult['ERROR']     = '';
            }
            else
            {
                $arrayResult['ERROR'] = $objHistorialClienteVIPNativeQuery['ERROR'];
            }
        }
        catch(\Exception $ex)
        {
            $arrayResult['ERROR'] = 'Error: ' . $ex->getMessage();
        }
        return $arrayResult;
    }
    
    /**
     * Documentación para el método 'getHistorialClienteVIPNativeQuery'.
     *
     * Retorna el objeto NativeQuery para la obtención del Historial de cambio de situación del Cliente y
     * la asignación/eliminación de los ingenieros VIP.
     *
     * @param Array $arrayParametros['EMPRESA']        String: Código de la empresa
     *                              ['CARACTERISTICA'] String: Descripción de la Característica
     *                              ['ESTADO']         String: Estado de la característica
     *                              ['ID_PER']         Int   : IdPersonaEmpresaRol del Cliente
     * costoQuery: 109
     * 
     * @return Array Listado Historial
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 04-04-2016
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.1 24-01-2020 - Se agregó al query la búsqueda del historial de la ciudad y la extensión de los ingenieros VIP.
     */
    private function getHistorialClienteVIPNativeQuery($arrayParametros)
    {
        try 
        {
            $objMappingBuilder = new ResultSetMappingBuilder($this->_em);
            $objNativeQuery    = $this->_em->createNativeQuery(null, $objMappingBuilder);

            $strSQL = " SELECT T.ACCION, T.VALOR,T.USUARIO, T.FECHA FROM(
                        SELECT 'ASIGNACION' ACCION, 'Se Asignó el Ingeniero VIP: '|| UPPER(P.NOMBRES || ' ' || P.APELLIDOS) VALOR,
                               PERC.USR_CREACION USUARIO, PERC.FE_CREACION FECHA
                        FROM       DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC PERC
                        INNER JOIN DB_COMERCIAL.ADMI_CARACTERISTICA            C    ON C.ID_CARACTERISTICA = PERC.CARACTERISTICA_ID
                        INNER JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL       PER  ON PER.ID_PERSONA_ROL  = PERC.PERSONA_EMPRESA_ROL_ID
                        INNER JOIN DB_COMERCIAL.INFO_EMPRESA_ROL               ER   ON ER.ID_EMPRESA_ROL   = PER.EMPRESA_ROL_ID
                        INNER JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL       PER2 ON PER2.ID_PERSONA_ROL = PERC.VALOR
                        INNER JOIN DB_COMERCIAL.INFO_EMPRESA_ROL               ER2  ON ER2.ID_EMPRESA_ROL  = PER.EMPRESA_ROL_ID
                        INNER JOIN DB_COMERCIAL.INFO_PERSONA                   P    ON P.ID_PERSONA        = PER2.PERSONA_ID

                        WHERE PERC.PERSONA_EMPRESA_ROL_ID  = :ID_CLIENTE
                        AND   C.DESCRIPCION_CARACTERISTICA = :CARACTERISTICA
                        AND   ER.EMPRESA_COD               = :EMPRESA
                        AND   ER2.EMPRESA_COD              = :EMPRESA

                        UNION

                        SELECT 'ELIMINACION' ACCION, 'Se Eliminó el Ingeniero VIP: '|| UPPER(P.NOMBRES || ' ' || P.APELLIDOS) VALOR,
                              PERC.USR_ULT_MOD USUARIO, PERC.FE_ULT_MOD FECHA
                        FROM       DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC PERC
                        INNER JOIN DB_COMERCIAL.ADMI_CARACTERISTICA            C    ON C.ID_CARACTERISTICA = PERC.CARACTERISTICA_ID
                        INNER JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL       PER  ON PER.ID_PERSONA_ROL  = PERC.PERSONA_EMPRESA_ROL_ID
                        INNER JOIN DB_COMERCIAL.INFO_EMPRESA_ROL               ER   ON ER.ID_EMPRESA_ROL   = PER.EMPRESA_ROL_ID
                        INNER JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL       PER2 ON PER2.ID_PERSONA_ROL = PERC.VALOR
                        INNER JOIN DB_COMERCIAL.INFO_EMPRESA_ROL               ER2  ON ER2.ID_EMPRESA_ROL  = PER.EMPRESA_ROL_ID
                        INNER JOIN DB_COMERCIAL.INFO_PERSONA                   P    ON P.ID_PERSONA        = PER2.PERSONA_ID

                        WHERE PERC.PERSONA_EMPRESA_ROL_ID  = :ID_CLIENTE
                        AND   C.DESCRIPCION_CARACTERISTICA = :CARACTERISTICA
                        AND   ER.EMPRESA_COD               = :EMPRESA
                        AND   ER2.EMPRESA_COD              = :EMPRESA
                        AND   PERC.ESTADO = :ESTADO

                        UNION

                        SELECT  'ASIGNACION' ACCION, 
                                'Se Asigno la ciudad ' || CANTON.NOMBRE_CANTON || ' al Ingeniero VIP: '|| INGENIERO_VIP.NOMBRES VALOR,
                                PERC.USR_CREACION USUARIO, PERC.FE_CREACION FECHA
                        FROM       DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC PERC
                        INNER JOIN DB_COMERCIAL.ADMI_CARACTERISTICA        C ON C.ID_CARACTERISTICA = PERC.CARACTERISTICA_ID
                        INNER JOIN DB_GENERAL.ADMI_CANTON             CANTON ON CANTON.ID_CANTON    = PERC.VALOR
                        INNER JOIN (
                        
                            SELECT PERC.ID_PERSONA_EMPRESA_ROL_CARACT, UPPER(P.NOMBRES || ' ' || P.APELLIDOS) NOMBRES
                            FROM       DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC PERC
                            INNER JOIN DB_COMERCIAL.ADMI_CARACTERISTICA            C    ON C.ID_CARACTERISTICA = PERC.CARACTERISTICA_ID
                            INNER JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL       PER  ON PER.ID_PERSONA_ROL  = PERC.PERSONA_EMPRESA_ROL_ID
                            INNER JOIN DB_COMERCIAL.INFO_EMPRESA_ROL               ER   ON ER.ID_EMPRESA_ROL   = PER.EMPRESA_ROL_ID
                            INNER JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL       PER2 ON PER2.ID_PERSONA_ROL = PERC.VALOR
                            INNER JOIN DB_COMERCIAL.INFO_EMPRESA_ROL               ER2  ON ER2.ID_EMPRESA_ROL  = PER.EMPRESA_ROL_ID
                            INNER JOIN DB_COMERCIAL.INFO_PERSONA                   P    ON P.ID_PERSONA        = PER2.PERSONA_ID

                            WHERE PERC.PERSONA_EMPRESA_ROL_ID  = :ID_CLIENTE
                            AND   C.DESCRIPCION_CARACTERISTICA = :CARACTERISTICA
                            AND   ER.EMPRESA_COD               = :EMPRESA
                            AND   ER2.EMPRESA_COD              = :EMPRESA
                            
                        ) INGENIERO_VIP ON INGENIERO_VIP.ID_PERSONA_EMPRESA_ROL_CARACT = PERC.PERSONA_EMPRESA_ROL_CARAC_ID

                        WHERE C.DESCRIPCION_CARACTERISTICA = :CARACT_CIUDAD

                        UNION

                        SELECT  'ELIMINACION' ACCION, 
                                'Se Eliminó la ciudad ' || CANTON.NOMBRE_CANTON || ' al Ingeniero VIP: '|| INGENIERO_VIP.NOMBRES VALOR,
                                PERC.USR_ULT_MOD USUARIO, PERC.FE_ULT_MOD FECHA
                        FROM       DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC PERC
                        INNER JOIN DB_COMERCIAL.ADMI_CARACTERISTICA        C ON C.ID_CARACTERISTICA = PERC.CARACTERISTICA_ID
                        INNER JOIN DB_GENERAL.ADMI_CANTON             CANTON ON CANTON.ID_CANTON    = PERC.VALOR
                        INNER JOIN (
                        
                            SELECT PERC.ID_PERSONA_EMPRESA_ROL_CARACT, UPPER(P.NOMBRES || ' ' || P.APELLIDOS) NOMBRES
                            FROM       DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC PERC
                            INNER JOIN DB_COMERCIAL.ADMI_CARACTERISTICA            C    ON C.ID_CARACTERISTICA = PERC.CARACTERISTICA_ID
                            INNER JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL       PER  ON PER.ID_PERSONA_ROL  = PERC.PERSONA_EMPRESA_ROL_ID
                            INNER JOIN DB_COMERCIAL.INFO_EMPRESA_ROL               ER   ON ER.ID_EMPRESA_ROL   = PER.EMPRESA_ROL_ID
                            INNER JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL       PER2 ON PER2.ID_PERSONA_ROL = PERC.VALOR
                            INNER JOIN DB_COMERCIAL.INFO_EMPRESA_ROL               ER2  ON ER2.ID_EMPRESA_ROL  = PER.EMPRESA_ROL_ID
                            INNER JOIN DB_COMERCIAL.INFO_PERSONA                   P    ON P.ID_PERSONA        = PER2.PERSONA_ID

                            WHERE PERC.PERSONA_EMPRESA_ROL_ID  = :ID_CLIENTE
                            AND   C.DESCRIPCION_CARACTERISTICA = :CARACTERISTICA
                            AND   ER.EMPRESA_COD               = :EMPRESA
                            AND   ER2.EMPRESA_COD              = :EMPRESA
                            
                        ) INGENIERO_VIP ON INGENIERO_VIP.ID_PERSONA_EMPRESA_ROL_CARACT = PERC.PERSONA_EMPRESA_ROL_CARAC_ID

                        WHERE C.DESCRIPCION_CARACTERISTICA = :CARACT_CIUDAD
                        AND   PERC.ESTADO                  = :ESTADO

                        UNION

                        SELECT  'ASIGNACION' ACCION, 
                                'Se Asigno la extensión ' || PERC.VALOR || ' del Ingeniero VIP: '|| INGENIERO_VIP.NOMBRES VALOR,
                                PERC.USR_CREACION USUARIO, PERC.FE_CREACION FECHA
                        FROM       DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC PERC
                        INNER JOIN DB_COMERCIAL.ADMI_CARACTERISTICA               C ON C.ID_CARACTERISTICA = PERC.CARACTERISTICA_ID
                        INNER JOIN (
                        
                            SELECT PERC.ID_PERSONA_EMPRESA_ROL_CARACT, UPPER(P.NOMBRES || ' ' || P.APELLIDOS) NOMBRES
                            FROM       DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC PERC
                            INNER JOIN DB_COMERCIAL.ADMI_CARACTERISTICA            C    ON C.ID_CARACTERISTICA = PERC.CARACTERISTICA_ID
                            INNER JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL       PER  ON PER.ID_PERSONA_ROL  = PERC.PERSONA_EMPRESA_ROL_ID
                            INNER JOIN DB_COMERCIAL.INFO_EMPRESA_ROL               ER   ON ER.ID_EMPRESA_ROL   = PER.EMPRESA_ROL_ID
                            INNER JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL       PER2 ON PER2.ID_PERSONA_ROL = PERC.VALOR
                            INNER JOIN DB_COMERCIAL.INFO_EMPRESA_ROL               ER2  ON ER2.ID_EMPRESA_ROL  = PER.EMPRESA_ROL_ID
                            INNER JOIN DB_COMERCIAL.INFO_PERSONA                   P    ON P.ID_PERSONA        = PER2.PERSONA_ID

                            WHERE PERC.PERSONA_EMPRESA_ROL_ID  = :ID_CLIENTE
                            AND   C.DESCRIPCION_CARACTERISTICA = :CARACTERISTICA
                            AND   ER.EMPRESA_COD               = :EMPRESA
                            AND   ER2.EMPRESA_COD              = :EMPRESA
                            
                        ) INGENIERO_VIP ON INGENIERO_VIP.ID_PERSONA_EMPRESA_ROL_CARACT = PERC.PERSONA_EMPRESA_ROL_CARAC_ID

                        WHERE C.DESCRIPCION_CARACTERISTICA = :CARACT_EXT

                        UNION

                        SELECT  'ELIMINACION' ACCION, 
                                'Se Eliminó la extensión ' || PERC.VALOR || ' del Ingeniero VIP: '|| INGENIERO_VIP.NOMBRES VALOR,
                                PERC.USR_ULT_MOD USUARIO, PERC.FE_ULT_MOD FECHA
                        FROM       DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC PERC
                        INNER JOIN DB_COMERCIAL.ADMI_CARACTERISTICA               C ON C.ID_CARACTERISTICA = PERC.CARACTERISTICA_ID
                        INNER JOIN (
                        
                            SELECT PERC.ID_PERSONA_EMPRESA_ROL_CARACT, UPPER(P.NOMBRES || ' ' || P.APELLIDOS) NOMBRES
                            FROM       DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC PERC
                            INNER JOIN DB_COMERCIAL.ADMI_CARACTERISTICA            C    ON C.ID_CARACTERISTICA = PERC.CARACTERISTICA_ID
                            INNER JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL       PER  ON PER.ID_PERSONA_ROL  = PERC.PERSONA_EMPRESA_ROL_ID
                            INNER JOIN DB_COMERCIAL.INFO_EMPRESA_ROL               ER   ON ER.ID_EMPRESA_ROL   = PER.EMPRESA_ROL_ID
                            INNER JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL       PER2 ON PER2.ID_PERSONA_ROL = PERC.VALOR
                            INNER JOIN DB_COMERCIAL.INFO_EMPRESA_ROL               ER2  ON ER2.ID_EMPRESA_ROL  = PER.EMPRESA_ROL_ID
                            INNER JOIN DB_COMERCIAL.INFO_PERSONA                   P    ON P.ID_PERSONA        = PER2.PERSONA_ID

                            WHERE PERC.PERSONA_EMPRESA_ROL_ID  = :ID_CLIENTE
                            AND   C.DESCRIPCION_CARACTERISTICA = :CARACTERISTICA
                            AND   ER.EMPRESA_COD               = :EMPRESA
                            AND   ER2.EMPRESA_COD              = :EMPRESA
                            
                        ) INGENIERO_VIP ON INGENIERO_VIP.ID_PERSONA_EMPRESA_ROL_CARACT = PERC.PERSONA_EMPRESA_ROL_CARAC_ID

                        WHERE C.DESCRIPCION_CARACTERISTICA = :CARACT_EXT
                        AND   PERC.ESTADO                  = :ESTADO

                        ) T
                        ORDER BY T.FECHA DESC";


            $objNativeQuery->setParameter('ID_CLIENTE',     $arrayParametros['ID_PER']);
            $objNativeQuery->setParameter('EMPRESA',        $arrayParametros['EMPRESA']);
            $objNativeQuery->setParameter('CARACTERISTICA', $arrayParametros['CARACTERISTICA']);
            $objNativeQuery->setParameter('ESTADO',         $arrayParametros['ESTADO']);
            $objNativeQuery->setParameter('CARACT_CIUDAD',  $arrayParametros['strCaractCiudad']);
            $objNativeQuery->setParameter('CARACT_EXT',     $arrayParametros['strCaractExtension']);

            $objMappingBuilder->addScalarResult('ACCION',  'accion',  'string');
            $objMappingBuilder->addScalarResult('VALOR',   'valor',   'string');
            $objMappingBuilder->addScalarResult('USUARIO', 'usuario', 'string');
            $objMappingBuilder->addScalarResult('FECHA',   'fecha',   'string');
            $objMappingBuilder->addScalarResult('TOTAL',   'total',   'integer');

            $objNativeQuery->setSQL($strSQL);

            $arrayResult['OBJ_QUERY'] = $objNativeQuery->setSQL($strSQL);
            $arrayResult['ERROR']     = '';
        }
        catch(\Exception $ex)
        {
            $arrayResult['ERROR'] = 'ERROR: ' . $ex->getMessage();
        }
        
        return $arrayResult;
	}
    
    /**
     * Documentación para el método 'getJsonHistorialCliente'.
     *
     * Retorna la cadena Json del historial historial de cambios de los datos de facturación del cliente.
     *
     * @param Array $arrayParametros['EMPRESA'] String: Código de la empresa
     *                              ['ID_PER']  Int   : IdPersonaEmpresaRol del Cliente
     * 
     * @return String cadena Json del historial del cliente.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 04-04-2016
     */
    public function getJsonHistorialCliente($arrayParametros)
    {
        $objResultado = $this->getResultadoHistorialCliente($arrayParametros);
       
        if(empty($objResultado['ERROR']))
        {
            $strJsonResultado = '{"total":"' . $objResultado['TOTAL'] . '","registros":' . json_encode($objResultado['REGISTROS']) . '}';
        }
        else
        {
            $strJsonResultado = '{"total":"0", "registros":[], "error":[' . $objResultado['ERROR'] . ']}';
        }

        return $strJsonResultado;
    }
    
    /**
     * Documentación para el método 'getResultadoHistorialCliente'.
     *
     * Retorna el listado paginado del historial de cambios de los datos de facturación del cliente.
     *
     * @param Array $arrayParametros['EMPRESA'] String: Código de la empresa
     *                              ['ID_PER']  Int   : IdPersonaEmpresaRol del Cliente
     * 
     * @return Array Listado del historial del cliente
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 04-04-2016
     */                                                                                                                             
    public function getResultadoHistorialCliente($arrayParametros)
    {
        $arrayResult           = array();
        $arrayHistorialCliente = array();
        
        $intStart = 0;
        $intLimit = 0;
    
        try
        {
            // Se obtiene el Objeto Navite Query para reformular el SQL y obtener el total de registros.
            $objHistorialClienteNativeQuery = $this->getHistorialClienteNativeQuery($arrayParametros);
            if(empty($objHistorialClienteNativeQuery['ERROR']))
            {
                $objNativeQuery = $objHistorialClienteNativeQuery['OBJ_QUERY'];
                $strQuery       = $objNativeQuery->getSQL();
                $objNativeQuery->setSQL("SELECT COUNT(*) AS TOTAL FROM ($strQuery)");
                
                $intTotalRegistros   = $objNativeQuery->getSingleScalarResult(); // Se obtiene el total de registros

                if($intTotalRegistros > 0)
                {
                    $objNativeQuery->setSQL($strQuery);

                    // Se define el Inicio y el Límite de la paginación.
                    if(isset($arrayParametros['LIMIT']) &&  isset($arrayParametros['START']))
                    {
                        $intStart = $arrayParametros['LIMIT'];
                        $intLimit = $arrayParametros['START'];
                    }
                    
                    $arrayHistorialCliente = $this->setQueryLimit($objNativeQuery, $intLimit, $intStart)->getResult();
                }
                $arrayResult['REGISTROS'] = $arrayHistorialCliente;
                $arrayResult['TOTAL']     = $intTotalRegistros;
                $arrayResult['ERROR']     = '';
            }
            else
            {
                $arrayResult['ERROR'] = $objHistorialClienteNativeQuery['ERROR'];
            }
        }
        catch(\Exception $ex)
        {
            $arrayResult['ERROR'] = 'Error: ' . $ex->getMessage();
        }
        return $arrayResult;
    }
    
    /**
     * Documentación para el método 'getHistorialClienteNativeQuery'.
     *
     * Retorna el objeto NativeQuery para la obtención del historial de cambios de los datos de facturación del cliente.
     *
     * @param Array $arrayParametros['EMPRESA'] String: Código de la empresa
     *                              ['ID_PER']  Int   : IdPersonaEmpresaRol del Cliente
     * costoQuery: 8
     * 
     * @return Array Listado Historial
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 04-04-2016
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 13-09-2016
     * Se quita el filtro de ESTADO y MOTIVO de la consulta del historial.
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 16-09-2016 - Se modifica la función para que me retorne todo el historial del cliente o precliente cuando no se le envían los 
     *                           parámetros de motivo y/o estado.
     * @author Jorge Guerrero <jguerrerop@telconet.ec>
     * @version 1.3 21-09-2017 - Se elimina el filtro que valida si el campo observacion o motivo es null.
     *                           Se agrega un mensaje estandar para los registros que no posean una observacion.
     * Se agrega el orden por ID_PERSONA_EMPRESA_ROL_HISTO
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.4
     * @since 08-05-2018
     */
    private function getHistorialClienteNativeQuery($arrayParametros)
    {
        try 
        {
            $objMappingBuilder = new ResultSetMappingBuilder($this->_em);
            $objNativeQuery    = $this->_em->createNativeQuery(null, $objMappingBuilder);
            
            $strSelect  = "SELECT NVL(perh.OBSERVACION,'Se ha realizado una modificación del cliente') ACCION, perh.USR_CREACION USUARIO,"
                          ."perh.FE_CREACION FECHA ";
            $strFrom    = "FROM INFO_PERSONA_EMPRESA_ROL_HISTO perh ";
            $strJoin    = "INNER JOIN INFO_PERSONA_EMPRESA_ROL per ON per.ID_PERSONA_ROL = perh.PERSONA_EMPRESA_ROL_ID
                           INNER JOIN INFO_EMPRESA_ROL ier ON ier.ID_EMPRESA_ROL = per.EMPRESA_ROL_ID ";
            $strWhere   = "WHERE perh.PERSONA_EMPRESA_ROL_ID  = :ID_CLIENTE 
                             AND ier.EMPRESA_COD = :EMPRESA ";
            $strOrderBy = "ORDER BY perh.FE_CREACION DESC, perh.ID_PERSONA_EMPRESA_ROL_HISTO DESC ";
            
            if( !empty($arrayParametros['MOTIVO']) )
            {
                $strJoin  .= "INNER JOIN ADMI_MOTIVO am ON am.ID_MOTIVO = perh.MOTIVO_ID ";
                $strWhere .= "AND am.NOMBRE_MOTIVO = :MOTIVO ";
                $objNativeQuery->setParameter('MOTIVO', $arrayParametros['MOTIVO']);
            }
            
            if( !empty($arrayParametros['ESTADO']) )
            {
                $strWhere .= "AND perh.ESTADO = :ESTADO ";
                $objNativeQuery->setParameter('ESTADO', $arrayParametros['ESTADO']);
            }
            
            $strSQL = $strSelect.$strFrom.$strJoin.$strWhere.$strOrderBy;

            $objNativeQuery->setParameter('ID_CLIENTE', $arrayParametros['ID_PER']);
            $objNativeQuery->setParameter('EMPRESA',    $arrayParametros['EMPRESA']);

            $objMappingBuilder->addScalarResult('ACCION',  'accion',  'string');
            $objMappingBuilder->addScalarResult('USUARIO', 'usuario', 'string');
            $objMappingBuilder->addScalarResult('FECHA',   'fecha',   'string');
            $objMappingBuilder->addScalarResult('TOTAL',   'total',   'integer');

            $objNativeQuery->setSQL($strSQL);

            $arrayResult['OBJ_QUERY'] = $objNativeQuery->setSQL($strSQL);
            $arrayResult['ERROR']     = '';
        }
        catch(\Exception $ex)
        {
            $arrayResult['ERROR'] = 'ERROR: ' . $ex->getMessage();
        }
        
        return $arrayResult;
	}
    
    
    
    /**
     * getJSONDocumentosPersona
     *
     * Método que genera el json de los documentos de una persona                            
     *
     * @param array $arrayParametros[
     *                                  tipoDocumentoGeneral    : id del tipo de documento general
     *                                  idPersonaEmpresaRol     : id de la persona empresa rol
     *                                  criterios       
     *                                      nombres             : nombres de la persona
     *                                      apellidos           : apellidos de la persona
     *                                      identificacion      : identificacion de la persona
     *                                      departamento        : id del departamento de la persona
     *                                      canton              : id del canton al que pertenece la persona
     *                                      region              : region a la que pertenece la persona "R1" O "R2"       
     *                                  intStart                : inicio del rownum
     *                                  intLimit                : fin del rownum
     *                              ]
     * 
     * @return json $jsonData
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 27-07-2016
     * 
     */
    public function getJSONDocumentosPersona($arrayParametros)
    {
        $arrayEncontrados       = array();
        $arrayResultado         = $this->getResultadoDocumentosPersona($arrayParametros);
        $arrayRegistros         = $arrayResultado['resultado'];
        $intTotal               = $arrayResultado['total'];

        if( $arrayRegistros )
        {
            foreach( $arrayRegistros as $data )
            {
                $arrayEncontrados[]=array(  'idPersonaEmpresaRol'       => $data['idPersonaEmpresaRol'],
                                            'idPersona'                 => $data['idPersona'],
                                            'tipoIdentificacion'        => ($data['tipoIdentificacion'] == "CED" ? "Cedula" : 
                                                                                                                $data['tipoIdentificacion']),
                                            'identificacion'            => ($data['identificacion'] ? $data['identificacion'] : 0),
                                            'nombres'                   => ($data['nombres'] ? $data['nombres']:""),
                                            'apellidos'                 => ($data['apellidos'] ? $data['apellidos']:""),
                                            'nacionalidad'              => ($data['nacionalidad'] == "EXT" ? "Extranjera" : 
                                                                            ($data['nacionalidad'] == "NAC" ? "Nacional" : "")),
                                            'direccion'                 => ($data['direccion'] ? $data['direccion'] : ""),
                                            'idDepartamento'            => ($data['idDepartamento'] ? $data['idDepartamento'] : ""),
                                            'nombreDepartamento'        => ($data['nombreDepartamento'] ? $data['nombreDepartamento'] : ""),
                                            'idCanton'                  => ($data['idCanton'] ? $data['idCanton'] : ""),
                                            'nombreCanton'              => ($data['nombreCanton'] ? $data['nombreCanton'] : ""),
                                            'idDocumentoRelacion'       => ($data['idDocumentoRelacion'] ? $data['idDocumentoRelacion'] : ""),
                                            'idDocumento'               => ($data['idDocumento'] ? $data['idDocumento'] : ""),
                                            'ubicacionFisicaDocumento'  => ($data['ubicacionFisicaDocumento'] ? $data['ubicacionFisicaDocumento']:""),
                                            'actionSubirDoc'            => ($data['idDocumentoRelacion']) ? 'icon-invisible' 
                                                                            :'button-grid-agregarArchivoCaso',
                                            'actionVerDoc'              => ($data['idDocumentoRelacion']) ? 'button-grid-show' 
                                                                            :'icon-invisible',
                                            'actionEliminarDoc'         => ($data['idDocumentoRelacion']) ? 'button-grid-delete' 
                                                                            :'icon-invisible'
                                         );
            }
        }

        $arrayRespuesta = array('total' => $intTotal, 'encontrados' => $arrayEncontrados);
        $jsonData       = json_encode($arrayRespuesta);

        return $jsonData;
    }
    
    /**
     * getResultadoDocumentosPersona
     *
     * Método que consulta los documentos de una persona                      
     *
     * @param array $arrayParametros[
     *                                  tipoDocumentoGeneral    : id del tipo de documento general
     *                                  idPersonaEmpresaRol     : id de la persona empresa rol
     *                                  criterios       
     *                                      nombres             : nombres de la persona
     *                                      apellidos           : apellidos de la persona
     *                                      identificacion      : identificacion de la persona
     *                                      departamento        : id del departamento de la persona
     *                                      canton              : id del canton al que pertenece la persona
     *                                      region              : region a la que pertenece la persona "R1" O "R2"       
     *                                  intStart                : inicio del rownum
     *                                  intLimit                : fin del rownum
     *                              ]
     * 
     * @return array $arrayRespuesta
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 27-07-2016
     * 
     */
    public function getResultadoDocumentosPersona($arrayParametros)
    {
        $arrayRespuesta['total'] = 0;
        $arrayRespuesta['resultado'] = "";
        try
        {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsmCount = new ResultSetMappingBuilder($this->_em);
            $ntvQuery = $this->_em->createNativeQuery(null, $rsm);
            $ntvQueryCount = $this->_em->createNativeQuery(null, $rsmCount);

            $strSelectCount = " SELECT COUNT(*) AS TOTAL ";
            $strSelect       = "SELECT DISTINCT (per.ID_PERSONA_ROL),per.ESTADO,p.ID_PERSONA,"
                               ."p.TIPO_IDENTIFICACION, p.IDENTIFICACION_CLIENTE,p.NOMBRES,p.APELLIDOS,p.NACIONALIDAD,"
                               ."p.DIRECCION,eg.COD_EMPRESA,d.ID_DEPARTAMENTO,d.NOMBRE_DEPARTAMENTO,"
                               ."c.ID_CANTON,c.NOMBRE_CANTON, "
                               ."docRel.ID_DOCUMENTO_RELACION,doc.ID_DOCUMENTO, doc.UBICACION_FISICA_DOCUMENTO ";
                   
            $strFromAndWhere = "FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL per
                                INNER JOIN DB_COMERCIAL.INFO_PERSONA p ON p.ID_PERSONA = per.PERSONA_ID 
                                INNER JOIN DB_COMERCIAL.INFO_EMPRESA_ROL er ON er.ID_EMPRESA_ROL = per.EMPRESA_ROL_ID 
                                INNER JOIN DB_COMERCIAL.INFO_EMPRESA_GRUPO eg ON eg.COD_EMPRESA = er.EMPRESA_COD 
                                INNER JOIN DB_COMERCIAL.INFO_OFICINA_GRUPO og ON og.ID_OFICINA = per.OFICINA_ID 
                                INNER JOIN DB_COMERCIAL.ADMI_ROL r ON r.ID_ROL = er.ROL_ID 
                                INNER JOIN DB_COMERCIAL.ADMI_TIPO_ROL tr ON tr.ID_TIPO_ROL = r.TIPO_ROL_ID 
                                INNER JOIN DB_COMERCIAL.ADMI_DEPARTAMENTO d ON d.ID_DEPARTAMENTO = per.DEPARTAMENTO_ID 
                                INNER JOIN DB_COMERCIAL.ADMI_CANTON c ON c.ID_CANTON = og.CANTON_ID 
                                LEFT JOIN  DB_COMUNICACION.INFO_DOCUMENTO_RELACION docRel ON docRel.PERSONA_EMPRESA_ROL_ID=per.ID_PERSONA_ROL
                                                                                    AND docRel.ESTADO = :estado 
                                LEFT JOIN DB_COMUNICACION.INFO_DOCUMENTO doc ON doc.ID_DOCUMENTO = docRel.DOCUMENTO_ID 
                                WHERE 
                                tr.DESCRIPCION_TIPO_ROL = :descripcionTipoRol AND
                                er.EMPRESA_COD = :empresaCod AND
                                per.ESTADO= :estado ";
            
            
            $rsm->addScalarResult('ID_PERSONA_ROL', 'idPersonaEmpresaRol', 'integer');
            $rsm->addScalarResult('ID_PERSONA', 'idPersona', 'integer');
            $rsm->addScalarResult('TIPO_IDENTIFICACION', 'tipoIdentificacion', 'string');
            $rsm->addScalarResult('IDENTIFICACION_CLIENTE', 'identificacion', 'string');
            $rsm->addScalarResult('NOMBRES', 'nombres', 'string');
            $rsm->addScalarResult('APELLIDOS', 'apellidos', 'string');
            $rsm->addScalarResult('NACIONALIDAD', 'nacionalidad', 'string');
            $rsm->addScalarResult('DIRECCION', 'direccion', 'string');
            $rsm->addScalarResult('ID_DEPARTAMENTO', 'idDepartamento', 'integer');
            $rsm->addScalarResult('NOMBRE_DEPARTAMENTO', 'nombreDepartamento', 'string');
            $rsm->addScalarResult('ID_CANTON', 'idCanton', 'integer');
            $rsm->addScalarResult('NOMBRE_CANTON', 'nombreCanton', 'string');
            $rsm->addScalarResult('ID_DOCUMENTO_RELACION', 'idDocumentoRelacion', 'integer');
            $rsm->addScalarResult('ID_DOCUMENTO', 'idDocumento', 'integer');
            $rsm->addScalarResult('UBICACION_FISICA_DOCUMENTO', 'ubicacionFisicaDocumento', 'string');
            
            $rsmCount->addScalarResult('TOTAL', 'total', 'integer');
            
            $ntvQuery->setParameter('empresaCod', $arrayParametros['empresaCod']);
            $ntvQueryCount->setParameter('empresaCod', $arrayParametros['empresaCod']);
            
            $ntvQuery->setParameter('estado', 'Activo');
            $ntvQueryCount->setParameter('estado', 'Activo');
            
            $ntvQuery->setParameter('descripcionTipoRol', 'Empleado');
            $ntvQueryCount->setParameter('descripcionTipoRol', 'Empleado');
            
            
            $strWhereBusqueda = '';

            if(isset($arrayParametros['tipoDocumentoGeneral']))
            {
                if($arrayParametros['tipoDocumentoGeneral'])
                {
                    $strWhereBusqueda .= 'AND doc.TIPO_DOCUMENTO_GENERAL_ID = :tipoDocumentoGeneralId ';
                    $ntvQuery->setParameter('tipoDocumentoGeneralId', $arrayParametros["tipoDocumentoGeneralId"]);
                    $ntvQueryCount->setParameter('tipoDocumentoGeneralId', $arrayParametros["tipoDocumentoGeneralId"]);  
                }
            }
            if(isset($arrayParametros['idPersonaEmpresaRol']))
            {
                if($arrayParametros['idPersonaEmpresaRol'])
                {
                    $strWhereBusqueda .= 'AND per.ID_PERSONA_ROL = :idPersonaEmpresaRol ';
                    $ntvQuery->setParameter('idPersonaEmpresaRol', $arrayParametros["idPersonaEmpresaRol"]);
                    $ntvQueryCount->setParameter('idPersonaEmpresaRol', $arrayParametros["idPersonaEmpresaRol"]);  
                }
            }
            
            if(isset($arrayParametros['criterios']))
            {
                if(isset($arrayParametros['criterios']['nombres']) )
                {
                    if($arrayParametros['criterios']['nombres'])
                    {
                        $strWhereBusqueda .= 'AND p.NOMBRES LIKE :nombres ';        
                        $ntvQuery->setParameter('nombres', '%'.strtoupper($arrayParametros['criterios']['nombres']).'%');
                        $ntvQueryCount->setParameter('nombres', '%'.strtoupper($arrayParametros['criterios']['nombres']).'%');

                    }
                }
                if(isset($arrayParametros['criterios']['apellidos']) )
                {
                    if($arrayParametros['criterios']['apellidos'])
                    {
                        $strWhereBusqueda .= 'AND p.APELLIDOS LIKE :apellidos ';        
                        $ntvQuery->setParameter('apellidos', '%'.strtoupper($arrayParametros['criterios']['apellidos']).'%');
                        $ntvQueryCount->setParameter('apellidos', '%'.strtoupper($arrayParametros['criterios']['apellidos']).'%');

                    }
                }

                if(isset($arrayParametros['criterios']['identificacion']) )
                {
                    if($arrayParametros['criterios']['identificacion'])
                    {
                        $strWhereBusqueda .= 'AND p.IDENTIFICACION_CLIENTE LIKE :identificacion ';        
                        $ntvQuery->setParameter('identificacion', '%'.$arrayParametros['criterios']['identificacion'].'%');
                        $ntvQueryCount->setParameter('identificacion', '%'.$arrayParametros['criterios']['identificacion'].'%');

                    }
                }
                
                if(isset($arrayParametros['criterios']['departamento']))
                {
                    if($arrayParametros['criterios']['departamento'])
                    {
                        if($arrayParametros['criterios']['departamento']!="Todos")
                        {
                            $strWhereBusqueda .= 'AND per.DEPARTAMENTO_ID = :idDepartamento ';        
                            $ntvQuery->setParameter('idDepartamento', $arrayParametros['criterios']['departamento']);
                            $ntvQueryCount->setParameter('idDepartamento', $arrayParametros['criterios']['departamento']);

                        }
                    }
                }
                
                if(isset($arrayParametros['criterios']['canton']) )
                {
                    if($arrayParametros['criterios']['canton'])
                    {
                        if($arrayParametros['criterios']['canton']!="Todos")
                        {
                            $strWhereBusqueda .= 'AND og.CANTON_ID = :idCanton ';        
                            $ntvQuery->setParameter('idCanton', $arrayParametros['criterios']['canton']);
                            $ntvQueryCount->setParameter('idCanton', $arrayParametros['criterios']['canton']);

                        }
                    } 
                }
                if(isset($arrayParametros['criterios']['region']) )
                {
                    if($arrayParametros['criterios']['region'])
                    {
                        $strWhereBusqueda .= 'AND c.REGION = :region ';        
                        $ntvQuery->setParameter('region', $arrayParametros['criterios']['region']);
                        $ntvQueryCount->setParameter('region', $arrayParametros['criterios']['region']);

                    }
                }
            }

            $strOrderBy=" ORDER BY p.NOMBRES, p.APELLIDOS ";
            
            $strSqlPrincipal = $strSelect . $strFromAndWhere .  $strWhereBusqueda . $strOrderBy;

            $strSqlFinal = '';

            if(isset($arrayParametros['intStart']) && isset($arrayParametros['intLimit']))
            {
                if($arrayParametros['intStart'] && $arrayParametros['intLimit'])
                {
                    $intInicio = $arrayParametros['intStart'];
                    $intFin = $arrayParametros['intStart'] + $arrayParametros['intLimit'];
                    $strSqlFinal = '  SELECT * FROM 
                                        (
                                            SELECT consultaPrincipal.*,rownum AS consultaPrincipal_rownum 
                                            FROM (' . $strSqlPrincipal . ') consultaPrincipal 
                                            WHERE rownum<=' . $intFin . '
                                        ) WHERE consultaPrincipal_rownum >' . $intInicio;
                }
                else
                {
                    $strSqlFinal = '  SELECT consultaPrincipal.* 
                                        FROM (' . $strSqlPrincipal . ') consultaPrincipal 
                                        WHERE rownum<=' . $arrayParametros['intLimit'];
                }
            }
            else
            {
                $strSqlFinal = $strSqlPrincipal;
            }
            
            
            $ntvQuery->setSQL($strSqlFinal);
            $arrayResultado = $ntvQuery->getResult();
            
            $strSqlCount = $strSelectCount . " FROM (" . $strSqlPrincipal . ")";
            $ntvQueryCount->setSQL($strSqlCount);

            $intTotal = $ntvQueryCount->getSingleScalarResult();

            $arrayRespuesta['resultado'] = $arrayResultado;
            $arrayRespuesta['total'] = $intTotal;

        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
        }

        return $arrayRespuesta;
    }

      
    /**
     * Funcion que sirve para obtener el array con la informacion de los empleados para validacion de Networking en aplicativos de su jurisdiccion
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0
     * @since 10-10-2016
     * 
     * @param Array $arrayParametros [ strTipo , strCodEmpresa ]
     * @return Array $arrayResultado [ total , resultado ] 
     * @throws \telconet\schemaBundle\Repository\Exception
     */
    public function getArrayInformacionEmpleados($arrayParametros)
    {                
        try
        {
            $arrayParametros['strTipoBusqueda'] = 'cont';
            $objQueryCont = $this->getInformacionEmpleados($arrayParametros);

            $arrayParametros['strTipoBusqueda'] = 'data';
            $objQueryData = $this->getInformacionEmpleados($arrayParametros);

            $arrayResultado['total']      = $objQueryCont->getSingleScalarResult();
            $arrayResultado['data']       = $objQueryData->getArrayResult();
            
            return $arrayResultado;        
        }
        catch(\Exception $e)
        {
            throw ($e);
        }        
    }
    /**
     * Funcion que sirve para obtener la informacion de los empleados para validacion de Networking en aplicativos de su jurisdiccion
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 11-09-2016
     * 
     * Costo : 6671
     * 
     * @param Array $arrayParametros [ strTipoBusqueda , strCodEmpresa , strTipoRol ]
     * @return Object $objQuery
     * @throws \telconet\schemaBundle\Repository\Exception
     */
    public function getInformacionEmpleados($arrayParametros)
    {        
        try
        {
            $rsm = new ResultSetMappingBuilder($this->_em);	      
            $objQuery = $this->_em->createNativeQuery(null, $rsm);        
            
            switch ($arrayParametros['strTipoBusqueda']):
                case 'cont':
                    $strSelect = "SELECT COUNT(*) CONT ";
                    $rsm->addScalarResult('CONT','cont','integer'); 
                    break;
                case 'data':
                    $strSelect = "SELECT 
                                    PERSONA.NOMBRES,
                                    PERSONA.APELLIDOS,
                                    PERSONA.LOGIN,
                                    DEPARTAMENTO.NOMBRE_DEPARTAMENTO,
                                    OFICINA.NOMBRE_OFICINA,
                                    AREA.NOMBRE_AREA,
                                    PERSONA_ROL.ESTADO ";
                    
                    $rsm->addScalarResult('NOMBRES','nombres','string');                        
                    $rsm->addScalarResult('APELLIDOS','apellidos','string');			             
                    $rsm->addScalarResult('LOGIN','login','string');			              
                    $rsm->addScalarResult('NOMBRE_DEPARTAMENTO','departamento','string');
                    $rsm->addScalarResult('NOMBRE_OFICINA','oficina','string');
                    $rsm->addScalarResult('NOMBRE_AREA','area','string');
                    $rsm->addScalarResult('ESTADO','estado','string');
                    break;
            endswitch;
            
            $strSql = $strSelect.
                      "FROM 
                        INFO_PERSONA             PERSONA,
                        INFO_PERSONA_EMPRESA_ROL PERSONA_ROL,
                        INFO_OFICINA_GRUPO       OFICINA,
                        ADMI_DEPARTAMENTO        DEPARTAMENTO,
                        ADMI_AREA                AREA,
                        INFO_EMPRESA_ROL         EMPRESA_ROL,
                        ADMI_ROL                 ROL,
                        ADMI_TIPO_ROL            TIPO_ROL
                      WHERE 
                            PERSONA.ID_PERSONA          = PERSONA_ROL.PERSONA_ID
                      AND PERSONA_ROL.OFICINA_ID        = OFICINA.ID_OFICINA
                      AND PERSONA_ROL.DEPARTAMENTO_ID   = DEPARTAMENTO.ID_DEPARTAMENTO
                      AND PERSONA_ROL.EMPRESA_ROL_ID    = EMPRESA_ROL.ID_EMPRESA_ROL
                      AND EMPRESA_ROL.ROL_ID            = ROL.ID_ROL
                      AND ROL.TIPO_ROL_ID               = TIPO_ROL.ID_TIPO_ROL
                      AND AREA.ID_AREA                  = DEPARTAMENTO.AREA_ID
                      AND TIPO_ROL.DESCRIPCION_TIPO_ROL = :tipoRol
                      AND EMPRESA_ROL.EMPRESA_COD       = :codEmpresa
                      AND PERSONA_ROL.ID_PERSONA_ROL    =
                        (SELECT MAX(ID_PERSONA_ROL)
                        FROM INFO_PERSONA_EMPRESA_ROL
                        WHERE PERSONA_ID = PERSONA.ID_PERSONA
                        )
                    ";
            
            $objQuery->setParameter('codEmpresa', $arrayParametros['strCodEmpresa']);        
            $objQuery->setParameter('tipoRol',    $arrayParametros['strTipoRol']);             
            
            $objQuery->setSQL($strSql);   
            
            return $objQuery;
        } 
        catch (\Exception $ex) 
        {
            throw $ex;
        }        
    }

    
    
    /**
    * Documentacion para la funcion getJsonClientesFacturas
    *
    * Función que retorna el listado de clientes con su factura inicial asociada
    * en formato json
    * @param mixed $arrayParametros[
    *                               'intEmpresaId'                => id empresa en sesion 
    *                               'strPrefijoEmpresa'           => prefijo de la empresa en sesion
    *                               'strUsrSesion'                => usuario en sesion
    *                               'strEmailUsrSesion'           => email usuario en sesion
    *                               'fechaCreacionDesde'          => rango inicial para fecha de creacion
    *                               'fechaCreacionHasta'          => rango final para fecha de creacion
    *                               'fechaActivacionDesde'        => rango inicial para fecha de activación
    *                               'fechaActivacionHasta'        => rango final para fecha de activación
    *                               'strIdentificacion'           => identificación del cliente
    *                               'strRazonSocial'              => razón social del cliente
    *                               'strNombres'                  => nombres del cliente
    *                               'strApellidos'                => apellidos del cliente
    *                               'strIdsPlan'                  => ids para filtrar por plan
    *                               'strIdsOficinasVendedor'      => ids para filtrar por oficina del vendedor   
    *                               'strIdsOficinasPtoCobertura'  => ids para filtrar por oficina de punto de cobertura
    *                               'intStart'                    => limite de rango de inicio para realizar consulta
    *                               'intLimit'                    => limite de rango final para realizar consulta
    *                               ]
    *
    * @param  router $objRouter
    * @return json   $objJsonData
    *
    * @author Edgar Holguin <eholguin@telconet.ec>
    * @version 1.0 02-12-2016
    *
    */

    public function getJsonClientesFacturas($arrayParametros,$objRouter = null)
    {
        $intTotal      = 0;
        $arrayDatos    = array();
        try
        { 
            
            if($arrayParametros && count($arrayParametros)>0)
            {

                $objCursor  = $arrayParametros['cursor'];

                $strSql = "BEGIN
                            DB_COMERCIAL.CMKG_REPORTE_COMERCIAL.P_GET_CLIENTES_FACTURAS
                            (
                                :Pn_EmpresaId,
                                :Pv_PrefijoEmpresa,
                                :Pv_UsrSesion,
                                :Pv_EmailUsrSesion,
                                :Pv_FechaCreacionDesde,
                                :Pv_FechaCreacionHasta,
                                :Pv_FechaActivacionDesde,
                                :Pv_FechaActivacionHasta,      
                                :Pv_FechaPrePlanificacionDesde,
                                :Pv_FechaPrePlanificacionHasta,                                  
                                :Pv_Identificacion,
                                :Pv_RazonSocial,
                                :Pv_Nombres,
                                :Pv_Apellidos,
                                :Pv_IdsPlan,
                                :Pv_IdsOficinasVendedor,
                                :Pv_IdsOficinasPtoCobertura,
                                :Pn_Start,
                                :Pn_Limit,
                                :Pn_TotalRegistros,
                                :Pc_Documentos
                            );
                        END;";

                $objStmt           = oci_parse($arrayParametros['oci_con'], $strSql);
                
                oci_bind_by_name($objStmt, ":Pn_EmpresaId", $arrayParametros['intEmpresaId']);
                oci_bind_by_name($objStmt, ":Pv_PrefijoEmpresa", $arrayParametros['strPrefijoEmpresa']);
                oci_bind_by_name($objStmt, ":Pv_UsrSesion", $arrayParametros['strUsrSesion']);
                oci_bind_by_name($objStmt, ":Pv_EmailUsrSesion", $arrayParametros['strEmailUsrSesion']);
                oci_bind_by_name($objStmt, ":Pv_FechaCreacionDesde", $arrayParametros['strFechaDesde']);
                oci_bind_by_name($objStmt, ":Pv_FechaCreacionHasta", $arrayParametros['strFechaHasta']);
                oci_bind_by_name($objStmt, ":Pv_FechaActivacionDesde", $arrayParametros['strFechaActivacionDesde']);
                oci_bind_by_name($objStmt, ":Pv_FechaActivacionHasta", $arrayParametros['strFechaActivacionHasta']);
                oci_bind_by_name($objStmt, ":Pv_FechaPrePlanificacionDesde", $arrayParametros['strFechaPrePlanificacionDesde']);
                oci_bind_by_name($objStmt, ":Pv_FechaPrePlanificacionHasta", $arrayParametros['strFechaPrePlanificacionHasta']);                
                oci_bind_by_name($objStmt, ":Pv_Identificacion", $arrayParametros['strIdentificacion']);
                oci_bind_by_name($objStmt, ":Pv_RazonSocial", $arrayParametros['strRazonSocial']);
                oci_bind_by_name($objStmt, ":Pv_Nombres", $arrayParametros['strNombres']);
                oci_bind_by_name($objStmt, ":Pv_Apellidos", $arrayParametros['strApellidos']);
                oci_bind_by_name($objStmt, ":Pv_IdsPlan", $arrayParametros['strIdsPlan']);
                oci_bind_by_name($objStmt, ":Pv_IdsOficinasVendedor", $arrayParametros['strIdsOficinasVendedor']);
                oci_bind_by_name($objStmt, ":Pv_IdsOficinasPtoCobertura", $arrayParametros['strIdsOficinasPtoCobertura']);
                oci_bind_by_name($objStmt, ":Pn_Start", $arrayParametros['intStart']);
                oci_bind_by_name($objStmt, ":Pn_Limit", $arrayParametros['intLimit']); 
                oci_bind_by_name($objStmt, ":Pn_TotalRegistros", $intTotal, 10);   
                oci_bind_by_name($objStmt, ":Pc_Documentos", $objCursor, -1, OCI_B_CURSOR);

                oci_execute($objStmt); 
                oci_execute($objCursor, OCI_DEFAULT);

                while (($row = oci_fetch_array($objCursor)) != false)
                {
                    $strUrlVer       = $objRouter->generate('cliente_show', array('id' => $row['ID_CLIENTE'],'idper'=>$row['ID_ROL']));
                    $arrayDatos[] = array(
                                           'idPersona'           => $row['ID_CLIENTE'],
                                           'idPerCli'            => $row['ID_ROL'],
                                           'Nombre'              => $row['CLIENTE'],
                                           'Direccion'           => $row['DIRECCION'],
                                           'fechaCreacion'       => $row['FE_CREACION_PER'],
                                           'usuarioCreacion'     => $row['USR_CREACION'],
                                           'login'               => $row['LOGIN'],
                                           'vendedor'            => $row['USR_VENDEDOR'],
                                           'servicio'            => $row['SERVICIO'],
                                           'fePrePlanificacion'  => $row['FE_PREPLANIFICACION'],                        
                                           'feActivacion'        => $row['FE_ACTIVACION'],
                                           'pagos'               => $row['TOTAL_PAGOS'],
                                           'feEmisionFactura'    => $row['FE_EMISION'],
                                           'numeroFactura'       => $row['NUMERO_FACTURA_SRI'],
                                           'estadoFactura'       => $row['ESTADO_IMPRESION_FACT'],
                                           'identificacion'      => $row['IDENTIFICACION'],
                                           'estado'              => $row['ESTADO_ROL'],
                                           'numeroEmpPub'        => $row['NUMERO_EMP_PUB'],
                                           'ofiVendedor'         => $row['OFICINA_VENDEDOR'],
                                           'ofiPtoCobertura'     => $row['OFICINA_PTO_COBERTURA'],
                                           'usuarioAprobacion'   => $row['USR_APROBACION'], 
                                           'fechaAprobacion'     => $row['FE_APROBACION'],
                                           'estadoContrato'      => $row['ESTADO_CONTRATO'],
                                           'descripcionCuenta'   => $row['DESCRIPCION_CUENTA'],
                                           'linkVer'             => $strUrlVer,
                                         );
                } 
            }
            
            $arrayResultado = array('total' => $intTotal, 'clientes' => $arrayDatos);
           
        }catch (\Exception $e) 
        {
            error_log($e->getMessage());
            throw($e);
        }

        
        return $arrayResultado;
    }
    
    
    
    
   /**
    * Documentación para el método 'generarReporteClientesFacturas'.
    *
    * Ejecuta la generación y envío de reporte de clientes - facturas según los parámetros indicados.
    *
    * @param mixed $arrayParametros[
    *                               'intEmpresaId'                => id empresa en sesion 
    *                               'strPrefijoEmpresa'           => prefijo de la empresa en sesion
    *                               'strUsrSesion'                => usuario en sesion
    *                               'strEmailUsrSesion'           => email usuario en sesion
    *                               'fechaCreacionDesde'          => rango inicial para fecha de creacion
    *                               'fechaCreacionHasta'          => rango final para fecha de creacion
    *                               'fechaActivacionDesde'        => rango inicial para fecha de activación
    *                               'fechaActivacionHasta'        => rango final para fecha de activación
    *                               'fechaPrePlanificacionDesde'  => rango inicial para fecha de pre-planificación
    *                               'fechaPrePlanificacionHasta'  => rango final para fecha de pre-planificación
    *                               'strIdentificacion'           => identificación del cliente
    *                               'strRazonSocial'              => razón social del cliente
    *                               'strNombres'                  => nombres del cliente
    *                               'strApellidos'                => apellidos del cliente
    *                               'strIdsPlan'                  => ids para filtrar por plan
    *                               'strIdsOficinasVendedor'      => ids para filtrar por oficina del vendedor
    *                               'strIdsOficinasPtoCobertura'  => ids para filtrar por oficina de punto de cobertura
    *                               ]
    *
    * @return strResultado  Resultado de generación del reporte.
    * @author Edgar Holguin <eholguin@telconet.ec>
    * @version 1.0 15-10-2016
    */
    public function generarReporteClientesFacturas($arrayParametros)
    {
        $strResultado = "";
        try
        {
            if($arrayParametros && count($arrayParametros)>0)
            {
                $strSql = "BEGIN
                            DB_COMERCIAL.CMKG_REPORTE_COMERCIAL.P_REPORTE_CLIENTES_FACTURAS
                            (
                                :Pn_EmpresaId,
                                :Pv_PrefijoEmpresa,
                                :Pv_UsrSesion,
                                :Pv_EmailUsrSesion,
                                :Pv_FechaCreacionDesde,
                                :Pv_FechaCreacionHasta,
                                :Pv_FechaActivacionDesde,
                                :Pv_FechaActivacionHasta,
                                :Pv_FechaPrePlanificacionDesde,
                                :Pv_FechaPrePlanificacionHasta,                                
                                :Pv_Identificacion,
                                :Pv_RazonSocial,
                                :Pv_Nombres,
                                :Pv_Apellidos,
                                :Pv_IdsPlan,
                                :Pv_IdsOficinasVendedor,
                                :Pv_IdsOficinasPtoCobertura,
                                :Pv_MsjResultado
                            );
                        END;";

                $objStmt = $this->_em->getConnection()->prepare($strSql);
               
                $strResultado = str_pad($strResultado, 5000, " ");
                $objStmt->bindParam('Pn_EmpresaId', $arrayParametros['intEmpresaId']);
                $objStmt->bindParam('Pv_PrefijoEmpresa', $arrayParametros['strPrefijoEmpresa']);
                $objStmt->bindParam('Pv_UsrSesion', $arrayParametros['strUsrSesion']);
                $objStmt->bindParam('Pv_EmailUsrSesion', $arrayParametros['strEmailUsrSesion']);
                $objStmt->bindParam('Pv_FechaCreacionDesde', $arrayParametros['strFechaDesde']);
                $objStmt->bindParam('Pv_FechaCreacionHasta', $arrayParametros['strFechaHasta']);
                $objStmt->bindParam('Pv_FechaActivacionDesde', $arrayParametros['strFechaActivacionDesde']);
                $objStmt->bindParam('Pv_FechaActivacionHasta', $arrayParametros['strFechaActivacionHasta']);
                $objStmt->bindParam('Pv_FechaPrePlanificacionDesde', $arrayParametros['strFechaPrePlanificacionDesde']);
                $objStmt->bindParam('Pv_FechaPrePlanificacionHasta', $arrayParametros['strFechaPrePlanificacionHasta']);                
                $objStmt->bindParam('Pv_Identificacion', $arrayParametros['strIdentificacion']);
                $objStmt->bindParam('Pv_RazonSocial', $arrayParametros['strRazonSocial']);
                $objStmt->bindParam('Pv_Nombres', $arrayParametros['strNombres']);
                $objStmt->bindParam('Pv_Apellidos', $arrayParametros['strApellidos']); 
                $objStmt->bindParam('Pv_IdsPlan', $arrayParametros['strIdsPlan']); 
                $objStmt->bindParam('Pv_IdsOficinasVendedor', $arrayParametros['strIdsOficinasVendedor']); 
                $objStmt->bindParam('Pv_IdsOficinasPtoCobertura', $arrayParametros['strIdsOficinasPtoCobertura']); 
                $objStmt->bindParam('Pv_MsjResultado', $strResultado);

                $objStmt->execute();
            }
            else
            {
                $strResultado= 'No se enviaron parámetros para generar la consulta.';
            }            

        }catch (\Exception $e)
        {
            $strResultado= 'No se enviaron parámetros para generar la consulta.';
            throw($e);
        }
        
        return $strResultado; 
    }      

   /**
    * Documentacion para la funcion getJsonReporteAprobacionContratos
    *
    * Función que retorna el listado de Consulta de Gestion de Aprobación de Contratos en formato json
    * 
    * @param mixed $arrayParametros[
    *                               'strIdsPtoCobertura'             => ids para filtrar por punto de cobertura
    *                               'strFechaPrePlanificacionDesde'  => rango inicial para fecha de pre-planificación
    *                               'strFechaPrePlanificacionHasta'  => rango final para fecha de pre-planificación
    *                               'strEmpresaId'                   => id empresa en sesion 
    *                               'strPrefijoEmpresa'              => prefijo de la empresa en sesion
    *                               'strUsrSesion'                   => usuario en sesion
    *                               'intStart'                       => limite de rango de inicio para realizar consulta
    *                               'intLimit'                       => limite de rango final para realizar consulta
    *                               'objOciCon'                      => conexion  oci_connect
    *                               'objCursor'                      => cursor  oci_new_cursor
    *                               ]
    *
    * @return json   $objJsonData
    *
    * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
    * @version 1.0 10-07-2017
    *
    */

    /**
     * Función que devuelve el USUARIO de un cursor específico.
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0
     * @since 19-06-2018
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.1
     * @since 15-09-2020
     * Se modifica parametros de entrada para buscar la caracteristica del Producto y generar usuario.
     *          @param  Pv_CaracUsuario   Caracteristica del usuario.
     *          @param  Pv_NombreTecnico  Nombre técnico del producto.
     * 
     */
    public function generaUsuario($arrayParametros)
    {
        $objRsm   = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsm);
        $strSql   = "SELECT DB_COMERCIAL.COMEK_TRANSACTION.F_GENERA_USUARIO(:Pn_IdPersona,"
                                                                        . " :Pv_InfoTabla,"
                                                                        . " :Pv_PrefijoEmpresa,"
                                                                        . " :Pv_CaracUsuario,"
                                                                        . " :Pv_NombreTecnico)"
                        . " AS USUARIO"
                . " FROM DUAL";
        $objQuery->setParameter("Pn_IdPersona", $arrayParametros["intIdPersona"]);
        $objQuery->setParameter("Pv_InfoTabla", $arrayParametros["strInfoTabla"]);
        $objQuery->setParameter("Pv_PrefijoEmpresa", $arrayParametros["strPrefijoEmpresa"]);
        $objQuery->setParameter("Pv_CaracUsuario", $arrayParametros['strCaracUsuario']);
        $objQuery->setParameter("Pv_NombreTecnico", $arrayParametros["strNombreTecnico"]);

        $objRsm->addScalarResult('USUARIO', 'usuario', 'string');
        $objQuery->setSQL($strSql);
        $arrayRespuesta = $objQuery->getScalarResult();

        return $arrayRespuesta[0]["usuario"];
    }

    public function getJsonReporteAprobacionContratos($arrayParametros)
    {
        $intTotal      = 0;
        $arrayDatos    = array();
        try
        { 
            
            if($arrayParametros && count($arrayParametros)>0)
            {

                $objCursor  = $arrayParametros['objCursor'];

                $strSql = "BEGIN
                            DB_COMERCIAL.CMKG_REPORTE_APROB_CONTRATOS.P_GET_CLIENTES_APROB_CONTRATOS
                            (
                                :Pn_EmpresaId,
                                :Pv_PrefijoEmpresa,
                                :Pv_UsrSesion,
                                :Pv_FechaPrePlanificacionDesde,
                                :Pv_FechaPrePlanificacionHasta,
                                :Pv_IdsPtoCobertura,
                                :Pn_Start,
                                :Pn_Limit,
                                :Pn_TotalRegistros,
                                :Pc_Documentos
                            );
                        END;";

                $objStmt           = oci_parse($arrayParametros['objOciCon'], $strSql);
                
                oci_bind_by_name($objStmt, ":Pn_EmpresaId", $arrayParametros['strEmpresaId']);
                oci_bind_by_name($objStmt, ":Pv_PrefijoEmpresa", $arrayParametros['strPrefijoEmpresa']);
                oci_bind_by_name($objStmt, ":Pv_UsrSesion", $arrayParametros['strUsrSesion']);
                oci_bind_by_name($objStmt, ":Pv_FechaPrePlanificacionDesde", $arrayParametros['strFechaPrePlanificacionDesde']);
                oci_bind_by_name($objStmt, ":Pv_FechaPrePlanificacionHasta", $arrayParametros['strFechaPrePlanificacionHasta']);
                oci_bind_by_name($objStmt, ":Pv_IdsPtoCobertura", $arrayParametros['strIdsPtoCobertura']);
                oci_bind_by_name($objStmt, ":Pn_Start", $arrayParametros['intStart']);
                oci_bind_by_name($objStmt, ":Pn_Limit", $arrayParametros['intLimit']); 
                oci_bind_by_name($objStmt, ":Pn_TotalRegistros", $intTotal, 10);
                oci_bind_by_name($objStmt, ":Pc_Documentos", $objCursor, -1, OCI_B_CURSOR);

                oci_execute($objStmt); 
                oci_execute($objCursor, OCI_DEFAULT);

                while (($arrayRow = oci_fetch_array($objCursor)))
                {                    
                    $arrayDatos[] = array(
                                           'intIdServicio'             => $arrayRow['ID_SERVICIO'],
                                           'strLogin'                  => $arrayRow['LOGIN_PUNTO'],
                                           'strFePrePlanificacion'     => $arrayRow['FE_PREPLANIFICACION'],
                                           'strUltEstadoSolPlanific'   => $arrayRow['ULT_ESTADO_SOL_PLANIFIC'],
                                           'strNumContratoEmpPub'      => $arrayRow['NUM_CONTRATO_EMP_PUB'],
                                           'strNumContratoSistema'     => $arrayRow['NUM_CONTRATO_SISTEMA'],
                                           'strIdentificacion'         => $arrayRow['IDENTIFICACION'],
                                           'strNombreCliente'          => $arrayRow['NOMBRE_CLIENTE'],
                                           'strPtoCobertura'           => $arrayRow['PTO_COBERTURA'],
                                           'strUsrAprobacion'          => $arrayRow['USUARIO_APROBACION'],
                                           'strVendedor'               => $arrayRow['VENDEDOR'],
                                           'strFeCreacionProspecto'    => $arrayRow['FE_CREACION_PROSPECTO'],
                                           'strFeCreacionPto'          => $arrayRow['FE_CREACION_PTO'],
                                           'strFeCreacionServicio'     => $arrayRow['FE_CREACION_SERVICIO'],
                                           'strFeFactible'             => $arrayRow['FE_FACTIBLE'],
                                           'strCanalVenta'             => $arrayRow['CANAL_VENTA'],
                                           'strPuntoVenta'             => $arrayRow['PUNTO_VENTA'],
                                           'strFormaPago'              => $arrayRow['FORMA_PAGO'],
                                           'strDescripcionBanco'       => $arrayRow['DESCRIPCION_BANCO'],
                                           'strDescripcionCuenta'      => $arrayRow['DESCRIPCION_CUENTA'],
                                           'strEstadoContrato'         => $arrayRow['ESTADO_CONTRATO'], 
                                           'fltCostoInstalacion'       => $arrayRow['COSTO_INSTALACION'],
                                           'strCortesia'               => $arrayRow['CORTESIA'],
                                           'strNumeroFactura'          => $arrayRow['NUMERO_FACTURA'],
                                           'strEstadoFactura'          => $arrayRow['ESTADO_FACTURA'],
                                           'strNumeroPago'             => $arrayRow['NUMERO_PAGO'],
                                           'strFeCreacionPago'         => $arrayRow['FE_CREACION_PAGO'],
                                           'strObservacionPago'        => $arrayRow['OBSERVACION_PAGO'],
                                           'strMotivoRechazo'          => $arrayRow['MOTIVO_RECHAZO'],
                                           'strUltimaMilla'            => $arrayRow['ULTIMA_MILLA'],
                                           'strSegmento'               => $arrayRow['SEGMENTO'],
                                           'strTipoContrato'           => $arrayRow['TIPO_CONTRATO'],
                                           'strPlanProducto'           => $arrayRow['PLAN_PRODUCTO'],
                                         );
                } 
            }
            
            $arrayResultado = array('total' => $intTotal, 'clientes' => $arrayDatos);
           
        }
        catch (\Exception $e) 
        {            
            throw($e);
        }

        
        return $arrayResultado;
    }

    /**
    * Documentación para el método 'generarRptAprobContratos'.
    *
    * Ejecuta la generación y envío de Reporte de Aprobación de Contratos según los parámetros indicados.
    *
    * @param mixed $arrayParametros[
    *                               'strIdsPtoCobertura'             => ids para filtrar por punto de cobertura
    *                               'strFechaPrePlanificacionDesde'  => rango inicial para fecha de pre-planificación
    *                               'strFechaPrePlanificacionHasta'  => rango final para fecha de pre-planificación
    *                               'strEmpresaId'                   => id empresa en sesion 
    *                               'strPrefijoEmpresa'              => prefijo de la empresa en sesion
    *                               'strUsrSesion'                   => usuario en sesion
    *                               ]
    *
    * @return strResultado  Resultado de generación del reporte.
    * @author Anabelle Peñaherrera <apenaherreratelconet.ec>
    * @version 1.0 11-07-2017
    */
    public function generarRptAprobContratos($arrayParametros)
    {
        $strResultado = "";
        try
        {
            if($arrayParametros && count($arrayParametros)>0)
            {
                $strSql = "BEGIN
                            DB_COMERCIAL.CMKG_REPORTE_APROB_CONTRATOS.P_REPORTE_APROBACION_CONTRATOS
                            (
                                :Pn_EmpresaId,
                                :Pv_PrefijoEmpresa,
                                :Pv_UsrSesion,
                                :Pv_FechaPrePlanificacionDesde,
                                :Pv_FechaPrePlanificacionHasta,
                                :Pv_IdsPtoCobertura,
                                :Pv_MsjResultado
                            );
                        END;";

                $objStmt = $this->_em->getConnection()->prepare($strSql);
               
                $strResultado = str_pad($strResultado, 5000, " ");
                $objStmt->bindParam('Pn_EmpresaId', $arrayParametros['strEmpresaId']);
                $objStmt->bindParam('Pv_PrefijoEmpresa', $arrayParametros['strPrefijoEmpresa']);
                $objStmt->bindParam('Pv_UsrSesion', $arrayParametros['strUsrSesion']);
                $objStmt->bindParam('Pv_FechaPrePlanificacionDesde', $arrayParametros['strFechaPrePlanificacionDesde']);
                $objStmt->bindParam('Pv_FechaPrePlanificacionHasta', $arrayParametros['strFechaPrePlanificacionHasta']);
                $objStmt->bindParam('Pv_IdsPtoCobertura', $arrayParametros['strIdsPtoCobertura']); 
                $objStmt->bindParam('Pv_MsjResultado', $strResultado);

                $objStmt->execute();
            }
            else
            {
                $strResultado= 'No se enviaron parámetros para generar el reporte.';
            }

        }
        catch (\Exception $e)
        {
            $strResultado= 'No se enviaron parámetros para generar el reporte.';
            throw($e);
        }
        
        return $strResultado; 
    }
    
    /**
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * Determina la validez de una identificación panameña RUC o CED
     * @version 1.0
     * @param string $strIdentificacionCliente
     *        string $strTipoIdentificacion
     * @return string mensaje de error, null en caso contrario
     */
    public function validarFormatoPanama($strIdentificacionCliente, $strTipoIdentificacion)
    {
        $strMensaje = str_repeat(' ', 100); // se debe enviar un string con suficiente espacio para la respuesta
        $strSql     = 'BEGIN VALIDA_IDENTIFICACION.P_VALIDA_FORMATO_PANAMA(:Pv_identificacion, :Pv_tipo_identificacion, :Pv_mensaje); END;';
        $objStmt    = $this->_em->getConnection()->prepare($strSql);
        $objStmt->bindParam('Pv_identificacion', $strIdentificacionCliente);
        $objStmt->bindParam('Pv_tipo_identificacion', $strTipoIdentificacion);
        $objStmt->bindParam('Pv_mensaje', $strMensaje);
        $objStmt->execute();
        $strMensaje = trim($strMensaje); // si no se modifico el string, quitar los espacios en blanco
        if($strMensaje == 'OK')
        {
            $strMensaje = '';
        }
        return $strMensaje;
    }
    
    /**
     * @author Edgar Holguín <eholguin@telconet.ec>
     * Determina la validez de una identificación para Telconet Guatemala NIT o DPI
     * @version 1.0 18-03-2019
     * @param  string $strIdentificacionCliente
     *         string $strTipoIdentificacion
     * @return string $strMensaje mensaje de error, null en caso contrario
     */
    public function validarFormatoGuatemala($arrayParametros)
    {
        $strMensaje = str_repeat(' ', 100);
 
        if($arrayParametros['strTipoIdentificacion'] !== 'PAS')
        {
            if($arrayParametros['strTipoIdentificacion'] === 'NIT')
            {
                $strSql = 'BEGIN VALIDA_IDENTIFICACION.P_VALIDA_NIT(:Pv_Identificacion, :Pv_Mensaje); END;';   
            }
            else
            {
                $strSql = 'BEGIN VALIDA_IDENTIFICACION.P_VALIDA_DPI(:Pv_Identificacion, :Pv_Mensaje); END;';    
            }

            $objStmt = $this->_em->getConnection()->prepare($strSql);
            $objStmt->bindParam('Pv_Identificacion', $arrayParametros['strIdentificacionCliente']);
            $objStmt->bindParam('Pv_Mensaje', $strMensaje);
            $objStmt->execute();
        }
              
        $strMensaje = trim($strMensaje);
        
        if($strMensaje === 'OK')
        {
            $strMensaje = '';
        }
        
        return $strMensaje;
    }    
    
     /**
     * Funcion que obtiene todas las tareas asignadas a una persona
     * 
     * @author Creado: John Vera <javera@telconet.ec>
     * @version 1.0 15-01-2018
     */    
    
    public function getTareasPorPersona($arrayParametros)
    {   
        //Obtiene la conexion
        $objCon = oci_connect($arrayParametros['user'], $arrayParametros['pass'], $arrayParametros['db']) or $this->throw_exceptionOci(oci_error());
        
        $strSql = "BEGIN  :strCasos := SPKG_GESTION_TAREAS_TYM.COMF_GET_TAREAS_POR_PERSONA(:intPersona); END; ";

        //Prepara la sentencia 
        $objStmt = oci_parse($objCon, $strSql) or $this->throw_exceptionOci(oci_error());

        //Declaro variable tipo CLOB
        $clobResult = oci_new_descriptor($objCon, OCI_D_LOB);       
        
        oci_bind_by_name($objStmt, ':intPersona', $arrayParametros['intPersona']) ;
        oci_bind_by_name($objStmt, ':strCasos', $clobResult, -1, OCI_B_CLOB) ;

        oci_execute($objStmt);
       
        if($clobResult)
        {
            return html_entity_decode($clobResult->load());
        }
        else
        {
            return '';
        }
    }


    /**
     * Función que sirve para obtener la información de los empleados para uso de aplicativos móviles
     * @author   Wilmer Vera <wvera@telconet.ec>
     * @version  1.0 21-12-2017
     *
     * @param $strUser
     *
     * @return Object
     * @throws \Exception
     *
     * Se quita el filtro del estado ID_PERSONA
     * @author Wilmer Vera <wvera@telconet.ec>
     * @version 1.1 06-08-2018
     * 
     * @author Jean Nazareno <jnazareno@telconet.ec>
     * @version 1.2 - 12-07-2019
     * Se modifica método para obtener el nombre del departamento de la persona.
     * @since 1.1
     * Costo (10)
     *
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.3 - 11-11-2019 Se modifica método para recibir el rol por parámetro si no recibe parámetro se asigna 1  
     * @param  $strUser
     *
     */
    public function getArrayInfoUsuarioByLogin($strUser, $intRol = 1)
    {
        try
        {
                $objRsm = new ResultSetMappingBuilder($this->_em);
                $objQuery = $this->_em->createNativeQuery(null, $objRsm );

                $strSql =
                    "SELECT EG.NOMBRE_EMPRESA,
                         EG.COD_EMPRESA,
                         EG.PREFIJO,
                         PER.ID_PERSONA_ROL, 
                         PER.EMPRESA_ROL_ID, 
                         PER.OFICINA_ID, 
                         PER.DEPARTAMENTO_ID, 
                         PER.ESTADO,PER.CUADRILLA_ID,
                         AD.NOMBRE_DEPARTAMENTO
                     FROM INFO_PERSONA P,
                         DB_GENERAL.INFO_PERSONA_EMPRESA_ROL PER,
                         DB_SEGURIDAD.INFO_EMPRESA_ROL ER,
                         DB_GENERAL.ADMI_ROL R, 
                         DB_GENERAL.ADMI_TIPO_ROL TR,
                         DB_SEGURIDAD.INFO_EMPRESA_GRUPO EG,
                         DB_GENERAL.ADMI_DEPARTAMENTO AD
                     WHERE P.ID_PERSONA         = PER.PERSONA_ID
                         AND R.ID_ROL           = ER.ROL_ID
                         AND R.TIPO_ROL_ID      = TR.ID_TIPO_ROL
                         AND TR.ID_TIPO_ROL     = :idTipoRol
                         AND ER.ID_EMPRESA_ROL  = PER.EMPRESA_ROL_ID
                         AND ER.EMPRESA_COD     = EG.COD_EMPRESA
                         AND AD.ID_DEPARTAMENTO = PER.DEPARTAMENTO_ID
                         AND P.LOGIN            = :strUser
                         AND PER.ESTADO         = :strEstado";
                $objQuery->setParameter('strUser', $strUser);
                $objQuery->setParameter('strEstado',"Activo");
                $objQuery->setParameter('idTipoRol', $intRol);


                $objRsm->addScalarResult('NOMBRE_EMPRESA','nombre_empresa','string');
                $objRsm->addScalarResult('COD_EMPRESA','cod_empresa','integer');
                $objRsm->addScalarResult('PREFIJO','prefijo','string');
                $objRsm->addScalarResult('ID_PERSONA_ROL','id_persona_rol','integer');
                $objRsm->addScalarResult('EMPRESA_ROL_ID','empresa_rol_id','integer');
                $objRsm->addScalarResult('OFICINA_ID','oficina_id','integer');
                $objRsm->addScalarResult('DEPARTAMENTO_ID','departamento_id','integer');
                $objRsm->addScalarResult('ESTADO','estado','string');
                $objRsm->addScalarResult('CUADRILLA_ID','cuadrilla_id','integer');
                $objRsm->addScalarResult('NOMBRE_DEPARTAMENTO','nombreDepartamento','string');

            $objQuery->setSQL($strSql);
            return $objQuery->getResult();
        }
        catch (Exception $ex)
        {
            error_log($ex->getMessage());
            throw $ex;
        }
    }

    /**
     * Funcion que sirve para validar permisos de un usuario que desee loguearse.
     * @author Wilmer Vera <wvera@telconet.ec>
     * @version 1.0 21-12-2017
     * @param Array $arrayParametros [strUserLogin]
     * @throws \Exception
     * @return Bolean
     */
    public function tienePerfilMovilOperaciones($strUser)
    {
        try
        {
            $objRsm = new ResultSetMappingBuilder($this->_em);
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);

            $strSql =
                "SELECT COUNT(P.ID_PERFIL) USUARIOS
                    FROM DB_SEGURIDAD.VISTA_PERFIL_PERSONA PP,
                      DB_SEGURIDAD.SIST_PERFIL P,
                      DB_COMERCIAL.INFO_PERSONA IP
                    WHERE P.ID_PERFIL = PP.PERFIL_ID
                    AND P.NOMBRE_PERFIL = 'MOBILE OPERATIVOS'
                    AND persona_id    = IP.ID_PERSONA 
                    AND IP.LOGIN = :strUser
                    ";
            $objQuery->setParameter('strUser', $strUser);
            $objQuery->setSQL($strSql);
            /* determinar el número de filas del resultado */
            $row_cnt = $objQuery->getResult();
            if($row_cnt>0){
                return true;
            }else{
                return false;
            }
        }
        catch (\Exception $ex)
        {
            error_log($ex->getMessage());
            throw $ex;
        }
    }

    /**
     * Funcion retorna la informacion de la persona por medio del usuario de login.
     * @author Wilmer Vera <wvera@telconet.ec>
     * @version 1.0 26-12-2017
     * @param String $arrayParametros [strUserLogin]
     * @throws \Exception
     * @return Array información personal del empleado.
     */
    public function getResultadoInfoEmpleado($strUser)
    {
        try {
            $objRsmInfoEmpleado = new ResultSetMappingBuilder($this->_em);
            $objQuery = $this->_em->createNativeQuery(null, $objRsmInfoEmpleado);

            $strSql =
                "Select IP.ID_PERSONA,ATI.DESCRIPCION_TITULO, IP.TITULO_ID, IP.TIPO_IDENTIFICACION,
                        IP.IDENTIFICACION_CLIENTE, IP.NOMBRES, IP.APELLIDOS, IP.DIRECCION, 
                        IP.LOGIN, IP.CARGO, IP.DIRECCION_TRIBUTARIA, IP.GENERO, IP.ESTADO 
                        FROM DB_COMERCIAL.INFO_PERSONA IP ,DB_COMERCIAL.ADMI_TITULO ATI 
                        WHERE IP.LOGIN = :strUser 
                         AND IP.TITULO_ID = ATI.ID_TITULO";
            $objQuery->setParameter('strUser', $strUser);
            $objQuery->setSQL($strSql);
            $objRsmInfoEmpleado->addScalarResult('ID_PERSONA','persona_id','integer');
            $objRsmInfoEmpleado->addScalarResult('DESCRIPCION_TITULO', 'descripcion_titulo', 'string');
            $objRsmInfoEmpleado->addScalarResult('TIPO_IDENTIFICACION', 'tipo_identificacion', 'integer');
            $objRsmInfoEmpleado->addScalarResult('IDENTIFICACION_CLIENTE', 'identificacion', 'string');
            $objRsmInfoEmpleado->addScalarResult('NOMBRES', 'nombres', 'string');
            $objRsmInfoEmpleado->addScalarResult('APELLIDOS', 'apellidos', 'string');
            $objRsmInfoEmpleado->addScalarResult('DIRECCION', 'direccion', 'string');
            $objRsmInfoEmpleado->addScalarResult('LOGIN', 'usuario_login', 'string');
            $objRsmInfoEmpleado->addScalarResult('CARGO', 'cargo', 'string');
            $objRsmInfoEmpleado->addScalarResult('DIRECCION_TRIBUTARIA', 'direccion_tributaria', 'string');
            $objRsmInfoEmpleado->addScalarResult('GENERO', 'genero', 'string');
            $objRsmInfoEmpleado->addScalarResult('ESTADO', 'estado', 'string');
            return $objQuery->getOneOrNullResult();
        } catch (\Exception $ex) {
            error_log($ex->getMessage());
            throw $ex;
        }
    }



    /**
     * getJsonMaxLongitudIdentificacion
     *
     * Función que retorna la longitud máxima del campo identificación en formato JSON                               
     *
     * @param  $arrayParametros  Array de parámetros (strNombrePais, strTipoIdentificacion)
     * @return json $jsonData  Respuesta en formato json.
     *
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.0 06-02-2018
     */
    public function getJsonMaxLongitudIdentificacion($arrayParametros)
    {   
        $intMaxLongitudIdentificacion = 0;
        
        $objAdmiParametroCab       = $this->_em->getRepository('schemaBundle:AdmiParametroCab')
                                               ->findOneBy( array('nombreParametro' => 'MAX_IDENTIFICACION', 
                                                                  'estado'          => 'Activo') );

        if(is_object($objAdmiParametroCab))
        {
               
            $objParametroDet = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                                         ->findOneBy( array( 'estado'      => 'Activo',
                                                             'parametroId' => $objAdmiParametroCab->getId(),
                                                             'valor1'      => $arrayParametros['strTipoIdentificacion'],
                                                             'valor2'      => $arrayParametros['strNombrePais']) );
            if(is_object($objParametroDet))
            {
                $intMaxLongitudIdentificacion  = $objParametroDet->getValor3();
            }   
              
            $arrayRespuesta['intMaxLongitudIdentificacion'] = (int) $intMaxLongitudIdentificacion;
            
        }

        $strJsonData = json_encode($arrayRespuesta);

        return $strJsonData;
    }
     /**
     * Determina la validez de una identificacion segun su tipo
     * @param  string $strLogin
     * @param  string $strIdentificacionCliente
     * @return string $strMensaje
     *
     * @author Sofía Fernandez <sfernandez@telconet.ec>
     * @version 1.0 09-03-2018
     */
    public function validarInfoPersonaByIdentificacionYLogin($strLogin, $strIdentificacionCliente)
    {
        $strMensaje = null;
        $strSql     = 'BEGIN DB_COMERCIAL.COMEK_CONSULTAS.P_GET_PERSONA_X_LOGIN_ID(:strLogin, :strIdentificacionCliente, :strMensaje); END;';
        try
        {
            $strStmt    = $this->_em->getConnection()->prepare($strSql);
            $strStmt->bindParam('strLogin', $strLogin);
            $strStmt->bindParam('strIdentificacionCliente', $strIdentificacionCliente);
            $strStmt->bindParam('strMensaje', $strMensaje);
            $strStmt->execute();
        } catch (\Exception $ex) {
            error_log("InfoPersonaRepository->validarInfoPersonaByIdentificacionYLogin " . $ex->getMessage());
        }
        return trim($strMensaje);
    }

    /*
    * Documentacion para la funcion getJsonClientesACambiarCicloFact
    *
    * Metodo que obtiene Listado de clientes a los cuales se realizara Cambio de Ciclo de Facturación
    * Consulta se realiza en base a filtros de Busqueda enviados por parametros.
    * 
    * @param mixed $arrayParametros[
    *                               'strIdentificacion'      => Numero de Identificación del Cliente
    *                               'strCliente'             => Nombre o Razon Social del Cliente 
    *                               'intIdCicloFacturacion'  => id del Ciclo de Facturación
    *                               'strIdsEstadoServicio'   => string con Id de Estados de Servicios definidos en Parameters
    *                               'strIdsPtoCobertura'     => ids para filtrar por punto de cobertura
    *                               'intIdFormaPago'         => id de la forma de Pago
    *                               'strEsTarjeta'           => Identifica si es Tarjeta S/N
    *                               'strIdsTipoCuenta'       => ids para filtrar por Tipo de Cuenta 
    *                               'strIdsBancos'           => ids para filtrar por Bancos 
    *                               'strEmpresaId'           => id empresa en sesion 
    *                               'strPrefijoEmpresa'      => prefijo de la empresa en sesion
    *                               'strUsrSesion'           => usuario en sesion
    *                               'intStart'               => limite de rango de inicio para realizar consulta
    *                               'intLimit'               => limite de rango final para realizar consulta
    *                               'objOciCon'              => conexion  oci_connect
    *                               'objCursor'              => cursor  oci_new_cursor
    *                               ]
    *
    * @return json   $objJsonData
    *
    * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
    * @version 1.0 15-09-2017
    *
    */

    public function getJsonClientesACambiarCicloFact($arrayParametros)
    {
        $intTotal      = 0;
        $arrayDatos    = array();
        try
        {             
            if($arrayParametros && count($arrayParametros)>0)
            {

                $objCursor  = $arrayParametros['objCursor'];

                $strSql = "BEGIN
                            DB_COMERCIAL.CMKG_CICLOS_FACTURACION.P_GET_CLIENTES_CAMBIO_CICLO
                            (
                                :Pv_Identificacion,
                                :Pv_NombreCliente,
                                :Pn_IdCicloFacturacion,
                                :Pv_IdsEstadoServicio,
                                :Pv_IdsPtoCobertura,
                                :Pn_IdFormaPago,
                                :Pv_EsTarjeta,
                                :Pv_IdsTipoCuenta,
                                :Pv_IdsBancos,
                                :Pv_CodEmpresa,
                                :Pv_PrefijoEmpresa,
                                :Pv_UsrSesion,                                                         
                                :Pn_Start,
                                :Pn_Limit,
                                :Pn_TotalRegistros,
                                :Pc_Registros
                            );
                        END;";

                $objStmt = oci_parse($arrayParametros['objOciCon'], $strSql);
                
                oci_bind_by_name($objStmt, ":Pv_Identificacion", $arrayParametros['strIdentificacion']);
                oci_bind_by_name($objStmt, ":Pv_NombreCliente", $arrayParametros['strCliente']);
                oci_bind_by_name($objStmt, ":Pn_IdCicloFacturacion", $arrayParametros['intIdCicloFacturacion']);
                oci_bind_by_name($objStmt, ":Pv_IdsEstadoServicio", $arrayParametros['strIdsEstadoServicio']);
                oci_bind_by_name($objStmt, ":Pv_IdsPtoCobertura", $arrayParametros['strIdsPtoCobertura']);
                oci_bind_by_name($objStmt, ":Pn_IdFormaPago", $arrayParametros['intIdFormaPago']);
                oci_bind_by_name($objStmt, ":Pv_EsTarjeta", $arrayParametros['strEsTarjeta']);
                oci_bind_by_name($objStmt, ":Pv_IdsTipoCuenta", $arrayParametros['strIdsTipoCuenta']);
                oci_bind_by_name($objStmt, ":Pv_IdsBancos", $arrayParametros['strIdsBancos']);                                                
                oci_bind_by_name($objStmt, ":Pv_CodEmpresa", $arrayParametros['strEmpresaId']);
                oci_bind_by_name($objStmt, ":Pv_PrefijoEmpresa", $arrayParametros['strPrefijoEmpresa']);
                oci_bind_by_name($objStmt, ":Pv_UsrSesion", $arrayParametros['strUsrSesion']);
                oci_bind_by_name($objStmt, ":Pn_Start", $arrayParametros['intStart']);
                oci_bind_by_name($objStmt, ":Pn_Limit", $arrayParametros['intLimit']); 
                oci_bind_by_name($objStmt, ":Pn_TotalRegistros", $intTotal, 10);
                oci_bind_by_name($objStmt, ":Pc_Registros", $objCursor, -1, OCI_B_CURSOR);

                oci_execute($objStmt); 
                oci_execute($objCursor, OCI_DEFAULT);

                while (($arrayRow = oci_fetch_array($objCursor)))
                {                    
                    $arrayDatos[] = array(
                                           'intIdServicio'            => $arrayRow['ID_SERVICIO'],
                                           'intIdPersonaRol'          => $arrayRow['ID_PERSONA_ROL'],
                                           'strIdentificacion'        => $arrayRow['IDENTIFICACION'],
                                           'strNombreCliente'         => $arrayRow['NOMBRE_CLIENTE'],
                                           'strLogin'                 => $arrayRow['LOGIN_PUNTO'],                        
                                           'strFormaPago'             => $arrayRow['FORMA_PAGO'],
                                           'strDescripcionCuenta'     => $arrayRow['DESCRIPCION_CUENTA'],
                                           'strDescripcionBanco'      => $arrayRow['DESCRIPCION_BANCO'],                                           
                                           'fltValorRecurrente'       => $arrayRow['VALOR_RECURRENTE'],
                                           'fltSaldoDeudor'           => $arrayRow['SALDO_DEUDOR'],
                                           'strNombreCiclo'           => $arrayRow['NOMBRE_CICLO'],
                                           'strEstadoServ'            => $arrayRow['ESTADO_SERV'],
                                           'strJurisdiccion'          => $arrayRow['PTO_COBERTURA'],     
                                           'strPlanProducto'          => $arrayRow['PLAN_PRODUCTO'],
                                         );
                } 
            }
            
            $arrayResultado = array('total' => $intTotal, 'clientes' => $arrayDatos);
           
        }
        catch (\Exception $e) 
        {            
            throw($e);
        }

        
        return $arrayResultado;
    }
    
    /**
    * Documentación para el método 'getAsignarCicloFacturacion'.
    *
    * Metodo que genera un Proceso Masivo de Cambio de Ciclo de Facturacion, en base a parametros enviados.
    * El metodo incluira en el PMA de Cambio de Ciclo a todos los Clientes que hayan sido previamente escogidos o
    * marcado en el proceso , asignando el nuevo Ciclo escogido.
    * @param mixed $arrayParametros[
    *                               'strIdsPersonaRol'            => ids de Clientes a los cuales se realizara el cambio de ciclo.
    *                               'intIdCicloFacturacionNuevo'  => id del Nuevo Ciclo de Facturación
    *                               'strIdsPtoCobertura'          => ids puntos de cobertura
    *                               'strEmpresaId'                => id empresa en sesion 
    *                               'strPrefijoEmpresa'           => prefijo de la empresa en sesion
    *                               'strUsrSesion'                => usuario en sesion
    *                               ]
    *
    * @return strResultado  Resultado de la ejecución.
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @version 1.0 22-09-2017
    */
    public function getAsignarCicloFacturacion($arrayParametros)
    {
        $strResultado = "";
        try
        {
            if($arrayParametros && count($arrayParametros)>0)
            {
                $strSql = "BEGIN
                            DB_COMERCIAL.CMKG_CICLOS_FACTURACION.P_CREA_PM_CAMBIO_CICLO
                            (
                                :Pv_IdsPersonaRol,
                                :Pn_IdCicloFacturacionNuevo,
                                :Pv_IdsPtoCobertura,
                                :Pv_CodEmpresa,
                                :Pv_PrefijoEmpresa,
                                :Pv_UsrSesion,
                                :Pv_MsjResultado
                            );
                        END;";

                $objStmt = $this->_em->getConnection()->prepare($strSql);
               
                $strResultado = str_pad($strResultado, 5000, " ");
                $objStmt->bindParam('Pv_IdsPersonaRol', $arrayParametros['strIdsPersonaRol']);
                $objStmt->bindParam('Pn_IdCicloFacturacionNuevo', $arrayParametros['intIdCicloFacturacionNuevo']);
                $objStmt->bindParam('Pv_IdsPtoCobertura', $arrayParametros['strIdsPtoCobertura']);
                $objStmt->bindParam('Pv_CodEmpresa', $arrayParametros['strEmpresaId']);
                $objStmt->bindParam('Pv_PrefijoEmpresa', $arrayParametros['strPrefijoEmpresa']);
                $objStmt->bindParam('Pv_UsrSesion', $arrayParametros['strUsrSesion']);                
                $objStmt->bindParam('Pv_MsjResultado', $strResultado);

                $objStmt->execute();
            }
            else
            {
                $strResultado= 'No se enviaron parámetros para generar el cambio de ciclo.';
            }

        }
        catch (\Exception $e)
        {
            $strResultado= 'No se enviaron parámetros para generar el cambio de ciclo.';
            throw($e);
        }
        
        return $strResultado; 
    }
    
    /**
    * Documentación para el método 'getAsignarCicloFactTodos'.
    *
    * Metodo que genera un Proceso Masivo de Cambio de Ciclo de Facturacion, en base a parametros enviados.
    * El metodo incluira en el PMA de Cambio de Ciclo a todos los Clientes que esten incluidos en los criterios 
    * o filtros seleccionados por pantalla, asignando el nuevo Ciclo escogido.
    * @param mixed $arrayParametros[
    *                               'intIdCicloFacturacionNuevo'  => id del Nuevo Ciclo de Facturación
    *                               'strIdentificacion'           => identificacion del cliente
    *                               'strCliente'                  => nombre o Razón social del cliente
    *                               'intIdCicloFacturacion'       => id del ciclo de Facturacion actual
    *                               'strIdsEstadoServicio'        => Id del estado del servicio parametrizado
    *                               'strIdsPtoCobertura'          => ids puntos de cobertura
    *                               'intIdFormaPago'              => Id de la forma de Pago
    *                               'strEsTarjeta'                => Identifica si es Tarjeta S/N 
    *                               'strIdsTipoCuenta'            => ids para filtrar por Tipo de Cuenta 
    *                               'strIdsBancos'                => ids para filtrar por Bancos          
    *                               'strEmpresaId'                => id empresa en sesion 
    *                               'strPrefijoEmpresa'           => prefijo de la empresa en sesion
    *                               'strUsrSesion'                => usuario en sesion
    *                               ]
    *
    * @return strResultado  Resultado de la ejecución.
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @version 1.0 13-10-2017
    */
    public function getAsignarCicloFactTodos($arrayParametros)
    {
        $strResultado = "";
        try
        {
            if($arrayParametros && count($arrayParametros)>0)
            {
                $strSql = "BEGIN
                            DB_COMERCIAL.CMKG_CICLOS_FACTURACION.P_CREA_PM_CMB_CICLO_TODOS
                            (                                
                                :Pn_IdCicloFacturacionNuevo,
                                :Pv_Identificacion,
                                :Pv_NombreCliente,
                                :Pn_IdCicloFacturacion,
                                :Pv_IdsEstadoServicio,
                                :Pv_IdsPtoCobertura,
                                :Pn_IdFormaPago,
                                :Pv_EsTarjeta,
                                :Pv_IdsTipoCuenta,
                                :Pv_IdsBancos,
                                :Pv_CodEmpresa,
                                :Pv_PrefijoEmpresa,
                                :Pv_UsrSesion,
                                :Pv_MsjResultado
                            );
                        END;";

                $objStmt = $this->_em->getConnection()->prepare($strSql);
               
                $strResultado = str_pad($strResultado, 5000, " ");                
                $objStmt->bindParam('Pn_IdCicloFacturacionNuevo', $arrayParametros['intIdCicloFacturacionNuevo']);
                $objStmt->bindParam('Pv_Identificacion', $arrayParametros['strIdentificacion']);
                $objStmt->bindParam('Pv_NombreCliente', $arrayParametros['strCliente']);
                $objStmt->bindParam('Pn_IdCicloFacturacion', $arrayParametros['intIdCicloFacturacion']);
                $objStmt->bindParam('Pv_IdsEstadoServicio', $arrayParametros['strIdsEstadoServicio']);
                $objStmt->bindParam('Pv_IdsPtoCobertura', $arrayParametros['strIdsPtoCobertura']);
                $objStmt->bindParam('Pn_IdFormaPago', $arrayParametros['intIdFormaPago']);
                $objStmt->bindParam('Pv_EsTarjeta', $arrayParametros['strEsTarjeta']);
                $objStmt->bindParam('Pv_IdsTipoCuenta', $arrayParametros['strIdsTipoCuenta']);
                $objStmt->bindParam('Pv_IdsBancos', $arrayParametros['strIdsBancos']);
                $objStmt->bindParam('Pv_CodEmpresa', $arrayParametros['strEmpresaId']);
                $objStmt->bindParam('Pv_PrefijoEmpresa', $arrayParametros['strPrefijoEmpresa']);
                $objStmt->bindParam('Pv_UsrSesion', $arrayParametros['strUsrSesion']);
                $objStmt->bindParam('Pv_MsjResultado', $strResultado);

                $objStmt->execute();
            }
            else
            {
                $strResultado= 'No se enviaron parámetros para generar el cambio de ciclo.';
            }

        }
        catch (\Exception $e)
        {
            $strResultado= 'No se enviaron parámetros para generar el cambio de ciclo.';
            throw($e);
        }
        
        return $strResultado; 
    }
    
    /**
    * Documentacion para la funcion generarRptCambioCiclo
    *
    * Documentación para el método 'generarRptCambioCiclo'
    * Metodo que obtiene reporte de clientes a los cuales se realizara Cambio de Ciclo de Facturación
    * La consulta se realiza en base a los filtros de busqueda enviados por parametros, genera CSV de la informacion 
    * que será enviado por correo.
    * @param mixed $arrayParametros[
    *                               'strIdentificacion'      => Numero de Identificación del Cliente
    *                               'strCliente'             => Nombre o Razon Social del Cliente 
    *                               'intIdCicloFacturacion'  => id del Ciclo de Facturación
    *                               'strIdsEstadoServicio'   => string con Id de Estados de Servicios definidos en Parameters
    *                               'strIdsPtoCobertura'     => ids para filtrar por punto de cobertura
    *                               'intIdFormaPago'         => id de la forma de Pago
    *                               'strEsTarjeta'           => Identifica si es Tarjeta S/N
    *                               'strIdsTipoCuenta'       => ids para filtrar por Tipo de Cuenta 
    *                               'strIdsBancos'           => ids para filtrar por Bancos 
    *                               'strEmpresaId'           => id empresa en sesion 
    *                               'strPrefijoEmpresa'      => prefijo de la empresa en sesion
    *                               'strUsrSesion'           => usuario en sesion   
    *                               ]
    *
    * @return json   $objJsonData
    *
    * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
    * @version 1.0 17-10-2017
    *
    */

    public function generarRptCambioCiclo($arrayParametros)
    {
        $strResultado = "";
        try
        {             
            if($arrayParametros && count($arrayParametros)>0)
            {                
                $strSql = "BEGIN
                            DB_COMERCIAL.CMKG_CICLOS_FACTURACION.P_GENERAR_RPT_CAMBIO_CICLO
                            (
                                :Pv_Identificacion,
                                :Pv_NombreCliente,
                                :Pn_IdCicloFacturacion,
                                :Pv_IdsEstadoServicio,
                                :Pv_IdsPtoCobertura,
                                :Pn_IdFormaPago,
                                :Pv_EsTarjeta,
                                :Pv_IdsTipoCuenta,
                                :Pv_IdsBancos,
                                :Pv_CodEmpresa,
                                :Pv_PrefijoEmpresa,
                                :Pv_UsrSesion, 
                                :Pv_MsjResultado
                            );
                        END;";

                $objStmt = $this->_em->getConnection()->prepare($strSql);
               
                $strResultado = str_pad($strResultado, 5000, " ");                                
                $objStmt->bindParam('Pv_Identificacion', $arrayParametros['strIdentificacion']);
                $objStmt->bindParam('Pv_NombreCliente', $arrayParametros['strCliente']);
                $objStmt->bindParam('Pn_IdCicloFacturacion', $arrayParametros['intIdCicloFacturacion']);
                $objStmt->bindParam('Pv_IdsEstadoServicio', $arrayParametros['strIdsEstadoServicio']);
                $objStmt->bindParam('Pv_IdsPtoCobertura', $arrayParametros['strIdsPtoCobertura']);
                $objStmt->bindParam('Pn_IdFormaPago', $arrayParametros['intIdFormaPago']);
                $objStmt->bindParam('Pv_EsTarjeta', $arrayParametros['strEsTarjeta']);
                $objStmt->bindParam('Pv_IdsTipoCuenta', $arrayParametros['strIdsTipoCuenta']);
                $objStmt->bindParam('Pv_IdsBancos', $arrayParametros['strIdsBancos']);
                $objStmt->bindParam('Pv_CodEmpresa', $arrayParametros['strEmpresaId']);
                $objStmt->bindParam('Pv_PrefijoEmpresa', $arrayParametros['strPrefijoEmpresa']);
                $objStmt->bindParam('Pv_UsrSesion', $arrayParametros['strUsrSesion']);
                $objStmt->bindParam('Pv_MsjResultado', $strResultado);

                $objStmt->execute();
            }
            else
            {
                $strResultado= 'No se enviaron parámetros para generar Reporte de cambio de ciclo.';
            }
           
        }
        catch (\Exception $e) 
        {            
            $strResultado= 'No se enviaron parámetros para generar Reporte de cambio de ciclo.';
            throw($e);
        }        
        return $strResultado;
    }
    
   /**
    * Documentacion para la funcion getContactosByIdPersonaAndFormaContacto
    *
    * Metodo que obtiene reporte de clientes a los cuales se realizara Cambio de Ciclo de Facturación
    * La consulta se realiza en base a los filtros de busqueda enviados por parametros, genera CSV de la informacion 
    * que será enviado por correo.

    * @return object   $objFormasContacto
    *
    * @author Edgar Pin Villavicencio <epin@telconet.ec>
    * @version 1.0 16-05-2018
    *
    */   

    public function getContactosByIdPersonaAndFormaContacto($arrayParametros)
    {
        $intIdPersona     = $arrayParametros['intIdPersona'];
        $strFormaContacto = $arrayParametros['strFormaContacto'];
        $strWhereFormaContacto = "";
        if($strFormaContacto)
        {
            $strWhereFormaContacto = " AND	lower(afc.descripcionFormaContacto) = lower('$strFormaContacto')";        
        }          
			
        $strSql = "SELECT afc.descripcionFormaContacto, pfc.valor
                   FROM 
                    schemaBundle:InfoPersona p, 
                    schemaBundle:InfoPersonaFormaContacto pfc,
                    schemaBundle:AdmiFormaContacto afc                 
                   WHERE 
                        p.id = pfc.personaId
			AND afc.id = pfc.formaContactoId
			AND	p.id = '$intIdPersona'
			AND lower(pfc.estado) = lower('Activo')	
			AND pfc.valor is not null 
			$strWhereFormaContacto ";

            $objQuery = $this->_em->createQuery($strSql);			
            $objFormasContacto =  $objQuery->getResult();
        return $objFormasContacto;
    }
    /**
     * 
     * Metodo encargado de retornar si el login del vendedor pertenece al: asistente, vendedor, subgerente
     * 
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.0 14-12-2018
     * Costo query: 6
     * 
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.1 29-11-2020 Se modifica el query para no filtrar por el login del vendedor.
     * 
     * @param  array $arrayParametros[
     *                                 strPrefijoEmpresa     Prefijo de la empresa
     *                                 strTipoPersonal       Cargo de la persona: asistente,vendedor,subgerente
     *                                 intIdPersonEmpresaRol idPersonEmpresaRol en sesion
     *                                 strLoginVendedor      login del vendedor a buscar
     *                               ]
     * @return array  $arrayVendAsignado
     * 
     */    
    public function getVendAsignado($arrayParametros)
    {
        $intIdPersonEmpresaRol = $arrayParametros['intIdPersonEmpresaRol'] ? intval($arrayParametros['intIdPersonEmpresaRol']) : 0;
        $strTipo               = ( isset($arrayParametros['strTipoPersonal']) && !empty($arrayParametros['strTipoPersonal']) )
                                    ? $arrayParametros['strTipoPersonal'] : 'Otros';
        $strPrefijoEmpresa     = ( isset($arrayParametros['strPrefijoEmpresa']) && !empty($arrayParametros['strPrefijoEmpresa']) )
                                    ? $arrayParametros['strPrefijoEmpresa'] : '';
        $strLoginVendedor      = ( isset($arrayParametros['strLoginVendedor']) && !empty($arrayParametros['strLoginVendedor']) )
                                    ? $arrayParametros['strLoginVendedor'] : '';
        $boolHolding           = ( isset($arrayParametros['boolHolding']) && !empty($arrayParametros['boolHolding']) )
                                    ? $arrayParametros['boolHolding'] : 'false';
        
        $strEstadoActivo       = 'Activo';
        $strDescripcion        = 'ASISTENTE_POR_CARGO';
        $arrayVendAsignado     = array();
        $objRsmBuilder         = new ResultSetMappingBuilder($this->_em);
        $objQuery              = $this->_em->createNativeQuery(null, $objRsmBuilder);
        try
        {
            if( ($strPrefijoEmpresa == 'TN' && $strTipo !== 'Otros' ) && ( $strTipo !=='GERENTE_VENTAS' && !empty($intIdPersonEmpresaRol)) )
            {
                $strSelect ="SELECT IP.LOGIN ";
                $strFrom   = "FROM INFO_PERSONA IP
                                 JOIN INFO_PERSONA_EMPRESA_ROL IPER
                                  ON IPER.PERSONA_ID                       = IP.ID_PERSONA ";
                if( $strTipo == 'SUBGERENTE' )
                {
                    $strWhere = "WHERE IPER.ESTADO                           = :strEstadoActivo
                                    AND IP.ESTADO                            = :strEstadoActivo
                                    AND (IPER.REPORTA_PERSONA_EMPRESA_ROL_ID = :intIdPersonEmpresaRol
                                    OR IPER.ID_PERSONA_ROL                   = :intIdPersonEmpresaRol)";                      
                }
                elseif( $strTipo == 'VENDEDOR' )
                {
                    $strWhere = "WHERE iper.id_persona_rol   = :intIdPersonEmpresaRol
                                    AND IPER.ESTADO          = :strEstadoActivo
                                    AND IP.ESTADO            = :strEstadoActivo";
                }
                elseif( $strTipo == 'ASISTENTE' )
                {
                    $strFrom   = "FROM INFO_PERSONA_EMPRESA_ROL_CARAC IPERC
                                    JOIN DB_COMERCIAL.ADMI_CARACTERISTICA AC
                                        ON AC.ID_CARACTERISTICA        = IPERC.CARACTERISTICA_ID
                                    JOIN INFO_PERSONA IP
                                        ON IP.ID_PERSONA               = TO_NUMBER(IPERC.VALOR) ";
                    $strWhere  = " WHERE IPERC.PERSONA_EMPRESA_ROL_ID  = :intIdPersonEmpresaRol
                                    AND AC.DESCRIPCION_CARACTERISTICA  = :strDescripcion
                                    AND AC.ESTADO                      = :strEstadoActivo
                                    AND IPERC.ESTADO                   = :strEstadoActivo
                                    AND IP.ESTADO                      = :strEstadoActivo";
                    $objQuery->setParameter('strDescripcion' , $strDescripcion);
                }
                if(!$boolHolding)
                {
                    $strWhere .= "AND IP.LOGIN                         = :strLoginVendedor ";
                }
                $objRsmBuilder->addScalarResult('LOGIN', 'LOGIN', 'string');

                $objQuery->setParameter('strEstadoActivo' , $strEstadoActivo);
                $objQuery->setParameter('strLoginVendedor' , $strLoginVendedor);
                $objQuery->setParameter('intIdPersonEmpresaRol' , $intIdPersonEmpresaRol);

                $strSql  = $strSelect.$strFrom.$strWhere;
                $objQuery->setSQL($strSql);
                $arrayVendAsignado['resultados'] = $objQuery->getResult();
            }
        }
        catch(\Exception $e)
        {
            error_log('getVendAsignado -> '.$e->getMessage());
            throw($e);
        }
        return $arrayVendAsignado;
    }
    /**
     * 
     * Metodo encargado de retornar el cargo de la persona en sesion 
     * con la siguiente descripcion: ASISTENTE_POR_CARGO, CARGO_GRUPO_ROLES_PERSONAL
     * 
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.0 14-12-2018
     * Costo query: 14
     * 
     * @param  string $strUsuario
     * @return array  $arrayCargo
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.1 17-06-2021 - Se agrega los nuevos cargos.
     *
     */    
    public function getCargosPersonas($strUsuario,$strCargosAdicionales = null)
    {
        $strEstadoActivo = 'Activo';
        $arrayCargo      = array();
        try
        {
            $strSelect ="SELECT DISTINCT
                            CASE
                              WHEN AR.DESCRIPCION_ROL IS NOT NULL
                              THEN 'ASISTENTE'
                              ELSE
                                (SELECT
                                    CASE
                                        WHEN APD.VALOR3 IN ('VENDEDOR','GERENTE_VENTAS','SUBGERENTE'".$strCargosAdicionales.")
                                        THEN APD.VALOR3
                                        ELSE 'Otros'
                                    END
                                FROM DB_GENERAL.ADMI_PARAMETRO_DET APD
                                WHERE APD.ID_PARAMETRO_DET = IPERC.VALOR
                                )
                            END AS strCargoPersonal  ";
            $strFrom   = "FROM DB_COMERCIAL.INFO_PERSONA IP
                            JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL       IPER  ON IP.ID_PERSONA                = IPER.PERSONA_ID
                            JOIN DB_COMERCIAL.INFO_EMPRESA_ROL               IER   ON IER.ID_EMPRESA_ROL           = IPER.EMPRESA_ROL_ID
                            LEFT JOIN DB_GENERAL.ADMI_ROL                    AR    ON AR.ID_ROL                    = IER.ROL_ID
                            AND LOWER(AR.DESCRIPCION_ROL) LIKE '%asist%'
                            JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC IPERC ON IPERC.PERSONA_EMPRESA_ROL_ID = IPER.ID_PERSONA_ROL
                            LEFT JOIN DB_COMERCIAL.ADMI_CARACTERISTICA       AC    ON AC.ID_CARACTERISTICA         = IPERC.CARACTERISTICA_ID ";
            $strWhere  = " WHERE IP.ESTADO                     = :strEstadoActivo
                            AND IPER.ESTADO                    = :strEstadoActivo
                            AND IPERC.ESTADO                   = :strEstadoActivo
                            AND AC.DESCRIPCION_CARACTERISTICA IN('ASISTENTE_POR_CARGO','CARGO_GRUPO_ROLES_PERSONAL')
                            AND IP.LOGIN                       = :strUsuario Order by strCargoPersonal asc ";

            $strSql  = $strSelect.$strFrom.$strWhere;
            $objStmt = $this->_em->getConnection()->prepare($strSql);
            $objStmt->bindValue('strEstadoActivo',$strEstadoActivo);
            $objStmt->bindValue('strUsuario',$strUsuario);
            $objStmt->execute();

            $arrayCargo = $objStmt->fetchAll();
        }
        catch(\Exception $e)
        {
            error_log('getCargosPersonas -> '.$e->getMessage());
            throw($e);
        }
        return $arrayCargo;
    }
    /**
     * Documentación para la función 'getVendedoresKams'.
     *
     * Función que retorna el listado de vendedores kams.
     *
     * @param array $arrayParametros [
     *                                  "strPrefijoEmpresa"     => Prefijo de la empresa.
     *                                  "strCodEmpresa"         => Código de la empresa.
     *                                  "strEstadoActivo"       => Estado.
     *                                  "strDescCaracteristica" => Característica 'CARGO_GRUPO_ROLES_PERSONAL'.
     *                                  "strNombreParametro"    => Parámetro 'GRUPO_ROLES_PERSONAL'.
     *                                  "strDescCargo"          => Cargo 'GERENTE_VENTAS'.
     *                                  "strDescRolNoPermitido" => Roles no permitidos 'ROLES_NO_PERMITIDOS'.
     *                               ]
     *
     * @return array $arrayRespuesta [
     *                                 "infoCliente"    =>  arreglo de la información del cliente.
     *                                 "infoVendedores" =>  vendedores asociados al punto.
     *                                 "error"          =>  mensaje de error.
     *                               ]
     *
     * Costo query: 143
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 17-06-2021
     *
     */
    public function getVendedoresKams($arrayParametros)
    {
        $strPrefijoEmpresa       = $arrayParametros['strPrefijoEmpresa']     ? $arrayParametros['strPrefijoEmpresa']:"";
        $strCodEmpresa           = $arrayParametros['strCodEmpresa']         ? $arrayParametros['strCodEmpresa']:"";
        $strEstadoActivo         = $arrayParametros['strEstadoActivo']       ? $arrayParametros['strEstadoActivo']:"";
        $strDescCaracteristica   = $arrayParametros['strDescCaracteristica'] ? $arrayParametros['strDescCaracteristica']:"";
        $strNombreParametro      = $arrayParametros['strNombreParametro']    ? $arrayParametros['strNombreParametro']:"";
        $strDescCargo            = $arrayParametros['strDescCargo']          ? $arrayParametros['strDescCargo']:"";
        $strDescRolNoPermitido   = $arrayParametros['strDescRolNoPermitido'] ? $arrayParametros['strDescRolNoPermitido']:"";
        $strSelect               = '';
        $strFrom                 = '';
        $strWhere                = '';
        try
        {
            if(empty($strPrefijoEmpresa) || empty($strCodEmpresa))
            {
                throw new \Exception("El prefijo, código y la identificación son obligatorios para realizar la búsqueda de vendedores kams.");
            }
            if($strPrefijoEmpresa != 'TN')
            {
                throw new \Exception("La consulta, solo aplica para la empresa Telconet");
            }
            $objRsmb      = new ResultSetMappingBuilder($this->_em);
            $objQuery     = $this->_em->createNativeQuery(null, $objRsmb);
            $strSelect    = " SELECT IP.ID_PERSONA,
                                     CONCAT(IP.NOMBRES || ' ', IP.APELLIDOS) AS NOMBRES_COMPLETOS,
                                     IP.LOGIN,
                                     IP.ESTADO,
                                     IPER.ID_PERSONA_ROL ";
            $strFrom      = " FROM DB_COMERCIAL.INFO_PERSONA               IP
                              JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL   IPER ON IPER.PERSONA_ID   = IP.ID_PERSONA
                              JOIN DB_COMERCIAL.INFO_EMPRESA_ROL           IER ON IER.ID_EMPRESA_ROL = IPER.EMPRESA_ROL_ID
                              JOIN DB_COMERCIAL.ADMI_ROL                   AR ON AR.ID_ROL           = IER.ROL_ID ";
            $strWhere     = " WHERE IER.EMPRESA_COD = :strCodEmpresa
                                    AND AR.DESCRIPCION_ROL NOT IN (
                                        SELECT
                                            APD.DESCRIPCION
                                        FROM
                                            DB_GENERAL.ADMI_PARAMETRO_CAB   APC
                                            JOIN DB_GENERAL.ADMI_PARAMETRO_DET   APD ON APC.ID_PARAMETRO = APD.PARAMETRO_ID
                                        WHERE
                                            APC.NOMBRE_PARAMETRO = :strDescRolNoPermitido
                                            AND APD.EMPRESA_COD = :strCodEmpresa
                                            AND APD.ESTADO = :strEstadoActivo
                                    )
                                    AND IPER.ESTADO = :strEstadoActivo
                                    AND IPER.REPORTA_PERSONA_EMPRESA_ROL_ID IN (
                                        SELECT
                                            IPER.ID_PERSONA_ROL
                                        FROM
                                            DB_COMERCIAL.INFO_PERSONA                     IP
                                            JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL         IPER ON IPER.PERSONA_ID = IP.ID_PERSONA
                                            JOIN DB_COMERCIAL.INFO_EMPRESA_ROL                 IER ON IER.ID_EMPRESA_ROL = IPER.EMPRESA_ROL_ID
                                            JOIN DB_COMERCIAL.ADMI_ROL                         AR ON AR.ID_ROL = IER.ROL_ID
                                            JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC   IPERC 
                                                                                               ON IPERC.PERSONA_EMPRESA_ROL_ID = IPER.ID_PERSONA_ROL
                                            JOIN DB_COMERCIAL.ADMI_CARACTERISTICA              AC ON AC.ID_CARACTERISTICA = IPERC.CARACTERISTICA_ID
                                            JOIN DB_GENERAL.ADMI_PARAMETRO_DET                 APD ON APD.ID_PARAMETRO_DET = IPERC.VALOR
                                            JOIN DB_GENERAL.ADMI_PARAMETRO_CAB                 APC ON APC.ID_PARAMETRO = APD.PARAMETRO_ID
                                        WHERE
                                            IER.EMPRESA_COD = :strCodEmpresa
                                            AND APD.ESTADO = :strEstadoActivo
                                            AND APC.ESTADO = :strEstadoActivo
                                            AND AC.ESTADO = :strEstadoActivo
                                            AND IPER.ESTADO = :strEstadoActivo
                                            AND IPERC.ESTADO = :strEstadoActivo
                                            AND AR.DESCRIPCION_ROL NOT IN (
                                                SELECT
                                                    APD.DESCRIPCION
                                                FROM
                                                    DB_GENERAL.ADMI_PARAMETRO_CAB   APC
                                                    JOIN DB_GENERAL.ADMI_PARAMETRO_DET   APD ON APC.ID_PARAMETRO = APD.PARAMETRO_ID
                                                WHERE
                                                    APC.NOMBRE_PARAMETRO = :strDescRolNoPermitido
                                                    AND APD.EMPRESA_COD = :strCodEmpresa
                                                    AND APD.ESTADO = :strEstadoActivo
                                            )
                                            AND APD.DESCRIPCION = :strDescCargo
                                            AND AC.DESCRIPCION_CARACTERISTICA = :strDescCaracteristica
                                            AND APC.NOMBRE_PARAMETRO = :strNombreParametro
                                    ) ";
            $strSql = $strSelect . $strFrom . $strWhere;
            $objQuery->setParameter('strCodEmpresa',         $strCodEmpresa,         'string');
            $objQuery->setParameter('strEstadoActivo',       $strEstadoActivo,       'string');
            $objQuery->setParameter('strDescCaracteristica', $strDescCaracteristica, 'string');
            $objQuery->setParameter('strNombreParametro',    $strNombreParametro,    'string');
            $objQuery->setParameter('strDescCargo',          $strDescCargo,          'string');
            $objQuery->setParameter('strDescRolNoPermitido', $strDescRolNoPermitido, 'string');

            $objRsmb->addScalarResult('ID_PERSONA'       ,'ID_PERSONA'       ,'string');
            $objRsmb->addScalarResult('NOMBRES_COMPLETOS','NOMBRES_COMPLETOS','string');
            $objRsmb->addScalarResult('LOGIN'            ,'LOGIN'            ,'string');
            $objRsmb->addScalarResult('ESTADO'           ,'ESTADO'           ,'string');
            $objRsmb->addScalarResult('ID_PERSONA_ROL'   ,'ID_PERSONA_ROL'   ,'string');
            $objQuery->setSQL($strSql);
            $arrayResultado                  = $objQuery->getResult();
            $arrayRespuesta['vendedoresKam'] = $arrayResultado;
        }
        catch(\Exception $e)
        {
            $strMensajeError = $ex->getMessage();
        }
        $arrayRespuesta['error'] = $strMensajeError;
        return $arrayRespuesta;
    }
    /**
     * 
     * Metodo encargado de retornar todos los vendedores de acuerdo al idReportaPersona y al prefijo de la empresa
     * 
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.0 17-08-2018
     * Costo query: 7
     * 
     * @param  string $intReportaPersona
     * @param  string $strPrefijoEmpresa
     * @return array $arrayListaVendedores
     * 
     */    
    public function getVendedor($intReportaPersona,$strPrefijoEmpresa)
    {
        $arrayListaVendedores=array();
        try
        {        
            $strSelect ="SELECT IP.LOGIN  ";
            $strFrom   = "FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPER
                                JOIN DB_COMERCIAL.INFO_PERSONA IP ON IP.ID_PERSONA=IPER.PERSONA_ID 
                                JOIN DB_COMERCIAL.INFO_OFICINA_GRUPO        IOG  ON IOG.ID_OFICINA=IPER.OFICINA_ID
                                JOIN DB_COMERCIAL.INFO_EMPRESA_GRUPO        IEG  ON IOG.EMPRESA_ID=IEG.COD_EMPRESA ";
            $strWhere  = " WHERE IPER.REPORTA_PERSONA_EMPRESA_ROL_ID=:IdReportaPersona
                                AND LOWER(IPER.ESTADO) = LOWER('Activo')
                                AND LOWER(IOG.ESTADO)  = LOWER('Activo')
                                AND LOWER(IEG.PREFIJO) = LOWER(:strPrefijoEmpresa)
                                AND LOWER(IP.ESTADO)   = LOWER('Activo') 
                                order by IP.LOGIN ";

            $strSql = $strSelect.$strFrom.$strWhere;

            $objStmt = $this->_em->getConnection()->prepare($strSql);

            $objStmt->bindValue('IdReportaPersona',$intReportaPersona);
            $objStmt->bindValue('strPrefijoEmpresa',$strPrefijoEmpresa);
            $objStmt->execute();

            $arrayListaVendedores = $objStmt->fetchAll();
        }
        catch(\Exception $e)
        {
            error_log('getVendedor -> '.$e->getMessage());
            throw($e);
        }        
        return $arrayListaVendedores;
    }
    
    /**
     * 
     * Metodo encargado de retornar la inforacion de acuerdo al login que recibe por parametro
     * 
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.0 17-08-2018
     * Costo query: 9
     * 
     * @param  string $strUser
     * @param  string $strPrefijoEmpresa
     * @return array $arrayInformacionVendedor
     * 
     */    
    public function getInfoVendedor($strUser,$strPrefijoEmpresa)
    {               
        $arrayInformacionVendedor = array();
        try
        {
            $strSelect =" SELECT IP.CARGO AS CARGO ";
            $strFrom   =" FROM DB_COMERCIAL.INFO_PERSONA IP 
                              JOIN DB_COMERCIAL.ADMI_TITULO               ATI  ON IP.TITULO_ID      = ATI.ID_TITULO
                              JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL  IPER ON IPER.PERSONA_ID = IP.ID_PERSONA
                              JOIN DB_COMERCIAL.INFO_OFICINA_GRUPO        IOG  ON IOG.ID_OFICINA=IPER.OFICINA_ID
                              JOIN DB_COMERCIAL.INFO_EMPRESA_GRUPO        IEG  ON IOG.EMPRESA_ID=IEG.COD_EMPRESA ";
            $strWhere  =" WHERE LOWER(IP.LOGIN) = LOWER(:strUser)
                            AND LOWER(IPER.ESTADO) = LOWER('Activo')
                            AND LOWER(IP.ESTADO)   = LOWER('Activo')
                            AND LOWER(ATI.ESTADO)  = LOWER('Activo')
                            AND LOWER(IOG.ESTADO)  = LOWER('Activo')
                            AND LOWER(IEG.PREFIJO) = LOWER(:strPrefijoEmpresa)
                            AND IPER.REPORTA_PERSONA_EMPRESA_ROL_ID IS NOT NULL ";
            
            $strSql = $strSelect.$strFrom.$strWhere;
            $objStmt = $this->_em->getConnection()->prepare($strSql);
            $objStmt->bindValue('strUser', $strUser);
            $objStmt->bindValue('strPrefijoEmpresa', $strPrefijoEmpresa);
            $objStmt->execute();

            $arrayInformacionVendedor = $objStmt->fetchAll();          
        }
        catch(\Exception $e)
        {
            error_log('getInfoVendedor -> '.$e->getMessage());
            throw($e);
        }
       
        return $arrayInformacionVendedor;
    }    
    
    /**
     * 
     * Método que devuelve información del perfil del ingeniero vip asignado
     * 
     * @author Ronny Moran Chancay <rmoranc@telconet.ec>
     * @version 1.0
     * @since 03-07-2018
     * 
     * Costo 6
     * 
     * @param Array $arrayParametros [ 
     *                                intIdPersonaEmpresaRol 
     *                               ]
     * @return Array $arrayResultado
     */
    public function getPerfilUsuarioVip($arrayParametros)
    {
        $objRsm   = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsm);
        $strWhere = "";
        $strSql   = "";
        if(isset($arrayParametros["intIdPersonaEmpresaRol"]) && $arrayParametros["intIdPersonaEmpresaRol"] > 0 &&
           isset($arrayParametros["intIdPersona"]) && $arrayParametros["intIdPersona"] > 0)
        {
            $strSql   = " SELECT NAF.NOMBRE                             AS NOMBRE,
                            NAF.FOTO                                    AS FOTO,
                            NAF.MAIL_CIA                                AS MAIL,
                            CONCAT('0', SUBSTR(IE.NOMBRE_ELEMENTO, -9)) AS CELULAR,
                            'VIP'                                       AS TIPO,
                            NAF.IND_REGION                              AS REGION,
                            NAF.SEXO                                    AS SEXO,
                            (SELECT MAX(IHIA.FE_CREACION)
                            FROM DB_SOPORTE.INFO_HISTORIAL_INGRESO_APP IHIA
                            WHERE IHIA.PERSONA_ID = IP_EMP.ID_PERSONA
                            ) AS ULTIMA_SESION
                          FROM DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO IDE,
                            DB_INFRAESTRUCTURA.INFO_ELEMENTO IE,
                            NAF47_TNET.ARPLME NAF,
                            DB_COMERCIAL.INFO_PERSONA IP_EMP,
                            DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPER_EMP,
                            DB_COMERCIAL.ADMI_CARACTERISTICA AC,
                            DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC IPERC_EMP,
                            DB_COMERCIAL.INFO_PERSONA IP_VIP,
                            DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPER_VIP
                          WHERE IPER_EMP.ID_PERSONA_ROL    = IPERC_EMP.PERSONA_EMPRESA_ROL_ID
                          AND IP_EMP.ID_PERSONA            = IPER_EMP.PERSONA_ID
                          AND AC.ID_CARACTERISTICA         = IPERC_EMP.CARACTERISTICA_ID
                          AND IDE.ELEMENTO_ID              = IE.ID_ELEMENTO
                          AND IPER_VIP.ID_PERSONA_ROL      = TO_NUMBER(IPERC_EMP.VALOR)
                          AND IDE.DETALLE_VALOR            = TO_CHAR(IPERC_EMP.VALOR)
                          AND IP_VIP.ID_PERSONA            = IPER_VIP.PERSONA_ID
                          AND NAF.CEDULA                   = IP_VIP.IDENTIFICACION_CLIENTE
                          AND AC.DESCRIPCION_CARACTERISTICA= :strDescripcionCaracteristica
                          AND NAF.NO_CIA                   = :intCodEmpresa
                          AND IDE.DETALLE_NOMBRE           = :strDetalleNombre
                          AND IPERC_EMP.ESTADO             = :strEstadoIPERCEMP
                          AND IPER_VIP.ESTADO              = :strEstadoIPERVIP
                          AND AC.ESTADO                    = :strEstadoAC
                          AND IDE.ESTADO                   = :strEstadoIDE
                          AND NAF.ESTADO                   = :strEstadoNAF
                          AND IPER_EMP.ID_PERSONA_ROL      = :intIdPersonaEmpresaRol";
            $objQuery->setParameter("intIdPersonaEmpresaRol",       $arrayParametros["intIdPersonaEmpresaRol"]);
            $objQuery->setParameter("intIdPersona",                 $arrayParametros["intIdPersona"]);
            $objQuery->setParameter("strDescripcionCaracteristica", 'ID_VIP');
            $objQuery->setParameter("strDescFormaContacto",         'Correo Electronico');
            $objQuery->setParameter("intCodEmpresa",                10);
            $objQuery->setParameter("strDetalleNombre",             'COLABORADOR');
            $objQuery->setParameter("strEstadoIPERCEMP",            'Activo');
            $objQuery->setParameter("strEstadoIPERVIP",             'Activo');
            $objQuery->setParameter("strEstadoAC",                  'Activo');
            $objQuery->setParameter("strEstadoIDE",                 'Activo');
            $objQuery->setParameter("strEstadoNAF",                 'A');
        }
        $strSql .= $strWhere;
        $objRsm->addScalarResult('NOMBRE',          'nombre',           'string');
        $objRsm->addScalarResult('FOTO',            'foto',             'string');
        $objRsm->addScalarResult('MAIL',            'mail',             'string');
        $objRsm->addScalarResult('CELULAR',         'celular',          'string');
        $objRsm->addScalarResult('TIPO',            'tipo',             'string');
        $objRsm->addScalarResult('REGION',          'region',           'string');
        $objRsm->addScalarResult('SEXO',            'sexo',             'string');
        $objRsm->addScalarResult('ULTIMA_SESION',   'ultimaSesion',     'string');

        $objQuery->setSQL($strSql);

        $arrayResultado = $objQuery->getArrayResult();
 
        return $arrayResultado;
    }

    /**
     * 
     * Método que devuelve información del asesor comercial.
     * 
     * @author Ronny Moran Chancay <rmoranc@telconet.ec>
     * @version 1.0
     * @since 03-07-2018
     * 
     * Costo 6
     * 
     * @param Array $arrayParametros [ 
     *                                intIdPersonaEmpresaRol 
     *                               ]
     * @return Array $arrayResultado
     */
    public function getPerfilUsuarioComercial($arrayParametros)
    {
        $objRsm   = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsm);
        $strWhere = "";
        $strSql   = "";
        if(isset($arrayParametros["intIdPersonaEmpresaRol"]) && $arrayParametros["intIdPersonaEmpresaRol"] > 0 &&
           isset($arrayParametros["intIdPersona"]) && $arrayParametros["intIdPersona"] > 0)
        {
            $strSql   = "SELECT NAF_PERCOM.NOMBRE AS NOMBRE,
                            NAF_PERCOM.FOTO        AS FOTO,
                            NAF_PERCOM.MAIL_CIA    AS MAIL,
                            NAF_PERCOM.CELULAR     AS CELULAR,
                            'ASESOR'               AS TIPO,
                            NAF_PERCOM.IND_REGION  AS REGION,
                            NAF_PERCOM.SEXO        AS SEXO,
                            (SELECT IPER.ID_PERSONA_ROL
                            FROM INFO_PERSONA INFP,
                              INFO_PERSONA_EMPRESA_ROL IPER
                            WHERE INFP.ID_PERSONA           = IPER.PERSONA_ID
                            AND INFP.IDENTIFICACION_CLIENTE = NAF_PERCOM.CEDULA
                            AND INFP.ESTADO                 = 'Activo'
                            AND IPER.ESTADO                 = 'Activo'
                            AND ROWNUM                      < 2
                            )                     AS PERSONA_EMPRESA_ROL_ID,
                            (SELECT MAX(IHIA.FE_CREACION)
                                FROM DB_SOPORTE.INFO_HISTORIAL_INGRESO_APP IHIA
                                WHERE IHIA.PERSONA_ID = :intIdPersona
                            ) AS ULTIMA_SESION
                          FROM NAF47_TNET.ARPLME NAF_PERCOM
                          WHERE CEDULA IN
                            (SELECT DISTINCT(pervend.IDENTIFICACION_CLIENTE)
                                FROM DB_COMERCIAL.INFO_SERVICIO infs,
                                  DB_COMERCIAL.INFO_PERSONA pervend
                                WHERE pervend.LOGIN  = infs.USR_VENDEDOR
                                AND infs.ID_SERVICIO =
                                  (SELECT MAX(iser.ID_SERVICIO)
                                  FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL iper,
                                    DB_COMERCIAL.INFO_PUNTO ipun,
                                    DB_COMERCIAL.INFO_SERVICIO iser
                                  WHERE iper.ID_PERSONA_ROL = ipun.PERSONA_EMPRESA_ROL_ID
                                  AND ipun.ID_PUNTO         = iser.PUNTO_ID
                                  AND iser.USR_VENDEDOR IS NOT NULL
                                  AND ipun.ESTADO           = :strEstado
                                  AND iser.ESTADO           = :strEstadoServ
                                  AND iper.ID_PERSONA_ROL   = :intIdPersonaEmpresaRol
                                  )
                            )
                          AND NAF_PERCOM.ESTADO = :strEstadoNAF
                          AND NAF_PERCOM.NO_CIA=:intCodEmpresa
                          AND ROWNUM < 2";

            $objQuery->setParameter("intIdPersonaEmpresaRol", $arrayParametros["intIdPersonaEmpresaRol"]);
            $objQuery->setParameter("intIdPersona",           $arrayParametros["intIdPersona"]);
            $objQuery->setParameter("strEstado",              'Activo');
            $objQuery->setParameter("strEstadoServ",          'Activo');
            $objQuery->setParameter("strEstadoNAF",           'A');
            $objQuery->setParameter("intCodEmpresa",          10);
        }
        $strSql .= $strWhere;
        $objRsm->addScalarResult('NOMBRE',                  'nombre',               'string');
        $objRsm->addScalarResult('FOTO',                    'foto',                 'string');
        $objRsm->addScalarResult('MAIL',                    'mail',                 'string');
        $objRsm->addScalarResult('CELULAR',                 'celular',              'string');
        $objRsm->addScalarResult('TIPO',                    'tipo',                 'string');
        $objRsm->addScalarResult('REGION',                  'region',               'string');
        $objRsm->addScalarResult('SEXO',                    'sexo',                 'string');
        $objRsm->addScalarResult('ULTIMA_SESION',           'ultimaSesion',         'string');
        $objRsm->addScalarResult('PERSONA_EMPRESA_ROL_ID',  'personaEmpresaRolId',  'string');

        $objQuery->setSQL($strSql);

        $arrayResultado = $objQuery->getArrayResult();

        return $arrayResultado;
    }
    
    
    /**
     * Método que obtiene la descripción de una tarea
     *  
     * @author: Ronny Morán <rmoranc@telconet.ec>
     * @version 1.0 28-12-2018 
     *
     *  
     * @param  $arrayParametros[ $arrayParametros]
     * @return $arrayResultado
     */
    public function obtenerDescripcionTarea($arrayParametros)
    {
        $arrayListaMaterialesNaf            = array();
        $intIdServicio                      = $arrayParametros['id_servicio'];
        $objRsm                             = new ResultSetMappingBuilder($this->_em);
        $objQuery                           = $this->_em->createNativeQuery(null, $objRsm);
     
        $strWhereInfo  =   "";
        $strSql        =   "SELECT  
                                ADMP.DESCRIPCION_PRODUCTO 
                            FROM 
                                INFO_SERVICIO INFS, ADMI_PRODUCTO ADMP 
                            WHERE 
                                INFS.PRODUCTO_ID = ADMP.ID_PRODUCTO ";
        
            if(isset($intIdServicio) && !empty($intIdServicio))
            {
                $strWhereInfo .= " AND ID_SERVICIO = :id_servicio";
                $objQuery->setParameter("id_servicio"       ,$intIdServicio); 
            }
            $strSql .= $strWhereInfo;
            
            $objRsm->addScalarResult('DESCRIPCION_PRODUCTO',              'descripcion',          'string');			
            $objQuery->setSQL($strSql);
            $arrayListaMaterialesNaf = $objQuery->getArrayResult();                  
            $strDescripcion = '';
            if(!empty($arrayListaMaterialesNaf))
            {
                foreach($arrayListaMaterialesNaf as $tareaDetalle)
                {
                    $strDescripcion         = $tareaDetalle['descripcion'];
                }
            }
        return $strDescripcion;
    }
    
    
    /**
     * Método que obtiene información de una tarea Netvoice
     *  
     * @author: Ronny Morán Chancay <rmoranc@telconet.ec>
     * @version 1.0 28-12-2018 
     * costoQuery = 14
     *
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.1 23/01/2020
     * Se agregan esquemas en las tablas consultadas 
     *  
     * @param  $arrayParametros[ $arrayParametros]
     * @return $arrayResultado
     */
    public function obtenerInformacionNetvoice($arrayParametros)
    {
        $arrayListaMaterialesNaf            = array();
        $intIdDetalle                      = $arrayParametros['idDetalle'];
        $objRsm                             = new ResultSetMappingBuilder($this->_em);
        $objQuery                           = $this->_em->createNativeQuery(null, $objRsm);
     
        error_log($intIdDetalle);
        
        $strWhereInfo  =   "";
        $strSql        =   "SELECT 
                                INFDT.OBSERVACION, 
                                INFDT.FE_CREACION,
                                ADMTA.NOMBRE_TAREA,
                                ADMICT.NOMBRE_CANTON
                            FROM 
                                DB_COMERCIAL.INFO_DETALLE INFDT,  DB_SOPORTE.ADMI_TAREA ADMTA,
                                DB_COMERCIAL.INFO_DETALLE_SOLICITUD INFDS, DB_COMERCIAL.INFO_SERVICIO  INFSERV,
                                DB_SOPORTE.INFO_PUNTO  INFPT, DB_GENERAL.ADMI_SECTOR  ADMISE, 
                                DB_COMERCIAL.ADMI_PARROQUIA  ADMIPA, DB_GENERAL.ADMI_CANTON  ADMICT
                            WHERE 
                                TAREA_ID = ADMTA.ID_TAREA
                                AND INFDS.ID_DETALLE_SOLICITUD = INFDT.DETALLE_SOLICITUD_ID   
                                AND INFSERV.ID_SERVICIO = INFDS.SERVICIO_ID
                                AND INFPT.ID_PUNTO = INFSERV.PUNTO_ID
                                AND ADMISE.ID_SECTOR = INFPT.SECTOR_ID
                                AND ADMIPA.ID_PARROQUIA = ADMISE.PARROQUIA_ID
                                AND ADMICT.ID_CANTON = ADMIPA.CANTON_ID";

            if(isset($intIdDetalle) && !empty($intIdDetalle))
            {
                $strWhereInfo .= " AND INFDT.ID_DETALLE = :idDetalle";
                $objQuery->setParameter("idDetalle"       ,$intIdDetalle); 
            }
            $strSql .= $strWhereInfo;
            
            $objRsm->addScalarResult('OBSERVACION',              'observacion',          'string');			
            $objRsm->addScalarResult('FE_CREACION',              'fecha_creacion',       'string');			
            $objRsm->addScalarResult('NOMBRE_TAREA',             'nombre_tarea',         'string');	
            $objRsm->addScalarResult('NOMBRE_CANTON',            'canton',               'string');	
            
            
            $objQuery->setSQL($strSql);
            $arrayListaMaterialesNaf = $objQuery->getArrayResult();                  
        return $arrayListaMaterialesNaf;
    }
    /**
     * Método encargado de devolver todos los clientes asignados a un vendedor según los parámetros enviados
     * Costo: 18
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.0 24-11-2019
     *
     * @param array $arrayParametros [
     *                                  'strLoginVendedor' => login del vendedor,
     *                               ]
     * @throws $objException
     * @return array $arrayRespuesta['total', 'registros']
     *
     */
    public function getClientesPorVendedor($arrayParametros)
    {
        $arrayRespuesta['registros'] = array();
        $arrayRespuesta['total']     = 0;
        $strSelect                   = '';
        $strFrom                     = '';
        $strWhere                    = '';
        $strOrderBy                  = '';

        try
        {
            $objRsmb       = new ResultSetMappingBuilder($this->getEntityManager());
            $objQuery      = $this->getEntityManager()->createNativeQuery(null, $objRsmb);

            $strSelect = " SELECT DISTINCT ip.id_persona as intIdPersona, 
                             iper.id_persona_rol as intIdPersonaEmpresaRol,
                             COALESCE(UPPER(TRIM(ip.razon_social)), TRIM(CONCAT(CONCAT(UPPER(TRIM(ip.nombres)), ' '),
                             UPPER(TRIM(ip.apellidos))))) as strRazonSocial,
                             ip.identificacion_cliente as strIdentificacion ";

            $strFrom = " FROM DB_COMERCIAL.Info_Persona ip 
                            JOIN  DB_COMERCIAL.Info_Persona_Empresa_Rol iper ON ip.id_persona = iper.persona_id
                            JOIN  DB_COMERCIAL.Info_Empresa_Rol ier ON iper.empresa_rol_id = ier.id_empresa_rol
                            JOIN  DB_COMERCIAL.Admi_Rol ar ON ier.rol_id = ar.id_rol
                            JOIN  DB_COMERCIAL.Info_Punto ipto ON ipto.persona_empresa_rol_id = iper.id_persona_rol ";

            $strWhere = " WHERE iper.estado = :strEstadoIper AND
                            ar.tipo_rol_id = :intTipoRolId AND
                            ier.empresa_cod = :strEmpresaCod ";

            if(isset($arrayParametros['strLoginVendedor']) && !empty($arrayParametros['strLoginVendedor']))
            {
                $strWhere .= " AND ipto.usr_vendedor = :strLoginVendedor ";

                $objQuery->setParameter('strLoginVendedor', $arrayParametros['strLoginVendedor'], 'string');
            }

            $strOrderBy =  " ORDER BY strRazonSocial ";

            $strSql         = $strSelect . $strFrom . $strWhere . $strOrderBy;

            $objQuery->setParameter('strEstadoIper', 'Activo', 'string');
            $objQuery->setParameter('intTipoRolId',2, 'integer');
            $objQuery->setParameter('strEmpresaCod', '10', 'string');

            $objRsmb->addScalarResult('INTIDPERSONA','intIdPersona','integer');
            $objRsmb->addScalarResult('INTIDPERSONAEMPRESAROL','intIdPersonaEmpresaRol','integer');
            $objRsmb->addScalarResult('STRRAZONSOCIAL','strRazonSocial','string');
            $objRsmb->addScalarResult('STRIDENTIFICACION','strIdentificacion','string');

            $objQuery->setSQL($strSql);

            $arrayResultado = $objQuery->getArrayResult();
            $intTotal       = count($arrayResultado);

            $arrayRespuesta['registros'] = $arrayResultado;
            $arrayRespuesta['total']     = $intTotal;
        }
        catch(\Exception $objException)
        {
            throw $objException;
        }

        return $arrayRespuesta;
    }

    /**
     * Método encargado de devolver todos los clientes de una solicitud de cambio masivo de vendedor según los parámetros enviados
     * Costo: 17
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>

     * @version 1.0 24-11-2019
     *
     * @param array $arrayParametros [
     *                                  'intIdSolicitud' => Id de la solicitud,
     *                               ]
     *
     * @throws $objException
     * @return array $arrayRespuesta['total', 'registros']
     */
    public function getClientesPorSolicitud($arrayParametros)
    {
        $strSelect  = '';
        $strFrom    = '';
        $strWhere   = '';
        $strOrderBy = '';

        try
        {
            $objRsmb       = new ResultSetMappingBuilder($this->getEntityManager());
            $objQuery      = $this->getEntityManager()->createNativeQuery(null, $objRsmb);
            $objSubRsmb    = new ResultSetMappingBuilder($this->getEntityManager());
            $objSubQuery   = $this->getEntityManager()->createNativeQuery(null, $objSubRsmb);

            $strSelectSub  = " SELECT DBMS_LOB.SUBSTR(il.descripcion, 32767, 1 ) as strIdClientes
                                 FROM DB_GENERAL.info_log il 
                                 WHERE il.aplicacion = TO_CHAR(:intIdSolicitud) ";

            $objSubRsmb->addScalarResult('STRIDCLIENTES','strIdClientes','string');
            $objSubQuery->setParameter('intIdSolicitud', $arrayParametros['intIdSolicitud'], 'integer');
            $objSubQuery->setSQL($strSelectSub);
            $strIdClientes = $objSubQuery->getSingleScalarResult();
            $strIdClientes = (!is_null($strIdClientes) && !empty($strIdClientes)) ? $strIdClientes : '0';

            $strSelect = " SELECT DISTINCT ip.id_persona as intIdPersona, 
                             iper.id_persona_rol as intIdPersonaEmpresaRol,
                             COALESCE(UPPER(TRIM(ip.razon_social)), TRIM(CONCAT(CONCAT(UPPER(TRIM(ip.nombres)), ' '),
                             UPPER(TRIM(ip.apellidos))))) as strRazonSocial,
                             ip.identificacion_cliente as strIdentificacion ";

            $strFrom = " FROM DB_COMERCIAL.Info_Persona ip 
                            JOIN  DB_COMERCIAL.Info_Persona_Empresa_Rol iper ON ip.id_persona = iper.persona_id
                            JOIN  DB_COMERCIAL.Info_Empresa_Rol ier ON iper.empresa_rol_id = ier.id_empresa_rol
                            JOIN  DB_COMERCIAL.Admi_Rol ar ON ier.rol_id = ar.id_rol ";

            $strWhere = " WHERE iper.estado = :strEstadoIper AND
                                ar.tipo_rol_id = :intTipoRolId AND
                                ier.empresa_cod = :strEmpresaCod AND 
                                iper.id_persona_rol in (" . $strIdClientes . ")";

            $strOrderBy =  " ORDER BY strRazonSocial ";

            $strSql         = $strSelect . $strFrom . $strWhere . $strOrderBy;


            $objQuery->setParameter('strEstadoIper', 'Activo', 'string');
            $objQuery->setParameter('intTipoRolId', 2, 'integer');
            $objQuery->setParameter('strEmpresaCod', '10', 'string');

            $objRsmb->addScalarResult('INTIDPERSONA','intIdPersona','integer');
            $objRsmb->addScalarResult('INTIDPERSONAEMPRESAROL','intIdPersonaEmpresaRol','integer');
            $objRsmb->addScalarResult('STRRAZONSOCIAL','strRazonSocial','string');
            $objRsmb->addScalarResult('STRIDENTIFICACION','strIdentificacion','string');

            $objQuery->setSQL($strSql);

            $arrayResultado = $objQuery->getArrayResult();
            $intTotal       = count($arrayResultado);

            $arrayRespuesta['registros'] = $arrayResultado;
            $arrayRespuesta['total']     = $intTotal;
        }
        catch(\Exception $objException)
        {
            throw $objException;
        }

        return $arrayRespuesta;
    }
    
     /**
     * 
     * Metodo encargado de retornar el mail del NAF según el login que recibe por parámetro
     * 
     * @author Karen Rodríguez <kyrodriguez@telconet.ec>
     * @version 1.0 17-02-2020
     * Costo query: 4
     * 
     * @param  array $arrayParametros
     * @return array $arrayInformacion
     * 
     */    
    public function getMailNaf($arrayParametros)
    {               
        $arrayInformacion = array();
        $objUtilService = $arrayParametros['objUtilService'];
        try
        {
            $strSelect =" SELECT MAX(EMPLE.MAIL_CIA) AS MAIL_CIA";
            $strFrom   =" FROM NAF47_TNET.V_EMPLEADOS_EMPRESAS EMPLE";
            $strWhere  =" WHERE EMPLE.LOGIN_EMPLE = LOWER(:strUser)
                          AND EMPLE.ESTADO = 'A' 
                          AND EMPLE.NO_CIA= :intIdEmp";
            
            $strSql = $strSelect.$strFrom.$strWhere;
            $objStmt = $this->_em->getConnection()->prepare($strSql);
            $objStmt->bindValue('strUser', $arrayParametros['strLogin']);
            $objStmt->bindValue('intIdEmp', $arrayParametros['intIdEmp']);
            $objStmt->execute();

            $arrayInformacion = $objStmt->fetchAll();       
            
        }
        catch(\Exception $e)
        {
            $objUtilService->insertError('Telcos+',
			                  'InfoPersonaRepository->getMailNaf',
			                  $e->getMessage(),
                                          'Telcos+',
                                          '');
            throw($e);
        }
       
        return $arrayInformacion[0]['MAIL_CIA'];
    } 

    /**
     * 
     * Metodo encargado de obtener el telefono e imei de la tablet asignada al empleado
     * 
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 1.0 28-07-2022
     * 
     * @param  array $arrayParametros
     * @return array $arrayInformacion
     * 
    */    

    public function getInfoTelImeiAsignado($arrayParametros)
    {               
        $objUtilService = $arrayParametros['objUtilService'];
        $arrayInformacion = array();
        try
        {
            $strSQL = "SELECT b.nombre_elemento VALOR
                            FROM db_infraestructura.info_detalle_elemento a,
                            db_infraestructura.info_elemento b
                            WHERE a.elemento_id = b.id_elemento 
                            AND a.detalle_valor = :idPersonaEmpresaRol 
                            AND a.detalle_nombre = :detalleNombre
                            AND a.estado = 'Activo'
                            AND b.estado = 'Activo'";

            $objStmt = $this->_em->getConnection()->prepare($strSQL);
            $objStmt->bindValue('idPersonaEmpresaRol', $arrayParametros['idPersonaEmpresaRol']);
            $objStmt->bindValue('detalleNombre', $arrayParametros['detalleNombre']);
            $objStmt->execute();

            $arrayInformacion = $objStmt->fetchAll();      
        }
        catch(\Exception $e)
        {
            $objUtilService->insertError('Telcos+',
            'InfoPersonaRepository->getInfoTelImeiAsignado',
            $e->getMessage(),
                        'Telcos+',
                        '');
            throw($e);
        }
        return $arrayInformacion[0]['VALOR'];    
    }

     /**
     *
     * Metodo encargado de retornar el estado del login en el NAF
     *
     * @author Richard Cabrera 1.0 10-03-2021
     * Costo query: 4
     *
     * @param  array $arrayParametros [ 'strLogin'       => login del servicio
     *                                  'intCodEmpresa   => codigo de la empresa
     *                                  'objUtilService' => service Util ]
     * @return array $arrayRespuesta
     *
     */
    public function getEstadoNafPorServicio($arrayParametros)
    {
        $arrayRespuesta = array();
        $objUtilService = $arrayParametros['objUtilService'];

        try
        {
            $strSql = " SELECT EMPLE.ESTADO AS ESTADO_NAF
                            FROM NAF47_TNET.V_EMPLEADOS_EMPRESAS EMPLE
                                WHERE EMPLE.LOGIN_EMPLE = LOWER(:strUser)
                                AND EMPLE.NO_CIA = :intIdEmp ";

            $objStmt = $this->_em->getConnection()->prepare($strSql);
            $objStmt->bindValue('strUser', $arrayParametros['strLogin']);
            $objStmt->bindValue('intIdEmp', $arrayParametros['intCodEmpresa']);
            $objStmt->execute();

            $arrayRespuesta = $objStmt->fetchAll();
        }
        catch(\Exception $e)
        {
            $objUtilService->insertError('Telcos+','InfoPersonaRepository->getEstadoNafPorServicio',$e->getMessage(),'Telcos+','');

            throw($e->getMessage());
        }

        return $arrayRespuesta[0]['ESTADO_NAF'];
    }

    /**
     * 
     * Metodo encargado de retornar la información del empleado de las tablas de naf.
     * 
     * @author David León <mdleon@telconet.ec>
     * @version 1.0 01-06-2020
     * Costo query: 8
     * 
     * @param  array $arrayParametros
     * @return array $arrayRegistros
     * 
     */
    public function getDataEmpleados($arrayParametros)
    {
        $objRsm = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsm);
        

        $strIndentificacion        = $arrayParametros['strIndentificacion'];

        
            $strSql = "select CEDULA,
                        NOMBRE,
                        NOMBRE_PILA,
                        NOMBRE_SEGUNDO,
                        APE_PAT,
                        APE_MAT,
                        DIRECCION,
                        F_NACIMI, 
                        SEXO ,
                        MAIL_CIA,
                        TELEFONO,
                        CELULAR,
                        NO_CIA,
                        (FLOOR(MONTHS_BETWEEN(sysdate, f_nacimi ) / 12)) EDAD,
                        (SELECT LOGIN FROM DB_COMERCIAL.INFO_PERSONA WHERE IDENTIFICACION_CLIENTE = cedula and rownum = 1) LOGIN
                        from NAF47_TNET.ARPLME where cedula = :identificacion and estado = :estado
              ";

               
        $objQuery->setParameter("estado", 'A');
        $objQuery->setParameter("identificacion", $strIndentificacion);     

        
        $objRsm->addScalarResult(strtoupper('CEDULA'), 'cedula', 'string');
        $objRsm->addScalarResult(strtoupper('NOMBRE'), 'nombre', 'string');
        $objRsm->addScalarResult(strtoupper('NOMBRE_PILA'), 'nombre1', 'string');
        $objRsm->addScalarResult(strtoupper('NOMBRE_SEGUNDO'), 'nombre2', 'string');
        $objRsm->addScalarResult(strtoupper('APE_PAT'), 'apellido1', 'string');
        $objRsm->addScalarResult(strtoupper('APE_MAT'), 'apellido2', 'string');
        $objRsm->addScalarResult(strtoupper('DIRECCION'), 'direccion', 'string');
        $objRsm->addScalarResult(strtoupper('F_NACIMI'), 'fechaNacimiento', 'date');
        $objRsm->addScalarResult(strtoupper('SEXO'), 'genero', 'string');
        $objRsm->addScalarResult(strtoupper('MAIL_CIA'), 'correo', 'string');        
        $objRsm->addScalarResult(strtoupper('TELEFONO'), 'telefono', 'string');
        $objRsm->addScalarResult(strtoupper('CELULAR'), 'celular', 'string');        
        $objRsm->addScalarResult(strtoupper('NO_CIA'), 'empresa', 'string');
        $objRsm->addScalarResult(strtoupper('EDAD'), 'edad', 'string');
        $objRsm->addScalarResult(strtoupper('LOGIN'), 'login', 'string');
        
        $objQuery->setSQL($strSql);
        
        $arrayRegistros = $objQuery->getResult();
        
        return $arrayRegistros;
    }
    
    /**
     * 
     * Se consulta los logines a regularizarce el Upgrade.
     * 
     * @author David León <mdleon@telconet.ec>
     * @version 1.0 09-06-2020
     * Costo query: 25
     *
     * @return array $arrayRegistros
     * 
     */
    public function getDatosUpdateBackup()
    {
        $objRsm = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsm);
        

        
            $strSql = "select SERIE,SERIE_N 
                from naf47_tnet.INV_REGULARIZACION_SERIES 
                where compania=:empresa
                and estado=:estado 
                and usuario_creacion=:user
              ";

        $objQuery->setParameter("empresa", '10');       
        $objQuery->setParameter("estado", 'P');
        $objQuery->setParameter("user", 'mdleon');     

        
        $objRsm->addScalarResult(strtoupper('SERIE'), 'login_aux', 'string');
        $objRsm->addScalarResult(strtoupper('SERIE_N'), 'aumento', 'string');
        
        $objQuery->setSQL($strSql);
        
        $arrayRegistros = $objQuery->getResult();
        
        return $arrayRegistros;
    }
         
    /**
     * Documentación para getEdadPersona
     * 
     * Función que obtiene la edad en años de una persona
     * 
     * @param array $arrayParametros['intIdPersona'  => 'Id de la Persona'                                   
     *                              ]
     * 
     * @return Retorna la edad en años de una persona
     * 
     * @author Anabelle Peñaherrera<apenaherrera@telconet.ec>
     * 
     * @version 1.0 22-02-2021
     */
    public function getEdadPersona($arrayParametros)
    {
        $intEdadPersona = 0;        
        try
        {
            if( !empty($arrayParametros) )
            {               
                $intIdPersona  = (isset($arrayParametros["intIdPersona"])
                                 && !empty($arrayParametros["intIdPersona"])) 
                                 ? $arrayParametros["intIdPersona"] : 0;
                                
                $intEdadPersona = str_pad($intEdadPersona, 50, " ");
                
                $strSql = "BEGIN :intEdadPersona := DB_COMERCIAL.CMKG_BENEFICIOS.F_EDAD_PERSONA (" .
                                                                    ":intIdPersona); END;";
                                                               
                $objStmt = $this->_em->getConnection()->prepare($strSql);
                $objStmt->bindParam('intIdPersona', $intIdPersona);               
                $objStmt->bindParam('intEdadPersona', $intEdadPersona);
                $objStmt->execute();
            }
        }
        catch(\Exception $ex)
        {
           throw($ex);
        }
        
        return $intEdadPersona;
    }
    
    /**
     * Documentación para la función 'getPersonaCRM'.
     *
     * Función que retorna los clientes para el ingreso de cuentas en TelcoCRM.
     *
     * @param array $arrayParametros [
     *                                  "strPrefijoEmpresa"     => Prefijo de la empresa.
     *                                  "strCodEmpresa"         => Código de la empresa.
     *                                  "strIdentificacion"     => Identificación del cliente a buscar.
     *                               ]
     *
     * @return array $arrayRespuesta [
     *                                 "infoCliente"    =>  arreglo de la información del cliente.
     *                                 "infoVendedores" =>  vendedores asociados al punto.
     *                                 "error"          =>  mensaje de error.
     *                               ]
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 11-06-2020
     * 
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.1 09-05-2021 - Se agrega lógica para retornar si el cliente es un distribuidor.
     *
     */
    public function getPersonaCRM($arrayParametros)
    {
        $strPrefijoEmpresa       = $arrayParametros['strPrefijoEmpresa'] ? $arrayParametros['strPrefijoEmpresa']:"";
        $strCodEmpresa           = $arrayParametros['strCodEmpresa'] ? $arrayParametros['strCodEmpresa']:"";
        $strIdentificacion       = $arrayParametros['strIdentificacion'] ? $arrayParametros['strIdentificacion']:"";
        $strSelect               = '';
        $strFrom                 = '';
        $strWhere                = '';
        $strMensajeError         = '';

        try
        {
            if(empty($strPrefijoEmpresa) || empty($strCodEmpresa) || empty($strIdentificacion))
            {
                throw new \Exception("El prefijo, código y la identificación son obligatorios para realizar la búsqueda del cliente.");
            }
            if($strPrefijoEmpresa != 'TN')
            {
                throw new \Exception("La consulta de clientes o pre-clientes, solo aplica para Telconet");
            }

            $objRsmb      = new ResultSetMappingBuilder($this->_em);
            $objQuery     = $this->_em->createNativeQuery(null, $objRsmb);
            $objRsmbVend  = new ResultSetMappingBuilder($this->_em);
            $objQueryVend = $this->_em->createNativeQuery(null, $objRsmbVend);

            $strSelect     = " SELECT
                                    IPE.TIPO_EMPRESA,
                                    IPE.TIPO_IDENTIFICACION,
                                    IPE.TIPO_TRIBUTARIO,
                                    IPE.NACIONALIDAD,
                                    IPE.IDENTIFICACION_CLIENTE,
                                    IPE.NOMBRES,
                                    IPE.APELLIDOS,
                                    IPE.TITULO_ID,
                                    IPE.DIRECCION_TRIBUTARIA,
                                    IPE.GENERO,
                                    IPE.ESTADO_CIVIL,
                                    IPE.FECHA_NACIMIENTO,
                                    IPE.RAZON_SOCIAL,
                                    IPE.REPRESENTANTE_LEGAL,
                                    AR.DESCRIPCION_ROL,
                                    IOG.ID_OFICINA,
                                    IOG.NOMBRE_OFICINA,
                                    IPE.ESTADO,
                                    IPE.NUMERO_CONADIS,
                                    IPE.DIRECCION,
                                    IPE.CONTRIBUYENTE_ESPECIAL,
                                    IPE.ORIGEN_INGRESOS,
                                    IPE.PAGA_IVA,
                                    apd.valor1 as HOLDING,
                                    CASE
                                        WHEN IPERC_DIST.VALOR IS NULL THEN
                                            'NO'
                                        ELSE
                                            IPERC_DIST.VALOR
                                    END AS DISTRIBUIDOR ";
            $strFrom       = " FROM info_persona                    ipe
                                    JOIN info_persona_empresa_rol   iper ON iper.persona_id    = ipe.id_persona
                                    JOIN info_empresa_rol            ier ON ier.id_empresa_rol = iper.empresa_rol_id
                                    JOIN admi_rol                     ar ON ar.id_rol          = ier.rol_id 
                                    JOIN info_oficina_grupo          iog ON iog.id_oficina=iper.oficina_id 
                                    LEFT JOIN ADMI_CARACTERISTICA ac on ac.DESCRIPCION_CARACTERISTICA=:strHolding
                                    LEFT JOIN INFO_PERSONA_EMPRESA_ROL_CARAC iperc on iperc.persona_empresa_rol_id=iper.id_persona_rol and 
                                            iperc.CARACTERISTICA_ID=ac.ID_CARACTERISTICA
                                    LEFT JOIN ADMI_PARAMETRO_DET apd on apd.ID_PARAMETRO_DET= iperc.valor
                                    LEFT JOIN admi_caracteristica            ac_dist    ON ac_dist.descripcion_caracteristica = :strDistribuidor
                                    LEFT JOIN info_persona_empresa_rol_carac iperc_dist ON iperc_dist.persona_empresa_rol_id  = iper.id_persona_rol
                                                                             AND iperc_dist.caracteristica_id = ac_dist.id_caracteristica ";

            $strWhere      = " WHERE ipe.identificacion_cliente = :strIdentificacion
                                     AND LOWER(ar.descripcion_rol) IN ('cliente','pre-cliente')
                                     AND ier.empresa_cod=:strCodEmpresa
                                     AND iper.estado IN (
                                        'Modificado',
                                        'Pendiente',
                                        'Activo'
                                    )";

            $strSelectVend  = " SELECT distinct ipu.USR_VENDEDOR ";
            $strFromVend    = $strFrom." JOIN info_punto ipu on ipu.persona_empresa_rol_id=iper.id_persona_rol ";
            $strSqlVend     = $strSelectVend . $strFromVend . $strWhere;

            $strSql         = $strSelect . $strFrom . $strWhere;

            $objQuery->setParameter('strIdentificacion', $strIdentificacion, 'string');
            $objQuery->setParameter('strCodEmpresa', $strCodEmpresa, 'string');
            $objQuery->setParameter('strHolding', 'HOLDING EMPRESARIAL', 'string');
            $objQuery->setParameter('strDistribuidor', 'ES_DISTRIBUIDOR', 'string');
            
            $objQueryVend->setParameter('strIdentificacion', $strIdentificacion, 'string');
            $objQueryVend->setParameter('strCodEmpresa', $strCodEmpresa, 'string');
            $objQueryVend->setParameter('strHolding', 'HOLDING EMPRESARIAL', 'string');
            $objQueryVend->setParameter('strDistribuidor', 'ES_DISTRIBUIDOR', 'string');

            $objRsmbVend->addScalarResult('USR_VENDEDOR','vendedor','string');

            $objRsmb->addScalarResult('TIPO_EMPRESA','tipoEmpresa','string');
            $objRsmb->addScalarResult('TIPO_IDENTIFICACION','tipoIdentificacion','string');
            $objRsmb->addScalarResult('TIPO_TRIBUTARIO','tipoTributario','string');
            $objRsmb->addScalarResult('NACIONALIDAD','nacionalidad','string');
            $objRsmb->addScalarResult('IDENTIFICACION_CLIENTE','identificacionCliente','string');
            $objRsmb->addScalarResult('NOMBRES','nombres','string');
            $objRsmb->addScalarResult('APELLIDOS','apellidos','string');
            $objRsmb->addScalarResult('TITULO_ID','tituloId','string');
            $objRsmb->addScalarResult('DIRECCION_TRIBUTARIA','direccionTributaria','string');
            $objRsmb->addScalarResult('GENERO','genero','string');
            $objRsmb->addScalarResult('ESTADO_CIVIL','estadoCivil','string');
            $objRsmb->addScalarResult('FECHA_NACIMIENTO','fechaNacimiento','string');
            $objRsmb->addScalarResult('RAZON_SOCIAL','razonSocial','string');
            $objRsmb->addScalarResult('REPRESENTANTE_LEGAL','representanteLegal','string');
            $objRsmb->addScalarResult('DESCRIPCION_ROL','rol','string');
            $objRsmb->addScalarResult('ID_OFICINA','oficinaId','string');
            $objRsmb->addScalarResult('NOMBRE_OFICINA','nombre_oficina','string');
            $objRsmb->addScalarResult('ESTADO','estado','string');
            $objRsmb->addScalarResult('NUMERO_CONADIS','strConadis','string');
            $objRsmb->addScalarResult('DIRECCION','direccion','string');
            $objRsmb->addScalarResult('CONTRIBUYENTE_ESPECIAL','strContribuyente','string');
            $objRsmb->addScalarResult('ORIGEN_INGRESOS','origenIngresos','string');
            $objRsmb->addScalarResult('PAGA_IVA','gravaIva','string');
            $objRsmb->addScalarResult('HOLDING','holding','string');
            $objRsmb->addScalarResult('DISTRIBUIDOR','distribuidor','string');

            $objQuery->setSQL($strSql);
            $objQueryVend->setSQL($strSqlVend);

            $arrayResultado = $objQuery->getResult();
            $arrayResultadoVend = $objQueryVend->getResult();
            $intTotal       = count($arrayResultado);

            $arrayRespuesta['infoCliente']    = $arrayResultado;
            $arrayRespuesta['infoVendedores'] = $arrayResultadoVend;
            $arrayRespuesta['total']          = $intTotal;
        }
        catch(\Exception $ex)
        {
            $strMensajeError = $ex->getMessage();
        }
        $arrayRespuesta['error'] = $strMensajeError;

        return $arrayRespuesta;
    }

    /**
     * Documentación para el método 'getDatosUsuarioNaf'.
     *
     * Retorna un arreglo con los datos del usuario obtenido desde NAF
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 12-06-2020
     *
     * @param Array $arrayParametros [
     *                                  'strLogin' => login del usuario
     *                                  'intIdEmp' => id de la empresa
     *                               ]
     *
     * @return Array $arrayResultado [
     *                                  'status'    => estado de respuesta de la operación 'OK' o 'ERROR',
     *                                  'result'    => arreglo con la información de los Ingenieros VIP o mensaje de error
     *                               ]
     *
     * costoQuery: 4
     */
    public function getDatosUsuarioNaf($arrayParametros)
    {
        $objUtilService = $arrayParametros['objUtilService'];
        try
        {
            $objMappingBuilder = new ResultSetMappingBuilder($this->_em);
            $objNativeQuery    = $this->_em->createNativeQuery(null, $objMappingBuilder);
            $strSQL = " SELECT LOGIN_EMPLE, MAIL_CIA, NOMBRE_DEPTO FROM NAF47_TNET.V_EMPLEADOS_EMPRESAS EMPLE 
                        WHERE EMPLE.LOGIN_EMPLE = LOWER(:strUser) 
                            AND EMPLE.ESTADO = 'A' 
                            AND EMPLE.NO_CIA= :intIdEmp";

            $objNativeQuery->setParameter('strUser',  $arrayParametros['strLogin']);
            $objNativeQuery->setParameter('intIdEmp', $arrayParametros['intIdEmp']);
            $objMappingBuilder->addScalarResult('LOGIN_EMPLE',  'loginEmp',    'string');
            $objMappingBuilder->addScalarResult('MAIL_CIA',     'mailCia',     'string');
            $objMappingBuilder->addScalarResult('NOMBRE_DEPTO', 'nombreDepto', 'string');
            $objNativeQuery->setSQL($strSQL);

            $arrayDatosEmpleado = $objNativeQuery->getOneOrNullResult();
            $arrayResult = array(
                'status' => 'OK',
                'result' => $arrayDatosEmpleado
            );
        }
        catch(\Exception $e)
        {
            $arrayResult = array(
                'status' => 'ERROR',
                'result' => $e->getMessage()
            );
            $objUtilService->insertError('Telcos+',
			                 'InfoPersonaRepository.getDatosUsuarioNaf',
			                 $e->getMessage(),
                                         'Telcos+',
                                         '');
        }
        return $arrayResult;
    }        

    /**
     * 
     * Metodo encargado de retornar el login a quien reporta el empleado.
     * 
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.0 19-01-2021
     * Costo query: 14
     * 
     * @param  string $strUsuario
     * @return array  $arrayCargo
     * 
     */    
    public function getCargosReportaEmp($strUsuario)
    {
        $strEstadoActivo = 'Activo';
        $arrayCargo      = array();
        try
        {

            $strSql = "SELECT IP2.login as login FROM DB_COMERCIAL.INFO_PERSONA IP2,DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL  IPER2 WHERE 
                       IP2.ID_PERSONA=IPER2.PERSONA_ID AND IPER2.ID_PERSONA_ROL=(
                        SELECT distinct IPER.REPORTA_PERSONA_EMPRESA_ROL_ID FROM DB_COMERCIAL.INFO_PERSONA IP,
                        DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPER
                        where IP.ID_PERSONA = IPER.PERSONA_ID
                        AND IP.ESTADO = :strEstadoActivo
                        AND IPER.ESTADO = :strEstadoActivo
                        AND IPER.REPORTA_PERSONA_EMPRESA_ROL_ID IS NOT NULL
                        AND IP.LOGIN = :strUsuario)";
            $objStmt = $this->_em->getConnection()->prepare($strSql);
            $objStmt->bindValue('strEstadoActivo',$strEstadoActivo);
            $objStmt->bindValue('strUsuario',$strUsuario);
            $objStmt->execute();

            $arrayCargo = $objStmt->fetchAll();
        }
        catch(\Exception $ex)
        {
            $strMensajeError = $ex->getMessage();
        }
        $arrayCargo['error'] = $strMensajeError;
        return $arrayCargo;
    }    

    /**
     * Función que retorna el listado de vendedores por login de asistente.
     *
     * @param Array $arrayParametros [
     *                                 strLoginUsuario : Login del vendedor.
     *                                 strCodEmpresa   : Código de la empresa.
     *                               ]
     *
     * @return Array $arrayRespuesta
     *
     * Costo query: 143
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 17-06-2021
     */
    public function getAsistentePorVendedores($arrayParametros)
    {
        $strLoginUsuario = $arrayParametros['strLoginUsuario'];
        $strCodEmpresa   = $arrayParametros['strCodEmpresa'];

        try
        {
            if (empty($strLoginUsuario) || empty($strCodEmpresa))
            {
                throw new \Exception("El login del usuario y el código de la empresa ".
                                     "son obligatorios para realizar la consulta.");
            }

            $objRsmb  = new ResultSetMappingBuilder($this->_em);
            $objQuery = $this->_em->createNativeQuery(null, $objRsmb);

            $strSql   = "SELECT          ".
                         "IPE.NOMBRES,   ".
                         "IPE.APELLIDOS, ".
                         "IPE.LOGIN,     ".
                         "IPE.ESTADO     ".
                        "FROM ".
                         "DB_COMERCIAL.INFO_PERSONA IPE ".
                         "JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL       IPER ON IPER.PERSONA_ID               = IPE.ID_PERSONA      ".
                         "JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC IPERC ON IPERC.PERSONA_EMPRESA_ROL_ID = IPER.ID_PERSONA_ROL ".
                         "JOIN DB_COMERCIAL.ADMI_CARACTERISTICA            ACA ON ACA.ID_CARACTERISTICA    = IPERC.CARACTERISTICA_ID   ".
                         "JOIN DB_COMERCIAL.INFO_EMPRESA_ROL               IER ON IER.ID_EMPRESA_ROL       = IPER.EMPRESA_ROL_ID       ".
                         "JOIN DB_COMERCIAL.INFO_PERSONA                   IPE_VEND ON IPE_VEND.ID_PERSONA = IPERC.VALOR               ".
                        "WHERE ".
                         "IPE_VEND.LOGIN      = :strLoginUsuario ".
                         "AND IER.EMPRESA_COD = :strCodEmpresa   ".
                         "AND IPE.ESTADO     IN (:arrayEstado)   ".
                         "AND IPER.ESTADO     = :strEstadoActivo ".
                         "AND IPERC.ESTADO    = :strEstadoActivo ".
                         "AND ACA.ESTADO      = :strEstadoActivo ".
                         "AND ACA.DESCRIPCION_CARACTERISTICA = :strCaracteristica";

            $objQuery->setParameter('strLoginUsuario'   ,  $strLoginUsuario);
            $objQuery->setParameter('strCodEmpresa'     ,  $strCodEmpresa);
            $objQuery->setParameter('arrayEstado'       ,  array('Activo','Pendiente'));
            $objQuery->setParameter('strEstadoActivo'   , 'Activo');
            $objQuery->setParameter('strCaracteristica' , 'ASISTENTE_POR_CARGO');

            $objRsmb->addScalarResult('LOGIN'     , 'login'     , 'string');
            $objRsmb->addScalarResult('NOMBRES'   , 'nombres'   , 'string');
            $objRsmb->addScalarResult('APELLIDOS' , 'apellidos' , 'string');
            $objRsmb->addScalarResult('ESTADO'    , 'estado'    , 'string');
            $objQuery->setSQL($strSql);
            $arrayRespuesta = array('asistente' => $objQuery->getResult());
        }
        catch(\Exception $objException)
        {
            $arrayRespuesta = array('error' => $objException->getMessage());
        }
        return $arrayRespuesta;
    }
 /**
     * Funcion que sirve para obtener los datos y departamento del usuario por empresa
     * 
     * @author Wilmer Vera G. <wvera@telconet.ec>
     * @version 1.0 29-03-2022
     * @param $strLoginCliente
     * 
     */
    public function getDatosClientePorLogin($strLoginCliente)
    {
        try
        {
            $objRsmb  = new ResultSetMappingBuilder($this->_em);
            $objQuery = $this->_em->createNativeQuery(null, $objRsmb);
            $strSql = "
                SELECT
                    PER.ID_PERSONA,
                    per.tipo_identificacion,
                    PER.IDENTIFICACION_CLIENTE,
                    per.tipo_empresa,
                    per.tipo_tributario,
                    PER.NOMBRES,
                    PER.APELLIDOS,
                    PER.ESTADO AS estado_persona,
                    PER.RAZON_SOCIAL,
                    per.representante_legal,
                    pto.usr_vendedor,
                    pto.usr_cobranzas,
                    pto.id_punto,
                    pto.login,
                    pto.estado,
                    rol.id_persona_rol,
                    rol.empresa_rol_id,
                    rol.estado AS rol_estado
                FROM
                    DB_COMERCIAL.INFO_PUNTO pto,
                    DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL rol,
                    DB_COMERCIAL.INFO_PERSONA per
                WHERE
                    PTO.PERSONA_EMPRESA_ROL_ID = ROL.ID_PERSONA_ROL
                    AND ROL.PERSONA_ID = PER.ID_PERSONA
                    AND pto.login = :loginCliente ";
            
            $objQuery->setParameter('loginCliente'   ,  $strLoginCliente);
            $objRsmb->addScalarResult('ID_PERSONA' , 'idPersona'     , 'string');
            $objRsmb->addScalarResult('IDENTIFICACION_CLIENTE' , 'identificacionCliente'   , 'string');
            $objRsmb->addScalarResult('NOMBRES' , 'nombres' , 'string');
            $objRsmb->addScalarResult('APELLIDOS' , 'apellidos' , 'string');
            $objRsmb->addScalarResult('RAZON_SOCIAL' , 'razonsocial' , 'string');
            $objQuery->setSQL($strSql);
            $arrayRespuesta = $objQuery->getResult();
        }
        catch(\Exception $objException)
        {
            $arrayRespuesta = array('error' => $objException->getMessage());
        }    
        return $arrayRespuesta;
    }
    /**
     * Función que retorna si el usuario es de una cuadrilla Hal.
     *
     * @param Array $arrayParametros [
     *                                 strLoginUsuario : Login del vendedor.
     *                               ]
     *
     * @return boolean $boolEsHal
     *
     * Costo query: 143
     *
     * @author Pedro Velez <psvelez@telconet.ec>
     * @version 1.0 10-09-2021
     */
    public function isHalTraking($arrayParametros)
    {
        $strEsHal = "";
        $boolEsHal = false;
        try
        {
            $objRsmb  = new ResultSetMappingBuilder($this->_em);
            $objQuery = $this->_em->createNativeQuery(null, $objRsmb);

            $strSql   = " SELECT C.ES_HAL ".
                         " FROM DB_COMERCIAL.INFO_PERSONA S, ".
                              " DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL A, ".
                              " DB_COMERCIAL.ADMI_CUADRILLA C ".
                        " WHERE S.ID_PERSONA = A.PERSONA_ID ".
                          " AND C.ID_CUADRILLA = A.CUADRILLA_ID ".
                          " AND S.LOGIN  = :strLogin ".
                          " AND S.ESTADO = 'Activo' ".
                          " AND A.ESTADO = 'Activo' ".
                          " AND C.ESTADO = 'Activo' ";

            $objQuery->setParameter('strLogin' ,  $arrayParametros['strLoginUsuario']);
            $objRsmb->addScalarResult('ES_HAL' , 'esHal' , 'string');
            $objQuery->setSQL($strSql);

            $strEsHal = $objQuery->getSingleScalarResult();
            if ($strEsHal=== "S")
            {
                $boolEsHal = true;
            }
        }
        catch(\Exception $ex)
        {
            $boolEsHal = false;
        }
        return $boolEsHal;
    }

    /**
     * 
     * Metodo encargado de retornar el mail personal del empelado en NAF según el login que recibe por parámetro
     * 
     * @author Byron Anton <banton@telconet.ec>
     * @version 1.0 05-01-2022
     * Costo query: 4
     * 
     * @param  array $arrayParametros
     * @return array $arrayInformacion
     * 
     */    
    public function getEmpleadoNafById($arrayParametros)
    {               
        $arrayInformacion = array();
        $objUtilService = $arrayParametros['objUtilService'];
        try
        {
            $strSelect =" SELECT LOGIN_EMPLE , MAIL,(SELECT PREFIJO FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
                                                     WHERE COD_EMPRESA=:strCodEmpresa) PREFIJO";
            $strFrom   =" FROM NAF47_TNET.V_EMPLEADOS_EMPRESAS EMPLE";
            $strWhere  =" WHERE EMPLE.NO_EMPLE = :strNoEmple
                          AND EMPLE.ESTADO = 'A' 
                          AND EMPLE.NO_CIA= :noCia";
            
            $strSql = $strSelect.$strFrom.$strWhere;
            $objStmt = $this->_em->getConnection()->prepare($strSql);
            $objStmt->bindValue('strCodEmpresa', $arrayParametros['noCia']);
            $objStmt->bindValue('strNoEmple', $arrayParametros['strNoEmple']);
            $objStmt->bindValue('noCia', $arrayParametros['noCia']);
            $objStmt->execute();

            $arrayInformacion = $objStmt->fetchAll();       
            
        }
        catch(\Exception $e)
        {
            $objUtilService->insertError('Telcos+',
			                  'InfoPersonaRepository->getMailPersonaNaf',
			                  $e->getMessage(),
                                          'Telcos+',
                                          '');
            throw($e);
        }
       
        return $arrayInformacion;
    }
    
    /**
     * getResultadoPersonasPorParametros
     *
     * Función que obtiene el listado de personas que cumplan con los parámetros enviados               
     *
     * @param array $arrayParametros [
     *                                  "nombrePerfilAsignado"  => nombre del perfil asignado a  las personas a buscar
     *                                  "descripcionTipoRol"    => descripción del tipo de rol
     *                                  "codEmpresa"            => id de la empresa
     *                                  "nombrePersona"         => nombres de la persona a buscar
     *                              ]
     * 
     * @return array $arrayRespuesta
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 08-04-2022
     * 
     */
    public function getResultadoPersonasPorParametros($arrayParametros)
    {
        $arrayRespuesta['total']        = 0;
        $arrayRespuesta['encontrados']  = "";
        try
        {
            $objRsm = new ResultSetMappingBuilder($this->_em);
            $objRsmCount = new ResultSetMappingBuilder($this->_em);
            $objNtvQuery = $this->_em->createNativeQuery(null, $objRsm);
            $objNtvQueryCount = $this->_em->createNativeQuery(null, $objRsmCount);

            $strSelectCount = " SELECT COUNT(*) AS TOTAL ";
            $strSelect      = " SELECT DISTINCT PERSONA.ID_PERSONA, PERSONA.NOMBRES, PERSONA.APELLIDOS, "
                            . " CONCAT(CONCAT(PERSONA.NOMBRES, ' '), PERSONA.APELLIDOS) AS NOMBRES_PERSONA ";   
            $strFrom        = " FROM DB_COMERCIAL.INFO_PERSONA PERSONA 
                                INNER JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL PER
                                ON PERSONA.ID_PERSONA = PER.PERSONA_ID 
                                INNER JOIN DB_COMERCIAL.INFO_EMPRESA_ROL EMPRESA_ROL
                                ON EMPRESA_ROL.ID_EMPRESA_ROL = PER.EMPRESA_ROL_ID
                                INNER JOIN DB_COMERCIAL.INFO_EMPRESA_GRUPO EMPRESA_GRUPO
                                ON EMPRESA_GRUPO.COD_EMPRESA  = EMPRESA_ROL.EMPRESA_COD
                                INNER JOIN DB_GENERAL.ADMI_ROL ROL
                                ON ROL.ID_ROL = EMPRESA_ROL.ROL_ID
                                INNER JOIN DB_GENERAL.ADMI_TIPO_ROL TIPO_ROL
                                ON TIPO_ROL.ID_TIPO_ROL = ROL.TIPO_ROL_ID ";
            $strWhere       = " WHERE PERSONA.ESTADO <> :estadoEliminado
                                AND PER.ESTADO <> :estadoEliminado
                                AND EMPRESA_ROL.ESTADO <> :estadoEliminado
                                AND ROL.ESTADO <> :estadoEliminado 
                                AND EMPRESA_GRUPO.COD_EMPRESA = :codEmpresa ";

            $objRsm->addScalarResult('ID_PERSONA', 'id_empleado', 'integer');
            $objRsm->addScalarResult('NOMBRES', 'nombres', 'string');
            $objRsm->addScalarResult('APELLIDOS', 'apellidos', 'string');
            $objRsm->addScalarResult('NOMBRES_PERSONA', 'nombre_empleado', 'string');
            $objRsmCount->addScalarResult('TOTAL', 'total', 'integer');
            
            $objNtvQuery->setParameter('codEmpresa', $arrayParametros['codEmpresa']);
            $objNtvQueryCount->setParameter('codEmpresa', $arrayParametros['codEmpresa']);
            
            $objNtvQuery->setParameter('estadoEliminado', 'Eliminado');
            $objNtvQueryCount->setParameter('estadoEliminado', 'Eliminado');
            
            if(isset($arrayParametros["nombrePerfilAsignado"]) && !empty($arrayParametros["nombrePerfilAsignado"]))
            {
                $strFrom    .= "INNER JOIN DB_SEGURIDAD.SEGU_PERFIL_PERSONA SEGU_PERFIL_PERSONA
                                ON SEGU_PERFIL_PERSONA.PERSONA_ID = PERSONA.ID_PERSONA 
                                INNER JOIN DB_SEGURIDAD.SIST_PERFIL PERFIL
                                ON PERFIL.ID_PERFIL = SEGU_PERFIL_PERSONA.PERFIL_ID ";
                
                $strWhere .= "AND PERFIL.NOMBRE_PERFIL = :nombrePerfilAsignado ";
                $objNtvQuery->setParameter('nombrePerfilAsignado', $arrayParametros["nombrePerfilAsignado"]);
                $objNtvQueryCount->setParameter('nombrePerfilAsignado', $arrayParametros["nombrePerfilAsignado"]);
            }
            
            if(isset($arrayParametros["descripcionTipoRol"]) && !empty($arrayParametros["descripcionTipoRol"]))
            {
                $strWhere .= "AND TIPO_ROL.DESCRIPCION_TIPO_ROL = :descripcionTipoRol ";
                $objNtvQuery->setParameter('descripcionTipoRol', $arrayParametros["descripcionTipoRol"]);
                $objNtvQueryCount->setParameter('descripcionTipoRol', $arrayParametros["descripcionTipoRol"]);
            }
            
            if(isset($arrayParametros["nombrePersona"]) && !empty($arrayParametros["nombrePersona"]))
            {
                $strWhere .= "AND CONCAT(PERSONA.NOMBRES, CONCAT(' ', PERSONA.APELLIDOS)) LIKE :nombrePersona ";
                $objNtvQuery->setParameter('nombrePersona', '%'.strtoupper($arrayParametros["nombrePersona"]).'%');
                $objNtvQueryCount->setParameter('nombrePersona', '%'.strtoupper($arrayParametros["nombrePersona"]).'%');
            }
            
            $strOrderBy =" ORDER BY PERSONA.NOMBRES, PERSONA.APELLIDOS ";
            
            $strSqlPrincipal = $strSelect . $strFrom .  $strWhere . $strOrderBy;

            $objNtvQuery->setSQL($strSqlPrincipal);
            $arrayResultado = $objNtvQuery->getResult();
            
            $strSqlCount = $strSelectCount . " FROM (" . $strSqlPrincipal . ")";
            $objNtvQueryCount->setSQL($strSqlCount);

            $intTotal = $objNtvQueryCount->getSingleScalarResult();

            $arrayRespuesta['encontrados']  = $arrayResultado;
            $arrayRespuesta['total']        = $intTotal;
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
        }
        return $arrayRespuesta;
    }

   /**
    * getDatosPerPorLoginEmpresa
    *
    * Metodo encargado de obtener la información de la personaEmpresaRol asociada a un login.
    *
    * @param array $arrayParametros
    *
    * @return object $objResultado
    *
    * @author Walther Joao Gaibor <wgaibor@telconet.ec>
    * @version 1.0 26-04-2022
    *
    */
    public function getDatosPerPorLoginEmpresa($arrayParametros)
    {
        $arrayResultados = array();
        $objResultado    = null;

        $objQuery = $this->_em->createQuery();

        $strSelect = 'SELECT iper ';
        $strFrom   = 'FROM schemaBundle:InfoPersona ip,
                            schemaBundle:InfoPersonaEmpresaRol iper,
                            schemaBundle:InfoOficinaGrupo iogr ';
        $strWhere  = 'WHERE ip.id = iper.personaId
                        AND iper.oficinaId = iogr.id
                        AND ip.login = :login
                        AND iogr.empresaId = :empresaId
                        AND LOWER(ip.estado) = LOWER(:estadoActivoPer)
                        AND LOWER(iper.estado) = LOWER(:estadoActivoErol) ';

        $objQuery->setParameter('login'             , $arrayParametros['strLogin']);
        $objQuery->setParameter('empresaId'         , $arrayParametros['strCodEmpresa']);
        $objQuery->setParameter('estadoActivoPer'   , 'Activo');
        $objQuery->setParameter('estadoActivoErol'  , 'Activo');

        $strQuery = $strSelect.$strFrom.$strWhere;

        $objQuery->setDQL($strQuery);

        $arrayResultados = $objQuery->setMaxResults(1)->getResult();

        if( $arrayResultados )
        {
            $objResultado = $arrayResultados[0];
        }
        return $objResultado;
    }
    /**
     * Envia correo a los clientes que solicitan Derechos del titular de Portabilidad
     * @param  string $strCorreo
     * @param  string $strIdentificacion
     * @return string $strMensaje
     *
     * @author Jessenia Piloso <jpiloso@telconet.ec>
     * @version 1.0 30-11-2022
     */
    public function enviarCorreoServiciosPersona($strCorreo, $strIdentificacion)
    {
        $strMensaje = str_pad('', 10000, " ");
        $strSql     = 'BEGIN DB_COMERCIAL.COMEK_CONSULTAS.P_GET_SERVICIO_ENVIO_CORREO(:strIdentificacion, :strCorreo, :strMensaje); END;';
        try
        {
            $strStmt    = $this->_em->getConnection()->prepare($strSql);
            $strStmt->bindParam('strCorreo', $strCorreo);
            $strStmt->bindParam('strIdentificacion', $strIdentificacion);
            $strStmt->bindParam('strMensaje', $strMensaje);
            $strStmt->execute();
        } catch (\Exception $ex) 
        {
            error_log("InfoPersonaRepository->enviarCorreoServiciosPersona " . $ex->getMessage());
            $strMensaje = $ex->getMessage();
        }
       
        return trim($strMensaje);
    }
     /**
     * Envia correo a los clientes que solicitan Derechos del titular de Oposicion, Suspension de Tratamiento y Detencion Suspension de tratamiento
     * @param  string $strCorreo
     * @param  string $strCliente
     * @return string $strMensaje
     *
     * @author Jessenia Piloso <jpiloso@telconet.ec>
     * @version 1.0 30-11-2022
     */
    public function enviarCorreoSolicitudLODPD($strCorreo,$strCliente)
    {
        $strMensaje = str_pad('', 10000, " ");
        $strSql     = 'BEGIN DB_COMERCIAL.COMEK_CONSULTAS.P_ENVIO_CORREO_LOPDP(:strCliente, :strCorreo, :strMensaje); END;';
        try
        {
            $strStmt    = $this->_em->getConnection()->prepare($strSql);
            $strStmt->bindParam('strCorreo', $strCorreo);
            $strStmt->bindParam('strCliente', $strCliente);
            $strStmt->bindParam('strMensaje', $strMensaje);
            $strStmt->execute();
        } catch (\Exception $ex) 
        {
            error_log("InfoPersonaRepository->enviarCorreoServiciosPersona " . $ex->getMessage());
            $strMensaje = $ex->getMessage();
        }
        return trim($strMensaje);
    }

     /**
     *
     * obtiene si la persona tiene discapacidad o no
     *
     * @author Jessenia Piloso <jpiloso@telconet.ec>
     * @version 1.0 25/01/2023
     * 
     * @param mixed arrayRequest => ['intIdServicio'           => Numero de filas a retornar
     *                               'intIdPersonaRol'      => Idpersona empresa Rol]
     * @return array $arraResponse => Array con el resultado del query
     * 
     */     
    public function getDiscapacidadByIdPersonaRol($arrayRequest)
    {
        $arrayResponse = array();
        try
        {
            $strQuery  = "SELECT DB_COMERCIAL.CMKG_BENEFICIOS.F_GET_ES_CLIENTE_DISCAPACITADO(:intIdServicio,:intIdPersonaRol) DISCAPACIDAD FROM DUAL";

            $objStmt = $this->_em->getConnection()->prepare($strQuery);
            $objStmt->bindValue('intIdServicio', $arrayRequest['intIdServicio']);
            $objStmt->bindValue('intIdPersonaRol', $arrayRequest['intIdPersonaRol']);
            $objStmt->execute();

            $arrayResponse = $objStmt->fetchAll();
        }
        catch(\Exception $e)
        {
            error_log('InfoServicioRepository -> getDiscapacidadByIdPersonaRol : '. $e->getMessage());
        }         
        return $arrayResponse;
    }

    /**
     * getOnePersonaBy
     *
     * Método que retorna una o varias personas según parámetros enviados
     *
     * @param  array   $arrayParametros ['idPersonaEmpresaRol', 'intIdPersona', 'strNombreTipoRol']
     *
     * @return array   $arrayPersona ['id', 'idPersonaEmpresaRol', 'idPersona', 'razonSocial', 'nombres', 'apellidos', 'identificacion',
     *                              'direccion', 'estado']
     *
     * @author Joel Muñoz M <efranco@telconet.ec>
     * @version 1.0 26-01-2023
     *
     */
    public function getOnePersonaBy($arrayParametros)
    {
        try
        {
            //INICIO: SE DECLARAN VARIABLES DE QUERY A EJECUTAR
            $strSelect  = "SELECT iper ";

            $strFrom    = " FROM schemaBundle:InfoPersona ip, 
            schemaBundle:InfoPersonaEmpresaRol iper,
            schemaBundle:InfoEmpresaRol ier, 
            schemaBundle:AdmiRol ar, 
            schemaBundle:AdmiTipoRol atr ";

            $strWhere   = " WHERE ip.id = iper.personaId
            AND iper.empresaRolId = ier.id
            AND ier.rolId = ar.id 
            AND ar.tipoRolId = atr.id ";

            $strOrderBy = " ORDER BY iper.estado, iper.id DESC ";
            //FIN: SE PREPARA QUERY A EJECUTAR




            //INICIO: SE PREPARA DECLARACIÓN
            $entityQuery = $this->_em->createQuery();

            //INICIO: SE AGREGAN PARÁMETROS ADICIONALES
            if(isset($arrayParametros['idPersonaRol']))
            {
                $strWhere .= " AND iper.id = :idPersonaRol ";

                $entityQuery->setParameter('idPersonaRol'    , $arrayParametros['idPersonaRol']);
            }
            //FIN: SE AGREGAN PARÁMETROS ADICIONALES

            //FIN: SE INICIA DECLARACIÓN



            // INICIO: SE EJECUTA QUERY
            $strSql = $strSelect.$strFrom.$strWhere.$strOrderBy;
            $entityQuery->setDQL($strSql);
            $objResultado =  $entityQuery->getOneOrNullResult();
            // FIN: SE EJECUTA QUERY


            // INICIO: SE PROCESA Y DEVUELVE DATA
            if( $objResultado && count($objResultado) > 0 )
            {
                $objPersona = $objResultado->getPersonaId();


                return array(
                    'id'                  => $objPersona->getId(),
                    'idPersonaEmpresaRol' => $objResultado->getId(),
                    'idPersona'           => $objPersona->getId(),
                    'razonSocial'         => $objPersona->getRazonSocial(),
                    'nombres'             => $objPersona->getNombres(),
                    'apellidos'           => $objPersona->getApellidos(),
                    'identificacion'      => $objPersona->getIdentificacionCliente(),
                    'direccion'           => $objPersona->getDireccion(),
                    'estado'              => $objPersona->getEstado()
                );
            }
            else
            {
                return array();
            }
            // INICIO: SE PROCESA Y DEVUELVE DATA
        }
        catch(\Exception $e)
        {
            throw($e);
        }
    }

    /**
     * Documentación para getPersonaPorIdentificacion
     *
     * Obtiene la persona por identificacion
     *
     * @param array $arrayParametros['intIdPersona'  => 'Id de la Persona'
     *                              ]
     *
     * @return Retorna la edad en años de una persona
     *
     * @author Eduardo Montenegro<emontenegro@telconet.ec>
     *
     * @version 1.0 17-02-2023
     */
    public function getPersonaPorIdentificacion($arrayParametros)
    {
        $strSql = "
            SELECT * FROM
            DB_COMERCIAL.INFO_PERSONA ipe
            WHERE
            ipe.identificacion_cliente  = :Lv_IdentificacionCliente
            AND ipe.tipo_identificacion = :Lv_TipoIdentificacion
        ";
        $objConsulta = $this->_em->getConnection()->prepare($strSql);
        $objConsulta->bindParam('Lv_IdentificacionCliente', $arrayParametros['identificacion']);
        $objConsulta->bindParam('Lv_TipoIdentificacion', $arrayParametros['tipoIdentificacion']);
        $objConsulta->execute();
        return $objConsulta->fetchAll();
    }


     /**
     * getDatosPersonaById
     *
     * Función que obtiene la persona que cumpla con los parámetros enviados               
     *
     * @param array $arrayParametros [
     *                                  "idPersona"            => id de la persona
     *                                  "codEmpresa"            => id de la empresa
     *                                ]
     * 
     * @return array $arrayRespuesta
     *
     * @author Katherine Solis <ksolis@telconet.ec>
     * @version 1.0 07/04/2023
     */     
    public function getDatosPersonaById($arrayParametros)
    {
        $arrayResponse = array();
        try
        {
            $strQuery  =    "SELECT ve.NO_EMPLE, ve.LOGIN_EMPLE, ve.AREA,ve.NOMBRE_AREA, ve.DEPTO, ve.NOMBRE_DEPTO, ve.NO_CIA, 
                            ve.OFICINA_PROVINCIA, ve.OFICINA_CANTON, ve.NOMBRE, ve.DESCRIPCION_CARGO,
                            persona.ID_PERSONA 
                            FROM NAF47_TNET.V_EMPLEADOS_EMPRESAS ve
                            INNER JOIN DB_COMERCIAL.info_persona persona
                            ON ve.login_emple = persona.LOGIN
                                WHERE ve.NO_CIA= :codEmpresa 
                                AND ve.ESTADO ='A'
                                AND persona.id_persona = :idPersona";
            
            $objStmt = $this->_em->getConnection()->prepare($strQuery);
            $objStmt->bindValue('codEmpresa', $arrayParametros['codEmpresa']);
            $objStmt->bindValue('idPersona', $arrayParametros['idPersona']);
            $objStmt->execute();

            $arrayResponse = $objStmt->fetchAll();
        }
        catch(\Exception $e)
        {
            error_log('InfoServicioRepository -> getDatosPersonaById : '. $e->getMessage());
        }         
        return $arrayResponse;

    }
}

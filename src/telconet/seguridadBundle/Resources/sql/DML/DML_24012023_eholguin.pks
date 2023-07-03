/*
 * @author Edgar Holguín <eholguin@telconet.ec>
 * @version 1.0
 * @since 24-01-2023
 * Se crean las sentencias DML para creación y clonación de perfiles (compartidos) a nivel de tabla SIST_PERFIL.
 */

-- VER CASOS --
INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Administrar Casos Todo',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
    INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Asesor_Comercial',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
    INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn casos asignaciones',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
    INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Cobranza_Agencias',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
    INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Consulta General',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
    INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Ejecutivo_Sac_Fact_Cob',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
    INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Exportar Casos',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
    INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Jefe_Soporte_Tecnico',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
    INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Lider_Desarrollo_Financiero',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
    INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Lider_Desarrollo_Tecnico',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
    INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Analista_Manager_Redes',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
    INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Analista_Marketing',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
    INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Asistente_Administrativa_Gerencial',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );

    INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Asistente_Administrativo',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
    
    INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Asistente_Facturacion',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
    INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Asistente_Servicio_Cliente_Sr',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
    INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Externo_SoporteRemoto',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
    
    INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Ing_Red_Acceso',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
    
        INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Ip_Contact_Center',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
    INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Opu_Coordinador',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    

    
    
    INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn supervisor_ATC',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
    INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn tecnico_externo',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );

    
    INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Perfil Activadores con_excepcion',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
 
     INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Perfil Coordinador',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
 
     INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Perfil: Gepon',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
     INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Perfil: JefeIPCC',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
     INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Perfil Sac Tecnico Ttco',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
    INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Perfil: SacTecnicoAgencias',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
    INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Perfil: SupervisorIPCC',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
    INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Soporte-Casos',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
    INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn soporte_casos_tareas_actividades',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
    INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Tecnico_Agencias',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );    
    
     INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Consultas',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
     INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Electrico_Jefe',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
     INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Perfil: ATC',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
     INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Perfil: ATC Quito',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );    
    
    INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Facturacion_Provincias',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
     INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Gis_Coordinador',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
     INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Gis_Dibujante',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
     INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Gis_Jefe',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
     INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Gis_Tecnica_Sucursal',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
    INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Inicio_Soporte_Adm',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
    INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Inicio_Soporte_Tec',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
     INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn IPCCL1',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );

          
    INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn IPCCL2_OTN_Asis',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
    INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Jefe_Tecnica_Sucursal',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
       
    INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Networking',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
    INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Noc',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
    INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn ObrasCiviles_Coordinador',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
     INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Procesos_Coordinador',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
     INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Procesos_Gerente',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
     INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Procesos_Jefe',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
         
         
    INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Radio',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
    INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn SeguridadLogica_Ingeniero',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
    INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn SeguridadLogica_Jefatura',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
     INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Tecnica_Sucursal',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
     INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Tecnico',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
    INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Ventas',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
     INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Ventas_Gerencia',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );                        
                            
-- VER INFO PUNTO --
INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Asistente_Agencias',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Asistente_Operaciones',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );   
    
    
INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Asistente_Ventas',
        'Activo',
        'telcosRegClon',
        SYSDATE
    ); 
    
INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Auxiliar_Facturacion',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );     
    
INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Cobranza_Agencias_sin_cobros',
        'Activo',
        'telcosRegClon',
        SYSDATE
    ); 
    
INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Cobranzas',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );  
    
INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Comercial_Agencias',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );  
    
INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Contabilidad',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Ejecutivo_Sac_Comercial',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
    
INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Facturacion',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );                             
    
INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Analista_Producto',
        'Activo',
        'telcosRegClon',
        SYSDATE
    ); 
    
    
INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn asesor_comercial_islas',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );  
    
    
INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Asistente_Contable',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );        
    
    
INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn comercial_externo',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );   
    
INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Ejecutivo_Ventas_bloqueado_digital',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );  
    
INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Jefe_Administrativo',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    

INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Opu_Coordinador_Uio',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );              
                         
  
INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn PAN_EjecutivoVentas',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
    
INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn PAN_Financiero',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
        
    
    INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Perfil Activadores',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
    INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Perfil: JefeCobranzas',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );  
    
    
    INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Boc_DC',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
    INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Bodega_Asis_Ayud',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
    INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Cobranzas_Asistente',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );  
    
    
    INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Comunicaciones_Unificadas',
        'Activo',
        'telcosRegClon',
        SYSDATE
    ); 
    
    
    INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Contabilidad_Tributacion',
        'Activo',
        'telcosRegClon',
        SYSDATE
    ); 
    
    INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Facturacion_Asis',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );              
    
    
    INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Facturacion_Jefe',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );  
     
    
    INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Fiscalizacion_Fiscalizador',
        'Activo',
        'telcosRegClon',
        SYSDATE
    ); 
    
                                   
    INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Gerente_Tecnico_Regional',
        'Activo',
        'telcosRegClon',
        SYSDATE
    ); 
    
    INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn GestionISO_Coordinador',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );    
       
    
    INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn IT',
        'Activo',
        'telcosRegClon',
        SYSDATE
    ); 
    
    INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Pac_DC',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );    
    
                
     INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn PyL_Asistente',
        'Activo',
        'telcosRegClon',
        SYSDATE
    ); 
    
    
    
     INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn PyL_CreaPuntos',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );  
    
     INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Ver Punto Cliente',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );   
    
    
    INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Ver estado de cuenta por punto',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );
    
    
     INSERT INTO DB_SEGURIDAD.SIST_PERFIL
    (
        ID_PERFIL,
        NOMBRE_PERFIL,
        ESTADO,
        USR_CREACION,
        FE_CREACION
    )
    VALUES
    (
        DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
        'Tn Editar Punto Cliente',
        'Activo',
        'telcosRegClon',
        SYSDATE
    );               
                        
  COMMIT;        
  /      
                                                                                         
    
    

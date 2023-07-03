
/**
 * Script de regularización y asignación de perfiles a empleados según el cargo enviado como parámetro. Grupo 1 (Todos los perfiles)
 * @version 1.0
 * @author Edgar Holguín <eholguin@telconet.ec>
 * @since 22-FEB-2023
 */


-- ASIGNAR PERMISOS EN TELCOS EJECUTANDO PROCEDURE EN LA BASE

DECLARE

   CURSOR C_GetDepartamentos(Cv_PrefijoEmpresa   DB_COMERCIAL.INFO_EMPRESA_GRUPO.PREFIJO%TYPE )
    IS
        SELECT DEP.ID_DEPARTAMENTO,DEP.NOMBRE_DEPARTAMENTO
        FROM DB_GENERAL.ADMI_DEPARTAMENTO    DEP
        JOIN DB_COMERCIAL.INFO_EMPRESA_GRUPO IEG ON IEG.COD_EMPRESA = DEP.EMPRESA_COD
        WHERE DEP.NOMBRE_DEPARTAMENTO 
        IN('Fiscalizacion','Planificacion Y Logistica','PLANIFICACION Y LOGISTICA') 
        AND IEG.PREFIJO = Cv_PrefijoEmpresa
        AND DEP.ESTADO = 'Activo';

    CURSOR C_GetPersonasPorRol(Cv_PrefijoEmpresa   DB_COMERCIAL.INFO_EMPRESA_GRUPO.PREFIJO%TYPE,
                               Cv_DescripcionRol   DB_GENERAL.ADMI_ROL.DESCRIPCION_ROL%TYPE,
                               Cv_Departamento     DB_GENERAL.ADMI_DEPARTAMENTO.NOMBRE_DEPARTAMENTO%TYPE )
    IS
      --
	SELECT DISTINCT IP.ID_PERSONA,IER.EMPRESA_COD, IP.NOMBRES, IP.APELLIDOS, IP.LOGIN,OFI.NOMBRE_OFICINA,OFI.ID_OFICINA,AD.NOMBRE_DEPARTAMENTO
	FROM 
	DB_COMERCIAL.INFO_PERSONA                  IP   
	JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPER ON IPER.PERSONA_ID         = IP.ID_PERSONA 
	JOIN DB_COMERCIAL.INFO_OFICINA_GRUPO       OFI  ON OFI.ID_OFICINA          = IPER.OFICINA_ID
	JOIN DB_COMERCIAL.ADMI_DEPARTAMENTO        AD   ON IPER.DEPARTAMENTO_ID    = AD.ID_DEPARTAMENTO
	JOIN DB_COMERCIAL.INFO_EMPRESA_ROL         IER  ON IER.ID_EMPRESA_ROL      = IPER.EMPRESA_ROL_ID 
	JOIN DB_COMERCIAL.INFO_EMPRESA_ROL         IER  ON IER.ID_EMPRESA_ROL      = IPER.EMPRESA_ROL_ID 
	JOIN DB_GENERAL.ADMI_ROL                   AR   ON AR.ID_ROL               = IER.ROL_ID 
	JOIN DB_GENERAL.ADMI_TIPO_ROL              ATR  ON ATR.ID_TIPO_ROL         = AR.TIPO_ROL_ID
	JOIN DB_COMERCIAL.INFO_EMPRESA_GRUPO       IEG  ON IER.EMPRESA_COD         = IEG.COD_EMPRESA 
	WHERE  IEG.PREFIJO IN(Cv_PrefijoEmpresa)
	AND    ATR.DESCRIPCION_TIPO_ROL ='Empleado'
	AND    AR.DESCRIPCION_ROL       = Cv_DescripcionRol
    AND    AD.NOMBRE_DEPARTAMENTO   = Cv_Departamento
	ORDER BY IER.EMPRESA_COD;      
         
   CURSOR C_GetRoles
   IS
	SELECT AR.ID_ROL,AR.DESCRIPCION_ROL
	FROM DB_GENERAL.ADMI_ROL AR
	WHERE AR.DESCRIPCION_ROL IN(
    'Jefe Dpto. Nacional',
	'Jefatura nacional ',
	'Subjefatura nacional',
    'Subjefe Departamental',
    'Subjefe Dpto Nacional',
    'Coordinador Nacional',
    'Jefe Departamental',
    'Lider Home',
    'Coordinador',
	'Planificador Home') AND AR.ESTADO   <> 'Eliminado';

   CURSOR C_GetPerfiles
   IS
     SELECT SPF.ID_PERFIL 
     FROM DB_SEGURIDAD.SIST_PERFIL SPF
     WHERE SPF.NOMBRE_PERFIL IN
     ( 'Md_Tn_BusquedaGeneral',
       'Md_Tn_BusquedaAvanzada',
       'Md_Tn_DatosPunto',
       'Md_Tn_DatosTecnico',
       'Md_Tn_Facturas',
       'Md_Tn_Casos',
       'Md_Tn_ResumenCliente',
       'Md_Tn_Diagnostico_OSS',
       'Md_Tn_VerContrato'
     );
     
   CURSOR C_GetRegExistente( Cv_IdPerfil    DB_SEGURIDAD.SEGU_PERFIL_PERSONA.PERFIL_ID%TYPE,
		                     Cv_IdPersona   DB_SEGURIDAD.SEGU_PERFIL_PERSONA.PERSONA_ID%TYPE,
		                     Cv_IdOficina   DB_SEGURIDAD.SEGU_PERFIL_PERSONA.OFICINA_ID%TYPE,
		                     Cv_EmpresaId   DB_SEGURIDAD.SEGU_PERFIL_PERSONA.EMPRESA_ID%TYPE)
   IS     
     SELECT COUNT(SPP.PERFIL_ID)
     FROM DB_SEGURIDAD.SEGU_PERFIL_PERSONA SPP
     WHERE SPP.PERFIL_ID  = Cv_IdPerfil  
     AND   SPP.PERSONA_ID = Cv_IdPersona 
     AND   SPP.OFICINA_ID = Cv_IdOficina  
     AND   SPP.EMPRESA_ID = Cv_EmpresaId;
                      
   TYPE T_Departamentos IS TABLE OF C_GetDepartamentos%ROWTYPE INDEX BY PLS_INTEGER;
   La_Departamentos  T_Departamentos;
   TYPE T_Roles IS TABLE OF C_GetRoles%ROWTYPE INDEX BY PLS_INTEGER;
   La_Roles     T_Roles;  
   TYPE T_Perfiles IS TABLE OF C_GetPerfiles%ROWTYPE INDEX BY PLS_INTEGER;
   La_Perfiles  T_Perfiles;
   TYPE T_Personas IS TABLE OF C_GetPersonasPorRol%ROWTYPE INDEX BY PLS_INTEGER;
   La_Personas  T_Personas;    
   Lv_MessageError        VARCHAR2(4000);
   Ln_Limit               PLS_INTEGER := 1000;
   Ln_Idx                 PLS_INTEGER := 0;
   Ln_i                   PLS_INTEGER := 0;
   Ln_j                   PLS_INTEGER := 0;
   Ln_k                   PLS_INTEGER := 0;
   Ln_NumRegistrosCommit  NUMBER      := 0;
   Ln_ContadorExistentes  NUMBER      := 0;
   Ln_ContRegistros       NUMBER      := 0;
   Ln_PrefijoEmpresa      VARCHAR2(5) := 'TN';
BEGIN

   IF C_GetDepartamentos%ISOPEN THEN
     CLOSE C_GetDepartamentos;
   END IF;

   IF C_GetPerfiles%ISOPEN THEN
     CLOSE C_GetPerfiles;
   END IF;

   IF C_GetRoles%ISOPEN THEN
     CLOSE C_GetRoles;
   END IF;
   
   IF C_GetPersonasPorRol%ISOPEN THEN
     CLOSE C_GetPersonasPorRol;
   END IF;   

   DBMS_OUTPUT.PUT_LINE('INGRESA A PROCESO');
   ------------------------
   OPEN C_GetDepartamentos(Ln_PrefijoEmpresa);
   LOOP
     FETCH C_GetDepartamentos bulk collect INTO La_Departamentos limit Ln_Limit;
     EXIT
       WHEN La_Departamentos.count = 0 ;

          Ln_Idx := La_Departamentos.FIRST;
          WHILE (Ln_Idx IS NOT NULL)
            LOOP
                -----------------------
                OPEN C_GetPerfiles();
                LOOP
                  FETCH C_GetPerfiles bulk collect INTO La_Perfiles limit Ln_Limit;
                  EXIT
                    WHEN La_Perfiles.count = 0 ;

                       Ln_i := La_Perfiles.FIRST;
                       WHILE (Ln_i IS NOT NULL)
                         LOOP
                                ----------------------------
                                OPEN C_GetRoles();
                                LOOP
                                    FETCH C_GetRoles bulk collect INTO La_Roles limit Ln_Limit;
                                    EXIT
                                      WHEN La_Roles.count = 0 ;

                                         Ln_j := La_Roles.FIRST;
                                         WHILE (Ln_j IS NOT NULL)
                                           LOOP
                                                ----------------------------
                                                OPEN C_GetPersonasPorRol(Ln_PrefijoEmpresa,La_Roles(Ln_j).DESCRIPCION_ROL, La_Departamentos(Ln_Idx).NOMBRE_DEPARTAMENTO);
                                                LOOP
                                                    FETCH C_GetPersonasPorRol bulk collect INTO La_Personas limit Ln_Limit;
                                                    EXIT
                                                      WHEN La_Personas.count = 0 ;

                                                         Ln_k := La_Personas.FIRST;
                                                         WHILE (Ln_k IS NOT NULL)
                                                           LOOP
                                                             Ln_ContadorExistentes  := 0;
                                                              IF (La_Perfiles(Ln_i).ID_PERFIL IS NOT NULL AND  La_Personas(Ln_k).ID_PERSONA IS NOT NULL AND La_Personas(Ln_k).ID_OFICINA IS NOT NULL) THEN

                                                                  OPEN C_GetRegExistente(La_Perfiles(Ln_i).ID_PERFIL,La_Personas(Ln_k).ID_PERSONA,La_Personas(Ln_k).ID_OFICINA,La_Personas(Ln_k).EMPRESA_COD);
                                                                  --
                                                                  FETCH C_GetRegExistente INTO Ln_ContadorExistentes;
                                                                  --
                                                                      IF Ln_ContadorExistentes <= 0 THEN              

                                                                        INSERT
                                                                        INTO DB_SEGURIDAD.SEGU_PERFIL_PERSONA
                                                                        (
                                                                          PERFIL_ID,
                                                                          PERSONA_ID,
                                                                          OFICINA_ID,
                                                                          EMPRESA_ID,
                                                                          USR_CREACION,
                                                                          FE_CREACION,
                                                                          IP_CREACION
                                                                        )
                                                                        VALUES
                                                                        (
                                                                          La_Perfiles(Ln_i).ID_PERFIL,
                                                                          La_Personas(Ln_k).ID_PERSONA,
                                                                          La_Personas(Ln_k).ID_OFICINA,
                                                                          La_Personas(Ln_k).EMPRESA_COD,
                                                                          'telcosRegPerfil',
                                                                          SYSDATE,
                                                                          '127.0.0.1'
                                                                        );
                                                                        Ln_ContRegistros:= Ln_ContRegistros+1;
                                                                     END IF;

                                                                  CLOSE C_GetRegExistente;

                                                             END IF;

                                                             Ln_k := La_Personas.NEXT(Ln_k);
                                                             Ln_NumRegistrosCommit := Ln_NumRegistrosCommit + 1;
                                                             IF (Ln_NumRegistrosCommit=100) THEN
                                                               COMMIT;
                                                               Ln_NumRegistrosCommit := 0;
                                                             END IF;
                                                           END LOOP;
                                                END LOOP;
                                                CLOSE C_GetPersonasPorRol;
                                                --------------------------
                                             Ln_j := La_Roles.NEXT(Ln_j);
                                           END LOOP;
                                END LOOP;
                                CLOSE C_GetRoles;
                                -----------------
                           Ln_i := La_Perfiles.NEXT(Ln_i);
                         END LOOP;
                  END LOOP;
                  CLOSE C_GetPerfiles;
                  -----------------------------------
              Ln_Idx := La_Departamentos.NEXT(Ln_Idx);
            END LOOP;
     END LOOP;
     CLOSE C_GetDepartamentos;
  COMMIT;
  DBMS_OUTPUT.PUT_LINE('Cantidad de registros de perfiles asignados:'|| Ln_ContRegistros); 
 EXCEPTION
  WHEN OTHERS THEN
    ROLLBACK;
    --
    Lv_MessageError := 'ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                        || ' ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE;
    --
    DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR( 'Telcos+', 'Error en Asignacion de Perfiles', Lv_MessageError, 
                                          'telcosRegPerfil', SYSDATE, 
                                          NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), '127.0.0.1') );    
END;

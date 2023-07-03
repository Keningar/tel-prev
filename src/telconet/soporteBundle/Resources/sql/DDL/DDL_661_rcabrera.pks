CREATE TABLE DB_SOPORTE.info_detalle_tareas
(
  detalle_id             NUMBER,
  numero_tarea           NUMBER,
  persona_empresa_rol_id NUMBER,
  departamento_id        NUMBER,
  oficina_id             NUMBER,
  estado                 VARCHAR2(20),
  detalle_asignacion_id  NUMBER,
  detalle_historial_id   NUMBER,
  usr_creacion           VARCHAR2(20),
  fe_creacion            TIMESTAMP
);

/

GRANT SELECT ON   DB_SOPORTE.info_detalle_tareas TO DB_COMERCIAL;  

/

CREATE INDEX DB_SOPORTE.INFO_DETALLE_TAREAS_IX_2 ON DB_SOPORTE.INFO_DETALLE_TAREAS (PERSONA_EMPRESA_ROL_ID ASC) 
LOGGING 
TABLESPACE DB_TELCONET;

/

CREATE INDEX DB_SOPORTE.INFO_DETALLE_TAREAS_IX_1 ON DB_SOPORTE.INFO_DETALLE_TAREAS (DEPARTAMENTO_ID ASC,OFICINA_ID ASC) 
LOGGING 
TABLESPACE DB_TELCONET;

/

CREATE OR REPLACE TRIGGER DB_SOPORTE.AFTER_INFO_DETALLE_HISTORIAL AFTER
  INSERT ON DB_SOPORTE.INFO_DETALLE_HISTORIAL FOR EACH ROW 
  
  DECLARE 
  
    CURSOR cu_detalle_tareas(cn_detalle_id NUMBER) IS
    SELECT detalle_id
    FROM db_soporte.INFO_DETALLE_TAREAS
    WHERE detalle_id = cn_detalle_id;
    
    ln_detalle_id NUMBER;
  
  BEGIN
  
    OPEN cu_detalle_tareas(:new.DETALLE_ID);
    FETCH cu_detalle_tareas INTO ln_detalle_id;
    
    IF cu_detalle_tareas%FOUND THEN
    
      IF ((:new.ESTADO = 'Finalizada') OR (:new.ESTADO = 'Cancelada') OR (:new.ESTADO = 'Rechazada')  OR (:new.ESTADO = 'Anulada')) THEN
      
        DELETE FROM db_soporte.INFO_DETALLE_TAREAS WHERE DETALLE_ID = :new.DETALLE_ID;
        
      ELSE
      
        UPDATE db_soporte.INFO_DETALLE_TAREAS
        SET ESTADO             = :new.ESTADO,
          DETALLE_HISTORIAL_ID = :new.ID_DETALLE_HISTORIAL
        WHERE DETALLE_ID       = :new.DETALLE_ID;
      
      END IF;    
      
    ELSE
    
      INSERT
      INTO db_soporte.INFO_DETALLE_TAREAS
        (
          DETALLE_ID,
          ESTADO,
          DETALLE_HISTORIAL_ID,
          USR_CREACION,
          FE_CREACION
        )
        VALUES
        (
          :new.DETALLE_ID,
          :new.ESTADO,
          :new.ID_DETALLE_HISTORIAL,
          'telcos',
          sysdate
        );
        
    END IF;
    
    CLOSE cu_detalle_tareas;
    
  END;

/

CREATE OR REPLACE TRIGGER DB_SOPORTE.AFTER_INFO_DETALLE_ASIGNACION AFTER
  INSERT OR UPDATE ON DB_SOPORTE.INFO_DETALLE_ASIGNACION FOR EACH ROW
  
DECLARE 
  
  CURSOR cu_persona_departamento(cn_persona_empresa_rol NUMBER)
  IS
    SELECT departamento_id
    FROM db_soporte.info_persona_empresa_rol
    WHERE id_persona_rol = cn_persona_empresa_rol;

  CURSOR cu_persona_oficina(cn_persona_empresa_rol NUMBER)
  IS
    SELECT oficina_id
    FROM db_soporte.info_persona_empresa_rol
    WHERE id_persona_rol = cn_persona_empresa_rol;   
  
  CURSOR cu_detalle_tareas(cn_detalle_id NUMBER)  
  IS
    SELECT detalle_id
    FROM db_soporte.INFO_DETALLE_TAREAS
    WHERE detalle_id = cn_detalle_id;

  CURSOR cu_numero_tarea(cn_detalle_id NUMBER)  
  IS
    SELECT min(id_comunicacion)
    FROM db_comunicacion.INFO_COMUNICACION
    WHERE detalle_id = cn_detalle_id;  
    
  ln_departamento_id NUMBER;
  ln_oficina_id      NUMBER;
  ln_numero_tarea    NUMBER;
  ln_detalle_id      NUMBER;
  
BEGIN

  OPEN cu_persona_departamento(:new.PERSONA_EMPRESA_ROL_ID);
  FETCH cu_persona_departamento INTO ln_departamento_id;  
  CLOSE cu_persona_departamento;

  OPEN cu_persona_oficina(:new.PERSONA_EMPRESA_ROL_ID);
  FETCH cu_persona_oficina INTO ln_oficina_id;  
  CLOSE cu_persona_oficina;
  
  OPEN cu_numero_tarea(:new.DETALLE_ID);
  FETCH cu_numero_tarea INTO ln_numero_tarea;  
  CLOSE cu_numero_tarea; 
  
  OPEN cu_detalle_tareas(:new.DETALLE_ID);
  FETCH cu_detalle_tareas INTO ln_detalle_id;
  
  IF cu_detalle_tareas%FOUND THEN
  
    UPDATE db_soporte.INFO_DETALLE_TAREAS
    SET PERSONA_EMPRESA_ROL_ID = :new.PERSONA_EMPRESA_ROL_ID,
      OFICINA_ID               = ln_oficina_id,
      NUMERO_TAREA             = ln_numero_tarea,
      DEPARTAMENTO_ID          = ln_departamento_id,
      DETALLE_ASIGNACION_ID    = :new.ID_DETALLE_ASIGNACION
    WHERE DETALLE_ID           = :new.DETALLE_ID;
    
  ELSE
  
    INSERT
    INTO db_soporte.INFO_DETALLE_TAREAS
      (
        DETALLE_ID,
        NUMERO_TAREA,
        PERSONA_EMPRESA_ROL_ID,
        DEPARTAMENTO_ID,
        OFICINA_ID,
        DETALLE_ASIGNACION_ID,
        USR_CREACION,
        FE_CREACION
      )
      VALUES
      (
        :new.DETALLE_ID,
        ln_numero_tarea,
        :new.PERSONA_EMPRESA_ROL_ID,
        ln_departamento_id,
        ln_oficina_id,
        :new.ID_DETALLE_ASIGNACION,
        'telcos',
        sysdate
      );
      
  END IF;
  
  CLOSE cu_detalle_tareas;
  
END;  

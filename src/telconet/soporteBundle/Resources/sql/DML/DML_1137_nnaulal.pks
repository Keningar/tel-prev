
Insert into DB_SOPORTE.ADMI_TAREA (
ID_TAREA,
PROCESO_ID,ROL_AUTORIZA_ID,
TAREA_ANTERIOR_ID,
TAREA_SIGUIENTE_ID,
PESO,ES_APROBADA,
NOMBRE_TAREA,
DESCRIPCION_TAREA,
TIEMPO_MAX,
UNIDAD_MEDIDA_TIEMPO,
COSTO,PRECIO_PROMEDIO,
ESTADO,USR_CREACION,
FE_CREACION,
USR_ULT_MOD,
FE_ULT_MOD,
AUTOMATICA_WS,
CATEGORIA_TAREA_ID,
PRIORIDAD,REQUIERE_FIBRA,
VISUALIZAR_MOVIL) 
values (DB_SOPORTE.SEQ_ADMI_TAREA.NEXTVAL,'301',null,null,null,'1','0',
'Cambiar puerto POM','Cambiar puerto POM','3','HORAS','0','0','Activo',
'nnaulal',sysdate,'nnaulal',
sysdate,null,null,null,'S','S');
commit;


Insert into DB_SOPORTE.ADMI_TAREA (
ID_TAREA,PROCESO_ID,ROL_AUTORIZA_ID,TAREA_ANTERIOR_ID,
TAREA_SIGUIENTE_ID,PESO,ES_APROBADA,NOMBRE_TAREA,
DESCRIPCION_TAREA,TIEMPO_MAX,UNIDAD_MEDIDA_TIEMPO,
COSTO,PRECIO_PROMEDIO,ESTADO,USR_CREACION,FE_CREACION,
USR_ULT_MOD,FE_ULT_MOD,AUTOMATICA_WS,CATEGORIA_TAREA_ID,
PRIORIDAD,REQUIERE_FIBRA,VISUALIZAR_MOVIL) 
values (DB_SOPORTE.SEQ_ADMI_TAREA.NEXTVAL,'611',null,null,null,'1','0',
'Cambiar puerto POM','Cambiar puerto POM','3','HORAS','0','0','Activo','nnaulal',sysdate,'nnaulal',
sysdate,null,null,null,'S','S');
COMMIT;



INSERT INTO DB_SOPORTE.INFO_PROGRESO_PORCENTAJE IPP 
    VALUES 
      (   DB_SOPORTE.SEQ_INFO_PROGRESO_PORCENTAJE.NEXTVAL,'20','1','850','Activo','admin',
          to_date('01/04/18','DD/MM/RR'),'127.0.0.1',
          null,to_date('01/04/18','DD/MM/RR'),'1','10'
      );
      
   INSERT INTO DB_SOPORTE.INFO_PROGRESO_PORCENTAJE IPP 
    VALUES 
      (   DB_SOPORTE.SEQ_INFO_PROGRESO_PORCENTAJE.NEXTVAL,'10','2','850','Activo','admin',
          to_date('01/04/18','DD/MM/RR'),'127.0.0.1',
          null,to_date('01/04/18','DD/MM/RR'),'2','10'
      );
      
    INSERT INTO DB_SOPORTE.INFO_PROGRESO_PORCENTAJE IPP 
    VALUES 
      (   DB_SOPORTE.SEQ_INFO_PROGRESO_PORCENTAJE.NEXTVAL,'20','10','850','Activo','admin',
          to_date('01/04/18','DD/MM/RR'),'127.0.0.1',
          null,to_date('01/04/18','DD/MM/RR'),'3','10'
      );
      
    INSERT INTO DB_SOPORTE.INFO_PROGRESO_PORCENTAJE IPP 
    VALUES 
      (   DB_SOPORTE.SEQ_INFO_PROGRESO_PORCENTAJE.NEXTVAL,'30','9','850','Activo','admin',
          to_date('01/04/18','DD/MM/RR'),'127.0.0.1',
          null,to_date('01/04/18','DD/MM/RR'),'4','10'
      );
      
      INSERT INTO DB_SOPORTE.INFO_PROGRESO_PORCENTAJE IPP 
    VALUES 
      (   DB_SOPORTE.SEQ_INFO_PROGRESO_PORCENTAJE.NEXTVAL,'10','7','850','Activo','admin',
          to_date('01/04/18','DD/MM/RR'),'127.0.0.1',
          null,to_date('01/04/18','DD/MM/RR'),'5','10'
      );
      
      INSERT INTO DB_SOPORTE.INFO_PROGRESO_PORCENTAJE IPP 
    VALUES 
      (   DB_SOPORTE.SEQ_INFO_PROGRESO_PORCENTAJE.NEXTVAL,'10','8','850','Activo','admin',
          to_date('01/04/18','DD/MM/RR'),'127.0.0.1',
          null,to_date('01/04/18','DD/MM/RR'),'6','10'
      );
  COMMIT;
  
  
  INSERT INTO DB_SOPORTE.INFO_PROGRESO_PORCENTAJE IPP 
    VALUES 
      (   DB_SOPORTE.SEQ_INFO_PROGRESO_PORCENTAJE.NEXTVAL,'20','1','850','Activo','admin',
          to_date('01/04/18','DD/MM/RR'),'127.0.0.1',
          null,to_date('01/04/18','DD/MM/RR'),'1','18'
      );
      
   INSERT INTO DB_SOPORTE.INFO_PROGRESO_PORCENTAJE IPP 
    VALUES 
      (   DB_SOPORTE.SEQ_INFO_PROGRESO_PORCENTAJE.NEXTVAL,'10','2','850','Activo','admin',
          to_date('01/04/18','DD/MM/RR'),'127.0.0.1',
          null,to_date('01/04/18','DD/MM/RR'),'2','18'
      );
      
    INSERT INTO DB_SOPORTE.INFO_PROGRESO_PORCENTAJE IPP 
    VALUES 
      (   DB_SOPORTE.SEQ_INFO_PROGRESO_PORCENTAJE.NEXTVAL,'20','10','850','Activo','admin',
          to_date('01/04/18','DD/MM/RR'),'127.0.0.1',
          null,to_date('01/04/18','DD/MM/RR'),'3','18'
      );
      
    INSERT INTO DB_SOPORTE.INFO_PROGRESO_PORCENTAJE IPP 
    VALUES 
      (   DB_SOPORTE.SEQ_INFO_PROGRESO_PORCENTAJE.NEXTVAL,'30','9','850','Activo','admin',
          to_date('01/04/18','DD/MM/RR'),'127.0.0.1',
          null,to_date('01/04/18','DD/MM/RR'),'4','18'
      );
      
     INSERT INTO DB_SOPORTE.INFO_PROGRESO_PORCENTAJE IPP 
    VALUES 
      (   DB_SOPORTE.SEQ_INFO_PROGRESO_PORCENTAJE.NEXTVAL,'10','7','850','Activo','admin',
          to_date('01/04/18','DD/MM/RR'),'127.0.0.1',
          null,to_date('01/04/18','DD/MM/RR'),'5','18'
      );
      
      INSERT INTO DB_SOPORTE.INFO_PROGRESO_PORCENTAJE IPP 
    VALUES 
      (   DB_SOPORTE.SEQ_INFO_PROGRESO_PORCENTAJE.NEXTVAL,'10','8','850','Activo','admin',
          to_date('01/04/18','DD/MM/RR'),'127.0.0.1',
          null,to_date('01/04/18','DD/MM/RR'),'6','18'
      );
  COMMIT;
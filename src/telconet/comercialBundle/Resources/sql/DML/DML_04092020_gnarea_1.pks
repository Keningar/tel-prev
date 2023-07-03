SET SERVEROUTPUT ON;
--REMOVER PERFIL INDIVIDUAL A LOS EMPLEADOS DE MD
--QUE TENGAN EL PERFIL 1496.
--PERFIL EDITAR_FORMA_PAGO: 1496 

declare
 CURSOR C_PERSONA_PERFIL_IND IS
   SELECT a.*,rowid 
    FROM db_seguridad.segu_perfil_persona a
    WHERE empresa_id = 18  --18:MD
    and perfil_id=1496; --perfil EDITAR FORMA DE PAGO
 
 Lv_fe_creacion VARCHAR(100);
 
BEGIN
  FOR v_row in c_persona_perfil_IND LOOP
  SELECT to_char(fe_creacion,'DD-MM-YYYY HH:MI:SS') INTO LV_FE_CREACION 
    FROM DB_SEGURIDAD.SEGU_PERFIL_PERSONA WHERE ROWID=v_row.rowid;
  --REVERSO
  DBMS_OUTPUT.PUT_LINE('INSERT INTO DB_SEGURIDAD.SEGU_PERFIL_PERSONA VALUES ('
    ||v_row.perfil_id||','||v_row.persona_id||','||v_row.oficina_id
    ||','||v_row.empresa_id||','''||v_row.usr_creacion||''',TO_DATE('''
    ||Lv_fe_creacion||''',''DD-MM-YYYY HH:MI:SS''), '''||v_row.ip_creacion
    ||''');' );
  DELETE FROM DB_SEGURIDAD.SEGU_PERFIL_PERSONA WHERE ROWID=v_row.rowid;  
  END LOOP;
  COMMIT;
END;

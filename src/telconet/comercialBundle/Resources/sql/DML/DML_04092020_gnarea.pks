src/telconet/comercialBundle/Resources/sql/DML/DML_04092020_gnarea.pks
SET SERVEROUTPUT ON;
DECLARE
--PERFIL EDITAR_FORMA_PAGO  1496
--PERFIL ADMINISTRACION     1
--RELACION_SISTEMA_ID = 2581
Ln_modulo_id NUMBER := 60;
Ln_accion_id NUMBER := 1006;
--REVERT
LV_USR_CREACION VARCHAR(200);
LV_FE_CREACION VARCHAR(200);
LV_IP_CREACION VARCHAR(200);


Ln_perfil_id NUMBER;
Ln_perfil_asignado NUMBER;
Ln_perfil_permitido NUMBER;

Ln_relacion_sistema_id NUMBER;


CURSOR C_perfil_from_mod_acc(Cn_modulo NUMBER, Cn_accion NUMBER) IS
   SELECT b.perfil_id, b.RELACION_SISTEMA_ID 
      FROM db_seguridad.segu_relacion_sistema a 
      INNER JOIN db_seguridad.segu_asignacion b 
      ON a.id_relacion_sistema = b.relacion_sistema_id 
            INNER JOIN DB_SEGURIDAD.sist_perfil c
            ON c.ID_PERFIL = b.PERFIL_ID
      WHERE modulo_id = Cn_modulo
      AND accion_id = Cn_accion
      AND (c.NOMBRE_PERFIL like 'Md%');


CURSOR C_perfil_permisos IS
  SELECT ID_PERFIL FROM db_seguridad.sist_perfil WHERE nombre_perfil IN
    ('Md_Asistente_Cobranzas','Md_Jefe_Cobranzas','Md_Asistente_Cobranzas_Jr',
    'Md_Asistente_Administracion_Contratos','Md_Coordinador_Facturacion',
    'Md_Asistente_Cobranzas_Bancario', 'Md_Coordinador_Cobranzas', 
    'Md_Asistente_Servicio_Cliente', 'Md_Coordinador_Servicio_Cliente');

   
Lb_flag BOOLEAN;
type Lt_cambios is table of number index by BINARY_INTEGER;
la_cambios Lt_cambios;
Ln_i number := 1;
Ln_relacion_sistema_new number;
Ln_tmp number;
Lv_perfil varchar2(100);
BEGIN
  

  FOR v_perfil IN C_perfil_from_mod_acc(Ln_modulo_id, Ln_accion_id) LOOP
  Lb_flag := False;
    Ln_perfil_id := v_perfil.perfil_id;
    Ln_relacion_sistema_id := v_perfil.relacion_sistema_id;

    FOR v_perfil_permitido IN c_perfil_permisos LOOP
      IF v_perfil_permitido.id_perfil = ln_perfil_id THEN
        lb_flag:=TRUE;
        exit;
      END IF;
    END LOOP;
    SELECT nombre_perfil INTO Lv_perfil from db_seguridad.SIST_PERFIL where id_perfil=ln_perfil_id;
    IF LB_FLAG THEN
      DBMS_OUTPUT.PUT_LINE('PERFIL '|| Lv_perfil ||'(' ||Ln_perfil_id||') Debe tener permisos');
    ELSE
      DBMS_OUTPUT.PUT_LINE('PERFIL '|| Lv_perfil ||'(' ||Ln_perfil_id||') NO Debe tener permisos');
      ------------guardo los perfiles anteriores que se borraron... que tienen acceso a editarFormaPago
      --REVERT
      --SELECT USR_CREACION, TO_CHAR(FE_CREACION, 'DD-MM-YYYY HH:MI:SS'), IP_CREACION INTO LV_USR_CREACION, LV_FE_CREACION, LV_IP_CREACION 
        --FROM SEGU_ASIGNACION WHERE PERFIL_ID = Ln_perfil_id AND relacion_sistema_id = 2581;
      --DBMS_output.put_line('INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION VALUES ('|| 
        --Ln_perfil_id||', 2581'||','''||LV_USR_CREACION||''', TO_DATE('''||LV_FE_CREACION||''', ''DD-MM-YYYY HH:MI:SS''), '''||
        --LV_IP_CREACION||''');');

      DELETE FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE PERFIL_ID = Ln_perfil_id AND relacion_sistema_id = 2581;

    END IF;
  END LOOP;
commit;
END;  

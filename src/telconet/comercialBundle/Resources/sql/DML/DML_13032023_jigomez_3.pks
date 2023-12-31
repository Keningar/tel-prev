/**
 * DEBE EJECUTARSE EN DB_COMERCIAL
 * Script para crear características y relacionar con producto SEGURIDAD ELECTRONICA VIDEO VIGILANCIA
 * @author Jorge Gomez <jigomez@telconet.ec>
 * @version 1.0 13-03-2023 - Versión Inicial.
 */

declare

parametro_num_1 number;
parametro_num_2 number;
parametro_num_3 number;
parametro_num_4 number;

begin

	select DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL
	into parametro_num_1
	from dual;

	select DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL
	into parametro_num_2
	from dual;

	select DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL
	into parametro_num_3
	from dual;

    select DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL
	into parametro_num_4
	from dual;

    INSERT INTO  DB_COMERCIAL.ADMI_CARACTERISTICA (ID_CARACTERISTICA, DESCRIPCION_CARACTERISTICA, TIPO_INGRESO, ESTADO, FE_CREACION, USR_CREACION, FE_ULT_MOD, USR_ULT_MOD, TIPO)VALUES
    (parametro_num_1,'SERIE_EQUIPO_PTZ', 'T', 'Activo', SYSDATE, 'jigomez', SYSDATE, 'jigomez','TECNICA');

    INSERT INTO  DB_COMERCIAL.ADMI_CARACTERISTICA (ID_CARACTERISTICA, DESCRIPCION_CARACTERISTICA, TIPO_INGRESO, ESTADO, FE_CREACION, USR_CREACION, FE_ULT_MOD, USR_ULT_MOD, TIPO)VALUES
    (parametro_num_2,'ID_CAMARA', 'T', 'Activo', SYSDATE, 'jigomez', SYSDATE, 'jigomez','TECNICA');

    INSERT INTO  DB_COMERCIAL.ADMI_CARACTERISTICA (ID_CARACTERISTICA, DESCRIPCION_CARACTERISTICA, TIPO_INGRESO, ESTADO, FE_CREACION, USR_CREACION, FE_ULT_MOD, USR_ULT_MOD, TIPO)VALUES
    (parametro_num_3,'ALTURA_POSTE', 'T', 'Activo', SYSDATE, 'jigomez', SYSDATE, 'jigomez','TECNICA');

    INSERT INTO  DB_COMERCIAL.ADMI_CARACTERISTICA (ID_CARACTERISTICA, DESCRIPCION_CARACTERISTICA, TIPO_INGRESO, ESTADO, FE_CREACION, USR_CREACION, FE_ULT_MOD, USR_ULT_MOD, TIPO)VALUES
    (parametro_num_4,'TIPO_POSTE', 'T', 'Activo', SYSDATE, 'jigomez', SYSDATE, 'jigomez','TECNICA');


    INSERT INTO  DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA (ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID, FE_CREACION, FE_ULT_MOD, USR_CREACION, USR_ULT_MOD, ESTADO, VISIBLE_COMERCIAL)
    VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL, 1099, parametro_num_1, SYSDATE, SYSDATE, 'jigomez', 'jigomez','Activo', 'NO');

    INSERT INTO  DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA (ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID, FE_CREACION, FE_ULT_MOD, USR_CREACION, USR_ULT_MOD, ESTADO, VISIBLE_COMERCIAL)
    VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL, 1099, parametro_num_2, SYSDATE, SYSDATE, 'jigomez', 'jigomez','Activo', 'NO');

    INSERT INTO  DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA (ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID, FE_CREACION, FE_ULT_MOD, USR_CREACION, USR_ULT_MOD, ESTADO, VISIBLE_COMERCIAL)
    VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL, 1099, parametro_num_3, SYSDATE, SYSDATE, 'jigomez', 'jigomez','Activo', 'NO');

    INSERT INTO  DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA (ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID, FE_CREACION, FE_ULT_MOD, USR_CREACION, USR_ULT_MOD, ESTADO, VISIBLE_COMERCIAL)
    VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL, 1099, parametro_num_4, SYSDATE, SYSDATE, 'jigomez', 'jigomez','Activo', 'NO');

	commit;

end;

/
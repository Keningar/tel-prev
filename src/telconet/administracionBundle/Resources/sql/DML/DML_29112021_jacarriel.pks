/*
 *
 * Se crea nuevo parametro de dias de bloqueo de bobinas para cuadrillas satelites.
 *	 
 * @author Jeampier Carriel <jacarriel@telconet.ec>
 * @version 1.0 29-11-2021
 *
*/

INSERT INTO db_general.admi_parametro_det VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'PARAMETROS_GENERALES_MOVIL'
    ),
    'PARAMETROS QUE SE REQUIEREN PARA EL USO DEL APLICATIVO MOVIL',
    'DIAS_BLOQUEO_BOBINA_DESPACHO_SATELITE',
    '15',
    NULL,
    NULL,
    'Activo',
    'jacarriel',
    SYSDATE,
    '192.168.1.1', 
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL
);


-----------------------------------------------------------------------------------------------------------------
-- Se agrega nueva columna ES_SATELITE en la tabla ADMI_CUADRILLA para validar si son cuadrillas son satelite
-----------------------------------------------------------------------------------------------------------------

ALTER TABLE DB_COMERCIAL.ADMI_CUADRILLA ADD Es_Satelite VARCHAR2(1);
COMMENT ON COLUMN DB_COMERCIAL.ADMI_CUADRILLA.ES_SATELITE IS 'CAMPO QUE DEFINE SI UNA CUADRILLA ES SATELITE(S) O NO (N)';

COMMIT;

-----------------------------------------------------------------------------------------------------------------
-- Se regulariza las cuadrillas satelites actualizando campo ES_SATELITE
-----------------------------------------------------------------------------------------------------------------
DECLARE
    CURSOR cuadrillas_1 IS
    SELECT
		ac.ID_CUADRILLA d,ac.NOMBRE_CUADRILLA, pe.PERSONA_ID
		FROM
				DB_COMERCIAL.ADMI_CUADRILLA ac,DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL pe,DB_COMERCIAL.INFO_PERSONA p
		WHERE
		nombre_cuadrilla IN (
		'ESM PROV TCH INT 01', 'ESM PROV BOR WIFI 01', 'ESM PROV VCH WIFI 01',
		'MNT PROV CHO INS 01', 'MNT PROV CHO INT 01', 'MNT PROV CHO SOP 01',
		'MNT PROV BHI INT 01', 'MNT PROV FLV WIFI 01', 'MNT PROV JAM WIFI 01',
		'MNT PROV JIP SOP 01', 'MNT PROV JIP INT 01', 'MNT PROV PLZ INT 01',
		'QVD PROV LMN INT 01', 'QVD PROV PCH INT 01', 'QVD PROV BLZ INS 01',
		'QVD PROV PAL INT 01', 'QVD PROV VNC INT 01', 'QVD PROV VNC SOP 01',
		'QVD PROV BBY INT 01', 'QVD PROV BBY INS 01', 'QVD PROV BBY SOP 01',
		'QVD PROV BBY SOP 02', 'QVD PROV BBY INS 0S', 'QVD PROV BBY CON 01',
		'QVD PROV VEN INS 01', 'STL PROV PLY SOP 01', 'STL PROV PLY INS 01',
		'STL PROV PLY INS 02', 'STL PROV SLN INS 05', 'STL PROV PLY INT 01',
		'STL PROV ENT INT 01', 'MIL PROV NAR INT 01', 'MIL PROV TRI INT 01',
		'MIL PROV TRI SOP 01', 'MIL PROV DAU INS 01', 'MIL PROV DAU SOP 01',
		'MIL PROV DAU INT 01', 'MIL PROV SMB WIFI 01', 'MIL PROV CUM WIFI 01',
		'MIL PROV PDC WIFI 01') AND pe.CUADRILLA_ID=ac.ID_CUADRILLA
		AND pe.ESTADO = 'Activo' AND pe.PERSONA_ID = p.ID_PERSONA AND p.CARGO ='Jefe Cuadrilla'
	AND ac.ESTADO = 'Activo';

CURSOR cuadrillas_2 IS
   SELECT
		ac.ID_CUADRILLA d,ac.NOMBRE_CUADRILLA, pe.PERSONA_ID
		FROM
				DB_COMERCIAL.ADMI_CUADRILLA ac,DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL pe,DB_COMERCIAL.INFO_PERSONA p
		WHERE
		nombre_cuadrilla IN (
		'LOJ PROV YAN INT 01',
		'LOJ PROV CEL INT 01', 'LOJ PROV CAR INT 01', 'IBA PROV TUL INT 01',
		'IBA PROV ANG INT 01', 'IBA PROV CAY INT 01', 'IBA PROV GBA WIFI 01',
		'IBA PROV SGA WIFI 01', 'RIO PROV TNW INT 01', 'RIO PROV PTZ INT 01',
		'RIO PROV GUA INT 01', 'RIO PROV ALA INT 01', 'CUE PROV ZHU INT 01',
		'CUE PROV STA INT 01', 'CUE PROV SUC INT 01', 'CUE PROV SUC INT 02',
		'CUE PROV GQZ INT 01', 'CUE PROV ONA INT 01', 'CUE PROV SIG WIF 01',
		'CUE PROV AZO WIF 01', 'CUE PROV MAC WIF 01', 'MCH PROV MCH INT 01',
		'MCH PROV GBO INT 01', 'MCH PROV HUA INT 01', 'MCH PROV BAL INT 01',
		'MCH PROV PIN INT 01', 'MCH PROV BLO WIFI 01', 'MCH PROV STR WIFI 01',
		'MCH PROV ZAR WIFI 01', 'STO PROV BAN INT 01', 'STO PROV CAR INT 01',
		'STO PROV CON INT 01', 'STO PROV TAN INT 01', 'STO PROV LZA WIFI 01',
		'STO PROV PTN WIFI 01', 'STO PROV PTQ WIFI 01', 'ESM PROV SL INT 01',
		'ESM PROV QUI INT 01', 'ESM PROV PED INT 01', 'ESM PROV PED SOP 01')
		AND pe.ESTADO = 'Activo' AND pe.CUADRILLA_ID=ac.ID_CUADRILLA 
		AND pe.PERSONA_ID = p.ID_PERSONA AND p.CARGO ='Jefe Cuadrilla'
		AND ac.ESTADO = 'Activo';


	TYPE L_id_Cuadrilla   				IS TABLE OF NUMBER(10); 
    TYPE L_nombre_Cuadrilla   			IS TABLE OF VARCHAR2(50); 
   	TYPE L_id_Persona		   			IS TABLE OF NUMBER(10); 
	V_id_Cuadrilla 						L_id_Cuadrilla;
    V_nombre_Cuadrilla 					L_nombre_Cuadrilla;
   	V_id_Persona 						L_id_Persona;
	Total_tareas CLOB;
	Total_cuadrillas NUMBER(2) 			:= 0; 
	Total_actualizadas NUMBER(2) 		:= 0; 
	Total_pendientes NUMBER(2) 			:= 0;
	i PLS_INTEGER						:= 0;

BEGIN
    OPEN cuadrillas_1;
    LOOP
        FETCH cuadrillas_1 BULK COLLECT INTO V_id_Cuadrilla,V_nombre_Cuadrilla,V_id_Persona LIMIT 100;
        EXIT WHEN V_id_Cuadrilla.count=0;
        i := V_id_Cuadrilla.FIRST;
       	WHILE (i IS NOT NULL) 
        LOOP
        total_cuadrillas := total_cuadrillas + 1;
       
       	total_tareas := DB_SOPORTE.SPKG_GESTION_TAREAS_TYM.COMF_GET_TAREAS_POR_PERSONA(V_id_Persona(i));
       
		IF total_tareas != empty_clob() THEN 
			DBMS_OUTPUT.PUT_LINE('La cuadrilla "' || V_nombre_Cuadrilla(i) || '" tiene tareas pendientes, no se puedo actualizar');
			total_pendientes := total_pendientes + 1;
		ELSE
			UPDATE DB_COMERCIAL.ADMI_CUADRILLA ac SET ac.ES_SATELITE = 'S' WHERE ac.ID_CUADRILLA = V_id_Cuadrilla(i) AND ac.ESTADO = 'Activo';
			total_actualizadas := total_actualizadas + 1;
			total_tareas:= empty_clob();
		END IF;		
		i := V_id_Cuadrilla.NEXT(i);
		END LOOP; 
	END LOOP;
    CLOSE cuadrillas_1;
   
    OPEN cuadrillas_2;
    LOOP
	FETCH cuadrillas_2 BULK COLLECT
		INTO V_id_Cuadrilla,	V_nombre_Cuadrilla,	V_id_Persona LIMIT 100;

	EXIT WHEN V_id_Cuadrilla.count = 0;
	
	i := V_id_Cuadrilla.FIRST;

	WHILE (i IS NOT NULL)
		LOOP
			total_cuadrillas := total_cuadrillas + 1;
		
				total_tareas := DB_SOPORTE.SPKG_GESTION_TAREAS_TYM.COMF_GET_TAREAS_POR_PERSONA(V_id_Persona(i));
		IF total_tareas != empty_clob() THEN 
			DBMS_OUTPUT.PUT_LINE('La cuadrilla "' || V_nombre_Cuadrilla(i) || '" tiene tareas pendientes, no se puedo actualizar');
			total_pendientes := total_pendientes + 1;
		ELSE
			UPDATE	DB_COMERCIAL.ADMI_CUADRILLA ac SET	ac.ES_SATELITE = 'S' WHERE	ac.ID_CUADRILLA = V_id_Cuadrilla(i) AND ac.ESTADO = 'Activo';
			total_actualizadas := total_actualizadas + 1;
			total_tareas := empty_clob();
		END IF;
		
		i := V_id_Cuadrilla.NEXT(i);
		END LOOP;
	END LOOP;
    CLOSE cuadrillas_2;
   
COMMIT;

DBMS_OUTPUT.PUT_LINE('Cuadrillas Totales: ' || total_cuadrillas);
DBMS_OUTPUT.PUT_LINE('Cuadrillas Actualizadas: ' || total_actualizadas);
DBMS_OUTPUT.PUT_LINE('Cuadrillas Pendientes: ' || total_pendientes);
EXCEPTION
	WHEN OTHERS THEN
	  DBMS_OUTPUT.PUT_LINE('Error: '||SQLERRM);
	ROLLBACK;
END;
--=======================================================================
-- Ingreso el detalle para las etiquetas personalizadas por nombre tecnico del producto de MobileBus
--=======================================================================
SET SERVEROUTPUT ON
DECLARE
    --
    TYPE Ltl_Array      IS VARRAY(32) OF VARCHAR2(100);--NUMERO DE REGISTROS VARRAY
    La_Etiquetas        Ltl_Array;
    La_Imagenes         Ltl_Array;
    La_Estados          Ltl_Array;
    Lv_Etiqueta         VARCHAR2(100);
    Lv_Imagen           VARCHAR2(100);
    Lv_Estado           VARCHAR2(100);
    Ln_Total 	        NUMBER;
    --
BEGIN
    --
    La_Etiquetas := Ltl_Array('Placa-Conductor(A)','Lateral-Izquierdo(A)','Lateral-Derecha(A)','Posterior(A)','Interior-Chofer(A)',
                            'Interior-Pasajero(A)','Interior-Posterior(A)','Batería(A)','Km-Combustible(A)','Placa-Conductor(D)',
                            'Lateral-Izquierdo(D)','Lateral-Derecha(D)','Posterior(D)','Interior-Chofer(D)','Interior-Pasajero(D)',
                            'Interior-Posterior(D)','Batería(D)','Km-Combustible(D)','Mdvr','Frontal_c1','Conductor_c2','Pasajeros_c3',
                            'Posterior_c4','Pulsador','Gps','4G','Wifi','SimCard','Disco-Duro','Cableado-Cam','Cableado_+/-','Monitoreo');
    --
    La_Imagenes := Ltl_Array('placa.png','lateral_izq_car.png','lateral_der_car.png','posterior_car.png','interior_chofer_car.png',
                            'interior_pasajero_car.png','interior_posterior_car.png','bateria_car.png','km_combustible_car.png',
                            'placa.png','lateral_izq_car.png','lateral_der_car.png','posterior_car.png','interior_chofer_car.png',
                            'interior_pasajero_car.png','interior_posterior_car.png','bateria_car.png','km_combustible_car.png',
                            'dvr.png','adaptador.png','adaptador.png','adaptador.png','adaptador.png','boton.png','gps.png','4g.png',
                            'wifi indoor.png','sim_card.png','disco_duro.png','cableado_cam.png','cableado_electrico.png','monitoreo.png');
    --
    La_Estados := Ltl_Array('AsignadoTarea','AsignadoTarea','AsignadoTarea','AsignadoTarea','AsignadoTarea','AsignadoTarea','AsignadoTarea',
                            'AsignadoTarea','AsignadoTarea','Activo','Activo','Activo','Activo','Activo','Activo','Activo','Activo','Activo',
                            'Activo','Activo','Activo','Activo','Activo','Activo','Activo','Activo','Activo','Activo','Activo','Activo','Activo',
                            'Activo');
    --
    Ln_Total := La_Etiquetas.count;
    FOR Ln_Index in 1 .. Ln_Total LOOP
        --
        Lv_Etiqueta := La_Etiquetas(Ln_Index);
        Lv_Imagen := La_Imagenes(Ln_Index);
        Lv_Estado := La_Estados(Ln_Index);
        INSERT INTO db_general.admi_parametro_det VALUES 
        (
            db_general.seq_admi_parametro_det.NEXTVAL,
            (
                SELECT
                    id_parametro
                FROM
                    db_general.admi_parametro_cab
                WHERE
                    nombre_parametro = 'ETIQUETA_FOTO'
            ),
            'ETIQUETAS DE LAS FOTOS',
            Lv_Etiqueta,
            Lv_Imagen,
            '128',
            'N',
            'Activo',
            'facaicedo',
            SYSDATE,
            '127.0.0.1',
            NULL,
            NULL,
            NULL,
            NULL,
            NULL,
            Lv_Estado,
            ( SELECT NOMBRE_TECNICO FROM DB_COMERCIAL.ADMI_PRODUCTO
               WHERE DESCRIPCION_PRODUCTO = 'MOBILE BUS' AND EMPRESA_COD = '10' AND ESTADO = 'Activo' ),
            NULL
        );
        DBMS_OUTPUT.PUT_LINE(Lv_Etiqueta);
        --
    END LOOP;
    --se guardan los cambios
    DBMS_OUTPUT.PUT_LINE('Se guadaron los cambios.');
    COMMIT;
    --
EXCEPTION
WHEN OTHERS THEN
    SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' 
                           || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
    DBMS_OUTPUT.PUT_LINE('Se reversan los cambios.');
    --se reversan los cambios
    ROLLBACK;
END;
/

--##############################################################################
--##################  ADMI_PRODUCTO_CARACTERISTICA  ############################
--##############################################################################

DECLARE 

	type array_t IS     varray(200) OF VARCHAR2(200);
	array array_t := array_t('VLAN',
                           'AS_PRIVADO',
                           'IP_LOOPBACK',
                           'IP_WAN_TELEFONICA',
                           'PROTOCOLO_ENRUTAMIENTO',
                           'ENLACE_DATOS',
                           'VRF',
                           'INTERCONEXION_CLIENTES',
                           'DEFAULT_GATEWAY',
                           'TIPO_FACTIBILIDAD',
                           'NO_REQUIERE_FACTIBILIDAD',
                           'TAREA_FWA',
                           'CAPACIDAD1',
                           'CAPACIDAD2',
                           'LOGIN_FWA',
                           'TERCERIZADORA');
BEGIN

  FORALL i IN 1..array.count SAVE EXCEPTIONS
      
	    INSERT
        INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA VALUES
          (
            DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
            (SELECT ID_PRODUCTO
            FROM DB_COMERCIAL.ADMI_PRODUCTO
            WHERE NOMBRE_TECNICO       = 'DATOS FWA'
            AND EMPRESA_COD            = 10
            AND ESTADO                 = 'Activo'
            ),
            (SELECT ID_CARACTERISTICA
            FROM DB_COMERCIAL.ADMI_CARACTERISTICA
            WHERE DESCRIPCION_CARACTERISTICA IN (array(i))
            ),
            SYSDATE,
            NULL,
            'wgaibor',
            NULL,
            'Activo',
            'NO'
          );
  COMMIT;
END;

/

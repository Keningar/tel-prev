/**
 * Script para regularización de reinicio de conteo de servicio.
 *
 * @author Edgar Holguín <eholguin@telconet.ec>
 * @version 1.0 10-03-2022 
 */

  INSERT
  INTO DB_COMERCIAL.INFO_SERVICIO_HISTORIAL
    (
      ID_SERVICIO_HISTORIAL,
      SERVICIO_ID,
      ESTADO,
      OBSERVACION,
      USR_CREACION,
      FE_CREACION,
      ACCION
    )
    VALUES
    (
      DB_COMERCIAL.SEQ_INFO_SERVICIO_HISTORIAL.NEXTVAL,
      543817,
      'Activo',
      'Se reinicio el conteo para la facturación',
      'telcosRegulariza',
      to_timestamp('01/04/21 00:18:05,000000000','DD/MM/RR HH24:MI:SSXFF'),
      'reinicioConteo'
    );

    INSERT INTO DB_COMERCIAL.INFO_SERVICIO_HISTORIAL
     (ID_SERVICIO_HISTORIAL,
     SERVICIO_ID,
     USR_CREACION,
     FE_CREACION,
     IP_CREACION,
     ESTADO,
     MOTIVO_ID,
     ACCION,
     OBSERVACION)
    VALUES 
    (DB_COMERCIAL.SEQ_INFO_SERVICIO_HISTORIAL.NEXTVAL,
     543817,
    'telcosRegulariza',
     SYSDATE,
    '127.0.0.1',
     'Activo',
     null,   
    'conteoFrecuencia',         
    'Se Actualiza los meses restantes: ' ||
    ' <br/><b>Meses_restantes anterior:</b> 2' ||
    ' <br/><b>Meses_restantes nuevo:</b>  1' ||
    ' <br/><b>Fecha de Reinicio de Conteo:</b>  01-04-2021'||
    ' <br/><b>Meses Transcurridos:</b>  11' ||'<br/>' 
    );

    UPDATE DB_COMERCIAL.INFO_SERVICIO set MESES_RESTANTES = 1 WHERE ID_SERVICIO=543817;

COMMIT;

/

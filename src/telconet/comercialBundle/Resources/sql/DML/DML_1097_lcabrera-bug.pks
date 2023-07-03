/**
  * Script DML por características no procesadas.
  * @author Luis Cabrera <lcabrera@telconet.ec>
  * @version 1.0
  * @since 14-09-2018
  */
        UPDATE DB_COMERCIAL.INFO_SERVICIO_CARACTERISTICA
          SET  OBSERVACION = 'Se inactiva la característica por inconsistencia de ciclo |' || OBSERVACION,
               ESTADO = 'Inactivo',
               VALOR = 'N',
               IP_ULT_MOD = '127.0.0.1',
               USR_ULT_MOD = 'telcos_CRS',
               FE_ULT_MOD = SYSDATE
        WHERE TRUNC(FE_FACTURACION) <= TRUNC(SYSDATE)
          AND ESTADO = 'Activo'
          AND VALOR = 'S';

        COMMIT;
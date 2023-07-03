/**
 * Documentación para crear plantilla RPT_SECURE
 *
 * @author Antonio Ayala <afayala@telconet.ec>
 * @version 1.0 10-09-2021
 */

Insert Into  DB_COMUNICACION.ADMI_PLANTILLA
(Id_plantilla,Nombre_Plantilla,Codigo,Modulo,Plantilla,Estado,Fe_creacion,Usr_creacion,Empresa_cod)
Values
(DB_COMUNICACION.SEQ_ADMI_PLANTILLA.nextval,
'Reporte Secure Cpe',
'RPT_SECURE',
'COMERCIAL',
'Estimado usuario, en el presente mail se adjunta el reporte del producto secure cpe generado.
Alguna novedad favor notificar a sistemas.
Telcos +',
'Activo',
SYSDATE,
'afayala',
10);

INSERT
  INTO DB_COMUNICACION.INFO_ALIAS_PLANTILLA
    (
      ID_ALIAS_PLANTILLA,
      ALIAS_ID,
      PLANTILLA_ID,
      ESTADO,
      FE_CREACION,
      USR_CREACION,
      ES_COPIA
    )
    VALUES
    (
      DB_COMUNICACION.SEQ_INFO_ALIAS_PLANTILLA.NEXTVAL,
      18,--security@telconet.ec
      (SELECT ID_PLANTILLA FROM DB_COMUNICACION.ADMI_PLANTILLA WHERE NOMBRE_PLANTILLA = 'Reporte Secure Cpe'
       AND CODIGO = 'RPT_SECURE'),
      'Activo',
      SYSDATE,
      'afayala',
      'NO'
    );

COMMIT;

/

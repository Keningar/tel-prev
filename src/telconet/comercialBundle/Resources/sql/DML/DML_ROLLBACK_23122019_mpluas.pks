/*
* Se crea script de reverso de las caracterisitcas CAMARA 3DEYE Y PARAMETROS.
* @author Marlon Plúas <mpluas@telconet.ec>
* @version 1.0 23-12-2019
*/

DELETE
FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA APC
WHERE APC.PRODUCTO_ID = (SELECT AP.ID_PRODUCTO
                         FROM DB_COMERCIAL.ADMI_PRODUCTO AP
                         WHERE AP.NOMBRE_TECNICO = 'CAMARA IP' AND AP.ESTADO = 'Activo'
                        )
  AND APC.CARACTERISTICA_ID = (SELECT AC.ID_CARACTERISTICA
                               FROM DB_COMERCIAL.ADMI_CARACTERISTICA AC
                               WHERE AC.DESCRIPCION_CARACTERISTICA = 'CAMARA 3DEYE'
                                 AND AC.ESTADO = 'Activo'
                              )
  AND APC.ESTADO = 'Activo';

DELETE
FROM DB_COMERCIAL.ADMI_CARACTERISTICA AC
WHERE AC.DESCRIPCION_CARACTERISTICA = 'CAMARA 3DEYE'
  AND AC.ESTADO = 'Activo';


DELETE
FROM DB_GENERAL.ADMI_PARAMETRO_DET APT
WHERE APT.PARAMETRO_ID = (SELECT APC.ID_PARAMETRO
                          FROM DB_GENERAL.ADMI_PARAMETRO_CAB APC
                          WHERE APC.NOMBRE_PARAMETRO = 'PORTAL 3DEYE'
                            AND APC.ESTADO = 'Activo'
                         )
  AND APT.ESTADO = 'Activo';

DELETE
FROM DB_GENERAL.ADMI_PARAMETRO_CAB APC
WHERE APC.NOMBRE_PARAMETRO = 'PORTAL 3DEYE'
  AND APC.ESTADO = 'Activo';


DELETE
FROM DB_GENERAL.ADMI_PARAMETRO_DET APT
WHERE APT.PARAMETRO_ID = (SELECT APC.ID_PARAMETRO
                          FROM DB_GENERAL.ADMI_PARAMETRO_CAB APC
                          WHERE APC.NOMBRE_PARAMETRO = 'ENDPOINT ROL 3DEYE'
                            AND APC.ESTADO = 'Activo'
                         )
  AND APT.ESTADO = 'Activo';

DELETE
FROM DB_GENERAL.ADMI_PARAMETRO_CAB APC
WHERE APC.NOMBRE_PARAMETRO = 'ENDPOINT ROL 3DEYE'
  AND APC.ESTADO = 'Activo';


DELETE
FROM DB_GENERAL.ADMI_PARAMETRO_DET APT
WHERE APT.PARAMETRO_ID = (SELECT APC.ID_PARAMETRO
                          FROM DB_GENERAL.ADMI_PARAMETRO_CAB APC
                          WHERE APC.NOMBRE_PARAMETRO = 'ENDPOINT CAMARA 3DEYE'
                            AND APC.ESTADO = 'Activo'
                         )
  AND APT.ESTADO = 'Activo';

DELETE
FROM DB_GENERAL.ADMI_PARAMETRO_CAB APC
WHERE APC.NOMBRE_PARAMETRO = 'ENDPOINT CAMARA 3DEYE'
  AND APC.ESTADO = 'Activo';


DELETE
FROM DB_GENERAL.ADMI_PARAMETRO_DET APT
WHERE APT.PARAMETRO_ID = (SELECT APC.ID_PARAMETRO
                          FROM DB_GENERAL.ADMI_PARAMETRO_CAB APC
                          WHERE APC.NOMBRE_PARAMETRO = 'ENDPOINT USER 3DEYE'
                            AND APC.ESTADO = 'Activo'
                         )
  AND APT.ESTADO = 'Activo';

DELETE
FROM DB_GENERAL.ADMI_PARAMETRO_CAB APC
WHERE APC.NOMBRE_PARAMETRO = 'ENDPOINT USER 3DEYE'
  AND APC.ESTADO = 'Activo';

COMMIT;

/



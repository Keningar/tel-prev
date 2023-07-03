/**
 * Documentación para crear características y productos con características
 *
 * @author Antonio Ayala <afayala@telconet.ec>
 * @version 1.0 02-08-2021
 */

--INSERT ADMI_CARACTERISTICA
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA VALUES
                                  ( DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
                                   'ES PARA MIGRACION',
                                   'N',
                                   'Activo',
                                   SYSDATE,
                                   'afayala',
                                   NULL,
                                   NULL,
                                   'COMERCIAL',
                                   NULL);  

--INSERT ADMI_PRODUCTO_CARACTERISTICA
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA VALUES
(
				DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
				(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'SECURE CPE' AND ESTADO = 'Activo'),
				(SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'ES PARA MIGRACION'),
				SYSDATE,
				NULL,
				'afayala',
				NULL,
				'Activo',
				'SI'
); 

--UPDATE ADMI_PRODUCTO
UPDATE DB_COMERCIAL.ADMI_PRODUCTO SET DESCRIPCION_PRODUCTO = 'SECURITY SECURE CPE'
WHERE DESCRIPCION_PRODUCTO = 'SECURE CPE' AND EMPRESA_COD = '10'; 

COMMIT;

/                       


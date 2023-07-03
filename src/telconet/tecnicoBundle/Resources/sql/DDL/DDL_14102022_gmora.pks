/**
 * Documentación para agregar columna de estado.
 *
 * @author Gabriela Mora <gmora@telconet.ec>
 * @version 1.0 14-10-2022
 */
ALTER TABLE DB_INFRAESTRUCTURA.INFO_ESPACIO_FISICO ADD ESTADO VARCHAR2(20); 
COMMENT ON COLUMN DB_INFRAESTRUCTURA.INFO_ESPACIO_FISICO.ESTADO is 'estado del espacio fisico, puede estar activo, pendiente de eliminacion o eliminado';

/


--Creación del tipo para la creación de casos ECUCERT
CREATE OR REPLACE TYPE DB_SOPORTE.CREAR_CASO_ECUCERT_TYPE AS OBJECT (
    Pn_idCaso               INTEGER,
    Pn_idCasoHistorial      INTEGER,
    Pn_idDocumento          INTEGER,
    Pn_idComunicacion       INTEGER,
    Pn_idDocuComunicacion   INTEGER,
    Pn_idDetalleHipotesis   INTEGER,
    Pn_idDetalle            INTEGER,
    Pn_idCriterioAfectado   INTEGER,
    Pn_idParteAfectada      INTEGER,
    Pn_idDetalleCaAsig      INTEGER,
    Pn_idComunicacionCaSig  INTEGER,
    Pn_idCasoHistorialAsig  INTEGER,
    Pn_idCasoAsignacion     INTEGER,
    Pn_idCasoDocumentoAsig  INTEGER,
    Pn_idDocuComunicaAsig   INTEGER
);

/
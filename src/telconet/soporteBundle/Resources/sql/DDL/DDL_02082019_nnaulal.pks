--Se crea la columna para saber si la tarea o caso ya fue procesado

ALTER TABLE DB_SOPORTE.INFO_INCIDENCIA_DET ADD CASO_PROCESADO NUMBER DEFAULT 0;
COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_DET.CASO_PROCESADO  is 'Campo para identificar si el caso fue procesado';

ALTER TABLE DB_SOPORTE.INFO_INCIDENCIA_DET ADD TAREA_PROCESADA NUMBER DEFAULT 0;
COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_DET.TAREA_PROCESADA  is 'Campo para identificar si la tarea fue procesada';

ALTER TABLE DB_SOPORTE.INFO_INCIDENCIA_DET ADD ESCGNAT NUMBER DEFAULT 0;
COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_DET.ESCGNAT  is 'Campo para identificar es un caso de CGNAT';

ALTER TABLE DB_SOPORTE.INFO_INCIDENCIA_DET ADD IPCGNAT VARCHAR2(400);
COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_DET.IPCGNAT  is 'Campo la ip que se obtiene de WS del CGNAT';

ALTER TABLE DB_SOPORTE.INFO_INCIDENCIA_DET ADD ESRDA NUMBER DEFAULT 0;
COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_DET.ESRDA  is 'Campo para identificar es un caso de RDA CWMP';

/

--Modificación del tipo para la actualización del detalle de la incidencia
CREATE OR REPLACE TYPE DB_SOPORTE.INCIDENCIA_NOT_DETALLE_TYPE AS OBJECT (
    Pv_ipAddress          VARCHAR2(800),
    Pn_IncidenciaIdDet    INTEGER,
    Pv_ipCreacion         VARCHAR2(800),
    Pv_feIncidenciaIp     VARCHAR2(800),
    Pv_user               VARCHAR2(800),
    Pv_puerto             VARCHAR2(800),
    Pv_noTicket           VARCHAR2(800),
    Pv_categoria          VARCHAR2(800),
    Pv_subCategoria       VARCHAR2(800),
    Pv_tipoEvento         VARCHAR2(800),
    Pv_ipDestino          VARCHAR2(800),
    Pv_BandCPE            VARCHAR2(800),
    Pv_statusIn           VARCHAR2(800),
    Pv_bandCGNAT          VARCHAR2(800),
    Pv_BandRDA            VARCHAR2(800)
);

/

CREATE OR REPLACE TYPE DB_SOPORTE.INCIDENCIA_ACT_DETALLE_TYPE AS OBJECT (
    Pn_IncidenciaDetId     INTEGER,
    Pn_CasoId              INTEGER,
    Pn_ComunicacionId      INTEGER,
    Pv_PersonaEmpRol       VARCHAR2(800),
    Pv_TipoUsuario         VARCHAR2(800),
    Pn_IdEmpresa           INTEGER,
    Pv_UsrModi             VARCHAR2(800),
    Pv_IpModi              VARCHAR2(800),
    Pn_IdServicio          INTEGER,
    Pn_EsClieCsoc          INTEGER,
    Pn_EsClieSG            INTEGER,
    Pn_EsCPE               INTEGER,
    Pn_bandCGNAT           INTEGER,
    Pn_BandRDA             INTEGER,
    Pv_IpAddressCGNAT      VARCHAR2(400)
);

/


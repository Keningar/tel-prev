DROP INDEX DB_COMERCIAL.INFO_DETALLE_MAPEO_PROMO_IDX7;

CREATE INDEX DB_COMERCIAL.INFO_DETALLE_MAPEO_PROMO_IDX7 ON DB_COMERCIAL.INFO_DETALLE_MAPEO_PROMO
 (ESTADO ASC, EMPRESA_COD ASC, TO_NUMBER(TO_CHAR(FE_MAPEO,'YYYY')) ASC, TO_NUMBER(TO_CHAR(FE_MAPEO,'MM')) ASC, TIPO_PROMOCION ASC);
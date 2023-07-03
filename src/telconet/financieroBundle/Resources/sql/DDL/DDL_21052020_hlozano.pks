/**
 * @author Hector Lozano <hlozano@telconet.ec>
 * @version 1.0
 * @since 21-05-2020    
 * Se crea DDL para tabla DB_FINANCIERO.INFO_DEBITO_CAB.
 * Se crea sentencia DDL para agregar CAMPO 'TIPO_ESCENARIO' y 'FILTRO_ESCENARIO' en tabla DB_FINANCIERO.INFO_DEBITO_CAB.  
 */

--AGREGAR CAMPO 'TIPO_ESCENARIO' EN LA TABLA DB_FINANCIERO.INFO_DEBITO_CAB.
ALTER TABLE DB_FINANCIERO.INFO_DEBITO_CAB
ADD
(
  TIPO_ESCENARIO VARCHAR2(1000)
);

COMMENT ON COLUMN DB_FINANCIERO.INFO_DEBITO_CAB.TIPO_ESCENARIO IS 'Almacena el tipo de escenario para la Generacion de Debito';


--AGREGAR CAMPO 'FILTRO_ESCENARIO' EN LA TABLA DB_FINANCIERO.INFO_DEBITO_CAB.
ALTER TABLE DB_FINANCIERO.INFO_DEBITO_CAB
ADD
(
  FILTRO_ESCENARIO VARCHAR2(1000)
);

COMMENT ON COLUMN DB_FINANCIERO.INFO_DEBITO_CAB.FILTRO_ESCENARIO IS 'Almacena el filtro correspondiente al escenario para la Generacion de Debito';


COMMIT;
/
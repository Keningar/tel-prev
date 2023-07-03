
-- Se crea la solicitud: SOLICITUD MIGRACION DE VLAN y se asocian vlan por PE al cliente NEDETEL


DECLARE
--
BEGIN

-----Crear la caracteristica: SOLICITUD MIGRACION DE VLAN
INSERT INTO db_comercial.admi_tipo_solicitud VALUES (
    db_comercial.seq_admi_tipo_solicitud.nextval,
    'SOLICITUD MIGRACION DE VLAN',
    SYSDATE,
    'rcabrera',
    SYSDATE,
    'rcabrera',
    'Activo',
    NULL,
    NULL,
    NULL
);

-------PE: ro124demayo.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro124demayo.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro124demayo.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro124demayo.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro124demayo.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);


-------PE:ro1alausibb.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1alausibb.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1alausibb.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1alausibb.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1alausibb.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

-------PE:ro1ambato.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1ambato.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1ambato.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1ambato.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1ambato.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

-------PE:ro1balsas.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1balsas.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1balsas.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1balsas.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1balsas.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);


-------PE:ro1balzar.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1balzar.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1balzar.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1balzar.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1balzar.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

-------PE:ro1banos.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1banos.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1banos.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1banos.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1banos.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);


-------PE:ro1catamayo.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1catamayo.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1catamayo.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1catamayo.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1catamayo.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

-------PE:ro1cayambe.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1cayambe.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1cayambe.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1cayambe.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1cayambe.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

-------PE:ro1cotacachi.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1cotacachi.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1cotacachi.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1cotacachi.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1cotacachi.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

-------PE:ro1cumanda.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1cumanda.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1cumanda.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1cumanda.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1cumanda.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);


-------PE:ro1daule.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1daule.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1daule.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1daule.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1daule.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);


-------PE:ro1elchaco.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1elchaco.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1elchaco.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1elchaco.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1elchaco.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

-------PE:ro1elcoca.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1elcoca.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1elcoca.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1elcoca.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1elcoca.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

-------PE:ro1elguabo.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1elguabo.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1elguabo.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1elguabo.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1elguabo.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);


-------PE:ro1elpangui.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1elpangui.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1elpangui.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1elpangui.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1elpangui.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);



-------PE:ro1elpuyo.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1elpuyo.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1elpuyo.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1elpuyo.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1elpuyo.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

-------PE:ro1esmeraldas.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1esmeraldas.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1esmeraldas.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1esmeraldas.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1esmeraldas.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);


-------PE:ro1gualaquiza.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1gualaquiza.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1gualaquiza.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1gualaquiza.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1gualaquiza.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

-------PE:ro1guaranda.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1guaranda.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1guaranda.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1guaranda.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1guaranda.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);


-------PE:ro1ibarra.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1ibarra.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1ibarra.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1ibarra.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1ibarra.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

-------PE:ro1jipijapa.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1jipijapa.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1jipijapa.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1jipijapa.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1jipijapa.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);


-------PE:ro1lagoagrio.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1lagoagrio.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1lagoagrio.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1lagoagrio.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1lagoagrio.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

-------PE:ro1latacunga.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1latacunga.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1latacunga.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1latacunga.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1latacunga.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);


-------PE:ro1macas.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1macas.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1macas.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1macas.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1macas.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);


-------PE:ro1machalabb.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1machalabb.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1machalabb.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1machalabb.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1machalabb.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

-------PE:ro1milagro.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1milagro.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1milagro.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1milagro.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1milagro.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

-------PE:ro1otavalo.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1otavalo.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1otavalo.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1otavalo.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1otavalo.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

-------PE:ro1pedernales.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1pedernales.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1pedernales.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1pedernales.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1pedernales.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);


-------PE:ro1pifo.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1pifo.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1pifo.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1pifo.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1pifo.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

-------PE:ro1pinas.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1pinas.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1pinas.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1pinas.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1pinas.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

-------PE:ro1playas.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1playas.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1playas.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1playas.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1playas.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

-------PE:ro1portoviejo.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1portoviejo.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1portoviejo.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1portoviejo.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1portoviejo.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

-------PE:ro1posorja.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1posorja.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1posorja.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1posorja.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1posorja.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);


-------PE:ro1progreso.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1progreso.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1progreso.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1progreso.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1progreso.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);


-------PE:ro1ptolopezmnt.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1ptolopezmnt.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1ptolopezmnt.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1ptolopezmnt.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1ptolopezmnt.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

-------PE:ro1quininde.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1quininde.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1quininde.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1quininde.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1quininde.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

-------PE:ro1riobamba.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1riobamba.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1riobamba.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1riobamba.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1riobamba.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

-------PE:ro1salcedo.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1salcedo.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1salcedo.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1salcedo.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1salcedo.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);


-------PE:ro1salitre.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1salitre.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1salitre.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1salitre.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1salitre.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

-------PE:ro1santaisabel.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1santaisabel.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1santaisabel.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1santaisabel.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1santaisabel.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);


-------PE:ro1shushufindi.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1shushufindi.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1shushufindi.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1shushufindi.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1shushufindi.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

-------PE:ro1simonbolivar.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1simonbolivar.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1simonbolivar.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1simonbolivar.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1simonbolivar.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);


-------PE:ro1stodomingo.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1stodomingo.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1stodomingo.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1stodomingo.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1stodomingo.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

-------PE:ro1tabacundo.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1tabacundo.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1tabacundo.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1tabacundo.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1tabacundo.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);


-------PE:ro1tena.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1tena.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1tena.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1tena.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1tena.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);


-------PE:Ro1tonsupa.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'Ro1tonsupa.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'Ro1tonsupa.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'Ro1tonsupa.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'Ro1tonsupa.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);



-------PE:ro1tulcan.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1tulcan.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1tulcan.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1tulcan.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1tulcan.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);


-------PE:ro1urcuqui.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1urcuqui.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1urcuqui.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1urcuqui.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1urcuqui.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

-------PE:ro1ventanasbb.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1ventanasbb.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1ventanasbb.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1ventanasbb.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1ventanasbb.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);


-------PE:ro1vinces.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1vinces.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1vinces.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1vinces.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1vinces.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);


-------PE:ro1virgendefatima.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1virgendefatima.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1virgendefatima.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1virgendefatima.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1virgendefatima.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

-------PE:ro1yantzaza.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1yantzaza.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1yantzaza.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1yantzaza.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1yantzaza.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);


-------PE:ro1zamora.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1zamora.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1zamora.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1zamora.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1zamora.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);



-------PE:ro1zaruma.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1zaruma.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1zaruma.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1zaruma.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1zaruma.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);


-------PE:robabahoyo.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'robabahoyo.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'robabahoyo.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'robabahoyo.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'robabahoyo.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);


-------PE:ronaranjalbb.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ronaranjalbb.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ronaranjalbb.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ronaranjalbb.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ronaranjalbb.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);



-------PE:ropalestinabb.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ropalestinabb.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ropalestinabb.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ropalestinabb.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ropalestinabb.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

-------PE:roppilar.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'roppilar.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'roppilar.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'roppilar.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'roppilar.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);


-------PE:rotelconetcuenca1.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'rotelconetcuenca1.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'rotelconetcuenca1.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'rotelconetcuenca1.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'rotelconetcuenca1.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);


-------PE:rotelconetloja1.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'rotelconetloja1.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'rotelconetloja1.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'rotelconetloja1.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'rotelconetloja1.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);


-------PE:rotelconetmanta1.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'rotelconetmanta1.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'rotelconetmanta1.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'rotelconetmanta1.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'rotelconetmanta1.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);


-------PE:rotelconetqvdo1.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'rotelconetqvdo1.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'rotelconetqvdo1.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'rotelconetqvdo1.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'rotelconetqvdo1.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);



-------PE:rotelconetsalinas1.telconet.net

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '42' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'rotelconetsalinas1.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '43' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'rotelconetsalinas1.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '44' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'rotelconetsalinas1.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);

INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    db_comercial.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    751502, --PERSONA_EMPRESA_ROL_ID
    5,
  (select to_char(id_detalle_elemento) from DB_INFRAESTRUCTURA.info_detalle_elemento where detalle_valor = '45' 
    and detalle_descripcion = 'VLAN PE' and detalle_nombre = 'VLAN' 
    and elemento_id = (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'rotelconetsalinas1.telconet.net')),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    '127.0.0.1',
    'Activo',
    null
);


COMMIT;
    

  SYS.DBMS_OUTPUT.PUT_LINE('Insertado Correctamente');
EXCEPTION
WHEN OTHERS THEN
  DBMS_OUTPUT.put_line('Error: '||sqlerrm);  
  ROLLBACK; 
END;

/ 


-- Se parametriza el rango de vlans disponibles para ser utilizadas en el web service de actualización de data (VLAN) en Telcos


DECLARE
  ln_id_param NUMBER := 0;
BEGIN


INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_CAB VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'PARAMETROS PROYECTO SEGMENTACION VLAN',
    'PARAMETROS UTILIZADOS EN EL PROYECTO SEGMENTACION VLAN',
    'INFRAESTRUCTURA',
    'ASIGNAR RECURSOS DE RED',
    'Activo',
    'rcabrera',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL
  );               


  SELECT id_parametro
  INTO ln_id_param
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO = 'PARAMETROS PROYECTO SEGMENTACION VLAN';
    

  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
      ln_id_param,
      'RANGO_VLANS',
      '41',
      '50',
      null,
      null,
      'Activo',
      'rcabrera',
      sysdate,
      '127.0.0.1',
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL      
    );  
    
    
---------------------------------------Se asocian el rango de vlan 41 - 50-------------------------------------------

-------PE: ro1urcuqui.telconet.net

INSERT INTO db_infraestructura.info_detalle_elemento VALUES (
    db_infraestructura.seq_info_detalle_elemento.nextval,
    (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1urcuqui.telconet.net'),
    'VLAN',
    '41',
    'VLAN PE',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    NULL,
    'Activo'
);

INSERT INTO db_infraestructura.info_detalle_elemento VALUES (
    db_infraestructura.seq_info_detalle_elemento.nextval,
    (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1urcuqui.telconet.net'),
    'VLAN',
    '42',
    'VLAN PE',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    NULL,
    'Activo'
);

INSERT INTO db_infraestructura.info_detalle_elemento VALUES (
    db_infraestructura.seq_info_detalle_elemento.nextval,
    (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1urcuqui.telconet.net'),
    'VLAN',
    '43',
    'VLAN PE',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    NULL,
    'Activo'
);

INSERT INTO db_infraestructura.info_detalle_elemento VALUES (
    db_infraestructura.seq_info_detalle_elemento.nextval,
    (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1urcuqui.telconet.net'),
    'VLAN',
    '44',
    'VLAN PE',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    NULL,
    'Activo'
);

INSERT INTO db_infraestructura.info_detalle_elemento VALUES (
    db_infraestructura.seq_info_detalle_elemento.nextval,
    (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1urcuqui.telconet.net'),
    'VLAN',
    '45',
    'VLAN PE',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    NULL,
    'Activo'
);

INSERT INTO db_infraestructura.info_detalle_elemento VALUES (
    db_infraestructura.seq_info_detalle_elemento.nextval,
    (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1urcuqui.telconet.net'),
    'VLAN',
    '46',
    'VLAN PE',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    NULL,
    'Activo'
);

INSERT INTO db_infraestructura.info_detalle_elemento VALUES (
    db_infraestructura.seq_info_detalle_elemento.nextval,
    (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1urcuqui.telconet.net'),
    'VLAN',
    '47',
    'VLAN PE',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    NULL,
    'Activo'
);

INSERT INTO db_infraestructura.info_detalle_elemento VALUES (
    db_infraestructura.seq_info_detalle_elemento.nextval,
    (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1urcuqui.telconet.net'),
    'VLAN',
    '48',
    'VLAN PE',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    NULL,
    'Activo'
);

INSERT INTO db_infraestructura.info_detalle_elemento VALUES (
    db_infraestructura.seq_info_detalle_elemento.nextval,
    (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1urcuqui.telconet.net'),
    'VLAN',
    '49',
    'VLAN PE',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    NULL,
    'Activo'
);

INSERT INTO db_infraestructura.info_detalle_elemento VALUES (
    db_infraestructura.seq_info_detalle_elemento.nextval,
    (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1urcuqui.telconet.net'),
    'VLAN',
    '50',
    'VLAN PE',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    NULL,
    'Activo'
);
    
    
-------PE: ro1simonbolivar.telconet.net
      
INSERT INTO db_infraestructura.info_detalle_elemento VALUES (
    db_infraestructura.seq_info_detalle_elemento.nextval,
    (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1simonbolivar.telconet.net'),
    'VLAN',
    '41',
    'VLAN PE',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    NULL,
    'Activo'
);

INSERT INTO db_infraestructura.info_detalle_elemento VALUES (
    db_infraestructura.seq_info_detalle_elemento.nextval,
    (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1simonbolivar.telconet.net'),
    'VLAN',
    '42',
    'VLAN PE',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    NULL,
    'Activo'
);

INSERT INTO db_infraestructura.info_detalle_elemento VALUES (
    db_infraestructura.seq_info_detalle_elemento.nextval,
    (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1simonbolivar.telconet.net'),
    'VLAN',
    '43',
    'VLAN PE',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    NULL,
    'Activo'
);

INSERT INTO db_infraestructura.info_detalle_elemento VALUES (
    db_infraestructura.seq_info_detalle_elemento.nextval,
    (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1simonbolivar.telconet.net'),
    'VLAN',
    '44',
    'VLAN PE',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    NULL,
    'Activo'
);

INSERT INTO db_infraestructura.info_detalle_elemento VALUES (
    db_infraestructura.seq_info_detalle_elemento.nextval,
    (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1simonbolivar.telconet.net'),
    'VLAN',
    '45',
    'VLAN PE',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    NULL,
    'Activo'
);

INSERT INTO db_infraestructura.info_detalle_elemento VALUES (
    db_infraestructura.seq_info_detalle_elemento.nextval,
    (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1simonbolivar.telconet.net'),
    'VLAN',
    '46',
    'VLAN PE',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    NULL,
    'Activo'
);

INSERT INTO db_infraestructura.info_detalle_elemento VALUES (
    db_infraestructura.seq_info_detalle_elemento.nextval,
    (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1simonbolivar.telconet.net'),
    'VLAN',
    '47',
    'VLAN PE',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    NULL,
    'Activo'
);

INSERT INTO db_infraestructura.info_detalle_elemento VALUES (
    db_infraestructura.seq_info_detalle_elemento.nextval,
    (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1simonbolivar.telconet.net'),
    'VLAN',
    '48',
    'VLAN PE',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    NULL,
    'Activo'
);

INSERT INTO db_infraestructura.info_detalle_elemento VALUES (
    db_infraestructura.seq_info_detalle_elemento.nextval,
    (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1simonbolivar.telconet.net'),
    'VLAN',
    '49',
    'VLAN PE',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    NULL,
    'Activo'
);

INSERT INTO db_infraestructura.info_detalle_elemento VALUES (
    db_infraestructura.seq_info_detalle_elemento.nextval,
    (select id_elemento from DB_INFRAESTRUCTURA.info_elemento where nombre_elemento = 'ro1simonbolivar.telconet.net'),
    'VLAN',
    '50',
    'VLAN PE',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    NULL,
    'Activo'
);  
  
  
---------------------------------------Se asocian el rango de vlan 41 - 50------------------------------------------- 


-----------// Se pone en estado reservado las vlans de la 42 a la 45 que son las mapeadas actualmente//-----------
UPDATE db_infraestructura.info_detalle_elemento ide
SET
    ide.estado = 'Reservada'
WHERE
    ide.id_detalle_elemento IN (
        SELECT
            TO_CHAR(ide2.id_detalle_elemento)
        FROM
            db_infraestructura.info_detalle_elemento ide2
        WHERE
            ide2.detalle_descripcion = 'VLAN PE'
            AND ide2.detalle_nombre = 'VLAN'
            AND ide2.detalle_valor IN (
                '42',
                '43',
                '44',
                '45'
            )
            AND ide2.elemento_id IN (
                SELECT
                    ie1.id_elemento
                FROM
                    db_infraestructura.info_elemento ie1
                WHERE
                    ie1.nombre_elemento IN (
                        'ro124demayo.telconet.net',
                        'ro1alausibb.telconet.net',
                        'ro1ambato.telconet.net',
                        'ro1balsas.telconet.net',
                        'ro1balzar.telconet.net',
                        'ro1banos.telconet.net',
                        'ro1catamayo.telconet.net',
                        'ro1cayambe.telconet.net',
                        'ro1cotacachi.telconet.net',
                        'ro1cumanda.telconet.net',
                        'ro1daule.telconet.net',
                        'ro1elchaco.telconet.net',
                        'ro1elcoca.telconet.net',
                        'ro1elguabo.telconet.net',
                        'ro1elpangui.telconet.net',
                        'ro1elpuyo.telconet.net',
                        'ro1esmeraldas.telconet.net',
                        'ro1gualaquiza.telconet.net',
                        'ro1guaranda.telconet.net',
                        'ro1ibarra.telconet.net',
                        'ro1jipijapa.telconet.net',
                        'ro1lagoagrio.telconet.net',
                        'ro1latacunga.telconet.net',
                        'ro1macas.telconet.net',
                        'ro1machalabb.telconet.net',
                        'ro1milagro.telconet.net',
                        'ro1otavalo.telconet.net',
                        'ro1pedernales.telconet.net',
                        'ro1pifo.telconet.net',
                        'ro1pinas.telconet.net',
                        'ro1playas.telconet.net',
                        'ro1portoviejo.telconet.net',
                        'ro1posorja.telconet.net',
                        'ro1progreso.telconet.net',
                        'ro1ptolopezmnt.telconet.net',
                        'ro1quininde.telconet.net',
                        'ro1riobamba.telconet.net',
                        'ro1salcedo.telconet.net',
                        'ro1salitre.telconet.net',
                        'ro1santaisabel.telconet.net',
                        'ro1shushufindi.telconet.net',
                        'ro1simonbolivar.telconet.net',
                        'ro1stodomingo.telconet.net',
                        'ro1tabacundo.telconet.net',
                        'ro1tena.telconet.net',
                        'Ro1tonsupa.telconet.net',
                        'ro1tulcan.telconet.net',
                        'ro1urcuqui.telconet.net',
                        'ro1ventanasbb.telconet.net',
                        'ro1vinces.telconet.net',
                        'ro1virgendefatima.telconet.net',
                        'ro1yantzaza.telconet.net',
                        'ro1zamora.telconet.net',
                        'ro1zaruma.telconet.net',
                        'robabahoyo.telconet.net',
                        'ronaranjalbb.telconet.net',
                        'ropalestinabb.telconet.net',
                        'roppilar.telconet.net',
                        'rotelconetcuenca1.telconet.net',
                        'rotelconetloja1.telconet.net',
                        'rotelconetmanta1.telconet.net',
                        'rotelconetqvdo1.telconet.net',
                        'rotelconetsalinas1.telconet.net'
                    )));

-------------------------------// Se pone en estado reservado las vlans //--------------------------------------

  
  COMMIT;
    

  SYS.DBMS_OUTPUT.PUT_LINE('Insertado Correctamente');
EXCEPTION
WHEN OTHERS THEN
  DBMS_OUTPUT.put_line('Error: '||sqlerrm);  
  ROLLBACK; 
END;

/ 

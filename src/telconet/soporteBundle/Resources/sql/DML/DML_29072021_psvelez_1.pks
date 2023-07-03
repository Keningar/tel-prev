
/**
 * DEBE EJECUTARSE EN DB_GENERAL
 * Script para crear de motivos de cierre de casos Hal Megadatos
 * @author Pero Velez <psvelez@telconet.ec>
 * @version 1.0 28-07-2021 - Versión Inicial.
 */
DECLARE
 type itemarray IS VARRAY(500) OF VARCHAR2(1000);
 boc1               itemarray;
 Lv_MensajeError    VARCHAR2(4000);
 nivel1             VARCHAR2(200);
 nivel2             VARCHAR2(200);
 tarea              VARCHAR2(200);
 motivo             VARCHAR2(200);
 tareanivel5        VARCHAR2(200);
 idAdmiParanetroCab number := 0;
 idAdmiParanetroDet number := 0;
 idHipotesisFinal   number := 0;
 total              integer;
 
BEGIN
  idAdmiParanetroCab := DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL; 
  
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB VALUES(idAdmiParanetroCab,'MOTIVOS_CATEGORIA_FIN_CASO'
  ,'DEFINE MOTIVOS DE CIERRE DE CASOS QUE ESTAN ASOCIADOS CON CATEGORIA DE TAREA','SOPORTE',NULL
  ,'Activo','psvelez',sysdate,'127.0.0.1',null,null,null);
   
  boc1 := itemarray('CLIENTE|MINI ODF/ROSETA|2613|1665|',
                    'CLIENTE|MINI ODF/ROSETA|2614|1664|',
                    'CLIENTE|MINI ODF/ROSETA|2615|1663|',
                    'CLIENTE|MINI ODF/ROSETA|2616|1666|',
                    'CLIENTE|ATENUACIÓN|6572|1659|',
                    'CLIENTE|PATCHCORD|2612|1669|',
                    'NODO|ODF|6526|146|',
                    'NODO|ODF|2600|1595|',
                    'NODO|ODF|2655|1604|',
                    'NODO|PATCHCORD|2651|1604|',
                    'RED DE DISTRIBUCIÓN|ATENUACIÓN|2604|1640|',
                    'RED DE DISTRIBUCIÓN|ATENUACIÓN|2604|1641|',
                    'RED DE DISTRIBUCIÓN|CAJA BMX/FTTH|2607|143|',
                    'RED DE DISTRIBUCIÓN|CAJA BMX/FTTH|2608|1649|',
                    'RED DE DISTRIBUCIÓN|CAJA BMX/FTTH|2609|1652|',
                    'RED DE DISTRIBUCIÓN|CAJA BMX/FTTH|2609|1653|',
                    'RED DE DISTRIBUCIÓN|CAJA BMX/FTTH|2609|151|',
                    'RED DE DISTRIBUCIÓN|CAJA BMX/FTTH|7489|146|',
                    'RED DE DISTRIBUCIÓN|CAJA BMX/FTTH|7489|147|',
                    'RED DE DISTRIBUCIÓN|CAJA BMX/FTTH|2660|140|',
                    'RED DE DISTRIBUCIÓN|CORTE FIBRA|2603|1288|',
                    'ULTIMA MILLA|CORTE FIBRA|2611|1290|',
                    'ULTIMA MILLA|CORTE FIBRA|2611|1287|',
                    'ULTIMA MILLA|CORTE FIBRA|2611|1314|',
                    'ULTIMA MILLA|CORTE FIBRA|2611|1315|',
                    'ULTIMA MILLA|CORTE FIBRA|2611|179|',
                    'ULTIMA MILLA|CORTE FIBRA|2611|129|',
                    'ULTIMA MILLA|CORTE FIBRA|2611|1267|',
                    'ULTIMA MILLA|CORTE FIBRA|2611|1291|',
                    'ULTIMA MILLA|CORTE FIBRA|2611|301|',
                    'ULTIMA MILLA|CORTE FIBRA|2611|1313|',
                    'ULTIMA MILLA|MINIMANGA|2610|1658|',
                    'ULTIMA MILLA|ATENUACIÓN|6576|116|',
                    'ULTIMA MILLA|ATENUACIÓN|6577|116|'
                    );

    total             := boc1.count;
    FOR i in 1 .. total LOOP      
      nivel1 := SUBSTR(boc1(i),0,(INSTR(boc1(i),'|')-1));
      nivel2 := SUBSTR(boc1(i), (INSTR(boc1(i),'|')+1), LENGTH(boc1(i)) );
      tarea  := SUBSTR(nivel2,(INSTR(nivel2,'|')+1),LENGTH(nivel2));
      nivel2 := SUBSTR(nivel2,0,(INSTR(nivel2,'|')-1));
      motivo := SUBSTR(tarea,INSTR(tarea,'|')+1,LENGTH(tarea)); 
      tarea  := SUBSTR(tarea,0,(INSTR(tarea,'|')-1));
      motivo := SUBSTR(motivo,0,(INSTR(motivo,'|')-1));
      
      idHipotesisFinal := motivo;
      
     -----------------------------------
      idAdmiParanetroDet := DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL;
    
      INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES(idAdmiParanetroDet,idAdmiParanetroCab,'MOTIVOS DE CIERRE DE CASOS HAL',
      TRIM(UPPER(nivel1)),TRIM(UPPER(nivel2)),tarea,idHipotesisFinal,'Activo','psvelez',sysdate,'127.0.0.1',null,null,null,128,null,null,null,null);
      
      nivel1 := '';
      nivel2 := '';
      tarea := '0';
      idHipotesisFinal := 0;
      idAdmiParanetroDet := 0;
      motivo :='';
      
    end loop;

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES(DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,1022,
    'VALOR DE ESPERA EN SEGUNDOS PARA REINTENTO DE CONSUMO DE WEB SERVICE PARA VALIDAR ENLACE HAL',
    'TIEMPO_ESPERA_REINTENTO',45,null,null,'Activo','psvelez',sysdate,'127.0.0.1',null,null,null,null,null,null,null,null);


commit;
 

EXCEPTION
  WHEN OTHERS THEN
    Lv_MensajeError := SQLCODE || ' -ERROR- ' || SQLERRM ;
    
    
    DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR( 'Telcos+',
                                          'MIGRACION ARBOL TAREAS - SYSCLOUD',
                                          Lv_MensajeError,
                                          NVL(SYS_CONTEXT('USERENV','HOST'), 'DB_SOPORTE'),
                                          SYSDATE,
                                          NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'),
                                          '127.0.0.1')
                                        );
END;
/

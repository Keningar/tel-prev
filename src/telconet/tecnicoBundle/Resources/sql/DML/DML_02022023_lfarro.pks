    
  /**
   * DML para llamar a procedimientos de insercion de TIPOS MARCAS Y MODELOS
   *
   * @author Luis Farro <lfarro@telconet.ec>
   * @version 1.0 02-02-2023
   */

declare

begin


--Aqui se hace la llamada al paquete
DB_INFRAESTRUCTURA.INFRK_TRANSACCIONES.P_INS_TIPO_MARCA_MODELO_DET;
DB_INFRAESTRUCTURA.INFRK_TRANSACCIONES.P_INS_TIPO_MARCA_MODELO_EL;


end;
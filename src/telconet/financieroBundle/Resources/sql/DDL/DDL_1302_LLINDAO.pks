-- Add/modify columns 
alter table DB_FINANCIERO.ADMI_CUENTA_CONTABLE add centro_costo VARCHAR2(15) default '000000000' not null;
-- Add comments to the columns 
comment on column DB_FINANCIERO.ADMI_CUENTA_CONTABLE.centro_costo is 'Centro de Costo para las cuenta que apliquen.';

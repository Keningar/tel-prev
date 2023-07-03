-- Add/modify columns 
alter table DB_FINANCIERO.INFO_PAGO_DET add tipo_proceso VARCHAR2(30) default 'Pago' not null;
-- Add comments to the columns 
comment on column DB_FINANCIERO.INFO_PAGO_DET.tipo_proceso is 'Indica el tipo de proceso, por defecto [Pago]';

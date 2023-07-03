-- Add/modify columns 
alter table INFO_DEBITO_GENERAL add CUENTA_CONTABLE_ID NUMBER;
-- Add comments to the columns 
comment on column INFO_DEBITO_GENERAL.CUENTA_CONTABLE_ID is 'Código de cuenta bancaria de empresa.';

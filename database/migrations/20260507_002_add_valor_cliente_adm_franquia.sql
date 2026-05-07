ALTER TABLE `franquia_configuracao`
  ADD COLUMN `valor_cliente_adm` DECIMAL(10,2) NOT NULL DEFAULT 28.65 AFTER `auto_faturar`;


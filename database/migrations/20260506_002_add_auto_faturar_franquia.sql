ALTER TABLE `franquia_configuracao`
    ADD COLUMN `auto_faturar` TINYINT(1) NOT NULL DEFAULT 0 AFTER `gerar_boleto_matriz`;


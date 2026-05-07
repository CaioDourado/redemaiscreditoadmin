ALTER TABLE `franquia_configuracao`
  ADD COLUMN `tipo_faturamento` ENUM('05a05','06a05') NOT NULL DEFAULT '05a05' AFTER `franquia`,
  ADD COLUMN `gerar_boleto_matriz` TINYINT(1) NOT NULL DEFAULT 0 AFTER `tipo_faturamento`;

INSERT INTO `franquia_configuracao` (
  `id_franquia_configuracao`,
  `id_franquia_fk`,
  `nome`,
  `nome_ou_fantasia`,
  `razao_social`,
  `mensalidade`,
  `franquia`,
  `tipo_faturamento`,
  `gerar_boleto_matriz`
)
SELECT
  seed.next_id,
  0,
  'Matriz',
  'Rede Mais Credito',
  'Rede Mais Credito',
  0,
  0,
  '05a05',
  0
FROM (
  SELECT COALESCE(MAX(`id_franquia_configuracao`), 0) + 1 AS next_id
  FROM `franquia_configuracao`
) AS seed
WHERE NOT EXISTS (
  SELECT 1
  FROM (SELECT `id_franquia_configuracao` FROM `franquia_configuracao` WHERE `id_franquia_fk` = 0 LIMIT 1) AS matriz
);

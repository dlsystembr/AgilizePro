ALTER TABLE `faturamento_entrada` 
ADD COLUMN `status` VARCHAR(20) NOT NULL DEFAULT 'pendente' AFTER `observacoes`,
ADD COLUMN `data_fechamento` DATETIME DEFAULT NULL AFTER `status`; 
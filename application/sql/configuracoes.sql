-- Drop table if exists
DROP TABLE IF EXISTS `configuracoes`;

-- Create table
CREATE TABLE `configuracoes` (
  `idConfig` int(11) NOT NULL AUTO_INCREMENT,
  `config` varchar(255) NOT NULL,
  `valor` text,
  PRIMARY KEY (`idConfig`),
  UNIQUE KEY `config` (`config`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default configurations
INSERT INTO `configuracoes` (`config`, `valor`) VALUES
('app_name', 'MapOS'),
('per_page', '10'),
('app_theme', 'white'),
('os_notification', 'todos'),
('email_automatico', '1'),
('control_estoque', '1'),
('control_baixa', '1'),
('control_editos', '1'),
('control_edit_vendas', '1'),
('control_datatable', '1'),
('os_status_list', '["Aberto","Faturado","Negociação","Em Andamento","Orçamento","Finalizado","Cancelado","Aguardando Peças"]'),
('control_2vias', '0'),
('notifica_whats', ''); 
-- Archivo SQL para importar en phpMyAdmin
-- El archivo PHP se coloca en htdocs

CREATE DATABASE IF NOT EXISTS `uriona_taller`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `uriona_taller`;

DROP TABLE IF EXISTS `solicitudes_servicio`;

CREATE TABLE `solicitudes_servicio` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `fecha_hora` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `nombre` VARCHAR(100) NOT NULL,
  `celular` VARCHAR(20) NOT NULL,
  `tipo_vehiculo` VARCHAR(100) NOT NULL,
  `servicio` VARCHAR(100) NOT NULL,
  `descripcion` TEXT NOT NULL,
  `precio_referencial` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `adelanto_25` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `estado_pago` VARCHAR(30) NOT NULL DEFAULT 'Pendiente',
  `estado` VARCHAR(30) NOT NULL DEFAULT 'Pendiente',
  PRIMARY KEY (`id`),
  KEY `idx_fecha_hora` (`fecha_hora`),
  KEY `idx_estado` (`estado`)
) ENGINE=InnoDB
  DEFAULT CHARACTER SET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

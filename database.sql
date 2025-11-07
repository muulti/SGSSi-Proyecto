-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Servidor: db
-- Tiempo de generación: 16-09-2020 a las 16:37:17
-- Versión del servidor: 10.5.5-MariaDB-1:10.5.5+maria~focal
-- Versión de PHP: 7.4.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `database`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL,
  `apellidos` VARCHAR(100) NOT NULL,
  
  `dni` VARCHAR(10) NOT NULL UNIQUE, 
  `telefono` VARCHAR(9) NOT NULL, 
  `fecha_nacimiento` DATE NOT NULL, 
  `email` VARCHAR(150) NOT NULL UNIQUE,
  
  `nombre_usuario` VARCHAR(50) NOT NULL UNIQUE,
  `contrasena` VARCHAR(255) NOT NULL, -- Para almacenar la contraseña hasheada
  
  -- Control de intentos fallidos de login
  `intentos_fallidos` INT DEFAULT 0,
  `bloqueado_hasta` DATETIME DEFAULT NULL,
  
  -- Control de registro
  `fecha_registro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellidos`, `dni`, `telefono`, `fecha_nacimiento`, `email`, `nombre_usuario`, `contrasena`) VALUES
(1, 'David', 'Miguez', '11111111-Z', '622342924', '2005-09-01', 'dmiguez001@ikasle.ehu.eus', 'Juan', '123');

--
-- Tabla para controlar intentos fallidos de login por IP
--
CREATE TABLE `login_attempts` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `ip_address` VARCHAR(45) NOT NULL,
  `intentos` INT DEFAULT 1,
  `bloqueado_hasta` DATETIME DEFAULT NULL,
  `ultimo_intento` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_ip` (`ip_address`),
  INDEX `idx_bloqueado` (`bloqueado_hasta`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Índices para tablas volcadas
--
CREATE TABLE videojuegos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    titulo VARCHAR(100) NOT NULL,
    genero VARCHAR(50),
    plataforma VARCHAR(50),
    fecha_lanzamiento DATE,
    precio DECIMAL(6,2)
);

INSERT INTO videojuegos (titulo, genero, plataforma, fecha_lanzamiento, precio) VALUES
('The Legend of Zelda: Tears of the Kingdom', 'Aventura', 'Nintendo Switch', '2023-05-12', 69.99),
('Elden Ring', 'RPG', 'PlayStation 5', '2022-02-25', 59.99),
('Minecraft', 'Sandbox', 'PC', '2011-11-18', 26.95),
('Cyberpunk 2077', 'Acción', 'PC', '2020-12-10', 49.99),
('Hollow Knight', 'Metroidvania', 'Nintendo Switch', '2018-06-12', 14.99),
('God of War Ragnarök', 'Acción', 'PlayStation 5', '2022-11-09', 69.99),
('Stardew Valley', 'Simulación', 'PC', '2016-02-26', 14.99),
('Red Dead Redemption 2', 'Acción', 'Xbox One', '2018-10-26', 59.99),
('Super Mario Odyssey', 'Plataformas', 'Nintendo Switch', '2017-10-27', 59.99),
('The Witcher 3: Wild Hunt', 'RPG', 'PC', '2015-05-19', 39.99);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

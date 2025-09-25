-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 20-06-2025 a las 05:14:07
-- Versión del servidor: 8.4.3
-- Versión de PHP: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `andercode_diplomas_v6`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `administrador`
--

CREATE TABLE `administrador` (
  `ID_administrador` int NOT NULL,
  `nom_admin` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `ape_paterno` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `ape_materno` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `telefono` varchar(9) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `correo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `sexo` enum('Masculino','Femenino') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `estado` enum('Activo','Inactivo') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Activo',
  `foto` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `administrador`
--


--
-- Disparadores `administrador`
--
DELIMITER $$
CREATE TRIGGER `after_administrador_estado` AFTER UPDATE ON `administrador` FOR EACH ROW BEGIN
    DECLARE admin_actual INT;

    -- Obtener el último ID_administrador (sesión activa)
     SELECT 
        CASE
            WHEN ID_gerente IS NOT NULL THEN NULL
            ELSE ID_administrador
        END
    INTO admin_actual
    FROM sesiones
    WHERE ID_administrador IS NOT NULL OR ID_gerente IS NOT NULL
    ORDER BY ID_sesiones DESC
    LIMIT 1;

    -- Solo si el estado cambió a Inactivo
    IF OLD.estado != NEW.estado AND NEW.estado = 'Inactivo' THEN
        INSERT INTO logs_administracion (
            ID_admin,
            tabla_afectada,
            registro,
            accion,
            old_data,
            new_data,
            fecha
        ) VALUES (
            admin_actual,
            'Administrador',
            OLD.ID_administrador,
            'DELETE',
            CONCAT('Nombre: ', OLD.nom_admin,' | Estado: ', OLD.estado),
            CONCAT('Nombre: ', NEW.nom_admin,' | Estado: ', NEW.estado),
            NOW()
        );
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_administrador_insert` AFTER INSERT ON `administrador` FOR EACH ROW BEGIN
    DECLARE admin_actual INT;

    SELECT 
        CASE
            WHEN ID_gerente IS NOT NULL THEN NULL
            ELSE ID_administrador
        END
    INTO admin_actual
    FROM sesiones
    WHERE ID_administrador IS NOT NULL OR ID_gerente IS NOT NULL
    ORDER BY ID_sesiones DESC
    LIMIT 1;

    INSERT INTO logs_administracion (
        ID_admin, tabla_afectada, registro, accion, new_data, fecha
    ) VALUES (
        admin_actual,
        'Administrador',
        NEW.ID_administrador,
        'INSERT',
        CONCAT('Nombre: ', NEW.nom_admin, ' ', NEW.ape_paterno, ' ', NEW.ape_materno, ', Correo: ', NEW.correo),
        NOW()
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_administrador_update` AFTER UPDATE ON `administrador` FOR EACH ROW BEGIN
    DECLARE admin_actual INT;
    DECLARE cambio_password VARCHAR(2) DEFAULT 'No';

    -- Obtener último admin activo
    SELECT 
        CASE
            WHEN ID_gerente IS NOT NULL THEN NULL
            ELSE ID_administrador
        END
    INTO admin_actual
    FROM sesiones
    WHERE ID_administrador IS NOT NULL OR ID_gerente IS NOT NULL
    ORDER BY ID_sesiones DESC
    LIMIT 1;

    -- Verificar cambio de contraseña
    IF OLD.password != NEW.password THEN
        SET cambio_password = 'Sí';
    END IF;

    -- Comparar campos clave (excepto 'estado')
    IF OLD.nom_admin     != NEW.nom_admin OR
       OLD.ape_paterno   != NEW.ape_paterno OR
       OLD.ape_materno   != NEW.ape_materno OR
       OLD.telefono      != NEW.telefono OR
       OLD.correo        != NEW.correo OR
       OLD.sexo          != NEW.sexo OR
       OLD.foto          != NEW.foto OR
       OLD.password      != NEW.password THEN

        INSERT INTO logs_administracion (
            ID_admin,
            tabla_afectada,
            registro,
            accion,
            old_data,
            new_data,
            fecha
        ) VALUES (
            admin_actual,
            'Administrador',
            OLD.ID_administrador,
            'UPDATE',
            CONCAT(
                'Nombre: ', OLD.nom_admin, ' ', OLD.ape_paterno, ' ', OLD.ape_materno,
                ', Teléfono: ', IFNULL(OLD.telefono, 'Ninguno'),
                ', Correo: ', OLD.correo,
                ', Sexo: ', OLD.sexo,
                ', Cambio contraseña: ', 'No'
            ),
            CONCAT(
                'Nombre: ', NEW.nom_admin, ' ', NEW.ape_paterno, ' ', NEW.ape_materno,
                ', Teléfono: ', IFNULL(NEW.telefono, 'Ninguno'),
                ', Correo: ', NEW.correo,
                ', Sexo: ', NEW.sexo,
                ', Cambio contraseña: ', cambio_password
            ),
            NOW()
        );
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

CREATE TABLE `categoria` (
  `ID_categoria` int NOT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `estado` enum('Activo','Inactivo') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categoria`
--


--
-- Disparadores `categoria`
--
DELIMITER $$
CREATE TRIGGER `after_categoria_estado` AFTER UPDATE ON `categoria` FOR EACH ROW BEGIN
    DECLARE admin_actual INT;

     SELECT 
        CASE
            WHEN ID_gerente IS NOT NULL THEN NULL
            ELSE ID_administrador
        END
    INTO admin_actual
    FROM sesiones
    WHERE ID_administrador IS NOT NULL OR ID_gerente IS NOT NULL
    ORDER BY ID_sesiones DESC
    LIMIT 1;

    IF OLD.estado != NEW.estado THEN
        INSERT INTO logs_administracion (
            ID_admin, tabla_afectada, registro, accion, old_data, new_data, fecha
        ) VALUES (
            admin_actual,
            'Categoria',
            OLD.ID_categoria,
            IF(NEW.estado = 'Inactivo', 'DELETE', 'RESTORE'),
            CONCAT('Nombre: ', OLD.nombre, ' | Estado: ', OLD.estado),
            CONCAT('Nombre: ', NEW.nombre, ' | Estado: ', NEW.estado),
            NOW()
        );
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_categoria_insert` AFTER INSERT ON `categoria` FOR EACH ROW BEGIN
    DECLARE admin_actual INT;

     SELECT 
        CASE
            WHEN ID_gerente IS NOT NULL THEN NULL
            ELSE ID_administrador
        END
    INTO admin_actual
    FROM sesiones
    WHERE ID_administrador IS NOT NULL OR ID_gerente IS NOT NULL
    ORDER BY ID_sesiones DESC
    LIMIT 1;

    INSERT INTO logs_administracion (
        ID_admin, tabla_afectada, registro, accion, new_data, fecha
    ) VALUES (
        admin_actual,
        'Categoria',
        NEW.ID_categoria,
        'INSERT',
        CONCAT('Usuario: ', NEW.nombre, ', Estado: ', NEW.estado),
        NOW()
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_categoria_update` AFTER UPDATE ON `categoria` FOR EACH ROW BEGIN
    DECLARE admin_actual INT;

     SELECT 
        CASE
            WHEN ID_gerente IS NOT NULL THEN NULL
            ELSE ID_administrador
        END
    INTO admin_actual
    FROM sesiones
    WHERE ID_administrador IS NOT NULL OR ID_gerente IS NOT NULL
    ORDER BY ID_sesiones DESC
    LIMIT 1;

    IF (OLD.nombre != NEW.nombre) THEN
        INSERT INTO logs_administracion (
            ID_admin, tabla_afectada, registro, accion, old_data, new_data, fecha
        ) VALUES (
            admin_actual,
            'Categoria',
            OLD.ID_categoria,
            'UPDATE',
            CONCAT('Usuario: ', OLD.nombre),
            CONCAT('Usuario: ', NEW.nombre),
            NOW()
        );
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `certificado`
--

CREATE TABLE `certificado` (
  `ID_certificado` int NOT NULL,
  `ID_usuario` int NOT NULL,
  `ID_curso` int NOT NULL,
  `fecha_emision` date NOT NULL,
  `fecha_vencimiento` date NOT NULL,
  `estado` enum('Activo','Inactivo') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Activo',
  `url_certificado` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `certificado`
--


--
-- Disparadores `certificado`
--
DELIMITER $$
CREATE TRIGGER `after_certificado_estado` AFTER UPDATE ON `certificado` FOR EACH ROW BEGIN
    DECLARE admin_actual INT;
    DECLARE nombre_usuario VARCHAR(255);

    -- Obtener el último ID_administrador (sesión activa)
    SELECT 
        CASE
            WHEN ID_gerente IS NOT NULL THEN NULL
            ELSE ID_administrador
        END
    INTO admin_actual
    FROM sesiones
    WHERE ID_administrador IS NOT NULL OR ID_gerente IS NOT NULL
    ORDER BY ID_sesiones DESC
    LIMIT 1;

    -- Obtener el nombre del curso relacionado al certificado
    SELECT nom_usuario
    INTO nombre_usuario
    FROM usuario
    WHERE ID_usuario = OLD.ID_usuario;

    -- Solo si el estado cambió a Inactivo
    IF OLD.estado != NEW.estado AND NEW.estado = 'Inactivo' THEN
        INSERT INTO logs_administracion (
            ID_admin,
            tabla_afectada,
            registro,
            accion,
            old_data,
            new_data,
            fecha
        ) VALUES (
            admin_actual,
            'Certificado',
            OLD.ID_certificado,
            'DELETE',
            CONCAT('Usuario: ', nombre_usuario, ' | Estado: ', OLD.estado),
            CONCAT('Usuario: ', nombre_usuario, ' | Estado: ', NEW.estado),
            NOW()
        );
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_certificado_insert` AFTER INSERT ON `certificado` FOR EACH ROW BEGIN
    DECLARE admin_actual INT;
    DECLARE nom_usuario_completo VARCHAR(255);
    DECLARE nom_curso VARCHAR(100);

    -- Obtener el último administrador que inició sesión
    SELECT 
        CASE
            WHEN ID_gerente IS NOT NULL THEN NULL
            ELSE ID_administrador
        END
    INTO admin_actual
    FROM sesiones
    WHERE ID_administrador IS NOT NULL OR ID_gerente IS NOT NULL
    ORDER BY ID_sesiones DESC
    LIMIT 1;

    -- Obtener nombre completo del usuario
    SELECT CONCAT(u.nom_usuario, ' ', u.ape_paterno, ' ', u.ape_materno)
    INTO nom_usuario_completo
    FROM usuario u
    WHERE u.ID_usuario = NEW.ID_usuario;

    -- Obtener nombre del curso
    SELECT c.nom_curso
    INTO nom_curso
    FROM curso c
    WHERE c.ID_curso = NEW.ID_curso;

    -- Insertar en log
    INSERT INTO logs_administracion (
        ID_admin,
        tabla_afectada,
        registro,
        accion,
        new_data,
        fecha
    ) VALUES (
        admin_actual,
        'Certificado',
        NEW.ID_certificado,
        'INSERT',
        CONCAT('Usuario: ', nom_usuario_completo,
               ', Curso: ', nom_curso,
               ', Fecha de emisión: ', NEW.fecha_emision,
               ', Fecha de vencimiento: ', NEW.fecha_vencimiento),
        NOW()
    );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `curso`
--

CREATE TABLE `curso` (
  `ID_curso` int NOT NULL,
  `nom_curso` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `ID_categoria` int NOT NULL,
  `ID_instructor` int NOT NULL,
  `foto` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `estado` enum('Activo','Inactivo') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Activo',
  `ruta_certificado` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `temario` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `curso`
--


--
-- Disparadores `curso`
--
DELIMITER $$
CREATE TRIGGER `after_curso_estado` AFTER UPDATE ON `curso` FOR EACH ROW BEGIN
    DECLARE admin_actual INT;

    -- Obtener el último ID_administrador (sesión activa)
     SELECT 
        CASE
            WHEN ID_gerente IS NOT NULL THEN NULL
            ELSE ID_administrador
        END
    INTO admin_actual
    FROM sesiones
    WHERE ID_administrador IS NOT NULL OR ID_gerente IS NOT NULL
    ORDER BY ID_sesiones DESC
    LIMIT 1;

    -- Solo si el estado cambió a Inactivo
    IF OLD.estado != NEW.estado AND NEW.estado = 'Inactivo' THEN
        INSERT INTO logs_administracion (
            ID_admin,
            tabla_afectada,
            registro,
            accion,
            old_data,
            new_data,
            fecha
        ) VALUES (
            admin_actual,
            'Curso',
            OLD.ID_curso,
            'DELETE',
            CONCAT('Nombre: ', OLD.nom_curso,' | Estado: ', OLD.estado),
            CONCAT('Nombre: ', NEW.nom_curso,' | Estado: ', NEW.estado),
            NOW()
        );
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_curso_insert` AFTER INSERT ON `curso` FOR EACH ROW BEGIN
    DECLARE admin_actual INT;

    -- Obtener el administrador más reciente que inició sesión
     SELECT 
        CASE
            WHEN ID_gerente IS NOT NULL THEN NULL
            ELSE ID_administrador
        END
    INTO admin_actual
    FROM sesiones
    WHERE ID_administrador IS NOT NULL OR ID_gerente IS NOT NULL
    ORDER BY ID_sesiones DESC
    LIMIT 1;

    -- Insertar registro en logs_administracion si se encontró un admin
    IF admin_actual IS NOT NULL THEN
        INSERT INTO logs_administracion (
            ID_admin,
            tabla_afectada,
            registro,
            accion,
            new_data,
            fecha
        ) VALUES (
            admin_actual,
            'Curso',
            NEW.ID_curso,
            'INSERT',
            CONCAT(
                'Nombre: ', NEW.nom_curso,
                ', Fecha de Inicio: ', NEW.fecha_inicio,
                ', Fecha de Fin: ', NEW.fecha_fin
            ),
            NOW()
        );
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_curso_update` AFTER UPDATE ON `curso` FOR EACH ROW BEGIN
    DECLARE admin_actual INT;

    -- Obtener el administrador más reciente
     SELECT 
        CASE
            WHEN ID_gerente IS NOT NULL THEN NULL
            ELSE ID_administrador
        END
    INTO admin_actual
    FROM sesiones
    WHERE ID_administrador IS NOT NULL OR ID_gerente IS NOT NULL
    ORDER BY ID_sesiones DESC
    LIMIT 1;

    -- Verificar si alguno de los campos relevantes cambió (excepto estado)
    IF OLD.nom_curso != NEW.nom_curso OR
       OLD.fecha_inicio != NEW.fecha_inicio OR
       OLD.fecha_fin != NEW.fecha_fin OR
       OLD.ID_categoria != NEW.ID_categoria OR
       OLD.ID_instructor != NEW.ID_instructor OR
       OLD.ruta_certificado != NEW.ruta_certificado OR
       OLD.temario != NEW.temario OR
       OLD.foto != NEW.foto THEN

        INSERT INTO logs_administracion (
            ID_admin,
            tabla_afectada,
            registro,
            accion,
            old_data,
            new_data,
            fecha
        ) VALUES (
            admin_actual,
            'Curso',
            OLD.ID_curso,
            'UPDATE',
            CONCAT(
                'Nombre: ', OLD.nom_curso,
                ', Inicio: ', OLD.fecha_inicio,
                ', Fin: ', OLD.fecha_fin,
                ', Categoría: ', OLD.ID_categoria,
                ', Instructor: ', OLD.ID_instructor
            ),
            CONCAT(
                'Nombre: ', NEW.nom_curso,
                ', Inicio: ', NEW.fecha_inicio,
                ', Fin: ', NEW.fecha_fin,
                ', Categoría: ', NEW.ID_categoria,
                ', Instructor: ', NEW.ID_instructor
            ),
            NOW()
        );
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gerente`
--

CREATE TABLE `gerente` (
  `ID_gerente` int NOT NULL,
  `nom_admin` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `ape_paterno` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `ape_materno` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `telefono` varchar(9) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `correo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `sexo` enum('Masculino','Femenino') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `foto` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `gerente`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `instructor`
--

CREATE TABLE `instructor` (
  `ID_instructor` int NOT NULL,
  `nom_instructor` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `ape_paterno` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `ape_materno` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `correo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `telefono` varchar(9) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `foto` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `estado` enum('Activo','Inactivo') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `instructor`
--


--
-- Disparadores `instructor`
--
DELIMITER $$
CREATE TRIGGER `after_instructor_estado` AFTER UPDATE ON `instructor` FOR EACH ROW BEGIN
    DECLARE admin_actual INT;

     SELECT 
        CASE
            WHEN ID_gerente IS NOT NULL THEN NULL
            ELSE ID_administrador
        END
    INTO admin_actual
    FROM sesiones
    WHERE ID_administrador IS NOT NULL OR ID_gerente IS NOT NULL
    ORDER BY ID_sesiones DESC
    LIMIT 1;

    IF OLD.estado != NEW.estado THEN
        INSERT INTO logs_administracion (
            ID_admin, tabla_afectada, registro, accion, old_data, new_data, fecha
        ) VALUES (
            admin_actual,
            'Instructor',
            OLD.ID_instructor,
            IF(NEW.estado = 'Inactivo', 'DELETE', 'RESTORE'),
            CONCAT('Nombre: ', OLD.nom_instructor, ' ', OLD.ape_paterno, ' ', OLD.ape_materno, ' | Estado: ', OLD.estado),
            CONCAT('Nombre: ', NEW.nom_instructor, ' ', NEW.ape_paterno, ' ', NEW.ape_materno, ' | Estado: ', NEW.estado),
            NOW()
        );
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_instructor_insert` AFTER INSERT ON `instructor` FOR EACH ROW BEGIN
    DECLARE admin_actual INT;

     SELECT 
        CASE
            WHEN ID_gerente IS NOT NULL THEN NULL
            ELSE ID_administrador
        END
    INTO admin_actual
    FROM sesiones
    WHERE ID_administrador IS NOT NULL OR ID_gerente IS NOT NULL
    ORDER BY ID_sesiones DESC
    LIMIT 1;

    INSERT INTO logs_administracion (
        ID_admin, tabla_afectada, registro, accion, new_data, fecha
    ) VALUES (
        admin_actual,
        'Instructor',
        NEW.ID_instructor,
        'INSERT',
        CONCAT('Nombre: ', NEW.nom_instructor, ' ', NEW.ape_paterno, ' ', NEW.ape_materno, ', Correo: ', NEW.correo),
        NOW()
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_instructor_update` AFTER UPDATE ON `instructor` FOR EACH ROW BEGIN
    DECLARE admin_actual INT;

     SELECT 
        CASE
            WHEN ID_gerente IS NOT NULL THEN NULL
            ELSE ID_administrador
        END
    INTO admin_actual
    FROM sesiones
    WHERE ID_administrador IS NOT NULL OR ID_gerente IS NOT NULL
    ORDER BY ID_sesiones DESC
    LIMIT 1;

    IF OLD.nom_instructor != NEW.nom_instructor OR
       OLD.ape_paterno != NEW.ape_paterno OR
       OLD.ape_materno != NEW.ape_materno OR
       OLD.correo != NEW.correo OR
       OLD.telefono != NEW.telefono OR
       OLD.foto != NEW.foto THEN
       
        INSERT INTO logs_administracion (
            ID_admin, tabla_afectada, registro, accion, old_data, new_data, fecha
        ) VALUES (
            admin_actual,
            'Instructor',
            OLD.ID_instructor,
            'UPDATE',
            CONCAT('Nombre: ', OLD.nom_instructor, ' ', OLD.ape_paterno, ' ', OLD.ape_materno, ', Correo: ', OLD.correo),
            CONCAT('Nombre: ', NEW.nom_instructor, ' ', NEW.ape_paterno, ' ', NEW.ape_materno, ', Correo: ', NEW.correo),
            NOW()
        );
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `logs_administracion`
--

CREATE TABLE `logs_administracion` (
  `ID_logs` int NOT NULL,
  `ID_admin` int DEFAULT NULL,
  `tabla_afectada` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `registro` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `accion` enum('INSERT','UPDATE','DELETE') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `old_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `new_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `fecha` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `logs_administracion`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permiso`
--

CREATE TABLE `permiso` (
  `ID_permiso` int NOT NULL,
  `ID_administrador` int NOT NULL,
  `tipo_permiso` tinyint NOT NULL,
  `fecha_asignacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `permiso`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sesiones`
--

CREATE TABLE `sesiones` (
  `ID_sesiones` int NOT NULL,
  `ID_administrador` int DEFAULT NULL,
  `ID_gerente` int DEFAULT NULL,
  `login_time` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sesiones`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `ID_usuario` int NOT NULL,
  `nom_usuario` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `ape_paterno` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `ape_materno` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `telefono` varchar(9) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sexo` enum('Masculino','Femenino') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `dni` varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `correo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `rol` enum('Admin','Usuario') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `foto` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `estado` enum('Activo','Inactivo') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

--
-- Disparadores `usuario`
--
DELIMITER $$
CREATE TRIGGER `after_usuario_estado` AFTER UPDATE ON `usuario` FOR EACH ROW BEGIN
    DECLARE admin_actual INT;

    -- Obtener el último ID_administrador sólo si NO hay un gerente
    SELECT 
        CASE
            WHEN ID_gerente IS NOT NULL THEN NULL
            ELSE ID_administrador
        END
    INTO admin_actual
    FROM sesiones
    WHERE ID_administrador IS NOT NULL OR ID_gerente IS NOT NULL
    ORDER BY ID_sesiones DESC
    LIMIT 1;

    -- Solo si el estado cambió a Inactivo
    IF OLD.estado != NEW.estado AND NEW.estado = 'Inactivo' THEN
        INSERT INTO logs_administracion (
            ID_admin,
            tabla_afectada,
            registro,
            accion,
            old_data,
            new_data,
            fecha
        ) VALUES (
            admin_actual,
            'Usuario',
            OLD.ID_usuario,
            'DELETE',
            CONCAT('Nombre: ', OLD.nom_usuario, ' ', OLD.ape_paterno, ' ', OLD.ape_materno, ' | Estado: ', OLD.estado),
            CONCAT('Nombre: ', NEW.nom_usuario, ' ', NEW.ape_paterno, ' ', NEW.ape_materno, ' | Estado: ', NEW.estado),
            NOW()
        );
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_usuario_insert` AFTER INSERT ON `usuario` FOR EACH ROW BEGIN
    DECLARE admin_actual INT;

    -- Obtener el último ID_administrador (puede ser NULL)
     SELECT 
        CASE
            WHEN ID_gerente IS NOT NULL THEN NULL
            ELSE ID_administrador
        END
    INTO admin_actual
    FROM sesiones
    WHERE ID_administrador IS NOT NULL OR ID_gerente IS NOT NULL
    ORDER BY ID_sesiones DESC
    LIMIT 1;

    -- Insertar log
    INSERT INTO logs_administracion (
        ID_admin,
        tabla_afectada,
        registro,
        accion,
        new_data,
        fecha
    ) VALUES (
        admin_actual,  -- Puede ser NULL si no hay sesión de administrador
        'Usuario',
        NEW.ID_usuario,
        'INSERT',
        CONCAT('Nombre: ', NEW.nom_usuario, ', Correo: ', NEW.correo),
        NOW()
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_usuario_update` AFTER UPDATE ON `usuario` FOR EACH ROW BEGIN
    DECLARE admin_actual INT;

    -- Obtener el último ID_administrador de sesiones
     SELECT 
        CASE
            WHEN ID_gerente IS NOT NULL THEN NULL
            ELSE ID_administrador
        END
    INTO admin_actual
    FROM sesiones
    WHERE ID_administrador IS NOT NULL OR ID_gerente IS NOT NULL
    ORDER BY ID_sesiones DESC
    LIMIT 1;

    -- Si algún campo ha cambiado, insertar en logs
    IF OLD.nom_usuario != NEW.nom_usuario OR
       OLD.ape_paterno != NEW.ape_paterno OR
       OLD.ape_materno != NEW.ape_materno OR
       OLD.telefono != NEW.telefono OR
       OLD.sexo != NEW.sexo OR
       OLD.dni != NEW.dni OR
       OLD.correo != NEW.correo OR
       OLD.rol != NEW.rol OR
       OLD.foto != NEW.foto THEN

        INSERT INTO logs_administracion (
            ID_admin,
            tabla_afectada,
            registro,
            accion,
            old_data,
            new_data,
            fecha
        ) VALUES (
            admin_actual,
            'Usuario',
            OLD.ID_usuario,
            'UPDATE',
            CONCAT(
                'Nombre: ', OLD.nom_usuario, ', Apellidos: ', OLD.ape_paterno, ' ', OLD.ape_materno,
                ', Teléfono: ', IFNULL(OLD.telefono, 'NULL'), ', Sexo: ', OLD.sexo,
                ', DNI: ', OLD.dni, ', Correo: ', OLD.correo,
                ', Rol: ', OLD.rol, ', Foto: ', IFNULL(OLD.foto, 'NULL')
            ),
            CONCAT(
                'Nombre: ', NEW.nom_usuario, ', Apellidos: ', NEW.ape_paterno, ' ', NEW.ape_materno,
                ', Teléfono: ', IFNULL(NEW.telefono, 'NULL'), ', Sexo: ', NEW.sexo,
                ', DNI: ', NEW.dni, ', Correo: ', NEW.correo,
                ', Rol: ', NEW.rol, ', Foto: ', IFNULL(NEW.foto, 'NULL')
            ),
            NOW()
        );
    END IF;
END
$$
DELIMITER ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `administrador`
--
ALTER TABLE `administrador`
  ADD PRIMARY KEY (`ID_administrador`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- Indices de la tabla `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`ID_categoria`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `certificado`
--
ALTER TABLE `certificado`
  ADD PRIMARY KEY (`ID_certificado`),
  ADD KEY `ID_usuario` (`ID_usuario`),
  ADD KEY `ID_curso` (`ID_curso`);

--
-- Indices de la tabla `curso`
--
ALTER TABLE `curso`
  ADD PRIMARY KEY (`ID_curso`),
  ADD KEY `ID_categoria` (`ID_categoria`),
  ADD KEY `ID_instructor` (`ID_instructor`);

--
-- Indices de la tabla `gerente`
--
ALTER TABLE `gerente`
  ADD PRIMARY KEY (`ID_gerente`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- Indices de la tabla `instructor`
--
ALTER TABLE `instructor`
  ADD PRIMARY KEY (`ID_instructor`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- Indices de la tabla `logs_administracion`
--
ALTER TABLE `logs_administracion`
  ADD PRIMARY KEY (`ID_logs`),
  ADD KEY `ID_admin` (`ID_admin`);

--
-- Indices de la tabla `permiso`
--
ALTER TABLE `permiso`
  ADD PRIMARY KEY (`ID_permiso`),
  ADD KEY `ID_administrador` (`ID_administrador`);

--
-- Indices de la tabla `sesiones`
--
ALTER TABLE `sesiones`
  ADD PRIMARY KEY (`ID_sesiones`),
  ADD KEY `ID_administrador` (`ID_administrador`),
  ADD KEY `ID_gerente` (`ID_gerente`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`ID_usuario`),
  ADD UNIQUE KEY `dni` (`dni`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `administrador`
--
ALTER TABLE `administrador`
  MODIFY `ID_administrador` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT de la tabla `categoria`
--
ALTER TABLE `categoria`
  MODIFY `ID_categoria` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

--
-- AUTO_INCREMENT de la tabla `certificado`
--
ALTER TABLE `certificado`
  MODIFY `ID_certificado` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=179;

--
-- AUTO_INCREMENT de la tabla `curso`
--
ALTER TABLE `curso`
  MODIFY `ID_curso` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT de la tabla `gerente`
--
ALTER TABLE `gerente`
  MODIFY `ID_gerente` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `instructor`
--
ALTER TABLE `instructor`
  MODIFY `ID_instructor` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=639;

--
-- AUTO_INCREMENT de la tabla `logs_administracion`
--
ALTER TABLE `logs_administracion`
  MODIFY `ID_logs` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=162;

--
-- AUTO_INCREMENT de la tabla `permiso`
--
ALTER TABLE `permiso`
  MODIFY `ID_permiso` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `sesiones`
--
ALTER TABLE `sesiones`
  MODIFY `ID_sesiones` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `ID_usuario` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=184;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `certificado`
--
ALTER TABLE `certificado`
  ADD CONSTRAINT `certificado_ibfk_1` FOREIGN KEY (`ID_usuario`) REFERENCES `usuario` (`ID_usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `certificado_ibfk_2` FOREIGN KEY (`ID_curso`) REFERENCES `curso` (`ID_curso`) ON DELETE CASCADE;

--
-- Filtros para la tabla `curso`
--
ALTER TABLE `curso`
  ADD CONSTRAINT `curso_ibfk_1` FOREIGN KEY (`ID_categoria`) REFERENCES `categoria` (`ID_categoria`) ON DELETE CASCADE,
  ADD CONSTRAINT `curso_ibfk_2` FOREIGN KEY (`ID_instructor`) REFERENCES `instructor` (`ID_instructor`) ON DELETE CASCADE;

--
-- Filtros para la tabla `logs_administracion`
--
ALTER TABLE `logs_administracion`
  ADD CONSTRAINT `logs_administracion_ibfk_1` FOREIGN KEY (`ID_admin`) REFERENCES `administrador` (`ID_administrador`);

--
-- Filtros para la tabla `permiso`
--
ALTER TABLE `permiso`
  ADD CONSTRAINT `permiso_ibfk_1` FOREIGN KEY (`ID_administrador`) REFERENCES `administrador` (`ID_administrador`) ON DELETE CASCADE;

--
-- Filtros para la tabla `sesiones`
--
ALTER TABLE `sesiones`
  ADD CONSTRAINT `sesiones_ibfk_1` FOREIGN KEY (`ID_administrador`) REFERENCES `administrador` (`ID_administrador`) ON DELETE SET NULL,
  ADD CONSTRAINT `sesiones_ibfk_2` FOREIGN KEY (`ID_gerente`) REFERENCES `gerente` (`ID_gerente`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

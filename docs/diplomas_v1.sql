-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: db
-- Tiempo de generación: 07-03-2025 a las 17:01:55
-- Versión del servidor: 8.0.41
-- Versión de PHP: 8.2.27

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

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `ActualizarUsuario` (IN `p_ID_usuario` INT, IN `p_nom_usuario` VARCHAR(100), IN `p_ape_paterno` VARCHAR(100), IN `p_ape_materno` VARCHAR(100), IN `p_telefono` CHAR(9), IN `p_sexo` ENUM('Masculino','Femenino'), IN `p_dni` CHAR(8), IN `p_correo` VARCHAR(255), IN `p_rol` ENUM('Admin','Usuario'), IN `p_ID_admin` INT)   BEGIN
    DECLARE admin_permisos INT;
    DECLARE old_data TEXT;

    
    IF NOT EXISTS (SELECT 1 FROM Usuario WHERE ID_usuario = p_ID_usuario) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: El usuario no existe.';
    END IF;

    
    SELECT perm_escritura INTO admin_permisos 
    FROM Permiso 
    WHERE ID_administrador = p_ID_admin;

    IF admin_permisos IS NULL OR admin_permisos = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: No tienes permisos para modificar usuarios.';
    END IF;

    
    IF LENGTH(p_dni) != 8 OR p_dni NOT REGEXP '^[0-9]{8}$' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: El DNI debe contener exactamente 8 dígitos.';
    END IF;

    
    IF LENGTH(p_telefono) != 9 OR p_telefono NOT REGEXP '^[0-9]{9}$' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: El teléfono debe contener exactamente 9 dígitos.';
    END IF;

    
    IF p_correo NOT REGEXP '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\\.[A-Za-z]{2,}$' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: El correo electrónico no tiene un formato válido.';
    END IF;

    
    SELECT CONCAT('Nombre:', nom_usuario, ', Apellido:', ape_paterno, ', Correo:', correo)
    INTO old_data
    FROM Usuario WHERE ID_usuario = p_ID_usuario;

    
    UPDATE Usuario 
    SET nom_usuario = p_nom_usuario,
        ape_paterno = p_ape_paterno,
        ape_materno = p_ape_materno,
        telefono = p_telefono,
        sexo = p_sexo,
        dni = p_dni,
        correo = p_correo,
        rol = p_rol
    WHERE ID_usuario = p_ID_usuario;

    
    INSERT INTO logs_administracion (ID_admin, tabla_afectada, registro, accion, old_data, new_data, fecha)
    VALUES (
        p_ID_admin,
        'Usuario',
        p_ID_usuario,
        'UPDATE',
        old_data,
        CONCAT('Nombre:', p_nom_usuario, ', Apellido:', p_ape_paterno, ', Correo:', p_correo),
        NOW()
    );
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `DesactivarAdministrador` (IN `p_ID_admin` INT)   BEGIN
    
    IF (SELECT COUNT(*) FROM Administrador WHERE ID_administrador = p_ID_admin AND estado = 'Activo') = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: El administrador no existe o ya está inactivo.';
    END IF;

    
    UPDATE Administrador SET estado = 'Inactivo' WHERE ID_administrador = p_ID_admin;

    
    INSERT INTO logs_administracion (ID_admin, tabla_afectada, registro, accion, old_data)
    VALUES (
        (SELECT ID_gerente FROM Gerente LIMIT 1),
        'Administrador',
        p_ID_admin,
        'DELETE',
        (SELECT CONCAT('Admin: ', nom_admin, ', Correo: ', correo) FROM Administrador WHERE ID_administrador = p_ID_admin)
    );
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `EliminarAdministrador` (IN `p_ID_admin` INT)   BEGIN
    
    IF (SELECT COUNT(*) FROM Administrador WHERE ID_administrador = p_ID_admin AND estado = 'Activo') = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: El administrador no existe o ya está inactivo.';
    END IF;

    
    UPDATE Administrador SET estado = 'Inactivo' WHERE ID_administrador = p_ID_admin;

    
    INSERT INTO logs_administracion (ID_admin, tabla_afectada, registro, accion, old_data)
    VALUES (
        p_ID_admin,
        'Administrador',
        p_ID_admin,
        'DELETE',
        (SELECT CONCAT('Nombre:', nom_admin, ', Correo:', correo) FROM Administrador WHERE ID_administrador = p_ID_admin)
    );
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `EliminarCategoria` (IN `p_ID_categoria` INT, IN `p_ID_admin` INT)   BEGIN
    DECLARE admin_permisos INT;

    
    SELECT perm_escritura INTO admin_permisos FROM permiso WHERE ID_administrador = p_ID_admin;

    IF admin_permisos IS NULL OR admin_permisos = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: No tienes permisos para eliminar categorías.';
    END IF;

    
    IF (SELECT COUNT(*) FROM categoria WHERE ID_categoria = p_ID_categoria AND estado = 'ACTIVO') = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: La categoría no existe o ya está inactiva.';
    END IF;

    
    UPDATE categoria SET estado = 'INACTIVO' WHERE ID_categoria = p_ID_categoria;

    
    INSERT INTO logs_administracion (ID_admin, tabla_afectada, registro, accion, old_data)
    VALUES (
        p_ID_admin,
        'Categoria',
        p_ID_categoria,
        'UPDATE',
        (SELECT CONCAT('Nombre:', nombre) FROM categoria WHERE ID_categoria = p_ID_categoria)
    );
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `EliminarCertificado` (IN `p_ID_certificado` INT, IN `p_ID_admin` INT)   BEGIN
    DECLARE admin_permisos INT;

    
    SELECT perm_escritura INTO admin_permisos FROM permiso WHERE ID_administrador = p_ID_admin;

    IF admin_permisos IS NULL OR admin_permisos = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: No tienes permisos para eliminar certificados.';
    END IF;

    
    IF (SELECT COUNT(*) FROM certificado WHERE ID_certificado = p_ID_certificado AND estado = 'ACTIVO') = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: El certificado no existe o ya está inactivo.';
    END IF;

    
    UPDATE certificado SET estado = 'INACTIVO' WHERE ID_certificado = p_ID_certificado;

    
    INSERT INTO logs_administracion (ID_admin, tabla_afectada, registro, accion, old_data)
    VALUES (
        p_ID_admin,
        'Certificado',
        p_ID_certificado,
        'UPDATE',
        (SELECT CONCAT('Usuario ID:', ID_usuario, ', Curso ID:', ID_curso) FROM certificado WHERE ID_certificado = p_ID_certificado)
    );
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `EliminarCurso` (IN `p_ID_curso` INT, IN `p_ID_admin` INT)   BEGIN
    DECLARE admin_permisos INT;

   
    SELECT perm_escritura INTO admin_permisos FROM permiso WHERE ID_administrador = p_ID_admin;

    IF admin_permisos IS NULL OR admin_permisos = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: No tienes permisos para eliminar cursos.';
    END IF;

    
    IF (SELECT COUNT(*) FROM curso WHERE ID_curso = p_ID_curso AND estado = 'ACTIVO') = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: El curso no existe o ya está inactivo.';
    END IF;

    
    UPDATE curso SET estado = 'INACTIVO' WHERE ID_curso = p_ID_curso;

    
    INSERT INTO logs_administracion (ID_admin, tabla_afectada, registro, accion, old_data)
    VALUES (
        p_ID_admin,
        'Curso',
        p_ID_curso,
        'UPDATE',
        (SELECT CONCAT('Nombre:', nom_curso, ', ID:', ID_curso) FROM curso WHERE ID_curso = p_ID_curso)
    );
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `EliminarInstructor` (IN `p_ID_instructor` INT, IN `p_ID_admin` INT)   BEGIN
    DECLARE admin_permisos INT;

    
    SELECT perm_escritura INTO admin_permisos FROM permiso WHERE ID_administrador = p_ID_admin;

    IF admin_permisos IS NULL OR admin_permisos = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: No tienes permisos para eliminar instructores.';
    END IF;

    
    IF (SELECT COUNT(*) FROM instructor WHERE ID_instructor = p_ID_instructor AND estado = 'ACTIVO') = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: El instructor no existe o ya está inactivo.';
    END IF;

    
    UPDATE instructor SET estado = 'INACTIVO' WHERE ID_instructor = p_ID_instructor;

    
    INSERT INTO logs_administracion (ID_admin, tabla_afectada, registro, accion, old_data)
    VALUES (
        p_ID_admin,
        'Instructor',
        p_ID_instructor,
        'UPDATE',
        (SELECT CONCAT('Nombre:', nom_instructor) FROM instructor WHERE ID_instructor = p_ID_instructor)
    );
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `EliminarUsuario` (IN `p_ID_usuario` INT, IN `p_ID_admin` INT)   BEGIN
    DECLARE admin_permisos INT;

    
    SELECT perm_escritura INTO admin_permisos FROM permiso WHERE ID_administrador = p_ID_admin;

    IF admin_permisos IS NULL OR admin_permisos = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: No tienes permisos para eliminar usuarios.';
    END IF;

    
    IF (SELECT COUNT(*) FROM usuario WHERE ID_usuario = p_ID_usuario AND estado = 'ACTIVO') = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: El usuario no existe o ya está inactivo.';
    END IF;

    -- Cambiar estado a INACTIVO
    UPDATE usuario SET estado = 'INACTIVO' WHERE ID_usuario = p_ID_usuario;

    
    INSERT INTO logs_administracion (ID_admin, tabla_afectada, registro, accion, old_data)
    VALUES (
        p_ID_admin,
        'Usuario',
        p_ID_usuario,
        'UPDATE',
        (SELECT CONCAT('Nombre:', nom_usuario, ', Correo:', correo) FROM usuario WHERE ID_usuario = p_ID_usuario)
    );
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `InsertarCertificado` (IN `p_ID_usuario` INT, IN `p_ID_curso` INT, IN `p_fecha_emision` DATE, IN `p_fecha_vencimiento` DATE, IN `p_ruta_certificado` VARCHAR(255))   BEGIN
    
    IF p_fecha_emision >= p_fecha_vencimiento THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: La fecha de emisión debe ser menor a la fecha de vencimiento.';
    END IF;

    
    INSERT INTO Certificado (ID_usuario, ID_curso, fecha_emision, fecha_vencimiento, ruta_certificado)
    VALUES (p_ID_usuario, p_ID_curso, p_fecha_emision, p_fecha_vencimiento, p_ruta_certificado);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `InsertarUsuario` (IN `p_nom_usuario` VARCHAR(100), IN `p_ape_paterno` VARCHAR(100), IN `p_ape_materno` VARCHAR(100), IN `p_telefono` CHAR(9), IN `p_sexo` ENUM('Masculino','Femenino'), IN `p_dni` CHAR(8), IN `p_correo` VARCHAR(255), IN `p_rol` ENUM('Admin','Usuario'))   BEGIN
    
    IF LENGTH(p_dni) != 8 OR p_dni NOT REGEXP '^[0-9]{8}$' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: El DNI debe contener exactamente 8 dígitos.';
    END IF;

    
    IF LENGTH(p_telefono) != 9 OR p_telefono NOT REGEXP '^[0-9]{9}$' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: El teléfono debe contener exactamente 9 dígitos.';
    END IF;

    
    IF p_correo NOT REGEXP '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\\.[A-Za-z]{2,}$' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: El correo electrónico no tiene un formato válido.';
    END IF;

    
    INSERT INTO Usuario (nom_usuario, ape_paterno, ape_materno, telefono, sexo, dni, correo, rol)
    VALUES (p_nom_usuario, p_ape_paterno, p_ape_materno, p_telefono, p_sexo, p_dni, p_correo, p_rol);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ListarCategorias` ()   BEGIN
    SELECT nombre AS nom_categoria
    FROM Categoria;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ListarCertificados` ()   BEGIN
    SELECT 
        c.nom_curso, 
        u.nom_usuario, 
        ce.fecha_emision AS fecha_inicio, 
        ce.fecha_vencimiento AS fecha_fin, 
        i.nom_instructor, 
        ce.ruta_certificado
    FROM Certificado ce
    INNER JOIN Curso c ON ce.ID_curso = c.ID_curso
    INNER JOIN Usuario u ON ce.ID_usuario = u.ID_usuario
    INNER JOIN Instructor i ON c.ID_instructor = i.ID_instructor;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ListarCursos` ()   BEGIN
    SELECT 
        cat.nombre AS categoria, 
        c.nom_curso, c.fecha_inicio, c.fecha_fin, 
        i.nom_instructor
    FROM Curso c
    INNER JOIN Categoria cat ON c.ID_categoria = cat.ID_categoria
    INNER JOIN Instructor i ON c.ID_instructor = i.ID_instructor;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ListarInstructores` ()   BEGIN
    SELECT nom_instructor, ape_paterno, ape_materno, correo, telefono
    FROM Instructor;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ListarUsuarios` ()   BEGIN
    SELECT nom_usuario, ape_paterno, ape_materno, correo, telefono, rol
    FROM Usuario;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ObtenerCursosUsuario` (IN `usuario_id` INT)   BEGIN
    SELECT 
        c.nom_curso, c.fecha_inicio, c.fecha_fin, 
        i.nom_instructor, ce.ruta_certificado
    FROM Certificado ce
    INNER JOIN Curso c ON ce.ID_curso = c.ID_curso
    INNER JOIN Instructor i ON c.ID_instructor = i.ID_instructor
    WHERE ce.ID_usuario = usuario_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `RegistrarAdministrador` (IN `p_nom_admin` VARCHAR(100), IN `p_ape_paterno` VARCHAR(100), IN `p_ape_materno` VARCHAR(100), IN `p_telefono` VARCHAR(9), IN `p_correo` VARCHAR(255), IN `p_sexo` ENUM('Masculino','Femenino'), IN `p_password` VARCHAR(255), IN `p_perm_lectura` BOOLEAN, IN `p_perm_escritura` BOOLEAN)   BEGIN
    
    INSERT INTO Administrador (nom_admin, ape_paterno, ape_materno, telefono, correo, sexo, password, estado)
    VALUES (p_nom_admin, p_ape_paterno, p_ape_materno, p_telefono, p_correo, p_sexo, p_password, 'Activo');
    
    
    SET @last_admin_id = LAST_INSERT_ID();

    
    INSERT INTO Permiso (ID_administrador, perm_lectura, perm_escritura)
    VALUES (@last_admin_id, p_perm_lectura, p_perm_escritura);
    
    
    INSERT INTO logs_administracion (ID_admin, tabla_afectada, registro, accion, new_data)
    VALUES (
        (SELECT ID_gerente FROM Gerente LIMIT 1),
        'Administrador',
        @last_admin_id,
        'INSERT',
        CONCAT('Admin: ', p_nom_admin, ', Permisos: Lectura(', p_perm_lectura, '), Escritura(', p_perm_escritura, ')')
    );
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `RegistrarNuevoAdministrador` (IN `p_nom_admin` VARCHAR(100), IN `p_ape_paterno` VARCHAR(100), IN `p_ape_materno` VARCHAR(100), IN `p_telefono` VARCHAR(15), IN `p_correo` VARCHAR(255), IN `p_sexo` ENUM('Masculino','Femenino'), IN `p_password` VARCHAR(255))   BEGIN
    
    UPDATE Administrador SET estado = 'Inactivo' WHERE estado = 'Activo';

    
    INSERT INTO Administrador (nom_admin, ape_paterno, ape_materno, telefono, correo, sexo, password, estado)
    VALUES (p_nom_admin, p_ape_paterno, p_ape_materno, p_telefono, p_correo, p_sexo, p_password, 'Activo');
    
    
    INSERT INTO logs_administracion (ID_admin, tabla_afectada, registro, accion, new_data)
    VALUES (
        (SELECT ID_administrador FROM Administrador WHERE estado = 'Activo' LIMIT 1),
        'Administrador',
        (SELECT ID_administrador FROM Administrador WHERE estado = 'Activo' LIMIT 1),
        'INSERT',
        CONCAT('Nuevo admin: ', p_nom_admin, ' ', p_ape_paterno, ', Correo: ', p_correo)
    );
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_Admin_CrearCategoria` (IN `p_nombre` VARCHAR(100))   BEGIN
    INSERT INTO Categoria (nombre) VALUES (p_nombre);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_Admin_CrearCurso` (IN `p_nom_curso` VARCHAR(100), IN `p_ID_categoria` INT, IN `p_ID_instructor` INT, IN `p_fecha_inicio` DATE, IN `p_fecha_fin` DATE, IN `p_foto` VARCHAR(255))   BEGIN
    INSERT INTO Curso (nom_curso, ID_categoria, ID_instructor, fecha_inicio, fecha_fin, foto) 
    VALUES (p_nom_curso, p_ID_categoria, p_ID_instructor, p_fecha_inicio, p_fecha_fin, p_foto);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_Admin_CrearInstructor` (IN `p_nom_instructor` VARCHAR(100), IN `p_ape_paterno` VARCHAR(100), IN `p_ape_materno` VARCHAR(100), IN `p_correo` VARCHAR(255), IN `p_telefono` CHAR(9))   BEGIN
    INSERT INTO Instructor (nom_instructor, ape_paterno, ape_materno, correo, telefono) 
    VALUES (p_nom_instructor, p_ape_paterno, p_ape_materno, p_correo, p_telefono);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_Admin_EditarCategoria` (IN `p_ID_categoria` INT, IN `p_nombre` VARCHAR(100))   BEGIN
    UPDATE Categoria SET nombre = p_nombre WHERE ID_categoria = p_ID_categoria;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_Admin_EditarCurso` (IN `p_ID_curso` INT, IN `p_nom_curso` VARCHAR(100), IN `p_ID_categoria` INT, IN `p_ID_instructor` INT, IN `p_fecha_inicio` DATE, IN `p_fecha_fin` DATE, IN `p_foto` VARCHAR(255))   BEGIN
    UPDATE Curso 
    SET nom_curso = p_nom_curso, ID_categoria = p_ID_categoria, 
        ID_instructor = p_ID_instructor, fecha_inicio = p_fecha_inicio, 
        fecha_fin = p_fecha_fin, foto = p_foto
    WHERE ID_curso = p_ID_curso;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_Admin_EditarInstructor` (IN `p_ID_instructor` INT, IN `p_nom_instructor` VARCHAR(100), IN `p_ape_paterno` VARCHAR(100), IN `p_ape_materno` VARCHAR(100), IN `p_correo` VARCHAR(255), IN `p_telefono` CHAR(9))   BEGIN
    UPDATE Instructor 
    SET nom_instructor = p_nom_instructor, ape_paterno = p_ape_paterno, 
        ape_materno = p_ape_materno, correo = p_correo, telefono = p_telefono
    WHERE ID_instructor = p_ID_instructor;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_Admin_EliminarCategoria` (IN `p_ID_categoria` INT)   BEGIN
    DELETE FROM Categoria WHERE ID_categoria = p_ID_categoria;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_Admin_EliminarCertificado` (IN `p_ID_certificado` INT)   BEGIN
    DELETE FROM Certificado WHERE ID_certificado = p_ID_certificado;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_Admin_EliminarCurso` (IN `p_ID_curso` INT)   BEGIN
    DELETE FROM Curso WHERE ID_curso = p_ID_curso;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_Admin_EliminarInstructor` (IN `p_ID_instructor` INT)   BEGIN
    DELETE FROM Instructor WHERE ID_instructor = p_ID_instructor;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_Gerente_AsignarPermiso` (IN `p_ID_administrador` INT, IN `p_perm_lectura` BOOLEAN, IN `p_perm_escritura` BOOLEAN)   BEGIN
    INSERT INTO Permiso (ID_administrador, perm_lectura, perm_escritura, fecha_asignacion) 
    VALUES (p_ID_administrador, p_perm_lectura, p_perm_escritura, NOW());
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_RegistrarSesion` (IN `p_ID_administrador` INT, IN `p_ID_gerente` INT)   BEGIN
    
    INSERT INTO Sesiones (ID_administrador, ID_gerente, login_time)
    VALUES (p_ID_administrador, p_ID_gerente, NOW());
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `ValidarPermisoEscritura` (IN `p_ID_admin` INT)   BEGIN
    DECLARE permiso INT;
    SELECT perm_escritura INTO permiso FROM permiso WHERE ID_administrador = p_ID_admin;
    IF permiso IS NULL OR permiso = 0 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Error: No tienes permisos de escritura.';
    END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `administrador`
--

CREATE TABLE `administrador` (
  `ID_administrador` int NOT NULL,
  `nom_admin` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `ape_paterno` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `ape_materno` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `telefono` varchar(9) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `correo` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `sexo` enum('Masculino','Femenino') COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `estado` enum('Activo','Inactivo') COLLATE utf8mb4_general_ci DEFAULT 'Activo',
  `foto` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `administrador`
--

INSERT INTO `administrador` (`ID_administrador`, `nom_admin`, `ape_paterno`, `ape_materno`, `telefono`, `correo`, `sexo`, `password`, `estado`, `foto`) VALUES
(1, 'Martin', 'Puertas', 'Cuadros', '987654321', 'admin@consigueventas.com', 'Masculino', 'admin123', 'Activo', NULL),
(8, 'Juan', 'Perez', 'Lopez', '987654321', 'juanperez@test.com', 'Masculino', 'admin123', 'Activo', NULL),
(9, 'Carlos Modificado', 'Ramirez', 'Gomez', '923456789', 'carlos.modificado@example.com', 'Masculino', 'password123', 'Inactivo', NULL);

--
-- Disparadores `administrador`
--
DELIMITER $$
CREATE TRIGGER `trg_ad_administrador` BEFORE DELETE ON `administrador` FOR EACH ROW BEGIN
    UPDATE administrador SET estado = 'INACTIVO' WHERE ID_administrador = OLD.ID_administrador;
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Operación bloqueada: Se actualizó el estado del administrador en lugar de eliminarlo.';
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_ai_administrador` AFTER INSERT ON `administrador` FOR EACH ROW BEGIN
    INSERT INTO logs_administracion (ID_admin, tabla_afectada, registro, accion, new_data, fecha)
    VALUES (NEW.ID_administrador, 'Administrador', NEW.ID_administrador, 'INSERT',
            CONCAT('Nombre:', NEW.nom_admin, ', Correo:', NEW.correo), NOW());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_au_administrador` AFTER UPDATE ON `administrador` FOR EACH ROW BEGIN
    INSERT INTO logs_administracion (ID_admin, tabla_afectada, registro, accion, old_data, new_data, fecha)
    VALUES (NEW.ID_administrador, 'Administrador', NEW.ID_administrador, 'UPDATE',
            CONCAT('Nombre:', OLD.nom_admin, ', Correo:', OLD.correo),
            CONCAT('Nombre:', NEW.nom_admin, ', Correo:', NEW.correo), NOW());
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

CREATE TABLE `categoria` (
  `ID_categoria` int NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `estado` enum('Activo','Inactivo') COLLATE utf8mb4_general_ci DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categoria`
--

INSERT INTO `categoria` (`ID_categoria`, `nombre`, `estado`) VALUES
(1, 'Programación', 'Activo'),
(2, 'Marketing', 'Inactivo'),
(3, 'Negocios', 'Activo'),
(4, 'Categoria de Prueba', 'Activo');

--
-- Disparadores `categoria`
--
DELIMITER $$
CREATE TRIGGER `trg_ad_categoria` BEFORE DELETE ON `categoria` FOR EACH ROW BEGIN
    UPDATE categoria SET estado = 'INACTIVO' WHERE ID_categoria = OLD.ID_categoria;
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Operación bloqueada: Se actualizó el estado de la categoría en lugar de eliminarla.';
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
  `ruta_certificado` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `estado` enum('Activo','Inactivo') COLLATE utf8mb4_general_ci DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `certificado`
--

INSERT INTO `certificado` (`ID_certificado`, `ID_usuario`, `ID_curso`, `fecha_emision`, `fecha_vencimiento`, `ruta_certificado`, `estado`) VALUES
(1, 1, 1, '2024-04-30', '2025-04-30', 'certificados/juan_html5.pdf', 'Activo'),
(2, 2, 2, '2024-04-20', '2025-04-20', 'certificados/ana_wordpress.pdf', 'Activo');

--
-- Disparadores `certificado`
--
DELIMITER $$
CREATE TRIGGER `trg_ad_certificado` BEFORE DELETE ON `certificado` FOR EACH ROW BEGIN
    UPDATE Certificado 
    SET estado = 'INACTIVO' 
    WHERE id_certificado = OLD.id_certificado;
    
    INSERT INTO logs_administracion (ID_admin, tabla_afectada, registro, accion, old_data, fecha)
    VALUES (NULL, 'Certificado', OLD.id_certificado, 'UPDATE',
            CONCAT('ID Usuario:', OLD.id_usuario, ', ID Curso:', OLD.id_curso, ', Estado cambiado a INACTIVO'), NOW());
    
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No se permite eliminar registros, solo cambiar estado a INACTIVO.';
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_ai_certificado` AFTER INSERT ON `certificado` FOR EACH ROW BEGIN
    INSERT INTO logs_administracion (ID_admin, tabla_afectada, registro, accion, new_data, fecha)
    VALUES (NULL, 'Certificado', NEW.ID_certificado, 'INSERT',
            CONCAT('Usuario:', NEW.ID_usuario, ', Curso:', NEW.ID_curso, ', Fecha Emisión:', NEW.fecha_emision), NOW());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_au_certificado` AFTER UPDATE ON `certificado` FOR EACH ROW BEGIN
    INSERT INTO logs_administracion (ID_admin, tabla_afectada, registro, accion, old_data, new_data, fecha)
    VALUES (NULL, 'Certificado', NEW.ID_certificado, 'UPDATE',
            CONCAT('Usuario:', OLD.ID_usuario, ', Curso:', OLD.ID_curso, ', Fecha Emisión:', OLD.fecha_emision),
            CONCAT('Usuario:', NEW.ID_usuario, ', Curso:', NEW.ID_curso, ', Fecha Emisión:', NEW.fecha_emision), NOW());
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `curso`
--

CREATE TABLE `curso` (
  `ID_curso` int NOT NULL,
  `nom_curso` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `ID_categoria` int NOT NULL,
  `ID_instructor` int NOT NULL,
  `foto` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `estado` enum('Activo','Inactivo') COLLATE utf8mb4_general_ci DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `curso`
--

INSERT INTO `curso` (`ID_curso`, `nom_curso`, `fecha_inicio`, `fecha_fin`, `ID_categoria`, `ID_instructor`, `foto`, `estado`) VALUES
(1, 'Curso de Python Avanzado', '2025-01-01', '2025-06-01', 2, 1, 'imagen.png', 'Activo'),
(2, 'Curso de WordPress', '2024-03-25', '2024-04-20', 1, 2, NULL, 'Activo'),
(3, 'Curso de Figma', '2024-03-05', '2024-03-30', 2, 1, NULL, 'Activo');

--
-- Disparadores `curso`
--
DELIMITER $$
CREATE TRIGGER `trg_ad_curso` BEFORE DELETE ON `curso` FOR EACH ROW BEGIN
    UPDATE curso SET estado = 'INACTIVO' WHERE ID_curso = OLD.ID_curso;
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Operación bloqueada: Se actualizó el estado del curso en lugar de eliminarlo.';
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_ai_curso` AFTER INSERT ON `curso` FOR EACH ROW BEGIN
    DECLARE instructor_nombre VARCHAR(100);

    
    SELECT nom_instructor INTO instructor_nombre 
    FROM Instructor 
    WHERE ID_instructor = NEW.ID_instructor;

    
    INSERT INTO logs_administracion (ID_admin, tabla_afectada, registro, accion, new_data, fecha)
    VALUES (NULL, 'Curso', NEW.ID_curso, 'INSERT',
            CONCAT('Curso:', NEW.nom_curso, ', Instructor:', instructor_nombre), NOW());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_au_curso` AFTER UPDATE ON `curso` FOR EACH ROW BEGIN
    DECLARE old_instructor VARCHAR(100);
    DECLARE new_instructor VARCHAR(100);

    
    SELECT nom_instructor INTO old_instructor FROM Instructor WHERE ID_instructor = OLD.ID_instructor;
    SELECT nom_instructor INTO new_instructor FROM Instructor WHERE ID_instructor = NEW.ID_instructor;

    
    INSERT INTO logs_administracion (ID_admin, tabla_afectada, registro, accion, old_data, new_data, fecha)
    VALUES (NULL, 'Curso', NEW.ID_curso, 'UPDATE',
            CONCAT('Curso:', OLD.nom_curso, ', Instructor:', old_instructor),
            CONCAT('Curso:', NEW.nom_curso, ', Instructor:', new_instructor), NOW());
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `descargas_certificados`
--

CREATE TABLE `descargas_certificados` (
  `ID_descarga` int NOT NULL,
  `ID_certificado` int NOT NULL,
  `ID_usuario` int NOT NULL,
  `fecha_descarga` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gerente`
--

CREATE TABLE `gerente` (
  `ID_gerente` int NOT NULL,
  `nom_admin` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `ape_paterno` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `ape_materno` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `telefono` varchar(9) COLLATE utf8mb4_general_ci NOT NULL,
  `correo` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `sexo` enum('Masculino','Femenino') COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `foto` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `instructor`
--

CREATE TABLE `instructor` (
  `ID_instructor` int NOT NULL,
  `nom_instructor` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `ape_paterno` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `ape_materno` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `correo` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `telefono` varchar(9) COLLATE utf8mb4_general_ci NOT NULL,
  `foto` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `estado` enum('Activo','Inactivo') COLLATE utf8mb4_general_ci DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `instructor`
--

INSERT INTO `instructor` (`ID_instructor`, `nom_instructor`, `ape_paterno`, `ape_materno`, `correo`, `telefono`, `foto`, `estado`) VALUES
(1, 'Ricardo', 'Palma', 'Flores', 'ricardo.palma@test.com', '965478321', NULL, 'Activo'),
(2, 'Toulouse', 'Lautrec', 'Pérez', 'toulouse.lautrec@test.com', '985642157', NULL, 'Inactivo');

--
-- Disparadores `instructor`
--
DELIMITER $$
CREATE TRIGGER `trg_ad_instructor` BEFORE DELETE ON `instructor` FOR EACH ROW BEGIN
    UPDATE instructor SET estado = 'INACTIVO' WHERE ID_instructor = OLD.ID_instructor;
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Operación bloqueada: Se actualizó el estado del instructor en lugar de eliminarlo.';
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_ai_instructor` AFTER INSERT ON `instructor` FOR EACH ROW BEGIN
    INSERT INTO logs_administracion (ID_admin, tabla_afectada, registro, accion, new_data, fecha)
    VALUES (NULL, 'Instructor', NEW.ID_instructor, 'INSERT',
            CONCAT('Nombre:', NEW.nom_instructor, ', Correo:', NEW.correo), NOW());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_au_instructor` AFTER UPDATE ON `instructor` FOR EACH ROW BEGIN
    INSERT INTO logs_administracion (ID_admin, tabla_afectada, registro, accion, old_data, new_data, fecha)
    VALUES (NULL, 'Instructor', NEW.ID_instructor, 'UPDATE',
            CONCAT('Nombre:', OLD.nom_instructor, ', Correo:', OLD.correo),
            CONCAT('Nombre:', NEW.nom_instructor, ', Correo:', NEW.correo), NOW());
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
  `tabla_afectada` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `registro` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `accion` enum('INSERT','UPDATE','DELETE') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `old_data` text COLLATE utf8mb4_general_ci,
  `new_data` text COLLATE utf8mb4_general_ci,
  `fecha` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `logs_administracion`
--

INSERT INTO `logs_administracion` (`ID_logs`, `ID_admin`, `tabla_afectada`, `registro`, `accion`, `old_data`, `new_data`, `fecha`) VALUES
(1, 1, 'Usuario', '100', 'UPDATE', 'Nombre:Carlos, Correo:carlos@test.com', 'Nombre:Carlos Modificado, Correo:carlos.modificado@test.com', '2025-03-05 20:09:11'),
(2, 1, 'Usuario', '100', 'UPDATE', 'Nombre:Carlos, Apellido:Ramirez, Correo:carlos@test.com', 'Nombre:Carlos Modificado, Apellido:Ramirez, Correo:carlos.modificado@test.com', '2025-03-05 20:09:11'),
(3, 9, 'Administrador', '9', 'INSERT', NULL, 'Nombre:Carlos, Correo:carlos@example.com', '2025-03-06 15:30:02'),
(4, 9, 'Administrador', '9', 'UPDATE', 'Nombre:Carlos, Correo:carlos@example.com', 'Nombre:Carlos Modificado, Correo:carlos.modificado@example.com', '2025-03-06 15:31:39'),
(5, 9, 'Administrador', '9', 'UPDATE', 'Nombre:Carlos Modificado, Correo:carlos.modificado@example.com', 'Nombre:Carlos Modificado, Correo:carlos.modificado@example.com', '2025-03-06 15:34:42'),
(6, 1, 'Usuario', '2', 'UPDATE', 'Nombre:Ana, Correo:ana.garcia@gmail.com', 'Nombre:Ana, Correo:ana.garcia@gmail.com', '2025-03-07 13:45:11'),
(7, NULL, 'Usuario', '2', 'UPDATE', 'Nombre:Ana, Correo:ana.garcia@gmail.com', 'Nombre:Ana, Correo:ana.garcia@gmail.com', '2025-03-07 13:45:11'),
(8, 1, 'Usuario', '2', 'UPDATE', 'Nombre:Ana, Correo:ana.garcia@gmail.com', NULL, '2025-03-07 13:45:11'),
(9, 1, 'Categoria', '2', 'UPDATE', 'Nombre:Marketing', NULL, '2025-03-07 13:45:40'),
(10, NULL, 'Instructor', '2', 'UPDATE', 'Nombre:Toulouse, Correo:toulouse.lautrec@test.com', 'Nombre:Toulouse, Correo:toulouse.lautrec@test.com', '2025-03-07 13:46:02'),
(11, 1, 'Instructor', '2', 'UPDATE', 'Nombre:Toulouse', NULL, '2025-03-07 13:46:02');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permiso`
--

CREATE TABLE `permiso` (
  `ID_permiso` int NOT NULL,
  `ID_administrador` int NOT NULL,
  `perm_lectura` tinyint(1) DEFAULT '0',
  `perm_escritura` tinyint(1) DEFAULT '0',
  `fecha_asignacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `permiso`
--

INSERT INTO `permiso` (`ID_permiso`, `ID_administrador`, `perm_lectura`, `perm_escritura`, `fecha_asignacion`) VALUES
(1, 1, 1, 1, '2025-03-05 20:07:50'),
(2, 8, 1, 0, '2025-03-05 20:07:50');

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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `ID_usuario` int NOT NULL,
  `nom_usuario` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `ape_paterno` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `ape_materno` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `telefono` varchar(9) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sexo` enum('Masculino','Femenino') COLLATE utf8mb4_general_ci NOT NULL,
  `dni` varchar(8) COLLATE utf8mb4_general_ci NOT NULL,
  `correo` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `rol` enum('Admin','Usuario') COLLATE utf8mb4_general_ci NOT NULL,
  `foto` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `estado` enum('Activo','Inactivo') COLLATE utf8mb4_general_ci DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`ID_usuario`, `nom_usuario`, `ape_paterno`, `ape_materno`, `telefono`, `sexo`, `dni`, `correo`, `rol`, `foto`, `estado`) VALUES
(1, 'Juan', 'Pérez', 'López', '999888777', 'Masculino', '12345678', 'juan.perez@gmail.com', 'Usuario', NULL, 'Activo'),
(2, 'Ana', 'García', 'Mendoza', '987654321', 'Femenino', '87654321', 'ana.garcia@gmail.com', 'Usuario', NULL, 'Inactivo'),
(100, 'Carlos Modificado', 'Ramirez', 'Gomez', '923456789', 'Masculino', '87651234', 'carlos.modificado@test.com', 'Usuario', NULL, 'Activo');

--
-- Disparadores `usuario`
--
DELIMITER $$
CREATE TRIGGER `after_usuario_update` AFTER UPDATE ON `usuario` FOR EACH ROW BEGIN
    INSERT INTO logs_administracion (ID_admin, tabla_afectada, registro, accion, old_data, new_data)
    VALUES (
        (SELECT ID_administrador FROM Administrador LIMIT 1), -- Admin activo
        'Usuario',
        OLD.ID_usuario,
        'UPDATE',
        CONCAT('Nombre:', OLD.nom_usuario, ', Correo:', OLD.correo),
        CONCAT('Nombre:', NEW.nom_usuario, ', Correo:', NEW.correo)
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_ad_usuario` BEFORE DELETE ON `usuario` FOR EACH ROW BEGIN
    UPDATE usuario SET estado = 'INACTIVO' WHERE ID_usuario = OLD.ID_usuario;
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Operación bloqueada: Se actualizó el estado del usuario en lugar de eliminarlo.';
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_ai_usuario` AFTER INSERT ON `usuario` FOR EACH ROW BEGIN
    INSERT INTO logs_administracion (ID_admin, tabla_afectada, registro, accion, new_data, fecha)
    VALUES (NULL, 'Usuario', NEW.ID_usuario, 'INSERT',
            CONCAT('Nombre:', NEW.nom_usuario, ', Correo:', NEW.correo), NOW());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_au_usuario` AFTER UPDATE ON `usuario` FOR EACH ROW BEGIN
    INSERT INTO logs_administracion (ID_admin, tabla_afectada, registro, accion, old_data, new_data, fecha)
    VALUES (NULL, 'Usuario', NEW.ID_usuario, 'UPDATE',
            CONCAT('Nombre:', OLD.nom_usuario, ', Correo:', OLD.correo),
            CONCAT('Nombre:', NEW.nom_usuario, ', Correo:', NEW.correo), NOW());
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
-- Indices de la tabla `descargas_certificados`
--
ALTER TABLE `descargas_certificados`
  ADD PRIMARY KEY (`ID_descarga`),
  ADD KEY `ID_certificado` (`ID_certificado`),
  ADD KEY `ID_usuario` (`ID_usuario`);

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
  MODIFY `ID_administrador` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `categoria`
--
ALTER TABLE `categoria`
  MODIFY `ID_categoria` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `certificado`
--
ALTER TABLE `certificado`
  MODIFY `ID_certificado` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `curso`
--
ALTER TABLE `curso`
  MODIFY `ID_curso` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `descargas_certificados`
--
ALTER TABLE `descargas_certificados`
  MODIFY `ID_descarga` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `gerente`
--
ALTER TABLE `gerente`
  MODIFY `ID_gerente` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `instructor`
--
ALTER TABLE `instructor`
  MODIFY `ID_instructor` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `logs_administracion`
--
ALTER TABLE `logs_administracion`
  MODIFY `ID_logs` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `permiso`
--
ALTER TABLE `permiso`
  MODIFY `ID_permiso` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `sesiones`
--
ALTER TABLE `sesiones`
  MODIFY `ID_sesiones` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `ID_usuario` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

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
-- Filtros para la tabla `descargas_certificados`
--
ALTER TABLE `descargas_certificados`
  ADD CONSTRAINT `descargas_certificados_ibfk_1` FOREIGN KEY (`ID_certificado`) REFERENCES `certificado` (`ID_certificado`) ON DELETE CASCADE,
  ADD CONSTRAINT `descargas_certificados_ibfk_2` FOREIGN KEY (`ID_usuario`) REFERENCES `usuario` (`ID_usuario`) ON DELETE CASCADE;

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
-- ============================================================
--  CASINO_DB — Script de Creación Completo
--  Base de datos: casino_db
--  Ejecutar como: mysql -u root -p < casino_db.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS casino_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE casino_db;

-- ============================================================
-- TABLA: usuarios
-- ============================================================
CREATE TABLE IF NOT EXISTS usuarios (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(100) NOT NULL,
    email       VARCHAR(150) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    es_admin    TINYINT(1)   NOT NULL DEFAULT 0,
    created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- TABLA: productos  (Paquetes de Créditos/Fichas)
-- ============================================================
CREATE TABLE IF NOT EXISTS productos (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    nombre_paquete  VARCHAR(150)   NOT NULL,
    descripcion     TEXT,
    precio          DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
    imagen_path     VARCHAR(500)   DEFAULT NULL,
    activo          TINYINT(1)     NOT NULL DEFAULT 1,
    created_at      TIMESTAMP      DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- TABLA: carrito  (sesión de carrito en BD)
-- ============================================================
CREATE TABLE IF NOT EXISTS carrito (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario  INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad    INT NOT NULL DEFAULT 1,
    UNIQUE KEY usuario_producto (id_usuario, id_producto),
    FOREIGN KEY (id_usuario)  REFERENCES usuarios(id)  ON DELETE CASCADE,
    FOREIGN KEY (id_producto) REFERENCES productos(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- TABLA: ventas  (cabecera de venta)
-- ============================================================
CREATE TABLE IF NOT EXISTS ventas (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    fecha       TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,
    total       DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
    id_usuario  INT NOT NULL,
    estatus     VARCHAR(20)    NOT NULL DEFAULT 'PAGADA',
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
) ENGINE=InnoDB;

-- ============================================================
-- TABLA: venta_detalle
-- ============================================================
CREATE TABLE IF NOT EXISTS venta_detalle (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    id_venta        INT           NOT NULL,
    id_producto     INT           NOT NULL,
    cantidad        INT           NOT NULL DEFAULT 1,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal        DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_venta)    REFERENCES ventas(id)   ON DELETE CASCADE,
    FOREIGN KEY (id_producto) REFERENCES productos(id)
) ENGINE=InnoDB;

-- ============================================================
-- TABLA: facturas
-- ============================================================
CREATE TABLE IF NOT EXISTS facturas (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    venta_id        INT           NOT NULL UNIQUE,
    folio_unico     VARCHAR(30)   NOT NULL UNIQUE,
    subtotal        DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    iva_16          DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    total_final     DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    fecha_emision   DATETIME      NOT NULL,
    concepto        VARCHAR(255)  NOT NULL DEFAULT 'Compra de créditos electrónicos para plataforma de entretenimiento',
    FOREIGN KEY (venta_id) REFERENCES ventas(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ============================================================
-- DATOS INICIALES — Paquetes de Fichas
-- ============================================================
INSERT INTO usuarios (nombre, email, password, es_admin) VALUES
('Administrador', 'admin@casinoglamour.mx', SHA2('Admin2026!', 256), 1),
('Demo Player',   'jugador@casinoglamour.mx', SHA2('Player2026!', 256), 0);

INSERT INTO productos (nombre_paquete, descripcion, precio, imagen_path) VALUES
('Pack Inicio — 500 Fichas',    'El paquete ideal para comenzar. 500 fichas para explorar todos los juegos de la plataforma.',        149.00, 'assets/img/pack_500.png'),
('Pack Bronce — 1,000 Fichas',  'Duplica la diversión. 1,000 fichas con acceso prioritario a mesas especiales.',                      249.00, 'assets/img/pack_1000.png'),
('Pack Plata — 2,500 Fichas',   'El favorito de los jugadores. 2,500 fichas con bono de 250 fichas adicionales.',                     549.00, 'assets/img/pack_2500.png'),
('Pack Oro — 5,000 Fichas',     'Para jugadores serios. 5,000 fichas con bono VIP de 1,000 fichas y acceso a sala privada.',         999.00, 'assets/img/pack_5000.png'),
('Pack Platino — 10,000 Fichas','La experiencia premium. 10,000 fichas, sala exclusiva y atención personalizada 24/7.',             1799.00, 'assets/img/pack_10000.png'),
('Pack Diamante — 25,000 Fichas','El máximo nivel. 25,000 fichas, crédito sin límite en torneos y host personal dedicado.',         3999.00, 'assets/img/pack_25000.png');

-- ============================================================
-- STORED PROCEDURE: Folio Correlativo Automático
-- ============================================================
DELIMITER $$

DROP PROCEDURE IF EXISTS sp_generar_folio$$
CREATE PROCEDURE sp_generar_folio(OUT p_folio VARCHAR(30))
BEGIN
    DECLARE v_year  CHAR(4);
    DECLARE v_seq   INT;
    SET v_year = YEAR(NOW());
    SELECT COUNT(*) + 1 INTO v_seq
    FROM facturas
    WHERE YEAR(fecha_emision) = v_year;
    SET p_folio = CONCAT('FAC-', v_year, '-', LPAD(v_seq, 3, '0'));
END$$

DELIMITER ;

-- ============================================================
-- VISTAS AUXILIARES
-- ============================================================
CREATE OR REPLACE VIEW vista_ventas AS
SELECT
    v.id, v.fecha, v.total, v.estatus,
    u.nombre AS nombre_usuario,
    u.email
FROM ventas v
INNER JOIN usuarios u ON u.id = v.id_usuario;

CREATE OR REPLACE VIEW vista_facturas AS
SELECT
    f.id AS factura_id,
    f.folio_unico,
    f.subtotal,
    f.iva_16,
    f.total_final,
    f.fecha_emision,
    f.concepto,
    v.id   AS venta_id,
    v.fecha AS fecha_venta,
    u.nombre AS nombre_usuario,
    u.email
FROM facturas f
INNER JOIN ventas v   ON v.id = f.venta_id
INNER JOIN usuarios u ON u.id = v.id_usuario;

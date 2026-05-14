esto lo copias en xamp database.sql                                                              CREATE DATABASE IF NOT EXISTS flores_alesli
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE flores_alesli;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    usuario VARCHAR(60) NOT NULL UNIQUE,
    clave VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'personal') NOT NULL DEFAULT 'personal',
    estado VARCHAR(30) DEFAULT 'Activo',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    telefono VARCHAR(30),
    email VARCHAR(120),
    direccion TEXT,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS personal (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    cargo VARCHAR(80),
    telefono VARCHAR(30),
    fecha_ingreso DATE,
    salario DECIMAL(10,2) DEFAULT 0,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categoria_id INT,
    nombre VARCHAR(140) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL DEFAULT 0,
    imagen_url VARCHAR(255),
    estado VARCHAR(30) DEFAULT 'Activo',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS inventario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT,
    stock INT NOT NULL DEFAULT 0,
    stock_minimo INT DEFAULT 0,
    ubicacion VARCHAR(120),
    fecha_actualizacion DATE,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT,
    fecha_pedido DATE NOT NULL,
    fecha_entrega DATE,
    direccion_entrega TEXT,
    estado VARCHAR(40) DEFAULT 'Nuevo',
    total DECIMAL(10,2) DEFAULT 0,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS detalle_pedido (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT,
    producto_id INT,
    cantidad INT NOT NULL DEFAULT 1,
    precio_unitario DECIMAL(10,2) DEFAULT 0,
    subtotal DECIMAL(10,2) DEFAULT 0,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS eventos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT,
    nombre_evento VARCHAR(140) NOT NULL,
    tipo_evento VARCHAR(60),
    fecha_evento DATE NOT NULL,
    lugar VARCHAR(160),
    estado VARCHAR(40) DEFAULT 'Cotizado',
    total DECIMAL(10,2) DEFAULT 0,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS detalle_evento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    evento_id INT,
    producto_id INT,
    descripcion_servicio TEXT,
    cantidad INT DEFAULT 1,
    precio DECIMAL(10,2) DEFAULT 0,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (evento_id) REFERENCES eventos(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS agenda (
    id INT AUTO_INCREMENT PRIMARY KEY,
    evento_id INT,
    personal_id INT,
    fecha DATE NOT NULL,
    hora TIME,
    actividad VARCHAR(180) NOT NULL,
    estado VARCHAR(40) DEFAULT 'Pendiente',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (evento_id) REFERENCES eventos(id) ON DELETE CASCADE,
    FOREIGN KEY (personal_id) REFERENCES personal(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS pagos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT,
    pedido_id INT,
    evento_id INT,
    fecha_pago DATE NOT NULL,
    monto DECIMAL(10,2) NOT NULL DEFAULT 0,
    metodo VARCHAR(40) DEFAULT 'QR',
    referencia_qr VARCHAR(140),
    estado VARCHAR(40) DEFAULT 'Pendiente',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE SET NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE SET NULL,
    FOREIGN KEY (evento_id) REFERENCES eventos(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    personal_id INT,
    cliente_id INT,
    fecha_venta DATE NOT NULL,
    concepto VARCHAR(180) NOT NULL,
    monto DECIMAL(10,2) NOT NULL DEFAULT 0,
    metodo_pago VARCHAR(40) DEFAULT 'QR',
    observacion TEXT,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (personal_id) REFERENCES personal(id) ON DELETE SET NULL,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS chat_reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_nombre VARCHAR(120) NOT NULL,
    telefono VARCHAR(30),
    tipo VARCHAR(40) DEFAULT 'Evento',
    fecha_reserva DATE NOT NULL,
    hora_reserva TIME,
    mensaje_cliente TEXT NOT NULL,
    respuesta_personal TEXT,
    estado VARCHAR(40) DEFAULT 'Pendiente',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO categorias (nombre, descripcion) VALUES
('Ramos', 'Ramos florales para regalos y fechas especiales'),
('Decoracion de eventos', 'Arreglos, centros de mesa y ambientacion'),
('Flores sueltas', 'Rosas, girasoles, lirios y flores de temporada')
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);

INSERT INTO clientes (nombre, telefono, email, direccion) VALUES
('Cliente ejemplo', '70000000', 'cliente@ejemplo.com', 'Santa Cruz, Bolivia');

INSERT INTO productos (categoria_id, nombre, descripcion, precio, estado) VALUES
(1, 'Ramo romantico', 'Ramo con rosas y follaje decorativo', 120.00, 'Activo'),
(2, 'Centro de mesa floral', 'Decoracion floral para eventos', 180.00, 'Activo'),
(3, 'Docena de rosas', 'Rosas frescas seleccionadas', 90.00, 'Activo');

INSERT INTO inventario (producto_id, stock, stock_minimo, ubicacion, fecha_actualizacion) VALUES
(1, 10, 3, 'Vitrina principal', CURDATE()),
(2, 5, 2, 'Deposito eventos', CURDATE()),
(3, 24, 6, 'Camara fria', CURDATE());

INSERT INTO personal (nombre, cargo, telefono, fecha_ingreso, salario) VALUES
('Personal ventas', 'Vendedor', '70000001', CURDATE(), 0);

INSERT INTO ventas (personal_id, cliente_id, fecha_venta, concepto, monto, metodo_pago, observacion) VALUES
(1, 1, CURDATE(), 'Venta inicial de ramo romantico', 120.00, 'QR', 'Registro de ejemplo');

INSERT INTO chat_reservas (cliente_nombre, telefono, tipo, fecha_reserva, hora_reserva, mensaje_cliente, estado) VALUES
('Reserva ejemplo', '70000002', 'Evento', DATE_ADD(CURDATE(), INTERVAL 3 DAY), '10:00:00', 'Decoracion floral para cumpleanos', 'Pendiente');
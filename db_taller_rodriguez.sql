DROP DATABASE IF EXISTS db_taller_rodriguez;

CREATE DATABASE db_taller_rodriguez;

USE db_taller_rodriguez;

CREATE TABLE administradores(
    id_administrador INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    nombre_administrador VARCHAR(50) NOT NULL,
    apellido_administrador VARCHAR(50) NOT NULL,
    alias_administrador VARCHAR(50) NOT NULL,
    correo_administrador VARCHAR(100) NOT NULL,
    clave_administrador VARCHAR(64) NOT NULL,
    fecha_registro DATE NOT NULL DEFAULT current_timestamp()
);

ALTER TABLE administradores
ADD CONSTRAINT unique_correo_administrador UNIQUE (correo_administrador);

ALTER TABLE administradores
ADD CONSTRAINT unique_alias_administrador UNIQUE (alias_administrador);

CREATE TABLE marcas(
    id_marca INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    marca_vehiculo VARCHAR(30) NOT NULL
);

ALTER TABLE marcas
ADD CONSTRAINT unique_marca_vehiculo UNIQUE (marca_vehiculo);

CREATE TABLE modelos(
    id_modelo INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    modelo_vehiculo VARCHAR(30) NOT NULL,
    id_marca INT NOT NULL,
    CONSTRAINT fk_modelo_marca
    FOREIGN KEY (id_marca)
    REFERENCES marcas (id_marca) ON DELETE CASCADE
);

ALTER TABLE modelos
ADD CONSTRAINT unique_modelo_vehiculo UNIQUE (modelo_vehiculo, id_marca);

CREATE TABLE clientes(
    id_cliente INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    nombre_cliente VARCHAR(50) NOT NULL,
    apellido_cliente VARCHAR(50) NOT NULL,
    alias_cliente VARCHAR(50) NOT NULL,
    correo_cliente VARCHAR(100) NOT NULL,
    clave_cliente VARCHAR(64) NOT NULL,
    contacto_cliente VARCHAR(9) NOT NULL,
    estado_cliente BOOLEAN NOT NULL
);

ALTER TABLE clientes
ADD CONSTRAINT chk_contacto_cliente_format
CHECK (contacto_cliente REGEXP '^[0-9]{4}-[0-9]{4}$');

ALTER TABLE clientes
ADD CONSTRAINT unique_correo_cliente UNIQUE (correo_cliente);

ALTER TABLE clientes
ADD CONSTRAINT unique_alias_cliente UNIQUE (alias_cliente);

CREATE TABLE vehiculos(
    id_vehiculo INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    id_modelo INT NOT NULL,
    id_cliente INT NOT NULL,
    placa_vehiculo VARCHAR(30) NOT NULL,
    color_vehiculo VARCHAR(30) NOT NULL,
    vim_motor VARCHAR(50) NOT NULL,
    id_marca INT NOT NULL,
    CONSTRAINT fk_vehiculo_modelo
    FOREIGN KEY (id_modelo)
    REFERENCES modelos (id_modelo) ON DELETE CASCADE,
    CONSTRAINT fk_vehiculo_cliente
    FOREIGN KEY (id_cliente)
    REFERENCES clientes (id_cliente) ON DELETE CASCADE,
    CONSTRAINT fk_vehiculo_marca
    FOREIGN KEY (id_marca)
    REFERENCES marcas (id_marca) ON DELETE CASCADE
);

CREATE TABLE servicios(
    id_servicio INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    nombre_servicio VARCHAR(50) NOT NULL,
    descripcion_servicio VARCHAR(250)
);

CREATE TABLE citas(
    id_cita INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    id_cliente INT NOT NULL,
    id_vehiculo INT NOT NULL,
    id_servicio INT NOT NULL,
    fecha_cita DATE NOT NULL,
	estado_cita ENUM('Pendiente', 'Completada', 'Cancelada') NOT NULL,
    CONSTRAINT fk_cita_cliente
    FOREIGN KEY (id_cliente)
    REFERENCES clientes (id_cliente) ON DELETE CASCADE,
    CONSTRAINT fk_cita_vehiculo
    FOREIGN KEY (id_vehiculo)
    REFERENCES vehiculos (id_vehiculo) ON DELETE CASCADE,
    CONSTRAINT fk_cita_servicio
    FOREIGN KEY (id_servicio)
    REFERENCES servicios (id_servicio) ON DELETE CASCADE
);

CREATE TABLE piezas(
    id_pieza INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    id_cliente INT NOT NULL,
    nombre_pieza VARCHAR(30) NOT NULL,
    descripcion_pieza VARCHAR(250) NOT NULL,
    precio_unitario DECIMAL(10, 2) NOT NULL,
    CONSTRAINT fk_pieza_vehiculo
    FOREIGN KEY (id_cliente)
    REFERENCES clientes (id_cliente) ON DELETE CASCADE
);

ALTER TABLE piezas
ADD CONSTRAINT unique_nombre_pieza UNIQUE (nombre_pieza);

ALTER TABLE piezas
ADD CONSTRAINT chk_precio_unitario_positive CHECK (precio_unitario > 0);

CREATE TABLE inventario (
    id_inventario INT AUTO_INCREMENT PRIMARY KEY,
    id_pieza INT NOT NULL,
    cantidad_disponible INT NOT NULL,
    proveedor VARCHAR(100) NOT NULL,
    fecha_ingreso DATE NOT NULL,
    CONSTRAINT fk_inventario_pieza
    FOREIGN KEY (id_pieza)
    REFERENCES piezas (id_pieza) ON DELETE CASCADE
);

SELECT * FROM administradores;
-- Datos para la tabla administradores
INSERT INTO administradores (nombre_administrador, apellido_administrador, alias_administrador, correo_administrador, clave_administrador, fecha_registro)
VALUES
('Carlos', 'Rodriguez', 'crodriguez', 'carlos.rodriguez@example.com', 'password1234', '2023-01-01'),
('Ana', 'Martinez', 'amartinez', 'ana.martinez@example.com', 'password5678', '2023-02-01'),
('Luis', 'Gonzalez', 'lgonzalez', 'luis.gonzalez@example.com', 'password9101', '2023-03-01'),
('Maria', 'Lopez', 'mlopez', 'maria.lopez@example.com', 'password1121', '2023-04-01'),
('Jose', 'Perez', 'jperez', 'jose.perez@example.com', 'password3141', '2023-05-01'),
('Sofia', 'Gomez', 'sgomez', 'sofia.gomez@example.com', 'password5161', '2023-06-01'),
('Diego', 'Fernandez', 'dfernandez', 'diego.fernandez@example.com', 'password7181', '2023-07-01'),
('Laura', 'Hernandez', 'lhernandez', 'laura.hernandez@example.com', 'password9201', '2023-08-01'),
('Pedro', 'Garcia', 'pgarcia', 'pedro.garcia@example.com', 'password1222', '2023-09-01'),
('Elena', 'Ramirez', 'eramirez', 'elena.ramirez@example.com', 'password3242', '2023-10-01'),
('Juan', 'Sanchez', 'jsanchez', 'juan.sanchez@example.com', 'password5262', '2023-11-01'),
('Lucia', 'Torres', 'ltorres', 'lucia.torres@example.com', 'password7282', '2023-12-01'),
('Fernando', 'Ruiz', 'fruiz', 'fernando.ruiz@example.com', 'password9302', '2024-01-01'),
('Valeria', 'Diaz', 'vdiaz', 'valeria.diaz@example.com', 'password1323', '2024-02-01'),
('Roberto', 'Alvarez', 'ralvarez', 'roberto.alvarez@example.com', 'password3343', '2024-03-01'),
('Carmen', 'Flores', 'cflores', 'carmen.flores@example.com', 'password5363', '2024-04-01'),
('Miguel', 'Ortiz', 'mortiz', 'miguel.ortiz@example.com', 'password7383', '2024-05-01'),
('Angela', 'Morales', 'amorales', 'angela.morales@example.com', 'password9403', '2024-06-01'),
('Andres', 'Gutierrez', 'agutierrez', 'andres.gutierrez@example.com', 'password1424', '2024-07-01'),
('Patricia', 'Mendoza', 'pmendoza', 'patricia.mendoza@example.com', 'password3444', '2024-08-01');

-- Datos para la tabla marcas
INSERT INTO marcas (marca_vehiculo)
VALUES
('Toyota'),
('Honda'),
('Ford'),
('Chevrolet'),
('BMW'),
('Mercedes-Benz'),
('Audi'),
('Nissan'),
('Volkswagen'),
('Hyundai'),
('Kia'),
('Mazda'),
('Subaru'),
('Lexus'),
('Acura'),
('Infiniti'),
('Porsche'),
('Jaguar'),
('Ferrari'),
('Lamborghini');

-- Datos para la tabla modelos
INSERT INTO modelos (modelo_vehiculo, id_marca)
VALUES
('Corolla', 1),
('Civic', 2),
('Mustang', 3),
('Camaro', 4),
('X5', 5),
('C-Class', 6),
('A4', 7),
('Altima', 8),
('Jetta', 9),
('Elantra', 10),
('Sorento', 11),
('CX-5', 12),
('Outback', 13),
('RX', 14),
('MDX', 15),
('Q50', 16),
('911', 17),
('F-Type', 18),
('488 GTB', 19),
('Huracan', 20);

-- Datos para la tabla clientes
INSERT INTO clientes (nombre_cliente, apellido_cliente, alias_cliente, correo_cliente, clave_cliente, contacto_cliente, estado_cliente)
VALUES
('Juan', 'Perez', 'jperez', 'juan.perez@example.com', 'password123', '1234-5678', TRUE),
('Ana', 'Lopez', 'alopez', 'ana.lopez@example.com', 'password456', '2345-6789', TRUE),
('Luis', 'Garcia', 'lgarcia', 'luis.garcia@example.com', 'password789', '3456-7890', TRUE),
('Maria', 'Martinez', 'mmartinez', 'maria.martinez@example.com', 'password012', '4567-8901', TRUE),
('Carlos', 'Rodriguez', 'crodriguez', 'carlos.rodriguez@example.com', 'password345', '5678-9012', TRUE),
('Sofia', 'Gomez', 'sgomez', 'sofia.gomez@example.com', 'password678', '6789-0123', TRUE),
('Diego', 'Fernandez', 'dfernandez', 'diego.fernandez@example.com', 'password901', '7890-1234', TRUE),
('Laura', 'Hernandez', 'lhernandez', 'laura.hernandez@example.com', 'password234', '8901-2345', TRUE),
('Pedro', 'Garcia', 'pgarcia', 'pedro.garcia@example.com', 'password567', '9012-3456', TRUE),
('Elena', 'Ramirez', 'eramirez', 'elena.ramirez@example.com', 'password890', '0123-4567', TRUE),
('Juan', 'Sanchez', 'jsanchez', 'juan.sanchez@example.com', 'password1234', '1234-5678', TRUE),
('Lucia', 'Torres', 'ltorres', 'lucia.torres@example.com', 'password5678', '2345-6789', TRUE),
('Fernando', 'Ruiz', 'fruiz', 'fernando.ruiz@example.com', 'password9101', '3456-7890', TRUE),
('Valeria', 'Diaz', 'vdiaz', 'valeria.diaz@example.com', 'password1121', '4567-8901', TRUE),
('Roberto', 'Alvarez', 'ralvarez', 'roberto.alvarez@example.com', 'password3141', '5678-9012', TRUE),
('Carmen', 'Flores', 'cflores', 'carmen.flores@example.com', 'password5161', '6789-0123', TRUE),
('Miguel', 'Ortiz', 'mortiz', 'miguel.ortiz@example.com', 'password7181', '7890-1234', TRUE),
('Angela', 'Morales', 'amorales', 'angela.morales@example.com', 'password9201', '8901-2345', TRUE),
('Andres', 'Gutierrez', 'agutierrez', 'andres.gutierrez@example.com', 'password1222', '9012-3456', TRUE),
('Patricia', 'Mendoza', 'pmendoza', 'patricia.mendoza@example.com', 'password3242', '0123-4567', TRUE);

-- Datos para la tabla vehiculos
INSERT INTO vehiculos (id_modelo, id_cliente, placa_vehiculo, color_vehiculo, vim_motor, id_marca)
VALUES
(1, 1, 'ABC-123', 'Rojo', '1HGBH41JXMN109186', 1),
(2, 2, 'DEF-456', 'Azul', '1HGBH41JXMN109187', 2),
(3, 3, 'GHI-789', 'Negro', '1HGBH41JXMN109188', 3),
(4, 4, 'JKL-012', 'Blanco', '1HGBH41JXMN109189', 4),
(5, 5, 'MNO-345', 'Gris', '1HGBH41JXMN109190', 5),
(6, 6, 'PQR-678', 'Verde', '1HGBH41JXMN109191', 6),
(7, 7, 'STU-901', 'Amarillo', '1HGBH41JXMN109192', 7),
(8, 8, 'VWX-234', 'Naranja', '1HGBH41JXMN109193', 8),
(9, 9, 'YZA-567', 'Rosa', '1HGBH41JXMN109194', 9),
(10, 10, 'BCD-890', 'Morado', '1HGBH41JXMN109195', 10),
(11, 11, 'CDE-123', 'Rojo', '1HGBH41JXMN109196', 11),
(12, 12, 'EFG-456', 'Azul', '1HGBH41JXMN109197', 12),
(13, 13, 'FGH-789', 'Negro', '1HGBH41JXMN109198', 13),
(14, 14, 'GHI-012', 'Blanco', '1HGBH41JXMN109199', 14),
(15, 15, 'HIJ-345', 'Gris', '1HGBH41JXMN109200', 15),
(16, 16, 'IJK-678', 'Verde', '1HGBH41JXMN109201', 16),
(17, 17, 'JKL-901', 'Amarillo', '1HGBH41JXMN109202', 17),
(18, 18, 'KLM-234', 'Naranja', '1HGBH41JXMN109203', 18),
(19, 19, 'LMN-567', 'Rosa', '1HGBH41JXMN109204', 19),
(20, 20, 'MNO-890', 'Morado', '1HGBH41JXMN109205', 20);

-- Datos para la tabla servicios
INSERT INTO servicios (nombre_servicio, descripcion_servicio)
VALUES
('Cambio de aceite', 'Cambio de aceite y filtro del motor'),
('Alineación y balanceo', 'Alineación y balanceo de las ruedas'),
('Revisión de frenos', 'Revisión y ajuste de frenos'),
('Cambio de batería', 'Sustitución de la batería del vehículo'),
('Mantenimiento de aire acondicionado', 'Revisión y mantenimiento del sistema de aire acondicionado'),
('Revisión general', 'Revisión general del vehículo'),
('Cambio de llantas', 'Sustitución de las llantas del vehículo'),
('Lavado y encerado', 'Lavado y encerado del vehículo'),
('Reparación de motor', 'Reparación y ajuste del motor'),
('Cambio de filtros', 'Cambio de filtros de aire y combustible'),
('Revisión eléctrica', 'Revisión del sistema eléctrico del vehículo'),
('Cambio de amortiguadores', 'Sustitución de amortiguadores'),
('Revisión de suspensión', 'Revisión y ajuste de la suspensión'),
('Revisión de transmisión', 'Revisión y mantenimiento de la transmisión'),
('Cambio de luces', 'Sustitución de luces delanteras y traseras'),
('Revisión de escape', 'Revisión y mantenimiento del sistema de escape'),
('Pintura', 'Pintura completa del vehículo'),
('Pulido', 'Pulido de la carrocería del vehículo'),
('Revisión de inyectores', 'Revisión y limpieza de inyectores'),
('Revisión de sistema de enfriamiento', 'Revisión y mantenimiento del sistema de enfriamiento');

-- Datos para la tabla citas
INSERT INTO citas (id_cliente, id_vehiculo, id_servicio, fecha_cita, estado_cita)
VALUES
(1, 1, 1, '2024-01-01', 'Pendiente'),
(2, 2, 2, '2024-01-02', 'Completada'),
(3, 3, 3, '2024-01-03', 'Cancelada'),
(4, 4, 4, '2024-01-04', 'Pendiente'),
(5, 5, 5, '2024-01-05', 'Completada'),
(6, 6, 6, '2024-01-06', 'Cancelada'),
(7, 7, 7, '2024-01-07', 'Pendiente'),
(8, 8, 8, '2024-01-08', 'Completada'),
(9, 9, 9, '2024-01-09', 'Cancelada'),
(10, 10, 10, '2024-01-10', 'Pendiente'),
(11, 11, 11, '2024-01-11', 'Completada'),
(12, 12, 12, '2024-01-12', 'Cancelada'),
(13, 13, 13, '2024-01-13', 'Pendiente'),
(14, 14, 14, '2024-01-14', 'Completada'),
(15, 15, 15, '2024-01-15', 'Cancelada'),
(16, 16, 16, '2024-01-16', 'Pendiente'),
(17, 17, 17, '2024-01-17', 'Completada'),
(18, 18, 18, '2024-01-18', 'Cancelada'),
(19, 19, 19, '2024-01-19', 'Pendiente'),
(20, 20, 20, '2024-01-20', 'Completada');

-- Datos para la tabla piezas
INSERT INTO piezas (id_cliente, nombre_pieza, descripcion_pieza, precio_unitario)
VALUES
(1, 'Filtro de aceite', 'Filtro para el aceite del motor', 10.50),
(2, 'Bujía', 'Bujía para el encendido del motor', 5.75),
(3, 'Pastilla de freno', 'Pastilla para el sistema de frenos', 20.00),
(4, 'Amortiguador', 'Amortiguador para la suspensión', 50.00),
(5, 'Filtro de aire', 'Filtro para el aire del motor', 15.25),
(6, 'Correa de distribución', 'Correa para la distribución del motor', 35.00),
(7, 'Filtro de combustible', 'Filtro para el combustible del motor', 25.00),
(8, 'Batería', 'Batería para el sistema eléctrico del vehículo', 100.00),
(9, 'Luz delantera', 'Luz delantera para el vehículo', 30.00),
(10, 'Luz trasera', 'Luz trasera para el vehículo', 25.50),
(11, 'Parabrisas', 'Parabrisas para el vehículo', 200.00),
(12, 'Espejo retrovisor', 'Espejo retrovisor para el vehículo', 15.00),
(13, 'Radiador', 'Radiador para el sistema de enfriamiento', 150.00),
(14, 'Bomba de agua', 'Bomba de agua para el motor', 75.00),
(15, 'Alternador', 'Alternador para el sistema eléctrico del vehículo', 120.00),
(16, 'Motor de arranque', 'Motor de arranque para el vehículo', 80.00),
(17, 'Embrague', 'Embrague para la transmisión del vehículo', 90.00),
(18, 'Faro antiniebla', 'Faro antiniebla para el vehículo', 40.00),
(19, 'Tubo de escape', 'Tubo de escape para el sistema de escape', 60.00),
(20, 'Catalizador', 'Catalizador para el sistema de escape', 180.00);

-- Datos para la tabla inventario
INSERT INTO inventario (id_pieza, cantidad_disponible, proveedor, fecha_ingreso)
VALUES
(1, 100, 'Proveedor A', '2024-01-01'),
(2, 200, 'Proveedor B', '2024-01-02'),
(3, 150, 'Proveedor C', '2024-01-03'),
(4, 120, 'Proveedor D', '2024-01-04'),
(5, 130, 'Proveedor E', '2024-01-05'),
(6, 140, 'Proveedor F', '2024-01-06'),
(7, 110, 'Proveedor G', '2024-01-07'),
(8, 160, 'Proveedor H', '2024-01-08'),
(9, 170, 'Proveedor I', '2024-01-09'),
(10, 180, 'Proveedor J', '2024-01-10'),
(11, 190, 'Proveedor K', '2024-01-11'),
(12, 200, 'Proveedor L', '2024-01-12'),
(13, 210, 'Proveedor M', '2024-01-13'),
(14, 220, 'Proveedor N', '2024-01-14'),
(15, 230, 'Proveedor O', '2024-01-15'),
(16, 240, 'Proveedor P', '2024-01-16'),
(17, 250, 'Proveedor Q', '2024-01-17'),
(18, 260, 'Proveedor R', '2024-01-18'),
(19, 270, 'Proveedor S', '2024-01-19'),
(20, 280, 'Proveedor T', '2024-01-20');

DROP DATABASE IF EXISTS db_taller_rodriguez;

CREATE DATABASE db_taller_rodriguez;

USE db_taller_rodriguez;

CREATE table roles(
	id_rol INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
	nombre_rol VARCHAR(30) NOT NULL
);

CREATE TABLE administradores(
    id_administrador INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    nombre_administrador VARCHAR(50) NOT NULL,
    apellido_administrador VARCHAR(50) NOT NULL,
    alias_administrador VARCHAR(50) NOT NULL,
    correo_administrador VARCHAR(100) NOT NULL,
    clave_administrador VARCHAR(64) NOT NULL,
    fecha_registro DATE NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    id_rol INT NOT NULL,
    CONSTRAINT fk_rol_administrador
    FOREIGN KEY (id_rol)
    REFERENCES roles (id_rol) ON DELETE CASCADE
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

ALTER TABLE clientes
ALTER COLUMN estado_cliente SET DEFAULT 1;

CREATE TABLE vehiculos(
    id_vehiculo INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    id_marca INT NOT NULL,
    id_modelo INT NOT NULL,
    id_cliente INT NOT NULL,
    placa_vehiculo VARCHAR(30) NOT NULL,
    año_vehiculo VARCHAR(4) NOT NULL,
    color_vehiculo VARCHAR(30) NOT NULL,
    vin_motor VARCHAR(50) NOT NULL,
    CONSTRAINT fk_vehiculo_marca
    FOREIGN KEY (id_marca)
    REFERENCES marcas (id_marca) ON DELETE CASCADE,
    CONSTRAINT fk_vehiculo_modelo
    FOREIGN KEY (id_modelo)
    REFERENCES modelos (id_modelo) ON DELETE CASCADE,
    CONSTRAINT fk_vehiculo_cliente
    FOREIGN KEY (id_cliente)
    REFERENCES clientes (id_cliente) ON DELETE CASCADE
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

CREATE TABLE detalle_citas(
	id_detalle_cita INT AUTO_INCREMENT PRIMARY KEY,
	id_pieza INT,
	 CONSTRAINT fk_detalle_pieza
    FOREIGN KEY (id_pieza)
    REFERENCES piezas (id_pieza) ON DELETE CASCADE,
    id_cita INT,
    CONSTRAINT fk_detalle_cita
    FOREIGN KEY (id_cita)
    REFERENCES citas (id_cita) ON DELETE CASCADE,
    cantidad INT NOT NULL	
);

SHOW TABLES;

INSERT INTO roles (nombre_rol)
VALUES
<<<<<<< HEAD
('Administrador General'),
('Mecanico'),
('Proveedor');

/*
INSERT INTO administradores (nombre_administrador, apellido_administrador, alias_administrador, correo_administrador, clave_administrador, fecha_registro, id_rol)
VALUES
('Carlos', 'Rodriguez', 'crodriguez', 'carlos.rodriguez@example.com', 'password1234', '2023-01-01',2),
('Ana', 'Martinez', 'amartinez', 'ana.martinez@example.com', 'password5678', '2023-02-01',2),
('Luis', 'Gonzalez', 'lgonzalez', 'luis.gonzalez@example.com', 'password9101', '2023-03-01',2),
('Maria', 'Lopez', 'mlopez', 'maria.lopez@example.com', 'password1121', '2023-04-01',2),
('Jose', 'Perez', 'jperez', 'jose.perez@example.com', 'password3141', '2023-05-01',2),
('Sofia', 'Gomez', 'sgomez', 'sofia.gomez@example.com', 'password5161', '2023-06-01',2),
('Diego', 'Fernandez', 'dfernandez', 'diego.fernandez@example.com', 'password7181', '2023-07-01',2),
('Laura', 'Hernandez', 'lhernandez', 'laura.hernandez@example.com', 'password9201', '2023-08-01',2),
('Pedro', 'Garcia', 'pgarcia', 'pedro.garcia@example.com', 'password1222', '2023-09-01',2),
('Elena', 'Ramirez', 'eramirez', 'elena.ramirez@example.com', 'password3242', '2023-10-01',2),
('Juan', 'Sanchez', 'jsanchez', 'juan.sanchez@example.com', 'password5262', '2023-11-01',3),
('Lucia', 'Torres', 'ltorres', 'lucia.torres@example.com', 'password7282', '2023-12-01',3),
('Fernando', 'Ruiz', 'fruiz', 'fernando.ruiz@example.com', 'password9302', '2024-01-01',3),
('Valeria', 'Diaz', 'vdiaz', 'valeria.diaz@example.com', 'password1323', '2024-02-01',3),
('Roberto', 'Alvarez', 'ralvarez', 'roberto.alvarez@example.com', 'password3343', '2024-03-01',3),
('Carmen', 'Flores', 'cflores', 'carmen.flores@example.com', 'password5363', '2024-04-01',3),
('Miguel', 'Ortiz', 'mortiz', 'miguel.ortiz@example.com', 'password7383', '2024-05-01',3),
('Angela', 'Morales', 'amorales', 'angela.morales@example.com', 'password9403', '2024-06-01',3),
('Andres', 'Gutierrez', 'agutierrez', 'andres.gutierrez@example.com', 'password1424', '2024-07-01',3),
('Patricia', 'Mendoza', 'pmendoza', 'patricia.mendoza@example.com', 'password3444', '2024-08-01',3);
*/
=======
('Administrador');
>>>>>>> 29833d5be4ee8a7dd063dd04291d0989bc92cd2c

SELECT * FROM vehiculos;

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

INSERT INTO modelos (modelo_vehiculo, id_marca)
VALUES
('Camry', 1), ('RAV4', 1), ('Highlander', 1), ('Prius', 1), -- Toyota
('Civic', 2), ('Accord', 2), ('CR-V', 2), ('Pilot', 2), ('Fit', 2), -- Honda
('Focus', 3), ('Fiesta', 3), ('Mustang', 3), ('Explorer', 3), ('Escape', 3), -- Ford
('Malibu', 4), ('Impala', 4), ('Equinox', 4), ('Traverse', 4), ('Tahoe', 4), -- Chevrolet
('3 Series', 5), ('5 Series', 5), ('X3', 5), ('X5', 5), ('Z4', 5), -- BMW
('C-Class', 6), ('E-Class', 6), ('S-Class', 6), ('GLC', 6), ('GLE', 6), -- Mercedes-Benz
('A3', 7), ('A4', 7), ('A6', 7), ('Q5', 7), ('Q7', 7), -- Audi
('Altima', 8), ('Sentra', 8), ('Rogue', 8), ('Murano', 8), ('Pathfinder', 8), -- Nissan
('Golf', 9), ('Passat', 9), ('Tiguan', 9), ('Atlas', 9), ('Jetta', 9), -- Volkswagen
('Elantra', 10), ('Sonata', 10), ('Tucson', 10), ('Santa Fe', 10), ('Kona', 10), -- Hyundai
('Rio', 11), ('Forte', 11), ('Sportage', 11), ('Sorento', 11), ('Optima', 11), -- Kia
('Mazda3', 12), ('Mazda6', 12), ('CX-5', 12), ('CX-9', 12), ('MX-5', 12), -- Mazda
('Impreza', 13), ('Outback', 13), ('Forester', 13), ('Crosstrek', 13), ('WRX', 13), -- Subaru
('IS', 14), ('ES', 14), ('RX', 14), ('GX', 14), ('LX', 14), -- Lexus
('TLX', 15), ('ILX', 15), ('RDX', 15), ('MDX', 15), ('NSX', 15), -- Acura
('Q50', 16), ('Q60', 16), ('QX50', 16), ('QX60', 16), ('QX80', 16), -- Infiniti
('911', 17), ('Cayenne', 17), ('Macan', 17), ('Panamera', 17), ('Taycan', 17), -- Porsche
('XE', 18), ('XF', 18), ('F-Pace', 18), ('E-Pace', 18), ('I-Pace', 18), -- Jaguar
('488', 19), ('F8', 19), ('Portofino', 19), ('Roma', 19), ('SF90', 19), -- Ferrari
('Aventador', 20), ('Huracan', 20), ('Urus', 20), ('Gallardo', 20), ('Murcielago', 20); -- Lamborghini
            
INSERT INTO servicios (nombre_servicio, descripcion_servicio) VALUES
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
<<<<<<< HEAD
('Revisión de sistema de enfriamiento', 'Revisión y mantenimiento del sistema de enfriamiento');

-- Datos para la tabla citas
INSERT INTO citas (id_cliente, id_vehiculo, id_servicio, fecha_cita, estado_cita)
VALUES
(1, 1, 1, '2020-01-01', 'Completada'),
(2, 2, 2, '2020-01-02', 'Completada'),
(3, 3, 3, '2020-01-03', 'Cancelada'),
(4, 4, 4, '2020-01-04', 'Completada'),
(5, 5, 5, '2020-01-05', 'Completada'),
(6, 6, 6, '2020-01-06', 'Completada'),
(7, 7, 7, '2020-01-07', 'Completada'),
(8, 8, 8, '2020-01-08', 'Completada'),
(9, 9, 9, '2020-01-09', 'Cancelada'),
(10, 10, 1, '2024-01-10', 'Completada'),
(1, 1, 1, '2021-01-01', 'Completada'),
(2, 2, 1, '2021-01-02', 'Completada'),
(3, 3, 3, '2021-01-03', 'Cancelada'),
(4, 4, 4, '2021-01-04', 'Completada'),
(5, 5, 1, '2021-01-05', 'Completada'),
(6, 6, 6, '2021-01-06', 'Cancelada'),
(7, 7, 7, '2021-01-07', 'Completada'),
(8, 8, 8, '2021-01-08', 'Completada'),
(9, 9, 9, '2021-01-09', 'Completada'),
(10, 10, 10, '2022-01-10', 'Completada'),
(11, 11, 11, '2022-01-11', 'Completada'),
(12, 12, 12, '2022-01-12', 'Completada'),
(13, 13, 13, '2022-01-13', 'Completada'),
(14, 14, 14, '2022-01-14', 'Completada'),
(15, 15, 15, '2022-01-15', 'Completada'),
(16, 16, 16, '2022-01-16', 'Completada'),
(17, 17, 17, '2022-01-17', 'Completada'),
(18, 18, 18, '2022-01-18', 'Completada'),
(19, 19, 19, '2022-01-19', 'Completada'),
(20, 20, 20, '2022-01-20', 'Completada'),
(1, 1, 1, '2023-01-01', 'Completada'),
(2, 2, 2, '2023-01-02', 'Completada'),
(3, 3, 3, '2023-01-03', 'Cancelada'),
(4, 4, 4, '2023-01-04', 'Completada'),
(5, 5, 5, '2023-01-05', 'Completada'),
(6, 6, 6, '2023-01-06', 'Completada'),
(7, 7, 7, '2023-01-07', 'Completada'),
(8, 8, 8, '2023-01-08', 'Completada'),
(9, 9, 9, '2023-01-09', 'Cancelada'),
(10, 10, 10, '2024-01-10', 'Pendiente'),
(11, 11, 11, '2024-01-11', 'Pendiente'),
(12, 12, 12, '2024-01-12', 'Cancelada'),
(13, 13, 13, '2024-01-13', 'Pendiente'),
(14, 14, 14, '2024-01-14', 'Pendiente'),
(15, 15, 15, '2024-01-15', 'Cancelada'),
(16, 16, 16, '2024-01-16', 'Pendiente'),
(17, 17, 17, '2024-01-17', 'Pendiente'),
(18, 18, 18, '2024-01-18', 'Pendiente'),
(19, 19, 19, '2024-01-19', 'Pendiente'),
(20, 20, 20, '2024-01-20', 'Pendiente');

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

INSERT INTO detalle_citas(id_pieza,id_cita, cantidad)
VALUES 
(1,1,5),
(2,2,4),
(3,3,7),
(4,4,2),
(5,5,3),
(6,6,10),
(7,7,6),
(8,8,2),
(9,9,1),
(10,10,6),
(11,11,3),
(12,12,2),
(13,13,8),
(14,14,7),
(15,15,3),
(16,16,2),
(17,17,5),
(18,18,2),
(19,19,1),
(20,20,7),
(1,21,8),
(2,22,3),
(3,23,2),
(4,24,1),
(5,25,8),
(6,26,7),
(7,27,6),
(8,28,5),
(9,29,4),
(10,30,4),
(11,31,7),
(12,32,3),
(13,33,2),
(14,34,1),
(15,35,2),
(16,36,3),
(17,37,5),
(18,38,3),
(19,39,2),
(20,40,2),
(11,41,7),
(12,42,3),
(13,43,2),
(14,44,1),
(15,45,2),
(16,46,3),
(17,47,5),
(18,48,3),
(19,49,2),
(20,50,2);

-- Sentencias de prueba para los gráficos 

SELECT * FROM citas;

SELECT COUNT(id_administrador) cantidad, nombre_rol 
FROM administradores
INNER JOIN roles USING (id_rol)
GROUP BY nombre_rol;

SELECT COUNT(id_vehiculo) cantidad, marca_vehiculo
FROM vehiculos
INNER JOIN marcas USING (id_marca)
GROUP BY marca_vehiculo
LIMIT 3;

SELECT COUNT(id_vehiculo) cantidad, modelo_vehiculo
FROM vehiculos
INNER JOIN modelos USING (id_modelo)
GROUP BY modelo_vehiculo
LIMIT 3;

SELECT estado_cita, ROUND((COUNT(id_cita) * 100.0 / (SELECT COUNT(id_cita) FROM citas)), 2) porcentaje
FROM citas
GROUP BY estado_cita ORDER BY porcentaje DESC;

SELECT modelo_vehiculo, COUNT(id_vehiculo) coches
FROM marcas, modelos, vehiculos
WHERE marcas.id_marca = modelos.id_marca AND
modelos.id_modelo = vehiculos.id_modelo AND
marcas.id_marca = 1
GROUP BY modelo_vehiculo;

SELECT modelo_vehiculo, COUNT(vehiculos.id_vehiculo) coches
FROM modelos, vehiculos, citas, piezas, detalle_citas
WHERE modelos.id_modelo = vehiculos.id_modelo AND
citas.id_vehiculo = vehiculos.id_vehiculo AND
citas.id_cita = detalle_citas.id_cita AND
detalle_citas.id_pieza = piezas.id_pieza and
piezas.id_pieza = 1
GROUP BY modelo_vehiculo
LIMIT 5;

SELECT marca_vehiculo, COUNT(citas.id_cita) coches
FROM servicios, citas, marcas, vehiculos
WHERE servicios.id_servicio = citas.id_servicio AND
marcas.id_marca = vehiculos.id_marca AND
vehiculos.id_vehiculo = citas.id_vehiculo AND
servicios.id_servicio = 1
GROUP BY marca_vehiculo ORDER BY coches DESC
LIMIT 5;

SELECT YEAR(fecha_cita) AS Año, SUM(cantidad * precio_unitario) AS Ganancias
FROM detalle_citas, piezas, citas
WHERE piezas.id_pieza = detalle_citas.id_pieza AND
detalle_citas.id_cita = citas.id_cita AND
estado_cita = "Completada"
GROUP BY Año;

SELECT * FROM piezas;
SELECT * FROM vehiculos;

SELECT modelo_vehiculo, COUNT(id_vehiculo) coches
                FROM marcas, modelos, vehiculos
                WHERE marcas.id_marca = modelos.id_marca AND
                modelos.id_modelo = vehiculos.id_modelo AND
                marcas.id_marca = 1
                GROUP BY modelo_vehiculo;

=======
('Revisión de sistema de enfriamiento', 'Revisión y mantenimiento del sistema de enfriamiento');
>>>>>>> 29833d5be4ee8a7dd063dd04291d0989bc92cd2c

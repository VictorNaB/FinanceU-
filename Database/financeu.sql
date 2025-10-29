-- =====================================================
-- CREACIÓN DE TABLAS SEGÚN EL MODELO ENTIDAD RELACIÓN (MySQL)
-- =====================================================

-- 1. Tabla: Universidad
CREATE TABLE Universidad (
    id_universidad INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL
) ENGINE=InnoDB;

-- 2. Tabla: Roles
CREATE TABLE roles (
    id_rol INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

-- 3. Tabla: Usuarios
CREATE TABLE Usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    id_universidad INT,
    id_rol INT NOT NULL,
    programa_estudio VARCHAR(150),
    fecha_registro DATE NOT NULL DEFAULT (CURRENT_DATE),
    FOREIGN KEY (id_universidad) REFERENCES Universidad(id_universidad)
        ON DELETE SET NULL
        ON UPDATE CASCADE,
    FOREIGN KEY (id_rol) REFERENCES roles(id_rol)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 4. Tabla: Accesos
CREATE TABLE Accesos (
    id_usuario INT NOT NULL,
    correo VARCHAR(200) UNIQUE NOT NULL,
    contrasena VARCHAR(200) NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES Usuarios(id_usuario)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 5. Tabla: Tipos de Recordatorio
CREATE TABLE TiposRecordatorio (
    idtipo_recordatorio INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

-- 6. Tabla: Recurrente (Periodicidad)
CREATE TABLE Recurrente (
    idrecurrente INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

-- 7. Tabla: Tipos de Transacción
CREATE TABLE TiposTransaccion (
    idtipo_transaccion INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

-- 8. Tabla: Categoría de Transacción
CREATE TABLE CategoriaTransaccion (
    idCategoriaTransaccion INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

-- 9. Tabla: Metas
CREATE TABLE Metas (
    id_meta INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    titulo_meta VARCHAR(150) NOT NULL,
    monto_objetivo DECIMAL(12,2) NOT NULL,
    monto_actual DECIMAL(12,2) NOT NULL DEFAULT 0,
    fecha_limite DATE NOT NULL,
    descripcion TEXT NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES Usuarios(id_usuario)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 10. Tabla: Recordatorios
CREATE TABLE Recordatorios (
    id_recordatorio INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    monto DECIMAL(12,2) NOT NULL,
    fecha DATE NOT NULL,
    idtipo_recordatorio INT NOT NULL,
    idrecurrente INT NOT NULL, 
    descripcion VARCHAR(200) NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES Usuarios(id_usuario)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (idtipo_recordatorio) REFERENCES TiposRecordatorio(idtipo_recordatorio)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (idrecurrente) REFERENCES Recurrente(idrecurrente)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 11. Tabla: Transaccion
CREATE TABLE Transaccion (
    id_transaccion INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    idtipo_transaccion INT NOT NULL,
    descripcion TEXT NOT NULL,
    monto DECIMAL(12,2) NOT NULL,
    idCategoriaTransaccion INT NOT NULL,
    fecha DATE NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES Usuarios(id_usuario)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (idtipo_transaccion) REFERENCES TiposTransaccion(idtipo_transaccion)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (idCategoriaTransaccion) REFERENCES CategoriaTransaccion(idCategoriaTransaccion)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 12. Tabla: Estadísticas de Uso
CREATE TABLE EstadisticasUso (
    id_usuario INT PRIMARY KEY NOT NULL,
    transacciones_registradas INT DEFAULT 0,
    metas_establecidas INT DEFAULT 0,
    dias_consecutivos INT DEFAULT 0,
    FOREIGN KEY (id_usuario) REFERENCES Usuarios(id_usuario)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 13. Tabla: Análisis Semanal
CREATE TABLE AnalisisSemanal (
  idAnalisis INT AUTO_INCREMENT PRIMARY KEY,
  id_usuario INT NOT NULL,
  semana_inicio DATE NOT NULL,
  semana_fin DATE NOT NULL,
  ingresos_totales DECIMAL(14,2) NOT NULL DEFAULT 0,
  gastos_totales   DECIMAL(14,2) NOT NULL DEFAULT 0,
  balance          DECIMAL(14,2) NOT NULL DEFAULT 0,
  CONSTRAINT uq_usuario_semana UNIQUE (id_usuario, semana_inicio),
  FOREIGN KEY (id_usuario) REFERENCES Usuarios(id_usuario)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB;
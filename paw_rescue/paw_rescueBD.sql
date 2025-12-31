-- ================================================================
-- PROYECTO: paw_rescue (PostgreSQL)
-- ================================================================

CREATE SCHEMA IF NOT EXISTS paw_rescue;

GRANT USAGE ON SCHEMA paw_rescue TO murasaki;
GRANT SELECT, INSERT, UPDATE, DELETE ON ALL TABLES IN SCHEMA paw_rescue TO murasaki;
GRANT USAGE, SELECT ON ALL SEQUENCES IN SCHEMA paw_rescue TO murasaki;

ALTER DEFAULT PRIVILEGES IN SCHEMA paw_rescue
GRANT SELECT, INSERT, UPDATE, DELETE ON TABLES TO murasaki;

ALTER DEFAULT PRIVILEGES IN SCHEMA paw_rescue
GRANT USAGE, SELECT ON SEQUENCES TO murasaki;

-- ================================================================
-- 1. CATÁLOGOS
-- ================================================================

CREATE TABLE paw_rescue.tipo_id (
    id_tipo INT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL
);

CREATE TABLE paw_rescue.especie (
    id_esp INT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL
);

CREATE TABLE paw_rescue.raza (
    id_raza INT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    id_esp INT NOT NULL REFERENCES paw_rescue.especie(id_esp)
);

CREATE TABLE paw_rescue.tam (
    id_tam INT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL
);

CREATE TABLE paw_rescue.temperamento (
    id_temp INT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL
);

CREATE TABLE paw_rescue.color (
    id_color INT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL
);

CREATE TABLE paw_rescue.color_ojos (
    id_ojos INT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL
);

CREATE TABLE paw_rescue.estatus_adop (
    id_estatus INT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL
);

CREATE TABLE paw_rescue.estado_animal (
    id_estado INT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL
);

-- ================================================================
-- 2. USUARIOS Y ROLES
-- ================================================================

CREATE TABLE paw_rescue.usuario (
    id_usuario INT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    correo VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    fecha_nacimiento DATE NOT NULL,
    CONSTRAINT chk_mayor_edad CHECK (fecha_nacimiento <= CURRENT_DATE - INTERVAL '18 years')
);

CREATE TABLE paw_rescue.rol (
    id_rol INT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL
);

CREATE TABLE paw_rescue.usuario_rol (
    id_usuario INT REFERENCES paw_rescue.usuario(id_usuario),
    id_rol INT REFERENCES paw_rescue.rol(id_rol),
    PRIMARY KEY (id_usuario, id_rol)
);

CREATE TABLE paw_rescue.adoptante (
    id_usuario INT PRIMARY KEY REFERENCES paw_rescue.usuario(id_usuario)
);

-- ================================================================
-- 3. REFUGIOS
-- ================================================================

CREATE TABLE paw_rescue.refugio (
    id_ref INT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    direccion VARCHAR(255),
    telefono VARCHAR(20)
);

-- ================================================================
-- 4. ANIMAL
-- ================================================================

CREATE TABLE paw_rescue.animal (
    id_animal INT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    id_esp INT NOT NULL REFERENCES paw_rescue.especie(id_esp),
    id_raza INT REFERENCES paw_rescue.raza(id_raza),
    id_tam INT REFERENCES paw_rescue.tam(id_tam),
    id_color INT REFERENCES paw_rescue.color(id_color),
    id_ojos INT REFERENCES paw_rescue.color_ojos(id_ojos),
    id_temp INT REFERENCES paw_rescue.temperamento(id_temp),
    id_estatus INT REFERENCES paw_rescue.estatus_adop(id_estatus),
    id_estado INT REFERENCES paw_rescue.estado_animal(id_estado),
    id_ref INT REFERENCES paw_rescue.refugio(id_ref),
    edad_aprox SMALLINT CHECK (edad_aprox >= 0),
    fecha_reg TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ================================================================
-- 5. IDENTIFICACIÓN
-- ================================================================

CREATE TABLE paw_rescue.ident_animal (
    id_ident SERIAL PRIMARY KEY,
    id_animal INT UNIQUE REFERENCES paw_rescue.animal(id_animal),
    id_tipo INT REFERENCES paw_rescue.tipo_id(id_tipo),
    codigo VARCHAR(100),
    tiene_id BOOLEAN DEFAULT FALSE,
    fecha DATE,
    CONSTRAINT chk_identificacion CHECK (
        (tiene_id = FALSE AND codigo IS NULL)
        OR
        (tiene_id = TRUE AND codigo IS NOT NULL)
    )
);

-- ================================================================
-- 6. RESCATE Y EVENTOS
-- ================================================================

CREATE TABLE paw_rescue.rescate (
    id_rescate SERIAL PRIMARY KEY,
    id_animal INT REFERENCES paw_rescue.animal(id_animal),
    fecha DATE,
    lugar VARCHAR(255),
    id_usuario INT REFERENCES paw_rescue.usuario(id_usuario),
    condiciones VARCHAR(1000)
);

CREATE TABLE paw_rescue.evento_animal (
    id_evento SERIAL PRIMARY KEY,
    id_animal INT REFERENCES paw_rescue.animal(id_animal),
    tipo VARCHAR(50),
    descripcion VARCHAR(1000),
    fecha DATE
);

CREATE TABLE paw_rescue.hist_estado (
    id_hist SERIAL PRIMARY KEY,
    id_animal INT REFERENCES paw_rescue.animal(id_animal),
    id_estado INT REFERENCES paw_rescue.estado_animal(id_estado),
    fecha DATE,
    obs VARCHAR(1000)
);

-- ================================================================
-- 7. SALUD
-- ================================================================

CREATE TABLE paw_rescue.enfermedad (
    id_enf INT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL
);

CREATE TABLE paw_rescue.vacuna (
    id_vac INT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    esencial BOOLEAN DEFAULT FALSE
);

CREATE TABLE paw_rescue.via_admin (
    id_via INT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL
);

CREATE TABLE paw_rescue.enf_animal (
    id_animal INT REFERENCES paw_rescue.animal(id_animal),
    id_enf INT REFERENCES paw_rescue.enfermedad(id_enf),
    fecha DATE,
    PRIMARY KEY (id_animal, id_enf)
);

CREATE TABLE paw_rescue.hist_vac (
    id_hist SERIAL PRIMARY KEY,
    id_animal INT REFERENCES paw_rescue.animal(id_animal),
    id_vac INT REFERENCES paw_rescue.vacuna(id_vac),
    id_via INT REFERENCES paw_rescue.via_admin(id_via),
    fecha_ap DATE,
    fecha_exp DATE,
    vet VARCHAR(100),
    obs VARCHAR(1000)
);

CREATE TABLE paw_rescue.salud_actual (
    id_salud SERIAL PRIMARY KEY,
    id_animal INT UNIQUE REFERENCES paw_rescue.animal(id_animal),
    enfermo BOOLEAN DEFAULT FALSE,
    diagnostico VARCHAR(1000),
    obs VARCHAR(1000),
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_salud CHECK (
        (enfermo = FALSE AND diagnostico IS NULL)
        OR
        (enfermo = TRUE AND diagnostico IS NOT NULL)
    )
);

-- ================================================================
-- 8. CONSULTAS
-- ================================================================

CREATE TABLE paw_rescue.consulta (
    id_cons SERIAL PRIMARY KEY,
    id_animal INT REFERENCES paw_rescue.animal(id_animal),
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    peso DECIMAL(5,2),
    temp DECIMAL(4,2),
    fc INT,
    fr INT,
    diagnostico VARCHAR(1500),
    plan VARCHAR(1500)
);

CREATE TABLE paw_rescue.tratamiento (
    id_trat SERIAL PRIMARY KEY,
    id_cons INT REFERENCES paw_rescue.consulta(id_cons),
    medicamento VARCHAR(100),
    dosis VARCHAR(50),
    frecuencia INT,
    duracion INT,
    indicaciones VARCHAR(1000)
);

CREATE TABLE paw_rescue.peso_hist (
    id_peso SERIAL PRIMARY KEY,
    id_animal INT REFERENCES paw_rescue.animal(id_animal),
    peso DECIMAL(5,2),
    fecha DATE,
    obs VARCHAR(500)
);

-- ================================================================
-- 9. ADOPCIONES
-- ================================================================

CREATE TABLE paw_rescue.adopcion (
    id_animal INT REFERENCES paw_rescue.animal(id_animal),
    id_usuario INT REFERENCES paw_rescue.adoptante(id_usuario),
    fecha DATE,
    PRIMARY KEY (id_animal, id_usuario)
);

CREATE TABLE paw_rescue.seg_adop (
    id_seg SERIAL PRIMARY KEY,
    id_animal INT REFERENCES paw_rescue.animal(id_animal),
    id_usuario INT REFERENCES paw_rescue.usuario(id_usuario),
    fecha DATE,
    obs VARCHAR(1000),
    estado VARCHAR(50)
);

-- ================================================================
-- 10. IMÁGENES
-- ================================================================

CREATE TABLE paw_rescue.img_animal_principal (
    id_img SERIAL PRIMARY KEY,
    id_animal INT UNIQUE REFERENCES paw_rescue.animal(id_animal),
    url VARCHAR(255),
    descripcion VARCHAR(150),
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE paw_rescue.img_animal_galeria (
    id_img SERIAL PRIMARY KEY,
    id_animal INT REFERENCES paw_rescue.animal(id_animal),
    url VARCHAR(255),
    descripcion VARCHAR(150),
    destacada BOOLEAN DEFAULT FALSE,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE paw_rescue.img_raza (
    id_img SERIAL PRIMARY KEY,
    id_raza INT REFERENCES paw_rescue.raza(id_raza),
    url VARCHAR(255) NOT NULL,
    descripcion VARCHAR(150),
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ================================================================
-- 11. CUESTIONARIO
-- ================================================================

-- ================================================================
-- CUESTIONARIO DE ADOPCIÓN – PAW RESCUE
-- ================================================================

-- ===============================
-- TIPOS DE VIVIENDA
-- ===============================
CREATE TABLE paw_rescue.tipo_vivienda (
    id_tipo SERIAL PRIMARY KEY,
    nombre VARCHAR(30) NOT NULL
);

-- ===============================
-- ESTADO DEL CUESTIONARIO
-- ===============================
CREATE TABLE paw_rescue.estado_cuestionario (
    id_estado SERIAL PRIMARY KEY,
    nombre VARCHAR(30) NOT NULL
);

-- ===============================
-- MOTIVO DE ADOPCIÓN
-- ===============================
CREATE TABLE paw_rescue.motivo_adopcion (
    id_motivo SERIAL PRIMARY KEY,
    descripcion VARCHAR(80) NOT NULL
);

-- ===============================
-- CUESTIONARIO DE ADOPCIÓN
-- ===============================
CREATE TABLE paw_rescue.cuestionario_adopcion (
    id_cuestionario SERIAL PRIMARY KEY,

    -- Usuario que ya está registrado
    id_usuario INT NOT NULL,
    FOREIGN KEY (id_usuario)
        REFERENCES paw_rescue.usuario(id_usuario)
        ON DELETE CASCADE,

    -- Identificación (CURP)
    curp CHAR(18) NOT NULL,

    -- Vivienda
    id_tipo_vivienda INT NOT NULL,
    FOREIGN KEY (id_tipo_vivienda)
        REFERENCES paw_rescue.tipo_vivienda(id_tipo),

    permiso_renta BOOLEAN,

    -- Condiciones del hogar
    comprobante_domicilio BOOLEAN NOT NULL,
    espacio_adecuado BOOLEAN NOT NULL,
    protecciones BOOLEAN NOT NULL,
    convivencia_ninos BOOLEAN NOT NULL,

    -- Compromisos del adoptante
    acepta_visitas BOOLEAN NOT NULL,
    acepta_esterilizacion BOOLEAN NOT NULL,
    compromiso_largo_plazo BOOLEAN NOT NULL,
    gastos_veterinarios BOOLEAN NOT NULL,

    -- Motivo
    id_motivo INT,
    FOREIGN KEY (id_motivo)
        REFERENCES paw_rescue.motivo_adopcion(id_motivo),

    -- Estado del cuestionario
    id_estado INT NOT NULL,
    FOREIGN KEY (id_estado)
        REFERENCES paw_rescue.estado_cuestionario(id_estado),

    observaciones VARCHAR(1000),
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);





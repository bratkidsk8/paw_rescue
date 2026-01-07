-- ================================================================
-- PROYECTO: paw_rescue (PostgreSQL)
-- ================================================================
PERMISOS PARA MOVER Y SUBIR IMAGENES A CARPETAS (LINUX)

sudo chown -R daemon:daemon /Applications/XAMPP/xamppfiles/htdocs/paw_rescue/imgReportes
sudo chmod 755 /Applications/XAMPP/xamppfiles/htdocs/paw_rescue/imgReportes


CREATE SCHEMA IF NOT EXISTS paw_rescue;

GRANT USAGE ON SCHEMA paw_rescue TO murasaki;
GRANT SELECT, INSERT, UPDATE, DELETE ON ALL TABLES IN SCHEMA paw_rescue TO murasaki;
GRANT USAGE, SELECT ON ALL SEQUENCES IN SCHEMA paw_rescue TO murasaki;

ALTER DEFAULT PRIVILEGES IN SCHEMA paw_rescue
GRANT SELECT, INSERT, UPDATE, DELETE ON TABLES TO murasaki;

ALTER DEFAULT PRIVILEGES IN SCHEMA paw_rescue
GRANT USAGE, SELECT ON SEQUENCES TO murasaki;

-- ================================================================
-- 1. SEPOMEX
-- ================================================================
DROP TABLE IF EXISTS sepomex_tmp;

CREATE TEMP TABLE sepomex_tmp (
    codigo_postal TEXT,
    entidad_id TEXT,
    municipio_id TEXT,
    municipio TEXT,
    asentamiento_id TEXT,
    asentamiento TEXT,
    tipo_asentamiento TEXT
);


\COPY sepomex_tmp
FROM '/Applications/XAMPP/xamppfiles/htdocs/paw_rescue/sepomex_base.csv'
WITH (
    FORMAT csv,
    HEADER true,
    DELIMITER ',',
    ENCODING 'LATIN1'
);


CREATE TABLE paw_rescue.sepomex (
    codigo_postal VARCHAR(5) NOT NULL,
    entidad_id INTEGER NOT NULL,
    municipio_id INTEGER NOT NULL,
    municipio VARCHAR(100) NOT NULL,
    asentamiento_id INTEGER NOT NULL,
    asentamiento VARCHAR(150) NOT NULL,
    tipo_asentamiento VARCHAR(100) NOT NULL,
    PRIMARY KEY (codigo_postal, asentamiento_id)
);

INSERT INTO paw_rescue.sepomex (
    codigo_postal,
    entidad_id,
    municipio_id,
    municipio,
    asentamiento_id,
    asentamiento,
    tipo_asentamiento
)
SELECT
    LPAD(TRIM(codigo_postal), 5, '0'),
    entidad_id::INTEGER,
    municipio_id::INTEGER,
    TRIM(municipio),
    asentamiento_id::INTEGER,
    TRIM(asentamiento),
    TRIM(tipo_asentamiento)
FROM sepomex_tmp
WHERE codigo_postal ~ '^[0-9]{4,5}$';


-- ================================================================
-- 2. CATÁLOGOS
-- ================================================================
CREATE TABLE paw_rescue.tipo_id (
    id_tipo SERIAL PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL
);

CREATE TABLE paw_rescue.especie (
    id_esp SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL
);

CREATE TABLE paw_rescue.raza (
    id_raza SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    id_esp INT REFERENCES paw_rescue.especie(id_esp)
);

CREATE TABLE paw_rescue.tam (
    id_tam SERIAL PRIMARY KEY,
    nombre VARCHAR(50)
);

CREATE TABLE paw_rescue.color (
    id_color SERIAL PRIMARY KEY,
    nombre VARCHAR(50)
);

CREATE TABLE paw_rescue.color_ojos (
    id_ojos SERIAL PRIMARY KEY,
    nombre VARCHAR(50)
);

CREATE TABLE paw_rescue.temperamento (
    id_temp SERIAL PRIMARY KEY,
    nombre VARCHAR(50)
);

CREATE TABLE paw_rescue.estatus_adop (
    id_estatus SERIAL PRIMARY KEY,
    nombre VARCHAR(50)
);

CREATE TABLE paw_rescue.estado_animal (
    id_estado SERIAL PRIMARY KEY,
    nombre VARCHAR(50)
);

-- ================================================================
-- 3. USUARIOS Y ROLES
-- ================================================================
CREATE TABLE paw_rescue.usuario (
    id_usuario SERIAL PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    primer_apellido VARCHAR(150) NOT NULL,
    segundo_apellido VARCHAR(150),
    correo VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    fecha_nacimiento DATE NOT NULL,
    CONSTRAINT chk_mayor_edad
        CHECK (fecha_nacimiento <= CURRENT_DATE - INTERVAL '18 years')
);

CREATE TABLE paw_rescue.rol (
    id_rol SERIAL PRIMARY KEY,
    nombre VARCHAR(50)
);

CREATE TABLE paw_rescue.usuario_rol (
    id_usuario INT REFERENCES paw_rescue.usuario(id_usuario),
    id_rol INT REFERENCES paw_rescue.rol(id_rol),
    PRIMARY KEY (id_usuario, id_rol)
);

CREATE TABLE paw_rescue.adoptante (
    id_usuario INT PRIMARY KEY REFERENCES paw_rescue.usuario(id_usuario)
);

CREATE TABLE paw_rescue.admin (
    id_admin SERIAL PRIMARY KEY,
    clave VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nombre VARCHAR(100)
);

-- ================================================================
-- 4. REFUGIOS
-- ================================================================
CREATE TABLE paw_rescue.refugio (
    id_ref SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    calle VARCHAR(150),
    telefono VARCHAR(20),
    codigo_postal CHAR(5),
    asentamiento_id INTEGER,
    FOREIGN KEY (codigo_postal, asentamiento_id)
        REFERENCES paw_rescue.sepomex (codigo_postal, asentamiento_id)
);

-- ================================================================
-- 5. ANIMALES
-- ================================================================
CREATE TABLE paw_rescue.animal (
    id_animal SERIAL PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    id_esp INT REFERENCES paw_rescue.especie(id_esp),
    id_raza INT REFERENCES paw_rescue.raza(id_raza),
    id_tam INT REFERENCES paw_rescue.tam(id_tam),
    id_color INT REFERENCES paw_rescue.color(id_color),
    id_ojos INT REFERENCES paw_rescue.color_ojos(id_ojos),
    id_temp INT REFERENCES paw_rescue.temperamento(id_temp),
    id_estatus INT REFERENCES paw_rescue.estatus_adop(id_estatus),
    id_estado INT REFERENCES paw_rescue.estado_animal(id_estado),
    id_ref INT REFERENCES paw_rescue.refugio(id_ref),
    edad_aprox SMALLINT CHECK (edad_aprox >= 0),
    tuvo_duenos_anteriores BOOLEAN,
    necesidades_especiales BOOLEAN DEFAULT FALSE,
    fecha_reg TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    foto VARCHAR(255)
);

-- ================================================================
-- 6. IDENTIFICACIÓN
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
-- 7. RESCATE Y REPORTES
-- ================================================================
CREATE TABLE paw_rescue.estatus_reporte (
    id_estatus SERIAL PRIMARY KEY,
    nombre VARCHAR(50)
);

INSERT INTO paw_rescue.estatus_reporte (nombre)
VALUES ('No encontrado'), ('No rescatado'), ('Rescatado');

CREATE TABLE paw_rescue.reporte_animal (
    id_reporte SERIAL PRIMARY KEY,
    nombre VARCHAR(120),
    situacion VARCHAR(50),
    herido VARCHAR(2) CHECK (herido IN ('SI','NO')),
    descripcion_heridas VARCHAR(500),
    descripcion VARCHAR(1000),
    ubicacion VARCHAR(255),
    foto VARCHAR(255),
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_usuario INT REFERENCES paw_rescue.usuario(id_usuario),
    id_estatus INT REFERENCES paw_rescue.estatus_reporte(id_estatus)
);

CREATE TABLE paw_rescue.rescate (
    id_rescate SERIAL PRIMARY KEY,
    id_animal INT NOT NULL
        REFERENCES paw_rescue.animal(id_animal)
        ON DELETE CASCADE,

    fecha DATE NOT NULL,

    lugar VARCHAR(255) NOT NULL,

    municipio VARCHAR(100) NOT NULL,

    codigo_postal CHAR(5),
    asentamiento_id INTEGER,

    FOREIGN KEY (codigo_postal, asentamiento_id)
        REFERENCES paw_rescue.sepomex (codigo_postal, asentamiento_id)
);

-- ================================================================
-- 8. SALUD
-- ================================================================
CREATE TABLE paw_rescue.enfermedad (
    id_enf SERIAL PRIMARY KEY,
    nombre VARCHAR(150)
);

CREATE TABLE paw_rescue.vacuna (
    id_vac SERIAL PRIMARY KEY,
    nombre VARCHAR(150),
    esencial BOOLEAN DEFAULT FALSE
);

CREATE TABLE paw_rescue.via_admin (
    id_via SERIAL PRIMARY KEY,
    nombre VARCHAR(50)
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

-- ================================================================
-- 9. ADOPCIÓN
-- ================================================================
CREATE TABLE paw_rescue.adopcion (
    id_animal INT REFERENCES paw_rescue.animal(id_animal),
    id_usuario INT REFERENCES paw_rescue.adoptante(id_usuario),
    fecha DATE,
    PRIMARY KEY (id_animal, id_usuario)
);

CREATE TABLE paw_rescue.cuestionario_adopcion (
    id_cuestionario SERIAL PRIMARY KEY,
    id_usuario INT REFERENCES paw_rescue.usuario(id_usuario),

    -- ================= IDENTIFICACIÓN =================
    curp VARCHAR(18),

    -- ================= DOMICILIO =================
    codigo_postal CHAR(5),
    asentamiento_id INT,
    calle VARCHAR(150),

    -- ================= ECONOMÍA / TIEMPO =================
    ingresos SMALLINT,               -- 1 a 4
    tiempo_dedicado SMALLINT,         -- 1 a 3
    personalidad VARCHAR(50),         -- Tranquila / Activa / Muy activa

    -- ================= PREFERENCIAS DE MASCOTA =================
    preferencia_especie VARCHAR(20),  -- Perro / Gato / Ambos
    nivel_actividad VARCHAR(20),      -- Tranquilo / Moderado / Activo

    -- ================= VIVIENDA =================
    tipo_vivienda VARCHAR(30),        -- Departamento / Casa chica / Casa amplia
    tiene_patio VARCHAR(5),           -- Si / No

    -- ================= MOTIVACIÓN =================
    motivo_adopcion TEXT,

    -- ================= CONVIVENCIA =================
    convivientes TEXT,                -- Adultos, Niños, Otras mascotas...
    total_personas SMALLINT,
    acuerdo_familiar VARCHAR(20),     -- Si / No / Parcial

    -- ================= EXPERIENCIA =================
    experiencia_previa VARCHAR(5),    -- Si / No
    destino_mascota VARCHAR(50),

    -- ================= RUTINA =================
    cuidador VARCHAR(50),
    frecuencia_viajes VARCHAR(50),

    -- ================= RESPONSABILIDAD =================
    conoce_costos VARCHAR(5),         -- Si / No
    gasto_mensual VARCHAR(50),

    respuesta_enfermedad VARCHAR(50),
    respuesta_danos VARCHAR(50),
    acepta_contrato VARCHAR(5),

    -- ================= PREVENCIÓN ABANDONO =================
    plan_emergencia TEXT,
    plan_largo_plazo TEXT,

    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE paw_rescue.estatus_proceso_adopcion (
    id_estatus SERIAL PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL
);

INSERT INTO paw_rescue.estatus_proceso_adopcion (nombre) VALUES
('En revisión'),
('Apto'),
('No apto'),
('Visita programada'),
('Periodo de prueba'),
('Aprobada'),
('Denegada'),
('Cancelada');

CREATE TABLE paw_rescue.solicitud_adopcion (
    id_solicitud SERIAL PRIMARY KEY,

    id_usuario INT NOT NULL
        REFERENCES paw_rescue.usuario(id_usuario),

    id_animal INT NOT NULL
        REFERENCES paw_rescue.animal(id_animal),

    id_estatus INT NOT NULL
        REFERENCES paw_rescue.estatus_proceso_adopcion(id_estatus),

    fecha_solicitud TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    -- Evaluación
    es_candidato BOOLEAN,
    observaciones TEXT,

    -- Visita
    fecha_visita DATE,

    -- Resultado
    aprobada BOOLEAN,
    motivo_denegacion TEXT,

    -- Periodo de prueba
    fecha_inicio_prueba DATE,
    fecha_fin_prueba DATE,

    CONSTRAINT uq_solicitud_unica
        UNIQUE (id_usuario, id_animal)
);


CREATE TABLE paw_rescue.compatibilidad_adopcion (
    id_compat SERIAL PRIMARY KEY,

    id_usuario INT
        REFERENCES paw_rescue.usuario(id_usuario),

    id_animal INT
        REFERENCES paw_rescue.animal(id_animal),

    nivel_compatibilidad SMALLINT CHECK (nivel_compatibilidad BETWEEN 1 AND 100),

    observaciones TEXT,

    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE paw_rescue.tipo_cita (
    id_tipo SERIAL PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL
);

INSERT INTO paw_rescue.tipo_cita (nombre) VALUES
('Conocer a la mascota'),
('Entrevista en persona'),
('Visita domiciliaria'),
('Entrega de mascota');


CREATE TABLE paw_rescue.estatus_cita (
    id_estatus SERIAL PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL
);

INSERT INTO paw_rescue.estatus_cita (nombre) VALUES
('Programada'),
('Confirmada'),
('Realizada'),
('Cancelada'),
('No asistió');


CREATE TABLE paw_rescue.cita_adopcion (
    id_cita SERIAL PRIMARY KEY,

    id_solicitud INT NOT NULL
        REFERENCES paw_rescue.solicitud_adopcion(id_solicitud)
        ON DELETE CASCADE,

    id_tipo INT NOT NULL
        REFERENCES paw_rescue.tipo_cita(id_tipo),

    id_estatus INT NOT NULL
        REFERENCES paw_rescue.estatus_cita(id_estatus),

    fecha DATE NOT NULL,
    hora TIME NOT NULL,

    observaciones TEXT,
    creada_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE paw_rescue.seguimiento_adopcion (
    id_seguimiento SERIAL PRIMARY KEY,
    id_solicitud INT NOT NULL REFERENCES paw_rescue.solicitud_adopcion(id_solicitud),

    id_tipo_cita INT NOT NULL REFERENCES paw_rescue.tipo_cita(id_tipo),
    id_estatus_cita INT NOT NULL REFERENCES paw_rescue.estatus_cita(id_estatus),

    fecha DATE NOT NULL,
    hora TIME NOT NULL,

    es_candidato BOOLEAN,
    fecha_inicio_prueba DATE,
    fecha_fin_prueba DATE,
    aprobada BOOLEAN,

    observaciones TEXT
);


-- ================================================================
-- 10. ADMIN INICIAL
-- ================================================================
INSERT INTO paw_rescue.admin (clave, password, nombre)
VALUES (
    'paw_admin',
    '$2y$10$sEKRNOL8A8EDsD5RURta../KF0fWGWBcG.BsV0iPBb3DphfTRFcnS',
    'Saul Martinez'
);

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
-- 1. CATÁLOGOS
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
    id_esp INT NOT NULL REFERENCES paw_rescue.especie(id_esp)
);

CREATE TABLE paw_rescue.tam (
    id_tam SERIAL PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL
);

CREATE TABLE paw_rescue.temperamento (
    id_temp SERIAL PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL
);

CREATE TABLE paw_rescue.color (
    id_color SERIAL PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL
);

CREATE TABLE paw_rescue.color_ojos (
    id_ojos SERIAL PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL
);

CREATE TABLE paw_rescue.estatus_adop (
    id_estatus SERIAL PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL
);

CREATE TABLE paw_rescue.estado_animal (
    id_estado SERIAL PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL
);

-- ================================================================
-- 2. USUARIOS Y ROLES
-- ================================================================

CREATE TABLE paw_rescue.usuario (
    id_usuario SERIAL PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    primer_apellido VARCHAR(150) NOT NULL,
    segundo_apellido VARCHAR(150) NOT NULL,
    correo VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    fecha_nacimiento DATE NOT NULL,
    CONSTRAINT chk_mayor_edad 
        CHECK (fecha_nacimiento <= CURRENT_DATE - INTERVAL '18 years')
);

CREATE TABLE paw_rescue.admin (
    id_admin SERIAL PRIMARY KEY,
    clave VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nombre VARCHAR(100)
);

CREATE TABLE paw_rescue.rol (
    id_rol SERIAL PRIMARY KEY,
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
    id_ref SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    direccion VARCHAR(255),
    telefono VARCHAR(20)
);

-- ================================================================
-- 4. ANIMALES
-- ================================================================

CREATE TABLE paw_rescue.animal (
    id_animal SERIAL PRIMARY KEY,
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
    tuvo_duenos_anteriores BOOLEAN,
    necesidades_especiales BOOLEAN DEFAULT FALSE,
    fecha_reg TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ================================================================
-- 5. CUIDADOS ESPECIALES
-- ================================================================

CREATE TABLE paw_rescue.tipo_cuidado_especial (
    id_cuidado SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion VARCHAR(300)
);

CREATE TABLE paw_rescue.animal_cuidado_especial (
    id_animal INT REFERENCES paw_rescue.animal(id_animal) ON DELETE CASCADE,
    id_cuidado INT REFERENCES paw_rescue.tipo_cuidado_especial(id_cuidado),
    observaciones VARCHAR(500),
    PRIMARY KEY (id_animal, id_cuidado)
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
-- 7. RESCATE Y EVENTOS
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
-- 8. SALUD
-- ================================================================

CREATE TABLE paw_rescue.enfermedad (
    id_enf SERIAL PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL
);

CREATE TABLE paw_rescue.vacuna (
    id_vac SERIAL PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    esencial BOOLEAN DEFAULT FALSE
);

CREATE TABLE paw_rescue.via_admin (
    id_via SERIAL PRIMARY KEY,
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
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
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

-- ================================================================
-- 10. CUESTIONARIO
-- ================================================================

CREATE TABLE paw_rescue.tipo_vivienda (
    id_tipo SERIAL PRIMARY KEY,
    nombre VARCHAR(30) NOT NULL
);

CREATE TABLE paw_rescue.estado_cuestionario (
    id_estado SERIAL PRIMARY KEY,
    nombre VARCHAR(30) NOT NULL
);

CREATE TABLE paw_rescue.motivo_adopcion (
    id_motivo SERIAL PRIMARY KEY,
    descripcion VARCHAR(80) NOT NULL
);

CREATE TABLE paw_rescue.cuestionario_adopcion (
    id_cuestionario SERIAL PRIMARY KEY,
    id_usuario INT REFERENCES paw_rescue.usuario(id_usuario),
    curp CHAR(18) NOT NULL,
    id_tipo_vivienda INT REFERENCES paw_rescue.tipo_vivienda(id_tipo),
    nivel_economico VARCHAR(30),
    permiso_renta BOOLEAN,
    comprobante_domicilio BOOLEAN,
    espacio_adecuado BOOLEAN,
    protecciones BOOLEAN,
    convivencia_ninos BOOLEAN,
    acepta_visitas BOOLEAN,
    acepta_esterilizacion BOOLEAN,
    compromiso_largo_plazo BOOLEAN,
    gastos_veterinarios BOOLEAN,
    id_motivo INT REFERENCES paw_rescue.motivo_adopcion(id_motivo),
    id_estado INT REFERENCES paw_rescue.estado_cuestionario(id_estado),
    observaciones VARCHAR(1000),
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ================================================================
-- 11. VISITAS
-- ================================================================

CREATE TABLE paw_rescue.visita_albergue (
    id_visita SERIAL PRIMARY KEY,
    id_usuario INT REFERENCES paw_rescue.usuario(id_usuario),
    id_ref INT REFERENCES paw_rescue.refugio(id_ref),
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    estado VARCHAR(30)
);

-- ================================================================
-- 12. COLABORADORES
-- ================================================================

CREATE TABLE paw_rescue.colaborador (
    id_usuario INT PRIMARY KEY REFERENCES paw_rescue.usuario(id_usuario),
    puesto VARCHAR(50),
    fecha_ingreso DATE,
    activo BOOLEAN DEFAULT TRUE
);

CREATE TABLE paw_rescue.asistencia_colaborador (
    id_asistencia SERIAL PRIMARY KEY,
    id_usuario INT REFERENCES paw_rescue.colaborador(id_usuario),
    fecha DATE,
    hora_entrada TIME,
    hora_salida TIME
);

CREATE TABLE paw_rescue.colaborador_refugio (
    id_usuario INT REFERENCES paw_rescue.colaborador(id_usuario),
    id_ref INT REFERENCES paw_rescue.refugio(id_ref),
    fecha_inicio DATE,
    fecha_fin DATE,
    PRIMARY KEY (id_usuario, id_ref)
);

-- ================================================================
-- 13. LISTA NEGRA Y RETIROS
-- ================================================================

CREATE TABLE paw_rescue.lista_negra (
    id_persona SERIAL PRIMARY KEY,
    nombre VARCHAR(150),
    primer_apellido VARCHAR(150),
    segundo_apellido VARCHAR(150),
    curp CHAR(18),
    motivo VARCHAR(500),
    fecha DATE DEFAULT CURRENT_DATE
);

CREATE TABLE paw_rescue.retiro_mascota (
    id_retiro SERIAL PRIMARY KEY,
    id_animal INT REFERENCES paw_rescue.animal(id_animal),
    id_persona INT REFERENCES paw_rescue.lista_negra(id_persona),
    fecha DATE DEFAULT CURRENT_DATE,
    motivo VARCHAR(500)
);

-- ================================================================
-- ADMIN INICIAL
-- ==========================================

INSERT INTO paw_rescue.admin (clave, password, nombre)
VALUES (
    'paw_admin',
    'paw10',
    'Saul Martinez'
);


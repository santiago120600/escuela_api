DROP DATABASE IF EXISTS escuela_api;
CREATE DATABASE IF NOT EXISTS escuela_api;
USE escuela_api;

CREATE TABLE estudiante(
    estudiante_id INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    nombre_estudiante VARCHAR(45),
    email_estudiante VARCHAR(45)
);

CREATE TABLE curso(
    curso_id INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    nombre_curso VARCHAR(45),
    duracion_curso INT
);

CREATE TABLE disciplina(
    disciplina_id INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    nombre_disciplina VARCHAR(45)
);

CREATE TABLE matricula(
    matricula_id INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    estudiante_fk INT NOT NULL,
    curso_fk INT NOT NULL,
    FOREIGN KEY(estudiante_fk) REFERENCES estudiante(estudiante_id),
    FOREIGN KEY(curso_fk) REFERENCES curso(curso_id)
);
CREATE TABLE curso_disciplina(
    curso_disciplina_id INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    curso_fk INT NOT NULL,
    disciplina_fk INT NOT NULL,
    FOREIGN KEY(curso_fk) REFERENCES curso(curso_id),
    FOREIGN KEY(disciplina_fk) REFERENCES disciplina(disciplina_id)
);

CREATE OR REPLACE VIEW matricula_view AS SELECT m.matricula_id, e.*,c.* FROM matricula AS m JOIN estudiante AS e ON e.estudiante_id = m.estudiante_fk JOIN curso AS c ON c.curso_id = m.curso_fk;

CREATE OR REPLACE VIEW curso_disciplina_view AS SELECT cd.curso_disciplina_id,c.*,d.* FROM curso_disciplina AS cd JOIN curso AS c ON c.curso_id = cd.curso_fk JOIN disciplina AS d ON d.disciplina_id = cd.disciplina_fk;
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

CREATE SCHEMA IF NOT EXISTS `taskcheck` DEFAULT CHARACTER SET utf8 ;
USE `taskcheck` ;

-- Tabela usuario
CREATE TABLE IF NOT EXISTS `taskcheck`.`usuario` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `senha` VARCHAR(255) NOT NULL,
  `tipo` ENUM('aluno', 'professor', 'coordenador') NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC))
ENGINE = InnoDB;

-- Tabela curso
CREATE TABLE IF NOT EXISTS `taskcheck`.`curso` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

-- Tabela aluno
CREATE TABLE IF NOT EXISTS `taskcheck`.`aluno` (
  `id_usuario` INT NOT NULL,
  `matricula` INT(8) NOT NULL,
  `id_curso` INT NOT NULL,
  PRIMARY KEY (`id_usuario`),
  INDEX `fk_aluno_curso_idx` (`id_curso` ASC),
  CONSTRAINT `fk_usuario_aluno`
    FOREIGN KEY (`id_usuario`)
    REFERENCES `taskcheck`.`usuario` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_aluno_curso`
    FOREIGN KEY (`id_curso`)
    REFERENCES `taskcheck`.`curso` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- Tabela professor
CREATE TABLE IF NOT EXISTS `taskcheck`.`professor` (
  `id_usuario` INT NOT NULL,
  PRIMARY KEY (`id_usuario`),
  CONSTRAINT `fk_usuario_professor`
    FOREIGN KEY (`id_usuario`)
    REFERENCES `taskcheck`.`usuario` (`id`)
    ON DELETE CASCADE)
ENGINE = InnoDB;

-- Tabela coordenador
CREATE TABLE IF NOT EXISTS `taskcheck`.`coordenador` (
  `id_usuario` INT NOT NULL,
  `id_curso_responsavel` INT NOT NULL,
  PRIMARY KEY (`id_usuario`),
  INDEX `fk_curso_responsavel_idx` (`id_curso_responsavel` ASC),
  CONSTRAINT `fk_usuario_coordenador`
    FOREIGN KEY (`id_usuario`)
    REFERENCES `taskcheck`.`usuario` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_curso_responsavel`
    FOREIGN KEY (`id_curso_responsavel`)
    REFERENCES `taskcheck`.`curso` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- Tabela categoria
CREATE TABLE IF NOT EXISTS `taskcheck`.`categoria` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(100) NOT NULL,
  `descricao` TEXT NOT NULL,
  `carga_horaria` INT NOT NULL,
  `id_curso` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_categoria_curso_idx` (`id_curso` ASC),
  CONSTRAINT `fk_categoria_curso`
    FOREIGN KEY (`id_curso`)
    REFERENCES `taskcheck`.`curso` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- Tabela relatorio_atividade
CREATE TABLE IF NOT EXISTS `taskcheck`.`relatorio_atividade` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(100) NOT NULL,
  `texto_reflexao` TEXT NOT NULL,
  `data_realizacao` DATE NOT NULL,
  `data_envio` DATE NOT NULL,
  `status` ENUM('Aguardando validacao', 'Invalido', 'Valido', 'Recategorizacao') NOT NULL,
  `horas_validadas` INT,
  `certificado` BLOB NOT NULL,
  `id_aluno` INT NOT NULL,
  `id_categoria` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_aluno_relatorio_idx` (`id_aluno` ASC),
  INDEX `fk_categoria_relatorio_idx` (`id_categoria` ASC),
  CONSTRAINT `fk_aluno_relatorio`
    FOREIGN KEY (`id_aluno`)
    REFERENCES `taskcheck`.`aluno` (`id_usuario`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_categoria_relatorio`
    FOREIGN KEY (`id_categoria`)
    REFERENCES `taskcheck`.`categoria` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- Tabela feedback
CREATE TABLE IF NOT EXISTS `taskcheck`.`feedback` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `texto_feedback` TEXT NOT NULL,
  `data_envio` DATE NOT NULL,
  `id_professor` INT NULL,
  `id_relatorio` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_professor_feedback_idx` (`id_professor` ASC),
  INDEX `fk_relatorio_feedback_idx` (`id_relatorio` ASC),
  CONSTRAINT `fk_professor_feedback`
    FOREIGN KEY (`id_professor`)
    REFERENCES `taskcheck`.`professor` (`id_usuario`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_relatorio_feedback`
    FOREIGN KEY (`id_relatorio`)
    REFERENCES `taskcheck`.`relatorio_atividade` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- Tabela professor_curso
CREATE TABLE IF NOT EXISTS `taskcheck`.`professor_curso` (
    `id_professor` INT NOT NULL,
    `id_curso` INT NOT NULL,
    PRIMARY KEY (`id_professor`, `id_curso`),
    INDEX `fk_professor_curso_professor_idx` (`id_professor` ASC),
    INDEX `fk_professor_curso_curso_idx` (`id_curso` ASC),
    CONSTRAINT `fk_professor_curso_professor`
    FOREIGN KEY (`id_professor`)
    REFERENCES `taskcheck`.`professor` (`id_usuario`)
    ON DELETE CASCADE,
    CONSTRAINT `fk_professor_curso_curso`
    FOREIGN KEY (`id_curso`)
    REFERENCES `taskcheck`.`curso` (`id`)
    ON DELETE CASCADE)
ENGINE = InnoDB;

-- Table `feedback_historico`
CREATE TABLE IF NOT EXISTS `taskcheck`.`feedback_historico` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `id_feedback` INT NULL DEFAULT NULL,
  `id_relatorio` INT NOT NULL,
  `texto_feedback` TEXT NOT NULL,
  `data_envio` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `id_professor` INT NULL DEFAULT NULL,
  `versao` INT NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `id_feedback` (`id_feedback` ASC),
  INDEX `id_relatorio` (`id_relatorio` ASC),
  INDEX `id_professor` (`id_professor` ASC),
  CONSTRAINT `feedback_historico_ibfk_2`
    FOREIGN KEY (`id_relatorio`)
    REFERENCES `taskcheck`.`relatorio_atividade` (`id`),
  CONSTRAINT `feedback_historico_ibfk_3`
    FOREIGN KEY (`id_professor`)
    REFERENCES `taskcheck`.`professor` (`id_usuario`)
) ENGINE = InnoDB;

-- Table `historico_relatorio_atividade`
CREATE TABLE IF NOT EXISTS `taskcheck`.`historico_relatorio_atividade` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `id_relatorio` INT NOT NULL,
  `nome_anterior` VARCHAR(100) NULL DEFAULT NULL,
  `texto_reflexao_anterior` TEXT NULL DEFAULT NULL,
  `data_realizacao_anterior` DATE NULL DEFAULT NULL,
  `status_anterior` ENUM('Aguardando validacao', 'Invalido', 'Valido', 'Recategorizacao') NULL DEFAULT NULL,
  `data_alteracao` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `certificado_anterior` BLOB NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_relatorio_historico` (`id_relatorio` ASC),
  CONSTRAINT `fk_relatorio_historico`
    FOREIGN KEY (`id_relatorio`)
    REFERENCES `taskcheck`.`relatorio_atividade` (`id`)
    ON DELETE CASCADE
) ENGINE = InnoDB;

-- Table `reversao_validacao`
CREATE TABLE IF NOT EXISTS `taskcheck`.`reversao_validacao` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `id_relatorio` INT NOT NULL,
  `justificativa` TEXT NOT NULL,
  `data_reversao` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `id_professor` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `id_relatorio` (`id_relatorio` ASC),
  INDEX `id_professor` (`id_professor` ASC),
  CONSTRAINT `reversao_validacao_ibfk_1`
    FOREIGN KEY (`id_relatorio`)
    REFERENCES `taskcheck`.`relatorio_atividade` (`id`),
  CONSTRAINT `reversao_validacao_ibfk_2`
    FOREIGN KEY (`id_professor`)
    REFERENCES `taskcheck`.`professor` (`id_usuario`)
) ENGINE = InnoDB;

INSERT INTO `taskcheck`.`curso` (nome) VALUES ("Engenharia de Software");

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
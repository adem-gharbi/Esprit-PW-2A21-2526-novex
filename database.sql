-- Création de la base de données (si elle n'existe pas)
CREATE DATABASE IF NOT EXISTS `projet_ecologique` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `projet_ecologique`;

-- Structure de la table `client`
CREATE TABLE IF NOT EXISTS `client` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fullname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `birthdate` date NOT NULL,
  `tel` varchar(20) NOT NULL,
  `sexe` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Structure de la table `admin`
CREATE TABLE IF NOT EXISTS `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fullname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'admin',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Insertion d'un administrateur par défaut (mot de passe: admin123)
-- NB: Il est toujours conseillé de hacher les mots de passe avec password_hash() en PHP pour la production. 
INSERT INTO `admin` (`fullname`, `email`, `password`, `role`) VALUES
('Super Admin', 'admin@projet-ecologique.com', 'admin123', 'admin');

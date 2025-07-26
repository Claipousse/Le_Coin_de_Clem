create database if not exists coin_de_clem character set utf8mb4 collate utf8mb4_unicode_ci;
use coin_de_clem;

-- Annonces (dans la page ventes) :
create table annonces (
	id int auto_increment primary key,
	titre varchar(100) not null,
	description varchar(2000) not null,
	categorie enum('Gaming', 'Informatique', 'Livre', 'Collection', 'Vêtement', 'Mobilier', 'Divers'),
	prix decimal(5, 2) not null,
	etat enum('Neuf', 'Très bon état', 'Bon état', 'Etat satisfaisant', 'Pour pièces') not null,
	statut enum('Disponible', 'Vendu') not null,
	vues int default 0,
	date_creation datetime default now(),
	-- Index pour faciliter la recherche dans les tables, utile pour la barre de recherche :
	index idx_categorie (categorie),
	index idx_statut (statut),
	index idx_prix (prix)	
);

create table photos (
	id int auto_increment primary key,
	annonce_id int not null,
	nom_fichier varchar(255) not null,
	ordre int default 1,
	foreign key (annonce_id) references annonces(id) on delete cascade,
	index idx_annonce (annonce_id),
	index idx_ordre (ordre)
);

create table admin (
	id int auto_increment primary key,
	username varchar(50) not null unique,
	password varchar(255) not null
);
-- SQL schema: CREATE DATABASE + tables `users` and `documents` (FK user_id, ON DELETE CASCADE)

CREATE DATABASE IF NOT EXISTS `edms`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `edms`;

create table if not exists users (
	id int unsigned auto_increment primary key,
	fio varchar(255) not null,
	city varchar(100) not null,
	phone varchar(100) not null,
	email varchar(255) not null,
	login varchar(100) not null,
	password varchar(255) not null,
	created_at DATETIME not null default current_timestamp,
	unique key uq_users_email(email),
	unique key uq_users_login(login),
	key idx_users_fio(fio)
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4;


create table  if not exists documents (
	id INT UNSIGNED auto_increment primary key,
	user_id INT UNSIGNED not null,
	name varchar(255) not null,
	description TEXT default null,
	doc_type ENUM('Excel', 'Word', 'TXT') not null,
	created_at DATETIME not null default current_timestamp,
	updated_at DATETIME null default null on update current_timestamp,
	key idx_documents_name(name), 
	key idx_document_user_id(user_id),
	constraint fk_document_user
		foreign key (user_id) references users(id)
		on delete cascade
		on update cascade
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


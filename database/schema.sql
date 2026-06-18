CREATE DATABASE IF NOT EXISTS movielist_nrwa
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE movielist_nrwa;

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    registered_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE movies (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    director VARCHAR(150) NOT NULL,
    release_year SMALLINT UNSIGNED NOT NULL,
    genre VARCHAR(100) NOT NULL,
    description TEXT NULL,
    added_by_user_id INT UNSIGNED NULL,
    CONSTRAINT fk_movies_added_by_user
        FOREIGN KEY (added_by_user_id) REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL,
    CONSTRAINT chk_movies_release_year
        CHECK (release_year BETWEEN 1888 AND 2100)
) ENGINE=InnoDB;

CREATE TABLE user_movies (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    status ENUM('watched', 'want_to_watch') NOT NULL,
    rating TINYINT UNSIGNED NULL,
    comment VARCHAR(500) NULL,
    watched_at DATE NULL,
    user_id INT UNSIGNED NOT NULL,
    movie_id INT UNSIGNED NOT NULL,
    CONSTRAINT fk_user_movies_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT fk_user_movies_movie
        FOREIGN KEY (movie_id) REFERENCES movies(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT uq_user_movies_user_movie
        UNIQUE (user_id, movie_id),
    CONSTRAINT chk_user_movies_rating
        CHECK (rating IS NULL OR rating BETWEEN 1 AND 5)
) ENGINE=InnoDB;

INSERT INTO users (name, email, password_hash, role) VALUES
('Test Korisnik', 'test@example.com', '$2y$10$abcdefghijklmnopqrstuv', 'user'),
('Admin', 'admin@example.com', '$2y$10$abcdefghijklmnopqrstuv', 'admin');

INSERT INTO movies (title, director, release_year, genre, description) VALUES
('The Shawshank Redemption', 'Frank Darabont', 1994, 'Drama', 'Prica o nadi, prijateljstvu i zivotu u zatvoru.'),
('The Godfather', 'Francis Ford Coppola', 1972, 'Crime', 'Klasik o obitelji Corleone i svijetu organiziranog kriminala.'),
('The Dark Knight', 'Christopher Nolan', 2008, 'Action', 'Batman se suocava s Jokerom u borbi za Gotham City.'),
('Pulp Fiction', 'Quentin Tarantino', 1994, 'Crime', 'Isprepletene price iz kriminalnog podzemlja Los Angelesa.'),
('Forrest Gump', 'Robert Zemeckis', 1994, 'Drama', 'Zivotna prica covjeka koji prolazi kroz vazne trenutke americke povijesti.'),
('Inception', 'Christopher Nolan', 2010, 'Science Fiction', 'Tim strucnjaka ulazi u snove kako bi izveo slozenu misiju.'),
('The Matrix', 'Lana Wachowski, Lilly Wachowski', 1999, 'Science Fiction', 'Programer otkriva skrivenu stvarnost iza svijeta koji poznaje.'),
('Interstellar', 'Christopher Nolan', 2014, 'Science Fiction', 'Putovanje kroz svemir u potrazi za novim domom za covjecanstvo.'),
('Fight Club', 'David Fincher', 1999, 'Drama', 'Nezadovoljni uredski radnik ulazi u neobican i opasan klub.'),
('Goodfellas', 'Martin Scorsese', 1990, 'Crime', 'Uspon i pad mafijasa Henryja Hilla.'),
('Parasite', 'Bong Joon-ho', 2019, 'Thriller', 'Dvije obitelji razlicitog drustvenog statusa ulaze u opasan odnos.'),
('Gladiator', 'Ridley Scott', 2000, 'Action', 'Rimski general postaje gladijator i trazi osvetu.'),
('The Lord of the Rings: The Fellowship of the Ring', 'Peter Jackson', 2001, 'Fantasy', 'Prvi dio putovanja druzine koja mora unistiti Prsten.'),
('Spirited Away', 'Hayao Miyazaki', 2001, 'Animation', 'Djevojcica ulazi u cudnovati svijet duhova.'),
('Whiplash', 'Damien Chazelle', 2014, 'Drama', 'Mladi bubnjar prolazi kroz intenzivan odnos s profesorom glazbe.');

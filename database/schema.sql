CREATE DATABASE IF NOT EXISTS movielist_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE movielist_db;

CREATE TABLE korisnici (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ime VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    lozinka_hash VARCHAR(255) NOT NULL,
    uloga ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    datum_registracije TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE filmovi (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    naslov VARCHAR(255) NOT NULL,
    redatelj VARCHAR(150) NOT NULL,
    godina SMALLINT UNSIGNED NOT NULL,
    zanr VARCHAR(100) NOT NULL,
    opis TEXT,
    dodao_korisnik_id INT UNSIGNED NULL,
    datum_dodavanja TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_filmovi_dodao_korisnik
        FOREIGN KEY (dodao_korisnik_id)
        REFERENCES korisnici(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE,
    CONSTRAINT chk_filmovi_godina
        CHECK (godina BETWEEN 1888 AND 2100)
) ENGINE=InnoDB;

CREATE TABLE korisnik_filmovi (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    status ENUM('watched', 'want_to_watch') NOT NULL,
    ocjena TINYINT UNSIGNED NULL,
    komentar VARCHAR(500) NULL,
    datum_gledanja DATE NULL,
    korisnik_id INT UNSIGNED NOT NULL,
    film_id INT UNSIGNED NOT NULL,
    datum_kreiranja TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    datum_izmjene TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_korisnik_filmovi_korisnik
        FOREIGN KEY (korisnik_id)
        REFERENCES korisnici(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_korisnik_filmovi_film
        FOREIGN KEY (film_id)
        REFERENCES filmovi(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT uq_korisnik_filmovi_korisnik_film
        UNIQUE (korisnik_id, film_id),
    CONSTRAINT chk_korisnik_filmovi_ocjena
        CHECK (ocjena IS NULL OR ocjena BETWEEN 1 AND 5)
) ENGINE=InnoDB;

INSERT INTO filmovi (naslov, redatelj, godina, zanr, opis) VALUES
('The Shawshank Redemption', 'Frank Darabont', 1994, 'Drama', 'Prica o nadi, prijateljstvu i zivotu u zatvoru Shawshank.'),
('The Godfather', 'Francis Ford Coppola', 1972, 'Crime', 'Kultna kriminalisticka drama o obitelji Corleone.'),
('The Dark Knight', 'Christopher Nolan', 2008, 'Action', 'Batman se suocava s Jokerom u borbi za Gotham.'),
('Pulp Fiction', 'Quentin Tarantino', 1994, 'Crime', 'Isprepletene price kriminalaca, boksera i placenih ubojica.'),
('Forrest Gump', 'Robert Zemeckis', 1994, 'Drama', 'Zivotna prica covjeka koji svjedoci vaznim trenucima americke povijesti.'),
('Inception', 'Christopher Nolan', 2010, 'Science Fiction', 'Tim ulazi u snove kako bi ukrao ili usadio ideje.'),
('Fight Club', 'David Fincher', 1999, 'Drama', 'Nezadovoljni uredski radnik ulazi u podzemni svijet borilackog kluba.'),
('The Matrix', 'Lana Wachowski, Lilly Wachowski', 1999, 'Science Fiction', 'Haker otkriva da je stvarnost simulacija.'),
('Interstellar', 'Christopher Nolan', 2014, 'Science Fiction', 'Skupina istrazivaca putuje kroz svemir kako bi pronasla novi dom za covjecanstvo.'),
('Parasite', 'Bong Joon-ho', 2019, 'Thriller', 'Crna komedija i triler o klasnim razlikama.'),
('Spirited Away', 'Hayao Miyazaki', 2001, 'Animation', 'Djevojcica ulazi u carobni svijet duhova.'),
('Gladiator', 'Ridley Scott', 2000, 'Action', 'Rimski general postaje gladijator i trazi pravdu.');

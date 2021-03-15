-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Czas generowania: 15 Mar 2021, 17:43
-- Wersja serwera: 10.4.17-MariaDB
-- Wersja PHP: 8.0.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `ludo`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `games`
--

CREATE TABLE `games` (
  `id` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `free_spaces` int(11) NOT NULL DEFAULT 4,
  `turn_start_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `throwed_cube` tinyint(1) NOT NULL DEFAULT 0,
  `last_throw_points` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Zrzut danych tabeli `games`
--

INSERT INTO `games` (`id`, `status`, `free_spaces`, `turn_start_time`, `throwed_cube`, `last_throw_points`) VALUES
(1, 1, 0, '2021-03-15 16:42:10', 0, 0),
(2, 1, 2, '2021-03-15 16:42:10', 0, 0),
(3, 1, 1, '2021-03-15 16:42:10', 0, 0);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `pawns`
--

CREATE TABLE `pawns` (
  `id` int(11) NOT NULL,
  `position` int(11) NOT NULL,
  `out_of_board` tinyint(1) NOT NULL DEFAULT 1,
  `in_home` tinyint(1) NOT NULL DEFAULT 0,
  `color_index` int(11) NOT NULL,
  `game_id` int(11) DEFAULT NULL,
  `player_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Zrzut danych tabeli `pawns`
--

INSERT INTO `pawns` (`id`, `position`, `out_of_board`, `in_home`, `color_index`, `game_id`, `player_id`) VALUES
(1, 0, 1, 0, 1, 2, 5),
(2, 1, 1, 0, 1, 2, 5),
(3, 2, 1, 0, 1, 2, 5),
(4, 3, 1, 0, 1, 2, 5),
(5, 0, 1, 0, 2, 2, 6),
(6, 1, 1, 0, 2, 2, 6),
(7, 2, 1, 0, 2, 2, 6),
(8, 3, 1, 0, 2, 2, 6),
(9, 0, 1, 0, 0, 3, 9),
(10, 1, 1, 0, 0, 3, 9),
(11, 2, 1, 0, 0, 3, 9),
(12, 3, 1, 0, 0, 3, 9),
(13, 0, 1, 0, 1, 3, 8),
(14, 1, 1, 0, 1, 3, 8),
(15, 2, 1, 0, 1, 3, 8),
(16, 3, 1, 0, 1, 3, 8),
(17, 0, 1, 0, 2, 3, 7),
(18, 1, 1, 0, 2, 3, 7),
(19, 2, 1, 0, 2, 3, 7),
(20, 3, 1, 0, 2, 3, 7);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `players`
--

CREATE TABLE `players` (
  `id` int(11) NOT NULL,
  `nick` varchar(60) COLLATE utf8_polish_ci NOT NULL,
  `color` varchar(60) COLLATE utf8_polish_ci NOT NULL DEFAULT 'grey',
  `status` int(11) NOT NULL DEFAULT 0,
  `game_id` int(11) DEFAULT NULL,
  `color_index` int(11) NOT NULL DEFAULT -1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Zrzut danych tabeli `players`
--

INSERT INTO `players` (`id`, `nick`, `color`, `status`, `game_id`, `color_index`) VALUES
(1, 'maks', 'grey', 2, 1, -1),
(2, 'dana', 'grey', 2, 1, -1),
(3, 'dana', 'grey', 3, 1, -1),
(4, 'dana', 'grey', 2, 1, -1),
(5, 'dana_malina', 'grey', 2, 2, 1),
(6, 'tank', 'grey', 3, 2, 2),
(7, 'dark', 'grey', 2, 3, 2),
(8, 'komar', 'grey', 2, 3, 1),
(9, 'tank', 'grey', 3, 3, 0);

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `pawns`
--
ALTER TABLE `pawns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `game_id` (`game_id`),
  ADD KEY `player_id` (`player_id`);

--
-- Indeksy dla tabeli `players`
--
ALTER TABLE `players`
  ADD PRIMARY KEY (`id`),
  ADD KEY `game_id` (`game_id`);

--
-- AUTO_INCREMENT dla zrzuconych tabel
--

--
-- AUTO_INCREMENT dla tabeli `games`
--
ALTER TABLE `games`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT dla tabeli `pawns`
--
ALTER TABLE `pawns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT dla tabeli `players`
--
ALTER TABLE `players`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Ograniczenia dla zrzutów tabel
--

--
-- Ograniczenia dla tabeli `pawns`
--
ALTER TABLE `pawns`
  ADD CONSTRAINT `pawns_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`),
  ADD CONSTRAINT `pawns_ibfk_2` FOREIGN KEY (`player_id`) REFERENCES `players` (`id`);

--
-- Ograniczenia dla tabeli `players`
--
ALTER TABLE `players`
  ADD CONSTRAINT `players_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

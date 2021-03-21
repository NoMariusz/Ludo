-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Czas generowania: 21 Mar 2021, 14:37
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
(1, 1, 2, '2021-03-21 13:37:07', 0, 6);

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
  `player_id` int(11) NOT NULL,
  `position_out_board` int(11) NOT NULL,
  `can_be_moved` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Zrzut danych tabeli `pawns`
--

INSERT INTO `pawns` (`id`, `position`, `out_of_board`, `in_home`, `color_index`, `game_id`, `player_id`, `position_out_board`, `can_be_moved`) VALUES
(1, 30, 0, 0, 2, 1, 2, 0, 0),
(2, 39, 0, 0, 2, 1, 2, 1, 0),
(3, 2, 1, 0, 2, 1, 2, 2, 0),
(4, 3, 0, 1, 2, 1, 2, 3, 0),
(5, 31, 0, 0, 3, 1, 1, 0, 0),
(6, 20, 0, 0, 3, 1, 1, 1, 0),
(7, 2, 0, 1, 3, 1, 1, 2, 0),
(8, 25, 0, 0, 3, 1, 1, 3, 0);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `players`
--

CREATE TABLE `players` (
  `id` int(11) NOT NULL,
  `nick` varchar(60) COLLATE utf8_polish_ci NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `game_id` int(11) DEFAULT NULL,
  `color_index` int(11) NOT NULL DEFAULT -1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Zrzut danych tabeli `players`
--

INSERT INTO `players` (`id`, `nick`, `status`, `game_id`, `color_index`) VALUES
(1, 'mainP', 3, 1, 3),
(2, 'privateM', 2, 1, 2);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT dla tabeli `pawns`
--
ALTER TABLE `pawns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT dla tabeli `players`
--
ALTER TABLE `players`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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

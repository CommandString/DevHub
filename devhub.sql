SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `activites` (
  `id` varchar(16) COLLATE utf8mb4_general_ci NOT NULL,
  `userid` varchar(16) COLLATE utf8mb4_general_ci NOT NULL,
  `type` int NOT NULL,
  `date` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `data` json DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `comments` (
  `id` varchar(16) COLLATE utf8mb4_general_ci NOT NULL,
  `poster` varchar(16) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci NOT NULL,
  `posted` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `question` varchar(16) COLLATE utf8mb4_general_ci NOT NULL,
  `isAnswer` tinyint(1) NOT NULL,
  `upVotes` int NOT NULL,
  `downVotes` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `questions` (
  `id` varchar(16) COLLATE utf8mb4_general_ci NOT NULL,
  `poster` varchar(16) COLLATE utf8mb4_general_ci NOT NULL,
  `title` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci NOT NULL,
  `posted` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `tags` json NOT NULL,
  `upvotes` int NOT NULL,
  `downvotes` int NOT NULL,
  `answered` tinyint(1) NOT NULL,
  `views` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `sessions` (
  `id` varchar(16) COLLATE utf8mb4_general_ci NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `expires` int NOT NULL
) ;

CREATE TABLE `users` (
  `id` varchar(16) COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(64) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `firstName` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `lastName` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `registered` varchar(10) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


ALTER TABLE `activites`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);
COMMIT;

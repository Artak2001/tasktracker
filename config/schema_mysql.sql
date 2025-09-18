
-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Сен 17 2025 г., 18:58
-- Версия сервера: 10.4.32-MariaDB
-- Версия PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- База данных: task_tracker
--

-- --------------------------------------------------------

--
-- Структура таблицы companies
--

CREATE TABLE companies (
  id int(11) NOT NULL,
  name varchar(191) NOT NULL,
  owner_user_id int(11) NOT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы companies
--

INSERT INTO companies (id, name, owner_user_id, created_at) VALUES
(1, 'Admin\'s Company', 9, '2025-09-13 17:54:57'),
(3, 'Taza Entertainment', 10, '2025-09-14 05:52:23'),
(5, 'Porcnakan Pictures', 12, '2025-09-14 07:24:07'),
(6, 'Nazar\'s Company', 14, '2025-09-15 00:09:41'),
(9, 'Inch Company', 17, '2025-09-15 05:10:51'),
(11, 'Edgar\'s Company', 18, '2025-09-16 07:01:21');

-- --------------------------------------------------------

--
-- Структура таблицы company_users
--

CREATE TABLE company_users (
  id int(11) NOT NULL,
  company_id int(11) NOT NULL,
  user_id int(11) NOT NULL,
  role enum('ADMIN','MANAGER','DEV') NOT NULL DEFAULT 'DEV',
  created_at timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы company_users
--

INSERT INTO company_users (id, company_id, user_id, role, created_at) VALUES
(1, 1, 9, 'ADMIN', '2025-09-13 17:54:57'),
(3, 3, 10, 'ADMIN', '2025-09-14 05:52:23'),
(5, 5, 12, 'ADMIN', '2025-09-14 07:24:07'),
(6, 1, 11, 'DEV', '2025-09-14 22:06:05'),
(7, 1, 13, 'DEV', '2025-09-14 22:13:41'),
(8, 6, 14, 'ADMIN', '2025-09-15 00:09:41'),
(10, 1, 15, 'DEV', '2025-09-15 03:31:21'),
(11, 1, 16, 'MANAGER', '2025-09-15 04:07:00'),
(13, 9, 17, 'ADMIN', '2025-09-15 05:10:51'),
(15, 11, 18, 'ADMIN', '2025-09-16 07:01:21'),
(16, 11, 19, 'DEV', '2025-09-16 07:03:19');

-- --------------------------------------------------------

--
-- Структура таблицы invitations
--

CREATE TABLE invitations (
  id int(11) NOT NULL,
  company_id int(11) NOT NULL,
  email varchar(191) NOT NULL,
  token varchar(64) NOT NULL,
  invited_by int(11) NOT NULL,
  status enum('PENDING','ACCEPTED','EXPIRED') NOT NULL DEFAULT 'PENDING',
  created_at timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы invitations
--

INSERT INTO invitations (id, company_id, email, token, invited_by, status, created_at) VALUES
(1, 1, 'aranc@mail.ru', '5fa66e343e4eb54133bf0a2319a777c8', 9, 'ACCEPTED', '2025-09-14 06:29:46'),
(2, 5, 'taza@mail.ru', '477fca6d7bdbf2eb9b7621cff0cd0164', 12, 'PENDING', '2025-09-14 07:24:55'),
(3, 5, 'aranc@mail.ru', '7511c93de1170e2e5b9e26f52cc08998', 12, 'PENDING', '2025-09-14 07:25:10'),
(4, 1, 'test@mail.ru', '7661cf3ce5ddb11854b8854280c3a560', 9, 'PENDING', '2025-09-14 20:47:22'),
(5, 1, 'dev@mail.ru', '5dce063cf1664e86871b1a04419d87dc', 9, 'ACCEPTED', '2025-09-14 22:11:21'),
(6, 1, 'andrei@mail.ru', '271626d4c74106210bfc998a3609be39', 9, 'ACCEPTED', '2025-09-15 03:29:41'),
(7, 1, 'tatos@mail.ru', 'ecce9cfbbdff77df63893f4e82707ba9', 9, 'ACCEPTED', '2025-09-15 04:05:54'),
(8, 11, 'porc@mail.ru', '72ac4f6d7e99aae2b4beb712e628b9f7', 18, 'ACCEPTED', '2025-09-16 07:01:58'),
(9, 1, 'edo@mail.ru', 'f50d612425adeb4dbb91c15310fed9c4', 9, 'PENDING', '2025-09-17 15:17:46');

-- --------------------------------------------------------

--
-- Структура таблицы tasks
--


CREATE TABLE tasks (
  id int(11) NOT NULL,
  company_id int(11) DEFAULT NULL,
  title varchar(255) NOT NULL,
  description text DEFAULT NULL,
  status enum('new','in_progress','done') NOT NULL DEFAULT 'new',
  assigned_to_user_id int(11) NOT NULL,
  created_by_user_id int(11) NOT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  updated_at timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы tasks
--

INSERT INTO tasks (id, company_id, title, description, status, assigned_to_user_id, created_by_user_id, created_at, updated_at) VALUES
(1, NULL, 'asd', NULL, 'new', 9, 9, '2025-09-12 21:35:18', NULL),
(2, NULL, 'NEW', 'Inch uzem kgrem', 'done', 9, 9, '2025-09-12 21:36:00', '2025-09-14 06:15:24'),
(3, NULL, 'gsg', 'adsadas', 'new', 9, 9, '2025-09-12 21:57:17', NULL),
(4, NULL, 'sadadsasad', 'dsadad', 'new', 9, 9, '2025-09-14 05:43:47', NULL),
(5, NULL, 'barev dzez', 'alo', 'in_progress', 9, 9, '2025-09-14 05:44:06', '2025-09-15 04:05:20'),
(6, NULL, 'aranc company task', 'solo asldkjasldkjadkj', 'new', 10, 10, '2025-09-14 05:51:49', NULL),
(7, NULL, 'Task Taza entertainment', 'yani esi aveli meca', 'new', 10, 10, '2025-09-14 05:52:53', NULL),
(8, NULL, 'sadsdaad', 'asdsad', 'in_progress', 9, 9, '2025-09-14 06:02:59', '2025-09-17 06:12:49'),
(9, NULL, 'asdasa', 'asdsadas', 'done', 11, 11, '2025-09-14 06:58:04', '2025-09-16 10:41:45'),
(10, NULL, 'task enq sarqel', 'sadlamklmaf', 'new', 12, 12, '2025-09-14 07:20:10', NULL),
(11, NULL, 'sarqm enq', 'askdasldk', 'new', 12, 12, '2025-09-14 07:25:30', NULL),
(12, 5, 'фвфывыф', 'фввывфывыфв', 'new', 12, 12, '2025-09-14 07:29:03', NULL),
(13, 1, 'zadacha', 'daljflkajdkslj', 'new', 9, 9, '2025-09-14 20:47:33', NULL),
(14, NULL, 'do homework', 'jkahdjkahfjkahfkjhsfjkahakfhafjhafkjhsakjfh', 'new', 14, 14, '2025-09-15 00:09:06', NULL),
(15, 6, 'Stex arden company memberem', 'NXJKnZKJsjcjklJCLKjzlkx', 'new', 14, 14, '2025-09-15 00:10:17', NULL),
(16, 1, 'cgfhgfhgjhgjhgjh', 'hgfghfhgvgvghfhgfhg', 'new', 9, 9, '2025-09-15 02:09:31', NULL),
(17, 1, 'asdjksahdsjkadha', 'kjsahdkjdsahkdjhask', 'in_progress', 9, 9, '2025-09-15 03:30:07', '2025-09-15 23:51:14'),
(18, NULL, 'test', 'sadjhsajds', 'in_progress', 15, 15, '2025-09-15 03:30:57', '2025-09-15 03:31:07'),
(19, 1, 'adjsdsakjlksdajlksaj', 'dlkjdlskjlskadjld', 'new', 9, 9, '2025-09-15 04:05:28', NULL),
(20, NULL, 'asdskdljslkadjlk', 'jlkjlsjdlkdjalk', 'done', 16, 16, '2025-09-15 04:06:42', '2025-09-15 04:06:47'),
(21, 1, 'lurj task', 'slide', 'in_progress', 16, 16, '2025-09-15 05:06:36', '2025-09-15 05:07:12'),
(22, 9, 'new task', 'skdjlksajdsalkjd', 'new', 17, 17, '2025-09-15 17:40:08', NULL),
(23, 1, 'Sateni Task', 'skjdhslkjklsafas', 'done', 16, 16, '2025-09-15 23:52:01', '2025-09-15 23:52:09'),
(24, NULL, 'Aranc company', 'sjadnjksadjksajdl', 'done', 18, 18, '2025-09-16 07:01:03', '2025-09-16 07:01:10'),
(25, 11, 'arden menq company enq', 'sadmajdkljdsal;', 'in_progress', 19, 18, '2025-09-16 07:02:16', '2025-09-16 07:08:26'),
(26, 1, 'test задача для проперки таска тамтарамтарм', 'lorem ipsum dolor sit ametlorem ipsum dolor sit ametlorem ipsum dolor sit ametlorem ipsum dolor sit ametlorem ipsum dolor sit ametlorem ipsum dolor sit ametlorem ipsum dolor sit ametlorem ipsum dolor sit ametlorem ipsum dolor sit ametlorem ipsum dolor sit ametlorem ipsum dolor sit ametlorem ipsum dolor sit ametlorem ipsum dolor sit ametlorem ipsum dolor sit ametlorem ipsum dolor sit ametlorem ipsum dolor sit ametlorem ipsum dolor sit amet', 'new', 11, 9, '2025-09-16 09:02:27', '2025-09-16 10:38:44'),
(27, 1, 'Es Hamoi taskna', 'jsdlskadjlskjlkfajlkjlkjfalkjdsakljfkl;jlk', 'in_progress', 9, 9, '2025-09-16 10:30:11', '2025-09-16 10:30:27'),


(28, 1, 'drrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrr', 'drrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrdrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrdrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrdrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrdrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrdrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrdrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrdrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrdrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrdrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrdrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrdrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrr', 'new', 11, 11, '2025-09-16 10:39:38', NULL),
(29, 1, '1', '1', 'done', 15, 11, '2025-09-16 10:50:22', '2025-09-17 06:13:10'),
(30, 1, 'hhjkhjgj', 'gjh,hjk4', 'new', 9, 9, '2025-09-17 06:13:57', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы task_logs
--

CREATE TABLE task_logs (
  id int(11) NOT NULL,
  task_id int(11) NOT NULL,
  action varchar(32) NOT NULL,
  old_value text DEFAULT NULL,
  new_value text DEFAULT NULL,
  performed_by int(11) NOT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы task_logs
--


INSERT INTO task_logs (id, task_id, action, old_value, new_value, performed_by, created_at) VALUES
(1, 8, 'status_change', 'new', 'in_progress', 9, '2025-09-14 06:14:57'),
(2, 8, 'status_change', 'in_progress', 'done', 9, '2025-09-14 06:15:04'),
(3, 2, 'status_change', 'done', 'new', 9, '2025-09-14 06:15:18'),
(4, 2, 'status_change', 'new', 'done', 9, '2025-09-14 06:15:25'),
(5, 9, 'status_change', 'new', 'in_progress', 11, '2025-09-14 06:58:10'),
(6, 18, 'status_change', 'new', 'in_progress', 15, '2025-09-15 03:31:07'),
(7, 5, 'status_change', 'new', 'in_progress', 9, '2025-09-15 04:05:20'),
(8, 20, 'status_change', 'new', 'done', 16, '2025-09-15 04:06:47'),
(9, 21, 'status_change', 'new', 'in_progress', 16, '2025-09-15 05:07:12'),
(10, 17, 'status_change', 'new', 'in_progress', 9, '2025-09-15 23:51:14'),
(11, 23, 'status_change', 'new', 'done', 16, '2025-09-15 23:52:09'),
(12, 24, 'status_change', 'new', 'done', 18, '2025-09-16 07:01:10'),
(13, 25, 'status_change', 'new', 'in_progress', 18, '2025-09-16 07:04:26'),
(14, 25, 'assignee_change', '18', '19', 18, '2025-09-16 07:08:17'),
(15, 25, 'assignee_change', '19', '18', 18, '2025-09-16 07:08:21'),
(16, 25, 'assignee_change', '18', '19', 18, '2025-09-16 07:08:26'),
(17, 27, 'status_change', 'new', 'in_progress', 9, '2025-09-16 10:30:28'),
(18, 26, 'assignee_change', '9', '11', 9, '2025-09-16 10:38:45'),
(19, 9, 'status_change', 'in_progress', 'done', 11, '2025-09-16 10:41:45'),
(20, 8, 'status_change', 'done', 'in_progress', 9, '2025-09-17 06:12:49'),
(21, 29, 'status_change', 'new', 'done', 9, '2025-09-17 06:12:54'),
(22, 29, 'assignee_change', '11', '13', 9, '2025-09-17 06:13:00'),
(23, 29, 'assignee_change', '13', '15', 9, '2025-09-17 06:13:10');

-- --------------------------------------------------------

--
-- Структура таблицы users
--

CREATE TABLE users (
  id int(11) NOT NULL,
  email varchar(191) NOT NULL,
  password_hash varchar(255) NOT NULL,
  name varchar(191) NOT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы users
--


INSERT INTO users (id, email, password_hash, name, created_at) VALUES
(1, 'arak@mail.ru', '$2y$10$WyXipxjtWteHpg9HlhiQZOCH9uqd38W4pGju4QIE8cix9E8X1nYh6', 'Artak', '2025-09-07 22:53:32'),
(2, 'miko@mail.ru', '$2y$10$NLc9Z3615HHzYTFkzpod0uBzzB8rE8elE8AAghVAjRd2SVTjtCRq6', 'MIKO', '2025-09-09 19:36:47'),
(3, 'vardan@mail.ru', '$2y$10$3bduFa6Xca3Ko/YzmoC0tepQu/5vM9FbCWqiqIBwaH5tMa80BB0TG', 'Vardam', '2025-09-11 05:27:25'),
(4, 'vrdo@mail.ru', '$2y$10$VSOZUyItGNdD72ImhA0GpOxk2LAuTpT3HxKM0ad5TYMBQPbuwVBu.', 'vrdo', '2025-09-11 07:45:28'),
(5, 'bako@mail.ru', '$2y$10$KS.15casgfvZDuZ.z4unBektA8Nb7wO2MeLeq9jZvzzVro5uDpqBS', 'bako', '2025-09-11 07:46:44'),
(6, 'new@mail.ru', '$2y$10$rX20R6lG5DCMsvCf.keK9.DO3n1jecQNP.t5ngcD7wK6eWHoUzn8a', 'newuser', '2025-09-11 09:24:55'),
(7, 'meka@mail.ru', '$2y$10$czRcb0KUSbocyuF.NexDjevUwXm0HptZBcDM8wGMqxkkjcUT6X72W', 'meka', '2025-09-11 11:44:25'),
(8, 'edo@mail.ru', '$2y$10$3B4ECLqokF9wMFDg4q7tDuADF5qy5lhDTqdJnhaUXr0AvBO2urcvy', 'Edo', '2025-09-11 11:45:56'),
(9, 'admin@mail.ru', '$2y$10$inmqmDwCmtyu1PJVAMb6mO6trjWoKkcO.B6sCSWPeEmqFTVv8eXCy', 'admin', '2025-09-12 21:27:16'),
(10, 'taza@mail.ru', '$2y$10$6h1Wflw9JQii7krtOqLrQ.4A.RDEYixSsjqAaeVjXNFU2SOnkjAfa', 'Taza', '2025-09-14 05:51:17'),
(11, 'aranc@mail.ru', '$2y$10$kDQJgAWwPbJQgrqe3uSbGO4O9CZGPf2oP39bXnDS17FW022Qy9yO6', 'aranc', '2025-09-14 06:24:58'),
(12, 'porcnakan@mail.ru', '$2y$10$3Hvx1MlxOsSoI1s/i1GUb.F.xn.HaTi1t/q9aoPl5/H/anwe//Wp6', 'porcnakan', '2025-09-14 07:19:51'),
(13, 'dev@mail.ru', '$2y$10$ZdW1oFD7nIldA892F11JxeEaOTAZXVyHxZ9RbKAChQ2y/PjEbJrqG', 'dev', '2025-09-14 22:12:22'),
(14, 'nazar@gmail.com', '$2y$10$ecY0mGIQrK5tI3Ab/wTfJutD2o0ZpG3Y9zFZsHait/zcXZG2AbrLG', 'Nazar', '2025-09-15 00:08:08'),
(15, 'andrei@mail.ru', '$2y$10$GYD4CKxLoxGahiuVSCs6CO/cOpio2h3KuGVxa322GB.haHquymBhC', 'Анрей', '2025-09-15 03:28:36'),
(16, 'tatos@mail.ru', '$2y$10$2xDjFvqZNN.j7RmhQvPKiuKfkW6HVvpAAqtGALArpRXj.5SGmKGHe', 'Tatos', '2025-09-15 04:06:21'),
(17, 'ogt@mail.ru', '$2y$10$i4oBh0/wQXA8EzRRbh24kO3T2gtV7RLRt3jHt8iz0HE4ox4uNa.g6', 'ogtater', '2025-09-15 05:10:03'),
(18, 'edgar1@mail.ru', '$2y$10$QlvYN3ISL5q1PUFbOVMQyeQUkEE4KR2PnajCBHPZfFiY0hnIG.g6G', 'Edgar', '2025-09-16 07:00:20'),
(19, 'porc@mail.ru', '$2y$10$rZLUiI4DaAL0RNbB2GRmP.HvsstJ9GeGgmyLzGaDjCmXA6FwO5Mqi', 'porcanq', '2025-09-16 07:03:06');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы companies
--
ALTER TABLE companies
  ADD PRIMARY KEY (id),
  ADD KEY owner_user_id (owner_user_id);

--
-- Индексы таблицы company_users
--
ALTER TABLE company_users
  ADD PRIMARY KEY (id),
  ADD UNIQUE KEY uniq_user (user_id),
  ADD KEY company_id (company_id);

--
-- Индексы таблицы invitations
--
ALTER TABLE invitations
  ADD PRIMARY KEY (id),
  ADD UNIQUE KEY token (token),
  ADD KEY company_id (company_id),
  ADD KEY invited_by (invited_by);

--
-- Индексы таблицы tasks
--
ALTER TABLE tasks
  ADD PRIMARY KEY (id),
  ADD KEY created_by_user_id (created_by_user_id),
  ADD KEY idx_tasks_company (company_id),
  ADD KEY idx_tasks_status (status),
  ADD KEY idx_tasks_assignee (assigned_to_user_id);

--
-- Индексы таблицы task_logs
--
ALTER TABLE task_logs
  ADD PRIMARY KEY (id),
  ADD KEY task_id (task_id),
  ADD KEY performed_by (performed_by);

--
-- Индексы таблицы users
--
ALTER TABLE users
  ADD PRIMARY KEY (id),
  ADD UNIQUE KEY email (email);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы companies
--
ALTER TABLE companies
  MODIFY id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT для таблицы company_users
--
ALTER TABLE company_users
  MODIFY id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT для таблицы invitations
--
ALTER TABLE invitations
  MODIFY id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT для таблицы tasks
--
ALTER TABLE tasks
  MODIFY id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT для таблицы task_logs
--
ALTER TABLE task_logs
  MODIFY id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;


--
-- AUTO_INCREMENT для таблицы users
--
ALTER TABLE users
  MODIFY id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы companies
--
ALTER TABLE companies
  ADD CONSTRAINT companies_ibfk_1 FOREIGN KEY (owner_user_id) REFERENCES users (id) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы company_users
--
ALTER TABLE company_users
  ADD CONSTRAINT company_users_ibfk_1 FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE CASCADE,
  ADD CONSTRAINT company_users_ibfk_2 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы invitations
--
ALTER TABLE invitations
  ADD CONSTRAINT invitations_ibfk_1 FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE CASCADE,
  ADD CONSTRAINT invitations_ibfk_2 FOREIGN KEY (invited_by) REFERENCES users (id) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы tasks
--
ALTER TABLE tasks
  ADD CONSTRAINT tasks_ibfk_1 FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE SET NULL,
  ADD CONSTRAINT tasks_ibfk_2 FOREIGN KEY (assigned_to_user_id) REFERENCES users (id) ON DELETE CASCADE,
  ADD CONSTRAINT tasks_ibfk_3 FOREIGN KEY (created_by_user_id) REFERENCES users (id) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы task_logs
--
ALTER TABLE task_logs
  ADD CONSTRAINT task_logs_ibfk_1 FOREIGN KEY (task_id) REFERENCES tasks (id) ON DELETE CASCADE,
  ADD CONSTRAINT task_logs_ibfk_2 FOREIGN KEY (performed_by) REFERENCES users (id) ON DELETE CASCADE;
COMMIT;
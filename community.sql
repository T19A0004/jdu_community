-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 24, 2023 at 11:04 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `community`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment_text` text NOT NULL,
  `created_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `post_id`, `user_id`, `comment_text`, `created_time`) VALUES
(13, 50, 17, '私も行きたい', '2023-07-18 12:24:53'),
(14, 50, 17, 'そのためには日本語の勉強は大事ですね。', '2023-07-18 12:32:50'),
(19, 49, 14, '私もアニメが好きです！', '2023-07-18 20:05:55'),
(24, 52, 15, 'そうですね。\r\n<br>一緒に勉強しましょう。', '2023-07-18 20:50:09'),
(25, 49, 15, '一番好きなアニメはナルト!😎', '2023-07-19 06:33:48'),
(27, 48, 15, 'ナルト!😡', '2023-07-19 06:46:00'),
(28, 48, 16, '鬼滅の刃はもっと最高だ！', '2023-07-22 09:08:58'),
(30, 47, 17, '私はITをよく学んで、IT専門家になりたい', '2023-07-23 17:42:10'),
(31, 47, 19, '本当にITはよく成長しているんですね😊', '2023-07-23 17:57:37');

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `id` int(11) NOT NULL,
  `comment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `likes`
--

INSERT INTO `likes` (`id`, `comment_id`, `user_id`, `created_time`) VALUES
(11, 13, 17, '2023-07-18 20:03:49'),
(20, 19, 15, '2023-07-19 06:33:16'),
(24, 27, 14, '2023-07-19 06:57:01'),
(25, 25, 14, '2023-07-19 13:50:14'),
(29, 13, 14, '2023-07-21 21:34:36'),
(32, 27, 16, '2023-07-22 09:11:02'),
(33, 28, 14, '2023-07-23 15:35:12'),
(42, 31, 15, '2023-07-23 18:01:03'),
(46, 31, 14, '2023-07-23 18:02:25'),
(48, 28, 15, '2023-07-24 06:03:57'),
(49, 24, 14, '2023-07-24 06:06:14'),
(50, 30, 19, '2023-07-24 06:15:32'),
(51, 14, 15, '2023-07-24 06:44:02'),
(52, 13, 15, '2023-07-24 06:44:06');

-- --------------------------------------------------------

--
-- Table structure for table `topics`
--

CREATE TABLE `topics` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_image` varchar(255) DEFAULT NULL,
  `post_title` varchar(255) NOT NULL,
  `main_text` text NOT NULL,
  `main_theme` varchar(255) NOT NULL,
  `created_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `topics`
--

INSERT INTO `topics` (`id`, `user_id`, `post_image`, `post_title`, `main_text`, `main_theme`, `created_time`) VALUES
(47, 21, 'post_img/64b3f869a543f_websites.jpg', 'IT 面白くなっている!!!', 'ITの世界はますます面白さが増しています！驚くべき進化と革新が日々起こり、私たちの生活を変えています。人工知能、ブロックチェーン、クラウドコンピューティングなどの先端技術が次々と登場し、未来を予想するのも楽しみです。また、ゲーム、仮想現実、拡張現実など、エンターテインメント分野でもITが大きな役割を果たしています。これからのITの進化に目が離せません！楽しみながら新たな可能性を追求していく時代に、私たちはワクワクと胸躍ります。', 'IT', '2023-07-16 19:02:17'),
(48, 15, 'post_img/64b3f910b6b4b_naruto.jpg', 'ナルトが最高だ!', 'ナルトは最高だ！魅力的なキャラクター、感動的なストーリー、熱い友情、そして壮大なバトルが魅力です。主人公の成長と努力に心打たれます。忍者の世界と個性的な忍術は引き込まれるばかり。愛と友情のメッセージは胸に響きます。長い物語でも飽きることなく、最後まで夢中になれる作品です。ナルトは永遠の名作であり、多くの人々に感動と勇気を与えています。忍者の旅路に共感し、熱い思いを共有しましょう！', 'アニメ', '2023-07-16 19:03:52'),
(49, 16, 'post_img/64b3fa314fc09_shark.png', '日本のアニメが好き🥰', '日本のアニメが好きです。その多様なジャンル、美しいアートワーク、そして感情豊かなストーリーテリングに引き込まれます。キャラクターたちの成長や友情、恋愛、冒険などのテーマは心に響きます。アニメは年齢や国境を越えて、世界中の人々に愛されています。特に、日本のアニメは文化や伝統を反映しており、独自の魅力があります。また、音楽もアニメの魅力を高めています。素晴らしいオープニングやエンディングテーマは、作品をより一層楽しませてくれます。日本のアニメは私にとって、心を豊かにし、想像力をかきたててくれる大切な存在です。これからも、新たな作品を楽しみにしています。', 'アニメ', '2023-07-17 19:09:53'),
(50, 19, 'post_img/64b3fab85096a_illustration.png', '日本へ行きたい!', '日本へ行きたい！美しい自然、豊かな文化、伝統的な風景、そして温かな人々に魅了されます。桜の花見や祭りの賑わい、古都の風情を感じたいです。和食や抹茶などの食文化も楽しみです。近代的なテクノロジーやポップカルチャーにも興味があります。日本の旅で新しい発見や感動を味わいたい！心躍る思いで旅立ちを夢見ています。日本への愛と憧れを胸に、素晴らしい経験を求めて行く日が待ち遠しいです！', '日本文化', '2023-07-14 19:12:08'),
(52, 17, 'post_img/64b42590a4d02_photo_2023-07-12_12-24-16 (2).jpg', '勉強が楽しい!', '勉強が楽しい！新しい知識を得る喜びと成長を感じることができます。好奇心をくすぐり、自分の興味に追求する時間は至福のひとときです。知識は未来への財産であり、学び続けることで自己を高められます。挑戦的な問題を解決し、知識を実践に役立てる喜びは何物にも代え難いです。どんな分野でも学びがあり、成果を実感できるのが勉強の楽しさ。進んで学び、自己成長の喜びを味わいましょう！', '勉強', '2023-07-16 22:14:56');

-- --------------------------------------------------------

--
-- Table structure for table `user_form`
--

CREATE TABLE `user_form` (
  `id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `surname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `image` varchar(100) NOT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_form`
--

INSERT INTO `user_form` (`id`, `name`, `surname`, `email`, `password`, `image`, `gender`, `role`) VALUES
(14, 'Admin', 'Admin', 'admin@gmail.com', '$2y$10$FOHiVZ9Yzz7XOKzEYessJOBfm2WINMfKcjWA586Y4D6eHkgLS4O.K', '64b2f68a21e0c_glitch.jpg', 'male', 'admin'),
(15, 'Komiljon', 'Riksiyev', 'komilriksiyev@gmail.com', '$2y$10$kk/cm25UnnNqRp6gBZl3t.erspbQNl3xxMU/CTlVu7nRMjMUbn.vy', '64be028fcedd4_man (2).jpg', 'male', 'user'),
(16, 'Bunyod', 'Jurayev', 'rasuli@gmail.com', '$2y$10$hOdiVh4TuDawirZ/vdM2KOhKza1qFSFJysKyPLnNtw/DhRkwmp0be', '64be02fe7bd3f_bunyod.jpg', 'male', 'user'),
(17, 'Islombek', 'Kamolov', 'islombek@gmail.com', '$2y$10$B5fbHehS1Y7OE8wOY5eAfeFx.sF4JBRTfWocrcmI/IJPeveYpYiwO', '64bd65fcbc39e_islombek (2).jpg', 'male', 'user'),
(19, 'Malika', 'Kenjayeva', 'malika@gmail.com', '$2y$10$jNUvK06h4Jk.CgKP889AIO8BfN.WFMe6taOlKHvLE396PMLjZ1coK', '64bd66f1daa49_malika (2).jpg', 'female', 'user'),
(21, 'Jonibek', 'Abduvaitov', 'jonibek@gmail.com', '$2y$10$UH6ek1SX5BwA1mWCoBMnDenElJu8pykTTCrtpC8xp9mFNNktfVHum', '64bd65a6d8119_jonibek (2).jpg', 'male', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `comment_id` (`comment_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `topics`
--
ALTER TABLE `topics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user_form`
--
ALTER TABLE `user_form`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `topics`
--
ALTER TABLE `topics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `user_form`
--
ALTER TABLE `user_form`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `topics` (`id`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user_form` (`id`);

--
-- Constraints for table `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`comment_id`) REFERENCES `comments` (`id`),
  ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user_form` (`id`);

--
-- Constraints for table `topics`
--
ALTER TABLE `topics`
  ADD CONSTRAINT `topics_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_form` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

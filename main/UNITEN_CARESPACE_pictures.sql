--
-- Database: `pictures`
--
CREATE DATABASE IF NOT EXISTS `pictures` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `pictures`;

-- --------------------------------------------------------

--
-- Table structure for table `pictures`
--

DROP TABLE IF EXISTS `pictures`;
CREATE TABLE IF NOT EXISTS `pictures` (
  `id` int NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) NOT NULL,
  `filepath` varchar(255) NOT NULL,
  `uploaded_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pictures`
--

INSERT INTO `pictures` (`id`, `filename`, `filepath`, `uploaded_at`) VALUES
(1, 'counsellors.jpeg', 'uploads/counsellors.jpeg', '2025-05-04 10:06:15'),
(2, 'logo_official.png', 'uploads/logo_official.png', '2025-05-04 10:49:05'),
(3, 'articles.jpg', 'uploads/articles.jpg', '2025-05-04 10:57:41'),
(4, 'dass.png', 'uploads/dass.png', '2025-05-04 11:01:36'),
(15, 'feedback.jpg', 'uploads/feedback.jpg', '2025-05-18 07:23:04');

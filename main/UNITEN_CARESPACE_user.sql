--
-- Database: `user`
--
CREATE DATABASE IF NOT EXISTS `user` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `user`;

-- --------------------------------------------------------

--
-- Table structure for table `dass_results`
--

DROP TABLE IF EXISTS `dass_results`;
CREATE TABLE IF NOT EXISTS `dass_results` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) DEFAULT NULL,
  `stress_level` varchar(20) DEFAULT NULL,
  `anxiety_level` varchar(20) DEFAULT NULL,
  `depression_level` varchar(20) DEFAULT NULL,
  `taken_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `dass_results`
--

INSERT INTO `dass_results` (`id`, `username`, `stress_level`, `anxiety_level`, `depression_level`, `taken_at`) VALUES
(1, 'IS812566', 'Normal', 'Mild', 'Mild', '2025-06-11 17:44:06'),
(2, 'test', 'Mild', 'Moderate', 'Moderate', '2025-06-12 16:37:54'),
(3, 'IS01082117', 'Mild', 'Mild', 'Mild', '2025-06-12 18:03:46'),
(4, 'CS01928382', 'Mild', 'Mild', 'Moderate', '2025-06-14 23:07:01'),
(5, 'IS01082525', 'Mild', 'Mild', 'Moderate', '2025-06-15 05:30:02');

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

DROP TABLE IF EXISTS `login`;
CREATE TABLE IF NOT EXISTS `login` (
  `username` varchar(50) NOT NULL,
  `password` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  PRIMARY KEY (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `login`
--

INSERT INTO `login` (`username`, `password`, `email`, `last_name`, `first_name`) VALUES
('IS01082525', 'Passw0rd!g', 'IS01082525@student.uniten.edu.my', 'Tan', 'Ahmad'),
('CS01928374', 'Uni10pass!', 'CS01928374@student.uniten.edu.my', 'Koh', 'Wei'),
('VM01029384', 'Uni10pass!', 'VM01029384@student.uniten.edu.my', 'Ramli', 'Shahrul'),
('IS01082526', 'Uni10pass!', 'IS01082526@student.uniten.edu.my', 'Ng', 'Jing'),
('CS01928375', 'Uni10pass!', 'CS01928375@student.uniten.edu.my', 'Ravi', 'Kumar'),
('VM01029385', 'Uni10pass!', 'VM01029385@student.uniten.edu.my', 'Ahmad', 'Zaid'),
('IS01082527', 'Uni10pass!', 'IS01082527@student.uniten.edu.my', 'Ali', 'Faiz'),
('CS01928376', 'Uni10pass!', 'CS01928376@student.uniten.edu.my', 'Lim', 'Han'),
('VM01029386', 'Uni10pass!', 'VM01029386@student.uniten.edu.my', 'Kumari', 'Geetha'),
('IS01082528', 'Uni10pass!', 'IS01082528@student.uniten.edu.my', 'Lee', 'Xian'),
('CS01928377', 'Uni10pass!', 'CS01928377@student.uniten.edu.my', 'Kumar', 'Arvind'),
('VM01029387', 'Uni10pass!', 'VM01029387@student.uniten.edu.my', 'Muthu', 'Raj'),
('IS01082529', 'Uni10pass!', 'IS01082529@student.uniten.edu.my', 'Wong', 'Fong'),
('CS01928378', 'Uni10pass!', 'CS01928378@student.uniten.edu.my', 'Tan', 'Rafi'),
('VM01029388', 'Uni10pass!', 'VM01029388@student.uniten.edu.my', 'Amin', 'Shah'),
('IS01082530', 'Uni10pass!', 'IS01082530@student.uniten.edu.my', 'Zain', 'Akmal'),
('CS01928379', 'Uni10pass!', 'CS01928379@student.uniten.edu.my', 'Junaid', 'Mohamed'),
('VM01029389', 'Uni10pass!', 'VM01029389@student.uniten.edu.my', 'Lai', 'Zhi'),
('IS01082531', 'Uni10pass!', 'IS01082531@student.uniten.edu.my', 'Ariff', 'Nashit'),
('CS01928380', 'Uni10pass!', 'CS01928380@student.uniten.edu.my', 'Koh', 'Jun'),
('VM01029390', 'Uni10pass!', 'VM01029390@student.uniten.edu.my', 'Ravindra', 'Babu'),
('IS01082532', 'Uni10pass!', 'IS01082532@student.uniten.edu.my', 'Siti', 'Mastura'),
('CS01928381', 'Uni10pass!', 'CS01928381@student.uniten.edu.my', 'Anwar', 'Nizar'),
('VM01029391', 'Uni10pass!', 'VM01029391@student.uniten.edu.my', 'Muthusamy', 'Subramaniam'),
('IS01082533', 'Uni10pass!', 'IS01082533@student.uniten.edu.my', 'Fatimah', 'Ali'),
('CS01928382', 'Uni10pass!', 'CS01928382@student.uniten.edu.my', 'Kamaruddin', 'Rizwan'),
('VM01029392', 'Uni10pass!', 'VM01029392@student.uniten.edu.my', 'Zainuddin', 'Nadia'),
('IS01082534', 'Uni10pass!', 'IS01082534@student.uniten.edu.my', 'Vijay', 'Singh'),
('CS01928383', 'Uni10pass!', 'CS01928383@student.uniten.edu.my', 'Rashid', 'Irfan'),
('VM01029393', 'Uni10pass!', 'VM01029393@student.uniten.edu.my', 'Suresh', 'Ramesh'),
('IS01082535', 'Uni10pass!', 'IS01082535@student.uniten.edu.my', 'Mahalakshmi', 'Sundari'),
('CS01928384', 'Uni10pass!', 'CS01928384@student.uniten.edu.my', 'Iqbal', 'Zakir'),
('VM01029394', 'Uni10pass!', 'VM01029394@student.uniten.edu.my', 'Vishal', 'Raj'),
('IS01082536', 'Uni10pass!', 'IS01082536@student.uniten.edu.my', 'Nur', 'Afiqah'),
('CS01928385', 'Uni10pass!', 'CS01928385@student.uniten.edu.my', 'Vijayan', 'Subhashini'),
('VM01029395', 'Uni10pass!', 'VM01029395@student.uniten.edu.my', 'Venu', 'Sankar'),
('IS01082537', 'Uni10pass!', 'IS01082537@student.uniten.edu.my', 'Yasir', 'Shabir'),
('CS01928386', 'Uni10pass!', 'CS01928386@student.uniten.edu.my', 'Shazia', 'Nashita'),
('VM01029396', 'Uni10pass!', 'VM01029396@student.uniten.edu.my', 'Yash', 'Kiran'),
('IS01082538', 'Uni10pass!', 'IS01082538@student.uniten.edu.my', 'Raja', 'Ishwar'),
('CS01928387', 'Uni10pass!', 'CS01928387@student.uniten.edu.my', 'Krishnan', 'Rajesh'),
('VM01029397', 'Uni10pass!', 'VM01029397@student.uniten.edu.my', 'Salma', 'Zainab'),
('IS01082539', 'Uni10pass!', 'IS01082539@student.uniten.edu.my', 'Bala', 'Sivaraj'),
('CS01928388', 'Uni10pass!', 'CS01928388@student.uniten.edu.my', 'Raj', 'Kumar'),
('VM01029398', 'Uni10pass!', 'VM01029398@student.uniten.edu.my', 'Amit', 'Kapoor'),
('IS01082540', 'Uni10pass!', 'IS01082540@student.uniten.edu.my', 'Shankar', 'Ravi'),
('CS01928389', 'Uni10pass!', 'CS01928389@student.uniten.edu.my', 'Rosli', 'Amin'),
('VM01029399', 'Uni10pass!', 'VM01029399@student.uniten.edu.my', 'Arun', 'Sundaram'),
('IS01082117', 'Muhammadnabil02!', 'muhammadnabil@gmail.com', 'Bin Muhamad', 'Muhammad Nabil'),
('IS0102832', 'Uni10pass!', 'IS01082832@student.uniten.edu.my', 'Essam', 'Yasmine');

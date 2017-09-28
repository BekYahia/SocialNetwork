-- phpMyAdmin SQL Dump
-- version 4.5.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 28, 2017 at 02:57 AM
-- Server version: 10.1.13-MariaDB
-- PHP Version: 5.5.37

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `SocialNetwork`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `CommentID` int(11) NOT NULL,
  `CommentBody` text NOT NULL,
  `User_ID` int(11) NOT NULL,
  `Post_ID` int(11) NOT NULL,
  `CommentTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Topics` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`CommentID`, `CommentBody`, `User_ID`, `Post_ID`, `CommentTime`, `Topics`) VALUES
(5, '<a href=''profile.php?username=ibra''>ibra</a> you made me speekless<br>#respect', 2, 26, '2017-09-14 20:13:31', ''),
(13, '<a href=''profile.php?username=verified''>verified</a> this words are start of glory ', 2, 1, '2017-09-20 16:29:41', ''),
(14, 'asli kyaaf ', 1, 26, '2017-09-23 22:53:35', ''),
(32, 'ava ya heart ', 1, 8, '2017-09-24 09:13:46', ''),
(34, 'sdsa ', 1, 1, '2017-09-24 20:59:07', ''),
(51, 'bla bla ', 1, 67, '2017-09-24 21:09:25', ''),
(99, '#respect', 2, 207, '2017-09-28 00:18:39', '');

-- --------------------------------------------------------

--
-- Table structure for table `followers`
--

CREATE TABLE `followers` (
  `FollowingID` int(11) NOT NULL,
  `FollowerID` int(11) NOT NULL,
  `User_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `followers`
--

INSERT INTO `followers` (`FollowingID`, `FollowerID`, `User_ID`) VALUES
(11, 3, 1),
(19, 2, 1),
(22, 2, 3),
(44, 1, 3),
(45, 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `login_tokens`
--

CREATE TABLE `login_tokens` (
  `TokenID` int(11) NOT NULL,
  `Token` varchar(255) NOT NULL,
  `User_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `MessageID` int(11) NOT NULL,
  `MessageBody` text NOT NULL,
  `Time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Sender` int(11) NOT NULL,
  `Receiver` int(11) NOT NULL,
  `Status` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`MessageID`, `MessageBody`, `Time`, `Sender`, `Receiver`, `Status`) VALUES
(1, 'hi bakri, from ibra', '2017-09-16 12:20:00', 2, 1, 0),
(5, 'wts up, man', '2017-09-16 12:24:09', 1, 2, 0),
(6, 'fine fine\r\nwt the hell alveg?', '2017-09-16 12:34:07', 2, 1, 0),
(7, 'ohhhhhhhhhhh homie, you too', '2017-09-16 12:34:56', 1, 2, 0),
(13, 'the nl2br works well,', '2017-09-16 12:53:17', 1, 2, 0);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `NotificationID` int(11) NOT NULL,
  `Sender` int(11) NOT NULL,
  `Receiver` int(11) NOT NULL,
  `Type` tinyint(4) NOT NULL,
  `Extra` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`NotificationID`, `Sender`, `Receiver`, `Type`, `Extra`) VALUES
(4, 2, 1, 1, 'hello @abubaker'),
(5, 1, 2, 1, '@ibra my buddy'),
(261, 1, 1, 2, ''),
(262, 2, 2, 2, '');

-- --------------------------------------------------------

--
-- Table structure for table `password_tokens`
--

CREATE TABLE `password_tokens` (
  `TokenID` int(11) NOT NULL,
  `Token` varchar(255) NOT NULL,
  `User_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `PostID` int(11) NOT NULL,
  `PostBody` text NOT NULL,
  `PostTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `User_ID` int(11) NOT NULL,
  `Likes` int(11) NOT NULL,
  `Topics` varchar(360) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`PostID`, `PostBody`, `PostTime`, `User_ID`, `Likes`, `Topics`) VALUES
(1, 'Hello Wolrd!', '2017-09-13 11:25:17', 3, 3, ''),
(8, 'hello form ava world', '2017-09-13 16:51:15', 2, 1, ''),
(26, '<a href=''profile.php?username=verified''>@verified</a> is a <b>shit</b>', '2017-09-14 20:01:44', 2, 1, ''),
(67, '<a href=''profile.php?username=ibra''>@ibra</a> my buddy ', '2017-09-15 18:34:39', 1, 4, ''),
(207, 'if other people are putting in 40 hour work weeks, and you''re putting in 100 hour work weeks, you''ll achieve in four months when it takes them year to achieve.\n@ellonMusk ', '2017-09-28 00:16:57', 1, 23, ''),
(209, 'never sacrifice your quality and beautiful code for speed\n@bradHussey ', '2017-09-28 00:49:28', 2, 15, '');

-- --------------------------------------------------------

--
-- Table structure for table `post_likes`
--

CREATE TABLE `post_likes` (
  `Post_ID` int(11) NOT NULL,
  `User_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `post_likes`
--

INSERT INTO `post_likes` (`Post_ID`, `User_ID`) VALUES
(1, 3),
(1, 2),
(26, 1),
(8, 1),
(1, 1),
(207, 1),
(207, 2),
(209, 2);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `UserID` int(11) NOT NULL,
  `Username` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Verified` tinyint(1) DEFAULT '0',
  `Bio` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`UserID`, `Username`, `Password`, `Email`, `Verified`, `Bio`) VALUES
(1, 'abubaker', '$2y$10$HcQFo91DAvQiwgYX7jLfROQaEJcnR1rkM.1StAqzII.9giavDcgZe', 'a@abubaker.com', 1, 'just a kid from kenana, love creating things, strong believer in importance of digital skills & technology for everybody and wanna make the world a better place.'),
(2, 'ibra', '$2y$10$8oRe0eh4kt2PlLDJXGNmdelEX9oCLPYGGdZtcDcZ1WzVLGxhL/JiC', 'i@ibrahim.com', 0, 'Who cares'),
(3, 'Verified', '$2y$10$COqNU6iKs8wyZmMZ14YxCeab5bhWmqvS3.We8nEoBJMc6weHKGyU2', 'v@verified.com', 1, ''),
(4, 'francis', '$2y$10$jRls6gqdFlmUqx7./TPzR.aDPL12lpThxMTVC7GsNS07k90AaC5Ou', 'f@fransice.com', 0, '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`CommentID`),
  ADD KEY `comment_user` (`User_ID`),
  ADD KEY `comment_post` (`Post_ID`);

--
-- Indexes for table `followers`
--
ALTER TABLE `followers`
  ADD PRIMARY KEY (`FollowingID`),
  ADD KEY `followed user` (`User_ID`);

--
-- Indexes for table `login_tokens`
--
ALTER TABLE `login_tokens`
  ADD PRIMARY KEY (`TokenID`),
  ADD KEY `user-tokens` (`User_ID`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`MessageID`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`NotificationID`);

--
-- Indexes for table `password_tokens`
--
ALTER TABLE `password_tokens`
  ADD PRIMARY KEY (`TokenID`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`PostID`),
  ADD KEY `user_post` (`User_ID`);

--
-- Indexes for table `post_likes`
--
ALTER TABLE `post_likes`
  ADD KEY `postlikes_user` (`User_ID`),
  ADD KEY `postlikes_post` (`Post_ID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UserID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `CommentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;
--
-- AUTO_INCREMENT for table `followers`
--
ALTER TABLE `followers`
  MODIFY `FollowingID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;
--
-- AUTO_INCREMENT for table `login_tokens`
--
ALTER TABLE `login_tokens`
  MODIFY `TokenID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `MessageID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;
--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `NotificationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=263;
--
-- AUTO_INCREMENT for table `password_tokens`
--
ALTER TABLE `password_tokens`
  MODIFY `TokenID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `PostID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=210;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comment_post` FOREIGN KEY (`Post_ID`) REFERENCES `posts` (`PostID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `comment_user` FOREIGN KEY (`User_ID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `followers`
--
ALTER TABLE `followers`
  ADD CONSTRAINT `followed user` FOREIGN KEY (`User_ID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `login_tokens`
--
ALTER TABLE `login_tokens`
  ADD CONSTRAINT `user-tokens` FOREIGN KEY (`User_ID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `user_post` FOREIGN KEY (`User_ID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `post_likes`
--
ALTER TABLE `post_likes`
  ADD CONSTRAINT `postlikes_post` FOREIGN KEY (`Post_ID`) REFERENCES `posts` (`PostID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `postlikes_user` FOREIGN KEY (`User_ID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

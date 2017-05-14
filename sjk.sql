-- phpMyAdmin SQL Dump
-- version 3.5.0
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2017 �?05 �?07 �?13:15
-- 服务器版本: 5.5.53
-- PHP 版本: 5.6.27

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `xj`
--

-- --------------------------------------------------------

--
-- 表的结构 `administrators`
--

CREATE TABLE IF NOT EXISTS `administrators` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `body_email` varchar(32) NOT NULL,
  `body_password` varchar(32) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `administrators`
--

INSERT INTO `administrators` (`id`, `body_email`, `body_password`, `created_at`, `updated_at`) VALUES
(1, '123456@qq.com', 'e10adc3949ba59abbe56e057f20f883e', '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- 表的结构 `captchas`
--

CREATE TABLE IF NOT EXISTS `captchas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `body_mobile` varchar(11) NOT NULL,
  `body_code` varchar(6) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `day_executes`
--

CREATE TABLE IF NOT EXISTS `day_executes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `start` varchar(20) COLLATE utf8_unicode_ci NOT NULL COMMENT '开始执行时间',
  `end` varchar(20) COLLATE utf8_unicode_ci NOT NULL COMMENT '结束执行时间',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `day_execute_logs`
--

CREATE TABLE IF NOT EXISTS `day_execute_logs` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` varchar(32) DEFAULT NULL,
  `amount` decimal(6,2) DEFAULT NULL,
  `remark` text,
  `time` int(10) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `feedbacks`
--

CREATE TABLE IF NOT EXISTS `feedbacks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `body_content` text NOT NULL,
  `body_tool` varchar(20) NOT NULL,
  `body_number` varchar(32) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `grades`
--

CREATE TABLE IF NOT EXISTS `grades` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_wechat` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `old_grade` int(1) DEFAULT NULL,
  `grade` int(1) NOT NULL,
  `status` int(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- 表的结构 `lines`
--

CREATE TABLE IF NOT EXISTS `lines` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_object` int(11) NOT NULL,
  `body_period` int(11) NOT NULL,
  `body_open` decimal(10,5) NOT NULL,
  `body_close` decimal(10,5) NOT NULL,
  `body_high` decimal(10,5) NOT NULL,
  `body_low` decimal(10,5) NOT NULL,
  `body_volume` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `migrations`
--

CREATE TABLE IF NOT EXISTS `migrations` (
  `migration` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `month_executes`
--

CREATE TABLE IF NOT EXISTS `month_executes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL COMMENT '执行者',
  `start` varchar(20) COLLATE utf8_unicode_ci NOT NULL COMMENT '开始执行时间',
  `end` varchar(20) COLLATE utf8_unicode_ci NOT NULL COMMENT '结束执行时间',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `month_execute_logs`
--

CREATE TABLE IF NOT EXISTS `month_execute_logs` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` varchar(32) DEFAULT NULL,
  `amount` decimal(6,2) DEFAULT NULL,
  `remark` text,
  `time` int(10) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `objects`
--

CREATE TABLE IF NOT EXISTS `objects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `body_profit` decimal(8,2) NOT NULL DEFAULT '0.80',
  `body_rank` int(11) NOT NULL DEFAULT '0',
  `body_name` varchar(255) NOT NULL,
  `body_status` int(1) NOT NULL DEFAULT '1',
  `body_name_english` varchar(50) NOT NULL,
  `body_tag` varchar(255) NOT NULL,
  `body_tag_forex` varchar(20) NOT NULL,
  `body_price` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `body_price_previous` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `body_price_min` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `body_price_max` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `body_price_interval` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `body_price_decimal` int(1) NOT NULL DEFAULT '5',
  `body_price_repeat` int(11) NOT NULL DEFAULT '0',
  `is_disabled` int(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- 转存表中的数据 `objects`
--

INSERT INTO `objects` (`id`, `body_profit`, `body_rank`, `body_name`, `body_status`, `body_name_english`, `body_tag`, `body_tag_forex`, `body_price`, `body_price_previous`, `body_price_min`, `body_price_max`, `body_price_interval`, `body_price_decimal`, `body_price_repeat`, `is_disabled`, `created_at`, `updated_at`) VALUES
(1, '0.80', 3, '澳元兑美元', 1, 'AUDUSD', 'fx_saudusd', 'AUDUSD', '0.74141', '0.74247', '0.74141', '0.74141', '0.00000', 5, 0, 0, '2016-04-17 16:00:00', '2017-05-06 15:25:11'),
(2, '0.80', 2, '欧元兑美元', 1, 'EURUSD', 'fx_seurusd', 'EURUSD', '1.09936', '1.09946', '1.09936', '1.09936', '0.00000', 5, 0, 0, '2016-04-17 16:00:00', '2017-05-06 15:25:11'),
(3, '0.80', 2, '英镑兑美元', 1, 'GBPUSD', 'fx_sgbpusd', 'GBPUSD', '1.29754', '1.29795', '1.29754', '1.29754', '0.00000', 5, 0, 0, '2016-04-17 16:00:00', '2017-05-06 15:25:11'),
(4, '0.80', 5, '英镑兑日元', 1, 'GBPJPY', 'fx_sgbpjpy', 'GBPJPY', '146.01800', '146.10600', '146.01800', '146.01800', '0.00000', 3, 0, 0, '2016-04-17 16:00:00', '2017-05-06 15:25:11'),
(5, '0.80', 5, '美元兑日元', 1, 'USDJPY', 'fx_susdjpy', 'USDJPY', '112.58000', '112.55700', '112.58000', '112.58000', '0.00000', 3, 0, 0, '2016-04-17 16:00:00', '2017-05-06 15:25:11'),
(6, '0.80', 5, '欧元兑日元', 1, 'EURJPY', 'fx_seurjpy', 'EURJPY', '123.75500', '123.76500', '123.75500', '123.75500', '0.00000', 3, 0, 0, '2016-04-17 16:00:00', '2017-05-06 15:25:11'),
(7, '0.80', 1, '纽交所黄金', 1, 'XAUUSD', 'hf_GC', 'XAUUSD', '1227.70000', '1228.78000', '1227.70000', '1227.70000', '0.00000', 2, 0, 0, '2016-05-16 16:00:00', '2017-05-06 15:25:11'),
(8, '0.80', 1, '纽交所白银', 1, 'XAGUSD', 'hf_SI', 'XAGUSD', '16.13500', '16.31300', '16.13500', '16.13500', '0.00000', 3, 0, 0, '2016-05-16 16:00:00', '2017-05-06 15:25:11'),
(9, '0.80', 0, '比特币', 1, 'BTCCNY', 'BTCCNY', 'BTCCNY', '1108.70000', '1108.41000', '1108.69578', '1108.69578', '0.00000', 2, 0, 0, '2016-04-17 16:00:00', '2017-05-06 15:40:21');

-- --------------------------------------------------------

--
-- 表的结构 `orders`
--

CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` varchar(15) NOT NULL,
  `id_object` int(11) NOT NULL,
  `body_price_buying` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `body_price_striked` decimal(10,5) DEFAULT '0.00000',
  `body_stake` decimal(8,2) NOT NULL DEFAULT '0.00',
  `body_bonus` decimal(8,2) NOT NULL DEFAULT '0.00',
  `body_direction` int(1) NOT NULL DEFAULT '0',
  `body_time` int(11) NOT NULL DEFAULT '60',
  `body_is_win` int(1) DEFAULT NULL,
  `body_is_draw` int(1) DEFAULT NULL,
  `body_is_controlled` int(1) NOT NULL DEFAULT '0',
  `times` varchar(25) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `striked_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `pay_requests`
--

CREATE TABLE IF NOT EXISTS `pay_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` varchar(12) NOT NULL,
  `body_stake` int(5) NOT NULL,
  `body_gateway` varchar(32) NOT NULL,
  `body_transfer_number` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `processed_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `prices`
--

CREATE TABLE IF NOT EXISTS `prices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_object` int(11) NOT NULL,
  `body_price` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `body_price_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `body_rank` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `records`
--

CREATE TABLE IF NOT EXISTS `records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` varchar(15) NOT NULL DEFAULT '0',
  `id_payRequest` int(11) NOT NULL DEFAULT '0',
  `id_withdrawRequest` int(11) NOT NULL DEFAULT '0',
  `id_refer` varchar(15) NOT NULL DEFAULT '0',
  `id_order` int(11) NOT NULL DEFAULT '0',
  `body_name` varchar(255) NOT NULL,
  `body_direction` int(1) NOT NULL DEFAULT '0',
  `body_stake` decimal(8,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `systems`
--

CREATE TABLE IF NOT EXISTS `systems` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `convert_max` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `convert_min` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `interest_rate` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `systems`
--

INSERT INTO `systems` (`id`, `convert_max`, `convert_min`, `interest_rate`) VALUES
(1, '6.5', '6.2', 2);

-- --------------------------------------------------------

--
-- 表的结构 `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_wechat` varchar(32) NOT NULL,
  `password` varchar(50) DEFAULT NULL,
  `id_introducer` varchar(12) NOT NULL DEFAULT '0',
  `body_phone` varchar(11) NOT NULL DEFAULT '0',
  `body_balance` decimal(8,2) NOT NULL DEFAULT '0.00',
  `body_transactions` decimal(10,2) NOT NULL DEFAULT '0.00',
  `body_transactions_network` decimal(10,2) NOT NULL DEFAULT '0.00',
  `body_bonus` decimal(10,2) NOT NULL DEFAULT '0.00',
  `is_disabled` int(1) NOT NULL DEFAULT '0',
  `grade` int(1) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `withdraw_requests`
--

CREATE TABLE IF NOT EXISTS `withdraw_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` varchar(15) NOT NULL,
  `body_stake` decimal(8,2) NOT NULL,
  `body_name` varchar(30) NOT NULL,
  `body_bank` varchar(255) NOT NULL,
  `body_deposit` varchar(255) NOT NULL,
  `body_number` varchar(30) NOT NULL,
  `body_transfer_number` varchar(255) NOT NULL DEFAULT 'PENDING',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `processed_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

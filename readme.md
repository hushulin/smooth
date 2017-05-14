#文档说明

##数据库处理
<pre>
DROP TABLE IF EXISTS `day_execute_logs`;
CREATE TABLE `day_execute_logs` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` varchar(32) DEFAULT NULL,
  `amount` decimal(6,2) DEFAULT NULL,
  `remark` text,
  `time` int(10) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
</pre>

<pre>
DROP TABLE IF EXISTS `month_execute_logs`;
CREATE TABLE `month_execute_logs` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` varchar(32) DEFAULT NULL,
  `amount` decimal(6,2) DEFAULT NULL,
  `remark` text,
  `time` int(10) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
</pre>
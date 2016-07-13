<?php
$sql = array(
	"ALTER TABLE `wards` ADD `last_update` TIMESTAMP NULL DEFAULT NULL AFTER `data`;",
		"ALTER TABLE `stats` ADD `bot` TINYINT(1) NULL DEFAULT '0' AFTER `datein`;",
		"ALTER TABLE `wards` ADD `parentID` VARCHAR(30) NULL DEFAULT NULL AFTER `data`;"




)
?>

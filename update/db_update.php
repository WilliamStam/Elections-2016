<?php
$sql = array(
	"CREATE TABLE `stats` (  `ID` int(6) NOT NULL,  `userKey` varchar(32) DEFAULT NULL,  `typeID` int(6) DEFAULT NULL,  `page` varchar(250) DEFAULT NULL,  `ip` varchar(30) DEFAULT NULL,  `datein` timestamp NULL DEFAULT CURRENT_TIMESTAMP);",
		"ALTER TABLE `stats`  ADD PRIMARY KEY (`ID`),  ADD KEY `userKey` (`userKey`),  ADD KEY `typeID` (`typeID`);",
		"ALTER TABLE `stats`  MODIFY `ID` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;"
)

?>

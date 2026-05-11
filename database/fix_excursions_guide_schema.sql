-- Fix for the Guide and Excursion back office.
-- Run this once in phpMyAdmin if your existing travel_db.guide table
-- was created before the `tel` column was added.

USE `travel_db`;

ALTER TABLE `guide`
  ADD COLUMN IF NOT EXISTS `tel` VARCHAR(20) NOT NULL DEFAULT '' AFTER `prenom`;

UPDATE `guide`
SET `tel` = CASE `id`
  WHEN 1 THEN '21697768214'
  WHEN 2 THEN '21697768215'
  ELSE `tel`
END
WHERE `tel` = '';

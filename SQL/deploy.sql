ALTER TABLE other_validations ADD jenis_alat INT(5) NOT NULL AFTER manufacturing;

ALTER TABLE `inhouse_validations` ADD `jenis_alat` INT(5) NOT NULL AFTER `machines`;

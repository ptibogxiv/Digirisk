-- Copyright (C) 2021 EOXIA <dev@eoxia.com>
--
-- This program is free software: you can redistribute it and/or modify
-- it under the terms of the GNU General Public License as published by
-- the Free Software Foundation, either version 3 of the License, or
-- (at your option) any later version.
--
-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU General Public License for more details.
--
-- You should have received a copy of the GNU General Public License
-- along with this program.  If not, see https://www.gnu.org/licenses/.

ALTER TABLE llx_digiriskdolibarr_digiriskelement ADD INDEX idx_digiriskdolibarr_digiriskelement_rowid (rowid);
ALTER TABLE llx_digiriskdolibarr_digiriskelement ADD INDEX idx_digiriskdolibarr_digiriskelement_ref (ref);
ALTER TABLE llx_digiriskdolibarr_digiriskelement ADD INDEX idx_digiriskdolibarr_digiriskelement_status (status);
ALTER TABLE llx_digiriskdolibarr_digiriskelement ADD CONSTRAINT llx_digiriskdolibarr_digiriskelement_fk_user_creat FOREIGN KEY (fk_user_creat) REFERENCES llx_user(rowid);
ALTER TABLE llx_digiriskdolibarr_digiriskelement ADD UNIQUE uk_digiriskelement_ref (ref, entity);




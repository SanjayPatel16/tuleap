<?php
/**
 * Copyright (c) Enalean, 2015. All Rights Reserved.
 *
 * This file is a part of Tuleap.
 *
 * Tuleap is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Tuleap is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Tuleap. If not, see <http://www.gnu.org/licenses/>.
 */

class TrovecatDao extends DataAccessObject {

    public function getMandatoryParentCategoriesUnderRoot() {
        $root_id = $this->da->escapeInt(TroveCat::ROOT_ID);

        $sql = "SELECT DISTINCT(parent.trove_cat_id), parent.shortname, parent.fullname
                FROM trove_cat parent
                  LEFT JOIN trove_cat children ON (parent.trove_cat_id = children.parent)
                WHERE parent.mandatory = 1
                  AND parent.parent = $root_id
                  AND children.trove_cat_id IS NOT NULL";

        return $this->retrieve($sql);
    }

    public function getCategoryChildren($trove_cat_id) {
        $trove_cat_id = $this->da->escapeInt($trove_cat_id);

        $sql = "SELECT trove_cat_id, shortname, fullname
                FROM trove_cat
                WHERE parent = $trove_cat_id";

        return $this->retrieve($sql);
    }

}
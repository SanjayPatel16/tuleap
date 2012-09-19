<?php
/**
 * Copyright (c) Enalean, 2012. All Rights Reserved.
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

class ElasticSearch_SearchResult {
    public $item_title;
    public $url;
    public $display_permissions;
    public $permissions;
    public $project_name;
    public $highlight;
        
    public function __construct(array $hit, Project $project) {
        $this->item_title    = $hit['fields']['title'];
        $this->url           = '/plugins/docman/?group_id='.$hit['fields']['group_id'].'&id='.$hit['fields']['id'].'&action=details';
        $this->project_name  = $project->getPublicName();
        $this->highlight     = isset($hit['highlight']['file']) ? array_shift($hit['highlight']['file']) : '';
        $this->has_highlight = !empty($this->highlight);
        $this->display_permissions = isset($hit['fields']['permissions']);
        if ($this->display_permissions) {
            $this->permissions = implode(', ', $hit['fields']['permissions']);
        }
    }
}
?>

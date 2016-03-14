<?php
/**
 * Copyright (c) Sogilis, 2016. All Rights Reserved.
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

namespace Tuleap\Tracker\FormElement\Field\ArtifactLink\Nature;

class NatureTablePresenter {

    public $nature;
    public $nature_label;
    public $id_label;
    public $project_label;
    public $tracker_label;
    public $summary_label;
    public $status_label;
    public $last_update_label;
    public $submitted_by_label;
    public $assigned_to_label;

    public $artifact_links;

    public function __construct(array $artifact_links, $is_reverse_artifact_links) {
        $this->nature = '_is_child';
        $language = $GLOBALS['Language'];
        if($is_reverse_artifact_links) {
            $this->nature_label   = $language->getText('plugin_tracker_artifact_links_natures', '_is_child_reverse');
        } else {
            $this->nature_label   = $language->getText('plugin_tracker_artifact_links_natures', '_is_child_forward');
        }
        $this->id_label           = $language->getText('plugin_tracker_formelement_admin', 'artifactid_label');
        $this->project_label      = $language->getText('plugin_tracker_include_artifact', 'project');
        $this->tracker_label      = 'Tracker';
        $this->summary_label      = $language->getText('plugin_tracker_include_artifact', 'summary');
        $this->status_label       = $language->getText('plugin_tracker_admin_semantic', 'status_label');
        $this->last_update_label  = $language->getText('plugin_tracker_formelement_admin', 'lastupdatedate_label');
        $this->submitted_by_label = $language->getText('plugin_tracker_formelement_admin', 'submittedby_label');
        $this->assigned_to_label  = $language->getText('plugin_tracker_formelement_admin', 'assignedto_label');

        $art_factory = \Tracker_ArtifactFactory::instance();
        $this->artifact_links = array();
        foreach($artifact_links as $artifact_link) {
            $artifact               = $art_factory->getArtifactById($artifact_link->getArtifactId());
            $this->artifact_links[] = new ArtifactInNatureTablePresenter($artifact);
        }
    }
}
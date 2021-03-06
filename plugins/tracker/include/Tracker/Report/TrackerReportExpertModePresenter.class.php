<?php
/**
 * Copyright (c) Enalean, 2016. All Rights Reserved.
 * Copyright (c) Xerox Corporation, Codendi Team, 2001-2009. All rights reserved
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

class TrackerReportExpertModePresenter
{
    public $id;
    public $class_toggler;
    public $is_in_expert_mode;
    public $allowed_fields_label;
    public $query_label;
    public $query_placeholder;
    public $title;
    public $btn_report_normal_mode;
    public $btn_search;
    public $query_tooltip;

    function __construct(
        $id,
        $class_toggler,
        $is_in_expert_mode
    ) {
        $this->id                     = $id;
        $this->class_toggler          = $class_toggler;
        $this->is_in_expert_mode      = $is_in_expert_mode;

        $this->allowed_fields_label   = $GLOBALS['Language']->getText('plugin_tracker_report', 'allowed_fields_label');
        $this->query_label            = $GLOBALS['Language']->getText('plugin_tracker_report', 'query_label');
        $this->query_placeholder      = $GLOBALS['Language']->getText('plugin_tracker_report', 'query_placeholder');
        $this->title                  = $GLOBALS['Language']->getText('plugin_tracker_report', 'search');
        $this->btn_report_normal_mode = $GLOBALS['Language']->getText('plugin_tracker_report', 'btn_report_normal_mode');
        $this->btn_search             = $GLOBALS['Language']->getText('global', 'btn_search');
        $this->query_tooltip          = $GLOBALS['Language']->getText('plugin_tracker_report', 'query_tooltip');
    }
}

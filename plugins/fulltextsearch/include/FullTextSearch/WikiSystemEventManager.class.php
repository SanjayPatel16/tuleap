<?php
/**
 * Copyright (c) Enalean, 2014. All Rights Reserved.
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

class FullTextSearch_WikiSystemEventManager {
    /**
     * @var fulltextsearchPlugin
     */
    private $plugin;

    /**
     * @var ElasticSearch_IndexClientFacade
     */
    private $index_client;

    /**
     * @var SystemEventManager
     */
    private $system_event_manager;

    /**
     * @var TruncateLevelLogger
     */
    private $logger;

    public function __construct(
        SystemEventManager $system_event_manager,
        ElasticSearch_IndexClientFacade $index_client,
        fulltextsearchPlugin $plugin,
        TruncateLevelLogger $logger
    ) {
        $this->system_event_manager = $system_event_manager;
        $this->index_client         = $index_client;
        $this->plugin               = $plugin;
        $this->logger               = $logger;
    }

    public function getSystemEventClass($type, &$class, &$dependencies) {
        switch($type) {
            case SystemEvent_FULLTEXTSEARCH_WIKI_INDEX::NAME:
            case SystemEvent_FULLTEXTSEARCH_WIKI_UPDATE::NAME:
            case SystemEvent_FULLTEXTSEARCH_WIKI_DELETE::NAME:
            case SystemEvent_FULLTEXTSEARCH_WIKI_UPDATE_PERMISSIONS::NAME:
            case SystemEvent_FULLTEXTSEARCH_WIKI_UPDATE_SERVICE_PERMISSIONS::NAME:
                $class = 'SystemEvent_'. $type;
                $dependencies = array(
                    $this->getWikiActions()
                );
                break;
            case SystemEvent_FULLTEXTSEARCH_WIKI_REINDEX_PROJECT::NAME:
                $class = 'SystemEvent_'. $type;
                $dependencies = array(
                    $this->getWikiActions(),
                    $this,
                    $this->logger
                );
                break;
        }
    }

    private function getWikiActions() {
        return new FullTextSearchWikiActions(
            $this->index_client,
            new ElasticSearch_1_2_RequestWikiDataFactory(
                new Wiki_PermissionsManager(
                    PermissionsManager::instance(),
                    ProjectManager::instance(),
                    new UGroupLiteralizer()
                ),
                UserManager::instance()
            ),
            $this->logger
        );
    }

    public function queueIndexWikiPage(array $params) {
        if ($this->plugin->isAllowed($params['group_id'])) {
            $this->system_event_manager->createEvent(
                SystemEvent_FULLTEXTSEARCH_WIKI_INDEX::NAME,
                $this->getWikiSerializedParameters($params),
                SystemEvent::PRIORITY_MEDIUM,
                SystemEvent::OWNER_APP
            );
        }
    }

    public function queueUpdateWikiPage(array $params) {
        if ($this->plugin->isAllowed($params['group_id'])) {
            $this->system_event_manager->createEvent(
                SystemEvent_FULLTEXTSEARCH_WIKI_UPDATE::NAME,
                $this->getWikiSerializedParameters($params),
                SystemEvent::PRIORITY_MEDIUM,
                SystemEvent::OWNER_APP
            );
        }
    }

    public function queueDeleteWikiPage(array $params) {
        if ($this->plugin->isAllowed($params['group_id'])) {
            $this->system_event_manager->createEvent(
                SystemEvent_FULLTEXTSEARCH_WIKI_DELETE::NAME,
                $this->getWikiSerializedParameters($params),
                SystemEvent::PRIORITY_MEDIUM,
                SystemEvent::OWNER_APP
            );
        }
    }

    public function queueUpdateWikiPagePermissions(array $params) {
        if ($this->plugin->isAllowed($params['group_id'])) {
            $this->system_event_manager->createEvent(
                SystemEvent_FULLTEXTSEARCH_WIKI_UPDATE_PERMISSIONS::NAME,
                $this->getWikiSerializedParameters($params),
                SystemEvent::PRIORITY_MEDIUM,
                SystemEvent::OWNER_APP
            );
        }
    }

    public function queueUpdateWikiServicePermissions(array $params) {
        if ($this->plugin->isAllowed($params['group_id'])) {
            $this->system_event_manager->createEvent(
                SystemEvent_FULLTEXTSEARCH_WIKI_UPDATE_SERVICE_PERMISSIONS::NAME,
                $this->getWikiSerializedParameters($params),
                SystemEvent::PRIORITY_MEDIUM,
                SystemEvent::OWNER_APP
            );
        }
    }

    private function getWikiSerializedParameters(array $params, array $additional_params = array()) {
        return implode(
            SystemEvent::PARAMETER_SEPARATOR,
            array_merge(
                array($params['group_id'], $params['wiki_page']),
                $additional_params
            )
        );
    }

    public function queueWikiProjectReindexation($project_id) {
        if ($this->plugin->isAllowed($project_id)) {
            $this->system_event_manager->createEvent(
                SystemEvent_FULLTEXTSEARCH_WIKI_REINDEX_PROJECT::NAME,
                $project_id,
                SystemEvent::PRIORITY_LOW,
                SystemEvent::OWNER_APP
            );
        }
    }

    public function isProjectReindexationAlreadyQueued($project_id) {
        return $this->system_event_manager->areThereMultipleEventsQueuedMatchingFirstParameter(SystemEvent_FULLTEXTSEARCH_WIKI_REINDEX_PROJECT::NAME, $project_id);
    }
}

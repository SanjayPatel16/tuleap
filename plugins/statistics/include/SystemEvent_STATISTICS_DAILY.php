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
  * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  * GNU General Public License for more details.
  *
  * You should have received a copy of the GNU General Public License
  * along with Tuleap. If not, see <http://www.gnu.org/licenses/
  */

class SystemEvent_STATISTICS_DAILY extends SystemEvent {

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var Statistics_DiskUsagePurger
     */
    private $disk_usage_purger;

    /**
     * @var Statistics_ConfigurationManager
     */
    private $configuration_manager;

    const NAME = 'STATISTICS_DAILY';

    public function injectDependencies(
        Logger $logger,
        Statistics_ConfigurationManager $configuration_manager,
        Statistics_DiskUsagePurger $disk_usage_purger
    ) {
        $this->logger                = $logger;
        $this->configuration_manager = $configuration_manager;
        $this->disk_usage_purger     = $disk_usage_purger;
    }

    public function process() {
        if ($this->todayIsSunday()) {
            $this->done('We do not collect datas on Sundays, since the db is stopped');
            return;
        }

        $this->archiveSessions();
        $this->diskUsage();
        $this->purge();

        $this->done();
    }

    private function todayIsSunday() {
        return date("N") === "7";
    }

    private function purge() {
        $this->logger->debug(__METHOD__);
        if ($this->configuration_manager->isDailyPurgeActivated()) {
            $this->disk_usage_purger->purge(strtotime(date('Y-m-d 00:00:00')));
        }
    }

    private function diskUsage() {
        $this->logger->debug(__METHOD__);
        $dum = new Statistics_DiskUsageManager();
        $dum->collectAll();
    }

    /**
     * Each day, load sessions info from elapsed day.
     * We need to do that because sessions are deleted from DB when user logout
     * or when session expire.
     *
     * This not perfect because with very short session (few hours for instance)
     * do data will survive in this session table.
     */
    private function archiveSessions() {
        $this->logger->debug(__METHOD__);
        $max = 0;
        $sql = 'SELECT MAX(time) as max FROM plugin_statistics_user_session';
        $res = db_query($sql);
        if ($res && db_numrows($res) == 1) {
            $row = db_fetch_array($res);
            if($row['max'] != null) {
                $max = $row['max'];
            }
        }

        $sql = 'INSERT INTO plugin_statistics_user_session (user_id, time)'.
               ' SELECT user_id, time FROM session WHERE time > '.$max;
        db_query($sql);
    }

    public function verbalizeParameters($with_link) {
        return '';
    }
}
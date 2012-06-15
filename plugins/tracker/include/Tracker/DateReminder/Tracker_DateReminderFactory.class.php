<?php
/**
 * Copyright (c) STMicroelectronics 2012. All rights reserved
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

require_once('Tracker_DateReminder.class.php');
require_once('dao/Tracker_DateReminderDao.class.php');
require_once 'common/date/DateHelper.class.php';
require_once('common/include/CSRFSynchronizerToken.class.php');
require_once('Tracker_DateReminderException.class.php');

class Tracker_DateReminderFactory {

    protected $tracker;

    /**
     * Constructor of the class
     *
     * @param Tracker $tracker Tracker associated to the manager
     *
     * @return Void
     */
    public function __construct(Tracker $tracker) {
        $this->tracker = $tracker;
        $this->csrf    = new CSRFSynchronizerToken(TRACKER_BASE_URL.'/?func=admin-notifications&tracker='.$this->tracker->id);
    }

    /**
     * Obtain the tracker associated to the manager
     *
     * @return Tracker
     */
    public function getTracker(){
        return $this->tracker;
    }

    /**
     * Retrieve all date reminders for a given tracker
     *
     * @return Array
     */
    public function getTrackerReminders() {
        $reminders          = array();
        $reminderManagerDao = $this->getDao();
        $dar = $reminderManagerDao->getDateReminders($this->getTracker()->getId());
        if ($dar && !$dar->isError()) {
            foreach ($dar as $row) {
                $reminders[] = $this->getInstanceFromRow($row);
            }
        }
        return $reminders;
    }

    /**
     * Add new reminder
     *
     * @param HTTPRequest $request Request object
     *
     * @return Boolean
     */
    public function addNewReminder(HTTPRequest $request) {
        $this->csrf->check();
        $reminderRenderer   = new Tracker_DateReminderRenderer($this->getTracker());
        try {
            $trackerId        = $reminderRenderer->validateTrackerId($request);
            $fieldId          = $reminderRenderer->validateFieldId($request);
            $notificationType = $reminderRenderer->validateNotificationType($request);
            $distance         = $reminderRenderer->validateDistance($request);
            $ugroups          = $reminderRenderer->validateReminderUgroups($request);
            $ugroups          = join(",", $ugroups);
            $this->checkDuplicatedReminders($trackerId, $fieldId, $ugroups, $notificationType, $distance);
            $this->isReminderBeforeOpenDate($fieldId, $notificationType, $distance);
        } catch (Tracker_DateReminderException $e) {
            $GLOBALS['Response']->addFeedback('error', $e->getMessage());
            $GLOBALS['Response']->redirect(TRACKER_BASE_URL.'/?func=admin-notifications&tracker='.$this->getTracker()->getId());
        }
        $historyDao = new ProjectHistoryDao(CodendiDataAccess::instance());
        $historyDao->groupAddHistory("tracker_date_reminder_add", $this->getTracker()->getName().":".$fieldId, $this->getTracker()->getGroupId(), array($distance.' Day(s), Type: '.$notificationType.' Ugroup(s): '.$ugroups));
        $reminder    = $this->getDao()->addDateReminder($trackerId, $fieldId, $ugroups, $notificationType, $distance);
        if ($reminder) {
            return $reminder;
        } else {
            $errorMessage = $GLOBALS['Language']->getText('project_admin_utils','tracker_date_reminder_add_failure', array($trackerId, $fieldId));
            throw new Tracker_DateReminderException($errorMessage);
        }
    }

    /**
     * Throws an excpetion when an enabled date reminder having same params already exist
     *
     * @param Integer $trackerId        Id of the tracker
     * @param Integer $fieldId          Id of the date field
     * @param String  $ugroups          Id of the user groups
     * @param Integer $notificationType 0 if before, 1 if after the value of the date field
     * @param Integer $distance         Distance from the value of the date fiels
     * @return Void
     */
    protected function checkDuplicatedReminders($trackerId, $fieldId, $ugroups, $notificationType, $distance) {
        $dupilcatedReminders = $this->getDao()->findReminders($trackerId, $fieldId, $ugroups, $notificationType, $distance);
        if ($dupilcatedReminders && !$dupilcatedReminders->isError() && $dupilcatedReminders->rowCount() > 0) {
            $errorMessage = $GLOBALS['Language']->getText('project_admin_utils','tracker_date_reminder_add_failure', array($trackerId, $fieldId));
            throw new Tracker_DateReminderException($errorMessage);
        }
    }

    /**
     * Throws an excpetion when an notification type is before 'submitted on' date field
     *
     * @param Integer $fieldId          Id of the date field
     * @param Integer $notificationType 0 if before, 1 if after the value of the date field
     * @param Integer $distance         Distance from the value of the date fiels
     *
     * @return Void
     */
    protected function isReminderBeforeOpenDate($fieldId, $notificationType, $distance) {
        $tff              = Tracker_FormElementFactory::instance();
        $trackerDateField = $tff->getFieldById($fieldId);
        $fieldType        = $tff->getType($trackerDateField);
        if ($fieldType == 'subon' && $notificationType == 0) {
            $errorMessage = $GLOBALS['Language']->getText('project_admin_utils','tracker_date_reminder_add_failure', array($trackerId, $fieldType));
            throw new Tracker_DateReminderException($errorMessage);
        }
    }

    /**
     * Edit a given date reminder
     *
     * @param HTTPRequest $request Reminder edit request
     *
     * @return Boolean
     */
    public function editTrackerReminder($request) {
        $this->csrf->check();
        $reminderRenderer   = new Tracker_DateReminderRenderer($this->getTracker());
        try {
            $reminderId       = $reminderRenderer->validateReminderId($request);
            $notificationType = $reminderRenderer->validateNotificationType($request);
            $ugroups          = $reminderRenderer->validateReminderUgroups($request);
            $distance         = $reminderRenderer->validateDistance($request);
            $status           = $reminderRenderer->validateStatus($request);
            $ugroups          = join(",", $ugroups);
            $fieldId          = $reminderRenderer->validateFieldId($request);
            $this->checkDuplicatedReminders($this->getTracker()->getId(), $fieldId, $ugroups, $notificationType, $distance);
            $this->isReminderBeforeOpenDate($fieldId, $notificationType, $distance);
        } catch (Tracker_DateReminderException $e) {
            $GLOBALS['Response']->addFeedback('error', $e->getMessage());
            $GLOBALS['Response']->redirect(TRACKER_BASE_URL.'/?func=admin-notifications&tracker='.$this->getTracker()->id);
        }
        $historyDao = new ProjectHistoryDao(CodendiDataAccess::instance());
        $historyDao->groupAddHistory("tracker_date_reminder_edit", $this->getTracker()->getName().":".$reminderId, $this->getTracker()->getGroupId(), array("Id: ".$reminderId.", Type: ".$notificationType.", Ugroup(s): ".$ugroups.", Day(s): ".$distance.", Status: ".$status));
        $updateReminder = $this->getDao()->updateDateReminder($reminderId, $ugroups, $notificationType, $distance, $status);
        if ($updateReminder) {
            return $updateReminder;
        } else {
            $errorMessage = $GLOBALS['Language']->getText('project_admin_utils','tracker_date_reminder_update_failure', array($reminderId));
            throw new Tracker_DateReminderException($errorMessage);
        }
    }

    /**
     * Build a reminder instance
     * @TODO: check if this is not an old duplicate of the code in Tracker_DateReminderManager
     *
     * @param array $row The data describing the reminder
     *
     * @return Tracker_DateReminder
     */
    public function getInstanceFromRow($row) {
        return new Tracker_DateReminder($row['reminder_id'],
                                        $row['tracker_id'],
                                        $row['field_id'],
                                        $row['ugroups'],
                                        $row['notification_type'],
                                        $row['distance'],
                                        $row['status']);
    }

    /**
     * Get the Tracker_DateReminder dao
     *
     * @return Tracker_DateReminderDao
     */
    protected function getDao() {
        return new Tracker_DateReminderDao();
    }

    /**
     * Get the reminder
     *
     * @param Integer  $reminderId    The reminder id
     *
     * @return Tracker_DateReminder
     */
    public function getReminder($reminderId) {
        if ($row = $this->getDao()->searchById($reminderId)->getRow()) {
            return $this->getInstanceFromRow($row);
        }
        return null;
    }

    /** Get artifacts that will send notification for a reminder
     * @TODO: check if this is not an old duplicate of the code in Tracker_DateReminderManager
     *
     * @param Tracker_DateReminder $reminder Reminder on which the notification is based on
     *
     *
     * @return Array
     */
    public function getArtifactsByreminder(Tracker_DateReminder $reminder) {
        $artifacts = array();
        $date      = DateHelper::getDistantDateFromToday($reminder->getDistance(), $reminder->getNotificationType());
        // @TODO: Include "last update date" & "submitted on" as types of date fields
        $dao = new Tracker_FormElement_Field_Value_DateDao();
        $dar = $dao->getArtifactsByFieldAndValue($reminder->getFieldId(), $date);
        if ($dar && !$dar->isError()) {
            $artifactFactory = Tracker_ArtifactFactory();
            foreach ($dar as $row) {
                $artifacts[] = $artifactFactory->getArtifactById($row['artifact_id']);
            }
        }
        return $artifacts;
    }

    /**
     * Get an instance of UserManager
     *
     * @return UserManager
     */
    public function getUserManager() {
        return UserManager::instance();
    }

}

?>
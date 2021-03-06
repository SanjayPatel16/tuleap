<?php
// @codingStandardsIgnoreFile
// @codeCoverageIgnoreStart
// this is an autogenerated file - do not edit
function autoload2dfebb202e9a86f1bfa9bc75be5da6f2($class) {
    static $classes = null;
    if ($classes === null) {
        $classes = array(
            'projectquotahtml' => '/ProjectQuotaHtml.class.php',
            'projectquotamanager' => '/ProjectQuotaManager.class.php',
            'projectsoverquotatableheaderpresenter' => '/ProjectsOverQuotaTableHeaderPresenter.class.php',
            'projectsoverquotatablepresenter' => '/ProjectsOverQuotaTablePresenter.class.php',
            'statistics_configurationdao' => '/Statistics_ConfigurationDao.class.php',
            'statistics_configurationmanager' => '/Statistics_ConfigurationManager.class.php',
            'statistics_diskusagedao' => '/Statistics_DiskUsageDao.class.php',
            'statistics_diskusagegraph' => '/Statistics_DiskUsageGraph.class.php',
            'statistics_diskusagehtml' => '/Statistics_DiskUsageHtml.class.php',
            'statistics_diskusagemanager' => '/Statistics_DiskUsageManager.class.php',
            'statistics_diskusageoutput' => '/Statistics_DiskUsageOutput.class.php',
            'statistics_diskusagepurger' => '/Statistics_DiskUsagePurger.class.php',
            'statistics_event' => '/Statistics_Event.php',
            'statistics_formatter' => '/Statistics_Formatter.class.php',
            'statistics_formatter_cvs' => '/Statistics_Formatter_Cvs.class.php',
            'statistics_formatter_scm' => '/Statistics_Formatter_Scm.class.php',
            'statistics_formatter_svn' => '/Statistics_Formatter_Svn.class.php',
            'statistics_projectquotadao' => '/Statistics_ProjectQuotaDao.class.php',
            'statistics_scmcvsdao' => '/Statistics_ScmCvsDao.class.php',
            'statistics_scmsvndao' => '/Statistics_ScmSvnDao.class.php',
            'statistics_services_usageformatter' => '/Statistics_Services_UsageFormatter.class.php',
            'statistics_servicesusagedao' => '/Statistics_ServicesUsageDao.class.php',
            'statistics_soapserver' => '/Statistics_SOAPServer.class.php',
            'statistics_widget_projectstatistics' => '/Statistics_Widget_ProjectStatistics.class.php',
            'statisticsaggregatordao' => '/StatisticsAggregatorDao.class.php',
            'statisticsplugin' => '/statisticsPlugin.class.php',
            'statisticsplugindescriptor' => '/StatisticsPluginDescriptor.class.php',
            'statisticsplugininfo' => '/StatisticsPluginInfo.class.php',
            'systemevent_statistics_daily' => '/SystemEvent_STATISTICS_DAILY.php',
            'systemeventqueuestatistics' => '/SystemEventQueueStatistics.php',
            'tuleap\\statistics\\adminheaderpresenter' => '/AdminHeaderPresenter.php',
            'tuleap\\statistics\\dataexportpresenter' => '/DataExportPresenter.php',
            'tuleap\\statistics\\dataexportpresenterbuilder' => '/DataExportPresenterBuilder.php',
            'tuleap\\statistics\\dataexportrouter' => '/DataExportRouter.php',
            'tuleap\\statistics\\diskusageglobalpresenter' => '/DiskUsageGlobalPresenter.php',
            'tuleap\\statistics\\diskusageglobalpresenterbuilder' => '/DiskUsageGlobalPresenterBuilder.php',
            'tuleap\\statistics\\diskusageprojectspresenter' => '/DiskUsageProjectsPresenter.php',
            'tuleap\\statistics\\diskusageprojectspresenterbuilder' => '/DiskUsageProjectsPresenterBuilder.php',
            'tuleap\\statistics\\diskusageprojectssearchfieldspresenter' => '/DiskUsageProjectsSearchFieldsPresenter.php',
            'tuleap\\statistics\\diskusagerouter' => '/DiskUsageRouter.php',
            'tuleap\\statistics\\diskusagesearchfieldspresenter' => '/DiskUsageSearchFieldsPresenter.php',
            'tuleap\\statistics\\diskusageservicespresenter' => '/DiskUsageServicesPresenter.php',
            'tuleap\\statistics\\diskusageservicespresenterbuilder' => '/DiskUsageServicesPresenterBuilder.php',
            'tuleap\\statistics\\diskusageservicessearchfieldspresenter' => '/DiskUsageServicesSearchFieldsPresenter.php',
            'tuleap\\statistics\\diskusagetopuserspresenter' => '/DiskUsageTopUsersPresenter.php',
            'tuleap\\statistics\\diskusagetopuserspresenterbuilder' => '/DiskUsageTopUsersPresenterBuilder.php',
            'tuleap\\statistics\\diskusageuserdetailspresenter' => '/DiskUsageUserDetailsPresenter.php',
            'tuleap\\statistics\\diskusageuserdetailspresenterbuilder' => '/DiskUsageUserDetailsPresenterBuilder.php',
            'tuleap\\statistics\\diskusageuserdetailssearchfieldspresenter' => '/DiskUsageUserDetailsSearchFieldsPresenter.php',
            'tuleap\\statistics\\frequenciespresenter' => '/FrequenciesPresenter.php',
            'tuleap\\statistics\\frequenciessearchfieldspresenter' => '/FrequenciesSearchFieldsPresenter.php',
            'tuleap\\statistics\\projectquotapresenter' => '/ProjectQuotaPresenter.php',
            'tuleap\\statistics\\scmstatisticspresenter' => '/SCMStatisticsPresenter.php',
            'tuleap\\statistics\\searchfieldspresenterbuilder' => '/SearchFieldsPresenterBuilder.php',
            'tuleap\\statistics\\servicesusagepresenter' => '/ServicesUsagePresenter.php',
            'tuleap\\statistics\\startdategreaterthanenddateexception' => '/StartDateGreaterThanEndDateException.php',
            'tuleap\\statistics\\usageprogresspresenter' => '/UsageProgressPresenter.php'
        );
    }
    $cn = strtolower($class);
    if (isset($classes[$cn])) {
        require dirname(__FILE__) . $classes[$cn];
    }
}
spl_autoload_register('autoload2dfebb202e9a86f1bfa9bc75be5da6f2');
// @codeCoverageIgnoreEnd

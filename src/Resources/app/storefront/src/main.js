import BoxalinoIntegrationPlugin from './boxalino-real-time-user-experience-integration/boxalino-real-time-user-experience-integration.plugin';

const PluginManager = window.PluginManager;
PluginManager.register(
    'BoxalinoRealTimeUserExperienceIntegrationPlugin',
    BoxalinoRealTimeUserExperienceIntegrationPlugin,
    '[data-boxalino-integration]'
);

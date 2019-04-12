import { Module } from 'src/core/shopware';
import './extension/sw-settings-index';
import './page/sw-settings-instagram-index';

import deDE from './snippet/de_DE.json';
import enGB from './snippet/en_GB.json';


Module.register('sw-settings-instagram', {
    type: 'core',
    name: 'Instagram Shopping',
    description: 'sw-settings-instagram.general.description',
    version: '1.0.0',
    targetVersion: '1.0.0',
    color: '#9AA8B5',
    icon: 'default-action-settings',
    favicon: 'icon-module-settings.png',
    entity: 'store_settings',

    snippets: {
        'de-DE': deDE,
        'en-GB': enGB
    },

    routes: {
        index: {
            component: 'sw-settings-instagram-index',
            path: 'index',
            meta: {
                parentPath: 'sw.settings.index'
            }
        }
    },

    navigation: [{
        id: 'sw-settings-instagram',
        icon: 'default-action-settings',
        label: 'sw-settings-instagram.general.mainMenuItemGeneral',
        color: '#9AA8B5',
        path: 'sw.settings.instagram.index',
        parent: 'sw-settings'
    }]
});

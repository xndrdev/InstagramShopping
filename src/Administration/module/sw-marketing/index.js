import { Module } from 'src/core/shopware'
import './component/sw-marketing-item';
import './page/sw-marketing-index';

import enGB from './snippet/en_GB.json';

Module.register('sw-marketing', {
    type: 'plugin',
    name: 'Marketing',
    description: 'Marketing',
    version: '1.0.0',
    targetVersion: '1.0.0',
    color: '#ff3d58',
    icon: 'default-shopping-paper-bag-product',

    snippets: {
        'en-GB': enGB
    },

    routes: {
        index: {
            component: 'sw-marketing-index',
            path: 'index'
        },

    },

    navigation: [
        {
            id: 'sw-marketing-index',
            label: 'Marketing',
            color: '#ff3d58',
            path: 'sw.marketing.index',
            icon: 'default-shopping-paper-bag-product',
            position: 100
        }
    ]
});

import { Application, Component, Mixin } from 'src/core/shopware';
import template from './instagram-shopping-settings.html.twig';

Component.register('instagram-shopping-settings', {
    template,

    inject: ['loginService'],

    mixins: [
        Mixin.getByName('notification')
    ],

    methods: {
        exportProducts() {
            const httpClient = Application.getContainer('init').httpClient;

            httpClient.get(
                '/instagram-shopping-export', {
                    headers: {
                        Authorization: `Bearer ${this.loginService.getToken()}`
                    }
                }
            ).then((response) => {
                this.createNotificationSuccess({
                    title: 'Export successful',
                    message: 'Exported '+response.data.data+' item(s)'
                });
            }).catch(() => {
                this.createNotificationError({
                    title: 'Error while exporting',
                    message: 'Service unavailable'
                });
            });
        },
    }
});

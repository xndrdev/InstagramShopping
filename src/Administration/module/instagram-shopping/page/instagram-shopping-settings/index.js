import { Application, Component } from 'src/core/shopware';
import template from './instagram-shopping-settings.html.twig';

Component.register('instagram-shopping-settings', {
    template,

    inject: ['loginService'],

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
                console.log(response.data);
            });
        },
    }
});

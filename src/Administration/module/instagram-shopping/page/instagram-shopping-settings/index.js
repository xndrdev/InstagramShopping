import { Application, Component } from 'src/core/shopware';
import template from './instagram-shopping-settings.html.twig';

Component.register('instagram-shopping-settings', {
    template,

    methods: {
        exportProducts() {
            const httpClient = Application.getContainer('init').httpClient;

            httpClient.post(
                '/instagram-shopping-export'
            ).then((response) => {
                console.log(response.data);
            });
        },
    }
});

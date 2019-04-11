import { Application, Component, Mixin } from 'src/core/shopware';
import template from './instagram-shopping-settings.html.twig';

Component.register('instagram-shopping-settings', {
    template,

    inject: ['loginService'],

    mixins: [
        Mixin.getByName('notification')
    ],

    data: function() {
        return {
            isLoading: false,
        }
    },

    methods: {
        exportProducts() {
            const httpClient = Application.getContainer('init').httpClient;

            this.isLoading = true;
            
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
            }).finally(() => {
                this.isLoading = false;
            });
        },

        deleteProducts() {
            const httpClient = Application.getContainer('init').httpClient;

            this.isLoading = true;
            
            httpClient.get(
                '/instagram-shopping-delete', {
                    headers: {
                        Authorization: `Bearer ${this.loginService.getToken()}`
                    }
                }
            ).then((response) => {
                this.createNotificationSuccess({
                    title: 'Delete successful',
                    message: 'Deleted '+response.data.data+' item(s)'
                });
            }).catch(() => {
                this.createNotificationError({
                    title: 'Error while deleting',
                    message: 'Service unavailable'
                });
            }).finally(() => {
                this.isLoading = false;
            });
        },

        getAllProducts() {
            const httpClient = Application.getContainer('init').httpClient;

            this.isLoading = true;
            
            httpClient.get(
                '/instagram-shopping-products', {
                    headers: {
                        Authorization: `Bearer ${this.loginService.getToken()}`
                    }
                }
            ).then((response) => {
                console.log(response);
            }).catch((error) => {
                console.log(error);
            }).finally(() => {
                this.isLoading = false;
            });
        },

        getLocalProducts() {
            const httpClient = Application.getContainer('init').httpClient;

            this.isLoading = true;
            
            httpClient.get(
                '/instagram-shopping-local-products', {
                    headers: {
                        Authorization: `Bearer ${this.loginService.getToken()}`
                    }
                }
            ).then((response) => {
                console.log(response);
            }).catch((error) => {
                console.log(error);
            }).finally(() => {
                this.isLoading = false;
            });
        }
    }
});

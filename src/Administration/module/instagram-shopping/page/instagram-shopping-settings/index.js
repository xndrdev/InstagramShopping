import { Application, Component, Mixin } from 'src/core/shopware';
import template from './instagram-shopping-settings.html.twig';

Component.register('instagram-shopping-settings', {
    template,

    inject: [
        'loginService',
        'repositoryFactory',
        'context'
    ],

    data() {
        return {
            repository: null,
            products: null,
            isLoading: false,
            localProducts: [],
            instagramProducts: [],
            localProductsLoading: true,
            instagramProductsLoading: true
        };
    },

    mixins: [
        Mixin.getByName('notification')
    ],


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

        getLocalProducts() {
            const httpClient = Application.getContainer('init').httpClient;

            this.isLoading = true;
            this.localProductsLoading = true;
            
            httpClient.get(
                '/instagram-shopping-local-products', {
                    headers: {
                        Authorization: `Bearer ${this.loginService.getToken()}`
                    }
                }
            ).then((response) => {
                this.localProducts = response.data.data;
            }).catch((error) => {
                console.log(error);
            }).finally(() => {
                this.isLoading = false;
                this.localProductsLoading = false;
            });
        },

        getInstagramProducts() {
            const httpClient = Application.getContainer('init').httpClient;

            this.isLoading = true;
            this.instagramProductsLoading = true;

            httpClient.get(
                '/instagram-shopping-products', {
                    headers: {
                        Authorization: `Bearer ${this.loginService.getToken()}`
                    }
                }
            ).then((response) => {
                this.instagramProducts = response.data.data;
            }).catch((error) => {
                console.log(error);
            }).finally(() => {
                this.isLoading = false;
                this.instagramProductsLoading = false;
            });
        }
    },

    computed: {
        columnsLocal() {
            return [
                {
                    property: 'name',
                    dataIndex: 'name',
                    label: 'Local Products',
                    allowResize: false,
                    primary: true
                }
            ];
        },
        columnsInstagram() {
            return [
                {
                    property: 'name',
                    dataIndex: 'name',
                    label: 'Instagram Products',
                    allowResize: false,
                    primary: true
                }
            ];
        }
    },

    created() {
        this.repository = this.repositoryFactory.create('product');
        this.isLoading = true;

        this.getLocalProducts();
        this.getInstagramProducts();
    }
});

import Vue from 'vue';
import AxiosPlugin from 'vue-axios-cors';
import Toasted from 'vue-toasted';
Vue.use(AxiosPlugin);
Vue.use(Toasted, {
    theme: 'nova',
    position: 'bottom-right',
    duration: 10000
});

new Vue({
    el: '#license',
    data() {
        return {
            data: {},
            count: 0,
            loaded: false,
            ready: false,
        };
    },
    created() {
        Nova.request().put('/nova-api/maia-settings/settings/field/license_status');
    },
    mounted() {
        this.getData();
    },
    methods: {
        async getData() {
            const {
                data: {data},
            } = await Nova.request()
                .get('/nova-vendor/maia-license')
                .catch(error => {
                    if (error.response.status === 404) {
                        this.$router.push({name: '404'});
                        return;
                    }
                });
            this.data.email = data.email;
            this.data.token = data.token;
            this.data.url = data.url;
            this.data.key = data.key;
            this.loaded = true;
            this.refreshPeriodically();
        },
        refreshPeriodically() {
            if (!this.loaded) {
                return;
            }
            Promise.all([
                this.post(this.count),
            ]).then(() => {
                this.ready = true;
                this.timeout = setTimeout(() => {
                    this.refreshPeriodically(false);
                }, 180000);
            });
        },
        post(count) {
            this.$axios({
                url: 'http://nova.admin.com/nova-vendor/maia-license/',
                method: 'POST',
                data: {
                    'email': this.data.email,
                    'token': this.data.token,
                    'url': this.data.url,
                    'key': this.data.key
                }
            }).then(({ data: { expired = false, message } }) => {
                if(expired) {
                    this.$toasted.show(message, {type: 'info'});
                } else {
                    this.loaded = false;
                }
            }).catch(error => {
                this.count = count+1;
                if(this.count === 1) {
                    this.$toasted.show(error.response.data.message, {type: 'error', duration: null, closeOnSwipe: false});
                }
                if(this.count === 3) {
                    this.$toasted.show('You will be logout in 10 seconds', {type: 'error', duration: null, closeOnSwipe: false});
                    setTimeout(() => {
                        Nova.request().delete('/nova-api/maia-settings/settings/field/license_status').then(r => {
                            this.$axios.get('/admin/logout')
                                .then(response => {
                                    this.$router.push('/admin');
                                }).catch(error => {
                                location.reload();
                            });
                        });
                    }, 10000);
                }
            })
        }
    }
});
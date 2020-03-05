<template>
    <loading-view :loading="loading">
        <form v-if="panels" @submit.prevent="update" autocomplete="off">
            <slot>
                <h4 class="text-90 font-normal text-2xl mb-3">{{ panel_name }}</h4>
            </slot>
            <div class="relationship-tabs-panel card mb-8">
                <div class="flex flex-row">
                    <div
                            class="py-5 px-8 border-b-2 focus:outline-none tab cursor-pointer"
                            :class="[activeTab === tab.name ? 'text-grey-black font-bold border-primary': 'text-grey font-semibold border-40']"
                            v-for="(tab, key) in tabs"
                            :key="key"
                            @click="handleTabClick(tab, $event)"
                    >{{ tab.name }}
                    </div>
                    <div class="flex-1 border-b-2 border-40"></div>
                </div>

                <div
                        v-for="(tab, index) in tabs"
                        v-show="tab.name === activeTab"
                        :label="tab.name"
                        :key="'related-tabs-fields' + index"
                >
                    <div :class="{'px-6 py-3':!tab.listable}">
                        <component
                                v-for="(field, index) in tab.fields"
                                :class="{'remove-bottom-border': index === tab.fields.length - 1}"
                                :key="'tab-' + index"
                                :is="'form-' + field.component"
                                :resource-name="'maia-settings'"
                                :resource-id="'settings'"
                                :errors="validationErrors"
                                :field="field"
                                @actionExecuted="actionExecuted"
                        />
                    </div>
                </div>
            </div>
            <!-- Update Button -->
            <div class="flex items-center">
                <progress-button
                        class="ml-auto"
                        @click.native="update"
                        :disabled="isUpdating"
                        :processing="isUpdating"
                >
                    {{ __('Save settings') }}
                </progress-button>
            </div>
        </form>
        <div class="py-3 px-6 border-50" v-else>
            <div class="flex">
                <div class="w-1/4 py-4">
                    <h4 class="font-normal text-80">Error</h4>
                </div>
                <div class="w-3/4 py-4">
                    <p class="text-90">No settings fields have been defined.</p>
                </div>
            </div>
        </div>
    </loading-view>
</template>

<script>
    import {Errors} from 'laravel-nova';

    export default {
        data() {
            return {
                loading: false,
                isUpdating: false,
                fields: [],
                panels: [],
                panel_name: null,
                tabs: null,
                activeTab: '',
                validationErrors: new Errors(),
            };
        },
        async created() {
            this.getFields();
        },
        methods: {
            async getFields() {
                this.loading = true;
                this.fields = [];
                const {
                    data: {fields, panels},
                } = await Nova.request()
                    .get('/nova-vendor/maia-settings/settings')
                    .catch(error => {
                        if (error.response.status === 404) {
                            this.$router.push({name: '404'});
                            return;
                        }
                    });
                this.fields = fields;
                this.panels = panels;
                this.panel_name = panels[0].name;
                this.loading = false;

                let tabs = {};
                this.fields.forEach(field => {
                    if (!tabs.hasOwnProperty(field.tab)) {
                        tabs[field.tab] = {
                            name: field.tab,
                            init: false,
                            listable: field.listableTab,
                            fields: []
                        };
                    }
                    tabs[field.tab].fields.push(field);
                });
                this.tabs = tabs;
                if (!_.isUndefined(this.$route.query.tab)) {
                    if (_.isUndefined(tabs[this.$route.query.tab])) {
                        this.handleTabClick(tabs[Object.keys(tabs)[0]]);
                    } else {
                        this.activeTab = this.$route.query.tab;
                        this.handleTabClick(tabs[this.$route.query.tab]);
                    }
                } else {
                    this.handleTabClick(tabs[Object.keys(tabs)[0]]);
                }
            },
            async update() {
                try {
                    this.isUpdating = true;
                    const response = await this.updateRequest();
                    this.$toasted.show('Settings successfully updated', {
                        type: 'success',
                    });
                    await this.getFields();
                    this.isUpdating = false;
                    this.validationErrors = new Errors();
                } catch (error) {
                    this.isUpdating = false;
                    if (error && error.response && error.response.status === 422) {
                        this.validationErrors = new Errors(error.response.data.errors);
                    }
                }
            },
            actionExecuted() {
                this.$emit('actionExecuted');
            },
            handleTabClick(tab, event) {
                tab.init = true;
                this.activeTab = tab.name;
            },
            updateRequest() {
                return Nova.request().post('/nova-vendor/maia-settings/settings', this.formData);
            },
        },
        computed: {
            formData() {
                return _.tap(new FormData(), formData => {
                    _(this.fields).each(field => field.fill(formData));
                    formData.append('_method', 'POST');
                });
            },
        },
    };
</script>

<style lang="scss">
    .relationship-tabs-panel {
        .text-error {
            color: var(--danger);

            &.border-primary {
                border-color: var(--danger);
            }
        }

        .card {
            box-shadow: none;
        }

        h1 {
            display: none;
        }

        .tab {
            padding-top: 1.25rem;
            padding-bottom: 1.25rem;
        }

        .tab-content > div > .relative > .flex {
            justify-content: flex-end;
            padding-left: 0.75rem;
            padding-right: 0.75rem;
            position: absolute;
            top: 0;
            right: 0;
            transform: translateY(-100%);
            align-items: center;
            height: 62px;

            > .mb-6 {
                margin-bottom: 0;
            }

            > .w-full {
                width: auto;
                margin-left: 1.5rem;
            }
        }
    }
</style>
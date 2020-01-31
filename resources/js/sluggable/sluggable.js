Nova.booting((Vue, router, store) => {
    Vue.component('index-maia-sluggable-slug-field', require('./components/Slug/IndexField'));
    Vue.component('detail-maia-sluggable-slug-field', require('./components/Slug/DetailField'));
    Vue.component('form-maia-sluggable-slug-field', require('./components/Slug/FormField'));
    Vue.component('index-maia-sluggable-sluggabletext-field', require('./components/SluggableText/IndexField'));
    Vue.component('detail-maia-sluggable-sluggabletext-field', require('./components/SluggableText/DetailField'));
    Vue.component('form-maia-sluggable-sluggabletext-field', require('./components/SluggableText/FormField'));
});

Nova.booting((Vue, router, store) => {
    router.addRoutes([
        {
            name: 'maia-seo',
            path: '/seo',
            component: require('./components/Seo').default,
        },
    ]);
});

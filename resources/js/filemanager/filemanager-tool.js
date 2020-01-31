Nova.booting((Vue, router) => {
    router.addRoutes([
        {
            name: 'maia-filemanager',
            path: '/filemanager',
            component: require('./components/Tool').default,
        },
    ]);
});

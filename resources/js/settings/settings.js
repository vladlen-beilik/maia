Nova.booting((Vue, router, store) => {
    router.addRoutes([
        {
            name: 'maia-settings',
            path: '/settings',
            component: require('./components/Settings').default,
        },
    ]);
});

Nova.booting((Vue, router, store) => {
    router.addRoutes([
        {
            name: 'maia-horizon',
            path: '/horizon',
            component: require('./components/Tool').default,
        },
    ]);
});
module.exports = {
    plugins: {
        'postcss-preset-env': {
            stage: 3, // Le niveau de maturité des fonctionnalités qu'on veut utiliser, '0' toutes les fonctionnalités expérimentales, '1' beaucoup de fonctionnalités y compris expérimentales, '2' fonctionnalités plus stables, '3' presque standardisées (r)
        },
        autoprefixer: {}, // { grid: true } -- Pour configurer autoprefixer inclus
        /*
            - On peut ajouter d'autres plugins
                - 'postcss-import': {},
                - 'postcss-nested': {}
        */
        /*
            features: {
                'nesting-rules': true -- Pour activer le nesting css '& .child'
            }
        */
    },
};
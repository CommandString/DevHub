const Encore = require('@symfony/webpack-encore');

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

const ENTRIES = ["home", "base", "404", "login", "register", "profile", "question", "questions"];

Encore
    .setOutputPath('public/assets/')
    .setPublicPath('/assets')
    .splitEntryChunks()
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild(["*.js", "*.css"])
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .enableSassLoader()
    .configureBabel((config) => {
        config.plugins.push('@babel/plugin-proposal-class-properties');
    })
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })
;

for (let entry in ENTRIES) {
    Encore.addEntry(ENTRIES[entry], `./assets/${ENTRIES[entry]}.js`);
}

module.exports = Encore.getWebpackConfig();
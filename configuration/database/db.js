var config = require('../config');
var Sequelize = require('sequelize');
const db = new Sequelize(config.db.cfg_db, config.db.cfg_db_user, config.db.cfg_db_pwd, {
    host: config.db.cfg_db_host,
    dialect: 'mysql',
    port: config.db.port,
    database : config.db.cfg_db,
    timezone : '+07:00',
    logging: config.db.logging,
    freezeTableName: true,
    pool: {
        max: config.db.cfg_MAX_POOL,
        min: config.db.cfg_MIN_POLL,
        idle: config.db.cfg_IDLE
    },
    define: {
        timestamps: false
    }
});

module.exports = db, db_conf;


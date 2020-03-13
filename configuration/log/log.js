/**
 * 
	@author Marvis
	@version 1.0
	@copyright Marvis
 * 
 */

var log4js = require('log4js');
var config = require('../config');

log4js.configure({
    appenders: {
      everything: { type: 'dateFile', filename: config.log.fileName , keepFileExt: true }
    },
    categories: {
      default: { appenders: [ 'everything' ], level: config.log.level }
    }
});

const logger = log4js.getLogger('ANIME');
module.exports = logger;
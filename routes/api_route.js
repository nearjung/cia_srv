var express = require('express');
var api_router = express.Router();

/** Controller */
var apiController = require('../controller/api/get');


api_router.get('/carapi', apiController.car_api);



module.exports = api_router;
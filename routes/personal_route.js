var express = require('express');
var personal_router = express.Router();

/** Controller */
var personalController = require('../controller/personal/get');


personal_router.get('/get', personalController.get);



module.exports = personal_router;
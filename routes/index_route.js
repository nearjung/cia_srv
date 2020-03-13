var express = require('express');
var router = express.Router();

/* GET home page. */
router.get('/', function(req, res, next) {
    var io = req.app.get('io')
    res.render('index', { title: 'Service REST API' });
});


module.exports =  router;

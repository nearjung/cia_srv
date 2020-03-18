var serviceResult = require('../../model/serviceResult');
const log = require('../../configuration/log/log');
var sql = require("mssql");
var db = require('../../configuration/config');
const get = async (req, res, next) => {
    try {
        var searchTxt = req.query.searchTxt;

        sql.connect(db.connect, (err) => {
            if (err) {
                console.log(err);
            }
            var request = new sql.Request();
            var command = "";
            command += "SELECT * FROM " + db.table.census + "";
            command += " WHERE Name LIKE '%" + searchTxt + "%' OR IDCard LIKE '%" + searchTxt + "%'";
            request.query(command, (err, result) => {
                if (err) {
                    console.log(err);
                    serviceResult.code = 500;
                    serviceResult.status = "Error";
                    serviceResult.text = "Error: " + err.message;
                    res.json(serviceResult);
                } else {
                    serviceResult.value = result;
                    serviceResult.code = 200;
                    serviceResult.status = "Success";
                    serviceResult.text = "Load Success";
                    res.json(serviceResult);
                }
            })
        })
    } catch (err) {
        console.error(err);
        log.error(err.stack);
        serviceResult.code = 500;
        serviceResult.status = "Error";
        serviceResult.text = "Error: " + err.message;
        res.json(serviceResult);
    }
}

const getPersonal = (req, res, next) => {
    try {
        var IDCard = req.query.idcard;
        if (IDCard) {
            sql.connect(db.connect, (err) => {
                if (err) {
                    console.log(err);
                }
                var request = new sql.Request();
                var sql = "";
                sql += "";
            })
        }
    } catch (err) {
        console.error(err);
        log.error(err.stack);
        serviceResult.code = 500;
        serviceResult.status = "Error";
        serviceResult.text = "Error: " + err.message;
        res.json(serviceResult);
    }
}

module.exports = { get, getPersonal };
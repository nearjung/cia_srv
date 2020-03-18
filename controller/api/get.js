var serviceResult = require('../../model/serviceResult');
const log = require('../../configuration/log/log');
var sql = require("mssql");
var db = require('../../configuration/config');

const car_api = async (req, res, next)  => {
    try {
        var plat1 = req.query.plate1;
        var plat2 = req.query.plate2;
        var province = req.query.province;
        var certification = req.query.certification;

        await sql.connect(db.connect, async (err) => {
            if (err) {
                console.error(err);
                log.error(err.stack);
                serviceResult.code = 500;
                serviceResult.status = "Error";
                serviceResult.text = "Error: " + err.message;
                res.json(serviceResult);
            } else {
                var request = await new sql.Request();
                var cmd = " SELECT cert_key FROM trcertification WHERE cert_key = '" + certification + "'";
                await request.query(cmd, async (err, result) => {
                    if (err) {
                        console.log(err);
                        serviceResult.code = 500;
                        serviceResult.status = "Error";
                        serviceResult.text = "Error: " + err.message;
                        res.json(serviceResult);
                    } else {
                        if (result.rowsAffected > 0) {
                            await sql.connect(db.connect, async (err) => {
                                if (err) {
                                    console.error(err);
                                    log.error(err.stack);
                                    serviceResult.code = 500;
                                    serviceResult.status = "Error";
                                    serviceResult.text = "Error: " + err.message;
                                    res.json(serviceResult);
                                } else {
                                    var request = await new sql.Request();
                                    var cmd = "";
                                    cmd += "SELECT [TYPE_D], [REG_DATE], [EXP_DATE], [PLATE1], [PLATE2], [OFF_PROV_D], [BRAND_D], [MODEL], [MKModel], [ACQ_TTL_D],";
                                    cmd += " [ACQ_FNAME], [ACQ_LNAME], [ACQ_ADDR], [ACQ_TUM_D], [ACQ_AMP_D], [ACQ_PRV_D], [ACQ_ZIP], [OCC_DATE], [RANK_OWNER],";
                                    cmd += " [JUT_TTL_D], [JUT_FNAME], [JUT_LNAME], [JUT_ADDR], [JUT_TUM_D], [JUT_AMP_D], [JUT_PRV_D], [JUT_ZIP],";
                                    cmd += " [NUM_BODY], [NUM_ENG], [JUT_BIRTH], [JUT_ID], [ACQ_BIRTH], [ACQ_ID], [ACQ_NAT_D], [COLOR1_D],";
                                    cmd += " [CC], [FUEL], [WGT_CAR], [MFG_YEAR], [STAT_CODE], [Ownertype], [Payment]";
                                    cmd += " FROM "+ db.table.carall +"";
                                    cmd += " WHERE PLATE1 = '" + plat1 + "' AND PLATE2 = '" + plat2 + "' AND ACQ_PRV_D = '" + province + "'";
                                    await request.query(cmd, async (err, result) => {
                                        if (err) {
                                            console.log(err);
                                            serviceResult.code = 500;
                                            serviceResult.status = "Error";
                                            serviceResult.text = "Error: " + err.message;
                                            res.json(serviceResult);
                                        } else {
                                            serviceResult.id = certification;
                                            serviceResult.name = "CIA Rest api";
                                            serviceResult.value = result.recordset;
                                            serviceResult.code = 200;
                                            serviceResult.status = "Success";
                                            serviceResult.text = "Load Success";
                                            await res.json(serviceResult);
                                        }
                                    })
                                }
                            })
                        } else {
                            serviceResult.code = 500;
                            serviceResult.status = "Error";
                            serviceResult.text = "Error: Invalid certificate.";
                            res.json(serviceResult);
                        }
                    }
                })
            }
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

const nexfin_api = (req, res, next) => {
    try {
        var plat1 = req.query.plate1.replace("'", "");
        var plat2 = req.query.plate2.replace("'", "");
        var province = req.query.province.replace("'", "");
        var certification = req.query.certification.replace("'", "");

        sql.connect(db.connect, (err) => {
            if (err) {
                console.error(err);
                log.error(err.stack);
                serviceResult.code = 500;
                serviceResult.status = "Error";
                serviceResult.text = "Error: " + err.message;
                res.json(serviceResult);
            } else {
                var request = new sql.Request();
                var cmd = " SELECT cert_key FROM trcertification WHERE cert_key = '" + certification + "'";
                request.query(cmd, (err, result) => {
                    if (err) {
                        console.log(err);
                        serviceResult.code = 500;
                        serviceResult.status = "Error";
                        serviceResult.text = "Error: " + err.message;
                        res.json(serviceResult);
                    } else {
                        if (result.rowsAffected > 0) {
                            sql.connect(db.connect, (err) => {
                                if (err) {
                                    console.error(err);
                                    log.error(err.stack);
                                    serviceResult.code = 500;
                                    serviceResult.status = "Error";
                                    serviceResult.text = "Error: " + err.message;
                                    res.json(serviceResult);
                                } else {
                                    var request = new sql.Request();
                                    var cmd = "";
                                    cmd += "SELECT [TYPE_D], [REG_DATE], [EXP_DATE], [PLATE1], [PLATE2], [OFF_PROV_D], [BRAND_D], [MODEL], [MKModel], [ACQ_TTL_D],";
                                    cmd += " [ACQ_FNAME], [ACQ_LNAME], [ACQ_ADDR], [ACQ_TUM_D], [ACQ_AMP_D], [ACQ_PRV_D], [ACQ_ZIP], [OCC_DATE], [RANK_OWNER],";
                                    cmd += " [JUT_TTL_D], [JUT_FNAME], [JUT_LNAME], [JUT_ADDR], [JUT_TUM_D], [JUT_AMP_D], [JUT_PRV_D], [JUT_ZIP],";
                                    cmd += " [NUM_BODY], [NUM_ENG], [JUT_BIRTH], [JUT_ID], [ACQ_BIRTH], [ACQ_ID], [ACQ_NAT_D], [COLOR1_D],";
                                    cmd += " [CC], [FUEL], [WGT_CAR], [MFG_YEAR], [STAT_CODE], [Ownertype], [Payment]";
                                    cmd += " FROM "+ db.table.carall +"";
                                    cmd += " WHERE PLATE1 = '" + plat1 + "' AND PLATE2 = '" + plat2 + "' AND ACQ_PRV_D = '" + province + "'";
                                    request.query(cmd, (err, result) => {
                                        if (err) {
                                            console.log(err);
                                            serviceResult.code = 500;
                                            serviceResult.status = "Error";
                                            serviceResult.text = "Error: " + err.message;
                                            res.json(serviceResult);
                                        } else {
                                            serviceResult.id = certification;
                                            serviceResult.name = "CIA Rest api";
                                            serviceResult.value = result.recordset;
                                            serviceResult.code = 200;
                                            serviceResult.status = "Success";
                                            serviceResult.text = "Load Success";
                                            res.json(serviceResult);
                                        }
                                    })
                                }
                            })
                        } else {
                            serviceResult.code = 500;
                            serviceResult.status = "Error";
                            serviceResult.text = "Error: Invalid certificate.";
                            res.json(serviceResult);
                        }
                    }
                })
            }
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
module.exports = { car_api };

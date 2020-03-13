var http = require('http');
var app = require('./app');
const config = require('./configuration/config');

var httpMessage = "http";
httpServer = http.createServer(app);

httpServer.listen(config.app.port, function () {
  console.log(httpMessage+' service listening on port ' + config.app.port);
});

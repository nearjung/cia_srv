const config = {
    app: {
      port: 9000,
    },
    connect:{
        user: "sa",
        password: "142536As",
        server: "DESKTOP-IRSA0B8", 
        database: "ciadb",
        connectionTimeout: 300000,
        requestTimeout: 300000,
        pool: {
            idleTimeoutMillis: 300000,
            max: 100
        },
        options: {
            "enableArithAbort": true,
            encrypt: true
        }
    },
    table:{
        census: "[ciadb].[dbo].[Census_10P]",
        carall: "[ciadb].[dbo].[CarAll_10P]",
        customer: "[ciadb].[dbo].[CustomerData]",
        SSDB: "[ciadb].[dbo].[SSDB]"
    },
    log:{
        fileName:"log/Log.log",
        level:"debug"
    }
}
module.exports = config;
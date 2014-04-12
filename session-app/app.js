// Load the http module to create an http server.
var fs     = require('fs'),
    async  = require('async'),
    _      = require('underscore'),
    http   = require('http'),
    Zombie = require('zombie');

var ROOTDIR = "..",
    CONFIGDIR = ROOTDIR + "/config/cnf",
    ENVFILE = CONFIGDIR + "/env";

var _logininfo = null;

var replaceAll = function(s, p, r) {
    var d = true, n = '';

    while (d) {
        n = s.replace(p, r);
        d = n != s;
        s = n;
    }

    return n;
}

var extend = function extend(a, b, excludeInstances) {
    for (var prop in b) {
        if (b.hasOwnProperty(prop)) {
            var isInstanceOfExcluded = false;
            if (excludeInstances)
                for (var i = 0; i < excludeInstances.length; i++)
                    if (b[prop] instanceof excludeInstances[i])
                        isInstanceOfExcluded = true;

            if (typeof b[prop] === 'object' && !isInstanceOfExcluded) {
                a[prop] = a[prop] !== undefined ? a[prop] : {};
                extend(a[prop], b[prop], excludeInstances);
            } else
                a[prop] = b[prop];
        }
    }
};

var getConfig = function(cb) {
    fs.readFile(ENVFILE, function(err, data) {
        if (err) return cb(err);

        data = data.toString("utf8", 0, data.length);

        var envs   = data.indexOf("\r\n") !== -1 ? data.split("\r\n") : data.split("\n"),
            config = {};

        async.eachSeries(envs, function(env, cb) {
            fs.readFile(CONFIGDIR + "/" + env + ".json", function(err, data) {
                data = data.toString('utf8', 0, data.length);
                data = replaceAll(data, /\/\*[.\s\S]*?\//, "")
                           .replace("cnf = {", "{")
                           .replace("};", "}");

                extend(config, JSON.parse(data));

                cb();
            });
        }, function() {
            cb(err, config);
        });

    });
};

var getLoginInfo = function(cb) {
    if (_logininfo) {
        return cb(null, _logininfo);
    } else {
        getConfig(function(err, config) {
            if (err) return cb(err);

            _logininfo = {
                email : config.gw2spidy.auth_email,
                password : config.gw2spidy.auth_password
            };

            cb(null, _logininfo);
        });
    }
};

// Configure our HTTP server to respond with Hello World to all requests.
var server = http.createServer(function (request, response) {
    var handleErr = function(err) {
        response.writeHead(500, {"Content-Type": "text/plain"});
        response.end(err);
    };

    var handleResult = function(s) {
        response.writeHead(200, {"Content-Type": "text/plain"});
        response.end(s);
    };

    browser = new Zombie({userAgent: "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:23.0) Gecko/20100101 Firefox/23.0", debug: true});
    browser.visit("https://account.guildwars2.com/login?redirect_uri=http%3A%2F%2Ftradingpost-live.ncplatform.net%2Fauthenticate%3Fsource%3D%252F&&game_code=gw2", function() {
        getLoginInfo(function(err, logininfo) {
            if (err) return handleErr(err);

            browser
                .fill("email", logininfo.email)
                .fill("password", logininfo.password)
                .pressButton("Sign in", function() {
                    var s = browser.cookies().get('s');
                    browser.close();
                    console.log(s);
                    return handleResult(s);
                });
        });
    });
});

getLoginInfo(function() {});

// Listen on port 8000, IP defaults to 127.0.0.1
server.listen(8000);

// Put a friendly message on the terminal
console.log("Server running at http://127.0.0.1:8000/");
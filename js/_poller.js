var _poller = {};

_poller.server = null;

_poller.polling = false;

_poller.looping = false;

_poller.timeout = 2000;

_poller.eventList = {};

_poller.sessionid = null;

/**
 * Poller initializer
 * @param server
 * @param handle
 */
_poller.init = function (server, handle) {
    _poller.server = server;
    _poller.ajaxer.post({ poller: _poller.session() || 'newer' }, function(datas) {
        var response = JSON.parse(datas);
        if (!response.error) {
            _poller.session(response.token);
            handle();
        }
        else {
            console.error('Server internal error');
        }
    });
};

/**
 * Emit datas to server
 * @param event
 * @param datas
 * @param broadcast
 */
_poller.emit = function (event, datas, broadcast) {
    _poller.ajaxer.post({pollerid: _poller.sessionid, event: event, data: datas, broadcast: broadcast ? 'yes':'no'}, function (response) {
        // something cool can happen
    });
};

/**
 * -------Experimental !------
 * @param event
 * @param datas
 */
_poller.broadcast = function (event, datas) {
    _poller.emit(event, datas, true);
};

/**
 * Handle event from server datas
 * @param event
 * @param handler
 */
_poller.on = function (event, handler) {
    _poller.eventList[event] = handler;
};

_poller.start = function () {
    _poller.looping = setInterval(function () { console.log("polling ...");
        if (!_poller.polling) {
            _poller.ajaxer.post({pollerid: _poller.sessionid, polling:'yes'}, function (datas) {
                datas = JSON.parse(datas);
                for (var x in datas) {
                    _poller.eventList[datas[x].event](datas[x].content);
                }
                _poller.polling = false;
            });
        }
    }, _poller.timeout);
};

_poller.end = function () {
    if (_poller.looping) {
        clearInterval(_poller.looping);
        _poller.looping = null;
    }
};

/**
 * Set or get sessionid from server
 * @param value
 * @returns {*}
 */
_poller.session = function (value) {
    if (value) {
        window.localStorage.setItem('poller', JSON.stringify({token: value}));
        _poller.sessionid = value;
    }
    else {
        var session = window.localStorage.getItem('poller');
        if (session) {
            return JSON.parse(session).token;
        }
        else {
            return false;
        }
    }
};

/**
 * Ajax method for communication to server
 * @param datas
 * @param handle
 */
_poller.ajaxer = {
    post: function (datas, handle) {
        _ajax.post({
            url: _poller.server,
            datas: datas,
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            callback: function (response) {
                handle(response);
            },
            fail: function (err) {
                console.error('Unable to join server');
            }
        });
    }
};

/** ajax **/

/**
 * ajax request object
 * @type {{object: XMLHttpRequest, timeout: number, post: _ajax.post, get: _ajax.get, query: _ajax.query}}
 * @private
 */
var _ajax = {
    object: new XMLHttpRequest(),

    timeout: 3000,

    post: function (options) {
        this.query('post', options.datas, options.url, options.headers, options.callback, options.fail, options.timeout);
    },

    get: function (options) {
        this.query('get', null, options.url, options.headers, options.callback, options.fail, options.timeout);
    },

    query: function (type, datas, url, headers, callback, fail, timeout) {
        this.object.open(type, url);

        if (type.toLowerCase() == 'post') {
            if (headers) {
                for (var header in headers) {
                    this.object.setRequestHeader(header, headers[header]);
                }
            }
            else {
                this.object.setRequestHeader('Content-Type', 'text/plain');
            }

            var stringvalues = [];

            for(var a in datas) {
                stringvalues.push(a+'='+datas[a]);
            }

            datas = stringvalues.join('&');
        }

        this.object.onload = function () {
            if (this.status == 200) {
                if (callback) callback(this.responseText);
            }
            else {
                if (fail) fail("Une erreur s'est produite. CODE: "+this.status, this.status);
            }
        };

        this.object.onerror = function () {
            if (fail) fail("Une erreur s'est produite. CODE: "+this.status, this.status);
        };

        this.object.ontimeout = function () {
            if (fail) fail("Une erreur s'est produite. CODE: TIMEOUT ERROR");
        };

        this.object.timeout = timeout ? timeout : this.timeout;

        // console.log(datas);
        this.object.send(datas);
    }
};
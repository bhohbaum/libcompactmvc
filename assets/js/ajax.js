/**
 * ajax.js
 *
 * @author Botho Hohbaum <bhohbaum@googlemail.com>
 * @package LibCompactMVC
 * @copyright Copyright (c) Botho Hohbaum
 * @license BSD License (see LICENSE file in root directory)
 * @link		https://github.com/bhohbaum/LibCompactMVC
 * @classDescription example:
 * 		<input id="description_<?= $epod->id ?>" type="text"
 * 			class="ajax"
 * 			data-path="<?= $this->lnk("ajaxep", "epod_lang_group", "/" . $epod->id . "/description") ?>"
 * 			data-content="$this.val(JSON.parse(result).description)"
 * 			data-change="$this.val()" />
 * 		<input id="is_global_<?= $epod->id ?>" type="checkbox"
 * 			class="ajax"
 * 			data-path="<?= $this->lnk("ajaxep", "epod_lang_group", "/" . $epod->id . "/is_global") ?>"
 * 			data-content="$this.prop('checked', JSON.parse(result).is_global == 1)"
 * 			data-change="($this.prop('checked') == true) ? 1 : 0" />
 */

var $_ajax = [];

function $ajax() {
	this._responseType = "";
	this._cbok = null;
	this._cberr = null;
	this._data = "";
	this._finished = false;
	this._xhr = new XMLHttpRequest();
};


/**
 * public functions
 */
$ajax.prototype.get = function(url) {
	return this._doRequest('GET', url);
};
$ajax.prototype.post = function(url) {
	return this._doRequest('POST', url);
};
$ajax.prototype.put = function(url) {
	return this._doRequest('PUT', url);
};
$ajax.prototype.del = function(url) {
	return this._doRequest('DELETE', url);
};
$ajax.prototype.reload = function() {
	$(".ajax").each(function() {
		var $this = $(this);
		var praefix = ($this.attr("data-path").substring(0, 4) == "http") ? "" : "/";
		var url = praefix + $this.attr("data-path") + "/" +
							$this.attr("data-param0") + "/" +
							$this.attr("data-param1") + "/" +
							$this.attr("data-param2") + "/" +
							$this.attr("data-param3") + "/" +
							$this.attr("data-param4");
		while (url != url.replace("/undefined", "")) {
			url = url.replace("/undefined", "");
		}
		var content = $this.attr("data-content");
		new $ajax().ok(function(result) {
			var cmd = (content.substring(0, 6) == "$this.") ? content : "$this." + content;
			try {
				eval(cmd);
			} catch (e) {
				console.log(e);
			}
		}).get(url);
	});
};
$ajax.prototype.data = function(str) {
	this._data = str;
	return this;
};
$ajax.prototype.responseType = function(type) {
	this._responseType = type;
	return this;
};
$ajax.prototype.ok = function(cb) {
	this._cbok = cb;
	return this;
};
$ajax.prototype.err = function(cb) {
	this._cberr = cb;
	return this;
};
$ajax.prototype.init = function() {
	$(".ajax").each(function() {
		var $this = $(this);
		var praefix = ($this.attr("data-path").substring(0, 4) == "http") ? "" : "/";
		var url = praefix + $this.attr("data-path") + "/" +
							$this.attr("data-param0") + "/" +
							$this.attr("data-param1") + "/" +
							$this.attr("data-param2") + "/" +
							$this.attr("data-param3") + "/" +
							$this.attr("data-param4");
		while (url != url.replace("/undefined", "")) {
			url = url.replace("/undefined", "");
		}
		var content = $this.attr("data-content");
		events = ["keydown", "keypress", "keyup",
				"click", "dblclick",
				"mousedown", "mouseup", "mouseover", "mousemove", "mouseout",
				"dragstart", "drag", "dragenter", "dragleave", "dragover", "drop", "dragend",
				"load", "unload", "abort", "error", "resize", "scroll",
				"select", "change", "submit", "reset", "focus", "blur",
				"focusin", "focusout"];
		for (var i = 0; i < events.length; i++) {
			if ($this.attr("data-" + events[i]) != null) {
				$this.on(events[i], function(event) {
					var cmd = 'var data = ' + $this.attr("data-" + event.type);
					try {
						eval(cmd);
						var ajaxp = new $ajax();
						ajaxp.data("&data=" + encodeURIComponent(data));
						ajaxp.ok(function(result) {
							var cmd = (content.substring(0, 6) == "$this.") ? content : "$this." + content;
							try {
								eval(cmd);
							} catch (e) {
								console.log(e);
							}
						});
						ajaxp.err(function() {
							ajaxp.post(url);
						});
						ajaxp.post(url);
					} catch (e) {
						console.log(e);
					}
				});
			}
		}
		var ajaxg = new $ajax();
		ajaxg.ok(function(result) {
			var cmd = (content.substring(0, 6) == "$this.") ? content : "$this." + content;
			try {
				eval(cmd);
			} catch (e) {
				console.log(e);
			}
		});
		ajaxg.err(function(result) {
			ajaxg.get(url);
		});
		ajaxg.get(url);
		$this.removeClass("ajax");
	});
};
$ajax.prototype.whenAllLoaded = function(func) {
	if (Object.keys($_ajax).length > 0) {
		setTimeout(function() {
			new $ajax().whenAllLoaded(func);
		}, 100);
	} else {
		try {
			func();
		} catch (e) {
			console.log(e);
		}
	}
};

/**
 * internal functions
 */
$ajax.prototype._response = function(response) {
	var res = null;
	if (response.currentTarget.hasOwnProperty('response')) {
		res = response.currentTarget.response;
	} else if (response.currentTarget.hasOwnProperty('responseText')) {
		res = response.currentTarget.responseText;
	} else { // FF hack
		res = response.currentTarget.response;
	}
	return res;
};
$ajax.prototype._callHandler = function(url, response, rData) {
	console.log(url + ": " + response.target.status);
	this._finished = true;
	try {
		if (response.target.status == 200) {
			if (this._cbok) {
				this._cbok(rData);
			};
		} else {
			if (this._cberr) {
				this._cberr(rData);
			};
		}
	} catch (e) {
		console.log(e);
	}
	delete $_ajax[url];
};
$ajax.prototype._doRequest = function(method, url, retry) {
	retry = (typeof retry !== 'undefined') ? retry : 0;
	var rnd = Math.round(Math.random() * 1000000000);
	if (!retry) url = url + "#req" + rnd;
	this._xhr.open(method, url, true);
	this._xhr.responseType = this._responseType;
	this._xhr.onload = function(response) {
		$_ajax[url]._callHandler(url, response, $_ajax[url]._response(response));
	};
	this._xhr.send(this._data);
	cmd = "($_ajax.hasOwnProperty('" + url + "')) ? $_ajax['" + url + "']._checkRequest('" + method + "', '" + url + "', " + retry + ") : null";
	delay = (retry + 5) / 5 * (15 + Object.keys($_ajax).length / 5);
	ms = 1000 * delay;
	console.log("Starting new timer (delay " + ms + "ms): " + cmd);
	this._timer = setTimeout(cmd, ms);
	this._finished = false;
	$_ajax[url] = this;
	console.log($_ajax);
	return this;
};
$ajax.prototype._checkRequest = function(method, url, retry) {
	retry = (typeof retry !== 'undefined') ? retry + 1 : 0;
	if (!this._finished) {
		console.log("Request timeout! Retrying: " + url);
		this._xhr.abort();
		this._doRequest(method, url, retry);
	}
};


/***********************************************************************************************************************************************
 * Websocket client
 **********************************************************************************************************************************************/
function $ws(url) {
	this.url = null;
	this.socket = null;
	this.handlers = [];
	if (url != undefined)
		this.id = url.substring(url.length - 32, url.length);
	else
		this.id = null;
}

/**
 * public functions
 */
$ws.prototype.init = function(url) {
	this.url = url;
	if (url != undefined)
		this.id = url.substring(url.length - 32, url.length);
	try {
		this.socket = new WebSocket(this.url, "event-dispatch-protocol");
		console.log('WS startup - status ' + this.socket.readyState);
		this.socket.ws = this;
		this.socket.onopen = function(msg) {
			console.log("WS connected - status " + this.readyState);
		};
		this.socket.onmessage = function(msg) {
			console.log("WS received data: " + msg.data);
			this.ws._run_handlers(msg.data);
		};
		this.socket.onclose = function(msg) {
			console.log("WS disconnected - status " + this.readyState);
		};
	} catch (ex) {
		console.log(ex);
	}
	return this;
};

$ws.prototype.add_handler = function(handler) {
	this.handlers[this.handlers.length] = handler;
	return this;
};

$ws.prototype.set_handler = function(event, handler) {
	if (!Number.isInteger(event)) {
		this.handlers[event] = handler;
	}
	return this;
};

$ws.prototype.send = function(msg) {
	try {
		this.socket.send(this.id + " " + msg);
	} catch (ex) {
		console.log(ex);
	}
	return this;
}

/**
 * internal functions
 */
$ws.prototype._run_handlers = function(data) {
	for (idx in this.handlers) {
		if (Number.isInteger(parseInt(idx))) {
			this.handlers[idx](data);
		} else {
			if (data.substring(33, 33 + idx.length) == idx) {
				this.handlers[idx](data.substring(33 + idx.length + 1, data.length));
			}
		}
	}
};


$(document).ready(function() {
	new $ajax().init();
});


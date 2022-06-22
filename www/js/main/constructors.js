const constructors = {
	loading: function () {

	},
	triggers: function (options, optionsEvents) {
		let handlers = [];

		this.bind = function (event, callback) {
			handlers[handlers.length] = [event, callback];
			return this;
		};
		this.unbind = function (event, callback) {
			event.split(' ').forEach((event) => {
				for (let i = handlers.length - 1; i >= 0; i--) {
					if (handlers[i][0] === event && (undefined === callback || handlers[i][1] === callback))
						handlers.splice(i, 1);
				}
			});
			return this;
		};
		this.trigger = function (event) {
			let args = [];
			for (let i = 1; i < arguments.length; i++) {
				args[args.length] = arguments[i];
			}
			for (let i = 0; i < handlers.length; i++) {
				if (handlers[i][0] !== event)
					continue;
				if (false === handlers[i][1].apply(this, args))
					return false;
			}
			return this;
		};
		this.bindOptions = function (options, optionsEvents) {
			if (!options)
				return;
			for (let i = 0; i < optionsEvents.length; i++) {
				if (typeof options[optionsEvents[i]] !== 'function')
					continue;
				this.bind(optionsEvents[i], options[optionsEvents[i]]);
			}
		};
	}
};
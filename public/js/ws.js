var ws = {
	socket			: {},
	host			: "ws://localhost:9503",
	user			: "",
	message 		: "",
	selfmessage 	: "",
	callbackRooms	: {},
	callbackMessage : {},


	// inicializa conexao websocket
	connect : function(host) {
		
		var self = this;

		try {
			this.socket = new WebSocket(this.host);
			
			// inicializa sessao
			this.socket.onopen = function(e) {
						
				var newMsg = {
					requestType: "init",
					user: self.user,
					handshakeSession: "",
				};

				if(localStorage.comp_chat_session){
					newMsg.handshakeSession = localStorage.comp_chat_session;
				}
				
				self.socket.send( JSON.stringify(newMsg));
			};

			// gerencia comunicacao
			this.socket.onmessage = function(msg) {
						
				var decodedData = JSON.parse(msg.data);

				switch(decodedData.requestType){
					// define sessao
					case "init":
						localStorage.comp_chat_session = decodedData.handshakeSession
						break;
					
					// carrega salas
					case "rooms":
						self.rooms(decodedData.rooms);
							break;

					// gerencia mensagens
					default:
						console.log(decodedData.msg);
						self.message_view(self.message.replace(/%msg%/g, decodedData.msg))
						break;

				}
			};




		} catch (exception) {
			// message("<p>Error" + exception);
		}
	},
	// carrega salas
	rooms: function(rooms) {
		this.callbackRooms(rooms);
	},
	// gerencia msg visual
	message_view: function(msg) {
		this.callbackMessage(msg);
	},
	send: function(msg){
		
		var newMsg = {
			requestType: "message",
			user: this.user,
			message: msg,
			handshakeSession: "",
		};

		if (localStorage.comp_chat_session) {
			newMsg.handshakeSession = localStorage.comp_chat_session;
		}
		console.log("#################################");
		console.log("local>>>" + newMsg.handshakeSession);

		this.socket.send(JSON.stringify(newMsg));
		this.message_view(this.selfmessage.replace(/%msg%/g, msg));
	},
	// desconecta sessao
	disconnect:  function(msg){
		this.socket.close();
	},

};

var ws = {
	socket			: {},
	host			: "ws://localhost:9503",
	user			: "",
	message 		: "",
	selfmessage 	: "",
	callbackRooms	: {},
	callbackMessage : {},
	callbackHistory : {},


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
						self.callbackRooms(decodedData.rooms);
							break;

					// carrega historico
					case "history":
							self.callbackHistory(decodedData.history);
							break;

					// gerencia mensagens
					default:
						console.log(decodedData.msg);
						self.callbackMessage(self.message.replace(/%msg%/g, decodedData.msg))
						break;

				}
			};
			
		} catch (exception) {
			// message("<p>Error" + exception);
		}
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
		this.callbackMessage(this.selfmessage.replace(/%msg%/g, msg));
	},
	// desconecta sessao
	disconnect:  function(msg){
		this.socket.close();
	},

};

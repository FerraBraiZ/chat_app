var __class  = function(obj){
	$.each(obj,function(key,value) {
		window[key] = $.each(function(){
			
			var vars    = { args  : arguments};
			var methods = { __main      :function(){},
							__construct :function(){
							var args = "";
							var args_len = this.args.length;
							for(x=1;x<=args_len;x++)
								args += "this.args["+(x-1)+"]"+(x%args_len>0?",":"");
							eval("this.construct("+args+");");
							}}; 
			
			var all   = $.extend( methods , vars    );
				all   = $.extend( all   , value   );
			
			$.extend( this,all,$.each(function(){ new Object()}));
			
			this.__main();
			
			if(typeof this.construct == "function"){
				this.__construct.__proto__ = this.construct.prototype;
				this.__construct();
			}
		});
	});
}

__class ({'Ws' :
{
	
	socket 				: {},
	host				: "ws://localhost:9503",
	user				: "",
	message 			: "",
	selfmessage 		: "",
	callbackRooms		: {},
	callbackMessage 	: {},
	callbackHistory 	: {},
	callbackHistoryView : {},

	/**
	 * Metodo do tipo contrutor.
	 */
	construct: function()
	{
		var self = this;
		// inicializa conexao websocket
		self.socket = new WebSocket(this.host);

		// inicializa sessao
		self.socket.onopen = function(e) {
			self.onOpen(e);
		};

		// gerencia comunicacao
		self.socket.onmessage = function(msg) {
			self.onMessage(msg);		
		};  

	},
	// inicializa sessao
	onOpen: function(e) 
	{
		var newMsg = {
			requestType: "init",
			user: this.user,
			handshakeSession: "",
		};

		if(localStorage.comp_chat_session){
			newMsg.handshakeSession = localStorage.comp_chat_session;
		}
		
		this.socket.send( JSON.stringify(newMsg));
	},

	// gerencia comunicacao
	onMessage: function(msg)
	{
		var decodedData = JSON.parse(msg.data);

		switch(decodedData.requestType){
			
			// define sessao
			case "init":
				localStorage.comp_chat_session = decodedData.handshakeSession
				break;
			
			// carrega salas
			case "rooms":
				this.callbackRooms(decodedData.rooms);
				break;

			// carrega historico
			case "history":
				this.callbackHistory(decodedData.history);
				break;
			
			// view historico
			case "historyView":
				this.callbackHistoryView(decodedData.history);
				break;

			// gerencia mensagens
			default:
				this.callbackMessage(this.message.replace(/%msg%/g, decodedData.msg))
				break;
		}
	},
	// envia msg para o servidor socket
	send: function(msg, current_room) 
	{
		var newMsg = {
			requestType: "message",
			user: this.user,
			message: msg,
			current_room: current_room,
			handshakeSession: "",
		};

		if (localStorage.comp_chat_session) {
			newMsg.handshakeSession = localStorage.comp_chat_session;
		}
		
		this.socket.send(JSON.stringify(newMsg));
		this.callbackMessage(this.selfmessage.replace(/%msg%/g, msg));
	},
	// faz a requisicao de listagem das salas
	viewRoom: function(room_id)
	{
		var newMsg = {
			requestType: "viewRoom",
			user: this.user,
			room_id: room_id,
			handshakeSession: localStorage.comp_chat_session,
		};

		this.socket.send(JSON.stringify(newMsg));
	},
	// desconecta sessao
	disconnect:  function(msg)
	{
		this.socket.close();
	},
}});

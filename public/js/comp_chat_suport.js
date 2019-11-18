$(document).ready(function() {
	if (!("WebSocket" in window)) {
		
		$(
			"<p>Ah, não, você precisa de um navegador que suporte WebSockets. E se</p>"
		).appendTo("#container");
	} else {
		
		connect();

		

		function connect() {
			var socket;
			var session;
			var host = "ws://localhost:9503";

			try {
				var socket = new WebSocket(host);

				socket.onopen = function(e) {
					if (localStorage.comp_chat_session) {
						socket.send(
							JSON.stringify({
								handshake_session:
									localStorage.comp_chat_session,
								user:
									"suport",
								
							})
						);
					}
				};

				socket.onmessage = function(msg) {
					// console.log(msg.data);
					var data = JSON.parse(msg.data);

					console.log(data);
					if(typeof data.rooms === 'undefined'){
						// console.log("server:" + data.session);
						// console.log("local:" + localStorage.comp_chat_session);

						if (data.session) {
							if (data.session != localStorage.comp_chat_session) {
								localStorage.comp_chat_session = data.session;
							}
						} else {
							message(
								'<li class="msg_container msg_guest">' +
									data.msg +
									'<img src="images/g.svg" alt="g" class="avatar"></li>'
							);
						}
					}else{
						consulta_rooms(data.rooms);
					}
				};
			} catch (exception) {
				// message("<p>Error" + exception);
			}

			
			function consulta_rooms(rooms) {
				
				$(".comp_chat .cli_list li").remove();
				$(".comp_chat .cli_list").append('<li class="separator"><hr class="separator_hr" /></li>');
				
				
				rooms.forEach((v, i) => $(".comp_chat .cli_list").append('<li class="msg_container"><img src="images/g.svg" alt="g" class="avatar"> Guest '+v.id+'</li>'));
			
			}

			function send() {
				var text = $("#text").val();

				if (text == "") {
					// message('<p class="warning">Please enter a message');
					return;
				}
				try {
					newMsg = {
						user: "suport",
						message: text,
						session: ""
					};

					if (localStorage.comp_chat_session) {
						newMsg.session = localStorage.comp_chat_session;
					}
					console.log("#################################");
					console.log("local>>>" + newMsg.session);

					socket.send(JSON.stringify(newMsg));
					message(
						'<li class="msg_container"><img src="images/s.svg" alt="s" class="avatar_suport">' +
							text +
							"</li>"
					);
				} catch (exception) {
					// message('<p class="warning">');
				}
				$("#text").val("");
			}

			function message(msg) {
				$(".comp_chat .msg_list").append(msg);
				$(".comp_chat .caixa-msg").scrollTop($(".msg_list").height());
			}

			$("#text").keypress(function(event) {
				if (event.keyCode == "13") {
					send();
					obj.play();
				}
			});

			$("#disconnect").click(function() {
				socket.close();
			});

			
		}
		var obj = document.createElement("audio");
		obj.src = "/sounds/chime.mp3";
		obj.volume = 0.1;
		obj.autoPlay = false;
		obj.preLoad = true;
		obj.controls = true;
	}

	$(".comp_chat .chat-btn").on("click", function() {
		$(".content_chat", ".comp_chat").slideToggle("fast");
		$(".glyphicon", this).toggleClass("glyphicon-comment glyphicon-remove");
	});
});

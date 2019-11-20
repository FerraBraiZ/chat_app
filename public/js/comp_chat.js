$(document).ready(function() {
	$.getScript('/js/ws.js', function() {
		$("#text").focus();
		
		
		var wsCustom = {
			user 		 		: "guest",
			message		 		: '<li class="msg_container msg_suport">%msg%<img src="images/s.svg" alt="s" class="avatar_suport"></li>',
			selfmessage			: '<li class="msg_container"><img src="images/g.svg" alt="g" class="avatar">%msg%</li>',
			callbackMessage 	: function(msg){
				$(".comp_chat .msg_list").append(msg);
				$(".comp_chat .caixa-msg").scrollTop($(".msg_list").height());
			},
			callbackHistory 	: function(history){
				
				history.forEach(function(element){
					var decodedData = JSON.parse(element);

					switch(decodedData.user){
						
						case "guest":
								ws.callbackMessage(ws.selfmessage.replace(/%msg%/g, decodedData.message));
							break;

						case "suport":
								ws.callbackMessage(ws.message.replace(/%msg%/g, decodedData.message));
							break;
					}
				});
			},
		}

		// inicializa conexao websocket
		var ws   = new Ws(wsCustom);
		
		// enviando msg
		$("#text").keypress(function(event) {
			if (event.keyCode == "13") {
				var msg = $(this).val();

				if (msg == "") {
					$(this).addClass("alert alert-danger");
					return;
				}
				ws.send(msg,current_room);

				$(this).val("");
				obj.play();
			}else{
				$(this).removeClass("alert alert-danger");
			}
		})
		
		$(".comp_chat .chat-btn").on("click", function() {
			$(".content_chat", ".comp_chat").slideToggle("fast");
			$(".glyphicon", this).toggleClass("glyphicon-comment glyphicon-remove");
		});

		var current_room = 0;
		
		var obj = document.createElement("audio");
			obj.src = "/sounds/chime.mp3";
			obj.volume = 0.1;
			obj.autoPlay = false;
			obj.preLoad = true;
			obj.controls = true;
	
	});
});

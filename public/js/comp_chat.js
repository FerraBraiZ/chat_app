$(document).ready(function() {
	$.getScript('/js/ws.js', function() {
		$("#text").focus();
		
		ws.user 		 	= "guest";
		ws.message		 	= '<li class="msg_container msg_suport">%msg%<img src="images/s.svg" alt="s" class="avatar_suport"></li>';
		ws.selfmessage		= '<li class="msg_container"><img src="images/g.svg" alt="g" class="avatar">%msg%</li>'
		ws.callbackMessage 	= function(msg){
			$(".comp_chat .msg_list").append(msg);
			$(".comp_chat .caixa-msg").scrollTop($(".msg_list").height());
		}


		// conectando com o servidor websocket
		ws.connect();

		// enviando msg
		function send() {
			var msg = $("#text").val();

			if (msg == "") {
				// message('<p class="warning">Please enter a message');
				return;
			}
			ws.send(msg);

			$("#text").val("");
		}


		$("#text").keypress(function(event) {
			if (event.keyCode == "13") {
				send();
				obj.play();
			}
		});

		$(".comp_chat .chat-btn").on("click", function() {
			$(".content_chat", ".comp_chat").slideToggle("fast");
			$(".glyphicon", this).toggleClass("glyphicon-comment glyphicon-remove");
		});

		var obj = document.createElement("audio");
			obj.src = "/sounds/chime.mp3";
			obj.volume = 0.1;
			obj.autoPlay = false;
			obj.preLoad = true;
			obj.controls = true;
	
	});
});

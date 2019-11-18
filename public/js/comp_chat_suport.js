$(document).ready(function() {
	$.getScript('/js/ws.js', function() {
		$("#text").focus();

		ws.user 		 = "suport";
		ws.message		 = '<li class="msg_container msg_guest">%msg%<img src="images/g.svg" alt="g" class="avatar"></li>';
		ws.selfmessage	 = '<li class="msg_container"><img src="images/s.svg" alt="s" class="avatar_suport">%msg%</li>';
		ws.callbackRooms = function (rooms) {
			$(".comp_chat .cli_list li").remove();
			$(".comp_chat .cli_list").append('<li class="separator"><hr class="separator_hr" /></li>');
			
			rooms.forEach((v, i) => $(".comp_chat .cli_list").append('<li class="msg_container"><img src="images/g.svg" alt="g" class="avatar"> Guest '+v.id+'</li>'));
		}
		ws.callbackMessage 	= function(msg){
			$(".comp_chat .msg_list").append(msg);
			$(".comp_chat .caixa-msg").scrollTop($(".msg_list").height());
		}
			

		ws.connect();

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

		$("#disconnect").click(function() {
			socket.close();
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

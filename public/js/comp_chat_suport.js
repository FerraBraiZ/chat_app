$(document).ready(function() {
	$.getScript('/js/ws.js', function() {
		$("#text").focus();

		var wsCustom = {
			user 		 : "suport",
			message		 : '<li class="msg_container msg_guest">%msg%<img src="images/g.svg" alt="g" class="avatar"></li>',
			selfmessage	 : '<li class="msg_container"><img src="images/s.svg" alt="s" class="avatar_suport">%msg%</li>',

			callbackRooms : function (rooms) {
				$(".comp_chat .cli_list li").remove();
				$(".comp_chat .cli_list").append('<li class="separator"><hr class="separator_hr" /></li>');
				
				rooms.forEach((v, i) => $(".comp_chat .cli_list").append('<li class="msg_container" room_id="'+v.id+'" room_name="Guest '+v.id+'"><img src="images/g.svg" alt="g" class="avatar"> Guest '+v.id+'</li>'));
			},

			callbackMessage 	: function(msg, room_id){
				
				if(current_room == room_id){
					$(".comp_chat .msg_list").append(msg);
					$(".comp_chat .caixa-msg").scrollTop($(".msg_list").height());
				}
			},

			callbackMessageView 	: function(msg){
				$(".comp_chat .msg_list").append(msg);
				$(".comp_chat .caixa-msg").scrollTop($(".msg_list").height());
			},
			
			callbackHistoryView 	: function(history){
				
				
				history.forEach(function(element){
					var decodedData = JSON.parse(element);

					switch(decodedData.user){
						
						case "guest":
								ws.callbackMessageView(ws.message.replace(/%msg%/g, decodedData.message));
							break;

						case "suport":
								ws.callbackMessageView(ws.selfmessage.replace(/%msg%/g, decodedData.message));
							break;
					}
				});
			},
		}
			
		// inicializa conexao websocket
		var ws   = new Ws(wsCustom);

		// enviando msg
		$("#text",".comp_chat").keypress(function(event) {
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

		$(".comp_chat")
			.on("click", ".chat-btn", 		function() {
				$(".content_chat", ".comp_chat").slideToggle("fast");
				$(".glyphicon", this).toggleClass("glyphicon-comment glyphicon-remove");
			})
			.on('click', '.msg_container', function() {
				$(".comp_chat .caixa-msg .msg_list li").remove();
				$(".comp_chat .caixa-msg .msg_list").append('<li class="separator"><hr class="separator_hr" /></li>');
				
				current_room = $(this).attr("room_id");
				ws.viewRoom(current_room);
				$(".header h2",".comp_chat").text($(this).attr("room_name"));

				$("#text",".comp_chat").removeAttr("disabled");
				$(".comp_chat .caixa-msg").css('background-image', 'none');
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

/*
 * Copyright 2010 - Robson Dantas <biu.dantas@gmail.com>
 * 
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

var Orkut = function(){};

Orkut.prototype.hasCaptcha=false;
Orkut.prototype.selUser = {};

// load function
Orkut.prototype.load = function () {

	$("#msg").attr("disabled",true);

	$("#call").change(function() {
		$("#msg").attr("disabled",true);

		if($(this).val()=="sendMessage"){
			$("#msg").removeAttr("disabled");
		}
	});
}

// when clicking in a button, execute this
Orkut.prototype.click = function() {

	if($("#call").val()=="getUserInfo") {
		this.showUsers();
	}

	if($("#call").val()=="sendMessage") {
		var uids = "";
		$.each(this.selUser, function(index,value) {
			if(value=="1")
				uids= (uids=="" ? index : uids+ "," + index);
		});

		if(uids=="")
			alert("Please selected some users");
		else
			this.sendMessage(uids, $("#msg").val());
	}

	if($("#call").val()=="listScrap") {	
		this.listScraps();
	}
		
}

Orkut.prototype.showUsers = function() {

	// another way to solve it ?
	var orkut = this;

	$.ajax({
		type:'POST',
		url:'../json/list_friends.php',
		dataType: 'json',
		success: function(json) {

			var result = json[0]['id'];

			if(result=="1") 
				alert("error: " + json[0]['message']);
			else if(result=="0") {

				var data = json[1];
		
				var uid = data['self']['data']['id'];
				var firstName = data['self']['data']['name']['givenName'];
				var lastName = data['self']['data']['name']['familyName'];
				var imageSmall = data['self']['data']['thumbnailUrl'];
			
				// print
				orkut.printUser(uid, firstName, lastName, imageSmall);

				$.each(data['friends']['data']['list'],function(i, item){

					uid = item['id'];
					firstName = item['name']['givenName'];
					lastName = item['name']['familyName'];
					imageSmall = item['thumbnailUrl'];

					orkut.printUser(uid, firstName, lastName, imageSmall);

				});
			}
			
		},
		error: function(msg) {
			alert('error calling');
		}
	});

}


Orkut.prototype.sendMessage = function(uids, amessage) {

	var extraData="";

	// again, another way to solve it ?
	var orkut = this;

	if(this.hasCaptcha)
	{
		var textcaptcha = $("#textcaptcha").val();
		var tokencaptcha = $("#tokencaptcha").val();

		extraData = "captchaToken=" + tokencaptcha + "&captchaValue=" + textcaptcha + "&";
	}

	$.ajax({
		type: 'POST',
		url: '../json/send_scrap.php',
		data: extraData + 'uids=' + uids + '&message=' + encodeURIComponent(amessage),
		dataType: 'json',
		success: function(json) {
	
			var result = json[0]['id'];

			if(result=="1") 
				alert("error: " + json[0]['message']);		
			// captcha
			else if(result == "2" ) {
				var image = json[0]['captchaUrl'];
				var token = json[0]['captchaToken'];
				var html='<img src="' + image + '">' +
					  'Captcha <input type="text" id="textcaptcha"><input type="text" id="tokencaptcha" value="' + token + '">';

				$('#captcha').html(html);
				orkut.hasCaptcha=true;

			}
			else {
				alert('message sent');
			}
			
		},
		error: function(msg) {
			alert('error calling');
		}
	});		

	if(this.hasCaptcha){ 
		$("#captcha").html("");
		hasCaptcha=false;
	}
}

Orkut.prototype.listScraps = function(){

	// another way to solve it ?
	var orkut = this;

	$.ajax({
		type:'POST',
		url:'../json/list_scraps.php',
		dataType: 'json',
		success: function(json) {

			var result = json[0]['id'];

			if(result=="1") 
				alert("error: " + json[0]['message']);
			else if(result=="0") {

				$("#result").html("");

				var body="";
				var from="";
				var uid="";
				var image="";

				var data = json[1];

				$.each(data['scraps']['data']['list'],function(i, item){

					body = item['body'];
					from = item['fromUserProfile']['name']['givenName'];
					image = item['fromUserProfile']['thumbnailUrl'];
					uid = item['fromUserProfile']['id'];
					
					orkut.printScrap(uid, image, from, body);

				});
			}


		}
	});
}
			
Orkut.prototype.printUser = function(uid, firstName, lastName, image) {

	var html = "<div id='"+uid+"' class='user'>" +
					"<div class='cont'><img src='" + image + "'></div>" +
					"<p><a href='javascript:orkutApi.sel(\"" + uid + "\");'>UID: " + uid + "</a></p>" +
					"<p>Name: " + firstName + " " + lastName + "</p>" +
				"</div>";
		
	$("#result").html( $("#result").html() + html);

}

Orkut.prototype.printScrap = function(uid, image, givenName, body) {

	var html = "<div id='"+uid+"' class='user'>" +
					"<div class='cont'><img src='" + image + "'></div>" +
					"<p>Name: "+givenName+" - UID: " + uid + "</p>" +
					"<p>Msg: " + body + "</p>" +
				"</div>";
		
	$("#result").html( $("#result").html() + html);

}

Orkut.prototype.sel = function(user) {

	if(this.selUser[user]) {
		this.selUser[user] = "";
		$("#" + user).css({"background-color": "#fff"});
	}
	else {
		this.selUser[user]="1";
		$("#" + user).css({"background-color": "#efefef"});
	}

}

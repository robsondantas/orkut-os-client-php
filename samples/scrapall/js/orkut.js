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

var Orkut = function(){

    this.hasCaptcha=false;
    this.userCaptched="";
    this.friendsArray = [];
    this.message="";
    this.tindex = 0;
    this.sent = 0;
    
    this.FRIENDS_PER_SEND=200;
    this.AJAX_TIMEOUT = 60 * 1000; //seconds


    /*
    * First method to be called. Gets an array of friends and a message (can be plain text, html, etc)
    */
    this.load = function (friends, message) {

        if(jQuery.trim(friends)=='')
            alert('Please select at least one friend');
        else if(jQuery.trim(message)=='')
            alert('No message written');
        else {
        
            this.createArray(friends);

            this.message = message;
            this.tindex = 0;
            this.sent = 0;

            // executa ao iniciar
            this.send();
        }
    }

    /*
    * Split friends in arrays containing the number defined in FRIENDS_PER_SEND
    * Suppose FRIENDS_PER_SEND is 50, and I have 800 friends, 16 array items will be created, 50 friends each one
    */
    this.createArray = function(friends) {

        var tmp = [];
        var tmpArray = friends.split(',');

        
        var i=1;
        for(var w=0; w < tmpArray.length; w++) {

            if(i % this.FRIENDS_PER_SEND == 0 || (w+1) == tmpArray.length ) {

                i=0;
                tmp.push(tmpArray[w]);

                // push to the class property
                this.friendsArray.push(tmp.join(','));

                // cleans the temporary
                tmp = [];
            }
            else
                tmp.push(tmpArray[w]);

            i++;
        }

        // cleans
        tmp = [];

    }

    /*
    * Control function. Determine the amount of items to be sent
    */
    this.send = function(){

        if(this.tindex <= this.friendsArray.length-1) {
            this.sendMessage(this.friendsArray[this.tindex]);	
            this.tindex++;
        }
        else {
            $("#info #de").text("messages sent");
            $("#info #total").text("");

            alert('Process ended!');
        }
    }

    /*
    * If a message gets catpcha, queue again.
    */
    this.reSend = function() {
        this.sendMessage(this.userCaptched);	
    }

    /**
    * Tells the amount of messages sent
    */
    this.updateStatus = function(uids, naoEnviadas) {


        // # of ids we got
        var totalIds = decodeURIComponent(uids).split(',').length;

        // # of to be sent
        var enviadas = totalIds - naoEnviadas;
        
        // update if the number if bigger than 0
        if(enviadas > 0)	
            $("#info #de").text( parseInt($("#info #de").text()) + enviadas );

    }

    /**
    * This function does the job, sending to the json wrapper.
    */
    this.sendMessage = function(uids) {

        var extraData="";
        var orkut = this;

        if(this.hasCaptcha)
        {
            var textcaptcha = $("#textcaptcha").val();
            var tokencaptcha = $("#tokencaptcha").val();

            extraData = "captchaToken=" + tokencaptcha + "&captchaValue=" + textcaptcha + "&";
        }

        $.ajax({
            type: 'POST',
            url: 'send_scrap.php',
            data: extraData + 'uids=' + encodeURIComponent(uids) + '&message=' + encodeURIComponent(orkut.message),
            dataType: 'json',
            timeout: orkut.AJAX_TIMEOUT, //30sec
            success: function(json) {

                var result = json[0]['id'];

                if(result=="1") 
                    alert("Error occured: " + json[0]['message']);		
                // captcha
                else if(result == "2" ) {
                    var image = json[0]['captchaUrl'];
                    var token = json[0]['captchaToken'];
                    var html='<img src="' + image + '">' +
                          '<br /><input type="text" id="textcaptcha"><input type="hidden" id="tokencaptcha" value="' + token + '">' +
                          '<input type="button" onclick="orkut.reSend()" value="ok">';

                    $('#captcha').html(html);
                    orkut.hasCaptcha=true;
                    orkut.userCaptched = json[0]['uids'];

                    orkut.updateStatus(uids, json[0]['uids']);
        
                }
                else {

                    // cleans
                    orkut.userCaptched="";
                    orkut.hasCaptcha=false;

                    orkut.updateStatus(uids, json[0]['uids']);

                    // calls the queue manager to send again.
                    // maybe placing a small timeout here ?
                    orkut.send();

                }
                
            },
            error: function(msg) {
                alert('Error while communicating!');
            }
        });		

        if(this.hasCaptcha){ 
            $("#captcha").html("");
            hasCaptcha=false;
        }
    }
}

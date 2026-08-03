/*
 * jQuery webSpeech 1.0.1
 * 
 * [webSpeech]
 * 
 * NO COPYRIGHTS OR LICENSES. DO WHAT YOU LIKE.
 * 
 * http://digipiph.com
 * 
 * File generated: Thur Mar 22 12:18 EST 2013
 */
(function($){

  //START $.fn.webSpeech
  $.fn.webSpeech = function (instanceSettings) {
	
    //settings
    var defaultSettings = {
      button      : 'webSpeech_button', //id of the initiating button
      lang        : 'en-US',
      format      : 'input',            //input, textarea, html
      build       : 'append',           //append, overwrite
      startImg    : site_base_url+'themes/default/images/mic.gif',
      animateImg  : site_base_url+'themes/default/images/mic-animate.gif',
      errorImg    : site_base_url+'themes/default/images/mic-slash.gif',
      showHelp    : true,
      showInterim : true
    };

    /***********************
    //General Configuring (Global Variables)
    ***********************/

    // get the defaults or any user set options
    var settings = $.extend(defaultSettings, instanceSettings);

    var obj = this;
    var final_transcript = '';
    var recognizing = false;
    var ignore_onend;
    var start_timestamp;
    var two_line = /\n\n/g;
    var one_line = /\n/g;
    var first_char = /\S/;

    if (!('webkitSpeechRecognition' in window)) {
      upgrade();
    } 
    else {

      //START: Construct webSpeech

      //set a unique ID for the webSpeech instance
      var id = (window.webSpeechID) ? parseInt(window.webSpeechID + 1) : 1;
      window.webSpeechID = id;

      //obj.wrap('<div id="webSpeech_'+ id +'" style="max-width: '+obj.width()+'px;" class="webSpeech_container" />'); //remove max-width
	  obj.wrap('<div id="webSpeech_'+ id +'" class="webSpeech_container" />');
      $('#webSpeech_'+ id).prepend('<div style="clear: both;"></div>');
      $('#webSpeech_'+ id).prepend('<div id="webSpeech_'+ id +'_info" class="webSpeech_info"></div>');
      $('#webSpeech_'+ id).append('<div style="clear: both;"></div>');
      $('#webSpeech_'+ id).append('<span id="webSpeech_'+ id +'_final_span" class="webSpeech_final"></span>');
      $('#webSpeech_'+ id).append('<span id="webSpeech_'+ id +'_interim_span" class="webSpeech_interim"></span>');
      $('#webSpeech_'+ id).append('<div style="clear: both;"></div>');
      
      //END: Construct webSpeech

      //START: Configure the button
      if ($('#'+settings.button).length == 0) {
        alert('No button found for initiating webSpeech');
        return;
      }
      else {
        $('#'+settings.button).click(function() {
          webSpeech_startButton(id, event);
        });
        $('#'+settings.button).html('<img id="webSpeech_'+ id +'_img_mic" src="'+ settings.startImg +'" alt="Start">');
      }
      //END: Configure the button

      showInfo(id, 'info_start');

      //START: recognition
      var recognition = new webkitSpeechRecognition();
      recognition.continuous = true;
      recognition.interimResults = true;

      recognition.onstart = function() {
        recognizing = true;
        showInfo(id, 'info_speak_now');
        $('#webSpeech_'+ id +'_img_mic').attr('src', settings.animateImg);

        //show the web spooken text
        if (settings.showInterim) {
          $('#webSpeech_'+ id +'_final_span').show();
          $('#webSpeech_'+ id +'_interim_span').show();
        }
      };

      recognition.onerror = function(event) {
        if (event.error == 'no-speech') {
          $('#webSpeech_'+ id +'_img_mic').attr('src', settings.startImg);
          showInfo(id, 'info_no_speech');
          ignore_onend = true;
        }
        if (event.error == 'audio-capture') {
          $('#webSpeech_'+ id +'_img_mic').attr('src', settings.startImg);
          showInfo(id, 'info_no_microphone');
          ignore_onend = true;
        }
        if (event.error == 'not-allowed') {
          if (event.timeStamp - start_timestamp < 100) {
            showInfo(id, 'info_blocked');
          }
          else {
            showInfo(id, 'info_denied');
          }
          ignore_onend = true;
        }
      };

      recognition.onend = function() {
        recognizing = false;
        if (ignore_onend) {
          return;
        }
        $('#webSpeech_'+ id +'_img_mic').attr('src', settings.startImg);
        if (!final_transcript) {
          showInfo(id, 'info_start');
          return;
        }
        showInfo(id, 'info_start');
        if (window.getSelection) {
          window.getSelection().removeAllRanges();
          var range = document.createRange();
          range.selectNode(document.getElementById('webSpeech_'+ id +'_final_span'));
          window.getSelection().addRange(range);

         //hide the web spooken text
         $('#webSpeech_'+ id +'_final_span').hide();
         $('#webSpeech_'+ id +'_interim_span').hide();

          //add web speech text to our element
          switch(settings.build) {
            case"append":
              if (settings.format == 'input' || settings.format == 'textarea') {
                obj.val(obj.val() + format($('#webSpeech_'+ id +'_final_span').html()));
              }
              else {
                obj.append(format($('#webSpeech_'+ id +'_final_span').html()));
              }
              break;
            case"overwrite":
              if (settings.format == 'input') {
                obj.val(format($('#webSpeech_'+ id +'_final_span').html()));
              }
              else {
                obj.html(format($('#webSpeech_'+ id +'_final_span').html()));
              }
              break;
          }

        }
      };

      recognition.onresult = function(event) {
        var interim_transcript = '';
        for (var i = event.resultIndex; i < event.results.length; ++i) {
          if (event.results[i].isFinal) {
            final_transcript += event.results[i][0].transcript;
          }
          else {
            interim_transcript += event.results[i][0].transcript;
          }
        }
        final_transcript = capitalize(final_transcript);
        $('#webSpeech_'+ id +'_final_span').html(linebreak(final_transcript));
        $('#webSpeech_'+ id +'_interim_span').html(linebreak(interim_transcript));
      };
      //END: recognition

    }

    /*******************
    * HELPER FUNCTIONS
    *******************/

    function upgrade() {
      $('#webSpeech_'+ id +'_btn_mic').hide();
      showInfo(id, 'info_upgrade');
    }

    function showInfo(id, msg) {
      if (!settings.showHelp) return;
      switch(msg) {
        default: 
          $('#webSpeech_'+ id +'_info').hide();
          return;
          break;
        case'info_start':
          $('#webSpeech_'+ id +'_info').html('');
          break;
        case'info_speak_now':
          $('#webSpeech_'+ id +'_info').html('Speak now.');
          break;
        case'info_no_speech':
          $('#webSpeech_'+ id +'_info').html('No speech was detected. You may need to adjust your <a href="//support.google.com/chrome/bin/answer.py?hl=en&amp;answer=1407892">microphone settings</a>.');
          break;
        case'info_no_microphone':
          $('#webSpeech_'+ id +'_info').html('No microphone was found. Ensure that a microphone is installed and that <a href="//support.google.com/chrome/bin/answer.py?hl=en&amp;answer=1407892">microphone settings</a> are configured correctly.');
          break;
        case'info_allow':
          $('#webSpeech_'+ id +'_info').html('Click the "Allow" button above to enable your microphone.');
          break;
        case'info_denied':
          $('#webSpeech_'+ id +'_info').html('Permission to use microphone was denied.');
          break;
        case'info_blocked':
          $('#webSpeech_'+ id +'_info').html('Permission to use microphone is blocked. To change, go to chrome://settings/contentExceptions#media-stream');
          break;
        case'info_upgrade':
          $('#webSpeech_'+ id +'_info').html('Web Speech API is not supported by this browser. Upgrade to <a href="//www.google.com/chrome">Chrome</a> version 25 or later.');
          break;
      }
      $('#webSpeech_'+ id +'_info').show();
    }

    function webSpeech_startButton(id,event) {
      if (recognizing) {
        recognition.stop();
        return;
      }
      final_transcript = '';
      recognition.lang = settings.lang;
      recognition.start();
      ignore_onend = false;
      $('#webSpeech_'+ id +'_final_span').html('');
      $('#webSpeech_'+ id +'_interim_span').html('');
      $('#webSpeech_'+ id +'_img_mic').attr('src', settings.errorImg);
      showInfo(id, 'info_allow');
      start_timestamp = event.timeStamp;
    }

    function linebreak(s) {
      return s.replace(two_line, '<p></p>').replace(one_line, '<br>');
    }

    function capitalize(s) {
      return s.replace(first_char, function(m) { return m.toUpperCase(); });
    }

    function format(s) {
      switch(settings.format) {
        case"input": case"textarea":
          return s.replace('<p></p>', '\n\n').replace('<br>', '\n');
          break;
        case"html":
          return s;
          break;
      }
    }

  }
  //END $.fn.webSpeech

})(jQuery)
/*
 * @file expertblock.js
 * Contains all functionality related to contact validation
 */
(function (Drupal) {
  'use strict';
  Drupal.behaviors.expertblock = {
    attach: function () {

    }
  };
})(Drupal, jQuery);
(function ($) {
  $(document).ready(function () {
    $(".expert-subinfo").attr("href", "javascript:void(0)");
    // Jquery ajax to save expert lead info
    $("body").on("click", ".expert-subinfo", function () {
      var exname = $.trim($("input[name=exname]").val());
      var exlname = $.trim($("input[name=exlname]").val());
      var exmail = $.trim($("input[name=exmail]").val());
      var exmobile = $.trim($("input[name=exmobile]").val());
      var exmessage = $.trim($("#edit-exmessage").val());
      var terms = $('#edit-exptermscondition').is(":checked")?1:0;
      var appid = $.trim($("input[name=appid]").val());
      var cdnmoengage = $.trim($("input[name=cdnmoengage]").val());
      var moengagedebug = $.trim($("input[name=moengage_debug]").val());
      var post_data = "&exname=" + exname + "&exlname=" + exlname + "&exmail=" + exmail + "&exmobile=" + exmobile + "&exmessage=" + exmessage + "&terms=" + terms + "&appid=" + appid + "&cdnmoengage=" + cdnmoengage + "&moengagedebug=" + moengagedebug;
      var pattern = /^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b$/i;
      var expert_flag = true;
      // initial blank error message
      $('.exname_error_msg').html('');
      $('.exlname_error_msg').html('');
      $('.exmail_error_msg').html('');
      $('.exmobile_error_msg').html('');
      $('.exmessage_error_msg').html('');
      // Add valid class in text field
      $("input[name=exname]").removeClass('invalid').addClass('valid');
      $("input[name=exlname]").removeClass('invalid').addClass('valid');
      $("input[name=exmail]").removeClass('invalid').addClass('valid');
      $("input[name=exmobile]").removeClass('invalid').addClass('valid');
      $("#edit-exmessage").removeClass('invalid').addClass('valid');
      if (exname == '') {
        $('.exname_error_msg').html('Please Provide First Name !');
        $("input[name=exname]").removeClass('valid').addClass('invalid');
        expert_flag = false;
      }
      if(exmail != '') {
        if (!pattern.test(exmail)) {
          $('.exmail_error_msg').html('Please Provide Valid Email !');
          $("input[name=exmail]").removeClass('valid').addClass('invalid');
          expert_flag = false;
        }
      }
      if (exmobile.length != 10) {
        $('.exmobile_error_msg').html('Please Provide Valid Mobile Number !');
        $("input[name=exmobile]").removeClass('valid').addClass('invalid');
        expert_flag = false;
      }
      if (expert_flag) {
        $.ajax({
          type: "POST",
          url: drupalSettings.path.baseUrl + "save_getintouch",
          data: post_data,
          cache: false,
          beforeSend: function () {
            $('.loader-wrapper').css('display', 'block');
          },
          success: function (data) {
            $('.loader-wrapper').css('display', 'none');
            if (data.expert_lead_status == 'success') {
                  (function(i, s, o, g, r, a, m, n) {
                    i.moengage_object = r;
                    t = {};
                    q = function(f) {
                      return function() {
                        (i.moengage_q = i.moengage_q || []).push({ f: f, a: arguments });
                      };
                    };
                    (f = [
                      "track_event",
                      "add_user_attribute",
                      "add_first_name",
                      "add_last_name",
                      "add_email",
                      "add_mobile",
                      "add_user_name",
                      "add_gender",
                      "add_birthday",
                      "destroy_session",
                      "add_unique_user_id",
                      "moe_events",
                      "call_web_push",
                      "track",
                      "location_type_attribute",
                    ]),
                      (h = { onsite: ["getData", "registerCallback"] });
                    for (k in f) {
                      t[f[k]] = q(f[k]);
                    }
                    for (k in h)
                      for (l in h[k]) {
                        null == t[k] && (t[k] = {}), (t[k][h[k][l]] = q(k + "." + h[k][l]));
                      }
                    a = s.createElement(o);
                    m = s.getElementsByTagName(o)[0];
                    a.async = 1;
                    a.src = g;
                    m.parentNode.insertBefore(a, m);
                    i.moe =
                      i.moe ||
                      function() {
                        n = arguments[0];
                        return t;
                      };
                    a.onload = function() {
                      if (n) {
                        i[r] = moe(n);
                      }
                    };
                  })(
                    window,
                    document,
                    "script",
                    cdnmoengage,
                    "Moengage"
                  );
                  Moengage = moe({
                    app_id: appid, // here goes your App Id
                    debug_logs: moengagedebug,
                  });
                    Moengage.track_event("Subscribe", {"NewsCategory": "Politics", "moe_non_interactive": 1});
                    Moengage.add_user_attribute("Subscribtion Form");
                    Moengage.add_first_name(exname);
                    Moengage.add_last_name(exlname);
                    Moengage.add_email(exmail);
                    Moengage.add_mobile(exmobile);
                    Moengage.add_unique_user_id(exmobile);
                  // End Moengage tracking system code
                  $(".expert_result_message").addClass("messages--status");
             $('.expert_result_message').html('Submitted Successfully !');
              $('#getintouch-form')[0].reset();
            } else {
              console.log('Something Went Wrong');
            }
          },
          error: function () {
          }
        });
      }
    });
    // Jquery ajax to resend otp
    $("body").on("click", ".rsendcall", function () {
        $(".exotp-rsend-lnk .rsendcall").hide();
        setTimeout(function() {
            $(".exotp-rsend-lnk .rsendcall").show();
        }, 20000);
      var mobileval = $.trim($("input[name=exmobile]").val());
      var resendotp = Math.floor(100000 + Math.random() * 900000)
      resendotp = resendotp.toString().substring(0, 4);
      var resend_data = "&mobileval=" + mobileval;
      var resend_flag = true;
      // initial blank error message
      $('.exmobile_error_msg').html('');
      // Add valid class in text field
      $("input[name=exmobile]").removeClass('invalid').addClass('valid');

      if (mobileval.length != 10) {
        $('.exmobile_error_msg').html('Please Provide Valid Mobile Number !');
        $("input[name=exmobile]").removeClass('valid').addClass('invalid');
        resend_flag = false;
      }
      if (resend_flag) {
        $.ajax({
          type: "POST",
          url: drupalSettings.path.baseUrl + "resend-otp-callback",
          data: resend_data,
          cache: false,
          beforeSend: function () {
          },
          success: function (data) {
            if (data.resend_otp_status == 'success') {
              $('.exotp_success_msg').html('4 Digit OTP sent on above mobile no').show().delay(2000).hide(1000);
              $('.exotp_error_msg').html('');
            } else if (data.resend_otp_status == 'error') {
              $('.exotp_error_msg').html('There was some problem, Please try again later !');
            } else {
              console.log('Something Went Wrong');
            }
          },
          error: function () {
          }
        });
      }
    });
    // Allow only digits in mobile number field
    $("#edit-exmobile, input[name=appmobile], #edit-exotp").keyup(function () {
      var $this = $(this);
      $this.val($this.val().replace(/[^\d.]/g, ''));
    });
    $('#edit-exname').bind('paste', function(){
        var self = this;
        setTimeout(function() {
            if(!/^[a-zA-Z]+$/.test($(self).val()))
                $(self).val('');
        }, 0);
    });
    $("#edit-exname, #edit-exlname").keypress(function (e) {

        var firstChar = $("#edit-exname, #edit-exlname").val()
        var key = e.keyCode;

        var regex = new RegExp("^[a-zA-Z]+$");
        var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
        if (regex.test(str) || key == 32 ) {
            if(e.keyCode == 32){
                var cursorPos = $("#edit-exname").prop("selectionStart");
                //Prevents spaces in Beginning and more than on at the end
                if(cursorPos == 0 || (cursorPos == firstChar.length && firstChar[firstChar.length-1] == " "))
                {
                    e.preventDefault();
                }
            }
            return true;
        } else {
          e.preventDefault();
	  return false;
        }
    });
  });
})(jQuery);

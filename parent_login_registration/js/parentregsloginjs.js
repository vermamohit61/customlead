/*
* @file parentregsloginjs.js
* Contains all functionality related to contact validation
*/
(function (Drupal, $) {
    'use strict';

    Drupal.behaviors.expertblock = {
      attach: function (context, settings) {

      }
    };

  })(Drupal, jQuery);



  (function ($) {
    $(document).ready(function () {
        // Allow only digits in mobile number field
       $("#edit-potp, #edit-pmobile").keyup(function () {
        var $this = $(this);
        $this.val($this.val().replace(/[^\d.]/g, ''));
      });
      var mobile_val = $('#edit-pmobile').val();
        if (mobile_val.length == 10) {
          jQuery(".unotp").attr('style','display: block');
        }

      $('#edit-pmobile').on('blur', function() {
        $(".signup-otp").hide();
        setTimeout(function() {
            $(".signup-otp").show();
        }, 20000);
      var mobileval = $.trim($("#edit-pmobile").val());
      var nodepath = $.trim($("input[name=nodepath]").val());
      var otp_flag = true;
      // condition to check mobile number
      if (mobileval.length != 10) {
        $('.otp-msg .error-text').html('Please Provide Valid Mobile Number.');
        otp_flag = false;
      }
      // If flag is true call controller via ajax
      if (otp_flag) {
        var otp_data = "&mobileval=" + mobileval;
        $.ajax({
          type: "POST",
          url: drupalSettings.path.baseUrl + "signup-otp-verify",
          data: otp_data,
          cache: false,
          beforeSend: function () {
          },
          success: function (data) {
            if (data.otp_verify_status == 'success') {
              $('.otp-msg .success-text').html('4 Digit OTP sent on above mobile no').show();
              $('.otp-msg .error-text').html('');
              $('.otp-link a').text('Resend OTP');
              $(".otp-link a").attr('style','display: block');
              $(".unotp").attr('style','display: block');
              $(".unotp").focus();
            } else if (data.otp_verify_status == 'error') {
              if (nodepath) {  
                $('.otp-msg .error-text').html('This Mobile number is already registered with lead school. <a href = "' + '/user/loginform?destination='+ nodepath +'" class="sign-up">Click Here</a>');         
              } else{
                $('.otp-msg .error-text').html('This Mobile number is already registered with lead school. <a href = "' + '/user/loginform" class="sign-up">Click Here</a>');
              }
              $('.otp-msg .error-text').append(anchor);
            } else {
              console.log('Something Went Wrong');
            }
          },
          error: function (error) {

          }
        });
      }

      });



      // code for signup otp
      // add javascript in link attribute
      $(".signup-otp").hide();
      $(".signup-otp").attr("href", "javascript:void(0)");
      $("body").on("click", ".signup-otp", function () {
          $(".signup-otp").hide();
          $('.otp-msg .success-text').html('4 Digit OTP sent on above mobile no').hide();
          setTimeout(function() {
              $(".signup-otp").show();
          }, 20000);
          setTimeout(function() {
            $('.otp-msg .success-text').html('4 Digit OTP sent on above mobile no').show();
        }, 300);
        var mobileval = $.trim($("#edit-pmobile").val());
        var otp_flag = true;
        // condition to check mobile number
        if (mobileval.length != 10) {
          $('.otp-msg .error-text').html('Please Provide Valid Mobile Number.');
          otp_flag = false;
        }
        // If flag is true call controller via ajax
        if (otp_flag) {
          var otp_data = "&mobileval=" + mobileval;
          $.ajax({
            type: "POST",
            url: drupalSettings.path.baseUrl + "resend-otp-register",
            data: otp_data,
            cache: false,
            beforeSend: function () {
            },
            success: function (data) {
              if (data.resend_otp_status == 'success') {
                $('.otp-msg .success-text').html('4 Digit OTP sent on above mobile no').show();
                $('.otp-msg .error-text').html('');
                $('.potp_error_msg').html('');
                $('.otp-link a').text('Resend OTP');
                $(".otp-link a").attr('style','display: block');
                $(".unotp").attr('style','display: block');
                $(".unotp").focus();
              } else if (data.resend_otp_status == 'successnot') {
                $('.otp-msg .error-text').html('There was some problem, Please try again later !');
              } else {
                console.log('Something Went Wrong');
              }
            },
            error: function (error) {

            }
          });
        }
      });
    });
})(jQuery);
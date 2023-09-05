/*
* @file googleAnalyticaljs.js
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
  $(document).ready(function (){
    // Jquery ajax to save expert lead info
    $("body").on("click", ".registration-subinfo", function () {
      var pname = $.trim($("input[name=pname]").val());
      var pmobile = $.trim($("input[name=pmobile]").val());
      var potp = $.trim($("input[name=potp]").val());
      var cname = $.trim($("input[name=cname]").val());
      var grade = $.trim($("#edit-gradeapplying").val());
      //datalayer for registration events
      var dataLayer = window.dataLayer || [];
      dataLayer.push({
        'event': 'signUp',
           'pname' : pname,
           'pmobile': pmobile,
           'potp' : potp,
           'cname' : cname,  
           'grade' : grade,   
      });
      console.log(dataLayer);
    });
   // login form
  $(".parentloginsend--subinfo").attr("href", "javascript:void(0)");
    // Jquery ajax to parent login GA
    $("body").on("click", ".parentlogin--subinfo", function () {
      var plmobile = $.trim($("input[name=plmobile]").val());
      var plotp = $.trim($("input[name=plotp]").val());
      ////datalayer for parentlogin events
      var dataLayer = window.dataLayer || [];
      dataLayer.push({
        'event': 'login',
           'plmobile': plmobile,
           'plotp': plotp,  
      });
      console.log(dataLayer);
    });
  });
})(jQuery);

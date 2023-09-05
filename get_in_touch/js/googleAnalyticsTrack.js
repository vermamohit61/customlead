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
   // Subscriber data send to google analytics
    $("body").on("click", ".expert-subinfo", function () {
      var exname = $.trim($("input[name=exname]").val());
      var exlname = $.trim($("input[name=exlname]").val());
      var exmail = $.trim($("input[name=exmail]").val()); 
      var exmobile = $.trim($("input[name=exmobile]").val());
      var exmessage = $.trim($("#edit-exmessage").val());
      //datalayer for subscriber
      var dataLayer = window.dataLayer || [];
      dataLayer.push({
        'event': 'Subscribe',
             'exname': exname,
             'exlname': exlname,
             'exmail': exmail,
             'exmobile': exmobile,
             'exmessage': exmessage,
      });
      console.log(dataLayer);
   });
});
})(jQuery);

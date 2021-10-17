jQuery(window).load(function() {

});

jQuery(window).on('scroll', function() {
    var y_scroll_pos = window.pageYOffset;
    var scroll_pos_test = 50;             // set to whatever you want it to be
    if(y_scroll_pos > scroll_pos_test) {
        jQuery("body").addClass('scrolled');

    }
    else{
        jQuery("body").removeClass('scrolled');

    }
});

function opencircle(num){
  var id = num;
  var circle = '.text'+id;
  jQuery(".circletext").removeClass("opencedcircle");
  jQuery(circle).addClass("opencedcircle");
}

function closecircle(num){
  var id = num;
  var circle = '.text'+id;
  jQuery(circle).removeClass("opencedcircle");
}

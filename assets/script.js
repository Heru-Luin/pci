$(function() {
  $("#search").keyup(function() {
    var value = $(this).val();
    
    $('#projects > li:not(:contains(' + value + '))').hide(); 
    $('#projects > li:contains(' + value + ')').show();
  });
  
  $("#search").keypress(function(e) {
    if(e.which == 13) {
      // Count visible li
      var visibleLi = $("ul#projects li:visible");
      
      // If = 1 than click li link
      if (visibleLi.length === 1) {
        document.location.href = visibleLi.find('a').attr('href');
      }
    }
  });
});

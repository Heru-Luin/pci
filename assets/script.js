$(function() {
  $("#search-project").keyup(function() {
    var value = $(this).val();
    
    $('#projects > li:not(:contains(' + value + '))').hide(); 
    $('#projects > li:contains(' + value + ')').show();
  });
  
  $("#search-project").keypress(function(e) {
    if(e.which == 13) {
      // Count visible li
      var visibleLi = $("ul#projects li:visible");
      
      // If = 1 than click li link
      if (visibleLi.length === 1) {
        document.location.href = visibleLi.find('a').attr('href');
      }
    }
  });
  
  $("#search-build").keyup(function() {
    var value = $(this).val();
    
    $('#builds > li:not(:contains(' + value + '))').hide(); 
    $('#builds > li:contains(' + value + ')').show();
  });
  
  $("#search-build").keypress(function(e) {
    if(e.which == 13) {
      // Count visible li
      var visibleLi = $("ul#builds li:visible");
      
      // If = 1 than click li link
      if (visibleLi.length === 1) {
        document.location.href = visibleLi.find('a').attr('href');
      }
    }
  });
});

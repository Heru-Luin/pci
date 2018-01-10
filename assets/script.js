$(function() {
  $("#search").keyup(function() {
    var value = $(this).val();
    
    $('#projects > li:not(:contains(' + value + '))').hide(); 
    $('#projects > li:contains(' + value + ')').show();
  });
});

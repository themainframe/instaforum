$(function() {
  // ------------------------------------------------------
  // Style Editor
  // ------------------------------------------------------
  $('a.style').click(function () {

    // Update code
    $('#' + $('textarea.code').attr('current')).attr('code', $('textarea.code').val());

    $('textarea.code').val($(this).attr('code'));
    $('textarea.code').attr("current", $(this).attr('id'));

    // Select it
    $('a.style').parent().css('background', '#cfcfcf');
    $(this).parent().css('background', '#fff');
  });

  // Submit the form
  $('#stylesubmit').click(function() {

    // Save the current one
    $('#' + $('textarea.code').attr('current')).attr('code', $('textarea.code').val());

    // Generate a field for each
    $('a.style').each(function(i, k) {
      $('<input />').attr('type', 'hidden')
                    .attr('name', $(k).attr('id'))
                    .attr('value', $(k).attr('code'))
                    .appendTo('#styleform');
    });

    $('#styleform').submit();

  });

  $('a.style:first').click();
});
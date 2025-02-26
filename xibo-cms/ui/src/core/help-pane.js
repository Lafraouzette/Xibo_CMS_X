

$(function() {
  const $help = $('#help-pane');
  $help.find('.help-pane-icon').on('click', () => {
    $help.find('.help-pane-card').toggle();
  });
  $help.find('.close-icon').on('click', () => {
    $help.find('.help-pane-card').hide();
  });
});

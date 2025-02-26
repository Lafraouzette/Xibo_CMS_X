
jQuery.fn.extend({
  dataSetRender: function(options) {
    // Any options?
    if (options === undefined || options === null) {
      options = {
        duration: 5,
        transition: 'fade',
        rowsPerPage: 0,
        previewWidth: 0,
        previewHeight: 0,
        scaleOverride: 0,
      };
    }

    $(this).each(function(_idx, el) {
      const numberItems = $(el).data('totalPages');
      const duration =
        (options.durationIsPerItem) ?
          options.duration : options.duration / numberItems;

      if (options.rowsPerPage > 0) {
        // Cycle handles this for us
        if ($(el).prop('isCycle')) {
          $(el).cycle('destroy');
        }

        $(el).prop('isCycle', true).cycle({
          fx: options.transition,
          timeout: duration * 1000,
          slides: '> table',
        });
      }
    });

    return $(this);
  },
});

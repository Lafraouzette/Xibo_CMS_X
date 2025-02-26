
jQuery.fn.extend({
  xiboLayoutAnimate: function(options) {
    // Default options
    const defaults = {
      effect: 'none',
    };
    options = $.extend({}, defaults, options);

    this.each(function(_key, element) {
      const isAndroid = navigator.userAgent.indexOf('Android') > -1;
      const $contentDiv = $(element);
      // Marquee effect
      if (
        options.effect === 'marqueeUp' ||
        options.effect === 'marqueeDown'
      ) {
        $contentDiv.find('.scroll:not(.animating)').marquee();
      } else if (
        options.effect === 'marqueeLeft' ||
        options.effect === 'marqueeRight'
      ) {
        if (isAndroid) {
          $contentDiv.find('.scroll:not(.animating)').overflowMarquee();
        } else {
          $contentDiv.find('.scroll:not(.animating)').marquee();
        }
      } else if (options.effect !== 'none' ||
        options.effect === 'noTransition'
      ) { // Cycle effect
        // Resume effect
        const $target = $contentDiv.is('.anim-cycle') ?
          $contentDiv : $contentDiv.find('.anim-cycle');

        $target.cycle('resume');
      }
    });

    return $(this);
  },
});

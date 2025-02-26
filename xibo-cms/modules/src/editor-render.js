
$(function() {
  // Get a message from the parent window
  // RUN ON IFRAME
  window.onmessage = function(e) {
    if (
      e.data.method == 'renderContent'
    ) {
      // Update global options for the widget
      globalOptions.originalWidth = e.data.options.originalWidth;
      globalOptions.originalHeight = e.data.options.originalHeight;

      // Set the pause state for animation to false
      // To start right after the render effects are generated
      globalOptions.pauseEffectOnStart =
        e.data.options.pauseEffectOnStart ?? false;

      // Arguments for both reRender
      const args = (typeof widget != 'undefined') ? [
        e.data.options.id, // id
        $('body'), // target
        widget.items, // items
        Object.assign(widget.properties, globalOptions), // properties
        widget.meta, // meta
      ] : [];

      // Call render array of functions if exists and it's an array
      if (window.renders && Array.isArray(window.renders)) {
        window.renders.forEach((render) => {
          render(...args);
        });
      }
    }
  };
});

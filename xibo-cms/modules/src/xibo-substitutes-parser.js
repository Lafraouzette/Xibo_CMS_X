
jQuery.fn.extend({
  xiboSubstitutesParser: function(
    template,
    dateFormat,
    dateFields = [],
    mapping = {},
  ) {
    const items = [];
    const parser = new RegExp('\\[.*?\\]', 'g');
    const pipeParser = new RegExp('\\|{1}', 'g');
    this.each(function(_idx, data) {
      // Parse the template for a list of things to substitute, and match those
      // with content from items.
      let replacement = template;
      let match = parser.exec(template);
      while (match != null) {
        // Matched text: match[0], match start: match.index,
        // capturing group n: match[n]
        // Remove the [] from the match
        let variable = match[0]
          .replace('[', '')
          .replace(']', '');
        variable = variable.charAt(0).toLowerCase() + variable.substring(1);

        // Check if variable has its own formatting
        // Then, parse it and use later as dateFormat
        let formatFromTemplate = null;

        if (variable.match(pipeParser) !== null &&
          variable.match(pipeParser).length === 1) {
          const variableWithFormat = variable.split('|');
          formatFromTemplate = variableWithFormat[1];
          variable = variableWithFormat[0];
        }

        if (mapping[variable]) {
          variable = mapping[variable];
        }
        let value = '';

        // Does this variable exist? or is it one of the ones in our map
        if (data.hasOwnProperty(variable)) {
          // Use it
          value = data[variable];

          // Is it a date field?
          dateFields.forEach((field) => {
            if (field === variable) {
              value = moment(value).format(formatFromTemplate !== null ?
                formatFromTemplate : dateFormat);
            }
          });
        }

        // If value is null, set it as empty string
        (value === null) && (value = '');

        // Finally set the replacement in the template
        replacement = replacement.replace(match[0], value);

        // Get the next match
        match = parser.exec(template);
      }

      // Add to our items
      items.push(replacement);
    });
    return items;
  },
});

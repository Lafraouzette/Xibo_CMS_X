


describe('Tasks', function() {
  beforeEach(function() {
    cy.login();
  });

  it('should edit a task', function() {
    // Intercept the PUT request
    cy.intercept({
      method: 'PUT',
      url: '/task/*',
    }).as('putRequest');

    cy.visit('/task/view');

    // Click on the first row element to open the delete modal
    cy.get('#tasks tr:first-child .dropdown-toggle').click({force: true});
    cy.get('#tasks tr:first-child .task_button_edit').click({force: true});

    // Assuming you have an input field with the id 'myInputField'
    cy.get('.modal input#name').invoke('val').then((value) => {
      return Cypress.Promise.resolve(value);
    }).then((value) => {
      if (value) {
        cy.get('.modal input#name').clear()
          .type(value + ' Edited');

        // edit test tag
        cy.get('.bootbox .save-button').click();

        // Wait for the intercepted PUT request and check the form data
        cy.wait('@putRequest').then((interception) => {
          // Get the request body (form data)
          const response = interception.response;
          const responseData = response.body.data;

          // assertion on the "task" value
          expect(responseData.name).to.eq(value + ' Edited');
        });
      }
    });
  });
});

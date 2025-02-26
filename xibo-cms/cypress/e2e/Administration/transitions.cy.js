


describe('Transitions', function() {
  beforeEach(function() {
    cy.login();
  });

  it('should edit an transition', function() {
    // Intercept the PUT request
    cy.intercept({
      method: 'PUT',
      url: '/transition/*',
    }).as('putRequest');

    cy.visit('/transition/view');
    cy.get('#transitions tbody tr').should('have.length', 3);

    // Click on the first row element to open the delete modal
    cy.get('#transitions tr:first-child .dropdown-toggle').click({force: true});
    cy.get('#transitions tr:first-child .transition_button_edit').click({force: true});

    cy.get('.modal #availableAsIn').then(($checkbox) => {
      const isChecked = $checkbox.prop('checked');
      cy.get('#availableAsIn').should('be.visible').click(); // Click to check/uncheck

      // edit
      cy.get('.bootbox .save-button').click();

      // Wait for the intercepted PUT request and check the form data
      cy.wait('@putRequest').then((interception) => {
        // Get the request body (form data)
        const response = interception.response;
        const responseData = response.body.data;

        // assertion on the "task" value
        if (isChecked) {
          expect(responseData.availableAsIn).to.eq(0);
        } else {
          expect(responseData.availableAsIn).to.eq(1);
        }
      });
    });
  });
});

describe('Applications', function() {
  let testRun = '';

  beforeEach(function() {
    cy.login();

    testRun = Cypress._.random(0, 1e9);
  });

  it('should add edit an application', function() {
    // Intercept the PUT request
    cy.intercept({
      method: 'PUT',
      url: '/application/*',
    }).as('putRequest');

    cy.visit('/application/view');

    // Click on the Add Application button
    cy.contains('Add Application').click();

    cy.get('.modal input#name')
      .type('Cypress Test Application ' + testRun);

    // Add first by clicking next
    cy.get('.modal .save-button').click();

    // Check if application is added in toast message
    cy.contains('Edit Application');

    cy.get('.modal input#name').clear()
      .type('Cypress Test Application Edited ' + testRun);

    // edit test application
    cy.get('.bootbox .save-button').click();

    // Wait for the intercepted PUT request and check the form data
    cy.wait('@putRequest').then((interception) => {
      // Get the request body (form data)
      const response = interception.response;
      const responseData = response.body.data;

      // assertion on the "application" value
      expect(responseData.name).to.eq('Cypress Test Application Edited ' + testRun);
      // Return appKey as a Cypress.Promise to ensure proper scoping
      return Cypress.Promise.resolve(responseData.key);
    }).then((appKey) => {
      if (appKey) {
        // TODO cannot be deleted via cypress
        // Delete the application and assert success
        // cy.deleteApplication(appKey).then((res) => {
        //   expect(res.status).to.equal(200);
        // });
      }
    });
  });
});

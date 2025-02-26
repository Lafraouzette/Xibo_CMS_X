


describe('Usergroups', function() {
  let testRun = '';

  beforeEach(function() {
    cy.login();

    testRun = Cypress._.random(0, 1e9);
  });

  it('should add a usergroup', function() {
    cy.visit('/group/view');

    // Click on the Add Usergroup button
    cy.contains('Add User Group').click();

    cy.get('.modal input#group')
      .type('Cypress Test Usergroup ' + testRun + '_1');

    // Add first by clicking next
    cy.get('.modal .save-button').click();

    // Check if usergroup is added in toast message
    cy.contains('Added Cypress Test Usergroup');
  });

  it('searches and edit existing usergroup', function() {
    // Create a new usergroup and then search for it and delete it
    cy.createUsergroup('Cypress Test Usergroup ' + testRun).then((groupId) => {
      cy.intercept({
        url: '/group?*',
        query: {userGroup: 'Cypress Test Usergroup ' + testRun},
      }).as('loadGridAfterSearch');

      // Intercept the PUT request
      cy.intercept({
        method: 'PUT',
        url: '/group/*',
      }).as('putRequest');

      cy.visit('/group/view');

      // Filter for the created usergroup
      cy.get('#Filter input[name="userGroup"]')
        .type('Cypress Test Usergroup ' + testRun);

      // Wait for the grid reload
      cy.wait('@loadGridAfterSearch');
      cy.get('#userGroups tbody tr').should('have.length', 1);

      // Click on the first row element to open the delete modal
      cy.get('#userGroups tr:first-child .dropdown-toggle').click({force: true});
      cy.get('#userGroups tr:first-child .usergroup_button_edit').click({force: true});

      cy.get('.modal input#group').clear()
        .type('Cypress Test Usergroup Edited ' + testRun);

      // edit test usergroup
      cy.get('.bootbox .save-button').click();

      // Wait for the intercepted PUT request and check the form data
      cy.wait('@putRequest').then((interception) => {
        // Get the request body (form data)
        const response = interception.response;
        const responseData = response.body.data;

        // assertion on the "usergroup" value
        expect(responseData.group).to.eq('Cypress Test Usergroup Edited ' + testRun);

        // Delete the usergroup and assert success
        cy.deleteUsergroup(groupId).then((response) => {
          expect(response.status).to.equal(200);
        });
      });
    });
  });

  it('searches and delete existing usergroup', function() {
    // Create a new usergroup and then search for it and delete it
    cy.createUsergroup('Cypress Test Usergroup ' + testRun).then((groupId) => {
      cy.intercept({
        url: '/group?*',
        query: {userGroup: 'Cypress Test Usergroup ' + testRun},
      }).as('loadGridAfterSearch');

      cy.visit('/group/view');

      // Filter for the created usergroup
      cy.get('#Filter input[name="userGroup"]')
        .type('Cypress Test Usergroup ' + testRun);

      // Wait for the grid reload
      cy.wait('@loadGridAfterSearch');
      cy.get('#userGroups tbody tr').should('have.length', 1);

      // Click on the first row element to open the delete modal
      cy.get('#userGroups tr:first-child .dropdown-toggle').click({force: true});
      cy.get('#userGroups tr:first-child .usergroup_button_delete').click({force: true});

      // Delete test usergroup
      cy.get('.bootbox .save-button').click();

      // Check if usergroup is deleted in toast message
      cy.get('.toast').contains('Deleted Cypress Test Usergroup');
    });
  });
});

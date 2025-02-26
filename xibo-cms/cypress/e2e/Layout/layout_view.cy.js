


describe('Layout View', function() {
  beforeEach(function() {
    cy.login();
  });

  it('searches and delete existing layout', function() {
    // Create random name
    const uuid = Cypress._.random(0, 1e10);

    // Create a new layout and go to the layout's designer page, then load toolbar prefs
    cy.createLayout(uuid).as('testLayoutId').then((res) => {
      cy.intercept('GET', '/layout?draw=2&*').as('layoutGridLoad');

      cy.visit('/layout/view');

      // Filter for the created layout
      cy.get('#Filter input[name="layout"]')
        .type(uuid);

      // Wait for the layout grid reload
      cy.wait('@layoutGridLoad');

      // Click on the first row element to open the designer
      cy.get('#layouts tr:first-child .dropdown-toggle').click({force: true});
      cy.get('#layouts tr:first-child .layout_button_delete').click({force: true});

      // Delete test layout
      cy.get('.bootbox .save-button').click();

      // Check if layout is deleted in toast message
      cy.get('.toast').contains('Deleted ' + uuid);
    });
  });
});

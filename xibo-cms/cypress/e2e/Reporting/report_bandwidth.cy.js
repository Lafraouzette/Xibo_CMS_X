


describe('Bandwidth', function() {
  const display1 = 'POP Display 1';

  beforeEach(function() {
    cy.login();
  });

  it('should load tabular data and charts', () => {
    // Create and alias for load Display
    cy.intercept({
      url: '/display?start=*',
      query: {display: display1},
    }).as('loadDisplayAfterSearch');

    cy.intercept('/report/data/bandwidth?*').as('reportData');

    cy.visit('/report/form/bandwidth');

    // Click on the select2 selection
    cy.get('#displayId + span .select2-selection').click();
    cy.get('.select2-container--open input[type="search"]').type(display1);
    cy.wait('@loadDisplayAfterSearch');
    cy.selectOption(display1);

    // Click on the Apply button
    cy.contains('Apply').should('be.visible').click();

    cy.get('.chart-container').should('be.visible');

    // Click on Tabular
    cy.contains('Tabular').should('be.visible').click();
    cy.wait('@reportData');

    // Should have media stats
    cy.get('#bandwidthTbl tbody tr:nth-child(1) td:nth-child(1)').contains('Submit Stats');
    cy.get('#bandwidthTbl tbody tr:nth-child(1) td:nth-child(2)').contains(200); // Bandwidth
    cy.get('#bandwidthTbl tbody tr:nth-child(1) td:nth-child(3)').contains('bytes'); // Unit
  });
});

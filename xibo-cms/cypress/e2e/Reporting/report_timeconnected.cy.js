


describe('Time Connected', function() {
  beforeEach(function() {
    cy.login();
  });

  it('should load time connected data of displays', () => {
    cy.visit('/report/form/timeconnected');

    // Click on the select2 selection
    cy.get('.select2-search__field').click();

    // Type the display name
    cy.get('.select2-container--open textarea[type="search"]').type('POP Display Group');
    cy.get('.select2-container--open .select2-results > ul').contains('POP Display Group').click();

    // Click on the Apply button
    cy.contains('Apply').should('be.visible').click();

    // Should have media stats
    cy.get('#records_table tr:nth-child(1) th:nth-child(1)').contains('POP Display 1');
    cy.get('#records_table tr:nth-child(2) td:nth-child(2)').contains('100%');
  });
});

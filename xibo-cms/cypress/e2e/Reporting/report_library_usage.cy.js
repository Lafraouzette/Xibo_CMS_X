


describe('Library Usage', function() {
  beforeEach(function() {
    cy.login();
  });

  it('should load tabular data and charts', () => {
    cy.visit('/report/form/libraryusage');

    cy.get('#libraryUsage_wrapper').should('be.visible');
    cy.get('#libraryChart').should('be.visible');
    cy.get('#userChart').should('be.visible');
  });
});



describe('Modules Page', function () {
    beforeEach(function () {
        cy.login();
    });

    it.skip('should load the modules page and show a complete table of modules', function () {

        cy.visit('/module/view');

        cy.contains('Modules');

        // Click on the first page of the pagination
        cy.get('.pagination > :nth-child(2) > a').click();

        cy.contains('Showing 1 to');
    });
});
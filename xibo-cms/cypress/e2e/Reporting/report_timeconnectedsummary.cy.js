


describe('Time Connected', function() {
  const display1 = 'POP Display 1';
  beforeEach(function() {
    cy.login();
  });

  it('should load time connected data of displays', () => {
    // Create and alias for load display
    cy.intercept({
      url: '/display?start=*',
      query: {display: display1},
    }).as('loadDisplayAfterSearch');

    cy.visit('/report/form/timedisconnectedsummary');

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

    // Should have media stats
    cy.get('#timeDisconnectedTbl tr:nth-child(1) td:nth-child(2)').contains('POP Display 1');
    cy.get('#timeDisconnectedTbl tr:nth-child(1) td:nth-child(3)').contains('10');
  });
});

<?php


namespace Xibo\Tests\Xmds;

use PHPUnit\Framework\Attributes\DataProvider;
use Xibo\Tests\XmdsTestCase;

/**
 * Report fault tests
 */
final class ReportFaultsTest extends XmdsTestCase
{
    use XmdsHelperTrait;

    public function setUp(): void
    {
        parent::setUp();
    }

    public static function successCases(): array
    {
        return [
            [7],
            [6],
        ];
    }

    public static function failureCases(): array
    {
        return [
            [5],
            [4],
            [3],
        ];
    }

    #[DataProvider('successCases')]
    public function testSendFaultSuccess(int $version)
    {
        $request = $this->sendRequest('POST', $this->reportFault($version), $version);

        $this->assertStringContainsString(
            '<ns1:ReportFaultsResponse><success xsi:type="xsd:boolean">true</success>',
            $request->getBody()->getContents(),
            'Send fault received incorrect response'
        );
    }

    #[DataProvider('failureCases')]
    public function testSendFaultFailure(int $version)
    {
        // disable exception on http_error in guzzle, so we can still check the response
        $request = $this->sendRequest('POST', $this->reportFault($version), $version, false);

        // check the fault code
        $this->assertStringContainsString(
            '<faultcode>SOAP-ENV:Server</faultcode>',
            $request->getBody(),
            'Send fault received incorrect response'
        );

        // check the fault string
        $this->assertStringContainsString(
            '<faultstring>Procedure \'ReportFaults\' not present</faultstring>',
            $request->getBody(),
            'Send fault received incorrect response'
        );
    }

    #[DataProvider('failureCases')]
    public function testSendFaultExceptionFailure(int $version)
    {
        // we are expecting 500 Server Exception here for xmds 3,4 and 5
        $this->expectException('GuzzleHttp\Exception\ServerException');
        $this->expectExceptionCode(500);
        $this->sendRequest('POST', $this->reportFault($version), $version);
    }
}

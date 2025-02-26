<?php


namespace Xibo\Tests\Xmds;

use GuzzleHttp\Exception\GuzzleException;
use Xibo\Tests\xmdsTestCase;

class SubmitLogTest extends XmdsTestCase
{
    use XmdsHelperTrait;

    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Submit log with category event
     * @return void
     * @throws GuzzleException
     */
    public function testSubmitEventLog()
    {
        $request = $this->sendRequest(
            'POST',
            $this->submitEventLog('7'),
            7
        );

        $this->assertStringContainsString(
            '<ns1:SubmitLogResponse><success xsi:type="xsd:boolean">true</success>',
            $request->getBody()->getContents(),
            'Submit Log received incorrect response'
        );
    }
}

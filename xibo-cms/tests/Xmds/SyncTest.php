<?php


namespace Xibo\Tests\Xmds;

use DOMDocument;
use DOMXPath;
use PHPUnit\Framework\Attributes\DataProvider;
use Xibo\Tests\xmdsTestCase;

/**
 * Sync Schedule and Register tests
 */
class SyncTest extends XmdsTestCase
{
    use XmdsHelperTrait;

    public function setUp(): void
    {
        parent::setUp();
    }

    public static function registerSuccessCases(): array
    {
        return [
            [7],
            [6],
        ];
    }

    public function testScheduleSyncEvent()
    {
        $request = $this->sendRequest('POST', $this->getSchedule('PHPUnit7'), 7);

        $response = $request->getBody()->getContents();

        $document = new DOMDocument();
        $document->loadXML($response);

        $xpath = new DOMXpath($document);
        $result = $xpath->evaluate('string(//ScheduleXml)');
        $innerDocument = new DOMDocument();
        $innerDocument->loadXML($result);
        $layouts = $innerDocument->documentElement->getElementsByTagName('layout');

        $i = 0;
        foreach ($layouts as $layout) {
            if ($i === 0) {
                $this->assertSame('8', $layout->getAttribute('file'));
                $this->assertSame('1', $layout->getAttribute('syncEvent'));
                $this->assertSame('2', $layout->getAttribute('scheduleid'));
            } else if ($i === 1) {
                $this->assertSame('6', $layout->getAttribute('file'));
                $this->assertSame('0', $layout->getAttribute('syncEvent'));
                $this->assertSame('1', $layout->getAttribute('scheduleid'));
            }
            $i++;
        }
    }

    #[DataProvider('registerSuccessCases')]
    public function testRegisterDisplay($version)
    {
        if ($version === 7) {
            $this->sendRequest('POST', $this->notifyStatus($version, '{"lanIpAddress":"192.168.0.3"}'), $version);
            $xml = $this->register(
                'PHPUnit7',
                'phpunitv7',
                'android'
            );
        } else {
            $xml = $this->register(
                'PHPUnit6',
                'phpunitv6',
                'android'
            );
        }

        $request = $this->sendRequest('POST', $xml, $version);
        $response = $request->getBody()->getContents();

        $document = new DOMDocument();
        $document->loadXML($response);

        $xpath = new DOMXpath($document);
        $result = $xpath->evaluate('string(//ActivationMessage)');
        $innerDocument = new DOMDocument();
        $innerDocument->loadXML($result);

        $this->assertSame('READY', $innerDocument->documentElement->getAttribute('code'));
        $this->assertSame(
            'Display is active and ready to start.',
            $innerDocument->documentElement->getAttribute('message')
        );

        $syncNodes = $innerDocument->getElementsByTagName('syncGroup');
        $this->assertSame(1, count($syncNodes));

        if ($version === 7) {
            $this->assertSame('lead', $syncNodes->item(0)->textContent);
        } else {
            $this->assertSame('192.168.0.3', $syncNodes->item(0)->textContent);
        }
    }
}

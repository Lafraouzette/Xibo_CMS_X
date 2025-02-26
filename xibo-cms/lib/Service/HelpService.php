<?php


namespace Xibo\Service;

use Illuminate\Support\Str;
use Symfony\Component\Yaml\Yaml;
use Xibo\Entity\HelpLink;

/**
 * Class HelpService
 * @package Xibo\Service
 */
class HelpService implements HelpServiceInterface
{
    /** @var string */
    private string $helpBase;

    private ?array $links = null;

    /**
     * @inheritdoc
     */
    public function __construct($helpBase)
    {
        $this->helpBase = $helpBase;
    }

    public function getLandingPage(): string
    {
        return $this->helpBase;
    }

    public function getLinksForPage(string $pageName): array
    {
        if ($this->links === null) {
            $this->loadLinks();
        }
        return $this->links[$pageName] ?? [];
    }

    private function loadLinks(): void
    {
        // Load links from file.
        try {
            if (file_exists(PROJECT_ROOT . '/custom/help-links.yaml')) {
                $links = (array)Yaml::parseFile(PROJECT_ROOT . '/custom/help-links.yaml');
            } else if (file_exists(PROJECT_ROOT . '/help-links.yaml')) {
                $links = (array)Yaml::parseFile(PROJECT_ROOT . '/help-links.yaml');
            } else {
                $this->links = [];
                return;
            }
        } catch (\Exception) {
            return;
        }

        // Parse links.
        $this->links = [];

        foreach ($links as $pageName => $page) {
            // New page
            $this->links[$pageName] = [];

            foreach ($page as $link) {
                $helpLink = new HelpLink($link);
                if (!Str::startsWith($helpLink->url, ['http://', 'https://'])) {
                    $helpLink->url = $this->helpBase . $helpLink->url;
                }
                if (!empty($helpLink->summary)) {
                    $helpLink->summary = \Parsedown::instance()->setSafeMode(true)->line($helpLink->summary);
                }

                $this->links[$pageName][] = $helpLink;
            }
        }
    }
}

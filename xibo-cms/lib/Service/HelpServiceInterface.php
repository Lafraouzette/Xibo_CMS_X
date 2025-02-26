<?php


namespace Xibo\Service;

use Xibo\Entity\HelpLink;

/**
 * Return help links for a page.
 * @package Xibo\Service
 */
interface HelpServiceInterface
{
    /**
     * Get the landing page
     * @return string
     */
    public function getLandingPage(): string;

    /**
     * Get links for page
     * @param string $pageName The page name to return links for
     * @return HelpLink[]
     */
    public function getLinksForPage(string $pageName): array;
}

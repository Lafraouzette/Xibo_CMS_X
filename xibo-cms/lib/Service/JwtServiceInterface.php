<?php


namespace Xibo\Service;

use Lcobucci\JWT\Token;
use Psr\Log\LoggerInterface;

/**
 * A service to create and validate JWTs
 */
interface JwtServiceInterface
{
    public function useLogger(LoggerInterface $logger): JwtServiceInterface;
    public function generateJwt($issuedBy, $permittedFor, $identifiedBy, $relatedTo, $ttl): Token;
    public function validateJwt($jwt): ?Token;
}

<?php


namespace Xibo\OAuth;

use Carbon\Carbon;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Token\Builder;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\Traits\AccessTokenTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\TokenEntityTrait;

/**
 * Class AccessTokenEntity
 * @package Xibo\OAuth
 */
class AccessTokenEntity implements AccessTokenEntityInterface
{
    use AccessTokenTrait, TokenEntityTrait, EntityTrait;

    /**
     * Generate a JWT from the access token
     *
     * @param CryptKey $privateKey
     *
     * @return Token
     */
    private function convertToJWT(CryptKey $privateKey)
    {
        $userId = $this->getUserIdentifier();
        $tokenBuilder = (new Builder(new JoseEncoder(), ChainedFormatter::default()));
        $signingKey = Key\InMemory::file($privateKey->getKeyPath());

        return $tokenBuilder
            ->issuedBy('info@xibosignage.com')
            ->permittedFor($this->getClient()->getIdentifier())
            ->identifiedBy($this->getIdentifier())
            ->issuedAt(Carbon::now()->toDateTimeImmutable())
            ->canOnlyBeUsedAfter(Carbon::now()->toDateTimeImmutable())
            ->expiresAt($this->getExpiryDateTime())
            ->relatedTo($userId)
            ->withClaim('scopes', $this->getScopes())
            ->getToken(new Sha256(), $signingKey)
            ;
    }

    /**
     * Generate a string representation from the access token
     */
    public function __toString()
    {
        return $this->convertToJWT($this->privateKey)->toString();
    }
}

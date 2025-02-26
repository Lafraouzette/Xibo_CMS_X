<?php


namespace Xibo\OAuth;

use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use Stash\Interfaces\PoolInterface;

class RefreshTokenRepository implements RefreshTokenRepositoryInterface
{
    /**
     * @var \Xibo\Service\LogServiceInterface
     */
    private $logger;
    /**
     * @var PoolInterface
     */
    private $pool;

    /**
     * AccessTokenRepository constructor.
     * @param \Xibo\Service\LogServiceInterface $logger
     */
    public function __construct(\Xibo\Service\LogServiceInterface $logger, PoolInterface $pool)
    {
        $this->logger = $logger;
        $this->pool = $pool;
    }

    /**
     * {@inheritdoc}
     */
    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity)
    {
        $date = clone $refreshTokenEntity->getExpiryDateTime();
        // since stash cache sets expiresAt at up to provided date
        // with up to 15% less than the provided date
        // add more time to normal refresh token expire, to ensure cache does not expire before the token.
        $date = $date->add(new \DateInterval('P15D'));

        // cache with refresh token identifier
        $cache = $this->pool->getItem('R_' . $refreshTokenEntity->getIdentifier());
        $cache->set(
            [
                'accessToken' => $refreshTokenEntity->getAccessToken()->getIdentifier(),
            ]
        );
        $cache->expiresAt($date);
        $this->pool->saveDeferred($cache);
    }

    /**
     * {@inheritdoc}
     */
    public function revokeRefreshToken($tokenId)
    {
        $this->pool->getItem('R_' . $tokenId)->clear();
    }

    /**
     * {@inheritdoc}
     */
    public function isRefreshTokenRevoked($tokenId)
    {
        // get cache by refresh token identifier
        $cache = $this->pool->getItem('R_' . $tokenId);
        $refreshTokenData = $cache->get();

        if ($cache->isMiss() || empty($refreshTokenData)) {
            return true;
        }

        // get access token cache by access token identifier
        $tokenCache = $this->pool->getItem('C_' . $refreshTokenData['accessToken']);
        $tokenCacheData = $tokenCache->get();

        // if the token itself not expired yet
        // check if it was unauthorised by the specific user
        // we cannot always check this as it would revoke refresh token if the access token already expired.
        if (!$tokenCache->isMiss() && !empty($tokenCacheData)) {
            // check access token cache by client and user identifiers
            // (see if application got changed secret/revoked access)
            $cache2 = $this->pool->getItem('C_' . $tokenCacheData['client'] . '/' . $tokenCacheData['userIdentifier']);
            $data2 = $cache2->get();

            if ($cache2->isMiss() || empty($data2)) {
                return true;
            }
        }

        return false; // The refresh token has not been revoked
    }

    /**
     * {@inheritdoc}
     */
    public function getNewRefreshToken()
    {
        return new RefreshTokenEntity();
    }
}

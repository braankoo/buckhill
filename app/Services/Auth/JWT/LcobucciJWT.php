<?php

namespace App\Services\Auth\JWT;

use DateTimeImmutable;
use Illuminate\Contracts\Auth\Authenticatable;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Token\Builder;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\UnencryptedToken;

/**
 *
 */
final class LcobucciJWT implements JWT
{
    public function provideToken(Authenticatable $user): UnencryptedToken
    {
        $tokenBuilder = (new Builder(new JoseEncoder(), ChainedFormatter::default()));

        $configuration = $this->getConfiguration();

        return $this->generateToken($tokenBuilder, $user, $configuration, new DateTimeImmutable());
    }

    public function parseToken(string $token): Token
    {
        $parser = new Parser(new JoseEncoder());
        return $parser->parse($token);
    }

    private function getConfiguration(): Configuration
    {
        return Configuration::forAsymmetricSigner(
            new Sha256(),
            InMemory::file(base_path('/token-key.pem')),
            InMemory::base64Encoded(config('jwt')['JWT_KEY'])
        );
    }

    private function generateToken(
        Builder $tokenBuilder,
        Authenticatable $user,
        Configuration $configuration,
        DateTimeImmutable $now
    ): UnencryptedToken {
        return $tokenBuilder
            ->issuedBy(config('app.url'))
            ->permittedFor(config('app.url'))
            ->identifiedBy($user->id)
            ->issuedAt($now)
            ->expiresAt($now->modify('+ ' . config('jwt')['JWT_TTL'] . ' seconds'))
            ->withClaim('user_uuid', $user->uuid)
            ->withClaim('user_level', $user->is_admin ? 'admin' : 'user')
            ->getToken($configuration->signer(), $configuration->signingKey());
    }
}

<?php

namespace App\Services\Auth\JWT;

use App\Models\User;
use DateTimeImmutable;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Token\Builder;
use Lcobucci\JWT\Token\InvalidTokenStructure;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\UnencryptedToken;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\PermittedFor;
use Lcobucci\JWT\Validation\Validator;

final class LcobucciJWT implements JWT
{
    public function provideToken(User $user): UnencryptedToken
    {
        $tokenBuilder = (new Builder(
            new JoseEncoder(),
            ChainedFormatter::default()
        ));

        $configuration = $this->getConfiguration();

        return $this->generateToken(
            $tokenBuilder,
            $user,
            $configuration,
            new DateTimeImmutable()
        );
    }

    public function parseToken(string $token): Token
    {
        if ($token === '') {
            throw new InvalidTokenStructure('Token cannot be empty');
        }
        $parser = new Parser(new JoseEncoder());

        return $parser->parse($token);
    }

    private function getConfiguration(): Configuration
    {
        $keyPath = base_path('/token-key.pem');
        if ($keyPath === '') {
            throw new \InvalidArgumentException('Key file path is empty');
        }
        if ( ! file_exists($keyPath)) {
            throw new \InvalidArgumentException('Key file not found: ');
        }

        return Configuration::forAsymmetricSigner(
            new Sha256(),
            InMemory::file($keyPath),
            InMemory::base64Encoded(config('jwt')['JWT_KEY'])
        );
    }

    private function generateToken(
        Builder $tokenBuilder,
        User $user,
        Configuration $configuration,
        DateTimeImmutable $now
    ): UnencryptedToken {
        return $tokenBuilder
            ->issuedBy(config('app.url'))
            ->permittedFor(config('app.url'))
            ->identifiedBy((string) $user->id)
            ->issuedAt($now)
            ->expiresAt(
                $now->modify('+ ' . config('jwt')['JWT_TTL'] . ' seconds')
            )
            ->withClaim('user_uuid', $user->uuid)
            ->withClaim('access_level', $user->is_admin ? 'admin' : 'user')
            ->getToken($configuration->signer(), $configuration->signingKey());
    }

    public function validateToken(UnencryptedToken $token): bool
    {
        $validator = new Validator();
        $constraints = [
            new IssuedBy(config('app.url')),
            new PermittedFor(config('app.url')),
        ];

        return $validator->validate($token, ...$constraints);
    }
}

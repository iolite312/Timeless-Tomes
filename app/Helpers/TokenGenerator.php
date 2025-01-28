<?php

namespace App\Helpers;

use Ramsey\Uuid\Uuid;
use Lcobucci\JWT\Token\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Encoding\ChainedFormatter;

class TokenGenerator
{
    public static function generateTemporaryToken()
    {
        $tokenBuilder = Builder::new(new JoseEncoder(), ChainedFormatter::default());
        $algorithm = new Sha256();
        $signingKey = InMemory::base64Encoded($_ENV['JWT_SECRET']);

        $fakeUser = [
            'id' => '1',
            'email' => 'example.com',
            'password' => '1234',
        ];

        $now = new \DateTimeImmutable();
        $token = $tokenBuilder
            ->issuedBy($_ENV['JWT_ISSUER'])
            ->permittedFor($_ENV['JWT_AUDIENCE'])
            ->relatedTo('Temporary')
            ->identifiedBy(Uuid::uuid4()->toString())
            ->issuedAt($now)
            ->canOnlyBeUsedAfter($now)
            ->expiresAt($now->modify('+1 hour'))
            ->withClaim('user', $fakeUser)
            ->getToken($algorithm, $signingKey);

        return $token->toString();
    }
}

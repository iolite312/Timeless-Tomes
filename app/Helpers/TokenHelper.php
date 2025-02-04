<?php

namespace App\Helpers;

use App\Models\User;
use Ramsey\Uuid\Uuid;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Token\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Validation\Validator;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Token\InvalidTokenStructure;
use Lcobucci\JWT\Encoding\CannotDecodeContent;
use Lcobucci\JWT\Token\UnsupportedHeaderFound;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\LooseValidAt;
use Lcobucci\JWT\Validation\Constraint\PermittedFor;

class TokenHelper
{
    public static function generateToken(User $user): string
    {
        $tokenBuilder = Builder::new(new JoseEncoder(), ChainedFormatter::default());
        $algorithm = new Sha256();
        $signingKey = InMemory::base64Encoded($_ENV['JWT_SECRET']);

        $now = new \DateTimeImmutable();
        $token = $tokenBuilder
            ->issuedBy($_ENV['JWT_ISSUER'])
            ->permittedFor($_ENV['JWT_AUDIENCE'])
            ->identifiedBy(Uuid::uuid4()->toString())
            ->issuedAt($now)
            ->canOnlyBeUsedAfter($now)
            ->expiresAt($now->modify('+1 day'))
            ->withClaim('user', $user->toArray())
            ->getToken($algorithm, $signingKey);

        return $token->toString();
    }

    public static function isValid($encryptedToken): bool
    {
        $parser = new Parser(new JoseEncoder());
        try {
            $token = $parser->parse(
                $encryptedToken
            );

            $validator = new Validator();

            if (
                !$validator->validate(
                    $token,
                    new IssuedBy($_ENV['JWT_ISSUER']),
                    new PermittedFor($_ENV['JWT_AUDIENCE']),
                    new LooseValidAt(SystemClock::fromUTC(), leeway: null)
                )
            ) {
                return false;
            }

            return true;
        } catch (CannotDecodeContent|InvalidTokenStructure|UnsupportedHeaderFound $e) {
            return false;
        }
    }

    public static function decode($encryptedToken): bool|Token
    {
        $parser = new Parser(new JoseEncoder());
        try {
            return $parser->parse(
                $encryptedToken
            );
        } catch (CannotDecodeContent|InvalidTokenStructure|UnsupportedHeaderFound $e) {
            return false;
        }
    }
}

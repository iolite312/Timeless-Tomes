<?php

namespace App\Helpers;

use Lcobucci\JWT\Token\Parser;
use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Validation\Validator;
use Lcobucci\JWT\Token\InvalidTokenStructure;
use Lcobucci\JWT\Encoding\CannotDecodeContent;
use Lcobucci\JWT\Token\UnsupportedHeaderFound;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\RelatedTo;
use Lcobucci\JWT\Validation\Constraint\LooseValidAt;
use Lcobucci\JWT\Validation\Constraint\PermittedFor;

class TokenValidator
{
    public static function isValid($encryptedToken)
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
                    new RelatedTo('Temporary'),
                    new IssuedBy($_ENV['JWT_ISSUER']),
                    new PermittedFor($_ENV['JWT_AUDIENCE']),
                    new LooseValidAt(SystemClock::fromUTC(), leeway: null)
                )
            ) {
                return false;
            }

            return true;
        } catch (CannotDecodeContent|InvalidTokenStructure|UnsupportedHeaderFound $e) {
            // echo $e->getMessage();

            return false;
        }
    }
}

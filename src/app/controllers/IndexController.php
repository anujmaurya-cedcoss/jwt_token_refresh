<?php
use Phalcon\Mvc\Controller;
use Phalcon\Security\JWT\Token\Parser;
use Phalcon\Security\JWT\Builder;
use Phalcon\Security\JWT\Signer\Hmac;

class IndexController extends Controller
{
    public function indexAction()
    {
        // redirected to index view
    }

    public function jwtAction()
    {
        $token = $_POST['token'];
        $payload = decode($token);

        $new_token = refresh($payload);
        print_r($new_token);
        die;
    }
}
function decode($token)
{
    $parser = new Parser();
    $tokenObject = $parser->parse($token);
    return json_encode($tokenObject->getClaims()->getPayload()['name']);
}

function refresh($data)
{
    // Defaults to 'sha512'
    $signer = new Hmac();

    // Builder object
    $builder = new Builder($signer);

    $now = new DateTimeImmutable();
    $issued = $now->getTimestamp();
    $notBefore = $now->modify('-1 minute')->getTimestamp();
    $expires = $now->modify('+1 day')->getTimestamp();
    $passphrase = 'QcMpZ&b&mo3TPsPk668J6QH8JA$&U&m2';

    // Setup
    $builder
        ->setExpirationTime($expires) // exp
        ->setId('abcd123456789') // JTI id
        ->setIssuedAt($issued) // iat
        ->setIssuer('https://phalcon.io') // iss
        ->setNotBefore($notBefore) // nbf
        ->setSubject($data) // sub
        ->setPassphrase($passphrase) // password
    ;
    // Phalcon\Security\JWT\Token\Token object
    $tokenObject = $builder->getToken();
    // The token
    return $tokenObject->getToken();
}

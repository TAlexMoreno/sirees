<?php

namespace App\Security;

use AXS\ApiBundle\Controller\Api;
use AXS\ApiBundle\Utils\JWT;
use DateTime;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class CustomAuth extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = "app_login";

    public function __construct(private UrlGeneratorInterface $urlGenerator, private Api $api)
    {
        return;
    }

    public function authenticate(Request $request): Passport
    {
        $username = $request->request->get("username", "");
        $request->getSession()->set(Security::LAST_USERNAME, $username);
        return new Passport(
            new UserBadge($username),
            new PasswordCredentials($request->request->get("password", "")),
            [
                new CsrfTokenBadge('authenticate', $request->request->get("_csrf_token"))
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $jwt = JWT::generate(
            ["alg" => "HS256", "typ" => "JWT"], 
            ["iss" => "web", "exp" => (new DateTime())->modify("+1 day")->getTimestamp(), "sub" => $token->getUserIdentifier()],
            $this->api->getSecret()
        );
        $request->getSession()->set("apiToken", $jwt);
        return new JsonResponse([
            "success" => true,
            "redirect" => "/"
        ]);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        return new JsonResponse([
            "success" => false,
            "error" => $exception->getMessage(),
            "errno" => $exception->getCode()
        ]);
    }

    protected function getLoginUrl(Request $req): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}

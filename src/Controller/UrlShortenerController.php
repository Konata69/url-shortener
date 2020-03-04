<?php

namespace App\Controller;

use App\DTO\UrlDTO;
use App\Service\Shortener;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UrlShortenerController extends AbstractController
{
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @Route("/short", name="short")
     * @param Request $request
     * @param Shortener $shorter
     * @return JsonResponse
     * @throws Exception
     */
    public function short(Request $request, Shortener $shorter): JsonResponse
    {
        $dto = new UrlDTO($request->get('url'));

        if ($errors = $this->validate($dto)) {
            return $errors;
        }

        $url = $shorter->short($dto->getUrl());

        $urlString = $this->generateUrl('follow', ['hash' => $url->getHash()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse(['url' => $urlString]);
    }

    /**
     * @Route("/{hash}", name="follow")
     * @param string $hash
     * @param Shortener $shortener
     * @return JsonResponse|RedirectResponse
     */
    public function follow(string $hash, Shortener $shortener)
    {
        $url = $shortener->getUrlByHash($hash);

        if ($url === null) {
            return new JsonResponse([], 404);
        }

        return new RedirectResponse($url);
    }

    private function validate($dto): ?JsonResponse
    {
        $errors = $this->validator->validate($dto);

        if (count($errors->getIterator()) > 0) {
            $errorResponse = [];

            /** @var ConstraintViolationInterface $error */
            foreach ($errors as $error) {
                $errorResponse[] = [
                    'message' => $error->getMessage(),
                    'value' => $error->getInvalidValue(),
                ];
            }

            return new JsonResponse($errorResponse);
        }

        return null;
    }
}
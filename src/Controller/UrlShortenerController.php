<?php

namespace App\Controller;

use App\Entity\Url;
use App\Service\Shortener;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
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
        $url = new Url($request->get('url'));

        if ($errors = $this->validate($url)) {
            return $errors;
        }

        $url = $shorter->short($url->getUrl());

        return new JsonResponse(['hash' => $url->getHash()]);
    }

    private function validate(Url $url): ?JsonResponse
    {
        $errors = $this->validator->validate($url);

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
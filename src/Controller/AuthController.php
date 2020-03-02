<?php

namespace App\Controller;

use App\DTO\UserDTO;
use App\Service\AuthInterface;
use App\View\UserView;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Throwable;

class AuthController extends AbstractController
{
    //TODO Сделать группу роутов auth

    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @Route("/auth/register", methods={"POST"})
     * @param Request $request
     * @param AuthInterface $auth
     * @return JsonResponse
     */
    public function register(Request $request, AuthInterface $auth): JsonResponse
    {
        $dto = UserDto::fromJson($request->getContent());

        if ($errors = $this->validate($dto)) {
            return $errors;
        }

        try {
            $user = $auth->createUser($dto);
        } catch (Throwable $e) {
            //TODO Перенаправить исключение в ошибку валидации и вернуть респонс
            return new JsonResponse(['error' => 'Can\'t create user'], 500);
        }

        $userView = UserView::fromEntity($user);

        return new JsonResponse($userView->asArray());
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
<?php

namespace App\Controller;

use App\DTO\UrlDTO;
use App\Entity\User;
use App\Service\Shortener;
use App\View\UserUrlsView;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Throwable;

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
     * @param Shortener $shortener
     * @return JsonResponse
     * @throws Exception
     */
    public function short(Request $request, Shortener $shortener): JsonResponse
    {
        $dto = new UrlDTO($request->get('url'), $this->getUser());

        if ($errors = $this->validate($dto)) {
            return $errors;
        }

        $url = $shortener->short($dto);
        $urlString = $shortener->getFollowUrl($url->getHash());

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

    /**
     * @Route("/short/list", name="short_list")
     * @param UserUrlsView $view
     * @return JsonResponse
     */
    public function list(UserUrlsView $view): JsonResponse
    {
        //TODO Чем заменить ограничение неаутентифицированных пользователей?
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'You should be authenticated!');

        $urls = ($this->getUser())->getUrls();

        $view->setUrls($urls);

        return new JsonResponse($view->asArray());
    }

    /**
     * @Route("/short/delete/{id}", name="short_delete")
     * @param int $id
     * @param Shortener $shortener
     * @return JsonResponse
     */
    public function delete(int $id, Shortener $shortener): JsonResponse
    {
        //TODO Чем заменить ограничение неаутентифицированных пользователей?
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'You should be authenticated!');

        /** @var User $user */
        $user = $this->getUser();

        try {
            $shortener->delete($id, $user);
        } catch (Throwable $e) {
            return new JsonResponse(['error' => $e->getMessage()]);
        }

        return new JsonResponse(['is_delete' => 'true']);
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
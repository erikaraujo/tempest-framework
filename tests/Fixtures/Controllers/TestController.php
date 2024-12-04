<?php

declare(strict_types=1);

namespace Tests\Tempest\Fixtures\Controllers;

use Tempest\Http\Connect;
use Tempest\Http\Delete;
use Tempest\Http\Get;
use Tempest\Http\Head;
use Tempest\Http\Options;
use Tempest\Http\Patch;
use Tempest\Http\Post;
use Tempest\Http\Put;
use Tempest\Http\Response;
use Tempest\Http\Responses\Created;
use Tempest\Http\Responses\NotFound;
use Tempest\Http\Responses\Ok;
use Tempest\Http\Responses\Redirect;
use Tempest\Http\Responses\ServerError;
use Tempest\Http\Trace;
use Tempest\View\View;
use Tests\Tempest\Fixtures\Views\ViewWithResponseData;
use function Tempest\view;

final readonly class TestController
{
    #[Get(uri: '/test/{id}/{name}')]
    public function withParams(string $id, string $name): Response
    {
        return new Ok($id . $name);
    }

    #[Get(uri: '/test/{id:[0-9]+}/{name:.*}')]
    public function withCustomRegexParams(string $id, string $name): Response
    {
        return new Ok($id . $name);
    }

    #[Get(uri: '/test/{id:\d{1,9}}')]
    public function withComplexCustomRegexParams(string $id): Response
    {
        return new Ok($id);
    }

    #[Connect(uri: '/test')]
    #[Delete(uri: '/test')]
    #[Get(uri: '/test')]
    #[Head(uri: '/test')]
    #[Options(uri: '/test')]
    #[Patch(uri: '/test')]
    #[Post(uri: '/test')]
    #[Put(uri: '/test')]
    #[Trace(uri: '/test')]
    public function __invoke(): Response
    {
        return new Ok('test');
    }

    #[Get(uri: '/view')]
    public function withView(): View
    {
        return view(__DIR__ . '/../Views/overview.view.php', name: 'Brent');
    }

    #[Get(uri: '/redirect')]
    public function redirect(): Response
    {
        return new Redirect('/');
    }

    #[Get(uri: '/not-found')]
    public function notFound(): Response
    {
        return new NotFound('Not Found Test');
    }

    #[Get(uri: '/server-error')]
    public function serverError(): Response
    {
        return new ServerError('Server Error Test');
    }

    #[Get(uri: '/with-middleware', middleware: [
        TestMiddleware::class,
    ])]
    public function withMiddleware(): Response
    {
        return new Ok();
    }

    #[Get('/view-model-with-response-data')]
    public function viewWithResponseData(): Response
    {
        return (new Created(new ViewWithResponseData()))
            ->addHeader('x-from-view', 'true');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use OpenApi\Attributes as OA;

#[OA\Info(
    title: 'API de Quiz - Teste Quiz EBC',
    version: '0.3.0',
    description: 'API para gerenciamento de quizzes, participação de usuários e ranking.'
)]
class Controller extends BaseController
{
    use AuthorizesRequests;
    use ValidatesRequests;
}

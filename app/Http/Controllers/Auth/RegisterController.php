<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\Models\UserService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;
use Inertia\ResponseFactory;

class RegisterController extends Controller
{
    public function __construct(
        protected UserService $userService,
        protected ResponseFactory $inertia,
    ) {}

    public function registerView(): Response
    {
        return $this->inertia->render('Register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $user = $this->userService->create($data);

        event(new Registered($user));

        Auth::login($user);

        // Matches SessionController::authenticate(). Without it, the one route that
        // hands a brand-new user a session is open to session fixation.
        $request->session()->regenerate();

        return redirect('/');
    }
}

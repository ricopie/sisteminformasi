<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use RuntimeException;

abstract class ModuleController extends Controller
{
    private ?string $moduleName = null;

    /** Detect module name from the child controller namespace */
    protected function moduleName(): string
    {
        if ($this->moduleName === null) {
            $parts = explode('\\', static::class);
            $moduleIndex = array_search('Modules', $parts);

            if ($moduleIndex === false || ! isset($parts[$moduleIndex + 1])) {
                throw new RuntimeException(sprintf(
                    'Cannot detect module name from controller class: %s',
                    static::class
                ));
            }

            $this->moduleName = $parts[$moduleIndex + 1];
        }

        return $this->moduleName;
    }

    /** Render a view from the module's view namespace */
    protected function moduleView(string $view, array $data = []): View
    {
        return view($this->moduleName().'::'.$view, $data);
    }

    /** Return a standardized JSON response */
    protected function moduleJson(mixed $data, string $message = 'OK', int $code = 200): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'data' => $data,
        ], $code);
    }
}

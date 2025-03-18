<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Sentry\SentrySdk;

class CalculationController extends Controller
{
    /**
     * Handle the calculation request.
     */
    public function calculate(Request $request): JsonResponse
    {
        try {
            // This is easier than JS lol
            sleep(1);

            // check if the value is a number
            $validated = $request->validate([
                'value' => 'required|numeric',
            ]);

            $value = $validated['value'];

            if ($value === 0) {
                \Sentry\captureEvent([
                    'message' => 'Division by zero error',
                    'contexts' => ['data' => ['value' => $value]],
                ]);
                throw new \Exception('Division by zero error');
            }

            $result = 100 / $value;

            // just return a generic result if its a good value
            return response()->json([
                'success' => true,
                'message' => 'Form submitted successfully',
                'result' => $result,
            ]);
        } catch (\Exception $error) {
            \Sentry\captureException($error);
            
            return response()->json([
                'success' => false,
                'error' => $error->getMessage(),
            ], 500);
        }
    }
}


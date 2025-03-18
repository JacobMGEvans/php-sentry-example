<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculation Form</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Load Sentry from CDN 
        if (typeof window !== 'undefined') {
            const script = document.createElement('script');
            script.src = 'https://browser.sentry-cdn.com/7.64.0/bundle.min.js';
            script.crossOrigin = 'anonymous';
            script.onload = () => {
                // ...and initialize it
                window.Sentry.init({
                    dsn: "{{ config('sentry.dsn') }}",
                    tracesSampleRate: 1.0,
                });
            };
            document.head.appendChild(script);
        }
    </script>
</head>
<body class="flex min-h-screen flex-col items-center justify-center p-12 text-sm">
    <h1 class="text-lg font-semibold">Calculation Form</h1>

    <form id="calculationForm" class="w-full max-w-xs mt-4 space-y-2">
        <input
            type="text"
            id="inputValue"
            class="w-full p-2 border rounded focus:outline-none"
            placeholder="Enter a number"
        >
        <p id="errorMessage" class="text-red-500 hidden"></p>

        <button
            type="submit"
            class="w-full p-2 bg-black text-white rounded"
        >
            Calculate
        </button>
    </form>

    <div id="resultContainer" class="mt-4 hidden">
        <div id="resultContent"></div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('calculationForm');
            const input = document.getElementById('inputValue');
            const errorMessage = document.getElementById('errorMessage');
            const resultContainer = document.getElementById('resultContainer');
            const resultContent = document.getElementById('resultContent');

            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                errorMessage.classList.add('hidden');
                errorMessage.textContent = '';
                resultContainer.classList.add('hidden');
                
                const inputValue = input.value.trim();
                
                if (!inputValue) {
                    errorMessage.textContent = 'Enter a number';
                    errorMessage.classList.remove('hidden');
                    return;
                }
                
                const numValue = Number(inputValue);
                if (isNaN(numValue)) {
                    errorMessage.textContent = 'Invalid number';
                    errorMessage.classList.remove('hidden');
                    return;
                }
                
                try {
                    
                    let transaction;
                    // Check if Sentry is loaded and I can run it
                    if (window.Sentry) {
                        transaction = window.Sentry.startTransaction({
                            name: 'fetchCalculation',
                        });
                        window.Sentry.configureScope(scope => {
                            scope.setSpan(transaction);
                        });
                    }
                    
                    resultContent.textContent = 'Loading...';
                    resultContainer.classList.remove('hidden');
                    
                    const response = await fetch('/api/submit', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ value: numValue })
                    });
                    
                    const data = await response.json();
                    
                    if (!response.ok) {
                        throw new Error(data.error || 'Error fetching calculation');
                    }
                    
                    resultContent.textContent = `Result: ${data.result}`;
                    
                    if (transaction) {
                        transaction.finish();
                    }
                } catch (error) {
                    if (window.Sentry) {
                        window.Sentry.captureException(error);
                    }
                    
                    resultContent.innerHTML = `
                        <div class="p-3 text-sm text-red-600" role="alert">
                            <p>${error.message}</p>
                            <button onclick="document.getElementById('calculationForm').reset(); document.getElementById('resultContainer').classList.add('hidden');" class="underline">
                                Try again
                            </button>
                        </div>
                    `;
                }
            });
        });
    </script>
</body>
</html>


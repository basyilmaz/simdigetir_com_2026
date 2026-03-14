<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
</head>
<body style="font-family: Arial, sans-serif; color: #111827; line-height: 1.6; margin: 0; padding: 24px;">
    <div style="max-width: 640px; margin: 0 auto;">
        <p style="margin: 0 0 16px 0; white-space: pre-line;">{{ $bodyText }}</p>
        @if (!empty($context))
            <hr style="border: 0; border-top: 1px solid #e5e7eb; margin: 20px 0;">
            <p style="font-size: 12px; color: #6b7280; margin: 0 0 8px 0;">Context</p>
            <pre style="background: #f9fafb; border: 1px solid #e5e7eb; padding: 12px; border-radius: 6px; overflow-x: auto;">{{ json_encode($context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        @endif
    </div>
</body>
</html>


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
    <h1 style="color: #4F46E5;">Ciao {{ $user->name }}!</h1>

    <p>Ti ricordiamo che domani sei iscritto al workshop:</p>

    <div style="background: #F3F4F6; padding: 20px; border-radius: 8px; margin: 20px 0;">
        <h2 style="margin: 0 0 10px 0;">{{ $workshop->title }}</h2>
        <p style="margin: 0; color: #6B7280;">{{ $workshop->description }}</p>
        <p style="margin: 10px 0 0 0;">
            📅 {{ \Carbon\Carbon::parse($workshop->starts_at)->format('d/m/Y H:i') }}
            -
            {{ \Carbon\Carbon::parse($workshop->ends_at)->format('H:i') }}
        </p>
    </div>

    <p>A domani!</p>
    <p><strong>Internal Academy</strong></p>
</body>
</html>

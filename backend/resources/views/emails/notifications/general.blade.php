<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>{{ $notification->subject }}</title>
</head>

<body style="font-family: Arial, Helvetica, sans-serif; line-height:1.6;">

    <h2>
        {{ $notification->subject }}
    </h2>

    <p>
        Hello {{ $notification->user->first_name }},
    </p>

    <p>
        {!! nl2br(e($notification->content)) !!}
    </p>

    <br>

    <p>
        Thank you,<br>
        <strong>Smart Care Healthcare Platform</strong>
    </p>

</body>

</html>
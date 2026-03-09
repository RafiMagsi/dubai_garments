<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $subjectLine }}</title>
</head>
<body>
    <p>Hello,</p>

    <p>{!! nl2br(e($messageBody)) !!}</p>

    @if ($quote)
        <p><strong>Quote Reference:</strong> {{ $quote->quote_number ?: '#'.$quote->id }}</p>
    @endif

    @if ($lead)
        <p><strong>Lead Tracking:</strong> {{ $lead->tracking_code ?: '#'.$lead->id }}</p>
    @endif

    <p>Regards,<br>Dubai Garments Sales Team</p>
</body>
</html>

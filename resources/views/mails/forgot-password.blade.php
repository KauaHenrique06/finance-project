<x-mail.layout title="Password recovery">
    <h1 style="margin:0 0 16px; font-size:20px;">Password recovery</h1>

    <p style="margin:0 0 16px;">
        Hi {{ $user->name }}. We received a request to reset your account password. Use the
        code below to set a new one:
    </p>

    <p style="margin:0 0 16px; padding:16px; background-color:#f4f5f7; border-radius:6px; font-family:monospace; font-size:16px; word-break:break-all;">
        {{ $token }}
    </p>

    <p style="margin:0 0 16px;">
        The code expires on {{ $expiresAt->format('M d, Y \a\t H:i') }}.
    </p>

    <p style="margin:0; font-size:13px; color:#7b8794;">
        If you did not ask to change your password, just ignore this email. Your current
        password is still valid.
    </p>
</x-mail.layout>

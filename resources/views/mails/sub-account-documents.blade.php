<x-mail.layout title="Send your documents">
    <h1 style="margin:0 0 16px; font-size:20px;">Almost there, {{ $user->name }}!</h1>

    <p style="margin:0 0 16px;">
        Your receiving account has been created, but it still needs to be activated. Send
        the requested documents to unlock withdrawals and campaign contributions.
    </p>

    <x-mail.button :url="$onboardingUrl">
        Send documents
    </x-mail.button>

    <p style="margin:0 0 16px;">
        It only takes a few minutes. Once the review is done, we will email you as soon as
        the account is approved.
    </p>

    @if ($expiresAt)
        <p style="margin:0; font-size:13px; color:#7b8794;">
            Send them by {{ $expiresAt->format('M d, Y') }} to avoid having your payments blocked.
        </p>
    @endif
</x-mail.layout>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name') }}</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f5f7; font-family:Arial, Helvetica, sans-serif; color:#1f2933;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f5f7; padding:24px 12px;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" style="max-width:560px; background-color:#ffffff; border-radius:8px; overflow:hidden;">
                    <tr>
                        <td style="background-color:#1f2933; padding:20px 24px;">
                            <span style="color:#ffffff; font-size:18px; font-weight:bold;">{{ config('app.name') }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px; font-size:15px; line-height:1.6;">
                            {{ $slot }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:16px 24px; background-color:#f4f5f7; font-size:12px; color:#7b8794;">
                            This is an automated message, there is no need to reply.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

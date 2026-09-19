<table cellpadding="0" cellspacing="0" style="margin:24px 0;">
    <tr>
        <td style="background-color:#1f2933; border-radius:6px;">
            <a href="{{ $url }}" style="display:inline-block; padding:12px 24px; color:#ffffff; font-size:15px; font-weight:bold; text-decoration:none;">
                {{ $slot }}
            </a>
        </td>
    </tr>
</table>
<p style="margin:0 0 16px; font-size:12px; color:#7b8794; word-break:break-all;">
    If the button does not work, copy and paste this link into your browser:<br>{{ $url }}
</p>

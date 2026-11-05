<x-mail::message>
# Welcome to FUNAAB Workforce

Dear {{ $name }},

An account has been set up for you on **FUNAAB Workforce**, the University's staff records portal. You can use it to view your records, upload documents to your Registry file and complete your APER form.

You can sign in with either:

- **Staff No.:** {{ $staffNo }}
- **E-mail:** {{ $email }}

First, choose your own password:

<x-mail::button :url="$link">
Set my password
</x-mail::button>

This link works for {{ $days }} days. After that, use "Forgot password" on the sign-in page: {{ $loginUrl }}

If you did not expect this e-mail, please contact Registry.

Regards,<br>
Registry, Federal University of Agriculture, Abeokuta
</x-mail::message>

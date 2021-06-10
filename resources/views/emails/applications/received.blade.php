@component('mail::message')
Dear Mr./Mrs.  {{ $application->company_owner_name }},\
Thank you for applying for a {{ $application->account_type }} account at {{ config('app.name') }}.
We will review your application and contact you as soon as possible.

In the mean time, do not hesitate to contact us should you need any additional information or assistance.

Kind Regards,<br>
{{ config('app.name') }} Team
@endcomponent
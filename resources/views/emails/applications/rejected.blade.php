@component('mail::message')
Dear Mr./Mrs.  {{ $application->company_owner_name }},\
We regret to inform you that your application for a {{ $application->account_type }} account at {{ config('app.name') }} has been rejected due to the following reason: 
@component('mail::panel')
{{$application->reason}}
@endcomponent

Do not hesitate to contact us in the future should the rejection reason be resolved or should you need any additional infomation or assistance.

Kind Regards,<br>
{{ config('app.name') }} Team
@endcomponent
@component('mail::message')
## You have received a new client account application:

@component('mail::panel')
###### Requested Account Type: {{ $application->account_type }}
###### Company Name: {{ $application->company_name }}
###### Company Registration Number: {{ $application->company_registration_number }}
###### Company Owner Name : {{ $application->company_owner_name }}
###### Company Email: {{ $application->company_email }}
###### Company Phone:  {{ $application->company_phone }}
###### Company Website:  {{ $application->company_website ?? "N/A"}}
###### Company Address: {{ $application->company_address }}, {{ $application->company_city }}, {{ $application->company_country }}
###### Applied At: {{ $application->created_at->format('Y-m-d') }}
@endcomponent

Please login to the admin panel to confirm or reject.
@if(isset($url) && $url !== null)
@component('mail::button', ['url' => $url])
App Admin System
@endcomponent
@endif

Thanks,<br>
{{ config('app.name') }}
@endcomponent

@component('mail::message')
Dear Mr./Mrs.  {{ $account->company_owner_name }},\
We are happy to inform you that your application for a {{ $account->account_type }} account at {{ config('app.name') }} has been confirmed!\
Please find below your account details:

@component('mail::panel')
##### ACCOUNT
###### Account Type: {{ $account->account_type }}
###### Account Number: {{ $account->number }}
###### Creation Date: {{ $account->created_at->format('Y-m-d') }}
##### ACCOUNT USER INFO
###### Name: {{ $account->user->name }}
###### Email: {{ $account->user->email }}
@endcomponent

To finish your account registration, please reset your password.\

@if(isset($reset_password_url) && $reset_password_url !== null)
@component('mail::button', ['url' => $reset_password_url])
Reset Password
@endcomponent
@endif

Once your password is set, you can login to our administration panel to manage your account data  
@if($account->isSupplier())
and to upload your villas.
@else
and to connect to our api.
@endif

Do not hesitate to contact us should you need any additional information or assistance.

Kind Regards,<br>
{{ config('app.name') }} Team
@endcomponent
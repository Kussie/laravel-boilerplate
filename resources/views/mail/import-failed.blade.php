@component('mail::message')
    # Import Failed

    A import job has failed.
    Package Name: {{$packageName}}
    Import Date: {{$importDate}}
    Import Duration: {{$processTime}} minutes
    Failure Reason: {{$failureReason}}
    Last File Opened: {{$failFile}}


    Thanks,
    {{ config('app.name') }}
@endcomponent

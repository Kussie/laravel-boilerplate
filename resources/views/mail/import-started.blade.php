@component('mail::message')
    # Import Started

    A import job has started being processed, this process may take anywhere between 10 and 20 minutes.
    Package Name: {{$packageName}}
    Import Date: {{$importDate}}

    Thanks,
    {{ config('app.name') }}
@endcomponent

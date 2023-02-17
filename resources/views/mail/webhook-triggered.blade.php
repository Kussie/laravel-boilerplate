@component('mail::message')
    # The import process is about to start

    The import webhook has been triggered and a package has been added to the queue to be processed.
    Package Name: {{$packageName}}
    Trigger Date: {{$triggerDate}}

    Thanks,
    {{ config('app.name') }}
@endcomponent

@component('mail::message')
# Import Finished

A import job has finished being processed and is ready for approval.

## Import Details

* Rule Version: {{$ruleVersion}}
* Rule Type: {{strtoupper($ruleType)}}
* Package Name: {{$packageName}}
* New Errors/warnings: {{count($newErrors)}}
* Total Errors/warnings: {{$errorCount}}
* Import Date: {{$importDate}}
* Import Duration: {{$processTime}} minutes
* Preview Link: <a href="{{$links['preview']}}">{{$links['preview']}}</a>

@if ($newErrors && count($newErrors) >= 1)
## The following new warnings/errors were raised during this import
@foreach($newErrors as $warning)
* {{$warning}}
@endforeach

A full list of errors and warnings is attached to this email.
@endif

## Approve Version
If everything looks okay in the preview it can be made public by clicking the link below.
<a href="{{$links['approve']}}">{{$links['approve']}}</a>


Thanks,
{{ config('app.name') }}
@endcomponent

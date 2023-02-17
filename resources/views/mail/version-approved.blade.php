@component('mail::message')
# Version Approved

A new version of a ruleset has been approved.
* Rule Type: {{$version->type}}
* Rule Version: {{$version->version}}
* Link: <a href="{{route('aemc.home', ['ruleType' => $version->type, 'version' => $version->id])}}">{{route('aemc.home', ['ruleType' => $version->type, 'version' => $version->id])}}</a>

Thanks,
{{ config('app.name') }}
@endcomponent

Hello {{ $data['department'] }} Department!
<br><br>
{{ $data['content'] }}
<br>
@if($data['image'] != null )
	<img src="{{ $message->embed($data['image']) }}">
	<br><br>
@endif

<br>
Thank you!
<br>

<div @class([
        "rounded-md p-4",
        "bg-red-50"=>$type == "error",
        "bg-yellow-50"=>$type == "warning",
        "bg-green-50"=>$type == "success",
        "bg-blue-50"=>$type == "info"
        ] )>
	@php
		if (!isset($label)){
			$label = 'There were an error with your submission';
		}
	@endphp
	<div class="flex">
{{--		<div class="flex-shrink-0">--}}
{{--			<!-- Heroicon name: mini/x-circle -->--}}
{{--			<svg @class([--}}
{{--                "h-5 w-5",--}}
{{--                "text-red-400"=> $type == "error",--}}
{{--                "text-yellow-400"=> $type == "warning",--}}
{{--                "text-green-400"=> $type == "success",--}}
{{--                "text-blue-400"=> $type == "info",--}}
{{--                ]) xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"--}}
{{--			     aria-hidden="true">--}}
{{--				<path fill-rule="evenodd"--}}
{{--				      d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z"--}}
{{--				      clip-rule="evenodd"/>--}}
{{--			</svg>--}}
{{--		</div>--}}
		<div class="ml-3">
{{--			<h3  @class([--}}
{{--                "text-sm font-medium",--}}
{{--                "text-red-800"=> $type == "error",--}}
{{--                "text-yellow-800"=> $type == "warning",--}}
{{--                "text-green-800"=> $type == "success",--}}
{{--                "text-blue-800"=> $type == "info",--}}
{{--                ])>{{$label}}</h3>--}}
			<div  @class([
                "mt-2 text-sm",
                "text-red-700"=> $type == "error",
                "text-yellow-700"=> $type == "warning",
                "text-green-700"=> $type == "success",
                "text-blue-700"=> $type == "info",
                ])>
				<p>{{$message}}</p>
			</div>
		</div>
	</div>
</div>
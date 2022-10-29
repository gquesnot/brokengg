<div class="flex flex-col fixed bottom-4 right-0 w-1/4">
{{--	load all class--}}
	<div class="bg-red-100 border-red-400 text-red-700 text-red-500bg-blue-100 border-blue-400 text-blue-700 text-blue-500 bg-green-100 border-green-400 text-green-700 text-green-500 bg-yellow-100 border-yellow-400 text-yellow-700 text-yellow-500"></div>
	@foreach(\App\Enums\FLashEnum::cases() as $key => $value)
		<x-flash :type="$value"></x-flash>
	@endforeach
</div>
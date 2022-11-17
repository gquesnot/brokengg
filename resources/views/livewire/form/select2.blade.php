<div>
	<select  id="select2-{{$model}}"  wire:model="{{$model}}" class="mt-1 block w-48 pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
		@if($nullable)
			<option value=""></option>
		@endif
		@foreach($options as $option)
			<option value="{{$option['value']}}">Empty</option>
		@endforeach

	</select>
	@push('scripts')
		<script>
			$(document).ready(function () {
				$('#select2-{{$model}}').select2();
                $('#select2-{{$model}}').on('change', function (e) {
					@this.set('{{$model}}', $(this).val());
				});
			});
		</script>

	@endpush
</div>
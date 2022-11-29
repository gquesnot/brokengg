<div wire:ignore>
	@php
		$model_id = "select2-".str_replace('.', '-', $model);
	@endphp
	<select id="{{$model_id}}" wire:model="{{$model}}"
	        class="mt-1 block w-48 pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
		@if($nullable)
			<option value=""></option>
		@endif


		@if($optGroup)
			@foreach($options as $group => $groupOptions)
				<optgroup label="{{$group}}">
					@foreach($groupOptions as $option)
						<option value="{{$option['value']}}">{{$option['label']}}</option>
					@endforeach
				</optgroup>
			@endforeach
		@else
			@foreach($options as $option)
				<option value="{{$option['value']}}">{{$option['label']}}</option>
			@endforeach
		@endif

	</select>
</div>
@push('scripts')
	<script type="module">

        $(document).ready(function () {
            $('#{{$model_id}}').select2({
                placeholder: "{{ $placeholder }}",
                allowClear: {{$nullable ? 'true' : 'false'}},
            });
            $('#{{$model_id}}').on('change', function (e) {
                if ($(this).val() == ''){
                    @this.set('{{$model}}', null);
                }
                else{
                    @this.set('{{$model}}', $(this).val());
                }
            });
            window.addEventListener('select2-clear', function () {
                $('#{{$model_id}}').val(null).trigger('change');
			});
        });
	</script>

@endpush

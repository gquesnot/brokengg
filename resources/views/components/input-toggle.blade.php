
<div>

	<!-- Enabled: "bg-indigo-600", Not Enabled: "bg-gray-200" -->
	<button type="button" wire:click="$toggle('{{$model}}')"
	        @class(["bg-gray-200" => !$value,"bg-indigo-600" => $value,"relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"]) role="switch"
	        aria-checked="false">
		<span class="sr-only">Use setting</span>
		<!-- Enabled: "translate-x-5", Not Enabled: "translate-x-0" -->
		<span
			aria-hidden="true" @class(["pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out", "translate-x-0"=>!$value, "translate-x-5"=>$value])></span>
	</button>
</div>
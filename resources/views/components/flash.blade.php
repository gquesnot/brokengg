<div>
	@if(Session::has($type->value))
		<div class=" px-2 py-2 w-full" wire:click="forgetFlash('{{$type->value}}')">
			<div
				class="bg-{{$type->getColor()}}-100 border border-{{$type->getColor()}}-400 text-{{$type->getColor()}}-700 px-4 py-3 rounded flex items-center" role="alert">

				<div class="flex flex-col w-3/4">
					<strong class="font-bold">{{$type->getMessage()}}</strong>
					<span class=" sm:inline">{{ Session::get($type->value) }}</span>
				</div>

				<div class="flex justify-end w-1/4">
                <span class=" px-4 py-3">
                <svg class="fill-current h-6 w-6 text-{{$type->getColor()}}-500" xmlns="http://www.w3.org/2000/svg"

                     viewBox="0 0 20 20">
                    <title>Close</title>
                    <path
	                    d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                </svg>
            </span>
				</div>

			</div>
		</div>

	@endif
</div>
<section class="max-w-screen-xl mb-8">
    <label for="status" class="inline-block mb-3 font-bold">{{ __('Status') }}</label>
    <ul class="w-full sm:flex sm:flex-wrap">
        <li class="flex items-center px-3 py-2 mb-2 mr-2 bg-green-300 rounded-md">
            <img class="inline w-4 h-4 mr-3" src="svg/icon_all.svg" alt="icon">
            <span class="mr-2 font-bold">ALL</span>
            <p>{{ __('Bug & security fixes') }}</p>
        </li>
        <li class="flex items-center px-3 py-2 mb-2 mr-2 bg-yellow-300 rounded-md">
            <img class="inline w-4 h-4 mr-3" src="svg/icon_sec.svg" alt="icon">
            <span class="mr-2 font-bold">SEC</span>
            <p>{{ __('Security fixes only') }}</p>
        </li>
        <li class="flex items-center px-3 py-2 mb-2 mr-2 bg-red-300 rounded-md">
            <img class="inline w-4 h-4 mr-3" src="svg/icon_eol.svg" alt="icon">
            <span class="mr-2 font-bold">EOL</span>
            <p>{{ __('End of Life') }}</p>
        </li>
        <li class="flex items-center px-3 py-2 mb-2 mr-2 bg-blue-300 rounded-md">
            <img class="inline w-4 h-4 mr-3" src="svg/icon_fut.svg" alt="icon">
            <span class="mr-2 font-bold">FUT</span>
            <p>{{ __('Future release') }}</p>
        </li>
    </ul>
</section>

<footer class="py-4 leading-loose text-center lg:mt-32 bg-gray-50 md:leading-normal">
    {{ __('Brought to you by the lovely folks at') }} <a href="https://tighten.co/" class="border-hover">Tighten</a>.

    <div class="my-1 text-sm md:text-base">
        <a href="https://github.com/tighten/laravelversions" class="border-hover">{{ __('Source on GitHub') }}</a>
        |
        <a href="{{ route('api.versions.index') }}" class="border-hover">{{ __('Data available in JSON format') }}</a>
        |
        <a href="https://twitter.com/laravelversions" class="border-hover">{{ __('Follow on Twitter for important dates') }}</a>
    </div>

    @if (! App::isLocale('en'))
        <br>
        {{ __('Greetings from the author of the translations') }}
    @endif
</footer>

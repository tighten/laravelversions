<section>
    @if ($version->status == App\Models\LaravelVersion::STATUS_ACTIVE)
        <div class="flex items-center p-5 mb-4 border-2 border-green-300 rounded-md">
            <svg 
                class="mr-3 text-green-300 fill-current"
                width="15" height="15" 
                xmlns="http://www.w3.org/2000/svg">
                <path d="M7.5 0C3.365 0 0 3.364 0 7.5 0 11.635 3.365 15 7.5 15S15 11.635 15 7.5C15 3.364 11.636 0 7.5 0zm3.782 6.012L7.144 10.15a.925.925 0 01-.658.273.925.925 0 01-.659-.273L3.718 8.04a.925.925 0 01-.272-.658c0-.25.096-.483.272-.659a.925.925 0 01.659-.273c.249 0 .483.097.658.273l1.45 1.45 3.48-3.479a.925.925 0 01.658-.272.932.932 0 01.659 1.59z"  fill-rule="nonzero"/>
            </svg>                            
            <h2 class="mr-1 text-xl font-bold">Status:</h2>
            <p>{{ $statusText[$version->status] }}</p>
        </div>
    @elseif ($version->status == App\Models\LaravelVersion::STATUS_SECURITY)
        <div class="flex items-center p-5 mb-4 border-2 border-yellow-300 rounded-md">
            <svg 
                class="mr-3 text-yellow-300 fill-current"
                width="15" height="15" 
                xmlns="http://www.w3.org/2000/svg">
                <path d="M14.688 10.81L9.39 1.076c-.852-1.432-2.927-1.434-3.78 0L.312 10.811c-.87 1.463.183 3.317 1.889 3.317h10.598c1.704 0 2.76-1.852 1.89-3.317zM7.5 12.372a.88.88 0 01-.879-.878.88.88 0 011.758 0 .88.88 0 01-.879.878zm.879-3.514a.88.88 0 01-1.758 0V4.465a.88.88 0 011.758 0v4.392z"  fill-rule="nonzero"/>
            </svg>                           
            <h2 class="mr-1 text-xl font-bold">Status:</h2>
            <p>{{ $statusText[$version->status] }}</p>
        </div>
    @elseif ($version->status == App\Models\LaravelVersion::STATUS_FUTURE)
        <div class="flex items-center p-5 mb-4 border-2 border-blue-300 rounded-md">
            <svg 
                class="mr-3 text-blue-300 fill-current"
                width="15" height="15" 
                xmlns="http://www.w3.org/2000/svg">
                <path d="M7.5 0C3.365 0 0 3.364 0 7.5 0 11.635 3.365 15 7.5 15S15 11.635 15 7.5C15 3.364 11.636 0 7.5 0zm3.782 6.012L7.144 10.15a.925.925 0 01-.658.273.925.925 0 01-.659-.273L3.718 8.04a.925.925 0 01-.272-.658c0-.25.096-.483.272-.659a.925.925 0 01.659-.273c.249 0 .483.097.658.273l1.45 1.45 3.48-3.479a.925.925 0 01.658-.272.932.932 0 01.659 1.59z"  fill-rule="nonzero"/>
            </svg>                          
            <h2 class="mr-1 text-xl font-bold">Status:</h2>
            <p>{{ $statusText[$version->status] }}</p>
        </div>
    @else
        <div class="flex items-center p-5 mb-4 border-2 border-red-300 rounded-md">
            <svg 
                class="mr-3 text-red-300 fill-current"
                width="15" height="15" 
                xmlns="http://www.w3.org/2000/svg">
                <path d="M7.5 0C11.636 0 15 3.364 15 7.5S11.636 15 7.5 15 0 11.636 0 7.5 3.364 0 7.5 0zM5.268 4.218a.742.742 0 10-1.05 1.05L6.45 7.5 4.218 9.732a.742.742 0 101.05 1.05L7.5 8.55l2.232 2.232a.74.74 0 001.05 0c.29-.29.29-.76 0-1.05L8.55 7.5l2.232-2.232a.742.742 0 10-1.05-1.05L7.5 6.45z"  fill-rule="nonzero"/>
            </svg>                          
            <h2 class="mr-1 text-xl font-bold">Status:</h2>
            <p>{{ $statusText[$version->status] }}</p>
        </div>
    @endif
</section>

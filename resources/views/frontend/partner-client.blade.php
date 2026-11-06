<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Partner Client</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body>
    @include('layout.header')

    <section class="w-full bg-white pt-28 md:pt-40 pb-16 md:pb-20">
        <div class="max-w-7xl mx-auto px-4 md:px-6">

            <h2 class="text-xl md:text-2xl font-bold text-center mb-8 md:mb-10">
                Client & Partner Kami
            </h2>

            <!-- Grid logo — responsif dari 3 kolom (mobile) sampai 8 kolom (desktop) -->
            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-4 md:gap-6 items-center">

                @php
                    $logos = \App\Models\ClientPartner::all();
                @endphp

                @foreach($logos as $logo)
                <a href="{{ $logo->link ?? '#' }}" target="_blank" class="flex justify-center">
                    <img src="{{ asset('storage/' . $logo->image) }}"
                         class="h-8 md:h-12 object-contain transition duration-300 hover:scale-110"
                         alt="{{ $logo->name }}">
                </a>
                @endforeach

            </div>
        </div>
    </section>

    @include('layout.footer')
</body>
</html>
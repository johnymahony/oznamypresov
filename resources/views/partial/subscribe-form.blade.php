@php
    $hide = @$_COOKIE['zobrazit-formular'] === 'nie';
@endphp
@if($hide)
    <style>
        .form-hide {
            display: none;
        }
    </style>
@endif
<div>
    <div class="text-center page xxxx">
        <div class="right form-hide" onclick="$('.form-hide').hide(); setCookie('zobrazit-formular', 'nie', 365)"><img
                    src="/images/close.png" class="cursor-pointer" alt="zavrite formular"></div>
        <h1 class="text-4xl pt-12 form-hide">Dostávajte pravidelne oznamy na email.</h1>
        <ol class="my-6 form-hide text-left mx-auto" style="width: 300px;">
            <li>1. Zadajte email.</li>
            <li class="my-4">2. Nastavte si oblasť záujmu.</li>
            <li>3. Dostávajte relevantné oznamy.</li>
        </ol>
        {{csrf_field()}}
        <input type="hidden" value="{{time()}}" name="t">
        <input type="hidden" value="" name="x">
        <div class="flex justify-content-center align-items-center">

            <div class="rounded-full bg-white p-1 mx-auto flex flex-col md:flex-row">

                <input type="email" name="email" id="email" class="p-1 rounded-full mx-4 mb-1 md:my-1 text-black"
                       placeholder="janko@hrasko.sk">
                <button type="submit" class="btn mx-4 md:mr-0 btn-red my-0" onclick="$('.form-hide').show()">Zapísať sa</button>
            </div>
        </div>
        <div class="my-6 form-hide">
            <label class="form-check-label must-agree">
                <input name="agree" type="checkbox" class="form-check-input" value="1"> Vyplnením
                tohto formulára súhlasíte s <a href="{{url('zasady-ochrany-osobnych-udajov')}}" target="_blank" class="underline">ukladaním
                    a
                    spracuvaním vašej emailovej adresy</a> prostredníctvom tejto webovej stránky.
            </label>
        </div>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        {{--<p class="tCenter">Oznamy su filtrované na základe geografickej polohy. Vďaka tomu Vám budú doručené iba oznamy o ktoré máte naozaj--}}
        {{--záujem.</p>--}}
        {{--<p>Ak sa chcete zapísať na odber, alebo zmeniť nastavenia zasielania, zadajte Vášu emailovú adresu.</p>--}}
    </div>
    <script>
        $(document).ready(() => {
            if (getCookie('zobrazit-formular') === 'nie') {
                // $('.form-hide').hide()
            }
        });

    </script>
</div>
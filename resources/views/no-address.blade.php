@extends('layouts.frontend')

@section('content_frontend')
    <form method="post" action="{{url('/save-no-address').'/'.request()->hash}}" style="max-width: 800px; margin: 0px auto;">
        {{csrf_field()}}
        <div class="form-group row page policy-page">
            <h2>Kolaudovali ste len nedávno?</h2>
            <p>Najčastejsí dôvod, prečo chýba adresa je kôli tomu, že čerstvo skolaudované domy ešte niesú v oficiálnych zoznamoch radnice. Niekedy to trvá aj pol roka.</p>
            <p><b>V takomto prípade si zaznačte nejaké najbližšie číslo domu.</b></p>

            <p>
                Ak bývate na adrese už dlhšie, prosím vyplnte formulár nižšie a ja sa pokúsim s radnicou zisiť prečo sa Vaša adresa v zoznamoch nenachádza.
            </p>

            <label for="street" class="col-sm-5 col-form-label">Vaša celá adresa vrátane čísla domu:</label>
            <div class="mt-4">
                <input type="text" name="street" id="street" class="selectize-input" required
                       placeholder="Sem napíšte vašu adresu"
                       value="">
            </div>
        </div>
        <input type="submit" value="Odoslať" class="btn btn-success" style="width: 100%">
@endsection
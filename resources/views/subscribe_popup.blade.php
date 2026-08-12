<script>
    $(document).ready(function () {
        if (getCookie('zobrazit-formular') !== 'nie') {
            $('.close-icon').on('click', function () {
                setCookie('zobrazit-formular', 'nie', 365);
                $(this).closest('#subscribe_popup').fadeOut();
            })
            setTimeout(function () {
                $('#subscribe_popup').slideToggle();
            }, 7000);
        }
    })

</script>
<div id="subscribe_popup">
    <div class="panel">
        <div class="title clickable close-icon" data-effect="fadeOut">
            Odber úradných oznamov
            <span class="right"><i class="fa fa-times"></i></span>
        </div>
        <div class="body fff">
            <div class="mx-auto w-500">
                <span class="h4">Zapíšte sa</span>
                <br>
                {{csrf_field()}}
                <input type="hidden" value="{{time()}}" name="t">
                <input type="hidden" value="" name="x">
                <label for="email_s" class="col-form-label w-full">Vaša emailová adresa:</label>
                <input type="email" name="email" id="email_s" class="selectize-input w-full"
                       placeholder="@">
                <label class="form-check-label my-3 block must-agree">
                    <input name="agree" type="checkbox" class="form-check-input" value="1"> Súhlasím
                    s <a class="link" href="{{url('zasady-ochrany-osobnych-udajov')}}" target="_blank">ukladaním a
                        spracovaním mojej emailovej adresy</a>.
                </label>
                <button type="button" class="btn btn-success save-button mt-3 w-full">Zapísať sa</button>
            </div>
        </div>
    </div>
</div>
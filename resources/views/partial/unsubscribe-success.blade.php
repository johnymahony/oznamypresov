<h1>Nastavenia boli uložené</h1>
<p class="alert alert-success">Boli ste úspešne odhlásený z odberu.</p>
<form method="post" action="{{url('savefeedback')}}" id="target">
    {{csrf_field()}}

    <br>
    <div class="form-group mt10">
        <label for="comment">Prosím, napíšte jednou vetou prečo sa odhlasujete:</label>
        <br>
        <br>
        <input name="reason" type="submit" value="Dostával som príliž veľa emailov" class="btn btn-warning mt-2">
        <input name="reason" type="submit" value="Správy nemali dobrý informačný charakter" class="btn btn-warning mt-2">
        <input name="reason" type="submit" value="Nebaví ma to" class="btn btn-warning mt-2">
        <br>
        <br>
        <textarea class="form-control" rows="5" name="comment" placeholder="Váš postreh" id="comment"></textarea>
    </div>
    <input name="reason" type="submit" value="Odoslať" class="btn btn-warning mt-2" id="submit-button">
</form>

<script>
    $(document).ready(function(){
      // $( "#target" ).submit(function( event ) {
      //   alert( "Handler for .submit() called." );
      //   event.preventDefault();
      // });

    });
</script>
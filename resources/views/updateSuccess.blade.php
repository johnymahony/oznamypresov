@extends('layouts.frontend')

@section('content_frontend')
    <script type="text/javascript">
        $(document).ready(function () {
            setCookie('zobrazit-formular', 'nie', 365);
        });
    </script>
    <div class="page">

        <h1 class="my-4 text-4xl">Skontrolujte svoju emailovú schránku</h1>
        <p class="alert alert-success">Na vašu emailovú adresu sme poslali odkaz, kde sa dozviete ďalší postup. Z
            bezpečnostných dôvodov je odkaz platný 15 minút</p>
    </div>
@endsection
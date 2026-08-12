@if(city()->googleAnalyticsTag())
    <script async src="https://www.googletagmanager.com/gtag/js?id={{city()->googleAnalyticsTag()}}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }

        gtag('js', new Date());

        gtag('config', '{{city()->googleAnalyticsTag()}}');
    </script>
@endif
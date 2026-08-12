<input class="bg-gray-200 px-4 py-2 rounded-full w-full" name="search" placeholder="Vyhľadať..." value="{{$search}}"
       id="search">

<select class="bg-gray-200 px-4 py-2 mt-2 rounded-full w-full" id="rok" name="rok">
    <option value="">Posledný rok</option>
    <option value="cele-obdobie" @if($year === 'cele-obdobie')selected="selected"@endif>Celé obdobie</option>
    @for($y = 2018; $y <= date('Y'); $y++)
        <option value="{{$y}}" @if($year == $y)selected="selected"@endif>{{$y}}</option>
    @endfor
</select>
<div class="my-2 checkboxes">

    @foreach(\App\Category::orderBy('name')->get() as $category)
        <div class="">
            <label class="container">{{$category->name}}
                <input type="checkbox" id="{{$category->slug}}" value="{{$category->slug}}"
                       name="cat[]"
                       class="cats"
                       @if(in_array($category->slug, $categories))checked="checked"@endif>
                <span class="checkmark"></span>
            </label>
        </div>
    @endforeach
    @if($filter)
        <a href="/" class="link">zrušiť filter</a>
    @endif
</div>
@include('components.button', [
'label' => 'Filtrovať',
'type' => 'button',
'classes' => 'w-full search-button btn-red',
'icon' => 'fa-search'
])

<script>
    $(document).ready(() => {
        var input = document.getElementById("search");

// Execute a function when the user releases a key on the keyboard
        input.addEventListener("keyup", function (event) {
            // Number 13 is the "Enter" key on the keyboard
            if (event.keyCode === 13) {
                // Cancel the default action, if needed
                event.preventDefault();
                // Trigger the button element with a click
                document.getElementsByClassName("search-button")[0].click();
            }
        });
        $('.search-button').click(() => {
            let search = $('#search').val();
            let url = '{{url('/filter')}}';
            if (search) {
                url += '/' + encodeURIComponent(search);
            }


            let year = $('#rok').val();

            if (year) {
                url += '/' + year;
            }
            var selected = [];
            $('.checkboxes input:checked').each(function () {
                selected.push($(this).val());
            });
            if (selected.length > 0) {
                url += '/' + selected.join('/')
            }
            console.log(url);
            window.location = url;
        })
    });
</script>

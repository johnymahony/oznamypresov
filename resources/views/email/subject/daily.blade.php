@php
    $categories = [];
    $noteUntil = false;
    foreach($boards as $board) {
        @$categories[trim($board->category->name)]++;
        if($board->note_until && !preg_match("/0000/", $board->note_until) && !preg_match("/1970/", $board->note_until)) {
            $noteUntil = true;
        }
    }
    $subject = [];
    foreach ($categories as $name => $count) {
        $subject[] = "$name".($count > 1 ? "({$count}x)" :'');
    }
@endphp {{$noteUntil ? 'Možnosť pripomienkovať: ' : ''}}{{implode(", ", $subject)}}
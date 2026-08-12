@extends('layouts.frontend')

@section('content_frontend')
<div>
<div class="wrapper">
    @foreach($cities as $city)

      <div class="photos-cities">
        <h2 class="title_city">{{$city->humanName()}}</h2>
        <a href="#s"><img class="img_city" src="/images/{{strtolower($city)}}.jpg" /></a>
      </div>


    @endforeach
     </div>
</div>
    <style>
    .wrapper {
    margin-top: 30px;
        display: grid;
        justify-items: center;
        grid-template-columns: 1fr 1fr 1fr;
        grid-template-columns: repeat(auto-fill, 400px);
            align-items: center;
            justify-content: center;
            justify-items: center;
        grid-gap: 30px 30px;
      }

      .photos-cities {
      margin: 30px;
      -webkit-box-shadow: 0px 0px 5px 5px rgba(34, 60, 80, 1);
      -moz-box-shadow: 0px 0px 5px 5px rgba(34, 60, 80, 1);
      box-shadow: 0px 0px 5px 5px rgba(34, 60, 80, 1);

      }

      .photos-cities:hover {
      transition: all 0.5s;
     -webkit-box-shadow: 0px 0px 5px 12px rgba(34, 60, 80, 1);
     -moz-box-shadow: 0px 0px 5px 12px rgba(34, 60, 80, 1);
     box-shadow: 0px 0px 5px 12px rgba(34, 60, 80, 1);
                         }

      .title_city {
        text-align: center;
        margin-bottom: 10px;

      }

//       .img_city {
//           width: 150px;
//           -webkit-box-shadow: 1px 0px 13px 0px rgba(34, 60, 80, 1);
//           -moz-box-shadow: 1px 0px 13px 0px rgba(34, 60, 80, 1);
//           box-shadow: 1px 0px 13px 0px rgba(34, 60, 80, 1);
//         }

//         .img_city:hover {
//           transition: all 0.5s;
//           -webkit-box-shadow: 1px 0px 13px 9px rgba(34, 60, 80, 1);
//           -moz-box-shadow: 1px 0px 13px 9px rgba(34, 60, 80, 1);
//           box-shadow: 1px 0px 13px 9px rgba(34, 60, 80, 1);
//         }
    </style>
@endsection
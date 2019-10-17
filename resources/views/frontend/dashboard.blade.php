@extends('layouts.app')

@section('content')
<section>
  
    <div class="container">
        <div class="row">
            <div id="header" class="col-md-3">
               @if($benifinary["user_type"] =='1') 
                @include('layouts.user-inner.left-menu')
               @else
               @include('layouts.user-inner.left-corp-menu')
               @endif
            </div>
            
            <div class="col-md-9 dashbord-white">
           <div class="form-section">
                    <div class="row marB10">
                        <div class="col-md-12">
                            <h3 class="h3-headline">{{trans('forms.DashboradIndividual.heading')}}</h3>
                        </div>
                    </div>
             </div>   
        </div>

    </div>
</div>
</section>

  





@endsection

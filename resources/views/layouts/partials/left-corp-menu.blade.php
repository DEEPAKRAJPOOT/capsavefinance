fsagjh
<div class="list-section">
	     <div class="kyc">
		   <h2>KYC</h2>
		   <p class="marT15 marB15">Individual Natural Person (director, shareholder, Ultimate Beneficial Owner)</p>
		    <ul class="menu-left">
                    
                    
                    
		    <li><a class="<?=(Route::currentRouteName()=="company_profile-show")?'active':'' ?>" href="{{route('company_profile-show')}}">Company Details</a></li>
                    
                    <li><a class="<?=(Route::currentRouteName()=="company-address-show")?'active':'' ?>" href="{{route('company-address-show')}}">Address Details</a></li>
                    <li><a class="<?=(Route::currentRouteName()=="shareholding_structure")?'active':'' ?>" href="{{route('shareholding_structure')}}">Shareholding Structure</a></li>
                    <li><a class="<?=(Route::currentRouteName()=="financial-show")?'active':'' ?>" href="{{route('financial-show')}}">Financial Information</a></li>
                    <li><a class="<?=(Route::currentRouteName()=="documents-show")?'active':'' ?>" href="{{route('documents-show')}}">Documents & Declaration</a></li>
		    </ul>
                   <ul class="menu-left">
                        <li>
                            <a  href="{{ route('frontend_logout') }}" onclick="event.preventDefault();
                            document.getElementById('logout-form').submit();">
                            Sign Out
                            </a>
                            <form id="logout-form" action="{{ route('frontend_logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </li>

                        </ul>
		</div>
	   </div>
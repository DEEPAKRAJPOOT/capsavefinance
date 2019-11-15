fsagjh
<div class="list-section">
	     <div class="kyc">
		   <h2>KYC</h2>
		   <p class="marT15 marB15">Individual Natural Person (director, shareholder, Ultimate Beneficial Owner)</p>
		    <ul class="menu-left">
                    
		    <li>Company Details</li>
                    
                    <li>Address Details</li>
                    <li>Shareholding Structure</li>
                    <li>Financial Information</li>
                    <li>Documents & Declaration</li>
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
<ul class="main-menu">
	<li>
		<a href="" class="active">Summary</a>
	</li>
        <li>
		<a href="{{ route('lms_get_bank_account', [ 'user_id' => $userInfo->user_id ]) }}">Bank Account</a>
	</li>
	<li>
		<a href="{{ route('lms_get_application_invoice', [ 'user_id' => $userInfo->user_id ]) }}">View Invoices</a>
	</li>
	<li>
		<a href="">Repayment History</a>
	</li>
	<li>
		<a href="">Charges</a>
	</li>
	<li>
		<a href="">SOA</a>
	</li>
	<li>
		<a href="">Bank Account</a>
	</li>
</ul>
<div class="col-12 dataTables_wrapper mt-4">
    <div class="overflow">
        <div class="dataTables_wrapper dt-bootstrap4 no-footer">
            <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive ps ps--theme_default" data-ps-id="0b57d57f-c517-e65f-5cf6-304e01f86376">
                        <table id="unsettledTransactions" class="table table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                            <thead>
                                <tr role="row">                                                   
                                    <th>Trans Date</th>       
                                    <th>Invoice No</th>       
                                    <th>Trans Type</th>		
                                    <th>Total Repay Amt</th>
                                    <th>Outstanding Amt</th>
                                    {{-- @if($paymentId)<th>Payment Date</th>@endif --}}
                                    @if($paymentId)<th>Pay</th>@endif
                                    <th><input type="checkbox" id="checkAll" onchange="apport.selectAllChecks(this.id)"></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <div id="unsettledTransactions_processing" class="dataTables_processing card" >Processing...</div>
                </div>
            </div>
        </div>
    </div>
</div>
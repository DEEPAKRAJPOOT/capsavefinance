<?php

namespace App\Jobs;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Exception;
use Carbon\Carbon;
use Storage;

class FactFileTransferJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $date;
    private $journalSourcePath;
    private $paymentSourcePath;
    private $tally_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($date,$journalSourcePath,$paymentSourcePath,$tally_id)
    {
        $this->date = $date;
        $this->journalSourcePath = $journalSourcePath;
        $this->paymentSourcePath = $paymentSourcePath;
        $this->tally_id = $tally_id;
        $tallyUpdate = \DB::table('tally')->where('id',$this->tally_id)->update(['is_sftp_transfer'=>1]);

    }

    /**
     * Execute the job.
     *
     * @return void
     */

    public function handle()
    {
        try {
            $journalDestinationPath = NULL;
            $datewiseJournalPath = NULL;
            $paymentDestinationPath = NULL;
            $datewisePaymentPath = NULL;
            if (Storage::exists($this->journalSourcePath)) {
                $journalDestinationPath = 'Daily_Journal_FACT_File/CAPFIN_SCF_FACT_Journal' . '.xlsx';
                $datewiseJournalPath = 'Datewise_Journal_FACT_File/CAPFIN_SCF_FACT_Journal'. '_' . $this->date . '.xlsx';
            }
                
            if (Storage::exists($this->paymentSourcePath)) {
                $paymentDestinationPath = 'Daily_Payment_FACT_File/CAPFIN_SCF_FACT_Payment'. '.xlsx';
                $datewisePaymentPath = 'Datewise_Payment_FACT_File/CAPFIN_SCF_FACT_Payment' . '_' . $this->date . '.xlsx';
            }

            // Upload to SFTP server
            if((isset($journalDestinationPath) && isset($datewiseJournalPath)) || (isset($paymentDestinationPath) && isset($datewisePaymentPath))){
                if(isset($journalDestinationPath)){
                    $factJournalUpload = Storage::disk('fact_ftp')->put($journalDestinationPath, Storage::get($this->journalSourcePath));
                }
                if(isset($datewiseJournalPath)){
                    $factJournalUploadDate = Storage::disk('fact_ftp')->put($datewiseJournalPath, Storage::get($this->journalSourcePath));
                }
                if(isset($paymentDestinationPath)){
                    $factPaymentUpload = Storage::disk('fact_ftp')->put($paymentDestinationPath,  Storage::get($this->paymentSourcePath));
                }
                if(isset($datewisePaymentPath)){
                    $factPaymentUploadDate = Storage::disk('fact_ftp')->put($datewisePaymentPath, Storage::get($this->paymentSourcePath));
                }
                    
                if($factJournalUpload || $factJournalUploadDate || $factPaymentUpload || $factPaymentUploadDate){
                    $tallyUpdate = \DB::table('tally')->where('id',$this->tally_id)->update(['is_sftp_transfer'=>2]);
                }else{
                    $tallyUpdate = \DB::table('tally')->where('id',$this->tally_id)->update(['is_sftp_transfer'=>0]);
                    throw new Exception("All file transfer on sftp server failed!  Tally Id: = ".$this->tally_id." and executed at" .Carbon::now()."!");
                }
            }else{
                $tallyUpdate = \DB::table('tally')->where('id',$this->tally_id)->update(['is_sftp_transfer'=>0]);
                throw new Exception("File Path Missing! Tally Id: = ".$this->tally_id." and executed at" .Carbon::now()."!");
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}

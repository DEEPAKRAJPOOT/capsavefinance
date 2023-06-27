<?php

namespace App\Jobs;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Exception;
use League\Flysystem\Sftp\SftpAdapter;
use League\Flysystem\Filesystem;
use Carbon\Carbon;
use League\Flysystem\Adapter\Ftp as Adapter;
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
            if( isset($journalDestinationPath) && isset($datewiseJournalPath) && isset($paymentDestinationPath) && isset($datewisePaymentPath)){
                $sftpAdapter = new Adapter(config('filesystems.disks.fact_ftp'));
                $sftpAdapter->connect();
                if(!$sftpAdapter->isconnected()){
                    throw new Exception("SFTP connection failure for tally_id = ".$this->tally_id." and executed at" .Carbon::now()."!");
                }else{
                    $filesystem = new Filesystem($sftpAdapter);
                    $factJournalUpload =  $filesystem->put($journalDestinationPath, Storage::get($this->journalSourcePath));
                    $factJournalUploadDate = $filesystem->put($datewiseJournalPath, Storage::get($this->journalSourcePath));
                    $factPaymentUpload = $filesystem->put($paymentDestinationPath,  Storage::get($this->paymentSourcePath));
                    $factPaymentUploadDate = $filesystem->put($datewisePaymentPath, Storage::get($this->paymentSourcePath));
                    
                    if($factJournalUpload && $factPaymentUpload && $factJournalUploadDate && $factPaymentUploadDate){
                        $tallyUpdate = \DB::table('tally')->where('id',$this->tally_id)->update(['is_sftp_transfer'=>2]);
                    }else{
                        $tallyUpdate = \DB::table('tally')->where('id',$this->tally_id)->update(['is_sftp_transfer'=>0]);
                        throw new Exception("All file transfer on sftp server failed!  Tally Id: = ".$this->tally_id." and executed at" .Carbon::now()."!");
                    }
                }
            }else{
                $tallyUpdate = \DB::table('tally')->where('id',$this->tally_id)->update(['is_sftp_transfer'=>0]);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}

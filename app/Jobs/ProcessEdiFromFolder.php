<?php

namespace App\Jobs;

use App\Models\Edi_data;
use App\Providers\EdiServiceProvider;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessEdiFromFolder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Edi_data $Edi_data)
    {
        //
        $this->edi_data = $Edi_data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $directory = 'app/edi/'
        $files = Storage::allFiles($directory);
		print_r($files);
		
    }
}

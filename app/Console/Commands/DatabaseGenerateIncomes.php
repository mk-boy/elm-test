<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Income;

class DatabaseGenerateIncomes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:database-generate-incomes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        set_time_limit(300);

        $token = env("API_TOKEN");
        $host = env("API_HOST");
        $page = 1;

        $url = $host . "/api/incomes?dateFrom=1971-01-01&dateTo=" . date("Y-m-d") . "&page=" . $page ."&key=" . $token;

        while (true) {
            if ( ($page % 60) == 0 ) sleep(5);

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_URL => $url,
            ]);
    
            $response = curl_exec($curl);
            $responseData = json_decode($response, true);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $errorMessage = curl_error($curl);
    
            if ($response === false) {
                throw new \Exception("cURL error: " . $errorMessage);
            }
            
            if ($httpCode >= 400) {
                throw new \Exception("API error: HTTP " . $httpCode);
            }
            curl_close($curl);

            if ($responseData["meta"]["last_page"] < $page) {
                break;
            } else {
                foreach ($responseData["data"] as $elem) {
                    Income::create([
                        'data' => $elem
                    ]);
                }
            }

            $page++;

            usleep(500000);
        }
    }
}

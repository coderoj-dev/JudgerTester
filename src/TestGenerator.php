<?php

namespace JudgerTest\Generator;

class TestGenerator
{
    private $dataNo;
    private $url;

    public function __construct($no)
    {
        $this->dataNo = $no;
        $this->setUrl();
    }

    /*
    - set url from .env using Dotenv package
    */
    public function setUrl()
    {
        $dotenv = \Dotenv\Dotenv::createMutable('./');
        $dotenv->load();
        $this->url = $_ENV['JUDGER_URL'];
    }

    public function getData()
    {
        $data             = file_get_contents("./data/" . $this->dataNo . "/data.json");
        $data             = json_decode($data);
        $data->time_limit = sprintf('%0.3f', ($data->time_limit / 1000));
        return (object) $data;
    }

    public function preparePostData($dataFile)
    {
        $data = [
            'source_code'                 => base64_encode(file_get_contents("./data/" . $this->dataNo . "/source.cpp")),
            'language'                    => $dataFile->language,
            'time_limit'                  => $dataFile->time_limit,
            'memory_limit'                => $dataFile->memory_limit,
            'input'                       => base64_encode(file_get_contents("./data/" . $this->dataNo . "/in.txt")),
            'expected_output'             => base64_encode(file_get_contents("./data/" . $this->dataNo . "/out.txt")),
            'checker_type'                => $dataFile->checker_type,
            'default_checker'             => $dataFile->default_checker,
            'custom_checker'              => "",
            'compile_file'                => "test_compile",
            'checker_compile_file'        => "test_checker_compile",
            'delete_compile_file'         => 1,
            'delete_checker_compile_file' => 1,
            'api_type'                    => 'submission',
        ];

        //print_r(json_encode($data));

        return $data;
    }

    public function sendJudger($data)
    {
        $client   = new \GuzzleHttp\Client();
        $response = $client->request('POST', $this->url . '/api/api.php', [
            'form_params' => $data,
        ]);
        return json_decode($response->getBody());
    }

    public function outputCompareAssert($dataFile, $response)
    {
        $assert = strtolower($dataFile->expected_verdict) == strtolower($response->status->status) ? true : false;
        return (object) [
            'assert'      => $assert,
            'assertError' => !$assert ? "Erro found" : "",
        ];
    }

    public function msg($key, $value)
    {
        echo "     - {$key}: {$value}\n";
    }

    public function check()
    {
        $dataFile = $this->getData();

        //print_r($dataFile);
        $postData = $this->preparePostData($dataFile);
        $response = $this->sendJudger($postData);

        $this->msg("Language", $dataFile->language);
        $this->msg("Time    ", $response->time . " (" . $dataFile->time_limit . ") - s");
        $this->msg("Memory  ", $response->memory . " (" . $dataFile->memory_limit . ") - kb");
        $this->msg("Verdict ", $response->status->status . " (" . $dataFile->expected_verdict . ")");
        echo "\n";

        return $this->outputCompareAssert($dataFile, $response);
    }

}

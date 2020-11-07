<?php
class block_proprofs extends block_base {
    public function init() {
        $this->title = get_string('pluginname', 'block_proprofs');
    }
    // The PHP tag and the curly bracket for the class definition 
    // will only be closed after there is another function added in the next section.

    function CallAPI($method, $url, $data = false)
    {
        $curl = curl_init();

        switch ($method)
        {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);

                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_PUT, 1);
                break;
            default:
                if ($data)
                    $url = sprintf("%s?%s", $url, http_build_query($data));
        }

        // Optional Authentication:
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);

        curl_close($curl);

        return $result;
    }

    public function get_content() {
        if ($this->content !== null) {
          return $this->content;
        }

        $url = "https://www.proprofs.com/api/classroom/v1/reports/users/";

        $data = [
            "token" => "<token>",
            "username" => "<username>",
            "start" => 1,
            "num" => 100
        ];

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS,  json_encode($data));
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Content-Type: application/json'
        ]);

        $response = json_decode(curl_exec($curl), true);
        curl_close($curl);

        global $DB;
        $DB->

        $this->content->header = "ProProfs";

        if($response["status"] == "SUCCESS") {
            $studentList = "Name, ID, Email";
            foreach($response["result"] as $user) {
                foreach($user["Group"] as $group) {
                    if($group == "1234-1S") {
                        $studentList = $studentList . "<br>" . 
                            $user["Name"] . " " .
                            $user["UID"] . " " .
                            $user["Email"];
                    }
                }
            }
            
            $this->content->text   = $studentList;
            // $this->content->footer = "";
        } else if($response["status"] == "ERROR") {
            $this->content->text = "An error occured: " . $response["error"];
        } else {
            $this->content->text = $response["status"];
        }
     
        return $this->content;
    }
}

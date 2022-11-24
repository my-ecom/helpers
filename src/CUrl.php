<?php

namespace oangia;

class CUrl
{
    private $curl = null;
    private $headers = [];
    private $options = [];
    private $mobile = false;
    private $json_data = false;
    private $userAgent = '';
    private $UA = [
        'browser' => [
            'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.1 Safari/537.36',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.0 Safari/537.36',
            'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.0 Safari/537.36',
            'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:40.0) Gecko/20100101 Firefox/40.1',
            'Mozilla/5.0 (Windows NT 6.3; rv:36.0) Gecko/20100101 Firefox/36.0',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10; rv:33.0) Gecko/20100101 Firefox/33.0',
            'Mozilla/5.0 (X11; Linux i586; rv:31.0) Gecko/20100101 Firefox/31.0',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_3) AppleWebKit/537.75.14 (KHTML, like Gecko) Version/7.0.3 Safari/7046A194A',
            'Mozilla/5.0 (iPad; CPU OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A5355d Safari/8536.25',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_8) AppleWebKit/537.13+ (KHTML, like Gecko) Version/5.1.7 Safari/534.57.2',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_3) AppleWebKit/534.55.3 (KHTML, like Gecko) Version/5.1.3 Safari/534.53.10'
        ],
        'mobile' => [
            'Mozilla/5.0 (Linux; U; Android 4.0.3; ko-kr; LG-L160L Build/IML74K) AppleWebkit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30',
            'Mozilla/5.0 (Linux; U; Android 4.0.3; de-ch; HTC Sensation Build/IML74K) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30',
            'Mozilla/5.0 (Linux; U; Android 2.3; en-us) AppleWebKit/999+ (KHTML, like Gecko) Safari/999.9',
            'Mozilla/5.0 (Linux; U; Android 2.3.5; zh-cn; HTC_IncredibleS_S710e Build/GRJ90) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1',
            'Mozilla/5.0 (BlackBerry; U; BlackBerry 9900; en) AppleWebKit/534.11+ (KHTML, like Gecko) Version/7.1.0.346 Mobile Safari/534.11+',
            'Mozilla/5.0 (BlackBerry; U; BlackBerry 9860; en-US) AppleWebKit/534.11+ (KHTML, like Gecko) Version/7.0.0.254 Mobile Safari/534.11+',
            'Mozilla/5.0 (BlackBerry; U; BlackBerry 9850; en-US) AppleWebKit/534.11+ (KHTML, like Gecko) Version/7.0.0.254 Mobile Safari/534.11+',
            'Mozilla/5.0 (BlackBerry; U; BlackBerry 9850; en-US) AppleWebKit/534.11+ (KHTML, like Gecko) Version/7.0.0.115 Mobile Safari/534.11+',
            'Mozilla/5.0 (iPad; CPU OS 7_0 like Mac OS X) AppleWebKit/537.51.1 (KHTML, like Gecko) CriOS/30.0.1599.12 Mobile/11A465 Safari/8536.25 (3B92C18B-D9DE-4CB7-A02A-22FD2AF17C8F)',
            'Mozilla/5.0 (iPad; CPU OS 7_0 like Mac OS X) AppleWebKit/537.51.1 (KHTML, like Gecko) Version/7.0 Mobile/11A465 Safari/9537.53',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 6_1_4 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10B350 Safari/8536.25',
            'Mozilla/5.0 (iPad; CPU OS 5_1_1 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Version/5.1 Mobile/9B206 Safari/7534.48.3'
        ]
    ];

    public function json_data()
    {
        $this->json_data = true;
    }

    public function mobile()
    {
        $this->mobile = true;
    }

    public function json()
    {
        $this->headers[] = 'Accept: application/json; charset=utf-8';
        if ($this->json_data) {
            $this->headers[] = 'Content-Type: application/json; charset=utf-8';
        } else {
            $this->headers[] = 'Content-Type: application/x-www-form-urlencoded; charset=utf-8';
        }
    }

    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;
    }

    public function setOpt($key, $value)
    {
        $this->options[$key] = $value;
    }

    public function setHeader($header)
    {
        $this->headers[] = $header;
    }

    public function connect($method, $url, $data = null)
    {
        $this->curl = curl_init();

        curl_setopt($this->curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($this->curl, CURLOPT_URL, $url);

        $this->userAgent();
        $this->detectSSL($url);
        $this->setHeaders();
        $this->setOptions();

        if ($data) {
            if ($this->json_data) {
                $data = json_encode($data);
            } else {
                $data = http_build_query($data);
            }
        }

        switch ($method) {
            case 'GET':
                break;
            case 'POST':
                curl_setopt($this->curl, CURLOPT_POST, true);
                curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);
                break;
            case 'PUT':
                curl_setopt($this->curl, CURLOPT_PUT, true);
                curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);
                break;
            case 'PATCH':
                curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
                curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);
                break;
            case 'DELETE':
                curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
                curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);
                break;
        }

        $response = curl_exec($this->curl);
        curl_close($this->curl);
        return $response;
    }

    public function reset()
    {
        $this->mobile = false;
        $this->json_data = false;
        $this->userAgent = '';
        $this->resetOpts();
        $this->resetHeaders();
    }

    private function detectSSL($url)
    {
        if (strpos($url, 'https://') !== false) {
            curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, 2);
        }
    }

    private function setOptions()
    {
        if (count($this->options) > 0) {
            foreach ($this->options as $key => $value) {
                curl_setopt($this->curl, $key, $value);
            }
        }
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
    }

    private function setHeaders()
    {
        if (count($this->headers) > 0) {
            curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->headers);
        }
    }

    private function userAgent()
    {
        if (! $this->userAgent) {
            if ($this->mobile) {
                $this->userAgent = $this->UA['mobile'][rand(0, count($this->UA['mobile']) - 1)];
            } else {
                $this->userAgent = $this->UA['browser'][rand(0, count($this->UA['browser']) - 1)];
            }
        }
        curl_setopt($this->curl, CURLOPT_USERAGENT, $this->userAgent);
    }

    private function resetOpts()
    {
        $this->options = [];
    }

    private function resetHeaders()
    {
        $this->headers = [];
    }
}

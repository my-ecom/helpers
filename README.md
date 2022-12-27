# CUrl
CUrl class for http request

### Get Request
```
use oangia\CUrl;

$curl = new CUrl();
$response = $curl->connect('GET', 'http://example.com');

echo $response;
```

### Post Request
```
use oangia\CUrl;

$curl = new CUrl();
$curl->json_data();
$curl->json();
$curl->setHeader('Authorization: aa123acfbd5efc');
$data = [
    'name' => 'Nhat'
];
$response = $curl->connect('POST', 'http://nhathuynh.com/api/v1/test', $data);
echo $response;
```

### NCrypt
```
use oangia\NCrypt;

$encryptTxt = NCrypt::encrypt('hello world', 'secret_key_a1c32efbc');
echo NCrypt::decrypt($encryptTxt, 'secret_key_a1c32efbc');
```

### Current version
- v1.0.15
### Push tags
```
git tag -a v1.0.0 -m "v1.0.0"
git push --tags
```

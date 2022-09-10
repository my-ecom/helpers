# CUrl
CUrl class for http request

### Get Request
```
$curl = new CUrl();
$response = $curl->connect('GET', 'http://example.com');

echo $response;
```

### Post Request
```
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

# NCrypt
```
$encryptTxt = NCrypt::encrypt('hello world', 'secret_key_a1c32efbc');
echo NCrypt::decrypt($encryptTxt, 'secret_key_a1c32efbc');
```
# Response
```
Response::json(['data' => '', 'message' => 'Success'], 200);
```

# Request
```
$data = Request::json($required = ['id']);
$id = Request::get('id');
```

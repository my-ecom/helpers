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

# NCrypt
```
use oangia\NCrypt;

$encryptTxt = NCrypt::encrypt('hello world', 'secret_key_a1c32efbc');
echo NCrypt::decrypt($encryptTxt, 'secret_key_a1c32efbc');
```
# Response
```
use oangia\Response;

Response::json(['data' => '', 'message' => 'Success'], 200);
```

# Request
```
use oangia\Request;

$data = Request::json($required = ['id']);
$id = Request::get('id');
```
# Firebase
```
use oangia\firebase\Firebase;
use oangia\firebase\FireStore;

$fb = new Firebase(['apiKey' => 'AIzaSyDBLyiGjroIhQndhe0T3iac39GalX-z9Lo', 'projectId' => 'myecom-f0a26']);
$firestore = new FireStore($fb);
$response = $firestore->getCollection('users', '123'));
```
### Push tags
Current version v1.0.8b

```
git tag -a v1.0.8b -m "v1.0.8b"
git push --tags
```

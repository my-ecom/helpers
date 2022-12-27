<html>
<head>
  <title>Admin</title>
  <link rel="modulepreload" as="script" href="https://www.gstatic.com/firebasejs/9.15.0/firebase-app.js">
  <link rel="modulepreload" as="script" href="https://www.gstatic.com/firebasejs/9.15.0/firebase-firestore.js">
  <style>
    textarea {
      width: 100%;
    }
  </style>
</head>
<body>
  <textarea id="data" rows="30" cols="50"></textarea>
  <input type="button" id="submit" value="Submit"/>
<script type="module">
  let host = '<?=$_SERVER['HTTP_HOST']?>';
  import { initializeApp } from "https://www.gstatic.com/firebasejs/9.15.0/firebase-app.js";
  import { getFirestore, collection, doc, getDoc, setDoc, query, where, getDocs, orderBy, limit } from "https://www.gstatic.com/firebasejs/9.15.0/firebase-firestore.js";

  const firebaseConfig = {
    apiKey: "AIzaSyDBLyiGjroIhQndhe0T3iac39GalX-z9Lo",
    authDomain: "myecom-f0a26.firebaseapp.com",
    projectId: "myecom-f0a26",
    storageBucket: "myecom-f0a26.appspot.com",
    messagingSenderId: "712937837803",
    appId: "1:712937837803:web:8deb7d47b788d0c3b6daae",
    measurementId: "G-PW078L30CG"
  };

  const app = initializeApp(firebaseConfig);
  const db = getFirestore(app);

  submit.addEventListener('click', async (e) => {
    let product = {title: "Nhat"};
    console.log(product);
    try {
        await setDoc(doc(db, host + "_products", "fashion-2022123"), product);
    } catch (e) {
        console.error(e); // handle your error here
    } finally {
        console.log('Cleanup here'); // cleanup, always executed
    }
  });
</script>
</body>
</html>

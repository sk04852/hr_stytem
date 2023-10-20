<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Drive Upload</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</head>

<body>
    <div class="container">
        <br />
        <h3>Uploaded File</h3>
        <div>
            <a href="https://drive.google.com/uc?export=view&id={{$id}}">
                <img src="https://drive.google.com/uc?export=view&id={{$id}}"
                    style="width: 500px; max-width: 100%; height: auto" title="Click for the larger version." />
            </a>
        </div>
        <!-- <div>
            <a href="{{$link ?? ''}}">Click Here to Download</a>
        </div> -->
    </div>
</body>

</html>

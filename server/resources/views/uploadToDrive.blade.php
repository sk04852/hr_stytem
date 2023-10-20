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
        <h3>Upload Files</h3>

        <form method="post" action="{{ url('api/open/drive/upload') }}" enctype="multipart/form-data">
            <div class="custom-file">
                <label for = 'file' >Upload the File</label>
                <input type="file" name="file" />
            </div>
            <div class="row">
                <label for="description">Description</label>
                <input type="text" name="description" id="description">
            </div>
            <button class="button-primary">Upload</button>
        </form>
    </div>
</body>

</html>

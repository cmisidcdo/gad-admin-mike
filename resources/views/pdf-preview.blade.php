<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        main {
            width: 100%;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        embed {
            width: 100%;
            height: 100%;
        }
    </style>
</head>

<body>
    <main>
        @if($file_contents)
        <embed src="data:application/pdf;base64,{{ $file_contents }}" title="{{ $title }}" type="application/pdf">
        @else
        <p>No PDF data found.</p>
        @endif
    </main>
</body>

</html>
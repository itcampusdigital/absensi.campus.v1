<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Document</title>
</head>
<body class="bg-dark">
    <div class="flex justify-center max-w-5xl min-h-screen pb-16 mx-auto">
        <div class="leading-none text-center text-white md:text-left" >
                <h1 class="mb-2 text-5xl font-extrabold"></h1>
                      <p class="text-xl text-gray-900">
                            {{ $exception->getStatusCode() }}
                            <br>
                            {{ $exception->getMessage() }}  
                            <br>
                            <a href="{{ route('admin.user.index') }}" class="mt-3 btn btn-sm btn-secondary"> Kembali</a>
                      </p>
              </div>
      </div>

<script src="{{ asset('templates/vali-admin/js/bootstrap.min.js') }}"></script>

</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Email verification from INTAXA</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container py-5">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card w-100 p-4" style="border-radius: 16px">
                    <h3>Intaxa Email Verification</h3>
                    <p>Hai {{ $text['username'] }}, Kami mengirimkan kode verifikasi kepada {{ $text['email'] }} untuk keperluan authentikasi user ke aplikasi / website Intaxa</p>
                    <p class="mb-0">Kode Verifikasi :</p>
                    <h1>{{ $text['code'] }}</h1>
                    <p>Kode Verifikasi diatas digunakan untuk proses verfikasi dengan cara memasukkannya ke dalam form verifikasi yang telah kami sediakan.</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
